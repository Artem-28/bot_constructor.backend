<?php

namespace App\Services;

use App\Mail\EmailMessage;
use App\Models\ConfirmationCode;
use Illuminate\Support\Facades\Mail;

class SendEmailService
{

    public function sendConfirmMessage(string $type, string $toEmail, string $code)
    {
        switch ($type) {
            case ConfirmationCode::REGISTRATION_TYPE:
                $this->sendConfirmRegistrationMessage($toEmail, $code);
                break;
            case ConfirmationCode::CHANGE_PASSWORD_TYPE:
                $this->sendConfirmChangePasswordMessage($toEmail, $code);
                break;
            default:
                break;
        }
    }

    private function sendConfirmChangePasswordMessage(string $toEmail, string $code)
    {
        $appName = env('APP_NAME');
        $data = array('code'=> $code, 'content' => "Код, для изменения пароля в приложении $appName:");

        $message = new EmailMessage($data);

        $message->to($toEmail)
            ->subject('Подтверждение регистрации')
            ->view('emails.confirm')
            ->send();
    }

    private function sendConfirmRegistrationMessage(string $toEmail, string $code)
    {
        $appName = env('APP_NAME');
        $data = array('code'=> $code, 'content' => "Код, для подтверждения регистрации в приложении $appName:");

        $message = new EmailMessage($data);

        $message->to($toEmail)
            ->subject('Подтверждение регистрации')
            ->view('emails.confirm')
            ->send();
    }

}
