<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePosBranches extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pos_branches', function (Blueprint $table) {
            $table->increments("id");
            $table->string("name")->nullable(); 
            $table->integer("pattern_id")->unsigned();
            $table->integer("store_id")->unsigned();
            $table->integer("invoice_scheme_id")->unsigned();
            $table->integer("main_cash_id")->unsigned();
            $table->integer("cash_id")->unsigned();
            $table->integer("main_visa_id")->unsigned();
            $table->integer("visa_id")->unsigned(); 
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
        Schema::dropIfExists('pos_branches');
    }
}
