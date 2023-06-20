<?php

namespace App\Models;

use App\Notifications\ResetPassword;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens;
    use Notifiable;
    use HasFactory;
    use HasUuids;

    /**
     * @var array
     */
    protected $fillable = [
        'first_name', 'last_name', 'email', 'password', 'profile_url'
    ];

    /**
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * @return bool
     */
    public function isOwner(): bool
    {
        return $this->email === tenant()?->email;
    }

    /**
     * @return Collection
     */
    public function tenants(): Collection
    {
        return Tenant::where('email', $this->email)->get();
    }

    /**
     * @return string
     */
    public function getNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    /**
     * @param $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPassword($token, $this));
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder[]|Collection|void
     */
    public function domainRoles()
    {
        $tenant = tenant();
        if ($tenant) {
            return TeamUser::with([
                'roles:id,name',
                'user' => function ($query) {
                    $query
                        ->select(['id'])
                        ->where(['id' => $this->getKey()]);
                },
                'team' => function ($query) use ($tenant) {
                    $query
                        ->select(['id', 'domain_id', 'tenant_id'])
                        ->where(['tenant_id' => $tenant->getKey()]);
                }
            ])
                ->select(['team_user.id', 'team_id', 'user_id'])
                ->get();
        }
    }

    /**
     * @return mixed|void
     */
    public function getTenant()
    {
        $tenant = tenant();
        if ($tenant) {
            return $tenant->getKey();
        }
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function teams()
    {
        return $this->belongsToMany(Team::class, TeamUser::class)->with('domain');
    }

    /**
     * @param $roles
     * @return void
     */
    public function assignAccess($roles = null)
    {
        if (!$roles) {
            $userInvitations = UserInvitation::where([
                'email' => $this->email, 'tenant_id' => tenant()->getKey()
            ])->get();
            foreach ($userInvitations as $userInvitation) {
                $all_roles = $userInvitation->domainRole;
                foreach ($all_roles as $role) {
                    $domain_id = $role->domain_id;
                    $role_id = $role->role_id;
                    $this->assignRole($domain_id, $role_id);
                }
            }
        } else {
            $all_deleted_teams = $this->teams()->whereNotIn('domain_id', array_column($roles, 'domain_id'))->get();
            foreach ($all_deleted_teams as $deleted_team) {
                $this->teams()->detach($deleted_team);
            }
            foreach ($roles as $role) {
                $domain_id = $role['domain_id'];
                $role_id = $role['role_id'];
                $this->assignRole($domain_id, $role_id);
            }
        }
    }

    /**
     * @param $domain_id
     * @param $role_id
     * @param $tenant_id
     * @return void
     */
    public function assignRole($domain_id, $role_id, $tenant_id = null)
    {
        if (!$tenant_id) {
            $tenant_id = tenant()->getKey();
        }

        $team = Team::where(['domain_id' => $domain_id, 'tenant_id' => $tenant_id])->first();
        if ($team) {
            $team_user = TeamUser::where(['team_id' => $team->id, 'user_id' => $this->getKey()])->first();
            if (!$team_user) {
                $team->users()->attach($this, ['id' => Str::orderedUuid()]);
                $team_user = TeamUser::where(['team_id' => $team->id, 'user_id' => $this->getKey()])->first();
            }
            if ($team_user) {
                $role_params['guard_name'] = Permission::GUARD;
                $role_params['id'] = $role_id;
                $role_object = Role::findByParam($role_params);
                $team_user->syncRoles($role_object);
            }
        }
    }
}
