<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Cookie;

class SetDatabaseConnection
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {

        
        // Determine the database name dynamically
        // $databaseName = $request->header('X-DB-Name', 'albasee2_da');
        // if($request->input('custom_header')){
        //     $databaseName = $request->input('custom_header'); 
        //     $cookie       = Cookie::queue('database', $databaseName, 60);
        // }else{ 
        //     if(Cookie::has('database') != false){
        //         $databaseName = Cookie::get('database');
        //     }
        // }
        // request()->session()->flush(); 
        // dd("database");
        if($request->session()->get('startLogin')){
            $databaseName = request()->session()->get('user_main.database');
           
        }else{
            $databaseName = 'izocloud';
        }        
        // Set the database configuration dynamically
        DB::purge('mysql');
        Config::set('database.connections.mysql.database', $databaseName);
        DB::reconnect('mysql'); 
        
        // Schema::connection('mysql')->getConnection()->reconnect();
        return $next($request);
    }
}
