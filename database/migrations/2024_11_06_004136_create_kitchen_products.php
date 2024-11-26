<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKitchenProducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kitchen_products', function (Blueprint $table) {
            $table->increments("id");
            $table->integer("business_id")->unsigned();
            $table->foreign("business_id")->on("business")->references("id")->onDelete("cascade")->onUpdate("cascade");
            $table->integer("kitchen_id")->unsigned();
            $table->foreign("kitchen_id")->on("kitchens")->references("id")->onDelete("cascade")->onUpdate("cascade");
            $table->integer("product_id")->unsigned();
            $table->foreign("product_id")->on("products")->references("id")->onDelete("cascade")->onUpdate("cascade");
            $table->integer("status")->unsigned();
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
        Schema::dropIfExists('kitchen_products');
    }
}
