<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateECommerceClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('e_commerce_clients', function (Blueprint $table) {
            $table->increments("id");
            $table->string('business_name')->nullable();
            $table->integer('business_type')->nullable();
            $table->string('first_name')->nullable();
            $table->string('second_name')->nullable();
            $table->string('email')->nullable();
            $table->string('username')->nullable();
            $table->string('password')->nullable();
            $table->string('id_device')->nullable();
            $table->string('ip_address')->nullable();
            $table->integer('mobile')->nullable();
            $table->text('api_token',500)->nullable();
            $table->string('address')->nullable();
            $table->integer('account_id')->nullable();
            $table->integer('store_id')->nullable();
            $table->integer('pattern_id')->nullable();
            $table->integer('language')->nullable();
            $table->string('contact_id')->nullable();
            $table->string('account_number')->nullable();
            $table->string('email_personal')->nullable();
            $table->string('email_work')->nullable();
            $table->string('mobile_personal')->nullable();
            $table->string('mobile_work')->nullable();
            $table->string('gender')->nullable();
            $table->date('dob')->nullable();
            $table->string('google_login')->nullable();
            $table->integer('google_client_id')->nullable();
            $table->tinyInteger('block_client')->default(0)->nullable();
            $table->enum('client_status',['new', 'notactive', 'active'])->nullable();
           
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
        Schema::dropIfExists('e_commerce_clients');
    }
}
