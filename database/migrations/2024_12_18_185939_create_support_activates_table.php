<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSupportActivatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('support_activates', function (Blueprint $table) {
            $table->increments('id');
            $table->string("email")->nullable();
            $table->string("mobile")->nullable();
            $table->string("whatsapp")->nullable();
            $table->string("email_activation_code")->nullable();
            $table->string("email_activation_token")->nullable();
            $table->string("mobile_activation_code")->nullable();
            $table->string("mobile_activation_token")->nullable();
            $table->string("whatsapp_activation_code")->nullable();
            $table->string("whatsapp_activation_token")->nullable();
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
        Schema::dropIfExists('support_activates');
    }
}
