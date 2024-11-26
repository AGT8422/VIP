<?php 
namespace App\Services\Cheque;
use App\Models\Check;
use App\Utils\TransactionUtil;
use App\Unit;
use DB;
class Bill
{
    
    public static function supplier()
    {
        $account_id =  app('request')->input('account_id');
        if(isset($account_id)){
            $account = \App\Account::where("id",$account_id)->first();
            if($account){
             if($account->contact_id != null){
                $contact_id = $account->contact_id ;
                }else{
                $contact_id = null ;
            
            }}
            else{
                $contact_id = null ;
            }
                
            
        } else{
            $contact_id =  app('request')->input('contact_id');
        }
        $type       =  (app('request')->input('type') == 'sale')?'sale':'purchase';
        $allData    =  \App\Transaction::where('type',$type)->where('contact_id',$contact_id)
                                ->where('payment_status','!=','paid')
                                ->get()->map(function($row){
                                    $due = $row->final_total  -   $row->payment_lines->sum('amount'); 
                                   return  [
                                            'check'=>'<input type="checkbox" class="add_item" onClick="add_item('.$row->id.','.round($due,2).')" data-id="'.$row->id.'"
                                                            value="'.$row->id.'" data-due="'.round($due,2).'">',
                                            'date'=>date('Y-m-d',strtotime($row->transaction_date)),
                                            'ref_no'=>$row->ref_no,
                                            'name'=>$row->contact->first_name,
                                            'status'=>$row->status,
                                            'payment_status'=>$row->payment_status,
                                            'grand_total'=>round($row->final_total,2)??0,
                                            'due'=>round($due,2),
                                            'store_name'=>($row->warehouse)?$row->warehouse->name:'---',
                                            'view'=>'<a href="#" data-href="/purchases/'.$row->id.'" class="btn-modal" data-container=".view_modal"><i class="fas fa-eye" aria-hidden="true"></i>View</a>'
                                        ];
                                });
        
       return [
        'data'=>$allData
       ] ;
    }
    public static function customer()
    {
        
        $account_id =  app('request')->input('account_id');
        if(isset($account_id)){
            $account = \App\Account::where("id",$account_id)->first();
            if($account){
             if($account->contact_id != null){
                $contact_id = $account->contact_id ;
                }else{
                $contact_id = null ;
            
            }}
            else{
                $contact_id = null ;
            }
                
            
        } else{
            $contact_id =  app('request')->input('contact_id');
        }
        $allData    =  \App\Transaction::where('type','sale')->where('contact_id',$contact_id)
                                ->where('payment_status','!=','paid')
                                ->get()->map(function($row){
                                    $due = $row->final_total  -   $row->payment_lines->sum('amount'); 
                                   return  [
                                            'check'=>'<input type="checkbox" class="add_item" onClick="add_item('.$row->id.','.round($due,2).')" data-id="'.$row->id.'"
                                                            value="'.$row->id.'" data-due="'.round($due,2).'">',
                                            'date'=>date('Y-m-d',strtotime($row->transaction_date)),
                                            'ref_no'=>$row->invoice_no,
                                            'name'=>$row->contact->first_name,
                                            'status'=>$row->status,
                                            'payment_status'=>$row->payment_status,
                                            'grand_total'=>round($row->final_total,2)??0,
                                            'due'=>round($due,2),
                                            'store_name'=>($row->warehouse)?$row->warehouse->name:'---',
                                            'view'=>'<a href="#" data-href="/sells/'.$row->id.'" class="btn-modal" data-container=".view_modal"><i class="fas fa-eye" aria-hidden="true"></i>View</a>'
                                        ];
                                });
         return [
        'data'=>$allData
       ] ;
    }
    public static function pay_transaction($id,$inputs,$user=null)
    {
        $cheque =  Check::find($id);
        if ($inputs['bill_id']) {
            foreach ($inputs['bill_id'] as  $key => $bill_id) {
                $transaction   = \App\Transaction::find($bill_id);
                $prefix_type   = 'sell_payment';
                if ($cheque->type == 1) {
                    $prefix_type = 'purchase_payment';
                }
                $business_id    = $cheque->business_id;
                \App\Models\StatusLive::insert_data_c($business_id,$transaction,$cheque,"Add Cheque");
              
                $ref_count      = \App\Utils\Util::setAndGetReferenceCount($prefix_type, $cheque->business_id);
                $payment_ref_no = \App\Utils\Util::generateReferenceNumber($prefix_type, $ref_count, $cheque->business_id);
             
                $payment_amount =  $inputs['bill_amount'][$key];
                $payment_data   = [
                    'store_id'                => $transaction->store,
                    'transaction_id'          => $transaction->id,
                    'amount'                  => $payment_amount,
                    'method'                  => 'cheque',
                    'business_id'             => $transaction->business_id,
                    'is_return'               => 0,
                    'card_transaction_number' => null,
                    'card_number'             => null,
                    'card_type'               => null,
                    'card_holder_name'        => null,
                    'card_month'              => null,
                    'card_security'           => null,
                    'cheque_number'           => $cheque->cheque_no,
                    'bank_account_number'     => null,
                    'note'                    => null,
                    'paid_on'                 => date('Y-m-d h:i:s',strtotime($cheque->write_date)),
                    'created_by'              => ($user != null)?$user->id:auth()->user()->id ,
                    'payment_for'             => $transaction->contact_id,
                    'payment_ref_no'          => $payment_ref_no,
                    'account_id'              => null,
                    'check_id'                => $cheque->id,
                    'source'                  => $payment_amount
                ];
                \App\TransactionPayment::create($payment_data);
                $total_paid = \App\TransactionPayment::where('transaction_id', $transaction->id)
                                                            ->select(DB::raw('SUM(IF( is_return = 0, amount, amount*-1))as total_paid'))
                                                            ->first()
                                                            ->total_paid;
                $cheque->transaction_id = $transaction->id;
                $cheque->update();
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
        
    }
    public static function update_pay_transaction($id,$inputs,$user=null)
    {
        $cheque =  Check::find($id);
        $ids    = isset($inputs['payment_id'])?$inputs['payment_id']:[] ;
        $dels =  \App\TransactionPayment::where('check_id',$id)->whereNotIn('id',$ids)->get();
        foreach ($dels as $del) {
            $tr =  $del->transaction;
            $del->delete();
            Bill::update_status($tr);
        }
        if (isset($inputs['bill_id'])) {
            foreach ($inputs['bill_id'] as  $key => $bill_id) {
                $transaction   =  \App\Transaction::find($bill_id);

                $prefix_type = 'sell_payment';
                if ($cheque->type == 1) {
                    $prefix_type = 'purchase_payment';
                }
                $business_id    = ($user != null)?$user->business_id:request()->session()->get('user.business_id');
                $ref_count      = \App\Utils\Util::setAndGetReferenceCount($prefix_type, $business_id);
                $payment_ref_no = \App\Utils\Util::generateReferenceNumber($prefix_type, $ref_count, $business_id);
                $payment_amount =  $inputs['bill_amount'][$key];
                $payment_data = [
                    'store_id'                => $transaction->store,
                    'transaction_id'          => $transaction->id,
                    'amount'                  => $payment_amount,
                    'method'                  => 'cheque',
                    'business_id'             => $transaction->business_id,
                    'is_return'               =>  0,
                    'card_transaction_number' => null,
                    'card_number'             => null,
                    'card_type'               => null,
                    'card_holder_name'        => null,
                    'card_month'              => null,
                    'card_security'           => null,
                    'cheque_number'           => $cheque->cheque_no,
                    'bank_account_number'     => null,
                    'note'                    =>  null,
                    'paid_on'                 => date('Y-m-d',strtotime($cheque->write_date)),
                    'created_by'              => ($user != null)?$user->id:auth()->user()->id ,
                    'payment_for'             => $transaction->contact_id,
                    'payment_ref_no'          => $payment_ref_no,
                    'account_id'              => null,
                    'check_id'                => $cheque->id,
                    'source'                  => $payment_amount
                ];
                \App\TransactionPayment::create($payment_data);
            }
        }
        
        $allData    =  \App\TransactionPayment::where('check_id',$id)->get();
        $payment_id =  \App\TransactionPayment::where('check_id',$id)->first();
        if(!empty($payment_id) ){
            $cheque->transaction_id = $payment_id->transaction_id;
            $cheque->update();
        }else{
            $cheque->transaction_id = null;
            $cheque->update();
        }
        foreach ($allData as $data) {
            Bill::update_status($data->transaction);
        }
        (!empty($payment_id))?$id_tr = $payment_id->transaction_id:$id_tr=null;
        (!empty($payment_id))?$id_v  = $payment_id->id:$id_v = null;
        \App\AccountTransaction::where("check_id",$id)->update([
            "transaction_id"         => $id_tr,
            "transaction_payment_id" => $id_v
        ]);

    }
    public static function update_status($transaction)
    {
        
        $total_paid = \App\TransactionPayment::where('transaction_id', $transaction->id)->sum('amount');
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
