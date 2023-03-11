<?php

namespace App\Services;

use App\Models\DialogGroup;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\QueryException;

class DialogGroupService extends BaseService
{
    // Создание новой группы сообщений
    public function create(array $data, User $user): \Illuminate\Database\Eloquent\Model
    {
        $data['group_creator'] = $user->id;
        return $user->adminDialogGroups()->create($data);
    }

    // Назначение администраторов
    public function addAdminToGroup($adminId, DialogGroup $group): DialogGroup
    {
        $arrayAdminIds = $this->convertParamToArray($adminId);

        foreach ($arrayAdminIds as $adminId) {
           try {
               $group->admins()->attach($adminId);
           } catch (QueryException $exception) {
               continue;
           }
        }
        return $group;
    }

    // Удаление прав администратора
    public function removeAdminFromGroup($adminId, DialogGroup $group): DialogGroup
    {
        $arrayAdminIds = $this->convertParamToArray($adminId);
        $groupCreatorId = $group->group_creator;

        $availableAdminId = array_filter($arrayAdminIds, function ($id) use ($groupCreatorId) {
            return $id !== $groupCreatorId;
        });

        $group->admins()->detach($availableAdminId);
        return $group;
    }

    // Добавление пользователя в группу
    public function addUserToGroup($userId, DialogGroup $group): DialogGroup
    {
        $arrayUserIds = $this->convertParamToArray($userId);

        foreach ($arrayUserIds as $userId) {
            try {
                $group->users()->attach(
                    $userId,
                    ['created_at' => Carbon::now()->format('Y-m-d H:i:s')]
                );
            } catch (QueryException $exception) {
                continue;
            }
        }
        return $group;
    }

    // Удаление пользователей из группы
    public function removeUserFromGroup($userId, DialogGroup $group): DialogGroup
    {
        $arrayUserIds = $this->convertParamToArray($userId);
        $admins = $group->admins->pluck('id')->toArray();

        $availableUserIds = array_filter($arrayUserIds, function ($id) use ($admins) {
            return !in_array($id, $admins);
        });

        $group->users()->detach($availableUserIds);

        return $group;
    }
}
