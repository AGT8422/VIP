<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExchangeRates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exchange_rates', function (Blueprint $table) {
            $table->increments("id");
            $table->integer("business_id")->unsigned();
            $table->foreign("business_id")->references("id")->on("business")->onDelete('cascade');
            $table->integer("currency_id")->unsigned();
            $table->foreign("currency_id")->references("id")->on("currencies")->onDelete('cascade');
            $table->decimal("amount",22,5)->nullable();
            $table->decimal("opposit_amount",22,5)->nullable();
            $table->date("date")->nullable();
            $table->boolean("source")->default(0)->nullable()->index();
            $table->boolean("right_amount")->default(0)->nullable()->index();
            $table->boolean("default")->default(0)->nullable()->index();
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
        Schema::dropIfExists('exchange_rates');
    }
}
