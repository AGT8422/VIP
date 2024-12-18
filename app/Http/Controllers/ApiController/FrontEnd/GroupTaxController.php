<?php

namespace App\Http\Controllers\ApiController\FrontEnd;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FrontEnd\Settings\GroupTax;
class GroupTaxController extends Controller
{
    // 1 index
    public function GroupTax(Request $request) {
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
            $groupTax    = GroupTax::getGroupTax($user);
            if($groupTax == false){
                return response()->json([
                    "status"   => 403,
                    "message"  => " No Have GroupTaxes ",
                ],403);
            }
            return response([
                "status"   => 200,
                "value"    => $groupTax,
                "message"  => "GroupTaxes Access Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"  => 403,
                "message" => "Failed Action Error 403",
            ],403);
        } 
    }
    // 2 create
    public function GroupTaxCreate(Request $request) {
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
            // ***************************************START**
            $create    = GroupTax::createGroupTax($user,$data);
            // *****************************************END**
            if($create == false){
                return response()->json([
                    "status"   => 403,
                    "message"  => " No Have GroupTax ",
                ],403);
            }
            return response([
                "status"   => 200,
                "value"    => $create,
                "message"  => "Create GroupTax Access Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"  => 403,
                "message" => "Failed Action Error 403",
            ],403);
        }
    }
    // 3 Edit
    public function GroupTaxEdit(Request $request,$id) {
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
            // **********************************START**
            $edit    = GroupTax::editGroupTax($user,$data,$id);
            // ************************************END**
            if($edit == false){
                return response()->json([
                    "status"   => 403,
                    "message"  => " No Have GroupTaxes ",
                ],403);
            }
            return response([
                "status"    => 200,
                "value"     => $edit,
                "message"   => "Edit GroupTaxes Access Successfully",
            ]);
        }catch(Exception $e){   
            return response([
                "status"  => 403,
                "message" => "Failed Action Error 403",
            ],403);
        }  
    }
    // 4 Store
    public function GroupTaxStore(Request $request) {
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
            
            // ***********************************************************************************START**
                $data                = $request->only(["name","taxes"]);
                $data["business_id"] = $user->business_id;
                $data["created_by"]  = $user->id;
                $save      = GroupTax::storeGroupTax($user,$data,$request);
                if($save == "old"){
                    return response()->json([
                        "status"   => 405,
                        "message"  => " Sorry !, This Name is Already Exist ",
                    ],405);
                }elseif($save == "false" || $save == "failed" ){
                    return response()->json([
                        "status"   => 403,
                        "message"  => " Failed Action ",
                    ],403);
                }else{
                    \DB::commit();
                    return response([
                        "status"   => 200,
                        "message" => "Added GroupTax Successfully",
                    ]);
                }
            // *************************************************************************************END**
        }catch(Exception $e){
            return response([
                "status"  => 403,
                "message" => "Failed Action Error 403",
            ],403);
        } 
    }
    // 5 Update
    public function GroupTaxUpdate(Request $request,$id) {
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
            // *******************************************************************START**
            $data                = $request->only(["name","taxes"]);
            $data["business_id"] = $user->business_id;
            $data["created_by"]  = $user->id;
            $update    = GroupTax::updateGroupTax($user,$data,$id,$request);
            if($update == "old"){
                return response()->json([
                    "status"   => 405,
                    "message"  => " Sorry !, This Name is Already Exist ",
                ],405);
            }elseif($update == "false" || $update == "failed" ){
                return response()->json([
                    "status"   => 403,
                    "message"  => " Failed Action ",
                ],403);
            }else{
                \DB::commit();
                return response([
                    "status"   => 200,
                    "message"  => "Updated GroupTaxes Successfully",
                ]);
            }
            // *********************************************************************END**
        }catch(Exception $e){
            return response([
                "status"  => 403,
                "message" => "Failed Action Error 403",
            ],403);
        } 
    }
    // 6 Delete
    public function GroupTaxDelete(Request $request,$id) {
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
            // ************************************START**
            $del    = GroupTax::deleteGroupTax($user,$id);
            // **************************************END**
            if($del == "no"){
                return response()->json([
                    "status"   => 403,
                    "message"  => " No Have GroupTaxes ",
                ],403);
            }elseif($del == "false"){
                return response()->json([
                    "status"   => 403,
                    "message"  => "Sorry! You Can't Delete This GroupTax Because Used From Products ",
                ],403);
            }else{
                return response([
                    "status"   => 200,
                    "message" => "Deleted GroupTaxes Successfully",
                ]);
            }
        }catch(Exception $e){
            return response([
                "status"  => 403,
                "message" => "Failed Action Error 403",
            ],403);
        }
    }
}
