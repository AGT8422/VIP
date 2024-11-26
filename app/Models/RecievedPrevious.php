<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecievedPrevious extends Model
{
    use HasFactory;
    public function transaction()
    {
        return $this->belongsTo('App\Transaction','transaction_id');
    }
    public function TrRecieved()
    {
        return $this->belongsTo('App\Models\TransactionRecieved','transaction_deliveries_id');
    }

    public function product()
    {
        return $this->belongsTo('App\Product','product_id');
    }
    public function purchase_line()
    {
        return $this->belongsTo('App\PurchaseLine','line_id');
    }
    public function unit()
    {
        return $this->belongsTo('App\Unit','unit_id');
    }
    public function store()
    {
        return $this->belongsTo('App\Models\Warehouse','store_id');
    }
    public function user()
    {
        return $this->belongsTo('App\Models\User','created_by');
    }
    public static function qty($id)
    {
        $data          = \App\Models\TransactionRecieved::where("transaction_id",$id)->first();
        $total_qty     = RecievedPrevious::where("transaction_deliveries_id",$data->id)->sum("current_qty");
        return $total_qty;
    }
}
