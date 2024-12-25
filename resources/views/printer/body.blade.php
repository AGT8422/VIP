

<?php 
    /**
     * ******************************************** * 
     * here the content of table depending on $data * 
     * ******************************************** *
    */

    // $data = ["1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1"];
    $data     = ["1"];
    $data     = (isset($transaction))?$transaction:\App\Transaction::where("type","sale")->first();
    if($data==null){
    $lis   = [];
    $lis[] = [    "item_tax"                   => 0,
                  "unit_price"                 => 0,
                  "line_discount_amount"       => 0,
                  "unit_price_inc_tax"         => 0,
                  "unit_price_before_discount" => 0,
                  "quantity"                   => 0,
                  "sell_line_note"             => "note",
                  "product"                    => [ "name"                 => "Test Product",
                                                    "sku"                  => "6123363743673",
                                                    "product_description"  => "Test Product 7452544",
                                                    "image"                => null,
                                                  ] 
            ];
    $lis[] = [    "item_tax"                   => 0,
                  "unit_price"                 => 0,
                  "line_discount_amount"       => 0,
                  "unit_price_inc_tax"         => 0,
                  "unit_price_before_discount" => 0,
                  "quantity"                   => 0,
                  "sell_line_note"             => "note",
                  "product"                    => [ "name"                 => "Test Product",
                                                    "sku"                  => "6123363743673",
                                                    "product_description"  => "Test Product 7452544",
                                                    "image"                => null,
                                                  ] 
            ];
    $data = [
        "contact"         => [
                "first_name" => "Cash Customer",
                "mobile"     => "0542515452",
                "address"    => "Custmoer Address",
                "tax_number" => "Customer Tax",
        ],
        "invoice_no"        =>"#inv5352",
        "project_no"        =>"#pro67463",
        "transaction_date"  =>"01-08-2024",
        "discount_amount"   => 0,
        "tax_id"            => 1,
        "sell_lines"        =>  $lis,
        "additional_notes"  =>  '',
        
    ];
     
    $data = json_decode(json_encode($data)); 
           
    }
    $currency = \App\Models\ExchangeRate::where("source",1)->first();
    
    if(isset($print_content)){
            
        // table 
        $choose_product_description             = $print_content["choose_product_description"];
        $invoice_no                             = $print_content["invoice_no"];
        $project_no                             = $print_content["project_no"];
        $customer_no                            = $print_content["customer_no"];
        $date_name                              = $print_content["date_name"];
        $address_name                           = $print_content["address_name"];  
        $mobile_name                            = $print_content["mobile_name"]; 
        $tax_name                               = $print_content["tax_name"]; 
        $page_number_view                       = $print_content["page_number_view"];
        $repeat_content_top                     = $print_content["repeat_content_top"];
        $content_width                          = $print_content["content_width"];
        $content_table_border_radius            = $print_content["content_table_border_radius"];
        $content_table_width                    = $print_content["content_table_width"];
        $content_table_th_font_size             = $print_content["content_table_th_font_size"];
        $content_table_th_text_align            = $print_content["content_table_th_text_align"];
        $content_table_th_border_width          = $print_content["content_table_td_border_width"];
        $content_table_th_border_style          = $print_content["content_table_th_border_style"];
        $content_table_th_border_color          = $print_content["content_table_th_border_color"];
        $content_table_th_padding               = $print_content["content_table_th_padding"];
        $content_table_td_font_size             = $print_content["content_table_td_font_size"];
        $content_table_td_text_align            = $print_content["content_table_td_text_align"];
        $content_table_td_border_width          = $print_content["content_table_td_border_width"];
        $content_table_td_border_style          = $print_content["content_table_td_border_style"];
        $content_table_td_border_color          = $print_content["content_table_td_border_color"];
        $content_table_td_padding               = $print_content["content_table_td_padding"];
        $content_table_width_no                 = $print_content["content_table_width_no"];
        $content_table_td_no_font_size          = $print_content["content_table_td_no_font_size"];
        $content_table_td_no_text_align         = $print_content["content_table_td_no_text_align"];
        $content_table_font_weight_no           = $print_content["content_table_font_weight_no"];
        $content_table_width_name               = $print_content["content_table_width_name"];
        $content_table_font_size_name           = $print_content["content_table_font_size_name"];
        $content_table_font_weight_name         = $print_content["content_table_font_weight_name"];
        $content_table_text_align_name          = $print_content["content_table_text_align_name"];

        $content_table_width_qty                = $print_content["content_table_width_qty"];
        $content_table_td_qty_font_size         = $print_content["content_table_td_qty_font_size"];
        $content_table_td_qty_text_align        = $print_content["content_table_td_qty_text_align"];
        $content_table_font_weight_qty          = $print_content["content_table_font_weight_qty"];
        $content_table_width_code               = $print_content["content_table_width_code"];
        $content_table_td_code_font_size        = $print_content["content_table_td_code_font_size"];
        $content_table_td_code_text_align       = $print_content["content_table_td_code_text_align"];
        $content_table_font_weight_code         = $print_content["content_table_font_weight_code"];
        $content_table_width_img                = $print_content["content_table_width_img"];
        $content_table_td_img_font_size         = $print_content["content_table_td_img_font_size"];
        $content_table_td_img_text_align        = $print_content["content_table_td_img_text_align"];
        $content_table_font_weight_img          = $print_content["content_table_font_weight_img"];
        $content_table_width_price              = $print_content["content_table_width_price"];
        $content_table_td_price_font_size       = $print_content["content_table_td_price_font_size"];
        $content_table_td_price_text_align      = $print_content["content_table_td_price_text_align"];
        $content_table_font_weight_price        = $print_content["content_table_font_weight_price"];
        $content_table_width_price_bdi          = $print_content["content_table_width_price_bdi"];
        $content_table_td_price_bdi_font_size   = $print_content["content_table_td_price_bdi_font_size"];
        $content_table_td_price_bdi_text_align  = $print_content["content_table_td_price_bdi_text_align"];
        $content_table_font_weight_price_bdi    = $print_content["content_table_font_weight_price_bdi"];
        $content_table_width_discount           = $print_content["content_table_width_discount"];
        $content_table_td_discount_font_size    = $print_content["content_table_td_discount_font_size"];
        $content_table_td_discount_text_align   = $print_content["content_table_td_discount_text_align"];
        $content_table_font_weight_discount     = $print_content["content_table_font_weight_discount"];
        $content_table_width_price_adi          = $print_content["content_table_width_price_adi"];
        $content_table_td_price_adi_font_size   = $print_content["content_table_td_price_adi_font_size"];
        $content_table_td_price_adi_text_align  = $print_content["content_table_td_price_adi_text_align"];
        $content_table_font_weight_price_adi    = $print_content["content_table_font_weight_price_adi"];
        $content_table_width_price_ade          = $print_content["content_table_width_price_ade"];
        $content_table_td_price_ade_font_size   = $print_content["content_table_td_price_ade_font_size"];
        $content_table_td_price_ade_text_align  = $print_content["content_table_td_price_ade_text_align"];
        $content_table_font_weight_price_ade    = $print_content["content_table_font_weight_price_ade"];
        $content_table_width_subtotal           = $print_content["content_table_width_subtotal"] ;
        $content_table_td_subtotal_font_size    = $print_content["content_table_td_subtotal_font_size"] ;
        $content_table_td_subtotal_text_align   = $print_content["content_table_td_subtotal_text_align"] ;
        $content_table_font_weight_subtotal     = $print_content["content_table_font_weight_subtotal"] ;
        $collapse                               = "collapse" ; 
        // top table
        $top_table_width                        = $print_content["top_table_width"];
        $top_table_margin_bottom                = $print_content["top_table_margin_bottom"];
        $top_table_border_width                 = $print_content["top_table_border_width"];
        $top_table_border_style                 = $print_content["top_table_border_style"];
        $top_table_border_color                 = $print_content["top_table_border_color"];
        $top_table_td_border_width              = $print_content["top_table_td_border_width"];
        $top_table_td_border_style              = $print_content["top_table_td_border_style"];
        $top_table_td_border_color              = $print_content["top_table_td_border_color"];
        // left top
        $left_top_table_width                   = $print_content["left_top_table_width"];
        $left_top_table_text_align              = $print_content["left_top_table_text_align"];
        $left_top_table_font_size               = $print_content["left_top_table_font_size"];
        // right top
        $right_top_table_width                  = $print_content["right_top_table_width"] ;
        $right_top_table_text_align             = $print_content["right_top_table_text_align"] ;
        $right_top_table_font_size              = $print_content["right_top_table_font_size"] ;
        // bottom table
        $bottom_table_width                     = "100%";
        $bottom_table_margin_bottom             = "0px";
        $bottom_table_margin_top                = "1px";
        $bottom_table_border_width              = "3px";
        $bottom_table_border_style              = "solid";
        $bottom_table_border_color              = "black";
        $bottom_table_td_border_width           = "1px";
        $bottom_table_td_border_style           = "solid";
        $bottom_table_td_border_color           = "black";
        // left bottom  
        $left_bottom_table_width                = $print_content["left_bottom_table_width"];
        $left_bottom_table_text_align           = $print_content["left_bottom_table_text_align"];
        $left_bottom_table_font_size            = $print_content["left_bottom_table_font_size"];
        $left_bottom_table_td_bor_width         = $print_content["left_bottom_table_td_bor_width"];
        $left_bottom_table_td_bor_style         = $print_content["left_bottom_table_td_bor_style"];
        $left_bottom_table_td_bor_color         = $print_content["left_bottom_table_td_bor_color"];
        // right bottom  
        $right_bottom_table_width               = $print_content["right_bottom_table_width"];
        $right_bottom_table_text_align          = $print_content["right_bottom_table_text_align"];
        $right_bottom_table_font_size           = $print_content["right_bottom_table_font_size"];
        $right_bottom_table_td_bor_width        = $print_content["right_bottom_table_td_bor_width"];
        $right_bottom_table_td_bor_style        = $print_content["right_bottom_table_td_bor_style"];
        $right_bottom_table_td_bor_color        = $print_content["right_bottom_table_td_bor_color"];
        // .......................................................
        $bill_table_info_width                  = $print_content["bill_table_info_width"];
        $bill_table_info_border_width           = $print_content["bill_table_border_width"];
        $bill_table_info_border_style           = $print_content["bill_table_border_style"];
        $bill_table_info_border_color           = $print_content["bill_table_border_color"];
        $bill_table_margin_bottom               = $print_content["bill_table_margin_bottom"];
        $bill_table_margin_top                  = $print_content["bill_table_margin_top"];
        $bill_table_border_width                = $print_content["bill_table_border_width"];
        $bill_table_border_style                = $print_content["bill_table_border_style"];
        $bill_table_border_color                = $print_content["bill_table_border_color"];
        $bill_table_left_td_width               = $print_content["bill_table_left_td_width"];
        $bill_table_left_td_font_size           = $print_content["bill_table_left_td_font_size"];
        $bill_table_left_td_weight              = $print_content["bill_table_left_td_weight"];
        $bill_table_left_td_text_align          = $print_content["bill_table_left_td_text_align"];
        $bill_table_left_td_border_width        = $print_content["bill_table_left_td_border_width"];
        $bill_table_left_td_border_style        = $print_content["bill_table_left_td_border_style"];
        $bill_table_left_td_border_color        = $print_content["bill_table_left_td_border_color"];
        $bill_table_left_td_padding_left        = $print_content["bill_table_left_td_padding_left"];
        $bill_table_right_td_width              = $print_content["bill_table_right_td_width"];
        $bill_table_right_td_font_size          = $print_content["bill_table_right_td_font_size"];
        $bill_table_right_td_weight             = $print_content["bill_table_right_td_weight"];
        $bill_table_right_td_text_align         = $print_content["bill_table_right_td_text_align"];
        $bill_table_right_td_border_width       = $print_content["bill_table_right_td_border_width"];
        $bill_table_right_td_border_style       = $print_content["bill_table_right_td_border_style"];
        $bill_table_right_td_border_color       = $print_content["bill_table_right_td_border_color"];
        $bill_table_right_td_padding_left       = $print_content["bill_table_right_td_padding_left"];
        $line_bill_table_width                  = $print_content["line_bill_table_width"];
        $line_bill_table_height                 = $print_content["line_bill_table_height"];
        $line_bill_table_color                  = $print_content["line_bill_table_color"];
        $line_bill_table_border_width           = $print_content["line_bill_table_border_width"];
        $line_bill_table_border_style           = $print_content["line_bill_table_border_style"];
        $line_bill_table_border_color           = $print_content["line_bill_table_border_color"];
        $line_bill_table_td_margin_left         = $print_content["line_bill_table_td_margin_left"];
        // display sections;
        $top_table_section                      = $print_content["top_table_section"]; 
        $content_table_section                  = $print_content["content_table_section"]; 
        $bottom_table_section                   = $print_content["bottom_table_section"]; 
        $footer_table                           = $print_content["footer_table"]; 
        $table_th_no                            = $print_content["table_th_no"]; 
        $table_th_code                          = $print_content["table_th_code"];
        $table_th_name                          = $print_content["table_th_name"];
        $table_th_img                           = $print_content["table_th_img"];
        $table_th_qty                           = $print_content["table_th_qty"]; 
        $table_th_price                         = $print_content["table_th_price"]; 
        $table_th_price_bdi                     = $print_content["table_th_price_bdi"]; 
        $table_th_discount                      = $print_content["table_th_discount"]; 
        $table_th_price_ade                     = $print_content["table_th_price_ade"]; 
        $table_th_price_adi                     = $print_content["table_th_price_adi"]; 
        $table_th_subtotal                      = $print_content["table_th_subtotal"]; 
        //  DD( $array["table_th_subtotal"] );
        $left_bottom_table                      = "";
        $right_top_table                        = "";
        $left_top_table                         = "";
        // $font_invoice_info   = 
        $left_invoice_info                      =  $print_content["left_invoice_info"];
        $color_invoice_info                     =  $print_content["color_invoice_info"];
        $right_invoice_info                     =  $print_content["right_invoice_info"];
        $padding_invoice_info                   =  $print_content["padding_invoice_info"];
        $background_color_invoice_info          =  $print_content["background_color_invoice_info"];
        // *********************************************** \\
        $class_width_left                           =  $print_content["class_width_left"];
        $class_width_right                          =  $print_content["class_width_right"];
        $bold_left_invoice_info                     =  $print_content["bold_left_invoice_info"];
        $bold_left_invoice_info_br_width            =  $print_content["bold_left_invoice_info_br_width"];
        $bold_left_invoice_info_br_style            =  $print_content["bold_left_invoice_info_br_style"];
        $bold_left_invoice_info_br_color            =  $print_content["bold_left_invoice_info_br_color"];
        $bold_left_invoice_info_text_align          =  $print_content["bold_left_invoice_info_text_align"];
                            /* */
        $class_width_left_right                     =  $print_content["class_width_left_right"];
        $class_width_right_right                    =  $print_content["class_width_right_right"] ;
        $bold_right_invoice_info                    =  $print_content["bold_right_invoice_info"];
        $bold_right_invoice_info_br_width           =  $print_content["bold_right_invoice_info_br_width"];
        $bold_right_invoice_info_br_style           =  $print_content["bold_right_invoice_info_br_style"];
        $bold_right_invoice_info_br_color           =  $print_content["bold_right_invoice_info_br_color"];
        $bold_right_invoice_info_text_align         =  $print_content["bold_right_invoice_info_text_align"];
        // *********************************************** \\
        $left_top_table                             =  $print_content["left_top_content"] ;
        $right_top_table                            =  $print_content["right_top_content"] ;
        $left_bottom_table                          =  $print_content["bottom_content"] ;
        $table_th_no_named                          =  $print_content["table_th_no_named"] ;
        $table_th_name_named                        =  $print_content["table_th_name_named"] ;
        $table_th_code_named                        =  $print_content["table_th_code_named"] ;
        $table_th_img_named                         =  $print_content["table_th_img_named"] ;
        $table_th_qty_named                         =  $print_content["table_th_qty_named"] ;
        $table_th_price_named                       =  $print_content["table_th_price_named"] ;
        $table_th_price_bdi_named                   =  $print_content["table_th_price_bdi_named"] ;
        $table_th_discount_named                    =  $print_content["table_th_discount_named"] ;
        $table_th_price_ade_named                   =  $print_content["table_th_price_ade_named"] ;
        $table_th_price_adi_named                   =  $print_content["table_th_price_adi_named"] ;
        $table_th_subtotal_named                    =  $print_content["table_th_subtotal_named"] ;
        $currency_in_row                            =  $print_content["currency_in_row"]; 
        $if_discount_zero                           =  $print_content["if_discount_zero"];
        $bill_invoice_info_down_vat                 =  $print_content["bill_invoice_info_down_vat"] ; 
        $bill_invoice_info_down_subtotal            =  $print_content["bill_invoice_info_down_subtotal"] ; 
        $bill_invoice_info_down_discount            =  $print_content["bill_invoice_info_down_discount"] ; 
        $bill_invoice_info_down_subtotal_after_dis  =  $print_content["bill_invoice_info_down_subtotal_after_dis"] ; 
        $bold_left_invoice_info_customer_number     =  $print_content["bold_left_invoice_info_customer_number"]  ; 
        $bold_left_invoice_info_customer_address    =  $print_content["bold_left_invoice_info_customer_address"]  ; 
        $bold_left_invoice_info_customer_mobile     =  $print_content["bold_left_invoice_info_customer_mobile"] ; 
        $bold_left_invoice_info_customer_tax        =  $print_content["bold_left_invoice_info_customer_tax"]  ; 
        $bold_left_invoice_info_number              =  $print_content["bold_left_invoice_info_number"]  ; 
        $bold_left_invoice_info_project             =  $print_content["bold_left_invoice_info_project"] ; 
        $bold_left_invoice_info_date                =  $print_content["bold_left_invoice_info_date"] ; 
    
        $margin_top_page                            =  $print_content["margin_top_page"] ;
        $margin_bottom_page                         =  $print_content["margin_bottom_page"] ;
        
        $body_content_top                           =  $print_content["body_content_top"] ;
        $body_content_margin_left                   =  $print_content["body_content_margin_left"] ;
        $body_content_margin_right                  =  $print_content["body_content_margin_right"] ;
        $body_content_margin_bottom                 =  $print_content["body_content_margin_bottom"] ;

        $body_content_top_repeat                    = floatVal($body_content_top) + 4.5 ;
        $body_content_top_repeat                    = $body_content_top_repeat."cm";
        
        $show_quotation_terms                       = $print_content["show_quotation_terms"];
        $show_customer_signature                    = $print_content["show_customer_signature"]; 
     
        

    }else{
    
            
            $check_type              = (isset($edit_type) || (isset($array["edit_type"]) && $array["edit_type"] != null ));
            
            if((isset($array["edit_type"]) && $array["edit_type"] != null )){
                $PrinterContentTemplate  = \App\Models\PrinterContentTemplate::where("printer_template_id",$array["edit_type"])->first();
                $PrinterTemplateContain  = \App\Models\PrinterTemplateContain::where("printer_templates_id",$array["edit_type"])->first();
            }
            
            // table 
            $content_width                     = (isset($array["content_width"]))?$array["content_width"]:(($check_type)?$PrinterContentTemplate->content_width:"100%");
            $content_table_border_radius       = (isset($array["content_table_border_radius"]))?$array["content_table_border_radius"]:(($check_type)?$PrinterContentTemplate->content_table_border_radius:"0px");
            $content_table_width               = (isset($array["content_table_width"]))?$array["content_table_width"]:(($check_type)?$PrinterContentTemplate->content_table_width:"100%");

            $content_table_th_font_size        = (isset($array["content_table_th_font_size"]))?$array["content_table_th_font_size"]:(($check_type)?$PrinterContentTemplate->content_table_th_font_size:"8px");
            $content_table_th_text_align       = (isset($array["content_table_th_text_align"]))?$array["content_table_th_text_align"]:(($check_type)?$PrinterContentTemplate->content_table_th_text_align:"left");
            $content_table_th_border_width     = (isset($array["content_table_th_border_width"]))?$array["content_table_th_border_width"]:(($check_type)?$PrinterContentTemplate->content_table_td_border_width:"1px");
            $content_table_th_border_style     = (isset($array["content_table_th_border_style"]))?$array["content_table_th_border_style"]:(($check_type)?$PrinterContentTemplate->content_table_th_border_style:"solid");
            $content_table_th_border_color     = (isset($array["content_table_th_border_color"]))?$array["content_table_th_border_color"]:(($check_type)?$PrinterContentTemplate->content_table_th_border_color:"black");
            $content_table_th_padding          = (isset($array["content_table_th_padding"]))?$array["content_table_th_padding"]:(($check_type)?$PrinterContentTemplate->content_table_th_padding:"0px");
            
            $content_table_td_font_size        = (isset($array["content_table_td_font_size"]))?$array["content_table_td_font_size"]:(($check_type)?$PrinterContentTemplate->content_table_td_font_size:"8px");
            $content_table_td_text_align       = (isset($array["content_table_td_text_align"]))?$array["content_table_td_text_align"]:(($check_type)?$PrinterContentTemplate->content_table_td_text_align:"left");
            $content_table_td_border_width     = (isset($array["content_table_td_border_width"]))?$array["content_table_td_border_width"]:(($check_type)?$PrinterContentTemplate->content_table_td_border_width:"1px");
            $content_table_td_border_style     = (isset($array["content_table_td_border_style"]))?$array["content_table_td_border_style"]:(($check_type)?$PrinterContentTemplate->content_table_td_border_style:"solid");
            $content_table_td_border_color     = (isset($array["content_table_td_border_color"]))?$array["content_table_td_border_color"]:(($check_type)?$PrinterContentTemplate->content_table_td_border_color:"black");
            $content_table_td_padding          = (isset($array["content_table_td_padding"]))?$array["content_table_td_padding"]:(($check_type)?$PrinterContentTemplate->content_table_td_padding:"0px");

            $content_table_width_no            = (isset($array["content_table_width_no"]))?$array["content_table_width_no"]:(($check_type)?$PrinterContentTemplate->content_table_width_no:"5%" );
            $content_table_td_no_font_size     = (isset($array["content_table_td_no_font_size"]))?$array["content_table_td_no_font_size"]:(($check_type)?$PrinterContentTemplate->content_table_td_no_font_size:"16px");
            $content_table_td_no_text_align    = (isset($array["content_table_td_no_text_align"]))?$array["content_table_td_no_text_align"]:(($check_type)?$PrinterContentTemplate->content_table_td_no_text_align:"left");
            $content_table_font_weight_no      = (isset($array["content_table_font_weight_no"]))?$array["content_table_font_weight_no"]:(($check_type)?$PrinterContentTemplate->content_table_font_weight_no:"500");
            
            $content_table_width_name          = (isset($array["content_table_width_name"]))?$array["content_table_width_name"]:(($check_type)?$PrinterContentTemplate->content_table_width_name:"5%");
            $content_table_font_size_name      = (isset($array["content_table_font_size_name"]))?$array["content_table_font_size_name"]:(($check_type)?$PrinterContentTemplate->content_table_font_size_name:"16px");
            $content_table_font_weight_name    = (isset($array["content_table_font_weight_name"]))?$array["content_table_font_weight_name"]:(($check_type)?$PrinterContentTemplate->content_table_font_weight_name:"500");
            $content_table_text_align_name     = (isset($array["content_table_text_align_name"]))?$array["content_table_text_align_name"]:(($check_type)?$PrinterContentTemplate->content_table_text_align_name:"left");
            
            $content_table_width_code          = (isset($array["content_table_width_code"]))?$array["content_table_width_code"]:(($check_type)?$PrinterContentTemplate->content_table_width_code:"5%");
            $content_table_td_code_font_size   = (isset($array["content_table_td_code_font_size"]))?$array["content_table_td_code_font_size"]:(($check_type)?$PrinterContentTemplate->content_table_td_code_font_size:"16px");
            $content_table_td_code_text_align  = (isset($array["content_table_td_code_text_align"]))?$array["content_table_td_code_text_align"]:(($check_type)?$PrinterContentTemplate->content_table_td_code_text_align:"left");
            $content_table_font_weight_code    = (isset($array["content_table_font_weight_code"]))?$array["content_table_font_weight_code"]:(($check_type)?$PrinterContentTemplate->content_table_font_weight_code:"500");
           
            $content_table_width_img           = (isset($array["content_table_width_img"]))?$array["content_table_width_img"]:(($check_type)?$PrinterContentTemplate->content_table_width_img:"5%");
            $content_table_td_img_font_size    = (isset($array["content_table_td_img_font_size"]))?$array["content_table_td_img_font_size"]:(($check_type)?$PrinterContentTemplate->content_table_td_img_font_size:"16px");
            $content_table_td_img_text_align   = (isset($array["content_table_td_img_text_align"]))?$array["content_table_td_img_text_align"]:(($check_type)?$PrinterContentTemplate->content_table_td_img_text_align:"left");
            $content_table_font_weight_img     = (isset($array["content_table_font_weight_img"]))?$array["content_table_font_weight_img"]:(($check_type)?$PrinterContentTemplate->content_table_font_weight_img:"500");
            
            $content_table_width_qty           = (isset($array["content_table_width_qty"]))?$array["content_table_width_qty"]:(($check_type)?$PrinterContentTemplate->content_table_width_qty:"5%");
            $content_table_td_qty_font_size    = (isset($array["content_table_td_qty_font_size"]))?$array["content_table_td_qty_font_size"]:(($check_type)?$PrinterContentTemplate->content_table_td_qty_font_size:"16px");
            $content_table_td_qty_text_align   = (isset($array["content_table_td_qty_text_align"]))?$array["content_table_td_qty_text_align"]:(($check_type)?$PrinterContentTemplate->content_table_td_qty_text_align:"left");
            $content_table_font_weight_qty     = (isset($array["content_table_font_weight_qty"]))?$array["content_table_font_weight_qty"]:(($check_type)?$PrinterContentTemplate->content_table_font_weight_qty:"500");
            
            $content_table_width_price         = (isset($array["content_table_width_price"]))?$array["content_table_width_price"]:(($check_type)?$PrinterContentTemplate->content_table_width_price:"5%");
            $content_table_td_price_font_size  = (isset($array["content_table_td_price_font_size"]))?$array["content_table_td_price_font_size"]:(($check_type)?$PrinterContentTemplate->content_table_td_price_font_size:"16px");
            $content_table_td_price_text_align = (isset($array["content_table_td_price_text_align"]))?$array["content_table_td_price_text_align"]:(($check_type)?$PrinterContentTemplate->content_table_td_price_text_align:"left");
            $content_table_font_weight_price   = (isset($array["content_table_font_weight_price"]))?$array["content_table_font_weight_price"]:(($check_type)?$PrinterContentTemplate->content_table_font_weight_price:"500");


            $content_table_width_price_bdi          = (isset($array["content_table_width_price_bdi"]))?$array["content_table_width_price_bdi"]:(($check_type)?$PrinterContentTemplate->content_table_width_price_bdi:"5%");
            $content_table_td_price_bdi_font_size   = (isset($array["content_table_td_price_bdi_font_size"]))?$array["content_table_td_price_bdi_font_size"]:(($check_type)?$PrinterContentTemplate->content_table_td_price_bdi_font_size:"16px");
            $content_table_td_price_bdi_text_align  = (isset($array["content_table_td_price_bdi_text_align"]))?$array["content_table_td_price_bdi_text_align"]:(($check_type)?$PrinterContentTemplate->content_table_td_price_bdi_text_align:"left");
            $content_table_font_weight_price_bdi    = (isset($array["content_table_font_weight_price_bdi"]))?$array["content_table_font_weight_price_bdi"]:(($check_type)?$PrinterContentTemplate->content_table_font_weight_price_bdi:"500");

            $content_table_width_discount           = (isset($array["content_table_width_discount"]))?$array["content_table_width_discount"]:(($check_type)?$PrinterContentTemplate->content_table_width_discount:"5%");
            $content_table_td_discount_font_size    = (isset($array["content_table_td_discount_font_size"]))?$array["content_table_td_discount_font_size"]:(($check_type)?$PrinterContentTemplate->content_table_td_discount_font_size:"16px");
            $content_table_td_discount_text_align   = (isset($array["content_table_td_discount_text_align"]))?$array["content_table_td_discount_text_align"]:(($check_type)?$PrinterContentTemplate->content_table_td_discount_text_align:"left");
            $content_table_font_weight_discount     = (isset($array["content_table_font_weight_discount"]))?$array["content_table_font_weight_discount"]:(($check_type)?$PrinterContentTemplate->content_table_font_weight_discount:"500");
        
            $content_table_width_price_adi          = (isset($array["content_table_width_price_adi"]))?$array["content_table_width_price_adi"]:(($check_type)?$PrinterContentTemplate->content_table_width_price_adi:"5%");
            $content_table_td_price_adi_font_size   = (isset($array["content_table_td_price_adi_font_size"]))?$array["content_table_td_price_adi_font_size"]:(($check_type)?$PrinterContentTemplate->content_table_td_price_adi_font_size:"16px");
            $content_table_td_price_adi_text_align  = (isset($array["content_table_td_price_adi_text_align"]))?$array["content_table_td_price_adi_text_align"]:(($check_type)?$PrinterContentTemplate->content_table_td_price_adi_text_align:"left");
            $content_table_font_weight_price_adi    = (isset($array["content_table_font_weight_price_adi"]))?$array["content_table_font_weight_price_adi"]:(($check_type)?$PrinterContentTemplate->content_table_font_weight_price_adi:"500");
            
            $content_table_width_price_ade          = (isset($array["content_table_width_price_ade"]))?$array["content_table_width_price_ade"]:(($check_type)?$PrinterContentTemplate->content_table_width_price_ade:"5%");
            $content_table_td_price_ade_font_size   = (isset($array["content_table_td_price_ade_font_size"]))?$array["content_table_td_price_ade_font_size"]:(($check_type)?$PrinterContentTemplate->content_table_td_price_ade_font_size:"16px");
            $content_table_td_price_ade_text_align  = (isset($array["content_table_td_price_ade_text_align"]))?$array["content_table_td_price_ade_text_align"]:(($check_type)?$PrinterContentTemplate->content_table_td_price_ade_text_align:"left");
            $content_table_font_weight_price_ade    = (isset($array["content_table_font_weight_price_ade"]))?$array["content_table_font_weight_price_ade"]:(($check_type)?$PrinterContentTemplate->content_table_font_weight_price_ade:"500");
            
            $content_table_width_subtotal           = (isset($array["content_table_width_subtotal"]))?$array["content_table_width_subtotal"]:(($check_type)?$PrinterContentTemplate->content_table_width_subtotal:"5%");
            $content_table_td_subtotal_font_size    = (isset($array["content_table_td_subtotal_font_size"]))?$array["content_table_td_subtotal_font_size"]:(($check_type)?$PrinterContentTemplate->content_table_td_subtotal_font_size:"16px");
            $content_table_td_subtotal_text_align   = (isset($array["content_table_td_subtotal_text_align"]))?$array["content_table_td_subtotal_text_align"]:(($check_type)?$PrinterContentTemplate->content_table_td_subtotal_text_align:"left");
            $content_table_font_weight_subtotal     = (isset($array["content_table_font_weight_subtotal"]))?$array["content_table_font_weight_subtotal"]:(($check_type)?$PrinterContentTemplate->content_table_font_weight_subtotal:"500");
            
            $collapse                          = "collapse" ; 


            // top table
            $top_table_width                   = (isset($array["top_table_width"]))?$array["top_table_width"]:(($check_type)?$PrinterContentTemplate->top_table_width:"100%");
            $top_table_margin_bottom           = (isset($array["top_table_margin_bottom"]))?$array["top_table_margin_bottom"]:(($check_type)?$PrinterContentTemplate->top_table_margin_bottom:"0px");
            $top_table_border_width            = (isset($array["top_table_border_width"]))?$array["top_table_border_width"]:(($check_type)?$PrinterContentTemplate->top_table_border_width:"2px");
            $top_table_border_style            = (isset($array["top_table_border_style"]))?$array["top_table_border_style"]:(($check_type)?$PrinterContentTemplate->top_table_border_style:"solid");
            $top_table_border_color            = (isset($array["top_table_border_color"]))?$array["top_table_border_color"]:(($check_type)?$PrinterContentTemplate->top_table_border_color:"black");
            $top_table_td_border_width         = (isset($array["top_table_td_border_width"]))?$array["top_table_td_border_width"]:(($check_type)?$PrinterContentTemplate->top_table_td_border_width:"0px");
            $top_table_td_border_style         = (isset($array["top_table_td_border_style"]))?$array["top_table_td_border_style"]:(($check_type)?$PrinterContentTemplate->top_table_td_border_style:"solid");
            $top_table_td_border_color         = (isset($array["top_table_td_border_color"]))?$array["top_table_td_border_color"]:(($check_type)?$PrinterContentTemplate->top_table_td_border_color:"transparent");
            // left top
            $left_top_table_width              = (isset($array["left_top_table_width"]))?$array["left_top_table_width"]:(($check_type)?$PrinterContentTemplate->left_top_table_width:"50%" );
            $left_top_table_text_align         = (isset($array["left_top_table_text_align"]))?$array["left_top_table_text_align"]:(($check_type)?$PrinterContentTemplate->left_top_table_text_align:"left");
            $left_top_table_font_size          = (isset($array["left_top_table_font_size"]))?$array["left_top_table_font_size"]:(($check_type)?$PrinterContentTemplate->left_top_table_font_size:"14px");
            
            // right top
            $right_top_table_width             = (isset($array["right_top_table_width"]))?$array["right_top_table_width"]:(($check_type)?$PrinterContentTemplate->right_top_table_width:"50%");
            $right_top_table_text_align        = (isset($array["right_top_table_text_align"]))?$array["right_top_table_text_align"]:(($check_type)?$PrinterContentTemplate->right_top_table_text_align:"right");
            $right_top_table_font_size         = (isset($array["right_top_table_font_size"]))?$array["right_top_table_font_size"]:(($check_type)?$PrinterContentTemplate->right_top_table_font_size:"14px");
            
            // bottom table
            $bottom_table_width                = (isset($array["bottom_table_width"]))?$array["bottom_table_width"]:"100%" ;
            $bottom_table_margin_bottom        = (isset($array["bottom_table_margin_bottom"]))?$array["bottom_table_margin_bottom"]:"10px";
            $bottom_table_margin_top           = (isset($array["bottom_table_margin_top"]))?$array["bottom_table_margin_top"]:"1px";
            $bottom_table_border_width         = (isset($array["bottom_table_border_width"]))?$array["bottom_table_border_width"]:"3px";
            $bottom_table_border_style         = (isset($array["bottom_table_border_style"]))?$array["bottom_table_border_style"]:"solid";
            $bottom_table_border_color         = (isset($array["bottom_table_border_color"]))?$array["bottom_table_border_color"]:"black";
            $bottom_table_td_border_width      = (isset($array["bottom_table_td_border_width"]))?$array["bottom_table_td_border_width"]:"1px";
            $bottom_table_td_border_style      = (isset($array["bottom_table_td_border_style"]))?$array["bottom_table_td_border_style"]:"solid";
            $bottom_table_td_border_color      = (isset($array["bottom_table_td_border_color"]))?$array["bottom_table_td_border_color"]:"black";
            
            // left bottom  
            $left_bottom_table_width           = (isset($array["left_bottom_table_width"]))?$array["left_bottom_table_width"]:(($check_type)?$PrinterContentTemplate->left_bottom_table_width:"50%");
            $left_bottom_table_text_align      = (isset($array["left_bottom_table_text_align"]))?$array["left_bottom_table_text_align"]:(($check_type)?$PrinterContentTemplate->left_bottom_table_text_align:"left");
            $left_bottom_table_font_size       = (isset($array["left_bottom_table_font_size"]))?$array["left_bottom_table_font_size"]:(($check_type)?$PrinterContentTemplate->left_bottom_table_font_size:"20px");
            $left_bottom_table_td_bor_width    = (isset($array["left_bottom_table_td_bor_width"]))?$array["left_bottom_table_td_bor_width"]:(($check_type)?$PrinterContentTemplate->left_bottom_table_td_bor_width:"1px");
            $left_bottom_table_td_bor_style    = (isset($array["left_bottom_table_td_bor_style"]))?$array["left_bottom_table_td_bor_style"]:(($check_type)?$PrinterContentTemplate->left_bottom_table_td_bor_style:"solid");
            $left_bottom_table_td_bor_color    = (isset($array["left_bottom_table_td_bor_color"]))?$array["left_bottom_table_td_bor_color"]:(($check_type)?$PrinterContentTemplate->left_bottom_table_td_bor_color:"black");
            
            // right bottom  
            $right_bottom_table_width          = (isset($array["right_bottom_table_width"]))?$array["right_bottom_table_width"]:(($check_type)?$PrinterContentTemplate->right_bottom_table_width:"50%");
            $right_bottom_table_text_align     = (isset($array["right_bottom_table_text_align"]))?$array["right_bottom_table_text_align"]:(($check_type)?$PrinterContentTemplate->right_bottom_table_text_align:"right");
            $right_bottom_table_font_size      = (isset($array["right_bottom_table_font_size"]))?$array["right_bottom_table_font_size"]:(($check_type)?$PrinterContentTemplate->right_bottom_table_font_size:"20px");
            $right_bottom_table_td_bor_width   = (isset($array["right_bottom_table_td_bor_width"]))?$array["right_bottom_table_td_bor_width"]:(($check_type)?$PrinterContentTemplate->right_bottom_table_td_bor_width:"1px");
            $right_bottom_table_td_bor_style   = (isset($array["right_bottom_table_td_bor_style"]))?$array["right_bottom_table_td_bor_style"]:(($check_type)?$PrinterContentTemplate->right_bottom_table_td_bor_style:"solid");
            $right_bottom_table_td_bor_color   = (isset($array["right_bottom_table_td_bor_color"]))?$array["right_bottom_table_td_bor_color"]:(($check_type)?$PrinterContentTemplate->right_bottom_table_td_bor_color:"black");
            
            // .......................................................
            $bill_table_info_width             = (isset($array["bill_table_info_width"]))?$array["bill_table_info_width"]:(($check_type)?$PrinterContentTemplate->bill_table_info_width:"50%");
            $bill_table_info_border_width      = (isset($array["bill_table_info_border_width"]))?$array["bill_table_info_border_width"]:(($check_type)?$PrinterContentTemplate->bill_table_border_width:"1px");
            $bill_table_info_border_style      = (isset($array["bill_table_info_border_style"]))?$array["bill_table_info_border_style"]:(($check_type)?$PrinterContentTemplate->bill_table_border_style:"solid");
            $bill_table_info_border_color      = (isset($array["bill_table_info_border_color"]))?$array["bill_table_info_border_color"]:(($check_type)?$PrinterContentTemplate->bill_table_border_color:"black");
            
            $bill_table_margin_bottom          = (isset($array["bill_table_margin_bottom"]))?$array["bill_table_margin_bottom"]:(($check_type)?$PrinterContentTemplate->bill_table_margin_bottom:"10px");
            $bill_table_margin_top             = (isset($array["bill_table_margin_top"]))?$array["bill_table_margin_top"]:(($check_type)?$PrinterContentTemplate->bill_table_margin_top:"10px");
            $bill_table_border_width           = (isset($array["bill_table_border_width"]))?$array["bill_table_border_width"]:(($check_type)?$PrinterContentTemplate->bill_table_border_width:"1px");
            $bill_table_border_style           = (isset($array["bill_table_border_style"]))?$array["bill_table_border_style"]:(($check_type)?$PrinterContentTemplate->bill_table_border_style:"solid");
            $bill_table_border_color           = (isset($array["bill_table_border_color"]))?$array["bill_table_border_color"]:(($check_type)?$PrinterContentTemplate->bill_table_border_color:"black");
        
            $bill_table_left_td_width          = (isset($array["bill_table_left_td_width"]))?$array["bill_table_left_td_width"]:(($check_type)?$PrinterContentTemplate->bill_table_left_td_width:"60%");
            $bill_table_left_td_font_size      = (isset($array["bill_table_left_td_font_size"]))?$array["bill_table_left_td_font_size"]:(($check_type)?$PrinterContentTemplate->bill_table_left_td_font_size:"16px");
            $bill_table_left_td_weight         = (isset($array["bill_table_left_td_weight"]))?$array["bill_table_left_td_weight"]:(($check_type)?$PrinterContentTemplate->bill_table_left_td_weight:"300");
            $bill_table_left_td_text_align     = (isset($array["bill_table_left_td_text_align"]))?$array["bill_table_left_td_text_align"]:(($check_type)?$PrinterContentTemplate->bill_table_left_td_text_align:"left");
            $bill_table_left_td_border_width   = (isset($array["bill_table_left_td_border_width"]))?$array["bill_table_left_td_border_width"]:(($check_type)?$PrinterContentTemplate->bill_table_left_td_border_width:"1px");
            $bill_table_left_td_border_style   = (isset($array["bill_table_left_td_border_style"]))?$array["bill_table_left_td_border_style"]:(($check_type)?$PrinterContentTemplate->bill_table_left_td_border_style:"solid");
            $bill_table_left_td_border_color   = (isset($array["bill_table_left_td_border_color"]))?$array["bill_table_left_td_border_color"]:(($check_type)?$PrinterContentTemplate->bill_table_left_td_border_color:"black");
            $bill_table_left_td_padding_left   = (isset($array["bill_table_left_td_padding_left"]))?$array["bill_table_left_td_padding_left"]:(($check_type)?$PrinterContentTemplate->bill_table_left_td_padding_left:"0px");

            $bill_table_right_td_width         = (isset($array["bill_table_right_td_width"]))?$array["bill_table_right_td_width"]:(($check_type)?$PrinterContentTemplate->bill_table_right_td_width:"40%");
            $bill_table_right_td_font_size     = (isset($array["bill_table_right_td_font_size"]))?$array["bill_table_right_td_font_size"]:(($check_type)?$PrinterContentTemplate->bill_table_right_td_font_size:"16px");
            $bill_table_right_td_weight        = (isset($array["bill_table_right_td_weight"]))?$array["bill_table_right_td_weight"]:(($check_type)?$PrinterContentTemplate->bill_table_right_td_weight:"300");
            $bill_table_right_td_text_align    = (isset($array["bill_table_right_td_text_align"]))?$array["bill_table_right_td_text_align"]:(($check_type)?$PrinterContentTemplate->bill_table_right_td_text_align:"right");
            $bill_table_right_td_border_width  = (isset($array["bill_table_right_td_border_width"]))?$array["bill_table_right_td_border_width"]:(($check_type)?$PrinterContentTemplate->bill_table_right_td_border_width:"1px");
            $bill_table_right_td_border_style  = (isset($array["bill_table_right_td_border_style"]))?$array["bill_table_right_td_border_style"]:(($check_type)?$PrinterContentTemplate->bill_table_right_td_border_style:"solid");
            $bill_table_right_td_border_color  = (isset($array["bill_table_right_td_border_color"]))?$array["bill_table_right_td_border_color"]:(($check_type)?$PrinterContentTemplate->bill_table_right_td_border_color:"black");
            $bill_table_right_td_padding_left  = (isset($array["bill_table_right_td_padding_left"]))?$array["bill_table_right_td_padding_left"]:(($check_type)?$PrinterContentTemplate->bill_table_right_td_padding_left:"0px");
            
            $line_bill_table_width             = (isset($array["line_bill_table_width"]))?$array["line_bill_table_width"]:(($check_type)?$PrinterContentTemplate->line_bill_table_width:"100%");
            $line_bill_table_height            = (isset($array["line_bill_table_height"]))?$array["line_bill_table_height"]:(($check_type)?$PrinterContentTemplate->line_bill_table_height:"2px");
            $line_bill_table_color             = (isset($array["line_bill_table_color"]))?$array["line_bill_table_color"]:(($check_type)?$PrinterContentTemplate->line_bill_table_color:"black");
            $line_bill_table_border_width      = (isset($array["line_bill_table_border_width"]))?$array["line_bill_table_border_width"]:(($check_type)?$PrinterContentTemplate->line_bill_table_border_width:"1px");
            $line_bill_table_border_style      = (isset($array["line_bill_table_border_style"]))?$array["line_bill_table_border_style"]:(($check_type)?$PrinterContentTemplate->line_bill_table_border_style:"solid");
            $line_bill_table_border_color      = (isset($array["line_bill_table_border_color"]))?$array["line_bill_table_border_color"]:(($check_type)?$PrinterContentTemplate->line_bill_table_border_color:"black");

            $line_bill_table_td_margin_left    = (isset($array["line_bill_table_td_margin_left"]))?$array["line_bill_table_td_margin_left"]:(($check_type)?$PrinterContentTemplate->line_bill_table_td_margin_left:"10px" );
        
            // display sections;
            $top_table_section                 = (isset($array["top_table_section"]))?(($array["top_table_section"] === "true")?true:false):(($check_type)?(($PrinterContentTemplate->top_table_section === 1)?true:false):true); 
            $content_table_section             = (isset($array["content_table_section"]))?(($array["content_table_section"] === "true")?true:false):(($check_type)?(($PrinterContentTemplate->content_table_section === 1)?true:false):true); 
            $bottom_table_section              = (isset($array["bottom_table_section"]))?(($array["bottom_table_section"] === "true")?true:false):(($check_type)?(($PrinterContentTemplate->bottom_table_section === 1)?true:false):true); 
            $footer_table                      = (isset($array["footer_table"]))?(($array["footer_table"] === "true")?true:false):(($check_type)?(($PrinterContentTemplate->footer_table === "true")?true:false):true); 
            $table_th_no                       = (isset($array["table_th_no"]))?(($array["table_th_no"] === "true")?true:false):(($check_type)?(($PrinterContentTemplate->table_th_no === "true")?true:false):true); 
            $table_th_code                     = (isset($array["table_th_code"]))?(($array["table_th_code"] === "true")?true:false):(($check_type)?(($PrinterContentTemplate->table_th_code === "true")?true:false):true);
            $table_th_name                     = (isset($array["table_th_name"]))?(($array["table_th_name"] === "true")?true:false):(($check_type)?(($PrinterContentTemplate->table_th_name === "true")?true:false):true);
            $table_th_img                      = (isset($array["table_th_img"]))?(($array["table_th_img"] === "true")?true:false):(($check_type)?(($PrinterContentTemplate->table_th_img === "true")?true:false):true);
            $table_th_qty                      = (isset($array["table_th_qty"]))?(($array["table_th_qty"] === "true")?true:false):(($check_type)?(($PrinterContentTemplate->table_th_qty === "true")?true:false):true); 
            $table_th_price                    = (isset($array["table_th_price"]))?(($array["table_th_price"] === "true")?true:false):(($check_type)?(($PrinterContentTemplate->table_th_price === "true")?true:false):true); 
            
            $show_quotation_terms              = (isset($array["show_quotation_terms"]))?(($array["show_quotation_terms"] === "true" || $array["show_quotation_terms"] === "on")?true:false):(($check_type)?(($PrinterContentTemplate->show_quotation_terms === 1)?true:false):true); 
            $show_customer_signature           = (isset($array["show_customer_signature"]))?(($array["show_customer_signature"] === "true" || $array["show_customer_signature"] === "on")?true:false):(($check_type)?(($PrinterContentTemplate->show_customer_signature === 1)?true:false):true); 
        
            $table_th_price_bdi                = (isset($array["table_th_price_bdi"]))?(($array["table_th_price_bdi"] === "true")?true:false):(($check_type)?(($PrinterContentTemplate->table_th_price_bdi === "true")?true:false):true); 
            $table_th_discount                 = (isset($array["table_th_discount"]))?(($array["table_th_discount"] === "true")?true:false):(($check_type)?(($PrinterContentTemplate->table_th_discount === "true")?true:false):true); 
            $table_th_price_ade                = (isset($array["table_th_price_ade"]))?(($array["table_th_price_ade"] === "true")?true:false):(($check_type)?(($PrinterContentTemplate->table_th_price_ade === "true")?true:false):true); 
            $table_th_price_adi                = (isset($array["table_th_price_adi"]))?(($array["table_th_price_adi"] === "true")?true:false):(($check_type)?(($PrinterContentTemplate->table_th_price_adi === "true")?true:false):true); 
            $table_th_subtotal                 = (isset($array["table_th_subtotal"]))?(($array["table_th_subtotal"] === "true")?true:false):(($check_type)?(($PrinterContentTemplate->table_th_subtotal === "true")?true:false):true); 
            //  DD( $array["table_th_subtotal"] );
            
            $left_bottom_table                 = "";
            $right_top_table                   = "";
            $left_top_table                    = "";
        
            // $font_invoice_info   = 
            $left_invoice_info                          = (isset($array["left_invoice_info"]))?$array["left_invoice_info"]:(($check_type)?$PrinterContentTemplate->left_invoice_info:"left");
            $color_invoice_info                         = (isset($array["color_invoice_info"]))?$array["color_invoice_info"]:(($check_type)?$PrinterContentTemplate->color_invoice_info:"black");
            $right_invoice_info                         = (isset($array["right_invoice_info"]))?$array["right_invoice_info"]:(($check_type)?$PrinterContentTemplate->right_invoice_info:"left");
            $padding_invoice_info                       = (isset($array["padding_invoice_info"]))?$array["padding_invoice_info"]:(($check_type)?$PrinterContentTemplate->padding_invoice_info:"10px");
            $background_color_invoice_info              = (isset($array["background_color_invoice_info"]))?$array["background_color_invoice_info"]:(($check_type)?$PrinterContentTemplate->background_color_invoice_info:"transparent");
            // *********************************************** \\ 
            $left_size                                  =  (isset($array["class_width_left"]))?$array["class_width_left"]:(($check_type)?$PrinterContentTemplate->class_width_left:"4");
            $right_size                                 =  (isset($array["class_width_right"]))?$array["class_width_right"]:(($check_type)?$PrinterContentTemplate->class_width_right:"8");
            $class_width_left                           = "col-md-".$left_size;
            $class_width_right                          = "col-md-".$right_size;
            
            $bold_left_invoice_info                     = (isset($array["bold_left_invoice_info"]))?$array["bold_left_invoice_info"]:(($check_type)?$PrinterContentTemplate->bold_left_invoice_info:"500");
            $bold_left_invoice_info_br_width            = (isset($array["bold_left_invoice_info_br_width"]))?$array["bold_left_invoice_info_br_width"]:(($check_type)?$PrinterContentTemplate->bold_left_invoice_info_br_width:"0px");
            $bold_left_invoice_info_br_style            = (isset($array["bold_left_invoice_info_br_style"]))?$array["bold_left_invoice_info_br_style"]:(($check_type)?$PrinterContentTemplate->bold_left_invoice_info_br_style:"solid");
            $bold_left_invoice_info_br_color            = (isset($array["bold_left_invoice_info_br_color"]))?$array["bold_left_invoice_info_br_color"]:(($check_type)?$PrinterContentTemplate->bold_left_invoice_info_br_color:"black");
            $bold_left_invoice_info_text_align          = (isset($array["bold_left_invoice_info_text_align"]))?$array["bold_left_invoice_info_text_align"]:(($check_type)?$PrinterContentTemplate->bold_left_invoice_info_text_align:"left");
                                /* */
            $left_left_size                             = (isset($array["class_width_left_right"]))?$array["class_width_left_right"]:(($check_type)?$PrinterContentTemplate->class_width_left_right:"4");
            $left_right_size                            = (isset($array["class_width_right_right"]))?$array["class_width_right_right"]:(($check_type)?$PrinterContentTemplate->class_width_right_right:"8");
            $class_width_left_right                     = "col-md-". $left_left_size ;
            $class_width_right_right                    = "col-md-". $left_right_size ;
            $bold_right_invoice_info                    = (isset($array["bold_right_invoice_info"]))?$array["bold_right_invoice_info"]:(($check_type)?$PrinterContentTemplate->bold_right_invoice_info:"500");
            $bold_right_invoice_info_br_width           = (isset($array["bold_right_invoice_info_br_width"]))?$array["bold_right_invoice_info_br_width"]:(($check_type)?$PrinterContentTemplate->bold_right_invoice_info_br_width:"0px");
            $bold_right_invoice_info_br_style           = (isset($array["bold_right_invoice_info_br_style"]))?$array["bold_right_invoice_info_br_style"]:(($check_type)?$PrinterContentTemplate->bold_right_invoice_info_br_style:"solid");
            $bold_right_invoice_info_br_color           = (isset($array["bold_right_invoice_info_br_color"]))?$array["bold_right_invoice_info_br_color"]:(($check_type)?$PrinterContentTemplate->bold_right_invoice_info_br_color:"black");
            $bold_right_invoice_info_text_align         = (isset($array["bold_right_invoice_info_text_align"]))?$array["bold_right_invoice_info_text_align"]:(($check_type)?$PrinterContentTemplate->bold_right_invoice_info_text_align:"left");
            // *********************************************** \\
        
            $margin_top_page                            = (isset($array["margin_top_page"]))?$array["margin_top_page"]:(($check_type)?$PrinterTemplateContain->margin_top_page:"2cm") ;
            $margin_bottom_page                         = (isset($array["margin_bottom_page"]))?$array["margin_bottom_page"]:(($check_type)?$PrinterTemplateContain->margin_bottom_page:"2cm") ;

            $body_content_top                           = (isset($array["body_content_top"]))?$array["body_content_top"]:(($check_type)?$PrinterTemplateContain->body_content_top:"2.5cm") ;
            $body_content_margin_left                   = (isset($array["body_content_margin_left"]))?$array["body_content_margin_left"]:(($check_type)?$PrinterTemplateContain->body_content_margin_left:"0px") ;
            $body_content_margin_right                  = (isset($array["body_content_margin_right"]))?$array["body_content_margin_right"]:(($check_type)?$PrinterTemplateContain->body_content_margin_right:"0px") ;
            $body_content_margin_bottom                 = (isset($array["body_content_margin_bottom"]))?$array["body_content_margin_bottom"]:(($check_type)?$PrinterTemplateContain->body_content_margin_bottom:"2.5cm") ;
            $body_content_top_repeat                    = floatVal($body_content_top) + 4.5 ;
            $body_content_top_repeat                    = $body_content_top_repeat."cm";
            $page_number_view                           = (isset($array["page_number_view"]))?(($array["page_number_view"] === "true")?true:false):(($check_type)?(($PrinterFooterTemplate->page_number_view === 1)?true:false):true); 
            
            $invoice_no                                 = (isset($array["invoice_no"]))?$array["invoice_no"]:(($check_type)?$PrinterContentTemplate->invoice_no:"Invoice No :") ;
            $project_no                                 = (isset($array["project_no"]))?$array["project_no"]:(($check_type)?$PrinterContentTemplate->project_no:"Project No :") ;
            $customer_no                                = (isset($array["customer_no"]))?$array["customer_no"]:(($check_type)?$PrinterContentTemplate->customer_name:"Customer Name :") ;
            
            $date_name                                  = (isset($array["date_name"]))?$array["date_name"]:(($check_type)?$PrinterContentTemplate->date_name:"Date :") ;
            $address_name                               = (isset($array["address_name"]))?$array["address_name"]:(($check_type)?$PrinterContentTemplate->address_name:"Address Name :") ;
            $mobile_name                                = (isset($array["mobile_name"]))?$array["mobile_name"]:(($check_type)?$PrinterContentTemplate->mobile_name:"Mobile Name :") ;
            $tax_name                                   = (isset($array["tax_name"]))?$array["tax_name"]:(($check_type)?$PrinterContentTemplate->tax_name:"Tax :") ;
            
            $choose_product_description                 = (isset($array["choose_product_description"]))?(($array["choose_product_description"] === "true")?true:false):(($check_type)?(($PrinterContentTemplate->choose_product_description === "true")?true:false):true); 
            
            if(isset($edit_type)){
                
                $left_top_table                             = $PrinterTemplateContain->left_top_content ;
                $right_top_table                            = $PrinterTemplateContain->right_top_content ;
                $left_bottom_table                          = $PrinterTemplateContain->bottom_content ;
                $table_th_no_named                          = $PrinterContentTemplate->table_th_no_named ;
                $table_th_name_named                        = $PrinterContentTemplate->table_th_name_named ;
                $table_th_code_named                        = $PrinterContentTemplate->table_th_code_named ;
                $table_th_img_named                         = $PrinterContentTemplate->table_th_img_named ;
                $table_th_qty_named                         = $PrinterContentTemplate->table_th_qty_named ;
                $table_th_price_named                       = $PrinterContentTemplate->table_th_price_named ;
                $table_th_price_bdi_named                   = $PrinterContentTemplate->table_th_price_bdi_named ;
                $table_th_discount_named                    = $PrinterContentTemplate->table_th_discount_named ;
                $table_th_price_ade_named                   = $PrinterContentTemplate->table_th_price_ade_named ;
                $table_th_price_adi_named                   = $PrinterContentTemplate->table_th_price_adi_named ;
                        
                $table_th_subtotal_named                    = $PrinterContentTemplate->table_th_subtotal_named ;
                $currency_in_row                            = $PrinterContentTemplate->currency_in_row; 
                $repeat_content_top                         = $PrinterContentTemplate->repeat_content_top; 
                $if_discount_zero                           = $PrinterContentTemplate->if_discount_zero;

                $bill_invoice_info_down_vat                 =  $PrinterContentTemplate->bill_invoice_info_down_vat ; 
                $bill_invoice_info_down_subtotal            =  $PrinterContentTemplate->bill_invoice_info_down_subtotal ; 
                $bill_invoice_info_down_discount            =  $PrinterContentTemplate->bill_invoice_info_down_discount ; 
                $bill_invoice_info_down_subtotal_after_dis  =  $PrinterContentTemplate->bill_invoice_info_down_subtotal_after_dis ; 

                $bold_left_invoice_info_customer_number     =  $PrinterContentTemplate->bold_left_invoice_info_customer_number  ; 
                $bold_left_invoice_info_customer_address    =  $PrinterContentTemplate->bold_left_invoice_info_customer_address  ; 
                $bold_left_invoice_info_customer_mobile     =  $PrinterContentTemplate->bold_left_invoice_info_customer_mobile ; 
                $bold_left_invoice_info_customer_tax        =  $PrinterContentTemplate->bold_left_invoice_info_customer_tax  ; 
                $bold_left_invoice_info_number              =  $PrinterContentTemplate->bold_left_invoice_info_number  ; 
                $bold_left_invoice_info_project             =  $PrinterContentTemplate->bold_left_invoice_info_project ; 
                $bold_left_invoice_info_date                =  $PrinterContentTemplate->bold_left_invoice_info_date ; 
            
            
            }else{
                // footer ** left
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
                // footer ** center middle
                if(isset($array["body_bottom_send_type"])){
                    if( $array["body_bottom_send_type"]  == "value" ){
                        $left_bottom_table                 = (isset($array["body_bottom"]))?$array["body_bottom"]:"";
                    }else{
                        $body_bottom_layout        = \App\InvoiceLayout::find($array["body_bottom"]);
                        $left_bottom_table         =  (isset($body_bottom_layout))?$body_bottom_layout->invoice_text:"";
                    }
                }else{  
                    $left_bottom_table              = (isset($array["body_bottom"]))?$array["body_bottom"]:"";
                }
                

                $table_th_no_named                          = (isset($array["table_th_no_named"]))?$array["table_th_no_named"]:"No";
                $table_th_code_named                        = (isset($array["table_th_code_named"]))?$array["table_th_code_named"]:"Code";
                $table_th_name_named                        = (isset($array["table_th_name_named"]))?$array["table_th_name_named"]:"Name";
                $table_th_img_named                         = (isset($array["table_th_img_named"]))?$array["table_th_img_named"]:"Image";
                $table_th_qty_named                         = (isset($array["table_th_qty_named"]))?$array["table_th_qty_named"]:"Qty";
                $table_th_price_named                       = (isset($array["table_th_price_named"]))?$array["table_th_price_named"]:"Price Before Dis Exc";
                $table_th_price_bdi_named                   = (isset($array["table_th_price_bdi_named"]))?$array["table_th_price_bdi_named"]:"Price Before Dis Inc";
                $table_th_discount_named                    = (isset($array["table_th_discount_named"]))?$array["table_th_discount_named"]:"Discount";
                $table_th_price_ade_named                   = (isset($array["table_th_price_ade_named"]))?$array["table_th_price_ade_named"]:"Price After Dis Exc";
                $table_th_price_adi_named                   = (isset($array["table_th_price_adi_named"]))?$array["table_th_price_adi_named"]:"Price After Dis Inc";
                $table_th_subtotal_named                    = (isset($array["table_th_subtotal_named"]))?$array["table_th_subtotal_named"]:"Subtotal";
                $currency_in_row                            = (isset($array["currency_in_row"]))?(($array["currency_in_row"] === "on")?true:false):true ; 
                $repeat_content_top                         = (isset($array["repeat_content_top"]))?(($array["repeat_content_top"] === "on")?true:false):true ; 
                $if_discount_zero                           = (isset($array["if_discount_zero"]))?(($array["if_discount_zero"] === "on")?true:false):false; 
                $bill_invoice_info_down_vat                 = (isset($array["bill_invoice_info_down_vat"]))?(($array["bill_invoice_info_down_vat"] === "on")?true:false):true ; 
                $bill_invoice_info_down_subtotal            = (isset($array["bill_invoice_info_down_subtotal"]))?(($array["bill_invoice_info_down_subtotal"] === "on")?true:false):true; 
                $bill_invoice_info_down_discount            = (isset($array["bill_invoice_info_down_discount"]))?(($array["bill_invoice_info_down_discount"] === "on")?true:false):true; 
                $bill_invoice_info_down_subtotal_after_dis  = (isset($array["bill_invoice_info_down_subtotal_after_dis"]))?(($array["bill_invoice_info_down_subtotal_after_dis"] === "on")?true:false):true; 
                $bold_left_invoice_info_customer_number     = (isset($array["bold_left_invoice_info_customer_number"]))?(($array["bold_left_invoice_info_customer_number"] === "on")?true:false):true; 
                $bold_left_invoice_info_customer_address    = (isset($array["bold_left_invoice_info_customer_address"]))?(($array["bold_left_invoice_info_customer_address"] === "on")?true:false):true; 
                $bold_left_invoice_info_customer_mobile     = (isset($array["bold_left_invoice_info_customer_mobile"]))?(($array["bold_left_invoice_info_customer_mobile"] === "on")?true:false):true; 
                $bold_left_invoice_info_customer_tax        = (isset($array["bold_left_invoice_info_customer_tax"]))?(($array["bold_left_invoice_info_customer_tax"] === "on")?true:false):true; 
                $bold_left_invoice_info_number              = (isset($array["bold_left_invoice_info_number"]))?(($array["bold_left_invoice_info_number"] === "on")?true:false) :true ; 
                $bold_left_invoice_info_project             = (isset($array["bold_left_invoice_info_project"]))?(($array["bold_left_invoice_info_project"] === "on")?true:false) :true ; 
                $bold_left_invoice_info_date                = (isset($array["bold_left_invoice_info_date"]))?(($array["bold_left_invoice_info_date"] === "on")?true:false) :true ; 
            
            
            }

    }
    
 
    ?>
