<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePrsRegClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prs_reg_clients', function (Blueprint $table) {
            $table->increments('id');
            $table->string('prs_id')->nullable();
            $table->string('regclient_id')->nullable();
            $table->string('consigner_id')->nullable();
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
        Schema::dropIfExists('prs_reg_clients');
    }
}
