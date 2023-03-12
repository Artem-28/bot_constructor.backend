<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ConfirmationCode;
use App\Services\AccountService;
use App\Services\AccountTypeService;
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
use phpDocumentor\Reflection\Types\This;

class AuthController extends Controller
{

    public AccountService $accountService;
    public AccountTypeService $accountTypeService;
    public ProfileService $profileService;
    public UserService $userService;
    public ConfirmationCodeService $confirmationCodeService;

    public function __construct
    (
        AccountService $accountService,
        AccountTypeService $accountTypeService,
        ProfileService $profileService,
        UserService $userService,
        ConfirmationCodeService $confirmationCodeService

    )
    {
        $this->accountService = $accountService;
        $this->accountTypeService = $accountTypeService;
        $this->profileService = $profileService;
        $this->userService = $userService;
        $this->confirmationCodeService = $confirmationCodeService;
    }

    public function registration(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $accountData = $request->input('account', []);
            $profileData = $request->input('profile', []);
            $userData = $request->only(['email', 'password', 'licenseAgreement']);
            $confirmCode = $request->get('code');

            if (!$userData['licenseAgreement']) {
                return $this->errorResponse('Не приняты условия лицинзионного соглашения');
            }

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

            DB::transaction(function () use ($accountData, $profileData, $userData) {

                $user = $this->userService->create($userData);
                $account = $this->accountService->create($accountData, $user);
                $this->accountTypeService->assignTypesToAccount($account, $accountData['accountTypes']);
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

    public function changePassword(Request $request)
    {
        try {
            $email = $request->get('email');
            $password = $request->get('password');
            $confirmCode = $request->get('code');

            $checkCode = $this->confirmationCodeService->checkCode
            (
                ConfirmationCode::EMAIL_CODE,
                ConfirmationCode::CHANGE_PASSWORD_TYPE,
                $email,
                $confirmCode
            );

            if (!$checkCode['live']) {
                return $this->errorResponse('Срок действия кода подтверждения истек', 404);
            }

            if (!$checkCode['matches']) {
                return $this->errorResponse('Код подтверждения введен не верно', 404);
            }

            $this->userService->changePassword($email, $password);
            return $this->successResponse(null, 'Пароль успешно изменен');

        } catch (\Exception $exception) {
            $message = $exception->getMessage();
            $this->errorResponse($message);
        }

    }
}
