<?php

namespace App\CoreLogic\Repositories;

use App\Models\Offer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;

class OfferRepository extends BaseRepository
{
    public string $modelName = Offer::class;
}
