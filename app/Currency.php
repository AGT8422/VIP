<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    //
    public static function currency(){
        $currency = \App\Currency::get();
        $array    = [];
        foreach($currency as $i){
            $array[] = [
                "id"    => $i->id,
                "value" => ' ( ' .$i->country . ' - ' . $i->currency . ' - '  . $i->code . ' - ' . $i->symbol .' ) ' 
            ];
        }
        return $array;
    }
}
