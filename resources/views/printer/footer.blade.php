<?php
    /**
     * ******************************* *
     * Here Set Variables For Settings *
     * ******************************* *
     */
    

    //.................................style
if(isset($print_footer)){ 
    $footer_align                   = $print_footer["align_text_footer"];        /**   محاذات العناون*/
    $footer_style_box               = $print_footer["style_footer"];         /**   شكل العنوان  */
    $footer_font_size               = $print_footer["footer_font_size"];          /**   خط العنوان */
    $footer_font_weight             = $print_footer["footer_font_weight"];
    $footer_width_header            = $print_footer["footer_width"];           /**   عرض الإطار العنوان  */
    
    $footer_border_width            = $print_footer["footer_border_width"];          /**   عرض الإطار العنوان  */
    $footer_border_style            = $print_footer["footer_border_style"];        /**   شكل إطار العنوان  */
    $footer_border_color            = $print_footer["footer_border_color"];        /**   لون إطار العنوان  */
    
    $footer_padding_right           = $print_footer["footer_padding_right"];            /**   المحيط التوسع  */
    $footer_padding_left            = $print_footer["footer_padding_left"];              /**   المحيط التوسع   */
    $footer_padding_top             = $print_footer["footer_padding_top"];                 /**   المحيط التوسع   */
    $footer_padding_bottom          = $print_footer["footer_padding_bottom"];          /**   المحيط التوسع   */
    
    $footer_header_text_position    = $print_footer["footer_position"];          /**   أول حرف كبير   */
    $footer_capital_text            = $print_footer["footer_style_letter"];  /**   أول حرف كبير   */
    
    $footer_header_text_top         = $print_footer["footer_top"];           /**   أول حرف كبير   */
    $footer_header_text_left        = $print_footer["footer_left"];         /**   أول حرف كبير   */
    $footer_header_text_right       = $print_footer["footer_right"];      /**   أول حرف كبير   */
    $footer_header_text_bottom      = $print_footer["footer_bottom"];   /**   أول حرف كبير   */
    
    // table
    $footer_table_width           = $print_footer["footer_table_width"];         /**   محاذات العنصر الأصلي  */
    $footer_border_width_table    = "0px";          /**   عرض الإطار العنوان  */
    $footer_border_style_table    = "solid";        /**   شكل إطار العنوان  */
    $footer_border_color_table    = "black";        /**   لون إطار العنوان  */
    $footer_table_background      = $print_footer["footer_table_color"];             /**   عرض الإطار العنوان  */
    $footer_border_radius_table   = $print_footer["footer_table_radius"];          /**   عرض الإطار العنوان  */
   
    // boxes
    $align_other_footer             = $print_footer["align_other_footer"];
    $footer_border_width_box_tax    = $print_footer["footer_other_border_width"];          /**   عرض الإطار العنوان  */
    $footer_border_style_box_tax    = $print_footer["footer_other_border_style"];        /**   شكل إطار العنوان  */
    $footer_border_color_box_tax    = $print_footer["footer_other_border_color"];        /**   لون إطار العنوان  */
    $footer_box_position            = $print_footer["footer_other_position"];     /**   محاذات العنصر الأصلي  */
    $footer_box_tax_top             = $print_footer["footer_other_top"];        /**   لون إطار العنوان  */
    $footer_box_tax_left            = $print_footer["footer_other_left"];        /**   لون إطار العنوان  */
    $footer_box_tax_right           = $print_footer["footer_other_right"];        /**   لون إطار العنوان  */
    $footer_box_tax_bottom          = $print_footer["footer_other_bottom"];        /**   لون إطار العنوان  */
    

    // tax
    $footer_align_tax               = $print_footer["footer_tax_align"];       /**   محاذات العناون*/
    $footer_font_size_tax           = $print_footer["footer_tax_font_size"];         /**   خط العنوان */
    $footer_width_tax               = $print_footer["footer_tax_width"];           /**   عرض الإطار العنوان  */
    $footer_border_width_tax        = $print_footer["footer_tax_border_width"];          /**   عرض الإطار العنوان  */
    $footer_border_style_tax        = $print_footer["footer_tax_border_style"];        /**   شكل إطار العنوان  */
    $footer_border_color_tax        = $print_footer["footer_tax_border_color"];        /**   لون إطار العنوان  */
    $footer_padding_right_tax       = $print_footer["footer_tax_right"];          /**   المحيط التوسع  */
    $footer_padding_left_tax        = $print_footer["footer_tax_left"];          /**          /**   المحيط التوسع   */
    $footer_padding_top_tax         = $print_footer["footer_tax_top"];          /**          /**   المحيط التوسع   */
    $footer_padding_bottom_tax      = $print_footer["footer_tax_bottom"];          /**   المحيط التوسع   */
    $footer_capital_text_tax        = $print_footer["footer_tax_letter"];    /**   أول حرف كبير   */
    $footer_position_tax            = $print_footer["footer_tax_position"];     /**   مكان توضع العنصر  */
    $footer_position_top_tax        = $print_footer["footer_tax_padding_top"];        /**   محاذات العنصر العائم  */
    $footer_position_bottom_tax     = $print_footer["footer_tax_padding_bottom"];          /**   محاذات العنصر العائم  */
    $footer_position_left_tax       = $print_footer["footer_tax_padding_left"];         /**   محاذات العنصر العائم  */
    $footer_position_right_tax      = $print_footer["footer_tax_padding_right"];          /**   محاذات العنصر العائم  */
    
    // address
    $footer_align_address             = $print_footer["footer_address_align"];        /**   محاذات العناون*/
    $footer_font_size_address         = $print_footer["footer_address_font_size"];         /**   خط العنوان */
    $footer_width_address             = $print_footer["footer_address_width"];         /**   عرض الإطار العنوان  */
    $footer_border_width_address      = $print_footer["footer_address_border_width"];          /**   عرض الإطار العنوان  */
    $footer_border_style_address      = $print_footer["footer_address_border_style"];        /**   شكل إطار العنوان  */
    $footer_border_color_address      = $print_footer["footer_address_border_color"];        /**   لون إطار العنوان  */
    $footer_padding_right_address     = $print_footer["footer_address_right"];          /**   المحيط التوسع  */
    $footer_padding_left_address      = $print_footer["footer_address_left"];          /**   المحيط التوسع   */
    $footer_padding_top_address       = $print_footer["footer_address_top"];          /**   المحيط التوسع   */
    $footer_padding_bottom_address    = $print_footer["footer_address_bottom"];          /**   المحيط التوسع   */
    $footer_capital_text_address      = $print_footer["footer_address_letter"];    /**   أول حرف كبير   */
    $footer_position_address          = $print_footer["footer_address_position"];     /**   مكان توضع العنصر  */
    $footer_position_top_address      = $print_footer["footer_address_padding_top"];        /**   محاذات العنصر العائم  */
    $footer_position_bottom_address   = $print_footer["footer_address_padding_bottom"];          /**   محاذات العنصر العائم  */
    $footer_position_left_address     = $print_footer["footer_address_padding_left"];         /**   محاذات العنصر العائم  */
    $footer_position_right_address    = $print_footer["footer_address_padding_right"];          /**   محاذات العنصر العائم  */
    
    // bill name
    $footer_align_bill             = $print_footer["footer_bill_align"];         /**   محاذات العناون*/
    $footer_font_size_bill         = $print_footer["footer_bill_font_size"];         /**   خط العنوان */
    $footer_width_bill             = $print_footer["footer_bill_width"];           /**   عرض الإطار العنوان  */
    $footer_border_width_bill      = $print_footer["footer_bill_border_width"];          /**   عرض الإطار العنوان  */
    $footer_border_style_bill      = $print_footer["footer_bill_border_style"];        /**   شكل إطار العنوان  */
    $footer_border_color_bill      = $print_footer["footer_bill_border_color"];        /**   لون إطار العنوان  */
    $footer_padding_right_bill     = $print_footer["footer_bill_right"];          /**   المحيط التوسع  */
    $footer_padding_left_bill      = $print_footer["footer_bill_left"];          /**   المحيط التوسع   */
    $footer_padding_top_bill       = $print_footer["footer_bill_top"];          /**   المحيط التوسع   */
    $footer_padding_bottom_bill    = $print_footer["footer_bill_bottom"];          /**   المحيط التوسع   */
    $footer_capital_text_bill      = $print_footer["footer_bill_letter"];    /**   أول حرف كبير   */
    $footer_position_bill          = $print_footer["footer_bill_position"];     /**   مكان توضع العنصر  */
    $footer_position_top_bill      = $print_footer["footer_bill_padding_top"];         /**   محاذات العنصر العائم  */
    $footer_position_bottom_bill   = $print_footer["footer_bill_padding_bottom"];          /**   محاذات العنصر العائم  */
    $footer_position_left_bill     = $print_footer["footer_bill_padding_left"];         /**   محاذات العنصر العائم  */
    $footer_position_right_bill    = $print_footer["footer_bill_padding_right"];          /**   محاذات العنصر العائم  */
    
    // image 
    $footer_enable_img_align        = $print_footer["align_image_footer"];  
    $footer_position_img_align      = $print_footer["position_img_footer"];  
    $footer_img_width               = $print_footer["footer_image_width"];
    $footer_img_height              = $print_footer["footer_image_height"];
    $footer_border_width_img        = $print_footer["footer_image_border_width"];          /**   عرض الإطار العنوان  */
    $footer_background_img          = $print_footer["footer_box_image_background"];       /**   عرض الإطار العنوان  */
    $footer_border_style_img        = $print_footer["footer_image_border_style"];        /**   شكل إطار العنوان  */
    $footer_border_color_img        = $print_footer["footer_image_border_color"];        /**   لون إطار العنوان  */
    $footer_border_radius_img       = $print_footer["footer_image_border_radius"];         /**   لون إطار العنوان  */
    $footer_img_box_color           = $print_footer["footer_box_image_color"];
    $footer_img_box_height          = $print_footer["footer_image_box_height"];
    
    // image box
    
    $footer_enable_img_box           = $print_footer["footer_image_view"]; 
    $footer_enable_img               = $print_footer["footer_image_view"]; 
    $footer_position_box_align       = $print_footer["position_box_footer_align"];
    $footer_img_box_width            = $print_footer["footer_image_box_width"];                                /**   لون إطار العنوان  */
    $footer_img_box_margin           = $print_footer["footer_image_box_margin"];                             /**   لون إطار العنوان  */
    $footer_border_width_img_box     = $print_footer["footer_image_box_border_width"];         /**   عرض الإطار العنوان  */
    $footer_border_style_img_box     = $print_footer["footer_image_box_border_style"];       /**   شكل إطار العنوان  */
    $footer_border_color_img_box     = $print_footer["footer_image_box_border_color"];        /**   لون إطار العنوان  */
    $footer_border_radius_img_box    = $print_footer["footer_image_box_border_radius"];                   /**   لون إطار العنوان  */
    $footer_background_img_box       = $print_footer["footer_image_box_background"];                      /**   عرض الإطار العنوان  */
     


    // header box
    $footer_enable_header_box           = $print_footer["footer_view"]; 
    $footer_header_box_width            = $print_footer["footer_box_width"];         /**   لون إطار العنوان  */
    $footer_border_width_header_box     = $print_footer["footer_box_border_width"];          /**   عرض الإطار العنوان  */
    $footer_border_style_header_box     = $print_footer["footer_box_border_style"];        /**   شكل إطار العنوان  */
    $footer_border_color_header_box     = $print_footer["footer_box_border_color"];        /**   لون إطار العنوان  */
    $footer_border_radius_header_box    = $print_footer["footer_box_border_radius"];         /**   لون إطار العنوان  */
    $footer_background_header_box       = $print_footer["footer_box_background"];       /**   عرض الإطار العنوان  */
    
    // other box
    $footer_enable_other_box           = $print_footer["footer_other_view"];          /**   لون إطار العنوان  */
    $footer_other_box_width            = $print_footer["footer_other_width"];  /**  لون إطار العنوان  */
    $footer_border_width_other_box     = $print_footer["footer_other_border_width"]; /**  عرض الإطار العنوان  */
    $footer_border_style_other_box     = $print_footer["footer_other_border_style"];        /**   شكل إطار العنوان  */
    $footer_border_color_other_box     = $print_footer["footer_other_border_color"];        /**   لون إطار العنوان  */
    $footer_border_radius_other_box    = $print_footer["footer_other_border_radius"];         /** إطار العنوان  */
    $footer_background_other_box       = $print_footer["other_background_footer"];       /**   عرض الإطار العنوان  */


    // rows lines 
    $footer_row_line_enable            = $print_footer["footer_line_view"];
    $footer_row_line_width             = $print_footer["footer_line_width"];
    $footer_row_line_height            = $print_footer["footer_line_height"];
    $footer_row_line_color             = $print_footer["footer_line_color"];
    $footer_row_line_radius            = $print_footer["footer_line_radius"];
    $footer_row_line_border_width      = $print_footer["footer_line_border_width"];
    $footer_row_line_border_style      = $print_footer["footer_line_border_style"];
    $footer_row_line_border_color      = $print_footer["footer_line_border_color"];
    $footer_row_line_margin_top        = $print_footer["footer_line_margin_top"];
    $footer_row_line_margin_bottom     = $print_footer["footer_line_margin_bottom"];  
    // .................................content

    $footer_top_align                   = "center";
    $footer_top_width                   = "100%";
    $footer_header_text                 =   $print_footer["left_footer_title"]  ;
    $footer_tax_text                    =   $print_footer["center_top_footer_title"];
    $footer_address_text                =   $print_footer["center_middle_footer_title"];
    $footer_invoice_text                =   $print_footer["center_last_footer_title"];
    $footer_img_url                     =   $print_footer["footer_image_url"] ;
    
    $page_number_text_align             = "center";
    $page_number_font_size              = "16px";
    $page_number_font_weight            = "800";
    $page_number_font_color             = "black";
    $page_number_font_style             = "uppercase";
    $page_number_view                   = $print_footer["page_number_view"];
}else{  
    $check_type              = (isset($edit_type) || (isset($array["edit_type"]) && $array["edit_type"] != null ));
     
     if((isset($array["edit_type"]) && $array["edit_type"] != null )){
         $PrinterFooterTemplate  = \App\Models\PrinterFooterTemplate::where("printer_template_id",$array["edit_type"])->first();
         $PrinterTemplateContain = \App\Models\PrinterTemplateContain::where("printer_templates_id",$array["edit_type"])->first();
     }
  
    // footer
    $footer_align                   = (isset($array["footer_text_align"]))?$array["footer_text_align"]:(($check_type)?$PrinterFooterTemplate->align_text_footer:"left");        /**   محاذات العناون*/
    $footer_style_box               = (isset($array["footer_style"]))?$array["footer_style"]:(($check_type)?$PrinterFooterTemplate->style_footer:"table");         /**   شكل العنوان  */
    $footer_font_size               = (isset($array["footer_font_size"]))?$array["footer_font_size"]:(($check_type)?$PrinterFooterTemplate->footer_font_size:"22px");          /**   خط العنوان */
    $footer_font_weight             = (isset($array["footer_weight"]))?$array["footer_weight"]:(($check_type)?$PrinterFooterTemplate->footer_font_weight:"300");
    $footer_width_header            = (isset($array["footer_width"]))?$array["footer_width"]:(($check_type)?$PrinterFooterTemplate->footer_width:"100%");           /**   عرض الإطار العنوان  */
    
    $footer_border_width            = (isset($array["footer_border_width"]))?$array["footer_border_width"]:(($check_type)?$PrinterFooterTemplate->footer_border_width:"0px");          /**   عرض الإطار العنوان  */
    $footer_border_style            = (isset($array["footer_border_style"]))?$array["footer_border_style"]:(($check_type)?$PrinterFooterTemplate->footer_border_style:"solid");        /**   شكل إطار العنوان  */
    $footer_border_color            = (isset($array["footer_border_color"]))?$array["footer_border_color"]:(($check_type)?$PrinterFooterTemplate->footer_border_color:"transparent");        /**   لون إطار العنوان  */
    
    $footer_padding_right           = (isset($array["footer_padding_right"]))?$array["footer_padding_right"]:(($check_type)?$PrinterFooterTemplate->footer_padding_right:"0px");            /**   المحيط التوسع  */
    $footer_padding_left            = (isset($array["footer_padding_left"]))?$array["footer_padding_left"]:(($check_type)?$PrinterFooterTemplate->footer_padding_left:"0px");              /**   المحيط التوسع   */
    $footer_padding_top             = (isset($array["footer_padding_top"]))?$array["footer_padding_top"]:(($check_type)?$PrinterFooterTemplate->footer_padding_top:"0px");                 /**   المحيط التوسع   */
    $footer_padding_bottom          = (isset($array["footer_padding_bottom"]))?$array["footer_padding_bottom"]:(($check_type)?$PrinterFooterTemplate->footer_padding_bottom:"0px");          /**   المحيط التوسع   */
    
    $footer_header_text_position    = (isset($array["footer_position"]))?$array["footer_position"]:(($check_type)?$PrinterFooterTemplate->footer_position:"relative");          /**   أول حرف كبير   */
    $footer_capital_text            = (isset($array["footer_style_letter"]))?$array["footer_style_letter"]:(($check_type)?$PrinterFooterTemplate->footer_style_letter:"capitalize");  /**   أول حرف كبير   */
    
    $footer_header_text_top         = (isset($array["footer_top"]))?$array["footer_top"]:(($check_type)?$PrinterFooterTemplate->footer_top:"0px");           /**   أول حرف كبير   */
    $footer_header_text_left        = (isset($array["footer_left"]))?$array["footer_left"]:(($check_type)?$PrinterFooterTemplate->footer_left:"0px");         /**   أول حرف كبير   */
    $footer_header_text_right       = (isset($array["footer_right"]))?$array["footer_right"]:(($check_type)?$PrinterFooterTemplate->footer_right:"0px");      /**   أول حرف كبير   */
    $footer_header_text_bottom      = (isset($array["footer_bottom"]))?$array["footer_bottom"]:(($check_type)?$PrinterFooterTemplate->footer_bottom:"0px");   /**   أول حرف كبير   */
    
    // table
    $footer_table_width           = (isset($array["footer_table_width"]))?$array["footer_table_width"]:(($check_type)?$PrinterFooterTemplate->footer_table_width:"100%");         /**   محاذات العنصر الأصلي  */
    $footer_border_width_table    = "0px";          /**   عرض الإطار العنوان  */
    $footer_border_style_table    = "solid";        /**   شكل إطار العنوان  */
    $footer_border_color_table    = "black";        /**   لون إطار العنوان  */
    $footer_table_background      = (isset($array["footer_table_color"]))?$array["footer_table_color"]:(($check_type)?$PrinterFooterTemplate->footer_table_color: "transparent");             /**   عرض الإطار العنوان  */
    $footer_border_radius_table   = (isset($array["footer_table_radius"]))?$array["footer_table_radius"]:(($check_type)?$PrinterFooterTemplate->footer_table_radius:  "0px");          /**   عرض الإطار العنوان  */
   
    // boxes
    $align_other_footer             = (isset($array["align_other_footer"]))?$array["align_other_footer"]:(($check_type)?$PrinterFooterTemplate->align_other_footer:"center");
    $footer_border_width_box_tax    = (isset($array["footer_other_border_width"]))?$array["footer_other_border_width"]:(($check_type)?$PrinterFooterTemplate->footer_other_border_width:"0px");          /**   عرض الإطار العنوان  */
    $footer_border_style_box_tax    = (isset($array["footer_other_border_style"]))?$array["footer_other_border_style"]:(($check_type)?$PrinterFooterTemplate->footer_other_border_style:"solid");        /**   شكل إطار العنوان  */
    $footer_border_color_box_tax    = (isset($array["footer_other_border_color"]))?$array["footer_other_border_color"]:(($check_type)?$PrinterFooterTemplate->footer_other_border_color:"black");        /**   لون إطار العنوان  */
    $footer_box_position            = (isset($array["footer_other_position"]))?$array["footer_other_position"]:(($check_type)?$PrinterFooterTemplate->footer_other_position:"relative");     /**   محاذات العنصر الأصلي  */
    $footer_box_tax_top             = (isset($array["footer_other_top"]))?$array["footer_other_top"]:(($check_type)?$PrinterFooterTemplate->footer_other_top:"0px");        /**   لون إطار العنوان  */
    $footer_box_tax_left            = (isset($array["footer_other_left"]))?$array["footer_other_left"]:(($check_type)?$PrinterFooterTemplate->footer_other_left:"0px");        /**   لون إطار العنوان  */
    $footer_box_tax_right           = (isset($array["footer_other_right"]))?$array["footer_other_right"]:(($check_type)?$PrinterFooterTemplate->footer_other_right:"0px");        /**   لون إطار العنوان  */
    $footer_box_tax_bottom          = (isset($array["footer_other_bottom"]))?$array["footer_other_bottom"]:(($check_type)?$PrinterFooterTemplate->footer_other_bottom:"0px");        /**   لون إطار العنوان  */
    

    // tax
    $footer_align_tax               = (isset($array["footer_tax_align"]))?$array["footer_tax_align"]:(($check_type)?$PrinterFooterTemplate->footer_tax_align:"center");       /**   محاذات العناون*/
    $footer_font_size_tax           = (isset($array["footer_tax_font_size"]))?$array["footer_tax_font_size"]:(($check_type)?$PrinterFooterTemplate->footer_tax_font_size:"22px");         /**   خط العنوان */
    $footer_width_tax               = (isset($array["footer_tax_width"]))?$array["footer_tax_width"]:(($check_type)?$PrinterFooterTemplate->footer_tax_width:"100%");           /**   عرض الإطار العنوان  */
    $footer_border_width_tax        = (isset($array["footer_tax_border_width"]))?$array["footer_tax_border_width"]:(($check_type)?$PrinterFooterTemplate->footer_tax_border_width:"0px");          /**   عرض الإطار العنوان  */
    $footer_border_style_tax        = (isset($array["footer_tax_border_style"]))?$array["footer_tax_border_style"]:(($check_type)?$PrinterFooterTemplate->footer_tax_border_style:"solid");        /**   شكل إطار العنوان  */
    $footer_border_color_tax        = (isset($array["footer_tax_border_color"]))?$array["footer_tax_border_color"]:(($check_type)?$PrinterFooterTemplate->footer_tax_border_color:"transparent");        /**   لون إطار العنوان  */
    $footer_padding_right_tax       = (isset($array["footer_tax_right"]))?$array["footer_tax_right"]:(($check_type)?$PrinterFooterTemplate->footer_tax_right:"0px");          /**   المحيط التوسع  */
    $footer_padding_left_tax        = (isset($array["footer_tax_left"]))?$array["footer_tax_left"]:(($check_type)?$PrinterFooterTemplate->footer_tax_left:"0px");          /**          /**   المحيط التوسع   */
    $footer_padding_top_tax         = (isset($array["footer_tax_top"]))?$array["footer_tax_top"]:(($check_type)?$PrinterFooterTemplate->footer_tax_top:"0px");          /**          /**   المحيط التوسع   */
    $footer_padding_bottom_tax      = (isset($array["footer_tax_bottom"]))?$array["footer_tax_bottom"]:(($check_type)?$PrinterFooterTemplate->footer_tax_bottom:"0px");          /**   المحيط التوسع   */
    $footer_capital_text_tax        = (isset($array["footer_tax_letter"]))?$array["footer_tax_letter"]:(($check_type)?$PrinterFooterTemplate->footer_tax_letter:"captalize");    /**   أول حرف كبير   */
    $footer_position_tax            = (isset($array["footer_tax_position"]))?$array["footer_tax_position"]:(($check_type)?$PrinterFooterTemplate->footer_tax_position:"relative");     /**   مكان توضع العنصر  */
    $footer_position_top_tax        = (isset($array["footer_tax_padding_top"]))?$array["footer_tax_padding_top"]:(($check_type)?$PrinterFooterTemplate->footer_tax_padding_top:"0px");        /**   محاذات العنصر العائم  */
    $footer_position_bottom_tax     = (isset($array["footer_tax_padding_bottom"]))?$array["footer_tax_padding_bottom"]:(($check_type)?$PrinterFooterTemplate->footer_tax_padding_bottom:"0px");          /**   محاذات العنصر العائم  */
    $footer_position_left_tax       = (isset($array["footer_tax_padding_left"]))?$array["footer_tax_padding_left"]:(($check_type)?$PrinterFooterTemplate->footer_tax_padding_left:"0px");         /**   محاذات العنصر العائم  */
    $footer_position_right_tax      = (isset($array["footer_tax_padding_right"]))?$array["footer_tax_padding_right"]:(($check_type)?$PrinterFooterTemplate->footer_tax_padding_right:"0px");          /**   محاذات العنصر العائم  */
    
    // address
    $footer_align_address             = (isset($array["footer_address_align"]))?$array["footer_address_align"]:(($check_type)?$PrinterFooterTemplate->footer_address_align:"center");        /**   محاذات العناون*/
    $footer_font_size_address         = (isset($array["footer_address_font_size"]))?$array["footer_address_font_size"]:(($check_type)?$PrinterFooterTemplate->footer_address_font_size:"22px");         /**   خط العنوان */
    $footer_width_address             = (isset($array["footer_address_width"]))?$array["footer_address_width"]:(($check_type)?$PrinterFooterTemplate->footer_address_width:"100%");         /**   عرض الإطار العنوان  */
    $footer_border_width_address      = (isset($array["footer_address_border_width"]))?$array["footer_address_border_width"]:(($check_type)?$PrinterFooterTemplate->footer_address_border_width:"0px");          /**   عرض الإطار العنوان  */
    $footer_border_style_address      = (isset($array["footer_address_border_style"]))?$array["footer_address_border_style"]:(($check_type)?$PrinterFooterTemplate->footer_address_border_style:"solid");        /**   شكل إطار العنوان  */
    $footer_border_color_address      = (isset($array["footer_address_border_color"]))?$array["footer_address_border_color"]:(($check_type)?$PrinterFooterTemplate->footer_address_border_color:"transparent");        /**   لون إطار العنوان  */
    $footer_padding_right_address     = (isset($array["footer_address_right"]))?$array["footer_address_right"]:(($check_type)?$PrinterFooterTemplate->footer_address_right:"0px");          /**   المحيط التوسع  */
    $footer_padding_left_address      = (isset($array["footer_address_left"]))?$array["footer_address_left"]:(($check_type)?$PrinterFooterTemplate->footer_address_left:"0px");          /**   المحيط التوسع   */
    $footer_padding_top_address       = (isset($array["footer_address_top"]))?$array["footer_address_top"]:(($check_type)?$PrinterFooterTemplate->footer_address_top:"0px");          /**   المحيط التوسع   */
    $footer_padding_bottom_address    = (isset($array["footer_address_bottom"]))?$array["footer_address_bottom"]:(($check_type)?$PrinterFooterTemplate->footer_address_bottom:"0px");          /**   المحيط التوسع   */
    $footer_capital_text_address      = (isset($array["footer_address_letter"]))?$array["footer_address_letter"]:(($check_type)?$PrinterFooterTemplate->footer_address_letter:"captalize");    /**   أول حرف كبير   */
    $footer_position_address          = (isset($array["footer_address_position"]))?$array["footer_address_position"]:(($check_type)?$PrinterFooterTemplate->footer_address_position:"relative");     /**   مكان توضع العنصر  */
    $footer_position_top_address      = (isset($array["footer_address_padding_top"]))?$array["footer_address_padding_top"]:(($check_type)?$PrinterFooterTemplate->footer_address_padding_top:"0px");        /**   محاذات العنصر العائم  */
    $footer_position_bottom_address   = (isset($array["footer_address_padding_bottom"]))?$array["footer_address_padding_bottom"]:(($check_type)?$PrinterFooterTemplate->footer_address_padding_bottom:"0px");          /**   محاذات العنصر العائم  */
    $footer_position_left_address     = (isset($array["footer_address_padding_left"]))?$array["footer_address_padding_left"]:(($check_type)?$PrinterFooterTemplate->footer_address_padding_left:"0px");         /**   محاذات العنصر العائم  */
    $footer_position_right_address    = (isset($array["footer_address_padding_right"]))?$array["footer_address_padding_right"]:(($check_type)?$PrinterFooterTemplate->footer_address_padding_right:"0px");          /**   محاذات العنصر العائم  */
    
    // bill name
    $footer_align_bill             = (isset($array["footer_bill_align"]))?$array["footer_bill_align"]:(($check_type)?$PrinterFooterTemplate->footer_bill_align:"center");         /**   محاذات العناون*/
    $footer_font_size_bill         = (isset($array["footer_bill_font_size"]))?$array["footer_bill_font_size"]:(($check_type)?$PrinterFooterTemplate->footer_bill_font_size:"22px");         /**   خط العنوان */
    $footer_width_bill             = (isset($array["footer_bill_width"]))?$array["footer_bill_width"]:(($check_type)?$PrinterFooterTemplate->footer_bill_width:"100%");           /**   عرض الإطار العنوان  */
    $footer_border_width_bill      = (isset($array["footer_bill_border_width"]))?$array["footer_bill_border_width"]:(($check_type)?$PrinterFooterTemplate->footer_bill_border_width:"0px");          /**   عرض الإطار العنوان  */
    $footer_border_style_bill      = (isset($array["footer_bill_border_style"]))?$array["footer_bill_border_style"]:(($check_type)?$PrinterFooterTemplate->footer_bill_border_style:"solid");        /**   شكل إطار العنوان  */
    $footer_border_color_bill      = (isset($array["footer_bill_border_color"]))?$array["footer_bill_border_color"]:(($check_type)?$PrinterFooterTemplate->footer_bill_border_color:"transparent");        /**   لون إطار العنوان  */
    $footer_padding_right_bill     = (isset($array["footer_bill_right"]))?$array["footer_bill_right"]:(($check_type)?$PrinterFooterTemplate->footer_bill_right:"0px");          /**   المحيط التوسع  */
    $footer_padding_left_bill      = (isset($array["footer_bill_left"]))?$array["footer_bill_left"]:(($check_type)?$PrinterFooterTemplate->footer_bill_left:"0px");          /**   المحيط التوسع   */
    $footer_padding_top_bill       = (isset($array["footer_bill_top"]))?$array["footer_bill_top"]:(($check_type)?$PrinterFooterTemplate->footer_bill_top:"0px");          /**   المحيط التوسع   */
    $footer_padding_bottom_bill    = (isset($array["footer_bill_bottom"]))?$array["footer_bill_bottom"]:(($check_type)?$PrinterFooterTemplate->footer_bill_bottom:"0px");          /**   المحيط التوسع   */
    $footer_capital_text_bill      = (isset($array["footer_bill_letter"]))?$array["footer_bill_letter"]:(($check_type)?$PrinterFooterTemplate->footer_bill_letter:"captalize");    /**   أول حرف كبير   */
    $footer_position_bill          = (isset($array["footer_bill_position"]))?$array["footer_bill_position"]:(($check_type)?$PrinterFooterTemplate->footer_bill_position:"relative");     /**   مكان توضع العنصر  */
    $footer_position_top_bill      = (isset($array["footer_bill_padding_top"]))?$array["footer_bill_padding_top"]:(($check_type)?$PrinterFooterTemplate->footer_bill_padding_top:"0px");         /**   محاذات العنصر العائم  */
    $footer_position_bottom_bill   = (isset($array["footer_bill_padding_bottom"]))?$array["footer_bill_padding_bottom"]:(($check_type)?$PrinterFooterTemplate->footer_bill_padding_bottom:"0px");          /**   محاذات العنصر العائم  */
    $footer_position_left_bill     = (isset($array["footer_bill_padding_left"]))?$array["footer_bill_padding_left"]:(($check_type)?$PrinterFooterTemplate->footer_bill_padding_left:"0px");         /**   محاذات العنصر العائم  */
    $footer_position_right_bill    = (isset($array["footer_bill_padding_right"]))?$array["footer_bill_padding_right"]:(($check_type)?$PrinterFooterTemplate->footer_bill_padding_right:"0px");          /**   محاذات العنصر العائم  */
    
    // image 
    $footer_enable_img_align        = (isset($array["align_image_footer"]))?$array["align_image_footer"]:(($check_type)?$PrinterFooterTemplate->align_image_footer:"right");  
    $footer_position_img_align      = (isset($array["position_img_footer"]))?$array["position_img_footer"]:(($check_type)?$PrinterFooterTemplate->position_img_footer:"right");  
    $footer_img_width               = (isset($array["footer_image_width"]))?$array["footer_image_width"]:(($check_type)?$PrinterFooterTemplate->footer_image_width:"100" );
    $footer_img_height              = (isset($array["footer_image_height"]))?$array["footer_image_height"]:(($check_type)?$PrinterFooterTemplate->footer_image_height:"100" );
    $footer_border_width_img        = (isset($array["footer_image_border_width"]))?$array["footer_image_border_width"]:(($check_type)?$PrinterFooterTemplate->footer_image_border_width:"0px");          /**   عرض الإطار العنوان  */
    $footer_background_img          = (isset($array["footer_box_image_background"]))?$array["footer_box_image_background"]:(($check_type)?$PrinterFooterTemplate->footer_box_image_background:"transparent");       /**   عرض الإطار العنوان  */
    $footer_border_style_img        = (isset($array["footer_image_border_style"]))?$array["footer_image_border_style"]:(($check_type)?$PrinterFooterTemplate->footer_image_border_style:"solid");        /**   شكل إطار العنوان  */
    $footer_border_color_img        = (isset($array["footer_image_border_color"]))?$array["footer_image_border_color"]:(($check_type)?$PrinterFooterTemplate->footer_image_border_color:"transparent");        /**   لون إطار العنوان  */
    $footer_border_radius_img       = (isset($array["footer_image_border_radius"]))?$array["footer_image_border_radius"]:(($check_type)?$PrinterFooterTemplate->footer_image_border_radius:"0px");         /**   لون إطار العنوان  */
    $footer_img_box_color           = (isset($array["footer_box_image_color"]))?$array["footer_box_image_color"]:(($check_type)?$PrinterFooterTemplate->footer_box_image_color:"transparent");
    $footer_img_box_height          = (isset($array["footer_image_box_height"]))?$array["footer_image_box_height"]:(($check_type)?$PrinterFooterTemplate->footer_image_box_height:"auto");
    
    // image box
    
    $footer_enable_img_box           = (isset($array["footer_image_view"]))?(($array["footer_image_view"] === "true")?true:false):(($check_type)?(($PrinterFooterTemplate->footer_image_view == 1)?true:false):true); 
    $footer_enable_img               = (isset($array["footer_image_view"]))?(($array["footer_image_view"] === "true")?true:false):(($check_type)?(($PrinterFooterTemplate->footer_image_view == 1)?true:false):true); 
    $footer_position_box_align       = (isset($array["position_box_footer_align"]))?$array["position_box_footer_align"]:(($check_type)?$PrinterFooterTemplate->position_box_footer_align:"center" );
    $footer_img_box_width            = (isset($array["footer_image_box_width"]))?$array["footer_image_box_width"]:(($check_type)?$PrinterFooterTemplate->footer_image_box_width:"auto");                                /**   لون إطار العنوان  */
    $footer_img_box_margin           = (isset($array["footer_image_box_margin"]))?$array["footer_image_box_margin"]:(($check_type)?$PrinterFooterTemplate->footer_image_box_margin:" auto 0%");                             /**   لون إطار العنوان  */
    $footer_border_width_img_box     = (isset($array["footer_image_box_border_width"]))?$array["footer_image_box_border_width"]:(($check_type)?$PrinterFooterTemplate->footer_image_box_border_width:"0px");         /**   عرض الإطار العنوان  */
    $footer_border_style_img_box     = (isset($array["footer_image_box_border_style"]))?$array["footer_image_box_border_style"]:(($check_type)?$PrinterFooterTemplate->footer_image_box_border_style:"solid");       /**   شكل إطار العنوان  */
    $footer_border_color_img_box     = (isset($array["footer_image_box_border_color"]))?$array["footer_image_box_border_color"]:(($check_type)?$PrinterFooterTemplate->footer_image_box_border_color:"transparent");        /**   لون إطار العنوان  */
    $footer_border_radius_img_box    = (isset($array["footer_image_box_border_radius"]))?$array["footer_image_box_border_radius"]:(($check_type)?$PrinterFooterTemplate->footer_image_box_border_radius:"1px");                   /**   لون إطار العنوان  */
    $footer_background_img_box       = (isset($array["footer_image_box_background"]))?$array["footer_image_box_background"]:(($check_type)?$PrinterFooterTemplate->footer_image_box_background:"transparent");                      /**   عرض الإطار العنوان  */
     


    // header box
    $footer_enable_header_box           = (isset($array["footer_view"]))?(($array["footer_view"] === "true")?true:false):(($check_type)?(($PrinterFooterTemplate->footer_view == 1)?true:false):true); 
    $footer_header_box_width            = (isset($array["footer_box_width"]))?$array["footer_box_width"]:(($check_type)?$PrinterFooterTemplate->footer_box_width:"auto");         /**   لون إطار العنوان  */
    $footer_border_width_header_box     = (isset($array["footer_box_border_width"]))?$array["footer_box_border_width"]:(($check_type)?$PrinterFooterTemplate->footer_box_border_width:"0px");          /**   عرض الإطار العنوان  */
    $footer_border_style_header_box     = (isset($array["footer_box_border_style"]))?$array["footer_box_border_style"]:(($check_type)?$PrinterFooterTemplate->footer_box_border_style:"solid");        /**   شكل إطار العنوان  */
    $footer_border_color_header_box     = (isset($array["footer_box_border_color"]))?$array["footer_box_border_color"]:(($check_type)?$PrinterFooterTemplate->footer_box_border_color:"transparent");        /**   لون إطار العنوان  */
    $footer_border_radius_header_box    = (isset($array["footer_box_border_radius"]))?$array["footer_box_border_radius"]:(($check_type)?$PrinterFooterTemplate->footer_box_border_radius:"0px");         /**   لون إطار العنوان  */
    $footer_background_header_box       = (isset($array["footer_box_background"]))?$array["footer_box_background"]:(($check_type)?$PrinterFooterTemplate->footer_box_background:"transparent");       /**   عرض الإطار العنوان  */
    
    // other box
    $footer_enable_other_box           = (isset($array["footer_other_view"]))?(($array["footer_other_view"] === "true")?true:false):(($check_type)?(($PrinterFooterTemplate->footer_other_view == 1)?true:false):true);          /**   لون إطار العنوان  */
    $footer_other_box_width            = (isset($array["footer_other_width"]))?$array["footer_other_width"]:(($check_type)?$PrinterFooterTemplate->footer_other_width:"auto");  /**  لون إطار العنوان  */
    $footer_border_width_other_box     = (isset($array["footer_other_border_width"]))?$array["footer_other_border_width"]:(($check_type)?$PrinterFooterTemplate->footer_other_border_width:"0px"); /**  عرض الإطار العنوان  */
    $footer_border_style_other_box     = (isset($array["footer_other_border_style"]))?$array["footer_other_border_style"]:(($check_type)?$PrinterFooterTemplate->footer_other_border_style:"solid");        /**   شكل إطار العنوان  */
    $footer_border_color_other_box     = (isset($array["footer_other_border_color"]))?$array["footer_other_border_color"]:(($check_type)?$PrinterFooterTemplate->footer_other_border_color:"transparent");        /**   لون إطار العنوان  */
    $footer_border_radius_other_box    = (isset($array["footer_other_border_radius"]))?$array["footer_other_border_radius"]:(($check_type)?$PrinterFooterTemplate->footer_other_border_radius:"0px");         /** إطار العنوان  */
    $footer_background_other_box       = (isset($array["other_background_footer"]))?$array["other_background_footer"]:(($check_type)?$PrinterFooterTemplate->other_background_footer:"transparent");       /**   عرض الإطار العنوان  */


    // rows lines 
    $footer_row_line_enable            = (isset($array["footer_line_view"]))?(($array["footer_line_view"] === "true")?true:false):(($check_type)?(($PrinterFooterTemplate->footer_line_view == 1)?true:false):true);
    $footer_row_line_width             = (isset($array["footer_line_width"]))?$array["footer_line_width"]:(($check_type)?$PrinterFooterTemplate->footer_line_width:"50%" );
    $footer_row_line_height            = (isset($array["footer_line_height"]))?$array["footer_line_height"]:(($check_type)?$PrinterFooterTemplate->footer_line_height:"1px" );
    $footer_row_line_color             = (isset($array["footer_line_color"]))?$array["footer_line_color"]:(($check_type)?$PrinterFooterTemplate->footer_line_color:"black" );
    $footer_row_line_radius            = (isset($array["footer_line_radius"]))?$array["footer_line_radius"]:(($check_type)?$PrinterFooterTemplate->footer_line_radius:"0px" );
    $footer_row_line_border_width      = (isset($array["footer_line_border_width"]))?$array["footer_line_border_width"]:(($check_type)?$PrinterFooterTemplate->footer_line_border_width:"1px" );
    $footer_row_line_border_style      = (isset($array["footer_line_border_style"]))?$array["footer_line_border_style"]:(($check_type)?$PrinterFooterTemplate->footer_line_border_style:"solid" );
    $footer_row_line_border_color      = (isset($array["footer_line_border_color"]))?$array["footer_line_border_color"]:(($check_type)?$PrinterFooterTemplate->footer_line_border_color:"black" );
    $footer_row_line_margin_top        = (isset($array["footer_line_margin_top"]))?$array["footer_line_margin_top"]:(($check_type)?$PrinterFooterTemplate->footer_line_margin_top:"10px" );
    $footer_row_line_margin_bottom     = (isset($array["footer_line_margin_bottom"]))?$array["footer_line_margin_bottom"]:(($check_type)?$PrinterFooterTemplate->footer_line_margin_bottom:"10px" );  
    // .................................content

    $footer_top_align                   = "center";
    $footer_top_width                   = "100%";

    $page_number_text_align             = "center";
    $page_number_font_size              = "16px";
    $page_number_font_weight            = "800";
    $page_number_font_color             = "black";
    $page_number_font_style             = "uppercase";
    $page_number_view                   = (isset($array["page_number_view"]))?(($array["page_number_view"] === "true")?true:false):(($check_type)?(($PrinterFooterTemplate->page_number_view == 1)?true:false):true);          /**   لون إطار العنوان  */

    if(isset($edit_type)){
        $footer_header_text             =   $PrinterTemplateContain->left_footer_title  ;
        $footer_tax_text                =   $PrinterTemplateContain->center_top_footer_title;
        $footer_address_text            =   $PrinterTemplateContain->center_middle_footer_title;
        $footer_invoice_text            =   $PrinterTemplateContain->center_last_footer_title;
        $footer_img_url                 =   ($PrinterTemplateContain->footer_image_url)?$PrinterTemplateContain->footer_image_url:"https://thumbs.dreamstime.com/b/invoice-linear-icon-modern-outline-invoice-logo-concept-whit-invoice-linear-icon-modern-outline-invoice-logo-concept-white-133517211.jpg";
    }else{
       
        // footer ** left
        if(isset($array["left_footer_send_type"])){
             
            if( $array["left_footer_send_type"]  == "value" ){
                $footer_header_text            = (isset($array["left_footer"]))?$array["left_footer"]:"footer Title";
            }else{
                $footer_left_layout          = \App\InvoiceLayout::find($array["left_footer"]);
                $footer_header_text          = (isset($footer_left_layout))?$footer_left_layout->footer_text:"footer Title";
            }
        }else{
            $footer_header_text              = (isset($array["left_footer"]))?$array["left_footer"]:"footer Title";
        }
        // footer ** center top
        if(isset($array["center_top_footer_send_type"])){
            if( $array["center_top_footer_send_type"]  == "value" ){
                $footer_tax_text                    = (isset($array["center_top_footer"]))?$array["center_top_footer"]:"Tax : ";
            }else{
                $footer_center_top_layout    = \App\InvoiceLayout::find($array["center_top_footer"]);
                $footer_tax_text             = (isset($footer_center_top_layout))?$footer_center_top_layout->sub_heading_line1:"Tax : ";
            }
        }else{  
            $footer_tax_text                   = (isset($array["center_top_footer"]))?$array["center_top_footer"]:"Tax : ";
        }
        // footer ** center middle
        if(isset($array["center_middle_footer_send_type"])){
            if( $array["center_middle_footer_send_type"]  == "value" ){
                $footer_address_text                 = (isset($array["center_middle_footer"]))?$array["center_middle_footer"]:"Address : ";
            }else{
                $footer_center_middle_layout = \App\InvoiceLayout::find($array["center_middle_footer"]);
                $footer_address_text         =  (isset($footer_center_middle_layout))?$footer_center_middle_layout->sub_heading_line4:"Address : ";
            }
        }else{  
            $footer_address_text              = (isset($array["center_middle_footer"]))?$array["center_middle_footer"]:"Address : ";
        }
        // footer ** center last
        if(isset($array["center_last_footer_send_type"])){
            if( $array["center_last_footer_send_type"]  == "value" ){
                $footer_invoice_text                    = (isset($array["center_last_footer"]))?$array["center_last_footer"]:"Invoice";
            }else{
                $footer_center_last_layout    = \App\InvoiceLayout::find($array["center_last_footer"]);
                $footer_invoice_text          = $footer_center_last_layout->sub_heading_line5;
            }
        }else{  
            $footer_invoice_text         = (isset($array["center_last_footer"]))?$array["center_last_footer"]:"Invoice";
        }
        $footer_img_url                  =   isset($PrinterTemplateContain->footer_image_url)?$PrinterTemplateContain->footer_image_url:"https://thumbs.dreamstime.com/b/invoice-linear-icon-modern-outline-invoice-logo-concept-whit-invoice-linear-icon-modern-outline-invoice-logo-concept-white-133517211.jpg";
    }
}
    // $footer_footer_text                 = "Footer Title";
    // $footer_tax_text                    = " ";
    // $footer_address_text                = "\n \n mob: 060402323";
    // $footer_invoice_text                = "Thank You For Visit";
    $footer_header_position             = "left";
    // $footer_img_url                     = "https://img.freepik.com/free-vector/leaf-maple-icon-logo-design_474888-2154.jpg?size=338&ext=jpg&ga=GA1.1.632798143.1717653600&semt=sph";
    // $footer_img_url                     =  isset($PrinterTemplateContain->footer_image_url)?$PrinterTemplateContain->footer_image_url:"https://thumbs.dreamstime.com/b/invoice-linear-icon-modern-outline-invoice-logo-concept-whit-invoice-linear-icon-modern-outline-invoice-logo-concept-white-133517211.jpg";
    
