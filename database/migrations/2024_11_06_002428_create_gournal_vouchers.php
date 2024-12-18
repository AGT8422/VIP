<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGournalVouchers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gournal_vouchers', function (Blueprint $table) {
            $table->increments("id");
            $table->string("ref_no");
            $table->text("document")->nullable();
            $table->date("date")->nullable();
            $table->integer("business_id")->unsigned();
            $table->foreign("business_id")->on("business")->references("id")->onDelete("cascade")->onUpdate("cascade");
            $table->integer("main_account_id")->nullable();
            $table->integer("cost_center_id")->nullable();
            $table->integer("currency_id")->unsigned()->nullable();
            $table->decimal("amount_in_currency",22,4)->nullable();
            $table->decimal("exchange_price",22,4)->nullable();
            $table->decimal("total_credit",22,4)->nullable(); 
            $table->tinyInteger("main_credit")->default(0)->nullable();
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
        Schema::dropIfExists('gournal_vouchers');
    }
}
