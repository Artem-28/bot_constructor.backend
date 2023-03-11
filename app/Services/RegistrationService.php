<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class RegistrationService
{
    public function registration($data,  Account $account, Profile $profile)
    {
        $user = new User($data);

    }
}
