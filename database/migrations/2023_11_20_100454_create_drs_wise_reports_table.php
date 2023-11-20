<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDrsWiseReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('drs_wise_reports', function (Blueprint $table) {
            $table->id();
            $table->string('drs_no')->nullable();
            $table->string('date')->nullable();
            $table->string('drs_no')->nullable();
            $table->string('vehicle_type')->nullable();
            $table->string('purchase_amount')->nullable();
            $table->string('transaction_id')->nullable();
            $table->string('transaction_id_amt')->nullable();
            $table->string('paid_amount')->nullable();
            $table->string('client')->nullable();
            $table->string('location')->nullable();
            $table->text('lr_no')->nullable();
            $table->string('no_of_cases')->nullable();
            $table->string('net_wt')->nullable();
            $table->string('gross_wt')->nullable();
            $table->string('status')->nullable();
            $table->string('branch_id')->nullable();
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
        Schema::dropIfExists('drs_wise_reports');
    }
}
