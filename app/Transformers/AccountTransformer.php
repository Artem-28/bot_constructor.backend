<?php

namespace App\Transformers;

use App\Models\Account;
use League\Fractal\TransformerAbstract;

class AccountTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        'types',
    ];

    public function transform(Account $account): array
    {
        return [
            'id' => $account->id,
            'tariffId' => $account->tariff_id,
            'title' => $account->title,
            'description' => $account->description
        ];
    }

    public function includeTypes(Account $account)
    {
        $accountTypes = $account->accountTypes;
        return $this->collection($accountTypes, new AccountTypeTransformer());
    }
}
