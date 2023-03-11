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
        }
    }

    public function sendConfirmRegistrationMessage(string $toEmail, string $code)
    {
        $data = array('code'=> $code);

        $message = new EmailMessage($data);

        $message->to($toEmail)
            ->subject('Подтверждение регистрации')
            ->view('emails.confirm')
            ->send();
    }

}
