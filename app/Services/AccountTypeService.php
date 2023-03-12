<?php

namespace App\Services;

use App\Models\Account;
use App\Models\AccountType;
use Illuminate\Database\Eloquent\Model;

class AccountTypeService
{
    public function getList(): \Illuminate\Database\Eloquent\Collection|array
    {
        return AccountType::all();
    }

    public function assignTypesToAccount(Account|Model $account, $types): array
    {
        return $account->accountTypes()->sync($types);
    }
}
