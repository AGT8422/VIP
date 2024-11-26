<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMovementWarehousesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('movement_warehouses', function (Blueprint $table) {
            $table->id();
            $table->integer('business_id')->unsigned();
            $table->integer('transaction_id')->unsigned();
            $table->string('product_name')->nullable();
            $table->integer('unit_id')->unsigned();
            $table->foreign('unit_id')->references('id')->on('units')->onDelete('cascade');
            $table->integer('store_id')->unsigned();
            $table->foreign('store_id')->references('id')->on('warehouses')->onDelete('cascade');
            $table->string('movement');
            $table->double('plus_qty');
            $table->double('minus_qty');
            $table->double('current_qty');
            $table->double('current_price');
            $table->integer('variation_id')->nullable();
            $table->integer('purchase_line_id')->nullable();
            $table->integer('transaction_sell_line_id')->nullable();
            $table->date('date')->nullable();
            $table->integer('for_move')->nullable();
            $table->integer('product_unit')->nullable();
            $table->integer('product_unit_qty')->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('movement_warehouses');
    }
}
