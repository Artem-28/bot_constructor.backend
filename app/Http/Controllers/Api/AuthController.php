<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ConfirmationCode;
use App\Services\AccountService;
use App\Services\ConfirmationCodeService;
use App\Services\ProfileService;
use App\Services\RoleService;
use App\Services\UserService;
use App\Transformers\UserTransformer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use League\Fractal\Resource\Item;

class AuthController extends Controller
{

    public AccountService $accountService;
    public ProfileService $profileService;
    public UserService $userService;
    public RoleService $roleService;
    public ConfirmationCodeService $confirmationCodeService;

    public function __construct
    (
        AccountService $accountService,
        ProfileService $profileService,
        UserService $userService,
        RoleService $roleService,
        ConfirmationCodeService $confirmationCodeService

    )
    {
        $this->accountService = $accountService;
        $this->profileService = $profileService;
        $this->userService = $userService;
        $this->roleService = $roleService;
        $this->confirmationCodeService = $confirmationCodeService;
    }

    public function registration(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $accountData = $request->input('account', []);
            $profileData = $request->input('profile', []);
            $userData = $request->only(['email', 'password']);
            $rolesData = $request->get('roles');
            $confirmCode = $request->get('code');

            $checkCode = $this->confirmationCodeService->checkCode
            (
                ConfirmationCode::EMAIL_CODE,
                ConfirmationCode::REGISTRATION_TYPE,
                $userData['email'],
                $confirmCode
            );

            if (!$checkCode['live']) {
                return $this->errorResponse('Срок действия кода подтверждения истек', 404);
            }

            if (!$checkCode['matches']) {
                return $this->errorResponse('Код подтверждения введен не верно', 404);
            }

            $userData['email_verified_at'] = Carbon::now()->format('Y-m-d H:i:s');

            DB::transaction(function () use ($accountData, $profileData, $userData, $rolesData) {

                $user = $this->userService->create($userData);
                $this->roleService->assignRoles($user, $rolesData);
                $this->accountService->create($accountData, $user);
                $this->profileService->create($profileData, $user);
            });
            return $this->successResponse(null, 'Регистрация завершена');

        } catch (\Exception $exception) {
            $message = $exception->getMessage();
            return $this->errorResponse($message);
        }

    }

    public function login(Request $request): \Illuminate\Http\JsonResponse
    {
        dd('test');
        $data = $request->only('email', 'password');
        $user = $this->userService->getUserByEmail($data['email']);

        if (!$user || ! Auth::attempt($data)) {
            return $this->errorResponse('Неверный логин или пароль', 401);
        }

        $token = $user->createToken('auth_token', $user->permissions)->plainTextToken;
        $resource = new Item($user, new UserTransformer());

        $data = array(
            'access_token' => $token,
            'token_type' => 'bearer',
            'user' => $this->createData($resource)
        );
        return $this->successResponse($data);
    }
}
