<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEfShipnowsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ef_shipnows', function (Blueprint $table) {
            $table->id();
            $table->string('pickUp')->nullable();
            $table->string('drop')->nullable();
            $table->string('phone')->nullable();
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
        Schema::dropIfExists('ef_shipnows');
    }
}
