<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_requests', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_id')->nullable();
            $table->string('drs_no')->nullable();
            $table->string('vendor_id')->nullable();
            $table->string('vehicle_no')->nullable();
            $table->string('total_amount')->nullable();
            $table->string('payment_type')->nullable();
            $table->string('advanced')->nullable();
            $table->string('balance')->nullable();
            $table->string('tds_deduct_balance')->nullable();
            $table->string('payment_status')->default(0)->comment('0=>unpaid 1=>paid 2=>sent 3=>partial paid')->nullable();
            $table->string('status')->nullable();
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
        Schema::dropIfExists('payment_requests');
    }
}
