<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use DB;use Illuminate\Support\Facades\Session;


class SingleSessionMiddleware
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
        // if($request->user()->id == 1){
           
        //     $id           = $request->user()->id;
        //     $user_info    = \App\User::where('id', $id)->first();
        //     $_info = \DB::table('sessions')->where("user_id","=",$id)->where("payload","!=",$request->session()->get("_token"))->get();
        //     $session_info = \DB::table('sessions')->where("user_id",$id)->max("last_activity");
        //     if($session_info){
                  
        //             foreach($_info as $it){
        //                 \DB::table('sessions')->where("id","=",$it->id)->delete();
        //             }
                   
        //     }

        //  }
         return $next($request);
    }
}
