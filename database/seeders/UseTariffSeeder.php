<?php

namespace Database\Seeders;

use App\Enums\EnumTariff;
use App\Models\Tariff\Tariff;
use Illuminate\Database\Seeder;

class UseTariffSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = array(
            [
                'code' => EnumTariff::CODE_FREE,
                'base_price' => 0,
                'public' => true,
            ],
            [
                'code' => EnumTariff::CODE_BASE,
                'base_price' => 190,
                'public' => true,
            ],
            [
                'code' => EnumTariff::CODE_STANDARD,
                'base_price' => 390,
                'public' => true,
            ],
            [
                'code' => EnumTariff::CODE_PREMIUM,
                'base_price' => 590,
                'public' => true,
            ],
            [
                'code' => EnumTariff::CODE_SPECIAL,
                'base_price' => 1190,
                'public' => true,
            ]
        );

        foreach ($data as $item) {
            Tariff::updateOrCreate([
                'code' => $item['code'],
            ], $item);
        }
    }
}
