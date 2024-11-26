<?php

namespace App\Models\FrontEnd\Accounts;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\FrontEnd\Utils\GlobalUtil;
use App\Models\FrontEnd\Vouchers\Voucher;
use App\Models\FrontEnd\Vouchers\JournalVoucher;
use App\Models\FrontEnd\Vouchers\ExpenseVoucher;
use App\Models\FrontEnd\Cheques\Cheque;
class Account extends Model
{
    use HasFactory,SoftDeletes;
    // *** REACT FRONT-END ACCOUNT *** // 
    // **1** ALL ACCOUNT
    public static function getAccount($user) {
        try {
            $business_id   = $user->business_id;
            $account       =  Account::allData("all",null,$business_id); 
            $require       =  Account::requirementFilter($business_id);
            $tree          =  Account::accountTree($business_id);
            $tableTree     =  Account::accountTableTree($business_id);
            if($account == false){ return false;}
            $listed["accounts"]    = $account;
            $listed["filter"]      = $require;
            $listed["tree"]        = $tree;
            $listed["table_tree"]  = $tableTree;
            return $listed;
        }catch(Exception $e){
            return false;
        }
    }
    // **2** CREATE ACCOUNT
    public static function createAccount($user,$data) {
        try {
            $business_id        = $user->business_id;
            $require            = Account::requirement($business_id);
            return $require;
        }catch(Exception $e){
            return false;
        }
    }
    // **3** EDIT ACCOUNT
    public static function editAccount($user,$data,$id) {
        try {
            $business_id      = $user->business_id;
            $account          = Account::allData(null,$id,$business_id);
            if(!$account){ return false; }
            return $account;
        }catch(Exception $e){
            return false;
        } 
    }
    // **4** STORE ACCOUNT
    public static function storeAccount($user,$data) {
        try {
            \DB::beginTransaction();
            $business_id         = $user->business_id;
            $data["name"]        = $data["name"] ;
            if(!empty($data["name"]) && $data["name"] != ""){
                $old             = \App\Account::where("name",$data["name"])->where("business_id",$business_id)->first();
                if($old){return "old";}
            }
            if(!empty($data["account_number"]) && $data["account_number"] != ""){
                $old             = \App\Account::where("account_number",$data["account_number"])->where("business_id",$business_id)->first();
                if($old){return "oldN";}
            }
            $output              = Account::createNewAccountBase($user,$data);
            if($output == false){ return "false"; } 
            \DB::commit();
            return "true";
        }catch(Exception $e){
            return "false";
        }
    }
    // **5** UPDATE ACCOUNT
    public static function updateAccount($user,$data,$id) {
        try {
            \DB::beginTransaction();
            $business_id         = $user->business_id;
            $data["name"]        = $data["name"];
            if(!empty($data["name"]) && $data["name"] != ""){
                $old             = \App\Account::where("name",$data["name"])->where("id","!=",$id)->where("business_id",$business_id)->first();
                if($old){return "old";}
            }
            if(!empty($data["account_number"]) && $data["account_number"] != ""){
                $old             = \App\Account::where("account_number",$data["account_number"])->where("id","!=",$id)->where("business_id",$business_id)->first();
                if($old){return "oldN";}
            }
            $output              = Account::updateOldAccountBase($user,$data,$id);
            if($output != "true"){
                return $output;
            } 
            \DB::commit();
            return "true";
        }catch(Exception $e){
            return "false";
        }
    }
    // **6** DELETE ACCOUNT
    public static function deleteAccount($user,$id) {
        try {
            \DB::beginTransaction();
            $business_id     = $user->business_id;
            $account         = \App\Account::find($id);
            if(!$account){ return "false"; }
            $check           = GlobalUtil::checkAccount($id);
            if($check == true){ return "related"; }
            if($account->contact_id != null){
                $contact      =  \App\Contact::find($account->contact_id);
                $check        =  GlobalUtil::check("contact",$account->contact_id); 
                if($check == true){
                    return "haveOld";
                }
                $contact->delete();
            }
            $account->delete();
            \DB::commit();
            return "true";
        }catch(Exception $e){
            return "false";
        }
    }
    // **1** ALL ACCOUNT TREE
    public static function getAccountTree($user) {
        try {
            $business_id   = $user->business_id; 
            $tree          =  Account::accountTree($business_id);
            // $tableTree     =  Account::accountTableTree($business_id);
            return $tree;
        }catch(Exception $e){
            return false;
        }
    }
    // ****** MAIN FUNCTIONS 
    // **1** CREATE ACCOUNT BASE
    public static function createNewAccountBase($user,$data) {
        try {
            $business       = \App\Business::find($user->business_id);
            if(isset($data['account_type_id'])){
                
                if($business->supplier_type_id == $data['account_type_id'] || $business->customer_type_id ==  $data['account_type_id'] ){
                    
                    // .........................................CONTACT TYPE CREATE
                    $dataAll                            = [];
                    $type                               = ($business->supplier_type_id == $data['account_type_id'])?"supplier":"customer";
                    // $NumberOfCount                      = GlobalUtil::SetReferenceCount($type,$business->id);
                    // $Reference                          = GlobalUtil::GenerateReferenceCount($type,$NumberOfCount,$business->id);
                    $Reference                          = $data['account_number'];
                    $dataAll["business_id"]             = $business->id;
                    $dataAll["contact_id"]              = $Reference;
                    $dataAll["type"]                    = ($business->supplier_type_id == $data['account_type_id'])?"supplier":"customer";
                    $dataAll["supplier_business_name"]  = (isset($data['name']))?$data['name']:null;
                    $dataAll["prefix"]                  = null;
                    $dataAll["name"]                    = (isset($data['name']))?$data['name']:null;
                    $dataAll["first_name"]              = (isset($data['name']))?$data['name']:null;
                    $dataAll["middle_name"]             = null;
                    $dataAll["last_name"]               = null;
                    $dataAll["tax_number"]              = null;
                    $dataAll["pay_term_number"]         = null;
                    $dataAll["pay_term_type"]           = null;
                    $dataAll["mobile"]                  = 0;
                    $dataAll["landline"]                = null;
                    $dataAll["alternate_number"]        = null;
                    $dataAll["city"]                    = null;
                    $dataAll["state"]                   = null;
                    $dataAll["country"]                 = null;
                    $dataAll["address_line_1"]          = null;
                    $dataAll["address_line_2"]          = null;
                    $dataAll["customer_group_id"]       = null;
                    $dataAll["zip_code"]                = null;
                    $dataAll["custom_field1"]           = null;
                    $dataAll["custom_field2"]           = null;
                    $dataAll["custom_field3"]           = null;
                    $dataAll["custom_field4"]           = null;
                    $dataAll["custom_field5"]           = null;
                    $dataAll["custom_field6"]           = null;
                    $dataAll["custom_field7"]           = null;
                    $dataAll["custom_field8"]           = null;
                    $dataAll["custom_field9"]           = null;
                    $dataAll["custom_field10"]          = null;
                    $dataAll["email"]                   = null;
                    $dataAll["shipping_address"]        = null;
                    $dataAll["position"]                = null;
                    $dataAll["dob"]                     = null;
                    $dataAll["credit_limit"]            = null;
                    $dataAll["opening_balance"]         = 0;
                    // .....................................................*****
                    $contact                  = \App\Contact::create($dataAll);
                    // .....................................................*****
                   
                    $account                  =  new \App\Account();
                    $account->contact_id      =  $contact->id;
                    $account->business_id     =  $business->id;
                    $account->name            = (isset($data['name']))?$data['name']:null;
                    $account->account_number  = (isset($data['account_number']))?$data['account_number']:null;
                    $account->account_type_id = (isset($data['account_type_id']))?$data['account_type_id']:null;
                    $account->note            = (isset($data['note']))?$data['note']:null;
                    $account->created_by      = $user->id;
                    $account->is_closed       = 0;
                    $account->is_second_curr  = 0;
                    $account->cost_center     = 0;
                    $account->save();
                }else{
                    
                    $account                  = new \App\Account();
                    $account->contact_id      = null;
                    $account->business_id     = $business->id;
                    $account->name            = (isset($data['name']))?$data['name']:null;
                    $account->account_number  = (isset($data['account_number']))?$data['account_number']:null;
                    $account->account_type_id = (isset($data['account_type_id']))?$data['account_type_id']:null;
                    $account->note            = (isset($data['note']))?$data['note']:null;
                    $account->created_by      = $user->id;
                    $account->is_closed       = 0;
                    $account->is_second_curr  = 0;
                    $account->cost_center     = 0;
                    $account->save();
                 
                }
                return true; 
            }else{
                return false; 
            }
        }catch(Exception $e){
            return false;
        }
    }
    // **2** UPDATE ACCOUNT BASE
    public static function updateOldAccountBase($user,$data,$id) {
        try {
            $business        = \App\Business::find($user->business_id);
            if(isset($data['account_type_id'])){
                $account                  =  \App\Account::find($id);
                $old_type                 = $account->account_type_id;
                if($business->supplier_type_id == $data['account_type_id'] || $business->customer_type_id ==  $data['account_type_id'] ){
                    if($account->contact_id != null){
                        $contact      =  \App\Contact::find($account->contact_id);
                        $check        =  GlobalUtil::check("contact",$account->contact_id); 
                        $contact_type =  $contact->type  ;
                        if($old_type ==  $business->supplier_type_id){
                            if($business->supplier_type_id == $data['account_type_id']){
                                $contact->supplier_business_name  = (isset($data['name']))?$data['name']:null;
                                $contact->contact_id              = (isset($data['account_number']))?$data['account_number']:null;
                                $contact->first_name              = (isset($data['name']))?$data['name']:null;
                                $contact->update();
                                $account->name                    = (isset($data['name']))?$data['name']:null;
                                $account->account_number          = (isset($data['account_number']))?$data['account_number']:null;
                                $account->account_type_id         = (isset($data['account_type_id']))?$data['account_type_id']:null;
                                $account->note                    = (isset($data['note']))?$data['note']:null;
                                $account->update(); 
                            }elseif($business->customer_type_id == $data['account_type_id']){
                                if($check == true){
                                    return "haveOld";
                                }
                                $contact->type                    = "customer";
                                $contact->contact_id              = (isset($data['account_number']))?$data['account_number']:null;
                                $contact->supplier_business_name  = (isset($data['name']))?$data['name']:null;
                                $contact->first_name              = (isset($data['name']))?$data['name']:null;
                                $contact->update();
                                $account->name                    = (isset($data['name']))?$data['name']:null;
                                $account->account_number          = (isset($data['account_number']))?$data['account_number']:null;
                                $account->account_type_id         = (isset($data['account_type_id']))?$data['account_type_id']:null;
                                $account->note                    = (isset($data['note']))?$data['note']:null;
                                $account->update();
                            }else{
                                if($check == true){
                                    return "haveOld";
                                }
                                $contact->delete();
                                $account->contact_id              = null;
                                $account->name                    = (isset($data['name']))?$data['name']:null;
                                $account->account_number          = (isset($data['account_number']))?$data['account_number']:null;
                                $account->account_type_id         = (isset($data['account_type_id']))?$data['account_type_id']:null;
                                $account->note                    = (isset($data['note']))?$data['note']:null;
                                $account->update();  
                            }
                        }
                        if($old_type ==  $business->customer_type_id){
                            if($business->customer_type_id == $data['account_type_id']){
                                $contact->supplier_business_name  = (isset($data['name']))?$data['name']:null;
                                $contact->first_name              = (isset($data['name']))?$data['name']:null;
                                $contact->contact_id              = (isset($data['account_number']))?$data['account_number']:null;
                                $contact->update();
                                $account->name                    = (isset($data['name']))?$data['name']:null;
                                $account->account_number          = (isset($data['account_number']))?$data['account_number']:null;
                                $account->account_type_id         = (isset($data['account_type_id']))?$data['account_type_id']:null;
                                $account->note                    = (isset($data['note']))?$data['note']:null;
                                $account->update();  
                            }elseif($business->supplier_type_id == $data['account_type_id']){
                                if($check == true){
                                    return "haveOld";
                                }
                                $contact->type                    = "supplier";
                                $contact->contact_id              = (isset($data['account_number']))?$data['account_number']:null;
                                $contact->supplier_business_name  = (isset($data['name']))?$data['name']:null;
                                $contact->first_name              = (isset($data['name']))?$data['name']:null;
                                $contact->update();
                                $account->name                    = (isset($data['name']))?$data['name']:null;
                                $account->account_number          = (isset($data['account_number']))?$data['account_number']:null;
                                $account->account_type_id         = (isset($data['account_type_id']))?$data['account_type_id']:null;
                                $account->note                    = (isset($data['note']))?$data['note']:null;
                                $account->update();
                            }else{
                                if($check == true){
                                    return "haveOld";
                                }
                                $contact->delete();
                                $account->contact_id      = null;
                                $account->name            = (isset($data['name']))?$data['name']:null;
                                $account->account_number  = (isset($data['account_number']))?$data['account_number']:null;
                                $account->account_type_id = (isset($data['account_type_id']))?$data['account_type_id']:null;
                                $account->note            = (isset($data['note']))?$data['note']:null;
                                $account->update();   
                            }
                        }
                    }else{
                        // .........................................CONTACT TYPE CREATE
                        $dataAll                            = [];
                        $type                               = ($business->supplier_type_id == $data['account_type_id'])?"supplier":"customer";
                        // $NumberOfCount                   = GlobalUtil::SetReferenceCount($type,$business->id);
                        // $Reference                       = GlobalUtil::GenerateReferenceCount($type,$NumberOfCount,$business->id);
                        $Reference                          = $data['account_number'];
                        $dataAll["business_id"]             = $business->id;
                        $dataAll["contact_id"]              = $Reference;
                        $dataAll["type"]                    = ($business->supplier_type_id == $data['account_type_id'])?"supplier":"customer";
                        $dataAll["supplier_business_name"]  = (isset($data['name']))?$data['name']:null;
                        $dataAll["prefix"]                  = null;
                        $dataAll["name"]                    = (isset($data['name']))?$data['name']:null;
                        $dataAll["first_name"]              = (isset($data['name']))?$data['name']:null;
                        $dataAll["middle_name"]             = null;
                        $dataAll["last_name"]               = null;
                        $dataAll["tax_number"]              = null;
                        $dataAll["pay_term_number"]         = null;
                        $dataAll["pay_term_type"]           = null;
                        $dataAll["mobile"]                  = 0;
                        $dataAll["landline"]                = null;
                        $dataAll["alternate_number"]        = null;
                        $dataAll["city"]                    = null;
                        $dataAll["state"]                   = null;
                        $dataAll["country"]                 = null;
                        $dataAll["address_line_1"]          = null;
                        $dataAll["address_line_2"]          = null;
                        $dataAll["customer_group_id"]       = null;
                        $dataAll["zip_code"]                = null;
                        $dataAll["custom_field1"]           = null;
                        $dataAll["custom_field2"]           = null;
                        $dataAll["custom_field3"]           = null;
                        $dataAll["custom_field4"]           = null;
                        $dataAll["custom_field5"]           = null;
                        $dataAll["custom_field6"]           = null;
                        $dataAll["custom_field7"]           = null;
                        $dataAll["custom_field8"]           = null;
                        $dataAll["custom_field9"]           = null;
                        $dataAll["custom_field10"]          = null;
                        $dataAll["email"]                   = null;
                        $dataAll["shipping_address"]        = null;
                        $dataAll["position"]                = null;
                        $dataAll["dob"]                     = null;
                        $dataAll["credit_limit"]            = null;
                        $dataAll["opening_balance"]         = 0;
                        // .....................................................*****
                        $contact                  = \App\Contact::create($dataAll);
                        // .....................................................*****
                        $account->contact_id      = $contact->id;
                        $account->name            = (isset($data['name']))?$data['name']:null;
                        $account->account_number  = (isset($data['account_number']))?$data['account_number']:null;
                        $account->account_type_id = (isset($data['account_type_id']))?$data['account_type_id']:null;
                        $account->note            = (isset($data['note']))?$data['note']:null;
                        $account->update();
                       
                    }
                }else{
                    if($account->contact_id != null){
                        $contact      =  \App\Contact::find($account->contact_id);
                        $check        =  GlobalUtil::check("contact",$account->contact_id);  
                        if($check == true){
                            return "haveOld";
                        }
                        $contact      =  \App\Contact::find($account->contact_id);
                        $contact->delete();
                    } 
                    $account->contact_id      = null;
                    $account->name            = (isset($data['name']))?$data['name']:null;
                    $account->account_number  = (isset($data['account_number']))?$data['account_number']:null;
                    $account->account_type_id = (isset($data['account_type_id']))?$data['account_type_id']:null;
                    $account->note            = (isset($data['note']))?$data['note']:null;
                    $account->update();
                }
                return "true"; 
            }else{
                return "false"; 
            }
        }catch(Exception $e){
            return "false";
        }
    }
    // **3** CREATE ACCOUNT
    public static function createNewAccount($user,$data) {
        try {
            $account        =  \App\Account::create($data);
            return true; 
        }catch(Exception $e){
            return false;
        }
    }
    // **4** UPDATE ACCOUNT
    public static function updateOldAccount($user,$data,$id) {
        try {
            $account         = \App\Account::find($id);
            $account->update($data);
            return true; 
        }catch(Exception $e){
            return false;
        }
    }

