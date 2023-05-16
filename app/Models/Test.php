<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Test
 *
 * @property int $id
 * @property int $account_id
 * @property string $title
 * @property string $description
 *  */
class Test extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'account_id',
        'title',
        'description',
    ];
}
