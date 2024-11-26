<?php

namespace App\Models\Ecommerce;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StoreFeature extends Model
{
    use HasFactory,SoftDeletes;
    protected $append = ["image_url"];

    // ***** APPEND SECTION 
    public function getImageUrlAttribute(){
        $image_url = "";
        if(!empty($this->image)){
            $image_url = asset("public/uploads/img/" . rawurlencode($this->image));
        }
        return $image_url;
    }
    // *1** SAVE STORE FEATURE
    public static function saveStoreFeature($data,$client,$request) {
        try{
            $list              = new StoreFeature();
            $list->business_id = $data["business_id"];
            $list->title       = $data["title"];
            if($request->hasFile("image") != null || $request->hasFile("image") != false){
                $dir_name =  config('constants.product_img_path');
                if ($request->file("image")->getSize() <= config('constants.document_size_limit')) {
                    $new_file_name = time() . '_' . $request->file("image")->getClientOriginalName();
                    if ($request->file("image")->storeAs($dir_name, $new_file_name)) {
                        $uploaded_file_name      = $new_file_name;
                        $list->image             = $uploaded_file_name;
                    }
                }
            }
            $list->description = $data["description"];
            $list->client_id   = $client->id;
            $list->save();
            return true;
        }catch(Exception $e){
            return false;
        }
    }

    // *2** UPDATE STORE FEATURE
    public static function updateStoreFeature($data,$client,$request) {
        try{
            $list              = StoreFeature::find($data["id"]);
            $list->business_id = $data["business_id"];
            $list->title       = $data["title"];
            if($request->hasFile("image") != null || $request->hasFile("image") != false){
                $dir_name =  config('constants.product_img_path');
                if ($request->file("image")->getSize() <= config('constants.document_size_limit')) {
                    $new_file_name = time() . '_' . $request->file("image")->getClientOriginalName();
                    if ($request->file("image")->storeAs($dir_name, $new_file_name)) {
                        $uploaded_file_name      = $new_file_name;
                        $list->image             = $uploaded_file_name;
                    }
                }
            }
            $list->description = $data["description"];
            $list->client_id   = $client->id;
            $list->update();
            return true;
        }catch(Exception $e){
            return false;
        }
    }

    // *3** DELETE STORE FEATURE
    public static function deleteStoreFeature($data,$client) {
        try{
            $list            = StoreFeature::find($data["id"]);
            $list->delete();
            return true;
        }catch(Exception $e){
            return false;
        }
    }

    // *4** GET LIST STORE FEATURE
    public static function getStoreFeature() {
        try{
            $stores    = StoreFeature::get();
            $list      = [];
            foreach($stores as $i){
                $list[] = [
                    "id"          => $i->id ,
                    "title"       => $i->title ,
                    "image"       => $i->image_url ,
                    "description" => $i->description ,
                    "business_id" => $i->business_id ,
                ];
            }
            return $list;
        }catch(Exception $e){
            return false;
        }
    }
    // *5** GET only One STORE FEATURE
    public static function getStoreFeatureOne($data,$id) {
        try{
            $stores    = StoreFeature::find($id);
            if(empty($stores)){ return false; }
            $list = [
                "id"          => $stores->id ,
                "title"       => $stores->title ,
                "image"       => $stores->image_url ,
                "description" => $stores->description ,
                "business_id" => $stores->business_id ,
            ];
            return $list;
        }catch(Exception $e){
            return false;
        }
    }


}
