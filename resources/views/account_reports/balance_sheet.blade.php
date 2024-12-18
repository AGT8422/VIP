@extends('layouts.app')

@section('title', __( 'account.balance_sheet' ))



@section('content')



<!-- Content Header (Page header) -->

<section class="content-header">

    <h1>@lang( 'account.balance_sheet')

    </h1>

</section>



<!-- Main content -->

<section class="content">

    <div class="row no-print">

        <div class="col-sm-12">

            <div class="col-sm-3 col-xs-6 pull-right">

                    <label for="end_date">@lang('messages.filter_by_date'):</label>

                    <div class="input-group">

                        <span class="input-group-addon">

                            <i class="fa fa-calendar"></i>

                        </span>

                        <input type="text" id="end_date" value="{{@format_date('now')}}" class="form-control" readonly>

                    </div>
                    
            </div>
             
            <div class="col-sm-3 col-xs-6 pull-right">
                <div class="form-group">
                    {!! Form::label('trending_product_date_range', __('report.date_range') . ':') !!}
                    {!! Form::text('date_range', null , ['placeholder' => __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'id' => 'date_range', 'readonly']); !!}
                </div>
            </div>
           
        </div>

    </div>

    <br>

    <div class="box box-solid">

        <div class="box-header print_section">

            <h3 class="box-title">{{session()->get('business.name')}} - @lang( 'account.balance_sheet') - <span id="hidden_date">{{@format_date('now')}}</span></h3>

        </div>

        <div class="box-body">

            <table class="table table-border-center no-border table-pl-12 table-bordered dataTable" id="account_book">

                <thead>

                    <tr class="bg-gray">

                        <th>@lang( 'account.liability')</th>

                        <th>@lang( 'account.assets')</th>

                    </tr>

                </thead>

                <tbody>

                    <tr>

                        <td>

                            <table class="table" id="liability_table">

                                <tbody id="account_balances" class="pl-20-td">

                                    <tr><td colspan="2"><i class="fas fa-sync fa-spin fa-fw"></i></td></tr>

                                </tbody>

                            </table>

                        </td>

                        <td>

                            <table class="table" id="assets_table">

                                <tbody id="account_balances" class="pl-20-td">

                                    <tr><td colspan="2"><i class="fas fa-sync fa-spin fa-fw"></i></td></tr>

                                </tbody>

                            </table>

                        </td>

                    </tr>
                    
                </tbody>

                <tfoot>


                    <tr class="bg-gray">

                        <td>

                            <table class="table bg-gray mb-0 no-border">

                                <tr>

                                    <th>

                                        @lang('account.total_liability'): 

                                    </th>

                                    <td>

                                        <span id="total_liabilty"><i class="fas fa-sync fa-spin fa-fw"></i></span>

                                    </td>

                                </tr>

                            </table>

                        </td>

                        <td>

                            <table class="table bg-gray mb-0 no-border">

                                <tr>

                                    <th>

                                        @lang('account.total_assets'): 

                                    </th>

                                    <td>

                                        <span id="total_assets"><i class="fas fa-sync fa-spin fa-fw"></i></span>

                                    </td>

                                </tr>

                            </table>

                        </td>

                    </tr>

                    @php
                        
                        
                    @endphp

                    <tr class="bg-gray">

                        <td>

                            <table class="table bg-gray mb-0 no-border" id="profit_amount" style="width: 100%">

                                <tr>

                                    <th>

                                        @lang('account.profit_amount'): 

                                    </th>

                                    <td>
                                        
                                        <span id="profit_amount" style="text-align: right"><i class="fas fa-sync fa-spin fa-fw"></i></span>

                                    </td>

                                </tr>

                            </table>

                        </td>



                        <td>

                            <table class="table bg-gray mb-0 no-border" id="loss_amount" style="width: 100%">

                                <tr>

                                    <th>

                                        @lang('account.loss_amount'): 

                                    </th>

                                    <td>

                                        <span id="loss_amount" style="text-align: right"><i class="fas fa-sync fa-spin fa-fw"></i></span>

                                    </td>

                                </tr>

                            </table>

                        </td>

                    </tr>

                    <tr class="bg-gray">

                        <td>

                            <table class="table bg-gray mb-0 no-border">

                                <tr>

                                  

                                </tr>

                            </table>

                        </td>

                        <td>

                            <table class="table bg-gray mb-0 no-border" id="close_stock" style="width: 100%">

                              



                                    <tr>

                                        <th>

                                            @lang('account.close_stock'): 

                                        </th>

                                        <td>

                                            <span id="close_stock" style="text-align: right"><i class="fas fa-sync fa-spin fa-fw"></i></span>  

                                        </td>

                                    </tr>

                                 

                            </table>

                        </td>

                    </tr>

                    <tr class="bg-gray">

                        <td>

                            <table class="table bg-gray mb-0 no-border">

                                <tr>

                                    <th>

                                        @lang('Total'): 

                                    </th>

                                    <td>

                                        <span id="total_l" style="text-align: right"><i class="fas fa-sync fa-spin fa-fw"></i></span>

                                    </td>

                                </tr>

                             

                            </table>

                        </td>

                        <td>

                            <table class="table bg-gray mb-0 no-border" id="close_stock_s" style="width: 100%">

                              



                                    <tr>

                                        <th>

                                            @lang('Total'): 

                                        </th>

                                        <td>

                                            <span id="total_s" style="text-align: right"><i class="fas fa-sync fa-spin fa-fw"></i></span>

                                        </td>

                                    </tr>

                                 

                            </table>

                        </td>

                    </tr>

                  

                </tfoot>

            </table>

        </div>

        <div class="box-footer">

            

          <div class="col-sm-1">

            <div class="form-group">

                <button class="btn btn-primary  " onClick="print_ledger();">@lang("messages.print") Preview  &nbsp;<i class="fa fas fa-eye"></i></button>

            </div>

        </div>

        </div>

    </div>



