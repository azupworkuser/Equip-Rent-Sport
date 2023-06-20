<?php

namespace App\Models\States\UserInvitation;

use App\Models\States\State;
use Spatie\ModelStates\Exceptions\InvalidConfig;
use Spatie\ModelStates\StateConfig;

abstract class UserInvitationStates extends State
{
    /**
     * @throws InvalidConfig
     */
    public static function config(): StateConfig
    {
        return parent::config()
            ->default(Draft::class)
            ->allowTransition(Draft::class, Sent::class)
            ->allowTransition(Sent::class, Accept::class)
            ->allowTransition(Sent::class, Reject::class)
            ->allowTransition(Sent::class, Expired::class);
    }
}
