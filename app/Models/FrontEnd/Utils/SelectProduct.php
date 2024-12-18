<?php

namespace App\Models\FrontEnd\Utils;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SelectProduct extends Model
{
    use HasFactory;
    // **** AGT FOR SELECT PRODUCT SECTION REACT FRONT END **** //
    // ***1 
    public static function openQuantity($id) {
        $list                     = [] ;
        $product                  = \App\Product::where("id",$id)->select(["id","sku","name","unit_id","sub_unit_ids as sub_unit"])->first();
        $stock                    = \App\Models\WarehouseInfo::where("product_id",$id)->sum("product_qty");
        $cost                     = \App\Models\ItemMove::orderBy("date","desc")->orderBy("id","desc")->where("product_id",$id)->first();
        $list_product             = [];
        $list_product["id"]       = $product->id;
        $list_product["name"]     = $product->name;
        $list_product["code"]     = $product->sku;
        $list_unit = []; $un = [];
        if($product->sub_unit != null){
            $list_price      = \App\Product::getProductPrices($product->id);
            $unit2           = \App\Unit::find($product->unit_id);
            $list_unit[]     = ["id" => $unit2->id ,"value" => $unit2->actual_name ,"unit_quantity"=>($unit2->base_unit_multiplier == null)?1:$unit2->base_unit_multiplier,"list_price"=> isset($list_price[$unit2->id])?$list_price[$unit2->id]:[]];
            $un []           = $unit2->id;
            $all             = json_decode( $product->sub_unit );
            foreach($all as $i){
                $unit            = \App\Unit::find($i);
                if(!in_array($i,$un)){
                    $list_unit[]   = ["id" => $i ,"value" => $unit->actual_name ,"unit_quantity"=>($unit->base_unit_multiplier == null)?1:$unit->base_unit_multiplier,"list_price"=> isset($list_price[$i])?$list_price[$i]:[]];
                    $un [] =  $i;
                }
            }
            $list_product['all_units']      =  $list_unit ;
            
        }else{
            $unit          = \App\Unit::find($product->unit_id);$list_price      = \App\Product::getProductPrices($product->id);
            $list_unit[]   = ["id" => $product->unit_id ,"value" => $unit->actual_name ,"unit_quantity"=>($unit->base_unit_multiplier == null)?1:$unit->base_unit_multiplier,"list_price"=> isset($list_price[$product->unit_id])?$list_price[$product->unit_id]:[]];
            $list_product['all_units']      =  $list_unit ;
        }
        $list["product"]          = $list_product;
        $list["cost"]             = (!empty($cost))?$cost->unit_cost:0;
        $list["stock"]            = $stock;
        return $list;
    }
    // ***2 
    public static function product($id) {
        $list                     = [] ;
        $product                  = \App\Product::where("id",$id)->select(["id","sku","name","unit_id","sub_unit_ids as sub_unit"])->first();
        $variation                = \App\Variation::where("product_id",$id)->first();
        $stock                    = \App\Models\WarehouseInfo::where("product_id",$id)->sum("product_qty");
        $cost                     = \App\Models\ItemMove::orderBy("date","desc")->orderBy("id","desc")->where("product_id",$id)->first();
        $list_product             = [];
        $list_product["id"]       = $product->id;
        $list_product["name"]     = $product->name;
        $list_product["code"]     = $product->sku;
        $list_unit = []; $un = [];
        if($product->sub_unit != null){
            $list_price      = \App\Product::getProductPrices($product->id);
            $unit2           = \App\Unit::find($product->unit_id);
            $list_unit[]     = ["id" => $unit2->id ,"value" => $unit2->actual_name ,"unit_quantity"=>($unit2->base_unit_multiplier == null)?1:$unit2->base_unit_multiplier,"list_price"=> isset($list_price[$unit2->id])?$list_price[$unit2->id]:[]];
            $un []           = $unit2->id;
            $all             = json_decode( $product->sub_unit );
            foreach($all as $i){
                $unit            = \App\Unit::find($i);
                if(!in_array($i,$un)){
                    $list_unit[]   = ["id" => $i ,"value" => $unit->actual_name ,"unit_quantity"=>($unit->base_unit_multiplier == null)?1:$unit->base_unit_multiplier,"list_price"=> isset($list_price[$i])?$list_price[$i]:[]];
                    $un [] =  $i;
                }
            }
            $list_product['all_units']      =  $list_unit ;
            
        }else{
            $unit          = \App\Unit::find($product->unit_id);
            $list_unit[]   = ["id" => $product->unit_id ,"value" => $unit->actual_name ,"unit_quantity"=>($unit->base_unit_multiplier == null)?1:$unit->base_unit_multiplier,"list_price"=> isset($list_price[$product->unit_id])?$list_price[$product->unit_id]:[]];
            $list_product['all_units']      =  $list_unit ;
        }
        $list["product"]          = $list_product;
        $list["cost"]             = (!empty($cost))?$cost->unit_cost:0;
        $list["stock"]            = $stock;
        $list["purchase_price"]   = $variation->default_purchase_price;
        return $list;
    }
}