    // **5** List Cash ACCOUNT
    public static function cashList($user) {
        try{
            $list              = [];
            $business_id       = $user->business_id;
            $business          = \App\Business::find($business_id);
            $accounts          = \App\Account::where("account_type_id",$business->cash)->orderBy("account_number","desc")->get(); 
            foreach($accounts as $e){
                $list[] = [
                    "id"     => $e->id,
                    "name"   => $e->name,
                    "number" => $e->account_number,
                ];
            }
            return $list;
        }catch(Exception $e){
            return false;
        } 
    }
    // **6** List Bank ACCOUNT
    public static function bankList($user) {
        try{
            $list              = [];
            $business_id       = $user->business_id;
            $business          = \App\Business::find($business_id);
            $accounts          = \App\Account::where("account_type_id",$business->bank)->orderBy("account_number","desc")->get(); 
            foreach($accounts as $e){
                $list[] = [
                    "id"     => $e->id,
                    "name"   => $e->name,
                    "number" => $e->account_number,
                ];
            }
            return $list;
        }catch(Exception $e){
            return false;
        } 
    }
    // **7** Create Cash ACCOUNT
    public static function cashCreate($user) {
        try{
            $list              = [];
            $business_id       = $user->business_id;
            $business          = \App\Business::find($business_id);
            $accounts          = \App\Account::where("account_type_id",$business->cash)->orderBy("account_number","desc")->first(); 
            $list["info"]      = [ 
                "account_type_id" => $business->cash,
                "account_number"  => intVal($accounts->account_number)+1
            ];
            return $list;
        }catch(Exception $e){
            return false;
        } 
    }
    // **8** Create Bank ACCOUNT
    public static function bankCreate($user) {
        try{
            $list              = [];
            $business_id       = $user->business_id;
            $business          = \App\Business::find($business_id);
            $accounts          = \App\Account::where("account_type_id",$business->bank)->orderBy("account_number","desc")->first(); 
            $list["info"]      = [ 
                "account_type_id" => $business->bank,
                "account_number"  => intVal($accounts->account_number)+1
            ];
            return $list;
        }catch(Exception $e){
            return false;
        } 
    }
    // **9** Store Cash ACCOUNT
    public static function cashStore($user,$data) {
        try{
            if(!empty($data["name"]) && $data["name"] != ""){
                $old             = \App\Account::where("name",$data["name"])->where("business_id",$data["business_id"])->first();
                if($old){return "old";}
            }
            $business                =  \App\Business::find($data["business_id"]);
            $accounts                =  \App\Account::where("account_type_id",$business->cash)->orderBy("account_number","desc")->first(); 
            $data["account_type_id"] =  $business->cash;
            $data["account_number"]  =  intVal($accounts->account_number)+1;
            $check                   =  Account::createNewAccount($user,$data);
            if($check == false){ return "false";}
            return "true";
        }catch(Exception $e){
            return"failed";
        } 
    }
    // **10** Store Bank ACCOUNT
    public static function bankStore($user,$data) {
        try{
            if(!empty($data["name"]) && $data["name"] != ""){
                $old             = \App\Account::where("name",$data["name"])->where("business_id",$data["business_id"])->first();
                if($old){return "old";}
            }
            $business                =  \App\Business::find($data["business_id"]);
            $accounts                =  \App\Account::where("account_type_id",$business->bank)->orderBy("account_number","desc")->first(); 
            $data["account_type_id"] =  $business->bank;
            $data["account_number"]  =  intVal($accounts->account_number)+1;
            $check                   =  Account::createNewAccount($user,$data);
            if($check == false){ return "false";}
            return "true";
        }catch(Exception $e){
            return"failed";
        } 
    }
    // **11** Update Cash ACCOUNT
    public static function cashUpdate($user,$data,$id) {
        try{
            if(!empty($data["name"]) && $data["name"] != ""){
                $old                 = \App\Account::where("name",$data["name"])->where("id","!=",$id)->where("business_id",$data["business_id"])->first();
                if($old){return "old";}
            }
            $account                 = \App\Account::find($id);
            if(!$account){return "false";}
            $check                   =  Account::updateOldAccount($user,$data,$id);
            if($check == false){ return "false";}
            return "true";
        }catch(Exception $e){
            return"failed";
        } 
    }
    // **12** Update Bank ACCOUNT
    public static function bankUpdate($user,$data,$id) {
        try{
            if(!empty($data["name"]) && $data["name"] != ""){
                $old                 = \App\Account::where("name",$data["name"])->where("id","!=",$id)->where("business_id",$data["business_id"])->first();
                if($old){return "old";}
            }
            $account                 = \App\Account::find($id);
            if(!$account){return "false";}
            $check                   =  Account::updateOldAccount($user,$data,$id);
            if($check == false){ return "false";}
            return "true";
        }catch(Exception $e){
            return"failed";
        } 
    }
    // **13** ENTRIES  
    public static function entries($user) {
        try{
            $list          = [];
            $entries       = \App\Models\Entry::select()->get();
            if(count($entries)==0){return   false ;}
                foreach($entries as $ie){
                    if($ie->check_id != null){
                        $source_id = $ie->check_id;
                        $type      = "cheque";
                    }elseif($ie->voucher_id != null){
                        $source_id = $ie->voucher_id;
                        $type      = "voucher";
                    }elseif($ie->journal_voucher_id != null){
                        $source_id = $ie->journal_voucher_id;
                        $type      = "journal_voucher";
                    }elseif($ie->expense_voucher_id != null){
                        $source_id = $ie->expense_voucher_id;
                        $type      = "Expense_voucher";
                    }elseif($ie->account_transaction != null && $ie->account_transaction != 0){
                        $source_id = $ie->account_transaction;
                        $type      = $ie->transaction->type;
                    }else {
                        $source_id = null;
                        $type      = "empty";
                    }
                   
                    $list[]  = [
                        "id"               => $ie->id,
                        "reference"        => $ie->refe_no_e,
                        "source_reference" => $ie->ref_no_e,
                        "type"             => $type,
                        "source_id"        => $source_id
                    ];
                }
            return $list;
        }catch(Exception $e){
            return"failed";
        } 
    }
 

