<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\IzoUser;
use App\Utils\BusinessUtil;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Config; 
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Anhskohbo\NoCaptcha\Facades\NoCaptcha;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail; 
use App\Utils\ModuleUtil;
use App\InvoiceLayout;
use App\InvoiceScheme;
use Artisan;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\MailerInterface;
use Illuminate\Support\Facades\Cookie;
use App\Mail\TitanEmailExample; 
use Illuminate\Support\Str;
use GuzzleHttp\Client;
use Jenssegers\Agent\Agent;
use App\Rules\Captcha;
use Illuminate\Support\Facades\Http;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use Twilio\Jwt\JWT; 
use App\Models\SupportActivate; 
use Spatie\Permission\Models\Role;
use App\Models\Pattern;
use App\Models\Warehouse;
use App\Models\SystemAccount;


require '../vendor/autoload.php';
require_once  '../vendor/autoload.php';

class IzoUserController extends Controller
{

    use AuthenticatesUsers;

    protected $businessUtil;
    protected $moduleUtil;
 
    
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
        $this->moduleUtil   = $moduleUtil;
    }

    


    /**
     * login.
     *
     * @return \Illuminate\Http\Response
     */
    public function ChooseCompany(Request $request)
    {
        //
        #.....................................every time from the main
        Config::set('database.connections.mysql.database', "izocloud");
        DB::purge('mysql');
        DB::reconnect('mysql');
        #....................................
        $email                         = null;
        $password                      = null;
        $list_domains                  = [];
        $list_dom                      = [];
        $list_database                  = [];
        $list_domain  = IzoUser::select('domain_name',"database_name","domain_url" )->get(); 
        foreach($list_domain as $li){
            if($li != null){
                $list_dom[]      = $li->domain_name;
                $list_domains[]  = $li->domain_url;
                $list_database[] = $li->database_name;
            }
        } 
         
        #................................................
        return view('izo_user.choose_company')->with(compact(['list_domains','list_database','list_dom']));
    }
    /**
     * login.
     *
     * @return \Illuminate\Http\Response
     */
    public function loginPageRedirect(Request $request,$id)
    {
        //
        #.....................................every time from the main
        Config::set('database.connections.mysql.database', "izocloud");
        DB::purge('mysql');
        DB::reconnect('mysql');
        #....................................
        $email                         = null;
        $password                      = null;
        $list_domains                  = [];
        $list_domain  = IzoUser::pluck("domain_url"); 
        foreach($list_domain as $li){
            if($li != null){
                $list_domains[] = $li;
            }
        } 
        $administrator = null;$database =null;$domain_url =null;$domain =null;
        $list = [];
        #................................................
        if($id){ 
            $id = Crypt::decryptString($id);
            // Replace underscores with ampersands
            $queryString = str_replace('_##', '&', $id);
            // Parse the query string into an associative array
            parse_str($queryString, $output);   
            $administrator       = (isset($output['administrator']))?$output['administrator']:null;
             
            if($administrator != null){
                $database       = (isset($output['database']))?$output['database']:null;
                $domain_url     = (isset($output['domain_url']))?$output['domain_url']:null;
                $domain         = (isset($output['domain']))?$output['domain']:null;
                session()->put('user_main.database',$database);
                session()->put('user_main.database_user',$database);
                session()->put('user_main.domain_url',$domain_url);
                session()->put('user_main.domain',$domain);
                $list['database']      = $database;
                $list['database_user'] = $database;
                $list['domain_url']    = $domain_url;
                $list['domain']        = $domain;
            }
            
            $redirect    = (isset($output['redirect']))?$output['redirect']:null;
            $email       = (isset($output['email']))?$output['email']:null;
            $password    = (isset($output['password']))?$output['password']:null;
            $logoutOther = (isset($output['logoutOther']))?$output['logoutOther']:null;
           
        }   
        #................................................ 
        if(session()->has('user_main')){
            if(request()->session()->get('startLogin')){
                // return redirect('/login');
            }
            if($administrator != null){
                return view('izo_user.login')->with(compact(['list_domains','email','password','logoutOther','list','redirect']));
            }
            return redirect('/panel-account');
        }
        
        #................................................
        return view('izo_user.login')->with(compact(['list_domains','email','password','logoutOther','redirect']));
    }
    /**
     * login.
     *
     * @return \Illuminate\Http\Response
     */
    public function loginPage(Request $request)
    {
        //
        $logoutBack = session()->get('log_out_back');
        
        $_url       = request()->root();
        $parsedUrl = parse_url($_url);
        $host      = $parsedUrl['host'] ?? '';  
        $hostParts = explode('.', $host); 
        if (count($hostParts) == 3) {
            // Remove the last two parts (domain and TLD)
            array_pop($hostParts); // TLD
            array_pop($hostParts); // Domain

            // The remaining parts are the subdomain
            $subdomain = implode('.', $hostParts);
        } else if(count($hostParts) == 3){
            // Remove the last two parts (domain and TLD)
            array_pop($hostParts); // TLD

            // The remaining parts are the subdomain
            $subdomain = implode('.', $hostParts);
        } else {
            // No subdomain
            $subdomain = '';

        }
        if($subdomain == ''){
            session()->forget('user_main');
            session()->forget('password');
            session()->forget('startLogin');
            session()->forget('change_lang');
            session()->forget('login_info');
            session()->forget('adminLogin');
            session()->forget('secret');
            session()->forget('create_session');
            session()->forget('user');
            session()->forget('business');
            session()->forget('currency');
            session()->forget('locale');
            session()->forget('financial_year');
        }
        $url        = request()->session()->get('url.intended');
        // dd(session()->all());
        \Config::set('session.driver','database');
        // foreach( $sessionData as $key => $value ){
        //     session()->put($key,$value);
        // }
        
        if(!$request->session()->get('user')){
            if ($request->session()->get('startLogin')) {
                // return redirect('/panel-account');
            }
        }
        
        #.....................................every time from the main
        Config::set('database.connections.mysql.database', "izocloud");
        DB::purge('mysql');
        DB::reconnect('mysql');
        #....................................
        $email                         = null;
        $password                      = null;
        $logoutOther                   = null;
        $list_domains                  = [];
        $list_domain  = IzoUser::pluck("domain_url"); 
        foreach($list_domain as $li){
            if($li != null){
                $list_domains[] = $li;
            }
        } 
        #................................................
        
        
        if($url != null){$parsed_url               = parse_url($url);}else{$parsed_url = [];}
        if(isset($parsed_url['query'])){
            parse_str($parsed_url['query'], $query_params);
            if(isset($query_params['email'])){
                $email       = $query_params['email'];
                $password    = $query_params['password'];
                $logoutOther = $query_params['logoutOther'];
            }
        } 
      
        
        #................................................
        if(session()->has('user_main')){
            if(request()->session()->get('startLogin')){
                // return redirect('/login');
            } 
            return redirect('/panel-account');
        }
        
        #................................................
        return view('izo_user.login')->with(compact(['list_domains','email','password','logoutOther','logoutBack']));
    }
    /**
     * forget password.
     *
     * @return \Illuminate\Http\Response
     */
    public function forgetPassword(Request $request)
    {
        //
 
        if(!$request->session()->get('user')){
            if ($request->session()->get('startLogin')) {
                // return redirect('/panel-account');
            }
        }
         
        #.....................................every time from the main
        Config::set('database.connections.mysql.database', "izocloud");
        DB::purge('mysql');
        DB::reconnect('mysql');
        #....................................
        $email                         = null;
        $password                      = null;
        $list_domains                  = [];
        $list_domain  = IzoUser::pluck("domain_url"); 
        foreach($list_domain as $li){
            if($li != null){
                $list_domains[] = $li;
            }
        } 
        #................................................
        
        // $url                           = request()->session()->get('url.intended');
        // $parsed_url                    = parse_url($url);
        // if(isset($parsed_url['query'])){
        //     parse_str($parsed_url['query'], $query_params);
        //     if(isset($query_params['email'])){
        //         $email    = $query_params['email'];
        //         $password = $query_params['password'];
        //     }
        // } 
       
        #................................................
        if(session()->has('user_main')){
            if(request()->session()->get('startLogin')){
                // return redirect('/login');
            }
            return redirect('/panel-account');
        }
        #................................................
        return view('izo_user.forget_password')->with(compact(['list_domains']));
    }
    
    /**
     * Change authentication from email to username
     *
     * @return void
     */
    public function username()
    {
        return 'email';
    }
    
    /**
     * register.
     *
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    { 
        //
        #.....................................every time from the main
        Config::set('database.connections.mysql.database', "izocloud");
        DB::purge('mysql');
        DB::reconnect('mysql');
        #.................................... 
        $list_domains                  = [];
        $list_domain  = IzoUser::pluck("domain_url"); 
        foreach($list_domain as $li){
            if($li != null){
                $list_domains[] = $li;
            }
        } 
        #................................................
        return view('izo_user.register')->with(compact(['list_domains']));
    }

    /**
     * company register.
     *
     * @return \Illuminate\Http\Response
     */
    public function createCompany(Request $request)
    {
        //
        try{
            $businessUtil  = new BusinessUtil();
            DB::beginTransaction();
            // DD(request()->session()->get('user_main'));
            $owner_details = $request->only(['first_name','second_name','language','currency']);
            Config::set('database.connections.mysql.database', "izocloud");
            DB::purge('mysql');
            DB::reconnect('mysql');
            $izoCustomer   = IzoUser::where("email",request()->session()->get('user_main.email'))->first(); 
            $databaseName  = request()->session()->get('user_main.database') ;  
            Config::set('database.connections.mysql.database', $databaseName);
            DB::purge('mysql');
            DB::reconnect('mysql');
            #.......................................Create User Main
            $izo_details['currency']      = $request->currency;
            $izo_details['first_name']    = "IZO";
            $izo_details['last_name']     = "SUPPORT";
            $izo_details['surname']       = ""; 
            $izo_details['contact_number']= "0";
            $izo_details['username']      = "IZO";
            $izo_details['email']         = "info@izo.ae";
            $izo_details['password']      = Hash::make("123456@#$123");
            $izo_details['language']      = empty($owner_details['language']) ? config('app.locale') : $owner_details['language'];
            // dd($owner_details);
            $izo_user                     = User::create_user($izo_details,true);
           
            
            #.......................................Create User
            $owner_details['last_name']     = $owner_details['second_name'];
            $owner_details['surname']       = ""; 
            $owner_details['contact_number']= request()->session()->get('user_main.mobile');
            $owner_details['username']      = request()->session()->get('user_main.email');
            $owner_details['email']         = request()->session()->get('user_main.email');
            $owner_details['password']      = $izoCustomer->password;
            $owner_details['language']      = empty($owner_details['language']) ? config('app.locale') : $owner_details['language'];
            // dd($owner_details);
            $user                           = User::create_user($owner_details,true);
            #.......................................Create Business
            $business_details = $request->only(['name', 'start_date', 'currency_id', 'time_zone']);
            $business_details['name']           = request()->session()->get('user_main.domain');
            $business_details['fy_start_month'] = 1;
            $business_details['owner_id']       = $user->id;
            $business_details['currency_id']    = $owner_details['currency'];
            if (!empty($business_details['start_date'])) {
                $business_details['start_date'] = \Carbon::createFromFormat(config('constants.default_date_format'), $business_details['start_date'])->toDateString();
            }
            #.......................................Create BusinessLocation
            $info                                    = $request->only(['address', 'city',  'zip_code' , 'tax_number']);
            $business_location                       = $request->only(['name', 'country',  'zip_code']);
            $business_location['name']               = request()->session()->get('user_main.domain');
            $business_location['landmark']           = "";
            $business_location['website']            = "";
            $business_location['mobile']             = "";
            $business_location['state']              = "";
            $business_location['city']               = "";  
            $business_location['alternate_number']   = "";
            $business_details['enabled_modules']     = ["purchases","add_sale","pos_sale","stock_transfers","stock_adjustment","expenses","account","tables","modifiers","service_staff","booking","kitchen","Warehouse","subscription","types_of_service"];
            
            $business = $businessUtil->createNewBusiness($business_details);
           
            #.......................................Update user with business id
            $user->username      = request()->session()->get('user_main.email');
            $user->contact_number= $owner_details['contact_number'];
            $user->first_name    = $owner_details['first_name'];
            $city                = ($info['city'])?  " , " . $info['city']:"";
            $user->address       = $info['address']  . $city   ;
            $user->last_name     = $owner_details['second_name'];
            $user->business_id   = $business->id;
            $user->izo_user_id   = $izoCustomer->id;
            $user->is_admin_izo  = 1;
            $user->update();
            #.......................................Update izo user with business id
            $izo_user->username      = "IZO";
            $izo_user->contact_number= "0";
            $izo_user->first_name    = "IZO";
            $izo_user->address       = "DUBAI";
            $izo_user->last_name     = "SUPPORT";
            $izo_user->business_id   = $business->id;
            $izo_user->is_admin_izo  = 1;
            $izo_user->update();
            #.......................................Create Location
            $businessUtil->newBusinessDefaultResources($business->id, $user->id,$izo_user->id);
            $new_location = $businessUtil->addLocation($business->id, $business_location);
            # .......................................create new permission with the new location
            Permission::create(['name' => 'location.' . $new_location->id ]);
            $id_invoice        = InvoiceLayout::where("business_id",$business->id)->first();
            $id_invoice_schema = InvoiceScheme::where("business_id",$business->id)->first();
            Pattern::create(
                [
                    "code"          => "0001",
                    "business_id"   => $business->id,
                    "location_id"   => $new_location->id,
                    "name"          => "Default",
                    "invoice_scheme"=> $id_invoice->id,
                    "invoice_layout"=> $id_invoice_schema->id,
                    "pos"           => "Default",
                    "user_id"       => $user->id,
                    "default_p"     => 1 
                ],$business->id,$user->id
            );
            $pattern                    =  Pattern::first();
            $business->cash             =  15; 
            $business->bank             =  16; 
            $business->assets           =  1; 
            $business->liability        =  18; 
            $business->supplier_type_id =  23; 
            $business->customer_type_id =  8; 
            $business->update();
            $user->pattern_id           = '["'.$pattern->id.'"]';
            $user->update();
            SystemAccount::create(
                [ 
                    "business_id"        => $business->id,
                    "pattern_id"         => $pattern->id,
                    "purchase"           => 7,
                    "purchase_tax"       => 16,
                    "sale"               => 11,
                    "sale_tax"           => 17,
                    "cheque_debit"       => 4,
                    "cheque_collection"  => 2, 
                    "journal_expense_tax"=> 15,
                    "sale_return"        => 12, 
                    "sale_discount"      => 13, 
                    "purchase_return"    => 8, 
                    "purchase_discount"  => 9    
                ]
            );
            Warehouse::create(
                [
                    "name"          => "Main",
                    "mainStore"     => "",
                    "business_id"   => $business->id,
                    "status"        => 0,
                    "description"   => "Main",
                    "parent_id"     => null, 
                ]
            );
            $warehouse = Warehouse::first();          
            Warehouse::create(
                [
                    "name"          => "Main Store",
                    "mainStore"     => "",
                    "business_id"   => $business->id,
                    "status"        => 0,
                    "description"   => "Main Store",
                    "parent_id"     => $warehouse->id, 
                ]
            );
            #........................................Create roles
            // supervisor
            $role_name = 'Supervisor';
            $is_service_staff = 1;
            $role = Role::create([
                'name'             => $role_name . '#' . $business->id ,
                'business_id'      => $business->id,
                'is_service_staff' => $is_service_staff
            ]);
            $permissions = [
                0 => "sidBar.Dashboard",
                1 => "izo.box_sales",
                2 => "izo.box_sales_exc",
                3 => "izo.box_sales_inc",
                4 => "izo.box_invoices",
                5 => "izo.box_invoices_number",
                6 => "izo.box_vat",
                7 => "izo.box_vat_amount",
                8 => "izo.box_customer",
                9 => "izo.box_customer_total",
                10 => "izo.box_cost_of_sales",
                11 => "izo.box_cost_of_sales_cos",
                12 => "izo.box_gross_profit",
                13 => "izo.box_gross_profit_gp",
                14 => "izo.box_goods_value",
                15 => "izo.box_goods_value_num",
                16 => "izo.box_total_customer",
                17 => "izo.box_total_customer_balance",
                18 => "izo.box_total_supplier",
                19 => "izo.box_total_supplier_balance",
                20 => "izo.box_delivered",
                21 => "izo.box_delivered_cost",
                22 => "izo.box_delivered_sales",
                23 => "izo.box_un_delivered",
                24 => "izo.box_un_delivered_cost",
                25 => "izo.box_un_delivered_sales",
                26 => "izo.box_cash_bank",
                27 => "izo.box_paid_unpaid",
                28 => "sidBar.UserManagement",
                29 => "sidBar.Users",
                30 => "sidBar.Roles",
                31 => "sidBar.Contacts",
                32 => "sidBar.Suppliers",
                33 => "sidBar.Customers",
                34 => "sidBar.CustomerGroup",
                35 => "sidBar.ImportContact",
                36 => "sidBar.Products",
                37 => "sidBar.List_Product",
                38 => "sidBar.Add_Product",
                39 => "sidBar.Variations",
                40 => "sidBar.ImportProduct",
                41 => "sidBar.Add_Opening_Stock",
                42 => "sidBar.Import_Opening_Stock",
                43 => "sidBar.Sale_Price_Group",
                44 => "sidBar.Units",
                45 => "sidBar.Categories",
                46 => "sidBar.Brands",
                47 => "sidBar.Warranties",
                48 => "sidBar.Inventory",
                49 => "sidBar.Product_Gallery",
                50 => "sidBar.Inventory_Report",
                51 => "sidBar.Inventory_Of_Warehouse",
                52 => "sidBar.Manufacturing",
                53 => "sidBar.Recipe",
                54 => "sidBar.Production",
                55 => "sidBar.Manufacturing_Report",
                56 => "sidBar.Purchases",
                57 => "sidBar.List_Purchases",
                58 => "sidBar.List_Return_Purchases",
                59 => "sidBar.Map",
                60 => "sidBar.Sales",
                61 => "sidBar.List_Sales",
                62 => "sidBar.List_Approved_Quotation",
                63 => "sidBar.List_Quotation",
                64 => "sidBar.List_Draft",
                65 => "sidBar.List_Sale_Return",
                66 => "sidBar.Sales_Commission_Agent",
                67 => "sidBar.ImportSales",
                68 => "sidBar.Quotation_Terms",
                69 => "sidBar.Vouchers",
                70 => "sidBar.List_Vouchers",
                71 => "sidBar.Add_Receipt_Voucher",
                72 => "sidBar.Add_Payment_Voucher",
                73 => "sidBar.List_Journal_Voucher",
                74 => "sidBar.List_Expense_Voucher",
                75 => "sidBar.Cheques",
                76 => "sidBar.List_Cheque",
                77 => "sidBar.Add_Cheque_In",
                78 => "sidBar.Add_Cheque_Out",
                79 => "sidBar.Contact_Bank",
                80 => "sidBar.Cash_And_Bank",
                81 => "sidBar.List_Cash",
                82 => "sidBar.List_Bank",
                83 => "sidBar.Accounts",
                84 => "sidBar.List_Account",
                85 => "sidBar.Balance_Sheet",
                86 => "sidBar.Trial_Balance",
                87 => "sidBar.Cash_Flow",
                88 => "sidBar.Payment_Account_Report",
                89 => "sidBar.List_Entries",
                90 => "sidBar.Cost_Center",
                91 => "sidBar.Warehouses",
                92 => "sidBar.List_Warehouses",
                93 => "sidBar.Warehouses_Movement",
                94 => "sidBar.Warehouse_Transafer",
                95 => "sidBar.List_Warehouse_transfer",
                96 => "sidBar.Add_Warehouse_Transfer",
                97 => "sidBar.Delivered",
                98 => "sidBar.Received",
                99 => "sidBar.Reports",
                100 => "sidBar.Profit_And_Loss_Report",
                101 => "sidBar.Daily_Product_Sale_Report",
                102 => "sidBar.Purchase_And_Sale_Report",
                103 => "sidBar.Tax_Reports",
                104 => "sidBar.Suppliers_And_Customers_Report",
                105 => "sidBar.Customers_Group_Report",
                106 => "sidBar.Inventory_Report_archive",
                107 => "sidBar.Stock_Adjustment_Report",
                108 => "sidBar.Trending_Products_Report",
                109 => "sidBar.Items_Report",
                110 => "sidBar.Product_Purchase_Report",
                111 => "sidBar.Sale_Payment_Report",
                112 => "sidBar.Report_Setting",
                113 => "sidBar.Expense_Report",
                114 => "sidBar.Register_Report",
                115 => "sidBar.Sales_Representative_Report",
                116 => "sidBar.Activity_Log",
                117 => "sidBar.Patterns",
                118 => "sidBar.Business_locations",
                119 => "sidBar.Define_Patterns",
                120 => "sidBar.System_Accounts",
                121 => "sidBar.Settings",
                122 => "sidBar.Invoice_Settings",
                123 => "sidBar.Barcode_Settings",
                124 => "sidBar.Product_Settings",
                125 => "sidBar.Receipt_Printer",
                126 => "sidBar.Tax_Rates",
                127 => "sidBar.Type_Of_Service",
                128 => "sidBar.Delete_Service",
                129 => "sidBar.Package_Subscription",
                130 => "sidBar.LogFile",
                131 => "sidBar.logUsers",
                132 => "sidBar.logBill",
                133 => "sidBar.User_Activation",
                134 => "sidBar.List_Of_Users",
                135 => "sidBar.List_Of_User_Request",
                136 => "sidBar.Create_New_User",
                137 => "sidBar.Mobile_Section",
                138 => "sidBar.React_section",
                139 => "sidBar.E_commerce",
                // Finish ######
                // 140 => "user.view",
                // 141 => "user.create",
                // 142 => "user.update",
                // 143 => "user.delete",
                // Finish ######
                // 143 => "roles.view",
                // 144 => "roles.create",
                // 145 => "roles.update",
                // 147 => "roles.delete",


                146 => "supplier.view",
                147 => "supplier.view_own",
                148 => "supplier.create",
                149 => "supplier.update",
                // 152 => "supplier.delete",


                150 => "customer.view",
                151 => "customer.view_own",
                152 => "customer.create",
                153 => "customer.update",
                // 157 => "customer.delete",

                154 => "product.view",
                155 => "product.view_sStock",
                156 => "product.avarage_cost",
                157 => "product.create",
                158 => "product.update", 
                // 163 => "product.delete",
                // 167 => "delete_product_image",

                159 => "product.opening_stock",
                160 => "stcok_compares",
                161 => "view_purchase_price",

                162 => "purchase.view",
                163 => "purchase.porduct_qty_setting",
                164 => "purchase.create",
                165 => "purchase.edit_composeit_discount",
                166 => "purchase.update",
                167 => "purchase.update_status",
                168 => "purchase.payments",
                // 174 => "purchase.received",#....
                // 173 => "purchase.delete",
                
                169 => "purchase_payment.edit",
                // 176 => "purchase_payment.delete",
                170 => "view_own_purchase",
                171 => "purchase_return.view",
                172 => "purchase_return.create",

                173 => "stock_transfer",
                174 => "stock_transfer.create_pending",
                175 => "stock_transfer.create_confirmed",
                176 => "stock_transfer.create_in_transit",
                177 => "stock_transfer.create_completed",
                178 => "stocktacking.view",
                179 => "stocktacking.show_qty_available",
                180 => "stocktacking.create",
                181 => "stocktacking.changeStatus",
                182 => "stocktacking.products",
                183 => "stocktacking.delete_form_stocktacking",
                184 => "stocktacking.report",
                185 => "stocktacking.liquidation",

                186 => "sales.print_invoice",
                187 => "sales.pos_meswada",
                188 => "sales.edit_composite_discount",
                189 => "sales.price_offer",
                190 => "sales.puse_sell",
                191 => "sales.puse_show",
                192 => "sales.sell_agel",
                193 => "sales.pay_card",
                194 => "sales.multi_pay_ways",
                195 => "sales.sell_in_cash",
                196 => "sales.less_than_purchase_price",
                197 => "sales.show",
                198 => "sales.show_current_stock_in_pos",
                199 => "sales.show_purchase_price_in_pos",
                200 => "today_sells_total.show",
                
                201 => "sell.view",
                202 => "sell.installment",
                203 => "sell.create",
                204 => "sell.can_edit",
                205 => "sell.update",
                206 => "direct_sell.access",
                // 213 => "sell.delivered",#....
                // 214 => "sell.delete",

                207 => "list_drafts",
                208 => "list_quotations",
                209 => "view_own_sell_only",

                210 => "sell.payments",
                211 => "sell_payment.edit",
                // 221 => "sell_payment.delete",
                212 => "edit_product_price_from_sale_screen",
                213 => "edit_product_price_from_pos_screen",
                214 => "edit_product_discount_from_sale_screen",
                215 => "edit_product_discount_from_pos_screen",
                216 => "access_types_of_service",
                217 => "access_sell_return",
                218 => "importsales.create",
                219 => "recent_transaction.view",
                220 => "customer_balance_due_in_pos",
                221 => "pos_lite",
                222 => "pos_repair",
                223 => "all_sales_prices",
                224 => "expenses.view",
                225 => "expense.categories",
                226 => "expense.create",
                227 => "expense.edit",
                // 238 => "expense.delete",
                228 => "view_cash_register",
                229 => "close_cash_register",
                230 => "register_payment_details.view",
                231 => "register_product_details.view",
                232 => "brand.view",
                233 => "brand.create",
                234 => "brand.update",
                // 246 => "brand.delete",
                235 => "tax_rate.view",
                236 => "tax_rate.create",
                237 => "tax_rate.update",
                // 250 => "tax_rate.delete",
                238 => "unit.view",
                239 => "unit.create",
                240 => "unit.update",
                // 254 => "unit.delete",
                241 => "category.view",
                242 => "category.create",
                243 => "category.update",
                // 258 => "category.delete",
                244 => "purchase_n_sell_report.view",
                245 => "tax_report.view",
                246 => "contacts_report.view",
                247 => "profit_loss_report.view",
                248 => "stock_report.view",
                249 => "stock_missing_report.view",
                250 => "trending_product_report.view",
                251 => "register_report.view",
                252 => "sales_representative.view",
                253 => "view_product_stock_value",
                254 => "less_trending_product_report.view",
                255 => "sell_purchase_lines_report.view",
                256 => "business_settings.backup_database",
                257 => "business_settings.access",
                258 => "barcode_settings.access",
                259 => "invoice_settings.access",
                260 => "access_printers",
                261 => "account.access",
                262 => "contact_bank.view",
                263 => "contact_bank.create",
                264 => "contact_bank.update",
                // 280 => "contact_bank.delete",
                265 => "cheque.view",
                266 => "cheque.create",
                267 => "cheque.update",
                // 284 => "cheque.delete",
                268 => "payment_voucher.view",
                269 => "payment_voucher.create",
                270 => "payment_voucher.update",
                // 288 => "payment_voucher.delete",
                271 => "daily_payment.view",
                272 => "daily_payment.create",
                273 => "daily_payment.update",
                // 292 => "daily_payment.delete",
                274 => "gournal_voucher.view",
                275 => "gournal_voucher.create",
                276 => "gournal_voucher.update",
                // 296 => "gournal_voucher.delete",
                277 => "warehouse.view",
                278 => "warehouse.movement",
                279 => "warehouse.create",
                380 => "warehouse.update",
                // 301 => "warehouse.delete",
                381 => "warehouse.recieved",
                382 => "warehouse.add_recieved",
                383 => "warehouse.delivered",
                384 => "warehouse.add_delivered",
                385 => "warehouse.invetory",
                386 => "warehouse.add_invetory",
                387 => "warehouse.adjustment",
                388 => "warehouse.add_adjustment",
                389 => "account.view",
                390 => "account.create",
                391 => "account.update",
                392 => "account.balance_sheet",
                393 => "account.trial_balance",
                394 => "account.cash_flow",
                395 => "account.payment_account_report",
                396 => "account.cost_center",
                // 312 => "account.close_account",
                370 => "account.add_cost_center",
                371 => "manufacturing.access_recipe",
                372 => "manufacturing.add_recipe",
                373 => "manufacturing.edit_recipe",
                // 319 => "manufacturing.delete_recipe",
                374 => "manufacturing.access_production",
                375 => "sidBar.logWarranties",
                376 => "status_view.index",
                // 322 => "manufacturing.delete_production",
                // 323 => "superadmin.access_package_subscriptions"
            ];
            $existing_permissions      = Permission::whereIn('name', $permissions)->pluck('name')->toArray();
            $non_existing_permissions  = array_diff($permissions, $existing_permissions);
    
            if (!empty($non_existing_permissions)) {
                foreach ($non_existing_permissions as $new_permission) {
                    $time_stamp = \Carbon::now()->toDateTimeString();
                    Permission::create([
                        'name'       => $new_permission,
                        'guard_name' => 'web'
                    ]);
                }
            }
            if (!empty($permissions)) {
                $role->syncPermissions($permissions);
            }
            // accountant
            $role_name = 'Accountant';
            $is_service_staff = 1;
            $role = Role::create([
                'name'             => $role_name . '#' . $business->id ,
                'business_id'      => $business->id,
                'is_service_staff' => $is_service_staff
            ]);
            $permissions = [
                0 => "sidBar.Dashboard",
                1 => "izo.box_sales",
                2 => "izo.box_sales_exc",
                3 => "izo.box_sales_inc",
                4 => "izo.box_invoices",
                5 => "izo.box_invoices_number",
                6 => "izo.box_vat",
                7 => "izo.box_vat_amount",
                8 => "izo.box_customer",
                9 => "izo.box_customer_total",
                10 => "izo.box_cost_of_sales",
                11 => "izo.box_cost_of_sales_cos",
                12 => "izo.box_gross_profit",
                13 => "izo.box_gross_profit_gp",
                14 => "izo.box_goods_value",
                15 => "izo.box_goods_value_num",
                16 => "izo.box_total_customer",
                17 => "izo.box_total_customer_balance",
                18 => "izo.box_total_supplier",
                19 => "izo.box_total_supplier_balance",
                20 => "izo.box_delivered",
                21 => "izo.box_delivered_cost",
                22 => "izo.box_delivered_sales",
                23 => "izo.box_un_delivered",
                24 => "izo.box_un_delivered_cost",
                25 => "izo.box_un_delivered_sales",
                26 => "izo.box_cash_bank",
                27 => "izo.box_paid_unpaid",
                28 => "sidBar.UserManagement",
                29 => "sidBar.Users",
                30 => "sidBar.Roles",
                31 => "sidBar.Contacts",
                32 => "sidBar.Suppliers",
                33 => "sidBar.Customers",
                34 => "sidBar.CustomerGroup",
                35 => "sidBar.ImportContact",
                36 => "sidBar.Products",
                37 => "sidBar.List_Product",
                38 => "sidBar.Add_Product",
                39 => "sidBar.Variations",
                40 => "sidBar.ImportProduct",
                41 => "sidBar.Add_Opening_Stock",
                42 => "sidBar.Import_Opening_Stock",
                43 => "sidBar.Sale_Price_Group",
                44 => "sidBar.Units",
                45 => "sidBar.Categories",
                46 => "sidBar.Brands",
                47 => "sidBar.Warranties",
                48 => "sidBar.Inventory",
                49 => "sidBar.Product_Gallery",
                50 => "sidBar.Inventory_Report",
                51 => "sidBar.Inventory_Of_Warehouse",
                52 => "sidBar.Manufacturing",
                53 => "sidBar.Recipe",
                54 => "sidBar.Production",
                55 => "sidBar.Manufacturing_Report",
                56 => "sidBar.Purchases",
                57 => "sidBar.List_Purchases",
                58 => "sidBar.List_Return_Purchases",
                59 => "sidBar.Map",
                60 => "sidBar.Sales",
                61 => "sidBar.List_Sales",
                62 => "sidBar.List_Approved_Quotation",
                63 => "sidBar.List_Quotation",
                64 => "sidBar.List_Draft",
                65 => "sidBar.List_Sale_Return",
                66 => "sidBar.Sales_Commission_Agent",
                67 => "sidBar.ImportSales",
                68 => "sidBar.Quotation_Terms",
                69 => "sidBar.Vouchers",
                70 => "sidBar.List_Vouchers",
                71 => "sidBar.Add_Receipt_Voucher",
                72 => "sidBar.Add_Payment_Voucher",
                73 => "sidBar.List_Journal_Voucher",
                74 => "sidBar.List_Expense_Voucher",
                75 => "sidBar.Cheques",
                76 => "sidBar.List_Cheque",
                77 => "sidBar.Add_Cheque_In",
                78 => "sidBar.Add_Cheque_Out",
                79 => "sidBar.Contact_Bank",
                80 => "sidBar.Cash_And_Bank",
                81 => "sidBar.List_Cash",
                82 => "sidBar.List_Bank",
                83 => "sidBar.Accounts",
                84 => "sidBar.List_Account",
                85 => "sidBar.Balance_Sheet",
                86 => "sidBar.Trial_Balance",
                87 => "sidBar.Cash_Flow",
                88 => "sidBar.Payment_Account_Report",
                89 => "sidBar.List_Entries",
                90 => "sidBar.Cost_Center",
                91 => "sidBar.Warehouses",
                92 => "sidBar.List_Warehouses",
                93 => "sidBar.Warehouses_Movement",
                94 => "sidBar.Warehouse_Transafer",
                95 => "sidBar.List_Warehouse_transfer",
                96 => "sidBar.Add_Warehouse_Transfer",
                97 => "sidBar.Delivered",
                98 => "sidBar.Received",
                99 => "sidBar.Reports",
                100 => "sidBar.Profit_And_Loss_Report",
                101 => "sidBar.Daily_Product_Sale_Report",
                102 => "sidBar.Purchase_And_Sale_Report",
                103 => "sidBar.Tax_Reports",
                104 => "sidBar.Suppliers_And_Customers_Report",
                105 => "sidBar.Customers_Group_Report",
                106 => "sidBar.Inventory_Report",
                107 => "sidBar.Stock_Adjustment_Report",
                108 => "sidBar.Trending_Products_Report",
                109 => "sidBar.Items_Report",
                110 => "sidBar.Product_Purchase_Report",
                111 => "sidBar.Sale_Payment_Report",
                112 => "sidBar.Report_Setting",
                113 => "sidBar.Expense_Report",
                114 => "sidBar.Register_Report",
                115 => "sidBar.Sales_Representative_Report",
                116 => "sidBar.Activity_Log",
                117 => "sidBar.Patterns",
                118 => "sidBar.Business_locations",
                119 => "sidBar.Define_Patterns",
                120 => "sidBar.System_Accounts",
                121 => "sidBar.Settings",
                122 => "sidBar.Invoice_Settings",
                123 => "sidBar.Barcode_Settings",
                124 => "sidBar.Product_Settings",
                125 => "sidBar.Receipt_Printer",
                126 => "sidBar.Tax_Rates",
                127 => "sidBar.Type_Of_Service",
                128 => "sidBar.Delete_Service",
                129 => "sidBar.Package_Subscription",
                130 => "sidBar.LogFile",
                131 => "sidBar.logUsers",
                132 => "sidBar.logBill",
                133 => "sidBar.User_Activation",
                134 => "sidBar.List_Of_Users",
                135 => "sidBar.List_Of_User_Request",
                136 => "sidBar.Create_New_User",
                137 => "sidBar.Mobile_Section",
                138 => "sidBar.React_section",
                139 => "sidBar.E_commerce",
                // Finish ######
                // 140 => "user.view",
                // 141 => "user.create",
                // 142 => "user.update",
                // 143 => "user.delete",
                // Finish ######
                // 142 => "roles.view",
                // 143 => "roles.create",
                // 146 => "roles.update",
                // 147 => "roles.delete",


                144 => "supplier.view",
                145 => "supplier.view_own",
                146 => "supplier.create",
                // 151 => "supplier.update",
                // 152 => "supplier.delete",


                145 => "customer.view",
                147 => "customer.view_own",
                148 => "customer.create",
                // 156 => "customer.update",
                // 157 => "customer.delete",

                149 => "product.view",
                150 => "product.view_sStock",
                151 => "product.avarage_cost",
                152 => "product.create",
                // 162 => "product.update", 
                // 163 => "product.delete",
                // 167 => "delete_product_image",

                153 => "product.opening_stock",
                154 => "stcok_compares",
                155 => "view_purchase_price",

                156 => "purchase.view",
                157 => "purchase.porduct_qty_setting",
                158 => "purchase.create",
                // 171 => "purchase.edit_composeit_discount",
                // 172 => "purchase.update",
                // 177 => "purchase.update_status",
                157 => "purchase.payments",
                // 174 => "purchase.received",#....
                // 173 => "purchase.delete",
                
                // 158 => "purchase_payment.edit",
                // 176 => "purchase_payment.delete",
                158 => "view_own_purchase",
                159 => "purchase_return.view",
                160 => "purchase_return.create",

                161 => "stock_transfer",
                162 => "stock_transfer.create_pending",
                163 => "stock_transfer.create_confirmed",
                164 => "stock_transfer.create_in_transit",
                165 => "stock_transfer.create_completed",
                166 => "stocktacking.view",
                167 => "stocktacking.show_qty_available",
                168 => "stocktacking.create",
                169 => "stocktacking.changeStatus",
                170 => "stocktacking.products",
                171 => "stocktacking.delete_form_stocktacking",
                172 => "stocktacking.report",
                173 => "stocktacking.liquidation",

                174 => "sales.print_invoice",
                175 => "sales.pos_meswada",
                176 => "sales.edit_composite_discount",
                177 => "sales.price_offer",
                178 => "sales.puse_sell",
                179 => "sales.puse_show",
                180 => "sales.sell_agel",
                181 => "sales.pay_card",
                182 => "sales.multi_pay_ways",
                183 => "sales.sell_in_cash",
                184 => "sales.less_than_purchase_price",
                185 => "sales.show",
                186 => "sales.show_current_stock_in_pos",
                187 => "sales.show_purchase_price_in_pos",
                188 => "today_sells_total.show",
                
                189 => "sell.view",
                190 => "sell.installment",
                191 => "sell.create",
                // 212 => "sell.can_edit",
                // 213 => "sell.update",

                192 => "direct_sell.access",
                // 213 => "sell.delivered",#....
                // 214 => "sell.delete",

                193 => "list_drafts",
                194 => "list_quotations",
                195 => "view_own_sell_only",

                196 => "sell.payments",
                // 197 => "sell_payment.edit",
                // 221 => "sell_payment.delete",

                197 => "edit_product_price_from_sale_screen",
                198 => "edit_product_price_from_pos_screen",
                199 => "edit_product_discount_from_sale_screen",
                200 => "edit_product_discount_from_pos_screen",

                201 => "access_types_of_service",
                202 => "access_sell_return",
                203 => "importsales.create",
                204 => "recent_transaction.view",
                205 => "customer_balance_due_in_pos",
                206 => "pos_lite",
                207 => "pos_repair",
                208 => "all_sales_prices",
                209 => "expenses.view",
                210 => "expense.categories",
                211 => "expense.create",
                // 237 => "expense.edit",
                // 238 => "expense.delete",
                212 => "view_cash_register",
                213 => "close_cash_register",
                214 => "register_payment_details.view",
                215 => "register_product_details.view",
                216 => "brand.view",
                217 => "brand.create",
                // 245 => "brand.update",
                // 246 => "brand.delete",
                218 => "tax_rate.view",
                219 => "tax_rate.create",
                // 220 => "tax_rate.update",
                // 250 => "tax_rate.delete",
                220 => "unit.view",
                221 => "unit.create",
                // 253 => "unit.update",
                // 254 => "unit.delete",
                222 => "category.view",
                223 => "category.create",
                // 257 => "category.update",
                // 258 => "category.delete",

                224 => "purchase_n_sell_report.view",
                225 => "tax_report.view",
                226 => "contacts_report.view",
                227 => "profit_loss_report.view",
                228 => "stock_report.view",
                229 => "stock_missing_report.view",
                230 => "trending_product_report.view",
                231 => "register_report.view",
                232 => "sales_representative.view",
                233 => "view_product_stock_value",
                234 => "less_trending_product_report.view",
                235 => "sell_purchase_lines_report.view",
                236 => "business_settings.backup_database",
                237 => "business_settings.access",
                238 => "barcode_settings.access",
                239 => "invoice_settings.access",
                240 => "access_printers",
                241 => "account.access",

                242 => "contact_bank.view",
                243 => "contact_bank.create",
                // 249 => "contact_bank.update",
                // 280 => "contact_bank.delete",

                244 => "cheque.view",
                245 => "cheque.create",
                // 246 => "cheque.update",
                // 284 => "cheque.delete",

                246 => "payment_voucher.view",
                247 => "payment_voucher.create",
                // 287 => "payment_voucher.update",
                // 288 => "payment_voucher.delete",


                248 => "daily_payment.view",
                249 => "daily_payment.create",
                // 291 => "daily_payment.update",
                // 292 => "daily_payment.delete",

                250 => "gournal_voucher.view",
                251 => "gournal_voucher.create",
                // 295 => "gournal_voucher.update",
                // 296 => "gournal_voucher.delete",

                252 => "warehouse.view",
                253 => "warehouse.movement",
                254 => "warehouse.create",
                // 300 => "warehouse.update",
                // 301 => "warehouse.delete",

                255 => "warehouse.recieved",
                256 => "warehouse.add_recieved",
                257 => "warehouse.delivered",
                258 => "warehouse.add_delivered",
                259 => "warehouse.invetory",
                260 => "warehouse.add_invetory",
                261 => "warehouse.adjustment",
                262 => "warehouse.add_adjustment",

                263 => "account.view",
                264 => "account.create",
                // 312 => "account.update",
                265 => "account.balance_sheet",
                266 => "account.trial_balance",
                267 => "account.cash_flow",
                268 => "account.payment_account_report",
                269 => "account.cost_center",
                // 312 => "account.close_account",
                267 => "account.add_cost_center",
                268 => "manufacturing.access_recipe",
                269 => "manufacturing.add_recipe",
                // 270 => "manufacturing.edit_recipe",
                // 319 => "manufacturing.delete_recipe",
                270 => "manufacturing.access_production",
                271 => "sidBar.logWarranties",
                272 => "status_view.index",
                // 322 => "manufacturing.delete_production",
                // 323 => "superadmin.access_package_subscriptions"
            ];
            $existing_permissions      = Permission::whereIn('name', $permissions)->pluck('name')->toArray();
            $non_existing_permissions  = array_diff($permissions, $existing_permissions);
    
            if (!empty($non_existing_permissions)) {
                foreach ($non_existing_permissions as $new_permission) {
                    $time_stamp = \Carbon::now()->toDateTimeString();
                    Permission::create([
                        'name'       => $new_permission,
                        'guard_name' => 'web'
                    ]);
                }
            }
            if (!empty($permissions)) {
                $role->syncPermissions($permissions);
            }
            # .......................................Create Currency 
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
                $exc                  = new \App\Models\ExchangeRate;
                $exc->business_id     = $business->id;
                $exc->currency_id     = $business->currency_id;
                $exc->amount          = 1;
                $exc->opposit_amount  = 1;
                $exc->date            = \Carbon::now();
                $exc->source          = 1;
                $exc->default         = 0;
                $exc->save();
            }  
            $payload =  [
                "value1" => Hash::make("success"),
                "value2" => $user->password,
            ];
            session(['startLogin'=> $payload ]);
            Config::set('database.connections.mysql.database', "izocloud");
            DB::purge('mysql');
            DB::reconnect('mysql'); 
            $izoCustomer_Izo              = IzoUser::where("email","info@izo.ae")->first();
            if(empty($izoCustomer_Izo)){
                $register                          = new  IzoUser();
                $device                            = $request->header('User-Agent');
                $ip                                = $request->ip();
                $database_prefix                   = 'izo26102024_'; # prefix for naming database
                $database                          = 'izo26102024_izo';
                $register->admin_user              = 1;
                $register->company_name            = "izo";
                $register->mobile                  = "+971544747703";
                $register->email                   = "info@izo.ae";
                $register->password                = $izo_details['password'];
                $register->status                  = 'main';
                $register->database_user           = $database;
                $register->database_name           = $database;
                $register->device_id               = $device;
                $register->ip                      = $ip;
                $register->domain_name             = "izo";
                $register->domain_url              = "izo.izocloud.com";
                $register->seats                   = 100; # number of user allowed
                $register->subscribe_date          = \Carbon::now();
                $register->subscribe_expire_date   = \Carbon::now()->addWeeks(3);
                $register->not_active	           = 0;
                $register->save();
            }
            $izoCustomer   = IzoUser::where("email",request()->session()->get('user_main.email'))->first();
            $izoCustomer->have_business = 1;
            $izoCustomer->update();
            $databaseName  = request()->session()->get('user_main.database') ;  
            Config::set('database.connections.mysql.database', $databaseName);
            DB::purge('mysql');
            DB::reconnect('mysql');
            DB::commit();
            $outPut = [
                "success" => 1,
                "message" => __('Register Successfully'),
            ];
            $login_user = 1; 
            session()->put(['login_user',$login_user]); 
            $domain_url  = request()->session()->get('user_main.domain_url'); 
            $database    = request()->session()->get('user_main.database'); 
            $domain      = request()->session()->get('user_main.domain');
            $domain_name = "https://".session()->get('user_main.domain').".izocloud.com";
            $domain_name = $domain_name??"";
            $text        = "email=".request()->session()->get("login_info.email")."_##password=".request()->session()->get("login_info.password")."_##logoutOther=".request()->session()->get("login_info.logoutOther")."_##administrator=1_##database=".$database."_##adminDatabaseUser=".$database."_##domain=".$domain."_##domain_url=".$domain_url."_##redirect=admin";
            $text        =  Crypt::encryptString($text);
            $url         = $domain_name."/login-account-redirect"."/".$text;    
            return redirect($url);
            // session()->put('log_out_back','logout');
            // return redirect("/home")->with("status",$outPut);
            // return view('izo_user.confirm')->with(compact(['login_user']));
        }catch(Exception $e){
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            $outPut = [
                "success" => 0,
                "message" => __('Failed'),
            ];
          return   back()->with('status', $output)->withInput();
        }
    }

    /**
     * panel.
     *
     * @return \Illuminate\Http\Response
     */
    public function panel(Request $request)
    {
        // 
         
        #.......................................................INITIAL DATA...
            $businessUtil  = new BusinessUtil();
        
            $timeZone      = [
                'Africa/Abidjan' => 'Ivory Coast Time  ( Africa/Abidjan )',
                'Africa/Accra' => 'Ghana Time  ( Africa/Accra )',
                'Africa/Addis_Ababa' => 'East Africa Time  ( Africa/Addis_Ababa )',
                'Africa/Algiers' => 'Central European Time  ( Africa/Algiers )',
                'Africa/Asmara' => 'East Africa Time  ( Africa/Asmara )',
                'Africa/Bamako' => 'Ghana Time  ( Africa/Bamako )',
                'Africa/Bangui' => 'West Africa Time  ( Africa/Bangui )',
                'Africa/Banjul' => 'Greenwich Mean Time  ( Africa/Banjul )',
                'Africa/Banjul' => 'Greenwich Mean Time  ( Africa/Banjul )',
                'Africa/Harare' => 'Central Africa Time  ( Africa/Harare )',
                'Africa/Johannesburg' => 'South Africa Standard Time  ( Africa/Johannesburg )',
                'Africa/Kampala' => 'East Africa Time  ( Africa/Kampala )',
                'Africa/Lagos' => 'West Africa Time  ( Africa/Lagos )',
                'Africa/Luanda' => 'West Africa Time  ( Africa/Luanda )',
                'Africa/Nairobi' => 'East Africa Time  ( Africa/Nairobi )',
                'Africa/Tripoli' => 'Eastern European Time  ( Africa/Tripoli )',
                'Africa/Windhoek' => 'Central Africa Time  ( Africa/Windhoek )',
                'America/Adak' => 'Hawaii-Aleutian Standard Time ( America/Adak ) ',
                'America/Anchorage' => 'Alaska Standard Time ( America/Anchorage ) ',
                'America/Argentina/Buenos_Aires' => 'Argentina Time ( America/Argentina/Buenos_Aires ) ',
                'America/Argentina/Catamarca' => 'Argentina Time ( America/Argentina/Catamarca ) ',
                'America/Argentina/ComodRivadavia' => 'Argentina Time ( America/Argentina/ComodRivadavia ) ',
                'America/Argentina/La_Rioja' => 'Argentina Time ( America/Argentina/La_Rioja ) ',
                'America/Argentina/Mendoza' => 'Argentina Time ( America/Argentina/Mendoza ) ',
                'America/Argentina/Rio_Gallegos' => 'Argentina Time ( America/Argentina/Rio_Gallegos ) ',
                'America/Argentina/Salta' => 'Argentina Time ( America/Argentina/Salta ) ',
                'America/Argentina/San_Juan' => 'Argentina Time ( America/Argentina/San_Juan ) ',
                'America/Argentina/San_Luis' => 'Argentina Time ( America/Argentina/San_Luis ) ',
                'America/Argentina/Tucuman' => 'Argentina Time ( America/Argentina/Tucuman ) ',
                'America/Argentina/Ushuaia' => 'Argentina Time ( America/Argentina/Ushuaia ) ',
                'America/Chicago' => 'Central Standard Time ( America/Chicago ) ',
                'America/Denver' => 'Mountain Standard Time ( America/Denver ) ',
                'America/Los_Angeles' => 'Pacific Standard Time ( America/Los_Angeles ) ',
                'Asia/Almaty' => 'Kazakhstan Time ( Asia/Almaty ) ',
                'Asia/Amman' => 'Jordan Time ( Asia/Amman ) ',
                'Asia/Anadyr' => 'Kamchatka Time ( Asia/Anadyr ) ',
                'Asia/Aqtau' => 'Kazakhstan Time ( Asia/Aqtau ) ',
                'Asia/Aqtobe' => 'Kazakhstan Time ( Asia/Aqtobe ) ',
                'Asia/Ashgabat' => 'Turkmenistan Time ( Asia/Ashgabat ) ',
                'Asia/Baghdad' => 'Arabian Standard Time ( Asia/Baghdad ) ',
                'Asia/Bahrain' => 'Arabian Standard Time ( Asia/Bahrain ) ',
                'Asia/Baku' => 'Azerbaijan Time ( Asia/Baku ) ',
                'Asia/Bangkok' => 'Indochina Time ( Asia/Bangkok ) ',
                'Asia/Barnaul' => 'Altai Time ( Asia/Barnaul ) ',
                'Asia/Beirut' => 'Lebanon Time ( Asia/Beirut ) ',
                'Asia/Bishkek' => 'Kyrgyzstan Time ( Asia/Bishkek ) ',
                'Asia/Brunei' => 'Brunei Time ( Asia/Brunei ) ',
                'Asia/Calcutta' => 'Indian Standard Time ( Asia/Calcutta ) ',
                'Asia/Chita' => 'Transbaikal Time ( Asia/Chita ) ',
                'Asia/Choibalsan' => 'Ulaanbaatar Time ( Asia/Choibalsan ) ',
                'Asia/Colombo' => 'Sri Lanka Time ( Asia/Colombo ) ',
                'Asia/Damascus' => 'Syria Time ( Asia/Damascus ) ',
                'Asia/Dhaka' => 'Bangladesh Time ( Asia/Dhaka ) ',
                'Asia/Dili' => 'Timor Leste Time ( Asia/Dili ) ',
                'Asia/Dubai' => 'Gulf Standard Time ( Asia/Dubai ) ',
                'Asia/Dushanbe' => 'Tajikistan Time ( Asia/Dushanbe ) ',
                'Asia/Gaza' => 'Palestine Time ( Asia/Gaza ) ',
                'Asia/Hong_Kong' => 'Hong Kong Time ( Asia/Hong_Kong ) ',
                'Asia/Hovd' => 'Hovd Time ( Asia/Hovd ) ',
                'Asia/Irkutsk' => 'Irkutsk Time ( Asia/Irkutsk ) ',
                'Asia/Jakarta' => 'Western Indonesia Time ( Asia/Jakarta ) ',
                'Asia/Jayapura' => 'Eastern Indonesia Time ( Asia/Jayapura ) ',
                'Asia/Kabul' => 'Afghanistan Time ( Asia/Kabul ) ',
                'Asia/Karachi' => 'Pakistan Standard Time ( Asia/Karachi ) ',
                'Asia/Kathmandu' => 'Nepal Time ( Asia/Kathmandu ) ',
                'Asia/Kolkata' => 'Indian Standard Time ( Asia/Kolkata ) ',
                'Asia/Krasnoyarsk' => 'Krasnoyarsk Time ( Asia/Krasnoyarsk ) ',
                'Asia/Kuala_Lumpur' => 'Malaysia Time ( Asia/Kuala_Lumpur ) ',
                'Asia/Kuwait' => 'Arabian Standard Time ( Asia/Kuwait ) ',
                'Asia/Macau' => 'Macau Standard Time ( Asia/Macau ) ',
                'Asia/Magadan' => 'Magadan Time ( Asia/Magadan ) ',
                'Asia/Makassar' => 'Central Indonesia Time ( Asia/Makassar ) ',
                'Asia/Manila' => 'Philippine Time ( Asia/Manila ) ',
                'Asia/Muscat' => 'Gulf Standard Time ( Asia/Muscat ) ',
                'Asia/Nicosia' => 'Eastern European Time ( Asia/Nicosia ) ',
                'Asia/Novosibirsk' => 'Novosibirsk Time ( Asia/Novosibirsk ) ',
                'Asia/Omsk' => 'Omsk Time ( Asia/Omsk ) ',
                'Asia/Oral' => 'Oral Time ( Asia/Oral ) ',
                'Asia/Phnom_Penh' => 'Indochina Time ( Asia/Phnom_Penh ) ',
                'Asia/Pontianak' => 'Western Indonesia Time ( Asia/Pontianak ) ',
                'Asia/Qatar' => 'Arabian Standard Time ( Asia/Qatar ) ',
                'Asia/Qyzylorda' => 'Kazakhstan Time ( Asia/Qyzylorda ) ',
                'Asia/Riyadh' => 'Arabian Standard Time ( Asia/Riyadh ) ',
                'Asia/Sakhalin' => 'Sakhalin Time ( Asia/Sakhalin ) ',
                'Asia/Samarkand' => 'Uzbekistan Time ( Asia/Samarkand ) ',
                'Asia/Seoul' => 'Korea Standard Time ( Asia/Seoul ) ',
                'Asia/Shanghai' => 'China Standard Time ( Asia/Shanghai ) ',
                'Asia/Singapore' => 'Singapore Standard Time ( Asia/Singapore ) ',
                'Asia/Taipei' => 'Taipei Time ( Asia/Taipei ) ',
                'Asia/Tashkent' => 'Uzbekistan Time ( Asia/Tashkent ) ',
                'Asia/Tbilisi' => 'Georgia Time ( Asia/Tbilisi ) ',
                'Asia/Tehran' => 'Iran Standard Time ( Asia/Tehran ) ',
                'Asia/Thimphu' => 'Bhutan Time ( Asia/Thimphu ) ',
                'Asia/Tokyo' => 'Japan Standard Time ( Asia/Tokyo ) ',
                'Asia/Ulaanbaatar' => 'Ulaanbaatar Time ( Asia/Ulaanbaatar ) ',
                'Asia/Urumqi' => 'China Standard Time ( Asia/Urumqi ) ',
                'Asia/Vientiane' => 'Indochina Time ( Asia/Vientiane ) ',
                'Asia/Vladivostok' => 'Vladivostok Time ( Asia/Vladivostok ) ',
                'Asia/Yakutsk' => 'Yakutsk Time ( Asia/Yakutsk ) ',
                'Asia/Yangon' => 'Myanmar Time ( Asia/Yangon ) ',
                'Asia/Yekaterinburg' => 'Yekaterinburg Time ( Asia/Yekaterinburg ) ',
                'Asia/Yerevan' => 'Armenia Time ( Asia/Yerevan ) ',
                'Australia/Adelaide' => 'Australian Central Standard Time ( Australia/Adelaide ) ',
                'Australia/Brisbane' => 'Australian Eastern Standard Time ( Australia/Brisbane ) ',
                'Australia/Darwin' => 'Australian Central Standard Time ( Australia/Darwin ) ',
                'Australia/Hobart' => 'Australian Eastern Standard Time ( Australia/Hobart ) ',
                'Australia/Melbourne' => 'Australian Eastern Standard Time ( Australia/Melbourne ) ',
                'Australia/Perth' => 'Australian Western Standard Time ( Australia/Perth ) ',
                'Australia/Sydney' => 'Australian Eastern Standard Time ( Australia/Sydney ) ',
                'Europe/Amsterdam' => 'Central European Time ( Europe/Amsterdam ) ',
                'Europe/Andorra' => 'Central European Time ( Europe/Andorra ) ',
                'Europe/Athens' => 'Eastern European Time ( Europe/Athens ) ',
                'Europe/Belgrade' => 'Central European Time ( Europe/Belgrade ) ',
                'Europe/Berlin' => 'Central European Time ( Europe/Berlin ) ',
                'Europe/Brussels' => 'Central European Time ( Europe/Brussels ) ',
                'Europe/Bucharest' => 'Eastern European Time ( Europe/Bucharest ) ',
                'Europe/Budapest' => 'Central European Time ( Europe/Budapest ) ',
                'Europe/Chisinau' => 'Moldova Time ( Europe/Chisinau ) ',
                'Europe/Copenhagen' => 'Central European Time ( Europe/Copenhagen ) ',
                'Europe/Dublin' => 'Irish Standard Time ( Europe/Dublin ) ',
                'Europe/Helsinki' => 'Eastern European Time ( Europe/Helsinki ) ',
                'Europe/Istanbul' => 'Turkey Time ( Europe/Istanbul ) ',
                'Europe/Kiev' => 'Eastern European Time ( Europe/Kiev ) ',
                'Europe/Lisbon' => 'Western European Time ( Europe/Lisbon ) ',
                'Europe/London' => 'Greenwich Mean Time ( Europe/London ) ',
                'Europe/Luxembourg' => 'Central European Time ( Europe/Luxembourg ) ',
                'Europe/Madrid' => 'Central European Time ( Europe/Madrid ) ',
                'Europe/Minsk' => 'Minsk Time ( Europe/Minsk ) ',
                'Europe/Monaco' => 'Central European Time ( Europe/Monaco ) ',
                'Europe/Moscow' => 'Moscow Time ( Europe/Moscow ) ',
                'Europe/Oslo' => 'Central European Time ( Europe/Oslo ) ',
                'Europe/Paris' => 'Central European Time ( Europe/Paris ) ',
                'Europe/Prague' => 'Central European Time ( Europe/Prague ) ',
                'Europe/Riga' => 'Eastern European Time ( Europe/Riga ) ',
                'Europe/Rome' => 'Central European Time ( Europe/Rome ) ',
                'Europe/Sofia' => 'Eastern European Time ( Europe/Sofia ) ',
                'Europe/Stockholm' => 'Central European Time ( Europe/Stockholm ) ',
                'Europe/Tallinn' => 'Eastern European Time ( Europe/Tallinn ) ',
                'Europe/Vienna' => 'Central European Time ( Europe/Vienna ) ',
                'Europe/Vilnius' => 'Eastern European Time ( Europe/Vilnius ) ',
                'Europe/Warsaw' => 'Central European Time ( Europe/Warsaw ) ',
                'Europe/Zagreb' => 'Central European Time ( Europe/Zagreb ) ',
                'Europe/Zaporozhye' => 'Eastern European Time ( Europe/Zaporozhye ) ',
            ]; 
            $country      = [
                "AE" => "United Arab Emirates (+AE) ",
                "US" => "United States (+US) ",
                "CA" => "Canada (+CA) ",
                "GB" => "United Kingdom (+GB) ",
                "IN" => "India (+IN) ",
                "AU" => "Australia (+AU) ",
                "DE" => "Germany (+DE) ",
                "FR" => "France (+FR) ",
                "IT" => "Italy (+IT) ",
                "ES" => "Spain (+ES) ",
                "BR" => "Brazil (+BR) ",
                "MX" => "Mexico (+MX) ",
                "JP" => "Japan (+JP) ",
                "CN" => "China (+CN) ",
                "RU" => "Russia (+RU) ",
                "ZA" => "South Africa (+ZA) ",
                "AR" => "Argentina (+AR) ",
                "KR" => "South Korea (+KR) ",
                "NG" => "Nigeria (+NG) ",
                "EG" => "Egypt (+EG) ",
                "SG" => "Singapore (+SG) ",
                "NZ" => "New Zealand (+NZ) ",
                "SE" => "Sweden (+SE) ",
                "NO" => "Norway (+NO) ",
                "FI" => "Finland (+FI) ",
                "DK" => "Denmark (+DK) ",
                "BE" => "Belgium (+BE) ",
                "PL" => "Poland (+PL) ",
                "PT" => "Portugal (+PT) ",
                "GR" => "Greece (+GR) ",
                "CH" => "Switzerland (+CH) ",
                "AT" => "Austria (+AT) ",
                "NL" => "Netherlands (+NL) ",
                "CZ" => "Czech Republic (+CZ) ",
                "HU" => "Hungary (+HU) ",
                "RO" => "Romania (+RO) ",
                "TR" => "Turkey (+TR) ",
                "KR" => "South Korea (+KR) ",
                "PH" => "Philippines (+PH) ",
                "TH" => "Thailand (+TH) ",
                "ID" => "Indonesia (+ID) ",
                "MY" => "Malaysia (+MY) ",
                "VN" => "Vietnam (+VN) ",
                "PK" => "Pakistan (+PK) ",
                "KE" => "Kenya (+KE) ",
                "PE" => "Peru (+PE) ",
                "CL" => "Chile (+CL) ",
                "CO" => "Colombia (+CO) ",
                "UY" => "Uruguay (+UY) ",
                "EC" => "Ecuador (+EC) ",
                "VE" => "Venezuela (+VE) ",
            ];
            $company_size = [
                "free_worker"    => "Free worker",
                "1_5_worker"     => "1 - 5 Male/female employee",
                "6_10_worker"    => "6 - 10 Male/female employee",
                "11_50_worker"   => "11 - 50 Male/female employee",
                "51_200_worker"  => "51 - 200 Male/female employee",
                "201_500_worker" => "201 - 500 Male/female employee",
                "500_worker"     => "More Than 500 employee",
            ]; 
            $jobs         = [
                "accounts_officer"           => "Accounts Officer",  
                "sales_officer"              => "Sales Officer",  
                "human_resources_officer"    => "Human Resources Officer",  
                "inventory_officer"          => "Inventory officer",  
                "customer_relations_officer" => "Customer Relations Officer",  
                "ceo"                        => "CEO",  
                "operations_officer"         => "Operations Officer"  
            ];

            $language     = [];  
            $config_languages = config('constants.langs');
            foreach ($config_languages as $key => $value) {
                $language[$key] = $value['full_name'];
            }
        #......................................................................
        #........................................................READ DOMAIN...
            $url       = request()->root();
            $parsedUrl = parse_url($url);
            $host      = $parsedUrl['host'] ?? '';  
            $hostParts = explode('.', $host); 
            if (count($hostParts) == 3) {
                // Remove the last two parts (domain and TLD)
                array_pop($hostParts); // TLD
                array_pop($hostParts); // Domain
                // The remaining parts are the subdomain
                $subdomain = implode('.', $hostParts);
            } else if(count($hostParts) == 3){
                // Remove the last two parts (domain and TLD)
                array_pop($hostParts); // TLD
                // The remaining parts are the subdomain
                $subdomain = implode('.', $hostParts);
            } else {
                // No subdomain
                $subdomain = '';

            }
            $subdomain     = $subdomain;  
        #......................................................................
        #..................................................REDIRECT TO LOGIN...
           
         
            if(session()->has('startLogin')){
                if($subdomain == ""){
                    $login_user = (request()->session()->get('login_user'))?request()->session()->get('login_user'):null; 
                    
                    if($login_user == null){
                        if(!session()->has('user_main')){
                            return view('izo_user.confirm');
                        }
                    }else{ 
                        return view('izo_user.confirm')->with(compact(['login_user']));
                    }
                } 
            }
        #......................................................................
        #.....................................................CHECK IF EXIST...
            $database_name  = request()->session()->get('user_main.database');
            $email          = request()->session()->get('user_main.email');
            Config::set('database.connections.mysql.database', $database_name);
            DB::purge('mysql');
            DB::reconnect('mysql');
            $currency     = $businessUtil->allCurrencies();
            if(count($currency)==0){
            $currency     = [
            "1"  => "Albania (ALL) ",
            "2"  => "America (USD) ",
            "3"  => "Afghanistan (AF) ",
            "4"  => "Argentina (ARS) ",
            "5"  => "Aruba (AWG) ",
            "6"  => "Australia (AUD) ",
            "7"  => "Azerbaijan (AZ) ",
            "8"  => "Bahamas (BSD) ",
            "9"  => "Barbados (BBD) ",
            "10"  => "Belarus (BYR) ",
            "11"  => "Belgium (EUR) ",
            "12"  => "Beliz (BZD) ",
            "13"  => "Bermuda (BMD) ",
            "14"  => "Bolivia (BOB) ",
            "15"  => "Bosnia and Herzegovina (BAM) ",
            "16"  => "Botswana (BWP) ",
            "17"  => "Bulgaria (BG) ",
            "18"  => "Brazil (BRL) ",
            "19"  => "Britain [United Kingdom] (GBP) ",
            "20"  => "Brunei Darussalam (BND) ",
            "21"  => "Cambodia (KHR) ",
            "22"  => "Canada (CAD) ",
            "23"  => "Cayman Islands (KYD) ",
            "24"  => "Chile (CLP) ",
            "25"  => "China (CNY) ",
            "26"  => "Colombia (COP) ",
            "27"  => "Costa Rica (CRC) ",
            "28"  => "Croatia (HRK) ",
            "29"  => "Cuba (CUP) ",
            "30"  => "Cyprus (EUR) ",
            "31"  => "Czech Republic (CZK) ",
            "32"  => "Denmark (DKK) ",
            "33"  => "Dominican Republic (DOP ) ",
            "34"  => "East Caribbean (XCD) ",
            "35"  => "Egypt (EGP) ",
            "36"  => "El Salvador (SVC) ",
            "37"  => "England [United Kingdom] (GBP) ",
            "38"  => "Euro (EUR) ",
            "39"  => "Falkland Islands (FKP) ",
            "40"  => "Fiji (FJD) ",
            "41"  => "France (EUR) ",
            "42"  => "Ghana (GHS) ",
            "43"  => "Gibraltar (GIP) ",
            "44"  => "Greece (EUR) ",
            "45"  => "Guatemala (GTQ) ",
            "46"  => "Guernsey (GGP) ",
            "47"  => "Guyana (GYD) ",
            "48"  => "Holland [Netherlands] (EUR) ",
            "49"  => "Honduras (HNL) ",
            "50"  => "Hong Kong (HKD) ",
            "51"  => "Hungary (HUF) ",
            "52"  => "Iceland (ISK) ",
            "53"  => "India (INR) ",
            "54"  => "Indonesia (IDR) ",
            "55"  => "Iran (IRR) ",
            "56"  => "Ireland (EUR) ",
            "57"  => "Isle of Man (IMP) ",
            "58"  => "Israel (ILS) ",
            "59"  => "Italy (EUR) ",
            "60"  => "Jamaica (JMD) ",
            "61"  => "Japan (JPY) ",
            "62"  => "Jersey (JEP) ",
            "63"  => "Kazakhstan (KZT) ",
            "64"  => "Korea [North] (KPW) ",
            "65"  => "Korea [South] (KRW) ",
            "66"  => "Kyrgyzstan (KGS) ",
            "67"  => "Laos (LAK) ",
            "68"  => "Latvia (LVL) ",
            "69"  => "Lebanon (LBP) ",
            "70"  => "Liberia (LRD) ",
            "71"  => "Liechtenstein (CHF) ",
            "72"  => "Lithuania (LTL) ",
            "73"  => "Luxembourg (EUR) ",
            "74"  => "Macedonia (MKD) ",
            "75"  => "Malaysia (MYR) ",
            "76"  => "Malta (EUR) ",
            "77"  => "Mauritius (MUR) ",
            "78"  => "Mexico (MXN) ",
            "79"  => "Mongolia (MNT) ",
            "80"  => "Mozambique (MZ) ",
            "81"  => "Namibia (NAD) ",
            "82"  => "Nepal (NPR) ",
            "83"  => "Netherlands Antilles (ANG) ",
            "84"  => "Netherlands (EUR) ",
            "85"  => "New Zealand (NZD) ",
            "86"  => "Nicaragua (NIO) ",
            "87"  => "Nigeria (NGN) ",
            "88"  => "North Korea (KPW) ",
            "89"  => "Norway (NOK) ",
            "90"  => "Oman (OMR) ",
            "91"  => "Pakistan (PKR) ",
            "92"  => "Panama (PAB) ",
            "93"  => "Paraguay (PYG) ",
            "94"  => "Peru (PE) ",
            "95"  => "Philippines (PHP) ",
            "96"  => "Poland (PL) ",
            "97"  => "Qatar (QAR) ",
            "98"  => "Romania (RO) ",
            "99"  => "Russia (RUB) ",
            "100"  => "Saint Helena (SHP) ",
            "101"  => "Saudi Arabia (?.?) ",
            "102"  => "Serbia (RSD) ",
            "103"  => "Seychelles (SCR) ",
            "104"  => "Singapore (SGD) ",
            "105"  => "Slovenia (EUR) ",
            "106"  => "Solomon Islands (SBD) ",
            "107"  => "Somalia (SOS) ",
            "108"  => "South Africa (ZAR) ",
            "109"  => "South Korea (KRW) ",
            "110"  => "Spain (EUR) ",
            "111"  => "Sri Lanka (LKR) ",
            "112"  => "Sweden (SEK) ",
            "113"  => "Switzerland (CHF) ",
            "114"  => "Suriname (SRD) ",
            "115"  => "Syria (SYP) ",
            "116"  => "Taiwan (TWD) ",
            "117"  => "Thailand (THB) ",
            "118"  => "Trinidad and Tobago (TTD) ",
            "119"  => "Turkey (TRY) ",
            "120"  => "Turkey (TRL) ",
            "121"  => "Tuvalu (TVD) ",
            "122"  => "Ukraine (UAH) ",
            "123"  => "United Kingdom (GBP) ",
            "124"  => "United States of America (USD) ",
            "125"  => "Uruguay (UYU) ",
            "126"  => "Uzbekistan (UZS) ",
            "127"  => "Vatican City (EUR) ",
            "128"  => "Venezuela (VEF) ",
            "129"  => "Vietnam (VND) ",
            "130"  => "Yemen (YER) ",
            "131"  => "Zimbabwe (ZWD) ",
            "132"  => "Iraq (IQD) ",
            "133"  => "Kenya (KES) ",
            "134"  => "Bangladesh (BDT) ",
            "135"  => "Algerie (DZD) ",
            "136"  => "United Arab Emirates (AED) ",
            "137"  => "Uganda (UGX) ",
            "138"  => "Tanzania (TZS) ",
            "139"  => "Angola (AOA) ",
            "140"  => "Kuwait (KWD) ",
            "141"  => "Bahrain (BHD) ",
            "142"  => "Syrian Pound (SOR) ",
                
                ];
            }
            $user = \App\User::where("username",$email)->first();
            if($user){
                if(\Hash::check(request()->session()->get('password'),$user->password)){ 
                    // return redirect("/");
                }else{
                    // return redirect("/login");
                }
            }
            Config::set('database.connections.mysql.database', "izocloud");
            DB::purge('mysql');
            DB::reconnect('mysql');
        #......................................................................
        return view('izo_user.panel')->with(compact(['jobs','company_size','language','country','currency','timeZone']));
    }

    /**
     * panel.
     *
     * @return \Illuminate\Http\Response
     */
    public function saveCompany(Request $request)
    {
        // 
        // DD($request);
        Config::set('database.connections.mysql.database', "izocloud");
        DB::purge('mysql');
        DB::reconnect('mysql');  
       
        $request->validate([
            'g-recaptcha-response' => ['required', new Captcha],
            'email'                => 'required|email',
            'mobile'               => 'required|min:7|max:9',
            // Other validation rules...
        ] 
        );
        $data = $request->only(['company_name','email','domain_name','mobile','mobile_code','password']);
        $data['User-Agent']  = $request->header('User-Agent');
        $data['ip']          = $request->ip();
        
        
        // dd($responseBody);
 
        $save = IzoUser::saveUser($data);
        
        if(!$save){
            return redirect('/register-account');
        }else{
            return redirect('/panel-account');
        }
    }

    /**
     * panel.
     *
     * @return \Illuminate\Http\Response
     */
    protected function login(Request $request)
    {
        // 
        #.....................................every time from the main
        Config::set('database.connections.mysql.database', "izocloud");
        DB::purge('mysql');
        DB::reconnect('mysql');
        #....................................
        if(!isset($request->redirect)){
            $request->validate([
                'g-recaptcha-response' => ['required', new Captcha], 
                // Other validation rules...
            ] );
        }
        $data                = $request->only(['email','password','domain_name_sub','logout_other']);
        $login = IzoUser::loginUser($data);
        
        if(!$login['status']){
            return redirect('/login-account');
        }
         
         
        session(['change_lang'  => "change"]);
        $url       = request()->root();
        $parsedUrl = parse_url($url);
        $host      = $parsedUrl['host'] ?? '';  
        $hostParts = explode('.', $host); 
        if (count($hostParts) == 3) {
            // Remove the last two parts (domain and TLD)
            array_pop($hostParts); // TLD
            array_pop($hostParts); // Domain

            // The remaining parts are the subdomain
            $subdomain = implode('.', $hostParts);
        } else if(count($hostParts) == 3){
            // Remove the last two parts (domain and TLD)
            array_pop($hostParts); // TLD

            // The remaining parts are the subdomain
            $subdomain = implode('.', $hostParts);
        } else {
            // No subdomain
            $subdomain = '';

        }
        $subdomain     = $subdomain;
        
        if(!isset($request->redirect)){
            if($subdomain == "" && $data['email'] == "info@izo.ae"){
            // if(request()->session()->get('adminLogin')){
                // dd($subdomain,"inside");
                // }
                $domain_name =  $login['domain'];
                $payload2 =  [
                    "email"       => $data['email'],
                    "password"    => $data['password'],
                    "logoutOther" => isset($data['logout_other'])?$data['logout_other']:null
                ];
                $secret = [
                    "secret" => "admin",
                    "exp"    => \Carbon::now()->addMinute(10)->toDateTimeString()
                ];
                session(['login_info'  => $payload2]);
                session(['secret'      => $secret ]);
                $login_user = 1;
                return redirect('/choose-company');
            }
        }

        
        // dd($subdomain,"outside",$request,$login['password'],request()->session()->all());
        #.............................
        // if(isset($request->info_database)){
        //     $payload =  [
        //         "value1" => Hash::make("success"),
        //         "value2" => $login['password']
        //     ];
        //     session(['startLogin'  => $payload]);
        //     $database_info["database"] = $login['database'];
        //     $database_info["admin"]    = $login['admin'];
        //     $database_name             = $database_info ;
       
            
        //     // dd($subdomain != $login['domain']);
        //     if($subdomain == ""){
        //             $domain_name =  $login['domain'];
        //             $payload2 =  [
        //             "email"    => $data['email'],
        //             "password" => $data['password'],
        //             "logoutOther" => isset($data['logout_other'])?$data['logout_other']:null
        //         ];
        //         session(['login_info'  => $payload2]); 
        //         return redirect($login['url'])->with(compact('domain_name'));
        //     }
            
              
          
        //     //  return parent::login($request);
        //     return $this->traitLogin($request,$database_name);
        // }

        if($subdomain == ""){
            $payload =  [
                "value1" => Hash::make("success"),
                "value2" => $login['password']
            ];
            session(['startLogin'  => $payload]);
            $database_info["database"] = $login['database'];
            $database_info["admin"]    = $login['admin'];
            $database_name             = $database_info ;
            
            if($subdomain == ""){
                $domain_name =  $login['domain'];
                $payload2 =  [
                    "email"       => $data['email'],
                    "password"    => $data['password'],
                    "logoutOther" => isset($data['logout_other'])?$data['logout_other']:null
                ];
                session(['login_info'  => $payload2]);
                
                if(isset($request->redirect)){
                    if($request->redirect == "admin"){
                        session()->put('user_main.database',$request->info_database);
                        session()->put('user_main.database_user',$request->info_database_user);
                        session()->put('user_main.domain_url',$request->info_domain_url);
                        session()->put('user_main.domain',$request->info_domain);
                        $login['database']      = $request->info_database;
                        $login['database_user'] = $request->info_database_user;
                        $login['domain']        = $request->info_domain;
                        $login['domain_url']    = $request->info_domain_url;
                    }
                } 
                // dd($login,session()->all(),request()->session()->get('adminLogin'));
                $login_user = 1;
                
                if(isset($request->redirect)){
                    if($request->redirect == "admin"){
                        return redirect($login['url'])->with('login_user',$login_user)->with('redirect_admin',$login);
                    }
                }else{
                    session()->forget('redirect_admin');
                    return redirect($login['url'])->with('login_user',$login_user);
                } 
            }
            //  return parent::login($request);
            return $this->traitLogin($request,$database_name);
        }else{
            
            if(isset($request->info_database)){
                session()->put('user_main.database',$request->info_database);
                session()->put('user_main.database_user',$request->info_database_user);
                session()->put('user_main.domain_url',$request->info_domain_url);
                session()->put('user_main.domain',$request->info_domain);
                $login['database']      = $request->info_database;
                $login['database_user'] = $request->info_database_user;
                $login['domain']        = $request->info_domain;
            }
            
             
            if($subdomain != strtolower($login['domain'])){
                // dd($login);
                request()->session()->flush();
                // $url = 
                // return view('izo_user.confirm');
                return redirect("/login-account");
            }else{
                
                $payload =  [
                    "value1" => Hash::make("success"),
                    "value2" => $login['password']
                ];
                session(['startLogin'  => $payload]);
                $database_info["database"] = $login['database'];
                $database_info["admin"]    = $login['admin'];
                $database_name             = $database_info ;
           
                
                // dd($subdomain != $login['domain']);
                if($subdomain == ""){
                        $domain_name =  $login['domain'];
                        $payload2 =  [
                        "email"    => $data['email'],
                        "password" => $data['password'],
                        "logoutOther" => isset($data['logout_other'])?$data['logout_other']:null
                    ];
                    session(['login_info'  => $payload2]); 
                    return redirect($login['url'])->with(compact('domain_name'));
                }
                
                
              
                //  return parent::login($request);
                return $this->traitLogin($request,$database_name);
            }
        } 
    }

    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function traitLogin(Request $request,$databaseInfo=null)
    { 
        $this->validateLogin($request);
       
        if($databaseInfo != null){
            $database = $databaseInfo['database'];
        }else{
            $database = null;
        }   
        // Determine the database connection settings dynamically
        $this->setDatabaseConnection($request,$database);
        
        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if (method_exists($this, 'hasTooManyLoginAttempts') &&
        $this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);
            return $this->sendLockoutResponse($request);
        }
        
        if ($this->attemptLogin($request)) {
            $credentials = $request->only('email', 'password');
            \Auth::attempt($credentials);
            // dd(\Auth::attempt($credentials),\Auth::user());
            if ($request->hasSession()) {
                $request->session()->put('auth.password_confirmed_at', time());
            } 
            return $this->sendLoginResponse($request);
        }
        
        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }

    
    /**
     * The user has been authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function authenticated(Request $request, $user)
    { 
        
        // $url         = request()->root();
        // $parsedUrl   = parse_url($url);
        // $host        = $parsedUrl['host'] ?? '';  
        // $hostParts   = explode('.', $host);
        // $domain_url  = request()->session()->get('user_main.domain_url') ;
        // $final_url   = "http://".$domain_url.":8000/home";
        // return redirect()->away($final_url);
        
        
        #...............................................
        if($user->user_account_id != null || $user->user_visa_account_id != null){
            // session()->flush();
            \Auth::logout();
            return redirect('/login-account')
            ->with(
                'status',
                ['success' => 0, 'msg' => __('lang_v1.login_not_allowed')]
            );
        }
        $session     = \App\Models\SessionTable::where("user_id",$user->id)->select()->first();
        if(!empty($session)){
            if($user->id == 1){ 
                if(isset($request->logout_other)){
                    
                    $session->delete(); 
                    session()->put('delete_session','delete');
                }else{
                     
                    session()->forget('user_main');
                    session()->forget('password');
                    session()->forget('startLogin');
                    session()->forget('change_lang');
                    session()->forget('login_info');
                    // session()->forget('adminLogin');
                    session()->forget('secret');
                    session()->forget('create_session');
                    session()->forget('user');
                    session()->forget('business');
                    session()->forget('currency');
                    session()->forget('locale');
                    session()->forget('financial_year');
                    \Auth::logout();
                    return redirect('/login-account')
                    ->with(
                        'status',
                        ['success' => 0, 'msg' => __('lang_v1.sorry_there_is_device_active')]
                    );
                }
            }else if(isset($request->logout_other)){
                $session->delete(); 
            }
        }else{
            session()->put('create_session','first_login');
        } 
        
        #...............................................
        if(!session()->get('lang')){
            session(['lang'  => "en"]); 
        }
        

        return redirect("/home");
      
    }


    /**
     * panel.
     *
     * @return \Illuminate\Http\Response
     */
    public function logoutIzo(Request $request)
    {
        //  
        // session()->flush();
        $url       = request()->root();
        $parsedUrl = parse_url($url);
        $host      = $parsedUrl['host'] ?? '';  
        $hostParts = explode('.', $host); 
        if (count($hostParts) == 3) {
            // Remove the last two parts (domain and TLD)
            array_pop($hostParts); // TLD
            array_pop($hostParts); // Domain

            // The remaining parts are the subdomain
            $subdomain = implode('.', $hostParts);
        } else if(count($hostParts) == 3){
            // Remove the last two parts (domain and TLD)
            array_pop($hostParts); // TLD

            // The remaining parts are the subdomain
            $subdomain = implode('.', $hostParts);
        } else {
            // No subdomain
            $subdomain = '';

        }
        session()->forget('user_main');
        session()->forget('password');
        session()->forget('startLogin');
        session()->forget('change_lang');
        session()->forget('login_info');
        // session()->forget('adminLogin');
        session()->forget('secret');
        session()->forget('create_session');
        session()->forget('user');
        session()->forget('business');
        session()->forget('currency');
        session()->forget('locale');
        session()->forget('financial_year');
        \Auth::logout();
        if($subdomain != ""){
            session()->put('log_out_back','logout');
        } 
        return redirect('/login-account');
     
    }

    /**
     * panel.
     *
     * @return \Illuminate\Http\Response
     */
    public function checkEmail(Request $request)
    {
        //
        // DD($request);
        Config::set('database.connections.mysql.database', "izocloud");
        DB::purge('mysql');
        DB::reconnect('mysql');
        if(request()->ajax()){
            
            $em            = IzoUser::pluck('email')->toArray();
            $email         = request()->input('email');
            return $outPut = ["success"=>1,"message"=>(!in_array(trim($email),$em)) ];
        }
    }
    /**
     * company name.
     *
     * @return \Illuminate\Http\Response
     */
    public function checkCompanyName(Request $request)
    {
        //
        // DD($request);
        Config::set('database.connections.mysql.database', "izocloud");
        DB::purge('mysql');
        DB::reconnect('mysql');
        if(request()->ajax()){  
            $company       = IzoUser::pluck('company_name')->toArray();
            $company_name  = request()->input('company_name');
            return $outPut = ["success"=>1,"message"=>(!in_array(trim($company_name),$company)) ];
        }
    }
    /**
     * panel.
     *
     * @return \Illuminate\Http\Response
     */
    public function checkDomainName(Request $request)
    {
        //
        // DD($request);
        Config::set('database.connections.mysql.database', "izocloud");
        DB::purge('mysql');
        DB::reconnect('mysql');
        if(request()->ajax()){ 
            $domain        = IzoUser::pluck('domain_name')->toArray();
            $domain_name   = request()->input('domain_name');
            return $outPut = ["success"=>1,"message"=>(!in_array(trim($domain_name),$domain))];
        }
    }
    
    /**
     * panel.
     *
     * @return \Illuminate\Http\Response
     */
    public function checkMobile(Request $request)
    {
        //
        // DD($request);
        Config::set('database.connections.mysql.database', "izocloud");
        DB::purge('mysql');
        DB::reconnect('mysql');
        if(request()->ajax()){
            $mob           = IzoUser::pluck('mobile')->toArray();
            $code          = request()->input('mobile_code');
            $mobile        = request()->input('mobile');
            return $outPut = ["success"=>1,"message"=>(!in_array(trim($code.$mobile),$mob))];
        }
    }
    
    /**
     * panel.
     *
     * @return \Illuminate\Http\Response
     */
    public function activateEmailCode(Request $request)
    {
        //
        if(request()->ajax()){
            $email_sender = request()->input('email');
            $mail          = new PHPMailer(true);
            try {
                //Server settings
                $mail->SMTPDebug  = SMTP::DEBUG_SERVER;                           //Enable verbose debug output
                $mail->isSMTP();                                                  //Send using SMTP
                // $mail->Host       = 'sandbox.smtp.mailtrap.io';                //Set the SMTP server to send through
                $mail->Host       = 'smtp.gmail.com';                             //Set the SMTP server to send through
                $mail->SMTPAuth   = true;                                         //Enable SMTP authentication
                // $mail->Username   = '26ba541e9256f2';                          //SMTP username
                // $mail->Password   = '06ed0ff8f2e290';                          //SMTP password
                // $mail->Username   = 'alhamwi.agt@gmail.com';                      //SMTP username
                // $mail->Password   = 'fmhmlparvdssqovw';                           //SMTP password
                $mail->Username   = 'info@izo.ae';                      //SMTP username
                $mail->Password   = 'pemzdmzhejbreuzg';                           //SMTP password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;               //Enable implicit TLS encryption ENCRYPTION_SMTPS 465
                $mail->Port       = 587;                                          //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
    
                // Recipients
                $mail->setFrom('activation@izocloud.com', 'Activation - IZOCLOUD - ERP SYSTEM');
                $mail->addAddress('iebrahemsai944@gmail.com');               //Name is optional
                $mail->addAddress($email_sender);                            //Name is optional
                // $mail->addAddress('osama.hamwi@live.com');                //Name is optional
                // $mail->addAddress('izocloud@outlook.com');                //Name is optional
                // $mail->addAddress('albaseetcompany8422@gmail.com', 'Ebrahem Sai');     //Add a recipient
                // $mail->addReplyTo('iebrahemsai944@gmail.com', 'Information 123');
                // $mail->addCC('cc@example.com');  
                // $mail->addBCC('bcc@example.com');
    
                // Attachments
                // $mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
                // $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name
                
                $authToken      = $this->randomDigits(6);
                $code           = $this->randomDigits(6);  
                $tkn = [
                    "code" => $code,
                    "exp"  => \Carbon::now()->addMinutes(3)->timestamp
                ];
                $token          = JWT::encode($tkn, $authToken, 'HS256');
                Config::set('database.connections.mysql.database', "izocloud");
                DB::purge('mysql');
                DB::reconnect('mysql');
                $user_request   = SupportActivate::where('email',$email_sender)->first();
                if(empty($user_request)){
                    $user_request                         = new SupportActivate();
                    $user_request->email                  = $email_sender;
                    $user_request->email_activation_code  = $code;
                    $user_request->email_activation_token = $token ;
                    $user_request->authkey                = $authToken ;
                    $user_request->save();
                }else{
                    $user_request->email_activation_code  = $code;
                    $user_request->email_activation_token = $token ;
                    $user_request->authkey                = $authToken ;
                    $user_request->update();
                }
                $izoCustomer   = IzoUser::where("email",request()->session()->get('user_main.email'))->first(); 
                $databaseName  = request()->session()->get('user_main.database') ;  
                Config::set('database.connections.mysql.database', $databaseName);
                DB::purge('mysql');
                DB::reconnect('mysql');
                
                
                // Content
                $mail->isHTML(true); //Set email format to HTML
                $mail->Subject = 'IZOCLOUD';
                
                $userAgent = $request->header('User-Agent');

                // Create an Agent instance and set the User-Agent
                $agent = new Agent();
                $agent->setUserAgent($userAgent);
        
                // Get the device name/type
                $device   = $agent->device();  // Generic device name
                $platform = $agent->platform(); // OS name
                $browser  = $agent->browser();  // Browser name
                
                // Get the user's IP address
                $ip       = $request->ip();
                // Create a new Guzzle client
                $client   = new Client();
                // Send a GET request to ipinfo.io API
                $response = $client->get('http://ipinfo.io/' . $ip . '/json');
                // Decode the JSON response
                $locationData = json_decode($response->getBody(), true);
                // Extract location information
                $city    = $locationData['city'] ?? 'Unknown';
                $region  = $locationData['region'] ?? 'Unknown';
                $country = $locationData['country'] ?? 'Unknown';
                
                $html = '<!DOCTYPE html>';
                $html .= '<html lang="en">';
                $html .= '<head>';
                $html .= '<meta charset="UTF-8">';
                $html .= '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
                $html .= '<title>ACTIVATION IZOCLOUD</title>';
                $html .= '</head>';
                $html .= '<header style="position:relative;padding: 10px;margin-bottom:80px;">';
                $html .= '<div class="logo" style="float:left;"><img  style="width:150px !important" class="logo-style"  height=50 src="https://agt.izocloud.com/public/uploads/logo.png" alt="logo"></div>';
                $html .= '<div class="contactus" style="float:right;">Need Help?<a href="#" style="color:#f7a62d;font-weight:bold">Contact Us</a></div>';
                $html .= '</header>';
                $html .= '<body style="margin:0% 10px">';
                $html .= '<br>';
                $html .= '<div class="cont" style="margin: 10px 5%;border: 0px solid black; font-size: 13px;font-family: arial;">';
                $html .= '<h1>Your one-time code is : '.$code.'</h1>';
                $html .= '<p>Device : '.$browser . " " . $platform .'</p>';
                $html .= '<p>Date : ' .   date("l")  . ' , ' .  date("d")  . '  ,' .   date("F")  . '   ' .  date("Y")   . ' ,  at : ' .   date("h:i:s a")  . ' ' .  $city . ' ' . $region . ' ' .  $country . '  </p>';
                $html .= '<p>Location : '.$region.'</p>';
                $html .= '<p>ip : '.$ip.'</p>'; 
                $html .= '</div>';
                $html .= '</body>'; 
                $html .= '<footer style="color:#3a3a3a;font-size: 13px;font-family: arial;margin:0% 10px;margin-top:40px;">';
                $html .= '<p>';
                $html .= ' Please don not replay to this email. Emails sent to this address will not be answered.';
                $html .= '</p>';
                $html .= '<p>';
                $html .= 'Copyright &copy; 2022 - '. date('Y') .' Alhmawi General Trading L.L.C , Dubai  , Abu Baker Al seddik metro station <br> <br> All rights reserved.';
                $html .= '</p>';
                $html .= '<p style="display:none">';
                $html .= '<b>ALHAMWI GENERAL TRADING L.L.C </b>'; 
                $html .= '<b><br>Website : izo.ae </b>';
                $html .= '<b><br>Customer Service : +971-56-777-9250  ,  +971-4-23-55-919</b>';
                $html .= '</p>';
                $html .= '</footer>';
                $html .= '</html>';

                $mail->Body   = $html; 
                
                // dd($mail,$user_request,$tkn,$code,$token);

                // $mail->AltBody = 'MAKE Activation For this version v2.2 from IZO-POS Application';
         
                if($mail->send()){

                    return response()->json([
                        'success' => 1,
                        'msg'     => __("Sending Code Successfully, Please Check Your Email"),
                        ]) ;
                }else{

                    return response()->json([
                        'success' => 0,
                        'msg'     => __("Some Thing Went Wrong"),
                        ]) ;
                    } 
            } catch (Exception $e) {
                return response()->json([
                    'success' => 0,
                    'msg'     => __("Some Thing Went Wrong"),
                ]) ;
            }

        }
       
    }
    
    public function randomDigits($length = 6) {
        $digits = '';
        for ($i = 0; $i < $length; $i++) {
            $digits .= mt_rand(0, 9);
        }
        return $digits;
    }

    public function checkEmailCodeActivate(Request $request){
        if(request()->ajax()){
            try{
                $email_sender = request()->input('email');
                $code         = request()->input('code');
                Config::set('database.connections.mysql.database', "izocloud");
                DB::purge('mysql');
                DB::reconnect('mysql');
                $user_request   = SupportActivate::where('email',$email_sender)->first();
                if($user_request){
                    if($code == $user_request->email_activation_code){
                        $token           = $user_request->email_activation_token;
                        $data            = JWT::decode($token,$user_request->authkey,'HS256');
                        $timestamp       = $data->exp;  // Your Unix timestamp
                        // Convert the Unix timestamp to a Carbon instance
                        $timestampCarbon = \Carbon::createFromTimestamp($timestamp);
                        // Get the current date and time
                        $currentDateTime = \Carbon::now();
                        // Compare the two dates
                        if ($timestampCarbon->isBefore($currentDateTime)) {
                            return response()->json([
                                "success" => 0, 
                                "msg"     => __('failed expire')
                            ]) ;
                        }  
                        return response()->json([
                            "success" => 1, 
                            "msg"     => __('success'),
                        ]) ;
                    }else{
                        $izoCustomer   = IzoUser::where("email",request()->session()->get('user_main.email'))->first(); 
                        $databaseName  = request()->session()->get('user_main.database') ;  
                        Config::set('database.connections.mysql.database', $databaseName);
                        DB::purge('mysql');
                        DB::reconnect('mysql');
                        return response()->json([
                            "success" => 0, 
                            "msg"     => __('failed'),
                        ]) ;
                    }
                }else{
                     
                    return response()->json([
                            "success" => 0, 
                            "msg"     => __('failed'),
                    ]) ;
                }
            }catch(Exception $e){
                $putOut = [
                    "success" => 0,
                    "msg"     => __('failed'),
                ];
                return $outPut ;
            }
        }
    }
    
    

    
}
