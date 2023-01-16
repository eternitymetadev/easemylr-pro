<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddToBranchIdToConsignmentNotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('consignment_notes', function (Blueprint $table) {
            $table->string('prs_id')->after('booked_drs')->nullable();
            $table->string('to_branch_id')->after('branch_id')->nullable(); 
            $table->string('fall_in')->after('to_branch_id')->nullable(); 
            $table->string('prs_remarks')->after('prsitem_status')->nullable(); 
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
