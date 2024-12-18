<?php

namespace App\Models\FrontEnd\Products;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\FrontEnd\Utils\GlobalUtil;

class Category extends Model
{
    use HasFactory,SoftDeletes;
    // *** REACT FRONT-END CATEGORY *** // 
    // **1** ALL CATEGORY F
    public static function getCategory($user) {
        try{
            $list        = [];
            $business_id = $user->business_id;
            $category    = \App\Category::where("business_id",$business_id)->get();
            if(count($category)==0) { return false; }
            foreach($category as $i){ $list[] = $i; }
            $list = GlobalUtil::toTreeTable("category",$business_id);
            return $list;
        }catch(Exception $e){
            return false;
        }
    }
    // **2** ALL CATEGORY  Tree F
    public static function getCategoryTree($user) {
        try{
            $list        = [];
            $business_id = $user->business_id;
            $category    = \App\Category::where("business_id",$business_id)->get();
            if(count($category)==0) { return false; }
            foreach($category as $i){ $list[] = $i; }
            $list = GlobalUtil::toTree("category",$business_id);
            return $list;
        }catch(Exception $e){
            return false;
        }
    }
    // **3** CREATE CATEGORY F
    public static function createCategory($user,$data) {
        try{
            $business_id    = $user->business_id;
            $list           = []; 
            $line           = []; 
            $category       = \App\Category::where("business_id",$business_id)->where("parent_id",0)->get();
            if(count($category)==0) { return false; }
            foreach($category as $i){ $list[] = ["id"=>$i->id,"value"=>$i->name]; }
            $line["categories"] = $list;
            return $line;
        }catch(Exception $e){
            return false;
        }
    }
    // **4** EDIT CATEGORY F
    public static function editCategory($user,$data,$id) {
        try{
            $business_id             = $user->business_id;
            $category                = \App\Category::find($id);
            $list           = []; 
            $line           = []; 
            $categories       = \App\Category::where("business_id",$business_id)->where("parent_id",0)->get();
            if(count($categories)==0) { return false; }
            foreach($categories as $i){ $list[] = ["id"=>$i->id,"value"=>$i->name]; }
            if(!$category){ return false; }
            $line["info"]        = $category;
            $line["categories"]  = $list;
            return $line;
        }catch(Exception $e){
            return false;
        } 
    }
    // **5** STORE CATEGORY F
    public static function storeCategory($user,$data,$request) {
        try{
            \DB::beginTransaction();
            if(!empty($data["name"]) && $data["name"] != ""){
                if($data["parent_id"]!=0){
                    $old             = \App\Category::where("name",trim($data["name"]))->where("business_id",$data["business_id"])->where("parent_id",$data["parent_id"])->first();
                }else{
                    $old             = \App\Category::where("name",trim($data["name"]))->where("business_id",$data["business_id"])->where("parent_id",0)->first();
                }
                if($old){return false;}
            }
           
            $output              = Category::createNewCategory($user,$data,$request);
            if($output == false){ return false; } 
            \DB::commit();
            return $output;
        }catch(Exception $e){
            return false;
        }
    }
    // **6** UPDATE CATEGORY F
    public static function updateCategory($user,$data,$id,$request) {
        try{
            \DB::beginTransaction();
            if(!empty($data["name"]) && $data["name"] != ""){
                if($data["parent_id"]!=0){
                    $old             = \App\Category::where("name",trim($data["name"]))->where("id","!=",$id)->where("business_id",$data["business_id"])->where("parent_id",$data["parent_id"])->first();
                }else{
                    $old             = \App\Category::where("name",trim($data["name"]))->where("id","!=",$id)->where("business_id",$data["business_id"])->where("parent_id",0)->first();
                }
                if($old){return false;}
            }
            $output              = Category::updateOldCategory($user,$data,$id,$request);
            \DB::commit();
            return $output;
        }catch(Exception $e){
            return false;
        }
    }
    // **7** DELETE CATEGORY F
    public static function deleteCategory($user,$id) {
        try{
            \DB::beginTransaction();
            $business_id = $user->business_id;
            $category    = \App\Category::find($id);
            $categories  = \App\Category::where("parent_id",$id)->first();
            $product     = \App\Product::where("sub_category_id",$id)->whereOr("category_id",$id)->first();
            if(!$category ){ return false; }
            
            if(!empty($categories)){ 
                return "parent"; 
            }else if(!empty($product)){
                return "product";
            }else{
                $category->delete();
                \DB::commit();
                return "success";
            }
        }catch(Exception $e){
            return false;
        }
    }


