<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWarehouseConveyorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('warehouse_conveyors', function (Blueprint $table) {
            $table->increments("id");
            $table->integer('store_src')->unsigned();
            $table->foreign('store_src')->references('id')->on('warehouses')->onDelete('cascade');
            $table->integer('store_des')->unsigned();
            $table->foreign('store_des')->references('id')->on('warehouses')->onDelete('cascade');
            $table->string('product_name');
            $table->double('total_qty');
            $table->double('transfer_qty');
            $table->integer('unit_id')->unsigned();
            $table->foreign('unit_id')->references('id')->on('units')->onDelete('cascade');
            $table->string('discription');
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
        Schema::dropIfExists('warehouse_conveyors');
    }
}
