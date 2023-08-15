<?php

namespace App\Http\Controllers\Api;

use App\Entities\TariffPeriod;
use App\Http\Controllers\Controller;
use App\Services\DiscountService;
use App\Services\TariffService;
use App\Transformers\TariffProjectTransformer;
use App\Transformers\TariffTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;

class TariffController extends Controller
{
    public TariffService $tariffService;
    public DiscountService $discountService;

    public function __construct
    (
        TariffService $tariffService,
        DiscountService $discountService,
    )
    {
        $this->middleware(['auth:sanctum']);
        $this->tariffService = $tariffService;
        $this->discountService = $discountService;
    }

    /**
     * @throws \Exception
     */
    public function index(): \Illuminate\Http\JsonResponse
    {
        try {
            $user = auth()->user();
            $relations = array('params', 'sales' => function($query) use ($user) {
                $this->discountService::buildQueryAvailableSales($query, $user->id);
            });
            $tariffs = $this->tariffService->getTariffList(...$relations);

            $resource = new Collection($tariffs, new TariffTransformer( 'trailPeriod', 'sales', 'params'));
            $data = $this->createData($resource);

            return $this->successResponse($data);

        } catch (\Exception $exception) {
            $message = $exception->getMessage();
            return $this->errorResponse($message);
        }
    }

    public function createTariffProject(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $tariffParams = $request->get('tariffParams', array());
            $tariffId = $request->get('tariffId');

            $periodDays = $request->get('period', 0);
            $period = new TariffPeriod($periodDays);

            $user = auth()->user();

            $tariff = $this->tariffService->getTariffById($tariffId, 'params');

            $this->tariffService->validateParams($tariffParams, $tariff, true);

            $tariffProjectData = array(
                'user_id' => $user->id,
                'tariff_code' => $tariff->code,
                'period' => $period->days,
                'payment_required' => true
            );

            $tariffProject = $this->tariffService->createTariffProject($tariffProjectData, $tariffParams);

            $resource = new Item($tariffProject, new TariffProjectTransformer());
            $data = $this->createData($resource);

            return $this->successResponse($data);

            /*return DB::transaction(function () use ($request) {
                $tariffParams = $request->get('tariffParams', array());
                $tariffId = $request->get('tariffId');
                $saleId = $request->get('saleId');
                $couponCode = $request->get('couponCode');

                $sale = null;
                $coupon = null;

                $periodDays = $request->get('period', 0);
                $period = new TariffPeriod($periodDays);

                $user = auth()->user();

                // Получаем тариф по id
                $tariff = $this->tariffService->getTariffById($tariffId, 'params');

                // Расчет базовой стоимости тарифа
                $price = $this->tariffService->calculateTariffPrice($tariffParams, $tariff);

                // Получение скидки если она была передана
                if ($saleId) {
                    $sales[] = $this->discountService->getAvailableSaleById($saleId, $user->id);;
                }

                // Получение купона если он был передан
                if ($couponCode) {
                    $sales[] = $this->discountService->getAvailableCouponByCode($couponCode, $user->id);
                }
                // Параметры для валидации скидок и купонов
                $validateSaleParams = array(
                    'userId' => $user->id,
                    'tariffCode' => $tariff->code,
                    'sales' => $sales,
                    'period' => $period->days,
                );
                // Валидируем скидки и купоны
                $this->discountService->validateDiscount($validateSaleParams, true);

                // Установка скидок
                if ($sale) {
                    $price->setSale($sale);
                }
                if ($coupon) {
                    $price->setSale($coupon);
                }

                // Увеличение колличества расное периоду
                $price->increment($period->month());

                $totalPrice = $price->total();
                $paymentRequired = $totalPrice['current'] > 0;

                $tariffProjectData = array(
                    'user_id' => $user->id,
                    'tariff_code' => $tariff->code,
                    'period' => $period->days,
                    'payment_required' => $paymentRequired
                );

                $tariffProject = $this->tariffService->createTariffProject($tariffProjectData, $tariffParams);

                // Параметры для активации скидок
                $activeDiscountParams = array(
                    'user_id' => $user->id,
                    'tariff_id' => $tariffProject->id,
                );

                // Активация скидок
                if ($sale) {
                    $this->discountService->activateDiscount($sale, $activeDiscountParams);
                }
                if ($coupon) {
                    $this->discountService->activateDiscount($coupon, $activeDiscountParams);
                }

                $resource = array(
                    'period' => $period->days,
                    'price' => $price,
                    'total' => $totalPrice,
                );

                return $this->successResponse($resource);
            });*/


        } catch (\Exception $exception) {
            $message = $exception->getMessage();
            return $this->errorResponse($message);
        }
    }

    public function calculatePrice(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $tariffParams = $request->get('tariffParams', array());
            $tariffId = $request->get('tariffId');
            $saleId = $request->get('saleId');
            $couponCode = $request->get('couponCode');

            $sale = null;
            $coupon = null;

            $periodDays = $request->get('period', 0);
            $period = new TariffPeriod($periodDays);

            $user = auth()->user();

            // Получаем тариф по id
            $tariff = $this->tariffService->getTariffById($tariffId, 'params');

            // Расчет базовой стоимости тарифа
            $price = $this->tariffService->calculateTariffPrice($tariffParams, $tariff);

            // Получение скидки если она была передана
            if ($saleId) {
                $sale = $this->discountService->getAvailableSaleById($saleId, $user->id);;
            }

            // Получение купона если он был передан
            if ($couponCode) {
                $coupon = $this->discountService->getAvailableCouponByCode($couponCode, $user->id);
            }
            // Параметры для валидации скидок и купонов
            $validateSaleParams = array(
                'userId' => $user->id,
                'tariffCode' => $tariff->code,
                'sales' => array($sale, $coupon),
                'period' => $period->days,
            );
            // Валидируем скидки и купоны
            $this->discountService->validateDiscount($validateSaleParams, true);

            // Установка скидок
            if ($sale) {
                $price->setSale($sale);
            }
            if ($coupon) {
                $price->setSale($coupon);
            }
            // Увеличение колличества расное периоду
            $price->increment($period->month());

            $resource = array(
                'period' => $period->days,
                'price' => $price,
                'total' => $price->total(),
            );

            return $this->successResponse($resource);

        } catch (\Exception $exception) {
            $message = $exception->getMessage();
            return $this->errorResponse($message);
        }
    }
}