</section>

<!-- /.content -->

@stop

@section('javascript')



<script type="text/javascript">
    if ($('#date_range').length == 1) {    
         $('#date_range').daterangepicker({
            ranges: ranges,
            autoUpdateInput: false,
            locale: {
                format: moment_date_format,
                cancelLabel: LANG.clear,
                applyLabel: LANG.apply,
                customRangeLabel: LANG.custom_range,
            },
        });
        $('#date_range').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(
                picker.startDate.format(moment_date_format) +
                    ' ~ ' +
                    picker.endDate.format(moment_date_format)
            );
            update_balance_sheet();
            
            $('#hidden_date').text($(this).val());
        });

        $('#date_range').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
            update_balance_sheet();

            $('#hidden_date').text($(this).val());
        });
    }
    function print_ledger(){

         

        var printContainer  = $("#account_book");

        // element_first       = $("#account_book_wrapper :first-child");

        // account_book_info   = $("#account_book_info");

        

        // transaction_date_range   = $("#transaction_date_range");

        // account_book_paginate    = $("#account_book_paginate");

        // if (element_first && account_book_info && account_book_paginate) {

        //     // element_first.remove();

        //     account_book_info.remove();

        //     account_book_paginate.remove();

        // }

        

         $("#account_book a").css({"text-decoration":"none","color":"black"});

         

        // var printContainer1 = $("#account_balance");

        // var printContainer2 = $("#account_name");

        // var printContainer3 = $("#opening_balance");

        var printWindow    = window.open("", "_blank");

        printWindow.document.write( '<html><head><title>Print</title></head>');

        printWindow.document.write( '<style>body{font-family:arial} @media print {.no-print{ display:none }} table th{background-color:#929090;color:#fff} table tfoot{background-color:#929090;color:#fff}   table{width:100%;text-align:left;} table td{font-size:10px;padding:5px;}</style>');

        printWindow.document.write( '<body>' );

        printWindow.document.write( '<table style="width:100%"><td><button class="btn btn-primary no-print" style="background-color:#000 ;color:#fff;padding:10px" onClick="window.print();">@lang("messages.print") &nbsp; </button></td><tr></tr></table>');

        printWindow.document.write( '<table style="width:100%; ; font-weight:bold;"><tr >' );

        printWindow.document.write( '<td style="width:30%; border:0px solid black;text-align:left"><img src="{{asset("../../../uploads/img/ledger.png")}}"   style="max-width: 400px;height:100px"> </td>');

        printWindow.document.write( '<td style="width:30%;font-size:19px; border:0px solid black;text-align:left"><br><br>Balance Sheet</td>' );

        printWindow.document.write( '<td style="width:35%; border:0px solid black;text-align:right"><span style="font-size:15px"><b>TAX No:</b> 100355364900003</span><br><span style="font-size:15px"> <b>P.O. Box:</b> 95659, Dubai, UAE </span><br><span style="font-size:15px"><b> Email:</b> info@dikitchen.ae </span><br><span style="font-size:15px"><b>Website:</b> www.dikitchen.ae </span><br><span style="font-size:12px"><b>Dubai:</b> (04)2520680 &nbsp;<b>Abu Dubai:</b> (02)2460163</span> <br><span style="font-size:12px"><b>Sharijah:</b> (06)5444595&nbsp;<b> Factory:</b> (06)7444305</span>');

        printWindow.document.write( '</td></tr></table>' );

        printWindow.document.write( '<table>'+printContainer.html()+'</table>');

        printWindow.document.write( '</body></html>' );

        printWindow.document.close();

  

           

        

    }

    

    $(document).ready( function(){

        //Date picker

        $('#end_date').datepicker({

            autoclose: true,

            format: datepicker_date_format

        });

        update_balance_sheet();



        $('#end_date').change( function() {

            update_balance_sheet();

            $('#hidden_date').text($(this).val());

        });
         

    });



    function update_balance_sheet(){

        var loader = '<i class="fas fa-sync fa-spin fa-fw"></i>';

        $('span.remote-data').each( function() {
            $(this).html(loader);
        });



        $('table#liability_table tbody#account_balances').html('<tr><td colspan="2"><i class="fas fa-sync fa-spin fa-fw"></i></td></tr>');
        $('table#assets_table tbody#account_balances').html('<tr><td colspan="2"><i class="fas fa-sync fa-spin fa-fw"></i></td></tr>');
        $('table#assets_table tbody#capital_account_balances').html('<tr><td colspan="2"><i class="fas fa-sync fa-spin fa-fw"></i></td></tr>');
        
        $('table#close_stock span#close_stock').html('<tr><td colspan="2"><i class="fas fa-sync fa-spin fa-fw"></i></td></tr>');
        $('span#total_assets').html('<tr><td colspan="2"><i class="fas fa-sync fa-spin fa-fw"></i></td></tr>');
        $('span#total_liabilty').html('<tr><td colspan="2"><i class="fas fa-sync fa-spin fa-fw"></i></td></tr>');
        $('span#profit_amount').html('<tr><td colspan="2"><i class="fas fa-sync fa-spin fa-fw"></i></td></tr>');
        $('span#loss_amount').html('<tr><td colspan="2"><i class="fas fa-sync fa-spin fa-fw"></i></td></tr>');
        $('span#total_s').html('<tr><td colspan="2"><i class="fas fa-sync fa-spin fa-fw"></i></td></tr>');
        $('span#total_l').html('<tr><td colspan="2"><i class="fas fa-sync fa-spin fa-fw"></i></td></tr>');



        var end_date = $('input#end_date').val();
        var date_range = $('input#date_range').val();

        $.ajax({

            url: "{{action('AccountReportsController@balanceSheet')}}?end_date=" + end_date + "&date_range=" + date_range,

            dataType: "json",

            success: function(result){

                $('span#supplier_due').text(__currency_trans_from_en(result.supplier_due, true));

                __write_number($('input#hidden_supplier_due'), result.supplier_due);

                $('span#customer_due').text(__currency_trans_from_en(result.customer_due, true));

                __write_number($('input#hidden_customer_due'), result.customer_due);

                $('span#closing_stock').text(__currency_trans_from_en(result.closing_stock, true));

                __write_number($('input#hidden_closing_stock'), result.closing_stock);

                var account_details_assets = result.account_details_assets;

                var account_details_liability = result.account_details_liability;

                $('table#assets_table tbody#account_balances').html('');

                $('table#liability_table tbody#account_balances').html('');

                var total_liability = 0;

                var total_assets    = 0;

                for (var key in account_details_assets) {

                     

                    if(result.account_details_assets[key]<0){
                       
                      

                        var type = "  CR (-)";

                        var amount = parseFloat(result.account_details_assets[key])*-1;

                        var accnt_bal = __currency_trans_from_en(amount);

                        var accnt_bal_with_sym = __currency_trans_from_en(amount, true);

                        var style ='<td><input type="hidden" class="asset" value="' + accnt_bal + '">' + accnt_bal_with_sym + '</td> ';

                        total_assets += parseFloat(result.account_details_assets[key]);

                    }else if(result.account_details_assets[key]>0){

                        var type = "   DR ";

                        var amount = result.account_details_assets[key];

                        var accnt_bal = __currency_trans_from_en(amount);

                        var accnt_bal_with_sym = __currency_trans_from_en(amount, true);

                        var style =' <td><input type="hidden" class="asset" value="' + accnt_bal + '">' + accnt_bal_with_sym + '</td>';

                        total_assets += parseFloat(result.account_details_assets[key]);

                    }

                    



                    var account_tr = '<tr><td class="pl-20-td"><strong><a href="account/'+key+'">' + result.account_name_assets[key] + " || " + result.account_number_assets[key] + '</a></strong> &nbsp;:</td>' + style + ' <td><strong>' + type +  '</strong></td></tr>';

                    $('table#assets_table tbody#account_balances').append(account_tr);

              

                

                }

                for (var key in account_details_liability) {



                    if(result.account_details_liability[key]<0){

                        var type = "   DR (-)"; 

                        var amount = parseFloat(result.account_details_liability[key])*-1;

                        var accnt_bal = __currency_trans_from_en(amount);

                        var accnt_bal_with_sym = __currency_trans_from_en(amount, true);

                        var style ='<td><input type="hidden" class="asset" value="' + accnt_bal + '">' + accnt_bal_with_sym + '</td>';

                        total_liability += parseFloat(result.account_details_liability[key]);

                    }else if(result.account_details_liability[key]>0){

                        var type = "   CR ";

                        var amount = parseFloat(result.account_details_liability[key]);

                        var accnt_bal = __currency_trans_from_en(amount);

                        var accnt_bal_with_sym = __currency_trans_from_en(amount, true);

                        var style ='<td><input type="hidden" class="asset" value="' + accnt_bal + '">' + accnt_bal_with_sym + '</td>';

                        total_liability += parseFloat(result.account_details_liability[key]);



                    } 

                    var account_tr = '<tr><td class="pl-20-td"><strong><a href="account/'+key+'">' + result.account_name_liability[key] + " || " + result.account_number_liability[key] +  '</a></strong> &nbsp;:</td>' + style + ' <td><strong>' + type +  '</strong></td></tr>';

                    $('table#liability_table tbody#account_balances').append(account_tr);

                    

                }
                 var cols = result.closing_stock.toFixed(2);
                
                 var net_sell      = Math.abs(result.data['total_sell']) + Math.abs(result.data['total_sell_shipping_charge']) - Math.abs(result.data['total_sell_return']) - Math.abs(result.data['total_sell_discount']);
                 net_sell         *=  -1;
                 var net_purchase  = Math.abs(result.data['total_purchase']) + Math.abs(result.data['total_purchase_shipping_charge'])   - Math.abs(result.data['total_purchase_return']) - Math.abs(result.data['total_purchase_discount']) + Math.abs(result.data['opening_stock']) - Math.abs(result.closing);
                 net_purchase      *=  -1;
                 var cross_profit  = Math.abs(net_sell)  - Math.abs(net_purchase);
                 cross_profit      *=  -1;
                 var net_profit    = Math.abs(cross_profit) - Math.abs(result.data['total_expense'])  + Math.abs(result.data['total_reveneus']);
                  net_profit       = net_profit.toFixed(2);
            
                $('table#close_stock span#close_stock').html(__currency_trans_from_en(result.closing_stock.toFixed(2), true));

                var capital_account_details = result.capital_account_details;

                $('table#assets_table tbody#capital_account_balances').html('');

                for (var key in capital_account_details) {

                    var accnt_bal = __currency_trans_from_en(result.capital_account_details[key]);

                    var accnt_bal_with_sym = __currency_trans_from_en(result.capital_account_details[key], true);

                    var account_tr = '<tr><td class="pl-20-td">' + key + ':</td><td><input type="hidden" class="asset" value="' + accnt_bal + '">' + accnt_bal_with_sym + '</td></tr>';

                    $('table#assets_table tbody#capital_account_balances').append(account_tr);

                }
          
                $('span#profit_amount').text(__currency_trans_from_en(net_profit, true) + "    ");
                $('span#loss_amount').text("    ");

        

                if(total_assets < 0){
                    total_as  =  total_assets * -1 ; 
                    total_s   =   total_as  + parseFloat(cols);
                    
                    $('span#total_s').html(__currency_trans_from_en(total_s, true) + "    ");
                    $('span#total_assets').text(__currency_trans_from_en(total_as, true) + "    ");

                }else if(total_assets > 0){

                    total_as  =  total_assets  ;
                    total_s   =   total_as  + parseFloat(cols);
                    $('span#total_s').html(__currency_trans_from_en(total_s, true) + "    ");
                    $('span#total_assets').text(__currency_trans_from_en(total_as, true) + "    ");

                }else{

                    total_as  = total_assets ;
                    total_s   = total_as  + parseFloat(cols);
                    $('span#total_s').html(__currency_trans_from_en(total_s, true) + "    ");
                    $('span#total_assets').text(__currency_trans_from_en(total_as, true));

                
                }
                if(total_liability < 0){

                    total_li =  total_liability * -1 ;  
                    total_l  =  total_liability + parseFloat(net_profit) ;  
                    $('span#total_l').html(__currency_trans_from_en(total_l, true) + "    ");
                    $('span#total_liabilty').text(__currency_trans_from_en(total_li, true) + "    ");

                 }else if(total_liability > 0){
                    total_li = total_liability  ;
                    total_l  =  total_liability + parseFloat(net_profit) ;  
                    $('span#total_l').html(__currency_trans_from_en(total_l, true) + "    ");
                    $('span#total_liabilty').text(__currency_trans_from_en(total_li, true) + "    " );

                 }else{

                    total_li = total_liability ;
                    total_l  =  total_liability + parseFloat(net_profit) ;  
                    $('span#total_l').html(__currency_trans_from_en(total_l, true) + "    ");
                    $('span#total_liabilty').text(__currency_trans_from_en(total_li, true));

                         

                }
            }

        });

    }

</script>



@endsection