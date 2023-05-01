<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGstDetailsToRegionalClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() 
    {
        Schema::table('regional_clients', function (Blueprint $table) {
            $table->string('regional_client_nick_name')->after('name')->nullable();
            $table->string('gst_no')->after('phone')->nullable();
            $table->string('pan')->after('gst_no')->nullable();
            $table->string('upload_gst')->after('pan')->nullable();
            $table->string('upload_pan')->after('upload_gst')->nullable();
            $table->string('payment_term')->after('upload_pan')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('regional_clients', function (Blueprint $table) {
            //
        });
    }
}
