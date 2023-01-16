<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsVerifyToPrsReceiveVehiclesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('prs_receive_vehicles', function (Blueprint $table) {
            $table->string('is_verify')->after('remarks')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('prs_receive_vehicles', function (Blueprint $table) {
            //
        });
    }
}
