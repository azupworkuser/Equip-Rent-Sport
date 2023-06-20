<?php

namespace App\CoreLogic\States\Product;

use Spatie\ModelStates\State;
use Spatie\ModelStates\StateConfig;

abstract class ProductState extends State
{
    /**
     * @return string
     */
    public function name(): string
    {
        return class_basename($this);
    }

    /**
     * @return StateConfig
     * @throws \Spatie\ModelStates\Exceptions\InvalidConfig
     */
    public static function config(): StateConfig
    {
        return parent::config()
            ->default(Active::class)
            ->allowTransition(Active::class, Disabled::class);
    }
}
