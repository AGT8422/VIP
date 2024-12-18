<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ecommerce extends Model
{
    use HasFactory;

    protected $appends = ["image_url","auth_url"];

     /**
     * Get the section image.
     *
     * @return string
     */
    public function getImageUrlAttribute()
    {
        $image_url ='';
        if (!empty($this->image)) {
            $image_url = asset('public/uploads/img/' . rawurlencode($this->image));
        } 
        return $image_url;
    }
    public function getAuthUrlAttribute()
    {
        $image_url ='';
        if (!empty($this->image)) {
            $image_url = asset('/public/uploads/img/' . rawurlencode($this->image));
        } 
        return $image_url;
    }
    // ** 1 **
    public static function saveRow($Data,$index,$array){
        
        if(count($array)>0){
            if(isset($array[$index])){
                        $item              =  Ecommerce::find($array[$index]);
                        $item->name        =  $Data["name"][$index]; 
                        $item->title       =  $Data["title"][$index]; 
                        $item->desc        =  $Data["description"][$index]; 
                        $item->button      =  $Data["button"][$index];
                      
                        if($Data->hasFile("image")  != null || $Data->hasFile("image") != false ){
                            $dir_name =  config('constants.product_img_path');
                            if ($Data->file("image")[$index]->getSize() <= config('constants.document_size_limit')) {
                                $new_file_name = time() . '_' . $Data->file("image")[$index]->getClientOriginalName();
                                if ($Data->file("image")[$index]->storeAs($dir_name, $new_file_name)) {
                                    $uploaded_file_name = $new_file_name;
                                    $item->image        = $uploaded_file_name; 
                                }
                            }
                        } 
                    $item->update();
            }else{
                    $item              = new Ecommerce(); 
                    $item->name        = $Data["name"][$index]; 
                    $item->title       = $Data["title"][$index]; 
                    $item->desc        = $Data["description"][$index]; 
                    $item->button      = $Data["button"][$index];
                    //   $item->view        = (isset($Data["view"][$index]))?$Data["view"][$index]:null; 
                    if($Data->hasFile("image") != null || $Data->hasFile("image") != false){
                        $dir_name =  config('constants.product_img_path');
                        if ($Data->file("image")[$index]->getSize() <= config('constants.document_size_limit')) {
                            $new_file_name = time() . '_' . $Data->file("image")[$index]->getClientOriginalName();
                            if ($Data->file("image")[$index]->storeAs($dir_name, $new_file_name)) {
                                $uploaded_file_name = $new_file_name;
                                $item->image        = $uploaded_file_name; 
                            }
                        }
                    } 
                    $item->save();
            }
          }else{
                $item              = new Ecommerce(); 
                $item->name        = $Data["name"][$index]; 
                $item->title       = $Data["title"][$index]; 
                $item->desc        = $Data["description"][$index]; 
                $item->button      = $Data["button"][$index];
                //   $item->view        = (isset($Data["view"][$index]))?$Data["view"][$index]:null; 
                if($Data->hasFile("image") != null || $Data->hasFile("image") != false){
                    $dir_name =  config('constants.product_img_path');
                    if ($Data->file("image")[$index]->getSize() <= config('constants.document_size_limit')) {
                        $new_file_name = time() . '_' . $Data->file("image")[$index]->getClientOriginalName();
                        if ($Data->file("image")[$index]->storeAs($dir_name, $new_file_name)) {
                            $uploaded_file_name = $new_file_name;
                            $item->image        = $uploaded_file_name; 
                        }
                    }
                } 
                $item->save();
            }
    }
    // ** 2 **
    public static function allData(){
           $item              = Ecommerce::get(); 
            
           return $item;
    }
    // ** 3 **
    public static function storeSection() {
        $storeSection = \App\Models\Ecommerce::where("store_page",1)->first();
        $data         = [];   
        if(!empty($storeSection)){
            $data[] = [
                    "Name"         =>  "first_section",
                    "title"        =>  $storeSection->title,
                    "description"  =>  $storeSection->desc,
                    "image"        =>  $storeSection->image_url
            ]; 
        }else{
            $data = ""; 
        }  
        return $data; 
    }
    // ** 4 **
    public static function typeProduct() {
        $data[] = [ 
                "id"   => "1", 
                "value" => "Single"
            ];
        $data[] = [ 
                "id"   => "2", 
                "value" => "Variable"
            ];
        $data[] = [ 
                "id"   => "3", 
                "value" => "Combo"
            ]; 
        $allData   = [
            "title"    => "Product Type",
            "name"     => "product_type",
            "options"  => $data
        ];
        return $allData; 
    }
    // ** 5 **
    public static function unitProduct() {
        $list  =  [] ;
        $unit  =  \App\Unit::whereNull("product_id")->get();
        foreach($unit as $i){
            $list[] = [ 
                "id"    => $i->id, 
                "value" => $i->actual_name 
            ]; 
        }       
        $allData   = [];
        $allData   = [
            "title"    => "Product Unit",
            "name"     => "product_unit",
            "options"  => $list
        ];  
        return $allData; 
    }
    // ** 6 **
    public static function subCategoryProduct($iid=null) {
        $list      =  [] ;
        if($iid ==null){
            $category  =  \App\Category::whereNotNull("parent_id")->where("parent_id","!=",0)->get();
        }else{
            $category  =  \App\Category::whereNotNull("parent_id")->whereIn("parent_id",$iid)->where("parent_id","!=",0)->get();
        }
        foreach($category as $i){
            $list[] = [
                "id"   => $i->id,
                "value" => $i->name
            ]; 
        }
        $allData   = [];
        $allData   = [
            "title"    => "Product Sub Category",
            "name"     => "product_sub_category",
            "options"  => $list
        ];         
        return $allData; 
    }
    // ** 7 **
    public static function categoryProduct() {
        $list  =  [] ;
        $category  =  \App\Category::where("parent_id",0)->get();
        foreach($category as $i){
            $list[] = [ 
                "id"   => $i->id, 
                "value" => $i->name 
            ]; 
        }  
        $allData   = [];
        $allData   = [
            "title"    => "Product Category",
            "name"     => "product_category",
            "options"  => $list
        ];        
        return $allData; 
    }
    // ** 8 **
    public static function brandProduct() {
        $list      =  [] ;
        $brand     =  \App\Brands::get();
        foreach($brand as $i){
            $list[] = [
                "id"   => $i->id,
                "value" => $i->name
            ]; 
        }   
        $allData   = [];
        $allData   = [
            "title"    => "Product Brand",
            "name"     => "product_brand",
            "options"  => $list
        ];      
        return $allData; 
    }
    // ** 9 **
    public static function collectionProduct() {
        $list           =  [] ;
        $collection     =  \App\Product::SelectAll();
        foreach($collection as $i){
            if($i->ecm_collection == 1){
                $prs                     = json_decode($i);
                $prs->sale_price         = round($prs->sale_price,2);
                if($i->product_type == "single"){
                    $productPrice                     = \App\Product::productPrice($i); 
                    $prs->price_before                = round($productPrice["before_price"],2);
                    $prs->price_after                 = round($productPrice["after_price"] ,2);
                    // if($productPrice["before_price"] != $productPrice["after_price"]){
                    //     $discount_pro[]      = $prs ;
                    // }
                }else if($i->product_type == "combo"){
                    $prs->sale_price   = ($alters["price"]!=null)?round($alters["price"],2):round($prs->sale_price,2);
                }
                $vedio                   = \App\Product::video($i);
                $alters                  = \App\Product::images($i);
                if($vedio != null){
                    $prs->vedios              = $vedio;
                }
                //..  product details for image and vedio
                if(count($alters["more_image"])>0){
                    $prs->alter_images            =   $alters["more_image"];
                }
                if(count($alters["more_image_items"])>0){
                    $prs->children                =   $alters["more_image_items"];
                }
                $wishlist = \App\Models\WishList::where("product_id",$prs->id)->first();
                if(!empty($wishlist)){
                    $prs->wishlist = true;
                }else{
                    $prs->wishlist = false;
                }
                if($i->ecm_collection == 1){
                    $list[]           = $prs ;
                }
            }
        }          
        return $list; 
    }
}
