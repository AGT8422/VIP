<?php
    /**
     * ******************************* *
     * Here Set Variables For Settings *
     * ******************************* *
     */
    

    //.................................style
    
    if(isset($print)){
        // header
        $invoice_no              = $print_content["invoice_no"];
        $project_no              = $print_content["project_no"];
        $customer_no             = $print_content["customer_no"];
        $date_name               = $print_content["date_name"];
        $address_name            = $print_content["address_name"];  
        $mobile_name             = $print_content["mobile_name"]; 
        $tax_name                = $print_content["tax_name"]; 
        $align                   = $print["align_text_header"];              /**   محاذات العناون*/
        $style_box               = $print["style_header"];                       /**   شكل العنوان  */
        $font_size               = $print["header_font_size"];                /**   خط العنوان */
        $font_weight             = $print["header_font_weight"];                       /**   خط عريض العنوان */
        $width_header            = $print["header_width"];                        /**   عرض الإطار العنوان  */ 

        $border_width            = $print["header_border_width"];           /**   عرض الإطار العنوان  $$$$$ */ 
        $border_style            = $print["header_border_style"];         /**   شكل إطار العنوان   $$$$$ */
        $border_color            = $print["header_border_color"];         /**   لون إطار العنوان   $$$$$ */
        
        $padding_right           = $print["header_padding_right"];         /**   المحيط التوسع  */
        $padding_left            = $print["header_padding_left"];           /**   المحيط التوسع   */
        $padding_top             = $print["header_padding_top"];             /**   المحيط التوسع   */
        $padding_bottom          = $print["header_padding_bottom"];       /**   المحيط التوسع   */
        
        $header_text_position    = $print["header_position"];              /** طريقة عرض العنوان */
        $capital_text            = $print["header_style_letter"];    /**   أول حرف كبير   */   
        
        $header_text_top         = $print["header_top"];        /**   موقع الاحداثيات العلوية   */
        $header_text_left        = $print["header_left"];        /**   موقع الاحداثيات اليسارية   */
        $header_text_right       = $print["header_right"];      /**   موقع الاحداثيات اليمينية  $$$$$ */
        $header_text_bottom      = $print["header_bottom"];    /**   موقع الاحداثيات السفلية   $$$$$$ */
    
        // table
        $header_table_width        = $print["header_table_width"];            /**   محاذات العنصر الأصلي  */
        $border_width_table        = "0px" ;             /**   عرض الإطار العنوان  */
        $border_style_table        = "solid" ;           /**   شكل إطار العنوان  */
        $border_color_table        = "black" ;           /**   لون إطار العنوان  */
        $header_table_background   = $print["header_table_color"];           /**   عرض الإطار العنوان  */
        $header_border_radius_table= $print["header_table_radius"];            /**   عرض الإطار العنوان  */

        //$%^ boxes
        $align_other_header        = $print["align_other_header"];     /**   محاذات العنصر الأصلي  */
        $border_width_box_tax      = $print["header_other_border_width"];          /**   عرض الإطار العنوان  */
        $border_style_box_tax      = $print["header_other_border_style"];        /**   شكل إطار العنوان  */
        $border_color_box_tax      = $print["header_other_border_color"];        /**   لون إطار العنوان  */
        $box_position              = $print["header_other_position"];     /**   محاذات العنصر الأصلي  */
        $box_tax_top               = $print["header_other_top"];         /**   لون إطار العنوان  */
        $box_tax_left              = $print["header_other_left"];          /**   لون إطار العنوان  */
        $box_tax_right             = $print["header_other_right"];          /**   لون إطار العنوان  */
        $box_tax_bottom            = $print["header_other_bottom"];          /**   لون إطار العنوان  */
        

        // tax
        $align_tax                 = $print["header_tax_align"];       /**   محاذات العناون*/
        $font_size_tax             = $print["header_tax_font_size"];          /**   خط العنوان */
        $width_tax                 = $print["header_tax_width"];         /**   عرض الإطار العنوان  */
        $border_width_tax          = $print["header_tax_border_width"];          /**   عرض الإطار العنوان  */
        $border_style_tax          = $print["header_tax_border_style"];        /**   شكل إطار العنوان  */
        $border_color_tax          = $print["header_tax_border_color"];        /**   لون إطار العنوان  */
        $padding_right_tax         = $print["header_tax_right"];          /**   المحيط التوسع  */
        $padding_left_tax          = $print["header_tax_left"];          /**   المحيط التوسع   */
        $padding_top_tax           = $print["header_tax_top"];          /**   المحيط التوسع   */
        $padding_bottom_tax        = $print["header_tax_bottom"];          /**   المحيط التوسع   */
        $capital_text_tax          = $print["header_tax_letter"];    /**   أول حرف كبير   */
        $position_tax              = $print["header_tax_position"];     /**   مكان توضع العنصر  */
        $position_top_tax          = $print["header_tax_padding_top"];        /**   محاذات العنصر العائم  */
        $position_bottom_tax       = $print["header_tax_padding_bottom"];          /**   محاذات العنصر العائم  */
        $position_left_tax         = $print["header_tax_padding_left"];          /**   محاذات العنصر العائم  */
        $position_right_tax        = $print["header_tax_padding_right"];          /**   محاذات العنصر العائم  */
        
        // address
        $align_address             = $print["header_address_align"];       /**   محاذات العناون*/
        $font_size_address         = $print["header_address_font_size"];          /**   خط العنوان */
        $width_address             = $print["header_address_width"];         /**   عرض الإطار العنوان  */
        $border_width_address      = $print["header_address_border_width"];          /**   عرض الإطار العنوان  */
        $border_style_address      = $print["header_address_border_style"];        /**   شكل إطار العنوان  */
        $border_color_address      = $print["header_address_border_color"];        /**   لون إطار العنوان  */
        $padding_right_address     = $print["header_address_right"];          /**   المحيط التوسع  */
        $padding_left_address      = $print["header_address_left"];          /**   المحيط التوسع   */
        $padding_top_address       = $print["header_address_top"];          /**   المحيط التوسع   */
        $padding_bottom_address    = $print["header_address_bottom"];          /**   المحيط التوسع   */
        $capital_text_address      = $print["header_address_letter"];    /**   أول حرف كبير   */
        $position_address          = $print["header_address_position"];     /**   مكان توضع العنصر  */
        $position_top_address      = $print["header_address_padding_top"];        /**   محاذات العنصر العائم  */
        $position_bottom_address   = $print["header_address_padding_bottom"];          /**   محاذات العنصر العائم  */
        $position_left_address     = $print["header_address_padding_left"];          /**   محاذات العنصر العائم  */
        $position_right_address    = $print["header_address_padding_right"];         /**   محاذات العنصر العائم  */
        
        // bill name
        $align_bill                = $print["header_bill_align"];       /**   محاذات العناون */ 
        $font_size_bill            = $print["header_bill_font_size"];         /**   خط العنوان */
        $width_bill                = $print["header_bill_width"];         /**   عرض الإطار العنوان  */
        $border_width_bill         = $print["header_bill_border_width"];          /**   عرض الإطار العنوان  */
        $border_style_bill         = $print["header_bill_border_style"];        /**   شكل إطار العنوان  */
        $border_color_bill         = $print["header_bill_border_color"];        /**   لون إطار العنوان  */
        $padding_right_bill        = $print["header_bill_right"];          /**   المحيط التوسع  */
        $padding_left_bill         = $print["header_bill_left"];          /**   المحيط التوسع   */
        $padding_top_bill          = $print["header_bill_top"];          /**   المحيط التوسع   */
        $padding_bottom_bill       = $print["header_bill_bottom"];          /**   المحيط التوسع   */
        $capital_text_bill         = $print["header_bill_letter"];    /**   أول حرف كبير   */
        $position_bill             = $print["header_bill_position"];     /**   مكان توضع العنصر  */
        $position_top_bill         = $print["header_bill_padding_top"];        /**   محاذات العنصر العائم  */
        $position_bottom_bill      = $print["header_bill_padding_bottom"];          /**   محاذات العنصر العائم  */
        $position_left_bill        = $print["header_bill_padding_left"];          /**   محاذات العنصر العائم  */
        $position_right_bill       = $print["header_bill_padding_right"];         /**   محاذات العنصر العائم  */
        
        // image 
        $enable_img_align            = $print["align_image_header"];                           /**   مكان توضع الصورة في الترويسة */
        $position_img_align          = $print["position_img_header"];                         /**   مكان توضع الصورة في جزء الصورة في الترويسة*/
        $img_width                   = $print["header_image_width"] ;                          /**   عرض الصورة    */
        $img_height                  = $print["header_image_height"] ;                        /**   ارتفاع الصورة */ 
        $border_width_img            = $print["header_image_border_width"] ;            /**   عرض الإطار  الصورة  */
        $background_img              = $print["header_box_image_background"];  /**   خلفية الصورة */ 
        $border_style_img            = $print["header_image_border_style"];            /**   شكل إطار الصورة  */
        $border_color_img            = $print["header_image_border_color"];            /**   لون إطار الصورة  */
        $border_radius_img           = $print["header_image_border_radius"];            /**   تصميم إطار الصورة */
        $img_box_color               = $print["header_box_image_color"];   
        $img_box_height              = $print["header_image_box_height"];
        
        // image box
        
        $enable_img_box              = $print["header_image_view"];
        $enable_img                  = $print["header_image_view"];
        $position_box_align          = $print["position_box_header_align"];
        $img_box_width               = $print["header_image_box_width"];                 /**   لون إطار العنوان  */
        $img_box_margin              = $print["header_image_box_margin"];                /**   لون إطار العنوان  */
        $border_width_img_box        = $print["header_image_box_border_width"];         /**   عرض الإطار العنوان  */
        $border_style_img_box        = $print["header_image_box_border_style"];       /**   شكل إطار العنوان  */
        $border_color_img_box        = $print["header_image_box_border_color"];        /**   لون إطار العنوان  */
        $border_radius_img_box       = $print["header_image_box_border_radius"];                     /**   لون إطار العنوان  */
        $background_img_box          = $print["header_image_box_background"];                  /**   عرض الإطار العنوان  */
        
        
        
        // header box
        $enable_header_box           = $print["header_view"];
        $header_box_width            = $print["header_box_width"];         /**   لون إطار العنوان  */
        $border_width_header_box     = $print["header_box_border_width"];          /**   عرض الإطار العنوان  */
        $border_style_header_box     = $print["header_box_border_style"];        /**   شكل إطار العنوان  */
        $border_color_header_box     = $print["header_box_border_color"];        /**   لون إطار العنوان  */
        $border_radius_header_box    = $print["header_box_border_radius"];         /**   لون إطار العنوان  */
        $background_header_box       = $print["header_box_background"];       /**   عرض الإطار العنوان  */
        

        
        //$%^ other box
        $enable_other_box           = $print["header_other_view"];        /**   لون إطار العنوان  */
        $other_box_width            = $print["header_other_width"];                          /**   لون إطار العنوان  */
        $border_width_other_box     = $print["header_other_border_width"];          /**   عرض الإطار العنوان  */
        $border_style_other_box     = $print["header_other_border_style"];        /**   شكل إطار العنوان  */
        $border_color_other_box     = $print["header_other_border_color"];        /**   لون إطار العنوان  */
        $border_radius_other_box    = $print["header_other_border_radius"];         /**   لون إطار العنوان  */
        $background_other_box       = $print["other_background_header"];       /**   عرض الإطار العنوان  */




        // rows lines 
        $row_line_enable            = $print["header_line_view"];       
        $row_line_width             = $print["header_line_width"] ;
        $row_line_height            = $print["header_line_height"] ;
        $row_line_color             = $print["header_line_color"] ;
        $row_line_radius            = $print["header_line_radius"] ;
        $row_line_border_width      = $print["header_line_border_width"];
        $row_line_border_style      = $print["header_line_border_style"] ;
        $row_line_border_color      = $print["header_line_border_color"] ;
        $row_line_margin_top        = $print["header_line_margin_top"] ;
        
        
        // .................................content
        $top_align                   = "center";
        $top_width                   = "100%";
        
        $page_number_view            = $print_content["page_number_view"];        
        $header_text                 = $print["left_header_title"]  ;
        $tax_text                    = $print["center_top_header_title"];
        $address_text                = $print["center_middle_header_title"];
        $invoice_text                = $print["center_last_header_title"];
        $img_url                     = $print["image_url"];
        // body ###########
        $top_table_section               =  $print_content["top_table_section"]; 
        $repeat_content_top              =  $print_content["repeat_content_top"]; 
        $left_top_table                  =  "";
        $left_top_table                  =  $print_content["left_top_content"] ;
        $bold_left_invoice_info_number   =  $print_content["bold_left_invoice_info_number"]  ;
        $bold_left_invoice_info_project  =  $print_content["bold_left_invoice_info_project"] ;
        $bold_left_invoice_info_date     =  $print_content["bold_left_invoice_info_date"] ;
        $class_width_left                =  $print_content["class_width_left"];
        $class_width_right               =  $print_content["class_width_right"];
        $right_top_table                            =  $print_content["right_top_content"] ;
        $bold_left_invoice_info_customer_number     =  $print_content["bold_left_invoice_info_customer_number"]  ;
        $class_width_left_right                     =  $print_content["class_width_left_right"];
        $class_width_right_right                    =  $print_content["class_width_right_right"] ;
        $bold_left_invoice_info_customer_address    =  $print_content["bold_left_invoice_info_customer_address"]  ;
        $bold_left_invoice_info_customer_mobile     =  $print_content["bold_left_invoice_info_customer_mobile"] ;
        $bold_left_invoice_info_customer_tax        =  $print_content["bold_left_invoice_info_customer_tax"]  ; 
    }else{
    
    
        $check_type              = (isset($edit_type) || (isset($array["edit_type"]) && $array["edit_type"] != null ));
        
        if((isset($array["edit_type"]) && $array["edit_type"] != null )){
            $PrinterTemplate        = \App\Models\PrinterTemplate::find($array["edit_type"]);
            $PrinterTemplateContain = \App\Models\PrinterTemplateContain::where("printer_templates_id",$array["edit_type"])->first();
            $PrinterContentTemplate = \App\Models\PrinterContentTemplate::where("printer_template_id",$array["edit_type"])->first();
            $PrinterFooterTemplate  = \App\Models\PrinterFooterTemplate::where("printer_template_id",$array["edit_type"])->first();
        }
        
        // header
        $align                   = (isset($array["header_text_align"]))?$array["header_text_align"]:(($check_type)?$PrinterTemplate->align_text_header:"left");              /**   محاذات العناون*/
        $style_box               = (isset($array["header_style"]))?$array["header_style"]:(($check_type)?$PrinterTemplate->style_header:"table");                       /**   شكل العنوان  */
        $font_size               = (isset($array["header_font_size"]))?$array["header_font_size"]:(($check_type)?$PrinterTemplate->header_font_size:"22px");                /**   خط العنوان */
        $font_weight             = (isset($array["header_weight"]))?$array["header_weight"]:(($check_type)?$PrinterTemplate->header_font_weight:"300");                       /**   خط عريض العنوان */
        $width_header            = (isset($array["header_width"]))?$array["header_width"]:(($check_type)?$PrinterTemplate->header_width:"100%");                        /**   عرض الإطار العنوان  */ 

        $border_width            = (isset($array["header_border_width"]))?$array["header_border_width"]:(($check_type)?$PrinterTemplate->header_border_width:"0px");           /**   عرض الإطار العنوان  $$$$$ */ 
        $border_style            = (isset($array["header_border_style"]))?$array["header_border_style"]:(($check_type)?$PrinterTemplate->header_border_style:"solid");         /**   شكل إطار العنوان   $$$$$ */
        $border_color            = (isset($array["header_border_color"]))?$array["header_border_color"]:(($check_type)?$PrinterTemplate->header_border_color:"transparent");         /**   لون إطار العنوان   $$$$$ */
        
        $padding_right           = (isset($array["header_padding_right"]))?$array["header_padding_right"]:(($check_type)?$PrinterTemplate->header_padding_right:"0px");         /**   المحيط التوسع  */
        $padding_left            = (isset($array["header_padding_left"]))?$array["header_padding_left"]:(($check_type)?$PrinterTemplate->header_padding_left:"0px");           /**   المحيط التوسع   */
        $padding_top             = (isset($array["header_padding_top"]))?$array["header_padding_top"]:(($check_type)?$PrinterTemplate->header_padding_top:"0px");             /**   المحيط التوسع   */
        $padding_bottom          = (isset($array["header_padding_bottom"]))?$array["header_padding_bottom"]:(($check_type)?$PrinterTemplate->header_padding_bottom:"0px");       /**   المحيط التوسع   */
        
        $header_text_position    = (isset($array["header_position"]))?$array["header_position"]:(($check_type)?$PrinterTemplate->header_position:"relative");              /** طريقة عرض العنوان */
        $capital_text            = (isset($array["header_style_letter"]))?$array["header_style_letter"]:(($check_type)?$PrinterTemplate->header_style_letter:"capitalize");    /**   أول حرف كبير   */   
        
        $header_text_top         = (isset($array["header_top"]))?$array["header_top"]:(($check_type)?$PrinterTemplate->header_top:"0px");        /**   موقع الاحداثيات العلوية   */
        $header_text_left        = (isset($array["header_left"]))?$array["header_left"]:(($check_type)?$PrinterTemplate->header_left:"0px");        /**   موقع الاحداثيات اليسارية   */
        $header_text_right       = (isset($array["header_right"]))?$array["header_right"]:(($check_type)?$PrinterTemplate->header_right:"0px");      /**   موقع الاحداثيات اليمينية  $$$$$ */
        $header_text_bottom      = (isset($array["header_bottom"]))?$array["header_bottom"]:(($check_type)?$PrinterTemplate->header_bottom:"0px");    /**   موقع الاحداثيات السفلية   $$$$$$ */
    
        // table
        $header_table_width        = (isset($array["header_table_width"]))?$array["header_table_width"]:(($check_type)?$PrinterTemplate->header_table_width:"100%");            /**   محاذات العنصر الأصلي  */
        $border_width_table        = "0px" ;             /**   عرض الإطار العنوان  */
        $border_style_table        = "solid" ;           /**   شكل إطار العنوان  */
        $border_color_table        = "black" ;           /**   لون إطار العنوان  */
        $header_table_background   = (isset($array["header_table_color"]))?$array["header_table_color"]:(($check_type)?$PrinterTemplate->header_table_color: "transparent");           /**   عرض الإطار العنوان  */
        $header_border_radius_table= (isset($array["header_table_radius"]))?$array["header_table_radius"]:(($check_type)?$PrinterTemplate->header_table_radius:  "0px");            /**   عرض الإطار العنوان  */

        //$%^ boxes
        $align_other_header        = (isset($array["align_other_header"]))?$array["align_other_header"]:(($check_type)?$PrinterTemplate->align_other_header:"center");     /**   محاذات العنصر الأصلي  */
        $border_width_box_tax      = (isset($array["header_other_border_width"]))?$array["header_other_border_width"]:(($check_type)?$PrinterTemplate->header_other_border_width:"0px");          /**   عرض الإطار العنوان  */
        $border_style_box_tax      = (isset($array["header_other_border_style"]))?$array["header_other_border_style"]:(($check_type)?$PrinterTemplate->header_other_border_style:"solid");        /**   شكل إطار العنوان  */
        $border_color_box_tax      = (isset($array["header_other_border_color"]))?$array["header_other_border_color"]:(($check_type)?$PrinterTemplate->header_other_border_color:"black");        /**   لون إطار العنوان  */
        $box_position              = (isset($array["header_other_position"]))?$array["header_other_position"]:(($check_type)?$PrinterTemplate->header_other_position:"relative");     /**   محاذات العنصر الأصلي  */
        $box_tax_top               = (isset($array["header_other_top"]))?$array["header_other_top"]:(($check_type)?$PrinterTemplate->header_other_top:"0px");         /**   لون إطار العنوان  */
        $box_tax_left              = (isset($array["header_other_left"]))?$array["header_other_left"]:(($check_type)?$PrinterTemplate->header_other_left:"0px");          /**   لون إطار العنوان  */
        $box_tax_right             = (isset($array["header_other_right"]))?$array["header_other_right"]:(($check_type)?$PrinterTemplate->header_other_right:"0px");          /**   لون إطار العنوان  */
        $box_tax_bottom            = (isset($array["header_other_bottom"]))?$array["header_other_bottom"]:(($check_type)?$PrinterTemplate->header_other_bottom:"0px");          /**   لون إطار العنوان  */
        

        // tax
        $align_tax                 = (isset($array["header_tax_align"]))?$array["header_tax_align"]:(($check_type)?$PrinterTemplate->header_tax_align:"center");       /**   محاذات العناون*/
        $font_size_tax             = (isset($array["header_tax_font_size"]))?$array["header_tax_font_size"]:(($check_type)?$PrinterTemplate->header_tax_font_size:"22px");          /**   خط العنوان */
        $width_tax                 = (isset($array["header_tax_width"]))?$array["header_tax_width"]:(($check_type)?$PrinterTemplate->header_tax_width:"100%");         /**   عرض الإطار العنوان  */
        $border_width_tax          = (isset($array["header_tax_border_width"]))?$array["header_tax_border_width"]:(($check_type)?$PrinterTemplate->header_tax_border_width:"0px");          /**   عرض الإطار العنوان  */
        $border_style_tax          = (isset($array["header_tax_border_style"]))?$array["header_tax_border_style"]:(($check_type)?$PrinterTemplate->header_tax_border_style:"solid");        /**   شكل إطار العنوان  */
        $border_color_tax          = (isset($array["header_tax_border_color"]))?$array["header_tax_border_color"]:(($check_type)?$PrinterTemplate->header_tax_border_color:"transparent");        /**   لون إطار العنوان  */
        $padding_right_tax         = (isset($array["header_tax_right"]))?$array["header_tax_right"]:(($check_type)?$PrinterTemplate->header_tax_right:"0px");          /**   المحيط التوسع  */
        $padding_left_tax          = (isset($array["header_tax_left"]))?$array["header_tax_left"]:(($check_type)?$PrinterTemplate->header_tax_left:"0px");          /**   المحيط التوسع   */
        $padding_top_tax           = (isset($array["header_tax_top"]))?$array["header_tax_top"]:(($check_type)?$PrinterTemplate->header_tax_top:"0px");          /**   المحيط التوسع   */
        $padding_bottom_tax        = (isset($array["header_tax_bottom"]))?$array["header_tax_bottom"]:(($check_type)?$PrinterTemplate->header_tax_bottom:"0px");          /**   المحيط التوسع   */
        $capital_text_tax          = (isset($array["header_tax_letter"]))?$array["header_tax_letter"]:(($check_type)?$PrinterTemplate->header_tax_letter:"captalize");    /**   أول حرف كبير   */
        $position_tax              = (isset($array["header_tax_position"]))?$array["header_tax_position"]:(($check_type)?$PrinterTemplate->header_tax_position:"relative");     /**   مكان توضع العنصر  */
        $position_top_tax          = (isset($array["header_tax_padding_top"]))?$array["header_tax_padding_top"]:(($check_type)?$PrinterTemplate->header_tax_padding_top:"0px");        /**   محاذات العنصر العائم  */
        $position_bottom_tax       = (isset($array["header_tax_padding_bottom"]))?$array["header_tax_padding_bottom"]:(($check_type)?$PrinterTemplate->header_tax_padding_bottom:"0px");          /**   محاذات العنصر العائم  */
        $position_left_tax         = (isset($array["header_tax_padding_left"]))?$array["header_tax_padding_left"]:(($check_type)?$PrinterTemplate->header_tax_padding_left:"0px");          /**   محاذات العنصر العائم  */
        $position_right_tax        = (isset($array["header_tax_padding_right"]))?$array["header_tax_padding_right"]:(($check_type)?$PrinterTemplate->header_tax_padding_right:"0px");          /**   محاذات العنصر العائم  */
        
        // address
        $align_address             = (isset($array["header_address_align"]))?$array["header_address_align"]:(($check_type)?$PrinterTemplate->header_address_align:"center");       /**   محاذات العناون*/
        $font_size_address         = (isset($array["header_address_font_size"]))?$array["header_address_font_size"]:(($check_type)?$PrinterTemplate->header_address_font_size:"22px");          /**   خط العنوان */
        $width_address             = (isset($array["header_address_width"]))?$array["header_address_width"]:(($check_type)?$PrinterTemplate->header_address_width:"100%");         /**   عرض الإطار العنوان  */
        $border_width_address      = (isset($array["header_address_border_width"]))?$array["header_address_border_width"]:(($check_type)?$PrinterTemplate->header_address_border_width:"0px");          /**   عرض الإطار العنوان  */
        $border_style_address      = (isset($array["header_address_border_style"]))?$array["header_address_border_style"]:(($check_type)?$PrinterTemplate->header_address_border_style:"solid");        /**   شكل إطار العنوان  */
        $border_color_address      = (isset($array["header_address_border_color"]))?$array["header_address_border_color"]:(($check_type)?$PrinterTemplate->header_address_border_color:"transparent");        /**   لون إطار العنوان  */
        $padding_right_address     = (isset($array["header_address_right"]))?$array["header_address_right"]:(($check_type)?$PrinterTemplate->header_address_right:"0px");          /**   المحيط التوسع  */
        $padding_left_address      = (isset($array["header_address_left"]))?$array["header_address_left"]:(($check_type)?$PrinterTemplate->header_address_left:"0px");          /**   المحيط التوسع   */
        $padding_top_address       = (isset($array["header_address_top"]))?$array["header_address_top"]:(($check_type)?$PrinterTemplate->header_address_top:"0px");          /**   المحيط التوسع   */
        $padding_bottom_address    = (isset($array["header_address_bottom"]))?$array["header_address_bottom"]:(($check_type)?$PrinterTemplate->header_address_bottom:"0px");          /**   المحيط التوسع   */
        $capital_text_address      = (isset($array["header_address_letter"]))?$array["header_address_letter"]:(($check_type)?$PrinterTemplate->header_address_letter:"captalize");    /**   أول حرف كبير   */
        $position_address          = (isset($array["header_address_position"]))?$array["header_address_position"]:(($check_type)?$PrinterTemplate->header_address_position:"relative");     /**   مكان توضع العنصر  */
        $position_top_address      = (isset($array["header_address_padding_top"]))?$array["header_address_padding_top"]:(($check_type)?$PrinterTemplate->header_address_padding_top:"0px");        /**   محاذات العنصر العائم  */
        $position_bottom_address   = (isset($array["header_address_padding_bottom"]))?$array["header_address_padding_bottom"]:(($check_type)?$PrinterTemplate->header_address_padding_bottom:"0px");          /**   محاذات العنصر العائم  */
        $position_left_address     = (isset($array["header_address_padding_left"]))?$array["header_address_padding_left"]:(($check_type)?$PrinterTemplate->header_address_padding_left:"0px");          /**   محاذات العنصر العائم  */
        $position_right_address    = (isset($array["header_address_padding_right"]))?$array["header_address_padding_right"]:(($check_type)?$PrinterTemplate->header_address_padding_right:"0px");         /**   محاذات العنصر العائم  */
        
        // bill name
        $align_bill                = (isset($array["header_bill_align"]))?$array["header_bill_align"]:(($check_type)?$PrinterTemplate->header_bill_align:"center");       /**   محاذات العناون */ 
        $font_size_bill            = (isset($array["header_bill_font_size"]))?$array["header_bill_font_size"]:(($check_type)?$PrinterTemplate->header_bill_font_size:"22px");         /**   خط العنوان */
        $width_bill                = (isset($array["header_bill_width"]))?$array["header_bill_width"]:(($check_type)?$PrinterTemplate->header_bill_width:"100%");         /**   عرض الإطار العنوان  */
        $border_width_bill         = (isset($array["header_bill_border_width"]))?$array["header_bill_border_width"]:(($check_type)?$PrinterTemplate->header_bill_border_width:"0px");          /**   عرض الإطار العنوان  */
        $border_style_bill         = (isset($array["header_bill_border_style"]))?$array["header_bill_border_style"]:(($check_type)?$PrinterTemplate->header_bill_border_style:"solid");        /**   شكل إطار العنوان  */
        $border_color_bill         = (isset($array["header_bill_border_color"]))?$array["header_bill_border_color"]:(($check_type)?$PrinterTemplate->header_bill_border_color:"transparent");        /**   لون إطار العنوان  */
        $padding_right_bill        = (isset($array["header_bill_right"]))?$array["header_bill_right"]:(($check_type)?$PrinterTemplate->header_bill_right:"0px");          /**   المحيط التوسع  */
        $padding_left_bill         = (isset($array["header_bill_left"]))?$array["header_bill_left"]:(($check_type)?$PrinterTemplate->header_bill_left:"0px");          /**   المحيط التوسع   */
        $padding_top_bill          = (isset($array["header_bill_top"]))?$array["header_bill_top"]:(($check_type)?$PrinterTemplate->header_bill_top:"0px");          /**   المحيط التوسع   */
        $padding_bottom_bill       = (isset($array["header_bill_bottom"]))?$array["header_bill_bottom"]:(($check_type)?$PrinterTemplate->header_bill_bottom:"0px");          /**   المحيط التوسع   */
        $capital_text_bill         = (isset($array["header_bill_letter"]))?$array["header_bill_letter"]:(($check_type)?$PrinterTemplate->header_bill_letter:"captalize");    /**   أول حرف كبير   */
        $position_bill             = (isset($array["header_bill_position"]))?$array["header_bill_position"]:(($check_type)?$PrinterTemplate->header_bill_position:"relative");     /**   مكان توضع العنصر  */
        $position_top_bill         = (isset($array["header_bill_padding_top"]))?$array["header_bill_padding_top"]:(($check_type)?$PrinterTemplate->header_bill_padding_top:"0px");        /**   محاذات العنصر العائم  */
        $position_bottom_bill      = (isset($array["header_bill_padding_bottom"]))?$array["header_bill_padding_bottom"]:(($check_type)?$PrinterTemplate->header_bill_padding_bottom:"0px");          /**   محاذات العنصر العائم  */
        $position_left_bill        = (isset($array["header_bill_padding_left"]))?$array["header_bill_padding_left"]:(($check_type)?$PrinterTemplate->header_bill_padding_left:"0px");          /**   محاذات العنصر العائم  */
        $position_right_bill       = (isset($array["header_bill_padding_right"]))?$array["header_bill_padding_right"]:(($check_type)?$PrinterTemplate->header_bill_padding_right:"0px");         /**   محاذات العنصر العائم  */
        
        // image 
        $enable_img_align            = (isset($array["align_image_header"]))?$array["align_image_header"]:(($check_type)?$PrinterTemplate->align_image_header:"right");                           /**   مكان توضع الصورة في الترويسة */
        $position_img_align          = (isset($array["position_img_header"]))?$array["position_img_header"]:(($check_type)?$PrinterTemplate->position_img_header:"right");                         /**   مكان توضع الصورة في جزء الصورة في الترويسة*/
        $img_width                   = (isset($array["header_image_width"]))?$array["header_image_width"]:(($check_type)?$PrinterTemplate->header_image_width:"100"  );                          /**   عرض الصورة    */
        $img_height                  = (isset($array["header_image_height"]))?$array["header_image_height"]:(($check_type)?$PrinterTemplate->header_image_height:"100"  );                        /**   ارتفاع الصورة */ 
        $border_width_img            = (isset($array["header_image_border_width"]))?$array["header_image_border_width"]:(($check_type)?$PrinterTemplate->header_image_border_width:"0px"  );            /**   عرض الإطار  الصورة  */
        $background_img              = (isset($array["header_box_image_background"]))?$array["header_box_image_background"]:(($check_type)?$PrinterTemplate->header_box_image_background:"transparent");  /**   خلفية الصورة */ 
        $border_style_img            = (isset($array["header_image_border_style"]))?$array["header_image_border_style"]:(($check_type)?$PrinterTemplate->header_image_border_style:"solid");            /**   شكل إطار الصورة  */
        $border_color_img            = (isset($array["header_image_border_color"]))?$array["header_image_border_color"]:(($check_type)?$PrinterTemplate->header_image_border_color:"transparent");            /**   لون إطار الصورة  */
        $border_radius_img           = (isset($array["header_image_border_radius"]))?$array["header_image_border_radius"]:(($check_type)?$PrinterTemplate->header_image_border_radius:"0px");            /**   تصميم إطار الصورة */
        $img_box_color               = (isset($array["header_box_image_color"]))?$array["header_box_image_color"]:(($check_type)?$PrinterTemplate->header_box_image_color:"transparent");   
        $img_box_height              = (isset($array["header_image_box_height"]))?$array["header_image_box_height"]:(($check_type)?$PrinterTemplate->header_image_box_height:"100%");
        
        // image box
        
        $enable_img_box              = (isset($array["header_image_view"]))?(($array["header_image_view"] === "true")?true:false):(($check_type)?(($PrinterTemplate->header_image_view == 1)?true:false):true);
        $enable_img                  = (isset($array["header_image_view"]))?(($array["header_image_view"] === "true")?true:false):(($check_type)?(($PrinterTemplate->header_image_view == 1)?true:false):true);
        $position_box_align          = (isset($array["position_box_header_align"]))?$array["position_box_header_align"]:(($check_type)?$PrinterTemplate->position_box_header_align:"center"  );
        $img_box_width               = (isset($array["header_image_box_width"]))?$array["header_image_box_width"]:(($check_type)?$PrinterTemplate->header_image_box_width:"32.333%" );                 /**   لون إطار العنوان  */
        $img_box_margin              = (isset($array["header_image_box_margin"]))?$array["header_image_box_margin"]:(($check_type)?$PrinterTemplate->header_image_box_margin:" auto 0%" );                /**   لون إطار العنوان  */
        $border_width_img_box        = (isset($array["header_image_box_border_width"]))?$array["header_image_box_border_width"]:(($check_type)?$PrinterTemplate->header_image_box_border_width:"0px" );         /**   عرض الإطار العنوان  */
        $border_style_img_box        = (isset($array["header_image_box_border_style"]))?$array["header_image_box_border_style"]:(($check_type)?$PrinterTemplate->header_image_box_border_style:"solid" );       /**   شكل إطار العنوان  */
        $border_color_img_box        = (isset($array["header_image_box_border_color"]))?$array["header_image_box_border_color"]:(($check_type)?$PrinterTemplate->header_image_box_border_color:"transparent" );        /**   لون إطار العنوان  */
        $border_radius_img_box       = (isset($array["header_image_box_border_radius"]))?$array["header_image_box_border_radius"]:(($check_type)?$PrinterTemplate->header_image_box_border_radius:"0px" );                     /**   لون إطار العنوان  */
        $background_img_box          = (isset($array["header_image_box_background"]))?$array["header_image_box_background"]:(($check_type)?$PrinterTemplate->header_image_box_background:"transparent" );                  /**   عرض الإطار العنوان  */
        
        
        
        // header box
        $page_number_view            = (isset($array["page_number_view"]))?(($array["page_number_view"] === "true")?true:false):(($check_type)?(($PrinterFooterTemplate->page_number_view == 1)?true:false):true);
        $enable_header_box           = (isset($array["header_view"]))?(($array["header_view"] === "true")?true:false):(($check_type)?(($PrinterTemplate->header_view == 1)?true:false):true);
        $header_box_width            = (isset($array["header_box_width"]))?$array["header_box_width"]:(($check_type)?$PrinterTemplate->header_box_width:"32.333%");         /**   لون إطار العنوان  */
        $border_width_header_box     = (isset($array["header_box_border_width"]))?$array["header_box_border_width"]:(($check_type)?$PrinterTemplate->header_box_border_width:"0px");          /**   عرض الإطار العنوان  */
        $border_style_header_box     = (isset($array["header_box_border_style"]))?$array["header_box_border_style"]:(($check_type)?$PrinterTemplate->header_box_border_style:"solid");        /**   شكل إطار العنوان  */
        $border_color_header_box     = (isset($array["header_box_border_color"]))?$array["header_box_border_color"]:(($check_type)?$PrinterTemplate->header_box_border_color:"transparent");        /**   لون إطار العنوان  */
        $border_radius_header_box    = (isset($array["header_box_border_radius"]))?$array["header_box_border_radius"]:(($check_type)?$PrinterTemplate->header_box_border_radius:"0px");         /**   لون إطار العنوان  */
        $background_header_box       = (isset($array["header_box_background"]))?$array["header_box_background"]:(($check_type)?$PrinterTemplate->header_box_background:"transparent");       /**   عرض الإطار العنوان  */
        

        
        //$%^ other box
        $enable_other_box           = (isset($array["header_other_view"]))?(($array["header_other_view"] === "true")?true:false):(($check_type)?(($PrinterTemplate->header_other_view == 1)?true:false):true);        /**   لون إطار العنوان  */
        $other_box_width            = (isset($array["header_other_width"]))?$array["header_other_width"]:(($check_type)?$PrinterTemplate->header_other_width:"32.333%");                          /**   لون إطار العنوان  */
        $border_width_other_box     = (isset($array["header_other_border_width"]))?$array["header_other_border_width"]:(($check_type)?$PrinterTemplate->header_other_border_width:"0px");          /**   عرض الإطار العنوان  */
        $border_style_other_box     = (isset($array["header_other_border_style"]))?$array["header_other_border_style"]:(($check_type)?$PrinterTemplate->header_other_border_style:"solid");        /**   شكل إطار العنوان  */
        $border_color_other_box     = (isset($array["header_other_border_color"]))?$array["header_other_border_color"]:(($check_type)?$PrinterTemplate->header_other_border_color:"transparent");        /**   لون إطار العنوان  */
        $border_radius_other_box    = (isset($array["header_other_border_radius"]))?$array["header_other_border_radius"]:(($check_type)?$PrinterTemplate->header_other_border_radius:"0px");         /**   لون إطار العنوان  */
        $background_other_box       = (isset($array["other_background_header"]))?$array["other_background_header"]:(($check_type)?$PrinterTemplate->other_background_header:"transpar   ent");       /**   عرض الإطار العنوان  */




        // rows lines 
        $row_line_enable            = (isset($array["header_line_view"]))?(($array["header_line_view"] === "true")?true:false):(($check_type)?(($PrinterTemplate->header_line_view == 1)?true:false):true);       
        $row_line_width             = (isset($array["header_line_width"]))?$array["header_line_width"]:(($check_type)?$PrinterTemplate->header_line_width:"50%" ) ;
        $row_line_height            = (isset($array["header_line_height"]))?$array["header_line_height"]:(($check_type)?$PrinterTemplate->header_line_height:"1px" ) ;
        $row_line_color             = (isset($array["header_line_color"]))?$array["header_line_color"]:(($check_type)?$PrinterTemplate->header_line_color:"black" ) ;
        $row_line_radius            = (isset($array["header_line_radius"]))?$array["header_line_radius"]:(($check_type)?$PrinterTemplate->header_line_radius:"0px" ) ;
        $row_line_border_width      = (isset($array["header_line_border_width"]))?$array["header_line_border_width"]:(($check_type)?$PrinterTemplate->header_line_border_width:"1px" ) ;
        $row_line_border_style      = (isset($array["header_line_border_style"]))?$array["header_line_border_style"]:(($check_type)?$PrinterTemplate->header_line_border_style:"solid" ) ;
        $row_line_border_color      = (isset($array["header_line_border_color"]))?$array["header_line_border_color"]:(($check_type)?$PrinterTemplate->header_line_border_color:"black" ) ;
        $row_line_margin_top        = (isset($array["header_line_margin_top"]))?$array["header_line_margin_top"]:(($check_type)?$PrinterTemplate->header_line_margin_top:"10px" ) ;
        
        // body ###########
        $top_table_section                          = (isset($array["top_table_section"]))?(($array["top_table_section"] === "true")?true:false):(($check_type)?(($PrinterContentTemplate->top_table_section === 1)?true:false):true); 
        $bold_left_invoice_info_number              = (isset($array["bold_left_invoice_info_number"]))?(($array["bold_left_invoice_info_number"] === "on")?true:false) :true ; 
        $bold_left_invoice_info_project             = (isset($array["bold_left_invoice_info_project"]))?(($array["bold_left_invoice_info_project"] === "on")?true:false) :true ; 
        $bold_left_invoice_info_date                = (isset($array["bold_left_invoice_info_date"]))?(($array["bold_left_invoice_info_date"] === "on")?true:false) :true ; 
        $left_size                                  =  (isset($array["class_width_left"]))?$array["class_width_left"]:(($check_type)?$PrinterContentTemplate->class_width_left:"4");
        $right_size                                 =  (isset($array["class_width_right"]))?$array["class_width_right"]:(($check_type)?$PrinterContentTemplate->class_width_right:"8");
        $class_width_left                           = "col-md-".$left_size;
        $class_width_right                          = "col-md-".$right_size;
        $left_left_size                             = (isset($array["class_width_left_right"]))?$array["class_width_left_right"]:(($check_type)?$PrinterContentTemplate->class_width_left_right:"4");
        $left_right_size                            = (isset($array["class_width_right_right"]))?$array["class_width_right_right"]:(($check_type)?$PrinterContentTemplate->class_width_right_right:"8");
        $class_width_left_right                     = "col-md-". $left_left_size ;
        $class_width_right_right                    = "col-md-". $left_right_size ;
        $bold_left_invoice_info_customer_number     = (isset($array["bold_left_invoice_info_customer_number"]))?(($array["bold_left_invoice_info_customer_number"] === "on")?true:false):true;
        $bold_left_invoice_info_customer_address    = (isset($array["bold_left_invoice_info_customer_address"]))?(($array["bold_left_invoice_info_customer_address"] === "on")?true:false):true;
        $bold_left_invoice_info_customer_mobile     = (isset($array["bold_left_invoice_info_customer_mobile"]))?(($array["bold_left_invoice_info_customer_mobile"] === "on")?true:false):true;
        $bold_left_invoice_info_customer_tax        = (isset($array["bold_left_invoice_info_customer_tax"]))?(($array["bold_left_invoice_info_customer_tax"] === "on")?true:false):true;
      
        $repeat_content_top                         = (isset($array["repeat_content_top"]))?(($array["repeat_content_top"] === "on")?true:false):(($check_type)?(($PrinterContentTemplate->repeat_content_top === 1)?true:false):true);
        $invoice_no                                 = (isset($array["invoice_no"]))?$array["invoice_no"]:(($check_type)?$PrinterContentTemplate->invoice_no:"Invoice No :") ;
        $project_no                                 = (isset($array["project_no"]))?$array["project_no"]:(($check_type)?$PrinterContentTemplate->project_no:"Project No :") ;
        $customer_no                                = (isset($array["customer_no"]))?$array["customer_no"]:(($check_type)?$PrinterContentTemplate->customer_name:"Customer Name :") ;
        
        $date_name                                  = (isset($array["date_name"]))?$array["date_name"]:(($check_type)?$PrinterContentTemplate->date_name:"Date :") ;
        $address_name                               = (isset($array["address_name"]))?$array["address_name"]:(($check_type)?$PrinterContentTemplate->address_name:"Address Name :") ;
        $mobile_name                                = (isset($array["mobile_name"]))?$array["mobile_name"]:(($check_type)?$PrinterContentTemplate->mobile_name:"Mobile Name :") ;
        $tax_name                                   = (isset($array["tax_name"]))?$array["tax_name"]:(($check_type)?$PrinterContentTemplate->tax_name:"Tax :") ;

        
        // .................................content
        $top_align                   = "center";
        $top_width                   = "100%";
        if(isset($edit_type)){
            $left_top_table              =   $PrinterTemplateContain->left_top_content ;
            $right_top_table             =   $PrinterTemplateContain->right_top_content ;
            $header_text                 =   $PrinterTemplateContain->left_header_title  ;
            $tax_text                    =   $PrinterTemplateContain->center_top_header_title;
            $address_text                =   $PrinterTemplateContain->center_middle_header_title;
            $invoice_text                =   $PrinterTemplateContain->center_last_header_title; 
            $img_url                     =   ($PrinterTemplateContain->image_url)?$PrinterTemplateContain->image_url:"https://thumbs.dreamstime.com/b/invoice-linear-icon-modern-outline-invoice-logo-concept-whit-invoice-linear-icon-modern-outline-invoice-logo-concept-white-133517211.jpg";
        }else{

            if(isset($array["body_top_left_send_type"])){
                if( $array["body_top_left_send_type"]  == "value" ){
                    $left_top_table            = (isset($array["body_top_left"]))?$array["body_top_left"]:"";
                }else{
                    $body_left_top_layout      = \App\InvoiceLayout::find($array["body_top_left"]);
                    
                    $left_top_table           = (isset($body_left_top_layout))?$body_left_top_layout->purchase_text:"";
                }
            }else{
                $left_top_table               = (isset($array["body_top_left"]))?$array["body_top_left"]:"";
            }
             // footer ** center top
            if(isset($array["body_top_right_send_type"])){
                if( $array["body_top_right_send_type"]  == "value" ){
                    $right_top_table                    = (isset($array["body_top_right"]))?$array["body_top_right"]:"";
                }else{
                    $body_right_top_layout    = \App\InvoiceLayout::find($array["body_top_right"]);
                    $right_top_table             = (isset($body_right_top_layout))?$body_right_top_layout->purchase_footer:"";
                }
            }else{  
                $right_top_table                   = (isset($array["body_top_right"]))?$array["body_top_right"]:"";
            }
            // header ** left
            if(isset($array["left_header_send_type"])){
                if( $array["left_header_send_type"]  == "value" ){
                    $header_text                 = (isset($array["left_header"]))?$array["left_header"]:"header Title";
                }else{
                    $header_left_layout          = \App\InvoiceLayout::find($array["left_header"]);
                    $header_text                 = (isset($header_left_layout))?$header_left_layout->header_text:"header Title";
                }
            }else{
                $header_text                 = (isset($array["left_header"]))?$array["left_header"]:"header Title";
            }
            // header ** center top
            if(isset($array["center_top_header_send_type"])){
                if( $array["center_top_header_send_type"]  == "value" ){
                    $tax_text                    = (isset($array["center_top_header"]))?$array["center_top_header"]:"Tax : ";
                }else{
                    $header_center_top_layout    = \App\InvoiceLayout::find($array["center_top_header"]);
                    $tax_text                    = (isset($header_center_top_layout))?$header_center_top_layout->sub_heading_line1:"Tax : ";
                }
            }else{  
                $tax_text                   = (isset($array["center_top_header"]))?$array["center_top_header"]:"Tax : ";
            }
            // header ** center middle
            if(isset($array["center_middle_header_send_type"])){
                if( $array["center_middle_header_send_type"]  == "value" ){
                    $address_text                 = (isset($array["center_middle_header"]))?$array["center_middle_header"]:"Address : ";
                }else{
                    $header_center_middle_layout = \App\InvoiceLayout::find($array["center_middle_header"]);
                    $address_text                =  (isset($header_center_middle_layout))?$header_center_middle_layout->sub_heading_line2:"Address : ";
                }
            }else{  
                $address_text              = (isset($array["center_middle_header"]))?$array["center_middle_header"]:"Address : ";
            }
            // header ** center last
            if(isset($array["center_last_header_send_type"])){
                if( $array["center_last_header_send_type"]  == "value" ){
                    $invoice_text                    = (isset($array["center_last_header"]))?$array["center_last_header"]:"Invoice";
                }else{
                    $header_center_last_layout    = \App\InvoiceLayout::find($array["center_last_header"]);
                    $invoice_text                 = $header_center_last_layout->sub_heading_line3;
                }
            }else{  
                $invoice_text         = (isset($array["center_last_header"]))?$array["center_last_header"]:"Invoice";
            }
            $img_url                     =   isset($PrinterTemplateContain->image_url)?$PrinterTemplateContain->image_url:"https://thumbs.dreamstime.com/b/invoice-linear-icon-modern-outline-invoice-logo-concept-whit-invoice-linear-icon-modern-outline-invoice-logo-concept-white-133517211.jpg";
        }
        
    }
    $header_position             = "left";
    // $img_url                     = "https://img.freepik.com/free-vector/leaf-maple-icon-logo-design_474888-2154.jpg?size=338&ext=jpg&ga=GA1.1.632798143.1717653600&semt=sph";
 
    
