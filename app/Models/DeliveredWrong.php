<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveredWrong extends Model
{
    use HasFactory;
    public function product(Type $var = null)
    {
        return $this->belongsTo('App\Product','product_id');
    }
  
    public function unit(Type $var = null)
    {
        return $this->belongsTo('App\Unit','unit_id');
    }
    public function store(Type $var = null)
    {
        return $this->belongsTo('\App\Models\Warehouse','store_id');
    }
    public function line()
    {
        return $this->belongsTo('App\TransactionSellLine','line_id');
    }
    public function transaction(Type $var = null)
    {
        return $this->belongsTo('App\Transaction','transaction_id');
    }
    public function T_delivered()
    {
        return $this->belongsTo('App\Models\TransactionDelivery','transaction_recieveds_id');
    }
}
