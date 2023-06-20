<?php

namespace App\Actions;

use App\Models\Tenant;

class CreateTenantS3Bucket
{
    /**
     * @param Tenant $tenant
     * @return Tenant
     */
    public function __invoke(Tenant $tenant): Tenant
    {
        $this->createBucket($tenant);

        return $tenant;
    }
}
