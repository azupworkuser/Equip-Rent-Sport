<?php

namespace App\CoreLogic\Repositories;

use App\Models\Tenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class TenantRepository extends BaseRepository
{
    public string $modelName = Tenant::class;
}
