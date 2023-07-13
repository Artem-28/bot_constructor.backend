<?php

namespace App\Transformers;
use App\Models\TariffParam;
use \League\Fractal\TransformerAbstract;

class TariffParamTransformer extends TransformerAbstract
{
    public function transform(TariffParam $param): array
    {
        return [
            'type' => $param->type,
            'min' => $param->min,
            'max' => $param->max,
            'price' => $param->price,
            'priceInfinity' => $param->price_infinity,
            'infinity' => $param->infinity,
            'enable' => $param->enable,
        ];
    }
}
