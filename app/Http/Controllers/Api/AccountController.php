<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AccountService;
use App\Transformers\AccountTransformer;
use Illuminate\Http\Request;
use League\Fractal\Resource\Item;

class AccountController extends Controller
{
    public AccountService $accountService;

    public function __construct
    (
        AccountService $accountService
    )
    {
        $this->middleware(['auth:sanctum']);
        $this->accountService = $accountService;
    }

    public function update(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $accountData = $request->only(['title', 'description']);

            $user = auth()->user();
            $account = $this->accountService->updateUserAccount($user, $accountData);

            $resource = new Item($account, new AccountTransformer());
            $data = $this->createData($resource);
            return $this->successResponse($data);

        } catch (\Exception $exception) {
            $message = $exception->getMessage();
            return $this->errorResponse($message);
        }
    }
}
