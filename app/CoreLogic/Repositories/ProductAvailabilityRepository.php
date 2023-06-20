<?php

namespace App\CoreLogic\Repositories;

use App\Models\Product;
use App\Models\ProductAvailability;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class ProductAvailabilityRepository extends BaseRepository
{
    public string $modelName = ProductAvailability::class;

    public function getByDate(Product $product, Carbon $date): Collection
    {
        return $this
            ->model
            ->forProduct($product->getKey())
            ->forDate($date)
            ->get();
    }
}
