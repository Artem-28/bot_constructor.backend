<?php

namespace Database\Seeders;

use App\Entities\DateTimeInterval;
use App\Enums\EnumDiscount;
use App\Enums\EnumPrice;
use App\Enums\EnumTariff;
use App\Models\Discount\Coupon;
use Illuminate\Database\Seeder;

class UseCouponSeeder extends Seeder
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
            // закончилась
            [
                'user_id' => null,
                'unit' => EnumDiscount::UNIT_VALUE_PERCENT,
                'currency' => EnumPrice::CURRENCY_RUB,
                'code' => 'SALE-30',
                'title' => 'coupon',
                'period' => EnumTariff::PERIOD_XXL,
                'value' => 30,
                'once' => true,
                ...$this->getInterval(-15, 5)
            ],
            // Идет одноразовая
            [
                'user_id' => null,
                'unit' => EnumDiscount::UNIT_VALUE_PERCENT,
                'currency' => EnumPrice::CURRENCY_RUB,
                'code' => 'SALE-50',
                'title' => 'coupon',
                'period' => EnumTariff::PERIOD_S,
                'value' => 50,
                'once' => true,
                ...$this->getInterval(-1, 30)
            ],
            // не началась
            [
                'user_id' => null,
                'unit' => EnumDiscount::UNIT_VALUE_PRICE,
                'currency' => EnumPrice::CURRENCY_RUB,
                'code' => 'SALE-700',
                'title' => 'coupon',
                'period' => EnumTariff::PERIOD_L,
                'value' => 700,
                'once' => false,
                ...$this->getInterval(12, 30)
            ],
            // идет бесконечная многоразовая
            [
                'user_id' => null,
                'unit' => EnumDiscount::UNIT_VALUE_PRICE,
                'currency' => EnumPrice::CURRENCY_RUB,
                'code' => 'SALE-1000',
                'title' => 'coupon',
                'period' => EnumTariff::PERIOD_XXL,
                'value' => 1000,
                'once' => true,
                ...$this->getInterval(-1)
            ],
            // идет бесконечная многоразовая
            [
                'user_id' => null,
                'unit' => EnumDiscount::UNIT_VALUE_PRICE,
                'currency' => EnumPrice::CURRENCY_RUB,
                'code' => 'SALE-900',
                'title' => 'coupon',
                'period' => EnumTariff::PERIOD_XXL,
                'value' => 900,
                'once' => false,
                ...$this->getInterval(-1)
            ],
        );

        foreach ($data as $item) {
            $param = new Coupon($item);
            $param->save();
        }
    }
}
