<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePrivacyConditionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('privacy_conditions', function (Blueprint $table) {
            $table->increments("id");
            $table->string("name")->nullable();
            $table->text("terms",500)->nullable();
            $table->text("privacy",500)->nullable();
            $table->text("return_policy",500)->nullable();
            $table->string("image")->nullable();
            $table->string("icon")->nullable();
            $table->string("img")->nullable();
            $table->tinyInteger("view")->default(0);
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
        Schema::dropIfExists('privacy_conditions');
    }
}
