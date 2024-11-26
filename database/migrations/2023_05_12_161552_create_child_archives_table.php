<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChildArchivesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('child_archives', function (Blueprint $table) {
            $table->increments("id");
            $table->integer('business_id')->nullable();
            $table->integer('location_id')->nullable();
            $table->integer('store_id')->nullable(); 
            $table->BigInteger('additional_shipping_id')->nullable();
            $table->BigInteger('ck_transaction_id')->nullable();
            $table->BigInteger('rp_transaction_id')->nullable();
            $table->BigInteger('dp_transaction_id')->nullable();
            $table->Integer('transaction_deliveries_id')->nullable();
            $table->Integer('transaction_recieveds_id')->nullable();
            $table->Integer('unit_id')->nullable();
            $table->unsignedInteger('type',0)->nullable();
            $table->double('sub_total')->nullable();
            $table->double('total_qty')->nullable();
            $table->double('current_qty')->nullable();
            $table->double('remain_qty')->nullable();
            $table->double('total')->nullable();
            $table->double('vat')->nullable();
            $table->double('amount',255)->nullable();
            $table->string('product_name',255)->nullable();
            $table->integer('line_id')->nullable();
            $table->string('ref_no',255)->nullable();
            $table->integer('contact_id')->nullable();
            $table->integer('account_id')->nullable();
            $table->integer('contact_bank_id')->nullable();
            $table->integer('collect_account_id')->nullable();
            $table->integer('credit_account_id')->nullable();
            $table->integer('tax_account_id')->nullable();
            $table->integer('debit_account_id')->nullable();
            $table->double('tax_percentage')->nullable();
            $table->double('tax_amount')->nullable();
            $table->integer('cost_center_id')->nullable();
            $table->integer('is_returned')->nullable();
            $table->string('text')->nullable();
            $table->string('note',255)->nullable();
            $table->string('document')->nullable();
            $table->integer('ch_status',0)->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('check_id')->nullable();
            $table->integer('payment_voucher_id')->nullable();
            $table->integer('transaction_payment_id')->nullable();
            $table->integer('daily_payment_id')->nullable();
            $table->integer('gournal_voucher_id')->nullable();
            $table->double('credit')->nullable();
            $table->double('debit')->nullable();
            $table->integer('log_parent_id')->nullable();
            $table->string('ref_number',255)->nullable();
            $table->string('cheque_no',255)->nullable();
            $table->string('state_action',255)->nullable();
            $table->dateTime('collecting_date')->nullable();
            $table->dateTime('due_date')->nullable();
            $table->dateTime('write_date')->nullable();
            $table->dateTime('date')->nullable();
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
        Schema::dropIfExists('child_archives');
    }
}