?>
<style>
        /* Text  header */
        .footer_title-header{
            text-align:     {{ $footer_align . " !important" }};
            font-size:      {{ $footer_font_size . " !important" }}; 
            font-weight:    {{ $footer_font_weight . " !important" }}; 
            width:          {{ $footer_width_header . " !important" }}; 
            border-width:   {{ $footer_border_width . " !important" }};     
            border-color:   {{ $footer_border_color . " !important" }};     
            border-style:   {{ $footer_border_style . " !important" }};     
            padding-left:   {{ $footer_padding_left . " !important" }};     
            padding-right:  {{ $footer_padding_right . " !important" }};     
            padding-top:    {{ $footer_padding_top . " !important" }};     
            padding-bottom: {{ $footer_padding_bottom . " !important" }};     
            text-transform: {{ $footer_capital_text . " !important" }};
            position:       {{ $footer_header_text_position . " !important"  }};
            top:            {{ $footer_header_text_top . " !important"       }};
            left:           {{ $footer_header_text_left . " !important"      }};
            right:          {{ $footer_header_text_right . " !important"     }};
            bottom:         {{ $footer_header_text_bottom . " !important"    }};
            }
        /* Tax header */
        .footer_tax_number{
            position:       {{ $footer_box_position . " !important" }};
            border-width:   {{ $footer_border_width_box_tax . " !important" }};     
            border-color:   {{ $footer_border_color_box_tax . " !important" }};     
            border-style:   {{ $footer_border_style_box_tax . " !important" }};     
            top:            {{ $footer_box_tax_top . " !important"   }};
            left:           {{ $footer_box_tax_left . " !important"  }};
            right:          {{ $footer_box_tax_right . " !important" }};
            bottom:         {{ $footer_box_tax_bottom . " !important"}};
        }
            
        /* Tax */
        .footer_tax{
            text-align:     {{ $footer_align_tax . " !important" }};
            font-size:      {{ $footer_font_size_tax . " !important" }}; 
            border-width:   {{ $footer_border_width_tax . " !important" }};     
            border-color:   {{ $footer_border_color_tax . " !important" }};     
            border-style:   {{ $footer_border_style_tax . " !important" }};     
            padding-left:   {{ $footer_padding_left_tax . " !important" }};     
            padding-right:  {{ $footer_padding_right_tax . " !important" }};     
            padding-top:    {{ $footer_padding_top_tax . " !important" }};     
            padding-bottom: {{ $footer_padding_bottom_tax . " !important" }};     
            text-transform: {{ $footer_capital_text_tax . " !important" }};
            width:          {{ $footer_width_tax . " !important" }}; 
            position:       {{ $footer_position_tax . " !important" }};
            top:            {{ $footer_position_top_tax . " !important" }};
            left:           {{ $footer_position_left_tax . " !important" }};
            right:          {{ $footer_position_right_tax . " !important"}};
            bottom:         {{ $footer_position_bottom_tax . " !important" }};
        }
        /* Address */
        .footer_address{
            text-align:     {{ $footer_align_address . " !important" }};
            font-size:      {{ $footer_font_size_address . " !important" }}; 
            border-width:   {{ $footer_border_width_address . " !important" }};     
            border-color:   {{ $footer_border_color_address . " !important" }};     
            border-style:   {{ $footer_border_style_address . " !important" }};     
            padding-left:   {{ $footer_padding_left_address . " !important" }};     
            padding-right:  {{ $footer_padding_right_address . " !important" }};     
            padding-top:    {{ $footer_padding_top_address . " !important" }};     
            padding-bottom: {{ $footer_padding_bottom_address . " !important" }};     
            text-transform: {{ $footer_capital_text_address . " !important" }};
            width:          {{ $footer_width_address . " !important" }}; 
            position:       {{ $footer_position_address . " !important" }};
            top:            {{ $footer_position_top_address . " !important" }};
            left:           {{ $footer_position_left_address . " !important" }};
            right:          {{ $footer_position_right_address . " !important" }};
            bottom:         {{ $footer_position_bottom_address . " !important" }};
        }
        /* bill */
        .footer_name_of_bill{
            text-align:     {{ $footer_align_bill . " !important" }};
            font-size:      {{ $footer_font_size_bill . " !important" }}; 
            border-width:   {{ $footer_border_width_bill . " !important" }};     
            border-color:   {{ $footer_border_color_bill . " !important" }};     
            border-style:   {{ $footer_border_style_bill . " !important" }};     
            padding-left:   {{ $footer_padding_left_bill . " !important" }};     
            padding-right:  {{ $footer_padding_right_bill . " !important" }};     
            padding-top:    {{ $footer_padding_top_bill . " !important" }};     
            padding-bottom: {{ $footer_padding_bottom_bill . " !important" }};     
            text-transform: {{ $footer_capital_text_bill . " !important" }};
            width:          {{ $footer_width_bill . " !important" }}; 
            position:       {{ $footer_position_bill . " !important" }};
            top:            {{ $footer_position_top_bill . " !important" }};
            left:           {{ $footer_position_left_bill . " !important" }};
            right:          {{ $footer_position_right_bill . " !important" }};
            bottom:         {{ $footer_position_bottom_bill . " !important" }};
        }
        /* table */
        .footer_table_header{
            width:             {{ $footer_table_width . " !important" }};
            background-color:  {{ $footer_table_background . " !important" }};     
            border-width:      {{ $footer_border_width_table . " !important" }};     
            border-color:      {{ $footer_border_style_table . " !important" }};     
            border-style:      {{ $footer_border_color_table . " !important" }};     
            border-radius:     {{ $footer_border_radius_table . " !important" }};     
                 
                 
                 
                 
                 

        }
        /* image box */
        .footer_images_box{
            border-width:      {{ $footer_border_width_img . " !important" }};     
            border-color:      {{ $footer_border_style_img . " !important" }};     
            border-style:      {{ $footer_border_color_img . " !important" }};     
            border-radius:     {{ $footer_border_radius_img . " !important" }};
            background-color:  {{ $footer_background_img . " !important" }};
            width:             {{ $footer_img_width . " !important" }};
            height:            {{ $footer_img_height . " !important" }};
            text-align:        {{ $footer_position_box_align . " !important" }};
            margin:            {{ $footer_img_box_margin . " !important" }};
            background-color:  {{ $footer_img_box_color . " !important" }};
            height:            {{ $footer_img_box_height . " !important" }} ;
            
            }
        .footer_img-boxs{
             
            width:             {{ $footer_img_box_width . " !important" }};
            text-align:        {{ $footer_position_img_align . " !important" }};
            border-width:      {{ $footer_border_width_img_box . " !important" }}; 
            border-color:      {{ $footer_border_style_img_box . " !important" }}; 
            border-style:      {{ $footer_border_color_img_box . " !important" }}; 
            border-radius:     {{ $footer_border_radius_img_box . " !important" }};
           
        
          
        }
        .footer_header-box{
            width:             {{ $footer_header_box_width . " !important" }};
            border-width:      {{ $footer_border_width_header_box . " !important" }}; 
            border-color:      {{ $footer_border_style_header_box . " !important" }}; 
            border-style:      {{ $footer_border_color_header_box . " !important" }}; 
            border-radius:     {{ $footer_border_radius_header_box . " !important" }};
        }
        .footer_other-box{
            width:             {{ $footer_other_box_width . " !important" }};
            text-align:        {{ $align_other_footer . " !important" }};
            border-width:      {{ $footer_border_width_other_box . " !important" }}; 
            border-color:      {{ $footer_border_style_other_box . " !important" }}; 
            border-style:      {{ $footer_border_color_other_box . " !important" }}; 
            border-radius:     {{ $footer_border_radius_other_box . " !important" }};
        }
        .footer_top{
            text-align: {{ $footer_top_align . " !important" }};
            width: {{ $footer_top_width . " !important" }};

        }
        .footer_row_line{
            width:             {{ $footer_row_line_width . " !important" }};
            height:            {{ $footer_row_line_height . " !important" }};
            background-color:  {{ $footer_row_line_color . " !important" }}; 
            border-radius:     {{ $footer_row_line_radius . " !important" }}; 
            border-width:      {{ $footer_row_line_border_width . " !important" }}; 
            border-style:      {{ $footer_row_line_border_style . " !important" }}; 
            border-color:      {{ $footer_row_line_border_color . " !important" }}; 
            margin-top:        {{ $footer_row_line_margin_top . " !important" }}; 
            margin-bottom:     {{ $footer_row_line_margin_bottom . " !important" }}; 
        }

        .footer_page_number{
            text-align:        {{ $page_number_text_align . " !important" }};
            font-size:         {{ $page_number_font_size . " !important" }};
            font-weight:       {{ $page_number_font_weight . " !important" }};
            color:             {{ $page_number_font_color . " !important" }};
            text-transform:    {{ $page_number_font_style . " !important" }};
        }
        
