<?php

namespace App\Http\Controllers\ApiController\FrontEnd;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FrontEnd\Contacts\CustomerGroup;

class CustomerGroupController extends Controller
{
    // 1 index
    public function CustomerGroup(Request $request) {
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
            $CustomerGroup    = CustomerGroup::getCustomerGroup($user);
            if($CustomerGroup == false){
                return response()->json([
                    "status"   => 403,
                    "message"  => " No Have CustomerGroups ",
                ],403);
            }
            return response([
                "status"           => 200,
                "value"            => $CustomerGroup,
                "message"          => "CustomerGroups Access Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"  => 403,
                "message" => "Failed Action Error 403",
            ],403);
        }   
    }
    // 2 create
    public function CustomerGroupCreate(Request $request) {
        try{
            $main_token   = $request->header("Authorization");
            $token        = substr($main_token,7);
            $data         = [];
            $data["price_collection_price"] = [
                    [
                        "key"   => "percentage",
                        "value" => "Percentage"
                    ],
                    [
                        "key"   => "selling_price_group",
                        "value" => "Sales Price Group"
                    ]
                ];
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
            $create    = CustomerGroup::createCustomerGroup($user,$data);
            // *****************************************************END**
            if($create == false){
                return response()->json([
                    "status"   => 403,
                    "message"  => " No Have Customer Groups ",
                ],403);
            }
            return response([
                "status"   => 200,
                "value"    => $create,
                "message"  => "Create Customer Groups Access Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"  => 403,
                "message" => "Failed Action Error 403",
            ],403);
        }    
    }
    // 3 Edit
    public function CustomerGroupEdit(Request $request,$id) {
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
            // **************************************************START**
            $edit    = CustomerGroup::editCustomerGroup($user,$data,$id);
            // *****************************************************END**
            if($edit == false){
                return response()->json([
                    "status"   => 403,
                    "message"  => " No Have Customer Groups ",
                ],403);
            }
            return response([
                "status"    => 200,
                "value"     => $edit,
                "message"   => "Edit Customer Groups Access Successfully",
            ]);
        }catch(Exception $e){   
            return response([
                "status"  => 403,
                "message" => "Failed Action Error 403",
            ],403);
        } 
    }
    // 4 Store
    public function CustomerGroupStore(Request $request) {
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
                $data                = $request->only(['name', 'amount', 'price_calculation_type', 'selling_price_group_id']);
                $data['business_id'] = $user->business_id;
                $data['created_by']  = $user->id;
                $save      = CustomerGroup::storeCustomerGroup($user,$data);
                if($save == false){
                    return response()->json([
                        "status"   => 403,
                        "message"  => " Failed Action ",
                    ],403);
                }
            // *******************************************************END**
            \DB::commit();
            return response([
                "status"   => 200,
                "message" => "Added Customer Groups Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"  => 403,
                "message" => "Failed Action Error 403",
            ],403);
        }
    }
    // 5 Update
    public function CustomerGroupUpdate(Request $request,$id) {
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
            $data                = $request->only(['name', 'amount', 'price_calculation_type', 'selling_price_group_id']);
            $data['business_id'] = $user->business_id;
            $data['created_by']  = $user->id;
            $update    = CustomerGroup::updateCustomerGroup($user,$data,$id);
            if($update == false){
                return response()->json([
                    "status"   => 403,
                    "message"  => " No Have Customer Group ",
                ],403);
            }
            // *******************************************************END**
            \DB::commit();
            return response([
                "status"   => 200,
                "message" => "Updated Customer Group Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"  => 403,
                "message" => "Failed Action Error 403",
            ],403);
        }
    }
    // 6 Delete
    public function CustomerGroupDelete(Request $request,$id) {
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
            $del    = CustomerGroup::deleteCustomerGroup($user,$id);
            // ***********************************END**
            if($del == false){
                return response()->json([
                    "status"   => 403,
                    "message"  => " No Have Customer Groups ",
                ],403);
            }
            return response([
                "status"   => 200,
                "message" => "Deleted Customer Groups Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"  => 403,
                "message" => "Failed Action Error 403",
            ],403);
        }
    }
}
