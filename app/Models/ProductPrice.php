<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductPrice extends Model
{
    use HasFactory;
    public function savePrices($data,$business_id){
      
        \DB::beginTransaction();
        $price = new ProductPrice;
        $price->business_id   = $business_id;
        $price->product_id    = $data->product_id;
        $price->date          = $data->date;
        $price->list_of_price = json_encode($data->list); 
        $price->save();
        \DB::commit();
        
    }

    public function unit()   {
            return $this->belongsTo(\App\Unit::class);
    }
    public function product()   {
            return $this->belongsTo(\App\Product::class);
    }
}
