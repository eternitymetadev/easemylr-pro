<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRequestStatusToHrsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('hrs', function (Blueprint $table) {
            $table->string('payment_status')->after('receving_status')->default(0)->comment('0=>unpaid 1=>paid 2=>sent 3=>partial paid')->nullable();
            $table->string('request_status')->after('payment_status')->default(0)->comment('0=>no request 1=>payment request create')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('hrs', function (Blueprint $table) {
            //
        });
    }
}
