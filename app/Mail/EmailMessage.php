<?php

namespace App\Mail;

use Illuminate\Support\Facades\Mail;

class EmailMessage
{
    private string $fromEmail;
    private string $fromName;
    private string $appName;
    private string $toEmail;
    private string $toName;
    private string $view;
    private string $subject;
    private array $data;

    public function __construct(array $data)
    {
        $this->fromEmail = env('MAIL_USERNAME');
        $this->fromName = env('APP_NAME');
        $this->appName = env('APP_NAME');
        $this->data = $data;
    }

    private function sendMessage()
    {
        Mail::send($this->view, $this->data, function ($message) {

            $message->to($this->toEmail, $this->toName)->subject($this->subject);
            $message->from( $this->fromEmail,  $this->fromName);
        });
    }

    public function to(string $email, string $name = ''): EmailMessage
    {
        $this->toEmail = $email;
        $this->toName = $name;
        return $this;
    }

    public function view($view): EmailMessage
    {
        $this->view = $view;
        return $this;
    }

    public function subject(string $subject): EmailMessage
    {
        $this->subject = $subject;
        return $this;
    }

    public function send()
    {
        $this->sendMessage();
    }
}
