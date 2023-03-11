<?php

namespace App\Policies;

use App\Models\DialogGroup;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class DialogGroupPolicy
{
    use HandlesAuthorization;
    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\DialogGroup  $group
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function addUser(User $user, DialogGroup $group)
    {
        if (!$group->userIsAdmin($user->id)) {
            return Response::deny('У вас недостаточно прав для добавления участников', 403);
        }
        return Response::allow();
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\DialogGroup  $group
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function addAdmin(User $user, DialogGroup $group)
    {
        if (!$group->userIsCreator($user->id)) {
            return Response::deny('У вас недостаточно прав назначения администраторов', 403);
        }
        return Response::allow();
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\DialogGroup  $group
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function removeAdmin(User $user, DialogGroup $group)
    {
        if (!$group->userIsCreator($user->id)) {
            return Response::deny('У вас недостаточно для снятия прав с администратора', 403);
        }
        return Response::allow();
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\DialogGroup  $group
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function removeUser(User $user, DialogGroup $group)
    {
        if (!$group->userIsAdmin($user->id)) {
            return Response::deny('У вас недостаточно прав для удаление участников', 403);
        }
        return Response::allow();
    }

}
