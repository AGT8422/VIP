<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBusinessTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('business', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('name');
            $table->integer('currency_id')->unsigned();
            $table->foreign('currency_id')->references('id')->on('currencies');
            $table->integer('currency_id_add')->unsigned()->nullable();
            $table->foreign('currency_id_add')->references('id')->on('currencies');
            $table->date('start_date')->nullable();
            $table->string('tax_number_1', 100);
            $table->string('tax_label_1', 10);
            $table->string('tax_number_2', 100)->nullable();
            $table->string('tax_label_2', 10)->nullable(); 
            $table->float('default_profit_percent', 5, 2)->default(0);
            $table->integer('owner_id')->unsigned();
            $table->foreign('owner_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('time_zone')->default('Asia/Dubai');
            $table->tinyInteger('fy_start_month')->default(1);
            $table->enum('accounting_method', ['fifo', 'lifo', 'avco'])->default('fifo');
            $table->decimal('default_sales_discount', 5, 2)->nullable();
            $table->tinyInteger('source_sell_price')->default(0);
            $table->enum('sell_price_tax', ['includes', 'excludes'])->default('includes');
            $table->string('logo')->nullable();
            $table->string('sku_prefix')->nullable();
            $table->boolean('enable_tooltip')->default(1); 
            $table->integer('transaction_edit_days')->unsigned()->default(30);
            $table->date('transaction_edit_date')->nullable();
            $table->integer('stock_expiry_alert_days')->unsigned()->default(30);
            $table->text('keyboard_shortcuts')->nullable();
            $table->text('pos_settings')->nullable();   
            $table->boolean('enable_racks')->default(0);
            $table->boolean('enable_row')->default(0);
            $table->boolean('enable_position')->default(0);
            $table->boolean('enable_editing_product_from_purchase')->default(1);
            $table->enum('sales_cmsn_agnt', ['logged_in_user', 'user', 'cmsn_agnt'])->nullable();
            $table->boolean('item_addition_method')->default(1);
            $table->boolean('enable_inline_tax')->default(1);
            $table->enum('currency_symbol_placement', ['before', 'after'])->default('before');
            $table->text('enabled_modules')->nullable();
            $table->string('date_format',191)->default("m/d/Y")->nullable();
            $table->enum('time_format', ['12', '24' ])->default(24)->nullable();
            $table->text('ref_no_prefixes')->nullable();
            $table->char('theme_color', 20)->nullable();	
            $table->integer('created_by')->nullable(); 
            $table->text('email_settings')->nullable();
            $table->text('sms_settings')->nullable();  
            $table->boolean('is_active')->default(true);
            $table->integer('sharenumber')->default(0) ;
            $table->float('capital', 10, 2)->default(0) ;  
            $table->boolean('enable_composite_dis_pro')->default(1);
            $table->boolean('enable_max_qty_pro')->default(1);
            $table->boolean('enable_max_dis_pro')->default(1); 
            $table->float('rate_currency', 8, 2)->nullable() ;          
            $table->integer('second_currency_id')->nullable() ;
            $table->string('code_label_1',191)->nullable() ;
            $table->string('code_1',45)->nullable() ;
            $table->string('code_label_2',45)->nullable() ;
            $table->string('code_2',45)->nullable() ;
            $table->text('itemMfg')->nullable();
            $table->text('profitMfg')->nullable();
            $table->double('wastageMfg')->nullable();
            $table->integer('liability')->nullable();
            $table->integer('assets')->nullable();
            $table->integer('bank')->nullable();
            $table->integer('cash')->nullable();
            $table->integer('store_mfg')->nullable();
            $table->integer('app_pattern_id')->nullable();
            $table->integer('app_store_id')->nullable();
            $table->integer('app_account')->nullable();

            $table->text('web_color')->nullable();
            $table->text('web_font_color')->nullable();
            $table->string('web_second_color')->nullable();
            $table->string('web_logo')->nullable();
            $table->integer('navigation')->nullable();
            $table->integer('floating')->nullable();
            $table->text('ico')->nullable();
            $table->text('share')->nullable();
            $table->integer('customer_type_id')->nullable();
            $table->integer('supplier_type_id')->nullable();
            $table->tinyInteger('separate_sell')->default(0);
            $table->tinyInteger('separate_pay_sell')->default(0);
            $table->text('sale_print_module')->nullable();
            $table->text('quotation_print_module')->nullable();
            $table->text('approve_quotation_print_module')->nullable();
            $table->text('draft_print_module')->nullable();
            $table->text('return_sale_print_module')->nullable();
            $table->text('purchase_print_module')->nullable();
            $table->text('return_purchase_print_module')->nullable();
            $table->text('default_price_unit')->nullable();
            $table->text('front_dashboard_style')->nullable();
            
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
        Schema::dropIfExists('business');
    }
}
