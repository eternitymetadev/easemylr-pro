<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVendorAvailabilitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vendor_availabilities', function (Blueprint $table) {
            $table->id();
            $table->string('vendor_id')->nullable();
            $table->string('pickup_state')->nullable();
            $table->longtext('pickup_district')->nullable();
            $table->string('drop_state')->nullable();
            $table->longtext('drop_district')->nullable();
            $table->tinyinteger('status')->default(1)->comment('0=>not active 1=>active');
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
        Schema::dropIfExists('vendor_availabilities');
    }
}
