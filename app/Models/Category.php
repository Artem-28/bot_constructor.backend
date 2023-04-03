<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Category
 *
 * @property int $id
 * @property int $order
 * @property string $title
 * @property string $code
 * @property string $type
 *  */
class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'code',
        'type',
        'order',
    ];
}
