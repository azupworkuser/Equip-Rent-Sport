<?php

namespace App\CoreLogic\Services\Availabilities;

use Carbon\Carbon;

class AnytimeAvailability extends AvailabilityType
{
    /**
     * @param Carbon $date
     * @return array
     */
    public function getTimeslots(Carbon $date): array
    {
        return [];
    }
}