?>  
<style>
        /* Text  header */
        .title-header{
            text-align:     {{ $align . " !important"  }};
            font-size:      {{ $font_size . " !important" }}; 
            font-weight:    {{ $font_weight . " !important" }}; 
            width:          {{ $width_header . " !important" }}; 
            border-width:   {{ $border_width . " !important" }};     
            border-color:   {{ $border_color . " !important" }};     
            border-style:   {{ $border_style . " !important" }};     
            padding-left:   {{ $padding_left . " !important" }};     
            padding-right:  {{ $padding_right . " !important" }};     
            padding-top:    {{ $padding_top . " !important" }};     
            padding-bottom: {{ $padding_bottom . " !important" }};     
            text-transform: {{ $capital_text . " !important" }};
            position:       {{ $header_text_position  . " !important" }};
            top:            {{ $header_text_top       . " !important" }};
            left:           {{ $header_text_left      . " !important" }};
            right:          {{ $header_text_right     . " !important" }};
            bottom:         {{ $header_text_bottom    . " !important" }};
            }
        /* Tax header */
        .tax_number{
            position:       {{ $box_position . " !important" }};
            border-width:   {{ $border_width_box_tax . " !important" }};     
            border-color:   {{ $border_color_box_tax . " !important" }};     
            border-style:   {{ $border_style_box_tax . " !important" }};     
            top:            {{ $box_tax_top   . " !important" }};
            left:           {{ $box_tax_left  . " !important" }};
            right:          {{ $box_tax_right . " !important" }};
            bottom:         {{ $box_tax_bottom . " !important" }};
        }
            
        /* Tax */
        .tax{
            text-align:     {{ $align_tax . " !important" }};
            font-size:      {{ $font_size_tax . " !important" }}; 
            border-width:   {{ $border_width_tax . " !important" }};     
            border-color:   {{ $border_color_tax . " !important" }};     
            border-style:   {{ $border_style_tax . " !important" }};     
            padding-left:   {{ $padding_left_tax . " !important" }};     
            padding-right:  {{ $padding_right_tax . " !important" }};     
            padding-top:    {{ $padding_top_tax . " !important" }};     
            padding-bottom: {{ $padding_bottom_tax . " !important" }};     
            text-transform: {{ $capital_text_tax . " !important" }};
            width:          {{ $width_tax . " !important" }}; 
            position:       {{ $position_tax . " !important" }};
            top:            {{ $position_top_tax . " !important" }};
            left:           {{ $position_left_tax . " !important" }};
            right:          {{ $position_right_tax . " !important"}};
            bottom:         {{ $position_bottom_tax . " !important" }};
        }
        /* Address */
        .address{
            text-align:     {{ $align_address . " !important" }};
            font-size:      {{ $font_size_address . " !important" }}; 
            border-width:   {{ $border_width_address . " !important" }};     
            border-color:   {{ $border_color_address . " !important" }};     
            border-style:   {{ $border_style_address . " !important" }};     
            padding-left:   {{ $padding_left_address . " !important" }};     
            padding-right:  {{ $padding_right_address . " !important" }};     
            padding-top:    {{ $padding_top_address . " !important" }};     
            padding-bottom: {{ $padding_bottom_address . " !important" }};     
            text-transform: {{ $capital_text_address . " !important" }};
            width:          {{ $width_address . " !important" }}; 
            position:       {{ $position_address . " !important" }};
            top:            {{ $position_top_address . " !important" }};
            left:           {{ $position_left_address . " !important" }};
            right:          {{ $position_right_address . " !important" }};
            bottom:         {{ $position_bottom_address . " !important" }};
        }
        /* bill */
        .name_of_bill{
            text-align:     {{ $align_bill . " !important" }};
            font-size:      {{ $font_size_bill . " !important" }}; 
            border-width:   {{ $border_width_bill . " !important" }};     
            border-color:   {{ $border_color_bill . " !important" }};     
            border-style:   {{ $border_style_bill . " !important" }};     
            padding-left:   {{ $padding_left_bill . " !important" }};     
            padding-right:  {{ $padding_right_bill . " !important" }};     
            padding-top:    {{ $padding_top_bill . " !important" }};     
            padding-bottom: {{ $padding_bottom_bill . " !important" }};     
            text-transform: {{ $capital_text_bill . " !important" }};
            width:          {{ $width_bill . " !important" }}; 
            position:       {{ $position_bill . " !important" }};
            top:            {{ $position_top_bill . " !important" }};
            left:           {{ $position_left_bill . " !important" }};
            right:          {{ $position_right_bill . " !important" }};
            bottom:         {{ $position_bottom_bill . " !important" }};
        }
        /* table */
        .table_header{
            width:             {{ $header_table_width . " !important" }};
            background-color:  {{ $header_table_background . " !important" }};     
            border-width:      {{ $border_width_table . " !important" }};     
            border-color:      {{ $border_style_table . " !important" }};     
            border-style:      {{ $border_color_table . " !important" }};     
            border-radius:     {{ $header_border_radius_table . " !important" }};     
                 
                 
                 
                 
                 

        }
        /* image box */
        .images_box{
            border-width:      {{ $border_width_img . " !important" }};     
            border-color:      {{ $border_style_img . " !important" }};     
            border-style:      {{ $border_color_img . " !important" }};     
            border-radius:     {{ $border_radius_img . " !important" }};
            background-color:  {{ $background_img . " !important" }};
            width:             {{ $img_width . " !important" }};
            height:            {{ $img_height . " !important" }};
            text-align:        {{ $position_box_align . " !important" }};
            margin:            {{ $img_box_margin . " !important" }};
            background-color:  {{ $img_box_color . " !important" }};
            height:            {{ $img_box_height . " !important" }} ;
            
            }
        .img-boxs{
             
            width:             {{ $img_box_width . " !important" }};
            text-align:        {{ $position_img_align . " !important" }};
            border-width:      {{ $border_width_img_box . " !important" }}; 
            border-color:      {{ $border_style_img_box . " !important" }}; 
            border-style:      {{ $border_color_img_box . " !important" }}; 
            border-radius:     {{ $border_radius_img_box . " !important" }};
           
        
          
        }
        .header-box{
            width:             {{ $header_box_width . " !important" }};
            border-width:      {{ $border_width_header_box . " !important" }}; 
            border-color:      {{ $border_style_header_box . " !important" }}; 
            border-style:      {{ $border_color_header_box . " !important" }}; 
            border-radius:     {{ $border_radius_header_box . " !important" }};
        }
        .other-box{
            width:             {{ $other_box_width . " !important" }};
            text-align:        {{ $align_other_header . " !important" }};
            border-width:      {{ $border_width_other_box . " !important" }}; 
            border-color:      {{ $border_style_other_box . " !important" }}; 
            border-style:      {{ $border_color_other_box . " !important" }}; 
            border-radius:     {{ $border_radius_other_box . " !important" }};
        }
        .top{
            text-align: {{ $top_align . " !important" }};
            width: {{ $top_width . " !important" }};

        }
        .row_line{
            width:             {{ $row_line_width . " !important" }};
            height:            {{ $row_line_height . " !important" }};
            background-color:  {{ $row_line_color . " !important" }}; 
            border-radius:     {{ $row_line_radius . " !important" }}; 
            border-width:      {{ $row_line_border_width . " !important" }}; 
            border-style:      {{ $row_line_border_style . " !important" }}; 
            border-color:      {{ $row_line_border_color . " !important" }}; 
            margin-top:        {{ $row_line_margin_top . " !important" }}; 
        }
        
