<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRecivedPreviousIdToMovementWarehousesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('movement_warehouses', function (Blueprint $table) {
            $table->integer('recived_previous_id')->unsigned()->nullable();
            $table->integer('recieved_wrong_id')->unsigned()->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('movement_warehouses', function (Blueprint $table) {
            //
        });
    }
}
