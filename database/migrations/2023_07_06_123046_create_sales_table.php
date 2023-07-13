<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use \App\Enums\EnumTariff;
use \App\Enums\EnumDiscount;
use \App\Enums\EnumPrice;

class CreateSalesTable extends Migration
{
    private array $tariff_code_enum = array(
        EnumTariff::CODE_FREE,
        EnumTariff::CODE_BASE,
        EnumTariff::CODE_STANDARD,
        EnumTariff::CODE_PREMIUM,
        EnumTariff::CODE_SPECIAL,
    );

    private array $sale_type_enum = array(
        EnumDiscount::TYPE_SALE,
        EnumDiscount::TYPE_SUBSCRIPTION_TRAIL,
    );

    private array $sale_unit_value_enum = array(
        EnumDiscount::UNIT_VALUE_PERCENT,
        EnumDiscount::UNIT_VALUE_PRICE,
    );
    private array $price_currency_enum = array(
        EnumPrice::CURRENCY_RUB
    );
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->enum('tariff_code', $this->tariff_code_enum);
            $table->enum('type', $this->sale_type_enum);
            $table->enum('unit', $this->sale_unit_value_enum);
            $table->enum('currency', $this->price_currency_enum);
            $table->integer('period')->nullable();
            $table->integer('priority')->default(1);
            $table->string('title');
            $table->integer('value');
            $table->dateTime('start_at');
            $table->dateTime('end_at')->nullable();
            $table->boolean('once');
            $table->timestamps();

            $table->foreign('tariff_code')
                ->references('code')
                ->on('tariffs')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sales');
    }
}
