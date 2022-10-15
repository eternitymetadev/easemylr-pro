<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRequestStatusToTransactionSheetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transaction_sheets', function (Blueprint $table) {
            $table->string('request_status')->after('payment_status')->default(0)->comment('0=>no request 1=>payment request create')->nullable();
             $table->dropColumn(['payment_type']);
             $table->dropColumn(['advanced']);
             $table->dropColumn(['balance']);
             $table->dropColumn(['tds_deduct_amt']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transaction_sheets', function (Blueprint $table) {
            //
        });
    }
}
