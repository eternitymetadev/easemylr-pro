<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePrsPaymentHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prs_payment_histories', function (Blueprint $table) {
            $table->id();
            $table->string('refrence_transaction_id')->nullable();
            $table->string('transaction_id')->nullable();
            $table->string('prs_no')->nullable();
            $table->string('bank_details')->nullable();
            $table->string('purchase_amount')->nullable();
            $table->string('payment_type')->nullable();
            $table->string('advance')->nullable();
            $table->string('balance')->nullable();
            $table->string('tds_deduct_balance')->nullable();
            $table->string('current_paid_amt')->nullable();
            $table->string('finfect_status')->nullable();
            $table->string('paid_amt')->nullable();
            $table->string('bank_refrence_no')->nullable();
            $table->string('payment_date')->nullable();
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
        Schema::dropIfExists('prs_payment_histories');
    }
}
