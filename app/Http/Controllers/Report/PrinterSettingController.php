<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use DOMPDF;
use SnaPDF;
use \App\Models\PrinterTemplate;
use \App\Models\PrinterContentTemplate;
use \App\Models\PrinterTemplateContain;
use \App\Models\PrinterFooterTemplate;
use Yajra\DataTables\Facades\DataTables;
use ArPHP\I18N\Arabic; 

use Dompdf\Options;
class PrinterSettingController extends Controller
{
    //
    public function generatePdf()
    {
        if(request()->ajax()){
            $html = request()->html;
            $pdf = DomPDF::loadHtml($html)
                ->setPaper('a4') 
                ->setOption('enable-javascript', true)
                ->setOption('javascript-delay', 1000)
                ->setOption('no-stop-slow-scripts', true)
                ->setOption('window-status', 'ready');
            return $pdf->stream('report.pdf');
        }
        $id              = request()->input('id');
        $transaction_id  = request()->input('sell_id');
        $transaction     = \App\Transaction::find($transaction_id);        

        $template        = \App\Models\PrinterTemplate::find($id);

        $contentTemp  = \App\Models\PrinterContentTemplate::where("printer_template_id",$template->id)->first();
        $footerTemp   = \App\Models\PrinterFooterTemplate::where("printer_template_id",$template->id)->first();
        $containTemp  = \App\Models\PrinterTemplateContain::where("printer_templates_id",$template->id)->first();
        /** requirment */
        $footer_display = 0  ; /*1* for choose footer hide or display*/
        $header_display = 0  ; /*2* for choose header hide or display*/
        
        $print                    = [] ;
        $print_content            = [] ;
        $print_footer             = [] ;

        $print["align_text_header"] = $template->align_text_header;              /**   محاذات العناون*/
        $print["style_header"] = $template->style_header;                       /**   شكل العنوان  */
        $print["Form_type"] = $template->Form_type;                       /**   شكل العنوان  */
        $print["header_font_size"] = $template->header_font_size;                /**   خط العنوان */
        $print["header_font_weight"] = $template->header_font_weight;                       /**   خط عريض العنوان */
        $print["header_width"] = $template->header_width;                        /**   عرض الإطار العنوان  */ 

        $print["header_border_width"] = $template->header_border_width;           /**   عرض الإطار العنوان  $$$$$ */ 
        $print["header_border_style"] = $template->header_border_style;         /**   شكل إطار العنوان   $$$$$ */
        $print["header_border_color"] = $template->header_border_color;         /**   لون إطار العنوان   $$$$$ */
        
        $print["header_padding_right"] = $template->header_padding_right;         /**   المحيط التوسع  */
        $print["header_padding_left"] = $template->header_padding_left;           /**   المحيط التوسع   */
        $print["header_padding_top"] = $template->header_padding_top;             /**   المحيط التوسع   */
        $print["header_padding_bottom"] = $template->header_padding_bottom;       /**   المحيط التوسع   */
        
        $print["header_position"] = $template->header_position;              /** طريقة عرض العنوان */
        $print["header_style_letter"] = $template->header_style_letter;    /**   أول حرف كبير   */   
        
        $print["header_top"] = $template->header_top;        /**   موقع الاحداثيات العلوية   */
        $print["header_left"] = $template->header_left;        /**   موقع الاحداثيات اليسارية   */
        $print["header_right"] = $template->header_right;      /**   موقع الاحداثيات اليمينية  $$$$$ */
        $print["header_bottom"] = $template->header_bottom;    /**   موقع الاحداثيات السفلية   $$$$$$ */
    
        // table
        $print["header_table_width"] = $template->header_table_width;            /**   محاذات العنصر الأصلي  */
        $print["header_table_color"] = $template->header_table_color;           /**   عرض الإطار العنوان  */
        $print["header_table_radius"] = $template->header_table_radius;            /**   عرض الإطار العنوان  */

        //$%^ boxes
        $print["align_other_header"] = $template->align_other_header;     /**   محاذات العنصر الأصلي  */
        $print["header_other_border_width"] = $template->header_other_border_width;          /**   عرض الإطار العنوان  */
        $print["header_other_border_style"] = $template->header_other_border_style;        /**   شكل إطار العنوان  */
        $print["header_other_border_color"] = $template->header_other_border_color;        /**   لون إطار العنوان  */
        $print["header_other_position"] = $template->header_other_position;     /**   محاذات العنصر الأصلي  */
        $print["header_other_top"] = $template->header_other_top;         /**   لون إطار العنوان  */
        $print["header_other_left"] = $template->header_other_left;          /**   لون إطار العنوان  */
        $print["header_other_right"] = $template->header_other_right;          /**   لون إطار العنوان  */
        $print["header_other_bottom"] = $template->header_other_bottom;          /**   لون إطار العنوان  */
        

        // tax
        $print["header_tax_align"] = $template->header_tax_align;       /**   محاذات العناون*/
        $print["header_tax_font_size"] = $template->header_tax_font_size;          /**   خط العنوان */
        $print["header_tax_width"] = $template->header_tax_width;         /**   عرض الإطار العنوان  */
        $print["header_tax_border_width"] = $template->header_tax_border_width;          /**   عرض الإطار العنوان  */
        $print["header_tax_border_style"] = $template->header_tax_border_style;        /**   شكل إطار العنوان  */
        $print["header_tax_border_color"] = $template->header_tax_border_color;        /**   لون إطار العنوان  */
        $print["header_tax_right"] = $template->header_tax_right;          /**   المحيط التوسع  */
        $print["header_tax_left"] = $template->header_tax_left;          /**   المحيط التوسع   */
        $print["header_tax_top"] = $template->header_tax_top;          /**   المحيط التوسع   */
        $print["header_tax_bottom"] = $template->header_tax_bottom;          /**   المحيط التوسع   */
        $print["header_tax_letter"] = $template->header_tax_letter;    /**   أول حرف كبير   */
        $print["header_tax_position"] = $template->header_tax_position;     /**   مكان توضع العنصر  */
        $print["header_tax_padding_top"] = $template->header_tax_padding_top;        /**   محاذات العنصر العائم  */
        $print["header_tax_padding_bottom"] = $template->header_tax_padding_bottom;          /**   محاذات العنصر العائم  */
        $print["header_tax_padding_left"] = $template->header_tax_padding_left;          /**   محاذات العنصر العائم  */
        $print["header_tax_padding_right"] = $template->header_tax_padding_right;          /**   محاذات العنصر العائم  */
        
        // address
        $print["header_address_align"] = $template->header_address_align;       /**   محاذات العناون*/
        $print["header_address_font_size"] = $template->header_address_font_size;          /**   خط العنوان */
        $print["header_address_width"] = $template->header_address_width;         /**   عرض الإطار العنوان  */
        $print["header_address_border_width"] = $template->header_address_border_width;          /**   عرض الإطار العنوان  */
        $print["header_address_border_style"] = $template->header_address_border_style;        /**   شكل إطار العنوان  */
        $print["header_address_border_color"] = $template->header_address_border_color;        /**   لون إطار العنوان  */
        $print["header_address_right"] = $template->header_address_right;          /**   المحيط التوسع  */
        $print["header_address_left"] = $template->header_address_left;          /**   المحيط التوسع   */
        $print["header_address_top"] = $template->header_address_top;          /**   المحيط التوسع   */
        $print["header_address_bottom"] = $template->header_address_bottom;          /**   المحيط التوسع   */
        $print["header_address_letter"] = $template->header_address_letter;    /**   أول حرف كبير   */
        $print["header_address_position"] = $template->header_address_position;     /**   مكان توضع العنصر  */
        $print["header_address_padding_top"] = $template->header_address_padding_top;        /**   محاذات العنصر العائم  */
        $print["header_address_padding_bottom"] = $template->header_address_padding_bottom;          /**   محاذات العنصر العائم  */
        $print["header_address_padding_left"] = $template->header_address_padding_left;          /**   محاذات العنصر العائم  */
        $print["header_address_padding_right"] = $template->header_address_padding_right;         /**   محاذات العنصر العائم  */
        
        // bill name
        $print["header_bill_align"] = $template->header_bill_align;       /**   محاذات العناون */ 
        $print["header_bill_font_size"] = $template->header_bill_font_size;         /**   خط العنوان */
        $print["header_bill_width"] = $template->header_bill_width;         /**   عرض الإطار العنوان  */
        $print["header_bill_border_width"] = $template->header_bill_border_width;          /**   عرض الإطار العنوان  */
        $print["header_bill_border_style"] = $template->header_bill_border_style;        /**   شكل إطار العنوان  */
        $print["header_bill_border_color"] = $template->header_bill_border_color;        /**   لون إطار العنوان  */
        $print["header_bill_right"] = $template->header_bill_right;          /**   المحيط التوسع  */
        $print["header_bill_left"] = $template->header_bill_left;          /**   المحيط التوسع   */
        $print["header_bill_top"] = $template->header_bill_top;          /**   المحيط التوسع   */
        $print["header_bill_bottom"] = $template->header_bill_bottom;          /**   المحيط التوسع   */
        $print["header_bill_letter"] = $template->header_bill_letter;    /**   أول حرف كبير   */
        $print["header_bill_position"] = $template->header_bill_position;     /**   مكان توضع العنصر  */
        $print["header_bill_padding_top"] = $template->header_bill_padding_top;        /**   محاذات العنصر العائم  */
        $print["header_bill_padding_bottom"] = $template->header_bill_padding_bottom;          /**   محاذات العنصر العائم  */
        $print["header_bill_padding_left"] = $template->header_bill_padding_left;          /**   محاذات العنصر العائم  */
        $print["header_bill_padding_right"] = $template->header_bill_padding_right;         /**   محاذات العنصر العائم  */
        
        // image 
        $print["align_image_header"] = $template->align_image_header;                           /**   مكان توضع الصورة في الترويسة */
        $print["position_img_header"] = $template->position_img_header;                         /**   مكان توضع الصورة في جزء الصورة في الترويسة*/
        $print["header_image_width"] = $template->header_image_width ;                          /**   عرض الصورة    */
        $print["header_image_height"] = $template->header_image_height ;                        /**   ارتفاع الصورة */ 
        $print["header_image_border_width"] = $template->header_image_border_width ;            /**   عرض الإطار  الصورة  */
        $print["header_box_image_background"] = $template->header_box_image_background;  /**   خلفية الصورة */ 
        $print["header_image_border_style"] = $template->header_image_border_style;            /**   شكل إطار الصورة  */
        $print["header_image_border_color"] = $template->header_image_border_color;            /**   لون إطار الصورة  */
        $print["header_image_border_radius"] = $template->header_image_border_radius;            /**   تصميم إطار الصورة */
        $print["header_box_image_color"] = $template->header_box_image_color;   
        $print["header_image_box_height"] = $template->header_image_box_height;
        
        // image box
        
        $print["header_image_view"]              =  ($template->header_image_view == 1)?true:false;
        $print["header_image_view"]              = ($template->header_image_view == 1)?true:false;
        $print["position_box_header_align"] = $template->position_box_header_align;
        $print["header_image_box_width"] = $template->header_image_box_width;                 /**   لون إطار العنوان  */
        $print["header_image_box_margin"] = $template->header_image_box_margin;                /**   لون إطار العنوان  */
        $print["header_image_box_border_width"] = $template->header_image_box_border_width;         /**   عرض الإطار العنوان  */
        $print["header_image_box_border_style"] = $template->header_image_box_border_style;       /**   شكل إطار العنوان  */
        $print["header_image_box_border_color"] = $template->header_image_box_border_color;        /**   لون إطار العنوان  */
        $print["header_image_box_border_radius"] = $template->header_image_box_border_radius;                     /**   لون إطار العنوان  */
        $print["header_image_box_background"] = $template->header_image_box_background;                  /**   عرض الإطار العنوان  */
        
        
        
        // header box
        $print["header_view"]           =  ($template->header_view == 1)?true:false;
        $print["header_box_width"] = $template->header_box_width;         /**   لون إطار العنوان  */
        $print["header_box_border_width"]     = $template->header_box_border_width;          /**   عرض الإطار العنوان  */
        $print["header_box_border_style"] = $template->header_box_border_style;        /**   شكل إطار العنوان  */
        $print["header_box_border_color"] = $template->header_box_border_color;        /**   لون إطار العنوان  */
        $print["header_box_border_radius"] = $template->header_box_border_radius;         /**   لون إطار العنوان  */
        $print["header_box_background"] = $template->header_box_background;       /**   عرض الإطار العنوان  */

        
        //$%^ other box
        $print["header_other_view"]    =  ($template->header_other_view == 1)?true:false;        /**   لون إطار العنوان  */
        $print["header_other_width"] = $template->header_other_width;                          /**   لون إطار العنوان  */
        $print["header_other_border_width"] = $template->header_other_border_width;          /**   عرض الإطار العنوان  */
        $print["header_other_border_style"] = $template->header_other_border_style;        /**   شكل إطار العنوان  */
        $print["header_other_border_color"] = $template->header_other_border_color;        /**   لون إطار العنوان  */
        $print["header_other_border_radius"] = $template->header_other_border_radius;         /**   لون إطار العنوان  */
        $print["other_background_header"] = $template->other_background_header;       /**   عرض الإطار العنوان  */

        // rows lines 
        $print["header_line_view"]    =  ($template->header_line_view == 1)?true:false;       
        $print["header_line_width"] = $template->header_line_width ;
        $print["header_line_height"] = $template->header_line_height ;
        $print["header_line_color"] = $template->header_line_color ;
        $print["header_line_radius"] = $template->header_line_radius ;
        $print["header_line_border_width"] = $template->header_line_border_width;
        $print["header_line_border_style"] = $template->header_line_border_style ;
        $print["header_line_border_color"] = $template->header_line_border_color ;
        $print["header_line_margin_top"] = $template->header_line_margin_top ;
        
        // .................................content
        
        $print["left_header_title"]             =   $containTemp->left_header_title  ;
        $print["center_top_header_title"]       =   $containTemp->center_top_header_title;
        $print["center_middle_header_title"]    =   $containTemp->center_middle_header_title;
        $print["center_last_header_title"]      =   $containTemp->center_last_header_title;
        if(!$containTemp->image_url){
            $print["image_url"]                     =    "https://thumbs.dreamstime.com/b/invoice-linear-icon-modern-outline-invoice-logo-concept-whit-invoice-linear-icon-modern-outline-invoice-logo-concept-white-133517211.jpg";
        }else{
            
            $path =  $containTemp->image_path_second ;
            if($path){
                $da   = file_get_contents($path);
                $type = pathinfo($path,PATHINFO_EXTENSION);
                $pic  = 'data:image/' . $type . ';base64,' . base64_encode($da);
            }else{
                $pic = '';
            }
            $print["image_url"]                     =    $pic;
        
        }

        $print_footer["align_text_footer"]  = $footerTemp->align_text_footer;        /**   محاذات العناون*/
        $print_footer["style_footer"]  = $footerTemp->style_footer;         /**   شكل العنوان  */
        $print_footer["footer_font_size"]  = $footerTemp->footer_font_size;          /**   خط العنوان */
        $print_footer["footer_font_weight"]  = $footerTemp->footer_font_weight;
        $print_footer["footer_width"]  = $footerTemp->footer_width;           /**   عرض الإطار العنوان  */
        $print_footer["footer_border_width"]  = $footerTemp->footer_border_width;          /**   عرض الإطار العنوان  */
        $print_footer["footer_border_style"]  = $footerTemp->footer_border_style;        /**   شكل إطار العنوان  */
        $print_footer["footer_border_color"]  = $footerTemp->footer_border_color;        /**   لون إطار العنوان  */
        $print_footer["footer_padding_right"]  = $footerTemp->footer_padding_right;            /**   المحيط التوسع  */
        $print_footer["footer_padding_left"]  = $footerTemp->footer_padding_left;              /**   المحيط التوسع   */
        $print_footer["footer_padding_top"]  = $footerTemp->footer_padding_top;                 /**   المحيط التوسع   */
        $print_footer["footer_padding_bottom"]  = $footerTemp->footer_padding_bottom;          /**   المحيط التوسع   */
        $print_footer["footer_position"]  = $footerTemp->footer_position;          /**   أول حرف كبير   */
        $print_footer["footer_style_letter"]  = $footerTemp->footer_style_letter;  /**   أول حرف كبير   */
        $print_footer["footer_top"]  = $footerTemp->footer_top;           /**   أول حرف كبير   */
        $print_footer["page_number_view"]  =  ( $footerTemp->page_number_view === 1)?true:false;           /**   أول حرف كبير   */
        $print_footer["footer_left"]  = $footerTemp->footer_left;         /**   أول حرف كبير   */
        $print_footer["footer_right"]  = $footerTemp->footer_right;      /**   أول حرف كبير   */
        $print_footer["footer_bottom"]  = $footerTemp->footer_bottom;   /**   أول حرف كبير   */
        // table
        $print_footer["footer_table_width"]  = $footerTemp->footer_table_width;         /**   محاذات العنصر الأصلي  */
        $print_footer["footer_table_color"]  = $footerTemp->footer_table_color;             /**   عرض الإطار العنوان  */
        $print_footer["footer_table_radius"]  = $footerTemp->footer_table_radius;          /**   عرض الإطار العنوان  */
        // boxes
        $print_footer["align_other_footer"]  = $footerTemp->align_other_footer;
        $print_footer["footer_other_border_width"]  = $footerTemp->footer_other_border_width;          /**   عرض الإطار العنوان  */
        $print_footer["footer_other_border_style"]  = $footerTemp->footer_other_border_style;        /**   شكل إطار العنوان  */
        $print_footer["footer_other_border_color"]  = $footerTemp->footer_other_border_color;        /**   لون إطار العنوان  */
        $print_footer["footer_other_position"]  = $footerTemp->footer_other_position;     /**   محاذات العنصر الأصلي  */
        $print_footer["footer_other_top"]  = $footerTemp->footer_other_top;        /**   لون إطار العنوان  */
        $print_footer["footer_other_left"]  = $footerTemp->footer_other_left;        /**   لون إطار العنوان  */
        $print_footer["footer_other_right"]  = $footerTemp->footer_other_right;        /**   لون إطار العنوان  */
        $print_footer["footer_other_bottom"]  = $footerTemp->footer_other_bottom;        /**   لون إطار العنوان  */
        // tax
        $print_footer["footer_tax_align"]  = $footerTemp->footer_tax_align;       /**   محاذات العناون*/
        $print_footer["footer_tax_font_size"]  = $footerTemp->footer_tax_font_size;         /**   خط العنوان */
        $print_footer["footer_tax_width"]  = $footerTemp->footer_tax_width;           /**   عرض الإطار العنوان  */
        $print_footer["footer_tax_border_width"]  = $footerTemp->footer_tax_border_width;          /**   عرض الإطار العنوان  */
        $print_footer["footer_tax_border_style"]  = $footerTemp->footer_tax_border_style;        /**   شكل إطار العنوان  */
        $print_footer["footer_tax_border_color"]  = $footerTemp->footer_tax_border_color;        /**   لون إطار العنوان  */
        $print_footer["footer_tax_right"]  = $footerTemp->footer_tax_right;          /**   المحيط التوسع  */
        $print_footer["footer_tax_left"]  = $footerTemp->footer_tax_left;          /**          /**   المحيط التوسع   */
        $print_footer["footer_tax_top"]  = $footerTemp->footer_tax_top;          /**          /**   المحيط التوسع   */
        $print_footer["footer_tax_bottom"]  = $footerTemp->footer_tax_bottom;          /**   المحيط التوسع   */
        $print_footer["footer_tax_letter"]  = $footerTemp->footer_tax_letter;    /**   أول حرف كبير   */
        $print_footer["footer_tax_position"]  = $footerTemp->footer_tax_position;     /**   مكان توضع العنصر  */
        $print_footer["footer_tax_padding_top"]  = $footerTemp->footer_tax_padding_top;        /**   محاذات العنصر العائم  */
        $print_footer["footer_tax_padding_bottom"]  = $footerTemp->footer_tax_padding_bottom;          /**   محاذات العنصر العائم  */
        $print_footer["footer_tax_padding_left"]  = $footerTemp->footer_tax_padding_left;         /**   محاذات العنصر العائم  */
        $print_footer["footer_tax_padding_right"]  = $footerTemp->footer_tax_padding_right;          /**   محاذات العنصر العائم  */
        // address
        $print_footer["footer_address_align"]  = $footerTemp->footer_address_align;        /**   محاذات العناون*/
        $print_footer["footer_address_font_size"]  = $footerTemp->footer_address_font_size;         /**   خط العنوان */
        $print_footer["footer_address_width"]  = $footerTemp->footer_address_width;         /**   عرض الإطار العنوان  */
        $print_footer["footer_address_border_width"]  = $footerTemp->footer_address_border_width;          /**   عرض الإطار العنوان  */
        $print_footer["footer_address_border_style"]  = $footerTemp->footer_address_border_style;        /**   شكل إطار العنوان  */
        $print_footer["footer_address_border_color"]  = $footerTemp->footer_address_border_color;        /**   لون إطار العنوان  */
        $print_footer["footer_address_right"]  = $footerTemp->footer_address_right;          /**   المحيط التوسع  */
        $print_footer["footer_address_left"]  = $footerTemp->footer_address_left;          /**   المحيط التوسع   */
        $print_footer["footer_address_top"]  = $footerTemp->footer_address_top;          /**   المحيط التوسع   */
        $print_footer["footer_address_bottom"]  = $footerTemp->footer_address_bottom;          /**   المحيط التوسع   */
        $print_footer["footer_address_letter"]  = $footerTemp->footer_address_letter;    /**   أول حرف كبير   */
        $print_footer["footer_address_position"]  = $footerTemp->footer_address_position;     /**   مكان توضع العنصر  */
        $print_footer["footer_address_padding_top"]  = $footerTemp->footer_address_padding_top;        /**   محاذات العنصر العائم  */
        $print_footer["footer_address_padding_bottom"]  = $footerTemp->footer_address_padding_bottom;          /**   محاذات العنصر العائم  */
        $print_footer["footer_address_padding_left"]  = $footerTemp->footer_address_padding_left;         /**   محاذات العنصر العائم  */
        $print_footer["footer_address_padding_right"]  = $footerTemp->footer_address_padding_right;          /**   محاذات العنصر العائم  */
        // bill name
        $print_footer["footer_bill_align"]  = $footerTemp->footer_bill_align;         /**   محاذات العناون*/
        $print_footer["footer_bill_font_size"]  = $footerTemp->footer_bill_font_size;         /**   خط العنوان */
        $print_footer["footer_bill_width"]  = $footerTemp->footer_bill_width;           /**   عرض الإطار العنوان  */
        $print_footer["footer_bill_border_width"]  = $footerTemp->footer_bill_border_width;          /**   عرض الإطار العنوان  */
        $print_footer["footer_bill_border_style"]  = $footerTemp->footer_bill_border_style;        /**   شكل إطار العنوان  */
        $print_footer["footer_bill_border_color"]  = $footerTemp->footer_bill_border_color;        /**   لون إطار العنوان  */
        $print_footer["footer_bill_right"]  = $footerTemp->footer_bill_right;          /**   المحيط التوسع  */
        $print_footer["footer_bill_left"]  = $footerTemp->footer_bill_left;          /**   المحيط التوسع   */
        $print_footer["footer_bill_top"]  = $footerTemp->footer_bill_top;          /**   المحيط التوسع   */
        $print_footer["footer_bill_bottom"]  = $footerTemp->footer_bill_bottom;          /**   المحيط التوسع   */
        $print_footer["footer_bill_letter"]  = $footerTemp->footer_bill_letter;    /**   أول حرف كبير   */
        $print_footer["footer_bill_position"]  = $footerTemp->footer_bill_position;     /**   مكان توضع العنصر  */
        $print_footer["footer_bill_padding_top"]  = $footerTemp->footer_bill_padding_top;         /**   محاذات العنصر العائم  */
        $print_footer["footer_bill_padding_bottom"]  = $footerTemp->footer_bill_padding_bottom;          /**   محاذات العنصر العائم  */
        $print_footer["footer_bill_padding_left"]  = $footerTemp->footer_bill_padding_left;         /**   محاذات العنصر العائم  */
        $print_footer["footer_bill_padding_right"]  = $footerTemp->footer_bill_padding_right;          /**   محاذات العنصر العائم  */
        // image 
        $print_footer["align_image_footer"]  = $footerTemp->align_image_footer;  
        $print_footer["position_img_footer"]  = $footerTemp->position_img_footer;  
        $print_footer["footer_image_width"]  = $footerTemp->footer_image_width;
        $print_footer["footer_image_height"]  = $footerTemp->footer_image_height;
        $print_footer["footer_image_border_width"]  = $footerTemp->footer_image_border_width;          /**   عرض الإطار العنوان  */
        $print_footer["footer_box_image_background"]  = $footerTemp->footer_box_image_background;       /**   عرض الإطار العنوان  */
        $print_footer["footer_image_border_style"]  = $footerTemp->footer_image_border_style;        /**   شكل إطار العنوان  */
        $print_footer["footer_image_border_color"]  = $footerTemp->footer_image_border_color;        /**   لون إطار العنوان  */
        $print_footer["footer_image_border_radius"]  = $footerTemp->footer_image_border_radius;         /**   لون إطار العنوان  */
        $print_footer["footer_box_image_color"]  = $footerTemp->footer_box_image_color;
        $print_footer["footer_image_box_height"]  = $footerTemp->footer_image_box_height;
        // image box
        $print_footer["footer_image_view"]  = ($footerTemp->footer_image_view == 1)?true:false; 
        $print_footer["footer_image_view"]  = ($footerTemp->footer_image_view == 1)?true:false; 
        $print_footer["position_box_footer_align"]  = $footerTemp->position_box_footer_align;
        $print_footer["footer_image_box_width"]  = $footerTemp->footer_image_box_width;                                /**   لون إطار العنوان  */
        $print_footer["footer_image_box_margin"]  = $footerTemp->footer_image_box_margin;                             /**   لون إطار العنوان  */
        $print_footer["footer_image_box_border_width"]  = $footerTemp->footer_image_box_border_width;         /**   عرض الإطار العنوان  */
        $print_footer["footer_image_box_border_style"]  = $footerTemp->footer_image_box_border_style;       /**   شكل إطار العنوان  */
        $print_footer["footer_image_box_border_color"]  = $footerTemp->footer_image_box_border_color;        /**   لون إطار العنوان  */
        $print_footer["footer_image_box_border_radius"]  = $footerTemp->footer_image_box_border_radius;                   /**   لون إطار العنوان  */
        $print_footer["footer_image_box_background"]  = $footerTemp->footer_image_box_background;                      /**   عرض الإطار العنوان  */
        // header box
        $print_footer["footer_view"]                = ($footerTemp->footer_view == 1)?true:false; 
        $print_footer["footer_box_width"]           = $footerTemp->footer_box_width;         /**   لون إطار العنوان  */
        $print_footer["footer_box_border_width"]    = $footerTemp->footer_box_border_width;          /**   عرض الإطار العنوان  */
        $print_footer["footer_box_border_style"]    = $footerTemp->footer_box_border_style;        /**   شكل إطار العنوان  */
        $print_footer["footer_box_border_color"]    = $footerTemp->footer_box_border_color;        /**   لون إطار العنوان  */
        $print_footer["footer_box_border_radius"]   = $footerTemp->footer_box_border_radius;         /**   لون إطار العنوان  */
        $print_footer["footer_box_background"]      = $footerTemp->footer_box_background;       /**   عرض الإطار العنوان  */
        // other box
        $print_footer["footer_other_view"]          = ($footerTemp->footer_other_view == 1)?true:false;          /**   لون إطار العنوان  */
        $print_footer["footer_other_width"]         = $footerTemp->footer_other_width;  /**  لون إطار العنوان  */
        $print_footer["footer_other_border_width"]  = $footerTemp->footer_other_border_width; /**  عرض الإطار العنوان  */
        $print_footer["footer_other_border_style"]  = $footerTemp->footer_other_border_style;        /**   شكل إطار العنوان  */
        $print_footer["footer_other_border_color"]  = $footerTemp->footer_other_border_color;        /**   لون إطار العنوان  */
        $print_footer["footer_other_border_radius"] = $footerTemp->footer_other_border_radius;         /** إطار العنوان  */
        $print_footer["other_background_footer"]    = $footerTemp->other_background_footer;       /**   عرض الإطار العنوان  */
    
    
        // rows lines 
        $print_footer["footer_line_view"]           = ($footerTemp->footer_line_view == 1)?true:false;
        $print_footer["footer_line_width"]          = $footerTemp->footer_line_width;
        $print_footer["footer_line_height"]         = $footerTemp->footer_line_height;
        $print_footer["footer_line_color"]          = $footerTemp->footer_line_color;
        $print_footer["footer_line_radius"]         = $footerTemp->footer_line_radius;
        $print_footer["footer_line_border_width"]   = $footerTemp->footer_line_border_width;
        $print_footer["footer_line_border_style"]   = $footerTemp->footer_line_border_style;
        $print_footer["footer_line_border_color"]   = $footerTemp->footer_line_border_color;
        $print_footer["footer_line_margin_top"]     = $footerTemp->footer_line_margin_top;
        $print_footer["footer_line_margin_bottom"]  = $footerTemp->footer_line_margin_bottom;  
        // .................................content
   
        $print_footer["left_footer_title"]  = $containTemp->left_footer_title  ;
        $print_footer["center_top_footer_title"]  = $containTemp->center_top_footer_title;
        $print_footer["center_middle_footer_title"]  = $containTemp->center_middle_footer_title;
        $print_footer["center_last_footer_title"]  = $containTemp->center_last_footer_title;
        
        if(!$containTemp->image_url){
            $print_footer["footer_image_url"]    =    "https://thumbs.dreamstime.com/b/invoice-linear-icon-modern-outline-invoice-logo-concept-whit-invoice-linear-icon-modern-outline-invoice-logo-concept-white-133517211.jpg";
        }else{
            $footer_path =  $containTemp->footer_image_path_second ;
            if($footer_path){
                $footer_da   = file_get_contents($footer_path);
                $footer_type = pathinfo($path,PATHINFO_EXTENSION);
                $footer_pic  = 'data:image/' . $footer_type . ';base64,' . base64_encode($footer_da);
            }else{
                $footer_pic  = '';
            }
            $print_footer["footer_image_url"]                     =    $footer_pic;
        
        }


            $print_content["choose_product_description"]    = $contentTemp->choose_product_description;
            $print_content["invoice_no"]    = $contentTemp->invoice_no;
            $print_content["project_no"]    = $contentTemp->project_no;
            $print_content["customer_no"]   = $contentTemp->customer_name;
            $print_content["date_name"]     = $contentTemp->date_name;
            $print_content["address_name"]  = $contentTemp->address_name;
            $print_content["mobile_name"]   = $contentTemp->mobile_name;
            $print_content["tax_name"]      = $contentTemp->tax_name;
            // table 
            $print_content["page_number_view"] = ( $footerTemp->page_number_view === 1)?true:false;
            $print_content["content_width"] = $contentTemp->content_width;
            $print_content["repeat_content_top"] = $contentTemp->repeat_content_top;
            $print_content["content_table_border_radius"] = $contentTemp->content_table_border_radius;
            $print_content["content_table_width"] = $contentTemp->content_table_width;
            $print_content["content_table_th_font_size"] = $contentTemp->content_table_th_font_size;
            $print_content["content_table_th_text_align"] = $contentTemp->content_table_th_text_align;
            $print_content["content_table_td_border_width"] = $contentTemp->content_table_td_border_width;
            $print_content["content_table_th_border_style"] = $contentTemp->content_table_th_border_style;
            $print_content["content_table_th_border_color"] = $contentTemp->content_table_th_border_color;
            $print_content["content_table_th_padding"] = $contentTemp->content_table_th_padding;
            $print_content["content_table_td_font_size"] = $contentTemp->content_table_td_font_size;
            $print_content["content_table_td_text_align"] = $contentTemp->content_table_td_text_align;
            $print_content["content_table_td_border_width"] = $contentTemp->content_table_td_border_width;
            $print_content["content_table_td_border_style"] = $contentTemp->content_table_td_border_style;
            $print_content["content_table_td_border_color"] = $contentTemp->content_table_td_border_color;
            $print_content["content_table_td_padding"] = $contentTemp->content_table_td_padding;
            $print_content["content_table_width_no"] = $contentTemp->content_table_width_no;
            $print_content["content_table_td_no_font_size"] = $contentTemp->content_table_td_no_font_size;
            $print_content["content_table_td_no_text_align"] = $contentTemp->content_table_td_no_text_align;
            $print_content["content_table_font_weight_no"] = $contentTemp->content_table_font_weight_no;
            $print_content["content_table_width_name"] = $contentTemp->content_table_width_name;
            $print_content["content_table_font_size_name"] = $contentTemp->content_table_font_size_name;
            $print_content["content_table_font_weight_name"] = $contentTemp->content_table_font_weight_name;
            $print_content["content_table_text_align_name"] = $contentTemp->content_table_text_align_name;
            
            $print_content["content_table_width_code"] = $contentTemp->content_table_width_code;
            $print_content["content_table_td_code_font_size"] = $contentTemp->content_table_td_code_font_size;
            $print_content["content_table_td_code_text_align"] = $contentTemp->content_table_td_code_text_align;
            $print_content["content_table_font_weight_code"] = $contentTemp->content_table_font_weight_code;
            
            $print_content["content_table_width_img"] = $contentTemp->content_table_width_img;
            $print_content["content_table_td_img_font_size"] = $contentTemp->content_table_td_img_font_size;
            $print_content["content_table_td_img_text_align"] = $contentTemp->content_table_td_img_text_align;
            $print_content["content_table_font_weight_img"] = $contentTemp->content_table_font_weight_img;
            
            $print_content["content_table_width_qty"] = $contentTemp->content_table_width_qty;
            $print_content["content_table_td_qty_font_size"] = $contentTemp->content_table_td_qty_font_size;
            $print_content["content_table_td_qty_text_align"] = $contentTemp->content_table_td_qty_text_align;
            $print_content["content_table_font_weight_qty"] = $contentTemp->content_table_font_weight_qty;
            $print_content["content_table_width_price"] = $contentTemp->content_table_width_price;
            $print_content["content_table_td_price_font_size"] = $contentTemp->content_table_td_price_font_size;
            $print_content["content_table_td_price_text_align"] = $contentTemp->content_table_td_price_text_align;
            $print_content["content_table_font_weight_price"] = $contentTemp->content_table_font_weight_price;
            $print_content["content_table_width_price_bdi"] = $contentTemp->content_table_width_price_bdi;
            $print_content["content_table_td_price_bdi_font_size"] = $contentTemp->content_table_td_price_bdi_font_size;
            $print_content["content_table_td_price_bdi_text_align"] = $contentTemp->content_table_td_price_bdi_text_align;
            $print_content["content_table_font_weight_price_bdi"] = $contentTemp->content_table_font_weight_price_bdi;
            $print_content["content_table_width_discount"] = $contentTemp->content_table_width_discount;
            $print_content["content_table_td_discount_font_size"] = $contentTemp->content_table_td_discount_font_size;
            $print_content["content_table_td_discount_text_align"] = $contentTemp->content_table_td_discount_text_align;
            $print_content["content_table_font_weight_discount"] = $contentTemp->content_table_font_weight_discount;
            $print_content["content_table_width_price_adi"] = $contentTemp->content_table_width_price_adi;
            $print_content["content_table_td_price_adi_font_size"] = $contentTemp->content_table_td_price_adi_font_size;
            $print_content["content_table_td_price_adi_text_align"] = $contentTemp->content_table_td_price_adi_text_align;
            $print_content["content_table_font_weight_price_adi"] = $contentTemp->content_table_font_weight_price_adi;
            $print_content["content_table_width_price_ade"] = $contentTemp->content_table_width_price_ade;
            $print_content["content_table_td_price_ade_font_size"] = $contentTemp->content_table_td_price_ade_font_size;
            $print_content["content_table_td_price_ade_text_align"] = $contentTemp->content_table_td_price_ade_text_align;
            $print_content["content_table_font_weight_price_ade"] = $contentTemp->content_table_font_weight_price_ade;
            $print_content["content_table_width_subtotal"] = $contentTemp->content_table_width_subtotal ;
            $print_content["content_table_td_subtotal_font_size"] = $contentTemp->content_table_td_subtotal_font_size ;
            $print_content["content_table_td_subtotal_text_align"] = $contentTemp->content_table_td_subtotal_text_align ;
            $print_content["content_table_font_weight_subtotal"] = $contentTemp->content_table_font_weight_subtotal ;
            // top table
            $print_content["top_table_width"] = $contentTemp->top_table_width;
            $print_content["top_table_margin_bottom"] = $contentTemp->top_table_margin_bottom;
            $print_content["top_table_border_width"] = $contentTemp->top_table_border_width;
            $print_content["top_table_border_style"] = $contentTemp->top_table_border_style;
            $print_content["top_table_border_color"] = $contentTemp->top_table_border_color;
            $print_content["top_table_td_border_width"] = $contentTemp->top_table_td_border_width;
            $print_content["top_table_td_border_style"] = $contentTemp->top_table_td_border_style;
            $print_content["top_table_td_border_color"] = $contentTemp->top_table_td_border_color;
            // left top
            $print_content["left_top_table_width"] = $contentTemp->left_top_table_width;
            $print_content["left_top_table_text_align"] = $contentTemp->left_top_table_text_align;
            $print_content["left_top_table_font_size"] = $contentTemp->left_top_table_font_size;
            $print_content["left_bottom_table"]        = $contentTemp->left_bottom_table;
            $print_content["right_top_table"]        = $contentTemp->right_top_table;
            $print_content["left_top_table"]        = $contentTemp->left_top_table;
            // right top
            $print_content["right_top_table_width"] = $contentTemp->right_top_table_width ;
            $print_content["right_top_table_text_align"] = $contentTemp->right_top_table_text_align ;
            $print_content["right_top_table_font_size"] = $contentTemp->right_top_table_font_size ;
            // left bottom  
            $print_content["left_bottom_table_width"] = $contentTemp->left_bottom_table_width;
            $print_content["left_bottom_table_text_align"] = $contentTemp->left_bottom_table_text_align;
            $print_content["left_bottom_table_font_size"] = $contentTemp->left_bottom_table_font_size;
            $print_content["left_bottom_table_td_bor_width"] = $contentTemp->left_bottom_table_td_bor_width;
            $print_content["left_bottom_table_td_bor_style"] = $contentTemp->left_bottom_table_td_bor_style;
            $print_content["left_bottom_table_td_bor_color"] = $contentTemp->left_bottom_table_td_bor_color;
            // right bottom  
            $print_content["right_bottom_table_width"] = $contentTemp->right_bottom_table_width;
            $print_content["right_bottom_table_text_align"] = $contentTemp->right_bottom_table_text_align;
            $print_content["right_bottom_table_font_size"] = $contentTemp->right_bottom_table_font_size;
            $print_content["right_bottom_table_td_bor_width"] = $contentTemp->right_bottom_table_td_bor_width;
            $print_content["right_bottom_table_td_bor_style"] = $contentTemp->right_bottom_table_td_bor_style;
            $print_content["right_bottom_table_td_bor_color"] = $contentTemp->right_bottom_table_td_bor_color;
            // .......................................................
            $print_content["bill_table_info_width"] = $contentTemp->bill_table_info_width;
            $print_content["bill_table_border_width"] = $contentTemp->bill_table_border_width;
            $print_content["bill_table_border_style"] = $contentTemp->bill_table_border_style;
            $print_content["bill_table_border_color"] = $contentTemp->bill_table_border_color;
            $print_content["bill_table_margin_bottom"] = $contentTemp->bill_table_margin_bottom;
            $print_content["bill_table_margin_top"] = $contentTemp->bill_table_margin_top;
            $print_content["bill_table_border_width"] = $contentTemp->bill_table_border_width;
            $print_content["bill_table_border_style"] = $contentTemp->bill_table_border_style;
            $print_content["bill_table_border_color"] = $contentTemp->bill_table_border_color;
            $print_content["bill_table_left_td_width"] = $contentTemp->bill_table_left_td_width;
            $print_content["bill_table_left_td_font_size"] = $contentTemp->bill_table_left_td_font_size;
            $print_content["bill_table_left_td_weight"] = $contentTemp->bill_table_left_td_weight;
            $print_content["bill_table_left_td_text_align"] = $contentTemp->bill_table_left_td_text_align;
            $print_content["bill_table_left_td_border_width"] = $contentTemp->bill_table_left_td_border_width;
            $print_content["bill_table_left_td_border_style"] = $contentTemp->bill_table_left_td_border_style;
            $print_content["bill_table_left_td_border_color"] = $contentTemp->bill_table_left_td_border_color;
            $print_content["bill_table_left_td_padding_left"] = $contentTemp->bill_table_left_td_padding_left;
            $print_content["bill_table_right_td_width"] = $contentTemp->bill_table_right_td_width;
            $print_content["bill_table_right_td_font_size"] = $contentTemp->bill_table_right_td_font_size;
            $print_content["bill_table_right_td_weight"] = $contentTemp->bill_table_right_td_weight;
            $print_content["bill_table_right_td_text_align"] = $contentTemp->bill_table_right_td_text_align;
            $print_content["bill_table_right_td_border_width"] = $contentTemp->bill_table_right_td_border_width;
            $print_content["bill_table_right_td_border_style"] = $contentTemp->bill_table_right_td_border_style;
            $print_content["bill_table_right_td_border_color"] = $contentTemp->bill_table_right_td_border_color;
            $print_content["bill_table_right_td_padding_left"] = $contentTemp->bill_table_right_td_padding_left;
            $print_content["line_bill_table_width"] = $contentTemp->line_bill_table_width;
            $print_content["line_bill_table_height"] = $contentTemp->line_bill_table_height;
            $print_content["line_bill_table_color"] = $contentTemp->line_bill_table_color;
            $print_content["line_bill_table_border_width"] = $contentTemp->line_bill_table_border_width;
            $print_content["line_bill_table_border_style"] = $contentTemp->line_bill_table_border_style;
            $print_content["line_bill_table_border_color"] = $contentTemp->line_bill_table_border_color;
            $print_content["line_bill_table_td_margin_left"] = $contentTemp->line_bill_table_td_margin_left;
           
               // display sections;
               $print_content["top_table_section"]  = ($contentTemp->top_table_section === 1)?true:false; 
               $print_content["content_table_section"]  = ($contentTemp->content_table_section === 1)?true:false; 
               $print_content["bottom_table_section"]  = ($contentTemp->bottom_table_section === 1)?true:false; 
               $print_content["footer_table"]  = ($contentTemp->footer_table === "true")?true:false; 
               $print_content["table_th_no"]  = ($contentTemp->table_th_no === "true")?true:false; 
               $print_content["table_th_code"]  = ($contentTemp->table_th_code === "true")?true:false;
               $print_content["table_th_name"]  = ($contentTemp->table_th_name === "true")?true:false;
               $print_content["table_th_img"]  = ($contentTemp->table_th_img === "true")?true:false;
               $print_content["table_th_qty"]  = ($contentTemp->table_th_qty === "true")?true:false; 
               $print_content["table_th_price"]  = ($contentTemp->table_th_price === "true")?true:false; 
               
               $print_content["table_th_price_bdi"]  = ($contentTemp->table_th_price_bdi === "true")?true:false; 
               $print_content["table_th_discount"]  = ($contentTemp->table_th_discount === "true")?true:false; 
               $print_content["table_th_price_ade"]  = ($contentTemp->table_th_price_ade === "true")?true:false; 
               $print_content["table_th_price_adi"]  = ($contentTemp->table_th_price_adi === "true")?true:false; 
               $print_content["table_th_subtotal"]  = ($contentTemp->table_th_subtotal === "true")?true:false; 
            
               // $font_invoice_info   = 
               $print_content["left_invoice_info"] = $contentTemp->left_invoice_info;
               $print_content["color_invoice_info"] = $contentTemp->color_invoice_info;
               $print_content["right_invoice_info"] = $contentTemp->right_invoice_info;
               $print_content["padding_invoice_info"] = $contentTemp->padding_invoice_info;
               $print_content["background_color_invoice_info"] = $contentTemp->background_color_invoice_info;
               // *********************************************** \\ 
               $left_size                                  =  $contentTemp->class_width_left;
               $right_size                                 =  $contentTemp->class_width_right;
               $print_content["class_width_left"]          = "col-md-".$left_size;
               $print_content["class_width_right"]         = "col-md-".$right_size;
               
               $print_content["bold_left_invoice_info"]  = $contentTemp->bold_left_invoice_info;
               $print_content["bold_left_invoice_info_br_width"]  = $contentTemp->bold_left_invoice_info_br_width;
               $print_content["bold_left_invoice_info_br_style"]  = $contentTemp->bold_left_invoice_info_br_style;
               $print_content["bold_left_invoice_info_br_color"]  = $contentTemp->bold_left_invoice_info_br_color;
               $print_content["bold_left_invoice_info_text_align"]  = $contentTemp->bold_left_invoice_info_text_align;
                                   /* */
               $left_left_size                             = $contentTemp->class_width_left_right;
               $left_right_size                            = $contentTemp->class_width_right_right;
               $print_content["class_width_left_right"]    = "col-md-". $left_left_size ;
               $print_content["class_width_right_right"]   = "col-md-". $left_right_size ;
               $print_content["bold_right_invoice_info"]   = $contentTemp->bold_right_invoice_info;
               $print_content["bold_right_invoice_info_br_width"]   = $contentTemp->bold_right_invoice_info_br_width;
               $print_content["bold_right_invoice_info_br_style"]   = $contentTemp->bold_right_invoice_info_br_style;
               $print_content["bold_right_invoice_info_br_color"]   = $contentTemp->bold_right_invoice_info_br_color;
               $print_content["bold_right_invoice_info_text_align"]   = $contentTemp->bold_right_invoice_info_text_align;
               // *********************************************** \\
                   
               $print_content["left_top_content"] =  $containTemp->left_top_content ;
               $print_content["right_top_content"] =  $containTemp->right_top_content ;
               $print_content["bottom_content"] =  $containTemp->bottom_content ;
               $print_content["table_th_no_named"] =  $contentTemp->table_th_no_named ;
               $print_content["table_th_code_named"] =  $contentTemp->table_th_code_named ;
               $print_content["table_th_name_named"] =  $contentTemp->table_th_name_named ;
               $print_content["table_th_img_named"] =  $contentTemp->table_th_img_named ;
               $print_content["table_th_qty_named"] =  $contentTemp->table_th_qty_named ;
               $print_content["table_th_price_named"] =  $contentTemp->table_th_price_named ;
               $print_content["table_th_price_bdi_named"] =  $contentTemp->table_th_price_bdi_named ;
               $print_content["table_th_discount_named"] =  $contentTemp->table_th_discount_named ;
               $print_content["table_th_price_ade_named"] =  $contentTemp->table_th_price_ade_named ;
               $print_content["table_th_price_adi_named"] =  $contentTemp->table_th_price_adi_named ;
               $print_content["table_th_subtotal_named"] =  $contentTemp->table_th_subtotal_named ;
               $print_content["currency_in_row"] =  $contentTemp->currency_in_row; 
               $print_content["if_discount_zero"] =  $contentTemp->if_discount_zero;
               $print_content["bill_invoice_info_down_vat"] =  $contentTemp->bill_invoice_info_down_vat ; 
               $print_content["bill_invoice_info_down_subtotal"] =  $contentTemp->bill_invoice_info_down_subtotal ; 
               $print_content["bill_invoice_info_down_discount"] =  $contentTemp->bill_invoice_info_down_discount ; 
               $print_content["bill_invoice_info_down_subtotal_after_dis"] =  $contentTemp->bill_invoice_info_down_subtotal_after_dis ; 
               $print_content["bold_left_invoice_info_customer_number"] =  $contentTemp->bold_left_invoice_info_customer_number  ; 
               $print_content["bold_left_invoice_info_customer_address"] =  $contentTemp->bold_left_invoice_info_customer_address  ; 
               $print_content["bold_left_invoice_info_customer_mobile"] =  $contentTemp->bold_left_invoice_info_customer_mobile ; 
               $print_content["bold_left_invoice_info_customer_tax"] =  $contentTemp->bold_left_invoice_info_customer_tax  ; 
               $print_content["bold_left_invoice_info_number"] =  $contentTemp->bold_left_invoice_info_number  ; 
               $print_content["bold_left_invoice_info_project"] =  $contentTemp->bold_left_invoice_info_project ; 
               $print_content["bold_left_invoice_info_date"] =  $contentTemp->bold_left_invoice_info_date ; 
               
               $print_content["margin_top_page"]  =  $containTemp->margin_top_page ; 
               $print_content["margin_bottom_page"] =  $containTemp->margin_bottom_page ;

               $print_content["show_quotation_terms"]  =  $contentTemp->show_quotation_terms ; 
               $print_content["show_customer_signature"] =  $contentTemp->show_customer_signature ; 
              
               $print_content["body_content_top"] =  $containTemp->body_content_top ; 
               $print_content["body_content_margin_left"] =  $containTemp->body_content_margin_left ; 
               $print_content["body_content_margin_right"] =  $containTemp->body_content_margin_right ; 
               $print_content["body_content_margin_bottom"] =  $containTemp->body_content_margin_bottom ; 
           
        $data = [
            'template'        => $template,
            'print'           => $print,
            'print_content'   => $print_content,
            'print_footer'    => $print_footer,
            'contentTemp'     => $contentTemp,
            'footerTemp'      => $footerTemp,
            'containTemp'     => $containTemp,
            'transaction'     => $transaction,
        ];
        //  ***  old way without arabic 
        // $pdf = DomPDF::setPaper('letter') 
        //         ->setOption('enable-javascript', true)
        //         ->setOption('javascript-delay', 1000)
        //         ->setOptions( ['isHtml5ParserEnabled' => true , 'isRemoteEnabled' => true])
        //         ->setOption('no-stop-slow-scripts', true)
        //         ->setOption('window-status', 'ready')->loadView('printer.setting', $data);
        $pdf  = $this->definePdf($data,$template);
        
        // Save the PDF to a temporary file to count pages
        $tempFilePath = storage_path('app/temp.pdf');
        $pdf->save($tempFilePath);

        // Step 2: Count the number of pages
        $totalPages = $this->countPdfPages($tempFilePath);

        // Delete the temporary PDF file after counting pages
        unlink($tempFilePath);

        // Step 3: Generate the final PDF with total pages included
        $data['totalPages'] = $totalPages;

        $pdf  = $this->definePdf($data,$template);

         

        return $pdf->stream('report.pdf');
    }
    private function countPdfPages($filePath)
    {
        // Use a regex to count the number of pages in the PDF
        $pdfText = file_get_contents($filePath);
        $pages = preg_match_all("/\/Page\W/", $pdfText, $matches);

        return $pages;
    }
    public function definePdf($data,$template){
        $html   = view('printer.setting', $data)->render();
        $arabic = new Arabic();
        $p      = $arabic->arIdentify($html);
        
        for ($i = count($p)-1; $i >= 0; $i-=2) {
            $utf8ar = $arabic->utf8Glyphs(substr($html, $p[$i-1], $p[$i] - $p[$i-1]));
            $html = substr_replace($html, $utf8ar, $p[$i-1], $p[$i] - $p[$i-1]);
        }
        $pdf =  DOMPDF::loadHTML($html);
  
        $page_size = "a4";
        switch($template->page_size){
        case 0: $page_size = "a4"; break;
        case 1: $page_size = "letter"; break;
        default : $page_size = "a4"; break; }
        
        $pdf->setPaper($page_size)
            ->setOption('enable-javascript', true)
            ->setOption('javascript-delay', 1000)
            ->setOptions( ['isHtml5ParserEnabled' => true , 'isRemoteEnabled' => true])
            ->setOption('no-stop-slow-scripts', true) 
            ->setOption('window-status', 'ready');
        return $pdf;
    }

