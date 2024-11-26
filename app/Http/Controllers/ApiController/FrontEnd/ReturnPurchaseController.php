<?php

namespace App\Http\Controllers\ApiController\FrontEnd;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FrontEnd\Purchases\ReturnPurchase;

class ReturnPurchaseController extends Controller
{
    // 01 index
    public function ReturnPurchase(Request $request) {
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
            $returnPurchase    = ReturnPurchase::getReturnPurchase($user,$request);
            if($returnPurchase == false){
                return response()->json([
                    "status"   => 200,
                    "error"    => true,
                    "message"  => " No Have Return Purchases ",
                ],200);
            }
            return response([
                "status"   => 200,
                "value"    => $returnPurchase,
                "error"    => false,
                "message"  => "Return Purchases Access Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"  => 200,
                "error"    => true,
                "message" => "Failed Action Error 403",
            ],200);
        }    
    }
    // 02 create
    public function ReturnPurchaseCreate(Request $request) {
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
            // *****************************************************START**
            $create    = ReturnPurchase::createReturnPurchase($user,$data);
            // *******************************************************END**
            if($create == false){
                return response()->json([
                    "status"   => 200,
                    "error"    => true,
                    "message"  => " No Have Return Purchases  ",
                ],200);
            }
            return response([
                "status"   => 200,
                "value"    => $create,
                "error"    => false,
                "message"  => "Create Return Purchases Access Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"   => 200,
                "error"    => true,
                "message"  => "Failed Action Error 403",
            ],200);
        }     
    }
    // 03 Edit
    public function ReturnPurchaseEdit(Request $request,$id) {
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
            // ****************************************************START**
            $edit    = ReturnPurchase::editReturnPurchase($user,$data,$id);
            // ******************************************************END**
            if($edit == false){
                return response()->json([
                    "status"   => 200,
                    "error"    => true,
                    "message"  => " No Have Return Purchase ",
                ],200);
            }
            return response([
                "status"    => 200,
                "value"     => $edit,
                "error"     => false,
                "message"   => "Edit Return Purchase Access Successfully",
            ]);
        }catch(Exception $e){   
            return response([
                "status"  => 200,
                "error"   => true,
                "message" => "Failed Action Error 403",
            ],200);
        } 
    }
    // 04 Store
    public function ReturnPurchaseStore(Request $request) {
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
        
            // *******************************************************START**
                $save      = ReturnPurchase::storeReturnPurchase($user,$request);
                if($save == false){
                    return response()->json([
                        "status"   => 200,
                        "error"    => true,
                        "message"  => " Failed Action ",
                    ],200);
                }
            // *********************************************************END**
            \DB::commit();
            return response([
                "status"   => 200,
                "error"    => false,
                "message" => "Added Return Purchases Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"   => 200,
                "error"    => true,
                "message"  => "Failed Action Error 403",
            ],200);
        } 
    }
    // 05 Update
    public function ReturnPurchaseUpdate(Request $request,$id) {
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
         
            // *********************************************************START**
            $update    = ReturnPurchase::updateReturnPurchase($user,$request,$id);
            if($update == false){
                return response()->json([
                    "status"   => 200,
                    "error"    => true,
                    "message"  => " No Have Return Purchase ",
                ],200);
            }
            // **********************************************************END**
            \DB::commit();
            return response([
                "status"   => 200,
                "error"    => false,
                "message" => "Updated Return Purchase Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"  => 200,
                "error"    => true,
                "message" => "Failed Action Error 403",
            ],200);
        }
    }
    // 06 Delete
    public function ReturnPurchaseDelete(Request $request,$id) {
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
            // *********************************START**
            $del    = ReturnPurchase::deleteReturnPurchase($user,$request,$id);
            // ***********************************END**
            if($del == "false"){
                return response()->json([
                    "status"   => 200,
                    "error"    => true,
                    "message"  => " No Have Return Purchase ",
                ],200);
            }else if($del == "OldReceived"){
                return response()->json([
                    "status"   => 200,
                    "error"    => true,
                    "message"  => "Sorry !!, Can't Delete This Purchase Return ",
                ],200);
            }else{
                \DB::commit();
                return response([
                    "status"   => 200,
                    "error"    => false,
                    "message" => "Deleted Return Purchase Successfully",
                ]);
            }
        }catch(Exception $e){
            return response([
                "status"  => 200,
                "error"   => true,
                "message" => "Failed Action Error 403",
            ],200);
        }
    }
    // 07 Entry
    public function ReturnPurchaseEntry(Request $request,$id) {
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
            $entry             = ReturnPurchase::entryReturnPurchase($user,$data,$id);
            if($entry == false){
                return response()->json([
                    "status"   => 200,
                    "error"    => true,
                    "message"  => " No Have Previous Entry",
                ],200);
            }
            // *********************************************END**
            \DB::commit();
            return response([
                "status"   => 200,
                "value"    => $entry,
                "error"    => false,
                "message"  => "Entry Access Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"   => 200,
                "error"    => true,
                "message"  => "Failed Action Error 403",
            ],200);
        }
    }
    // 08 Print
    public function ReturnPurchasePrint(Request $request,$id) {
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
            $print             = ReturnPurchase::printReturnPurchase($user,$data,$id);
            if($print == false){
                return response()->json([
                    "status"   => 200,
                    "error"    => true,
                    "message"  => " No Have Previous Purchase Return",
                ],200);
            }
            // *********************************************END**
            \DB::commit();
            return response([
                "status"   => 200,
                "value"    => $print,
                "error"    => true,
                "message"  => "Print Access Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"   => 200,
                "error"    => true,
                "message"  => "Failed Action Error 403",
            ],200);
        }
    }
    // 09 View
    public function ReturnPurchaseView(Request $request,$id) {
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
            $view             = ReturnPurchase::viewReturnPurchase($user,$data,$id);
            if($view == false){
                return response()->json([
                    "status"   => 200,
                    "error"    => true,
                    "message"  => " No Have Previous Purchase Return",
                ],200);
            }
            // *********************************************END**
            \DB::commit();
            return response([
                "status"   => 200,
                "value"    => $view,
                "error"    => false,
                "message"  => "View Access Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"   => 200,
                "error"    => true,
                "message"  => "Failed Action Error 403",
            ],200);
        }
    }
    // 10 Map
    public function ReturnPurchaseMap(Request $request,$id) {
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
            $map             = ReturnPurchase::mapReturnPurchase($user,$data,$id);
            if($map == false){
                return response()->json([
                    "status"   => 200,
                    "error"    => true,
                    "message"  => " No Have Previous Movement",
                ],200);
            }
            // *********************************************END**
            \DB::commit();
            return response([
                "status"   => 200,
                "value"    => $map,
                "error"    => false,
                "message"  => "Map Access Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"  => 200,
                "error"   => true,
                "message" => "Failed Action Error 403",
            ],200);
        }
    }
    // 11 Attachment
    public function ReturnPurchaseAttachment(Request $request,$id) {
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
            $currency             = ReturnPurchase::attachReturnPurchase($user,$data,$id);
            if($currency == false){
                return response()->json([
                    "status"   => 200,
                    "error"    => true,
                    "message"  => " No Have Previous Attachment",
                ],200);
            }
            // *********************************************END**
            \DB::commit();
            return response([
                "status"   => 200,
                "value"    => $currency,
                "error"    => false,
                "message"  => "Attachment Access Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"  => 200,
                "error"    => true,
                "message" => "Failed Action Error 403",
            ],200);
        }
    }
    // 12 Add Payment
    public function ReturnPurchaseAddPayment(Request $request,$id) {
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
            $payments         = ReturnPurchase::addReturnPaymentPurchase($user,$data,$id);
            if($payments == false){
                return response()->json([
                    "status"   => 200,
                    "error"    => true,
                    "message"  => " No Have Previous Purchase Return",
                ],200);
            }
            // *********************************************END**
            \DB::commit();
            return response([
                "status"   => 200,
                "value"    => $payments,
                "error"    => false,
                "message"  => "Map Access Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"   => 200,
                "error"    => true,
                "message"  => "Failed Action Error 403",
            ],200);
        }
    }
    // 13 View Payment
    public function ReturnPurchaseViewPayment(Request $request,$id) {
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
            $payments         = ReturnPurchase::viewReturnPaymentPurchase($user,$data,$id);
            if($payments == false){
                return response()->json([
                    "status"   => 200,
                    "error"    => true,
                    "message"  => " No Have Previous Payments",
                ],200);
            }
            // *********************************************END**
            \DB::commit();
            return response([
                "status"   => 200,
                "value"    => $payments,
                "error"    => false,
                "message"  => "Payments Access Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"  => 200,
                "error"    => true,
                "message" => "Failed Action Error 403",
            ],200);
        }
    }
    // 14 Get Update Status 
    public function ReturnPurchaseUpdateStatus(Request $request,$id) {
        try{
            $main_token   = $request->header("Authorization");
            $token        = substr($main_token,7);
            $data         = $request->only(["status","contact_id"]);
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
            $status             = ReturnPurchase::updateStatusReturnPurchase($user,$data,$id);
            if($status == false){
                return response()->json([
                    "status"   => 200,
                    "error"    => true,
                    "message"  => " No Have Previous Purchase Return",
                ],200);
            }
            // *********************************************END**
            \DB::commit();
            return response([
                "status"   => 200,
                "value"    => $status,
                "error"    => true,
                "message"  => "Get Status Access Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"   => 200,
                "error"    => true,
                "message"  => "Failed Action Error 403",
            ],200);
        }
    }
    // 15 Update Status
    public function ReturnPurchaseGetUpdateStatus(Request $request,$id) {
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
            $status             = ReturnPurchase::getUpdateStatusReturnPurchase($user,$data,$id);
            if($status == false){
                return response()->json([
                    "status"   => 200,
                    "error"    => true,
                    "message"  => " No Have Previous Purchase Return",
                ],200);
            }
            // *********************************************END**
            \DB::commit();
            return response([
                "status"   => 200,
                "value"    => $status,
                "error"    => false,
                "message"  => "Get Status Access Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"  => 200,
                "error"    => true,
                "message" => "Failed Action Error 403",
            ],200);
        }
    }

    // ******* Old Purchase Section ******* \\ 
    // 1 save  old purchase Return
    public function ReturnOldPurchaseStore(Request $request,$id) {
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
                $save             = ReturnPurchase::storeReturnOldPurchase($user,$request,$id);
                if($save == false){
                    return response()->json([
                        "status"   => 200,
                        "error"    => true,
                        "message"  => " Failed Action ",
                    ],200);
                }
            // *********************************************************END**
            \DB::commit();
            return response([
                "status"   => 200,
                "error"    => false,
                "message"  => "Added Return Old Purchases Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"  => 200,
                "error"    => true,
                "message" => "Failed Action Error 403",
            ],200);
        }
    }
    // 2 update  old purchase Return
    public function ReturnOldPurchaseUpdate(Request $request,$id) {
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
                $save             = ReturnPurchase::updateReturnOldPurchase($user,$request,$id);
                if($save == false){
                    return response()->json([
                        "status"   => 200,
                        "error"    => true,
                        "message"  => " Failed Action ",
                    ],200);
                }
            // *********************************************************END**
            \DB::commit();
            return response([
                "status"   => 200,
                "error"    => false,
                "message"  => "Added Return Old Purchases Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"  => 200,
                "error"    => true,
                "message" => "Failed Action Error 403",
            ],200);
        }
    }
    // 3 Create  old purchase Return
    public function ReturnOldPurchaseCreate(Request $request,$id) {
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
                $save             = ReturnPurchase::createReturnOldPurchase($user,$request,$id);
                if($save == false){
                    return response()->json([
                        "status"   => 200,
                        "error"    => true,
                        "message"  => " Failed Action ",
                    ],200);
                }
            // *********************************************************END**
            \DB::commit();
            return response([
                "status"   => 200,
                "error"    => false,
                "message"  => "Added Return Old Purchases Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"  => 200,
                "error"    => true,
                "message" => "Failed Action Error 403",
            ],200);
        }
    }
    // 4 Edit  old purchase Return
    public function ReturnOldPurchaseEdit(Request $request,$id) {
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
                $save             = ReturnPurchase::editReturnOldPurchase($user,$request,$id);
                if($save == false){
                    return response()->json([
                        "status"   => 200,
                        "error"    => true,
                        "message"  => " Failed Action ",
                    ],200);
                }
            // *********************************************************END**
            \DB::commit();
            return response([
                "status"   => 200,
                "error"    => false,
                "message"  => "Added Return Old Purchases Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"  => 200,
                "error"    => true,
                "message" => "Failed Action Error 403",
            ],200);
        }
    }
    
    
    // ******* Received Section ******* \\ 
    // 1 Get All Received 
    public function ReturnPurchaseAllReceived(Request $request){
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
            $purchase    = ReturnPurchase::getAllReturnPurchaseReceived($user,$request);
            if($purchase == false){
                return response()->json([
                    "status"   => 200,
                    "error"    => true,
                    "message"  => " No Have Receives",
                ],200);
            }
            return response([
                "status"   => 200,
                "value"    => $purchase,
                "error"    => false,
                "message"  => "Received Access Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"  => 200,
                "error"   => true,
                "message" => "Failed Action Error 403",
            ],200);
        } 
    }
    // 2 Get Return Purchase Received 
    public function ReturnPurchaseReceived(Request $request,$id){
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
            $purchase    = ReturnPurchase::getReturnPurchaseReceived($user,$request,$id);
            if($purchase == false){
                return response()->json([
                    "status"   => 200,
                    "error"    => true,
                    "message"  => " No Have Old Received For This Purchase Return",
                ],200);
            }
            return response([
                "status"   => 200,
                "value"    => $purchase,
                "error"    => false,
                "message"  => "Purchase Return Received Access Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"   => 200,
                "error"    => true,
                "message"  => "Failed Action Error 403",
            ],200);
        } 
    }
    // 3 Create Return Purchase Received 
    public function ReturnPurchaseCreateReceived(Request $request){
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
            $purchase    = ReturnPurchase::createReturnPurchaseReceived($user,$request);
            if($purchase == false){
                return response()->json([
                    "status"   => 200,
                    "error"    => true,
                    "message"  => " There is error ",
                ],200);
            }
            return response([
                "status"   => 200,
                "value"    => $purchase,
                "error"    => false,
                "message"  => "Create Purchase Return Received Access Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"  => 200,
                "error"    => true,
                "message" => "Failed Action Error 403",
            ],200);
        } 
    }
    // 4 Edit Purchase Received 
    public function ReturnPurchaseEditReceived(Request $request,$id){
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
            $purchase    = ReturnPurchase::editReturnPurchaseReceived($user,$request,$id);
            if($purchase == false){
                return response()->json([
                    "status"   => 200,
                    "error"    => true,
                    "message"  => " No Have Purchase Return Received ",
                ],200);
            }
            return response([
                "status"   => 200,
                "value"    => $purchase,
                "error"    => false,
                "message"  => "Edit Purchase Received Access Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"   => 200,
                "error"    => true,
                "message"  => "Failed Action Error 403",
            ],200);
        } 
    }
    // 5 Save Return Purchase Received 
    public function ReturnPurchaseReceivedStore(Request $request){
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
            // ......................................................START.
            $purchase    = ReturnPurchase::saveReturnPurchaseReceived($user,$request);
            // .......................................................END.. 
            if($purchase == false){
                return response()->json([
                    "status"   => 200,
                    "error"    => true,
                    "message"  => " There is error ",
                ],200);
            }
            return response([
                "status"   => 200, 
                "error"    => false,
                "message"  => "Purchase Return Received Saved Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"  => 200,
                "error"    => true,
                "message" => "Failed Action Error 403",
            ],200);
        } 
    }
    // 6 Update Return Purchase Received 
    public function ReturnPurchaseReceivedUpdate(Request $request,$id){
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
            // ...........................................................START..
            $data = $request->only([
                ""
            ]);
            $shipping_data = $request->only([
                ""
            ]); 
            $purchase    = ReturnPurchase::updateReturnPurchaseReceived($user,$request,$id);
            // ...........................................................END....
            if($purchase == false){
                return response()->json([
                    "status"   => 200,
                    "error"    => true,
                    "message"  => " There is error ",
                ],200);
            }
            return response([
                "status"   => 200, 
                "error"    => false,
                "message"  => "Purchase Return Received Updated Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"  => 200,
                "error"    => true,
                "message" => "Failed Action Error 403",
            ],200);
        } 
    }
    // 7 View Return Purchase Received 
    public function ReturnPurchaseViewReceived(Request $request,$id){
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
            $purchase    = ReturnPurchase::viewReturnPurchaseReceived($user,$request,$id);
            if($purchase == false){
                return response()->json([
                    "status"   => 200,
                    "error"    => true,
                    "message"  => " No Have Purchase Return Received ",
                ],200);
            }
            return response([
                "status"   => 200,
                "value"    => $purchase,
                "error"    => false,
                "message"  => "View Purchase Return Received Access Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"  => 200,
                "error"    => true,
                "message" => "Failed Action Error 403",
            ],200);
        } 
    }
    // 8 Print Return Purchase Received 
    public function ReturnPurchasePrintReceived(Request $request,$id){
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
            $purchase    = ReturnPurchase::printReturnPurchaseReceived($user,$request,$id);
            if($purchase == false){
                return response()->json([
                    "status"   => 200,
                    "error"    => true,
                    "message"  => " No Have Purchase Return Received ",
                ],200);
            }
            return response([
                "status"   => 200,
                "value"    => $purchase,
                "error"    => false,
                "message"  => "Purchase Return Received Note Access Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"  => 200,
                "error"    => true,
                "message" => "Failed Action Error 403",
            ],200);
        } 
    }
    // 9 Attachment Return Purchase Received
    public function ReturnPurchaseAttachmentReceived(Request $request,$id) {
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
            $attach             = ReturnPurchase::attachReturnPurchaseReceived($user,$request,$id);
            if($attach == false){
                return response()->json([
                    "status"   => 200,
                    "error"    => true,
                    "message"  => " No Have Previous Attachment",
                ],200);
            }
            // *********************************************END**
            \DB::commit();
            return response([
                "status"   => 200,
                "value"    => $attach,
                "error"    => false,
                "message"  => "Attachment Access Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"   => 200,
                "error"    => true,
                "message"  => "Failed Action Error 403",
            ],200);
        }
    }
    // 10 Delete Return Purchase Received 
    public function ReturnPurchaseReceivedDelete(Request $request,$id){
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
            $del    =  ReturnPurchase::deleteReturnPurchaseReceived($user,$request,$id);
            if($del == "notFound"){
                return response()->json([
                    "status"   => 200,
                    "error"    => true,
                    "message"  => " No Have Purchase Return Received ",
                ],200);
            }else if($del == "related"){
                return response()->json([
                    "status"   => 200,
                    "error"    => true,
                    "message"  => "Can't Delete, There are relations on this purchase ",
                ],200);
            }else{
                return response([
                    "status"   => 200,
                    "error"    => false,
                    "message" => "Deleted Purchase Return Received Successfully",
                ]);
            } 
        }catch(Exception $e){
            return response([
                "status"  => 200,
                "error"    => true,
                "message" => "Failed Action Error 403",
            ],200);
        }
    }

}
