<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use \App\Enums\EnumTariff;

class CreateTariffProjectParamsTable extends Migration
{
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
        Schema::create('tariff_project_params', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tariff_id');
            $table->enum('type', $this->tariff_param_type_enum);
            $table->integer('value')->nullable();
            $table->boolean('enable');
            $table->boolean('infinity');
            $table->timestamps();

            $table->foreign('tariff_id')
                ->references('id')
                ->on('tariff_projects')
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
        Schema::dropIfExists('tariff_project_params');
    }
}
