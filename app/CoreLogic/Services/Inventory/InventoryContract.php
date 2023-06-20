<?php

namespace App\CoreLogic\Services\Inventory;

use App\Models\Product;
use App\Models\ProductInventory;

interface InventoryContract
{
    public function create(Product $product, array $data): ProductInventory;
}
