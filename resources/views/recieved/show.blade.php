@extends('layouts.app')
@section('title', __('recieved.show_recieved'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>{{ __('recieved.show_recieved') }}</h1>
    <br>
    {{-- <h2> {{ __('warehouse.add_info') }} </h2> --}}

    <!-- <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
        <li class="active">Here</li>
    </ol> -->
</section> 


<div class="row">
    <div class="col-md-12">
           
                
            
            
        
        <!-- Main content -->
        <section class="content no-print">
            @component('components.filters', ['title' => __('report.filters')])
            <div class="col-md-3" >
                <div class="form-group">
                    {!! Form::label('type', __('warehouse.type') . ':') !!}
                    {!! Form::select('type', ['1' => __('warehouse.main'), '2' => __('warehouse.sub')], null, ['class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'type', 'placeholder' => __('lang_v1.all')]); !!}
                </div>
            </div>
            <div class="col-md-3" >
                <div class="form-group">
                    {!! Form::label('name', __('warehouse.nameW') . ':') !!}
                    {!! Form::select('name', $mainstore_categories, null, ['class' => 'form-control','id' => 'name']); !!}
                    {{-- {!! Form::text('text', "", ['class' => 'form-control ', 'style' => 'width:100%', 'id' => 'name', 'placeholder' => __('warehouse.nameW')]); !!} --}}
                </div>
            </div>
              @endcomponent
            @component('components.widget', ['class' => 'box-primary', 'title' => __('lang_v1.all_warehouse')])
            {{-- @slot('tool')
            <div class="box-tools">
                <a class="btn btn-block btn-primary" href="{{action('WarehouseController@create')}}">
                    <i class="fa fa-plus"></i> @lang('messages.add')</a>
                </div>
                @endslot --}}
                <div class="table-responsive">
                    <table class="table table-bordered table-striped ajax_view warehouse" style=""  id="warehouse">
                        <thead>
                            <tr>
                                {{-- <th>@lang('purchase.additional_notes')</th> --}}
                                <th>@lang('warehouse.name')</th>
                                <th>@lang('warehouse.mainStore')</th>
                                {{-- <th>@lang('warehouse.business_id')</th> --}}
                                <th>@lang('messages.action')</th>
                                {{-- <th>@lang('purchase.additional_notes')</th> --}}
                                
                            </tr>
                        </thead>
                        <tfoot> 
                            <tr class="bg-gray font-17 text-center footer-total">
                                <td colspan="1"><strong>@lang('sale.total'):</strong></td>
                                <td class="footer1"></td>
                                {{-- <td class="footer2"></td> --}}
                                <td class="footer3"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                @endcomponent
            </section>
            
        
        </div>
    </div>

    
    
    
    
    
    {{-- <section id="receipt_section" class="print_section"></section> --}}
    
    @stop
    @section('javascript')
<script type="text/javascript">
    //Roles table
    
    $(document).ready( function(){
       warehouse =  $('#warehouse').DataTable({
        	processing: true,
			serverSide: true,
            "ajax": {
                "url": "/warehouse/allStores",
                "data": function ( d ) {
                    d.name = $('#name').val();
                    d.type = $('#type').val();
                    d = __datatable_ajax_callback(d);
                }
            },
            columns: [
			    // {data: 'id', name: 'id'},
			    {data: 'name', name: 'name'},
			    {data: 'mainStore', name: 'mainStore'},
			    // {data: 'business_id', name: 'business_id'},
			    {data: 'action', name: 'action', searchable: false, orderable: false},
			  
			],
            "footerCallback": function (row, data, start, end, display){
                var count = 0;
                for (var r in data){
                    count = count + 1;
                }
                $('.footer1').html(count);
            }
        });
    });
    $(document).on("change","#type",function(){
        warehouse.ajax.reload();

        
    });
    $(document).on("input","#name",function(){
        warehouse.ajax.reload();

        
    });
</script>
@endsection

{{--  --}}