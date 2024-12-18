<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdditionalShippingItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('additional_shipping_items', function (Blueprint $table) {
            $table->increments("id"); 
            $table->bigInteger("additional_shipping_id")->unsigned();
            $table->bigInteger("contact_id")->unsigned();
            $table->bigInteger("account_id")->unsigned();
            $table->decimal("amount",8,2);
            $table->decimal("vat",8,2);
            $table->decimal("total",8,2);
            $table->text("text")->nullable();
            $table->date("date")->nullable();
            $table->integer("currency_id")->nullable();
            $table->decimal("exchange_rate",22,12)->nullable();
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
        Schema::dropIfExists('additional_shipping_items');
    }
}
