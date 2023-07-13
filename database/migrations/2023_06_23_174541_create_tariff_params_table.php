<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use \App\Enums\EnumTariff;

class CreateTariffParamsTable extends Migration
{
    private array $tariff_code_enum = array(
        EnumTariff::CODE_FREE,
        EnumTariff::CODE_BASE,
        EnumTariff::CODE_STANDARD,
        EnumTariff::CODE_PREMIUM,
        EnumTariff::CODE_SPECIAL,
    );
    private array $tariff_param_type_enum = array(
        EnumTariff::PARAMS_TYPE_ADMIN,
        EnumTariff::PARAMS_TYPE_RESPONDENT,
        EnumTariff::PARAMS_TYPE_STORAGE,
        EnumTariff::PARAMS_TYPE_SCRIPT,
        EnumTariff::PARAMS_TYPE_QUESTION,
    );
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tariff_params', function (Blueprint $table) {
            $table->id();
            $table->enum('tariff_code', $this->tariff_code_enum);
            $table->enum('type', $this->tariff_param_type_enum);
            $table->integer('min');
            $table->integer('max');
            $table->float('price');
            $table->float('price_infinity');
            $table->boolean('enable');
            $table->boolean('infinity');
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
        Schema::dropIfExists('tariff_params');
    }
}
