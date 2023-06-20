<?php

namespace App\Actions;

use App\Events\Domain\DomainCreated;
use App\Models\Domain;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Team;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Str;

class CreateTenantAction
{
    /**
     * @param array $data
     * @param string $domain
     * @param bool $createStripeCustomer
     * @return Tenant
     */
    public function __invoke(array $data, string $domain, bool $createStripeCustomer = true): Tenant
    {
        $tenant = $this->createTenant($data);
        $domainModel = $this->createDomain($tenant, $domain);
        $user = $this->createUser($tenant);
        $team = $this->createAdminTeam($tenant, $domainModel);
        $team->users()->attach($user, ['id' => Str::orderedUuid()]);
        $this->createRoles($tenant);
        $role_params['guard_name'] = Permission::GUARD;
        $role_params['name'] = Role::OWNER;
        setPermissionsTeamId($tenant->getKey());
        $role = Role::findByParam($role_params);
        if ($role) {
            $user->assignRole($domainModel->id, $role->getKey(), $tenant->getKey());
        }
        return $tenant;
    }

    /**
     * @param array $data
     * @return Tenant
     */
    protected function createTenant(array $data): Tenant
    {
        return Tenant::create($data + [
                'ready' => true,
                'trial_ends_at' => now()->addDays(config('saas.trial_days')),
            ]);
    }

    /**
     * @param Tenant $tenant
     * @param string $domain
     * @return Domain
     */
    protected function createDomain(Tenant $tenant, string $domain): Domain
    {
        $domainModel = $tenant->createDomain([
            'domain' => $domain,
            'location_name' => $tenant->company . ' (Main Location)',
        ])->makePrimary()->makeFallback();
        DomainCreated::dispatch($domainModel->fresh());
        return $domainModel;
    }

    /**
     * @param Tenant $tenant
     * @return User
     */
    protected function createUser(Tenant $tenant): User
    {
        return User::create(
            $tenant->only(['first_name', 'last_name', 'email', 'password'])
        );
    }

    /**
     * @param Tenant $tenant
     * @param $domain
     * @return Team
     */
    protected function createAdminTeam(Tenant $tenant, $domain): Team
    {
        return $tenant->teams()->create([
            'name' => 'Admins',
            'is_primary' => true,
            'domain_id' => $domain->id,
        ]);
    }

    /**
     * @param Tenant $tenant
     * @return void
     */
    protected function createRoles(Tenant $tenant)
    {
        foreach (Role::ROLES as $role_name => $role_permissions) {
            $role['guard_name'] = Permission::GUARD;
            $role['name'] = $role_name;
            $role['tenant_id'] = $tenant->getKey();
            $roleModel = Role::create($role);
            $roleModel?->syncPermissions($role_permissions);
        }
    }
}
