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

            if ($checkCode['delay']) {
                $delayValue = $this->confirmationCodeService->delayTimeCode;
                return $this->errorResponse('Интервал между отправкой должен быть не менее ' . $delayValue . 'сек.', 404);
            }

            $code = $this->confirmationCodeService->createCode(ConfirmationCode::EMAIL_CODE, $confirmType, $email);

            $this->sendEmailService->sendConfirmMessage($confirmType, $email, $code);

            $data = array(
                'live' => $this->confirmationCodeService->liveTimeCode,
                'delay' => $this->confirmationCodeService->delayTimeCode,
            );

            return $this->successResponse($data, "Код подтверждения отправлен на email");

        } catch (\Exception $exception) {
            $message = $exception->getMessage();
            return $this->errorResponse($message);
        }

    }
}
