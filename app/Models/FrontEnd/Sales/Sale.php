<?php

namespace App\Models\FrontEnd\Sales;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sale extends Model
{
    use HasFactory,SoftDeletes;
    // *** REACT FRONT-END SALES *** // 
    // **1** ALL SALES
    public static function getSale($user) {
        try{
            $list        = [];
            $business_id = $user->business_id;
            $sales       = \App\Transaction::where("business_id",$business_id)->where("type","sale")->get();
            if(count($sales)==0) { return false; }
            foreach($sales as $i){ $list[] = $i; }
            return $list;
        }catch(Exception $e){
            return false;
        }
    }
    // **2** CREATE SALES
    public static function createSale($user,$data) {
        try{
            $business_id             = $user->business_id;
            $list["type"]            = ["value" => $data["type"]];
            return $list;
        }catch(Exception $e){
            return false;
        }
    }
    // **3** EDIT SALES
    public static function editSale($user,$data,$id) {
        try{
            $business_id            = $user->business_id;
            $sale                   = \App\Transaction::find($id);
            $list["sale"]           = $sale;
            if(!$sale){ return false; }
            return $list;
        }catch(Exception $e){
            return false;
        } 
    }
    // **4** STORE SALES
    public static function storeSale($user,$data) {
        try{
            \DB::beginTransaction();
            $business_id         = $user->business_id;
            $data["name"]        = implode(" ", [$data["first_name"]]);
            $data["business_id"] = $business_id;
            $data["created_by"]  = $user->id;
            if(!empty($data["contact_id"]) && $data["contact_id"] != ""){
                $old             = \App\Contact::where("contact_id",$data["contact_id"])->where("business_id",$data["business_id"])->first();
                if($old){return false;}
            }
            $output              = Sale::createNewSale($user,$data);
            if($output == false){ return false; } 
            \DB::commit();
            return $output;
        }catch(Exception $e){
            return false;
        }
    }
    // **5** UPDATE SALES
    public static function updateSale($user,$data,$id) {
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
            $output              = Sale::updateOldSale($user,$data,$id);
            \DB::commit();
            return $output;
        }catch(Exception $e){
            return false;
        }
    }
    // **6** DELETE SALES
    public static function deleteSale($user,$id) {
        try{
            \DB::beginTransaction();
            $business_id = $user->business_id;
            $sale        = \App\Transaction::find($id);
            if(!$sale){ return false; }
            $sale->delete();
            \DB::commit();
            return true;
        }catch(Exception $e){
            return false;
        }
    }


    // ****** MAIN FUNCTIONS 
    // **1** CREATE SALES
    public static function createNewSale($user,$data) {
       try{
            $business_id   = $user->business_id;
            $sale          = new \App\Transaction();
            $sale->save();
            return true; 
        }catch(Exception $e){
            return false;
        }
    }
    // **2** UPDATE SALES
    public static function updateOldSale($user,$data,$id) {
       try{
            $business_id   = $user->business_id;
            $sale          = \App\Transaction::find($id);
            $sale->update();
            return true; 
        }catch(Exception $e){
            return false;
        }
    }


}
