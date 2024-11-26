<?php

namespace App\Http\Controllers\ApiController\FrontEnd;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FrontEnd\Cheques\ContactBank;

class ContactBankController extends Controller
{
    // 1 index
    public function ContactBank(Request $request) {
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
            $contactBank    = ContactBank::getContactBank($user);
            if($contactBank == false){
                return response()->json([
                    "status"   => 403,
                    "message"  => " No Have Contact Bank ",
                ],403);
            }
            return response([
                "status"   => 200,
                "value"    => $contactBank,
                "message"  => "Contact Bank Access Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"  => 403,
                "message" => "Failed Action Error 403",
            ],403);
        } 
    }
    // 2 create
    public function ContactBankCreate(Request $request) {
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
            $create    = ContactBank::createContactBank($user,$data);
            // *****************************************END**
            if($create == false){
                return response()->json([
                    "status"   => 403,
                    "message"  => " No Have Brand ",
                ],403);
            }
            return response([
                "status"   => 200,
                "value"    => $create,
                "message"  => "Create Brand Access Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"  => 403,
                "message" => "Failed Action Error 403",
            ],403);
        }
    }
    // 3 Edit
    public function ContactBankEdit(Request $request,$id) {
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
            $edit    = ContactBank::editContactBank($user,$data,$id);
            // ************************************END**
            if($edit == false){
                return response()->json([
                    "status"   => 403,
                    "message"  => " No Have Contact Bank ",
                ],403);
            }
            return response([
                "status"    => 200,
                "value"     => $edit,
                "message"   => "Edit Contact Bank Access Successfully",
            ]);
        }catch(Exception $e){   
            return response([
                "status"  => 403,
                "message" => "Failed Action Error 403",
            ],403);
        }  
    }
    // 4 Store
    public function ContactBankStore(Request $request) {
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
            
            // ************************************START**
                $data                = $request->only(["name","location_id","contact_id"]);
                $data["business_id"] = $user->business_id;
                $data["created_by"]  = $user->id;
                $save      = ContactBank::storeContactBank($user,$data,$request);
                if($save == false){
                    return response()->json([
                        "status"   => 403,
                        "message"  => " Failed Action ",
                    ],403);
                }
            // **************************************END**
            \DB::commit();
            return response([
                "status"   => 200,
                "message" => "Added Contact Bank Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"  => 403,
                "message" => "Failed Action Error 403",
            ],403);
        } 
    }
    // 5 Update
    public function ContactBankUpdate(Request $request,$id) {
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
            $data                = $request->only(["name","location_id","contact_id"]);
            $data["business_id"] = $user->business_id;
            $data["created_by"]  = $user->id;
            $update    = ContactBank::updateContactBank($user,$data,$id,$request);
            if($update == false){
                return response()->json([
                    "status"   => 403,
                    "message"  => " No Have Contact Bank ",
                ],403);
            }
            // *********************************************END**
            \DB::commit();
            return response([
                "status"   => 200,
                "message" => "Updated Contact Bank Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"  => 403,
                "message" => "Failed Action Error 403",
            ],403);
        } 
    }
    // 6 Delete
    public function ContactBankDelete(Request $request,$id) {
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
            $del    = ContactBank::deleteContactBank($user,$id);
            // ***********************************END**
            if($del == "no"){
                return response()->json([
                    "status"   => 403,
                    "message"  => " No Have Contact Bank ",
                ],403);
            }elseif($del == "false"){
                return response()->json([
                    "status"   => 403,
                    "message"  => "Sorry! You Can't Delete This Contact Bank Because Used From Cheques ",
                ],403);
            }else{
                return response([
                    "status"   => 200,
                    "message" => "Deleted Contact Bank Successfully",
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
