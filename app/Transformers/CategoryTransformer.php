<?php

namespace App\Transformers;
use App\Models\Category;
use League\Fractal\TransformerAbstract;

class CategoryTransformer extends TransformerAbstract
{
    public function transform(Category $category): array
    {
        return [
            'id' => $category->id,
            'code' => $category->code,
            'title' => $category->title,
            'order' => $category->order,
        ];
    }
}
