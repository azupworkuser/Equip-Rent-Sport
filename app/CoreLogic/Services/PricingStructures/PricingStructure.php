<?php

namespace App\CoreLogic\Services\PricingStructures;

use App\Models\Product;

class PricingStructure
{
    public Product $product;

    public static array $structures = [
        'fixed' => Fixed::class,
        'per_minute' => PerMinute::class,
        'per_day' => PerDay::class,
        'per_hour' => PerItem::class,
        'per_item' => PerItem::class,
        'by_person' => ByPerson::class,
    ];

    /**
     * @param string $type
     * @return static
     */
    public static function make(string $type): self
    {
        return self::$structures[$type];
    }
}
