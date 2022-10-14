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
            $table->string('regclient_id')->nullable();
            $table->string('prs_type')->nullable()->comment('0=>one time 1=>recuring');
            $table->string('vehicle_id')->nullable();
            $table->string('driver_id')->nullable();
            $table->string('prs_date')->nullable();
            $table->string('status')->default(0)->comment('0=>assigned 1=>picked 2=>received');
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
