<?php

namespace App\Transformers;

use App\Models\Tariff\TariffProject;
use League\Fractal\TransformerAbstract;

class TariffProjectTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [];

    public function __construct(...$relations)
    {
        $this->defaultIncludes = [...$this->defaultIncludes, ...$relations];
    }

    public function transform(TariffProject $tariff): array
    {
        return [
            'id' => $tariff->id,
            'userId' => $tariff->user_id,
            'tariffCode' => $tariff->tariff_code,
            'period' => $tariff->period,
            'startAt' => $tariff->start_at,
            'endAt' => $tariff->end_at,
            'status' => $tariff->status,
        ];
    }
}
