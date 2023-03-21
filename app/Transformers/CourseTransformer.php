<?php

namespace App\Transformers;
use App\Models\Course;
use \League\Fractal\TransformerAbstract;

class CourseTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [];

    public function __construct(...$relations)
    {
       $this->defaultIncludes = [...$this->defaultIncludes, ...$relations];
    }

    public function transform(Course $course): array
    {
        return [
            'id' => $course->id,
            'accountId' => $course->account_id,
            'parentCourseId' => $course->parent_course_id,
            'title' => $course->title,
            'description' => $course->description,
            'order' => $course->description,
        ];
    }

    public function includeSubCourses(Course $course): ?\League\Fractal\Resource\Collection
    {
        $collection = $this->collection($course->subCourses, new CourseTransformer(...$this->defaultIncludes));

        $isEmpty = !$collection->getData()->count();
        if ($isEmpty) return null;
        return $collection;
    }
}
