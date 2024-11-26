<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentVouchers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_vouchers', function (Blueprint $table) {
            $table->increments("id");
            $table->decimal("amount",22,4);
            $table->integer("business_id")->unsigned();
            $table->foreign("business_id")->on("business")->references("id")->onDelete("cascade")->onUpdate("cascade");
            $table->integer("type")->default(0)->nullable();
            $table->string("ref_no")->nullable();
            $table->integer("contact_id");
            $table->integer("account_id");
            $table->date("date")->nullable();
            $table->text("text")->nullable();
            $table->text("document")->nullable();
            $table->integer("currency_id")->nullable();
            $table->decimal("amount_in_currency",22,4)->nullable();
            $table->decimal("exchange_price",22,4)->nullable();
            $table->decimal("currency_amount",22,4)->nullable();
            $table->text("additional_account_id")->nullable();
            $table->tinyInteger("account_type")->default(0);
            $table->integer("is_invoice")->nullable();
            $table->tinyInteger("return_voucher")->default(0);
            
            
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
        Schema::dropIfExists('payment_vouchers');
    }
}
