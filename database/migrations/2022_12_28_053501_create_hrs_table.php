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
            $table->string('total_hrs_quantity')->nullable();
            $table->string('total_receive_quantity')->nullable();
            $table->string('remarks')->nullable();
            $table->string('branch_id')->nullable();
            $table->string('to_branch_id')->nullable();
            $table->string('status')->nullable();
            $table->string('receving_status')->default(1)->comment('1=>incoming 2=>received');
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
