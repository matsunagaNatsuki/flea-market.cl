<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTradeReviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trade_reviews', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('trade_id');
            $table->unsignedBigInteger('from_user_id'); //ユーザーを評価する
            $table->unsignedBigInteger('to_user_id'); //ユーザーから評価される
            $table->integer('score');
            $table->timestamps();

            $table->foreign('trade_id')->references('id')->on('trades')->onDelete('cascade');
            $table->foreign('from_user_id')->references('id')->on('profiles')->onDelete('cascade');
            $table->foreign('to_user_id')->references('id')->on('profiles')->onDelete('cascade');

            $table->unique(['trade_id', 'from_user_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('trade_reviews');
    }
}
