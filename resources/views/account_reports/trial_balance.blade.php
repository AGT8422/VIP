@extends('layouts.app')
@section('title', __( 'account.trial_balance' ))

@section('content')

@php
        $transactionUtil         = new  \App\Utils\TransactionUtil();
        $business_id             = session()->get('user.business_id');
        $currency_details        = $transactionUtil->purchaseCurrencyDetails($business_id);
@endphp


<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang( 'account.trial_balance')
    </h1>
</section>

<!-- Main content -->
<section class="content">
    <div class="row no-print">
        <div class="col-sm-12">
            
                    <!-- Page level currency setting -->
            <input type="hidden" id="p_code" value="{{$currency_details->code}}">
            <input type="hidden" id="p_symbol" value="{{$currency_details->symbol}}">
            <input type="hidden" id="p_thousand" value="{{$currency_details->thousand_separator}}">
            <input type="hidden" id="p_decimal" value="{{$currency_details->decimal_separator}}">
            
            <div class="col-sm-3 col-xs-6 pull-right">
                <label for="end_date">@lang('messages.filter_by_date'):</label>
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                    </span>
                    <input type="text" id="end_date" value="{{@format_date('now')}}" class="form-control" >
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
            <h3 class="box-title">{{session()->get('business.name')}} - @lang( 'account.trial_balance') - <span id="hidden_date">{{@format_date('now')}}</span></h3>
        </div>
          {{-- <div class="box-body">
            <table class="table table-border-center-col no-border table-pl-12" id="trial_balance_table">
                <thead>
                    <tr class="bg-gray">
                        <th>@lang('account.trial_balance')</th>
                        <th>@lang('account.credit')</th>
                        <th>@lang('account.debit')</th>
                    </tr>
                </thead>
                <tbody>   --}}
                    {{-- <tr>
                        <th>@lang('account.supplier_due'):</th>
                        <td>&nbsp;</td>
                        <td>
                            <input type="hidden" id="hidden_supplier_due" class="debit">
                            <span class="remote-data" id="supplier_due">
                                <i class="fas fa-sync fa-spin fa-fw"></i>
                            </span>
                        </td>
                    </tr> --}}
                    {{-- <tr>
                        <th>@lang('account.customer_due'):</th>
                        <td>
                            <input type="hidden" id="hidden_customer_due" class="credit">
                            <span class="remote-data" id="customer_due">
                                <i class="fas fa-sync fa-spin fa-fw"></i>
                            </span>
                        </td>
                        <td>&nbsp;</td>
                    </tr> --}}
                    {{-- <tr>
                        <th>@lang('account.account_balances'):</th>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr> --}}
               {{-- </tbody>
                <tbody id="account_balances_details">
                </tbody>
        
                <tfoot>
                    <tr class="bg-gray">
                        <th>@lang('sale.total')</th>
                        <td>
                            <span class="remote-data" id="total_credit">
                                <i class="fas fa-sync fa-spin fa-fw"></i>
                            </span>
                        </td>
                        <td>
                            <span class="remote-data" id="total_debit">
                                <i class="fas fa-sync fa-spin fa-fw"></i>
                            </span>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>   --}}
        <div class="box-body">
            <table class="table table-border-center-col no-border table-pl-12" style="width:100%" id="trial">
                <thead>
                    <tr class="bg-gray">
                        <th>@lang('lang_v1.id')</th>
                        <th>@lang('lang_v1.account_main')</th>
                        <th>@lang('lang_v1.account_type')</th>
                        <th>@lang('lang_v1.name')</th>
                        <th>@lang('account.credit')</th>
                        <th>@lang('account.debit')</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        {{-- <td class="footer_trial_total"></td> --}}
                        <td ></td>
                        <td ></td>
                        <td ></td>
                        <td ></td>
                        <td class="footer_trial_credit"></td>
                        <td class="footer_trial_debit"></td>
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
        
        <input class="fo_debit" hidden value="" >
        <input class="fo_credit" hidden value="" >
        
    </div>

    
</section>
<!-- /.content -->
@stop
@section('javascript')
 
<script type="text/javascript">

