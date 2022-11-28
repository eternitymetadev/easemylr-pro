<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePrsReceiveVehiclesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prs_receive_vehicles', function (Blueprint $table) {
            $table->increments('id');
            $table->string('prs_id')->nullable();
            $table->string('consigner_id')->nullable();
            $table->string('invoice_no')->nullable();
            $table->string('total_qty')->nullable();
            $table->string('receive_qty')->nullable();
            $table->string('remaining_qty')->nullable();
            $table->string('remarks')->nullable();
            $table->string('user_id')->nullable();
            $table->string('branch_id')->nullable();
            $table->tinyInteger('status')->default(0);
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
        Schema::dropIfExists('prs_receive_vehicles');
    }
}
