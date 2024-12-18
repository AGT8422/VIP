<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemMoves extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('item_moves', function (Blueprint $table) {
            $table->increments('id');
            
            $table->integer("business_id")->unsigned(); 
            $table->foreign("business_id")->on('business')->references("id")->onDelete("cascade")->onUpdate("cascade"); 
            $table->integer("product_id")->unsigned(); 
            $table->foreign("product_id")->on('products')->references("id")->onDelete("cascade")->onUpdate("cascade"); 
            $table->integer("variation_id")->unsigned(); 
            $table->foreign("variation_id")->on('variations')->references("id")->onDelete("cascade")->onUpdate("cascade"); 
            $table->integer("account_id")->nullable(); 
            $table->string("state")->nullable(); 
            $table->string("ref_no")->nullable();  
            $table->decimal("qty",22,4)->nullable(); 
            $table->text("signal")->nullable();  
            $table->double("row_price")->nullable(); 
            $table->double("row_price_inc_exp")->nullable(); 
            $table->double("unit_cost")->nullable(); 
            $table->string("current_qty")->nullable(); 
            $table->integer("transaction_id")->unsigned(); 
            $table->foreign("transaction_id")->on('transactions')->references("id")->onDelete("cascade")->onUpdate("cascade"); 
            $table->integer("line_id")->nullable(); 
            $table->integer("entry_option")->nullable(); 
            $table->integer("recieve_id")->nullable(); 
            $table->integer("purchase_line_id")->nullable(); 
            $table->integer("sells_line_id")->nullable(); 
            $table->double("out_price")->nullable();   
            $table->integer("is_returned")->nullable(); 
            $table->date("date")->nullable(); 
            $table->integer("store_id")->unsigned(); 
            $table->foreign("store_id")->on('warehouses')->references("id")->onDelete("cascade")->onUpdate("cascade"); 
            $table->integer("transaction_rd_id")->nullable(); 
            $table->integer("order_id")->nullable(); 
            $table->integer("product_unit")->nullable(); 
            $table->integer("product_unit_qty")->nullable(); 
            
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
        Schema::dropIfExists('item_moves');
    }
}
