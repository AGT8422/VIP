@extends('layouts.app')
@if($role->id)
    @section('title', __('role.edit_role'))
@else
  @section('title', __('role.add_role'))
@endif
@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        @if($role->id)
          <h1>@lang( 'role.edit_role' )</h1>
        @else
            <h1>@lang( 'role.add_role' )</h1>
        @endif

    </section>

    <!-- Main content -->
    <section class="content">
        @component('components.widget', ['class' => 'box-primary'])
            @if($role->id)
                {!! Form::open(['url' => action('RoleController@update', [$role->id]), 'id' => 'role_form' ]) !!}
                <input name="_method" type="hidden" value="PUT">
            @else
                {!! Form::open(['url' => action('RoleController@store'), 'method' => 'post', 'id' => 'role_add_form' ]) !!}
                <input name="_method" type="hidden" value="post">
            @endif

            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('name', __( 'user.role_name' ) . ':*') !!}
                        {!! Form::text('name', str_replace( '#' . auth()->user()->business_id, '', $role->name) , ['class' => 'form-control', 'required', 'placeholder' => __( 'user.role_name' ) ]); !!}
                    </div>
                </div>
            
                @if(in_array('service_staff', $enabled_modules))
                        <div class="col-md-4 text-center">
                            <div class="form-group">
                                <h4>@lang( 'lang_v1.user_type' )</h4>
                            </div>
                        </div>
                        <div class="col-md-4  ">
                            <div class="form-group">
                                <div class="col-md-12">
                                    <div class="checkbox">
                                        <label>
                                            {!! Form::checkbox('is_service_staff', 1, $role->is_service_staff,
                                            [ 'class' => 'input-icheck']); !!} {{ __( 'restaurant.service_staff' ) }}
                                        </label>
                                        @show_tooltip(__('restaurant.tooltip_service_staff'))
                                    </div>
                                </div>
                            </div>
                        </div>
                    
                @endif
            </div>
            <div class="row">
                <div class="col-md-3">
                    <label>@lang( 'user.permissions' ):</label>
                </div>
            </div>

            {{-- admin supervisor --}}
            <div class="row check_group">
                <div class="col-md-2">
                    <h4>@lang( 'role.SideBar Permission' )</h4>
                </div>
                <div class="col-md-2">
                    <div class="checkbox">
                        <label class="ch_all">  
                            <input type="checkbox" class="check_all input-icheck" > {{ __( 'role.select_all' ) }}
                        </label>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="col-md-12 text-left main_permission"> <!-- ** 1 ** -->
                        <div class="checkbox">
                            <label><b>
                                {!! Form::checkbox('permissions[]', 'sidBar.Dashboard', in_array('sidBar.Dashboard', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.Dashboard' ) }}
                            </b></label>
                        </div>
                    </div>
                    <div class="clearfix"></div><hr  style="width:100%;border: 1px solid #ee8835b4">
                    <div class="col-md-12  main_permission text-left"><!-- ** 2 ** -->
                        <div class="checkbox ">
                            <label><b>
                                {!! Form::checkbox('permissions[]', 'sidBar.UserManagement', in_array('sidBar.UserManagement', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.UserManagement' ) }}
                            </b></label>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="col-md-4"><!-- ** 3 ** -->
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'sidBar.Users', in_array('sidBar.Users', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.Users' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4"><!-- ** 4 ** -->
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'sidBar.Roles', in_array('sidBar.Roles', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.Roles' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="clearfix"></div><hr style="width:100%;border: 1px solid #ee8835b4">
                    <div class="col-md-12 text-left main_permission"><!-- ** 5 ** -->
                        <div class="checkbox">
                            <label><b>
                                {!! Form::checkbox('permissions[]', 'sidBar.Contacts', in_array('sidBar.Contacts', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.Contacts' ) }}
                            </b></label>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="col-md-4"><!-- ** 6 ** -->
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'sidBar.Suppliers', in_array('sidBar.Suppliers', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.Suppliers' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4"><!-- ** 7 ** -->
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'sidBar.Customers', in_array('sidBar.Customers', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.Customers' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4"><!-- ** 8 ** -->
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'sidBar.CustomerGroup', in_array('sidBar.CustomerGroup', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.CustomerGroup' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4"><!-- ** 9 ** -->
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'sidBar.ImportContact', in_array('sidBar.ImportContact', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.ImportContact' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <hr  style="width:100%;border: 1px solid #ee8835b4">
                    <div class="col-md-12 text-left main_permission"> <!-- ** 10 ** -->
                        <div class="checkbox">
                            <label><b>
                                {!! Form::checkbox('permissions[]', 'sidBar.Products', in_array('sidBar.Products', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.Products' ) }}
                            </b></label>
                        </div>
                    </div>
                    <div class="clearfix"></div> 
                    <div class="col-md-4"> <!-- ** 10 ** -->
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'sidBar.List_Product', in_array('sidBar.List_Product', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.List_Product' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4"><!-- ** 11 ** -->
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'sidBar.Add_Product', in_array('sidBar.Add_Product', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.Add_Product' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4"><!-- ** 12 ** -->
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'sidBar.Variations', in_array('sidBar.Variations', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.Variations' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4"><!-- ** 13 ** -->
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'sidBar.ImportContact', in_array('sidBar.ImportContact', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.ImportContact' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4"><!-- ** 14 ** -->
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'sidBar.Add_Opening_Stock', in_array('sidBar.Add_Opening_Stock', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.Add_Opening_Stock' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4"><!-- ** 15 ** -->
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'sidBar.Import_Opening_Stock', in_array('sidBar.Import_Opening_Stock', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.Import_Opening_Stock' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4"><!-- ** 16 ** -->
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'sidBar.Sale_Price_Group', in_array('sidBar.Sale_Price_Group', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.Sale_Price_Group' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4"><!-- ** 17 ** -->
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'sidBar.Units', in_array('sidBar.Units', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.Units' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4"><!-- ** 18 ** -->
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'sidBar.Categories', in_array('sidBar.Categories', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.Categories' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4"><!-- ** 19 ** -->
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'sidBar.Brands', in_array('sidBar.Brands', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.Brands' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4"><!-- ** 20 ** -->
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'sidBar.Warranties', in_array('sidBar.Warranties', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.Warranties' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="clearfix"></div><hr  style="width:100%;border: 1px solid #ee8835b4">
                    <div class="col-md-12 text-left main_permission"><!-- ** 21 ** -->
                        <div class="checkbox">
                            <label><b>
                                {!! Form::checkbox('permissions[]', 'sidBar.Inventory', in_array('sidBar.Inventory', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.Inventory' ) }}
                                </b>
                            </label>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="col-md-4"><!-- ** 22 ** -->
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'sidBar.Product_Gallery', in_array('sidBar.Product_Gallery', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.Product_Gallery' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4"><!-- ** 23 ** -->
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'sidBar.Inventory_Report', in_array('sidBar.Inventory_Report', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.Inventory_Report' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4"><!-- ** 24 ** -->
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'sidBar.Inventory_Of_Warehouse', in_array('sidBar.Inventory_Of_Warehouse', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.Inventory_Of_Warehouse' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <hr  style="width:100%;border: 1px solid #ee8835b4">
                    <div class="col-md-12 text-left main_permission"><!-- ** 25 ** -->
                        <div class="checkbox">
                            <label><b>
                                {!! Form::checkbox('permissions[]', 'sidBar.Manufacturing', in_array('sidBar.Manufacturing', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.Manufacturing' ) }}
                            </b></label>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="col-md-4"><!-- ** 26 ** -->
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'sidBar.Recipe', in_array('sidBar.Recipe', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.Recipe' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4"><!-- ** 27 ** -->
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'sidBar.Production', in_array('sidBar.Production', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.Production' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4"><!-- ** 28 ** -->
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'sidBar.Manufacturing_Report', in_array('sidBar.Manufacturing_Report', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.Manufacturing_Report' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <hr  style="width:100%;border: 1px solid #ee8835b4">
                    <div class="col-md-12 text-left main_permission"><!-- ** 29 ** -->
                        <div class="checkbox">
                            <label><b>
                                {!! Form::checkbox('permissions[]', 'sidBar.Purchases', in_array('sidBar.Purchases', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.Purchases' ) }}
                            </b></label>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="col-md-4  "><!-- ** 30 ** -->
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'sidBar.List_Purchases', in_array('sidBar.List_Purchases', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.List_Purchases' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4"><!-- ** 31 ** -->
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'sidBar.List_Return_Purchases', in_array('sidBar.List_Return_Purchases', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.List_Return_Purchases' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4"><!-- ** 32 ** -->
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'sidBar.Map', in_array('sidBar.Map', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.Map' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <hr  style="width:100%;border: 1px solid #ee8835b4">
                    <div class="col-md-12 text-left main_permission"><!-- ** 33 ** -->
                        <div class="checkbox">
                            <label><b>
                                {!! Form::checkbox('permissions[]', 'sidBar.Sales', in_array('sidBar.Sales', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.Sales' ) }}
                            </b></label>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="col-md-4"><!-- ** 34 ** -->
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'sidBar.List_Sales', in_array('sidBar.List_Sales', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.List_Sales' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4"><!-- ** 35 ** -->
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'sidBar.List_Approved_Quotation', in_array('sidBar.List_Approved_Quotation', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.List_Approved_Quotation' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4"><!-- ** 36 ** -->
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'sidBar.List_Quotation', in_array('sidBar.List_Quotation', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.List_Quotation' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4"><!-- ** 37 ** -->
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'sidBar.List_Draft', in_array('sidBar.List_Draft', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.List_Draft' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4"><!-- ** 38 ** -->
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'sidBar.List_Sale_Return', in_array('sidBar.List_Sale_Return', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.List_Sale_Return' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4"><!-- ** 39 ** -->
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'sidBar.Sales_Commission_Agent', in_array('sidBar.Sales_Commission_Agent', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.Sales_Commission_Agent' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4"><!-- ** 40 ** -->
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'sidBar.ImportSales', in_array('sidBar.ImportSales', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.ImportSales' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4"><!-- ** 41 ** -->
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'sidBar.Quotation_Terms', in_array('sidBar.Quotation_Terms', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.Quotation_Terms' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <hr  style="width:100%;border: 1px solid #ee8835b4">
                    <div class="col-md-12 text-left main_permission"><!-- ** 42 ** -->
                        <div class="checkbox">
                            <label><b>
                                {!! Form::checkbox('permissions[]', 'sidBar.Vouchers', in_array('sidBar.Vouchers', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.Vouchers' ) }}
                            </b></label>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="col-md-4"><!-- ** 43 ** -->
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'sidBar.List_Vouchers', in_array('sidBar.List_Vouchers', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.List_Vouchers' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4"><!-- ** 44 ** -->
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'sidBar.Add_Receipt_Voucher', in_array('sidBar.Add_Receipt_Voucher', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.Add_Receipt_Voucher' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4"><!-- ** 45 ** -->
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'sidBar.Add_Payment_Voucher', in_array('sidBar.Add_Payment_Voucher', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.Add_Payment_Voucher' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4"><!-- ** 46 ** -->
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'sidBar.List_Journal_Voucher', in_array('sidBar.List_Journal_Voucher', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.List_Journal_Voucher' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4"><!-- ** 47 ** -->
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'sidBar.List_Expense_Voucher', in_array('sidBar.List_Expense_Voucher', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.List_Expense_Voucher' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <hr  style="width:100%;border: 1px solid #ee8835b4">
                     <div class="col-md-12 text-left main_permission"><!-- ** 48 ** -->
                        <div class="checkbox">
                            <label><b>
                                {!! Form::checkbox('permissions[]', 'sidBar.Cheques', in_array('sidBar.Cheques', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.Cheques' ) }}
                            </b></label>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="col-md-4"><!-- ** 49 ** -->
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'sidBar.List_Cheque', in_array('sidBar.List_Cheque', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.List_Cheque' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4"><!-- ** 50 ** -->
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'sidBar.Add_Cheque_In', in_array('sidBar.Add_Cheque_In', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.Add_Cheque_In' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4"><!-- ** 51 ** -->
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'sidBar.Add_Cheque_Out', in_array('sidBar.Add_Cheque_Out', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.Add_Cheque_Out' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4"><!-- ** 52 ** -->
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'sidBar.Contact_Bank', in_array('sidBar.Contact_Bank', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.Contact_Bank' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <hr  style="width:100%;border: 1px solid #ee8835b4">
                    <div class="col-md-12 text-left main_permission"><!-- ** 53 ** -->
                        <div class="checkbox">
                            <label><b>
                                {!! Form::checkbox('permissions[]', 'sidBar.Cash_And_Bank', in_array('sidBar.Cash_And_Bank', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.Cash_And_Bank' ) }}
                            </b></label>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="col-md-4"><!-- ** 54 ** -->
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'sidBar.List_Cash', in_array('sidBar.List_Cash', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.List_Cash' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4"><!-- ** 55 ** -->
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'sidBar.List_Bank', in_array('sidBar.List_Bank', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.List_Bank' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <hr  style="width:100%;border: 1px solid #ee8835b4">
                    <div class="col-md-12 text-left main_permission"><!-- ** 56 ** -->
                        <div class="checkbox">
                            <label><b>
                                {!! Form::checkbox('permissions[]', 'sidBar.Accounts', in_array('sidBar.Accounts', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.Accounts' ) }}
                            </b></label>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="col-md-4"><!-- ** 57 ** -->
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'sidBar.List_Account', in_array('sidBar.List_Account', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.List_Account' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4"><!-- ** 58 ** -->
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'sidBar.Balance_Sheet', in_array('sidBar.Balance_Sheet', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.Balance_Sheet' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4"><!-- ** 59 ** -->
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'sidBar.Trial_Balance', in_array('sidBar.Trial_Balance', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.Trial_Balance' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4"><!-- ** 60 ** -->
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'sidBar.Cash_Flow', in_array('sidBar.Cash_Flow', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.Cash_Flow' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4"><!-- ** 61 ** -->
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'sidBar.Payment_Account_Report', in_array('sidBar.Payment_Account_Report', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.Payment_Account_Report' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4"><!-- ** 62 ** -->
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'sidBar.List_Entries', in_array('sidBar.List_Entries', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.List_Entries' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4"><!-- ** 63 ** -->
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'sidBar.Cost_Center', in_array('sidBar.Cost_Center', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.Cost_Center' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <hr  style="width:100%;border: 1px solid #ee8835b4">
                    <div class="col-md-12 text-left main_permission"><!-- ** 64 ** -->
                        <div class="checkbox">
                            <label><b>
                                {!! Form::checkbox('permissions[]', 'sidBar.Warehouses', in_array('sidBar.Warehouses', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.Warehouses' ) }}
                            </b></label>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="col-md-4"><!-- ** 65 ** -->
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'sidBar.List_Warehouses', in_array('sidBar.List_Warehouses', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.List_Warehouses' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4"><!-- ** 66 ** -->
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'sidBar.Warehouses_Movement', in_array('sidBar.Warehouses_Movement', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.Warehouses_Movement' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <hr  style="width:100%;border: 1px solid #ee8835b4">
                    <div class="col-md-12 text-left main_permission"><!-- ** 67 ** -->
                        <div class="checkbox">
                            <label><b>
                                {!! Form::checkbox('permissions[]', 'sidBar.Warehouse_Transafer', in_array('sidBar.Warehouse_Transafer', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.Warehouse_Transfer' ) }}
                            </b></label>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="col-md-4"><!-- ** 68 ** -->
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'sidBar.List_Warehouse_transfer', in_array('sidBar.List_Warehouse_transfer', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.List_Warehouse_transfer' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4"><!-- ** 69 ** -->
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'sidBar.Add_Warehouse_Transfer', in_array('sidBar.Add_Warehouse_Transfer', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.Add_Warehouse_Transfer' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4"><!-- ** 70 ** -->
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'sidBar.Delivered', in_array('sidBar.Delivered', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.Delivered' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4"><!-- ** 71 ** -->
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'sidBar.Received', in_array('sidBar.Received', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.Received' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <hr  style="width:100%;border: 1px solid #ee8835b4">
                    <div class="col-md-12 text-left main_permission"><!-- ** 64 ** -->
                        <div class="checkbox">
                            <label><b>
                                {!! Form::checkbox('permissions[]', 'sidBar.Reports', in_array('sidBar.Reports', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.Reports' ) }}
                            </b></label>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="col-md-4"><!-- ** 72 ** -->
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'sidBar.Profit_And_Loss_Report', in_array('sidBar.Profit_And_Loss_Report', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.Profit_And_Loss_Report' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4"><!-- ** 73 ** -->
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'sidBar.Daily_Product_Sale_Report', in_array('sidBar.Daily_Product_Sale_Report', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.Daily_Product_Sale_Report' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4"><!-- ** 74 ** -->
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'sidBar.Purchase_And_Sale_Report', in_array('sidBar.Purchase_And_Sale_Report', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.Purchase_And_Sale_Report' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4"><!-- ** 75 ** -->
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'sidBar.Tax_Reports', in_array('sidBar.Tax_Reports', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.Tax_Reports' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4"><!-- ** 76 ** -->
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'sidBar.Suppliers_And_Customers_Report', in_array('sidBar.Suppliers_And_Customers_Report', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.Suppliers_And_Customers_Report' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4"><!-- ** 78 ** -->
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'sidBar.Customers_Group_Report', in_array('sidBar.Customers_Group_Report', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.Customers_Group_Report' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4"><!-- ** 79 ** -->
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'sidBar.Inventory_Report', in_array('sidBar.Inventory_Report', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.Inventory_Report' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4"><!-- ** 80 ** -->
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'sidBar.Stock_Adjustment_Report', in_array('sidBar.Stock_Adjustment_Report', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.Stock_Adjustment_Report' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4"><!-- ** 82 ** -->
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'sidBar.Trending_Products_Report', in_array('sidBar.Trending_Products_Report', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.Trending_Products_Report' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4"><!-- ** 83 ** -->
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'sidBar.Items_Report', in_array('sidBar.Items_Report', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.Items_Report' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4"><!-- ** 84 ** -->
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'sidBar.Product_Purchase_Report', in_array('sidBar.Product_Purchase_Report', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.Product_Purchase_Report' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4"><!-- ** 85 ** -->
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'sidBar.Sale_Payment_Report', in_array('sidBar.Sale_Payment_Report', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.Sale_Payment_Report' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4"><!-- ** 86 ** -->
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'sidBar.Report_Setting', in_array('sidBar.Report_Setting', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.Report_Setting' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4"><!-- ** 87 ** -->
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'sidBar.Expense_Report', in_array('sidBar.Expense_Report', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.Expense_Report' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4"><!-- ** 88 ** -->
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'sidBar.Register_Report', in_array('sidBar.Register_Report', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.Register_Report' ) }}
                            </label>
                        </div>
                    </div>
                    {{-- <div class="col-md-4"><!-- ** 89 ** -->
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'Expense_Report', in_array('Expense_Report', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.Expense_Report' ) }}
                            </label>
                        </div>
                    </div> --}}
                    <div class="col-md-4"><!-- ** 90 ** -->
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'sidBar.Sales_Representative_Report', in_array('sidBar.Sales_Representative_Report', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.Sales_Representative_Report' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4"><!-- ** 91 ** -->
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'sidBar.Activity_Log', in_array('sidBar.Activity_Log', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.Activity_Log' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <hr  style="width:100%;border: 1px solid #ee8835b4">
                    <div class="col-md-12 text-left main_permission"><!-- ** 92 ** -->
                        <div class="checkbox">
                            <label><b>
                                {!! Form::checkbox('permissions[]', 'sidBar.Patterns', in_array('sidBar.Patterns', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.Patterns' ) }}
                            </b></label>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="col-md-4"><!-- ** 93 ** -->
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'sidBar.Business_locations', in_array('sidBar.Business_locations', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.Business_locations' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4"><!-- ** 94 ** -->
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'sidBar.Define_Patterns', in_array('sidBar.Define_Patterns', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.Define_Patterns' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4"><!-- ** 95 ** -->
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'sidBar.System_Accounts', in_array('sidBar.System_Accounts', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.System_Accounts' ) }}
                            </label>
                        </div>
                    </div>
                     
                    <div class="clearfix"></div>
                    <hr  style="width:100%;border: 1px solid #ee8835b4">
                    <div class="col-md-12 text-left main_permission"><!-- ** 96 ** -->
                        <div class="checkbox">
                            <label><b>
                                {!! Form::checkbox('permissions[]', 'sidBar.Settings', in_array('sidBar.Settings', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.Settings' ) }}
                            </b></label>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="col-md-4"><!-- ** 97 ** -->
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'sidBar.Invoice_Settings', in_array('sidBar.Invoice_Settings', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.Invoice_Settings' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4"><!-- ** 98 ** -->
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'sidBar.Barcode_Settings', in_array('sidBar.Barcode_Settings', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.Barcode_Settings' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4"><!-- ** 99 ** -->
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'sidBar.Product_Settings', in_array('sidBar.Product_Settings', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.Product_Settings' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4"><!-- ** 100 ** -->
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'sidBar.Receipt_Printer', in_array('sidBar.Receipt_Printer', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.Receipt_Printer' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4"><!-- ** 101 ** -->
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'sidBar.Tax_Rates', in_array('sidBar.Tax_Rates', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.Tax_Rates' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4"><!-- ** 102 ** -->
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'sidBar.Type_Of_Service', in_array('sidBar.Type_Of_Service', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.Type_Of_Service' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4"><!-- ** 103 ** -->
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'sidBar.Delete_Service', in_array('sidBar.Delete_Service', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.Delete_Service' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4"><!-- ** 104 ** -->
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'sidBar.Package_Subscription', in_array('sidBar.Package_Subscription', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.Package_Subscription' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <hr  style="width:100%;border: 1px solid #ee8835b4">
                    <div class="col-md-12 text-left main_permission"><!-- ** 105 ** -->
                        <div class="checkbox">
                            <label><b>
                                {!! Form::checkbox('permissions[]', 'sidBar.LogFile', in_array('sidBar.LogFile', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.LogFile' ) }}
                            </b></label>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="col-md-4"><!-- ** 106 ** -->
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'sidBar.logWarranties', in_array('lsidBar.ogWarranties', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.logWarranties' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4"><!-- ** 107 ** -->
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'sidBar.logUsers', in_array('sidBar.logUsers', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.logUsers' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4"><!-- ** 108 ** -->
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'sidBar.logBill', in_array('sidBar.logBill', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.logBill' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <hr  style="width:100%;border: 1px solid #ee8835b4">
                    <div class="col-md-12 text-left main_permission"><!-- ** 109 ** -->
                        <div class="checkbox">
                            <label><b>
                                {!! Form::checkbox('permissions[]', 'sidBar.User_Activation', in_array('sidBar.User_Activation', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.User_Activation' ) }}
                            </b></label>
                        </div>
                    </div>
                    <div class="col-md-4"><!-- ** 110 ** -->
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'sidBar.List_Of_Users', in_array('sidBar.List_Of_Users', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.List_Of_Users' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4"><!-- ** 111 ** -->
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'sidBar.List_Of_User_Request', in_array('sidBar.List_Of_User_Request', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.List_Of_User_Request' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4"><!-- ** 112 ** -->
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'sidBar.Create_New_User', in_array('sidBar.Create_New_User', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.Create_New_User' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4"><!-- ** 113 ** -->
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'sidBar.Mobile_Section', in_array('sidBar.Mobile_Section', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.Mobile_Section' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4"><!-- ** 114 ** -->
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'sidBar.React_section', in_array('sidBar.React_section', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.React_section' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4"><!-- ** 115 ** -->
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'sidBar.E_commerce', in_array('sidBar.E_commerce', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.E_commerce' ) }}
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            {{-- admin supervisor --}}
            <div class="row check_group">
                <div class="col-md-2">
                    <h4>@lang( 'role.admin supervisor' )</h4>
                </div>
                <div class="col-md-2">
                    <div class="checkbox">
                        <label class="ch_all">
                            <input type="checkbox" class="check_all input-icheck" > {{ __( 'role.select_all' ) }}
                        </label>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="col-md-4">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'admin_supervisor.views', in_array('admin_supervisor.views', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.View Permission' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'admin_supervisor.Add', in_array('admin_supervisor.Add', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.Add Permission' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'admin_supervisor.Edit', in_array('admin_supervisor.Edit', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.Edit Permission' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'admin_supervisor.Delete', in_array('admin_supervisor.Delete', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.Delete Permission' ) }}
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            {{-- admin without --}}
            <div class="row check_group">
                <div class="col-md-2">
                    <h4>@lang( 'role.admin without' )</h4>
                </div>
                <div class="col-md-2">
                    <div class="checkbox">
                        <label class="ch_all">
                            <input type="checkbox" class="check_all input-icheck" > {{ __( 'role.select_all' ) }}
                        </label>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="col-md-4">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'admin_without.views', in_array('admin_without.views', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.View Permission' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'admin_without.Add', in_array('admin_without.Add', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.Add Permission' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'admin_without.Edit', in_array('admin_without.Edit', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.Edit Permission' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'admin_without.Delete', in_array('admin_without.Delete', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.Delete Permission' ) }}
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            {{-- ReadOnly  --}}
            <div class="row check_group">
                <div class="col-md-2">
                    <h4>@lang( 'role.ReadOnly' )</h4>
                </div>
                <div class="col-md-2">
                    <div class="checkbox">
                        <label class="ch_all">
                            <input type="checkbox" class="check_all input-icheck" > {{ __( 'role.select_all' ) }}
                        </label>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="col-md-4">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'ReadOnly.views', in_array('ReadOnly.views', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.View Permission' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'ReadOnly.Add', in_array('ReadOnly.Add', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.Add Permission' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'ReadOnly.Edit', in_array('ReadOnly.Edit', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.Edit Permission' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'ReadOnly.Delete', in_array('ReadOnly.Delete', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.Delete Permission' ) }}
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            {{-- supervisor     --}}
            <div class="row check_group">
                <div class="col-md-2">
                    <h4>@lang( 'role.supervisor' )</h4>
                </div>
                <div class="col-md-2">
                    <div class="checkbox">
                        <label class="ch_all">
                            <input type="checkbox" class="check_all input-icheck" > {{ __( 'role.select_all' ) }}
                        </label>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="col-md-4">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'supervisor.views', in_array('supervisor.views', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.View Permission' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'supervisor.Add', in_array('supervisor.Add', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.Add Permission' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'supervisor.Edit', in_array('supervisor.Edit', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.Edit Permission' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'supervisor.Delete', in_array('supervisor.Delete', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.Delete Permission' ) }}
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            {{-- Accountant --}}
            <div class="row check_group">
                <div class="col-md-2">
                    <h4>@lang( 'role.Accountant' )</h4>
                </div>
                <div class="col-md-2">
                    <div class="checkbox">
                        <label class="ch_all">
                            <input type="checkbox" class="check_all input-icheck" > {{ __( 'role.select_all' ) }}
                        </label>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="col-md-4">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'Accountant.views', in_array('Accountant.views', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.View Permission' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'Accountant.Add', in_array('Accountant.Add', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.Add Permission' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'Accountant.Edit', in_array('Accountant.Edit', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.Edit Permission' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'Accountant.Delete', in_array('Accountant.Delete', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.Delete Permission' ) }}
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            {{-- sales --}}
            <div class="row check_group">
                <div class="col-md-2">
                    <h4>@lang( 'role.sales' )</h4>
                </div>
                <div class="col-md-2">
                    <div class="checkbox">
                        <label class="ch_all">
                            <input type="checkbox" class="check_all input-icheck" > {{ __( 'role.select_all' ) }}
                        </label>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="col-md-4">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'SalesMan.views', in_array('SalesMan.views', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.View Permission' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'SalesMan.Add', in_array('SalesMan.Add', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.Add Permission' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'SalesMan.Edit', in_array('SalesMan.Edit', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.Edit Permission' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'SalesMan.Delete', in_array('SalesMan.Delete', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.Delete Permission' ) }}
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            {{-- warehouse --}}
            <div class="row check_group">
                <div class="col-md-2">
                    <h4>@lang( 'role.warehouse' )</h4>
                </div>
                <div class="col-md-2">
                    <div class="checkbox">
                        <label class="ch_all">
                            <input type="checkbox" class="check_all input-icheck" > {{ __( 'role.select_all' ) }}
                        </label>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="col-md-4">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'warehouse.views', in_array('warehouse.views', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.View Permission' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'warehouse.Add', in_array('warehouse.Add', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.Add Permission' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'warehouse.Edit', in_array('warehouse.Edit', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.Edit Permission' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'warehouse.Delete', in_array('warehouse.Delete', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.Delete Permission' ) }}
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            {{-- manufuctoring --}}
            <div class="row check_group">
                <div class="col-md-2">
                    <h4>@lang( 'role.manufacturing' )</h4>
                </div>
                <div class="col-md-2">
                    <div class="checkbox">
                        <label class="ch_all">
                            <input type="checkbox" class="check_all input-icheck" > {{ __( 'role.select_all' ) }}
                        </label>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="col-md-4">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'manufuctoring.views', in_array('manufuctoring.views', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.View Permission' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'manufuctoring.Add', in_array('manufuctoring.Add', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.Add Permission' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'manufuctoring.Edit', in_array('manufuctoring.Edit', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.Edit Permission' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'manufuctoring.Delete', in_array('manufuctoring.Delete', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.Delete Permission' ) }}
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            {{-- User --}}
            <div class="row check_group">
                <div class="col-md-2">
                    <h4>@lang( 'role.user' )</h4>
                </div>
                <div class="col-md-2">
                    <div class="checkbox">
                        <label class="ch_all">
                            <input type="checkbox" class="check_all input-icheck" > {{ __( 'role.select_all' ) }}
                        </label>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="col-md-4">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'user.view', in_array('user.view', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.user.view' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'user.create', in_array('user.create', $role_permissions),[ 'class' => 'input-icheck']); !!} {{ __( 'role.user.create' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'user.update', in_array('user.update', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.user.update' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'user.delete', in_array('user.delete', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.user.delete' ) }}
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            {{-- Roles --}}
            <div class="row check_group">
                <div class="col-md-2">
                    <h4>@lang( 'user.roles' )</h4>
                </div>
                <div class="col-md-2">
                    <div class="checkbox">
                        <label class="ch_all">
                            <input type="checkbox" class="check_all input-icheck" > {{ __( 'role.select_all' ) }}
                        </label>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="col-md-4">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'roles.view', in_array('roles.view', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.view_role' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'roles.create', in_array('roles.create', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.add_role' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'roles.update', in_array('roles.update', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.edit_role' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'roles.delete', in_array('roles.delete', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.delete_role' ) }}
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            {{-- Supplier --}}
            <div class="row check_group">
                <div class="col-md-2">
                    <h4>@lang( 'role.supplier' )</h4>
                </div>
                <div class="col-md-2">
                    <div class="checkbox">
                        <label class="ch_all">
                            <input type="checkbox" class="check_all input-icheck" > {{ __( 'role.select_all' ) }}
                        </label>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="col-md-4">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'supplier.view', in_array('supplier.view', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.view_all_supplier' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'supplier.view_own', in_array('supplier.view_own', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.view_own_supplier' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'supplier.create', in_array('supplier.create', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.supplier.create' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'supplier.update', in_array('supplier.update', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.supplier.update' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'supplier.delete', in_array('supplier.delete', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.supplier.delete' ) }}
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            {{-- Customer --}}
            <div class="row check_group">
                <div class="col-md-2">
                    <h4>@lang( 'role.customer' )</h4>
                </div>
                <div class="col-md-2">
                    <div class="checkbox">
                        <label class="ch_all">
                            <input type="checkbox" class="check_all input-icheck" > {{ __( 'role.select_all' ) }}
                        </label>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="col-md-4">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'customer.view', in_array('customer.view', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.view_all_customer' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'customer.view_own', in_array('customer.view_own', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.view_own_customer' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'customer.create', in_array('customer.create', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.customer.create' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'customer.update', in_array('customer.update', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.customer.update' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'customer.delete', in_array('customer.delete', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.customer.delete' ) }}
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            {{-- Product --}}
            <div class="row check_group">
                <div class="col-md-2">
                    <h4>@lang( 'business.product' )</h4>
                </div>
                <div class="col-md-2">
                    <div class="checkbox">
                        <label class="ch_all">
                            <input type="checkbox" class="check_all input-icheck" > {{ __( 'role.select_all' ) }}
                        </label>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="col-md-4">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'product.view', in_array('product.view', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.product.view' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'product.view_sStock', in_array('product.view_sStock', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.View Stock' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4"> 
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'product.avarage_cost', in_array('product.avarage_cost', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.Average Cost' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'product.create', in_array('product.create', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.product.create' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'product.update', in_array('product.update', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.product.update' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'product.delete', in_array('product.delete', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.product.delete' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'product.opening_stock', in_array('product.opening_stock', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.add_opening_stock' ) }}
                            </label>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'stcok_compares', in_array('stcok_compares', $role_permissions),
                                [ 'class' => 'input-icheck']); !!}   {{ __( 'lang_v1.compare_store' ) }}
                            </label>
                        </div>
                    </div>



                    <div class="col-md-4">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'view_purchase_price', in_array('view_purchase_price', $role_permissions),['class' => 'input-icheck']); !!}
                                {{ __('lang_v1.view_purchase_price') }}
                            </label>
                            @show_tooltip(__('lang_v1.view_purchase_price_tooltip'))
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'delete_product_image', in_array('delete_product_image', $role_permissions),['class' => 'input-icheck']); !!}
                                {{ __('Delete Product Image') }}
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            {{-- Purchase --}}
            @if(in_array('purchases', $enabled_modules) || in_array('stock_adjustment', $enabled_modules) )
                <div class="row check_group">
                    <div class="col-md-2">
                        <h4>@lang( 'role.purchase' )</h4>
                    </div>
                    <div class="col-md-2">
                        <div class="checkbox">
                            <label class="ch_all">
                                <input type="checkbox" class="check_all input-icheck" > {{ __( 'role.select_all' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="col-md-4">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'purchase.view', in_array('purchase.view', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!}    {{ __( 'lang_v1.purchase_view' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'purchase.porduct_qty_setting', in_array('purchase.porduct_qty_setting', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.Adjusting_dead_stock' ) }}
                                </label>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'purchase.create', in_array('purchase.create', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.purchase.create' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'purchase.edit_composeit_discount', in_array('purchase.edit_composeit_discount', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.Modifying_purchase' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'purchase.update', in_array('purchase.update', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.purchase.update' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'purchase.delete', in_array('purchase.delete', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.purchase.delete' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'purchase.payments', in_array('purchase.payments', $role_permissions),['class' => 'input-icheck']); !!}
                                         {{ __( 'lang_v1.select_all' ) }}
                                </label>

                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'purchase_payment.edit', in_array('purchase_payment.edit', $role_permissions),['class' => 'input-icheck']); !!}
                                         {{ __( 'lang_v1.AddPaymentPurchase' ) }}
                                </label>

                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'purchase_payment.delete', in_array('purchase_payment.delete', $role_permissions),['class' => 'input-icheck']); !!}
                                         {{ __( 'lang_v1.EditPaymentPurchase' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'purchase.update_status', in_array('purchase.update_status', $role_permissions),['class' => 'input-icheck']); !!}
                                    {{ __('lang_v1.DeletePaymentPurchase') }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'view_own_purchase', in_array('view_own_purchase', $role_permissions),['class' => 'input-icheck']); !!}
                                    {{ __('lang_v1.view_own_purchase') }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'purchase_return.view', in_array('purchase_return.view', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!}    {{ __( 'lang_v1.viewReturnPurchase' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'purchase_return.create', in_array('purchase_return.create', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!}      {{ __( 'lang_v1.AddReturnPurchase' ) }}
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-----------------for stocktransfer -------------------->
                <div class="row check_group">
                    <div class="col-md-2">
                        <h4>تحويلات المخازن </h4>
                    </div>
                    <div class="col-md-2">
                        <div class="checkbox">
                            <label class="ch_all">
                                <input type="checkbox" class="check_all input-icheck" > {{ __( 'role.select_all' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="col-md-4">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'stock_transfer', in_array('stock_transfer', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!}عرض تحويلات المخازن
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'stock_transfer.create_pending', in_array('stock_transfer.create_pending', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} انشاء وتعديل  تحويل مخازن -طلب -
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'stock_transfer.create_confirmed', in_array('stock_transfer.create_confirmed', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} انشاء وتعديل  تحويل مخازن -  تأكيد-
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'stock_transfer.create_in_transit', in_array('stock_transfer.create_in_transit', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} انشاء وتعديل  تحويل مخازن - تسليم -

                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'stock_transfer.create_completed', in_array('stock_transfer.create_completed', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} انشاء وتعديل  تحويل مخازن - استلام --
                                </label>
                            </div>
                        </div>

                    </div>
                </div>

                <!-----------------for stocktacking -------------------->
                <div class="row check_group">
                    <div class="col-md-2">
                        <h4>جرد المخازن</h4>
                    </div>
                    <div class="col-md-2">
                        <div class="checkbox">
                            <label class="ch_all">
                                <input type="checkbox" class="check_all input-icheck" > {{ __( 'role.select_all' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="col-md-4">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'stocktacking.view', in_array('stocktacking.view', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} اظهار صفحة الجرد
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'stocktacking.show_qty_available', in_array('stocktacking.show_qty_available', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} اظهار الكمية الحالية أثناء الجرد
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'stocktacking.create', in_array('stocktacking.create', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} انشاء جرد
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'stocktacking.changeStatus', in_array('stocktacking.changeStatus', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} تغير حالة الجرد
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'stocktacking.products', in_array('stocktacking.products', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!}  جرد المنتجات
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'stocktacking.delete_form_stocktacking', in_array('stocktacking.delete_form_stocktacking', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!}  حذف منتج من الجرد
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'stocktacking.report', in_array('stocktacking.report', $role_permissions),['class' => 'input-icheck']); !!}
                                    عرض تقارير المنتجات المجرودة
                                </label>

                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'stocktacking.liquidation', in_array('stocktacking.liquidation', $role_permissions),['class' => 'input-icheck']); !!}
                                    عمل تصفية
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            {{--Sells Permissions--}}
            <div class="row check_group">
                <div class="col-md-2">
                    <h4>@lang( 'sale.sale' )</h4>
                </div>
                <div class="col-md-2">
                    <div class="checkbox">
                        <label class="ch_all">
                            <input type="checkbox" class="check_all input-icheck" > {{ __( 'role.select_all' ) }}
                        </label>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="col-md-4">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'sales.print_invoice', in_array('sales.print_invoice', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} طباعة الفاتورة
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'sales.pos_meswada', in_array('sales.pos_meswada', $role_permissions),
                                [ 'class' => 'input-icheck']); !!}     عمل مسودة
                            </label>
                        </div>
                    </div>
                        <div class="col-md-4">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'sales.edit_composite_discount', in_array('sales.edit_composite_discount', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!}      تعديل الخصم المركب في نقطة البيع
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'sales.price_offer', in_array('sales.price_offer', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!}    عمل عرض سعر
                                </label>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'sales.puse_sell', in_array('sales.puse_sell', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!}     تعليق عملية بيع
                                </label>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'sales.puse_show', in_array('sales.puse_show', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!}عرض عمليات البيع المعلقة
                                </label>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'sales.sell_agel', in_array('sales.sell_agel', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!}     بيع اجل
                                </label>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'sales.pay_card', in_array('sales.pay_card', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!}     بطاقة دفع
                                </label>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'sales.multi_pay_ways', in_array('sales.multi_pay_ways', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!}     طرق تحصيل متعددة
                                </label>
                            </div>
                        </div>


                        <div class="col-md-4">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'sales.sell_in_cash', in_array('sales.sell_in_cash', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!}     بيع كاش
                                </label>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'sales.less_than_purchase_price', in_array('sales.less_than_purchase_price', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!}     البيع باقل من سعر الشراء
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'sales.show', in_array('sales.show', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} عرض قائمة المبيعات
                                </label>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'sales.show_current_stock_in_pos', in_array('sales.show_current_stock_in_pos', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} عرض المخزون الحالي  في نقطة البيع
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'sales.show_purchase_price_in_pos', in_array('sales.show_purchase_price_in_pos', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} عرض سعر الشراء في نقطة البيع
                                </label>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'today_sells_total.show', in_array('today_sells_total.show', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} عرض الاجمالي اليومي
                                </label>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'sell.view', in_array('sell.view', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.sell.view' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'sell.installment', in_array('sell.installment', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} جميع الاقساط
                                </label>
                            </div>
                        </div>
                        @if(in_array('pos_sale', $enabled_modules))
                            <div class="col-md-4">
                                <div class="checkbox">
                                    <label>
                                        {!! Form::checkbox('permissions[]', 'sell.create', in_array('sell.create', $role_permissions),
                                        [ 'class' => 'input-icheck']); !!} {{ __( 'role.sell.create' ) }}
                                    </label>
                                </div>
                            </div>
                        @endif
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'sell.can_edit', in_array('sell.can_edit', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!}   {{ __( 'role.sell.can_edit' ) }}   
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'sell.update', in_array('sell.update', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.sell.update' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'sell.delete', in_array('sell.delete', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.sell.delete' ) }}
                                </label>
                            </div>
                        </div>
                        @if(in_array('add_sale', $enabled_modules))
                            <div class="col-md-4">
                                <div class="checkbox">
                                    <label>
                                        {!! Form::checkbox('permissions[]', 'direct_sell.access', in_array('direct_sell.access', $role_permissions),
                                        [ 'class' => 'input-icheck']); !!} {{ __( 'role.direct_sell.access' ) }}
                                    </label>
                                </div>
                            </div>
                        @endif
                        <div class="col-md-4">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'list_drafts', in_array('list_drafts', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.list_drafts' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'list_quotations', in_array('list_quotations', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.list_quotations' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'view_own_sell_only', in_array('view_own_sell_only', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.view_own_sell_only' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'sell.payments', in_array('sell.payments', $role_permissions),['class' => 'input-icheck']); !!}
                                    اضافة دفع بيع
                                </label>

                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'sell_payment.edit', in_array('sell_payment.edit', $role_permissions),['class' => 'input-icheck']); !!}
                                    تعديل دفع البيع
                                </label>

                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'sell_payment.delete', in_array('sell_payment.delete', $role_permissions),['class' => 'input-icheck']); !!}
                                    حذف دفغ البيع
                                </label>

                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'edit_product_price_from_sale_screen', in_array('edit_product_price_from_sale_screen', $role_permissions), ['class' => 'input-icheck']); !!}
                                    {{ __('lang_v1.edit_product_price_from_sale_screen') }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'edit_product_price_from_pos_screen', in_array('edit_product_price_from_pos_screen', $role_permissions), ['class' => 'input-icheck']); !!}
                                    {{ __('lang_v1.edit_product_price_from_pos_screen') }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'edit_product_discount_from_sale_screen', in_array('edit_product_discount_from_sale_screen', $role_permissions), ['class' => 'input-icheck']); !!}
                                    {{ __('lang_v1.edit_product_discount_from_sale_screen') }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'edit_product_discount_from_pos_screen', in_array('edit_product_discount_from_pos_screen', $role_permissions), ['class' => 'input-icheck']); !!}
                                    {{ __('lang_v1.edit_product_discount_from_pos_screen') }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'discount.access', in_array('discount.access', $role_permissions), ['class' => 'input-icheck']); !!}
                                    {{ __('lang_v1.discount.access') }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'access_shipping', in_array('access_shipping', $role_permissions), ['class' => 'input-icheck']); !!}
                                    {{ __('lang_v1.access_shipping') }}
                                </label>
                            </div>
                        </div>
                        @if(in_array('types_of_service', $enabled_modules))
                            <div class="col-md-4">
                                <div class="checkbox">
                                    <label>
                                        {!! Form::checkbox('permissions[]', 'access_types_of_service', in_array('access_types_of_service', $role_permissions),
                                        [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.access_types_of_service' ) }}
                                    </label>
                                </div>
                            </div>
                        @endif
                        <div class="col-md-4">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'access_sell_return', in_array('access_sell_return', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'مرتجع المبيعات' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'importsales.create', in_array('importsales.create', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} استيراد مبيعات من نظام اخر
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'recent_transaction.view', in_array('recent_transaction.view', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!}     اخر المعاملات في شاشة نقاط البيع
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'customer_balance_due_in_pos', in_array('customer_balance_due_in_pos', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!}    عرض الرصيد المستحق علي العميل في شاشة نقاط البيع
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'pos_lite',  in_array('pos_lite', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!}    نقطة بيع لايت
                                </label>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'pos_repair',  in_array('pos_repair', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!}    نقطة بيع الصيانة
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'all_sales_prices',  in_array('all_sales_prices', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} All Sales Prices
                                </label>
                            </div>
                        </div>
                </div>
            </div>
            {{--Expenses--}}
            <div class="row check_group">
                <div class="col-md-2">
                    <h4>@lang( 'role.expenses' )</h4>
                </div>
                <div class="col-md-2">
                    <div class="checkbox">
                        <label class="ch_all">
                            <input type="checkbox" class="check_all input-icheck" > {{ __( 'role.select_all' ) }}
                        </label>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="col-md-4">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'expenses.view',  in_array('expenses.view', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.expenses.view' ) }}
                            </label>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'expense.categories', in_array('expenses.categories', $role_permissions),
                                ['class' => 'input-icheck']); !!} {{ __('role.expenses.categories') }}
                            </label>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'expense.create', in_array('expenses.create', $role_permissions),
                                ['class' => 'input-icheck']); !!} {{ __('role.expenses.create') }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'expense.edit',  in_array('expenses.edit', $role_permissions),
                                ['class' => 'input-icheck']); !!} {{ __('role.expenses.edit') }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'expense.delete',  in_array('expenses.delete', $role_permissions),
                                ['class' => 'input-icheck']); !!} {{ __('role.expenses.delete') }}
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            {{-- End of Expense --}}
            {{-- Cash Register --}}
            <div class="row check_group">
                <div class="col-md-2">
                    <h4>@lang( 'cash_register.cash_register' )</h4>
                </div>
                <div class="col-md-2">
                    <div class="checkbox">
                        <label class="ch_all">
                            <input type="checkbox" class="check_all input-icheck" > {{ __( 'role.select_all' ) }}
                        </label>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="col-md-4">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'view_cash_register', in_array('view_cash_register', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.view_cash_register' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'close_cash_register', in_array('close_cash_register', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.close_cash_register' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'register_payment_details.view', in_array('register_payment_details.view', $role_permissions),
                                [ 'class' => 'input-icheck']); !!}    عرض تفاصيل الدفعات  في كشف الوردية
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'register_product_details.view',in_array('register_product_details.view', $role_permissions),
                                [ 'class' => 'input-icheck']); !!}     عرض تفاصيل المنتجات المباعة في كشف الوردية
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            {{-- Brand --}}
            <div class="row check_group">
                <div class="col-md-2">
                    <h4>@lang( 'role.brand' )</h4>
                </div>
                <div class="col-md-2">
                    <div class="checkbox">
                        <label class="ch_all">
                            <input type="checkbox" class="check_all input-icheck" > {{ __( 'role.select_all' ) }}
                        </label>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="col-md-4">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'brand.view', in_array('brand.view', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.brand.view' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'brand.create', in_array('brand.create', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.brand.create' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'brand.update', in_array('brand.update', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.brand.update' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'brand.delete', in_array('brand.delete', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.brand.delete' ) }}
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            {{-- Tax Rate --}}
            <div class="row check_group">
                <div class="col-md-2">
                    <h4>@lang( 'role.tax_rate' )</h4>
                </div>
                <div class="col-md-2">
                    <div class="checkbox">
                        <label class="ch_all">
                            <input type="checkbox" class="check_all input-icheck" > {{ __( 'role.select_all' ) }}
                        </label>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="col-md-4">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'tax_rate.view', in_array('tax_rate.view', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.tax_rate.view' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'tax_rate.create', in_array('tax_rate.create', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.tax_rate.create' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'tax_rate.update', in_array('tax_rate.update', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.tax_rate.update' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'tax_rate.delete', in_array('tax_rate.delete', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.tax_rate.delete' ) }}
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            {{-- Unit --}}
            <div class="row check_group">
                <div class="col-md-2">
                    <h4>@lang( 'role.unit' )</h4>
                </div>
                <div class="col-md-2">
                    <div class="checkbox">
                        <label class="ch_all">
                            <input type="checkbox" class="check_all input-icheck" > {{ __( 'role.select_all' ) }}
                        </label>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="col-md-4">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'unit.view', in_array('role.unit.view', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.unit.view' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'unit.create', in_array('role.unit.create', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.unit.create' ) }}
                                </label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'unit.update', in_array('role.unit.update', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.unit.update' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'unit.delete', in_array('role.unit.delete', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.unit.delete' ) }}
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row check_group">
                <div class="col-md-2">
                    <h4>@lang( 'category.category' )</h4>
                </div>
                <div class="col-md-2">
                    <div class="checkbox">
                        <label class="ch_all">
                            <input type="checkbox" class="check_all input-icheck" > {{ __( 'role.select_all' ) }}
                        </label>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="col-md-4">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'category.view', in_array('category.view', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.category.view' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'category.create', in_array('category.create', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.category.create' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'category.update', in_array('category.update', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.category.update' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'category.delete', in_array('category.delete', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.category.delete' ) }}
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row check_group">
                <div class="col-md-2">
                    <h4>@lang( 'role.report' )</h4>
                </div>
                <div class="col-md-2">
                    <div class="checkbox">
                        <label class="ch_all">
                            <input type="checkbox" class="check_all input-icheck" > {{ __( 'role.select_all' ) }}
                        </label>
                    </div>
                </div>
                <div class="col-md-8">
                    @if(in_array('purchases', $enabled_modules) || in_array('add_sale', $enabled_modules) || in_array('pos_sale', $enabled_modules))
                        <div class="col-md-4">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'purchase_n_sell_report.view', in_array('purchase_n_sell_report.view', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.purchase_n_sell_report.view' ) }}
                                </label>
                            </div>
                        </div>
                    @endif
                    <div class="col-md-4">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'tax_report.view', in_array('tax_report.view', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.tax_report.view' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'contacts_report.view', in_array('contacts_report.view', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.contacts_report.view' ) }}
                            </label>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'profit_loss_report.view', in_array('profit_loss_report.view', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.profit_loss_report.view' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'stock_report.view', in_array('stock_report.view', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.stock_report.view' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'stock_missing_report.view', in_array('stock_missing_report.view', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} تقرير النواقص
                            </label>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'trending_product_report.view', in_array('trending_product_report.view', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.trending_product_report.view' ) }}
                            </label>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'register_report.view', in_array('register_report.view', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} تقرير الوردية
                            </label>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'sales_representative.view', in_array('sales_representative.view', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.sales_representative.view' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'view_product_stock_value', in_array('view_product_stock_value', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.view_product_stock_value' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'less_trending_product_report.view', in_array('less_trending_product_report.view', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} المنتجات الراكدة
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'sell_purchase_lines_report.view', in_array('sell_purchase_lines_report.view', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} تقرير حركة صنف
                            </label>
                        </div>
                    </div>

                </div>
            </div>

            <div class="row check_group">
                <div class="col-md-2">
                    <h4>@lang( 'role.settings' )</h4>
                </div>
                <div class="col-md-2">
                    <div class="checkbox">
                        <label class="ch_all">
                            <input type="checkbox" class="check_all input-icheck" > {{ __( 'role.select_all' ) }}
                        </label>
                    </div>
                </div>
                <div class="col-md-8">

                    <div class="col-md-4">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'business_settings.backup_database', in_array('business_settings.backup_database', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} نسخ احتياطي
                            </label>
                        </div>
                    </div>


                    <div class="col-md-4">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'business_settings.access', in_array('business_settings.access', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.business_settings.access' ) }}
                            </label>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'barcode_settings.access', in_array('barcode_settings.access', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.barcode_settings.access' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'invoice_settings.access', in_array('invoice_settings.access', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.invoice_settings.access' ) }}
                            </label>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'access_printers', in_array('access_printers', $role_permissions),['class' => 'input-icheck']); !!}
                                {{ __('lang_v1.access_printers') }}
                            </label>
                        </div>
                    </div>
                </div>
            </div>
                
                
            {{--dashboard--}}
            <div class="row check_group">
                <div class="col-md-3">
                    <h4>@lang( 'role.dashboard' )</h4>
                </div>
                <div class="col-md-9">
                    <div class="col-md-4">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'dashboard.data', in_array('dashboard.data', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.dashboard.data' ) }}
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            {{--account.account--}}
            <div class="row check_group">
                <div class="col-md-4">
                    <h4>@lang( 'account.account' )</h4>
                </div>
                <div class="col-md-8">
                    <div class="col-md-4">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'account.access', in_array('account.access', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.access_accounts' ) }}
                            </label>
                        </div>
                    </div>
                </div>
            </div>
                 

                @if(in_array('booking', $enabled_modules))
                    <div class="row check_group">
                        <div class="col-md-2">
                            <h4>@lang( 'restaurant.bookings' )</h4>
                        </div>
                        <div class="col-md-2">
                            <div class="checkbox">
                                <label class="ch_all">
                                    <input type="checkbox" class="check_all input-icheck" > {{ __( 'role.select_all' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="col-md-4">
                                <div class="checkbox">
                                    <label>
                                        {!! Form::checkbox('permissions[]', 'crud_all_bookings', in_array('crud_all_bookings', $role_permissions),
                                        [ 'class' => 'input-icheck']); !!} {{ __( 'restaurant.add_edit_view_all_booking' ) }}
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="checkbox">
                                    <label>
                                        {!! Form::checkbox('permissions[]', 'crud_own_bookings', in_array('crud_own_bookings', $role_permissions),
                                        [ 'class' => 'input-icheck']); !!} {{ __( 'restaurant.add_edit_view_own_booking' ) }}
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
              
                {{--Projects Setting--}}
                <div class="row check_group">
                    <div class="col-md-4">
                        <h4> Projects Setting </h4>
                    </div>
                    <div class="col-md-8">
                        <div class="col-md-4">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'projcts.show', in_array('projcts.show', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} عرض المشروعات
                                </label>
                            </div>
                        </div>
                    </div>

                </div>
                {{-- Catalog Setting--}}
                <div class="row check_group">
                    <div class="col-md-4">
                        <h4> مديول كاتالوج </h4>
                    </div>
                    <div class="col-md-8">
                        <div class="col-md-4">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'catalouge.show', in_array('catalouge.show', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} عرض الكتالوج
                                </label>
                            </div>
                        </div>
                    </div>

                </div>
                {{--Repair--}}
                <div class="row check_group">
                    <div class="col-md-4">
                        <h4> مديول الصيانة </h4>
                    </div>
                    <div class="col-md-8">
                        <div class="col-md-4">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'repair_setting.view', in_array('repair_setting.view', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} الاعدادت
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'repair_device_model.create', in_array('repair_device_model.create', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} اضافة موديل جهاز
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                {{--HRM Setting--}}
                <div class="row check_group">
                    <div class="col-md-4">
                        <h4> HRM Setting </h4>
                    </div>
                    <div class="col-md-8">
                        <div class="col-md-4">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'hrm_show', in_array('hrm_show', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} HRM
                                </label>
                            </div>
                        </div>
                    </div>

                </div>
                
                @if(in_array('tables', $enabled_modules))
                    <div class="row check_group">
                        <div class="col-md-4">
                            <h4>@lang( 'restaurant.restaurant' )</h4>
                        </div>
                        <div class="col-md-8">
                            <div class="col-md-4">
                                <div class="checkbox">
                                    <label>
                                        {!! Form::checkbox('permissions[]', 'access_tables', in_array('access_tables', $role_permissions),
                                        [ 'class' => 'input-icheck']); !!} {{ __('lang_v1.access_tables') }}
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                <div class="row check_group">
                    <div class="col-md-2">
                        <h4>@lang( 'role.Banks' )</h4>
                    </div>
                    <div class="col-md-2">
                        <div class="checkbox">
                            <label class="ch_all">
                                <input type="checkbox" class="check_all input-icheck" > {{ __( 'role.select_all' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="col-md-4">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'contact_bank.view', in_array('contact_bank.view', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.view_bank' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'contact_bank.create', in_array('contact_bank.create', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.add_bank' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'contact_bank.update', in_array('contact_bank.update', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.edit_bank' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'contact_bank.delete', in_array('contact_bank.delete', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.delete_bank' ) }}
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row check_group">
                    <div class="col-md-2">
                        <h4>@lang( 'home.Cheques' )</h4>
                    </div>
                    <div class="col-md-2">
                        <div class="checkbox">
                            <label class="ch_all">
                                <input type="checkbox" class="check_all input-icheck" > {{ __( 'role.select_all' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="col-md-4">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'cheque.view', in_array('cheque.view', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.View Cheque' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'cheque.create', in_array('cheque.create', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.Add Cheque' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'cheque.update', in_array('cheque.update', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.Edit Cheque' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'cheque.delete', in_array('cheque.delete', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.Delete Cheque' ) }}
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row check_group">
                    <div class="col-md-2">
                        <h4>@lang( 'home.Voucher' )</h4>
                    </div>
                    <div class="col-md-2">
                        <div class="checkbox">
                            <label class="ch_all">
                                <input type="checkbox" class="check_all input-icheck" > {{ __( 'role.select_all' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="col-md-4">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'payment_voucher.view', in_array('payment_voucher.view', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.View Voucher' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'payment_voucher.create', in_array('payment_voucher.create', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.Add Voucher' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'payment_voucher.update', in_array('payment_voucher.update', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.Edit Voucher' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'payment_voucher.delete', in_array('payment_voucher.delete', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.Delete Voucher' ) }}
                                </label>
                            </div>
                        </div>
                        {{-- update dailypayment --}}
                        <div class="col-md-4">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'daily_payment.view', in_array('daily_payment.view', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.journal voucher List' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'daily_payment.create', in_array('daily_payment.create', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.Add journal voucher' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'daily_payment.update', in_array('daily_payment.update', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.Edit journal voucher' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'daily_payment.delete', in_array('daily_payment.delete', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.Delete journal voucher' ) }}
                                </label>
                            </div>
                        </div>
                        {{-- end update dailypayment --}}
                        <div class="col-md-4">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'gournal_voucher.view', in_array('gournal_voucher.view', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.View Expense Voucher' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'gournal_voucher.create', in_array('gournal_voucher.create', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.Add Expense Voucher' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'gournal_voucher.update', in_array('gournal_voucher.update', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.Edit Expense Voucher' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'gournal_voucher.delete', in_array('gournal_voucher.delete', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.Delete Expense Voucher' ) }}
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row check_group">
                    <div class="col-md-2">
                        <h4>@lang( 'home.Warehouses' )</h4>
                    </div>
                    <div class="col-md-2">
                        <div class="checkbox">
                            <label class="ch_all">
                                <input type="checkbox" class="check_all input-icheck" > {{ __( 'role.select_all' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="col-md-4">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'warehouse.view', in_array('warehouse.view', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.View Warehouse' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'warehouse.movement', in_array('warehouse.movement', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.View WarehouseMovement' ) }}
                                </label>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'warehouse.create', in_array('warehouse.create', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.Add Warehouse' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'warehouse.update', in_array('warehouse.update', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.Edit Warehouse' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'warehouse.delete', in_array('warehouse.delete', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.Delete Warehouse' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'warehouse.recieved', in_array('warehouse.recieved', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.View Recived' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'warehouse.add_recieved', in_array('warehouse.add_recieved', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.Add Recived' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'warehouse.delivered', in_array('warehouse.delivered', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.View Delivered' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'warehouse.add_delivered', in_array('warehouse.add_delivered', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.Add Delivered' ) }}
                                </label>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'warehouse.invetory', in_array('warehouse.invetory', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.View Invetory' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'warehouse.add_invetory', in_array('warehouse.add_invetory', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.Add Invetory' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'warehouse.adjustment', in_array('warehouse.adjustment', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.View Adjustment' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'warehouse.add_adjustment', in_array('warehouse.add_adjustment', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.Add Adjustment' ) }}
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row check_group">
                    <div class="col-md-2">
                        <h4>@lang( 'role.Accounts' )</h4>
                    </div>
                    <div class="col-md-2">
                        <div class="checkbox">
                            <label class="ch_all">
                                <input type="checkbox" class="check_all input-icheck" > {{ __( 'role.select_all' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="col-md-4">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'account.view', in_array('account.view', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.List Accounts' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'account.create', in_array('account.create', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.Add Account' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'account.update', in_array('account.update', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.Edit Account' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'account.balance_sheet', in_array('account.balance_sheet', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __('account.balance_sheet') }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'account.trial_balance', in_array('account.trial_balance', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.trial balance' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'account.cash_flow',in_array('account.cash_flow', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.cash flow' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'account.payment_account_report',
                                             in_array('account.payment_account_report', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.payment account report' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'account.cost_center',
                                             in_array('account.cost_center', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.cost center' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'account.add_cost_center',
                                             in_array('account.add_cost_center', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.add cost center' ) }}
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                @include('role.partials.module_permissions')
                {{-- Sales Price Group --}}
                <div class="row check_group">
                    <div class="col-md-4">
                        <h4>@lang( 'lang_v1.access_selling_price_groups' )</h4>
                    </div>
                    <div class="col-md-8">
                        <div class="col-md-4">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('spg_permissions[]', 'access_default_selling_price', in_array('access_default_selling_price', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __('lang_v1.default_selling_price') }}
                                </label>
                            </div>
                        </div>
                        @if(count($selling_price_groups) > 0)
                            @foreach($selling_price_groups as $selling_price_group)
                                <div class="col-md-4">
                                    <div class="checkbox">
                                        <label>
                                            {!! Form::checkbox('spg_permissions[]', 'selling_price_group.' . $selling_price_group->id, in_array('selling_price_group.' . $selling_price_group->id, $role_permissions),
                                            [ 'class' => 'input-icheck']); !!} {{ $selling_price_group->name }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary pull-right">@lang( 'messages.update' )</button>
                    </div>
                </div>

            {!! Form::close() !!}
        @endcomponent
    </section>
    <!-- /.content -->
@endsection
