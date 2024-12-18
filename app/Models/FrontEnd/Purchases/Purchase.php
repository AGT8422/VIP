<?php

namespace App\Models\FrontEnd\Purchases;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\FrontEnd\Utils\GlobalUtil;
use App\Utils\ModuleUtil;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use Spatie\Activitylog\Models\Activity;

class Purchase extends Model
{
    use HasFactory,SoftDeletes;
    // *** REACT FRONT-END PURCHASE *** // 
    // **1** ALL PURCHASE
    public static function getPurchase($user,$request) {
        try{
            $list           = [];
            $business_id    = $user->business_id;
            $purchase       = Purchase::allData("all",null,$business_id,$request); 
            if($purchase == false){ return false;}
            return $purchase;
        }catch(Exception $e){
            return false;
        }
    }
    // **2** CREATE PURCHASE
    public static function createPurchase($user,$data) {
        try{
            $business_id             = $user->business_id;
            $data                    = Purchase::data($user,"create");
            return $data;
        }catch(Exception $e){
            return false;
        }
    }
    // **3** EDIT PURCHASE
    public static function editPurchase($user,$data,$id) {
        try{
            $business_id             = $user->business_id;
            $purchase                = Purchase::allData(null,$id,$business_id,null,"main","edit");
            if(!$purchase){ return false; }
            $data                    = Purchase::data($user,"edit",$id);
            $list["requirement"]     = $data;
            $list["info"]            = $purchase;
            return $list;
        }catch(Exception $e){
            return false;
        } 
    }
    // **4** STORE PURCHASE
    public static function storePurchase($user,$data,$shipping_data,$request) {
        try{
            \DB::beginTransaction();
            $business_id         = $user->business_id;
            $data["business_id"] = $business_id;
            $data["created_by"]  = $user->id;
            $output              = Purchase::createNewPurchase($user,$data,$shipping_data,$request);
            if($output == false){ return false; } 
            \DB::commit();
            return $output;
        }catch(Exception $e){
            return false;
        }
    }
    // **5** UPDATE PURCHASE
    public static function updatePurchase($user,$data,$shipping_data,$id,$request) {
        try{
            \DB::beginTransaction();
            $business_id         = $user->business_id;
            $data["business_id"] = $business_id;
            $data["created_by"]  = $user->id;
            $output              = Purchase::updateOldPurchase($user,$data,$shipping_data,$id,$request);
            \DB::commit();
            return $output;
        }catch(Exception $e){
            return false;
        }
    }
    // **6** DELETE PURCHASE
    public static function deletePurchase($user,$id) {
        try{
            \DB::beginTransaction();
            $business_id    = $user->business_id;
            $purchase       = \App\Transaction::find($id);
            if(!$purchase){ return false; }
            $check           = GlobalUtil::check("purchase",$id);
            if( $check != null) {
                return "related";
            }else {
                
            }
            // $purchase->delete();
            \DB::commit();
            return "saved";
        }catch(Exception $e){
            return "notFound";
        }
    }
    // **7** GET SUPPLIER
    public static function getSupplier($user,$data) {
        try{
            \DB::beginTransaction();
            $business_id    = $user->business_id;
            $contact        = \App\Contact::where('name', 'like', '%' . $data["letters"] . '%')->get();
            if(empty($contact)){ return false; }
            foreach($contact as $i){
                $account                 = \App\Account::where("contact_id",$i->id)->first();
                $debit                   = \App\AccountTransaction::where("account_id",$account->id)->where("for_repeat",null)->where("type","debit")->sum("amount");
                $credit                  = \App\AccountTransaction::where("account_id",$account->id)->where("for_repeat",null)->where("type","credit")->sum("amount");
                $total                   = $debit - $credit;
                $total                   = ($total==0)?0:(($total<0)?(($total)*-1 . " / Credit"):(($total) . " / Debit"));
                $list[] = [
                    "id"               => $i->id,
                    "name"             => $i->name . '  ' . $i->middle_name . '  ' . $i->last_name,
                    "businessName"     => $i->first_name,
                    "contactNumber"    => $i->contact_id,
                    "tax_number"       => $i->tax_number,
                    "mobile"           => $i->mobile,
                    "balance"          => $total,
                    "email"            => $i->email,
                ];
            }
            \DB::commit();
            return  $list ;
        }catch(Exception $e){
            return false;
        }
    }
    // **8** SELECT SUPPLIER
    public static function selectSupplier($user,$data) {
        try{
            \DB::beginTransaction();
            $business_id    = $user->business_id;
            $contact        = \App\Contact::find($data["id"]);
            if(empty($contact)){ return false; }
            $account                 = \App\Account::where("contact_id",$contact->id)->first();
            $debit                   = \App\AccountTransaction::where("account_id",$account->id)->where("for_repeat",null)->where("type","debit")->sum("amount");
            $credit                  = \App\AccountTransaction::where("account_id",$account->id)->where("for_repeat",null)->where("type","credit")->sum("amount");
            $total                   = $debit - $credit;
            $total                   = ($total==0)?0:(($total<0)?(($total)*-1 . " / Credit"):(($total) . " / Debit"));
            $list = [
                "id"               => $contact->id,
                "name"             => $contact->name . '  ' . $contact->middle_name . '  ' . $contact->last_name,
                "businessName"     => $contact->first_name,
                "contactNumber"    => $contact->contact_id,
                "tax_number"       => $contact->tax_number,
                "mobile"           => $contact->mobile,
                "balance"          => $total,
                "email"            => $contact->email,
            ];
            
            \DB::commit();
            return  $list ;
        }catch(Exception $e){
            return false;
        }
    }
    // **9** ENTRY PURCHASE
    public static function entryPurchase($user,$data,$id) {
        try{
            \DB::beginTransaction();
            $list_of_entry        =  [];$list_of_entry2        =  [];
            $line                 =  [];  $array = [];
            // $data                 =  \App\Models\PaymentVoucher::find($id);
            $business_id          =  $user->business_id;
            // ..................................................................1........
            $entry_id             =  \App\AccountTransaction::where('transaction_id',$id)->whereNull("for_repeat")->where('amount','>',0)->groupBy("entry_id")->pluck("entry_id");
            $entry                =  \App\Models\Entry::whereIn("id",$entry_id)->get();
            
            $reference = "";
            foreach($entry as $items){
                $line2                       =  [];
                $reference = $items->ref_no_e;;
                $list_of_entry["id"]               = $items->id;
                $list_of_entry["entry_reference"]  = $items->refe_no_e;
                // ..................................................................2........
                $allData              =  \App\AccountTransaction::where('transaction_id',$id)->where('entry_id',$items->id)->whereNull("for_repeat")->where('amount','>',0)->get();
                $debit  = 0 ; $credit = 0 ;
                foreach($allData as $items){
                    $list_of_entry2["id"]                     = $items->id;
                    $list_of_entry2["account_id"]             = ($items->account != null)?$items->account->name . " | " . $items->account->account_number:"" ;
                    $list_of_entry2["type"]                   = $items->type ;
                    $debit                                   += ($items->type == "debit")?$items->amount:0;
                    $credit                                  += ($items->type == "credit")?$items->amount:0;
                    $list_of_entry2["amount"]                 = $items->amount ;
                    $list_of_entry2["operation_date"]         = $items->operation_date->format("Y-m-d") ;
                    $list_of_entry2["transaction_id"]         = $items->transaction_id ;
                    $list_of_entry2["transaction_payment_id"] = $items->transaction_payment_id ;
                    $list_of_entry2["note"]                   = $items->note ;
                    $list_of_entry2["entry_id"]               = $items->entry_id ;
                    // $list_of_entry2["created_by"]     = $items-> ;
                    if($items->cs_related_id != null){
                        $cost_center = \App\Account::find($items->cs_related_id);
                        $list_of_entry2["cost_center"]        = $cost_center->name;
                    }else{
                        $list_of_entry2["cost_center"]        = "";
                    }
                    $line2[]              = $list_of_entry2;
                } 
                $list_of_entry["allData"]     =  $line2;
                // $array["data"]        =  $data;
                $list_of_entry["balance"]     =  [
                    "total_credit" => $credit ,
                    "total_debit"  => $debit,
                    "balance"      => (($debit - $credit) != 0)?false:true,

                ];
                $line[]             = $list_of_entry;
            } 
            $array["source_reference"] = $reference;
            $array["entries"] = $line;
            \DB::commit();
            return $array;
        }catch(Exception $e){
            return false;
        }
    }
    // **10** ATTACH PURCHASE
    public static function attachPurchase($user,$data,$id) {
        try{
            \DB::beginTransaction();
            $list_of_attach       =  []; 
            $business_id          =  $user->business_id;
            // ..................................................................1........
            $transaction          =  \App\Transaction::find($id);
            // ..................................................................2........
            $attach     =  isset($transaction->document)?$transaction->document:null ;
            if($attach != null){
                foreach($attach as $doc){
                    $list_of_attach[]  =  \URL::to($doc);
                } 
            }
            \DB::commit();
            return $list_of_attach;
        }catch(Exception $e){
            return false;
        }
    }
    // **11** VIEW PURCHASE
    public static function viewPurchase($user,$data,$id) {
        try{
            \DB::beginTransaction();
            $productUtil = new ProductUtil;
            $AllData     = [];$ObjectData= [];  
            $business_id = $user->business_id;
            $voucher     = Purchase::allData(null,$id,$business_id,null,"main","view");
            if($voucher  == false){ return false; }
            // *........ order the result to become like view page
                foreach($voucher as $object){
                  
                    // ## Section One Contact Info & Bill Info
                    $ObjectData["sectionOne"]   = [ 
                        "contact_name"        => $object['contact']->name,
                        "contact_address"     => $object['contact']->address_line_1,
                        "contact_mobile"      => $object['contact']->mobile,
                        "contact_tax"         => $object['contact']->tax_number,

                        "business_number"     => $object['location']->location_id,
                        "business_name"       => $object['location']->name,
                        "business_landmark"   => $object['location']->landmark,
                        "business_city"       => $object['location']->city,
                        "business_state"      => $object['location']->state,
                        "business_country"    => $object['location']->country,
                        
                        "bill_date"                  => $object['date'],
                        "bill_reference"             => $object['reference_no'],
                        "bill_status"                => $object['status'],
                        "bill_payment_status"        => $object['payment_status'],
                        "bill_store_name"            => $object['storeName'],
                        "bill__main_currency_symbol" => ($object['main_currency'])?$object['main_currency']->symbol:0,
                        "bill_currency_symbol"       => ($object['add_currency'])?$object['add_currency']->symbol:0,
                        "currency_exchange"          => floatVal(($object['exchange_price'])?$object['exchange_price']:1),

                    ];
                    // ## Section Two Bill ROWS && Bill Info
                    
                    $bill_info                     = [];
                    // ### main currency
                    $subtotal                      = 0;
                    $dis = 0;
                    $main_tax_amount     = ($object["tax_main"]!=null)?$object["tax_main"]->amount:0;
                    if($object['discount_type'] == "fixed_before_vat"){
                            $dis = $object['discount_amount'];
                    }else if($object['discount_type'] == "fixed_after_vat"){
                            $dis = $object['discount_amount']*100/100+$main_tax_amount;
                    }else if($object['discount_type'] == "percentage"){
                            $dis = $object['sub_total']*$object['discount_amount']/100;
                    }
                    $discount                      = $dis;
                    $price_after_discount          = $object['sub_total']-$dis;
                    $vat                           = $object['tax_amount'];
                    $final_total                   = $object['final_total'];
                    // ### other currency
                    $exc                           = floatVal(($object['exchange_price'])?$object['exchange_price']:1);
                    $discount_currency             = ($dis)/$exc;
                    $price_after_discount_currency = ($object['sub_total']-$dis)/$exc;
                    $vat_currency                  = ($object['tax_amount'])/$exc;
                    $final_total_currency          = ($object['final_total'])/$exc;$row = [];
                    // ## rows
                    foreach($object['list'] as $rw){
                        $tax_amount                                    = ($rw["tax"]!=null)?$rw["tax"]->amount:0;
                        $row[] = [ 
                            "product_name"                             => $rw["product_id"]->name,
                            "product_code"                             => $rw["product_id"]->sku,

                            "quantity"                                 => $rw["quantity"],

                            "unit_price_before_dis_exc_vat"            => $rw["pp_without_discount"],
                            "unit_price_before_dis_inc_vat"            => $rw["pp_without_discount"]+($rw["pp_without_discount"]*$tax_amount/100), 
                            "unit_price_before_dis_exc_vat_currency"   => ($rw["pp_without_discount"])/$exc, 
                            "unit_price_before_dis_inc_vat_currency"   => ($rw["pp_without_discount"]+($rw["pp_without_discount"]*$tax_amount/100))/$exc, 

                            "discount"                                 => $rw["discount_percent"],

                            "unit_price_after_dis_exc_vat"             => $rw["purchase_price"],
                            "unit_price_after_dis_inc_vat"             => $rw["purchase_price_inc_tax"],
                            "unit_price_after_dis_exc_vat_currency"    => ($rw["purchase_price"])/$exc, 
                            "unit_price_after_dis_inc_vat_currency"    => ($rw["purchase_price_inc_tax"])/$exc, 

                            "subtotal_before_tax"                      => $rw["quantity"]*$rw["purchase_price"],
                            "tax"                                      => $tax_amount,
                            "subtotal_after_tax"                       => $rw["quantity"]*$rw["purchase_price_inc_tax"],

                            "mfg_date"                                 => $rw["mfg_date"],
                            "exp_date"                                 => $rw["exp_date"],
                        ];
                        // ## bill info update 
                        // ### main currency
                        $subtotal                      += $rw["quantity"]*$rw["purchase_price"];
                        
                    }
                    $subtotal_currency = $subtotal/$exc;
                    // ## bill info
                    $bill_info = [
                        "subtotal"                      => $subtotal,
                        "discount"                      => $discount,
                        "price_after_discount"          => $price_after_discount,
                        "vat"                           => $vat,
                        "final_total"                   => $final_total,
                        
                        "subtotal_currency"             => $subtotal_currency,
                        "discount_currency"             => $discount_currency,
                        "price_after_discount_currency" => $price_after_discount_currency,
                        "vat_currency"                  => $vat_currency,
                        "final_total_currency"          => $final_total_currency,

                    ];
                    $row_shipping      = []; $contact_amount = 0;  $other_amount = 0;
                    $s_total_amount    = 0; $s_total_vat     = 0;  $s_total_total = 0; 
                    // ## additional Expense
                    foreach($object['additional'] as $shs){
                         $allItems = \App\Models\AdditionalShippingItem::where("additional_shipping_id",$shs->id)->get();
                        foreach($allItems as $ships){
                                if($ships->contact_id == $object['contact']->id){
                                    $contact_amount += $ships->total;
                                }else{
                                    $other_amount   += $ships->total;
                                }
                            $s_total_amount += $ships->amount;
                            $s_total_vat    += $ships->vat;
                            $s_total_total  += $ships->total;
                            $row_shipping[] = [
                                "contact_name"     => ($ships->contact)?$ships->contact->name:"",
                                "amount"           => floatVal($ships->amount),
                                "vat"              => floatVal($ships->vat),
                                "total"            => floatVal($ships->total),
                                "amount_currency"  => $ships->amount/$exc,
                                "vat_currency"     => $ships->vat/$exc,
                                "total_currency"   => $ships->total/$exc,
                                "cost_center"      => ($ships->cost_center)?$ships->cost_center->name:"",
                                "text"             => $ships->text,
                                "date"             => $ships->date,
                            ];  
                        }
                    }
                    $bill_info['subtotal_after_contact_expanse']    = $final_total+$contact_amount;
                    $bill_info['subtotal_after_total_expanse']      = $final_total+$contact_amount+$other_amount;
                    $pay_row                                        = [];
                    $s_total = [
                        "total_amount" => $s_total_amount,
                        "total_vat"    => $s_total_vat   ,
                        "total"        => $s_total_total ,
                    ];
                    $ObjectData["sectionTwo"]      = [
                            "rows"                     => $row,    
                            "row_shipping"             => $row_shipping,
                            "total_shipping"           => $s_total,
                            "bill_info"                => $bill_info,
                            "shipping_details"         => $object['shipping_details'],
                            "additional_shipping_note" => $object['additional_notes'],
                    ];
                    // ## Section Three Bill Payments
                    foreach($object['payments'] as $pw){
                      
                        $pay_row[] = [ 
                            "date"         => $pw['paid_on'],
                            "reference_no" => $pw['payment_ref_no'],
                            "amount"       => $pw['amount'],
                            "payment_mode" => $pw['method'],
                            "payment_note" => $pw['note'],
                        ];
                    }
                    $ObjectData["sectionThree"] = $pay_row; $active=[];
                    // ## Section Four Bill Activity 
                    foreach($object['activities']  as $activity){
                        $changes             = $activity->attributes['properties'];
                        $changes             = json_decode($changes) ;
                        $attributes          = $changes->attributes  ?? null;
                        $old                 = $changes->old ?? null;
                        $status              = $attributes->status   ?? '';
                        $payment_status      = $attributes->payment_status  ?? '';
                        $sub_status          = $attributes->sub_status  ?? '';
                        $shipping_status     = $attributes->shipping_status   ?? '';
                        $status              = in_array($sub_status, ['quotation', 'proforma']) ? $sub_status : $status;
                        $final_total         = $attributes->final_total  ?? 0;
                        
                        $old_status          = $old->status  ?? '';
                        $old_sub_status      = $old->sub_status  ?? '';
                        $old_shipping_status = $old->shipping_status  ?? '';
                        $old_status          = in_array($old_sub_status, ['quotation', 'proforma']) ? $old_sub_status : $old_status;
                        $old_final_total     = $old->final_total  ?? 0;
                        $old_payment_status  = $old->payment_status  ?? '';
                        $update_note         = $activity->getExtraProperty('update_note');
                        $statuses            = $productUtil->orderStatuses(1);
                        if(!empty($status) && $status != $old_status){
                            if(!empty($old_status)){
                                $from = ($statuses[$old_status]) ? $statuses[$old_status]: '';
                                $to   = ($statuses[$status]) ? $statuses[$status]: '';
                                $update_notes["status"]  = [ "name"=>"Status","from" =>  $from , "to"   =>   $to  ];
                            }else{
                                $to   = ($statuses[$status]) ? $statuses[$status]: '';
                                $update_notes["status"]  = [ "name"=>"Status","value" =>  $to   ];
                            }
                        }
                        if(!empty($shipping_status) && $shipping_status != $old_shipping_status){
                            if(!empty($old_shipping_status)){
                                $from = ($shipping_statuses[$old_shipping_status]) ? $shipping_statuses[$old_shipping_status]: '';
                                $to   = ($shipping_statuses[$shipping_status]) ? $shipping_statuses[$shipping_status]: '';
                                $update_notes["shipping_status"]  = [ "name"=>"Shipping Status","from" =>  $from , "to"   => $to  ];
                            }else{
                                $to   = ($shipping_statuses[$shipping_status]) ? $shipping_statuses[$shipping_status]: '';
                                $update_notes["shipping_status"]  = [ "name"=>"Shipping Status","value" =>  $to  ];
                            }
                        }
                        if(!empty($final_total) && $final_total != $old_final_total){
                            if(!empty($old_final_total)){
                                $from =  $old_final_total ;
                                $to   =  $final_total;
                                $update_notes["final_total"]  = [ "name"=>"Final Total","from" => floatVal($from), "to"   => floatVal($to) ];
                            }else{
                                $to   =  $final_total;
                                $update_notes["final_total"]  = [ "name"=>"Final Total","value" => floatVal($to)  ];
                            }
                        }
                        if(!empty($payment_status) && $payment_status != $old_payment_status){
                            if(!empty($old_payment_status)){
                                $from =  $old_payment_status ;
                                $to   =  $payment_status;
                                $update_notes["payment_status"]  = [ "name"=>"Payment Status","from" =>  $from , "to"   =>  $to  ];
                            }else{
                                $to   =  $payment_status;
                                $update_notes["payment_status"]  = [ "name"=>"Payment Status","value" =>  $to   ];
                            }
                        }
                        $update_notes["note"]    = $update_note;
                        $active[] = [  
                            'date'        =>  $activity->created_at->format('Y-m-d h:s:i a'),
                            'description' =>  $activity->description,
                            'created_by'  =>  ($activity->causer)?$activity->causer->user_full_name:"",
                            'update_note' =>  $update_notes,
                        ];
                    }
                    $ObjectData["sectionFour"]  = $active;
                }
                $AllData =  $ObjectData;
            // *.................................................*
            \DB::commit();
            return $AllData;
        }catch(Exception $e){
            return false;
        }
    }
    // **12** PRINT PURCHASE
    public static function printPurchase($user,$data,$id) {
        try{
            \DB::beginTransaction();
            $business_id  = $user->business_id;
            $transaction  = \App\Transaction::find($id);
            if(empty($transaction)){ return false; }
            $file  =   \URL::to('reports/purchase/'.$transaction->id.'?ref_no='.$transaction->ref_no) ; 
            \DB::commit();
            return $file;
        }catch(Exception $e){
            return false;
        }
    }
    // **13** MAP PURCHASE
    public static function mapPurchase($user,$data,$id) {
        try{
            \DB::beginTransaction();
            $map          = [];
            $business_id  = $user->business_id;
            $allData      = \App\Models\StatusLive::where("business_id",$business_id)->where("transaction_id",$id)->get();
            if(count($allData)==0){ return false; }
            foreach($allData as $line){
                $map[] = [
                    "id"           =>  $line->id,
                    "state"        =>  $line->state,
                    "reference_id" =>  $line->transaction_id,
                    "reference_no" =>  $line->reference_no,
                    "price"        =>  $line->price,
                    "date"         =>  $line->created_at->format('Y-m-d'),
                ];
            }
            \DB::commit();
            return $map;
        }catch(Exception $e){
            return false;
        }
    }
    // **14** Add Payment PURCHASE
    public static function addPaymentPurchase($user,$data,$id) {
        try{
            \DB::beginTransaction();
            $business_id  = $user->business_id;
            $allData      = Purchase::dataPayment($user,$id);
            \DB::commit();
            return $allData;
        }catch(Exception $e){
            return false;
        }
    }
    // **15** View Payment PURCHASE
    public static function viewPaymentPurchase($user,$data,$id) {
        try{
            \DB::beginTransaction();
            $business_id  = $user->business_id;
            $allData      = Purchase::viewPayment($user,$id);
            \DB::commit();
            return $allData;
        }catch(Exception $e){
            return false;
        }
    }
    // **16** GET  PURCHASE STATUS 
    public static function getUpdateStatusPurchase($user,$data,$id) {
         try{
            \DB::beginTransaction();
            $productUtil               = new \App\Utils\Util(); 
            $orderStatuses             = $productUtil->orderStatuses();
            $business_id               = $user->business_id;
            $allData['contact_id']     = (\App\Transaction::find($id))?\App\Transaction::find($id)->contact_id:"";
            $allData['status']         = (\App\Transaction::find($id))?\App\Transaction::find($id)->status:"";
            if($allData['status']  == 'received'){
                $allData['list_status']    = ["received"=>"recieved"];
            }else{
                $allData['list_status']    = $orderStatuses;
            }
            \DB::commit();
            return $allData;
        }catch(Exception $e){
            return false;
        }
    }
    // **17** UPDATE  PURCHASE STATUS 
    public static function updateStatusPurchase($user,$data,$id) {
        try{
            \DB::beginTransaction();
            $transactionUtil          = new TransactionUtil;
            $productUtil              = new ProductUtil;
            $business_id              = $user->business_id;
            $type_transaction         = "purchase";
            $transaction              = \App\Transaction::where('business_id', $business_id)
                                                    ->where('type', $type_transaction)
                                                    ->with(['purchase_lines'])
                                                    ->find($id);
            $transaction_before       =  $transaction->replicate();
            $old_status               =  $transaction->status;
            $before_status            =  $transaction->status;
            $update_data['status']    =  $data['status'];
         
            $total_ship               = \App\Models\Purchase::supplier_shipp($id);
        
            //update transaction
            $transaction->update($update_data);
            $currency_details          = $transactionUtil->purchaseCurrencyDetails($business_id);
            
            if ( $transaction->status == 'received'  && $old_status != 'received' ) {
                    $currency_details             =  $transactionUtil->purchaseCurrencyDetails($business_id);
                    $type                         = 'purchase_receive';
                    $ref_count                    =  $productUtil->setAndGetReferenceCount($type,$business_id);
                    $receipt_no                   =  $productUtil->generateReferenceNumber($type, $ref_count,$business_id);
                    $tr_received                  =  new \App\Models\TransactionRecieved;
                    $tr_received->store_id        =  $transaction->store;
                    $tr_received->transaction_id  =  $transaction->id;
                    $tr_received->business_id     =  $transaction->business_id ;
                    $tr_received->reciept_no      =  $receipt_no ;
                    $tr_received->ref_no          =  $transaction->ref_no;
                    $tr_received->status          =  "purchase"; 
                    $tr_received->is_returned     =  0; 
                    $tr_received->save();
                    \App\Models\StatusLive::insert_data_p($business_id,$transaction, $data['status'],$tr_received);
            } 

            foreach ($transaction->purchase_lines as $purchase_line) {
                $productUtil->updateProductStock($before_status, $transaction, $purchase_line->product_id, $purchase_line->variation_id, $purchase_line->quantity, $purchase_line->quantity, $currency_details);
                if ( $transaction->status == 'received'  && $old_status != 'received' ) {
                    $prev                      =  \App\Models\RecievedPrevious::where('transaction_id',$transaction->id)
                                                                    ->where('product_id',$purchase_line->product_id)
                                                                    ->where("line_id",$purchase_line->id)
                                                                    ->first();
                    if (empty($prev)) {
                        $prev                              =  new \App\Models\RecievedPrevious;
                        $prev->product_id                  =  $purchase_line->product_id;
                        $prev->store_id                    =  $transaction->store;
                        $prev->business_id                 =  $transaction->business_id ;
                        $prev->transaction_id              =  $transaction->id;
                        $prev->unit_id                     =  $purchase_line->product->unit_id;
                        $prev->total_qty                   =  $purchase_line->quantity;
                        $prev->current_qty                 =  $purchase_line->quantity;
                        $prev->remain_qty                  =  0;
                        $prev->transaction_deliveries_id   =  $tr_received->id;
                        $prev->product_name                =  $purchase_line->product->name;  
                        $prev->line_id                     =  $purchase_line->id;  
                        $prev->is_returned                 =  0; 
                        $prev->save();
                    } 
                    $type_move                             = "plus";
                    $qty                                   = ($purchase_line->quantity);
                    \App\Models\WarehouseInfo::update_stoct($purchase_line->product_id,$transaction->store,$qty,$transaction->business_id);
                    \App\MovementWarehouse::movemnet_warehouse($transaction,$purchase_line->product,$purchase_line->quantity,$transaction->store,$purchase_line,$type_move,$prev->id);
                    
                }elseif ( $transaction->status != 'received'  && $old_status == 'received' ) {
                    $currency_details = $transactionUtil->purchaseCurrencyDetails($business_id);
                    \App\MovementWarehouse::where('transaction_id',$transaction->id)->delete();
                    \App\Models\RecievedPrevious::where('transaction_id',$transaction->id)->delete();
                    \App\Models\TransactionRecieved::where('transaction_id',$transaction->id)->delete();
                }
            }
            
            if (($old_status !=  'final' && $old_status != 'received' ) &&  ($data['status'] == 'final' ||  $data['status'] == 'received' ) ) {
                \App\AccountTransaction::add_purchase($transaction,$total_ship);
            }elseif (($old_status ==  'final' || $old_status == 'received' ) &&  ($data['status'] != 'final' && $data['status'] != 'received' ) ){
                \App\AccountTransaction::where('transaction_id',$transaction->id)->delete();
                \App\Models\Entry::delete_entries($transaction->id);
                \App\Models\ItemMove::delete_move($transaction->id);
            }

            //Update mapping of purchase & Sell.
            $transactionUtil->adjustMappingPurchaseSellAfterEditingPurchase($before_status, $transaction, null);
            //Adjust stock over selling if found
            $productUtil->adjustStockOverSelling($transaction);

            if ($data['status'] ==  'received' || $data['status'] ==  'final') {
                $cost            =  0;
                $without_contact =  0;
                \App\Models\AdditionalShipping::add_purchase_payment($transaction->id,null,null,null,$user);
                $data_ship       = \App\Models\AdditionalShipping::where("transaction_id",$transaction->id)->first();
                if(!empty($data_ship)){
                    $ids = $data_ship->items->pluck("id");
                    foreach($ids as $i){
                        $data_shipment    = \App\Models\AdditionalShippingItem::find($i);
                        $cost            += ($data_shipment->contact_id == $data['contact_id'])?$data_shipment->total:0;
                        $without_contact += ($data_shipment->contact_id == $data['contact_id'])?0:$data_shipment->total;
                        \App\Models\StatusLive::insert_data_sh($business_id,$transaction,$data_shipment,"Add Expense");
                    }
                }
            }else{
                \App\Models\Entry::where("account_transaction",$transaction->id)->delete();
                $shipping_id = \App\Models\AdditionalShipping::where("transaction_id",$transaction->id)->first();
                if(!empty($shipping_id)){
                    \App\Models\Entry::where("shipping_id",$shipping_id->id)->delete();
                }
                \App\AccountTransaction::where('transaction_id',$transaction->id)->whereNotNull('additional_shipping_item_id')->delete();
                \App\Models\StatusLive::where("transaction_id",$transaction->id)->whereNotNull("shipping_item_id")->delete();
                // \App\Models\StatusLive::where("transaction_id",$transaction->id)->where("num_serial","!=",1)->delete();
                \App\Models\StatusLive::insert_data_p($business_id,$transaction,$data['status']);
            }

            if (( $old_status != 'received' ) &&  ( $data['status'] == 'received' ) ) {
                $cost            = 0;
                $without_contact = 0; 
                $data_ship       = \App\Models\AdditionalShipping::where("transaction_id",$transaction->id)->first();
                if(!empty($data_ship)){
                    $ids = $data_ship->items->pluck("id");
                    foreach($ids as $i){
                        $data_shipment    = \App\Models\AdditionalShippingItem::find($i);
                        $cost            += ($data_shipment->contact_id == $data['contact_id'])?$data_shipment->amount:0; 
                        $without_contact += ($data_shipment->contact_id == $data['contact_id'])?0:$data_shipment->amount;
                    }
                }
                $total_expense = $cost + $without_contact; 
                if ($transaction->discount_type == "fixed_before_vat"){
                    $dis = $transaction->discount_amount;
                }else if ($transaction->discount_type == "fixed_after_vat"){
                    $tax = \App\TaxRate::find($transaction->tax_id);
                    $dis = ($transaction->discount_amount*100)/(100+(($tax)?$tax->amount:0)) ;
                }else if ($transaction->discount_type == "percentage"){
                    $dis = ($transaction->total_before_tax *  $$transaction->discount_amount)/100;
                }else{
                    $dis = 0;
                }
                 
                $before = \App\Models\WarehouseInfo::qty_before($transaction);
                \App\Models\ItemMove::update_itemMove($transaction,$total_expense,$before,null,$dis);
                 
            }
             
            $transactionUtil->activityLog($transaction, 'edited', $transaction_before);
            $archive         =  \App\Models\ArchiveTransaction::save_parent($transaction,"edit");
            $purchase_lines  = \App\PurchaseLine::where('transaction_id', $transaction->id)->get();
            foreach($purchase_lines as $it){
                \App\Models\ArchivePurchaseLine::save_purchases($archive , $it);
            }
            \DB::commit();
            return true;
        }catch(Exception $e){
            return false;
        }
    }
    // **18** All Maps 
    public static function allMapPurchase($user,$data) {
        try{ 
            $allData       = [];
            $business_id   = $user->business_id; 
            $all           = \App\Models\StatusLive::where("business_id",$business_id)->groupBy('transaction_id')->get();
            foreach($all as $line){
                $state = ($line->transaction)?$line->transaction->type:"";
                if( ( $state == "purchase" || $state == "purchase_return" ) && $state!= ""){
                    $allData[] = [
                        'id'           => $line->id,
                        'view_id'      => $line->transaction_id,
                        'reference_no' => $line->reference_no,
                        'state'        => $state,
                        'date'         => $line->created_at->format('Y-m-d h:i a')
                    ];
                }
            } 
            return $allData;
        }catch(Exception $e){
            return false;
        }
    }
    // **19** All Log File 
    public static function allLogFilePurchase($user,$data) {
        try{ 
            $allData       = [];
            $business_id   = $user->business_id; 
            $all           = \App\Transaction::where("business_id",$business_id)->whereIn('type',['purchase','purchase_return'])->pluck('id');
            if(count($all)==0){return false;}
            foreach($all as $id){ 
                $purchase      = \App\Transaction::find($id);
                $child         = \App\Models\ArchiveTransaction::where("new_id",$id)->orderby("id","asc")->get();
                $child_ids     = \App\Models\ArchiveTransaction::where("new_id",$id)->orderby("id","asc")->pluck('id');
                $payment_main  = \App\TransactionPayment::where("transaction_id",$id)->orderby("id","desc")->get();
                $payment_child = \App\Models\ParentArchive::where("tp_transaction_no",$id)->orderby("id","desc")->get(); 
                $logFile       = [];
                $old           = [];
                $movement      = [];
                foreach($child as $key => $i){
                    $title     = ($key==0)?"Create Purchase":"Edit Purchase";
                    if($key == 0){
                        $old =  $i; 
                        $new =  $old; 
                        $movement[]    = [
                            "title"         => $title,
                            "reference"     => $i->ref_no,
                            "date"          => $i->created_at->format('Y-m-d'),
                            "time"          => $i->created_at->format('h:i:s a'),
                            "description"   => "Create New Purchase with " . $new->status . " Status from Supplier Details - Supplier Name : " . (($new->contact)? $new->contact->name:"") . " - Supplier Mobile : " . (($new->contact)? $new->contact->mobile:"") ,
                            "modify"        => Purchase::compare($old,$new),
                            "price"         => $i->final_total,
                            "status"        => "Create",
                            "old_status"    => "",
                            "user_name"     => ($i->sales_person)?$i->sales_person->first_name:"",
                            "user_logo"     => ($i->sales_person)?(($i->sales_person->media)?$i->sales_person->media->display_url:""):"",
                            "log_reference" => $i->ref_number,
                            "new_bill"      => $new,
                            "bill"          => $old
                        ]; 
                    }else if($key > 0){
                        $old =  $child[$key-1]; 
                        $new =  $i; 
                        if(count($child)>$key+1){
                            $movement[]    = [
                                "title"         => $title,
                                "reference"     => $i->ref_no,
                                "date"          => $i->created_at->format('Y-m-d'),
                                "time"          => $i->created_at->format('h:i:s a'),
                                "description"   => "Edit Old Purchase with " . $new->status . " Status from Supplier Details - Supplier Name : " . (($new->contact)? $new->contact->name:"") . " - Supplier Mobile : " . (($new->contact)? $new->contact->mobile:"") ,
                                "modify"        => Purchase::compare($old,$new),
                                "price"         => $i->final_total,
                                "status"        => "Edit",
                                "old_status"    => ($key>1)?"Edit":"Create",
                                "user_name"     => ($i->sales_person)?$i->sales_person->first_name:"",
                                "user_logo"     => ($i->sales_person)?(($i->sales_person->media)?$i->sales_person->media->display_url:""):"",
                                "log_reference" => $i->ref_number,
                                "new_bill"      => $new,
                                "bill"          => $old
                            ]; 
                        }else{
                            $movement[]    = [
                                "title"         => $title,
                                "reference"     => $i->ref_no,
                                "date"          => $i->created_at->format('Y-m-d'),
                                "time"          => $i->created_at->format('h:i:s a'),
                                "description"   =>  "Edit Old Purchase with " . $new->status . " Status from Supplier Details - Supplier Name : " . (($new->contact)? $new->contact->name:"") . " - Supplier Mobile : " . (($new->contact)? $new->contact->mobile:"") ,
                                "modify"        => Purchase::compare($old,$new),
                                "price"         => $i->final_total,
                                "status"        => "Edit",
                                "old_status"    => ($key>1)?"Edit":"Create",
                                "user_name"     => ($i->sales_person)?$i->sales_person->first_name:"",
                                "user_logo"     => ($i->sales_person)?(($i->sales_person->media)?$i->sales_person->media->display_url:""):"",
                                "log_reference" => $i->ref_number,
                                "new_bill"      => $new,
                                "bill"          => $old
                            ]; 
                        }
                    }
                } 
                $allData[] = $movement; 
            }
            return $allData;
        }catch(Exception $e){
            return false;
        }
    }
    
    
    // ****** MAIN FUNCTIONS 
    // ****** Purchase
    // **1** CREATE PURCHASE
    public static function createNewPurchase($user,$data,$shipping_data,$request) {
       try{
            // .0......................................
            $business_id       = $user->business_id;
            $user_id           = $user->id;
            $productUtil       = new  ProductUtil(); 
            $transactionUtil   = new  TransactionUtil(); 
            //Update business exchange rate.
           \App\Business::update_business($business_id, ['p_exchange_rate' => ($data['exchange_rate'])]);
           $actual_discount    = 0; 
           
           // .1...............................discount..
            if ($data['discount_type']  == 'fixed') {
                $data['discount_amount'] =  $data['discount_amount'];
            } else if ($data['discount_type'] == 'percentage') {
                $data['discount_amount'] =  $data['discount_amount'];
                $actual_discount         =  $data['discount_amount'];
                if($request->currency_id != null){  
                    $actual_discount = ($data['total_before_tax']*($data['discount_amount'])/100)  ;
                }
            } else if($data['discount_type'] == null){
                $data['discount_amount'] = 0;
            } else {
                if($data['discount_type']  == 'fixed_before_vat'){
                    $data['discount_amount'] = $request->discount_amount;
                    if($request->currency_id != null){  
                        $actual_discount = $request->discount_amount * $request->currency_id_amount ;
                    }
                }elseif($data['discount_type']  == 'fixed_after_vat'){
                    $VT                                  = \App\TaxRate::find($request->tax_id);
                    $vat_amount                          = ($VT != NULL)?$VT->amount:0; 
                    $data['discount_amount'] = $request->discount_amount ;
                    if($request->currency_id != null){  
                        $actual_discount = ($request->discount_amount*100/(100+$vat_amount)) * $request->currency_id_amount ;
                    }
                }else{
                    $data['discount_amount'] = $request->discount_amount;
                }
            }
            // .2.................................const..
            $data['shipping_charges']      = 0;
            $data['ship_amount']           = 0;
            $data['type']                  = "purchase";
            $data['payment_status']        = 'due'; 
            $data['store']                 = $data["store_id"];
            $data['business_id']           = $business_id;
            $data['created_by']            = $user_id;
            // .2.................end......const.........
            // .3.......................currency.........
            $data['exchange_price']       = $data['currency_id_amount'];
            $data["amount_in_currency"]   = ($data['currency_id_amount'] != "" && $data['currency_id_amount'] != 0 && $data['currency_id'] != null)? $data['final_total'] / $data['currency_id_amount']:null;
            // .3.................end..currency..........
            // .4................................media...
            //upload document
            $document_purchase = [];
            if ($request->hasFile('document')) {
                $id_sf = 1;
                foreach ($request->file('document') as $file) {
                    if( $file->getClientMimeType() == "image/jpeg"){
                        $file_name     = 'public/uploads/documents/'.time().'_'.$id_sf++.'.'.$file->getClientOriginalExtension();
                        $datas         = getimagesize($file);
                        $width         = $datas[0];
                        $height        = $datas[1];
                        $half_width    = $width/2;
                        $half_height   = $height/2;
                        $imgs          = \Image::make($file)->resize($half_width,$half_height); //$request->$file_name->storeAs($dir_name, $new_file_name)  ||\public_path($new_file_name)
                        if ($imgs->save(base_path("$file_name"),20)) {
                            $uploaded_file_name      = $file_name;
                            array_push($document_purchase,$file_name);
                         }
                    }else{    
                        $file_name =  'public/uploads/documents/'.time().'_'.$id_sf++.'.'.$file->getClientOriginalExtension();
                        $file->move('public/uploads/documents',$file_name);
                        array_push($document_purchase,$file_name);
                    }
                }
            }
            $data['document']      = json_encode($document_purchase);
            // .4...................end........media.....
            //Generate reference number
            $ref_count             = GlobalUtil::SetReferenceCount($data['type'],$business_id);
            $data['ref_no']        = GlobalUtil::GenerateReferenceCount($data['type'], $ref_count,$business_id);
            // .4.........................................
            // .5................................transaction...
            if($data["pay_term_type"] != null){
                $type_date                 = "";
                $bill_date                 = \Carbon::createFromFormat('Y-m-d H:i',$data['transaction_date']);
                $bill_date_due             = \Carbon::createFromFormat('Y-m-d H:i',$data["pay_term_type"])->diff($bill_date)->days;
                $data["pay_term_type"]     = "days"; 
                $data["pay_term_number"]   = $bill_date_due+1;
            }else{
                $data["pay_term_type"]     = null; 
                $data["pay_term_number"]   = null;
            } 
            $transaction           = \App\Transaction::create($data);
            $archive               = \App\Models\ArchiveTransaction::save_parent($transaction,"create");
            // .5..............................................
            // .6.............................expense.......
            $document_expense = [];
            if ($request->hasFile('document_expense')) {
                foreach ($request->file('document_expense') as $file) {
                    if( $file->getClientMimeType() == "image/jpeg"){
                        $file_name     = 'public/uploads/documents/'.time().'_'.$id_sf++.'.'.$file->getClientOriginalExtension();
                        $datass        = getimagesize($file);
                        $width         = $datass[0];
                        $height        = $datass[1];
                        $half_width    = $width/2;
                        $half_height   = $height/2;
                        $imgs          = \Image::make($file)->resize($half_width,$half_height); //$request->$file_name->storeAs($dir_name, $new_file_name)  ||\public_path($new_file_name)
                        if ($imgs->save(base_path("$file_name"),20)) {
                            $uploaded_file_name      = $file_name;
                            array_push($document_expense,$file_name);
                         }
                    }else{    
                        $file_name =  'public/uploads/documents/'.time().'_'.$id_sf++.'.'.$file->getClientOriginalExtension();
                        $file->move('public/uploads/documents',$file_name);
                        array_push($document_expense,$file_name);
                    }
                     
                }
            }
            // .6.............................expense.......
            // .7.......................entry...expense.....
            if(isset($shipping_data['shipping_amount'])){
                if($shipping_data['shipping_amount'] != null){
                    \App\Models\AdditionalShipping::add_purchase($transaction->id,$shipping_data,$document_expense,null,null,null,null,null,$user);
                }
            }
            // .7.............................expense.......
            // .8.............................purchase.......
            $purchase_lines    = [];
            $purchases         = $request->input('purchases');
            $type_update       = "create";
            $currency_details  = $transactionUtil->purchaseCurrencyDetails($business_id);
            $RESULT            = $productUtil->createOrUpdatePurchaseLines($transaction, $purchases, $currency_details, true,$type_update,null,$archive,$request,"purchase");
            // .8.............................purchase.......

            // .10........................payment.....
            // .10...................end..payment.....

            // .11..................................check over selling.....
            $productUtil->adjustStockOverSelling($transaction);
            $transactionUtil->activityLog($transaction, 'added');
            // .11...........................end.......check over selling..
            // .12.........................................................
            if ($request->status ==  'received' || $request->status ==  'final') {
                if(isset($shipping_data['shipping_amount'])){
                    if($shipping_data['shipping_amount'] != null){
                        \App\Models\AdditionalShipping::add_purchase_payment($transaction->id,null,null,null,$user);
                    }
                }
               \App\Models\StatusLive::insert_data_p($business_id,$transaction,$request->status);
               $cost=0;$without_contact=0;
               if(isset($shipping_data['shipping_amount'])){
                if($shipping_data['shipping_amount'] != null){
                        $data_ship = \App\Models\AdditionalShipping::where("transaction_id",$transaction->id)->first();
                        $ids = $data_ship->items->pluck("id");
                        foreach($ids as $i){
                            $data_shippment   = \App\Models\AdditionalShippingItem::find($i);
                            if($data_shippment->contact_id == $request->contact_id){ 
                                $cost += $data_shippment->amount;
                            }else{
                                $without_contact += $data_shippment->amount;
                            }
                            \App\Models\StatusLive::insert_data_sh($business_id,$transaction,$data_shippment,"Add Expense");
                        }
                    }
                }
                
            }
            if($request->status ==  'received'){
                $total_expense = $cost + $without_contact;
                if ($request->discount_type == "fixed_before_vat"){
                    $dis = $request->discount_amount;
                }else if ($request->discount_type == "fixed_after_vat"){
                    $tax = \App\TaxRate::find($request->tax_id);
                     if(!empty($tax)){
                        $dis = ($request->discount_amount*100)/(100+$tax->amount) ;
                    }else{
                        $dis = ($request->discount_amount*100)/(100) ;
                    }
                 }else if ($request->discount_type == "percentage"){
                    $dis = ($request->total_before_tax *  $request->discount_amount)/100;
                }else{
                    $dis = 0;
                }
                $before = \App\Models\WarehouseInfo::qty_before($transaction);
                \App\Models\ItemMove::create_itemMove($transaction,$total_expense,$before,null,$dis);

            }
            // .12......................................................................................

            return true; 
        }catch(Exception $e){
            return false;
        }
    }
    // **2** UPDATE PURCHASE
    public static function updateOldPurchase($user,$data,$shipping_data,$id,$request) {
        try{
            // .0...........................main info ..
            $business_id                 = $user->business_id;
            // .0.....................end...main info ..
            // .1...........................main purchase ..
            $transaction                 = \App\Transaction::find($id);
            // .1....................end....main purchase ..
            // .2...........................archive purchase ..
            $archive                     =  \App\Models\ArchiveTransaction::save_parent($transaction,"edit");
            $purchase_lines              =  \App\PurchaseLine::where("transaction_id",$transaction->id)->get();
            foreach($purchase_lines as $it){
                \App\Models\ArchivePurchaseLine::save_purchases( $archive , $it);
            }
            // .2....................end....archive purchase ..
            // .3...........................old purchase ......
            $old_status                  = $transaction->status;
            $old_trans                   = $transaction->cost_center_id;
            $old_account                 = $transaction->contact_id;
            $old_discount                = $transaction->discount_amount;
            $old_tax                     = $transaction->tax_amount;
            $old_document                = ($transaction->document != null)?$transaction->document:[];
            $before_status               = $transaction->status;
            $business_id                 = $transaction->business_id;
            $enable_product_editing      = true;
            $transaction_before          = $transaction->replicate();
            $sup                         = $data['supplier_id'];
            $sup_id                      = $data['sup_id'];
            $data['contact_id']          = $data['supplier_id'] ;
            $exchange_rate               = $data['exchange_rate'];

            // .3....................end....old purchase ......
            // .4...........................discount ..........
            if ($data['discount_type']  == 'fixed') {
                $data['discount_amount'] = $data['discount_amount'] * $exchange_rate;
            } elseif ($data['discount_type'] == 'percentage') {
                $data['discount_amount'] = $data['discount_amount'] ;
            } else if($data['discount_type'] == null){
                $data['discount_amount'] = 0;
            } else {
                $data['discount_amount'] = $request->discount_amount;
            }
            $data['tax_amount']          = $data['tax_amount'] * $exchange_rate;
            $data['final_total']         = ($data['final_total'] + $data['ADD_SHIP']) * $exchange_rate;
            $data['dis_type']            = $data["dis_type"];
            $data["ship_amount"]         = 0;
            $data["currency_id"]         = ($data["currency_id"]!=null)?$data["currency_id"]:null;
            $data["exchange_price"]      = $data["currency_id_amount"];
            $data['discount_type']       = $data["discount_type"];
            $data["amount_in_currency"]  = ($data["currency_id_amount"] != "" && $data["currency_id_amount"] != 0 && $data["currency_id"] != null)? $data["final_total"] / $data["currency_id_amount"]:null;
            $data['sup_refe']            = $data["sup_refe"];
            $data['list_price']          = $data["list_price"];
            $data["store"]               = $data["store_id"];
            // .4....................end....discount ... ......
             
            // .5.................................. media .....
            if ($request->hasFile('document')) {
                $id_cv = 1;
                foreach ($request->file('document') as $file) {
                    if( $file->getClientMimeType() == "image/jpeg"){
                        $file_name     = 'public/uploads/documents/'.time().'_'.$id_cv++.'.'.$file->getClientOriginalExtension();
                        $datas         = getimagesize($file);
                        $width         = $datas[0];
                        $height        = $datas[1];
                        $half_width    = $width/2;
                        $half_height   = $height/2;
                        $imgs          = \Image::make($file)->resize($half_width,$half_height); //$request->$file_name->storeAs($dir_name, $new_file_name)  ||\public_path($new_file_name)
                        if ($imgs->save(base_path("$file_name"),20)) {
                            $uploaded_file_name      = $file_name;
                            array_push($old_document,$file_name);
                         }
                    }else{    
                        $file_name =  'public/uploads/documents/'.time().'_'.$id_cv++.'.'.$file->getClientOriginalExtension();
                        $file->move('public/uploads/documents',$file_name);
                        array_push($old_document,$file_name);
                    }
                }
            }
            if(json_encode($old_document)!="[]"){
                $data['document']         = json_encode($old_document);
                
            }
            // .5....................end...... media ..........
            // .6............................. expense media ..
            $document_expense = $request->old_document??[];
            if ($request->hasFile('document_expense')) {
                $id_cc = 1;
                foreach ($request->file('document_expense') as $file) {
                    if( $file->getClientMimeType() == "image/jpeg"){
                        $file_name     = 'public/uploads/documents/'.time().'_'.$id_cv++.'.'.$file->getClientOriginalExtension();
                        $datas         = getimagesize($file);
                        $width         = $datas[0];
                        $height        = $datas[1];
                        $half_width    = $width/2;
                        $half_height   = $height/2;
                        $imgs          = \Image::make($file)->resize($half_width,$half_height); //$request->$file_name->storeAs($dir_name, $new_file_name)  ||\public_path($new_file_name)
                        if ($imgs->save(base_path("$file_name"),20)) {
                            $uploaded_file_name      = $file_name;
                            array_push($document_expense,$file_name);
                         }
                    }else{    
                        $file_name =  'public/uploads/documents/'.time().'_'.$id_cc++.'.'.$file->getclientoriginalextension();
                        $file->move('public/uploads/documents',$file_name);
                        array_push($document_expense,$file_name);
                    }
                }
            } 
            // .6....................end...... expense media ..
            //.. 7   ..........  .............  update purchase 
            \App\Models\AdditionalShipping::update_purchase($transaction->id,$shipping_data,$document_expense,null,null,$user);
            if($data["pay_term_type"] != null){
                $type_date                 = "";
                $bill_date                 = \Carbon::createFromFormat('Y-m-d H:i',$data['transaction_date']);
                $bill_date_due             = \Carbon::createFromFormat('Y-m-d H:i',$data["pay_term_type"])->diff($bill_date)->days;
                $data["pay_term_type"]     = "days"; 
                $data["pay_term_number"]   = $bill_date_due;
            } 
            $transaction->update($data);
            $transaction->exchange_price     = ($data["currency_id"]!=null)?$data["currency_id_amount"]:null;
            $transaction->amount_in_currency = ($data["currency_id_amount"] != "" && $data["currency_id_amount"] != 0 && $data["currency_id"] != null)? ($data['final_total'] / $data["currency_id_amount"]):null ;
            $transaction->update() ;
            //.. 7   .............end........  update purchase 
            
            $productUtil                     = new  ProductUtil(); 
            $transactionUtil                 = new  TransactionUtil(); 
            $currency_details                = $transactionUtil->purchaseCurrencyDetails($business_id);

            //Update transaction payment status
            $payment_status                  = $transactionUtil->updatePaymentStatus($transaction->id);
            $transaction->payment_status     = $payment_status;
            $purchases                       = $request->input('purchases');
            
            //................................. update  purchase ...................... 
            //......................................................................... 
            $type_update                     = "update";
            $delete_purchase_lines           = $productUtil->createOrUpdatePurchaseLines($transaction, $purchases, $currency_details, $enable_product_editing, $before_status,$type_update,null,$request,"purchase");
            //......................................................................... 
            //......................................................................... 
            
            //Update mapping of purchase & Sell.
            $transactionUtil->adjustMappingPurchaseSellAfterEditingPurchase($before_status, $transaction, $delete_purchase_lines);
            //Adjust stock over selling if found
            $productUtil->adjustStockOverSelling($transaction);
            $transactionUtil->activityLog($transaction, 'edited', $transaction_before);
            // update accounts 
            $currency_details = $transactionUtil->purchaseCurrencyDetails($business_id);
            
            $purchase_lines   = \App\PurchaseLine::where('transaction_id', $transaction->id)->get();
            /// search in childs
            foreach ($purchase_lines as $purchase_line) {
               
                if ( $transaction->status == 'received'  && $old_status != 'received' ) {
                    $id_tr = 0;
                    $trp   = \App\Models\TransactionRecieved::where("transaction_id",$transaction->id)->first();
                    if ( empty($trp) ) {
                        $currency_details             =  $transactionUtil->purchaseCurrencyDetails($business_id);
                        $type                         =  'purchase_receive';
                        $ref_count                    =  $productUtil->setAndGetReferenceCount($type,$business_id);
                        $reciept_no                   =  $productUtil->generateReferenceNumber($type, $ref_count,$business_id);
                        $tr_recieved                  =  new \App\Models\TransactionRecieved;
                        $tr_recieved->store_id        =  $transaction->store;
                        $tr_recieved->transaction_id  =  $transaction->id;
                        $tr_recieved->business_id     =  $transaction->business_id ;
                        $tr_recieved->reciept_no      =  $reciept_no ;
                        // $tr_recieved->invoice_no      =  $data->ref_no;
                        $tr_recieved->ref_no          =  $transaction->ref_no;
                        $tr_recieved->status          = 'purchase';
                        $tr_recieved->save();
                        $id_tr                        = $tr_recieved->id;
                    } else{
                        $id_tr                        = $trp->id;
                    }

                    $prev =  \App\Models\RecievedPrevious::where('transaction_id',$transaction->id)
                                ->where('product_id',$purchase_line->product_id)->where("line_id",$purchase_line->id)->first();
                    if (empty($prev)) {
                        $prev                              =  new \App\Models\RecievedPrevious;
                        $prev->product_id                  =  $purchase_line->product_id;
                        $prev->store_id                    =  $transaction->store;
                        $prev->business_id                 =  $transaction->business_id ;
                        $prev->transaction_id              =  $transaction->id;
                        $prev->unit_id                     =  $purchase_line->product->unit_id;
                        $prev->total_qty                   =  $purchase_line->quantity;
                        $prev->current_qty                 =  $purchase_line->quantity;
                        $prev->remain_qty                  =  0;
                        $prev->transaction_deliveries_id   =  $id_tr;
                        $prev->product_name                =  $purchase_line->product->name;  
                        $prev->line_id                     =  $purchase_line->id;  
                        $prev->save();
                    }else{
                        $prev->total_qty                  +=  $purchase_line->quantity;
                        $prev->current_qty                +=  $purchase_line->quantity;
                        $prev->remain_qty                  =  0;
                        $prev->transaction_deliveries_id   =  $id_tr;
                        $prev->store_id                    =  $transaction->store;
                        $prev->product_name                =  $purchase_line->product->name;  
                        $prev->save();
                    }
                    
                    //update info 
                    \App\Models\WarehouseInfo::update_stoct($purchase_line->product_id,$transaction->store,$purchase_line->quantity,$transaction->business_id);
                    // movement
                    \App\MovementWarehouse::movemnet_warehouse($transaction,$purchase_line->product,$purchase_line->quantity,
                                  $transaction->store,$purchase_line,'plus',$prev->id);


                }elseif ( $transaction->status != 'received'  && $old_status == 'received' ) {
                    $currency_details = $transactionUtil->purchaseCurrencyDetails($business_id);
                    \App\Models\WarehouseInfo::update_stoct($purchase_line->product_id,$transaction->store,($purchase_line->quantity*-1),
                            $transaction->business_id);
                    \App\MovementWarehouse::where('transaction_id',$transaction->id)->delete();
                    \App\Models\RecievedPrevious::where('transaction_id',$transaction->id)->delete();
                }elseif ( $transaction->status == 'received'  && $old_status == 'received' ){
                    $id_tr = 0; 
                    $trp   = \App\Models\TransactionRecieved::where("transaction_id",$transaction->id)->first();
                    if ( empty($trp) ) {
                        $currency_details             =  $transactionUtil->purchaseCurrencyDetails($business_id);
                        $type                         =  'purchase_receive';
                        $ref_count                    =  $productUtil->setAndGetReferenceCount($type,$business_id);
                        $reciept_no                   =  $productUtil->generateReferenceNumber($type, $ref_count,$business_id);
                        $tr_recieved                  =  new \App\Models\TransactionRecieved;
                        $tr_recieved->store_id        =  $transaction->store;
                        $tr_recieved->transaction_id  =  $transaction->id;
                        $tr_recieved->business_id     =  $transaction->business_id ;
                        $tr_recieved->reciept_no      =  $reciept_no ;
                        $tr_recieved->ref_no          =  $transaction->ref_no;
                        $tr_recieved->status          = 'purchase';
                        $tr_recieved->save();
                        $id_tr                        = $tr_recieved->id;
                    } else{
                        $id_tr                        = $trp->id;
                    }
                    $prev =  \App\Models\RecievedPrevious::where('transaction_id',$transaction->id)
                                                            ->where('product_id',$purchase_line->product_id)
                                                            ->where("line_id",$purchase_line->id)
                                                            ->first();
                    if (empty($prev)) {
                        $prev                              =  new \App\Models\RecievedPrevious;
                        $prev->product_id                  =  $purchase_line->product_id;
                        $prev->store_id                    =  $transaction->store;
                        $prev->business_id                 =  $transaction->business_id ;
                        $prev->transaction_id              =  $transaction->id;
                        $prev->unit_id                     =  $purchase_line->product->unit_id;
                        $prev->total_qty                   =  $purchase_line->quantity;
                        $prev->current_qty                 =  $purchase_line->quantity;
                        $prev->remain_qty                  =  0;
                        $prev->transaction_deliveries_id   =  $id_tr;
                        $prev->product_name                =  $purchase_line->product->name;  
                        $prev->save();
                        //update info 
                        \App\Models\WarehouseInfo::update_stoct($purchase_line->product_id,$transaction->store,$purchase_line->quantity,$transaction->business_id);
                        // movement
                        \App\MovementWarehouse::movemnet_warehouse($transaction,$purchase_line->product,$purchase_line->quantity,
                                      $transaction->store,$purchase_line,'plus',$prev->id);
                    }else {
                        $margin     = $purchase_line->quantity - $prev->current_qty;
                        $move_store = \App\MovementWarehouse::where("transaction_id",$transaction->id)
                                                            ->where("purchase_line_id",$purchase_line->id)
                                                            ->where("product_id",$purchase_line->product->id)
                                                            ->first();
                       
                        if($margin != 0){
                           // *** CHECK CHANGE STORE ?!!
                            if($prev->store_id !=  $transaction->store){
                                \App\Models\WarehouseInfo::update_stoct($purchase_line->product_id,$prev->store_id,$prev->current_qty*-1,$transaction->business_id);
                                \App\Models\WarehouseInfo::update_stoct($purchase_line->product_id,$transaction->store,$purchase_line->quantity,$transaction->business_id);
                            }else{
                               \App\Models\WarehouseInfo::update_stoct($purchase_line->product_id,$transaction->store,$margin,$transaction->business_id);
                                
                            } 
                            $prev->total_qty                  +=  $margin;
                            $prev->current_qty                +=  $margin;
                            $prev->remain_qty                  =  0;
                            $prev->transaction_deliveries_id   =  $id_tr;
                            $prev->store_id                    =  $transaction->store;
                            $prev->product_name                =  $purchase_line->product->name;  
                            $prev->update();
                            // movement
                             
                            if(!empty($move_store)){
                                $before_qty = 0;
                                $before     = \App\MovementWarehouse::orderBy("id","desc")
                                                                    ->where("transaction_id",$transaction->id)
                                                                    ->where("product_id",$purchase_line->product->id)
                                                                    ->where("store_id",$transaction->store)
                                                                    ->where("id","<",$move_store->id)
                                                                    ->first();
                                if(!empty($before)){
                                    $before_qty          =  $before->current_qty ;
                                } 
                                $current_qty             =  $before_qty + $purchase_line->quantity ;
                                $move_store->store_id    =  $transaction->store;
                                $move_store->plus_qty    =  $purchase_line->quantity;
                                $move_store->current_qty =  $current_qty;
                                $move_store->update();
                                
                            }
                        }else{
                           
                            // *** CHECK CHANGE STORE ?!!
                            if($prev->store_id !=  $transaction->store){
                                \App\Models\WarehouseInfo::update_stoct($purchase_line->product_id,$prev->store_id,$purchase_line->quantity*-1,$transaction->business_id);
                                \App\Models\WarehouseInfo::update_stoct($purchase_line->product_id,$transaction->store,$purchase_line->quantity,$transaction->business_id);
                            } 
                            $prev->store_id        =  $transaction->store;
                            $prev->update();
                            if(!empty($move_store)){
                                $move_store->plus_qty  =  $purchase_line->quantity;
                                $move_store->store_id  =  $transaction->store;
                                $move_store->update();
                            }
                        }
                    }
                }
                
            }
            // 12...
            $total_ship       = \App\Models\Purchase::supplier_shipp($transaction->id);
            // .........................................................................................................................................
            if (($old_status !=  'final' && $old_status != 'received' ) && ($transaction->status == 'final' ||  $transaction->status == 'received' ) ) {
                ///.... add purchase action 
                \App\AccountTransaction::add_purchase($transaction,$total_ship);
                //.... for map purchase 
                $cost=0;$without_contact=0;
                if($request->shipping_amount != null){
                    $data_ship = \App\Models\AdditionalShipping::where("transaction_id",$transaction->id)->first();
                    $ids = $data_ship->items->pluck("id");
                    foreach($ids as $i){
                        $data_shippment   = \App\Models\AdditionalShippingItem::find($i);
                        if($data_shippment->contact_id == $request->contact_id){ 
                            $cost += $data_shippment->amount;
                        }else{
                            $without_contact += $data_shippment->amount;
                        }
                        \App\Models\StatusLive::insert_data_sh($business_id,$transaction,$data_shippment,"Add Expense");
                    }
                }

            }else if (($old_status ==  'final' || $old_status == 'received' ) && ($transaction->status != 'final' && $transaction->status != 'received' ) ){
                 /// ...... Delete  All Actions
                \App\AccountTransaction::where('transaction_id',$transaction->id)->delete();
                 /// ...... Delete  All Entries
                \App\Models\Entry::delete_entries($transaction->id);
            } 
           
            // ........................................................................................................................................
            if ($transaction->status ==  'received' || $transaction->status ==  'final' || ($transaction->status == null && $request->old_sts == "final")) {
                //.. 2  ..........  update expenses 
                \App\Models\AdditionalShipping::add_purchase_payment($transaction->id,null,null,null,$user);
               
                if (!(($old_status !=  'final' && $old_status != 'received' ) && ($transaction->status == 'final' ||  $transaction->status == 'received' )) ) {
                    //.. 3 ..........  update expenses 
                    \App\AccountTransaction::update_purchase($transaction,$total_ship,$old_trans,$old_account,$old_discount,$old_tax,$user);
                }
                \App\Models\StatusLive::update_data_p($business_id,$transaction,$request->status);

                $cost=0;$without_contact=0;
                 if($request->old_shipping_amount != null){
                    $data_ship = \App\Models\AdditionalShipping::where("transaction_id",$transaction->id)->first();
                    $ids = $data_ship->items->pluck("id");
                    foreach($ids as $i){
                        $data_shippment   = \App\Models\AdditionalShippingItem::find($i);
                        if($data_shippment->contact_id == $request->contact_id){ 
                            $cost += $data_shippment->amount;
                        }else{
                            $without_contact += $data_shippment->amount;
                        }
                        \App\Models\StatusLive::insert_data_sh($business_id,$transaction,$data_shippment,"Add Expense");
                    }
                }
            }else{
                \App\AccountTransaction::where('transaction_id',$transaction->id)
                        ->whereNotNull('additional_shipping_item_id')->delete();
                \App\Models\Entry::where("account_transaction",$transaction->id)->delete();
                $shipping_id = \App\Models\AdditionalShipping::where("transaction_id",$transaction->id)->first();
                if(!empty($shipping_id)){
                    \App\Models\Entry::where("shipping_id",$shipping_id->id)->delete();
                }
                \App\Models\StatusLive::where("transaction_id",$transaction->id)->whereNotNull("shipping_item_id")->delete();
                \App\Models\StatusLive::where("transaction_id",$transaction->id)->where("num_serial","!=",1)->delete();
                \App\Models\StatusLive::where("transaction_id",$transaction->id)->update(["state"=>"Purchase ".$transaction->status ]);
            }
            
            // ** FOR DO THIS CONDITION EVERY TIME UPDATE PURCHASE WITH STATUS "received" FOR UPDATE ITEM MOVEMENT 
            // *** AGT8422 
                if($transaction->status ==  'received' ){
                    $total_expense = $cost + $without_contact; 
                    if ($request->discount_type == "fixed_before_vat"){
                        $dis = $request->discount_amount;
                    }else if ($request->discount_type == "fixed_after_vat"){
                        $tax = \App\TaxRate::find($request->tax_id);
                        $dis = ($request->discount_amount*100)/(100+$tax->amount) ;
                    }else if ($request->discount_type == "percentage"){
                        $dis = ($request->total_before_tax *  $request->discount_amount)/100;
                    }else{
                        $dis = 0;
                    }
                    $before = \App\Models\WarehouseInfo::qty_before($transaction);
                    \App\Models\ItemMove::update_itemMove($transaction,$total_expense,$before,null,$dis);    
                }
            // ************
            
            // *1* FOR IF CHANGE SUPPLIER 
            // *** AGT8422
                if(app("request")->input("dialog")){
                    if(app("request")->input("check")){
                        if(app("request")->input("check") == 1){ // *** without payment
                            $add_ship  = \App\Models\AdditionalShipping::where("transaction_id",$id)->get();
                            foreach($add_ship as $is){
                                $items = \App\Models\AdditionalShippingItem::where("additional_shipping_id",$is->id)->get();
                                foreach($items  as $i){
                                    if($i->contact_id == $sup_id ){
                                        $acct = \App\Account::where("contact_id",$sup_id)->first();
                                        $account_transaction = \App\AccountTransaction::where("account_id",($acct)?$acct->id:null)->where("additional_shipping_item_id",$i->id)->get();
                                        foreach($account_transaction as $ie){
                                            $account        = \App\Account::where("contact_id",$sup)->first();
                                            $ie->account_id = ($account)?$account->id:null; 
                                            $ie->update();
                                        }
                                        $i->contact_id = $sup;   
                                        $i->update();
                                    }
                                }
                            }
                            $payment       = \App\TransactionPayment::where("transaction_id",$id)->get();
                            $array_voucher = [];
                            $array_check   = [];
                            foreach($payment as $it){
                                if($it->payment_voucher_id != null ){
                                    $array_voucher[]  = $it->payment_voucher_id;
                                }
                                if($it->check_id != null ){
                                    $array_check[]  = $it->check_id;
                                }
                                $it->payment_for = $sup;
                                $it->update();
                            }
                            foreach($array_voucher as $it){
                                $voucher = \App\Models\PaymentVoucher::where("id",$it)->first();
                                if($voucher){
                                    $acct = \App\Account::where("contact_id",$sup_id)->first();
                                    $voucher->contact_id = $sup; 
                                    $account_transaction = \App\AccountTransaction::where("account_id",($acct)?$acct->id:null)->where("payment_voucher_id",$it)->get();
                                
                                    foreach($account_transaction as $ie){
                                        $account = \App\Account::where("contact_id",$sup)->first();
                                        $ie->account_id = ($account)?$account->id:null; 
                                        $ie->update();
                                    }
                                    $voucher->update();
                                }
                            }
                            foreach($array_check as $i){
                                $check   = \App\Models\Check::where("id",$i)->first();
                                if($check){
                                    $acct = \App\Account::where("contact_id",$sup_id)->first();
                                    $new_account = \App\Account::where("contact_id",$sup)->first();
                                    $check->contact_id = ($new_account)?$new_account->id:null; 
                                    $account_transaction = \App\AccountTransaction::where("account_id",($acct)?$acct->id:null)->where("check_id",$i)->get();
                                    foreach($account_transaction as $ie){
                                        $account = \App\Account::where("contact_id",$sup)->first();
                                        $ie->account_id = ($account)?$account->id:null; 
                                        $ie->update();
                                    }
                                    $check->update();
                                }
                            }
                        
                        } else {  // *** with payment
                                
                            $add_ship  = \App\Models\AdditionalShipping::where("transaction_id",$id)->get();
                            foreach($add_ship as $is){
                                $items = \App\Models\AdditionalShippingItem::where("additional_shipping_id",$is->id)->get();
                                foreach($items  as $i){
                                    if($i->contact_id == $sup_id ){
                                        $acct = \App\Account::where("contact_id",$sup_id)->first();
                                        $account_transaction = \App\AccountTransaction::where("account_id",($acct)?$acct->id:null)->where("additional_shipping_item_id",$i->id)->get();
                                        foreach($account_transaction as $ie){
                                            $account = \App\Account::where("contact_id",$sup)->first();
                                            $ie->account_id = ($account)?$account->id:null; 
                                            $ie->update();
                                        }
                                        $i->contact_id = $sup;   
                                        $i->update();
                                    }
                                }
                            }
                            $payment   = \App\TransactionPayment::where("transaction_id",$id)->get();
                            $array_voucher = [];
                            $array_check   = [];
                            foreach($payment as $it){
                                if($it->payment_voucher_id != null ){
                                    $array_voucher[]  = $it->payment_voucher_id;
                                }
                                if($it->check_id != null ){
                                    $array_check[]  = $it->check_id;
                                }
                                $it->payment_for = $sup;
                                $it->update();
                            }
                            foreach($array_voucher as $it){
                                $voucher = \App\Models\PaymentVoucher::where("id",$it)->first();
                                if($voucher){
                                    $acct = \App\Account::where("contact_id",$sup_id)->first();
                                    $voucher->contact_id = $sup; 
                                    $account_transaction = \App\AccountTransaction::where("account_id",($acct)?$acct->id:null)->where("payment_voucher_id",$it)->get();
                                
                                    foreach($account_transaction as $ie){
                                        $account = \App\Account::where("contact_id",$sup)->first();
                                        $ie->account_id = ($account)?$account->id:null; 
                                        $ie->update();
                                    }
                                    $voucher->update();
                                }
                            }
                            foreach($array_check as $i){
                                $check   = \App\Models\Check::where("id",$i)->first();
                                if($check){
                                    $acct = \App\Account::where("contact_id",$sup_id)->first();
                                    $new_account = \App\Account::where("contact_id",$sup)->first();
                                    $check->contact_id = ($new_account)?$new_account->id:null; 
                                    $account_transaction = \App\AccountTransaction::where("account_id",($acct)?$acct->id:null)->where("check_id",$i)->get();
                                    foreach($account_transaction as $ie){
                                        $account = \App\Account::where("contact_id",$sup)->first();
                                        $ie->account_id = ($account)?$account->id:null; 
                                        $ie->update();
                                    }
                                    $check->update();
                                }
                            }
                        }
                    } else {
                            
                        $add_ship  = \App\Models\AdditionalShipping::where("transaction_id",$id)->get();
                        foreach($add_ship as $is){
                            $items = \App\Models\AdditionalShippingItem::where("additional_shipping_id",$is->id)->get();
                            foreach($items  as $i){
                                if($i->contact_id == $sup_id ){
                                    $acct                = \App\Account::where("contact_id",$sup_id)->first();
                                    $account_transaction = \App\AccountTransaction::where("account_id",($acct)?$acct->id:null)->where("additional_shipping_item_id",$i->id)->get();
                                    foreach($account_transaction as $ie){
                                        $account         = \App\Account::where("contact_id",$sup)->first();
                                        $ie->account_id  = ($account)?$account->id:null; 
                                        $ie->update();
                                    }
                                    $i->contact_id = $sup;   
                                    $i->update();
                                }
                            }
                        }
                        $payment       = \App\TransactionPayment::where("transaction_id",$id)->get();
                        $array_voucher = [];
                        $array_check   = [];
                        // ............. payment
                        foreach($payment as $it){
                            if($it->payment_voucher_id != null ){
                                $array_voucher[]  = $it->payment_voucher_id;
                            }
                            if($it->check_id != null ){
                                $array_check[]  = $it->check_id;
                            }
                        }
                        // ............. voucher
                        foreach($array_voucher as $it){
                            $voucher = \App\Models\PaymentVoucher::where("id",$it)->first();
                            if($voucher){
                                $account_transaction = \App\AccountTransaction::where("payment_voucher_id",$it)->get();
                                foreach($account_transaction as $ie){
                                    $ie->transaction_id =  null; 
                                    $ie->update();
                                }
                            }
                        }
                        // ............. check
                        foreach($array_check as $i){
                            $check   = \App\Models\Check::where("id",$i)->first();
                            if($check){
                                $account_transaction = \App\AccountTransaction::where("check_id",$i)->get();
                                foreach($account_transaction as $ie){
                                    $ie->transaction_id = null; 
                                    $ie->update();
                                }
                            }
                        }
                        foreach($payment as $it){
                            $it->delete();
                        }
                        $transaction = \App\Transaction::find($id);
                        $total_paid  = \App\TransactionPayment::where('transaction_id', $id)
                                                                    ->select(DB::raw('SUM(IF( is_return = 0, amount, amount*-1))as total_paid'))
                                                                    ->first()
                                                                    ->total_paid;
                        $final_amount =  $transaction->final_total;
                        if (is_null($final_amount)) {
                            $final_amount = \App\Transaction::find($id)->final_total;
                        }   
                        $status = 'due';
                        if ($final_amount <= $total_paid) {
                            $status = 'paid';
                        } elseif ($total_paid > 0 && $final_amount > $total_paid) {
                            $status = 'partial';
                        }
                        \App\Transaction::where('id',$id)->update([
                                'payment_status'=>$status
                        ]);


                    }
                }
            // ***********
           
            
            return true; 
        }catch(Exception $e){
            return false;
        }
    }
    // **3** GET  PURCHASE
    public static function allData($type=null,$id=null,$business_id,$request=null,$main = null,$view=null) {
        try{
            $list      = [];
            $allData   = [];
            if($type != null){
                // ....................START..
                $page         = $request->query('page',1);
                $skip         = $request->query('skip',0);
                $limit        = $request->query('limit',25);
                $skpP         = ($page-1)*$limit;
                $count        = \App\Transaction::where("type","purchase")->count();
                $totalPages   = ceil($count / $limit);
                $prevPage     = $page > 1 ? $page - 1 : null;
                $nextPage     = $page < $totalPages ? $page + 1 : null;
                // ....................END....
                $purchases           = \App\Transaction::where("business_id",$business_id)->where("type","purchase")->skip($skpP)->limit($limit)->orderBy("id","desc")->get();
                if(count($purchases) == 0 ){ return false; }
                foreach($purchases as $ie){
                    $product_list   = []; 
                    $lines          = [];
                    $lines_payments = [];
                    $items          = \App\PurchaseLine::where("transaction_id",$ie->id)->get();
                    $payments       = \App\TransactionPayment::where("transaction_id",$ie->id)->get();
                    foreach($items as $ii){
                        $product_list[]              = $ii->product_id;
                        $lines[] = [
                            "id"                     => $ii->id,
                            "store_id"               => ($ii->warehouse!=null)?$ii->warehouse->name:"",  
                            "product_id"             => ($ii->product!=null)?$ii->product->name:"", 
                            "transaction_id"         => ($ii->transaction!=null)?$ii->transaction->ref_no:"", 
                            "variation_id"           => $ii->variation_id,
                            "quantity"               => $ii->quantity,
                            "pp_without_discount"    => round($ii->pp_without_discount,2),
                            "discount_percent"       => round($ii->discount_percent,2),
                            "purchase_price"         => round($ii->purchase_price,2),
                            "purchase_price_inc_tax" => round($ii->purchase_price_inc_tax,2),
                            "item_tax"               => round($ii->item_tax,2),
                            "tax_id"                 => ($ii->line_tax!=null)?$ii->line_tax->name:"",
                            "quantity_returned"      => round($ii->quantity_returned,2),
                            "purchase_note"          => $ii->purchase_note,
                            "order_id"               => $ii->order_id,
                            "mfg_date"               => $ii->mfg_date,
                            "exp_date"               => $ii->exp_date,
                        ];
                    }
                    $paid_amount = 0;
                    foreach($payments as $iei){
                        $user_id                      = \App\Models\User::find($iei->created_by);
                        $paid_amount                  += round($iei->amount,2);
                        $lines_payments[] = [
                            "id"                      => $iei->id,
                            "transaction_id"          => ($iei->transaction!=null)?$iei->transaction->ref_no:"", 
                            "amount"                  => round($iei->amount,2),
                            "method"                  => $iei->method ,
                            "card_transaction_number" => $iei->card_transaction_number ,
                            "card_number"             => $iei->card_number ,
                            "card_type"               => $iei->card_type ,
                            "card_holder_name"        => $iei->card_holder_name ,
                            "card_month"              => $iei->card_month ,
                            "card_year"               => $iei->card_year ,
                            "card_security"           => $iei->card_security ,
                            "cheque_number"           => $iei->cheque_number ,
                            "bank_account_number"     => $iei->bank_account_number ,
                            "payment_ref_no"          => $iei->payment_ref_no ,
                            "note"                    => $iei->note ,
                            "account_id"              => ($iei->account)?$iei->account->name:"" ,
                            "payment_voucher_id"      => ($iei->voucher)?$iei->voucher->ref_no:"" ,
                            "check_id"                => ($iei->check)?$iei->check->ref_no:""    ,
                            "created_by"              => (!empty($user_id))?$user_id->first_name:"" ,
                            "paid_on"                 => $iei->paid_on ,
                        ];
                    }
                    $PurchaseLine     = \App\PurchaseLine::where("transaction_id",$ie->id)->whereIn("product_id",$product_list)->select(\DB::raw("SUM(quantity) as total"))->first()->total;
                    $RecievedPrevious = \App\Models\RecievedPrevious::where("transaction_id",$ie->id)->whereIn("product_id",$product_list)->select(\DB::raw("SUM(current_qty) as total"))->first()->total;
                    $wrong            = \App\Models\RecievedWrong::where("transaction_id",$ie->id)->select(\DB::raw("SUM(current_qty) as total"))->first()->total;
                    $rec_status = "Not Recieved";
                    if($RecievedPrevious == null){
                        $rec_status = "Not Recieved";
                    }else if($PurchaseLine == $RecievedPrevious){
                        $rec_status = "Recieved";
                    }else if( $RecievedPrevious < $PurchaseLine && $RecievedPrevious != 0){
                        $rec_status = "separate";
                    }else if( $RecievedPrevious > $PurchaseLine && $RecievedPrevious != 0 ){
                        $rec_status = "wrong";
                    }
                     
                    $tr             = \App\Transaction::find($ie->id);
                    $cost_shipping  = \App\Models\AdditionalShippingItem::whereHas("additional_shipping",function($query) use($ie,$tr) {
                                                                                $query->where("type",1);
                                                                                $query->where("transaction_id",$ie->id);
                                                                                $query->where("contact_id",$tr->contact_id);
                                                                            })->sum("total");
                    
                    $due                     = $ie->final_total + $cost_shipping;
                    $user                    = \App\Models\User::find($ie->created_by);
                    $agent                   = \App\Models\User::find($ie->agent_id);
                    $account                 = \App\Account::where("contact_id",$tr->contact_id)->first();
                    $debit                   = \App\AccountTransaction::where("account_id",$account->id)->where("for_repeat",null)->where("type","debit")->sum("amount");
                    $credit                  = \App\AccountTransaction::where("account_id",$account->id)->where("for_repeat",null)->where("type","credit")->sum("amount");
                    $total                   = $debit - $credit;
                    $total                   = ($total==0)?0:(($total<0)?(($total)*-1 . " / Credit"):(($total) . " / Debit"));
                    $contact_balance         = $total;
                    $list[] = [
                        "id"                 => $ie->id,
                        "store"              => ($ie->warehouse != null)?($ie->warehouse->name):"",
                        "location"           => ($ie->location != null)?$ie->location->name:"",
                        "type"               => $ie->type,
                        "status"             => $ie->status,
                        "payment_status"     => $ie->payment_status,
                        "contact"            => ($ie->contact != null)?$ie->contact->name:"",
                        "contact_balance"    => $contact_balance,
                        "reference_no"       => $ie->ref_no,
                        "project_no"         => $ie->project_no,
                        "date"               => $ie->transaction_date,
                        "tax"                => ($ie->tax != null)?$ie->tax->name:"",
                        "tax_amount"         => round($ie->tax_amount,2),
                        "discount_type"      => $ie->discount_type,
                        "discount_amount"    => round($ie->discount_amount,2),
                        "sub_total"          => round($ie->total_before_tax,2),
                        "final_total"        => round($ie->final_total,2),
                        "additional_notes"   => $ie->additional_notes,
                        "created_by"         => (!empty($user))?$user->first_name:"",
                        "source_reference"   => $ie->sup_refe,
                        // "agent_id"           => (!empty($agent))?$agent->first_name:"",
                        // "pattern"            => $ie->pattern_id,
                        "attachment"         => $ie->document ,                        
                        "recieved_status"    => $rec_status,
                        "currency_id"        => $ie->currency_id,
                        "amount_in_currency" => $ie->amount_in_currency,
                        "exchange_price"     => $ie->exchange_price,
                        "list"               => $lines,
                        "payment_due"        => $due-$paid_amount,
                        "payments"           => $lines_payments,
                    ];
                }
                // ................................................. 
                $allData["items"] = $list;
                $allData["info"]  = [
                    "totalRows"     => $count,
                    'current_page'  => $page,
                    'last_page'     => $totalPages,
                    'limit'         => 25,
                    'prev_page_url' => $prevPage ? "/api/app/react/purchase/all?page=$prevPage" : null,
                    'next_page_url' => $nextPage ? "/api/app/react/purchase/all?page=$nextPage" : null,
                ];
                 
            }else{
                $purchase              = \App\Transaction::find($id);
                $business              = \App\Business::find($purchase->business_id);
                if(empty($purchase)){ return false; }
                $product_list          = [];
                $lines                 = [];
                $lines_payments        = [];
                $items                 = \App\PurchaseLine::where("transaction_id",$id)->get();
                $payments              = \App\TransactionPayment::where("transaction_id",$id)->get();
                
                foreach($items as $ii){
                    $PRO             = \App\Product::find($ii->product_id);
                    $STR             = \App\Models\Warehouse::find($ii->store_id);
                    $list_price      = \App\Product::getProductPrices($ii->product_id);
                    $unit2           = \App\Unit::find($PRO->unit_id);
                    $list_unit[]     = ["id" => $unit2->id ,"value" => $unit2->actual_name ,"unit_quantity"=>($unit2->base_unit_multiplier == null)?1:$unit2->base_unit_multiplier,"list_price"=> isset($list_price[$unit2->id])?$list_price[$unit2->id]:[]];
                    $un []           = $unit2->id;
                    $all             = $PRO->sub_unit_ids ;
                    if($all != null){
                        foreach($all as $i){
                            $unit            = \App\Unit::find($i);
                            if(!in_array($i,$un)){
                                $list_unit[]   = ["id" => $i ,"value" => $unit->actual_name ,"unit_quantity"=>($unit->base_unit_multiplier == null)?1:$unit->base_unit_multiplier,"list_price"=> isset($list_price[$i])?$list_price[$i]:[]];
                                $un [] =  $i;
                            }
                        }
                    }
                    $product_list[]                   =  $ii->product_id;
                    $lines[] = [
                        "id"                          => $ii->id,
                        
                        "store_id"                    => ($ii->warehouse!=null)?$ii->warehouse->id:"",  
                        "storeName"                   => ($ii->warehouse!=null)?$ii->warehouse->name:""  ,  

                        "variation_id"                => $ii->variation_id,
                        "product_id"                  => ($view == "edit")?$ii->product->id:$ii->product, 
                        "name"                        => ($view == "edit")?$ii->product->name:"", 

                        "transaction_id"              => ($ii->transaction!=null)?$ii->transaction->id:"", 
                        "reference"                   => ($ii->transaction!=null)?$ii->transaction->ref_no:"", 
                        
                        "quantity"                    => $ii->quantity,
                        "list_price"                  => $ii->list_price,
                        "all_unit"                    => $list_unit,
                        

                        "pp_without_discount"         => round($ii->pp_without_discount,2),
                        "pp_without_discount_tax"     => round($ii->pp_without_discount+($ii->pp_without_discount*$ii->item_tax/100),2),
                        "pp_without_discount_curr"    => round(($purchase->currency_id!=null)?($ii->pp_without_discount/$purchase->exchange_price):0,2),
                        
                        "discount_percent"            => round($ii->discount_percent,2),
                        "discount_percentage"         => round(($purchase->currency_id!=null)?(($ii->discount_percent/($ii->pp_without_discount/$purchase->exchange_price))*100):(($ii->discount_percent/($ii->pp_without_discount))*100),2),
                        
                        "purchase_price"              => round($ii->purchase_price,2),
                        "purchase_price_inc_tax"      => round($ii->purchase_price_inc_tax,2),
                        "purchase_price_curr"         => round(($purchase->currency_id!=null)?($ii->purchase_price/$purchase->exchange_price):0,2),

                        "tax"                         => $purchase->tax,
                        "tax_id"                      => ($ii->line_tax!=null)?$ii->line_tax->id:"",
                        "taxName"                     => ($ii->line_tax!=null)?($ii->line_tax->name):"",
                        "item_tax"                    => round($ii->item_tax,2),

                        "quantity_returned"           => round($ii->quantity_returned,2),
                        
                        "purchase_note"               => $ii->purchase_note,

                        "order_id"                    => $ii->order_id,
                        
                        "line_sort"                   => $ii->order_id,

                        "purchase_total"              => round($ii->purchase_price_inc_tax*$ii->quantity,2),
                        "purchase_total_curr"         => round((($purchase->currency_id!=null)?(($ii->purchase_price_inc_tax/$purchase->exchange_price)*$ii->quantity):0),2),

                        "mfg_date"                    => $ii->mfg_date,
                        "exp_date"                    => $ii->exp_date,
                        
                    ];
                }
                
                $paid_amount = 0;
                foreach($payments as $iei){
                    $user_id                      = \App\Models\User::find($iei->created_by);
                    $paid_amount                  += round($iei->amount,2);
                    $lines_payments[] = [
                        "id"                         => $iei->id,
                        "transaction_id"             => ($iei->transaction!=null)?$iei->transaction->id:"", 
                        "reference"                  => ($iei->transaction!=null)?$iei->transaction->ref_no:"", 
                        "amount"                     => round($iei->amount,2),
                        "method"                     => $iei->method ,
                        "card_transaction_number"    => $iei->card_transaction_number ,
                        "card_number"                => $iei->card_number ,
                        "card_type"                  => $iei->card_type ,
                        "card_holder_name"           => $iei->card_holder_name ,
                        "card_month"                 => $iei->card_month ,
                        "card_year"                  => $iei->card_year ,
                        "card_security"              => $iei->card_security ,
                        "cheque_number"              => $iei->cheque_number ,
                        "bank_account_number"        => $iei->bank_account_number ,
                        "note"                       => $iei->note ,
                        "payment_ref_no"             => $iei->payment_ref_no ,
                        "account_id"                 => ($iei->account)?$iei->account->id:"" ,
                        "accountName"                => ($iei->account)?$iei->account->name:"" ,
                        "payment_voucher_id"         => ($iei->voucher)?$iei->voucher->id:"" ,
                        "payment_voucher_reference"  => ($iei->voucher)?$iei->voucher->ref_no:"" ,
                        "check_id"                   => ($iei->check)?$iei->check->id:""    ,
                        "check_reference"            => ($iei->check)?$iei->check->ref_no:""    ,
                        "created_by"                 => (!empty($user_id))?$user_id->id:"" ,
                        "created_by_name"            => (!empty($user_id))?$user_id->first_name:"" ,
                        "paid_on"                    => $iei->paid_on ,
                    ];
                }

                $PurchaseLine     = \App\PurchaseLine::where("transaction_id",$purchase->id)->whereIn("product_id",$product_list)->select(\DB::raw("SUM(quantity) as total"))->first()->total;
                $RecievedPrevious = \App\Models\RecievedPrevious::where("transaction_id",$purchase->id)->whereIn("product_id",$product_list)->select(\DB::raw("SUM(current_qty) as total"))->first()->total;
                $wrong            = \App\Models\RecievedWrong::where("transaction_id",$purchase->id)->select(\DB::raw("SUM(current_qty) as total"))->first()->total;
                
                $rec_status = "Not Recieved";
                if($RecievedPrevious == null){
                    $rec_status = "Not Recieved";
                }else if($PurchaseLine == $RecievedPrevious){
                    $rec_status = "Recieved";
                }else if( $RecievedPrevious < $PurchaseLine && $RecievedPrevious != 0){
                    $rec_status = "separate";
                }else if( $RecievedPrevious > $PurchaseLine && $RecievedPrevious != 0 ){
                    $rec_status = "wrong";
                }
                    
                $tr             = \App\Transaction::find($purchase->id);
                $cost_shipping  = \App\Models\AdditionalShippingItem::whereHas("additional_shipping",function($query) use($purchase,$tr) {
                                                                            $query->where("type",1);
                                                                            $query->where("transaction_id",$purchase->id);
                                                                            $query->where("contact_id",$tr->contact_id);
                                                                        })->sum("total");
                    
                $due                     = $purchase->final_total + $cost_shipping;
                $user                    = \App\Models\User::find($purchase->created_by);
                $agent                   = \App\Models\User::find($purchase->agent_id);
                $activities              = Activity::forSubject($purchase)->with(['causer', 'subject'])->latest()->get();
                $due_date                = "";
                
                if($purchase->pay_term_number != 0 && $purchase->pay_term_number != null){
                    $date_purchase = \Carbon::parse($purchase->transaction_date);
                    if($purchase->pay_term_type == "months"){
                        $daysToAdd               = $purchase->pay_term_number*30;
                    }elseif($purchase->pay_term_type == "days"){
                        $daysToAdd               = $purchase->pay_term_number;
                    } 
                    $secondDate              = $date_purchase->addDays($daysToAdd);
                    $due_date                = $secondDate->toDateString();
                }

                $list_shippings              = [];
                $list_of_prices              = \App\Product::getListPrices(1);
                $listed["id"]                = $purchase->contact->id;
                $supplier                    = self::selectSupplier($user,$listed);
                $dis = 0;
                $main_tax_amount     = ($purchase->tax!=null)?$purchase->tax->amount:0;
                if($purchase->discount_type == "fixed_before_vat"){
                        $dis = ($purchase->currency)?(($purchase->discount_amount)*$purchase->exchange_price):($purchase->discount_amount);
                }else if($purchase->discount_type == "fixed_after_vat"){
                        $dis = ($purchase->currency)?((($purchase->discount_amount)*$purchase->exchange_price)*100/(100+$main_tax_amount)):($purchase->discount_amount*100/(100+$main_tax_amount));
                }else if($purchase->discount_type == "percentage"){
                        $dis = ($purchase->currency)?(($purchase->total_before_tax/(($purchase->exchange_price)?$purchase->exchange_price:1))*$purchase->discount_amount/100):($purchase->total_before_tax*$purchase->discount_amount/100);
                }
                $discount                      = $dis;
                $attach                        = [] ;
                $attach_purchase               = $purchase->document ;
                if($attach_purchase){
                    foreach($attach_purchase as $at){
                        $attach[] = $at; 
                    }
                }
                $amount      = 0 ; $vat        = 0 ; $total      = 0;$shippings = [];
                $supplier_shipping = 0 ;             $cost_shipping   = 0 ; 
                $attach_expense                = $purchase->document ;
                if($purchase->additional_shipings){
                    foreach($purchase->additional_shipings as $oneAdditional){
                        if($oneAdditional->document){ foreach($oneAdditional->document as $at){ $attach[] = $at;  } }
                        $child_shipping  = \App\Models\AdditionalShippingItem::where("additional_shipping_id",$oneAdditional->id)->get();
                        foreach($child_shipping as $item_child){
                            $amount_curr      = ($item_child->currency_id)?($item_child->amount/(($item_child->exchange_rate!=0)?$item_child->exchange_rate:1)):0;
                            $vat_curr         = ($item_child->currency_id)?($item_child->vat/(($item_child->exchange_rate!=0)?$item_child->exchange_rate:1)):0;
                            $total_curr       = ($item_child->currency_id)?($item_child->total/(($item_child->exchange_rate!=0)?$item_child->exchange_rate:1)):0;
                            $list_shippings[] = [
                                "id"                 => $item_child->id,
                                "supplier_id"        => $item_child->contact_id,
                                "supplier_name"      => $item_child->contact->name,
                                "amount_line"        => FloatVal($item_child->amount),
                                "vat_line"           => FloatVal($item_child->vat),
                                "total_line"         => FloatVal($item_child->total),
                                "amount_line_curr"   => $amount_curr ,
                                "vat_line_curr"      => $vat_curr    ,
                                "total_line_curr"    => $total_curr  ,
                                "debit_account_id"   => ($item_child->account_id)?$item_child->account_id:"",
                                "debit_account_name" => ($item_child->account_id)?$item_child->account->name:"",
                                "note"               => $item_child->text,
                                "cost_center_id"     => ($item_child->cost_center_id)?$item_child->cost_center_id:"",
                                "cost_center_name"   => ($item_child->cost_center_id)?$item_child->cost_center->name:"",
                                "currency_id"        => ($item_child->currency_id)?$item_child->currency->id:"",
                                "currency_name"      => ($item_child->currency_id)?$item_child->currency->name:"",
                                "currency_amount"    => ($item_child->currency_id)?$item_child->exchange_rate:"",
                                "date"               => $item_child->date,
                            ];
                            $amount                 += $item_child->amount;
                            $vat                    += $item_child->vat;
                            $total                  += $item_child->total;
                            $supplier_shipping      += ($item_child->contact_id==$purchase->contact_id)?$item_child->total:0;
                            $cost_shipping          += ($item_child->contact_id!=$purchase->contact_id)?$item_child->total:0;
                            

                        }
                        $shippings[] = [
                            "id"                => $oneAdditional->id,
                            "lines"             => $list_shippings,
                            "amount"            => $amount,
                            "vat"               => $vat   ,
                            "total"             => $total ,

                            "supplier_shipping" => $supplier_shipping,
                            "cost_shipping"     => $cost_shipping   , 

                            "supplier_shipping_curr" => ($item_child->currency_id)?($supplier_shipping/(($item_child->exchange_rate!=0)?$item_child->exchange_rate:1)):0,
                            "cost_shipping_curr"     => ($item_child->currency_id)?($cost_shipping/(($item_child->exchange_rate!=0)?$item_child->exchange_rate:1)):0   , 
                        ];
                    }
                }

                $allData[] = [
                    "id"                 => $purchase->id,
                    // "location"           => ($purchase->location != null)?(($main!=null)?$purchase->location->id:($purchase->location->name)):"",
                    "location"           => ($view == "edit")?$purchase->location->id:$purchase->location, 
                    "type"               => $purchase->type,
                    
                    "reference_no"       => $purchase->ref_no,
                    "date"               => $purchase->transaction_date,
                    "due_date"           => $due_date,
                    
                    "store"              => ($purchase->warehouse != null)?$purchase->warehouse->id:"",
                    "storeName"          => ($purchase->warehouse != null)?$purchase->warehouse->name:"",
                    "list_price"         => $purchase->list_price,
                    "list_price_name"    => isset($list_of_prices[$purchase->list_price])?$list_of_prices[$purchase->list_price]:"",
                    "project_no"         => $purchase->project_no,
                    "source_reference"   => $purchase->sup_refe,
                    "cost_center_id"     => ($purchase->cost_center)?$purchase->cost_center->id:"",
                    "cost_center_name"   => ($purchase->cost_center)?$purchase->cost_center->name:"",
                    
                    "status"             => $purchase->status,
                    "payment_status"     => $purchase->payment_status,
                    "recieved_status"    => $rec_status,
                    
                    // "contact"            => ($purchase->contact != null)?(($main!=null)?$purchase->contact->id:($purchase->contact->name)):"",
                    "contact"            => ($view == "edit")?$purchase->contact->id:$purchase->contact, 
                    "contact_info"       => $supplier, 
                    
                    #...................................................
                    "tax"                => ($purchase->tax != null)?$purchase->tax->id:"",
                    "taxName"            => ($purchase->tax != null)?$purchase->tax->name:"",
                    "taxValue"           => ($purchase->tax != null)?$purchase->tax->amount:"",
                    "discount_type"      => $purchase->discount_type,
                    "discount_amount"    => round($purchase->discount_amount,2),
                    
                    #...................................................
                    "sub_total"          => round($purchase->total_before_tax,2),
                    "sub_total_curr"     => round(($purchase->currency_id!=null)?($purchase->total_before_tax/$purchase->exchange_price):0,2),
                    
                    #...................................................
                    
                    "discount_amount_view"         => round($discount,2),
                    "discount_amount_view_curr"    => round(($purchase->currency)?($discount/$purchase->exchange_price):0,2),
                    
                    #...................................................
                    "tax_amount"         => round($purchase->tax_amount,2),
                    "tax_amount_curr"    => round(($purchase->currency)?($purchase->tax_amount/$purchase->exchange_price):0,2),
                    
                    #...................................................
                    "final_total"        => round($purchase->final_total,2),
                    "final_total_curr"   => round(($purchase->currency_id!=null)?($purchase->final_total/$purchase->exchange_price):0,2),
                    
                    
                    "shipping_details"   => $purchase->shipping_details,
                    "additional_notes"   => $purchase->additional_notes,
                    
                    "created_by"         => (!empty($user))?(($main!=null)?$user->id:($user->first_name)):"",
                    // "agent_id"           => (!empty($agent))?(($main!=null)?$agent->id:($agent->first_name)):"",
                    // "pattern"            => $purchase->pattern_id,
                    "attachment"         => $attach ,
                    # ...............................................
                    
                    "currency_id"           => $purchase->currency_id,
                    "currency_name"         => ($purchase->currency)?($purchase->currency->country . " " . $purchase->currency->currency . " ( " . $purchase->currency->code . " )"):"",
                    "amount_in_currency"    => $purchase->amount_in_currency,
                    "exchange_price"        => $purchase->exchange_price,
                    "currency_symbol"       => ($purchase->currency)?$purchase->currency->symbol:"",
                    "main_currency_symbol"  => ($business->currency)?$business->currency->symbol:"",
                    # ...............................................
                    "list"               => $lines,
                    # ...............................................
                    "payments"           => $lines_payments,
                    "payment_due"        => $due-$paid_amount,
                    "additional"         => $shippings,
                    "activities"         => $activities,
                    "add_currency"       => $purchase->currency,
                    "main_currency"      => $business->currency,
                    "tax_main"           => $purchase->tax,

                ];
                if($view == "edit"){
                    
                    unset($allData[0]['add_currency']);
                    unset($allData[0]['main_currency']);
                    unset($allData[0]['tax_main']);
                }
            }
            return $allData; 
        }catch(Exception $e){
            return false;
        }
    }
    // ****** Received
    // **1** CREATE PURCHASE
    public static function createNewPurchaseReceived($user,$request) {
        try{
            $business_id       = $user->business_id;
            $productUtil       = new ProductUtil;
            // ...............................................
            $additional_inputs = $request->only([
                                    'contact_id'
                                    ,'shipping_amount'
                                    ,'shipping_vat'
                                    ,'shipping_total'
                                    ,'shipping_account_id'
                                    ,'shiping_text'
                                    ,'shiping_date'
                                    ,'shipping_contact_id'
                                    ,'shipping_cost_center_id'
                                    ,'cost_center_id'
                                ]);
            $document_expense  = [];
            if ($request->hasFile('document_expense')) {
                $counter       = 0;  
                foreach ($request->file('document_expense') as $file) {
                    $counter   =   $counter + 1;  
                    $file_name =  'public/uploads/documents/'.time().'_'.$counter.'.'.$file->getClientOriginalExtension();
                    $file->move('public/uploads/documents',$file_name);
                    array_push($document_expense,$file_name);
                }
            }
            // ............................................
            $type           = 1;
            $sub_total      = $request->total_subtotal_input_id;
            $purchase_total = $request->final_total_hidden_items;
            $shipping_total = $request->total_final_items_;
            \App\Models\AdditionalShipping::add_purchase($request->transaction_id,$additional_inputs,$document_expense,$type,$sub_total, $purchase_total,$shipping_total,null,$user);
            // ...........................................
            $transaction           = \App\Transaction::find($request->transaction_id);
            if($request->shipping_amount != null){
                $data_ship = \App\Models\AdditionalShipping::where("transaction_id",$request->transaction_id)->where("type",1)->first();
                $ids       = $data_ship->items->pluck("id");
                foreach($ids as $i){
                   $data_shippment = \App\Models\AdditionalShippingItem::find($i);
                   \App\Models\StatusLive::insert_data_sh($transaction->business_id,$transaction,$data_shippment,"Add Expense",);
                }
             }
             $check_for_wrong = 0;
            // ..............................
            if ($request->purchases) {
                //.....
                $total                        =  0 ;
                $type                         =  'purchase_receive';
                $ref_count                    =  $productUtil->setAndGetReferenceCount($type,$transaction->business_id);
                $reciept_no                   =  $productUtil->generateReferenceNumber($type, $ref_count,$transaction->business_id);
                $tr_recieved                  =  new \App\Models\TransactionRecieved;
                $tr_recieved->store_id        =  $transaction->store;
                $tr_recieved->transaction_id  =  $transaction->id;
                $tr_recieved->business_id     =  $transaction->business_id ;
                $tr_recieved->reciept_no      =  $reciept_no ;
                $tr_recieved->ref_no          =  $transaction->ref_no;
                $tr_recieved->date            =  ($request->date == null)?\Carbon::now():$request->date;
                $tr_recieved->status          = 'purchase';
                $tr_recieved->save();
                
                foreach ($request->purchases as $key => $single) {
                   $store_   = ($request->stores_id[$key] != null)?$request->stores_id[$key]:$request->store_id;
                   $product  =  \App\Product::find($single['product_id']);
                   $margin   =  \App\PurchaseLine::check_transation_product($transaction->id,$single['product_id']);
                   $line     =  \App\PurchaseLine::where('transaction_id',$transaction->id)->where('product_id',$single['product_id'])->first();
                   $diff     =  $margin - $single['quantity'];
                   if ($diff > 0) {
                        Purchase::receive($transaction,$product,$single['quantity'],$store_,$tr_recieved,$line);
                   }else if ($diff < 0 && $line) {
                      // correct  receive
                      ($margin > 0 )? Purchase::receive($transaction,$product,$margin,$store_,$tr_recieved,$line):'';
                      // wrong  receive
                      Purchase::wrong_receive($transaction,$product,abs($diff),$store_,$tr_recieved,$line);
                      $check_for_wrong = 1;
                   }else if ($diff ==  0 && $line){
                      Purchase::receive($transaction,$product,$single['quantity'],$store_,$tr_recieved,$line);
                   }else {
                      Purchase::wrong_receive($transaction,$product,$single['quantity'],$store_,$tr_recieved);
                      $check_for_wrong = 1;
                   }
                }
             
                $total_prev        = \App\Models\RecievedPrevious::where("transaction_id",$transaction->id)->get();
                $total_wrong       = \App\Models\RecievedWrong::where("transaction_id",$transaction->id)->get();
                $previous          = \App\Models\RecievedPrevious::orderBy("id","desc")->where("transaction_id",$transaction->id)->first();
                $wrong             = \App\Models\RecievedWrong::orderBy("id","desc")->where("transaction_id",$transaction->id)->first();
                $total_final       = 0;
                $total_final_wrong = 0;
                if(!empty($previous)){
                   foreach($total_prev as $item) {
                      $cost = \App\Product::product_cost($item->id) * $item->current_qty; 
                      $total_final += $cost;
                   }
                   \App\Models\StatusLive::insert_data_pr($transaction->business_id,$transaction,$previous,"Receive items",$total_final);
                
                } 
                if(!empty($wrong)){
                   foreach($total_wrong as $item) {
                      $cost = \App\Product::product_cost($item->id) * $item->current_qty; 
                      $total_final_wrong += $cost;
                   }
                   $wrong    = \App\Models\RecievedWrong::where("transaction_id",$transaction->id)->first();
                   if(!empty($wrong)){
                      \App\Models\StatusLive::insert_data_pr($transaction->business_id,$transaction,$wrong,"Wrong receive",$total_final_wrong);
                   }
                }
            }
            // ..............................
            if($request->shipping_amount != null){
                $TransRecieved     = \App\Models\TransactionRecieved::orderBy("id","desc")->where("transaction_id",$transaction->id)->first();
                $it                = \App\Models\AdditionalShipping::orderBy("id","desc")->where("transaction_id",$transaction->id)->where("type",1)->whereNull("t_recieved")->first();
                $it->t_recieved    = $TransRecieved->id;
                $it->update(); 
                \App\Models\AdditionalShipping::add_purchase_payment($request->transaction_id,$type,$TransRecieved->id,NULL,$user);
                $map               = \App\Models\StatusLive::where("transaction_id",$transaction->id)->whereNotNull("shipping_item_id")->get();
                foreach($map as $item){
                      $item->t_received = $TransRecieved->id;
                      $item->update();
                }
             }
            // ..............................
            $cost=0;$without_contact=0;
            $data_ship = \App\Models\AdditionalShipping::where("transaction_id",$transaction->id)->where("type","!=",1)->first();
            if(!empty($data_ship)){
               $ids = $data_ship->items->pluck("id");
               foreach($ids as $i){
                  $data_shippment   = \App\Models\AdditionalShippingItem::find($i);
                  if($data_shippment->contact_id == $transaction->contact_id){ 
                     $cost += $data_shippment->amount;
                  }else{
                     $without_contact += $data_shippment->amount;
                  }
               }
            }
            // ..............................
            $cost_receive=0;$without_contact_receive=0;
            $data_ship_receive = \App\Models\AdditionalShipping::orderBy("id","desc")->where("transaction_id",$transaction->id)->where("t_recieved",$tr_recieved->id)->where("type",1)->first();
            if(!empty($data_ship_receive)){
               $ids_receive = $data_ship_receive->items->pluck("id");
               foreach($ids_receive as $i){
                  $data_shippment_receive   = \App\Models\AdditionalShippingItem::find($i);
                  if($data_shippment_receive->contact_id == $transaction->contact_id){ 
                     $cost_receive += $data_shippment_receive->amount;
                  }else{
                     $without_contact_receive += $data_shippment_receive->amount;
                  }
               }
            }
            // ..............................
            $total_expense         = $cost + $without_contact;
            $total_expense_receive = $cost_receive + $without_contact_receive;
            \App\Models\ItemMove::receive($transaction,$total_expense,$total_expense_receive,$tr_recieved->id);
            if( $check_for_wrong == 1){
               \App\Models\ItemMove::Wrong_recieve($transaction->id,$tr_recieved->id);
            }
            // ..............................
            return true;
        }catch(Exception $e){
            return false;
        }
    }
    // **2** UPDATE PURCHASE
    public static function updateOldPurchaseReceived($user,$request,$id) {
        try{
            
            $business_id    = $user->business_id;
            $TranRed        = \App\Models\TransactionRecieved::find($id);  
            $data           = \App\Transaction::find($TranRed->transaction_id);
            // ....................................................... 
            $exist_items    = $request->recieve_previous_id?$request->recieve_previous_id:[];
            $removes        = \App\Models\RecievedPrevious::where('transaction_id',$data->id)
                                                   ->where("transaction_deliveries_id",$id)
                                                   ->whereNotIn('id',$exist_items)
                                                   ->get();
            foreach ($removes as $re) {
               //.1.//
                $info    =  \App\Models\WarehouseInfo::where('store_id',$re->store_id)->where('product_id',$re->product_id)->first();
                if ($info) {
                        $info->decrement('product_qty',$re->current_qty);
                        $info->save();
                }
               //.2.//
                \App\MovementWarehouse::where('recived_previous_id',$re->id)->delete();
               //.3.//
                $q  =  $re->current_qty*-1;
                Purchase::update_variation($re->product_id,$q,$re->transaction->location_id);
               //.4.//
                \App\Models\ItemMove::delete_recieve($data->id,$re->id,$re);
               //.5.//
                  $re->delete();
            } 
            // .......................................................
            $wrong_id        = $request->recieved_wrong_id?$request->recieved_wrong_id:[];
            $wrongs          = \App\Models\RecievedWrong::where('transaction_id',$data->id)->where("transaction_deliveries_id",$id)->whereNotIn('id',$wrong_id)->get();
            $check_for_wrong = 0;
            foreach ($wrongs as $re) {
               //.1.//
                  $info =  \App\Models\WarehouseInfo::where('store_id',$re->store_id)
                           ->where('product_id',$re->product_id)->first();
                  if ($info) {
                        $info->decrement('product_qty',$re->current_qty);
                        $info->save();
                  }
               //.2.//
                  \App\MovementWarehouse::where('recieved_wrong_id',$re->id)->delete();
               //.3.// 
                  $q  =  $re->current_qty*-1;
                  Purchase::update_variation($re->product_id,$q,$re->transaction->location_id);
               //.4.// 
                  $array_del      = [];   $line_id        = [];
                  $product_id     = [];   $move_id        = []; 
                  if(!in_array($re->product_id,$line_id)){
                     $line_id[]    = $re->product_id;
                     $product_id[] = $re->product_id;
                  }
                  $wrongMove = \App\Models\ItemMove::where("transaction_id",$data->id)->where("recieve_id",$re->id)->first();
                  if(!empty($wrongMove)){
                     $move_id[] = $wrongMove->id; 
                  }
                  if(!empty($wrongMove)){
                     $date  = ($wrongMove->date != null) ? $wrongMove->date : $wrongMove->created_at  ;
                     $wrongMove->delete();
                     // *** refresh in new way #$%
                     \App\Models\ItemMove::updateRefresh($wrongMove,$wrongMove,$move_id,$date);
                  }
               //.5.//
                  $re->delete();
            }  
            // ....................................................... 
            if ($wrong_id != [] && count($wrong_id)>0){
                $check_for_wrong = 1;
            }

            if ($request->recieve_previous_id) {
                foreach ($request->recieve_previous_id  as $key => $pr_id) {
                        $prev              =  \App\Models\RecievedPrevious::find($pr_id);
                        $sum_product_id    =  \App\PurchaseLine::where("transaction_id",$data->id)->where("product_id",$prev->product_id)->sum("quantity");

                        $line              =  \App\PurchaseLine::where("transaction_id",$data->id)->where("product_id",$prev->product_id)->first();
                        $old_store         =  $prev->store_id;
                        $old_qty           =  $prev->current_qty*-1;
                        $diff              =  $request->recieve_previous_qty[$key] - $prev->current_qty;
                        $_store            =  $request->old_store_id[$key] ;
                        $prev->store_id    =  $request->old_store_id[$key];
                        $prev->total_qty   =  $sum_product_id;
                        $prev->current_qty =  $request->recieve_previous_qty[$key];
                        $prev->save();
                        if ($old_store ==  $request->old_store_id[$key]) {
                        \App\Models\WarehouseInfo::update_stoct($prev->product_id,$prev->store_id,$diff,$data->business_id);
                        }else{
                        \App\Models\WarehouseInfo::update_stoct($prev->product_id,$prev->store_id,$request->recieve_previous_qty[$key],$data->business_id);
                        \App\Models\WarehouseInfo::update_stoct($prev->product_id,$old_store,$old_qty,$data->business_id);
                        }
                    Purchase::update_variation($prev->product_id,$diff,$prev->transaction->location_id);
                    \App\MovementWarehouse::recieve_return($pr_id,$request->recieve_previous_qty[$key],"correct",$_store);
                    // *** AGT8422 FOR UPDATE WAREHOUSE ROWS ********************************************************************
                    \App\MovementWarehouse::update_receive($data,$prev,$request->recieve_previous_qty[$key],"correct",$_store,$line);   
                    // *** **************************** **** ********************************************************************
                }
            }
             
            if ($request->recieved_wrong_id){
                foreach ($request->recieved_wrong_id  as $key => $pr_id) {
                    $prev              =  \App\Models\RecievedWrong::find($pr_id);
                    $line              =  \App\PurchaseLine::where("transaction_id",$data->id)->where("product_id",$prev->product_id)->first();
                    \App\MovementWarehouse::update_receive($data,$prev,$prev->current_qty,"wrong",$prev->store_id,$line);   
                }
            } 
            // .......................................................  
            $tr_recieved                  = \App\Models\TransactionRecieved::find($id);
            $tr_recieved->date            = ($request->date == null)?\Carbon::now():$request->date;
            $tr_recieved->update();
            // .......................................................  
            if ($request->purchases ) {
                $data    =    \App\Transaction::find($data->id);
                $total   = 0 ;
                   
                foreach ($request->purchases as $key => $single) {
                   // check transaction  
                   $margin =  \App\PurchaseLine::check_transation_product($data->id,$single['product_id']);
                   $diff   =  $margin - $single['quantity'];
             
                   $product  =  \App\Product::find($single['product_id']);
                   $line     =  \App\PurchaseLine::where('transaction_id',$data->id)
                                        ->where('product_id',$single['product_id'])->first();
                   if($request->stores_id[$key] != null){
                      $store_   = $request->stores_id[$key];
                   }else{
                      $store_   = $request->store_id;
                   }
                   if ($diff > 0) {
                      Purchase::receive($data,$product,$single['quantity'],$store_,$tr_recieved,$line);
                   }elseif ($diff < 0 && $line) {
                      // correct  recieve
                      ($margin > 0 )? Purchase::receive($data,$product,$margin,$store_,$tr_recieved,$line):'';
                      //wrong  recieve
                      Purchase::wrong_receive($data,$product,abs($diff),$store_,$tr_recieved);
                      $check_for_wrong = 1 ;
                   }elseif($diff ==  0 && $line){
                      Purchase::receive($data,$product,$single['quantity'],$store_,$tr_recieved,$line);
                   }else{
                      Purchase::wrong_receive($data,$product,$single['quantity'],$store_,$tr_recieved);
                      $check_for_wrong = 1 ;
                   }
    
                }
    
             }
            // .......................................................
            $additional_inputs = $request->only([
                'contact_id','shipping_amount','shipping_vat','shipping_total','shipping_account_id','shiping_text',
                'shiping_date','additional_shipping_item_id','old_shipping_amount','old_shipping_vat','old_shipping_total','old_shipping_account_id',
                'old_shiping_text','old_shiping_date','old_shipping_contact_id','shipping_contact_id','old_shipping_cost_center_id','cost_center_id'
            ]); 
            $document_expense = $request->old_document??[];
            if ($request->hasFile('document_expense')) {
                foreach ($request->file('document_expense') as $file) {
                    $file_name =  'public/uploads/documents/'.time().'.'.$file->getclientoriginalextension();
                    $file->move('public/uploads/documents',$file_name);
                    array_push($document_expense,$file_name);
                }
            }  
            // .......................................................
            $total_prev        = \App\Models\RecievedPrevious::where("transaction_id",$data->id)->get();
            $total_wrong       = \App\Models\RecievedWrong::where("transaction_id",$data->id)->get();
            $previous          = \App\Models\RecievedPrevious::orderBy("id","desc")->where("transaction_id",$data->id)->first();
            $wrong             = \App\Models\RecievedWrong::orderBy("id","desc")->where("transaction_id",$data->id)->first();
            $line              = \App\PurchaseLine::where('transaction_id',$data->id)->sum("quantity");
            $type              = "update";
            \App\Models\AdditionalShipping::update_purchase($data->id,$additional_inputs,$document_expense,$type,$id);
            \App\Models\AdditionalShipping::add_purchase_payment($data->id,$type,$id);
            \App\Models\StatusLive::update_data_pr($data->business_id,$data,$previous,"Receive items");  
            // .......................................................
            $info = \App\Models\TransactionRecieved::where("transaction_id",$data->id)->get();
            if(!(count($info)>0)){
                $trans_change_status = \App\Transaction::find($data->id);
            }elseif($total_prev->sum("current_qty")<$line){
                $trans_change_status = \App\Transaction::find($data->id);
            }
            \App\Models\ItemMove::recieve_update($id,$data->id,$TranRed->id);
            if( $check_for_wrong == 1){
                \App\Models\ItemMove::Wrong_recieve($data->id,$tr_recieved->id);
            }  
            // .......................................................  
            return true;
        }catch(Exception $e){
            return false;
        }
    }
    // **3** GET  PURCHASE
    public static function allReceivedData($type=null,$id=null,$business_id,$request=null,$main = null,$view=null,$transaction_id=null) {
        try{
            $list      = [];
            $allData   = [];
            if($type != null){
                // ....................START..
                $page         = $request->query('page',1);
                $skip         = $request->query('skip',0);
                $limit        = $request->query('limit',25);
                $skpP         = ($page-1)*$limit;
                $count        = \App\Models\TransactionRecieved::where("business_id",$business_id);
                if($transaction_id!=null){
                     $count->where("transaction_id",$transaction_id);
                }
                $count        = $count->count();
                $totalPages   = ceil($count / $limit);
                $prevPage     = $page > 1 ? $page - 1 : null;
                $nextPage     = $page < $totalPages ? $page + 1 : null;
                // ....................END....
                $received           = \App\Models\TransactionRecieved::where("business_id",$business_id);
                if($transaction_id!=null){
                     $received->where("transaction_id",$transaction_id);
                }
                $received           = $received->skip($skpP)->limit($limit)->get();
                if(count($received) == 0 ){ return false; }
                foreach($received as $ie){
                    $product_list   = []; 
                    $lines          = [];
                    $lines_wrong    = [];
                    $lines_payments = [];
                    $items          = \App\Models\RecievedPrevious::where("transaction_deliveries_id",$ie->id)->get();
                    $wrong_items    = \App\Models\RecievedWrong::where("transaction_deliveries_id",$ie->id)->get();
                    foreach($items as $ii){
                        $lines[] = [
                            "id"           => $ii->id,
                            "product_id"   => $ii->product_id,
                            "productName"  => $ii->product_name,
                            "unit_id"      => $ii->unit_id,
                            "unitName"     => ($ii->unit)?$ii->unit->actual_name:"",
                            // "total_qty"    => $ii->total_qty,
                            "current_qty"  => $ii->current_qty,
                            // "remain_qty"   => $ii->remain_qty, 
                            "store"        => $ii->store_id, 
                            "storeName"    => ($ii->store)?$ii->store->name:"", 
                            "line_id"      => $ii->line_id, 
                            "is_returned"  => $ii->is_returned, 
                        ];
                    }
                    foreach($wrong_items as $ii){
                        $lines_wrong[] = [
                            "id"           => $ii->id,
                            "product_id"   => $ii->product_id,
                            "productName"  => $ii->product_name,
                            "unit_id"      => $ii->unit_id,
                            "unitName"     => ($ii->unit)?$ii->unit->actual_name:"",
                            // "total_qty"    => $ii->total_qty,
                            "current_qty"  => $ii->current_qty,
                            // "remain_qty"   => $ii->remain_qty, 
                            "store"        => $ii->store_id, 
                            "storeName"    => ($ii->store)?$ii->store->name:"", 
                            "line_id"      => $ii->line_id, 
                            "is_returned"  => $ii->is_returned, 
                        ];
                    }
                    $list[] = [
                        "id"                     => $ie->id,
                        "reference"              => $ie->reciept_no, 
                        "store"                  => ($ie->store!=null)?$ie->store->id:"",  
                        "storeName"              => ($ie->store!=null)?$ie->store->name:"",  
                        "transaction_reference"  => ($ie->transaction!=null)?$ie->transaction->ref_no:"", 
                        "transaction_id"         => $ie->transaction_id, 
                        "status"                 => $ie->status,
                        "date"                   => ($ie->date!=null)?$ie->date:$ie->created_at->format('Y-m-d'),
                        "created_by"             => ($ie->user)?$ie->user->first_name:"",
                        "lines"                  => $lines,
                        "wrong_lines"            => $lines_wrong,
                    ];
                }
                // ................................................. 
                $allData["items"] = $list;
                $allData["info"]  = [
                    "totalRows"     => $count,
                    'current_page'  => $page,
                    'last_page'     => $totalPages,
                    'limit'         => 25,
                    'prev_page_url' => $prevPage ? "/api/app/react/purchase/all?page=$prevPage" : null,
                    'next_page_url' => $nextPage ? "/api/app/react/purchase/all?page=$nextPage" : null,
                ];
                 
            }else{
                $received    = \App\Models\TransactionRecieved::find($id);
                $business    = \App\Business::find($received->business_id);
                if(empty($received)){ return false; }
                $lines          = [];
                $lines_wrong    = [];
                $product_ids    = [];
                $product_qty    = [];
                $items          = \App\Models\RecievedPrevious::where("transaction_deliveries_id",$id)->get();
                $purchase_line  = \App\Models\RecievedPrevious::where("transaction_deliveries_id",$id)->pluck('line_id');
                foreach($purchase_line as $i){
                    $pl =  \App\PurchaseLine::find($i);
                    if(!in_array($pl->product_id,$product_ids)){
                        $product_ids[]   = $pl->product_id;
                        $product_qty[$pl->product_id] =  $pl->quantity ;
                    }else{
                        $product_qty[$pl->product_id] =  $product_qty[$pl->product_id] + $pl->quantity ;
                    }
                }
                $wrong_items    = \App\Models\RecievedWrong::where("transaction_deliveries_id",$id)->get();
                foreach($items as $ii){
                    $total_qty         = (in_array($ii->product_id,$product_ids))?$product_qty[$ii->product_id]:0;
                    $lines[] = [
                        "id"           => $ii->id,
                        "product_id"   => $ii->product_id,
                        "productName"  => $ii->product_name,
                        "unit_id"      => $ii->unit_id,
                        "unitName"     => ($ii->unit)?$ii->unit->actual_name:"",
                        // "total_qty"    => $ii->total_qty,
                        "current_qty"  => $ii->current_qty,
                        // "remain_qty"   => $ii->remain_qty, 
                        "store"        => $ii->store_id, 
                        "storeName"    => ($ii->store)?$ii->store->name:"", 
                        "line_id"      => $ii->line_id, 
                        "is_returned"  => $ii->is_returned, 
                        "allQuantity"  => $total_qty, 
                    ];
                }
                foreach($wrong_items as $ii){
                    $lines_wrong[] = [
                        "id"           => $ii->id,
                        "product_id"   => $ii->product_id,
                        "productName"  => $ii->product_name,
                        "unit_id"      => $ii->unit_id,
                        "unitName"     => ($ii->unit)?$ii->unit->actual_name:"",
                        // "total_qty"    => $ii->total_qty,
                        "current_qty"  => $ii->current_qty,
                        // "remain_qty"   => $ii->remain_qty, 
                        "store"        => $ii->store_id, 
                        "storeName"    => ($ii->store)?$ii->store->name:"", 
                        "line_id"      => $ii->line_id, 
                        "is_returned"  => $ii->is_returned, 
                    ];
                }
                $allData[] = [
                    "id"                     => $received->id,
                    "reference"              => $received->reciept_no, 
                    "store"                  => ($received->store!=null)?$received->store->id:"",
                    "storeName"              => ($received->store!=null)?$received->store->name:"",  
                    "transaction_reference"  => ($received->transaction!=null)?$received->transaction->ref_no:"", 
                    "transaction_id"         => $received->transaction_id, 
                    "status"                 => $received->status,
                    "date"                   => ($received->date!=null)?$received->date:$received->created_at->format('Y-m-d'),
                    "created_by"             => ($received->user)?$received->user->first_name:"",
                    "lines"                  => $lines,
                    "wrong_lines"            => $lines_wrong,

                ];
                
            }
            return $allData; 
        }catch(Exception $e){
            return false;
        }
    }


