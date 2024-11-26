<?php

namespace App\Http\Controllers\ApiController\FrontEnd;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FrontEnd\Accounts\Account;

class AccountController extends Controller
{
    // ** 1** list of cash
    public function cashAccount(Request $request) {
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
            // ***************************START**
            $check    = Account::cashList($user);
            // *****************************END**   
            if($check == false){
                return response()->json([
                    "status"   => 403,
                    "message"  => " No Have Cash Accounts ",
                ],403);
            }
            return response([
                "status"   => 200,
                "value"    => $check,
                "message"  => "Cash Accounts Access Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"  => 403,
                "message" => "Failed Actions",
            ],403);
        
        }
    }
    // ** 2** list of bank
    public function bankAccount(Request $request) {
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
            // ***************************START**
            $check    = Account::bankList($user);
            // *****************************END**
            if($check == false){
                return response()->json([
                    "status"   => 403,
                    "message"  => " No Have Bank Accounts ",
                ],403);
            }
            return response([
                "status"   => 200,
                "value"    => $check,
                "message"  => "Bank Accounts Access Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"  => 403,
                "message" => "Failed Actions",
            ],403);
        
        }
    }
    // ** 3** Store of cash
    public function cashAccountStore(Request $request) {
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
            // *********************************START**
            $data = $request->only([
                            "name",
                            "note"
            ]);
            $data["business_id"] = $user->business_id;
            $data["created_by"]  = $user->id;
            $check    = Account::cashStore($user,$data);
            // ********************************END**   
            if($check == "failed"){
                return response()->json([
                    "status"   => 403,
                    "message"  => " Failed Actions ! ",
                ],403);
            }elseif($check == "old"){
                return response()->json([
                    "status"   => 405,
                    "message"  => " Sorry !, This Name is Already Exist ",
                ],405);
            }elseif($check == "true"){
                \DB::commit();
                return response([
                    "status"   => 200,
                    "message"  => "Cash Accounts Added Successfully",
                ]);
            }else{
                return response()->json([
                    "status"   => 403,
                    "message"  => " Failed Actions ",
                ],403);
            }
        }catch(Exception $e){
            return response([
                "status"  => 403,
                "message" => "Failed Actions",
            ],403);
        
        }
    }
    // ** 4** Store of bank
    public function bankAccountStore(Request $request) {
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
            \DB::beginTransaction();
            // ***********************************START**
            $data = $request->only([
                "name",
                "note"
            ]);
            $data["business_id"] = $user->business_id;
            $data["created_by"]  = $user->id;
            $check    = Account::bankStore($user,$data);
            // *************************************END**
            if($check == "failed"){
                return response()->json([
                    "status"   => 403,
                    "message"  => " Failed Actions ! ",
                ],403);
            }elseif($check == "old"){
                return response()->json([
                    "status"   => 405,
                    "message"  => " Sorry !, This Name is Already Exist ",
                ],405);
            }elseif($check == "true"){
                \DB::commit();
                return response([
                    "status"   => 200,
                    "message"  => "Bank Accounts Added Successfully",
                ]);
            }else{
                return response()->json([
                    "status"   => 403,
                    "message"  => " Failed Actions ",
                ],403);
            }
        }catch(Exception $e){
            return response([
                "status"  => 403,
                "message" => "Failed Actions",
            ],403);
        
        }
    }
    // ** 3** Update of cash
    public function cashAccountUpdate(Request $request,$id) {
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
            // ******************************************START**
            $data = $request->only([
                            "name",
                            "account_number",
                            "note"
            ]);
            $data["business_id"] = $user->business_id;
            $data["created_by"]  = $user->id;
            $check    = Account::cashUpdate($user,$data,$id);
            // *****************************************END**   
            if($check == "failed"){
                return response()->json([
                    "status"   => 403,
                    "message"  => " Failed Actions ! ",
                ],403);
            }elseif($check == "old"){
                return response()->json([
                    "status"   => 405,
                    "message"  => " Sorry !, This Name is Already Exist ",
                ],405);
            }elseif($check == "true"){
                \DB::commit();
                return response([
                    "status"   => 200,
                    "message"  => "Cash Accounts Updated Successfully",
                ]);
            }else{
                return response()->json([
                    "status"   => 403,
                    "message"  => " Failed Actions ",
                ],403);
            }
        }catch(Exception $e){
            return response([
                "status"  => 403,
                "message" => "Failed Actions",
            ],403);
        
        }
    }
    // ** 4** Update of bank
    public function bankAccountUpdate(Request $request,$id) {
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
            // ***************************************START**
            $data = $request->only([
                "name",
                "account_number",
                "note"
            ]);
            $data["business_id"] = $user->business_id;
            $data["created_by"]  = $user->id;
            $check    = Account::bankUpdate($user,$data,$id);
            // *****************************************END**
            if($check == "failed"){
                return response()->json([
                    "status"   => 403,
                    "message"  => " Failed Actions ! ",
                ],403);
            }elseif($check == "old"){
                return response()->json([
                    "status"   => 405,
                    "message"  => " Sorry !, This Name is Already Exist ",
                ],405);
            }elseif($check == "true"){
                \DB::commit();
                return response([
                    "status"   => 200,
                    "message"  => "Bank Accounts Updated Successfully",
                ]);
            }else{
                return response()->json([
                    "status"   => 403,
                    "message"  => " Failed Actions ",
                ],403);
            }
        }catch(Exception $e){
            return response([
                "status"  => 403,
                "message" => "Failed Actions",
            ],403);
        
        }
    }
    // ** 5** Create of cash
    public function cashAccountCreate(Request $request) {
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
            // ***************************START**
            $check    = Account::cashCreate($user);
            // *****************************END**   
            if($check == false){
                return response()->json([
                    "status"   => 403,
                    "message"  => " No Have Permission ",
                ],403);
            }
            return response([
                "status"   => 200,
                "value"    => $check,
                "message"  => "Create Cash Accounts Access Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"  => 403,
                "message" => "Failed Actions",
            ],403);
        
        }
    }
    // ** 6** Create of bank
    public function bankAccountCreate(Request $request) {
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
            // ***************************START**
            $check    = Account::bankCreate($user);
            // *****************************END**
            if($check == false){
                return response()->json([
                    "status"   => 403,
                    "message"  => " No Have Permissions ",
                ],403);
            }
            return response([
                "status"   => 200,
                "value"    => $check,
                "message"  => "Create Bank Accounts Access Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"  => 403,
                "message" => "Failed Actions",
            ],403);
        
        }
    }
    // ** 6** Delete  Account
    public function AccountDelete(Request $request,$id) {
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
            $check    = Account::deleteAccount($user,$id);
            // *************************************END**
           
            if($check == "false"){
                return response()->json([
                    "status"   => 403,
                    "message"  => " No Have Account  ",
                ],403);
            }elseif($check == "related"){
                return response()->json([
                    "status"   => 405,
                    "message"  => " Sorry ! You Cannot Delete The Account , It's have Movements ",
                ],405);
            }elseif($check == "haveOld"){
                return response()->json([
                    "status"   => 403,
                    "message"  => "Sorry, This Account Has a Previous Transaction ",
                ],403);
            }elseif($check == "true"){
                return response([
                    "status"   => 200,
                    "message"  => "Accounts Deleted Successfully",
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
    // ******.............................****** \\
    // ** 1** list of Account
    public function Account(Request $request) {
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
            // ***************************START**
            $check    = Account::getAccount($user);
            // *****************************END**   
            if($check == false){
                return response()->json([
                    "status"   => 403,
                    "message"  => "No Have  Account  ",
                ],403);
            }
            return response([
                "status"   => 200,
                "value"    => $check,
                "message"  => " Account  Access Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"  => 403,
                "message" => "Failed Actions",
            ],403);
        
        }
    }
    // ** 2** Create Account
    public function AccountCreate(Request $request) {
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
            // ***************************************START**
            $check    = Account::createAccount($user,$data);
            // *****************************************END**   
            if($check == false){
                return response()->json([
                    "status"   => 403,
                    "message"  => "No Have Account",
                ],403);
            }
            return response([
                "status"   => 200,
                "value"    => $check,
                "message"  => "Create Account Access Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"  => 403,
                "message" => "Failed Actions",
            ],403);
        
        }
    }
    // ** 3** Edit Account
    public function AccountEdit(Request $request,$id) {
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
            // ***************************************START**
            $check    = Account::editAccount($user,$data,$id);
            // *****************************************END**   
            if($check == false){
                return response()->json([
                    "status"   => 403,
                    "message"  => "No Have Account",
                ],403);
            }
            return response([
                "status"   => 200,
                "value"    => $check,
                "message"  => "Edit Account Access Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"  => 403,
                "message" => "Failed Actions",
            ],403);
        
        }
    }
    // ** 4** Store Account
    public function AccountStore(Request $request) {
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
            // ***************************************START**
            $data     = $request->only([
                            "name",
                            "account_number",
                            "account_type_id",
                            "note"
                        ]); 
            $check    = Account::storeAccount($user,$data);
            // *****************************************END**   
            if($check == "old"){
                return response()->json([
                    "status"   => 403,
                    "message"  => "Sorry, This Name Of Account is Already Exist",
                ],403);
            }elseif($check == "oldN"){
                return response()->json([
                    "status"   => 403,
                    "message"  => "Sorry, This  Number Of Account is Already Exist",
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
                    "message"  => "Account Added Successfully",
                ],200);
            }
        }catch(Exception $e){
            return response([
                "status"  => 403,
                "message" => "Failed Actions",
            ],403);
        
        }
    }
    // ** 5** Update Account
    public function AccountUpdate(Request $request,$id) {
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
            // *****************************************START**
            $data     = $request->only([
                            "name",
                            "account_number",
                            "account_type_id",
                            "note"
                        ]); 
            $check    = Account::updateAccount($user,$data,$id);
            // *******************************************END**   
            if($check == "old"){
                return response()->json([
                    "status"   => 403,
                    "message"  => "Sorry, This Name Of Account is Already Exist",
                ],403);
            }elseif($check == "oldN"){
                return response()->json([
                    "status"   => 403,
                    "message"  => "Sorry, This  Number Of Account is Already Exist",
                ],403);
            }elseif($check == "haveOld"){
                return response()->json([
                    "status"   => 403,
                    "message"  => "Sorry, This Account Has a Previous Transaction ",
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
                    "message"  => "Account Updated Successfully",
                ]);
            }
        }catch(Exception $e){
            return response([
                "status"  => 403,
                "message" => "Failed Actions",
            ],403);
        
        }
    }
    // ** 6** list of Account tree
    public function AccountTree(Request $request) {
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
            $check    = Account::getAccountTree($user);
            // ************************************END**   
            if($check == false){
                return response()->json([
                    "status"   => 403,
                    "message"  => "No Have  Account  ",
                ],403);
            }
            return response([
                "status"   => 200,
                "value"    => $check,
                "message"  => " Account  Access Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"  => 403,
                "message" => "Failed Actions",
            ],403);
        
        }
    }
    // ** 7** list of Account tree
    public function viewEntry(Request $request,$id) {
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
            $data     = $request->only(["type"]);
            $check    = Account::viewEntry($user,$data,$id);
            // ****************************************END**   
            if($check == false){
                return response()->json([
                    "status"   => 403,
                    "message"  => "No Have  Entry  ",
                ],403);
            }
            return response([
                "status"   => 200,
                "value"    => $check,
                "message"  => " Entry  Access Successfully",
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
