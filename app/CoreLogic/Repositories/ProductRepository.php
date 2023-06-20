<?php

namespace App\CoreLogic\Repositories;

use App\Models\Product;

class ProductRepository extends BaseRepository
{
    public string $modelName = Product::class;
}
