<?php

namespace App\Http\Controllers\ApiController\FrontEnd;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FrontEnd\Products\Variation;

class VariationController extends Controller
{
    // 1 index
    public function Variation(Request $request) {
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
            $variation    = Variation::getVariation($user);
            if($variation == false){
                return response()->json([
                    "status"   => 405,
                    "message"  => " No Have Variations ",
                ],405);
            }
            return response([
                "status"   => 200,
                "value"    => $variation,
                "message"  => "Variations Access Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"  => 405,
                "message" => "Failed Action Error 405",
            ],405);
        }   
    }
    // 2 create
    public function VariationCreate(Request $request) {
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
            // *******************************************START**
            $create    = Variation::createVariation($user,$data);
            // *********************************************END**
            if($create == false){
                return response()->json([
                    "status"   => 405,
                    "message"  => " No Have Variations  ",
                ],405);
            }
            return response([
                "status"   => 200,
                "value"    => $create,
                "message"  => "Create Variations Access Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"  => 405,
                "message" => "Failed Action Error 405",
            ],405);
        }  
    }
    // 3 Edit
    public function VariationEdit(Request $request,$id) {
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
            // ******************************************START**
            $edit    = Variation::editVariation($user,$data,$id);
            // ********************************************END**
            if($edit == false){
                return response()->json([
                    "status"   => 405,
                    "message"  => " No Have Variations ",
                ],405);
            }
            return response([
                "status"    => 200,
                "value"     => $edit,
                "message"   => "Edit Variations Access Successfully",
            ]);
        }catch(Exception $e){   
            return response([
                "status"  => 405,
                "message" => "Failed Action Error 405",
            ],405);
        }  
    }
    // 4 Store
    public function VariationStore(Request $request) {
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
                $data      = $request->only([ "name", "items" ]);
                $save      = Variation::storeVariation($user,$data);
                if($save == false){
                    return response()->json([
                        "status"   => 405,
                        "message"  => " Failed Action ",
                    ],405);
                }
            // **********************************************END**
            \DB::commit();
            return response([
                "status"   => 200,
                "message"  => "Added Variations Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"  => 405,
                "message" => "Failed Action Error 405",
            ],405);
        }
    }
    // 5 Update
    public function VariationUpdate(Request $request,$id) {
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
            $data      = $request->only([ "name", "items", "old_items" ]);
            $update    = Variation::updateVariation($user,$data,$id);
            if($update == "empty"){
                return response()->json([
                    "status"   => 405,
                    "message"  => " No Have Variations ",
                ],405);
            }elseif($update == "old"){
                return response()->json([
                    "status"   => 405,
                    "message"  => "Sorry !, There is variation have the same name ,choose other name .  ",
                ],405);
                
            }elseif($update == "false"){
                 return response()->json([
                    "status"   => 405,
                    "message"  => "Faild action ",
                ],405);
                
            }
            // *********************************************END**
            \DB::commit();
            return response([
                "status"   => 200,
                "message" => "Updated Variations Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"  => 405,
                "message" => "Failed Action Error 405",
            ],405);
        }
    }
    // 6 Delete
    public function VariationDelete(Request $request,$id) {
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
            // *************************************START**
            $del    = Variation::deleteVariation($user,$id);
            // ***************************************END**
            if($del == false){
                return response()->json([
                    "status"   => 405,
                    "message"  => " No Have Variation ",
                ],405);
            }
            return response([
                "status"   => 200,
                "message" => "Deleted Variation Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"  => 405,
                "message" => "Failed Action Error 405",
            ],405);
        }
    }
    // 6 Delete
    public function VariationRowDelete(Request $request,$id) {
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
            // ****************************************START**
            $del    = Variation::deleteRowVariation($user,$id);
            // ******************************************END**
            if($del == false){
                return response()->json([
                    "status"   => 405,
                    "message"  => " No Have Variation ",
                ],405);
            }
            return response([
                "status"   => 200,
                "message" => "Deleted Variation Row Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"  => 405,
                "message" => "Failed Action Error 405",
            ],405);
        }
    }
}
