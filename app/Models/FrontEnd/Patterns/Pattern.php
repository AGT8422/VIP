<?php

namespace App\Models\FrontEnd\Patterns;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\FrontEnd\Utils\GlobalUtil;

class Pattern extends Model
{
    use HasFactory,SoftDeletes;
    // *** REACT FRONT-END PATTERN *** // 
    // **1** ALL PATTERN
    public static function getPattern($user) {
        try{
            $list               = [];
            $business_id        = $user->business_id;
            $pattern            =  Pattern::allData("all",null,$business_id); 
            if($pattern == false){ return false;}
            return $pattern;
        }catch(Exception $e){
            return false;
        }
    }
    // **2** CREATE PATTERN
    public static function createPattern($user,$data) {
        try{
            $business_id        = $user->business_id;
            $require            = Pattern::getRequire($business_id);
            return $require;
        }catch(Exception $e){
            return false;
        }
    }
    // **3** EDIT PATTERN
    public static function editPattern($user,$data,$id) {
        try{
            $business_id          = $user->business_id;
            $pattern              = Pattern::allData(null,$id,$business_id);
            if(!$pattern){ return false; }
            return $pattern;
        }catch(Exception $e){
            return false;
        } 
    }
    // **4** STORE PATTERN
    public static function storePattern($user,$data) {
        try{
            \DB::beginTransaction();
            $business_id         = $user->business_id;
            $data["business_id"] = $business_id;
            $data["created_by"]  = $user->id;
            if(!empty($data["name"]) && $data["name"] != ""){
                $old             = \App\Models\Pattern::where("name",$data["name"])->where("business_id",$data["business_id"])->first();
                if($old){return "old";}
            }
            $output              = Pattern::createNewPattern($user,$data);
            if($output == false){ return "false"; } 
            \DB::commit();
            return $output;
        }catch(Exception $e){
            return "failed";
        }
    }
    // **5** UPDATE PATTERN
    public static function updatePattern($user,$data,$id) {
        try{
            \DB::beginTransaction();
            $business_id         = $user->business_id;
            $data["business_id"] = $business_id;
            $data["created_by"]  = $user->id;
            if(!empty($data["name"]) && $data["name"] != ""){
                $old             = \App\Models\Pattern::where("name",$data["name"])->where("id","!=",$id)->where("business_id",$data["business_id"])->first();
                if($old){return "old";}
            }
            $output              = Pattern::updateOldPattern($user,$data,$id);
            if($output == false){ return "false"; } 
            \DB::commit();
            return "true";
        }catch(Exception $e){
            return "failed";
        }
    }
    // **6** DELETE PATTERN
    public static function deletePattern($user,$id) {
        try{
            \DB::beginTransaction();
            $business_id = $user->business_id;
            $pattern     = \App\Models\Pattern::find($id);
            if(!$pattern){ return "false"; }
            $check            = GlobalUtil::checkPattern($id);
            if($check){ return "cannot"; }
            $pattern->delete();
            \DB::commit();
            return "true";
        }catch(Exception $e){
            return "false";
        }
    }

    // ****** MAIN FUNCTIONS 
    // **1** CREATE PATTERN
    public static function createNewPattern($user,$data) {
        try{
            $business_id             =  $user->business_id;
            $pattern                 =  new \App\Models\Pattern();
            $pattern->code           =  $data["code"];  
            $pattern->business_id    =  $business_id;  
            $pattern->location_id    =  $data["location_id"];  
            $pattern->name           =  $data["name"];  
            $pattern->invoice_scheme =  $data["invoice_scheme"];  
            $pattern->invoice_layout =  $data["invoice_layout"];  
            $pattern->pos            =  $data["pos"]; 
            $pattern->user_id        =  $user->id; 
            $pattern->save();
            return true; 
        }catch(Exception $e){
            return false;
        }
    }
    // **2** UPDATE PATTERN
    public static function updateOldPattern($user,$data,$id) {
        try{
            $business_id             =  $user->business_id;
            $pattern                 =  \App\Models\Pattern::find($id);
            $pattern->code           =  $data["code"];  
            $pattern->business_id    =  $business_id;  
            $pattern->location_id    =  $data["location_id"];  
            $pattern->name           =  $data["name"];  
            $pattern->invoice_scheme =  $data["invoice_scheme"];  
            $pattern->invoice_layout =  $data["invoice_layout"];  
            $pattern->pos            =  $data["pos"]; 
            $pattern->user_id        =  $user->id; 
            $pattern->update();
            return true; 
        }catch(Exception $e){
            return false;
        }
    }

    // **3** GET PATTERN  
    public static function allData($type=null,$id=null,$business_id) {
        try{
            $list   = [];
            if($type != null){
                $pattern     = \App\Models\Pattern::where("business_id",$business_id)->get();
                if(count($pattern) == 0 ){ return false; }
                foreach($pattern as $ie){
                    $list[] = [
                        "id"             => $ie->id,
                        "name"           => $ie->name,
                        "code"           => $ie->code,
                        "location"       => $ie->location_id,
                        "invoice_scheme" => ($ie->scheme)?$ie->scheme->name:$ie->invoice_scheme,
                        "invoice_layout" => ($ie->layout)?$ie->layout->name:$ie->invoice_layout,
                        "pos"            => $ie->pos,
                        "created_by"     => ($ie->user)?$ie->user->first_name:$ie->user_id,
                    ];
                }
            }else{
                $pattern  = \App\Models\Pattern::find($id);
                if(empty($pattern)){ return false; }
                $list["info"] = [
                    "id"             => $pattern->id,
                    "name"           => $pattern->name,
                    "code"           => $pattern->code,
                    "location"       => $pattern->location_id,
                    "invoice_scheme" => ($pattern->scheme)?$pattern->scheme->name:$pattern->invoice_scheme,
                    "invoice_layout" => ($pattern->layout)?$pattern->layout->name:$pattern->invoice_layout,
                    "pos"            => $pattern->pos,
                    "created_by"     => ($pattern->user)?$pattern->user->id:$pattern->user_id,
                ];
                $list["require"]      = Pattern::getRequire($business_id);
            }
            return $list; 
        }catch(Exception $e){
            return false;
        }
    }

    // **4** GET lOCATION
    public static function getRequire($business_id){
        $list_1          = [];$list_2 = [];$list_3  = [];$list = [];
        $location        = \App\BusinessLocation::where("business_id",$business_id)->get();
        $InvoiceLayout   = \App\InvoiceLayout::where("business_id",$business_id)->get();
        $InvoiceSchema   = \App\InvoiceScheme::where("business_id",$business_id)->get();
        foreach($location as $e){
            $list_1[] = [
                "id"   => $e->id,
                "name" => $e->name,
            ];
        }
        foreach($InvoiceLayout as $e){
            $list_2[] = [
                "id"   => $e->id,
                "name" => $e->name,
            ];
        }
        foreach($InvoiceSchema as $e){
            $list_3[] = [
                "id"   => $e->id,
                "name" => $e->name,
            ];
        }
        $list["location"]       = $list_1;
        $list["invoiceLayout"]  = $list_2;
        $list["invoiceSchema"]  = $list_3;
        return  $list;
    }

}

