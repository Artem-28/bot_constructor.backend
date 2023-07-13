<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\TariffProject
 *
 * @property int $id
 * @property string $tariff_code
 * @property int $respondent_count
 * @property int $admin_count
 * @property int $storage_count
 * @property int $price
 * @property string $start_at
 * @property string $end_at
 *  */
class TariffProject extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'tariff_code',
        'respondent_count',
        'admin_count',
        'storage_count',
        'price',
        'start_at',
        'end_at'
    ];
}
