<?php

namespace App\Transformers;

use App\Models\Profile;
use League\Fractal\TransformerAbstract;

class ProfileTransformer extends TransformerAbstract
{
    public function transform(Profile $profile): array
    {
        return [
            'id' => $profile->id,
            'name' => $profile->name,
            'surname' => $profile->surname,
            'patronymic' => $profile->patronymic,
            'birthday' => $profile->birthday,
            'avatar' => $profile->avatar
        ];
    }
}
