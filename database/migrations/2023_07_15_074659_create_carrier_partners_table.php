<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCarrierPartnersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('carrier_partners', function (Blueprint $table) {
            $table->id();
            $table->string('areaOfDelivery');
            $table->string('company');
            $table->string('companyAddress');
            $table->string('contactPerson');
            $table->string('email');
            $table->string('fleetSize');
            $table->string('isCompliant');
            $table->string('leaseVehicle');
            $table->string('phone');
            $table->string('reference');
            $table->string('specializedTransportation');
            $table->string('typeOfShipment');
            $table->string('valueAddedServices');
            $table->string('workingYears');
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
        Schema::dropIfExists('carrier_partners');
    }
}
