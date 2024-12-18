<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdditionalShippings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('additional_shippings', function (Blueprint $table) {
            $table->increments("id");
            $table->text("document")->nullable();
            $table->bigInteger("transaction_id")->unsigned();
            $table->integer("type")->default(0)->nullable();
            $table->double("sub_total")->nullable();
            $table->integer("total_purchase")->nullable();
            $table->integer("total_ship")->nullable();
            $table->integer("t_recieved")->nullable();
            $table->integer("currency_id")->nullable();
            $table->decimal("exchange_rate",22,12)->nullable();
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
        Schema::dropIfExists('additional_shippings');
    }
}