<style>
    
    /* Table */
    .table-content,{
        width:  {{ $content_width .  " !important"  }};
    }
  
    .table-content table{
        border-radius:    {{ $content_table_border_radius .  " !important"  }};
        border-collapse:  {{ $collapse .  " !important" }};
        width:            {{ $content_table_width  .  " !important" }};
        
    }
    .table-content th{
        font-size:     {{ $content_table_th_font_size   .  " !important" }};
        text-align:    {{ $content_table_th_text_align    .  " !important" }}; 
        border-width:  {{ $content_table_th_border_width    .  " !important" }}; 
        border-style:  {{ $content_table_th_border_style    .  " !important" }}; 
        border-color:  {{ $content_table_th_border_color    .  " !important" }}; 
        padding:       {{ $content_table_th_padding    .  " !important" }}; 
        background-color: {{ "white" .  " !important" }};
        color: {{ "black" .  " !important" }};
    }
    .table-content td{
        font-size:     {{ $content_table_td_font_size     }};
        text-align:    {{ $content_table_td_text_align   .  " !important" }};
        border-width:  {{ $content_table_td_border_width    .  " !important" }}; 
        border-style:  {{ $content_table_td_border_style    .  " !important" }}; 
        border-color:  {{ $content_table_td_border_color    .  " !important" }}; 
        padding:       {{ $content_table_td_padding    .  " !important" }}; 
    }
    .tb_no{
        width: {{ $content_table_width_no  .  " !important" }};
    }
    .tb_td_no{
        font-size:  {{ $content_table_td_no_font_size     .  " !important" }};
        font-weight:   {{ $content_table_font_weight_no  .  " !important" }};
        text-align: {{ $content_table_td_no_text_align   .  " !important" }};
    }
    .tb_name{
        width:         {{ $content_table_width_name  .  " !important" }};
    }
    .tb_td_name{
        font-size:     {{ $content_table_font_size_name  .  " !important" }};
        text-align:    {{ $content_table_text_align_name  .  " !important" }};
        font-weight:   {{ $content_table_font_weight_name  .  " !important" }};
        color:{{ "black !important" }}
        font-family : {{"Arial"}}
    }
    .tb_qty{
        width: {{ $content_table_width_qty  .  " !important" }};
    }
    .tb_td_qty{
        font-size:  {{ $content_table_td_qty_font_size     .  " !important" }};
        font-weight:   {{ $content_table_font_weight_qty  .  " !important" }};
        text-align: {{ $content_table_td_qty_text_align   .  " !important" }};
    }
    .tb_discount{
        width: {{ $content_table_width_discount  .  " !important" }};
    }
    .tb_td_discount{
        font-size:  {{ $content_table_td_discount_font_size     .  " !important" }};
        font-weight:   {{ $content_table_font_weight_discount  .  " !important" }};
        text-align: {{ $content_table_td_discount_text_align   .  " !important" }};
    }
    .tb_subtotal{
        width: {{ $content_table_width_subtotal  .  " !important" }};
    }
    .tb_td_subtotal{
        font-size:  {{ $content_table_td_subtotal_font_size     .  " !important" }};
        font-weight:   {{ $content_table_font_weight_subtotal  .  " !important" }};
        text-align: {{ $content_table_td_subtotal_text_align   .  " !important" }};
    }
    .tb_price{
        width: {{ $content_table_width_price   .  " !important" }};
    }
    .tb_td_price{ 
        font-size:  {{ $content_table_td_price_font_size     .  " !important" }};
        font-weight:   {{ $content_table_font_weight_price  .  " !important" }};
        text-align: {{ $content_table_td_price_text_align   .  " !important" }};
    }
    .tb_code{
        width: {{ $content_table_width_code   .  " !important" }};
    }
    .tb_td_code{ 
        font-size:  {{ $content_table_td_code_font_size     .  " !important" }};
        font-weight:   {{ $content_table_font_weight_code  .  " !important" }};
        text-align: {{ $content_table_td_code_text_align   .  " !important" }};
    }
    .tb_img{
        width: {{ $content_table_width_img   .  " !important" }};
    }
    .tb_td_img{ 
        font-size:  {{ $content_table_td_img_font_size     .  " !important" }};
        font-weight:   {{ $content_table_font_weight_img  .  " !important" }};
        text-align: {{ $content_table_td_img_text_align   .  " !important" }};
    }
    .tb_price_bdi{
        width: {{ $content_table_width_price_bdi   .  " !important" }};
    }
    .tb_td_price_bdi{ 
        font-size:  {{ $content_table_td_price_bdi_font_size     .  " !important" }};
        font-weight:   {{ $content_table_font_weight_price_bdi  .  " !important" }};
        text-align: {{ $content_table_td_price_bdi_text_align   .  " !important" }};
    }
    .tb_price_ade{
        width: {{ $content_table_width_price_ade   .  " !important" }};
    }
    .tb_td_price_ade{ 
        font-size:  {{ $content_table_td_price_ade_font_size     .  " !important" }};
        font-weight:   {{ $content_table_font_weight_price_ade  .  " !important" }};
        text-align: {{ $content_table_td_price_ade_text_align   .  " !important" }};
    }
    .tb_price_adi{
        width: {{ $content_table_width_price_adi   .  " !important" }};
    }
    .tb_td_price_adi{ 
        font-size:  {{ $content_table_td_price_adi_font_size     .  " !important" }};
        font-weight:   {{ $content_table_font_weight_price_adi  .  " !important" }};
        text-align: {{ $content_table_td_price_adi_text_align   .  " !important" }};
    }

    /* top table */
    .left_top_table{
        width:        {{ $left_top_table_width   .  " !important" }};
        text-align:   {{ $left_top_table_text_align   .  " !important" }};
        font-size:    {{ $left_top_table_font_size   .  " !important" }};
        background-color:  {{ $background_color_invoice_info . " !important"}} ;
        color:  {{ $color_invoice_info . " !important"}} ;
        padding:  {{ $padding_invoice_info . " !important"}} ;
        }
    .top_table_row{
        
        background-color:  {{ $background_color_invoice_info . " !important"}} ;
       
        }
    .right_top_table{
        width:        {{ $right_top_table_width   .  " !important" }};
        text-align:   {{ $right_top_table_text_align   .  " !important" }};
        font-size:    {{ $right_top_table_font_size   .  " !important" }};
        background-color:  {{ $background_color_invoice_info . " !important"}} ;
        color:  {{ $color_invoice_info . " !important"}} ;
        padding:  {{ $padding_invoice_info . " !important"}} ;
    }
    .top_table table{
        width:             {{ $top_table_width   .  " !important" }};
        border-top-width:  {{ $top_table_border_width   .  " !important" }};
        border-top-style:  {{ $top_table_border_style   .  " !important" }};
        border-top-color:  {{ $top_table_border_color   .  " !important" }};
        margin-bottom:     {{ $top_table_margin_bottom   .  " !important" }};
        
    }
    .top_table table td{
        border-width:  {{ $top_table_td_border_width   .  " !important" }};
        border-style:  {{ $top_table_td_border_style   .  " !important" }};
        border-color:  {{ $top_table_td_border_color   .  " !important" }};
    }
    /* bottom table */
    .bottom_table table{
        width:             {{ $bottom_table_width   .  " !important" }};
        border-top-width:  {{ $bottom_table_border_width   .  " !important" }};
        border-top-style:  {{ $bottom_table_border_style   .  " !important" }};
        border-top-color:  {{ $bottom_table_border_color   .  " !important" }};
        margin-bottom:     {{ $bottom_table_margin_bottom   .  " !important" }};
        margin-top:        {{ $bottom_table_margin_top   .  " !important" }};
        
    }
    /* .bottom_table table td{
        border-width:  {{ $bottom_table_td_border_width   .  " !important" }};
        border-style:  {{ $bottom_table_td_border_style   .  " !important" }};
        border-color:  {{ $bottom_table_td_border_color   .  " !important" }};
    } */
    .left_bottom_table{
        width:        {{ $left_bottom_table_width   .  " !important" }};
        text-align:   {{ $left_bottom_table_text_align   .  " !important" }};
        font-size:    {{ $left_bottom_table_font_size   .  " !important" }};
    }
    .right_bottom_table{
        width:        {{ $right_bottom_table_width   .  " !important" }};
        text-align:   {{ $right_bottom_table_text_align   .  " !important" }};
        font-size:    {{ $right_bottom_table_font_size   .  " !important" }};
    }
   
    .left_bottom_table{
        border-width:        {{ $left_bottom_table_td_bor_width   .  " !important" }};
        border-style:        {{ $left_bottom_table_td_bor_style   .  " !important" }};
        border-color:        {{ $left_bottom_table_td_bor_color   .  " !important" }};
    }
    .right_bottom_table{
        border-width:        {{ $right_bottom_table_td_bor_width   .  " !important" }};
        border-style:        {{ $right_bottom_table_td_bor_style   .  " !important" }};
        border-color:        {{ $right_bottom_table_td_bor_color   .  " !important" }};
        
    }
    /* bill info */
    .bill_info{
        width:  {{ $bill_table_info_width  .  " !important" }};
        border-top-width:  {{ $bill_table_border_width   .  " !important" }};
        border-top-style:  {{ $bill_table_border_style   .  " !important" }};
        border-top-color:  {{ $bill_table_border_color   .  " !important" }};
        margin-bottom:     {{ $bill_table_margin_bottom   .  " !important" }};
        margin-top:        {{ $bill_table_margin_top   .  " !important" }};
    }
    /* .bill_info td{
        font-size:     {{ $bill_table_info_width  .  " !important" }};
        border-width:  {{ $bill_table_info_border_width  .  " !important" }};
        border-style:  {{ $bill_table_info_border_style  .  " !important" }};
        border-color:  {{ $bill_table_info_border_color  .  " !important" }};
    } */
    .left_bill_table_td{
        width:        {{ $bill_table_left_td_width  .  " !important" }};
        font-size:    {{ $bill_table_left_td_font_size  .  " !important" }};
        font-weight:  {{ $bill_table_left_td_weight  .  " !important" }};
        text-align:   {{ $bill_table_left_td_text_align  .  " !important" }}; 
        border-width:   {{ $bill_table_left_td_border_width  .  " !important" }}; 
        border-style:   {{ $bill_table_left_td_border_style  .  " !important" }}; 
        border-color:   {{ $bill_table_left_td_border_color  .  " !important" }}; 
        padding-left:   {{ $bill_table_left_td_padding_left  .  " !important" }};          
    }
    .right_bill_table_td{
        width:        {{ $bill_table_right_td_width  .  " !important" }};
        font-size:    {{ $bill_table_right_td_font_size  .  " !important" }};
        font-weight:  {{ $bill_table_right_td_weight  .  " !important" }};
        text-align:   {{ $bill_table_right_td_text_align  .  " !important" }}; 
        border-width:   {{ $bill_table_right_td_border_width  .  " !important" }}; 
        border-style:   {{ $bill_table_right_td_border_style  .  " !important" }}; 
        border-color:   {{ $bill_table_right_td_border_color  .  " !important" }};          
        padding-left:   {{ $bill_table_right_td_padding_left  .  " !important" }};          
    }
    /* line rows */
    .line_rows{
        position: relative;
        width:            {{ $line_bill_table_width   .  " !important" }};
        height:           {{ $line_bill_table_height  .  " !important" }};     
        background-color: {{ $line_bill_table_color   .  " !important" }};
        border-width:     {{ $line_bill_table_border_width  .  " !important" }};         
        border-style:     {{ $line_bill_table_border_style  .  " !important" }};         
        border-color:     {{ $line_bill_table_border_color  .  " !important" }};         
    }
    .left_invoice_info{
        text-align:  {{ $left_invoice_info . " !important"}} ;
        
    }
    .right_invoice_info{
        text-align: {{ $right_invoice_info . " !important"}} ;
      
    }
    .bold_left_invoice_info{
        font-weight: {{ $bold_left_invoice_info . " !important" }};
        border-width:{{ $bold_left_invoice_info_br_width . " !important"}};
        border-style:{{ $bold_left_invoice_info_br_style . " !important"}};
        border-color:{{ $bold_left_invoice_info_br_color . " !important"}};
        text-align:  {{ $bold_left_invoice_info_text_align . " !important"}};
    }
    .bold_right_invoice_info{
        font-weight: {{ $bold_right_invoice_info . " !important" }};
        border-width:{{ $bold_right_invoice_info_br_width . " !important"}};
        border-style:{{ $bold_right_invoice_info_br_style . " !important"}};
        border-color:{{ $bold_right_invoice_info_br_color . " !important"}};
        text-align:  {{ $bold_right_invoice_info_text_align . " !important"}};
    }
  

    .contentss{
            position: relative;
            top: {{ $body_content_top   }};
            margin-left:   {{ $body_content_margin_left   }};
            margin-right:  {{ $body_content_margin_right  }};
            margin-bottom: {{ $body_content_margin_bottom }};
            border-radius: {{"1px"}};
            border:        {{ "0px solid black" }};
        }
    .contentss_repreat{
            position: relative;
            top:           {{ $body_content_top_repeat  }};
            margin-left:   {{ $body_content_margin_left   }};
            margin-right:  {{ $body_content_margin_right  }};
            margin-bottom: {{ $body_content_margin_bottom }};
            border-radius: {{"1px"}};
            border:        {{ "0px solid black" }};
        }
   .terms{
        margin-top:5% ;
         
    }
   .terms table{
        background-color: rgba(255, 0, 0, 0);
        border:0px solid black;
    }
   .terms table ,
   .terms table tbody,
   .terms table tbody tr,
   .terms table tbody tr td{
        background-color: rgba(0, 255, 132, 0);
        border:0px solid rgb(255, 255, 255) !important;
    }
     
