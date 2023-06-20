<?php

namespace App\CoreLogic\Enum\Deductible;

enum DeductibleCategoryEnum: int
{
    case TAX = 1;
    case FEE = 2;

    /**
     * @return array[]
     */
    public static function values(): array
    {
        return [
            [
                'name' => 'Tax',
                'value' => self::TAX,
            ],
            [
                'name' => 'Fee',
                'value' => self::FEE,
            ]
        ];
    }
}
