<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEfContactUsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ef_contact_us', function (Blueprint $table) {
            $table->id();
            $table->string('companyName');
            $table->string('companyWebsite')->nullable();
            $table->string('connectionPreference')->nullable();
            $table->boolean('consent')->default(false);
            $table->string('email');
            $table->string('fullName');
            $table->string('phone');
            $table->string('serviceType');
            $table->string('state');
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
        Schema::dropIfExists('ef_contact_us');
    }
}
