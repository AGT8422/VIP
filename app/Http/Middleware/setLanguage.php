<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class setLanguage
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
        if(session()->get('lang')){
            \App::setLocale(session()->get('lang')) ; 
            if(request()->session()->get('user')){
                $business_id          = request()->session()->get('user.business_id');
                $user_id              = request()->session()->get('user.id');
                $i                    = session()->get('lang');
                $input['id']          = $user_id;
                $input['business_id'] = $business_id;
                $input                = ["language"=>$i];
                $user                 = \App\User::find($user_id);
                $user->update($input);
                session()->put('user.language', $i);
                session()->put('locale', $i);  
            } 
        }else{  
            \App::setLocale(config('app.locale')) ;
        }
        return $next($request);
    }
}
