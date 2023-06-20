<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\MorphPivot;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Offerable extends MorphPivot
{
    use HasUuids;

    protected $casts = [
        'data' => 'array'
    ];
}
