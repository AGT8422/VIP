<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AuthIzo
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

        if (!\Auth::user()) {
            return redirect("/login-account");
        }
        #.......................
        $user            = \App\Models\User::find(\Auth::user()->id);
        $session         = \App\Models\SessionTable::where("user_id",\Auth::user()->id)->first();
        if(!$session){
            $sessionData     = request()->session()->all();
            \Config::set('session.driver','database');
            foreach( $sessionData as $key => $value ){
                session()->put($key,$value);
            }
            session()->put("create_ses","yes");
        }else{
                if(request()->header('user-agent') != $session->user_agent && request()->ip() != $session->ip_address ){
                    // session()->put("create_ses","yes");
                    // $business_id          = request()->session()->get('user.business_id');
                    // $user_id              = request()->session()->get('user.id');
                    // $i                    = request()->input('lang');
                    // $input['id']          = $user_id;
                    // $input['business_id'] = $business_id;
                    // $input                = ["language"=>"en"];
                    // $user                 = \App\User::find($user_id);
                    // $user->update($input);
                    session()->flush();
                    \Auth::logout(); 
                    return redirect('/login-account'); 
                }else{
                    if($session->user_actives != null){
                        if($session->user_actives != (request()->header('user-agent')."_".request()->ip()."_".$user->username."_".$user->id)){
                            // $business_id          = request()->session()->get('user.business_id');
                            // $user_id              = request()->session()->get('user.id');
                            // $i                    = request()->input('lang');
                            // $input['id']          = $user_id;
                            // $input['business_id'] = $business_id;
                            // $input                = ["language"=>"en"];
                            // $user                 = \App\User::find($user_id);
                            // $user->update($input);
                            session()->flush();
                            \Auth::logout(); 
                            return redirect('/login-account'); 
                        }
                    }
                }
            } 
        // if(!session()->get('create_session')){
            // $session         = \App\Models\SessionTable::where("user_id",\Auth::user()->id)->delete();
            // $sessionData     = request()->session()->all();
            // \Config::set('session.driver','database');
            // foreach( $sessionData as $key => $value ){
            //     session()->put($key,$value);
            // }
        // }else{
        // }  
        #.......................
        // dd(session()->all());
       
        return $next($request);
    }
}
