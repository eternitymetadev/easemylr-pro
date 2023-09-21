<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConsignmentNotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('consignment_notes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('consigner_id')->nullable();
            $table->string('consignee_id')->nullable();
            $table->string('ship_to_id')->nullable();
            $table->string('consignment_no')->nullable();
            $table->string('consignment_date')->nullable();
            $table->string('dispatch')->nullable();
            $table->string('invoice_no')->nullable();
            $table->string('invoice_date')->nullable();
            $table->string('invoice_amount')->nullable();
            $table->string('vehicle_id')->nullable();
            $table->string('total_quantity')->nullable();
            $table->string('total_weight')->nullable();
            $table->string('total_gross_weight')->nullable();
            $table->string('total_freight')->nullable();
            $table->string('pdf_name')->nullable();
            $table->string('transporter_name')->nullable();
            $table->string('vehicle_type')->nullable();
            $table->string('purchase_price')->nullable();
            $table->string('user_id')->nullable();
            $table->string('branch_id')->nullable();
            $table->string('driver_id')->nullable();
            $table->string('bar_code')->nullable();
            $table->string('reason_to_cancel')->nullable();
            $table->string('cancel_userid')->nullable();
            $table->string('order_id')->nullable();
            $table->string('edd')->nullable();
            $table->string('status')->nullable();
            $table->string('delivery_status')->nullable();
            $table->string('delivery_date')->nullable();
            $table->string('signed_drs')->nullable();
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
        Schema::dropIfExists('consignment_notes');
    }
}
