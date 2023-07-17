<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEfCarrierPartnersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ef_carrier_partners', function (Blueprint $table) {
            $table->id();
            $table->string('areaOfDelivery');
            $table->string('company');
            $table->text('companyAddress');
            $table->string('contactPerson');
            $table->string('email');
            $table->string('fleetSize');
            $table->tinyinteger('isCompliant')->default(0)->comment('0=>no 1=>yes');
            $table->tinyinteger('leaseVehicle')->default(0)->comment('0=>no 1=>yes');
            $table->string('phone');
            $table->string('reference');
            $table->tinyinteger('specializedTransportation')->default(0)->comment('0=>no 1=>yes');
            $table->string('typeOfShipment');
            $table->tinyinteger('valueAddedServices')->default(0)->comment('0=>no 1=>yes');
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
        Schema::dropIfExists('ef_carrier_partners');
    }
}