    // ******** REQUIREMENT FUNCTIONS
    // **1** 
    public static function data($user,$type,$id=null){
        $requirement   = [];
        // ...........................................................1**..
        $status_list   = [
            "received"   => "Received",
            "final"      => "Final",
            "pending"    => "Pending",
            "ordered"    => "Ordered",
        ];
        $payment_term_list   = [
            ""           => "Please_Select",
            "days"       => "Day",
            "months"     => "Month",
        ];
        $discount_list   = [
            ""                       => "None",
            "fixed_before_vat"       => "Fixed Before Vat",
            "fixed_after_vat"        => "Fixed After Vat",
            "percentage"             => "Percentage",
        ];
        // ..........................................................2**...
        $warehouse_list              = \App\Models\Warehouse::childs($user->business_id);
        // $currency                    = \App\Models\ExchangeRate::where("source","!=",1)->get();
        // $currency_list               = [];
        // foreach($currency as $i){
        //     $currency_list[$i->currency->id] = $i->currency->country . " " . $i->currency->currency . " ( " . $i->currency->code . " )";
        // }
        $currency = [];
        $amount   = 1;
            
        $allCurrency           = \App\Models\ExchangeRate::where("source","!=",1)->where("business_id",$user->business_id)->get();
        foreach( $allCurrency as $item){
            $currency_set      = \App\Models\ExchangeRate::where("id",$item->id)->first();
            if(!empty($currency_set)){
                if($currency_set->right_amount == 0){
                    $amount = $currency_set->amount;
                }else{
                    $amount = $currency_set->opposit_amount;
                }
                $symbol = $currency_set->currency->symbol;
                
            }else{
                $symbol = "";
            }
            $currency[] = [
                "id"        => $item->currency->id,
                "value"     => $item->currency->country . " " . $item->currency->currency . " ( " . $item->currency->code . " )",
                "amount"    => $amount,
                "symbol"    => $symbol
                ] ; 
        }   
        $tax_list                    = \App\TaxRate::where('business_id', $user->business_id)->select(["id","name","amount"])->get();
        $tax_x                       = [];
        foreach($tax_list as $key => $value){
            $tax_x[]    = [
                    "id"        => $value->id,
                    "value"     => $value->name,
                    "amount"    => $value->amount,
            ] ; 
            
        }
        $row                         = 1;
        $cost_center_list            = \App\Account::Cost_center();
        $account_list                = \App\Account::main("cash",$user->business_id);
        $list_of_prices              = \App\Product::getListPrices($row);
        $contacts                    = \App\Contact::suppliers($user->business_id);
        $expenses                    = \App\Account::main('Expenses',$user->business_id);
        // ..........................................................3**...
        $reference                        = \App\ReferenceCount::where("ref_type","purchase")->where("business_id",$user->business_id)->first();
        if(empty($reference)){ $count = 1; }else{
            $count = $reference->ref_count + 1;
        }
        $prefix                           = '';
        $business                         = \App\Business::find($user->business_id);
        $prefixes                         = $business->ref_no_prefixes;
        $prefix                          .= !empty($prefixes['purchase']) ? $prefixes['purchase'] : '';
        $ref_digits                       =  str_pad($count, 5, 0, STR_PAD_LEFT);
        $ref_year                         = \Carbon::now()->year;
        $ref_number                       =  $prefix . $ref_year . '/' . $ref_digits;
        // .............................................................
        $layout                           = \App\InvoiceLayout::where('business_id',$user->business_id)->where('is_default',1)->first();
        // .............................................................
        $company_details                  = ($layout)?(($layout->header_text != null)?$layout->header_text:""):"";
        // .............................................................
        $logo                             = ($layout)?(($layout->logo_url != null)?$layout->logo_url:""):"";
        if($type == "edit"){$ref_number = (\App\Transaction::find($id))?\App\Transaction::find($id)->ref_no:$ref_number; }
        // .............................................................
        // ** data
        $requirement["company_logo"]      =  $logo ;
        $requirement["invoice_number"]    =  $ref_number ;
        $requirement["company_details"]   =  $company_details ;
        $requirement["status"]            =  GlobalUtil::arrayToObject($status_list) ;
        $requirement["stores"]            =  GlobalUtil::arrayToObject($warehouse_list) ;
        $requirement["currencies"]        =  $currency;
        $requirement["prices"]            =  GlobalUtil::arrayToObject($list_of_prices);
        $requirement["cost_center"]       =  GlobalUtil::arrayToObject($cost_center_list) ;
        $requirement["discount"]          =  GlobalUtil::arrayToObject($discount_list) ;
        $requirement["taxes"]             =  $tax_x  ;
        $requirement["accounts"]          =  GlobalUtil::arrayToObject($account_list) ;
        $requirement["suppliers"]         =  GlobalUtil::arrayToObject($contacts) ;
        $requirement["expense_accounts"]  =  GlobalUtil::arrayToObject($expenses) ;
        return $requirement;
    }
    // **2** 
    public static function dataPayment($user,$id){
        $transactionUtil   = new TransactionUtil();
        // ..........................................................1**..
        $business_id       = $user->business_id ;
        $transaction       = \App\Transaction::where('business_id', $business_id)->with(['contact', 'location'])->find($id);
        if ($transaction->payment_status != 'paid') {
            $show_advance          = in_array($transaction->type, ['sale', 'purchase']) ? true : false;
            $payment_types         = $transactionUtil->payment_types($transaction->location, $show_advance);
            $paid_amount           = $transactionUtil->getTotalPaid($id);
            $amount                = $transaction->final_total - $paid_amount;
            if ($amount < 0) {
                $amount = 0;
            } 

            $payment_line          = new \App\TransactionPayment();
            $payment_line->amount  = $amount;
            $payment_line->method  = 'cash';
            $payment_line->paid_on = \Carbon::now()->toDateTimeString();
            $cheques               = \App\Models\check::where("transaction_id",$transaction->id)->whereIn("status",[0,2])->get();
            $transaction_child     = \App\Transaction::where("separate_parent",$transaction->id)->where("separate_type","payment")->get();

           
            //Accounts
            $accountsCash =  \App\Account::main('cash',$business_id);
            $accountsBank =  \App\Account::main('bank',$business_id );
            $cheque_type  =  ($transaction->type == 'purchase' || $transaction->type == 'sell_return')?1:0;
            $output       = [         
                'status'             => 'due',
                'payment_method'     => GlobalUtil::arrayToObject($payment_types),
                'accounts_cash'      => GlobalUtil::arrayToObject($accountsCash),
                'accounts_bank'      => GlobalUtil::arrayToObject($accountsBank),
                'payment_amount'     => $amount,
                'old_payment_amount' => $paid_amount,
                'cheque_type'        => $cheque_type,
                'old_cheques'        => $cheques,
                'separate_bill'      => $transaction_child,
             ];
        } else {
            $output       = [      
                'status'             => 'paid',
                'payment_method'     => '',
                'account'            => [],
                'payment_amount'     => 0,
                'old_payment_amount' => 0,
                'cheque_type'        => [],
                'old_cheques'        => [],
                'separate_bill'      => [],
             ];
        }
        // ..........................................................2**...
        
        
        // ..........................................................3**...
        
        // $requirement["status"]       =  GlobalUtil::arrayToObject($status_list) ;
 
        return $output;
    }
    // **3** 
    public static function viewPayment($user,$id){
        $transactionUtil    = new TransactionUtil();
        // ..........................................................1**..
        $business_id        = $user->business_id ;
        $transaction        = \App\Transaction::where('id', $id)->with(['contact', 'business', 'transaction_for'])->first();
        $payments_query     = \App\TransactionPayment::where('transaction_id', $id);
        $all_payments_bill  = \App\Transaction::where("separate_parent",$id)->where("separate_type","payment")->get();
        $payments_bill      = [];
        
        foreach($all_payments_bill as $all){
            $payments_row        = \App\TransactionPayment::where('transaction_id', $all->id);
            $payments_bill[]     = $payments_row->first();
        }
         
        $payments      = $payments_query->get();
        $location_id   = !empty($transaction->location_id) ? $transaction->location_id : null;
        $payment_types = $transactionUtil->payment_types($location_id, true);
        // ..........................................................2**...
        $output       = [      
            'payments'           => $payments,
            'payments_bill'      => $payments_bill,
         ];
        // ..........................................................3**...
        
        // $requirement["status"]       =  GlobalUtil::arrayToObject($status_list) ;
 
        return $output;
    }
    // **4**
    public static function dataReceived($user) {
        $requirement                 =  [];
        $cost_center_list            =  \App\Account::Cost_center();
        $account_list                =  \App\Account::main("cash",$user->business_id);
        $warehouse_list              =  \App\Models\Warehouse::childs($user->business_id);
        $allCurrency           = \App\Models\ExchangeRate::where("source","!=",1)->where("business_id",$user->business_id)->get();
        foreach( $allCurrency as $item){
            $currency_set      = \App\Models\ExchangeRate::where("id",$item->id)->first();
            if(!empty($currency_set)){
                if($currency_set->right_amount == 0){
                    $amount = $currency_set->amount;
                }else{
                    $amount = $currency_set->opposit_amount;
                }
                $symbol = $currency_set->currency->symbol;
                
            }else{
                $symbol = "";
            }
            $currency[] = [
                "id"        => $item->currency->id,
                "value"     => $item->currency->country . " " . $item->currency->currency . " ( " . $item->currency->code . " )",
                "amount"    => $amount,
                "symbol"    => $symbol,
                ] ; 
        }
        $expenses                    = \App\Account::main('Expenses',$user->business_id);
        // ..............................................................................
        $requirement["cost_center"]       =  GlobalUtil::arrayToObject($cost_center_list) ;
        $requirement["stores"]            =  GlobalUtil::arrayToObject($warehouse_list) ;
        $requirement["currencies"]        =  $currency;
        $requirement["accounts"]          =  GlobalUtil::arrayToObject($account_list) ;
        $requirement["expense_accounts"]  =  GlobalUtil::arrayToObject($expenses) ;
        return $requirement;
    }


