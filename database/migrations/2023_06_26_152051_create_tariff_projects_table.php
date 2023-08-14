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
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tariff_projects', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('transaction_id')->nullable();
            $table->enum('tariff_code', $this->tariff_code_enum);
            $table->integer('period');
            $table->dateTime('start_at')->nullable();
            $table->dateTime('end_at')->nullable();
            $table->timestamps();

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

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
        Schema::dropIfExists('tariff_projects');
    }
}
