<?php

namespace App\Http\Controllers;

use App\Business;
use App\Currency;
use App\Account;
use App\Notifications\TestEmailNotification;
use App\System;
use App\TaxRate;
use App\Unit;
use Artisan;
use App\Models\User;
use App\Models\ExchangeRate;
use App\Utils\BusinessUtil;
use App\Utils\ModuleUtil;
use App\Utils\RestaurantUtil;
use Carbon\Carbon;
use DateTimeZone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission; 
use Illuminate\Support\Facades\Config; 

class BusinessController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | BusinessController
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new business/business as well as their
    | validation and creation.
    |
    */

    /**
     * All Utils instance.
     *
     */
    protected $businessUtil;
    protected $restaurantUtil;
    protected $moduleUtil;
    protected $mailDrivers;

    /**
     * Constructor
     *
     * @param ProductUtils $product
     * @return void
     */
    public function __construct(BusinessUtil $businessUtil, RestaurantUtil $restaurantUtil, ModuleUtil $moduleUtil)
    {
        $this->businessUtil = $businessUtil;
        $this->moduleUtil = $moduleUtil;
        
        $this->theme_colors = [
            'blue'           => 'Blue',
            'black'          => 'Black',
            'purple'         => 'Purple',
            'green'          => 'Green',
            'red'            => 'Red',
            'yellow'         => 'Yellow',
            'blue-light'     => 'Blue Light',
            'black-light'    => 'Black Light',
            'purple-light'   => 'Purple Light',
            'green-light'    => 'Green Light',
            'red-light'      => 'Red Light',
        ];

        $this->mailDrivers = [
                'smtp' => 'SMTP',
                'sendmail' => 'Sendmail',
                // 'mailgun' => 'Mailgun',
                // 'mandrill' => 'Mandrill',
                // 'ses' => 'SES',
                // 'sparkpost' => 'Sparkpost'
            ];
    }

    /**
     * Shows registration form
     *
     * @return \Illuminate\Http\Response
     */
    public function getRegister()
    {
        return redirect("/register-account");
        if (!config('constants.allow_registration')) {
            return redirect('/');
        }

        $currencies    = $this->businessUtil->allCurrencies();
        
        $timezone_list = $this->businessUtil->allTimeZones();

        $months = [];
        for ($i=1; $i<=12; $i++) {
            $months[$i] = __('business.months.' . $i);
        }

        $accounting_methods = $this->businessUtil->allAccountingMethods();
        $package_id = request()->package;

        $system_settings = System::getProperties(['superadmin_enable_register_tc', 'superadmin_register_tc'], true);
        
        return view('business.register', compact(
            'currencies',
            'timezone_list',
            'months',
            'accounting_methods',
            'package_id',
            'system_settings'
        ));
    }

    /**
     * Handles the registration of a new business and it's owner
     *
     * @return \Illuminate\Http\Response
     */
    public function postRegister(Request $request)
    {
        if (!config('constants.allow_registration')) {
            return redirect('/');
        }
    
        
        try {
            $validator = $request->validate(
                [
                'name' => 'required|max:255',
                'currency_id' => 'required|numeric',
                'country' => 'required|max:255',
                'state' => 'required|max:255',
                'city' => 'required|max:255',
                'zip_code' => 'required|max:255',
                'landmark' => 'required|max:255',
                'time_zone' => 'required|max:255',
                'surname' => 'max:10',
                'email' => 'sometimes|nullable|email|unique:users|max:255',
                'first_name' => 'required|max:255',
                'username' => 'required|min:4|max:255|unique:users',
                'password' => 'required|min:4|max:255',
                'fy_start_month' => 'required',
                'accounting_method' => 'required',
                ],
                [
                'name.required' => __('validation.required', ['attribute' => __('business.business_name')]),
                'name.currency_id' => __('validation.required', ['attribute' => __('business.currency')]),
                'country.required' => __('validation.required', ['attribute' => __('business.country')]),
                'state.required' => __('validation.required', ['attribute' => __('business.state')]),
                'city.required' => __('validation.required', ['attribute' => __('business.city')]),
                'zip_code.required' => __('validation.required', ['attribute' => __('business.zip_code')]),
                'landmark.required' => __('validation.required', ['attribute' => __('business.landmark')]),
                'time_zone.required' => __('validation.required', ['attribute' => __('business.time_zone')]),
                'email.email' => __('validation.email', ['attribute' => __('business.email')]),
                'email.email' => __('validation.unique', ['attribute' => __('business.email')]),
                'first_name.required' => __('validation.required', ['attribute' =>
                    __('business.first_name')]),
                'username.required' => __('validation.required', ['attribute' => __('business.username')]),
                'username.min' => __('validation.min', ['attribute' => __('business.username')]),
                'password.required' => __('validation.required', ['attribute' => __('business.username')]),
                'password.min' => __('validation.min', ['attribute' => __('business.username')]),
                'fy_start_month.required' => __('validation.required', ['attribute' => __('business.fy_start_month')]),
                'accounting_method.required' => __('validation.required', ['attribute' => __('business.accounting_method')]),
                ]
            );

            DB::beginTransaction();
            // Artisan::call('command:run');
            // dd($request);
            // dd(Artisan::call('command:run'));
            //Create owner.
            $owner_details = $request->only(['surname', 'first_name', 'last_name', 'username', 'email', 'password', 'language']);

            $owner_details['language'] = empty($owner_details['language']) ? config('app.locale') : $owner_details['language'];

            $user = User::create_user($owner_details);

            $business_details = $request->only(['name', 'start_date', 'currency_id', 'time_zone']);
            $business_details['fy_start_month'] = 1;

            $business_location = $request->only(['name', 'country', 'state', 'city', 'zip_code', 'landmark', 'website', 'mobile', 'alternate_number']);
            
            //Create the business
            $business_details['owner_id'] = $user->id;
            if (!empty($business_details['start_date'])) {
                $business_details['start_date'] = Carbon::createFromFormat(config('constants.default_date_format'), $business_details['start_date'])->toDateString();
            }
            
            //upload logo
            $logo_name = $this->businessUtil->uploadFile($request, 'business_logo', 'business_logos', 'image');
            if (!empty($logo_name)) {
                $business_details['logo'] = $logo_name;
            }
            
            //default enabled modules edit 2021-5-2
           /* $business_details['enabled_modules'] = ['purchases','add_sale','pos_sale','stock_transfers','stock_adjustment','expenses'];*/
            $business_details['enabled_modules'] =["purchases","add_sale","pos_sale","stock_transfers","stock_adjustment","expenses","account","tables","modifiers","service_staff","booking","kitchen","Warehouse","subscription","types_of_service","stock_tacking","warehouse","cash_and_bank","check","voucher","account","product","pattern","log_file","user_activation","mobile_section","react_section"];
            
            $business = $this->businessUtil->createNewBusiness($business_details);


            //Update user with business id
            $user->business_id = $business->id;
            $user->save();

            $this->businessUtil->newBusinessDefaultResources($business->id, $user->id);
            $new_location = $this->businessUtil->addLocation($business->id, $business_location);

            //create new permission with the new location
            Permission::create(['name' => 'location.' . $new_location->id ]);

            DB::commit();

            //Module function to be called after after business is created
            if (config('app.env') != 'demo') {
                $this->moduleUtil->getModuleData('after_business_created', ['business' => $business]);
            }

            //Process payment information if superadmin is installed & package information is present
            $is_installed_superadmin = $this->moduleUtil->isSuperadminInstalled();
            $package_id = $request->get('package_id', null);
            if ($is_installed_superadmin && !empty($package_id) && (config('app.env') != 'demo')) {
                $package = \Modules\Superadmin\Entities\Package::find($package_id);
                if (!empty($package)) {
                    Auth::login($user);
                    return redirect()->route('register-pay', ['package_id' => $package_id]);
                }
            }
            $currency_details = $request->only(["currency_id"]);
            $exchange_rate    = \App\Models\ExchangeRate::where("currency_id",$currency_details)->where("source",1)->first();
            if(!empty($exchange_rate)){
                $exchange_rate->business_id     = $business->id;
                $exchange_rate->currency_id     = $business->currency_id;
                $exchange_rate->amount          = 1;
                $exchange_rate->opposit_amount  = 1;
                $exchange_rate->date            = \Carbon::now();
                $exchange_rate->default         = 0;
                $exchange_rate->source          = 1;
                $exchange_rate->update();
            }else{
                $exc                  = new ExchangeRate;
                $exc->business_id     = $business->id;
                $exc->currency_id     = $business->currency_id;
                $exc->amount          = 1;
                $exc->opposit_amount  = 1;
                $exc->date            = \Carbon::now();
                $exc->source          = 1;
                $exc->default         = 0;
                $exc->save();
            }

            $output = ['success' => 1,
                    'msg' => __('business.business_created_succesfully')
                ];

            return redirect('login')->with('status', $output);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());

            $output = ['success' => 0,
                            'msg' => $e->getMessage()
                        ];

            return back()->with('status', $output)->withInput();
        }
    }
    
    /**
     * Handles the validation username
     *
     * @return \Illuminate\Http\Response
     */
    public function postCheckUsername(Request $request)
    {
        $username = $request->input('username');

        if (!empty($request->input('username_ext'))) {
            $username .= $request->input('username_ext');
        }
            
 
             
      
        $total = 0;
        $count = User::where('username', $username)->count();
        if ($count > 0) {
            $total = 1;
        } 

        Config::set('database.connections.mysql.database', "izocloud");
        DB::purge('mysql');
        DB::reconnect('mysql');
        $userIzo         = \App\Models\IzoUser::where('email',$username)->count();
        if ($userIzo > 0) {
            $total = 1;
        }  
        $database_name  = request()->session()->get('user_main.database');
        Config::set('database.connections.mysql.database', $database_name);
        DB::purge('mysql');
        DB::reconnect('mysql');
         
        if($total == 0){
            echo "true";
            exit;
        }else{
            echo "false";
            exit;
        }
    }
    /**
     * Handles the validation username
     *
     * @return \Illuminate\Http\Response
     */
    public function postCheckMobile(Request $request)
    {
        $mobile = $request->input('contact_number');
        $old    = $request->input('old_number');
        $edit   = $request->input('edit');
        $total = 0;
        
         
        

        $count = User::where('contact_number', $mobile)->count();
        if ($count > 0) {
            if($edit != null){
                if($old != null){
                    if ($old != $mobile ) {
                        $total = 1;
                    }
                }else{
                    $total = 1;
                }
            }else{
                $total = 1;
            }
        } 

        Config::set('database.connections.mysql.database', "izocloud");
        DB::purge('mysql');
        DB::reconnect('mysql');
        $userIzo         = \App\Models\IzoUser::where('mobile',$mobile)->count();
        if ($userIzo > 0) {
            if($edit != null){
                if($old != null){
                    if ($old != $mobile ) {
                        $total = 1;
                    }
                }else{
                    $total = 1;
                }
            }else{
                $total = 1;
            }
        }  
        $database_name  = request()->session()->get('user_main.database');
        Config::set('database.connections.mysql.database', $database_name);
        DB::purge('mysql');
        DB::reconnect('mysql');
         
        if($total == 0){
            echo "true";
            exit;
        }else{
            echo "false";
            exit;
        }
    }
    /**
     * Handles the validation username
     *
     * @return \Illuminate\Http\Response
     */
    public function postCheckPassword(Request $request)
    {
        $password = $request->input('password');
        $total = 0;
        if(strlen($password) >  6){
            if(!preg_match('/[a-z]/', $password)){
                $total = 1;
            }else if(!preg_match('/[A-Z]/', $password)){
                $total = 1;
            }else if(!preg_match('/[0-9]/', $password)){
                $total = 1;
            } 
        } else{
            $total = 1; 
        } 
         
        if($total == 0){
            echo "true";
            exit;
        }else{
            echo "false";
            exit;
        }
    }
    
    /**
     * Shows business settings form
     *
     * @return \Illuminate\Http\Response
     */
    public function getBusinessSettings()
    {
        if (!auth()->user()->can('business_settings.access')) {
            abort(403, 'Unauthorized action.');
        }

        $timezones     = DateTimeZone::listIdentifiers(DateTimeZone::ALL);
        $timezone_list = [];
        foreach ($timezones as $timezone) {
            $timezone_list[$timezone] = $timezone;
        }
        $business_id   = request()->session()->get('user.business_id');
        $product_price = \App\Models\ProductPrice::where("business_id",$business_id)->whereNull("product_id")->get();
        $business      = Business::where('id', $business_id)->first();
        $accounts      =  Account:: items();
        $accounts_type =  \App\AccountType::where("business_id",$business_id)->get();
        $account_type_ = [];
        foreach($accounts_type as $it){
                $account_type_[$it->id] = $it->name;
        }
        
        $units         = Unit::forDropdown($business_id);
        $unitsP        = Unit::forDropdownInPrice($business_id);
        $currencies    = $this->businessUtil->allCurrencies();
        $tax_details   = TaxRate::forBusinessDropdown($business_id);
        $tax_rates     = $tax_details['tax_rates'];
 
        $months = [];
        for ($i=1; $i<=12; $i++) {
            $months[$i] = __('business.months.' . $i);
        }

        $accounting_methods = [
                'fifo' => __('business.fifo'),
                'lifo' => __('business.lifo')
            ];

        $commission_agent_dropdown = [
                '' => __('lang_v1.disable'),
                'logged_in_user' => __('lang_v1.logged_in_user'),
                'user' => __('lang_v1.select_from_users_list'),
                'cmsn_agnt' => __('lang_v1.select_from_commisssion_agents_list')
            ];

        $itemMfg            =   $business->itemMfg;
        $app_store_id       =   $business->app_store_id;
        $app_pattern_id     =   $business->app_pattern_id;
        $app_account        =   $business->app_account;
        $profitMfg          =   $business->profitMfg;
        $store_mfg          =   $business->store_mfg;
        $wastageMfg         =   $business->wastageMfg;
        $source_sell_price  =   $business->source_sell_price;
        $separate_sell      =   $business->separate_sell;
        $separate_pay_sell  =   $business->separate_pay_sell;
        $default_price_unit  =  $business->default_price_unit;
        $module             =   \App\Models\PrinterTemplate::where("business_id",$business_id)->where("Form_type","Sale")->get();
        $purchaseModule     =   \App\Models\PrinterTemplate::where("business_id",$business_id)->where("Form_type","Purchase")->get();
        $excange_rates      =   \App\Models\ExchangeRate::where("business_id",$business_id)->orderBy("source","desc")->get();
        $Stores             =   [];
        $listModules        =   [];
        $listModulesPurchase=   [];
        $warehousea         = \App\Models\Warehouse::where("business_id",$business_id)->where("status",1)->get();
        
        foreach($warehousea as $it){
            $Stores[$it->id]       = $it->name;
        }
        foreach($module as $it){
            $listModules[$it->id]       = $it->name_template;
        }
        foreach($purchaseModule as $it){
            $listModulesPurchase[$it->id]       = $it->name_template;
        }
        $patterns    =   [];
        $pattern     = \App\Models\Pattern::where("business_id",$business_id)->get();
        
        foreach($pattern as $it){
            $patterns[$it->id]       = $it->name;
        }

        $units_dropdown = Unit::forDropdown($business_id, true);

        $date_formats   = Business::date_formats();

        $shortcuts      = json_decode($business->keyboard_shortcuts, true);
        
        $pos_settings   = empty($business->pos_settings) ? $this->businessUtil->defaultPosSettings() : json_decode($business->pos_settings, true);

        $email_settings = empty($business->email_settings) ? $this->businessUtil->defaultEmailSettings() : $business->email_settings;

        $sms_settings   = empty($business->sms_settings) ? $this->businessUtil->defaultSmsSettings() : $business->sms_settings;

        $modules = $this->moduleUtil->availableModules();

        $theme_colors = $this->theme_colors;

        $mail_drivers = $this->mailDrivers;

        $allow_superadmin_email_settings = System::getProperty('allow_email_settings_to_businesses');

        $custom_labels = !empty($business->custom_labels) ? json_decode($business->custom_labels, true) : [];

        $common_settings = !empty($business->common_settings) ? $business->common_settings : [];

        $weighing_scale_setting = !empty($business->weighing_scale_setting) ? $business->weighing_scale_setting : [];

        return view('business.settings', compact('business','default_price_unit','listModules','listModulesPurchase','units','unitsP','separate_sell','separate_pay_sell','product_price','source_sell_price','patterns','excange_rates','app_account','app_store_id','app_pattern_id','Stores' , 'store_mfg' ,'account_type_', 'accounts', 'itemMfg', 'profitMfg', 'wastageMfg', 'currencies', 'tax_rates', 'timezone_list', 'months', 'accounting_methods', 'commission_agent_dropdown', 'units_dropdown', 'date_formats', 'shortcuts', 'pos_settings', 'modules', 'theme_colors', 'email_settings', 'sms_settings', 'mail_drivers', 'allow_superadmin_email_settings', 'custom_labels', 'common_settings', 'weighing_scale_setting'));
    }

    /**
     * Updates business settings
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function postBusinessSettings(Request $request)
    {
        if (!auth()->user()->can('business_settings.access')) {
            abort(403, 'Unauthorized action.');
        }
        
        try {
            $old_business_info  = Business::find($request->id );  
            $notAllowed = $this->businessUtil->notAllowedInDemo();
            if (!empty($notAllowed)) {
                return $notAllowed;
            }
            /**
             * 
             *   'stop_selling_before', 'default_unit', 'expiry_type', 'date_format','separate_sell','separate_pay_sell',
             *   'time_format', 'ref_no_prefixes', 'theme_color', 'email_settings','app_store_id','app_pattern_id','app_account',
             *   'sms_settings', 'rp_name', 'amount_for_unit_rp','bank','cash','store_mfg',
             *   'itemMfg', 'profitMfg', 'account_assets','account_liability', 
             *   'min_order_total_for_rp', 'max_rp_per_order','currency_price','name_add','unit_idd','return_purchase_print_module','purchase_print_module','return_sale_print_module',
             *   'redeem_amount_per_unit_rp', 'min_order_total_for_redeem','source_sell_price',
             *   'min_redeem_point', 'max_redeem_point', 'rp_expiry_period','sale_print_module','quotation_print_module','approve_quotation_print_module','draft_print_module',
             */
            $business_details = $request->only(['name', 'start_date', 'currency_id', 'tax_label_1', 'tax_number_1', 'tax_label_2', 'tax_number_2', 'default_profit_percent', 'default_sales_tax', 'default_sales_discount', 'sell_price_tax', 'sku_prefix', 'time_zone', 'fy_start_month', 'accounting_method', 'transaction_edit_days', 'sales_cmsn_agnt', 'item_addition_method', 'currency_symbol_placement', 'on_product_expiry',
                'stop_selling_before', 'default_unit', 'expiry_type', 'date_format','customer_type_id','supplier_type_id',
                'time_format', 'ref_no_prefixes', 'theme_color', 'email_settings','app_store_id','app_pattern_id','app_account',
                'sms_settings', 'rp_name', 'amount_for_unit_rp','bank','cash','store_mfg','separate_sell','separate_pay_sell',
                'itemMfg', 'profitMfg', 'account_assets','account_liability','source_sell_price', 
                'min_order_total_for_rp', 'max_rp_per_order','currency_price','name_add','unit_idd','return_purchase_print_module','purchase_print_module','return_sale_print_module',
                'redeem_amount_per_unit_rp', 'min_order_total_for_redeem','source_sell_price','sale_print_module','quotation_print_module','approve_quotation_print_module','draft_print_module',
                'min_redeem_point', 'max_redeem_point', 'rp_expiry_period',
                'rp_expiry_type', 'custom_labels', 'weighing_scale_setting']);

            if (!empty($request->input('enable_rp')) &&  $request->input('enable_rp') == 1) {
                $business_details['enable_rp'] = 1;
            } else {
                $business_details['enable_rp'] = 0;
            }

            
           
            $business_details['sale_print_module']  = (isset($business_details['sale_print_module']))?json_encode($business_details['sale_print_module']):"[]";
            $business_details['quotation_print_module']  =  (isset($business_details['quotation_print_module']))?json_encode($business_details['quotation_print_module']):"[]";
            $business_details['approve_quotation_print_module']  =  (isset($business_details['approve_quotation_print_module']))?json_encode($business_details['approve_quotation_print_module']):"[]";
            $business_details['draft_print_module']  =  (isset($business_details['draft_print_module']))?json_encode($business_details['draft_print_module']):"[]";
            $business_details['return_sale_print_module']  =  (isset($business_details['return_sale_print_module']))?json_encode($business_details['return_sale_print_module']):"[]";
            $business_details['purchase_print_module']  =  (isset($business_details['purchase_print_module']))?json_encode($business_details['purchase_print_module']):"[]";
            $business_details['return_purchase_print_module']  =  (isset($business_details['return_purchase_print_module']))?json_encode($business_details['return_purchase_print_module']):"[]";

            $business_details['amount_for_unit_rp'] = !empty($business_details['amount_for_unit_rp']) ? $this->businessUtil->num_uf($business_details['amount_for_unit_rp']) : 1;
            $business_details['min_order_total_for_rp'] = !empty($business_details['min_order_total_for_rp']) ? $this->businessUtil->num_uf($business_details['min_order_total_for_rp']) : 1;
            $business_details['redeem_amount_per_unit_rp'] = !empty($business_details['redeem_amount_per_unit_rp']) ? $this->businessUtil->num_uf($business_details['redeem_amount_per_unit_rp']) : 1;
            $business_details['min_order_total_for_redeem'] = !empty($business_details['min_order_total_for_redeem']) ? $this->businessUtil->num_uf($business_details['min_order_total_for_redeem']) : 1;

            $business_details['default_profit_percent'] = !empty($business_details['default_profit_percent']) ? $this->businessUtil->num_uf($business_details['default_profit_percent']) : 0;

            $business_details['default_sales_discount'] = !empty($business_details['default_sales_discount']) ? $this->businessUtil->num_uf($business_details['default_sales_discount']) : 0;

            if (!empty($business_details['start_date'])) {
                $business_details['start_date'] = $this->businessUtil->uf_date($business_details['start_date']);
            }

            if (!empty($request->input('enable_tooltip')) &&  $request->input('enable_tooltip') == 1) {
                $business_details['enable_tooltip'] = 1;
            } else {
                $business_details['enable_tooltip'] = 0;
            }

            $business_details['enable_product_expiry'] = !empty($request->input('enable_product_expiry')) &&  $request->input('enable_product_expiry') == 1 ? 1 : 0;
            if ($business_details['on_product_expiry'] == 'keep_selling') {
                $business_details['stop_selling_before'] = null;
            }

            $business_details['stock_expiry_alert_days'] = !empty($request->input('stock_expiry_alert_days')) ? $request->input('stock_expiry_alert_days') : 30;

            //Check for Purchase currency
            if (!empty($request->input('purchase_in_diff_currency')) &&  $request->input('purchase_in_diff_currency') == 1) {
                $business_details['purchase_in_diff_currency'] = 1;
                $business_details['purchase_currency_id'] = $request->input('purchase_currency_id');
                $business_details['p_exchange_rate'] = $request->input('p_exchange_rate');
            } else {
                $business_details['purchase_in_diff_currency'] = 0;
                $business_details['purchase_currency_id'] = null;
                $business_details['p_exchange_rate'] = 1;
            }

            //upload logo
            $logo_name = $this->businessUtil->uploadFile($request, 'business_logo', 'business_logos', 'image');
            if (!empty($logo_name)) {
                $business_details['logo'] = $logo_name;
            }

            $checkboxes = ['enable_editing_product_from_purchase',
                'enable_inline_tax','wastageMfg',
                'enable_brand', 'enable_category', 'enable_sub_category', 'enable_price_tax', 'enable_purchase_status',
                'enable_lot_number', 'enable_racks', 'enable_row', 'default_price_unit','enable_position', 'enable_sub_units'];
            foreach ($checkboxes as $key => $value) {
                $business_details[$value] = !empty($request->input($value)) &&  $request->input($value) == 1 ? 1 : 0;
            }
            


            

            $business_id = request()->session()->get('user.business_id');
            $business = Business::where('id', $business_id)->first();

            if(count($request->only('enable_product_prices')) > 0){
            
                $name_add          =  $request->input('name_add');
                $currency_price    =  $request->input('currency_price');
                $unit_idd          =  $request->input('unit_idd');
                $allProducts       =  \App\Product::get();
                foreach($allProducts as $item){
                    foreach($currency_price as $key => $pprice){
              
                        $val = 0;
                        $product_id  =  \App\Models\ProductPrice::where("product_id",$item->id)
                                                                ->whereNull("default_name")
                                                                ->where("number_of_default",$val)
                                                                ->where("unit_id",$unit_idd[$key])
                                                                ->first();
                        if(empty($product_id)){
                            $product_id_ptice                     =  new \App\Models\ProductPrice();
                            $product_id_ptice->product_id         =  $item->id ;   
                            $product_id_ptice->business_id        =  $business_id ;   
                            $product_id_ptice->name               =  "Default Price" ;   
                            $product_id_ptice->default_sell_price =  0.0  ;   
                            $product_id_ptice->number_of_default  =  $val ;     
                            $product_id_ptice->unit_id            =  $unit_idd[$key] ;
                            $product_id_ptice->save();
                        }
                        foreach( $name_add as $km => $i ){
                            switch ($km) {
                                case 0:
                                    $val = 1;
                                   
                                    break;
                                case 1:
                                    $val = 2;
                                    
                                    break;
                                case 2:
                                    $val = 3;
                                    
                                    break;
                                case 3:
                                    $val = 4;
                                     
                                    break;
                                case 4:
                                    $val = 5;
                                    
                                    break;
                                case 5:
                                    $val = 6;
                                     
                                    break;
                                case 6:
                                    $val = 7;
                                  
                                    break;
                                case 7:
                                    $val = 8;
                                   
                                    break;
                                case 8:
                                    $val = 9;
                                    
                                    break;
                                default:
                                    
                                    $val = null;
                                
    
                            }
                            $product_id  =  \App\Models\ProductPrice::where("product_id",$item->id)
                                                                    ->whereNull("default_name")
                                                                    ->where("number_of_default",$val)
                                                                    ->where("unit_id",$unit_idd[$key])
                                                                    ->first();
                            if(empty($product_id)){
                                $product_id_ptice                      =  new \App\Models\ProductPrice();
                                $product_id_ptice->business_id         =  $business_id ;   
                                $product_id_ptice->product_id          =  $item->id ;   
                                $product_id_ptice->name                =  $i ;   
                                $product_id_ptice->default_sell_price  =  $pprice[$km]  ;   
                                $product_id_ptice->number_of_default   =  $val ;     
                                $product_id_ptice->unit_id             =  $unit_idd[$key] ;
                                $product_id_ptice->save();
                            }else{
                                 $product_id->business_id         =  $business_id ;   
                                 $product_id->product_id          =  $item->id ;   
                                 $product_id->name                =  $i  ;   
                                 $product_id->default_sell_price  =  $pprice[$km]  ;   
                                 $product_id->update();
                                 
                            }
    
                            $global_product =  \App\Models\ProductPrice::where("default_name",1)
                                                                            ->where("number_of_default",$val)
                                                                            ->first();
                            $global_product->name               =  $i  ;   
                            $global_product->update();
    
                        }
                    }
                }
            }

            //Update business settings
            if (!empty($business_details['logo'])) {
                $business->logo = $business_details['logo'];
            } else {
                unset($business_details['logo']);
            }
            
           
            if($request->cur_line){
                $line_delete = [];
                foreach($request->cur_line AS $it){
                    $line_delete[] = $it ;
                }
                $exchange_rate = \App\Models\ExchangeRate::whereNotIn("id",$line_delete)->get();
                foreach($exchange_rate as $i){
                        $i->delete();
                }
            }
            $defaul_id = null ;
           
            if($request->cur_default_old){
                foreach($request->cur_default_old as $key => $i){
                    $exc_rate = \App\Models\ExchangeRate::where("currency_id","!=",$key)->get();
                    foreach($exc_rate  as $ii){
                            $ii->default = 0;
                            $ii->update();
                    }
                }
                $exch_rate = \App\Models\ExchangeRate::where("currency_id",$key)->first();
                if($exch_rate){
                    $exch_rate->default = 1 ;
                    $exch_rate->update();
                }
            }
           

            
            if($request->currency_name_old){
                $list_of_currency = [];
                foreach($request->currency_name_old as $key => $curr_id){
                    $list_of_currency[]       = $curr_id;
                    $exc                      = ExchangeRate::where("currency_id",$curr_id)->first();
                    if(!empty($exc)){
                        $exc->currency_id     = $request->currency_name_old[$key];
                        $exc->amount          = $request->currency_amount_old[$key];
                        $exc->opposit_amount  = $request->currency_opposit_amount_old[$key];
                        $exc->date            = $it;
                        $exc->update();
                    }else{
                        $exc                  = new ExchangeRate;
                        $exc->business_id     = $business->id;
                        $exc->currency_id     = $request->currency_name_old[$key];
                        $exc->amount          = $request->currency_amount_old[$key];
                        $exc->opposit_amount  = $request->currency_opposit_amount_old[$key];
                        $exc->date            = \Carbon::now();
                        $exc->source          = 0;
                        $exc->default         = 0;
                        $exc->save();
                    }
                }
                $ForDelete                    = ExchangeRate::whereNotIn("currency_id",$list_of_currency)->get();
                foreach($ForDelete as $oneDelete){
                    $oneDelete->delete();
                }
                
            }
            if($request->currency_date){
               
                foreach($request->currency_date as $key => $it){
                    $array_id[$key]       = $request->currency_name[$key]; 
                    $exc                  = new ExchangeRate;
                    $exc->business_id     = $business->id;
                    $exc->currency_id     = $request->currency_name[$key];
                    $exc->amount          = $request->currency_amount[$key];
                    $exc->opposit_amount  = $request->currency_opposit_amount[$key];
                    $exc->date            = $it;
                    $exc->source          = 0;
                    $exc->default         = 0;
                    $exc->save();
                }
            }
            if($request->cur_default){
                 
                foreach($request->cur_default as $key => $i){
                    $exc_rate = \App\Models\ExchangeRate::where("currency_id","!=",$key)->get();
                    foreach($exc_rate  as $ii){
                            $ii->default = 0;
                            $ii->update();
                    }
                }
                $exch_rate = \App\Models\ExchangeRate::where("currency_id",$key)->first();
                if($exch_rate){
                    $exch_rate->default = 1 ;
                    $exch_rate->update();
                }
            }
            // #2024-8-6
            if($request->currency_date_old){
                foreach($request->currency_date_old as $key => $it){
                    $array_id[$key]       = $request->currency_name_old[$key]; 
                    $exc                  = ExchangeRate::find($request->cur_line[$key]);  
                    $exc->currency_id     = $request->currency_name_old[$key];
                    $exc->amount          = $request->currency_amount_old[$key];
                    $exc->opposit_amount  = $request->currency_opposit_amount_old[$key];
                    $exc->date            = $it;
                    $exc->update();
                }
            }
           
            //System settings
            $shortcuts = $request->input('shortcuts');
            $business_details['keyboard_shortcuts'] = json_encode($shortcuts);

            //pos_settings
            $pos_settings = $request->input('pos_settings');
            $default_pos_settings = $this->businessUtil->defaultPosSettings();
            foreach ($default_pos_settings as $key => $value) {
                if (!isset($pos_settings[$key])) {
                    $pos_settings[$key] = $value;
                }
            }
            $business_details['pos_settings']  = json_encode($pos_settings);

            $business_details['custom_labels'] = json_encode($business_details['custom_labels']);

            $business_details['common_settings'] = !empty($request->input('common_settings')) ? $request->input('common_settings') : [];
            $business_details['assets'] = $request->input('account_assets');
            $business_details['liability'] = $request->input('account_liability');
            $business_details['separate_sell'] = $request->input('separate_sell');
            $business_details['separate_pay_sell'] = $request->input('separate_pay_sell');
            //Enabled modules
            $enabled_modules = $request->input('enabled_modules');
            $business_details['enabled_modules'] = !empty($enabled_modules) ? $enabled_modules : null;
             
            $business->fill($business_details);
            $business->save();

            //update session data
            $request->session()->put('business', $business);

            //Update Currency details
            $currency = Currency::find($business->currency_id);
            $request->session()->put('currency', [
                        'id' => $currency->id,
                        'code' => $currency->code,
                        'symbol' => $currency->symbol,
                        'thousand_separator' => $currency->thousand_separator,
                        'decimal_separator' => $currency->decimal_separator,
                        ]);
            
            //update current financial year to session
            $financial_year = $this->businessUtil->getCurrentFinancialYear($business->id);
            $request->session()->put('financial_year', $financial_year);
            $currency_details = $request->only(["currency_id"]);
            $exchange_rate    = \App\Models\ExchangeRate::where("source",1)->first();
            if(!empty($exchange_rate)){
                $exchange_rate->business_id     = $business->id;
                $exchange_rate->currency_id     = $business->currency_id;
                $exchange_rate->amount          = 1;
                $exchange_rate->opposit_amount  = 1;
                $exchange_rate->date            = \Carbon::now();
                $exchange_rate->default         = 0;
                $exchange_rate->source          = 1;
                $exchange_rate->update();
            }else{
                $exc                  = new ExchangeRate;
                $exc->business_id     = $business->id;
                $exc->currency_id     = $business->currency_id;
                $exc->amount          = 1;
                $exc->opposit_amount  = 1;
                $exc->date            = \Carbon::now();
                $exc->source          = 1;
                $exc->default         = 0;
                $exc->save();
            }
            $output = ['success' => 1,
                            'msg' => __('business.settings_updated_success')
                        ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            $output = ['success' => 0,
                            // 'msg' => $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage()
                            'msg' => __('messages.something_went_wrong')
                        ];
        }
        return redirect('business/settings')->with('status', $output);
    }

    /**
     * Handles the validation email
     *
     * @return \Illuminate\Http\Response
     */
    public function postCheckEmail(Request $request)
    {
        $email = $request->input('email');

        $query = User::where('email', $email);

        if (!empty($request->input('user_id'))) {
            $user_id = $request->input('user_id');
            $query->where('id', '!=', $user_id);
        }

        $exists = $query->exists();
        if (!$exists) {
            echo "true";
            exit;
        } else {
            echo "false";
            exit;
        }
    }


    public function symbol($id)
    {
        if(request()->ajax()){
            $currency = \App\Currency::where("id",$id)->first();
            if(!empty($currency)){
                $symbol = $currency->symbol;
            }else{
                $symbol = "";
            }
            return $symbol;
        }
    }

    public function symbol_amount($id)
    {
       
        if(request()->ajax()){
            $currency = \App\Models\ExchangeRate::where("currency_id",$id)->first();
     
            if(!empty($currency)){
                if($currency->right_amount == 0){
                    $amount = $currency->amount;
                }else{
                    $amount = $currency->opposit_amount;
                }
                $symbol = $currency->currency->symbol;
              
            }else{
                $symbol = "";
            }
            $array = [];
            $array["amount"] = $amount;
            $array["symbol"] = $symbol;
            
            return $array;
        }
    }
    public function symbolLeftAmount($id)
    {
        if(request()->ajax()){
            try{
                $currency = \App\Models\ExchangeRate::where("currency_id",$id)->first();
     
                if(!empty($currency)){
                     $currency->right_amount = 0;
                     $currency->update();
                }
                $output = [
                            "success" => true,
                            "msg"    => __('Successfully Update')
                    ];
            }catch(Exception $e){
                $output = [
                    "success" => false,
                    "msg"    => __('Failed')
                ];
            }
           
            
            return $output;
        }
    }
    public function symbolRightAmount($id)
    {
       
        if(request()->ajax()){
           
            try{
                $currency = \App\Models\ExchangeRate::where("currency_id",$id)->first();
                if(!empty($currency)){
                     $currency->right_amount = 1;
                     $currency->update();
                }

                $output = [
                            "success" => true,
                            "msg"    => __('Successfully Update')
                    ];
            }catch(Exception $e){
                $output = [
                    "success" => false,
                    "msg"    => __('Failed')
                ];
            }
           
            
            return $output;
        }
    }

    public function getEcomSettings()
    {
        try {
            $api_token = request()->header('API-TOKEN');
            $api_settings = $this->moduleUtil->getApiSettings($api_token);

            $settings = Business::where('id', $api_settings->business_id)
                        ->value('ecom_settings');

            $settings_array = !empty($settings) ? json_decode($settings, true) : [];

            if (!empty($settings_array['slides'])) {
                foreach ($settings_array['slides'] as $key => $value) {
                    $settings_array['slides'][$key]['image_url'] = !empty($value['image']) ? url('/uploads/img/' . $value['image']) : '';
                }
            }
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            return $this->respondWentWrong($e);
        }

        return $this->respond($settings_array);
    }

    /**
     * Handles the testing of email configuration
     *
     * @return \Illuminate\Http\Response
     */
    public function testEmailConfiguration(Request $request)
    {
        try {
            $email_settings = $request->input();

            $data['email_settings'] = $email_settings;
            \Notification::route('mail', $email_settings['mail_from_address'])
            ->notify(new TestEmailNotification($data));

            $output = [
                'success' => 1,
                'msg' => __('lang_v1.email_tested_successfully')
            ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            $output = [
                'success' => 0,
                'msg' => $e->getMessage()
            ];
        }

        return $output;
    }

    /**
     * Handles the testing of sms configuration
     *
     * @return \Illuminate\Http\Response
     */
    public function testSmsConfiguration(Request $request)
    {
        try {
            $sms_settings = $request->input();
            
            $data = [
                'sms_settings' => $sms_settings,
                'mobile_number' => $sms_settings['test_number'],
                'sms_body' => 'This is a test SMS',
            ];
            if (!empty($sms_settings['test_number'])) {
                $response = $this->businessUtil->sendSms($data);
            } else {
                $response = __('lang_v1.test_number_is_required');
            }

            $output = [
                'success' => 1,
                'msg' => $response
            ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            $output = [
                'success' => 0,
                'msg' => $e->getMessage()
            ];
        }

        return $output;
    }
}
