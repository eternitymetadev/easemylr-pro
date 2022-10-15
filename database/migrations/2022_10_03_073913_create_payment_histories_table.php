<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_histories', function (Blueprint $table) {
            $table->id();
            $table->string('refrence_transaction_id')->nullable();
            $table->string('drs_no')->nullable();
            $table->string('bank_details')->nullable();
            $table->string('purchase_amount')->nullable();
            $table->string('payment_type')->nullable();
            $table->string('advance')->nullable();
            $table->string('balance')->nullable();
            $table->string('tds_deduct_balance')->nullable();
            $table->string('payment_status')->default(0)->comment('0=>unpaid 1=>paid 2=>sent 3=>partial paid')->nullable();
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
        Schema::dropIfExists('payment_histories');
    }
}
