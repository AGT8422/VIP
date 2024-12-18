<?php

namespace App\Models\FrontEnd\AccountTypes;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\FrontEnd\Utils\GlobalUtil;

class AccountType extends Model
{
    use HasFactory,SoftDeletes;
    // *** REACT FRONT-END ACCOUNT TYPE *** // 
    // **1** ALL ACCOUNT TYPE
    public static function getAccountType($user) {
        try {
            $business_id     = $user->business_id;
            $account         =  AccountType::allData("all",null,$business_id); 
            $require         =  AccountType::requirementFilter($business_id);
            $tree            =  AccountType::accountTree($business_id);
            $tableTree       =  AccountType::accountTableTree($business_id);
            $li              =  json_decode(json_encode($tableTree));
            $tableTreeFinal  = [];
            foreach($li as $ii){
                $tableTreeFinal[] = [
                    "id"                     => $ii->id,
                    "name"                   => $ii->name,
                    "code"                   => $ii->code,
                    "parent_account_type_id" => $ii->parent_account_type_id
                ];
                if(count($ii->sub_types)>0){
                    $tableTreeFinal  =  AccountType::accountTableTreeFinal($ii->sub_types,$tableTreeFinal);
                }
            }
       
            if($account == false){ return false;}
            $listed["accounts_type"]    = $account;
            $listed["filter"]      = $require;
            $listed["tree"]        = $tree;
            $listed["table_tree"]  = $tableTreeFinal;
            return $listed;
        }catch(Exception $e){
            return false;
        }
    }
    // **2** CREATE ACCOUNT TYPE
    public static function createAccountType($user,$data) {
        try {
            $business_id        = $user->business_id;
            $require            = AccountType::requirement($business_id);
            return $require;
        }catch(Exception $e){
            return false;
        }
    } 
    // **3** EDIT ACCOUNT TYPE
    public static function editAccountType($user,$data,$id) {
        try {
            $business_id      = $user->business_id;
            $account          = AccountType::allData(null,$id,$business_id);
            if(!$account){ return false; }
            return $account;
        }catch(Exception $e){
            return false;
        } 
    }
    // **4** STORE ACCOUNT TYPE
    public static function storeAccountType($user,$data) {
        try {
            \DB::beginTransaction();
            $business_id         = $user->business_id;
            $data["name"]        = $data["name"] ;
            if(!empty($data["name"]) && $data["name"] != ""){
                $old             = \App\AccountType::where("name",$data["name"])->where("business_id",$business_id)->first();
                if($old){return "old";}
            }
            if(!empty($data["code"]) && $data["code"] != ""){
                $old             = \App\AccountType::where("code",$data["code"])->where("business_id",$business_id)->first();
                if($old){return "oldN";}
            }
            $output              = AccountType::createNewAccountTypeBase($user,$data);
            if($output == false){ return "false"; } 
            \DB::commit();
            return "true";
        }catch(Exception $e){
            return "false";
        }
    }
    // **5** UPDATE ACCOUNT TYPE
    public static function updateAccountType($user,$data,$id) {
        try {
            \DB::beginTransaction();
            $business_id         = $user->business_id;
            $data["name"]        = $data["name"];
            if(!empty($data["name"]) && $data["name"] != ""){
                $old             = \App\AccountType::where("name",$data["name"])->where("id","!=",$id)->where("business_id",$business_id)->first();
                if($old){return "old";}
            }
            if(!empty($data["code"]) && $data["code"] != ""){
                $old             = \App\AccountType::where("code",$data["code"])->where("id","!=",$id)->where("business_id",$business_id)->first();
                if($old){return "oldN";}
            }
            $output              = AccountType::updateOldAccountTypeBase($user,$data,$id);
            if($output != "true"){
                return $output;
            } 
            \DB::commit();
            return "true";
        }catch(Exception $e){
            return "false";
        }
    }
    // **6** DELETE ACCOUNT TYPE 
    public static function deleteAccountType($user,$id) {
        try {
            \DB::beginTransaction();
            $business_id     = $user->business_id;
            $account         = \App\AccountType::find($id);
            if(!$account){ return "false"; }
            $check           = GlobalUtil::checkAccountType($id);
            if($check == true){ return "related"; }
            $account->delete();
            \DB::commit();
            return "true";
        }catch(Exception $e){
            return "false";
        }
    }
    // **7** ALL ACCOUNT TYPE TREE
    public static function getAccountTypeTree($user) {
        try {
            $business_id   = $user->business_id; 
            $tree          =  AccountType::accountTree($business_id);
            // $tableTree     =  Account::accountTableTree($business_id);
            return $tree;
        }catch(Exception $e){
            return false;
        }
    }

