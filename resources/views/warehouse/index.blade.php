@extends('layouts.app')
@section('title', __('warehouse.warehouse'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>{{ __('warehouse.warehouse') }}</h1>
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
              {{-- <div class="col-md-3" > 
                <div class="form-group"> 
                   <button data-href="{{action('WarehouseController@zero_qty')}}" class="delete-all-i btn btn-xs btn-danger"><i class="fa fa-trash"></i> @lang("messages.delete")</button>  
                </div> 
             </div>  --}}
            <div class="col-md-3" >
                <div class="form-group">
                    {!! Form::label('type', __('warehouse.type') . ':') !!}
                    {!! Form::select('type', ['1' => __('warehouse.main'), '2' => __('warehouse.sub')], null, ['class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'type', 'placeholder' => __('lang_v1.all')]); !!}
                </div>
            </div>
            <div class="col-md-3" >
                <div class="form-group">
                    {!! Form::label('name', __('warehouse.nameW') . ':') !!}
                    {!! Form::select('name', $mainstore_categories, null, ['class' => 'form-control','id' => 'name', 'placeholder' => __('lang_v1.all')]); !!}
                    {{-- {!! Form::text('text', "", ['class' => 'form-control ', 'style' => 'width:100%', 'id' => 'name', 'placeholder' => __('warehouse.nameW')]); !!} --}}
                </div>
            </div>
              @endcomponent
            @component('components.widget', ['class' => 'box-primary', 'title' => __('lang_v1.all_warehouse')])
               @if(auth()->user()->can("warehouse.views"))
                @slot('tool')
                <div class="box-tools">
                    <a class="btn btn-block btn-primary" href="{{action('WarehouseController@create')}}">
                    <i class="fa fa-plus"></i> @lang('messages.add')</a>
                </div>
                @endslot

                @endif
              
                <div class="table-responsive">
                    <table class="full_width_table table table-bordered table-striped ajax_view warehouse" style=""  id="warehouse">
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
                <h4>@lang("warehouse.component")</h4>
                <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                    @foreach($mainstore->whereNull('parent_id') as $single)
                     <div class="panel panel-default">
                       <div class="panel-heading" role="tab" id="headingTwo{{ $single->id }}">
                         <h4 class="panel-title">
                         <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo{{ $single->id }}" aria-expanded="false" aria-controls="collapseTwo">
                           {{ $single->name }}
                         </a>
                       </h4>
                       </div>
                       <div id="collapseTwo{{ $single->id }}" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo{{ $single->id }}">
                         <div class="panel-body">
                           @foreach($single->sub_stores as $sub_store)
                           <ul> 
                               <li>
                                   <a href="{{ URL::to('warehouse/index?warehouse_id='.$sub_store->id) }}">
                                        {{ $sub_store->name }}
                                   </a>
                               </li>
                           </ul>
                          @endforeach
                         </div>
                       </div>
                     </div>
                     @endforeach
                     
                   </div>
                @endcomponent
            

            @component('components.widget', ['class' => 'box-primary' , "style" => " margin:10px;padding:10px;" ,'title' => __('warehouse.Quentity') ])

                <div class="row" >
                    <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table table-recieve" >
                                <tr style="background:#f1f1f1;">
                                    <th>@lang('product.product_name')</th>
                                    <th>@lang('lang_v1.quantity_all')</th>
                                    <th>@lang('warehouse.nameW')</th>
                                    <th>@lang("warehouse.movement")</th>
                               
                                </tr>
                                @php
                                $array_warehouse_name = [];
                                $total2 = 0;
                                @endphp
                                @forelse ($warehouse_info as $ware_house)
                                    @if($ware_house->product_qty !=0)
                                        @php
                                            
                                            $Warehouse_name = "";
                                            $Product_name = "";
                                            $store_id = $ware_house->store_id;
                                            $counter_1 = 1; 
                                            if(!in_array($ware_house->store_id,$array_warehouse_name)){
                                                array_push($array_warehouse_name,$ware_house->store_id );
                                            }
                                            $x = array_keys($Warehouse_list);
                                            foreach($x as $sd){
                                                if($ware_house->store_id == $sd ){
                                                    $Warehouse_name = $Warehouse_list[$sd] ;
                                                }
                                            }
                                            $xx = array_keys($product_list);
                                            foreach($xx as $sxd){
                                                if($ware_house->product_id == $sxd ){
                                                    $Product_name = $product_list[$sxd] ;
                                                }
                                            }
                                
                                        @endphp 
            
                                        <tr style="border:1px solid #f1f1f1;">
                                        <td>{{$Product_name}}</td>
                                        <td>{{$ware_house->product_qty}}</td>
                                        <td>{{$Warehouse_name}}</td>
                                        <td><a href="/warehouse/movement?store_id={{ $store_id }}"  >@lang("warehouse.movement")</a></td>
                                        </tr>
                                    @endif
                                @empty
                                
                                @endforelse
                                @php
    
                                @endphp
                                
                                <tfoot>
                                <tr class="bg-gray  font-17 footer-total" style="border:1px solid #f1f1f1;  ">
                                    <td class="text-center " colspan="1">&nbsp;</strong></td>
                                    <td></td>
                                    <td></td>
                                  
                            
                                </tr>
                            </tfoot>
                            </table>
                        </div>
                    </div>
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
			    {data: 'parent_name', name: 'mainStore'},
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
$(document).on('click', 'button.delete-all-i', function() {
    swal({
        title: LANG.sure,
        icon: 'warning',
        buttons: true,
        dangerMode: true,
    }).then(willDelete => {
        if (willDelete) {
            var href = $(this).data('href');
            var data = $(this).serialize();
            $.ajax({
                method: 'DELETE',
                url: href,
                dataType: 'json',
                data: data,
                success: function(result) {
                    
                    if (result.success == true) {
                        toastr.success(result.msg);
                        recipe_table.ajax.reload();
                    } else {
                        toastr.error(result.msg);
                    }
                },
            });
        }
    });
});
$(document).on('click', 'button.delete_user_button', function() {
    swal({
        title: LANG.sure,
        icon: 'warning',
        buttons: true,
        dangerMode: true,
    }).then(willDelete => {
        if (willDelete) {
            var href = $(this).data('href');
            var data = $(this).serialize();
            $.ajax({
                method: 'GET',
                url: href,
                dataType: 'json',
                data: data,
                success: function(result) {
                    if (result.success == true) {
                        toastr.success(result.msg);
                        recipe_table.ajax.reload();
                    } else {
                        toastr.error(result.msg);
                    }
                },
            });
        }
    });
});
    $(document).on("input","#name",function(){
        warehouse.ajax.reload();

        
    });
</script>
@endsection

{{--  --}}