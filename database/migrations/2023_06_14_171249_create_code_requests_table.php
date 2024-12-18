<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCodeRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('code_requests', function (Blueprint $table) {
            $table->increments("id");
            $table->string("type",191)->nullable();
            $table->string("name",191)->nullable()->index();
            $table->string("company_name",191)->nullable()->index();
            $table->string("device_no",191)->nullable()->index();
            $table->string("address",191)->nullable()->index();
            $table->string("email",191)->nullable()->index();
            $table->string("mobile",191)->nullable()->index();
            $table->string("services",191)->nullable()->index();
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
        Schema::dropIfExists('code_requests');
    }
}
