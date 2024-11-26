<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGournalVoucherItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gournal_voucher_items', function (Blueprint $table) {
            $table->increments("id");
            $table->bigInteger("gournal_voucher_id")->unsigned();
            $table->bigInteger("credit_account_id")->unsigned();
            $table->bigInteger("tax_account_id")->unsigned();
            $table->bigInteger("debit_account_id")->unsigned();
            $table->decimal("amount",8,2);
            $table->double("tax_percentage",8,2)->default(0);
            $table->double("tax_amount",8,2)->default(0);
            $table->text("text")->nullable();
            $table->date("date");
            $table->integer("cost_center_id")->nullable();
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
        Schema::dropIfExists('gournal_voucher_items');
    }
}
