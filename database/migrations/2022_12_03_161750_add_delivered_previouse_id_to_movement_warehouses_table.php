<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDeliveredPreviouseIdToMovementWarehousesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('movement_warehouses', function (Blueprint $table) {
            $table->integer('delivered_previouse_id')->nullable();
            // $table->foreign('delivered_previouse_id')->references('id')->on('delivered_previouses')->nullable();
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
           
        });
    }
}
