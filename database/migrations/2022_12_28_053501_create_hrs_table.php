<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHrsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hrs', function (Blueprint $table) {
            $table->id();
            $table->string('hrs_no')->nullable();
            $table->string('consignment_id')->nullable();
            $table->string('vehicle_id')->nullable();
            $table->string('driver_id')->nullable();
            $table->string('vehicle_type_id')->nullable();
            $table->string('transporter_name')->nullable();
            $table->string('purchase_price')->nullable();
            $table->string('branch_id')->nullable();
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
        Schema::dropIfExists('hrs');
    }
}
