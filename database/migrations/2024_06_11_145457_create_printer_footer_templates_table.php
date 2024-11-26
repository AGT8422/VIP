<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePrinterFooterTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('printer_footer_templates', function (Blueprint $table) {
            $table->increments('id');
            $table->integer("printer_template_id")->unsigned();  
            $table->foreign("printer_template_id")->on("printer_templates")->references("id")->onDelete("cascade")->onUpdate("cascade");  
             $table->boolean("footer_view")->default(0);
            $table->text("style_footer",40)->nullable();
            $table->text("footer_width",40)->nullable();
            $table->text("footer_style_letter",40)->nullable();
            $table->text("footer_table_width",40)->nullable();
            $table->text("footer_table_color",40)->nullable();  
            $table->text("footer_table_radius",40)->nullable();
            $table->text("align_text_footer",40)->nullable();
            $table->text("footer_font_size",40)->nullable();
            $table->text("footer_font_weight",40)->nullable();  
            $table->text("footer_border_width",40)->nullable();
            $table->text("footer_border_style",40)->nullable();
            $table->text("footer_border_color",40)->nullable();  
            $table->text("footer_padding_left",40)->nullable();
            $table->text("footer_padding_top",40)->nullable();
            $table->text("footer_padding_bottom",40)->nullable();
            $table->text("footer_padding_right",40)->nullable();
            $table->text("footer_position",40)->nullable();  
            $table->text("footer_top",40)->nullable();
            $table->text("footer_bottom",40)->nullable();
            $table->text("footer_left",40)->nullable();
            $table->text("footer_right",40)->nullable();
            $table->text("footer_box_width",40)->nullable();
            $table->text("footer_box_border_style",40)->nullable();
            $table->text("footer_box_border_width",40)->nullable();
            $table->text("footer_box_background",40)->nullable(); 
            $table->text("footer_box_border_color",40)->nullable(); 
            $table->text("footer_box_border_radius",40)->nullable();
            $table->boolean("footer_image_view")->default(0); 
            $table->text("align_image_footer",40)->nullable(); 
            $table->text("position_img_footer",40)->nullable();  
            $table->text("footer_image_width",40)->nullable();
            $table->text("footer_image_height",40)->nullable();
            $table->text("footer_image_border_width",40)->nullable();
            $table->text("footer_image_border_color",40)->nullable(); 
            $table->text("footer_image_border_style",40)->nullable();
            $table->text("footer_image_border_radius",40)->nullable();
            $table->text("footer_image_box_width",40)->nullable();
            $table->text("footer_image_box_height",40)->nullable();
            $table->text("footer_image_box_margin",40)->nullable();
            $table->text("footer_box_image_background",40)->nullable();  
            $table->text("footer_box_image_color",40)->nullable();  
            $table->text("position_box_footer_align",40)->nullable(); 
            $table->text("footer_image_box_background",40)->nullable();  
            $table->text("footer_image_box_border_width",40)->nullable();
            $table->text("footer_image_box_border_style",40)->nullable();
            $table->text("footer_image_box_border_color",40)->nullable();  
            $table->text("footer_image_box_border_radius",40)->nullable();
            $table->boolean("footer_other_view")->default(0);  
            $table->text("align_other_footer",40)->nullable();  
            $table->text("other_background_footer",40)->nullable();  
            $table->text("footer_other_width",40)->nullable();
            $table->text("footer_other_border_radius",40)->nullable();
            $table->text("footer_other_border_width",40)->nullable();
            $table->text("footer_other_border_style",40)->nullable();
            $table->text("footer_other_border_color",40)->nullable(); 
            $table->text("footer_other_position",40)->nullable();
            $table->text("footer_other_top",40)->nullable();
            $table->text("footer_other_left",40)->nullable();
            $table->text("footer_other_right",40)->nullable();
            $table->text("footer_other_bottom",40)->nullable();
            $table->text("footer_tax_align",40)->nullable();
            $table->text("footer_tax_font_size",40)->nullable();
            $table->text("footer_tax_width",40)->nullable();
            $table->text("footer_tax_letter",40)->nullable();
            $table->text("footer_tax_border_width",40)->nullable();
            $table->text("footer_tax_border_style",40)->nullable();
            $table->text("footer_tax_border_color",40)->nullable();
            $table->text("footer_tax_padding_top",40)->nullable();
            $table->text("footer_tax_padding_left",40)->nullable();
            $table->text("footer_tax_padding_right",40)->nullable();
            $table->text("footer_tax_padding_bottom",40)->nullable();
            $table->text("footer_tax_position",40)->nullable();
            $table->text("footer_tax_top",40)->nullable();
            $table->text("footer_tax_bottom",40)->nullable();
            $table->text("footer_tax_left",40)->nullable();
            $table->text("footer_tax_right",40)->nullable();
            $table->text("footer_address_align",40)->nullable();
            $table->text("footer_address_font_size",40)->nullable();
            $table->text("footer_address_width",40)->nullable();
            $table->text("footer_address_letter",40)->nullable();
            $table->text("footer_address_border_width",40)->nullable();
            $table->text("footer_address_border_style",40)->nullable();
            $table->text("footer_address_border_color",40)->nullable();
            $table->text("footer_address_padding_top",40)->nullable();
            $table->text("footer_address_padding_left",40)->nullable();
            $table->text("footer_address_padding_right",40)->nullable();
            $table->text("footer_address_padding_bottom",40)->nullable();
            $table->text("footer_address_position",40)->nullable();
            $table->text("footer_address_top",40)->nullable();
            $table->text("footer_address_bottom",40)->nullable();
            $table->text("footer_address_left",40)->nullable();
            $table->text("footer_address_right",40)->nullable();
            $table->text("footer_bill_align",40)->nullable();
            $table->text("footer_bill_font_size",40)->nullable();
            $table->text("footer_bill_width",40)->nullable();
            $table->text("footer_bill_letter",40)->nullable();
            $table->text("footer_bill_border_width",40)->nullable();
            $table->text("footer_bill_border_style",40)->nullable();
            $table->text("footer_bill_border_color",40)->nullable();
            $table->text("footer_bill_padding_top",40)->nullable();
            $table->text("footer_bill_padding_left",40)->nullable();
            $table->text("footer_bill_padding_right",40)->nullable();
            $table->text("footer_bill_padding_bottom",40)->nullable();
            $table->text("footer_bill_position",40)->nullable();
            $table->text("footer_bill_top",40)->nullable();
            $table->text("footer_bill_bottom",40)->nullable();
            $table->text("footer_bill_left",40)->nullable();
            $table->text("footer_bill_right",40)->nullable();
            $table->boolean("footer_line_view")->default(0);
            $table->text("footer_line_height",40)->nullable();
            $table->text("footer_line_width",40)->nullable();
            $table->text("footer_line_color",40)->nullable();
            $table->text("footer_line_margin_top",40)->nullable();
            $table->text("footer_line_margin_bottom",40)->nullable();
            $table->text("footer_line_radius",40)->nullable();
            $table->text("footer_line_border_width",40)->nullable();
            $table->text("footer_line_border_style",40)->nullable();
            $table->text("footer_line_border_color",40)->nullable();
            $table->tinyInteger("page_number_view")->nullable()->default(0);

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
        Schema::dropIfExists('printer_footer_templates');
    }
}
