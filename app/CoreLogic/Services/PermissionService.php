<?php

namespace App\CoreLogic\Services;

use App\Models\Permission;
use App\CoreLogic\Repositories\PermissionRepository;

class PermissionService extends Service
{
    protected string $repositoryName = PermissionRepository::class;

    /**
     * @param Permission $permission
     * @return Permission
     */
    public function get(Permission $permission): Permission
    {
        return $permission;
    }
}
