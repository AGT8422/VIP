<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDailyPayments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('daily_payments', function (Blueprint $table) {
            $table->increments("id");
            $table->string("ref_no");  
            $table->decimal('amount',22,4)->nullable();  
            $table->date('date')->nullable();  
            $table->integer("business_id")->unsigned()->nullable();  
            $table->integer("currency_id")->unsigned()->nullable();  
            $table->text('document')->nullable();  
            $table->decimal("amount_in_currency",22,4)->nullable();  
            $table->decimal("exchange_price",22,4)->nullable();  
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
        Schema::dropIfExists('daily_payments');
    }
}
