<?php

namespace App\Policies;

use App\Models\Account;
use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class AccountPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Account  $account
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function addTeacherToAccount(User $user, Account $account)
    {
        if(!$user->hasPermission(Role::BUSINESS)) {
            return Response::deny('У вас недостаточно прав для добавления учителей', 403);
        }
        return Response::allow();
    }
}
