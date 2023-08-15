<?php

namespace App\Models\Tariff;

use App\Models\Discount\Sale;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Tariff\Tariff
 *
 * @property int $id
 * @property string $code
 * @property int $base_price
 * @property bool $public
 *  */
class Tariff extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'code',
        'base_price',
        'public'
    ];

    public function params(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(TariffParam::class, 'tariff_code', 'code');
    }

    public function sales(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Sale::class, 'tariff_code', 'code');
    }
}
