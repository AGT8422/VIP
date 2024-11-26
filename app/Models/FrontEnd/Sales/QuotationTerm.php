<?php

namespace App\Models\FrontEnd\Sales;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuotationTerm extends Model
{
    use HasFactory,SoftDeletes;
    // *** REACT FRONT-END QUOTATION TERM  *** // 
    // **1** ALL QUOTATION TERM 
    public static function getQuotationTerm($user) {
        try{
            $list        = [];
            $business_id = $user->business_id;
            $brand       = \App\Models\QuatationTerm::where("business_id",$business_id)->get();
            if(count($brand)==0) { return false; }
            foreach($brand as $i){ $list[] = $i; }
            return $list;
        }catch(Exception $e){
            return false;
        }
    }
    // **2** CREATE QUOTATION TERM 
    public static function createQuotationTerm($user,$data) {
        try{
            $business_id             = $user->business_id;
            $list["type"]            = ["value" => $data["type"]];
            return $list;
        }catch(Exception $e){
            return false;
        }
    }
    // **3** EDIT QUOTATION TERM 
    public static function editQuotationTerm($user,$data,$id) {
        try{
            $business_id             = $user->business_id;
            $brand                   = \App\QuotationTerm::find($id);
            $list["brand"]           = $brand;
            if(!$brand){ return false; }
            return $list;
        }catch(Exception $e){
            return false;
        } 
    }
    // **4** STORE QUOTATION TERM 
    public static function storeQuotationTerm($user,$data) {
        try{
            \DB::beginTransaction();
            $business_id         = $user->business_id;
            $data["name"]        = implode(" ", [$data["first_name"]]);
            $data["business_id"] = $business_id;
            $data["created_by"]  = $user->id;
            if(!empty($data["contact_id"]) && $data["contact_id"] != ""){
                $old             = \App\Models\QuatationTerm::where("contact_id",$data["contact_id"])->where("business_id",$data["business_id"])->first();
                if($old){return false;}
            }
            $output              = QuotationTerm::createNewQuotationTerm($user,$data);
            if($output == false){ return false; } 
            \DB::commit();
            return $output;
        }catch(Exception $e){
            return false;
        }
    }
    // **5** UPDATE QUOTATION TERM 
    public static function updateQuotationTerm($user,$data,$id) {
        try{
            \DB::beginTransaction();
            $business_id         = $user->business_id;
            $data["name"]        = implode(" ", [$data["first_name"]]);
            $data["business_id"] = $business_id;
            $data["created_by"]  = $user->id;
            if(!empty($data["contact_id"]) && $data["contact_id"] != ""){
                $old             = \App\Models\QuatationTerm::where("contact_id",$data["contact_id"])->where("id","!=",$id)->where("business_id",$data["business_id"])->first();
                if($old){return false;}
            }
            $output              = QuotationTerm::updateOldQuotationTerm($user,$data,$id);
            \DB::commit();
            return $output;
        }catch(Exception $e){
            return false;
        }
    }
    // **6** DELETE QUOTATION TERM 
    public static function deleteQuotationTerm($user,$id) {
        try{
            \DB::beginTransaction();
            $business_id   = $user->business_id;
            $quotationTerm = \App\Models\QuatationTerm::find($id);
            if(!$quotationTerm){ return false; }
            $quotationTerm->delete();
            \DB::commit();
            return true;
        }catch(Exception $e){
            return false;
        }
    }


    // ****** MAIN FUNCTIONS 
    // **1** CREATE QUOTATION TERM 
    public static function createNewQuotationTerm($user,$data) {
       try{
            $business_id     = $user->business_id;
            $quotationTerm   = new \App\Models\QuatationTerm();
            $quotationTerm->save();
            return true; 
        }catch(Exception $e){
            return false;
        }
    }
    // **2** UPDATE QUOTATION TERM 
    public static function updateOldQuotationTerm($user,$data,$id) {
       try{
            $business_id        = $user->business_id;
            $quotationTerm      = \App\Models\QuatationTerm::find($id);
            $quotationTerm->update();
            return true; 
        }catch(Exception $e){
            return false;
        }
    }


}
