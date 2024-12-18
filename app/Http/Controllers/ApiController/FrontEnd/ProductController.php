<?php

namespace App\Http\Controllers\ApiController\FrontEnd;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FrontEnd\Products\Product;
use App\Models\FrontEnd\Products\Unit;
use App\Models\FrontEnd\Utils\SearchProduct;
use App\Models\FrontEnd\Utils\SelectProduct;
class ProductController extends Controller
{
    // 1 index
    public function Product(Request $request) {
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
            $product    = Product::getProduct($user,$request);
            if($product == false){
                return response()->json([
                    "status"   => 403,
                    "message"  => " No Have Products ",
                ],403);
            }
            return response([
                "status"   => 200,
                "value"    => $product,
                "message"  => "Products Access Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"  => 403,
                "message" => "Failed Action Error 403",
            ],403);
        }    
    }
    // 2 create
    public function ProductCreate(Request $request) {
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
            // *********************************START**
            $create    = Product::createProduct($user);
            // ***********************************END**
            if($create == false){
                return response()->json([
                    "status"   => 403,
                    "message"  => " No Have Products  ",
                ],403);
            }
            return response([
                "status"   => 200,
                "value"    => $create,
                "message"  => "Create Products Access Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"  => 403,
                "message" => "Failed Action Error 403",
            ],403);
        }   
    }
    // 3 Edit
    public function ProductEdit(Request $request,$id) {
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
            // **************************************START**
            $edit    = Product::editProduct($user,$id);
            // ****************************************END**
            if($edit == false){
                return response()->json([
                    "status"   => 403,
                    "message"  => " No Have Products ",
                ],403);
            }
            return response([
                "status"    => 200,
                "value"     => $edit,
                "message"   => "Edit Products Access Successfully",
            ]);
        }catch(Exception $e){   
            return response([
                "status"  => 403,
                "message" => "Failed Action Error 403",
            ],403);
        }   
    }
    // 4 Store
    public function ProductStore(Request $request) {
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
            // ************************************************************************************************START**
                $data                =  $request->only([
                                            "name","code","code2","barcode_type","expiry_period","expiry_period_type",
                                            "unit_id","brand_id","category_id","sub_category_id","sub_unit_ids",
                                            "enable_stock","alert_quantity","warranty_id","description","more_image",
                                            "not_for_sale","tax_id","full_description","weight","custom_field_1",
                                            "custom_field_2","custom_field_3","custom_field_4","product_type",
                                            "product_price","table_price_1","table_price_2","table_price_3"
                                        ]);
                 

                $data["name"]        = trim($data["name"]);
                $data["code"]        = trim($data["code"]);
                $data["code2"]       = trim($data["code2"]);
                if(  ( $data["name"] == "" || $data["name"] ==   null ) ||  ( $data["code"] == "" || $data["code"] ==   null )  ||  ( $data["code2"] == "" || $data["code2"] ==   null )  ){
                    return response()->json([
                        "status"   => 403,
                        "message"  => " Failed Action "
                    ],403);
                }
                $save                = Product::storeProduct($user,$data,$request);
                if($save == "Failed"){
                    return response()->json([
                        "status"   => 403,
                        "message"  => " Failed Action "
                    ],403);
                }elseif($save != "Success"){
                    return response([
                        "status"   => 403,
                        "message" => $save
                    ],403);
                }else{
                    \DB::commit();
                    return response([
                        "status"   => 200,
                        "message"  => "Add Product Successfully"
                    ],200);
                }
            // ***************************************************************************************************END**
        }catch(Exception $e){
            return response([
                "status"  => 403,
                "message" => "Failed Action Error 403"
            ],403);
        } 
    }
    // 5 Update
    public function ProductUpdate(Request $request,$id) {
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
            // ******************************************************************************************START**
            $data                = $request->only([
                                    "name","code","code2","barcode_type","expiry_period","expiry_period_type",
                                    "unit_id","brand_id","category_id","sub_category_id","sub_unit_ids",
                                    "enable_stock","alert_quantity","warranty_id","description","more_image",
                                    "not_for_sale","tax_id","full_description","weight","custom_field_1",
                                    "custom_field_2","custom_field_3","custom_field_4","product_type",
                                    "product_price","table_price_1","table_price_2","table_price_3"]);
            $data["name"]        = trim($data["name"]);
            $data["code"]        = trim($data["code"]);
            $data["code2"]       = trim($data["code2"]);
            $update              = Product::updateProduct($user,$data,$request,$id);
            if($update == "Failed"){
                    return response()->json([
                        "status"   => 403,
                        "message"  => " Failed Action "
                    ],403);
                }elseif($update != "Success"){
                    return response([
                        "status"   => 403,
                        "message" => $update
                    ],403);
                }else{
                    \DB::commit();
                    return response([
                        "status"   => 200,
                        "message"  => "Updated Product Successfully"
                    ],200);
                }
            // *********************************************************************************************END**
        }catch(Exception $e){
            return response([
                "status"  => 403,
                "message" => "Failed Action Error 403",
            ],403);
        }
    }
    // 6 Delete
    public function ProductDelete(Request $request,$id) {
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
            // *********************************START**
            $del    = Product::deleteProduct($user,$id);
            // ***********************************END**
            if($del == "notFound"){
                return response()->json([
                    "status"   => 403,
                    "message"  => " No Have Products ",
                ],403);
            }else if($del == "related"){
                return response()->json([
                    "status"   => 403,
                    "message"  => "Can't Delete, There are movements on this product ",
                ],403);
            }else{
                return response([
                    "status"   => 200,
                    "message" => "Deleted Products Successfully",
                ]);
            } 
        }catch(Exception $e){
            return response([
                "status"  => 403,
                "message" => "Failed Action Error 403",
            ],403);
        }
    }
    // 7 Gallery
    public function ProductGallery(Request $request) {
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
            // *********************************START**
            $del    = Product::ProductGallery($user,$request);
            // ***********************************END**
            if($del == false){
                return response()->json([
                    "status"   => 403,
                    "message"  => " No Have Products ",
                ],403);
            }else{
                return response([
                    "status"   => 200,
                    "list"     => $del,
                    "message" => " Products Gallery Successfully",
                ]);
            } 
        }catch(Exception $e){
            return response([
                "status"  => 403,
                "message" => "Failed Action Error 403",
            ],403);
        }
    }
    // 8 Inventory
    public function InventoryReport(Request $request) {
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
            // *********************************START**
            $del    = Product::InventoryReport($user,$request);
            // ***********************************END**
            if($del == false){
                return response()->json([
                    "status"   => 403,
                    "message"  => " No Have Products ",
                ],403);
            }else{
                return response([
                    "status"   => 200,
                    "list"     => $del,
                    "message" => "Inventory Report Successfully",
                ]);
            } 
        }catch(Exception $e){
            return response([
                "status"  => 403,
                "message" => "Failed Action Error 403",
            ],403);
        }
    }
    // 9 view
    public function ProductView(Request $request,$id) {
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
            // *********************************START**
            $view    = Product::viewProduct($user,$id);
            // ***********************************END**
            if($view == false){
                return response()->json([
                    "status"   => 403,
                    "message"  => " No Have Products  ",
                ],403);
            }
            return response([
                "status"   => 200,
                "value"    => $view,
                "message"  => "View Stock Products Access Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"  => 403,
                "message" => "Failed Action Error 403",
            ],403);
        }   
    }
    // 10 delete
    public function ProductMediaDelete(Request $request,$id) {
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
            // **************************************START**
            $del    = Product::deleteMediaProduct($user,$id);
            // ****************************************END**
            if($del == false){
                return response()->json([
                    "status"   => 403,
                    "message"  => " No Have Products Image ",
                ],403);
            }
            return response([
                "status"   => 200,
                "value"    => $del,
                "message"  => "One Products Image Deleted Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"  => 403,
                "message" => "Failed Action Error 403",
            ],403);
        }   
    }
    // 11 Search Product 
    public function SearchProduct(Request $request){
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
            $data       = $request->only(["value"]);
            // **********************************************START**
            $product    = SearchProduct::product($user,$data);
            // ************************************************END**
            if($product=="[]"){
                    return response()->json([
                        "status"   => 200,
                        "info"     => [],
                        "message"  => __("Not Found  !!"),
                    ],200);
            }
            return response([
                "status"   => 200,
                "info"     => json_decode($product),
                "message" => "Access Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"  => 403,
                "message" => "Failed Action Error 403",
            ],403);
        }
    }
    // 12 Select Product 
    public function SelectProduct(Request $request){
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
            $data       = $request->only(["id"]);
            // **********************************************START**
            $product    = SelectProduct::product($data["id"]);
            // ************************************************END**
            if($product=="[]"){
                    return response()->json([
                        "status"   => 403,
                        "message"  => __("Not Found  !!"),
                    ],403);
            }
            return response([
                "status"   => 200,
                "info"     => $product,
                "message" => "Access Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"  => 403,
                "message" => "Failed Action Error 403",
            ],403);
        }
    }    
    
    // ** actions ** //
    // 1 create units
    public function UnitCreate(Request $request) {
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
            // *********************************START**
            $create    = Unit::createUnit($user,$data);
            // ***********************************END**
            if($create == false){
                return response()->json([
                    "status"   => 403,
                    "message"  => " No Have Units  ",
                ],403);
            }
            return response([
                "status"   => 200,
                "value"    => $create,
                "message"  => "Create Units Access Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"  => 403,
                "message" => "Failed Action Error 403",
            ],403);
        }  
    }
    // 2 store units
    public function UnitStore(Request $request) {
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
            // ********************************************START**
                $data         = $request->only([
                                    "name","short_name",
                                    "allow_decimal","multiple_unit",
                                    "sub_qty","parent_unit"
                ]);
                $save      = Unit::storeUnit($user,$data,$request);
                if($save == false){
                    return response()->json([
                        "status"   => 403,
                        "message"  => " Failed Action ",
                    ],403);
                }
            // **********************************************END**
            \DB::commit();
            return response([
                "status"   => 200,
                "message" => "Added Units Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"  => 403,
                "message" => "Failed Action Error 403",
            ],403);
        }
    }
    // 3 export product   
    public function exportFile(Request $request) {
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
            $data       = [];
            // *********************************************START**
            $product    = Product::productExport($user,$data,$request);
            // ***********************************************END**
            if($product=="[]"){
                return response()->json([
                    "status"   => 403,
                    "message"  => __("Not Found  !!"),
                ],403);
            }
            return response([
                "status"   => 200,
                "info"     => $product ,
                "message" => "Access Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"  => 403,
                "message" => "Failed Action Error 403",
            ],403);
        }
    }
    // 4 iMPORT product
    public function importFile(Request $request) {
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
            $data       = [];
            // *************************************************START**
            $product    = Product::productImport($user,$data,$request);
            // ***************************************************END**
            if($product==false){
                return response()->json([
                    "status"   => 403,
                    "message"  => __("Not Found  !!"),
                ],403);
            }
            return response([
                "status"   => 200,
                "message" => "Import Opening Stock Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"  => 403,
                "message" => "Failed Action Error 403",
            ],403);
        }
    }
    // 5 movement product
    public function itemMove(Request $request,$id) {
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
            $data       = [];
            // *************************************************START**
            $product    = Product::productItemMove($user,$data,$id);
            // ***************************************************END**
            if($product==false){
                return response()->json([
                    "status"   => 403,
                    "message"  => __("Not Found  !!"),
                ],403);
            }
            return response([
                "status"     => 200,
                "movement"   => $product,
                "message"    => "Item Movement Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"  => 403,
                "message" => "Failed Action Error 403",
            ],403);
        }
    }
    
    
}
