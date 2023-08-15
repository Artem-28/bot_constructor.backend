<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ProjectService;
use App\Services\TariffService;
use App\Transformers\CouponTransformer;
use App\Transformers\ProjectTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;

class ProjectController extends Controller
{

    public TariffService $tariffService;
    public ProjectService $projectService;

    public function __construct
    (
        TariffService $tariffService,
        ProjectService $projectService,
    )
    {
        $this->middleware(['auth:sanctum']);
        $this->tariffService = $tariffService;
        $this->projectService = $projectService;
    }

    public function index(): \Illuminate\Http\JsonResponse
    {
        try {
            $user = auth()->user();
            $relations = array('tariff', 'params');
            $projects = $this->projectService->getProjects($user->id, ...$relations);

            $resource = new Collection($projects, new ProjectTransformer(...$relations));

            $data = $this->createData($resource);
            return $this->successResponse($data);

        } catch (\Exception $exception) {
            $message = $exception->getMessage();
            return $this->errorResponse($message);
        }
    }
    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $user = auth()->user();
            $projectData = array(
                'title' => $request->get('title'),
                'user_id' => $user->id
            );
            $project = $this->projectService->create($projectData);
            $resource = new Item($project, new ProjectTransformer());

            $data = $this->createData($resource);
            return $this->successResponse($data);

        } catch (\Exception $exception) {
            $message = $exception->getMessage();
            return $this->errorResponse($message);
        }
    }
}
