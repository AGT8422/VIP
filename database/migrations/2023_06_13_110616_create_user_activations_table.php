<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserActivationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_activations', function (Blueprint $table) {
            $table->increments("id");
            $table->string("user_service",500)->nullable();
            $table->string("user_products",500)->nullable();
            $table->string("user_name",255)->nullable();
            $table->text("company_name")->nullable();
            $table->string("user_email",255)->nullable();
            $table->string("user_address",191)->nullable();
            $table->string("user_mobile",191)->nullable();
            $table->integer("user_number_device")->default(1)->nullable();
            $table->string("user_username",191)->nullable()->unique()->index();
            $table->string("user_password",191)->nullable()->unique()->index();
            $table->string("user_token",191)->nullable()->unique()->index();
            $table->decimal("user_payment",22,4)->nullable();
            $table->string("user_due_payment",500)->nullable();
            $table->string("user_status",255)->nullable();
            $table->date("user_dateactivate")->nullable()->index();
            $table->date("activation_period")->nullable()->index();
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
        Schema::dropIfExists('user_activations');
    }
}
