<?php

namespace App\Http\Middleware;

use  App\Http\Controllers\AccountTreeController;
use  App\Http\Controllers\ArchiveTransactionController;
use  App\Utils\ModuleUtil;
use Closure;
use Menu;

class AdminSidebarMenu
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
            if ($request->ajax()) {
                return $next($request);
            }
         
            if(\Auth::User()->language == 'en'){
                \App::setLocale('en');
            }else{
                \App::setlocale('ar');
            }
           Menu::create('admin-sidebar-menu', function ($menu) {
            $enabled_modules = !empty(session('business.enabled_modules')) ? session('business.enabled_modules') : [];


            $is_admin = auth()->user()->hasRole('Admin#' . session('business.id')) ? true : false;
            /*  dd(session('business.id'), auth()->user()->hasRole('Admin#' . session('business.id')));*/
 
       
            //Home  5
            $menu->url(action('HomeController@index'), __('home.home'), ['icon' => 'fa fas fa-home', 'active' => request()->segment(1) == 'home'])->order(5);
             
           
            //User management dropdown  10
            if (  request()->session()->get("user.id") == 1 || request()->session()->get("user.id") == 7 || request()->session()->get("user.id") == 8) {
                $menu->dropdown(
                    __('user.user_management'),
                    function ($sub) {
                        
                        if (auth()->user()->can('user.view') || auth()->user()->can('ReadOnly.views') || auth()->user()->can('admin_without.views')) {
                            $sub->url(
                                action('ManageUserController@index'),
                                __('user.users'),
                                ['icon' => 'fa fas fa-user', 'active' => request()->segment(1) == 'users' ,'style'=>'font-weight:bold']
                            );
                        }
                        if ( request()->session()->get("user.id") == 1) {
                            $sub->url(
                                action('RoleController@index'),
                                __('user.roles'),
                                ['icon' => 'fa fas fa-briefcase', 'active' => request()->segment(1) == 'roles','style'=>'font-weight:bold']
                            );
                        }
                    },
                    ['icon' => 'fa fas fa-users']
                )->order(10);
            }
         
            //Contacts dropdown 15
            if (auth()->user()->can('supplier.view') || 
                auth()->user()->can('customer.view')     || 
                auth()->user()->can('ReadOnly.views')    || 
                auth()->user()->can('admin_without.views') || 
                auth()->user()->can('warehouse.views')     ||
                auth()->user()->can('admin_supervisor.views') ||
                auth()->user()->can('SalesMan.views') ||
                auth()->user()->can('Accountant.views')
                
            ) {
                $menu->dropdown(
                    __('contact.contacts'),
                    function ($sub) {
                        if (auth()->user()->can('supplier.view') || auth()->user()->can('ReadOnly.views') || auth()->user()->can('admin_without.views') || auth()->user()->can('warehouse.views')) {
                            $sub->url(
                                action('ContactController@index', ['type' => 'supplier']),
                                __('report.supplier'),
                                ['icon' => 'fa fas fa-star', 'active' => request()->input('type') == 'supplier','style'=>'font-weight:bold']
                            );
                        }
                        if (auth()->user()->can('customer.view') || auth()->user()->can('ReadOnly.views')|| auth()->user()->can('admin_without.views') || auth()->user()->can('warehouse.views')) {
                            $sub->url(
                                action('ContactController@index', ['type' => 'customer']),
                                __('report.customer'),
                                ['icon' => 'fa fas fa-star', 'active' => request()->input('type') == 'customer','style'=>'font-weight:bold']
                            );
                            $sub->url(
                                action('CustomerGroupController@index'),
                                __('lang_v1.customer_groups'),
                                ['icon' => 'fa fas fa-users', 'active' => request()->segment(1) == 'customer-group','style'=>'font-weight:bold']
                            );
                        }
                        if (auth()->user()->can('supplier.create') || auth()->user()->can('customer.create') || auth()->user()->can('admin_without.views')  ) {
                            $sub->url(
                                action('ContactController@getImportContacts'),
                                __('lang_v1.import_contacts'),
                                ['icon' => 'fa fas fa-download', 'active' => request()->segment(1) == 'contacts' && request()->segment(2) == 'import','style'=>'font-weight:bold']
                            );
                        }

                        if(!empty(env('GOOGLE_MAP_API_KEY'))    ) {
                            $sub->url(
                                action('ContactController@contactMap'),
                                __('lang_v1.map'),
                                ['icon' => 'fa fas fa-map-marker-alt', 'active' => request()->segment(1) == 'contacts' && request()->segment(2) == 'map','style'=>'font-weight:bold']
                            );
                        }
                    
                    },
                    ['icon' => 'fa fas fa-address-book', 'id' => "tour_step4"]
                )->order(15);
            }

            //Products dropdown  20
            if (auth()->user()->can('product.view')  || auth()->user()->can('product.create')  ||
                auth()->user()->can('brand.view')    || auth()->user()->can('unit.view')       ||
                auth()->user()->can('category.view') || auth()->user()->can('brand.create')    ||
                auth()->user()->can('unit.create')   || auth()->user()->can('category.create') ||
                auth()->user()->can('admin_without.views') || auth()->user()->can('manufuctoring.views') ||
                auth()->user()->can('SalesMan.views') || auth()->user()->can('Accountant.views')||
                auth()->user()->can('warehouse.views') || auth()->user()->can('admin_supervisor.views')
                ) {
                $menu->dropdown(
                    __('sale.products'),
                    function ($sub) {
                        if (auth()->user()->can('product.view') || auth()->user()->can('admin_without.views') || auth()->user()->can('warehouse.views') || auth()->user()->can('manufuctoring.views')) {
                            $sub->url(
                                action('ProductController@index'),
                                __('lang_v1.list_products'),
                                ['icon' => 'fa fas fa-list', 'active' => request()->segment(1) == 'products' && request()->segment(2) == '','style'=>'font-weight:bold']
                            );
                        }
                        if (auth()->user()->can('product.create')|| auth()->user()->can('admin_without.views') || auth()->user()->can('manufuctoring.views') || auth()->user()->can('warehouse.views')) {
                            $sub->url(
                                action('ProductController@create'),
                                __('product.add_product'),
                                ['icon' => 'fa fas fa-plus-circle', 'active' => request()->segment(1) == 'products' && request()->segment(2) == 'create','style'=>'font-weight:bold']
                            );
                        }

                      
                        if (auth()->user()->can('product.view')|| auth()->user()->can('admin_without.views') || auth()->user()->can('warehouse.views')) {
                            $sub->url(
                                action('LabelsController@show',['product_id=null']),
                                __('barcode.print_labels'),
                                ['icon' => 'fa fas fa-barcode', 'active' => request()->segment(1) == 'labels' && request()->segment(2) == 'show']
                            );
                        }


                        if (auth()->user()->can('product.create')|| auth()->user()->can('admin_without.views') || auth()->user()->can('manufuctoring.views') ||auth()->user()->can('warehouse.views')) {
                            $sub->url(
                                action('VariationTemplateController@index'),
                                __('product.variations'),
                                ['icon' => 'fa fas fa-circle', 'active' => request()->segment(1) == 'variation-templates','style'=>'font-weight:bold']
                            );
                            if(auth()->user()->can('product.create') || !auth()->user()->can('warehouse.views') || !auth()->user()->can('manufuctoring.views') ){
                                $sub->url(
                                    action('ImportProductsController@index'),
                                    __('product.import_products'),
                                    ['icon' => 'fa fas fa-download', 'active' => request()->segment(1) == 'import-products','style'=>'font-weight:bold']
                                );
                            }
                        }
                        if (auth()->user()->can('product.create') || auth()->user()->can('admin_without.views') || auth()->user()->can('manufuctoring.views') || auth()->user()->can('manufuctoring.views')  || auth()->user()->can('warehouse.views')) {
                            $sub->url(
                                action('ProductController@OpeningProduct'),
                                __('product.FirstTerm'),
                                ['icon' => 'fa fas fa-circle', 'active' => request()->segment(1) == 'products' && request()->segment(2) == 'Opening_product','style'=>'font-weight:bold']
                            );
                          
                        }

                        if (auth()->user()->can('product.opening_stock')|| auth()->user()->can('admin_without.views') || auth()->user()->can('warehouse.views') || auth()->user()->can('manufuctoring.views')) {
                            $sub->url(
                                action('ImportOpeningStockController@index'),
                                __('lang_v1.import_opening_stock'),
                                ['icon' => 'fa fas fa-download', 'active' => request()->segment(1) == 'import-opening-stock','style'=>'font-weight:bold']
                            );
                        }
                      
                        if (auth()->user()->can('product.create')|| auth()->user()->can('admin_without.views') || auth()->user()->can('manufuctoring.views') ) {
                            $sub->url(
                                action('SellingPriceGroupController@index'),
                                __('lang_v1.selling_price_group'),
                                ['icon' => 'fa fas fa-circle', 'active' => request()->segment(1) == 'selling-price-group','style'=>'font-weight:bold']
                            );
                        }

                        if (auth()->user()->can('unit.view') || auth()->user()->can('unit.create')|| auth()->user()->can('admin_without.views') || auth()->user()->can('manufuctoring.views')|| auth()->user()->can('warehouse.views')) {
                            $sub->url(
                                action('UnitController@index'),
                                __('unit.units'),
                                ['icon' => 'fa fas fa-balance-scale', 'active' => request()->segment(1) == 'units','style'=>'font-weight:bold']
                            );
                        }

                        if (auth()->user()->can('category.view') || auth()->user()->can('category.create') || auth()->user()->can('admin_without.views')|| auth()->user()->can('manufuctoring.views')) {
                            $sub->url(
                                action('TaxonomyController@index') . '?type=product',
                                __('category.categories'),
                                ['icon' => 'fa fas fa-tags', 'active' => request()->segment(1) == 'taxonomies' && request()->get('type') == 'product','style'=>'font-weight:bold']
                            );
                        }

                        if (auth()->user()->can('brand.view') || auth()->user()->can('brand.create') || auth()->user()->can('admin_without.views') || auth()->user()->can('warehouse.views') || auth()->user()->can('manufuctoring.views')) {
                            $sub->url(
                                action('BrandController@index'),
                                __('brand.brands'),
                                ['icon' => 'fa fas fa-gem', 'active' => request()->segment(1) == 'brands','style'=>'font-weight:bold']
                            );
                        }

                        if (auth()->user()->can('brand.view') || auth()->user()->can('brand.create') || auth()->user()->can('admin_without.views') || auth()->user()->can('warehouse.views') || auth()->user()->can('manufuctoring.views')) {
                            $sub->url(
                                action('WarrantyController@index'),
                                __('lang_v1.warranties'),
                                ['icon' => 'fa fas fa-shield-alt', 'active' => request()->segment(1) == 'warranties','style'=>'font-weight:bold']
                            );
                        }

                       

                    },
                    ['icon' => 'fa fas fa-cubes', 'id' => 'tour_step5']
                )->order(20);
            }

            /* Product Gallery   21 */
            if(auth()->user()->can('product.gallary') || auth()->user()->can('admin_without.views') || auth()->user()->can('warehouse.views') ||  auth()->user()->can('manufuctoring.views') ||  auth()->user()->can('SalesMan.views') ||  auth()->user()->can('admin_supervisor.views')   ){
                $menu->dropdown(
                    __('lang_v1.inventory_'),
                    function ($sub) {
                            $sub->url(
                                action('ProductGallery@gallery'),
                                __('lang_v1.product_gallery'),
                                ['icon' => 'fa fas fa-list', 'active' => request()->segment(1)=='gallery' && request()->segment(2)=='gallery','style'=>'font-weight:bold' ]
                             );
                            $sub->url(
                                action('ProductGallery@stock_report'),
                                __('report.stock_report'),
                                ['icon' => 'fa fas fa-list', 'active' => request()->segment(1)=='gallery' && request()->segment(2)=='stock_report' ,'style'=>'font-weight:bold']
                            );
                             $sub->url( action('StocktackingController@index'),
                                __('lang_v1.Inventory_of_stores'),
                                 ['icon' => 'fa fas fa-plus-circle', 'active' => request()->segment(1) == 'stocktacking' && request()->segment(2) == null]
                            );
                         },
                    ['icon' => 'fa fas fa-book']
                )->order(21);
            }

            //Purchase dropdown   25
            if (in_array('purchases', $enabled_modules) && (auth()->user()->can('purchase.view') || auth()->user()->can('purchase.create') || auth()->user()->can('purchase.update') || auth()->user()->can('admin_without.views')|| auth()->user()->can('warehouse.views') ||  auth()->user()->can('manufuctoring.views') ||     auth()->user()->can('admin_supervisor.views')  )) {
                $menu->dropdown(
                    __('purchase.purchases'),
                    function ($sub) {
                        if (auth()->user()->can('purchase.view') ||     auth()->user()->can('manufuctoring.views') ||  auth()->user()->can('SalesMan.views') ||  auth()->user()->can('admin_supervisor.views') || auth()->user()->can('view_own_purchase')|| auth()->user()->can('admin_without.views') || auth()->user()->can('warehouse.views')) {
                            $sub->url(
                                action('PurchaseController@index'),
                                __('purchase.list_purchase'),
                                ['icon' => 'fa fas fa-list', 'active' => request()->segment(1) == 'purchases' && request()->segment(2) == null,'style'=>'font-weight:bold']
                            );
                        }
                       
                        if (auth()->user()->can('purchase_return.view')|| auth()->user()->can('admin_without.views') || auth()->user()->can('warehouse.views') ||  auth()->user()->can('manufuctoring.views') ||  auth()->user()->can('SalesMan.views') ||  auth()->user()->can('admin_supervisor.views') ) {
                            $sub->url(
                                action('PurchaseReturnController@index'),
                                __('lang_v1.list_purchase_return'),
                                ['icon' => 'fa fas fa-undo', 'active' => request()->segment(1) == 'purchase-return','style'=>'font-weight:bold']
                            );
                        }

                        if (auth()->user()->can('purchase_return.create') ||  auth()->user()->can('manufuctoring.views') ||  auth()->user()->can('SalesMan.views') ||  auth()->user()->can('admin_supervisor.views') ) {
                            $sub->url(
                                action('ReportController@getproductPurchaseReport'),
                                __('lang_v1.purchase_return'),
                                ['icon' => 'fa fas fa-undo', 'active' => request()->segment(2) == 'product-purchase-report' ,'style'=>'font-weight:bold']
                            );
                        }

                        if (auth()->user()->can('purchase.view') || auth()->user()->can('view_own_purchase')|| auth()->user()->can('admin_without.views') ||     auth()->user()->can('SalesMan.views') ||  auth()->user()->can('admin_supervisor.views') ) {
                            $sub->url(
                                action('StatusLiveController@index'),
                                __('home.Status Live'),
                                ['icon' => 'fa fas fa-list', 'active' => request()->segment(1) == 'status-live' && request()->segment(2) == null,'style'=>'font-weight:bold']
                            );
                        }
                    },
                    ['icon' => 'fa fas fa-shopping-cart', 'id' => 'tour_step6']
                )->order(25);
            }

            
            //Sell dropdown  30
            if ($is_admin || auth()->user()->hasAnyPermission(['sell.view','sell.create', 'manufuctoring.views' , 'direct_sell.access', 'view_own_sell_only', 'view_commission_agent_sell', 'access_shipping', 'access_own_shipping', 'access_commission_agent_shipping', 'access_sell_return'   ]) ||auth()->user()->can('admin_without.views') ) {
                $menu->dropdown(
                    __('sale.sale'),
                    function ($sub) use ($enabled_modules, $is_admin) {
                        if ( $is_admin || auth()->user()->can('admin_without.views') ||  auth()->user()->can('manufuctoring.views') ||  auth()->user()->can('SalesMan.views') ||  auth()->user()->can('admin_supervisor.views') ||   auth()->user()->hasAnyPermission(['sell.view', 'sell.create', 'direct_sell.access', 'view_own_sell_only', 'view_commission_agent_sell', 'access_shipping', 'access_own_shipping', 'access_commission_agent_shipping']) ) {
                            $sub->url(
                                action('SellController@index'),
                                __('lang_v1.all_sales'),
                                ['icon' => 'fa fas fa-list', 'active' => request()->segment(1) == 'sells' && request()->segment(2) == null,'style'=>'font-weight:bold']
                            );
                        }
                    
                        if (auth()->user()->can('list_QuatationApproved')|| auth()->user()->can('admin_without.views') || auth()->user()->can('manufuctoring.views') ||  auth()->user()->can('SalesMan.views') ||  auth()->user()->can('admin_supervisor.views')) {
                            $sub->url(
                                action('SellController@getApproved'),
                                __('lang_v1.list_drafts'),
                                ['icon' => 'fa fas fa-pen-square', 'active' => request()->segment(1) == 'sells' && request()->segment(2) == "QuatationApproved",'style'=>'font-weight:bold']
                            );
                        }
                        
                        if (auth()->user()->can('list_quotations')|| auth()->user()->can('admin_without.views')   || auth()->user()->can('manufuctoring.views') ||  auth()->user()->can('SalesMan.views') ||  auth()->user()->can('admin_supervisor.views')) {
                            $sub->url(
                                action('SellController@getQuotations'),
                                __('lang_v1.list_quotations'),
                                ['icon' => 'fa fas fa-pen-square', 'active' => request()->segment(1) == 'sells' && request()->segment(2) == "quotations",'style'=>'font-weight:bold']
                            );
                        }
                       
                        if (auth()->user()->can('list_drafts')|| auth()->user()->can('admin_without.views')   || auth()->user()->can('manufuctoring.views') ||  auth()->user()->can('SalesMan.views') ||  auth()->user()->can('admin_supervisor.views')) {
                            $sub->url(
                                action('SellController@getDrafts'),
                                __('lang_v1.list_drafts1'),
                                ['icon' => 'fa fas fa-pen-square', 'active' => request()->segment(1) == 'sells' && request()->segment(2) == "drafts",'style'=>'font-weight:bold']
                            );
                        }
                        
                        if (auth()->user()->can('access_sell_return')|| auth()->user()->can('admin_without.views')  || auth()->user()->can('manufuctoring.views') ||  auth()->user()->can('SalesMan.views') ||  auth()->user()->can('admin_supervisor.views')) {
                            $sub->url(
                                action('SellReturnController@index'),
                                __('lang_v1.list_sell_return'),
                                ['icon' => 'fa fas fa-undo', 'active' => request()->segment(1) == 'sell-return' && request()->segment(2) == null,'style'=>'font-weight:bold','style'=>'font-weight:bold']
                            );
                        }

                        
                        // if ($is_admin ||       auth()->user()->hasAnyPermission(['access_shipping', 'access_own_shipping', 'access_commission_agent_shipping']) ) {
                            $sub->url(
                                action('SellController@shipments'),
                                __('lang_v1.shipments'),
                                ['icon' => 'fa fas fa-truck', 'active' => request()->segment(1) == 'shipments','style'=>'font-weight:bold']
                            );
                        // }
                        if (auth()->user()->can('user.create')  ) {
                            $sub->url(
                                action('SalesCommissionAgentController@index'),
                                __('lang_v1.sales_commission_agents'),
                                ['icon' => 'fa fas fa-handshake', 'active' => request()->segment(1) == 'sales-commission-agents','style'=>'font-weight:bold']
                            );
                        }
                   
                        // if (auth()->user()->can('discount.access')) {
                            $sub->url(
                                action('DiscountController@index'),
                                __('lang_v1.discounts'),
                                ['icon' => 'fa fas fa-percent', 'active' => request()->segment(1) == 'discount','style'=>'font-weight:bold']
                            );
                        // }

                        if (in_array('subscription', $enabled_modules) && auth()->user()->can('direct_sell.access')) {
                            $sub->url(
                                action('SellPosController@listSubscriptions'),
                                __('lang_v1.subscriptions'),
                                ['icon' => 'fa fas fa-recycle', 'active' => request()->segment(1) == 'subscriptions','style'=>'font-weight:bold']
                            );
                        }

                        if (auth()->user()->can('sell.create')|| auth()->user()->can('admin_without.views')) {
                            $sub->url(
                                action('ImportSalesController@index'),
                                __('lang_v1.import_sales'),
                                ['icon' => 'fa fas fa-file-import', 'active' => request()->segment(1) == 'import-sales','style'=>'font-weight:bold']
                            );
                        }
                        if (auth()->user()->can('sell.create')|| auth()->user()->can('admin_without.views') || auth()->user()->can('admin_supervisor.views') || auth()->user()->can('SalesMan.views')) {
                            $sub->url(
                                action('QuotationController@index'),
                                __('lang_v1.terms'),
                                ['icon' => 'fa fas fa-file-import', 'active' => request()->segment(1) == 'sells' && request()->segment(2) == "terms",'style'=>'font-weight:bold']
                            );
                        }
                    },
                    ['icon' => 'fa fas fa-money-check-alt', 'id' => 'tour_step7']
                )->order(26);
            }

            // .....  pos ...  
            // if (auth()->user()->can('pos.view')  ){
                $menu->dropdown(
                    __('home.pos'),
                    function ($sub){
                        if(auth()->user()->can('home.List_pos')){
                                $sub->url(
                                        action('PosBranchController@index'),
                                        __('home.List_pos'),
                                        ['icon'=>'fa fas fa-list' , 'active' => request()->segment(1) == 'pos-branch' && request()->segment(2) == null ]
                                    );
                        }
                        if(auth()->user()->can('home.Create_pos')){
                            $sub->url(
                                action('PosBranchController@create'),
                                __('home.Create_pos'),
                                ['icon'=>'fa fas fa-list' , 'active' => request()->segment(1) == 'pos-branch' && request()->segment(2) == "create"]
                            );
                        }
                        if(auth()->user()->can('home.Go_To')){
                            $sub->url(
                                action('PosBranchController@Pos'),
                                __('home.Go_To'),
                                ['icon'=>'fa fas fa-list' , 'active' => request()->segment(1) == 'pos-branch' && request()->segment(2) == "go-to-pos"]
                            );
                        }
                    },
                    ['icon'=>'fa fas fa-window-maximize']
                )->order(27);
            // }

            // warehouse menu  in_array('Warehouse', $enabled_modules)
            if (auth()->user()->can('warehouse.view') || auth()->user()->can('admin_without.views')|| auth()->user()->can('warehouse.views') || auth()->user()->hasAnyPermission(['manufacturing_module']) || auth()->user()->can('admin_supervisor.views' )|| auth()->user()->can('manufuctoring.views' ) || auth()->user()->can('SalesMan.views' )){
                $menu->dropdown(
                    __('warehouse.warehouse'),
                    function ($sub){
                        if(auth()->user()->can('warehouse.view')|| auth()->user()->can('admin_without.views') || auth()->user()->can('warehouse.views')  || auth()->user()->can('manufuctoring.views') || auth()->user()->can('admin_supervisor.views' ) || auth()->user()->can('SalesMan.views' )){
                                $sub->url(
                                        action('WarehouseController@index'),
                                        __('warehouse.show_warehouse'),
                                        ['icon'=>'fa fas fa-list' , 'active' => request()->segment(1) == 'warehouse' && request()->segment(2) == "index",'style'=>'font-weight:bold' ]
                                    );
                        }
                        if(auth()->user()->can('warehouse.movement')|| auth()->user()->can('admin_without.views') || auth()->user()->can('warehouse.views' )|| auth()->user()->can('manufuctoring.views' )|| auth()->user()->can('admin_supervisor.views' ) || auth()->user()->can('SalesMan.views' )){
                            $sub->url(
                                action('WarehouseController@movement'),
                                __('warehouse.Warehouse_Movement'),
                                ['icon'=>'fa fas fa-list' , 'active' => request()->segment(1) == 'warehouse' && request()->segment(2) == "movement",'style'=>'font-weight:bold']
                            );
                        }
                        if(auth()->user()->can('warehouse.recieved')|| auth()->user()->can('admin_without.views') || auth()->user()->can('warehouse.views') || auth()->user()->can('manufuctoring.views')|| auth()->user()->can('admin_supervisor.views') || auth()->user()->can('SalesMan.views' )){
                                $sub->dropdown(
                                    __('recieved.recieved'),
                                    function ($sub){
                                        $sub->url(
                                            action('RecievedPageController@index'),
                                            __('recieved.show_recieved'),
                                            ['icon'=>'fa fas fa-list', 'active' => request()->segment(1) == 'recieved' && request()->segment(2) == "index",'style'=>'font-weight:bold !important']
                                        );
                                    },
                                    ['icon'=>'fa fa-retweet']
                                )->order(35);
                        }
                        if(auth()->user()->can('warehouse.delivered')|| auth()->user()->can('admin_without.views') || auth()->user()->can('warehouse.views')|| auth()->user()->can('manufuctoring.views') || auth()->user()->can('admin_supervisor.views') || auth()->user()->can('SalesMan.views' )){
                                $sub->dropdown(
                                    __('delivery.delivered'),
                                    function ($sub){
                                        $sub->url(
                                            action('DeliveryPageController@index'),
                                            __('delivery.show_delivered'),
                                            ['icon'=>'fa fas fa-list' , 'active' => request()->segment(1) == 'delivery' && request()->segment(2) == "index",'style'=>'font-weight:bold !important']
                                        );
                                    },
                                    ['icon'=>'fa fa-bars']
                                )->order(35);
                        }
                    
                        //Stock transfer dropdown  35
                        if (auth()->user()->can('purchase.view')|| auth()->user()->can('admin_without.views') || auth()->user()->can('warehouse.views')|| auth()->user()->can('manufuctoring.views')) {
                                $sub->dropdown(
                                    __('lang_v1.stock_transfers'),
                                    function ($sub) {
                                        if (auth()->user()->can('purchase.view')|| auth()->user()->can('admin_without.views') || auth()->user()->can('warehouse.views')|| auth()->user()->can('manufuctoring.views') || auth()->user()->can('admin_supervisor.views')) {
                                            $sub->url(
                                                action('StockTransferController@index'),
                                                __('lang_v1.list_stock_transfers'),
                                                ['icon' => 'fa fas fa-list', "Style" => "color:#f1f1f1 !important: " , 'active' => request()->segment(1) == 'stock-transfers' && request()->segment(2) == null,'style'=>'font-weight:bold !important']
                                            );
                                        }
                                        if (auth()->user()->can('purchase.create')|| auth()->user()->can('admin_without.views') || auth()->user()->can('warehouse.views')|| auth()->user()->can('manufuctoring.views') || auth()->user()->can('admin_supervisor.views')) {
                                            $sub->url(
                                                action('StockTransferController@create'),
                                                __('lang_v1.add_stock_transfer'),
                                                ['icon' => 'fa fas fa-plus-circle', 'active' => request()->segment(1) == 'stock-transfers' && request()->segment(2) == 'create','style'=>'font-weight:bold !important']
                                            );
                                        }
                                    },
                                    ['icon' => 'fa fas fa-truck']
                                )->order(35);
                        }
                        if (auth()->user()->can('warehouse.invetory')|| auth()->user()->can('admin_without.views') || !auth()->user()->can('warehouse.views')|| auth()->user()->can('manufuctoring.views')|| auth()->user()->can('manufuctoring.views')) {
                                    $sub->dropdown(
                                        __('lang_v1.Inventory_of_stores'),
                                        function ($sub) {
                                            if (auth()->user()->can('warehouse.invetory')|| auth()->user()->can('admin_without.views') || auth()->user()->can('warehouse.views')) {
                                                $sub->url( action('StocktackingController@index'),
                                                            __('lang_v1.Inventory_of_stores'),
                                                    ['icon' => 'fa fas fa-plus-circle', 'active' => request()->segment(1) == 'stocktacking' && request()->segment(2) == null]
                                                );
                                            }
                                            if (auth()->user()->can('warehouse.add_invetory')|| auth()->user()->can('admin_without.views') || auth()->user()->can('warehouse.views')) {
                                                $sub->url(
                                                    action('StocktackingController@create'),
                                                    __('lang_v1.Create_an_inventory_period'),
                                                    ['icon' => 'fa fas fa-plus-circle', 'active' => request()->segment(1) == 'stocktacking' && request()->segment(2) == 'create']
                                                );
                                            }
                                        },
                                        ['icon' => 'fa fas fa-warehouse']
                                    )->order(35);
                        }

                        if ((auth()->user()->can('warehouse.add_adjustment')|| auth()->user()->can('admin_without.views') ) && !auth()->user()->can("manufuctoring.views") && !auth()->user()->can("warehouse.views") ) {
                            $sub->dropdown(
                                __('stock_adjustment.stock_adjustment'),
                                function ($sub) {
                                    if (auth()->user()->can('purchase.view')|| auth()->user()->can('admin_without.views') || auth()->user()->can('warehouse.views')) {
                                        $sub->url(
                                            action('StockAdjustmentController@index'),
                                            __('stock_adjustment.list'),
                                            ['icon' => 'fa fas fa-list', 'active' => request()->segment(1) == 'stock-adjustments' && request()->segment(2) == null]
                                        );
                                    }
                                    if (auth()->user()->can('purchase.create')|| auth()->user()->can('admin_without.views') || auth()->user()->can('warehouse.views')) {
                                        $sub->url(
                                            action('StockAdjustmentController@create'),
                                            __('stock_adjustment.add'),
                                            ['icon' => 'fa fas fa-plus-circle', 'active' => request()->segment(1) == 'stock-adjustments' && request()->segment(2) == 'create']
                                        );
                                    }
                                },
                                ['icon' => 'fa fas fa-database']
                            )->order(40);
                        }
                    
                    },
                    ['icon'=>'fa fas fa-list']
                )->order(35);
            }

            

            // cash and bank   
            if (auth()->user()->can('cashandbank.view')  || auth()->user()->can('admin_without.views')  ){
                $menu->dropdown(
                    __('lang_v1.cash_and_bank'),
                    function ($sub){
                        if(auth()->user()->can('account.cash_list')|| auth()->user()->can('admin_without.views')){
                                $sub->url(
                                        action('AccountController@showCash'),
                                        __('lang_v1.cash_list'),
                                        ['icon'=>'fa fas fa-list' , 'active' => request()->segment(1) == 'account' && request()->segment(2) == "cash",'style'=>'font-weight:bold' ]
                                    );
                        }
                        if(auth()->user()->can('account.bank_list')|| auth()->user()->can('admin_without.views')){
                            $sub->url(
                                action('AccountController@showBank'),
                                __('lang_v1.bank_list'),
                                ['icon'=>'fa fas fa-list' , 'active' => request()->segment(1) == 'account' && request()->segment(2) == "bank",'style'=>'font-weight:bold']
                            );
                        }
                    },
                    ['icon'=>'fa fas fa-wallet']
                    )->order(36);
            }
                 
            if (in_array('stock_adjustment', $enabled_modules) && (auth()->user()->can('contact_bank.view') || auth()->user()->can('admin_without.views')|| auth()->user()->can('contact_bank.create')|| auth()->user()->can('cheque.view') || auth()->user()->can('cheque.create'))) {
               $menu->dropdown(__('home.Cheques'),function($sub){
                        if(auth()->user()->can('cheque.view')|| auth()->user()->can('admin_without.views')){
                            $sub->url(
                            action('General\CheckController@index'),
                            __('home.Cheque_list'),
                            ['icon' => 'fa fas fa-file-alt', 'active' => request()->segment(1) == 'cheque' && request()->segment(2) ==  null,'style'=>'font-weight:bold']
                            );
                        }
                        if(auth()->user()->can('cheque.create')|| auth()->user()->can('admin_without.views')){
                            $sub->url(
                            action('General\CheckController@add',["type" => "0"]),
                            __('home.Add_Cheque_in'),
                            ['icon' => 'fa fas fa-file-alt', 'active' => request()->segment(1) == 'cheque' && request()->segment(2) == 'add' && request()->input("type") == 0,'style'=>'font-weight:bold']
                            );
                            $sub->url(
                            action('General\CheckController@add' ,["type" => "1"]),
                            __('home.Add_Cheque_Out'),
                            ['icon' => 'fa fas fa-file-alt', 'active' => request()->segment(1) == 'cheque' && request()->segment(2) == 'add' && request()->input("type") == 1,'style'=>'font-weight:bold']
                            );
                        }
                        if(auth()->user()->can('contact_bank.view')|| auth()->user()->can('admin_without.views')|| auth()->user()->can('admin_supervisor.views')|| auth()->user()->can('SalesMan.views')){
                            $sub->url(
                                action('General\ContactBankController@index'),
                                    __('home.Contact_banks'),
                                    ['icon' => 'fa fas fa-file-alt', 'active' => request()->segment(1) == 'contact-banks' ,'style'=>'font-weight:bold']
                                );
                        }
                    },
                    ['icon' => 'fa fas fa-edit']
                )->order(29);
             
            }
            if (in_array('stock_adjustment', $enabled_modules) && (auth()->user()->can('payment_voucher.view') ||
                             auth()->user()->can('gournal_voucher.view')|| auth()->user()->can('admin_without.views') )) {
                $menu->dropdown(__('home.Vouchers'),function($sub){
                        if (auth()->user()->can('payment_voucher.view')|| auth()->user()->can('admin_without.views')) {
                            $sub->url(
                                action('General\PaymentVoucherController@index'),
                                __('home.Vouchers List'),
                                ['icon' => 'fa fas fa-file-alt', 'active' => request()->segment(1) == 'payment-voucher' && request()->segment(2) ==  null,'style'=>'font-weight:bold']
                                );
                        }
                        if (auth()->user()->can('payment_voucher.create')|| auth()->user()->can('admin_without.views')) {
                            $sub->url(
                                action('General\PaymentVoucherController@add',["type" => "1"]),
                                __('home.Receipt voucher'),
                                ['icon' => 'fa fas fa-file-alt', 'active' => request()->segment(1) == 'payment-voucher' && request()->segment(2) == 'add'  && request()->input("type") == 1,'style'=>'font-weight:bold']
                                );
                        }  
                        if (auth()->user()->can('payment_voucher.create')|| auth()->user()->can('admin_without.views')) {
                            $sub->url(
                            action('General\PaymentVoucherController@add',["type" => "0"]),
                            __('home.Payment Voucher'),
                            ['icon' => 'fa fas fa-file-alt', 'active' => request()->segment(1) == 'payment-voucher' && request()->segment(2) == 'add'  && request()->input("type") == 0,'style'=>'font-weight:bold']
                            );
                        } 
                        if (auth()->user()->can('daily_payment.view')|| auth()->user()->can('admin_without.views')) {
                                $sub->url(
                                    action('General\DailyPaymentController@index'),
                                    __('home.DailyPayment List'),
                                    ['icon' => 'fa fas fa-file-alt', 'active' => request()->segment(1) == 'daily-payment' && request()->segment(2) == null,'style'=>'font-weight:bold']
                                    );
                        }
                   
                        if (auth()->user()->can('gournal_voucher.view')|| auth()->user()->can('admin_without.views')) {
                            $sub->url(
                                action('General\GournalVoucherController@index' ),
                                    __('home.expenseJurnalist'),
                                    ['icon' => 'fa fas fa-file-alt', 'active' => request()->segment(1) == 'gournal-voucher' && request()->segment(2) == null,'style'=>'font-weight:bold']
                                    );
                        }    
                  
                    },
                    ['icon' => 'fa fas fa-edit']
                )->order(28);
                }
             

            //Accounts dropdown 50
            if (auth()->user()->can('account.view') && in_array('account', $enabled_modules)|| auth()->user()->can('admin_without.views')) {
                $menu->dropdown(
                    __('lang_v1.payment_accounts'),
                    function ($sub) {
                        if (auth()->user()->can('account.view') || auth()->user()->can('admin_without.views') || auth()->user()->can('admin_supervisor.views')|| auth()->user()->can('SalesMan.views')) {
                            $sub->url(
                                action('AccountController@index'),
                                __('account.list_accounts'),
                                ['icon' => 'fa fas fa-list', 'active' => request()->segment(1) == 'account' && request()->segment(2) == 'account','style'=>'font-weight:bold']
                            );
                        }
                        if (auth()->user()->can('account.balance_sheet')|| auth()->user()->can('admin_without.views') || auth()->user()->can('admin_supervisor.views')|| auth()->user()->can('SalesMan.views')){
                            $sub->url(
                                action('AccountReportsController@balanceSheet'),
                                __('account.balance_sheet'),
                                ['icon' => 'fa fas fa-book', 'active' => request()->segment(1) == 'account' && request()->segment(2) == 'balance-sheet','style'=>'font-weight:bold']
                            );
                        }
                        if (auth()->user()->can('account.trail_balance')|| auth()->user()->can('admin_without.views') || auth()->user()->can('admin_supervisor.views')|| auth()->user()->can('SalesMan.views')){
                                $sub->url(
                                    action('AccountReportsController@trialBalance'),
                                    __('account.trial_balance'),
                                    ['icon' => 'fa fas fa-balance-scale', 'active' => request()->segment(1) == 'account' && request()->segment(2) == 'trial-balance','style'=>'font-weight:bold']
                                );
                        }
                        if (auth()->user()->can('account.cash_flow')|| auth()->user()->can('admin_without.views')){
                                $sub->url(
                                    action('AccountController@cashFlow'),
                                    __('lang_v1.cash_flow'),
                                    ['icon' => 'fa fas fa-exchange-alt', 'active' => request()->segment(1) == 'account' && request()->segment(2) == 'cash-flow','style'=>'font-weight:bold']
                                );
                        }
                        if (auth()->user()->can('account.payment_account_report')|| auth()->user()->can('admin_without.views')){
                            $sub->url(
                                action('AccountReportsController@paymentAccountReport'),
                                __('account.payment_account_report'),
                                ['icon' => 'fa fas fa-file-alt', 'active' => request()->segment(1) == 'account' && request()->segment(2) == 'payment-account-report','style'=>'font-weight:bold']
                            );
                        }
                         if (auth()->user()->can('account.entries')|| auth()->user()->can('admin_without.views')){
                             $sub->url(
                                    action('General\EntriesController@index'),
                                    __('home.List Entries'),
                                    ['icon' => 'fa fas fa-file-alt', 'active' => request()->segment(1) == 'account' && request()->segment(2) == 'entries' && request()->segment(3) == 'list','style'=>'font-weight:bold']
                                );
                        }
                        if (auth()->user()->can('account.cost_center')|| auth()->user()->can('admin_without.views')){
                            $sub->url(
                                action('General\CostCenterController@index'),
                                __('home.Cost Center'),
                                ['icon' => 'fa fas fa-file-alt', 'active' => request()->segment(1) == 'account' && request()->segment(2) == 'cost-center','style'=>'font-weight:bold']
                            );
                        }
                      
                    },
                    ['icon' => 'fa fas fa-money-check-alt']
                )->order(50);
            }
    

            if(!$is_admin ){
                if(!auth()->user()->can("SalesMan.views") ){
              
               }
            }else{
                 //Reports dropdown 55
                if (auth()->user()->can('purchase_n_sell_report.view') || auth()->user()->can('contacts_report.view') || auth()->user()->can('admin_without.views')
                    || auth()->user()->can('stock_report.view') | auth()->user()->can('tax_report.view')
                    || auth()->user()->can('trending_product_report.view')|| !auth()->user()->can('SalesMan.views') || auth()->user()->can('sales_representative.view') || auth()->user()->can('register_report.view')
                    || auth()->user()->can('expense_report.view')) {
                    $menu->dropdown(
                        __('report.reports'),
                        function ($sub) use ($enabled_modules,$is_admin) {
                            if (auth()->user()->can('profit_loss_report.view')|| auth()->user()->can('admin_without.views')) {
                                $sub->url(
                                    action('ReportController@getProfitLoss'),
                                    __('report.profit_loss'),
                                    ['icon' => 'fa fas fa-file-invoice-dollar', 'active' => request()->segment(2) == 'profit-loss','style'=>'font-weight:bold']
                                );
                            }
                            $sub->url(
                                action('ReportController@getsells'),
                                __('lang_v1.product_sell_day') ,
                                ['icon' => 'fa fas fa-arrow-circle-down', 'active' => request()->segment(2) == 'getsells','style'=>'font-weight:bold']
                            );
    
                            if (config('constants.show_report_606') == true) {
                                $sub->url(
                                    action('ReportController@purchaseReport'),
                                    'Report 606 (' . __('lang_v1.purchase') . ')',
                                    ['icon' => 'fa fas fa-arrow-circle-down', 'active' => request()->segment(2) == 'purchase-report','style'=>'font-weight:bold']
                                );
                            }
    
    
    
                            if (config('constants.show_report_606') == true) {
                                $sub->url(
                                    action('ReportController@purchaseReport'),
                                    'Report 606 (' . __('lang_v1.purchase') . ')',
                                    ['icon' => 'fa fas fa-arrow-circle-down', 'active' => request()->segment(2) == 'purchase-report','style'=>'font-weight:bold']
                                );
                            }
    
                            if (config('constants.show_report_607') == true) {
                                $sub->url(
                                    action('ReportController@saleReport'),
                                    'Report 607 (' . __('business.sale') . ')',
                                    ['icon' => 'fa fas fa-arrow-circle-up', 'active' => request()->segment(2) == 'sale-report','style'=>'font-weight:bold']
                                );
                            }
                            if ((in_array('purchases', $enabled_modules) || in_array('add_sale', $enabled_modules) || in_array('pos_sale', $enabled_modules)) && auth()->user()->can('purchase_n_sell_report.view')) {
                                $sub->url(
                                    action('ReportController@getPurchaseSell'),
                                    __('report.purchase_sell_report'),
                                    ['icon' => 'fa fas fa-exchange-alt', 'active' => request()->segment(2) == 'purchase-sell','style'=>'font-weight:bold']
                                );
                            }
    
                            if (auth()->user()->can('tax_report.view')|| auth()->user()->can('admin_without.views')) {
                                $sub->url(
                                    action('ReportController@getTaxReport'),
                                    __('report.tax_report'),
                                    ['icon' => 'fa fas fa-percent', 'active' => request()->segment(2) == 'tax-report','style'=>'font-weight:bold']
                                );
                            }
                            if (auth()->user()->can('contacts_report.view')|| auth()->user()->can('admin_without.views')) {
                                $sub->url(
                                    action('ReportController@getCustomerSuppliers'),
                                    __('report.contacts'),
                                    ['icon' => 'fa fas fa-address-book', 'active' => request()->segment(2) == 'customer-supplier','style'=>'font-weight:bold']
                                );
                                $sub->url(
                                    action('ReportController@getCustomerGroup'),
                                    __('lang_v1.customer_groups_report'),
                                    ['icon' => 'fa fas fa-users', 'active' => request()->segment(2) == 'customer-group','style'=>'font-weight:bold']
                                );
                            }
                            if (auth()->user()->can('stock_report.view')|| auth()->user()->can('admin_without.views')) {
                                $sub->url(
                                    action('ReportController@getStockReport'),
                                    __('report.stock_report'),
                                    ['icon' => 'fa fas fa-hourglass-half', 'active' => request()->segment(2) == 'stock-report','style'=>'font-weight:bold']
                                );
                                if (session('business.enable_product_expiry') == 1) {
                                    $sub->url(
                                        action('ReportController@getStockExpiryReport'),
                                        __('report.stock_expiry_report'),
                                        ['icon' => 'fa fas fa-calendar-times', 'active' => request()->segment(2) == 'stock-expiry','style'=>'font-weight:bold']
                                    );
                                }
                                if (session('business.enable_lot_number') == 1) {
                                    $sub->url(
                                        action('ReportController@getLotReport'),
                                        __('lang_v1.lot_report'),
                                        ['icon' => 'fa fas fa-hourglass-half', 'active' => request()->segment(2) == 'lot-report','style'=>'font-weight:bold']
                                    );
                                }
    
                                if (in_array('stock_adjustment', $enabled_modules)) {
                                    $sub->url(
                                        action('ReportController@getStockAdjustmentReport'),
                                        __('report.stock_adjustment_report'),
                                        ['icon' => 'fa fas fa-sliders-h', 'active' => request()->segment(2) == 'stock-adjustment-report','style'=>'font-weight:bold']
                                    );
                                }
                            }
    
                            if (auth()->user()->can('trending_product_report.view')|| auth()->user()->can('admin_without.views')) {
                                $sub->url(
                                    action('ReportController@getTrendingProducts'),
                                    __('report.trending_products'),
                                    ['icon' => 'fa fas fa-chart-line', 'active' => request()->segment(2) == 'trending-products','style'=>'font-weight:bold']
                                );
                            }
    
                            if (auth()->user()->can('purchase_n_sell_report.view')|| auth()->user()->can('admin_without.views')) {
                                $sub->url(
                                    action('ReportController@itemsReport'),
                                    __('lang_v1.items_report'),
                                    ['icon' => 'fa fas fa-tasks', 'active' => request()->segment(2) == 'items-report','style'=>'font-weight:bold']
                                );
    
                                $sub->url(
                                    action('ReportController@getproductPurchaseReport'),
                                    __('lang_v1.product_purchase_report'),
                                    ['icon' => 'fa fas fa-arrow-circle-down', 'active' => request()->segment(2) == 'product-purchase-report','style'=>'font-weight:bold']
                                );
    
                                $sub->url(
                                    action('ReportController@getproductSellReport'),
                                    __('lang_v1.product_sell_report'),
                                    ['icon' => 'fa fas fa-arrow-circle-up', 'active' => request()->segment(2) == 'product-sell-report','style'=>'font-weight:bold']
                                );
    
                                $sub->url(
                                    action('ReportController@purchasePaymentReport'),
                                    __('lang_v1.purchase_payment_report'),
                                    ['icon' => 'fa fas fa-search-dollar', 'active' => request()->segment(2) == 'purchase-payment-report','style'=>'font-weight:bold']
                                );
    
                                $sub->url(
                                    action('ReportController@sellPaymentReport'),
                                    __('lang_v1.sell_payment_report'),
                                    ['icon' => 'fa fas fa-search-dollar', 'active' => request()->segment(2) == 'sell-payment-report','style'=>'font-weight:bold']
                                );
                                $sub->url(
                                    action('ReportController@reportSetting'),
                                    __('lang_v1.setting_report'),
                                    ['icon' => 'fa fas fa-gear', 'active' => request()->segment(2) == 'sell-payment-report','style'=>'font-weight:bold']
                                );
                            }
                            if (in_array('expenses', $enabled_modules) && auth()->user()->can('expense_report.view')|| auth()->user()->can('admin_without.views')) {
                                $sub->url(
                                    action('ReportController@getExpenseReport'),
                                    __('report.expense_report'),
                                    ['icon' => 'fa fas fa-search-minus', 'active' => request()->segment(2) == 'expense-report','style'=>'font-weight:bold']
                                );
                            }
                            if (auth()->user()->can('register_report.view')) {
                                $sub->url(
                                    action('ReportController@getRegisterReport'),
                                    __('report.register_report'),
                                    ['icon' => 'fa fas fa-briefcase', 'active' => request()->segment(2) == 'register-report','style'=>'font-weight:bold']
                                );
                            }
                            if (auth()->user()->can('sales_representative.view')|| auth()->user()->can('admin_without.views')) {
                                $sub->url(
                                    action('ReportController@getSalesRepresentativeReport'),
                                    __('report.sales_representative'),
                                    ['icon' => 'fa fas fa-user', 'active' => request()->segment(2) == 'sales-representative-report','style'=>'font-weight:bold']
                                );
                            }
    
    
                            if ($is_admin) {
                                $sub->url(
                                    action('ReportController@activityLog'),
                                    __('lang_v1.activity_log'),
                                    ['icon' => 'fa fas fa-user-secret', 'active' => request()->segment(2) == 'activity-log','style'=>'font-weight:bold']
                                );
                            }
    
    
                        },
                        ['icon' => 'fa fas fa-chart-bar', 'id' => 'tour_step8']
                    )->order(55);
                }
               
           }

            //Backup menu 60
            if (request()->session()->get("user.id") == 1 ) {
                $menu->url(action('BackUpController@index'), __('lang_v1.backup'), ['icon' => 'fa fas fa-hdd', 'active' => request()->segment(1) == 'backup'])->order(60);
            }

            //Modules menu 61
            if (auth()->user()->can('manage_modules')  ) {
                $menu->url(action('Install\ModulesController@index'), __('lang_v1.modules'), ['icon' => 'fa fas fa-plug', 'active' => request()->segment(1) == 'manage-modules','style'=>'font-weight:bold'])->order(61);
            }

            //Booking menu 65



            //Notification template menu
            // if (auth()->user()->can('send_notifications')) {
            //     $menu->url(action('NotificationTemplateController@index'), __('lang_v1.notification_templates'), ['icon' => 'fa fas fa-envelope', 'active' => request()->segment(1) == 'notification-templates'])->order(80);
            // }
          

            // if(!auth()->user()->can('SalesMan.views')){
            //Settings Dropdown
            if (auth()->user()->can('business_settings.access') ||
                auth()->user()->can('barcode_settings.access') ||
                 auth()->user()->can('invoice_settings.access') ||(
                auth()->user()->can('tax_rate.view') && !auth()->user()->can("SalesMan.views") )||(
                auth()->user()->can('tax_rate.create') && !auth()->user()->can("SalesMan.views") )||
                auth()->user()->can('access_package_subscriptions')
                || auth()->user()->can('admin_without.views')) {
                $menu->dropdown(
                    __('home.patterns'),
                    function ($sub) use ($enabled_modules) {
                        if (auth()->user()->can('business_settings.access') || auth()->user()->can('admin_without.views')) {
                             
                            $sub->url(
                                action('BusinessLocationController@index'),
                                __('business.business_locations'),
                                ['icon' => 'fa fas fa-map-marker', 'active' => request()->segment(1) == 'business-location','style'=>'font-weight:bold']
                            );
                        }
                        if (auth()->user()->can('business_settings.access') || auth()->user()->can('admin_without.views')) {
                            $sub->url(
                                action('PatternController@index'),
                                __('business.Define_patterns'),
                                ['icon' => 'fa fas fa-map-marker', 'active' => request()->segment(1) == 'patterns-list','style'=>'font-weight:bold']
                            );
                            $sub->url(
                                action('General\SystemAccountController@index'),
                                __('home.System Account'),
                                ['icon' => 'fa fas fa-cogs', 'active' => request()->segment(1) == 'account' && request()->segment(2) == 'system-account-list','style'=>'font-weight:bold' ]
                            );
                        }
                        
                    },
                    ['icon' => 'fa fa-object-ungroup', 'id' => 'tour_step3']
                )->order(85);
            }
            //Settings Dropdown
            if (auth()->user()->can('business_settings.access') ||
                auth()->user()->can('barcode_settings.access') ||
                 auth()->user()->can('invoice_settings.access') ||(
                auth()->user()->can('tax_rate.view') && !auth()->user()->can("SalesMan.views") )||(
                auth()->user()->can('tax_rate.create') && !auth()->user()->can("SalesMan.views") )||
                auth()->user()->can('access_package_subscriptions')
                || auth()->user()->can('admin_without.views') ) {
                    
                // if(!auth()->user()->can('SalesMan.views')){
                    $menu->dropdown(
                        __('business.settings'),
                        function ($sub) use ($enabled_modules) {
                            if (auth()->user()->can('business_settings.access')|| auth()->user()->can('admin_without.views')) {
                                $sub->url(
                                    action('BusinessController@getBusinessSettings'),
                                    __('business.business_settings'),
                                    ['icon' => 'fa fas fa-cogs', 'active' => request()->segment(1) == 'business', 'id' => "tour_step2"]
                                );
                                
                            }
                            
                            if (auth()->user()->can('invoice_settings.access')|| auth()->user()->can('admin_without.views')) {
                                $sub->url(
                                    action('InvoiceSchemeController@index'),
                                    __('invoice.invoice_settings'),
                                    ['icon' => 'fa fas fa-file', 'active' => in_array(request()->segment(1), ['invoice-schemes', 'invoice-layouts']),'style'=>'font-weight:bold']
                                );
                            }
                            if (auth()->user()->can('barcode_settings.access')|| auth()->user()->can('admin_without.views')) {
                                $sub->url(
                                    action('BarcodeController@index'),
                                    __('barcode.barcode_settings'),
                                    ['icon' => 'fa fas fa-barcode', 'active' => request()->segment(1) == 'barcodes','style'=>'font-weight:bold']
                                );
                            }
                            if (auth()->user()->can('access_printers')|| auth()->user()->can('admin_without.views')) {
                                $sub->url(
                                    action('PrinterController@index'),
                                    __('printer.receipt_printers'),
                                    ['icon' => 'fa fas fa-share-alt', 'active' => request()->segment(1) == 'printers','style'=>'font-weight:bold']
                                );
                            }
                             if (auth()->user()->can('access_printers')|| auth()->user()->can('admin_without.views')) {
                                $sub->url(
                                    action('Report\PrinterSettingController@index'),
                                    __('printer.Setting_printers'),
                                    ['icon' => 'fa fas fa-share-alt', 'active' => request()->segment(1) == 'printers' && request()->segment(2) == 'setting','style'=>'font-weight:bold']
                                );
                            }
                            if (auth()->user()->can('tax_rate.view') || auth()->user()->can('tax_rate.create')|| auth()->user()->can('admin_without.views')) {
                                $sub->url(
                                    action('TaxRateController@index'),
                                    __('tax_rate.tax_rates'),
                                    ['icon' => 'fa fas fa-bolt', 'active' => request()->segment(1) == 'tax-rates','style'=>'font-weight:bold']
                                );
                            }
    
    
                            if (in_array('types_of_service', $enabled_modules) && auth()->user()->can('access_types_of_service')|| auth()->user()->can('admin_without.views')) {
                                $sub->url(
                                    action('TypesOfServiceController@index'),
                                    __('lang_v1.types_of_service'),
                                    ['icon' => 'fa fas fa-user-circle', 'active' => request()->segment(1) == 'types-of-service','style'=>'font-weight:bold']
                                );
                            }
                            if (request()->session()->get("user.id") == 1) {
                                $sub->url(
                                    action('General\DeleteController@index'),
                                    __('home.delete_service'),
                                    ['icon' => 'fa fas fa-user-circle', 'active' => request()->segment(1) == 'delete-file','style'=>'font-weight:bold']
                                );
                            }
                        },
                        ['icon' => 'fa fas fa-cog', 'id' => 'tour_step3']
                    )->order(85);
                // }
            }

            if(request()->session()->get("user.id") == 1){
                $menu->dropdown(
                    __('lang_v1.log_file'),
                    function ($sub) use ($enabled_modules) {
                         
                        $sub->url(
                            action('ArchiveTransactionController@warranties'),
                            __('lang_v1.warranties'),
                            ['icon' => 'fa fas fa-cogs', 'active' => request()->segment(1) == 'warranty-log-file', 'id' => "log-file-warranty",'style'=>'font-weight:bold']
                        );
                        $sub->url(
                            action('ArchiveTransactionController@users_activations'),
                            __('home.users_active'),
                            ['icon' => 'fa fas fa-cogs', 'active' => request()->segment(1) == 'user-log-file', 'id' => "log-file-user",'style'=>'font-weight:bold']
                        );
                        $sub->url(
                            action('ArchiveTransactionController@transaction_activations'),
                            __('home.bill_active'),
                            ['icon' => 'fa fas fa-cogs', 'active' => request()->segment(1) == 'bill-log-file', 'id' => "log-file-bill",'style'=>'font-weight:bold']
                        );
                            
                       
                    },
                    ['icon' => 'fa fas fa-file', 'id' => 'log-file']
                )->order(86); 
            }
            if(request()->session()->get("user.id") == 1){
                $menu->dropdown(
                    __('lang_v1.user_activation'),
                    function ($sub) use ($enabled_modules) {
                         
                        $sub->url(
                            action('UserActivationController@index'),
                            __('lang_v1.list_of_user'),
                            ['icon' => 'fa fas fa-cogs', 'active' => request()->segment(1) == 'user-activation', 'id' => "user-activation",'style'=>'font-weight:bold']
                        );
                        $sub->url(
                            action('UserActivationController@create'),
                            __('lang_v1.create_user'),
                            ['icon' => 'fa fas fa-cogs', 'active' => request()->segment(1) == 'user-activation' && request()->segment(2) == 'create', 'id' => "user-activation-create",'style'=>'font-weight:bold']
                        );
                        $sub->url(
                            action('UserActivationController@shows'),
                            __('lang_v1.list_of_Request'),
                            ['icon' => 'fa fas fa-cogs', 'active' => request()->segment(1) == 'user-activation'&& request()->segment(2) == 'show', 'id' => "user-activation-show",'style'=>'font-weight:bold']
                        );
                        $sub->url(
                            action('UserActivationController@login'),
                            __('lang_v1.list_of_Register'),
                            ['icon' => 'fa fas fa-cogs', 'active' => request()->segment(1) == 'user-activation'&& request()->segment(2) == 'show', 'id' => "user-activation-show",'style'=>'font-weight:bold']
                        );
                         
                         
                            
                       
                    },
                    ['icon' => 'fa fas fa-user-check', 'id' => 'user-activate']
                )->order(86); 
            }
         
            if(request()->session()->get("user.id") == 1){
                $menu->dropdown(
                    __('home.Mobile_Section'),
                    function ($sub) use ($enabled_modules) {
                    if (auth()->user()->can('business_settings.access')|| auth()->user()->can('admin_without.views')) {
                        $sub->url(
                            action('ApimobileController@getApiList'),
                            __('Mobile List'),
                            ['icon' => 'fa fas fa-cogs', 'active' => request()->segment(1) == 'get-api', 'id' => "app_list" ,'style' => 'font-weight:bold']
                        );
                    }
                }
                ,['icon' => 'fa fas fa-mobile', 'id' => 'mobile-app']
                )->order(87);
            }

            if(request()->session()->get("user.id") == 1){
                $menu->dropdown(
                     __('home.React_Section'),
                    function ($sub) use ($enabled_modules) {
                        if (auth()->user()->can('business_settings.access')|| auth()->user()->can('admin_without.views')) {
                            $sub->url(
                                action('ReactFrontController@getApiList'),
                                __('React List'),
                                ['icon' => 'fa fas fa-cogs', 'active' => request()->segment(1) == 'Rct', 'id' => "app1_list" ,'style' => 'font-weight:bold']
                            );
                        }
                    } 
                    ,['icon' => 'fab fa-react', 'id' => 'react-app']
                    )->order(87);
                }
                
        });
        // Add menus from modules
        $moduleUtil = new ModuleUtil;
        $moduleUtil->getModuleData('modifyAdminMenu');


        return $next($request);
    }
}