    public function index(){

        if(request()->ajax()){
            $allData = PrinterTemplate::get();
            return Datatables::of($allData)
            ->addColumn('action', function ($row) {
                $html = '<div class="btn-group">
                <button type="button" class="btn btn-info dropdown-toggle btn-xs" 
                data-toggle="dropdown" aria-expanded="false">' .
                __("messages.actions") .
                '<span class="caret"></span><span class="sr-only">Toggle Dropdown
                </span>
                </button> 
                        <ul class="dropdown-menu dropdown-menu-left" role="menu">';
                $html .= '<li><a  href="' . action('Report\PrinterSettingController@edit', ["id" => $row->id ]) . '" class="" ><i class="fas fa-edit" aria-hidden="true"></i> ' . __("messages.edit") . '</a></li>';
                $html .= '<li><a  data-href="' . action('Report\PrinterSettingController@destroy', ["id" => $row->id ]) . '" onclick="delete_module($(this));" class="deleted_module" ><i class="fas fa-trash" aria-hidden="true"></i> ' . __("messages.delete") . '</a></li>';
                $html .=  '</ul></div>';
                return $html;
            })->editColumn("created_at",function($row){
                return $row->created_at->format("Y-m-d h:s:i a");
            })
            ->rawColumns(["action"])
            ->make(true);
        }
        return view("printer.templates");
    }
    public function store(Request $request){
        $data = $request;
        try{
            \DB::beginTransaction();
           
            PrinterTemplate::storeTemplate($data,$request);  
            \DB::commit();   
            $outPut = [
                "success"=>1,  
                "msg"    =>__("messages.added_successfull"),  
            ];
        }catch(Exception $e){
            $outPut = [
                "success"=>0,  
                "msg"    =>__("messages.something_went_wrong"),  
            ];
        }
        return redirect("/printer/settings/list");
    }
    public function update(Request $request,$id){
        $data = $request;
        try{
            \DB::beginTransaction();
       
            PrinterTemplate::updateTemplate($data,$id,$request);  
            \DB::commit();   
            $outPut = [
                "success"=>1,  
                "msg"    =>__("messages.updated_successfull"),  
            ];
        }catch(Exception $e){
            $outPut = [
                "success"=>0,  
                "msg"    =>__("messages.something_went_wrong"),  
            ];
        }
        return redirect("/printer/settings/list");
    }
    public function destroy(Request $request,$id){
        try{
            if(request()->ajax()){
               
                \DB::beginTransaction();
                PrinterTemplate::destroyTemplate($id,$request);  
                \DB::commit();   
                $outPut = [
                    "success" => true,  
                    "msg"     => __("messages.deleted_successfull"),  
                ];
                return $outPut;
            }
        }catch(Exception $e){
            $outPut = [
                "success"=> false,  
                "msg"    => __("messages.something_went_wrong"),  
            ];
            return $outPut;
        }
    }

