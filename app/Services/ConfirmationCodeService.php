<?php

namespace App\Services;

use App\Models\ConfirmationCode;
use Carbon\Carbon;

class ConfirmationCodeService
{
    private int $liveTimeCode;

    public function __construct()
    {
        $this->liveTimeCode = 360;
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

    private function getEmailCode($email, $confirmType)
    {
        return ConfirmationCode::where(['email' => $email, 'type' => $confirmType])->first();
    }

    private function checkEmailCode(string $confirmType, string $email, string $confirmCode): array
    {
        $dataCode = $this->getEmailCode($email, $confirmType);

        if (!$dataCode) {
            throw new \Exception('код подтверждения не найден', 404);
        }
        $live = $this->checkIsLiveCode($dataCode);
        $matches =  $dataCode->code === $confirmCode;

        return array('live' => $live, 'matches' => $matches);
    }

    private function checkPhoneCode(int $phone, string $confirmCode): array
    {
        return array('live' => false, 'matches' => false);
    }

    public function createCode(string $type, string $confirmType, string $address)
    {
        switch ($type) {
            case ConfirmationCode::EMAIL_CODE:
                return $this->saveEmailCode($confirmType, $address);
            case ConfirmationCode::PHONE_CODE:
                return $this->savePhoneCode($address);
        }
    }

    public function checkCode
    (
        string $type,
        string $confirmType,
        string $address,
        string $confirmCode
    )
    {
        switch ($type) {
            case ConfirmationCode::EMAIL_CODE:
                return $this->checkEmailCode($confirmType, $address, $confirmCode);
            case ConfirmationCode::PHONE_CODE:
                return $this->checkPhoneCode($address, $confirmCode);
        }
    }
}
