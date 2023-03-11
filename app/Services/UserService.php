<?php

namespace App\Services;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class UserService
{
    public function create($data): User
    {
        $user = new User([
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'email_verified_at' =>$data['email_verified_at'],
        ]);
        $user->save();

        return $user;
    }

    public function getUserByEmail(string $email)
    {
        return User::where('email', $email)->first();
    }

    public function getUserByIds(...$ids): Collection
    {
        return User::find($ids);
    }

    // Получение пользователей по id с учетом ролей
    public function getUserByRoleAndId($role, $id): Collection
    {
        $arrayIds = $id;
        $arrayRoles = $role;

        if (!is_array($id)) {
            $arrayIds = array($id);
        }

        if (!is_array($role)) {
            $arrayRoles = array($role);
        }

        return User::whereIn('id', $arrayIds)
            ->whereHas('roles', function ($query) use ($arrayRoles) {
                    $query->whereIn('slug', $arrayRoles);
            })->get();

    }

}
