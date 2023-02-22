<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCodFieldToConsignmentNotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('consignment_notes', function (Blueprint $table) {
            $table->string('freight_on_delivery')->after('freight')->nullable();
            $table->string('cod')->after('freight_on_delivery')->nullable();
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
