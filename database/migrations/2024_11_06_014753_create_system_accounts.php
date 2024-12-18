<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSystemAccounts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('system_accounts', function (Blueprint $table) {
            $table->increments("id");  
            $table->integer("business_id")->unsigned();
            $table->foreign("business_id")->on("business")->references("id")->onDelete("cascade")->onUpdate("cascade");
            $table->integer("pattern_id");
            $table->integer("purchase")->unsigned();
            $table->integer("purchase_tax")->unsigned();
            $table->integer("sale")->unsigned();
            $table->integer("sale_tax")->unsigned();
            $table->integer("cheque_debit")->unsigned();
            $table->integer("cheque_collection")->unsigned();
            $table->integer("journal_expense_tax")->unsigned();
            $table->integer("sale_return")->unsigned()->nullable();
            $table->integer("sale_discount")->unsigned()->nullable();
            $table->integer("purchase_return")->unsigned()->nullable();
            $table->integer("purchase_discount")->unsigned()->nullable();
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
        Schema::dropIfExists('system_accounts');
    }
}
