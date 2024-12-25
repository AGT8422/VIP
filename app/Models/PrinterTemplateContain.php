<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Utils\ProductUtil;
class PrinterTemplateContain extends Model
{
    use HasFactory,SoftDeletes;

    /**
     * Get the Header image.
     *
     * @return string
     */
    public function getImageUrlAttribute()
    {
        $image_url ='';
        if (!empty($this->header_image)) {
            // $image_url = asset('public/uploads/img/' . rawurlencode($this->header_image));
            $company_name = request()->session()->get("user_main.domain");
            $image_url = asset('uploads/companies/'.$company_name.'/img/' . rawurlencode($this->header_image));
        } 
        return $image_url;
    }
    /**
    * Get the Header image path.
    *
    * @return string
    */
    public function getImagePathAttribute()
    {
        if (!empty($this->header_image)) {
            $company_name = request()->session()->get("user_main.domain");
            $image_path = public_path('uploads/companies/'.$company_name) . '/' . config('constants.product_img_path') . '/' . $this->header_image;
        } else {
            $image_path = null;
        }
        return $image_path;
    }/**
    * Get the Header image path.
    *
    * @return string
    */
    public function getImagePathSecondAttribute()
    {
        if (!empty($this->header_image)) {
            $company_name = request()->session()->get("user_main.domain");
            $image_path = public_path('/uploads/companies/'.$company_name) . '/' . config('constants.product_img_path') . '/' . $this->header_image;
        } else {
            $image_path = null;
        }
        return $image_path;
    }
    /**
     * Get the Footer image.
     *
     * @return string
     */
    public function getFooterImageUrlAttribute()
    {
        $image_url ='';
        if (!empty($this->footer_image)) {
            // $image_url = asset('public/uploads/img/' . rawurlencode($this->footer_image));
            $company_name = request()->session()->get("user_main.domain");
            $image_url = asset('uploads/companies/'.$company_name.'/img/' . rawurlencode($this->footer_image));
        } 
        return $image_url;
    }
    /**
    * Get the Footer image path.
    *
    * @return string
    */
    public function getFooterImagePathAttribute()
    {
        if (!empty($this->footer_image)) {
            $company_name = request()->session()->get("user_main.domain");
            $image_path = public_path('uploads/companies/'.$company_name) . '/' . config('constants.product_img_path') . '/' . $this->footer_image;
        } else {
            $image_path = null;
        }
        return $image_path;
    }
    /**
    * Get the Footer image path.
    *
    * @return string
    */
    public function getFooterImagePathSecondAttribute()
    {
        if (!empty($this->footer_image)) {
            $company_name = request()->session()->get("user_main.domain");
            $image_path = public_path('uploads/companies/'.$company_name) . '/' . config('constants.product_img_path') . '/' . $this->footer_image;
        } else {
            $image_path = null;
        }
        return $image_path;
    }


