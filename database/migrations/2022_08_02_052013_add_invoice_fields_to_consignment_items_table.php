<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInvoiceFieldsToConsignmentItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('consignment_items', function (Blueprint $table) {
            $table->string('order_id')->after('payment_type')->nullable();
            $table->string('invoice_no')->after('order_id')->nullable();
            $table->string('invoice_date')->after('invoice_no')->nullable();
            $table->string('invoice_amount')->after('invoice_date')->nullable();
            $table->string('e_way_bill')->after('invoice_amount')->nullable();
            $table->string('e_way_bill_date')->after('e_way_bill')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('consignment_items', function (Blueprint $table) {
            //
        });
    }
}
