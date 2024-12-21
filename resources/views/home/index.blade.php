
{{-- **1** --}}
@extends('layouts.app')
{{-- **2** --}}
@section('title', __('home.home'))
{{-- **3** --}}


@php  
    $CashBal = 0;
    $BankBal = 0;
    $p_font       = (in_array(session()->get('lang', config('app.locale')), config('constants.langs_rtl')))?'18px' : '16px';
    $h4_font      = (in_array(session()->get('lang', config('app.locale')), config('constants.langs_rtl')))?'22px' : '20px';
    $D_rang_font  = (in_array(session()->get('lang', config('app.locale')), config('constants.langs_rtl')))?'18px' : '16px';
    $margin_bank  = (in_array(session()->get('lang', config('app.locale')), config('constants.langs_rtl')))?'0% 15% 0% 0%' : '0% 0% 0% 15%';
    $translate    = (in_array(session()->get('lang', config('app.locale')), config('constants.langs_rtl')))?'50%' : '-50%';
    $margin_sec   = (in_array(session()->get('lang', config('app.locale')), config('constants.langs_rtl')))?'0% 5% 0% 0%' : '0% 0% 0% 5%';
    $margin_left  = (in_array(session()->get('lang', config('app.locale')), config('constants.langs_rtl')))?'initial' : '50%';
    $margin_right = (in_array(session()->get('lang', config('app.locale')), config('constants.langs_rtl')))?'50%' : 'initial';
   
@endphp 

@section('special_css')

    <style>
            p{
                font-size:{{$p_font}};
            }
            h4{
                font-size:{{$h4_font}};
            }
            .D_rang{
                font-size:{{$D_rang_font}} !important;
            }
            .date_filter  b{
                font-size:{{$p_font}} !important;
            }
            .sec-body * {
                font-family:Georgia, 'Times New Roman', Times, serif !important;
            }
            .standard{
                font-family:arial !important ;
            }
            .card3{
                margin: 0% 0% 0% 1%;
                /* max-width: 500px; */
                position: relative;
                border-radius: 20px ;
                padding: 20px ;
                height: auto;
                background-color: white;
                box-shadow: 0px 0px 10px #3a3a3a33;
            }
            .card1{
                /* margin: 20px; */
                /* max-width: 500px; */
                border-radius: 20px ;
                padding: 20px ;
                height: 170.9px;
                background-color: white;
                box-shadow: 0px 0px 10px #3a3a3a33;
            }
            .card{
                /* margin: 20px; */
                /* max-width: 500px; */
                border-radius: 20px ;
                padding: 20px ;
                /* height: 400px; */
                 background-color: white;
                box-shadow: 0px 0px 10px #3a3a3a33;
            }
            .card-header{
                font-size: 20px;
                margin: 0px;
                /* border:1px solid black;  */
            }
            .card-box-header{
                margin:auto 10px;
                width:calc(90%/3);
                border-radius:10px ;
                background-color: #f7f7f7;
            }
            #date_search{
                background-color: #d6d6d6d5;
                color: black;
                border-radius:2px;
                padding: 5px;
                border:0px solid black;
            }
            #totalRevenueChart{
                /* border-right:1px solid black;  */

            }
            #growthChart{
                /* border:1px solid black;  */
            }
            .sec-total{
                margin: {{$margin_sec}};
                max-width: 1400px;
            }
            .clear{
                padding: 10px !important;
                height: 5px !important;
            }
            .big-number{
                font-size: 16px ;
            }
            .date_filter{
                margin-right: 4%;
                display: flex;
                width: 30%;

            }
            .date_filter_1{
                margin-left: {{$margin_left}};
                margin-right: {{$margin_right}};
                transform: translateX({{$translate}});
                display: flex;
                width: 500px;

            }
           
            .card-box-body{
                margin-top: 30px;
            }
            .card-chart-body{
                max-width:90%;
                /* max-height:100px; */
                margin: 0% 5% ;  
            }
            .scrolling{
                /* height: 60px ; */
                padding: 0% 40px;
                /* overflow-y: scroll; */
            }
             
    </style>
