<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Sale
 *
 * @property int $id
 * @property string $tariff_code
 * @property string $type
 * @property string $unit
 * @property string $currency
 * @property string $title
 * @property int $value
 * @property int $period
 * @property string $start_at
 * @property string $end_at
 * @property bool $once
 *  */
class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'tariff_code',
        'type',
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
        return $this->hasMany(ActivatedDiscount::class, 'sale_id', 'id');
    }

}
