<?php

namespace App\Transformers;

use App\Models\Account;
use League\Fractal\TransformerAbstract;

class AccountTransformer extends TransformerAbstract
{
    public function transform(Account $account): array
    {
        return [
            'id' => $account->id,
            'tariffId' => $account->tariff_id,
            'title' => $account->title,
            'description' => $account->description
        ];
    }
}
