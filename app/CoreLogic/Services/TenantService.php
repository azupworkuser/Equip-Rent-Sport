<?php

namespace App\CoreLogic\Services;

use App\Models\Domain;
use App\Models\Tenant;
use App\CoreLogic\Repositories\TenantRepository;
use App\Models\User;
use Illuminate\Support\Collection;

class TenantService extends Service
{
    protected string $repositoryName = TenantRepository::class;

    /**
     * @param $id
     * @return mixed
     */
    public function getTenant($id)
    {
        $tenant = $this->repository->find($id);
        return $tenant;
    }
}
