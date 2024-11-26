@extends('layouts.app')
@section('title', __('lang_v1.account_tree_purchase'))

@section('content')

<section class="content-header">
    <h1>{{ __('lang_v1.account_tree_purchase') }}</h1>
    <br>
</section> 
  
<!-- Main content -->
<section class="content no-print">
    
    @component('components.widget', ['class' => 'box-primary', 'title' => ""])
    
    <div class="row">
        <div class="col-md-12">
        @component('components.filters', ['title' => __('report.filters')])
                <div class="col-md-3" >
                    <div class="form-group">
                        {!! Form::label('type', __('product.product_type') . ':') !!}
                        {!! Form::select('type', ['single' => __('lang_v1.single'), 'variable' => __('lang_v1.variable'), 'combo' => __('lang_v1.combo')], null, ['class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'product_list_filter_type', 'placeholder' => __('lang_v1.all')]); !!}
                    </div>
                </div>
                
                <div class="col-md-3" id="location_filter">
                    <div class="form-group">
                        {!! Form::label('location_id',  __('purchase.business_location') . ':') !!}
                        {!! Form::select('location_id', $business_locations, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
                    </div>
                </div>
                <div class="col-md-3" >
                    <div class="form-group">
                        {!! Form::label('type', __('warehouse.type') . ':') !!}
                        {!! Form::select('type', ['1' => __('warehouse.main'), '2' => __('warehouse.sub')], null, ['class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'type', 'placeholder' => __('lang_v1.all')]); !!}
                    </div>
                </div>
                {{-- <div class="col-md-3" >
                    <div class="form-group">
                        {!! Form::label('name', __('warehouse.nameW') . ':') !!}
                        {!! Form::select('name', $mainstore_categories, null, ['class' => 'form-control','id' => 'name']); !!}
                    </div>
                </div>
                <div class="col-md-3" >
                    <div class="form-group">
                        {!! Form::label('movement', __('warehouse.movement') . ':') !!}
                        {!! Form::select('movement', $movement_categories, $movement_categories, ['class' => 'form-control','id' => 'movement']); !!}
                    </div>
                </div> --}}
        @endcomponent
    
    
        </div>
    </div>
    

        <div class="table-responsive">
            <table class="table table-bordered table-striped ajax_view warehouse" style=""  id="account_tree_purchase">
                <thead>
                    <tr>
                        {{-- <th>@lang('purchase.additional_notes')</th> --}}
                        <th>@lang('lang_v1.Registration_number')</th>
                        <th>@lang('lang_v1.movement_type')</th>
                        <th>@lang('lang_v1.credit_account')</th>
                        <th>@lang('lang_v1.debit_account')</th>
                        <th>@lang('lang_v1.credit')</th>
                        <th>@lang('lang_v1.debit')</th>
                        <th>@lang('lang_v1.sstatus')</th>
                        <th>@lang('lang_v1.total')</th>
                        <th>@lang('lang_v1.Statement')</th>
                        <th>@lang('lang_v1.date')</th>
                        {{-- <th>@lang('messages.action')</th> --}}
                        {{-- <th>@lang('purchase.additional_notes')</th> --}}

                    </tr>
                </thead>
                <tfoot> 
                    <tr class="bg-gray font-17 text-center footer-total">
                        <td colspan="5"></td>
                        <td></td>
                        <td class="footer_status_count"></td>
                        <td class="footer_payment_status_count"></td>
                        <td class="footer_purchase_total"></td>
                        <td></td>
                        {{-- <td><س/td> --}}
                    </tr>
                </tfoot>
            </table>
        </div>


    @endcomponent
</section>

@stop

@section('javascript')
<script src="{{ asset('js/producte.js?v=' . $asset_v) }}"></script>
<script src="{{ asset('js/functions.js?v=' . $asset_v) }}"></script>
<script type="text/javascript">
    //Roles table
    
    $(document).ready( function(){
        account_tree_purchase = $('#account_tree_purchase').DataTable({
        	processing: true,
			serverSide: true,
            ajax:{ 
            "url":'/accountsTree/allpurchases',
            "data": function (d){
                
                // d.location_id = $('#location_id').val();
                // d.name = $('#name').val();
                // d.type = $('#type').val();
                // d.movement = $('#movement').val();
                // d = __datatable_ajax_callback(d);

                }
            },
            columnDefs: [ {
                "targets": [0, 1, 2],
                "orderable": false,
                "searchable": false
            } ],
            columns: [
			    // {data: 'id', name: 'id'},
			    {data: 'restriction_id', name: 'restriction_id'},
			    {data: 'movement_type', name: 'movement_type'},
			    {data: 'credit_account', name: 'credit_account'},
			    {data: 'debit_account', name: 'debit_account'},
			    {data: 'credit', name: 'credit'},
			    {data: 'debit', name: 'debit'},
			    {data: 'status', name: 'status'},
			    {data: 'total', name: 'total'},
			    {data: 'statement', name: 'statement'},
			    {data: 'created_at', name: 'created_at'},
			    // {data: 'updated_at', name: 'updated_at'},
			    // {data: 'action', name: 'action', searchable: false, orderable: false},
			  
			]
        });
    });
    // $(document).on('change','#location_id',
    //             function() {
    //                 account_tree_purchase.ajax.reload();
    //         });
    // $(document).on('change','#name',
    //             function() {
    //                 account_tree_purchase.ajax.reload();
    //         });
    // $(document).on('change','#type',
    //             function() {
    //                 account_tree_purchase.ajax.reload();
    //         });
    // $(document).on('change','#movement',
    //             function() {
    //                 account_tree_purchase.ajax.reload();
    //         });
    
</script>
@endsection