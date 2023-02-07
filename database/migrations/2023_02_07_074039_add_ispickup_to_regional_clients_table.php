<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIspickupToRegionalClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('regional_clients', function (Blueprint $table) {
            $table->string('is_prs_pickup')->after('is_invoice_item_check')->default(0)->comment('0=>not required  1=>required')->nullable();
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
