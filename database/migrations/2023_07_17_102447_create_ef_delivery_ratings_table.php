<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEfDeliveryRatingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ef_delivery_ratings', function (Blueprint $table) {
            $table->id();
            $table->string('lr_id')->nullable();
            $table->string('rating')->nullable();
            $table->string('feedback')->nullable();
            $table->tinyinteger('status')->default(0)->comment('0=>not active 1=>active');
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
        Schema::dropIfExists('ef_delivery_ratings');
    }
}
