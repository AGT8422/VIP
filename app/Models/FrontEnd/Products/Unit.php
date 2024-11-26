<?php

namespace App\Models\FrontEnd\Products;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Unit extends Model
{
    use HasFactory,SoftDeletes;
    // *** REACT FRONT-END UNIT *** // 
    // **1** ALL UNIT
    public static function getUnit($user) {
        try{
            $list        = [];
            $business_id = $user->business_id;
            $unit        = Unit::allData("all",null,$business_id);
            if(count($unit)==0) { return false; }
            return $unit;
        }catch(Exception $e){
            return false;
        }
    }
    // **2** CREATE UNIT
    public static function createUnit($user,$data) {
        try{
            $business_id             = $user->business_id;
            $allUnit                 = Unit::allData("all",null,$business_id);
            $list                    = [];
            $list["units"]           = $allUnit;
            return $list;
        }catch(Exception $e){
            return false;
        }
    }
    // **3** EDIT UNIT
    public static function editUnit($user,$data,$id) {
        try{
            $business_id             = $user->business_id;
            $unit                    = Unit::allData(null,$id,$business_id);
            $allUnit                 = Unit::allData("all",null,$business_id,false,$id);
            if(!$unit){ return false; }
            $list["info"]            = $unit;
            $list["units"]           = $allUnit;
            return $list;
        }catch(Exception $e){
            return false;
        } 
    }
    // **4** STORE UNIT
    public static function storeUnit($user,$data,$request) {
        try{
            \DB::beginTransaction();
            $data["business_id"] =  $user->business_id;
            $data["name"]        =  trim($data["name"]);
            if(!empty($data["name"]) && $data["name"] != ""){
                $old             = \App\Unit::where("actual_name",$data["name"])->where("business_id",$data["business_id"])->first();
                if($old){return false;}
            }
            $output              = Unit::createNewUnit($user,$data,$request);
            if($output == false){ return false; } 
            \DB::commit();
            return $output;
        }catch(Exception $e){
            return false;
        }
    }
    // **5** UPDATE UNIT
    public static function updateUnit($user,$data,$id) {
        try{
            \DB::beginTransaction();
            $data["name"]        = trim($data["name"]);
            $data["business_id"] = $user->business_id;
            if(!empty($data["name"]) && $data["name"] != ""){
                $old             = \App\Unit::where("actual_name",$data["name"])->where("id","!=",$id)->where("business_id",$data["business_id"])->first();
                if($old){return false;}
            }
            $output              = Unit::updateOldUnit($user,$data,$id);
            \DB::commit();
            return $output;
        }catch(Exception $e){
            return false;
        }
    }
    // **6** DELETE UNIT
    public static function deleteUnit($user,$id) {
        try{
            \DB::beginTransaction();
            $business_id = $user->business_id;
            $unit        = \App\Unit::find($id);
            if(!$unit){ return false; }
            $unit->delete();
            \DB::commit();
            return true;
        }catch(Exception $e){
            return false;
        }
    }

    // ****** MAIN FUNCTIONS 
    // **1** CREATE UNIT
    public static function createNewUnit($user,$data,$request) {
        try{
            $business_id                  = $user->business_id;
            $unit                         = new \App\Unit();
            $unit->business_id            = $business_id;
            $unit->actual_name            = $data["name"];
            $unit->short_name             = $data["short_name"];
            $unit->allow_decimal          = $data["allow_decimal"];
             
            if(isset($request->multiple_unit)){
                if( $request->multiple_unit){
                    
                    $unit->base_unit_id           =  ($data["parent_unit"] === 0)?null:$data["parent_unit"];
                    $unit->base_unit_multiplier   = $data["sub_qty"];
                }
            }
            $unit->created_by             = $user->id;
            $unit->default                = 0;
            $unit->save();
            return true; 
        }catch(Exception $e){
            return false;
        }
    }
    // **2** UPDATE UNIT
    public static function updateOldUnit($user,$data,$id) {
        try{
            $business_id                  = $user->business_id;
            $unit                         = \App\Unit::find($id);
            if(empty($unit)){return false;}
            $unit->actual_name            = $data["name"];
            $unit->short_name             = $data["short_name"];
            $unit->allow_decimal          = $data["allow_decimal"];
            if(isset($data["multiple_unit"])  &&  ($data["multiple_unit"] == 1 || $data["multiple_unit"] == true)){
                
                $unit->base_unit_id           = ($data["parent_unit"] == false)?null:$data["parent_unit"];
                $unit->base_unit_multiplier   = $data["sub_qty"];
            }else{
                $unit->base_unit_id           = null;
                $unit->base_unit_multiplier   = null;
            }
            $unit->update();
            return true; 
        }catch(Exception $e){
            return false;
        }
    }
    // **3** GET  UNITS
    public static function allData($type=null,$id=null,$business_id,$tp = true,$edit = null) {
        try{
            $list   = [];
            if($type != null){
                if($tp == true){
                    if($edit == null){
                        $units     = \App\Unit::where("business_id",$business_id)->get();
                    }else{
                        $units     = \App\Unit::where("business_id",$business_id)->whereNull("base_unit_id")->where("id","!=",$edit)->get();
                    }
                }else{
                    if($edit == null){
                        $units     = \App\Unit::where("business_id",$business_id)->get();
                    }else{
                        $units     = \App\Unit::where("business_id",$business_id)->where("id","!=",$edit)->whereNull("base_unit_id")->get();
                    }
                }
                if(count($units) == 0 ){ return false; }
                foreach($units as $ie){
                    $list[] = [
                        "id"                   => $ie->id,
                        "name"                 => $ie->actual_name,
                        "short_name"           => $ie->short_name,
                        "allow_decimal"        => $ie->allow_decimal,
                        "base_unit_id"         => $ie->base_unit_id,
                        "base_unit_multiplier" => $ie->base_unit_multiplier,
                        "default"              => $ie->default,
                        "created_by"           =>  ($ie->user)?$ie->user->first_name:"",
                        "in_product"           => $ie->in_product,
                        "in_product"           => $ie->price_unit,
                        "date"                 => $ie->created_at->format("Y-m-d h:i:s"),
                    ];
                }
            }else{
                $units     = \App\Unit::find($id);
                if(empty($units)){ return false; }
                $list[] = [
                    "id"                   => $units->id,
                    "name"                 => $units->actual_name,
                    "short_name"           => $units->short_name,
                    "allow_decimal"        => $units->allow_decimal,
                    "base_unit_id"         => $units->base_unit_id,
                    "base_unit_multiplier" => $units->base_unit_multiplier,
                    "default"              => $units->default,
                    "created_by"           => ($units->user)?$units->user->first_name:"",
                    "in_product"           => $units->in_product,
                    "in_product"           => $units->price_unit,
                    "date"                 => $units->created_at->format("Y-m-d h:i:s"),
                ];
                
            }
            return $list; 
        }catch(Exception $e){
            return false;
        }
    }
    // **4** SET DEFAULT UNIT
    public static function defaultUnit($user,$id) {
        try{
            $un           = \App\Unit::find($id);
            if(empty($un)){return false;} 
            $un_all       = \App\Unit::where("id","!=",$id)->where("business_id",$user->business_id)->get();  
            foreach($un_all as $uns){
                $uns->default = 0;
                $uns->update();
            }
            $un->default = 1;
            $un->update();  
            return true;
        }catch(Exception $e){
            return false;
        }
    }

}
