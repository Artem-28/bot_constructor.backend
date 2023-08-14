<?php

namespace App\Models\Tariff;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Tariff\TariffProjectParams
 *
 * @property int $id
 * @property int $tariff_id
 * @property string $type
 * @property int $value
 * @property bool $enable
 * @property bool $infinity
 *  */
class TariffProjectParam extends Model
{
    use HasFactory;

    protected $fillable = [
        'tariff_id',
        'type',
        'value',
        'enable',
        'infinity',
    ];
}
