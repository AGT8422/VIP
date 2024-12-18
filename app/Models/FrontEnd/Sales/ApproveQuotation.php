<?php

namespace App\Models\FrontEnd\Sales;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ApproveQuotation extends Model
{
    use HasFactory,SoftDeletes;
    // *** REACT FRONT-END APPROVE QUOTATION *** // 
    // **1** ALL APPROVE QUOTATION
    public static function getApprove($user) {
        try{
            $list          = [];
            $business_id   = $user->business_id;
            $approve       = \App\Approve::where("business_id",$business_id)->get();
            if(count($approve)==0) { return false; }
            foreach($approve as $i){ $list[] = $i; }
            return $list;
        }catch(Exception $e){
            return false;
        }
    }
    // **2** CREATE APPROVE QUOTATION
    public static function createApprove($user,$data) {
        try{
            $business_id             = $user->business_id;
            $list["type"]            = ["value" => $data["type"]];
            return $list;
        }catch(Exception $e){
            return false;
        }
    }
    // **3** EDIT APPROVE QUOTATION
    public static function editApprove($user,$data,$id) {
        try{
            $business_id             = $user->business_id;
            $approve                 = \App\Transaction::find($id);
            $list["approve"]         = $approve;
            if(!$brand){ return false; }
            return $list;
        }catch(Exception $e){
            return false;
        } 
    }
    // **4** STORE APPROVE QUOTATION
    public static function storeApprove($user,$data) {
        try{
            \DB::beginTransaction();
            $business_id         = $user->business_id;
            $data["name"]        = implode(" ", [$data["first_name"]]);
            $data["business_id"] = $business_id;
            $data["created_by"]  = $user->id;
            if(!empty($data["contact_id"]) && $data["contact_id"] != ""){
                $old             = \App\Transaction::where("contact_id",$data["contact_id"])->where("business_id",$data["business_id"])->first();
                if($old){return false;}
            }
            $output              = Approve::createNewApprove($user,$data);
            if($output == false){ return false; } 
            \DB::commit();
            return $output;
        }catch(Exception $e){
            return false;
        }
    }
    // **5** UPDATE APPROVE QUOTATION
    public static function updateApprove($user,$data,$id) {
        try{
            \DB::beginTransaction();
            $business_id         = $user->business_id;
            $data["name"]        = implode(" ", [$data["first_name"]]);
            $data["business_id"] = $business_id;
            $data["created_by"]  = $user->id;
            if(!empty($data["contact_id"]) && $data["contact_id"] != ""){
                $old             = \App\Transaction::where("contact_id",$data["contact_id"])->where("id","!=",$id)->where("business_id",$data["business_id"])->first();
                if($old){return false;}
            }
            $output              = Approve::updateOldApprove($user,$data,$id);
            \DB::commit();
            return $output;
        }catch(Exception $e){
            return false;
        }
    }
    // **6** DELETE APPROVE QUOTATION
    public static function deleteApprove($user,$id) {
        try{
            \DB::beginTransaction();
            $business_id = $user->business_id;
            $approve     = \App\Transaction::find($id);
            if(!$approve){ return false; }
            $approve->delete();
            \DB::commit();
            return true;
        }catch(Exception $e){
            return false;
        }
    }


    // ****** MAIN FUNCTIONS 
    // **1** CREATE APPROVE QUOTATION
    public static function createNewApprove($user,$data) {
       try{
            $business_id   = $user->business_id;
            $approve       = new \App\Transaction();
            $approve->save();
            return true; 
        }catch(Exception $e){
            return false;
        }
    }
    // **2** UPDATE APPROVE QUOTATION
    public static function updateOldApprove($user,$data,$id) {
       try{
            $business_id   = $user->business_id;
            $approve       = \App\Transaction::find($id);
            $approve->update();
            return true; 
        }catch(Exception $e){
            return false;
        }
    }


}
