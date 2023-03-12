<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\OptionsService;
use App\Transformers\factories\OptionFactory;
use Illuminate\Http\Request;

class OptionsController extends Controller
{
    public OptionsService $optionsService;
    public OptionFactory $optionFactory;

    public function __construct
    (
        OptionsService $optionsService,
        OptionFactory $optionFactory,
    )
    {
        $this->optionsService = $optionsService;
        $this->optionFactory = $optionFactory;
    }

    // Получение списка типов аккаунта
    public function accountTypeOptions(): \Illuminate\Http\JsonResponse
    {
        try {
            $type = OptionsService::ACCOUNT_TYPE_OPTIONS;
            $options = $this->optionsService->getOptions($type);
            $data = $this->optionFactory->transformOptions($type, $options);

            return $this->successResponse($data);

        } catch (\Exception $exception) {
            $message = $exception->getMessage();
            return $this->errorResponse($message);
        }
    }
}
