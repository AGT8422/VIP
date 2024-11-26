@section('javascript')
<script type="text/javascript">

    function printArea(areaName) {
        var newcontent =
            document.getElementById(areaName).innerHTML;
        var actContents = document.body.innerHTML;
        document.body.innerHTML = newcontent;
        window.print();
        document.body.innerHTML = actContents;
    }
    var img_fileinput_setting = {
        showUpload: false,
        showPreview: true,
        browseLabel: LANG.file_browse_label,
        removeLabel: LANG.remove,
        previewSettings: {
            image: { width: 'auto', height: 'auto', 'max-width': '100%', 'max-height': '100%' },
        },
    };
    $('#upload_image').fileinput(img_fileinput_setting);
    $(document).on("change","#margin_top_page",function(){
        html   = $(this).val();
        result = (parseFloat(html)+parseFloat(.5)) ;
        $("#body_content_top").val(result+"cm"); 
        if(($("#left_top_content_id").val() != null && $("#left_top_content_id").val() != "" )){
            load_content_body($("#left_top_content_id").val());
        }else{
            load_content_body();
        }
    });
    $(document).on("change","#margin_bottom_page",function(){
        html   = $(this).val();
        result = (parseFloat(html)+parseFloat(4.5)) ;
        $("#body_content_margin_bottom").val(result+"cm");
        if(($("#left_top_content_id").val() != null && $("#left_top_content_id").val() != "" )){
            load_content_body($("#left_top_content_id").val());
        }else{
            load_content_body();
        }
    });
    $(document).on("change","#body_content_top",function(){
        html   = $(this).val();
        result = (parseFloat(html)-parseFloat(.5)) ;
        $("#margin_top_page").val(result+"cm"); 
        if(($("#left_top_content_id").val() != null && $("#left_top_content_id").val() != "" )){
            load_content_body($("#left_top_content_id").val());
        }else{
            load_content_body();
        }
    });
    $(document).on("change","#body_content_margin_bottom",function(){
        html   = $(this).val();
        result = (parseFloat(html)-parseFloat(4.5)) ;
        $("#margin_bottom_page").val(result+"cm");
        if(($("#left_top_content_id").val() != null && $("#left_top_content_id").val() != "" )){
            load_content_body($("#left_top_content_id").val());
        }else{
            load_content_body();
        }
    });
    // Header  
    $(document).on("change"
                ,"#header_font_size,#align_text_header,#header_width,#header_view"+
                ",#style_header,#header_font_weight,#header_border_width,#header_border_style"+
                ",#header_border_color,#header_padding_top,#header_padding_left,#header_padding_right"+
                ",#header_padding_bottom,#header_position,#header_style_letter,#header_top,#header_left"+
                ",#header_right,#header_bottom,#header_image_box_height,#header_box_image_color"+
                ",#header_box_image_background,#header_image_border_color,#header_image_border_style"+
                ",#header_image_view,#header_image_width,#header_image_height,#header_image_border_width"+
                ",#header_image_border_radius,#position_img_header,#align_image_header"+
                ",#header_other_view,#header_line_view,#header_line_border_color,#header_line_border_style"+
                ",#header_line_border_width,#header_line_radius,#header_line_width,#header_line_height"+
                ",#header_line_color,#header_line_margin_top,#header_other_border_color"+
                ",#header_other_border_style,#header_other_border_width,#align_other_header"+
                ",#header_other_border_radius,#header_other_width,#other_background_header"+
                ",#header_address_align,#header_address_font_size,#header_address_width"+
                ",#header_address_letter,#header_address_border_width,#header_address_border_style"+
                ",#header_address_border_color,#header_address_position,#header_address_top,#header_address_left"+
                ",#header_address_right,#header_address_bottom,#header_address_padding_top"+
                ",#header_address_padding_left,#header_address_padding_right,#header_address_padding_bottom"+
                ",#header_tax_align,#header_tax_font_size,#header_tax_width,#header_tax_letter"+
                ",#header_tax_border_width,#header_tax_border_style,#header_tax_border_color,#header_tax_position"+
                ",#header_tax_top,#header_tax_left,#header_tax_right,#header_tax_bottom"+
                ",#header_tax_padding_top,#header_tax_padding_left,#header_tax_padding_right,#header_tax_padding_bottom"+
                ",#header_bill_align,#header_bill_font_size,#header_bill_width,#header_bill_letter"+
                ",#header_bill_border_width,#header_bill_border_style,#header_bill_border_color,#header_bill_position"+
                ",#header_bill_top,#header_bill_left,#header_bill_right,#header_bill_bottom,#header_table_radius"+
                ",#header_bill_padding_top,#header_bill_padding_left,#header_bill_padding_right,#header_bill_padding_bottom"+
                ",#header_other_position,#header_other_top,#header_other_left,#header_other_right,#header_other_bottom"+
                ",#header_box_width,#header_box_border_radius,#header_box_border_width,#header_image_box_width,#header_image_box_margin"+
                ",#header_box_border_style,#header_box_border_color,#header_box_background,#header_image_box_border_radius"+
                ",#header_image_box_border_width,#header_image_box_border_left,#header_image_box_border_right,#header_table_color"+
                ",#header_image_box_border_bottom,#position_box_header_align,#header_image_box_background,#header_table_width" 
                
                ,function(){
                load_header($("#left_header_id").val());
                if($("#left_header_id").val() != null){
                    load_content_header($("#left_header_id").val());
                }else{
                    load_content_header();
                }
                // html = $(".title-header-setting").html();
                // $(".title-header-setting").remove();
                // innerHtml   = "<div class='title-header-setting' style='height:auto'></div>";
                // $(".se-header").append(innerHtml);
                // $(".title-header-setting").addClass("height_auto");
            
       
    });
    // Footer
    $(document).on("change"
                ,"#footer_font_size,#align_text_footer,#footer_width,#footer_view"+
                ",#style_footer,#footer_font_weight,#footer_border_width,#footer_border_style"+
                ",#footer_border_color,#footer_padding_top,#footer_padding_left,#footer_padding_right"+
                ",#footer_padding_bottom,#footer_position,#footer_style_letter,#footer_top,#footer_left"+
                ",#footer_right,#footer_bottom,#footer_image_box_height,#footer_box_image_color"+
                ",#footer_box_image_background,#footer_image_border_color,#footer_image_border_style"+
                ",#footer_image_view,#footer_image_width,#footer_image_height,#footer_image_border_width"+
                ",#footer_image_border_radius,#position_img_footer,#align_image_footer"+
                ",#footer_other_view,#footer_line_view,#footer_line_border_color,#footer_line_border_style"+
                ",#footer_line_border_width,#footer_line_radius,#footer_line_width,#footer_line_height"+
                ",#footer_line_color,#footer_line_margin_top,#footer_line_margin_bottom,#footer_other_border_color"+
                ",#footer_other_border_style,#footer_other_border_width,#align_other_footer"+
                ",#footer_other_border_radius,#footer_other_width,#other_background_footer"+
                ",#footer_address_align,#footer_address_font_size,#footer_address_width"+
                ",#footer_address_letter,#footer_address_border_width,#footer_address_border_style"+
                ",#footer_address_border_color,#footer_address_position,#footer_address_top,#footer_address_left"+
                ",#footer_address_right,#footer_address_bottom,#footer_address_padding_top"+
                ",#footer_address_padding_left,#footer_address_padding_right,#footer_address_padding_bottom"+
                ",#footer_tax_align,#footer_tax_font_size,#footer_tax_width,#footer_tax_letter"+
                ",#footer_tax_border_width,#footer_tax_border_style,#footer_tax_border_color,#footer_tax_position"+
                ",#footer_tax_top,#footer_tax_left,#footer_tax_right,#footer_tax_bottom"+
                ",#footer_tax_padding_top,#footer_tax_padding_left,#footer_tax_padding_right,#footer_tax_padding_bottom"+
                ",#footer_bill_align,#footer_bill_font_size,#footer_bill_width,#footer_bill_letter"+
                ",#footer_bill_border_width,#footer_bill_border_style,#footer_bill_border_color,#footer_bill_position"+
                ",#footer_bill_top,#footer_bill_left,#footer_bill_right,#footer_bill_bottom,#footer_table_radius"+
                ",#footer_bill_padding_top,#footer_bill_padding_left,#footer_bill_padding_right,#footer_bill_padding_bottom"+
                ",#footer_other_position,#footer_other_top,#footer_other_left,#footer_other_right,#footer_other_bottom"+
                ",#footer_box_width,#footer_box_border_radius,#footer_box_border_width,#footer_image_box_width,#footer_image_box_margin"+
                ",#footer_box_border_style,#footer_box_border_color,#footer_box_background,#footer_image_box_border_radius"+
                ",#footer_image_box_border_width,#footer_image_box_border_left,#footer_image_box_border_right,#footer_table_color"+
                ",#footer_image_box_border_bottom,#position_box_footer_align,#footer_image_box_background,#footer_table_width" 
                
                ,function(){
                // load_footer($("#left_footer_id").val());
                
                if($("#left_footer_id").val() != null && $("#left_footer_id").val() != ""){
                    load_content_footer($("#left_footer_id").val());
                }else{
                    load_content_footer();
                }
    
    });
    // body
    $(document).on("change"
                ,"#top_table_section,#top_table_td_border_width,#top_table_margin_bottom,#top_table_width"+
                ",#top_table_td_border_color,#top_table_td_border_style,#left_top_table_font_size"+
                ",#left_top_table_text_align,#left_top_table_width,#right_top_table_font_size"+
                ",#right_top_table_text_align,#right_top_table_width,#top_table_border_color"+
                ",#top_table_border_style,#top_table_border_width,#content_table_section"+
                ",#content_table_width,#content_width,#content_table_border_radius,#footer_table"+
                ",#content_table_th_border_color,#content_table_th_padding,#content_table_th_border_style"+
                ",#content_table_th_text_align,#content_table_th_border_width,#content_table_th_font_size"+
                ",#content_table_td_border_color,#content_table_td_padding,#content_table_td_border_style"+
                ",#content_table_td_text_align,#content_table_td_border_width,#content_table_td_font_size"+
                ",#content_table_td_no_text_align,#content_table_td_no_font_size"+
                ",#content_table_font_weight_no,#table_th_no,#content_table_width_no"+
                ",#content_table_text_align_name,#content_table_font_size_name"+
                ",#content_table_font_weight_name,#table_th_name,#content_table_width_name"+
                ",#content_table_td_qty_text_align,#content_table_td_qty_font_size"+
                ",#content_table_font_weight_qty,#content_table_width_qty,#table_th_qty"+
                ",#content_table_td_img_text_align,#content_table_td_img_font_size"+
                ",#content_table_font_weight_img,#content_table_width_img,#table_th_img"+
                ",#content_table_td_code_text_align,#content_table_td_code_font_size"+
                ",#content_table_font_weight_code,#content_table_width_code,#table_th_code"+
                ",#content_table_td_subtotal_text_align,#content_table_td_subtotal_font_size"+
                ",#content_table_font_weight_subtotal,#content_table_width_subtotal,#table_th_subtotal"+
                ",#content_table_td_discount_text_align,#content_table_td_discount_font_size"+
                ",#content_table_font_weight_discount,#content_table_width_discount,#table_th_discount"+
                ",#content_table_td_price_text_align,#content_table_td_price_font_size"+
                ",#content_table_font_weight_price,#content_table_width_price,#table_th_price"+
                ",#content_table_td_price_bdi_text_align,#content_table_td_price_bdi_font_size"+
                ",#content_table_font_weight_price_bdi,#content_table_width_price_bdi,#table_th_price_bdi"+
                ",#content_table_td_price_ade_text_align,#content_table_td_price_ade_font_size"+
                ",#content_table_font_weight_price_ade,#content_table_width_price_ade,#table_th_price_ade"+
                ",#content_table_td_price_adi_text_align,#content_table_td_price_adi_font_size"+
                ",#content_table_font_weight_price_adi,#content_table_width_price_adi,#table_th_price_adi"+
                ",#line_bill_table_td_margin_left,#line_bill_table_border_color,#line_bill_table_border_style"+
                ",#line_bill_table_border_width,#line_bill_table_color,#line_bill_table_height"+
                ",#line_bill_table_width,#bill_table_right_td_border_color,#bill_table_right_td_border_style"+
                ",#bill_table_right_td_padding_left,#bill_table_right_td_border_width,#bill_table_right_td_text_align"+
                ",#bill_table_right_td_weight,#bill_table_right_td_font_size,#bill_table_right_td_width"+
                ",#bill_table_left_td_border_color,#bill_table_left_td_border_style,#bill_table_left_td_padding_left"+
                ",#bill_table_left_td_border_width,#bill_table_left_td_text_align,#bill_table_left_td_weight"+
                ",#bill_table_left_td_font_size,#bill_table_left_td_width,#bill_table_border_color"+
                ",#bill_table_border_style,#bill_table_border_width,#bill_table_info_border_color"+
                ",#bill_table_info_border_style,#bill_table_margin_top,#bill_table_info_border_width"+
                ",#bill_table_margin_bottom,#bill_table_info_width,#right_bottom_table_td_bor_color"+
                ",#right_bottom_table_font_size,#right_bottom_table_td_bor_style,#right_bottom_table_text_align"+
                ",#right_bottom_table_td_bor_width,#right_bottom_table_width,#left_bottom_table_td_bor_color"+
                ",#left_bottom_table_font_size,#left_bottom_table_td_bor_style,#left_bottom_table_text_align"+
                ",#table_th_price_named,#table_th_price_adi_named,#table_th_price_ade_named,#table_th_subtotal_named"+
                ",#table_th_no_named,#table_th_price_bdi_named,#table_th_name_named,#table_th_img_named,#table_th_code_named,#table_th_qty_named,#table_th_discount_named"+
                ",#left_bottom_table_td_bor_width,#left_bottom_table_width,#bottom_table_section"+
                ",#left_invoice_info,#color_invoice_info,#right_invoice_info,#padding_invoice_info,#background_color_invoice_info"+
                ",#class_width_left_right,#class_width_right_right,#bold_right_invoice_info,#bold_right_invoice_info_br_width,#bold_right_invoice_info_br_style"+
                ",#bold_right_invoice_info_br_color,#bold_right_invoice_info_text_align"+
                ",#bold_left_invoice_info_customer_number,#bold_left_invoice_info_customer_address,#bold_left_invoice_info_customer_mobile,#bold_left_invoice_info_customer_tax,#bold_left_invoice_info_number,#bold_left_invoice_info_project,#bold_left_invoice_info_date"+
                ",#currency_in_row,#if_discount_zero,#bill_invoice_info_down_vat,#bill_invoice_info_down_subtotal,#bill_invoice_info_down_discount,#bill_invoice_info_down_subtotal_after_dis"+
                ",#class_width_left,#class_width_right,#bold_left_invoice_info,#bold_left_invoice_info_br_width,#bold_left_invoice_info_br_style,#bold_left_invoice_info_br_color"+
                ",#bold_left_invoice_info_text_align"+
                ",#body_content_top,#body_content_margin_left,#body_content_margin_right,#body_content_margin_bottom"+
                ",#show_customer_signature,#show_quotation_terms" 
                ,function(){
                if(($("#left_top_content_id").val() != null && $("#left_top_content_id").val() != "" )){
                    load_content_body($("#left_top_content_id").val());
                }else{
                    load_content_body();
                }
               
            
    });
    $(document).on("change"
                ,"#repeat_content_top",function(){
                if(($("#left_top_content_id").val() != null && $("#left_top_content_id").val() != "" )){
                    load_content_body($("#left_top_content_id").val());
                }else{
                    load_content_body();
                }
                load_header($("#left_header_id").val());
                if($("#left_header_id").val() != null){
                    load_content_header($("#left_header_id").val());
                }else{
                    load_content_header();
                }
               
            
    });
    $(document).on("change"
                ,"#page_number_view,#invoice_no,#customer_no,#project_no,#date_name,#address_name,#mobile_name,#tax_name",function(){
                if(($("#left_top_content_id").val() != null && $("#left_top_content_id").val() != "" )){
                    load_content_body($("#left_top_content_id").val());
                }else{
                    load_content_body();
                }
                if($("#left_footer_id").val() != null && $("#left_footer_id").val() != ""){
                    load_content_footer($("#left_footer_id").val());
                }else{
                    load_content_footer();
                }
                load_header($("#left_header_id").val());
                if($("#left_header_id").val() != null){
                    load_content_header($("#left_header_id").val());
                }else{
                    load_content_header();
                }
            
    });
    $(document).ready(function(){
        if($("#style_header").val() == "table"){ $('.table-section-setting').removeClass("hide"); }else{ $('.table-section-setting').addClass("hide"); }
        $("#style_header").on("change",function(){
            if($(this).val() == "table"){
                $('.table-section-setting').removeClass("hide");
            }else{
                $('.table-section-setting').addClass("hide");
            }
        })
    });
    $(".btn-contain-more").on("click",function(){
       element =  $(this); 
       html    =  $(this).closest(".paper-content");
       section = html.find(".section-content-setting");
        
            if(section.hasClass("hide")){
                element.html(" more --");
            }else{
                element.html(" more ++");
            }
        
       section.toggleClass("hide"); 
    });
    $(".btn-more").on("click",function(){
       element =  $(this); 
       html    =  $(this).closest(".section");
       section = html.find(".section-setting");
        
            if(section.hasClass("hide")){
                element.html(" more --");
            }else{
                element.html(" more ++");
            }
        
       section.toggleClass("hide"); 
    });
    $(".form_type_select").on("change",function(){
        element          =  $(this); 
        html             =  $(this).val();
       cheque_relation   = $(".cheque-relation");
       voucher_relation  = $(".voucher-relation");
       sale_relation     = $(".sale-relation");
        if(html == "Sale"){
            cheque_relation.addClass("hide");
            voucher_relation.addClass("hide");
            sale_relation.removeClass("hide");
        }else if(html == "Return_Sale"){
            cheque_relation.addClass("hide");
            voucher_relation.addClass("hide");
            sale_relation.removeClass("hide");
        }else if(html == "Voucher"){
            cheque_relation.addClass("hide");
            voucher_relation.removeClass("hide");
            sale_relation.addClass("hide");
        }else if(html == "Cheque"){
            cheque_relation.removeClass("hide");
            voucher_relation.addClass("hide");
            sale_relation.addClass("hide");
        }else{
            cheque_relation.addClass("hide");
            voucher_relation.addClass("hide");
            sale_relation.addClass("hide");
        }
        
    });
    left_header = $("#left_header").val();
     $("#left_header").on("change",function(){
        alert(left_header);
    });

    function load_header(header_left_layout=null,header_center_top_layout=null,header_center_middle_layout=null,header_center_last_layout=null){
        if( (header_left_layout != null && header_left_layout != "") || (header_center_top_layout != null && header_center_top_layout != "")||
        (header_center_middle_layout != null && header_center_middle_layout != "") || (header_center_last_layout != null && header_center_last_layout != "")
        ){return load_content_header(header_left_layout,header_center_top_layout,header_center_middle_layout,header_center_last_layout)}
        // @if(isset($edit_type)) 
        //   edit_type                      = {{$PrinterTemplate->id}};
        // @else
        //   edit_type                      = null;
        // @endif
        left_header                      = null;
        header_font_size                 = $("#header_font_size").val();
        header_text_align                = $("#align_text_header").val();
        header_width                     = $("#header_width").val();
        header_view                      = ($("#header_view").is(':checked'))?true:false;
        header_style                     = $("#style_header").val();
        header_weight                    = $("#header_font_weight").val();
        header_border_width              = $("#header_border_width").val();
        header_border_style              = $("#header_border_style").val();
        header_border_color              = $("#header_border_color").val();
        header_padding_top               = $("#header_padding_top").val();
        header_padding_left              = $("#header_padding_left").val();
        header_padding_right             = $("#header_padding_right").val();
        header_padding_bottom            = $("#header_padding_bottom").val();
        header_position                  = $("#header_position").val();
        header_style_letter              = $("#header_style_letter").val();
        header_top                       = $("#header_top").val();
        repeat_content_top               = $("#repeat_content_top").val();
        header_left                      = $("#header_left").val();
        header_right                     = $("#header_right").val();
        header_bottom                    = $("#header_bottom").val();
        header_image_box_height          = $("#header_image_box_height").val();
        header_box_image_color           = $("#header_box_image_color").val();
        header_box_image_background      = $("#header_box_image_background").val();
        header_image_border_color        = $("#header_image_border_color").val();
        header_image_border_style        = $("#header_image_border_style").val();
        header_image_view                = ($("#header_image_view").is(':checked'))?true:false;
        header_image_width               = $("#header_image_width").val();
        header_image_height              = $("#header_image_height").val();
        header_image_border_width        = $("#header_image_border_width").val();
        header_image_border_radius       = $("#header_image_border_radius").val();
        position_img_header              = $("#position_img_header").val();
        align_image_header               = $("#align_image_header").val(); 
        page_number_view                 = ($("#page_number_view").is(':checked'))?"true":false; 
        header_line_view                 = ($("#header_line_view").is(':checked'))?true:false; 
        header_other_view                = ($("#header_other_view").is(':checked'))?true:false;
        header_line_border_color         = $("#header_line_border_color").val(); 
        header_line_border_style         = $("#header_line_border_style").val(); 
        header_line_border_width         = $("#header_line_border_width").val(); 
        header_line_radius               = $("#header_line_radius").val(); 
        header_line_width                = $("#header_line_width").val(); 
        header_line_radius               = $("#header_line_radius").val(); 
        header_line_height               = $("#header_line_height").val(); 
        header_line_radius               = $("#header_line_radius").val(); 
        header_line_color                = $("#header_line_color").val(); 
        header_line_margin_top           = $("#header_line_margin_top").val(); 
        header_other_border_color        = $("#header_other_border_color").val(); 
        header_other_border_style        = $("#header_other_border_style").val(); 
        header_other_border_width        = $("#header_other_border_width").val(); 
        header_other_border_radius       = $("#header_other_border_radius").val(); 
        header_other_width               = $("#header_other_width").val(); 
        other_background_header          = $("#other_background_header").val(); 
        align_other_header               = $("#align_other_header").val(); 
        header_address_align             = $("#header_address_align").val();
        header_address_font_size         = $("#header_address_font_size").val();
        header_address_width             = $("#header_address_width").val();
        header_address_letter            = $("#header_address_letter").val();
        header_address_border_width      = $("#header_address_border_width").val();
        header_address_border_style      = $("#header_address_border_style").val();
        header_address_border_color      = $("#header_address_border_color").val();
        header_address_position          = $("#header_address_position").val();
        header_address_top               = $("#header_address_top").val();
        header_address_left              = $("#header_address_left").val();
        header_address_right             = $("#header_address_right").val();
        header_address_bottom            = $("#header_address_bottom").val();
        header_address_padding_top       = $("#header_address_padding_top").val();
        header_address_padding_left      = $("#header_address_padding_left").val();
        header_address_padding_right     = $("#header_address_padding_right").val();
        header_address_padding_bottom    = $("#header_address_padding_bottom").val();
        header_tax_align                 = $("#header_tax_align").val();
        header_tax_font_size             = $("#header_tax_font_size").val();
        header_tax_width                 = $("#header_tax_width").val();
        header_tax_letter                = $("#header_tax_letter").val();
        header_tax_border_width          = $("#header_tax_border_width").val();
        header_tax_border_style          = $("#header_tax_border_style").val();
        header_tax_border_color          = $("#header_tax_border_color").val();
        header_tax_position              = $("#header_tax_position").val();
        header_tax_top                   = $("#header_tax_top").val();
        header_tax_left                  = $("#header_tax_left").val();
        header_tax_right                 = $("#header_tax_right").val();
        header_tax_bottom                = $("#header_tax_bottom").val();
        header_tax_padding_top           = $("#header_tax_padding_top").val();
        header_tax_padding_left          = $("#header_tax_padding_left").val();
        header_tax_padding_right         = $("#header_tax_padding_right").val();
        header_tax_padding_bottom        = $("#header_tax_padding_bottom").val();
        header_bill_align                = $("#header_bill_align").val();
        header_bill_font_size            = $("#header_bill_font_size").val();
        header_bill_width                = $("#header_bill_width").val();
        header_bill_letter               = $("#header_bill_letter").val();
        header_bill_border_width         = $("#header_bill_border_width").val();
        header_bill_border_style         = $("#header_bill_border_style").val();
        header_bill_border_color         = $("#header_bill_border_color").val();
        header_bill_position             = $("#header_bill_position").val();
        header_bill_top                  = $("#header_bill_top").val();
        header_bill_left                 = $("#header_bill_left").val();
        header_bill_right                = $("#header_bill_right").val();
        header_bill_bottom               = $("#header_bill_bottom").val();
        header_bill_padding_top          = $("#header_bill_padding_top").val();
        header_bill_padding_left         = $("#header_bill_padding_left").val();
        header_bill_padding_right        = $("#header_bill_padding_right").val();
        header_bill_padding_bottom       = $("#header_bill_padding_bottom").val();
        header_other_position            = $("#header_other_position").val();
        header_other_top                 = $("#header_other_top").val();
        header_other_left                = $("#header_other_left").val();
        header_other_right               = $("#header_other_right").val();
        header_other_bottom              = $("#header_other_bottom").val();
        header_box_width                 = $("#header_box_width").val();
        header_box_border_radius         = $("#header_box_border_radius").val();
        header_box_border_width          = $("#header_box_border_width").val();
        header_box_border_style          = $("#header_box_border_style").val();
        header_box_border_color          = $("#header_box_border_color").val();
        header_box_background            = $("#header_box_background").val();
        header_image_box_width           = $("#header_image_box_width").val();
        header_image_box_margin          = $("#header_image_box_margin").val();
        header_image_box_border_radius   = $("#header_image_box_border_radius").val();
        header_image_box_border_width    = $("#header_image_box_border_width").val();
        header_image_box_border_style    = $("#header_image_box_border_style").val();
        header_image_box_border_color    = $("#header_image_box_border_color").val();
        position_box_header_align        = $("#position_box_header_align").val();
        header_image_box_background      = $("#header_image_box_background").val();
        header_table_width               = $("#header_table_width").val();
        header_table_color               = $("#header_table_color").val();
        header_table_radius              = $("#header_table_radius").val();
        
        invoice_no                       = $("#invoice_no").val();
        project_no                       = $("#project_no").val();
        customer_no                      = $("#customer_no").val();
        date_name                        = $("#date_name").val();
        address_name                     = $("#address_name").val();
        mobile_name                      = $("#mobile_name").val();
        tax_name                         = $("#tax_name").val();
         
        $.ajax({
            url:"/printer/header/style",
            dataType:"html",
            method:"GET",
            data:{
                // edit_type:edit_type, 
                header_font_size:header_font_size,
                header_width:header_width,
                header_text_align:header_text_align,
                header_view:header_view,
                header_style:header_style,
                header_weight:header_weight,
                header_border_width:header_border_width,
                header_border_style:header_border_style,
                header_border_color:header_border_color,
                header_padding_top:header_padding_top,
                header_padding_left:header_padding_left,
                header_padding_right:header_padding_right,
                header_padding_bottom:header_padding_bottom,
                header_position:header_position,
                header_style_letter:header_style_letter,
                header_top:header_top,
                header_left:header_left,
                header_right:header_right,
                header_bottom:header_bottom,
                header_image_box_height:header_image_box_height,
                header_box_image_color:header_box_image_color,
                header_box_image_background:header_box_image_background,
                header_image_border_color:header_image_border_color,
                header_image_border_style:header_image_border_style,
                header_image_view:header_image_view,
                header_image_width:header_image_width,
                header_image_height:header_image_height,
                position_img_header:position_img_header,
                align_image_header :align_image_header ,
                header_image_border_width:header_image_border_width,
                header_image_border_radius:header_image_border_radius,
                header_other_view :header_other_view ,
                header_line_view :header_line_view ,
                header_line_border_color:header_line_border_color,
                header_line_border_style:header_line_border_style,
                header_line_border_width:header_line_border_width,
                header_line_radius:header_line_radius,
                header_line_width:header_line_width,
                header_line_radius:header_line_radius,
                header_line_height:header_line_height,
                header_line_radius:header_line_radius,
                header_line_color:header_line_color,
                page_number_view:page_number_view,
                header_line_margin_top:header_line_margin_top,
                header_other_border_color:header_other_border_color,
                header_other_border_style:header_other_border_style,
                header_other_border_width:header_other_border_width,
                header_other_border_radius:header_other_border_radius,
                header_other_width:header_other_width,
                repeat_content_top:repeat_content_top,
                other_background_header:other_background_header,
                align_other_header:align_other_header,
                header_address_align:header_address_align,
                header_address_font_size:header_address_font_size,
                header_address_width:header_address_width,
                header_address_letter:header_address_letter,
                header_address_border_width:header_address_border_width,
                header_address_border_style:header_address_border_style,
                header_address_border_color:header_address_border_color,
                header_address_position:header_address_position,
                header_address_top:header_address_top,
                header_address_left:header_address_left,
                header_address_right:header_address_right,
                header_address_bottom:header_address_bottom,
                header_address_padding_top:header_address_padding_top,
                header_address_padding_left:header_address_padding_left,
                header_address_padding_right:header_address_padding_right,
                header_address_padding_bottom:header_address_padding_bottom,
                header_tax_align:header_tax_align,
                header_tax_font_size:header_tax_font_size,
                header_tax_width:header_tax_width,
                header_tax_letter:header_tax_letter,
                header_tax_border_width:header_tax_border_width,
                header_tax_border_style:header_tax_border_style,
                header_tax_border_color:header_tax_border_color,
                header_tax_position:header_tax_position,
                header_tax_top:header_tax_top,
                header_tax_left:header_tax_left,
                header_tax_right:header_tax_right,
                header_tax_bottom:header_tax_bottom,
                header_tax_padding_top:header_tax_padding_top,
                header_tax_padding_left:header_tax_padding_left,
                header_tax_padding_right:header_tax_padding_right,
                header_tax_padding_bottom:header_tax_padding_bottom,
                header_bill_align:header_bill_align,
                header_bill_font_size:header_bill_font_size,
                header_bill_width:header_bill_width,
                header_bill_letter:header_bill_letter,
                header_bill_border_width:header_bill_border_width,
                header_bill_border_style:header_bill_border_style,
                header_bill_border_color:header_bill_border_color,
                header_bill_position:header_bill_position,
                header_bill_top:header_bill_top,
                header_bill_left:header_bill_left,
                header_bill_right:header_bill_right,
                header_bill_bottom:header_bill_bottom,
                header_bill_padding_top:header_bill_padding_top,
                header_bill_padding_left:header_bill_padding_left,
                header_bill_padding_right:header_bill_padding_right,
                header_bill_padding_bottom:header_bill_padding_bottom,
                header_other_position:header_other_position,
                header_other_top:header_other_top,
                header_other_left:header_other_left,
                header_other_right:header_other_right,
                header_other_bottom:header_other_bottom,
                header_box_width:header_box_width,
                header_box_border_radius:header_box_border_radius,
                header_box_border_width:header_box_border_width,
                header_box_border_style:header_box_border_style,
                header_box_border_color:header_box_border_color,
                header_box_background:header_box_background,
                header_image_box_width:header_image_box_width,
                header_image_box_margin:header_image_box_margin,
                header_image_box_border_radius:header_image_box_border_radius,
                header_image_box_border_width:header_image_box_border_width,
                header_image_box_border_style:header_image_box_border_style,
                header_image_box_border_color:header_image_box_border_color,
                header_image_box_background:header_image_box_background,
                header_table_width:header_table_width,
                header_table_color:header_table_color,
                header_table_radius:header_table_radius,
                invoice_no :invoice_no ,
                project_no :project_no ,
                customer_no:customer_no,
                date_name :date_name ,
                address_name :address_name ,
                mobile_name :mobile_name ,
                tax_name:tax_name,
            },
            success: function(result) {
         
                $(".title-header-setting")
                    .html(result);
            },

        })
    }
    function load_footer(footer_left_layout=null,footer_center_top_layout=null,footer_center_middle_layout=null,footer_center_last_layout=null){
        if((footer_left_layout != null && footer_left_layout != "") || (footer_center_top_layout != null  && footer_center_top_layout != "") ||
        (footer_center_middle_layout != null && footer_center_middle_layout != "")  || (footer_center_last_layout != null && footer_center_last_layout != "") 
        ){return load_content_footer(footer_left_layout,footer_center_top_layout,footer_center_middle_layout,footer_center_last_layout)}
        left_footer                      = null;
        footer_font_size                 = $("#footer_font_size").val();
        footer_text_align                = $("#align_text_footer").val();
        footer_width                     = $("#footer_width").val();
        footer_view                      = ($("#footer_view").is(':checked'))?true:false;
        footer_style                     = $("#style_footer").val();
        footer_weight                    = $("#footer_font_weight").val();
        footer_border_width              = $("#footer_border_width").val();
        footer_border_style              = $("#footer_border_style").val();
        footer_border_color              = $("#footer_border_color").val();
        footer_padding_top               = $("#footer_padding_top").val();
        footer_padding_left              = $("#footer_padding_left").val();
        footer_padding_right             = $("#footer_padding_right").val();
        footer_padding_bottom            = $("#footer_padding_bottom").val();
        footer_position                  = $("#footer_position").val();
        footer_style_letter              = $("#footer_style_letter").val();
        footer_top                       = $("#footer_top").val();
        footer_left                      = $("#footer_left").val();
        footer_right                     = $("#footer_right").val();
        footer_bottom                    = $("#footer_bottom").val();
        footer_image_box_height          = $("#footer_image_box_height").val();
        footer_box_image_color           = $("#footer_box_image_color").val();
        footer_box_image_background      = $("#footer_box_image_background").val();
        footer_image_border_color        = $("#footer_image_border_color").val();
        footer_image_border_style        = $("#footer_image_border_style").val();
        footer_image_view                = ($("#footer_image_view").is(':checked'))?true:false;
        footer_image_width               = $("#footer_image_width").val();
        footer_image_height              = $("#footer_image_height").val();
        footer_image_border_width        = $("#footer_image_border_width").val();
        footer_image_border_radius       = $("#footer_image_border_radius").val();
        position_img_footer              = $("#position_img_footer").val();
        align_image_footer               = $("#align_image_footer").val(); 
        page_number_view                 = ($("#page_number_view").is(':checked'))?"true":false; 
        footer_line_view                 = ($("#footer_line_view").is(':checked'))?true:false; 
        footer_other_view                = ($("#footer_other_view").is(':checked'))?true:false;
        footer_line_border_color         = $("#footer_line_border_color").val(); 
        footer_line_border_style         = $("#footer_line_border_style").val(); 
        footer_line_border_width         = $("#footer_line_border_width").val(); 
        footer_line_radius               = $("#footer_line_radius").val(); 
        footer_line_width                = $("#footer_line_width").val(); 
        footer_line_radius               = $("#footer_line_radius").val(); 
        footer_line_height               = $("#footer_line_height").val(); 
        footer_line_radius               = $("#footer_line_radius").val(); 
        footer_line_color                = $("#footer_line_color").val(); 
        footer_line_margin_top           = $("#footer_line_margin_top").val(); 
        footer_line_margin_bottom        = $("#footer_line_margin_bottom").val(); 
        footer_other_border_color        = $("#footer_other_border_color").val(); 
        footer_other_border_style        = $("#footer_other_border_style").val(); 
        footer_other_border_width        = $("#footer_other_border_width").val(); 
        footer_other_border_radius       = $("#footer_other_border_radius").val(); 
        footer_other_width               = $("#footer_other_width").val(); 
        other_background_footer          = $("#other_background_footer").val(); 
        align_other_footer               = $("#align_other_footer").val(); 
        footer_address_align             = $("#footer_address_align").val();
        footer_address_font_size         = $("#footer_address_font_size").val();
        footer_address_width             = $("#footer_address_width").val();
        footer_address_letter            = $("#footer_address_letter").val();
        footer_address_border_width      = $("#footer_address_border_width").val();
        footer_address_border_style      = $("#footer_address_border_style").val();
        footer_address_border_color      = $("#footer_address_border_color").val();
        footer_address_position          = $("#footer_address_position").val();
        footer_address_top               = $("#footer_address_top").val();
        footer_address_left              = $("#footer_address_left").val();
        footer_address_right             = $("#footer_address_right").val();
        footer_address_bottom            = $("#footer_address_bottom").val();
        footer_address_padding_top       = $("#footer_address_padding_top").val();
        footer_address_padding_left      = $("#footer_address_padding_left").val();
        footer_address_padding_right     = $("#footer_address_padding_right").val();
        footer_address_padding_bottom    = $("#footer_address_padding_bottom").val();
        footer_tax_align                 = $("#footer_tax_align").val();
        footer_tax_font_size             = $("#footer_tax_font_size").val();
        footer_tax_width                 = $("#footer_tax_width").val();
        footer_tax_letter                = $("#footer_tax_letter").val();
        footer_tax_border_width          = $("#footer_tax_border_width").val();
        footer_tax_border_style          = $("#footer_tax_border_style").val();
        footer_tax_border_color          = $("#footer_tax_border_color").val();
        footer_tax_position              = $("#footer_tax_position").val();
        footer_tax_top                   = $("#footer_tax_top").val();
        footer_tax_left                  = $("#footer_tax_left").val();
        footer_tax_right                 = $("#footer_tax_right").val();
        footer_tax_bottom                = $("#footer_tax_bottom").val();
        footer_tax_padding_top           = $("#footer_tax_padding_top").val();
        footer_tax_padding_left          = $("#footer_tax_padding_left").val();
        footer_tax_padding_right         = $("#footer_tax_padding_right").val();
        footer_tax_padding_bottom        = $("#footer_tax_padding_bottom").val();
        footer_bill_align                = $("#footer_bill_align").val();
        footer_bill_font_size            = $("#footer_bill_font_size").val();
        footer_bill_width                = $("#footer_bill_width").val();
        footer_bill_letter               = $("#footer_bill_letter").val();
        footer_bill_border_width         = $("#footer_bill_border_width").val();
        footer_bill_border_style         = $("#footer_bill_border_style").val();
        footer_bill_border_color         = $("#footer_bill_border_color").val();
        footer_bill_position             = $("#footer_bill_position").val();
        footer_bill_top                  = $("#footer_bill_top").val();
        footer_bill_left                 = $("#footer_bill_left").val();
        footer_bill_right                = $("#footer_bill_right").val();
        footer_bill_bottom               = $("#footer_bill_bottom").val();
        footer_bill_padding_top          = $("#footer_bill_padding_top").val();
        footer_bill_padding_left         = $("#footer_bill_padding_left").val();
        footer_bill_padding_right        = $("#footer_bill_padding_right").val();
        footer_bill_padding_bottom       = $("#footer_bill_padding_bottom").val();
        footer_other_position            = $("#footer_other_position").val();
        footer_other_top                 = $("#footer_other_top").val();
        footer_other_left                = $("#footer_other_left").val();
        footer_other_right               = $("#footer_other_right").val();
        footer_other_bottom              = $("#footer_other_bottom").val();
        footer_box_width                 = $("#footer_box_width").val();
        footer_box_border_radius         = $("#footer_box_border_radius").val();
        footer_box_border_width          = $("#footer_box_border_width").val();
        footer_box_border_style          = $("#footer_box_border_style").val();
        footer_box_border_color          = $("#footer_box_border_color").val();
        footer_box_background            = $("#footer_box_background").val();
        footer_image_box_width           = $("#footer_image_box_width").val();
        footer_image_box_margin          = $("#footer_image_box_margin").val();
        footer_image_box_border_radius   = $("#footer_image_box_border_radius").val();
        footer_image_box_border_width    = $("#footer_image_box_border_width").val();
        footer_image_box_border_style    = $("#footer_image_box_border_style").val();
        footer_image_box_border_color    = $("#footer_image_box_border_color").val();
        position_box_footer_align        = $("#position_box_footer_align").val();
        footer_image_box_background      = $("#footer_image_box_background").val();
        footer_table_width               = $("#footer_table_width").val();
        footer_table_color               = $("#footer_table_color").val();
        footer_table_radius              = $("#footer_table_radius").val();
         

        
        $.ajax({
            url:"/printer/footer/style",
            dataType:"html",
            method:"GET",
            data:{
                footer_font_size:footer_font_size,
                footer_width:footer_width,
                footer_text_align:footer_text_align,
                footer_view:footer_view,
                footer_style:footer_style,
                footer_weight:footer_weight,
                footer_border_width:footer_border_width,
                footer_border_style:footer_border_style,
                footer_border_color:footer_border_color,
                footer_padding_top:footer_padding_top,
                footer_padding_left:footer_padding_left,
                footer_padding_right:footer_padding_right,
                footer_padding_bottom:footer_padding_bottom,
                footer_position:footer_position,
                footer_style_letter:footer_style_letter,
                footer_top:footer_top,
                footer_left:footer_left,
                footer_right:footer_right,
                footer_bottom:footer_bottom,
                footer_image_box_height:footer_image_box_height,
                footer_box_image_color:footer_box_image_color,
                footer_box_image_background:footer_box_image_background,
                footer_image_border_color:footer_image_border_color,
                footer_image_border_style:footer_image_border_style,
                footer_image_view:footer_image_view,
                footer_image_width:footer_image_width,
                footer_image_height:footer_image_height,
                position_img_footer:position_img_footer,
                align_image_footer :align_image_footer ,
                footer_image_border_width:footer_image_border_width,
                footer_image_border_radius:footer_image_border_radius,
                footer_other_view :footer_other_view ,
                footer_line_view :footer_line_view ,
                page_number_view :page_number_view ,
                footer_line_border_color:footer_line_border_color,
                footer_line_border_style:footer_line_border_style,
                footer_line_border_width:footer_line_border_width,
                footer_line_radius:footer_line_radius,
                footer_line_width:footer_line_width,
                footer_line_radius:footer_line_radius,
                footer_line_height:footer_line_height,
                footer_line_radius:footer_line_radius,
                footer_line_color:footer_line_color,
                footer_line_margin_top:footer_line_margin_top,
                footer_line_margin_bottom:footer_line_margin_bottom,
                footer_other_border_color:footer_other_border_color,
                footer_other_border_style:footer_other_border_style,
                footer_other_border_width:footer_other_border_width,
                footer_other_border_radius:footer_other_border_radius,
                footer_other_width:footer_other_width,
                other_background_footer:other_background_footer,
                align_other_footer:align_other_footer,
                footer_address_align:footer_address_align,
                footer_address_font_size:footer_address_font_size,
                footer_address_width:footer_address_width,
                footer_address_letter:footer_address_letter,
                footer_address_border_width:footer_address_border_width,
                footer_address_border_style:footer_address_border_style,
                footer_address_border_color:footer_address_border_color,
                footer_address_position:footer_address_position,
                footer_address_top:footer_address_top,
                footer_address_left:footer_address_left,
                footer_address_right:footer_address_right,
                footer_address_bottom:footer_address_bottom,
                footer_address_padding_top:footer_address_padding_top,
                footer_address_padding_left:footer_address_padding_left,
                footer_address_padding_right:footer_address_padding_right,
                footer_address_padding_bottom:footer_address_padding_bottom,
                footer_tax_align:footer_tax_align,
                footer_tax_font_size:footer_tax_font_size,
                footer_tax_width:footer_tax_width,
                footer_tax_letter:footer_tax_letter,
                footer_tax_border_width:footer_tax_border_width,
                footer_tax_border_style:footer_tax_border_style,
                footer_tax_border_color:footer_tax_border_color,
                footer_tax_position:footer_tax_position,
                footer_tax_top:footer_tax_top,
                footer_tax_left:footer_tax_left,
                footer_tax_right:footer_tax_right,
                footer_tax_bottom:footer_tax_bottom,
                footer_tax_padding_top:footer_tax_padding_top,
                footer_tax_padding_left:footer_tax_padding_left,
                footer_tax_padding_right:footer_tax_padding_right,
                footer_tax_padding_bottom:footer_tax_padding_bottom,
                footer_bill_align:footer_bill_align,
                footer_bill_font_size:footer_bill_font_size,
                footer_bill_width:footer_bill_width,
                footer_bill_letter:footer_bill_letter,
                footer_bill_border_width:footer_bill_border_width,
                footer_bill_border_style:footer_bill_border_style,
                footer_bill_border_color:footer_bill_border_color,
                footer_bill_position:footer_bill_position,
                footer_bill_top:footer_bill_top,
                footer_bill_left:footer_bill_left,
                footer_bill_right:footer_bill_right,
                footer_bill_bottom:footer_bill_bottom,
                footer_bill_padding_top:footer_bill_padding_top,
                footer_bill_padding_left:footer_bill_padding_left,
                footer_bill_padding_right:footer_bill_padding_right,
                footer_bill_padding_bottom:footer_bill_padding_bottom,
                footer_other_position:footer_other_position,
                footer_other_top:footer_other_top,
                footer_other_left:footer_other_left,
                footer_other_right:footer_other_right,
                footer_other_bottom:footer_other_bottom,
                footer_box_width:footer_box_width,
                footer_box_border_radius:footer_box_border_radius,
                footer_box_border_width:footer_box_border_width,
                footer_box_border_style:footer_box_border_style,
                footer_box_border_color:footer_box_border_color,
                footer_box_background:footer_box_background,
                footer_image_box_width:footer_image_box_width,
                footer_image_box_margin:footer_image_box_margin,
                footer_image_box_border_radius:footer_image_box_border_radius,
                footer_image_box_border_width:footer_image_box_border_width,
                footer_image_box_border_style:footer_image_box_border_style,
                footer_image_box_border_color:footer_image_box_border_color,
                footer_image_box_background:footer_image_box_background,
                footer_table_width:footer_table_width,
                footer_table_color:footer_table_color,
                footer_table_radius:footer_table_radius,
            },
            success: function(result) {
         
                $(".title-footer-setting")
                    .html(result);
            },

        })
    }
    function load_body(body_left_top_layout=null,body_right_top_layout=null,body_bottom_layout=null){
        if( (body_left_top_layout != null && body_left_top_layout != "") || (body_right_top_layout != null && body_right_top_layout != "")||
        (body_bottom_layout != null && body_bottom_layout != "")  ){return load_content_body(body_left_top_layout,body_right_top_layout,body_bottom_layout)}
        // @if(isset($edit_type)) 
        //   edit_type                      = {{$PrinterTemplate->id}};
        // @else
        //   edit_type                      = null;
        // @endif
        tinyMCE.triggerSave();
        top_table_section = ($("#top_table_section").is(':checked'))?true:false;
        top_table_td_border_width = $("#top_table_td_border_width").val();
        top_table_margin_bottom = $("#top_table_margin_bottom").val();
        top_table_width = $("#top_table_width").val();
        top_table_td_border_color = $("#top_table_td_border_color").val();
        top_table_td_border_style = $("#top_table_td_border_style").val();
        left_top_table_font_size = $("#left_top_table_font_size").val();
        left_top_table_text_align = $("#left_top_table_text_align").val();
        left_top_table_width = $("#left_top_table_width").val();
        right_top_table_font_size = $("#right_top_table_font_size").val();
        right_top_table_text_align = $("#right_top_table_text_align").val();
        right_top_table_width = $("#right_top_table_width").val();
        top_table_border_color = $("#top_table_border_color").val();
        top_table_border_style = $("#top_table_border_style").val();
        top_table_border_width = $("#top_table_border_width").val();
        content_table_section = ($("#content_table_section").is(':checked'))?true:false;
        page_number_view = ($("#page_number_view").is(':checked'))?"true":false;
        content_table_width = $("#content_table_width").val();
        content_width = $("#content_width").val();
        content_table_border_radius = $("#content_table_border_radius").val();
        footer_table = $("#footer_table").val();
        content_table_th_border_color = $("#content_table_th_border_color").val();
        content_table_th_padding = $("#content_table_th_padding").val();
        content_table_th_border_style = $("#content_table_th_border_style").val();
        content_table_th_text_align = $("#content_table_th_text_align").val();
        content_table_th_border_width = $("#content_table_th_border_width").val();
        content_table_th_font_size = $("#content_table_th_font_size").val();
        content_table_td_border_color = $("#content_table_td_border_color").val();
        content_table_td_padding = $("#content_table_td_padding").val();
        content_table_td_border_style = $("#content_table_td_border_style").val();
        content_table_td_text_align = $("#content_table_td_text_align").val();
        content_table_td_border_width = $("#content_table_td_border_width").val();
        content_table_td_font_size = $("#content_table_td_font_size").val();
        content_table_td_no_text_align = $("#content_table_td_no_text_align").val();
        content_table_td_no_font_size = $("#content_table_td_no_font_size").val();
        content_table_font_weight_no = $("#content_table_font_weight_no").val();
        table_th_no = $("#table_th_no").val();
        content_table_width_no = $("#content_table_width_no").val();
        content_table_text_align_name = $("#content_table_text_align_name").val();
        content_table_font_size_name = $("#content_table_font_size_name").val();
        content_table_font_weight_name = $("#content_table_font_weight_name").val();
        table_th_name = $("#table_th_name").val();
        content_table_width_name = $("#content_table_width_name").val();
        content_table_td_qty_text_align = $("#content_table_td_qty_text_align").val();
        content_table_td_qty_font_size = $("#content_table_td_qty_font_size").val();
        content_table_font_weight_qty = $("#content_table_font_weight_qty").val();
        content_table_width_qty = $("#content_table_width_qty").val();
        table_th_qty = $("#table_th_qty").val();
        
        content_table_td_code_text_align = $("#content_table_td_code_text_align").val();
        content_table_td_code_font_size = $("#content_table_td_code_font_size").val();
        content_table_font_weight_code = $("#content_table_font_weight_code").val();
        content_table_width_code = $("#content_table_width_code").val();
        table_th_code = $("#table_th_code").val();
        
        content_table_td_img_text_align = $("#content_table_td_img_text_align").val();
        content_table_td_img_font_size = $("#content_table_td_img_font_size").val();
        content_table_font_weight_img = $("#content_table_font_weight_img").val();
        content_table_width_img = $("#content_table_width_img").val();
        table_th_img = $("#table_th_img").val();

        content_table_td_discount_text_align = $("#content_table_td_discount_text_align").val();
        content_table_td_discount_font_size = $("#content_table_td_discount_font_size").val();
        content_table_font_weight_discount = $("#content_table_font_weight_discount").val();
        content_table_width_discount = $("#content_table_width_discount").val();
        table_th_discount = $("#table_th_discount").val();
        content_table_td_subtotal_text_align = $("#content_table_td_subtotal_text_align").val();
        content_table_td_subtotal_font_size = $("#content_table_td_subtotal_font_size").val();
        content_table_font_weight_subtotal = $("#content_table_font_weight_subtotal").val();
        content_table_width_subtotal = $("#content_table_width_subtotal").val();
        table_th_subtotal = $("#table_th_subtotal").val();
        content_table_td_price_text_align = $("#content_table_td_price_text_align").val();
        content_table_td_price_font_size = $("#content_table_td_price_font_size").val();
        content_table_font_weight_price = $("#content_table_font_weight_price").val();
        content_table_width_price = $("#content_table_width_price").val();
        table_th_price = $("#table_th_price").val();
        content_table_td_price_bdi_text_align = $("#content_table_td_price_bdi_text_align").val();
        content_table_td_price_bdi_font_size = $("#content_table_td_price_bdi_font_size").val();
        content_table_font_weight_price_bdi = $("#content_table_font_weight_price_bdi").val();
        content_table_width_price_bdi = $("#content_table_width_price_bdi").val();
        table_th_price_bdi = $("#table_th_price_bdi").val();
        content_table_td_price_ade_text_align = $("#content_table_td_price_ade_text_align").val();
        content_table_td_price_ade_font_size = $("#content_table_td_price_ade_font_size").val();
        content_table_font_weight_price_ade = $("#content_table_font_weight_price_ade").val();
        content_table_width_price_ade = $("#content_table_width_price_ade").val();
        table_th_price_ade = $("#table_th_price_ade").val();
        content_table_td_price_adi_text_align = $("#content_table_td_price_adi_text_align").val();
        content_table_td_price_adi_font_size = $("#content_table_td_price_adi_font_size").val();
        content_table_font_weight_price_adi = $("#content_table_font_weight_price_adi").val();
        content_table_width_price_adi = $("#content_table_width_price_adi").val();
        table_th_price_adi = $("#table_th_price_adi").val();
        line_bill_table_td_margin_left = $("#line_bill_table_td_margin_left").val();
        line_bill_table_border_color = $("#line_bill_table_border_color").val();
        line_bill_table_border_style = $("#line_bill_table_border_style").val();
        line_bill_table_border_width = $("#line_bill_table_border_width").val();
        line_bill_table_color = $("#line_bill_table_color").val();
        line_bill_table_height = $("#line_bill_table_height").val();
        line_bill_table_width = $("#line_bill_table_width").val();
        bill_table_right_td_border_color = $("#bill_table_right_td_border_color").val();
        bill_table_right_td_border_style = $("#bill_table_right_td_border_style").val();
        bill_table_right_td_padding_left = $("#bill_table_right_td_padding_left").val();
        bill_table_right_td_border_width = $("#bill_table_right_td_border_width").val();
        bill_table_right_td_text_align = $("#bill_table_right_td_text_align").val();
        bill_table_right_td_weight = $("#bill_table_right_td_weight").val();
        bill_table_right_td_font_size = $("#bill_table_right_td_font_size").val();
        bill_table_right_td_width = $("#bill_table_right_td_width").val();
        bill_table_left_td_border_color = $("#bill_table_left_td_border_color").val();
        bill_table_left_td_border_style = $("#bill_table_left_td_border_style").val();
        bill_table_left_td_padding_left = $("#bill_table_left_td_padding_left").val();
        bill_table_left_td_border_width = $("#bill_table_left_td_border_width").val();
        bill_table_left_td_text_align = $("#bill_table_left_td_text_align").val();
        bill_table_left_td_weight = $("#bill_table_left_td_weight").val();
        bill_table_left_td_font_size = $("#bill_table_left_td_font_size").val();
        bill_table_left_td_width = $("#bill_table_left_td_width").val();
        bill_table_border_color = $("#bill_table_border_color").val();
        bill_table_border_style = $("#bill_table_border_style").val();
        bill_table_border_width = $("#bill_table_border_width").val();
        bill_table_info_border_color = $("#bill_table_info_border_color").val();
        bill_table_info_border_style = $("#bill_table_info_border_style").val();
        bill_table_margin_top = $("#bill_table_margin_top").val();
        bill_table_info_border_width = $("#bill_table_info_border_width").val();
        bill_table_margin_bottom = $("#bill_table_margin_bottom").val();
        bill_table_info_width = $("#bill_table_info_width").val();
        right_bottom_table_td_bor_color = $("#right_bottom_table_td_bor_color").val();
        right_bottom_table_font_size = $("#right_bottom_table_font_size").val();
        right_bottom_table_td_bor_style = $("#right_bottom_table_td_bor_style").val();
        right_bottom_table_text_align = $("#right_bottom_table_text_align").val();
        right_bottom_table_td_bor_width = $("#right_bottom_table_td_bor_width").val();
        right_bottom_table_width = $("#right_bottom_table_width").val();
        left_bottom_table_td_bor_color = $("#left_bottom_table_td_bor_color").val();
        left_bottom_table_font_size = $("#left_bottom_table_font_size").val();
        left_bottom_table_td_bor_style = $("#left_bottom_table_td_bor_style").val();
        left_bottom_table_text_align = $("#left_bottom_table_text_align").val();
        left_bottom_table_td_bor_width = $("#left_bottom_table_td_bor_width").val();
        left_bottom_table_width = $("#left_bottom_table_width").val();
        bottom_table_section = ($("#bottom_table_section").is(':checked'))?true:false;
        bottom_table_width   = $("#bottom_table_width").val();
        bottom_table_margin_bottom  = $("#left_bottom_table_width").val();
        bottom_table_margin_top  = $("#left_bottom_table_width").val();
        bottom_table_border_width  = $("#left_bottom_table_width").val();
        bottom_table_border_style  = $("#left_bottom_table_width").val();
        bottom_table_border_color  = $("#left_bottom_table_width").val();
        bottom_table_td_border_width  = $("#left_bottom_table_width").val();
        bottom_table_td_border_style  = $("#left_bottom_table_width").val();
        bottom_table_td_border_color  = $("#left_bottom_table_width").val();

        table_th_no_named                             = $("#table_th_no_named").val();       
        table_th_code_named                           = $("#table_th_code_named").val();       
        table_th_name_named                           = $("#table_th_name_named").val();       
        table_th_img_named                            = $("#table_th_img_named").val();       
        table_th_qty_named                            = $("#table_th_qty_named").val();       
        table_th_price_named                          = $("#table_th_price_named").val();       
        table_th_price_bdi_named                      = $("#table_th_price_bdi_named").val();       
        table_th_discount_named                       = $("#table_th_discount_named").val();       
        table_th_price_ade_named                      = $("#table_th_price_ade_named").val();       
        table_th_price_adi_named                      = $("#table_th_price_adi_named").val();       
        table_th_subtotal_named                       = $("#table_th_subtotal_named").val();  

        left_invoice_info                             = $("#left_invoice_info").val();
        color_invoice_info                            = $("#color_invoice_info").val();
        right_invoice_info                            = $("#right_invoice_info").val();
        padding_invoice_info                          = $("#padding_invoice_info").val();
        background_color_invoice_info                 = $("#background_color_invoice_info").val();
        class_width_left_right                        = $("#class_width_left_right").val();
        class_width_right_right                       = $("#class_width_right_right").val();
        bold_right_invoice_info                       = $("#bold_right_invoice_info").val();
        bold_right_invoice_info_br_width              = $("#bold_right_invoice_info_br_width").val();
        bold_right_invoice_info_br_style              = $("#bold_right_invoice_info_br_style").val();
        bold_right_invoice_info_br_color              = $("#bold_right_invoice_info_br_color").val();
        bold_right_invoice_info_text_align            = $("#bold_right_invoice_info_text_align").val();
       
        bold_left_invoice_info_customer_number        = ($("#bold_left_invoice_info_customer_number").is(':checked'))?true:false;
        bold_left_invoice_info_customer_address       = ($("#bold_left_invoice_info_customer_address").is(':checked'))?true:false;
        bold_left_invoice_info_customer_mobile        = ($("#bold_left_invoice_info_customer_mobile").is(':checked'))?true:false;
        bold_left_invoice_info_customer_tax           = ($("#bold_left_invoice_info_customer_tax").is(':checked'))?true:false;
        bold_left_invoice_info_number                 = ($("#bold_left_invoice_info_number").is(':checked'))?true:false;
        bold_left_invoice_info_project                = ($("#bold_left_invoice_info_project").is(':checked'))?true:false;
        bold_left_invoice_info_date                   = ($("#bold_left_invoice_info_date").is(':checked'))?true:false;
        currency_in_row                               = ($("#currency_in_row").is(':checked'))?true:false;
        repeat_content_top                            = ($("#repeat_content_top").is(':checked'))?true:false;
        if_discount_zero                              = ($("#if_discount_zero").is(':checked'))?true:false;
        bill_invoice_info_down_vat                    = ($("#bill_invoice_info_down_vat").is(':checked'))?true:false;
        bill_invoice_info_down_subtotal               = ($("#bill_invoice_info_down_subtotal").is(':checked'))?true:false;
        bill_invoice_info_down_discount               = ($("#bill_invoice_info_down_discount").is(':checked'))?true:false;
        bill_invoice_info_down_subtotal_after_dis     = ($("#bill_invoice_info_down_subtotal_after_dis").is(':checked'))?true:false;
        
        class_width_left                              = $("#class_width_left").val();
        class_width_right                             = $("#class_width_right").val();
                
        margin_top_page                               = $("#margin_top_page").val();
        margin_bottom_page                            = $("#margin_bottom_page").val();
        bold_left_invoice_info                        = $("#bold_left_invoice_info").val();
        bold_left_invoice_info_br_width               = $("#bold_left_invoice_info_br_width").val();
        bold_left_invoice_info_br_style               = $("#bold_left_invoice_info_br_style").val();
        bold_left_invoice_info_br_color               = $("#bold_left_invoice_info_br_color").val();
        bold_left_invoice_info_text_align             = $("#bold_left_invoice_info_text_align").val();
                
        invoice_no                                    = $("#invoice_no").val();
        project_no                                    = $("#project_no").val();
        customer_no                                   = $("#customer_no").val();
        date_name                                     = $("#date_name").val();
        address_name                                  = $("#address_name").val();
        mobile_name                                   = $("#mobile_name").val();
        tax_name                                      = $("#tax_name").val();
        show_quotation_terms                          = ($("#show_quotation_terms").is(':checked'))?true:false;
        show_customer_signature                       = ($("#show_customer_signature").is(':checked'))?true:false;
        $.ajax({
            url:"/printer/body/style",
            dataType:"html",
            method:"GET",
            data:{
                top_table_section:top_table_section,
                top_table_td_border_width:top_table_td_border_width,
                top_table_margin_bottom:top_table_margin_bottom,
                top_table_width:top_table_width,
                top_table_td_border_color:top_table_td_border_color,
                top_table_td_border_style:top_table_td_border_style,
                left_top_table_font_size:left_top_table_font_size,
                left_top_table_text_align:left_top_table_text_align,
                left_top_table_width:left_top_table_width,
                right_top_table_font_size:right_top_table_font_size,
                right_top_table_text_align:right_top_table_text_align,
                right_top_table_width:right_top_table_width,
                top_table_border_color:top_table_border_color,
                top_table_border_style:top_table_border_style,
                top_table_border_width:top_table_border_width,
                content_table_section:content_table_section,
                content_table_width:content_table_width,
                content_width:content_width,
                content_table_border_radius:content_table_border_radius,
                footer_table:footer_table,
                content_table_th_border_color:content_table_th_border_color,
                content_table_th_padding:content_table_th_padding,
                content_table_th_border_style:content_table_th_border_style,
                content_table_th_text_align:content_table_th_text_align,
                content_table_th_border_width:content_table_th_border_width,
                content_table_th_font_size:content_table_th_font_size,
                content_table_td_border_color:content_table_td_border_color,
                content_table_td_padding:content_table_td_padding,
                content_table_td_border_style:content_table_td_border_style,
                content_table_td_text_align:content_table_td_text_align,
                content_table_td_border_width:content_table_td_border_width,
                content_table_td_font_size:content_table_td_font_size,
                content_table_td_no_text_align:content_table_td_no_text_align,
                content_table_td_no_font_size:content_table_td_no_font_size,
                content_table_font_weight_no:content_table_font_weight_no,
                table_th_no:table_th_no,
                content_table_width_no:content_table_width_no,
                content_table_text_align_name:content_table_text_align_name,
                content_table_font_size_name:content_table_font_size_name,
                content_table_font_weight_name:content_table_font_weight_name,
                table_th_name:table_th_name,
                content_table_td_img_text_align:content_table_td_img_text_align,
                content_table_td_img_font_size:content_table_td_img_font_size,
                content_table_font_weight_img:content_table_font_weight_img,
                content_table_width_img:content_table_width_img,
                table_th_img:table_th_img,
                page_number_view:page_number_view,
                content_table_td_code_text_align:content_table_td_code_text_align,
                content_table_td_code_font_size:content_table_td_code_font_size,
                content_table_font_weight_code:content_table_font_weight_code,
                content_table_width_code:content_table_width_code,
                table_th_code:table_th_code,
                content_table_width_name:content_table_width_name,
                content_table_td_qty_text_align:content_table_td_qty_text_align,
                content_table_td_qty_font_size:content_table_td_qty_font_size,
                content_table_font_weight_qty:content_table_font_weight_qty,
                content_table_width_qty:content_table_width_qty,
                table_th_qty:table_th_qty,
                content_table_td_discount_text_align:content_table_td_discount_text_align,
                content_table_td_discount_font_size:content_table_td_discount_font_size,
                content_table_font_weight_discount:content_table_font_weight_discount,
                content_table_width_discount:content_table_width_discount,
                table_th_discount:table_th_discount,
                content_table_td_subtotal_text_align:content_table_td_subtotal_text_align,
                content_table_td_subtotal_font_size:content_table_td_subtotal_font_size,
                content_table_font_weight_subtotal:content_table_font_weight_subtotal,
                content_table_width_subtotal:content_table_width_subtotal,
                table_th_subtotal:table_th_subtotal,
                content_table_td_price_text_align:content_table_td_price_text_align,
                content_table_td_price_font_size:content_table_td_price_font_size,
                content_table_font_weight_price:content_table_font_weight_price,
                content_table_width_price:content_table_width_price,
                table_th_price:table_th_price,
                content_table_td_price_bdi_text_align:content_table_td_price_bdi_text_align,
                content_table_td_price_bdi_font_size:content_table_td_price_bdi_font_size,
                content_table_font_weight_price_bdi:content_table_font_weight_price_bdi,
                content_table_width_price_bdi:content_table_width_price_bdi,
                table_th_price_bdi:table_th_price_bdi,
                content_table_td_price_ade_text_align:content_table_td_price_ade_text_align,
                content_table_td_price_ade_font_size:content_table_td_price_ade_font_size,
                content_table_font_weight_price_ade:content_table_font_weight_price_ade,
                content_table_width_price_ade:content_table_width_price_ade,
                table_th_price_ade:table_th_price_ade,
                content_table_td_price_adi_text_align:content_table_td_price_adi_text_align,
                content_table_td_price_adi_font_size:content_table_td_price_adi_font_size,
                content_table_font_weight_price_adi:content_table_font_weight_price_adi,
                content_table_width_price_adi:content_table_width_price_adi,
                table_th_price_adi:table_th_price_adi,
                line_bill_table_td_margin_left:line_bill_table_td_margin_left,
                line_bill_table_border_color:line_bill_table_border_color,
                line_bill_table_border_style:line_bill_table_border_style,
                line_bill_table_border_width:line_bill_table_border_width,
                line_bill_table_color:line_bill_table_color,
                line_bill_table_height:line_bill_table_height,
                line_bill_table_width:line_bill_table_width,
                bill_table_right_td_border_color:bill_table_right_td_border_color,
                bill_table_right_td_border_style:bill_table_right_td_border_style,
                bill_table_right_td_padding_left:bill_table_right_td_padding_left,
                bill_table_right_td_border_width:bill_table_right_td_border_width,
                bill_table_right_td_text_align:bill_table_right_td_text_align,
                bill_table_right_td_weight:bill_table_right_td_weight,
                bill_table_right_td_font_size:bill_table_right_td_font_size,
                bill_table_right_td_width:bill_table_right_td_width,
                bill_table_left_td_border_color:bill_table_left_td_border_color,
                bill_table_left_td_border_style:bill_table_left_td_border_style,
                bill_table_left_td_padding_left:bill_table_left_td_padding_left,
                bill_table_left_td_border_width:bill_table_left_td_border_width,
                bill_table_left_td_text_align:bill_table_left_td_text_align,
                bill_table_left_td_weight:bill_table_left_td_weight,
                bill_table_left_td_font_size:bill_table_left_td_font_size,
                bill_table_left_td_width:bill_table_left_td_width,
                bill_table_border_color:bill_table_border_color,
                bill_table_border_style:bill_table_border_style,
                bill_table_border_width:bill_table_border_width,
                bill_table_info_border_color:bill_table_info_border_color,
                bill_table_info_border_style:bill_table_info_border_style,
                bill_table_margin_top:bill_table_margin_top,
                bill_table_info_border_width:bill_table_info_border_width,
                bill_table_margin_bottom:bill_table_margin_bottom,
                bill_table_info_width:bill_table_info_width,
                right_bottom_table_td_bor_color:right_bottom_table_td_bor_color,
                right_bottom_table_font_size:right_bottom_table_font_size,
                right_bottom_table_td_bor_style:right_bottom_table_td_bor_style,
                right_bottom_table_text_align:right_bottom_table_text_align,
                right_bottom_table_td_bor_width:right_bottom_table_td_bor_width,
                right_bottom_table_width:right_bottom_table_width,
                left_bottom_table_td_bor_color:left_bottom_table_td_bor_color,
                left_bottom_table_font_size:left_bottom_table_font_size,
                left_bottom_table_td_bor_style:left_bottom_table_td_bor_style,
                left_bottom_table_text_align:left_bottom_table_text_align,
                left_bottom_table_td_bor_width:left_bottom_table_td_bor_width,
                left_bottom_table_width:left_bottom_table_width,
                bottom_table_section:bottom_table_section,
                bottom_table_width:bottom_table_width,
                bottom_table_margin_bottom:bottom_table_margin_bottom,
                bottom_table_margin_top:bottom_table_margin_top,
                bottom_table_border_width:bottom_table_border_width,
                bottom_table_border_style:bottom_table_border_style,
                bottom_table_border_color:bottom_table_border_color,
                bottom_table_td_border_width:bottom_table_td_border_width,
                bottom_table_td_border_style:bottom_table_td_border_style,
                bottom_table_td_border_color:bottom_table_td_border_color,
                table_th_no_named:table_th_no_named,
                table_th_name_named:table_th_name_named,
                table_th_code_named:table_th_code_named,
                table_th_img_named:table_th_img_named,
                table_th_qty_named:table_th_qty_named,
                table_th_price_named:table_th_price_named,
                table_th_price_bdi_named:table_th_price_bdi_named,
                table_th_discount_named:table_th_discount_named,
                table_th_price_ade_named:table_th_price_ade_named,
                table_th_price_adi_named:table_th_price_adi_named,
                table_th_subtotal_named:table_th_subtotal_named,
                left_invoice_info:left_invoice_info,
                color_invoice_info:color_invoice_info,
                right_invoice_info:right_invoice_info,
                padding_invoice_info:padding_invoice_info,
                background_color_invoice_info:background_color_invoice_info,
                class_width_left_right:class_width_left_right,
                class_width_right_right:class_width_right_right,
                bold_right_invoice_info:bold_right_invoice_info,
                bold_right_invoice_info_br_width:bold_right_invoice_info_br_width,
                bold_right_invoice_info_br_style:bold_right_invoice_info_br_style,
                bold_right_invoice_info_br_color:bold_right_invoice_info_br_color,
                bold_right_invoice_info_text_align:bold_right_invoice_info_text_align,
                bold_left_invoice_info_customer_number:bold_left_invoice_info_customer_number,
                bold_left_invoice_info_customer_address:bold_left_invoice_info_customer_address,
                bold_left_invoice_info_customer_mobile:bold_left_invoice_info_customer_mobile,
                bold_left_invoice_info_customer_tax:bold_left_invoice_info_customer_tax,
                bold_left_invoice_info_number:bold_left_invoice_info_number,
                bold_left_invoice_info_project:bold_left_invoice_info_project,
                bold_left_invoice_info_date:bold_left_invoice_info_date,
                currency_in_row:currency_in_row,
                repeat_content_top:repeat_content_top,
                if_discount_zero:if_discount_zero,
                bill_invoice_info_down_vat:bill_invoice_info_down_vat,
                bill_invoice_info_down_subtotal:bill_invoice_info_down_subtotal,
                bill_invoice_info_down_discount:bill_invoice_info_down_discount,
                bill_invoice_info_down_subtotal_after_dis:bill_invoice_info_down_subtotal_after_dis,
                class_width_left:class_width_left,
                class_width_right:class_width_right,
                bold_left_invoice_info:bold_left_invoice_info,
                bold_left_invoice_info_br_width:bold_left_invoice_info_br_width,
                bold_left_invoice_info_br_style:bold_left_invoice_info_br_style,
                bold_left_invoice_info_br_color:bold_left_invoice_info_br_color,
                bold_left_invoice_info_text_align:bold_left_invoice_info_text_align,
                margin_top_page:margin_top_page,
                margin_bottom_page:margin_bottom_page,            
                invoice_no:invoice_no,  
                project_no:project_no,  
                customer_no:customer_no, 
                date_name :date_name ,
                address_name :address_name ,
                mobile_name :mobile_name ,
                tax_name:tax_name,
                show_quotation_terms:show_quotation_terms,
                show_customer_signature:show_customer_signature
            },
            success: function(result) {
        
                $(".title-body-setting")
                    .html(result);
            },

        })
    }
    function load_content_body(body_left_top_layout=null,body_right_top_layout=null,body_bottom_layout=null){
        tinyMCE.triggerSave();
        @if(isset($edit_type)) 
          edit_type                      = {{$PrinterTemplate->id}};
        @else
          edit_type                      = null;
        @endif


        body_top_left_send_type       = (body_left_top_layout!=null)?"drop":"value";
        body_top_right_send_type      = (body_right_top_layout!=null)?"drop":"value";
        body_bottom_send_type         = (body_bottom_layout!=null)?"drop":"value";

        body_top_left                 = (body_left_top_layout!=null)?body_left_top_layout:$("#left_top_content").val();
        body_top_right                = (body_right_top_layout!=null)?body_right_top_layout:$("#right_top_content").val();
        body_bottom                   = (body_bottom_layout!=null)?body_bottom_layout:$("#bottom_content").val();

        top_table_section = ($("#top_table_section").is(':checked'))?true:false;
        top_table_td_border_width = $("#top_table_td_border_width").val();
        top_table_margin_bottom = $("#top_table_margin_bottom").val();
        top_table_width = $("#top_table_width").val();
        top_table_td_border_color = $("#top_table_td_border_color").val();
        top_table_td_border_style = $("#top_table_td_border_style").val();
        left_top_table_font_size = $("#left_top_table_font_size").val();
        left_top_table_text_align = $("#left_top_table_text_align").val();
        left_top_table_width = $("#left_top_table_width").val();
        right_top_table_font_size = $("#right_top_table_font_size").val();
        right_top_table_text_align = $("#right_top_table_text_align").val();
        right_top_table_width = $("#right_top_table_width").val();
        top_table_border_color = $("#top_table_border_color").val();
        top_table_border_style = $("#top_table_border_style").val();
        top_table_border_width = $("#top_table_border_width").val();
        content_table_section = ($("#content_table_section").is(':checked'))?true:false;
        content_table_width = $("#content_table_width").val();
        content_width = $("#content_width").val();
        content_table_border_radius = $("#content_table_border_radius").val();
        footer_table = $("#footer_table").val();
        content_table_th_border_color = $("#content_table_th_border_color").val();
        content_table_th_padding = $("#content_table_th_padding").val();
        content_table_th_border_style = $("#content_table_th_border_style").val();
        content_table_th_text_align = $("#content_table_th_text_align").val();
        content_table_th_border_width = $("#content_table_th_border_width").val();
        content_table_th_font_size = $("#content_table_th_font_size").val();
        content_table_td_border_color = $("#content_table_td_border_color").val();
        content_table_td_padding = $("#content_table_td_padding").val();
        content_table_td_border_style = $("#content_table_td_border_style").val();
        content_table_td_text_align = $("#content_table_td_text_align").val();
        content_table_td_border_width = $("#content_table_td_border_width").val();
        content_table_td_font_size = $("#content_table_td_font_size").val();
        content_table_td_no_text_align = $("#content_table_td_no_text_align").val();
        content_table_td_no_font_size = $("#content_table_td_no_font_size").val();
        content_table_font_weight_no = $("#content_table_font_weight_no").val();
        table_th_no = $("#table_th_no").val();
        content_table_width_no = $("#content_table_width_no").val();
        content_table_text_align_name = $("#content_table_text_align_name").val();
        content_table_font_size_name = $("#content_table_font_size_name").val();
        content_table_font_weight_name = $("#content_table_font_weight_name").val();
        table_th_name = $("#table_th_name").val();
        content_table_width_name = $("#content_table_width_name").val();
        content_table_td_qty_text_align = $("#content_table_td_qty_text_align").val();
        content_table_td_qty_font_size = $("#content_table_td_qty_font_size").val();
        content_table_font_weight_qty = $("#content_table_font_weight_qty").val();
        content_table_width_qty = $("#content_table_width_qty").val();
        table_th_qty = $("#table_th_qty").val();
        content_table_td_discount_text_align = $("#content_table_td_discount_text_align").val();
        content_table_td_discount_font_size = $("#content_table_td_discount_font_size").val();
        content_table_font_weight_discount = $("#content_table_font_weight_discount").val();
        content_table_width_discount = $("#content_table_width_discount").val();
        table_th_discount = $("#table_th_discount").val();
        content_table_td_subtotal_text_align = $("#content_table_td_subtotal_text_align").val();
        content_table_td_subtotal_font_size = $("#content_table_td_subtotal_font_size").val();
        content_table_font_weight_subtotal = $("#content_table_font_weight_subtotal").val();
        content_table_width_subtotal = $("#content_table_width_subtotal").val();
        table_th_subtotal = $("#table_th_subtotal").val();
       
        content_table_td_code_text_align = $("#content_table_td_code_text_align").val();
        content_table_td_code_font_size = $("#content_table_td_code_font_size").val();
        content_table_font_weight_code = $("#content_table_font_weight_code").val();
        content_table_width_code = $("#content_table_width_code").val();
        table_th_code = $("#table_th_code").val();
        
        content_table_td_img_text_align = $("#content_table_td_img_text_align").val();
        content_table_td_img_font_size = $("#content_table_td_img_font_size").val();
        content_table_font_weight_img = $("#content_table_font_weight_img").val();
        content_table_width_img = $("#content_table_width_img").val();
        table_th_img = $("#table_th_img").val();
       
        content_table_td_price_text_align = $("#content_table_td_price_text_align").val();
        content_table_td_price_font_size = $("#content_table_td_price_font_size").val();
        content_table_font_weight_price = $("#content_table_font_weight_price").val();
        content_table_width_price = $("#content_table_width_price").val();
        table_th_price = $("#table_th_price").val();
        content_table_td_price_bdi_text_align = $("#content_table_td_price_bdi_text_align").val();
        content_table_td_price_bdi_font_size = $("#content_table_td_price_bdi_font_size").val(); 
        content_table_font_weight_price_bdi = $("#content_table_font_weight_price_bdi").val();
        content_table_width_price_bdi = $("#content_table_width_price_bdi").val();
        table_th_price_bdi = $("#table_th_price_bdi").val();
        content_table_td_price_ade_text_align = $("#content_table_td_price_ade_text_align").val();
        content_table_td_price_ade_font_size = $("#content_table_td_price_ade_font_size").val();
        content_table_font_weight_price_ade = $("#content_table_font_weight_price_ade").val();
        content_table_width_price_ade = $("#content_table_width_price_ade").val();
        table_th_price_ade = $("#table_th_price_ade").val();
        content_table_td_price_adi_text_align = $("#content_table_td_price_adi_text_align").val();
        content_table_td_price_adi_font_size = $("#content_table_td_price_adi_font_size").val();
        content_table_font_weight_price_adi = $("#content_table_font_weight_price_adi").val();
        content_table_width_price_adi = $("#content_table_width_price_adi").val();
        table_th_price_adi = $("#table_th_price_adi").val();
        line_bill_table_td_margin_left = $("#line_bill_table_td_margin_left").val();
        line_bill_table_border_color = $("#line_bill_table_border_color").val();
        line_bill_table_border_style = $("#line_bill_table_border_style").val();
        line_bill_table_border_width = $("#line_bill_table_border_width").val();
        line_bill_table_color = $("#line_bill_table_color").val();
        line_bill_table_height = $("#line_bill_table_height").val();
        line_bill_table_width = $("#line_bill_table_width").val();
        bill_table_right_td_border_color = $("#bill_table_right_td_border_color").val();
        bill_table_right_td_border_style = $("#bill_table_right_td_border_style").val();
        bill_table_right_td_padding_left = $("#bill_table_right_td_padding_left").val();
        bill_table_right_td_border_width = $("#bill_table_right_td_border_width").val();
        bill_table_right_td_text_align = $("#bill_table_right_td_text_align").val();
        bill_table_right_td_weight = $("#bill_table_right_td_weight").val();
        bill_table_right_td_font_size = $("#bill_table_right_td_font_size").val();
        bill_table_right_td_width = $("#bill_table_right_td_width").val();
        bill_table_left_td_border_color = $("#bill_table_left_td_border_color").val();
        bill_table_left_td_border_style = $("#bill_table_left_td_border_style").val();
        bill_table_left_td_padding_left = $("#bill_table_left_td_padding_left").val();
        bill_table_left_td_border_width = $("#bill_table_left_td_border_width").val();
        bill_table_left_td_text_align = $("#bill_table_left_td_text_align").val();
        bill_table_left_td_weight = $("#bill_table_left_td_weight").val();
        bill_table_left_td_font_size = $("#bill_table_left_td_font_size").val();
        bill_table_left_td_width = $("#bill_table_left_td_width").val();
        bill_table_border_color = $("#bill_table_border_color").val();
        bill_table_border_style = $("#bill_table_border_style").val();
        bill_table_border_width = $("#bill_table_border_width").val();
        bill_table_info_border_color = $("#bill_table_info_border_color").val();
        bill_table_info_border_style = $("#bill_table_info_border_style").val();
        bill_table_margin_top = $("#bill_table_margin_top").val();
        bill_table_info_border_width = $("#bill_table_info_border_width").val();
        bill_table_margin_bottom = $("#bill_table_margin_bottom").val();
        bill_table_info_width = $("#bill_table_info_width").val();
        right_bottom_table_td_bor_color = $("#right_bottom_table_td_bor_color").val();
        right_bottom_table_font_size = $("#right_bottom_table_font_size").val();
        right_bottom_table_td_bor_style = $("#right_bottom_table_td_bor_style").val();
        right_bottom_table_text_align = $("#right_bottom_table_text_align").val();
        right_bottom_table_td_bor_width = $("#right_bottom_table_td_bor_width").val();
        right_bottom_table_width = $("#right_bottom_table_width").val();
        left_bottom_table_td_bor_color = $("#left_bottom_table_td_bor_color").val();
        left_bottom_table_font_size = $("#left_bottom_table_font_size").val();
        left_bottom_table_td_bor_style = $("#left_bottom_table_td_bor_style").val();
        left_bottom_table_text_align = $("#left_bottom_table_text_align").val();
        left_bottom_table_td_bor_width = $("#left_bottom_table_td_bor_width").val();
        left_bottom_table_width = $("#left_bottom_table_width").val();
        bottom_table_section = ($("#bottom_table_section").is(':checked'))?true:false;
        page_number_view = ($("#page_number_view").is(':checked'))?"true":false;
        bottom_table_width   = $("#bottom_table_width").val();
        bottom_table_margin_bottom  = $("#left_bottom_table_width").val();
        bottom_table_margin_top  = $("#left_bottom_table_width").val();
        bottom_table_border_width  = $("#left_bottom_table_width").val();
        bottom_table_border_style  = $("#left_bottom_table_width").val();
        bottom_table_border_color  = $("#left_bottom_table_width").val();
        bottom_table_td_border_width  = $("#left_bottom_table_width").val();
        bottom_table_td_border_style  = $("#left_bottom_table_width").val();
        bottom_table_td_border_color  = $("#left_bottom_table_width").val();
        
        table_th_no_named   = $("#table_th_no_named").val();       
        table_th_code_named   = $("#table_th_code_named").val();       
        table_th_name_named   = $("#table_th_name_named").val();       
        table_th_img_named   = $("#table_th_img_named").val();       
        table_th_qty_named   = $("#table_th_qty_named").val();       
        table_th_price_named   = $("#table_th_price_named").val();       
        table_th_price_bdi_named   = $("#table_th_price_bdi_named").val();       
        table_th_discount_named   = $("#table_th_discount_named").val();       
        table_th_price_ade_named   = $("#table_th_price_ade_named").val();       
        table_th_price_adi_named   = $("#table_th_price_adi_named").val();       
        table_th_subtotal_named   = $("#table_th_subtotal_named").val();       
        
        left_invoice_info                     = $("#left_invoice_info").val();
        color_invoice_info                    = $("#color_invoice_info").val();
        right_invoice_info                    = $("#right_invoice_info").val();
        padding_invoice_info                  = $("#padding_invoice_info").val();
        background_color_invoice_info         = $("#background_color_invoice_info").val();

        class_width_left_right                = $("#class_width_left_right").val();
        class_width_right_right               = $("#class_width_right_right").val();

        bold_right_invoice_info                       = $("#bold_right_invoice_info").val();
        bold_right_invoice_info_br_width              = $("#bold_right_invoice_info_br_width").val();
        bold_right_invoice_info_br_style              = $("#bold_right_invoice_info_br_style").val();
        bold_right_invoice_info_br_color              = $("#bold_right_invoice_info_br_color").val();
        bold_right_invoice_info_text_align            = $("#bold_right_invoice_info_text_align").val();

        bold_left_invoice_info_customer_number        = ($("#bold_left_invoice_info_customer_number").is(':checked'))?"on":false;
        bold_left_invoice_info_customer_address       = ($("#bold_left_invoice_info_customer_address").is(':checked'))?"on":false;
        bold_left_invoice_info_customer_mobile        = ($("#bold_left_invoice_info_customer_mobile").is(':checked'))?"on":false;
        bold_left_invoice_info_customer_tax           = ($("#bold_left_invoice_info_customer_tax").is(':checked'))?"on":false;
        bold_left_invoice_info_number                 = ($("#bold_left_invoice_info_number").is(':checked'))?"on":false;
        bold_left_invoice_info_project                = ($("#bold_left_invoice_info_project").is(':checked'))?"on":false;
        bold_left_invoice_info_date                   = ($("#bold_left_invoice_info_date").is(':checked'))?"on":false;
        currency_in_row                               = ($("#currency_in_row").is(':checked'))?"on":false;
        repeat_content_top                            = ($("#repeat_content_top").is(':checked'))?"on":false;
        if_discount_zero                              = ($("#if_discount_zero").is(':checked'))?"on":false;
        bill_invoice_info_down_vat                    = ($("#bill_invoice_info_down_vat").is(':checked'))?"on":false;
        bill_invoice_info_down_subtotal               = ($("#bill_invoice_info_down_subtotal").is(':checked'))?"on":false;
        bill_invoice_info_down_discount               = ($("#bill_invoice_info_down_discount").is(':checked'))?"on":false;
        bill_invoice_info_down_subtotal_after_dis     = ($("#bill_invoice_info_down_subtotal_after_dis").is(':checked'))?"on":false;

        class_width_left                              = $("#class_width_left").val();
        class_width_right                             = $("#class_width_right").val();

        bold_left_invoice_info                        = $("#bold_left_invoice_info").val();
        bold_left_invoice_info_br_width               = $("#bold_left_invoice_info_br_width").val();
        bold_left_invoice_info_br_style               = $("#bold_left_invoice_info_br_style").val();
        bold_left_invoice_info_br_color               = $("#bold_left_invoice_info_br_color").val();
        bold_left_invoice_info_text_align             = $("#bold_left_invoice_info_text_align").val();
        
        margin_top_page                         = $("#margin_top_page").val();
        margin_bottom_page                      = $("#margin_bottom_page").val();
        body_content_top                        = $("#body_content_top").val();
        body_content_margin_left                = $("#body_content_margin_left").val();
        body_content_margin_right               = $("#body_content_margin_right").val();
        body_content_margin_bottom              = $("#body_content_margin_bottom").val();
        
        invoice_no                              = $("#invoice_no").val();
        project_no                              = $("#project_no").val();
        customer_no                             = $("#customer_no").val();
        date_name                               = $("#date_name").val();
        address_name                            = $("#address_name").val();
        mobile_name                             = $("#mobile_name").val();
        tax_name                                = $("#tax_name").val();
        show_quotation_terms                    = ($("#show_quotation_terms").is(':checked'))?"on":false;
        show_customer_signature                 = ($("#show_customer_signature").is(':checked'))?"on":false;
        $.ajax({
            url:"/printer/body/style",
            dataType:"html",
            method:"GET",
            data:{
                edit_type:edit_type,
                top_table_section:top_table_section,
                top_table_td_border_width:top_table_td_border_width,
                top_table_margin_bottom:top_table_margin_bottom,
                top_table_width:top_table_width,
                top_table_td_border_color:top_table_td_border_color,
                top_table_td_border_style:top_table_td_border_style,
                left_top_table_font_size:left_top_table_font_size,
                left_top_table_text_align:left_top_table_text_align,
                left_top_table_width:left_top_table_width,
                right_top_table_font_size:right_top_table_font_size,
                right_top_table_text_align:right_top_table_text_align,
                right_top_table_width:right_top_table_width,
                top_table_border_color:top_table_border_color,
                top_table_border_style:top_table_border_style,
                top_table_border_width:top_table_border_width,
                content_table_section:content_table_section,
                content_table_width:content_table_width,
                content_width:content_width,
                page_number_view:page_number_view,
                content_table_border_radius:content_table_border_radius,
                footer_table:footer_table,
                content_table_th_border_color:content_table_th_border_color,
                content_table_th_padding:content_table_th_padding,
                content_table_th_border_style:content_table_th_border_style,
                content_table_th_text_align:content_table_th_text_align,
                content_table_th_border_width:content_table_th_border_width,
                content_table_th_font_size:content_table_th_font_size,
                content_table_td_border_color:content_table_td_border_color,
                content_table_td_padding:content_table_td_padding,
                content_table_td_border_style:content_table_td_border_style,
                content_table_td_text_align:content_table_td_text_align,
                content_table_td_border_width:content_table_td_border_width,
                content_table_td_font_size:content_table_td_font_size,
                content_table_td_no_text_align:content_table_td_no_text_align,
                content_table_td_no_font_size:content_table_td_no_font_size,
                content_table_font_weight_no:content_table_font_weight_no,
                table_th_no:table_th_no,
                content_table_width_no:content_table_width_no,
                content_table_text_align_name:content_table_text_align_name,
                content_table_font_size_name:content_table_font_size_name,
                content_table_font_weight_name:content_table_font_weight_name,
                table_th_name:table_th_name,
                content_table_width_name:content_table_width_name,
                content_table_td_qty_text_align:content_table_td_qty_text_align,
                content_table_td_qty_font_size:content_table_td_qty_font_size,
                content_table_font_weight_qty:content_table_font_weight_qty,
                content_table_width_qty:content_table_width_qty,
                table_th_qty:table_th_qty,
                content_table_td_img_text_align:content_table_td_img_text_align,
                content_table_td_img_font_size:content_table_td_img_font_size,
                content_table_font_weight_img:content_table_font_weight_img,
                content_table_width_img:content_table_width_img,
                table_th_img:table_th_img,
                content_table_td_code_text_align:content_table_td_code_text_align,
                content_table_td_code_font_size:content_table_td_code_font_size,
                content_table_font_weight_code:content_table_font_weight_code,
                content_table_width_code:content_table_width_code,
                table_th_code:table_th_code,
                content_table_td_discount_text_align:content_table_td_discount_text_align,
                content_table_td_discount_font_size:content_table_td_discount_font_size,
                content_table_font_weight_discount:content_table_font_weight_discount,
                content_table_width_discount:content_table_width_discount,
                table_th_discount:table_th_discount,
                content_table_td_price_text_align:content_table_td_price_text_align,
                content_table_td_price_font_size:content_table_td_price_font_size,
                content_table_font_weight_price:content_table_font_weight_price,
                content_table_width_price:content_table_width_price,
                table_th_price:table_th_price,
                content_table_td_price_bdi_text_align:content_table_td_price_bdi_text_align,
                content_table_td_price_bdi_font_size:content_table_td_price_bdi_font_size,
                content_table_font_weight_price_bdi:content_table_font_weight_price_bdi,
                content_table_width_price_bdi:content_table_width_price_bdi,
                table_th_price_bdi:table_th_price_bdi,
                content_table_td_price_ade_text_align:content_table_td_price_ade_text_align,
                content_table_td_price_ade_font_size:content_table_td_price_ade_font_size,
                content_table_font_weight_price_ade:content_table_font_weight_price_ade,
                content_table_width_price_ade:content_table_width_price_ade,
                table_th_price_ade:table_th_price_ade,
                content_table_td_price_adi_text_align:content_table_td_price_adi_text_align,
                content_table_td_price_adi_font_size:content_table_td_price_adi_font_size,
                content_table_font_weight_price_adi:content_table_font_weight_price_adi,
                content_table_width_price_adi:content_table_width_price_adi,
                table_th_price_adi:table_th_price_adi,
                content_table_td_subtotal_text_align:content_table_td_subtotal_text_align,
                content_table_td_subtotal_font_size:content_table_td_subtotal_font_size,
                content_table_font_weight_subtotal:content_table_font_weight_subtotal,
                content_table_width_subtotal:content_table_width_subtotal,
                table_th_subtotal:table_th_subtotal,
                line_bill_table_td_margin_left:line_bill_table_td_margin_left,
                line_bill_table_border_color:line_bill_table_border_color,
                line_bill_table_border_style:line_bill_table_border_style,
                line_bill_table_border_width:line_bill_table_border_width,
                line_bill_table_color:line_bill_table_color,
                line_bill_table_height:line_bill_table_height,
                line_bill_table_width:line_bill_table_width,
                bill_table_right_td_border_color:bill_table_right_td_border_color,
                bill_table_right_td_border_style:bill_table_right_td_border_style,
                bill_table_right_td_padding_left:bill_table_right_td_padding_left,
                bill_table_right_td_border_width:bill_table_right_td_border_width,
                bill_table_right_td_text_align:bill_table_right_td_text_align,
                bill_table_right_td_weight:bill_table_right_td_weight,
                bill_table_right_td_font_size:bill_table_right_td_font_size,
                bill_table_right_td_width:bill_table_right_td_width,
                bill_table_left_td_border_color:bill_table_left_td_border_color,
                bill_table_left_td_border_style:bill_table_left_td_border_style,
                bill_table_left_td_padding_left:bill_table_left_td_padding_left,
                bill_table_left_td_border_width:bill_table_left_td_border_width,
                bill_table_left_td_text_align:bill_table_left_td_text_align,
                bill_table_left_td_weight:bill_table_left_td_weight,
                bill_table_left_td_font_size:bill_table_left_td_font_size,
                bill_table_left_td_width:bill_table_left_td_width,
                bill_table_border_color:bill_table_border_color,
                bill_table_border_style:bill_table_border_style,
                bill_table_border_width:bill_table_border_width,
                bill_table_info_border_color:bill_table_info_border_color,
                bill_table_info_border_style:bill_table_info_border_style,
                bill_table_margin_top:bill_table_margin_top,
                bill_table_info_border_width:bill_table_info_border_width,
                bill_table_margin_bottom:bill_table_margin_bottom,
                bill_table_info_width:bill_table_info_width,
                right_bottom_table_td_bor_color:right_bottom_table_td_bor_color,
                right_bottom_table_font_size:right_bottom_table_font_size,
                right_bottom_table_td_bor_style:right_bottom_table_td_bor_style,
                right_bottom_table_text_align:right_bottom_table_text_align,
                right_bottom_table_td_bor_width:right_bottom_table_td_bor_width,
                right_bottom_table_width:right_bottom_table_width,
                left_bottom_table_td_bor_color:left_bottom_table_td_bor_color,
                left_bottom_table_font_size:left_bottom_table_font_size,
                left_bottom_table_td_bor_style:left_bottom_table_td_bor_style,
                left_bottom_table_text_align:left_bottom_table_text_align,
                left_bottom_table_td_bor_width:left_bottom_table_td_bor_width,
                left_bottom_table_width:left_bottom_table_width,
                bottom_table_section:bottom_table_section,
                bottom_table_width:bottom_table_width,
                bottom_table_margin_bottom:bottom_table_margin_bottom,
                bottom_table_margin_top:bottom_table_margin_top,
                bottom_table_border_width:bottom_table_border_width,
                bottom_table_border_style:bottom_table_border_style,
                bottom_table_border_color:bottom_table_border_color,
                bottom_table_td_border_width:bottom_table_td_border_width,
                bottom_table_td_border_style:bottom_table_td_border_style,
                bottom_table_td_border_color:bottom_table_td_border_color,
                body_top_left_send_type:body_top_left_send_type,
                body_top_right_send_type:body_top_right_send_type,
                body_bottom_send_type:body_bottom_send_type,
                body_top_left:body_top_left,
                body_top_right:body_top_right,
                body_bottom:body_bottom,
                table_th_no_named:table_th_no_named,
                table_th_name_named:table_th_name_named,
                table_th_code_named:table_th_code_named,
                table_th_img_named:table_th_img_named,
                table_th_qty_named:table_th_qty_named,
                table_th_price_named:table_th_price_named,
                table_th_price_bdi_named:table_th_price_bdi_named,
                table_th_discount_named:table_th_discount_named,
                table_th_price_ade_named:table_th_price_ade_named,
                table_th_price_adi_named:table_th_price_adi_named,
                table_th_subtotal_named:table_th_subtotal_named,
                left_invoice_info:left_invoice_info,
                color_invoice_info:color_invoice_info,
                right_invoice_info:right_invoice_info,
                padding_invoice_info:padding_invoice_info,
                background_color_invoice_info:background_color_invoice_info,
                class_width_left_right:class_width_left_right,
                class_width_right_right:class_width_right_right,
                bold_right_invoice_info:bold_right_invoice_info,
                bold_right_invoice_info_br_width:bold_right_invoice_info_br_width,
                bold_right_invoice_info_br_style:bold_right_invoice_info_br_style,
                bold_right_invoice_info_br_color:bold_right_invoice_info_br_color,
                bold_right_invoice_info_text_align:bold_right_invoice_info_text_align,
                bold_left_invoice_info_customer_number:bold_left_invoice_info_customer_number,
                bold_left_invoice_info_customer_address:bold_left_invoice_info_customer_address,
                bold_left_invoice_info_customer_mobile:bold_left_invoice_info_customer_mobile,
                bold_left_invoice_info_customer_tax:bold_left_invoice_info_customer_tax,
                bold_left_invoice_info_number:bold_left_invoice_info_number,
                bold_left_invoice_info_project:bold_left_invoice_info_project,
                bold_left_invoice_info_date:bold_left_invoice_info_date,
                currency_in_row:currency_in_row,
                repeat_content_top:repeat_content_top,
                if_discount_zero:if_discount_zero,
                bill_invoice_info_down_vat:bill_invoice_info_down_vat,
                bill_invoice_info_down_subtotal:bill_invoice_info_down_subtotal,
                bill_invoice_info_down_discount:bill_invoice_info_down_discount,
                bill_invoice_info_down_subtotal_after_dis:bill_invoice_info_down_subtotal_after_dis,
                class_width_left:class_width_left,
                class_width_right:class_width_right,
                bold_left_invoice_info:bold_left_invoice_info,
                bold_left_invoice_info_br_width:bold_left_invoice_info_br_width,
                bold_left_invoice_info_br_style:bold_left_invoice_info_br_style,
                bold_left_invoice_info_br_color:bold_left_invoice_info_br_color,
                bold_left_invoice_info_text_align:bold_left_invoice_info_text_align,
                body_content_top:body_content_top,
                body_content_margin_left:body_content_margin_left,
                body_content_margin_right:body_content_margin_right,
                body_content_margin_bottom:body_content_margin_bottom,
                margin_top_page:margin_top_page,
                margin_bottom_page:margin_bottom_page,
                invoice_no:invoice_no,
                project_no:project_no,
                customer_no:customer_no,
                date_name :date_name ,
                address_name :address_name ,
                mobile_name :mobile_name ,
                tax_name:tax_name,
                show_quotation_terms:show_quotation_terms,
                show_customer_signature:show_customer_signature
            },
            success: function(result) {
                $(".title-body-setting")
                .css({height:"auto"});
                $(".title-body-setting")
                    .html(result);
            },

        })
    }
    function load_content_header(left_layout=null,center_top_layout=null,center_middle_layout=null,center_last_layout=null,right_image=null){
        // left_header = ($("#left_header").is(':checked'))?true:false;
        tinyMCE.triggerSave();
        @if(isset($edit_type)) 
          edit_type                      = {{$PrinterTemplate->id}};
        @else
          edit_type                      = null;
        @endif

        left_header_send_type            = (left_layout!=null)?"drop":"value";
        center_top_header_send_type      = (center_top_layout!=null)?"drop":"value";
        center_middle_header_send_type   = (center_middle_layout!=null)?"drop":"value";
        center_last_header_send_type     = (center_last_layout!=null)?"drop":"value";

        left_header                      = (left_layout!=null)?left_layout:$("#left_header").val();
        center_top_header                = (center_top_layout!=null)?center_top_layout:$("#center_top_header").val();
        center_middle_header             = (center_middle_layout!=null)?center_middle_layout:$("#center_middle_header").val();
        center_last_header               = (center_last_layout!=null)?center_last_layout:$("#center_last_header").val();

        header_font_size                 = $("#header_font_size").val();
        header_text_align                = $("#align_text_header").val();
        header_width                     = $("#header_width").val();
        header_view                      = ($("#header_view").is(':checked'))?true:false;
        header_style                     = $("#style_header").val();
        header_weight                    = $("#header_font_weight").val();
        header_border_width              = $("#header_border_width").val();
        header_border_style              = $("#header_border_style").val();
        header_border_color              = $("#header_border_color").val();
        header_padding_top               = $("#header_padding_top").val();
        header_padding_left              = $("#header_padding_left").val();
        header_padding_right             = $("#header_padding_right").val();
        header_padding_bottom            = $("#header_padding_bottom").val();
        header_position                  = $("#header_position").val();
        header_style_letter              = $("#header_style_letter").val();
        header_top                       = $("#header_top").val();
        header_left                      = $("#header_left").val();
        header_right                     = $("#header_right").val();
        header_bottom                    = $("#header_bottom").val();
        header_image_box_height          = $("#header_image_box_height").val();
        header_box_image_color           = $("#header_box_image_color").val();
        header_box_image_background      = $("#header_box_image_background").val();
        header_image_border_color        = $("#header_image_border_color").val();
        header_image_border_style        = $("#header_image_border_style").val();
        header_image_view                = ($("#header_image_view").is(':checked'))?true:false;
        header_image_width               = $("#header_image_width").val();
        header_image_height              = $("#header_image_height").val();
        header_image_border_width        = $("#header_image_border_width").val();
        header_image_border_radius       = $("#header_image_border_radius").val();
        position_img_header              = $("#position_img_header").val();
        align_image_header               = $("#align_image_header").val(); 
        page_number_view                 = ($("#page_number_view").is(':checked'))?"true":false; 
        header_line_view                 = ($("#header_line_view").is(':checked'))?true:false; 
        header_other_view                = ($("#header_other_view").is(':checked'))?true:false;
        header_line_border_color         = $("#header_line_border_color").val(); 
        header_line_border_style         = $("#header_line_border_style").val(); 
        header_line_border_width         = $("#header_line_border_width").val(); 
        header_line_radius               = $("#header_line_radius").val(); 
        header_line_width                = $("#header_line_width").val(); 
        header_line_radius               = $("#header_line_radius").val(); 
        header_line_height               = $("#header_line_height").val(); 
        header_line_radius               = $("#header_line_radius").val(); 
        header_line_color                = $("#header_line_color").val(); 
        header_line_margin_top           = $("#header_line_margin_top").val(); 
        header_other_border_color        = $("#header_other_border_color").val(); 
        header_other_border_style        = $("#header_other_border_style").val(); 
        header_other_border_width        = $("#header_other_border_width").val(); 
        header_other_border_radius       = $("#header_other_border_radius").val(); 
        header_other_width               = $("#header_other_width").val(); 
        other_background_header          = $("#other_background_header").val(); 
        align_other_header               = $("#align_other_header").val(); 
        header_address_align             = $("#header_address_align").val();
        header_address_font_size         = $("#header_address_font_size").val();
        header_address_width             = $("#header_address_width").val();
        header_address_letter            = $("#header_address_letter").val();
        header_address_border_width      = $("#header_address_border_width").val();
        header_address_border_style      = $("#header_address_border_style").val();
        header_address_border_color      = $("#header_address_border_color").val();
        header_address_position          = $("#header_address_position").val();
        header_address_top               = $("#header_address_top").val();
        header_address_left              = $("#header_address_left").val();
        header_address_right             = $("#header_address_right").val();
        header_address_bottom            = $("#header_address_bottom").val();
        header_address_padding_top       = $("#header_address_padding_top").val();
        header_address_padding_left      = $("#header_address_padding_left").val();
        header_address_padding_right     = $("#header_address_padding_right").val();
        header_address_padding_bottom    = $("#header_address_padding_bottom").val();
        header_tax_align                 = $("#header_tax_align").val();
        header_tax_font_size             = $("#header_tax_font_size").val();
        header_tax_width                 = $("#header_tax_width").val();
        header_tax_letter                = $("#header_tax_letter").val();
        header_tax_border_width          = $("#header_tax_border_width").val();
        header_tax_border_style          = $("#header_tax_border_style").val();
        header_tax_border_color          = $("#header_tax_border_color").val();
        header_tax_position              = $("#header_tax_position").val();
        header_tax_top                   = $("#header_tax_top").val();
        repeat_content_top               = ($("#repeat_content_top").is(':checked'))?"on":false;
        header_tax_left                  = $("#header_tax_left").val();
        header_tax_right                 = $("#header_tax_right").val();
        header_tax_bottom                = $("#header_tax_bottom").val();
        header_tax_padding_top           = $("#header_tax_padding_top").val();
        header_tax_padding_left          = $("#header_tax_padding_left").val();
        header_tax_padding_right         = $("#header_tax_padding_right").val();
        header_tax_padding_bottom        = $("#header_tax_padding_bottom").val();
        header_bill_align                = $("#header_bill_align").val();
        header_bill_font_size            = $("#header_bill_font_size").val();
        header_bill_width                = $("#header_bill_width").val();
        header_bill_letter               = $("#header_bill_letter").val();
        header_bill_border_width         = $("#header_bill_border_width").val();
        header_bill_border_style         = $("#header_bill_border_style").val();
        header_bill_border_color         = $("#header_bill_border_color").val();
        header_bill_position             = $("#header_bill_position").val();
        header_bill_top                  = $("#header_bill_top").val();
        header_bill_left                 = $("#header_bill_left").val();
        header_bill_right                = $("#header_bill_right").val();
        header_bill_bottom               = $("#header_bill_bottom").val();
        header_bill_padding_top          = $("#header_bill_padding_top").val();
        header_bill_padding_left         = $("#header_bill_padding_left").val();
        header_bill_padding_right        = $("#header_bill_padding_right").val();
        header_bill_padding_bottom       = $("#header_bill_padding_bottom").val();
        header_other_position            = $("#header_other_position").val();
        header_other_top                 = $("#header_other_top").val();
        header_other_left                = $("#header_other_left").val();
        header_other_right               = $("#header_other_right").val();
        header_other_bottom              = $("#header_other_bottom").val();
        header_box_width                 = $("#header_box_width").val();
        header_box_border_radius         = $("#header_box_border_radius").val();
        header_box_border_width          = $("#header_box_border_width").val();
        header_box_border_style          = $("#header_box_border_style").val();
        header_box_border_color          = $("#header_box_border_color").val();
        header_box_background            = $("#header_box_background").val();
        header_image_box_width           = $("#header_image_box_width").val();
        header_image_box_margin          = $("#header_image_box_margin").val();
        header_image_box_border_radius   = $("#header_image_box_border_radius").val();
        header_image_box_border_width    = $("#header_image_box_border_width").val();
        header_image_box_border_style    = $("#header_image_box_border_style").val();
        header_image_box_border_color    = $("#header_image_box_border_color").val();
        position_box_header_align        = $("#position_box_header_align").val();
        header_image_box_background      = $("#header_image_box_background").val();
        header_table_width               = $("#header_table_width").val();
        header_table_color               = $("#header_table_color").val();
        header_table_radius              = $("#header_table_radius").val();
                 
        invoice_no                       = $("#invoice_no").val();
        project_no                       = $("#project_no").val();
        customer_no                      = $("#customer_no").val();
 
        date_name                        = $("#date_name").val();
        address_name                     = $("#address_name").val();
        mobile_name                      = $("#mobile_name").val();
        tax_name                         = $("#tax_name").val();

        $.ajax({
            url:"/printer/header/content",
            dataType:"html",
            method:"GET",
            data:{
                edit_type:edit_type,
                left_header_send_type:left_header_send_type,
                center_top_header_send_type:center_top_header_send_type,
                center_middle_header_send_type:center_middle_header_send_type,
                center_last_header_send_type:center_last_header_send_type,
                left_header:left_header,
                center_top_header:center_top_header,
                center_middle_header:center_middle_header,
                center_last_header:center_last_header,
                header_font_size:header_font_size,
                header_width:header_width,
                header_text_align:header_text_align,
                header_view:header_view,
                header_style:header_style,
                header_weight:header_weight,
                header_border_width:header_border_width,
                header_border_style:header_border_style,
                header_border_color:header_border_color,
                header_padding_top:header_padding_top,
                header_padding_left:header_padding_left,
                header_padding_right:header_padding_right,
                header_padding_bottom:header_padding_bottom,
                header_position:header_position,
                header_style_letter:header_style_letter,
                header_top:header_top,
                repeat_content_top:repeat_content_top,
                header_left:header_left,
                page_number_view:page_number_view,
                header_right:header_right,
                header_bottom:header_bottom,
                header_image_box_height:header_image_box_height,
                header_box_image_color:header_box_image_color,
                header_box_image_background:header_box_image_background,
                header_image_border_color:header_image_border_color,
                header_image_border_style:header_image_border_style,
                header_image_view:header_image_view,
                header_image_width:header_image_width,
                header_image_height:header_image_height,
                position_img_header:position_img_header,
                align_image_header :align_image_header ,
                header_image_border_width:header_image_border_width,
                header_image_border_radius:header_image_border_radius,
                header_other_view :header_other_view ,
                header_line_view :header_line_view ,
                header_line_border_color:header_line_border_color,
                header_line_border_style:header_line_border_style,
                header_line_border_width:header_line_border_width,
                header_line_radius:header_line_radius,
                header_line_width:header_line_width,
                header_line_radius:header_line_radius,
                header_line_height:header_line_height,
                header_line_radius:header_line_radius,
                header_line_color:header_line_color,
                header_line_margin_top:header_line_margin_top,
                header_other_border_color:header_other_border_color,
                header_other_border_style:header_other_border_style,
                header_other_border_width:header_other_border_width,
                header_other_border_radius:header_other_border_radius,
                header_other_width:header_other_width,
                other_background_header:other_background_header,
                align_other_header:align_other_header,
                header_address_align:header_address_align,
                header_address_font_size:header_address_font_size,
                header_address_width:header_address_width,
                header_address_letter:header_address_letter,
                header_address_border_width:header_address_border_width,
                header_address_border_style:header_address_border_style,
                header_address_border_color:header_address_border_color,
                header_address_position:header_address_position,
                header_address_top:header_address_top,
                header_address_left:header_address_left,
                header_address_right:header_address_right,
                header_address_bottom:header_address_bottom,
                header_address_padding_top:header_address_padding_top,
                header_address_padding_left:header_address_padding_left,
                header_address_padding_right:header_address_padding_right,
                header_address_padding_bottom:header_address_padding_bottom,
                header_tax_align:header_tax_align,
                header_tax_font_size:header_tax_font_size,
                header_tax_width:header_tax_width,
                header_tax_letter:header_tax_letter,
                header_tax_border_width:header_tax_border_width,
                header_tax_border_style:header_tax_border_style,
                header_tax_border_color:header_tax_border_color,
                header_tax_position:header_tax_position,
                header_tax_top:header_tax_top,
                header_tax_left:header_tax_left,
                header_tax_right:header_tax_right,
                header_tax_bottom:header_tax_bottom,
                header_tax_padding_top:header_tax_padding_top,
                header_tax_padding_left:header_tax_padding_left,
                header_tax_padding_right:header_tax_padding_right,
                header_tax_padding_bottom:header_tax_padding_bottom,
                header_bill_align:header_bill_align,
                header_bill_font_size:header_bill_font_size,
                header_bill_width:header_bill_width,
                header_bill_letter:header_bill_letter,
                header_bill_border_width:header_bill_border_width,
                header_bill_border_style:header_bill_border_style,
                header_bill_border_color:header_bill_border_color,
                header_bill_position:header_bill_position,
                header_bill_top:header_bill_top,
                header_bill_left:header_bill_left,
                header_bill_right:header_bill_right,
                header_bill_bottom:header_bill_bottom,
                header_bill_padding_top:header_bill_padding_top,
                header_bill_padding_left:header_bill_padding_left,
                header_bill_padding_right:header_bill_padding_right,
                header_bill_padding_bottom:header_bill_padding_bottom,
                header_other_position:header_other_position,
                header_other_top:header_other_top,
                header_other_left:header_other_left,
                header_other_right:header_other_right,
                header_other_bottom:header_other_bottom,
                header_box_width:header_box_width,
                header_box_border_radius:header_box_border_radius,
                header_box_border_width:header_box_border_width,
                header_box_border_style:header_box_border_style,
                header_box_border_color:header_box_border_color,
                header_box_background:header_box_background,
                header_image_box_width:header_image_box_width,
                header_image_box_margin:header_image_box_margin,
                header_image_box_border_radius:header_image_box_border_radius,
                header_image_box_border_width:header_image_box_border_width,
                header_image_box_border_style:header_image_box_border_style,
                header_image_box_border_color:header_image_box_border_color,
                header_image_box_background:header_image_box_background,
                header_table_width:header_table_width,
                header_table_color:header_table_color,
                header_table_radius:header_table_radius,
                invoice_no :invoice_no ,
                project_no :project_no ,
                customer_no:customer_no,
                date_name :date_name ,
                address_name :address_name ,
                mobile_name :mobile_name ,
                tax_name:tax_name,
            },
            success: function(result) {
        
                $(".title-header-setting")
                    .css({height:"auto"});
                $(".title-header-setting")
                    .html(result);
                    
            },

        })
    }
    function load_content_footer(left_footer_layout=null,center_top_footer_layout=null,center_middle_footer_layout=null,center_last_footer_layout=null,right_footer_image=null){
         // left_footer = ($("#left_footer").is(':checked'))?true:false;
        tinyMCE.triggerSave();
        @if(isset($edit_type)) 
          edit_type                      = {{$PrinterTemplate->id}};
        @else
          edit_type                      = null;
        @endif

        left_footer_send_type            = (left_footer_layout!=null && left_footer_layout!="" )?"drop":"value";
        center_top_footer_send_type      = (center_top_footer_layout!=null && center_top_footer_layout!="" )?"drop":"value";
        center_middle_footer_send_type   = (center_middle_footer_layout!=null && center_middle_footer_layout!="" )?"drop":"value";
        center_last_footer_send_type     = (center_last_footer_layout!=null && center_last_footer_layout!="" )?"drop":"value";
        
        left_footer                      = (left_footer_layout!=null && left_footer_layout!="" )?left_footer_layout:$("#left_footer").val();
        center_top_footer                = (center_top_footer_layout!=null && center_top_footer_layout!="" )?center_top_footer_layout:$("#center_top_footer").val();
        center_middle_footer             = (center_middle_footer_layout!=null && center_middle_footer_layout!="" )?center_middle_footer_layout:$("#center_middle_footer").val();
        center_last_footer               = (center_last_footer_layout!=null && center_last_footer_layout!="" )?center_last_footer_layout:$("#center_last_footer").val();

        footer_font_size                 = $("#footer_font_size").val();
        footer_text_align                = $("#align_text_footer").val();
        footer_width                     = $("#footer_width").val();
        footer_view                      = ($("#footer_view").is(':checked'))?true:false;
        footer_style                     = $("#style_footer").val();
        footer_weight                    = $("#footer_font_weight").val();
        footer_border_width              = $("#footer_border_width").val();
        footer_border_style              = $("#footer_border_style").val();
        footer_border_color              = $("#footer_border_color").val();
        footer_padding_top               = $("#footer_padding_top").val();
        footer_padding_left              = $("#footer_padding_left").val();
        footer_padding_right             = $("#footer_padding_right").val();
        footer_padding_bottom            = $("#footer_padding_bottom").val();
        footer_position                  = $("#footer_position").val();
        footer_style_letter              = $("#footer_style_letter").val();
        footer_top                       = $("#footer_top").val();
        footer_left                      = $("#footer_left").val();
        footer_right                     = $("#footer_right").val();
        footer_bottom                    = $("#footer_bottom").val();
        footer_image_box_height          = $("#footer_image_box_height").val();
        footer_box_image_color           = $("#footer_box_image_color").val();
        footer_box_image_background      = $("#footer_box_image_background").val();
        footer_image_border_color        = $("#footer_image_border_color").val();
        footer_image_border_style        = $("#footer_image_border_style").val();
        footer_image_view                = ($("#footer_image_view").is(':checked'))?true:false;
        footer_image_width               = $("#footer_image_width").val();
        footer_image_height              = $("#footer_image_height").val();
        footer_image_border_width        = $("#footer_image_border_width").val();
        footer_image_border_radius       = $("#footer_image_border_radius").val();
        position_img_footer              = $("#position_img_footer").val();
        align_image_footer               = $("#align_image_footer").val(); 
        page_number_view                 = ($("#page_number_view").is(':checked'))?true:false; 
        footer_line_view                 = ($("#footer_line_view").is(':checked'))?true:false; 
        footer_other_view                = ($("#footer_other_view").is(':checked'))?true:false;
        footer_line_border_color         = $("#footer_line_border_color").val(); 
        footer_line_border_style         = $("#footer_line_border_style").val(); 
        footer_line_border_width         = $("#footer_line_border_width").val(); 
        footer_line_radius               = $("#footer_line_radius").val(); 
        footer_line_width                = $("#footer_line_width").val(); 
        footer_line_radius               = $("#footer_line_radius").val(); 
        footer_line_height               = $("#footer_line_height").val(); 
        footer_line_radius               = $("#footer_line_radius").val(); 
        footer_line_color                = $("#footer_line_color").val(); 
        footer_line_margin_top           = $("#footer_line_margin_top").val(); 
        footer_line_margin_bottom        = $("#footer_line_margin_bottom").val(); 
        footer_other_border_color        = $("#footer_other_border_color").val(); 
        footer_other_border_style        = $("#footer_other_border_style").val(); 
        footer_other_border_width        = $("#footer_other_border_width").val(); 
        footer_other_border_radius       = $("#footer_other_border_radius").val(); 
        footer_other_width               = $("#footer_other_width").val(); 
        other_background_footer          = $("#other_background_footer").val(); 
        align_other_footer               = $("#align_other_footer").val(); 
        footer_address_align             = $("#footer_address_align").val();
        footer_address_font_size         = $("#footer_address_font_size").val();
        footer_address_width             = $("#footer_address_width").val();
        footer_address_letter            = $("#footer_address_letter").val();
        footer_address_border_width      = $("#footer_address_border_width").val();
        footer_address_border_style      = $("#footer_address_border_style").val();
        footer_address_border_color      = $("#footer_address_border_color").val();
        footer_address_position          = $("#footer_address_position").val();
        footer_address_top               = $("#footer_address_top").val();
        footer_address_left              = $("#footer_address_left").val();
        footer_address_right             = $("#footer_address_right").val();
        footer_address_bottom            = $("#footer_address_bottom").val();
        footer_address_padding_top       = $("#footer_address_padding_top").val();
        footer_address_padding_left      = $("#footer_address_padding_left").val();
        footer_address_padding_right     = $("#footer_address_padding_right").val();
        footer_address_padding_bottom    = $("#footer_address_padding_bottom").val();
        footer_tax_align                 = $("#footer_tax_align").val();
        footer_tax_font_size             = $("#footer_tax_font_size").val();
        footer_tax_width                 = $("#footer_tax_width").val();
        footer_tax_letter                = $("#footer_tax_letter").val();
        footer_tax_border_width          = $("#footer_tax_border_width").val();
        footer_tax_border_style          = $("#footer_tax_border_style").val();
        footer_tax_border_color          = $("#footer_tax_border_color").val();
        footer_tax_position              = $("#footer_tax_position").val();
        footer_tax_top                   = $("#footer_tax_top").val();
        footer_tax_left                  = $("#footer_tax_left").val();
        footer_tax_right                 = $("#footer_tax_right").val();
        footer_tax_bottom                = $("#footer_tax_bottom").val();
        footer_tax_padding_top           = $("#footer_tax_padding_top").val();
        footer_tax_padding_left          = $("#footer_tax_padding_left").val();
        footer_tax_padding_right         = $("#footer_tax_padding_right").val();
        footer_tax_padding_bottom        = $("#footer_tax_padding_bottom").val();
        footer_bill_align                = $("#footer_bill_align").val();
        footer_bill_font_size            = $("#footer_bill_font_size").val();
        footer_bill_width                = $("#footer_bill_width").val();
        footer_bill_letter               = $("#footer_bill_letter").val();
        footer_bill_border_width         = $("#footer_bill_border_width").val();
        footer_bill_border_style         = $("#footer_bill_border_style").val();
        footer_bill_border_color         = $("#footer_bill_border_color").val();
        footer_bill_position             = $("#footer_bill_position").val();
        footer_bill_top                  = $("#footer_bill_top").val();
        footer_bill_left                 = $("#footer_bill_left").val();
        footer_bill_right                = $("#footer_bill_right").val();
        footer_bill_bottom               = $("#footer_bill_bottom").val();
        footer_bill_padding_top          = $("#footer_bill_padding_top").val();
        footer_bill_padding_left         = $("#footer_bill_padding_left").val();
        footer_bill_padding_right        = $("#footer_bill_padding_right").val();
        footer_bill_padding_bottom       = $("#footer_bill_padding_bottom").val();
        footer_other_position            = $("#footer_other_position").val();
        footer_other_top                 = $("#footer_other_top").val();
        footer_other_left                = $("#footer_other_left").val();
        footer_other_right               = $("#footer_other_right").val();
        footer_other_bottom              = $("#footer_other_bottom").val();
        footer_box_width                 = $("#footer_box_width").val();
        footer_box_border_radius         = $("#footer_box_border_radius").val();
        footer_box_border_width          = $("#footer_box_border_width").val();
        footer_box_border_style          = $("#footer_box_border_style").val();
        footer_box_border_color          = $("#footer_box_border_color").val();
        footer_box_background            = $("#footer_box_background").val();
        footer_image_box_width           = $("#footer_image_box_width").val();
        footer_image_box_margin          = $("#footer_image_box_margin").val();
        footer_image_box_border_radius   = $("#footer_image_box_border_radius").val();
        footer_image_box_border_width    = $("#footer_image_box_border_width").val();
        footer_image_box_border_style    = $("#footer_image_box_border_style").val();
        footer_image_box_border_color    = $("#footer_image_box_border_color").val();
        position_box_footer_align        = $("#position_box_footer_align").val();
        footer_image_box_background      = $("#footer_image_box_background").val();
        footer_table_width               = $("#footer_table_width").val();
        footer_table_color               = $("#footer_table_color").val();
        footer_table_radius              = $("#footer_table_radius").val();
         

        
        $.ajax({
            url:"/printer/footer/style",
            dataType:"html",
            method:"GET",
            data:{
                edit_type:edit_type,
                left_footer_send_type:left_footer_send_type,
                center_top_footer_send_type:center_top_footer_send_type,
                center_middle_footer_send_type:center_middle_footer_send_type,
                center_last_footer_send_type:center_last_footer_send_type,
                left_footer:left_footer,
                center_top_footer:center_top_footer,
                center_middle_footer:center_middle_footer,
                center_last_footer:center_last_footer,
                footer_font_size:footer_font_size,
                footer_width:footer_width,
                footer_text_align:footer_text_align,
                footer_view:footer_view,
                footer_style:footer_style,
                footer_weight:footer_weight,
                footer_border_width:footer_border_width,
                footer_border_style:footer_border_style,
                footer_border_color:footer_border_color,
                footer_padding_top:footer_padding_top,
                footer_padding_left:footer_padding_left,
                footer_padding_right:footer_padding_right,
                footer_padding_bottom:footer_padding_bottom,
                footer_position:footer_position,
                footer_style_letter:footer_style_letter,
                page_number_view:page_number_view,
                footer_top:footer_top,
                footer_left:footer_left,
                footer_right:footer_right,
                footer_bottom:footer_bottom,
                footer_image_box_height:footer_image_box_height,
                footer_box_image_color:footer_box_image_color,
                footer_box_image_background:footer_box_image_background,
                footer_image_border_color:footer_image_border_color,
                footer_image_border_style:footer_image_border_style,
                footer_image_view:footer_image_view,
                footer_image_width:footer_image_width,
                footer_image_height:footer_image_height,
                position_img_footer:position_img_footer,
                align_image_footer :align_image_footer ,
                footer_image_border_width:footer_image_border_width,
                footer_image_border_radius:footer_image_border_radius,
                footer_other_view :footer_other_view ,
                footer_line_view :footer_line_view ,
                footer_line_border_color:footer_line_border_color,
                footer_line_border_style:footer_line_border_style,
                footer_line_border_width:footer_line_border_width,
                footer_line_radius:footer_line_radius,
                footer_line_width:footer_line_width,
                footer_line_radius:footer_line_radius,
                footer_line_height:footer_line_height,
                footer_line_radius:footer_line_radius,
                footer_line_color:footer_line_color,
                footer_line_margin_top:footer_line_margin_top,
                footer_line_margin_bottom:footer_line_margin_bottom,
                footer_other_border_color:footer_other_border_color,
                footer_other_border_style:footer_other_border_style,
                footer_other_border_width:footer_other_border_width,
                footer_other_border_radius:footer_other_border_radius,
                footer_other_width:footer_other_width,
                other_background_footer:other_background_footer,
                align_other_footer:align_other_footer,
                footer_address_align:footer_address_align,
                footer_address_font_size:footer_address_font_size,
                footer_address_width:footer_address_width,
                footer_address_letter:footer_address_letter,
                footer_address_border_width:footer_address_border_width,
                footer_address_border_style:footer_address_border_style,
                footer_address_border_color:footer_address_border_color,
                footer_address_position:footer_address_position,
                footer_address_top:footer_address_top,
                footer_address_left:footer_address_left,
                footer_address_right:footer_address_right,
                footer_address_bottom:footer_address_bottom,
                footer_address_padding_top:footer_address_padding_top,
                footer_address_padding_left:footer_address_padding_left,
                footer_address_padding_right:footer_address_padding_right,
                footer_address_padding_bottom:footer_address_padding_bottom,
                footer_tax_align:footer_tax_align,
                footer_tax_font_size:footer_tax_font_size,
                footer_tax_width:footer_tax_width,
                footer_tax_letter:footer_tax_letter,
                footer_tax_border_width:footer_tax_border_width,
                footer_tax_border_style:footer_tax_border_style,
                footer_tax_border_color:footer_tax_border_color,
                footer_tax_position:footer_tax_position,
                footer_tax_top:footer_tax_top,
                footer_tax_left:footer_tax_left,
                footer_tax_right:footer_tax_right,
                footer_tax_bottom:footer_tax_bottom,
                footer_tax_padding_top:footer_tax_padding_top,
                footer_tax_padding_left:footer_tax_padding_left,
                footer_tax_padding_right:footer_tax_padding_right,
                footer_tax_padding_bottom:footer_tax_padding_bottom,
                footer_bill_align:footer_bill_align,
                footer_bill_font_size:footer_bill_font_size,
                footer_bill_width:footer_bill_width,
                footer_bill_letter:footer_bill_letter,
                footer_bill_border_width:footer_bill_border_width,
                footer_bill_border_style:footer_bill_border_style,
                footer_bill_border_color:footer_bill_border_color,
                footer_bill_position:footer_bill_position,
                footer_bill_top:footer_bill_top,
                footer_bill_left:footer_bill_left,
                footer_bill_right:footer_bill_right,
                footer_bill_bottom:footer_bill_bottom,
                footer_bill_padding_top:footer_bill_padding_top,
                footer_bill_padding_left:footer_bill_padding_left,
                footer_bill_padding_right:footer_bill_padding_right,
                footer_bill_padding_bottom:footer_bill_padding_bottom,
                footer_other_position:footer_other_position,
                footer_other_top:footer_other_top,
                footer_other_left:footer_other_left,
                footer_other_right:footer_other_right,
                footer_other_bottom:footer_other_bottom,
                footer_box_width:footer_box_width,
                footer_box_border_radius:footer_box_border_radius,
                footer_box_border_width:footer_box_border_width,
                footer_box_border_style:footer_box_border_style,
                footer_box_border_color:footer_box_border_color,
                footer_box_background:footer_box_background,
                footer_image_box_width:footer_image_box_width,
                footer_image_box_margin:footer_image_box_margin,
                footer_image_box_border_radius:footer_image_box_border_radius,
                footer_image_box_border_width:footer_image_box_border_width,
                footer_image_box_border_style:footer_image_box_border_style,
                footer_image_box_border_color:footer_image_box_border_color,
                footer_image_box_background:footer_image_box_background,
                footer_table_width:footer_table_width,
                footer_table_color:footer_table_color,
                footer_table_radius:footer_table_radius,
            },
            success: function(result) {
                $(".title-footer-setting")
                .css({height:"auto"});
                $(".title-footer-setting")
                    .html(result);
            },

        })
    } 

    function init_header(){
        $("#style_header").val("table");
        $("#header_width").val("100%") ;
        $("#header_style_letter").val("capitalize");
        $("#header_table_width").val("100%") ;
        $("#header_table_color").val("transparent") ;
        $("#header_table_radius").val("0px") ;
        
        $("#align_text_header").val("left");
        $("#header_font_size").val("22px");
        $("#header_font_weight").val("300") ;
        
        $("#header_border_width").val("0px") ;
        $("#header_border_style").val("solid") ;
        $("#header_border_color").val("transparent");
        
        $("#header_padding_left").val("0px");
        $("#header_padding_top").val("0px");
        $("#header_padding_bottom").val("0px");
        $("#header_padding_right").val("0px");
        

        $("#header_position").val("relative");

        $("#header_right").val("0px");
        $("#header_left").val("0px");
        $("#header_bottom").val("0px");
        $("#header_top").val("0px");
        
        $("#position_img_header").val("right");
        $("#align_image_header").val("right");

        $("#header_image_width").val("100");
        $("#header_image_height").val("100");

        $("#header_image_border_radius").val("0px");
        $("#header_image_border_style").val("solid");
        $("#header_image_border_color").val("transparent");
        $("#header_image_border_width").val("0px");
        
        
        $("#header_box_image_color").val("transparent");
        $("#header_box_image_background").val("transparent");
        $("#header_image_box_margin").val(" auto 0%");
        $("#header_image_box_height").val("100%");
        $("#header_image_box_width").val("32.333%");
        
        $("#header_image_box_border_radius").val("0px");
        $("#header_image_box_border_color").val("transparent");
        
        $("#header_image_box_border_style").val("solid");
        $("#header_image_box_border_width").val("0px");
        $("#header_image_box_background").val("transparent");
        $("#position_box_header_align").val("center");
        
        $("#header_other_border_radius").val("0px");
        $("#header_other_width").val("32.333%");
        $("#other_background_header").val("transparent");
        $("#align_other_header").val("center");
        
        $("#header_other_border_width").val("0px");
        $("#header_other_border_style").val("solid");
        $("#header_other_border_color").val("transparent");
        
        $("#header_other_bottom").val("0px");
        $("#header_other_right").val("0px");
        $("#header_other_left").val("0px");
        $("#header_other_top").val("0px");
        $("#header_other_position").val("relative");
        
        $("#header_tax_right").val("0px");
        $("#header_tax_left").val("0px");
        $("#header_tax_bottom").val("0px");
        $("#header_tax_top").val("0px");
        $("#header_tax_position").val("relative");
        
        $("#header_tax_letter").val("capitalize");
        $("#header_tax_width").val("100%");
        $("#header_tax_font_size").val("22px");
        $("#header_tax_align").val("center");
        
        $("#header_tax_border_color").val("transparent");
        $("#header_tax_border_style").val("solid");
        $("#header_tax_border_width").val("0px");
        
        $("#header_tax_padding_bottom").val("0px");
        $("#header_tax_padding_right").val("0px");
        $("#header_tax_padding_left").val("0px");
        $("#header_tax_padding_top").val("0px");

        $("#header_address_letter").val("capitalize");
        $("#header_address_width").val("100%");
        $("#header_address_font_size").val("22px");
        $("#header_address_align").val("center");
        
        $("#header_address_border_color").val("transparent");
        $("#header_address_border_style").val("solid");
        $("#header_address_border_width").val("0px");
        
        $("#header_address_padding_bottom").val("0px");
        $("#header_address_padding_right").val("0px");
        $("#header_address_padding_left").val("0px");
        $("#header_address_padding_top").val("0px");
       
        $("#header_address_right").val("0px");
        $("#header_address_left").val("0px");
        $("#header_address_bottom").val("0px");
        $("#header_address_top").val("0px");
        $("#header_address_position").val("relative");
       
        $("#header_bill_right").val("0px");
        $("#header_bill_left").val("0px");
        $("#header_bill_bottom").val("0px");
        $("#header_bill_top").val("0px");
        $("#header_bill_position").val("relative");


        $("#header_bill_letter").val("capitalize");
        $("#header_bill_width").val("100%");
        $("#header_bill_font_size").val("22px");
        $("#header_bill_align").val("center");
        
        $("#header_bill_border_width").val("0px");
        $("#header_bill_border_style").val("solid");
        $("#header_bill_border_color").val("transparent");
        
        $("#header_bill_padding_bottom").val("0px");
        $("#header_bill_padding_right").val("0px");
        $("#header_bill_padding_left").val("0px");
        $("#header_bill_padding_top").val("0px");
        

        $("#header_line_color").val("black");
        $("#header_line_width").val("50%");
        $("#header_line_height").val("1px");
        
        $("#header_line_radius").val("0px");
        $("#header_line_margin_top").val("10px");
        
        $("#header_line_border_color").val("black");
        $("#header_line_border_style").val("solid");
        $("#header_line_border_width").val("1px");
        
        
       
        
    }
    function init_content(){

        $("#top_table_width").val("100%");
        $("#top_table_margin_bottom").val("100px");
        
        $("#top_table_td_border_width").val("0px");
        $("#top_table_td_border_style").val("solid");
        $("#top_table_td_border_color").val("transparent");
        
        $("#left_top_table_width").val("50%");
        $("#left_top_table_font_size").val("20px");
        $("#left_top_table_text_align").val("left");
        
        $("#color_invoice_info").val("black");

        $("#right_top_table_width").val("50%");
        $("#right_top_table_font_size").val("20px");
        $("#right_top_table_text_align").val("right");

        $("#top_table_border_color").val("black");
        $("#top_table_border_style").val("solid");
        $("#top_table_border_width").val("2px");
        
        $("#content_width").val("100%");
        $("#content_table_border_radius").val("0px");
        $("#content_table_width").val("100%");
        $("#footer_table").val("true");
        
        $("#content_table_th_border_color").val("transparent");
        $("#content_table_th_padding").val("0px");
        $("#content_table_th_border_style").val("solid");
        $("#content_table_th_text_align").val("left");
        $("#content_table_th_border_width").val("1px");
        $("#content_table_th_font_size").val("8px");
        
        $("#content_table_td_border_color").val("transparent");
        $("#content_table_td_padding").val("0px");
        $("#content_table_td_border_style").val("solid");
        $("#content_table_td_text_align").val("left");
        $("#content_table_td_border_width").val("1px");
        $("#content_table_td_font_size").val("8px");
        
        $("#content_table_td_no_text_align").val("left");
        $("#content_table_td_no_font_size").val("16px");
        $("#content_table_font_weight_no").val("500");
        $("#content_table_width_no").val("10%");
        $("#table_th_no").val("true");
        
        $("#content_table_text_align_name").val("left");
        $("#content_table_font_size_name").val("16px");
        $("#content_table_font_weight_name").val("500");
        $("#content_table_width_name").val("40%");
        $("#table_th_name").val("true");
        
        $("#content_table_td_qty_text_align").val("left");
        $("#content_table_td_qty_font_size").val("16px");
        $("#content_table_font_weight_qty").val("500");
        $("#content_table_width_qty").val("25%");
        $("#table_th_qty").val("true");
        
        $("#content_table_td_price_text_align").val("left");
        $("#content_table_td_price_font_size").val("16px");
        $("#content_table_font_weight_price").val("500");
        $("#content_table_width_price").val("25%");
        $("#table_th_price").val("true");
        
        $("#left_bottom_table_td_bor_color").val("black");
        $("#left_bottom_table_font_size").val("20px");
        $("#left_bottom_table_td_bor_style").val("solid");
        $("#left_bottom_table_text_align").val("left");
        $("#left_bottom_table_td_bor_width").val("1px");
        $("#left_bottom_table_width").val("50%");

        $("#right_bottom_table_td_bor_color").val("black");
        $("#right_bottom_table_font_size").val("20px");
        $("#right_bottom_table_td_bor_style").val("solid");
        $("#right_bottom_table_text_align").val("left");
        $("#right_bottom_table_td_bor_width").val("1px");
        $("#right_bottom_table_width").val("50%");
        
        $("#bill_table_info_width").val("50%");
        $("#bill_table_info_border_width").val("1px");
        $("#bill_table_info_border_style").val("soild");
        $("#bill_table_info_border_color").val("black");

        $("#bill_table_margin_bottom").val("10px");
        $("#bill_table_margin_top").val("10px");
        
        $("#bill_table_border_width").val("1px");
        $("#bill_table_border_style").val("solid");
        $("#bill_table_border_color").val("black");
        
        $("#bill_table_left_td_weight").val("300");
        $("#bill_table_left_td_font_size").val("16px");
        $("#bill_table_left_td_width").val("60%");

        $("#bill_table_left_td_border_color").val("black");
        $("#bill_table_left_td_border_style").val("solid");
        $("#bill_table_left_td_padding_left").val("0px");
        $("#bill_table_left_td_border_width").val("1px");
        $("#bill_table_left_td_text_align").val("left");
        
        $("#bill_table_right_td_width").val("40%");
        $("#bill_table_right_td_font_size").val("16px");
        $("#bill_table_right_td_weight").val("300");

        $("#bill_table_right_td_border_color").val("black");
        $("#bill_table_right_td_border_style").val("solid");
        $("#bill_table_right_td_padding_left").val("0px");
        $("#bill_table_right_td_border_width").val("1px");
        $("#bill_table_right_td_text_align").val("right");
        

        $("#line_bill_table_border_color").val("black");
        $("#line_bill_table_border_style").val("solid");
        $("#line_bill_table_border_width").val("1px");
        $("#line_bill_table_color").val("black");
        $("#line_bill_table_height").val("2px");
        $("#line_bill_table_width").val("100%");
        $("#line_bill_table_td_margin_left").val("10px");
    
    }
    function init_footer(){
        $("#style_footer").val("table");
        $("#footer_width").val("100%") ;
        $("#footer_style_letter").val("capitalize");
        $("#footer_table_width").val("100%") ;
        $("#footer_table_color").val("transparent") ;
        $("#footer_table_radius").val("0px") ;
        
        $("#align_text_footer").val("left");
        $("#footer_font_size").val("22px");
        $("#footer_font_weight").val("300") ;
        
        $("#footer_border_width").val("0px") ;
        $("#footer_border_style").val("solid") ;
        $("#footer_border_color").val("transparent");
        
        $("#footer_padding_left").val("0px");
        $("#footer_padding_top").val("0px");
        $("#footer_padding_bottom").val("0px");
        $("#footer_padding_right").val("0px");
        

        $("#footer_position").val("relative");

        $("#footer_right").val("0px");
        $("#footer_left").val("0px");
        $("#footer_bottom").val("0px");
        $("#footer_top").val("0px");
        
        $("#position_img_footer").val("right");
        $("#align_image_footer").val("right");

        $("#footer_image_width").val("100");
        $("#footer_image_height").val("100");

        $("#footer_image_border_radius").val("0px");
        $("#footer_image_border_style").val("solid");
        $("#footer_image_border_color").val("transparent");
        $("#footer_image_border_width").val("0px");
        
        
        $("#footer_box_image_color").val("transparent");
        $("#footer_box_image_background").val("transparent");
        $("#footer_image_box_margin").val(" auto 0%");
        $("#footer_image_box_height").val("100%");
        $("#footer_image_box_width").val("32.333%");
        
        $("#footer_image_box_border_radius").val("0px");
        $("#footer_image_box_border_color").val("transparent");
        
        $("#footer_image_box_border_style").val("solid");
        $("#footer_image_box_border_width").val("0px");
        $("#footer_image_box_background").val("transparent");
        $("#position_box_footer_align").val("center");
        
        $("#footer_other_border_radius").val("0px");
        $("#footer_other_width").val("32.333%");
        $("#other_background_footer").val("transparent");
        $("#align_other_footer").val("center");
        
        $("#footer_other_border_width").val("0px");
        $("#footer_other_border_style").val("solid");
        $("#footer_other_border_color").val("transparent");
        
        $("#footer_other_bottom").val("0px");
        $("#footer_other_right").val("0px");
        $("#footer_other_left").val("0px");
        $("#footer_other_top").val("0px");
        $("#footer_other_position").val("relative");
        
        $("#footer_tax_right").val("0px");
        $("#footer_tax_left").val("0px");
        $("#footer_tax_bottom").val("0px");
        $("#footer_tax_top").val("0px");
        $("#footer_tax_position").val("relative");
        
        $("#footer_tax_letter").val("capitalize");
        $("#footer_tax_width").val("100%");
        $("#footer_tax_font_size").val("22px");
        $("#footer_tax_align").val("center");
        
        $("#footer_tax_border_color").val("transparent");
        $("#footer_tax_border_style").val("solid");
        $("#footer_tax_border_width").val("0px");
        
        $("#footer_tax_padding_bottom").val("0px");
        $("#footer_tax_padding_right").val("0px");
        $("#footer_tax_padding_left").val("0px");
        $("#footer_tax_padding_top").val("0px");

        $("#footer_address_letter").val("capitalize");
        $("#footer_address_width").val("100%");
        $("#footer_address_font_size").val("22px");
        $("#footer_address_align").val("center");
        
        $("#footer_address_border_color").val("transparent");
        $("#footer_address_border_style").val("solid");
        $("#footer_address_border_width").val("0px");
        
        $("#footer_address_padding_bottom").val("0px");
        $("#footer_address_padding_right").val("0px");
        $("#footer_address_padding_left").val("0px");
        $("#footer_address_padding_top").val("0px");
       
        $("#footer_address_right").val("0px");
        $("#footer_address_left").val("0px");
        $("#footer_address_bottom").val("0px");
        $("#footer_address_top").val("0px");
        $("#footer_address_position").val("relative");
       
        $("#footer_bill_right").val("0px");
        $("#footer_bill_left").val("0px");
        $("#footer_bill_bottom").val("0px");
        $("#footer_bill_top").val("0px");
        $("#footer_bill_position").val("relative");


        $("#footer_bill_letter").val("capitalize");
        $("#footer_bill_width").val("100%");
        $("#footer_bill_font_size").val("22px");
        $("#footer_bill_align").val("center");
        
        $("#footer_bill_border_width").val("0px");
        $("#footer_bill_border_style").val("solid");
        $("#footer_bill_border_color").val("transparent");
        
        $("#footer_bill_padding_bottom").val("0px");
        $("#footer_bill_padding_right").val("0px");
        $("#footer_bill_padding_left").val("0px");
        $("#footer_bill_padding_top").val("0px");
        

        $("#footer_line_color").val("black");
        $("#footer_line_width").val("50%");
        $("#footer_line_height").val("1px");
        
        $("#footer_line_radius").val("0px");
        $("#footer_line_margin_top").val("10px");
        
        $("#footer_line_border_color").val("black");
        $("#footer_line_border_style").val("solid");
        $("#footer_line_border_width").val("1px");
        
        
       
        
    }

    // reset section
    $("#set_all_default").on("click",function(){
        // alert("all");
        // top nave bar 
        $("#name_template").val("");
        $("#Paper-size").val(0).trigger('change');
        $("#Form-type").val("Sale").trigger('change');
        $("#Pattern-type").val(null).trigger('change');
        $("#Voucher-type").val("Payment").trigger('change');
        $("#Cheque-type").val("In").trigger('change');
        // header settings 
        init_header();
        init_content();
        init_footer();
        load_header();
        load_body();
        load_footer();
    });
    $("#set_header_default").on("click",function(){
        // alert("header");
        // header settings 
        init_header();
        load_header();
    });
    $("#set_content_default").on("click",function(){
        // alert("content");
        init_content();
        load_body();
    });
    $("#set_footer_default").on("click",function(){
        // alert("footer");
        init_footer();
        load_footer();
    });

    $(document).ready(function(){
        $("#change_setting1").on("click",function(){
             $("#change_setting1").addClass("hide");
             $("#change_setting2").removeClass("hide");
             $(".paper-setting").addClass("hide");
             $(".paper-content").removeClass("hide");
        });
        $("#change_setting2").on("click",function(){
             $("#change_setting1").removeClass("hide");
             $("#change_setting2").addClass("hide");
             $(".paper-setting").removeClass("hide");
             $(".paper-content").addClass("hide");
        });
    })
 
    // header
    $('input:radio[name="left_header_radio"]').change(
        function(){
            if (this.checked && this.value == 'write') {
                load_content_header();
                $(".style_write").removeClass("hide");
                $(".style_write_drop").addClass("hide");
            }else{
                load_header($("#left_header_id").val(),$("#center_top_header_id").val());
                $(".style_write_drop").removeClass("hide");
                $(".style_write").addClass("hide");

            }
    });
    $('input:radio[name="center_top_header_radio"]').change(
        function(){
            if (this.checked && this.value == 'write') {
                load_content_header();
                $(".style_center_top_header_write").removeClass("hide");
                $(".style_write_center_top_header_drop").addClass("hide");
            }else{
                load_header($("#left_header_id").val(),$("#center_top_header_id").val());
                $(".style_write_center_top_header_drop").removeClass("hide");
                $(".style_center_top_header_write").addClass("hide");

            }
    });
    $('input:radio[name="center_middle_header_radio"]').change(
        function(){
            if (this.checked && this.value == 'write') {
                load_content_header();
                $(".style_center_middle_header_write").removeClass("hide");
                $(".style_write_center_middle_header_drop").addClass("hide");
            }else{
                load_header($("#left_header_id").val(),$("#center_top_header_id").val(),$("#center_middle_header_id").val());
                $(".style_write_center_middle_header_drop").removeClass("hide");
                $(".style_center_middle_header_write").addClass("hide");

            }
    });
    $('input:radio[name="center_last_header_radio"]').change(
        function(){
            if (this.checked && this.value == 'write') {
                load_content_header();
                $(".style_center_last_header_write").removeClass("hide");
                $(".style_write_center_last_header_drop").addClass("hide");
            }else{
                load_header($("#left_header_id").val(),$("#center_top_header_id").val(),$("#center_middle_header_id").val(),$("#center_last_header_id").val());
                $(".style_write_center_last_header_drop").removeClass("hide");
                $(".style_center_last_header_write").addClass("hide");

            }
    });
        /* 1- Select From Layout  */
    $("#left_header_id").on("change",function(){
        load_content_header($(this).val(),$("#center_top_header_id").val());
        setTimeout(() => {
            tinymce.get('left_header').setContent($(".title-header").html())
            if($("#center_top_header_id").val() != null){
                tinymce.get('center_top_header').setContent($(".tax").html())
            }
        }, 2000);
    });
    $("#center_top_header_id").on("change",function(){
        
        load_content_header($("#left_header_id").val(),$(this).val());
        setTimeout(() => {
            if($("#left_header_id").val() != null){
                tinymce.get('left_header').setContent($(".title-header").html())
            }
            tinymce.get('center_top_header').setContent($(".tax").html())
        }, 2000);
    });
    $("#center_middle_header_id").on("change",function(){
        
        load_content_header($("#left_header_id").val(),$("#center_top_header_id").val(),$(this).val());
        setTimeout(() => {
            if($("#left_header_id").val() != null){
                tinymce.get('left_header').setContent($(".title-header").html())
            }
            if($("#center_top_header_id").val() != null){
                tinymce.get('center_top_header').setContent($(".tax").html())
            }
            tinymce.get('center_middle_header').setContent($(".address").html())
        }, 2000);
    });
    $("#center_last_header_id").on("change",function(){
        
        load_content_header($("#left_header_id").val(),$("#center_top_header_id").val(),$("#center_middle_header_id").val(),$(this).val());
        setTimeout(() => {
            if($("#left_header_id").val() != null){
                tinymce.get('left_header').setContent($(".title-header").html())
            }
            if($("#center_top_header_id").val() != null){
                tinymce.get('center_top_header').setContent($(".tax").html())
            }
            if($("#center_middle_header_id").val() != null){
                tinymce.get('center_middle_header').setContent($(".address").html())
            }
            tinymce.get('center_last_header').setContent($(".name_of_bill").html())
        }, 2000);
    });


    // footer
    $('input:radio[name="left_footer_radio"]').change(
        function(){
            if (this.checked && this.value == 'write') {
             
                $(".style_footer_write").removeClass("hide");
                $(".style_write_footer_drop").addClass("hide");
            }else{
                
                $(".style_write_footer_drop").removeClass("hide");
                $(".style_footer_write").addClass("hide");

            }
    });
    $('input:radio[name="center_top_footer_radio"]').change(
        function(){
            if (this.checked && this.value == 'write') {
             
                $(".style_center_top_footer_write").removeClass("hide");
                $(".style_write_center_top_footer_drop").addClass("hide");
            }else{
                
                $(".style_write_center_top_footer_drop").removeClass("hide");
                $(".style_center_top_footer_write").addClass("hide");

            }
    });
    $('input:radio[name="center_middle_footer_radio"]').change(
        function(){
            if (this.checked && this.value == 'write') {
             
                $(".style_center_middle_footer_write").removeClass("hide");
                $(".style_write_center_middle_footer_drop").addClass("hide");
            }else{
                
                $(".style_write_center_middle_footer_drop").removeClass("hide");
                $(".style_center_middle_footer_write").addClass("hide");

            }
    });
    $('input:radio[name="center_last_footer_radio"]').change(
        function(){
            if (this.checked && this.value == 'write') {
             
                $(".style_center_last_footer_write").removeClass("hide");
                $(".style_write_center_last_footer_drop").addClass("hide");
            }else{
                
                $(".style_write_center_last_footer_drop").removeClass("hide");
                $(".style_center_last_footer_write").addClass("hide");

            }
    });
     /* 1- Select From Layout  */
    $("#left_footer_id").on("change",function(){
        load_content_footer($(this).val(),$("#center_top_footer_id").val());
        setTimeout(() => {
            tinymce.get('left_footer').setContent($(".footer_title-header").html())
            if($("#center_top_footer_id").val() != null){
                tinymce.get('center_top_footer').setContent($(".footer_tax").html())
            }
        }, 1000);
    });
    $("#center_top_footer_id").on("change",function(){
        
        load_content_footer($("#left_footer_id").val(),$(this).val());
        setTimeout(() => {
            if($("#left_footer_id").val() != null){
                tinymce.get('left_footer').setContent($(".footer_title-header").html())
            }
            tinymce.get('center_top_footer').setContent($(".footer_tax").html())
        }, 1000);
    });
    $("#center_middle_footer_id").on("change",function(){
        
        load_content_footer($("#left_footer_id").val(),$("#center_top_footer_id").val(),$(this).val());
        setTimeout(() => {
            if($("#left_footer_id").val() != null){
                tinymce.get('left_footer').setContent($(".footer_title-header").html())
            }
            if($("#center_top_footer_id").val() != null){
                tinymce.get('center_top_footer').setContent($(".footer_tax").html())
            }
            tinymce.get('center_middle_footer').setContent($(".footer_address").html())
        }, 1000);
    });
    $("#center_last_footer_id").on("change",function(){
        
        load_content_footer($("#left_footer_id").val(),$("#center_top_footer_id").val(),$("#center_middle_footer_id").val(),$(this).val());
        setTimeout(() => {
            if($("#left_footer_id").val() != null){
                tinymce.get('left_footer').setContent($(".footer_title-header").html())
            }
            if($("#center_top_footer_id").val() != null){
                tinymce.get('center_top_footer').setContent($(".footer_tax").html())
            }
            if($("#center_middle_footer_id").val() != null){
                tinymce.get('center_middle_footer').setContent($(".footer_address").html())
            }
            tinymce.get('center_last_footer').setContent($(".footer_name_of_bill").html())
        }, 1000);
    });

    
    // content
    $('input:radio[name="left_top_content_radio"]').change(
        function(){
            if (this.checked && this.value == 'write') {
             
                $(".style_top_left_content_write").removeClass("hide");
                $(".style_write_top_left_content_drop").addClass("hide");
            }else{
                
                $(".style_write_top_left_content_drop").removeClass("hide");
                $(".style_top_left_content_write").addClass("hide");

            }
    });
    $('input:radio[name="right_top_content_radio"]').change(
        function(){
            if (this.checked && this.value == 'write') {
             
                $(".style_top_right_content_write").removeClass("hide");
                $(".style_write_top_right_content_drop").addClass("hide");
            }else{
                
                $(".style_write_top_right_content_drop").removeClass("hide");
                $(".style_top_right_content_write").addClass("hide");

            }
    });
    $('input:radio[name="bottom_content_radio"]').change(
        function(){
            if (this.checked && this.value == 'write') {
             
                $(".style_bottom_content_write").removeClass("hide");
                $(".style_write_bottom_content_drop").addClass("hide");
            }else{
                
                $(".style_write_bottom_content_drop").removeClass("hide");
                $(".style_bottom_content_write").addClass("hide");

            }
    });
     /* 1- Select From Layout  */
     $("#left_top_content_id").on("change",function(){
        load_content_body($(this).val(),$("#right_top_content_id").val(),$("#bottom_content_id").val());
        setTimeout(() => {
            tinymce.get('left_top_content').setContent($(".left_top_table").html())
            if($("#right_top_content_id").val() != null){
                tinymce.get('right_top_content').setContent($(".right_top_table").html())
            }
            if($("#bottom_content_id").val() != null){
                tinymce.get('bottom_content').setContent($(".left_bottom_table").html())
            }
        }, 1000);
    });
    $("#right_top_content_id").on("change",function(){
        
        load_content_body($("#left_top_content_id").val(),$(this).val(),$("#bottom_content_id").val());
        setTimeout(() => {
            if($("#left_top_content_id").val() != null){
                tinymce.get('left_top_content').setContent($(".left_top_table").html())
            }
            if($("#bottom_content_id").val() != null){
                tinymce.get('bottom_content').setContent($(".left_bottom_table").html())
            }
            tinymce.get('right_top_content').setContent($(".right_top_table").html())
        }, 1000);
    });
    $("#bottom_content_id").on("change",function(){
        
        load_content_body($("#left_top_content_id").val(),$("#right_top_content_id").val(),$(this).val());
        setTimeout(() => {
            if($("#left_top_content_id").val() != null){
                tinymce.get('left_top_content').setContent($(".left_top_table").html())
            }
            if($("#right_top_content_id").val() != null){
                tinymce.get('right_top_content').setContent($(".right_top_table").html())
            }
            tinymce.get('bottom_content').setContent($(".left_bottom_table").html())
        }, 1000);
    });


    // editor
    if ($('#center_last_footer').length) {
        init_tinymce('center_last_footer');
    }
    if ($('#center_middle_footer').length) {
        init_tinymce('center_middle_footer');
    }
    if ($('#center_top_footer').length) {
        init_tinymce('center_top_footer');
    }
    if ($('#left_footer').length) {
        init_tinymce('left_footer');
    }
    if ($('#center_last_header').length) {
        init_tinymce('center_last_header');
    }
    if ($('#center_middle_header').length) {
        init_tinymce('center_middle_header');
    }
    if ($('#center_top_header').length) {
        init_tinymce('center_top_header');
    }
    if ($('#left_header').length) {
        init_tinymce('left_header');
    }
    if ($('#bottom_content').length) {
        init_tinymce('bottom_content');
    }
    if ($('#right_top_content').length) {
        init_tinymce('right_top_content');
    }
    if ($('#left_top_content').length) {
        init_tinymce('left_top_content');
    }
    
    function delete_image(type){
        if(type == 1){
            swal({
                title: LANG.sure,
                icon: 'warning',
                buttons: true,
                dangerMode: true,
            }).then(willDelete => {
                if (willDelete) {
                    html = '<input type="text" hidden value="delete_header_image" id="delete_header_image" name="delete_header_image">';
                    $(".img_sec").html(html);
                }
            });
        }else if(type == 2){
            swal({
                title: LANG.sure,
                icon: 'warning',
                buttons: true,
                dangerMode: true,
            }).then(willDelete => {
                if (willDelete) {
                    html = '<input type="text" hidden value="delete_header_image" id="delete_footer_image" name="delete_footer_image">';
                    $(".foot_img_sec").html(html);
                }
            });
        }
    }
 
    function PrintModule(html){
        $.ajax({
            url:"/printers/print/p",
            method:"GET",
            dataType:"html",
            data:{
                html:"htm"
            },  
            success: function(result) {
                
            },
        });
    }

    $("#upload_image").on("change",function(){
        readURL(this,1);
    })
    $("#upload_document").on("change",function(){
        readURL(this,2);
    })

    
    
    function readURL(input,number) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function (e) {
            if(number == 1){
                $('#header_img_logo').attr('src', e.target.result); 
            }else{    
                $('#footer_img_logo').attr('src', e.target.result); 
            }

        }

        reader.readAsDataURL(input.files[0]);
    }
}

</script>
@endsection