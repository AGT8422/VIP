<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Utils\BusinessUtil;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB; 
use Illuminate\Validation\Rule;
use App\Utils\ModuleUtil;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;
use Laravel\Sanctum\Contracts;
use App\User;
use Illuminate\Support\Facades\Auth;


class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * All Utils instance.
     *
     */
    protected $businessUtil;
    protected $moduleUtil;
    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    // protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(BusinessUtil $businessUtil, ModuleUtil $moduleUtil)
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('guest:api')->except('logout');
        $this->businessUtil = $businessUtil;
        $this->moduleUtil = $moduleUtil;
    }



     


    /**
     * Change authentication from email to username
     *
     * @return void
     */
    public function username()
    {
        return 'username';
    }

    public function logout()
    { 
        $this->businessUtil->activityLog(auth()->user(), 'logout');
        
        $business_id          = request()->session()->get('user.business_id');
        $user_id              = request()->session()->get('user.id');
        $i                    = request()->input('lang');
        $input['id']          = $user_id;
        $input['business_id'] = $business_id;
        $input                = ["language"=>"en"];
        $user                 = \App\User::find($user_id);
        $session              = \App\Models\SessionTable::where("user_id",\Auth::user()->id)->delete();
        $user->update($input);
        
        // request()->session()->flush();
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
       
    }

    function connect($hostname, $username, $password, $database)
        {
            // Erase the tenant connection, thus making Laravel get the default values all over again.
            DB::purge('business');
            // Make sure to use the database name we want to establish a connection.
            Config::set('database.connections.tenant.host', $hostname);
            Config::set('database.connections.tenant.database', $database);
            Config::set('database.connections.tenant.username', $username);
            Config::set('database.connections.tenant.password', $password);
            // Rearrange the connection data
            DB::reconnect('business');
            // Ping the database. This will throw an exception in case the database does not exists.
            Schema::connection('tenant')->getConnection()->reconnect();
        }

    /**
     * The user has been authenticated.
     * Check if the business is active or not.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function authenticated(Request $request, $user)
    {

            
            
            if($user->user_account_id != null || $user->user_visa_account_id != null){
                \Auth::logout();
                return redirect('/login')
                ->with(
                    'status',
                    ['success' => 0, 'msg' => __('lang_v1.login_not_allowed')]
                );
            }
                
            $session     = \App\Models\SessionTable::where("user_id",$user->id)->select()->first();
          
            if(!empty($session)){
                if($user->id == 1){
                    
                }else if(isset($request->logout_other)){
                    $session->delete();
                }else{
                    \Auth::logout();
                    return redirect('/login')
                    ->with(
                        'status',
                        ['success' => 0, 'msg' => __('lang_v1.sorry_there_is_device_active')]
                    ); 
                }
            } 
            
            $this->businessUtil->activityLog($user, 'login');
            
            if ($user->business==null) {
                \Auth::logout();
                return redirect('/login')
                ->with(
                    'status',
                    ['success' => 0, 'msg' => __('lang_v1.business_inactive')]
                );
            }

            if (!$user->business->is_active) {
                \Auth::logout();
                return redirect('/login')
                    ->with(
                        'status',
                        ['success' => 0, 'msg' => __('lang_v1.business_inactive')]
                    );
            } elseif ($user->status != 'active') {
                
                \Auth::logout();
                return redirect('/login')
                ->with(
                    'status',
                    ['success' => 0, 'msg' => __('lang_v1.user_inactive')]
                );
            } elseif (!$user->allow_login) {
                
                \Auth::logout();
                return redirect('/login')
                ->with(
                    'status',
                    ['success' => 0, 'msg' => __('lang_v1.login_not_allowed')]
                );
            } elseif (($user->user_type == 'user_customer') && !$this->moduleUtil->hasThePermissionInSubscription($user->business_id, 'crm_module')) {
                
                \Auth::logout();
                return redirect('/login')
                ->with(
                    'status',
                    ['success' => 0, 'msg' => __('lang_v1.business_dont_have_crm_subscription')]
                );
            }
        
        
        
    
        
    }

    protected function redirectTo()
    {
            $CHECK = 0;
            $user = \Auth::user();
            
            // if (!$user->can('dashboard.data') && $user->can('sell.create')) {
            //     $CHECK = 1;
            //     return '/pos/create';
            // }
            if ($user->user_type == 'user_customer') {
                $CHECK = 2;
                return 'contact/contact-dashboard';
            }
        
            $B=\DB::table('subscriptions')->where('business_id',$user->business->id)->first();
            
            if($B==NULL || $B->status !=='approved' ){
                $CHECK = 3;
                return '/subscription';
            }
        
            return '/home';
            
            
    }
}
