<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGroupQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('group_questions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('test_id');
            $table->unsignedBigInteger('prev_id')->nullable();
            $table->unsignedBigInteger('next_id')->nullable();
            $table->timestamps();

            $table->foreign('test_id')
                ->references('id')
                ->on('tests')
                ->onDelete('cascade');

            $table->foreign('prev_id')
                ->references('id')
                ->on('group_questions')
                ->onDelete('cascade');

            $table->foreign('next_id')
                ->references('id')
                ->on('group_questions')
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
        Schema::dropIfExists('group_questions');
    }
}
