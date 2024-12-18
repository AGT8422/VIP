<?php

namespace App\Models\FrontEnd\Utils;

use App\Product;
use App\Variation;
use App\PurchaseLine;
use App\ReferenceCount;
use App\MovementWarehouse;
use App\Models\ItemMove;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use App\Utils\Util;
use App\Models\WarehouseInfo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Media;
use App\Models\GournalVoucherItem;

class GlobalUtil extends Model
{
    use HasFactory;

    // ******* FOR COUNTER FUNCTION
        //*****1**  get the current count of model */
            public static function SetReferenceCount($type,$business_id,$pattern=null)
            {
                $Old_number = ReferenceCount::where('ref_type', $type)->where('business_id', $business_id)->where('pattern_id', $pattern)->first();
                if(!empty($Old_number)) {
                    $Old_number->ref_count += 1;
                    $Old_number->save();
                    $count = $Old_number->ref_count;
                }else{
                    $New_number = ReferenceCount::create([
                                    'ref_type'    => $type,
                                    'business_id' => $business_id,
                                    'pattern_id'  => $pattern,
                                    'ref_count'   => 1
                                ]);
                    $count = $New_number->ref_count;
                }
                return $count;
            }
        //*****2**  set the new count of model */
            public static function GenerateReferenceCount($type,$ref_count,$business_id=null,$default_prefix=null,$pattern=null)
            {
                $ref_digits =  str_pad($ref_count, 5, 0, STR_PAD_LEFT);
                if(!empty($default_prefix)) { $prefix = $default_prefix;  }
                if(!isset($prefix)){ $prefix = ""; }
                if (!in_array($type, ['contacts', 'business_location','username' ,'supplier','customer'])) {
                    $ref_year   = \Carbon::now()->year;
                    $ref_number = $prefix . $ref_year . '/' . $ref_digits;
                } else {
                    $ref_number = $prefix . $ref_digits;
                }
                return  $ref_number;
            }
    // ******END*

    // ******* FOR LIST DROPDOWN FUNCTION
        //*****1**  get the sub stores  */ 
            public static function stores() {
                $list   = [];
                $stores = \App\Models\Warehouse::where("status",1)->get();
                foreach($stores as $e){
                    $list[] = [
                    "id"   => $e->id,
                    "name" => $e->name
                    ];
                }
                return $list;
            }
        //*****2**  get the Business locations  */ 
            public static function businessLocation($business_id, $show_all = false, $receipt_printer_type_attribute = false, $append_id = true, $check_permission = true,$user=null){
                $query = \App\BusinessLocation::where('business_id', $business_id)->Active();
                if ($check_permission) {
                    $permitted_locations = ($user)?$user->permitted_locations():auth()->user()->permitted_locations();
                    if ($permitted_locations != 'all') {
                        $query->whereIn('id', $permitted_locations);
                    }
                }
                if ($append_id) {
                    $query->select(
                        \DB::raw("IF(location_id IS NULL OR location_id='', name, CONCAT(name, ' (', location_id, ')')) AS name"),
                        'id',
                        'receipt_printer_type',
                        'selling_price_group_id',
                        'default_payment_accounts'
                    );
                }
                $result        = $query->get();
                $locations     = $result->pluck('name', 'id');
                $price_groups  = GlobalUtil::sellingPriceGroups($business_id,true,$user);

                if ($show_all) {
                    $locations->prepend(__('report.all_locations'), '');
                }
                if ($receipt_printer_type_attribute) {
                    $attributes = collect($result)->mapWithKeys(function ($item) use ($price_groups) {
                        $default_payment_accounts = json_decode($item->default_payment_accounts, true);
                        $default_payment_accounts['advance'] = [
                            'is_enabled' => 1,
                            'account' => null
                        ];
                        return [$item->id => [
                                    'data-receipt_printer_type' => $item->receipt_printer_type,
                                    'data-default_price_group' => !empty($item->selling_price_group_id) && array_key_exists($item->selling_price_group_id, $price_groups) ? $item->selling_price_group_id : null,
                                    'data-default_payment_accounts' => json_encode($default_payment_accounts)
                                ]
                            ];
                    })->all();

                    return ['locations' => $locations, 'attributes' => $attributes];
                } else {
                    return $locations;
                }
            }
        //*****3**  get the selling price groups   */ 
            public static function sellingPriceGroups($business_id, $with_default = true,$user=null)
            {
                $price_groups = \App\SellingPriceGroup::where('business_id', $business_id)
                                            ->active()
                                            ->get();

                $dropdown = [];

                if ($with_default && ( ($user)?$user->can('access_default_selling_price'):auth()->user()->can('access_default_selling_price'))) {
                    $dropdown[0] = __('lang_v1.default_selling_price');
                }
                
                foreach ($price_groups as $price_group) {
                    if ((($user)?$user->can('selling_price_group.' . $price_group->id):auth()->user()->can('selling_price_group.' . $price_group->id))) {
                        $dropdown[$price_group->id] = $price_group->name;
                    }
                }
                return $dropdown;
            }
    // ******END*

