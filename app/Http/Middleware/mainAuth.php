<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Cookie;
use App\Models\IzoUser;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Artisan;

class mainAuth
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
         
        
        Config::set('database.connections.mysql.database', "izocloud");
        DB::purge('mysql');
        DB::reconnect('mysql');
        
        if (!(session()->has('user_main'))) {
            return redirect('/login-account');
        }else{
            $customer     = IzoUser::where('email',session('user_main')['email'])->first();
            if(!empty($customer)){
                if(request()->session()->get('redirect_admin')){
                    $databaseName = request()->session()->get('redirect_admin.database');  
                }else{
                    $databaseName = $customer->database_name ;  
                }
                Config::set('database.connections.mysql.database', $databaseName);
                DB::purge('mysql');
                DB::reconnect('mysql');
                $payload =  [
                    "value1" => Hash::make("success"),
                    "value2" => $customer->password,
                ];
                session(['startLogin'  => $payload]);
                if($customer->is_migrate == 0){
                    $exitCode = Artisan::call('migrate');
                    $output   = Artisan::output();
                    Config::set('database.connections.mysql.database', "izocloud");
                    DB::purge('mysql');
                    DB::reconnect('mysql');
                    $customer->is_migrate = 1;
                    $customer->update();
                    Config::set('database.connections.mysql.database', $databaseName);
                    DB::purge('mysql');
                    DB::reconnect('mysql');
                } 
                if($customer->admin_user  != 1){
                    $login_user = 1; 
                    $domain_url  = request()->session()->get('user_main.domain_url'); 
                    $database    = request()->session()->get('user_main.database'); 
                    $domain      = request()->session()->get('user_main.domain');
                    $domain_name = "https://".session()->get('user_main.domain').".izocloud.com";
                    $domain_name = $domain_name??"";
                    $text        = "email=".request()->session()->get("login_info.email")."_##password=".request()->session()->get("login_info.password")."_##logoutOther=".request()->session()->get("login_info.logoutOther")."_##administrator=1_##database=".$database."_##adminDatabaseUser=".$database."_##domain=".$domain."_##domain_url=".$domain_url."_##redirect=admin";
                    $text        =  Crypt::encryptString($text);
                    $url         = $domain_name."/login-account-redirect"."/".$text;    
                    return redirect($url);
                    // dd($url,session()->all()); 
                }

            } 
        } 
        return $next($request);
    }
}
