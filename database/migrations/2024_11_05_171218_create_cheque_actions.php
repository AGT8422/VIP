<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChequeActions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cheque_actions', function (Blueprint $table) {
            $table->increments("id");
            $table->integer("check_id")->unsigned();
            $table->foreign("check_id")->on("checks")->references("id")->onDelete("cascade")->onUpdate("cascade");
            $table->string("type",255)->default('add')->nullable();
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
        Schema::dropIfExists('cheque_actions');
    }
}
