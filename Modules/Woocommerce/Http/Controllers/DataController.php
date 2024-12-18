<?php

namespace Modules\Woocommerce\Http\Controllers;

use App\Utils\ModuleUtil;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Artisan;
use Menu;

class DataController extends Controller
{
    public function dummy_data()
    {
        Artisan::call('db:seed', ["--class" => 'Modules\Woocommerce\Database\Seeders\AddDummySyncLogTableSeeder']);
    }

    public function superadmin_package()
    {
        return [
            [
                'name' => 'woocommerce_module',
                'label' => __('woocommerce::lang.woocommerce_module'),
                'default' => false
            ]
        ];
    }

    /**
     * Defines user permissions for the module.
     * @return array
     */
    public function user_permissions()
    {
        return [
            [
                'value' => 'woocommerce.syc_categories',
                'label' => __('woocommerce::lang.sync_product_categories'),
                'default' => false
            ],
            [
                'value' => 'woocommerce.sync_products',
                'label' => __('woocommerce::lang.sync_products'),
                'default' => false
            ],
            [
                'value' => 'woocommerce.sync_orders',
                'label' => __('woocommerce::lang.sync_orders'),
                'default' => false
            ],
            [
                'value' => 'woocommerce.map_tax_rates',
                'label' => __('woocommerce::lang.map_tax_rates'),
                'default' => false
            ],
            [
                'value' => 'woocommerce.access_woocommerce_api_settings',
                'label' => __('woocommerce::lang.access_woocommerce_api_settings'),
                'default' => false
            ],

        ];
    }

    /**
     * Parses notification message from database.
     * @return array
     */
    public function parse_notification($notification)
    {
        $notification_data = [];
        if ($notification->type ==
            'Modules\Woocommerce\Notifications\SyncOrdersNotification') {
            $msg = __('woocommerce::lang.orders_sync_notification');

            $notification_data = [
                'msg' => $msg,
                'icon_class' => "fas fa-sync bg-light-blue",
                'link' =>  action('SellController@index'),
                'read_at' => $notification->read_at,
                'created_at' => $notification->created_at->diffForHumans()
            ];
        }

        return $notification_data;
    }

    /**
     * Returns product form part path with required extra data.
     *
     * @return array
     */
    public function product_form_part()
    {
        $path = 'woocommerce::woocommerce.partials.product_form_part';

        $business_id = request()->session()->get('user.business_id');

        $module_util = new ModuleUtil();
        $is_woo_enabled = (boolean)$module_util->hasThePermissionInSubscription($business_id, 'woocommerce_module', 'superadmin_package');
        if ($is_woo_enabled) {
            return  [
                'template_path' => $path,
                'template_data' => []
            ];
        } else {
            return [];
        }
    }

    /**
     * Returns products table extra columns for this module
     *
     * @return array
     */
    public function product_form_fields()
    {
        return ['woocommerce_disable_sync'];
    }

