<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePrsTaskItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prs_task_items', function (Blueprint $table) {
            $table->increments('id');
            $table->string('drivertask_id')->nullable();
            $table->string('order_id')->nullable();
            $table->string('invoice_no')->nullable();
            $table->string('invoice_date')->nullable();
            $table->string('quantity')->nullable();
            $table->string('net_weight')->nullable();
            $table->string('gross_weight')->nullable();
            $table->string('user_id')->nullable();
            $table->string('branch_id')->nullable();
            $table->tinyInteger('status')->default(0);
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
        Schema::dropIfExists('prs_task_items');
    }
}
