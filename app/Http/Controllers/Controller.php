<?php

namespace App\Http\Controllers;

use App\Traits\DataPreparation;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests, DataPreparation;

    // Ответ ошибки
    protected function errorResponse(string $message = '', int $status = 500): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message
        ], $status);
    }

    // Успешный ответ
    protected function successResponse($data = null, string $message = null): \Illuminate\Http\JsonResponse
    {
        $responseData = array('success' => true);

        if ($message) {
            $responseData['message'] = $message;
        }
        if (!empty($data)) {
            $responseData['data'] = $data;
        }

        return response()->json($responseData, 200);
    }
}
