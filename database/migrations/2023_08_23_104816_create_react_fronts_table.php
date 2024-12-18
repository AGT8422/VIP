<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReactFrontsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('react_fronts', function (Blueprint $table) {
            $table->increments("id");
            $table->string("name",255)->nullable();
            $table->string("surname",255)->nullable();
            $table->string("username",191)->unique();
            $table->string("password",255);
            $table->text("device_id",500)->nullable();
            $table->text("device_ip",500)->nullable();
            $table->string("email",255)->nullable();
            $table->string("mobile",12)->nullable();
            $table->string("api_url",191)->unique();
            $table->string("last_login",255)->nullable();
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
        Schema::dropIfExists('react_fronts');
    }
}
