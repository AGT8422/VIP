<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class adminIzo
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
        if(!request()->session()->get('secret')){
            // $timestamp       = request()->session()->get('secret.exp');  // Your Unix timestamp
            // // Convert the Unix timestamp to a Carbon instance
            // $timestampCarbon = \Carbon::createFromTimestamp($timestamp);
            // // Get the current date and time
            // $currentDateTime = \Carbon::now();
            // // Compare the two dates
            // if ($timestampCarbon->isBefore($currentDateTime)) {
            //     return response()->json([
            //         "success" => 0, 
            //         "msg"     => __('failed expire')
            //     ]) ;
            // }  
            return redirect('/login-account');
        }
        return $next($request);
    }
}
