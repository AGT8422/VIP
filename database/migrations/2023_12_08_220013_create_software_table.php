<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSoftwareTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('software', function (Blueprint $table) {
            $table->increments("id");
            $table->string("name")->nullable();
            $table->string("title")->nullable();
            $table->string("description")->nullable();
            $table->text("button")->nullable();
            $table->text("image")->nullable();
            $table->text("video")->nullable();
            $table->text("alter_image")->nullable();
            $table->tinyInteger("topSection")->default(0);
            $table->tinyInteger("view")->default(0);
            $table->integer("created_by");
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
        Schema::dropIfExists('software');
    }
}
