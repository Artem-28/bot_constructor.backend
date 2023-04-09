<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\GroupQuestionService;
use Illuminate\Http\Request;

class QuestionGroupController extends Controller
{
    private GroupQuestionService $groupQuestionService;

    public function __construct
    (
        GroupQuestionService $groupQuestionService,
    )
    {
        $this->middleware(['auth:sanctum']);
        $this->groupQuestionService = $groupQuestionService;
    }
    // Создает вопрос в новой группе или уже в существующей группе
    public function createQuestion(Request $request)
    {
        try {
            $testId = $request->get('testId');
            $groupData = $request->only(['prevGroupId', 'groupId']);
            $questionData = $request->only(['type', 'text', 'prevQuestionId']);

            $this->groupQuestionService->createTestQuestion($testId, $groupData, $questionData);

        } catch (\Exception $exception) {
            $message = $exception->getMessage();
            return $this->errorResponse($message);
        }
    }

    public function updateQuestion(Request $request, int $questionId)
    {
        try {
            $questionData = $request->only(['groupId', 'prevQuestionId', 'text']);
            $this->groupQuestionService->updateQuestion($questionId, $questionData);

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

    // Удаление вопроса
    public function deleteQuestion(int $questionId): \Illuminate\Http\JsonResponse
    {
        try {
            $this->groupQuestionService->deleteQuestion($questionId);
            return $this->successResponse(null, 'Вопрос успешно удален');

        } catch (\Exception $exception) {
            $message = $exception->getMessage();
            return $this->errorResponse($message);
        }
    }
}
