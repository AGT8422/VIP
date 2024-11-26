<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArchivePurchaseLinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('archive_purchase_lines', function (Blueprint $table) {
            $table->increments("id"); 
            $table->integer("store_id")->nullable();
            $table->integer("product_id")->nullable();
            $table->integer("transaction_id")->nullable();
            $table->integer("variation_id")->nullable();
            $table->decimal("quantity",22,4);
            $table->decimal("pp_without_discount",22,4)->default(0);
            $table->decimal("discount_percent",22,4)->default(0);
            $table->decimal("purchase_price",22,4);
            $table->decimal("purchase_price_inc_tax",22,4)->default(0);
            $table->decimal("item_tax",22,4);
            $table->integer("tax_id")->unsigned()->nullable();
            $table->decimal("quantity_sold",22,4)->default(0);
            $table->decimal("quantity_adjusted",22,4)->default(0);
            $table->decimal("quantity_returned",22,4)->default(0);
            $table->decimal("mfg_quantity_used",22,4)->default(0);
            $table->date("mfg_date")->nullable();
            $table->date("exp_date")->nullable();
            $table->string("lot_number")->index();
            $table->integer("sub_unit_id")->nullable();
            $table->integer("sub_unit_qty")->nullable();
            $table->string("purchase_note")->nullable();
            $table->double("bill_return_price")->nullable();
            $table->integer("new_id")->nullable();
            $table->integer("main_transaction")->nullable();
            $table->integer("parent_id")->nullable();
            $table->integer("line_id")->nullable();
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
        Schema::dropIfExists('archive_purchase_lines');
    }
}
