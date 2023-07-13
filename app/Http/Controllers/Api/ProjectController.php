<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\TariffService;
use Illuminate\Http\Request;

class ProjectController extends Controller
{

    public TariffService $tariffService;

    public function __construct
    (
        TariffService $tariffService,
    )
    {
        $this->middleware(['auth:sanctum']);
        $this->tariffService = $tariffService;
    }
    public function store(Request $request)
    {
        try {

        } catch (\Exception $exception) {
            $message = $exception->getMessage();
            return $this->errorResponse($message);
        }
    }
}
