<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('account_settings', function (Blueprint $table) {
            $table->increments("id");
            $table->integer("pattern_id")->nullable();
            $table->integer("purchase")->nullable();
            $table->integer("purchase_tax")->nullable();
            $table->integer("purchase_discount")->nullable();
            $table->integer("purchase_return")->nullable();
            $table->integer("sale")->nullable();
            $table->integer("sale_tax")->nullable();
            $table->integer("sale_discount")->nullable();
            $table->integer("sale_return")->nullable();
            $table->integer("client_account_id")->nullable();
            $table->integer("client_visa_account_id")->nullable();
            $table->integer("client_store_id")->nullable();
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
        Schema::dropIfExists('account_settings');
    }
}
