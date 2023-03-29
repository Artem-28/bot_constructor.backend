<?php

namespace App\Transformers;
use App\Models\Course;
use \League\Fractal\TransformerAbstract;

class CourseTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [];
    protected array $defaultAttributes = [];

    public function __construct(...$relations)
    {
        $this->separateRelations($relations);
    }

    protected function separateRelations(array $relations)
    {
        $attributes = ['categoryCodes'];

        foreach ($relations as $relation) {
            $isAttribute = in_array($relation,  $attributes);
            if ($isAttribute) {
                array_push($this->defaultAttributes, $relation);
                continue;
            }
            array_push($this->defaultIncludes, $relation);
        }
    }

    public function transform(Course $course): array
    {

        $response = [
            'id' => $course->id,
            'accountId' => $course->account_id,
            'parentCourseId' => $course->parent_course_id,
            'title' => $course->title,
            'description' => $course->description,
            'order' => $course->description,
        ];

        if (in_array('categoryCodes',  $this->defaultAttributes)) {
            $response['categoryCodes'] = $course->category_codes;
        }

        return $response;
    }

    public function includeSubCourses(Course $course): ?\League\Fractal\Resource\Collection
    {
        $collection = $this->collection($course->subCourses, new CourseTransformer(...$this->defaultIncludes));

        $isEmpty = !$collection->getData()->count();
        if ($isEmpty) return null;
        return $collection;
    }

    public function includeCategories(Course $course): ?\League\Fractal\Resource\Collection
    {
        $collection = $this->collection($course->categories, new CategoryTransformer());

        $isEmpty = !$collection->getData()->count();
        if ($isEmpty) return null;
        return $collection;
    }
}
