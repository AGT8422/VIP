<?php

namespace Modules\IZOPOS\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Menu;
class DataController extends Controller
{
    public function modifyAdminMenu()
    {
        $background_color = '#fff !important';
        // if(auth()->user()->can('assets.view')){
              Menu::modify('admin-sidebar-menu', function ($menu) use ($background_color) {
                // $menu->url(
                //     action('\Modules\IZOPOS\Http\Controllers\IZOPOSController@index'),
                //     __('IZO POS'),
                //     ['icon' => 'fa fas fa-cube', 'active' => request()->segment(1) == 'Assets', 'style' => 'background-color:black']
                // )->order(6);
            
                $menu->dropdown(
                    __('IZO POS'),
                    function ($sub) {
                        // if (auth()->user()->can('supplier.view') || auth()->user()->can('ReadOnly.views') || auth()->user()->can('admin_without.views') || auth()->user()->can('warehouse.views')) {
                            $sub->url(
                                action('\Modules\IZOPOS\Http\Controllers\IZOPOSController@closeBox'),
                                __('Close Box'),
                                ['icon' => 'fa fas fa-star', 'active' => request()->input('type') == 'supplier','style'=>'font-weight:bold']
                            );
                        // }
                        // if (auth()->user()->can('customer.view') || auth()->user()->can('ReadOnly.views')|| auth()->user()->can('admin_without.views') || auth()->user()->can('warehouse.views')) {
                            $sub->url(
                                action('\Modules\IZOPOS\Http\Controllers\IZOPOSController@salesReport'),
                                __('Sales Report'),
                                ['icon' => 'fa fas fa-star', 'active' => request()->input('type') == 'customer','style'=>'font-weight:bold']
                            );
                            $sub->url(
                                action('\Modules\IZOPOS\Http\Controllers\IZOPOSController@Bills'),
                                __('Bills'),
                                ['icon' => 'fa fas fa-users', 'active' => request()->segment(1) == 'customer-group','style'=>'font-weight:bold']
                            );
                            $sub->url(
                                action('\Modules\IZOPOS\Http\Controllers\IZOPOSController@POS'),
                                __('POS'),
                                ['icon' => 'fa fas fa-users', 'active' => request()->segment(1) == 'customer-group','style'=>'font-weight:bold']
                            );
                        // }
                    },
                    ['icon' => 'fa fas fa-address-book', 'id' => "tour_step4"]
                )->order(5);
        });
        // }
    }
}