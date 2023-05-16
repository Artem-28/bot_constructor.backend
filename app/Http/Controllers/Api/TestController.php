<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\TestService;
use App\Transformers\TestTransformer;
use Illuminate\Http\Request;
use League\Fractal\Resource\Item;

class TestController extends Controller
{
    public TestService $testService;

    public function __construct
    (
        TestService $testService,
    )
    {
        $this->middleware(['auth:sanctum']);
        $this->testService = $testService;
    }

    // Получит тест по id
    public function show(Request $request, int $testId): \Illuminate\Http\JsonResponse
    {
        try {
            $test = $this->testService->getTestById($testId);

            if (!$test) {
                $message = 'Тест с id: ' . $testId . ' не найден';
                return $this->errorResponse( $message, 404);
            }
            $resource = new Item($test, new TestTransformer());
            $data = $this->createData($resource);
            return $this->successResponse($data);

        } catch (\Exception $exception) {
            $message = $exception->getMessage();
            return $this->errorResponse($message);
        }
    }

    // Создание теста
    public function create(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $testData = $request->only('title', 'description');
            $accountId = $request->get('accountId');
            $test = $this->testService->create($accountId, $testData);

            $resource = new Item($test, new TestTransformer());
            $data = $this->createData($resource);
            return $this->successResponse($data);

        } catch (\Exception $exception) {
            $message = $exception->getMessage();
            return $this->errorResponse($message);
        }
    }

    // Обновление теста
    public function update(Request $request, int $testId): \Illuminate\Http\JsonResponse
    {
        try {
            $testData = $request->only('title', 'description', 'accountId');
            $success = $this->testService->update($testId, $testData);
            if (!$success) {
                return $this->errorResponse( 'Не найдено ни одной записи для обновления', 404);
            }
            return $this->successResponse(null, 'Обновлено успешно');

        } catch (\Exception $exception) {
            $message = $exception->getMessage();
            return $this->errorResponse($message);
        }
    }

    // Удаление теста
    public function delete(Request $request, int $testId): \Illuminate\Http\JsonResponse
    {
        try {
            $success = $this->testService->delete($testId);
            if (!$success) {
                return $this->errorResponse( 'Не удалось удалить тест');
            }
            return $this->successResponse(null, 'Удалено успешно');

        } catch (\Exception $exception) {
            $message = $exception->getMessage();
            return $this->errorResponse($message);
        }

    }
}
