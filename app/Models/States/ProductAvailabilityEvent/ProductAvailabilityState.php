<?php

namespace App\Models\States\ProductAvailabilityEvent;

use App\Models\States\State;
use Illuminate\Support\Str;
use Spatie\ModelStates\StateConfig;

abstract class ProductAvailabilityState extends State
{
    public static function config(): StateConfig
    {
        return parent::config()
            ->default(Active::class)
            ->allowTransitions([
                [Active::class, Disabled::class],
                [Active::class, Completed::class],
                [Disabled::class, Active::class],
            ]);
    }
}
