<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentCardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_cards', function (Blueprint $table) {
            $table->increments("id");

            $table->integer("card_number")->nullable();
            $table->datetime("card_expire")->nullable();
            $table->string("card_type")->nullable();
            $table->string("last_four_number")->nullable();
            $table->tinyInteger("card_active")->nullable();
            $table->string("card_cvv")->nullable();
            $table->integer("client_id")->nullable();
            
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
        Schema::dropIfExists('payment_cards');
    }
}