    // ******* PURCHASE RECEIVED ******* \\
    // **1**
    public static function getAllPurchaseReceived($user,$request) {
        try{
            $list           = [];
            $business_id    = $user->business_id;
            $purchase       = Purchase::allReceivedData("all",null,$business_id,$request); 
            if($purchase == false){ return false;}
            return $purchase;
        }catch(Exception $e){
            return false;
        }
    }
    // **1**
    public static function getPurchaseReceived($user,$request,$id) {
        try{
            $list           = [];
            $business_id    = $user->business_id;
            $purchase       = Purchase::allReceivedData("all",null,$business_id,$request,null,null,$id); 
            if($purchase == false){ return false;}
            return $purchase;
        }catch(Exception $e){
            return false;
        } 
    }
    // **2**
    public static function createPurchaseReceived($user,$request) {
        try{
            $business_id             = $user->business_id;
            $data                    = Purchase::dataReceived($user);
            return $data;
        }catch(Exception $e){
            return false;
        }
    }
    // **3**
    public static function editPurchaseReceived($user,$request,$id) {
        try{
            $business_id             = $user->business_id;
            $received                = Purchase::allReceivedData(null,$id,$business_id,null,"main","edit");
            if(!$received){ return false; }
            $data                    = Purchase::dataReceived($user);
            $list["requirement"]     = $data;
            $list["info"]            = $received;
            return $list;
        }catch(Exception $e){
            return false;
        } 
    }
    // **4**
    public static function savePurchaseReceived($user,$request) {
        try{
            \DB::beginTransaction();
            $business_id         = $user->business_id;
            $data["business_id"] = $business_id;
            $data["created_by"]  = $user->id;
            $output              = Purchase::createNewPurchaseReceived($user,$request);
            if($output == false){ return false; } 
            \DB::commit();
            return $output;
        }catch(Exception $e){
            return false;
        }
    }
    // **5**
    public static function updatePurchaseReceived($user,$request,$id) {
        try{
            \DB::beginTransaction();
            $business_id         = $user->business_id;
            $data["business_id"] = $business_id;
            $data["created_by"]  = $user->id;
            $output              = Purchase::updateOldPurchaseReceived($user,$request,$id);
            \DB::commit();
            return $output;
        }catch(Exception $e){
            return false;
        }
    }
    // **6**
    public static function viewPurchaseReceived($user,$request,$id) {
        try{
            \DB::beginTransaction();  
            $business_id = $user->business_id;
            $voucher     = Purchase::allReceivedData(null,$id,$business_id,null,"main","edit");
            if($voucher  == false){ return false; }
            \DB::commit();
            return $voucher;
        }catch(Exception $e){
            return false;
        }
    }
    // **7**
    public static function printPurchaseReceived($user,$request,$id) {
        try{
            \DB::beginTransaction();
            $business_id  = $user->business_id;
            $transaction  = \App\Models\TransactionRecieved::find($id);
            if(empty($transaction)){ return false; }
            $file  =   \URL::to('reports/receive/'.$id) ; 
            \DB::commit();
            return $file;
        }catch(Exception $e){
            return false;
        }
    }
    // **8**
    public static function attachPurchaseReceived($user,$request,$id) {
        try{
            \DB::beginTransaction();
            $list_of_attach       =  []; 
            $business_id          =  $user->business_id;
            // ..................................................................1........
            $transaction          =  \App\Models\TransactionRecieved::find($id);
            // ..................................................................2........
            $attach     =  isset($transaction->document)?$transaction->document:null ;
            if($attach != null){
                foreach($attach as $doc){
                    $list_of_attach[]  =  \URL::to($doc);
                } 
            }
            \DB::commit();
            return $list_of_attach;
        }catch(Exception $e){
            return false;
        }
    }
    // **9**
    public static function deletePurchaseReceived($user,$request,$id) {
        try{
            \DB::beginTransaction();
            $productUtil         = new ProductUtil();
            $business_id         = $user->business_id;
            $payment             = \App\Models\TransactionRecieved::find($id);
            if(!$payment){ return "notFound"; } 
            $transaction         = $payment->transaction_id;
            $ids                 = \App\Models\TransactionRecieved::childs($id);
            $ids_wrong           = \App\Models\TransactionRecieved::childs_wrong($id);
            $additional_shipping = \App\Models\AdditionalShipping::where("t_recieved",$id)->first();
            $RecievedPrevious    = \App\Models\RecievedPrevious::whereIn("id",$ids)->get();
            $RecievedWrong       = \App\Models\RecievedWrong::whereIn("id",$ids_wrong)->get();
            //.1.// ........ 
            if(!empty($additional_shipping)){
                $ids_shipping = $additional_shipping->items->pluck("id");
                foreach($ids_shipping as $i){
                        $ship = \App\Models\AdditionalShippingItem::find($i);
                        $ship->delete();
                }
                $additional_shipping->delete();
            }
            //.2.// ..... 
            $receive = \App\Models\RecievedPrevious::where("transaction_id",$transaction)->where("transaction_deliveries_id",$id)->get();
            if(count($receive)>0){
                $tr        = \App\Transaction::find($transaction); 
                $sum       = \App\Models\RecievedPrevious::where("transaction_id",$transaction)->where("transaction_deliveries_id",$id)->sum("current_qty");
                $previous  = \App\Models\RecievedPrevious::where("transaction_id",$transaction)->where("transaction_deliveries_id",$id)->get();
                $move_id        = [];
                foreach($previous as $it){
                    $ite_m = \App\Models\ItemMove::where("transaction_id",$transaction)->where("recieve_id",$it->id)->first();
                    if(!empty($ite_m)){
                        $move_id[] = $ite_m->id; 
                    }
                    if(!empty($ite_m)){
                        $ite_m->delete();
                        \App\Models\ItemMove::updateRefresh($ite_m,$ite_m,$move_id,null);
                    }
                }
                foreach($previous as $it){
                    if($it->additional_shipping_id == $id ){
                        $it->delete();
                    }
                }
            }else{
                $items = \App\Models\ItemMove::where("transaction_id",$transaction)->get();
                foreach($items as $it){
                    $it->delete();
                }
            }
            //.2-1.// ..... 
            $wrong = \App\Models\RecievedWrong::where("transaction_id",$transaction)->where("transaction_deliveries_id",$id)->get();
            if(count($wrong)>0){
                $tr             = \App\Transaction::find($transaction); 
                $sum            = \App\Models\RecievedWrong::where("transaction_id",$transaction)->sum("current_qty");
                $wrong          = \App\Models\RecievedWrong::where("transaction_id",$transaction)->get();
                $move_id        = [];
                foreach($wrong as $it){
                    $ite_m = \App\Models\ItemMove::where("transaction_id",$transaction)->where("recieve_id",$it->id)->first();
                    if(!empty($ite_m)){
                        $move_id[] = $ite_m->id; 
                    }
                    if(!empty($ite_m)){
                        $ite_m->delete();
                        \App\Models\ItemMove::updateRefresh($ite_m,$ite_m,$move_id,null);
                    }
                }
                foreach($wrong as $it){
                    if($it->additional_shipping_id == $id ){
                        $it->delete();
                    }
                }
            }else{
                $items = \App\Models\ItemMove::where("transaction_id",$transaction)->get();
                foreach($items as $it){
                    $it->delete();
                }
            }
            //.3.// ............. 
            if(count($RecievedPrevious)>0){
                foreach($RecievedPrevious as $rp){
                    $productUtil->decreaseProductQuantity(
                        $rp->product->id  ,
                        $rp->product->variations[0]->id ,
                        $rp->product->product_locations[0]->id,
                        $rp->current_qty
                    );
              
                    \App\Models\WarehouseInfo::where("product_id",$rp->product->id)
                                                ->where("store_id",$rp->store->id)
                                                ->decrement('product_qty',$rp->current_qty);
                    
                    \App\MovementWarehouse::where("product_id",$rp->product->id)
                                                   ->where("transaction_id",$rp->transaction_id)
                                                   ->where("recived_previous_id",$rp->id)
                                                //    ->where("store_id",$rp->store->id)
                                                   ->delete();
                    $rp->delete();
                }
            }
            //.4.// .........
            if(count($RecievedWrong)>0){
                foreach($RecievedWrong as $rp){
                    $productUtil->decreaseProductQuantity(
                        $rp->product->id  ,
                        $rp->product->variations[0]->id ,
                        $rp->product->product_locations[0]->id,
                        $rp->current_qty
                    );
                    
                    \App\Models\WarehouseInfo::where("product_id",$rp->product->id)
                                                        ->where("store_id",$rp->store->id)
                                                        ->decrement('product_qty',$rp->current_qty);
                
                    \App\MovementWarehouse::where("product_id",$rp->product->id)
                                                ->where("transaction_id",$rp->transaction_id)
                                                ->where("recieved_wrong_id",$rp->id)
                                                // ->where("store_id",$rp->store->id)
                                                ->delete();
                    $rp->delete();
                }
            }
            //.5.// ... 
            $StatusLive = \App\Models\StatusLive::where("t_received",$id)->get();
            foreach($StatusLive as $s_live){
                if($s_live->shipping_item_id != null){
                    $s_live->delete();
                }else{
                    $tr  = \App\Transaction::find($transaction); 
                    $s_live->state   = "Purchase ".$tr->status; 
                    $s_live->update();
                }
            }
            //.6.// .... 
            $payment->delete();
            //.7.//  ... 
            $info = \App\Models\TransactionRecieved::where("transaction_id",$transaction)->get();
            if(!(count($info)>0)){
                $trans_change_status = \App\Transaction::find($transaction);
                $trans_change_status->status = "final";
                $trans_change_status->update();
            } 
            \DB::commit();
            return "saved";
        }catch(Exception $e){
            return "notFound";
        }
    }

