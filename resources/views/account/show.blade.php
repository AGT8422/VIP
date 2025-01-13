@extends('layouts.app')

        @if($account->cost_center == 1)

            @section('title', __('account.account_book_cost_center'))

        @else

            @section('title', __('account.account_book'))

        @endif



@section('content')



<!-- Content Header (Page header) -->

<section class="content-header">

    <h1>

        @if($account->cost_center == 1)

            @lang('account.account_book_cost_center')

        @else

            @lang('account.account_book')

        @endif
        
    </h1>
    @php $mainUrl = '/account/account';  @endphp  
    <h5><i><b><a href="{{\URL::to($mainUrl)}}">{{ __("lang_v1.payment_accounts") }} {{  __("izo.>") . " " }}</a></b><b>@if($account->cost_center == 1) @lang('account.account_book_cost_center') @else @lang('account.account_book') @endif </b> </i> {{  __("izo.>") . " " }} <i>  {{ $account->name }}  </i></h5>
    
</section>

 

<!-- Main content -->

<section class="content">

      <!-- Page level currency setting -->

	<input type="hidden" id="p_code" value="{{$currency_details->code}}">

	<input type="hidden" id="p_symbol" value="{{$currency_details->symbol}}">

	<input type="hidden" id="p_thousand" value="{{$currency_details->thousand_separator}}">

	<input type="hidden" id="p_decimal" value="{{$currency_details->decimal_separator}}">

    <div class="row">

        <div class="col-sm-4 col-xs-6">

            <div class="box box-solid">

                <div class="box-body">

                    <table class="table">

                        

                        <tr>

                            <th>@lang('account.account_name'): </th>

                            <td>

                                <a href="/contacts/{{$account->contact_id}}" target="_blank">

                                {{$account->name}}

                                </a>

                                <span  hidden   id="account_name">{{$account->name}}<span>

                                <span  hidden   id="opening_balance"> <span>



                            </td>

                        </tr>

                        <tr>

                            <th>@lang('lang_v1.account_type'):</th>

                            <td>@if(!empty($account->account_type->parent_account)) {{$account->account_type->parent_account->name}} - @endif {{$account->account_type->name ?? ''}}</td>

                        </tr>

                        <tr>

                            <th>@lang('account.account_number'):</th>

                            <td>{{$account->account_number}}</td>

                        </tr>

                        <tr>

                            <th>@lang('lang_v1.balance'):</th>

                            <td><span id="account_balance"></span></td>

                        </tr>

                    </table>

                </div>

            </div>

        </div>

        <div class="col-sm-8 col-xs-12">

            <div class="box box-solid">

                <div class="box-header">

                    <h3 class="box-title"> <i class="fa fa-filter" aria-hidden="true"></i> @lang('report.filters'):</h3>

                </div>

                <div class="box-body">

                    <div class="col-sm-6">

                        <div class="form-group">

                            {!! Form::label('transaction_date_range', __('report.date_range') . ':') !!}

                            <div class="input-group">

                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>

                                {!! Form::text('transaction_date_range', null, ['class' => 'form-control', 'readonly', 'placeholder' => __('report.date_range')]) !!}

                            </div>

                        </div>

                    </div>

                    <div class="col-sm-6">

                        <div class="form-group">

                            {!! Form::label('transaction_type', __('account.transaction_type') . ':') !!}

                            <div class="input-group">

                                <span class="input-group-addon"><i class="fas fa-exchange-alt"></i></span>

                                {!! Form::select('transaction_type', ['' => __('messages.all'),'debit' => __('account.debit'), 'credit' => __('account.credit')], '', ['class' => 'form-control']) !!}

                            </div>

                        </div>

                    </div>

                    <div class="col-sm-6">

                        <div class="form-group">

                            {!! Form::label('cost_center', __('home.Cost Center') . ':') !!}

                            <div class="input-group">

                                <span class="input-group-addon"><i class="fa fa-store"></i></span>

                                {!! Form::select('cost_center', $costcenter , null, ['class' => 'form-control select2','id'=>'costcenter','placeholder' => __('messages.please_select')]) !!}

                            </div>

                        </div>

                    </div>

                    <div class="col-sm-3">

                        <div class="form-group box_check">

                            <div class="checkbox">

                                
                                <label for="check_box">@lang( 'home.previous_balance' )</label>
                                <input type="checkbox"  id="check_box"    value="1"   > 

                                 

                            </div>

                        </div>

                    </div>

                    <div class="col-sm-1">

                        <div class="form-group p-10">
                            <button class="btn btn-primary" onClick="print_ledger();">@lang("messages.print") Preview  &nbsp;<i class="fa fas fa-eye"></i></button>
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

    <div class="row">

        <div class="col-sm-12">

        	<div class="box">

                <div class="box-body">

                    @can('account.access')
                        @include("account.action_parents.account_book")
                    @endcan

                    
                    
                    {{-- <div class="table-responsive">
 
                        <table class="table table-bordered table-striped dataTable" id="account_book" style="width:100%">    
                    
                            <thead>
                    
                                <tr>
                    
                                    <th>@lang('home.Ref No')</th>
                    
                                    <th>@lang( 'messages.date' )</th>
                    
                                    <th>@lang( 'lang_v1.description' )</th>
                    
                                    <th>@lang( 'brand.note' )</th>
                    
                                    <th>@lang( 'lang_v1.added_by' )</th>
                    
                                    <th>@lang( 'home.Cost Center' )</th>
                    
                                    <th class="header_debit">@lang('account.debit')</th>
                    
                                    <th class="header_credit">@lang('account.credit')</th>
                    
                                    <th class="header_balance">@lang( 'lang_v1.balance' )</th>
                    
                                </tr>
                    
                              
                    
                            </thead>
                    
                            <tbody>
                                <tr style="background-color: burlywood;color:black;font-weight:bolder">
                                    @php
                                        if($start_date == null){ $start_date = "2023-10-1"; }
                                        $row             = \App\AccountTransaction::find($id_first);
                                        $os_debit        = \App\Account::amount_balance($row,$accounts,$check_box,$rows_count,$id_first,$start_date,'debit');
                                        $os_credit       = \App\Account::amount_balance($row,$accounts,$check_box,$rows_count,$id_first,$start_date,'credit');
                                        $balance         =  $os_debit - $os_credit;
                                         
                                    @endphp
                                    <td style="text-align: center" colspan="9">
                                        {{$balance}}
                                    </td>
                                    
                                </tr>
                                @php $counter = 0;  @endphp
                                @foreach($accounts as $row)
                                   <tr>
                                        {{-- reference no --}}
                                         {{-- <td>
                                            @php $ref_no =  ($row->transaction)?$row->transaction->ref_no.' '.$row->transaction->invoice_no:$row->transaction_id; @endphp
                                                @if ($row->transaction)  
                                                    @if ($row->transaction->ref_no)  
                                                        <a href="#" data-href="'.url('purchases/'.$row->transaction_id).'" class="btn-modal" data-container=".view_modal">{{$ref_no}}</a>
                                                    @else 
                                                        <a href="#" data-href="'.url('sells/'.$row->transaction_id).'" class="btn-modal" data-container=".view_modal">{{$ref_no}}</a>
                                                    @endif
                                                @else
                                                        <a href="#" data-href="'.url('account/account-ref/'.$row->id).'" class="btn-modal" data-container=".view_modal">{{$row->parent_ref}}</a>     
                                                @endif
                                        </td> --}}
                                        {{-- date --}}
                                        {{-- <td>
                                            @php $date_i   = \Carbon\Carbon::parse($row->operation_date); @endphp
                                            <span  > {{ $date_i->format("Y-m-d") }} </span> 
                                        </td> --}}
                                        {{-- description --}}
                                        {{-- <td>
                                            @php
                                             $details = '';
                                            if (!empty($row->sub_type)) {
                                                $details = __('account.' . $row->sub_type);
                                                if (in_array($row->sub_type, ['fund_transfer', 'deposit']) && !empty($row->transfer_transaction)) {
                                                    if ($row->type == 'credit') {
                                                        $details .= ' ( ' . __('account.from') .': ' . $row->transfer_transaction->account->name . ')';
                                                    } else {
                                                        $details .= ' ( ' . __('account.to') .': ' . $row->transfer_transaction->account->name . ')';
                                                    }
                                                }
                                            } else {
                                                if (!empty($row->transaction->type)) {
                                                    if ($row->transaction->type == 'purchase') {
                                                        $details = __('lang_v1.purchase') . '<br><b>' . __('purchase.supplier') . ':</b> ' . $row->transaction->contact->name . '<br><b>'.
                                                        __('purchase.ref_no') . ':</b> <a href="#" data-href="' . action("PurchaseController@show", [$row->transaction->id]) . '" class="btn-modal" data-container=".view_modal">' . $row->transaction->ref_no . '</a>';
                                                    }elseif ($row->transaction->type == 'expense') {
                                                        $details = __('lang_v1.expense') . '<br><b>' . __('purchase.ref_no') . ':</b>' . $row->transaction->ref_no;
                                                    } elseif ($row->transaction->type == 'sale') {
                                                        $details = __('sale.sale') . '<br><b>' . __('contact.customer') . ':</b> ' . $row->transaction->contact->name . '<br><b>'.
                                                        __('sale.invoice_no') . ':</b> <a href="#" data-href="' . action("SellController@show", [$row->transaction->id]) . '" class="btn-modal" data-container=".view_modal">' . $row->transaction->invoice_no . '</a>';
                                                    }
                                                }
                                            }

                                            if (!empty($row->payment_ref_no)) {
                                                if (!empty($details)) {
                                                    $details .= '<br/>';
                                                }
                                                if($row->check_id != null){
                                                    
                                                
                                                    $details .= '<b>' . __('lang_v1.pay_reference_no') . ':</b> <button class="btn btn-modal btn-link" data-container=".view_modal" data-href="'.action("General\CheckController@show",[$row->check->id]).'">' . $row->check->ref_no.'</button>';
                                                }else if($row->payment_voucher_id != null){
                                                    $voucher =\App\Models\PaymentVoucher::find($row->payment_voucher_id);
                                                    $details .= '<b>' . __('lang_v1.pay_reference_no') . ':</b> <button class="btn btn-modal btn-link" data-container=".view_modal" data-href="'.action("General\PaymentVoucherController@show",[$voucher->id]).'">' . $voucher->ref_no.'</button>';
                                                }else{
                                                    
                                                    $details .= '<b>' . __('lang_v1.pay_reference_no') . ':</b> ' . $row->payment_ref_no;
                                                }
                                            }
                                            if (!empty($row->payment_for)) {
                                                if (!empty($details)) {
                                                    $details .= '<br/>';
                                                }

                                                $details .= '<b>' . __('account.payment_for') . ':</b> ' . $row->payment_for;
                                            }

                                            if ($row->is_advance == 1) {
                                                $details .= '<br>(' . __('lang_v1.advance_payment') . ')';
                                            }
                                            @endphp
                                            <span>{!!$details!!}</span>
                                        </td> --}}
                                        {{-- note --}}
                                        {{-- <td>
                                            @if($row->note == "refund Collect") 
                                                    @php $html = "Refund Cheque"; @endphp
                                            @else 
                                                @if ($row->note == "Add Purchase") 
                                                    @if($row->transaction) 
                                                        @php $html = $row->transaction->additional_notes;@endphp
                                                    @else 
                                                        @php  $html =  $row->note;@endphp
                                                    @endif
                                                @elseif ($row->note == "Add Sale") 
                                                    @if($row->transaction) 
                                                        @php $html = $row->transaction->sell_line_note;@endphp
                                                    @else 
                                                        @php  $html =  $row->note;@endphp
                                                    @endif
                                                @elseif ($row->note == "Add Cheque") 
                                                    @if($row->check)  
                                                        @php  $html = $row->check->note;@endphp
                                                    @else 
                                                        @php $html = "";@endphp
                                                    @endif
                                                @else  
                                                        @php $html = $row->note;@endphp
                                                @endif
                                            @endif
                                            
                                            <span>{{$html}}</span>
                                        </td> --}}
                                        {{-- added by --}}
                                        {{-- <td>
                                             
                                            {{ $row->added_by }}
                                        </td> --}}
                                        {{-- cost center --}}
                                        {{-- <td>
                                            @if( !empty($row->transaction) ) 
                                                @if(in_array($row->transaction->id,$id_account["transaction"])) 
                                                    @php 
                                                        $cost_center     = \App\Account::find($row->transaction->cost_center_id);
                                                        $name_of_account = $cost_center->name;
                                                    @endphp 
                                                @else 
                                                    @php
                                                        $name_of_account = "";
                                                    @endphp
                                                @endif
                                            @elseif( !empty($row->daily_payment_item) ) 
                                                @if(in_array($row->daily_payment_item->id,$id_account["daily"])) 
                                                    @php
                                                        $cost_center     = \App\Account::find($row->daily_payment_item->cost_center->id);
                                                        $name_of_account = $cost_center->name;
                                                    @endphp
                                                @else 
                                                    @php
                                                        $name_of_account = "";
                                                    @endphp
                                                @endif
                                            @elseif( !empty($row->gournal_voucher_item) ) 
                                                @if(in_array($row->gournal_voucher_item->id,$id_account["gournal"])) 
                                                    @php
                                                        $cost_center     = \App\Account::find($row->gournal_voucher_item->cost_center->id);
                                                        $name_of_account = $cost_center->name;
                                                    @endphp
                                                @else 
                                                    @php
                                                        $name_of_account = "";
                                                    @endphp
                                                @endif
                                            @elseif( !empty($row->additional_shipping_item) ) 
                                                @if(in_array($row->additional_shipping_item->id,$id_account["additional"])) 
                                                    @php
                                                        $cost_center     = \App\Account::find($row->additional_shipping_item->cost_center->id);
                                                    $name_of_account = $cost_center->name;
                                                    @endphp
                                                @else  
                                                    @php
                                                        $name_of_account = "";
                                                    @endphp
                                                @endif
                                            @elseif( !empty($row->return_transaction) ) 
                                                @if(in_array($row->return_transaction->id,$id_account["return"])) 
                                                    @php
                                                        $cost_center     = \App\Account::find($row->return_transaction->cost_center->id);
                                                        $name_of_account = $cost_center->name;
                                                    @endphp
                                                @else 
                                                    @php
                                                        $name_of_account = "";
                                                    @endphp
                                                @endif
                                            @else 
                                                    @php
                                                        $name_of_account = "";
                                                    @endphp
                                            @endif
                                            <span >{{ $name_of_account }}</span> 
                                        </td> --}}
                                        {{-- debit --}}
                                        {{-- <td>
                                            @if($row->type == 'debit' )  
                                                 <span class="display_currency" data-currency_symbol="true">{{ $row->amount }}</span>
                                                 @php $os_debit += $row->amount; @endphp
                                            @else
                                                <span > </span>
                                            @endif
                                        </td> --}}
                                        {{-- credit --}}
                                        {{-- <td>
                                            @if($row->type == 'credit' )  
                                                 <span class="display_currency" data-currency_symbol="true">{{ $row->amount }}</span> 
                                                 @php $os_credit += $row->amount; @endphp
                                            @else
                                                <span > </span>
                                            @endif

                                        </td> --}}
                                        {{-- balance --}}
                                        {{-- <td>
                                            @php
                                                $os_debit   = \App\Account::row_balance($row,$accounts,$check_box,$rows_count,$id_first,'debit');
                                                $os_credit  = \App\Account::row_balance($row,$accounts,$check_box,$rows_count,$id_first,'credit');
                                                $balance    =  $os_debit - $os_credit;
                                                $bal_text   =  $balance;
                                                $bl         =  ' / Debit';
                                                if ($balance < 0 ) {
                                                    $bl = ' / Credit';
                                                    $bal_text  =  abs($balance);
                                                }
                                            @endphp
                                            <span class="display_currency" data-currency_symbol="true"> {{ $bal_text }}</span> {{ $bl }}  
                                        </td>
                                    </tr>
                                    @php $counter += 1;  @endphp
                                @endforeach
                            </tbody>
                    
                            
                    
                             <tfoot>
                    
                                <td >{{"Totla Rows : " . $counter}}</td>
                    
                                <td ></td>
                    
                                <td ></td>
                    
                                <td ></td>
                    
                                <td ></td>
                    
                                <td ></td>
                    
                                <td class="debit_footer">{{round($os_debit,2). " AED"}}</td> 
                    
                                <td class="credit_footer">{{round($os_credit,2) . " AED"}}</td> 
                    
                                <td class="balance_footer" style="font-weight:bolder">{{round($os_debit,2) - round($os_credit,2)}}   {{(round($os_debit,2) - round($os_credit,2)<0)?" Credit":" Debit"}}</td>
                    
                            </tfoot>
                    
                        </table>
                     --}}
                    {{-- </div> --}}  
              </div>
                <div class="header_blc" id="header_blc" hidden ></div>
            </div>

        </div>

    </div>  


    
    <input type="text" id="balance_added" hidden value = "0">
    <input type="text" id="balance_cost_center" hidden value = "0">



    <div class="modal fade account_model" tabindex="-1" role="dialog" 

    	aria-labelledby="gridSystemModalLabel">

    </div>



</section>

<!-- /.content -->



@endsection

@php    
    $layout         = \App\InvoiceLayout::where('business_id',request()->session()->get("business.id"))->where('is_default',1)->first();
    $company_name   = request()->session()->get("user_main.domain");
    $business_color = "#b0906c";
    $header_text    = (!empty($layout))?$layout->header_text:"";
    $img_url        = 'uploads/companies/'.$company_name.'/business_logo/' . Session::get('business.logo');
@endphp

@section('javascript')

<script>

    function print_ledger(){

         

        var printContainer  = $("#account_book");

        element_first       = $("#account_book_wrapper :first-child");

        account_book_info   = $("#account_book_info");

        

        transaction_date_range   = $("#transaction_date_range");

        account_book_paginate    = $("#account_book_paginate");

        if (element_first && account_book_info && account_book_paginate) {

            // element_first.remove();

            account_book_info.remove();

            account_book_paginate.remove();

        }

        

         $("#account_book a").css({"text-decoration":"none","color":"black"});

         

        var printContainer1 = $("#account_balance");

        var printContainer2 = $("#account_name");

        var printContainer3 = $("#opening_balance");

        var printWindow    = window.open("", "_blank");

        printWindow.document.write( '<html><head><title>Print</title></head>');

        printWindow.document.write( '<style>body{font-family:arial} @media print {.no-print{ display:none }} table th{background-color:#929090;color:#000} table tfoot{background-color:#929090;color:#000} table th:nth-child(3),table  td:nth-child(3),table  td:nth-child(5),table  td:nth-child(6),table  th:nth-child(6),table  th:nth-child(5){display:none} table  th:nth-child(2),table  td:nth-child(2){width:12%} table{width:100%;text-align:left;} table td{font-size:10px;padding:5px;}  </style>');

        printWindow.document.write( '<body>' );

        printWindow.document.write( '<table style="width:100%"><td><button class="btn btn-primary no-print" style="background-color:#000 ;color:#fff;padding:10px" onClick="window.print();">@lang("messages.print") &nbsp; </button></td><tr></tr></table>');

        printWindow.document.write( '<table style="width:100%; ; font-weight:bold;"><tr >' );

        printWindow.document.write( '<td style="width:100%; border:0px solid black;text-align:center"><img src="{{ asset( $img_url ) }}"   style="max-width: 400px;height:100px"> <br><br>General Ledger</td>');

        // printWindow.document.write( '<td style="width:30%;font-size:19px; border:0px solid black;text-align:left"></td>' );

        // printWindow.document.write( '<td style="width:30%; border:0px solid black;text-align:left"></td>' );

        // printWindow.document.write( '<td style="width:0%; border:0px solid black;text-align:left"></td>' );

        // printWindow.document.write( '<td style="width:0%; border:0px solid black;text-align:left"></td>' );

        // printWindow.document.write( '<td style="width:0%; border:0px solid black;text-align:left"></td>' );

        printWindow.document.write( '</td></tr></table ><table style="width:100%;border-top:3px solid {{$business_color}};padding-top:10px;"><tr><td style="width:50%;><b> Account name : <span> '+printContainer2.html()+'</span></b><br><b> Balance: '+printContainer1.html()+'</b><br><b>Opening Balance: '+printContainer3.val()+'</b><br><b> Date: '+transaction_date_range.val()+'</b></td><td style="width:50%; border:0px solid black;text-align:right"><span style="font-size:13px">{!!$header_text!!}</span>');

        printWindow.document.write( '</td></tr></table >' );

        printWindow.document.write( '<table  >'+printContainer.html()+'</table>');

        printWindow.document.write( '</body></html>' );

        printWindow.document.close();

  

           

        

    }

    $(document).ready(function(){

        update_account_balance();

        dateRangeSettings.startDate = moment().startOf('year');
        dateRangeSettings.endDate   = moment().endOf('year');
        
        $('#transaction_date_range').daterangepicker(
            dateRangeSettings,
            function (start, end) {
                $('#transaction_date_range').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
                account_book.ajax.reload();
            }
        );

        $('#transaction_date_range').on('cancel.daterangepicker', function(ev, picker) {
            $('#transaction_date_range').val('');
            account_book.ajax.reload();
        });

        $(document).on("change","#costcenter",function(){
            $("#check_box").prop('checked', false); 
            if($("#transaction_type").val() != "" && $(this).val() == "" || $("#transaction_type").val() != "" && $(this).val() == null){
                // alert($("#transaction_type").val() );
                $("#check_box").prop('checked', true); 
            }
        });
        $(document).on("change","#transaction_type",function(){
            value = ($(this).val() == "")?false:true; 
            $("#check_box").prop('checked', value); 
             
        });
        $(document).on("change","#check_box,#costcenter,#transaction_type",function(){
            account_book.ajax.reload();
        });

        

        // Account Book
        account_book = $('#account_book').DataTable({

                        processing: true,

                        serverSide: true,

                        ajax: {

                            url: '{{action("AccountController@show",[$account->id])}}',

                            data: function(d) {

                                var start = '';

                                var end   = '';
                                
                                var balance_added =   $("#balance_added").val();

                                cost_center   = $("#costcenter").val();

                                checkboxs = $("#check_box:checked").val();

                                if($('#transaction_date_range').val()){

                                    start = $('input#transaction_date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');

                                    end   = $('input#transaction_date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');

                                }

                                var transaction_type = $('select#transaction_type').val();

                                d.start_date = start;

                                d.end_date = end;
                                // ... For Previous Balance
                                d.balance_added = balance_added;

                                d.cost_center = cost_center;

                                d.check_box   = checkboxs;

                                d.type = transaction_type;

                                if(checkboxs != null){

                                    $(".header_balance").html("Balance");

                                } 

                                    

                            }

                        },

                        "pageLength": 25, 

                        "ordering": false,

                        "searching": false,

                        columns: [

                            {data: 'ref_no', name: 'transaction_id' , class:'font_number'},

                            {data: 'operation_date', name: 'operation_date'},

                            {data: 'sub_type', name: 'sub_type'},

                            {data: 'note', name: 'note'},

                            {data: 'added_by', name: 'added_by'},

                            {data: 'cost_center', name: 'cost_center'},

                            {data: 'debit', name: 'amount'},

                            {data: 'credit', name: 'amount_'},

                            {
                                data: 'balance', name:'balance' 
                                // render: function (data, type, full, meta) {
                                //     var currentCell = $("#account_book").DataTable().cells({"row":meta.row, "column":meta.col}).nodes(0);
                                //     $(currentCell).html("<i class='fas fa-progress fa-spin fa-fw'></i>");
                                //     $.ajax({    
                                //         url: '/account/getBalance/' + data
                                //     }).done(function (result) {
                                //         $(currentCell).html(result);
                                //     });
                                //     return null;
                                // }
                            },

                        ],

                        fnDrawCallback: function (oSettings) {
                           
                                 tables        = $('#account_book tbody').html();
                                 cost_center   = $("#costcenter").val();
                                 var balance_type_current = 0;
                                $("#account_book tbody tr").each(function(){
                                    console.log($(this).html());
                                    balance_type_current = parseFloat(balance_type_current) + parseFloat($(this).find(".balance_type").html()) ;
                                    balance_current = (balance_type_current<0)?balance_type_current*-1:balance_type_current;
                                    html                 = '<span class="display_currency" data-currency_symbol="true">'+balance_current+'</span>';
                                    $(this).find(".balance_type").parent().html(html) ;
                                });
                                var balance   = $('.header_blc').html();
                                var checkboxs = $("#check_box:checked").val();
                                if(checkboxs != null){
                                    $('#account_book tbody').prepend("<tr class='hide' style='background-color:#eee;color:#000;font-weight:bold'><td></td><td></td><td>Rounded Balance</td><td colspan='5'></td><td id='rounded_balance'>"+ balance+"</td></tr>") ;
                                    
                                } else{
                                    $('#account_book tbody').prepend("<tr class='' style='background-color:#eee;color:#000;font-weight:bold'><td></td><td></td><td>Rounded Balance</td><td colspan='5'></td><td id='rounded_balance'>"+ balance+"</td></tr>") ;
                                }
                                $('.header_balance').html("Balance");
                                
                                // rows = [];
                                // $('#account_book tbody tr').each(function(){
                                //     rows.push($(this));
                                // });
                                // final_rows = rows.reverse();
                                // $('#account_book tbody').html("");
                                // var i;
                                // for (i = 0; i < final_rows.length; ++i) {
                                //     $('#account_book tbody').append(final_rows[i]);
                                     
                                // } 
                                __currency_convert_recursively($('#account_book'));
                        },   

                        "footerCallback": function ( row, data ) {
                                if($('#transaction_date_range').val()){

                                    start = $('input#transaction_date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');

                                    end   = $('input#transaction_date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');

                                }
                                var debit         = 0 ;
                                var credit        = 0;
                                var balance       = 0;
                                var type_amount   = "";
                                var balances      = 0; 
                                var count         = 0; 
                                var minus         = 0;
                                id_row   = "null";
                                date_row = "date";
                                var transaction_type = $('select#transaction_type').val();
                            // ..1....................................................
                                for (var r in data){
                                    if(data[r].type == "debit"){
                                        // *#* debit amount
                                        debit += parseFloat(data[r].amount);
                                        // .. GET FIRST ROW DETAILES FROM DATE AND ID (FROM DATABASE) 
                                        if(r == 0){
                                            id_row   = data[r].id;
                                            date_row = data[r].operation_date
                                        }
                                        
                                    }else{
                                        // *#* credit amount
                                        credit += parseFloat(data[r].amount);
                                        // .. GET FIRST ROW DETAILES FROM DATE AND ID (FROM DATABASE) 
                                        if(r == 0){
                                            id_row   = data[r].id;
                                            date_row = data[r].operation_date
                                        }
                                         
                                    }
                                }
                            // ..2...................................................
                                margin =    debit - credit ;
                                if(date_row == "date"){
                                    date_row = start;
                                }
                            //  .....................................................
                                // initilize footer
                                $('.debit_footer').html("<b style='font-family:arial !important'>" +__currency_trans_from_en(debit)+"</b>");
                                $('.credit_footer').html("<b style='font-family:arial !important'>" +__currency_trans_from_en(credit)+"</b>");
                            // ........................................................
                                //  if( id_row != "null"){
                                    $.ajax({
                                    method: 'GET',
                                    url: "/account/getBalance/"+id_row,
                                    dataType: 'json',
                                    data : {
                                        check_box    : checkboxs, 
                                        cost_center  : cost_center, 
                                        start_date   : date_row,
                                        start_date   : date_row,
                                        account_id   : {{$account->id}},
                                    },
                                    success: function(result) {
                                        if (result.success == true) {
                                            // console.log("id = " + id_row)
                                            // console.log("success = " + result.value)
                                            balances = result.value;
                                            $("#balance_added").attr("value",balances); 
                                            // console.log("success2 = " + $("#balance_added").val())
                                            blc = balances;
                                            // console.log("success3 = " + margin)
                                            // if press on hide previous balance 
                                            if(checkboxs != null){


                                                $('.debit_footer').html("<b style='font-family:arial !important'>" +__currency_trans_from_en(debit)+"</b>");

                                                $('.credit_footer').html("<b style='font-family:arial !important'>" +__currency_trans_from_en(credit)+"</b>");

                                                $(".header_credit").html("Credit");

                                                $(".header_debit").html("Debit");
                                                if(margin < 0){
                                                    type_amount  = "   CR";
                                                }else if(margin == 0){
                                                    type_amount  = " ";
                                                }else{
                                                    type_amount  = "   DR";
                                                }
                                                $('.balance_footer').html( __currency_trans_from_en(Math.abs(margin)) + type_amount   );

                                                $('#opening_balance').val(__currency_trans_from_en(blc));
                                               
                                            } else{
                                                if(balances > 0){
                                                    
                                                    $('.debit_footer').html("<b style='font-family:arial !important'>" +__currency_trans_from_en(debit+parseFloat(Math.abs(blc)))+"</b>");
                                                    
                                                    $('.credit_footer').html("<b style='font-family:arial !important'>" + __currency_trans_from_en(credit)+"</b>" );
                                                    
                                                    $(".header_debit").html("Debit _ " +__currency_trans_from_en(blc));
                                                    $('.header_blc').html( __currency_trans_from_en(blc) + " _ Debit"  );
                                                    $('#rounded_balance').html( __currency_trans_from_en(blc) + " _ Debit"  );
                                                    margin = ( debit + parseFloat(Math.abs(blc)) ) -  credit  ;

                                                    if(margin < 0){
                                                        type_amount  = "   CR";
                                                    }else if(margin == 0){
                                                        type_amount  = " ";
                                                    }else{
                                                        type_amount  = "   DR";
                                                    }
                                                    $('#opening_balance').val(__currency_trans_from_en(blc) + " _ Debit");
                                                    
                                                }else if( balances <  0){
 
                                                     
                                                    $('.debit_footer').html("<b style='font-family:arial !important'>"+__currency_trans_from_en(debit)+"</b>" );

                                                    $('.credit_footer').html("<b style='font-family:arial !important'>"+__currency_trans_from_en(credit+parseFloat(Math.abs(blc)))+"</b>" );

                                                    $(".header_credit").html("Credit _ " + __currency_trans_from_en(blc));
                                                    $('.header_blc').html( __currency_trans_from_en(blc) + " _ Credit"  );
                                                        $('#rounded_balance').html( __currency_trans_from_en(blc) + " _ Credit"  );

                                                    margin = debit - (credit + parseFloat(Math.abs(blc)))
                                                     if(margin < 0){
                                                        type_amount  = "   CR";
                                                    }else if(margin == 0){
                                                        type_amount  = " ";
                                                    }else{
                                                        type_amount  = "   DB";
                                                    }
                                                    $('#opening_balance').val(__currency_trans_from_en(blc) + " _ Credit");
                                                     

                                                }else{
                                                    

                                                    $(".header_credit").html("Credit");

                                                    $(".header_debit").html("Debit");
                                                    $('.header_blc').html( __currency_trans_from_en(0)   );
                                                    $('#rounded_balance').html( __currency_trans_from_en(0)   );
                                                     
                                                    $('.debit_footer').html("<b style='font-family:arial !important'>"+__currency_trans_from_en(debit)+"</b>");

                                                    $('.credit_footer').html("<b style='font-family:arial !important'>"+__currency_trans_from_en(credit)+"</b>");
        
                                                    $('#opening_balance').val(__currency_trans_from_en(blc));
                                
                                                   
                                                     if(margin < 0){
                                                        type_amount  = "   CR";
                                                    }else if(margin == 0){
                                                        type_amount  = " ";
                                                    }else{
                                                        type_amount  = "   DR";
                                                    }
                                                }
                                                var tot = margin  ;
                                                
                                                $('.balance_footer').text( __currency_trans_from_en(Math.abs(tot)) + type_amount   );
                                            }
                                        }  

                                    },
                                })
                                // }else{/
                                //     $('.balance_footer').text(0);
                                // }
                                
                            // ........................................................
                           
                         },

        });

    });

    $(document).on('click', '.delete_account_transaction', function(e){

        e.preventDefault();

        swal({

          title: LANG.sure,

          icon: "warning",

          buttons: true,

          dangerMode: true,

        }).then((willDelete) => {

            if (willDelete) {

                var href = $(this).data('href');

                $.ajax({

                    url: href,

                    dataType: "json",

                    success: function(result){

                        if(result.success === true){

                            toastr.success(result.msg);

                            account_book.ajax.reload();

                            update_account_balance();

                        } else {

                            toastr.error(result.msg);

                        }

                    }

                });

            }

        });

    });



    function update_account_balance(argument) {

        $('span#account_balance').html('<i class="fas fa-sync fa-spin"></i>');

        $.ajax({

            url: '{{action("AccountController@getAccountBalance", [$account->id])}}',

            dataType: "json",

            success: function(data){

           var dat = 0;

                var crd = " ";

                if(data.balance < 0){

                      dat = data.balance*-1 ;

                      crd = "   DR";

                }else{

                      dat = data.balance ;

                      crd = "   CR";

                

                }

                

                if(dat == null){

                  

                    var crd = " ";

                

                }

                $('span#account_balance').text(__currency_trans_from_en(dat, true) + crd);

            }

        });

    }

</script>

@endsection