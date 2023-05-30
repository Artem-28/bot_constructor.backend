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
        $this->middleware(['auth:sanctum'])->only(['authUser']);
        $this->accountService = $accountService;
        $this->accountTypeService = $accountTypeService;
        $this->profileService = $profileService;
        $this->userService = $userService;
        $this->confirmationCodeService = $confirmationCodeService;
    }

    public function registration(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $userData = $request->only(['email', 'password', 'licenseAgreement']);
            $profileData = $request->input('profile', []);
            $confirmCode = $request->get('code');

            if (!$userData['licenseAgreement']) {
                return $this->errorResponse('Не приняты условия лицинзионного соглашения', 'access');
            }

            $checkCode = $this->confirmationCodeService->checkCode
            (
                ConfirmationCode::EMAIL_CODE,
                ConfirmationCode::REGISTRATION_TYPE,
                $userData['email'],
                $confirmCode
            );

            $liveCheckCode = $checkCode['live'];
            if (!$liveCheckCode['valid']) {
                return $this->errorResponse('Срок действия кода подтверждения истек', 'confirm_code');
            }

            if (!$checkCode['matches']) {
                return $this->errorResponse('Код подтверждения введен не верно', 'confirm_code');
            }

            $userData['email_verified_at'] = Carbon::now()->format('Y-m-d H:i:s');

            DB::transaction(function () use ($profileData, $userData) {
                $user = $this->userService->create($userData);
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
            return $this->errorResponse('Неверный логин или пароль', 'login', 401);
        }

        $token = $user->createToken('auth_token', $user->permissions)->plainTextToken;
        $resource = new Item($user, new UserTransformer());

        $data = array(
            'access_token' => $token,
            'token_type' => 'Bearer',
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

            $liveCheckCode = $checkCode['live'];
            if (!$liveCheckCode['valid']) {
                return $this->errorResponse('Срок действия кода подтверждения истек', 'confirm_code');
            }

            if (!$checkCode['matches']) {
                return $this->errorResponse('Код подтверждения введен не верно', 'confirm_code');
            }

            $this->userService->changePassword($email, $password);
            return $this->successResponse(null, 'Пароль успешно изменен');

        } catch (\Exception $exception) {
            $message = $exception->getMessage();
            $this->errorResponse($message);
        }

    }

    public function checkExists(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $email = $request->get('email');
            $isExist = $this->userService->isExistsUserByEmail($email);
            $data = array('exists' => $isExist);
            return $this->successResponse($data);

        } catch (\Exception $exception) {
            $message = $exception->getMessage();
            return $this->errorResponse($message);
        }
    }

    public function authUser(): \Illuminate\Http\JsonResponse
    {
        try {
            $user = Auth::user();
            $resource = new Item($user, new UserTransformer());
            $data = $this->createData($resource);
            return $this->successResponse($data);

        } catch (\Exception $exception) {
            $message = $exception->getMessage();
            return $this->errorResponse($message);
        }
    }
}
