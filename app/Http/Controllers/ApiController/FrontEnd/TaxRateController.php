<?php

namespace App\Http\Controllers\ApiController\FrontEnd;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FrontEnd\Settings\TaxRate;

class TaxRateController extends Controller
{
    // 1 index
    public function TaxRate(Request $request) {
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
            $taxRate    = TaxRate::getTaxRate($user);
            if($taxRate == false){
                return response()->json([
                    "status"   => 403,
                    "message"  => " No Have TaxRates ",
                ],403);
            }
            return response([
                "status"   => 200,
                "value"    => $taxRate,
                "message"  => "TaxRates Access Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"  => 403,
                "message" => "Failed Action Error 403",
            ],403);
        } 
    }
    // 2 create
    public function TaxRateCreate(Request $request) {
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
            $create    = TaxRate::createTaxRate($user,$data);
            // *****************************************END**
            if($create == false){
                return response()->json([
                    "status"   => 403,
                    "message"  => " No Have TaxRate ",
                ],403);
            }
            return response([
                "status"   => 200,
                "value"    => $create,
                "message"  => "Create TaxRate Access Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"  => 403,
                "message" => "Failed Action Error 403",
            ],403);
        }
    }
    // 3 Edit
    public function TaxRateEdit(Request $request,$id) {
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
            $edit    = TaxRate::editTaxRate($user,$data,$id);
            // ************************************END**
            if($edit == false){
                return response()->json([
                    "status"   => 403,
                    "message"  => " No Have TaxRates ",
                ],403);
            }
            return response([
                "status"    => 200,
                "value"     => $edit,
                "message"   => "Edit TaxRates Access Successfully",
            ]);
        }catch(Exception $e){   
            return response([
                "status"  => 403,
                "message" => "Failed Action Error 403",
            ],403);
        }  
    }
    // 4 Store
    public function TaxRateStore(Request $request) {
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
                $data                = $request->only(["name","amount","for_tax_group"]);
                $data["business_id"] = $user->business_id;
                $data["created_by"]  = $user->id;
                $save      = TaxRate::storeTaxRate($user,$data,$request);
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
                        "message" => "Added TaxRate Successfully",
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
    public function TaxRateUpdate(Request $request,$id) {
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
            $data                = $request->only(["name","amount","for_tax_group"]);
            $data["business_id"] = $user->business_id;
            $data["created_by"]  = $user->id;
            $update    = TaxRate::updateTaxRate($user,$data,$id,$request);
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
                    "message"  => "Updated TaxRates Successfully",
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
    public function TaxRateDelete(Request $request,$id) {
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
            $del    = TaxRate::deleteTaxRate($user,$id);
            // ***********************************END**
            if($del == "no"){
                return response()->json([
                    "status"   => 403,
                    "message"  => " No Have TaxRates ",
                ],403);
            }elseif($del == "false"){
                return response()->json([
                    "status"   => 403,
                    "message"  => "Sorry! You Can't Delete This TaxRate Because Used From Products ",
                ],403);
            }else{
                return response([
                    "status"   => 200,
                    "message" => "Deleted TaxRates Successfully",
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
