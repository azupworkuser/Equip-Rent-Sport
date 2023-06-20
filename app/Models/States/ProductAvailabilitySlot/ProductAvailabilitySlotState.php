<?php

namespace App\Models\States\ProductAvailabilitySlot;

use App\Models\States\State;
use Illuminate\Support\Str;
use Spatie\ModelStates\StateConfig;

abstract class ProductAvailabilitySlotState extends State
{
    public static function config(): StateConfig
    {
        return parent::config()
            ->default(Available::class)
            ->allowTransitions([
                [Available::class, Hold::class],
                [Available::class, Booked::class],
                [Available::class, Cancelled::class],
                [Hold::class, Available::class],
                [Hold::class, Booked::class],
                [Hold::class, Cancelled::class],
            ]);
    }
}
