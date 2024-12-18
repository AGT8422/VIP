<?php

namespace App\Models\FrontEnd\Products;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalePriceGroup extends Model
{
    use HasFactory,SoftDeletes;
    // *** REACT FRONT-END SALES PRICE GROUP *** // 
    // **1** ALL SALES PRICE GROUP
    public static function getSalesPriceGroup($user) {
        try{
            $business_id           = $user->business_id;
            $data                  = SalePriceGroup::allData("all",null,$business_id);
            if($data == false){ return false;}
            return $data;
        }catch(Exception $e){
            return false;
        }
    }
    // **2** CREATE SALES PRICE GROUP
    public static function createSalesPriceGroup($user,$data) {
        try{
            $business_id             = $user->business_id;
            return true;
        }catch(Exception $e){
            return false;
        }
    }
    // **3** EDIT SALES PRICE GROUP
    public static function editSalesPriceGroup($user,$data,$id) {
        try{
            $business_id                = $user->business_id;
            $sellingPriceGroup          = SalePriceGroup::allData(null,$id,$business_id);
            if($sellingPriceGroup == false){ return false; }
            $list["info"]               = $sellingPriceGroup;
            return $list;
        }catch(Exception $e){
            return false;
        } 
    }
    // **4** STORE SALES PRICE GROUP
    public static function storeSalesPriceGroup($user,$data) {
        try{
            \DB::beginTransaction();
            $data["name"]        = trim($data["name"]);
            $data["business_id"] = $user->business_id;
            if(!empty($data["name"]) && $data["name"] != ""){
                $old             = \App\SellingPriceGroup::where("name",$data["name"])->where("business_id",$data["business_id"])->first();
                if($old){return false;}
            }
            $output              = SalePriceGroup::createNewSalesPriceGroup($user,$data);
            if($output == false){ return false; } 
            \DB::commit();
            return $output;
        }catch(Exception $e){
            return false;
        }
    }
    // **5** UPDATE SALES PRICE GROUP
    public static function updateSalesPriceGroup($user,$data,$id) {
        try{
            \DB::beginTransaction();
            $data["name"]        = trim($data["name"]);
            $data["business_id"] = $user->business_id;
            if(!empty($data["name"]) && $data["name"] != ""){
                $old             = \App\SellingPriceGroup::where("name",$data["name"])->where("id","!=",$id)->where("business_id",$data["business_id"])->first();
                if($old){return false;}
            }
            $output              = SalePriceGroup::updateOldSalesPriceGroup($user,$data,$id);
            \DB::commit();
            return $output;
        }catch(Exception $e){
            return false;
        }
    }
    // **6** DELETE SALES PRICE GROUP
    public static function deleteSalesPriceGroup($user,$id) {
        try{
            \DB::beginTransaction();
            $business_id           = $user->business_id;
            $sellingPriceGroup     = \App\SellingPriceGroup::find($id);
            if(!$sellingPriceGroup){ return false; }
            $sellingPriceGroup->delete();
            \DB::commit();
            return true;
        }catch(Exception $e){
            return false;
        }
    }


    // ****** MAIN FUNCTIONS 
    // **1** CREATE SALES PRICE GROUP
    public static function createNewSalesPriceGroup($user,$data) {
       try{
            $business_id         = $user->business_id;
            $sales               = new \App\SellingPriceGroup();
            $sales->business_id  = $business_id;
            $sales->name         = $data["name"] ;
            $sales->description  = $data["description"];
            $sales->is_active    = 1;
            $sales->save();
            return true; 
        }catch(Exception $e){
            return false;
        }
    }
    // **2** UPDATE SALES PRICE GROUP
    public static function updateOldSalesPriceGroup($user,$data,$id) {
       try{
            $business_id         = $user->business_id;
            $sales               = \App\SellingPriceGroup::find($id);
            if(empty($sales)){ return false; }
            $sales->name         = $data["name"] ;
            $sales->description  = $data["description"];
            $sales->update();
            return true; 
        }catch(Exception $e){
            return false;
        }
    }
    // **3** GET  VARIATIONS
    public static function allData($type=null,$id=null,$business_id) {
        try{
            $list   = [];
            if($type != null){
                $salePrice     = \App\SellingPriceGroup::where("business_id",$business_id)->get();
                if(count($salePrice) == 0 ){ return false; }
                foreach($salePrice as $ie){
                    $list[] = [
                        "id"           => $ie->id,
                        "name"         => $ie->name,
                        "description"  => $ie->description,
                        "is_active"    => $ie->is_active,
                        "date"         => $ie->created_at->format("Y-m-d h:i:s"),
                    ];
                }
            }else{
                $salePrice  = \App\SellingPriceGroup::find($id);
                if(empty($salePrice)){ return false; }
                $list[] = [
                    "id"           => $salePrice->id,
                    "name"         => $salePrice->name,
                    "description"  => $salePrice->description,
                    "is_active"    => $salePrice->is_active,
                    "date"         => $salePrice->created_at->format("Y-m-d h:i:s"),
                ];
                
            }
            return $list; 
        }catch(Exception    $e){
            return false;
        }
    }
    // **4** ACTIVE
    public static function activeSalesPriceGroup($user,$id) {
        try{
            $source = null;
            $msg    = "";
            $salePrice            = \App\SellingPriceGroup::find($id);
             
            if(empty($salePrice)){ 
                $list["status"] = false;
                $list["msg"]    = "There Is No Sales Price Group With This Id";
                return $list; 
            }
            
            $source               = ($salePrice->is_active==0)?1:0;
            $salePrice->is_active = ($salePrice->is_active==0)?1:0 ;
            $salePrice->update();
            
            if($source == 0){
                $msg = "Deactivate This Sale Price Group";
            }elseif($source == 1){
                $msg = "Activate This Sale Price Group";
            }else{
                $msg = "Some Thing Wrong";
            }
            $list["status"] = true;
            $list["msg"]    = $msg;
            return $list;; 
        }catch(Exception    $e){
            return false;
        }
    }   
     
   
}
