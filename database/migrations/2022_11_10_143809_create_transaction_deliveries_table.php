<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionDeliveriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaction_deliveries', function (Blueprint $table) {
            $table->increments("id");
            $table->integer('transaction_id')->unsigned();
            $table->foreign('transaction_id')->references('id')->on('transactions')->onDelete('cascade');
            $table->integer('business_id')->unsigned();
            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade'); 
            $table->string('invoice_no');
            $table->string('ref_no');
            $table->double('amount');
            $table->string('status');
            $table->string('notes');
            $table->integer('delivered_for');
            $table->tinyInteger('is_returned')->nullable()->default(0);
            $table->integer('is_invoice')->nullable();
            $table->date('date')->nullable();
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
        Schema::dropIfExists('transaction_deliveries');
    }
}
