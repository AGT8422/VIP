<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEcommercesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ecommerces', function (Blueprint $table) {
            $table->increments("id");
            $table->text("name")->nullable();
            $table->text("title")->nullable();
            $table->text("desc")->nullable();
            $table->text("button")->nullable();
            $table->text("image")->nullable();
            $table->tinyInteger("view")->default(0)->nullable();
            $table->tinyInteger("about_us")->default(0)->nullable();
            $table->tinyInteger("subscribe")->default(0)->nullable();
            $table->tinyInteger("store_page")->default(0)->nullable();
            $table->tinyInteger("topSection")->default(0)->nullable();
            $table->text("login")->nullable();
            $table->text("signup")->nullable();
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
        Schema::dropIfExists('ecommerces');
    }
}
