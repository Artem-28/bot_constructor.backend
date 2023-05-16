<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GroupQuestion;
use App\Services\GroupQuestionService;
use App\Transformers\GroupQuestionTransformer;
use Illuminate\Http\Request;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;

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
    // Получение вопросов для теста
    public function indexQuestion(Request $request, int $testId): \Illuminate\Http\JsonResponse
    {
        try {
            $groups = $this->groupQuestionService->getTestQuestions($testId);
            $resource = new Collection($groups, new GroupQuestionTransformer('sortedQuestions'));
            $data = $this->createData($resource);
            return $this->successResponse($data);

        } catch (\Exception $exception) {
            $message = $exception->getMessage();
            return $this->errorResponse($message);
        }
    }
    // Создает вопрос в новой группе или уже в существующей группе
    public function createQuestion(Request $request)
    {
        try {
            $testId = $request->get('testId');
            $groupData = $request->only(['prevGroupId', 'groupId']);
            $questionData = $request->only(['type', 'text', 'prevQuestionId']);

            $groupQuestion = $this->groupQuestionService
                ->createTestQuestion($testId, $groupData, $questionData);

            $resource = new Item($groupQuestion, new GroupQuestionTransformer('questions'));
            $resourceService = $this->groupQuestionService->getResource();
            $resourceUpdated = new Collection($resourceService['updated'], new GroupQuestionTransformer());
            $createData = $this->createData($resource);
            $updateData = $this->createData($resourceUpdated);
            return $this->successResponse(['questions' => null, 'groups' => null]);

        } catch (\Exception $exception) {
            $message = $exception->getMessage();
            return $this->errorResponse($message);
        }
    }

    // Обновление вопроса
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

    // Перенос вопроса в другую группу или изменение позиции в группе
    public function updateQuestionPosition(Request $request, int $questionId)
    {
        try {
            $groupId = $request->get('groupId');
            $prevQuestionId = $request->get('prevQuestionId');
            $question = $this->groupQuestionService->updateQuestionPosition($questionId, $groupId, $prevQuestionId);
        } catch (\Exception $exception) {
            $message = $exception->getMessage();
            return $this->errorResponse($message);
        }
    }

    // Обновление позиции группы
    public function updateQuestionGroupPosition(Request $request, int $groupId)
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
