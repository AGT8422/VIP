<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChecks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('checks', function (Blueprint $table) {
            $table->increments("id");
            $table->integer("collect_account_id")->nullable();
            $table->integer("business_id")->unsigned();
            $table->foreign("business_id")->on("business")->references("id")->onDelete("cascade")->onUpdate("cascade");
            $table->integer("transaction_id")->unsigned();
            $table->foreign("transaction_id")->on("transactions")->references("id")->onDelete("cascade")->onUpdate("cascade");
            $table->string("ref_no",255)->nullable();
            $table->integer("location_id")->unsigned();
            $table->bigInteger("contact_id")->unsigned()->nullable();
            $table->bigInteger("account_id")->unsigned()->nullable();
            $table->bigInteger("contact_bank_id")->unsigned()->nullable();
            $table->integer("type")->default(0)->nullable();
            $table->integer("status")->default(0)->nullable();
            $table->decimal("amount",8,2);
            $table->date("write_date");
            $table->date("due_date");
            $table->string("cheque_no",255)->nullable();
            $table->date("collecting_date")->nullable();
            $table->integer("transaction_payment_id")->nullable();
            $table->string("note",190)->nullable();
            $table->text("document")->nullable();
            $table->integer("currency_id")->nullable();
            $table->decimal("amount_in_currency",22,4)->nullable();
            $table->decimal("exchange_price",22,4)->nullable();
            $table->integer("account_type")->nullable();
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
        Schema::dropIfExists('checks');
    }
}
