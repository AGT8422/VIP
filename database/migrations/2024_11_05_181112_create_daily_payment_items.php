<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDailyPaymentItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('daily_payment_items', function (Blueprint $table) {
            $table->increments("id");
            $table->bigInteger("daily_payment_id")->unsigned();  
            $table->double('credit');  
            $table->double('debit');  
            $table->bigInteger("account_id")->unsigned();  
            $table->text('text')->nullable();  
            $table->integer("cost_center_id")->nullable();  
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
        Schema::dropIfExists('daily_payment_items');
    }
}
