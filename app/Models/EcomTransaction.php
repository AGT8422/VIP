<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\ReferenceCount;


class EcomTransaction extends Model
{
    use HasFactory;
    use SoftDeletes;

    
    // *1* save cart QTY
    public static function SaveCartQty($data,$client){
        try{
            // **1** if no data sended 
            if(count($data) == 0 ){
                return response([
                    "status"  => 403,
                    "message" => "Error",
                ],403);
            }
            \DB::beginTransaction();
            
            // **2** save Transaction
            $draft           = EcomTransaction::CreateCart($client);
            // **3** save Transaction Items
            $type            = "plus";
            $qty             = $data["qty"];
            $draftItem       = EcomTransaction::CreateItemCart($client,$data,$draft,$type,$qty);
            $enter_stock     =  \App\Models\WarehouseInfo::where("product_id",$data["product_id"])->sum("product_qty");
            if($draftItem == null){
                return response([
                    "status"  => 403,
                    "stock_quantity" => $enter_stock ,
                    "message" => "Out Of Stock",
                ],403);
            }
            // **4** Update Transaction Info
            $updateDraftInfo = EcomTransaction::UpdateInfoCart($client,$data,$draft);
                
            \DB::commit();
            
            return response([
                "status"  => 200,
                "message" => "Saved Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"  => 403,
                "message" => "Invalid Data",
            ],403);
        }
    }  
    // *2* update cart QTY
    public static function UpdateCartQty($data,$client) {
        try{
            // **1** if no data sended 
            if(count($data) == 0 ){
                return response([
                    "status"  => 403,
                    "message" => "Error",
                ],403);
            }
            \DB::beginTransaction();
            // **1** check Transaction
            $draft           = EcomTransaction::CreateCart($client);
            // **2** update Transaction
            $qty             = $data['qty'];
            $draftItem       = EcomTransaction::CreateItemCart($client,$data,$draft,null,$qty,"Edit");
            $enter_stock     =  \App\Models\WarehouseInfo::where("product_id",$data["product_id"])->sum("product_qty");
            if($draftItem == null){
                return response([
                    "status"  => 403,
                    "stock_quantity" => $enter_stock ,
                    "message" => "Out Of Stock",
                ],403);
            }
            // **3** Update Transaction Info
            $updateDraftInfo = EcomTransaction::UpdateInfoCart($client,$data,$draft); 
            \DB::commit();
            return response([
                "status"  => 200,
                "message" => "Updated Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"  => 403,
                "message" => "Invalid Data",
            ],403);
        }
    }
    // *3* save cart 
    public static function saveCart($data,$client){
        try{
            // **1** if no data sended 
            if(count($data) == 0 ){
                return response([
                    "status"  => 403,
                    "message" => "Error",
                ],403);
            }
            \DB::beginTransaction();
            
            // **2** save Transaction
            $draft           = EcomTransaction::CreateCart($client);
            // **3** save Transaction Items
            $type            = "plus";
            $draftItem       = EcomTransaction::CreateItemCart($client,$data,$draft,$type);
            $enter_stock     =  \App\Models\WarehouseInfo::where("product_id",$data["product_id"])->sum("product_qty");
            if($draftItem == null){
                return response([
                    "status"  => 403,
                    "stock_quantity" => $enter_stock ,
                    "message" => "Out Of Stock",
                ],403);
            }
            // **4** Update Transaction Info
            $updateDraftInfo = EcomTransaction::UpdateInfoCart($client,$data,$draft);
                
            \DB::commit();
            
            return response([
                "status"  => 200,
                "message" => "Saved Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"  => 403,
                "message" => "Invalid Data",
            ],403);
        }
    }  
    // *4* update cart
    public static function updateCart($data,$client) {
        try{
            // **1** if no data sended 
            if(count($data) == 0 ){
                return response([
                    "status"  => 403,
                    "message" => "Error",
                ],403);
            }
            \DB::beginTransaction();
            // **1** check Transaction
            $draft           = EcomTransaction::CreateCart($client);
            // **2** update Transaction
            $type            = $data['type'];
            $draftItem       = EcomTransaction::CreateItemCart($client,$data,$draft,$type);
            $enter_stock     =  \App\Models\WarehouseInfo::where("product_id",$data["product_id"])->sum("product_qty");
            if($draftItem == null){
                return response([
                    "status"  => 403,
                    "stock_quantity" => $enter_stock ,
                    "message" => "Out Of Stock",
                ],403);
            }
            // **3** Update Transaction Info
            $updateDraftInfo = EcomTransaction::UpdateInfoCart($client,$data,$draft); 
            \DB::commit();
            return response([
                "status"  => 200,
                "message" => "Updated Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"  => 403,
                "message" => "Invalid Data",
            ],403);
        }
    }
    // *5* delete cart
    public static function deleteCart($data,$client) {
        try{
            // **1** if no data sended 
            if(count($data) == 0 ){
                return response([
                    "status"  => 403,
                    "message" => "Error",
                ]);
            }
            \DB::beginTransaction();
            // **1** check Transaction
            $draft           = EcomTransaction::CreateCart($client);
            // **2** delete item
            $draftItem       = EcomTransaction::DeleteCartRow($client,$data,$draft);
            if($draftItem == null){
                return response([
                    "status"  => 403,
                    "message" => "Invalid Data",
                ],403);
            }
            // **3** Update Transaction Info
            $updateDraftInfo = EcomTransaction::UpdateInfoCart($client,$data,$draft); 
            \DB::commit();
            return response([
                "status"  => 200,
                "message" => "Deleted Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"  => 403,
                "message" => "Invalid Data",
            ],403);
        }
    }
    // *6* get tax invoice list 
    public static function TaxInvoiceList($data,$client){
        try{
            \DB::beginTransaction();
            $orders = EcomTransaction::OrderList($client,$data);
            
              
            \DB::commit();
            if($orders == null){
                return response([
                    "status"  => 403,
                    "message" => "No Previous Order",
                ],403);
            }else{
                return response([
                    "status"       => 200,
                    "orders"       => $orders,
                    "message"      => "Order Access Successfully",
                ],200);
            }
        }catch(Exception $e){
            return response([
                "status"  => 403,
                "message" => "Invalid Data",
            ],403);
        }
    }  
    // *7* save tax invoice 
    public static function saveTaxInvoice($data,$client){
        try{
            \DB::beginTransaction();
            $draft = EcomTransaction::TaxInvoice($client,$data);
            \DB::commit();
            if($draft != null){
                return response([
                    "status"  => 200,
                    "message" => "Tax Invoice Saved Successfully",
                ]);
            }else{
                return response([
                    "status"  => 403,
                    "message" => "Sorry The Card Not Found",
                ],403);
                
            }
        }catch(Exception $e){
            return response([
                "status"  => 403,
                "message" => "Invalid Data",
            ],403);
        }
    }  
    // *8* save Ecom Transaction
    public static function CreateCart($client){
        $draft            = EcomTransaction::where("created_by",$client->id)
                                            ->where("status","draft")
                                            ->where("not_finished",1)
                                            ->first();
        $account_settings = \App\Models\AccountSetting::first();
        $tax_id = (!empty($account_settings))?$account_settings->tax_id:0;
        
        if(empty($draft)){
            $business_id               = 1;
            $ref_count                 = EcomTransaction::setAndGetReferenceCount('Ecom_draft', $business_id,1);
            $invoice_no                = EcomTransaction::generateReferenceNumber('Ecom_draft', $ref_count, $business_id,"EC/DFT",1);
            $draft                     = new EcomTransaction();
            $draft->business_id        = $business_id;
            $draft->store              = 1;
            $draft->invoice_no         = $invoice_no;
            $draft->type               = "Ecom_sale";
            $draft->status             = "draft";
            $draft->contact_id         = $client->contact_id;
            $draft->total_before_tax   = 123;
            $draft->tax_id             = $tax_id;
            $draft->discount_type      = "percentage";
            $draft->discount_amount    = 0;
            $draft->tax_amount         = 12;
            $draft->created_by         = $client->id;
            $draft->final_total        = 123;
            $draft->is_quotation       = 0;
            $draft->payment_status     = 1;
            $draft->transaction_date   = \Carbon::now()->format('Y-m-d h:i:s');
            $draft->sub_status         = "";
            $draft->exchange_rate      = 1;
            $draft->is_suspend         = 0;
            $draft->is_recurring       = 0;
            $draft->recur_interval     = 1;
            $draft->recur_repetitions  = 0;
            $draft->rp_earned          = 0;
            $draft->rp_redeemed        = 0;
            $draft->rp_redeemed_amount = 0;
            $draft->packing_charge     = 0;
            $draft->round_off_amount   = 0;
            $draft->is_export          = 0;
            $draft->not_finished       = 1;
            $draft->pattern_id         = null;
            $draft->save();
        }
        return $draft;
    }
    // *9* save Ecom Transaction Items
    public static function CreateItemCart($client,$data,$draft,$type,$qty=null,$item_type=null){
        $draftItem                              =  \App\Models\EcomTransactionSellLine::where("ecom_transaction_id",$draft->id)->where("product_id",$data["product_id"])->first();
        $enter_stock                            =  \App\Models\WarehouseInfo::where("product_id",$data["product_id"])->sum("product_qty");

        if(!empty($draftItem)){
            if($qty !== null){
                // ** (=) ASSIGNMENT QUANTITY TO THE QTY REQUESTED FROM E-COMMERCE
                if($item_type!=null){
                    if($enter_stock<$qty){return null; }
                    $draftItem->quantity                        =  ($enter_stock<=$qty)?(($enter_stock>0)?$enter_stock:0):$qty;
                }else{
                    if($enter_stock<($draftItem->quantity + $qty)){return null; }
                    $draftItem->quantity                        =   ($enter_stock <= ($draftItem->quantity + $qty))?(($enter_stock>0)?$enter_stock:0):($draftItem->quantity + $qty);
                }
            }else{
                if($type == "plus"){
                    if($enter_stock<($draftItem->quantity + 1)){return null; }
                // ** (+) IF INCREASE ITEM QUANTITY
                    $draftItem->quantity                    =  ($enter_stock <= ($draftItem->quantity + 1))?(($enter_stock>0)?$enter_stock:0):($draftItem->quantity + 1);
                }else{
                // ** (-) IF DECREASE ITEM QUANTITY
                    $draftItem->quantity                    =  ($enter_stock <= ($draftItem->quantity - 1))?(($enter_stock>0)?$enter_stock:0):($draftItem->quantity - 1);
                }
            }
          
            // ** FOR CHECK IF QTY BECOME (0) DELETE ROW FROM ITEMS
            if($draftItem->quantity == 0){
                $draftItem->delete();
            }else{
                $draftItem->update();
            }
        }else{
            if($type == "plus"){
                $discount                               = 0;
                $qt                                     = (count(\App\Variation::where("product_id",$data["product_id"])->get())>0)?count(\App\Variation::where("product_id",$data["product_id"])->get()):0; 
                $variation                              = \App\Variation::where("product_id",$data["product_id"])->select(\DB::raw("SUM(default_sell_price) as price_exc"),\DB::raw("SUM(dpp_inc_tax) as price_inc"))->first();
                $product_info                           = \App\Product::find($data["product_id"]);
                $productPrice                           = \App\Models\ProductPrice::where("product_id",$data["product_id"])->where("unit_id",$product_info->unit_id)->where("name","ECM After Price")->first();
                $price_exc                              = ($qt!=0)?((!empty($productPrice))?$productPrice->default_sell_price:$variation->price_exc)/$qt:0;
                $price_inc                              = ($qt!=0)?((!empty($productPrice))?$productPrice->sell_price_inc_tax:$variation->price_inc)/$qt:0;
              
                $tax                                    = \App\TaxRate::find($draft->tax_id);
                if(!empty($tax)){ $tax_amount           = (($price_exc - $discount)  * $tax->amount) / (100); }else{ $tax_amount  = 0; }
                $enter_stock                            =  \App\Models\WarehouseInfo::where("product_id",$data["product_id"])->sum("product_qty");
                if($enter_stock > 0){
                    if($enter_stock<=$qty){return null; }
                    $draftItem                              =  new \App\Models\EcomTransactionSellLine();
                    $draftItem->ecom_transaction_id         =  $draft->id; 
                    $draftItem->product_id                  =  $data["product_id"]; 
                    $draftItem->store_id                    =  $draft->store; 
                    $draftItem->variation_id                =  $data["product_id"];
                    $draftItem->quantity                    =  ($qty != null) ? ( ($enter_stock<=$qty) ? ( ($enter_stock>0) ? $enter_stock : 0 ) : $qty ) : ( ($enter_stock<1) ? 0 : 1 );
                    $draftItem->unit_price_before_discount  =  $price_exc;
                    $draftItem->unit_price                  =  $price_exc - $discount;
                    $draftItem->store_id                    =  $draft->store;
                    $draftItem->line_discount_type          =  null;
                    $draftItem->line_discount_amount        =  $discount;
                    $draftItem->item_tax                    =  $tax_amount;
                    $draftItem->tax_id                      =  $draft->tax_id;
                    $draftItem->unit_price_inc_tax          =  ($price_exc - $discount)+$tax_amount ;
                    $draftItem->sell_line_note              =  '';
                    $draftItem->save();
                }else{
                    return null;
                }
            }else{
                return null;
            }
        }
        return $draftItem;
    }
    // *10* update Ecom Transaction Info
    public static function UpdateInfoCart($client,$data,$draft){
        $transaction             = \App\Models\EcomTransaction::find($draft->id);
        $allData                 = \App\Models\EcomTransactionSellLine::whereHas("Ecom_transaction",function($query) use($draft,$client){
                                                                            $query->where("id",$draft->id);                                                                    
                                                                            $query->where("created_by",$client->id);                                                                    
                                                                            $query->where("not_finished",1);
                                                                        })
                                                                        ->select(
                                                                            \DB::raw("SUM(quantity * unit_price) as total_before_tax")
                                                                            ,\DB::raw("SUM(quantity * unit_price_inc_tax) as final_total")
                                                                        )->first();
        $tax                     = \App\TaxRate::find($transaction->tax_id); 
        if(!empty($tax)){
            $tax_amount          = ($allData->final_total * $tax->amount) / (100 + $tax->amount);
        }else{
            $tax_amount          = 0;
        }
        $draft->total_before_tax = $allData->total_before_tax ;
        $draft->tax_amount       = $tax_amount;
        $draft->final_total      = $allData->final_total ;
        $draft->type             = "Ecom_sale" ;
        // $draft->transaction_date = \Carbon::now()->format('Y-m-d h:i:s');
        $draft->payment_status   = 2 ;
        $draft->update();
        return $draft;
    }
    // *11* Delete Ecom Transaction Info
    public static function DeleteCartRow($client,$data,$draft){
        $allData                 = \App\Models\EcomTransactionSellLine::whereHas("Ecom_transaction",function($query) use($draft,$client){
                                                                            $query->where("id",$draft->id);                                                                    
                                                                            $query->where("created_by",$client->id);                                                                    
                                                                            $query->where("not_finished",1);
                                                                        })
                                                                        ->where("product_id",$data["product_id"])
                                                                        ->first();
        if(!empty($allData)){
            $allData->delete();
        }else{
            return null;
        }
        return $draft;
    }
    // *12* convert Ecom Transaction To Tax Invoice 
    public static function TaxInvoice($client,$data){
        $draft            = EcomTransaction::where("created_by",$client->id)
                                                        ->where("status","draft")
                                                        ->where("not_finished",1)
                                                        ->first();
        // dd(request());                                                
        // if($method == "card"){  try{ }catch(Exception $e){ return null;} } 
        if(!empty($draft)){
            // *1* update Ecom Transaction info 
            $items                   = \App\Models\EcomTransactionSellLine::where("ecom_transaction_id",$draft->id)->get();
            if(count($items)>0){
            $business_id             = 1;
            $ref_count               = EcomTransaction::setAndGetReferenceCount('Ecom_sale', $business_id,1);
            $invoice_no              = EcomTransaction::generateReferenceNumber('Ecom_sale', $ref_count, $business_id,"EC/INV",1);
            $draft->status           = "final"; 
            $draft->refe_no          = $draft->invoice_no; 
            $draft->invoice_no       = $invoice_no; 
            $draft->not_finished     = 0; 
            $draft->update();
            
            // *2* check If Cart is Empty 
                // *3* save Sale Transaction  
                $sale                    = EcomTransaction::saveTransaction($draft);
                if($sale != null){
                    $sale_item           = EcomTransaction::saveTransactionItem($items,$sale);
                    if($sale_item == false){
                        return null;    
                    }
                    // *4* save Sale Transaction Payments
                    $payment_type        = $data["payment_type"] ; 
                    $method              = $payment_type;
                    if($method == "card"){
                        $card                = \App\Models\PaymentCard::find($data["card_id"]);
                        $card_number         = [
                            "card_number" =>  $card->card_number,
                            "card_cvv"    =>  $card->card_cvv,
                            "card_month"  =>  \Carbon::createFromFormat("Y-m-d H:i:s",$card->card_expire)->format("m"),
                            "card_year"   =>  \Carbon::createFromFormat("Y-m-d H:i:s",$card->card_expire)->format("Y")
                        ];  
                    }else{$card_number = null;}
                    $payment             = EcomTransaction::saveTransactionPayment($sale,$method,$card_number);
                    if($payment == false){
                        return null;    
                    }
                }else{
                    return null;    
                }
                return $draft;
            }else{
                return null;    
            }
        }else{
            return null;    
        }                                                
    }
    // *13* FOR SAVE TRANSACTION  
    public static function saveTransaction($draft){
        try{
            $accountSetting          = \App\Models\AccountSetting::first();
            $duplicate               = $draft->replicate();
            $location                = \App\BusinessLocation::where("business_id",$duplicate->business_id)->first();
            $invoice_source          = EcomTransaction::getInvoiceNumber($duplicate->business_id,$location->id); 
            $sale                    = new \App\Transaction();
            $sale->store             = $duplicate->store;                
            $sale->business_id       = $duplicate->business_id;                
            $sale->location_id       = $location->id;                
            $sale->type              = "sale";                
            $sale->status            = $duplicate->status;                
            $sale->payment_status    = 1;                
            $sale->contact_id	     = $duplicate->contact_id;                
            $sale->invoice_no        = $invoice_source;                
            $sale->transaction_date  = $duplicate->transaction_date;                
            $sale->total_before_tax  = ($duplicate->total_before_tax != null)?$duplicate->total_before_tax:0;                
            $sale->tax_id            = $duplicate->tax_id;                
            $sale->tax_amount        = $duplicate->tax_amount;                
            $sale->final_total       = ($duplicate->final_total != null)?$duplicate->final_total:0;               
            $sale->created_by        = $duplicate->created_by;                
            $sale->previous          = $duplicate->invoice_no;                
            $sale->refe_no           = $duplicate->refe_no;                
            $sale->pattern_id        = $accountSetting->pattern_id;                
            $sale->ecommerce         = 1;
            $sale->is_direct_sale    = 1;
            $sale->save();
            // TRANSACTION ACCOUNTS 
            \App\AccountTransaction::add_sell_pos($sale,$sale->pattern_id);
            // TRANSACTION MAP
            \App\Models\StatusLive::insert_data_s($sale->business_id,$sale,"Sales Invoice");
            return $sale;
        }catch(Exception $e){
            return null;
        }
    }
    // *14* FOR SAVE TRANSACTION ITEM  
    public static function saveTransactionItem($items,$sale){
        try{
            foreach($items as $it){
                $sale_item                             = new \App\TransactionSellLine();
                $sale_item->transaction_id             = $sale->id ; 
                $sale_item->store_id                   = $it->store_id ; 
                $sale_item->product_id                 = $it->product_id ; 
                $sale_item->variation_id               = $it->variation_id ; 
                $sale_item->quantity                   = $it->quantity ; 
                $sale_item->unit_price_before_discount = $it->unit_price_before_discount ; 
                $sale_item->unit_price                 = $it->unit_price ; 
                $sale_item->line_discount_type         = $it->line_discount_type ; 
                $sale_item->line_discount_amount       = $it->line_discount_amount ; 
                $sale_item->unit_price_inc_tax         = $it->unit_price_inc_tax ; 
                $sale_item->item_tax                   = $it->item_tax ; 
                $sale_item->tax_id                     = $it->tax_id ; 
                $sale_item->discount_id                = $it->discount_id ; 
                $sale_item->save(); 
            }
            return true;
        }catch(Exception $e){
            return false;
        }
    }
    // *15* FOR SAVE TRANSACTION PAYMENT  
    public static function saveTransactionPayment($draft,$method,$card_number=null){
        try {
            // if(($request->amount + $request->visa_amount) != 0 ){
                $accountSetting                        = \App\Models\AccountSetting::first();
                $payment                               = new \App\TransactionPayment;
                $payment->business_id                  = $draft->business_id;
                $payment->store_id                     = $accountSetting->client_store_id;
                if($method == "card"){
                    $payment->account_id               = $accountSetting->client_visa_account_id;
                    $payment->card_number              = $card_number["card_number"];
                    $payment->card_security            = $card_number["card_cvv"];
                    $payment->card_month               = $card_number["card_month"];
                    $payment->card_year                = $card_number["card_year"];
                }else if($method== "cash_visa"){
                    $payment->account_id               = $accountSetting->client_account_id;
                }else{
                    $payment->account_id               = $accountSetting->client_account_id;
                }
                $payment->amount                       = $draft->final_total;
                $payment->source                       = $draft->final_total;
                $payment->transaction_id               = $draft->id;
                $payment->method                       = $method;
                $payment->paid_on                      = $draft->transaction_date; 
                $payment->created_by                   = $draft->created_by;
                $type_pay                              = 'sell_payment';
                $payment_ref_no_count                  = EcomTransaction::setAndGetReferenceCount($type_pay,$draft->business_id ,$draft->pattern_id );
                $payment_ref_no                        = EcomTransaction::generateReferenceNumber($type_pay, $payment_ref_no_count,$draft->business_id,$draft->pattern_id);
                $payment->payment_ref_no               = $payment_ref_no; 
                $payment->save();
                //  end second section 
                //  first section 
                
                if($method == "cash" || $method == "card"){
                    $check = null;
                    
                    $ref_count_pay = EcomTransaction::setAndGetReferenceCount("voucher",$draft->business_id,$draft->pattern_id);
                    //Generate reference number
                    $ref_no_pay    = EcomTransaction::generateReferenceNumber("voucher" , $ref_count_pay,$draft->business_id,null,$draft->pattern_id);
                    //return $this->add_main($request->cheque_type);
                    $data               =  new PaymentVoucher;
                    $data->business_id  =  $draft->business_id;
                    $data->ref_no       =  $ref_no_pay;
                    if($method == "card"){
                        $data->account_id   =  $accountSetting->client_visa_account_id;
                        $data->amount       =  $draft->final_total;
                    }else{
                        $data->amount       =  $draft->final_total;
                        $data->account_id   =  $accountSetting->client_account_id;
                    }
                    $data->contact_id   =  $draft->contact_id;
                    //  type = 1         // Receipt Payment // 
                    $data->type         =  1;
                    $data->text         =  "Strip Payment";
                    $data->date         =  $draft->transaction_date;
                    $data->save();
                    $state              =  'credit';
                    $re_state           =  'debit';
                    // effect cash  account 
                    $credit_data = [
                        'amount' => $data->amount,
                        'account_id' => ($method == "card")?$accountSetting->client_visa_account_id:$accountSetting->client_account_id,
                        'type' => $re_state,
                        'sub_type' => 'deposit',
                        'operation_date' => $data->date,
                        'created_by' =>  $draft->created_by,
                        'note' => $data->text,
                        'transaction_id'=>$draft->id,
                        'payment_voucher_id'=>$data->id
                    ];
                    $credit = \App\AccountTransaction::createAccountTransaction($credit_data);
            
                    // effect contact account 
                    $account_id  =  \App\Contact::add_account($data->contact_id,$draft->business_id);
                    $credit_data = [
                        'amount' => $data->amount,
                        'account_id' => $account_id,
                        'type' => $state,
                        'sub_type' => 'deposit',
                        'operation_date' => $data->date,
                        'created_by' => $draft->created_by ,
                        'note' => $data->text,
                        'transaction_id'=>$draft->id,
                        'payment_voucher_id'=>$data->id
                    ];
                    $credit = \App\AccountTransaction::createAccountTransaction($credit_data);
                    $types = "voucher";
                    $ref_count_voucher  = EcomTransaction::setAndGetReferenceCount("entries",$draft->business_id,NULL);
                    //Generate reference number
                    $refence_no_voucher = EcomTransaction::generateReferenceNumber("entries" , $ref_count_voucher,$draft->business_id,null,NULL);
                    $entries                         = new Entry;
                    $entries->business_id            = $data->business_id;
                    $entries->refe_no_e              = 'EN_'.$refence_no_voucher;
                    $entries->ref_no_e               = $data->ref_no;
                    if($data->type == 1){
                        $entries->state              = 'Receipt Voucher';
                    }else{
                        $entries->state              = 'Payment Voucher';
                    }            
                    $entries->debit                  = $data->amount;
                    $entries->credit                 = $data->amount;
                    $entries->created_at             = $data->date;
                    $entries->updated_at             = $data->date;
                    $entries->voucher_id             = $data->id;
                    $entries->save();
                    $dat = \App\AccountTransaction::where("payment_voucher_id",$data->id)->update([
                                                        "entry_id"=>$entries->id
                                                    ]);
                    $payment_voucher = $data->id;
                }else{
                    $payment_voucher = null;
                    $check = null;
                }
                
                if(round(($data->amount),2) >= round($draft->final_total,2) ){
                    $draft->payment_status = 1;
                    $draft->update();
                }elseif(($data->amount) == 0 || ($data->amount) == null){
                    $draft->payment_status = 2;
                    $draft->update();
                }elseif(round(($data->amount),2) < round($draft->final_total,2)){
                    $draft->payment_status = 3;
                    $draft->update();
                }
                //  end first section 

                //..update 
                $payment->payment_voucher_id           = $payment_voucher;  
                $payment->check_id                     = $check; 
                $payment->save();
                //..
            // }
            return true;    
        } catch (Exception $e) {
            return false;
        }
    }
    // *16* FOR GET TRANSACTION   
    public static function OrderList($client,$data){
        try {
            $transaction = \App\Models\EcomTransaction::where("status","!=","draft")->where("created_by",$client->id)->get();
            $dataAll = [];
            foreach($transaction as $i){
                $source = \App\Transaction::where("previous",$i->invoice_no)->first();
                $items  = \App\TransactionSellLine::where("transaction_id",$source->id)->with("warranties")->get();
                if($source->type == "purchase"){
                    $invoice_no = $source->ref_no;
                }else{
                    $invoice_no = $source->invoice_no;
                }
                $items_list  =  [];
                foreach($items as $I){
                    $items_list[] = [
                           "id"                   => $I->id,            
                           "product_id"           => $I->product->name,            
                           "image"                => $I->product->image_url,  
                           "category"             => $I->product->category->name,            
                           "sub_category"         => $I->product->sub_category->name,                  
                           "quantity"             => round($I->quantity,2),            
                           "price"                => round($I->unit_price,2),            
                           "discount_amount"      => round($I->line_discount_amount,2),            
                           "price_inc_tax"        => round($I->unit_price_inc_tax,2),          
                           "warranty"             => (count($I->warranties)>0)?$I->warranties[0]->name . "<br>" . $I->warranties[0]->description:"",
                           "wishlist"             => ($I->product->wishlist != null)?true :false            
        
                    ];
                }
                $data  = [
                    "id"               => $source->id,
                    "type"             => $source->type,
                    "payment_status"   => $source->payment_status,
                    "status"           => $source->status,
                    "contact"          => $source->contact->name,
                    "invoice_no"       => $invoice_no,
                    "sub_total"        => round($source->total_before_tax,2),
                    "date"             => $source->transaction_date,
                    "delivery_date"    => $source->transaction_date,
                    "vat"              => round($source->final_total - $source->total_before_tax,2),
                    "total"            => round($source->final_total+20,2),
                    "delivery_price"   => round(20,2),
                    "items"            => $items_list,
                ];
                $dataAll[] = $data; 
            }
            return $dataAll;
        } catch (Exception $e) {
            return null;
        } 
    }
    // *17* FOR GET DRAFT TRANSACTION FOR CHECKOUT   
    public static function checkout($data,$client){
        try {
            $transaction = \App\Models\EcomTransaction::where("status","=","draft")->where("created_by",$client->id)->get();
            $dataAll = [];
            foreach($transaction as $i){
                $items  = \App\Models\EcomTransactionSellLine::where("ecom_transaction_id",$i->id)->get();
              
                if($i->type == "purchase"){
                    $invoice_no = $i->ref_no;
                }else{
                    $invoice_no = $i->invoice_no;
                }
                $items_list  =  [];
                foreach($items as $I){
                    $wishlist     =  \App\Models\WishList::where("product_id",$I->product->id)->first();
                    $enter_stock  =  \App\Models\WarehouseInfo::where("product_id",$I->product->id)->sum("product_qty");
                    $items_list[] = [
                           "product_id"           => $I->product->id,            
                           "product_name"         => $I->product->name,            
                           "product_code"         => $I->product->sku,            
                           "image"                => $I->product->image_url,            
                           "category"             => $I->product->category->name,            
                           "sub_category"         => $I->product->sub_category->name,            
                           "warranty"             => ($I->product->warranty)?$I->product->warranty->name:"",            
                           "quantity"             => DoubleVal($I->quantity),            
                           "price_exc_tax"        => round($I->unit_price,2),            
                           "discount"             => round($I->line_discount_amount,2),            
                           "price_inc_tax"        => round($I->unit_price_inc_tax,2),          
                           "wishlist"             => ($wishlist != null)?true :false,
                           "stock_qty"            => $enter_stock
                    ];
                }
                $data  = [
                    "id"               => $i->id,
                    "type"             => $i->type,
                    "payment_status"   => $i->payment_status,
                    "status"           => $i->status,
                    "contact"          => $i->contact->name,
                    "invoice_no"       => $invoice_no,
                    "transaction_date" => $i->transaction_date,
                    "sub_total"        => round($i->total_before_tax,2),
                    "vat"              => round($i->tax_amount,2),
                    "final_total"      => round($i->final_total,2),
                    "items"            => $items_list,
                ];
                $dataAll = $data; 
            }
            return $dataAll;
        } catch (Exception $e) {
            return null;
        } 
    }
    // *18* FOR CHECKOUT  GET ADDRESSES    
    public static function addresses($client){
        try {
            $address     = \App\Models\AccountAddress::where("client_id",$client->id)->get();
            $listAddress = [];
            foreach($address as $i){
                $listAddress[] = [
                        "id"             => $i->id,            
                        "title"          => $i->title,            
                        "building"       => $i->building,            
                        "street"         => $i->street,            
                        "flat"           => $i->flat,            
                        "area"           => $i->area,            
                        "city"           => $i->city,          
                        "country"        => $i->country,          
                        "address_name"   => $i->address_name,         
                        "address_type"   => $i->address_type          
                ];
            }
            return $listAddress;
        } catch (Exception $e) {
            return null;
        } 
    }
    // *19* FOR CHECKOUT GET PAYMENT CARD    
    public static function card($client){
        try {
            $card     = \App\Models\PaymentCard::where("client_id",$client->id)->get();
            $listCard = [];
            foreach($card as $i){
                $listCard[] = [
                    "id"                => $i->id,            
                    "card_type"         => $i->card_type,            
                    "card_expire"       => $i->card_expire,            
                    "last_four_number"  => substr(decrypt($i->card_number),12),                  
                ];
            }
            return $listCard;
        } catch (Exception $e) {
            return null;
        } 
    }

