<?php

namespace App\Models\Ecommerce;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    use HasFactory;
    protected $table = "ecommerces";
    protected $appends = ["image_url"];

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
    // **1** get all sections
    public static function getAllSections($data){
        if(count($data)>0){
            $client  = \App\Models\Ecommerce\Eadmin::where("api_token",$data["token"])->first();
            if(!$client){
                return response([
                    "status"   => 401 ,
                    "message" => __('Token Expire') ,
                ],401);
            }
            $list       = [] ;
            $list_about = [] ;
            $list_store = [] ;
            $list_home  = [] ;
            $all              = Section::where("about_us",0)->where("subscribe",0)->where("store_page",0)->where("topSection",0)->first();
            $home_section     = Section::where("about_us",0)->where("subscribe",0)->where("store_page",0)->where("topSection",1)->first();
            $list_home = [
                "id"            => $home_section->id,
                "name"          => $home_section->name,
                "title"         => $home_section->title,
                "description"   => $home_section->desc,
                "button"        => $home_section->button,
                "image"         => $home_section->image_url,
                "topSection"    => $home_section->topSection,
                "view"          => $home_section->view,
            ];
            $list = [
                "id"            => $all->id,
                "name"          => $all->name,
                "title"         => $all->title,
                "description"   => $all->desc,
                "button"        => $all->button,
                "image"         => $all->image_url,
                "topSection"    => $all->topSection,
                "view"          => $all->view,
            ];

            $all_about     = Section::where("about_us",1)->where("subscribe",0)->where("store_page",0)->get();
            foreach($all_about as $i){
                $list_about[] = [
                    "id"            => $i->id,
                    "name"          => $i->name,
                    "title"         => $i->title,
                    "description"   => $i->desc,
                    "button"        => $i->button,
                    "image"         => $i->image_url,
                    "topSection"    => $i->topSection,
                    "view"          => $i->view,
                ];
            }
            $all_store     = Section::where("about_us",0)->where("subscribe",0)->where("store_page",1)->where("topSection",1)->first();
            // foreach($all_store as $i){
                $list_store[] = [
                    "id"            => $all_store->id,
                    "name"          => $all_store->name,
                    "title"         => $all_store->title,
                    "description"   => $all_store->desc,
                    "button"        => $all_store->button,
                    "image"         => $all_store->image_url,
                    "topSection"    => $all_store->topSection,
                    "view"          => $all_store->view,
                ];
            // }
            return $output = [
                    "status"                => 200,
                    "top_section_home"      => $home_section,
                    "bottem_section_home"   => $list,
                    "about_us"              => $list_about,
                    "top_section_store"     => $list_store,
                    "message"               => __("Access Sections Successfully")    
            ] ;
        }else{
            return response([
                "status"   => 403 ,
                "message" => __('Invalid Data') ,
            ],403);
        }
    }
    // **2** update all sections
    public static function editAllSections($data){
        if(count($data)>0){
            $client  = \App\Models\Ecommerce\Eadmin::where("api_token",$data["token"])->first();
            if(!$client){
                return response([
                    "status"   => 401 ,
                    "messages" => __('Token Expire') ,
                ],401);
            }
            \DB::beginTransaction();
            $array_id = [];
            $ie       = $data["sections"];
            // foreach($data["sections"] as $ie){
                $array_id[]      = $ie["id"];
                $section         = Section::find($ie["id"]);
                if(empty($section)){return false;}
                $section->name   = isset($ie["name"])?$ie["name"]:"";
                $section->title  = isset($ie["title"])?$ie["title"]:"";
                $section->desc   = isset($ie["description"])?$ie["description"]:"";
                if(isset($ie["image"])){
                    if($ie["image"] != null || $ie["image"] != false){
                        $dir_name =  config('constants.product_img_path');
                        if ($ie["image"]->getSize() <= config('constants.document_size_limit')) {
                            $new_file_name = time() . '_' . $ie["image"]->getClientOriginalName();
                            // $data          = getimagesize($ie["image"]);
                            // $width         = $data[0];
                            // $height        = $data[1];
                            // $half_width    = $width/2;
                            // $half_height   = $height/2;
                            // $imgs          = \Image::make($ie["image"])->resize($half_width,$half_height); //$request->$file_name->storeAs($dir_name, $new_file_name)  ||\public_path($new_file_name)
                            if ($ie["image"]->storeAs($dir_name,$new_file_name)) {
                                $uploaded_file_name      = $new_file_name;
                                $section->image          = $uploaded_file_name;
                            }
                        }
                    }
                }
                $section->button = isset($ie["button"])?$ie["button"]:"";
                $section->update();
            // } 
            \DB::commit();
            return $output = [
                    "status"         => 200,
                    "message"       => __("Updated Sections Successfully")    
            ] ;
        }else{
            return response([
                "status"   => 403 ,
                "message" => __('Invalid Data') ,
            ],403);
        }
    }
}
