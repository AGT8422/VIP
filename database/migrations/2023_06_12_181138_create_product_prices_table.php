<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateProductPricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_prices', function (Blueprint $table) {
            $table->increments("id");
            $table->integer("business_id")->nullable();
            $table->integer("product_id")->nullable();
            $table->integer("variations_value_id")->nullable();
            $table->integer("variations_template_id")->nullable();
            $table->integer("ks_line")->nullable();
            $table->string("name",191)->nullable()->index();
            $table->decimal("price",22,4)->nullable();
            $table->text("list_of_price")->nullable();
            $table->integer("default_name")->nullable();
            $table->integer("number_of_default")->nullable();
            $table->date("date");
            $table->decimal("default_purchase_price",22,4)->nullable();
            $table->decimal("dpp_inc_tax",22,4)->nullable();
            $table->decimal("profit_percent",22,4)->nullable();
            $table->decimal("default_sell_price",22,4)->nullable();
            $table->decimal("sell_price_inc_tax",22,4)->nullable();
            $table->integer("unit_id")->nullable();
            $table->integer("parent_id")->nullable();
              
            $table->timestamps();
        });
        $time = \Carbon::now();
        DB::statement("INSERT INTO product_prices (id, business_id, product_id, variations_value_id, variations_template_id, ks_line, `name`, price, list_of_price, default_name, number_of_default, `date`, default_purchase_price, dpp_inc_tax, profit_percent, default_sell_price, sell_price_inc_tax, unit_id, parent_id, created_at, updated_at) VALUES ('1','1','?','?','?','?','Whole Price','?','[]','1','2024-12-20','0','0','0','0','0','0','?','?','?','?')",[null,null,null,null,null,null,null,$time,$time]);
        DB::statement("INSERT INTO product_prices (id, business_id, product_id, variations_value_id, variations_template_id, ks_line, `name`, price, list_of_price, default_name, number_of_default, `date`, default_purchase_price, dpp_inc_tax, profit_percent, default_sell_price, sell_price_inc_tax, unit_id, parent_id, created_at, updated_at) VALUES ('2','1','?','?','?','?','Retail Price','?','[]','1','2024-12-20','0','0','0','0','0','0','?','?','?','?')",[null,null,null,null,null,null,null,$time,$time]);
        DB::statement("INSERT INTO product_prices (id, business_id, product_id, variations_value_id, variations_template_id, ks_line, `name`, price, list_of_price, default_name, number_of_default, `date`, default_purchase_price, dpp_inc_tax, profit_percent, default_sell_price, sell_price_inc_tax, unit_id, parent_id, created_at, updated_at) VALUES ('3','1','?','?','?','?','Minimum Price','?','[]','1','2024-12-20','0','0','0','0','0','0','?','?','?','?')",[null,null,null,null,null,null,null,$time,$time]);
        DB::statement("INSERT INTO product_prices (id, business_id, product_id, variations_value_id, variations_template_id, ks_line, `name`, price, list_of_price, default_name, number_of_default, `date`, default_purchase_price, dpp_inc_tax, profit_percent, default_sell_price, sell_price_inc_tax, unit_id, parent_id, created_at, updated_at) VALUES ('4','1','?','?','?','?','Last Price','?','[]','1','2024-12-20','0','0','0','0','0','0','?','?','?','?')",[null,null,null,null,null,null,null,$time,$time]);
        DB::statement("INSERT INTO product_prices (id, business_id, product_id, variations_value_id, variations_template_id, ks_line, `name`, price, list_of_price, default_name, number_of_default, `date`, default_purchase_price, dpp_inc_tax, profit_percent, default_sell_price, sell_price_inc_tax, unit_id, parent_id, created_at, updated_at) VALUES ('5','1','?','?','?','?','ECM Before Price','?','[]','1','2024-12-20','0','0','0','0','0','0','?','?','?','?')",[null,null,null,null,null,null,null,$time,$time]);
        DB::statement("INSERT INTO product_prices (id, business_id, product_id, variations_value_id, variations_template_id, ks_line, `name`, price, list_of_price, default_name, number_of_default, `date`, default_purchase_price, dpp_inc_tax, profit_percent, default_sell_price, sell_price_inc_tax, unit_id, parent_id, created_at, updated_at) VALUES ('6','1','?','?','?','?','ECM After Price','?','[]','1','2024-12-20','0','0','0','0','0','0','?','?','?','?')",[null,null,null,null,null,null,null,$time,$time]);
        DB::statement("INSERT INTO product_prices (id, business_id, product_id, variations_value_id, variations_template_id, ks_line, `name`, price, list_of_price, default_name, number_of_default, `date`, default_purchase_price, dpp_inc_tax, profit_percent, default_sell_price, sell_price_inc_tax, unit_id, parent_id, created_at, updated_at) VALUES ('7','1','?','?','?','?','Custom Price 1','?','[]','1','2024-12-20','0','0','0','0','0','0','?','?','?','?')",[null,null,null,null,null,null,null,$time,$time]);
        DB::statement("INSERT INTO product_prices (id, business_id, product_id, variations_value_id, variations_template_id, ks_line, `name`, price, list_of_price, default_name, number_of_default, `date`, default_purchase_price, dpp_inc_tax, profit_percent, default_sell_price, sell_price_inc_tax, unit_id, parent_id, created_at, updated_at) VALUES ('8','1','?','?','?','?','Custom Price 2','?','[]','1','2024-12-20','0','0','0','0','0','0','?','?','?','?')",[null,null,null,null,null,null,null,$time,$time]);
        DB::statement("INSERT INTO product_prices (id, business_id, product_id, variations_value_id, variations_template_id, ks_line, `name`, price, list_of_price, default_name, number_of_default, `date`, default_purchase_price, dpp_inc_tax, profit_percent, default_sell_price, sell_price_inc_tax, unit_id, parent_id, created_at, updated_at) VALUES ('9','1','?','?','?','?','Custom Price 3','?','[]','1','2024-12-20','0','0','0','0','0','0','?','?','?','?')",[null,null,null,null,null,null,null,$time,$time]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_prices');
    }
}
