<?php

namespace App\Transformers;

use App\Models\Payment\Transaction;
use League\Fractal\TransformerAbstract;

class TransactionTransformer extends TransformerAbstract
{
    public function transform(Transaction $transaction): array
    {
        return [
            'id' => $transaction->id,
            'userId' => $transaction->user_id,
            'amount' => $transaction->amount,
            'currency' => $transaction->currency,
            'type' => $transaction->type,
            'status' => $transaction->status,
        ];
    }
}
