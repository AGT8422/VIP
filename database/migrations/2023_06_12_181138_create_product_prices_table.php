<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductPricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_prices', function (Blueprint $table) {
            $table->increments("id");
            $table->integer("business_id")->nullable();
            $table->integer("product_id")->nullable();
            $table->integer("variations_value_id")->nullable();
            $table->integer("variations_template_id")->nullable();
            $table->integer("ks_line")->nullable();
            $table->string("name",191)->nullable()->index();
            $table->decimal("price",22,4)->nullable();
            $table->text("list_of_price")->nullable();
            $table->integer("default_name")->nullable();
            $table->integer("number_of_default")->nullable();
            $table->date("date");
            $table->decimal("default_purchase_price",22,4)->nullable();
            $table->decimal("dpp_inc_tax",22,4)->nullable();
            $table->decimal("profit_percent",22,4)->nullable();
            $table->decimal("default_sell_price",22,4)->nullable();
            $table->decimal("sell_price_inc_tax",22,4)->nullable();
            $table->integer("unit_id")->nullable();
            $table->integer("parent_id")->nullable();
              
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
        Schema::dropIfExists('product_prices');
    }
}
