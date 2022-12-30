<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLrTypeToConsignmentNotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('consignment_notes', function (Blueprint $table) {
            $table->string('lr_type')->after('id')->comment('0=>FTL 1=>PTL')->nullable();
            $table->string('h2h_check')->after('lr_type')->nullable();
            $table->string('hrs_status')->after('h2h_check')->comment('1=>hrs created 2=>create hrs pending')->nullable();
            
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