    // **8** CREATE ACCOUNT TYPE BASE
    public static function createNewAccountTypeBase($user,$data) {
        try {
            $business                            = \App\Business::find($user->business_id);
            $accountType                         = new \App\AccountType();
            $accountType->business_id            = $business->id; 
            $accountType->name                   = (isset($data["name"]))?$data["name"]:null; 
            $accountType->code                   = (isset($data["code"]))?$data["code"]:null; 
            $accountType->parent_account_type_id = (isset($data["parent_account_type_id"]))?$data["parent_account_type_id"]:null; 
            $accountType->active                 = 1; 
            $accountType->save(); 
            return "true"; 
        }catch(Exception $e){
            return "false";
        }
    }
    // **9** UPDATE ACCOUNT TYPE BASE
    public static function updateOldAccountTypeBase($user,$data,$id) {
        try {
            $business                            = \App\Business::find($user->business_id);
            $accountType                         = \App\AccountType::find($id);
            $accountType->name                   = (isset($data["name"]))?$data["name"]:null; 
            $accountType->code                   = (isset($data["code"]))?$data["code"]:null; 
            $accountType->parent_account_type_id = (isset($data["parent_account_type_id"]))?$data["parent_account_type_id"]:null; 
            $accountType->update(); 
            return "true";
        }catch(Exception $e){
            return "false";
        }
    }
    // **10** GET ACCOUNT TYPE  
    public static function allData($type=null,$id=null,$business_id) {
        try{
            $list   = [];
            if($type != null){
                $query     = \App\AccountType::where("business_id",$business_id);
                if(request()->input("name")){
                    $name   = request()->input("name");
                    $query->where("name","LIKE",'%'.$name.'%');  
                }
                if(request()->input("account_number")){
                    $account_number   = request()->input("account_number");
                    $query->where("account_number",$account_number);  
                }
                if(request()->input("main_account")){
                    $main_account   = request()->input("main_account");
                    $query->whereHas("account_type",function($q) use($main_account){
                        $q->where("parent_account_type_id",$main_account);
                    });  
                }
                if( request()->input('account_type') != null){
                    $type_account   = request()->input("account_type");
                    $query->whereHas("account_type",function($q) use($type_account){
                        $q->where("sub_parent_id",$type_account);
                        $q->orWhere("parent_account_type_id",$type_account);
                        $q->orWhere("id",$type_account);
                    }); 
                }
                if( request()->input('account_sub_type') != null  ){
                    $account_sub_type   = request()->input("account_sub_type");
                    $query->where('account_type_id', $account_sub_type);
                }
                $account = $query->get();
                if(count($account) == 0 ){ return false; }
                foreach($account as $ie){
                    $debit    = \App\AccountTransaction::whereHas("account",function($q) use($ie){$q->where("account_type_id",$ie->id);$q->orWhereHas("account_type",function($query)use($ie){$query->where("parent_account_type_id",$ie->id);$query->orWhere("id",$ie->id);});})->whereNull("for_repeat")->where("type","debit")->sum("amount");
                    $credit   = \App\AccountTransaction::whereHas("account",function($q) use($ie){$q->where("account_type_id",$ie->id);$q->orWhereHas("account_type",function($query)use($ie){$query->where("parent_account_type_id",$ie->id);$query->orWhere("id",$ie->id);});})->whereNull("for_repeat")->where("type","credit")->sum("amount");
                    if(($debit - $credit)== 0){
                        $balance = 0;
                    }else if(($debit - $credit)< 0){
                        $balance = ($debit - $credit);
                    }else{
                        $balance = ($debit - $credit) ;
                    }
                    $list[] = [
                        "id"                 => $ie->id,
                        "name"               => $ie->name,
                        "account_number"     => $ie->code,
                        "parent"             => ($ie->parent_account)?$ie->parent_account->name:"",
                        // "account_type"       => ($ie->account_type)?$ie->account_type->name:"000",
                        "balance"            => abs($balance),
                        "type"               => ($balance>0)?"Debit":(($balance==0)?"":"Credit"),
                        "date"               => $ie->created_at->format("Y-m-d h:i:s a"),
                    ];
                }
            }else{
                $account  = \App\AccountType::find($id);
                $debit    = \App\AccountTransaction::whereHas("account",function($q) use($account){$q->where("account_type_id",$account->id);$q->orWhereHas("account_type",function($query)use($account){$query->where("parent_account_type_id",$account->id);$query->orWhere("id",$account->id);});})->whereNull("for_repeat")->where("type","debit")->sum("amount");
                $credit   = \App\AccountTransaction::whereHas("account",function($q) use($account){$q->where("account_type_id",$account->id);$q->orWhereHas("account_type",function($query)use($account){$query->where("parent_account_type_id",$account->id);$query->orWhere("id",$account->id);});})->whereNull("for_repeat")->where("type","credit")->sum("amount");
                if(($debit - $credit)== 0){
                    $balance = 0;
                }else if(($debit - $credit)< 0){
                    $balance = ($debit - $credit);
                }else{
                    $balance = ($debit - $credit) ;
                }
                if(empty($account)){ return false; }
                $list["info"] = [
                    "id"                 => $account->id,
                    "name"               => $account->name,
                    "account_number"     => $account->code,
                    "parent"             => ($account->parent_account)?$account->parent_account->name:"",
                    // "account_type"       => ($account->account_type)?$account->account_type->name:"000",
                    "balance"            => abs($balance),
                    "type"               => ($balance>0)?"Debit":(($balance==0)?"":"Credit"),
                    "date"               => $account->created_at->format("Y-m-d h:i:s a"),
                ];
                $list["require"]         =  AccountType::requirement($business_id);
            }
            return $list; 
        }catch(Exception $e){
            return false;
        }
    }
    // **11** GET ACCOUNT TYPE
    public static function requirement($business_id){
        $list_1          = [];$list_2 = [];$list_3  = [];$list = [];
        $account         = \App\AccountType::where("business_id",$business_id)->get();
        foreach($account as $e){
            $list_1[] = [
                "id"   => $e->id,
                "name" => $e->name . " | " . $e->code,
            ];
        }
        $list["accounts"]  = $list_1;
        return  $list;
    }
    // **12** GET FILTER ACCOUNT TYPE
    public static function requirementFilter($business_id){
        $list_1          = [];$list_2 = [];$list_3  = [];$list = [];
        $account         = \App\AccountType::where("business_id",$business_id)->get();
        foreach($account as $e){
            if($e->parent_account_type_id == null){
                $list_1[] = [
                    "id"   => $e->id,
                    "name" => $e->name . " | " . $e->code,
                ];
                $account_type = \App\AccountType::where("parent_account_type_id",$e->id)->get();
                if(count($account_type)>0){
                    foreach($account_type as $item){
                        if($item->sub_parent_id == null){
                            // $child = \App\AccountType::find($item->id);
                            $child = \App\AccountType::where("sub_parent_id",$item->id)->orWhere("parent_account_type_id",$item->id)->first();
                            
                            if(!empty($child)){
                                $list_2[] = [
                                    "id"          => $item->id,
                                    "parent_id"   => $item->parent_account_type_id,
                                    "name"        => $item->name . " | " . $item->code,
                                ];
                            }else{
                                if($item->parent_account_type_id != null   ){
                                    $list_2[] = [
                                        "id"          => $item->id,
                                        "parent_id"   => $item->parent_account_type_id,
                                        "name"        => $item->name . " | " . $item->code,
                                    ];  
                                }
                            }
                             
                        }else{
                            $list_2[] = [
                                "id"          => $item->id,
                                "parent_id"   => $item->sub_parent_id,
                                "name"        => $item->name . " | " . $item->code,
                            ];
                        } 
                        
                    }
                }
            }else{
                
                $account_sub_type = \App\AccountType::where("sub_parent_id",$e->id)->orWhere("parent_account_type_id",$e->id)->get();
                if(count($account_sub_type)>0){
                    foreach($account_sub_type as $item){
                        if($item->sub_parent_id != null){
                                $list_3[] = [
                                    "id"          => $item->id,
                                    "parent_id"   => $item->sub_parent_id,
                                    "name"        => $item->name . " | " . $item->code,
                                ];
                               
                        }elseif($item->parent_account_type_id != null){ 
                                $list_3[] = [
                                    "id"          => $item->id,
                                    "parent_id"   => $item->parent_account_type_id,
                                    "name"        => $item->name . " | " . $item->code,
                                ];
                                
                              
                        }
                    }
                    //!--------!\\
                    //TODO-----!\\
                }
            }
        }
        $list["main_accounts"]     = $list_1;
        $list["account_type"]      = $list_2;
        $list["account_sub_type"]  = $list_3;
        return  $list;
    }
    // **13** GET  ACCOUNT TYPE TREE
    public static function accountTree($business_id){
        $dd = GlobalUtil::toTree("account_type",$business_id); 
        return $dd;
    }
    // **14** GET  ACCOUNT TYPE TABLE TREE
    public static function accountTableTree($business_id){
        $dd = GlobalUtil::toTreeTable("account_type",$business_id); 
        return $dd;
    }
    // **14** GET  ACCOUNT TYPE TABLE TREE
    public static function accountTableTreeFinal($list,$li){
        foreach($list as $i){
            $li[] = [
                "id"                     => $i->id,
                "name"                   => $i->name,
                "code"                   => $i->code,
                "parent_account_type_id" => $i->parent_account_type_id
            ];
            if(count($i->sub_types)>0){
                $li =  self::accountTableTreeFinal($i->sub_types,$li);
            }
        }
        
        return $li;
    }
}
