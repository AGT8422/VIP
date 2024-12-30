@extends('layouts.app')
@section('title', __('lang_v1.payment_accounts'))

@section('css')
    <style>
        .size_column_table{
            width:30%; 
        }
    </style>
@endsection

@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('lang_v1.payment_accounts')
        <small>@lang('account.manage_your_account')</small>
    </h1>
    @php $mainUrl = '/account/account';  @endphp  
    <h5><i><b><a href="{{\URL::to($mainUrl)}}">{{ __("lang_v1.payment_accounts") }} {{  __("izo.>") . " " }}</a></b>{{ __("izo.list_of")   }}  {{__('lang_v1.payment_accounts')}}  </i>  </h5>

</section>
<!-- Main content -->
<section class="content">

      <!-- Page level currency setting -->
	<input type="hidden" id="p_code"     value="{{$currency_details->code}}">
	<input type="hidden" id="p_symbol"   value="{{$currency_details->symbol}}">
	<input type="hidden" id="p_thousand" value="{{$currency_details->thousand_separator}}">
	<input type="hidden" id="p_decimal"  value="{{$currency_details->decimal_separator}}">
    
    {{-- @if(!empty($not_linked_payments))
        <div class="row">
            <div class="col-sm-12">
                <div class="alert alert-danger">
                    <ul>
                        @if(!empty($not_linked_payments))
                            <li>{!! __('account.payments_not_linked_with_account', ['payments' => $not_linked_payments]) !!} <a href="{{action('AccountReportsController@paymentAccountReport')}}">@lang('account.view_details')</a></li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    @endif --}}
 
    <div class="row">
        <div class="col-sm-12">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li class="active">
                        <a href="#other_accounts" data-toggle="tab">
                            <i class="fa fa-book"></i> <strong>@lang('account.accounts')</strong>
                        </a>
                    </li>
                    <li>
                        <a href="#account_types" data-toggle="tab">
                            <i class="fa fa-list"></i> <strong>
                            @lang('lang_v1.account_types') </strong>
                        </a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="other_accounts">
                        <div class="row">
                            <div class="col-md-12">
                                @component('components.filters', ['title' => __('report.filters') , 'class' => 'box-primary'])
                                    <div class="col-md-3"> 
                                        <div class="form-group">
                                            {!! Form::label('account_name', __('lang_v1.name') . ':') !!}
                                            {!! Form::select('account_name', $account_ , null, ['id' => "account_name",'class' => 'form-control account_name-select2 select2', 'style' => 'width:100%',   'placeholder' => __('lang_v1.all')]); !!}
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            {!! Form::label('account_number', __('lang_v1.account_number') . ':') !!}
                                            {!! Form::select('account_number',$account_number, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'account_number', 'placeholder' => __('lang_v1.all')]); !!}
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            {!! Form::label('main_account', __('lang_v1.account_main') . ':') !!}
                                            {!! Form::select('main_account', $array_of_main_, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'main_account', 'placeholder' => __('lang_v1.all')]); !!}
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            {!! Form::label('account_type', __('lang_v1.account_type') . ':') !!}
                                            {!! Form::select('account_type', $array_of_type_, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'account_type', 'placeholder' => __('lang_v1.all')]); !!}
                                        </div>
                                    </div>
                                    <div class="col-md-3 hide">
                                        <div class="form-group">
                                            {!! Form::label('account_sub_type', __('lang_v1.account_sub_type') . ':') !!}
                                            {!! Form::select('account_sub_type',$array_of_type_sub, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'account_sub_type', 'placeholder' => __('lang_v1.all')]); !!}
                                        </div>
                                    </div>
                                @endcomponent

                                @component('components.widget' ,[ 'class' => 'box-primary'])
                                    <div class="col-md-4">
                                        {!! Form::label('transaction_date_range', __('Active') . ':') !!}
                                        {!! Form::select('account_status', ['active' => __('business.is_active'), 'closed' => __('account.closed')], null, ['class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'account_status']); !!}
                                    </div>
                                    <div class="col-md-4 hide">
                                        <div class="form-group">
                                            {!! Form::label('transaction_date_range', __('report.date_range') . ':') !!}
                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                                {!! Form::text('transaction_date_range', null, ['class' => 'form-control', 'readonly', 'placeholder' => __('report.date_range')]) !!}
                                            </div>
                                        </div>
                                    </div>  
                                    <div class="col-md-4">  &nbsp; </div>
                                    <div class="col-md-4">
                                        <button type="button" class="btn btn-primary btn-modal pull-right" 
                                            data-container=".account_model"
                                            data-href="{{action('AccountController@create')}}">
                                            <i class="fa fa-plus"></i> @lang( 'messages.add' )</button>
                                    </div>
                                    {{-- <div class="col-md-4">
                                        <button type="button" class="btn btn-primary pull-right repair_balance" 
                                            data-href="{{\URL::to('/account/repair-balances')}}">
                                            <i class="fa fa-gear"></i> @lang( 'Repair Balance' )</button>
                                    </div> --}}
                                @endcomponent
                            </div>
                            <div class="col-sm-12">
                            <br>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped" id="other_account_table">
                                        <thead>
                                            <tr>
                                                <th>@lang( 'lang_v1.name' )</th>
                                                <th>@lang( 'lang_v1.account_main' )</th>
                                                <th>@lang( 'lang_v1.account_type' )</th>
                                                <th>@lang( 'lang_v1.account_sub_type' )</th>
                                                <th>@lang('account.account_number')</th>
                                                <th>@lang( 'brand.note' )</th>
                                                <th>@lang('Current Balance')</th>
                                                <th class="hide">@lang( 'Filtered Balance' )</th>
                                                <th class="hide">@lang('lang_v1.balance')</th>
                                                <th>@lang('lang_v1.type')</th>
                                                <th>@lang('lang_v1.added_by')</th>
                                                <th>@lang( 'messages.action' )</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{--
                    <div class="tab-pane" id="capital_accounts">
                        <table class="table table-bordered table-striped" id="capital_account_table" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>@lang( 'lang_v1.name' )</th>
                                    <th>@lang('account.account_number')</th>
                                    <th>@lang( 'brand.note' )</th>
                                    <th>@lang('lang_v1.balance')</th>
                                    <th>@lang( 'messages.action' )</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                    --}}
                    <div class="tab-pane" id="account_types">
                        <div class="row">
                            <div class="col-md-12">
                                <button type="button" class="btn btn-primary btn-modal pull-right" 
                                    data-href="{{action('AccountTypeController@create')}}"
                                    data-container="#account_type_modal">
                                    <i class="fa fa-plus"></i> @lang( 'messages.add' )</button>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-12">
                                <table class="table table-striped table-bordered" id="account_types_table" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>@lang( 'lang_v1.code' )</th>
                                            <th>@lang( 'lang_v1.name' )</th>
                                            <th>@lang( 'messages.action' )</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($array_ids as $key => $i)
                                           
                                            <tr class="account_type_{{$i->id}}">
                                                <td>{{$i->code}}</td>
                                                @if($i->parent_account_type_id == null)
                                                    <th>{{$i->name}}</th>
                                                @else 
                                                    @php
                                                        $check =  $i->parent_account_type_id ;
                                                        $s     = "*";
                                                    @endphp 
                                                    @while($check != null)
                                                        @php
                                                            $account = \App\AccountType::find($check);
                                                            $s     .= "*";
                                                            $check   = $account->parent_account_type_id;
                                                        @endphp 
                                                    @endwhile

                                                    <td>{{ $s . "   " . $i->name}}</td>
                                                @endif
                                                <td>
                                                    {!! Form::open(['url' => action('AccountTypeController@destroy', $i->id), 'method' => 'delete' ]) !!}
                                                    <button type="button" class="btn btn-primary btn-modal btn-xs" 
                                                    data-href="{{action('AccountTypeController@edit', $i->id)}}"
                                                    data-container="#account_type_modal">
                                                    <i class="fa fa-edit"></i> @lang( 'messages.edit' )</button>
                                                    @if(request()->session()->get("user.id") == 1)
                                                        <button type="button" class="btn btn-danger btn-xs delete_account_type" >
                                                        <i class="fa fa-trash"></i> @lang( 'messages.delete' )</button>
                                                    @endif
                                                    {!! Form::close() !!}
                                                </td>
                                            </tr>
                                        @endforeach
                                        {{-- @foreach($account_types as $account_type)
                                            <tr class="account_type_{{$account_type->id}}">

                                                <td>{{$account_type->code}}</td>
                                                <th>{{$account_type->name}}</th>
                                                <td>
                                                    
                                                    {!! Form::open(['url' => action('AccountTypeController@destroy', $account_type->id), 'method' => 'delete' ]) !!}
                                                    <button type="button" class="btn btn-primary btn-modal btn-xs" 
                                                    data-href="{{action('AccountTypeController@edit', $account_type->id)}}"
                                                    data-container="#account_type_modal">
                                                    <i class="fa fa-edit"></i> @lang( 'messages.edit' )</button>
                                                    <!--@if(request()->session()->get("user.id") == 1)-->
                                                    <!--    <button type="button" class="btn btn-danger btn-xs delete_account_type" >-->
                                                    <!--    <i class="fa fa-trash"></i> @lang( 'messages.delete' )</button>-->
                                                    <!--@endif-->
                                                    {!! Form::close() !!}
                                                </td>
                                            </tr>
                                           
                                            @php
                                                $allInMain = $account_type->allInParent($account_type->id) ;
                                            @endphp
                                            @foreach($allInMain  as $key => $sub_id)
                                               
                                                <tr>
                                                    <td>{{$account_type->findAccount($sub_id)->code}}</td>
                                                    <td>&nbsp;&nbsp;- {{$account_type->findAccount($sub_id)->name}}</td>
                                                    <td>
                                                        

                                                        {!! Form::open(['url' => action('AccountTypeController@destroy', $sub_id), 'method' => 'delete' ]) !!}
                                                        <button type="button" class="btn btn-primary btn-modal btn-xs" 
                                                        data-href="{{action('AccountTypeController@edit', $sub_id)}}"
                                                        data-container="#account_type_modal">
                                                        <i class="fa fa-edit"></i> @lang( 'messages.edit' )</button>
                                                            <!--<button type="button" class="btn btn-danger btn-xs delete_account_type" >-->
                                                            <!--<i class="fa fa-trash"></i> @lang( 'messages.delete' )</button>-->
                                                            {!! Form::close() !!}
                                                    </td>
                                                </tr>
                                                @php
                                                    $allInSub = $account_type->allInSubParent($sub_id) ;
                                                     
                                                @endphp
                                               
                                                @foreach($allInSub  as $key => $sub_p_id)
                                                            
                                                   
                                                        <tr>
                                                            <td> {{ $account_type->findAccount($sub_p_id)->code }}</td>
                                                            <td>&nbsp;&nbsp;-- {{  $account_type->findAccount($sub_p_id)->name }}</td>
                                                            <td>
                                                                

                                                                {!! Form::open(['url' => action('AccountTypeController@destroy', $sub_p_id), 'method' => 'delete' ]) !!}
                                                                    <button type="button" class="btn btn-primary btn-modal btn-xs" 
                                                                data-href="{{action('AccountTypeController@edit', $sub_p_id)}}"
                                                                data-container="#account_type_modal">
                                                                <i class="fa fa-edit"></i> @lang( 'messages.edit' )</button>
                                                                <!--@if(request()->session()->get("user.id") == 1)-->
                                                                <!--    <button type="button" class="btn btn-danger btn-xs delete_account_type" >-->
                                                                <!--    <i class="fa fa-trash"></i> @lang( 'messages.delete' )</button>-->
                                                                <!--@endif-->
                                                                {!! Form::close() !!}
                                                            </td>
                                                        </tr>
                                                   
                                                @endforeach
                                                
                                            @endforeach
                                        @endforeach --}}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    
    <div class="modal fade account_model" tabindex="-1" role="dialog" 
    	aria-labelledby="gridSystemModalLabel">
    </div>

    <div class="modal fade" tabindex="-1" role="dialog" 
        aria-labelledby="gridSystemModalLabel" id="account_type_modal">
    </div>
</section>
<!-- /.content -->

@endsection

@section('javascript')
 
<script type="text/javascript">
    $(document).ready(function(){
      
        
    $(".select2").select2();
        $(document).on('click', 'button.close_account', function(){
            swal({
                title: LANG.sure,
                icon: "warning",
                buttons: true,
                dangerMode: true,
            }).then((willDelete)=>{
                if(willDelete){
                     var url = $(this).data('url');

                     $.ajax({
                         method: "get",
                         url: url,
                         dataType: "json",
                         success: function(result){
                             if(result.success == true){
                                toastr.success(result.msg);
                                capital_account_table.ajax.reload();
                                other_account_table.ajax.reload();
                             }else{
                                toastr.error(result.msg);
                            }

                        }
                    });
                }
            });
        });
        $(document).on('submit', 'form#edit_payment_account_form', function(e){
            e.preventDefault();
            var data = $(this).serialize();
            $.ajax({
                method: "POST",
                url: $(this).attr("action"),
                dataType: "json",
                data: data,
                success:function(result){
                    if(result.success == true){
                        $('div.account_model').modal('hide');
                        toastr.success(result.msg);
                        capital_account_table.ajax.reload();
                        other_account_table.ajax.reload();
                    }else{
                        toastr.error(result.msg);
                    }
                }
            });
        });
        $(document).on('submit', 'form#payment_account_form', function(e){
            e.preventDefault();
            var data = $(this).serialize();
            $.ajax({
                method: "post",
                url: $(this).attr("action"),
                dataType: "json",
                data: data,
                success:function(result){
                    if(result.success == true){
                        $('div.account_model').modal('hide');
                        toastr.success(result.msg);
                        capital_account_table.ajax.reload();
                        other_account_table.ajax.reload();
                    }else{
                        toastr.error(result.msg);
                    }
                }
            });
        });
        $('#transaction_date_range').on('cancel.daterangepicker', function(ev, picker) {
            $('#transaction_date_range').val('');
            other_account_table.ajax.reload();
        });
        $('#transaction_date_range').daterangepicker(
            dateRangeSettings,
            function (start, end) {
                $('#transaction_date_range').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
                other_account_table.ajax.reload();
            }
        );
        // capital_account_table
        capital_account_table = $('#capital_account_table').DataTable({
                        processing: true,
                        serverSide: true,
                        ajax: '/account/account?account_type=capital',
                        columnDefs:[{
                                "targets": 5,
                                "orderable": false,
                                "searchable": false
                            }],
                        columns: [
                            {data: 'name', name: 'name'},
                            {data: 'account_number', name: 'account_number'},
                            {data: 'note', name: 'note'},
                            {data: 'balance', name: 'balance', searchable: false},
                            {data: 'action', name: 'action'}
                        ],
                        "fnDrawCallback": function (oSettings) {
                            __currency_convert_recursively($('#capital_account_table'));
                        }
                    });
        // capital_account_table
        other_account_table = $('#other_account_table').DataTable({
                        processing: true,
                        serverSide: true,
                        ajax: {
                            url: '/account/account?account_type=other',
                            data: function(d){
                                var start          = '';
                                var end            = '';
                                d.account_status   = $('#account_status').val();
                                d.account_name     = $('#account_name').val();
                                d.account_sub_type = $('#account_sub_type').val();
                                d.account_type     = $('#account_type').val();
                                d.account_number   = $('#account_number').val();
                                d.main_account     = $('#main_account').val();
                                if($('#transaction_date_range').val()){
                                    start = $('input#transaction_date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
                                    end   = $('input#transaction_date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');
                                }
                                d.start_date = start;
                                d.end_date   = end;

                            }
                        },
                        columnDefs:[{
                                "targets": 7,
                                "orderable": false,
                                "searchable": false
                            }],
                        columns: [
                            {data: 'name', name: 'accounts.name' ,class:"size_column_table"},
                            {data: 'parent_account_type_name', name: 'pat.name'},
                            {data: 'sub_parent_name', name: 'pat_sub.name'},
                            {data: 'account_type_name', name: 'ats.name'},
                            {data: 'account_number', name: 'accounts.account_number'},
                            {data: 'note', name: 'accounts.note'},
                            {data: 'balance_final', name: 'balance_final', searchable: false},
                            {data: 'balance_with_date', name: 'balance_with_date', searchable: false, class:'hide'},
                            {data: 'balance', name: 'balance', searchable: false , class:'hide'},
                            {data: 'type', name: 'type', searchable: false},
                            {data: 'added_by', name: 'u.first_name'},
                            {data: 'action', name: 'action'}
                        ],
                        "fnDrawCallback": function (oSettings) {
                            //  var lines           = new Array();
                            //  var ob              = new Array();
                            //  var older           = new Array();
                            
                            //  $('.balance_rows').each(function(){
                            //     var item = $(this); 
                            //     var id   = item.attr("data-id");
                            //     lines.push(id);
                            //     ob.push(item);
                            // });
                            
                            // getblc(lines,0); 
                            // function getblc(lines,index){ 
                            //         // alert(index in lines);
                            //         // alert(lines.length);
                            //         // alert(lines[index]);
                            //         if (index < lines.length){
                            //             if(!older.includes(index)){
                            //                 older.push(index);
                            //                 if (index in lines){
                            //                     $.ajax({
                            //                         method: "get",
                            //                         url: '/account/get-balance/'+lines[index]+'?in_row=1',
                            //                         dataType: "json",
                            //                         success: function(result){
                            //                             if(result.value == true){ 
                            //                                 // toastr.success(result.balance);
                            //                                 type = (result.balance<0)?"Debit":((result.balance>0)?"Credit":"");
                            //                                 blc  = (result.balance<0)?result.balance*-1:((result.balance>0)?result.balance:0);
                            //                                 ob[index].parent().parent().find(".tType").html(type);
                            //                                 ob[index].html('<span class="display_currency balance_rows" data-id="'+lines[index]+'"  data-currency_symbol="true">'+__currency_trans_from_en(parseFloat(blc))+'</span> ');
                            //                                 index = parseFloat(index)+1; 
                            //                                 getblc(lines,index);
                            //                             }else{ 
                            //                                 index = parseFloat(index)+1; 
                            //                                 getblc(lines,index);
                            //                                 ob[index].html("wait ...");
                            //                                 // toastr.error(result.msg);
                            //                             }
                                                        
                            //                         }
                            //                     });
                            //                 }
                            //             }
                            //         }
                            // }
                             
                            __currency_convert_recursively($('#other_account_table'));
                        }
                    });

    });
    $(document).on('change','#account_status,#account_number,#account_name,#account_sub_type',function(){
        other_account_table.ajax.reload();
    });
    $(document).on('change','#main_account', function(e) {
        var el = $(this).val();
        $.ajax({
            method: 'GET',
            url: "/account/get-account-type/"+el,
            success: function(result) {
                if (result.success == true) {
                    $('#account_type').html("");
                    
                    html = '<option selected="selected" value >All</option>';
                    for( var r in result.array){
                        html += "<option value='"+r+"' >"+result.array[r]+"</option>";
                    }
                    $('#account_type').append(html);
                    toastr.success(result.msg);
                } else {
                    toastr.error(result.msg);
                }
            },
        });
        other_account_table.ajax.reload();
        
    });
    $(document).on('change','#account_type', function(e) {
        var el = $(this).val();
        $.ajax({
            method: 'GET',
            url: "/account/get-sub-account-type/"+el,
            success: function(result) {
                if (result.success == true) {
                    $('#account_sub_type').html("");
                    
                    html = '<option selected="selected" value >All</option>';
                    for( var r in result.array){
                        html += "<option value='"+r+"' >"+result.array[r]+"</option>";
                    }
                    $('#account_sub_type').append(html);
                    toastr.success(result.msg);
                } else {
                    toastr.error(result.msg);
                }
            },
        });
        other_account_table.ajax.reload();
    });
    $(document).on('submit','form#deposit_form', function(e){
        e.preventDefault();
        var data = $(this).serialize();

        $.ajax({
          method: "POST",
          url: $(this).attr("action"),
          dataType: "json",
          data: data,
          success: function(result){
            if(result.success == true){
              $('div.view_modal').modal('hide');
              toastr.success(result.msg);
              capital_account_table.ajax.reload();
              other_account_table.ajax.reload();
            } else {
              toastr.error(result.msg);
            }
          }
        });
    });
    $('.account_model').on('shown.bs.modal', function(e) {
        $('.account_model .select2').select2({ dropdownParent: $(this) })
    });
    $(document).on('click','button.delete_account_type', function(){
        swal({
            title: LANG.sure,
            icon: "warning",
            buttons: true,
            dangerMode: true,
        }).then((willDelete)=>{
            if(willDelete){
                $(this).closest('form').submit();
            }
        });
    })
    $(document).on('click','button.activate_account', function(){
        swal({
            title: LANG.sure,
            icon: "warning",
            buttons: true,
            dangerMode: true,
        }).then((willActivate)=>{
            if(willActivate){
                 var url = $(this).data('url');
                 $.ajax({
                     method: "get",
                     url: url,
                     dataType: "json",
                     success: function(result){
                         if(result.success == true){
                            toastr.success(result.msg);
                            capital_account_table.ajax.reload();
                            other_account_table.ajax.reload();
                         }else{
                            toastr.error(result.msg);
                        }

                    }
                });
            }
        });
    });
    $(document).on('click','button.one_account_balance', function(){
        var id = $(this).attr('data-id');
            $.ajax({
                method: "get",
                url: '/account/get-balance/'+id,
                dataType: "json",
                success: function(result){
                    if(result.value == true){
                        toastr.success(result.balance);
                    }else{
                        toastr.error(result.msg);
                    }

                }
            });
    });
    $(document).on('click','button.repair_balance', function(){
        var url = $(this).attr('data-href');
        $.ajax({
            method: "get",
            url: url,
            dataType: "json",
            success: function(result){
                if(result.success == 1){
                    toastr.success(result.value);
                }else{
                    toastr.error(result.value);
                }
            }
        });
    });

    
</script>
@endsection