<?php

namespace App\Http\Controllers\ApiController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use App\Models\Ecommerce\Eadmin;
use Firebase\JWT\JWT;

use Illuminate\Support\Str;


class LogInController extends Controller
{
    // ..................................................ADMINS....
        // **0** E-Commerce Log In Admin
        public function ALogIn(Request $request){
            // **/1/** check admin in E-Commerce Client Table     
            try{
                $validator = $request->validate(
                    [
                        'username'    => 'required|max:255',
                        'password'    => 'required|min:2|max:255',
                        // 'email'       => 'required|sometimes|nullable|email|unique:users|max:255',
                        // 'language'    => 'required',
                    ],[
                        'username.required' => __('validation.required', ['attribute' => __('e_commerce.username')]),
                        'password.required' => __('validation.required', ['attribute' => __('e_commerce.password')]),
                        // 'email.required' => __('validation.required', ['attribute' => __('e_commerce.email')]),
                        // 'email.email' => __('validation.email', ['attribute' => __('e_commerce.email')]),
                        // 'email.email' => __('validation.unique', ['attribute' => __('e_commerce.unique')]),           
                    ]
                );
                $data  = $request->only(['username','password']);
                $check = Eadmin::login($data);
                if($check["status"] == false){
                    return response([
                    "status "    => 403,
                    "message"   => __('Please Check your username or password')  
                ]);
                }else{
                    return response([
                        "status "    => 200,
                        "jwt"        => $check["token"],
                        "message"   => __('Login successfully')  
                    ]);
                }
            }catch(Exception $e){
                return response([
                    "status "    => 403,
                    "message"   => __('Failed')  
                ],403);
            }
        } 
        // **0** E-Commerce Log out Admin
        public function ALogOut(Request $request){
            // **/1/** check admin in E-Commerce Client Table     
            try{
                $main_token     = $request->header('Authorization');
                $data["token"]  = substr($main_token,7);
                $check          = Eadmin::logout($data);
                return  $check;
            }catch(Exception $e){
                return response([
                    "status "    => 403,
                    "message"   => __('e_commerce.failed')  
                ]);
            }
        } 
    // ..................................................CLIENT....
    //  **1** E-Commerce Login
    public function Login(Request $request) {
        // **/1/** check client in E-Commerce Client Table     
        try{
            $validator = $request->validate(
                [
                    'username'    => 'required|max:255',
                    'password'    => 'required|min:2|max:255',
                    // 'email'       => 'required|sometimes|nullable|email|unique:users|max:255',
                    // 'language'    => 'required',
                ],[
                    'username.required' => __('validation.required', ['attribute' => __('e_commerce.username')]),
                    'password.required' => __('validation.required', ['attribute' => __('e_commerce.password')]),
                    'password.confirmed' => __('validation.confirmed', ['attribute' => __('e_commerce.confirmed')])
                    // 'email.required' => __('validation.required', ['attribute' => __('e_commerce.email')]),
                    // 'email.email' => __('validation.email', ['attribute' => __('e_commerce.email')]),
                    // 'email.email' => __('validation.unique', ['attribute' => __('e_commerce.unique')]),           
                ]
            );
            $data  = $request->only(['username','password']);
            $check = \App\Models\e_commerceClient::login($data);
            return  $check;
        }catch(Exception $e){
            return response([
                "status "    => 403,
                "message"   => __('e_commerce.failed')  
            ]);
        }
    } 
    //  **2** E-Commerce refresh function
    public function Refresh(Request $request) {
        try{
            $main_token     = $request->header('Authorization');
            $data["token"]  = substr($main_token,7);
            $check = \App\Models\e_commerceClient::refreshToken($data);
            return $check;
        }catch(Exception $e){
            return response([
                "status "    => 403,
                "message"   => __('e_commerce.failed')  
            ]);
        }
    }
    //  **3** E-Commerce logout function
    public function logout(Request $request) {
        try{
            $main_token     = $request->header('Authorization');
            $data["token"]  = substr($main_token,7);
            
            $check = \App\Models\e_commerceClient::logout($data);
            return $check;
        }catch(Exception $e){
            return response([
                "status "    => 403,
                "message"   => __('e_commerce.failed')  
            ]);
        }
    }
    //  **4** E-Commerce redirect function
    public function forgetPassword(Request $request) {
        try{
            $data  = $request->only(['email']);
            $check = \App\Models\e_commerceClient::forgetPassword($data);
            if($check["status"] == 200){
                 return $check;
            }else{
                return response([
                    "status"  => "403",
                    "message" => "Invalid",
                    "result"  => 0
                ],403);
            }
            
        }catch(Exception $e){
            return response([
                "status "    => 403,
                "message"   => __('e_commerce.failed')  
            ],403);
        }
    }
    //  **5** E-Commerce save Page function
    public function forgetSave(Request $request) {
        try{
            // $main_token     = $request->header('Authorization');
            $data           = $request->only(['key','email','password']);
            // $data["token"]  = substr($main_token,7);
            $check          = \App\Models\e_commerceClient::forgetSave($data);
            return $check;
        }catch(Exception $e){
            return response([
                "status "    => 403,
                "message"   => __('e_commerce.failed')  
            ]);
        }
    }
    //  **6** E-Commerce login google Page function
    public function loginGoogle(Request $request) {
        try{
            
            $main_token     = $request->header('Authorization');
            $token          = substr($main_token,7);
            if($token == null || $token == ""){
                // return response([
                //     "status "    => 403,
                //     "message"   => __('failed')  
                // ]);
            }
            $data           = $request->only(['idToken' ]);
            $data["token"]  = $token;
            $check = \App\Models\e_commerceClient::loginGoogle($data,$request);
            return $check;
        }catch(Exception $e){
            return response([
                "status "    => 403,
                "message"   => __('e_commerce.failed')  
            ]);
        }
    }
    //  **7** E-Commerce getChanged function
    public function getChanged(Request $request) {
        try{
            
            $main_token      = $request->header();
            
            return view("password.change");
        }catch(Exception $e){
            return response([
                "status "    => 403,
                "message"   => __('e_commerce.failed')  
            ]);
        }
    }
    //  **8** E-Commerce AuthImage function
    public function AuthImage(Request $request) {
        try{
            $type = $request->input("type");
            if($type == "login"){
                $e_commerce = \App\Models\Ecommerce::where("login",1)->first();
                return response([
                    "status "    => 200,
                    "image"      => ($e_commerce)?$e_commerce->image_url:"https://montevista.greatheartsamerica.org/wp-content/uploads/sites/2/2016/11/default-placeholder.png",
                    "message"    => __('Image successfully')  
                ],200);
            }elseif($type == "signup"){
                $e_commerce = \App\Models\Ecommerce::where("signup",1)->first();
                return response([
                    "status "    => 200,
                    "image"      => ($e_commerce)?$e_commerce->image_url:"https://montevista.greatheartsamerica.org/wp-content/uploads/sites/2/2016/11/default-placeholder.png",
                    "message"    => __('Image successfully')  
                ],200);
            }else{
                return response([
                    "status "    => 200,
                    "image"      => "https://montevista.greatheartsamerica.org/wp-content/uploads/sites/2/2016/11/default-placeholder.png",
                    "message"    => __('Image successfully')  
                ],200);
            }
        }catch(Exception $e){
            return response([
                "status "    => 403,
                "message"   => __('e_commerce.failed')  
            ]);
        }
    }
}
