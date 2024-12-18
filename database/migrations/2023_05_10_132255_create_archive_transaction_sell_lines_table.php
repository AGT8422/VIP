<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArchiveTransactionSellLinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('archive_transaction_sell_lines', function (Blueprint $table) {
            $table->increments("id");
            $table->integer("store_id")->nullable();
            $table->integer("product_id")->nullable();
            $table->integer("transaction_id")->nullable();
            $table->integer("variation_id")->nullable();
            $table->decimal("quantity",22,4)->default(0);
            $table->decimal("mfg_waste_percent",22,4)->default(0);
            $table->decimal("quantity_returned",22,4)->default(0);
            $table->decimal("unit_price_before_discount",22,4)->default(0);
            $table->decimal("unit_price",22,4)->nullable();
            $table->enum('line_discount_type', ['fixed', 'percentage'])->nullable();
            $table->decimal("line_discount_amount",22,4)->default(0);
            $table->decimal("unit_price_inc_tax",22,4)->nullable();
            $table->decimal("item_tax",22,4);
            $table->integer("tax_id")->unsigned()->nullable();
            $table->integer("discount_id")->nullable();
            $table->integer("lot_no_line_id")->nullable();
            $table->text("sell_line_note")->nullable();
            $table->integer("woocommerce_line_items_id")->nullable();
            $table->integer("so_line_id")->nullable();
            $table->decimal("so_quantity_invoiced",22,4)->default(0);
            $table->integer("res_service_staff_id")->nullable();
            $table->string("res_line_order_status")->nullable();
            $table->integer("parent_sell_line_id")->nullable();
            $table->string("children_type")->nullable();
            $table->integer("sub_unit_id")->nullable();
            $table->string("kitchen_status")->default(0);
            $table->double("bill_return_price",22,4)->nullable();
            $table->integer("main_transaction")->nullable();
            $table->integer("new_id")->nullable();
            $table->integer("line_id")->nullable();
            $table->integer("parent_id")->nullable();
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
        Schema::dropIfExists('archive_transaction_sell_lines');
    }
}
