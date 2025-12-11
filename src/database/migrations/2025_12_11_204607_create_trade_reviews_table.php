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
            $table->unsignedBigInteger('sell_id');
            $table->foreign('sell_id')->references('id')->on('sells')->onDelete('cascade');
            $table->unsignedBigInteger('from_user_id');
            $table->foreign('from_user_id')->references('id')->on('profiles')->onDelete('cascade');
            $table->unsignedBigInteger('to_user_id');
            $table->foreign('to_user_id')->references('id')->on('profiles')->onDelete('cascade');
            $table->integer('score')->nullable();
            $table->string('comments', 255)->nullable();
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
        Schema::dropIfExists('trade_reviews');
    }
}
