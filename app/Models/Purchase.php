<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class Purchase extends Model
{
    use HasFactory;

    public static function transaction()
    {
        return $this->belongsTo("App\Transaction","transaction_id");
    }

    public static function supplier_shipp($id)
    {
      
        $transaction = null;
        $total_ship  = null;
        $transaction = \App\Transaction::find($id);
        $ship = \App\Models\AdditionalShipping::where("transaction_id",$id)->select("id")->first();
        if($ship != null ){
        
            $shipment = \App\Models\AdditionalShippingItem::where("additional_shipping_id",$ship->id)->where("contact_id",($transaction)?$transaction->contact_id:null)->select(DB::raw('SUM(total) as total'))->first()->total;
            
            $total_ship = ($shipment != null)?$shipment:null;
        }
        return $total_ship;
    }

}
