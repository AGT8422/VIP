<?php

namespace App\Models\FrontEnd\Settings;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaxRate extends Model
{
    use HasFactory;
     

    // *** REACT FRONT-END TAX RATE *** // 
    // **1** ALL TAX RATE  
    public static function getTaxRate($user) {
        try{
            $list        = [];
            $business_id = $user->business_id;
            $taxRate     = \App\TaxRate::where("business_id",$business_id)->get();
            if(count($taxRate)==0) { return false; }
            foreach($taxRate as $i){ 
                $user   = \App\User::find($i->created_by);
                $list[] = [
                    "id"                      => $i->id, 
                    "name"                    => $i->name, 
                    "amount"                  => $i->amount, 
                    "is_tax_group"            => $i->is_tax_group, 
                    "for_tax_group"           => $i->for_tax_group, 
                    "created_by"              => ($user)?$user->first_name:"",
                    "woocommerce_tax_rate_id" => $i->woocommerce_tax_rate_id,
                    "count_months"            => $i->count_months,
                    "is_composite"            => $i->is_composite,
                ];
                
            }
            return $list;
        }catch(Exception $e){
            return false;
        }
    }
    // **2** CREATE TAX RATE 
    public static function createTaxRate($user,$data) {
        try{
            return true;
        }catch(Exception $e){
            return false;
        }
    }
    // **3** EDIT TAX RATE  
    public static function editTaxRate($user,$data,$id) {
        try{
            $business_id             = $user->business_id;
            $taxRate                 = \App\TaxRate::find($id);
            if(!$taxRate){ return false; }
            $user                    = \App\User::find($taxRate->created_by);
            $item                    = [
                "id"                      => $taxRate->id, 
                "name"                    => $taxRate->name, 
                "amount"                  => $taxRate->amount, 
                "is_tax_group"            => $taxRate->is_tax_group, 
                "for_tax_group"           => $taxRate->for_tax_group, 
                "created_by"              => ($user)?$user->first_name:"",
                "woocommerce_tax_rate_id" => $taxRate->woocommerce_tax_rate_id,
                "count_months"            => $taxRate->count_months,
                "is_composite"            => $taxRate->is_composite,
            ];
            $list["info"]            = $item;
            return $list;
        }catch(Exception $e){
            return false;
        } 
    }
    // **4** STORE TAX RATE  
    public static function storeTaxRate($user,$data,$request) {
        try{
            \DB::beginTransaction();
            if(!empty($data["name"]) && $data["name"] != ""){
                $old             = \App\TaxRate::where("name",$data["name"])->where("business_id",$data["business_id"])->first();
                if($old){return "old";}
            }
            $output              = TaxRate::createNewTaxRate($user,$data,$request);
            if($output == false){ return "false"; } 
            \DB::commit();
            return "true";
        }catch(Exception $e){
            return "failed";
        }
    }
    // **5** UPDATE TAX RATE  
    public static function updateTaxRate($user,$data,$id,$request) {
        try{
            \DB::beginTransaction();
            if(!empty($data["name"]) && $data["name"] != ""){
                $old             = \App\TaxRate::where("name",$data["name"])->where("id","!=",$id)->where("business_id",$data["business_id"])->first();
                if($old){return "old";}
            }
            $output              = TaxRate::updateOldTaxRate($user,$data,$id,$request);
            \DB::commit();
            return "true";
        }catch(Exception $e){
            return "false";
        }
    }
    // **6** DELETE TAX RATE  
    public static function deleteTaxRate($user,$id) {
        try{
            \DB::beginTransaction();
            $business_id = $user->business_id;
            $taxRate     = \App\TaxRate::find($id);
            if(!$taxRate ){ return "no"; }
            $product     = \App\Product::where("tax",$id)->first();
            if(!$taxRate || !empty($product)){ return "false"; }
            $taxRate->delete();
            \DB::commit();
            return "true";
        }catch(Exception $e){
            return false;
        }
    }


    // ****** MAIN FUNCTIONS 
    // **1** CREATE TAX RATE   
    public static function createNewTaxRate($user,$data,$request) {
       try{
            $business_id                       = $user->business_id;
            $taxRate                           = new \App\TaxRate();
            $taxRate->business_id              = $business_id;
            $taxRate->name                     = isset($data["name"])?$data["name"]:"";
            $taxRate->amount                   = isset($data["amount"])?$data["amount"]:0;
            $taxRate->created_by               = $user->id;
            $taxRate->is_tax_group             = 0;
            $taxRate->for_tax_group            = isset($data["for_tax_group"])?$data["for_tax_group"]:0;
            $taxRate->woocommerce_tax_rate_id  = isset($data["woocommerce_tax_rate_id"])?$data["woocommerce_tax_rate_id"]:null;
            $taxRate->count_months             = isset($data["count_months"])?$data["count_months"]:null;
            $taxRate->is_composite             = isset($data["is_composite"])?$data["is_composite"]:0;
            $taxRate->save();
            return true; 
        }catch(Exception $e){
            return false;
        }
    }
    // **2** UPDATE TAX RATE F
    public static function updateOldTaxRate($user,$data,$id,$request) {
       try{
            $business_id                       = $user->business_id;
            $taxRate                           = \App\TaxRate::find($id);
            $taxRate->business_id              = $business_id;
            $taxRate->name                     = isset($data["name"])?$data["name"]:"";
            $taxRate->amount                   = isset($data["amount"])?$data["amount"]:0;
            $taxRate->created_by               = $user->id;
            $taxRate->is_tax_group             = 0;
            $taxRate->for_tax_group            = isset($data["for_tax_group"])?$data["for_tax_group"]:0;
            $taxRate->woocommerce_tax_rate_id  = isset($data["woocommerce_tax_rate_id"])?$data["woocommerce_tax_rate_id"]:null;
            $taxRate->count_months             = isset($data["count_months"])?$data["count_months"]:null;
            $taxRate->is_composite             = isset($data["is_composite"])?$data["is_composite"]:0;
            $taxRate->update();
            return true; 
        }catch(Exception $e){
            return false;
        }
    }

}
