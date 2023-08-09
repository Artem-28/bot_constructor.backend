<?php

namespace App\Models\Payment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
/**
 * App\Models\Payment
 *
 * @property int $id
 * @property int $user_id
 * @property string $description
 * @property int $amount
 * @property string $status
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
        'amount',
        'description',
        'status'
    ];
}
