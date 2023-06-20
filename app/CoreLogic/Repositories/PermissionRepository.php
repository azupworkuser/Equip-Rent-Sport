<?php

namespace App\CoreLogic\Repositories;

use App\Models\Permission;

class PermissionRepository extends BaseRepository
{
    public string $modelName = Permission::class;
}
