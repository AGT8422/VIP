<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePrinterTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('printer_templates', function (Blueprint $table) {
            $table->increments('id');
            $table->integer("business_id");
            $table->text("name_template",40)->nullable();
            $table->integer("page_size")->nullable();
            $table->text("Form_type",40)->nullable();
            $table->text("type",40)->nullable();
            $table->boolean("header_view")->default(0); 
            $table->text("style_header",40)->nullable(); 
            $table->text("header_width",40)->nullable(); 
            $table->text("header_style_letter",40)->nullable(); 
            $table->text("header_table_width",40)->nullable(); 
            $table->text("header_table_color",40)->nullable(); 
            $table->text("header_table_radius",40)->nullable(); 
            $table->text("align_text_header",40)->nullable(); 
            $table->text("header_font_size",40)->nullable(); 
            $table->text("header_font_weight",40)->nullable(); 
            $table->text("header_border_width",40)->nullable(); 
            $table->text("header_border_style",40)->nullable(); 
            $table->text("header_border_color",40)->nullable(); 
            $table->text("header_padding_left",40)->nullable(); 
            $table->text("header_padding_top",40)->nullable(); 
            $table->text("header_padding_right",40)->nullable(); 
            $table->text("header_padding_bottom",40)->nullable(); 
            $table->text("header_position",40)->nullable(); 
            $table->text("header_top",40)->nullable(); 
            $table->text("header_bottom",40)->nullable(); 
            $table->text("header_left",40)->nullable(); 
            $table->text("header_right",40)->nullable(); 
            $table->text("header_box_width",40)->nullable(); 
            $table->text("header_box_border_style",40)->nullable(); 
            $table->text("header_box_border_width",40)->nullable(); 
            $table->text("header_box_background",40)->nullable(); 
            $table->text("header_box_border_color",40)->nullable(); 
            $table->text("header_box_border_radius",40)->nullable(); 
            $table->text("header_image_view",40)->nullable(); 
            $table->text("align_image_header",40)->nullable(); 
            $table->text("position_img_header",40)->nullable(); 
            $table->text("header_image_width",40)->nullable(); 
            $table->text("header_image_height",40)->nullable(); 
            $table->text("header_image_border_width",40)->nullable(); 
            $table->text("header_image_border_color",40)->nullable(); 
            $table->text("header_image_border_style",40)->nullable(); 
            $table->text("header_image_border_radius",40)->nullable(); 
            $table->text("header_image_box_width",40)->nullable(); 
            $table->text("header_image_box_height",40)->nullable(); 
            $table->text("header_image_box_margin",40)->nullable(); 
            $table->text("header_box_image_background",40)->nullable(); 
            $table->text("header_box_image_color",40)->nullable(); 
            $table->text("position_box_header_align",40)->nullable(); 
            $table->text("header_image_box_background",40)->nullable(); 
            $table->text("header_image_box_border_width",40)->nullable(); 
            $table->text("header_image_box_border_style",40)->nullable(); 
            $table->text("header_image_box_border_color",40)->nullable(); 
            $table->text("header_image_box_border_radius",40)->nullable(); 
            $table->boolean("header_other_view")->default(0); 
            $table->text("align_other_header",40)->nullable(); 
            $table->text("other_background_header",40)->nullable(); 
            $table->text("header_other_width",40)->nullable(); 
            $table->text("header_other_border_radius",40)->nullable(); 
            $table->text("header_other_border_width",40)->nullable(); 
            $table->text("header_other_border_style",40)->nullable(); 
            $table->text("header_other_border_color",40)->nullable(); 
            $table->text("header_other_position",40)->nullable(); 
            $table->text("header_other_top",40)->nullable(); 
            $table->text("header_other_left",40)->nullable(); 
            $table->text("header_other_right",40)->nullable(); 
            $table->text("header_other_bottom",40)->nullable(); 
            $table->text("header_tax_align",40)->nullable(); 
            $table->text("header_tax_font_size",40)->nullable(); 
            $table->text("header_tax_width",40)->nullable(); 
            $table->text("header_tax_letter",40)->nullable(); 
            $table->text("header_tax_border_width",40)->nullable();
            $table->text("header_tax_border_style",40)->nullable();
            $table->text("header_tax_border_color",40)->nullable();
            $table->text("header_tax_padding_top",40)->nullable();
            $table->text("header_tax_padding_left",40)->nullable();
            $table->text("header_tax_padding_right",40)->nullable();
            $table->text("header_tax_padding_bottom",40)->nullable();
            $table->text("header_tax_position",40)->nullable();
            $table->text("header_tax_top",40)->nullable();
            $table->text("header_tax_bottom",40)->nullable();
            $table->text("header_tax_left",40)->nullable();
            $table->text("header_tax_right",40)->nullable();
            $table->text("header_address_align",40)->nullable();
            $table->text("header_address_font_size",40)->nullable();
            $table->text("header_address_width",40)->nullable();
            $table->text("header_address_letter",40)->nullable();
            $table->text("header_address_border_width",40)->nullable();
            $table->text("header_address_border_style",40)->nullable();
            $table->text("header_address_border_color",40)->nullable();
            $table->text("header_address_padding_top",40)->nullable();
            $table->text("header_address_padding_left",40)->nullable();
            $table->text("header_address_padding_right",40)->nullable();
            $table->text("header_address_padding_bottom",40)->nullable();
            $table->text("header_address_position",40)->nullable();
            $table->text("header_address_top",40)->nullable();
            $table->text("header_address_bottom",40)->nullable();
            $table->text("header_address_left",40)->nullable();
            $table->text("header_address_right",40)->nullable();
            $table->text("header_bill_align",40)->nullable();
            $table->text("header_bill_font_size",40)->nullable();
            $table->text("header_bill_width",40)->nullable();
            $table->text("header_bill_letter",40)->nullable();
            $table->text("header_bill_border_width",40)->nullable();
            $table->text("header_bill_border_style",40)->nullable();
            $table->text("header_bill_border_color",40)->nullable();
            $table->text("header_bill_padding_top",40)->nullable();
            $table->text("header_bill_padding_left",40)->nullable();
            $table->text("header_bill_padding_right",40)->nullable();
            $table->text("header_bill_padding_bottom",40)->nullable();
            $table->text("header_bill_position",40)->nullable();
            $table->text("header_bill_top",40)->nullable();
            $table->text("header_bill_bottom",40)->nullable();
            $table->text("header_bill_left",40)->nullable();
            $table->text("header_bill_right",40)->nullable();
            $table->boolean("header_line_view")->default(0);
            $table->text("header_line_height",40)->nullable();
            $table->text("header_line_width",40)->nullable();
            $table->text("header_line_color",40)->nullable(); 
            $table->text("header_line_margin_top",40)->nullable();
            $table->text("header_line_radius",40)->nullable();
            $table->text("header_line_border_width",40)->nullable();
            $table->text("header_line_border_style",40)->nullable();
            $table->text("header_line_border_color",40)->nullable(); 
           
          

           
             
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
        Schema::dropIfExists('printer_templates');
    }
}
