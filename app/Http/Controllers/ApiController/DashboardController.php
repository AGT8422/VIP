<?php

namespace App\Http\Controllers\ApiController;

use App\Http\Controllers\Controller;
use App\Utils\BusinessUtil;
use DB;
use Illuminate\Validation\Rule;
use App\Utils\ModuleUtil;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Transaction;
use App\Utils\TransactionUtil;
use App\Utils\CommonUtil;
use App\Utils\RestUtil;
use Spatie\Activitylog\Models\Activity;
use Laravel\Sanctum\Contracts;
use Illuminate\Session\Store;
use Utils\Util;
use Illuminate\Support\Str;
use App\Models\User;

use App\Models\FrontEnd\Users\UserManagement;
use App\Models\FrontEnd\Users\UserRole;

class DashboardController extends Controller
{
 
    /**
     * All Utils instance.
     *
     */
    protected $businessUtil;
    protected $transactionUtil;
    protected $moduleUtil;
    protected $commonUtil;
    protected $restUtil;

    public function __construct(
        BusinessUtil $businessUtil,
        TransactionUtil $transactionUtil,
        ModuleUtil $moduleUtil
    ) {
        $this->businessUtil    = $businessUtil;
        $this->transactionUtil = $transactionUtil;
        $this->moduleUtil      = $moduleUtil;
    }

