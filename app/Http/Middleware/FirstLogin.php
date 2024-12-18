<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class FirstLogin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {    
        // dd("firstLogin"); 
        // dd($request->session()->get('startLogin'));  
        if (!$request->session()->get('startLogin')) {
            return redirect('/login-account');
        }else if(!Hash::check("success", $request->session()->get('startLogin.value1'))){
            return redirect('/login-account');
        }
        // else if(!$request->session()->get('user')){
        //     return redirect('/panel-account');
        // }
        return $next($request);
    }
}
