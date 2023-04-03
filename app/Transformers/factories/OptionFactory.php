<?php

namespace App\Transformers\factories;

use App\Services\OptionsService;
use App\Traits\DataPreparation;
use Illuminate\Support\Collection;
use App\Transformers\AccountTypeTransformer;
use App\Transformers\CategoryTransformer;

class OptionFactory
{
    use DataPreparation;

    public function transformOptions(string $type, Collection $collection): array|null
    {
        switch ($type) {
            case OptionsService::ACCOUNT_TYPE_OPTIONS:
                $resource = new \League\Fractal\Resource\Collection($collection, new AccountTypeTransformer());
                return $this->createData($resource);
            case OptionsService::COURSE_CATEGORY_OPTIONS:
                $resource = new \League\Fractal\Resource\Collection($collection, new CategoryTransformer());
                return $this->createData($resource);
            default:
                return null;
        }
    }
}
