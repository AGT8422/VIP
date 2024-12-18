<?php

namespace App\Models\FrontEnd\Products;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Warranty extends Model
{
    use HasFactory,SoftDeletes;
    // *** REACT FRONT-END WARRANTY *** // 
    // **1** ALL WARRANTY
    public static function getWarranty($user) {
        try{
            $list               = [];
            $business_id        = $user->business_id;
            $warranty           = \App\Warranty::where("business_id",$business_id)->get();
            if(count($warranty)==0) { return false; }
            foreach($warranty as $i){
                $list[] = [
                    "id"            => $i->id,
                    "name"          => $i->name,
                    "description"   => $i->description,
                    "duration"      => $i->duration,
                    "duration_type" => $i->duration_type,
                    "user_id"       => $i->user->first_name
                ]; 
            }
            return $list;
        }catch(Exception $e){
            return false;
        }
    }
    // **2** CREATE WARRANTY
    public static function createWarranty($user,$data) {
        try{
            $business_id         = $user->business_id;
            $list["condition"]   = [
                        ""       => "Please Select",
                        "days"   => "Days",
                        "months" => "Months",
                        "years"  => "Years",
            ];
            return $list;
        }catch(Exception $e){
            return false;
        }
    }
    // **3** EDIT WARRANTY
    public static function editWarranty($user,$data,$id) {
        try{
            $business_id         = $user->business_id;
            $warranty            = \App\Warranty::find($id);
            if(!$warranty){ return false; }
            $line[] = [
                "id"            => $warranty->id,
                "name"          => $warranty->name,
                "description"   => $warranty->description,
                "duration"      => $warranty->duration,
                "duration_type" => $warranty->duration_type,
                "user_id"       => $warranty->user->first_name    
            ];
            $list["info"]        = $line;
            $list["condition"]   = [
                        ""       => "Please Select",
                        "days"   => "Days",
                        "months" => "Months",
                        "years"  => "Years",
            ];
            return $list;
        }catch(Exception $e){
            return false;
        } 
    }
    // **4** STORE WARRANTY
    public static function storeWarranty($user,$data) {
        try{
            \DB::beginTransaction();
            $data["business_id"] = $user->business_id;
            $data["name"]        = trim($data["name"]);
            if(!empty($data["name"]) && $data["name"] != ""){
                $old             = \App\Warranty::where("name",$data["name"])->where("business_id",$data["business_id"])->first();
                if($old){return false;}
            }
            $output              = Warranty::createNewWarranty($user,$data);
            if($output == false){ return false; } 
            \DB::commit();
            return $output;
        }catch(Exception $e){
            return false;
        }
    }
    // **5** UPDATE WARRANTY
    public static function updateWarranty($user,$data,$id) {
        try{
            \DB::beginTransaction();
            $data["business_id"] = $user->business_id;
            $data["name"]        = trim($data["name"]);
            $warranty            = \App\Warranty::find($id);
            if(!$warranty){ return false; }
            if(!empty($data["name"]) && $data["name"] != ""){
                $old             = \App\Warranty::where("name",$data["name"])->where("id",$id)->where("business_id",$data["business_id"])->first();
                if($old){return false;}
            }
            $output              = Warranty::updateOldWarranty($user,$data,$id);
            \DB::commit();
            return $output;
        }catch(Exception $e){
            return false;
        }
    }
    // **6** DELETE WARRANTY
    public static function deleteWarranty($user,$id) {
        try{
            \DB::beginTransaction();
            $business_id = $user->business_id;
            $warranty    = \App\Warranty::find($id);
            if(!$warranty){ return false; }
            $warranty->delete();
            \DB::commit();
            return true;
        }catch(Exception $e){
            return false;
        }
    }

    // ****** MAIN FUNCTIONS 
    // **1** CREATE WARRANTY
    public static function createNewWarranty($user,$data) {
        try{
            $business_id             = $user->business_id;
            $warranty                = new \App\Warranty();
            $warranty->business_id   = $data["business_id"];
            $warranty->name          = $data["name"];
            $warranty->description   = $data["description"];
            $warranty->duration      = $data["duration"];
            $warranty->duration_type = $data["duration_type"];
            $warranty->state_action  = "Add";
            $warranty->type          = 0;
            $warranty->user_id       = $user->id;
            $warranty->save();
            return true; 
        }catch(Exception $e){
            return false;
        }
    }
    // **2** UPDATE WARRANTY
    public static function updateOldWarranty($user,$data,$id) {
        try{
            $warranty                = \App\Warranty::find($id);
            $warranty->business_id   = $data["business_id"];
            $warranty->name          = $data["name"];
            $warranty->description   = $data["description"];
            $warranty->duration      = $data["duration"];
            $warranty->duration_type = $data["duration_type"];
            $warranty->update();
            return true; 
        }catch(Exception $e){
            return false;
        }
    }


}
