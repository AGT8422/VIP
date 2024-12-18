@extends('layouts.app')
@section('title', __( 'lang_v1.quotation'))
@section('content')

<!-- Content Header (Page header) -->
<section class="content-header no-print">
    <h1>@lang('lang_v1.list_quotations')
        <small></small>
    </h1>
</section>

<!-- Main content -->
<section class="content no-print">
        @component('components.filters', ['title' => __('report.filters')])
        <div class="col-md-3">
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
                {!! Form::label('project_no', __('sale.project_no') . ':') !!}
                {!! Form::select('project_no', $project_numbers, null, ['class' => 'form-control select2' ,'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
            </div>
        </div>
        {{-- draft number --}}
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('refe_no', __('sale.draft') . ':') !!}
                {!! Form::select('refe_no', $refe_nos, null, ['class' => 'form-control select2' ,'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
            </div>
        </div>
           {{-- agent --}}
     <div class="col-md-4">
         <div class="form-group">
             {!! Form::label('agents_id', __('home.Agents') . ':') !!}
             {{  Form::select('agents_id',$users,null,['class'=>'form-control select2 ','placeholder'=>trans('lang_v1.all')]) }}
         </div>
     </div>
       	{{-- cost center --}}
        <div class="col-md-4">
            <div class="form-group">
                {!! Form::label('cost_center_id', __('home.Cost Center') . ':') !!}
                {{  Form::select('cost_center_id',$cost_centers,null,['class'=>'form-control select2 ','placeholder'=>trans('lang_v1.all')]) }}
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
                    <a class="btn btn-block btn-primary" href="{{action('SellController@create', ['status' => 'quotation'])}}">
                    <i class="fa fa-plus"></i> @lang('lang_v1.add_quotation')</a>
                </div>
            @endslot
        @endif
        <div class="table-responsive">
            <table class="table table-bordered table-striped ajax_view" id="sell_table">
                <thead>
                    <tr>
                        <th>@lang('messages.action')</th>
                        <th>@lang('messages.date')</th>
                        <th>@lang('home.Agent')</th>
                        <th>@lang('home.Cost Center')</th>
                        <th>@lang('sale.project_no')</th>
                        <th>@lang('purchase.ref_no')</th>
                        <th>@lang('sale.draft_no')</th>
                        <th>@lang('sale.customer_name')</th>
                        <th>@lang('lang_v1.contact_no')</th>
                        <th class="hide">@lang('sale.location')</th>
                        <th>@lang('lang_v1.total_items')</th>
                        <th>@lang('lang_v1.added_by')</th>
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
    // alert("stop");
    //Date range as a button
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
            "url": '/sells/quatation-dt?is_quotation=1',
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
                 if($('#agents_id').length) {
                     
                    d.agents_id = $('#agents_id').val();
                }
                 if($('#refe_no').length) {
                     
                    d.refe_no = $('#refe_no').val();
                }
                if($('#cost_center_id').length) {
                    d.cost_center_id = $('#cost_center_id').val();
                }
                d.customer_id = $('#sell_list_filter_customer_id').val();

                if($('#created_by').length) {
                    d.created_by = $('#created_by').val();
                }
                if($("#project_no").length) {
                    d.project_no = $('#project_no').val();
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
            { data: 'agents', name: 'agents'},
            { data: 'cost_center_id', name: 'cost_center_id'},
            { data: 'project_no', name: 'project_no'},
            { data: 'invoice_no', name: 'invoice_no'},
            { data: 'refe_no', name: 'refe_no'},
            { data: 'conatct_name', name: 'conatct_name'},
            { data: 'mobile', name: 'contacts.mobile'},
            { data: 'business_location', name: 'bl.name',class:"hide"},
            
            { data: 'total_items', name: 'total_items', "searchable": false},
            { data: 'added_by', name: 'added_by'},
        ],
        "fnDrawCallback": function (oSettings) {
            __currency_convert_recursively($('#purchase_table'));
        }
    });
    
    $(document).on('change', '#refe_no,#sell_list_filter_location_id,#project_no,#agents_id,#cost_center_id, #sell_list_filter_customer_id, #created_by',  function() {
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
    //                      }
    //                 })
    //             };
    //         },
    //         cache: true
    //     }
    // });
});
</script>
<script type="text/javascript" src="{{ asset('js/payments.js?v=' . $asset_v) }}"></script>
@endsection