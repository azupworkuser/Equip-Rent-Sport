<?php

namespace App\CoreLogic\Services\Availabilities;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Spatie\ModelStates\State;
use Spatie\ModelStates\StateConfig;

abstract class AvailabilityType extends State
{
    abstract public function getTimeslots(Carbon $date): array;

    /**
     * @return string
     */
    public function name(): string
    {
        return Str::camel(
            basename(
                str_replace('\\', '/', static::class)
            )
        );
    }
}
