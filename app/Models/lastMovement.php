<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class lastMovement extends Model
{
    use HasFactory,SoftDeletes;

    // 1 / E-COMMERCE AGT8422 LAST MOVEMENT
    public static function saveMovement($data,$client)  {
        try{
            $lastMove                   = new lastMovement();
            $lastMove->client_id        = $client->id ;
            $lastMove->url              = $data['url'] ;
            $lastMove->type             = $data['type'] ;
            $lastMove->product_id       = $data['product_id'] ;
            $lastMove->reference_number = $data['reference_number'] ;
            $lastMove->order_no         = $data['order_no'] ;
            $lastMove->save();
            return true ;
        }catch(Exception $e){
            return false ;
        }
    }

    // 2 / E-COMMERCE AGT8422 LAST MOVEMENT
    public static function GetLastProduct($data,$client) {
        try{
            $listProduct   =   \App\Models\lastMovement::orderBy("id","desc")->where("client_id",$client->id)->select(["url","product_id"])->limit(5)->get();
            $list          = [];
            foreach($listProduct as $pro){
                $list[] = [
                        "id"            => $pro->product->id,
                        "name"          => $pro->product->name,
                        "category"      => $pro->product->sub_category->name,
                        "price_exc_tax" => $pro->product->variations[0]->default_sell_price,
                        "price_inc_tax" => $pro->product->variations[0]->sell_price_inc_tax,
                ];
            }
            return $list;
        }catch(Exception $e){
           return false; 
        }
    }

    // 3 / E-COMMERCE AGT8422 LAST MOVEMENT
    public function product(){
        return $this->belongsTo("\App\Product","product_id");
    }


}
