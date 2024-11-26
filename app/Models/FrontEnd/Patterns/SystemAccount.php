<?php

namespace App\Models\FrontEnd\Patterns;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\FrontEnd\Utils\GlobalUtil;

class SystemAccount extends Model
{
    use HasFactory,SoftDeletes;
    // *** REACT FRONT-END SYSTEM ACCOUNT *** // 
    // **1** ALL SYSTEM ACCOUNT
    public static function getSystemAccount($user) {
        try{
            $list               = [];
            $business_id        = $user->business_id;
            $systemAccount      = SystemAccount::allData("all",null,$business_id); 
            if($systemAccount == false){ return false;}
            return $systemAccount;
            return $list;
        }catch(Exception $e){
            return false;
        }
    }
    // **2** CREATE SYSTEM ACCOUNT
    public static function createSystemAccount($user,$data) {
        try{
            $business_id        = $user->business_id;
            $account            = SystemAccount::account($business_id);
            return $account ;
        }catch(Exception $e){
            return false;
        }
    }
    // **3** EDIT  SYSTEM ACCOUNT
    public static function editSystemAccount($user,$data,$id) {
        try{
            $business_id            = $user->business_id;
            $systemAccount          = SystemAccount::allData(null,$id,$business_id);
            if(!$systemAccount){ return false; }
            return $systemAccount;
        }catch(Exception $e){
            return false;
        } 
    }
    // **4** STORE SYSTEM ACCOUNT
    public static function storeSystemAccount($user,$data) {
        try{
            \DB::beginTransaction();
            $business_id         = $user->business_id;
            $data["business_id"] = $business_id;
            $data["created_by"]  = $user->id;
            $output              = SystemAccount::createNewSystemAccount($user,$data);
            if($output == false){ return false; } 
            \DB::commit();
            return $output;
        }catch(Exception $e){
            return false;
        }
    }
    // **5** UPDATE SYSTEM ACCOUNT
    public static function updateSystemAccount($user,$data,$id) {
        try{
            \DB::beginTransaction();
            $business_id         = $user->business_id;
            $data["business_id"] = $business_id;
            $data["created_by"]  = $user->id;
            $output              = SystemAccount::updateOldSystemAccount($user,$data,$id);
            \DB::commit();
            return $output;
        }catch(Exception $e){
            return false;
        }
    }
    // **6** DELETE SYSTEM ACCOUNT
    public static function deleteSystemAccount($user,$id) {
        try{
            \DB::beginTransaction();
            $business_id      = $user->business_id;
            $systemAccount    = \App\Models\SystemAccount::find($id);
            if(!$systemAccount){ return "false"; }
            $pattern          = $systemAccount->pattern_id;
            $check            = GlobalUtil::checkPattern($pattern);
            if($check){ return "cannot"; }
            $systemAccount->delete();
            \DB::commit();
            return "true";
        }catch(Exception $e){
            return "false";
        }
    }

