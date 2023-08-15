<?php

namespace App\Services;

use App\Enums\EnumPayment;
use App\Models\Payment\Transaction;
use App\Models\Tariff\TariffProject;
use YooKassa\Client;
use YooKassa\Common\Exceptions\ApiException;
use YooKassa\Common\Exceptions\BadApiRequestException;
use YooKassa\Common\Exceptions\ExtensionNotFoundException;
use YooKassa\Common\Exceptions\ForbiddenException;
use YooKassa\Common\Exceptions\InternalServerError;
use YooKassa\Common\Exceptions\NotFoundException;
use YooKassa\Common\Exceptions\ResponseProcessingException;
use YooKassa\Common\Exceptions\TooManyRequestsException;
use YooKassa\Common\Exceptions\UnauthorizedException;
use YooKassa\Model\Notification\NotificationCanceled;
use YooKassa\Model\Notification\NotificationSucceeded;
use YooKassa\Model\Notification\NotificationWaitingForCapture;
use YooKassa\Model\NotificationEventType;

class PaymentService
{
    private function getClient() :Client
    {
        $client = new Client();
        $shopId = config('services.yookassa.shop_id');
        $secretKey = config('services.yookassa.secret_key');
        $client->setAuth($shopId, $secretKey);
        return $client;
    }

    /**
     * @throws NotFoundException
     * @throws ResponseProcessingException
     * @throws ApiException
     * @throws BadApiRequestException
     * @throws ExtensionNotFoundException
     * @throws InternalServerError
     * @throws ForbiddenException
     * @throws TooManyRequestsException
     * @throws UnauthorizedException
     */
    public function createPayment(Transaction $transaction, array $metadata = []): ?\YooKassa\Request\Payments\CreatePaymentResponse
    {
        $client = $this->getClient();

        $amount = array(
            'value' => $transaction->amount,
            'currency' => $transaction->currency,
        );
        $confirmation = array(
            'type' => 'redirect',
            'return_url' => route('payment.callback'), // Редирект после платежа
        );
        $paymentData = array(
            'amount' => $amount,
            'confirmation' => $confirmation,
            'capture' => false, // отключить подтверждение платежа в yookassa
            'description' => $transaction->type,
            'metadata' => $metadata,
        );
        $payment = $client->createPayment($paymentData, uniqid('', true));
        $transaction->payment_id = $payment->id;
        $transaction->update();

        return $payment;
    }

    public function createTransaction(array $data): Transaction
    {
        $status = EnumPayment::STATUS_CREATED;
        if ($data['amount'] === 0) {
            $status = EnumPayment::STATUS_SUCCEEDED;
        }
        $data['status'] = $status;
        $transaction = new Transaction($data);
        $transaction->save();
        return $transaction;
    }

    public function getTransactionById(int $transactionId)
    {
        return Transaction::query()->find($transactionId);
    }

    public function setTransactionForProduct(TariffProject $product, Transaction $transaction): TariffProject
    {
        if ($product->transaction_id) {
            throw new \Exception(__('errors.payment.set_transaction'));
        }
        $product->transaction()->associate($transaction);
        $product->save();
        return $product;
    }


    public function updateTransaction(int $transactionId, int $userId, array $data)
    {
        $transaction = Transaction::query()
            ->where('user_id', '=', $userId)
            ->find($transactionId);

        if (!$transaction) {
            throw new \Exception(__('errors.payment.update_transaction'));
        }

        if (array_key_exists('status', $data)) {
            $transaction->status = $data['status'];
        }

        $transaction->update();
        return $transaction;
    }

    public function createPaymentNotification(array $data): NotificationCanceled|NotificationWaitingForCapture|NotificationSucceeded|null
    {
        $event = null;

        if (array_key_exists('event', $data)) {
            $event = $data['event'];
        }
        switch ($event) {
            case NotificationEventType::PAYMENT_SUCCEEDED:
                return new NotificationSucceeded($data);
            case NotificationEventType::PAYMENT_CANCELED:
                return new NotificationCanceled($data);
            case NotificationEventType::PAYMENT_WAITING_FOR_CAPTURE:
                return new NotificationWaitingForCapture($data);
            default:
                return null;
        }
    }

    public function getPaymentStatus(NotificationCanceled|NotificationWaitingForCapture|NotificationSucceeded $notification): string
    {
        $payment = $notification->getObject();
        switch ($payment->status) {
            case NotificationEventType::PAYMENT_SUCCEEDED:
                return EnumPayment::STATUS_SUCCEEDED;
            case NotificationEventType::PAYMENT_CANCELED:
                return EnumPayment::STATUS_CANCELED;
            case NotificationEventType::PAYMENT_WAITING_FOR_CAPTURE:
                return EnumPayment::STATUS_WAITING_FOR_CAPTURE;
            default:
                return $payment->status;
        }
    }

    public function paymentEventHandle(callable | null $callback = null): void
    {
        $source = file_get_contents('php://input');
        $requestBody = json_decode($source, true);

        if (!$requestBody) {
            throw new \Exception(__('errors.payment.invalid_request'));
        }

        $notification = $this->createPaymentNotification($requestBody);

        if (!$notification) {
            throw new \Exception(__('errors.payment.invalid_notification'));
        }

        $status = $this->getPaymentStatus($notification);
        $payment = $notification->getObject();

        if (!$payment) {
            throw new \Exception(__('errors.payment.invalid_payment'));
        }
        $metadata = $payment->metadata;
        $transactionId = $metadata->transaction_id;
        $userId = $metadata->user_id;

        if (!$transactionId || !$userId) {
            throw new \Exception(__('errors.payment.invalid_metadata'));
        }

        $transactionData = array('status' => $status);
        $transaction = $this->updateTransaction($transactionId, $userId, $transactionData);

        if (is_callable($callback)) {
            $callback($status, $metadata, $transaction);
        }
    }
}
