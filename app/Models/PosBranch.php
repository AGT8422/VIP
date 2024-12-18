<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PosBranch extends Model
{
    use HasFactory;

    public static function create_pos($business_id,$data)
    {
            $pattern = \App\Models\Pattern::find($data["pattern"]);
            $business_id = (!empty($pattern))?$pattern->business_id:null;
           
        // ................... create pos .............. \\
            $item                    =  new PosBranch;
            
            $item->name              =  $data["pos"];
            $item->pattern_id        =  $data["pattern"];
            $item->store_id          =  $data["store"];
            $item->invoice_scheme_id =  $data["invoice_scheme"];
            $item->main_cash_id      =  $data["cash_main"];
            $item->cash_id           =  $data["cash"];
            $item->main_visa_id      =  $data["visa_main"];
            $item->visa_id           =  $data["visa"];

            $item->save();
    }
}
