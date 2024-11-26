<?php

namespace App\Models\FrontEnd\Contacts;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerGroup extends Model
{
    use HasFactory,SoftDeletes;
    // *** REACT FRONT-END CUSTOMER GROUP *** // 
    // **1** ALL CUSTOMER GROUP
    public static function getCustomerGroup($user) {
        try{
            $list               = [];
            $business_id        = $user->business_id;
            $customerGroup      = \App\CustomerGroup::where("business_id",$business_id)->get();
            if(count($customerGroup)==0) { return false; }
            foreach($customerGroup as $i){ $list[] = $i; }
            return $list;
        }catch(Exception $e){
            return false;
        }
    }
    // **2** CREATE CUSTOMER GROUP
    public static function createCustomerGroup($user,$data) {
        try{
            $list               = [];
            $business_id        = $user->business_id;
            $sales              = \App\SellingPriceGroup::get();
            foreach($sales as $e){
                $list[]   = [
                    "id"    => $e->id,
                    "value" => $e->name
                ];
            } 
            $data["sales_price_group"] = $list;
            return $data;
        }catch(Exception $e){
            return false;
        }
    }
    // **3** EDIT CUSTOMER GROUP
    public static function editCustomerGroup($user,$data,$id) {
        try{
            $business_id             = $user->business_id;
            $customerGroup           = \App\CustomerGroup::find($id);
            $list["info"]            = $customerGroup;
            $sales                   = \App\SellingPriceGroup::get();
            foreach($sales as $e){
                $line[]   = [
                    "id"    => $e->id,
                    "value" => $e->name
                ];
            } 
            $list["sales_price_group"] = $line;
            $list["price_collection_price"] = [
                [
                    "key"   => "percentage",
                    "value" => "Percentage"
                ],
                [
                    "key"   => "selling_price_group",
                    "value" => "Sales Price Group"
                ]
            ];
            if(!$customerGroup){ return false; }
            return $list;
        }catch(Exception $e){
            return false;
        } 
    }
    // **4** STORE CUSTOMER GROUP
    public static function storeCustomerGroup($user,$data) {
        try{
            \DB::beginTransaction();
            if(!empty($data["name"]) && $data["name"] != ""){
                $old             = \App\CustomerGroup::where("name",$data["name"])->where("business_id",$data["business_id"])->first();
                if($old){return false;}
            }
            $output              = CustomerGroup::createNewCustomerGroup($user,$data);
            if($output == false){ return false; } 
            \DB::commit();
            return $output;
        }catch(Exception $e){
            return false;
        }
    }
    // **5** UPDATE CUSTOMER GROUP
    public static function updateCustomerGroup($user,$data,$id) {
        try{
            \DB::beginTransaction();
            if(!empty($data["name"]) && $data["name"] != ""){
                $old             = \App\CustomerGroup::where("name",$data["name"])->where("business_id",$data["business_id"])->first();
                if($old){return false;}
            }
            $output              = CustomerGroup::updateOldCustomerGroup($user,$data,$id);
            \DB::commit();
            return $output;
        }catch(Exception $e){
            return false;
        }
    }
    // **6** DELETE CUSTOMER GROUP
    public static function deleteCustomerGroup($user,$id) {
        try{
            \DB::beginTransaction();
            $business_id     = $user->business_id;
            $customerGroup   = \App\CustomerGroup::find($id);
            if(!$customerGroup){ return false; }
            $customerGroup->delete();
            \DB::commit();
            return true;
        }catch(Exception $e){
            return false;
        }
    }

    // ****** MAIN FUNCTIONS 
    // **1** CREATE CUSTOMER GROUP
    public static function createNewCustomerGroup($user,$data) {
        try{
            $customerGroup                           = new \App\CustomerGroup();
            $customerGroup->business_id              = $data["business_id"];
            $customerGroup->name                     = $data["name"];
            $customerGroup->amount                   = $data["amount"]??0;
            $customerGroup->price_calculation_type   = $data["price_calculation_type"];
            $customerGroup->selling_price_group_id	 = $data["selling_price_group_id"];
            $customerGroup->created_by           	 = $user->id ;
            $customerGroup->save();
            return true; 
        }catch(Exception $e){
            return false;
        }
    }
    // **2** UPDATE CUSTOMER GROUP
    public static function updateOldCustomerGroup($user,$data,$id) {
        try{
            $customerGroup                           = \App\CustomerGroup::find($id);
            $customerGroup->name                     = $data["name"];
            $customerGroup->amount                   = $data["amount"]??0;
            $customerGroup->price_calculation_type   = $data["price_calculation_type"];
            $customerGroup->selling_price_group_id	 = $data["selling_price_group_id"];
            $customerGroup->update();
            return true; 
        }catch(Exception $e){
            return false;
        }
    }


}
