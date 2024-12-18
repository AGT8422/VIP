<?php

namespace App\Models\FrontEnd\Vouchers;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Utils\ProductUtil;
use App\Models\FrontEnd\Utils\GlobalUtil;
class Voucher extends Model
{
    use HasFactory,SoftDeletes;
    // *** REACT FRONT-END VOUCHER *** // 
    // **1** ALL VOUCHER
    public static function getVoucher($user,$filter) {
        try{
            $business_id       = $user->business_id;
            if($filter != null){ 
                $data              = Voucher::allData("all",null,$business_id,$filter);
            }else{
                $data              = Voucher::allData("all",null,$business_id);
            } 
            if($data == false){ return false;}
            return $data;
        }catch(Exception $e){
            return false;
        }
    }
    // **2** CREATE VOUCHER
    public static function createVoucher($user,$data) {
        try{
            $business_id             = $user->business_id;
            $create                  = Voucher::requirement($user);
            return $create;
        }catch(Exception $e){
            return false;
        }
    }
    // **3** EDIT VOUCHER
    public static function editVoucher($user,$data,$id) {
        try{
            $business_id             = $user->business_id;
            $data                    = Voucher::allData(null,$id,$business_id);
            $edit                    = Voucher::requirement($user);
            $account                 = \App\Models\PaymentVoucher::find($id);
            if($data  == false){ return false; }
           
            // ................. BILLS
            $bills       = [];
            $remaining   = 0;
            $all_list    = [];
            if($account->account_type == 0){
               $contact_idd  =  $account->contact_id;
            }else{
                $act         = \App\Account::where("id",$account->contact_id)->first();
                $contact_idd =  $act->contact_id;
            } 
            if($account){
                $types = [];
                if($account->type == 1){
                    $types = ["sale","sell_return"];
                }elseif($account->type == 0){
                    $types = [ "purchase","purchase_return" ];
                }
                $bills       = \App\Transaction::whereIn("type",$types)->where("contact_id",$contact_idd)->get();
            }
             
            $all_list[] = [
                "status"           => "" ,    
                "date"             => "" ,    
                "check"            => "" ,    
                "reference_no"     => "" ,    
                "contact_name"     => "" ,    
                "invoice_status"   => "" ,    
                "store"            => "" ,    
                "final_total"      => "" ,    
                "pay_due"          => "" ,    
                "bill_id"          => "" ,    
                "payment_id"       => "" ,      
                "previous_payment" => "" ,  
            ];
            
            $vch_info  =   \App\Models\PaymentVoucher::find($id);
            $remaining =   ($vch_info)?$vch_info->amount:0;

            foreach($bills as $ies){
                $transaction_payment  = \App\TransactionPayment::where("transaction_id",$ies->id)->sum("amount");
                if($ies->payment_status == "paid"){
                    $payment_id           = \App\TransactionPayment::where("transaction_id",$ies->id)->where("payment_voucher_id",$id)->first();
                    if(!empty($payment_id)){
                        $this_value           = ($payment_id)?$payment_id->amount:0;
                        $pr_payment           =  $transaction_payment - $this_value;
                        if((round(doubleVal($ies->final_total),2)  - $transaction_payment)>0){
                            $pay_status     = ($payment_id)?0:1;
                            $check          = ($payment_id)?true:false;
                            $am               = ($payment_id)?$payment_id->amount:0;
                            $previous_payment = floatVal($pr_payment);
                        }else{
                            $check          = ($payment_id)?true:false;
                            $pay_status     = 0;
                            $am               = ($payment_id)?$payment_id->amount:0;
                            $previous_payment = floatVal($pr_payment);
                        }
                        $all_list[] = [
                            "status"        => $pay_status ,    
                            "check"         => $check ,    
                            "date"          => $ies->transaction_date ,    
                            "reference_no"  => ($ies->type == "purchase" || $ies->type == "purchase_return")?$ies->ref_no:$ies->invoice_no ,    
                            "contact_name"  => ($ies->contact)?$ies->contact->name:"" ,    
                            "invoice_status"=> $ies->status ,    
                            "store"         => ($ies->warehouse)?$ies->warehouse->name:"" ,    
                            "final_total"   => round(doubleVal($ies->final_total),2) ,    
                            "pay_due"       => ((round(doubleVal($ies->final_total),2)  - $transaction_payment)>0)?round(doubleVal($ies->final_total),2)  -  $transaction_payment:0,    
                            "total_payment" => round($transaction_payment,2), 
                            "bill_id"       => $ies->id,     
                            "payment_id"    => ($payment_id)?$payment_id->id:"",     
                            "previous_payment" => $previous_payment
                        ];
                    }
                }else{
                    $payment_id           = \App\TransactionPayment::where("transaction_id",$ies->id)->where("payment_voucher_id",$id)->first();
                    $this_value           = ($payment_id)?$payment_id->amount:0;
                    $pr_payment           =  $transaction_payment - $this_value;
                    if((round(doubleVal($ies->final_total),2)  - $transaction_payment)>0){
                        $pay_status     = ($payment_id)?0:1;
                        $check          = ($payment_id)?true:false;
                        $am               = ($payment_id)?$payment_id->amount:0;
                        $previous_payment = floatVal($pr_payment);
                    }else{
                        $check          = ($payment_id)?true:false;
                        $pay_status     = 0;
                        $am               = ($payment_id)?$payment_id->amount:0;
                        $previous_payment = floatVal($pr_payment);
                    }
                    $all_list[] = [
                        "status"        => $pay_status ,    
                        "check"         => $check ,    
                        "date"          => $ies->transaction_date ,    
                        "reference_no"  => ($ies->type == "purchase" || $ies->type == "purchase_return")?$ies->ref_no:$ies->invoice_no ,    
                        "contact_name"  => ($ies->contact)?$ies->contact->name:"" ,    
                        "invoice_status"=> $ies->status ,    
                        "store"         => ($ies->warehouse)?$ies->warehouse->name:"" ,    
                        "final_total"   => round(doubleVal($ies->final_total),2) ,    
                        "pay_due"       => ((round(doubleVal($ies->final_total),2)  - $transaction_payment)>0)?round(doubleVal($ies->final_total),2)  -  $transaction_payment:0,    
                        "total_payment" => round($transaction_payment,2), 
                        "bill_id"       => $ies->id,     
                        "payment_id"    => ($payment_id)?$payment_id->id:"",     
                        "previous_payment" => $previous_payment,    
                    ];
                }
                

                
                
                $remaining  -= ($payment_id)?round($payment_id->amount,2):0;
                
            }
                 
            $list["info"]            = $data;
            $list["require"]         = $edit;
            $list["bill"]            = $all_list;
            $list["remaining"]       = $remaining;
            return $list;
        }catch(Exception $e){
            return false;
        } 
    }
    // **4** STORE VOUCHER
    public static function storeVoucher($user,$data,$request) {
        try{
            \DB::beginTransaction();
            $business_id         = $user->business_id;
            $output              = Voucher::createNewVoucher($user,$data,$request);
            if($output == false){ return false; } 
            \DB::commit();
            return $output;
        }catch(Exception $e){
            return false;
        }
    }
    // **5** UPDATE VOUCHER
    public static function updateVoucher($user,$data,$id,$request) {
        try{
            \DB::beginTransaction();
            $business_id         = $user->business_id;
            $output              = Voucher::updateOldVoucher($user,$data,$id,$request);
            if($output == false){ return false; } 
            \DB::commit();
            return $output;
        }catch(Exception $e){
            return false;
        }
    }
    // **6** DELETE VOUCHER
    public static function deleteVoucher($user,$id) {
        try{
            \DB::beginTransaction();
            $business_id = $user->business_id;
            $voucher     = \App\Models\PaymentVoucher::find($id);
            if(!$voucher){ return false; }
            if ($voucher) {
                \App\AccountTransaction::where('payment_voucher_id',$id)->delete();
                $payments =  \App\TransactionPayment::where("payment_voucher_id",$id)->get();
                foreach($payments as $payment){
                    if(!empty($payment)){
                        $payment->amount = 0;
                        $payment->update();
                        $total_paid = $payment->amount;
                        
                        $final_amount = \App\Transaction::find($payment->transaction_id);
                        if(!empty($final_amount)){
                            $balance = $final_amount->final_total;
                            
                            $status = 'due';
                            if ($balance <= $total_paid) {
                                $status = 'paid';
                            } elseif ($total_paid > 0 && $balance > $total_paid) {
                                $status = 'partial';
                            }
                            $final_amount->payment_status = $status;
                            $final_amount->update();
                        } 
                        
                        $payment->delete();
                        
                    } 
                }
                $voucher->delete();
            } 
            \DB::commit();
            return true;
        }catch(Exception $e){
            return false;
        }
    }
    // **7** BILLS VOUCHER
    public static function billVoucher($user,$data,$id) {
        try{
            \DB::beginTransaction();
            $business_id = $user->business_id;
            $types       = $data["type"];
            $account     = \App\Account::where("id",$id)->first();
            $bills       = [];
            $all_list    = [];
            if($account){
                $type_list   = ($types == "receipt")?["sale","sell_return"]:(($types == "payment")?["purchase","purchase_return"]:[]);
                $bills       = \App\Transaction::whereIn("type",$type_list)->where("payment_status","!=","paid")->where("contact_id",$account->contact_id)->get();
            }
            if(count($bills) == 0){ return []; }
            
            foreach($bills as $ies){
                    $transaction_payment  = \App\TransactionPayment::where("transaction_id",$ies->id)->where("return_payment","=",0)->sum("amount");
                    $payment_id           = null;
                    $this_value           = ($payment_id)?$payment_id->amount:0;
                    $pr_payment           =  $transaction_payment - $this_value;
                    if((round(doubleVal($ies->final_total),2)  - $transaction_payment)>0){
                        $pay_status     = ($payment_id)?0:1;
                        $check          = ($payment_id)?true:false;
                        $am               = ($payment_id)?$payment_id->amount:0;
                        $previous_payment = floatVal($pr_payment);
                    }else{
                        $check          = ($payment_id)?true:false;
                        $pay_status     = 0;
                        $am               = ($payment_id)?$payment_id->amount:0;
                        $previous_payment = floatVal($pr_payment);
                    }
                    $all_list[] = [
                        "status"        => $pay_status ,    
                        "check"         => $check ,    
                        "date"          => $ies->transaction_date ,    
                        "reference_no"  => ($ies->type == "purchase" || $ies->type == "purchase_return")?$ies->ref_no:$ies->invoice_no ,    
                        "contact_name"  => ($ies->contact)?$ies->contact->name:"" ,    
                        "invoice_status"=> $ies->status ,    
                        "store"         => ($ies->warehouse)?$ies->warehouse->name:"" ,    
                        "final_total"   => round(doubleVal($ies->final_total),2) ,    
                        "pay_due"       => ((round(doubleVal($ies->final_total),2)  - $transaction_payment)>0)?round(doubleVal($ies->final_total),2)  -  $transaction_payment:0,    
                        "total_payment" => round($transaction_payment,2), 
                        "bill_id"       => $ies->id,     
                        "payment_id"    => ($payment_id)?$payment_id->id:"",     
                        "previous_payment" => $previous_payment,    
                    ];
              
             

                
                
                // $remaining  -= ($payment_id)?round($payment_id->amount,2):0;
                
            }
            \DB::commit();
            return $all_list;
        }catch(Exception $e){
            return false;
        }
    }
    // **8** VIEW VOUCHER
    public static function viewVoucher($user,$data,$id) {
        try{
            \DB::beginTransaction();
            $business_id = $user->business_id;
            $voucher     = Voucher::allData(null,$id,$business_id);
            if($voucher  == false){ return false; } 
            \DB::commit();
            return $voucher;
        }catch(Exception $e){
            return false;
        }
    }
    // **9** PRINT VOUCHER
    public static function printVoucher($user,$data,$id) {
        try{
            \DB::beginTransaction();
            $business_id = $user->business_id;
            $voucher     = \App\Models\PaymentVoucher::find($id);
            if(empty($voucher)){ return false; }
            $voucher     = ($voucher->type == 1)?  \URL::to('reports/r-vh/'.$voucher->id) : \URL::to('reports/p-vh/'.$voucher->id) ; 
            \DB::commit();
            return $voucher;
        }catch(Exception $e){
            return false;
        }
    }
    // **10** CURRENCY VOUCHER
    public static function currencyVoucher($user,$data,$id) {
        try{
            \DB::beginTransaction();
            $business_id = $user->business_id;
            $currency    = \App\Models\ExchangeRate::where("id",$id)->first();
            if(!empty($currency)){
                if($currency->right_amount == 0){
                    $amount = $currency->amount;
                }else{
                    $amount = $currency->opposit_amount;
                }
                $symbol = $currency->currency->symbol;
              
            }else{
                $symbol = "";
            }
            $array = [];
            $array["amount"] = $amount;
            $array["symbol"] = $symbol;
            \DB::commit();
            return $array;
        }catch(Exception $e){
            return false;
        }
    }
    // **11** ENTRY VOUCHER
    public static function entryVoucher($user,$data,$id) {
         try{
            \DB::beginTransaction();
            $list_of_entry        =  [];$list_of_entry2        =  [];
            $line                 =  [];  $array = [];
            // $data                 =  \App\Models\PaymentVoucher::find($id);
            $business_id          =  $user->business_id;
            // ..................................................................1........
            $entry_id             =  \App\AccountTransaction::where('payment_voucher_id',$id)->whereNull("for_repeat")->where('amount','>',0)->groupBy("entry_id")->pluck("entry_id");
            $entry                =  \App\Models\Entry::whereIn("id",$entry_id)->get();
            
            $reference = "";
            foreach($entry as $items){
              $line2                       =  [];
              $reference = $items->ref_no_e;;
              $list_of_entry["id"]               = $items->id;
              $list_of_entry["entry_reference"]  = $items->refe_no_e;
                // ..................................................................2........
                $allData              =  \App\AccountTransaction::where('payment_voucher_id',$id)->where('entry_id',$items->id)->whereNull("for_repeat")->where('amount','>',0)->get();
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
                    $list_of_entry2["payment_voucher_id"]     = $items->payment_voucher_id ;
                    $list_of_entry2["note"]                   = $items->note ;
                    $list_of_entry2["entry_id"]               = $items->entry_id ;
                    // $list_of_entry2["created_by"]     = $items-> ;
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
    // **13** VIEW VOUCHER
    public static function viewBillVoucher($user,$data,$id) {
        try{
            \DB::beginTransaction();
            $business_id = $user->business_id;
            $bill     = \App\Transaction::find($id);
            if(empty($bill)){ return false; }
            $line      = [];
            if($bill){
                $line[] = [
                    "id"              => $bill->id ,
                    "invoice_no"      => ($bill->invoice_no != null)?$bill->invoice_no:$bill->ref_no ,
                    "sub_total"       => $bill->total_before_tax ,
                    "tax_name"        => ($bill->tax)?$bill->tax->name:$bill->tax_id ,
                    "tax"             => $bill->tax_amount ,
                    "final_total"     => $bill->final_total ,
                    "type"            => $bill->type ,
                    "status"          => $bill->status ,
                    "date"            => $bill->transaction_date ,
                    "project_no"      => $bill->project_no ,
                    "lines"           => $bill->sell_lines ,
                ];
            }
            \DB::commit();
            return $line;
        }catch(Exception $e){
            return false;
        }
    }
    // **12** ATTACH VOUCHER
    public static function attachVoucher($user,$data,$id) {
         try{
            \DB::beginTransaction();
            $list_of_attach       =  []; 
            $business_id          =  $user->business_id;
            // ..................................................................1........
            $voucher              =  \App\Models\PaymentVoucher::find($id);
            // ..................................................................2........
            $attach     =  isset($voucher->document)?$voucher->document:null ;
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

    // ****** MAIN FUNCTIONS 
    // **1** CREATE VOUCHER
    public static function createNewVoucher($user,$data,$request) {
       try{
            $business_id      = $user->business_id;
            $document_expense = [];
            if ($request->hasFile('document_expense')) {
                $count_doc1 = 1;
                foreach ($request->file('document_expense') as $file) {
                    
                    $file_name   =  'public/uploads/documents/'.time().'.'.$count_doc1++.'.'.$file->getClientOriginalExtension();
                    // ...........................
                    $data_sized  = getimagesize($file);
                    $width       = $data_sized[0];
                    $height      = $data_sized[1];
                    $half_width  = $width/2;
                    $half_height = $height/2;
                    $img_sized   = \Image::make($file)->resize($half_width,$half_height); //$request->$file_name->storeAs($dir_name, $new_file_name)  ||\public_path($new_file_name)
                    if ($img_sized->save(base_path($file_name),20)) {
                        $uploaded_file_name        = $file_name;
                        array_push($document_expense,$uploaded_file_name);
                    }
                }
            }
 
            // note 0=> supplier , 1 =>customer
            $productUtil           = new ProductUtil();
            $ref_count             = $productUtil->setAndGetReferenceCount("voucher",$business_id);
            //Generate reference number
            $ref_no                = $productUtil->generateReferenceNumber("voucher" , $ref_count,$business_id);
            //return $this->add_main($request->cheque_type);
            $Data                  =  new \App\Models\PaymentVoucher;
            $Data->business_id     =  $business_id;
            $Data->ref_no          =  $ref_no;
            $Data->amount          =  isset($data['amount'])?$data['amount']:0;
            $Data->account_id      =  isset($data['account_id'])?$data['account_id']:null;
            $Data->contact_id      =  isset($data['contact_id'])?$data['contact_id']:null;
            $Data->type            =  isset($data['type'])?$data['type']:null;
            $Data->currency_id     =  isset($data['currency_id'])?$data['currency_id']:null;
            $Data->currency_amount =  isset($data['amount_currency'])?$data['amount_currency']:null;
            $Data->exchange_price  =  isset($data['currency_id_amount'])?$data['currency_id_amount']:null;
            $Data->text            =  isset($data['text'])?$data['text']:null;
            $Data->date            =  isset($data['date'])?$data['date']:null;
            $Data->document        =  json_encode($document_expense) ;
            $Data->account_type    =  1;
            $Data->save();
            GlobalUtil::effect_account_expanse($Data->id,$Data->type,$user->id);
            $bills                 =  $request->only([
                    'bill_id','bill_amount'
            ]);
            if ($request->bill_id) {
                \App\Services\PaymentVoucher\Bill::pay_transaction($Data->id,$bills,$user);
            }
            $type="voucher";
            \App\Models\Entry::create_entries($Data,$type);
            return true; 
        }catch(Exception $e){
            return false;
        }
    }
    // **2** UPDATE VOUCHER
    public static function updateOldVoucher($user,$data,$id,$request) {
       try{
            \DB::beginTransaction();
            $business_id              =  $user->business_id;
            $voucher                  =  \App\Models\PaymentVoucher::find($id);
            $old_id                   =  $voucher->account_id;
            $old_contact              =  $voucher->contact_id;
            $old_type                 =  $voucher->account_type;
            $voucher->currency_id     =  $request->currency_id;
            $voucher->currency_amount =  $request->amount_currency;
            $voucher->exchange_price  =  $request->currency_id_amount;
            $voucher->amount          =  $request->amount;
            $voucher->account_id      =  $request->account_id;
            $voucher->contact_id      =  $request->contact_id;
            $voucher->text            =  $request->text;
            $voucher->date            =  $request->date;
            $voucher->type            =  $request->type;
            $voucher->account_type    =  1;
            $old_document             =  $voucher->document;
            if($old_document == null){
             $old_document            = [];
             }
             
            if ($request->hasFile('document_expense')) {
                $count_doc2 = 1;
                foreach ($request->file('document_expense') as $file) {
                    $file_name   =  'public/uploads/documents/'.time().'.'.$count_doc2++.'.'.$file->getClientOriginalExtension();
                    // ...........................
                    $data_sized  = getimagesize($file);
                    $width       = $data_sized[0];
                    $height      = $data_sized[1];
                    $half_width  = $width/2;
                    $half_height = $height/2;
                    $img_sized   = \Image::make($file)->resize($half_width,$half_height); //$request->$file_name->storeAs($dir_name, $new_file_name)  ||\public_path($new_file_name)
                    if ($img_sized->save(base_path($file_name),20)) {
                        $uploaded_file_name        = $file_name;
                        array_push($old_document,$uploaded_file_name);
                    }
                }
            }
           
            if(json_encode($old_document)!="[]"){
                $voucher->document        =  json_encode($old_document) ;
            }
             
            $voucher->update();
            \App\AccountTransaction::where('payment_voucher_id',$id)
                                ->where('account_id',$old_id)->update([
                                    'note'=>$request->text,
                                    'amount'=>$request->amount,
                                    'operation_date'=>$request->date,
                                    'account_id'=>$request->account_id
                                ]); 
           
            if($old_type == 0){
                $old_account_id     =  \App\Account::where('contact_id',$old_contact)->first();
            }else{
                $old_account_id     =  \App\Account::where('id',$old_contact)->first();
            }
            
          
           \App\AccountTransaction::where('payment_voucher_id',$id)
                                        ->where('account_id',$old_account_id->id)->update([
                                            'note'=>$request->text,
                                            'amount'=>$request->amount,
                                            'operation_date'=>$request->date,
                                            'account_id'=>$request->contact_id
                                        ]);
             if($request->contact_id != $old_contact){
                $allPayment = \App\TransactionPayment::where("payment_voucher_id",$id)->get();
                foreach($allPayment as $i){
                    $transaction = \App\Transaction::find($i->transaction_id);
                    $i->delete();
                    \App\Services\PaymentVoucher\Bill::update_status($transaction);

                }
            }                                      
            $bills                       =  $request->only([
                'bill_id','bill_amount','old_bill_id','old_bill_amount','payment_id'
            ]);
            \App\Services\PaymentVoucher\Bill::update_pay_transaction($voucher->id,$bills,$user);
            
            $payment = \App\TransactionPayment::where("payment_voucher_id",$id)->orderBy("id","desc")->first();
            if($payment){
                $old_payment = $payment->replicate();
                $parent      = \App\Models\ParentArchive::save_payment_parent($payment->id,"Edit",$old_payment);
            }
             if(!empty($payment)){
           
                $sum           =  \App\TransactionPayment::where('transaction_id',$payment->transaction_id)->sum("amount");
                $final         =  $sum - $payment->amount ;
                $transaction   =  \App\Transaction::find($payment->transaction_id);
                $total_bill    =  $transaction->final_total;
                
                //...... here no previous payment 
                if($final == 0){
                 
                    $margin_total = round($total_bill,2) - round($request->amount,2);
                    if($margin_total < 0 || $margin_total == 0){
                        $payment_amount_final = $total_bill;
                    }elseif($margin_total > 0){
                        $payment_amount_final = $request->amount;
                    } 
                }else{
                   $margin_total = $total_bill - $final;
                    // dd($total_bill);
                    // dd($final);
                    // dd($margin_total);
                    $now_with_old = round($margin_total,config("constants.currency_precision")) - round($payment->amount,config("constants.currency_precision")); 
                    $now          = round($margin_total,config("constants.currency_precision")) - round($request->amount,config("constants.currency_precision")); 
                    if($now < 0  ){
                        $payment_amount_final = round($margin_total,config("constants.currency_precision"));
                    }elseif($now > 0){
                        $payment_amount_final = round($request->amount,config("constants.currency_precision"));
                    }else {
                        $payment_amount_final = $total_bill;
                    } 
                }
                $payment->payment_for = $data["contact_id"];
                $payment->amount      = $payment_amount_final;
                $payment->source      = $payment_amount_final;
                $payment->save();

    
            }
            \DB::commit();
            return true; 
        }catch(Exception $e){
            return false;
        }
    }

    // **3** GET  VOUCHER
    public static function allData($type=null,$id=null,$business_id,$filter=null) {
        try{
            $list   = [];
            if($type != null){
                if($filter!=null){
                    $voucher     = \App\Models\PaymentVoucher::where("business_id",$business_id)->orderBy("id","desc");
                    if($filter["startDate"] != null){
                        $voucher->whereDate("date",">=",$filter["startDate"]);
                    }
                    if($filter["endDate"] != null){
                        $voucher->whereDate("date","<=",$filter["endDate"]);
                    }
                     if($filter["month"] != null){
                        $m = \Carbon::createFromFormat('Y-m-d',$filter["month"])->format('m');
                        $y = \Carbon::createFromFormat('Y-m-d',$filter["month"])->format('Y');
                        $startD  = $y."-".$m."-01";
                       
                        $voucher->whereDate("date","<=",$filter["month"]); 
                        $voucher->whereDate("date",">=",$startD); 
                    }
                    if($filter["day"] != null){
                        $voucher->whereDate("date","=",$filter["day"]); 
                    }
                    if($filter["year"] != null){
                        $m = \Carbon::createFromFormat('Y-m-d',$filter["year"])->format('m');
                        $y = \Carbon::createFromFormat('Y-m-d',$filter["year"])->format('Y');
                        $startD  = $y."-01-01";
                       
                        $voucher->whereDate("date","<=",$filter["year"]); 
                        $voucher->whereDate("date",">=",$startD); 
                    }
                    if($filter["week"] != null){
                        $m = \Carbon::createFromFormat('Y-m-d',$filter["week"])->format('m');
                        $y = \Carbon::createFromFormat('Y-m-d',$filter["week"])->format('Y');
                        $d = \Carbon::createFromFormat('Y-m-d',$filter["week"])->format('d');
                        // list of date with 31 or 30
                        $dayOf31 = [1,3,5,7,9,10,12] ;
                        $dayOf30 = [2,4,6,8,11] ;
                        // for day of week
                        $d = $d - 7;
                        if($d < 0){
                             if((intVal($m) - 1)<0){
                                $y = (intVal($y) - 1);
                                $m = abs((intVal($m) - 1)%12);
                                 
                             }elseif((intVal($m) - 1)==0){
                                $y = intVal($y)-1;
                                $m = (((intVal($m) - 1) % 12)==0)?12:abs((intVal($m) - 1) % 12);
                                 
                             }else{
                                $m =  (intVal($m) - 1);
                                 
                             }
                            if(in_array(intVal($m),$dayOf31)){
                                $d = 31 - abs($d);
                            }else{
                                if(intVal($m) == 2){
                                    // Leap Years 1800 - 2400
                                    $mod = substr($y,3)%4;
                                    $numberOfDay = ($mod == 0)?29:28;
                                    $d = $numberOfDay - abs($d);
                                }else{
                                    $d = 30 - abs($d);
                                }
                            }
                        }elseif($d == 0){
                            if((intVal($m) - 1)<0){
                                $y = (intVal($y) - 1);
                                $m = abs((intVal($m) - 1)%12);
                             }elseif((intVal($m) - 1)==0){
                                $y = intVal($y)-1;
                                $m = (((intVal($m) - 1) % 12)==0)?12:abs((intVal($m) - 1) % 12);
                             }else{
                                $m =  (intVal($m) - 1);
                             }
                            if(in_array(intVal($m),$dayOf31)){
                                $d = 31;
                            }else{ 
                                 if(intVal($m) == 2){
                                     // Leap Years 1800 - 2400
                                    $mod = substr($y,3)%4;
                                    $numberOfDay = ($mod == 0)?29:28;
                                    $d = $numberOfDay - abs($d);
                                }else{
                                    $d = 30 - abs($d);
                                }
                            }
                        } 
                        $startD  = $y."-".$m."-".$d;
                        
                        $voucher->whereDate("date","<=",$filter["week"]); 
                        $voucher->whereDate("date",">=",$startD); 
                    }
                    $voucher     = $voucher->get();
                }else{
                    $voucher     = \App\Models\PaymentVoucher::where("business_id",$business_id)->orderBy("id","desc")->get();
                }
                
                
                
                if(count($voucher) == 0 ){ return false; }
                foreach($voucher as $ie){
                    $line      = [];
                    $payments  = $ie->payments;
                    foreach($payments as $lin){
                        $line[] = [
                            "id"                       => $lin->id,
                            "payment_ref_no"           => $lin->payment_ref_no,
                            "store_id"                 => $lin->store_id,
                            "transaction_id"           => ($lin->transaction)?(($lin->transaction->type == "purchase")?$lin->transaction->ref_no:$lin->transaction->invoice_no):$lin->transaction_id,
                            "amount"                   => $lin->amount,
                            "method"                   => $lin->method,
                            "transaction_no"           => $lin->transaction_no,
                            "card_transaction_number"  => $lin->card_transaction_number,
                            "card_number"              => $lin->card_number,
                            "card_type"                => $lin->card_type,
                            "card_holder_name"         => $lin->card_holder_name,
                            "card_month"               => $lin->card_month,
                            "card_year"                => $lin->card_year,
                            "card_security"            => $lin->card_security,
                            "cheque_number"            => $lin->cheque_number,
                            "bank_account_number"      => $lin->bank_account_number,
                            "paid_on"                  => $lin->paid_on,
                            "created_by"               => $lin->created_by,
                            "is_advance"               => $lin->is_advance,
                            // "payment_for"            => $lin->payment_for,
                            "note"                     => $lin->note,
                            "document"                 => $lin->document,
                            "payment_status"           => $lin->method,
                            // "contact_type"             => $lin->contact_type,
                            // "prepaid"                  => $lin->prepaid,
                            "amount_second_curr"       => $lin->amount_second_curr,
                            "check_id"                 => $lin->check_id,
                            "payment_voucher_id"       => $lin->payment_voucher_id,
                            "source"                   => $lin->source,
                            "balance"                  => abs($ie->amount - $lin->source),
                            "payment_for"              => ($lin->transaction)?$lin->transaction->final_total:0,
                            "link"                     => \URL::to("api/app/react/voucher/bill/view/")."/".$lin->transaction_id,

                        ];
                    }
                    
                    $documents = [];
                    if($ie->document != null){
                        foreach($ie->document as $k => $v) {
                            $documents[] = \URL::to($v);
                        }
                    }
                     
                    $name = "";
                    if($ie->account_type == 0){
                        $contacts  = \App\Account::where("contact_id",$ie->contact_id)->first(); 
                        $name      = ($contacts)?$contacts->name:""; 
                    }else{
                        $contacts  = \App\Account::find($ie->contact_id);
                        $name      = ($contacts)?$contacts->name:""; 
                    }

                    $list[] = [
                        "id"                  => $ie->id,
                        "amount"              => $ie->amount,
                        "type"                => ($ie->type == 1)?'Receipt Voucher':'Payment Voucher',
                        "ref_no"              => $ie->ref_no,
                        "contact_id"          => $name,
                        "account_id"          => ($ie->account)?$ie->account->name:$ie->account_id,
                        "text"                => $ie->text,
                        "document"            => $documents,
                        "currency_id"         => $ie->currency_id,
                        "amount_in_currency"  => $ie->amount_in_currency,
                        "exchange_price"      => $ie->exchange_price,
                        "currency_amount"     => $ie->currency_amount,
                        "date"                => $ie->date,
                        "payments"            => $line,
                    ];
                }
            }else{
                $voucher  = \App\Models\PaymentVoucher::find($id);
                if(empty($voucher)){ return false; }
                $line      = [];
                $payments  = $voucher->payments;
                foreach($payments as $lin){
                    $line[] = [
                        "id"                       => $lin->id,
                        "payment_ref_no"           => $lin->payment_ref_no,
                        "store_id"                 => $lin->store_id,
                        "transaction_id"           => ($lin->transaction)?(($lin->transaction->type == "purchase")?$lin->transaction->ref_no:$lin->transaction->invoice_no):$lin->transaction_id,
                        "amount"                   => $lin->amount,
                        "method"                   => $lin->method,
                        "transaction_no"           => $lin->transaction_no,
                        "card_transaction_number"  => $lin->card_transaction_number,
                        "card_number"              => $lin->card_number,
                        "card_type"                => $lin->card_type,
                        "card_holder_name"         => $lin->card_holder_name,
                        "card_month"               => $lin->card_month,
                        "card_year"                => $lin->card_year,
                        "card_security"            => $lin->card_security,
                        "cheque_number"            => $lin->cheque_number,
                        "bank_account_number"      => $lin->bank_account_number,
                        "paid_on"                  => $lin->paid_on,
                        "created_by"               => $lin->created_by,
                        "is_advance"               => $lin->is_advance,
                        // "payment_for"            => $lin->payment_for,
                        "note"                     => $lin->note,
                        "document"                 => $lin->document,
                        "payment_status"           => $lin->method,
                        // "contact_type"             => $lin->contact_type,
                        // "prepaid"                  => $lin->prepaid,
                        "amount_second_curr"       => $lin->amount_second_curr,
                        "check_id"                 => $lin->check_id,
                        "payment_voucher_id"       => $lin->payment_voucher_id,
                        "source"                   => $lin->source,
                        "balance"                  => abs($voucher->amount - $lin->source),
                        "payment_for"              => ($lin->transaction)?$lin->transaction->final_total:0,
                        "link"                     => \URL::to("api/app/react/voucher/bill/view")."/".$lin->transaction_id,

                    ];
                }
                $documents = [];
                if($voucher->document != null){
                    foreach($voucher->document as $k => $v) {
                        $documents[] = \URL::to($v);
                    }
                }
 
                $name = "";
                if($voucher->account_type == 0){
                    $contacts  = \App\Account::where("contact_id",$voucher->contact_id)->first(); 
                    if($contacts){
                        $name = $contacts->account_number . " | " . $contacts->name; 
                    }else{
                        $name = "--"; 
                    }
                }else{
                    $contacts  = \App\Account::find($voucher->contact_id);
                    if($contacts){
                        $name = $contacts->account_number . " | " . $contacts->name; 
                    }else{
                        $name = "--"; 
                    }
                }
                $list[] = [
                    "id"                  => $voucher->id,
                    "amount"              => $voucher->amount,
                    "type"                => ($voucher->type == 1)?'Receipt Voucher':'Payment Voucher',
                    "ref_no"              => $voucher->ref_no,
                    "contact_id"          => $voucher->contact_id,
                    "contactText"         => $name,
                    "account_id"          => $voucher->account_id,
                    "accountText"         => ($voucher->account)?$voucher->account->name:$voucher->account_id,
                    "text"                => $voucher->text,
                    "document"            => $documents,
                    "currency_id"         => $voucher->currency_id,
                    "amount_in_currency"  => $voucher->amount_in_currency,
                    "exchange_price"      => $voucher->exchange_price,
                    "currency_amount"     => $voucher->currency_amount,
                    "date"                => $voucher->date,
                    "payments"            => $line,
                ];
                
            }
            return $list; 
        }catch(Exception    $e){
            return false;
        }
    }
    // **4** GET REQUIRE DATA
    public static function requirement($user) {
        try{
            $list                 = [];   
            $allData              = [];  $accounts    = [];
            $currency             = [];  $contacts    = [];
            $allContact           = \App\Account::where("business_id",$user->business_id)->get();
            foreach( $allContact as $item){
                $contacts[$item->id] = $item->account_number . " | " . $item->name; 
            }   
            $amount = 1;
            
            $allCurrency           = \App\Models\ExchangeRate::where("business_id",$user->business_id)->get();
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
                    "value"     => $item->currency->currency . " | " . $item->currency->symbol,
                    "amount"    => $amount,
                    ] ; 
            }   
            $allAccount           = \App\Account::accounts($user->business_id);
            $allData["accounts"]  = GlobalUtil::arrayToObject($allAccount);
            $allData["contact"]   = GlobalUtil::arrayToObject($contacts);
            $allData["currency"]  = $currency ;
           
            return $allData; 
        }catch(Exception $e){
           return false; 
        }
    }

    
}
