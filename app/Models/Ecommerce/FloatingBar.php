<?php

namespace App\Models\Ecommerce;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FloatingBar extends Model
{
    use HasFactory,SoftDeletes;


    protected $appends = ['icon_url'];

    // ***** APPEND SECTION
    public function getIconUrlAttribute()
    {
        $icon_url ='';
        if (!empty($this->icon)) {
            $icon_url = asset('public/uploads/img/' . rawurlencode($this->icon));
        } 
        return $icon_url;
    }

    // *1** GET FLOATING BAR
    public static function getListFloatingBar() {
        try{
            $source       = FloatingBar::where("view",1)->get(); 
            $list         = [];
            foreach($source as $i){
                $list_product = [];
                $category = \App\Category::find($i->category_id);
                if(!empty($category)){
                    $product  = \App\Product::where("sub_category_id",$category->id)->orWhere("category_id",$category->id)->get();
                }else{
                    $product  = [];
                }
                if(count($product)>1){
                    foreach($product as $ie){
                        $list_product[] = [
                            "id"    => $ie->id,        
                            "name"  => $ie->name,        
                            "image" => ($ie->image_url != "")?$ie->image_url:"https://upload.wikimedia.org/wikipedia/commons/thumb/6/65/No-Image-Placeholder.svg/1665px-No-Image-Placeholder.svg.png",        
                        ];
                    }
                    $list []    = [
                        "id"    => $i->id,   
                        "title" => $category->name,   
                        "icon"  => $i->icon_url,
                        "items" => $list_product
                    ];    
                }else{
                    $list []  = [
                        "id"    => $i->id,   
                        "title" => $category->name,   
                        "icon"  => $i->icon_url
                    ];    
                }
            }
            return $list;
        }catch(Exception $e){
            return false;
        }
    }

    // *2** SAVE FLOATING BAR 
    public static function saveFloatingBar($data,$client,$request) {
        try{
            $source              = new FloatingBar();
            $source->business_id = $client->contact->business_id;
            $source->title       = $data["title"];
            if($request->hasFile("icon") != null || $request->hasFile("icon") != false){
                $dir_name =  config('constants.product_img_path');
                if ($request->file("icon")->getSize() <= config('constants.document_size_limit')) {
                    $new_file_name = time() . '_' . $request->file("icon")->getClientOriginalName();
                    if ($request->file("icon")->storeAs($dir_name, $new_file_name)) {
                        $uploaded_file_name      = $new_file_name;
                        $source->icon            = $uploaded_file_name;
                    }
                }
            }
            $source->save();
            return true;
        }catch(Exception $e){
            return false;
        }
    }
    
    // *3**  UPDATE FLOATING BAR
    public static function updateFloatingBar($data,$client) {
        try{
            $source              = FloatingBar::find($data["id"]); 
            $source->business_id = $client->contact->business_id;
            $source->title       = $data["title"];
            if($request->hasFile("icon") != null || $request->hasFile("icon") != false){
                $dir_name =  config('constants.product_img_path');
                if ($request->file("icon")->getSize() <= config('constants.document_size_limit')) {
                    $new_file_name = time() . '_' . $request->file("icon")->getClientOriginalName();
                    if ($request->file("icon")->storeAs($dir_name, $new_file_name)) {
                        $uploaded_file_name      = $new_file_name;
                        $source->icon            = $uploaded_file_name;
                    }
                }
            }
            $source->update();
            return true;
        }catch(Exception $e){
            return false;
        }
    }

    // *4** DELETE FLOATING BAR
    public static function deleteFloatingBar($data,$client) {
        try{
            $source  = FloatingBar::find($data["id"]); 
            $source->delete();
            return true;
        }catch(Exception $e){
            return false;
        }
    }
}
