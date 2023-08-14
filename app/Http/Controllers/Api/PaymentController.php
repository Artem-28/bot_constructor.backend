<?php

namespace App\Http\Controllers\Api;

use App\Entities\TariffPeriod;
use App\Enums\EnumPayment;
use App\Enums\EnumTariff;
use App\Http\Controllers\Controller;
use App\Models\Payment\Transaction;
use App\Services\DiscountService;
use App\Services\PaymentService;
use App\Services\ProjectService;
use App\Services\TariffService;
use App\Transformers\TransactionTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use League\Fractal\Resource\Item;
use YooKassa\Model\Metadata;

class PaymentController extends Controller
{
    public PaymentService $paymentService;
    public TariffService $tariffService;
    public DiscountService $discountService;
    public ProjectService $projectService;
    public function __construct
    (
        PaymentService $paymentService,
        TariffService $tariffService,
        DiscountService $discountService,
        ProjectService $projectService,

    )
    {
        $this->middleware(['auth:sanctum'])->except(['callback']);
        $this->paymentService = $paymentService;
        $this->tariffService = $tariffService;
        $this->discountService = $discountService;
        $this->projectService = $projectService;
    }

    public function paymentTariff(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            return DB::transaction(function () use ($request) {
                $user = auth()->user();
                $projectId = $request->get('projectId');
                $tariffProjectId = $request->get('tariffProjectId');
                $saleId = $request->get('saleId');
                $couponCode = $request->get('couponCode');

                $sale = null;
                $coupon = null;

                $project = $this->projectService->getProjectById($projectId, $user->id);
                if (!$project) {
                    return $this->errorResponse('error.payment');
                }

                $tariffProjectRelations = array(
                    'params',
                    'tariff' => array('params')
                );
                $tariffProject = $this->tariffService->getTariffProjectById($tariffProjectId, $user->id, ...$tariffProjectRelations);

                if (!$tariffProject) {
                    return $this->errorResponse('error.payment');
                }
                if ($tariffProject->status !== EnumTariff::STATUS_NOT_USED) {
                    return $this->errorResponse('error.payment');
                }

                $tariff = $tariffProject->tariff;
                $tariffParams = $tariffProject->paramsToArray();

                $price = $this->tariffService->calculateTariffPrice($tariffParams, $tariff);
                $period = new TariffPeriod($tariffProject->period);
                $price->increment($period->month());

                // Получение скидок если они передавались
                if ($saleId) {
                    $sale = $this->discountService->getAvailableSaleById($saleId, $user->id);
                }
                if ($couponCode) {
                    $coupon = $this->discountService->getAvailableCouponByCode($couponCode, $user->id);
                }

                // Параметры необходимые для валидации скидок
                $validateSaleParams = array(
                    'userId' => $user->id,
                    'tariffCode' => $tariff->code,
                    'sales' => array($sale, $coupon),
                    'period' => $period->days,
                );
                // Валидируем скидки и купоны
                $this->discountService->validateDiscount($validateSaleParams, true);

                // Параметры для активации скидок
                $activeDiscountParams = array(
                    'user_id' => $user->id,
                    'tariff_id' => $tariffProject->id,
                );
                // Активация скидок
                if ($sale) {
                    $price->setSale($sale);
                    $this->discountService->activateDiscount($sale, $activeDiscountParams);
                }
                if ($coupon) {
                    $price->setSale($coupon);
                    $this->discountService->activateDiscount($coupon, $activeDiscountParams);
                }

                $totalPrice = $price->total();
                $amount = $totalPrice['current'];
                $currency = $totalPrice['currency'];

                $transactionData = array(
                    'user_id' => $user->id,
                    'amount' => $amount,
                    'currency' => $currency,
                    'type' => EnumPayment::TRANSACTION_TYPE_TARIFF,
                );

                $transaction = $this->paymentService->createTransaction($transactionData);
                $this->paymentService->setTransactionForProduct($tariffProject, $transaction);
                $transactionResource = new Item($transaction, new TransactionTransformer());
                $transactionData = $this->createData($transactionResource);

                if ($transaction->confirmed) {
                    $resource = array(
                        'paymentLink' => null,
                        'transaction' => $transactionData,
                    );
                    return $this->successResponse($resource);
                }
                $metadata = array(
                    'transaction_id' => $transaction->id,
                    'user_id' => $user->id,
                    'product_id' => $tariffProject->id,
                    'project_id' => $projectId,
                    'coupon_id' => $coupon?->id,
                    'sale_id' => $sale?->id,
                );
                $payment = $this->paymentService->createPayment($transaction, $metadata);
                $resource = array(
                    'paymentLink' => $payment->getConfirmation()->getConfirmationUrl(),
                    'transaction' => $transactionData,
                );
                return $this->successResponse($resource);
            });


        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }
    }

    public function callback()
    {
        try {
            $this->paymentService->paymentEventHandle(function (string $status, Metadata $metadata, Transaction $transaction) {

                // Обработка успешной оплаты тарифа
                if ($status === EnumPayment::STATUS_SUCCEEDED && $transaction->type === EnumPayment::TRANSACTION_TYPE_TARIFF) {
                    // Активация тарифа
                    $tariffProject = $this->tariffService->activateTariff($metadata->product_id, $metadata->user_id);
                    $data = array('tariff' => $tariffProject);
                    // Присоединение тарифа к проекту
                    $this->projectService->updateProject($metadata->project_id, $metadata->user_id, $data);
                }
                Log::info($status);
            });

        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }
    }
}
