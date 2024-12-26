@extends('layouts.app')
@section('title', __('purchase.purchases'))

@section('content')
<!-- Content Header (Page header) -->
<section class="content-header no-print">
    <h1>@lang('purchase.purchases')
        <small></small>
        <h5><i><b>{{ "   Purchases  >  " }} </b>{{ "List Purchase  "   }} <b> {{" "}} </b> {{" "}}</i></h5>
        <br> 
    </h1>
    <!-- <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
        <li class="active">Here</li>
    </ol> -->
</section>

<!-- Main content -->
<section class="content no-print" >
    @component('components.filters', ['class' => 'box-primary','title' => __('report.filters')])
        <div class="col-md-4 hide">
            <div class="form-group">
                {!! Form::label('purchase_list_filter_location_id',  __('purchase.business_location') . ':') !!}
                {!! Form::select('purchase_list_filter_location_id', $business_locations, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                {!! Form::label('purchase_list_filter_supplier_id',  __('purchase.supplier') . ':') !!}
                {!! Form::select('purchase_list_filter_supplier_id', $suppliers, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                {!! Form::label('purchase_list_filter_receipt',  __('purchase.sup_refe') . ':') !!}
                {!! Form::select('purchase_list_filter_receipt' ,$sup_refe , null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                {!! Form::label('purchase_list_filter_status',  __('purchase.purchase_status') . ':') !!}
                {!! Form::select('purchase_list_filter_status', $orderStatuses, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                {!! Form::label('purchase_list_filter_payment_status',  __('purchase.payment_status') . ':') !!}
                {!! Form::select('purchase_list_filter_payment_status', ['paid' => __('lang_v1.paid'), 'due' => __('lang_v1.due'), 'partial' => __('lang_v1.partial'), 'overdue' => __('lang_v1.overdue')], null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                {!! Form::label('purchase_list_filter_date_range', __('report.date_range') . ':') !!}
                {!! Form::text('purchase_list_filter_date_range', null, ['placeholder' => __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'readonly']); !!}
            </div>
        </div>
         

    @endcomponent

    @component('components.widget', ['class' => 'box-primary', 'title' => __('purchase.all_purchases')])
        @include('purchase.partials.update_purchase_status_modal')
        @can('purchase.create')
            @slot('tool')
                <div class="box-tools">
                    <a class="btn btn-block btn-primary" href="{{action('PurchaseController@create')}}">
                    <i class="fa fa-plus"></i> @lang('messages.add')</a>
                </div>
            @endslot
        @elsecan('SalesMan.views')
            @slot('tool')
                <div class="box-tools">
                    <a class="btn btn-block btn-primary" href="{{action('PurchaseController@create')}}">
                    <i class="fa fa-plus"></i> @lang('messages.add')</a>
                </div>
            @endslot
        @elsecan('admin_supervisor.views')
            @slot('tool')
                <div class="box-tools">
                    <a class="btn btn-block btn-primary" href="{{action('PurchaseController@create')}}">
                    <i class="fa fa-plus"></i> @lang('messages.add')</a>
                </div>
            @endslot
        @endcan
        @include('purchase.partials.purchase_table')
    @endcomponent

    <div class="modal fade product_modal" tabindex="-1" role="dialog" 
    	aria-labelledby="gridSystemModalLabel">
    </div>

    <div class="modal fade payment_modal" tabindex="-1" role="dialog" 
        aria-labelledby="gridSystemModalLabel">
    </div>

    <div class="modal fade edit_payment_modal" tabindex="-1" role="dialog" 
        aria-labelledby="gridSystemModalLabel">
    </div>

   

</section>

<section id="receipt_section" class="print_section"></section>
<!-- /.content -->
@stop
@section('javascript')
<script src="{{ asset('js/purchase.js?v=' . $asset_v) }}"></script>
<script src="{{ asset('js/payments.js?v=' . $asset_v) }}"></script>
{{-- <script src="{{ asset('js/worker.js?v=' . $asset_v) }}"></script> --}}
<script>
    
        
    //  $(document).ready(function(){
    //     if (window.Worker) {
    //           // // Create a new Web Worker
    //           const worker = new Worker('js/worker.js');
    //           // // Start the worker when the button is clicked
    //             @if(session('data'))
    //                 list                = "{{session('data')}}"  ;
    //                 business_id         = "{{session('business_id')}}"  ;
    //                 lines               = list.split(",");
    //                 var older           = new Array();
    //                 var row_lines       = new Array();
    //                 var ob              = new Array();
    //                 for(i in lines){ 
    //                     row_lines.push(lines[i]);
    //                     localStorage.setItem(lines[i],"notAccountCompleted");
    //                 }
    //                 getblc(row_lines,0);
    //                 function getblc(lines,index){ 
    //                     if (index < lines.length){
    //                         if(!older.includes(index)){
    //                             older.push(index);
    //                             if (index in lines){
    //                                 list_of_data = [];
    //                                 list_of_data.push(lines[index]);
    //                                 list_of_data.push(business_id);
    //                                 // Post a message to the worker 
    //                                 worker.postMessage(list_of_data); 
                                    
    //                                 // Listen for messages from the worker
    //                                 worker.onmessage = function (event) {
    //                                     // Display the result from the worker
    //                                     // $('#result').html('Result: ' + event.data);
    //                                     if(event.data == "true"){
    //                                         localStorage.setItem(lines[index],"completed");
    //                                         localStorage.removeItem(lines[index]);
    //                                         index = parseFloat(index)+1; 
    //                                         getblc(lines,index); 
    //                                     }
    //                                 };
    //                                 // Handle any errors that occur in the worker
    //                                 worker.onerror = function (error) {
    //                                     console.error('Worker error:', error);
    //                                 };
    //                             }
    //                         }
    //                     }
    //                 }
                     
    //             @endif
              
    //             window.addEventListener('beforeunload', () => {
    //                worker.terminate();
    //            });
    //     } else {
    //         console.error('Your browser doesn\'t support Web Workers.');
    //     }
        
    //  });
      // Check if the browser supports Web Workers

    //Date range as a button
    $('#purchase_list_filter_date_range').daterangepicker(
        dateRangeSettings,
        function (start, end) {
            $('#purchase_list_filter_date_range').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
           purchase_table.ajax.reload();
        }
    );

    $('#purchase_list_filter_date_range').on('cancel.daterangepicker', function(ev, picker) {
        $('#purchase_list_filter_date_range').val('');
        purchase_table.ajax.reload();
    });

    $('#purchase_list_filter_receipt').on('change', function(ev, picker) {
        purchase_table.ajax.reload();
    });

    $(document).on('click', '.update_status', function(e){
        e.preventDefault();
        $('#update_purchase_status_form').find('#status').val($(this).data('status'));
        $('#update_purchase_status_form').find('#purchase_id').val($(this).data('purchase_id'));
        $('#update_purchase_status_modal').modal('show');
    });

    $(document).on('submit', '#update_purchase_status_form', function(e){
        e.preventDefault();
        var form = $(this);
        var data = form.serialize();

        $.ajax({
            method: 'POST',
            url: $(this).attr('action'),
            dataType: 'json',
            data: data,
            beforeSend: function(xhr) {
                __disable_submit_button(form.find('button[type="submit"]'));
            },
            success: function(result) {
                if (result.success == true) {
                    $('#update_purchase_status_modal').modal('hide');
                    toastr.success(result.msg);
                    purchase_table.ajax.reload();
                    $('#update_purchase_status_form')
                        .find('button[type="submit"]')
                        .attr('disabled', false);
                } else {
                    toastr.error(result.msg);
                }
            },
        });
    });
     
    $('#purchase_list_filter_receipt').select2({
        placeholder: '',
        ajax: {
            url: '/purchases/sup_refe',
            dataType: 'json',
            delay: 250,
            processResults: function (data) {
                return {
                    results: $.map(data, function (item) {
                        return {
                            text: item.sup_refe,
                            id: item.sup_refe,
                         }
                    })
                };
            },
            cache: true
        }
    });
</script>
	
@endsection