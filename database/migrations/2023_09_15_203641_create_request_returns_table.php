<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequestReturnsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('request_returns', function (Blueprint $table) {
            $table->increments("id");
            $table->integer("client_id")->nullable();
            $table->integer("transaction_id")->nullable();
            $table->text("transaction_sell_line_id")->nullable();
            $table->integer("quantity")->nullable();
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
        Schema::dropIfExists('request_returns');
    }
}
