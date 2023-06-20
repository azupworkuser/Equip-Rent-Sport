<?php

namespace App\CoreLogic\Services;

use App\Models\Permission;
use App\Models\Role;
use App\CoreLogic\Repositories\RoleRepository;

class RoleService extends Service
{
    protected string $repositoryName = RoleRepository::class;

    /**
     * @param  array  $role
     * @return bool|Role
     */
    public function create(array $role): bool|Role
    {
        $permissions = $role['permissions'];
        $role['guard_name'] = Permission::GUARD;
        unset($role['permissions']);
        $roleModel = $this->repository->create($role);
        $roleModel->syncPermissions($permissions);
        return $roleModel;
    }

    /**
     * @param  array  $payload
     * @param $role
     * @return Role
     */
    public function update(array $payload, $role): Role
    {
        $permissions = $payload['permissions'];
        $role['guard_name'] = Permission::GUARD;
        unset($payload['permissions']);
        $role->update($payload);
        $role->syncPermissions($permissions);
        return $role;
    }

    /**
     * @param  Role  $role
     * @return void
     */
    public function archive(Role $role): void
    {
        $role->delete();
    }

    /**
     * @param Role $role
     * @return Role
     */
    public function get(Role $role): Role
    {
        return $role;
    }
}
