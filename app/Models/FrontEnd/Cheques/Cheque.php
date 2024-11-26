<?php

namespace App\Models\FrontEnd\Cheques;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Utils\ProductUtil;
use App\Models\FrontEnd\Utils\GlobalUtil;
class Cheque extends Model
{
    use HasFactory,SoftDeletes;
    // *** REACT FRONT-END CHEQUE *** // 
    // **1** ALL CHEQUE
    public static function getCheque($user,$filter=null) {
        try{
            $list          = [];
            $business_id   = $user->business_id;
            if($filter != null){
                $data          = Cheque::allData("all",null,$business_id,$filter);
            }else{
                $data          = Cheque::allData("all",null,$business_id);
            }
            if($data == false){ return false;}
            $list["value"]       = $data;
            $list["requirement"] = Cheque::requirement($user);
            return $list;
        }catch(Exception $e){
            return false;
        }
    }
    // **2** CREATE CHEQUE
    public static function createCheque($user,$data) {
        try{
            $business_id        = $user->business_id;
            $create             = Cheque::requirement($user);
            return $create;
        }catch(Exception $e){
            return false;
        }
    }
    // **3** EDIT CHEQUE
    public static function editCheque($user,$data,$id) {
        try{
            $business_id      = $user->business_id;
            $data             = Cheque::allData(null,$id,$business_id);
            $edit             = Cheque::requirement($user);
            $account            = \App\Models\Check::find($id);
            // $list["check"]    = $check;
            if(!$account){ return false; }

                // ................. BILLS
                $bills       = [];
                $all_list    = [];
                if($account->account_type == 0){
                   $contact_idd  =  $account->contact_id;
                }else{
                    $act         = \App\Account::where("id",$account->contact_id)->first();
                    $contact_idd =  $act->contact_id;
                } 
                if($account){
                    $types = [];
                    if($account->type == 0){
                        $types = ["sale","sell_return"];
                    }elseif($account->type == 1){
                        $types = [ "purchase","purchase_return" ];
                    }
                    $bills       = \App\Transaction::whereIn("type",$types)->where("contact_id",$contact_idd)->get();
                }
                $all_list[] = [
                    "status"        => "" ,    
                    "date"          => "" ,    
                    "check"         => "" ,    
                    "reference_no"  => "" ,    
                    "contact_name"  => "" ,    
                    "invoice_status"=> "" ,    
                    "store"         => "" ,    
                    "final_total"   => "" ,    
                    "pay_due"       => "" ,    
                    "total_payment" => "" ,    
                    "bill_id"       => "" ,    
                    "payment_id"    => "" ,  
                    "previous_payment" => "" ,
                ];
                $vch_info  =   \App\Models\Check::find($id);
                $remaining =   ($vch_info)?$vch_info->amount:0;
                foreach($bills as $ies){
                    $transaction_payment  = \App\TransactionPayment::where("transaction_id",$ies->id)->sum("amount");
                    if($ies->payment_status == "paid"){
                        $payment_id           = \App\TransactionPayment::where("transaction_id",$ies->id)->where("check_id",$id)->first();
                        if(!empty($payment_id)){
                            $this_value           = ($payment_id)?$payment_id->amount:0;
                            $pr_payment           =  $transaction_payment - $this_value;
                            if((round(doubleVal($ies->final_total),2)  - $transaction_payment)>0){
                                $pay_status       = ($payment_id)?0:1;
                                $check            = ($payment_id)?true:false;
                                $am = ($payment_id)?$payment_id->amount:0;
                                $previous_payment = floatVal($pr_payment);
                            }else{
                                $check            = ($payment_id)?true:false;
                                $pay_status       = 0;
                                $am = ($payment_id)?$payment_id->amount:0;
                                $previous_payment = floatVal($pr_payment);
                            }
                            $all_list[] = [
                                "status"           => $pay_status , 
                                "date"             => $ies->transaction_date ,    
                                "reference_no"     => ($ies->type == "purchase" || $ies->type == "purchase_return")?$ies->ref_no:$ies->invoice_no ,  
                                "check"            => $check ,
                                "contact_name"     => ($ies->contact)?$ies->contact->name:"" ,    
                                "invoice_status"   => $ies->status ,    
                                "store"            => ($ies->warehouse)?$ies->warehouse->name:"" ,    
                                "final_total"      => round(doubleVal($ies->final_total),2) ,    
                                "pay_due"          => ((round(doubleVal($ies->final_total),2)  - $transaction_payment)>0)?round(doubleVal($ies->final_total),2)  -  $transaction_payment:0,    
                                "total_payment"    => round($transaction_payment,2),
                                "bill_id"          => $ies->id,
                                "payment_id"       => ($payment_id)?$payment_id->id:"",
                                "previous_payment" => $previous_payment,      
                            ];
                        }
                    }else{
                        $payment_id           = \App\TransactionPayment::where("transaction_id",$ies->id)->where("check_id",$id)->first();
                        $this_value           = ($payment_id)?$payment_id->amount:0;
                        $pr_payment           =  $transaction_payment - $this_value;
                        if((round(doubleVal($ies->final_total),2)  - $transaction_payment)>0){
                            $pay_status       = ($payment_id)?0:1;
                            $check            = ($payment_id)?true:false;
                            $am = ($payment_id)?$payment_id->amount:0;
                            $previous_payment = floatVal($pr_payment);
                        }else{
                            $check            = ($payment_id)?true:false;
                            $pay_status       = 0;
                            $am = ($payment_id)?$payment_id->amount:0;
                            $previous_payment = floatVal($pr_payment);
                        }
                     
                     
                        $all_list[] = [
                            "status"           => $pay_status , 
                            "date"             => $ies->transaction_date ,    
                            "reference_no"     => ($ies->type == "purchase" || $ies->type == "purchase_return")?$ies->ref_no:$ies->invoice_no ,  
                            "check"            => $check ,
                            "contact_name"     => ($ies->contact)?$ies->contact->name:"" ,    
                            "invoice_status"   => $ies->status ,    
                            "store"            => ($ies->warehouse)?$ies->warehouse->name:"" ,    
                            "final_total"      => round(doubleVal($ies->final_total),2) ,    
                            "pay_due"          => ((round(doubleVal($ies->final_total),2)  - $transaction_payment)>0)?round(doubleVal($ies->final_total),2)  -  $transaction_payment:0,    
                            "total_payment"    => round($transaction_payment,2),
                            "bill_id"          => $ies->id,
                            "payment_id"       => ($payment_id)?$payment_id->id:"",
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
    // **4** STORE CHEQUE
    public static function storeCheque($user,$data,$request) {
        try{
            \DB::beginTransaction();
            $business_id         = $user->business_id;
            $output              = Cheque::createNewCheque($user,$data,$request);
            if($output == false){ return false; } 
            \DB::commit();
            return $output;
        }catch(Exception $e){
            return false;
        }
    }
    // **5** UPDATE CHEQUE
    public static function updateCheque($user,$data,$id,$request) {
        try{
            \DB::beginTransaction();
            $business_id         = $user->business_id;
            $output              = Cheque::updateOldCheque($user,$data,$id,$request);
            \DB::commit();
            return $output;
        }catch(Exception $e){
            return false;
        }
    }
    // **6** DELETE CHEQUE
    public static function deleteCheque($user,$id) {
        try{
            \DB::beginTransaction();
            $business_id     = $user->business_id;
            $check           = \App\Models\Check::find($id);
            if(!$check){ return false; }
            \App\AccountTransaction::where('check_id',$id)->delete();
            $payment =  \App\TransactionPayment::where("check_id",$id)->first();
            
            if(!empty($payment)){
                $payment->amount = 0;
                $payment->update();
                $total_paid = $payment->amount;
            }else{
                $total_paid = 0;
            }

            $final_amount = \App\Transaction::find($check->transaction_id);
            if(!empty($final_amount)){
                $balance = $final_amount->final_total;
            
                $status  = 'due';
                if ($balance <= $total_paid) {
                    $status = 'paid';
                } elseif ($total_paid > 0 && $balance > $total_paid) {
                    $status = 'partial';
                }
                $final_amount->payment_status = $status;
                $final_amount->update();

            } 
            if(!empty($payment)){
                $payment->delete();
            }
            $check->delete();
            \DB::commit();
            return true;
        }catch(Exception $e){
            return false;
        }
    }
    // **7** BILLS CHEQUE
    public static function billCheque($user,$data,$id) {
        try{
            \DB::beginTransaction();
            $business_id = $user->business_id;
            $types       = $data["type"];
            $account     = \App\Account::where("id",$id)->first();
            $bills       = [];
            $all_list    = [];
            if($account){
                $type_list   = ($types == "in")?["sale","sell_return"]:(($types == "out")?["purchase","purchase_return"]:[]);
                $bills       = \App\Transaction::whereIn("type",$type_list)->where("payment_status","!=","paid")->where("contact_id",$account->contact_id)->get();
            }
            if(count($bills) == 0){ return false; }
            foreach($bills as $ies){
                $transaction_payment  = \App\TransactionPayment::where("transaction_id",$ies->id)->where("return_payment","=",0)->sum("amount");
                $payment_id           = null;
                $this_value           = ($payment_id)?$payment_id->amount:0;
                $pr_payment           =  $transaction_payment - $this_value;
                if((round(doubleVal($ies->final_total),2)  - $transaction_payment)>0){
                    $pay_status       = ($payment_id)?0:1;
                    $check            = ($payment_id)?true:false;
                    $am = ($payment_id)?$payment_id->amount:0;
                    $previous_payment = floatVal($pr_payment);
                }else{
                    $check            = ($payment_id)?true:false;
                    $pay_status       = 0;
                    $am = ($payment_id)?$payment_id->amount:0;
                    $previous_payment = floatVal($pr_payment);
                }
                
                
                $all_list[] = [
                    "status"           => $pay_status , 
                    "date"             => $ies->transaction_date ,    
                    "reference_no"     => ($ies->type == "purchase" || $ies->type == "purchase_return")?$ies->ref_no:$ies->invoice_no ,  
                    "check"            => $check ,
                    "contact_name"     => ($ies->contact)?$ies->contact->name:"" ,    
                    "invoice_status"   => $ies->status ,    
                    "store"            => ($ies->warehouse)?$ies->warehouse->name:"" ,    
                    "final_total"      => round(doubleVal($ies->final_total),2) ,    
                    "pay_due"          => ((round(doubleVal($ies->final_total),2)  - $transaction_payment)>0)?round(doubleVal($ies->final_total),2)  -  $transaction_payment:0,    
                    "total_payment"    => round($transaction_payment,2),
                    "bill_id"          => $ies->id,
                    "payment_id"       => ($payment_id)?$payment_id->id:"",
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
    // **8** VIEW CHEQUE
    public static function viewCheque($user,$data,$id) {
        try{
            \DB::beginTransaction();
            $business_id = $user->business_id;
            $voucher     = Cheque::allData(null,$id,$business_id);
            if($voucher  == false){ return false; } 
            \DB::commit();
            return $voucher;
        }catch(Exception $e){
            return false;
        }
    }
    // **9** PRINT CHEQUE
    public static function printCheque($user,$data,$id) {
        try{
            \DB::beginTransaction();
            $business_id = $user->business_id;
            $voucher     = \App\Models\Check::find($id);
            if(empty($voucher)){ return false; }
            $voucher     = ($voucher->type == 0)?  \URL::to('reports/i-ch/'.$voucher->id) : \URL::to('reports/o-ch/'.$voucher->id) ; 
            \DB::commit();
            return $voucher;
        }catch(Exception $e){
            return false;
        }
    }
    // **10** CURRENCY CHEQUE
    public static function currencyCheque($user,$data,$id) {
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
    // **11** ENTRY CHEQUE
    public static function entryCheque($user,$data,$id) {
        try{
        \DB::beginTransaction();
        $list_of_entry        =  [];$list_of_entry2        =  [];
        $line                 =  [];$array=[];
        // $data                 =  \App\Models\PaymentVoucher::find($id);
        $business_id          =  $user->business_id;
        // ..................................................................1........
        $entry_id             =  \App\AccountTransaction::where('check_id',$id)->whereNull("for_repeat")->where('amount','>',0)->groupBy("entry_id")->pluck("entry_id");
        $entry                =  \App\Models\Entry::whereIn("id",$entry_id)->get();
        $reference            = "";
        foreach($entry as $items){
            $line2                             =  [];
            $reference                         = $items->ref_no_e;
            $list_of_entry["id"]               = $items->id;
            $list_of_entry["entry_reference"]  = $items->refe_no_e;
            
                // ..................................................................2........
            $allData              =  \App\AccountTransaction::where('check_id',$id)->where('entry_id',$items->id)->whereNull("for_repeat")->where('amount','>',0)->get();
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
                $list_of_entry2["check_id"]               = $items->check_id ;
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
        $array["entries"]          = $line;
        \DB::commit();
        return $array;
    }catch(Exception $e){
        return false;
    }
}
    // **12** COLLECT CHEQUE
    public static function collectCheque($user,$data,$id,$request) {
        try{
            
            \DB::beginTransaction();
            $business_id = $user->business_id;
            $Check        = \App\Models\Check::find($id);
            if(empty($Check)){ return false; }
            \App\Models\Check::add_action($id,'collect');
            $Check->status             =  1;
            $Check->collecting_date    =  ($request->date)?$request->date:date('Y-m-d');
            $Check->collect_account_id =  $request->account_id;
            $Check->update();
            $type                     = ($Check->type == 1) ?'credit':'debit';
            $re_type                  = ($Check->type == 1) ?'debit' :'credit';
            $credit_data = [
                'amount'              => $Check->amount,
                'account_id'          => $request->account_id,
                'type'                => $type,
                'sub_type'            => 'deposit',
                'operation_date'      => $request->date,
                'created_by'          => $user->id,
                'note'                => 'collecting cheque',
                'check_id'            => $Check->id,
                'transaction_id'      => $Check->transaction_id
            ];
             \App\AccountTransaction::createAccountTransaction($credit_data);
            //update old account
            $setting =  \App\Models\SystemAccount::where('business_id',$Check->business_id)->first();

            $main_id     =  ($Check->type == 1)?$setting->cheque_debit:$setting->cheque_collection;
            $credit_data = [
                'amount'         => $Check->amount,
                'account_id'     => $main_id,
                'type'           => $re_type,
                'sub_type'       => 'deposit',
                'operation_date' => $request->date,
                'created_by'     => $user->id,
                'note'           => 'collecting cheque',
                'check_id'       => $Check->id,
                'transaction_id' => $Check->transaction_id
            ];
            \App\AccountTransaction::createAccountTransaction($credit_data);
            $type = "Collect Cheque";
            \App\Models\Entry::create_entries($Check,$type);
            \DB::commit();
            return true;
        }catch(Exception $e){
            return false;
        }
    }
    // **13** UN_COLLECT CHEQUE
    public static function unCollectCheque($user,$data,$id) {
        try{
            \DB::beginTransaction();
            $business_id = $user->business_id;
            $Check        = \App\Models\Check::find($id);
            if(empty($Check)){ return false; }
            $Check->status = 4;
            $Check->update();
            // reference collecting account
            \App\Models\Check::un_collect($id,$user);
            $type ="UNCollect Cheque";
            \App\Models\Entry::create_entries($Check,$type);
            \DB::commit();
            return true;
        }catch(Exception $e){
            return false;
        }
    }
    // **14** REFUND CHEQUE
    public static function refundCheque($user,$data,$id) {
        try{
            \DB::beginTransaction();
            $business_id = $user->business_id;
            $Check        = \App\Models\Check::find($id);
            if(empty($Check)){ return false; }
            \App\Models\Check::add_action($id,'refund');
            $old_state    =  $Check->status;
            $Check->status =  2;
            $Check->update();
            //Check::update_status($data->status,$old_state);
            if($Check->account_type == 0){
                \App\Models\Check::refund($id,null,$user);
            }else{
                \App\Models\Check::refund($id,1,$user);
            }
            $pay  = \App\TransactionPayment::where("check_id",$id)->first();
            if(!empty($pay)){
                $account_transaction = \App\AccountTransaction::where("note","refund Collect")
                                        ->where("check_id",$id)
                                        ->update([
                                            "transaction_id" =>$pay->transaction_id
                                        ]);
                $pay->amount   = 0;
                $pay->update();
                $tr = \App\Transaction::find($pay->transaction_id);
                \App\Services\Cheque\Bill::update_status($tr);
            }
            $type = "Refund Cheque";
            \App\Models\Entry::create_entries($Check,$type);
            \DB::commit();
            return true;
        }catch(Exception $e){
            return false;
        }
    }
    // **15** DELETE COLLECT CHEQUE
    public static function deleteCollectCheque($user,$data,$id)
    {
        try{
            \DB::beginTransaction();
            $business_id  = $user->business_id;
            $Check        = \App\Models\Check::find($id);
            if(empty($Check)){ return false; }
            $entry = \App\AccountTransaction::orderBy("id","desc")->where("note","collecting cheque")->where('check_id',$id)->first();
            
            $Check->status = 3;
            $Check->update();
            // delete collecting account
            $x1   =  \App\AccountTransaction::where('account_id',$Check->collect_account_id)
                                                ->where('check_id',$id)
                                                ->where("entry_id",$entry->entry_id)
                                                ->where('type','credit')
                                                ->delete();
            $x2   =  \App\AccountTransaction::where('account_id',$Check->account_id)
                                                ->where('check_id',$id)
                                                ->where("entry_id",$entry->entry_id)
                                                ->where('type','debit')
                                                ->delete();

            $entry_id = \App\Models\Entry::find($entry->entry_id);
            $entry_id->delete();
            \DB::commit();
            return true;
        }catch(Exception $e){
            return false;
        }
    }
    // **16** ATTACH CHEQUE
    public static function attachCheque($user,$data,$id) {
         try{
            \DB::beginTransaction();
            $list_of_attach       =  []; 
            $business_id          =  $user->business_id;
            // ..................................................................1........
            $check              =  \App\Models\Check::find($id);
            // ..................................................................2........
            $attach     =  $check->document ;
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
    // **1** CREATE CHEQUE
    public static function createNewCheque($user,$data,$request) {
        try{
            $productUtil    =  new ProductUtil();
            $business_id    =  $user->business_id;
            $location       =  \App\BusinessLocation::where("business_id",$business_id)->first();             
            $setting        =  \App\Models\SystemAccount::where('business_id',$business_id)->first();
            $bills_         =  $request->only(['bill_id','bill_amount']);
            $id_trans       =  null;$transaction    =  null;

            if(!empty($bills_)){
                foreach($bills_["bill_id"] as $bl){ $id_trans =  $bl;  if( $transaction == null ) { $transaction = $bl;  }else { $transaction .= ",".$bl ; } }
            }
    
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

            $ref_count                =  $productUtil->setAndGetReferenceCount("Cheque",$business_id);
            $ref_no                   =  $productUtil->generateReferenceNumber("Cheque" , $ref_count,$business_id);
            $id                       =  ($request->cheque_type == 0)?$setting->cheque_collection:$setting->cheque_debit;
            $data                     =  new \App\Models\Check;
            $data->location_id        =  (!empty($location))?$location->id:1;
            $data->contact_id         =  $request->contact_id;
            $data->business_id        =  $business_id;
            $data->cheque_no          =  $request->cheque_no;
            $data->amount             =  $request->amount;
            $data->contact_bank_id    =  $request->bank_id;
            $data->type               =  $request->cheque_type;
            $data->write_date         =  $request->write_date;
            $data->due_date           =  $request->due_date;
            $data->note               =  $request->note;
            $data->currency_id        =  $request->currency_id;
            $data->exchange_price     =  ($request->currency_id != null)?$request->currency_id_amount:null;
            $data->amount_in_currency =  ($request->currency_id != null && $request->currency_id_amount != 0)?$request->amount / $request->currency_id_amount:null;
            $data->account_type       =  1;
            $data->account_id         =  $id;
            $data->document           =  json_encode($document_expense) ;
            $data->ref_no             =  $ref_no;
            $data->account_type       =  1;
            $data->save();
    
            \App\Models\Check::add_action($data->id,'added');
            $type        = ($data->type == 0)?'debit':'credit';
            $credit_data = [
                'amount'             => $request->amount,
                'account_id'         => $id,
                'transaction_id'     => $id_trans,
                'type'               => $type,
                'sub_type'           => 'deposit',
                'operation_date'     => $request->write_date,
                'created_by'         => $user->id,
                'note'               => 'added cheque',
                'check_id'           => $data->id,
                'transaction_array'  => $transaction,
            ];
            $credit  = \App\AccountTransaction::createAccountTransaction($credit_data);
            \App\Models\Check::contact_effect($data->id,$transaction,$id_trans,"all",$request->contact_id,$user->id);
            $bills   =  $request->only([
                            'bill_id','bill_amount'
                        ]);
            if ($request->bill_id) {
                \App\Services\Cheque\Bill::pay_transaction($data->id,$bills,$user);
            }
            $str_arr = explode (",", $transaction); 
            $types   = "check";
            \App\Models\Entry::create_entries($data,$types);
    
            return true; 
        }catch(Exception $e){
            return false;
        }
    }
    // **2** UPDATE CHEQUE
    public static function updateOldCheque($user,$data,$id,$request) {
        try{
            $business_id               = $user->business_id;
            $check                     = \App\Models\Check::find($id);
            $old_contact_id            = $check->contact_id;
            $check->contact_id         = $request->contact_id;
            $check->business_id        = $business_id;
            $check->cheque_no          = $request->cheque_no;
            $check->amount             = $request->amount;
            $check->contact_bank_id    = $request->bank_id;
            $check->write_date         = $request->write_date;
            $check->due_date           = $request->due_date;
            $check->note               = $request->text;
            $check->currency_id        = $request->currency_id;
            $check->exchange_price     = ($request->currency_id != null)?$request->currency_id_amount:null;
            $check->amount_in_currency = ($request->currency_id != null && $request->currency_id_amount != 0)?$request->amount / $request->currency_id_amount:null;
            // $check->account_id      = $request->account_id;
            
            $old_document              = $check->document;
            if($old_document == null){
                $old_document = [];
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
                $check->document        =  json_encode($old_document) ;
            }

            if($old_contact_id != $request->contact_id ){
                $check->account_type    =  1;
            }

            $check->save();
            \App\AccountTransaction::where('check_id',$id)->update([
                    'note'           => $check->note,
                    'amount'         => $check->amount,
                    'operation_date' => $request->write_date
                ]);

            if($old_contact_id != $request->contact_id ){
                $account     =  \App\Account::where('id',$check->contact_id)->first();
                $old_account =  \App\Account::where('id',$old_contact_id)->first();
                \App\AccountTransaction::where('check_id',$id)
                                        ->where('account_id',$old_account->id)
                                        ->update([
                                            'note'           => $check->note,
                                            'account_id'     => $account->id,
                                            'operation_date' => $request->write_date
                                        ]);
            }
            
            if($request->contact_id != $old_contact_id){
                $allPayment = \App\TransactionPayment::where("check_id",$id)->get();
                foreach($allPayment as $i){
                    $transaction = \App\Transaction::find($i->transaction_id);
                    $i->delete();
                    \App\Services\Cheque\Bill::update_status($transaction);
                }
            }  
            
            $transactionPay    = \App\TransactionPayment::where('check_id', $id)->first();
            if($transactionPay){
                $old_payment = $transactionPay->replicate();
                $parent      = \App\Models\ParentArchive::save_payment_parent($transactionPay->id,"Edit",$old_payment);
            }
            if($transactionPay){
                $sum           =  \App\TransactionPayment::where('transaction_id',$transactionPay->transaction_id)->sum("amount");
                $final         =  $sum - $transactionPay->amount ;
                $transaction   =  \App\Transaction::find($transactionPay->transaction_id);
                $total_bill    =  $transaction->final_total;
                //...... here no previous payment 
                if($final == 0){
                    $margin_total = round($total_bill,config("constants.currency_precision")) - round($request->amount,config("constants.currency_precision"));
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
                $transactionPay->payment_for = $data["contact_id"];
                $transactionPay->amount      = $payment_amount_final;
                $transactionPay->source      = $payment_amount_final;
                $transactionPay->save();

                // \App\TransactionPayment::where('check_id', $id)->update([
                //     'amount' => $payment_amount_final,
                //     'source' => $payment_amount_final
                // ]);
            } 
                $bills   =  $request->only([
                                'bill_id','bill_amount','old_bill_id','old_bill_amount','payment_id'
                            ]);
                \App\Services\Cheque\Bill::update_pay_transaction($check->id,$bills,$user);
            
            
            return true; 
        }catch(Exception $e){
            return false;
        }
    }
    // **3** GET  CHEQUE
    public static function allData($type=null,$id=null,$business_id,$filter=null) {
        try{
            $list   = [];
            if($type != null){
                if($filter != null){
                    $check     = \App\Models\Check::where("business_id",$business_id)->orderBy("id","desc");
                    if($filter["dueDateTo"] != null){
                        $check->whereDate("due_date","<=",$filter["dueDateTo"]);
                    }
                    if($filter["dueDateFrom"] != null){
                        $check->whereDate("due_date",">=",$filter["dueDateFrom"]);
                    }
                    if($filter["writeDateTo"] != null){
                        $check->whereDate("write_date","<=",$filter["writeDateTo"]);
                    }
                    if($filter["writeDateFrom"] != null){
                        $check->whereDate("write_date",">=",$filter["writeDateFrom"]); 
                    }
                    if($filter["month"] != null){
                        $m = \Carbon::createFromFormat('Y-m-d',$filter["month"])->format('m');
                        $y = \Carbon::createFromFormat('Y-m-d',$filter["month"])->format('Y');
                        $startD  = $y."-".$m."-01";
                        $check->whereDate("write_date","<=",$filter["month"]); 
                        $check->whereDate("write_date",">=",$startD); 
                    }
                    if($filter["day"] != null){
                        $check->whereDate("write_date","=",$filter["day"]); 
                    }
                    if($filter["year"] != null){
                        $m = \Carbon::createFromFormat('Y-m-d',$filter["year"])->format('m');
                        $y = \Carbon::createFromFormat('Y-m-d',$filter["year"])->format('Y');
                        $startD  = $y."-01-01";
                         
                        $check->whereDate("write_date","<=",$filter["year"]); 
                        $check->whereDate("write_date",">=",$startD); 
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
                        $check->whereDate("write_date","<=",$filter["week"]); 
                        $check->whereDate("write_date",">=",$startD); 
                    }
                    
                    $check = $check->get();
                  
                }else{
                    $check     = \App\Models\Check::where("business_id",$business_id)->get();
                }
                
                if(count($check) == 0 ){ return false; }
                foreach($check as $ie){
                    $line      = [];
                    $increase_pay = 0;
                    $payments  = $ie->payments;
                    $list_of_attach       =  [];
                    $attach     =  $ie->document ;
                    if($attach != null){
                        foreach($attach as $doc){
                            $list_of_attach[]  =  \URL::to($doc);
                        } 
                    }
                    foreach($payments as $lin){
                        
                        $line[] = [
                            "id"                       => $lin->id,
                            "payment_ref_no"           => $lin->payment_ref_no,
                            "store_id"                 => $lin->store_id,
                            "transaction_id"           => ($lin->transaction)?(($lin->transaction->type == "purchase" || $lin->transaction->type == "return_purchase")?$lin->transaction->ref_no:$lin->transaction->invoice_no):"",
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
                            "note"                     => $ie->note,
                            "document"                 => $lin->document,
                            // "contact_type"             => $lin->contact_type,
                            // "prepaid"                  => $lin->prepaid,
                            "amount_second_curr"       => $lin->amount_second_curr,
                            "check_id"                 => $lin->check_id,
                            "payment_voucher_id"       => $lin->payment_voucher_id,
                            "source"                   => $lin->source,
                           "balance"                  => abs($lin->transaction->final_total - ($ie->amount + $increase_pay)),
                            "payment_for"              => ($lin->transaction)?$lin->transaction->final_total:0,
                            "link"                     => \URL::to("api/app/react/voucher/bill/view/")."/".$lin->transaction_id,
                        ];
                        $increase_pay += $lin->amount;
                    }
                    $contactText        = '--';
                    $collectAccountText = "";
                    if($ie->account_type == 0){
                        $accounts    = \App\Account::where("contact_id",$ie->contact_id)->first();
                        $collectText = \App\Account::where("id",$ie->collect_account_id)->first();
                        if($accounts){
                            $contactText = $accounts->account_number . " | " . $accounts->name; 
                        }else{
                            $contactText = "--"; 
                        }
                        if($collectText){ $collectAccountText = $collectText->name ; }
                    }else{
                        $accounts    = \App\Account::where("id",$ie->contact_id)->first();
                        $collectText = \App\Account::where("id",$ie->collect_account_id)->first();
                        if($accounts){
                          $contactText =   $accounts->account_number . " | " . $accounts->name ;
                        }else{
                           $contactText = "--"; 
                        }
                        if($collectText){ $collectAccountText = $collectText->name ; }
                    }
                    $list[] = [
                        "id"                  => $ie->id,
                        "collect_account_id"  => $ie->collect_account_id,
                        "collectAccountText"  => $collectAccountText,/** */
                        "transaction_id"      => $ie->transaction_id,/** */
                        "ref_no"              => $ie->ref_no,
                        "contact_id"          => $ie->contact_id,
                        "contactText"         => $contactText,/** */
                        "account_id"          => $ie->account_id,
                        "accountText"         => ($ie->account)?$ie->account->name:$ie->account_id,/** */
                        "contact_bank_id"     => $ie->contact_bank_id,
                        "type"                => $ie->type,
                        "amount"              => $ie->amount,
                        "status"              => $ie->status,
                        "write_date"          => $ie->write_date,
                        "due_date"            => $ie->due_date,
                        "cheque_no"           => $ie->cheque_no,
                        "collecting_date"     => $ie->collecting_date,
                        "note"                => $ie->note,
                        "document"            => $list_of_attach,
                        "currency_id"         => $ie->currency_id,
                        "amount_in_currency"  => $ie->amount_in_currency,
                        "exchange_price"      => $ie->exchange_price,
                        "currency_amount"     => $ie->currency_amount,
                        "status_name"         => $ie->status_name,
                        "account_type"        => $ie->account_type,
                        "created_at"          => $ie->created_at->format("Y-m-d h:i:s a"),
                        "payments"            => $line,
                    ];
                }
            }else{
                $check  = \App\Models\Check::find($id);
                if(empty($check)){ return false; }
                $line      = [];
                $increase_pay = 0;
                $payments  = $check->payments;
                $list_of_attach       =  [];
                    $attach     =  $check->document ;
                    if($attach != null){
                        foreach($attach as $doc){
                            $list_of_attach[]  =  \URL::to($doc);
                        } 
                    }
                foreach($payments as $lin){
                   
                    $line[] = [
                        "id"                       => $lin->id,
                        "payment_ref_no"           => $lin->payment_ref_no,
                        "store_id"                 => $lin->store_id,
                        "transaction_id"           => ($lin->transaction)?(($lin->transaction->type == "purchase" || $lin->transaction->type == "return_purchase")?$lin->transaction->ref_no:$lin->transaction->invoice_no):"",
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
                        // "contact_type"             => $lin->contact_type,
                        // "prepaid"                  => $lin->prepaid,
                        "amount_second_curr"       => $lin->amount_second_curr,
                        "check_id"                 => $lin->check_id,
                        "payment_voucher_id"       => $lin->payment_voucher_id,
                        "source"                   => $lin->source,
                        "balance"                  => abs($lin->transaction->final_total - ($check->amount + $increase_pay)),
                        "payment_for"              => ($lin->transaction)?$lin->transaction->final_total:0,
                        "link"                     => \URL::to("api/app/react/voucher/bill/view")."/".$lin->transaction_id,
                    ];
                    $increase_pay += $lin->amount;
                }
                $contactText        = '--';
                $collectAccountText = "";
                if($check->account_type == 0){
                    $accounts    = \App\Account::where("contact_id",$check->contact_id)->first();
                    $collectText = \App\Account::where("id",$check->collect_account_id)->first();
                    if($accounts){
                        $contactText = $accounts->account_number . " | " . $accounts->name; 
                    }else{
                        $contactText = "--"; 
                    }
                    if($collectText){ $collectAccountText = $collectText->name ; }
                }else{
                    $accounts    = \App\Account::where("id",$check->contact_id)->first();
                    $collectText = \App\Account::where("id",$check->collect_account_id)->first();
                    if($accounts){
                        $contactText = $accounts->account_number . " | " . $accounts->name; 
                    }else{
                        $contactText = "--"; 
                    }
                    if($collectText){ $collectAccountText = $collectText->name ; }
                }
                $list[] = [
                    "id"                  => $check->id,
                    "collect_account_id"  => $check->collect_account_id,
                    "collect_accountText" => $collectAccountText,
                    "transaction_id"      => $check->transaction_id,
                    "ref_no"              => $check->ref_no,
                    "contact_id"          => $check->contact_id,
                    "contactText"         => $contactText,
                    "account_id"          => $check->account_id,
                    "accountText"         => ($check->account)?$check->account->name:$check->account_id,
                    "contact_bank_id"     => $check->contact_bank_id,
                    "type"                => $check->type,
                    "amount"              => $check->amount,
                    "status"              => $check->status,
                    "write_date"          => $check->write_date,
                    "due_date"            => $check->due_date,
                    "cheque_no"           => $check->cheque_no,
                    "collecting_date"     => $check->collecting_date,
                    "note"                => $check->note,
                    "document"            => $list_of_attach,
                    "currency_id"         => $check->currency_id,
                    "amount_in_currency"  => $check->amount_in_currency,
                    "exchange_price"      => $check->exchange_price,
                    "currency_amount"     => $check->currency_amount,
                    "status_name"         => $check->status_name,
                    "account_type"        => $check->account_type,
                    "created_at"          => $check->created_at->format("Y-m-d h:i:s a"),
                    "payments"            => $line,
                ];
                
            }
            return $list; 
        }catch(Exception    $e){
            return false;
        }
    }
    // **4** REQUIREMENT
    public static function requirement($user) {
        try{
            $list                 = [];   
            $allData              = [];  $accounts    = [];
            $currency             = [];  $contacts    = [];$contactBanks    = [];
            $allContact           = \App\Account::where("business_id",$user->business_id)->get();
            $cheque_account       = [];
            $systemAccount        = \App\Models\SystemAccount::where("business_id",$user->business_id)->first();
            $AllContactBanks      = \App\Models\ContactBank::where("business_id",$user->business_id)->get();
            $cheque_account[]     = $systemAccount->cheque_debit;
            $cheque_account[]     = $systemAccount->cheque_collection;
            $allAccount           = [] ;
            foreach( $allContact as $item ){
                $contacts[$item->id] = $item->account_number . " | " . $item->name; 
                if(in_array($item->id,$cheque_account)){
                    $checkSystemAccount        = \App\Models\SystemAccount::where("business_id",$user->business_id)->where("cheque_debit",$item->id)->first();
                    $type = ($checkSystemAccount)?"chequeOut":"chequeIn";
                    $allAccount[] = [
                      "type"   =>  $type,
                      "id"     =>  $item->id,
                      "value"  =>  $item->account_number . " | " . $item->name
                    ]; 
                }
            }   
            $allCurrency           = \App\Models\ExchangeRate::where("business_id",$user->business_id)->get();
            foreach( $allCurrency as $item){
                $currency[] = [ 
                    "id"     => $item->id,
                    "value"  => $item->currency->currency . " | " . $item->currency->symbol,
                    "amount" => $item->amount,
                ]; 
            }
            foreach( $AllContactBanks as $item){
                $contactBanks[$item->id] =  $item->name ; 
            }
            $allData["account_collect"]      = GlobalUtil::arrayToObject(\App\Account::items(3,$user));   
            // $allAccount                      = \App\Account::accounts($user->business_id);
            $allData["accounts"]             =  $allAccount ;
            $allData["contact"]              = GlobalUtil::arrayToObject($contacts);
            $allData["contact_banks"]        = GlobalUtil::arrayToObject($contactBanks);
            $allData["currency"]             = $currency ;
           
            return $allData; 
        }catch(Exception $e){
           return false; 
        }
    }
    
}
