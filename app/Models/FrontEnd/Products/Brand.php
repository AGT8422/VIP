<?php

namespace App\Models\FrontEnd\Products;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Brand extends Model
{
    use HasFactory,SoftDeletes;

     

    // *** REACT FRONT-END BRAND *** // 
    // **1** ALL BRAND F
    public static function getBrand($user) {
        try{
            $list        = [];
            $business_id = $user->business_id;
            $brand       = \App\Brands::where("business_id",$business_id)->get();
            if(count($brand)==0) { return false; }
            foreach($brand as $i){ 
                $user = \App\User::find($i->created_by);
                $list[] = [
                    "id"             => $i->id, 
                    "name"           => $i->name, 
                    "description"    => $i->description, 
                    "created_by"     => ($user)?$user->first_name:"",
                    "use_for_repair" => $i->use_for_repair,
                    "image"          => $i->image_url,
                ];
                
            }
            return $list;
        }catch(Exception $e){
            return false;
        }
    }
    // **2** CREATE BRAND F
    public static function createBrand($user,$data) {
        try{
            return true;
        }catch(Exception $e){
            return false;
        }
    }
    // **3** EDIT BRAND F
    public static function editBrand($user,$data,$id) {
        try{
            $business_id             = $user->business_id;
            $brand                   = \App\Brands::find($id);
            $list["info"]            = $brand;
            if(!$brand){ return false; }
            return $list;
        }catch(Exception $e){
            return false;
        } 
    }
    // **4** STORE BRAND F
    public static function storeBrand($user,$data,$request) {
        try{
            \DB::beginTransaction();
            if(!empty($data["name"]) && $data["name"] != ""){
                $old             = \App\Brands::where("name",$data["name"])->where("business_id",$data["business_id"])->first();
                if($old){return false;}
            }
            $output              = Brand::createNewBrand($user,$data,$request);
            if($output == false){ return false; } 
            \DB::commit();
            return $output;
        }catch(Exception $e){
            return false;
        }
    }
    // **5** UPDATE BRAND F
    public static function updateBrand($user,$data,$id,$request) {
        try{
            \DB::beginTransaction();
            if(!empty($data["name"]) && $data["name"] != ""){
                $old             = \App\Brands::where("name",$data["name"])->where("id","!=",$id)->where("business_id",$data["business_id"])->first();
                if($old){return false;}
            }
            $output              = Brand::updateOldBrand($user,$data,$id,$request);
            \DB::commit();
            return $output;
        }catch(Exception $e){
            return false;
        }
    }
    // **6** DELETE BRAND F
    public static function deleteBrand($user,$id) {
        try{
            \DB::beginTransaction();
            $business_id = $user->business_id;
            $brand       = \App\Brands::find($id);
            if(!$brand ){ return "no"; }
            $product     = \App\Product::where("brand_id",$id)->first();
            if(!$brand || !empty($product)){ return "false"; }
            $brand->delete();
            \DB::commit();
            return "true";
        }catch(Exception $e){
            return false;
        }
    }


    // ****** MAIN FUNCTIONS 
    // **1** CREATE BRAND F
    public static function createNewBrand($user,$data,$request) {
       try{
            $brand                   = new \App\Brands();
            $brand->business_id      = $data["business_id"];
            $brand->name             = $data["name"];
            $brand->description      = $data["description"];
            $brand->created_by       = $user->id;
            $brand->use_for_repair   = $data["use_for_repair"];
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
                        $brand->image            = $uploaded_file_name;
                    } 
                }
            }
            $brand->save();
            return true; 
        }catch(Exception $e){
            return false;
        }
    }
    // **2** UPDATE BRAND F
    public static function updateOldBrand($user,$data,$id,$request) {
       try{
            $brand                   = \App\Brands::find($id);
            $brand->business_id      = $data["business_id"];
            $brand->name             = $data["name"];
            $brand->description      = $data["description"];
            $brand->created_by       = $user->id;
            $brand->use_for_repair   = $data["use_for_repair"];
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
                        $brand->image            = $uploaded_file_name;
                    } 
                }
            }
            $brand->update();
            return true; 
        }catch(Exception $e){
            return false;
        }
    }


}
