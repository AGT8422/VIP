<?php

namespace App\Http\Middleware;

use Closure;

use Config;
use App;
use Auth;
class Language
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // $locale = config('app.locale');
        // if ($request->session()->has('user.language')) {
        //     $locale = $request->session()->get('user.language');
        // }
        // App::setLocale($locale);
        $locale =  'ar';
        
        if(Auth::id()){
            $locale =  Auth::User()->language;
        }
      
        App::setLocale($locale);
        return $next($request);
    }
}
