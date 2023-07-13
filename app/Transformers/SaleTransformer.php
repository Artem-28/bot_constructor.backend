<?php

namespace App\Transformers;
use App\Models\Sale;
use \League\Fractal\TransformerAbstract;

class SaleTransformer extends TransformerAbstract
{
    public function transform(Sale $sale): array
    {
        return [
            'id' => $sale->id,
            'tariffCode' => $sale->tariff_code,
            'type' => $sale->type,
            'unit' => $sale->unit,
            'currency' => $sale->currency,
            'title' => $sale->title,
            'period' => $sale->period,
            'value' => $sale->value,
            'startAt' => $sale->start_at,
            'endAt' => $sale->end_at,
            'once' => $sale->once,
        ];
    }
}
