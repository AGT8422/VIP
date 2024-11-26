<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('account_addresses', function (Blueprint $table) {
            $table->id();
            $table->string("title")->nullable();
            $table->string("building")->nullable();
            $table->string("street")->nullable();
            $table->string("flat")->nullable();
            $table->string("area")->nullable();
            $table->string("city")->nullable();
            $table->string("country")->nullable();
            $table->string("address_name")->nullable();
            $table->integer("address_type")->nullable();
            $table->integer("client_id")->nullable();
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
        Schema::dropIfExists('account_addresses');
    }
}
