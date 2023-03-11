<?php

namespace App\Services;

use App\Models\Profile;
use App\Models\User;

class ProfileService
{
    public function create($data, User $user)
    {
        $profile = new Profile($data);
        return $user->profile()->save($profile);
    }
}
