<?php

namespace App\Services;

use App\Models\Account;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use function PHPUnit\Framework\isEmpty;

class AccountService
{
    public function create($data, User $user): Model
    {
        $account = new Account($data);
        return $user->account()->save($account);
    }

    public function updateUserAccount(User $user, $data)
    {
        $user->account()->update($data);
        return $user->account;
    }

    public function getAccountById(int $accountId): Account
    {
        return Account::find($accountId);
    }
}
