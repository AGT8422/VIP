<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLastMovementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('last_movements', function (Blueprint $table) {
            $table->increments("id");
            $table->integer("client_id")->nullable();
            $table->text("url")->nullable();
            $table->string("type")->nullable();
            $table->integer("product_id")->nullable();
            $table->string("reference_number")->nullable();
            $table->integer("order_no")->nullable();
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
        Schema::dropIfExists('last_movements');
    }
}
