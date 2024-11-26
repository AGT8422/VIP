<?php

namespace App\Models\FrontEnd\Utils;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\FrontEnd\Utils\GlobalUtil;
class SearchProduct extends Model
{
    use HasFactory;
    // ******* AGT FOR REACT ******** //
    // *** 1 SEARCH PRODUCT /OPEN QUANTITY
    public static function openQuantity($user,$data) {
        $business_id           = $user->business_id;
        $location              = \App\BusinessLocation::where("business_id",$business_id)->first();
        $term                  = $data["value"];
        $check_enable_stock    = false;
        $only_variations       = false;
        if (empty($term)) {
            return json_encode([]);
        }
        $q = \App\Product::leftJoin('variations','products.id','=','variations.product_id')
                            ->where(function ($query) use ($term) {
                                $query->where('products.name', 'like', '%' . $term .'%');
                                $query->orWhere('sku', 'like', '%' . $term .'%');
                                $query->orWhere('sku2', 'like', '%' . $term .'%');
                                $query->orWhere('sub_sku', 'like', '%' . $term .'%');
                            })
                            ->active()
                            ->where('business_id', $business_id)
                            ->whereNull('variations.deleted_at')
                            ->select(
                                'products.id as product_id',
                                'products.unit_id as unit_id',
                                'products.sub_unit_ids as sub_unit',
                                'products.id as product_id',
                                'products.name',
                                'products.type',
                                'products.sku as sku',
                                'variations.id as variation_id',
                                'variations.name as variation',
                                'variations.sub_sku as sub_sku'
                            )
                            ->groupBy('variation_id');
        if ($check_enable_stock) {
            $q->where('enable_stock', 1);
        }
        if (!empty($location)) {
            $q->ForLocation($location->id);
        }
        $products       = $q->get();
      
        $products_array = [];
        foreach ($products as $product) {
         
            $list_unit = []; $un = [];
            if($product->sub_unit != null){
                $list_price      = \App\Product::getProductPrices($product->product_id);
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
                $products_array[$product->product_id]['all_units']              =  $list_unit ;
                
            }else{
                $unit                      = \App\Unit::find($product->unit_id);$list_price      = \App\Product::getProductPrices($product->product_id);
                $list_unit[]   = ["id" => $product->unit_id ,"value" => $unit->actual_name ,"unit_quantity"=>($unit->base_unit_multiplier == null)?1:$unit->base_unit_multiplier,"list_price"=> isset($list_price[$product->unit])?$list_price[$product->unit]:[]];
                $products_array[$product->product_id]['all_units']              =  $list_unit ;
            }
            $products_array[$product->product_id]['name']         = $product->name;
            $products_array[$product->product_id]['sku']          = $product->sub_sku;
            $products_array[$product->product_id]['type']         = $product->type;
            $products_array[$product->product_id]['variations'][] = [ 'variation_id'  => $product->variation_id, 'variation_name' => $product->variation,'sub_sku' => $product->sub_sku ];
        }
        
        $result  = []; $i = 1; $no_of_records = $products->count();
        if (!empty($products_array)) {
            foreach ($products_array as $key => $value) {
                $cost         = \App\Models\ItemMove::orderBy("date","desc")->orderBy("id","desc")->where("product_id",$key)->first();
                $stock_qty    = \App\Models\WarehouseInfo::where("product_id",$key)->sum("product_qty");
                if ($no_of_records > 1 && $value['type'] != 'single' && !$only_variations) {
                    $result[] = [   'id'           => $i,
                                    "open"         => "open",    
                                    'text'         => $value['name'] . ' - ' . $value['sku'],
                                    'variation_id' => 0,
                                    'product_id'   => $key,
                                    'type'         => $value['type'],
                                    'all_units'    => $value['all_units'],
                                    'cost'         => (!empty($cost))?$cost->unit_cost:0,
                                    'stock'        => $stock_qty
                            ];
                }
                $name = $value['name'];
                foreach ($value['variations'] as $variation) {
                    $text = $name;
                    if ($value['type'] == 'variable') {
                        $text = $text . ' (' . $variation['variation_name'] . ')';
                    }
                    $i++;
                    $result[] = [   'id'           => $i,
                                    "open"         => "open",
                                    "edit"         => null,
                                    'text'         => $text . ' - ' . $variation['sub_sku'],
                                    'product_id'   => $key ,
                                    'type'         => $value['type'] ,
                                    'all_units'    => $value['all_units'],
                                    'variation_id' => $variation['variation_id'],
                                    'cost'         => (!empty($cost))?$cost->unit_cost:0,
                                    'stock'        => $stock_qty

                                    ];
                }
                $i++;
            }
        }
        return json_encode($result);
    }
    // *** 
    // *** 2 SEARCH PRODUCT /OPEN PRODUCT
    public static function product($user,$data) {
        $business_id           = $user->business_id;
        $location              = \App\BusinessLocation::where("business_id",$business_id)->first();
        $term                  = $data["value"];
        $check_enable_stock    = false;
        $only_variations       = false;
        if (empty($term)) {
            return json_encode([]);
        }
        $q = \App\Product::leftJoin('variations','products.id','=','variations.product_id')
                            ->where(function ($query) use ($term) {
                                $query->where('products.name', 'like', '%' . $term .'%');
                                $query->orWhere('sku', 'like', '%' . $term .'%');
                                $query->orWhere('sku2', 'like', '%' . $term .'%');
                                $query->orWhere('sub_sku', 'like', '%' . $term .'%');
                            })
                            ->active()
                            ->where('business_id', $business_id)
                            ->whereNull('variations.deleted_at')
                            ->select(
                                'products.id as product_id',
                                'products.name',
                                'products.sub_unit_ids as sub_unit',
                                'products.product_description as product_description',
                                'products.unit_id as unit',
                                'products.type',
                                'products.sku as sku',
                                'variations.id as variation_id',
                                'variations.name as variation',
                                'variations.sub_sku as sub_sku',
                                'variations.default_purchase_price as default_purchase_price'
                            )
                            ->groupBy('variation_id');
        if ($check_enable_stock) {
            $q->where('enable_stock', 1);
        }
        if (!empty($location)) {
            $q->ForLocation($location->id);
        }
        $products       = $q->get();
      
        $products_array = [];
        foreach ($products as $product) {
            $list_unit = [];$un = [];
            if($product->sub_unit != null){
                $list_price      = \App\Product::getProductPrices($product->product_id);
                $unit2           = \App\Unit::find($product->unit);
                $list_unit[]     = ["id" => $unit2->id ,"value" => $unit2->actual_name ,"unit_quantity"=>($unit2->base_unit_multiplier == null)?1:$unit2->base_unit_multiplier,"list_price"=> isset($list_price[$unit2->id])?$list_price[$unit2->id]:[] ];
                $un []           = $unit2->id;
                $all = json_decode( $product->sub_unit );
                foreach($all as $i){
                    $unit            = \App\Unit::find($i);
                    if(!in_array($i,$un)){
                        $list_unit[]   = ["id" => $i ,"value" => $unit->actual_name ,"unit_quantity"=>($unit->base_unit_multiplier == null)?1:$unit->base_unit_multiplier,"list_price"=> isset($list_price[$i])?$list_price[$i]:[]];
                        $un [] =  $i;
                    }
                }
                $products_array[$product->product_id]['all_units']              =  $list_unit ;
                
            }else{
                $unit                      = \App\Unit::find($product->unit);$list_price      = \App\Product::getProductPrices($product->product_id);
                $list_unit[]   = ["id" => $product->unit ,"value" => $unit->actual_name ,"unit_quantity"=>($unit->base_unit_multiplier == null)?1:$unit->base_unit_multiplier ,"list_price"=> isset($list_price[$product->unit])?$list_price[$product->unit]:[] ];
                $products_array[$product->product_id]['all_units']              =  $list_unit ;
            }
            $products_array[$product->product_id]['unit']                       = $product->unit;
            $products_array[$product->product_id]['name']                       = $product->name;
            $products_array[$product->product_id]['sku']                        = $product->sub_sku;
            $products_array[$product->product_id]['description']                = $product->product_description;
            $products_array[$product->product_id]['type']                       = $product->type;
            $products_array[$product->product_id]['variations'][]               = [ 'variation_id'  => $product->variation_id, 'variation_name' => $product->variation,'sub_sku' => $product->sub_sku ,'description' => $product->product_description ,'unit' => $product->unit ,'default_purchase_price' => $product->default_purchase_price ];
            $products_array[$product->product_id]['default_purchase_price']     = $product->default_purchase_price;
        }
        $result  = []; $i = 1; $no_of_records = $products->count();
        if (!empty($products_array)) {
            foreach ($products_array as $key => $value) {
                $cost         = \App\Models\ItemMove::orderBy("date","desc")->orderBy("id","desc")->where("product_id",$key)->first();
                $stock_qty    = \App\Models\WarehouseInfo::where("product_id",$key)->sum("product_qty");
                if ($no_of_records > 1 && $value['type'] != 'single' && !$only_variations) {
                    $result[] = [   'id'             => $i,
                                    "open"           => "product",    
                                    'name'           => $value['name'] . ' - ' . $value['sku'],
                                    'description'    => ($value['description']!=null)?$value['description']:"",
                                    'variation_id'   => 0,
                                    'product_id'     => $key,
                                    'cost'           => (!empty($cost))?$cost->unit_cost:0,
                                    'stock'          => $stock_qty,
                                    'purchase_price' => $value['default_purchase_price'],
                                    'unit'           => $value['unit'],
                                    'all_unit'       => $value['all_units']

                            ];
                }
                $name = $value['name'];
                foreach ($value['variations'] as $variation) {
                    $text = $name;
                    if ($value['type'] == 'variable') {
                        $text = $text . ' (' . $variation['variation_name'] . ')';
                    }
                    $i++;
                    $result[] = [   'id'             => $i,
                                    "open"           => "product",
                                    "edit"           => null,
                                    'name'           => $text . ' - ' . $variation['sub_sku'],
                                    'description'    => ($variation['description']!=null)?$variation['description']:"",
                                    'product_id'     => $key ,
                                    'variation_id'   => $variation['variation_id'],
                                    'cost'           => (!empty($cost))?$cost->unit_cost:0,
                                    'stock'          => $stock_qty,
                                    'purchase_price' => $variation['default_purchase_price'],
                                    'unit'           => $variation['unit'],
                                    'all_unit'       => $value['all_units']

                                    ];
                }
                $i++;
            }
        }
        return json_encode($result);
    }
    // *** 

    
}
