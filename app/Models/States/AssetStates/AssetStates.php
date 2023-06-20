<?php

namespace App\Models\States\AssetStates;

use App\Models\States\State;
use Spatie\ModelStates\StateConfig;

abstract class AssetStates extends State
{
    public static function config(): StateConfig
    {
        return parent::config()
            ->default(EnabledState::class)
            ->allowTransition(DisabledState::class, EnabledState::class)
            ->allowTransition(EnabledState::class, DisabledState::class)
            ->allowTransition(DisabledState::class, ArchivedState::class)
            ->allowTransition(EnabledState::class, ArchivedState::class);
    }
}
