<?php

namespace App\Transformers;

use App\Models\DialogGroup;
use League\Fractal\TransformerAbstract;

class DialogGroupTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        'users',
        'admins'
    ];

    public function transform(DialogGroup $group): array
    {
        return [
            'id' => $group->id,
            'title' => $group->title,
            'avatar' => $group->avatar,
            'createdAt' => $group->created_at,
        ];
    }

    public function includeUsers(DialogGroup $group): \League\Fractal\Resource\Collection
    {
        $users = $group->users;
        return $this->collection($users, new UserTransformer());
    }

    public function includeAdmins(DialogGroup $group): \League\Fractal\Resource\Collection
    {
        $admins = $group->admins;
        return $this->collection($admins, new UserTransformer());
    }
}
