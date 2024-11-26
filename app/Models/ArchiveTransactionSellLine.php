<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArchiveTransactionSellLine extends Model
{
    use HasFactory;

    // ........ Source is Transaction  ......... \\
    // ******** get lines of purchase  ********* \\
    // ***************************************** \\
    public static function get_sells_line($source)
    {
        $sell_line = \App\TransactionSellLine::where("transaction_id",$source)->get();
        return $sell_line;
    }
    // ........ Source is Transaction  ........ \\
    // **************************************** \\
    public static function save_sells_line($archive,$line)
    {
        $parent = \App\Models\ArchiveTransactionSellLine::orderBy("id","desc")->where("new_id",$line->id)->first();
        if(!empty($line)){
            $item                        =  new ArchiveTransactionSellLine;
            $item->new_id                = $line->id;
            $item->store_id              = $line->store_id;
            $item->transaction_id        = $archive->id;
            $item->product_id            = $line->product_id;
            $item->variation_id          = $line->variation_id;
            $item->quantity              = $line->quantity;
            $item->mfg_waste_percent     = $line->mfg_waste_percent;
            $item->quantity_returned     = $line->quantity_returned;
            $item->unit_price_before_discount = $line->unit_price_before_discount;
            $item->unit_price            = $line->unit_price;
            $item->line_discount_type    = $line->line_discount_type;
            $item->line_discount_amount  = $line->line_discount_amount;
            $item->unit_price_inc_tax    = $line->unit_price_inc_tax;
            $item->item_tax              = $line->item_tax;
            $item->tax_id                = $line->tax_id;
            $item->discount_id           = $line->discount_id;
            $item->lot_no_line_id        = $line->lot_no_line_id;
            $item->sell_line_note        = $line->sell_line_note;
            $item->woocommerce_line_items_id = $line->woocommerce_line_items_id;
            $item->so_line_id            = $line->so_line_id;
            $item->so_quantity_invoiced  = $line->so_quantity_invoiced;
            $item->res_service_staff_id  = $line->res_service_staff_id;
            $item->res_line_order_status = $line->res_line_order_status;
            $item->parent_sell_line_id   = $line->parent_sell_line_id;
            $item->children_type         = $line->children_type;
            $item->sub_unit_id           = $line->sub_unit_id;
            $item->kitchen_status        = $line->kitchen_status;
            $item->bill_return_price     = $line->bill_return_price;
            $item->parent_id             = ($parent)?$parent->id:null;
            $item->main_transaction      = $line->transaction_id;
            $item->save();
            return $item;
        }else{
            return [];
        }
    }

    public function transaction()
    {
        return $this->belongsTo(\App\Transaction::class);
    }
    public function product()
    {
        return $this->belongsTo(\App\Product::class, 'product_id');
    }
    public function variations()
    {
        return $this->belongsTo(\App\Variation::class, 'variation_id');
    }
    public function modifiers()
    {
        return $this->hasMany(\App\TransactionSellLine::class, 'parent_sell_line_id')
            ->where('children_type', 'modifier');
    }
    public function lot_details()
    {
        return $this->belongsTo(\App\PurchaseLine::class, 'lot_no_line_id');
    }
    public function sub_unit()
    {
        return $this->belongsTo(\App\Unit::class, 'sub_unit_id');
    }
    public function service_staff()
    {
        return $this->belongsTo(\App\User::class, 'res_service_staff_id');
    }
    public function warranties()
    {
        return $this->belongsToMany('App\Warranty', 'sell_line_warranties', 'sell_line_id', 'warranty_id');
    }
    public function get_discount_amount_s()
    {
        $discount_amount = 0;
        if (!empty($this->line_discount_type) && !empty($this->line_discount_amount)) {
            if ($this->line_discount_type == 'fixed') {
                $discount_amount = $this->line_discount_amount;
            } elseif ($this->line_discount_type == 'percentage') {
                $discount_amount = $this->line_discount_amount ;
            }
        }
       
        return $discount_amount;
    }
    public function get_discount_amount()
    {
        $discount_amount = 0;
        if (!empty($this->line_discount_type) && !empty($this->line_discount_amount)) {
            if ($this->line_discount_type == 'fixed') {
                $discount_amount = $this->line_discount_amount;
            } elseif ($this->line_discount_type == 'percentage') {
                $discount_amount = ($this->unit_price_before_discount * $this->line_discount_amount) / 100;
            }
        }
   
        return $discount_amount;
    }
}
