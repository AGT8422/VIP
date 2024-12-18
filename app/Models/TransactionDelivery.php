<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransactionDelivery extends Model
{
    use HasFactory,SoftDeletes;
    public function wrongs(Type $var = null)
    {
        return $this->hasMany('\App\Models\DeliveredWrong','transaction_recieveds_id');
    }
     public static function get_string_between($string, $start, $end){
        $string = ' ' . $string;
        $ini = strpos($string, $start);
        if ($ini == 0) return '';
        $ini += strlen($start);
        $len = strpos($string, $end, $ini) - $ini;
        return substr($string, $ini, $len);
        
    }
    public function store()
    {
         return $this->belongsTo("\App\Models\Warehouse","store_id");
    }
    public static function childs($id)
    {
        $childs_ids = \App\Models\DeliveredPrevious::where("transaction_recieveds_id",$id)->get();
        $array = [];
        foreach($childs_ids as $item){ $array[] = $item->id; }
        return $array;
    }
    public static function childs_wrong($id)
    {
        $childs_ids = \App\Models\DeliveredWrong::where("transaction_recieveds_id",$id)->get();
        $array = [];
        foreach($childs_ids as $item){ $array[] = $item->id; }
        return $array;
    }
}