    // ****** MAIN FUNCTIONS 
    // **1** CREATE SYSTEM ACCOUNT
    public static function createNewSystemAccount($user,$data) {
        try{
            $business_id                        = $user->business_id;
            $systemAccount                      =  \App\Models\SystemAccount::where('business_id',$business_id)->where("pattern_id",$data["pattern_id"])->first();
            if (empty($systemAccount)) {
                $systemAccount                  =  new \App\Models\SystemAccount;
            }
            $systemAccount->pattern_id          =  $data["pattern_id"];
            $systemAccount->business_id         =  $business_id;
            $systemAccount->purchase            =  $data["purchase"];
            $systemAccount->purchase_tax        =  $data["purchase_tax"];
            $systemAccount->sale                =  $data["sale"];
            $systemAccount->sale_tax            =  $data["sale_tax"];
            $systemAccount->cheque_debit        =  $data["cheque_debit"];
            $systemAccount->cheque_collection   =  $data["cheque_collection"];
            $systemAccount->journal_expense_tax =  $data["journal_expense_tax"];
            $systemAccount->sale_return         =  $data["sale_return"];
            $systemAccount->sale_discount       =  $data["sale_discount"];
            $systemAccount->purchase_return     =  $data["purchase_return"];
            $systemAccount->purchase_discount   =  $data["purchase_discount"];
            $systemAccount->save();
            return true; 
        }catch(Exception $e){
            return false;
        }
    }
    // **2** UPDATE SYSTEM ACCOUNT
    public static function updateOldSystemAccount($user,$data,$id) {
        try{
            $business_id                        =  $user->business_id;
            $systemAccount                      =  \App\Models\SystemAccount::find($id);
            $systemAccount->business_id         =  $business_id;
            $systemAccount->purchase            =  $data["purchase"];
            $systemAccount->purchase_tax        =  $data["purchase_tax"];
            $systemAccount->sale                =  $data["sale"];
            $systemAccount->sale_tax            =  $data["sale_tax"];
            $systemAccount->cheque_debit        =  $data["cheque_debit"];
            $systemAccount->cheque_collection   =  $data["cheque_collection"];
            $systemAccount->journal_expense_tax =  $data["journal_expense_tax"];
            $systemAccount->sale_return         =  $data["sale_return"];
            $systemAccount->sale_discount       =  $data["sale_discount"];
            $systemAccount->purchase_return     =  $data["purchase_return"];
            $systemAccount->purchase_discount   =  $data["purchase_discount"];
            $systemAccount->update();
            return true; 
        }catch(Exception $e){
            return false;
        }
    }
    // **3** GET  SYSTEM ACCOUNT   
    public static function allData($type=null,$id=null,$business_id) {
        try{
            $list   = [];
            if($type != null){
                $pattern     = \App\Models\SystemAccount::where("business_id",$business_id)->get();
                if(count($pattern) == 0 ){ return false; }
                foreach($pattern as $ie){
                    $list[] = [
                        "id"                  => $ie->id,
                        "pattern"             => ($ie->pattern)?$ie->pattern->name:$ie->pattern_id,
                        "purchase"            => ($ie->account_purchase)?$ie->account_purchase->name:$ie->purchase,
                        "purchase_return"     => ($ie->account_purchase_return)?$ie->account_purchase_return->name:$ie->purchase_return,
                        "purchase_tax"        => ($ie->account_purchase_tax)?$ie->account_purchase_tax->name:$ie->purchase_tax,
                        "purchase_discount"   => ($ie->account_purchase_discount)?$ie->account_purchase_discount->name:$ie->purchase_discount,
                        "sale"                => ($ie->account_sale)?$ie->account_sale->name:$ie->sale,
                        "sale_return"         => ($ie->account_sale_return)?$ie->account_sale_return->name:$ie->sale_return,
                        "sale_tax"            => ($ie->account_sale_tax)?$ie->account_sale_tax->name:$ie->sale_tax,
                        "sale_discount"       => ($ie->account_sale_discount)?$ie->account_sale_discount->name:$ie->sale_discount,
                        "cheque_debit"        => ($ie->account_cheque_debit)?$ie->account_cheque_debit->name:$ie->cheque_debit,
                        "cheque_collection"   => ($ie->account_cheque_collection)?$ie->account_cheque_collection->name:$ie->cheque_collection,
                        "journal_expense_tax" => ($ie->account_journal_expense_tax)?$ie->account_journal_expense_tax->name:$ie->journal_expense_tax,
                    ];
                }
            }else{
                $systemAccount  = \App\Models\SystemAccount::find($id);
                if(empty($systemAccount)){ return false; }
                $list["info"] = [
                    "id"                  => $systemAccount->id,
                    "pattern"             => ($systemAccount->pattern)?$systemAccount->pattern->name:$systemAccount->pattern_id,
                    "purchase"            => ($systemAccount->account_purchase)?$systemAccount->account_purchase->name:$systemAccount->purchase,
                    "purchase_return"     => ($systemAccount->account_purchase_return)?$systemAccount->account_purchase_return->name:$systemAccount->purchase_return,
                    "purchase_tax"        => ($systemAccount->account_purchase_tax)?$systemAccount->account_purchase_tax->name:$systemAccount->purchase_tax,
                    "purchase_discount"   => ($systemAccount->account_purchase_discount)?$systemAccount->account_purchase_discount->name:$systemAccount->purchase_discount,
                    "sale"                => ($systemAccount->account_sale)?$systemAccount->account_sale->name:$systemAccount->sale,
                    "sale_return"         => ($systemAccount->account_sale_return)?$systemAccount->account_sale_return->name:$systemAccount->sale_return,
                    "sale_tax"            => ($systemAccount->account_sale_tax)?$systemAccount->account_sale_tax->name:$systemAccount->sale_tax,
                    "sale_discount"       => ($systemAccount->account_sale_discount)?$systemAccount->account_sale_discount->name:$systemAccount->sale_discount,
                    "cheque_debit"        => ($systemAccount->account_cheque_debit)?$systemAccount->account_cheque_debit->name:$systemAccount->cheque_debit,
                    "cheque_collection"   => ($systemAccount->account_cheque_collection)?$systemAccount->account_cheque_collection->name:$systemAccount->cheque_collection,
                    "journal_expense_tax" => ($systemAccount->account_journal_expense_tax)?$systemAccount->account_journal_expense_tax->name:$systemAccount->journal_expense_tax,
                ];
                $list["require"]  = SystemAccount::account($business_id);
            }
            return $list; 
        }catch(Exception $e){
            return false;
        }
    }
    // **4** GET  ACCOUNT List  
    public static function account($business_id) {
        $list     = []; $list_pattern     = []; $list_final     = [];
        $account  = \App\Account::where("business_id",$business_id)->get();
        foreach($account as $ie){
            $list[] = [
                "id"       =>  $ie->id,
                "name"     =>  $ie->name,
            ];
        }
        $pattern  = \App\Models\Pattern::where("business_id",$business_id)->get();
        foreach($pattern as $ie){
            $list_pattern[] = [
                "id"       =>  $ie->id,
                "name"     =>  $ie->name,
            ];
        }
        $list_final["accounts"]  = $list;
        $list_final["patterns"]  = $list_pattern;
        return $list_final;    
    }

}
