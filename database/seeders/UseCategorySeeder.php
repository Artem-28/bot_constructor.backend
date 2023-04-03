<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use phpDocumentor\Reflection\Types\This;

class UseCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $courseCategories = array(
            [
                'title' => 'Программирование',
                'code' => 'programming',
                'type' => 'course_category',
                'subcategories' => [
                    [ 'title' => 'Бэкенд-разработка', 'code' => 'backend' ],
                    [ 'title' => 'Веб-разработка', 'code' => 'web' ],
                    [ 'title' => 'Мобильная разработка', 'code' => 'mobile' ],
                    [ 'title' => 'Анализ данных', 'code' => 'data_analysis' ]
                ]
            ],
            [
                'title' => 'Маркетинг',
                'code' => 'marketing',
                'type' => 'course_category',
                'subcategories' => [
                    [ 'title' => 'Бренд-маркетинг', 'code' => 'brand' ],
                    [ 'title' => 'Аналитика', 'code' => 'analytics' ],
                    [ 'title' => 'Перформанс-маркетинг', 'code' => 'performance' ],
                    [ 'title' => 'Электронная коммерция', 'code' => 'e-commerce' ]
                ]
            ],
            [
                'title' => 'Управление',
                'code' => 'control',
                'type' => 'course_category',
                'subcategories' => [
                    [ 'title' => 'Образование', 'code' => 'education' ],
                    [ 'title' => 'Финансы и бухгалтерия', 'code' => 'finance' ],
                    [ 'title' => 'Проекты и продукты', 'code' => 'project' ],
                    [ 'title' => 'Менеджмент и аналитика', 'code' => 'management' ]
                ]
            ]
        );

        $allCategories = array(...$courseCategories);
        $data = $this->createCategoryData($allCategories);

        foreach ($data as $item) {
            Category::updateOrCreate([
                'code' => $item['code'],
            ], $item);
        }
    }

    private function createCategoryData(array $categories, array $parent = null): array
    {
        $step = 1;
        $data = [];
        foreach ($categories as $key => $value) {
            $type = $parent ? $parent['type'] . '_' . $parent['code'] : $value['type'];
            $code = $parent ? $parent['code'] . '_' . $value['code'] : $value['code'];
            $order = $step * ($key + 1);
            if (!empty($value['subcategories'])) {
                $subcategories = $this->createCategoryData($value['subcategories'], $value);
                array_push($data, ...$subcategories);
            }

            $category = array([
                'title' => $value['title'],
                'code' => $code,
                'type' => $type,
                'order' => $order
            ]);

            array_push($data, ...$category);
        }

        return $data;
    }
}
