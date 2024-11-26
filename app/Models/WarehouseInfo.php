<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarehouseInfo extends Model
{
    use HasFactory;
    public static function update_stoct($id,$store_id,$quntity,$business_id,$variation_id=null)
    {
        $data =  WarehouseInfo::where('store_id',$store_id)
                            ->where('product_id',$id);
        if($variation_id!=null){
            $data->where('variation_id',$variation_id);
        }                    
        $data = $data->first();
        
        if ($data) {
            $data->product_qty = $data->product_qty +  $quntity;
            $data->update(); 
         }else { 
             if($store_id){
                $data =  new WarehouseInfo;
                $data->business_id =  $business_id;
                $data->store_id    =  $store_id;
                if($variation_id != null){
                    $data->variation_id    =  $variation_id;
                }
                $data->product_qty =  $quntity;
                $data->product_id  =  $id;
                $data->save();
             }
         }
    }
    public static function deliver_stoct($id,$store_id,$quntity,$type=null, $business_id)
    {
        $data =  WarehouseInfo::where('store_id',$store_id)
                        ->where('product_id',$id)->first();
                    
        if ($data) {
            if($type == null){
                $data->decrement('product_qty',$quntity);
                $data->save(); 
            }else{
                $data->increment('product_qty',$quntity);
                $data->save(); 
            }
        }else {
            if($store_id){
                $data              =  new WarehouseInfo;
                $data->business_id =  $business_id;
                $data->store_id    =  $store_id;
                $data->product_qty =  $quntity;
                $data->product_id  =  $id;
                $data->save();
             }
        } 
    }

    //** ............ EB 

    public static function transferfromTo($id,$store_id,$transfer_store_id,$quntity,$business_id)
    {

         $ware_source = WarehouseInfo::where("store_id",$store_id)
                        ->where("product_id",$id)->first();

         $ware_destination = WarehouseInfo::where("store_id",$transfer_store_id)
                        ->where("product_id",$id)->first();
 

         if ($ware_destination) {
            $ware_destination->increment('product_qty',$quntity);
            $ware_destination->save(); 

         }else {
            $ware_destination              =  new WarehouseInfo;
            $ware_destination->business_id =  $business_id;
            $ware_destination->store_id    =  $transfer_store_id;
            $ware_destination->product_qty =  $quntity;
            $ware_destination->product_id  =  $id;
            $ware_destination->save();
         }

         $ware_source->decrement('product_qty',$quntity);
         $ware_source->save(); 

    }

    /**............ */

    public function products()
    {
        return $this->hasOne('\App\Product','id');
    }
    public function product()
    {
        return $this->belongsTo('\App\Product','id');
    }
    public function store()
    {
        return $this->belongsTo('\App\Models\Warehouse','store_id');
    }
    public static function qty_before($data)
    {
        ($data->type=="purchase")?$type=1:$type=0;
        if($type==1){
            $line = \App\PurchaseLine::where("transaction_id",$data->id)->get();
        }else{
            $line = \App\TransactionSellLine::where("transaction_id",$data->id)->get();
        }
        $ids      = [];
        $qties    = [];
        foreach($line as $li){ if(!in_array($li->product_id,$ids)){$ids[]  = $li->product_id;}}   
        foreach($ids as $i){
            $current = \App\Models\WarehouseInfo::where("product_id",$i)->sum("product_qty");
            array_push($qties,(object)[ "id" => $i, "qty" => $current]);
        }
        return $qties;
    }
    public static function qty_after($data)
    {
        ($data->type=="purchase")?$type=1:$type=0;
        if($type==1){
            $line = \App\PurchaseLine::where("transaction_id",$data->id)->get();
        }else{
            $line = \App\TransactionSellLine::where("transaction_id",$data->id)->get();
        }
        $ids      = [];
        $qties    = [];
        foreach($line as $li){ if(!in_array($li->product_id,$ids)){$ids[]  = $li->product_id;}}   
        foreach($ids as $i){
            $current = \App\Models\WarehouseInfo::where("product_id",$i)->sum("product_qty");
            array_push($qties,(object)[ "id" => $i, "qty" => $current]);
        }
     
        return $qties;
    }
    
    public static function store_qty($id,$store,$product_id)
    {
        //....... sum product qty in one store
        $sum  = \App\Models\WarehouseInfo::where("business_id",$id)->where("product_id",$product_id)->where("store_id",$store)->sum("product_qty");
        return  $sum;
    }
    public static function store_decrement($id,$store,$product_id,$qty)
    {
        //....... decrement product qty in one store
        $sum  = \App\Models\WarehouseInfo::where("business_id",$id)->where("product_id",$product_id)->where("store_id",$store)->decrement("product_qty",$qty);
        return  $sum;
    }
    public static function store_increment($id,$store,$product_id,$qty)
    {
        //....... inecrement product qty in one store
        $sum  = \App\Models\WarehouseInfo::where("business_id",$id)->where("product_id",$product_id)->where("store_id",$store)->increment("product_qty",$qty);
        return  $sum;
    }
    public static function zero_qty($id)
    {
        try{
            //....... update product qty in one store
            $sum         = \App\Models\WarehouseInfo::where("business_id",$id)->update(["product_qty"=>0]);
            $move        = \App\Models\ItemMove::where("business_id",$id)->delete();
            $StatusLive  = \App\Models\StatusLive::where("business_id",$id)->delete();
            $Wmove       = \App\MovementWarehouse::where("business_id",$id)->delete();
            $Voucher     = \App\Models\PaymentVoucher::where("business_id",$id)->delete();
            $Entry       = \App\Models\Entry::where("business_id",$id)->delete();
            $Check       = \App\Models\Check::where("business_id",$id)->delete();


            //........ open quantity
            $open        = \App\Models\OpeningQuantity::select()->get();
            foreach($open as $it){
                $it->delete();
            }

            
            //.......... action
            $action      = \App\AccountTransaction::select()->get();
            foreach($action as $it){
                $it->delete();
            }

            //..............
            $TR          = \App\Transaction::select()->get();
            $TR_p        = \App\TransactionPayment::select()->get();
            foreach($TR as $it){
                $it->delete();
            }
            foreach($TR_p as $it){
                $it->delete();
            }


            //..............
            $TR_d        = \App\Models\TransactionDelivery::select()->get();
            $TR_r        = \App\Models\TransactionRecieved::select()->get();
            foreach($TR_d as $it){
                $it->delete();
            }
            foreach($TR_r as $it){
                $it->delete();
            }

            //..............
            $R_p         = \App\Models\RecievedPrevious::select()->get();
            $R_p_W       = \App\Models\RecievedWrong::select()->get();
            foreach($R_p as $it){
                $it->delete();
            }
            foreach($R_p_W as $it){
                $it->delete();
            }
            
            //..............
            $R_d         = \App\Models\DeliveredPrevious::select()->get();
            $R_d_W       = \App\Models\DeliveredWrong::select()->get();
            foreach($R_d as $it){
                $it->delete();
            }
            foreach($R_d_W as $it){
                $it->delete();
            }
            
            
            //..............
            $g_v         = \App\Models\GournalVoucher::select()->get();
            $g_v_i       = \App\Models\GournalVoucherItem::select()->get();
            foreach($g_v as $it){
                $it->delete();
            }
            foreach($g_v_i as $it){
                $it->delete();
            }
            
            //..............
            $add_s       = \App\Models\AdditionalShipping::select()->get();
            $add_s_i     = \App\Models\AdditionalShippingItem::select()->get();
            foreach($add_s as $it){
                $it->delete();
            }
            foreach($add_s_i as $it){
                $it->delete();
            }

            
            //..............
            $d_p         = \App\Models\DailyPayment::select()->get();
            $d_p_i       = \App\Models\DailyPaymentItem::select()->get();
 
            foreach($d_p as $it){
                $it->delete();
            }
            foreach($d_p_i as $it){
                $it->delete();
            }
            
            
            
            $output   = [
                    "success"=>true,
                    "msg"=>__("messages.added_successfully")
            ];
            
        }catch(Exception $e){
            $output   = [
                    "success"=>false,
                    "msg"=>__("messages.something_wrong")
            ];
        }
        return  $output;
    }


}
