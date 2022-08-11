<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFreightToConsignmentNotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('consignment_notes', function (Blueprint $table) {
            $table->string('freight')->after('purchase_price')->nullable();
            // $table->dropColumn(['total_freight']);
            // $table->dropColumn(['order_id']);
            // $table->dropColumn(['invoice_no']);
            // $table->dropColumn(['invoice_date']);
            // $table->dropColumn(['invoice_amount']);
            // $table->dropColumn(['e_way_bill']);
            // $table->dropColumn(['e_way_bill_date']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('consignment_notes', function (Blueprint $table) {
            //
        });
    }
}
