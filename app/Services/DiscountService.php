<?php

namespace App\Services;

use App\Models\Discount\ActivatedDiscount;
use App\Models\Discount\Coupon;
use App\Models\Discount\Sale;

class DiscountService
{

    public static function buildQueryAvailableSales($rootQuery, int $userId)
    {
        $currentDate = date('Y-m-d H:i:s');
        // Получение активированной скидки по пользователю
        return $rootQuery->with(['activated' => function($query) use ($userId) {
            $query->where('user_id', $userId);
        }])

            // Ограничение по дате начала
            ->where('start_at', '<', $currentDate)

            // Ограничение по дате окончания
            ->where(function ($query) use ($currentDate) {
                $query->where('end_at', '>', $currentDate)
                    ->orWhereNull('end_at');
            })

            // Ограничение по использованной скидки
            ->where(function ($query) {
                $query->where('once', false)
                    ->orWhereDoesntHave('activated');
            });
    }

    public static function buildQueryAvailableCoupons($rootQuery, int $userId)
    {
        $currentDate = date('Y-m-d H:i:s');

        return $rootQuery->with(['activated' => function($query) use ($userId) {
            $query->where('user_id', $userId);
        }])
            ->where('start_at', '<', $currentDate)

            ->where(function ($query) use ($currentDate) {
                $query->where('end_at', '>', $currentDate)
                    ->orWhereNull('end_at');
            })
            ->where(function ($query) use ($userId) {
                $query->where('user_id', $userId)
                    ->orWhereNull('user_id');
            })
            ->where(function ($query) use ($userId) {
                $query->where('once', false)
                    ->orWhereDoesntHave('activated');
            });
    }

    // Получение валидных купонов
    public function getAvailableCoupons(int $userId): \Illuminate\Database\Eloquent\Collection|array
    {
        return $this::buildQueryAvailableCoupons(Coupon::query(), $userId)
            ->get();
    }

    // Получение валидной скидки по id
    public function getAvailableSaleById(int $saleId, int $userId): Sale|null
    {
        return $this::buildQueryAvailableSales(Sale::query(), $userId)
            ->find($saleId);
    }

    // Получение валидного купона по code
    public function getAvailableCouponByCode(string $couponCode, int $userId )
    {
        return $this::buildQueryAvailableCoupons(Coupon::query(), $userId)
            ->where('code', $couponCode)
            ->first();
    }

    /**
     * @throws \Exception
     */
    public function validateDiscount(array $params, $throwException = false): array
    {
        $userId = null;
        $tariffCode = null;
        $period = null;
        $sales = array();

        $validate = array('unique' => true);

        if (array_key_exists('sales', $params)) {
            $sales = $params['sales'];
        }

        if (array_key_exists('userId', $params)) {
            $userId = $params['userId'];
            $validate['user'] = true;
        }

        if (array_key_exists('tariffCode', $params)) {
            $tariffCode = $params['tariffCode'];
            $validate['tariff'] = true;
        }

        if (array_key_exists('period', $params)) {
            $period = $params['period'];
            $validate['period'] = true;
        }

        $uniqueSaleIds = [];


        foreach ($sales as $sale) {
            if (!$sale) continue;

            // Проверяем на уникальность
            $unique = !in_array($sale->id, $uniqueSaleIds) && $validate['unique'];
            $validate['unique'] = $unique;
            if ($unique) {
                $uniqueSaleIds[] = $sale->id;
            }

            // Валидация на пренадлежность пользователю$param
            if ($userId && $sale->user_id) {
                $validate['user'] = $sale->user_id === $userId && $validate['user'];
            }

            // Валидация на пренадлежность к тарифу
            if ($tariffCode && $sale->tariff_code) {
                $validate['tariff'] = $sale->tariff_code === $tariffCode && $validate['tariff'];
            }

            // Валидация на совпадения периода
            if ($period && $sale->period) {
                $validate['period'] = $sale->period === $period && $validate['period'];
            }
        }

        if ($throwException) {
            foreach ($validate as $valid) {
                if (!$valid) throw new \Exception(__('errors.sale.validation.base'));
            }
        }

        return $validate;
    }

    public function activateDiscount(Sale | Coupon $discount, array $params): \Illuminate\Database\Eloquent\Model|bool
    {
        $activatedDiscount = new ActivatedDiscount($params);
        return $discount->activated()->save($activatedDiscount);
    }

    public function deactivationTariffDiscount(int $tariffId, int $userId)
    {
        $rootQuery = ActivatedDiscount::query()
            ->where('user_id', $userId)
            ->where('tariff_id', $tariffId)
            ->delete();
        return $rootQuery;
    }
}
