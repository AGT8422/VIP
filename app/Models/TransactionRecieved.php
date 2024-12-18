<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\RecievedPrevious;

class TransactionRecieved extends Model
{
    use HasFactory;

    public function transactionDetails($product_id,$business_id)
    {
        $transaction_recieve = TransactionRecieved::where("business_id",$business_id)->get();
        $RecievedPrevious = RecievedPrevious::where("transaction_deliveries_id",$transaction_recieve->id)->get();
         
        $list_previous = [];


        return "1";

    }
    public function transaction()
    {
        return $this->belongsTo('App\Transaction','transaction_id');
    }
    public function store()
    {
        return $this->belongsTo('App\Models\Warehouse','store_id');
    }
    public function user()
    {
        return $this->belongsTo('App\Models\User','created_by');
    }
    public static function childs($id)
    {
        $childs_ids = \App\Models\RecievedPrevious::where("transaction_deliveries_id",$id)->get();
        $array = [];
        foreach($childs_ids as $item){ $array[] = $item->id; }
        return $array;
    }
    public static function childs_wrong($id)
    {
        $childs_ids = \App\Models\RecievedWrong::where("transaction_deliveries_id",$id)->get();
        $array = [];
        foreach($childs_ids as $item){ $array[] = $item->id; }
        return $array;
    }
}
