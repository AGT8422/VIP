<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use App\Utils\ModuleUtil;
use App\Utils\ProductUtil;

class Entry extends Model
{
    use HasFactory,SoftDeletes;
 

    

    public static function info($business_id)
    {
        $total_entries = Entry::where("business_id",$business_id)->select(["ref_no_e","refe_no_e",])->get();
        $info    = []; $state   = [
            "Purchase"=>"Purchase",
            "Return Purchase"=>"Return Purchase",
            "Production"=>"Production",
            "Sale"=>"Sale",
            "Return Sale"=>"Return Sale",
            "Receipt Voucher"=>"Receipt Voucher",
            "Payment Voucher"=>"Payment Voucher",
            "Journal Voucher"=>"Journal Voucher",
            "Expense Voucher"=>"Expense Voucher",
            "Shipping"=>"Shipping",
            "Collect Cheque"=>"Collect Cheque",
            "UNCollect Cheque"=>"UNCollect Cheque",
            "Refund Cheque"=>"Refund Cheque",
        ]; $refe_no = []; $ref_no  = [];
        foreach( $total_entries as $te ){ $refe_no[$te->refe_no_e]  = $te->refe_no_e; $ref_no[$te->ref_no_e]  = $te->ref_no_e; }
        $info[] = $state; $info[] = $refe_no; $info[] = $ref_no; 
        return $info;
 
    }
    public function purchase()
    {
      return $this->belongsTo("\App\Transaction","account_transaction");
    }
    
    public function payment()
    {
      return $this->belongsTo("\App\TransactionPayment","payment_id");
    }
    
    public static function total($business_id)
    {
        $total_entries = Entry::where("business_id",$business_id)->select([DB::raw("COUNT('id') as total")])->first();
        if(!empty($total_entries)){$total = $total_entries->total;}else{$total = 0;}
        return $total;
    }
    
    public  function additional_shippings()
    {
       return $this->hasMany("\App\Models\AdditionalShippingItem","id");
    }
    
    public  function additional_shipping()
    {
       return $this->belongsTo("\App\Models\AdditionalShipping","shipping_id");
    }
    
