<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePrinterTemplateContainsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('printer_template_contains', function (Blueprint $table) {
            $table->increments("id");

            $table->string("left_header_title")->nullable();
            $table->integer("left_header_id")->nullable();
            $table->string("center_top_header_title")->nullable();
            $table->integer("center_top_header_id")->nullable();
            $table->string("center_middle_header_title")->nullable();
            $table->integer("center_middle_header_id")->nullable();
            $table->string("center_last_header_title")->nullable();
            $table->integer("center_last_header_id")->nullable();
            $table->text("header_image")->nullable();

            $table->string("left_footer_title")->nullable();
            $table->integer("left_footer_id")->nullable();
            $table->string("center_top_footer_title")->nullable();
            $table->integer("center_top_footer_id")->nullable();
            $table->string("center_middle_footer_title")->nullable();
            $table->integer("center_middle_footer_id")->nullable();
            $table->string("center_last_footer_title")->nullable();
            $table->integer("center_last_footer_id")->nullable();
            $table->text("footer_image")->nullable();
            
            $table->string("invoice_left_footer")->nullable();
            $table->string("quotation_term")->nullable();


            $table->text("left_header_radio",10)->nullable();
            $table->text("center_top_header_radio",10)->nullable();
            $table->text("center_middle_header_radio",10)->nullable();
            $table->text("center_last_header_radio",10)->nullable();
            $table->text("left_top_content_radio",10)->nullable();
            $table->text("right_top_content_radio",10)->nullable();
            $table->text("bottom_content_radio",10)->nullable();
            $table->integer("left_top_content_id")->nullable();
            $table->integer("right_top_content_id")->nullable();
            $table->integer("bottom_content_id")->nullable();
            $table->text("left_top_content",10)->nullable();
            $table->text("right_top_content",10)->nullable();
            $table->text("bottom_content",10)->nullable();
            $table->text("left_footer_radio",10)->nullable();
            $table->text("center_top_footer_radio",10)->nullable();
            $table->text("center_middle_footer_radio",10)->nullable();
            $table->text("center_last_footer_radio",10)->nullable();
            
            $table->integer("printer_templates_id");
            $table->integer("created_by");
            
            $table->text("body_content_top")->nullable();
            $table->text("body_content_margin_left")->nullable();
            $table->text("body_content_margin_right")->nullable();
            $table->text("body_content_margin_bottom")->nullable();
            
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
        Schema::dropIfExists('printer_template_contains');
    }
}
