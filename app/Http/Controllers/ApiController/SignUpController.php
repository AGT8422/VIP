<?php

namespace App\Http\Controllers\ApiController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SignUpController extends Controller
{
    // **1** E-Commerce SignUp
    public function SignUp(Request $request){
        // **/1/** save client in E-Commerce Client Table     
        try{
            $validator = $request->validate(
                [
                    // 'first_name'  => 'required|max:255',
                    // 'second_name' => 'required|max:255',
                    // 'username'    => 'required|max:255',
                    'password'    => 'required|min:4|max:255',
                    'email'       => 'required|nullable|email|unique:users|max:255',
                    // 'mobile'      => 'required|numeric',
                    // 'language'    => 'required',
                    // 'address'     => 'max:255',
                ],[
                    // 'first_name.required' => __('validation.required', ['attribute' => __('e_commerce.first_name')]),
                    // 'second_name.required' => __('validation.required', ['attribute' => __('e_commerce.second_name')]),
                    // 'username.required' => __('validation.required', ['attribute' => __('e_commerce.username')]),
                    'password.required' => __('validation.required', ['attribute' => __('e_commerce.password')]),
                    'password.confirmed' => __('validation.confirmed', ['attribute' => __('e_commerce.confirmed')]),
                    'email.required' => __('validation.required', ['attribute' => __('e_commerce.email')]),
                    'email.email' => __('validation.email', ['attribute' => __('e_commerce.email')]),
                    'email.email' => __('validation.unique', ['attribute' => __('e_commerce.unique')]),             
                    // 'mobile.number' => __('validation.number', ['attribute' => __('e_commerce.number')]),           
                     ]
            );
            
            \DB::beginTransaction();
            
            // *1* prepare request data
            $data = $request->only([
                        // 'business_name',
                        // 'business_type',
                        'first_name',
                        'second_name',
                        // 'username',
                        'password',
                        'email',
                        'mobile',
                        // 'address',
                        // 'dob',
                        // 'email_personal',
                        // 'email_work',
                        // 'mobile_work',
                        // 'gender',
                        // 'language',
                    ]);
            // *2* save new client
            $register = \App\Models\e_commerceClient::signup($data,$request);
            \DB::commit();
            return $register;
            
        }catch(Exception $e){
            return response([
                "status "    => 403,
                "message"   => __('failed')  
            ]);
        }
        
    } 
}
