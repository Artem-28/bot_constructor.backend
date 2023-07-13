<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\DiscountService;
use App\Transformers\CouponTransformer;
use Illuminate\Http\Request;
use League\Fractal\Resource\Collection;

class DiscountController extends Controller
{
    public DiscountService $discountService;

    public function __construct
    (
        DiscountService $discountService,
    )
    {
        $this->middleware(['auth:sanctum']);
        $this->discountService = $discountService;
    }

    public function getCoupons(): \Illuminate\Http\JsonResponse
    {
        try {
            $user = auth()->user();
            $coupons = $this->discountService->getAvailableCoupons($user->id);
            $resource = new Collection($coupons, new CouponTransformer());

            $data = $this->createData($resource);
            return $this->successResponse($data);

        } catch (\Exception $exception) {
            $message = $exception->getMessage();
            return $this->errorResponse($message);
        }
    }
}
