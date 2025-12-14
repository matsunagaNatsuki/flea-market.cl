<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTradesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trades', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sell_id')->unique();
            $table->unsignedBigInteger('seller_profile_id');
            $table->unsignedBigInteger('buyer_profile_id');
            $table->enum('status', ['active', 'completed'])->default('active');
            $table->timestamps();

            $table->foreign('sell_id')->references('id')->on('sells')->onDelete('cascade');
            $table->foreign('seller_profile_id')->references('id')->on('profiles')->onDelete('cascade');
            $table->foreign('buyer_profile_id')->references('id')->on('profiles')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('trades');
    }
}
