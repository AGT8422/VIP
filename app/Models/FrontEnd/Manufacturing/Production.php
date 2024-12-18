<?php

namespace App\Models\FrontEnd\Manufacturing;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Production extends Model
{
    use HasFactory,SoftDeletes;
    // *** REACT FRONT-END PRODUCTION *** // 
    // **1** ALL PRODUCTION
    public static function getProduction($user) {
        try{
            $list               = [];
            $business_id        = $user->business_id;
            $production         = \App\Models\Production::where("business_id",$business_id)->get();
            if(count($production)==0) { return false; }
            foreach($production as $i){ $list[] = $i; }
            return $list;
        }catch(Exception $e){
            return false;
        }
    }
    // **2** CREATE PRODUCTION
    public static function createProduction($user,$data) {
        try{
            $business_id        = $user->business_id;
            $list["type"]       = ["value" => $data["type"]];
            return $list;
        }catch(Exception $e){
            return false;
        }
    }
    // **3** EDIT PRODUCTION
    public static function editProduction($user,$data,$id) {
        try{
            $business_id           = $user->business_id;
            $production            = \App\Models\Production::find($id);
            $list["production"]    = $production;
            if(!$production){ return false; }
            return $list;
        }catch(Exception $e){
            return false;
        } 
    }
    // **4** STORE PRODUCTION
    public static function storeProduction($user,$data) {
        try{
            \DB::beginTransaction();
            $business_id         = $user->business_id;
            $data["name"]        = implode(" ", [$data["first_name"]]);
            $data["business_id"] = $business_id;
            $data["created_by"]  = $user->id;
            if(!empty($data["contact_id"]) && $data["contact_id"] != ""){
                $old             = \App\Models\Production::where("contact_id",$data["contact_id"])->where("business_id",$data["business_id"])->first();
                if($old){return false;}
            }
            $output              = Production::createNewProduction($user,$data);
            if($output == false){ return false; } 
            \DB::commit();
            return $output;
        }catch(Exception $e){
            return false;
        }
    }
    // **5** UPDATE PRODUCTION
    public static function updateProduction($user,$data,$id) {
        try{
            \DB::beginTransaction();
            $business_id         = $user->business_id;
            $data["name"]        = implode(" ", [$data["first_name"]]);
            $data["business_id"] = $business_id;
            $data["created_by"]  = $user->id;
            if(!empty($data["contact_id"]) && $data["contact_id"] != ""){
                $old             = \App\Models\Production::where("contact_id",$data["contact_id"])->where("id","!=",$id)->where("business_id",$data["business_id"])->first();
                if($old){return false;}
            }
            $output              = Production::updateOldPattern($user,$data,$id);
            \DB::commit();
            return $output;
        }catch(Exception $e){
            return false;
        }
    }
    // **6** DELETE PRODUCTION
    public static function deleteProduction($user,$id) {
        try{
            \DB::beginTransaction();
            $business_id   = $user->business_id;
            $production    = \App\Models\Production::find($id);
            if(!$production){ return false; }
            $production->delete();
            \DB::commit();
            return true;
        }catch(Exception $e){
            return false;
        }
    }

    // ****** MAIN FUNCTIONS 
    // **1** CREATE PRODUCTION
    public static function createNewProduction($user,$data) {
        try{
            $business_id   = $user->business_id;
            $pattern       = new \App\Models\Production();
            $pattern->save();
            return true; 
        }catch(Exception $e){
            return false;
        }
    }
    // **2** UPDATE PRODUCTION
    public static function updateOldProduction($user,$data,$id) {
        try{
            $business_id   = $user->business_id;
            $production    = \App\Models\Production::find($id);
            $production->update();
            return true; 
        }catch(Exception $e){
            return false;
        }
    }


}
