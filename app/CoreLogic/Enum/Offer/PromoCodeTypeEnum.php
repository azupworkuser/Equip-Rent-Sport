<?php

namespace App\CoreLogic\Enum\Offer;

enum PromoCodeTypeEnum: string
{
    case FIXED = 'fixed';
    case PERCENT = 'percent';
}
