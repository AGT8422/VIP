<?php

namespace App\Http\Controllers\ApiController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CodeRequest;

class UserActivateController extends Controller
{
    //  ... request code activations 
    public function needCode(Request $request){

        try{
            
            \DB::beginTransaction();
            
            $CodeRequest               = new CodeRequest;
            
            $CodeRequest->name         = $request->name;
            $CodeRequest->type         = $request->type;
            $CodeRequest->company_name = $request->company_name;
            $CodeRequest->device_no    = $request->device_no;
            $CodeRequest->email        = $request->email;
            $CodeRequest->address      = $request->address;
            $CodeRequest->mobile       = $request->user_mobile;
            $CodeRequest->services     = $request->services;
            
            $CodeRequest->save();
            
            \DB::commit();
            $output = [
                "success"=>1,
                "msg"=>__("messages.added_successfully"),
            ];
            return response()->json([
                            "status"   => 200,
                            "message"  => " success ",
                            "output"   => $output

                        ]);
        }catch(Exception $e){
            \DB::rollback();
            \Log::emergency();
            \Log::alert($e);
            $output = [
                "success"=>0,
                "msg"=>__("messages.something_went_wrong"),
            ];
            
            return response()->json([
                                "status"   => 403,
                                "message"  => " Failed ",
                                "output"   => $output

                            ]); ;
        }
    }
    //  ... request code activations 
    public function codecheck(Request $request){

        try{
            $device_no = app("request")->input("device_no");
            \DB::beginTransaction();
            
            $CodeRequest         = \App\Models\UserActivation::where("user_username",$device_no)->first();
            
            if(!empty($CodeRequest)){
                $output = [
                    "success"=>1,
                    "msg"=>__("messages.version_activated"),
                ];
                return response()->json([
                                "status"   => 200,
                                "message"  => " Success ",
                                "jwt"      => $CodeRequest->user_token,
                                "mobile_no"=> $CodeRequest->user_number_device,
                                "output"   => $output
                            ]);
            }else{
                abort(404,"not found");
            }
        
            
            \DB::commit();
        }catch(Exception $e){
            \DB::rollback();
            \Log::emergency();
            \Log::alert($e);
            $output = [
                "success"=>0,
                "msg"=>__("messages.something_went_wrong"),
            ];
            
            return response()->json([
                                "status"   => 403,
                                "message"  => " Failed ",
                                "output"   => $output

                            ]); ;
        }
    }
}
