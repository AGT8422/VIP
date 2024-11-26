<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStoreFeaturesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('store_features', function (Blueprint $table) {
            $table->increments("id");
            $table->integer("business_id")->unsigned();
            $table->foreign("business_id")->references("id")->on("business")->onDelete("cascade")->onUpdate("cascade");
            $table->string("title")->nullable();
            $table->string("image")->nullable();
            $table->string("description")->nullable();
            $table->integer("client_id")->unsigned();
            $table->foreign("client_id")->references("id")->on("e_commerce_clients")->onDelete("cascade")->onUpdate("cascade");
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
        Schema::dropIfExists('store_features');
    }
}
