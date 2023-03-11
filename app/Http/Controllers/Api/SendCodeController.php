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
        return $this->successResponse(null, "Код подтверждения отправлен на email");
        try {
            $email = $request->get('email');
            $confirmType = $request->get('type');

            $code = $this->confirmationCodeService->createCode(ConfirmationCode::EMAIL_CODE, $confirmType, $email);

            $this->sendEmailService->sendConfirmMessage($confirmType, $email, $code);

            return $this->successResponse(null, "Код подтверждения отправлен на email");

        } catch (\Exception $exception) {
            $message = $exception->getMessage();
            return $this->errorResponse($message);
        }

    }
}
