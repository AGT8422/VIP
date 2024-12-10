
{{-- **1** --}}
@extends('layouts.app')
{{-- **2** --}}
@section('title', __('home.home'))
{{-- **3** --}}


@php  
    $CashBal = 0;
    $BankBal = 0;
    // dd(Auth::user());
@endphp 

@section('special_css')

    <style>

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
                height: 137.98px;
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
                margin: 0% 0% 0% 5%;
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
                margin-left: 50%;
                transform: translateX(-50%);
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
        <div class="col-md-12 text-center">
          
            <small class="date_filter_1" >  
                    <span style="font-size:18px;line-height:34px"> Date Range : &nbsp; &nbsp;</span>
                    <div class="input-group" style="box-shadow:0px 0px 10px #3a3a3a33;border-radius:10px !important">
                        <span class="input-group-addon" style="border-top-left-radius:10px !important;border-bottom-left-radius:10px !important"><i class="fa fa-calendar" style="color:#ec6808;font-size:17px;"></i></span>
                        {!! Form::text('transaction_date_range', null, ['class' => 'form-control','id'=>'transaction_date_range' ,  'style' => 'font-size:19px;border-top-right-radius:10px !important;border-bottom-right-radius:10px !important', 'readonly', 'placeholder' => __('report.date_range')]) !!}
                    </div>
                </small>
         
            
        </div>
        
        <div class="col-md-12">&nbsp;</div> 
    </div>
    <div class="row">
         <!-- Total Revenue -->
         <div class="col-md-5 ">
            <div class="col-md-6">
                <div class="card1">
                    <H4>Sales  <small class="pull-right D_rang">Date Range</small></H4>
                    <p>Excluding VAT : <b class="display_currency pull-right big-number sales-exc" data-currency_symbol="true" >{{$totalSalesExclude}}</b></p>
                    <p>Including VAT : <b class="display_currency pull-right big-number sales-inc" data-currency_symbol="true" >{{$totalSales}}</b></p>
                </div>
            </div>
            {{-- 2 --}}
            <div class="col-md-6">
                <div class="card1">
                    <H4>Invoices <small class="pull-right D_rang">Date Range</small></H4>
                    <p>  &nbsp;</p>
                    <p>Number Of Invoices : <b  class="pull-right big-number number-of-invoice">{{$numberOfInvoice}}</b></p>
                </div>
            </div>
            <div class="col-md-12">
                &nbsp;
            </div>
            {{--  TODO  IF REGISTER IN VAT SHOW THIS --}}
            <div class="col-md-6">
                <div class="card1">
                    <H4>VAT  <small class="pull-right D_rang">Date Range</small></H4>
                    <p>  &nbsp;</p>
                    <p>VAT Amount : <b class="display_currency pull-right big-number vat-sales" data-currency_symbol="true" >{{ $totalSalesTax}}</b></p>
                </div>
            </div>
            {{-- 1 --}}
            <div class="col-md-6">
                <div class="card1">
                    <H4>Customers <small class="pull-right D_rang">Date Range</small></H4>
                    <p>  &nbsp;</p>
                    <p>Total Customer : <b  class="pull-right big-number number-of-contact">{{$contactOfInvoice}}</b></p>
                </div> 
            </div> 
           
            <div class="col-md-12">
                &nbsp;
            </div>
             {{-- 2 --}}
             <div class="col-md-6">
                <div class="card1">
                    <H4>Cost Of Sales <small class="pull-right D_rang">Date Range</small></H4>
                    <p>  &nbsp;</p>
                    <p>COS : <b class="display_currency pull-right big-number cost-of-sales" data-currency_symbol="true" >{{ $costOfDeliveredSale + $costOfUnDeliveredSale }}</b></p>
                </div>
            </div>
              
            <div class="col-md-6">
                <div class="card1">
                    <H4>Delivered<small class="pull-right D_rang">Date Range</small><br> <small style="color:black">Goods  Value</small></H4>
                    <p>Cost Value : <b class="display_currency pull-right big-number cost-delivered" data-currency_symbol="true" >{{$costOfDeliveredSale}}</b></p>
                    <p>Sales Value : <b class="display_currency pull-right big-number amount-delivered" data-currency_symbol="true" >{{$amountOfDeliveredSale}}</b></p>
                </div>
            </div>
            <div class="col-md-12">
                &nbsp;
            </div>  
            {{-- # --}}
            <div class="col-md-6">
                <div class="card1">
                    <H4>Gross Profit <small class="pull-right D_rang">Date Range</small></H4>
                    <p>  &nbsp;</p>
                    <p> GP : <b class="display_currency pull-right big-number gross-profit" data-currency_symbol="true" >{{$totalSalesExclude - ($costOfDeliveredSale + $costOfUnDeliveredSale) }}</b></p>
                </div>
            </div>  
            {{-- # --}}
            <div class="col-md-6">
                <div class="card1">
                    <H4 style=" ">Undelivered<small class="pull-right D_rang">Date Range</small><br> <small style="color:black">Goods  Value</small></H4>
                    <p>Cost Value : <b class="display_currency pull-right big-number cost-un-delivered" data-currency_symbol="true" >{{$costOfUnDeliveredSale}}</b></p>
                    <p>Sales Value : <b class="display_currency pull-right big-number amount-un-delivered" data-currency_symbol="true" >{{$amountOfUnDeliveredSale}}</b></p>
                </div>
            </div>
            <div class="col-md-12">
                &nbsp;  
            </div>    
            {{-- 2 --}}
            <div class="col-md-12">
                <div class="card1">
                    <H4><a href="{{\URL::to('/gallery/stock_report')}}">Goods Value </a> <small class="pull-right D_rang">Accumulated</small></H4>
                    <p>  &nbsp;</p>
                    <p>Stock Value : <b class="display_currency pull-right big-number" data-currency_symbol="true" >{{ $closing }}</b></p>
                </div>
            </div>
            <div class="col-md-12">
                &nbsp;
            </div>  
            {{-- # --}}
            <div class="col-md-6">
                <div class="card1">
                    <H4>Total Customers <small class="pull-right D_rang">Accumulated</small></H4>
                    <p>  &nbsp;</p>
                    <p> Balance : <b class="display_currency pull-right big-number" data-currency_symbol="true" >{{abs($totalBalanceCustomer)}}</b></p>
                </div>
            </div>  
            {{-- # --}}
            <div class="col-md-6">
                <div class="card1">
                    <H4>Total Suppliers <small class="pull-right D_rang">Accumulated</small></H4>
                    <p>  &nbsp;</p>
                    <p> Balance : <b class="display_currency pull-right big-number" data-currency_symbol="true" >{{abs($totalBalanceSupplier)}}</b></p>
                </div>
            </div>   
        </div>   
        <!-- Total Revenue -->
        <div class="col-md-7 ">
            <div class="card">
                <H4>Paid/Unpaid Sales 
                    
                    <small class="date_filter pull-right">
                        <b class="p-5" style="line-height: 34px ">Pattern:</b> &nbsp;
                        <div class="col-md-12">
                            {!! Form::select('pattern_id', $patterns,null, ['class' => 'form-control select2','id'=>'pattern_id' , 'placeholder' => __('ALL')]) !!}
                        </div>
                    </small>
                </H4>
                <div class="card-box-body row">
                    <div class=" card-box-header col-md-4">
                        <h4 class="display_currency" id="totalSales" data-currency_symbol="true">{{$totalSales}}</h4>
                        <p>Total Sales</p>
                    </div>
                    <div class=" card-box-header col-md-4">
                        <h4 class="display_currency" id="totalPaidSales" data-currency_symbol="true">{{$totalPaidSales}}</h4>
                        <p>Total Paid Sales</p>
                    </div>
                    <div class=" card-box-header col-md-4">
                        <h4 class="display_currency" id="totalUnPaidSales" data-currency_symbol="true">{{$totalUnPaidSales}}</h4>
                        <p>Total Unpaid Sales</p>
                    </div>
                </div>
                <div class="card-chart-body">
                    {{-- <canvas id="myChart"></canvas> --}}
                    <div id="chart" style="height: 200px; !important"></div>
                </div>
            </div>
            <br>
            @foreach ($cash as $k => $balance) 
                @php  $CashBal = $CashBal +  $balance @endphp  
            @endforeach
            @foreach ($bank as $k => $balance)
                @php  $BankBal = $BankBal +  $balance @endphp   
            @endforeach
            <div class="card"   >
                <h4>Cash & Bank 
                    <small class="date_filter pull-right">
                        <b class="p-5">Total Bank:  &nbsp;&nbsp;&nbsp;<b class="display_currency pull-right big-number" data-currency_symbol="true" >{{$BankBal}}</b></b>
                    </small>
                    <small class="date_filter pull-right">
                        <b class="p-5">Total Cash: &nbsp;&nbsp;&nbsp;<b class="display_currency pull-right big-number" data-currency_symbol="true" >{{$CashBal}}</b></b> 
                    </small>
                </h4>
                <div class="scrolling">
                    <h6>#Cash</h6>
                    @foreach ($cash as $k => $balance)
                        @php
                            $url = '/account/account/'.$cashId[$k];
                        @endphp
                        <p> <a href="{{\URL::to($url)}}">{{$k}}</a>: <b class="display_currency pull-right big-number" data-currency_symbol="true" >{{abs($balance)}}</b></p>
                    @endforeach
                    <br>
                    <h6>#Bank</h6>
                    @foreach ($bank as $k => $balance)  
                        @php
                            $url = '/account/account/'.$bankId[$k];
                        @endphp
                        <p> <a href="{{\URL::to($url)}}">{{$k}}</a>: <b class="display_currency pull-right big-number" data-currency_symbol="true" >{{abs($balance)}}</b></p>
                    @endforeach
                </div>
            </div>   
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