</style>

@php

    // GLOBAL VARIABLE
    $total_qty                  = 0;
    $subtotal                   = 0;
    $price                      = 0;
    $price_bdi                  = 0;
    $price_discount             = 0;
    $price_ade                  = 0;
    $price_adi                  = 0;
    $discount                   = number_format($data->discount_amount,config("constats.currency_precision"));
    $check_purchase_dis         = 0;  
    $check_sale_dis             = 0; 
    $choose_product_description = 0; 
@endphp

<div @if($repeat_content_top == 1) class="contentss_repreat" @else class="contentss" @endif >
    @if($repeat_content_top == 0)
        {{-- top section --}}
        @if($top_table_section == true)
            <div class="top_table">
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
                                                <td class="{{$class_width_right}} bold_right_invoice_info">{{($data->invoice_no != null)?$data->invoice_no:$data->ref_no}}</td>
                                            </tr>    
                                        @endif
                                        @if($bold_left_invoice_info_project == true)
                                            <tr>
                                                <td class="{{$class_width_left}} bold_left_invoice_info">{{ $project_no  }}</td>
                                                <td class="{{$class_width_right}} bold_right_invoice_info">{{($data->project_no != null)?$data->project_no:""}}</td>
                                            </tr>    
                                        @endif
                                        @if($bold_left_invoice_info_date == true)
                                            <tr>
                                                <td class="{{$class_width_left}} bold_left_invoice_info">{{ $date_name  }}</td>
                                                <td class="{{$class_width_right}} bold_right_invoice_info">{{($data->transaction_date != null)?   @format_date($data->transaction_date)  :"&nbsp;"}}</td>
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
                                                    $name = ".";
                                                }
                                            @endphp
                                            <tr>
                                                <td class="{{$class_width_left}} bold_left_invoice_info">{{ $customer_no  }}</td>
                                                @if($name == "arabic")
                                                    <td class="{{$class_width_right_right}} ">{!! ($data->contact->first_name)? $data->contact->first_name :" " !!}</td>
                                                @else
                                                    <td class="{{$class_width_right_right}} bold_right_invoice_info">{!! ($data->contact->first_name)? $data->contact->first_name :" " !!}</td>
                                                @endif
                                            </tr>
                                        @endif 
                                        @if($bold_left_invoice_info_customer_address == true )
                                            <tr>
                                                <td class="{{$class_width_left_right}} bold_left_invoice_info">{{ $address_name  }}</td>
                                                <td class="{{$class_width_right_right}} bold_right_invoice_info">{{ ($data->contact->address != null)?$data->contact->address:"."}}</td>
                                            </tr>
                                        @endif 
                                        @if($bold_left_invoice_info_customer_mobile == true )
                                            <tr>
                                                <td class="{{$class_width_left_right}} bold_left_invoice_info">{{ $mobile_name  }}</td>
                                                <td class="{{$class_width_right_right}} bold_right_invoice_info">{{ ($data->contact->mobile != null)?$data->contact->mobile:"."}}</td>
                                            </tr> 
                                        @endif 
                                        @if($bold_left_invoice_info_customer_tax == true )
                                            <tr>
                                                <td class="{{$class_width_left_right}} bold_left_invoice_info">{{ $tax_name  }}</td>
                                                <td class="{{$class_width_right_right}} bold_right_invoice_info">{{ ($data->contact->tax_number != null)?$data->contact->tax_number:"."}}</td>
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
    {{-- table content --}}
    @if($content_table_section == true)
        <div class="table-content">
            <table>
                @if(isset($print))
                    @if($print['Form_type'] == "Purchase" || $print['Form_type'] == "Return_Purchase") 
                        @foreach($data->purchase_lines as $ke => $item)
                            @if($item->discount_percent > 0)
                            @php $check_purchase_dis = 1; @endphp
                            @endif
                        @endforeach
                    @else
                        @if(count($data->sell_lines)>0)
                            @foreach($data->sell_lines as $ke => $item)
                                @if($item->line_discount_amount > 0)
                                @php $check_sale_dis = 1; @endphp
                                @endif
                            @endforeach 
                        @endif 
                    @endif
                @else
                @endif
                @php $list_img = []; @endphp
                <thead class="hed_table">
                    <tr>
                        @if($table_th_no == true)
                        <th class="tb_no">{!!$table_th_no_named!!}</th>
                        @endif
                        @if($table_th_code == true)
                        <th class="tb_code">{!!$table_th_code_named!!}</th> 
                        @endif
                        @if($table_th_name == true)
                        <th class="tb_name">{!!$table_th_name_named!!}</th> 
                        @endif
                        @if($table_th_img == true)
                        <th class="tb_img">{!!$table_th_img_named!!}</th> 
                        @endif
                        @if($table_th_qty == true)
                        <th class="tb_qty">{!!$table_th_qty_named!!}</th>
                        @endif
                        @if($table_th_price == true)
                        <th class="tb_price">{!!$table_th_price_named!!}</th>
                        @endif
                        
                        @if($table_th_price_bdi == true)
                        <th class="tb_price_bdi">{!!$table_th_price_bdi_named!!}</th>
                        @endif

                        @if($if_discount_zero == true)
                            @if(isset($print))
                                @if($print['Form_type'] == "Purchase" || $print['Form_type'] == "Return_Purchase")
                                    @if($check_purchase_dis == 1 )
                                        @if($table_th_discount == true)
                                            <th class="tb_discount">{!!$table_th_discount_named!!}</th>
                                        @endif
                                        @if($table_th_price_ade == true)
                                        <th class="tb_price_ade">{!!$table_th_price_ade_named!!}</th>
                                        @endif
                                        @if($table_th_price_adi == true)
                                        <th class="tb_price_adi">{!!$table_th_price_adi_named!!}</th>
                                        @endif
                                    @endif
                                @else
                                    @if($check_sale_dis == 1 )
                                        @if($table_th_discount == true)
                                            <th class="tb_discount">{!!$table_th_discount_named!!}</th>
                                        @endif
                                        @if($table_th_price_ade == true)
                                        <th class="tb_price_ade">{!!$table_th_price_ade_named!!}</th>
                                        @endif
                                        @if($table_th_price_adi == true)
                                        <th class="tb_price_adi">{!!$table_th_price_adi_named!!}</th>
                                        @endif
                                    @endif    
                                @endif 
                            @else
                                @if($check_sale_dis == 1 )
                                    @if($table_th_discount == true)
                                        <th class="tb_discount">{!!$table_th_discount_named!!}</th>
                                    @endif
                                    @if($table_th_price_ade == true)
                                    <th class="tb_price_ade">{!!$table_th_price_ade_named!!}</th>
                                    @endif
                                    @if($table_th_price_adi == true)
                                    <th class="tb_price_adi">{!!$table_th_price_adi_named!!}</th>
                                    @endif
                                @endif    
                            @endif   
                        @else
                            @if($table_th_discount == true)
                                <th class="tb_discount">{!!$table_th_discount_named!!}</th>
                            @endif
                            @if($table_th_price_ade == true)
                            <th class="tb_price_ade">{!!$table_th_price_ade_named!!}</th>
                            @endif
                            @if($table_th_price_adi == true)
                            <th class="tb_price_adi">{!!$table_th_price_adi_named!!}</th>
                            @endif
                        @endif
                       
                        @if($table_th_subtotal == true)
                        <th class="tb_subtotal">{!!$table_th_subtotal_named!!}</th>
                        @endif
                    </tr>
                </thead>
                <tbody >
                    @if(isset($print))
                        @if($print['Form_type'] == "Purchase" || $print['Form_type'] == "Return_Purchase")
                            @foreach($data->purchase_lines as $ke => $item)
                                @php 
                                    $description        = ($choose_product_description == 1)?$item->product->product_description:$item->purchase_note;
                                @endphp
                                <tr>
                                    @if($table_th_no == true)
                                    <td class="tb_td_no">{{$ke+1}}</td>
                                    @endif
                                    @if($table_th_code == true)
                                    <td class="tb_td_code">{!!  "<B>" . $item->product->sku . "</B>" !!}</td>
                                    @endif
                                    @if($table_th_name == true)
                                        <td class="tb_td_name" style="font-family:Arial, Helvetica, sans-serif !important">{!! "<B>" . $item->product->name . "</B><br> " . $description . "" !!}</td>
                                    @endif
                                    @if($table_th_img == true)
                                        @php
                                            if($item->product->image){
                                                if($item->product->image_url){
                                                    $path_image  = $item->product->image_path_second ;
                                                    // $path_image  = public_path("image_path") ;
                                                    try{
                                                        if($path_image != null){
                                                            $img_dir     = file_get_contents($path_image);
                                                            $row_type    = pathinfo($path_image,PATHINFO_EXTENSION);
                                                            $row_pic     = 'data:image/' . $row_type . ';base64,' . base64_encode($img_dir);
                                                            $row_img_url   =    $row_pic;
                                                        }else{
                                                            $row_img_url = null;
                                                        }
                                                    }catch(Exception $ex){
                                                        //Process the exception
                                                        $row_img_url = null;
                                                    }
                                                
                                                }else{
                                                    $row_img_url = null;
                                                }
                                            }else{
                                                $row_img_url = null;
                                            }
                                        @endphp
                                    <td class="tb_td_img"> @if($row_img_url != null) <img src="{{$row_img_url }}"  width="100px" height="100px" alt="item-image"> @else {{ "No Image" }} @endif  </td>
                                    @endif
                                    @if($table_th_qty == true)
                                    <td class="tb_td_qty">{{$item->quantity}}</td>
                                    @endif
                                    @if($table_th_price == true)
                                    <td class="tb_td_price">{{number_format($item->pp_without_discount ,config("constants.currency_precision"))}} @if($currency_in_row == true) {{" " .  ($currency)?(($currency->currency)?$currency->currency->symbol:""):"" }} @endif</td>
                                    @endif
                                    @if($table_th_price_bdi == true)
                                    <td class="tb_td_price_bdi">{{number_format(($item->pp_without_discount*$item->discount_percent/100)+$item->pp_without_discount,config("constants.currency_precision"))}} @if($currency_in_row == true) {{ " " . ($currency)?(($currency->currency)?$currency->currency->symbol:""):"" }} @endif</td>
                                    @endif
                                    @if($if_discount_zero == true)
                                        @if($check_purchase_dis == 1 )
                                            @if($table_th_discount == true)
                                            <td class="tb_td_discount">{{number_format($item->discount_percent,config("constants.currency_precision"))}} @if($currency_in_row == true) {{" " .  ($currency)?(($currency->currency)?$currency->currency->symbol:""):"" }} @endif</td>
                                            @endif
                                            @if($table_th_price_ade == true)
                                            <td class="tb_td_price_ade">{{number_format($item->purchase_price,config("constants.currency_precision"))}} @if($currency_in_row == true) {{ " " . ($currency)?(($currency->currency)?$currency->currency->symbol:""):"" }} @endif</td>
                                            @endif
                                            @if($table_th_price_adi == true)
                                            <td class="tb_td_price_adi">{{number_format(($item->purchase_price + $item->item_tax),config("constants.currency_precision"))}} @if($currency_in_row == true) {{ " " . ($currency)?(($currency->currency)?$currency->currency->symbol:""):"" }} @endif</td>
                                            @endif
                                        @endif
                                    @else
                                        @if($table_th_discount == true)
                                        <td class="tb_td_discount">{{number_format($item->discount_percent ,config("constants.currency_precision"))}} @if($currency_in_row == true) {{" " .  ($currency)?(($currency->currency)?$currency->currency->symbol:""):"" }} @endif</td>
                                        @endif
                                        @if($table_th_price_ade == true)
                                        <td class="tb_td_price_ade">{{number_format($item->purchase_price,config("constants.currency_precision"))}} @if($currency_in_row == true) {{ " " . ($currency)?(($currency->currency)?$currency->currency->symbol:""):"" }} @endif</td>
                                        @endif
                                        @if($table_th_price_adi == true)
                                        <td class="tb_td_price_adi">{{number_format(($item->purchase_price + $item->item_tax),config("constants.currency_precision"))}} @if($currency_in_row == true) {{ " " . ($currency)?(($currency->currency)?$currency->currency->symbol:""):"" }} @endif</td>
                                        @endif
                                    @endif

                                    @if($table_th_subtotal == true)
                                    <td class="tb_td_subtotal">{{number_format($item->quantity*($item->purchase_price),config("constants.currency_precision"))}} @if($currency_in_row == true) {{ " " . ($currency)?(($currency->currency)?$currency->currency->symbol:""):"" }} @endif</td>
                                    @endif
                                </tr>
                                @php 
                                    // calculate invoice
                                    $total_qty       += $item->quantity; 
                                    $price           += $item->pp_without_discount; 
                                    $price_bdi       += ($item->pp_without_discount*$item->discount_percent/100)+$item->pp_without_discount; 
                                    $price_discount  += $item->discount_percent; 
                                    $price_ade       += $item->purchase_price; 
                                    $price_adi       += ($item->purchase_price + $item->item_tax); 
                                    $subtotal        += $item->quantity*($item->purchase_price); 
                                @endphp 
                            @endforeach
                        @else 
                            @if(count($data->sell_lines)>0)                 
                                @foreach($data->sell_lines as $ke => $item)
                                    @php 
                                        $description        = ($choose_product_description == 1)?$item->product->product_description:$item->sell_line_note;
                                    @endphp
                                    <tr>
                                        @if($table_th_no == true)
                                        <td class="tb_td_no">{{$ke+1}}</td>
                                        @endif
                                        @if($table_th_code == true)
                                        <td class="tb_td_code">{!!  "<B>" . $item->product->sku . "</B>" !!}</td>
                                        @endif
                                        @if($table_th_name == true)
                                            <td class="tb_td_name" style="font-family:Arial, Helvetica, sans-serif !important">{!! "<B>" . $item->product->name . "</B><br> " . $description . "" !!}</td>
                                        @endif
                                        @if($table_th_img == true)
                                            @php
                                                if($item->product->image){
                                                    if($item->product->image_url){
                                                        $path_image  = $item->product->image_path_second ;
                                                        // $path_image  = public_path("image_path") ;
                                                        try{
                                                            if($path_image != null){
                                                                $img_dir       = file_get_contents($path_image);
                                                                $row_type      = pathinfo($path_image,PATHINFO_EXTENSION);
                                                                $row_pic       = 'data:image/' . $row_type . ';base64,' . base64_encode($img_dir);
                                                                $list_img[]    = $path_image;
                                                                
                                                                $row_img_url   = $row_pic;
                                                            
                                                            }else{
                                                                $row_img_url = null;
                                                            }
                                                        }catch(Exception $ex){
                                                            //Process the exception
                                                            $row_img_url = null;
                                                        }
                                                        
                                                    }else{
                                                        $row_img_url = null;
                                                    }
                                                }else{
                                                    $row_img_url = null;
                                                }
                                            @endphp
                                         <td class="tb_td_img"> @if($row_img_url != null) <img src="{{$row_img_url}}"  width="100px" height="100px" alt="item-image"> @else {{ "No Image" }} @endif  </td> 
                                       
                                        @endif
                                        @if($table_th_qty == true)
                                        <td class="tb_td_qty">{{$item->quantity}}</td>
                                        @endif
                                        @if($table_th_price == true)
                                        <td class="tb_td_price">{{number_format($item->unit_price_before_discount,config("constants.currency_precision"))}} @if($currency_in_row == true) {{" " .  ($currency)?(($currency->currency)?$currency->currency->symbol:""):"" }} @endif</td>
                                        @endif
                                        @if($table_th_price_bdi == true)
                                        <td class="tb_td_price_bdi">{{number_format($item->unit_price_inc_tax,config("constants.currency_precision"))}} @if($currency_in_row == true) {{ " " . ($currency)?(($currency->currency)?$currency->currency->symbol:""):"" }} @endif</td>
                                        @endif
                                        @if($if_discount_zero == true)
                                            @if($check_sale_dis == 1 )
                                                @if($table_th_discount == true)
                                                    <td class="tb_td_discount">{{number_format($item->line_discount_amount,config("constants.currency_precision"))}} @if($currency_in_row == true) {{" " .  ($currency)?(($currency->currency)?$currency->currency->symbol:""):"" }} @endif</td>
                                                @endif
                                                @if($table_th_price_ade == true)
                                                <td class="tb_td_price_ade">{{number_format($item->unit_price,config("constants.currency_precision"))}} @if($currency_in_row == true) {{ " " . ($currency)?(($currency->currency)?$currency->currency->symbol:""):"" }} @endif</td>
                                                @endif
                                                @if($table_th_price_adi == true)
                                                <td class="tb_td_price_adi">{{number_format(($item->unit_price + $item->item_tax),config("constants.currency_precision"))}} @if($currency_in_row == true) {{ " " . ($currency)?(($currency->currency)?$currency->currency->symbol:""):"" }} @endif</td>
                                                @endif
                                            @endif
                                        @else
                                            @if($table_th_discount == true)
                                                <td class="tb_td_discount">{{number_format($item->line_discount_amount,config("constants.currency_precision"))}} @if($currency_in_row == true) {{" " .  ($currency)?(($currency->currency)?$currency->currency->symbol:""):"" }} @endif</td>
                                            @endif
                                            @if($table_th_price_ade == true)
                                            <td class="tb_td_price_ade">{{number_format($item->unit_price,config("constants.currency_precision"))}} @if($currency_in_row == true) {{ " " . ($currency)?(($currency->currency)?$currency->currency->symbol:""):"" }} @endif</td>
                                            @endif
                                            @if($table_th_price_adi == true)
                                            <td class="tb_td_price_adi">{{number_format(($item->unit_price + $item->item_tax),config("constants.currency_precision"))}} @if($currency_in_row == true) {{ " " . ($currency)?(($currency->currency)?$currency->currency->symbol:""):"" }} @endif</td>
                                            @endif
                                        @endif

                                        @if($table_th_subtotal == true)
                                        <td class="tb_td_subtotal">{{number_format($item->quantity*($item->unit_price),config("constants.currency_precision"))}} @if($currency_in_row == true) {{ " " . ($currency)?(($currency->currency)?$currency->currency->symbol:""):"" }} @endif</td>
                                        @endif
                                    </tr>
                                    @php 
                                       
                                        // calculate invoice
                                        $total_qty       += $item->quantity; 
                                        $price           += $item->unit_price_before_discount; 
                                        $price_bdi       += $item->unit_price_inc_tax; 
                                        $price_discount  += $item->line_discount_amount; 
                                        $price_ade       += $item->unit_price; 
                                        $price_adi       += ($item->unit_price + $item->item_tax); 
                                        $subtotal        += $item->quantity*($item->unit_price); 
                                    @endphp 
                                @endforeach
                            @else
                                @foreach($data->payment_lines  as $line_sep)
                                    <tr>
                                        @if($table_th_no == true)
                                        <td class="tb_td_no">{{1}}</td>
                                        @endif
                                        @if($table_th_code == true)
                                        <td class="tb_td_code">{!!  "<B>###</B>" !!}</td>
                                        @endif
                                        @if($table_th_name == true)
                                        <td class="tb_td_name" style="font-family:Arial, Helvetica, sans-serif !important">{!! "<B>".$line_sep->note."</B><br>" !!}</td>
                                        @endif
                                        @if($table_th_img == true)
                                            @php $row_img_url = null; @endphp
                                        <td class="tb_td_img"> @if($row_img_url != null) <img src="{{$row_img_url }}"  width="100px" height="100px" alt="item-image"> @else {{ "No Image" }} @endif  </td>
                                        @endif
                                        @if($table_th_qty == true)
                                        <td class="tb_td_qty">{{1}}</td>
                                        @endif
                                        @if($table_th_price == true)
                                        @php 
                                            $tax_amount                          = \App\TaxRate::find($data->tax_id);
                                            $value                               = ($tax_amount)?$tax_amount->amount:0;
                                            $subtotal_line                       = $line_sep->amount * 100 / ( 100 + $value) ; 
                                            $value_tax                           = $line_sep->amount * $value / ( 100 + $value) ;
                                        @endphp
                                        <td class="tb_td_price">{{number_format($subtotal_line,config("constants.currency_precision"))}} @if($currency_in_row == true) {{" " .  ($currency)?(($currency->currency)?$currency->currency->symbol:""):"" }} @endif</td>
                                        @endif
                                        @if($table_th_price_bdi == true)
                                        <td class="tb_td_price_bdi">{{number_format($line_sep->amount,config("constants.currency_precision"))}} @if($currency_in_row == true) {{ " " . ($currency)?(($currency->currency)?$currency->currency->symbol:""):"" }} @endif</td>
                                        @endif
                                        @if($if_discount_zero == true)
                                            {{-- @if($table_th_discount == true)
                                            <td class="tb_td_discount">{{number_format(0,config("constants.currency_precision"))}} @if($currency_in_row == true) {{" " .  ($currency)?(($currency->currency)?$currency->currency->symbol:""):"" }} @endif</td>
                                            @endif --}}
                                        @else
                                            @if($table_th_discount == true)
                                            <td class="tb_td_discount">{{number_format(0,config("constants.currency_precision"))}} @if($currency_in_row == true) {{" " .  ($currency)?(($currency->currency)?$currency->currency->symbol:""):"" }} @endif</td>
                                            @endif
                                            @if($table_th_price_ade == true)
                                            <td class="tb_td_price_ade">{{number_format($subtotal_line,config("constants.currency_precision"))}} @if($currency_in_row == true) {{ " " . ($currency)?(($currency->currency)?$currency->currency->symbol:""):"" }} @endif</td>
                                            @endif
                                            @if($table_th_price_adi == true)
                                            <td class="tb_td_price_adi">{{number_format(($line_sep->amount),config("constants.currency_precision"))}} @if($currency_in_row == true) {{ " " . ($currency)?(($currency->currency)?$currency->currency->symbol:""):"" }} @endif</td>
                                            @endif
                                        @endif


                                        @if($table_th_subtotal == true)
                                        <td class="tb_td_subtotal">{{number_format(1 * $subtotal_line,config("constants.currency_precision"))}} @if($currency_in_row == true) {{ " " . ($currency)?(($currency->currency)?$currency->currency->symbol:""):"" }} @endif</td>
                                        @endif
                                    </tr>
                                    @php 
                                        // calculate invoice
                                        $total_qty       = 1; 
                                        $price           = $subtotal_line; 
                                        $price_bdi       = $line_sep->amount; 
                                        $price_discount  = 0; 
                                        $price_ade       = $subtotal_line; 
                                        $price_adi       = $line_sep->amount; 
                                        $subtotal        = 1*($subtotal_line); 
                                    @endphp
                                @endforeach 
                            @endif
                        @endif
                    
                    @else
                        @foreach($data->sell_lines as $ke => $item)
                            @php 
                                $description        = ($choose_product_description == 1)?$item->product->product_description:$item->sell_line_note;
                            @endphp
                            <tr>
                                @if($table_th_no == true)
                                <td class="tb_td_no">{{$ke+1}}</td>
                                @endif
                                @if($table_th_code == true)
                                <td class="tb_td_code">{!!  "<B>" . $item->product->sku . "</B>" !!}</td>
                                @endif
                                @if($table_th_name == true)
                                <td class="tb_td_name" style="font-family:Arial, Helvetica, sans-serif !important">{!! "<B>" . $item->product->name . "</B><br> " . $description . "" !!}</td>
                                @endif
                                @if($table_th_img == true)
                                    @php
                                    
                                        if($item->product->image){
                                            if($item->product->image_url){
                                                $path_image  = $item->product->image_path_second ;
                                                // $path_image  = public_path("image_path") ;
                                                try{
                                                    if($path_image != null){
                                                        $img_dir     = file_get_contents($path_image);
                                                        $row_type    = pathinfo($path_image,PATHINFO_EXTENSION);
                                                        $row_pic     = 'data:image/' . $row_type . ';base64,' . base64_encode($img_dir);
                                                        $row_img_url   =    $row_pic;
                                                    }else{
                                                        $row_img_url = null;
                                                    }
                                                }catch(Exception $ex){
                                                    //Process the exception
                                                    $row_img_url = null;
                                                }
                                                
                                            }else{
                                                $row_img_url = null;
                                            }
                                        }else{
                                            $row_img_url = null;
                                        }
                                    @endphp
                                <td class="tb_td_img"> @if($row_img_url != null) <img src="{{$row_img_url }}"  width="100px" height="100px" alt="item-image"> @else {{ "No Image" }} @endif  </td>
                                @endif
                                @if($table_th_qty == true)
                                <td class="tb_td_qty">{{$item->quantity}}</td>
                                @endif
                                @if($table_th_price == true)
                                <td class="tb_td_price">{{number_format($item->unit_price_before_discount,config("constants.currency_precision"))}} @if($currency_in_row == true) {{" " .  ($currency)?(($currency->currency)?$currency->currency->symbol:""):"" }} @endif</td>
                                @endif
                                @if($table_th_price_bdi == true)
                                <td class="tb_td_price_bdi">{{number_format($item->unit_price_inc_tax,config("constants.currency_precision"))}} @if($currency_in_row == true) {{ " " . ($currency)?(($currency->currency)?$currency->currency->symbol:""):"" }} @endif</td>
                                @endif
                                @if($if_discount_zero == true)
                                    @if($check_sale_dis == 1 )
                                        @if($table_th_discount == true)
                                        <td class="tb_td_discount">{{number_format($item->line_discount_amount,config("constants.currency_precision"))}} @if($currency_in_row == true) {{" " .  ($currency)?(($currency->currency)?$currency->currency->symbol:""):"" }} @endif</td>
                                        @endif
                                        @if($table_th_price_ade == true)
                                        <td class="tb_td_price_ade">{{number_format($item->unit_price,config("constants.currency_precision"))}} @if($currency_in_row == true) {{ " " . ($currency)?(($currency->currency)?$currency->currency->symbol:""):"" }} @endif</td>
                                        @endif
                                        @if($table_th_price_adi == true)
                                        <td class="tb_td_price_adi">{{number_format(($item->unit_price + $item->item_tax),config("constants.currency_precision"))}} @if($currency_in_row == true) {{ " " . ($currency)?(($currency->currency)?$currency->currency->symbol:""):"" }} @endif</td>
                                        @endif
                                    @endif
                                @else
                                    @if($table_th_discount == true)
                                    <td class="tb_td_discount">{{number_format($item->line_discount_amount,config("constants.currency_precision"))}} @if($currency_in_row == true) {{" " .  ($currency)?(($currency->currency)?$currency->currency->symbol:""):"" }} @endif</td>
                                    @endif
                                    @if($table_th_price_ade == true)
                                    <td class="tb_td_price_ade">{{number_format($item->unit_price,config("constants.currency_precision"))}} @if($currency_in_row == true) {{ " " . ($currency)?(($currency->currency)?$currency->currency->symbol:""):"" }} @endif</td>
                                    @endif
                                    @if($table_th_price_adi == true)
                                    <td class="tb_td_price_adi">{{number_format(($item->unit_price + $item->item_tax),config("constants.currency_precision"))}} @if($currency_in_row == true) {{ " " . ($currency)?(($currency->currency)?$currency->currency->symbol:""):"" }} @endif</td>
                                    @endif
                                @endif
                                @if($table_th_subtotal == true)
                                <td class="tb_td_subtotal">{{number_format($item->quantity*($item->unit_price),config("constants.currency_precision"))}} @if($currency_in_row == true) {{ " " . ($currency)?(($currency->currency)?$currency->currency->symbol:""):"" }} @endif</td>
                                @endif
                            </tr>
                            @php 
                                // calculate invoice
                                $total_qty       += $item->quantity; 
                                $price           += $item->unit_price_before_discount; 
                                $price_bdi       += $item->unit_price_inc_tax; 
                                $price_discount  += $item->line_discount_amount; 
                                $price_ade       += $item->unit_price; 
                                $price_adi       += ($item->unit_price + $item->item_tax); 
                                $subtotal        += $item->quantity*($item->unit_price); 
                            @endphp 
                        @endforeach
                    @endif
                       
                </tbody>
                @if($footer_table == true)
                   
                    <tfoot>
                        <tr>
                            @if($table_th_no == true)
                            <th>{{"QTY : " . $total_qty }}</th>
                            @endif
                            @if($table_th_code == true)
                            <th>@if($table_th_no == false){{"QTY : " . $total_qty }}@endif</th>
                            @endif
                            @if($table_th_name == true)
                            <th>@if($table_th_no == false && $table_th_code == false){{"QTY : " . $total_qty }}@endif</th>
                            @endif
                            @if($table_th_img == true)
                            <th>@if($table_th_no == false && $table_th_code == false && $table_th_name == false){{"QTY : " . $total_qty }}@endif</th>
                            @endif
                            @if($table_th_qty == true)
                            <th>@if($table_th_no == false && $table_th_code == false && $table_th_name == false && $table_th_img == false){{"QTY : " . $total_qty }}@endif</th>
                            @endif
                            @if($table_th_price == true)
                            <th>{{number_format($price,config("constants.currency_precision"))}} @if($currency_in_row == true) {{" " .  ($currency)?(($currency->currency)?$currency->currency->symbol:""):"" }} @endif</th>
                            @endif
                            @if($table_th_price_bdi == true)
                            <th>{{number_format($price_bdi,config("constants.currency_precision"))}} @if($currency_in_row == true) {{" " .  ($currency)?(($currency->currency)?$currency->currency->symbol:""):"" }} @endif</th>
                            @endif
                            @if($if_discount_zero == true)
                                @if(isset($print))
                                    @if($print['Form_type'] == "Purchase" || $print['Form_type'] == "Return_Purchase")
                                        @if($check_purchase_dis == 1 )
                                            @if($table_th_discount == true)
                                                <th>{{number_format($price_discount,config("constants.currency_precision"))}} @if($currency_in_row == true) {{" " .  ($currency)?(($currency->currency)?$currency->currency->symbol:""):"" }} @endif</th>
                                            @endif
                                            @if($table_th_price_ade == true)
                                                <th>{{number_format($price_ade,config("constants.currency_precision"))}} @if($currency_in_row == true) {{" " .  ($currency)?(($currency->currency)?$currency->currency->symbol:""):"" }} @endif</th>
                                            @endif
                                            @if($table_th_price_adi == true)
                                                <th>{{number_format($price_adi,config("constants.currency_precision"))}} @if($currency_in_row == true) {{" " .  ($currency)?(($currency->currency)?$currency->currency->symbol:""):"" }} @endif</th>
                                            @endif
                                        @endif
                                    @else
                                        @if($check_sale_dis == 1 )
                                            @if($table_th_discount == true)
                                                <th>{{number_format($price_discount,config("constants.currency_precision"))}} @if($currency_in_row == true) {{" " .  ($currency)?(($currency->currency)?$currency->currency->symbol:""):"" }} @endif</th>
                                            @endif
                                            @if($table_th_price_ade == true)
                                                <th>{{number_format($price_ade,config("constants.currency_precision"))}} @if($currency_in_row == true) {{" " .  ($currency)?(($currency->currency)?$currency->currency->symbol:""):"" }} @endif</th>
                                            @endif
                                            @if($table_th_price_adi == true)
                                                <th>{{number_format($price_adi,config("constants.currency_precision"))}} @if($currency_in_row == true) {{" " .  ($currency)?(($currency->currency)?$currency->currency->symbol:""):"" }} @endif</th>
                                            @endif
                                        @endif    
                                    @endif    
                                @else
                                    @if($check_sale_dis == 1 )
                                        @if($table_th_discount == true)
                                            <th>{{number_format($price_discount,config("constants.currency_precision"))}} @if($currency_in_row == true) {{" " .  ($currency)?(($currency->currency)?$currency->currency->symbol:""):"" }} @endif</th>
                                        @endif
                                        @if($table_th_price_ade == true)
                                            <th>{{number_format($price_ade,config("constants.currency_precision"))}} @if($currency_in_row == true) {{" " .  ($currency)?(($currency->currency)?$currency->currency->symbol:""):"" }} @endif</th>
                                        @endif
                                        @if($table_th_price_adi == true)
                                            <th>{{number_format($price_adi,config("constants.currency_precision"))}} @if($currency_in_row == true) {{" " .  ($currency)?(($currency->currency)?$currency->currency->symbol:""):"" }} @endif</th>
                                        @endif
                                    @endif    
                                @endif
                            @else
                                @if($table_th_discount == true)
                                    <th>{{number_format($price_discount,config("constants.currency_precision"))}} @if($currency_in_row == true) {{" " .  ($currency)?(($currency->currency)?$currency->currency->symbol:""):"" }} @endif</th>
                                @endif
                                
                                @if($table_th_price_ade == true)
                                    <th>{{number_format($price_ade,config("constants.currency_precision"))}} @if($currency_in_row == true) {{" " .  ($currency)?(($currency->currency)?$currency->currency->symbol:""):"" }} @endif</th>
                                @endif
                                @if($table_th_price_adi == true)
                                    <th>{{number_format($price_adi,config("constants.currency_precision"))}} @if($currency_in_row == true) {{" " .  ($currency)?(($currency->currency)?$currency->currency->symbol:""):"" }} @endif</th>
                                @endif
                            @endif

                            @if($table_th_subtotal == true)
                            <th>{{number_format($subtotal,config("constants.currency_precision"))}} @if($currency_in_row == true) {{" " .  ($currency)?(($currency->currency)?$currency->currency->symbol:""):"" }} @endif</th>
                            @endif
                            
                            
                            
                            
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    @endif
    {{-- bottom section --}}
    @if($bottom_table_section == true)
        <div class="bottom_table">
            <table>
                <tbody style="border:0px solid black !important">
                    <tr>
                        {{-- left --}}
                        <td class="left_bottom_table">
                            {!!$left_bottom_table!!} 
                        </td >
                        {{-- right --}}
                        <td class="right_bottom_table">
                            
                            <table class="bill_info">
                                <tbody>
                                    {{-- subtotal --}}
                                    @if($bill_invoice_info_down_subtotal == true)
                                    <tr>
                                        <td class="left_bill_table_td">{{"SubTotal : "}}</td>
                                        <td class="right_bill_table_td">{{number_format($subtotal,config("constants.currency_precision")) ." "}} {{ ($currency)?(($currency->currency)?$currency->currency->symbol:""):"" }}</td>
                                    </tr>
                                    @endif
                                    @if($if_discount_zero == true)
                                        @if($data->discount_amount != 0)
                                            {{-- discount --}}
                                            @if($bill_invoice_info_down_discount == true)
                                                <tr>
                                                    <td class="left_bill_table_td">{{"Discount : "}}</td>
                                                    <td class="right_bill_table_td">{{$discount . " " }} {{ ($currency)?(($currency->currency)?$currency->currency->symbol:""):"" }}</td>
                                                </tr>
                                            @endif
                                            {{-- subtotal after dis --}}
                                            @if($bill_invoice_info_down_subtotal_after_dis == true)
                                                <tr>
                                                    <td class="left_bill_table_td">{{"SubTotal Aft Dis : "}} </td>
                                                    <td class="right_bill_table_td">{{number_format(($subtotal-$data->discount_amount),config("constants.currency_precision")) . " "}} {{ ($currency)?(($currency->currency)?$currency->currency->symbol:""):"" }}</td>
                                                </tr>
                                            @endif
                                        @endif
                                    @else
                                        {{-- discount --}}
                                        @if($bill_invoice_info_down_discount == true)
                                            <tr>
                                                <td class="left_bill_table_td">{{"Discount : "}}</td>
                                                <td class="right_bill_table_td">{{$discount . " " }} {{ ($currency)?(($currency->currency)?$currency->currency->symbol:""):"" }}</td>
                                            </tr>
                                        @endif
                                        {{-- subtotal after dis --}}
                                        @if($bill_invoice_info_down_subtotal_after_dis == true)
                                            <tr>
                                                <td class="left_bill_table_td">{{"SubTotal Aft Dis : "}} </td>
                                                <td class="right_bill_table_td">{{number_format(($subtotal-$data->discount_amount),config("constants.currency_precision")) . " "}} {{ ($currency)?(($currency->currency)?$currency->currency->symbol:""):"" }}</td>
                                            </tr>
                                        @endif
                                    @endif
                                    @php
                                        $tax           = \App\TaxRate::find($data->tax_id);
                                        $tax_amount    = (!empty($tax))?$tax->amount:0;
                                        $tax_after_dis = (($subtotal-$data->discount_amount) * $tax_amount) /100;
                                    @endphp
                                    {{-- vat --}}
                                    @if($bill_invoice_info_down_vat == true)
                                    <tr>
                                        @php
                                            $tax           = \App\TaxRate::find($data->tax_id);
                                            $tax_amount    = (!empty($tax))?$tax->amount:0;
                                            $tax_after_dis = (($subtotal-$data->discount_amount) * $tax_amount) /100;
                                        @endphp
                                        <td class="left_bill_table_td">{{"Vat : "}}</td>
                                        <td class="right_bill_table_td">{{number_format($tax_after_dis,config("constants.currency_precision")) . " "}} {{ ($currency)?(($currency->currency)?$currency->currency->symbol:""):"" }}</td>
                                    </tr>
                                    @endif
                                    <tr class="line_rows">
                                        <td colspan="2"></td>
                                        
                                    </tr>
                                    {{-- final total --}}
                                    <tr>
                                        <td class="left_bill_table_td">{{"FinalTotal : "}}</td>
                                        <td class="right_bill_table_td">{{number_format(($subtotal-$data->discount_amount)+$tax_after_dis,config("constants.currency_precision")) . " "}} {{ ($currency)?(($currency->currency)?$currency->currency->symbol:""):"" }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>

                </tbody>
                
                        
                     
            </table>
           
            @if($show_customer_signature == 1)
                <table style="width:100%;border:0px solid black !important;margin-bottom:200px !important" >
                    <tr>
                        <td style="width:40%;font-size:12px"><b>{{"Customer Signature : "}}</b></td>
                        <td style="width:10%;color:transparent">&nbsp;{{"Customer Signature : "}}</td>
                        <td style="width:10%;color:transparent">&nbsp;{{"Customer Signature : "}}</td>
                        <td style="width:40%;font-size:12px"><b>{{"Salesman Signature : "}}</b></td>
                    </tr>
                    <tr>
                        <th colspan="4">
                            <br>
                        </th>
                    </tr>
                </table>
            @endif
            @if($show_quotation_terms == 1)
                
                    <div class="terms" style="font-size:10px !important;padding:10px !important;">
                            @php
                                $trm = \App\Models\QuatationTerm::where("id",$data->additional_notes)->first();
                            @endphp
                            @if(!empty($trm))
                                    {!!  $trm->description  !!} 
                            @endif
                    </div>
                
            @endif
        </div>
        @endif
</div>
