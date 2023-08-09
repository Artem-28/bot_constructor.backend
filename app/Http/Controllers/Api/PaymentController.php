<?php

namespace App\Http\Controllers\Api;

use App\Enums\EnumPayment;
use App\Http\Controllers\Controller;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use YooKassa\Model\Notification\NotificationSucceeded;
use YooKassa\Model\Notification\NotificationWaitingForCapture;
use YooKassa\Model\NotificationEventType;

class PaymentController extends Controller
{
    public PaymentService $paymentService;
    public function __construct
    (
        PaymentService $paymentService
    )
    {
        $this->middleware(['auth:sanctum'])->except(['callback']);
        $this->paymentService = $paymentService;
    }

    public function create(Request $request): \Illuminate\Http\JsonResponse
    {
        $amount = $request->get('amount');
        $description = 'payment test';

        try {
            $user = auth()->user();
            $transaction = $this->paymentService->createTransaction($user->id, $amount, $description);
            $options = array(
                'transactionId' => $transaction->id,
                'userId' => $transaction->user_id,
            );
            $link = $this->paymentService->createPayment($amount, $description, $options);
            $resource = array('link' => $link);
            return $this->successResponse($resource);
        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }



    }
    public function callback()
    {
        try {
            $source = file_get_contents('php://input');
            $requestBody = json_decode($source, true);

            $notification = ($requestBody['event'] === NotificationEventType::PAYMENT_SUCCEEDED)
                ? new NotificationSucceeded($requestBody)
                : new NotificationWaitingForCapture($requestBody);

            $payment = $notification->getObject();
            $metadata = $payment->metadata;
            $transactionId = $metadata->transactionId;
            Log::info(json_encode($payment));
            if (!$transactionId) return;
            if ($payment->status === 'succeeded') {
                $transactionData = array('status' => EnumPayment::STATUS_CONFIRMED);
                $this->paymentService->updateTransaction($transactionId, $transactionData);
            }
        } catch (\Exception $exception) {

        }

    }
}
