<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWebCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('web_comments', function (Blueprint $table) {
            $table->increments("id");
            $table->integer("number_of_stars")->nullable();
            $table->string("message")->nullable();
            $table->integer("message_parent_id")->nullable();
            $table->integer("client_id")->nullable();
            $table->string("clients_shared")->nullable();
            $table->integer("product_id")->nullable();
            $table->enum("liked_emoji",["","like","loved","dislike","angry"])->default("");
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
        Schema::dropIfExists('web_comments');
    }
}
