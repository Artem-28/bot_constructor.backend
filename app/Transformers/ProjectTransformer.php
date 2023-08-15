<?php

namespace App\Transformers;

use App\Models\Project\Project;
use League\Fractal\TransformerAbstract;

class ProjectTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
    ];

    public function __construct(...$relations)
    {
        $this->defaultIncludes = [...$this->defaultIncludes, ...$relations];
    }
    public function transform(Project $project): array
    {
        return [
            'id' => $project->id,
            'title' => $project->title,
            'tariffId' => $project->tariff_id,
            'createdAt' => $project->created_at,
        ];
    }

    public function includeTariff(Project $project): ?\League\Fractal\Resource\Item
    {
        $tariff = $project->tariff;
        if (!$tariff) return null;
        return $this->item($tariff, new TariffProjectTransformer());
    }

    public function includeParams(Project $project)
    {
        $params = $project->params;
        return $this->collection($params, new TariffProjectParamTransformer());
    }
}
