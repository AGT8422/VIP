<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContactUs extends Model
{
    use HasFactory;
    use SoftDeletes;


    protected $appends = ['icon_url'];

    /**
     * Get the products image.
     *
     * @return string
     */
    public function getIconUrlAttribute()
    {
        $icon_url ='';
        if (!empty($this->icon)) {
            $icon_url = asset('public/uploads/img/' . rawurlencode($this->icon));
        } 
        return $icon_url;
    }

    public static function saveData($Data,$index,$array,$request){
        if(count($array)>0){
            if(isset($array[$index])){
                $arrays                       = [] ;
                $contact_us                   = ContactUs::find($array[$index]);
                $contact_us->title            = $Data["title"][$index];
                $contact_us->mobile           = $Data["mobile"][$index];
                $contact_us->links            = $Data["title"][$index];
                $contact_us->additional_info  = $Data["mobile"][$index];
                if($request->hasFile("icon") != null || $request->hasFile("icon") != false){
                    $dir_name =  config('constants.product_img_path');
                    if ($request->file("icon")->getSize() <= config('constants.document_size_limit')) {
                        $new_file_name = time() . '_' . $request->file("icon")->getClientOriginalName();
                        if ($request->file("icon")->storeAs($dir_name, $new_file_name)) {
                            $uploaded_file_name      = $new_file_name;
                            $contact_us->icon              = $uploaded_file_name;
                        }
                    }
                }
                $contact_us->view             = 0;
                $contact_us->update();
                
            }else{
                $contact_us                   = new ContactUs(); 
                $contact_us->title            = $Data["title"][$index];
                $contact_us->mobile           = $Data["mobile"][$index];
                $contact_us->links            = $Data["title"][$index];
                $contact_us->additional_info  = $Data["mobile"][$index];
                if($request->hasFile("icon") != null || $request->hasFile("icon") != false){
                    $dir_name =  config('constants.product_img_path');
                    if ($request->file("icon")->getSize() <= config('constants.document_size_limit')) {
                        $new_file_name = time() . '_' . $request->file("icon")->getClientOriginalName();
                        if ($request->file("icon")->storeAs($dir_name, $new_file_name)) {
                            $uploaded_file_name      = $new_file_name;
                            $contact_us->icon              = $uploaded_file_name;
                        }
                    }
                }
                $contact_us->view             = 0;
                $contact_us->save();
        }
        }else{
            $contact_us                   = new ContactUs(); 
            $contact_us->title            = $Data["title"][$index];
            $contact_us->mobile           = $Data["mobile"][$index];
            $contact_us->links            = $Data["title"][$index];
            $contact_us->additional_info  = $Data["mobile"][$index];
            if($request->hasFile("icon") != null || $request->hasFile("icon") != false){
                $dir_name =  config('constants.product_img_path');
                if ($request->file("icon")->getSize() <= config('constants.document_size_limit')) {
                    $new_file_name = time() . '_' . $request->file("icon")->getClientOriginalName();
                    if ($request->file("icon")->storeAs($dir_name, $new_file_name)) {
                        $uploaded_file_name      = $new_file_name;
                        $contact_us->icon              = $uploaded_file_name;
                    }
                }
            }
            $contact_us->view             = 0;
            $contact_us->save();
          
        }
    }
    public static function allData(){
        $item              = ContactUs::where("view",1)->get(); 
       return $item;
    }
}
