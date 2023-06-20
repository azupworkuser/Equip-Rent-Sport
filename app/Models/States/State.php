<?php

namespace App\Models\States;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Spatie\ModelStates\State as BaseState;

abstract class State extends BaseState
{
    public function name(): string
    {
        return str(Arr::last(explode('\\', static::class)))->camel()->replace('State', '');
    }
}
