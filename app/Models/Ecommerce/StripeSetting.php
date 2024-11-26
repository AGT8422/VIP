<?php

namespace App\Models\Ecommerce;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StripeSetting extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = "stripe_settings";
    // **  list STRIPE E_COMMERCE INFO
    public static function getInfo(){
        $stripe =  StripeSetting::get();
        $list   = [];
        foreach($stripe as $item){
            $list[$item->id] = $item->url_website;
        }
        return $list;
    }
    // ** Edit STRIPE E_COMMERCE INFO
    public static function editInfo(Request $request ,$id){
        
    }
    // ** Update STRIPE E_COMMERCE INFO
    public static function updateInfo(Request $request ,$id){
        
    }
     
}
