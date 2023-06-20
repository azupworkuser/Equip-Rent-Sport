<?php

namespace App\CoreLogic\Services\PricingStructures;

use App\Models\Product;

class Fixed extends PricingStructure
{
    /**
     * @param Product $product
     * @param array $units
     * @return mixed
     */
    public function calculate(Product $product, array $units)
    {
        return $product->advertising_price;
    }
}
