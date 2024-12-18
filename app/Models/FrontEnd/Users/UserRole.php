<?php

namespace App\Models\FrontEnd\Users;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Models\Role;
use App\Models\FrontEnd\Utils\GlobalUtil;
use App\SellingPriceGroup;
use App\Utils\ModuleUtil;
use Spatie\Permission\Models\Permission;

class UserRole extends Model
{
    use HasFactory,SoftDeletes;
    // *** initialize the table 
    protected  $table = "roles";
    /**
     * All Utils instance.
     *
     */
    protected static $moduleUtil;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(ModuleUtil $moduleUtil)
    {
        static::$moduleUtil = $moduleUtil;
    }

    // ...1 List Role
    public static function Roles($business_id,$date) {
        $users          = Role::where("business_id",$business_id)->get();
        $count_of_users = 0;
        $list_users     = [];
        foreach($users as $i){
            if($i->is_cmmsn_agnt == 0)
            {
                $list_users[] =  [ 
                    "id"   => $i->id,
                    "name" => strrev(substr(strrev($i->name),strpos(strrev($i->name),"#")+1)),
                ];
                $count_of_users++;
            }
        }  
        $array = [
                "count" => $count_of_users,
                "list"  => $list_users
            ];
        return $array;  
    }
    // ...2 Create Role 
    public static function CreateRoles($user) {
        try{
            $data                         = [];
            $business_id                  = $user->business_id;
            $selling_price_groups         = SellingPriceGroup::where('business_id', $business_id)
                                                        ->active()
                                                        ->get();
            // $roles                        = UserRole::getPage($user,"edit",1);
            $roles                        = UserRole::getPage($user,"create",1,$selling_price_groups);
            // dd(json_decode(json_encode($roles)));
            $module_permissions           = (static::$moduleUtil)?static::$moduleUtil->getModuleData('user_permissions'):[];
            // $role                         = new Role();
            $role_permissions             = [];
            $data["steps"]                = json_decode(json_encode($roles))->steps; 
            $data["initial"]              = json_decode(json_encode($roles))->global; 
            // $data["module_permissions"]   = $module_permissions; 
            // $data["role"]                 = $role; 
            // $data["role_permissions"]     = $role_permissions;
            return $data;
        }catch(Exception $e){
            return false;
        }
    }
    // ...3 Edit Role 
    public static function EditRoles($user,$id) {
        try{
            $data                         = [];
            $business_id                  = $user->business_id;
            $selling_price_groups         = SellingPriceGroup::where('business_id', $business_id)
                                                        ->active()
                                                        ->get();
         
            $module_permissions           = (static::$moduleUtil)?static::$moduleUtil->getModuleData('user_permissions'):[];
            $role_permissions             = [];
            $roles                        = UserRole::getPage($user,"edit",$id,$selling_price_groups);
            $data["steps"]                = json_decode(json_encode($roles))->steps; 
            $data["initial"]              = json_decode(json_encode($roles))->global; 
            // $data["module_permissions"]   = $module_permissions; 
            // $data["role_permissions"]     = $role_permissions; 

            return $data;
        }catch(Exception $e){
            return false;
        }
    }
    // ...4 Store Role 
    public static function StoreRoles($user,$data,$request) {
        try{
            $GlobalData = [];
            // dd(json_decode(json_encode($request->all())));
            $data = json_decode(json_encode($request->all()));
            // GET KEYS OF OBJECT;
            $dataKeys    = get_object_vars($data); 
            $allKeys     = array_keys($dataKeys);
            $role_name   = "";
            $permissions = [];
            // ... go inside object on the side bar of role page ;
            foreach ($allKeys as $key => $value) {
                if($value == "role_name"){
                    $role_name            = $data->role_name;
                    $GlobalData["name"]   = $role_name ; 
                }else{
                   
                    # get One Object that be array ..
                    $object            = $data->$value;
                    # item of Global Object the first item from array
                    $title             = $object[0]->title;
                    $objectPermissions = $object[0]->permissions;
                    $checked           = $object[0]->checked;
                    $checked_line      = 0;
                    if($title != "Service staff Management"){
                        # go in permissions 
                        if($value == "Access Sales Price Group"){
                            $list_sale = [];
                            foreach($objectPermissions as $role_line){
                                $RoleName    = $role_line->title;
                                $RoleChecked = $role_line->checked;
                                if($RoleChecked == true){
                                    $list_sale[] = $RoleName;
                                }
                            }
                            $GlobalData["spg_permissions"] = $list_sale;
                        }else{
                            if($value != "Dashboard"){
                                foreach($objectPermissions as $child){
                                    $childTitle    =  $child->title;
                                    $childSubTitle =  $child->subTitle;
                                    $childTaps     =  $child->taps;
                                    foreach($childTaps as $index => $role_section){
                                        # make sales price group separate ..
                                        if($value != "Access Sales Price Group"){
                                        $role_sections = $role_section->section;
                                            $prefix = "";
                                            if($index == 0){
                                                $prefix = "sidBar.";
                                            }
                                            if($index == 1){
                                                $prefix = "Tables.";
                                            }
                                            if($index == 2){
                                                $prefix = "Action.";
                                            }
                                            foreach($role_sections as $role_line){
                                                $RoleName    = $role_line->title;
                                                $RoleChecked = $role_line->checked;
                                                if($RoleChecked == true){
                                                    $checked_line = 1;
                                                    if($RoleName != ""){
                                                        $permissions[] = $prefix.$RoleName;
                                                    }
                                                }
                                            } 
                                        }else{
                                            $list = [];
                                            if($index == 0){
                                                $role_sections = $role_section->section;
                                                foreach($role_sections as $role_line){
                                                    $RoleName    = $role_line->title;
                                                    $RoleChecked = $role_line->checked;
                                                    if($RoleChecked == true){
                                                        $list[] = $RoleName;
                                                    }
                                                }
                                                $GlobalData["spg_permissions"] = $list;
                                            }
                                        }
                                
                                    }
                                }
                            }else{
                                foreach($objectPermissions as $child){
                                    $childTitle    =  $child->title;
                                    if($child->checked == true){
                                        $checked_line  =  1;
                                        $prefix        = "sidBar.";
                                        $permissions[] = $prefix.$childTitle;
                                    } 
                                }
                            }
                        }
                        
                        if($checked_line == 1){
                            $prx           = "sidRol.";
                            $permissions[] = $prx.$value;
                        }
                       
                    }else{
                        foreach($objectPermissions as $index => $role_section){
                            if($role_section->checked == true){
                                $checked_line  = 1 ;
                                $permissions[] = $role_section->title ;
                            }
                        }
                        // if($checked_line == 1){
                        //     $prx           = "sidRol.";
                        //     $permissions[] = $prx.$value;
                        // }
                    }
                    # for the list on the side bar of the role page
                    
                    
                }
            }
            $final_permission = [];
            $side_permission  = [];
            foreach($permissions as $perm){
                
               $nameOfRole =  substr($perm,7,strlen($perm));
               $prefix     =  substr($perm,0,7);
                if($prefix == "Action."){
                    $side_permission[]  = $nameOfRole;
                    if($nameOfRole == "As Service staff"){
                        $GlobalData["is_service_staff"] = true ;
                    }else{
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
                        if(isset($array_compare[$nameOfRole])){
                            $final_permission[] = $array_compare[$nameOfRole];
                        }
                    }
                }else{    
                    if($perm == "Service staff"){
                        $GlobalData["is_service_staff"] = true ;
                    }else{
                        $final_permission[] = $perm;
                    }
                }
            }
             
            $GlobalData["permissions"] = $final_permission;
            
            $permissions = $GlobalData["permissions"]; 
            $business_id = $user->business_id;
            $count       = Role::where('name', $role_name . '#' . $business_id)
                                ->where('business_id', $business_id)
                                ->count();
             
            if ($count == 0) {
                // *1* services staff ( exam : hairdressers salon )
                $is_service_staff = 0;
                if ($GlobalData['is_service_staff'] != null) {
                    $is_service_staff = 1;
                }

                // *2* Include selling price group permissions
                $spg_permissions = $GlobalData['spg_permissions'];
                if (!empty($spg_permissions)) {
                    foreach ($spg_permissions as $spg_permission) {
                        $permissions[] = $spg_permission;
                    }
                }

                // *3* create the role
                $role = Role::create([
                            'name'             => $role_name . '#' . $business_id ,
                            'business_id'      => $business_id,
                            'is_service_staff' => $is_service_staff
                        ]);


                if (!$role->is_default || $role->name == 'Cashier#' . $business_id) {
                    if ($role->name == 'Cashier#' . $business_id) {
                        $role->is_default = 0;
                    }

                    $is_service_staff = 0;
                    if (isset($GlobalData['is_service_staff']) &&  $GlobalData['is_service_staff'] == 1) {
                        $is_service_staff = 1;
                    }

                    //Include selling price group permissions
                    $existing_permissions = Permission::whereIn('name', $permissions)
                                                ->pluck('name')
                                                ->toArray();
                    $non_existing_permissions = array_diff($permissions, $existing_permissions);
                                                
                            // dd($non_existing_permissions);
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

                    $output = ['success' => 1,
                            'msg' => __("user.role_updated")
                        ];
                } else {
                    $output = ['success' => 0,
                            'msg' => __("user.role_is_default")
                        ];
                }
            } else {
                $output = [
                            'success' => 0,
                            'msg' => __("user.role_already_exists")
                        ];
            }
            return $output;
        }catch(Exception $e){
            return false;
        }
    }
    // ...5 Update Role 
    public static function UpdateRoles($user,$data,$id,$request) {
        try{
            $GlobalData                   = [];
            $business_id                  = $user->business_id;
            $data                         = json_decode(json_encode($request->all()));
            // GET KEYS OF OBJECT;
            $dataKeys                     = get_object_vars($data); 
            $allKeys                      = array_keys($dataKeys);
            $role_name                    = "";
            $permissions                  = [];

            // ... go inside object on the side bar of role page ;
            foreach ($allKeys as $key => $value) {
                if($value == "role_name"){
                    $role_name            = $data->role_name;
                    $GlobalData["name"]   = $role_name ; 
                }else{
                    # get One Object that be array ..
                    $object            = $data->$value;
                    # item of Global Object the first item from array
                    $title             = $object[0]->title;
                    $objectPermissions = $object[0]->permissions;
                    $checked           = $object[0]->checked;
                    $checked_line      = 0;
                    if($title != "Service staff Management"){
                        # go in permissions 
                        if($value == "Access Sales Price Group"){
                            $list_sale = [];
                            foreach($objectPermissions as $role_line){
                                $RoleName    = $role_line->title;
                                $RoleChecked = $role_line->checked;
                                if($RoleChecked == true){
                                    $list_sale[] = $RoleName;
                                }
                            }
                            $GlobalData["spg_permissions"] = $list_sale;
                        }else{
                            if($value != "Dashboard"){
                                foreach($objectPermissions as $child){
                                    $childTitle    =  $child->title;
                                    $childSubTitle =  $child->subTitle;
                                    $childTaps     =  $child->taps;
                                    foreach($childTaps as $index => $role_section){
                                        # make sales price group separate ..
                                        if($value != "Access Sales Price Group"){
                                        $role_sections = $role_section->section;
                                            $prefix = "";
                                            if($index == 0){
                                                $prefix = "sidBar.";
                                            }
                                            if($index == 1){
                                                $prefix = "Tables.";
                                            }
                                            if($index == 2){
                                                $prefix = "Action.";
                                            }
                                            foreach($role_sections as $role_line){
                                                $RoleName    = $role_line->title;
                                                $RoleChecked = $role_line->checked;
                                                if($RoleChecked == true){
                                                    $checked_line = 1;
                                                    if($RoleName != ""){
                                                        $permissions[] = $prefix.$RoleName;
                                                    }
                                                }
                                            } 
                                        }else{
                                            $list = [];
                                            if($index == 0){
                                                $role_sections = $role_section->section;
                                                foreach($role_sections as $role_line){
                                                    $RoleName    = $role_line->title;
                                                    $RoleChecked = $role_line->checked;
                                                    if($RoleChecked == true){
                                                        $list[] = $RoleName;
                                                    }
                                                }
                                                $GlobalData["spg_permissions"] = $list;
                                            }
                                        }
                                
                                    }
                                }
                            }else{
                                foreach($objectPermissions as $child){
                                    $childTitle    =  $child->title;
                                    if($child->checked == true){
                                        $checked_line  = 1;
                                        $prefix        = "sidBar.";
                                        $permissions[] = $prefix.$childTitle;
                                    } 
                                }
                            }
                        }
                        if($checked_line == 1){
                            $prx           = "sidRol.";
                            $permissions[] = $prx.$value;
                        }
                    }else{
                        foreach($objectPermissions as $index => $role_section){
                            if($role_section->checked == true){
                                $checked_line  = 1 ;
                                $permissions[] = $role_section->title ;
                            }
                        }
                        // if($checked_line == 1){
                        //     $prx           = "sidRol.";
                        //     $permissions[] = $prx.$value;
                        // }
                    }
                    # for the list on the side bar of the role page
                }
            }
            $final_permission = [];
            $side_permission  = [];
            foreach($permissions as $perm){
                
               $nameOfRole =  substr($perm,7,strlen($perm));
               $prefix     =  substr($perm,0,7);
                if($prefix == "Action."){
                    $side_permission[]  = $nameOfRole;
                    if($nameOfRole == "As Service staff"){
                        $GlobalData["is_service_staff"] = true ;
                    }else{
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
                        if(isset($array_compare[$nameOfRole])){
                            $final_permission[] = $array_compare[$nameOfRole];
                        }
                    }
                }else{    
                    if($perm == "Service staff"){
                        $GlobalData["is_service_staff"] = true ;
                    }else{
                        $final_permission[] = $perm;
                    }
                }
            }
            
            //Include selling price group permissions
            $spg_permissions = isset($GlobalData['spg_permissions'])?$GlobalData['spg_permissions']:[];
            if (!empty($spg_permissions)) {
                foreach ($spg_permissions as $spg_permission) {
                    $final_permission[] = $spg_permission;
                }
            }
            $GlobalData["permissions"]    = $final_permission;
            $role_name                    = $GlobalData["name"];
            $permissions                  = $GlobalData["permissions"];
            
            $count                        = Role::where('name', $role_name . '#' . $business_id)
                                                         ->where('id', '!=', $id)
                                                         ->where('business_id', $business_id)
                                                         ->count();
            if ($count == 0) {
                $role = Role::findOrFail($id);
                if (!$role->is_default || $role->name == 'Cashier#' . $business_id) {
                     
                    if ($role->name == 'Cashier#' . $business_id) {
                        $role->is_default = 0;
                    }

                    $is_service_staff = 0;
                    if (isset($GlobalData["is_service_staff"]) && $GlobalData["is_service_staff"] == true) {
                        $is_service_staff = 1;
                    }
                    $role->is_service_staff   = $is_service_staff;
                    $role->name = $role_name . '#' . $business_id;
                    $role->save();

                  
                    
                    $existing_permissions = Permission::whereIn('name', $permissions)
                                                ->pluck('name')
                                                ->toArray();

                    $non_existing_permissions = array_diff($permissions, $existing_permissions);

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

                    $output = ['success' => 1,
                            'msg' => __("user.role_updated")
                        ];
                } else {
                   
                    $output = ['success' => 0,
                            'msg' => __("user.role_is_default")
                        ];
                }
            } else {
                $output = ['success' => 0,
                        'msg' => __("user.role_already_exists")
                    ];
            }

            return $output;
        }catch(Exception $e){
            return false;
        }
    }
    // ...6 Delete Role 
    public static function DeleteRoles($user,$id) {
        try{
            $data                         = [];
            $business_id                  = $user->business_id;
            $role = Role::where('business_id', $business_id)->find($id);

            if (!$role->is_default || $role->name == 'Cashier#' . $business_id) {
                $role->delete();
                $output = ['success' => 1,
                            'msg' => __("user.role_deleted")
                        ];
            } else {
                $output = ['success' => 0,
                        'msg' => __("user.role_is_default")
                    ];
            } 

            return $output;
        }catch(Exception $e){
            return false;
        }
    }

