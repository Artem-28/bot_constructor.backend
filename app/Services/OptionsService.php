<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Support\Collection;

class OptionsService
{
    const ACCOUNT_TYPE_OPTIONS = 'account_type';
    const COURSE_CATEGORY_OPTIONS = 'course_category';

    private AccountTypeService $accountTypeService;
    private CategoryService $categoryService;

    public function __construct
    (
        AccountTypeService $accountTypeService,
        CategoryService $categoryService,
    )
    {
        $this->accountTypeService = $accountTypeService;
        $this->categoryService = $categoryService;
    }

    public function getOptions(string $type, ...$atr): ?Collection
    {
        return match ($type) {
            self::ACCOUNT_TYPE_OPTIONS => $this->accountTypeService->getList(),
            self::COURSE_CATEGORY_OPTIONS => $this->categoryService->getCourseCategories(...$atr),
            default => null,
        };
    }
}