    // ******* Built-In FUNCTION
        //*****0** Array => Object  */ 
            public static function arrayToObject($array) {
                $listObject       = [];
                foreach($array as $key => $value){
                    $listObject[] = [
                        "id"      => $key,
                        "value"   => $value
                    ];
                }
                return $listObject;
            }
        //*****1** Check if there is relations */
            public static function check($type,$id) {
                $check = false;
                if($type == "product"){
                    $check = GlobalUtil::checkProduct($id);
                }
                if($type == "store"){
                    $check = GlobalUtil::checkStore($id);
                }
                if($type == "contact"){
                    $check = GlobalUtil::checkContact($id);
                }
                if($type == "purchase"){
                    $check = GlobalUtil::checkPurchase($id);
                }

            return $check;
            }
        //*****2** Check if there is relations */
            public static function checkStore($id) {
                $checkParent     = \App\Models\Warehouse::where("parent_id",$id)->first();
                $checkItemMove   = \App\Models\ItemMove::where("store_id",$id)->first();
                $checkWareHouse  = \App\MovementWarehouse::where("store_id",$id)->first();
                $checkTransaction= \App\Transaction::where("store",$id)->first();
                $checkPurchase   = \App\PurchaseLine::where("store_id",$id)->first();
                $checkSale       = \App\TransactionSellLine::where("store_id",$id)->first();
                // $checkOpen       = \App\Models\OpeningQuantity::where("store_id",$id)->first();
                // $checkRecipe     = \Modules\Manufacturing\Entities\MfgRecipe::where("store_id",$id)->first();
                // $variations      = \App\Variation::where("product_id",$id)->pluck("id")->toArray();
                // $checkIngredient = \Modules\Manufacturing\Entities\MfgRecipeIngredient::whereIn("variation_id",$variations)->first();
                if( $checkItemMove   != null || $checkWareHouse    != null 
                ||  $checkPurchase   != null || $checkSale         != null 
                || $checkTransaction != null || $checkParent       != null ) {
                    return true;
                }else {
                    return false;
                }
            }
        //*****3** Check if there is relations */
            public static function checkProduct($id) {
                $checkItemMove   = \App\Models\ItemMove::where("product_id",$id)->first();
                $checkWareHouse  = \App\MovementWarehouse::where("product_id",$id)->first();
                $checkPurchase   = \App\PurchaseLine::where("product_id",$id)->first();
                $checkSale       = \App\TransactionSellLine::where("product_id",$id)->first();
                $checkOpen       = \App\Models\OpeningQuantity::where("product_id",$id)->first();
                $checkRecipe     = \Modules\Manufacturing\Entities\MfgRecipe::where("product_id",$id)->first();
                $variations      = \App\Variation::where("product_id",$id)->pluck("id")->toArray();
                $checkIngredient = \Modules\Manufacturing\Entities\MfgRecipeIngredient::whereIn("variation_id",$variations)->first();
                if( $checkItemMove   != null || $checkWareHouse != null 
                ||  $checkPurchase   != null || $checkSale      != null 
                ||  $checkOpen       != null || $checkRecipe    != null
                ||  $checkIngredient != null) {
                    return true;
                }else {
                    return false;
                }
            }
        //*****4** Check if there is relations */
            public static function checkAccount($id) {
                $check          = false;
                $checkACCOUNT   = \App\AccountTransaction::where("account_id",$id)->whereNull("deleted_at")->first();
                if(!empty($checkACCOUNT)){$check = true;}
                return $check;
            }
        //*****5** Product Price */
            public static function createSingleProductVariationPrices($product, $sku, $list_purchase_price, $list_dpp_inc_tax, $list_profit_percent, $list_selling_price, $list_selling_price_inc_tax, $combo_variations = [],$type=null,$list_unit)
            {
                if (!is_object($product)) {
                    $product = \App\Product::find($product);
                }
                //create product variations
                $product_variation_data = [
                    'name'     => 'DUMMY',
                    'is_dummy' => 1
                ];
                $product_variation = $product->product_variations()->create($product_variation_data);
                //create variations
                $variation_data    = [
                    'name'                      => 'DUMMY',
                    'product_id'                => $product->id,
                    'sub_sku'                   => $sku,
                    'default_purchase_price'    => $list_purchase_price,
                    'dpp_inc_tax'               => $list_dpp_inc_tax,
                    'profit_percent'            => $list_profit_percent,
                    'default_sell_price'        => $list_selling_price,
                    'sell_price_inc_tax'        => $list_selling_price_inc_tax,
                    'combo_variations'          => $combo_variations
                ];
                $variation = $product_variation->variations()->create($variation_data);
                return true;
            }
        //*****6** Variation price  */ 
            public static function calculateVariationPrices($dpp_exc_tax, $dpp_inc_tax, $selling_price, $tax_amount, $tax_type, $margin,$productUtil)
            {
                
                //Calculate purchase prices
                if ($dpp_inc_tax == 0) {
                    $dpp_inc_tax = $productUtil->calc_percentage(
                        $dpp_exc_tax,
                        $tax_amount,
                        $dpp_exc_tax
                    );
                }
        
                if ($dpp_exc_tax == 0) {
                    $dpp_exc_tax = $productUtil->calc_percentage_base($dpp_inc_tax, $tax_amount);
                }
        
        
                if ($selling_price != 0) {
                    if ($tax_type == 'inclusive') {
                        $dsp_inc_tax = $selling_price;
                        $dsp_exc_tax = $productUtil->calc_percentage_base(
                            $dsp_inc_tax,
                            $tax_amount
                        );
                    } elseif ($tax_type == 'exclusive') {
                        $dsp_exc_tax = $selling_price;
                        $dsp_inc_tax = $productUtil->calc_percentage(
                            $selling_price,
                            $tax_amount,
                            $selling_price
                        );
                    }
                } else {
                    $dsp_exc_tax = $productUtil->calc_percentage(
                        $dpp_exc_tax,
                        $margin,
                        $dpp_exc_tax
                    );
                    $dsp_inc_tax = $productUtil->calc_percentage(
                        $dsp_exc_tax,
                        $tax_amount,
                        $dsp_exc_tax
                    );
                }
        
                return [
                    'dpp_exc_tax' => $productUtil->num_f($dpp_exc_tax),
                    'dpp_inc_tax' => $productUtil->num_f($dpp_inc_tax),
                    'dsp_exc_tax' => $productUtil->num_f($dsp_exc_tax),
                    'dsp_inc_tax' => $productUtil->num_f($dsp_inc_tax)
                ];
            }
        //*****7** Rack Details  */ 
            public static function rackDetails($rack_value, $row_value, $position_value, $business_id, $product_id, $row_no,$productUtil)
            {
                if (!empty($rack_value) || !empty($row_value) || !empty($position_value)) {
                    $locations = \App\BusinessLocation::forDropdown($business_id);
                    $loc_count = count($locations);
        
                    $racks = explode('|', $rack_value);
                    $rows  = explode('|', $row_value);
                    $position = explode('|', $position_value);
        
                    if (count($racks) > $loc_count) {
                        $error_msg = "Invalid value for RACK in row no. $row_no";
                        throw new \Exception($error_msg);
                    }
        
                    if (count($rows) > $loc_count) {
                        $error_msg = "Invalid value for ROW in row no. $row_no";
                        throw new \Exception($error_msg);
                    }
        
                    if (count($position) > $loc_count) {
                        $error_msg = "Invalid value for POSITION in row no. $row_no";
                        throw new \Exception($error_msg);
                    }
        
                    $rack_details = [];
                    $counter = 0;
                    foreach ($locations as $key => $value) {
                        $rack_details[$key]['rack'] = isset($racks[$counter]) ? $racks[$counter] : '';
                        $rack_details[$key]['row'] = isset($rows[$counter]) ? $rows[$counter] : '';
                        $rack_details[$key]['position'] = isset($position[$counter]) ? $position[$counter] : '';
                        $counter += 1;
                    }
        
                    if (!empty($rack_details)) {
                        $productUtil->addRackDetails($business_id, $product_id, $rack_details);
                    }
                }
            }
        //*****8** Opening Stock  */ 
            public static function addOpeningStock($opening_stock, $product, $business_id,$user_id,$productUtil)
            {
                
                $variation        = \App\Variation::where('product_id', $product->id)->first();
        
                $total_before_tax = $opening_stock['quantity'] * $variation->dpp_inc_tax;
        
                $transaction_date = request()->session()->get("financial_year.start");
                $transaction_date = \Carbon::createFromFormat('Y-m-d', $transaction_date)->toDateTimeString();
                //Add opening stock transaction
                $transaction      = \App\Transaction::create(
                    [
                                        'type' => 'opening_stock',
                                        'opening_stock_product_id' => $product->id,
                                        'status' => 'received',
                                        'business_id' => $business_id,
                                        'transaction_date' => $transaction_date,
                                        'total_before_tax' => $total_before_tax,
                                        'location_id' => $opening_stock['location_id'],
                                        'final_total' => $total_before_tax,
                                        'payment_status' => 'paid',
                                        'created_by' => $user_id
                                    ]
                );
                //Get product tax
                $tax_percent = !empty($product->product_tax->amount) ? $product->product_tax->amount : 0;
                $tax_id      = !empty($product->product_tax->id) ? $product->product_tax->id : null;
        
                $item_tax    = $productUtil->calc_percentage($variation->default_purchase_price, $tax_percent);
        
                //Create purchase line
                $transaction->purchase_lines()->create([
                                'product_id' => $product->id,
                                'variation_id' => $variation->id,
                                'quantity' => $opening_stock['quantity'],
                                'item_tax' => $item_tax,
                                'tax_id' => $tax_id,
                                'pp_without_discount' => $variation->default_purchase_price,
                                'purchase_price' => $variation->default_purchase_price,
                                'purchase_price_inc_tax' => $variation->dpp_inc_tax,
                                'exp_date' => !empty($opening_stock['exp_date']) ? $opening_stock['exp_date'] : null
                            ]);
                //Update variation location details
                $productUtil->updateProductQuantity($opening_stock['location_id'], $product->id, $variation->id, $opening_stock['quantity']);
        
                //Add product location
                GlobalUtil::__addProductLocation($product, $opening_stock['location_id']);
                
            }
        //*****9** Product Location  */ 
            public static function __addProductLocation($product, $location_id)
            {
                $count = \DB::table('product_locations')->where('product_id', $product->id)
                                                    ->where('location_id', $location_id)
                                                    ->count();
                if ($count == 0) {
                    \DB::table('product_locations')->insert(['product_id' => $product->id, 
                                        'location_id' => $location_id]);
                }
            }
        //*****10** Opening Stock For Variable */ 
            public static function addOpeningStockForVariable($variations, $product, $business_id,$user_id,$productUtil)
            {
                
        
                $transaction_date = request()->session()->get("financial_year.start");
                $transaction_date = \Carbon::createFromFormat('Y-m-d', $transaction_date)->toDateTimeString();
        
                $total_before_tax = 0;
                $location_id      = $variations['opening_stock_location'];
                if (isset($variations['variations'][0]['opening_stock'])) {
                    //Add opening stock transaction
                    $transaction = \App\Transaction::create(
                        [
                                        'type' => 'opening_stock',
                                        'opening_stock_product_id' => $product->id,
                                        'status' => 'received',
                                        'business_id' => $business_id,
                                        'transaction_date' => $transaction_date,
                                        'total_before_tax' => $total_before_tax,
                                        'location_id' => $location_id,
                                        'final_total' => $total_before_tax,
                                        'payment_status' => 'paid',
                                        'created_by' => $user_id
                                    ]
                    );
        
                    //Add product location
                    GlobalUtil::__addProductLocation($product, $location_id);
        
                    foreach ($variations['variations'] as $variation_os) {
                        if (!empty($variation_os['opening_stock'])) {
                            $variation = \App\Variation::where('product_id', $product->id)
                                            ->where('name', $variation_os['value'])
                                            ->first();
                            if (!empty($variation)) {
                                $opening_stock = [
                                    'quantity' => $variation_os['opening_stock'],
                                    'exp_date' => $variation_os['opening_stock_exp_date'],
                                ];
        
                                $total_before_tax = $total_before_tax + ($variation_os['opening_stock'] * $variation->dpp_inc_tax);
                            }
        
                            //Get product tax
                            $tax_percent = !empty($product->product_tax->amount) ? $product->product_tax->amount : 0;
                            $tax_id      = !empty($product->product_tax->id) ? $product->product_tax->id : null;
        
                            $item_tax    = $productUtil->calc_percentage($variation->default_purchase_price, $tax_percent);
        
                            //Create purchase line
                            $transaction->purchase_lines()->create([
                                            'product_id'             => $product->id,
                                            'variation_id'           => $variation->id,
                                            'quantity'               => $opening_stock['quantity'],
                                            'item_tax'               => $item_tax,
                                            'tax_id'                 => $tax_id,
                                            'purchase_price'         => $variation->default_purchase_price,
                                            'purchase_price_inc_tax' => $variation->dpp_inc_tax,
                                            'exp_date'               => !empty($opening_stock['exp_date']) ? $opening_stock['exp_date'] : null
                                        ]);
                            //Update variation location details
                            $productUtil->updateProductQuantity($location_id, $product->id, $variation->id, $opening_stock['quantity']);
                        }
                    }
        
                    $transaction->total_before_tax = $total_before_tax;
                    $transaction->final_total      = $total_before_tax;
                    $transaction->save();
                }
            }
        //*****11** Array => Object  */ 
            public static function arrayToObjectGroupBy($array) {
                $lOb                   = [];
                $listObject            = [];
                $line                  = [];
                $temp                  = [];
                foreach($array as $key => $value){
                    foreach($value as $k => $vl){
                        $temp[$k][] =$vl; 
                    } 
                }
                return GlobalUtil::arrayToObject($temp);
            }
        //*****12** Tree Items  */ 
            public static function toTree($class,$business_id) {
                if($class == "category"){
                    $list        = [];
                    $list_a      = [];
                    $list_parent = [];
                    $main_data = \App\Category::where("business_id",$business_id)->get();
                    foreach($main_data as $child){
                        $list[$child->id][]        =  $child ;
                        $check                     =  $child->parent_id;
                        if($check == 0 || $check == null){$list_parent[] = $child->id;}
                        while($check != 0 && $check != null){
                            $category            = \App\Category::find($check);
                            $list[$child->id][]  =  $category ;
                            $check               =  $category->parent_id;
                        }   
                    }
                    foreach($list as $key => $tree_list){
                        $array  = array_reverse($tree_list);
                        foreach($array as $e){
                            if($e["parent_id"] != 0){
                                if(!in_array($e,$list[$e["parent_id"]])){
                                    array_push($list[$e["parent_id"]],$e); 
                                }
                            } 
                        }
                    }
                    foreach($list as $key => $tree_parent){ 
                        $ol =  [];
                        foreach($tree_parent as $k => $ele){
                            if($ele->id == $key){
                                unset($tree_parent[$k]);
                            }else{
                                $ol[] = $tree_parent[$k];
                            }
                        }
                        $list[$key] = $ol;
                    }
                    foreach($list as $key => $td_list){
                        $array  = array_reverse($td_list);
                        foreach($array as $e){
                            if($e["parent_id"] == 0){
                                unset($list[$key]);
                            } 
                        }
                    }
                    foreach($list as $key => $tr_list){
                        if(empty($tr_list)){
                            // unset($list[$key]);
                        }
                    }
                    // ..***...................For Table.....
                    $lo_last = [];
                    foreach($list as $key => $ts_list){
                        $category  = \App\Category::find($key); 
                        $lo_last[] = $category;
                        foreach($ts_list as $element){
                            $lo_last[] = $element;
                        }
                    }
                    // ..***................................
                    $type_o = null;
                }elseif($class == "account_type"){
                    // $list        = [];
                    // $list_a      = [];
                    // $list_parent = [];
                    // $main_data = \App\AccountType::where("business_id",$business_id)->get();
                    // foreach($main_data as $child){
                    //     $list[$child->id][]        =  $child ;
                    //     $check                     =  $child->parent_account_type_id;
                    //     if($check == 0 || $check == null){$list_parent[] = $child->id;}
                    //     while($check != 0 && $check != null){
                    //         $category            = \App\AccountType::find($check);
                    //         $list[$child->id][]  =  $category ;
                    //         $check               =  $category->parent_account_type_id;
                    //     }   
                    // }
                    // foreach($list as $key => $tree_list){
                    //     $array  = array_reverse($tree_list);
                    //     foreach($array as $e){
                    //         if($e["parent_account_type_id"] != null){
                    //             if(!in_array($e,$list[$e["parent_account_type_id"]])){
                    //                 array_push($list[$e["parent_account_type_id"]],$e); 
                    //             }
                    //         } 
                    //     }
                    // }
                    // foreach($list as $key => $tree_parent){ 
                    //     $ol =  [];
                    //     foreach($tree_parent as $k => $ele){
                    //         if($ele->id == $key){
                    //             unset($tree_parent[$k]);
                    //         }else{
                    //             $ol[] = $tree_parent[$k];
                    //         }
                    //     }
                    //     $list[$key] = $ol;
                    // }
                    // foreach($list as $key => $td_list){
                    //     $array  = array_reverse($td_list);
                    //     foreach($array as $e){
                    //         if($e["parent_account_type_id"] == null){
                    //             unset($list[$key]);
                    //         } 
                    //     }
                    // }
                    // foreach($list as $key => $tr_list){
                    //     if(empty($tr_list)){
                    //         // unset($list[$key]);
                    //     }
                    // }
                    // // ..***...................For Table.....
                    // $lo_last = [];
                    // foreach($list as $key => $ts_list){
                    //     $category  = \App\AccountType::find($key); 
                    //     $lo_last[] = $category;
                    //     foreach($ts_list as $element){
                    //         $lo_last[] = $element;
                    //     }
                    // }
                    $accountType = \App\AccountType::where("business_id",$business_id)->orderBy("parent_account_type_id","asc")->get();
                    $list        = \App\AccountType::nestable($accountType,[]);
                    $list        = json_decode(json_encode($list));
                    
                    // ..***................................
                    $type_o = "account_type";
                } 
                if($type_o == "account_type"){
                    return GlobalUtil::toTreeItemsAccount($list,$type_o) ;
                }else{
                    return GlobalUtil::toTreeItems($list,$type_o) ;
                }
            }
        //*****13** Tree Items  Table */ 
            public static function toTreeTable($class,$business_id) {
                if($class == "category"){
                    $list        = [];
                    $list_a      = [];
                    $list_parent = [];
                    $main_data   = \App\Category::join("users as us" ,"us.id","categories.created_by")->where("categories.business_id",$business_id)
                                                            ->select([
                                                                "categories.id as id",
                                                                "categories.name as name",
                                                                "categories.short_code as short_code",
                                                                "categories.parent_id as parent_id",
                                                                "categories.description as description",
                                                                "categories.category_type as category_type",
                                                                "categories.image",
                                                                "us.first_name as created_by",
                                                            ])
                                                            ->get();
                    foreach($main_data as $child){
                        $list[$child->id][]        =  $child ;
                        $check                     =  $child->parent_id;
                        if($check == 0 || $check == null){$list_parent[] = $child->id;}
                        while($check != 0 && $check != null){
                            $category            = \App\Category::join("users as us" ,"us.id","categories.created_by")->where("categories.id",$check)
                                                                ->select([
                                                                    "categories.id as id",
                                                                    "categories.name as name",
                                                                    "categories.short_code as short_code",
                                                                    "categories.parent_id as parent_id",
                                                                    "categories.category_type as category_type",
                                                                    "categories.image",
                                                                    "us.first_name as created_by",
                                                                ])
                                                                ->first();
                            $list[$child->id][]  =  $category ;
                            $check               =  $category->parent_id;
                        }   
                    }
                    foreach($list as $key => $tree_list){
                        $array  = array_reverse($tree_list);
                        foreach($array as $e){
                            if($e["parent_id"] != 0){
                                if(!in_array($e,$list[$e["parent_id"]])){
                                    array_push($list[$e["parent_id"]],$e); 
                                }
                            } 
                        }
                    }
                    foreach($list as $key => $tree_parent){ 
                        $ol =  [];
                        foreach($tree_parent as $k => $ele){
                            if($ele->id == $key){
                                unset($tree_parent[$k]);
                            }else{
                                $ol[] = $tree_parent[$k];
                            }
                        }
                        $list[$key] = $ol;
                    }
                    foreach($list as $key => $td_list){
                        $array  = array_reverse($td_list);
                        foreach($array as $e){
                            if($e["parent_id"] == 0){
                                unset($list[$key]);
                            } 
                        }
                    }
                    foreach($list as $key => $tr_list){
                        if(empty($tr_list)){
                            // unset($list[$key]);
                        }
                    }
                    // ..***...................For Table.....
                    $lo_last = [];
                    foreach($list as $key => $ts_list){
                        $category  = \App\Category::join("users as us" ,"us.id","categories.created_by")
                                                        ->where("categories.id",$key)
                                                        ->select([
                                                            "categories.id as id",
                                                            "categories.name as name",
                                                            "categories.short_code as short_code",
                                                            "categories.parent_id as parent_id",
                                                            "categories.description as description",
                                                            "categories.category_type as category_type",
                                                            "categories.image",
                                                            "us.first_name as created_by",
                                                        ])
                                                        ->first(); 
                        $lo_last[] = $category;
                        foreach($ts_list as $element){
                            $lo_last[] = $element;
                        }
                    }
                    // ..***................................

                }elseif($class == "account_type"){
                    $list        = [] ;
                    $accountType = \App\AccountType::where("business_id",$business_id)->orderBy("parent_account_type_id","asc")->get();
                    $lo_last     = \App\AccountType::nestable($accountType,[]);
                }  
                // ..***................................
            
                return $lo_last;
            }
        //*****14** Check if there is relations */
            public static function checkPurchase($id) {
                $transactionUtil = new TransactionUtil();
                $check           = true;
                if ($transactionUtil->isReturnExist($id)) {
                    return true;
                }
                return $check;
            }
        //*****15** Check if there is relations */
            public static function lastContact($business_id, $type) {
                $check           = true;
                $last = \App\Contact::where('type',$type)->orderBy('id',"desc")->first();
                if(empty($last)){
                    $last           = false;
                }else{
                    $last = [
                        "id"               => $last->id,
                        "name"             => $last->name . '  ' . $last->middle_name . '  ' . $last->last_name,
                        "businessName"     => $last->first_name,
                        "contactNumber"    => $last->contact_id,
                        "tax_number"       => $last->tax_number,
                        "mobile"           => $last->mobile,
                        "balance"          => $last->balance,
                        "email"            => $last->email,
                    ];
                }
                return $last;
            }
        //*****16** Check if there is relations */
            public static function checkContactName($business_id, $name) {
                $check          = true;
                $contact        = \App\Contact::where('first_name',trim($name))->get();
                if(count($contact)>0){ $check = false; }
                return $check;
            }
        // ****************************
            public function MainCount($id){
                $list   = [];
                $accountType = \App\AccountType::where("parent_account_type_id",$id)->get();
                if(count($accountType)>0){
                    foreach($accountType as $items){
                        $list[] = $items->id;
                    }
                } 
                return $list;
            }
            public function SubCount($id){
                $list   = [];
                $accountType = \App\AccountType::where("parent_account_type_id",$id)->get();
                if(count($accountType)>0){
                    foreach($accountType as $items){
                        $list[] = $items->id;
                    }
                } 
                return $list;
            }
        // **************************
        //*****14** To Tree Items  */ 
            public static function toTreeItems($array,$type_o=null) {
                $listA  = [];
                $line   = [];
                $listed = [];
                foreach ($array as $key => $value) {
                    if(!in_array($key,$listed)){
                        if($type_o != null){
                            if($type_o == "account_type"){
                                $category         = \App\AccountType::find($key);
                            }else{
                                $category         = \App\Category::find($key);
                            }
                        }else{
                            $category         = \App\Category::find($key);
                        }
                        $line["id"]       = $key;
                        $line["label"]    = $category->name ;
                        $line["children"] = [];
                        array_push($listed,$key);
                        if(count($value) > 0){
                            foreach($value as $i){
                                $list_b = [];
                                if(!in_array($i->id,$listed)){
                                    $list_b["id"]         = $i->id;
                                    $list_b["label"]      = $i->name ;
                                    $line["children"][]   = $list_b;
                                    array_push($listed,$i->id);
                                }
                            }
                        }
                        if(count( $line["children"] ) == 0){unset($line["children"]);}
                    }
                    $listA[] = $line;
                }
                return $listA ;
            }
        //*****15** To Tree Items  */ 
            public static function toTreeItemsAccount($array,$type_o=null) {
                $listA  = [];    $line   = [];   $listed = [];
                $array_ids = [] ;
                foreach ($array as $key => $value) {
                    if(!in_array($value->id,$array_ids)){
                        $array_ids[]      = $value->id;
                        $line["id"]       = $value->id;
                        $line["label"]    = $value->name ;
                        $line["children"] = [];
                        if(count($value->sub_types) > 0){
                            $lines             = GlobalUtil::children($value->sub_types,[],$array_ids) ;
                            $line["children"]  = $lines["line"];
                            $array_ids         = $lines["array"];
                        }
                        if(count( $line["children"] ) == 0){unset($line["children"]);}
                        $listA[] = $line;

                    }
                    
                }
                
                return $listA ;
            }
        //*****16** To Tree Items  */ 
            public static function children($sub_types,$line,$array_ids){
                $line_object    = [];
                $final["line"]  = $line;
                $final["array"] = $array_ids;
                foreach($sub_types as $k => $i){
                    if(!in_array($i->id,$array_ids)){
                        $array_ids[]               = $i->id;
                        $line_object[$k]["id"]     = $i->id;
                        $line_object[$k]["label"]  = $i->name;
                        if(count($i->sub_types)>0){
                            $line_obj                     = self::children($i->sub_types,$line,$array_ids);
                            $line_object[$k]["children"]  =  $line_obj["line"];
                            $array_ids                    =  $line_obj["array"];
                        }
                        $line           = $line_object;
                        $final["line"]  = $line;
                        foreach($array_ids as $o){
                            if(!in_array($o,$final["array"])){
                                $final["array"][] = $o;
                            }
                        }
                    }
                }
                return $final;
            }
        //*****17** To Check Pattern  */ 
            public static function checkPattern($id) {
                $check = 0;
                $transaction  = \App\Transaction::where("pattern_id",$id)->first(); 
                if(!empty($transaction)){$check = 1;}
                $user         = \App\User::where("user_pattern_id",$id)->first();
                if(!empty($user)){$check = 1;}
                return $check;
            }
        //*****18** Product-Price
            public static function createVariableProductVariations($product, $input_variations, $business_id = null,$lengthOfTables=null)
            {
                $productUtil = new ProductUtil();
                if (!is_object($product)) {
                    $product = \App\Product::find($product);
                }
                
                
                //create product variations
                foreach (json_decode($input_variations) as $key => $value) {
                    $images                  = [];
                    
                    $variation_template_name = !empty($value->name) ? $value->name : null;
                    $variation_template_id   = !empty($value->variation_template_id) ? $value->variation_template_id : null;
                    if (empty($variation_template_id)) {
                        if ($variation_template_name != 'DUMMY') {  
                            $variation_template = \App\VariationTemplate::where('business_id', $business_id)
                                                                ->whereRaw('LOWER(name)="' . strtolower($variation_template_name) . '"')
                    
                                                                ->with(['values'])
                                                                ->first();
                            if (empty($variation_template)) {
                                $variation_template = \App\VariationTemplate::create([
                                    'name'          => $variation_template_name,
                                    'business_id'   => $business_id
                                ]);
                            }
                            $variation_template_id = $variation_template->id;
                        }
                    } else {
                        $variation_template      = \App\VariationTemplate::with(['values'])->find($value->variation_template_id);
                        $variation_template_id   = $variation_template->id;
                        $variation_template_name = $variation_template->name;
                    }
        
                    $product_variation_data = [
                                            'name'                  => $variation_template_name,
                                            'product_id'            => $product->id,
                                            'is_dummy'              => 0,
                                            'variation_template_id' => $variation_template_id
                                        ];
                    $product_variation = \App\ProductVariation::create($product_variation_data);
                    
                    //create variations
                    if (!empty($value->variations)) {
                        $variation_data = [];
        
                        $c = \App\Variation::withTrashed()
                                ->where('product_id', $product->id)
                                ->count() + 1;
                            
                        foreach ($value->variations as $k => $v) {
                            $sub_sku              = empty($v->sub_sku)? GlobalUtil::generateSubSku($product->sku, $c, $product->barcode_type) :$v->sub_sku;
                        
                            $variation_value_id   = !empty($v->variation_value_id) ? $v->variation_value_id : null;
                            $variation_value_name = !empty($v->value) ? $v->value : null;
        
                            if (!empty($variation_value_id)) {
                                $variation_value = $variation_template->values->filter(function ($item) use ($variation_value_id) {
                                    return $item->id == $variation_value_id;
                                })->first();
                                $variation_value_name = $variation_value->name;
                            } else {
                                if (!empty($variation_template)) {
                                    $variation_value =  \App\VariationValueTemplate::where('variation_template_id', $variation_template->id)
                                        ->whereRaw('LOWER(name)="' . $variation_value_name . '"')
                                        ->first();
                                    if (empty($variation_value)) {
                                        $variation_value =  \App\VariationValueTemplate::create([
                                            'name' => $variation_value_name,
                                            'variation_template_id' => $variation_template->id
                                        ]);
                                    }
                                    $variation_value_id = $variation_value->id;
                                    $variation_value_name = $variation_value->name;
                                } else {
                                    $variation_value_id = null;
                                    $variation_value_name = $variation_value_name;
                                }
                            }
                            $variation_data[] = [
                            'name'                   => $variation_value_name,
                            'variation_value_id'     => $variation_value_id,
                            'product_id'             => $product->id,
                            'sub_sku'                => $sub_sku,
                            'default_purchase_price' => $productUtil->num_uf($v->default_purchase_price),
                            'dpp_inc_tax'            => $productUtil->num_uf($v->dpp_inc_tax),
                            'profit_percent'         => $productUtil->num_uf($v->profit_percent),
                            'default_sell_price'     => $productUtil->num_uf($v->default_sell_price),
                            'sell_price_inc_tax'     => $productUtil->num_uf($v->sell_price_inc_tax)
                            ];
                            
                            $c++;
                            if($lengthOfTables == null){
                                
                                $images[] = 'variation_images_' . $key . '_' . $k;
                            }else{
                                
                                $images[] = 'variation_images_' . $lengthOfTables  . '_' . $k;
                            }
                        }
                        $variations = $product_variation->variations()->createMany($variation_data);
                        $i = 0;
                        
                    
                        foreach ($variations as $variation) {
                        
                            \App\Media::uploadMedia($product->business_id, $variation, request(), $images[$i]);
                            $i++;
                        }
                            
                    }
                    $lengthOfTables++;
                }
            } 
        //*****19** Price One In Product
            public static function oneRowPrice($product,$user,$data1) {
                
                $unit              =  $data1->unit_id;
                if($data1->value  == "default_price")    {$val = "Default Price"    ; $k = 0 ;}
                if($data1->value  == "whole_price")      {$val = "Whole Price"      ; $k = 1 ;}
                if($data1->value  == "retail_price")     {$val = "Retail Price"     ; $k = 2 ;}
                if($data1->value  == "minimum_price")    {$val = "Minimum Price"    ; $k = 3 ;}
                if($data1->value  == "last_price")       {$val = "Last Price"       ; $k = 4 ;}
                if($data1->value  == "ecm_before_price") {$val = "ECM Before Price" ; $k = 5 ;}
                if($data1->value  == "ecm_after_price")  {$val = "ECM After Price"  ; $k = 6 ;}
                if($data1->value  == "custom_price_1")   {$val = "Custom Price 1"   ; $k = 7 ;}
                if($data1->value  == "custom_price_2")   {$val = "Custom Price 2"   ; $k = 8 ;}
                if($data1->value  == "custom_price_3")   {$val = "Custom Price 3"   ; $k = 9 ;}

                $product_id_price                         =  new \App\Models\ProductPrice();
                $product_id_price->product_id             =  $product->id ;   
                $product_id_price->business_id            =  $user->business_id ;   
                $product_id_price->name                   =  $val ;   
                $product_id_price->default_purchase_price =  $data1->single_dpp ;   
                $product_id_price->dpp_inc_tax            =  $data1->single_dpp_inc_tax ;   
                $product_id_price->profit_percent         =  $data1->profit_percent ;   
                $product_id_price->default_sell_price     =  $data1->single_dsp ;   
                $product_id_price->sell_price_inc_tax     =  $data1->single_dsp_inc_tax ;   
                $product_id_price->number_of_default      =  $k ;     
                $product_id_price->unit_id                =  $unit ;
                $product_id_price->save();
                        
            }
        //*****20** Price One In Product
            public static function updateOneRowPrice($product,$user,$data1) {
                
                $unit              =  $data1->unit_id;
                if($data1->value == "Default Price")    {$val = "Default Price"    ; $k = 0 ;}
                if($data1->value == "Whole Price")      {$val = "Whole Price"      ; $k = 1 ;}
                if($data1->value == "Retail Price")     {$val = "Retail Price"     ; $k = 2 ;}
                if($data1->value == "Minimum Price")    {$val = "Minimum Price"    ; $k = 3 ;}
                if($data1->value == "Last Price")       {$val = "Last Price"       ; $k = 4 ;}
                if($data1->value == "ECM Before Price") {$val = "ECM Before Price" ; $k = 5 ;}
                if($data1->value == "ECM After Price")  {$val = "ECM After Price"  ; $k = 6 ;}
                if($data1->value == "Custom Price 1")   {$val = "Custom Price 1"   ; $k = 7 ;}
                if($data1->value == "Custom Price 2")   {$val = "Custom Price 2"   ; $k = 8 ;}
                if($data1->value == "Custom Price 3")   {$val = "Custom Price 3"   ; $k = 9 ;}

                $product_id_price                         =  \App\Models\ProductPrice::where("product_id",$product->id)->where("name",$val)->where("unit_id",$unit)->first();
                // dd($data1);
                // dd($product_id_price);
                // dd($product_id_price);
                if(!empty($product_id_price)){
                    $product_id_price->default_purchase_price =  $data1->single_dpp ;   
                    $product_id_price->dpp_inc_tax            =  $data1->single_dpp_inc_tax ;   
                    $product_id_price->profit_percent         =  $data1->profit_percent ;   
                    $product_id_price->default_sell_price     =  $data1->single_dsp ;   
                    $product_id_price->sell_price_inc_tax     =  $data1->single_dsp_inc_tax ;   
                    $product_id_price->update();
                }else{
                    $product_id_price                         =  new \App\Models\ProductPrice();
                    $product_id_price->product_id             =  $product->id ;   
                    $product_id_price->business_id            =  $user->business_id ;   
                    $product_id_price->name                   =  $val ;   
                    $product_id_price->default_purchase_price =  $data1->single_dpp;   
                    $product_id_price->dpp_inc_tax            =  $data1->single_dpp_inc_tax ;   
                    $product_id_price->profit_percent         =  $data1->profit_percent ;   
                    $product_id_price->default_sell_price     =  $data1->single_dsp ;   
                    $product_id_price->sell_price_inc_tax     =  $data1->single_dsp_inc_tax ;   
                    $product_id_price->number_of_default      =  $k ;     
                    $product_id_price->unit_id                =  $unit ;
                    $product_id_price->save();
                }
                
            }
        //*****21** Price Variation
            public static function createSingleProductVariation($product, $sku, $purchase_price, $dpp_inc_tax, $profit_percent, $selling_price, $selling_price_inc_tax, $combo_variations = [])
            {
                if (!is_object($product)) {
                    $product = \App\Product::find($product);
                }
                //create product variations
                $product_variation_data = [
                                            'name'     => 'DUMMY',
                                            'is_dummy' => 1
                                        ];
                $product_variation = $product->product_variations()->create($product_variation_data);
                $taxes_product     = ($product->product_tax)?$product->product_tax->amount:0;

                //create variations
                $Util  = new Util();
                $variation_data = [
                        'name'                   => 'DUMMY',
                        'product_id'             => $product->id,
                        'sub_sku'                => $sku,
                        'default_purchase_price' => $Util->num_uf($purchase_price),
                        'dpp_inc_tax'            => $Util->num_uf(($purchase_price + (($purchase_price * $taxes_product)/(100)))),
                        'profit_percent'         => $Util->num_uf($profit_percent),
                        'default_sell_price'     => $Util->num_uf((($selling_price_inc_tax * 100 )/(100+$taxes_product))),
                        'sell_price_inc_tax'     => $Util->num_uf($selling_price_inc_tax),
                        'combo_variations'       => $combo_variations
                    ];
                $variation = $product_variation->variations()->create($variation_data);
                return true;
            } 
        //*****22** Price Variation
            public static function updateVariableProductVariations($product_id, $input_variations_edit,$type=null)
            {
                $product = \App\Product::find($product_id);

                //Update product variations
                $product_variation_ids = [];
                $variations_ids = [];
                $lengthOfTables = 0;
                foreach ($input_variations_edit as $key => $value) {
                    $product_variation_ids[] = $key;
                    
                    $product_variation =  \App\ProductVariation::find($key);
                    $product_variation->name = $value['name'];
                    $product_variation->save();
                    $Util  = new Util();
                    //Update existing variations
                    if (!empty($value['variations_edit'])) {
                        $line = 0;
                        foreach ($value['variations_edit'] as $k => $v) {
                        
                            $data = [
                                'name'                    => $v['value'],
                                'default_purchase_price'  => $Util->num_uf($v['default_purchase_price']),
                                'dpp_inc_tax'             => $Util->num_uf($v['dpp_inc_tax']),
                                'profit_percent'          => $Util->num_uf($v['profit_percent']),
                                'default_sell_price'      => $Util->num_uf($v['default_sell_price']),
                                'sell_price_inc_tax'      => $Util->num_uf($v['sell_price_inc_tax'])
                            ];
                            if (!empty($v['sub_sku'])) {
                                $data['sub_sku'] = $v['sub_sku'];
                            }
                            $variation =  \App\Variation::where('id', $k)
                                    ->where('product_variation_id', $key)
                                    ->first();
                            $variation->update($data);
                            
                            
                            if($type!=null){
                                \App\Media::uploadMedia($product->business_id, $variation, request(), 'edit_variation_images_' .  $lengthOfTables . '_' . $line++);
                            }else{
                                \App\Media::uploadMedia($product->business_id, $variation, request(), 'edit_variation_images_' . $key . '_' . $line++);
                            }

                            $variations_ids[] = $k;
                        }
                    }

                    //Add new variations
                    if (!empty($value['variations'])) {
                        $variation_data = [];
                        $c =  \App\Variation::withTrashed()
                                        ->where('product_id', $product->id)
                                        ->count()+1;
                        $media = [];
                            
                        foreach ($value['variations'] as $k => $v) {
                            $sub_sku = empty($v['sub_sku'])? GlobalUtil::generateSubSku($product->sku, $c, $product->barcode_type) :$v['sub_sku'];
                        
                            $variation_value_name = !empty($v['value'])? $v['value'] : null;
                            $variation_value_id = null;

                            if (!empty($product_variation->variation_template_id)) {
                                $variation_value =  \App\VariationValueTemplate::where('variation_template_id', $product_variation->variation_template_id)
                                        ->whereRaw('LOWER(name)="' . $v['value'] . '"')
                                        ->first();
                                if (empty($variation_value)) {
                                    $variation_value =  \App\VariationValueTemplate::create([
                                        'name' => $v['value'],
                                        'variation_template_id' => $product_variation->variation_template_id
                                    ]);
                                }

                                $variation_value_id = $variation_value->id;
                            }

                            $variation_data[] = [
                            'name'                    => $variation_value_name,
                            'variation_value_id'      => $variation_value_id,
                            'product_id'              => $product->id,
                            'sub_sku'                 => $sub_sku,
                            'default_purchase_price'  => $Util->num_uf($v['default_purchase_price']),
                            'dpp_inc_tax'             => $Util->num_uf($v['dpp_inc_tax']),
                            'profit_percent'          => $Util->num_uf($v['profit_percent']),
                            'default_sell_price'      => $Util->num_uf($v['default_sell_price']),
                            'sell_price_inc_tax'      => $Util->num_uf($v['sell_price_inc_tax'])
                            ];
                            $c++;
                            $media[] = 'variation_images_' . $key . '_' . $k;
                        }
                    
                        $new_variations = $product_variation->variations()->createMany($variation_data);

                        $i = 0;
                        
                        foreach ($new_variations as $new_variation) {
                            $variations_ids[] = $new_variation->id;
                            \App\Media::uploadMedia($product->business_id, $new_variation, request(), $media[$i]);
                            $i++;
                        }
                    }
                    $lengthOfTables++;

                //Check if purchase or sell exist for the deletable variations
                $count_purchase = \App\PurchaseLine::join(
                    'transactions as T',
                    'purchase_lines.transaction_id',
                    '=',
                    'T.id'
                    )
                    ->where('T.type', 'purchase')
                    ->where('T.status', 'received')
                    ->where('T.business_id', $product->business_id)
                    ->where('purchase_lines.product_id', $product->id)
                    ->whereNotIn('purchase_lines.variation_id', $variations_ids)
                    ->count();

                $count_sell =  \App\TransactionSellLine::join(
                    'transactions as T',
                    'transaction_sell_lines.transaction_id',
                    '=',
                    'T.id'
                    )
                    ->where('T.type', 'sell')
                    ->where('T.status', 'final')
                    ->where('T.business_id', $product->business_id)
                    ->where('transaction_sell_lines.product_id', $product->id)
                    ->whereNotIn('transaction_sell_lines.variation_id', $variations_ids)
                    ->count();

                $is_variation_delatable = $count_purchase > 0 || $count_sell > 0? false : true;
        
            
                if ($is_variation_delatable) {
                    \App\Variation::whereNotIn('id', $variations_ids)
                                    ->where('product_variation_id', $key)->delete();
                } else {
                    throw new \Exception(__('lang_v1.purchase_already_exist'));
                }

                $allTable = \App\ProductVariation::where('product_id', $product_id)
                        ->whereNotIn('id', $product_variation_ids)
                        ->get();
                    foreach($allTable as $table){
                        $child = \App\Variation::where('product_variation_id', $table->id)->get();
                        foreach($child as $chi){
                            $chi->delete();
                        }
                        $table->delete();
                    }
                }
                if($type!=null){
                    return $lengthOfTables;
                }
                    
            }
        //*****23** Check if there is relations */
            public static function checkContact($id) {
                $checkAccount     = \App\Account::where("contact_id",$id)->first();
                $checkTransaction = \App\Transaction::where("contact_id",$id)->first();
                $checkAccountMove = \App\AccountTransaction::where("account_id",$checkAccount->id)->first(); 
                if( $checkAccountMove   != null  || $checkTransaction != null  ) {
                    return true;
                }else {
                    return false;
                }
            }
        //*****24** Check if there is relations */
            public static function checkAccountType($id) {
                $checkParentAccount    = \App\AccountType::where("parent_account_type_id",$id)->first();
                $checkAccount          = \App\Account::where("account_type_id",$id)->first();
                if( $checkParentAccount   != null  || $checkAccount != null  ) {
                    return true;
                }else {
                    return false;
                }
            }
        //*****25** generate product code */
            public static function generateSubSku($sku, $c, $barcode_type)
            {
                $sub_sku = $sku . $c;
        
                if (in_array($barcode_type, ['C128', 'C39'])) {
                    $sub_sku = $sku . '-' . $c;
                }
        
                return $sub_sku;
            }
        // ******END*
        
