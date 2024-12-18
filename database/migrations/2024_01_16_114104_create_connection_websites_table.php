<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConnectionWebsitesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('connection_websites', function (Blueprint $table) {
            $table->increments("id");
            $table->string("e_commerce_url")->nullable();
            $table->string("erp_url")->nullable();
            $table->string("company_name")->nullable();
            $table->string("username")->nullable();
            $table->string("password")->nullable();
            $table->string("token_id")->nullable();
            $table->tinyInteger("active")->default(1);
            $table->date("date")->nullable();
            $table->date("end_date")->nullable();
            $table->integer("payments")->nullable();
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
        Schema::dropIfExists('connection_websites');
    }
}