    public function edit($id){
        $patterns    = [];
        $layouts     = [];$terms       = [];
        $business_id = request()->session()->get("user.business_id");
        $allPattern  = \App\Models\Pattern::where("business_id",$business_id)->get();
        foreach($allPattern as $i){ $patterns[$i->id] = $i->name; }
        $edit_type                  = $id;
        $PrinterTemplate            = PrinterTemplate::find($id);
        $PrinterTemplateContain     = PrinterTemplateContain::where("printer_templates_id",$id)->first();
       
        if($PrinterTemplate){
            $PrinterContentTemplate = PrinterContentTemplate::where("printer_template_id",$id)->first();
            $PrinterFooterTemplate  = PrinterFooterTemplate::where("printer_template_id",$id)->first();
        }else{
            $PrinterContentTemplate = null;
            $PrinterFooterTemplate  = null ;
        }
        $allTerms    = \App\Models\QuatationTerm::where("business_id",$business_id)->get();
        $allLayout   = \App\InvoiceLayout::where("business_id",$business_id)->get();
        foreach($allLayout  as $i){ $layouts[$i->id]  = $i->name; }
        foreach($allTerms as $i){ $terms[$i->id] = $i->name; }
        return view('printer.setting_printer')->with(compact(["edit_type","PrinterTemplate","PrinterTemplateContain","PrinterContentTemplate","PrinterFooterTemplate","patterns","layouts","terms"]));
    }
    public function printer_setting(){
        $patterns    = [];
        $layouts     = [];
        $terms       = [];
        $business_id = request()->session()->get("user.business_id");
        $allPattern  = \App\Models\Pattern::where("business_id",$business_id)->get();
        $allLayout   = \App\InvoiceLayout::where("business_id",$business_id)->get();
        $allTerms    = \App\Models\QuatationTerm::where("business_id",$business_id)->get();
        foreach($allLayout  as $i){ $layouts[$i->id]  = $i->name; }
        foreach($allPattern as $i){ $patterns[$i->id] = $i->name; }
        foreach($allTerms as $i){ $terms[$i->id] = $i->name; }
       
        return view('printer.setting_printer')->with(compact(["patterns","layouts","terms"]));
    }
    public function header_style(){
         
        return view('printer.header.header_style');
    }
    public function footer_style(){
         
        return view('printer.header.footer_style');
    }
    public function body_style(){
         
        return view('printer.header.body_style');
    }
    public function header_content(){
         
        return view('printer.header.header_style');
    }
  
}
