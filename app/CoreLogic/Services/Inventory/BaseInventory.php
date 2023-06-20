<?php

namespace App\CoreLogic\Services\Inventory;

use App\CoreLogic\Repositories\ProductInventoryRepository;

abstract class BaseInventory
{
    public function __construct(
        protected ProductInventoryRepository $repository
    ) {
    }
}
