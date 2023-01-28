<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLocationidToPickupRunSheetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pickup_run_sheets', function (Blueprint $table) {
            $table->string('hub_location_id')->after('location_id')->nullable();
            $table->string('purchase_amount')->after('branch_id')->nullable();
            $table->string('request_status')->after('purchase_amount')->default(0)->comment('0=>no request created 1=>request created');
            $table->string('payment_status')->after('request_status')->default(0)->comment('0=>unpaid 1=>paid 2=>sent 3=>partial paid');
            // $table->dropColumn(['regclient_id','consigner_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pickup_run_sheets', function (Blueprint $table) {
           
        });
    }
}
