<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use \App\Enums\EnumTariff;

class CreateTariffsTable extends Migration
{
    private array $tariff_code_enum = array(
        EnumTariff::CODE_FREE,
        EnumTariff::CODE_BASE,
        EnumTariff::CODE_STANDARD,
        EnumTariff::CODE_PREMIUM,
        EnumTariff::CODE_SPECIAL,
    );
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tariffs', function (Blueprint $table) {
            $table->id();
            $table->enum('code', $this->tariff_code_enum)->unique();
            $table->integer('base_price');
            $table->boolean('public');
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
        Schema::dropIfExists('tariffs');
    }
}
