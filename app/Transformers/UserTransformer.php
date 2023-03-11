<?php

namespace App\Transformers;

use App\Models\User;
use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        'account',
        'profile'
    ];

    public function transform(User $user): array
    {
        return [
            'id' => $user->id,
            'email' => $user->email,
            'phone' => $user->phone,
            'permissions' => $user->permissions,
        ];
    }

    public function includeAccount(User $user): \League\Fractal\Resource\Item
    {
        $account = $user->account;
        return $this->item($account, new AccountTransformer());
    }

    public function includeProfile(User $user): \League\Fractal\Resource\Item
    {
        $profile = $user->profile;
        return $this->item($profile, new ProfileTransformer());
    }
}