    // ****** MAIN FUNCTIONS 
    // **1** CREATE CATEGORY
    public static function createNewCategory($user,$data,$request) {
       try{
            $business_id                      = $user->business_id;
            $category                         = new \App\Category();
            $category->business_id            = $business_id;
            $category->name                   = $data["name"];
            $category->short_code             = $data["short_code"];
            $category->parent_id              = $data["parent_id"]??0;  
            $category->created_by             = $data["created_by"];
            $category->woocommerce_cat_id     = $data["woocommerce_cat_id"];
            $category->category_type          = "product";
            $category->description            = $data["description"];
            $category->slug                   = $data["slug"];
            if($request->hasFile("image") != null || $request->hasFile("image") != false){
                $dir_name =  config('constants.product_img_path');
                if ($request->file("image")->getSize() <= config('constants.document_size_limit')) {
                    $new_file_name = time() . '_' . $request->file("image")->getClientOriginalName();
                    $data          = getimagesize($request->file("image"));
                    $width         = $data[0];
                    $height        = $data[1];
                    $half_width    = $width/2;
                    $half_height   = $height/2;
                    $imgs          = \Image::make($request->file("image"))->resize($half_width,$half_height); //$request->$file_name->storeAs($dir_name, $new_file_name)  ||\public_path($new_file_name)
                    if ($imgs->save(base_path("public/uploads/img/$new_file_name"),20)) {
                        $uploaded_file_name      = $new_file_name;
                        $category->image         = $uploaded_file_name;
                    }
                }
            }
            $category->save();
            return true; 
        }catch(Exception $e){
            return false;
        }
    }
    // **2** UPDATE CATEGORY
    public static function updateOldCategory($user,$data,$id,$request) {
       try{
            $business_id                      = $user->business_id;
            $category                         = \App\Category::find($id);
            $category->business_id            = $business_id;
            $category->name                   = $data["name"];
            $category->short_code             = $data["short_code"];
            $category->parent_id              = $data["parent_id"]??0;  
            $category->created_by             = $data["created_by"];
            $category->woocommerce_cat_id     = $data["woocommerce_cat_id"];
            $category->category_type          = "product";
            $category->description            = $data["description"];
            $category->slug                   = $data["slug"];
            if($request->hasFile("image") != null || $request->hasFile("image") != false){
                $dir_name =  config('constants.product_img_path');
                if ($request->file("image")->getSize() <= config('constants.document_size_limit')) {
                    $new_file_name = time() . '_' . $request->file("image")->getClientOriginalName();
                    $data          = getimagesize($request->file("image"));
                    $width         = $data[0];
                    $height        = $data[1];
                    $half_width    = $width/2;
                    $half_height   = $height/2;
                    $imgs          = \Image::make($request->file("image"))->resize($half_width,$half_height); //$request->$file_name->storeAs($dir_name, $new_file_name)  ||\public_path($new_file_name)
                    if ($imgs->save(base_path("public/uploads/img/$new_file_name"),20)) {
                        $uploaded_file_name      = $new_file_name;
                        $category->image         = $uploaded_file_name;
                    }
                }
            }
            $category->update();
            return true; 
        }catch(Exception $e){
            return false;
        }
    }

}
