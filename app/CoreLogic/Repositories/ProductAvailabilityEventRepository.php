<?php

namespace App\CoreLogic\Repositories;

use App\Models\ProductOptionAvailabilityEvent;

class ProductAvailabilityEventRepository extends BaseRepository
{
    public string $modelName = ProductOptionAvailabilityEvent::class;
}
