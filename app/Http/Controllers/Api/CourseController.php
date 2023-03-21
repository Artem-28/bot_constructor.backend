<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CourseService;
use App\Transformers\CourseTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use League\Fractal\Resource\Item;

class CourseController extends Controller
{
    public CourseService $courseService;

    public function __construct
    (
        CourseService $courseService
    )
    {
        $this->middleware(['auth:sanctum']);
        $this->courseService = $courseService;
    }

    public function create(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $courseData = $request->only(['title', 'description', 'order', 'parentCourseId', 'subCourses']);
            $accountId = $request->get('accountId');
            $course = $this->courseService->create($accountId, $courseData);

            $resource = new Item($course, new CourseTransformer('subCourses'));
            $data = $this->createData($resource);
            return $this->successResponse($data);

        } catch (\Exception $exception) {

            $message = $exception->getMessage();
            return $this->errorResponse($message);
        }
    }


    public function show(Request $request, int $id): \Illuminate\Http\JsonResponse
    {
        try {
            $course = $this->courseService->getCourseById($id);

            $resource = new Item($course, new CourseTransformer('subCourses'));
            $data = $this->createData($resource);
            return $this->successResponse($data);

        } catch (\Exception $exception) {

            $message = $exception->getMessage();
            return $this->errorResponse($message);
        }
    }
}
