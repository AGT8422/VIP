<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateParentArchivesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('parent_archives', function (Blueprint $table) {
            $table->increments("id");
            $table->integer('business_id')->nullable();
            $table->integer('store_id')->nullable();
            $table->BigInteger('additional_shipping_id')->nullable();
            $table->BigInteger('ship_transaction_id')->nullable();
            $table->BigInteger('tr_transaction_id')->nullable();
            $table->BigInteger('td_transaction_id')->nullable();
            $table->string('tp_transaction_no',255)->nullable();
            $table->Integer('total_ship')->nullable();
            $table->unsignedInteger('type',0)->nullable();
            $table->double('sub_total')->nullable();
            $table->double('amount',255)->nullable();
            $table->string('ref_no',255)->nullable();
            $table->string('invoice_no',255)->nullable();
            $table->string('reciept_no',255)->nullable();
            $table->integer('contact_id')->nullable();
            $table->integer('account_id')->nullable();
            $table->integer('main_account_id')->nullable();
            $table->integer('cost_center_id')->nullable();
            $table->integer('is_return')->nullable();
            $table->integer('is_returned')->nullable();
            $table->Integer('t_recieved')->nullable();
            $table->string('text')->nullable();
            $table->string('note',255)->nullable();
            $table->string('document')->nullable();
            $table->string('status',255)->nullable();
            $table->string('method',255)->nullable();
            $table->string('card_transaction_number',255)->nullable();
            $table->string('card_number',255)->nullable();
            $table->string('card_type',255)->nullable();
            $table->string('card_holder_name',255)->nullable();
            $table->string('card_month',255)->nullable();
            $table->string('card_year',255)->nullable();
            $table->string('card_security',255)->nullable();
            $table->string('cheque_number',255)->nullable();
            $table->string('bank_account_number',255)->nullable();
            $table->integer('is_advance')->nullable();
            $table->integer('payment_for')->nullable();
            $table->integer('parent_id')->nullable();
            $table->string('payment_ref_no',255)->nullable();
            $table->string('contact_type',255)->nullable();
            $table->string('prepaid',255)->nullable();
            $table->double('amount_second_curr',255)->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('check_id')->nullable();
            $table->integer('payment_voucher_id')->nullable();
            $table->integer('gournal_voucher_id')->nullable();
            $table->integer('source')->nullable();
            $table->integer('log_parent_id')->nullable();
            $table->string('ref_number',255)->nullable();
            $table->string('state_action',255)->nullable();
            $table->dateTime('paid_on')->nullable();
            $table->dateTime('date')->nullable();
            $table->integer('line_id')->nullable();
            $table->double('total_purchase')->nullable();
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
        Schema::dropIfExists('parent_archives');
    }
}
