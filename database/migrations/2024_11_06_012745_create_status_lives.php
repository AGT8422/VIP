<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStatusLives extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('status_lives', function (Blueprint $table) {
            $table->increments("id"); 
            $table->integer("business_id")->unsigned();
            $table->foreign("business_id")->on("business")->references("id")->onDelete("cascade")->onUpdate("cascade");
            $table->integer("transaction_id")->unsigned();
            $table->foreign("transaction_id")->on("transactions")->references("id")->onDelete("cascade")->onUpdate("cascade");
            $table->string("reference_no")->nullable();
            $table->string("state")->nullable();
            $table->integer("price")->nullable();
            $table->integer("check_id")->nullable();
            $table->integer("voucher_id")->nullable();
            $table->integer("return_id")->nullable();
            $table->integer("journal_voucher_id")->nullable();
            $table->integer("shipping_id")->nullable();
            $table->integer("shipping_item_id")->nullable();
            $table->integer("expense_voucher_id")->nullable();
            $table->integer("payment_id")->nullable();
            $table->integer("t_received")->nullable();
            $table->integer("t_delivery")->nullable();
            $table->integer("num_serial")->nullable();
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
        Schema::dropIfExists('status_lives');
    }
}
