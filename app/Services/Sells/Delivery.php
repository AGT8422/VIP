<?php
namespace App\Services\Sells;
use App\Models\TransactionDelivery;
use App\Utils\productUtil;
use App\Product;
use App\Utils\Util;
class Delivery  extends Util
{
   public static function index($transaction,$arr)
   {
            $type                         = 'trans_delivery';
            $pr_util                      =  new productUtil;
            $ref_count                    =  $pr_util->setAndGetReferenceCount($type);
            $receipt_no                   =  $pr_util->generateReferenceNumber($type, $ref_count);
            $tr_received                  =  new TransactionDelivery;
            $tr_received->store_id        =  $transaction->store;
            $tr_received->transaction_id  =  $transaction->id;
            $tr_received->business_id     =  $transaction->business_id ;
            $tr_received->reciept_no      =  $receipt_no ;
            $tr_received->invoice_no      =  $transaction->invoice_no;
            $tr_received->status          = 'sales';
            $tr_received->save(); 
            foreach ($arr as $it) {
                $product  =  Product::find($it['product_id']);
                $line     =  \App\TransactionSellLine::create([
                                'transaction_id'              => $transaction->id,
                                'store_id'                    => $transaction->store,
                                'product_id'                  => $product->id,
                                'variation_id'                => $it['variation_id'],   
                                'quantity'                    => $it['quantity'],
                                'unit_price_before_disco unt' => $it['unit_price'],
                                'unit_price'                  => $it['unit_price'],
                                'unit_price_inc_tax'          => $it['unit_price_inc_tax'],
                                ]);
                \App\Models\DeliveredPrevious::recieve($transaction,$product,$it['quantity'],$transaction->store,$tr_received,$line);  
            }
   }
   public static function add_payment($transaction,$pay)
   { 
            // foreach ($pay as $item) {
            //     $prefix_type = 'sell_payment';
            //     $utl =  new Util;
            //     $ref_count = $utl->setAndGetReferenceCount($prefix_type, $transaction->business_id);
            //     $payment_ref_no = $utl->generateReferenceNumber($prefix_type, $ref_count, $transaction->business_id);
            //     $item['transaction_id'] =  $transaction->id;
            //     $item['store_id']       =  $transaction->store;
            //     $item['business_id']    = $transaction->business_id;
            //     $item['payment_ref_no'] = $payment_ref_no; 
            //     $it  =     \App\TransactionPayment::create($item);
            //     //action
            //     \App\AccountTransaction::add_customer($transaction,$item);  
            //     $credit_data = [
            //         'amount' => $item['amount'],
            //         'account_id' =>$item['account_id'],
            //         'type' => 'debit',
            //         'sub_type' => 'deposit',
            //         'operation_date' => date('Y-m-d'),
            //         'created_by' => session()->get('user.id'),
            //         'note' => 'payment ',
            //         'transaction_id'=>$transaction->id,
            //         'transaction_payment_id'=>$it->id
            //     ];
            //     \App\AccountTransaction::createAccountTransaction($credit_data);
                
            // }
   }
}
