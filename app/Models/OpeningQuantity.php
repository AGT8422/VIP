<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OpeningQuantity extends Model
{
    use HasFactory,SoftDeletes;
    public function product()
    {
        return $this->belongsTo('App\Product','product_id');
    }
    public function transaction()
    {
        return $this->belongsTo('App\Transaction','transaction_id');
    }
    public function lines()
    {
        return $this->belongsTo('App\PurchaseLine','purchase_line_id');
    }
    public function store()
    {
        return $this->belongsTo('App\Models\Warehouse','warehouse_id');
    }
    public function location()
    {
        return $this->belongsTo('App\BusinessLocation','business_location_id');
    }
}
