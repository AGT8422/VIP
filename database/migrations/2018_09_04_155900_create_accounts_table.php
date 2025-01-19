<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('accounts');
        
        Schema::create('accounts', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('business_id');
            $table->integer('contact_id')->nullable();
            $table->string('name');
            $table->string('account_number');
            $table->enum('account_type', ['saving_current', 'capital'])->nullable();
            $table->text('note')->nullable();
            $table->integer('created_by');
            $table->boolean('is_closed')->default(0);
            $table->integer('is_second_curr'); 
            $table->integer('cost_center')->default(0);
            $table->decimal('balance',22,4)->nullable();
            $table->decimal('currency_balance',22,4)->nullable();
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
        Schema::dropIfExists('accounts');
    }
}