    // 7 Roles **** Important
    public static function RolesBy($user,$data) {
        try{
            if(isset($data['type'])){
                $list            = [];
                if($data['type'] == "warranty"){
                    $list[0] = "sidBar.Warranties";
                    $list[1] = "sidBar.logWarranties";
                    $list[2] = "warranty.index";
                    $list[3] = "warranty.update";
                    $list[4] = "warranty.create";
                    $list[5] = "warranty.view";
                    $list[6] = "warranty.delete";

                }elseif($data['type'] == "variation"){
                    $list[0] = "sidBar.Variations";
                    $list[1] = "sidBar.logVariations";
                    $list[2] = "variation.index";
                    $list[3] = "variation.update";
                    $list[4] = "variation.create";
                    $list[5] = "variation.view";
                    $list[6] = "variation.delete";
                    $list[7] = "variation.delete_row";

                }elseif($data['type'] == "unit"){
                    $list[0] = "sidBar.Units";
                    $list[1] = "sidBar.logUnits";
                    $list[2] = "unit.index";
                    $list[3] = "unit.update";
                    $list[4] = "unit.create";
                    $list[5] = "unit.view";
                    $list[6] = "unit.delete";
                    $list[7] = "unit.default";

                }elseif($data['type'] == "sales_price_group"){
                    $list[0] = "sidBar.Sale_Price_Group";
                    $list[1] = "sidBar.logSale_Price_Group";
                    $list[2] = "sales_price_group.index";
                    $list[3] = "sales_price_group.update";
                    $list[4] = "sales_price_group.create";
                    $list[5] = "sales_price_group.view";
                    $list[6] = "sales_price_group.delete";
                    $list[7] = "sales_price_group.active";
                    $list[8] = "sales_price_group.export";
                    $list[9] = "sales_price_group.import";

                }elseif($data['type'] == "brand"){
                    $list[0] = "sidBar.Brands";
                    $list[1] = "sidBar.logBrands";
                    $list[2] = "brand.index";
                    $list[3] = "brand.update";
                    $list[4] = "brand.create";
                    $list[5] = "brand.view";
                    $list[6] = "brand.delete";

                }elseif($data['type'] == "category"){
                    $list[0] = "sidBar.Categories";
                    $list[1] = "sidBar.logCategories";
                    $list[2] = "category.index";
                    $list[3] = "category.update";
                    $list[4] = "category.create";
                    $list[5] = "category.view";
                    $list[6] = "category.delete";

                }elseif($data['type'] == "product"){
                    $list[0]  = "sidBar.Products";
                    $list[1]  = "sidBar.List_Product";
                    $list[2]  = "sidBar.Add_Product";
                    $list[8]  = "sidBar.logProducts";
                    $list[9]  = "product.index";
                    $list[10] = "product.update";
                    $list[11] = "product.create";
                    $list[12] = "product.view";
                    $list[13] = "product.delete";
                    $list[14] = "product.view_sStock";
                    $list[15] = "product.avarage_cost";
                    $list[23] = "view_product_stock_value";

                }
                return GlobalUtil::arrayToObject($list);
            }else{
                return false;
            }
        }catch(Exception $e){
            return false;
        }
    }

