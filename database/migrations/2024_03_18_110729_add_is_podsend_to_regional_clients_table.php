<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsPodsendToRegionalClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('regional_clients', function (Blueprint $table) {
            $table->string('is_podemail')->after('is_misemail')->default(0)->comment('0:not sent, 1:sent');
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
