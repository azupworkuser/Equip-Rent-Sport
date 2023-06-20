<?php

namespace App\Models\States\Offer;

use App\Models\States\State;
use Spatie\ModelStates\StateConfig;

abstract class OfferStates extends State
{
    public static function config(): StateConfig
    {
        return parent::config()
            ->default(Active::class)
            ->allowTransition(Active::class, Inactive::class)
            ->allowTransition(Active::class, Expired::class)
            ->allowTransition(Active::class, Completed::class)
            ->allowTransition(Active::class, Scheduled::class)
            ->allowTransition(Scheduled::class, Active::class)
            ->allowTransition(Scheduled::class, Expired::class)
            ->allowTransition(Scheduled::class, Inactive::class)
            ->allowTransition(Inactive::class, Active::class)
            ->allowTransition(Inactive::class, Expired::class)
            ->allowTransition(Inactive::class, Scheduled::class)
            ->allowTransition(Expired::class, Active::class)
            ->allowTransition(Expired::class, Scheduled::class)
            ->allowTransition(Completed::class, Active::class)
            ->allowTransition(Active::class, Archived::class)
            ->allowTransition(Completed::class, Archived::class)
            ->allowTransition(Expired::class, Archived::class)
            ->allowTransition(Inactive::class, Archived::class)
            ->allowTransition(Scheduled::class, Archived::class);
    }
}
