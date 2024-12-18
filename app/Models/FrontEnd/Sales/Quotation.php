<?php

namespace App\Models\FrontEnd\Sales;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Quotation extends Model
{
    use HasFactory,SoftDeletes;
    // *** REACT FRONT-END QUOTATION *** // 
    // **1** ALL QUOTATION
    public static function getQuotation($user) {
        try{
            $list         = [];
            $business_id  = $user->business_id;
            $quotation    = \App\Quotation::where("business_id",$business_id)->get();
            if(count($quotation)==0) { return false; }
            foreach($quotation as $i){ $list[] = $i; }
            return $list;
        }catch(Exception $e){
            return false;
        }
    }
    // **2** CREATE QUOTATION
    public static function createQuotation($user,$data) {
        try{
            $business_id             = $user->business_id;
            $list["type"]            = ["value" => $data["type"]];
            return $list;
        }catch(Exception $e){
            return false;
        }
    }
    // **3** EDIT QUOTATION
    public static function editQuotation($user,$data,$id) {
        try{
            $business_id             = $user->business_id;
            $quotation               = \App\Transaction::find($id);
            $list["quotation"]       = $quotation;
            if(!$quotation){ return false; }
            return $list;
        }catch(Exception $e){
            return false;
        } 
    }
    // **4** STORE QUOTATION
    public static function storeQuotation($user,$data) {
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
            $output              = Quotation::createNewQuotation($user,$data);
            if($output == false){ return false; } 
            \DB::commit();
            return $output;
        }catch(Exception $e){
            return false;
        }
    }
    // **5** UPDATE QUOTATION
    public static function updateQuotation($user,$data,$id) {
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
            $output              = Quotation::updateOldQuotation($user,$data,$id);
            \DB::commit();
            return $output;
        }catch(Exception $e){
            return false;
        }
    }
    // **6** DELETE QUOTATION
    public static function deleteQuotation($user,$id) {
        try{
            \DB::beginTransaction();
            $business_id  = $user->business_id;
            $quotation    = \App\Transaction::find($id);
            if(!$quotation){ return false; }
            $quotation->delete();
            \DB::commit();
            return true;
        }catch(Exception $e){
            return false;
        }
    }


    // ****** MAIN FUNCTIONS 
    // **1** CREATE QUOTATION
    public static function createNewQuotation($user,$data) {
       try{
            $business_id   = $user->business_id;
            $quotation     = new \App\Transaction();
            $quotation->save();
            return true; 
        }catch(Exception $e){
            return false;
        }
    }
    // **2** UPDATE QUOTATION
    public static function updateOldQuotation($user,$data,$id) {
       try{
            $business_id   = $user->business_id;
            $quotation         = \App\Transaction::find($id);
            $quotation->update();
            return true; 
        }catch(Exception $e){
            return false;
        }
    }


}
