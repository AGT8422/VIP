<?php

namespace App\Http\Controllers\ApiController\FrontEnd;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FrontEnd\Patterns\SystemAccount;
class SystemAccountController extends Controller
{
    // 1 index
    public function SystemAccount(Request $request) {
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
            $systemAccount    = SystemAccount::getSystemAccount($user);
            if($systemAccount == false){
                return response()->json([
                    "status"   => 403,
                    "message"  => " No Have System Accounts ",
                ],403);
            }
            return response([
                "status"   => 200,
                "value"    => $systemAccount,
                "message"  => "System Accounts Access Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"  => 403,
                "message" => "Failed Action Error 403",
            ],403);
        }    
    }
    // 2 create
    public function SystemAccountCreate(Request $request) {
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
            // ***************************************************START**
            $create    = SystemAccount::createSystemAccount($user,$data);
            // *****************************************************END**
            if($create == false){
                return response()->json([
                    "status"   => 403,
                    "message"  => " No Have Patterns  ",
                ],403);
            }
            return response([
                "status"   => 200,
                "value"    => $create,
                "message"  => "Create System Accounts Access Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"  => 403,
                "message" => "Failed Action Error 403",
            ],403);
        }   
    }
    // 3 Edit
    public function SystemAccountEdit(Request $request,$id) {
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
            // **************************************************START**
            $edit    = SystemAccount::editSystemAccount($user,$data,$id);
            // ****************************************************END**
            if($edit == false){
                return response()->json([
                    "status"   => 405,
                    "message"  => " No Have System Accounts ",
                ],405);
            }
            return response([
                "status"    => 200,
                "value"     => $edit,
                "message"   => "Edit System Accounts Access Successfully",
            ]);
        }catch(Exception $e){   
            return response([
                "status"  => 403,
                "message" => "Failed Action Error 403",
            ],403);
        }   
    }
    // 4 Store
    public function SystemAccountStore(Request $request) {
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
            // ******************************************************START**
                $data         = $request->only([
                                    "pattern_id",
                                    "purchase",
                                    "purchase_tax",
                                    "sale",
                                    "sale_tax",
                                    "cheque_debit",
                                    "cheque_collection",
                                    "journal_expense_tax",
                                    "sale_return",
                                    "sale_discount",
                                    "purchase_return",
                                    "purchase_discount"
                ]);
                $save      = SystemAccount::storeSystemAccount($user,$data);
                if($save == false){
                    return response()->json([
                        "status"   => 403,
                        "message"  => " Failed Action ",
                    ],403);
                }
            // ********************************************************END**
            \DB::commit();
            return response([
                "status"   => 200,
                "message" => "Added System Accounts Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"  => 403,
                "message" => "Failed Action Error 403",
            ],403);
        } 
    }
    // 5 Update
    public function SystemAccountUpdate(Request $request,$id) {
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
            // *******************************************************START**
                $data         = $request->only([
                                "pattern_id",
                                "purchase",
                                "purchase_tax",
                                "sale",
                                "sale_tax",
                                "cheque_debit",
                                "cheque_collection",
                                "journal_expense_tax",
                                "sale_return",
                                "sale_discount",
                                "purchase_return",
                                "purchase_discount"
                ]);
                $update    = SystemAccount::updateSystemAccount($user,$data,$id);
                if($update == false){
                    return response()->json([
                        "status"   => 403,
                        "message"  => " No Have System Accounts ",
                    ],403);
                }
            // *********************************************************END**
            \DB::commit();
            return response([
                "status"   => 200,
                "message" => "Updated System Accounts Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"  => 403,
                "message" => "Failed Action Error 403",
            ],403);
        }
    }
    // 6 Delete
    public function SystemAccountDelete(Request $request,$id) {
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
            // **********************************************START**
            $del    = SystemAccount::deleteSystemAccount($user,$id);
            // ************************************************END**
            if($del == "false"){
                return response()->json([
                    "status"   => 403,
                    "message"  => " No Have System Accounts ",
                ],403);
            }elseif($del == "cannot"){
                return response()->json([
                    "status"   => 405,
                    "message"  => " Sorry , You Cannot Delete The System Account There Transactions Related ",
                ],405);
            }elseif($del == "true"){
                return response([
                    "status"   => 200,
                    "message" => "Deleted System Accounts Successfully",
                ]);
            }else{
                return response([
                    "status"  => 403,
                    "message" => "Failed Action Error 403",
                ],403);
            }
        }catch(Exception $e){
            return response([
                "status"  => 403,
                "message" => "Failed Action Error 403",
            ],403);
        }
    }
}
