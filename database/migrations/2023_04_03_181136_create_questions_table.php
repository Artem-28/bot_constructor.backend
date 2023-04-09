<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('group_id');
            $table->unsignedBigInteger('next_id')->nullable();
            $table->unsignedBigInteger('prev_id')->nullable();
            $table->string('type');
            $table->text('text');
            $table->timestamps();

            $table->foreign('group_id')
                ->references('id')
                ->on('group_questions')
                ->onDelete('cascade');

            $table->foreign('prev_id')
                ->references('id')
                ->on('questions')
                ->onDelete('cascade');

            $table->foreign('next_id')
                ->references('id')
                ->on('questions')
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
        Schema::dropIfExists('questions');
    }
}
