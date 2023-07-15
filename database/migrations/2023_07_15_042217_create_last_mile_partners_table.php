<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLastMilePartnersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('last_mile_partners', function (Blueprint $table) {
            $table->increments('id');
            $table->string('company_name')->nullable();
            $table->text('contact_name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->text('company_add')->nullable();
            $table->string('goods_type')->nullable();
            $table->string('state')->nullable();
            $table->string('volume')->nullable();
            $table->text('delivery_frequency')->nullable();
            $table->string('special_delivery')->nullable();
            $table->string('expected_timeline')->nullable();
            $table->string('delivery_type')->nullable();
            $table->string('reference')->nullable();
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
        Schema::dropIfExists('last_mile_partners');
    }
}
