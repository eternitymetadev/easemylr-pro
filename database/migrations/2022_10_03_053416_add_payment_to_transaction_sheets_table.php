<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPaymentToTransactionSheetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transaction_sheets', function (Blueprint $table) {
            $table->string('payment_type')->after('status')->nullable();
            $table->string('advanced')->after('payment_type')->nullable();
            $table->string('balance')->after('advanced')->nullable();
            $table->string('payment_status')->after('balance')->default(0)->comment('0=>unpaid 1=>paid 2=>sent 3=>partial paid')->nullable();
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
