<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePrinterContentTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('printer_content_templates', function (Blueprint $table) {
            $table->increments('id');

            $table->integer("printer_template_id")->unsigned();  
            $table->foreign("printer_template_id")->on("printer_templates")->references("id")->onDelete("cascade")->onUpdate("cascade");  
            $table->boolean("top_table_section")->default(0);  
            $table->text("top_table_margin_bottom",40)->nullable();
            $table->text("top_table_width",40)->nullable();
            $table->text("top_table_td_border_width",40)->nullable();
            $table->text("top_table_td_border_style",40)->nullable();
            $table->text("top_table_td_border_color",40)->nullable();
            $table->text("left_top_table_width",40)->nullable();
            $table->text("left_top_table_text_align",40)->nullable();  
            $table->text("left_top_table_font_size",40)->nullable();
            $table->text("right_top_table_width",40)->nullable();
            $table->text("right_top_table_text_align",40)->nullable(); 
            $table->text("right_top_table_font_size",40)->nullable();
            $table->text("top_table_border_width",40)->nullable();
            $table->text("top_table_border_style",40)->nullable();
            $table->text("top_table_border_color",40)->nullable();
            $table->boolean("content_table_section")->default(0);
            $table->text("content_table_width",40)->nullable();
            $table->text("content_width",40)->nullable();
            $table->text("content_table_border_radius",40)->nullable();
            $table->text("footer_table",40)->nullable(); 
            $table->text("content_table_th_font_size",40)->nullable();
            $table->text("content_table_th_border_width",40)->nullable();
            $table->text("content_table_th_text_align",40)->nullable();
            $table->text("content_table_th_border_style",40)->nullable();
            $table->text("content_table_th_padding",40)->nullable();
            $table->text("content_table_th_border_color",40)->nullable();
            $table->text("content_table_td_font_size",40)->nullable();
            $table->text("content_table_td_border_width",40)->nullable();
            $table->text("content_table_td_text_align",40)->nullable();
            $table->text("content_table_td_border_style",40)->nullable();
            $table->text("content_table_td_padding",40)->nullable();
            $table->text("content_table_td_border_color",40)->nullable();
            $table->text("table_th_no",40)->nullable(); 
            $table->text("content_table_width_no",40)->nullable();
            $table->text("content_table_font_weight_no",40)->nullable();
            $table->text("content_table_td_no_font_size",40)->nullable();
            $table->text("content_table_td_no_text_align",40)->nullable();
            $table->text("table_th_name",40)->nullable(); 
            $table->text("content_table_width_name",40)->nullable();
            $table->text("content_table_font_weight_name",40)->nullable();
            $table->text("content_table_font_size_name",40)->nullable();
            $table->text("content_table_text_align_name",40)->nullable();
            $table->text("table_th_code",40)->nullable(); 
            $table->text("content_table_width_code",40)->nullable();
            $table->text("content_table_font_weight_code",40)->nullable();
            $table->text("content_table_td_code_font_size",40)->nullable();
            $table->text("content_table_text_align_code",40)->nullable();
            $table->text("table_th_img",40)->nullable(); 
            $table->text("content_table_width_img",40)->nullable();
            $table->text("content_table_font_weight_img",40)->nullable();
            $table->text("content_table_td_img_font_size",40)->nullable();
            $table->text("content_table_text_align_img",40)->nullable();
            $table->text("table_th_qty",40)->nullable(); 
            $table->text("content_table_width_qty",40)->nullable();
            $table->text("content_table_font_weight_qty",40)->nullable();
            $table->text("content_table_td_qty_font_size",40)->nullable();
            $table->text("content_table_td_qty_text_align",40)->nullable();
            $table->text("table_th_price",40)->nullable(); 
            $table->text("content_table_width_price",40)->nullable();
            $table->text("content_table_font_weight_price",40)->nullable();
            $table->text("content_table_td_price_font_size",40)->nullable();
            $table->text("content_table_td_price_text_align",40)->nullable();
            
            $table->text("table_th_no_named",40)->nullable(); 
            $table->text("table_th_name_named",40)->nullable();
            $table->text("table_th_code_named",40)->nullable();
            $table->text("table_th_img_named",40)->nullable();
            $table->text("table_th_qty_named",40)->nullable();
            $table->text("table_th_price_named",40)->nullable();
            $table->text("table_th_price_bdi_named",40)->nullable();
            $table->text("table_th_discount_named",40)->nullable();
            $table->text("table_th_price_ade_named",40)->nullable();
            $table->text("table_th_price_adi_named",40)->nullable();
            $table->text("table_th_subtotal_named",40)->nullable();
            $table->text("table_th_price_bdi",40)->nullable();
            
            $table->text("content_table_width_price_bdi",40)->nullable(); 
            $table->text("content_table_font_weight_price_bdi",40)->nullable();
            $table->text("content_table_td_price_bdi_font_size",40)->nullable();
            $table->text("content_table_td_price_bdi_text_align",40)->nullable();
            
            $table->text("table_th_discount",40)->nullable();
            $table->text("content_table_width_discount",40)->nullable();
            $table->text("content_table_font_weight_discount",40)->nullable();
            $table->text("content_table_td_discount_font_size",40)->nullable();
            $table->text("content_table_td_discount_text_align",40)->nullable();
            
            $table->text("table_th_price_ade",40)->nullable();
            $table->text("content_table_width_price_ade",40)->nullable();
            $table->text("content_table_font_weight_price_ade",40)->nullable();
            $table->text("content_table_td_price_ade_font_size",40)->nullable();
            $table->text("content_table_td_price_ade_text_align",40)->nullable();
            
            $table->text("table_th_price_adi",40)->nullable();
            $table->text("content_table_width_price_adi",40)->nullable();
            $table->text("content_table_font_weight_price_adi",40)->nullable();
            $table->text("content_table_td_price_adi_font_size",40)->nullable();
            $table->text("content_table_td_price_adi_text_align",40)->nullable();
            
            $table->text("content_table_td_code_text_align",40)->nullable();
            $table->text("content_table_td_img_text_align",40)->nullable();
            
            $table->text("table_th_subtotal",40)->nullable();
            $table->text("content_table_width_subtotal",40)->nullable();
            $table->text("content_table_font_weight_subtotal",40)->nullable();
            $table->text("content_table_td_subtotal_font_size",40)->nullable();
            $table->text("content_table_td_subtotal_text_align",40)->nullable();
            
            $table->boolean("bottom_table_section")->default(0);
            $table->text("left_bottom_table_width",40)->nullable();
            $table->text("left_bottom_table_td_bor_width",40)->nullable();
            $table->text("left_bottom_table_text_align",40)->nullable(); 
            $table->text("left_bottom_table_td_bor_style",40)->nullable();
            $table->text("left_bottom_table_font_size",40)->nullable();
            $table->text("left_bottom_table_td_bor_color",40)->nullable();
            $table->text("right_bottom_table_width",40)->nullable();
            $table->text("right_bottom_table_td_bor_width",40)->nullable();
            $table->text("right_bottom_table_text_align",40)->nullable(); 
            $table->text("right_bottom_table_td_bor_style",40)->nullable();
            $table->text("right_bottom_table_font_size",40)->nullable();
            $table->text("right_bottom_table_td_bor_color",40)->nullable();
            $table->text("bill_table_info_width",40)->nullable();
            $table->text("bill_table_margin_bottom",40)->nullable();
            $table->text("bill_table_info_border_width",40)->nullable();
            $table->text("bill_table_margin_top",40)->nullable();
            $table->text("bill_table_info_border_style",40)->nullable();
            $table->text("bill_table_info_border_color",40)->nullable();
            $table->text("bill_table_border_width",40)->nullable();
            $table->text("bill_table_border_style",40)->nullable();
            $table->text("bill_table_border_color",40)->nullable();
            $table->text("bill_table_left_td_width",40)->nullable();
            $table->text("bill_table_left_td_font_size",40)->nullable();
            $table->text("bill_table_left_td_weight",40)->nullable();
            $table->text("bill_table_left_td_text_align",40)->nullable();
            $table->text("bill_table_left_td_border_width",40)->nullable();
            $table->text("bill_table_left_td_padding_left",40)->nullable();
            $table->text("bill_table_left_td_border_style",40)->nullable();
            $table->text("bill_table_left_td_border_color",40)->nullable();
            $table->text("bill_table_right_td_width",40)->nullable();
            $table->text("bill_table_right_td_font_size",40)->nullable();
            $table->text("bill_table_right_td_weight",40)->nullable();
            $table->text("bill_table_right_td_text_align",40)->nullable();
            $table->text("bill_table_right_td_border_width",40)->nullable();
            $table->text("bill_table_right_td_padding_left",40)->nullable();
            $table->text("bill_table_right_td_border_style",40)->nullable();
            $table->text("bill_table_right_td_border_color",40)->nullable();
            $table->text("line_bill_table_width",40)->nullable();
            $table->text("line_bill_table_height",40)->nullable();
            $table->text("line_bill_table_color",40)->nullable();
            $table->text("line_bill_table_border_width",40)->nullable();
            $table->text("line_bill_table_border_style",40)->nullable();
            $table->text("line_bill_table_border_color",40)->nullable();
            $table->text("line_bill_table_td_margin_left",40)->nullable();
            
            $table->tinyInteger("if_discount_zero")->nullable();
            $table->tinyInteger("currency_in_row")->nullable();
            $table->text("background_color_invoice_info",40)->nullable();
            $table->text("color_invoice_info",40)->nullable();
            $table->text("padding_invoice_info",40)->nullable();
            $table->tinyInteger("bold_left_invoice_info_number")->nullable();
            $table->tinyInteger("bold_left_invoice_info_project")->nullable();
            $table->tinyInteger("bold_left_invoice_info_date")->nullable();
            $table->text("bold_left_invoice_info",40)->nullable();
            
            $table->text("bold_left_invoice_info_br_style",40)->nullable();
            $table->text("bold_left_invoice_info_br_color",40)->nullable();
            $table->text("bold_left_invoice_info_br_width",40)->nullable();
            $table->text("left_invoice_info",40)->nullable();
            $table->text("class_width_left",40)->nullable();
            $table->text("class_width_right",40)->nullable();
            $table->text("bold_left_invoice_info_text_align",40)->nullable();
            
            $table->tinyInteger("bold_left_invoice_info_customer_number")->nullable();
            $table->tinyInteger("bold_left_invoice_info_customer_address")->nullable();
            $table->tinyInteger("bold_left_invoice_info_customer_mobile")->nullable();
            $table->tinyInteger("bold_left_invoice_info_customer_tax")->nullable();

            $table->text("bold_right_invoice_info",40)->nullable();
            $table->text("bold_right_invoice_info_br_style",40)->nullable();
            $table->text("bold_right_invoice_info_br_color",40)->nullable();
            $table->text("bold_right_invoice_info_br_width",40)->nullable();
            
            $table->text("right_invoice_info",40)->nullable();
            $table->text("class_width_left_right",40)->nullable();
            $table->text("class_width_right_right",40)->nullable();
            $table->text("bold_right_invoice_info_text_align",40)->nullable();

            $table->tinyInteger("bill_invoice_info_down_subtotal")->nullable();
            $table->tinyInteger("bill_invoice_info_down_discount")->nullable();
            $table->tinyInteger("bill_invoice_info_down_subtotal_after_dis")->nullable();
            $table->tinyInteger("bill_invoice_info_down_vat")->nullable();
            $table->tinyInteger("repeat_content_top")->default(0)->nullable();
            
            $table->text("customer_name",40)->nullable();
            $table->text("invoice_no",40)->nullable();
            $table->text("project_no",40)->nullable();
            $table->text("date_name",40)->nullable();
            $table->text("address_name",40)->nullable();
            $table->text("mobile_name",40)->nullable();
            $table->text("tax_name",40)->nullable();

            $table->tinyInteger("choose_product_description")->default(0)->nullable();
            $table->tinyInteger("show_customer_signature")->default(0)->nullable();
            $table->tinyInteger("show_quotation_terms")->default(0)->nullable();
             

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
        Schema::dropIfExists('printer_content_templates');
    }
}
