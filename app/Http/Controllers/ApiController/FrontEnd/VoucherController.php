<?php

namespace App\Http\Controllers\ApiController\FrontEnd;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FrontEnd\Vouchers\Voucher;

class VoucherController extends Controller
{
    // 1 index
    public function Voucher(Request $request) {
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
            // **** Filter
            $startDate     = request()->input("startDate");
            $endDate       = request()->input("endDate");
            $year          = request()->input("year");
            $month         = request()->input("month");
            $day           = request()->input("day");
            $week          = request()->input("week");

            $filter    = [
                "startDate"     => $startDate,
                "endDate"       => $endDate,
                "year"          => $year,
                "month"         => $month,
                "day"           => $day,
                "week"          => $week,
            ];
            
            $voucher    = Voucher::getVoucher($user,$filter);
            if($voucher == false){
                return response()->json([
                    "status"   => 200,
                    "message"  => " No Have Vouchers ",
                ],200);
            }
            return response([
                "status"   => 200,
                "value"    => $voucher,
                "message"  => "Vouchers Access Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"  => 403,
                "message" => "Failed Action Error 403",
            ],403);
        }    
    }
    // 2 create
    public function VoucherCreate(Request $request) {
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
            // *******************************************START**
            $create    = Voucher::createVoucher($user,$data);
            // *********************************************END**
            if($create == false){
                return response()->json([
                    "status"   => 200,
                    "message"  => " No Have Vouchers  ",
                ],200);
            }
            return response([
                "status"   => 200,
                "value"    => $create,
                "message"  => "Create Vouchers Access Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"  => 403,
                "message" => "Failed Action Error 403",
            ],403);
        }   
    }
    // 3 Edit
    public function VoucherEdit(Request $request,$id) {
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
            $edit    = Voucher::editVoucher($user,$data,$id);
            // ****************************************END**
            if($edit == false){
                return response()->json([
                    "status"   => 200,
                    "message"  => " No Have Vouchers ",
                ],200);
            }
            return response([
                "status"    => 200,
                "value"     => $edit,
                "message"   => "Edit Vouchers Access Successfully",
            ]);
        }catch(Exception $e){   
            return response([
                "status"  => 403,
                "message" => "Failed Action Error 403",
            ],403);
        }  
    }
    // 4 Store
    public function VoucherStore(Request $request) {
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
                                "amount",
                                "account_id",
                                "contact_id",
                                "type",
                                "currency_id",
                                "amount_currency",
                                "currency_id_amount",
                                "text",
                                "date"
            ]);
                $save      =  Voucher::storeVoucher($user,$data,$request);
                if($save == false){
                    return response()->json([
                        "status"   => 403,
                        "message"  => " Failed Action ",
                    ],403);
                }
            // **********************************************END**
            \DB::commit();
            return response([
                "status"   => 200,
                "message" => "Added Vouchers Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"  => 403,
                "message" => "Failed Action Error 403",
            ],403);
        } 
    }
    // 5 Update
    public function VoucherUpdate(Request $request,$id) {
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
                            "amount",
                            "account_id",
                            "contact_id",
                            "type",
                            "currency_id",
                            "amount_currency",
                            "currency_id_amount",
                            "text",
                            "date"
            ]);
            $update    = Voucher::updateVoucher($user,$data,$id,$request);
            if($update == false){
                return response()->json([
                    "status"   => 200,
                    "message"  => " No Have Vouchers ",
                ],200);
            }
            // *********************************************END**
            \DB::commit();
            return response([
                "status"   => 200,
                "message" => "Updated Vouchers Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"  => 403,
                "message" => "Failed Action Error 403",
            ],403);
        }
    }
    // 6 Delete
    public function VoucherDelete(Request $request,$id) {
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
            $del    = Voucher::deleteVoucher($user,$id);
            // ***********************************END**
            if($del == false){
                return response()->json([
                    "status"   => 403,
                    "message"  => " No Have Voucher ",
                ],403);
            }
            return response([
                "status"   => 200,
                "message" => "Deleted Voucher Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"  => 403,
                "message" => "Failed Action Error 403",
            ],403);
        } 
    }
    // 7 Bills
    public function VoucherBills(Request $request,$id) {
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
            $data["contact_id"] = $id;
            $bills             = Voucher::billVoucher($user,$data,$id);
            if($bills == false){
                return response()->json([
                    "status"   => 200,
                    "value"    => [],
                    "message"  => " No Have Previous Bills",
                ],200);
            }
            // *********************************************END**
            \DB::commit();
            return response([
                "status"   => 200,
                "value"    => $bills,
                "message"  => "Bills Access Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"  => 403,
                "message" => "Failed Action Error 403",
            ],403);
        }
    }
    // 8 View
    public function VoucherView(Request $request,$id) {
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
            $view             = Voucher::viewVoucher($user,$data,$id);
            if($view == false){
                return response()->json([
                    "status"   => 200,
                    "message"  => " No Have Previous Voucher",
                ],200);
            }
            // *********************************************END**
            \DB::commit();
            return response([
                "status"   => 200,
                "value"    => $view,
                "message"  => "View Access Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"  => 403,
                "message" => "Failed Action Error 403",
            ],403);
        }
    }
    // 9 Print
    public function VoucherPrint(Request $request,$id) {
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
            $view             = Voucher::printVoucher($user,$data,$id);
            if($view == false){
                return response()->json([
                    "status"   => 200,
                    "message"  => " No Have Previous Voucher",
                ],200);
            }
            // *********************************************END**
            \DB::commit();
            return response([
                "status"   => 200,
                "value"    => $view,
                "message"  => "Print Access Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"  => 403,
                "message" => "Failed Action Error 403",
            ],403);
        }
    }
    // 9 Currency
    public function VoucherCurrency(Request $request,$id) {
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
            $currency             = Voucher::currencyVoucher($user,$data,$id);
            if($currency == false){
                return response()->json([
                    "status"   => 200,
                    "message"  => " No Have Previous Currency",
                ],200);
            }
            // *********************************************END**
            \DB::commit();
            return response([
                "status"   => 200,
                "value"    => $currency,
                "message"  => "Currency Access Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"  => 403,
                "message" => "Failed Action Error 403",
            ],403);
        }
    }
    // 10 Entry
    public function VoucherEntry(Request $request,$id) {
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
            $currency             = Voucher::entryVoucher($user,$data,$id);
            if($currency == false){
                return response()->json([
                    "status"   => 200,
                    "message"  => " No Have Previous Entry",
                ],200);
            }
            // *********************************************END**
            \DB::commit();
            return response([
                "status"   => 200,
                "value"    => $currency,
                "message"  => "Entry Access Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"  => 403,
                "message" => "Failed Action Error 403",
            ],403);
        }
    }
    // 11 Attachment
    public function VoucherAttachment(Request $request,$id) {
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
            $currency             = Voucher::attachVoucher($user,$data,$id);
            if($currency == false){
                return response()->json([
                    "status"   => 200,
                    "message"  => " No Have Previous Attachment",
                ],200);
            }
            // *********************************************END**
            \DB::commit();
            return response([
                "status"   => 200,
                "value"    => $currency,
                "message"  => "Attachment Access Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"  => 403,
                "message" => "Failed Action Error 403",
            ],403);
        }
    }
    // 12 Bill View
    public function VoucherBillView(Request $request,$id) {
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
            $view             = Voucher::viewBillVoucher($user,$data,$id);
            if($view == false){
                return response()->json([
                    "status"   => 200,
                    "message"  => " No Have Previous Bill Voucher",
                ],200);
            }
            // *********************************************END**
            \DB::commit();
            return response([
                "status"   => 200,
                "value"    => $view,
                "message"  => "View Bill Access Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"  => 403,
                "message" => "Failed Action Error 403",
            ],403);
        }
    }
    
}
