<?php

namespace App\Http\Controllers\ApiController\FrontEnd;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FrontEnd\Warehouses\Warehouse;

class WarehouseController extends Controller
{
  
    // 1 index
    public function Warehouse(Request $request) {
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
            $Warehouse    = Warehouse::getWarehouse($user);
            if($Warehouse == false){
                return response()->json([
                    "status"   => 405,
                    "message"  => " No Have Warehouses ",
                ],405);
            }
            return response([
                "status"   => 200,
                "value"    => $Warehouse,
                "message"  => "Warehouses Access Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"  => 403,
                "message" => "Failed Action Error 403",
            ],403);
        }    
    }
    // 2 create
    public function WarehouseCreate(Request $request) {
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
            $create    = Warehouse::createWarehouse($user,$data);
            // ****************************************END**
            if($create == false){
                return response()->json([
                    "status"   => 403,
                    "message"  => " No Have Warehouses  ",
                ],403);
            }
            return response([
                "status"   => 200,
                "value"    => $create,
                "message"  => "Create Warehouses Access Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"  => 403,
                "message" => "Failed Action Error 403",
            ],403);
        }   
    }
    // 3 Edit
    public function WarehouseEdit(Request $request,$id) {
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
            $edit    = Warehouse::editWarehouse($user,$data,$id);
            // ****************************************END**
            if($edit == false){
                return response()->json([
                    "status"   => 403,
                    "message"  => " No Have Warehouses ",
                ],403);
            }
            return response([
                "status"    => 200,
                "value"     => $edit,
                "message"   => "Edit Warehouses Access Successfully",
            ]);
        }catch(Exception $e){   
            return response([
                "status"  => 403,
                "message" => "Failed Action Error 403",
            ],403);
        }   
    }
    // 4 Store
    public function WarehouseStore(Request $request) {
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
                                    "name",
                                    "parent_id",
                                    "description",
                                    
                ]);
                $save      = Warehouse::storeWarehouse($user,$data);
                if($save === false){
                    return response()->json([
                        "status"   => 403,
                        "message"  => " Failed Action ",
                    ],403);
                }elseif($save == "old"){
                    return response()->json([
                        "status"   => 405,
                        "message"  => " Sorry !, This Name is Already Exist ",
                    ],405);
                }elseif($save === "failed"){
                    return response()->json([
                        "status"   => 403,
                        "message"  => " Failed Action ",
                    ],403);
                }elseif($save === "true"){
                        \DB::commit();
                    return response([
                        "status"   => 200,
                        "message" => "Added Warehouses Successfully",
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
    public function WarehouseUpdate(Request $request,$id) {
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
            $update    = Warehouse::updateWarehouse($user,$data,$id);
            
            if($update === false){
                return response()->json([
                    "status"   => 403,
                    "message"  => " No Have Warehouses ",
                ],403);
            }elseif($update === "old"){
                return response()->json([
                    "status"   => 405,
                    "message"  => " Sorry !, This Name is Already Exist ",
                ],405);
            }elseif($update === "failed"){
                return response()->json([
                    "status"   => 403,
                    "message"  => " Failed Action ",
                ],403);
            }elseif($update === "true"){
                \DB::commit();
                return response([
                    "status"   => 200,
                    "message" => "Updated Warehouses Successfully",
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
    public function WarehouseDelete(Request $request,$id) {
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
            $del    = Warehouse::deleteWarehouse($user,$id);
            // ***********************************END**
            if($del == "false"){
                return response()->json([
                    "status"   => 403,
                    "message"  => " No Have Warehouses ",
                ],403);
            }elseif($del == "cannot"){
                return response()->json([
                    "status"   => 405,
                    "message"  => " Sorry , You Cannot Delete The Warehouse There Transactions Related ",
                ],405);
            }elseif($del == "true"){
                return response([
                    "status"   => 200,
                    "message" => "Deleted Warehouse Successfully",
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
    // 7 Movement
    public function WarehouseMovement(Request $request,$id) {
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
            $movement    = Warehouse::getWarehouseMovement($user,$id);
            if($movement == false){
                return response()->json([
                    "status"   => 405,
                    "message"  => " No Have Warehouse Movement ",
                ],405);
            }
            return response([
                "status"   => 200,
                "value"    => $movement,
                "message"  => "Warehouse Movement  Access Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"  => 403,
                "message" => "Failed Action Error 403",
            ],403);
        }    
    }
}
