<?php

namespace App\Models\FrontEnd\Sales;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SaleCommisionAgent extends Model
{
    use HasFactory,SoftDeletes;
    // *** REACT FRONT-END SALES COMMISSION AGENT *** // 
    // **1** ALL SALES COMMISSION AGENT
    public static function getSalesCommissionAgent($user) {
        try{
            $list        = [];
            $business_id = $user->business_id;
            $users       = \App\User::where("business_id",$business_id)->get();
            if(count($users)==0) { return false; }
            foreach($users as $i){ $list[] = $i; }
            return $list;
        }catch(Exception $e){
            return false;
        }
    }
    // **2** CREATE SALES COMMISSION AGENT
    public static function createSalesCommissionAgent($user,$data) {
        try{
            $business_id             = $user->business_id;
            $list["type"]            = ["value" => $data["type"]];
            return $list;
        }catch(Exception $e){
            return false;
        }
    }
    // **3** EDIT SALES COMMISSION AGENT
    public static function editSalesCommissionAgent($user,$data,$id) {
        try{
            $business_id             = $user->business_id;
            $users                   = \App\User::find($id);
            $list["users"]           = $users;
            if(!$brand){ return false; }
            return $list;
        }catch(Exception $e){
            return false;
        } 
    }
    // **4** STORE SALES COMMISSION AGENT
    public static function storeSalesCommissionAgent($user,$data) {
        try{
            \DB::beginTransaction();
            $business_id         = $user->business_id;
            $data["name"]        = implode(" ", [$data["first_name"]]);
            $data["business_id"] = $business_id;
            $data["created_by"]  = $user->id;
            if(!empty($data["contact_id"]) && $data["contact_id"] != ""){
                $old             = \App\User::where("contact_id",$data["contact_id"])->where("business_id",$data["business_id"])->first();
                if($old){return false;}
            }
            $output              = SaleCommisionAgent::createNewSalesCommissionAgent($user,$data);
            if($output == false){ return false; } 
            \DB::commit();
            return $output;
        }catch(Exception $e){
            return false;
        }
    }
    // **5** UPDATE SALES COMMISSION AGENT
    public static function updateSalesCommissionAgent($user,$data,$id) {
        try{
            \DB::beginTransaction();
            $business_id         = $user->business_id;
            $data["name"]        = implode(" ", [$data["first_name"]]);
            $data["business_id"] = $business_id;
            $data["created_by"]  = $user->id;
            if(!empty($data["contact_id"]) && $data["contact_id"] != ""){
                $old             = \App\Contact::where("contact_id",$data["contact_id"])->where("id","!=",$id)->where("business_id",$data["business_id"])->first();
                if($old){return false;}
            }
            $output              = SaleCommisionAgent::updateOldSalesCommissionAgent($user,$data,$id);
            \DB::commit();
            return $output;
        }catch(Exception $e){
            return false;
        }
    }
    // **6** DELETE SALES COMMISSION AGENT
    public static function deleteSalesCommissionAgent($user,$id) {
        try{
            \DB::beginTransaction();
            $business_id = $user->business_id;
            $users       = \App\User::find($id);
            if(!$users){ return false; }
            $users->delete();
            \DB::commit();
            return true;
        }catch(Exception $e){
            return false;
        }
    }


    // ****** MAIN FUNCTIONS 
    // **1** CREATE SALES COMMISSION AGENT
    public static function createNewSalesCommissionAgent($user,$data) {
       try{
            $business_id    = $user->business_id;
            $users          = new \App\User();
            $users->save();
            return true; 
        }catch(Exception $e){
            return false;
        }
    }
    // **2** UPDATE SALES COMMISSION AGENT
    public static function updateOldSalesCommissionAgent($user,$data,$id) {
       try{
            $business_id    = $user->business_id;
            $users          = \App\User::find($id);
            $users->update();
            return true; 
        }catch(Exception $e){
            return false;
        }
    }


}
