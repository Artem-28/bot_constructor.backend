<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\TariffParam
 *
 * @property int $id
 * @property string $tariff_code
 * @property string $type
 * @property int $min
 * @property int $max
 * @property float $price
 * @property float $price_infinity
 * @property boolean $infinity
 * @property boolean $enable
 *  */
class TariffParam extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'tariff_code',
        'type',
        'min',
        'max',
        'price',
        'price_infinity',
        'infinity',
        'enable'
    ];
}
