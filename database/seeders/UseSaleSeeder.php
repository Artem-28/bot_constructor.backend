<?php

namespace Database\Seeders;

use App\Entities\DateTimeInterval;
use App\Enums\EnumDiscount;
use App\Enums\EnumPrice;
use App\Enums\EnumTariff;
use App\Models\Sale;

use Illuminate\Database\Seeder;

class UseSaleSeeder extends Seeder
{

    /**
     * @throws \Exception
     */
    private function getInterval(int $start = 0, int | null $period = null): array
    {
        $interval = new DateTimeInterval($start, $period);
        return array(
            'start_at' => $interval->getStartAt(),
            'end_at' => $interval->getEndAt()
        );
    }

    /**
     * Run the database seeds.
     *
     * @return void
     * @throws \Exception
     */
    public function run()
    {
        $data = array(
            [
                'tariff_code' => EnumTariff::CODE_BASE,
                'type' => EnumDiscount::TYPE_SALE,
                'unit' => EnumDiscount::UNIT_VALUE_PERCENT,
                'currency' => EnumPrice::CURRENCY_RUB,
                'title' => 'sale.' . EnumDiscount::TYPE_SALE,
                'period' => EnumTariff::PERIOD_S,
                'value' => 10,
                'priority' => 1,
                'once' => false,
                ...$this->getInterval(-15, 10)
            ],
            [
                'tariff_code' => EnumTariff::CODE_BASE,
                'type' => EnumDiscount::TYPE_SALE,
                'unit' => EnumDiscount::UNIT_VALUE_PERCENT,
                'currency' => EnumPrice::CURRENCY_RUB,
                'title' => 'sale.' . EnumDiscount::TYPE_SALE,
                'period' => EnumTariff::PERIOD_L,
                'value' => 15,
                'priority' => 1,
                'once' => false,
                ...$this->getInterval(15, 31)
            ],
            [
                'tariff_code' => EnumTariff::CODE_BASE,
                'type' => EnumDiscount::TYPE_SALE,
                'unit' => EnumDiscount::UNIT_VALUE_PERCENT,
                'currency' => EnumPrice::CURRENCY_RUB,
                'title' => 'sale.' . EnumDiscount::TYPE_SALE,
                'period' => EnumTariff::PERIOD_XXL,
                'value' => 20,
                'priority' => 1,
                'once' => false,
                ...$this->getInterval(0, 183)
            ],
            [
                'tariff_code' => EnumTariff::CODE_STANDARD,
                'type' => EnumDiscount::TYPE_SUBSCRIPTION_TRAIL,
                'unit' => EnumDiscount::UNIT_VALUE_PERCENT,
                'currency' => EnumPrice::CURRENCY_RUB,
                'title' => 'sale.' . EnumDiscount::TYPE_SUBSCRIPTION_TRAIL,
                'period' => EnumTariff::PERIOD_XS,
                'value' => 100,
                'priority' => 1,
                'once' => true,
                ...$this->getInterval(),
            ],
            [
                'tariff_code' => EnumTariff::CODE_STANDARD,
                'type' => EnumDiscount::TYPE_SALE,
                'unit' => EnumDiscount::UNIT_VALUE_PERCENT,
                'currency' => EnumPrice::CURRENCY_RUB,
                'title' => 'sale.' . EnumDiscount::TYPE_SALE,
                'period' => EnumTariff::PERIOD_S,
                'value' => 13,
                'priority' => 1,
                'once' => false,
                ...$this->getInterval(),
            ],
            [
                'tariff_code' => EnumTariff::CODE_STANDARD,
                'type' => EnumDiscount::TYPE_SALE,
                'unit' => EnumDiscount::UNIT_VALUE_PERCENT,
                'currency' => EnumPrice::CURRENCY_RUB,
                'title' => 'sale.' . EnumDiscount::TYPE_SALE,
                'period' => EnumTariff::PERIOD_L,
                'value' => 18,
                'priority' => 1,
                'once' => false,
                ...$this->getInterval(),
            ],
            [
                'tariff_code' => EnumTariff::CODE_STANDARD,
                'type' => EnumDiscount::TYPE_SALE,
                'unit' => EnumDiscount::UNIT_VALUE_PERCENT,
                'currency' => EnumPrice::CURRENCY_RUB,
                'title' => 'sale.' . EnumDiscount::TYPE_SALE,
                'period' => EnumTariff::PERIOD_XXL,
                'value' => 23,
                'priority' => 1,
                'once' => false,
                ...$this->getInterval(),
            ],
            [
                'tariff_code' => EnumTariff::CODE_PREMIUM,
                'type' => EnumDiscount::TYPE_SALE,
                'unit' => EnumDiscount::UNIT_VALUE_PERCENT,
                'currency' => EnumPrice::CURRENCY_RUB,
                'title' => 'sale.' . EnumDiscount::TYPE_SALE,
                'period' => EnumTariff::PERIOD_S,
                'value' => 23,
                'priority' => 1,
                'once' => false,
                ...$this->getInterval(),
            ],
            [
                'tariff_code' => EnumTariff::CODE_PREMIUM,
                'type' => EnumDiscount::TYPE_SALE,
                'unit' => EnumDiscount::UNIT_VALUE_PERCENT,
                'currency' => EnumPrice::CURRENCY_RUB,
                'title' => 'sale.' . EnumDiscount::TYPE_SALE,
                'period' => EnumTariff::PERIOD_L,
                'value' => 28,
                'priority' => 1,
                'once' => false,
                ...$this->getInterval(),
            ],
            [
                'tariff_code' => EnumTariff::CODE_PREMIUM,
                'type' => EnumDiscount::TYPE_SALE,
                'unit' => EnumDiscount::UNIT_VALUE_PERCENT,
                'currency' => EnumPrice::CURRENCY_RUB,
                'title' => 'sale.' . EnumDiscount::TYPE_SALE,
                'period' => EnumTariff::PERIOD_XXL,
                'value' => 33,
                'priority' => 1,
                'once' => false,
                ...$this->getInterval(),
            ],
            [
                'tariff_code' => EnumTariff::CODE_SPECIAL,
                'type' => EnumDiscount::TYPE_SALE,
                'unit' => EnumDiscount::UNIT_VALUE_PERCENT,
                'currency' => EnumPrice::CURRENCY_RUB,
                'title' => 'sale.' . EnumDiscount::TYPE_SALE,
                'period' => EnumTariff::PERIOD_S,
                'value' => 28,
                'priority' => 1,
                'once' => false,
                ...$this->getInterval(),
            ],
            [
                'tariff_code' => EnumTariff::CODE_SPECIAL,
                'type' => EnumDiscount::TYPE_SALE,
                'unit' => EnumDiscount::UNIT_VALUE_PERCENT,
                'currency' => EnumPrice::CURRENCY_RUB,
                'title' => 'sale.' . EnumDiscount::TYPE_SALE,
                'period' => EnumTariff::PERIOD_L,
                'value' => 33,
                'priority' => 1,
                'once' => false,
                ...$this->getInterval(),
            ],
            [
                'tariff_code' => EnumTariff::CODE_SPECIAL,
                'type' => EnumDiscount::TYPE_SALE,
                'unit' => EnumDiscount::UNIT_VALUE_PERCENT,
                'currency' => EnumPrice::CURRENCY_RUB,
                'title' => 'sale.' . EnumDiscount::TYPE_SALE,
                'period' => EnumTariff::PERIOD_XXL,
                'value' => 38,
                'priority' => 1,
                'once' => false,
                ...$this->getInterval(),
            ],
        );

        foreach ($data as $item) {
            $param = new Sale($item);
            $param->save();
        }
    }
}
