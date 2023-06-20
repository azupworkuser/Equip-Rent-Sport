<?php

namespace App\CoreLogic\Enum\Offer;

enum OfferTypeEnum: string
{
    case PROMO_CODE = 'promo_code';
    case VOUCHER = 'voucher';
    case GIFT_CARD = 'gift_card';
}
