<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBudgetsTable extends Migration 
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('budgets', function (Blueprint $table) {
            $table->increments("id");
            $table->integer('restriction_id')->unsigned();
            $table->foreign('restriction_id')->references('id')->on('restrictions')->onDelete('cascade');
            $table->string('movement_type');
            $table->string('credit_account');
            $table->string('debit_account');
            $table->double('credit');
            $table->double('debit');
            $table->double('status');
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
        Schema::dropIfExists('budgets');
    }
}
