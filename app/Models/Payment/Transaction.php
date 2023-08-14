<?php

namespace App\Models\Payment;

use App\Enums\EnumPayment;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
/**
 * App\Models\Payment
 *
 * @property int $id
 * @property int $user_id
 * @property string $payment_id
 * @property string $type
 * @property int $amount
 * @property string $status
 * @property boolean $confirmed
 * @property string $currency
 *  */
class Transaction extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'currency',
        'amount',
        'type',
        'status',
        'payment_id',
    ];

    protected $appends = [
        'confirmed'
    ];

    public function getConfirmedAttribute(): bool
    {
        return $this->status === EnumPayment::STATUS_SUCCEEDED;
    }
}
