<?php

namespace App\Transformers;

use App\Models\AccountType;
use League\Fractal\TransformerAbstract;

class AccountTypeTransformer extends TransformerAbstract
{
    public function transform(AccountType $accountType): array
    {
        return [
            'id' => $accountType->id,
            'code' => $accountType->code,
            'title' => $accountType->title,
            'description' => $accountType->description
        ];
    }
}
