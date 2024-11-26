<?php

namespace App\Http\Controllers\ApiController\FrontEnd;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FrontEnd\AccountTypes\AccountType;

class AccountTypeController extends Controller
{
    // ** 1** list of Account Type
    public function AccountType(Request $request) {
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
            // *************************************START**
            $check    = AccountType::getAccountType($user);
            // ***************************************END**   
            if($check == false){
                return response()->json([
                    "status"   => 403,
                    "message"  => "No Have  Account Type  ",
                ],403);
            }
            return response([
                "status"   => 200,
                "value"    => $check,
                "message"  => " Account Type  Access Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"  => 403,
                "message" => "Failed Actions",
            ],403);
        
        }
    }
    // ** 2** Create Account Type
    public function AccountTypeCreate(Request $request) {
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
            $data     = [];
            // **********************************************START**
            $check    = AccountType::createAccountType($user,$data);
            // ************************************************END**   
            if($check == false){
                return response()->json([
                    "status"   => 403,
                    "message"  => "No Have Account Type",
                ],403);
            }
            return response([
                "status"   => 200,
                "value"    => $check,
                "message"  => "Create Account Type Access Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"  => 403,
                "message" => "Failed Actions",
            ],403);
        
        }
    }
    // ** 3** Edit Account Type 
    public function AccountTypeEdit(Request $request,$id) {
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
            // ************************************************START**
            $check    = AccountType::editAccountType($user,$data,$id);
            // **************************************************END**   
            if($check == false){
                return response()->json([
                    "status"   => 403,
                    "message"  => "No Have Account Type",
                ],403);
            }
            return response([
                "status"   => 200,
                "value"    => $check,
                "message"  => "Edit Account Type Access Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"  => 403,
                "message" => "Failed Actions",
            ],403);
        
        }
    }
    // ** 4** Store Account Type
    public function AccountTypeStore(Request $request) {
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
            // *********************************************START**
            $data     = $request->only([
                            "name",
                            "code",
                            "parent_account_type_id"
                        ]); 
            $check    = AccountType::storeAccountType($user,$data);
            // ***********************************************END**   
            if($check == "old"){
                return response()->json([
                    "status"   => 403,
                    "message"  => "Sorry, This Name Of Account Type is Already Exist",
                ],403);
            }elseif($check == "oldN"){
                return response()->json([
                    "status"   => 403,
                    "message"  => "Sorry, This  Number Of Account Type is Already Exist",
                ],403);
            }elseif($check == "false"){
                return response([
                    "status"   => 403,
                    "message"  => "Failed Actions",
                ],403);
            }else{
                \DB::commit();
                return response([
                    "status"   => 200,
                    "message"  => "Account Type Added Successfully",
                ],200);
            }
        }catch(Exception $e){
            return response([
                "status"  => 403,
                "message" => "Failed Actions",
            ],403);
        
        }
    }
    // ** 5** Update Account Type
    public function AccountTypeUpdate(Request $request,$id) {
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
            // ***************************************************START**
            $data     = $request->only([
                                "name",
                                "code",
                                "parent_account_type_id"
                        ]); 
            $check    = AccountType::updateAccountType($user,$data,$id);
            // *****************************************************END**   
            if($check == "old"){
                return response()->json([
                    "status"   => 403,
                    "message"  => "Sorry, This Name Of Account Type is Already Exist",
                ],403);
            }elseif($check == "oldN"){
                return response()->json([
                    "status"   => 403,
                    "message"  => "Sorry, This  Number Of Account Type is Already Exist",
                ],403);
            }elseif($check == "haveOld"){
                return response()->json([
                    "status"   => 403,
                    "message"  => "Sorry, This Account Type Has a Previous Transaction ",
                ],403);
            }elseif($check == "false"){
                return response()->json([
                    "status"   => 403,
                    "message"  => "Failed Actions",
                ],403);
            }else{
                \DB::commit();
                return response([
                    "status"   => 200,
                    "message"  => "Account Type Updated Successfully",
                ]);
            }
        }catch(Exception $e){
            return response([
                "status"  => 403,
                "message" => "Failed Actions",
            ],403);
        
        }
    }
    // ** 6** Delete Account Type
    public function AccountTypeDelete(Request $request,$id) {
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
            // **********************************************START**
            $check    = AccountType::deleteAccountType($user,$id);
            // ************************************************END**
           
            if($check == "false"){
                return response()->json([
                    "status"   => 403,
                    "message"  => " No Have Account Type  ",
                ],403);
            }elseif($check == "related"){
                return response()->json([
                    "status"   => 405,
                    "message"  => " Sorry ! You Cannot Delete The Account Type , It's have Movements ",
                ],405);
            }elseif($check == "haveOld"){
                return response()->json([
                    "status"   => 403,
                    "message"  => "Sorry, This Account Type Has a Previous Transaction ",
                ],403);
            }elseif($check == "true"){
                return response([
                    "status"   => 200,
                    "message"  => "Account Type Deleted Successfully",
                ]);
            }else{
                return response([
                    "status"  => 403,
                    "message" => "Failed Actions",
                ],403);
            }
           
        }catch(Exception $e){
            return response([
                "status"  => 403,
                "message" => "Failed Actions",
            ],403);
        
        }
    }
    // ** 7** Entries   
    public function Entries(Request $request) {
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
            // ***********************************START**
            $entries    = Account::entries($user);
            // *************************************END**
            if($entries ==  false ){
                return response()->json([
                    "status"   => 403,
                    "message"  => " No Have Entries  ",
                ],403);
            }else{
                return response([
                    "status"  => 200,
                    "value"   => $entries,
                    "message" => "Entries Access Successfully",
                ],200);
            }
           
        }catch(Exception $e){
            return response([
                "status"  => 403,
                "message" => "Failed Actions",
            ],403);
        
        }
    }
    // ** 8** list of Account Type Type tree
    public function AccountTypeTree(Request $request) {
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
            // **********************************START**
            $check    = AccountType::getAccountTypeTree($user);
            // ************************************END**   
            if($check == false){
                return response()->json([
                    "status"   => 403,
                    "message"  => "No Have  Account Type  ",
                ],403);
            }
            return response([
                "status"   => 200,
                "value"    => $check,
                "message"  => " Account Type  Access Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"  => 403,
                "message" => "Failed Actions",
            ],403);
        
        }
    }
    // ******.............................****** \\
}
