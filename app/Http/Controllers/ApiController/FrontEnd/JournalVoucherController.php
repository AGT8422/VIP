<?php

namespace App\Http\Controllers\ApiController\FrontEnd;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FrontEnd\Vouchers\JournalVoucher;

class JournalVoucherController extends Controller
{
    // 1 index
    public function JournalVoucher(Request $request) {
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
            $JournalVoucher    = JournalVoucher::getJournalVoucher($user,$filter);
            if($JournalVoucher == false){
                return response()->json([
                    "status"   => 200,
                    "message"  => " No Have Journal Voucher ",
                ],200);
            }
            return response([
                "status"  => 200,
                "value"   => $JournalVoucher,
                "message" => "Journal Voucher Access Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"  => 403,
                "message" => "Failed Action Error 403",
            ],403);
        }  
    }
    // 2 create
    public function JournalVoucherCreate(Request $request) {
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
            $create    = JournalVoucher::createJournalVoucher($user,$data);
            // *******************************************************END**
            if($create == false){
                return response()->json([
                    "status"   => 200,
                    "message"  => " No Have Journal Vouchers ",
                ],200);
            }
            return response([
                "status"   => 200,
                "value"    => $create,
                "message"  => "Create Journal Vouchers Access Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"  => 403,
                "message" => "Failed Action Error 403",
            ],403);
        }   
    }
    // 3 Edit
    public function JournalVoucherEdit(Request $request,$id) {
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
            // ******************************************************START**
            $edit    = JournalVoucher::editJournalVoucher($user,$data,$id);
            // ********************************************************END**
            if($edit == false){
                return response()->json([
                    "status"   => 200,
                    "message"  => " No Have Journal Vouchers ",
                ],200);
            }
            return response([
                "status"    => 200,
                "value"     => $edit,
                "message"   => "Edit Journal Vouchers Access Successfully",
            ]);
        }catch(Exception $e){   
            return response([
                "status"  => 403,
                "message" => "Failed Action Error 403",
            ],403);
        }   
    }
    // 4 Store
    public function JournalVoucherStore(Request $request) {
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
            // ********************************************************START**
            $data         = $request->only([
                                "currency_id","currency_id_amount","account_id","credit","debit",
                                "cost_center_id","text","total_debit","total_credit","date"
            ]);
                $save      = JournalVoucher::storeJournalVoucher($user,$data,$request);
                if($save == false){
                    return response()->json([
                        "status"   => 403,
                        "message"  => " Failed Action ",
                    ],403);
                }
            // *********************************************************END**
            \DB::commit();
            return response([
                "status"   => 200,
                "message" => "Added Journal Vouchers Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"  => 403,
                "message" => "Failed Action Error 403",
            ],403);
        } 
    }
    // 5 Update
    public function JournalVoucherUpdate(Request $request,$id) {
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
            $data         = $request->only([
                            "currency_id","currency_id_amount","account_id","credit","debit",
                            "cost_center_id","text","total_debit","total_credit","date",
                            "old_item","old_account_id","old_credit","old_debit","old_cost_center_id","old_text"
            ]);
            // ********************************************************START**
            $update    = JournalVoucher::updateJournalVoucher($user,$data,$id,$request);
            if($update == false){
                return response()->json([
                    "status"   => 403,
                    "message"  => " No Have Journal Vouchers ",
                ],403);
            }
            // **********************************************************END**
            \DB::commit();
            return response([
                "status"   => 200,
                "message" => "Updated Journal Vouchers Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"  => 403,
                "message" => "Failed Action Error 403",
            ],403);
        }
    }
    // 6 Delete
    public function JournalVoucherDelete(Request $request,$id) {
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
            $del    = JournalVoucher::deleteJournalVoucher($user,$id);
            // ***********************************END**
            if($del == false){
                return response()->json([
                    "status"   => 403,
                    "message"  => " No Have Journal Voucher ",
                ],403);
            }
            return response([
                "status"   => 200,
                "message" => "Deleted Journal Voucher Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"  => 403,
                "message" => "Failed Action Error 403",
            ],403);
        }
    }
    // 7 Currency
    public function JournalVoucherCurrency(Request $request,$id) {
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
            $currency             = JournalVoucher::currencyJournalVoucher($user,$data,$id);
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
    // 8 View
    public function JournalVoucherView(Request $request,$id) {
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
            $view             = JournalVoucher::viewJournalVoucher($user,$data,$id);
            if($view == false){
                return response()->json([
                    "status"   => 200,
                    "message"  => " No Have Previous Journal Voucher",
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
    public function JournalVoucherPrint(Request $request,$id) {
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
            $view             = JournalVoucher::printJournalVoucher($user,$data,$id);
            if($view == false){
                return response()->json([
                    "status"   => 200,
                    "message"  => " No Have Previous Journal Voucher",
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
    // 10 Entry
    public function JournalVoucherEntry(Request $request,$id) {
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
            $currency             = JournalVoucher::entryJournalVoucher($user,$data,$id);
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
    public function JournalVoucherAttachment(Request $request,$id) {
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
            $attach             = JournalVoucher::attachJournalVoucher($user,$data,$id);
            if($attach == false){
                return response()->json([
                    "status"   => 200,
                    "message"  => " No Have Previous Attachment",
                ],200);
            }
            // *********************************************END**
            \DB::commit();
            return response([
                "status"   => 200,
                "value"    => $attach,
                "message"  => "Attachment Access Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"  => 403,
                "message" => "Failed Action Error 403",
            ],403);
        }
    }
}
