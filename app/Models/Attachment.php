<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Attachment
 *
 * @property int $id
 * @property int $account_id
 * @property string $name
 * @property int $size
 * @property string $type
 * @property string $path
 *  */
class Attachment extends Model
{
    use HasFactory;

    const DISK = 'public';
    const STORAGE_PATH = 'attachments';

    const STATUS_SUCCESS = 'success';
    const STATUS_FAILED = 'failed';
    const STATUS_EXISTS = 'exists';

    protected $fillable = [
        'account_id',
        'name',
        'type',
        'size',
        'path'
    ];

    public function account(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Account::class);
    }

}
