<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVendorTypeToVendorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vendors', function (Blueprint $table) {
            $table->string('vendor_type')->after('other_details')->nullable();
            $table->string('declaration_available')->after('vendor_type')->nullable();
            $table->string('declaration_file')->after('declaration_available')->nullable();
            $table->string('tds_rate')->after('declaration_file')->nullable();
            $table->string('branch_id')->after('tds_rate')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vendors', function (Blueprint $table) {
            //
        });
    }
}
