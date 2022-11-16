<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePickupRunSheetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pickup_run_sheets', function (Blueprint $table) {
            $table->increments('id');
            $table->string('pickup_id')->nullable();
            $table->string('prs_type')->nullable()->comment('0=>one time 1=>recuring');
            $table->string('vehicletype_id')->nullable();
            $table->string('vehicle_id')->nullable();
            $table->string('driver_id')->nullable();
            $table->string('prs_date')->nullable();
            $table->string('user_id')->nullable();
            $table->string('branch_id')->nullable();
            $table->tinyInteger('status')->default(1)->comment('1=>assigned 2=>acknowledged 3=>started 4=>material picked up 5=>material received in hub');
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
        Schema::dropIfExists('pickup_run_sheets');
    }
}