    public static function create_entries($data=NULL,$type=NULL,$debit=null,$credit=null,$idEx=null,$pattern=null,$return_id=null,$business_id=null)
    {
        
        if($type == "Purchase"){
            ($debit  == null )? $debit  = 0:"";
            ($credit == null )? $credit = 0:"";
            $ref_count  = ProductUtil::setAndGetReferenceCount("entries",$data->business_id);
            //Generate reference number
            DB::beginTransaction();
            $refence_no = ProductUtil::generateReferenceNumber("entries" , $ref_count,$data->business_id);
            $entries                         = new Entry;
            $entries->business_id            = $data->business_id;
            $entries->account_transaction    = $data->id;
            $entries->refe_no_e              = 'EN_'.$refence_no;
            $entries->ref_no_e               = $data->ref_no;
            $entries->state                  = 'Purchase';
            $entries->debit                  = $debit;
            $entries->credit                 = $credit;
            
            $entries->save();
            DB::commit();
        }
        if($type == "Sale"){
            ($debit  == null )? $debit  = 0:"";
            ($credit == null )? $credit = 0:"";
            if($pattern != null){
                $ref_count  = ProductUtil::setAndGetReferenceCount("entries",$data->business_id,$pattern);
            }else{
                $ref_count  = ProductUtil::setAndGetReferenceCount("entries" ,$data->business_id);
            }
            //Generate reference number
            DB::beginTransaction();
            $refence_no = ProductUtil::generateReferenceNumber("entries" , $ref_count,$data->business_id);
            $entries                         = new Entry;
            $entries->business_id            = $data->business_id;
            $entries->account_transaction    = $data->id;
            $entries->refe_no_e              = 'EN_'.$refence_no;
            $entries->ref_no_e               = $data->invoice_no;
            $entries->state                  = 'Sale';
            $entries->debit                  = $credit;
            $entries->credit                 = $debit;
            $entries->save();
            DB::commit();
        }
        if($type == "Production"){
            ($debit  == null )? $debit  = 0:"";
            ($credit == null )? $credit = 0:"";
            $ref_count  = ProductUtil::setAndGetReferenceCount("entries",$data->business_id);
            //Generate reference number
            DB::beginTransaction();
            $refence_no = ProductUtil::generateReferenceNumber("entries" , $ref_count,$data->business_id);
            $entries                         = new Entry;
            $entries->business_id            = $data->business_id;
            $entries->account_transaction    = $data->id;
            $entries->refe_no_e              = 'EN_'.$refence_no;
            $entries->ref_no_e               = $data->ref_no;
            $entries->state                  = 'Production';
            $entries->debit                  = $data->final_total;
            $entries->credit                 = $data->final_total;
            $entries->save();
            $dat = \App\AccountTransaction::where("transaction_id",$data->id)->update(["entry_id"=>$entries->id]);
            DB::commit();
        }
        // if($transaction != NULL){
        //     foreach($transaction as $id){
        //         \App\TransactionPayment::find()
        //     }
      
        // }
 
        if($type == "PReturn"){
            $o = \App\Models\Entry::where("account_transaction",$data->id)->where("return_id",$return_id)->first();
            if(empty($o)){
                $ref_count  = ProductUtil::setAndGetReferenceCount("entries" ,$data->business_id);
                //Generate reference number
                DB::beginTransaction();
                $refence_no = ProductUtil::generateReferenceNumber("entries" , $ref_count ,$data->business_id);
                
                $entries                         = new Entry;
                $entries->business_id            = $data->business_id;
                $entries->refe_no_e              = 'EN_'.$refence_no;
                $entries->ref_no_e               = $data->ref_no; 
                $entries->state                  = 'Return Purchase';
                $entries->account_transaction    = $data->id;
                $entries->return_id              = $return_id;
                $entries->save();
                
                $dat = \App\AccountTransaction::where('return_transaction_id',$data->id)->update(["entry_id"=>$entries->id]);
                DB::commit();
                
            }else{
                $dat = \App\AccountTransaction::where('return_transaction_id',$data->id)->update(["entry_id"=>$o->id]);
            }
        }
        if($type == "SReturn"){
            $o = \App\Models\Entry::where("account_transaction",$data->id)->where("return_id",$return_id)->first();
            if(empty($o)){
                $ref_count  = ProductUtil::setAndGetReferenceCount("entries",$data->business_id);
                //Generate reference number
                DB::beginTransaction();
                $refence_no = ProductUtil::generateReferenceNumber("entries" , $ref_count, $data->business_id);
                $entries                         = new Entry;
                $entries->business_id            = $data->business_id;
                $entries->refe_no_e              = 'EN_'.$refence_no;
                $entries->ref_no_e               = $data->invoice_no; 
                $entries->state                  = 'Return Sale';
                $entries->account_transaction    = $data->id;
                $entries->return_id              = $return_id;
                $entries->save();
                $dat = \App\AccountTransaction::where("return_transaction_id",$data->id)->update(["entry_id"=>$entries->id]);
                DB::commit();
            }else {
                $dat = \App\AccountTransaction::where('return_transaction_id',$data->id)->update(["entry_id"=>$o->id]);
            }
        }
        if($type == "Payment"){
            $ref_count  = ProductUtil::setAndGetReferenceCount("entries",$data->business_id);
            //Generate reference number
            DB::beginTransaction();
            $refence_no = ProductUtil::generateReferenceNumber("entries" , $ref_count , $data->business_id);
            $entries                         = new Entry;
            $entries->business_id            = $data->business_id;
            $entries->refe_no_e              = 'EN_'.$refence_no;
            $entries->ref_no_e               = $data->transaction->ref_no;
            $entries->debit                  = $data->amount;
            $entries->credit                 = $data->amount;
            $entries->state                  = 'Payment';
            $entries->payment_id             = $data->id;
            $entries->save();
            $dat = \App\AccountTransaction::where("transaction_payment_id",$data->id)->update(["entry_id"=>$entries->id]);
            DB::commit();
        }
        if($type == "PCheck"){
            $ref_count  = ProductUtil::setAndGetReferenceCount("entries",$data->business_id);
            //Generate reference number
            DB::beginTransaction();
            $refence_no = ProductUtil::generateReferenceNumber("entries" , $ref_count, $data->business_id);
            $entries                         = new Entry;
            $entries->business_id            = $data->business_id;
            $entries->refe_no_e              = 'EN_'.$refence_no;
            $entries->ref_no_e               = $data->ref_no;
            $entries->debit                  = $data->amount;
            $entries->credit                 = $data->amount;
            $entries->state                  = 'Cheque';
            $entries->check_id               = $data->id;
            $entries->payment_id             = $data->transaction_payment_id;
            $entries->save();
            $dat = \App\AccountTransaction::where("check_id",$data->id)->update(["entry_id"=>$entries->id]);
           
            DB::commit();
        }
        if($type == "check"){
            $ref_count  = ProductUtil::setAndGetReferenceCount("entries",$data->business_id);
            //Generate reference number
            DB::beginTransaction();
            $refence_no = ProductUtil::generateReferenceNumber("entries" , $ref_count ,$data->business_id);
            $entries                         = new Entry;
            $entries->business_id            = $data->business_id;
            $entries->refe_no_e              = 'EN_'.$refence_no;
            $entries->ref_no_e               = $data->ref_no;
            $entries->debit                  = $data->amount;
            $entries->credit                 = $data->amount;
            $entries->state                  = 'Cheque';
            $entries->check_id               = $data->id;
            $entries->save();
            $dat = \App\AccountTransaction::where("check_id",$data->id)->update(["entry_id"=>$entries->id]);
            DB::commit();
        }
        if($type == "Collect Cheque"){
            $ref_count  = ProductUtil::setAndGetReferenceCount("entries",$data->business_id);
            //Generate reference number
            DB::beginTransaction();
            $refence_no = ProductUtil::generateReferenceNumber("entries" , $ref_count,$data->business_id);
            $entries                         = new Entry;
            $entries->business_id            = $data->business_id;
            $entries->refe_no_e              = 'EN_'.$refence_no;
            $entries->ref_no_e               = $data->ref_no;
            $entries->debit                  = $data->amount;
            $entries->credit                 = $data->amount;
            $entries->state                  = 'Collect Cheque';
            $entries->check_id               = $data->id;
            $entries->save();
            $dat = \App\AccountTransaction::where("check_id",$data->id)->whereNull("entry_id")->get();
            foreach($dat as $it){
                if($it->note == "collecting cheque"){
                    $it->entry_id = $entries->id ;
                    $it->update();
                 }
            }
            DB::commit();
        }
        if($type == "UNCollect Cheque"){
            $ref_count  = ProductUtil::setAndGetReferenceCount("entries",$data->business_id);
            //Generate reference number
            DB::beginTransaction();
            $refence_no = ProductUtil::generateReferenceNumber("entries" , $ref_count,$data->business_id);
            $entries                         = new Entry;
            $entries->business_id            = $data->business_id;
            $entries->refe_no_e              = 'EN_'.$refence_no;
            $entries->ref_no_e               = $data->ref_no;
            $entries->debit                  = $data->amount;
            $entries->credit                 = $data->amount;
            $entries->state                  = 'Un Collect Cheque';
            $entries->check_id               = $data->id;
            $entries->save();
            $dat = \App\AccountTransaction::where("check_id",$data->id)->whereNull("entry_id")->get();
            foreach($dat as $it){
                if($it->note == "un collecting cheque"){
                    $it->entry_id = $entries->id ;
                    $it->update();
                 }
            }
            DB::commit();
        }
        if($type == "Return Voucher"){
            $ref_count  = ProductUtil::setAndGetReferenceCount("entries",$data->business_id);
            //Generate reference number
            DB::beginTransaction();
            $refence_no = ProductUtil::generateReferenceNumber("entries" , $ref_count,$data->business_id);
            $entries                         = new Entry;
            $entries->business_id            = $data->business_id;
            $entries->refe_no_e              = 'EN_'.$refence_no;
            $entries->ref_no_e               = $data->ref_no;
            $entries->debit                  = $data->amount;
            $entries->credit                 = $data->amount;
            $entries->state                  = 'Return Voucher';           
            $entries->voucher_id             = $data->id;
            $entries->save();
            DB::commit();
        }
        if($type == "Refund Cheque"){
            $ref_count  = ProductUtil::setAndGetReferenceCount("entries",$data->business_id);
            //Generate reference number
            DB::beginTransaction();
            $refence_no = ProductUtil::generateReferenceNumber("entries" , $ref_count,$data->business_id);
            $entries                         = new Entry;
            $entries->business_id            = $data->business_id;
            $entries->refe_no_e              = 'EN_'.$refence_no;
            $entries->ref_no_e               = $data->ref_no;
            $entries->debit                  = $data->amount;
            $entries->credit                 = $data->amount;
            $entries->state                  = 'refund Collect';
            $entries->check_id               = $data->id;
            $entries->save();
            $dat = \App\AccountTransaction::where("check_id",$data->id)->whereNull("entry_id")->get();
            foreach($dat as $it){
                if($it->note == "refund Collect"){
                    $it->entry_id = $entries->id ;
                    $it->update();
                 }
            }
            DB::commit();
        }
        if($type == "voucher"){
            $vouchers = \App\Models\PaymentVoucher::find($data->id);
            $ref_count  = ProductUtil::setAndGetReferenceCount("entries",$vouchers->business_id);
            //Generate reference number
            DB::beginTransaction();
            $refence_no = ProductUtil::generateReferenceNumber("entries" , $ref_count,$vouchers->business_id);
            $entries                         = new Entry;
            $entries->business_id            = $data->business_id;
            $entries->refe_no_e              = 'EN_'.$refence_no;
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
            DB::commit();
        }
        if($type == "journalV"){
            $ref_count  = ProductUtil::setAndGetReferenceCount("entries",$data->business_id);
            //Generate reference number
            DB::beginTransaction();

            $refence_no = ProductUtil::generateReferenceNumber("entries" , $ref_count,$data->business_id);
            $entries                         = new Entry;
            $entries->business_id            = $data->business_id;
            $entries->refe_no_e              = 'EN_'.$refence_no;
            $entries->ref_no_e               = $data->ref_no;
            $entries->state                  = 'Journal Voucher';
            $entries->debit                  = $data->amount;
            $entries->credit                 = $data->amount;
            $entries->created_at             = $data->date;
            $entries->updated_at             = $data->date;
            $entries->journal_voucher_id     = $data->id;
            $entries->save();
            $dat = \App\AccountTransaction::whereIn("daily_payment_item_id",$data->items->pluck("id"))->update(["entry_id"=>$entries->id]);
            DB::commit();
        }
        if($type == "journalEx"){
            $ref_count  = ProductUtil::setAndGetReferenceCount("entries",$data->gournal_voucher->business_id);
            //Generate reference number
            DB::beginTransaction();
             
            $refence_no = ProductUtil::generateReferenceNumber("entries" , $ref_count,$data->gournal_voucher->business_id);
            $entries                         = new Entry;
            $entries->business_id            = $data->gournal_voucher->business_id;
            $entries->refe_no_e              = 'EN_'.$refence_no;
            $entries->ref_no_e               = $data->gournal_voucher->ref_no;
            $entries->state                  = 'Expense Voucher';
            $entries->debit                  = $data->amount;
            $entries->credit                 = $data->amount;
            $entries->created_at             = $data->date;
            $entries->updated_at             = $data->date;
            $entries->expense_voucher_id     = $data->gournal_voucher->id;
            $entries->save();
            $dat = \App\AccountTransaction::whereHas("gournal_voucher_item",function($query) use($data){
                                                    $query->whereHas("gournal_voucher",function($query) use($data){
                                                        $query->where("id",$data->gournal_voucher->id);
                                                    }); 
                                            })->update(["entry_id"=>$entries->id]);

            DB::commit();
        }
        if($type == "Shipping"){ 
            $ref_count  = ProductUtil::setAndGetReferenceCount("entries",$data->additional_shipping->transaction->business_id);
            //Generate reference number
            DB::beginTransaction();
            $refence_no = ProductUtil::generateReferenceNumber("entries" , $ref_count,$data->additional_shipping->transaction->business_id);
            $entries                         = new Entry;
            $entries->business_id            = $data->additional_shipping->transaction->business_id;
            $entries->refe_no_e              = 'EN_'.$refence_no;
            if($data->additional_shipping->transaction->type == "purchase"){
                $entries->ref_no_e               = $data->additional_shipping->transaction->ref_no;
            }else{
                $entries->ref_no_e               = $data->additional_shipping->transaction->invoice_no;
            }
            $entries->state                  = 'Shipping';
            $entries->debit                  = $data->total;
            $entries->credit                 = $data->total;
            $entries->created_at             = $data->date;
            $entries->updated_at             = $data->date;
            $entries->shipping_id            = $data->additional_shipping->id;
            $entries->shipping_item_id       = $data->id;
            $entries->save();
            $dat = \App\AccountTransaction::where("additional_shipping_item_id",$data->id)->update(["entry_id"=>$entries->id]);

            DB::commit();
        }

        
    }
    public static function purchases($business_id)
    {
      $data  = Entry::where("business_id",$business_id)->get();
      $check = \App\Models\Check::where("business_id",$business_id)->get();
      $id_checks = [];
      foreach($data as $url_){ 
        foreach($check as $cek){
            if($url_->ref_no_e == $cek->ref_no){
                $id_checks[$url_->id] = $cek->id ;
            }
        }
      }
      return $id_checks;
    }
    public static function delete_entries($transaction_id)
    {
        $data  =  Entry::where("account_transaction",$transaction_id)->whereNull("voucher_id")->whereNull("check_id");
        $data->delete();
    }
    public static function sum_debit($transaction=null,$check=null)
    {
        $debit = null;
        if($transaction != null){
          $debit =  \App\AccountTransaction::where("transaction_id",$transaction)
                                                ->whereHas('account',function($query){
                                                    $query->where('cost_center',0);
                                                })->where("type","debit")
                                                ->where("transaction_payment_id",null)
                                                ->where("id_delete",null)
                                                ->sum("amount");
          $debit +=  \App\AccountTransaction::where("transaction_id",$transaction)->where("type","debit")->where("sub_type","!=",null)->where("transaction_payment_id","!=",null)->where("id_delete",null)->sum("amount");
       
        }
        if($check != null){
          $debit  =  \App\AccountTransaction::where("check_id",$check)->where("type","debit")->where("for_repeat",1)->sum("amount");
        }
        return $debit;
    }
    public static function sum_credit($transaction=null,$check=null)
    {
        $credit = null;
        if($transaction != null){
            $credit =  \App\AccountTransaction::where("transaction_id",$transaction)->whereHas('account',function($query){
                $query->where('cost_center',0);
            })->where("type","credit")->sum("amount");
                      }
          if($check != null){
            $credit =  \App\AccountTransaction::where("check_id",$check)->where("type","credit")->where("for_repeat",1)->sum("amount");
          }
        return $credit;
    }
    public function transaction()
    {
        return $this->belongsTo("\App\Transaction","account_transaction");
    }

}
