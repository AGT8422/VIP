<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIzoUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('izo_users', function (Blueprint $table) {
            $table->increments('id');
            $table->tinyInteger('admin_user')->default(0);
            $table->string('company_name')->nullable();
            $table->string('mobile')->unique()->index();
            $table->string('email')->unique()->index();
            $table->string('password')->nullable();
            $table->string('status')->nullable();
            $table->string('database_user')->nullable();
            $table->string('device_id')->nullable();
            $table->string('ip')->nullable();
            $table->string('database_name')->nullable();
            $table->string('domain_name')->nullable();
            $table->string('domain_url')->nullable();
            $table->integer('seats')->nullable();
            $table->date('subscribe_expire_date')->nullable();
            $table->date('subscribe_date')->nullable();
            $table->tinyInteger('not_active')->default(0);
            $table->tinyInteger('is_migrate')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('izo_users');
    }
}
