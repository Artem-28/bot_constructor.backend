<?php

namespace App\Services;

use App\Models\Course;

class CourseService
{

    private function courseModel(int $accountId, $data): Course
    {
        return new Course([
            'account_id' =>$accountId,
            'parent_course_id' => array_key_exists('parentCourseId', $data) ? $data['parentCourseId'] : null,
            'title' => $data['title'],
            'description' => $data['description'],
            'order' =>$data['order'],
        ]);
    }

    public function create(int $accountId, $data): Course
    {
        $course = $this->courseModel($accountId, $data);

        $course->save();

        if (
            array_key_exists('subCourses', $data)
            && !empty($subCoursesData = $data['subCourses'])
        ) {

            $subCourses = array_map(function ($data) use ($accountId) {
                return $this->courseModel($accountId, $data);
            }, $subCoursesData);
        }

        if (!empty($subCourses)) {

            $course->subCourses()->saveMany($subCourses);
        }
        return $course;
    }

    public function getCourseById(int $courseId)
    {
        return Course::with('subCourses')->find($courseId);
    }

    // Добавляет категории к курсу
    public function assignCategories(Course $course, array $codeCategories): array
    {
        return $course->categories()->sync($codeCategories);
    }
}
