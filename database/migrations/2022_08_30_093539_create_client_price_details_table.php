<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientPriceDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('client_price_details', function (Blueprint $table) {
            $table->increments('id');
            $table->string('regclientdetail_id')->nullable();
            $table->string('from_state')->nullable();
            $table->string('to_state')->nullable();
            $table->string('price_per_kg')->nullable();
            $table->string('open_delivery_price')->nullable();
            $table->string('status')->nullable();
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
        Schema::dropIfExists('client_price_details');
    }
}
