<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGstDetailsToBaseClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('base_clients', function (Blueprint $table) {
            $table->string('gst_no')->after('client_name')->nullable();
            $table->string('pan')->after('gst_no')->nullable();
            $table->string('tan')->after('pan')->nullable();
            $table->string('upload_gst')->after('tan')->nullable();
            $table->string('upload_pan')->after('upload_gst')->nullable();
            $table->string('upload_tan')->after('upload_pan')->nullable();
            $table->string('upload_moa')->after('upload_tan')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('base_clients', function (Blueprint $table) {
            //
        });
    }
}
