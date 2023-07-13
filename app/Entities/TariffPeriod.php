<?php

namespace App\Entities;

use App\Enums\EnumTariff;

class TariffPeriod
{
    public int $days;

    /**
     * @throws \Exception
     */
    public function __construct(int $periodDays)
    {
        $this->validatePeriod($periodDays);
        $this->days = $periodDays;
    }

    /**
     * @throws \Exception
     */
    private function validatePeriod(int $period): void
    {
        $availablePeriod = array(
            EnumTariff::PERIOD_XS,
            EnumTariff::PERIOD_S,
            EnumTariff::PERIOD_L,
            EnumTariff::PERIOD_XXL
        );
        $valid = $period && in_array($period, $availablePeriod);
        if (!$valid) {
            throw new \Exception(__('errors.tariff.validation.period'));
        }
    }

    public function month(): int
    {
        return round( ceil($this->days / EnumTariff::PERIOD_S), 0);
    }
}
