<?php

namespace App\Http\Controllers\ApiController\FrontEnd;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SalesCommissionAgentController extends Controller
{
    // 1 index
    public function SalesCommissionAgent(Request $request) {
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
            $saleCa    = SalesCommissionAgent::getSalesCommissionAgent($user);
            if($saleCa == false){
                return response()->json([
                    "status"   => 403,
                    "message"  => " No Have Sales Commission Agent",
                ],403);
            }
            return response([
                "status"   => 200,
                "value"    => $saleCa,
                "message"  => "Sales Commission Agent Access Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"  => 403,
                "message" => "Failed Action Error 403",
            ],403);
        }   
    }
    // 2 create
    public function SalesCommissionAgentCreate(Request $request) {
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
            // *****************************************************************START**
            $create    = SalesCommissionAgent::createSalesCommissionAgent($user,$data);
            // *******************************************************************END**
            if($create == false){
                return response()->json([
                    "status"   => 403,
                    "message"  => " No Have Sales  ",
                ],403);
            }
            return response([
                "status"   => 200,
                "value"    => $create,
                "message"  => "Create Sales Commission Agent Access Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"  => 403,
                "message" => "Failed Action Error 403",
            ],403);
        }    
    }
    // 3 Edit
    public function SalesCommissionAgentEdit(Request $request,$id) {
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
            // *****************************************************************START**
            $edit    = SalesCommissionAgent::editSalesCommissionAgent($user,$data,$id);
            // *******************************************************************END**
            if($edit == false){
                return response()->json([
                    "status"   => 403,
                    "message"  => " No Have Sales ",
                ],403);
            }
            return response([
                "status"    => 200,
                "value"     => $edit,
                "message"   => "Edit Sales Commission Agent Access Successfully",
            ]);
        }catch(Exception $e){   
            return response([
                "status"  => 403,
                "message" => "Failed Action Error 403",
            ],403);
        } 
    }
    // 4 Store
    public function SalesCommissionAgentStore(Request $request) {
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
            $data         = $request->only([
                                "type",
                                "supplier_business_name"
            ]);
            // ********************************************************************START**
                $save      = SalesCommissionAgent::storeSalesCommissionAgent($user,$data);
                if($save == false){
                    return response()->json([
                        "status"   => 403,
                        "message"  => " Failed Action ",
                    ],403);
                } 
            // **********************************************************************END**
            \DB::commit();
            return response([
                "status"   => 200,
                "message" => "Added Sales Commission Agent Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"  => 403,
                "message" => "Failed Action Error 403",
            ],403);
        }
    }
    // 5 Update
    public function SalesCommissionAgentUpdate(Request $request,$id) {
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
            $data         = $request->only([
                                "type","supplier_business_name"
            ]);
            // *********************************************************************START**
            $update    = SalesCommissionAgent::updateSalesCommissionAgent($user,$data,$id);
            if($update == false){
                return response()->json([
                    "status"   => 403,
                    "message"  => " No Have Sales ",
                ],403);
            }
            // ***********************************************************************END**
            \DB::commit();
            return response([
                "status"   => 200,
                "message" => "Updated Sales Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"  => 403,
                "message" => "Failed Action Error 403",
            ],403);
        }
    }
    // 6 Delete
    public function SalesCommissionAgentDelete(Request $request,$id) {
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
            // *************************************************************START**
            $del    = SalesCommissionAgent::deleteSalesCommissionAgent($user,$id);
            // ***************************************************************END**
            if($del == false){
                return response()->json([
                    "status"   => 403,
                    "message"  => " No Have  Sales Commission Agent ",
                ],403);
            }
            return response([
                "status"   => 200,
                "message" => "Deleted Sales Commission Agent Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"  => 403,
                "message" => "Failed Action Error 403",
            ],403);
        } 
    }
}
