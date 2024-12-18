<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableRecievedPreviouses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('recieved_previouses', function (Blueprint $table) {
                 $table->integer('business_id')->unsigned();
                 $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');
                 $table->integer('transaction_id')->unsigned();
                 $table->foreign('transaction_id')->references('id')->on('transactions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('recieved_previouses', function (Blueprint $table) {
            $table->dropColumn(['business_id']);
            $table->dropColumn(['transaction_id']);
        });
    }
}
