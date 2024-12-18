<?php
namespace App\Services\Purchase;
use App\Models\TransactionDelivery;
use App\Utils\productUtil;
use App\Product;
use App\Utils\Util;
class Recieve  extends Util
{
   
   public static function add_payment($transaction,$pay)
   { 
            foreach ($pay as $item) {
                $prefix_type = 'purchase_payment';
                $utl =  new Util;
                $ref_count = $utl->setAndGetReferenceCount($prefix_type, $transaction->business_id);
                $payment_ref_no = $utl->generateReferenceNumber($prefix_type, $ref_count, $transaction->business_id);
                $item['transaction_id'] =  $transaction->id;
                $item['store_id']       =  $transaction->store;
                $item['business_id']    = $transaction->business_id;
                $item['payment_ref_no'] = $payment_ref_no; 
                $it  =     \App\TransactionPayment::create($item);
                //action
                \App\AccountTransaction::add_suplier($transaction,$item);  
                $credit_data = [
                    'amount' => $item['amount'],
                    'account_id' =>$item['account_id'],
                    'type' => 'credit',
                    'sub_type' => 'deposit',
                    'operation_date' => date('Y-m-d'),
                    'created_by' => session()->get('user.id'),
                    'note' => 'payment ',
                    'transaction_id'=>$transaction->id,
                    'transaction_payment_id'=>$it->id
                ];
                \App\AccountTransaction::createAccountTransaction($credit_data);
                
            }
   }
}
