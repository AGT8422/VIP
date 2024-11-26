<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShopCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shop_categories', function (Blueprint $table) {
            $table->increments("id");
            $table->integer("category_id")->nullable();
            $table->integer("business_id")->nullable();
            $table->string("name")->nullable();
            $table->string("short_code")->nullable();
            $table->string("parent_id")->nullable();
            $table->integer("created_by")->nullable();
            $table->string("description")->nullable();
            $table->string("icon")->nullable();
            $table->tinyInteger("view")->nullable()->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shop_categories');
    }
}
