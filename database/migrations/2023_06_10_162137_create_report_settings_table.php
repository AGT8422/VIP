<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReportSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('report_settings', function (Blueprint $table) {
            $table->increments("id");
            $table->integer("sale")->nullable();
            $table->integer("sale_return")->nullable()->index();
            $table->integer("sale_addtional_cost")->nullable()->index();
            $table->integer("sale_discount")->nullable()->index();
            $table->integer("purchase")->nullable()->index();
            $table->integer("purchase_return")->nullable()->index();
            $table->integer("purchase_addtional_cost")->nullable()->index();
            $table->integer("purchase_discount")->nullable()->index();
            $table->integer("purchase_opening_stock")->nullable()->index();
            $table->integer("purchase_closing_stock")->nullable()->index();
            $table->integer("expense")->nullable()->index();
            $table->integer("revenue")->nullable()->index();
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
        Schema::dropIfExists('report_settings');
    }
}
