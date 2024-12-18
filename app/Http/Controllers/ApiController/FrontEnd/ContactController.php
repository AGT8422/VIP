<?php

namespace App\Http\Controllers\ApiController\FrontEnd;

use App\Http\Controllers\Controller;
use App\Models\FrontEnd\Contacts\Contact;
use Illuminate\Http\Request;
use App\Utils\Util;

class ContactController extends Controller
{

    /**
     * Constructor
     *
     * @param Util $commonUtil
     * @return void
     */
    public function __construct( Util $commonUtil) {
        $this->commonUtil = $commonUtil;
    }
    
    // **** REACT FRONT-END CONTACT **** //
    // ... 1 ...    List Of Contact 
    public function GetContact(Request $request) {
        try{
            $main_token   = $request->header("Authorization");
            $token        = substr($main_token,7);
            if($token == "" || $token == null){
                return response([
                    "status"   => 401,
                    "error"    => true,
                    "message"  => "Invalid Token"
                ],401);
            }
            $user      = \App\User::where("api_token",$token)->first();
            if(!$user){
                return response()->json([
                    "status"   => 403,
                    "error"    => true,
                    "message"  => " Unauthorized action ",
                ],403);
            } 
            $contacts    = Contact::getContact($user);
            if($contacts == false){
                return response()->json([
                    "status"   => 200,
                    "error"    => true,
                    "message"  => " No Have Contacts ",
                ],200);
            }
            return response([
                "status"   => 200,
                "error"    => false,
                "contact"  => $contacts,
                "message"  => "Contact Access Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"   => 200,
                "error"    => true,
                "message"  => "Failed Action Error 403",
            ],200);
        }
    }
    // ... 2 ...    List Of Suppliers 
    public function GetSupplier(Request $request) {
        try{
            $main_token   = $request->header("Authorization");
            $token        = substr($main_token,7);
            if($token == "" || $token == null){
                return response([
                    "status"   => 401,
                    "error"    => true,
                    "message"  => "Invalid Token"
                ],401);
            }
            $user      = \App\User::where("api_token",$token)->first();
            if(!$user){
                return response()->json([
                    "status"   => 403,
                    "error"    => true,
                    "message"  => " Unauthorized action ",
                ],403);
            } 
            $contacts    = Contact::getSupplier($user);
            if($contacts == false){
                return response()->json([
                    "status"   => 200,
                    "error"    => true,
                    "message"  => " No Have Suppliers ",
                ],200);
            }
            return response([
                "status"   => 200,
                "error"    => false,
                "contact"  => $contacts,
                "message" => "Suppliers Access Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"   => 200,
                "error"    => true,
                "message"  => "Failed Action Error 403",
            ],200);
        }
    }
    // ... 3 ...    List Of Customers 
    public function GetCustomer(Request $request) {
        try{
            $main_token   = $request->header("Authorization");
            $token        = substr($main_token,7);
            if($token == "" || $token == null){
                return response([
                    "status"   => 401,
                    "error"    => true,
                    "message"  => "Invalid Token"
                ],401);
            }
            $user      = \App\User::where("api_token",$token)->first();
            if(!$user){
                return response()->json([
                    "status"   => 403,
                    "error"    => true,
                    "message"  => " Unauthorized action ",
                ],403);
            } 
            $contacts    = Contact::getCustomer($user);
            if($contacts == false){
                return response()->json([
                    "status"   => 200,
                    "error"    => true,
                    "message"  => " No Have Customers ",
                ],200);
            }
            return response([
                "status"   => 200,
                "error"    => false,
                "contact"  => $contacts,
                "message"  => "Customers Access Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"   => 200,
                "error"    => true,
                "message"  => "Failed Action Error 403",
            ],200);
        }
    }
    // ... 4 ...    Create Contact 
    public function CreateContact(Request $request) {
        try{
            $main_token   = $request->header("Authorization");
            $token        = substr($main_token,7);
            $data         = $request->only(["type"]);
            if($token == "" || $token == null){
                return response([
                    "status"   => 401,
                    "error"    => true,
                    "message"  => "Invalid Token"
                ],401);
            }
            $user      = \App\User::where("api_token",$token)->first();
            if(!$user){
                return response()->json([
                    "status"   => 403,
                    "error"    => true,
                    "message"  => " Unauthorized action ",
                ],403);
            } 
            // ***************************************START**
            $create    = Contact::createContact($user,$data);
            // *****************************************END**
            if($create == false){
                return response()->json([
                    "status"   => 200,
                    "error"    => true,
                    "message"  => " No Have Contact ",
                ],200);
            }
            return response([
                "status"   => 200,
                "response" => $create,
                "error"    => false,
                "message"  => "Create Contact Access Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"   => 200,
                "error"    => true,
                "message"  => "Failed Action Error 403",
            ],200);
        }
    }
    // ... 5 ...    Edit Contact 
    public function EditContact(Request $request,$id) {
        try{
            $main_token   = $request->header("Authorization");
            $token        = substr($main_token,7);
            $data         = $request->only(["type"]);
            if($token == "" || $token == null){
                return response([
                    "status"   => 401,
                    "error"    => true,
                    "message"  => "Invalid Token"
                ],401);
            }
            $user      = \App\User::where("api_token",$token)->first();
            if(!$user){
                return response()->json([
                    "status"   => 403,
                    "error"    => true,
                    "message"  => " Unauthorized action ",
                ],403);
            } 
            // **************************************START**
            $edit    = Contact::editContact($user,$data,$id);
            // ****************************************END**
            if($edit == false){
                return response()->json([
                    "status"   => 200,
                    "error"    => true,
                    "message"  => " No Have Contact ",
                ],200);
            }
            return response([
                "status"    => 200,
                "error"     => false,
                "response"  => $edit,
                "message"   => "Edit Contact Access Successfully",
            ]);
        }catch(Exception $e){   
            return response([
                "status"   => 200,
                "error"    => true,
                "message"  => "Failed Action Error 403",
            ],200);
        }
    }
    // ... 6 ...    View Contact 
    public function ViewContact(Request $request,$id) {
        try{
            $main_token   = $request->header("Authorization");
            $token        = substr($main_token,7);
            $data         = $request->only(["start_date","end_date"]);
            if($token == "" || $token == null){
                return response([
                    "status"   => 401,
                    "error"    => true,
                    "message"  => "Invalid Token"
                ],401);
            }
            $user      = \App\User::where("api_token",$token)->first();
            if(!$user){
                return response()->json([
                    "status"   => 403,
                    "error"    => true,
                    "message"  => " Unauthorized action ",
                ],403);
            } 
            // **************************************START**
            $view    = Contact::viewContact($user,$data,$id);
            // ****************************************END**
            if($view == false){
                return response()->json([
                    "status"   => 200,
                    "error"    => true,
                    "message"  => " No Have Contact ",
                ],200);
            }
            return response([
                "status"    => 200,
                "error"     => false,
                "response"  => $view,
                "message"   => "View Contact Access Successfully",
            ]);
        }catch(Exception $e){   
            return response([
                "status"   => 200,
                "error"    => true,
                "message"  => "Failed Action Error 403",
            ],200);
        }
    }
    // ... 7 ...    Store Contact 
    public function StoreContact(Request $request) {
        try{
            $main_token   = $request->header("Authorization");
            $token        = substr($main_token,7);
            if($token == "" || $token == null){
                return response([
                    "status"   => 401,
                    "error"    => true,
                    "message"  => "Invalid Token"
                ],401);
            }
            $user      = \App\User::where("api_token",$token)->first();
            if(!$user){
                return response()->json([
                    "status"   => 403,
                    "error"    => true,
                    "message"  => " Unauthorized action ",
                ],403);
            } 
            \DB::beginTransaction();
            $data         = $request->only([
                                "type","supplier_business_name",
                                "prefix","first_name","middle_name",
                                "last_name","tax_number","pay_term_number","pay_term_type",
                                "mobile","landline","alternate_number","city",
                                "state","country","address_line_1","address_line_2","customer_group_id","zip_code",
                                "contact_id","custom_field1","custom_field2","custom_field3","custom_field4",
                                "custom_field5","custom_field6","custom_field7",
                                "custom_field8","custom_field9","custom_field10",
                                "email","shipping_address","position","dob","credit_limit",
                                "opening_balance"
            ]);
            // ************************************START**
                $save      = Contact::storeContact($user,$data);
                if($save == false){
                    return response()->json([
                        "status"   => 200,
                        "error"    => true,
                        "message"  => " Failed Action ",
                    ],200);
                }
                $last      = \App\Contact::OrderBy("id","desc")->first(); 
                \App\Contact::add_account($last->id,$user->business_id);
                $this->commonUtil->activityLog($save,"Added");
            // **************************************END**
            \DB::commit();
            return response([
                "status"   => 200,
                "error"    => false,
                "message"  => "Added Contact Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"   => 200,
                "error"    => true,
                "message"  => "Failed Action Error 403",
            ],200);
        }
    }
    // ... 8 ...    Update Contact 
    public function UpdateContact(Request $request,$id) {
        try{
            $main_token   = $request->header("Authorization");
            $token        = substr($main_token,7);
            $data         = $request->only(["type"]);
            if($token == "" || $token == null){
                return response([
                    "status"   => 401,
                    "error"    => true,
                    "message"  => "Invalid Token"
                ],401);
            }
            $user      = \App\User::where("api_token",$token)->first();
            if(!$user){
                return response()->json([
                    "status"   => 403,
                    "error"    => true,
                    "message"  => " Unauthorized action ",
                ],403);
            } 
            \DB::beginTransaction();
            $data         = $request->only([
                                "type","supplier_business_name",
                                "prefix","first_name","middle_name",
                                "last_name","tax_number","pay_term_number","pay_term_type",
                                "mobile","landline","alternate_number","city",
                                "state","country","address_line_1","address_line_2","customer_group_id","zip_code",
                                "contact_id","custom_field1","custom_field2","custom_field3","custom_field4",
                                "custom_field5","custom_field6","custom_field7",
                                "custom_field8","custom_field9","custom_field10",
                                "email","shipping_address","position","dob","credit_limit",
                                "opening_balance"
            ]);
            // *******************************************START**
            $update    = Contact::updateContact($user,$data,$id);
            if($update == false){
                return response()->json([
                    "status"   => 200,
                    "error"    => true,
                    "message"  => " No Have Contact ",
                ],200);
            }
            $this->commonUtil->activityLog($update,"edited");
            // *********************************************END**
            \DB::commit();
            return response([
                "status"   => 200,
                "error"    => false,
                "message"  => "Updated Contact Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"   => 200,
                "error"    => true,
                "message"  => "Failed Action Error 403",
            ],200);
        }
    }
    // ... 9 ...    Delete Contact 
    public function DeleteContact(Request $request,$id) {
        try{
            $main_token   = $request->header("Authorization");
            $token        = substr($main_token,7);
            $type         = $request->input("type");
            if($token == "" || $token == null){
                return response([
                    "status"   => 401,
                    "error"    => true,
                    "message"  => "Invalid Token"
                ],401);
            }
            $user      = \App\User::where("api_token",$token)->first();
            if(!$user){
                return response()->json([
                    "status"   => 403,
                    "error"    => true,
                    "message"  => " Unauthorized action ",
                ],403);
            } 
            // *********************************START**
            $del    = Contact::deleteContact($user,$id);
            // ***********************************END**
            if($del == false){
                return response()->json([
                    "status"   => 200,
                    "error"    => true,
                    "message"  => " No Have Contact ",
                ],200);
            }
            return response([
                "status"   => 200,
                "error"    => false,
                "message"  => "Deleted Contact Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"   => 200,
                "error"    => true,
                "message"  => "Failed Action Error 403",
            ],200);
        }
    }
    // ... 10 ...    Export Contact 
    public function ExportContact(Request $request) {
        try{
            $main_token   = $request->header("Authorization");
            $token        = substr($main_token,7);
            $type         = $request->input("type");
            if($token == "" || $token == null){
                return response([
                    "status"   => 401,
                    "error"    => true,
                    "message"  => "Invalid Token"
                ],401);
            }
            $user      = \App\User::where("api_token",$token)->first();
            if(!$user){
                return response()->json([
                    "status"   => 403,
                    "error"    => true,
                    "message"  => " Unauthorized action ",
                ],403);
            } 
            // *********************************START**
            $file    = Contact::exportContact($user);
            // ***********************************END**
            if($file == false){
                return response()->json([
                    "status"   => 200,
                    "error"    => true,
                    "message"  => " No Have Template Contact ",
                ],200);
            }
            return response([
                "status"   => 200,
                "error"    => false,
                "value"    => $file,
                "message"  => "Access File Contact Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"   => 200,
                "error"    => true,
                "message"  => "Failed Action Error 403",
            ],200);
        }
    }
    // ... 11 ...    Import Contact 
    public function ImportContact(Request $request) {
        try{
            $main_token   = $request->header("Authorization");
            $token        = substr($main_token,7);
            $type         = $request->input("type");
            if($token == "" || $token == null){
                return response([
                    "status"   => 401,
                    "error"    => true,
                    "message"  => "Invalid Token"
                ],401);
            }
            $user      = \App\User::where("api_token",$token)->first();
            if(!$user){
                return response()->json([
                    "status"   => 403,
                    "error"    => true,
                    "message"  => " Unauthorized action ",
                ],403);
            } 
            // *********************************START**
            if ($request->hasFile('contacts')) {
                $data    = $request->file('contacts');
                $ex      = $data->getClientOriginalExtension();
                $list    = ['csv','xlsx','xls'];
                $MSG     = " Failed Action  ";
                if(in_array($ex,$list)){
                    $fileResponse      = Contact::importContact($user,$data);
                    if($fileResponse['status'] == false){
                        $file = false;
                        $MSG  = $fileResponse['message'];
                    }else{
                        $file = true;
                    }
                }else{
                    $file    = false;
                }
            }else{
                $file    = false;
            }
            // ***********************************END**
            if($file == false){
                return response()->json([
                    "status"   => 200,
                    "error"    => true,
                    "message"  => $MSG,
                ],200);
            }
            return response([
                "status"   => 200,
                "error"    => false,
                "message"  => "Import Contact Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"   => 200,
                "error"    => true,
                "message"  => "Failed Action Error 403",
            ],200);
        }
    }
}
