<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEadminsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('eadmins', function (Blueprint $table) {
            $table->increments("id");
            $table->string("name")->nullable();
            $table->string("username")->index();
            $table->string("password")->nullable();
            $table->tinyInteger("active")->default(0);
            $table->string("role");
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
        Schema::dropIfExists('eadmins');
    }
}
