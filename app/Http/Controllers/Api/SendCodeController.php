<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ConfirmationCode;
use App\Services\ConfirmationCodeService;
use App\Services\SendEmailService;
use Illuminate\Http\Request;

class SendCodeController extends Controller
{
    public ConfirmationCodeService $confirmationCodeService;
    public SendEmailService $sendEmailService;

    public function __construct
    (
        ConfirmationCodeService $confirmationCodeService,
        SendEmailService $sendEmailService
    )
    {
        $this->confirmationCodeService = $confirmationCodeService;
        $this->sendEmailService = $sendEmailService;
    }

    public function sendCode(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $email = $request->get('email');
            $confirmType = $request->get('type');

            $checkCode = $this->confirmationCodeService->checkCode(ConfirmationCode::EMAIL_CODE, $confirmType, $email);
            $checkDelay = $checkCode['delay'];

            if ($checkDelay['valid']) {
                $delayValue = $this->confirmationCodeService->delayTimeCode;
                return $this->errorResponse('Интервал между отправкой должен быть не менее ' . $delayValue . 'сек.', 404);
            }

            $code = $this->confirmationCodeService->createCode(ConfirmationCode::EMAIL_CODE, $confirmType, $email);

            $this->sendEmailService->sendConfirmMessage($confirmType, $email, $code);

            $liveData = array('valid' => true, 'time' => $this->confirmationCodeService->liveTimeCode);
            $delayData = array('valid' => true, 'time' => $this->confirmationCodeService->delayTimeCode);

            $data = array('live' => $liveData, 'delay' => $delayData);

            return $this->successResponse($data, "Код подтверждения отправлен на email");

        } catch (\Exception $exception) {
            $message = $exception->getMessage();
            return $this->errorResponse($message);
        }

    }

    public function checkCode(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $email = $request->get('email');
            $confirmType = $request->get('type');
            $code = $request->get('code');

            $data = $this->confirmationCodeService->checkCode(ConfirmationCode::EMAIL_CODE, $confirmType, $email, $code);

            return $this->successResponse($data);

        } catch (\Exception $exception) {
            $message = $exception->getMessage();
            return $this->errorResponse($message);
        }
    }

}
