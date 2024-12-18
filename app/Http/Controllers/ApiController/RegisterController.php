<?php

namespace App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Http;
use App\Business;
use App\Currency;
use App\Account;
use App\Notifications\TestEmailNotification;
use App\System;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Spatie\Activitylog\Models\Activity;
use Laravel\Sanctum\Contracts;
use Illuminate\Session\Store;
use Utils\Util;
use App\Models\ExchangeRate;
use Illuminate\Support\Str;
use App\Utils\BusinessUtil;
use App\Utils\ModuleUtil;
use App\Utils\RestaurantUtil;
use Carbon\Carbon;
use Spatie\Permission\Models\Permission;
use DateTimeZone;
 use Illuminate\Support\Facades\DB;
class RegisterController extends Controller
{   

    protected $businessUtil;
    protected $restaurantUtil;
    protected $moduleUtil;
    protected $mailDrivers;
    /**
     * Constructor
     *
     * @param ProductUtils $product
     * @return void
     */
    public function __construct(BusinessUtil $businessUtil, RestaurantUtil $restaurantUtil, ModuleUtil $moduleUtil)
    {
        $this->businessUtil = $businessUtil;
        $this->moduleUtil = $moduleUtil;
        
        $this->theme_colors = [
            'blue' => 'Blue',
            'black' => 'Black',
            'purple' => 'Purple',
            'green' => 'Green',
            'red' => 'Red',
            'yellow' => 'Yellow',
            'blue-light' => 'Blue Light',
            'black-light' => 'Black Light',
            'purple-light' => 'Purple Light',
            'green-light' => 'Green Light',
            'red-light' => 'Red Light',
        ];

        $this->mailDrivers = [
                'smtp' => 'SMTP',
                // 'sendmail' => 'Sendmail',
                // 'mailgun' => 'Mailgun',
                // 'mandrill' => 'Mandrill',
                // 'ses' => 'SES',
                // 'sparkpost' => 'Sparkpost'
            ];
    }
    // *1***
    public function login(Request $request) {
        
        $username    =  $request->username;
        $password    =  $request->password;
        $device_id   =  $request->device_id;
        $device_ip   =  $request->device_ip;
        if(($device_ip != "" && $device_ip != null) && ($device_id != "" && $device_id != null)){
            $MApp   = \App\Models\MobileApp::get();
            if(count($MApp)==0){
               abort(404,"not found");
            }
            $MobileApp   = \App\Models\MobileApp::where("username",$username)->first();
            if(!empty($MobileApp)){
                if(Hash::check($password,$MobileApp->password)){
                    $Mobile = \App\Models\MobileApp::where("username",$username)->where("device_id",$device_id)->first();
                    if(!empty($Mobile)){
                        $Mobile->device_ip = $device_ip;
                        $Mobile->last_login = \Carbon::now();
                        $Mobile->update();
                    }else{
                        if($MobileApp->device_id == null){
                            $MobileApp->device_id = $device_id;
                            $MobileApp->device_ip = $device_ip;
                            $MobileApp->last_login = \Carbon::now();
                            $MobileApp->update();
                        }else{
                             
                            $data = [];
                            $data["device_id"] = $device_id;
                            $data["device_ip"] = $device_ip;
                            $data["last_login"] = \Carbon::now();
                            \App\Models\MobileApp::replicateTable($MobileApp->id,$data);
                        }
                    }
                    return response([
                        "status"  => 200,
                        "message" => "Successfully Access" ,
                        "api_url" => $MobileApp->api_url,
                    ]);
                }else{
                    abort(404,"No Data Found");
                    
                } 
            }else{
                  
                abort(404,"No Data Found");
            }
        }else{
            abort(404,"Invaild Information");
        }


    }
    // *2***
    public function saveApi(Request $request){
        $data    = $request;
        try{
            $connect = \App\Models\FrontendConnection::saveApi($data);
            return response(
                [
                        "status"=>200,
                        "msg"=>__('Connection Successfully')
                    ]
            );
        }catch(Exception $e){
             
            return response(
                [
                        "status"=>403,
                        "msg"=>__('Something Wrong')
                    ]
            );
        }

    }
    // *3*** for first time
    public function loginFront(Request $request) {
        
        $username    =  $request->username;
        $password    =  $request->password;
        $device_id   =  $request->header("User-Agent");
        $device_ip   =  $request->ip();
          
        if(($device_ip != "" && $device_ip != null) && ($device_id != "" && $device_id != null)){
            $FApp   = \App\Models\ReactFront::get();
            
            if(count($FApp)==0){
               abort(404,"not found");
            }
            $ReactFronts   = \App\Models\ReactFront::where("username",$username)->get();
            $check = 1;
            foreach($ReactFronts as $ReactFront){
                if(!empty($ReactFront)){
                    if(Hash::check($password,$ReactFront->password)){
                        $Front = \App\Models\ReactFront::where("username",$username)->where("device_id",$device_id)->first();
                        if(!empty($Front)){
                            $Front->device_ip = $device_ip;
                            $Front->last_login = \Carbon::now();
                            $Front->update();
                        }else{
                            if($ReactFront->device_id == null){
                                $ReactFront->device_id = $device_id;
                                $ReactFront->device_ip = $device_ip;
                                $ReactFront->last_login = \Carbon::now();
                                $ReactFront->update();
                            }else{
                                 
                                $data = [];
                                $data["device_id"] = $device_id;
                                $data["device_ip"] = $device_ip;
                                $data["last_login"] = \Carbon::now();
                                \App\Models\ReactFront::replicateTable($ReactFront->id,$data);
                            }
                        }
                        $response     = Http::post($ReactFront->api_url . '/connection',[
                            "api_url" => $ReactFront->api_url
                        ]);
                        $responseData      = $response->json();
                        $response_data     = Http::get($ReactFront->api_url . '/get-user');
                        $resData           = $response_data->json();
                        return response([
                            "status"  => 200,
                            "message" => "Successfully Access" ,
                            "api_url" => $ReactFront->api_url,
                            "users"   => $resData["users"],
                        ]);
                         $check = 0;
                         break;
                    }else{
                         $check = 1;
                        
                    } 
                }
            }
            if($check == 1){
                abort(404,"No Data Found");
            }
        }else{
            abort(404,"Invalid Information");
        }


    }
    // *4*** 
    public function RegisterBusiness(Request $request){
        
        try {
             $validator = $request->validate(
                [
                    'name' => 'required|max:255',
                    'currency_id' => 'required|numeric',
                    'surname' => 'max:10',
                    'email' => 'sometimes|nullable|email|unique:users|max:255',
                    'first_name' => 'required|max:255',
                    'username' => 'required|min:4|max:255|unique:users',
                    'password' => 'required|min:4|max:255',
                
                ],
                [
                    'name.required' => __('validation.required', ['attribute' => __('business.business_name')]),
                    'currency_id.required' => __('validation.required', ['attribute' => __('business.currency')]),
                    'email.email' => __('validation.email', ['attribute' => __('business.email')]),
                    'email.email' => __('validation.unique', ['attribute' => __('business.email')]),
                    'first_name.required' => __('validation.required', ['attribute' =>
                        __('business.first_name')]),
                    'username.required' => __('validation.required', ['attribute' => __('business.username')]),
                    'username.min' => __('validation.min', ['attribute' => __('business.username')]),
                    'password.required' => __('validation.required', ['attribute' => __('business.username')]),
                    'password.min' => __('validation.min', ['attribute' => __('business.username')]),
                    
                ]
            );
            
            DB::beginTransaction();
             
            $owner_details                       = $request->only(['surname', 'first_name', 'last_name', 'username', 'email', 'password', 'language']);
            $owner_details['language']           = empty($owner_details['language']) ? config('app.locale') : $owner_details['language'];
            $user                                = \App\User::create_user($owner_details);
            $business_details                    = $request->only(['name',  'currency_id', ]);
            $business_details['start_date']      = \Carbon::today()->format("d/m/Y");
            $business_details['time_zone']       = "Asia/Dubai";
            $business_details['fy_start_month']  = 1; 
            $business_location                   = $request->only(['name', 'mobile', 'alternate_number']);
            $business_details['owner_id']        = $user->id;
            $business_location['country']        = "Dubai";
            $business_location['state']          = "Street";
            $business_location['website']        = "www.izocloud.com";
            $business_location['city']           = "Emirates";
            $business_location['zip_code']       = "0909908";
            $business_location['landmark']       = "5";
            if (!empty($business_details['start_date'])) {
                $business_details['start_date']  = \Carbon::createFromFormat(config('constants.default_date_format'), $business_details['start_date'])->toDateString();
            }
          
            
            $logo_name                           = $this->businessUtil->uploadFile($request, 'business_logo', 'business_logos', 'image');
            if (!empty($logo_name)) {
                $business_details['logo']        = $logo_name;
            }
            $business_details['enabled_modules'] = ["purchases","add_sale","pos_sale","stock_transfers","stock_adjustment","expenses","account","tables","modifiers","service_staff","booking","kitchen","Warehouse","subscription","types_of_service"];
            $business                            = $this->businessUtil->createNewBusiness($business_details);
            $user->business_id                   = $business->id;
            $user->save();   
             
               
                                                  $this->businessUtil->newBusinessDefaultResources($business->id, $user->id);
            $new_location                        = $this->businessUtil->addLocation($business->id, $business_location);
            Permission::create(['name' => 'location.' . $new_location->id ]);
            DB::commit();
            if (config('app.env') != 'demo') {
                                                   $this->moduleUtil->getModuleData('after_business_created', ['business' => $business]);
            }
           
            $is_installed_superadmin             = $this->moduleUtil->isSuperadminInstalled();
            $package_id                          = $request->get('package_id', null);
            if ($is_installed_superadmin && !empty($package_id) && (config('app.env') != 'demo')) {
                $package                         = \Modules\Superadmin\Entities\Package::find($package_id);
                if (!empty($package)) {
                    Auth::login($user);
                    return redirect()->route('register-pay', ['package_id' => $package_id]);
                }
            }
            $currency_details                    = $request->only(["currency_id"]);
            $exchange_rate                       = \App\Models\ExchangeRate::where("currency_id",$currency_details)->where("source",1)->first();
            if(!empty($exchange_rate)){
                $exchange_rate->business_id      = $business->id;
                $exchange_rate->currency_id      = $business->currency_id;
                $exchange_rate->amount           = 1;
                $exchange_rate->opposit_amount   = 1;
                $exchange_rate->date             = \Carbon::now();
                $exchange_rate->default          = 0;
                $exchange_rate->source           = 1;
                $exchange_rate->update();
            }else{
                $exc                              = new ExchangeRate;
                $exc->business_id                 = $business->id;
                $exc->currency_id                 = $business->currency_id;
                $exc->amount                      = 1;
                $exc->opposit_amount              = 1;
                $exc->date                        = \Carbon::now();
                $exc->source                      = 1;
                $exc->default                     = 0;
                $exc->save();
            }

            return  response([
                        "status"  => 200,
                        "message" => "Successfully Registered" ,
                    ]);
        } catch (\Exception $e) {
            abort(403,"Invaild Information");
        }
    }
    // *5***
    public function getUser(Request $request){
        $users =  []; 
        $user  =  \App\User::where("id","!=",1)->where("allow_login",1)->get();
        
        foreach($user as $i){
                $users[] = [ 
                    "id"    => $i->id,
                    "value" => $i->username
                ];
        }
        return response([
                "status"=> 200,
                "users" => $users,
        ]);
    }
    // *6***
    public function currency(){
        $array = \App\Currency::currency();
        return response([
                    "status "    => 200,
                    "currencies" => $array
                ]);
    }
    // *7***
    public function language(){
        $array[] = [
                    'id'    =>  'en' ,
                    'value' =>  'English' 
                ];
        $array[] = [
                    'id'    =>  'ar' ,
                    'value' =>  'Arabic - العَرَبِيَّة' 
                ]; 
        // 'es' =>  'Spanish - Español',
        // 'sq' =>  'Albanian - Shqip', 
        // 'hi' =>  'Hindi - हिंदी', 
        // 'nl' =>  'Dutch' ,
        // 'fr' =>  'French - Français' ,
        // 'de' =>  'German - Deutsch' ,
        // 'tr' =>  'Turkish - Türkçe',  
        // 'id' =>  'Indonesian' ,
        // 'ps' =>  'Pashto' ,
        // 'pt' =>  'Portuguese',  
        // 'vi' =>  'Vietnamese' ,
        // 'ce' =>  'Chinese' ,
        // 'ro' =>  'Romanian' ,
        // 'lo' =>  'Lao' 
        return response([
                    "status "    => 200,
                    "languages"  => $array
                ]);
    } 


}