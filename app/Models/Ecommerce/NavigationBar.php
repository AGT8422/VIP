<?php

namespace App\Models\Ecommerce;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NavigationBar extends Model
{
    use HasFactory,SoftDeletes;

    protected $append = ["icon_url"];

    // ***** APPEND SECTION 
    public function getIconUrlAttribute(){
        $icon_url = "";
        if(!empty($this->icon)){
            $icon_url = asset("public/uploads/img/" . rawurlencode($this->icon));
        }
        return $icon_url;
    }

    // *1** GET NAVIGATION BAR
    public static function getListNavigationBar() {
        try{
            $source  = NavigationBar::get(); 
            $list    = [];
            foreach($source as $i){
                $list [] = [
                    "id"    => $i->id,   
                    "title" => $i->title,   
                    "icon"  => $i->icon_url,   
                ];    
            }
            return $list;
        }catch(Exception $e){
            return false;
        }
    }

    // *2** SAVE NAVIGATION BAR 
    public static function saveNavigationBar($data,$client,$request) {
        try{
            $source              = new NavigationBar();
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

    // *3**  UPDATE NAVIGATION BAR 
    public static function updateNavigationBar($data,$client) {
        try{
            $source              = NavigationBar::find($data["id"]); 
            $source->business_id = $client->contact->business_id;
            $source->title       = $data["title"];
            $source->icon        = $data["icon"];
            $source->update();
            return true;
        }catch(Exception $e){
            return false;
        }
    }

    // *4** DELETE NAVIGATION  BAR 
    public static function deleteNavigationBar($data,$client) {
        try{
            $source  = NavigationBar::find($data["id"]); 
            $source->delete();
            return true;
        }catch(Exception $e){
            return false;
        }
    }
}
