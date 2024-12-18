<?php

namespace App\Models\FrontEnd\Contacts;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\CustomerGroup;
use App\Transaction;
use App\Utils\Util;

use Excel;
use DB;

use App\Utils\TransactionUtil;
use App\Models\FrontEnd\Utils\GlobalUtil;

class Contact extends Model
{
    use HasFactory,SoftDeletes;
    // *** REACT FRONT-END CONTACT *** // 
    // ** 1 ** ALL CONTACTS
    public static function getContact($user) {
        try{
            $list        = [];
            $business_id = $user->business_id;
            $contact     = \App\Contact::where("business_id",$business_id)->get();
            if(count($contact)==0) { return false; }
            foreach($contact as $i){ $list[] = $i; }
            return $list;
        }catch(Exception $e){
            return false;
        }
    }
    // ** 2 ** SUPPLIER CONTACTS
    public static function getSupplier($user) {
        try{
            $list         = [];
            $business_id  = $user->business_id;
            $supplier     = \App\Contact::where("business_id",$business_id)->where("type","supplier")->orWhere("type","both")->orderBy('id',"desc")->get();
            if(count($supplier)==0){
                return false;
            }
            foreach($supplier as $i){
                $list[] = $i;
            }
            return $list;
        }catch(Exception $e){
            return false;
        }
    }
    // ** 3 ** CUSTOMER CONTACTS
    public static function getCustomer($user) {
        try{
            $list        = [];
            $business_id = $user->business_id;
            $customer    = \App\Contact::where("business_id",$business_id)->where("type","customer")->orWhere("type","both")->orderBy('id',"desc")->get();
            if(count($customer)==0){
                return false;
            }
            foreach($customer as $i){
                $list[] = $i;
            }
            return $list;
        }catch(Exception $e){
            return false;
        }
    }
    // ** 4 ** CREATE CONTACTS
    public static function createContact($user,$data) {
        try{
            $business_id             = $user->business_id;
            $list["type"]            = ["value" => $data["type"]];
            $customer_groups         = CustomerGroup::forDropdown($business_id);
            $list["customer_group"]  = $customer_groups;
            return $list;
        }catch(Exception $e){
            return false;
        }
    }
    // ** 5 ** EDIT CONTACTS
    public static function editContact($user,$data,$id) {
        try{
            $business_id             = $user->business_id;
            $supplier                = \App\Contact::find($id);
            $list["contact"]         = $supplier;
            $customer_groups         = CustomerGroup::forDropdown($business_id);
            $list["customer_group"]  = $customer_groups;
            if(!$supplier){
                return false;
            }
            return $list;
        }catch(Exception $e){
            return false;
        }
    }
    // ** 6 ** VIEW CONTACTS
    public static function viewContact($user,$data,$id) {
        try{
            $business_id             = $user->business_id;
            $contact                 = \App\Contact::find($id);
            $list_sale=[];           $list_purchase=[];
            $sales                   = \App\Transaction::where("contact_id",$id)
                                                        ->whereIn("type",["sale","sell_return"])
                                                        ->select([
                                                            "id","business_id",
                                                            "location_id","invoice_no",
                                                            "type","is_quotation","payment_status","pattern_id","currency_id",
                                                            "status","sub_status","transaction_date","project_no","agent_id",
                                                            "total_before_tax","tax_id","tax_amount","discount_type",
                                                            "discount_amount","additional_notes","final_total","created_by",
                                                        ])
                                                        ->whereDate('transaction_date', '>=', $data["start_date"])
                                                        ->whereDate('transaction_date', '<=', $data["end_date"])
                                                        ->get();
            $purchases               = \App\Transaction::where("contact_id",$id)
                                                        ->whereIn("type",["purchase","purchase_return"])
                                                        ->select([
                                                            "id","business_id",
                                                            "location_id","ref_no",
                                                            "type","is_quotation","payment_status","pattern_id","currency_id",
                                                            "status","sub_status","transaction_date","project_no","agent_id",
                                                            "total_before_tax","tax_id","tax_amount","discount_type",
                                                            "discount_amount","additional_notes","final_total","created_by",
                                                        ])
                                                        // ->whereDate('transaction_date', '>=', $data["start_date"])
                                                        // ->whereDate('transaction_date', '<=', $data["end_date"])
                                                        ->get();
            $payments                = \App\TransactionPayment::whereHas("transaction",function($query) use($id){
                                                                    $query->where("contact_id",$id); 
                                                                })
                                                                ->whereDate('paid_on', '>=', $data["start_date"])
                                                                ->whereDate('paid_on', '<=', $data["end_date"])
                                                                ->get();
            $list_sale  = [];
            foreach($sales as $is){
                $business   = \App\Business::find($is->business_id);
                $currency   = \App\Currency::find($business->currency_id);                                            
                $created_by = \App\Models\User::find($is->created_by);
                $user       = ($created_by)?$created_by->first_name:"" ;
                if($is->agent_id != null || $is->agent_id != ""){
                    $agent      = \App\Models\User::find($is->agent_id);
                    $agt        = ($agent)?$agent->first_name:"" ;
                }else{
                    $agt   = "" ; 
                }
                $list_sale[]     = [
                    "id"                =>   $is->id,
                    "business_id"       =>   $is->business_id,
                    "location"          =>   $is->location->name,
                    "invoice_no"        =>   $is->invoice_no,
                    "type"              =>   $is->type,
                    "is_quotation"      =>   $is->is_quotation,
                    "payment_status"    =>   $is->payment_status,
                    "pattern"           =>   ($is->pattern)?$is->pattern->name:"",
                    "currency"          =>   ($is->currency)?$is->currency->currency:$currency->symbol,
                    "status"            =>   $is->status,
                    "sub_status"        =>   $is->sub_status,
                    "date"              =>   $is->transaction_date,
                    "project_no"        =>   $is->project_no,
                    "agent"             =>   $agt,
                    "sub_total"         =>   round($is->total_before_tax,2),
                    "tax_id"            =>   $is->tax->name,
                    "tax_amount"        =>   round($is->tax_amount,2),
                    "discount_type"     =>   $is->discount_type,
                    "discount_amount"   =>   round($is->discount_amount,2),
                    "additional_notes"  =>   $is->additional_notes,
                    "total"             =>   round($is->final_total,2),
                    "created_by"        =>   $user,
                ];
            }
            $list_purchase   = [];
            foreach($purchases as $ip){
                $business   = \App\Business::find($ip->business_id);
                $currency   = \App\Currency::find($business->currency_id);  
                $created_by = \App\Models\User::find($ip->created_by);
                $user       = ($created_by)?$created_by->first_name:"" ;
                if($ip->agent_id != null || $ip->agent_id != ""){
                    $agent      = \App\Models\User::find($ip->agent_id);
                    $agt        = ($agent)?$agent->first_name:"" ;
                }else{
                    $agt   = "" ; 
                }
                $list_purchase[] = [
                    "id"                =>   $ip->id,
                    "business_id"       =>   $ip->business_id,
                    "location"          =>   $ip->location->name,
                    "invoice_no"        =>   $ip->invoice_no,
                    "type"              =>   $ip->type,
                    "is_quotation"      =>   $ip->is_quotation,
                    "payment_status"    =>   $ip->payment_status,
                    "pattern_id"        =>   ($ip->pattern)?$ip->pattern->name:"",
                    "currency_id"       =>   ($ip->currency)?$ip->currency->currency:$currency->symbol,
                    "status"            =>   $ip->status,
                    "sub_status"        =>   $ip->sub_status,
                    "date"              =>   $ip->transaction_date,
                    "project_no"        =>   $ip->project_no,
                    "agent"             =>   $agt,
                    "sub_total"         =>   round($ip->total_before_tax,2),
                    "tax_id"            =>   $ip->tax->name,
                    "tax_amount"        =>   round($ip->tax_amount,2),
                    "discount_type"     =>   $ip->discount_type,
                    "discount_amount"   =>   round($ip->discount_amount,2),
                    "additional_notes"  =>   $ip->additional_notes,
                    "total"             =>   round($ip->final_total,2),
                    "created_by"        =>   $user,
                ];
            }
            $list_tp   = [];
            foreach($payments as $tp){
                $business   = \App\Business::find($tp->business_id);
                $currency   = \App\Currency::find($business->currency_id);  
                 
                $list_tp[] = [
                    "id"                =>   $tp->id,
                    "store"             =>   ($tp->transaction)?(($tp->transaction->warehouse)?$tp->transaction->warehouse->name:""):"",
                    "transaction"       =>   ($tp->transaction->invoice_no == null)? $tp->transaction->ref_no : $tp->transaction->invoice_no,
                    "business_id"       =>   $tp->business_id,
                    "is_return"         =>   $tp->is_return,
                    "amount"            =>   $tp->amount,
                    "method"            =>   $tp->method,
                    "card_transaction_number" =>   $tp->card_transaction_number,
                    "card_number"       =>   $tp->card_number,
                    "card_type"         =>   $tp->card_type,
                    "card_holder_name"  =>   $tp->card_holder_name,
                    "card_month"        =>   $tp->card_month,
                    "card_year"         =>   $tp->card_year,
                    "card_security"     =>   $tp->card_security,
                    "cheque_number"     =>   $tp->cheque_number,
                    "paid_on"           =>   $tp->paid_on,
                    "account_id"        =>   $tp->account_id,
                    "payment_voucher_id"=>   $tp->payment_voucher_id,
                    "check_id"          =>   $tp->check_id,
                    "payment_ref_no"    =>   $tp->payment_ref_no,
                    "created_by"        =>   $user,
                ];
            }
            $ledger                  = \App\AccountTransaction::whereHas("account",function($query) use($id){
                                                                    $query->where("contact_id",$id);
                                                                })
                                                                ->whereNull("for_repeat")
                                                                ->whereDate('operation_date', '>=', $data["start_date"])
                                                                ->whereDate('operation_date', '<=', $data["end_date"])
                                                                ->get();
            $list_ledger             = [];$bal = 0;
            foreach($ledger as $ld){
                $created_by = \App\Models\User::find($ld->created_by);
                $user       = ($created_by)?$created_by->first_name:"" ;
                $reference  = "";
                $source     = "";
                if($ld->transaction_id != null){
                    $source  = ($ld->transaction)?(($ld->transaction->type == "sale" || $ld->transaction->type == "sell_return")?$ld->transaction->invoice_no:$ld->transaction->ref_no):"";
                    if($ld->transaction_payment_id != null){
                        $reference     = ($ld->payment)?$ld->payment->payment_ref_no:"";
                    }else{    
                        $reference     = $source;
                    }
                }elseif($ld->payment_voucher_id != null){
                    $source     = ($ld->payment_voucher)?$ld->payment_voucher->ref_no:"";
                    $reference  = ($ld->payment_voucher)?$ld->payment_voucher->ref_no:"";
                }elseif($ld->daily_payment_item_id != null){
                    $source     = ($ld->daily_payment_item)?(($ld->daily_payment_item->daily_payment)?$ld->daily_payment_item->daily_payment->ref_no:""):"";
                    $reference  = ($ld->daily_payment_item)?(($ld->daily_payment_item->daily_payment)?$ld->daily_payment_item->daily_payment->ref_no:""):"";
                }elseif($ld->gournal_voucher_item_id != null){
                    $source     = ($ld->gournal_voucher_item)?(($ld->gournal_voucher_item->gournal_voucher)?$ld->gournal_voucher_item->gournal_voucher->ref_no:""):"";
                    $reference  = ($ld->gournal_voucher_item)?(($ld->gournal_voucher_item->gournal_voucher)?$ld->gournal_voucher_item->gournal_voucher->ref_no:""):"";
                }
                $bal = ($ld->type=="credit")?($bal - $ld->amount):($bal + $ld->amount); 
                $list_ledger[] = [
                    "id"                =>   $ld->id,
                    "date"              =>   $ld->operation_date->format("Y-m-d h:i:s"),
                    "entry_id"          =>   ($ld->entry)?$ld->entry->refe_no_e:"",
                    "source_no"         =>   $source,
                    "reference_no"      =>   $reference,
                    "credit"            =>   ($ld->type=="credit")?DoubleVal($ld->amount):0,
                    "debit"             =>   ($ld->type=="debit")?DoubleVal($ld->amount):0,
                    "note"              =>   $ld->note,
                    "created_by"        =>   $user,
                    "type"              =>   ($bal<0)?"credit":"debit",
                    "amount"            =>   DoubleVal($ld->amount),
                    "balance"           =>   ($bal<0)?DoubleVal(abs($bal)):($bal==0)?0:DoubleVal($bal),
                ];
            }
            $allInfo = []; 
            if($contact->type == "customer" || $contact->type == "both" ){
                $pr_amount  =  \App\AccountTransaction::whereHas('transaction',function($query) use( $contact){
                                                                $query->where('contact_id',$contact->id);
                                                                $query->whereIn('type',['sale','sell_return']);
                                                                $query->where('note',"!=",'Add Payment');
                                                        })->whereHas('account',function($query) use( $contact){
                                                                $query->where('contact_id',$contact->id);
                                                        })->where('type','debit')
                                                        ->where("note","!=","refund Collect")
                                                        ->whereNotNull("transaction_id")
                                                        ->whereDate('operation_date', '>=', $data["start_date"])
                                                        ->whereDate('operation_date', '<=', $data["end_date"])
                                                        ->sum('amount'); 
                $pr_payed   =  \App\AccountTransaction::whereHas('account',function($query) use( $contact){
                                                            $query->where('contact_id',$contact->id);
                                                        })->where('type','credit')
                                                        ->whereNull("for_repeat")
                                                        ->whereNull("id_delete")
                                                        ->whereDate('operation_date', '>=', $data["start_date"])
                                                        ->whereDate('operation_date', '<=', $data["end_date"])
                                                        ->sum('amount'); 
                $diff       =  $pr_payed -  $pr_amount  ;
                $allInfo["total_bill"]           = $pr_amount;
                $allInfo["total_paid"]           = $pr_payed;
                $allInfo["advance_balance"]      = ($diff > 0)?$diff:0;
                $allInfo["balance_due"]          = ($diff*-1 > 0)?abs($diff):0;
            }else if($contact->type == "supplier" || $contact->type == "both"){
                $pr_amount  =  \App\AccountTransaction::whereHas('transaction',function($query) use( $contact){
                                                                $query->where('contact_id',$contact->id);
                                                                $query->whereIn('type',['purchase','purchase_return']);
                                                        })->whereHas('account',function($query) use( $contact){
                                                                $query->where('contact_id',$contact->id);
                                                        })->where('type','credit')
                                                        ->where("note","!=","refund Collect")
                                                        ->whereDate('operation_date', '>=', $data["start_date"])
                                                        ->whereDate('operation_date', '<=', $data["end_date"])
                                                        ->sum("amount");
                $pr_payed   =  \App\AccountTransaction::whereHas('account',function($query) use( $contact){
                                                            $query->where('contact_id',$contact->id);
                                                        })
                                                        ->where('type','debit')
                                                        ->whereNull("for_repeat")
                                                        ->whereNull("id_delete")
                                                        ->whereDate('operation_date', '>=', $data["start_date"])
                                                        ->whereDate('operation_date', '<=', $data["end_date"])
                                                        ->sum('amount');
                $diff                            = $pr_payed - $pr_amount;
                $allInfo["total_bill"]           = $pr_amount;
                $allInfo["total_received"]       = $pr_payed;
                $allInfo["advance_balance"]      = ($diff > 0)?$diff:0;
                $allInfo["balance_due"]          = ($diff*-1 > 0)?abs($diff):0;
            }
           
            $business_in    = \App\Business::find($contact->business_id);
            $location       = ($business_in)?\App\BusinessLocation::where("business_id",$contact->business_id)->first():"";  
            $list_Contact   = [
                "id"                      =>   $contact->id,
                "business_id"             =>   ($location != "" && $location != null)?$location->name:"",
                "type"                    =>   $contact->type,
                "supplier_business_name"  =>   $contact->supplier_business_name,
                "name"                    =>   $contact->name,
                "prefix"                  =>   $contact->prefix,
                "first_name"              =>   $contact->first_name,
                "middle_name"             =>   $contact->middle_name,
                "last_name"               =>   $contact->last_name,
                "email"                   =>   $contact->email,
                "contact_id"              =>   $contact->contact_id,
                "contact_status"          =>   $contact->contact_status,
                "tax_number"              =>   $contact->tax_number,
                "city"                    =>   $contact->city, 
                "state"                   =>   $contact->state ,
                "country"                 =>   $contact->country,
                "address_line_1"          =>   $contact->address_line_1,
                "address_line_2"          =>   $contact->address_line_2,
                "zip_code"                =>   $contact->zip_code,
                "dob"                     =>   $contact->dob,
                "mobile"                  =>   $contact->mobile,
                "landline"                =>   $contact->landline,
                "alternate_number"        =>   $contact->alternate_number,
                "pay_term_number"         =>   $contact->pay_term_number,
                "pay_term_type"           =>   $contact->pay_term_type,
                "credit_limit"            =>   $contact->credit_limit,
                "created_by"              =>   $contact->created_by,
                "converted_by"            =>   $contact->converted_by,
                "converted_on"            =>   $contact->converted_on,
                "balance"                 =>   $contact->balance,
                "total_rp"                =>   $contact->total_rp,
                "total_rp_used"           =>   $contact->total_rp_used,
                "total_rp_expired"        =>   $contact->total_rp_expired,
                "is_default"              =>   $contact->is_default,
                "shipping_address"        =>   $contact->shipping_address,
                "position"                =>   $contact->position,
                "customer_group_id"       =>   $contact->customer_group_id,
                "crm_source"              =>   $contact->crm_source,
                "crm_life_stage"          =>   $contact->crm_life_stage,
                "custom_field1"           =>   $contact->custom_field1,
                "custom_field2"           =>   $contact->custom_field2,
                "custom_field3"           =>   $contact->custom_field3,
                "custom_field4"           =>   $contact->custom_field4,
                "custom_field5"           =>   $contact->custom_field5,
                "custom_field6"           =>   $contact->custom_field6,
                "custom_field7"           =>   $contact->custom_field7,
                "custom_field8"           =>   $contact->custom_field8,
                "custom_field9"           =>   $contact->custom_field9,
                "custom_field10"          =>   $contact->custom_field10,
                "deleted_at"              =>   $contact->deleted_at,
                "created_at"              =>   $contact->created_at,
                "updated_at"              =>   $contact->updated_at,
                "price_group_id"          =>   $contact->price_group_id
            ];
            $list["contact"]              = $list_Contact;
            $list["ledger"]               = [ "rows" => $list_ledger , "info" =>$allInfo ];
            $list["sale"]                 = $list_sale;
            $list["purchase"]             = $list_purchase;
            $list["payment"]              = $list_tp;
           
            if(!$contact){
                return false;
            }
            return $list;
        }catch(Exception $e){
            return false;
        }
    }
    // ** 7 ** STORE CONTACTS
    public static function storeContact($user,$data) {
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
            $output              = Contact::createNewContact($user,$data); 
            \DB::commit();
            return $output;
        }catch(Exception $e){
            return false;
        }
    }
    // ** 8 ** UPDATE CONTACTS
    public static function updateContact($user,$data,$id) {
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
            $output              = Contact::updateOldContact($user,$data,$id);
            \DB::commit();
            return $output;
        }catch(Exception $e){
            return false;
        }
    }
    // ** 9 ** DELETE CONTACTS
    public static function deleteContact($user,$id) {
        try{
            \DB::beginTransaction();
            $business_id = $user->business_id;
            $contact     = \App\Contact::find($id);
            if(!$contact){
                return false;
            }
            $contact->delete();
            \DB::commit();
            return true;
        }catch(Exception $e){
            return false;
        }
    }

    // ****** MAIN FUNCTIONS 
    // *1* CREATE CONTACT 
    public static function createNewContact($user,$data){
        $business_id   = $user->business_id;
        if(empty($data["contact_id"])){
            $type               = (!in_array($data["type"],["customer","supplier"]))?"contacts":$data["type"];
            $NumberOfCount      = GlobalUtil::SetReferenceCount($type,$business_id);
            $Reference          = GlobalUtil::GenerateReferenceCount($type,$NumberOfCount,$business_id);
            $data["contact_id"] = $Reference;
        }
        $Opening_balance        = (isset($data["opening_balance"]) && $data["opening_balance"] != "")?$data["opening_balance"]:0;
        $data["dob"]            = \Carbon::parse($data["dob"]);
        $contact                = \App\Contact::create($data);
        $contact_id             = $contact->id;
        $created_by             = $user->id;
        // if($Opening_balance != 0){ \App\Models\FrontEnd\Utils\Contact::OpeningBalance($business_id, $contact_id, $Opening_balance, $created_by); }
        return $contact;
    }
    // *2*  UPDATE CONTACT 
    public static function updateOldContact($user,$data,$id){
        //Get opening balance if exists
        // $Opening_balance        = (isset($data["opening_balance"]) && $data["opening_balance"] != "")?$data["opening_balance"]:0;
        // $Opening_transaction    = Transaction::where("contact_id", $id)->where("type","opening_balance")->first();
        // if($Opening_balance != 0){ \App\Models\FrontEnd\Utils\Contact::OpeningBalance($business_id, $contact_id, $Opening_balance, $created_by); }
        $business_id   = $user->business_id;
        $type                   = (!in_array($data["type"],["customer","supplier"]))?"contacts":$data["type"];
        $contact                = \App\Contact::where('business_id', $business_id)->findOrFail($id);
        unset($data['opening_balance']);
        if(!empty($contact)){
            foreach($data as $key => $value){
                if($key == "dob"){
                    if($value != "" && $value != null){
                        $date          = \Carbon::parse($value);
                        $contact->$key = $date;
                    }else{
                        $contact->$key = $value;
                    }
                }else{
                    $contact->$key = $value;
                }
            }
            $contact->update();
        }
        $account                = \App\Account::where("contact_id",$id)->first();
        if($account != null){
            $account->name                        = $data["first_name"];
            $account->update();
        }
        return $contact;
    }


    # Excel section
    # Export template
    public static function exportContact($user) {
        try{
            return asset('files/import_contacts_csv_template.xls');
        }catch(Exception $e){return false;}
    }
    # Import template
    public static function importContact($user,$data) {
        try{
                $transactionUtil = new TransactionUtil(); 
                $commonUtil      = new Util();
                $file          = $data;
                $parsed_array  = Excel::toArray([], $file);
                // Remove header row
                $imported_data = array_splice($parsed_array[0], 1);
                $business_id   = $user->business_id;
                $user_id       = $user->id;
                $format_data   = [];
                $is_valid      = true;
                $error_msg     = '';
                DB::beginTransaction();
                foreach ($imported_data as $key => $value) {
                    //Check if 27 no. of columns exists
                    if (count($value) != 27) {
                        $is_valid  =  false;
                        $error_msg = "Number of columns mismatch";
                        break;
                    }
                    $row_no = $key + 1;
                    $contact_array = [];
                    //Check contact type
                    $contact_type  = '';
                    $contact_types = [
                        1 => 'customer',
                        2 => 'supplier',
                        3 => 'both'
                    ];
                    if (!empty($value[0])) {
                        $contact_type = strtolower(trim($value[0]));
                        if (in_array($contact_type, [1, 2, 3])) {
                            $contact_array['type'] = $contact_types[$contact_type];
                        } else {
                            $is_valid  =  false;
                            $error_msg = "Invalid contact type $contact_type in row no. $row_no";
                            break;
                        }
                    } else {
                        $is_valid  =  false;
                        $error_msg = "Contact type is required in row no. $row_no";
                        break;
                    }
                    $contact_array['prefix'] = $value[1];
                    //Check contact name
                    if (!empty($value[2])) {
                        $contact_array['first_name'] = $value[2];
                    } else {
                        $is_valid  =  false;
                        $error_msg = "First name is required in row no. $row_no";
                        break;
                    }
                    $contact_array['middle_name'] = $value[3];
                    $contact_array['last_name']   = $value[4];
                    $contact_array['name']        = implode(' ', [$contact_array['prefix'], $contact_array['first_name'], $contact_array['middle_name'], $contact_array['last_name']]);
                    //Check supplier fields
                    if (in_array($contact_type, ['supplier', 'both'])) {
                        //Check business name
                        if (!empty(trim($value[5]))) {
                            $contact_array['supplier_business_name'] = $value[5];
                        } else {
                            $is_valid  =  false;
                            $error_msg = "Business name is required in row no. $row_no";
                            break;
                        }
                        //Check pay term
                        if (trim($value[9]) != '') {
                            $contact_array['pay_term_number'] = trim($value[9]);
                        } else {
                            $is_valid  =  false;
                            $error_msg = "Pay term is required in row no. $row_no";
                            break;
                        }
                        //Check pay period
                        $pay_term_type = strtolower(trim($value[10]));
                        if (in_array($pay_term_type, ['days', 'months'])) {
                            $contact_array['pay_term_type'] = $pay_term_type;
                        } else {
                            $is_valid  =  false;
                            $error_msg = "Pay term period is required in row no. $row_no";
                            break;
                        }
                    }
                    //Check contact ID
                    if (!empty(trim($value[6]))) {
                        $count = \App\Contact::where('business_id', $business_id)->where('contact_id', $value[6])->count();
                        if ($count == 0) {
                            $contact_array['contact_id'] = $value[6];
                        } else {
                            $is_valid  =  false;
                            $error_msg = "Contact ID already exists in row no. $row_no";
                            break;
                        }
                    }

                    //Tax number
                    if (!empty(trim($value[7]))) {
                        $contact_array['tax_number'] = $value[7];
                    }

                    //Check opening balance
                    if (!empty(trim($value[8])) && $value[8] != 0) {
                        $contact_array['opening_balance'] = trim($value[8]);
                    }

                    //Check credit limit
                    if (trim($value[11]) != '' && in_array($contact_type, ['customer', 'both'])) {
                        $contact_array['credit_limit'] = trim($value[11]);
                    }

                    //Check email
                    if (!empty(trim($value[12]))) {
                        if (filter_var(trim($value[12]), FILTER_VALIDATE_EMAIL)) {
                            $contact_array['email'] = $value[12];
                        } else {
                            $is_valid  =  false;
                            $error_msg = "Invalid email id in row no. $row_no";
                            break;
                        }
                    }

                    //Mobile number
                    if (!empty(trim($value[13]))) {
                        $contact_array['mobile'] = $value[13];
                    } else {
                        $is_valid  =  false;
                        $error_msg = "Mobile number is required in row no. $row_no";
                        break;
                    }

                    //Alt contact number
                    $contact_array['alternate_number'] = $value[14];

                    //Landline
                    $contact_array['landline']         = $value[15];

                    //City
                    $contact_array['city']             = $value[16];

                    //State
                    $contact_array['state']            = $value[17];

                    //Country
                    $contact_array['country']          = $value[18];

                    //address_line_1
                    $contact_array['address_line_1']   = $value[19];
                    //address_line_2
                    $contact_array['address_line_2']   = $value[20];
                    $contact_array['zip_code']         = $value[21];
                    $contact_array['dob']              = $value[22];

                    //Custom fields
                    $contact_array['custom_field1']    = $value[23];
                    $contact_array['custom_field2']    = $value[24];
                    $contact_array['custom_field3']    = $value[25];
                    $contact_array['custom_field4']    = $value[26];

                    $format_data[] = $contact_array;
                }
                if (!$is_valid) {
                    // throw new \Exception($error_msg);
                    $output  = [
                        'status'  => false,
                        'message' => $error_msg,
                    ];
                    return $output ;
                }

                if (!empty($format_data)) {
                    foreach ($format_data as $contact_data) {
                        if($contact_array['type'] == 'both'){ $type_contact = 'contacts'; }else{ $type_contact = $contact_array['type']; }
                        $ref_count = $transactionUtil->setAndGetReferenceCount($type_contact,$business_id);
                        //Set contact id if empty
                        if (empty($contact_data['contact_id'])) {
                            $contact_data['contact_id'] = $commonUtil->generateReferenceNumber($type_contact, $ref_count,$business_id);
                        }
                        $opening_balance = 0;
                        if (isset($contact_data['opening_balance'])) {
                            $opening_balance = $contact_data['opening_balance'];
                            unset($contact_data['opening_balance']);
                        }

                        $contact_data['business_id'] = $business_id;
                        $contact_data['created_by'] = $user_id;

                        $contact = \App\Contact::create($contact_data);
                        \App\Contact::add_account($contact->id,$business_id);
                        if (!empty($opening_balance)) {
                            $transactionUtil->createOpeningBalanceTransaction($business_id, $contact->id, $opening_balance);
                        }
                        $transactionUtil->activityLog($contact, 'imported');
                    }
                    
                }
                $output  = [
                    'status'  => true,
                    'message' => "Import Successfully",
                ];
              
                DB::commit();
            
            return $output;
        }catch(Exception $e){
            $output  = [
                'status'  => false,
                'message' => 'Failed Action',
            ];
            return $output;
        }
        
    }
}
