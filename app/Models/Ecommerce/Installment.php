<?php

namespace App\Models\Ecommerce;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Installment extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = "ecom_installments";

    protected $append = ["image_url"];

    // ***** APPEND SECTION 
    public function getImageUrlAttribute(){
        $image_url = "";
        if(!empty($this->image)){
            $image_url = asset("public/uploads/img/" . rawurlencode($this->image));
        }
        return $image_url;
    }


    // 1 *** index
    public static function indexInstallment($client) {
        try{
            $list          = []; 
            $condition     = Installment::get();
            foreach($condition as $e){
                $list[]    = [
                    "id"     => $e->id,
                    "value"  => $e->name,
                    "image"  => $e->image_url
                ];
            }
            return $list;
        }catch(Exception $e){
            return false;
        }
    }
    // 2 *** store
    public static function storeInstallment($client,$data,$request) {
        try{
            $condition          = new Installment;
            $condition->name    = $data["name"] ;
            if($request->hasFile("image") != null || $request->hasFile("image") != false){
                $dir_name =  config('constants.product_img_path');
                if ($request->file("image")->getSize() <= config('constants.document_size_limit')) {
                    $new_file_name = time() . '_' . $request->file("image")->getClientOriginalName();
                    if ($request->file("image")->storeAs($dir_name, $new_file_name)) {
                        $uploaded_file_name      = $new_file_name;
                        $condition->image        = $uploaded_file_name;
                    }
                }
            }
            $condition->save();
            return true;
        }catch(Exception $e){
            return false;
        }
        
    }
    // 3 *** update
    public static function updateInstallment($client,$data,$id) {
        try{
            $condition          = Installment::find($id);
            $condition->name    = $data["name"] ;
            if($request->hasFile("image") != null || $request->hasFile("image") != false){
                $dir_name =  config('constants.product_img_path');
                if ($request->file("image")->getSize() <= config('constants.document_size_limit')) {
                    $new_file_name = time() . '_' . $request->file("image")->getClientOriginalName();
                    if ($request->file("image")->storeAs($dir_name, $new_file_name)) {
                        $uploaded_file_name      = $new_file_name;
                        $condition->image        = $uploaded_file_name;
                    }
                }
            }
            $condition->update();
            return true;
        }catch(Exception $e){
            return false;
        }
        
    }
    // 4 *** delete
    public static function deleteInstallment($client,$data,$id) {
        try{
            $condition          = Installment::find($id);
            $condition->delete();
            return true;
        }catch(Exception $e){
            return false;
        }
        
    }

}
