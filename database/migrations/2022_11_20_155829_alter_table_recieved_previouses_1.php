<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableRecievedPreviouses1 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('recieved_previouses', function (Blueprint $table) {
            $table->integer('transaction_deliveries_id')->unsigned();
            $table->foreign('transaction_deliveries_id')->references('id')->on('transaction_recieveds')->onDelete('cascade');
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
           $table->dropColumn(['transaction_id']);
        });
    }
}
