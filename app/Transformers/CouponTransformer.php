<?php

namespace App\Transformers;

use App\Models\Coupon;
use \League\Fractal\TransformerAbstract;

class CouponTransformer extends TransformerAbstract
{
    public function transform(Coupon $coupon): array
    {
        return [
            'id' => $coupon->id,
            'userId' => $coupon->user_id,
            'code' => $coupon->code,
            'unit' => $coupon->unit,
            'currency' => $coupon->currency,
            'title' => $coupon->title,
            'period' => $coupon->period,
            'value' => $coupon->value,
            'startAt' => $coupon->start_at,
            'endAt' => $coupon->end_at,
            'once' => $coupon->once,
        ];
    }
}
