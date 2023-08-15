<?php

namespace App\Services;

use App\Entities\DateTimeInterval;
use App\Entities\Price;
use App\Enums\EnumPayment;
use App\Enums\EnumTariff;
use App\Models\Tariff\Tariff;
use App\Models\Tariff\TariffParam;
use App\Models\Tariff\TariffProject;
use App\Models\Tariff\TariffProjectParam;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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

        return $rootQuery->with($relations)
            ->where('public', true)
            ->get();
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

    public function getTariffProjectById(int $tariffId, int $userId, ...$relations)
    {
        $with = array();
        $rootQuery = TariffProject::query();

        if (in_array('params', $relations)) {
            $with[] = 'params';
        }
        if (in_array('transaction', $relations)) {
            $with[] = 'transaction';
        }
        if (in_array('project', $relations)) {
            $with[] = 'project';
        }
        if (in_array('tariff', $relations)) {
            $with[] = 'tariff';
        }
        if (array_key_exists('tariff', $relations) && is_array($relations['tariff'])) {
            $tariffRelations = $relations['tariff'];
            $with['tariff'] = function ($query) use ($tariffRelations) {
                $query->with($tariffRelations);
            };
        }

        return $rootQuery->with($with)
            ->where('user_id', '=', $userId)
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

    public function createTariffProject(array $data, array $tariffParamsData)
    {
        return DB::transaction(function () use ($data, $tariffParamsData) {
            $tariff = new TariffProject($data);
            $tariff->save();
            $availableParamTypes = array(
                EnumTariff::PARAMS_TYPE_ADMIN,
                EnumTariff::PARAMS_TYPE_QUESTION,
                EnumTariff::PARAMS_TYPE_RESPONDENT,
                EnumTariff::PARAMS_TYPE_SCRIPT,
                EnumTariff::PARAMS_TYPE_STORAGE
            );

            $params = array();

            foreach ($availableParamTypes as $paramType) {
                $enable = array_key_exists($paramType, $tariffParamsData);
                $infinity = $enable && $tariffParamsData[$paramType] === 'infinity';
                $value = 0;
                if ($enable && !$infinity) {
                    $value = $tariffParamsData[$paramType];
                }
                $param = new TariffProjectParam([
                    'type' => $paramType,
                    'enable' => $enable,
                    'infinity' => $infinity,
                    'value' => $value,
                ]);
                $params[] = $param;
            }

            $tariff->params()->saveMany($params);
            return $tariff;
        });
    }

    /**
     * @throws Exception
     */
    public function activateTariff(int $tariffId, int $userId)
    {
        $tariff = $this->getTariffProjectById($tariffId, $userId, 'transaction');
        if (!$tariff) {
            throw new Exception(__('errors.tariff.activate'));
        }
        $available = $tariff->transaction->status === EnumPayment::STATUS_SUCCEEDED && !$tariff->start_at && !$tariff->end_at;
        if (!$available) {
            throw new Exception(__('errors.tariff.activate'));
        }
        $period = new DateTimeInterval(0, $tariff->period);
        $tariff->start_at = $period->getStartAt();
        $tariff->end_at = $period->getEndAt();
        $tariff->update();
        return $tariff;
    }
}
