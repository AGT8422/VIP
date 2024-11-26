<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArchivePurchaseLine extends Model
{
    use HasFactory;

    // ........ Source is Transaction  ......... \\
    // ******** get lines of purchase  ********* \\
    // ***************************************** \\
    public static function get_purchases($source)
    {
        $purchase_line = \App\PurchaseLine::where("transaction_id",$source)->get();
        return $purchase_line;
    }
    // ........ Source is Transaction  ........ \\
    // **************************************** \\
    public static function save_purchases($archive,$line)
    {
        $parent = \App\Models\ArchivePurchaseLine::orderBy("id","desc")->where("new_id",$line->id)->first();
        if(!empty($line)){
            $item                      = new ArchivePurchaseLine;
            $item->new_id              = $line->id;
            $item->store_id            = $line->store_id;
            $item->product_id          = $line->product_id;
            $item->transaction_id      = $archive->id;
            $item->variation_id        = $line->variation_id;
            $item->quantity            = $line->quantity;
            $item->pp_without_discount = $line->pp_without_discount;
            $item->quantity_returned   = $line->quantity_returned;
            $item->discount_percent    = $line->discount_percent;
            $item->purchase_price      = $line->purchase_price;
            $item->purchase_price_inc_tax = $line->purchase_price_inc_tax;
            $item->item_tax            = $line->item_tax;
            $item->quantity_sold       = $line->quantity_sold;
            $item->quantity_adjusted   = $line->quantity_adjusted;
            $item->quantity_returned   = $line->quantity_returned;
            $item->mfg_quantity_used   = $line->mfg_quantity_used;
            $item->mfg_date            = $line->mfg_date;
            $item->exp_date            = $line->exp_date;
            $item->lot_number          = $line->lot_number;
            $item->sub_unit_id         = $line->sub_unit_id;
            $item->sub_unit_qty        = $line->sub_unit_qty;
            $item->purchase_note       = $line->purchase_note;
            $item->item_tax            = $line->item_tax;
            $item->tax_id              = $line->tax_id;
            $item->bill_return_price   = $line->bill_return_price;
            $item->parent_id           = ($parent)?$parent->id:null;
            $item->main_transaction    = $line->transaction_id;
            $item->save();
            return $item;
        }else{
            return [];
        }
    }
    public function product()
    {
        return $this->belongsTo(\App\Product::class, 'product_id');
    }
    public function transaction()
    {
        return $this->belongsTo(\App\Models\ArchiveTransaction::class,'transaction_id');
    }
    public function variations()
    {
        return $this->belongsTo(\App\Variation::class, 'variation_id');
    }
    public function sub_unit()
    {
        return $this->belongsTo(\App\Unit::class, 'sub_unit_id');
    }


}
