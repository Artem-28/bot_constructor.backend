<?php

namespace App\Services;

use App\Models\Category;

class CategoryService
{
    public function getCourseCategories(string | null $code = null): \Illuminate\Database\Eloquent\Collection
    {
        $baseType = 'course_category';
        $type = $code ? $baseType . '_' . $code : $baseType;
        return Category::query()->where('type', $type)->get();
    }
}
