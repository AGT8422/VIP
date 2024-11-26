<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Models\RecievedPrevious;


class PurchaseLine extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected  $guarded = ['id'];
    protected  $appends = ['margin'];
     public function getMarginAttribute()
    {
        $recived  =  RecievedPrevious::where('product_id',$this->attributes['product_id'])
                            ->where('transaction_id',$this->attributes['transaction_id'])->sum('current_qty');
        return $this->attributes['quantity'] - $recived  ;
    }
    public function transaction()
    {
        return $this->belongsTo(\App\Transaction::class,'transaction_id');
    }

    public function product()
    {
        return $this->belongsTo(\App\Product::class, 'product_id');
    }
    
    public static function delete_all_wrong($id)
    {   
        $act = \App\AccountTransaction::where("transaction_id",$id)->whereNotNull("additional_shipping_item_id")->get();
        $add = \App\Models\AdditionalShippingItem::whereHas("additional_shipping",function($query) use($id){
                                                            $query->where("transaction_id",$id);                                                
                                                        })->get();
        $map = \App\Models\StatusLive::where("transaction_id",$id)->whereNotNull("shipping_item_id")->get();

        $deleteshipp = 1;
        $deletemap   = 1;
        foreach($act as $it){
            foreach($add as $i){
                if($it->additional_shipping_item_id == $i->id ){
                    $deleteshipp = 0;
                }else{
                    $deleteshipp = 1;
                }
            }
            if($deleteshipp == 1){
                $it->delete();
            }
        }
        foreach($map as $m){
            foreach($add as $i){
                if($m->shipping_item_id == $i->id ){
                    $deletemap = 0;
                }else{
                    
                    $deletemap = 1;
                }
            }
            if($deletemap == 1){
                $m->delete();
            }
        }
        
    }

    public function getDocumentAttribute()
    {
        return json_decode($this->attributes['document']);
    }

    public function variations()
    {
        return $this->belongsTo(\App\Variation::class, 'variation_id');
    }

    /**
     * Set the quantity.
     *
     * @param  string  $value
     * @return float $value
     */
   public function getQuantityAttribute($value)
    {
        return (float)$value;
    }

    /**
     * Get the unit associated with the purchase line.
     */
    public function sub_unit()
    {
        return $this->belongsTo(\App\Unit::class, 'sub_unit_id');
    }

    /**
     * Get the unit associated with the purchase line.
     */
    public function warehouse()
    {
        return $this->belongsTo(\App\Models\Warehouse::class, 'store_id');
    }

    /**
     * Give the quantity remaining for a particular
     * purchase line.
     *
     * @return float $value
     */
    public function getQuantityRemainingAttribute()
    {
        return (float)($this->quantity - $this->quantity_used);
    }

    /**
     * Give the sum of quantity sold, adjusted, returned.
     *
     * @return float $value
     */
    public function getQuantityUsedAttribute()
    {
        return (float)($this->quantity_sold + $this->quantity_adjusted + $this->quantity_returned + $this->mfg_quantity_used);
    }

    public function line_tax()
    {
        return $this->belongsTo(\App\TaxRate::class, 'tax_id');
    }
    public static function check_transation_product($id,$product_id)
    {
         $data =  PurchaseLine::where('transaction_id',$id)->where('product_id',$product_id)->sum("quantity");
         if($data){
            //cjheck  quantity 
            $diff  =  $data - RecievedPrevious::where('transaction_id',$id) 
                                            ->where('product_id',$product_id)->sum('current_qty');
            return $diff;
         }else {
            return 0;
         }
    }
    public static function check_transation_product_return($id,$product_id,$isd=null)
    {
         $data =  PurchaseLine::where('transaction_id',$id)->where('product_id',$product_id)->sum("quantity_returned");
         $tr   =  \App\Transaction::where("return_parent_id",$id)->first();
         if($data){

            //cjheck  quantity 
            $diff  =  $data - RecievedPrevious::whereHas("transaction",function($query) use($tr){
                                                        $query->where("id",$tr->id);
                                                    }) 
                                                    ->where('product_id',$product_id)->where("is_returned",1)->sum('current_qty');
           
            return $diff;
         }else {
            return 0;
         }
    }
     public static function getPercentOfShipping($transaction_id)
    {
        $product_id   = [];
        $total        = 0;
        $transaction  = Transaction::find($transaction_id);
        if(isset($transaction)){
            $shipping     = $transaction->shipping_charges;
        }else{
            $shipping     = 0;
        }
        
        $purchaseLine = PurchaseLine::where("transaction_id",$transaction_id)->get();
        foreach($purchaseLine as $value){
            $total = $total + $value->quantity;
            $id       = $value->product_id;
            if(!in_array($id,$product_id,false)){ 
                array_push($product_id,$id);
                $product_id[$id] =  $value->quantity;
            }elseif(isset($product_id[$id])){
                $product_id[$id] = $product_id[$id] +  $value->quantity;
            }
        }
        //............ EB LIKE 50 FROM (1000 / 20 )
        $ship_one = $shipping / $total ;
        $array = [];
        foreach($product_id as $key => $value){
            if($key != 0){
               $percent =  $value / $total ;
               $cost = $percent * $ship_one ;
               $array[$key] = $cost;  
            }
        }

        return $array ;
    }
}

