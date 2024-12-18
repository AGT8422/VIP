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
                $databaseName = $customer->database_name ;  
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
                    return redirect('/');
                }
            } 
        } 
        return $next($request);
    }
}