</style>
 
 
{{-- TITLE SECTION ****$% --}}
@if($style_box == "h1")
            @if($enable_img == true && $enable_img_align == "top")  
                    <div class="img-boxs">
                        <div class="images_box" >
                            <img src="{{$img_url}}" id="header_img_logo"  width="{{$img_width}}" height="{{$img_height}}" alt="LOGO"> 
                             
                        </div>
                    </div>
            @endif
            @if($enable_header_box == true)
                <h1 class="title-header"  >
                    {!! $header_text !!}  
                </h1>
            @endif
            @if($enable_img == true && $enable_img_align == "bottom") 
                <div class="img-boxs">
                    <div class="images_box" >
                        <img src="{{$img_url}}" id="header_img_logo"  width="{{$img_width}}" height="{{$img_height}}" alt="LOGO"> 
                         
                    </div>
                </div>
            @endif
            @if($enable_other_box == true)
                <div class="tax_number">
                    <div class="tax">{!! $tax_text !!}</div>
                    <div class="address">{!! $address_text !!}</div>
                    <div class="name_of_bill">{!! $invoice_text !!}</div>
                </div>
            @endif
            @if($row_line_enable == true)
                <hr class="row_line">
            @endif
            
@elseif($style_box == "table")
    <table class="table_header">
        <tbody>
            <tr> 
                @if($enable_img == true && $enable_img_align == "left")
                    <td class="img-boxs">
                        <div class="images" >
                            <img src="{{$img_url}}"  id="header_img_logo" width="{{$img_width}}" height="{{$img_height}}" alt="LOGO"> 
                             
                        </div>
                    </td>
                @endif

                @if( $header_position == "left")
                    @if($enable_header_box == true)
                        <td class="header-box">
                            <div class="title-header" >
                                {!! $header_text !!}
                            </div>
                        </td>
                    @endif
                    @if($enable_img == true && $enable_img_align == "center")
                        <td class="img-boxs">
                            <div class="images" >
                                <img src="{{$img_url}}" id="header_img_logo" width="{{$img_width}}" height="{{$img_height}}" alt="LOGO"> 
                                 
                            </div>
                        </td>
                    @endif
                    @if($enable_other_box == true)
                        <td class="other-box">
                            <div class="tax_number">
                                <div class="tax">{!! $tax_text !!}</div>
                                <div class="address">{!! $address_text !!}</div>
                                <div class="name_of_bill">{!! $invoice_text !!}</div>
                            </div>
                        </td>
                    @endif
                @elseif( $header_position == "right")
                    @if($enable_other_box == true)
                        <td class="other-box">
                            <div class="tax_number">
                                <div class="tax">{!! $tax_text !!}</div>
                                <div class="address">{!! $address_text !!}</div>
                                <div class="name_of_bill">{!! $invoice_text !!}</div>
                            </div>
                        </td>
                    @endif
                    
                    @if($enable_img == true && $enable_img_align == "center")
                        <td class="img-boxs">
                            <div class="images" >
                                <img src="{{$img_url}}" id="header_img_logo" width="{{$img_width}}" height="{{$img_height}}" alt="LOGO"> 
                                 
                            </div>
                       </td>
                    @endif
                    @if($enable_header_box == true)           
                        <td class="header-box">
                            <div class="title-header" >
                                {!! $header_text !!}
                            </div>
                        </td>
                    @endif
                        
                
                @endif
                @if($enable_img == true && $enable_img_align == "right")
                    <td class="img-boxs">
                        <div class="images" >
                            <img src="{{$img_url}}" id="header_img_logo"  width="{{$img_width}}" height="{{$img_height}}" alt="LOGO"> 
                             
                        </div>
                    </td>
                @endif
            </tr>
        </tbody>
    </table>
    @if($row_line_enable == true)
        <hr class="row_line">
    @endif
    
