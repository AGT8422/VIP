<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNavigationBarsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('navigation_bars', function (Blueprint $table) {
            $table->increments("id");
            $table->integer("business_id")->unsigned();
            $table->foreign("business_id")->on("business")->references("id")->onDelete("cascade")->onUpdate("cascade");
            $table->string("title")->nullable();
            $table->string("icon")->nullable();
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
        Schema::dropIfExists('navigation_bars');
    }
}
