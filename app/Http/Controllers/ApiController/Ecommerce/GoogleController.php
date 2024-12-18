<?php

namespace App\Http\Controllers\ApiController\Ecommerce;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

use Exception;
class GoogleController extends Controller
{
    //**1 */
    public function googlePage() {
        return Socialite::driver('google')->redirect();
    }
    //**2 */
    public function googleCallBack() {
        try{
            
             
            $user      = Socialite::driver('google')->stateless()->user();
            $findUser  = \App\Models\User::where('google_id',$user->id)->first();
            
            if($findUser){
                Auth::login($findUser);
                return redirect()->back();
            }else{
                $newUser = \App\Models\User::create([
                    'first_name'  => $user->name,
                    'email'       => $user->email,
                    'google_id'   => $user->id,
                    'password'    => Hash::make("123456"),
                ]);
                
                Auth::login($newUser);
                return redirect("/");
                
            }
        }catch(Exception $e){
          
            
           return  redirect()->back();
        }
    }
}
