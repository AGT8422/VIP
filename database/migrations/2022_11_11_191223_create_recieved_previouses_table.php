<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRecievedPreviousesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recieved_previouses', function (Blueprint $table) {
            $table->increments("id");
            $table->string('product_name');
            $table->integer('unit_id')->unsigned();
            $table->foreign('unit_id')->references('id')->on('units')->onDelete('cascade');
            $table->double('total_qty');
            $table->double('current_qty');
            $table->double('remain_qty');
            $table->integer('store_id')->unsigned();
            $table->foreign('store_id')->references('id')->on('warehouses')->onDelete('cascade');
            $table->integer('line_id')->nullable();
            $table->tinyInteger('is_returned')->default(0);
            $table->string('note');
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
        Schema::dropIfExists('recieved_previouses');
    }
} 
