<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\Utils\BusinessUtil;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

use App\Business;

class SetSessionData
{
    /**
     * Checks if session data is set or not for a user. If data is not set then set it.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        
        if (!$request->session()->has('user')) {
            $business_util = new BusinessUtil;
            $i    = session()->get('lang');
            $user = Auth::user();
            
            $session_data = ['id'         => $user->id,
                            'surname'     => $user->surname,
                            'first_name'  => $user->first_name,
                            'last_name'   => $user->last_name,
                            'email'       => $user->email,
                            'business_id' => $user->business_id,
                            'language'    => $i,
                            ];


            $business = Business::findOrFail($user->business_id);
            // dd( Config::get('database.connections.mysql.database'));
            $currency                                =  $business->currency;
            $currency_data = [  'id'                 => $currency->id,
                                'code'               => $currency->code,
                                'symbol'             => $currency->symbol,
                                'thousand_separator' => $currency->thousand_separator,
                                'decimal_separator'  => $currency->decimal_separator,
                                'currency'           => $currency->currency,
                            ];
            $request->session()->put('user', $session_data);
            session()->put('user', $session_data);
            $request->session()->put('business', $business);
            session()->put('business', $business);
            $request->session()->put('currency', $currency_data);
            session()->put('currency', $currency_data);
            $request->session()->put('locale', $i); 
            session()->put('locale', $i); 
            $financial_year = $business_util->getCurrentFinancialYear($business->id);
            $request->session()->put('financial_year', $financial_year);
            session()->put('financial_year', $financial_year);
            //set current financial year to session
            // dd($user); 
            // dd(session()->all());
        }
        return $next($request);
    }
}
