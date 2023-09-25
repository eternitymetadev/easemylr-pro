<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexMisreport2ToConsignmentNotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('consignment_notes', function (Blueprint $table) {
            $table->index('status');
            $table->index('consignment_date');
            $table->index('regclient_id');
            $table->index('branch_id');
            $table->index('baseclient_id');
            $table->index('role_id');
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
            $table->dropIndex(['status']);
            $table->dropIndex(['consignment_date']);
            $table->dropIndex(['regclient_id']);
            $table->dropIndex(['branch_id']);
            $table->dropIndex(['baseclient_id']);
            $table->dropIndex(['role_id']);
        });
    }
}
