<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFinfactFieldToPaymentHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payment_histories', function (Blueprint $table) {
            $table->string('finfect_status')->after('current_paid_amt')->nullable();
            $table->string('paid_amt')->after('finfect_status')->nullable();
            $table->string('bank_refrence_no')->after('paid_amt')->nullable();
            $table->string('payment_date')->after('bank_refrence_no')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payment_histories', function (Blueprint $table) {
            //
        });
    }
}
