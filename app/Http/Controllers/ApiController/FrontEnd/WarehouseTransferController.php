<?php

namespace App\Http\Controllers\ApiController\FrontEnd;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FrontEnd\Warehouses\WarehouseTransfer;

class WarehouseTransferController extends Controller
{
    // ............................................................
    // 1 index
    public function WarehouseTransfer(Request $request) {
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
                    "status"   => 401,
                    "message"  => " Unauthorized action ",
                ],401);
            } 
            $Warehouse    = WarehouseTransfer::getWarehouseTransfer($user);
            if($Warehouse == false){
                return response()->json([
                    "status"   => 405,
                    "message"  => " No Have Warehouse Transfer ",
                ],405);
            }
            return response([
                "status"   => 200,
                "value"    => $Warehouse,
                "message"  => "Warehouse Transfer Access Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"  => 403,
                "message" => "Failed Action Error 403",
            ],403);
        }    
    }
    // 2 create
    public function WarehouseTransferCreate(Request $request) {
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
            // **************************************START**
            $create    = WarehouseTransfer::createWarehouseTransfer($user,$data);
            // ****************************************END**
            if($create == false){
                return response()->json([
                    "status"   => 403,
                    "message"  => " No Have Warehouse Transfer  ",
                ],403);
            }
            return response([
                "status"   => 200,
                "value"    => $create,
                "message"  => "Create Warehouse Transfer Access Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"  => 403,
                "message" => "Failed Action Error 403",
            ],403);
        }   
    }
    // 3 Edit
    public function WarehouseTransferEdit(Request $request,$id) {
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
            // **************************************START**
            $edit    = WarehouseTransfer::editWarehouseTransfer($user,$data,$id);
            // ****************************************END**
            if($edit == false){
                return response()->json([
                    "status"   => 403,
                    "message"  => " No Have Warehouse Transfer ",
                ],403);
            }
            return response([
                "status"    => 200,
                "value"     => $edit,
                "message"   => "Edit Warehouse Transfer Access Successfully",
            ]);
        }catch(Exception $e){   
            return response([
                "status"  => 403,
                "message" => "Failed Action Error 403",
            ],403);
        }   
    }
    // 4 Store
    public function WarehouseTransferStore(Request $request) {
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
                                    "transaction_date","ref_no","status","location_id","transfer_location_id",
                                    "location_id1","final_total","products","additional_notes"  
                                    
                ]);
                $save      = WarehouseTransfer::storeWarehouseTransfer($user,$data,$request);
                if($save === false){
                    return response()->json([
                        "status"   => 403,
                        "message"  => " Failed Action ",
                    ],403);
                }elseif($save === true){
                        \DB::commit();
                    return response([
                        "status"   => 200,
                        "message" => "Added Warehouse Transfer Successfully",
                    ]);
                }
            // **********************************************END**
            
        }catch(Exception $e){
            return response([
                "status"  => 403,
                "message" => "Failed Action Error 403",
            ],403);
        } 
    }
    // 5 Update
    public function WarehouseTransferUpdate(Request $request,$id) {
    
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
            $data         = $request->only([
                                "name",
                                "parent_id",
                                "description",
                            
            ]);
            $update    = WarehouseTransfer::updateWarehouseTransfer($user,$data,$id,$request);
            
            if($update === false){
                return response()->json([
                    "status"   => 403,
                    "message"  => " No Have Warehouse Transfer ",
                ],403);
            }elseif($update === true){
                \DB::commit();
                return response([
                    "status"   => 200,
                    "message" => "Updated Warehouse Transfer Successfully",
                ]);
            }
            // *********************************************END**
        }catch(Exception $e){
            return response([
                "status"  => 403,
                "message" => "Failed Action Error 403",
            ],403);
        }
    }
    // 6 Delete
    public function WarehouseTransferDelete(Request $request,$id) {
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
            $del    = WarehouseTransfer::deleteWarehouseTransfer($user,$id);
            // ***********************************END**
            if($del == "false"){
                return response()->json([
                    "status"   => 403,
                    "message"  => " No Have Warehouse Transfer ",
                ],403);
            }elseif($del == "cannot"){
                return response()->json([
                    "status"   => 405,
                    "message"  => " Sorry , You Cannot Delete The Warehouse Transfer There Transactions Related ",
                ],405);
            }elseif($del == "true"){
                return response([
                    "status"   => 200,
                    "message" => "Deleted Warehouse Transfer Successfully",
                ]);
            }else{
                return response([
                    "status"  => 405,
                    "message" => "Failed Action Error 405",
                ],405);
            }
        }catch(Exception $e){
            return response([
                "status"  => 405,
                "message" => "Failed Action Error 405",
            ],405);
        }
    }
    // .............................................................
}
