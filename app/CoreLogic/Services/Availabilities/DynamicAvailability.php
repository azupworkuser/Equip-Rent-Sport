<?php

namespace App\CoreLogic\Services\Availabilities;

use App\Models\ProductAvailability;
use Carbon\Carbon;

class DynamicAvailability extends AvailabilityType
{
    protected ProductAvailability $productAvailability;

    /**
     * @param ProductAvailability $productAvailability
     * @return $this
     */
    public function setProductAvailability(ProductAvailability $productAvailability): self
    {
        $this->productAvailability = $productAvailability;
        return $this;
    }

    /**
     * @param Carbon $date
     * @return array
     */
    public function getTimeslots(Carbon $date): array
    {
        return [];
    }
}
