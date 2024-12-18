<?php

namespace App\Http\Controllers\ApiController;

use App\Http\Controllers\Controller;
use App\Utils\BusinessUtil;
use DB;
use Illuminate\Validation\Rule;
use App\Utils\ModuleUtil;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Spatie\Activitylog\Models\Activity;
use Laravel\Sanctum\Contracts;
use Illuminate\Session\Store;
use Utils\Util;
use Illuminate\Support\Str;
use App\Models\User;
 
// use App\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\FrontEnd\Utils\GlobalUtil;
use App\SellingPriceGroup;

class ApiLoginController extends Controller
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
        $this->businessUtil = $businessUtil;
        $this->moduleUtil = $moduleUtil;
    }
    /** ----------------------------------------------------------  **/
    /**                     login button                            **/
    /** ----------------------------------------------------------  **/
    public function login(Request $request)
    {
      
       $user = User::where("username",$request->username)->first();

       $credentials = $request->only('username', 'password');
        
        if(!$user || !Hash::check($request->password,$user->password)){
            return response([
                "status"  => "403",
                "message" => "Invalid",
                "result"  => 0
            ],404);
        }
        $credentialsnt        =  $request->only(["username","password"]);
        $token                =  Auth::guard('api')->attempt($credentialsnt);
        $user                 =  User::find($user->id);
     
        
              
        $user->api_token  = $token;
        $user->update();
        // $authToken = Str::random(40);
        // $token     = JWT::encode($User->toArray(), $authToken, 'HS256');
        return response()->json([
            'status'   => 'success',
             'authorisation' => [
                'token' => $token,
                'type'  => 'Bearer',
            ]
        ]);
 
    }
    /** ----------------------------------------------------------  **/
    /**                     Logout button  react                    **/
    /** ----------------------------------------------------------  **/
    public function loginFront(Request $request)
    {
        $connection = \App\Models\FrontendConnection::first();
        if(!empty($connection)){
            if($connection->api == null){
                return response([
                    "status"            => 403,
                    "login_first_time"  => true,
                    "authorization"     => [
                        "token"         => null,
                        "success"       => false,
                        "type"          => null,
                        "user"          => null
                    ],
                    "api_url"           => null
                ],403);
            }
        }else{
            return response([
                "status"                => 403,
                "login_first_time"      => true,
                "authorization"         => [
                    "token"             => null,
                    "success"           => false,
                    "type"              => null,
                    "user"              => null
                ],
                "api_url"=>null
            ],403);
        }
       $user = User::where("username",$request->username)->where("allow_login",1)->first();
      
        if($user){
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
        }
        $item           = [
                            "username"  => $request->username,
                            "password"  => $request->password,
                            "random"    => Str::random(10)
                            ]  ;
        $device         = $request->header('User-Agent');
        $ip             = $request->ip();
        $pay            = json_encode($item);
        $id             = hash('sha1', $pay);
        $payload        = base64_encode(hash('sha256', $pay ,true));
        $time           = \Carbon::now()->timestamp;
        
        $credentials = $request->only('username', 'password');
        
        if(!$user || !Hash::check($request->password,$user->password)){
            return response([
                "status"  => "403",
                "message" => "Invalid",
                "result"  => 0
            ],404);
        }
        $user_data                   =  User::where("username",$request->username)->where("allow_login",1)->first();
        $credentialsNt               =  $request->only(["username","password"]);
        $token                       =  Auth::guard('web')->attempt($credentialsNt);
        $tokenApi                    =  Auth::guard('api')->attempt($credentialsNt);
        if($token == true){ 
            $session                 = new \App\Models\SessionTable();
            $session->id             = $id;
            $session->user_id        = $user->id;
            $session->ip_address     = $ip;
            $session->user_agent     = $device;
            $session->payload        = $payload;
            $session->last_activity  = $time ;
            $session->save();
        }
         // ... JWT secret Key
        $user                 =  User::where("id",$user->id)->select("id","username","api_token")->first();
        $secret               =  "izo-" . $request->password . $request->username ;
        $tokenSecret          =  JWTAuth::claims(['secret_k' => $secret ])->fromUser($user);

        # go to the source from erp system main roles name ;
        $array_compare = [
            "View User"                                 =>"user.view",
            "Create User"                               =>"user.create",
            "Edit User"                                 =>"user.update",
            "Delete User"                               =>"user.delete",
            "View Role"                                 =>"roles.view",
            "Create Role"                               =>"roles.create",
            "Edit Role"                                 =>"roles.update",
            "Delete Role"                               =>"roles.delete",
            "View Supplier"                             =>"supplier.view",
            "View Statement Supplier"                   =>"supplier.view_own",
            "Ledger Supplier"                           =>"supplier.ledger",
            "Create Supplier"                           =>"supplier.create",
            "Edit Supplier"                             =>"supplier.update",
            "Delete Supplier"                           =>"supplier.delete",
            "Pay Supplier"                              =>"supplier.pay",
            "Deactivate Supplier"                       =>"supplier.deactivate",
            "View Purchase Supplier"                    =>"supplier.purchase",
            "Supplier Stock Report"                     =>"supplier.stock",
            "Document & Note Supplier"                  =>"supplier.document",
            "View Customer"                             =>"customer.view",
            "View Statement Customer"                   =>"customer.view_own",
            "Ledger Customer"                           =>"customer.ledger",
            "Create Customer"                           =>"customer.create",
            "Edit Customer"                             =>"customer.update",
            "Delete Customer"                           =>"customer.delete",
            "Pay Customer"                              =>"customer.pay",
            "Deactivate Customer"                       =>"customer.deactivate",
            "View Sales Customer"                       =>"customer.sales",
            "Customer Stock Report"                     =>"customer.stock",
            "Document & Note Customer"                  =>"customer.document",
            "View Customer Group"                       =>"customer_group.view",
            "Create Customer Group"                     =>"customer_group.create",
            "Edit Customer Group"                       =>"customer_group.update",
            "Delete Customer Group"                     =>"customer_group.delete",
            "Download Import Contacts"                  =>"contact.import",
            "Submit Import Contacts"                    =>"contact.submit",
            "View Product"                              =>"product.view",
            "Create Product"                            =>"product.create",
            "Edit Product"                              =>"product.update",
            "Delete Product"                            =>"product.delete",
            "Product Labels"                            =>"product.labels",
            "Product Add Barcode"                       =>"product.add_barcode",
            "Label Barcode"                             =>"product.label_barcode",
            "Product Add Opening Stock"                 =>"product.add_opening",
            //    .................................................................***
            "Product View Stock"                        =>"product.view_sStock",
            "Product Cost"                              =>"product.avarage_cost",
            //    .................................................................***
            "Product History"                           =>"product.history",
            "Duplicate Product"                         =>"product.duplicate",
            "Download Product Brochure"                 =>"product.download_brochure",
            "Download Import Products"                  =>"product.import",
            "Submit Import Products"                    =>"product.submit",
            "View Variation"                            =>"variation.view",
            "Create Variation"                          =>"variation.create",
            "Edit Variation"                            =>"variation.update",
            "Delete Variation"                          =>"variation.delete",
            "View Opening Stock"                        =>"opening_stock.view",
            "Create Opening Stock"                      =>"opening_stock.create",
            "Edit Opening Stock"                        =>"opening_stock.update",
            "Delete Opening Stock"                      =>"opening_stock.delete",
            "Download Import Opening Stock"             =>"opening_stock.import",
            "Submit Import Opening Stock"               =>"opening_stock.submit",
            "View Sales Price Group"                    =>"sales_price_group.view",
            "Create Sales Price Group"                  =>"sales_price_group.create",
            "Edit Sales Price Group"                    =>"sales_price_group.update",
            "Delete Sales Price Group"                  =>"sales_price_group.delete",
            "Deactivate Sales Price Group"              =>"sales_price_group.deactivate",
            "Download Import Sales Price Group"         =>"sales_price_group.import",
            "Submit Import Sales Price Group"           =>"sales_price_group.submit",
            "View Unit"                                 =>"unit.view",
            "Create Unit"                               =>"unit.create",
            "Edit Unit"                                 =>"unit.update",
            "Delete Unit"                               =>"unit.delete",
            "Default Unit"                              =>"unit.default",
            "View Category"                             =>"category.view",
            "Create Category"                           =>"category.create",
            "Edit Category"                             =>"category.update",
            "Delete Category"                           =>"category.delete",
            "View Brand"                                =>"brand.view",
            "Create Brand"                              =>"brand.create",
            "Edit Brand"                                =>"brand.update",
            "Delete Brand"                              =>"brand.delete",
            "View Warranty"                             =>"warranty.view",
            "Create Warranty"                           =>"warranty.create",
            "Edit Warranty"                             =>"warranty.update",
            "Delete Warranty"                           =>"warranty.delete",
            "View Recipe"                               =>"manufacturing.access_recipe",
            "Create Recipe"                             =>"manufacturing.add_recipe",
            "Edit Recipe"                               =>"manufacturing.edit_recipe",
            "Delete Recipe"                             =>"manufacturing.delete_recipe",
            "View Production"                           =>"manufacturing.access_production",
            "Create Production"                         =>"manufacturing.add_production",
            "Edit Production"                           =>"manufacturing.edit_production",
            "Delete Production"                         =>"manufacturing.delete_production",
            "Entry Production"                          =>"manufacturing.entry_production",
            "Manufacturing Report Inventory Report"     =>"manufacturing.inventory_production",
            "Manufacturing Report Items Report"         =>"manufacturing.report_items_production",
            "View Purchase"                             =>"purchase.view",
            "Create Purchase"                           =>"purchase.create",
            "Edit Purchase"                             =>"purchase.update",
            "Delete Purchase"                           =>"purchase.delete",
            "Entry Purchase"                            =>"purchase.entry",
            // .................................................................**
            "Product Qty Purchase"                      =>"purchase.porduct_qty_setting",
            "Edit Composeit Discount Purchase"          =>"purchase.edit_composeit_discount",
            "Price Of Purchase"                         =>"view_purchase_price",
            // .................................................................**
            "Map Purchase"                              =>"purchase.map",
            "View Payment Purchase"                     =>"purchase.payments",
            "Add Payment Purchase"                      =>"purchase_payment.create",
            "Edit Payment Purchase"                     =>"purchase_payment.edit",
            "Delete Payment Purchase"                   =>"purchase_payment.delete",
            "Return Purchase"                           =>"purchase.purchase_return",
            "Update Status"                             =>"purchase.update_status",
            "Print Purchase"                            =>"purchase.print",
            "New Order Notification Purchase"           =>"purchase.note",
            // ................................................................**
            "View Own Purchase Purchases Return"        =>"view_own_purchase",
            // ................................................................**
            "View Purchases Return"                     =>"purchase_return.view",
            "Create Purchases Return"                   =>"purchase_return.create",
            "Edit Purchases Return"                     =>"purchase_return.update",
            "Delete Purchases Return"                   =>"purchase_return.delete",
            "View Payment Purchase Return"              =>"purchase_return.payments",
            "Print Purchase Return"                     =>"purchase_return.print",
            "Entry Purchase Return"                     =>"purchase_return.entry",
            "Add Payment Purchase Return"               =>"purchase_return_payment.create",
            "Edit Payment Purchase Return"              =>"purchase_return_payment.update",
            "Delete Payment Purchase Return"            =>"purchase_return_payment.delete",
            "View Map"                                  =>"map.view",
            "View Sales"                                =>"sell.view",
            "Create Sales"                              =>"sell.create",
            "Edit Sales"                                =>"sell.update",
            "Delete Sales"                              =>"sell.delete",
            "Entry Sales"                               =>"sell.entry",
            "Map Sales"                                 =>"sell.map",
            "Add Payment Sales"                         =>"sell_payment.create",
            "Edit Payment Sales"                        =>"sell_payment.edit",
            "Delete Payment Sales"                      =>"sell_payment.delete",
            "View Payment Sales"                        =>"sell.payments",
            "Return Sales"                              =>"sell.sells_return",
            "Duplicate Sales"                           =>"sell.duplicate",
            "Print Sales"                               =>"sell.print",
            "Packing list Sales"                        =>"sell.packing_list",
            "Invoice Url Sales"                         =>"sell.invoice_url",
            "View Delivered"                            =>"sell.view_delivered",
            "View Approved"                             =>"sell.view_approve",
            "Create Approved"                           =>"sell.create_approve",
            "Edit Approved"                             =>"sell.edit_approve",
            "Delete Approved"                           =>"sell.delete_approve",
            "Print Approved"                            =>"sell.print_approve",
            "Convert To Invoice"                        =>"sell.convert_to_invoice",
            "List Quotation"                            =>"list_quotations",
            "View Quotation"                            =>"sell.view_quotation",
            "Create Quotation"                          =>"sell.create_quotation",
            "Edit Quotation"                            =>"sell.edit_quotation",
            "Delete Quotation"                          =>"sell.delete_quotation",
            "Print Quotation"                           =>"sell.print_quotation",
            "Quotation Url Quotation"                   =>"sell.quotation_url",
            "New Quotation Notifications Quotation"     =>"sell.quotation_notes",
            "Convert To Approved Quotation"             =>"sell.convert_to_approve",
            "List Draft"                                =>"list_drafts",
            "View Draft"                                =>"sell.view_draft",
            "Create Draft"                              =>"sell.create_draft",
            "Edit Draft"                                =>"sell.edit_draft",
            "Delete Draft"                              =>"sell.delete_draft",
            "Print Draft"                               =>"sell.print_draft",
            "Convert To Quotation"                      =>"sell.convert_to_quotation",
            // .......................................................................***
            "View Own Sell Only"                        =>"view_own_sell_only",
            // .......................................................................***
            "View Sale Return"                          =>"access_sell_return",
            "Create Sale Return"                        =>"sell_return.create",
            "Edit Sale Return"                          =>"sell_return.update",
            "Delete Sale Return"                        =>"sell_return.delete",
            "Print Sale Return"                         =>"sell_return.print",
            "View Payment Sale Return"                  =>"sell_return.payments",
            "Add Payment Sale Return"                   =>"sell_return_payment.create",
            "Edit Payment Sale Return"                  =>"sell_return_payment.update",
            "Delete Payment Sale Return"                =>"sell_return_payment.delete",
            "View Delivered Sale Return"                =>"sell_return.view_delivered",
            "Download Import Sales"                     =>"sell.import",
            "Submit Import Sales"                       =>"sell.submit",
            "View Quotation Terms"                      =>"quotation_term.view",
            "Create Quotation Terms"                    =>"quotation_term.create",
            "Edit Quotation Terms"                      =>"quotation_term.update",
            "Delete Quotation Terms"                    =>"quotation_term.delete",
            "View Voucher"                              =>"payment_voucher.view",
            "Create Voucher"                            =>"payment_voucher.create",
            "Edit Voucher"                              =>"payment_voucher.update",
            "Delete Voucher"                            =>"payment_voucher.delete",
            "Attachment Voucher"                        =>"payment_voucher.attachment",
            "Entry Voucher"                             =>"payment_voucher.entry",
            "Print Voucher"                             =>"payment_voucher.print",
            "View Journal Voucher"                      =>"daily_payment.view",
            "Create Journal Voucher"                    =>"daily_payment.create",
            "Edit Journal Voucher"                      =>"daily_payment.update",
            "Delete Journal Voucher"                    =>"daily_payment.delete",
            "Attachment Journal Voucher"                =>"daily_payment.attachment",
            "Entry Journal Voucher"                     =>"daily_payment.entry",
            "Print Journal Voucher"                     =>"daily_payment.print",
            "View Expense Voucher"                      =>"gournal_voucher.view",
            "Create Expense Voucher"                    =>"gournal_voucher.create",
            "Edit Expense Voucher"                      =>"gournal_voucher.update",
            "Delete Expense Voucher"                    =>"gournal_voucher.delete",
            "Attachment Expense Voucher"                =>"gournal_voucher.attachment",
            "Entry Expense Voucher"                     =>"gournal_voucher.entry",
            "Print Expense Voucher"                     =>"gournal_voucher.print",
            "View Cheques"                              =>"cheque.view",
            "Create Cheques"                            =>"cheque.create",
            "Edit Cheques"                              =>"cheque.update",
            "Delete Cheques"                            =>"cheque.delete",
            "Collect Cheques"                           =>"cheque.collect",
            "UnCollect Cheques"                         =>"cheque.uncollect",
            "Refund Cheques"                            =>"cheque.refund",
            "Delete Collect Cheques"                    =>"cheque.delete_collect",
            "Attachment Cheques"                        =>"cheque.attachment",
            "Entry Cheques"                             =>"cheque.entry",
            "Print Cheques"                             =>"cheque.print",
            "View Contact Bank"                         =>"contact_bank.view",
            "Create Contact Bank"                       =>"contact_bank.create",
            "Edit Contact Bank"                         =>"contact_bank.update",
            "Delete Contact Bank"                       =>"contact_bank.delete",
            "View Store"                                =>"warehouse.view",
            "Create Store"                              =>"warehouse.create",
            "Edit Store"                                =>"warehouse.update",
            "Delete Store"                              =>"warehouse.delete",
            // .................................................................#####
            "View Stores Transfers"                     =>"stock_transfer.view",
            "Create Stores Transfers"                   =>"stock_transfer.create",
            "Edit Stores Transfers"                     =>"stock_transfer.update",
            "Delete Stores Transfers"                   =>"stock_transfer.delete",
            "Print Stores Transfers"                    =>"stock_transfer.print",
            "Create Pending Stores Transfers"           =>"stock_transfer.create_pending",
            "Create Confirmed Stores Transfers"         =>"stock_transfer.create_confirmed",
            "Create In Transit Stores Transfers"        =>"stock_transfer.create_in_transit",
            "Create Completed Stores Transfers"         =>"stock_transfer.create_completed",
            "Show Qty Available Stores Transfers"       =>"stock_transfer.show_qty_available",
            "Change Status Stores Transfers"            =>"stock_transfer.changeStatus",
            "Products Stores Transfers"                 =>"stock_transfer.changeStatus",
            "Delete From Stock Tracking Store Transfers"=>"stock_transfer.delete_form_stocktacking",
            "Report Stores Transfers"                   =>"stock_transfer.report",
            "Liquidation Stores Transfers"              =>"stock_transfer.liquidation",
            // .................................................................#####
            "Ledger Cash"                               =>"cash.ledger",
            "Create Cash"                               =>"cash.create",
            "Edit Cash"                                 =>"cash.update",
            "Close Cash"                                =>"cash.close",
            "ledger Bank"                               =>"bank.ledger",
            "Create Bank"                               =>"bank.create",
            "Edit Bank"                                 =>"bank.update",
            "Close Bank"                                =>"bank.close",
            "ledger Account"                            =>"account.ledger",
            "Create Account"                            =>"account.create",
            "Edit Account"                              =>"account.update",
            "Close Account"                             =>"account.close",
            "Print Balance Sheet"                       =>"account.balance_sheet",
            "Print Trial Balance"                       =>"account.trial_balance",
            "Entry Entries"                             =>"entry.all",
            "Movement Cost Center"                      =>"cost_center.movement",
            "Create Cost Center"                        =>"cost_center.create",
            "Edit Cost Center"                          =>"cost_center.update",
            "Delete Cost Center"                        =>"cost_center.delete",
            "View Sale Payment Report"                  =>"sell_payment_report.view",
            "View Purchase Payment Report"              =>"purchase_payment_report.view",
            "View Business Location"                    =>"business_location.view",
            "Settings Business Location"                =>"business_location.setting",
            "Create Business Location"                  =>"business_location.create",
            "Edit Business Location"                    =>"business_location.edit",
            "Deactivate Business Location"              =>"business_location.deactivate",
            "View Pattern"                              =>"pattern.view",
            "Create Pattern"                            =>"pattern.create",
            "Edit Pattern"                              =>"pattern.update",
            "Delete Pattern"                            =>"pattern.delete",
            "View System Account"                       =>"system_account.view",
            "Create System Account"                     =>"system_account.create",
            "Edit System Account"                       =>"system_account.edit",
            "Delete System Account"                     =>"system_account.delete",
            "View Asset"                                =>"assets.view",
            "Create Asset"                              =>"assets.create",
            "Edit Asset"                                =>"assets.edit",
            "Delete Asset"                              =>"assets.delete",
            "View Partner"                              =>"Partners.view",
            "Create Partner"                            =>"partner.create",
            "Edit Partner"                              =>"partner.edit",
            "Delete Partner"                            =>"partner.delete",
            "Create Partner Payment history"            =>"partner.payment_create",
            "Edit Partner Payment history"              =>"partner.payment_edit",
            "View Partner Payment history"              =>"partner.payment_view",
            "Delete Partner Payment history"            =>"partner.payment_delete",
            "Create Final Accounts"                     =>"partner.final_account_create",
            "Edit Final Accounts"                       =>"partner.final_account_edit",
            "View Final Accounts"                       =>"partner.final_account_view",
            "Delete Final Accounts"                     =>"partner.final_account_delete",
            "Final Accounts Distribute"                 =>"partner.final_account_distribute",
        ]; 

        $business_id                  = $user->business_id;
        $selling_price_groups         = SellingPriceGroup::where('business_id', $business_id)->active()->get();

        $sales_price_group   = [];
        $sales_price_group[] = "access_default_selling_price";
        foreach($selling_price_groups as $onePrice){
            $sales_price_group[]= $onePrice->name;
        }
       $old_role_permissions_all    = [];
       $old_role_permissions_sidRol = [];
       $old_role_permissions_sidBar = [];
       $old_role_permissions_Action = [];
       $old_role_permissions_Tables = [];
       $old_role_permissions_Others = [];
       $list_sell_price_group       = [];
       $permissions = [];

        $sectionsTitle = [
                        "Users",
                        "Products",
                        "Contacts",
                        "Vouchers",
                        "Cash and Bank",
                        "Accounts",
                        "Settings",
                        "Assets",
                        "Partners",
                        "Maintenance Services",
                        "Installment",
                        "Restaurants",
                        "Notifications",
                        "Projects",
                        "HRM",
                        "Essentials",
                        "User Activation",
                        "CRM",
                        "E-commerce"
        ];

        if($user_data->roles->first()){
            $role = Role::where('roles.id', $user_data->roles->first()->id)->with('permissions')->first();

            $allData =  $role->permissions ;
            foreach($allData as $item){
                $permissions[] = $item->name;
            }

            foreach ($role->permissions as $role_perm) {
               
                if(in_array($role_perm->name,$sales_price_group)){
                    $list_sell_price_group[] = $role_perm->name;
                }
                $nameOfRoles =  substr($role_perm->name,7,strlen($role_perm->name));
                $prefix      =  substr($role_perm->name,0,7);

                if($prefix == "Tables."){
                    $Role_name = $nameOfRoles;
                    foreach($array_compare as $idKey => $valueRole){
                        if($valueRole == $nameOfRoles){
                            $Role_name = $idKey;
                            break;
                        }
                    }
                    $old_role_permissions_Tables[] = $Role_name ;
                }elseif($prefix == "Action."){
                    $Role_name = $nameOfRoles;
                    foreach($array_compare as $idKey => $valueRole){
                        if($valueRole == $nameOfRoles){
                            $Role_name = $idKey;
                            break;
                        }
                    }
                    $old_role_permissions_Action[] = $Role_name ;
                }elseif($prefix == "sidBar."){
                    $Role_name = $nameOfRoles;
                    foreach($array_compare as $idKey => $valueRole){
                        if($valueRole == $nameOfRoles){
                            $Role_name = $idKey;
                            break;
                        }
                    }
                    $old_role_permissions_sidBar[] = $Role_name ;
                }elseif($prefix == "sidRol."){
                     $Role_name = $nameOfRoles;
                    foreach($array_compare as $idKey => $valueRole){
                        if($valueRole == $nameOfRoles){
                            $Role_name = $idKey;
                            break;
                        }
                    }
                    $old_role_permissions_sidRol[] = $Role_name ;  
                }else{
                    if(in_array($role_perm->name,$array_compare)){
                        foreach($array_compare as $keyValue => $oneValue ){
                            if($role_perm->name == $oneValue){
                                $old_role_permissions_Action[] = $keyValue;
                            }
                        } 
                    }else{
                        $old_role_permissions_Others[] = $role_perm->name;
                    }
                }
                
            }

            $old_role_permissions_all['sidebar'] = $old_role_permissions_sidBar;
            $old_role_permissions_all['sideRole']= $old_role_permissions_sidRol;
            $old_role_permissions_all['action']  = $old_role_permissions_Action;
            $old_role_permissions_all['table']   = $old_role_permissions_Tables;
            $old_role_permissions_all['other']   = $old_role_permissions_Others;

            $boardSideBar = [];
            $sideBarObject = [];
            $compare_index = 0;
            foreach($old_role_permissions_all['sideRole'] as $k => $side){
                switch ($side) {
                    case "Dashboard" :
                        $list            = [
                                            "E-commerce"     => "/dashboards/ecommerce",
                                            "Analytics"      => "/dashboards/analytics",
                                            "CRM Analytics"  => "/dashboards/crm"
                                        ] ;
                        $children        = [];
                        $index           =  0;
                        foreach($list as $t => $p ){
                            $children[] = [
                                "title" => $t,
                                "path"  => $p,
                            ];
                        }
                        $badContent      = "New";
                        $badgeColor      = "Success";
                        $path            = "" ;
                        $icons           = 'fluent-mdl2:contact-card-settings-mirrored';  break;
                    case "Users" : 
                        $boardSideBar[1] = [
                            "sectionTitle" =>  " SEC - Users"  
                        ];
                        $list            = [ 
                                            "List Users" => "/users/list"
                                         ] ;
                        $children        = [];
                        $index           =  2;
                        foreach($list as $t => $p ){
                            $children[] = [
                                "title" => $t,
                                "path"  => $p,
                            ];
                        }
                        $badContent      = "";
                        $badgeColor      = "";
                        $path            = "" ;
                        $icons           = 'solar:user-id-bold';  break;
                    case "Contacts" : 
                        $boardSideBar[4] = [
                            "sectionTitle" =>  " SEC - Contacts"  
                        ];
                        $list            = [
                                            "Suppliers"       => "/contact/supplier",
                                            "Customers"       => "/contact/customers",
                                            "Customer Groups" => "/contact/customer-group",
                                            "Import Contact"  => "/contact/import"
                                        ] ;
                        $children        = [];
                        $index           =  5;
                        foreach($list as $t => $p ){
                            $children[] = [
                                "title" => $t,
                                "path"  => $p,
                            ];
                        }
                        $badContent      = "";
                        $badgeColor      = "";
                        $path            = "" ;
                        $icons           = 'bxs:contact';  break;
                    case "Products" : 
                        $boardSideBar[8] = [
                            "sectionTitle" =>  " SEC - Products"  
                        ];
                        $list            = [
                                            "List Products"          => "/product/list",
                                            "Variations"             => "/product/variations/list",
                                            "Units"                  => "/product/units/list",
                                            "Categories"             => "/product/categories/list",
                                            "Brands"                 => "/product/brands/list",
                                            "Warranties"             => "/product/warranties/list",
                                            "Import Product"         => "/product/import",
                                            "Sales Price Group"      => "/product/import",
                                            "Opening Stock"          => "/product/opening-stock/list",
                                            "Import Opening Stock"   => "/product/opening-stock/import",
                                            "Products Gallery"       => "/product/gallery",
                                        ] ;
                        $children        = [];
                        $index           =  9;
                        foreach($list as $t => $p ){
                            $children[] = [
                                "title" => $t,
                                "path"  => $p,
                            ];
                        }
                        $badContent      = "";
                        $badgeColor      = "";
                        $path            = "" ;
                        $icons           = 'bxs:package';  break;
                   case "Manufacturing" : 
                        $list            = [
                                            "Recipe"                 => "/recipe/list",
                                            "Production"             => "/production/list",
                                            "Manufacturing Report"   => "/manufacturing/report",
                                        ] ;
                        $children        = [];
                        $index           =  13;
                        foreach($list as $t => $p ){
                            $children[] = [
                                "title" => $t,
                                "path"  => $p,
                            ];
                        }
                        $badContent      = "";
                        $badgeColor      = "";
                        $path            = "" ;
                        $icons           = 'ic:sharp-factory';  break;
                    case "Purchases" : 
                        $list            = [
                                            "Purchases"          => "/purchases/list",
                                            "Purchases Return"   => "/purchases/return/list",
                                            "Map"                => "/map/report",
                                        ] ;
                        $children        = [];
                        $index           =  6;
                        foreach($list as $t => $p ){
                            $children[] = [
                                "title" => $t,
                                "path"  => $p,
                            ];
                        }
                        $badContent      = "";
                        $badgeColor      = "";
                        $path            = "" ;
                        $icons           = 'bxs:purchase-tag-alt';  break;
                    case "Sales" : 
                        $list            = [
                                            "Sales"                   => "/sales/list",
                                            "Approved Quotation"      => "/approved-quotation/list",
                                            "Quotation"               => "/quotation/list",
                                            "Draft"                   => "/draft/list",
                                            "Sales Commission Agent"  => "/sales-commission-agent/list",
                                            "Quotation Terms"         => "/quotation-term/list",
                                            "Import Sales"            => "/sales/import",
                                        ] ;
                        $children        = [];
                        $index           =  7;
                        foreach($list as $t => $p ){
                            $children[] = [
                                "title" => $t,
                                "path"  => $p,
                            ];
                        }
                        $badContent      = "";
                        $badgeColor      = "";
                        $path            = "" ;
                        $icons           = 'bxs:purchase-tag';  break;
                    case "Vouchers" : 
                        $boardSideBar[14] = [
                            "sectionTitle" =>  " SEC - Vouchers"  
                        ];
                        $list            = [
                                            "Vouchers"                => "/vouchers/list",
                                            "Receipt Voucher"         => "/voucher/receipt/list",
                                            "Payment Voucher"         => "/vouchers/payment/list",
                                            "Journal Voucher"         => "/vouchers/journal/list",
                                            "Expense Voucher"         => "/vouchers/expense/list",
                                        ] ;
                        $children        = [];
                        $index           =  15;
                        foreach($list as $t => $p ){
                            $children[] = [
                                "title" => $t,
                                "path"  => $p,
                            ];
                        }
                        $badContent      = "";
                        $badgeColor      = "";
                        $path            = "" ;
                        $icons           = 'basil:edit-solid';  break;
                    case "Cheques" : 
                        $list            = [
                                            "Cheques"            => "/cheques/list",
                                            "Cheque In"          => "/cheques-in/create",
                                            "Cheque Out"         => "/cheques-out/create",
                                            "Contact Bank"       => "/contact-bank/list",
                                        ] ;
                        $children        = [];
                        $index           =  16;
                        foreach($list as $t => $p ){
                            $children[] = [
                                "title" => $t,
                                "path"  => $p,
                            ];
                        }
                        $badContent      = "";
                        $badgeColor      = "";
                        $path            = "" ;
                        $icons           = 'icon-park-solid:bank-card-one';  break;
                    case "Store" : 
                        $list            = [
                                            "Stores"             => "/stores/list",
                                            "Stores Movements"   => "/stores/movement",
                                            "Stores Transfer"    => "/stores/transfer",
                                            "Received"           => "/stores/received",
                                            "Delivered"          => "/stores/delivered",
                                        ] ;
                        $children        = [];
                        $index           =  10;
                        foreach($list as $t => $p ){
                            $children[] = [
                                "title" => $t,
                                "path"  => $p,
                            ];
                        }
                        $badContent      = "";
                        $badgeColor      = "";
                        $path            = "" ;
                        $icons           = 'ic:round-warehouse';  break;
                    case "Cash and Bank" : 
                        $boardSideBar[17] = [
                            "sectionTitle" =>  " SEC - Cash and Bank"  
                        ];
                        $list            = [
                                            "Cash List"        => "/accounts/cash/list",
                                            "Bank List"        => "/accounts/bank/list",
                                        ] ;
                        $children        = [];
                        $index           =  18;
                        foreach($list as $t => $p ){
                            $children[] = [
                                "title" => $t,
                                "path"  => $p,
                            ];
                        }
                        $badContent      = "";
                        $badgeColor      = "";
                        $path            = "" ;
                        $icons           = 'ic:sharp-money';  break;
                    case "Accounts" : 
                        $boardSideBar[19] = [
                            "sectionTitle" =>  " SEC - Accounts"  
                        ];
                        $list            = [
                                            "List Accounts"     => "/accounts/list",
                                            "List Entries"      => "/accounts/entries/list",
                                            "List Cost Center"  => "/accounts/cost-center/list",
                                            "Trial Balance"     => "/accounts/trial-balance/report",
                                            "Balance Sheet"     => "/accounts/balance-sheet/report",
                                            "Cash Flow"         => "/accounts/cash-flow/report",
                                        ] ;
                        $children        = [];
                        $index           =  20;
                        foreach($list as $t => $p ){
                            $children[] = [
                                "title" => $t,
                                "path"  => $p,
                            ];
                        }
                        $badContent      = "";
                        $badgeColor      = "";
                        $path            = "" ;
                        $icons           = 'ic:round-account-balance';  break;
                    case "Reports" : 
                        $list            = [
                                            "Profit/Loss Report"          => "/reports/profit-loss",
                                            "Purchase & Sales Report"     => "/reports/purchase-sales",
                                            "Product Sales Day Report"    => "/reports/product-sale-day",
                                            "Tax Report"                  => "/reports/tax",
                                            "Supplier Customer Report"    => "/reports/supplier-customer-group",
                                            "Customer Group Report"       => "/reports/customer-group",
                                            "Inventory Report"            => "/reports/inventory",
                                            "Stock Adjustment Report"     => "/reports/stock-adjustment",
                                            "Trending Product Report"     => "/reports/trending-product",
                                            "Item Report"                 => "/reports/item-report",
                                            "Product Purchase Report"     => "/reports/product-purchase",
                                            "Product Sale Report"         => "/reports/product-sale",
                                            "Purchase Payment Report"     => "/reports/purchase-payment",
                                            "Sale Payment Report"         => "/reports/sale-payment",
                                            "Expense Report"              => "/reports/expense",
                                            "Register Report"             => "/reports/register",
                                            "Sales Representative Report" => "/reports/sale-representative",
                                            "Activity Log"                => "/reports/activity-log",
                                        ] ;
                        $children        = [];
                        $index           =  21;
                        foreach($list as $t => $p ){
                            $children[] = [
                                "title" => $t,
                                "path"  => $p,
                            ];
                        }
                        $badContent      = "";
                        $badgeColor      = "";
                        $path            = "" ;
                        $icons           = 'ic:round-report';  break;
                    case "Patterns" : 
                        $list            = [
                                            "Define Patterns"      => "/pattern/list",
                                            "System Accounts"      => "/pattern/list",
                                            "Business Locations"   => "/business-location/list",
                                        ] ;
                        $children        = [];
                        $index           =  22;
                        foreach($list as $t => $p ){
                            $children[] = [
                                "title" => $t,
                                "path"  => $p,
                            ];
                        }
                        $badContent      = "";
                        $badgeColor      = "";
                        $path            = "" ;
                        $icons           = 'eos-icons:patterns';  break;
                    case "Settings" : 
                        $boardSideBar[23] = [
                            "sectionTitle" =>  " SEC - Settings"  
                        ];
                        $list            = [
                                            "Business Settings"      => "/business/settings/list",
                                            "Invoice Settings"       => "/invoice/settings/list",
                                            "Report Settings"        => "/reports/setting/list",
                                            "Barcode Settings"       => "/barcode/setting/list",
                                            "Receipt Printers"       => "/receipt-printer/setting/list",
                                            "Tax Rates"              => "/tax/setting/list",
                                            "Types Of Service"       => "/type-of-service/setting/list",
                                            "Product Price Settings" => "/product-price/setting/list",
                                            "Delete Service"         => "/delete-page/list",
                                        ] ;
                        $children        = [];
                        $index           =  24;
                        foreach($list as $t => $p ){
                            $children[] = [
                                "title" => $t,
                                "path"  => $p,
                            ];
                        }
                        $badContent      = "";
                        $badgeColor      = "";
                        $path            = "" ;
                        $icons           = 'ic:round-settings';  break;
                    case "Log File" : 
                        $list            = [
                                            "Products log"    => "log-file/products"    ,
                                            "Sales log"       => "log-file/sales"       ,
                                            "Purchases log"   => "log-file/purchases"   ,
                                            "Payments log"    => "log-file/payments"    ,
                                            "Users log"       => "log-file/users"       ,
                                            "Vouchers log"    => "log-file/vouchers"    ,
                                            "Cheques log"     => "log-file/cheques"     ,
                                            "Stores log"      => "log-file/stores"      ,
                                            "Contacts log"    => "log-file/contacts"    ,
                                            "Accounts log"    => "log-file/accounts"    ,
                                            "Recipes log"     => "log-file/receipt"     ,
                                            "Production log"  => "log-file/production"  ,
                                        ] ;
                        $children        = [];
                        $index           =  25;
                        foreach($list as $t => $p ){
                            $children[] = [
                                "title" => $t,
                                "path"  => $p,
                            ];
                        }
                        $badContent      = "";
                        $badgeColor      = "";
                        $path            = "" ;
                        $icons           = 'fluent-mdl2:contact-card-settings-mirrored';  break;
                    case "Assets" : 
                        $boardSideBar[26] = [
                            "sectionTitle" =>  " SEC - Assets"  
                        ];
                        $list            = [] ;
                        $children        = [];
                        $index           =  27;
                        foreach($list as $t => $p ){
                            $children[] = [
                                "title" => $t,
                                "path"  => $p,
                            ];
                        }
                        $badContent      = "";
                        $badgeColor      = "";
                        $path            = "assets/page" ;
                        $icons           = 'fluent-mdl2:contact-card-settings-mirrored';  break;
                    case "Partners" : 
                        $boardSideBar[28] = [
                            "sectionTitle" =>  " SEC - Partners"  
                        ];
                        $list            = [] ;
                        $children        = [];
                        $index           =  29;
                        foreach($list as $t => $p ){
                            $children[] = [
                                "title" => $t,
                                "path"  => $p,
                            ];
                        }
                        $badContent      = "";
                        $badgeColor      = "";
                        $path            = "partners/page" ;
                        $icons           = 'fluent-mdl2:contact-card-settings-mirrored';  break;
                    case "Maintenance Services" : 
                        $boardSideBar[30] = [
                            "sectionTitle" =>  " SEC - Maintenance Services"  
                        ];
                        $list            = [] ;
                        $children        = [];
                        $index           =  31;
                        foreach($list as $t => $p ){
                            $children[] = [
                                "title" => $t,
                                "path"  => $p,
                            ];
                        }
                        $badContent      = "";
                        $badgeColor      = "";
                        $path            = "services/page" ;
                        $icons           = 'fluent-mdl2:contact-card-settings-mirrored';  break;
                    case "Installment" : 
                        $boardSideBar[32] = [
                            "sectionTitle" =>  " SEC - Installment"  
                        ];
                        $list            = [] ;
                        $children        = [];
                        $index           =  33;
                        foreach($list as $t => $p ){
                            $children[] = [
                                "title" => $t,
                                "path"  => $p,
                            ];
                        }
                        $badContent      = "";
                        $badgeColor      = "";
                        $path            = "installment/page" ;
                        $icons           = 'fluent-mdl2:contact-card-settings-mirrored';  break;
                    case "Restaurants" : 
                        $boardSideBar[34] = [
                            "sectionTitle" =>  " SEC - Restaurants"  
                        ];
                        $list            = [] ;
                        $children        = [];
                        $index           =  35 ;
                        foreach($list as $t => $p ){
                            $children[] = [
                                "title" => $t,
                                "path"  => $p,
                            ];
                        }
                        $badContent      = "";
                        $badgeColor      = "";
                        $path            = "" ;
                        $icons           = 'fluent-mdl2:contact-card-settings-mirrored';  break;
                    case "Store Inventory" : 
                        $list            = [] ;
                        $children        = [] ;
                        $index           =  11;
                        foreach($list as $t => $p ){
                            $children[] = [
                                "title" => $t,
                                "path"  => $p,
                            ];
                        }
                        $badContent      = "";
                        $badgeColor      = "";
                        $path            = "" ;
                        $icons           = 'fluent-mdl2:contact-card-settings-mirrored';  break;
                    case "Damaged Inventory" : 
                        $list            = [] ;
                        $children        = [];
                        $index           =  12;
                        foreach($list as $t => $p ){
                            $children[] = [
                                "title" => $t,
                                "path"  => $p,
                            ];
                        }
                        $badContent      = "";
                        $badgeColor      = "";
                        $path            = "" ;
                        $icons           = 'fluent-mdl2:contact-card-settings-mirrored';  break;
                    case "Notifications" : 
                        $boardSideBar[36] = [
                            "sectionTitle" =>  " SEC - Restaurants"  
                        ];
                        $list            = [] ;
                        $children        = [];
                        $index           =  37;
                        foreach($list as $t => $p ){
                            $children[] = [
                                "title" => $t,
                                "path"  => $p,
                            ];
                        }
                        $badContent      = "";
                        $badgeColor      = "";
                        $path            = "notifications/page" ;
                        $icons           = 'fluent-mdl2:contact-card-settings-mirrored';  break;
                    case "Projects" : 
                        $boardSideBar[38] = [
                            "sectionTitle" =>  " SEC - Projects"  
                        ];
                        $list            = [] ;
                        $children        = [];
                        $index           =  39;
                        foreach($list as $t => $p ){
                            $children[] = [
                                "title" => $t,
                                "path"  => $p,
                            ];
                        }
                        $badContent      = "";
                        $badgeColor      = "";
                        $path            = "projects/page" ;
                        $icons           = 'fluent-mdl2:contact-card-settings-mirrored';  break;
                    case "HRM" :
                        $boardSideBar[40] = [
                            "sectionTitle" =>  " SEC - HRM"  
                        ]; 
                        $list            = [] ;
                        $children        = [];
                        $index           =  41;
                        foreach($list as $t => $p ){
                            $children[] = [
                                "title" => $t,
                                "path"  => $p,
                            ];
                        }
                        $badContent      = "";
                        $badgeColor      = "";
                        $path            = "hrm/page" ;
                        $icons           = 'fluent-mdl2:contact-card-settings-mirrored';  break;
                    case "Essentials" :
                        $boardSideBar[42] = [
                            "sectionTitle" =>  " SEC - Essentials"  
                        ]; 
                        $list            = [] ;
                        $children        = [];
                        $index           =  43;
                        foreach($list as $t => $p ){
                            $children[] = [
                                "title" => $t,
                                "path"  => $p,
                            ];
                        }
                        $badContent      = "";
                        $badgeColor      = "";
                        $path            = "essentials/page" ;
                        $icons           = 'fluent-mdl2:contact-card-settings-mirrored';  break;
                    case "Catalogue QR" : 
                        $boardSideBar[44] = [
                            "sectionTitle" =>  " SEC - Catalogue QR"  
                        ];
                        $list            = [] ;
                        $children        = [];
                        $index           =  45;
                        foreach($list as $t => $p ){
                            $children[] = [
                                "title" => $t,
                                "path"  => $p,
                            ];
                        }
                        $badContent      = "";
                        $badgeColor      = "";
                        $path            = "gq-catalogue/page" ;
                        $icons           = 'fluent-mdl2:contact-card-settings-mirrored';  break;
                    case "User Activation" : 
                        $boardSideBar[46] = [
                            "sectionTitle" =>  " SEC - Activation"  
                        ];
                        $list            = [
                                            "List Of POS Users"      => "activate/pos/list",
                                            "List Of POS Request"    => "activate/pos/request/list",
                                            "Add POS Activation"     => "activate/pos/create",
                                        ] ;
                        $children        = [];
                        $index           =  47;
                        foreach($list as $t => $p ){
                            $children[] = [
                                "title" => $t,
                                "path"  => $p,
                            ];
                        }
                        $badContent      = "";
                        $badgeColor      = "";
                        $path            = "" ;
                        $icons           = 'fluent-mdl2:contact-card-settings-mirrored';  break;
                    case "React Frontend Section" : 
                        $list            = [
                                            "List Of Companies"      => "activate/company/list",
                                        ] ;
                        $children        = [];
                        $index           =  48;
                        foreach($list as $t => $p ){
                            $children[] = [
                                "title" => $t,
                                "path"  => $p,
                            ];
                        }
                        $badContent      = "";
                        $badgeColor      = "";
                        $path            = "" ;
                        $icons           = 'fluent-mdl2:contact-card-settings-mirrored';  break;
                    case "Mobile Section" : 
                        $list            = [
                                            "List Of Mobiles"   => "activate/mobile/list",
                                        ] ;
                        $children        = [];
                        $index           =  49;
                        foreach($list as $t => $p ){
                            $children[] = [
                                "title" => $t,
                                "path"  => $p,
                            ];
                        }
                        $badContent      = "";
                        $badgeColor      = "";
                        $path            = "" ;
                        $icons           = 'fluent-mdl2:contact-card-settings-mirrored';  break;
                    case "CRM" : 
                        $boardSideBar[50] = [
                            "sectionTitle" =>  " SEC - CRM"  
                        ];
                        $list            = [] ;
                        $children        = [];
                        $index           =  51;
                        foreach($list as $t => $p ){
                            $children[] = [
                                "title" => $t,
                                "path"  => $p,
                            ];
                        }
                        $badContent      = "";
                        $badgeColor      = "";
                        $path            = "crm/page" ;
                        $icons           = 'fluent-mdl2:contact-card-settings-mirrored';  break;
                    case "E-commerce" : 
                        $boardSideBar[52] = [
                            "sectionTitle" =>  " SEC - E-commerce"  
                        ];
                        $list            = [] ;
                        $children        = [];
                        $index           =  53 ;
                        foreach($list as $t => $p ){
                            $children[] = [
                                "title" => $t,
                                "path"  => $p,
                            ];
                        }
                        $badContent      = "";
                        $badgeColor      = "";
                        $path            = "" ;
                        $icons           = 'fluent-mdl2:contact-card-settings-mirrored';  break;
                    case "Roles" : 
                        $list            = [
                                            "List Roles"  => "roles/list",
                                        ] ;
                        $children        = [] ;
                        $index           =  3; 
                        foreach($list as $t => $p ){
                            $children[] = [
                                "title" => $t,
                                "path"  => $p,
                            ];
                        }
                        $badContent      = "";
                        $badgeColor      = "";
                        $path            = "" ;
                        $icons           = 'bx:check-shield';  break;
                    default :  
                        
                    $icons = 'fluent-mdl2:contact-card-settings-mirrored'; break;
                }
               
                
                $boardSideBar[$index] =  [
                    "title"    => $side,
                    "icon"     => $icons,
                ];
                 
                if($path != ""){
                    $boardSideBar[$index]["path"] = $path;
                } 
                if($badContent  != ""){
                    $boardSideBar[$index]["badgContent"] = $badContent;
                } 
                if($badgeColor != ""){
                    $boardSideBar[$index]["badgeColor"] = $badgeColor;
                } 
                if(count($children)>0){
                    $boardSideBar[$index]["children"] = $children;
                } 
            }
            $list_keys_for_sorting = [];
            foreach($boardSideBar as $keyObject => $object){
                $list_keys_for_sorting[] = $keyObject ;
            }
            sort($list_keys_for_sorting);
            $sorted = [];
            foreach($list_keys_for_sorting as $ky => $idObject){
                $sorted[] = $boardSideBar[$idObject] ;
            }
            
            
            if($role->is_default){
                $sorted  = "";
            }
        } 
        $user_items  = [
            "id"                  => $user_data->id,
            "first_name"          => $user_data->first_name,
            "profile_photo_url"   => $user_data->profile_photo_url,
            "role"                => ($user_data->roles->first())?strrev(substr(strrev($user_data->roles->first()->name),strpos(strrev($user_data->roles->first()->name), '#')+1)):"",
            "permission"          => $old_role_permissions_all, 
            "sidebar"             => $sorted
            ];     

        $user->api_token  = $tokenSecret;
        $user->update();
        // $authToken = Str::random(40);
        // $token     = JWT::encode($User->toArray(), $authToken, 'HS256');
        $business       = \App\Business::find($user_data->business_id);
        $currency       = \App\Models\ExchangeRate::where("business_id",$business->id)->where("source",1)->first();
        $modules        = $this->moduleUtil->availableModules();
        $enabled_modules=["purchases","add_sale","pos_sale","stock_transfers","stock_adjustment","expenses","account","tables","modifiers","service_staff","booking","kitchen","Warehouse","subscription","types_of_service"];
        $list_of_label  = !empty($business->custom_labels) ? json_decode($business->custom_labels, true) : [];
        $list_of_module = [];
        foreach($modules as $k => $value){
            if(in_array($k, $enabled_modules)){
                $list_of_module[] = $k;
            }
        }
        $global_data[]  = [/** here you can add new global variable for use in all pages iE */
            "global_settings" => [ 
                    "BusinessName"                => $business->name,
                    "StartDate"                   => $business->start_date ,
                    "DefaultProfit"               => $business->default_profit_percent ,
                    "CurrencySymbolPlacement"     => $business->currency_symbol_placement ,
                    "TimeZone"                    => $business->time_zone ,
                    "FinancialYearStartMonth"     => $business->fy_start_month ,
                    "StockAccountingMethod"       => $business->accounting_method ,
                    "TransactionEditDays"         => $business->transaction_edit_days ,
                    "DateFormat"                  => $business->date_format ,
                    "TimeFormat"                  => $business->time_format ,
                    "DecimalFormat"               => config('constants.currency_precision') ,
                    "FilterInitial"               => "week" ,// day , month , year ,week
                    "FontSize"                    => "17px" ,
                    "FontWeight"                  => "600" ,
                    "FontStyle"                   => "capitalize" ,
                ]];
        $global_data[]  = [
            "tax" => [
                    "Tax1Name" => $business->tax_label_1,    
                    "Tax1No"   => $business->tax_number_1,    
                    "Tax2Name" => $business->tax_label_2,    
                    "Tax2No"   => $business->tax_number_2,    
                ]];
        $global_data[]  = [
            "product" => [
                    "SKUPrefix"             => $business->sku_prefix,    
                    "EnableProductExpiry"   => ["enable_expire"     => $business->enable_product_expiry , "expire_type"     => $business->expiry_type],    
                    "OnProductExpiry"       => ["on_product_expire" => $business->on_product_expiry , "stop_selling_before" => $business->stop_selling_before ] ,    
                    "EnableBrands"          => $business->enable_brand,    
                    "EnableCategories"      => $business->enable_category,    
                    "EnableSub-Categories"  => $business->enable_sub_category,    
                    "EnablePrice&Taxinfo"   => $business->enable_price_tax,    
                    "DefaultUnit"           => $business->default_unit,    
                    "EnableSubUnits"        => $business->enable_sub_units,    
                    "EnablePosition"        => $business->enable_racks,    
                    "EnableRacks"           => $business->enable_row,    
                    "EnableRow"             => $business->enable_position,    
                    "EnableWarranty"        => $business->common_settings["enable_product_warranty"],    
                ]];
        $global_data[]  = [
            "sale" => [
                    "DefaultSaleDiscount"               => $business->default_sales_discount,    
                    "DefaultSaleTax"                    => $business->default_sales_tax,    
                    "SalesCommissionAgent"              => $business->sales_cmsn_agnt,    
                    "SalesItemAdditionMethod"           => $business->item_addition_method,    
                    "SourceOfSalePrice"                 => $business->source_sell_price,    
                    "AmountRoundingmethod"              => json_decode($business->pos_settings)->amount_rounding_method,    
                    "SalesPriceIsMinimumSellingPrice"   =>  0  ,
                    "AllowOverselling"                  =>  0  ,
                ]];
        $global_data[]  = [
            "purchase" => [
                "EnableEditingProductPriceFromPurchaseScreen"     => $business->enable_editing_product_from_purchase,    
                "EnablePurchaseStatus"                            => $business->enable_purchase_status,    
                "EnableLotNumber"                                 => $business->enable_lot_number,    
                ]];
        $global_data[]  = [
            "dashboard" => [
                "ViewStockExpiryAlertFor:" => $business->stock_expiry_alert_days,       
                ]]; 
        $global_data[]  = [
            "system" => [
                "ThemeColor"                     => $business->stock_expiry_alert_days,       
                "DefaultDataTablePageEntries"    => $business->stock_expiry_alert_days,       
                "ShowHelpText"                   => $business->stock_expiry_alert_days,       
                ]];
        $global_data[]  = [
            "prefixes" => [
                "PurchaseOrder"         => $business->ref_no_prefixes["purchase"],       
                "PurchaseReturn"        => $business->ref_no_prefixes["purchase_return"],       
                "StockTransfer"         => $business->ref_no_prefixes["stock_transfer"],       
                "WarehouseAdjustment"   => $business->ref_no_prefixes["stock_adjustment"],       
                "SalesReturn"           => $business->ref_no_prefixes["sell_return"],       
                "Expenses"              => $business->ref_no_prefixes["expense"],       
                "Contacts"              => $business->ref_no_prefixes["contacts"],       
                "PurchasePayment"       => $business->ref_no_prefixes["purchase_payment"],       
                "SellPayment"           => $business->ref_no_prefixes["sell_payment"],       
                "ExpensePayment"        => $business->ref_no_prefixes["expense_payment"],       
                "BusinessLocation"      => $business->ref_no_prefixes["business_location"],       
                "Username"              => $business->ref_no_prefixes["username"],       
                "SubscriptionNo"        => $business->ref_no_prefixes["subscription"],       
                "Draft"                 => $business->ref_no_prefixes["draft"],       
                "PurchaseReceive"       => $business->ref_no_prefixes["purchase_receive"],       
                "ProjectNo"             => $business->ref_no_prefixes["project_no"],       
                "DeliveredInvoice"      => $business->ref_no_prefixes["sell_delivery"],       
                "DeliveryReceipt"       => $business->ref_no_prefixes["trans_delivery"],       
                "Approve"               => $business->ref_no_prefixes["Approve"],       
                "OpenQuantity"          => $business->ref_no_prefixes["Open_Quantity"],       
                "Voucher"               => $business->ref_no_prefixes["voucher"],       
                "ExpenseVoucher"        => $business->ref_no_prefixes["gouranl_voucher"],       
                "journalvoucher"        => $business->ref_no_prefixes["daily_payment"],       
                "Cheque"                => $business->ref_no_prefixes["Cheque"],       
                "Quotation"             => $business->ref_no_prefixes["quotation"],       
                "Supplier"              => $business->ref_no_prefixes["supplier"],       
                "Customer"              => $business->ref_no_prefixes["customer"],       
                ]];
        $global_data[]  = [
            "email" => [
                    "MailDriver"               => $business->email_settings["mail_driver"],       
                    "Host"                     => $business->email_settings["mail_host"],       
                    "Port"                     => $business->email_settings["mail_port"],       
                    "Username"                 => $business->email_settings["mail_username"],       
                    "Password"                 => $business->email_settings["mail_password"],       
                    "Encryption"               => $business->email_settings["mail_encryption"],       
                    "FromAddress"              => $business->email_settings["mail_from_address"],       
                    "FromName"                 => $business->email_settings["mail_from_name"],       
                ]];
        $global_data[]  = [
            "sms" => [
                    "SMSService"              => isset($business->sms_settings['sms_service']) ? $business->sms_settings['sms_service'] : 'other',       
                    "URL"                     => !empty($business->sms_settings['url']) ? $business->sms_settings['url'] : null,       
                    "SendToParameterName"     => !empty($business->sms_settings['send_to_param_name']) ? $business->sms_settings['send_to_param_name'] : null,       
                    "MessageParameterName"    => !empty($business->sms_settings['msg_param_name']) ? $business->sms_settings['msg_param_name'] : null,       
                    "RequestMethod"           => !empty($business->sms_settings['request_method']) ? $business->sms_settings['request_method'] : null,       
                    "Header1key"              => !empty($business->sms_settings['header_1']) ? $business->sms_settings['header_1'] : null,       
                    "Header2key"              => !empty($business->sms_settings['header_2']) ? $business->sms_settings['header_2'] : null,       
                    "Header3key"              => !empty($business->sms_settings['header_3']) ? $business->sms_settings['header_3'] : null,       
                    "Header1value"            => !empty($business->sms_settings['header_val_1']) ? $business->sms_settings['header_val_1'] : null,       
                    "Header2value"            => !empty($business->sms_settings['header_val_2']) ? $business->sms_settings['header_val_2'] : null,       
                    "Header3value"            => !empty($business->sms_settings['header_val_3']) ? $business->sms_settings['header_val_3'] : null,       
                    "Parameter1key"           => !empty($business->sms_settings['param_1']) ? $business->sms_settings['param_1'] : null,       
                    "Parameter2key"           => !empty($business->sms_settings['param_2']) ? $business->sms_settings['param_2'] : null,       
                    "Parameter3key"           => !empty($business->sms_settings['param_3']) ? $business->sms_settings['param_3'] : null,       
                    "Parameter4key"           => !empty($business->sms_settings['param_4']) ? $business->sms_settings['param_4'] : null,       
                    "Parameter5key"           => !empty($business->sms_settings['param_5']) ? $business->sms_settings['param_5'] : null,       
                    "Parameter6key"           => !empty($business->sms_settings['param_6']) ? $business->sms_settings['param_6'] : null,       
                    "Parameter7key"           => !empty($business->sms_settings['param_7']) ? $business->sms_settings['param_7'] : null,       
                    "Parameter8key"           => !empty($business->sms_settings['param_8']) ? $business->sms_settings['param_8'] : null,       
                    "Parameter9key"           => !empty($business->sms_settings['param_9']) ? $business->sms_settings['param_9'] : null,       
                    "Parameter10key"          => !empty($business->sms_settings['param_10']) ? $business->sms_settings['param_10'] : null,       
                    "Parameter1value"         => !empty($business->sms_settings['param_val_1']) ? $business->sms_settings['param_val_1'] : null,       
                    "Parameter2value"         => !empty($business->sms_settings['param_val_2']) ? $business->sms_settings['param_val_2'] : null,       
                    "Parameter3value"         => !empty($business->sms_settings['param_val_3']) ? $business->sms_settings['param_val_3'] : null,       
                    "Parameter4value"         => !empty($business->sms_settings['param_val_4']) ? $business->sms_settings['param_val_4'] : null,       
                    "Parameter5value"         => !empty($business->sms_settings['param_val_5']) ? $business->sms_settings['param_val_5'] : null,       
                    "Parameter6value"         => !empty($business->sms_settings['param_val_6']) ? $business->sms_settings['param_val_6'] : null,       
                    "Parameter7value"         => !empty($business->sms_settings['param_val_7']) ? $business->sms_settings['param_val_7'] : null,       
                    "Parameter8value"         => !empty($business->sms_settings['param_val_8']) ? $business->sms_settings['param_val_8'] : null,       
                    "Parameter9value"         => !empty($business->sms_settings['param_val_9']) ? $business->sms_settings['param_val_9'] : null,       
                    "Parameter10value"        => !empty($business->sms_settings['param_val_10']) ? $business->sms_settings['param_val_10'] : null,       
                ]];
        $global_data[]  = [
                "reward_point" => [
                    "EnableRewardPoint"                   => $business->enable_rp,       
                    "RewardPointDisplayName"              => $business->rp_name,       
                    "AmountSpendForUnitPoint"             => $business->amount_for_unit_rp,       
                    "MinimumOrderTotalToEarnReward"       => $business->min_order_total_for_rp,       
                    "MaximumPointsPerOrder"               => $business->max_rp_per_order,       
                    "RedeemAmountPerUnitPoint"            => $business->redeem_amount_per_unit_rp,       
                    "MinimumOrderTotalToRedeemPoints"     => $business->min_order_total_for_redeem,       
                    "MinimumRedeemPoint"                  => $business->min_redeem_point,       
                    "MaximumRedeemPointPerOrder"          => $business->max_redeem_point,       
                    "RewardPointExpiryPeriod"             => $business->rp_expiry_period,       
                ]];
        $global_data[]  = [
            "modules" => [
                    "Purchases"             => (in_array("purchases",$list_of_module))?true:false,       
                    "AddSale"               => (in_array("add_sale",$list_of_module))?true:false,       
                    "POS"                   => (in_array("pos_sale",$list_of_module))?true:false,       
                    "WarehouseTransfers"    => (in_array("stock_transfers",$list_of_module))?true:false,       
                    "WarehouseAdjustment"   => (in_array("stock_adjustment",$list_of_module))?true:false,       
                    "Expenses"              => (in_array("expenses",$list_of_module))?true:false,       
                    "Account"               => (in_array("account",$list_of_module))?true:false,       
                    "Tables"                => (in_array("tables",$list_of_module))?true:false,       
                    "Modifiers"             => (in_array("modifiers",$list_of_module))?true:false,       
                    "ServiceStaff"          => (in_array("service_staff",$list_of_module))?true:false,       
                    "EnableBookings"        => (in_array("booking",$list_of_module))?true:false,       
                    "Kitchen"               => (in_array("kitchen",$list_of_module))?true:false,       
                    "EnableSubscription"    => (in_array("subscription",$list_of_module))?true:false,       
                    "TypesOfService"        => (in_array("types_of_service",$list_of_module))?true:false,       
                ]];
        $global_data[]  = [
            "custom_labels" => [
                    "CustomPayment1"                 => !empty($list_of_label) ? $list_of_label['payments']['custom_pay_1']: null,       
                    "CustomPayment2"                 => !empty($list_of_label) ? $list_of_label['payments']['custom_pay_2']: null,       
                    "CustomPayment3"                 => !empty($list_of_label) ? $list_of_label['payments']['custom_pay_3']: null,       
                    "CustomPayment4"                 => !empty($list_of_label) ? $list_of_label['payments']['custom_pay_4']: null,       
                    "CustomPayment5"                 => !empty($list_of_label) ? $list_of_label['payments']['custom_pay_5']: null,       
                    "CustomPayment6"                 => !empty($list_of_label) ? $list_of_label['payments']['custom_pay_6']: null,       
                    "CustomPayment7"                 => !empty($list_of_label) ? $list_of_label['payments']['custom_pay_7']: null, 

                    "ContactCustomField1"           => !empty($list_of_label) ? $list_of_label['contact']['custom_field_1']: null,       
                    "ContactCustomField2"           => !empty($list_of_label) ? $list_of_label['contact']['custom_field_2']: null,       
                    "ContactCustomField3"           => !empty($list_of_label) ? $list_of_label['contact']['custom_field_3']: null,       
                    "ContactCustomField4"           => !empty($list_of_label) ? $list_of_label['contact']['custom_field_4']: null,       
                    "ContactCustomField5"           => !empty($list_of_label) ? $list_of_label['contact']['custom_field_5']: null,       
                    "ContactCustomField6"           => !empty($list_of_label) ? $list_of_label['contact']['custom_field_6']: null,       
                    "ContactCustomField7"           => !empty($list_of_label) ? $list_of_label['contact']['custom_field_7']: null,       
                    "ContactCustomField8"           => !empty($list_of_label) ? $list_of_label['contact']['custom_field_8']: null,       
                    "ContactCustomField9"           => !empty($list_of_label) ? $list_of_label['contact']['custom_field_9']: null,       
                    "ContactCustomField10"          => !empty($list_of_label) ? $list_of_label['contact']['custom_field_10']: null, 

                    "ProductCustomField1"           => !empty($list_of_label) ? $list_of_label['product']['custom_field_1']: null,       
                    "ProductCustomField2"           => !empty($list_of_label) ? $list_of_label['product']['custom_field_2']: null,       
                    "ProductCustomField3"           => !empty($list_of_label) ? $list_of_label['product']['custom_field_3']: null,       
                    "ProductCustomField4"           => !empty($list_of_label) ? $list_of_label['product']['custom_field_4']: null, 

                    "LocationCustomField1"          => !empty($list_of_label) ? $list_of_label['location']['custom_field_1']: null,       
                    "LocationCustomField2"          => !empty($list_of_label) ? $list_of_label['location']['custom_field_1']: null,       
                    "LocationCustomField3"          => !empty($list_of_label) ? $list_of_label['location']['custom_field_1']: null,       
                    "LocationCustomField4"          => !empty($list_of_label) ? $list_of_label['location']['custom_field_1']: null, 

                    "UserCustomField1"              => !empty($list_of_label) ? $list_of_label['user']['custom_field_1']: null,       
                    "UserCustomField2"              => !empty($list_of_label) ? $list_of_label['user']['custom_field_2']: null,       
                    "UserCustomField3"              => !empty($list_of_label) ? $list_of_label['user']['custom_field_3']: null,      
                    "UserCustomField4"              => !empty($list_of_label) ? $list_of_label['user']['custom_field_4']: null, 

                    "PurchaseCustomField1"          => !empty($list_of_label) ? $list_of_label['purchase']['custom_field_1']: null,       
                    "PurchaseCustomField2"          => !empty($list_of_label) ? $list_of_label['purchase']['custom_field_2']: null,       
                    "PurchaseCustomField3"          => !empty($list_of_label) ? $list_of_label['purchase']['custom_field_3']: null,       
                    "PurchaseCustomField4"          => !empty($list_of_label) ? $list_of_label['purchase']['custom_field_4']: null,

                    "SellCustomField1"              => !empty($list_of_label) ? $list_of_label['sell']['custom_field_1']: null,       
                    "SellCustomField2"              => !empty($list_of_label) ? $list_of_label['sell']['custom_field_2']: null,       
                    "SellCustomField3"              => !empty($list_of_label) ? $list_of_label['sell']['custom_field_3']: null,       
                    "SellCustomField4"              => !empty($list_of_label) ? $list_of_label['sell']['custom_field_4']: null,

                    "SaleShippingCustomField1"      => !empty($list_of_label) ? $list_of_label['shipping']['custom_field_1']: null,       
                    "SaleShippingCustomField2"      => !empty($list_of_label) ? $list_of_label['shipping']['custom_field_2']: null,       
                    "SaleShippingCustomField3"      => !empty($list_of_label) ? $list_of_label['shipping']['custom_field_3']: null,       
                    "SaleShippingCustomField4"      => !empty($list_of_label) ? $list_of_label['shipping']['custom_field_4']: null,

                    "TypesOfServiceCustomField1"    => !empty($list_of_label) ? $list_of_label['types_of_service']['custom_field_1']: null,       
                    "TypesOfServiceCustomField2"    => !empty($list_of_label) ? $list_of_label['types_of_service']['custom_field_2']: null,       
                    "TypesOfServiceCustomField3"    => !empty($list_of_label) ? $list_of_label['types_of_service']['custom_field_3']: null,       
                    "TypesOfServiceCustomField4"    => !empty($list_of_label) ? $list_of_label['types_of_service']['custom_field_4']: null      
                ]];
        $global_data[]  = [
            "manufacturing" => [
                    "Account"                         => $business->itemMfg,      
                    "ProfitAccount"                   => $business->profitMfg,      
                    "ManufacturingStore"              => $business->store_mfg,      
                ]];
    
        
        
        $code = ($currency->currency)?$currency->currency->code:"";
        $c_id = ($currency->currency)?$currency->currency->id:"";
        return response([
            "status"           => 200,
            "login_first_time" => false,
            "authorization"    => [
                "token"        => $tokenSecret,
                "success"      => true,
                "type"         => "Bearer",
                "user"         => $user_items,
            ],
            "currency"         => ["id"=>$c_id,"code"=>$code],
            "global_data"      => $global_data,
            "api_url"          => $connection->api
        ],200);
 
    }  

    public function logout(Request $request)
    {
         
        $this->guard()->logout();
         
       
        if ($response = $this->loggedOut($request)) {
            return $response;
        }

        return $request->wantsJson()
            ? new JsonResponse([], 204)
            : redirect('/');
    }
    /** ----------------------------------------------------------  **/
    /**                     Get Sell Bill                           **/
    /** ----------------------------------------------------------  **/
    public function getBill(Request $request)
    {

        $api_token = $request->token;
        $user      = User::where("api_token",$api_token)->first();
        
        if(!$user){
            abort(403, 'Unauthorized action.');
        }

        $user_id = $user->id;
        $sells   = \App\Transaction::join("transaction_payments as tp","transactions.id","tp.transaction_id")->whereIn("type",["sale","sell_return"])->select(["transactions.id","transactions.store_id","transactions.contact_id","transactions.agent_id","transactions.total_before_tax","transactions.final_total","transactions.transaction_date","transactions.transaction_date" ,"transactions.sell_lines","transactions.invoice_no","tp.created_by","transactions.payment_status","tp.amount"])->where("tp.created_by",$user->id)->get();
        
 
        return response()->json([
                "sales" => $sells   ,
                "token" => $last_api
            ]);
    }
    /** ----------------------------------------------------------  **/
    /**                     Get Sell Bill                           **/
    /** ----------------------------------------------------------  **/
    public function store(Request $request)
    {
        
        $api_token = request()->input("token");
        $api       = substr( $api_token,1);
        $last_api  = substr( $api_token,1,strlen($api)-1);
        $user      = User::where("api_token",$last_api)->first();
        
        if(!$user){
            abort(403, 'Unauthorized action.');
        }

        $user_id = $user->id;

        
        
        return response()->json([
                "sales" => $sells,
                "token" => $last_api
            ]);
    }
    protected function guard()
    {
        return Auth::guard();
    }
    /** ----------------------------------------------------------  **/
    /**                     OBJECT SORTING                          **/
    /** ----------------------------------------------------------  **/
    function compareKeys($k1, $k2) {
        return $k1->age - $k2->age;
    }
}
