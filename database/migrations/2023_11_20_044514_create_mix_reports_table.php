<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMixReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mix_reports', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_date')->nullable();
            $table->string('transaction_id')->nullable();
            $table->string('drs_no')->nullable();
            $table->string('no_of_drs')->nullable();
            $table->string('no_of_lrs')->nullable();
            $table->string('box_count')->nullable();
            $table->string('gross_wt')->nullable();
            $table->string('net_wt')->nullable();
            $table->text('consignee_distt')->nullable();
            $table->text('vehicle_type')->nullable();
            $table->string('branch_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mix_reports');
    }
}
