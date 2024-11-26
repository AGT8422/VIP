<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEcomTransactionSellLinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ecom_transaction_sell_lines', function (Blueprint $table) {
            $table->increments("id");
            $table->integer('ecom_transaction_id')->unsigned();
            $table->integer('store_id')->unsigned();
            $table->integer('product_id')->unsigned();
            $table->integer('variation_id')->unsigned();
            $table->decimal('quantity', 22, 4)->default(0);
            $table->decimal('quantity_returned', 22, 4)->default(0);
            $table->decimal('mfg_waste_percent', 22, 4)->default(0);
            $table->decimal('unit_price_before_discount', 22, 4)->nullable();
            $table->enum('line_discount_type',['fixed','percentage'])->nullable();
            $table->decimal('line_discount_amount', 22, 4)->default(0);
            $table->decimal('unit_price', 22, 4)->comment("Sell price excluding tax")->nullable();
            $table->decimal('unit_price_inc_tax', 22, 4)->comment("Sell price including tax")->nullable();
            $table->decimal('item_tax', 22, 4)->comment("Tax for one quantity");
            $table->integer('tax_id')->unsigned()->nullable();
            $table->integer('discount_id')->nullable();
            $table->integer('lot_no_line_id')->nullable();
            $table->string('sell_line_note')->nullable();
            $table->integer('woocommerce_line_items_id')->nullable();
            $table->integer('so_line_id')->nullable();
            $table->decimal('so_quantity_invoiced', 22, 4)->default(0);
            $table->integer('res_service_staff_id')->nullable();
            $table->string('res_line_order_status')->nullable();
            $table->integer('parent_sell_line_id')->nullable();
            $table->string('children_type');
            $table->integer('sub_unit_id')->nullable();
            $table->integer('kitchen_status')->default(0);
            $table->double('bill_return_price')->nullable();
            $table->string('se_note')->nullable();
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
        Schema::dropIfExists('ecom_transaction_sell_lines');
    }
}
