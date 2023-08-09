<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use \App\Enums\EnumPayment;

class CreateTransactionsTable extends Migration
{

    private array $payment_status_enum = array(
        EnumPayment::STATUS_CONFIRMED,
        EnumPayment::STATUS_CREATED,
        EnumPayment::STATUS_FAILED
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
            $table->float('amount');
            $table->enum('status',$this->payment_status_enum)->default(EnumPayment::STATUS_CREATED);
            $table->string('description');
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
