<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Console\Output\ConsoleOutput;

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
        // dd(Auth::user());
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
                    // dd("Stop1");
                    session()->forget('device_id');
                    session()->forget('user_main');
                    session()->forget('password');
                    session()->forget('startLogin');
                    session()->forget('create_session');
                    session()->forget('user');
                    session()->forget('business');
                    session()->forget('currency');
                    session()->forget('locale');
                    session()->forget('financial_year');
                    session()->put('log_out_back','logout');
                    \Auth::logout(); 
                    return redirect('/login-account'); 
                }else{
                    if($session->user_actives != null){
                        if($session->user_actives != (request()->header('user-agent')."_".request()->ip()."_".$user->username."_".$user->id."_". session('device_id'))){
                            // $databaseName     =  "izo26102024_elke" ; 
                            // if($databaseName == Config::get('database.connections.mysql.database')){
                            //     $output   = new ConsoleOutput();
                            //     // $put      = passthru('php artisan get:ip-address');
                            //     $exitCode = Artisan::call('get:ip-address',[],$output);
                            //     $outputs  = Artisan::output();
                            //     $serverIp  = shell_exec("sudo ip -4 addr show | grep inet | awk '{print $2}'| cut -d/ -f1");
                            //     dd( $serverIp, $exitCode, $outputs ,session()->all(),session("ipv_device"),session("ip_device"));
                            // }
                            // dd(gethostname(),gethostbyname(gethostname()),"Stop2",$session->user_actives,request()->header('user-agent')."_".request()->ip()."_".$user->username."_".$user->id."_". session('device_id'));
                            // $business_id          = request()->session()->get('user.business_id');
                            // $user_id              = request()->session()->get('user.id');
                            // $i                    = request()->input('lang');
                            // $input['id']          = $user_id;
                            // $input['business_id'] = $business_id;
                            // $input                = ["language"=>"en"];
                            // $user                 = \App\User::find($user_id);
                            // $user->update($input); 
                            // session()->forget('device_id');
                            // session()->forget('user_main');
                            // session()->forget('password');
                            // session()->forget('startLogin');
                            // session()->forget('create_session');
                            // session()->forget('user');
                            // session()->forget('business');
                            // session()->forget('currency');
                            // session()->forget('locale');
                            // session()->forget('financial_year');
                            // session()->put('log_out_back','logout');
                            // \Auth::logout(); 
                            // return redirect('/login-account'); 
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
