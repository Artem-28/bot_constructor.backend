<?php

namespace App\Enums;

class EnumPayment
{
    const STATUS_CREATED = 'created';
    const STATUS_PENDING = 'pending';
    const STATUS_WAITING_FOR_CAPTURE = 'waiting_for_capture';
    const STATUS_CANCELED = 'failed';
    const STATUS_SUCCEEDED = 'succeeded';

    const TRANSACTION_TYPE_TARIFF = 'tariff';
}
