<?php

namespace App\Models\Ecommerce;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SocialMedia extends Model
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

    // SHOW LINKS SOCIAL MEDIA
    public static function getLinks($data,$client) {
        try{
            $links   = SocialMedia::where("business_id",$client->business_id)->get();   
            $list    = [];
            foreach($links as  $i){
                $list[]= [
                        "id"    => $i->id,
                        "icon"  => $i->icon_url,
                        "title" => $i->title,
                        "link"  => $i->link
                ];
            }
            return $list;
        }catch(Exception $e){
            return false;
        };
    }

    // ADD LINKS SOCIAL MEDIA
    public static function addLinks($data,$client,$request) {
        try{
            $link                 =  new SocialMedia();
            $link->business_id    =  $client->business_id; 
            $link->title          =  $data['title']; 
            $link->link           =  $data['link']; 
            $link->client_id      =  $client->id; 
             if($request->hasFile("icon") != null || $request->hasFile("icon") != false){
                $dir_name =  config('constants.product_img_path');
                if ($request->file("icon")->getSize() <= config('constants.document_size_limit')) {
                    $new_file_name = time() . '_' . $request->file("icon")->getClientOriginalName();
                    if ($request->file("icon")->storeAs($dir_name, $new_file_name)) {
                        $uploaded_file_name      = $new_file_name;
                        $link->icon            = $uploaded_file_name;
                    }
                }
            }
            $link->save();
            return true;
        }catch(Exception $e){
            return false;
        } 
    }

    // UPDATE LINKS SOCIAL MEDIA
    public static function updateLinks($data,$client,$request) {
        try{
            $link                 =  SocialMedia::find($data["id"]);
            if($request->hasFile("icon") != null || $request->hasFile("icon") != false){
                $dir_name =  config('constants.product_img_path');
                if ($request->file("icon")->getSize() <= config('constants.document_size_limit')) {
                    $new_file_name = time() . '_' . $request->file("icon")->getClientOriginalName();
                    if ($request->file("icon")->storeAs($dir_name, $new_file_name)) {
                        $uploaded_file_name      = $new_file_name;
                        $link->icon            = $uploaded_file_name;
                    }
                }
            }
            $link->title          =  $data['title']; 
            $link->link           =  $data['link']; 
            $link->update();
            return true;
        }catch(Exception $e){
            return false;
        } 
    }

    // DELETE LINKS SOCIAL MEDIA
    public static function delLinks($data,$client) {
        try{
            $link                 =  SocialMedia::find($data["id"]);
            $link->delete();
            return true;
        }catch(Exception $e){
            return false;
        } 
    }

    public static function allData(){
        $item              = SocialMedia::get(); 
       return $item;
    }
}
