<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Coupon
 *
 * @property int $id
 * @property int $user_id
 * @property string $code
 * @property string $unit
 * @property string $currency
 * @property string $title
 * @property int $value
 * @property int $period
 * @property string $start_at
 * @property string $end_at
 * @property bool $once
 *  */
class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'code',
        'unit',
        'currency',
        'title',
        'period',
        'value',
        'start_at',
        'end_at',
        'once'
    ];

    protected $casts = [
        'once' => 'boolean'
    ];

    public function activated(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ActivatedDiscount::class, 'coupon_id', 'id');
    }
}
