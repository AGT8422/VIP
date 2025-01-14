<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePatternsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('patterns', function (Blueprint $table) {
            $table->increments("id");
            $table->string("code")->nullable();
            $table->integer("business_id")->unsigned();
            $table->foreign("business_id")->references("id")->on("business")->onDelete("cascade")->onUpdate("cascade");
            $table->integer("location_id")->unsigned();
            $table->foreign("location_id")->references("id")->on("business_locations")->onDelete("cascade")->onUpdate("cascade");
            $table->string("name",191)->nullable();
            $table->enum('type', ['purchase', 'sale','cheque'])->default('sale');
            $table->integer("invoice_scheme")->unsigned();
            $table->foreign("invoice_scheme")->references("id")->on("invoice_schemes")->onDelete("cascade")->onUpdate("cascade");
            $table->integer("invoice_layout")->unsigned();
            $table->foreign("invoice_layout")->references("id")->on("invoice_layouts")->onDelete("cascade")->onUpdate("cascade");
            $table->string("pos",255)->nullable();
            $table->integer("user_id")->unsigned();
            $table->foreign("user_id")->references("id")->on("users")->onDelete("cascade")->onUpdate("cascade");
            $table->integer("printer_type")->nullable();
            // $table->foreign("printer_type")->references("id")->on("printer_templates")->onDelete("cascade")->onUpdate("cascade");
            $table->tinyInteger("default_p")->default(0)->nullable();
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
        Schema::dropIfExists('patterns');
    }
}