@else
    @if($enable_img == true && $enable_img_align == "top")  
        <div class="img-boxs">
            <div class="images_box" >
                <img src="{{$img_url}}"  width="{{$img_width}}" height="{{$img_height}}" alt="LOGO"> 
            </div>
        </div>
    @endif
    @if($enable_header_box == true)
        <div class="title-header"  >
        {!! $header_text !!}  
        </div>
    @endif
    @if($enable_img == true && $enable_img_align == "bottom") 
        <div class="img-boxs">
            <div class="images_box" >
                <img src="{{$img_url}}"  width="{{$img_width}}" height="{{$img_height}}" alt="LOGO"> 
            </div>
        </div>
    @endif
    @if($enable_other_box == true)
        <div class="tax_number">
            <div class="tax">{!! $tax_text !!}</div>
            <div class="address">{!! $address_text !!}</div>
            <div class="name_of_bill">{!! $invoice_text !!}</div>
        </div>
    @endif
    @if($row_line_enable == true)
        <hr class="row_line">
    @endif
     
@endif

@if($repeat_content_top == true)
 
    @php
        $data     = ["1"];
        $data     = (isset($transaction))?$transaction:\App\Transaction::where("type","sale")->first();
        
        $currency = \App\Models\ExchangeRate::where("source",1)->first();
    @endphp
    {{-- top section --}}
    @if($top_table_section == true)
        <div class="top_table"  >
            <table class="top_table_row" style="padding: 0px !important;">
                <tbody style="padding: 0px !important;" >
                    <tr style="padding: 0px !important;">
                        {{-- left table --}}
                        <td class="left_top_table" style="padding : 0px !important">
                            <table class="left_top_table" style="border:0px solid black !important">
                                <tbody  class="left_invoice_info" style="border:0px solid black !important">
                                    <tr>
                                        <td colspan="2">{!! $left_top_table !!}</td>
                                    </tr>
                                    @if($bold_left_invoice_info_number == true)
                                        <tr>
                                            <td class="{{$class_width_left}} bold_left_invoice_info">{{ $invoice_no  }}</td>
                                            <td class="{{$class_width_right}} bold_right_invoice_info">{{($data != null)?(($data->invoice_no != null)?$data->invoice_no:$data->ref_no):""}}</td>
                                        </tr>    
                                    @endif
                                    @if($bold_left_invoice_info_project == true)
                                        <tr>
                                            <td class="{{$class_width_left}} bold_left_invoice_info">{{ $project_no  }}</td>
                                            <td class="{{$class_width_right}} bold_right_invoice_info">{{($data != null)?$data->project_no:""}}</td>
                                        </tr>    
                                    @endif
                                    @if($bold_left_invoice_info_date == true)
                                        <tr>
                                            <td class="{{$class_width_left}} bold_left_invoice_info">{{ $date_name  }}</td>
                                            <td class="{{$class_width_right}} bold_right_invoice_info">{{($data != null)?   @format_date($data->transaction_date)  :"&nbsp;"}}</td>
                                        </tr>    
                                    @endif
                                    @if($page_number_view == true)
                                        <tr>
                                            <td class="{{$class_width_left}} bold_left_invoice_info">{{ "Total Page  "  }}</td>
                                            <td class="{{$class_width_right}} bold_right_invoice_info"><div class="title_number">{{(isset($totalPages))?$totalPages:""}}<div></td>
                                        </tr> 
                                    @endif   
                                </tbody>
                            </table>    
                        </td>   
                        {{-- right table --}}                     
                        <td class="right_top_table" style="padding:0px  !important">
                            <table class="right_top_table" style="border:0px solid black !important">
                                <tbody class="right_invoice_info" style="border:0px solid black !important">
                                    <tr>
                                        <td colspan="2">{!! $right_top_table !!}</td>
                                    </tr>
                                    @if($bold_left_invoice_info_customer_number == true )
                                        @php
                                            if($data){ 
                                                if($data->contact->first_name != null){
                                                    $arabic = new ArPHP\I18N\Arabic();
                                                    $p      = $arabic->arIdentify($data->contact->first_name);
                                                    if(count($p)>0){
                                                        for ($i = count($p)-1; $i >= 0; $i-=2) {
                                                            $utf8ar = $arabic->utf8Glyphs(substr($data->contact->first_name, $p[$i-1], $p[$i] - $p[$i-1]));
                                                            $name   = substr_replace($data->contact->first_name, $utf8ar, $p[$i-1], $p[$i] - $p[$i-1]);
                                                        }
                                                        $name = "arabic";
                                                    }else{
                                                        
                                                        $name = $data->contact->first_name;
                                                    }
                                                
                                                }else{
                                                    $name = "";
                                                }
                                            }else{
                                                    $name = "";
                                            }
                                        @endphp
                                        <tr>
                                            <td class="{{$class_width_left}} bold_left_invoice_info">{{ $customer_no  }}</td>
                                            @if($name == "arabic")
                                                <td class="{{$class_width_right_right}} ">{!! ($data)?(($data->contact->first_name)? $data->contact->first_name :" "):"" !!}</td>
                                            @else
                                                <td class="{{$class_width_right_right}} bold_right_invoice_info">{!! ($data)?(($data->contact->first_name)? $data->contact->first_name :" "):"" !!}</td>
                                            @endif
                                        </tr>
                                    @endif 
                                    @if($bold_left_invoice_info_customer_address == true )
                                        <tr>
                                            <td class="{{$class_width_left_right}} bold_left_invoice_info">{{ $address_name  }}</td>
                                            <td class="{{$class_width_right_right}} bold_right_invoice_info">{{ ($data)?(($data->contact->address != null)?$data->contact->address:""):""}}</td>
                                        </tr>
                                    @endif 
                                    @if($bold_left_invoice_info_customer_mobile == true )
                                        <tr>
                                            <td class="{{$class_width_left_right}} bold_left_invoice_info">{{ $mobile_name  }}</td>
                                            <td class="{{$class_width_right_right}} bold_right_invoice_info">{{ ($data)?(($data->contact->mobile != null)?$data->contact->mobile:""):""}}</td>
                                        </tr> 
                                    @endif 
                                    @if($bold_left_invoice_info_customer_tax == true )
                                        <tr>
                                            <td class="{{$class_width_left_right}} bold_left_invoice_info">{{ $tax_name  }}</td>
                                            <td class="{{$class_width_right_right}} bold_right_invoice_info">{{ ($data)?(($data->contact->tax_number != null)?$data->contact->tax_number:""):""}}</td>
                                        </tr> 
                                    @endif 
                                </tbody>
                            </table>    
                        </td>                        
                    </tr>
                </tbody>
            </table>
        </div>
    @endif
@endif
 