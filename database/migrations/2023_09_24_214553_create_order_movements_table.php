<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderMovementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_movements', function (Blueprint $table) {
            $table->increments("id");
            $table->integer("bill_id")->nullable();
            $table->string("state")->nullable();
            $table->string("reference_no")->nullable();
            $table->decimal("total",22,4)->nullable();
            $table->string("payment_status")->nullable();
            $table->string("delivery_status")->nullable();
            $table->timestamp("date")->nullable();
            $table->integer("created_by");
            $table->string("type")->nullable();
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
        Schema::dropIfExists('order_movements');
    }
}