</style>
{{-- TITLE SECTION ****$% --}}
@if($footer_style_box == "h1")
            @if($footer_row_line_enable == true)
                <hr class="footer_row_line">
            @endif
            @if($footer_enable_img == true && $footer_enable_img_align == "top")  
                    <div class="footer_img-boxs">
                        <div class="footer_images_box" >
                            <img src="{{$footer_img_url}}" id="footer_img_logo"  width="{{$footer_img_width}}" height="{{$footer_img_height}}" alt="LOGO"> 
                        </div>
                    </div>
            @endif
            @if($footer_enable_header_box == true)
                <h1 class="footer_title-header"  >
                    {!! $footer_header_text !!}  
                </h1>
            @endif
            @if($footer_enable_img == true && $footer_enable_img_align == "bottom") 
                <div class="footer_img-boxs">
                    <div class="footer_images_box" >
                        <img src="{{$footer_img_url}}" id="footer_img_logo"  width="{{$footer_img_width}}" height="{{$footer_img_height}}" alt="LOGO"> 
                    </div>
                    </div>
                    @endif
                    @if($footer_enable_other_box == true)
                    <div class="footer_tax_number">
                        <div class="footer_tax hide">{!! $footer_tax_text !!}</div>
                        <div class="footer_address">{!! $footer_address_text !!}</div>
                        <div class="footer_name_of_bill">{!! $footer_invoice_text !!}</div>
                </div>
            @endif
