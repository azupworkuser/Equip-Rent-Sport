<?php

namespace App\Models\Traits;

use App\Models\Offer;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait HasOffers
{
    public function offers(): MorphToMany
    {
        return $this->morphToMany(Offer::class, 'offerable');
    }
}
