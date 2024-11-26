<?php

namespace App\Http\Controllers\ApiController\FrontEnd;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FrontEnd\Patterns\Pattern;

class PatternController extends Controller
{
    // 1 index
    public function Pattern(Request $request) {
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
            $pattern    = Pattern::getPattern($user);
            if($pattern == false){
                return response()->json([
                    "status"   => 405,
                    "message"  => " No Have Patterns ",
                ],405);
            }
            return response([
                "status"   => 200,
                "value"    => $pattern,
                "message"  => "Patterns Access Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"  => 403,
                "message" => "Failed Action Error 403",
            ],403);
        }    
    }
    // 2 create
    public function PatternCreate(Request $request) {
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
            $create    = Pattern::createPattern($user,$data);
            // ****************************************END**
            if($create == false){
                return response()->json([
                    "status"   => 403,
                    "message"  => " No Have Patterns  ",
                ],403);
            }
            return response([
                "status"   => 200,
                "value"    => $create,
                "message"  => "Create Patterns Access Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"  => 403,
                "message" => "Failed Action Error 403",
            ],403);
        }   
    }
    // 3 Edit
    public function PatternEdit(Request $request,$id) {
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
            $edit    = Pattern::editPattern($user,$data,$id);
            // ****************************************END**
            if($edit == false){
                return response()->json([
                    "status"   => 403,
                    "message"  => " No Have Patterns ",
                ],403);
            }
            return response([
                "status"    => 200,
                "value"     => $edit,
                "message"   => "Edit Patterns Access Successfully",
            ]);
        }catch(Exception $e){   
            return response([
                "status"  => 403,
                "message" => "Failed Action Error 403",
            ],403);
        }   
    }
    // 4 Store
    public function PatternStore(Request $request) {
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
                                    "code",
                                    "location_id",
                                    "name",
                                    "invoice_scheme",
                                    "invoice_layout",
                                    "pos"
                ]);
                $save      = Pattern::storePattern($user,$data);
                if($save == "false"){
                    return response()->json([
                        "status"   => 403,
                        "message"  => " Failed Action ",
                    ],403);
                }elseif($save == "old"){
                    return response()->json([
                        "status"   => 405,
                        "message"  => " Sorry !, This Name is Already Exist ",
                    ],405);
                }elseif($save == "failed"){
                    return response()->json([
                        "status"   => 403,
                        "message"  => " Failed Action ",
                    ],403);
                }
            // **********************************************END**
            \DB::commit();
            return response([
                "status"   => 200,
                "message" => "Added Patterns Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"  => 403,
                "message" => "Failed Action Error 403",
            ],403);
        } 
    }
    // 5 Update
    public function PatternUpdate(Request $request,$id) {
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
                            "code",
                            "location_id",
                            "name",
                            "invoice_scheme",
                            "invoice_layout",
                            "pos"
            ]);
            $update    = Pattern::updatePattern($user,$data,$id);
          
            if($update == "false"){
                return response()->json([
                    "status"   => 403,
                    "message"  => " No Have Patterns ",
                ],403);
            }elseif($update == "old"){
                return response()->json([
                    "status"   => 405,
                    "message"  => " Sorry !, This Name is Already Exist ",
                ],405);
            }elseif($update == "failed"){
                return response()->json([
                    "status"   => 403,
                    "message"  => " Failed Action ",
                ],403);
            }
            // *********************************************END**
            \DB::commit();
            return response([
                "status"   => 200,
                "message" => "Updated Patterns Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"  => 403,
                "message" => "Failed Action Error 403",
            ],403);
        }
    }
    // 6 Delete
    public function PatternDelete(Request $request,$id) {
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
            $del    = Pattern::deletePattern($user,$id);
            // ***********************************END**
            if($del == "false"){
                return response()->json([
                    "status"   => 403,
                    "message"  => " No Have Patterns ",
                ],403);
            }elseif($del == "cannot"){
                return response()->json([
                    "status"   => 405,
                    "message"  => " Sorry , You Cannot Delete The Pattern There Transactions Related ",
                ],405);
            }elseif($del == "true"){
                return response([
                    "status"   => 200,
                    "message" => "Deleted Pattern Successfully",
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
}
