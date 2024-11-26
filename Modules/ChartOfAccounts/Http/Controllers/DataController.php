<?php

namespace Modules\ChartOfAccounts\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Utils\ModuleUtil;
use Menu;

class DataController extends Controller
{

    public function superadmin_package()
    {
        return [
            [
                'name' => 'ChartofAccounts',
                'label' => "شجرة الحسابات",
                'default' => false
            ]
        ];
    }
    public function modifyAdminMenu()
    {

        $business_id = session()->get('user.business_id');
        $module_util = new ModuleUtil();
        $background_color = '#B61616 !important';
        // $is_mfg_enabled = (boolean)$module_util->hasThePermissionInSubscription($business_id, 'chartofaccounts');
        //  if ($is_mfg_enabled){
            // if(auth()->user()->can('chartofaccounts.view')){
                 Menu::modify('admin-sidebar-menu', function ($menu) use ($background_color) {
                 $menu->dropdown(
                  __('chartofaccounts::lang.chartofaccounts'),
                  function ($sub) {
                                      $sub->url(
                                              action('\Modules\ChartOfAccounts\Http\Controllers\ChartOfAccountsController@chart_view'),
                                              __('chartofaccounts::lang.chart_view'),
                                              ['icon' => 'fa fas fa-address-book', 'active' => request()->segment(2) == 'partners', 'style' => 'background-color:#fff !important']
                                            );

                                      $sub->url(
                                              action('\Modules\ChartOfAccounts\Http\Controllers\ChartOfAccountsController@index'),
                                              __('chartofaccounts::lang.cost-centers'),
                                              ['icon' => 'fa fas fa-user', 'active' => request()->segment(2) == 'assets', 'style' => 'background-color:#fff !important']
                                           );

                      $sub->url(
                          action('\Modules\ChartOfAccounts\Http\Controllers\ChartOfAccountsController@index'),
                         __('chartofaccounts::lang.journals'),
                          ['icon' => 'fa fas fa-user', 'active' => request()->segment(2) == 'assets', 'style' => 'background-color:#fff !important']
                      );


                      $sub->url(
                          action('\Modules\ChartOfAccounts\Http\Controllers\ChartOfAccountsController@index'),
                          __('chartofaccounts::lang.assets'),
                          ['icon' => 'fa fas fa-user', 'active' => request()->segment(2) == 'assets', 'style' => 'background-color:#fff !important']
                      );

                      $sub->url(
                          action('\Modules\ChartOfAccounts\Http\Controllers\ChartOfAccountsController@index'),
                          __('chartofaccounts::lang.accounts_routing'),
                          ['icon' => 'fa fas fa-user', 'active' => request()->segment(2) == 'assets', 'style' => 'background-color:#fff !important']
                      );

                                 },
                    ['icon' => 'fa fas  fa-users']
                  )->order(47);
              });
            // }
        // }

    }
    public function user_permissions()
    {
        return [
            [
                'value' => 'Partners.view',
                'label' =>  __('partners::lang.partner_view'),
                'default' => false
            ],
            [
                'value' => 'partner.create',
                'label' =>  __('partners::lang.partner_create'),
                'default' => false
            ],
            [
                'value' => 'partner.delete',
                'label' =>  __('partners::lang.partner_delete'),
                'default' => false
            ],
            [
                'value' => 'partner.edit',
                'label' => __('partners::lang.partner_edit'),
                'default' => false
            ],

            [
                'value' => 'partner.payment_view',
                'label' => __('partners::lang.payment_view'),
                'default' => false
            ],
            [
                'value' => 'partner.payment_edit',
                'label' => __('partners::lang.payment_edit'),
                'default' => false
            ],
            [
                'value' => 'partner.payment_delete',
                'label' => __('partners::lang.payment_delete'),
                'default' => false
            ],

        ];
    }
    public function index()
    {
        return view('chartofaccounts::index');
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('chartofaccounts::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('chartofaccounts::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('chartofaccounts::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        //
    }
}
