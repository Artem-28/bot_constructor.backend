<?php

namespace App\Services;

use App\Models\Payment\Transaction;
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
    public function createPayment(float $amount, string $description, array $options = []): string
    {
        $client = $this->getClient();
        $payment = $client->createPayment(
            array(
                'amount' => array(
                    'value' => $amount,
                    'currency' => 'RUB',
                ),
                'confirmation' => array(
                    'type' => 'redirect',
                    'return_url' => route('payment.callback'), // Редирект после платежа
                ),
                'capture' => true, // отключить подтверждение платежа в yookassa
                'description' => $description,
                'metadata' => $options,
            ),
            uniqid('', true)
        );

        return $payment->getConfirmation()->getConfirmationUrl();
    }

    public function createTransaction(int $userId, float $amount, string $description): Transaction
    {
        $transaction = new Transaction([
            'amount' => $amount,
            'user_id' => $userId,
            'description' => $description,
        ]);
        $transaction->save();
        return $transaction;
    }

    public function getTransactionById(int $transactionId)
    {
        return Transaction::query()->find($transactionId);
    }

    public function updateTransaction(int $transactionId, array $data)
    {
        $transaction = Transaction::query()->find($transactionId);

        if (!$transaction) return null;

        if (array_key_exists('status', $data)) {
            $transaction->status = $data['status'];
        }

        $transaction->update();
        return $transaction;
    }
}
