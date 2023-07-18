<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEfLastMilePartnersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ef_last_mile_partners', function (Blueprint $table) {
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
            $table->tinyinteger('special_delivery')->default(0)->comment('0=>no 1=>yes');
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
        Schema::dropIfExists('ef_last_mile_partners');
    }
}
