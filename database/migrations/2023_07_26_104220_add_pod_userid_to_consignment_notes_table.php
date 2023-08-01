<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPodUseridToConsignmentNotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('consignment_notes', function (Blueprint $table) {
            $table->string('pod_userid')->after('signed_drs')->nullable();
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
