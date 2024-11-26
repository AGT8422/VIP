<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecievedWrong extends Model
{
    use HasFactory;
    public function unit(Type $var = null)
    {
        return $this->belongsTo('\App\Unit','unit_id');
    }
    public function store(Type $var = null)
    {
        return $this->belongsTo('\App\Models\Warehouse','store_id');
    }
    public function transaction()
    {
        return $this->belongsTo('App\Transaction','transaction_id');
    }
    public function purchase_line()
    {
        return $this->belongsTo('App\PurchaseLine','line_id');
    }
    public function product()
    {
        return $this->belongsTo('App\Product','product_id');
    }
    public function TrRecieved()
    {
        return $this->belongsTo('App\Models\TransactionRecieved','transaction_deliveries_id');
    }
}