    // 1............ DashBoard ...............\\
    // ************************************** \\
    public function Dashboard(Request $request){
        try{
            // *1* FOR CHECK AUTHENTICATION USER
            // *** AGT8422
                $api_token = $request->header("Authorization");
                $last_api  = substr( $api_token,7);
                $date      = request()->input("date_type");
                $token     = $last_api;
                if($api_token == "" &&  $api_token == null){
                    return response([
                        "status"    => 403,
                        "messages"  => __("Unauthorized Action")
                    ],403);
                } 
                $user      = \App\User::where("api_token",$token)->first();
                if(!$user){
                    return response([
                        "status"    => 403,
                        "messages"  => __("Unauthorized Action")
                    ],403);
                } 
            // ************
            // *2* FOR GET DATA
            // *** AGT8422
                $type         = request()->input("type"); 
                $allData      = \App\Models\ReportSetting::allData($date,$user->business_id);
                $accountsData = \App\Models\ReportSetting::Accounts($user);     
                // $userData     = \App\Models\ReportSetting::UserData($user);     
                // $voucherData  = \App\Models\ReportSetting::Vouchers($user,$type);     
                // $paymentsData = \App\Models\ReportSetting::Payments($user);     
            // ************
            // *3* FOR RESPONSE SECTION 
            // *** AGT8422
                return response()->json([
                            "Status"          => 200,
                            "Message"         => " Successfully Access ",
                            "Type"            => ($date == null) ? "Today":$date,
                            "Report"          => $allData['report'], 
                            "Profit"          => $allData['array'], 
                            "Accounts"        => $accountsData, 
                            "Currency_Name"   => ($allData['symbol'] != "")?$allData['currency']:"", 
                            "Currency_Symbol" => ($allData['symbol'] != "")?$allData['symbol']:"", 
                            "Token"           => $token,
                            // "UserData" => $userData, 
                            // "Vouchers" => $voucherData, 
                            // "Payments" => $paymentsData, 
                        ]);
            // ************ 
        }catch(Exception $e){
            DB::rollBack();
            \Log::emergency("File" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            \Log::alert($e);
            return response()->json([
                "status"   => 403,
                "message"  => " Failed ",
            ],403);
        }
    }
    // 2............ Customers .............. \\
    // ************************************** \\
    public function Customers(Request $request){
        try{
            $api_token = $request->header("Authorization");
            $last_api  = substr( $api_token,7);
            $date      = request()->input("date_type");
            $token     = $last_api;
            if($api_token == "" &&  $api_token == null){
                return response([
                    "status"    => 403,
                    "messages"  => __("Unauthorized Action")
                ],403);
            } 
            $user      = \App\User::where("api_token",$token)->first();
    
            if(!$user){
                return response([
                    "status"    => 403,
                    "messages"  => __("Unauthorized Action")
                ],403);
            } 
            $allData   = \App\Models\ReportSetting::Customers($user->business_id,$date);
            return response()->json([
                    "status"   => 200,
                    "msg"      => __("Access Customers Successfully"),
                    "customers" => $allData['list'],
                    "count-customers" => $allData['count']
            ]);
        }catch(Exception $e){
            DB::rollBack();
            \Log::emergency("File" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            \Log::alert($e);
            return response()->json([
                "status"   => 403,
                "message"  => " Failed ",
            ],403);
        }
    }
    // 3............ Suppliers .............. \\
    // ************************************** \\
    public function Suppliers(Request $request){
        try{
            $api_token = $request->header("Authorization");
            $last_api  = substr( $api_token,7);
            $date      = request()->input("date_type");
            $token     = $last_api;
            if($api_token == "" &&  $api_token == null){
                return response([
                    "status"    => 403,
                    "messages"  => __("Unauthorized Action")
                ],403);            } 
            $user      = \App\User::where("api_token",$token)->first();
            
            if(!$user){
                return response([
                    "status"    => 403,
                    "messages"  => __("Unauthorized Action")
                ],403);            
            } 
            $allData   = \App\Models\ReportSetting::Suppliers($user->business_id,$date);
            return response()->json([
                    "status"   => 200,
                    "msg"      => __("Access Suppliers Successfully"),
                    "suppliers" => $allData['list'],
                    "count-suppliers" => $allData['count']
            ]);
        }catch(Exception $e){
            DB::rollBack();
            \Log::emergency("File" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            \Log::alert($e);
            return response()->json([
                "status"   => 403,
                "message"  => " Failed ",
            ],403);
        }
    }
    // 4............ Products    ............ \\
    // ************************************** \\
    public function Products(Request $request){
        try{
            $api_token = $request->header("Authorization");
            $last_api  = substr( $api_token,7);
            $date      = request()->input("date_type");
            $token     = $last_api;
            if($api_token == "" &&  $api_token == null){
                return response([
                    "status"    => 403,
                    "messages"  => __("Unauthorized Action")
                ],403);            } 
            $user      = \App\User::where("api_token",$token)->first();
    
            if(!$user){
                return response([
                    "status"    => 403,
                    "messages"  => __("Unauthorized Action")
                ],403);
            } 
        }catch(Exception $e){
            DB::rollBack();
            \Log::emergency("File" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            \Log::alert($e);
            return response()->json([
                "status"   => 403,
                "message"  => " Failed ",
            ],403);
        }
    }
    // 5............ Currencies    .......... \\
    // ************************************** \\
    public function Currency(Request $request){
        try{
            $api_token = $request->header("Authorization");
            $last_api  = substr( $api_token,7);
            $date      = request()->input("date_type");
            $token     = $last_api;
            if($api_token == "" &&  $api_token == null){
                return response([
                    "status"    => 403,
                    "messages"  => __("Unauthorized Action")
                ],403);            
            } 
            $user      = \App\User::where("api_token",$token)->first();
    
            if(!$user){
                return response([
                    "status"    => 403,
                    "messages"  => __("Unauthorized Action")
                ],403);
            } 
           $allData = \App\Models\ReportSetting::currencies($user->business_id,$date);
           return response()->json([
                "status"   => 200,
                "msg"      => __("Access Currency Successfully"),
                "company-main-currency" => $allData['amin_currency'],
                "company-additional-currency" => $allData['list'],
                "count-additional-currency" => $allData['count']
           ]);
        }catch(Exception $e){
            DB::rollBack();
            \Log::emergency("File" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            \Log::alert($e);
            return response()->json([
                "status"   => 403,
                "message"  => " Failed ",
            ],403);
        }
    }
    // 6............    Style      .......... \\
    // ************************************** \\
    public function getStyle(Request $request){
        try{
            $api_token = $request->header("Authorization");
            $last_api  = substr( $api_token,7);
            $date      = request()->input("date_type");
            $token     = $last_api;
            if($api_token == "" &&  $api_token == null){
                return response([
                    "status"    => 403,
                    "messages"  => __("Unauthorized Action")
                ],403);            
            } 
            $user      = \App\User::where("api_token",$token)->first();
    
            if(!$user){
                return response([
                    "status"    => 403,
                    "messages"  => __("Unauthorized Action")
                ],403);
            } 
           $business = \App\Business::find($user->business_id);
           $style    = $business->front_dashboard_style;
           if($style == null){
                return response([
                    "status"    => 200,
                    "error"     => true,
                    "messages"  => __("Sorry Don't have any style"),
                ]);
           } 
           return response()->json([
                "status"                      => 200,
                "error"                       => false,
                "value"                       => json_decode($style),
                "messages"                    => __("Access Style Successfully"),
           ]);
        }catch(Exception $e){
            DB::rollBack();
            \Log::emergency("File" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            \Log::alert($e);
            return response()->json([
                "status"   => 403,
                "message"  => " Failed ",
            ],403);
        }
    }
    // 7............    Style      .......... \\
    // ************************************** \\
    public function saveStyle(Request $request){
        try{
            $api_token = $request->header("Authorization");
            $last_api  = substr( $api_token,7);
            $date      = request()->input("date_type");
            $token     = $last_api;
            if($api_token == "" &&  $api_token == null){
                return response([
                    "status"    => 403,
                    "messages"  => __("Unauthorized Action")
                ],403);            
            } 
            $user      = \App\User::where("api_token",$token)->first();
    
            if(!$user){
                return response([
                    "status"    => 403,
                    "messages"  => __("Unauthorized Action")
                ],403);
            } 
            $business = \App\Business::find($user->business_id);
            if(!$business){
                return response([
                    "status"    => 403,
                    "error"     => true,
                    "messages"  => __("Something Wrong")
                ]);
            }  
            $business->front_dashboard_style = json_encode($request->style);
            $business->update();
           return response()->json([
                "status"        => 200,
                "error"         => false,
                "messages"      => __("Save Style Successfully") 
           ]);
        }catch(Exception $e){
            DB::rollBack();
            \Log::emergency("File" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            \Log::alert($e);
            return response()->json([
                "status"   => 403,
                "message"  => " Failed ",
            ],403);
        }
    }
    

    // *** DATA SECTION  
    // *1* USERS
    // 1............ Users    .............. \\
    // ************************************** \\
    public function Users(Request $request){
        try{
            $main_token     = $request->header('Authorization');
            $token          = substr($main_token,7);
            $date           = request()->input("date_type");
            if($token == "" &&  $token == null){
                return response()->json([
                    "status"   => 403,
                    "message"  => " Unauthorized action ",
                ],403);
            } 
            $user      = \App\User::where("api_token",$token)->first();
            if(!$user){
                return response()->json([
                    "status"   => 403,
                    "message"  => " Unauthorized action ",
                ],403);
            } 
            $allData   = UserManagement::Users($user->business_id,$date);
            return response()->json([
                "status"       => 200,
                "msg"          => __("Access Users Successfully"),
                "users"        => $allData['list'],
                "count-users"  => $allData['count']
           ]);
        }catch(Exception $e){
            DB::rollBack();
            \Log::emergency("File" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            \Log::alert($e);
            return response()->json([
                "status"   => 403,
                "message"  => " Failed ",
            ],403);
        }
    }
    // 2............ Users  create ......... \\
    // ************************************** \\
    public function UsersCreate(Request $request) {
        try{
            $main_token     = $request->header('Authorization');
            $token          = substr($main_token,7);
            if($token == "" &&  $main_token == null){
                return response()->json([
                    "status"   => 403,
                    "message"  => " Unauthorized action ",
                ],403);
            }  
            $user      = \App\User::where("api_token",$token)->first();
            if(!$user){
                return response()->json([
                    "status"   => 403,
                    "message"  => " Unauthorized action ",
                ],403);
            } 
            $allData   = UserManagement::CreateUsers($user);

            return response([
                "status"       => 200,
                "Requirement"  => $allData,
                "messages"     => __("Create User Successfully Access")
            ]);
        }catch(Exception $e){
            return response([
                "status"    => 403,
                "messages"  => __("Failed Action")
            ],403);
        }
    }
    // 3............ Users edit    .......... \\
    // ************************************** \\
    public function UsersEdit(Request $request , $id) {
        try{
            $main_token     = $request->header('Authorization');
            $token          = substr($main_token,7);
            if($token == "" &&  $token == null){
                return response([
                    "status"    => 403,
                    "messages"  => __("Unauthorized Action")
                ],403);
            } 
            $user      = \App\User::where("api_token",$token)->first();
            if(!$user){
                return response([
                    "status"    => 403,
                    "messages"  => __("Unauthorized Action")
                ],403);
            } 
            $allData                             = UserManagement::EditUsers($user,$id);
            $user_info                           = $allData["user"];
            $requirement ["BusinessLocation"]    = $allData["BusinessLocation"];
            $requirement ["accounts"]            = $allData["accounts"];
            $requirement ["agents"]              = $allData["agents"];
            $requirement ["cost_center"]         = $allData["cost_center"];
            $requirement ["warehouse"]           = $allData["warehouse"];
            $requirement ["patterns"]            = $allData["patterns"];
            $requirement ["contacts"]            = $allData["contacts"];
            $requirement ["roles"]               = $allData["roles"];               
            $requirement ["gender"]              = $allData["gender"];
            $requirement ["marital"]             = $allData["marital"];
            $requirement ["taxes"]               = $allData["taxes"];                
            $requirement ["pPrice"]              = $allData["pPrice"];
            return response([
                "status"       => 200,
                "Requirement"  => $requirement,
                "UserInfo"     => $user_info,
                "messages"     => __("Edit User Successfully Access")
            ]);
        }catch(Exception $e){
            return response([
                "status"    => 403,
                "messages"  => __("Failed Action")
            ],403);
        }
            
    }
    // 4............ Users store  ........... \\
    // ************************************** \\
    public function UsersStore(Request $request) {
        try{
            $main_token     = $request->header('Authorization');
            $token          = substr($main_token,7);
            if($token == "" &&  $token == null){
                return response([
                    "status"    => 403,
                    "messages"  => __("Invalid Token")
                ],403);
            } 
            $user      = \App\User::where("api_token",$token)->first();
            if(!$user){
                return response([
                    "status"    => 403,
                    "messages"  => __("Invalid Token")
                ],403);
            } 
            $data           = $request->only([
                                    "surname","first_name","last_name","email","gender","business_id","user_tax_id",
                                    "is_active","user_visa_account_id","user_agent_id","user_cost_center_id","user_store_id",
                                    "user_account_id","user_pattern_id","allow_login","username","password","role","cmmsn_percent","max_sales_discount_percent","pattern_id",
                                    "dob","marital_status","blood_group","contact_number","alt_number","family_number","fb_link","twitter_link","social_media_1",
                                    "social_media_2","custom_field_1","custom_field_2","custom_field_3","custom_field_4","guardian_name","id_proof_name","id_proof_number",
                                    "permanent_address","current_address","bank_details","access_all_locations","selected_contacts","location_permissions","selected_contact_ids"
                                ]);
            $username            = \App\User::where("username",$data["username"])->whereNull('deleted_at')->first(); 
            if(!empty($username)){
                return response([
                    "status"       => 403,
                    "messages"     => __(" Duplicated Username  ")
                ],403);
            } 
            $save                = UserManagement::StoreUsers($data,$request);
            if(!$save){
                return response([
                    "status"       => 403,
                    "messages"     => __(" Invalid action  ")
                ],403);
            }
            return response([
                "status"       => 200,
                "messages"     => __(" Added Successfully")
            ]);
        }catch(Exception $e){
            return response([
                "status"    => 403,
                "messages"  => __("Failed Action")
            ],403);
        }
    }    
    // 5............ Users update ........... \\
    // ************************************** \\
    public function UsersUpdate(Request $request,$id) {
        try{
            $main_token     = $request->header('Authorization');
            $token          = substr($main_token,7);
            if($token == "" &&  $token == null){
                return response([
                    "status"    => 403,
                    "messages"  => __("Unauthorized Action")
                ],403);
            } 
            $user      = \App\User::where("api_token",$token)->first();
            if(!$user){
                return response([
                    "status"    => 403,
                    "messages"  => __("Unauthorized Action")
                ],403);
            } 
            $data           = $request->only([
                                    "surname","first_name","last_name","email","gender","business_id","user_tax_id",
                                    "is_active","user_visa_account_id","user_agent_id","user_cost_center_id","user_store_id",
                                    "user_account_id","user_pattern_id","allow_login","username","password","role","cmmsn_percent","max_sales_discount_percent","pattern_id",
                                    "dob","marital_status","blood_group","contact_number","alt_number","family_number","fb_link","twitter_link","social_media_1",
                                    "social_media_2","custom_field_1","custom_field_2","custom_field_3","custom_field_4","guardian_name","id_proof_name","id_proof_number",
                                    "permanent_address","current_address","bank_details","access_all_locations","selected_contacts","location_permissions","selected_contact_ids"
                                ]);
            $update                = UserManagement::UpdateUsers($data,$request,$id);
            if(!$update){
                return response([
                    "status"    => 403,
                    "messages"  => __("Invalid Action")
                ],403);
            }
            return response([
                "status"       => 200,
                "messages"     => __(" Updated Successfully")
            ]);
        }catch(Exception $e){
            return response([
                "status"    => 403,
                "messages"  => __("Failed Action")
            ],403);
        }
    }
    // 6............ Users delete ........... \\
    // ************************************** \\
    public function UsersDelete(Request $request,$id) {
        try{
            $main_token     = $request->header('Authorization');
            $token          = substr($main_token,7);
           
            if($token == "" &&  $token == null){
                return response([
                    "status"    => 403,
                    "messages"  => __("Token Invalid")
                ],403);
            } 
            $user      = \App\User::where("api_token",$token)->first();
            if(!$user){
                return response([
                    "status"    => 403,
                    "messages"  => __("Token Expired")
                ],403);
            } 
            $del                = UserManagement::DeleteUsers($id);
            if(!$del){
                return response([
                    "status"    => 403,
                    "messages"  => __("Failed To Delete")
                ],403);
            }
            return response([
                "status"       => 200,
                "messages"     => __(" Deleted Successfully")
            ]);
        }catch(Exception $e){
            return response([
                "status"    => 403,
                "messages"  => __("Failed Action")
            ],403);
        }
    } 
    // 7............ Users View ........... \\
    // ************************************** \\
    public function UsersView(Request $request,$id) {
        try{
            $main_token     = $request->header('Authorization');
            $token          = substr($main_token,7);
         
            if($token == "" &&  $token == null){
                return response([
                    "status"    => 403,
                    "messages"  => __("Token Invalid")
                ],403);
            } 
            $user      = \App\User::where("api_token",$token)->first();
            if(!$user){
                return response([
                    "status"    => 403,
                    "messages"  => __("Token Expired")
                ],403);
            } 
            $view                = UserManagement::ViewUsers($user,$id);
            if(!$view){
                return response([
                    "status"    => 403,
                    "messages"  => __("Failed To View ")
                ],403);
            }
            return response([
                "status"       => 200,
                "user"         => $view,
                "messages"     => __(" Shown Successfully")
            ]);
        }catch(Exception $e){
            return response([
                "status"    => 403,
                "messages"  => __("Failed Action")
            ],403);
        }
    } 
    
    
    // ************************************** \\
    // *** DATA SECTION  
    // *2* ROLES
    // 1............ Roles    .............. \\
    // ************************************** \\
    public function Roles(Request $request){
        try{
            $main_token     = $request->header('Authorization');
            $token          = substr($main_token,7);
            $date           = request()->input("date_type");
            if($token == "" &&  $token == null){
                return response()->json([
                    "status"   => 403,
                    "message"  => " Unauthorized action ",
                ],403);
            } 
            $user      = \App\User::where("api_token",$token)->first();
            if(!$user){
                return response()->json([
                    "status"   => 403,
                    "message"  => " Unauthorized action ",
                ],403);
            } 
            $allData   = UserRole::Roles($user->business_id,$date);
            return response()->json([
                "status"       => 200,
                "list"         => $allData,
                "message"      => __("Access Role Successfully")
           ]);
        }catch(Exception $e){
            DB::rollBack();
            \Log::emergency("File" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            \Log::alert($e);
            return response()->json([
                "status"   => 403,
                "message"  => " Failed ",
            ],403);
        }
    }
    // 2............ Roles  create ......... \\
    // ************************************** \\
    public function RolesCreate(Request $request) {
        try{
            $main_token     = $request->header('Authorization');
            $token          = substr($main_token,7);
            if($token == "" &&  $main_token == null){
                return response()->json([
                    "status"   => 403,
                    "message"  => " Unauthorized action ",
                ],403);
            } 
            $user      = \App\User::where("api_token",$token)->first();
            if(!$user){
                return response()->json([
                    "status"   => 403,
                    "message"  => " Unauthorized action ",
                ],403);
            } 
            $create    =  UserRole::CreateRoles($user);
            if(!$create){
                return response()->json([
                    "status"   => 403,
                    "message"  => " Unauthorized action ",
                ],403);
            }
            return response([
                "status"       => 200,
                "response"     => $create, 
                "messages"     => __("Create Role Successfully Access")
            ]);
        }catch(Exception $e){
            return response([
                "status"    => 403,
                "messages"  => __("Failed Action")
            ],403);
        }
    }
    // 3............ Roles edit    .......... \\
    // ************************************** \\
    public function RolesEdit(Request $request , $id) {
        try{
            $main_token     = $request->header('Authorization');
            $token          = substr($main_token,7);
            if($token == "" &&  $token == null){
                return response([
                    "status"    => 403,
                    "messages"  => __("Unauthorized Action")
                ],403);
            } 
            $user      = \App\User::where("api_token",$token)->first();
            if(!$user){
                return response([
                    "status"    => 403,
                    "messages"  => __("Unauthorized Action")
                ],403);
            } 
            $edit    =  UserRole::EditRoles($user);
            if(!$edit){
                return response()->json([
                    "status"   => 403,
                    "message"  => " Unauthorized action ",
                ],403);
            }
            return response([
                "status"       => 200,
                "list"         => $edit,
                "messages"     => __("Edit Role Successfully Access")
            ]);
        }catch(Exception $e){
            return response([
                "status"    => 403,
                "messages"  => __("Failed Action")
            ],403);
        }
            
    }
    // 4............ Roles store  ........... \\
    // ************************************** \\
    public function RolesStore(Request $request) {
        try{
            $main_token     = $request->header('Authorization');
            $token          = substr($main_token,7);
            if($token == "" &&  $token == null){
                return response([
                    "status"    => 403,
                    "messages"  => __("Invalid Token")
                ],403);
            } 
            $user      = \App\User::where("api_token",$token)->first();
            if(!$user){
                return response([
                    "status"    => 403,
                    "messages"  => __("Invalid Token")
                ],403);
            }
            \DB::beginTransaction();
            $data                     = [];
            $data["name"]             = $request->name ; 
            $data["permission"]       = $request->permission ; 
            $data["guard_name"]       = $request->guard_name ; 
            $data["spg_permissions"]  = $request->spg_permissions ; 
            $data["is_service_staff"] = $request->is_service_staff ; 
            $save                     =  UserRole::StoreRoles($user,$data,$request);
            if(!$save){
                return response()->json([
                    "status"   => 403,
                    "message"  => " Unauthorized action ",
                ],403);
            }
            if( $save["success"] == 0){
                return response([
                    "status"       => 405,
                    "messages"     => $save["msg"]
                ],405);
            
            }else{
                \DB::commit();
                return response([
                    "status"       => 200,
                    "messages"     => $save["msg"]
                ],200);
            
            }

        }catch(Exception $e){
            return response([
                "status"    => 403,
                "messages"  => __("Failed Action")
            ],403);
        }
    }    
    // 5............ Roles update ........... \\
    // ************************************** \\
    public function RolesUpdate(Request $request,$id) {
        try{
            $main_token     = $request->header('Authorization');
            $token          = substr($main_token,7);
            if($token == "" &&  $token == null){
                return response([
                    "status"    => 403,
                    "messages"  => __("Unauthorized Action")
                ],403);
            } 
            $user      = \App\User::where("api_token",$token)->first();
            if(!$user){
                return response([
                    "status"    => 403,
                    "messages"  => __("Unauthorized Action")
                ],403);
            } 
            \DB::beginTransaction();
            $data                     = [];
            $data["name"]             = $request->name ; 
            $data["permission"]       = $request->permission ; 
            $data["guard_name"]       = $request->guard_name ; 
            $data["spg_permissions"]  = $request->spg_permissions ; 
            $data["is_service_staff"] = $request->is_service_staff ; 
            $update    =  UserRole::UpdateRoles($user,$data,$id,$request);
            if(!$update){
                return response()->json([
                    "status"   => 403,
                    "message"  => " Unauthorized action ",
                ],403);
            }
            if( $update["success"] == 0){
                return response([
                    "status"       => 405,
                    "messages"     => $update["msg"]
                ],405);
            
            }else{
                \DB::commit();
                return response([
                    "status"       => 200,
                    "messages"     => $update["msg"]
                ],200);
            
            }
            
        }catch(Exception $e){
            return response([
                "status"    => 403,
                "messages"  => __("Failed Action")
            ],403);
        }
    }
    // 6............ Roles delete ........... \\
    // ************************************** \\
    public function RolesDelete(Request $request,$id) {
        try{
            $main_token     = $request->header('Authorization');
            $token          = substr($main_token,7);
            if($token == "" &&  $token == null){
                return response([
                    "status"    => 403,
                    "messages"  => __("Unauthorized Action")
                ],403);
            } 
            $user      = \App\User::where("api_token",$token)->first();
            if(!$user){
                return response([
                    "status"    => 403,
                    "messages"  => __("Invalid Action")
                ],403);
            }  
            \DB::beginTransaction();
            $delete    =  UserRole::DeleteRoles($user,$id);
            if(!$delete){
                return response()->json([
                    "status"   => 403,
                    "message"  => " Unauthorized action ",
                ],403);
            }

            if( $delete["success"] == 0){
                return response([
                    "status"       => 405,
                    "messages"     => $update["msg"]
                ],405);
            
            }else{
                \DB::commit();
                return response([
                    "status"       => 200,
                    "messages"     => $update["msg"]
                ],200);
            
            }
             
        }catch(Exception $e){
            return response([
                "status"    => 403,
                "messages"  => __("Failed Action")
            ],403);
        }
    }    
    // ************************************** \\
    public function RolesBy(Request $request) {
        try{
            $main_token     = $request->header('Authorization');
            $token          = substr($main_token,7);
            if($token == "" &&  $token == null){
                return response([
                    "status"    => 403,
                    "messages"  => __("Unauthorized Action")
                ],403);
            } 
            $user      = \App\User::where("api_token",$token)->first();
            if(!$user){
                return response([
                    "status"    => 403,
                    "messages"  => __("Invalid Action")
                ],403);
            }
            $data     =  $request->only(["type"]);  
            $roles    =  UserRole::RolesBy($user,$data);
            if(!$roles){
                return response()->json([
                    "status"   => 403,
                    "message"  => " Unauthorized action ",
                ],403);
            }
            
            return response([
                "status"       => 200,
                "role"         => $roles,
                "messages"     => __(" Roles Access Successfully")
            ]);
        }catch(Exception $e){
            return response([
                "status"    => 403,
                "messages"  => __("Failed Action")
            ],403);
        }
    }

    
    
     
    
    
}
