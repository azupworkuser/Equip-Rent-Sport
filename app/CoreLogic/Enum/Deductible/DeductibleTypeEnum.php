<?php

namespace App\CoreLogic\Enum\Deductible;

enum DeductibleTypeEnum: int
{
    case PERCENTAGE = 1;
    case FIXED_PER_ORDER_ITEM = 2;
    case FIXED_PER_QUANTITY = 3;
    case FIXED_PER_DURATION = 4;

    /**
     * @return array[]
     */
    public static function values(): array
    {
        return [
            [
                'name' => 'Percentage',
                'value' => self::PERCENTAGE,
            ],
            [
                'name' => 'Fixed per order item',
                'value' => self::FIXED_PER_ORDER_ITEM,
            ],
            [
                'name' => 'Fixed per quantity',
                'value' => self::FIXED_PER_QUANTITY,
            ],
            [
                'name' => 'Fixed per duration',
                'value' => self::FIXED_PER_DURATION,
            ],
        ];
    }
}