    // *****ACTIONS**** \\
    // ...1 get Role 
    public static function getPage($user,$type,$id=null,$selling_price_groups) {
        try{
                $sales_price_group   = [];
                $sales_price_group[] = "access_default_selling_price";
                foreach($selling_price_groups as $onePrice){
                    $sales_price_group[]= $onePrice->name;
                }
                $data                         = [];
                $data_line                    = [];
                $taps_staff                   = [];
                $sideStaff                    = [];
                $asSideStaff                  = [];
                $permissions_staff            = [];
                $treeChildFirst               = [];
                $treeChildSecond              = [];
                $business_id                  = $user->business_id;

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
                if($type == "create"){
                    $old_role_permissions_all            = [];
                    $old_role_permissions_all['sidebar'] = [];
                    $old_role_permissions_all['sideRole']= [];
                    $old_role_permissions_all['action']  = [];
                    $old_role_permissions_all['table']   = [];
                    $old_role_permissions_all['other']   = [];
                    $list_sell_price_group       = [];
                    // ......................
                    $data_line["role_name"]   = "";
                    $data["steps"][]  = [
                        "icon"     => "bx:grid-alt",
                        "title"    => "Role Name",
                        "subTitle" => "Setup Permissions For Role Name Section",
                    ];
                }elseif($type == "edit"){
                    $OldRole  = Role::where('business_id', $business_id)
                                ->with(['permissions'])
                                ->find($id);
                    $old_role_permissions        = [];
                    $old_role_permissions_all    = [];
                    $old_role_permissions_sidRol = [];
                    $old_role_permissions_sidBar = [];
                    $old_role_permissions_Action = [];
                    $old_role_permissions_Tables = [];
                    $old_role_permissions_Others = [];
                    $list_sell_price_group       = [];
                    foreach ($OldRole->permissions as $role_perm) {
                       
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
                      
                    // ......................
                    $data_line["role_name"]   = strrev(substr(strrev($OldRole->name),strpos(strrev($OldRole->name),"#")+1));
                    $data["steps"][]  = [
                        "icon"     => "bx:grid-alt",
                        "title"    => "Role Name",
                        "subTitle" => "Setup Permissions For Role Name Section",
                    ];
                }    
                 
                $permissions_staff[] = [
                    "title"    => "Service staff" ,    
                    "checked"  => (isset($OldRole))?(($OldRole->is_service_staff)?true:false):false ,    
                    
                ];
                $data_line["service_staff"] = [
                    "title"         => "Service staff Management",
                    "checked"       => (isset($OldRole))?(($OldRole->is_service_staff)?true:false):false ,
                    "permissions"   => $permissions_staff, 
                ];
                $data["steps"][]  = [
                    "icon"     => "bx:grid-alt",
                    "title"    => "Service staff",
                    "subTitle" => "Setup Permissions For Service staff Section",
                ];
                // ** START Pages class

                // *1*......................................................PAGES.
                $allPagesSections["Users"]                  = ["List Users"];
                $allPagesSections["Roles"]                  = ["List Roles"];
                $allPagesSections["Contacts"]               = ["List Supplier","List Customer","List Customer Group","Import Contacts"];

                $allPagesSections["Products"]               = ["List Products","Import Products","Print Labels","Variations","List Opening Stock",
                                                               "Import Opening Stock","Sales Price Group","Units","Categories","Brands","Warranties"];
                $allPagesSections["Inventory"]              = ["Product Gallery","Inventory Report"];
                $allPagesSections["Manufacturing"]          = ["Recipe","Production","Manufacturing Report"];
                $allPagesSections["Purchases"]              = ["List Purchases","List Purchases Return","Map"];
                $allPagesSections["Sales"]                  = ["List Sales","List Approved Quotation","List Quotation","List Draft","List Sale Return",
                                                               "Sales Commission Agent","Import Sales","Quotation Terms"];
                $allPagesSections["Vouchers"]               = ["Vouchers List","Receipt Voucher","Payment Voucher","Journal Voucher List","Expense Voucher List"];
                $allPagesSections["Cheques"]                = ["Cheques List","Add Cheque In","Add Cheque Out","Contact Bank"];
                $allPagesSections["Store "]                 = ["Stores List","Stores Movement","Stores Tree","Stores Transfers","Received","Delivered"];
                $allPagesSections["Cash and Bank"]          = ["Cash List","Bank List"];
                $allPagesSections["Accounts"]               = ["List Accounts","List Accounts Type","Balance Sheet","Trial Balance","Cash Flow","List Entries","Cost Center"];
                $allPagesSections["Reports"]                = ["Profit & Loss Report","Profit & Loss Report By Product","Profit & Loss Report By Categories",
                                                               "Profit & Loss Report By Brands","Profit & Loss Report By Locations","Profit & Loss Report By Invoice",
                                                               "Profit & Loss Report By Date","Profit & Loss Report By Customer","Profit & Loss Report By Day",
                                                               "Product Purchase Report","Sales Representative Report","Register Report","Expense Report",
                                                               "Report Setting","Sale Payment Report","Purchase Payment Report","Product Sale Report Detailed",
                                                               "Product Sale Report DWPurchase","Product Sale Report Grouped","Items Report","Stock Expire Report",
                                                               "Product Sale Day","Trending Product","Stock Adjustment Report","Inventory Reports","Customer Group Report",
                                                               "Supplier & Customer Report","Tax Report Input Tax","Tax Report OutPut Tax","Tax Report Expense Tax",
                                                               "Tax Report OutPut Tax (Project Invoice)","Purchase & Sale Report","Activity Log"];
                $allPagesSections["Patterns"]               = ["Business Location","Define Pattern","System Account"];            
                $allPagesSections["Settings"]               = ["Business Settings","Invoice Settings","Barcode Settings","Receipt Printers","Tax Rates","Type Of Service",
                                                               "Delete Service","Package Subscription"];            
                $allPagesSections["Log File"]               = ["Products log","Sales log","Purchases log","Payments log","Users log","Vouchers log","Cheques log","Stores log","Contacts log","Accounts log","Recipes log","Production log"];            
                $allPagesSections["Assets"]                 = ["List Assets"]; 
                $allPagesSections["Partners"]               = ["List Partners","Partner Payment history","Final Accounts","Financial Estimation"]; 
                $allPagesSections["Maintenance Services"]   = ["List Services","Work List","Add a Receipt Number","Service Warranty","Payment List","Add Service Invoice","Service Brand","Service Settings"]; 
                $allPagesSections["Installment"]            = ["Installment Systems","List Invoices","Customer Premiums","Installment Report","Installment Customers"]; 
                $allPagesSections["Restaurants"]            = ["Orders Kitchen","Orders","Bookings","Table Report","Service Personnel Report","Tables","Additions","Sections","Kitchen Sections"]; 
                $allPagesSections["Store Inventory"]        = ["List Store Inventories"]; 
                $allPagesSections["Damaged Inventory"]      = ["List Damaged Inventories"]; 
                $allPagesSections["Notifications"]          = ["Notice Forms"]; 
                $allPagesSections["Projects"]               = ["Projects","My Tasks","Projects Reports","Project Categories"]; 
                $allPagesSections["HRM"]                    = ["HRM","The Kind Of Holiday","Leave","The Audience","Deduction Allowance","Payroll","Holiday","HR Department","HR Designation","HR Settings"]; 
                $allPagesSections["Essentials"]             = ["Essentials","TO DO","Document","Memos","Reminders","Messages","Essentials Settings"]; 
                $allPagesSections["Catalogue QR"]           = ["Catalogue QR"]; 
                $allPagesSections["User Activation"]        = ["List Of Users","List Of User Requests"]; 
                $allPagesSections["React Frontend Section"] = ["List React"]; 
                $allPagesSections["Mobile Section"]         = ["List Mobile"]; 
                $allPagesSections["CRM"]                    = ["CRM","Leads","Follow ups","Campaigns","Contact Login","Sources","Life Stage","CRM Reports"]; 
                $allPagesSections["E-commerce"]             = ["Websites","List Of Invoices","List Of Carts","Websites Settings","Websites Main Settings","Accounts Settings","Floating Bar Settings","Shop By Category Settings","Sections Settings","Contacts Us Settings","Stripe Settings"]; 
                // if(count($selling_price_groups)>0){
                //     $allPagesSections["Access Sales Price Group"]  = ["Access Sales Price Group"];    
                // }
                // *2*...................................   ..................TABLES.
                $allTableSections["List Users"]             = ["User Username","User Name","User Role","User Email","User Action"]; /** Users */
                $allTableSections["List Roles"]             = ["Role Name","Role Assigned To","Role Action"];  /** Roles */
                $allTableSections["List Supplier"]          = ["Supplier Contact ID","Supplier Business Name","Supplier Email","Supplier Tax Number",
                                                               "Supplier Opening Balance","Supplier Advanced Balance","Supplier Added On","Supplier Pay Term",
                                                               "Supplier Address","Supplier Mobile","Supplier Total Purchase Due","Supplier Total Purchase Return Due",
                                                               "Supplier Custom Field 1","Supplier Custom Field 2","Supplier Custom Field 3","Supplier Custom Field 4",
                                                               "Supplier Custom Field 5","Supplier Custom Field 6","Supplier Custom Field 7","Supplier Custom Field 8",
                                                               "Supplier Custom Field 9","Supplier Custom Field 10",
                                                               "Supplier Action"]; /** Supplier */
                $allTableSections["List Customer"]          = ["Customer Contact ID","Customer Business Name","Customer Email","Customer Tax Number",
                                                               "Customer Opening Balance","Customer Advanced Balance","Customer Customer Group","Customer Added On","Customer Credit Limit","Customer Pay Term",
                                                               "Customer Address","Customer Mobile","Customer Total Sale Due","Customer Total Sale Return Due",
                                                               "Customer Custom Field 1","Customer Custom Field 2","Customer Custom Field 3","Customer Custom Field 4",
                                                               "Customer Custom Field 5","Customer Custom Field 6","Customer Custom Field 7","Customer Custom Field 8",
                                                               "Customer Custom Field 9","Customer Custom Field 10",
                                                               "Customer Action"];  /** Customer */

                $allTableSections["List Customer Group"]    = ["Customer Group Name","Customer Group Calculation Percentage","Customer Group Sale Price Group","Customer Group Action"];   /** Customer Group */
                $allTableSections["List Products"]          = ["Product Image","Product Name","Product Business Location","Product Unit Cost Price","Product Unit Sale Price Exc.Vat",
                                                               "Product Sale Price","Product Current Stock","Product Type","Product Category","Product Sub Category","Product Brand",
                                                               "Product Tax","Product Code","Product Sub Code","Product Custom Field 1","Product Custom Field 2","Product Custom Field 3",
                                                               "Product Custom Field 4"];   /** Products */
                $allTableSections["Variations"]             = ["Variations","Variation Value","Variation Action"];  /** Variations */
                $allTableSections["List Opening Stock"]     = ["Opening Stock Reference No","Opening Stock Business Location","Opening Stock Date","Opening Stock Action"];  /** Opening Stock */
                $allTableSections["Sales Price Group"]      = ["Sales Price Group Name","Sales Price Group Description","Sales Price Group Action"];  /** Sales Price Group */
                $allTableSections["Units"]                  = ["Unit Name","Unit Short Name","Unit Accept Decimal","Unit Action"];  /** Units */
                $allTableSections["Categories"]             = ["Category Name","Category Code","Category Description","Category Action"];  /** Categories */
                $allTableSections["Brands"]                 = ["Brand Name","Brand Note","Brand Action"];  /** Brands */
                $allTableSections["Warranties"]             = ["Warranty Name","Warranty Description","Warranty Duration","Warranty Action"];  /** Warranties */
                $allTableSections["Inventory Report"]       = ["Inventory Report Name","Inventory Report Code","Inventory Report Image","Inventory Report Current Qty",
                                                               "Inventory Report Price","Inventory Report Total",
                                                               "Inventory Report Should Received","Inventory Report Should Delivered"];  /** Inventory Report */
                $allTableSections["Product Gallery"]        = ["Product Gallery Name","Product Gallery Image","Product Gallery Price","Product Gallery Current Qty"];  /** Product Gallery */
                $allTableSections["Recipe"]                 = ["Recipe Name","Recipe Category","Recipe Sub Category","Recipe Quantity","Recipe Action"];  /** Recipe */
                $allTableSections["Production"]             = ["Production Date","Production Reference No","Production Location","Production Product name",
                                                               "Production Quantity", "Production Total Cost","Production Action"];  /** Production */
                $allTableSections["Manufacturing Report"]   = ["Total Production","Total Production Cost","Total Sold"];  /** Manufacturing Report */
                $allTableSections["List Purchases"]         = ["Purchase Date","Purchase Receipt Reference","Purchase Reference No","Purchase Location",
                                                               "Purchase Supplier Name","Purchase Status","Purchase Payment Status","Purchase Received Status",
                                                               "Purchase Store Name","Purchase Grand Total","Purchase Payment Due","Purchase Added By",
                                                               "Purchase Action"];  /** List Purchases  */
                $allTableSections["List Purchases Return"]  = ["Purchase Return Date","Purchase Return Reference No","Purchase Return Parent Purchase","Purchase Return Location",
                                                               "Purchase Return Status","Purchase Return Supplier Name","Purchase Return Payment Status","Purchase Return Received Status",
                                                               "Purchase Return Store Name","Purchase Return Added By","Purchase Return Action"];  /** List Purchases Return */
                $allTableSections["Map"]                    = ["Map Date","Map Source Reference no","Map Action"];  /** Map */
                $allTableSections["List Sales"]             = ["Sales Date","Sales Agent","Sales Cost Center","Sales Project Number","Sales Invoice Number",
                                                               "Sales Customer Name","Sales Customer Mobile Number","Sales Store Name","Sales Location",
                                                               "Sales Payment Status","Sales Payment Method","Sales Sales Delivery Status","Sales Total Amount",
                                                               "Sales Return Due","Sales Total Paid","Sales Due","Sales Shipping Status","Sales Total Items",
                                                               "Sales Type Of Service","Sales Custom Field 1","Sales Added By","Sales Note","Sales Shipping Details",
                                                               "Sales Action"]; /** Sales */
                $allTableSections["List Approved Quotation"]= ["Approved Date","Approved Agent","Approved Cost Center","Approved Project Number","Approved Reference Number",
                                                               "Approved Approved Number","Approved Quotation Number"."Approved Draft Number","Approved Customer Name",
                                                               "Approved Customer Mobile Number","Approved Store Name","Approved Payment Status","Approved Delivery Status",
                                                               "Approved Total Amount","Approved Added By","Approved Converted","Approved Converted Date",
                                                               "Approved Action"]; /** Approved */
                $allTableSections["List Sale Return"]       = ["SalesReturn Date","SalesReturn Invoice Number","SalesReturn Parent Sale","SalesReturn Customer Name",
                                                               "SalesReturn Location","SalesReturn Invoice Status","SalesReturn Payment Status","SalesReturn Total Amount",
                                                               "SalesReturn Payment Due","SalesReturn Delivery Status","SalesReturn Added By",
                                                               "SalesReturn Store Name","SalesReturn Action"]; /** SalesReturn */
                $allTableSections["List Quotation"]         = ["Quotation Date","Quotation Agent","Quotation Cost Center","Quotation Project Number",
                                                               "Quotation Reference Number","Quotation Customer Name","Quotation Customer Mobile","Quotation Location",
                                                               "Quotation Total Items","Quotation Store Name","Quotation Added By","Quotation Action"]; /** Quotation */
                $allTableSections["List Draft"]             = ["Draft Date","Draft Agent","Draft Project Number","Draft Reference Number",
                                                               "Draft Customer Name","Draft Customer Mobile","Draft Location",
                                                               "Draft Store Name","Draft Total Items","Draft Added By","Draft Action"]; /** Draft */
                $allTableSections["Sales Commission Agent"] = ["SCAgent Name","SCAgent Email","SCAgent Customer Number","SCAgent Address",
                                                               "SCAgent Sales Commission Percentage (%)","SCAgent Action"]; /** Sales Commission Agent */
                $allTableSections["Quotation Terms"]        = ["Quotation Terms Name","Quotation Terms Description","Quotation Terms Date","Quotation Terms Action"];  /** Quotation Terms */
                $allTableSections["Vouchers List"]          = ["Vouchers Reference Number","Vouchers Contact","Vouchers Amount","Vouchers Invoice Amount",
                                                               "Vouchers Account","Vouchers Type","Voucher Date","Vouchers Action"];  /** Vouchers List */
                $allTableSections["Journal Voucher List"]   = ["Journal Voucher Reference Number","Journal Voucher Amount","Journal Voucher Date",
                                                               "Journal Voucher Action"];  /** Journal Voucher List */
                $allTableSections["Expense Voucher List"]   = ["Expense Voucher Reference Number","Expense Voucher Amount","Expense Voucher Date",
                                                               "Expense Voucher Action"];  /** Expense Voucher List */         
                $allTableSections["Cheques List"]           = ["Cheques Reference Number","Cheques Cheque Number","Cheques Contact","Cheques Amount",
                                                               "Cheques Payment For","Cheques Account","Cheques Collection Account","Cheques Status",
                                                               "Cheques Write Date","Cheques Due Date","Cheques Collection Date",
                                                               "Cheques Note","Cheques Action"];  /** Cheques List */         
                $allTableSections["Contact Bank"]           = ["Contact Bank Name","Contact Bank Business Location","Contact Bank Action"]; /** Contact Bank */                                                               
                $allTableSections["Stores List"]            = ["Store Name","Store Parent Store","Store Action"]; /** Store */                                                               
                $allTableSections["Stores Movement"]        = ["Store Move Product Name","Store Move Location","Store Move Unit","Store Move Store Name",
                                                               "Store Move Movement","Store Move Plus","Store Move Minus","Store Move Current Amount",
                                                               "Store Move Date"  ]; /** Store Movement */                                                               
                $allTableSections["Stores Transfers"]       = ["Store Transfer Date","Store Transfer References Number","Store Transfer From Store Name",
                                                               "Store Transfer To Store Name","Store Transfer Status","Store Transfer Total Quantity",
                                                               "Store Transfer Additional Notes","Store Transfer Action"]; /** Stores Transfers */ 
                $allTableSections["Cash List"]              = ["Cash Account Number","Cash Account Name","Cash Debit","Cash Credit","Cash Status","Cash Balance","Cash Action"]; /** Cash */
                $allTableSections["Bank List"]              = ["Bank Account Number","Bank Account Name","Bank Debit","Bank Credit","Bank Status","Bank Balance","Bank Action"]; /** Bank */
                $allTableSections["List Accounts"]          = ["Account Number","Account Name","Account Main Account Name","Account Debit","Account Credit","Account Note","Account Balance","Account Action"]; /** Account */
                $allTableSections["List Accounts Type"]     = ["Account Type Code","Account Type Name","Account Action"]; /** Account Type */
                $allTableSections["Balance Sheet"]          = ["Balance Sheet Liability","Balance Sheet Assets","Balance Sheet Loss Amount","Balance Sheet Profit Amount",
                                                               "Balance Sheet Closing Stock","Balance Sheet Total Liability"]; /** Balance Sheet */
                $allTableSections["Trial Balance"]          = ["Trial Balance Main Account","Trial Balance Account Type Name","Trial Balance Account Name",
                                                               "Trial Balance Credit","Trial Balance Debit"]; /** Trial Balance */
                $allTableSections["Cash Flow"]              = ["Cash Flow Date","Cash Flow Account Name","Cash Flow Description",
                                                               "Cash Flow Credit","Cash Flow Debit","Cash Flow Balance"]; /** Cash Flow */
                $allTableSections["List Entries"]           = ["Entries Date","Entries Reference Number","Entries Source Number",
                                                               "Entries State","Entries Action"]; /** List Entries */
                $allTableSections["Cost Center"]            = ["Cost Center Number","Cost Center Name","Cost Center Action"]; /** Cost Center */
                // ** REPORTS   
                $allTableSections["Profit & Loss Report"]                   = ["Profit/Loss Top Table"]; /** Top Table */
                $allTableSections["Profit & Loss Report By Product"]        = ["Profit/Loss By Product Product Name","Profit/Loss By Product Gross Profit"]; /** Profit/Loss By Product */
                $allTableSections["Profit & Loss Report By Categories"]     = ["Profit/Loss By Categories Category Name","Profit/Loss By Categories Gross Profit"]; /** Profit/Loss By Categories */
                $allTableSections["Profit & Loss Report By Brands"]         = ["Profit/Loss By Brands Brand Name","Profit/Loss By Brands Gross Profit"]; /** Profit/Loss By Brands */
                $allTableSections["Profit & Loss Report By Locations"]      = ["Profit/Loss By Locations Location Name","Profit/Loss By Locations Gross Profit"]; /** Profit/Loss By Locations */
                $allTableSections["Profit & Loss Report By Invoice"]        = ["Profit/Loss By Invoice Invoice NUmber","Profit/Loss By Invoice Gross Profit"]; /** Profit/Loss By Invoice */
                $allTableSections["Profit & Loss Report By Date"]           = ["Profit/Loss By Date Date","Profit/Loss By Date Gross Profit"]; /** Profit/Loss By Date */
                $allTableSections["Profit & Loss Report By Customer"]       = ["Profit/Loss By Customer Customer Name","Profit/Loss By Customer Gross Profit"]; /** Profit/Loss By Customer */
                $allTableSections["Profit & Loss Report By Day"]            = ["Profit/Loss By Day Day Name","Profit/Loss By Day Gross Profit"]; /** Profit/Loss By Day */
                
                $allTableSections["Product Purchase Report"]                = [""]; /** Product Purchase Report */
                $allTableSections["Sale Representative Report"]             = ["Sale Representative Summary"]; /** Sale Representative Report */
                $allTableSections["Sale Representative Report Sales Added"] = ["SalesAdded Date","SalesAdded Invoice Number","SalesAdded Customer Name","SalesAdded Payment Status","SalesAdded Sub Total",
                                                                               "SalesAdded Discount","SalesAdded Tax","SalesAdded Total Amount","SalesAdded Total Paid","SalesAdded Total Remaining",
                                                                               "SalesAdded Agent","SalesAdded Pattern","SalesAdded Project Number","SalesAdded Store Name","SalesAdded Unit Cost",
                                                                               "SalesAdded User"]; /** Sale Representative SalesAdded Report */
                $allTableSections["Sale Representative Report Sales With Commission "]   = ["SWCommission Date","SWCommission Invoice Number","SWCommission Customer Name",
                                                                                "SWCommission Location","SWCommission Payment Status","SWCommission Total Amount",
                                                                                "SWCommission Total Paid","SWCommission Total Remaining"]; /** Sale Representative SWCommission Report */
                $allTableSections["Sale Representative Report Expenses"]    = ["SRExpense Date","SRExpense Reference Number","SRExpense Expense Category","SRExpense Location",
                                                                               "SRExpense Payment Status","SRExpense Total Amount","SRExpense Expense For",
                                                                               "SRExpense Expense Note"]; /** Sale Representative Expenses Report */

                $allTableSections["Register Report"]                      = ["Register Report Open Time","Register Report Close Time","Register Report Location",
                                                                             "Register Report User","Register Report Total Card Slips",
                                                                             "Register Report Total Cheques","Register Report Total Cash","Register Report Actions"
                                                                            ]; /** Register Report */
                $allTableSections["Expense Report"]                       = ["Expense Report Expense Category","Expense Report Total Expense","Expense Report Footer Total" ]; /** Expense Report */
                $allTableSections["Sale Payment Report"]                  = ["Sale Payment Report Reference Number","Sale Payment Report Paid On","Sale Payment Report Amount",
                                                                             "Sale Payment Report Customer Name","Sale Payment Report Customer Group","Sale Payment Report Payment Method",
                                                                             "Sale Payment Report Sales Number","Sale Payment Report Action" ]; /** Sale Payment Report */
                $allTableSections["Purchase Payment Report"]              = ["Purchase Payment Report Reference Number","Purchase Payment Report Paid On","Purchase Payment Report Amount",
                                                                             "Purchase Payment Report Supplier Name","Purchase Payment Report Payment Method",
                                                                             "Purchase Payment Report Purchase Number","Purchase Payment Report Action" ]; /** Purchase Payment Report */  
                $allTableSections["Product Sale Report Detailed"]         = ["Product Sale Report Detailed Returned","Product Sale Report Detailed Product Name","Product Sale Report Detailed Item Code",
                                                                             "Product Sale Report Detailed Customer Name","Product Sale Report Detailed Contact Id",
                                                                             "Product Sale Report Detailed Invoice Number","Product Sale Report Detailed Date","Product Sale Report Detailed Quantity",
                                                                             "Product Sale Report Detailed Unit Price","Product Sale Report Detailed Discount","Product Sale Report Detailed Tax",
                                                                             "Product Sale Report Detailed Price inc.tax","Product Sale Report Detailed Total" ]; /** Product Sale Report Detailed */  
                $allTableSections["Product Sale Report DWPurchase"]       = ["Product Sale Report DWPurchase Product Name","Product Sale Report DWPurchase Item Code",
                                                                             "Product Sale Report DWPurchase Customer Name","Product Sale Report DWPurchase Invoice Number",
                                                                             "Product Sale Report DWPurchase Date","Product Sale Report DWPurchase Purchase Reference Number",
                                                                             "Product Sale Report DWPurchase Supplier Name","Product Sale Report DWPurchase Quantity"]; /** Product Sale Report DWPurchase */  
                $allTableSections["Product Sale Report Grouped"]          = ["Product Sale Report Grouped Product Name","Product Sale Report Grouped Item Code",
                                                                             "Product Sale Report Grouped Date","Product Sale Report Grouped Current Stock",
                                                                             "Product Sale Report Grouped Total Unit Sold","Product Sale Report Grouped Total"]; /** Product Sale Report Grouped */  
                $allTableSections["Items Report"]                         = ["Items Report Product Name","Items Report Item Code","Items Report Purchase Date",
                                                                             "Items Report Purchase Number","Items Report Supplier Name","Items Report Purchase Price",
                                                                             "Items Report Sale Date","Items Report Sale Number","Items Report Customer Name",
                                                                             "Items Report Location","Items Report Quantity","Items Report Sale Price","Items Report SubTotal"]; /** Items Report */  
                $allTableSections["Stock Adjustment Report"]              = ["Stock Adjustment Report Date","Stock Adjustment Report Reference Number","Stock Adjustment Report Location",
                                                                             "Stock Adjustment Report Adjustment Type","Stock Adjustment Report Total Quantity","Stock Adjustment Report Total Amount Recovered",
                                                                             "Stock Adjustment Report Reason","Stock Adjustment Report Added By","Stock Adjustment Report Action"]; /** Stock Adjustment Report */  
                $allTableSections["Stock Expire Report"]                  = ["Stock Expire Report Product Name","Stock Expire Report Item Code","Stock Expire Report Location",
                                                                             "Stock Expire Report Stock Left","Stock Expire Report Lot Number","Stock Expire Report Exp Date",
                                                                             "Stock Expire Report MFG date"]; /** Stock Expire Report */  
                $allTableSections["Inventory Reports"]                    = ["Inventory Report Closing Stock By Purchase","Inventory Report Closing Stock By Sale","Inventory Report Potential Profit",
                                                                             "Inventory Report Profit Margin %","Inventory Report Item Code","Inventory Report Product Name",
                                                                             "Inventory Report Location","Inventory Report Unit Price","Inventory Report Current Stock","Inventory Report Should Receive",
                                                                             "Inventory Report Should Deliver","Inventory Report Reserved Quantity","Inventory Report Current Stock Individual Price Purchase",
                                                                             "Inventory Report Current Stock Price Purchase","Inventory Report Purchase Price In Local Currency",
                                                                             "Inventory Report Current Stock Individual Price Sale", "Inventory Report Current Stock Price Sale",
                                                                             "Inventory Report Sale Price In Local Currency","Inventory Report Potential individual Profit",
                                                                             "Inventory Report Total Unit Sold","Inventory Report Total Unit Transferred","Inventory Report Total Unit Adjusted",
                                                                             "Inventory Report Current Stock Manufacturing","Inventory Report Action"]; /** Inventory Report */  
                                                                             
                $allTableSections["Customer Group Report"]                  = ["Customer Group Report Customer Group Name","Customer Group Report Total Sale"]; /** Customer Group Report */                                                                
                $allTableSections["Supplier & Customer Report"]             = ["Supplier & Customer Report Contact Name","Supplier & Customer Report Total Purchase",
                                                                               "Supplier & Customer Report Total Purchase Return","Supplier & Customer Report Total Sale","Supplier & Customer Report Total Sale Return",
                                                                               "Supplier & Customer Report Opening Balance Due","Supplier & Customer Report Due"]; /** Supplier & Customer Report */                                                                
                $allTableSections["Tax Report Input Tax"]                   = ["Tax Report InpTax Date","Tax Report InpTax Reference Number","Tax Report InpTax Supplier Name",
                                                                               "Tax Report InpTax Tax Number","Tax Report InpTax Tax Amount","Tax Report InpTax Discount",
                                                                               "Tax Report InpTax By Table"]; /** Tax Report Input Tax */  
                $allTableSections["Tax Report OutPut Tax"]                  = ["Tax Report OupTax Date","Tax Report OupTax Invoice Number","Tax Report OupTax Customer Name",
                                                                               "Tax Report OupTax Tax Number","Tax Report OupTax Total Amount","Tax Report OupTax Discount",
                                                                               "Tax Report OupTax By Table"]; /** Tax Report OutPut Tax */  
                $allTableSections["Tax Report Expense Tax"]                 = ["Tax Report ExpTax Date","Tax Report ExpTax Reference Number","Tax Report ExpTax Tax Number",
                                                                               "Tax Report ExpTax Total Amount","Tax Report ExpTax By Table"]; /** Tax Report Expense Tax */  
                $allTableSections["Tax Report OutPut Tax (Project Invoice)"]= ["Tax Report OupTax (Project Invoice) Date",
                                                                               "Tax Report OupTax (Project Invoice) Invoice Number","Tax Report OupTax (Project Invoice) Customer Name",
                                                                               "Tax Report OupTax (Project Invoice) Tax Number","Tax Report OupTax (Project Invoice) Total Amount",
                                                                               "Tax Report OupTax (Project Invoice) Discount","Tax Report OupTax (Project Invoice) By Table"]; /** Tax Report OutPut Tax (Project Invoice) */  
                $allTableSections["Activity Log"]                           = ["Activity Date","Activity Subject Type","Activity Action","Activity By",
                                                                               "Activity Note"]; /** Activity Log */
                $allTableSections["Business Location"]                      = ["Business Location Name","Business Location Location Id","Business Location Landmark",
                                                                               "Business Location City","Business Location Zip Code","Business Location State",
                                                                               "Business Location Country","Business Location Price Group","Business Location Invoice Schema",
                                                                               "Business Location Invoice Layout For Pos","Business Location Invoice Layout For Sale",
                                                                               "Business Location Action"]; /** Business Location */
                $allTableSections["Define Pattern"]                         = ["Pattern Name","Pattern Location","Pattern Invoice Schema","Pattern Invoice Layout",
                                                                               "Pattern POS Relations","Pattern Date","Pattern Added By","Pattern Action"]; /** Pattern */
                $allTableSections["System Account"]                         = ["System Account Pattern Name","System Account Location",
                                                                               "System Account Date","System Account Added By","System Account Action"]; /** System Account */
                $allTableSections["Products log"]                           = [""]; /** Products log */
                $allTableSections["Sales log"]                              = [""]; /** Sales log */
                $allTableSections["Purchases log"]                          = [""]; /** Purchases log */
                $allTableSections["Payments log"]                           = [""]; /** Payments log */
                $allTableSections["Users log"]                              = [""]; /** Users log */
                $allTableSections["Vouchers log"]                           = [""]; /** Vouchers log */
                $allTableSections["Cheques log"]                            = [""]; /** Cheques log */
                $allTableSections["Stores log"]                             = [""]; /** Stores log */
                $allTableSections["List Assets"]                            = ["Asset Code","Asset Location","Asset Quantity","Asset Description","Asset Create Date","Asset Type",
                                                                               "Asset Depreciation/Increase Ratio","Asset Price","Asset Edit Date","Asset Price Second","Asset Status",
                                                                               "Asset Note","Asset Actions"]; /** List Assets */
                $allTableSections["List Partners"]                          = ["Partner Name","Partner Address","Partner Mobile","Partner The value of paid-up capital","Partner Number of Shares",
                                                                               "Partner Debit","Partner Credit","Partner Actions"]; /** List Partners */
                $allTableSections["Partner Payment history"]                = ["Partner Payment history Name","Partner Payment history Amount","Partner Payment history Date",
                                                                               "Partner Payment history Type Of Process","Partner Payment history By","Partner Payment history Note",
                                                                               "Partner Payment history Actions",]; /** Partner Payment history */
                $allTableSections["Final Accounts"]                         = ["Final Accounts Profit Amount","Final Accounts Period From","Final Accounts Period To",
                                                                               "Final Accounts Number Of Shares","Final Accounts Share Amount","Final Accounts Note",
                                                                               "Final Accounts Actions","Final Accounts Name","Final Accounts Address","Final Accounts Mobile",
                                                                               "Final Accounts Debit","Final Accounts Credit"]; /** Final Accounts */
                $allTableSections["Financial Estimation"]                   = ["Financial Estimation Ending inventory (purchase price)","Financial Estimation Ending inventory (at selling price)",
                                                                               "Financial Estimation Potential profit","Financial Estimation Profit Margin %","Financial Estimation Storage and bank balance",
                                                                               "Financial Estimation Customer receivables","Financial Estimation Suppliers' receivables","Financial Estimation Total at purchase price",
                                                                               "Financial Estimation Total selling price","Financial Estimation Number Of Shares","Financial Estimation Share price at purchase price",
                                                                               "Financial Estimation Share price at selling price"]; /** Financial Estimation */
                $allTableSections["List Services"]                          = [""]; /** List Services */
                $allTableSections["Work List"]                              = [""]; /** Work List */
                $allTableSections["Add a Receipt Number"]                   = [""]; /** Add a Receipt Number */
                $allTableSections["Service Warranty"]                       = [""]; /** Service Warranty */
                $allTableSections["Payment List"]                           = [""]; /** Payment List */
                $allTableSections["Add Service Invoice"]                    = [""]; /** Add Service Invoice */
                $allTableSections["Service Brand"]                          = [""]; /** Service Brand */
                $allTableSections["Service Settings"]                       = [""]; /** Service Settings */
                $allTableSections["Installment Systems"]                    = [""]; /** Installment Systems */
                $allTableSections["List Invoices"]                          = [""]; /** List Invoices */
                $allTableSections["Customer Premiums"]                      = [""]; /** Customer Premiums */
                $allTableSections["Installment Report"]                     = [""]; /** Installment Report */
                $allTableSections["Installment Customers"]                  = [""]; /** Installment Customers */
                $allTableSections["Orders Kitchen"]                         = [""]; /** Orders Kitchen */
                $allTableSections["Orders"]                                 = [""]; /** Orders */
                $allTableSections["Bookings"]                               = [""]; /** Bookings */
                $allTableSections["Table Report"]                           = [""]; /** Table Report */
                $allTableSections["Service Personnel Report"]               = [""]; /** Service Personnel Report */
                $allTableSections["Tables"]                                 = [""]; /** Tables */
                $allTableSections["Additions"]                              = [""]; /** Additions */
                $allTableSections["Sections"]                               = [""]; /** Sections */
                $allTableSections["Kitchen Sections"]                       = [""]; /** Kitchen Sections */
                $allTableSections["List Store Inventories"]                 = [""]; /** List Store Inventories */
                $allTableSections["List Damaged Inventories"]               = [""]; /** List Damaged Inventories */
                $allTableSections["Notice Forms"]                           = [""]; /** Notice Forms */
                $allTableSections["Projects"]                               = [""]; /** Projects */
                $allTableSections["My Tasks"]                               = [""]; /** My Tasks */
                $allTableSections["Projects Reports"]                       = [""]; /** Projects Reports */
                $allTableSections["Project Categories"]                     = [""]; /** Project Categories */
                $allTableSections["HRM"]                                    = [""]; /** HRM */
                $allTableSections["The Kind Of Holiday"]                    = [""]; /** The Kind Of Holiday */
                $allTableSections["Leave"]                                  = [""]; /** Leave */
                $allTableSections["The Audience"]                           = [""]; /** The Audience */
                $allTableSections["Deduction Allowance"]                    = [""]; /** Deduction Allowance */
                $allTableSections["Payroll"]                                = [""]; /** Payroll */
                $allTableSections["Holiday"]                                = [""]; /** Holiday */
                $allTableSections["HR Department"]                          = [""]; /** HR Department */
                $allTableSections["HR Designation"]                         = [""]; /** HR Designation */
                $allTableSections["HR Settings"]                            = [""]; /** HR Settings */
                $allTableSections["Essentials"]                             = [""]; /** Essentials */
                $allTableSections["TO DO"]                                  = [""]; /** TO DO */
                $allTableSections["Document"]                               = [""]; /** Document */
                $allTableSections["Memos"]                                  = [""]; /** Memos */
                $allTableSections["Reminders"]                              = [""]; /** Reminders */
                $allTableSections["Messages"]                               = [""]; /** Messages */
                $allTableSections["Essentials Settings"]                    = [""]; /** Essentials Settings */ 
                $allTableSections["Catalogue QR"]                           = [""]; /** Catalogue QR */
                $allTableSections["List Of Users"]                          = [""]; /** List Of Users */
                $allTableSections["List Of User Requests"]                  = [""]; /** List Of User Requests */
                $allTableSections["List React"]                             = [""]; /** List React */
                $allTableSections["List Mobile"]                            = [""]; /** List Mobile */
                $allTableSections["CRM"]                                    = [""]; /** CRM */
                $allTableSections["Leads"]                                  = [""]; /** Leads */
                $allTableSections["Follow ups"]                             = [""]; /** Follow ups */
                $allTableSections["Campaigns"]                              = [""]; /** Campaigns */
                $allTableSections["Contact Login"]                          = [""]; /** Contact Login */
                $allTableSections["Sources"]                                = [""]; /** Sources */
                $allTableSections["Life Stage"]                             = [""]; /** Life Stage */
                $allTableSections["CRM Reports"]                            = [""]; /** CRM Reports */
                $allTableSections["Websites"]                               = [""]; /** Websites */
                $allTableSections["List Of Invoices"]                       = [""]; /** List Of Invoices */
                $allTableSections["List Of Carts"]                          = [""]; /** List Of Carts */
                $allTableSections["Websites Settings"]                      = [""]; /** Websites Settings */
                $allTableSections["Websites Main Settings"]                 = [""]; /** Websites Main Settings */
                $allTableSections["Accounts Settings"]                      = [""]; /** Accounts Settings */
                $allTableSections["Floating Bar Settings"]                  = [""]; /** Floating Bar Settings */
                $allTableSections["Shop By Category Settings"]              = [""]; /** Shop By Category Settings */
                $allTableSections["Sections Settings"]                      = [""]; /** Sections Settings */
                $allTableSections["Contacts Us Settings"]                   = [""]; /** Contacts Us Settings */
                $allTableSections["Stripe Settings"]                        = [""]; /** Stripe Settings */
                
                // *3*......................................................ACTIONS.
                
                /** Users */$allActionSections["List Users"]                                         = ["View User","Create User","Edit User","Delete User"];
                /** Roles */$allActionSections["List Roles"]                                         = ["View Role","Create Role","Edit Role","Delete Role"]; 
                /** Supplier */$allActionSections["List Supplier"]                                   = ["View Supplier","View Statement Supplier","Ledger Supplier",
                                                                                                        "Create Supplier","Edit Supplier","Delete Supplier","Pay Supplier",
                                                                                                        "Deactivate Supplier","View Purchase Supplier","Supplier Stock Report","Document & Note Supplier"]; 
                /** Customer */$allActionSections["List Customer"]                                   = ["View Customer","View Statement Customer","Ledger Customer",
                                                                                                        "Create Customer","Edit Customer","Delete Customer","Pay Customer",
                                                                                                        "Deactivate Customer","View Sales Customer","Customer Stock Report","Document & Note Customer"]; 
                /** Customer Group*/$allActionSections["List Customer Group"]                        = ["View Customer Group","Create Customer Group","Edit Customer Group","Delete Customer Group"]; 
                /** Import Contacts */$allActionSections["Import Contacts"]                          = ["Download Import Contacts","Submit Import Contacts"]; 
                /** Import Products */$allActionSections["Import Products"]                          = ["Download Import Products","Submit Import Products"]; 
                /** Import Sales */$allActionSections["Import Sales"]                                = ["Download Import Sales","Submit Import Sales"]; 
                /** Import Opening Stock */$allActionSections["Import Opening Stock"]                = ["Download Import Opening Stock","Submit Import Opening Stock"]; 
                /** Products */$allActionSections["List Products"]                                   = ["View Product","Create Product","Edit Product","Delete Product","Product Labels",
                                                                                                        "Product Add Barcode","Label Barcode","Product Add Opening Stock","Product History",
                                                                                                        "Duplicate Product","Download Product Brochure"]; 
                /** Variations */$allActionSections["Variations"]                                    = ["View Variation","Create Variation","Edit Variation","Delete Variation"]; 
                /** Opening Stock */$allActionSections["List Opening Stock"]                         = ["View Opening Stock","Create Opening Stock","Edit Opening Stock","Delete Opening Stock"]; 
                /** Sales Price Group */$allActionSections["Sales Price Group"]                      = ["View Sales Price Group","Create Sales Price Group","Edit Sales Price Group","Delete Sales Price Group",
                                                                                                        "Deactivate Sales Price Group","Download Import Sales Price Group","Submit Import Sales Price Group"]; 
                /** Units */$allActionSections["Units"]                                              = ["View Unit","Create Unit","Edit Unit","Delete Unit","Default Unit"];
                /** Categories */$allActionSections["Categories"]                                    = ["View Category","Create Category","Edit Category","Delete Category"];
                /** Brands */$allActionSections["Brands"]                                            = ["View Brand","Create Brand","Edit Brand","Delete Brand"];
                /** Warranties */$allActionSections["Warranties"]                                    = ["View Warranty","Create Warranty","Edit Warranty","Delete Warranty"];
                /** Recipes */$allActionSections["Recipe"]                                           = ["View Recipe","Create Recipe","Edit Recipe","Delete Recipe"];
                /** Production */$allActionSections["Production"]                                    = ["View Production","Create Production","Edit Production","Delete Production","Entry Production"];
                /** Manufacturing Report */$allActionSections["Manufacturing Report"]                = ["Manufacturing Report Inventory Report","Manufacturing Report Items Report"];
                /** List Purchases */$allActionSections["List Purchases"]                            = ["View Purchase","Create Purchase","Edit Purchase","Delete Purchase","Entry Purchase","Map Purchase",
                                                                                                        "Add Payment Purchase","View Payment Purchase","Return Purchase","Update Status","Edit Payment Purchase",
                                                                                                        "Delete Payment Purchase","Print Purchase","New Order Notification Purchase"];
                /** List Purchases Return */$allActionSections["List Purchases Return"]              = ["View Purchases Return","Create Purchases Return","Edit Purchases Return","Delete Purchases Return",
                                                                                                        "View Payment Purchase Return","Print Purchase Return","Entry Purchase Return","Edit Payment Purchase Return",
                                                                                                        "Delete Payment Purchase Return","Add Payment Purchase Return"]; 
                /** List Sales */$allActionSections["List Sales"]                                    = ["View Sales","Create Sales","Edit Sales","Delete Sales","Entry Sales","Map Sales",
                                                                                                        "Add Payment Sales","View Payment Sales","Return Sales","Duplicate Sales","Edit Payment Sales",
                                                                                                        "Delete Payment Sales","Print Sales","Packing list Sales","Invoice Url Sales","View Delivered"];
                /** List Approved */$allActionSections["List Approved Quotation"]                    = ["View Approved","Create Approved","Edit Approved","Delete Approved", 
                                                                                                        "Print Approved","Convert To Invoice"];
                /** List Quotation */$allActionSections["List Quotation"]                            = ["View Quotation","Create Quotation","Edit Quotation","Delete Quotation", 
                                                                                                        "Print Quotation","Quotation Url Quotation","New Quotation Notifications Quotation",
                                                                                                        "Convert To Approved Quotation"];
                /** List Draft */$allActionSections["List Draft"]                                    = ["View Draft","Create Draft","Edit Draft","Delete Draft", 
                                                                                                        "Print Draft","Convert To Quotation"];
                /** List Sale Return */$allActionSections["List Sale Return"]                        = ["View Sale Return","Create Sale Return","Edit Sale Return","Delete Sale Return", 
                                                                                                        "Print Sale Return","Add Payment Sale Return","View Payment Sale Return",
                                                                                                        "View Delivered Sale Return"];
                /** List SCAgent */$allActionSections["Map"]                                         = ["View Map"];
                /** List SCAgent */$allActionSections["List Sales Commission Agent"]                 = ["View SCAgent","Create SCAgent","Edit SCAgent","Delete SCAgent"];
                /** List Quotation Terms */$allActionSections["Quotation Terms"]                     = ["View Quotation Terms","Create Quotation Terms","Edit Quotation Terms","Delete Quotation Terms"];
                /** List Voucher */$allActionSections["Vouchers List"]                               = ["View Voucher","Create Voucher","Edit Voucher","Delete Voucher",
                                                                                                        "Attachment Voucher","Entry Voucher","Print Voucher"];
                /** List Journal Voucher */$allActionSections["Journal Voucher List"]                = ["View Journal Voucher","Create Journal Voucher","Edit Journal Voucher","Delete Journal Voucher",
                                                                                                        "Attachment Journal Voucher","Entry Journal Voucher","Print Journal Voucher"];
                /** List Expense Voucher */$allActionSections["Expense Voucher List"]                = ["View Expense Voucher","Create Expense Voucher","Edit Expense Voucher","Delete Expense Voucher",
                                                                                                        "Attachment Expense Voucher","Entry Expense Voucher","Print Expense Voucher"];
                /** List Cheques */$allActionSections["Cheques List"]                                = ["View Cheques","Create Cheques","Edit Cheques","Delete Cheques",  
                                                                                                        "Collect Cheques","UnCollect Cheques","Refund Cheques","Delete Collect Cheques",
                                                                                                        "Attachment Cheques","Entry Cheques","Print Cheques"];
                /** List Contact Bank */$allActionSections["Contact Bank"]                           = ["View Contact Bank","Create Contact Bank","Edit Contact Bank","Delete Contact Bank"];
                /** List Store */$allActionSections["Stores List"]                                   = ["View Store","Create Store","Edit Store","Delete Store"];
                /** Stores Transfers */$allActionSections["Stores Transfers"]                        = ["View Stores Transfers","Create Stores Transfers","Edit Stores Transfers",
                                                                                                        "Print Stores Transfers","Delete Stores Transfers","Change Status Stores Transfers"];
                /** Cash List */$allActionSections["Cash List"]                                      = ["Ledger Cash","Create Cash","Edit Cash","Close Cash"];
                /** Bank List */$allActionSections["Bank List"]                                      = ["ledger Bank","Create Bank","Edit Bank","Close Bank"];
                /** Account List */$allActionSections["List Accounts"]                               = ["ledger Account","Create Account","Edit Account","Close Account"];
                /** Account Types List */$allActionSections["List Accounts Type"]                    = ["Create Account Type","Edit Account Type","Delete Account Type"];
                /** Balance Sheet */$allActionSections["Balance Sheet"]                              = ["Print Balance Sheet"];
                /** Trial Balance */$allActionSections["Trial Balance"]                              = ["Print Trial Balance"];
                /** List Entries */$allActionSections["List Entries"]                                = ["Entry Entries"];
                /** Cost Center */$allActionSections["Cost Center"]                                  = ["Movement Cost Center","Create Cost Center","Delete Cost Center","Edit Cost Center"];
                /** Purchase Payment Report */$allActionSections["Purchase Payment Report"]          = ["View Purchase Payment Report"];
                /** Sale Payment Report */$allActionSections["Sale Payment Report"]                  = ["View Sale Payment Report"];
                /** Business Location */$allActionSections["Business Location"]                      = ["View Business Location","Settings Business Location","Create Business Location","Edit Business Location","Deactivate Business Location"];
                /** Define Pattern */$allActionSections["Define Pattern"]                            = ["View Pattern","Create Pattern","Edit Pattern","Delete Pattern"];
                /** System Account */$allActionSections["System Account"]                            = ["View System Account","Create System Account","Edit System Account","Delete System Account"];
                /** Products log */$allActionSections["Products log"]                                = [""];
                /** Sales log */$allActionSections["Sales log"]                                      = [""];
                /** Purchases log */$allActionSections["Purchases log"]                              = [""];
                /** Payments log */$allActionSections["Payments log"]                                = [""];
                /** Users log */$allActionSections["Users log"]                                      = [""];
                /** Vouchers log */$allActionSections["Vouchers log"]                                = [""];
                /** Cheques log */$allActionSections["Cheques log"]                                  = [""];
                /** Stores log */$allActionSections["Stores log"]                                    = [""];
 
                /** List Assets */$allActionSections["List Assets"]                                  = ["Create Asset","Edit Asset","View Asset","Delete Asset"]; 
                /** List Partners */$allActionSections["List Partners"]                              = ["Create Partner","Edit Partner","View Partner","Delete Partner"]; 
                /** Partner Payment history */$allActionSections["Partner Payment history"]          = ["Create Partner Payment history","Edit Partner Payment history",
                                                                                                        "View Partner Payment history","Delete Partner Payment history"]; 
                /** Final Accounts */$allActionSections["Final Accounts"]                            = ["Create Final Accounts","Edit Final Accounts","View Final Accounts",
                                                                                                        "Delete Final Accounts","Final Accounts Distribute"]; 
                /** Financial Estimation */ // $allActionSections["Financial Estimation"]                = [""]; 
                /** List Services */$allActionSections["List Services"]                              = [""]; 
                /** Work List */$allActionSections["Work List"]                                      = [""]; 
                /** Add a Receipt Number */$allActionSections["Add a Receipt Number"]                = [""]; 
                /** Service Warranty */$allActionSections["Service Warranty"]                        = [""]; 
                /** Payment List */$allActionSections["Payment List"]                                = [""]; 
                /** Add Service Invoice */$allActionSections["Add Service Invoice"]                  = [""]; 
                /** Service Brand */$allActionSections["Service Brand"]                              = [""]; 
                /** Service Settings */$allActionSections["Service Settings"]                        = [""]; 
                /** Installment Systems */$allActionSections["Installment Systems"]                  = [""]; 
                /** List Invoices */$allActionSections["List Invoices"]                              = [""]; 
                /** Customer Premiums */$allActionSections["Customer Premiums"]                      = [""]; 
                /** Installment Report */$allActionSections["Installment Report"]                    = [""]; 
                /** Installment Customers */$allActionSections["Installment Customers"]              = [""]; 
                /** Orders Kitchen */$allActionSections["Orders Kitchen"]                            = [""]; 
                /** Orders */$allActionSections["Orders"]                                            = [""]; 
                /** Bookings */$allActionSections["Bookings"]                                        = [""]; 
                /** Table Report */$allActionSections["Table Report"]                                = [""]; 
                /** Service Personnel Report */$allActionSections["Service Personnel Report"]        = [""]; 
                /** Tables */$allActionSections["Tables"]                                            = [""]; 
                /** Additions */$allActionSections["Additions"]                                      = [""];
                /** Sections */$allActionSections["Sections"]                                        = [""]; 
                /** Kitchen Sections */$allActionSections["Kitchen Sections"]                        = [""]; 
                /** List Store Inventories */$allActionSections["List Store Inventories"]            = [""]; 
                /** List Damaged Inventories */$allActionSections["List Damaged Inventories"]        = [""]; 
                /** Notice Forms */$allActionSections["Notice Forms"]                                = [""]; 
                /** Projects */$allActionSections["Projects"]                                        = [""]; 
                /** My Tasks */$allActionSections["My Tasks"]                                        = [""]; 
                /** Projects Reports */$allActionSections["Projects Reports"]                        = [""]; 
                /** Project Categories */$allActionSections["Project Categories"]                    = [""]; 
                /** HRM */$allActionSections["HRM"]                                                  = [""]; 
                /** The Kind Of Holiday */$allActionSections["The Kind Of Holiday"]                  = [""]; 
                /** Leave */$allActionSections["Leave"]                                              = [""]; 
                /** The Audience */$allActionSections["The Audience"]                                = [""]; 
                /** Deduction Allowance */$allActionSections["Deduction Allowance"]                  = [""]; 
                /** Payroll */$allActionSections["Payroll"]                                          = [""]; 
                /** Holiday */$allActionSections["Holiday"]                                          = [""]; 
                /** HR Department */$allActionSections["HR Department"]                              = [""]; 
                /** HR Designation */$allActionSections["HR Designation"]                            = [""]; 
                /** HR Settings */$allActionSections["HR Settings"]                                  = [""]; 
                /** Essentials */$allActionSections["Essentials"]                                    = [""]; 
                /** TO DO */$allActionSections["TO DO"]                                              = [""]; 
                /** Document */$allActionSections["Document"]                                        = [""]; 
                /** Memos */$allActionSections["Memos"]                                              = [""]; 
                /** Reminders */$allActionSections["Reminders"]                                      = [""]; 
                /** Messages */$allActionSections["Messages"]                                        = [""]; 
                /** Essentials Settings */$allActionSections["Essentials Settings"]                  = [""];  
                /** Catalogue QR */$allActionSections["Catalogue QR"]                                = [""]; 
                /** List Of Users */$allActionSections["List Of Users"]                              = [""]; 
                /** List Of User Requests */$allActionSections["List Of User Requests"]              = [""]; 
                /** List React */$allActionSections["List React"]                                    = [""]; 
                /** List Mobile */$allActionSections["List Mobile"]                                  = [""]; 
                /** CRM */$allActionSections["CRM"]                                                  = [""]; 
                /** Leads */$allActionSections["Leads"]                                              = [""]; 
                /** Follow ups */$allActionSections["Follow ups"]                                    = [""]; 
                /** Campaigns */$allActionSections["Campaigns"]                                      = [""]; 
                /** Contact Login */$allActionSections["Contact Login"]                              = [""]; 
                /** Sources */$allActionSections["Sources"]                                          = [""]; 
                /** Life Stage */$allActionSections["Life Stage"]                                    = [""]; 
                /** CRM Reports */$allActionSections["CRM Reports"]                                  = [""]; 
                /** Websites */$allActionSections["Websites"]                                        = [""]; 
                /** List Of Invoices */$allActionSections["List Of Invoices"]                        = [""]; 
                /** List Of Carts */$allActionSections["List Of Carts"]                              = [""]; 
                /** Websites Settings */$allActionSections["Websites Settings"]                      = [""]; 
                /** Websites Main Settings */$allActionSections["Websites Main Settings"]            = [""]; 
                /** Accounts Settings */$allActionSections["Accounts Settings"]                      = [""]; 
                /** Floating Bar Settings */$allActionSections["Floating Bar Settings"]              = [""]; 
                /** Shop By Category Settings */$allActionSections["Shop By Category Settings"]      = [""]; 
                /** Sections Settings */$allActionSections["Sections Settings"]                      = [""]; 
                /** Contacts Us Settings */$allActionSections["Contacts Us Settings"]                = [""]; 
                /** Stripe Settings */$allActionSections["Stripe Settings"]                          = [""]; 


                // ** END Pages class
                
                   
                
                // ** START Sections class ................................SECTIONS.
                /** 01  */$allSidBarSections["Dashboard"]              = ["E-commerce","Analytics","CRM Analytics"]; 
                /** 02  */$allSidBarSections["Users"]                  = ["List Users"]; 
                /** 03  */$allSidBarSections["Roles"]                  = ["List Roles"]; 
                /** 04  */$allSidBarSections["Contacts"]               = ["List Supplier","List Customer","List Customer Group","Import Contacts"];
                /** 05  */$allSidBarSections["Products"]               = ["List Products","Import Products","Print Labels","Variations","List Opening Stock",
                                                                          "Import Opening Stock","Sales Price Group","Units","Categories","Brands","Warranties"]; 

                /** 06  */$allSidBarSections["Inventory"]              = ["Product Gallery","Inventory Report"]; 
                /** 07  */$allSidBarSections["Manufacturing"]          = ["Recipe","Production","Manufacturing Report"]; 
                /** 08  */$allSidBarSections["Purchases"]              = ["List Purchases","List Purchases Return","Map"]; 
                /** 09  */$allSidBarSections["Sales"]                  = ["List Sales","List Approved Quotation","List Quotation","List Draft","List Sale Return",
                                                                          "Sales Commission Agent","Import Sales","Quotation Terms"]; 
                /** 10  */$allSidBarSections["Vouchers"]               = ["Vouchers List","Receipt Voucher","Payment Voucher","Journal Voucher List","Expense Voucher List"]; 
                /** 11  */$allSidBarSections["Cheques"]                = ["Cheques List","Add Cheque In","Add Cheque Out","Contact Bank"]; 
                /** 12  */$allSidBarSections["Store"]                  = ["Stores List","Stores Movement","Stores Transfers","Received","Delivered"]; 
                /** 13  */$allSidBarSections["Cash and Bank"]          = ["Cash List","Bank List"]; 
                /** 14  */$allSidBarSections["Accounts"]               = ["List Accounts","Balance Sheet","Trial Balance","Cash Flow","List Entries","Cost Center"]; 
                /** 15  */$allSidBarSections["Reports"]                = ["Profit & Loss Report","Profit & Loss Report By Product","Profit & Loss Report By Categories",
                                                                          "Profit & Loss Report By Brands","Profit & Loss Report By Locations","Profit & Loss Report By Invoice",
                                                                          "Profit & Loss Report By Date","Profit & Loss Report By Customer","Profit & Loss Report By Day",
                                                                          "Product Purchase Report","Sales Representative Report","Register Report","Expense Report",
                                                                          "Report Setting","Sale Payment Report","Purchase Payment Report","Product Sale Report Detailed",
                                                                          "Product Sale Report DWPurchase","Product Sale Report Grouped","Items Report","Stock Expire Report",
                                                                          "Product Sale Day","Trending Product","Stock Adjustment Report","Inventory Reports","Customer Group Report",
                                                                          "Supplier & Customer Report","Tax Report Input Tax","Tax Report OutPut Tax","Tax Report Expense Tax",
                                                                          "Tax Report OutPut Tax (Project Invoice)","Purchase & Sale Report","Activity Log"]; 
                /** 16  */$allSidBarSections["Patterns"]                = ["Business Location","Define Pattern","System Account"]; 
                /** 17  */$allSidBarSections["Settings"]                = ["Business Setting","Invoice Settings","Barcode Settings","Product Price","Receipt Printer","Tax Rates","Types Of Services","Delete Service","Package Subscription"]; 
                /** 18  */$allSidBarSections["Log File"]                = ["Products log","Sales log","Purchases log","Payments log","Users log","Vouchers log","Cheques log","Stores log","Contacts log","Accounts log","Recipes log","Production log"]; 
                /** 19  */$allSidBarSections["Assets"]                  = ["List Assets"]; 
                /** 20  */$allSidBarSections["Partners"]                = ["List Partners","Partner Payment history","Final Accounts","Financial Estimation"]; 
                /** 21  */$allSidBarSections["Maintenance Services"]    = ["List Services","Work List","Add a Receipt Number","Service Warranty","Payment List","Add Service Invoice","Service Brand","Service Settings"]; 
                /** 22  */$allSidBarSections["Installment"]             = ["Installment Systems","List Invoices","Customer Premiums","Installment Report","Installment Customers"]; 
                /** 23  */$allSidBarSections["Restaurants"]             = ["Orders Kitchen","Orders","Bookings","Table Report","Service Personnel Report","Tables","Additions","Sections","Kitchen Sections"]; 
                /** 24  */$allSidBarSections["Store Inventory"]         = ["List Store Inventories"]; 
                /** 25  */$allSidBarSections["Damaged Inventory"]       = ["List Damaged Inventories"]; 
                /** 26  */$allSidBarSections["Notifications"]           = ["Notice Forms"]; 
                /** 27  */$allSidBarSections["Projects"]                = ["Projects","My Tasks","Projects Reports","Project Categories"]; 
                /** 28  */$allSidBarSections["HRM"]                     = ["HRM","The Kind Of Holiday","Leave","The Audience","Deduction Allowance","Payroll","Holiday","HR Department","HR Designation","HR Settings"]; 
                /** 29  */$allSidBarSections["Essentials"]              = ["Essentials","TO DO","Document","Memos","Reminders","Messages","Essentials Settings"]; 
                /** 30  */$allSidBarSections["Catalogue QR"]            = ["Catalogue QR"]; 
                /** 31  */$allSidBarSections["User Activation"]         = ["List Of Users","List Of User Requests"]; 
                /** 32  */$allSidBarSections["React Frontend Section"]  = ["List React"]; 
                /** 33  */$allSidBarSections["Mobile Section"]          = ["List Mobile"]; 
                /** 34  */$allSidBarSections["CRM"]                     = ["CRM","Leads","Follow ups","Campaigns","Contact Login","Sources","Life Stage","Reports"]; 
                /** 35  */$allSidBarSections["E-commerce"]              = ["Websites","List Of Invoices","List Of Carts","Websites Settings","Websites Main Settings","Accounts Settings","Floating Bar Settings","Shop By Category Settings","Sections Settings","Contacts Us Settings","Stripe Settings"]; 
                if(count($selling_price_groups)>0){
                /** 36  */$allSidBarSections["Access Sales Price Group"]  = ["Access Sales Price Group"];    
                }
                // .....................................TREE SECTIONS
                // $treeChildFirst["CRM"]                = ["CRM","Leads","Follow ups","Campaigns","Contact Login","Sources","Life Stage","Reports"];
                // $treeChildFirst["E-commerce"]         = ["Websites","List Of Invoices","List Of Carts","Websites Settings"];
                // $treeChildSecond["Websites Settings"] = ["Websites Main Settings","Accounts Settings","Floating Bar Settings","Shop By Category Settings","Sections Settings","Contacts Us Settings","Stripe Settings"]; 
                 
                // ** END Sections class
                
                // ......................COLLECT ROLES........................ \\
                foreach($allSidBarSections as $key => $section){
                    $permissions   = [];
                    foreach($section as $obj){
                        $sectionTap    = [];
                        $taps          = [];
                        // if(isset($treeChildFirst[$obj])){
                        //     $treeChildPermissions = [];
                        //     foreach($treeChildFirst[$obj] as $child_obj){
                        //         if(isset($treeChildSecond[$child_obj])){
                        //             $treeChildSecondPermissions = [];
                        //             foreach($treeChildSecond[$child_obj] as $child_obj_2){
                        //                 $treeChildSecondPermissions[] = [
                        //                     "title"       => $child_obj_2 ,    
                        //                     "checked"     => false ,    
                        //                 ];
                        //             }
                        //             $treeChildPermissions[] = [
                        //                 "title"       => $child_obj,    
                        //                 "checked"     => false ,    
                        //                 "permissions" => $treeChildSecondPermissions ,    
                        //             ];
                        //         }else{
                        //             $treeChildPermissions[] = [
                        //                 "title"       => $child_obj ,    
                        //                 "checked"     => false ,    
                        //             ];
                        //         }
                        //     }
                        //     $permissions[] = [
                        //         "title"       => $obj ,    
                        //         "checked"     => false ,    
                        //         "permissions" => $treeChildPermissions ,    
                        //     ];
                        // }else{
                            if($obj == "Access Sales Price Group"){
                                $permissions[] = [
                                    "title"   => "access_default_selling_price" ,    
                                    "checked" => (in_array("access_default_selling_price",$list_sell_price_group))?true:true ,    
                                ];
                                foreach($selling_price_groups as $one){
                                    $permissions[] = [
                                        "title"   => $one->name ,    
                                        "checked" => (in_array($one->name,$list_sell_price_group))?true:false ,   
                                    ];
                                }
                            }else{
                                if( $obj != "Dashboard" ){
                                
                                    $sectionTap[] = [
                                        "title"   => $obj ,    
                                        "checked" => (in_array($obj,$old_role_permissions_all['sidebar']))?true:false ,    
                                    ];
                                
                                    $taps[] = [
                                        "title"   => "Side Bar",    
                                        "value"   => 0,    
                                        "section" => $sectionTap,    
                                    ] ;

                                    $tables       = [];
                                    $actions      = [];
                                    foreach($allPagesSections as $keys => $page){
                                        foreach($page as $ki => $title){
                                            if($title == $obj){
                                                $tables       = [];
                                                $actions      = [];
                                                if(isset($allActionSections[$title])){
                                                    $allActions   = $allActionSections[$title];
                                                    foreach($allActions as $obj_action){
                                                        $actions[] = [
                                                            "title"   => $obj_action ,    
                                                            "checked" => (in_array($obj_action,$old_role_permissions_all['action']))?true:false ,  
                                                        ];
                                                    }
                                                }
                                                if(isset($allTableSections[$title])){
                                                    $allTables    = $allTableSections[$title];
                                                    foreach($allTables as $obj_table){
                                                        $tables[] = [
                                                            "title"   => $obj_table ,    
                                                            "checked" => (in_array($obj_table,$old_role_permissions_all['table']))?true:false ,  
                                                        ];
                                                    }
                                                } 
                                            }
                                        }
                                    }

                                    $taps[] = [
                                        "title"   => "Table",    
                                        "value"   => 1,    
                                        "section" => $tables,    
                                    ] ; 
                                    $taps[] = [
                                        "title"   => "Actions",    
                                        "value"   => 2,    
                                        "section" => $actions,    
                                    ] ; 

                                    $permissions[] = [
                                        "title"    => $obj ,    
                                        "subTitle" => "View All " .$obj ,    
                                        "taps"     => $taps,   
                                    ];
                                }
                            }
                        // }
                    }
                    $data["steps"][]  = [
                        "icon"     => "bx:grid-alt",
                        "title"    => $key,
                        "subTitle" => "Setup Permissions For ".$key." Section",
                    ];
                    if($key == "Dashboard"){
                        $list_permission   = [];
                        foreach($section as $objDashboard){  
                            $list_permission[] = [
                                "title"   => $objDashboard,
                                "checked" => (in_array($objDashboard,$old_role_permissions_all['sidebar']))?true:false , 
                            ];
                        }
                        $data_line[$key][] = [
                                 "title"       => "Dashboard Management" ,    
                                 "checked"     => (in_array("Dashboard",$old_role_permissions_all['sideRole']))?true:false ,    
                                 "permissions" => $list_permission ,    
                        ];
                    }else{
                        $data_line[$key][] = [
                            "title"         => $key." Management",
                            "checked"       => (in_array($key,$old_role_permissions_all['sideRole']))?true:false , 
                            "permissions"   => $permissions, 
                        ];
                    }
                }
                 
                $data["global"] = $data_line;
                // ........................END_of_ROLE......................... \\
                // ...........................
           
            return $data;
        }catch(Exception $e){
            return false;
        }
    }
}