@elseif($footer_style_box == "table")
    @if($footer_row_line_enable == true)
        <hr class="footer_row_line">
    @endif
    <table class="footer_table_header">
        <tbody>
            <tr> 
                @if($footer_enable_img == true && $footer_enable_img_align == "left")
                    <td class="footer_img-boxs">
                        <div class="footer_images_box" >
                            <img src="{{$footer_img_url}}" id="footer_img_logo"  width="{{$footer_img_width}}" height="{{$footer_img_height}}" alt="LOGO"> 
                           
                        </div>
                    </td>
                @endif

                @if( $footer_header_position == "left")
                    @if($footer_enable_header_box == true)
                        <td class="footer_header-box">
                            <div class="footer_title-header" >
                                {!! $footer_header_text !!}
                                
                            </div>
                        </td>
                    @endif
                    @if($footer_enable_img == true && $footer_enable_img_align == "center")
                        <td class="footer_img-boxs">
                            <div class="footer_images" >
                                <img src="{{$footer_img_url}}" id="footer_img_logo"  width="{{$footer_img_width}}" height="{{$footer_img_height}}" alt="LOGO"> 
                            </div>
                        </td>
                    @endif
                    @if($footer_enable_other_box == true)
                        <td class="footer_other-box">
                            <div class="footer_tax_number">
                                <div class="footer_tax hide">{!! $footer_tax_text !!}</div>
                                <div class="footer_address">{!! $footer_address_text !!}</div>
                                <div class="footer_name_of_bill">{!! $footer_invoice_text !!}</div>
                            </div>
                        </td>
                    @endif
                @elseif( $footer_header_position == "right")
                    @if($footer_enable_other_box == true)
                        <td class="footer_other-box">
                            <div class="footer_tax_number">
                                <div class="footer_tax hide">{!! $footer_tax_text !!}</div>
                                <div class="footer_address">{!! $footer_address_text !!}</div>
                                <div class="footer_name_of_bill">{!! $footer_invoice_text !!}</div>
                            </div>
                        </td>
                    @endif
                    
                    @if($footer_enable_img == true && $footer_enable_img_align == "center")
                        <td class="footer_img-boxs">
                            <div class="footer_images" >
                                <img src="{{$footer_img_url}}" id="footer_img_logo"  width="{{$footer_img_width}}" height="{{$footer_img_height}}" alt="LOGO"> 
                            </div>
                       </td>
                    @endif
                    @if($footer_enable_header_box == true)           
                        <td class="footer_header-box">
                            <div class="footer_title-header" >
                                {!! $footer_header_text !!}
                            </div>
                        </td>
                    @endif
                        
                
                @endif
                @if($footer_enable_img == true && $footer_enable_img_align == "right")
                    <td class="footer_img-boxs">
                        <div class="footer_images" >
                            <img src="{{$footer_img_url}}" id="footer_img_logo"  width="{{$footer_img_width}}" height="{{$footer_img_height}}" alt="LOGO"> 
                        </div>
                    </td>
                @endif
            </tr>
             
        </tbody>
    </table>
@else

    <div class="footer_title-header" >
        {!! $footer_header_text !!}
    </div>
    <div class="footer_tax_number">
        <div class="footer_tax hide">{!! $footer_tax_text !!}</div>
        <div class="footer_address">{!! $footer_address_text !!}</div>
        <div class="footer_name_of_bill">{!! $footer_invoice_text !!}</div>
    </div>
    
@endif
@if($page_number_view == true)
    <p class="footer_page_number">Page <span class="page-number"></span></p>
@endif
 