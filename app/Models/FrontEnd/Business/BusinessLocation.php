<?php

namespace App\Models\FrontEnd\Business;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BusinessLocation extends Model
{
    use HasFactory,SoftDeletes;
    // *** REACT FRONT-END BUSINESS LOCATION *** // 
    // **1** ALL BUSINESS LOCATION
    public static function getBusinessLocation($user) {
        try {
            $list                = [];
            $business_id         = $user->business_id;
            $businessLocation    = \App\BusinessLocation::where("business_id",$business_id)->get();
            if(count($businessLocation)==0) { return false; }
            foreach($businessLocation as $i){ $list[] = $i; }
            return $list;
        }catch(Exception $e){
            return false;
        }
    }
    // **2** CREATE BUSINESS LOCATION
    public static function createBusinessLocation($user,$data) {
        try {
            $business_id        = $user->business_id;
            $list["type"]       = ["value" => $data["type"]];
            return $list;
        }catch(Exception $e){
            return false;
        }
    }
    // **3** EDIT BUSINESS LOCATION
    public static function editBusinessLocation($user,$data,$id) {
        try {
            $business_id               = $user->business_id;
            $businessLocation          = \App\BusinessLocation::find($id);
            $list["businessLocation"]  = $businessLocation;
            if(!$businessLocation){ return false; }
            return $list;
        }catch(Exception $e){
            return false;
        } 
    }
    // **4** STORE BUSINESS LOCATION
    public static function storeBusinessLocation($user,$data) {
        try {
            \DB::beginTransaction();
            $business_id         = $user->business_id;
            $data["name"]        = implode(" ", [$data["first_name"]]);
            $data["business_id"] = $business_id;
            $data["created_by"]  = $user->id;
            if(!empty($data["contact_id"]) && $data["contact_id"] != ""){
                $old             = \App\BusinessLocation::where("contact_id",$data["contact_id"])->where("business_id",$data["business_id"])->first();
                if($old){return false;}
            }
            $output              = BusinessLocation::createNewBusinessLocation($user,$data);
            if($output == false){ return false; } 
            \DB::commit();
            return $output;
        }catch(Exception $e){
            return false;
        }
    }
    // **5** UPDATE BUSINESS LOCATION
    public static function updateBusinessLocation($user,$data,$id) {
        try {
            \DB::beginTransaction();
            $business_id         = $user->business_id;
            $data["name"]        = implode(" ", [$data["first_name"]]);
            $data["business_id"] = $business_id;
            $data["created_by"]  = $user->id;
            if(!empty($data["contact_id"]) && $data["contact_id"] != ""){
                $old             = \App\BusinessLocation::where("contact_id",$data["contact_id"])->where("id","!=",$id)->where("business_id",$data["business_id"])->first();
                if($old){return false;}
            }
            $output              = BusinessLocation::updateOldBusinessLocation($user,$data,$id);
            \DB::commit();
            return $output;
        }catch(Exception $e){
            return false;
        }
    }
    // **6** DELETE BUSINESS LOCATION
    public static function deleteBusinessLocation($user,$id) {
        try {
            \DB::beginTransaction();
            $business_id      = $user->business_id;
            $businessLocation = \App\BusinessLocation::find($id);
            if(!$businessLocation){ return false; }
            $businessLocation->delete();
            \DB::commit();
            return true;
        }catch(Exception $e){
            return false;
        }
    }
    // ****** MAIN FUNCTIONS 
    // **1** CREATE BUSINESS LOCATION
    public static function createNewBusinessLocation($user,$data) {
        try {
            $business_id    = $user->business_id;
            $businessLocation        = new \App\BusinessLocation();
            $businessLocation->save();
            return true; 
        }catch(Exception $e){
            return false;
        }
    }
    // **2** UPDATE BUSINESS LOCATION
    public static function updateOldBusinessLocation($user,$data,$id) {
        try {
            $business_id       = $user->business_id;
            $businessLocation  = \App\BusinessLocation::find($id);
            $businessLocation->update();
            return true; 
        }catch(Exception $e){
            return false;
        }
    }

    
}