    /**
     * Adds Woocommerce menus
     * @return null
     */
    public function modifyAdminMenu()
    {
        $module_util = new ModuleUtil();
        
        if (auth()->user()->can('superadmin')) {
            $is_woo_enabled = $module_util->isModuleInstalled('Woocommerce');
        } else {
            $business_id = session()->get('user.business_id');
            $is_woo_enabled = (boolean)$module_util->hasThePermissionInSubscription($business_id, 'woocommerce_module', 'superadmin_package');
        }
        if ($is_woo_enabled && (auth()->user()->can('woocommerce.syc_categories') || auth()->user()->can('woocommerce.sync_products') || auth()->user()->can('woocommerce.sync_orders') || auth()->user()->can('woocommerce.map_tax_rates') || auth()->user()->can('woocommerce.access_woocommerce_api_settings'))) {
            Menu::modify('admin-sidebar-menu', function ($menu) {
                $menu->dropdown(
                    __('woocommerce::lang.woocommerce'),
                    function ($sub) {
                        $sub->url(
                            action('\Modules\Woocommerce\Http\Controllers\WoocommerceController@connectWebsite'),
                            __('woocommerce::lang.connection_website'),
                            ['icon' => 'fa fas fa-sync', 'active' => request()->segment(1) == 'woocommerce' &&  request()->segment(2) == "website"]
                        );
                        // $sub->url(
                        //     action('\Modules\Woocommerce\Http\Controllers\WoocommerceController@index'),
                        //     __('woocommerce::lang.sync'),
                        //     ['icon' => 'fa fas fa-sync', 'active' => request()->segment(1) == 'woocommerce' && empty(request()->segment(2))]
                        // );
                        // $sub->url(
                        //     action('\Modules\Woocommerce\Http\Controllers\WoocommerceController@viewSyncLog'),
                        //     __('woocommerce::lang.sync_log'),
                        //     ['icon' => 'fa fas fa-history', 'active' => request()->segment(1) == 'woocommerce' && request()->segment(2) == 'view-sync-log']
                        // );

                        if (auth()->user()->can('woocommerce.access_woocommerce_api_settings')) {
                            // $sub->url(
                            //     action('\Modules\Woocommerce\Http\Controllers\WoocommerceController@apiSettings'),
                            //     __('woocommerce::lang.api_settings'),
                            //     ['icon' => 'fa fas fa-cogs', 'active' => request()->segment(1) == 'woocommerce' && request()->segment(2) == 'api-settings']
                            // );
                        }
                        if (auth()->user()->can('woocommerce.access_woocommerce_api_settings')) {
                            $sub->url(
                                action('\Modules\Woocommerce\Http\Controllers\WoocommerceController@AuthInfo'),
                                __('woocommerce::lang.auth_page'),
                                ['icon' => 'fa fas fa-cogs', 'active' => request()->segment(1) == 'woocommerce' && request()->segment(2) == 'auth-info']
                            );
                            $sub->url(
                                action('\Modules\Woocommerce\Http\Controllers\WoocommerceController@softwarePage'),
                                __('woocommerce::lang.software_page'),
                                ['icon' => 'fa fas fa-cogs', 'active' => request()->segment(1) == 'woocommerce' && request()->segment(2) == 'software']
                            );
                        }
                        if (auth()->user()->can('woocommerce.access_woocommerce_api_settings')) {
                            $sub->url(
                                action('\Modules\Woocommerce\Http\Controllers\WoocommerceController@productSettings'),
                                __('woocommerce::lang.product_settings'),
                                ['icon' => 'fa fas fa-cogs', 'active' => request()->segment(1) == 'woocommerce' && request()->segment(2) == 'products']
                            );
                            $sub->url(
                                action('\Modules\Woocommerce\Http\Controllers\WoocommerceController@sectionsSettings'),
                                __('woocommerce::lang.sections_settings'),
                                ['icon' => 'fa fas fa-cogs', 'active' => request()->segment(1) == 'woocommerce' && request()->segment(2) == 'sections']
                            );
                            $sub->url(
                                action('\Modules\Woocommerce\Http\Controllers\WoocommerceController@contactSettings'),
                                __('woocommerce::lang.contactus_settings'),
                                ['icon' => 'fa fas fa-cogs', 'active' => request()->segment(1) == 'woocommerce' && request()->segment(2) == 'contacts']
                            );
                            $sub->url(
                                action('\Modules\Woocommerce\Http\Controllers\WoocommerceController@accountsSettings'),
                                __('woocommerce::lang.accounts_settings'),
                                ['icon' => 'fa fas fa-cogs', 'active' => request()->segment(1) == 'woocommerce' && request()->segment(2) == 'accounts']
                            );
                            $sub->url(
                                action('\Modules\Woocommerce\Http\Controllers\WoocommerceController@getFloat'),
                                __('woocommerce::lang.floatingBar'),
                                ['icon' => 'fa fas fa-cogs', 'active' => request()->segment(1) == 'woocommerce' && request()->segment(2) == 'float']
                            );
                            $sub->url(
                                action('\Modules\Woocommerce\Http\Controllers\WoocommerceController@getStripeApi'),
                                __('woocommerce::lang.stripe_settings'),
                                ['icon' => 'fa fas fa-cogs', 'active' => request()->segment(1) == 'woocommerce' && request()->segment(2) == 'strip']
                            );
                            $sub->url(
                                action('\Modules\Woocommerce\Http\Controllers\WoocommerceController@getShop'),
                                __('woocommerce::lang.shop_settings'),
                                ['icon' => 'fa fas fa-cogs', 'active' => request()->segment(1) == 'woocommerce' && request()->segment(2) == 'shop']
                            );
                            $sub->url(
                                action('\Modules\Woocommerce\Http\Controllers\WoocommerceController@getBill'),
                                __('woocommerce::lang.bill'),
                                ['icon' => 'fa fas fa-cogs', 'active' => request()->segment(1) == 'woocommerce' && request()->segment(2) == 'bill']
                            );
                            $sub->url(
                                action('\Modules\Woocommerce\Http\Controllers\WoocommerceController@getCart'),
                                __('woocommerce::lang.cart'),
                                ['icon' => 'fa fas fa-cogs', 'active' => request()->segment(1) == 'woocommerce' && request()->segment(2) == 'cart']
                            );
                            $sub->url(
                                action('\Modules\Woocommerce\Http\Controllers\WoocommerceController@getLogo'),
                                __('woocommerce::lang.logo'),
                                ['icon' => 'fa fas fa-cogs', 'active' => request()->segment(1) == 'woocommerce' && request()->segment(2) == 'logo']
                            );
                        }
                    },
                    ['icon' => 'fab fa-wordpress' ]
                )->order(88);
            });
        }
    }
}
