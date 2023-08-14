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
}
