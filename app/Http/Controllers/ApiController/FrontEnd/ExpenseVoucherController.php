<?php

namespace App\Http\Controllers\ApiController\FrontEnd;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FrontEnd\Vouchers\ExpenseVoucher;

class ExpenseVoucherController extends Controller
{
    // 1 index
    public function ExpenseVoucher(Request $request) {
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
            $ExpenseVoucher    = ExpenseVoucher::getExpenseVoucher($user,$filter);
            if($ExpenseVoucher == false){
                return response()->json([
                    "status"   => 403,
                    "message"  => " No Have Expense Voucher  ",
                ],403);
            }
            return response([
                "status"   => 200,
                "value"    => $ExpenseVoucher,
                "message"  => "Expense Voucher Access Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"  => 403,
                "message" => "Failed Action Error 403",
            ],403);
        }  
    }
    // 2 create
    public function ExpenseVoucherCreate(Request $request) {
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
            $create    = ExpenseVoucher::createExpenseVoucher($user,$data);
            // *******************************************************END**
            if($create == false){
                return response()->json([
                    "status"   => 403,
                    "message"  => " No Have Expense Vouchers ",
                ],403);
            }
            return response([
                "status"   => 200,
                "value"    => $create,
                "message"  => "Create Expense Vouchers Access Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"  => 403,
                "message" => "Failed Action Error 403",
            ],403);
        }   
    }
    // 3 Edit
    public function ExpenseVoucherEdit(Request $request,$id) {
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
            $edit    = ExpenseVoucher::editExpenseVoucher($user,$data,$id);
            // ********************************************************END**
            if($edit == false){
                return response()->json([
                    "status"   => 403,
                    "message"  => " No Have Expense Vouchers ",
                ],403);
            }
            return response([
                "status"    => 200,
                "value"     => $edit,
                "message"   => "Edit Expense Vouchers Access Successfully",
            ]);
        }catch(Exception $e){   
            return response([
                "status"  => 403,
                "message" => "Failed Action Error 403",
            ],403);
        } 
    }
    // 4 Store
    public function ExpenseVoucherStore(Request $request) {
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
            $data         = $request->only([
                                "main_account_id","main_credit","total_credit",
                                "gournal_date","currency_id","currency_id_amount",
                                "note_main","currency_id","credit_account_id","debit_account_id",
                                "amount","center_id","tax_percentage","tax_amount","net_amount","date","text"
            ]);
            // ********************************************************START**
                $save      = ExpenseVoucher::storeExpenseVoucher($user,$data,$request);
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
                "message" => "Added Expense Vouchers Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"  => 403,
                "message" => "Failed Action Error 403",
            ],403);
        }
    }
    // 5 Update
    public function ExpenseVoucherUpdate(Request $request,$id) {
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
                                "main_account_id","main_credit","total_credit",
                                "gournal_date","currency_id","currency_id_amount",
                                "note_main","currency_id","credit_account_id",
                                "amount","center_id","tax_percentage","tax_amount",
                                "date","text","item_id","old_credit_account_id",
                                "old_amount","old_center_id","old_tax_percentage",
                                "old_net_amount","old_date","old_text","net_amount",
                                "old_debit_account_id","old_tax_amount","debit_account_id",
            ]);
            // ********************************************************START**
            $update    = ExpenseVoucher::updateExpenseVoucher($user,$data,$id,$request);
            if($update == false){
                return response()->json([
                    "status"   => 403,
                    "message"  => " No Have Expense Voucher ",
                ],403);
            }
            // ********************************************************END**
            \DB::commit();
            return response([
                "status"   => 200,
                "message" => "Updated Expense Voucher Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"  => 403,
                "message" => "Failed Action Error 403",
            ],403);
        }
    }
    // 6 Delete
    public function ExpenseVoucherDelete(Request $request,$id) {
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
            $del    = ExpenseVoucher::deleteExpenseVoucher($user,$id);
            // ***********************************END**
            if($del == false){
                return response()->json([
                    "status"   => 403,
                    "message"  => " No Have Expense Vouchers ",
                ],403);
            }
            return response([
                "status"   => 200,
                "message" => "Deleted Expense Vouchers Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"  => 403,
                "message" => "Failed Action Error 403",
            ],403);
        }
    }
    // 7 Currency
    public function ExpenseVoucherCurrency(Request $request,$id) {
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
            $currency             = ExpenseVoucher::currencyExpenseVoucher($user,$data,$id);
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
    public function ExpenseVoucherView(Request $request,$id) {
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
            $view             = ExpenseVoucher::viewExpenseVoucher($user,$data,$id);
            if($view == false){
                return response()->json([
                    "status"   => 200,
                    "message"  => " No Have Previous Expense Voucher",
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
    public function ExpenseVoucherPrint(Request $request,$id) {
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
            $view             = ExpenseVoucher::printExpenseVoucher($user,$data,$id);
            if($view == false){
                return response()->json([
                    "status"   => 200,
                    "message"  => " No Have Previous Expense Voucher",
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
    public function ExpenseVoucherEntry(Request $request,$id) {
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
            $currency             = ExpenseVoucher::entryExpenseVoucher($user,$data,$id);
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
    public function ExpenseVoucherAttachment(Request $request,$id) {
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
            $attach             = ExpenseVoucher::attachExpenseVoucher($user,$data,$id);
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
