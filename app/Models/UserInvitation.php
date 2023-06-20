<?php

namespace App\Models;

use App\Models\States\UserInvitation\UserInvitationStates;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\ModelStates\HasStates;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class UserInvitation extends Model
{
    use HasFactory;
    use SoftDeletes;
    use HasUuids;
    use BelongsToTenant;
    use HasStates;

    protected $casts = [
        'status' => UserInvitationStates::class,
        'data' => 'array',
    ];

    protected $fillable = [
        'user_id',
        'tenant_id',
        'created_by',
        'accepted_at',
        'email',
        'status',
        'token',
        'domains',
        'data'
    ];

    /**
     * @return string[]
     */
    public static function getCustomColumns(): array
    {
        return [
            'first_name',
            'last_name',
            'phone_number'
        ];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function domains()
    {
        return $this->belongsToMany(Domain::class, UserInvitationDomain::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function domainRole()
    {
        return $this->hasMany(UserInvitationDomain::class);
    }
}
