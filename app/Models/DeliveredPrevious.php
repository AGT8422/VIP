<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\MovementWarehouse;
use App\Utils\productUtil;

class DeliveredPrevious extends Model
{
    use HasFactory,SoftDeletes;
    
    
    public function product()
    {
        return $this->belongsTo('App\Product','product_id');
    }
    public function store()
    {
        return $this->belongsTo('App\Models\Warehouse','store_id');
    }
    public function line()
    {
        return $this->belongsTo('App\TransactionSellLine','line_id');
    }
    public function unit()
    {
        return $this->belongsTo('App\Unit','unit_id');
    }
    public function transaction()
    {
        return $this->belongsTo('App\Transaction','transaction_id');
    }
    public function T_delivered()
    {
        return $this->belongsTo('App\Models\TransactionDelivery','transaction_recieveds_id');
    }
    public static function recieve($data,$product,$quntity,$store_id,$tr_recieved,$line)
    {
        $prev                  =  new DeliveredPrevious;
        $prev->product_id      =  $product->id;
        $prev->store_id        =  $store_id;
        $prev->business_id     =  $data->business_id ;
        $prev->transaction_id  =  $data->id;
        $prev->unit_id         =  $product->unit_id;
        $prev->total_qty       =  $line->quantity;
        $prev->current_qty     =  $quntity;
        $prev->remain_qty      =  0;
        $prev->transaction_recieveds_id   =  $tr_recieved->id;
        $prev->product_name   =  $product->name;
        $prev->save();
        WarehouseInfo::deliver_stoct($product->id,$store_id,$quntity,null,$data->business_id);
        MovementWarehouse::movemnet_warehouse_sell($data,$product,$quntity,$store_id,$line,$prev->id);
        $pr_util =  new productUtil;
        $pr_util->decreaseProductQuantity(
            $product->id,
            $line->variation_id,
            $data->location_id,
            $quntity
        );
        
    }
}
