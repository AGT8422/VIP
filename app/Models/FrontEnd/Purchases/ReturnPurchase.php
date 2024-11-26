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

class ReturnPurchase extends Model
{
    use HasFactory,SoftDeletes;
    // *** REACT FRONT-END RETURN PURCHASE *** // 
    // **1** ALL RETURN PURCHASE
    public static function getReturnPurchase($user,$request) {
        try{
            // $list             = [];
            // $business_id      = $user->business_id;
            // $returnPurchase   = \App\Transaction::where('type','purchase_return')->where("business_id",$business_id)->get();
            // if(count($returnPurchase)==0) { return false; }
            // foreach($returnPurchase as $i){ $list[] = $i; }
            // return $list;
            $list                  = [];
            $business_id           = $user->business_id;
            $return_purchase       = ReturnPurchase::allData("all",null,$business_id,$request); 
            if($return_purchase == false){ return false;}
            return $return_purchase;
        }catch(Exception $e){
            return false;
        }
    }
    // **2** CREATE RETURN PURCHASE
    public static function createReturnPurchase($user,$data) {
        try{
            $business_id             = $user->business_id;
            $data                    = ReturnPurchase::data($user,"create");
            return $data;
        }catch(Exception $e){
            return false;
        }
    }
    // **3** EDIT RETURN PURCHASE
    public static function editReturnPurchase($user,$data,$id) {
        try{
            $business_id             = $user->business_id;
            $purchase                = ReturnPurchase::allData(null,$id,$business_id,null,"main","edit");
            if(!$purchase){ return false; }
            $data                    = ReturnPurchase::data($user,"edit",$id);
            $list["requirement"]     = $data;
            $list["info"]            = $purchase;
            return $list;
        }catch(Exception $e){
            return false;
        } 
    }
    // **4** STORE RETURN PURCHASE
    public static function storeReturnPurchase($user,$request) {
        try{
            \DB::beginTransaction();
            $output              = ReturnPurchase::createNewReturnPurchase($user,$request);
            if($output == false){ return false; } 
            \DB::commit();
            return $output;
        }catch(Exception $e){
            return false;
        }
    }
    // **5** UPDATE RETURN PURCHASE
    public static function updateReturnPurchase($user,$request,$id) {
        try{
            \DB::beginTransaction(); 
            $output              = ReturnPurchase::updateOldReturnPurchase($user,$request,$id);
            \DB::commit();
            return $output;
        }catch(Exception $e){
            return false;
        }
    }
    // **6** ENTRY RETURN PURCHASE
    public static function entryReturnPurchase($user,$data,$id) {
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
    // **7** ATTACH RETURN PURCHASE
    public static function attachReturnPurchase($user,$data,$id) {
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
    // **8** VIEW RETURN PURCHASE
    public static function viewReturnPurchase($user,$data,$id) {
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
                        "bill_store_name"            => $object['store'],
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
                        $statuses            = $productUtil->orderStatuses();
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
    // **9** PRINT RETURN PURCHASE
    public static function printReturnPurchase($user,$data,$id) {
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
    // **10** MAP RETURN PURCHASE
    public static function mapReturnPurchase($user,$data,$id) {
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
    // **11** DELETE RETURN PURCHASE
    public static function deleteReturnPurchase($user,$request,$id) {
        try{
            $productUtil      = new  ProductUtil(); 
            $transactionUtil  = new  TransactionUtil(); 
            $business_id      = $user->business_id;
            $purchase_return  = \App\Transaction::where('id', $id)->where('business_id', $business_id)->where('type', 'purchase_return')->with(['purchase_lines'])->first();
            $receive          = \App\Models\RecievedPrevious::where("transaction_id",$id)->where("is_returned",1)->first();
            if (!empty($receive)) { return "OldReceived"; }
            $delete_purchase_line_ids  = \App\PurchaseLine::where('transaction_id', $purchase_return->id)->pluck('id');
            // .........................................................
            if(app('request')->input("basic")){
                $ship_main                 = \App\Models\AdditionalShipping::where("transaction_id",$purchase_return->id)->first();
                if(!empty($ship_main)){
                    $ship_item             = \App\Models\AdditionalShippingItem::where("additional_shipping_id",$ship_main->id)->get();
                    foreach($ship_item as $it){
                        $it->delete();
                    }
                    $ship_main->delete();
                }
                \App\PurchaseLine::where('transaction_id', $purchase_return->id)->whereIn('id', $delete_purchase_line_ids)->delete();
            }
            // .........................................................
            if (empty($purchase_return->return_parent_id)) {
                \App\PurchaseLine::where('transaction_id', $purchase_return->id)->whereIn('id', $delete_purchase_line_ids)->delete();
            } else {
                if(app('request')->input("basic")){
                    $parent_purchase = \App\Transaction::where('id', $purchase_return->id)->where('business_id', $business_id)->where('type', 'purchase_return')->with(['purchase_lines'])->first();
                }else{
                    $parent_purchase = \App\Transaction::where('id', $purchase_return->return_parent_id)->where('business_id', $business_id)->where('type', 'purchase')->with(['purchase_lines'])->first();
                }
                $updated_purchase_lines =  ($parent_purchase)?$parent_purchase->purchase_lines:[];
                foreach ($updated_purchase_lines as $purchase_line) {
                    $productUtil->updateProductQuantity($parent_purchase->location_id, $purchase_line->product_id, $purchase_line->variation_id, $purchase_line->quantity_returned, 0, null, false);
                    $purchase_line->quantity_returned = 0;
                    $purchase_line->update();
                }
            }
            // .........................................................
            if(!empty($purchase_return)){
                if(app('request')->input("basic")){
                    $all = \App\AccountTransaction::where("return_transaction_id",$purchase_return->id)->delete();
                }else{
                    $all = \App\AccountTransaction::where("return_transaction_id",$purchase_return->id)->whereNotNull('return_transaction_id')->delete();
                }
            }
            $purchase_return->delete();
            // .........................................................
            return "true";
        }catch(Exception $e){
            return "false";
        }
    }
    // **14** Add Payment RETURN PURCHASE
    public static function addPaymentReturnPurchase($user,$data,$id) {
        try{
            \DB::beginTransaction();
            $business_id  = $user->business_id;
            $allData      = ReturnPurchase::dataPayment($user,$id);
            \DB::commit();
            return $allData;
        }catch(Exception $e){
            return false;
        }
    }
    // **15** View Payment RETURN PURCHASE
    public static function viewPaymentReturnPurchase($user,$data,$id) {
        try{
            \DB::beginTransaction();
            $business_id  = $user->business_id;
            $allData      = ReturnPurchase::viewPayment($user,$id);
            \DB::commit();
            return $allData;
        }catch(Exception $e){
            return false;
        }
    }
    // **16** GET  PURCHASE RETURN STATUS $%$%$%$%
    public static function getUpdateStatusReturnPurchase($user,$data,$id) {
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
    // **17** UPDATE  PURCHASE RETURN STATUS $%$%$%$%
    public static function updateStatusReturnPurchase($user,$data,$id) {
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
                    $ref_count                    =  $productUtil->setAndGetReferenceCount($type);
                    $reciept_no                   =  $productUtil->generateReferenceNumber($type, $ref_count);
                    $tr_recieved                  =  new \App\Models\TransactionRecieved;
                    $tr_recieved->store_id        =  $transaction->store;
                    $tr_recieved->transaction_id  =  $transaction->id;
                    $tr_recieved->business_id     =  $transaction->business_id ;
                    $tr_recieved->reciept_no      =  $reciept_no ;
                    $tr_recieved->ref_no          =  $transaction->ref_no;
                    $tr_recieved->status          =  "purchase"; 
                    $tr_recieved->is_returned     =  0; 
                    $tr_recieved->save();
                    \App\Models\StatusLive::insert_data_p($business_id,$transaction, $data['status'],$tr_recieved);
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
                        $prev->transaction_deliveries_id   =  $tr_recieved->id;
                        $prev->product_name                =  $purchase_line->product->name;  
                        $prev->line_id                     =  $purchase_line->id;  
                        $prev->is_returned                 =  0; 
                        $prev->save();
                    } 
                    $type_move                             = "plus";
                    $qty                                   = ($purchase_line->quantity);
                    // update info 
                    WarehouseInfo::update_stoct($purchase_line->product_id,$transaction->store,$qty,$transaction->business_id);
                    // movement
                    \App\MovementWarehouse::movemnet_warehouse($transaction,$purchase_line->product,$purchase_line->quantity,
                                    $transaction->store,$purchase_line,$type_move,$prev->id);
                    
                }elseif ( $transaction->status != 'received'  && $old_status == 'received' ) {
                    $currency_details = $transactionUtil->purchaseCurrencyDetails($business_id);
                    \App\Models\TransactionRecieved::where('transaction_id',$transaction->id)->delete();
                    \App\MovementWarehouse::where('transaction_id',$transaction->id)->delete();
                    \App\Models\RecievedPrevious::where('transaction_id',$transaction->id)->delete();
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
                
                \App\Models\AdditionalShipping::add_purchase_payment($transaction->id,null,null,null,$user);
                    
                $data_ship = \App\Models\AdditionalShipping::where("transaction_id",$transaction->id)->first();
                $cost=0;$without_contact =0;
                if(!empty($data_ship)){
                    $ids = $data_ship->items->pluck("id");
                    foreach($ids as $i){
                        $data_shippment   = \App\Models\AdditionalShippingItem::find($i);
                        if($data_shippment->contact_id == $data['contact_id']){ 
                            $cost += $data_shippment->total;
                        }else{
                            $without_contact += $data_shippment->total;
                        }
                        \App\Models\StatusLive::insert_data_sh($business_id,$transaction,$data_shippment,"Add Expense");
                    }
                }
            }else{
                \App\Models\Entry::where("account_transaction",$transaction->id)->delete();
                $shipping_id = \App\Models\AdditionalShipping::where("transaction_id",$transaction->id)->first();
                if(!empty($shipping_id)){
                    \App\Models\Entry::where("shipping_id",$shipping_id->id)->delete();
                }
                \App\Models\StatusLive::where("transaction_id",$transaction->id)->whereNotNull("shipping_item_id")->delete();
                \App\Models\StatusLive::where("transaction_id",$transaction->id)->where("num_serial","!=",1)->delete();
                \App\Models\StatusLive::insert_data_p($business_id,$transaction,$data['status']);
                \App\AccountTransaction::where('transaction_id',$transaction->id)->whereNotNull('additional_shipping_item_id')->delete();
            }

            if (( $old_status != 'received' ) &&  ( $data['status'] == 'received' ) ) {
                $tr             = \App\Transaction::find($transaction->id);
                $cost=0;$without_contact=0;
                $data_ship = \App\Models\AdditionalShipping::where("transaction_id",$transaction->id)->first();
                if(!empty($data_ship)){
                    $ids = $data_ship->items->pluck("id");
                    foreach($ids as $i){
                        $data_shippment   = \App\Models\AdditionalShippingItem::find($i);
                        if($data_shippment->contact_id == $data['contact_id']){ 
                            $cost += $data_shippment->amount;
                        }else{
                            $without_contact += $data_shippment->amount;
                        }
                    }
                }
                $total_expense = $cost + $without_contact; 
                if ($tr->discount_type == "fixed_before_vat"){
                    $dis = $tr->discount_amount;
                }else if ($tr->discount_type == "fixed_after_vat"){
                    $tax = \App\TaxRate::find($tr->tax_id);
                    $dis = ($tr->discount_amount*100)/(100+$tax->amount) ;
                }else if ($tr->discount_type == "percentage"){
                    $dis = ($tr->total_before_tax *  $tr->discount_amount)/100;
                }else{
                    $dis = 0;
                }
                    
                $before = \App\Models\WarehouseInfo::qty_before($transaction);
                \App\Models\ItemMove::update_itemMove($transaction,$total_expense,$before,null,$dis);
                    
            }
                
            $transactionUtil->activityLog($transaction, 'edited', $transaction_before);

            \DB::commit();
            return true;
        }catch(Exception $e){
            return false;
        }
    }
    // /************************ Return Old Purchase ********** */
    // **1** STORE RETURN OLD PURCHASE
    public static function storeReturnOldPurchase($user,$request,$id) {
        try{
            \DB::beginTransaction();
            $output              = ReturnPurchase::createNewReturnOldPurchase($user,$request,$id);
            if($output == false){ return false; } 
            \DB::commit();
            return $output;
        }catch(Exception $e){
            return false;
        }
    }
    // **2** SAVE RETURN OLD PURCHASE
    public static function createNewReturnOldPurchase($user,$request,$id) {
        try{ 
            dd($request);
            $productUtil        = new  ProductUtil(); 
            $transactionUtil    = new  TransactionUtil(); 
            $return_total       = 0;
            $business_id        = $user->business_id; 
            $return_quantities  = $request->input('returns');
            // .1........
            $purchase           = \App\Transaction::where('business_id', $business_id)->where('type', 'purchase')->with([ 'purchase_lines', 'purchase_lines.sub_unit' ])->find($id);
            $return_transaction = \App\Transaction::where('business_id', $business_id)->where('type', 'purchase_return')->where('return_parent_id', $purchase->id)->first();
            // .2........

            if (!empty($return_transaction)) {
                    $return_transaction_before                      = $return_transaction->replicate();
                    $return_transaction_data['business_id']         = $business_id;
                    $return_transaction_data['location_id']         = $purchase->location_id;
                    $return_transaction_data['type']                = 'purchase_return';
                    $return_transaction_data['status']              = 'final';
                    $return_transaction_data['contact_id']          = $purchase->contact_id;
                    $return_transaction_data['transaction_date']    = \Carbon::now();
                    $return_transaction_data['created_by']          = $user->id;
                    $return_transaction_data['discount_type']       = $request->discount_type;
                    $return_transaction_data['discount_amount']     = ($request->last_dis)?$request->last_dis:$request->last_discount;
                    $return_transaction_data['tax_id']              = $purchase->tax_id;
                    $return_transaction_data['store']               = $purchase->store;
                    $return_transaction_data['tax_amount']          = $request->tax_amount;
                    $return_transaction_data['final_total']         = $request->total_rt_purchase;
                    $return_transaction_data['currency_id']         = $request->currency_id;
                    $return_transaction_data['exchange_price']      = ($request->currency_id != null)?$request->currency_id_amount:null;
                    $return_transaction_data['amount_in_currency']  = ($request->currency_id != null)?(($request->currency_id_amount > 0)?round($request->total_rt_purchase/$request->currency_id_amount,2):0):null;
                    $return_transaction_data['return_parent_id']    = $purchase->id;
                    $return_transaction_data['cost_center_id']      = $request->cost_center_id;
                    $return_transaction_data['document']            = $transactionUtil->uploadFile($request, 'document', 'documents'); 
                    $return_transaction->update($return_transaction_data);
                    $transactionUtil->activityLog($return_transaction, 'edited', $return_transaction_before);

            } else {
                    $return_transaction_data['business_id']           = $business_id;
                    $return_transaction_data['location_id']           = $purchase->location_id;
                    $return_transaction_data['store']                 = $purchase->store;
                    $return_transaction_data['type']                  = 'purchase_return';
                    $return_transaction_data['status']                = 'final';
                    $type                                             = 'purchase_return';
                    $ref_count                                        = $productUtil->setAndGetReferenceCount($type,$business_id);
                    $receipt_no                                       = $productUtil->generateReferenceNumber($type, $ref_count,$business_id);
                    $return_transaction_data['ref_no']                = $receipt_no;
                    $return_transaction_data['contact_id']            = $purchase->contact_id;
                    $return_transaction_data['transaction_date']      = \Carbon::now();
                    $return_transaction_data['created_by']            = $user->id;
                    $return_transaction_data['discount_type']         = $request->discount_type;
                    $return_transaction_data['discount_amount']       = ($request->last_dis)?$request->last_dis:$request->last_discount;
                    $return_transaction_data['return_parent_id']      = $purchase->id;
                    $return_transaction_data['tax_id']                = $purchase->tax_id;
                    $return_transaction_data['tax_amount']            = $request->tax_amount;
                    $return_transaction_data['final_total']           = $request->total_rt_purchase;
                    $return_transaction_data['cost_center_id']        = $request->cost_center_id;
                    $return_transaction_data['currency_id']           = $request->currency_id;
                    $return_transaction_data['exchange_price']        = ($request->currency_id != null)?$request->currency_id_amount:null;
                    $return_transaction_data['amount_in_currency']    = ($request->currency_id != null)?(($request->currency_id_amount > 0)?round($request->total_rt_purchase/$request->currency_id_amount,2):0):null;
                    $return_transaction_data['document']              = $transactionUtil->uploadFile($request, 'document', 'documents'); 
                    $return_transaction                               = \App\Transaction::create($return_transaction_data);
                    $archive                                          = \App\Models\ArchiveTransaction::save_parent($return_transaction,"create");
                    $transactionUtil->activityLog($return_transaction, 'added');
            }
            $archive        =  \App\Models\ArchiveTransaction::save_parent($return_transaction,"edit");
            $purchase_lines = \App\PurchaseLine::where("transaction_id",$return_transaction->id)->get();
            $type = 0;
            foreach ($purchase->purchase_lines as  $purchase_line) {
                $old_return_qty  = $purchase_line->quantity_returned;
                $return_quantity = !empty($return_quantities[$purchase_line->id]) ? $productUtil->num_uf($return_quantities[$purchase_line->id]) : 0;
                $multiplier      = 1;
                if (!empty($purchase_line->sub_unit->base_unit_multiplier)) {
                    $multiplier      = $purchase_line->sub_unit->base_unit_multiplier;
                    $return_quantity = $return_quantity * $multiplier;
                }
                $purchase_line->quantity_returned = $return_quantity;
                    
                /**
                 *.... here don't update returned quantity  
                 * ... just in recieved page ............
                 */

                $pr                        = \App\PurchaseLine::find($purchase_line->id);
                $pr->quantity_returned     = $return_quantity;
                $pr->bill_return_price     = $request->products[$purchase_line->id]["unit_price_"];
                $pr->save();

                $return_total             += $request->products[$purchase_line->id]["unit_price_"] * $purchase_line->quantity_returned;
            }
            $purchase_lines        = \App\PurchaseLine::where("transaction_id",$return_transaction->id)->get();
            $return_total_inc_tax  = $return_total + $request->input('tax_amount');
            foreach($purchase_lines as $it){ \App\Models\ArchivePurchaseLine::save_purchases($archive , $it); }
            \App\AccountTransaction::return_purchase($purchase,$request->last_discount,$request->total_rt_purchase,$request->sub_total_rt_purchase,$request->tax_amount);
            //.. expense .. 
            $type="PReturn";
            \App\Models\Entry::create_entries($return_transaction,$type,null,null,null,null,$return_transaction->id);
            \App\Models\StatusLive::insert_data_rp($business_id,$purchase,"Return Purchase",$return_total_inc_tax);

            $return_transaction_data = [
                'total_before_tax' => $return_total,
                'final_total'      => $return_total_inc_tax,
                'tax_amount'       => $request->input('tax_amount'),
                'tax_id'           => $purchase->tax_id,
                'cost_center_id'   => $request->input('cost_center_id')
            ];

            if (empty($request->input('ref_no'))) {
                //Update reference count
                $ref_count                         = $transactionUtil->setAndGetReferenceCount('purchase_return',$business_id);
                $return_transaction_data['ref_no'] = $transactionUtil->generateReferenceNumber('purchase_return', $ref_count,$business_id);
            }

            $transactionUtil->updatePaymentStatus($return_transaction->id, $return_transaction->final_total);
           
    
            return true;
        }catch(Exception $e){
            return false;
        }
    }
    // **3** update RETURN OLD PURCHASE
    public static function updateReturnOldPurchase($user,$request,$id) {
        try{
            \DB::beginTransaction();
            $output              = ReturnPurchase::updateOldReturnOldPurchase($user,$request,$id);
            if($output == false){ return false; } 
            \DB::commit();
            return $output;
        }catch(Exception $e){
            return false;
        }
    }
    // **4** Update RETURN OLD PURCHASE
    public static function updateOldReturnOldPurchase($user,$request,$id) {
        try{ 
            dd($request);
            // return $output;
        }catch(Exception $e){
            return false;
        }
    }
    // **5** Create RETURN OLD PURCHASE
    public static function createReturnOldPurchase($user,$request,$id) {
        try{ 
            dd($request);
            // return $output;
        }catch(Exception $e){
            return false;
        }
    }
    // **6** Edit RETURN OLD PURCHASE
    public static function editReturnOldPurchase($user,$request,$id) {
        try{ 
            dd($request);
            // return $output;
        }catch(Exception $e){
            return false;
        }
    }

    // ****** MAIN FUNCTIONS 
    // **1** CREATE RETURN PURCHASE
    public static function createNewReturnPurchase($user,$request) {
       try{
            $productUtil    = new  ProductUtil(); 
            $transactionUtil= new  TransactionUtil(); 
            $business_id    = $user->business_id;
            $user_id        = $user->id;
            $input_data     = $request->only([ 
                                'location_id', 
                                'transaction_date', 
                                'final_total',
                                'total_finals_',
                                'status',
                                'ref_no',
                                'cost_center_id',
                                'store_id',
                                'discount_amount2',
                                'discount_type',
                                'currency_id',
                                'currency_id_amount',
                                'tax_id', 
                                'tax_amount2', 
                                'contact_id',
                                'sup_ref_no',
                                'shipping_details',
                                'additional_notes'
                            ]);

            $input_data['store']                = $input_data['store_id'];
            $input_data['type']                 = 'purchase_return';
            $input_data['tax_amount']           = $input_data['tax_amount2'];
            $input_data['business_id']          = $business_id;
            $input_data['created_by']           = $user_id;
            $input_data['transaction_date']     = $input_data['transaction_date'];
            $input_data['total_before_tax']     = $input_data['total_finals_'] - $input_data['tax_amount2'];
            $input_data['sup_refe']             = $input_data['sup_ref_no'];
            $input_data['ship_amount']          = 0;
            $input_data['payment_status']       = 2;
            $input_data['currency_id']          = $input_data['currency_id'];
            $input_data['exchange_price']       = ($input_data['currency_id']!= null && $input_data['currency_id_amount'] != 0)?($input_data['currency_id_amount']):null;
            $input_data['amount_in_currency']   = ($input_data['currency_id']!= null && $input_data['currency_id_amount'] != 0)?($input_data['final_total'] / $input_data['currency_id_amount']):null ;
            $input_data['discount_amount']      = $input_data['discount_amount2'];
            // ....................................................................
            $ref_count = $productUtil->setAndGetReferenceCount('purchase_return',$business_id);
            //Generate reference number
            if (empty($input_data['ref_no'])) {
                $input_data['ref_no'] = $productUtil->generateReferenceNumber('purchase_return', $ref_count,$business_id);
            }
            // ....................................................................
            $document_purchase = [];
            if ($request->hasFile('document_purchase')) {
                $new_id = 1;
                foreach ($request->file('document_purchase') as $file) {
                    $file_name =  'public/uploads/documents/'.time().'_'.$new_id++.'.'.$file->getClientOriginalExtension();
                    $file->move('public/uploads/documents',$file_name);
                    array_push($document_purchase,$file_name);
                }
            }
            // ....................................................................
            $input_data['document'] = json_encode($document_purchase);
            $products               = $request->input('products');
            $sub_total_rt_purchase  = 0;
            if (!empty($products)) {
                $product_data                      = [];
                $purchase_return                   = \App\Transaction::create($input_data);
                $archive                           = \App\Models\ArchiveTransaction::save_parent($purchase_return,"create");
                $purchase_return->return_parent_id = $purchase_return->id;
                $purchase_return->save();
                foreach ($products as $key => $product) {
                    $unit_price                    = $productUtil->num_uf($product['unit_price_before_dis_exc']);
                    $unit_price_after_dis_exc      = $productUtil->num_uf($product['unit_price_after_dis_exc']);
                    $unit_price_after_dis_inc      = $productUtil->num_uf($product['unit_price_after_dis_inc']);
                    $return_line = [
                        'product_id'             => $product['product_id'],
                        'store_id'               => $input_data['store_id'],
                        'variation_id'           => $product['variation_id'],
                        'quantity'               => $productUtil->num_uf($product['quantity']),
                        'pp_without_discount'    => $unit_price,
                        'purchase_price'         => $unit_price_after_dis_exc,
                        'bill_return_price'      => $unit_price_after_dis_exc,
                        'discount_percent'       => $product["discount_percent_return"],  
                        'purchase_price_inc_tax' => $unit_price_after_dis_inc,
                        'quantity_returned'      => $productUtil->num_uf($product['quantity']),
                        'lot_number'             => !empty($product['lot_number']) ? $product['lot_number'] : null,
                        'exp_date'               => !empty($product['exp_date']) ? $productUtil->uf_date($product['exp_date']) : null
                    ];
                    $sub_total_rt_purchase += ($product['quantity']*$unit_price_after_dis_exc) ;
                    $product_data[]         = $return_line;
                }
                $purchase_return->purchase_lines()->createMany($product_data);
                $purchaseLines = \App\PurchaseLine::where("transaction_id",$purchase_return->id)->get();
                foreach($purchaseLines as $it){
                    \App\Models\ArchivePurchaseLine::save_purchases($archive , $it);
                } 
            }
            if($request->status == "received" || $request->status == "final"){
                \App\AccountTransaction::return_purchase($purchase_return,$input_data['discount_amount2'],$request->total_finals_,$sub_total_rt_purchase,$input_data['tax_amount2']);
            }
            // ....................................................................
            $additional_inputs = $request->only([
                                    'contact_id',
                                    'shipping_amount',
                                    'shipping_vat',
                                    'shipping_total',
                                    'shipping_account_id',
                                    'shiping_text',
                                    'shiping_date',
                                    'shipping_contact_id',
                                    'shipping_cost_center_id',
                                    'cost_center_id'
                                ]);
            $document_expense = [];
            if ($request->hasFile('document_expense')) {
                $i =1;
                foreach ($request->file('document_expense') as $file) {
                    $file_name =  'public/uploads/documents/'.time().'_'.$i++.'.'.$file->getClientOriginalExtension();
                    $file->move('public/uploads/documents',$file_name);
                    array_push($document_expense,$file_name);
                }
            }
            // ....................................................................
            if($request->shipping_amount != null){
                \App\Models\AdditionalShipping::add_purchase($purchase_return->id,$additional_inputs,$document_expense,NULL,NULL,NULL,NULL,NULL,$user);
            }
            if($request->status == "received" || $request->status == "final"){
                if($request->shipping_amount != null){
                     \App\Models\AdditionalShipping::add_purchase_payment($purchase_return->id,null,null,1,$user);
                }
                //..........................................................................
                //..........................................................................
                $type="PReturn";
                \App\Models\Entry::create_entries($purchase_return,$type,null,null,null,null,$purchase_return->id);
                
                $entry    = \App\Models\Entry::orderBy("id","desc")->where('account_transaction',$purchase_return->id)->first();
                if(!empty($entry)){
                    $accountTransaction = \App\AccountTransaction::where("transaction_id",$purchase_return->id)->get();
                    foreach($accountTransaction as $it){
                        $it->entry_id = ($entry)? $entry->id:null;
                        $it->update();
                    }
                }
            }
            if($request->status == "received"  ){
                $prc_lines = \App\PurchaseLine::where("transaction_id",$purchase_return->id)->get();
                foreach($prc_lines as $it){
                    $price  = \App\PurchaseLine::orderby("id","desc")->where("product_id",$it->product_id)->first();
                    \App\Models\WarehouseInfo::update_stoct($it->product_id,$it->store_id,$it->quantity*-1,$it->transaction->business_id);
                    
                    $prev                  =  new \App\Models\RecievedPrevious;
                    $prev->product_id      =  $it->product_id;
                    $prev->store_id        =  $it->store_id;
                    $prev->business_id     =  $it->transaction->business_id ;
                    $prev->product_name    =  $it->product->name ;
                    $prev->transaction_id  =  $it->transaction->id;
                    $prev->unit_id         =  $it->product->unit->id;
                    $prev->total_qty       =  $it->quantity;
                    $prev->current_qty     =  $it->quantity;
                    $prev->remain_qty      =  0;
                    $prev->line_id         =  $it->id;  
                    $prev->is_returned     =  1;  
                    $prev->save();
                    //***  ........... eb
                    \App\MovementWarehouse::movemnet_warehouse($it->transaction,$it->product,$it->quantity,$it->store_id,$price,'minus',$prev->id);
                    //......................................................
                }
                //.....................................................................
                $tr_received      = \App\Models\TransactionRecieved::where("transaction_id",$purchase_return->id)->first();
                if(empty($tr_received)){
                    $type                         = 'purchase_receive';
                    $ref_count                    =  $productUtil->setAndGetReferenceCount($type,$business_id);
                    $receipt_no                   =  $productUtil->generateReferenceNumber($type, $ref_count,$business_id);
                    $tr_received                  =  new \App\Models\TransactionRecieved;
                    $tr_received->store_id        =  $purchase_return->store;
                    $tr_received->transaction_id  =  $purchase_return->id;
                    $tr_received->business_id     =  $purchase_return->business_id ;
                    $tr_received->reciept_no      =  $receipt_no ;
                    $tr_received->ref_no          =  $purchase_return->ref_no;
                    $tr_received->is_returned     =  1;
                    $tr_received->status          = 'Return Purchase';
                    $tr_received->save();
                }
                $prev = \App\Models\RecievedPrevious::where("transaction_id",$purchase_return->id)->get();
                foreach($prev as $pr){
                    $item_prv = \App\Models\RecievedPrevious::find($pr->id); 
                    $item_prv->transaction_deliveries_id = $tr_received->id;
                    $item_prv->update();
                }
                \App\Models\ItemMove::return_recieve($purchase_return,$tr_received->id);
            } else {
                $sellLine      = \App\PurchaseLine::where("transaction_id",$purchase_return->id)->get();
                $service_lines = [] ;
                foreach($sellLine as $it){
                    if($it->product->enable_stock == 0){   
                        $service_lines[]=$it;                             
                    }
                }
                if(count($service_lines)>0){
                        $type                         =  'trans_delivery';
                        $ref_count                    =  $productUtil->setAndGetReferenceCount($type,$business_id);
                        $receipt_no                   =  $productUtil->generateReferenceNumber($type, $ref_count,$business_id);
                        $tr_received                  =  new \App\Models\TransactionRecieved;
                        $tr_received->store_id        =  $purchase_return->store;
                        $tr_received->transaction_id  =  $purchase_return->id;
                        $tr_received->business_id     =  $purchase_return->business_id ;
                        $tr_received->reciept_no      =  $receipt_no ;
                        $tr_received->invoice_no      =  $purchase_return->ref_no;
                        //$tr_received->ref_no        =  $data->ref_no;
                        $tr_received->status          = 'Service Item';
                        $tr_received->save();

                    foreach($service_lines as $it){

                        $prev                              =  new \App\Models\RecievedPrevious;
                        $prev->product_id                  =  $it->product_id;
                        $prev->store_id                    =  $it->store_id;
                        $prev->business_id                 =  $it->transaction->business_id ;
                        $prev->transaction_id              =  $it->transaction->id;
                        $prev->unit_id                     =  $it->product->unit->id;
                        $prev->total_qty                   =  $it->quantity;
                        $prev->current_qty                 =  $it->quantity;
                        $prev->remain_qty                  =  0;
                        $prev->transaction_deliveries_id   =  $tr_received->id;
                        $prev->product_name                =  $it->product->name;
                        $prev->line_id                     =  $it->id;
                        $prev->save();
                        \App\Models\WarehouseInfo::update_stoct($it->product->id,$it->store_id,$it->quantity*-1,$it->transaction->business_id);
                        \App\MovementWarehouse::movemnet_warehouse($purchase_return,$it->product,$it->quantity,$it->store_id,$it,"minus",$tr_received->id);
                         
                    }
                    \App\Models\ItemMove::return_recieve($purchase_return,$tr_received->id);
                    \App\Transaction::update_status($purchase_return->id);
                }
            }
            // ....................................................................
            return true; 
        }catch(Exception $e){
            return false;
        }
    }
    // **2** UPDATE RETURN PURCHASE
    public static function updateOldReturnPurchase($user,$request,$id) {
       try{
            $productUtil     = new ProductUtil(); 
            $transactionUtil = new TransactionUtil(); 
            $business_id     = $user->business_id;
            $user_id         = $user->id;
            // ..............................
            $input_data = $request->only([
                            'location_id', 
                            'transaction_date', 
                            'final_total',
                            'status',
                            'currency_id',
                            'currency_id_amount',
                            'ref_no',
                            'cost_center_id',
                            'store_id',
                            'discount_amount',
                            'discount_amount2',
                            'discount_type',
                            'tax_id', 
                            'tax_amount2', 
                            'contact_id',
                            'sup_ref_no',
                            'shipping_details',
                            'additional_notes'
                        ]);
            if (!empty($request->input('ref_no'))) {
                $input_data['ref_no'] = $request->input('ref_no');
            }
            $input_data['transaction_date']     = $input_data['transaction_date'] ;
            $input_data['total_before_tax']     = $productUtil->num_uf($input_data['final_total']) - $productUtil->num_uf($input_data['tax_amount2']);
            $input_data['currency_id']          = $input_data['currency_id'];
            $input_data['exchange_price']       = $input_data['currency_id_amount'];
            $input_data['amount_in_currency']   = ($input_data['currency_id_amount']!= 0)? $input_data['final_total'] / $input_data['currency_id_amount'] :0;
            $input_data['discount_amount']      = $input_data['discount_amount'] ;
            // ..............................
            
            $document_purchase = [];
            if ($request->hasFile('document_purchase')) {
                $i = 1;
                foreach ($request->file('document_purchase') as $file) {
                    $file_name =  'public/uploads/documents/'.time().'_'.$i++.'.'.$file->getClientOriginalExtension();
                    $file->move('public/uploads/documents',$file_name);
                    array_push($document_purchase,$file_name);
                }
            }
            if(json_encode($document_purchase)!="[]"){
                $input_data['document']         = json_encode($document_purchase);
            }
            // ..............................
            $products            = $request->input('products');
            $purchase_return_id  = $request->input('purchase_return_id');
            $purchase_return     = \App\Transaction::where('business_id', $business_id)->where('type', 'purchase_return')->find($purchase_return_id);
            $archive             = \App\Models\ArchiveTransaction::save_parent($purchase_return,"edit");
            $purchaseLines       = \App\PurchaseLine::where("transaction_id",$purchase_return->id)->get();
            foreach($purchaseLines as $it){
                \App\Models\ArchivePurchaseLine::save_purchases($archive,$it);
            }
            $old_return     = $purchase_return->replicate();
            $old_lines      = \App\PurchaseLine::where("transaction_id",$purchase_return->id)->get();
            $lines_purchase = [];
            foreach($old_lines as $ele){
                $lines_purchase[$ele->id] =  $ele->replicate();
            }
            $sub_total_rt_purchase = 0;
            // ..............................
            if (!empty($products)) {
                $product_data           = [];
                $new_id                 = [];
                $updated_purchase_lines = [];
                foreach ($products as $product) {
                    $unit_price               = $productUtil->num_uf($product['unit_price_before_dis_exc']);
                    $unit_price_after_dis_exc = $productUtil->num_uf($product['unit_price_after_dis_exc']);
                    $unit_price_after_dis_inc = $productUtil->num_uf($product['unit_price_after_dis_inc']);
                    if (!empty($product['purchase_line_id'])) {
                        $return_line              = \App\PurchaseLine::find($product['purchase_line_id']);
                        $updated_purchase_lines[] = $return_line->id;
                    } else {
                        $return_line = new \App\PurchaseLine([
                            'product_id'     => $product['product_id'],
                            'variation_id'   => $product['variation_id'],
                            'transaction_id' => $purchase_return_id,
                            'quantity'       => $productUtil->num_uf($product['quantity'])
                        ]);
                    }
                    $sub_total_rt_purchase              += ($product['quantity']*$unit_price_after_dis_exc) ;
                    $return_line->store_id               = $request->store_id;
                    $return_line->quantity               = $productUtil->num_uf($product['quantity']);
                    $return_line->purchase_price         = $unit_price_after_dis_exc;
                    $return_line->pp_without_discount    = $unit_price;
                    $return_line->purchase_price_inc_tax = $unit_price_after_dis_inc;
                    $return_line->discount_percent       = $product['discount_percent_return'];
                    $return_line->bill_return_price      = $unit_price_after_dis_exc;
                    $return_line->quantity_returned      = $productUtil->num_uf($product['quantity']);
                    $return_line->lot_number             = !empty($product['lot_number']) ? $product['lot_number'] : null;
                    $return_line->exp_date               = !empty($product['exp_date']) ? $productUtil->uf_date($product['exp_date']) : null;
                    if (!empty($product['purchase_line_id'])) {
                        $return_line->update();
                    } else {
                        $return_line->save();
                        $new_id[] = $return_line->id;
                    }
                    $product_data[] = $return_line;
                }
                $purchase_return->update($input_data);
                
                //If purchase line deleted add return quantity to stock
                $deleted_purchase_lines = \App\PurchaseLine::where('transaction_id', $purchase_return_id)->whereNotIn('id', $updated_purchase_lines)->whereNotIn('id',$new_id)->get();
                \App\PurchaseLine::where('transaction_id', $purchase_return_id)->whereNotIn('id', $updated_purchase_lines)->whereNotIn('id',$new_id)->delete();
                if($request->status == "received" && $old_return->status != "received" ){
                    $prc_lines = \App\PurchaseLine::where("transaction_id",$purchase_return->id)->get();
                    foreach($prc_lines as $it){
                        $price  = \App\PurchaseLine::orderby("id","desc")->where("product_id",$it->product_id)->first();
                        \App\Models\WarehouseInfo::update_stoct($it->product_id,$it->store_id,$it->quantity*-1,$it->transaction->business_id);
                        
                        $prev                  =  new \App\Models\RecievedPrevious;
                        $prev->product_id      =  $it->product_id;
                        $prev->store_id        =  $it->store_id;
                        $prev->business_id     =  $it->transaction->business_id ;
                        $prev->product_name    =  $it->product->name ;
                        $prev->transaction_id  =  $it->transaction->id;
                        $prev->unit_id         =  $it->product->unit->id;
                        $prev->total_qty       =  $it->quantity;
                        $prev->current_qty     =  $it->quantity;
                        $prev->remain_qty      =  0;
                        $prev->line_id         =  $it->id;  
                        $prev->is_returned     =  1;  
                        $prev->save();
                        //***  ........... eb
                        \App\MovementWarehouse::movemnet_warehouse($it->transaction,$it->product,$it->quantity,$it->store_id,$price,'minus',$prev->id);
                        //......................................................
                    }
                    //.....................................................................
                    $tr_received      = \App\Models\TransactionRecieved::where("transaction_id",$purchase_return->id)->first();
                    if(empty($tr_received)){
                        $type                         =  'purchase_receive';
                        $ref_count                    =  $productUtil->setAndGetReferenceCount($type,$business_id );
                        $receipt_no                   =  $productUtil->generateReferenceNumber($type, $ref_count,$business_id );
                        $tr_received                  =  new \App\Models\TransactionRecieved;
                        $tr_received->store_id        =  $purchase_return->store;
                        $tr_received->transaction_id  =  $purchase_return->id;
                        $tr_received->business_id     =  $purchase_return->business_id ;
                        $tr_received->reciept_no      =  $receipt_no ;
                        $tr_received->ref_no          =  $purchase_return->ref_no;
                        $tr_received->is_returned     =  1;
                        $tr_received->status          = 'Return Purchase';
                        $tr_received->save();
                    }
                    $prev  = \App\Models\RecievedPrevious::where("transaction_id",$purchase_return->id)->get();
                    foreach($prev as $pr){
                        $item_prv = \App\Models\RecievedPrevious::find($pr->id); 
                        $item_prv->transaction_deliveries_id = $tr_received->id;
                        $item_prv->update();
                    }
                    \App\Models\ItemMove::return_recieve($purchase_return,$tr_received->id);
                } else if($request->status == "received" && $old_return->status == "received" ){
                    $tr_received = \App\Models\TransactionRecieved::where("transaction_id",$purchase_return->id)->first();
                    \App\Models\ItemMove::return_recieve_update($tr_received,$purchase_return,$tr_received->id);
                }

                if(($request->status != "received" && $request->status != "final") && ($old_return->status == "final"  )){
                    $all = \App\AccountTransaction::where('transaction_id',$purchase_return_id)->get();
                    foreach($all as $i){
                        $i->delete();
                    }   
                    \App\Models\Entry::delete_entries($purchase_return_id);
                    \App\Models\ItemMove::delete_move($purchase_return_id);
                    \App\Models\TransactionRecieved::where('transaction_id',$purchase_return_id)->delete();
                    \App\MovementWarehouse::where('transaction_id',$purchase_return_id)->delete();
                    \App\Models\RecievedPrevious::where('transaction_id',$purchase_return_id)->delete();
                    $shipping_id = \App\Models\AdditionalShipping::where("transaction_id",$purchase_return_id)->first();
                    if(!empty($shipping_id)){ \App\Models\Entry::where("shipping_id",$shipping_id->id)->delete(); }
                    \App\Models\Entry::where("account_transaction",$purchase_return_id)->delete();
                    \App\Models\StatusLive::where("transaction_id",$purchase_return_id)->whereNotNull("shipping_item_id")->delete();
                    \App\Models\StatusLive::where("transaction_id",$purchase_return_id)->where("num_serial","!=",1)->delete();
                    \App\Models\StatusLive::insert_data_p($business_id,$purchase_return,$request->status);
                    \App\AccountTransaction::where('transaction_id',$purchase_return_id)->whereNotNull('additional_shipping_item_id')->delete();
                }   
                //update payment status
                $transactionUtil->updatePaymentStatus($purchase_return_id, $purchase_return->final_total);
            }
            // ..............................
            $additional_inputs = $request->only([
                                    'contact_id',
                                    'shipping_amount',
                                    'shipping_vat',
                                    'shipping_total',
                                    'shipping_account_id',
                                    'shiping_text',
                                    'shiping_date',
                                    'additional_shipping_item_id',
                                    'old_shipping_amount',
                                    'old_shipping_vat',
                                    'old_shipping_total',
                                    'old_shipping_account_id',
                                    'old_shiping_text',
                                    'old_shiping_date',
                                    'old_shipping_contact_id',
                                    'shipping_contact_id',
                                    'old_shipping_cost_center_id',
                                    'cost_center_id'
                                ]);
            $document_expense = $request->old_document??[];
            if ($request->hasFile('document_expense')) {
                $id = 1;
                foreach ($request->file('document_expense') as $file) {
                    $file_name =  'public/uploads/documents/'.time().'_'.$id++.'.'.$file->getclientoriginalextension();
                    $file->move('public/uploads/documents',$file_name);
                    array_push($document_expense,$file_name);
                }
            } 
            if(($request->status == "received" || $request->status == "final")){
                \App\Models\AdditionalShipping::update_purchase($purchase_return->id,$additional_inputs,$document_expense,null,null,$user);
                \App\Models\AdditionalShipping::add_purchase_payment($purchase_return->id,null,null,1,$user);
                \App\AccountTransaction::update_return_purchase($purchase_return,$input_data['discount_amount2'],$request->total_finals_,$sub_total_rt_purchase,$input_data['tax_amount2'],$old_return,$user);
            }
            if( ($request->status == "received" || $request->status == "final") && ($old_return->status != "received" && $old_return->status != "final" )){
                $type="PReturn";
                \App\Models\Entry::create_entries($purchase_return,$type,null,null,null,null,$purchase_return->id,$user->id);
                
                $entry    = \App\Models\Entry::orderBy("id","desc")->where('account_transaction',$purchase_return->id)->first();
                if(!empty($entry)){
                    $accountTransaction = \App\AccountTransaction::where("transaction_id",$purchase_return->id)->get();
                    foreach($accountTransaction as $it){
                        $it->entry_id = ($entry)? $entry->id:null;
                        $it->update();
                    }
                }
            } 
            return true; 
        }catch(Exception $e){
            return false;
        }
    }
    // **3** GET RETURN PURCHASE
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
                $count        = \App\Transaction::where("type","purchase_return")->count();
                $totalPages   = ceil($count / $limit);
                $prevPage     = $page > 1 ? $page - 1 : null;
                $nextPage     = $page < $totalPages ? $page + 1 : null;
                // ....................END....
                $purchases           = \App\Transaction::where("business_id",$business_id)->where("type","purchase_return")->skip($skpP)->limit($limit)->get();
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
                    $list[] = [
                        "id"                 => $ie->id,
                        "store"              => ($ie->warehouse != null)?($ie->warehouse->name):"",
                        "location"           => ($ie->location != null)?$ie->location->name:"",
                        "type"               => $ie->type,
                        "status"             => $ie->status,
                        "payment_status"     => $ie->payment_status,
                        "contact"            => ($ie->contact != null)?$ie->contact->name:"",
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
                $purchase    = \App\Transaction::find($id);
                $business    = \App\Business::find($purchase->business_id);
                if(empty($purchase)){ return false; }
                $product_list   = [];
                $lines          = [];
                $lines_payments = [];
                $items          = \App\PurchaseLine::where("transaction_id",$id)->get();
                $payments       = \App\TransactionPayment::where("transaction_id",$id)->get();
                foreach($items as $ii){
                    $product_list[]              = $ii->product_id;
                    $lines[] = [
                        "id"                     => $ii->id,
                        "store_id"               => ($ii->warehouse!=null)?$ii->warehouse->id:"",  
                        "storeName"              => ($ii->warehouse!=null)?$ii->warehouse->name:""  ,  
                        "product_id"             => ($view == "edit")?$ii->product->id:$ii->product, 
                        "transaction_id"         => ($ii->transaction!=null)?$ii->transaction->id:"", 
                        "reference"              => ($ii->transaction!=null)?$ii->transaction->ref_no:"", 
                        "variation_id"           => $ii->variation_id,
                        "quantity"               => $ii->quantity,
                        "pp_without_discount"    => round($ii->pp_without_discount,2),
                        "discount_percent"       => round($ii->discount_percent,2),
                        "purchase_price"         => round($ii->purchase_price,2),
                        "purchase_price_inc_tax" => round($ii->purchase_price_inc_tax,2),
                        "item_tax"               => round($ii->item_tax,2),
                        "tax"                    => $purchase->tax,
                        "tax_id"                 => ($ii->line_tax!=null)?$ii->line_tax->id:"",
                        "taxName"                => ($ii->line_tax!=null)?($ii->line_tax->name):"",
                        "quantity_returned"      => round($ii->quantity_returned,2),
                        "purchase_note"          => $ii->purchase_note,
                        "order_id"               => $ii->order_id,
                        "mfg_date"               => $ii->mfg_date,
                        "exp_date"               => $ii->exp_date,
                        
                    ];
                }
                $paid_amount = 0;
                foreach($payments as $iei){
                    $user_id                       = \App\Models\User::find($iei->created_by);
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
                $allData[] = [
                    "id"                 => $purchase->id,
                    "store"              => ($purchase->warehouse != null)?$purchase->warehouse->id:"",
                    "storeName"          => ($purchase->warehouse != null)?$purchase->warehouse->name:"",
                    // "location"           => ($purchase->location != null)?(($main!=null)?$purchase->location->id:($purchase->location->name)):"",
                    "location"           => ($view == "edit")?$purchase->location->id:$purchase->location, 
                    "type"               => $purchase->type,
                    "status"             => $purchase->status,
                    "payment_status"     => $purchase->payment_status,
                    // "contact"            => ($purchase->contact != null)?(($main!=null)?$purchase->contact->id:($purchase->contact->name)):"",
                    "contact"            => ($view == "edit")?$purchase->contact->id:$purchase->contact, 
                    "reference_no"       => $purchase->ref_no,
                    "project_no"         => $purchase->project_no,
                    "date"               => $purchase->transaction_date,
                    "tax"                => ($purchase->tax != null)?$purchase->tax->id:"",
                    "taxName"            => ($purchase->tax != null)?$purchase->tax->name:"",
                    "tax_amount"         => round($purchase->tax_amount,2),
                    "discount_type"      => $purchase->discount_type,
                    "discount_amount"    => round($purchase->discount_amount,2),
                    "sub_total"          => round($purchase->total_before_tax,2),
                    "final_total"        => round($purchase->final_total,2),
                    "additional_notes"   => $purchase->additional_notes,
                    "shipping_details"   => $purchase->shipping_details,
                    "created_by"         => (!empty($user))?(($main!=null)?$user->id:($user->first_name)):"",
                    "source_reference"   => $purchase->sup_refe,
                    // "agent_id"           => (!empty($agent))?(($main!=null)?$agent->id:($agent->first_name)):"",
                    // "pattern"            => $purchase->pattern_id,
                    "attachment"         => $purchase->document ,
                    "tax_main"           => $purchase->tax,
                    "recieved_status"    => $rec_status,
                    "currency_id"        => $purchase->currency_id,
                    "amount_in_currency" => $purchase->amount_in_currency,
                    "exchange_price"     => $purchase->exchange_price,
                    "list"               => $lines,
                    "payment_due"        => $due-$paid_amount,
                    "payments"           => $lines_payments,
                    "additional"         => $purchase->additional_shipings,
                    "activities"         => $activities,
                    "add_currency"       => $purchase->currency,
                    "main_currency"      => $business->currency,

                ];
                
            }
            return $allData; 
        }catch(Exception $e){
            return false;
        }
    }
    // ****** Received
    // **1** CREATE RETURN PURCHASE
    public static function createNewReturnPurchaseReceived($user,$request) {
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
            $type            = 1;
            $sub_total       = $request->total_subtotal_input_id;
            $purchase_total  = $request->final_total_hidden_items;
            $shipping_total  = $request->total_final_items_;
            $transaction     = \App\Transaction::find($request->transaction_id);
            // ..............................
            $check_for_wrong = 0;         
            if ($request->purchases ) {
                $total                        =  0 ;
                $type                         =  'purchase_receive';
                $ref_count                    =  $productUtil->setAndGetReferenceCount($type,$business_id);
                $reciept_no                   =  $productUtil->generateReferenceNumber($type,$ref_count,$business_id);
                $tr_recieved                  =  new \App\Models\TransactionRecieved;
                $tr_recieved->store_id        =  $transaction->store;
                $tr_recieved->transaction_id  =  $request->transaction_id;
                $tr_recieved->business_id     =  $transaction->business_id ;
                $tr_recieved->is_returned     =  1 ;
                $tr_recieved->reciept_no      =  $reciept_no ;
                $tr_recieved->ref_no          =  $transaction->ref_no;
                $tr_recieved->date            =  $request->date;
                $tr_recieved->status          = 'Return Purchase';
                $tr_recieved->save();

                $counter = 0;
                foreach ($request->purchases as $key => $single) {
                    $tr       = \App\Transaction::where("id",$transaction->return_parent_id)->first();
                    $margin   = \App\PurchaseLine::check_transation_product_return($tr->id,$single['product_id'],$counter);
                    $diff     = $margin - $single['quantity'];
                    $product  = \App\Product::find($single['product_id']);
                    $line     = \App\PurchaseLine::where('transaction_id',$tr->id)->where('product_id',$single['product_id'])->first();
                    $counter++;
                    $store_   = ($request->stores_id[$key] != null)?$request->stores_id[$key]:$request->store_id;
                    if ($diff > 0) {
                        ReturnPurchase::receive($transaction,$product,$single['quantity'],$store_,$tr_recieved,$line);
                    }else if ($diff < 0 && $line) {
                        ($margin > 0 )? ReturnPurchase::receive($transaction,$product,$margin,$store_,$tr_recieved,$line):'';
                        ReturnPurchase::wrong_receive($transaction,$product,abs($diff),$store_,$tr_recieved,$line);
                        $check_for_wrong = 1;
                    }else if ($diff ==  0 && $line){
                        ReturnPurchase::receive($transaction,$product,$single['quantity'],$store_,$tr_recieved,$line);
                    }else {
                        ReturnPurchase::wrong_receive($transaction,$product,$single['quantity'],$store_,$tr_recieved,$line );
                        $check_for_wrong = 1;
                    }
                }
            }
            \App\Models\ItemMove::return_recieve($transaction,$tr_recieved->id);
            if( $check_for_wrong == 1){
                \App\Models\ItemMove::Wrong_recieve_return($transaction,$tr_recieved->id);
            }
            // ..............................
            return true;
        }catch(Exception $e){
            return false;
        }
    }
    // **2** UPDATE RETURN PURCHASE
    public static function updateOldReturnPurchaseReceived($user,$request,$id) {
        try{
            
            $business_id       = $user->business_id;
            $TranRed           = \App\Models\TransactionRecieved::find($id);  
            $data              = \App\Transaction::find($TranRed->transaction_id);  
            $exist_items       = $request->recieve_previous_id?$request->recieve_previous_id:[];
            $removes           = \App\Models\RecievedPrevious::where('transaction_id',$data->id)->where("transaction_deliveries_id",$id)->whereNotIn('id',$exist_items)->get();
            $TranRed->date     = $request->date;
            $TranRed->update();  
            foreach ($removes as $re) {
                $info =  \App\Models\WarehouseInfo::where('store_id',$re->store_id)->where('product_id',$re->product_id)->first();
                if ($info) {
                    $info->increment('product_qty',$re->current_qty);
                    $info->save();
                }
                \App\MovementWarehouse::where('recived_previous_id',$re->id)->delete();
                $q  =  $re->current_qty*-1;
                ReturnPurchase::update_variation($re->product_id,$q,$re->transaction->location_id);
                \App\Models\ItemMove::delete_recieve($data->id,$re->id,$re);
                $re->delete();
            }
            // ..........................
            $wrong_id        = $request->recieved_wrong_id?$request->recieved_wrong_id:[];
            $wrongs          = \App\Models\RecievedWrong::where('transaction_id',$data->id)->where("transaction_deliveries_id",$id)->whereNotIn('id',$wrong_id)->get();
            $wrongs_return   = \App\Models\RecievedWrong::where('transaction_id',$data->id)->where("transaction_deliveries_id",$id)->whereIn('id',$wrong_id)->get();
            $check_for_wrong = 0;
            foreach ($wrongs as $re) {
                $info =  \App\Models\WarehouseInfo::where('store_id',$re->store_id)->where('product_id',$re->product_id)->first();
                if ($info) {
                    $info->increment('product_qty',$re->current_qty);
                    $info->save();
                }
                \App\MovementWarehouse::where('recieved_wrong_id',$re->id)->delete();
                $q  =  $re->current_qty*-1;
                ReturnPurchase::update_variation($re->product_id,$q,$re->transaction->location_id);
                $array_del      = [];
                $line_id        = [];
                $product_id     = [];
                $move_id        = []; 
                if(!in_array($re->product_id,$line_id)){
                    $line_id[]    = $re->product_id;
                    $product_id[] = $re->product_id;
                }
                $wrongMove = \App\Models\ItemMove::where("transaction_id",$data->id)->where("recieve_id",$re->id)->first();
                if(!empty($wrongMove)){ $move_id[] = $wrongMove->id; }
                if(!empty($wrongMove)){
                    $wrongMove->delete();
                    \App\Models\ItemMove::refresh_item($wrongMove->id,$re->product_id);
                    $move_all  = \App\Models\ItemMove::where("product_id",$re->product_id)->whereNotIn("id",$move_id)->get(); 
                    if(count($move_all)>0){
                        foreach($move_all as $key =>  $it){
                            \App\Models\ItemMove::refresh_item($it->id,$it->product_id );
                        }
                    }
                }
                $re->delete();
            }
            // ..........................
            if ($request->recieve_previous_id) {
                foreach ($request->recieve_previous_id  as $key => $pr_id) {
                    $tr                =  \App\Transaction::where("id",$data->return_parent_id)->first();
                    $prev              =  \App\Models\RecievedPrevious::find($pr_id);
                    $sum_product_id    =  \App\PurchaseLine::where("transaction_id",$tr->id)->where("product_id",$prev->product_id)->sum("quantity_returned");
                    $line              =  \App\PurchaseLine::where("transaction_id",$tr->id)->where("product_id",$prev->product_id)->first();
                    $old_store         =  $prev->store_id;
                    $old_qty           =  $prev->current_qty;
                    $diff              =  $request->recieve_previous_qty[$key] - $prev->current_qty;
                    $_store            =  $request->old_store_id[$key] ;
                    $prev->store_id    =  $request->old_store_id[$key];
                    $prev->total_qty   =  $sum_product_id;
                    $prev->current_qty =  $request->recieve_previous_qty[$key];
                    $prev->save();
                    if ($old_store ==  $request->old_store_id[$key]) {
                        \App\Models\WarehouseInfo::update_stoct($prev->product_id,$prev->store_id,$diff*-1,$data->business_id);
                    }else{
                        \App\Models\WarehouseInfo::update_stoct($prev->product_id,$prev->store_id,$request->recieve_previous_qty[$key]*-1,$data->business_id);
                        \App\Models\WarehouseInfo::update_stoct($prev->product_id,$old_store,$old_qty,$data->business_id);
                    }
                    ReturnPurchase::update_variation($prev->product_id,$diff,$prev->transaction->location_id);
                    \App\MovementWarehouse::recieve_return($pr_id,$request->recieve_previous_qty[$key],"correct",$_store,"return");
                }
            }
        
            if ($request->purchases ) {
                $data                         = \App\Transaction::find($data->id);
                $total                        = 0 ;
                $tr_recieved                  = \App\Models\TransactionRecieved::find($id);
                $tr_recieved->date            = ($request->date == null)?\Carbon::now():$request->date;
                $tr_recieved->update();
                foreach ($request->purchases as $key => $single) { 
                    $tr       =  \App\Transaction::where("id",$data->return_parent_id)->first();
                    $margin   =  \App\PurchaseLine::check_transation_product_return($tr->id,$single['product_id']);
                    $diff     =  $margin - $single['quantity'];
                    $product  =  \App\Product::find($single['product_id']);
                    $line     =  \App\PurchaseLine::where('transaction_id',$data->id)->where('product_id',$single['product_id'])->first();
                    $store_   =  ($request->stores_id[$key] != null)?$request->stores_id[$key]:$request->store_id;
                   
                    if ($diff > 0) {
                        ReturnPurchase::receive($data,$product,$single['quantity'],$store_,$tr_recieved,$line);
                    }elseif ($diff < 0 && $line) { 
                        ($margin > 0 )? ReturnPurchase::receive($data,$product,$margin,$store_,$tr_recieved,$line):''; 
                        ReturnPurchase::wrong_receive($data,$product,abs($diff),$store_,$tr_recieved);
                        $check_for_wrong = 1 ;
                    }elseif($diff ==  0 && $line){
                        ReturnPurchase::receive($data,$product,$single['quantity'],$store_,$tr_recieved,$line);
                    }else{
                        ReturnPurchase::wrong_receive($data,$product,$single['quantity'],$store_,$tr_recieved);
                        $check_for_wrong = 1 ;
                    }
                }
            }   
            // Transaction::update_status($data->id);
            \App\Models\ItemMove::return_recieve_update($id,$data,$TranRed);
            if( $check_for_wrong == 1 || (count($wrongs_return)>0)){
                \App\Models\ItemMove::Wrong_recieve_return($data,$TranRed->id);
            }
            // .......................................................  
            return true;
        }catch(Exception $e){
            return false;
        }
    }
    // **3** GET RETURN PURCHASE
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
                $count        = \App\Models\TransactionRecieved::where("business_id",$business_id)->whereHas("transaction",function($q){
                    $q->where('type','purchase_return');                                  
                });
                if($transaction_id!=null){
                     $count->where("transaction_id",$transaction_id);
                }
                $count        = $count->count();
                $totalPages   = ceil($count / $limit);
                $prevPage     = $page > 1 ? $page - 1 : null;
                $nextPage     = $page < $totalPages ? $page + 1 : null;
                // ....................END....
                $received           = \App\Models\TransactionRecieved::where("business_id",$business_id)->whereHas("transaction",function($q){
                    $q->where('type','purchase_return');                                  
                });
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
                if(empty($received)){ return false; }
                $business    = \App\Business::find($received->business_id);
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
                    $child             = \App\Models\Warehouse::product_stores($ii->product_id,$business_id); 
                    $storesQuantity    = [];  $storesQuantityCheck    = [];
                    foreach($child as $key => $items_store){ $storesQuantityCheck[$key] = 1 ; $storesQuantity[] = ["id" => $key ,"name" => $items_store["name"] , "available_qty" => $items_store["available_qty"]  ];  }
                    if(count($storesQuantity) == 0 || !isset($storesQuantityCheck[$ii->store_id])){ $storesQuantity[] = ["id" => $ii->store_id , "name" => ($ii->store)?$ii->store->name:"" , "available_qty" => $total_qty ] ;  }
                    
                    $lines[] = [
                        "id"                 => $ii->id,
                        "product_id"         => $ii->product_id,
                        "productName"        => $ii->product_name,
                        "unit_id"            => $ii->unit_id,
                        "unitName"           => ($ii->unit)?$ii->unit->actual_name:"",
                        // "total_qty"    => $ii->total_qty,
                        "current_qty"        => $ii->current_qty,
                        // "remain_qty"   => $ii->remain_qty, 
                        "store"              => $ii->store_id, 
                        "storeName"          => ($ii->store)?$ii->store->name:"", 
                        "line_id"            => $ii->line_id, 
                        "is_returned"        => $ii->is_returned, 
                        "allQuantity"        => $total_qty, 
                        "storeWithQuantity"  => $storesQuantity 
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
        $currency              = [];
        $amount                = 1;
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
                "id"        => $item->id,
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
        $row                              = 1;
        $cost_center_list                 = \App\Account::Cost_center();
        $contacts                         = \App\Contact::suppliers($user->business_id);
        $account_list                     = \App\Account::main("cash",$user->business_id);
        $list_of_prices                   = \App\Product::getListPrices($row);
        $expenses                         = \App\Account::main('Expenses',$user->business_id);
        // ..........................................................3**...
        $reference                        = \App\ReferenceCount::where("ref_type","purchase_return")->where("business_id",$user->business_id)->first();
        if(empty($reference)){ $count = 1; }else{
            $count = $reference->ref_count + 1;
        }
        $prefix                           = '';
        $business                         = \App\Business::find($user->business_id);
        $prefixes                         = $business->ref_no_prefixes;
        $prefix                          .= !empty($prefixes['purchase_return']) ? $prefixes['purchase_return'] : '';
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
                "id"        => $item->id,
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
    public static function getAllReturnPurchaseReceived($user,$request) {
        try{
            $list           = [];
            $business_id    = $user->business_id;
            $purchase       = ReturnPurchase::allReceivedData("all",null,$business_id,$request); 
            if($purchase == false){ return false;}
            return $purchase;
        }catch(Exception $e){
            return false;
        }
    }
    // **1**
    public static function getReturnPurchaseReceived($user,$request,$id) {
        try{
            $list           = [];
            $business_id    = $user->business_id;
            $purchase       = ReturnPurchase::allReceivedData("all",null,$business_id,$request,null,null,$id); 
            if($purchase == false){ return false;}
            return $purchase;
        }catch(Exception $e){
            return false;
        } 
    }
    // **2**
    public static function createReturnPurchaseReceived($user,$request) {
        try{
            $business_id             = $user->business_id;
            $data                    = ReturnPurchase::dataReceived($user);
            return $data;
        }catch(Exception $e){
            return false;
        }
    }
    // **3**
    public static function editReturnPurchaseReceived($user,$request,$id) {
        try{
            $business_id             = $user->business_id;
            $received                = ReturnPurchase::allReceivedData(null,$id,$business_id,null,"main","edit");
            if(!$received){ return false; }
            $data                    = ReturnPurchase::dataReceived($user);
            $list["requirement"]     = $data;
            $list["info"]            = $received;
            return $list;
        }catch(Exception $e){
            return false;
        } 
    }
    // **4**
    public static function saveReturnPurchaseReceived($user,$request) {
        try{
            \DB::beginTransaction();
            $business_id         = $user->business_id;
            $data["business_id"] = $business_id;
            $data["created_by"]  = $user->id;
            $output              = ReturnPurchase::createNewReturnPurchaseReceived($user,$request);
            if($output == false){ return false; } 
            \DB::commit();
            return $output;
        }catch(Exception $e){
            return false;
        }
    }
    // **5**
    public static function updateReturnPurchaseReceived($user,$request,$id) {
        try{
            \DB::beginTransaction();
            $business_id         = $user->business_id;
            $data["business_id"] = $business_id;
            $data["created_by"]  = $user->id;
            $output              = ReturnPurchase::updateOldReturnPurchaseReceived($user,$request,$id);
            \DB::commit();
            return $output;
        }catch(Exception $e){
            return false;
        }
    }
    // **6**
    public static function viewReturnPurchaseReceived($user,$request,$id) {
        try{
            \DB::beginTransaction();  
            $business_id = $user->business_id;
            $voucher     = ReturnPurchase::allReceivedData(null,$id,$business_id,null,"main","edit");
            if($voucher  == false){ return false; }
            \DB::commit();
            return $voucher;
        }catch(Exception $e){
            return false;
        }
    }
    // **7**
    public static function printReturnPurchaseReceived($user,$request,$id) {
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
    public static function attachReturnPurchaseReceived($user,$request,$id) {
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
    public static function deleteReturnPurchaseReceived($user,$request,$id) {
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
                    $productUtil->updateProductQuantity(
                        $rp->product->product_locations[0]->id,
                        $rp->product->id  ,
                        $rp->product->variations[0]->id ,
                        $rp->current_qty
                    );
              
                    \App\Models\WarehouseInfo::where("product_id",$rp->product->id)
                                                ->where("store_id",$rp->store->id)
                                                ->increment('product_qty',$rp->current_qty);
                    
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
                    $productUtil->updateProductQuantity(
                        $rp->product->product_locations[0]->id,
                        $rp->product->id  ,
                        $rp->product->variations[0]->id ,
                        $rp->current_qty
                    );
                    
                    \App\Models\WarehouseInfo::where("product_id",$rp->product->id)
                                                        ->where("store_id",$rp->store->id)
                                                        ->increment('product_qty',$rp->current_qty);
                
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
    public static function wrong_receive($data,$product,$quantity,$store_id,$tr_recieved,$line) {
        
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
        $prev->is_returned                 =  1; 
        $prev->save(); 
        // must be the same arrangement
        \App\Models\WarehouseInfo::update_stoct($product->id,$store_id,$quantity*-1,$data->business_id);
        $line =  \App\PurchaseLine::OrderBy('id','desc')->where('product_id',$product->id)->first();
        \App\MovementWarehouse::movemnet_warehouse($data,$product,$quantity,$store_id,$line,'minus',NULL,$prev->id,"received");
        //*** eb ..............................................................
        $variation_id = \App\Variation::where("product_id" , $product->id)->first();
        //.....................................................................
        $currency_details = $transactionUtil->purchaseCurrencyDetails($data->business_id);
        
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
        $prev->total_qty                   =  $line->quantity_returned;
        $prev->current_qty                 =  $quantity;
        $prev->remain_qty                  =  0;
        $prev->transaction_deliveries_id   =  $tr_recieved->id;
        $prev->product_name                =  $product->name;  
        $prev->line_id                     =  $line->id;  
        $prev->is_returned                 =  1;  
        $prev->save(); 
        \App\Models\WarehouseInfo::update_stoct($product->id,$store_id,$quantity*-1,$data->business_id);
        \App\MovementWarehouse::movemnet_warehouse($data,$product,$quantity,$store_id,$line,'minus',$prev->id,"received");
        $currency_details = $transactionUtil->purchaseCurrencyDetails($data->business_id);

    }
    public static function update_variation($id,$quantity,$location){
        $data    =  \App\VariationLocationDetails::where('product_id',$id)->where('location_id',$location)->first();
        if ($data) {
        $data->qty_available =  $data->qty_available + $quantity;
        $data->save();
        }
    }    

}
