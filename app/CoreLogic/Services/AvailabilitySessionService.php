<?php

namespace App\CoreLogic\Services;

use App\Models\ProductOption;
use App\CoreLogic\Repositories\AvailabilitySessionRepository;
use App\CoreLogic\Repositories\SessionRepository;
use Illuminate\Support\Collection;

class AvailabilitySessionService extends Service
{
    protected string $repositoryName = AvailabilitySessionRepository::class;

    /**
     * @param ProductOption $productOption
     * @param string $date
     * @return Collection
     */
    public function getAvailableSessions(ProductOption $productOption, string $date): Collection
    {
        return $this
            ->repository
            ->getByProductOptionAndDate($productOption, $date);
    }
}
