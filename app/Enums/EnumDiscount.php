<?php

namespace App\Enums;

class EnumDiscount
{
    const TYPE_SUBSCRIPTION_TRAIL = 'subscription_trail'; // Подписка на пробный период
    const TYPE_SALE = 'sale'; // простая скидка на тариф
    const TYPE_COUPON = 'coupon'; // купон на скидку выдается с кодом coupon_code

    const UNIT_VALUE_PRICE = 'price';
    const UNIT_VALUE_PERCENT = 'percent';
}
