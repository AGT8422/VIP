<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RequestReturn extends Model
{
    use HasFactory , SoftDeletes;

    // *1* send  Return Request 
    // *** AGT8422
    public static function sendRequest($data,$client){
        try {
            $req                           =  new RequestReturn();
            $req->client_id                =  $client->id; 
            $req->transaction_id           =  $data["bill_id"]; 
            $req->transaction_sell_line_id =  json_encode($data["items"]); 
            $req->quantity                 =  json_encode($data["quantity"]); 
            $req->save(); 
            return true;
        } catch (Exception $e) {
            return false;
        }
       
    }
    
    // *2* update Return Request 
    // *** AGT8422
    public static function UpdateRequest($data,$id,$client){
        try {
            $req                           =  RequestReturn::find($id);
            $req->client_id                =  $client->id; 
            $req->transaction_id           =  $data["bill_id"]; 
            $req->transaction_sell_line_id =  json_encode($data["item_id"]); 
            $req->quantity                 =  json_encode($data["quantity"]); 
            $req->update();             
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
    
    // *3* delete Return Request 
    // *** AGT8422
    public static function DeleteRequest($data,$id){
        try {
            $req                           =  RequestReturn::find($id);
            $req->delete();            
            return true;
        } catch (Exception $e) {
            return false;
        } 
    }
    
    // *4* list Return Request 
    // *** AGT8422
    public static function GetLastReturn($data,$client){
        try {
            $req                           =  RequestReturn::where("client_id",$client->id)->get();
            $list          = [];
            foreach($req as $i){
                $items        = json_decode($i->transaction_sell_line_id);
                $quantity     = json_decode($i->quantity);
                $list_items   = [] ;
                foreach($items as $key => $it){
                    $child        = \App\TransactionSellLine::find($it);
                    if(!$child){
                        abort(403,"Invalid Data");
                    }
                    $list_items[] = [
                                "id"                    => $child->id,         
                                "product_name"          => $child->product->name,   
                                "image"                 => $child->product->image_url,        
                                "quantity"              => round($quantity[$key],2),
                                "category"              => $child->product->category->name,            
                                "product_category"      => $child->product->sub_category->name,
                                "product_price_exc_tax" => round($child->product->variations[0]->default_sell_price,2),
                                "product_price_inc_tax" => round($child->product->variations[0]->sell_price_inc_tax,2), 
                                "warranty"              => (count($child->warranties)>0)?$child->warranties[0]->name . "<br>" . $child->warranties[0]->description:"",
                                "wishlist"              => ($child->product->wishlist != null)?true :false              
                    ];
                }
                $list[] = [
                        "date"      => $i->created_at->format("Y-m-d h:i:s a"),
                        "bill_id"   => $i->transaction_id,
                        "items"     => $list_items,
                ];
            }          
            return $list;
        } catch (Exception $e) {
            return false;
        } 
    }

}
