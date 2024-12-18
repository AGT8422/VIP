<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStocktackingLines extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stocktacking_lines', function (Blueprint $table) {
            $table->increments("id"); 
            $table->integer("transaction_id")->unsigned();
            $table->foreign("transaction_id")->on("transactions")->references("id")->onDelete("cascade")->onUpdate("cascade");
            $table->integer("variation_id");
             $table->string("real_qty_available");
            $table->string("current_stock")->nullable();
            $table->integer("created_by");
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
        Schema::dropIfExists('stocktacking_lines');
    }
}