    // *1*
    public static function storeTemplateContain($data,$id,$request){
        // $data = $data->only([
        //     "left_header_id",
        //     "left_header",
        //     "center_top_header",
        //     "center_top_header_id",
        //     "center_middle_header",
        //     "center_middle_header_id",
        //     "center_last_header",
        //     "style_write_center_last_header_drop",
        //     "header_image",
        //     "left_footer",
        //     "left_footer_id",
        //     "center_top_footer",
        //     "center_top_footer_id",
        //     "center_middle_footer",
        //     "center_middle_footer_id",
        //     "center_last_footer",
        //     "style_write_center_last_footer_drop",
        //     "footer_image",
        //     "invoice_left_footer",
        //     "quotation_term",
        // ]);
        $printerTemplateContain                                  =  new PrinterTemplateContain();
        $printerTemplateContain->left_header_title               =  $data->left_header ;
        $printerTemplateContain->left_header_id                  =  $data->left_header_id ;
        $printerTemplateContain->center_top_header_title         =  $data->center_top_header ;
        $printerTemplateContain->center_top_header_id            =  $data->center_top_header_id ;
        $printerTemplateContain->center_middle_header_title      =  $data->center_middle_header  ;
        $printerTemplateContain->center_middle_header_id         =  $data->center_middle_header_id ;
        $printerTemplateContain->center_last_header_title        =  $data->center_last_header ;
        $printerTemplateContain->center_last_header_id           =  $data->style_write_center_last_header_drop ;
        $productUtil                                             =  new ProductUtil();
        
        if(  $request->hasFile('header_image')  ){
            $printerTemplateContain->header_image                =  $productUtil->uploadFile($request, 'header_image', config('constants.product_img_path'), 'image');   ;
        }
        
        $printerTemplateContain->left_footer_title               =  $data->left_footer ;
        $printerTemplateContain->left_footer_id                  =  $data->left_footer_id ;
        $printerTemplateContain->center_top_footer_title         =  $data->center_top_footer ;
        $printerTemplateContain->center_top_footer_id            =  $data->center_top_footer_id ;
        $printerTemplateContain->center_middle_footer_title      =  $data->center_middle_footer ;
        $printerTemplateContain->center_middle_footer_id         =  $data->center_middle_footer_id ;
        $printerTemplateContain->center_last_footer_title        =  $data->center_last_footer ;
        $printerTemplateContain->center_last_footer_id           =  $data->style_write_center_last_footer_drop ;
        
        if(  $request->hasFile('footer_image')  ){
            $printerTemplateContain->footer_image                =  $productUtil->uploadFile($request, 'footer_image', config('constants.product_img_path'), 'image');   ;
        }

        $printerTemplateContain->invoice_left_footer             =  $data->invoice_left_footer ;
        $printerTemplateContain->quotation_term                  =  $data->quotation_term ;
        $printerTemplateContain->printer_templates_id            =  $id ;
        $printerTemplateContain->left_header_radio               =  $data->left_header_radio ;
        $printerTemplateContain->center_top_header_radio         =  $data->center_top_header_radio ;
        $printerTemplateContain->center_middle_header_radio      =  $data->center_middle_header_radio ;
        $printerTemplateContain->center_last_header_radio        =  $data->center_last_header_radio ;
        $printerTemplateContain->left_top_content_radio          =  $data->left_top_content_radio ;
        $printerTemplateContain->right_top_content_radio         =  $data->right_top_content_radio ;
        $printerTemplateContain->bottom_content_radio            =  $data->bottom_content_radio ;
        $printerTemplateContain->left_footer_radio               =  $data->left_footer_radio ;
        $printerTemplateContain->left_top_content_id             =  $data->left_top_content_id ;
        $printerTemplateContain->right_top_content_id            =  $data->right_top_content_id ;
        $printerTemplateContain->bottom_content_id               =  $data->bottom_content_id ;
        $printerTemplateContain->left_top_content                =  $data->left_top_content ;
        $printerTemplateContain->right_top_content               =  $data->right_top_content ;
        $printerTemplateContain->bottom_content                  =  $data->bottom_content ;
        $printerTemplateContain->center_top_footer_radio         =  $data->center_top_footer_radio ;
        $printerTemplateContain->center_middle_footer_radio      =  $data->center_middle_footer_radio ;
        $printerTemplateContain->center_last_footer_radio        =  $data->center_last_footer_radio ;
        $printerTemplateContain->margin_top_page                 =  $data->margin_top_page ;
        $printerTemplateContain->margin_bottom_page              =  $data->margin_bottom_page ;
        $printerTemplateContain->body_content_top                =  $data->body_content_top ;
        $printerTemplateContain->body_content_margin_left        =  $data->body_content_margin_left ;
        $printerTemplateContain->body_content_margin_right       =  $data->body_content_margin_right ;
        $printerTemplateContain->body_content_margin_bottom      =  $data->body_content_margin_bottom ;
        $printerTemplateContain->created_by                      =  auth()->user()->id ;
        $printerTemplateContain->save();
    }
    // *2*
    public static function updateTemplateContain($data,$printerTemplateContain,$request){
        // $data = $data->only([
        //     "left_header_id",
        //     "left_header",
        //     "center_top_header",
        //     "center_top_header_id",
        //     "center_middle_header",
        //     "center_middle_header_id",
        //     "center_last_header",
        //     "style_write_center_last_header_drop",
        //     "header_image",
        //     "left_footer",
        //     "left_footer_id",
        //     "center_top_footer",
        //     "center_top_footer_id",
        //     "center_middle_footer",
        //     "center_middle_footer_id",
        //     "center_last_footer",
        //     "style_write_center_last_footer_drop",
        //     "footer_image",
        //     "invoice_left_footer",
        //     "quotation_term",
        // ]);
       
        if($printerTemplateContain != null || !empty($printerTemplateContain)){
            $printerTemplateContain->left_header_title               =  $data->left_header ;
            $printerTemplateContain->left_header_id                  =  $data->left_header_id ;
            $printerTemplateContain->center_top_header_title         =  $data->center_top_header ;
            $printerTemplateContain->center_top_header_id            =  $data->center_top_header_id ;
            $printerTemplateContain->center_middle_header_title      =  $data->center_middle_header  ;
            $printerTemplateContain->center_middle_header_id         =  $data->center_middle_header_id ;
            $printerTemplateContain->center_last_header_title        =  $data->center_last_header ;
            $printerTemplateContain->center_last_header_id           =  $data->style_write_center_last_header_drop ;
            if(isset($request->delete_header_image)){
                // unlink($printerTemplateContain->image_path_second);
                unlink($printerTemplateContain->image_path);
                $printerTemplateContain->header_image = null;
            }
            $productUtil                                         =  new ProductUtil();
            if(  $request->hasFile('header_image')  ){
                
                if(!empty($printerTemplateContain->image_path) && file_exists($printerTemplateContain->image_path)){
                    // unlink($printerTemplateContain->image_path_second);
                    unlink($printerTemplateContain->image_path);
                }
                $printerTemplateContain->header_image                =  $productUtil->uploadFile($request, 'header_image', config('constants.product_img_path'), 'image');   ;
            }
            $printerTemplateContain->left_footer_title               =  $data->left_footer ;
            $printerTemplateContain->left_footer_id                  =  $data->left_footer_id ;
            $printerTemplateContain->center_top_footer_title         =  $data->center_top_footer ;
            $printerTemplateContain->center_top_footer_id            =  $data->center_top_footer_id ;
            $printerTemplateContain->center_middle_footer_title      =  $data->center_middle_footer ;
            $printerTemplateContain->center_middle_footer_id         =  $data->center_middle_footer_id ;
            $printerTemplateContain->center_last_footer_title        =  $data->center_last_footer ;
            $printerTemplateContain->margin_top_page                 =  $data->margin_top_page ;
            $printerTemplateContain->margin_bottom_page              =  $data->margin_bottom_page ;
            $printerTemplateContain->center_last_footer_id           =  $data->style_write_center_last_footer_drop ;
            if(isset($request->delete_footer_image)){
                // unlink($printerTemplateContain->footer_image_path_second);
                unlink($printerTemplateContain->footer_image_path);
                $printerTemplateContain->footer_image = null;
            }
            if(  $request->hasFile('footer_image')  ){
                
                if(!empty($printerTemplateContain->footer_image_path) && file_exists($printerTemplateContain->footer_image_path)){
                    // unlink($printerTemplateContain->footer_image_path_second);
                    unlink($printerTemplateContain->footer_image_path);
                }
                $printerTemplateContain->footer_image                =  $productUtil->uploadFile($request, 'footer_image', config('constants.product_img_path'), 'image');   ;
            }
            $printerTemplateContain->invoice_left_footer             =  $data->invoice_left_footer ;
            $printerTemplateContain->quotation_term                  =  $data->quotation_term ;
            // $printerTemplateContain->printer_templates_id            =  $id ;
            // $printerTemplateContain->created_by                      =  auth()->user()->id ;
            $printerTemplateContain->left_header_radio               =  $data->left_header_radio ;
            $printerTemplateContain->center_top_header_radio         =  $data->center_top_header_radio ;
            $printerTemplateContain->center_middle_header_radio      =  $data->center_middle_header_radio ;
            $printerTemplateContain->center_last_header_radio        =  $data->center_last_header_radio ;
            $printerTemplateContain->left_top_content_radio          =  $data->left_top_content_radio ;
            $printerTemplateContain->right_top_content_radio         =  $data->right_top_content_radio ;
            $printerTemplateContain->bottom_content_radio            =  $data->bottom_content_radio ;
            $printerTemplateContain->left_top_content_id             =  $data->left_top_content_id ;
            $printerTemplateContain->right_top_content_id            =  $data->right_top_content_id ;
            $printerTemplateContain->bottom_content_id               =  $data->bottom_content_id ;
            $printerTemplateContain->left_top_content                =  $data->left_top_content ;
            $printerTemplateContain->right_top_content               =  $data->right_top_content ;
            $printerTemplateContain->bottom_content                  =  $data->bottom_content ;
            $printerTemplateContain->left_footer_radio               =  $data->left_footer_radio ;
            $printerTemplateContain->center_top_footer_radio         =  $data->center_top_footer_radio ;
            $printerTemplateContain->center_middle_footer_radio      =  $data->center_middle_footer_radio ;
            $printerTemplateContain->center_last_footer_radio        =  $data->center_last_footer_radio ;
            $printerTemplateContain->body_content_top                =  $data->body_content_top ;
            $printerTemplateContain->body_content_margin_left        =  $data->body_content_margin_left ;
            $printerTemplateContain->body_content_margin_right       =  $data->body_content_margin_right ;
            $printerTemplateContain->body_content_margin_bottom      =  $data->body_content_margin_bottom ;
            $printerTemplateContain->update();
        }
    }
}
