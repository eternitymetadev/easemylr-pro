<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexesToConsignmentNotes extends Migration
{
    public function up()
    {
        Schema::table('consignment_notes', function (Blueprint $table) {
            $table->index('status');
            $table->index('consignment_no');
            $table->index('invoice_no');
            $table->index('user_id');
            $table->index('order_id');

            // Add more index definitions as needed.
        });
    }

    public function down()
    {
        Schema::table('consignment_notes', function (Blueprint $table) {
            $table->dropIndex('consignment_notes_status_index');
            $table->dropIndex('consignment_notes_consignment_no_index');
            $table->dropIndex('consignment_notes_invoice_no_index');
            $table->dropIndex('consignment_notes_user_id_index');
            $table->dropIndex('consignment_notes_order_id_index');

            // Drop additional indexes if necessary.
        });
    }
}
