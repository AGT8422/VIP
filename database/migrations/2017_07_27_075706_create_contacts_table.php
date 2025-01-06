<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContactsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contacts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('business_id')->unsigned();
            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');
            $table->string('type',191)->index();
            $table->string('supplier_business_name',191)->nullable();
            $table->string('name',191);    
            $table->string('email',191)->nullable();
            $table->string('contact_id',191)->nullable(); 
            $table->string('tax_number',191)->nullable();
            $table->string('city',191)->nullable();
            $table->string('state',191)->nullable();
            $table->string('country',191)->nullable();  
            $table->string('mobile',191);
            $table->string('landline',191)->nullable();
            $table->string('alternate_number',191)->nullable();
            $table->integer('pay_term_number')->nullable();
            $table->enum('pay_term_type', ['days', 'months'])->nullable();
            $table->decimal('credit_limit',22,4)->nullable();
            $table->integer('created_by')->unsigned(); 
            $table->tinyInteger('is_default')->default(0); 
             $table->string('landmark')->nullable(); 
            $table->integer('customer_group_id')->nullable(); 
            $table->string('custom_field1',191)->nullable();
            $table->string('custom_field2',191)->nullable();
            $table->string('custom_field3',191)->nullable();
            $table->string('custom_field4',191)->nullable(); 
            $table->integer('price_group_id')->nullable();
            $table->tinyInteger('e_commerce')->default(0)->nullable();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
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
        Schema::dropIfExists('contacts');
    }
}
