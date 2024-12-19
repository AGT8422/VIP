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
            $table->text("email")->nullable();
            $table->text("mobile")->nullable();
            $table->text("whatsapp")->nullable();
            $table->text("email_activation_code")->nullable();
            $table->text("email_activation_token")->nullable();
            $table->text("mobile_activation_code")->nullable();
            $table->text("mobile_activation_token")->nullable();
            $table->text("whatsapp_activation_code")->nullable();
            $table->text("whatsapp_activation_token")->nullable();
            $table->text("key")->nullable();
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
