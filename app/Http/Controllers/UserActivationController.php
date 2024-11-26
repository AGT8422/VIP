<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use Tymon\JWTAuth\Facades\JWTAuth;
use Carbon\Carbon;
class UserActivationController extends Controller
{
    //  .. List of users
    public function index() {
        // if(auth()->user()-can("user_list.show") ){
        //     abort(403,"UnAuthentication actions");   
        // }
        $user_products    = [];
        $user_mobile      = [];
        $user_services    = [];
        $user_status      = [];
        $user_usernames   = [];
        $user_addresses   = [];
        $user_email       = [];
        $user_name        = [];
        $company_name     = [];
        $userActivations = \App\Models\UserActivation::select()->get();
    
        if(request()->ajax()){

            $userActivation = \App\Models\UserActivation::select();
            if(!empty(request()->user_products)){
              $filter = request()->user_products;
              $userActivation->where("user_products",$filter);       
            }
            if(!empty(request()->user_mobile)){
               $filter = request()->user_mobile;
               $userActivation->where("user_mobile",$filter);  
            }
            if(!empty(request()->user_service)){
               $filter = request()->user_service;
               $userActivation->where("user_service",$filter);  
            }
            // if(!empty(request()->user_password)){
            //    $filter = request()->user_password;
            //    $userActivation->where("user_password",$filter);  
            // }
            if(!empty(request()->user_status)){
              $filter = request()->user_status;
              $userActivation->where("user_status",$filter);   
            }
            if(!empty(request()->user_username)){
                $filter = request()->user_username;
                $userActivation->where("user_username",$filter); 
            }
            if(!empty(request()->user_address)){
               $filter = request()->user_address;
               $userActivation->where("user_address",$filter);  
            }
            if(!empty(request()->user_email)){
                $filter = request()->user_email;
                $userActivation->where("user_email",$filter); 
            }
            if(!empty(request()->user_name)){
               $filter =request()->user_name ;
               $userActivation->where("user_name",$filter);  
            }
            if(!empty(request()->user_date)){
               $filter = request()->user_date ;
               $userActivation->whereDate("user_dateactivate","<=",$filter);  
            }
            
            $userActivation->get();  
            return DataTables::of($userActivation)
                        ->editColumn("created_at",function($row){
                               return $row->created_at->format('Y-m-d H:i:s');  
                        })
                        ->editColumn("user_token",function($row){
                               return  $row->user_token ;  
                        })
                        ->editColumn("user_username",function($row){
                                $html  = "<span class='user-name-check' id='user-name-check'>";   
                                $html .= $row->user_username ;   
                                $html .= "</span>";   
                            return  $html;  
                        })
                        ->editColumn("user_password",function($row){
                                $html  = "<span class='user-password-check' id='user-password-check'>";   
                                $html .= $row->user_password ;   
                                $html .= "</span>";   
                            return  $html;  
                        })
                        ->editColumn("activation_period",function($row){
                                $html  = "<span class='user-password-check' id='user-password-check'>";   
                                $Now   = Carbon::parse(\Carbon::now()->format('Y-m-d'));
                                $UNTIL = Carbon::parse(\Carbon::createFromTimestamp($row->activation_period)->format('Y-m-d'));
                                $html .= $Now->diffInDays($UNTIL) . " Days"  ;   
                                $html .= "</span>";   
                            return  $html;  
                        })
                        ->addColumn("user_activateion_code",function($row){
                               return  "<input class='form-control check-code-activate' id='check-code-activate' placeholder='Check Activation Code'  value=''><span class='correct-activation hide'><i class='fa fas fa-check'></i> &nbsp;Correct</span> &nbsp;<span class='wrang-activation hide'> <i class='fa fas fa-times'></i> &nbsp;Invalid value</span>" ;  
                        })
                        ->rawColumns([
                        "id", 
                        "user_service",
                        "user_products",
                        "user_name",
                        "user_password",
                        "user_email",
                        "company_name",
                        "user_address",
                        "user_mobile",
                        "user_activateion_code",
                        "user_username",
                        "user_token",
                        "user_payment",
                        "user_due_payment",
                        "user_status",
                        "user_dateactivate",
                        "user_number_device",
                        "activation_period",
                        "created_at"])
                        ->make(true) ;
                        
        }

        foreach($userActivations as $it){
            $user_products  [$it->user_products]   =  $it->user_products  ;  
            $user_mobile    [$it->user_mobile]     =  $it->user_mobile    ;
            $company_name   [$it->company_name]    =  $it->company_name   ;
            $user_services  [$it->user_service]    =  $it->user_service   ;
            $user_status    [$it->user_status]     =  $it->user_status    ;
            $user_usernames [$it->user_username]   =  $it->user_username  ;
            $user_addresses [$it->user_address]    =  $it->user_address   ;
            $user_email     [$it->user_email]      =  $it->user_email     ;
            $user_name      [$it->user_name]       =  $it->user_name      ;
        }
       

        return view("user_activation.index")->with(compact("user_name","user_products","user_mobile","user_services","user_status","user_addresses","user_usernames","user_email","company_name")); 
    }
    //  .. List of users requested
    public function shows() {
        // if(auth()->user()-can("user_list.show") ){
        //     abort(403,"UnAuthentication actions");   
        // }
      
        $user_products    = [];
        $user_mobile      = [];
        $user_services    = [];
        $user_status      = [];
        $user_usernames   = [];
        $user_addresses   = [];
        $user_email       = [];
        $user_name        = [];
        $userActivations = \App\Models\CodeRequest::select()->where("type","Activation")->get();
    
        if(request()->ajax()){
        
            $userActivation = \App\Models\CodeRequest::select()->where("type","Activation");
             
            if(!empty(request()->user_mobile)){
               $filter = request()->user_mobile;
               $userActivation->where("mobile",$filter);  
            }
            if(!empty(request()->user_service)){
               $filter = request()->user_service;
               $userActivation->where("services",$filter);  
            }
             
            if(!empty(request()->user_username)){
                $filter = request()->user_username;
                $userActivation->where("device_no",$filter); 
            }
            if(!empty(request()->user_address)){
               $filter = request()->user_address;
               $userActivation->where("address",$filter);  
            }
            if(!empty(request()->user_email)){
                $filter = request()->user_email;
                $userActivation->where("email",$filter); 
            }
            if(!empty(request()->user_name)){
               $filter =request()->user_name ;
               $userActivation->where("name",$filter);  
            }
            if(!empty(request()->user_date)){
               $filter = request()->user_date ;
               $userActivation->whereDate("created_at","<=",$filter);  
            }
            
            $userActivation->get();  
        
            return DataTables::of($userActivation)
                        ->editColumn("created_at",function($row){
                            return $row->created_at->format('Y-m-d H:i:s');  
                        })
                        ->addColumn("activate",function($row){
                            $html   = "<button class='btn btn-modal btn-info' data-container='.view_modal' data-href='". action('UserActivationController@activate',[$row->id]) . "'>";
                            $html  .= __("lang_v1.activate");
                            $html  .= "</button>";
                            return $html; 
                        })
                        ->rawColumns([
                        "activate", 
                        "id", 
                        "services",
                        "name",
                        "email",
                        "address",
                        "mobile",
                        "device_no",
                        "created_at"])
                        ->make(true) ;
                        
        }

        foreach($userActivations as $it){
            $user_mobile    [$it->mobile]       =  $it->mobile    ;
            $user_services  [$it->services]     =  $it->services  ;
            $user_usernames [$it->device_no]    =  $it->device_no ;
            $user_addresses [$it->address]      =  $it->address   ;
            $user_email     [$it->email]        =  $it->email     ;
            $user_name      [$it->name]         =  $it->name      ;
        }
       

        return view("user_activation.show")->with(compact("user_name","user_mobile","user_services","user_addresses","user_usernames","user_email")); 
    }
    //  .. Create new customers
    public function create(Request $request) {
        // if(auth()->user()-can("user_create.create") ){
        //     abort(403,"UnAuthentication actions");   
        // }
        return view("user_activation.create");
    }
    //  .. Save new user 
    public function store(Request $request) {
        // if(auth()->user()-can("user_save.create") ){
        //     abort(403,"UnAuthentication actions");   
        // }
        try {
            $data = $request->except("_token"); 
            \App\Models\UserActivation::post_add($data);
            $output = [
                "success" => 1,
                "msg" => __("messages.added_successfull")
            ];
        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine(). "Message:" .$e->getMessage());
            \Log::alert($e);
            $output = [
                "success" => 0,
                "msg" =>  __("messages.something_went_wrong"),
            ];
        }
        
