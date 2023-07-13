<?php

namespace App\Services;

use App\Entities\Price;
use App\Entities\TariffSalePeriod;
use App\Models\Tariff\Tariff;
use App\Models\Tariff\TariffParam;
use Exception;

class TariffService
{

    // Расчет стоимости одного параметра
    private function _calculateParamPrice(array $tariffParams, TariffParam $param): float
    {
        if (!$param->enable) {
            return 0.0;
        }

        if (!array_key_exists($param->type, $tariffParams)) {
            return 0.0;
        }

        $value = $tariffParams[$param->type];

        if ($value === 'infinity') {
            return $param->price_infinity;
        }

        if (is_float($value) || is_int($value)) {
            return $value * $param->price;
        }

        return 0.0;
    }

    /**
     * @throws Exception
     */
    // Расчет стоимости тарифа с учетом скидок купонов и периода
    public function calculateTariffPrice(array $tariffParams, Tariff $tariff): Price
    {
        // Валидация параметров тарифа
        $this->validateParams($tariffParams, $tariff, true);

        $totalSum = $tariff->base_price;
        foreach ($tariff->params as $param) {
            $totalSum += $this->_calculateParamPrice($tariffParams, $param);
        }
        return new Price($totalSum);
    }

    // Получение списка тарифов
    public function getTariffList(...$relations): \Illuminate\Database\Eloquent\Collection|array
    {
        $rootQuery = Tariff::query();
        if (array_key_exists('sales', $relations) && is_callable($relations['sales'])) {

            $rootQuery->with(['sales' => function($query) use ($relations) {
                $relations['sales']($query);
            }]);

            unset($relations['sales']);
        }

        return $rootQuery->with($relations)->where('public', true)->get();
    }

    /**
     * @throws Exception
     */
    public function getTariffById(int $tariffId, ...$relations): \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Builder|array|null
    {

        $rootQuery = Tariff::query();

        if (array_key_exists('sales', $relations) && is_callable($relations['sales'])) {

            $rootQuery->with(['sales' => function($query) use ($relations) {
                $relations['sales']($query);
            }]);

            unset($relations['sales']);
        }


        return $rootQuery->with($relations)
            ->where('public', true)
            ->find($tariffId);
    }

    public function getTariffByCode(string $code)
    {
        return Tariff::query()
            ->with('params')
            ->where(['public' => true, 'code' => $code])
            ->first();
    }

    // Валидация одного параметра тарифа
    private function _validateParam($valueParams, TariffParam $tariffParam): bool
    {
        $isNotValue = !array_key_exists($tariffParam->type, $valueParams);
        if (!$tariffParam->enable) {
            return $isNotValue;
        }

        if ($isNotValue) {
            return false;
        }

        $min = $tariffParam->min;
        $max = $tariffParam->max;
        $value = $valueParams[$tariffParam->type];

        if ($value === 'infinity' && $tariffParam->infinity) {
            return true;
        }

        if (!is_int($value)) {
            return false;
        }

        $validateMin = $min >= $value;
        $validateMax = $max <= $value || $tariffParam->infinity;

        return $validateMin && $validateMax;
    }

    /**
     * @throws Exception
     */
    // Валидация параметров тарифа
    public function validateParams($valueParams, Tariff $tariff, $throwException = false): array
    {
        $validateParams = array();

        foreach ($tariff->params as $param) {
            $isValid = $this->_validateParam($valueParams, $param);
            $validateParams[$param->type] = $isValid;

            if ($throwException && !$isValid) {
                $message = 'errors.tariff.validation.' . $param->type;
                throw new Exception(__($message));
            }

        }

        return $validateParams;
    }

    public function createTariffProject($data)
    {

    }
}
