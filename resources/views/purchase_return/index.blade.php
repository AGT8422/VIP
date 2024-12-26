@extends('layouts.app')
@section('title', __('lang_v1.purchase_return'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header no-print">
    <h1>@lang('lang_v1.purchase_return')
    </h1>
</section>

   <!-- Page level currency setting -->
   <input type="hidden" id="p_code" value="{{$currency_details->code}}">
   <input type="hidden" id="p_symbol" value="{{$currency_details->symbol}}">
   <input type="hidden" id="p_thousand" value="{{$currency_details->thousand_separator}}">
   <input type="hidden" id="p_decimal" value="{{$currency_details->decimal_separator}}">

<!-- Main content -->
<section class="content no-print">
    @component('components.filters', ['title' => __('report.filters')])
        <div class="col-md-3 hide">
            <div class="form-group">
                {!! Form::label('purchase_list_filter_location_id',  __('purchase.business_location') . ':') !!}
                {!! Form::select('purchase_list_filter_location_id', $business_locations, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('purchase_list_filter_date_range', __('report.date_range') . ':') !!}
                {!! Form::text('purchase_list_filter_date_range', null, ['placeholder' => __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'readonly']); !!}
            </div>
        </div>
    @endcomponent
    @component('components.widget', ['class' => 'box-primary', 'title' => __('lang_v1.all_purchase_returns')])
        @include('purchase.partials.update_purchase_status_modal',["type"=>"return_sale"])

        @can('purchase.update')
            @slot('tool')
                 <div class="box-tools"> 
                     <a class="btn btn-block btn-primary" href="{{action('CombinedPurchaseReturnController@create')}}"> 
                  <i class="fa fa-plus"></i> @lang('messages.add')</a> 
                 </div> 
            @endslot
        @endcan
        @can('purchase.view')
            @include('purchase_return.partials.purchase_return_list')
        @elsecan('warehouse.views')
            @include('purchase_return.partials.purchase_return_list')
        @elsecan('admin_supervisor.views')
            @include('purchase_return.partials.purchase_return_list')
        @elsecan('SalesMan.views')
            @include('purchase_return.partials.purchase_return_list')
        @elsecan('manufuctoring.views')
            @include('purchase_return.partials.purchase_return_list')
        @endcan
    @endcomponent

    <div class="modal fade payment_modal" tabindex="-1" role="dialog" 
        aria-labelledby="gridSystemModalLabel">
    </div>

    <div class="modal fade edit_payment_modal" tabindex="-1" role="dialog" 
        aria-labelledby="gridSystemModalLabel">
    </div>

</section>

<!-- /.content -->
@stop
@section('javascript')
<script src="{{ asset('js/payments.js?v=' . $asset_v) }}"></script>
<script>
    $(document).ready( function(){
        $('#purchase_list_filter_date_range').daterangepicker(
            dateRangeSettings,
            function (start, end) {
                $('#purchase_list_filter_date_range').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
               purchase_return_table.ajax.reload();
            }
        );
        $('#purchase_list_filter_date_range').on('cancel.daterangepicker', function(ev, picker) {
            $('#purchase_list_filter_date_range').val('');
            purchase_return_table.ajax.reload();
        });
        $(document).on('click', '.update_status', function(e){
            e.preventDefault();
           
            $('#update_purchase_status_form').find('#status').val($(this).data('status'));
            $('#update_purchase_status_form').find('#purchase_id').val($(this).data('purchase_id'));
            $('#update_purchase_status_modal').modal('show');
        });
        //Purchase table
        purchase_return_table = $('#purchase_return_datatable').DataTable({
            processing: true,
            serverSide: true,
            aaSorting: [[0, 'desc']],
            ajax: {
            url: '/purchase-return',
            data: function(d) {
                if ($('#purchase_list_filter_location_id').length) {
                    d.location_id = $('#purchase_list_filter_location_id').val();
                }

                var start = '';
                var end = '';
                if ($('#purchase_list_filter_date_range').val()) {
                    start = $('input#purchase_list_filter_date_range')
                        .data('daterangepicker')
                        .startDate.format('YYYY-MM-DD');
                    end = $('input#purchase_list_filter_date_range')
                        .data('daterangepicker')
                        .endDate.format('YYYY-MM-DD');
                }
                d.start_date = start;
                d.end_date = end;
            },
        },
            columnDefs: [ {
                "targets": [7, 8],
                "orderable": false,
                "searchable": false
            } ],
            columns: [
                { data: 'transaction_date', name: 'transaction_date'  },
                { data: 'ref_no', name: 'ref_no'},
                { data: 'parent_purchase', name: 'T.ref_no'},
                { data: 'location_name', name: 'BS.name' , class:"hide"},
                { data: 'status', name: 'status'},
                { data: 'name', name: 'contacts.name'},
                { data: 'payment_status', name: 'payment_status'},
                { data: 'final_total', name: 'final_total'},
                { data: 'payment_due', name: 'payment_due'},
                { data: 'received_status', name: 'received_status'},
                { data: 'action', name: 'action'}
            ],
            "fnDrawCallback": function (oSettings) {
                var total_purchase = sum_table_col($('#purchase_return_datatable'), 'final_total');
                $('#footer_purchase_return_total').text(total_purchase);
                
                $('#footer_payment_status_count').html(__sum_status_html($('#purchase_return_datatable'), 'payment-status-label'));

                var total_due = sum_table_col($('#purchase_return_datatable'), 'payment_due');
                $('#footer_total_due').text(total_due);
                
                __currency_convert_recursively($('#purchase_return_datatable'));
            },
            createdRow: function( row, data, dataIndex ) {
                $( row ).find('td:eq(5)').attr('class', 'clickable_td');
            }
        });
        $(document).on('click', '.delete_recieve', function(e) {
            swal({
                title: LANG.sure,
                text: LANG.delete_recieve_alert,
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
                                $('div.payment_modal').modal('hide');
                                $('div.edit_payment_modal').modal('hide');
                                toastr.success(result.msg);
                                if (typeof purchase_table != 'undefined') {
                                    purchase_table.ajax.reload();
                                }
                                if (typeof purchase_return_table != 'undefined') {
                                    purchase_return_table.ajax.reload();
                                }
                                if (typeof sell_table != 'undefined') {
                                    sell_table.ajax.reload();
                                }
                                if (typeof expense_table != 'undefined') {
                                    expense_table.ajax.reload();
                                }
                                if (typeof ob_payment_table != 'undefined') {
                                    ob_payment_table.ajax.reload();
                                }
                                // project Module
                                if (typeof project_invoice_datatable != 'undefined') {
                                    project_invoice_datatable.ajax.reload();
                                }
                                
                                if ($('#contact_payments_table').length) {
                                    get_contact_payments();
                                }
                            } else {
                                toastr.error(result.msg);
                            }
                        },
                    });
                }
            });
        });
        $(document).on(
        'change',
            '#purchase_list_filter_location_id',
            function() {
                purchase_return_table.ajax.reload();
            }
        );
    });
</script>
	
@endsection