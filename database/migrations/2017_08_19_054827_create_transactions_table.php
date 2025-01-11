<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('business_id')->unsigned();
            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');
            $table->enum('type', ['purchase', 'sell']);
            $table->string('separate_type')->nullable();
            $table->integer('separate_parent')->nullable();
            
            $table->enum('status', ['received', 'pending', 'ordered', 'draft', 'final']);
            $table->enum('payment_status', ['paid', 'due']);
            $table->integer('contact_id')->unsigned();
            $table->foreign('contact_id')->references('id')->on('contacts')->onDelete('cascade');
            $table->integer('customer_group_id')->nullable();
            $table->string('invoice_no')->nullable();
            $table->string('ref_no')->nullable();

            $table->string('additional_expense_key_1')->nullable();
            $table->decimal('additional_expense_value_1', 22, 4)->default(0);
            $table->string('additional_expense_key_2')->nullable();
            $table->decimal('additional_expense_value_2', 22, 4)->default(0);
            $table->string('additional_expense_key_3')->nullable();
            $table->decimal('additional_expense_value_3', 22, 4)->default(0);
            $table->string('additional_expense_key_4')->nullable();
            $table->decimal('additional_expense_value_4', 22, 4)->default(0);

            $table->text('refe_no')->nullable();
            $table->text('first_ref_no')->nullable();
            $table->text('previous')->nullable();
            $table->integer('list_price')->nullable();

            $table->dateTime('transaction_date');
            $table->decimal('total_before_tax', 22, 4)->default(0)->comment('Total before the purchase/invoice tax, this includeds the indivisual product tax');
            $table->integer('tax_id')->unsigned()->nullable();
            $table->foreign('tax_id')->references('id')->on('tax_rates')->onDelete('cascade');
            $table->decimal('tax_amount', 22, 4)->default(0);
            $table->tinyInteger('dis_currency')->default(0);
            $table->enum('discount_type', ['fixed', 'percentage','fixed_before_vat','fixed_after_vat'])->nullable();
            $table->decimal('discount_amount', 22, 4)->default(0);
            $table->string('shipping_details')->nullable();
            $table->decimal('shipping_charges', 22, 4)->default(0);
            $table->text('additional_notes')->nullable();
            $table->text('staff_note')->nullable();
            $table->tinyInteger('is_export')->default(0);
            $table->longText('export_custom_fields_info')->nullable();
            $table->decimal('final_total', 22, 4)->default(0);
            $table->integer('created_by')->unsigned();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->string('prefer_payment_method')->nullable();
            $table->integer('prefer_payment_account')->nullable();
            $table->text('sales_order_ids')->nullable();
            $table->text('purchase_order_ids')->nullable();
            $table->timestamp("end_date");
            $table->string('shipping_company_id')->nullable();
            
            
            
            $table->integer('store_in')->nullable();
            $table->integer('dis_type')->default(0);
            $table->integer('agent_id')->nullable();
            $table->text('sup_refe')->nullable();
            $table->integer('ship_amount')->nullable();
            $table->integer('cost_center_id')->nullable();
            $table->integer('pattern_id')->nullable();
            $table->integer('currency_id')->nullable();
            $table->decimal('amount_in_currency', 22, 4)->default(0)->nullable();
            $table->decimal('exchange_price', 22, 12)->default(1);
            $table->integer('ecommerce')->nullable();
            $table->text('note')->nullable();
            
            //Indexing
            $table->index('business_id');
            $table->index('type');
            $table->index('contact_id');
            $table->index('transaction_date');
            $table->index('created_by');
            $table->string('mfg_production_cost_type')->nullable()->default('percentage');
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
        Schema::dropIfExists('transactions');
    }
}
