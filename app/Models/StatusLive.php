<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatusLive extends Model
{
    use HasFactory;
    //......add purchase
    public static function insert_data_p($business_id,$transaction,$state,$receive=null)
    {
        $data   = StatusLive::where("business_id",$business_id)
                            ->where("transaction_id",$transaction->id)
                            ->whereNull("shipping_item_id")
                            ->orderBy("id","desc")
                            ->first();
        if(empty($data)){
            $item                      = new StatusLive();
            $item->business_id         = $business_id;
            $item->transaction_id      = $transaction->id;
            $item->reference_no        = $transaction->ref_no;
            $item->state               = "Purchase ".$state;
            $item->price               = $transaction->final_total;
            $item->t_received          = ($receve!= null)?$receve->id:null;
            $item->num_serial          = 1;
            $item->save();
        }else{
            
            if( $data->state == "Purchase ". $state){
                $data->state               =  "Purchase ". $state;
                $data->price               =  $transaction->final_total;
                $data->update();
            }else{
                $item                      = new StatusLive();
                $item->business_id         = $business_id;
                $item->transaction_id      = $transaction->id;
                $item->reference_no        = $transaction->ref_no;
                $item->state               = "Purchase ".$state;
                $item->price               = $transaction->final_total;
                $item->t_received          = ($receve!= null)?$receve->id:null;
                $item->num_serial          = $data->num_serial+1;
                $item->save();
            }
        } 
    }
    //......update purchase
    public static function update_data_p($business_id,$transaction,$state)
    {
        $data   = StatusLive::orderBy("id","desc")->where("transaction_id",$transaction->id)->whereNull("shipping_item_id")->where("business_id",$business_id)->first();
        if(empty($data)){
            $item                      =  new StatusLive();
            $item->business_id         = $business_id;
            $item->transaction_id      = $transaction->id;
            $item->reference_no        = $transaction->ref_no;
            $item->state               = "Purchase ".$state;
            $item->price               = $transaction->final_total;
            $item->num_serial          = 1;
            $item->save();
        }else{
            if( $data->state =="Purchase ". $state){
                $data->state               =  "Purchase ". $state;
                $data->price               =  $transaction->final_total;
                $data->update();
            }else{
                $item                      =  new StatusLive();
                $item->business_id         = $business_id;
                $item->transaction_id      = $transaction->id;
                $item->reference_no        = $transaction->ref_no;
                $item->state               = "Purchase ".$state;
                $item->price               = $transaction->final_total;
                $item->num_serial          = isset($data->num_serial)?$data->num_serial+1:1;
                $item->save();
            }
           
        }
    }
    // .. add sale
    public static function insert_data_s($business_id,$transaction,$state)
    {
        $data   = StatusLive::orderBy("id","desc")->where("transaction_id",$transaction->id)->where("business_id",$business_id)->first();
        if(empty($data)){
            $item                      =  new StatusLive();
            $item->business_id         = $business_id;
            $item->transaction_id      = $transaction->id;
            $item->reference_no        = $transaction->invoice_no;
            $item->state               = $state;
            $item->price               = $transaction->final_total;
            $item->num_serial          = 1;
            $item->save();
        } 
    }
    // .. add sale
    public static function update_data_s($business_id,$transaction,$state)
    {
        $data   = StatusLive::orderBy("id","desc")->where("transaction_id",$transaction->id)->where("business_id",$business_id)->first();
        if(empty($data)){
            $item                      =  new StatusLive();
            $item->business_id         = $business_id;
            $item->transaction_id      = $transaction->id;
            $item->reference_no        = $transaction->invoice_no;
            $item->state               = $state;
            $item->price               = $transaction->final_total;
            $item->num_serial          = 1;
            $item->save();
        } 
    }
    // .. add cheque
    public static function insert_data_c($business_id,$transaction,$check,$state)
    {
        $data   = StatusLive::orderBy("id","desc")->where("transaction_id",$transaction->id)->where("check_id",$check->id)->where("business_id",$business_id)->first();
        if(empty($data)){
            $item                      = new StatusLive();
            $item->business_id         = $business_id;
            $item->transaction_id      = $transaction->id;
            $item->reference_no        = $check->ref_no;
            $item->state               = $state;
            $item->price               = $check->amount;
            $item->check_id            = $check->id;
            $item->num_serial          = 1;

            $item->save();
        }else{
            $item                      = new StatusLive();
            $item->business_id         = $business_id;
            $item->transaction_id      = $transaction->id;
            $item->reference_no        = $check->ref_no;
            $item->state               = $state;
            $item->price               = $check->amount;
            $item->voucher_id          = $check->id;
            $item->num_serial          = isset($data->num_serial)?$data->num_serial+1:1;

            $item->save();
        }
    }
    // .. add voucher
    public static function insert_data_v($business_id,$transaction,$voucher,$state)
    {
        $data   = StatusLive::orderBy("id","desc")->where("transaction_id",$transaction->id)->where("voucher_id",$voucher->id)->where("business_id",$business_id)->first();
        if(empty($data)){
            $item                      = new StatusLive();
            $item->business_id         = $business_id;
            $item->transaction_id      = $transaction->id;
            $item->reference_no        = $voucher->ref_no;
            $item->state               = $state;
            $item->price               = $voucher->amount;
            $item->voucher_id          = $voucher->id;
            $item->num_serial          = 1;

            $item->save();
        }else{
            $item                      = new StatusLive();
            $item->business_id         = $business_id;
            $item->transaction_id      = $transaction->id;
            $item->reference_no        = $voucher->ref_no;
            $item->state               = $state;
            $item->price               = $voucher->amount;
            $item->voucher_id          = $voucher->id;
            $item->num_serial          = isset($data->num_serial)?$data->num_serial+1:1;

            $item->save();
        }
    }
    // .. return purchase
    public static function insert_data_rp($business_id,$transaction,$state,$total)
    {
        $data   = StatusLive::orderBy("id","desc")->where("transaction_id",$transaction->id)->where("business_id",$business_id)->first();
        if(empty($data)){
            $item                      = new StatusLive();
            $item->business_id         = $business_id;
            $item->transaction_id      = $transaction->id;
            $item->reference_no        = $transaction->ref_no;
            $item->state               = $state;
            $item->price               = $total;
            $item->return_id           = $transaction->id;
            $item->num_serial          = isset($data->num_serial)?$data->num_serial+1:1;
            $item->save();
        }
        
    }
    // .. return sale
    public static function insert_data_rs($business_id,$transaction,$state,$total)
    {
        $data   = StatusLive::orderBy("id","desc")->where("transaction_id",$transaction->id)->where("business_id",$business_id)->first();
         
        $item                      = new StatusLive();
        $item->business_id         = $business_id;
        $item->transaction_id      = $transaction->id;
        $item->reference_no        = $transaction->invoice_no;
        $item->state               = $state;
        $item->price               = $total;
        $item->return_id           = $transaction->id;
        $item->num_serial          = isset($data->num_serial)?$data->num_serial+1:1;

        $item->save();
        
    }
    // .. add expense
    public static function insert_data_sh($business_id,$transaction,$ship,$state,$receve=null)
    {
        $data    = StatusLive::orderBy("id","desc")->where("transaction_id",$transaction->id)->where("business_id",$business_id)->first();
        $datas   = StatusLive::orderBy("id","desc")->where("transaction_id",$transaction->id)->where("shipping_item_id",$ship->id)->where("business_id",$business_id)->first();
        if(empty($datas)){
            $item                      = new StatusLive();
            $item->business_id         = $business_id;
            $item->transaction_id      = $transaction->id;
            $item->reference_no        = $transaction->ref_no;
            $item->state               = $state;
            $item->price               = $ship->total;
            $item->shipping_id         = $ship->additional_shipping->id;
            $item->shipping_item_id    = $ship->id;
            $item->t_received          = ($receve!=null)?$receve->TrRecieved->id:null;
            $item->num_serial          = 1;
            $item->save();
        }else{
            $item                      = new StatusLive();
            $item->business_id         = $business_id;
            $item->transaction_id      = $transaction->id;
            $item->reference_no        = $transaction->ref_no;
            $item->state               = $state;
            $item->price               = $ship->total;
            $item->shipping_id         = $ship->additional_shipping->id;
            $item->shipping_item_id    = $ship->id;
            $item->t_received          = ($receve!=null)?$receve->TrRecieved->id:null;
            $item->num_serial          = isset($data->num_serial)?$data->num_serial+1:1;

            $item->save();
        } 
        
    }
    // .. add receive
    public static function insert_data_pr($business_id,$transaction,$receive,$state,$total)
    {
        $data   = StatusLive::orderBy("id","desc")->where("transaction_id",$transaction->id)->where("business_id",$business_id)->first();
        if(empty($data)){
            $sum_qty                   = \App\Models\RecievedPrevious::qty($transaction->id);
            $item                      = new StatusLive();
            $item->business_id         = $business_id;
            $item->transaction_id      = $transaction->id;
            $item->reference_no        = $receive->TrRecieved->reciept_no;
            $item->state               = $state;
            $item->price               = $sum_qty;
            $item->t_received          = $receive->TrRecieved->id;
            $item->num_serial          = isset($data->num_serial)?$data->num_serial+1:1;

            $item->save();
        } 
        
    } 
    // .. update receive
    public static function update_data_pr($business_id,$transaction,$receive,$state)
    {
        $data   = StatusLive::orderBy("id","desc")->where("transaction_id",$transaction->id)->where("business_id",$business_id)->first();
        if(empty($data)){  
            $item                      = new StatusLive();
            $item->business_id         = $business_id;
            $item->transaction_id      = $transaction->id;
            $item->reference_no        = $receive->TrRecieved->reciept_no;
            $item->state               = $state;
            $item->price               = 0;
            $item->t_received          = $receive->TrRecieved->id;
            $item->num_serial          = 1;

            $item->save();
        }else{
            $item                      = StatusLive::find($data->id);
            $sum_qty                   = \App\Models\RecievedPrevious::qty($item->transaction_id);
            $item->price               = $sum_qty;
            $item->update();
        }
        
    } 
    // .. add deliver
    public static function insert_data_sd($business_id,$transaction,$delivery,$state,$total)
    {
         $data   = StatusLive::orderBy("id","desc")->where("transaction_id",$transaction->id)->where("business_id",$business_id)->first();
         if(empty($data)){
            $item                      = new StatusLive();
            $item->business_id         = $business_id;
            $item->transaction_id      = $transaction->id;
            $item->reference_no        = $delivery->T_delivered->reciept_no;
            $item->state               = $state;
            $item->price               = $total;
            $item->t_delivery          = $delivery->T_delivered->id;
            $item->num_serial          = 1;
            $item->save();
        }else{
            $item                      = new StatusLive();
            $item->business_id         = $business_id;
            $item->transaction_id      = $transaction->id;
            $item->reference_no        = $delivery->T_delivered->reciept_no;
            $item->state               = $state;
            $item->price               = $total;
            $item->t_delivery          = $delivery->T_delivered->id;
            $item->num_serial          = isset($data->num_serial)?$data->num_serial+1:1;
            $item->save();
        }
        
    }
 
    public function transaction()
    {
         return $this->belongsTo("\App\Transaction" , "transaction_id");
    }
    
    
}
