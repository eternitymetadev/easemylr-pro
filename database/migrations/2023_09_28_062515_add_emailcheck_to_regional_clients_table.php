<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEmailcheckToRegionalClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('regional_clients', function (Blueprint $table) {
            $table->string('is_misemail')->after('is_email_sent')->default(0)->comment('0=>no 1=>yes');  // send email cron mis report3 to and cc regclient email
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
