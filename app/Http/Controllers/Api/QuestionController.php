<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\GroupQuestionService;
use App\Services\QuestionService;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    private QuestionService $questionService;
    private GroupQuestionService $groupQuestionService;

    public function __construct
    (
        QuestionService $questionService,
        GroupQuestionService $groupQuestionService,
    )
    {
        $this->middleware(['auth:sanctum']);
        $this->questionService = $questionService;
        $this->groupQuestionService = $groupQuestionService;
    }
    public function create(Request $request)
    {
        try {
            $testId = $request->get('testId');
            $prevGroupId = $request->get('prevGroupId');
            $group = $this->groupQuestionService->create($testId, $prevGroupId);

        } catch (\Exception $exception) {
            $message = $exception->getMessage();
            return $this->errorResponse($message);
        }
    }

    public function updatePositionQuestionGroup(Request $request, int $groupId)
    {
        try {
            $prevGroupId = $request->get('prevGroupId');
            $this->groupQuestionService->updateGroupPosition($groupId, $prevGroupId);
        } catch (\Exception $exception) {
            $message = $exception->getMessage();
            return $this->errorResponse($message);
        }

    }

    // Удаление группы вопросов
    public function deleteQuestionGroup(int $groupId): \Illuminate\Http\JsonResponse
    {
        try {
            $this->groupQuestionService->deleteQuestionGroup($groupId);
            return $this->successResponse(null, 'Группа успешно удалена');

        } catch (\Exception $exception) {
            $message = $exception->getMessage();
            return $this->errorResponse($message);
        }
    }
}
