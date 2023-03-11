<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DialogGroup;
use App\Services\DialogGroupService;
use App\Transformers\DialogGroupTransformer;
use Illuminate\Http\Request;
use League\Fractal\Resource\Item;
use Illuminate\Support\Facades\Gate;

class DialogGroupController extends Controller
{
    public DialogGroupService $dialogGroupService;

    public function __construct(DialogGroupService $dialogGroupService)
    {
        $this->middleware(['auth:sanctum']);
        $this->dialogGroupService = $dialogGroupService;
    }

    // Создание группы
    public function createGroup(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $data = $request->only(['title', 'avatar']);
            $membersGroup = $request->get('userIds', []);
            $user = auth()->user();
            array_push($membersGroup, $user->id);
            $group = $this->dialogGroupService->create($data, $user);
            $group = $this->dialogGroupService->addUserToGroup($membersGroup, $group);

            $resource = new Item($group, new DialogGroupTransformer());

            return response()->json([
                'success' => true,
                'group' => $this->createData($resource)
            ]);

        } catch (\Exception $exception) {

            return response()->json([
                'success' => false,
                'message' => $exception->getMessage()
            ], 500);
        }
    }

    // Добавление участников в группу
    public function addUser(Request $request, DialogGroup $group): \Illuminate\Http\JsonResponse
    {
        $response = Gate::inspect('addUser', $group);

        if (!$response->allowed()) {

            return response()->json([
                'success' => false,
                'message' => $response->message()
            ], $response->code());
        }

        try {

            $usersId = $request->get('userId');
            $group = $this->dialogGroupService->addUserToGroup($usersId, $group);

            $resource = new Item($group, new DialogGroupTransformer());

            return response()->json([
                'success' => true,
                'group' => $this->createData($resource)
            ]);

        } catch (\Exception $exception) {

            return response()->json([
                'success' => false,
                'message' => $exception->getMessage()
            ], 500);
        }
    }

    // Добавление администраторов в группу
    public function addAdmin(Request $request, DialogGroup $group): \Illuminate\Http\JsonResponse
    {
        $response = Gate::inspect('addAdmin', $group);

        if (!$response->allowed()) {

            return response()->json([
                'success' => false,
                'message' => $response->message()
            ], $response->code());
        }

        try {
            $adminId = $request->get('adminId');
            $this->dialogGroupService->addAdminToGroup($adminId, $group);
            $group = $this->dialogGroupService->addUserToGroup($adminId, $group);

            $resource = new Item($group, new DialogGroupTransformer());

            return response()->json([
                'success' => true,
                'group' => $this->createData($resource)
            ]);

        } catch (\Exception $exception) {

            return response()->json([
                'success' => false,
                'message' => $exception->getMessage()
            ], 500);
        }
    }

    // Удаление прав администратора
    public function removeAdmin(Request $request, DialogGroup $group): \Illuminate\Http\JsonResponse
    {
        $response = Gate::inspect('removeAdmin', $group);

        if (!$response->allowed()) {

            return response()->json([
                'success' => false,
                'message' => $response->message()
            ], $response->code());
        }

        try {
            $adminId = $request->get('adminId');
            $group = $this->dialogGroupService->removeAdminFromGroup($adminId, $group);

            $resource = new Item($group, new DialogGroupTransformer());

            return response()->json([
                'success' => true,
                'group' => $this->createData($resource)
            ]);

        } catch (\Exception $exception) {

            return response()->json([
                'success' => false,
                'message' => $exception->getMessage()
            ], 500);
        }
    }

    // Удаление пользователей из группы
    public function removeUser(Request $request, DialogGroup $group): \Illuminate\Http\JsonResponse
    {
        $response = Gate::inspect('removeUser', $group);

        if (!$response->allowed()) {

            return response()->json([
                'success' => false,
                'message' => $response->message()
            ], $response->code());
        }

        try {
            $userId = $request->get('userId');
            $group = $this->dialogGroupService->removeUserFromGroup($userId, $group);

            $resource = new Item($group, new DialogGroupTransformer());

            return response()->json([
                'success' => true,
                'group' => $this->createData($resource)
            ]);
        } catch (\Exception $exception) {

            return response()->json([
                'success' => false,
                'message' => $exception->getMessage()
            ], 500);
        }
    }
}
