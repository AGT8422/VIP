<?php

namespace App\Http\Controllers\ApiController\FrontEnd;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FrontEnd\Purchases\Purchase;
use App\Models\FrontEnd\Utils\SearchProduct;
use App\Models\FrontEnd\Utils\SelectProduct;

class PurchaseController extends Controller
{
    // 01 index
    public function Purchase(Request $request) {
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
            $purchase    = Purchase::getPurchase($user,$request);
            if($purchase == false){
                return response()->json([
                    "status"   => 200,
                    "error"    => true,
                    "message"  => " No Have Purchases ",
                ],200);
            }
            return response([
                "status"   => 200,
                "value"    => $purchase,
                "error"    => false,
                "message"  => "Purchases Access Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"  => 200,
                "error"   => true,
                "message" => "Failed Action Error 403",
            ],200);
        }  
    }
    // 02 create
    public function PurchaseCreate(Request $request) {
        try{
            $main_token   = $request->header("Authorization");
            $token        = substr($main_token,7);
            $data         = [];
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
            // *****************************************START**
            $create    = Purchase::createPurchase($user,$data);
            // *******************************************END**
            if($create == false){
                return response()->json([
                    "status"   => 200,
                    "error"    => true,
                    "message"  => " There is error  ",
                ],200);
            }
            return response([
                "status"   => 200,
                "value"    => $create,
                "error"    => false,
                "message"  => "Create Purchases Access Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"  => 200,
                "error"   => true,
                "message" => "Failed Action Error 403",
            ],200);
        }     
    }
    // 03 Edit
    public function PurchaseEdit(Request $request,$id) {
        try{
            $main_token   = $request->header("Authorization");
            $token        = substr($main_token,7);
            $data         = $request->only([]);
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
            // ****************************************START**
            $edit    = Purchase::editPurchase($user,$data,$id);
            // ******************************************END**
            if($edit == false){
                return response()->json([
                    "status"   => 200,
                    "error"    => true,
                    "message"  => " No Have Purchase ",
                ],200);
            }
            return response([
                "status"    => 200,
                "value"     => $edit,
                "error"     => false,
                "message"   => "Edit Purchase Access Successfully",
            ]);
        }catch(Exception $e){   
            return response([
                "status"   => 200,
                "error"    => true,
                "message"  => "Failed Action Error 403",
            ],200);
        } 
    }
    // 04 Store
    public function PurchaseStore(Request $request) {
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
            $data            = $request->only([
                                    "contact_id",
                                    "ref_no",
                                    "sup_refe",
                                    "transaction_date",
                                    "location_id",
                                    "exchange_rate",
                                    "pay_term_type",
                                    "pay_term_number",
                                    "status",
                                    "cost_center_id",
                                    "store_id",
                                    "currency_id",
                                    "currency_id_amount",
                                    "project_no",
                                    "depending_curr",
                                    "dis_currency",
                                    "list_price",
                                    "dis_type",
                                    "line_sort",
                                    "purchases",
                                    "total_before_tax",
                                    "total_before_tax_cur",
                                    "discount_type", 
                                    "discount_amount",
                                    "tax_id",
                                    "tax_amount",
                                    "shipping_details",
                                    "ADD_SHIP",
                                    "add_currency_id",
                                    "add_currency_id_amount",
                                    "final_total",
                                    "final_total_hidden_",
                                    "ADD_SHIP_",
                                    "additional_notes",
                        
            ]);
            $shipping_data   = $request->only([
                                'contact_id',
                                'cost_center_id',
                                'shipping_contact_id',
                                'shipping_amount',
                                'shipping_vat',
                                'shipping_total',
                                'shipping_amount_curr',
                                'shipping_vat_curr',
                                'shipping_total_curr',
                                'shipping_account_id',
                                'shipping_cost_center_id',
                                'shiping_text',
                                'line_currency_id',
                                'line_currency_id_amount',
                                'shiping_date',
            ]); 
             
            
             
            // ********************************************START**
                if($request->validate([
                    'status'           => 'required',
                    'contact_id'       => 'required',
                    'transaction_date' => 'required',
                    'total_before_tax' => 'required',
                    'location_id'      => 'required',
                    'store_id'         => 'required',
                    'final_total'      => 'required',
                ])){
                      
                    $save      = Purchase::storePurchase($user,$data,$shipping_data,$request);
                    if($save == false){
                        return response()->json([
                            "status"   => 200,
                            "error"    => true,
                            "message"  => " Failed Action ",
                        ],200);
                    }
                    // **********************************************END**
                    \DB::commit();
                    return response([
                        "status"   => 200,
                        "error"    => false,
                        "message" => "Added Purchases Successfully",
                    ]);
                }else{
                    return response()->json([
                        "status"   => 200,
                        "error"    => true,
                        "message"  => " Failed Action Wrong with input data",
                    ],200);
                }
        }catch(Exception $e){
            return response([
                "status"  => 200,
                "error"    => true,
                "message" => "Failed Action Error 403",
            ],200);
        }  
    }
    // 05 Update
    public function PurchaseUpdate(Request $request,$id) {
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
            $data            = $request->only([
                            "supplier_id",
                            "sup_id",
                            "status",
                            "sup_refe",
                            "pay_term_number",
                            "pay_term_type",
                            "ref_no",
                            "old_sts",
                            "cost_center_id",
                            "transaction_date",
                            "exchange_rate",
                            "currency_id",
                            "currency_id_amount",
                            "depending_curr",
                            "dis_currency",
                            "store_id",
                            "list_price",
                            "dis_type",
                            "line_sort",
                            "purchases",
                            "total_before_tax",
                            "total_before_tax_cur",
                            "discount_type", 
                            "discount_amount",
                            "tax_id",
                            "tax_amount",
                            "shipping_details",
                            "ADD_SHIP",
                            "project_no",
                            "location_id",
                            "grand_total_hidden_curr",
                            "final_total",
                            "final_total_hidden_",
                            "ADD_SHIP_",
                            "add_currency_id",
                            "add_currency_id_amount",
                            "additional_notes",
                            "contact_id",
                    
            ]);
            $shipping_data = $request->only([
                            'cost_center_id',
                            'supplier_id',
                            'sup_id',
                            'contact_id',
                            'additional_shipping_item_id',
                            'old_shipping_contact_id',
                            'old_shipping_amount',
                            'old_shipping_vat',
                            'old_shipping_total',
                            'old_shipping_account_id',
                            'old_shipping_cost_center_id',
                            'old_shiping_text',
                            'old_shiping_date',
                            'shipping_contact_id',
                            'shipping_amount',
                            'shipping_vat',
                            'shipping_total',
                            'shipping_account_id',
                            'shiping_text',
                            'shiping_date',
            ]);

            // ************************************************************************START**
            if($request->validate([
                'old_sts'           => 'required',
                'supplier_id'       => 'required',
                'sup_id'            => 'required',
                'transaction_date'  => 'required',
                'total_before_tax'  => 'required',
                'location_id'       => 'required',
                'store_id'          => 'required',
                'final_total'       => 'required',
            ])){
                $update    = Purchase::updatePurchase($user,$data,$shipping_data,$id,$request);
                if($update == false){
                    return response()->json([
                        "status"   => 200,
                        "error"    => true,
                        "message"  => " No Have Purchases ",
                    ],200);
                }
                \DB::commit();
                return response([
                    "status"   => 200,
                    "error"    => false,
                    "message" => "Updated Purchases Successfully",
                ]);
            }else{
                return response()->json([
                    "status"   => 200,
                    "error"    => true,
                    "message"  => " Failed Action Wrong with input data",
                ],200);
            }
            
            // **************************************************************************END**
          
        }catch(Exception $e){
            return response([
                "status"  => 200,
                "error"   => true,
                "message" => "Failed Action Error 403",
            ],200);
        }
    }
    // 06 Delete
    public function PurchaseDelete(Request $request,$id) {
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
            $del    = Purchase::deletePurchase($user,$id);
            // ***********************************END**
            if($del == "notFound"){
                return response()->json([
                    "status"   => 200,
                    "error"    => true,
                    "message"  => " No Have Purchase ",
                ],200);
            }else if($del == "related"){
                return response()->json([
                    "status"   => 200,
                    "error"    => true,
                    "message"  => "Can't Delete, There are relations on this purchase ",
                ],200);
            }else{
                return response([
                    "status"   => 200,
                    "error"    => false,
                    "message" => "Deleted Purchase Successfully",
                ]);
            } 
        }catch(Exception $e){
            return response([
                "status"  => 200,
                "error"    => true,
                "message" => "Failed Action Error 403",
            ],200);
        }
    }
    // 07 supplier
    public function getSupplier(Request $request) {
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
            $data        = $request->only(["letters"]);
            $supplier    = Purchase::getSupplier($user,$data);
            if($supplier == false){
                return response()->json([
                    "status"   => 200,
                    "error"    => true,
                    "message"  => " No Have Supplier With This Letter ",
                ],200);
            }
            return response([
                "status"   => 200,
                "value"    => $supplier,
                "error"    => false,
                "message"  => "Supplier Access Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"  => 200,
                "error"   => true,
                "message" => "Failed Action Error 403",
            ],200);
        }  
    }
    // 08 select  supplier
    public function selectSupplier(Request $request,$id) {
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
            $data["id"]  = $id;
            $supplier    = Purchase::selectSupplier($user,$data);
            if($supplier == false){
                return response()->json([
                    "status"   => 200,
                    "error"    => true,
                    "message"  => " No Have Supplier Selected With This Id ",
                ],200);
            }
            return response([
                "status"   => 200,
                "value"    => $supplier,
                "error"    => false,
                "message"  => "Supplier Access Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"  => 200,
                "error"   => true,
                "message" => "Failed Action Error 403",
            ],200);
        }  
    }
    // 09 Entry
    public function PurchaseEntry(Request $request,$id) {
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
            // *******************************************START**
            $entry             = Purchase::entryPurchase($user,$data,$id);
            if($entry == false){
                return response()->json([
                    "status"   => 200,
                    "error"    => true,
                    "message"  => " No Have Previous Entry",
                ],200);
            }
            // *********************************************END**
            \DB::commit();
            return response([
                "status"   => 200,
                "value"    => $entry,
                "error"    => false,
                "message"  => "Entry Access Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"  => 200,
                "error"    => true,
                "message" => "Failed Action Error 403",
            ],200);
        }
    }
    // 10 Print
    public function PurchasePrint(Request $request,$id) {
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
            // *******************************************START**
            $print             = Purchase::printPurchase($user,$data,$id);
            if($print == false){
                return response()->json([
                    "status"   => 200,
                    "error"    => true,
                    "message"  => " No Have Previous Purchase",
                ],200);
            }
            // *********************************************END**
            \DB::commit();
            return response([
                "status"   => 200,
                "value"    => $print,
                "error"    => false,
                "message"  => "Print Access Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"  => 200,
                "error"   => true,
                "message" => "Failed Action Error 403",
            ],200);
        }
    }
    // 11 View
    public function PurchaseView(Request $request,$id) {
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
            // *******************************************START**
            $view             = Purchase::viewPurchase($user,$data,$id);
            if($view == false){
                return response()->json([
                    "status"   => 200,
                    "error"    => true,
                    "message"  => " No Have Previous Purchase",
                ],200);
            }
            // *********************************************END**
            \DB::commit();
            return response([
                "status"   => 200,
                "value"    => $view,
                "error"    => false,
                "message"  => "View Access Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"  => 200,
                "error"    => true,
                "message" => "Failed Action Error 403",
            ],200);
        }
    }
    // 12 Map
    public function PurchaseMap(Request $request,$id) {
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
            // *******************************************START**
            $map             = Purchase::mapPurchase($user,$data,$id);
            if($map == false){
                return response()->json([
                    "status"   => 200,
                    "error"    => true,
                    "message"  => " No Have Previous Movement",
                ],200);
            }
            // *********************************************END**
            \DB::commit();
            return response([
                "status"   => 200,
                "value"    => $map,
                "error"    => false,
                "message"  => "Map Access Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"  => 200,
                "error"    => true,
                "message" => "Failed Action Error 403",
            ],200);
        }
    }
    // 13 Add Payment
    public function PurchaseAddPayment(Request $request,$id) {
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
            // *******************************************START**
            $payments         = Purchase::addPaymentPurchase($user,$data,$id);
            if($payments == false){
                return response()->json([
                    "status"   => 200,
                    "error"    => true,
                    "message"  => " No Have Previous Purchase",
                ],200);
            }
            // *********************************************END**
            \DB::commit();
            return response([
                "status"   => 200,
                "value"    => $payments,
                "error"    => false,
                "message"  => "Map Access Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"  => 200,
                "error"    => true,
                "message" => "Failed Action Error 403",
            ],200);
        }
    }
    // 14 View Payment
    public function PurchaseViewPayment(Request $request,$id) {
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
            // *******************************************START**
            $payments         = Purchase::viewPaymentPurchase($user,$data,$id);
            if($payments == false){
                return response()->json([
                    "status"   => 200,
                    "error"    => true,
                    "message"  => " No Have Previous Payments",
                ],200);
            }
            // *********************************************END**
            \DB::commit();
            return response([
                "status"   => 200,
                "value"    => $payments,
                "error"    => false,
                "message"  => "Payments Access Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"  => 200,
                "error"    => true,
                "message" => "Failed Action Error 403",
            ],200);
        }
    }
    // 15 Search Product
    public function PurchaseSearchProduct(Request $request) {
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
                     "error"    => true,
                     "message"  => __("Not Found  !!"),
                 ],200);
            }
            return response([
                "status"   => 200,
                "info"     => json_decode($product),
                "error"    => false,
                "message" => "Access Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"  => 200,
                "error"   => true,
                "message" => "Failed Action Error 403",
            ],200);
        }
    }
    // 16 Select Product
    public function PurchaseSelectProduct(Request $request) {
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
                     "status"   => 200,
                     "error"    => true,
                     "message"  => __("Not Found  !!"),
                 ],200);
            }
            return response([
                "status"   => 200,
                "info"     => $product,
                "error"    => false,
                "message"  => "Access Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"  => 200,
                "error"   => true,
                "message" => "Failed Action Error 403",
            ],200);
        }
    }
    // 17 Attachment
    public function PurchaseAttachment(Request $request,$id) {
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
            // *******************************************START**
            $currency             = Purchase::attachPurchase($user,$data,$id);
            if($currency == false){
                return response()->json([
                    "status"   => 200,
                    "error"    => true,
                    "message"  => " No Have Previous Attachment",
                ],200);
            }
            // *********************************************END**
            \DB::commit();
            return response([
                "status"   => 200,
                "value"    => $currency,
                "error"    => false,
                "message"  => "Attachment Access Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"  => 200,
                "error"    => true,
                "message" => "Failed Action Error 403",
            ],200);
        }
    }
    // 18 Get Update Status 
    public function PurchaseUpdateStatus(Request $request,$id) {
        try{
            $main_token   = $request->header("Authorization");
            $token        = substr($main_token,7);
            $data         = $request->only(["status","contact_id"]);
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
            // *******************************************START**
            $status             = Purchase::updateStatusPurchase($user,$data,$id);
            if($status == false){
                return response()->json([
                    "status"   => 200,
                    "error"    => true,
                    "message"  => " No Have Previous Purchase",
                ],200);
            }
            // *********************************************END**
            \DB::commit();
            return response([
                "status"   => 200,
                "value"    => $status,
                "error"    => false,
                "message"  => "Get Status Access Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"  => 200,
                "error"   => true,
                "message" => "Failed Action Error 403",
            ],200);
        }
    }
    // 19 Update Status
    public function PurchaseGetUpdateStatus(Request $request,$id) {
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
            // *******************************************START**
            $status             = Purchase::getUpdateStatusPurchase($user,$data,$id);
            if($status == false){
                return response()->json([
                    "status"   => 200,
                    "error"    => true,
                    "message"  => " No Have Previous Purchase",
                ],200);
            }
            // *********************************************END**
            \DB::commit();
            return response([
                "status"   => 200,
                "value"    => $status,
                "error"    => false,
                "message"  => "Get Status Access Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"  => 200,
                "error"   => true,
                "message" => "Failed Action Error 403",
            ],200);
        }
    }

    // ******* Received Section ******* \\
    
    // 1 Get All Received 
    public function PurchaseAllReceived(Request $request){
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
            $purchase    = Purchase::getAllPurchaseReceived($user,$request);
            if($purchase == false){
                return response()->json([
                    "status"   => 420003,
                    "error"    => true,
                    "message"  => " No Have Receives",
                ],200);
            }
            return response([
                "status"   => 200,
                "value"    => $purchase,
                "error"    => true,
                "message"  => "Received Access Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"   => 200,
                "error"    => true,
                "message"  => "Failed Action Error 403",
            ],200);
        } 
    }
    // 2 Get Purchase Received 
    public function PurchaseReceived(Request $request,$id){
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
            $purchase    = Purchase::getPurchaseReceived($user,$request,$id);
            if($purchase == false){
                return response()->json([
                    "status"   => 200,
                    "error"    => true,
                    "message"  => " No Have Old Received For This Purchase ",
                ],200);
            }
            return response([
                "status"   => 200,
                "value"    => $purchase,
                "error"    => true,
                "message"  => "Purchase Received Access Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"   => 200,
                "error"    => true,
                "message"  => "Failed Action Error 403",
            ],200);
        } 
    }
    // 3 Create Purchase Received 
    public function PurchaseCreateReceived(Request $request){
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
            $purchase    = Purchase::createPurchaseReceived($user,$request);
            if($purchase == false){
                return response()->json([
                    "status"   => 200,
                    "error"    => true,
                    "message"  => " There is error ",
                ],200);
            }
            return response([
                "status"   => 200,
                "value"    => $purchase,
                "error"    => false,
                "message"  => "Create Purchase Received Access Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"   => 200,
                "error"    => true,
                "message"  => "Failed Action Error 403",
            ],200);
        } 
    }
    // 4 Edit Purchase Received 
    public function PurchaseEditReceived(Request $request,$id){
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
            $purchase    = Purchase::editPurchaseReceived($user,$request,$id);
            if($purchase == false){
                return response()->json([
                    "status"   => 200,
                    "error"    => true,
                    "message"  => " No Have Purchase Received ",
                ],200);
            }
            return response([
                "status"   => 200,
                "value"    => $purchase,
                "error"    => false,
                "message"  => "Edit Purchase Received Access Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"   => 200,
                "error"    => true,
                "message"  => "Failed Action Error 403",
            ],200);
        } 
    }
    // 5 Save Purchase Received 
    public function PurchaseReceivedStore(Request $request){
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
            // ......................................................START.
            $purchase    = Purchase::savePurchaseReceived($user,$request);
            // .......................................................END.. 
            if($purchase == false){
                return response()->json([
                    "status"   => 200,
                    "error"    => true,
                    "message"  => " There is error ",
                ],200);
            }
            return response([
                "status"   => 200, 
                "error"    => false,
                "message"  => "Purchase Received Saved Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"  => 200,
                "error"    => true,
                "message" => "Failed Action Error 403",
            ],200);
        } 
    }
    // 6 Update Purchase Received 
    public function PurchaseReceivedUpdate(Request $request,$id){
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
            // ...........................................................START..
            $data = $request->only([
                ""
            ]);
            $shipping_data = $request->only([
                ""
            ]); 
            $purchase    = Purchase::updatePurchaseReceived($user,$request,$id);
            // ...........................................................END....
            if($purchase == false){
                return response()->json([
                    "status"   => 200,
                    "error"    => true,
                    "message"  => " There is error ",
                ],200);
            }
            return response([
                "status"   => 200, 
                "error"    => false,
                "message"  => "Purchase Received Updated Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"  => 200,
                "error"    => true,
                "message" => "Failed Action Error 403",
            ],200);
        } 
    }
    // 7 View Purchase Received 
    public function PurchaseViewReceived(Request $request,$id){
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
            $purchase    = Purchase::viewPurchaseReceived($user,$request,$id);
            if($purchase == false){
                return response()->json([
                    "status"   => 200,
                    "error"    => true,
                    "message"  => " No Have Purchase Received ",
                ],200);
            }
            return response([
                "status"   => 200,
                "value"    => $purchase,
                "error"    => false,
                "message"  => "View Purchase Received Access Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"   => 200,
                "error"    => true,
                "message"  => "Failed Action Error 403",
            ],200);
        } 
    }
    // 8 Print Purchase Received 
    public function PurchasePrintReceived(Request $request,$id){
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
            $purchase    = Purchase::printPurchaseReceived($user,$request,$id);
            if($purchase == false){
                return response()->json([
                    "status"   => 200,
                    "error"    => true,
                    "message"  => " No Have Purchase Received ",
                ],200);
            }
            return response([
                "status"   => 200,
                "value"    => $purchase,
                "error"    => true,
                "message"  => "Purchase Received Note Access Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"   => 200,
                "error"    => true,
                "message"  => "Failed Action Error 403",
            ],200);
        } 
    }
    // 9 Attachment Purchase Received
    public function PurchaseAttachmentReceived(Request $request,$id) {
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
            // *******************************************START**
            $attach             = Purchase::attachPurchaseReceived($user,$request,$id);
            if($attach == false){
                return response()->json([
                    "status"   => 200,
                    "error"    => true,
                    "message"  => " No Have Previous Attachment",
                ],200);
            }
            // *********************************************END**
            \DB::commit();
            return response([
                "status"   => 200,
                "value"    => $attach,
                "error"    => false,
                "message"  => "Attachment Access Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"   => 200,
                "error"    => true,
                "message"  => "Failed Action Error 403",
            ],200);
        }
    }
    // 10 Delete Purchase Received 
    public function PurchaseReceivedDelete(Request $request,$id){
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
            $del    = Purchase::deletePurchaseReceived($user,$request,$id);
            if($del == "notFound"){
                return response()->json([
                    "status"   => 200,
                    "error"    => true,
                    "message"  => " No Have Purchase Received ",
                ],200);
            }else if($del == "related"){
                return response()->json([
                    "status"   => 200,
                    "error"    => true,
                    "message"  => "Can't Delete, There are relations on this purchase ",
                ],200);
            }else{
                return response([
                    "status"   => 200,
                    "error"    => false,
                    "message" => "Deleted Purchase Received Successfully",
                ]);
            } 
        }catch(Exception $e){
            return response([
                "status"   => 200,
                "error"    => true,
                "message"  => "Failed Action Error 403",
            ],200);
        }
    }
    // ...........................................................
    // 01 Select Last Of Supplier ................................
    public function PurchaseLastContact(Request $request) {
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
            $data['type']  = "supplier";
            $last    = Purchase::lastPurchaseContact($user,$data);
            if($last == false){
                return response()->json([
                    "status"   => 200,
                    "error"    => true,
                    "message"  => " No Have Contact ",
                ],403);
            } 
            return response([
                "status"   => 200,
                "value"    => $last,
                "error"    => false,
                "message"  => "Access Successfully",
            ]);
             
        }catch(Exception $e){
            return response([
                "status"   => 200,
                "error"    => true,
                "message"  => "Failed Action Error 403",
            ],200);
        }
    }
    // 02 check if the name is exist .............................
    public function PurchaseCheckContact(Request $request) {
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
            $data['value']  = request()->input('value');
            $check    = Purchase::checkPurchaseContact($user,$data);
            if($check == false){
                return response()->json([
                    "status"   => 200,
                    "error"    => true,
                    "message"  => " Sorry !!, This Name Is Already Exist ",
                ],200);
            } 
            return response([
                "status"   => 200,
                "error"    => false,
                "message"  => "Successfully",
            ]);
            
        }catch(Exception $e){
            return response([
                "status"   => 200,
                "error"    => true,
                "message"  => "Failed Action Error 403",
            ],200);
        }
    }
    // 03 Log file of purchase ...................................
    public function PurchaseLogFile(Request $request,$id) {
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
            $log    = Purchase::logFilePurchase($user,$id);
            if($log == false){
                return response()->json([
                    "status"   => 200,
                    "error"    => true,
                    "message"  => " You Don't Have Previous Purchase ",
                ],200);
            } 
            return response([
                "status"   => 200,
                "error"    => false,
                "value"    => $log,
                "message"  => "Successfully",
            ]);
            
        }catch(Exception $e){
            return response([
                "status"   => 200,
                "error"    => true,
                "message"  => "Failed Action Error 403",
            ],200);
        }
    }
    // 04 Map of purchase ...................................
    public function PurchaseAllMap(Request $request) {
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
            $data   = []; 
            $map    = Purchase::allMapPurchase($user,$data);
            if($map == false){
                return response()->json([
                    "status"   => 200,
                    "error"    => true,
                    "message"  => " You Don't Have Previous Map ",
                ],200);
            } 
            return response([
                "status"   => 200,
                "error"    => false,
                "value"    => $map,
                "message"  => "Access Successfully",
            ]);
            
        }catch(Exception $e){
            return response([
                "status"   => 200,
                "error"    => true,
                "message"  => "Failed Action Error 403",
            ],200);
        }
    }
    // 04 Log File of purchase ...................................
    public function PurchaseAllLogFile(Request $request) {
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
            $data   = []; 
            $log    = Purchase::allLogFilePurchase($user,$data);
            if($log == false){
                return response()->json([
                    "status"   => 200,
                    "error"    => true,
                    "message"  => " You Don't Have Previous Purchases ",
                ],200);
            } 
            return response([
                "status"   => 200,
                "error"    => false,
                "value"    => $log,
                "message"  => "Access Successfully",
            ]);
            
        }catch(Exception $e){
            return response([
                "status"   => 200,
                "error"    => true,
                "message"  => "Failed Action Error 403",
            ],200);
        }
    }
}

