<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEfDriverPartnersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ef_driver_partners', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('driving_record')->nullable();
            $table->text('exp_details')->nullable();
            $table->string('is_available')->nullable();
            $table->string('is_compliant')->nullable();
            $table->string('is_flexible')->nullable();
            $table->string('preferred_state')->nullable();
            $table->string('reference')->nullable();
            $table->string('valid_license')->nullable();
            $table->string('working_years')->nullable();
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
        Schema::dropIfExists('ef_driver_partners');
    }
}
