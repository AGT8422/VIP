<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEntriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('entries', function (Blueprint $table) {
            $table->increments("id");
            $table->integer("business_id")->unsigned();
            $table->foreign("business_id")->references("id")->on("business")->onDelete("cascade")->onUpdate("cascade");
            $table->string("refe_no_e",191)->nullable();
            $table->integer("account_transaction")->unsigned();
            $table->foreign("account_transaction")->references("id")->on("transactions")->onDelete("cascade")->onUpdate("cascade")->nullable();
            $table->string("ref_no_e",191)->nullable();
            $table->double("debit");
            $table->double("vat");
            $table->double("credit");
            $table->integer("check_id")->nullable();
            $table->integer("voucher_id")->nullable();
            $table->integer("return_id")->nullable();
            $table->integer("journal_voucher_id")->nullable();
            $table->integer("shipping_id")->nullable();
            $table->integer("shipping_item_id")->nullable();
            $table->integer("expense_voucher_id")->nullable();
            $table->integer("payment_id")->nullable();
            $table->string("state",191)->nullable();
            $table->date("deleted_at_date")->nullable();
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
        Schema::dropIfExists('entries');
    }
}
