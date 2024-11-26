<?php 
namespace App\Services\PaymentVoucher;
use App\Models\Check;
use App\Utils\TransactionUtil;
use App\Unit;
use DB;
use App\Models\PaymentVoucher;
class Bill
{
    public static function pay_transaction($id,$inputs,$created_by=null,$type=null)
    {
        $payment_voucher =  PaymentVoucher::find($id);
        if ($inputs['bill_id']) {
            foreach ($inputs['bill_id'] as  $key=>$bill_id) {
                $transaction     =  \App\Transaction::find($bill_id);
                $prefix_type     = 'sell_payment';
                if ($payment_voucher->type == 0) {
                    $prefix_type = 'purchase_payment';
                }
                $business_id     = $payment_voucher->business_id;
                $ref_count       = \App\Utils\Util::setAndGetReferenceCount($prefix_type, $transaction->business_id);
                $payment_ref_no  = \App\Utils\Util::generateReferenceNumber($prefix_type, $ref_count, $transaction->business_id);
                $payment_amount  = $inputs['bill_amount'][$key];
                
                $payment_data = [
                    'store_id'                => $transaction->warehouse->id,
                    'transaction_id'          => $transaction->id,
                    'amount'                  => $payment_amount,
                    'method'                  => ($type != null)? 'card':'payment voucher',
                    'business_id'             => $transaction->business_id,
                    'is_return'               => 0,
                    'card_transaction_number' => null,
                    'card_number'             => null,
                    'card_type'               => ($type != null)? 'card':null,
                    'card_holder_name'        => null,
                    'card_month'              => null,
                    'card_security'           => null,
                    'cheque_number'           => null,
                    'bank_account_number'     => null,
                    'note'                    => null,
                    'paid_on'                 => date('Y-m-d h:i:s',strtotime($payment_voucher->date)),
                    'created_by'              => ($created_by)?$created_by:auth()->user()->id ,
                    'payment_for'             => $transaction->contact_id,
                    'payment_ref_no'          => $payment_ref_no,
                    'account_id'              => null,
                    'payment_voucher_id'      => $payment_voucher->id,
                    'source'                  => $payment_amount
                ];
                //......... create payment
                $tp     = \App\TransactionPayment::create($payment_data);
                $parent = \App\Models\ParentArchive::save_payment_parent($tp->id,"Add");
        
                //......... update_status
                $total_paid = \App\TransactionPayment::where('transaction_id', $transaction->id)
                                ->select(DB::raw('SUM(IF( is_return = 0, amount, amount*-1))as total_paid'))
                                ->first()
                                ->total_paid;

                $final_amount =  $transaction->final_total;
                if (is_null($final_amount)) {
                    $final_amount = \App\Transaction::find($transaction->id)->final_total;
                }
                $status = 'due';
                if ($final_amount <= $total_paid) {
                    $status = 'paid';
                } elseif ($total_paid > 0 && $final_amount > $total_paid) {
                    $status = 'partial';
                }
                \App\Transaction::where('id',$transaction->id)->update([
                        'payment_status'=>$status
                ]);

              
            }
            $allData =  \App\TransactionPayment::where('payment_voucher_id',$payment_voucher->id)->get();
            $voucher =  \App\TransactionPayment::where('payment_voucher_id',$payment_voucher->id)->first();
            
            foreach ($allData as $data) {
                Bill::update_status($data->transaction);
            }
            (!empty($voucher))?$id_tr=$voucher->transaction_id:$id_tr=null;
            (!empty($voucher))?$id_v=$voucher->id:$id_v = null;
            if($type!=null){
                \App\AccountTransaction::where("payment_voucher_id",$payment_voucher->id)->update([
                    "transaction_id"=>$id_tr,
                    "transaction_payment_id"=>$id_v,
                    "account_id"=>$created_by->user_visa_account_id,
                    
                ]);
            }else{
            
                \App\AccountTransaction::where("payment_voucher_id",$payment_voucher->id)->update([
                    "transaction_id"=>$id_tr,
                    "transaction_payment_id"=>$id_v,
                    
                ]);
            }
        }
        
    }
    public static function update_pay_transaction($id,$inputs,$user=null)
    {
        
        $payment_voucher =  PaymentVoucher::find($id);
        $ids             =  isset($inputs['payment_id'])?$inputs['payment_id']:[] ;
        $deletes         =  \App\TransactionPayment::where('payment_voucher_id',$id)->whereNotIn('id',$ids)->get();
        foreach ($deletes as $del) {
            $tr =  $del->transaction;
            $old_payment = $del->replicate();
            $del->delete();
            Bill::update_status($tr);
        }
        #........................................................................
        if (isset($inputs['bill_id'])) {
            foreach ($inputs['bill_id'] as  $key=>$bill_id) {
                $transaction   =  \App\Transaction::find($bill_id);
                $prefix_type   =  'sell_payment';
                if ($payment_voucher->type == 0) {
                    $prefix_type = 'purchase_payment';
                }
                $old_bill_at_the_Same_voucher_id = \App\TransactionPayment::where('payment_voucher_id',$id)->where('transaction_id',$transaction->id)->first();
                $payment_amount                  =  $inputs['bill_amount'][$key];
                if(empty($old_bill_at_the_Same_voucher_id)){
                    $business_id    =  request()->session()->get('user.business_id');
                    $ref_count      =  \App\Utils\Util::setAndGetReferenceCount($prefix_type, $business_id);
                    $payment_ref_no =  \App\Utils\Util::generateReferenceNumber($prefix_type, $ref_count, $business_id);
                    $payment_data   =  [
                        'store_id'                => $transaction->store,
                        'transaction_id'          => $transaction->id,
                        'amount'                  => $payment_amount,
                        'method'                  => 'payment voucher',
                        'business_id'             => $transaction->business_id,
                        'is_return'               => 0,
                        'card_transaction_number' => null,
                        'card_number'             => null,
                        'card_type'               => null,
                        'card_holder_name'        => null,
                        'card_month'              => null,
                        'card_security'           => null,
                        'cheque_number'           => null,
                        'bank_account_number'     => null,
                        'note'                    => null,
                        'paid_on'                 => date('Y-m-d',strtotime($payment_voucher->date)),
                        'created_by'              => auth()->user()->id ,
                        'payment_for'             => $transaction->contact_id,
                        'payment_ref_no'          => $payment_ref_no,
                        'account_id'              => null,
                        'payment_voucher_id'      => $payment_voucher->id,
                        'source'                  => $payment_amount
                    ];
                    $tp     = \App\TransactionPayment::create($payment_data);
                    $parent = \App\Models\ParentArchive::save_payment_parent($tp->id,"Add");
                }else{
                    $old_bill_at_the_Same_voucher_id->amount = FloatVal($old_bill_at_the_Same_voucher_id->amount) + FloatVal($payment_amount);
                    $old_bill_at_the_Same_voucher_id->source = FloatVal($old_bill_at_the_Same_voucher_id->amount) + FloatVal($payment_amount);
                    $old_bill_at_the_Same_voucher_id->update();
                }
            }
        }
        #........................................................................
        $allData =  \App\TransactionPayment::where('payment_voucher_id',$id)->get();
        $voucher =  \App\TransactionPayment::where('payment_voucher_id',$id)->first();
        foreach ($allData as $data) {
            Bill::update_status($data->transaction);
        }
        (!empty($voucher))?$id_tr=$voucher->transaction_id:$id_tr=null;
        (!empty($voucher))?$id_v =$voucher->id:$id_v = null;
        \App\AccountTransaction::where("payment_voucher_id",$id)->update([
             "transaction_id"         => $id_tr,
             "transaction_payment_id" => $id_v,

        ]);
    }
    public static function update_status($transaction)
    {
        
        $total_paid   = \App\TransactionPayment::where('transaction_id', $transaction->id)->whereNull('deleted_at')->sum('amount'); 
        $final_amount =  $transaction->final_total;
        if (is_null($final_amount)) {
            $final_amount = \App\Transaction::find($transaction->id)->final_total;
        } 
        $status = 'due';
        if ($final_amount <= $total_paid) {
            $status = 'paid';
        } elseif ($total_paid > 0 && $final_amount > $total_paid) {
            $status = 'partial';
        }
        \App\Transaction::where('id',$transaction->id)->update([
                'payment_status'=>$status
        ]);
    }
}
