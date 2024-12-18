<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArchiveTransactions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('archive_transactions', function (Blueprint $table) {
            $table->increments("id");
            $table->integer("store")->unsigned()->nullable();
            $table->integer("business_id")->unsigned()->nullable();
            $table->integer("location_id")->unsigned()->nullable();
            $table->integer("res_table_id")->unsigned()->nullable();
            $table->integer("res_waiter_id")->unsigned()->nullable();
            $table->enum('res_order_status', ['cooked', 'pending', 'served'])->nullable();
            $table->string("type")->nullable();
            $table->string("sub_type")->nullable();
            $table->string("status")->nullable();
            $table->string("sub_status")->nullable();
            $table->tinyInteger("is_quotation")->default(1);
            $table->enum('payment_status', ['paid', 'due', 'served'])->nullable();
            $table->enum('adjustment_type', ['normal', 'abnormal'])->nullable();
            $table->integer("contact_id")->unsigned()->nullable();
            $table->integer("customer_group_id")->nullable();
            $table->string("invoice_no")->nullable();
            $table->string("ref_no")->nullable();
            $table->string("subscription_no")->nullable();
            $table->string("subscription_repeat_on")->nullable();
            $table->dateTime("transaction_date");
            $table->decimal("total_before_tax",22,4)->default(0);
            $table->integer("tax_id")->nullable();
            $table->decimal("tax_amount",22,4)->default(0);
            $table->enum('discount_type', ['fixed_before_vat', 'fixed_after_vat','percentage'])->nullable();
            $table->decimal("discount_amount",22,4)->default(0);
            $table->integer("rp_redeemed")->nullable();
            $table->decimal("rp_redeemed_amount",22,4)->default(0);
            $table->string("shipping_details")->nullable();
            $table->text("shipping_address")->nullable();
            $table->string("shipping_status")->nullable(); 
            $table->decimal("shipping_charges",22,4)->default(0);
            $table->string("delivered_to")->nullable();
            
            $table->string("shipping_custom_field_1")->nullable();
            $table->string("shipping_custom_field_2")->nullable();
            $table->string("shipping_custom_field_3")->nullable();
            $table->string("shipping_custom_field_4")->nullable();
            $table->string("shipping_custom_field_5")->nullable();
            $table->text("additional_notes")->nullable();
            $table->text("staff_note")->nullable();
            $table->tinyInteger("is_export")->default(1);
            $table->longText("export_custom_fields_info")->nullable();
            $table->decimal("round_off_amount",22,4)->default(0);
            $table->string("additional_expense_key_1")->nullable();
            $table->decimal("additional_expense_value_1",22,4)->default(0);
            $table->string("additional_expense_key_2")->nullable();
            $table->decimal("additional_expense_value_2",22,4)->default(0);
            $table->string("additional_expense_key_3")->nullable();
            $table->decimal("additional_expense_value_3",22,4)->default(0);
            $table->string("additional_expense_key_4")->nullable();
            $table->decimal("additional_expense_value_4",22,4)->default(0);
            $table->decimal("final_total",22,4)->default(0);
            
            $table->integer("expense_category_id")->nullable();
            $table->integer("expense_for")->unsigned()->nullable();

            $table->integer("commission_agent")->nullable();
            $table->text("document")->nullable();
            
            $table->tinyInteger("is_direct_sale")->nullable();
            $table->tinyInteger("is_suspend")->nullable();
            $table->decimal("exchange_rate",22,4)->default(1);
            
            $table->decimal("total_amount_recovered",22,4)->default(0);
            
            $table->integer("transfer_parent_id")->nullable();
            $table->integer("return_parent_id")->nullable();
            
            $table->integer("opening_stock_product_id")->nullable();
            $table->integer("created_by")->unsigned()->nullable();
            $table->integer("woocommerce_order_id")->unsigned()->nullable();
            $table->dateTime("repair_completed_on")->nullable();
            $table->integer("repair_warranty_id")->nullable();
            $table->integer("repair_brand_id")->nullable();
            $table->integer("repair_status_id")->nullable();
            $table->integer("repair_model_id")->nullable();
            $table->integer("repair_job_sheet_id")->unsigned()->nullable();
            $table->text("repair_defects")->nullable();
            $table->string("repair_serial_no")->nullable();
            $table->text("repair_checklist")->nullable();
            $table->string("repair_security_pwd")->nullable();
            $table->string("repair_security_pattern")->nullable();
            $table->dateTime("repair_due_date")->nullable();
            $table->integer("repair_device_id")->nullable();
            $table->tinyInteger("repair_updates_notif")->default(0);
            $table->integer("mfg_parent_production_purchase_id")->nullable();
            
            $table->decimal("mfg_wasted_units",22,4)->nullable();
            $table->decimal("mfg_production_cost",22,4)->default(0);
            $table->string("mfg_production_cost_type")->default('percentage')->nullable();
            $table->tinyInteger("mfg_is_final")->default(1);
            $table->decimal("essentials_duration",8,4);
            $table->string("essentials_duration_unit")->nullable();
            $table->decimal("essentials_amount_per_unit_duration",8,4)->default(0);
            $table->text("essentials_allowances")->nullable();
            $table->text("essentials_deductions")->nullable();
            $table->string("prefer_payment_method")->nullable();
            $table->integer("prefer_payment_account")->nullable();
            $table->text("sales_order_ids")->nullable();
            $table->text("purchase_order_ids")->nullable();
            $table->string("custom_field_1")->nullable();
            $table->string("custom_field_2")->nullable();
            $table->string("custom_field_3")->nullable();
            $table->string("custom_field_4")->nullable(); 
            $table->integer("import_batch")->nullable();
            $table->dateTime("import_time")->nullable();
            $table->integer("types_of_service_id")->nullable();
            $table->decimal("packing_charge",22,4)->nullable();
            $table->enum('packing_charge_type', ['fixed', 'percent'])->nullable();
            $table->text("service_custom_field_1")->nullable();
            $table->text("service_custom_field_2")->nullable();
            $table->text("service_custom_field_3")->nullable();
            $table->text("service_custom_field_4")->nullable();
            $table->text("service_custom_field_5")->nullable();
            $table->text("service_custom_field_6")->nullable();
            $table->tinyInteger("is_created_from_api")->default(0);
            $table->integer("rp_earned")->default(0);
            $table->string("order_addresses")->nullable();
            $table->tinyInteger("is_recurring")->default(1);
            $table->double("recur_interval",22,4)->nullable();
            $table->enum('recur_interval_type', ['days', 'months','years'])->nullable();
            $table->integer("recur_repetitions")->nullable();
            $table->dateTime("recur_stopped_on")->nullable();
            $table->integer("recur_parent_id")->nullable();
            $table->string("invoice_token")->nullable();
            $table->integer("pay_term_number")->nullable();
            $table->enum('pay_term_type', ['days', 'months'])->nullable();
            $table->integer("pjt_project_id")->unsigned()->nullable();
            $table->string("pjt_title")->nullable();
            $table->integer("selling_price_group_id")->unsigned()->nullable();
            $table->timestamp("end_date");   
            $table->string("shipping_company_id")->nullable();
            $table->string("refe_no")->nullable();
            $table->integer("due_state")->unsigned()->nullable();
            $table->string("project_no")->nullable();
            $table->integer("store_in")->nullable();
            $table->integer("dis_type")->nullable();
            $table->integer("agent_id")->nullable();
            $table->text("sup_refe")->nullable();
            $table->text("first_ref_no")->nullable();
            $table->text("previous")->nullable();
            $table->integer("ship_amount")->nullable();
            $table->integer("cost_center_id")->nullable();
            $table->integer("pattern_id")->nullable();
            $table->integer("new_id")->nullable();
            $table->string("state_action",255)->nullable();
            $table->integer("parent_id")->nullable();
            $table->string("ref_number",255)->nullable();

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
        Schema::dropIfExists('archive_transactions');
    }
}
