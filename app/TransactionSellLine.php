<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\DeliveredPrevious;


class TransactionSellLine extends Model
{
     use  SoftDeletes;
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];
    protected  $appends = ['margin'];

    public function transaction()
    {
        return $this->belongsTo(\App\Transaction::class);
    }
    public function getMarginAttribute()
    {
        if(isset($this->attributes['product_id'])){
        $recived  =  DeliveredPrevious::where('product_id',$this->attributes['product_id'])
                            ->where('transaction_id',$this->attributes['transaction_id'])->sum('current_qty');
        return $this->attributes['quantity'] - $recived  ;
        }else{
            return 0;
        }
    }
    public function product()
    {
        return $this->belongsTo(\App\Product::class, 'product_id');
    }

    public function variations()
    {
        return $this->belongsTo(\App\Variation::class, 'variation_id');
    }
    public static function check_transation_product($id,$product_id)
    {
         $data =  TransactionSellLine::where('transaction_id',$id)->where('product_id',$product_id)->sum("quantity");
         if($data){
            //cjheck  quantity 
            $diff  =  $data - DeliveredPrevious::where('transaction_id',$id) 
                                                    ->where('product_id',$product_id)
                                                    ->sum('current_qty');
            return $diff;
         }else {
            return 0;
         }
    }
    public static function check_transation_product_return($id,$product_id)
    {
         $data =  TransactionSellLine::where('transaction_id',$id)->where('product_id',$product_id)->sum("quantity_returned");
         if($data){
            //cjheck  quantity 
            $diff  =  $data - DeliveredPrevious::whereHas("transaction",function($query) use($id){
                                                    $query->where("id",$id);
                                                    })->whereHas("T_delivered",function($query){
                                                        $query->where("is_returned",1);
                                                    }) 
                                                    ->where('product_id',$product_id)
                                                    ->sum('current_qty');
            return $diff;
         }else {
            return 0;
         }
    }
    public function modifiers()
    {
        return $this->hasMany(\App\TransactionSellLine::class, 'parent_sell_line_id')
            ->where('children_type', 'modifier');
    }

    public function sell_line_purchase_lines()
    {
        return $this->hasMany(\App\TransactionSellLinesPurchaseLines::class, 'sell_line_id');
    }

    /**
     * Get the quantity column.
     *
     * @param  string  $value
     * @return float $value
     */
    public function getQuantityAttribute($value)
    {
        return (float)$value;
    }

    public function lot_details()
    {
        return $this->belongsTo(\App\PurchaseLine::class, 'lot_no_line_id');
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

    /**
     * Get the unit associated with the purchase line.
     */
    public function sub_unit()
    {
        return $this->belongsTo(\App\Unit::class, 'sub_unit_id');
    }

    public function order_statuses()
    {
        $statuses = [
            'received',
            'cooked',
            'served'
        ];
    }
    public function getDates()
    {
        return [];
    }
    public function service_staff()
    {
        return $this->belongsTo(\App\User::class, 'res_service_staff_id');
    }
    public function store()
    {
        return $this->belongsTo(\App\Models\Warehouse::class, 'store_id');
    }

    /**
     * The warranties that belong to the sell lines.
     */
    public function warranties()
    {
        return $this->belongsToMany('App\Warranty', 'sell_line_warranties', 'sell_line_id', 'warranty_id');
    }

    public function line_tax()
    {
        return $this->belongsTo(\App\TaxRate::class, 'tax_id');
    }
    public function getDateFormat()
    {
         return 'Y-m-d H:i:s.u';
    }
}
