<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use \App\Enums\EnumPayment;
use \App\Enums\EnumPrice;

class CreateTransactionsTable extends Migration
{

    private array $price_currency_enum = array(
       EnumPrice::CURRENCY_RUB
    );
    private array $payment_status_enum = array(
        EnumPayment::STATUS_SUCCEEDED,
        EnumPayment::STATUS_CREATED,
        EnumPayment::STATUS_PENDING,
        EnumPayment::STATUS_WAITING_FOR_CAPTURE,
        EnumPayment::STATUS_CANCELED
    );
    private array $payment_transaction_type_enum = array(
        EnumPayment::TRANSACTION_TYPE_TARIFF,
    );
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('payment_id')->nullable();
            $table->float('amount');
            $table->enum('currency', $this->price_currency_enum);
            $table->enum('status',$this->payment_status_enum)->default(EnumPayment::STATUS_CREATED);
            $table->enum('type', $this->payment_transaction_type_enum);
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
        Schema::dropIfExists('transactions');
    }
}
