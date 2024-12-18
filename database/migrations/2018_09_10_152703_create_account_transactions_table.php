<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccountTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('account_transactions', function (Blueprint $table) {
            $table->increments('id');
            
            $table->integer('account_id');
            $table->enum('type', ['debit', 'credit']);
            $table->enum('sub_type', ['opening_balance', 'fund_transfer', 'deposit'])->nullable();
            $table->decimal('amount', 22, 4);
            $table->decimal('current_balance', 22, 4);
            $table->string('balance_type')->nullable();
            $table->string('reff_no')->nullable();
            $table->dateTime('operation_date');
            $table->integer('created_by');
            $table->integer('transaction_id')->nullable();
            $table->integer('transaction_payment_id')->nullable();
            $table->integer('transfer_transaction_id')->nullable();
            $table->text('note')->nullable();

            $table->integer('check_id')->nullable();
            $table->integer('for_repeat')->nullable();
            $table->integer('payment_voucher_id')->nullable();
            $table->integer('daily_payment_item_id')->nullable();
            $table->integer('gournal_voucher_item_id')->nullable();
            $table->integer('purchase_line_id')->nullable();
            $table->integer('additional_shipping_item_id')->nullable();
            $table->integer('transaction_sell_line_id')->nullable();
            $table->integer('cost_center_id')->nullable();
            $table->integer('return_transaction_id')->nullable();
            $table->integer('gournal_voucher_id')->nullable();
            $table->string('id_delete')->nullable();
            $table->string('transaction_array')->nullable();
            $table->integer('cs_related_id')->nullable();
            $table->integer('entry_id')->nullable();

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
        Schema::dropIfExists('account_transactions');
    }
}
