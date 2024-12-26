@extends('layouts.app')
@section('title', __( 'sale.approved_quatation'))
@section('content')

<!-- Content Header (Page header) -->
<section class="content-header no-print">
    <h1>@lang('sale.approved_quatation')
    </h1>
</section>

<!-- Main content -->
<section class="content no-print">
    <!-- Page level currency setting -->
	<input type="hidden" id="p_code" value="{{$currency_details->code}}">
	<input type="hidden" id="p_symbol" value="{{$currency_details->symbol}}">
	<input type="hidden" id="p_thousand" value="{{$currency_details->thousand_separator}}">
	<input type="hidden" id="p_decimal" value="{{$currency_details->decimal_separator}}">

    @component('components.filters', ['title' => __('report.filters'),'class' => 'box-primary'])
        <div class="col-md-3 hide">
            <div class="form-group">
                {!! Form::label('sell_list_filter_location_id',  __('purchase.business_location') . ':') !!}

                {!! Form::select('sell_list_filter_location_id', $business_locations, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all') ]); !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('sell_list_filter_customer_id',  __('contact.customer') . ':') !!}
                {!! Form::select('sell_list_filter_customer_id', $customers, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('sell_list_filter_converted',  __('home.convert') . ':') !!}
                {!! Form::select('sell_list_filter_converted', ["1" => __('home.convert'),"2"=>__("home.noteConverted")], null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('project_no', __('sale.project_no') . ':') !!}
                {!! Form::select('project_no', $project_numbers, null, ['class' => 'form-control select2' ,'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
            </div>
        </div>
       <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('agent_id', __('home.Agent') . ':') !!}
                {!! Form::select('agent_id', $users, null, ['class' => 'form-control select2' ,'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
            </div>
        </div>
        {{-- approve quotation --}}
       <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('previous_no', __('sale.approve_no') . ':') !!}
                {!! Form::select('previous_no', $previouses, null, ['id' => 'previous_no','class' => 'form-control select2' ,'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
            </div>
        </div>
        {{-- quotation --}}
       <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('quotation_no', __('sale.quotation_no') . ':') !!}
                {!! Form::select('quotation_no', $first_nos, null, ['id' => 'quotation_no','class' => 'form-control select2' ,'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
            </div>
        </div>
        {{-- draft --}}
       <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('draft_no', __('sale.draft_no') . ':') !!}
                {!! Form::select('draft_no', $refe_nos, null, ['id' => 'draft_no','class' => 'form-control select2' ,'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('cost_center_id', __('home.Cost Center') . ':') !!}
                {!! Form::select('cost_center_id', $cost_centers, null, ['class' => 'form-control select2' ,'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('sell_list_filter_date_range', __('report.date_range') . ':') !!}
                {!! Form::text('sell_list_filter_date_range', null, ['placeholder' => __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'readonly']); !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('created_by',  __('report.user') . ':') !!}
                {!! Form::select('created_by', $sales_representative, null, ['class' => 'form-control select2', 'style' => 'width:100%']); !!}
            </div>
        </div>
         
    @endcomponent
    @component('components.widget', ['class' => 'box-primary'])
    @if(auth()->user()->hasRole('Admin#' . session('business.id')) || auth()->user()->can("SalesMan.views") || auth()->user()->can("admin_supervisor.views")|| auth()->user()->can("admin_without.views"))
        @slot('tool')
            <div class="box-tools">
                <a class="btn btn-block btn-primary" href="{{action('SellController@create', ['status' => 'ApprovedQuotation'])}}">
                <i class="fa fa-plus"></i> @lang('lang_v1.add_approve')</a>
            </div>
        @endslot
    @endcan
        <div class="table-responsive">
            <table class="table table-bordered table-striped ajax_view" id="sell_table">
                <thead>
                    <tr>
                        <th>@lang('messages.action')</th>
                        <th>@lang('messages.date')</th>
                        <th>@lang('purchase.ref_no')</th>
                        <th>@lang('sale.project_no')</th>
                        <th class="hide">@lang('home.Agent')</th>
                        <th class="hide">@lang('home.Cost Center')</th>
                        <th>@lang('Grand Total')</th>
                        <th>@lang('TAX')</th>
                        <th>@lang('Total Paid')</th>
                        <th>@lang('Total Remaining')</th>
                        <th>@lang('sale.approve_no')</th>
                        <th>@lang('sale.quotation_no')</th>
                        <th>@lang('sale.draft_no')</th>
                        <th>@lang('sale.customer_name')</th>
                        <th>@lang('lang_v1.contact_no')</th>
                        <th class="hide">@lang('sale.location')</th>
                        <th class="hide">@lang('warehouse.nameW')</th>
                        <th>@lang('sale.payment_status')</th>
                        <th>@lang('sale.deliver_status')</th>
                        <th>@lang('lang_v1.total_items')</th>
                        <th>@lang('lang_v1.added_by')</th>
                        <th>@lang('home.convert')</th>
                        <th>@lang('home.date_convert')</th>
                    </tr>
                </thead>
            </table>
        </div>
    @endcomponent
</section>
<div class="modal fade product_modal" tabindex="-1" role="dialog" 
aria-labelledby="gridSystemModalLabel">
</div>

<div class="modal fade payment_modal" tabindex="-1" role="dialog" 
aria-labelledby="gridSystemModalLabel">
</div>

<div class="modal fade edit_payment_modal" tabindex="-1" role="dialog" 
aria-labelledby="gridSystemModalLabel">
</div>
<!-- /.content -->
@stop
@section('javascript')
<script type="text/javascript">
    $(document).ready( function(){
        $('#sell_list_filter_date_range').daterangepicker(
            dateRangeSettings,
            function (start, end) {
                $('#sell_list_filter_date_range').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
                sell_table.ajax.reload();
            }
        );
        $('#sell_list_filter_date_range').on('cancel.daterangepicker', function(ev, picker) {
            $('#sell_list_filter_date_range').val('');
            sell_table.ajax.reload();
        });
        sell_table = $('#sell_table').DataTable({
            processing: true,
            serverSide: true,
            aaSorting: [[1, 'desc']],
            "ajax": {
                "url": '/sells/draft-dt1?is_quotation=0',
                "data": function ( d ) {
                    if($('#sell_list_filter_date_range').val()) {
                        var start = $('#sell_list_filter_date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
                        var end = $('#sell_list_filter_date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');
                        d.start_date = start;
                        d.end_date = end;
                    }

                    if($('#sell_list_filter_location_id').length) {
                        d.location_id = $('#sell_list_filter_location_id').val();
                    }
                    if($('#cost_center_id').length) {
                        d.cost_center_id = $('#cost_center_id').val();
                    }
                    if($('#agent_id').length) {
                        d.agent_id = $('#agent_id').val();
                    }
                    if($('#previous_no').length) {
                        d.previous_no = $('#previous_no').val();
                    }
                    if($('#draft_no').length) {
                        d.draft_no = $('#draft_no').val();
                    }
                    if($('#quotation_no').length) {
                        d.quotation_no = $('#quotation_no').val();
                    }
                    if($('#project_no').length) {
                        d.project_no = $('#project_no').val();
                    }
                    d.customer_id = $('#sell_list_filter_customer_id').val();
                    d.converted = $('#sell_list_filter_converted').val();
                    
                    if($('#created_by').length) {
                        d.created_by = $('#created_by').val();
                    }
                }
            },
            columnDefs: [ {
                "targets": 7,
                "orderable": false,
                "searchable": false
            } ],
            columns: [
                { data: 'action', name: 'action'},
                { data: 'transaction_date', name: 'transaction_date'  },
                { data: 'invoice_no', name: 'invoice_no'},
                { data: 'project_no', name: 'project_no'},
                { data: 'agents', name: 'agents' , class:"hide"},
                { data: 'cost_center_id', name: 'cost_center_id' ,class:"hide"},
                { data: 'final_total', name: 'final_total'},
                { data: 'tax_amount', name: 'tax_amount'},
                { data: 'total_paid', name: 'total_paid'},
                { data: 'total_remaining', name: 'total_remaining'},
                { data: 'previous', name: 'previous'},
                { data: 'first_ref_no', name: 'first_ref_no'},
                { data: 'refe_no', name: 'refe_no'},
                { data: 'conatct_name', name: 'conatct_name'},
                { data: 'mobile', name: 'contacts.mobile'},
                { data: 'business_location', name: 'bl.name',class:"hide"},
                { data: 'store', name: 'store' ,class:"hide"},
                { data: 'payment_status', name: 'payment_status'},
                { data: 'deliver_status', name: 'deliver_status', "searchable": false},
                { data: 'total_items', name: 'total_items', "searchable": false},
                { data: 'added_by', name: 'added_by'},
                { data: 'converted', name: 'converted'},
                { data: 'converted_date', name: 'converted_date'},
            ],
            "fnDrawCallback": function (oSettings) {
                __currency_convert_recursively($('#purchase_table'));
            }
        });
        $(document).on('change', '#sell_list_filter_converted,#quotation_no,#draft_no,#previous_no,#sell_list_filter_location_id,#project_no,#cost_center_id,#agent_id, #sell_list_filter_customer_id, #created_by',  function() {
            sell_table.ajax.reload();
        });

        $(document).on('click', 'a.convert-to-proforma', function(e){
            e.preventDefault();
            swal({
                title: LANG.sure,
                icon: 'warning',
                buttons: true,
                dangerMode: true,
            }).then(confirm => {
                if (confirm) {
                    var url = $(this).attr('href');
                    $.ajax({
                        method: 'GET',
                        url: url,
                        dataType: 'json',
                        success: function(result) {
                            if (result.success == true) {
                                toastr.success(result.msg);
                                sell_table.ajax.reload();
                            } else {
                                toastr.error(result.msg);
                            }
                            sell_table.ajax.reload();
                        },
                    });
                }
            });
        });
        // $('#project_no').select2({
        //     placeholder: '',
        //     ajax: {
        //         url: '/sell/project_no',
        //         dataType: 'json',
        //         delay: 250,
        //         processResults: function (data) {
        //             return {
        //                 results: $.map(data, function (item) {
        //                     return {
        //                         text: item.project_no,
        //                         id: item.project_no,
        //                     }
        //                 })
        //             };
        //         },
        //         cache: true
        //     }
        // }); 

    });

    $(document).on('click', '.delete-sale-Q', function(e) {
            
            swal({
                title: LANG.sure,
                text: LANG.confirm_delete_payment,
                icon: 'warning',
                buttons: true,
                dangerMode: true,
            }).then(willDelete => {
                if (willDelete) {
                    $.ajax({
                        url: $(this).data('href'),
                        method: 'get',
                        dataType: 'json',
                        success: function(result) {
                            if (result.success === true) {
                                toastr.success(result.msg);
                                sell_table.ajax.reload();
                            } else {
                                toastr.error(result.msg);
                            }
                        },
                    });
                }
            });
    });
    $(document).on('click', '.convert-to-invoice', function(e) {
            
            swal({
                title: LANG.sure,
                text: LANG.confirm_delete_payment,
                icon: 'warning',
                buttons: true,
                dangerMode: true,
            }).then(willDelete => {
                if (willDelete) {
                    $.ajax({
                        url: $(this).data('href'),
                        method: 'get',
                        dataType: 'json',
                        success: function(result) {
                            if (result.success == 1) {
                                toastr.success(result.msg);
                                sell_table.ajax.reload();
                            } else {
                                toastr.error(result.msg);
                            }
                        },
                    });
                }
            });
    });
</script>
<script src="{{ asset('js/payments.js?v=' . $asset_v) }}"></script>

@endsection