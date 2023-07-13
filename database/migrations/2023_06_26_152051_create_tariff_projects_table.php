<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use \App\Enums\EnumTariff;
use \App\Enums\EnumPrice;

class CreateTariffProjectsTable extends Migration
{
    private array $tariff_code_enum = array(
        EnumTariff::CODE_FREE,
        EnumTariff::CODE_BASE,
        EnumTariff::CODE_STANDARD,
        EnumTariff::CODE_PREMIUM,
        EnumTariff::CODE_SPECIAL,
    );
    private array $price_currency_enum = array(
        EnumPrice::CURRENCY_RUB
    );

    private array $tariff_period_enum = array(
        EnumTariff::PERIOD_XS,
        EnumTariff::PERIOD_S,
        EnumTariff::PERIOD_L,
        EnumTariff::PERIOD_XXL,
    );
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tariff_projects', function (Blueprint $table) {
            $table->id();
            $table->float('price');
            $table->enum('currency', $this->price_currency_enum);
            $table->enum('period', $this->tariff_period_enum);
            $table->dateTime('start_at')->nullable();
            $table->dateTime('end_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tariff_projects');
    }
}
