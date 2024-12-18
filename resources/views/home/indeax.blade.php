@extends('layouts.app')
@section('title', __('home.home'))

@section('content')
    <style>

        @font-face {
            font-family: "icomoon";
            src: url("fonts/icomoon.eot");
            src: url("fonts/icomoon.eot?#iefix") format("embedded-opentype"), url("fonts/icomoon.woff")
            format("woff"), url("fonts/icomoon.ttf") format("truetype"), url("fonts/icomoon.svg#icomoon")
            format("svg");
            font-weight: normal;
            font-style: normal;
        }

        .info-box-text {
            color: #01070e;
            font-weight: 600;
            font-size: 16px;
            margin-bottom: 2px;
        }

        .abb_{
            background:#edeeee !important;

        }

        h5{
            font-family: 'Cairo', sans-serif;
            color: inherit;

        }
        .display{
            display:block !important;
        }
        .row-custom .col-custom {
            display: flex;
            /* margin: 0px; */
            padding: 0px 4px;
            margin: 0px 0px;
        }
        .box, .info-box {
            margin-bottom: 7px;
            box-shadow: 0 0 2rem 0 rgba(136,152,170,.15)!important;
            border-radius: 5px;
        }
        .box-icon{
            color: #40485b !important;
            background:white !important;
            text-align:center;
            border: none;
        }
        .box-icon:hover{
            background: #40485b !important;
            color:white !important;
            text-align:center;
        }
        .parent-box{
            border:1px solid #ddd;
            height: 110px;
            background:white;
            padding-top: 20px;
            color: #40485b;
            text-align: center;
        }

        .parent-box h5{
            font-family: 'Cairo', sans-serif;
            font-size: 13px;
        }
        .parent-box i{
            font-size: 36px;



        }
        .colorButton label{
            background-color:#323232 !important;
            border: 1px solid black;
        }
        .list-group-item {
            position: relative;
            display: block;
            padding: 5px 15px;
            margin-bottom: -1px;
            background: inherit;
            border: none;

        }
        .parent-box:hover{
            font-size: 36px;
            background: #40485b !important;
            color:white !important;

        }
        .list-group-item a{
            color: inherit;
            font-size: 10px;
            text-decoration: inherit;
        }
        .list-group-item a:hover{
            color: inherit;
            font-size: 10px;
            text-decoration: revert;
        }
        .icon-user-tie:before {
            content: "\e976";
        }
        .list-group-item {
            position: relative;
            display: block;
            padding: 9px 15px;
            margin-bottom: -1px;
            background: inherit;
            border: none;
        }
        .list-group{
            color: #40485b !important;
            background:white !important;
            text-decoration: none;
            height: 331px;

        }
        .list-group:hover{
            background: #40485b !important;
            color:white !important;
            text-decoration: revert;
        }
        .row{
            background:#fafafa;
        }
        .info-box-new-style .info-box-content {
            padding: 6px 12px 6px 12px;
            margin-left: 64px;
        }
        .info-box-number{
            float:left;
        }
        .total-labels{
            font-size:20px !important;
            float:right;
        }
        .change-charts{
            z-index: 100;
            position: relative;
            top: 36px;
            left:  -55%;
        }
        .cont{
            background-color: #0baba5;
            color: white;
            display: flex;
            flex-flow: column;
            justify-content: center;
            justify-items: center;
            align-content: center;
            align-items: center;
            /* position: ab; */
            height: 240px;
            text-align: center;
            padding-top: 2px;
            font-size: 30px;
            border-radius: 10px;
            /*border: 1px solid #590631;*/
            margin: auto;
            margin-bottom: auto;
            margin-bottom: 15px;
            /* max-width: 200px; */
            transition: all .5s ease;
            box-shadow: 1px 1px 2px 1px rgba(0, 0, 0, 0.347);
          /*  border-top: 3px solid #18466f;*/
            /* border-bottom: 9px solid #8c1818; */
        }
        .cont1{
            background-color: #0baba5;
            color: white !important;
            display: flex;
            flex-flow: column;
            justify-content: center;
            justify-items: center;
            align-content: center;
            align-items: center;
            /* position: ab; */
            height: 240px;
            text-align: center;
            padding-top: 2px;
            font-size: 23px;
            border-radius: 10px;
            /*border: 1px solid #590631;*/
            margin: auto;
            margin-bottom: auto;
            margin-bottom: 15px;
            /* max-width: 200px; */
            transition: all .5s ease;
            box-shadow: 1px 1px 2px 1px rgba(0, 0, 0, 0.347);
          /*  border-top: 3px solid #18466f;*/
            /* border-bottom: 9px solid #8c1818; */
        }

        .cont>h3,h2{
            font-size: xx-large;
            color: rgb(255, 255, 255);
            text-shadow: 1px 1px 6px black;
        }
        .cont1>h3,h2{
            font-size: xx-large;
            text-shadow: 1px 1px 6px black;
            color: rgb(255, 255, 255);
        }

        .cont:hover,.cont1:hover{
            font-size: xx-large;
            background-color: #323232;
            transform: scale(1.051);

        }

        .cont:hover h2,.cont:hover h3,.cont1:hover h3,.cont1:hover h2{
            font-size: xx-large;
             color: white;
        }
        .cont:hover h2 i{
            transition: .3s ease-in;
            transform: scale(1.3);
            text-shadow: 1px 1px 2px black;
        }
        
        .backItem{
             background-color: #f1f1f1 !important;
             position: relative !important;
             width: calc(100%/6);
        }
        @media screen and (max-width:1200px){
        
            .backItem{
             position: relative !important;
             width: 100% !important;
        }
        }
        .Position{
             /* background-color: black !important; */
             position: absolute !important;
             width: 100% !important;
        }

    </style>

	<!-- Page level currency setting -->
	<input type="hidden" id="p_code" value="{{isset($currency_detail)?$currency_detail->code:''}}">
	<input type="hidden" id="p_symbol" value="{{isset($currency_detail)?$currency_detail->symbol:''}}">
	<input type="hidden" id="p_thousand" value="{{isset($currency_detail)?$currency_detail->thousand_separator:''}}">
	<input type="hidden" id="p_decimal" value="{{isset($currency_detail)?$currency_detail->decimal_separator:''}}">

    <div style="background-color: #f1f1f1;
                width: 95%;
                margin: auto;
                margin-top: 20px;
                padding: 20px;
                border-top: solid;
                border-top-color: currentcolor;
                border-top-style: solid;
                border-top-width: medium;
                border-radius: 10px 10px 0px 0px;
                box-shadow:0px 0px 10px 2px rgba(2, 2, 2, 0.282);">
                @if(\App\User::where('id',auth()->id())->whereHas('roles',function($query){
                                $query->where('id',1);
                            })->first())
                <div class="row">
            <!--<div class=" col-12  col-lg-2 backItem">-->
            <!--    <a href="/pos/create" class="cont" >-->
            <!--        <h2><i class="fas fa-dollar-sign"></i></h2>-->
                    <!--<h3>@lang('lang_v1.kasher')   </h3>-->

            <!--    </a>-->
            <!--</div>-->

            <div class="col-lg-4 backItem">
                <a href="/sells" class=" cont" >
                    <h2><i class="fa fa-registered"></i></h2>
                    <h3>@lang('lang_v1.sells')  </h3>
                </a>
            </div>

            <div class="col-lg-4 backItem">
                <a href="/reports/product-sell-return-report" class=" cont" >
                    <h2><i class="fa fa-undo-alt"></i></h2>
                    <h3>@lang('lang_v1.sell_return')  </h3>
                </a>
            </div>

            <div class="col-lg-4 backItem">
                <a href="/purchases" class=" cont" >
                    <h2><i class="fa fa-cart-plus"></i></h2>
                    <h3> @lang('lang_v1.purchases') </h3>
                </a>
            </div>

            <div class="col-lg-4 backItem ">
                <a href="/reports/product-purchase-report" class=" cont" >
                    <h2><i class="fa fa-undo-alt"></i></h2>
                    <h3> @lang('lang_v1.purchase_return') </h3>
                </a>
            </div>
           <div class="col-lg-4 backItem">
                <a href="/products" class=" cont" >
                    <h2><i class="fa fas fa-cubes"></i></h2>
                    <h3>@lang('lang_v1.list_products')   </h3>
                </a>
            </div>
            <div class="col-lg-4 backItem">
                <a href="/contacts?type=customer" class=" cont" >
                    <h2><i class="fa fas fa-address-book"></i></h2>
                    <h3>  @lang('lang_v1.customers')</h3>
                </a>
            </div>
        </div>
        <div class="row">

            <div class="col-lg-2 backItem">
                <a href="{{action('AccountController@index')}}" class=" cont" >
                    <h2><i class="fa fas fa-money-check-alt"></i></h2>
                    <h3>  @lang('lang_v1.payment_accounts')</h3>
                </a>
            </div>

            <div class="col-lg-2 backItem">
                <a href="/contacts?type=supplier" class=" cont" >
                    <h2><i class="fa fas fa-address-book"></i></h2>
                    <h3>@lang('lang_v1.suppliers')   </h3>
                </a>
            </div>

            <div class="col-lg-2 backItem ">
                <a href="/users" class=" cont" >
                    <h2><i class="fa fas fa-users"></i></h2>
                    <h3>@lang('lang_v1.users')   </h3>
                </a>
            </div>

            
            <div class="col-lg-2 backItem">
                <a href="/gallery/stock_report" class=" cont" >
                    <h2><i class="fa fas fa-chart-bar"></i></h2>
                    <h3>@lang('report.stock_report')   </h3>
                </a>
            </div>


            <div class="col-lg-2 backItem">
                <a href="/item-move/show/1" class=" cont" >
                    <h2><i class="fa fas fa-shopping-bag"></i></h2>
                    <h3>@lang('lang_v1.Item Movement')   </h3>
                </a>
            </div>
            <div class="col-lg-2 backItem ">
                <a href="account/account-show/1" class=" cont" >
                    <h2><i class="fa fas fa-shopping-bag"></i></h2>
                    <h3>@lang('lang_v1.General Ledger')   </h3>
                </a>
            </div>
            {{-- <div class="col-lg-2 backItem">
                <a href="reports/product-sell-report" class=" cont" >
                    <h2><i class="fa fas fa-shopping-bag"></i></h2>
                    <h3>@lang('lang_v1.product_sell_day')   </h3>
                </a>
            </div>
            <div class="col-lg-2 backItem ">
                <a href="reports/product-purchase-report" class=" cont" >
                    <h2><i class="fa fas fa-shopping-bag"></i></h2>
                    <h3>@lang('lang_v1.product_purchas_day')   </h3>
                </a>
            </div> --}}
            {{-- <div class="col-lg-2 backItem">
                <a href="/expenses" class=" cont" >
                    <h2><i class="fa fa-truck"></i></h2>
                    <h3>@lang('lang_v1.expense')   </h3>
                </a>
            </div> --}}
            {{-- <div class="col-lg-2 backItem">
                <a href="/repair/dashboard" class=" cont" >
                    <h2><i class="fa fas fa-wrench"></i></h2>
                    <h3>   @lang('lang_v1.repair')</h3>
                </a>
            </div> --}}

            {{-- <div class="col-lg-2 backItem">
                <a href="manufacturing/recipe" class=" cont" >
                    <h2><i class="fa fas fa-industry"></i></h2>
                    <h3>@lang('lang_v1.manufacturing')   </h3>
                </a>
            </div> --}}

        </div>
       

      


 
 
        {{-- buttons --}}
        {{-- <div class="row" style=" background-color:#f1f1f1;">
            <div class="col-md-8 col-xs-12" style="float:left;">
                <div class="btn-group pull-right colorButton" data-toggle="buttons">
                    <label class="btn btn-info active">
                        <input type="radio" name="date-filter"
                               data-start="{{ date('Y-m-d') }}"
                               data-end="{{ date('Y-m-d') }}"
                               checked> {{ __('home.today') }}
                    </label>
                   
                    <label class="btn btn-info">
                        <input type="radio" name="date-filter"
                               data-start="{{ $date_filters['this_month']['start']}}"
                               data-end="{{ $date_filters['this_month']['end']}}"
                               checked> {{ __('home.this_month') }}
                    </label>
                    <label class="btn btn-info">
                        <input type="radio" name="date-filter"
                               data-start="{{ $date_filters['this_fy']['start']}}"
                               data-end="{{ $date_filters['this_fy']['end']}}"
                               checked> {{ __('home.this_fy') }}
                    </label>
                </div>
            </div>
        </div> --}}
        <br>


        <div class="row row-custom">
            <div class="col-md-12">
                <div class="box box-warning">
                    <div class="box-body">

                        <div class="row">
                            <div class="col-md-6 col-sm-6 col-xs-12 col-custom">
                                <div class="info-box info-box-new-style">
                                    <div class="info-box-content">
                                        <span class="info-box-text" style="color:#2d91ea">{{ __('home.total_purchase') }}</span>
                                        <table >
                                            <tr>
                                                <td><span class="total-labels">@lang("lang_v1.today")&nbsp;</span></td>
                                                <td><span class="info-box-number total_purchase"><i class="fas fa-sync fa-spin fa-fw margin-bottom"></i> </span></td>
                                            </tr>
                                            <tr>
                                                <td></span><span class="total-labels">@lang("lang_v1.month")&nbsp;</span></td>
                                                <td><span class="info-box-number total_purchase_month"><i class="fas fa-sync fa-spin fa-fw margin-bottom"></i></td>
                                            </tr>
                                            <tr>
                                                <td></span><span class="total-labels">@lang("lang_v1.year")&nbsp;</span></td>
                                                <td><span class="info-box-number total_purchase_year"><i class="fas fa-sync fa-spin fa-fw margin-bottom"></i></td>
                                            </tr>
                                        </table>
                                    </div>
                                    <!-- /.info-box-content -->
                                </div>
                                <!-- /.info-box -->
                            </div>
                            <!-- /.col -->
                            <div class="col-md-6 col-sm-6 col-xs-12 col-custom">
                                <div class="info-box info-box-new-style">

                                    <div class="info-box-content">
                                        <span class="info-box-text" style="color:#3ebfbe">{{ __('home.total_sell') }}</span>
                                        <table >
                                            <tr>
                                                <td><span class="total-labels">@lang("lang_v1.today")&nbsp;</span></td>
                                                <td><span class="info-box-number total_sell"><i class="fas fa-sync fa-spin fa-fw margin-bottom"></i> </span></td>
                                            </tr>
                                            <tr>
                                                <td></span><span class="total-labels">@lang("lang_v1.month")&nbsp;</span></td>
                                                <td><span class="info-box-number total_sell_month"><i class="fas fa-sync fa-spin fa-fw margin-bottom"></i></td>
                                            </tr>
                                            <tr>
                                                <td></span><span class="total-labels">@lang("lang_v1.year")&nbsp;</span></td>
                                                <td><span class="info-box-number total_sell_year"><i class="fas fa-sync fa-spin fa-fw margin-bottom"></i></td>
                                            </tr>
                                        </table>


                                    </div>
                                    <!-- /.info-box-content -->
                                </div>
                                <!-- /.info-box -->
                            </div>
                            <!-- /.col -->
                        </div>

                        <div class="row">
                            <div class="col-md-6 col-sm-6 col-xs-12 col-custom">
                                <div class="info-box info-box-new-style">
                                    <div class="info-box-content">
                                        <span class="info-box-text" style="color:#ffb553">@lang("lang_v1.Purchase_receivables")</span>
                                        <table >
                                            <tr>
                                                <td><span class="total-labels">@lang("lang_v1.today")&nbsp;</span></td>
                                                <td><span class="info-box-number purchase_due"><i class="fas fa-sync fa-spin fa-fw margin-bottom"></i> </span></td>
                                            </tr>
                                            <tr>
                                                <td></span><span class="total-labels">@lang("lang_v1.month")&nbsp;</span></td>
                                                <td><span class="info-box-number purchase_due_month"><i class="fas fa-sync fa-spin fa-fw margin-bottom"></i></td>
                                            </tr>
                                            <tr>
                                                <td></span><span class="total-labels">@lang("lang_v1.year")&nbsp;</span></td>
                                                <td><span class="info-box-number purchase_due_year"><i class="fas fa-sync fa-spin fa-fw margin-bottom"></i></td>
                                            </tr>
                                        </table>
                                    </div>
                                    <!-- /.info-box-content -->
                                </div>
                                <!-- /.info-box -->
                            </div>
                            <!-- /.col -->

                            <!-- fix for small devices only -->
                            <!-- <div class="clearfix visible-sm-block"></div> -->
                            <div class="col-md-6 col-sm-6 col-xs-12 col-custom">
                                <div class="info-box info-box-new-style">
                                    <div class="info-box-content">
                                        <span class="info-box-text" style="color:#f33e6f">@lang("lang_v1.Sells_receivables")</span>
                                        <table >
                                            <tr>
                                                <td><span class="total-labels">@lang("lang_v1.today") &nbsp;</span></td>
                                                <td><span class="info-box-number invoice_due"><i class="fas fa-sync fa-spin fa-fw margin-bottom"></i> </span></td>
                                            </tr>
                                            <tr>
                                                <td></span><span class="total-labels">@lang("lang_v1.month") &nbsp;</span></td>
                                                <td><span class="info-box-number invoice_due_month"><i class="fas fa-sync fa-spin fa-fw margin-bottom"></i></td>
                                            </tr>
                                            <tr>
                                                <td></span><span class="total-labels">@lang("lang_v1.year") &nbsp;</span></td>
                                                <td><span class="info-box-number invoice_due_year"><i class="fas fa-sync fa-spin fa-fw margin-bottom"></i></td>
                                            </tr>
                                        </table>

                                    </div>
                                    <!-- /.info-box-content -->
                                </div>
                                <!-- /.info-box -->
                            </div>
                            <!-- /.col -->
                        </div>

                        <!-- expense -->
                        <div class="row">
                            <div class="col-md-12 col-sm-12 col-xs-12 col-custom">
                                <div class="info-box info-box-new-style">
                                    <div class="info-box-content">
                                            <span class="info-box-text" style="color:#64d2e9">
                                                {{ __('lang_v1.expense') }}
                                            </span>
                                        <table >
                                            <tr>
                                                <td><span class="total-labels">@lang("lang_v1.today")&nbsp;</span></td>
                                                <td><span class="info-box-number total_expense"><i class="fas fa-sync fa-spin fa-fw margin-bottom"></i> </span></td>
                                            </tr>
                                            <tr>
                                                <td></span><span class="total-labels">@lang("lang_v1.month")&nbsp;</span></td>
                                                <td><span class="info-box-number total_expense_month"><i class="fas fa-sync fa-spin fa-fw margin-bottom"></i></span></td>
                                            </tr>
                                            <tr>
                                                <td></span><span class="total-labels">@lang("lang_v1.year")&nbsp;</span></td>
                                                <td><span class="info-box-number total_expense_year"><i class="fas fa-sync fa-spin fa-fw margin-bottom"></i></span></td>
                                            </tr>
                                        </table>
                                    </div>
                                    <!-- /.info-box-content -->
                                </div>
                                <!-- /.info-box -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>



        </div>
        
        
        <div class="row">
            <div class="@if((session('business.enable_product_expiry') != 1) && auth()->user()->can('stock_report.view')) col-sm-12 @else col-sm-6 @endif">
                @component('components.widget', ['class' => 'box-warning'])
                    @slot('icon')
                        <i class="fa fa-exclamation-triangle text-yellow" aria-hidden="true"></i>
                    @endslot
                    @slot('title')
                        {{ __('home.product_stock_alert') }} @show_tooltip(__('tooltip.product_stock_alert'))
                    @endslot
                    <table class="table table-bordered table-striped" id="stock_alert_table" style="width: 100%;">
                        <thead>
                         <tr>
                            <th>@lang( 'sale.product' )</th>
                            <th>@lang( 'business.location' )</th>
                            <th>@lang( 'report.current_stock' )</th>
                         </tr>
                        </thead>
                    </table>
                @endcomponent
            </div>
        </div>
        @endif
        @if(\App\User::where('id',auth()->id())->whereHas('roles',function($query){
                                    $query->where('id',1);
                                 })->first() || auth()->user()->can("admin_without.views"))
        @else
                @if(auth()->user()->can("warehouse.views"))
                        @include('home.partials.warehouse' , ["warehouse"=> $warehouse , "product" => $product])
                @elseif(auth()->user()->can("manufuctoring.views"))
                        @include('home.partials.manufacturing' , ["warehouse"=> $warehouse , "product" => $product])
                @elseif(auth()->user()->can("admin_supervisor.views"))
                        @include('home.partials.sales_manager' , ["warehouse"=> $warehouse , "product" => $product])
                @elseif(auth()->user()->can("SalesMan.views"))
                        @include('home.partials.sales' , ["warehouse"=> $warehouse , "product" => $product])
                @elseif(auth()->user()->can("Accountant.views"))
                        @include('home.partials.Accountant' , ["warehouse"=> $warehouse , "product" => $product])
                @endif
        @endif

        </div>





<!-- /.content -->
<div class="modal fade payment_modal" tabindex="-1" role="dialog" 
    aria-labelledby="gridSystemModalLabel">
</div>

<div class="modal fade edit_payment_modal" tabindex="-1" role="dialog" 
    aria-labelledby="gridSystemModalLabel">
</div>
@stop
@section('javascript')

    <script src="{{ asset('js/home.js?v=' . $asset_v) }}"></script>
    <script src="{{ asset('js/payments.js?v=' . $asset_v) }}"></script>
    



@endsection

