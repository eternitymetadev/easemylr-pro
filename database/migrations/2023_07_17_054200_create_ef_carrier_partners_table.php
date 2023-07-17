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
            $table->string('areaOfDelivery')->nullable();
            $table->string('company')->nullable();
            $table->text('companyAddress')->nullable();
            $table->string('contactPerson')->nullable();
            $table->string('email')->nullable();
            $table->string('fleetSize')->nullable();
            $table->tinyinteger('isCompliant')->default(0)->comment('0=>no 1=>yes');
            $table->tinyinteger('leaseVehicle')->default(0)->comment('0=>no 1=>yes');
            $table->string('phone')->nullable();
            $table->string('reference')->nullable();
            $table->tinyinteger('specializedTransportation')->default(0)->comment('0=>no 1=>yes');
            $table->string('typeOfShipment')->nullable();
            $table->tinyinteger('valueAddedServices')->default(0)->comment('0=>no 1=>yes');
            $table->string('workingYears')->nullable();
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
