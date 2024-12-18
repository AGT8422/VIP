<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WishList extends Model
{
    use HasFactory;
    use SoftDeletes;

    // ** 1 E-Commerce Add To WishList
    public static function AddWishlist($data,$client_id){
        if(count($data)>0){
            $id       = $data['product_id'];
            $Wishlist = \App\Models\WishList::where("product_id",$id)->where("client_id",$client_id)->first(); 
            if(!empty($Wishlist)){
                
                return  $output = [
                    "status"   => 200,
                    "messages" => __('Product Already Added Successfully'),
                ];

            }else{
                
                $Wishlist             = new \App\Models\WishList();
                $Wishlist->product_id = $id;
                $Wishlist->client_id  = $client_id;
                $Wishlist->save();

                return  $output = [
                    "status"   => 200,
                    "messages" => __('Added Successfully'),
                ]; 
                
            }
      
            return $output = [
                        "status"   => 200 ,
                        "messages" => __('Removed Successfully') ,
                    ];
        }else{
            return response([
                "status"   => 403 ,
                "messages" => __('Invalid Data') ,
            ]);
        }
    }
    // ** 2 E-Commerce Remove From WishList
    public static function RemoveWishlist($data,$client_id){
        if(count($data)>0){
            $id      = $data['product_id'];
            $Wishlist = \App\Models\WishList::where("product_id",$id)->where("client_id",$client_id)->first(); 
            if(empty($Wishlist)){
                return  $output = [
                    "status"   => 200,
                    "messages" => __('Product Already Removed Successfully'),
                ];
            } 
            $Wishlist->delete();
            return $output = [
                        "status"   => 200 ,
                        "messages" => __('Removed Successfully') ,
                    ];
        }else{
            return response([
                "status"   => 403 ,
                "messages" => __('Invalid Data') ,
            ]);
        }
    }
    
    // *** relation
    public function product(){
        return $this->belongsTo("\App\Product","product_id");
    } 
}
