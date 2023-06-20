<?php

namespace App\CoreLogic\Services\Availabilities;

use App\Models\ProductAvailabilityTimeSlot;
use Carbon\Carbon;

class FixedAvailability extends AvailabilityType
{
    /**
     * @param Carbon $date
     * @return array
     */
    public function getTimeslots(Carbon $date): array
    {
        return ProductAvailabilityTimeSlot::
                where('availability_id', $this->getModel()->getKey())
                ->get()
                ->toArray();
    }
}