    // **14** GET ACCOUNT  
    public static function allData($type=null,$id=null,$business_id) {
        try{
            $list   = [];
            if($type != null){
                $query     = \App\Account::where("business_id",$business_id);
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
                    $debit  = \App\AccountTransaction::where("account_id",$ie->id)->whereNull("for_repeat")->where("type","debit")->sum("amount");
                    $credit = \App\AccountTransaction::where("account_id",$ie->id)->whereNull("for_repeat")->where("type","credit")->sum("amount");
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
                        "account_number"     => $ie->account_number,
                        "account_type"       => ($ie->account_type)?$ie->account_type->name:"000",
                        "balance"            => abs($balance),
                        "type"               => ($balance>0)?"Debit":(($balance==0)?"":"Credit"),
                        "date"               => $ie->created_at->format("Y-m-d h:i:s a"),
                    ];
                }
            }else{
                $account  = \App\Account::find($id);
                $debit    = \App\AccountTransaction::where("account_id",$account->id)->whereNull("for_repeat")->where("type","debit")->sum("amount");
                $credit   = \App\AccountTransaction::where("account_id",$account->id)->whereNull("for_repeat")->where("type","credit")->sum("amount");
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
                    "account_number"     => $account->account_number,
                    "account_type"       => ($account->account_type)?$account->account_type->name:"000",
                    "balance"            => abs($balance),
                    "type"               => ($balance>0)?"Debit":(($balance==0)?"":"Credit"),
                    "date"               => $account->created_at->format("Y-m-d h:i:s a"),
                ];
                $list["require"]         =  Account::requirement($business_id);
            }
            return $list; 
        }catch(Exception $e){
            return false;
        }
    }
    // **15** GET ACCOUNT
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
    // **16** GET FILTER ACCOUNT
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
                            }
                            // }else{
                                
                            // }
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
    // **17** GET  ACCOUNT TYPE TREE
    public static function accountTree($business_id){
        $dd = GlobalUtil::toTree("account_type",$business_id); 
        return $dd;
    }
    // **18** GET  ACCOUNT TYPE TABLE TREE
    public static function accountTableTree($business_id){
        $dd = GlobalUtil::toTreeTable("account_type",$business_id); 
        return $dd;
    }
    // **19** ENTRIES  
    public static function viewEntry($user,$data,$id) {
        try{
            $list = false; 
            if(isset($data['type']) && $data['type'] == "voucher"){
                $list = Voucher::entryVoucher($user,$data,$id);
            }elseif(isset($data['type']) && $data['type'] == "Expense_voucher"){
                $list = ExpenseVoucher::entryExpenseVoucher($user,$data,$id);
            }elseif(isset($data['type']) && $data['type'] == "journal_voucher"){
                $list =  JournalVoucher::entryJournalVoucher($user,$data,$id);
            }elseif(isset($data['type']) && $data['type'] == "cheque"){
                $list =  Cheque::entryCheque($user,$data,$id);
            }
              
            return $list;
        }catch(Exception $e){
            return false;
        } 
    }
}