@endsection


 
@section('content')
    <style> 
    </style>
 
 
     
 <div class="sec-total">
    <div class="row">
        <div class="col-md-12">&nbsp;</div>
        <div class="col-md-6 text-center">
          
                <small class="date_filter_1" >  
                    <span style="font-size:18px;line-height:34px;font-family:Georgia, 'Times New Roman', Times, serif !important;"> {{__('izo.date_range')}} : &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;    &nbsp;</span>
                    <div class="input-group" dir="ltr" style="box-shadow:0px 0px 10px #3a3a3a33;border-radius:10px !important">
                        <span class="input-group-addon" style="border-top-left-radius:10px !important;border-bottom-left-radius:10px !important"><i class="fa fa-calendar" style="color:#ec6808;font-size:17px; "></i></span>
                        {!! Form::text('transaction_date_range', null, ['class' => 'form-control','id'=>'transaction_date_range' ,  'style' => 'font-size:19px;border-top-right-radius:10px !important;border-bottom-right-radius:10px !important;', 'readonly', 'placeholder' => __('report.date_range')]) !!}
                    </div>
                    

                </small>
                <h6>&nbsp;</h6>
                 
            
        </div>
         
        <div class="col-md-6 text-center">
            <small class="date_filter_1" >  
                <b class="p-5" style="line-height: 34px;font-size:17px;font-family:Georgia, 'Times New Roman', Times, serif !important; ">{{__('izo.pattern')}}  </b> &nbsp;
                <div class="col-md-8 col-md-offset-2 ml-10 ">
                    {!! Form::select('pattern_id', $patterns,null, ['class' => 'form-control select2','style'=>'font-size:18px; !important','id'=>'pattern_id' , 'placeholder' => __('ALL')]) !!}
                </div>
            </small> 
            <h6>&nbsp;</h6>
        </div>
        <div class="col-md-12">&nbsp;</div> 
    </div>
    @php  $section_left = 0; @endphp
    <div class="row sec-body">
         <!-- Total Revenue -->
         {{-- ***** --}}
            @if(auth()->user()->can('izo.box_sales'))
                <div @if(auth()->user()->can('izo.box_invoices')) class="col-md-6" @else  class="col-md-12" @endif>
                @php  $section_left = 1; @endphp
                <div @if(auth()->user()->can('izo.box_invoices')) class="col-md-6" @else  class="col-md-12" @endif>
                    <div class="card1">
                        <H4>{{__('izo.sales')}}  <small class="pull-right D_rang">{{__('izo.date_range')}}</small></H4>
                        <p>  &nbsp;</p>
                        @if(auth()->user()->can('izo.box_sales_exc'))
                            <p>{{__('izo.exc_vat')}}   <b class="display_currency pull-right big-number sales-exc standard" data-currency_symbol="true"  >{{$totalSalesExclude}}</b></p>
                        @endif
                        @if(auth()->user()->can('izo.box_sales_inc'))
                            <p>{{__('izo.inc_vat')}}   <b class="display_currency pull-right big-number sales-inc standard" data-currency_symbol="true"  >{{$totalSales}}</b></p>
                        @endif
                    </div>
                </div>
            @endif
            {{-- ***** --}}
            @if(auth()->user()->can('izo.box_invoices'))
                @php  $section_left = 1; @endphp
                <div @if(auth()->user()->can('izo.box_sales')) class="col-md-6" @else  class="col-md-12" @endif>
                    <div class="card1">
                        <H4>{{__('izo.invoices')}} <small class="pull-right D_rang">{{__('izo.date_range')}}</small></H4>
                        <p>  &nbsp;</p>
                        <p>  &nbsp;</p>
                        @if(auth()->user()->can('izo.box_invoices_number'))
                            <p>{{__('izo.number_of_invoice')}}   <b  class="pull-right big-number number-of-invoice standard">{{$numberOfInvoice}}</b></p>
                        @endif
                    </div>
                </div>
            @endif
            {{-- ##### --}}
            <div class="col-md-12"> &nbsp; </div>
            {{-- ##### --}}
            {{-- ***** --}}
            @if(auth()->user()->can('izo.box_vat'))
                {{--  TODO  IF REGISTER IN VAT SHOW THIS --}}
                @php  $section_left = 1; @endphp
                <div @if(auth()->user()->can('izo.box_customer')) class="col-md-6" @else  class="col-md-12" @endif>
                    <div class="card1">
                        <H4>{{__('izo.vat')}}  <small class="pull-right D_rang">{{__('izo.date_range')}}</small></H4>
                        <p>  &nbsp;</p>
                        <p>  &nbsp;</p>
                        @if(auth()->user()->can('izo.box_vat_amount'))
                            <p>{{__('izo.vat_amount')}}   <b class="display_currency pull-right big-number vat-sales standard" data-currency_symbol="true" >{{ $totalSalesTax}}</b></p>
                        @endif
                    </div>
                </div>
            @endif
            {{-- ***** --}}
            @if(auth()->user()->can('izo.box_customer'))
                @php  $section_left = 1; @endphp
                <div @if(auth()->user()->can('izo.box_vat')) class="col-md-6" @else  class="col-md-12" @endif>
                    <div class="card1">
                        <H4><span >{{__('izo.customers')}}</span> <small class="pull-right D_rang" >{{__('izo.date_range')}}</small></H4>
                        <p>  &nbsp;</p>
                        <p>  &nbsp;</p>
                        @if(auth()->user()->can('izo.box_customer_total'))
                            <p>{{__('izo.total_customers')}}   <b  class="pull-right big-number number-of-contact standard" >{{$contactOfInvoice}}</b></p>
                        @endif
                    </div> 
                </div> 
            @endif
            @if($section_left == 1)
                {{-- ##### --}}
                <div class="col-md-12"> &nbsp; </div>
                {{-- ##### --}}
            @endif
            {{-- ***** --}} 
            @if(auth()->user()->can('izo.box_cost_of_sales'))
                @php  $section_left = 1; @endphp
                <div @if(auth()->user()->can('izo.box_delivered')) class="col-md-6" @else  class="col-md-12" @endif>
                    <div class="card1">
                        <H4>{{__('izo.cost_of_sales')}} <small class="pull-right D_rang">{{__('izo.date_range')}}</small></H4>
                        <p>  &nbsp;</p>
                        <p>  &nbsp;</p>
                        @if(auth()->user()->can('izo.box_cost_of_sales_cos'))
                            <p>COS   <b class="display_currency pull-right big-number cost-of-sales standard" data-currency_symbol="true" >{{ $costOfDeliveredSale + $costOfUnDeliveredSale }}</b></p>
                        @endif
                    </div>
                </div>
            @endif
            {{-- ***** --}}
            @if(auth()->user()->can('izo.box_delivered'))
            @php  $section_left = 1; @endphp
            <div @if(auth()->user()->can('izo.box_cost_of_sales')) class="col-md-6" @else  class="col-md-12" @endif>
                <div class="card1">
                    <H4>{{__('izo.delivered_goods_value_left')}} <small class="pull-right D_rang">{{__('izo.date_range')}}</small>{!!__('izo.delivered_goods_value_right')!!}</H4>
                    @if(auth()->user()->can('izo.box_delivered_cost'))
                        <p>{{__('izo.cost_value')}}    <b class="display_currency pull-right big-number cost-delivered standard" data-currency_symbol="true" >{{$costOfDeliveredSale}}</b></p>
                    @endif
                    @if(auth()->user()->can('izo.box_delivered_sales'))
                    <p>{{__('izo.sales_value')}}   <b class="display_currency pull-right big-number amount-delivered standard" data-currency_symbol="true" >{{$amountOfDeliveredSale}}</b></p>
                    @endif
                </div>
            </div>
            @endif
            @if($section_left == 1)
                {{-- ##### --}}
                <div class="col-md-12"> &nbsp; </div>
                {{-- ##### --}}
            @endif
            {{-- ***** --}}  
            @if(auth()->user()->can('izo.box_gross_profit'))
            @php  $section_left = 1; @endphp
                <div @if(auth()->user()->can('izo.box_un_delivered')) class="col-md-6" @else  class="col-md-12" @endif>
                    <div class="card1">
                        <H4>{{__('izo.gross_profit')}} <small class="pull-right D_rang">{{__('izo.date_range')}}</small></H4>
                        <p>  &nbsp;</p>
                        <p>  &nbsp;</p>
                        @if(auth()->user()->can('izo.box_gross_profit_gp'))
                            <p> GP   <b class="display_currency pull-right big-number gross-profit standard" data-currency_symbol="true" >{{$totalSalesExclude - ($costOfDeliveredSale + $costOfUnDeliveredSale) }}</b></p>
                        @endif 
                    </div>
                </div> 
            @endif 
            {{-- ***** --}}
            @if(auth()->user()->can('izo.box_un_delivered'))
            @php  $section_left = 1; @endphp
            <div @if(auth()->user()->can('izo.box_gross_profit')) class="col-md-6" @else  class="col-md-12" @endif>
                <div class="card1">
                    <H4 style=" ">{{__('izo.undelivered_goods_value_left')}}<small class="pull-right D_rang">{{__('izo.date_range')}}</small>{!!__('izo.undelivered_goods_value_right')!!}</H4>
                    @if(auth()->user()->can('izo.box_un_delivered_cost'))
                        <p>{{__('izo.cost_value')}}   <b class="display_currency pull-right big-number cost-un-delivered standard" data-currency_symbol="true" >{{$costOfUnDeliveredSale}}</b></p>
                    @endif
                    @if(auth()->user()->can('izo.box_un_delivered_sales'))
                        <p>{{__('izo.sales_value')}}   <b class="display_currency pull-right big-number amount-un-delivered standard" data-currency_symbol="true" >{{$amountOfUnDeliveredSale}}</b></p>
                    @endif
                    </div>
                </div>
            @endif
            @if($section_left == 1)
                {{-- ##### --}}
                <div class="col-md-12"> &nbsp; </div>
                {{-- ##### --}}
            @endif
            {{-- ***** --}} 
            @if(auth()->user()->can('izo.box_goods_value'))
            @php  $section_left = 1; @endphp
            <div class="col-md-12">
                <div class="card1">
                    <H4><a href="{{\URL::to('/gallery/stock_report')}}">{{__('izo.goods_value')}} </a> <small class="pull-right D_rang">{{__('izo.accumulated')}}</small></H4>
                    <p>  &nbsp;</p>
                    <p>  &nbsp;</p>
                    @if(auth()->user()->can('izo.box_goods_value_num'))
                        <p>{{__('izo.goods_value')}}   <b class="display_currency pull-right big-number standard" data-currency_symbol="true" >{{ $closing }}</b></p>
                    @endif
                    </div>
                </div>
            @endif
            @if($section_left == 1)
                {{-- ##### --}}
                <div class="col-md-12"> &nbsp; </div>
                {{-- ##### --}}
            @endif
            {{-- ***** --}}
            @php $customerUrl = '/contacts?type=customer'   @endphp
            @php $supplierUrl = '/contacts?type=supplier'   @endphp
            @if(auth()->user()->can('izo.box_total_customer'))
            @php  $section_left = 1; @endphp
            <div @if(auth()->user()->can('izo.box_total_supplier')) class="col-md-6" @else  class="col-md-12" @endif>
                <div class="card1">
                    <H4><a href="{{\URL::to($customerUrl)}}">{{__('izo.total_customer')}}</a> <small class="pull-right D_rang"  style="font-size:13px !important">{{__('izo.accumulated')}}</small></H4>
                    <p>  &nbsp;</p>
                    <p>  &nbsp;</p>
                    @if(auth()->user()->can('izo.box_total_customer_balance'))
                        <p> {{__('izo.balance')}}   <b class="display_currency pull-right big-number standard" data-currency_symbol="true" >{{abs($totalBalanceCustomer)}}</b></p>
                    @endif 
                </div>
            </div> 
            @endif 
            {{-- ***** --}}
            @if(auth()->user()->can('izo.box_total_supplier'))
            @php  $section_left = 1; @endphp
            <div @if(auth()->user()->can('izo.box_total_customer')) class="col-md-6" @else  class="col-md-12" @endif>
                <div class="card1">
                    <H4><a href="{{\URL::to($supplierUrl)}}">{{__('izo.total_supplier')}}</a> <small class="pull-right D_rang" style="font-size:13px !important">{{__('izo.accumulated')}}</small></H4>
                    <p>  &nbsp;</p>
                    <p>  &nbsp;</p>
                    @if(auth()->user()->can('izo.box_total_supplier_balance'))
                        <p> {{__('izo.balance')}}   <b class="display_currency pull-right big-number standard" data-currency_symbol="true" >{{abs($totalBalanceSupplier)}}</b></p>
                    @endif   
                    </div>
                </div>
            @endif   
        </div>   
        <!-- Total Revenue -->
        <div @if($section_left == 1) class="col-md-6" @else  class="col-md-12" @endif>
            @if(auth()->user()->can('izo.box_paid_unpaid')) 
                <div @if(auth()->user()->can('izo.box_cash_bank')) class="card" @else class="card" @endif >
                    <H4>{{__('izo.paid_unpaid')}} &nbsp;&nbsp;<small>{{ " " . __('izo.inc_vat')}}</small>
                        <small class="pull-right D_rang">{{__('izo.date_range')}}</small>    
                    </H4>
                    <div class="card-box-body row">
                        <div class=" card-box-header col-md-4">
                            <h4 class="display_currency standard" id="totalSales" data-currency_symbol="true">{{$totalSales}}</h4>
                            <p>{{__('izo.total_sales')}} </p>
                        </div>
                        <div class=" card-box-header col-md-4">
                            <h4 class="display_currency standard" id="totalPaidSales" data-currency_symbol="true">{{$totalPaidSales}}</h4>
                            <p>{{__('izo.total_paid_sales')}} </p>
                        </div>
                        <div class=" card-box-header col-md-4">
                            <h4 class="display_currency standard" id="totalUnPaidSales" data-currency_symbol="true">{{$totalUnPaidSales}}</h4>
                            <p>{{__('izo.total_unpaid_sales')}} </p>
                        </div>
                    </div>
                    <div class="card-chart-body">
                        {{-- <canvas id="myChart"></canvas> --}}
                        <div id="chart" style="height: 200px; !important"></div>
                    </div>
                </div>
            @endif
            <br>
            @if(auth()->user()->can('izo.box_cash_bank'))
                @foreach ($cash as $k => $balance) 
                    @php  $CashBal = $CashBal +  $balance @endphp  
                @endforeach
                @foreach ($bank as $k => $balance)
                    @php  $BankBal = $BankBal +  $balance @endphp   
                @endforeach 
                <div @if(auth()->user()->can('izo.box_cash_bank')) class="card" @else class="card" @endif >
                    <h4>{{__('izo.cash_bank')}}
                        <h6>&nbsp;</h6>
                        <small class="date_filter pull-right" style="width: 100%;margin:{{$margin_bank}};float: left">
                            <b class="p-5" style="">{{__('izo.total_bank')}}:  &nbsp;&nbsp;&nbsp;<b class="display_currency pull-right big-number standard" data-currency_symbol="true" >{{$BankBal}}</b></b>
                        </small>
                        <h6>&nbsp;</h6>
                        <small class="date_filter pull-right" style="width: 100%;margin:{{$margin_bank}};float: right">
                            <b class="p-5">{{__('izo.total_cash')}}: &nbsp;&nbsp;&nbsp;<b class="display_currency pull-right big-number standard" data-currency_symbol="true" >{{$CashBal}}</b></b> 
                        </small>
                    <h6>&nbsp;</h6>
                    </h4>
                    <div class="scrolling">
                        <h6>{{__('izo.cash')}}</h6>
                        @foreach ($cash as $k => $balance)
                            @php
                                $url = '/account/account/'.$cashId[$k];
                            @endphp
                            <p> <a href="{{\URL::to($url)}}">{{$k}}</a>  <b class="display_currency pull-right big-number standard" data-currency_symbol="true" >{{abs($balance)}}</b></p>
                        @endforeach
                        <br>
                        <h6>{{__('izo.bank')}}</h6>
                        @foreach ($bank as $k => $balance)  
                            @php
                                $url = '/account/account/'.$bankId[$k];
                            @endphp
                            <p> <a href="{{\URL::to($url)}}">{{$k}}</a>  <b class="display_currency pull-right big-number standard" data-currency_symbol="true" >{{abs($balance)}}</b></p>
                        @endforeach
                    </div>
                </div>   
            @endif
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 "> 
                <div class="row">
                    <!-- bank -->
                    <div class="col-md-5 "> 
                        
                    </div>
                    <!-- cash -->
                    <div class="col-md-7 ">
                       
                    </div>
                </div> 
        </div>
    </div>
</div> 

 
 

    
@stop
 
@section('javascript') 
    <script type="text/javascript"> 
        //Date picker
        
        // var ctx = document.getElementById('myChart').getContext('2d');
        // var myChart = new Chart(ctx, {
        //     type: 'line', // Change this to the type of chart you want
        //     data: {
        //         labels: @json($months),
        //         datasets: [{
        //             label: 'Sample Data',
        //             data: @json($values),
        //             borderColor: '#ec6608',
        //             backgroundColor: '#ec6608',
        //         }]
        //     },
        //     options: {
        //         responsive: true,
        //         scales: {
        //             y: {
        //                 beginAtZero: true
        //             }
        //         }
        //     }
        // });
        document.addEventListener('DOMContentLoaded', function () {

                   
                transaction_date_range      = $("#transaction_date_range");
                dateRangeSettings.startDate = moment().startOf('year');
                dateRangeSettings.endDate   = moment().endOf('year');
                $('#transaction_date_range').daterangepicker(
                    dateRangeSettings,
                    function (start, end) {
                        $('#transaction_date_range').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
                        startDate = $('#transaction_date_range').val().split('~')[0];
                        endDate   = $('#transaction_date_range').val().split('~')[1];
                        search(startDate,endDate);
                    }
                );
                $('#transaction_date_range').on('cancel.daterangepicker', function(ev, picker) {
                    $('#transaction_date_range').val('');
                    startDate = "";
                    endDate   = ""; 
                    search();
                });
                function search(startDate="",endDate=""){
                    console.log(startDate,endDate);
                    $.ajax({
                        type: 'GET',
                        url: '{{ URL::to("/") }}',
                        data: {
                            'start_date': startDate,
                            'end_date': endDate,
                        },
                        success: function(response) { 
                            if (response.months && response.values && response.values2) {
                                renderChart(response.months, response.values, response.values2);
                                console.log(
                                    response.totalSalesExclude,
                                    response.totalSales,
                                    response.totalSalesTax,
                                    response.costOfSales,
                                    response.costOfDeliveredSale,
                                    response.amountOfDeliveredSale,
                                    response.grossProfit,
                                    response.costOfUnDeliveredSale,
                                    response.amountOfUnDeliveredSale,
                                    response.contactOfInvoice,
                                    response.numberOfInvoice,
                                    response.totalSales,
                                    response.totalPaidSales,
                                    response.totalUnPaidSales
                                );
                                $('.sales-exc').html(__currency_trans_from_en(response.totalSalesExclude));
                                $('.sales-inc').html(__currency_trans_from_en(response.totalSales)); 
                                $('.vat-sales').html(__currency_trans_from_en(response.totalSalesTax)); 
                                $('.cost-of-sales').html(__currency_trans_from_en(response.costOfSales));
                                $('.cost-delivered').html(__currency_trans_from_en(response.costOfDeliveredSale));
                                $('.amount-delivered').html(__currency_trans_from_en(response.amountOfDeliveredSale));
                                $('.gross-profit').html(__currency_trans_from_en(response.grossProfit));
                                $('.cost-un-delivered').html(__currency_trans_from_en(response.costOfUnDeliveredSale));
                                $('.amount-un-delivered').html(__currency_trans_from_en(response.amountOfUnDeliveredSale));
                                $('.number-of-contact').html(response.contactOfInvoice);
                                $('.number-of-invoice').html(response.numberOfInvoice);
                                $('#totalSales').html(__currency_trans_from_en(response.totalSales));
                                $('#totalPaidSales').html(__currency_trans_from_en(response.totalPaidSales));
                                $('#totalUnPaidSales').html(__currency_trans_from_en(response.totalUnPaidSales));
                            } else {
                                renderChart([], [], []);
                            }
                        },
                        error: function(xhr, status, error) {
                            renderChart([], [], []);
                        }
                    })
                }
                   
                var chart;

                function renderChart(months, values, values2) {
                    var options = {
                        chart: {
                            type: 'bar' // or 'column'
                        },
                        series: [
                            {
                                name: 'Paid Sales',
                                data:  values ,
                                color: '#ee6808',
                            },
                            {
                                name: 'UnPaid Sales',
                                data:  values2 ,
                                color: '#3a3a3a33',
                            }
                        ],
                        xaxis: {
                            categories:  months 
                        },
                        plotOptions: {
                            bar: {
                                dataLabels: {
                                    position: 'bottom' // top, center, bottom
                                }
                            }
                        },
                        dataLabels: {
                            enabled: true,
                            formatter: function (val) {
                                return val + "%";
                            },
                            offsetY: -20,
                            style: {
                                fontSize: '12px',
                                colors: ["#30475800"]
                            }
                        },
                        theme: {
                            palette: 'palette1' // If you want to use the predefined color palette
                        }
                    };
                    if (chart) { 
                        $('.card-chart-body').html('');
                        $('.card-chart-body').html('<div id="chart"></div>');
                        chart.updateOptions(options);
                        chart = new ApexCharts(document.querySelector("#chart"), options);
                        chart.render();
                    } else {  
                        chart = new ApexCharts(document.querySelector("#chart"), options);
                        chart.render();
                    } 
                }
                 
          
                search("","");
            });

    </script>
@endsection