function print_ledger(){
         
        var printContainer  = $("#trial");
        element_first       = $("#trial_wrapper :first-child");
        account_book_info   = $("#trial_info");
        
         account_book_paginate    = $("#trial_paginate");
        if (element_first && account_book_info && account_book_paginate) {
            // element_first.remove();
            // account_book_info.remove();
            // account_book_paginate.remove();
        }
        
         $("#trial a").css({"text-decoration":"none","color":"black"});
         
        var printContainer1 = $(".fo_credit");
        var printContainer2 = $(".fo_debit");
        
        // var printContainer3 = $("#opening_balance");
        var printWindow    = window.open("", "_blank");
        printWindow.document.write( '<html><head><title>Print</title></head>');
        printWindow.document.write( '<style>body{font-family:arial} @media print {.no-print{ display:none }} table th{background-color:#929090;color:#fff} table tfoot{background-color:#929090;color:#fff}   table{width:100%;text-align:left;} table td{font-size:10px;padding:5px;}</style>');
        printWindow.document.write( '<body>' );
        printWindow.document.write( '<table style="width:100%"><td><button class="btn btn-primary no-print" style="background-color:#000 ;color:#fff;padding:10px" onClick="window.print();">@lang("messages.print") &nbsp; </button></td><tr></tr></table>');
        printWindow.document.write( '<table style="width:100%; ; font-weight:bold;"><tr >' );
        printWindow.document.write( '<td style="width:30%; border:0px solid black;text-align:left"><img src="{{asset("../../../uploads/img/ledger.png")}}"   style="max-width: 400px;height:100px"> </td>');
        printWindow.document.write( '<td style="width:30%;font-size:19px; border:0px solid black;text-align:left"><br><br>Trial Balance </td>' );
        printWindow.document.write( '<td style="width:35%; border:0px solid black;text-align:right"><span style="font-size:15px"><b>TAX No:</b> 100355364900003</span><br><span style="font-size:15px"> <b>P.O. Box:</b> 95659, Dubai, UAE </span><br><span style="font-size:15px"><b> Email:</b> info@dikitchen.ae </span><br><span style="font-size:15px"><b>Website:</b> www.dikitchen.ae </span><br><span style="font-size:12px"><b>Dubai:</b> (04)2520680 &nbsp;<b>Abu Dubai:</b> (02)2460163</span> <br><span style="font-size:12px"><b>Sharijah:</b> (06)5444595&nbsp;<b> Factory:</b> (06)7444305</span>');
        printWindow.document.write( '</td></tr></table>' );
        printWindow.document.write( '<table><thead><tr><th>#</th><th>Main Account</th><th>Account Type</th><th>Name</th><th>Credit</th><th>Debit</th></tr></thead>'+printContainer.html()+'<tfoot><tr><td colspan="4"></td><td>'+printContainer1.val()+'</td><td>'+printContainer2.val()+'</td></tr></tfoot></table>');
        printWindow.document.write( '</body></html>' );
        printWindow.document.close();
  
           
        
    }
    
    $(document).ready( function(){
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
            purchase_table.ajax.reload();
            
            
        });

        $('#date_range').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
            purchase_table.ajax.reload();

            
        });
    }
        //Date picker
        $('#end_date').datepicker({
            autoclose: true,
            format: datepicker_date_format
        });
        update_trial_balance();

        $('#end_date').change( function() {
             purchase_table.ajax.reload();
            $('#hidden_date').text($(this).val());
            console.log($('input#end_date').val());
        });
    });
    // function update_trial_balance(){
    //     var loader = '<i class="fas fa-sync fa-spin fa-fw"></i>';
    //     $('span.remote-data').each( function() {
    //         $(this).html(loader);
    //     });
    //     $('table#trial_balance_table tbody#capital_account_balances_details').html('<tr><td colspan="3"><i class="fas fa-sync fa-spin fa-fw"></i></td></tr>');
    //     $('table#trial_balance_table tbody#account_balances_details').html('<tr><td colspan="3"><i class="fas fa-sync fa-spin fa-fw"></i></td></tr>');
    //     var end_date = $('input#end_date').val();
    //     $.ajax({
    //         url: "{{action('AccountReportsController@trialBalance')}}?end_date=" + end_date,
    //         dataType: "json",
    //         success: function(result){
    //             $('span#supplier_due').text(__currency_trans_from_en(result.supplier_due, true));
    //             __write_number($('input#hidden_supplier_due'), result.supplier_due);

    //             $('span#customer_due').text(__currency_trans_from_en(result.customer_due, true));
    //             __write_number($('input#hidden_customer_due'), result.customer_due);

    //             var account_balances = result.account_balances;
    //             $('table#trial_balance_table tbody#account_balances_details').html('');
    //             for (var key in account_balances) {
    //                 if(result.account_balances[key] > 0){
                         
    //                     var accnt_bal = __currency_trans_from_en(result.account_balances[key]);
    //                     var accnt_bal_with_sym = __currency_trans_from_en(result.account_balances[key], true);
    //                     var account_tr = '<tr><td class="pl-20-td"><a href="account/' + key + '">' + result.account_name[key] + " || " + result.account_number[key] + ':</a></td><td><input type="hidden" class="credit" value="' + accnt_bal + '">' + accnt_bal_with_sym + '</td><td>&nbsp;</td></tr>';
    //                     $('table#trial_balance_table tbody#account_balances_details').append(account_tr);
    //                 }else if(result.account_balances[key] < 0){
                         

    //                      var eb_result = result.account_balances[key]*-1;
    //                     var accnt_bal = __currency_trans_from_en(eb_result);
    //                     var accnt_bal_with_sym =  __currency_trans_from_en(eb_result,true) ;
    //                     var account_tr = '<tr><td class="pl-20-td"><a href="account/' + key + '">'  + result.account_name[key] + " || " + result.account_number[key] + ':</a></td><td>&nbsp;</td><td><input type="hidden" class="debit" value="' + accnt_bal + '">' + accnt_bal_with_sym + '</td></tr>';
    //                     $('table#trial_balance_table tbody#account_balances_details').append(account_tr);
                    
    //                 }
    //             }

    //             var capital_account_details = result.capital_account_details;
    //             $('table#trial_balance_table tbody#capital_account_balances_details').html('');
    //             for (var key in capital_account_details) {
    //                 var accnt_bal = __currency_trans_from_en(result.capital_account_details[key]);
    //                 var accnt_bal_with_sym = __currency_trans_from_en(result.capital_account_details[key], true);
    //                 var account_tr = '<tr><td class="pl-20-td"><a href="account/' +key + '">' + key + ':</a></td><td><input type="hidden" class="credit" value="' + accnt_bal + '">' + accnt_bal_with_sym + '</td><td>&nbsp;</td></tr>';
    //                 $('table#trial_balance_table tbody#capital_account_balances_details').append(account_tr);
    //             }

    //             var total_debit = 0;
    //             var total_credit = 0;
    //             $('input.debit').each( function(){
    //                 total_debit += __read_number($(this));
    //             });
    //             $('input.credit').each( function(){
    //                 total_credit += __read_number($(this));
    //             });

    //             $('span#total_debit').text(__currency_trans_from_en(total_debit, true));
    //             $('span#total_credit').text(__currency_trans_from_en(total_credit, true));
    //         }
    //     });

    // }

    function update_trial_balance(){
        var end_date = $('input#end_date').val();
        var date_range = $('input#date_range').val();

        //Trial Table
        purchase_table = $('#trial').DataTable({
        processing: true,
        serverSide: true,
        scrollY: "75vh",
        scrollX:        true,
        scrollCollapse: true,
        ajax: {
            url: "{{action('AccountReportsController@trialBalance')}}?end_date=" + end_date+ "&date_range=" + date_range,
            data: function(d) {
                d.date = $('input#end_date').val();
                d.date_range = $('input#date_range').val();
                d = __datatable_ajax_callback(d);
            },
        },
        pageLength : -1, 
        aaSorting: [0, 'asc'],
        columns: [
            { data: 'id'  , name: 'id',class:"hide"   },
            { data: 'Main_name'  , name: 'Main_name'   },
            { data: 'sub_name'  , name: 'sub_name'   },
            { data: 'name'  , name: 'name'   },
            { data: 'credit', name: 'credit' , class: 'am_cur display_currency'  },
            { data: 'debit' , name: 'debit' , class: 'am_cur display_currency'  },
        ],
        fnDrawCallback: function(oSettings) {
            __currency_convert_recursively($('#trial'));
        },
        "footerCallback": function ( row, data, start, end, display ) {
            var total_credit = 0;
            var total_debit  = 0;
            for (var r in data){
                if(data[r].credit != ""   ){
                    total_credit += parseFloat( data[r].credit  );
                }
                if(data[r].debit != ""   ){
                    total_debit +=   parseFloat( data[r].debit )  ;
                }
                    
                 
             }
              
            //  __currency_trans_from_en(
            $('.footer_trial_debit').html(__currency_trans_from_en(parseFloat(total_debit)) );
            $('.fo_debit').val(__currency_trans_from_en(parseFloat(total_debit)) );
            $('.footer_trial_credit').html( __currency_trans_from_en(parseFloat(total_credit)) );
            $('.fo_credit').val( __currency_trans_from_en(parseFloat(total_credit)) );
              
        },
        
        });
    }
 
</script>

@endsection