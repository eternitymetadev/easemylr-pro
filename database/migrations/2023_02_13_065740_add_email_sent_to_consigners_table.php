<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEmailSentToConsignersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('consigners', function (Blueprint $table) {
            $table->string('is_email_sent')->after('state_id')->default(0)->comment('0=>No  1=>Yes')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('consigners', function (Blueprint $table) {
            //
        });
    }
}
