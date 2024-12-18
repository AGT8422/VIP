<?php

namespace App\Http\Controllers\ApiController\FrontEnd;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FrontEnd\Products\SalePriceGroup;
use Excel;
use App\Utils\Util;
use DB;
class SalesPriceGroupController extends Controller
{
    // 1 index
    public function SalesPriceGroup(Request $request) {
        try{
            $main_token   = $request->header("Authorization");
            $token        = substr($main_token,7);
            if($token == "" || $token == null){
                return response([
                    "status"  => 403,
                    "message" => "Invalid Token"
                ],403);
            }
            $user      = \App\User::where("api_token",$token)->first();
            if(!$user){
                return response()->json([
                    "status"   => 403,
                    "message"  => " Unauthorized action ",
                ],403);
            } 
            $salesPriceGroup    = SalePriceGroup::getSalesPriceGroup($user);
            if($salesPriceGroup == false){
                return response()->json([
                    "status"   => 403,
                    "message"  => " No Have Sales Price Groups ",
                ],403);
            }
            return response([
                "status"   => 200,
                "value"    => $salesPriceGroup,
                "message"  => "Sales Price Groups Access Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"  => 403,
                "message" => "Failed Action Error 403",
            ],403);
        }    
    }
    // 2 create
    public function SalesPriceGroupCreate(Request $request) {
        try{
            $main_token   = $request->header("Authorization");
            $token        = substr($main_token,7);
            if($token == "" || $token == null){
                return response([
                    "status"  => 403,
                    "message" => "Invalid Token"
                ],403);
            }
            $user      = \App\User::where("api_token",$token)->first();
            if(!$user){
                return response()->json([
                    "status"   => 403,
                    "message"  => " Unauthorized action ",
                ],403);
            } 
            $data = [];
            // *******************************************************START**
            $create    = SalePriceGroup::createSalesPriceGroup($user,$data);
            // *********************************************************END**
            if($create == false){
                return response()->json([
                    "status"   => 403,
                    "message"  => " No Have Sales Price Groups  ",
                ],403);
            }
            return response([
                "status"   => 200,
                "value"    => $create,
                "message"  => "Create Sales Price Groups Access Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"  => 403,
                "message" => "Failed Action Error 403",
            ],403);
        }     
    }
    // 3 Edit
    public function SalesPriceGroupEdit(Request $request,$id) {
        try{
            $main_token   = $request->header("Authorization");
            $token        = substr($main_token,7);
            if($token == "" || $token == null){
                return response([
                    "status"  => 403,
                    "message" => "Invalid Token"
                ],403);
            }
            $user      = \App\User::where("api_token",$token)->first();
            if(!$user){
                return response()->json([
                    "status"   => 403,
                    "message"  => " Unauthorized action ",
                ],403);
            } 
            $data = [];
            // *******************************************************START**
            $edit    = SalePriceGroup::editSalesPriceGroup($user,$data,$id);
            // *********************************************************END**
            if($edit == false){
                return response()->json([
                    "status"   => 403,
                    "message"  => " No Have Sales Price Groups ",
                ],403);
            }
            return response([
                "status"    => 200,
                "value"     => $edit,
                "message"   => "Edit Sales Price Groups Access Successfully",
            ]);
        }catch(Exception $e){   
            return response([
                "status"  => 403,
                "message" => "Failed Action Error 403",
            ],403);
        }    
    }
    // 4 Store
    public function SalesPriceGroupStore(Request $request) {
        try{
            $main_token   = $request->header("Authorization");
            $token        = substr($main_token,7);
            if($token == "" || $token == null){
                return response([
                    "status"  => 403,
                    "message" => "Invalid Token"
                ],403);
            }
            $user      = \App\User::where("api_token",$token)->first();
            if(!$user){
                return response()->json([
                    "status"   => 403,
                    "message"  => " Unauthorized action ",
                ],403);
            } 
            \DB::beginTransaction();
            // **********************************************************START**
                $data      = $request->only([ "name", "description" ]);
                $save      = SalePriceGroup::storeSalesPriceGroup($user,$data);
                if($save == false){
                    return response()->json([
                        "status"   => 403,
                        "message"  => " Failed Action ",
                    ],403);
                }
            // ***********************************************************END**
            \DB::commit();
            return response([
                "status"   => 200,
                "message" => "Added Sales Price Groups Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"  => 403,
                "message" => "Failed Action Error 403",
            ],403);
        }
    }
    // 5 Update
    public function SalesPriceGroupUpdate(Request $request,$id) {
        try{
            $main_token   = $request->header("Authorization");
            $token        = substr($main_token,7);
            $data         = $request->only(["type"]);
            if($token == "" || $token == null){
                return response([
                    "status"  => 403,
                    "message" => "Invalid Token"
                ],403);
            }
            $user      = \App\User::where("api_token",$token)->first();
            if(!$user){
                return response()->json([
                    "status"   => 403,
                    "message"  => " Unauthorized action ",
                ],403);
            } 
            \DB::beginTransaction();
            // **********************************************************START**
            $data      = $request->only([ "name","description"  ]);
            $update    = SalePriceGroup::updateSalesPriceGroup($user,$data,$id);
            if($update == false){
                return response()->json([
                    "status"   => 403,
                    "message"  => " No Have Sales Price Groups ",
                ],403);
            }
            // ************************************************************END**
            \DB::commit();
            return response([
                "status"   => 200,
                "message" => "Updated Sales Price Groups Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"  => 403,
                "message" => "Failed Action Error 403",
            ],403);
        }
    }
    // 6 Delete
    public function SalesPriceGroupDelete(Request $request,$id) {
        try{
            $main_token   = $request->header("Authorization");
            $token        = substr($main_token,7);
            $type         = $request->input("type");
            if($token == "" || $token == null){
                return response([
                    "status"  => 403,
                    "message" => "Invalid Token"
                ],403);
            }
            $user      = \App\User::where("api_token",$token)->first();
            if(!$user){
                return response()->json([
                    "status"   => 403,
                    "message"  => " Unauthorized action ",
                ],403);
            } 
            // *************************************************START**
            $del    = SalePriceGroup::deleteSalesPriceGroup($user,$id);
            // ****************************************************END**
            if($del == false){
                return response()->json([
                    "status"   => 403,
                    "message"  => " No Have Sales Price Groups ",
                ],403);
            }
            return response([
                "status"   => 200,
                "message" => "Deleted Sales Price Groups Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"  => 403,
                "message" => "Failed Action Error 403",
            ],403);
        }
    }
    // 6 Active
    public function SalesPriceGroupActive(Request $request,$id) {
        try{
            $main_token   = $request->header("Authorization");
            $token        = substr($main_token,7);
            $type         = [];
            if($token == "" || $token == null){
                return response([
                    "status"  => 403,
                    "message" => "Invalid Token"
                ],403);
            }
            $user      = \App\User::where("api_token",$token)->first();
            if(!$user){
                return response()->json([
                    "status"   => 403,
                    "message"  => " Unauthorized action ",
                ],403);
            } 
            // *************************************************START**
            $active    = SalePriceGroup::activeSalesPriceGroup($user,$id);
            // ****************************************************END**
            if($active["status"] == false){
                return response()->json([
                    "status"   => 403,
                    "message"  => $active["msg"],
                ],403);
            }else{
                return response([
                    "status"   => 200,
                    "message"  => $active["msg"],
                ]);
            }
        }catch(Exception $e){
            return response([
                "status"  => 403,
                "message" => "Failed Action Error 403",
            ],403);
        }
    }
    // 7 export
    public function export(Request $request) {
        try{
            $main_token   = $request->header("Authorization");
            $token        = substr($main_token,7);
            $type         = [];
            if($token == "" || $token == null){
                return response([
                    "status"  => 403,
                    "message" => "Invalid Token"
                ],403);
            }
            $user      = \App\User::where("api_token",$token)->first();
            if(!$user){
                return response()->json([
                    "status"   => 403,
                    "message"  => " Unauthorized action ",
                ],403);
            } 
            // **********************************START**
            $business_id  = $user->business_id;
            $price_groups = \App\SellingPriceGroup::where('business_id', $business_id)->active()->get();
    
            $variations   = \App\Variation::join('products as p', 'variations.product_id', '=', 'p.id')
                                        ->join('product_variations as pv', 'variations.product_variation_id', '=', 'pv.id')
                                        ->where('p.business_id', $business_id)
                                        ->whereIn('p.type', ['single', 'variable'])
                                        ->select('sub_sku', 'p.name as product_name', 'variations.name as variation_name', 'p.type', 'variations.id', 'pv.name as product_variation_name', 'sell_price_inc_tax')
                                        ->with(['group_prices'])
                                        ->get();
            $export_data  = [];
            foreach ($variations as $variation) {
                $temp                       = [];
                $temp['product']            = $variation->type == 'single' ? $variation->product_name : $variation->product_name . ' - ' . $variation->product_variation_name . ' - ' . $variation->variation_name;
                $temp['sku']                = $variation->sub_sku;
                $temp['Base Selling Price'] = $variation->sell_price_inc_tax;
    
                foreach ($price_groups as $price_group) {
                    $price_group_id = $price_group->id;
                    $variation_pg   = $variation->group_prices->filter(function ($item) use ($price_group_id) {
                        return $item->price_group_id == $price_group_id;
                    });
                    
                    $temp[$price_group->name] = $variation_pg->isNotEmpty() ? $variation_pg->first()->price_inc_tax : '';
                }
                $export_data[] = $temp;
            }
    
            if (ob_get_contents()) ob_end_clean();
            ob_start();
            return collect($export_data)->downloadExcel(
                'product_group_prices.xlsx',
                null,
                true
            );
            // ************************************END**
            
        }catch(Exception $e){
            return response([
                "status"  => 403,
                "message" => "Failed Action Error 403",
            ],403);
        }
    }
 