    //*----------------------------------------*\\
    //*--1------ references  bill -------------*\\
    //******************************************\\
    public static function setAndGetReferenceCount($type,$business_id,$pattern)
    {
        $ref = ReferenceCount::where('ref_type', $type)
                          ->where('business_id', $business_id)
                          ->where('pattern_id', $pattern)
                          ->first();
        if (!empty($ref)) {
            $ref->ref_count += 1;
            $ref->save();
            return $ref->ref_count;
        } else {
            $new_ref = ReferenceCount::create([
                'ref_type' => $type,
                'business_id' => $business_id,
                'pattern_id' => $pattern,
                'ref_count' => 1
            ]);
            return $new_ref->ref_count;
        }
    }
    
    //*----------------------------------------*\\
    //*--2------ references  bill -------------*\\
    //******************************************\\
    public static function generateReferenceNumber($type, $ref_count, $business_id = null, $default_prefix = null,$pattern =null)
    {
        if (!empty($default_prefix)) {
            $prefix = $default_prefix;
        }
        $ref_digits =  str_pad($ref_count, 5, 0, STR_PAD_LEFT);
        if(!isset($prefix)){
                $prefix = "";
        }
        if (!in_array($type, ['contacts', 'business_location', 'username' ,"supplier","customer"   ])) {
            $ref_year = \Carbon::now()->year;
           
            $ref_number = $prefix . $ref_year . '/' . $ref_digits;
            
        } else {
             
            $ref_number = $prefix . $ref_digits;
        }
        return  $ref_number;
    }
    //*----------------------------------------*\\
    //*--3------ references  bill -------------*\\
    //******************************************\\
    public static function getInvoiceNumber($business_id, $location_id)
    {
        $scheme_id = \App\BusinessLocation::where('business_id', $business_id)
                    ->where('id', $location_id)
                    ->first()
                    ->invoice_scheme_id;
        if (!empty($scheme_id) && $scheme_id != 0) {
            $scheme = \App\InvoiceScheme::find($scheme_id);
        }

        //Check if scheme is not found then return default scheme
        if (empty($scheme)) {
            $scheme = \App\InvoiceScheme::where('business_id', $business_id)
                    ->where('is_default', 1)
                    ->first();
        }

        if ($scheme->scheme_type == 'blank') {
            $prefix = $scheme->prefix;
        } else {
            $prefix = $scheme->prefix . date('Y') . config('constants.invoice_scheme_separator');
        }

        //Count
        $count = $scheme->start_number + $scheme->invoice_count;
        $count = str_pad($count, $scheme->total_digits, '0', STR_PAD_LEFT);

        //Prefix + count
        $invoice_no = $prefix . $count;

        //Increment the invoice count
        $scheme->invoice_count = $scheme->invoice_count + 1;
        $scheme->save();
        return $invoice_no;
    }
    //*----------------------------------------*\\
    //*--4------ relations   bill -------------*\\
    //******************************************\\
    public function contact(){
        return $this->belongsTo("\App\Contact","contact_id");
    }
}
