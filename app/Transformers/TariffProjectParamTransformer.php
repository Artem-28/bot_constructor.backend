<?php

namespace App\Transformers;

use App\Models\Tariff\TariffProjectParam;
use League\Fractal\TransformerAbstract;

class TariffProjectParamTransformer extends TransformerAbstract
{
    public function transform(TariffProjectParam $param): array
    {
        return [
            'id' => $param->id,
            'tariffId' => $param->tariff_id,
            'type' => $param->type,
            'value' => $param->value,
            'enable' => $param->enable,
            'infinity' => $param->infinity,
        ];
    }
}
