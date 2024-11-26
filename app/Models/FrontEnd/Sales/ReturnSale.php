<?php

namespace App\Models\FrontEnd\Sales;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReturnSale extends Model
{
    use HasFactory,SoftDeletes;
    // *** REACT FRONT-END RETURN SALE *** // 
    // **1** ALL RETURN SALE
    public static function getReturnSale($user) {
        try{
            $list             = [];
            $business_id      = $user->business_id;
            $returnSale       = \App\Transaction::where("business_id",$business_id)->get();
            if(count($returnSale)==0) { return false; }
            foreach($returnSale as $i){ $list[] = $i; }
            return $list;
        }catch(Exception $e){
            return false;
        }
    }
    // **2** CREATE RETURN SALE
    public static function createReturnSale($user,$data) {
        try{
            $business_id             = $user->business_id;
            $list["type"]            = ["value" => $data["type"]];
            return $list;
        }catch(Exception $e){
            return false;
        }
    }
    // **3** EDIT RETURN SALE
    public static function editReturnSale($user,$data,$id) {
        try{
            $business_id             = $user->business_id;
            $returnSale              = \App\Transaction::find($id);
            $list["returnSale"]      = $returnSale;
            if(!$returnSale){ return false; }
            return $list;
        }catch(Exception $e){
            return false;
        } 
    }
    // **4** STORE RETURN SALE
    public static function storeReturnSale($user,$data) {
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
            $output              = ReturnSale::createNewReturnSale($user,$data);
            if($output == false){ return false; } 
            \DB::commit();
            return $output;
        }catch(Exception $e){
            return false;
        }
    }
    // **5** UPDATE RETURN SALE
    public static function updateReturnSale($user,$data,$id) {
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
            $output              = ReturnSale::updateOldReturnSale($user,$data,$id);
            \DB::commit();
            return $output;
        }catch(Exception $e){
            return false;
        }
    }
    // **6** DELETE RETURN SALE
    public static function deleteReturnSale($user,$id) {
        try{
            \DB::beginTransaction();
            $business_id  = $user->business_id;
            $returnSale   = \App\Transaction::find($id);
            if(!$returnSale){ return false; }
            $returnSale->delete();
            \DB::commit();
            return true;
        }catch(Exception $e){
            return false;
        }
    }


    // ****** MAIN FUNCTIONS 
    // **1** CREATE RETURN SALE
    public static function createNewReturnSale($user,$data) {
       try{
            $business_id   = $user->business_id;
            $returnSale    = new \App\Transaction();
            $returnSale->save();
            return true; 
        }catch(Exception $e){
            return false;
        }
    }
    // **2** UPDATE RETURN SALE
    public static function updateOldReturnSale($user,$data,$id) {
       try{
            $business_id   = $user->business_id;
            $returnSale    = \App\Transaction::find($id);
            $returnSale->update();
            return true; 
        }catch(Exception $e){
            return false;
        }
    }


}
