<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableSellInstallments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('table_sell_installments', function (Blueprint $table) {
            $table->increments("id");  
            $table->integer("transaction_id")->unsigned();
            $table->foreign("transaction_id")->on("transactions")->references("id")->onDelete("cascade")->onUpdate("cascade");
            $table->decimal("amount",22,4);
            $table->date("due_at");
            $table->date("paid_at")->nullable();
            $table->string("status");
            $table->integer("TP_id")->nullable();
            $table->string("fine")->nullable();
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
        Schema::dropIfExists('table_sell_installments');
    }
}
