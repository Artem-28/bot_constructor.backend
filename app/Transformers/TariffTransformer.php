<?php

namespace App\Transformers;
use App\Enums\EnumDiscount;
use App\Models\Sale;
use App\Models\Tariff;
use League\Fractal\TransformerAbstract;

class TariffTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [];

    public function __construct(...$relations)
    {
        $this->defaultIncludes = [...$this->defaultIncludes, ...$relations];
    }

    public function transform(Tariff $tariff): array
    {
        return [
            'id' => $tariff->id,
            'code' => $tariff->code,
            'basePrice' => $tariff->base_price,
            'trailPeriod' => null,
            'sales' => null,
        ];
    }

    public function includeTrailPeriod(Tariff $tariff)
    {
        $sales = $tariff->sales;
        if ($sales->isEmpty()) return null;
        $trailPeriod = $sales->first(function (Sale $sale) {
            return $sale->type === EnumDiscount::TYPE_SUBSCRIPTION_TRAIL;
        });
        if (!$trailPeriod) return null;
        return $this->item($trailPeriod, new SaleTransformer());
    }

    public function includeSales(Tariff $tariff)
    {
        $sales = $tariff->sales;
        if ($sales->isEmpty()) return null;
        $filtered = $sales->filter(function (Sale $sale) {
            return $sale->type === EnumDiscount::TYPE_SALE;
        });
        return $this->collection($filtered, new SaleTransformer());
    }

    public function includeParams(Tariff $tariff)
    {
        $params = $tariff->params;
        return $this->collection($params, new TariffParamTransformer());
    }
}