    // ****
    public static function wrong_receive($data,$product,$quantity,$store_id,$tr_recieved) {
        
        $transactionUtil                   =  new TransactionUtil();
        $productUtil                       =  new ProductUtil();
        $prev                              =  new \App\Models\RecievedWrong;
        $prev->product_id                  =  $product->id;
        $prev->store_id                    =  $store_id;
        $prev->business_id                 =  $data->business_id ;
        $prev->transaction_id              =  $data->id;
        $prev->unit_id                     =  $product->unit_id;
        $prev->total_qty                   =  0;
        $prev->current_qty                 =  $quantity;
        $prev->remain_qty                  =  ($quantity*-1);
        $prev->transaction_deliveries_id   =  $tr_recieved->id;
        $prev->product_name                =  $product->name;
        $prev->save();
        // must be the same arrangement
        
        \App\Models\WarehouseInfo::update_stoct($product->id,$store_id,$quantity,$data->business_id);
        $line =  \App\PurchaseLine::OrderBy('id','desc')->where('product_id',$product->id)->first();
        \App\MovementWarehouse::movemnet_warehouse($data,$product,$quantity,$store_id,$line,'plus',NULL,$prev->id,"received");
        //*** eb ..............................................................
        $variation_id = \App\Variation::where("product_id" , $product->id)->first();
        //.....................................................................
        $currency_details = $transactionUtil->purchaseCurrencyDetails($data->business_id);
        $productUtil->updateProductQuantity($data->location_id, $product->id, $variation_id->id, $quantity, 0, $currency_details);
        
    }
    public static function receive($data,$product,$quantity,$store_id,$tr_recieved,$line) {
        
        $transactionUtil                   =  new TransactionUtil();
        $productUtil                       =  new ProductUtil();
        $prev                              =  new \App\Models\RecievedPrevious;
        $prev->product_id                  =  $product->id;
        $prev->store_id                    =  $store_id;
        $prev->business_id                 =  $data->business_id ;
        $prev->transaction_id              =  $data->id;
        $prev->unit_id                     =  $product->unit_id;
        $prev->total_qty                   =  $line->quantity;
        $prev->current_qty                 =  $quantity;
        $prev->remain_qty                  =  0;
        $prev->transaction_deliveries_id   =  $tr_recieved->id;
        $prev->product_name                =  $product->name;  
        $prev->line_id                     =  $line->id;  
        $prev->save();
        // must be the same arrangement
        \App\Models\WarehouseInfo::update_stoct($product->id,$store_id,$quantity,$data->business_id);
        \App\MovementWarehouse::movemnet_warehouse($data,$product,$quantity,$store_id,$line,'plus',$prev->id,null,"received");
        $currency_details = $transactionUtil->purchaseCurrencyDetails($data->business_id);
        $productUtil->updateProductQuantity($data->location_id, $product->id, $line->variation_id, $quantity, 0, $currency_details);

    }
    public static function update_variation($id,$quantity,$location){
        $data    =  \App\VariationLocationDetails::where('product_id',$id)->where('location_id',$location)->first();
        if ($data) {
        $data->qty_available =  $data->qty_available + $quantity;
        $data->save();
        }
    }
    // .........................................................
    // Supplier ................................................
    // get last one ............................................ 
    public static function lastPurchaseContact($user,$data) {
        try{
            \DB::beginTransaction();  
            $business_id = $user->business_id;
            $type        = $data['type'];
            $contact     = GlobalUtil::lastContact($business_id,$type);
            if($contact  == false){ return false; }
            \DB::commit();
            return $contact;
        }catch(Exception $e){
            return false;
        }
    }
    // check id name is exist .................................. 
    public static function checkPurchaseContact($user,$data) {
        try{
            \DB::beginTransaction();  
            $business_id = $user->business_id;
            $name        = $data['value'];
            $check       = GlobalUtil::checkContactName($business_id,$name);
            if($check  == false){ return false; }
            \DB::commit();
            return $check;
        }catch(Exception $e){
            return false;
        }
    }  
    // log file of purchase ....................................
    public static function logFilePurchase($user,$id) {
        try{
            \DB::beginTransaction();  
            $business_id   = $user->business_id;
            $purchase      = \App\Transaction::find($id);
            if($purchase  == null){ return false; }
            $child         = \App\Models\ArchiveTransaction::where("new_id",$id)->orderby("id","asc")->get();
            $child_ids     = \App\Models\ArchiveTransaction::where("new_id",$id)->orderby("id","asc")->pluck('id');
            $payment_main  = \App\TransactionPayment::where("transaction_id",$id)->orderby("id","desc")->get();
            $payment_child = \App\Models\ParentArchive::where("tp_transaction_no",$id)->orderby("id","desc")->get();
            \DB::commit();
            // dd($child_ids);
            $logFile       = [];
            $old           = [];
            $movement      = [];
            foreach($child as $key => $i){
                $title     = ($key==0)?"Create Purchase":"Edit Purchase";
                if($key == 0){
                    $old =  $i; 
                    $new =  $old; 
                    $movement[]    = [
                        "title"         => $title,
                        "reference"     => $i->ref_no,
                        "date"          => $i->created_at->format('Y-m-d'),
                        "time"          => $i->created_at->format('h:i:s a'),
                        "description"   => "Create New Purchase with " . $new->status . " Status from Supplier Details - Supplier Name : " . (($new->contact)? $new->contact->name:"") . " - Supplier Mobile : " . (($new->contact)? $new->contact->mobile:"") ,
                        "modify"        => Purchase::compare($old,$new),
                        "price"         => $i->final_total,
                        "status"        => "Create",
                        "old_status"    => "",
                        "user_name"     => ($i->sales_person)?$i->sales_person->first_name:"",
                        "user_logo"     => ($i->sales_person)?(($i->sales_person->media)?$i->sales_person->media->display_url:""):"",
                        "log_reference" => $i->ref_number,
                        "new_bill"      => $new,
                        "bill"          => $old
                    ]; 
                }else if($key > 0){
                    $old =  $child[$key-1]; 
                    $new =  $i; 
                    if(count($child)>$key+1){
                        $movement[]    = [
                            "title"         => $title,
                            "reference"     => $i->ref_no,
                            "date"          => $i->created_at->format('Y-m-d'),
                            "time"          => $i->created_at->format('h:i:s a'),
                            "description"   => "Edit Old Purchase with " . $new->status . " Status from Supplier Details - Supplier Name : " . (($new->contact)? $new->contact->name:"") . " - Supplier Mobile : " . (($new->contact)? $new->contact->mobile:"") ,
                            "modify"        => Purchase::compare($old,$new),
                            "price"         => $i->final_total,
                            "status"        => "Edit",
                            "old_status"    => ($key>1)?"Edit":"Create",
                            "user_name"     => ($i->sales_person)?$i->sales_person->first_name:"",
                            "user_logo"     => ($i->sales_person)?(($i->sales_person->media)?$i->sales_person->media->display_url:""):"",
                            "log_reference" => $i->ref_number,
                            "new_bill"      => $new,
                            "bill"          => $old
                        ]; 
                    }else{
                        $movement[]    = [
                            "title"         => $title,
                            "reference"     => $i->ref_no,
                            "date"          => $i->created_at->format('Y-m-d'),
                            "time"          => $i->created_at->format('h:i:s a'),
                            "description"   =>  "Edit Old Purchase with " . $new->status . " Status from Supplier Details - Supplier Name : " . (($new->contact)? $new->contact->name:"") . " - Supplier Mobile : " . (($new->contact)? $new->contact->mobile:"") ,
                            "modify"        => Purchase::compare($old,$new),
                            "price"         => $i->final_total,
                            "status"        => "Edit",
                            "old_status"    => ($key>1)?"Edit":"Create",
                            "user_name"     => ($i->sales_person)?$i->sales_person->first_name:"",
                            "user_logo"     => ($i->sales_person)?(($i->sales_person->media)?$i->sales_person->media->display_url:""):"",
                            "log_reference" => $i->ref_number,
                            "new_bill"      => $new,
                            "bill"          => $old
                        ]; 
                    }
                }
            } 
           
            return $movement;
        }catch(Exception $e){
            return false;
        } 
    }
    // log compare purchase ....................................
    public static function compare($old,$new){
        $i               = 1;
        $change_details  = "Change Details: ";

        $change_details .= ($old->contact_id         != $new->contact_id)?"<br/>".$i++." - Change Purchase Supplier From (".(($old->contact)?$old->contact->name ." ". $old->contact->contact_id:"").")"." To (".(($new->contact)?$new->contact->name ." ". $new->contact->contact_id:"").")":"";  
        $change_details .= ($old->store              != $new->store)?"<br/>".$i++." - Change Purchase Store From (".(($old->storex)?$old->storex->name:"").")"." To (".(($new->storex)?$new->storex->name:"").")":"";  
        $change_details .= ($old->status             != $new->status)?"<br/>".$i++." - Change Purchase Status From (".$old->status.")"." To (".$new->status.")":"";  
        $change_details .= ($old->payment_status     != $new->payment_status)?"<br/>".$i++." - Change Purchase Payment Status From (".$old->payment_status.")"." To (".$new->payment_status.")":"";  
        $change_details .= ($old->transaction_date   != $new->transaction_date)?"<br/>".$i++." - Change Purchase Date From (".$old->transaction_date.")"." To (".$new->transaction_date.")":"";  
        $change_details .= ($old->total_before_tax   != $new->total_before_tax)?"<br/>".$i++." - Change Purchase Subtotal From (".$old->total_before_tax.")"." To (".$new->total_before_tax.")":"";  
        $change_details .= ($old->tax_id             != $new->tax_id)?"<br/>".$i++." - Change Purchase Tax Role From (".(($old->tax)?$old->tax->name:"").")"." To (".(($new->tax)?$new->tax->name:"").")":"";  
        $change_details .= ($old->tax_amount         != $new->tax_amount)?"<br/>".$i++." - Change Purchase Tax Amount From (".$old->tax_amount.")"." To (".$new->tax_amount.")":"";  
        $change_details .= ($old->discount_type      != $new->discount_type)?"<br/>".$i++." - Change Purchase Discount Type From (".$old->discount_type.")"." To (".$new->discount_type.")":"";  
        $change_details .= ($old->discount_amount    != $new->discount_amount)?"<br/>".$i++." - Change Purchase Discount Amount From (".$old->discount_amount.")"." To (".$new->discount_amount.")":"";  
        $change_details .= ($old->final_total        != $new->final_total)?"<br/>".$i++." - Change Purchase Final Amount From (".$old->final_total.")"." To (".$new->final_total.")":"";  
        $old_document                                 = $old->document;
        $document_sell                                = [];
        if ($new->document) { foreach ($new->document as $file) { array_push($document_sell,$file); } }
        $change_details .= (count($old_document)     != count($document_sell))?"<br/>".$i++." - Change Purchase Attachment From (11) To (22)":"";  
        $change_details .= ($old->shipping_details   != $new->shipping_details)?"<br/>".$i++." - Change Purchase Shipping Details From (".$old->shipping_details.")"." To (".$new->shipping_details.")":"";  
        $change_details .= ($old->additional_notes   != $new->additional_notes)?"<br/>".$i++." - Change Purchase Note From (".$old->additional_notes.")"." To (".$new->additional_notes.")":"";  
        $change_details .= ($old->created_by         != $new->created_by)?"<br/>".$i++." - Change Purchase Created By From (".$old->created_by.")"." To (".$new->created_by.")":"";  
        $change_details .= ($old->project_no         != $new->project_no)?"<br/>".$i++." - Change Purchase Project Number From (".$old->project_no.")"." To (".$new->project_no.")":"";  
        // $change_details .= ($old->refe_no            != $new->refe_no)?"<br/>".$i++." - Change Purchase Discount Type From (".$old->refe_no.")"." To (".$new->refe_no.")":"";  
        $change_details .= ($old->sup_refe           != $new->sup_refe)?"<br/>".$i++." - Change Purchase Source Reference From (".$old->sup_refe.")"." To (".$new->sup_refe.")":"";  
        $change_details .= ($old->agent_id           != $new->agent_id)?"<br/>".$i++." - Change Purchase Agent From (".$old->agent_id.")"." To (".$new->agent_id.")":"";  
        $change_details .= ($old->cost_center_id     != $new->cost_center_id)?"<br/>".$i++." - Change Purchase Cost Center From (".$old->cost_center_id.")"." To (".$new->cost_center_id.")":"";  
        $lines_old       = $old->purchase_lines;
        $lines_new       = $new->purchase_lines;
        
        $lines_old_ids    = [];            
        $lines_shared_ids = [];            
        $lines_new_ids    = [];            
        foreach($lines_old as $one_line){
            $lines_old_ids[]                          = $one_line->new_id;            
            $lines_old_item[$one_line->id]            = $one_line;            
            $lines_source_old_item[$one_line->new_id] = $one_line->id;            
        }
      
        foreach($lines_new as $one_line){
            $lines_new_ids[] = $one_line->new_id;
            if(!in_array($one_line->new_id,$lines_old_ids)){
                $change_details .= "<br/>".$i++." - Add New Line in Purchase with unit price (".$one_line->pp_without_discount.")";  
            }else{
                $lines_shared_ids[] = $one_line->new_id;
                $change_details    .= ($one_line->quantity             != $lines_old_item[$lines_source_old_item[$one_line->new_id]]->quantity)?"<br/>".$i++." - Change Purchase Line  <b> Quantity </b> That Have Product Name is ( ".(($one_line->product)?$one_line->product->name:'')." ) From (".$lines_old_item[$lines_source_old_item[$one_line->new_id]]->quantity.")"." To (".$one_line->quantity.")":"";  
                $change_details    .= ($one_line->pp_without_discount  != $lines_old_item[$lines_source_old_item[$one_line->new_id]]->pp_without_discount)?"<br/>".$i++." - Change Purchase Line  <b> Unit Price </b> That Have Product Name is ( ".(($one_line->product)?$one_line->product->name:'')." ) From (".$lines_old_item[$lines_source_old_item[$one_line->new_id]]->pp_without_discount.")"." To (".$one_line->pp_without_discount.")":"";  
                $change_details    .= ($one_line->discount_percent     != $lines_old_item[$lines_source_old_item[$one_line->new_id]]->discount_percent)?"<br/>".$i++." - Change Purchase Line  <b> Discount </b> That Have Product Name is ( ".(($one_line->product)?$one_line->product->name:'')." ) From (".$lines_old_item[$lines_source_old_item[$one_line->new_id]]->discount_percent.")"." To (".$one_line->discount_percent.")":"";  
                $change_details    .= ($one_line->purchase_price       != $lines_old_item[$lines_source_old_item[$one_line->new_id]]->purchase_price)?"<br/>".$i++." - Change Purchase Line  <b> Unit Price After Discount </b> That Have Product Name is ( ".(($one_line->product)?$one_line->product->name:'')." ) From (".$lines_old_item[$lines_source_old_item[$one_line->new_id]]->purchase_price.")"." To (".$one_line->purchase_price.")":"";  
                $change_details    .= ($one_line->quantity_returned    != $lines_old_item[$lines_source_old_item[$one_line->new_id]]->quantity_returned)?"<br/>".$i++." - Change Purchase Line  <b> Returned Quantity </b> That Have Product Name is ( ".(($one_line->product)?$one_line->product->name:'')." ) From (".$lines_old_item[$lines_source_old_item[$one_line->new_id]]->quantity_returned.")"." To (".$one_line->quantity_returned.")":"";  
                $change_details    .= ($one_line->purchase_note        != $lines_old_item[$lines_source_old_item[$one_line->new_id]]->purchase_note)?"<br/>".$i++." - Change Purchase Line  <b> Product Note </b> That Have Product Name is ( ".(($one_line->product)?$one_line->product->name:'')." ) From (".$lines_old_item[$lines_source_old_item[$one_line->new_id]]->purchase_note.")"." To (".$one_line->purchase_note.")":"";  
                $change_details    .= ($one_line->sub_unit_id          != $lines_old_item[$lines_source_old_item[$one_line->new_id]]->sub_unit_id)?"<br/>".$i++." - Change Purchase Line  <b> Product Unit </b> That Have Product Name is ( ".(($one_line->product)?$one_line->product->name:'')." ) From (".(($lines_old_item[$lines_source_old_item[$one_line->new_id]]->sub_unit)?$lines_old_item[$lines_source_old_item[$one_line->new_id]]->sub_unit->actual_name:'##').")"." To (".(($one_line->sub_unit)?$one_line->sub_unit->actual_name:"##").")":"";  
                $change_details    .= ($one_line->sub_unit_qty         != $lines_old_item[$lines_source_old_item[$one_line->new_id]]->sub_unit_qty)?"<br/>".$i++." - Change Purchase Line  <b> Product Unit Quantity </b> That Have Product Name is ( ".(($one_line->product)?$one_line->product->name:'')." ) From (".$lines_old_item[$lines_source_old_item[$one_line->new_id]]->sub_unit_qty.")"." To (".$one_line->sub_unit_qty.")":"";  
            }            
        }
        foreach($lines_old_ids as $oid){
            if( !in_array($oid,$lines_new_ids) && !in_array($oid,$lines_shared_ids)){
                $deleted         = \App\Models\ArchivePurchaseLine::find($lines_source_old_item[$oid]);
                $change_details .= "<br/>".$i++." - Delete Old Line in Purchase with unit price (".$deleted->pp_without_discount.")";  
            }
        }
       
        // $old->pattern_id                        = $new->pattern_id;   
        // $old->sub_type                          = $new->sub_type;  
        // $old->sub_status                        = $new->sub_status;  
        // $old->is_quotation                      = $new->is_quotation;  
        // $old->contact_id                        = $new->contact_id;  
        // $old->customer_group_id                 = $new->customer_group_id;  
        // $old->invoice_no                        = $new->invoice_no;  
        // $old->ref_no                            = $new->ref_no;  
        // $old->subscription_no                   = $new->subscription_no;  
        // $old->subscription_repeat_on            = $new->subscription_repeat_on; 
        // $old->delivered_to                      = $new->delivered_to;  
        // $old->transfer_parent_id                = $new->transfer_parent_id;  
        // $old->return_parent_id                  = $new->return_parent_id;  
        // $old->first_ref_no                      = $new->first_ref_no;  
        // $old->previous                          = $new->previous;  
        // $old->dis_type                          = $new->dis_type;  
        // $old->is_suspend                        = $new->is_suspend;  
        // $old->exchange_rate                     = $new->exchange_rate;  
        // $old->is_direct_sale                    = $new->is_direct_sale;  
        // $old->opening_stock_product_id          = $new->opening_stock_product_id;  
        // $old->mfg_parent_production_purchase_id = $new->mfg_parent_production_purchase_id;  
        // $old->mfg_wasted_units                  = $new->mfg_wasted_units;  
        // $old->mfg_production_cost               = $new->mfg_production_cost;  
        // $old->mfg_production_cost_type          = $new->mfg_production_cost_type;  
        // $old->mfg_is_final                      = $new->mfg_is_final;  
        // $old->due_state	                        = $new->due_state;  
        // $old->store_in                          = $new->store_in;  
        // $old->ship_amount                       = $new->ship_amount; 

        if($i == 1){
            $change_details = "";
        } 
        return $change_details;
    }
    
}