        return redirect("/user-activation")->with("status",$output);
    }
    //  .. Edit old user
    public function edit($id,Request $request) {
        // if(auth()->user()-can("user_update.edit") ){
        //     abort(403,"UnAuthentication actions");   
        // }
        return view("user_activation.edit");
    }
    //  .. update the changes on user
    public function update($id,Request $request) {
        // if(auth()->user()-can("user_save_change.edit") ){
        //     abort(403,"UnAuthentication actions");   
        // }
        $output = [
                "success" => 1,
                "msg" => __("messages.update_successfully")
        ];
        return $output;
    }
    //  .. Delete the user
    public function delete($id,Request $request) {
        // if(auth()->user()-can("user_delete.delete") ){
        //     abort(403,"UnAuthentication actions");   
        // }
        $output = [
                "success" => 1,
                "msg" => __("messages.delete_successfully")
        ];
        return $output;
    }
    //  .. List of users requested
    public function login() {
        // if(auth()->user()-can("user_list.show") ){
        //     abort(403,"UnAuthentication actions");   
        // }
      
        $user_products    = [];
        $user_mobile      = [];
        $user_services    = [];
        $user_status      = [];
        $user_usernames   = [];
        $user_addresses   = [];
        $user_email       = [];
        $user_name        = [];
        $userActivations = \App\Models\CodeRequest::select()->where("type","Register")->get();
    
        if(request()->ajax()){
        
            $userActivation = \App\Models\CodeRequest::select()->where("type","Register");
             
            if(!empty(request()->user_mobile)){
               $filter = request()->user_mobile;
               $userActivation->where("mobile",$filter);  
            }
            if(!empty(request()->user_service)){
               $filter = request()->user_service;
               $userActivation->where("services",$filter);  
            }
             
            if(!empty(request()->user_username)){
                $filter = request()->user_username;
                $userActivation->where("device_no",$filter); 
            }
            if(!empty(request()->user_address)){
               $filter = request()->user_address;
               $userActivation->where("address",$filter);  
            }
            if(!empty(request()->user_email)){
                $filter = request()->user_email;
                $userActivation->where("email",$filter); 
            }
            if(!empty(request()->user_name)){
               $filter =request()->user_name ;
               $userActivation->where("name",$filter);  
            }
            if(!empty(request()->user_date)){
               $filter = request()->user_date ;
               $userActivation->whereDate("created_at","<=",$filter);  
            }
            
            $userActivation->get();  
        
            return DataTables::of($userActivation)
                        ->editColumn("created_at",function($row){
                            return $row->created_at->format('Y-m-d H:i:s');  
                        })
                        ->addColumn("activate",function($row){
                            $html   = "<button class='btn btn-modal btn-info' data-container='.view_modal' data-href='". action('UserActivationController@activate',[$row->id]) . "'>";
                            $html  .= __("lang_v1.activate");
                            $html  .= "</button>";
                            return $html; 
                        })
                        ->rawColumns([
                        "activate", 
                        "id", 
                        "services",
                        "name",
                        "email",
                        "address",
                        "mobile",
                        "device_no",
                        "created_at"])
                        ->make(true) ;
                        
        }

        foreach($userActivations as $it){
            $user_mobile    [$it->mobile]       =  $it->mobile    ;
            $user_services  [$it->services]     =  $it->services  ;
            $user_usernames [$it->device_no]    =  $it->device_no ;
            $user_addresses [$it->address]      =  $it->address   ;
            $user_email     [$it->email]        =  $it->email     ;
            $user_name      [$it->name]         =  $it->name      ;
        }
       

        return view("user_activation.login")->with(compact("user_name","user_mobile","user_services","user_addresses","user_usernames","user_email")); 
    }
    // .... check activation 
    public function checkActivation(Request $request){
        if(request()->ajax()){
            $user_activation = \App\Models\UserActivation::where("user_username",request()->input("username"))->first();
            if($user_activation){
               
                 if(Hash::check(request()->input("val"),$user_activation->user_password)){
                    $response =[
                        "success" => 1,
                        "data"    => $user_activation
                    ];
                }else{
                    $response =[
                        "success" => 0,
                        "data"    => $user_activation
                    ];
                }
            }else{
                $response =[
                    "success" => 2,
                    "data"    => null
                ];
            }
            return $response;
        }
    }
    //  ....  
    public function activate(Request $request,$id){

        $device = \App\Models\CodeRequest::find($id);
        $device_no = ($device)?($device):null;
        return view("user_activation.activate")->with(compact("device_no"));
    }

}
