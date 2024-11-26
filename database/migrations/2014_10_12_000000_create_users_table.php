<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
           
            $table->increments('id');  
            $table->string('surname');
            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->string('username');
            $table->string('email')->nullable();
            $table->string('password')->nullable();
            $table->longText('api_token',500)->nullable();
            $table->text('google_id')->nullable();
            $table->text('two_factor_secret')->nullable();
            $table->text('two_factor_recovery_codes')->nullable();
            $table->text('address')->nullable();
            $table->char('language', 7)->default('en');
            $table->char('contact_no', 15)->nullable();    
            $table->enum('status',['active','inactive','terminated'])->default('active'); 
            $table->tinyInteger('is_cmmsn_agnt')->default(0);
            $table->decimal('cmmsn_percent',4,2)->default(0);   
            $table->text('pattern_id')->nullable();
            $table->timestamp('last_login_at')->nullable();
            $table->integer('user_account_id')->nullable();
            
            $table->integer('user_visa_account_id')->nullable();
            $table->integer('user_agent_id')->nullable();
            $table->integer('user_cost_center_id')->nullable();
            $table->integer('user_pattern_id')->nullable();
            $table->integer('user_store_id')->nullable();
            $table->integer('tax_id')->nullable(); 
            $table->tinyInteger('include')->default(0);
            
            $table->tinyInteger('is_admin_izo')->default(0);
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
