<?php

namespace App\Services;

use App\Models\ConfirmationCode;
use Carbon\Carbon;

class ConfirmationCodeService
{
    public int $liveTimeCode;
    public int $delayTimeCode;

    public function __construct()
    {
        $this->liveTimeCode = 360;
        $this->delayTimeCode = 360;
    }

    private function generateCode(int $codeLength): string
    {
        $random = array();

        for ($c = -1; $c < $codeLength - 1; $c++) {
            array_push($random, mt_rand(0, 9));
            shuffle($random);
        }

        return join('', $random);
    }

    private function saveEmailCode($confirmType, $email): string
    {
        $data = array(
            'code' => $this->generateCode(6),
            'type' => $confirmType
        );

        $confirmCode = ConfirmationCode::updateOrCreate([
            'email' => $email,
        ], $data);

        return $confirmCode->code;
    }

    private function savePhoneCode($phone)
    {
        return null;
    }

    private function checkIsLiveCode(ConfirmationCode $confirmationCode): bool
    {
        $updatedDate = $confirmationCode->updated_at;
        $updatedTimestamp = Carbon::parse($updatedDate)->timestamp;
        $nowTimestamp = Carbon::now()->timestamp;

        return  $updatedTimestamp + $this->liveTimeCode > $nowTimestamp;
    }

    private function checkIsDelayCode(ConfirmationCode $confirmationCode): bool
    {
        $updatedDate = $confirmationCode->updated_at;
        $updatedTimestamp = Carbon::parse($updatedDate)->timestamp;
        $nowTimestamp = Carbon::now()->timestamp;

        return  $updatedTimestamp + $this->delayTimeCode > $nowTimestamp;
    }

    private function getEmailCode($email, $confirmType)
    {
        return ConfirmationCode::where(['email' => $email, 'type' => $confirmType])->first();
    }

    private function checkEmailCode
    (
        string $confirmType,
        string $email,
        string|null $confirmCode = null
    ): array
    {
        $dataCode = $this->getEmailCode($email, $confirmType);

        $live = false;
        $delay = false;
        $matches = false;

        if ($dataCode) {
            $live = $this->checkIsLiveCode($dataCode);
            $delay = $this->checkIsDelayCode($dataCode);
            $matches =  $dataCode->code === $confirmCode;
        }

        return array('live' => $live, 'delay' => $delay, 'matches' => $matches);
    }

    private function checkPhoneCode(int $phone, string $confirmCode): array
    {
        return array('live' => false, 'matches' => false);
    }

    public function createCode(string $type, string $confirmType, string $address)
    {

        return match ($type) {
            ConfirmationCode::EMAIL_CODE => $this->saveEmailCode($confirmType, $address),
            ConfirmationCode::PHONE_CODE => $this->savePhoneCode($address),
            default => null,
        };
    }

    public function checkCode
    (
        string $type,
        string $confirmType,
        string $address,
        null|string $confirmCode = null
    )
    {

        return match ($type) {
            ConfirmationCode::EMAIL_CODE => $this->checkEmailCode($confirmType, $address, $confirmCode),
            ConfirmationCode::PHONE_CODE => $this->checkPhoneCode($address, $confirmCode),
            default => null,
        };
    }
}
