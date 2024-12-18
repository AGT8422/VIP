<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableDelieveredPreviouses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('delivered_previouses', function (Blueprint $table) {
            $table->integer('transaction_recieveds_id')->unsigned();
            // $table->foreign('transaction_recieveds_id')->references('id')->on('transaction_deliveries')->onDelete('cascade');
        
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('delivered_previouses', function (Blueprint $table) {
            $table->dropColumn(['transaction_recieveds_id']);
        });
    }
}
