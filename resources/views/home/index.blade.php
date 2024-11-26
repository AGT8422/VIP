
{{-- **1** --}}
@extends('layouts.app')
{{-- **2** --}}
@section('title', __('home.home'))
{{-- **3** --}}
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
            position: relative;
            background-color: #7a7676;
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
            border-radius: 10%;
            border: 1px solid rgb(87, 87, 87);
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
            position: relative;
            background-color: #7a7676;
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
            border-radius: 10%;
            border: 1px solid rgb(87, 87, 87);
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
            font-size: x-large;
            color: rgb(255, 255, 255);
            text-shadow: 1px 1px 6px black;
        }
        .cont1>h3,h2{
            font-size: x-large;
            text-shadow: 1px 1px 6px black;
            color: rgb(255, 255, 255);
        }

        .cont:hover,.cont1:hover{
            font-size: x-large;
            background-color: #0e0e0e;
            transform: scale(1.051);
            border: 4px solid rgb(231, 103, 5);

        }
        .row>div{
            
        }

        .cont:hover h3, .cont1:hover h3 {
            font-size: x-large;
             color: rgb(231, 103, 5);
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
        
        .short{
            background: #0baba5;   
            position: relative;
            cursor: pointer;
             -webkit-box-shadow:1px 1px 1px  #343c3c;
            -moz-box-shadow:1px 1px 1px  #343c3c;
            -o-box-shadow:1px 1px 1px #343c3c ;
            box-shadow:1px 1px 1px  #343c3c;
            padding: 20px;
        }
        .under_box{
            position: absolute;
            background:#343c3c ;
            height: 50px;
            bottom: 0px;
            left: 0px;
            width: 100%;
            border-bottom-left-radius:20px; 
            border-bottom-right-radius:20px; 
        }
        .back_box{
            position: absolute;
            background:#343c3c ;
            height: 100%;
            bottom: 0px;
            left: 0px;
            width: 100%;
             z-index: 1;
        }
        .above_box{
            position: absolute;
            height: 100%;
            bottom: 0px;
            text-align: center;
            font-size: xx-large;
             padding: 10px;
            left: 0px;
            width: 100%;
            color: white;
            z-index: 2;
 
         }
        
        @keyframes slide-in {
            0% {
                height: 0px;
                transition: .3s ease-in;
             }
            100% {
                height: 100px;
                transition: .3s ease-in;
            }
        }
        .short>h3{
            text-align: center;
            color: rgb(255, 255, 255);
            font-weight: bold;
        }
        .cont>h3{
            font-size: x-large;
            text-shadow: 1px 1px 6px black;
            color: rgb(255, 255, 255);
        }
        .panel{
            background-color: #f1f1f1;
            width: 95%;
            margin: auto;
            margin-top: 20px;
            padding: 20px;
            border-top: solid;
            border-top-color: #ec6608;
            border-top-style: solid;
            border-top-width: 2px;
            border-radius: 10px 10px 0px 0px;
         }
        .home-boxes{
            background: #7a7676;
            padding: 10px;
        
        }
        .info-box-content table{
            background: #ff0b0b !important;
            padding: 10px;
            width: 100%;
            
        
        }
        .info-box-content table tbody tr {
            background: #f7f7f7;   
            border: 1px solid #ec6608;
        }
        .info-box-content table tbody tr td span{
            background: #343c3c00;  
            text-align: right !important;
            float: right !important; 
            width: 100% ;
            padding-right:5px; 
            color: black;
            border: 0px solid rgb(255, 255, 255);
        }
        .info-box-content table tbody tr td .total-labels {
            background: #474e4e00;  
            text-align: center !important;
            float: left !important; 
            width: 50% ;
            color: black;
            border: 0px solid black;
        }
        .box-warning{
            border-top: 2px solid #ec6608 !important;
            border-bottom: 1px solid rgba(72, 71, 71, 0.597) !important;
            box-shadow:0px 0px 10px 2px rgba(2, 2, 2, 0.282) !important;
        }
        .start_item{
            transition: .3s ease-in;
         }
        .border_style{
            border-top: 2px solid #ec6608;
            border-bottom: 0px solid black;
            border-left: 0px solid black;
            border-right:0px solid rgb(65, 65, 65);
         }

        #stock_alert_table tbody tr td {
            background: #0baba5;   
            position: relative;
            border: 1px solid rgb(255, 255, 255);
            color: white;
            font-size: medium;
        }

        #stock_alert_table  tbody tr td{
            background: #7a767600;
            color: black;
            border: .5px solid #ee6808a0;
        }

        .box-sale{
            height: 100px;
            font-size: 19px;
            font-weight: bold;
            padding:5px;
            background-color: #ffe8d9;
            border-radius: 3px;
            color: #ee6808  !important;
            border-top:2px solid #4f4f4f; 
            border-bottom:4px solid #ee6808; 
            -webkit-box-shadow: 0px 0px 20px rgba(200, 200, 200, 0.818);
            -moz-box-shadow: 0px 0px 20px rgb(200, 200, 200, 0.818);
            -o-box-shadow: 0px 0px 20px rgb(200, 200, 200, 0.818);
            box-shadow: 0px 0px 20px rgb(200, 200, 200, 0.818);
        }
        .right-style-btn{
            float: right;
            font-weight: 500 !important;
            font-size: 20px;
            text-align: right
        }
        .right-style-btn i{
            padding: 3px;
             cursor: pointer;
        }
        .box-sale  h3{
            margin: 0px;
            padding: 0px 10px;
            font-weight: bold !important;
            color: #4f4f4f !important;
        }
        .box-main {
        
        }
        .box-sale ul{
            position: absolute;
            z-index: 10;
            margin: 5px 0px 0px -70px;
             list-style: none;
             border-radius: 3px;
             background-color: #ffffff;
             padding: 10px 0px ;
             -webkit-box-shadow: 0px 0px 20px rgba(169, 169, 169, 0.818);
            -moz-box-shadow: 0px 0px 20px rgb(169, 169, 169, 0.818);
            -o-box-shadow: 0px 0px 20px rgb(169, 169, 169, 0.818);
            box-shadow: 0px 0px 20px rgb(169, 169, 169, 0.818);
        }
        .box-sale ul li{
            font-size: 14px;
            padding: 4px 20px;
            cursor: pointer;
            
        }
        .box-sale ul li:hover  {
            
            color: #ffffff;
            background-color: #3e3e3e;
          
        }
        .box-sale ul li:hover a  {
            color: #ffffff;
        }
        .box-sale ul li a{
            color: #ee6800;
        }
        .hide{
            transition: .3s ease-out;
        }
        .box_alert_before_time{
            margin: 1% 25%;
            border-radius: 10px;
            border: 2px solid #ff8585;
            color: #ffffff;
            background-color: #ff8585;
            width: 95%;
            font-size: xx-large;
            transform: translateX(-24%);
            padding: 10px;
            position: relative;

        }
        .box_alert_before_time b{
            
            color: white;
            

        }
        .box_alert_before_time p{
            font-size:18px;
            color: white;
            

        }
        .box_alert_before_time .after{
           content: "x";
           background-color: #1111112f; 
           position: absolute;
           right:7px;
           top: 20%;
           font-size: 19px;
           border: 2px solid #1111112f;
           color: #1c1c1c;
           cursor: pointer;
           transform: translateY(-50%);
           padding: 2px 12px;
           border-radius: 10px;
        

        }
    </style>

	<!-- Page level currency setting -->
	<input type="hidden" id="p_code" value="{{isset($currency_detail)?$currency_detail->code:''}}">
	<input type="hidden" id="p_symbol" value="{{isset($currency_detail)?$currency_detail->symbol:''}}">
	<input type="hidden" id="p_thousand" value="{{isset($currency_detail)?$currency_detail->thousand_separator:''}}">
	<input type="hidden" id="p_decimal" value="{{isset($currency_detail)?$currency_detail->decimal_separator:''}}">

    {{-- <div class="content">
        <div class="row">
            <div class="col-sm-2 ">
                <div class="box-sale">
                    <h3 class="box-main">
                        @lang('Sales')
                        <span class="right-style-btn">
                            <i class="fa  fa-cog set"></i>
                            <ul class="hide">
                                <li><a onclick="" >@lang("Today")</a></li>
                                <li><a onclick="" >@lang("Month")</a></li>
                                <li><a onclick="" >@lang("Year")</a></li>
                            </ul>
                        </span>
                        <br>
                    </h3>
                    <br>
                    <div class="col-sm-6 text-left" >
                            4500
                    </div>
                    <div class="col-sm-6 text-right">
                            AED
                    </div>
                </div>
            </div>
            <div class="col-sm-3">
                
            </div>
            <div class="col-sm-6">
                
            </div>
        </div>
    </div> --}}
    {{-- <div class="waring_message text-center" >
        {!! __("lang_v1.subscribe_wrang") !!} {{ " During 10-07-2024 "    }}
    </div> --}}
    @php $moduleUtil = new \App\Utils\ModuleUtil; $package =  \Modules\Superadmin\Entities\Subscription::first();   @endphp 
    @if($moduleUtil->isSubscribed(request()->session()->get('user.business_id')))
        @php $now = \Carbon::now(); @endphp 
        @if($package->end_date->diff()->days <= 10)
            @if($package->end_date->diff()->days>0)
                <div class="box_alert_before_time">
                    <b>Attention !! </b> <br>
                    <p>Your subscribe will be expire at  {{$package->end_date->format('l jS \\of F Y h:i:s A')}} hurry up to renew your subscribe <br><small>Remaining Days ( {{$package->end_date->diff()->days}} ).</small></p>
                    
                    <div class="after">x</div>
                </div>
            @else
                @if($package->end_date->diff()->days>0)
                    <div class="box_alert_before_time" style="background-color: #0baba5 !important; border:1px solid #0baba5 !important;">
                        <b>Your Subscribe is Expired </b> <br>
                        <p>hurry up to renew your subscribe <br><small>Premitted Period Until ( {{\Carbon::createFromFormat('Y-m-d',$package->permitted_period)->diff()->days}} ).</small></p>
                        
                        <div class="after">x</div>
                    </div>
                @else
                    <div class="box_alert_before_time"  >
                        <b>Your Subscribe is Expired </b> <br>
                        <p>hurry up to renew your subscribe</p>
                        
                        <div class="after">x</div>
                    </div>
                @endif
            @endif
        @elseif($package->end_date < $now)
            <div class="box_alert_before_time"  >
                <b>Your Subscribe is Expired </b> <br>
                <p>hurry up to renew your subscribe</p>
                
                <div class="after">x</div>
            </div>
        @endif
    @endif
    
    <div class="panel " style="">
        @if(\App\User::where('id',auth()->id())->whereHas('roles',function($query){
                        $query->where('id',1);
                    })->first())
            <div>&nbsp;</div>
            @component("components.widget",["class"=>"box-primary","title"=>__('Shortcuts')])
                {{-- ... first rows --}}      
                <div class="row shorts ">
                    
                    <div class="col-lg-4 backItem">
                        <a href="/sells" class=" cont" >
                            <h2><i class="fa fa-registered"> </i></h2>
                            <h3>@lang('lang_v1.sells')  </h3>
                            <div class="under_box"></div>
                        </a>
                    </div>

                    <div class="col-lg-4 backItem">
                        <a href="/reports/product-sell-return-report" class=" cont" >
                            <h2><i class="fa fa-undo-alt"></i></h2>
                            <h3>@lang('lang_v1.sell_return')  </h3>
                                <div class="under_box"></div>

                        </a>
                    </div>

                    <div class="col-lg-4 backItem">
                        <a href="/purchases" class=" cont" >
                            <h2><i class="fa fa-cart-plus"></i></h2>
                            <h3> @lang('lang_v1.purchases') </h3>
                                    <div class="under_box"></div>

                        </a>
                    </div>

                    <div class="col-lg-4 backItem ">
                        <a href="/reports/product-purchase-report" class=" cont" >
                            <h2><i class="fa fa-undo-alt"></i></h2>
                            <h3> @lang('lang_v1.purchase_return') </h3>
                                <div class="under_box"></div>

                        </a>
                    </div>

                    <div class="col-lg-4 backItem">
                        <a href="/products" class=" cont" >
                            <h2><i class="fa fas fa-cubes"></i></h2>
                            <h3>@lang('lang_v1.list_products')   </h3>
                                <div class="under_box"></div>

                        </a>
                    </div>

                    <div class="col-lg-4 backItem">
                        <a href="/contacts?type=customer" class=" cont" >
                            <h2><i class="fa fas fa-address-book"></i></h2>
                            <h3>  @lang('lang_v1.customers')</h3>
                                    <div class="under_box"></div>

                        </a>
                    </div>

                </div>
                {{-- ... second rows --}}
                <div class="row shorts ">
                    <div class="col-lg-2 backItem">
                        <a href="{{action('AccountController@index')}}" class=" cont" >
                            <h2><i class="fa fas fa-money-check-alt"></i></h2>
                            <h3>  @lang('lang_v1.payment_accounts')</h3>
                                    <div class="under_box"></div>
                        </a>
                    </div>
                    <div class="col-lg-2 backItem">
                        <a href="/contacts?type=supplier" class=" cont" >
                            <h2><i class="fa fas fa-address-book"></i></h2>
                            <h3>@lang('lang_v1.suppliers')   </h3>
                                <div class="under_box"></div>
                        </a>
                    </div>
                    <div class="col-lg-2 backItem ">
                        <a href="/users" class=" cont" >
                            <h2><i class="fa fas fa-users"></i></h2>
                            <h3>@lang('lang_v1.users')   </h3>
                                <div class="under_box"></div>
                        </a>
                    </div>
                    <div class="col-lg-2 backItem">
                        <a href="/gallery/stock_report" class=" cont" >
                            <h2><i class="fa fas fa-chart-bar"></i></h2>
                            <h3>@lang('report.stock_report')   </h3>
                                <div class="under_box"></div>
                        </a>
                    </div>
                    <div class="col-lg-2 backItem">
                        <a href="/item-move/show/1" class=" cont" >
                            <h2><i class="fa fas fa-shopping-bag"></i></h2>
                            <h3>@lang('lang_v1.Item Movement')   </h3>
                            <div class="under_box"></div>
                        </a>
                    </div>
                    <div class="col-lg-2 backItem ">
                        <a href="account/account-show/1" class=" cont" >
                            <h2><i class="fa fas fa-shopping-bag"></i></h2>
                            <h3>@lang('lang_v1.General Ledger')   </h3>
                            <div class="under_box"></div>
                        </a>
                    </div>
                </div>
            @endcomponent
            <br>
            @component("components.widget",["class"=>"box-primary","title"=>__('report.reports')])
            <div class="box-body">
                <div class="row">
                    <div class="col-md-6 col-sm-6 col-xs-12 col-custom">
                        <div class="info-box info-box-new-style">
                            <div class="info-box-content">
                                <span class="info-box-text home-boxes" style="color:#ffffff">{{ __('home.total_purchase') }}</span>
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
                                <span class="info-box-text home-boxes" style="color:#ffffff">{{ __('home.total_sell') }}</span>
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
                                <span class="info-box-text home-boxes" style="color:#ffffff">@lang("lang_v1.Purchase_receivables")</span>
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
                                <span class="info-box-text home-boxes" style="color:#ffffff">@lang("lang_v1.Sells_receivables")</span>
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
                    <div class="col-md-6 col-sm-6 col-xs-6 col-custom">
                        <div class="info-box info-box-new-style">
                            <div class="info-box-content">
                                    <span class="info-box-text home-boxes" style="color:#ffffff">
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
            @endcomponent
            <div class="row">
                <div class="@if((session('business.enable_product_expiry') != 1) && auth()->user()->can('stock_report.view')) col-sm-12 @else col-sm-6 @endif">
                    @component('components.widget', ['class' => 'box-warning'])
                        @slot('icon')
                            <i class="fa fa-exclamation-triangle text-yellow" aria-hidden="true"></i>
                        @endslot
                        @slot('title'  )
                            {{ __('home.product_stock_alert') }} @show_tooltip(__('tooltip.product_stock_alert'))
                        @endslot
                        <table class="table table-bordered table-striped" id="stock_alert_table" style="width: 100%;">
                            <thead >
                                <tr style="background:#7a7676 ">
                                <th >@lang( 'sale.product' )</th>
                                <th >@lang( 'business.location' )</th>
                                <th  >@lang( 'report.current_stock' )</th>
                                </tr>
                            </thead>
                        </table>
                    @endcomponent
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <a href="{{action("Report\PrinterSettingController@generatePdf")}}">{{" TEST PDF "}}</a>
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
{{-- **4** --}}
@section('javascript')
    <script src="{{ asset('js/home.js?v=' . $asset_v) }}"></script>
    <script src="{{ asset('js/payments.js?v=' . $asset_v) }}"></script>
    <script type="text/javascript">
        function viewShort(){
            $(".shorts").toggleClass("hide");
            $(".shorts").toggleClass("start_item");
            $(".hi_box").toggleClass("hide");
            $(".sh_box").toggleClass("hide");
            $(".panel").toggleClass("border_style");
        }
        $(".set").on("click",function(){
            $(".box-sale ul").toggleClass("hide");
        })
        $(".box_alert_before_time .after").on("click",function(){
            $(this).parent().addClass("hide");
        })
    </script>
@endsection

