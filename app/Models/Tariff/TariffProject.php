<?php

namespace App\Models\Tariff;

use App\Enums\EnumPayment;
use App\Enums\EnumTariff;
use App\Models\Payment\Transaction;
use App\Models\Project\Project;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use function PHPUnit\Framework\isEmpty;

/**
 * App\Models\Tariff\TariffProject
 *
 * @property int $id
 * @property int $transaction_id
 * @property int $user_id
 * @property string $tariff_code
 * @property string $period
 * @property string $start_at
 * @property string $end_at
 * @property string $status
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
        'transaction_id',
        'user_id',
        'tariff_code',
        'period',
        'start_at',
        'end_at'
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime'
    ];

    protected $appends = [
        'status'
    ];

    public function transaction(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Transaction::class, 'transaction_id', 'id');
    }

    public function params(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(TariffProjectParam::class, 'tariff_id', 'id');
    }

    public function project(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Project::class, 'id', 'tariff_id');
    }

    public function tariff(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Tariff::class,'tariff_code', 'code');
    }

    /**
     * @throws \Exception
     */
    public function getStatusAttribute(): string
    {
        $startDate = new \DateTime($this->start_at);
        $endDate = new \DateTime($this->end_at);
        $currentDate = new \DateTime();
        $validStartDate = $startDate->getTimestamp() < $currentDate->getTimestamp();
        $validEndDate = $endDate->getTimestamp() > $currentDate->getTimestamp();

        if ($validEndDate && $validStartDate) {
            return EnumTariff::STATUS_ACTIVE;
        }
        if ($this->transaction->status === EnumPayment::STATUS_CREATED && !$this->start_at) {
            return EnumTariff::STATUS_NOT_USED;
        }
        if ($this->transaction->status === EnumPayment::STATUS_WAITING_FOR_CAPTURE) {
            return EnumTariff::STATUS_PENDING;
        }
        return EnumTariff::STATUS_INACTIVE;
    }

    public function paramsToArray(): array
    {
        $params = $this->params;
        $formatParams = array();
        if (!$params) return $formatParams;

        foreach ($params as $param) {
            if ($param->infinity) {
                $formatParams[$param->type] = 'infinity';
                continue;
            }
            if (!$param->enable) {
                continue;
            }
            $formatParams[$param->type] = $param->value;
        }
        return $formatParams;
    }
}
