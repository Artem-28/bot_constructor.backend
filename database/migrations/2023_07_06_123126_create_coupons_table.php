<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use \App\Enums\EnumDiscount;
use \App\Enums\EnumPrice;
use \App\Enums\EnumTariff;

class CreateCouponsTable extends Migration
{

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
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->enum('unit', $this->sale_unit_value_enum);
            $table->enum('currency', $this->price_currency_enum);
            $table->integer('period')->nullable();
            $table->string('code');
            $table->string('title');
            $table->integer('value');
            $table->dateTime('start_at');
            $table->dateTime('end_at')->nullable();
            $table->boolean('once');
            $table->timestamps();

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
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
        Schema::dropIfExists('coupons');
    }
}