    // 8 import
    public function import(Request $request) {
        try{
            $main_token   = $request->header("Authorization");
            $token        = substr($main_token,7);
            $type         = [];
            if($token == "" || $token == null){
                return response([
                    "status"  => 403,
                    "message" => "Invalid Token"
                ],403);
            }
            $user      = \App\User::where("api_token",$token)->first();
            if(!$user){
                return response()->json([
                    "status"   => 403,
                    "message"  => " Unauthorized action ",
                ],403);
            } 
            // **********************************START**
                DB::beginTransaction();
                $commonUtil = new Util();
                $notAllowed = $commonUtil->notAllowedInDemo();
                if (!empty($notAllowed)) {
                    return $notAllowed;
                }
                //Set maximum php execution time
                ini_set('max_execution_time', 0);
                ini_set('memory_limit', -1);
    
                if ($request->hasFile('product_group_prices')) {
                    $file = $request->file('product_group_prices');
                    
                    $parsed_array = Excel::toArray([], $file);
    
                    $headers      = $parsed_array[0][0];
    
                    //Remove header row
                    $imported_data = array_splice($parsed_array[0], 1);
    
                    $business_id  = $user->business_id;
                    $price_groups = \App\SellingPriceGroup::where('business_id', $business_id)->active()->get();
    
                    //Get price group names from headers
                    $imported_pgs = [];
                    foreach ($headers as $key => $value) {
                        if (!empty($value) && $key > 2) {
                            $imported_pgs[$key] = $value;
                        }
                    }
    
                    $error_msg = '';
                    
                    foreach ($imported_data as $key => $value) {
                        $variation = \App\Variation::where('sub_sku', $value[1])
                                            ->first();
                        if (empty($variation)) {
                            $row = $key + 1;
                            $error_msg = __('lang_v1.product_not_found_exception', ['sku' => $value[1], 'row' => $row]);
    
                            return response([
                                "status"  => 403,
                                "message" => $error_msg,
                            ],403);
                        }
    
                        foreach ($imported_pgs as $k => $v) {
                            $price_group = $price_groups->filter(function ($item) use ($v) {
                                return response([
                                    "status"  => 405,
                                    "message" => strtolower($item->name) == strtolower($v),
                                ],405);
                            });
                           
                            if ($price_group->isNotEmpty()) {
                                //Check if price is numeric
                                if (!is_null($value[$k]) && !is_numeric($value[$k])) {
                                    $row = $key + 1;
                                    $error_msg = __('lang_v1.price_group_non_numeric_exception', ['row' => $row]);
    
                                    return response([
                                        "status"  => 403,
                                        "message" => $error_msg,
                                    ],403);
                                }
    
                                if (!is_null($value[$k])) {
                                    \App\VariationGroupPrice::updateOrCreate(
                                        ['variation_id' => $variation->id,
                                        'price_group_id' => $price_group->first()->id
                                        ],
                                        ['price_inc_tax' => $value[$k]
                                    ]
                                    );
                                }
                            } else {
                                $row = $key + 1;
                                $error_msg = __('lang_v1.price_group_not_found_exception', ['pg' => $v, 'row' => $row]);
    
                                return response([
                                    "status"  => 403,
                                    "message" => $error_msg,
                                ],403);
                            }
                        }
                    }
                    
                }
            // ************************************END**
            
        }catch(Exception $e){
            return response([
                "status"  => 403,
                "message" => "Failed Action Error 403",
            ],403);
        }
    }
}
