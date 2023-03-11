<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Role;
use App\Models\User;

class AccountService
{
    public function create($data, User $user)
    {
        $account = new Account($data);
        return $user->account()->save($account);
    }

    public function getAccountById(int $accountId): Account
    {
        return Account::find($accountId);
    }
}
