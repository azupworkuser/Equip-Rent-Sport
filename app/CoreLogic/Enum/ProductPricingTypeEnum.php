<?php

namespace App\CoreLogic\Enum;

use App\CoreLogic\Services\PricingStructures\ByPerson;
use App\CoreLogic\Services\PricingStructures\Fixed;
use App\CoreLogic\Services\PricingStructures\PerDay;
use App\CoreLogic\Services\PricingStructures\PerHour;
use App\CoreLogic\Services\PricingStructures\PerItem;
use App\CoreLogic\Services\PricingStructures\PerMinute;

enum ProductPricingTypeEnum: string
{
    case FIXED = Fixed::class;
    case ByPerson = ByPerson::class;
    case PerItem = PerItem::class;

    case PerDay = PerDay::class;

    case PerHour = PerHour::class;

    case PerMinute = PerMinute::class;
}
