<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\QueryException;

class SubscriberService
{
    const STATUS_USER_CONFIRMATION = 'userConfirmation';
    const STATUS_ACCEPT = 'accept';

    // Добавление учителей к аккаунту
    public function addTeacherToAccount(Account $account, Collection $users): \Illuminate\Support\Collection
    {
        $teachers = collect();
        foreach ($users as $user) {

            try {
                $account->teachers()->attach(
                    $user->id,
                    [
                        'status' => SubscriberService::STATUS_USER_CONFIRMATION,
                        'created_at' => Carbon::now()->format('Y-m-d H:i:s')
                    ]
                );
                $teachers->push($user);
            } catch (QueryException $exception) {
                continue;
            }
        }
        return $teachers;
    }

    public function removeTeachersFromAccount(Account $account, $teacherId)
    {

    }

    // Подтверждение приглашения
    public function confirmInvite($user, $fromAccountId): array
    {
        $arrayAccountIds = $fromAccountId;

        if (!is_array($fromAccountId)) {
            $arrayAccountIds = array($fromAccountId);
        }

        $confirmedAccountIds = array();

        foreach ($arrayAccountIds as $accountId) {
            try {
                $user->teacherAccounts()->updateExistingPivot(
                        $accountId,
                        [
                            'status' => SubscriberService::STATUS_ACCEPT,
                            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                        ]
                );
                array_push($confirmedAccountIds, $accountId);
            } catch (QueryException $exception) {
                continue;
            }
        }
        return $confirmedAccountIds;
    }
}
