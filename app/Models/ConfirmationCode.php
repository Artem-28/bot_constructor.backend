<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ConfirmationCode
 *
 * @property int $id
 * @property string $email
 * @property string|null $phone
 * @property string $code
 * @property string $updated_at
 *  */

class ConfirmationCode extends Model
{
    use HasFactory;

    const EMAIL_CODE = 'email_code';
    const PHONE_CODE = 'phone_code';
    const REGISTRATION_TYPE = 'registrationType';

    protected $fillable = [
        'email',
        'phone',
        'code',
        'type'
    ];
}