        // ******* FOR SAVE FUNCTION
        //*****1**  save transaction  */ 
            public static function transaction($data) {
                $ref_count           =  GlobalUtil::SetReferenceCount('Open Quantity', $data["business_id"]);
                $ref_no              =  GlobalUtil::GenerateReferenceCount('Open Quantity', $ref_count , $data["business_id"]);
                $location            =  \App\BusinessLocation::where("business_id",$data["business_id"])->first();
                $transaction         =  \App\Transaction::create([
                                            'ref_no'          => $ref_no,
                                            'type'            => 'opening_stock',
                                            'status'          => 'received',
                                            'business_id'     => $data["business_id"],
                                            'store'           => $data["store"],
                                            'location_id'     => $location->id,  
                                            'list_price'      => $data["list_price"],  
                                            'transaction_date'=>($data["date"])?\Carbon\Carbon::parse($data["date"]):date('Y-m-d h:i:s',time())
                                        ]);
                return $transaction;
            }
        //*****2**  save purchase line  */ 
            public static function purchase_line($data,$transaction) {
                $sub_unit                        =  \App\Unit::find($data['unit_id']);  
                $product                         =  Product::find($data['product_id']);
                $tax_info                        = \App\TaxRate::find($product->tax);
                $tax_percent                     = ($tax_info)?$tax_info->amount/100:0;
                $line                            =  new PurchaseLine;
                $line->store_id                  = ($data["line_store"]!="")?$data["line_store"]:$transaction->store;
                $line->product_id                = $data['product_id'];
                $line->transaction_id            = $transaction->id;
                $line->variation_id              = isset($product->variations[0]->id)?$product->variations[0]->id:NULL;
                $line->quantity                  = $data['quantity'];
                $line->pp_without_discount       = $data['price'];
                $line->discount_percent          = 0;
                $line->purchase_price            = $data['price'];
                $line->purchase_price_inc_tax    = ($data['price'] + $data['price']*$tax_percent);
                $line->item_tax                  = $data['price']*$tax_percent;
                $line->tax_id                    = 1;
                $line->order_id                  = $data['line_sorting'];
                $line->sub_unit_id               = ($sub_unit)?$sub_unit->id:null;
                $line->sub_unit_qty              = ($sub_unit)?(($sub_unit->base_unit_multiplier!=null)?$sub_unit->base_unit_multiplier:1):1;
                $line->list_price                = ($data['list_price']!=null || $data['list_price']!="")?$data['list_price']:$transaction->list_price;
                $line->save();
                return $line;
            }
        //*****3**  update transaction  */ 
            public static function updateTransaction($data,$id) {
                \App\Transaction::where("id",$id)->update([
                                            'store'           => $data["store"],
                                            'list_price'      => $data["list_price"],
                                            'transaction_date'=>($data["date"])?\Carbon\Carbon::parse($data["date"]):date('Y-m-d h:i:s',time())
                ]);
                $transaction  = \App\Transaction::find($id);
                return $transaction;
            }
        //*****4**  update purchase line  */ 
            public static function updatePurchase_line($data,$transaction,$id) {
                $sub_unit                            =  \App\Unit::find($data['unit_id']);
                if($data["line_id"] != ""){
                    $product                         = Product::find($data['product_id']);
                    $tax_info                        = \App\TaxRate::find($product->tax);
                    $tax_percent                     = ($tax_info)?$tax_info->amount/100:0;
                    $line                            = PurchaseLine::find($data["line_id"]);
                    $line->store_id                  = ($data["line_store"]!="")?$data["line_store"]:$transaction->store;
                    $line->product_id                = $data['product_id'];
                    $line->variation_id              = isset($product->variations[0]->id)?$product->variations[0]->id:NULL;
                    $line->quantity                  = $data['quantity'];
                    $line->pp_without_discount       = $data['price'];
                    $line->discount_percent          = 0;
                    $line->purchase_price            = $data['price'];
                    $line->purchase_price_inc_tax    = ($data['price'] + $data['price']*$tax_percent);
                    $line->item_tax                  = $data['price']*$tax_percent;
                    $line->tax_id                    = 1;
                    $line->order_id                  = $data['line_sorting'];
                    $line->sub_unit_id               = ($sub_unit)?$sub_unit->id:null;
                    $line->sub_unit_qty              = ($sub_unit)?(($sub_unit->base_unit_multiplier!=null)?$sub_unit->base_unit_multiplier:1):1;
                    $line->list_price                = ($data['list_price']!=null || $data['list_price']!="")?$data['list_price']:$transaction->list_price;
                    $line->update();
                }else{
                    
                    $product                         = Product::find($data['product_id']);
                    $tax_info                        = \App\TaxRate::find($product->tax);
                    $tax_percent                     = ($tax_info)?$tax_info->amount/100:0;
                    $line                            = new PurchaseLine();
                    $line->store_id                  = ($data["line_store"]!="")?$data["line_store"]:$transaction->store;
                    $line->product_id                = $data['product_id'];
                    $line->transaction_id            = $transaction->id;
                    $line->variation_id              = isset($product->variations[0]->id)?$product->variations[0]->id:NULL;
                    $line->quantity                  = $data['quantity'];
                    $line->pp_without_discount       = $data['price'];
                    $line->discount_percent          = 0;
                    $line->purchase_price            = $data['price'];
                    $line->purchase_price_inc_tax    = ($data['price'] + $data['price']*$tax_percent);
                    $line->item_tax                  = $data['price']*$tax_percent;
                    $line->tax_id                    = 1;
                    $line->order_id                  = $data['line_sorting'];
                    $line->sub_unit_id               = ($sub_unit)?$sub_unit->id:null;
                    $line->sub_unit_qty              = ($sub_unit)?(($sub_unit->base_unit_multiplier!=null)?$sub_unit->base_unit_multiplier:1):1;
                    $line->list_price                = ($data['list_price']!=null || $data['list_price']!="")?$data['list_price']:$transaction->list_price;
                    $line->save();
                }
                return $line;
            }
        //*****5** save storeMovement & itemMovement */
            public static function storeItemMovement($data,$transaction,$line,$sub_unit) {
                try{
                    $quantity_if_multiple_unit     = ($sub_unit)?(($sub_unit->base_unit_multiplier!=null)?$sub_unit->base_unit_multiplier:1):1;  
                    $final_quantity                = $quantity_if_multiple_unit * $data->quantity; 
                    WarehouseInfo::update_stoct($data->product_id,$data->warehouse_id,$final_quantity,$transaction->business_id);
                    //****** eb ..............................................................
                    $variation_id = Variation::where('product_id', $data->product_id)->first();
                    //.........................................................................
                    //move
                    $info                      =  WarehouseInfo::where('store_id',$data->warehouse_id)->where('product_id',$data->product_id)->first();
                    $move                      =  new MovementWarehouse;
                    $move->business_id         =  $transaction->business_id;
                    $move->transaction_id      =  $transaction->id  ;
                    $move->product_name        =  $data->product->name;
                    $move->unit_id             =  $data->product->unit_id;
                    $move->store_id            =  $data->warehouse_id  ;
                    $move->movement            =   'opening_stock';
                    $move->plus_qty            =  $final_quantity ;
                    $move->minus_qty           =  0;
                    $move->current_qty         =  $info->product_qty ;
                    $move->product_unit        =  ($sub_unit)?$sub_unit->id:null;
                    $move->product_unit_qty    =  ($sub_unit)?(($sub_unit->base_unit_multiplier!=null)?$sub_unit->base_unit_multiplier:1):1;
                    $move->date                =  ($transaction->transaction_date)?\Carbon\Carbon::parse($transaction->transaction_date):date('Y-m-d h:i:s',time()) ;
                    $move->product_id          =  $data->product_id;
                    $move->current_price       =  $data->price;
                    $move->opening_quantity_id =  $data->id;
                    $move->save();
                    //.........................................................................
                    $before                    = WarehouseInfo::qty_before($transaction);
                    ItemMove::create_open($transaction,0,$before,null,0,$line->id);
                    return true;
                }catch(Exception $e){
                    return false;
                }
            }
        //*****6** update storeMovement & itemMovement */
            public static function updateStoreItemMovement($data,$open,$transaction,$line,$old_qty,$old_store,$sub_unit,$old_list) {
                try{
                    
                   
                    $quantity           =  $data["quantity"];
                    if($old_qty != null){
                         
                        $old_quantity       =  $old_list['old_quantity'] ;
                        $old_unit_id        =  $old_list['old_unit_id'] ;
                        $old_unit_qty       =  $old_list['old_unit_qty'] ;
                        $new_unit_qty       =  $old_list['new_unit_qty'] ;
                        
                        $quantity           =  ($new_unit_qty!=0)?$quantity*$new_unit_qty:$quantity;
                        $old_qty            =  ($old_unit_qty!=0)?(($old_qty!=null)?$old_qty:0)*$old_unit_qty:$old_qty;
                        
                        $diff               =  $quantity - $old_qty;
                    }else{
                        $new_unit_qty       =  $old_list['new_unit_qty'] ;
                        $diff               =  ($new_unit_qty!=0)?$quantity*$new_unit_qty:$quantity - 0;
                        $quantity           =  $diff;
                         
                    }
                    
                    $stores         = ($data["line_store"] != "")?$data["line_store"]:$transaction->store;
                    
                    if ($old_store  ==  $stores) {
                            WarehouseInfo::update_stoct($data["product_id"],$stores,$diff,$transaction->business_id);
                    }else{
                        if($old_store != null){
                            WarehouseInfo::update_stoct($data["product_id"],$old_store,($old_qty*-1),$transaction->business_id);
                        }
                        if($stores != null){
                            WarehouseInfo::update_stoct($data["product_id"],$stores,$quantity,$transaction->business_id);
                        }
                    }
                    //****** eb ..............................................................
                    $variation_id = Variation::where('product_id', $data["product_id"])->first();
                    //.........................................................................
                    //move
                    $info                      =  WarehouseInfo::where('store_id',$stores)->where('product_id',$data["product_id"])->first();
                    $product                   =  Product::find($data["product_id"]);
                    $move                      =  MovementWarehouse::where('opening_quantity_id',$open->id)->first();
                    if(!empty($move)){
                        $move->store_id            =  $stores  ;
                        $move->plus_qty            =  $quantity ;
                        $move->minus_qty           =  0;
                        $move->product_unit        =  ($sub_unit)?$sub_unit->id:null;#2024-8-6
                        $move->product_unit_qty    =  isset($new_unit_qty)?$new_unit_qty:1;#2024-8-6
                        $move->current_qty         =  $info->product_qty ;
                        $move->date                =  ($transaction->transaction_date)?\Carbon\Carbon::parse():date('Y-m-d h:i:s',time());
                        $move->current_price       =  $data["price"];
                        $move->movement            =  'opening_stock';
                        $move->update();
                    }else{
                        $move                      =  new MovementWarehouse;
                        $move->business_id         =  $transaction->business_id;
                        $move->transaction_id      =  $transaction->id  ;
                        $move->product_name        =  $product->name;
                        $move->unit_id             =  $product->unit_id;
                        $move->product_unit        =  ($sub_unit)?$sub_unit->id:null;#2024-8-6
                        $move->product_unit_qty    =  isset($new_unit_qty)?$new_unit_qty:1;#2024-8-6
                        $move->store_id            =  $stores ;
                        $move->movement            =  'opening_stock';
                        $move->plus_qty            =  $quantity ;
                        $move->minus_qty           =  0;
                        $move->current_qty         =  $info->product_qty ;
                        $move->date                =  ($transaction->transaction_date)?\Carbon\Carbon::parse($transaction->transaction_date):date('Y-m-d h:i:s',time()) ;
                        $move->product_id          =  $data["product_id"];
                        $move->current_price       =  $data["price"];
                        $move->opening_quantity_id =  $open->id;
                        $move->save();
                    }
                    $before                    = WarehouseInfo::qty_before($transaction);
                    ItemMove::create_open($transaction,0,$before,null,0,$line->id);
                    return true;
                }catch(Exception $e){
                    return false;
                }
            }
        // *********END*
    // ............ VOUCHER SECTION
        // **1 ** 
        public static function effect_account_expanse($id,$type,$created_by=null) {
                // supplier depit  => bank  credit
                // customer credit => debit  
                $data          =  \App\Models\PaymentVoucher::find($id);
                $state         =  'debit';
                $re_state      =  'credit';
                if ($type == 1 ) {
                    $state     =  'credit';
                    $re_state  =  'debit';
                }
                // effect cash  account 
                $credit_data = [
                    'amount'              => $data->amount,
                    'account_id'          => $data->account_id,
                    'type'                => $re_state,
                    'sub_type'            => 'deposit',
                    'operation_date'      => $data->date,
                    'created_by'          => ($created_by != null)? $created_by:session()->get('user.id'),
                    'note'                => $data->text,
                    'payment_voucher_id'  => $id
                ];
                $credit = \App\AccountTransaction::createAccountTransaction($credit_data);
        
                // effect contact account 
                $account_id  = $data->contact_id;
                // $account_id  =  Contact::add_account($data->contact_id);
                $credit_data = [
                    'amount'              => $data->amount,
                    'account_id'          => $account_id,
                    'type'                => $state,
                    'sub_type'            => 'deposit',
                    'operation_date'      => $data->date,
                    'created_by'          => ($created_by != null)? $created_by:session()->get('user.id'),
                    'note'                => $data->text,
                    'payment_voucher_id'  => $data->id
                ];
                $credit = \App\AccountTransaction::createAccountTransaction($credit_data);
                
         }
    // ********END*
    // ............ EXPENSE VOUCHER SECTION
        // **1 ** 
        public static function effect_cost_center($data,$user,$entry)
        {
            $credit =  \App\AccountTransaction::where('gournal_voucher_item_id',$data->id)
                            ->whereHas('account',function($query){
                                $query->where('cost_center','>',0);
                            })->first();
            if ($data->cost_center_id) {
                if (!empty($credit)) {
                    $credit->update([
                        'amount'     =>  $data->amount  -  $data->tax_amount ,
                        'account_id' => $data->cost_center_id,
                    ]);
                }else {
                    $credit_data =  [
                        'amount'                  => $data->amount  -  $data->tax_amount ,
                        'account_id'              => $data->cost_center_id,
                        'type'                    => 'credit',
                        'sub_type'                => 'deposit',
                        'operation_date'          => $data->date,
                        'created_by'              => $user->id,
                        'note'                    => $data->text??'add journal expense', 
                        'gournal_voucher_item_id' => $data->id,
                        'entry_id'                => ($entry)?$entry->id:null
                    ];
                    $credit = \App\AccountTransaction::createAccountTransaction($credit_data);
                }
            }else {
                // \App\AccountTransaction::where('gournal_voucher_item_id',$data->id)->delete();
            }
        }
        // **2 ** 
        public static function effect_account($id,$user,$type=null,$entry)
        {
            $data  =  \App\Models\GournalVoucherItem::find($id);
            // credit account  
            $credit_data = [
                'amount'                  => $data->amount  ,
                'account_id'              => $data->credit_account_id,
                'type'                    => 'credit',
                'sub_type'                => 'deposit',
                'operation_date'          => $data->date,
                'created_by'              => $user->id,
                'note'                    => $data->text, 
                'gournal_voucher_item_id' => $data->id,
                'entry_id'                => ($entry)?$entry->id:null
            ];
            $credit = \App\AccountTransaction::createAccountTransaction($credit_data);
            if($type==null){
                if($data->tax_amount > 0){
                    // tax account  
                    $credit_data = [
                        'amount'                  => $data->tax_amount,
                        'account_id'              => $data->tax_account_id,
                        'type'                    => 'debit',
                        'sub_type'                => 'deposit',
                        'operation_date'          => $data->date,
                        'created_by'              => $user->id,
                        'note'                    => $data->text,
                        'gournal_voucher_item_id' => $data->id,
                        'entry_id'                => ($entry)?$entry->id:null
                    ];
                    $credit = \App\AccountTransaction::createAccountTransaction($credit_data);
                }
                // tax account  
                $credit_data = [
                    'amount'                  => ($data->amount  -  $data->tax_amount),
                    'account_id'              => $data->debit_account_id,
                    'type'                    => 'debit',
                    'sub_type'                => 'deposit',
                    'operation_date'          => $data->date,
                    'created_by'              => $user->id,
                    'note'                    => $data->text,
                    'gournal_voucher_item_id' => $data->id,
                    'cs_related_id'           => $data->cost_center_id,
                    'entry_id'                => ($entry)?$entry->id:null
        
                ];
                $credit = \App\AccountTransaction::createAccountTransaction($credit_data);
            }
        }
        // **3 ** 
        public static function effect_account_total($total,$account_id,$id,$note,$user,$entry)
        {
            $data  =  \App\Models\GournalVoucherItem::find($id);
            \App\AccountTransaction::whereHas("gournal_voucher_item",function($q) use($data){
                                        $q->whereHas("gournal_voucher",function($q) use($data){
                                            $q->where("id",$data->gournal_voucher_id);
                                        });
                                    })->where("type","credit")
                                    ->delete();
            // credit account  
            $credit_data = [
                'amount'                  => $total ,
                'account_id'              => $account_id,
                'type'                    => 'credit',
                'sub_type'                => 'deposit',
                'operation_date'          => $data->date,
                'created_by'              => $user->id,
                'note'                    => $note, 
                'gournal_voucher_item_id' => $data->id,
                'gournal_voucher_id'      => $data->gournal_voucher->id,
                'entry_id'                => ($entry)?$entry->id:null
            ];
            $credit = \App\AccountTransaction::createAccountTransaction($credit_data);
        }
        // **4 ** 
        public static function effect_debit_total($id,$user,$entry)
        {
            $data  =  \App\Models\GournalVoucherItem::find($id);
            if($data->tax_amount > 0){
                // tax account  
                $credit_data = [
                    'amount'                  =>  $data->tax_amount ,
                    'account_id'              => $data->tax_account_id,
                    'type'                    => 'debit',
                    'sub_type'                => 'deposit',
                    'operation_date'          => $data->date,
                    'created_by'              => $user->id,
                    'note'                    => $data->text,
                    'gournal_voucher_item_id' => $data->id,
                    'entry_id'                => ($entry)?$entry->id:null
                ];
                $credit = \App\AccountTransaction::createAccountTransaction($credit_data);
            }
              // tax account  
             $credit_data = [
                'amount'                  => ( $data->amount  -  $data->tax_amount ),
                'account_id'              => $data->debit_account_id,
                'type'                    => 'debit',
                'sub_type'                => 'deposit',
                'operation_date'          => $data->date,
                'created_by'              => $user->id,
                'note'                    => $data->text,
                'gournal_voucher_item_id' => $data->id,
                'cs_related_id'           => $data->cost_center_id,
                'entry_id'                => ($entry)?$entry->id:null
    
            ];
            $credit = \App\AccountTransaction::createAccountTransaction($credit_data);
        }
        // **5 ** 
        public static function edit_effect($data,$old_credit,$old_debit,$old_tax,$user,$old_account_main,$old_status,$entry)
        {
            if($old_status == 1){
                $gournal_id            =  ($data->gournal_voucher)?$data->gournal_voucher->id:null;
                if($gournal_id != null){
                    $trans                 =  \App\AccountTransaction::where('gournal_voucher_id',$gournal_id)
                                                                        ->where('account_id',$old_account_main)
                                                                        ->first();
                    
                    if($trans){
                        $trans->delete();
                    }    
                }
                GlobalUtil::effect_account($data->id,$user,1,$entry);
            }else{
            
                 
                \App\AccountTransaction::where('gournal_voucher_item_id',$data->id)
                                        ->where('account_id',$old_credit)
                                        ->update([
                                                'amount' => $data->amount,
                                                'operation_date' => $data->date,
                                                'account_id' =>$data->credit_account_id,
                                        ]);
                 
            
                $business_id = $user->business_id;
                $setting     = \App\Models\SystemAccount::where("business_id",$business_id)->first();
                $tax         = ($setting)?$setting->journal_expense_tax:\App\Account::add_main('tax expense');
                // tax account  
                \App\AccountTransaction::where('gournal_voucher_item_id',$data->id)
                                ->where('type',"debit")
                                ->where('account_id',$old_tax)
                                ->update([
                                    'amount'         => $data->tax_amount ,
                                    'operation_date' => $data->date,
                                    'account_id'     => $tax,
                                ]);
                                
                \App\AccountTransaction::where('gournal_voucher_item_id',$data->id)
                            ->where('type',"debit")
                            ->where('account_id',$old_debit)
                            ->update([
                                'amount'         => ( $data->amount  -  $data->tax_amount ),
                                'operation_date' => $data->date,
                                'account_id'     => $data->debit_account_id,
                                'cs_related_id'  => $data->cost_center_id
    
                            ]);
            }
        }
        // **6 ** 
        public static function edit_effect_accounts($data,$old_credit,$old_debit,$old_tax,$user,$old_status,$entry)
        {
          
            $business_id = $user->business_id;
            $setting     = \App\Models\SystemAccount::where("business_id",$business_id)->first();
            $tax         = ($setting)?$setting->journal_expense_tax:\App\Account::add_main('tax expense');
            // tax account  
            \App\AccountTransaction::where('gournal_voucher_item_id',$data->id)
                            ->where('type',"debit")
                            ->where('account_id',$old_tax)
                            ->update([
                            'amount'         => $data->tax_amount ,
                            'operation_date' => $data->date,
                            'account_id'     => $tax,
                            ]);
            \App\AccountTransaction::where('gournal_voucher_item_id',$data->id)
                            ->where('type',"debit")
                            ->where('account_id',$old_debit)
                            ->update([
                                'amount'         => ( $data->amount  -  $data->tax_amount  ),
                                'operation_date' => $data->date,
                                'account_id'     => $data->debit_account_id,
                                'cs_related_id'  => $data->cost_center_id
    
                            ]);
        }
        // **7 ** 
        public static function edit_effect_main($data,$total,$account_id,$note,$user,$old_account_main,$old_status,$note_main,$entry)
        {
            if($data->gournal_voucher->main_account_id != $old_account_main){
                if($old_status == 0){
                    $list_ids  = GournalVoucherItem::where('gournal_voucher_id',$data->gournal_voucher->id)->pluck('id');
                    $list_main = [$data->gournal_voucher->main_account_id,$old_account_main];
                    GlobalUtil::delete_credit_items($list_ids,$list_main);
                    $credit_data = [
                        'amount'                  => $data->gournal_voucher->total_credit,
                        'account_id'              => $data->gournal_voucher->main_account_id,
                        'type'                    => 'credit',
                        'sub_type'                => 'deposit',
                        'operation_date'          => $data->date,
                        'created_by'              => $user->id ,
                        'note'                    => $note_main, 
                        // 'gournal_voucher_item_id' => $data->id,
                        'gournal_voucher_id'      => $data->gournal_voucher->id,
                        'entry_id'                => ($entry)?$entry->id:null
                    ];
                    $credit = \App\AccountTransaction::createAccountTransaction($credit_data);
                }else{
                    // ** here 
                    $gournal_id            =  ($data->gournal_voucher)?$data->gournal_voucher->id:null;
                    if($gournal_id != null){
                        $trans             =  \App\AccountTransaction::where('gournal_voucher_id',$gournal_id)
                                                                        ->where('account_id',$old_account_main)
                                                                        ->first();
                      
                        if(!empty($trans)){
                            $trans->delete();
                        }
                    }
                    $credit_data = [
                        'amount'                  => $data->gournal_voucher->total_credit,
                        'account_id'              => $data->gournal_voucher->main_account_id,
                        'type'                    => 'credit',
                        'sub_type'                => 'deposit',
                        'operation_date'          => $data->date,
                        'created_by'              => $user->id,
                        'note'                    => $note_main, 
                        'gournal_voucher_id'      => $gournal_id,
                        'entry_id'                => ($entry)?$entry->id:null
                    ];
                    $credit = \App\AccountTransaction::createAccountTransaction($credit_data);
                    
                }
            }else if($data->gournal_voucher->main_account_id == $old_account_main){
               
                if($old_status == 0){
                    $list_ids  = GournalVoucherItem::where('gournal_voucher_id',$data->gournal_voucher->id)->pluck('id');
                    $list_main = [$data->gournal_voucher->main_account_id,$old_account_main];
                    GlobalUtil::delete_credit_items($list_ids,$list_main);
                    $credit_data = [
                        'amount'                  => $data->gournal_voucher->total_credit,
                        'account_id'              => $data->gournal_voucher->main_account_id,
                        'type'                    => 'credit',
                        'sub_type'                => 'deposit',
                        'operation_date'          => $data->date,
                        'created_by'              => $user->id,
                        'note'                    => $note_main, 
                        // 'gournal_voucher_item_id' => $data->id
                        'gournal_voucher_id' => $data->gournal_voucher->id,
                        'entry_id'                => ($entry)?$entry->id:null
                    ];
                    $credit   = \App\AccountTransaction::createAccountTransaction($credit_data);
                }else{
                     // ** here
                    $gournal_id            =  ($data->gournal_voucher)?$data->gournal_voucher->id:null;
                    if($gournal_id != null){
                        $trans             =  \App\AccountTransaction::where('gournal_voucher_id',$gournal_id)
                                                                    ->where('account_id',$data->gournal_voucher->main_account_id)
                                                                    ->first();
                        if(!empty($trans)){
                            $trans->amount         = $total;
                            $trans->operation_date = $data->date;
                            $trans->account_id     = $account_id;
                            $trans->note           = $note;
                            $trans->update();
                        }
                    }
                }
            }  
        }
        // **8 ** 
        public function delete_credit_items($list_ids,$account_id) {
            $credit =  \App\AccountTransaction::whereIn('gournal_voucher_item_id',$list_ids)
                            ->where('type','credit')
                            ->whereHas('account',function($query) use($account_id){
                                $query->where('cost_center',"=",0);
                            })->get();
            
            foreach($credit as $i){
                $i->delete();
            }
            $main = \App\AccountTransaction::whereIn('gournal_voucher_id',$account_id)->get();
            foreach($main as $i){
                $i->delete();
            }
        }
        // **9 ** 
        public function create_credit_items($list_ids,$user,$entry) {
            foreach($list_ids as $id){
                $data  =  GournalVoucherItem::find($id);
                // credit account  
                $credit_data = [
                    'amount'                  => $data->amount,
                    'account_id'              => $data->credit_account_id,
                    'type'                    => 'credit',
                    'sub_type'                => 'deposit',
                    'operation_date'          => $data->date,
                    'created_by'              => $user->id,
                    'note'                    => $data->text, 
                    'gournal_voucher_item_id' => $data->id,
                    'entry_id'                => ($entry)?$entry->id:null
                ];
                $credit = \App\AccountTransaction::createAccountTransaction($credit_data);
            }
        }
    // ********END*
    // ****
        public static function permissions($list){
            $permissions                                          =  [] ;
            // ... Dashboard
            $allPermissions                                       =  [] ;
            $allPermissions["dashboard"]                          =  (in_array("sidBar.Dashboard",$list) || in_array("Dashboard",$list))?true:false ;
            $permissions["sidBar"]["dashboard"] = $allPermissions;
           
            // **AGT8422... User Section   
            $allPermissions                                       =  [] ;$allChildren = [];$allTable = [];$allTableAction = [];
            $allPermissions["user_management"]                    =  in_array("sidBar.UserManagement",$list)?true:false ;
            // ************************************************************************************* \\
            /** 1*/$allPermissions["users"]                       =  in_array("sidBar.Users",$list)?true:false ;
            /** Child */    
            $allChildren["user_view"]                             =  in_array("user.view",$list)?true:false ;
            $allChildren["user_create"]                           =  in_array("user.create",$list)?true:false ;
            $allChildren["user_update"]                           =  in_array("user.update",$list)?true:false ;
            $allChildren["user_delete"]                           =  in_array("user.delete",$list)?true:false ;
            /** Table action */    
            $allTableAction["user_action_view"]                   =  in_array("user.view",$list)?true:false ;
            $allTableAction["user_action_update"]                 =  in_array("user.update",$list)?true:false ;
            $allTableAction["user_action_delete"]                 =  in_array("user.delete",$list)?true:false ;
            /** Table Column */    
            $allTable["user_col_username"]                        =  in_array("user_col.username",$list)?true:false ;/** 1 */
            $allTable["user_col_name"]                            =  in_array("user_col.name",$list)?true:false ;/** 2 */
            $allTable["user_col_role"]                            =  in_array("user_col.role",$list)?true:false ;/** 3 */
            $allTable["user_col_email"]                           =  in_array("user_col.email",$list)?true:false ;/** 4 */
            // ....................................................
            // ************************************************************************************* \\
            $parent                       =  [] ;
            $parent["parent"]             = $allPermissions;
            $parent["child"]              = $allChildren;
            $parent["action"]             = $allTableAction;
            $parent["column"]             = $allTable;
            $permissions["sidBar"]["users"]     = $parent;
         
            
            // ************************************************************************************* \\
            $allPermissions                                       =  [] ;$allChildren = [];$allTable = [];$allTableAction = [];
            $allPermissions["user_management"]                    =  in_array("sidBar.UserManagement",$list)?true:false ;
            /** 2*/$allPermissions["roles"]                       =  in_array("sidBar.Roles",$list)?true:false ;
            /** Child */    
            $allChildren["role_view"]                             =  in_array("role.view",$list)?true:false ;
            $allChildren["role_create"]                           =  in_array("role.create",$list)?true:false ;
            $allChildren["role_update"]                           =  in_array("role.update",$list)?true:false ;
            $allChildren["role_delete"]                           =  in_array("role.delete",$list)?true:false ;
            /** Table action */    
            $allTableAction["role_action_view"]                   =  in_array("role.view",$list)?true:false ;
            $allTableAction["role_action_update"]                 =  in_array("role.update",$list)?true:false ;
            $allTableAction["role_action_delete"]                 =  in_array("role.delete",$list)?true:false ;
            /** Table Column */    
            $allTable["role_col_name"]                            =  in_array("role_col.name",$list)?true:false ;/** 1 */
            $allTable["role_col_assigned"]                        =  in_array("role_col.assigned",$list)?true:false ;/** 2 */
            $allTable["role_col_date"]                            =  in_array("role_col.date",$list)?true:false ;/** 3 */
            // ....................................................
            // ************************************************************************************* \\
            $parent                       =  [] ;
            $parent["parent"]             = $allPermissions;
            $parent["child"]              = $allChildren;
            $parent["action"]             = $allTableAction;
            $parent["column"]             = $allTable;
            $permissions["sidBar"]["role"]      = $parent;
            dd( $permissions );
            dd(json_decode(json_encode($permissions)));

            // **AGT8422... Contact Section   
            $allPermissions["contacts"]                           =  in_array("sidBar.Contacts",$list)?true:false ;
            // ************************************************************************************* \\
            /** 1*/$allPermissions["suppliers"]                   =  in_array("sidBar.Suppliers",$list)?true:false ;
            /** Child */    
            $allChildren["supplier_view"]                         =  in_array("supplier.view",$list)?true:false ;
            $allChildren["supplier_view_own"]                     =  in_array("supplier.view_own",$list)?true:false ;
            $allChildren["supplier_create"]                       =  in_array("supplier.create",$list)?true:false ;
            $allChildren["supplier_update"]                       =  in_array("supplier.update",$list)?true:false ;
            $allChildren["supplier_delete"]                       =  in_array("supplier.delete",$list)?true:false ;
            /** Table action */    
            $allTableAction["supplier_action_view"]               =  in_array("supplier.view",$list)?true:false ;
            $allTableAction["supplier_action_view_statement"]     =  in_array("supplier.view_statement",$list)?true:false ;
            $allTableAction["supplier_action_pay"]                =  in_array("supplier.pay",$list)?true:false ;
            $allTableAction["supplier_action_deactivate"]         =  in_array("supplier.deactivate",$list)?true:false ;
            $allTableAction["supplier_action_ledger"]             =  in_array("supplier.ledger",$list)?true:false ;
            $allTableAction["supplier_action_document_and_notes"] =  in_array("supplier.document_and_notes",$list)?true:false ;
            $allTableAction["supplier_action_update"]             =  in_array("supplier.update",$list)?true:false ;
            $allTableAction["supplier_action_delete"]             =  in_array("supplier.delete",$list)?true:false ;
            /** Table Column */    
            $allTable["supplier_col_business_name"]               =  in_array("supplier_col.business_name",$list)?true:false ;/** 1 */
            $allTable["supplier_col_tax_number"]                  =  in_array("supplier_col.tax_number",$list)?true:false ;/** 2 */
            $allTable["supplier_col_type"]                        =  in_array("supplier_col.type",$list)?true:false ;/** 3 */
            $allTable["supplier_col_status"]                      =  in_array("supplier_col.status",$list)?true:false ;/** 4 */
            $allTable["supplier_col_mobile"]                      =  in_array("supplier_col.mobile",$list)?true:false ;/** 5 */
            $allTable["supplier_col_credit_limit"]                =  in_array("supplier_col.credit_limit",$list)?true:false ;/** 6 */
            $allTable["supplier_col_pay_term"]                    =  in_array("supplier_col.pay_term",$list)?true:false ;/** 7 */
            $allTable["supplier_col_opening_balance"]             =  in_array("supplier_col.opening_balance",$list)?true:false ;/** 8 */
            $allTable["supplier_col_advanced_balance"]            =  in_array("supplier_col.advanced_balance",$list)?true:false ;/** 9 */
            $allTable["supplier_col_added_on"]                    =  in_array("supplier_col.added_on",$list)?true:false ;/** 10 */
            $allTable["supplier_col_customer_group"]              =  in_array("supplier_col.customer_group",$list)?true:false ;/** 11 */
            $allTable["supplier_col_address"]                     =  in_array("supplier_col.address",$list)?true:false ;/** 12 */
            $allTable["supplier_col_total_purchase_due"]          =  in_array("supplier_col.total_purchase_due",$list)?true:false ;/** 13 */
            $allTable["supplier_col_total_purchase_return"]       =  in_array("supplier_col.total_purchase_return",$list)?true:false ;/** 14 */
            $allTable["supplier_col_total_all_due"]               =  in_array("supplier_col.total_all_due",$list)?true:false ;/** 15 */
            $allTable["supplier_col_custom_field_1"]              =  in_array("supplier_col.custom_field_1",$list)?true:false ;/** 16 */
            $allTable["supplier_col_custom_field_2"]              =  in_array("supplier_col.custom_field_2",$list)?true:false ;/** 17 */
            $allTable["supplier_col_custom_field_3"]              =  in_array("supplier_col.custom_field_3",$list)?true:false ;/** 18 */
            $allTable["supplier_col_custom_field_4"]              =  in_array("supplier_col.custom_field_4",$list)?true:false ;/** 19 */
            $allTable["supplier_col_custom_field_5"]              =  in_array("supplier_col.custom_field_5",$list)?true:false ;/** 20 */
            $allTable["supplier_col_custom_field_6"]              =  in_array("supplier_col.custom_field_6",$list)?true:false ;/** 21 */
            $allTable["supplier_col_custom_field_7"]              =  in_array("supplier_col.custom_field_7",$list)?true:false ;/** 22 */
            $allTable["supplier_col_custom_field_8"]              =  in_array("supplier_col.custom_field_8",$list)?true:false ;/** 23 */
            $allTable["supplier_col_custom_field_9"]              =  in_array("supplier_col.custom_field_9",$list)?true:false ;/** 24 */
            $allTable["supplier_col_custom_field_10"]             =  in_array("supplier_col.custom_field_10",$list)?true:false ;/** 25 */
            // ....................................................
            // ************************************************************************************* \\
            
            // ************************************************************************************* \\
            /** 2*/$allPermissions["customers"]                   =  in_array("sidBar.Customers",$list)?true:false ;
            /** Child */    
            $allChildren["customer_view"]                         =  in_array("customer.view",$list)?true:false ;
            $allChildren["customer_view_own"]                     =  in_array("customer.view_own",$list)?true:false ;
            $allChildren["customer_create"]                       =  in_array("customer.create",$list)?true:false ;
            $allChildren["customer_update"]                       =  in_array("customer.update",$list)?true:false ;
            $allChildren["customer_delete"]                       =  in_array("customer.delete",$list)?true:false ;        
            /** Table action */    
            $allTableAction["customer_action_view"]               =  in_array("customer.view",$list)?true:false ;
            $allTableAction["customer_action_view_statement"]     =  in_array("customer.view_statement",$list)?true:false ;
            $allTableAction["customer_action_pay"]                =  in_array("customer.pay",$list)?true:false ;
            $allTableAction["customer_action_deactivate"]         =  in_array("customer.deactivate",$list)?true:false ;
            $allTableAction["customer_action_ledger"]             =  in_array("customer.ledger",$list)?true:false ;
            $allTableAction["customer_action_document_and_notes"] =  in_array("customer.document_and_notes",$list)?true:false ;
            $allTableAction["customer_action_update"]             =  in_array("customer.update",$list)?true:false ;
            $allTableAction["customer_action_delete"]             =  in_array("customer.delete",$list)?true:false ;
            /** Table Column */    
            $allTable["customer_col_business_name"]               =  in_array("customer_col.business_name",$list)?true:false ;/** 1 */
            $allTable["customer_col_tax_number"]                  =  in_array("customer_col.tax_number",$list)?true:false ;/** 2 */
            $allTable["customer_col_type"]                        =  in_array("customer_col.type",$list)?true:false ;/** 3 */
            $allTable["customer_col_status"]                      =  in_array("customer_col.status",$list)?true:false ;/** 4 */
            $allTable["customer_col_mobile"]                      =  in_array("customer_col.mobile",$list)?true:false ;/** 5 */
            $allTable["customer_col_credit_limit"]                =  in_array("customer_col.credit_limit",$list)?true:false ;/** 6 */
            $allTable["customer_col_pay_term"]                    =  in_array("customer_col.pay_term",$list)?true:false ;/** 7 */
            $allTable["customer_col_opening_balance"]             =  in_array("customer_col.opening_balance",$list)?true:false ;/** 8 */
            $allTable["customer_col_advanced_balance"]            =  in_array("customer_col.advanced_balance",$list)?true:false ;/** 9 */
            $allTable["customer_col_added_on"]                    =  in_array("customer_col.added_on",$list)?true:false ;/** 10 */
            $allTable["customer_col_customer_group"]              =  in_array("customer_col.customer_group",$list)?true:false ;/** 11 */
            $allTable["customer_col_address"]                     =  in_array("customer_col.address",$list)?true:false ;/** 12 */
            $allTable["customer_col_total_purchase_due"]          =  in_array("customer_col.total_purchase_due",$list)?true:false ;/** 13 */
            $allTable["customer_col_total_purchase_return"]       =  in_array("customer_col.total_purchase_return",$list)?true:false ;/** 14 */
            $allTable["customer_col_total_all_due"]               =  in_array("customer_col.total_all_due",$list)?true:false ;/** 15 */
            $allTable["customer_col_custom_field_1"]              =  in_array("customer_col.custom_field_1",$list)?true:false ;/** 16 */
            $allTable["customer_col_custom_field_2"]              =  in_array("customer_col.custom_field_2",$list)?true:false ;/** 17 */
            $allTable["customer_col_custom_field_3"]              =  in_array("customer_col.custom_field_3",$list)?true:false ;/** 18 */
            $allTable["customer_col_custom_field_4"]              =  in_array("customer_col.custom_field_4",$list)?true:false ;/** 19 */
            $allTable["customer_col_custom_field_5"]              =  in_array("customer_col.custom_field_5",$list)?true:false ;/** 20 */
            $allTable["customer_col_custom_field_6"]              =  in_array("customer_col.custom_field_6",$list)?true:false ;/** 21 */
            $allTable["customer_col_custom_field_7"]              =  in_array("customer_col.custom_field_7",$list)?true:false ;/** 22 */
            $allTable["customer_col_custom_field_8"]              =  in_array("customer_col.custom_field_8",$list)?true:false ;/** 23 */
            $allTable["customer_col_custom_field_9"]              =  in_array("customer_col.custom_field_9",$list)?true:false ;/** 24 */
            $allTable["customer_col_custom_field_10"]             =  in_array("customer_col.custom_field_10",$list)?true:false ;/** 25 */
            // ....................................................
            // ************************************************************************************* \\
            
            
            // ************************************************************************************* \\
            /** 3*/$allPermissions["customer_group"]              =  in_array("sidBar.CustomerGroup",$list)?true:false ;
            /** Child */
            $allChildren["customer_group_view"]                   =  in_array("customer_group.view",$list)?true:false ;
            $allChildren["customer_group_create"]                 =  in_array("customer_group.create",$list)?true:false ;
            $allChildren["customer_group_update"]                 =  in_array("customer_group.update",$list)?true:false ;
            $allChildren["customer_group_delete"]                 =  in_array("customer_group.delete",$list)?true:false ;
            /** Table action */    
            $allTableAction["customer_group_action_view"]         =  in_array("customer_group.view",$list)?true:false ;
            $allTableAction["customer_group_action_update"]       =  in_array("customer_group.update",$list)?true:false ;
            $allTableAction["customer_group_action_delete"]       =  in_array("customer_group.delete",$list)?true:false ;
            /** Table Column */    
            $allTable["customer_group_col_customer_group_name"]   =  in_array("customer_group_col.customer_group_name",$list)?true:false ;/** 1 */
            $allTable["customer_group_col_calculation_percent"]   =  in_array("customer_group_col.calculation_percent",$list)?true:false ;/** 2 */
            $allTable["customer_group_col_sale_price_group"]      =  in_array("customer_group_col.sale_price_group",$list)?true:false ;/** 3 */
            // ....................................................
            // ************************************************************************************* \\
            // ....................................................
            /** 4*/$allPermissions["import_contact"]              =  in_array("sidBar.ImportContact",$list)?true:false ;
            // ....................................................
            
            
            // ************************************************************************************* \\
            // ... Product Section   
            $allPermissions["products"]                           =  in_array("sidBar.Products",$list)?true:false ;
            /** 1*/$allPermissions["list_product"]                =  in_array("sidBar.List_Product",$list)?true:false ;
            /** Child */    
            $allChildren["product_view"]                          =  in_array("product.view",$list)?true:false ;
            $allChildren["product_view_stock"]                    =  in_array("product.view_sStock",$list)?true:false ;
            $allChildren["product_average_cost"]                  =  in_array("product.avarage_cost",$list)?true:false ;
            $allChildren["product_create"]                        =  in_array("product.create",$list)?true:false ;
            $allChildren["product_update"]                        =  in_array("product.update",$list)?true:false ;
            $allChildren["product_delete"]                        =  in_array("product.delete",$list)?true:false ;
            $allChildren["product_opening_stock"]                 =  in_array("product.opening_stock",$list)?true:false ;
            /** Table action */    
            $allTableAction["product_action_view"]                =  in_array("product.view",$list)?true:false ;
            $allTableAction["product_action_update"]              =  in_array("product.update",$list)?true:false ;
            $allTableAction["product_action_delete"]              =  in_array("product.delete",$list)?true:false ;
            $allTableAction["product_action_more_barcode"]        =  in_array("product.more_barcode",$list)?true:false ;
            $allTableAction["product_action_view_opening_stock"]  =  in_array("product.opening_stock",$list)?true:false ;
            $allTableAction["product_action_product_history"]     =  in_array("product.product_history",$list)?true:false ;
            $allTableAction["product_action_duplicate_product"]   =  in_array("product.duplicate_product",$list)?true:false ;
            /** Table Column */    
            $allTable["product_col_image"]                        =  in_array("product_col.image",$list)?true:false ;/** 1 */
            $allTable["product_col_product_name"]                 =  in_array("product_col.product_name",$list)?true:false ;/** 2 */
            $allTable["product_col_product_type"]                 =  in_array("product_col.product_type",$list)?true:false ;/** 3 */
            $allTable["product_col_business_location"]            =  in_array("product_col.business_location",$list)?true:false ;/** 4 */
            $allTable["product_col_unit_cost_price"]              =  in_array("product_col.unit_cost_price",$list)?true:false ;/** 5 */
            $allTable["product_col_unit_sale_price_exc"]          =  in_array("product_col.unit_sale_price_exc",$list)?true:false ;/** 6 */
            $allTable["product_col_unit_sale_price"]              =  in_array("product_col.unit_sale_price",$list)?true:false ;/** 7 */
            $allTable["product_col_current_stock"]                =  in_array("product_col.current_stock",$list)?true:false ;/** 8 */
            $allTable["product_col_category"]                     =  in_array("product_col.category",$list)?true:false ;/** 9 */
            $allTable["product_col_sub_category"]                 =  in_array("product_col.sub_category",$list)?true:false ;/** 10  new*/
            $allTable["product_col_brand"]                        =  in_array("product_col.brand",$list)?true:false ;/** 11 */
            $allTable["product_col_tax"]                          =  in_array("product_col.tax",$list)?true:false ;/** 12 */
            $allTable["product_col_code"]                         =  in_array("product_col.code",$list)?true:false ;/** 13 */
            $allTable["product_col_sub_code"]                     =  in_array("product_col.sub_code",$list)?true:false ;/** 14  new*/
            $allTable["product_col_created_by"]                   =  in_array("product_col.created_by",$list)?true:false ;/** 15  */
            // ....................................................
            // ************************************************************************************* \\
            
            
            // ************************************************************************************* \\
            /** 2*/$allPermissions["variations"]                  =  in_array("sidBar.Variations",$list)?true:false ;
            /** Child */    
            $allChildren["variation_view"]                        =  in_array("variation.view",$list)?true:false ;
            $allChildren["variation_create"]                      =  in_array("variation.create",$list)?true:false ;
            $allChildren["variation_update"]                      =  in_array("variation.update",$list)?true:false ;
            $allChildren["variation_delete"]                      =  in_array("variation.delete",$list)?true:false ;
            /** Table action */    
            $allTableAction["variation_action_view"]              =  in_array("variation.view",$list)?true:false ;
            $allTableAction["variation_action_update"]            =  in_array("variation.update",$list)?true:false ;
            $allTableAction["variation_action_delete"]            =  in_array("variation.delete",$list)?true:false ;
            /** Table Column */    
            $allTable["variation_col_variations"]                 =  in_array("variation_col.variations",$list)?true:false ;/** 1 */
            $allTable["variation_col_values"]                     =  in_array("variation_col.values",$list)?true:false ;/** 2 */
            // ....................................................
            // ************************************************************************************* \\
            dd($final);
            
            
            
            
            // ************************************************************************************* \\
            /** 3*/$allPermissions["opening_stock"]               =  in_array("sidBar.Add_Opening_Stock",$list)?true:false ;
            /** Child */    
            $allChildren["opening_stock_view"]                    =  in_array("opening_stock.view",$list)?true:false ;
            $allChildren["opening_stock_create"]                  =  in_array("opening_stock.create",$list)?true:false ;
            $allChildren["opening_stock_update"]                  =  in_array("opening_stock.update",$list)?true:false ;
            $allChildren["opening_stock_delete"]                  =  in_array("opening_stock.delete",$list)?true:false ;
            // ....................................................
            /** 4*/$allPermissions["import_opening_stock"]        =  in_array("sidBar.Import_Opening_Stock",$list)?true:false ;
            // ....................................................
            /** 5*/$allPermissions["sale_price_group"]            =  in_array("sidBar.Sale_Price_Group",$list)?true:false ;
            /** Child */    
            $allChildren["sale_price_group_view"]                 =  in_array("sale_price_group.view",$list)?true:false ;
            $allChildren["sale_price_group_create"]               =  in_array("sale_price_group.create",$list)?true:false ;
            $allChildren["sale_price_group_update"]               =  in_array("sale_price_group.update",$list)?true:false ;
            $allChildren["sale_price_group_delete"]               =  in_array("sale_price_group.delete",$list)?true:false ;
            // ....................................................
            /** 6*/$allPermissions["units"]                       =  in_array("sidBar.Units",$list)?true:false ;
            /** Child */    
            $allChildren["unit_view"]                             =  in_array("unit.view",$list)?true:false ;
            $allChildren["unit_create"]                           =  in_array("unit.create",$list)?true:false ;
            $allChildren["unit_update"]                           =  in_array("unit.update",$list)?true:false ;
            $allChildren["unit_delete"]                           =  in_array("unit.delete",$list)?true:false ;
            // ....................................................
            /** 7*/$allPermissions["categories"]                  =  in_array("sidBar.Categories",$list)?true:false ;
            /** Child */    
            $allChildren["category_view"]                         =  in_array("category.view",$list)?true:false ;
            $allChildren["category_create"]                       =  in_array("category.create",$list)?true:false ;
            $allChildren["category_update"]                       =  in_array("category.update",$list)?true:false ;
            $allChildren["category_delete"]                       =  in_array("category.delete",$list)?true:false ;
            // ....................................................
            /** 8*/$allPermissions["brands"]                      =  in_array("sidBar.Brands",$list)?true:false ;
            /** Child */
            $allChildren["brand_view"]                            =  in_array("brand.view",$list)?true:false ;
            $allChildren["brand_create"]                          =  in_array("brand.create",$list)?true:false ;
            $allChildren["brand_update"]                          =  in_array("brand.update",$list)?true:false ;
            $allChildren["brand_delete"]                          =  in_array("brand.delete",$list)?true:false ;
            // ....................................................
            /** 9*/$allPermissions["warranties"]                  =  in_array("sidBar.Warranties",$list)?true:false ;
            /** Child */    
            $allChildren["warranty_view"]                         =  in_array("warranty.view",$list)?true:false ;
            $allChildren["warranty_create"]                       =  in_array("warranty.create",$list)?true:false ;
            $allChildren["warranty_update"]                       =  in_array("warranty.update",$list)?true:false ;
            $allChildren["warranty_delete"]                       =  in_array("warranty.delete",$list)?true:false ;



            // ... Inventory Report   
            $allPermissions["inventory"]                          =  in_array("sidBar.Inventory",$list)?true:false ;
            $allPermissions["product_gallery"]                    =  in_array("sidBar.Product_Gallery",$list)?true:false ;
            $allPermissions["inventory_report"]                   =  in_array("sidBar.Inventory_Report",$list)?true:false ;
            $allPermissions["inventory_of_warehouse"]             =  in_array("sidBar.Inventory_Of_Warehouse",$list)?true:false ;
            // ... Manufacturing       
            $allPermissions["manufacturing"]                      =  in_array("sidBar.Manufacturing",$list)?true:false ;
            $allPermissions["recipe"]                             =  in_array("sidBar.Recipe",$list)?true:false ;
            $allPermissions["production"]                         =  in_array("sidBar.Production",$list)?true:false ;
            $allPermissions["manufacturing_report"]               =  in_array("sidBar.Manufacturing_Report",$list)?true:false ;
            // ... Purchase Section       
            $allPermissions["purchases"]                          =  in_array("sidBar.Purchases",$list)?true:false ;
            $allPermissions["list_purchases"]                     =  in_array("sidBar.List_Purchases",$list)?true:false ;
            $allPermissions["list_return_purchases"]              =  in_array("sidBar.List_Return_Purchases",$list)?true:false ;
            $allPermissions["map"]                                =  in_array("sidBar.Map",$list)?true:false ;
            // ... Sales Section        
            $allPermissions["sales"]                              =  in_array("sidBar.Sales",$list)?true:false ;
            $allPermissions["list_sales"]                         =  in_array("sidBar.List_Sales",$list)?true:false ;
            $allPermissions["list_approved_quotation"]            =  in_array("sidBar.List_Approved_Quotation",$list)?true:false ;
            $allPermissions["list_quotation"]                     =  in_array("sidBar.List_Quotation",$list)?true:false ;
            $allPermissions["list_sale_return"]                   =  in_array("sidBar.List_Sale_Return",$list)?true:false ;
            $allPermissions["sales_commission_agent"]             =  in_array("sidBar.Sales_Commission_Agent",$list)?true:false ;
            $allPermissions["import_sales"]                       =  in_array("sidBar.ImportSales",$list)?true:false ;
            $allPermissions["quotation_terms"]                    =  in_array("sidBar.Quotation_Terms",$list)?true:false ;
            // ... Voucher Section       
            $allPermissions["vouchers"]                           =  in_array("sidBar.Vouchers",$list)?true:false ;
            $allPermissions["list_vouchers"]                      =  in_array("sidBar.List_Vouchers",$list)?true:false ;
            $allPermissions["add_receipt_voucher"]                =  in_array("sidBar.Add_Receipt_Voucher",$list)?true:false ;
            $allPermissions["add_payment_voucher"]                =  in_array("sidBar.Add_Payment_Voucher",$list)?true:false ;
            $allPermissions["list_journal_voucher"]               =  in_array("sidBar.List_Journal_Voucher",$list)?true:false ;
            $allPermissions["list_expense_voucher"]               =  in_array("sidBar.List_Expense_Voucher",$list)?true:false ;
            // ... Cheque Section       
            $allPermissions["vouchers"]                           =  in_array("sidBar.Cheques",$list)?true:false ;
            $allPermissions["list_vouchers"]                      =  in_array("sidBar.List_Cheque",$list)?true:false ;
            $allPermissions["add_receipt_voucher"]                =  in_array("sidBar.Add_Cheque_In",$list)?true:false ;
            $allPermissions["add_payment_voucher"]                =  in_array("sidBar.Add_Cheque_Out",$list)?true:false ;
            $allPermissions["list_journal_voucher"]               =  in_array("sidBar.Contact_Bank",$list)?true:false ;
            // ... Cash & Bank Section       
            $allPermissions["cash_and_bank"]                      =  in_array("sidBar.Cash_And_Bank",$list)?true:false ;
            $allPermissions["list_cash"]                          =  in_array("sidBar.List_Cash",$list)?true:false ;
            $allPermissions["list_bank"]                          =  in_array("sidBar.List_Bank",$list)?true:false ;
            // ... Account Section            
            $allPermissions["accounts"]                           =  in_array("sidBar.Accounts",$list)?true:false ;
            $allPermissions["list_account"]                       =  in_array("sidBar.List_Account",$list)?true:false ;
            $allPermissions["balance_sheet"]                      =  in_array("sidBar.Balance_Sheet",$list)?true:false ;
            $allPermissions["trial_balance"]                      =  in_array("sidBar.Trial_Balance",$list)?true:false ;
            $allPermissions["cash_flow"]                          =  in_array("sidBar.Cash_Flow",$list)?true:false ;
            $allPermissions["payment_account_report"]             =  in_array("sidBar.Payment_Account_Report",$list)?true:false ;
            $allPermissions["list_entries"]                       =  in_array("sidBar.List_Entries",$list)?true:false ;
            $allPermissions["cost_center"]                        =  in_array("sidBar.Cost_Center",$list)?true:false ;
            // ... Warehouse Section       
            $allPermissions["warehouses"]                         =  in_array("sidBar.Warehouses",$list)?true:false ;
            $allPermissions["list_warehouses"]                    =  in_array("sidBar.List_Warehouses",$list)?true:false ;
            $allPermissions["warehouses_movement"]                =  in_array("sidBar.Warehouses_Movement",$list)?true:false ;
            $allPermissions["warehouse_transfer"]                 =  in_array("sidBar.Warehouse_Transafer",$list)?true:false ;
            $allPermissions["list_warehouse_transfer"]            =  in_array("sidBar.List_Warehouse_transfer",$list)?true:false ;
            $allPermissions["add_warehouse_transfer"]             =  in_array("sidBar.Add_Warehouse_Transfer",$list)?true:false ;
            $allPermissions["warehouse_transfer"]                 =  in_array("sidBar.Warehouse_Transafer",$list)?true:false ;
            $allPermissions["delivered"]                          =  in_array("sidBar.Delivered",$list)?true:false ;
            $allPermissions["received"]                           =  in_array("sidBar.Received",$list)?true:false ;
            // ... Reports Section       
            $allPermissions["reports"]                            =  in_array("sidBar.Reports",$list)?true:false ;
            $allPermissions["profit_and_loss_report"]             =  in_array("sidBar.Profit_And_Loss_Report",$list)?true:false ;
            $allPermissions["daily_product_sale_report"]          =  in_array("sidBar.Daily_Product_Sale_Report",$list)?true:false ;
            $allPermissions["purchase_and_sale_report"]           =  in_array("sidBar.Purchase_And_Sale_Report",$list)?true:false ;
            $allPermissions["tax_reports"]                        =  in_array("sidBar.Tax_Reports",$list)?true:false ;
            $allPermissions["suppliers_and_customers_report"]     =  in_array("sidBar.Suppliers_And_Customers_Report",$list)?true:false ;
            $allPermissions["customers_group_report"]             =  in_array("sidBar.Customers_Group_Report",$list)?true:false ;
            $allPermissions["stock_adjustment_report"]            =  in_array("sidBar.Stock_Adjustment_Report",$list)?true:false ;
            $allPermissions["trending_products_report"]           =  in_array("sidBar.Trending_Products_Report",$list)?true:false ;
            $allPermissions["items_report"]                       =  in_array("sidBar.Items_Report",$list)?true:false ;
            $allPermissions["product_purchase_report"]            =  in_array("sidBar.Product_Purchase_Report",$list)?true:false ;
            $allPermissions["sale_payment_report"]                =  in_array("sidBar.Sale_Payment_Report",$list)?true:false ;
            $allPermissions["report_setting"]                     =  in_array("sidBar.Report_Setting",$list)?true:false ;
            $allPermissions["expense_report"]                     =  in_array("sidBar.Expense_Report",$list)?true:false ;
            $allPermissions["register_report"]                    =  in_array("sidBar.Register_Report",$list)?true:false ;
            $allPermissions["sales_representative_report"]        =  in_array("sidBar.Sales_Representative_Report",$list)?true:false ;
            $allPermissions["activity_log"]                       =  in_array("sidBar.Activity_Log",$list)?true:false ;
            // ... Pattern Section    
            $allPermissions["patterns"]                           =  in_array("sidBar.Patterns",$list)?true:false ;
            $allPermissions["business_locations"]                 =  in_array("sidBar.Business_locations",$list)?true:false ;
            $allPermissions["define_patterns"]                    =  in_array("sidBar.Define_Patterns",$list)?true:false ;
            $allPermissions["system_accounts"]                    =  in_array("sidBar.System_Accounts",$list)?true:false ;
            // ... Setting Section    
            $allPermissions["settings"]                           =  in_array("sidBar.Settings",$list)?true:false ;
            $allPermissions["invoice_settings"]                   =  in_array("sidBar.Invoice_Settings",$list)?true:false ;
            $allPermissions["barcode_settings"]                   =  in_array("sidBar.Barcode_Settings",$list)?true:false ;
            $allPermissions["product_settings"]                   =  in_array("sidBar.Product_Settings",$list)?true:false ;
            $allPermissions["receipt_printer"]                    =  in_array("sidBar.Receipt_Printer",$list)?true:false ;
            $allPermissions["tax_rates"]                          =  in_array("sidBar.Tax_Rates",$list)?true:false ;
            $allPermissions["type_of_service"]                    =  in_array("sidBar.Type_Of_Service",$list)?true:false ;
            $allPermissions["delete_service"]                     =  in_array("sidBar.Delete_Service",$list)?true:false ;
            $allPermissions["package_subscription"]               =  in_array("sidBar.Package_Subscription",$list)?true:false ;
            // ... log file    
            $allPermissions["log_file"]                           =  in_array("sidBar.LogFile",$list)?true:false ;
            $allPermissions["log_warranties"]                     =  in_array("sidBar.logWarranties",$list)?true:false ;
            $allPermissions["log_users"]                          =  in_array("sidBar.logUsers",$list)?true:false ;
            $allPermissions["log_bill"]                           =  in_array("sidBar.logBill",$list)?true:false ;
            // ... User Activation    
            $allPermissions["user_activation"]                    =  in_array("sidBar.User_Activation",$list)?true:false ;
            $allPermissions["list_of_users"]                      =  in_array("sidBar.List_Of_Users",$list)?true:false ;
            $allPermissions["list_of_user_request"]               =  in_array("sidBar.List_Of_User_Request",$list)?true:false ;
            $allPermissions["Create_New_User"]                    =  in_array("sidBar.Create_New_User",$list)?true:false ;
            // ... Mobile Section    
            $allPermissions["mobile_section"]                     =  in_array("sidBar.Mobile_Section",$list)?true:false ;
            // ... React Section    
            $allPermissions["react_section"]                      =  in_array("sidBar.React_section",$list)?true:false ;
            // ... E_commerce Section    
            $allPermissions["e_commerce"]                         =  in_array("sidBar.E_commerce",$list)?true:false ;
            


            return $permissions;   
        }
    // *************************
}
