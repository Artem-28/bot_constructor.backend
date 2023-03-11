<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Services\AccountService;
use App\Services\SubscriberService;
use App\Services\UserService;
use App\Transformers\UserTransformer;
use Illuminate\Http\Request;
use League\Fractal\Resource\Collection;
use Illuminate\Support\Facades\Gate;

class TeacherController extends Controller
{
    public SubscriberService $subscriberService;
    public UserService $userService;
    public AccountService $accountService;

    public function __construct
    (
        SubscriberService $subscriberService,
        UserService $userService,
        AccountService $accountService
    )
    {
        $this->middleware(['auth:sanctum']);
        $this->subscriberService = $subscriberService;
        $this->userService = $userService;
        $this->accountService = $accountService;
    }

    // Добавление учителей к аккаунту
    public function addToAccount(Request $request): \Illuminate\Http\JsonResponse
    {
        $teacherId = $request->get('teacherId', []);
        $account = auth()->user()->account;

        $response = Gate::inspect('addTeacherToAccount', $account);

        if (!$response->allowed()) {
            return $this->errorResponse($response->message(), $response->code());
        }

        try {
            $users = $this->userService->getUserByRoleAndId(Role::TEACHER, $teacherId);
            $teachers = $this->subscriberService->addTeacherToAccount($account, $users);

            $resource = new Collection($teachers, new UserTransformer());
            $data = $this->createData($resource);
            return $this->successResponse($data);

        } catch (\Exception $exception) {
            $message = $exception->getMessage();
            return $this->errorResponse($message);
        }
    }

    // Подтверждение приглашения
    public function acceptInvite(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $fromAccountId = $request->get('fromAccountId');
            $user = auth()->user();
            $confirmedAccountIds = $this->subscriberService->confirmInvite($user, $fromAccountId);

            return $this->successResponse($confirmedAccountIds);

        } catch (\Exception $exception) {
            $message = $exception->getMessage();
            return $this->errorResponse($message);
        }
    }

    // Удаление учителя из аккаунта
    //TODO доделать когда будут готовы курсы нужна проверка если учитель не привязан к курсу
    public function removeFromAccount(Request $request)
    {
        $teacherId = $request->get('teacherId');
    }
}
