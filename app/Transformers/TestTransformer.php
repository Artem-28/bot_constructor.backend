<?php

namespace App\Transformers;

use App\Models\Test;
use League\Fractal\TransformerAbstract;

class TestTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [];

    public function __construct(...$relations)
    {
        $this->defaultIncludes = [...$this->defaultIncludes, ...$relations];
    }

    public function transform(Test $test): array
    {
        return [
            'id' => $test->id,
            'accountId' => $test->account_id,
            'title' => $test->title,
            'description' => $test->description,
        ];
    }
}
