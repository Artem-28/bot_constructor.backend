<?php

namespace App\Services;

use App\Models\User;

class RoleService
{
    public function assignRoles(User $user, array $roles)
    {
        $user->roles()->sync($roles);
    }

}
