<?php

namespace App\Models\FrontEnd\Settings;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupTax extends Model
{
    use HasFactory;
         

    // *** REACT FRONT-END GROUP TAX *** // 
    // **1** ALL GROUP TAX  
    public static function getGroupTax($user) {
        try{
            $list        = [];
            $business_id = $user->business_id;
            $taxRate     = \App\TaxRate::where("business_id",$business_id)->where("is_tax_group",1)->get();
            if(count($taxRate)==0) { return false; }
            foreach($taxRate as $i){ 
                $user    = \App\User::find($i->created_by);
                $groups  = \App\GroupSubTax::where("group_tax_id",$i->id)->get();
                $child   = [];
                foreach($groups as $ie){
                    $child[] = [
                        "group_tax_id"        => $ie->group_tax_id,
                        "tax_id"              => $ie->tax_id
                    ];
                }
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
                    "child"                   => $child,
                ];
                
            }
            return $list;
        }catch(Exception $e){
            return false;
        }
    }
    // **2** CREATE GROUP TAX 
    public static function createGroupTax($user,$data) {
        try{
            return true;
        }catch(Exception $e){
            return false;
        }
    }
    // **3** EDIT GROUP TAX  
    public static function editGroupTax($user,$data,$id) {
        try{
            $business_id             = $user->business_id;
            $taxRate                 = \App\TaxRate::find($id);
            if(!$taxRate){ return false; }
            if($taxRate->is_tax_group == 0){ return false; }
            $user                    = \App\User::find($taxRate->created_by);
            $groups                  = \App\GroupSubTax::where("group_tax_id",$taxRate->id)->get();
            $child                   = [];
            foreach($groups as $ie){
                $child[]  = [
                    "group_tax_id"        => $ie->group_tax_id,
                    "tax_id"              => $ie->tax_id
                ];
            }
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
                "child"                   => $child,
            ];
            $list["info"]            = $item;
            return $list;
        }catch(Exception $e){
            return false;
        } 
    }
    // **4** STORE GROUP TAX  
    public static function storeGroupTax($user,$data,$request) {
        try{
            \DB::beginTransaction();
            if(!empty($data["name"]) && $data["name"] != ""){
                $old             = \App\TaxRate::where("name",$data["name"])->where("is_tax_group",1)->where("business_id",$data["business_id"])->first();
                if($old){return "old";}
            }
            $output              = GroupTax::createNewGroupTax($user,$data,$request);
            if($output == false){ return "false"; } 
            \DB::commit();
            return "true";
        }catch(Exception $e){
            return "failed";
        }
    }
    // **5** UPDATE GROUP TAX  
    public static function updateGroupTax($user,$data,$id,$request) {
        try{
            \DB::beginTransaction();
            if(!empty($data["name"]) && $data["name"] != ""){
                $old             = \App\TaxRate::where("name",$data["name"])->where("is_tax_group",1)->where("id","!=",$id)->where("business_id",$data["business_id"])->first();
                if($old){return "old";}
            }
            $output              = GroupTax::updateOldGroupTax($user,$data,$id,$request);
            \DB::commit();
            return "true";
        }catch(Exception $e){
            return "false";
        }
    }
    // **6** DELETE GROUP TAX  
    public static function deleteGroupTax($user,$id) {
        try{
            \DB::beginTransaction();
            $business_id = $user->business_id;
            $taxRate     = \App\TaxRate::find($id);
            if(!$taxRate ){ return "no"; }
            $groups                  = \App\GroupSubTax::where("group_tax_id",$taxRate->id)->get();
            foreach($groups as $ie){
                $ie->delete();
            }
            $taxRate->delete();
            \DB::commit();
            return "true";
        }catch(Exception $e){
            return false;
        }
    }


    // ****** MAIN FUNCTIONS 
    // **1** CREATE GROUP TAX   
    public static function createNewGroupTax($user,$data,$request) {
       try{
            $business_id                       = $user->business_id;
            $taxRate                           = new \App\TaxRate(); 
            $taxRate->business_id              = $business_id;
            $taxRate->name                     = isset($data["name"])?$data["name"]:"";
            $taxRate->amount                   = isset($data["amount"])?$data["amount"]:0;
            $taxRate->created_by               = $user->id;
            $taxRate->is_tax_group             = 1;
            $taxRate->for_tax_group            = isset($data["for_tax_group"])?$data["for_tax_group"]:0;
            $taxRate->woocommerce_tax_rate_id  = isset($data["woocommerce_tax_rate_id"])?$data["woocommerce_tax_rate_id"]:null;
            $taxRate->count_months             = isset($data["count_months"])?$data["count_months"]:null;
            $taxRate->is_composite             = isset($data["is_composite"])?$data["is_composite"]:0;
            $taxRate->save();
        
            $taxRate->sub_taxes()->sync($data["taxes"]);
            return true; 
        }catch(Exception $e){
            return false;
        }
    }
    // **2** UPDATE GROUP TAX F
    public static function updateOldGroupTax($user,$data,$id,$request) {
       try{
            $business_id                       = $user->business_id;
            $taxRate                           = \App\TaxRate::find($id); 
            $taxRate->business_id              = $business_id;
            $taxRate->name                     = isset($data["name"])?$data["name"]:"";
            $taxRate->amount                   = isset($data["amount"])?$data["amount"]:0;
            $taxRate->created_by               = $user->id;
            $taxRate->is_tax_group             = 1;
            $taxRate->for_tax_group            = isset($data["for_tax_group"])?$data["for_tax_group"]:0;
            $taxRate->woocommerce_tax_rate_id  = isset($data["woocommerce_tax_rate_id"])?$data["woocommerce_tax_rate_id"]:null;
            $taxRate->count_months             = isset($data["count_months"])?$data["count_months"]:null;
            $taxRate->is_composite             = isset($data["is_composite"])?$data["is_composite"]:0;
            $taxRate->update();
            $taxRate->sub_taxes()->sync($data["taxes"]);
            return true; 
        }catch(Exception $e){
            return false;
        }
    }

}
