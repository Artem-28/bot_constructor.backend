<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\AccountType
 *
 * @property int $id
 * @property string $title
 * @property string $code
 * @property string $description
 *  */

class AccountType extends Model
{
    use HasFactory;

    const STUDENT_CODE = 'student';
    const TEACHER_CODE = 'teacher';
    const BUSINESS_CODE = 'business';

    protected $fillable = [
        'title',
        'description',
        'code',
    ];
}
