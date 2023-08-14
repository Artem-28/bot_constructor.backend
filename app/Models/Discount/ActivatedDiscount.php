<?php

namespace App\Models\Discount;

use App\Models\Project\Project;
use App\Models\Tariff\TariffProject;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivatedDiscount extends Model
{
    use HasFactory;

    protected $fillable = [
        'coupon_id',
        'sale_id',
        'user_id',
        'tariff_id'
    ];

    // Активированный купон
    public function coupon(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Coupon::class, 'id', 'coupon_id');
    }

    // Активированная скидка
    public function sale(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Sale::class, 'id', 'sale_id');
    }

    // Пользователь к который применил скидку или купон
    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'id', 'user_id');
    }

    // проект к которому применена скидка или купон
    public function tariff(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(TariffProject::class, 'id', 'tariff_id');
    }
}
