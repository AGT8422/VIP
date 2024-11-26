@extends('layouts.app')
@section('title', __('warehouse.conveyor'))

@section('content')

    <section class="content-header">
        <h1>{{ __('warehouse.Warehouse_Movement') }}</h1>
        <br>
    </section> 
    
    <!-- Main content -->
    <section class="content no-print"> 
        @component('components.widget', ['class' => 'box-primary', 'title' => __('warehouse.all_movement')])
            <div class="store" hidden >{{ app('request')->input('store_id')??array_keys($mainstore_categories)[1]}}</div>
            <div class="state" hidden >{{ app('request')->input('store_id')?1:0}}</div> 
            <div class="row">
                <div class="col-md-12">
                    @component('components.filters', ['title' => __('report.filters')])
            
                            <div class="col-md-4" id="location_filter">
                                <div class="form-group">
                                    {!! Form::label('product_id',  __('product.name') . ':') !!}
                                    {!! Form::select('product_id', $product_list, null, ['class' => 'form-control select2', 'style' => 'width:100%' ]); !!}
                                </div>
                            </div> 
                            <div class="col-md-4" >
                                <div class="form-group">
                                    {!! Form::label('name', __('warehouse.nameW') . ':') !!}
                                    {!! Form::select('name', $mainstore_categories, null, ['class' => 'form-control','id' => 'name']); !!}
                                </div>
                            </div>
                            <div class="col-md-4" >
                                <div class="form-group">
                                    {!! Form::label('movement', __('warehouse.movement') . ':') !!}
                                    {!! Form::select('movement', $movement_categories, null, ['class' => 'form-control','id' => 'movement']); !!}
                                </div>
                            </div>
                            <div class="col-md-4" >
                                <div class="form-group">
                                    {!! Form::label('date', __('lang_v1.until_date') . ':') !!}
                                    {!! Form::date('date', null, ['class' => 'form-control','id' => 'date']); !!}
                                </div>
                            </div>
                    @endcomponent 
                </div>
            </div> 
            <div class="table-responsive" hidden>
                <table class="table table-bordered table-striped ajax_view warehouse"  style=""  id="warehouse_movement">
                    <thead>
                        <tr>
                            <th>@lang('lang_v1.date')</th>
                            <th>@lang('lang_v1.id')</th>
                            <th>@lang('lang_v1.loaction_address')</th>
                            <th>@lang('product.product_name')</th>
                            <th>@lang('lang_v1.unit')</th>
                            <th>@lang('warehouse.nameW')</th>
                            <th>@lang('movement.move')</th>
                            <th>@lang('movement.movePlus')</th>
                            <th>@lang('movement.moveMinus')</th>
                            <th>@lang('movement.total')</th>
                            <th class="hide">@lang('lang_v1.price')</th>
                    
                        </tr>
                    </thead>
                    <tfoot> 
                        <tr class="bg-gray font-17 text-center footer-total">
                            <td colspan="5"><strong>@lang('sale.total'):</strong></td>
                            <td class="text-left"><small>@lang('lang_v1.sells') -  <span class="footer_sells"></span><br>
                            @lang('lang_v1.purchase') - <span class="footer_purchases"></span><br>@lang('purchase.Open') - <span class="footer_Open">
                            </small></td>
                            <td class="footer_status_count"></td>
                            <td class="footer_payment_status_count"></td>
                            <td class="footer_total_amount"></td>
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
           var getUrlParameter = function getUrlParameter(sParam) {
        var sPageURL = window.location.search.substring(1),
            sURLVariables = sPageURL.split('&'),
            sParameterName,
            i;
        for (i = 0; i < sURLVariables.length; i++) {
            sParameterName = sURLVariables[i].split('=');
            if (sParameterName[0] === sParam) {
                return sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
            }
        }
        return false;
    };
    var store = getUrlParameter("store_id") ;
        warehouseTable = $('#warehouse_movement').DataTable({
        	processing: true,
			serverSide: true,
            ajax:{ 
            "url":'/warehouse/allMovement?store_id=' + store ,
            "data": function (d){
                
                d.location_id = $('#location_id').val();
                d.name        = $('#name').val();
                d.type        = $('#type').val();
                d.date        = $('#date').val();
                d.product_id  = $('#product_id').val();
                d.movement    = $('#movement').val();
                d              = __datatable_ajax_callback(d);

                }
            },
            aaSorting: [0, 'asc'],
            columnDefs: [ {
                "targets": [0, 1, 2],
                "orderable": false,
                "searchable": false
            } ],
            columns: [
			    {data: 'created_at', name: 'created_at'},
			    {data: 'id', name: 'id'},
			    {data: 'business_id', name: 'business_id'},
			    {data: 'product_name', name: 'product_name' },
			    {data: 'unit_id', name: 'unit_id'},
			    {data: 'store_id', name: 'store_id' , class:'store', data_id:'store_id'},
			    {data: 'movement', name: 'movement'},
			    {data: 'plus_qty', name: 'plus_qty'},
			    {data: 'minus_qty', name: 'minus_qty'},
			    {data: 'current_qty', name: 'current_qty' , class:'quantity_os'},
			    {data: 'current_price', name: 'current_price' ,class:'hide'},
			  
			],
            fnDrawCallback: function(oSettings) {
            __currency_convert_recursively($('#purchase_table'));
            },
            "footerCallback": function ( row, data, start, end, display ) {
                var total_qty = 0;
                var purchase = 0 ;
                var sale = 0 ;
                var open_quantitye = 0 ;

                
               
                var plus_qty  = 0; 
                var minus_qty  = 0; 
                for (var r in data){
                    //   alert(JSON.stringify(data[r].movement));
                    const myJSON = JSON.stringify(data[r].movement);
                    
                    if( myJSON == '"sale"'){
                        sale = parseFloat(sale) + 1;
                    }else if(myJSON == '"purchase"') {
                        purchase = purchase + 1;
                    }else if( myJSON.indexOf("opening_stock") !== -1) {
                        open_quantitye = open_quantitye + 1;
                    
                    }
                   // total_qty = total_qty + parseFloat(JSON.stringify(data[r].current_qty));
                  
                   open_quantity =  open_quantity + (JSON.stringify(data[r].current_qty) - open_quantity);
                   plus_qty =  plus_qty +  parseFloat(JSON.stringify(data[r].plus_qty)); 
                   minus_qty =  minus_qty + parseFloat(JSON.stringify(data[r].minus_qty));  
                    //   console.log(open_quantity+'--------yarab');
                    
                    
                }
                var open_quantity = plus_qty - minus_qty;
                $('.footer_total_amount').html(open_quantity);
                $('.footer_sells').html(sale);
                $('.footer_purchases').html(purchase);
                $('.footer_Open').html(open_quantitye);
              
            },
        });
    });
    $(document).ready(function(){
      if($(".state").html() == 1){
            $(".table-responsive").attr("hidden", false);
            $(".table-responsive").css("width","100%");
            $("#warehouse_movement").css("width","100%");
        }
    });
    $(document).on('change','#location_id',
                function() {
                        warehouseTable.ajax.reload();
                    });
    $(document).on('change','#name,#date',
                    function() {
                        warehouseTable.ajax.reload();
                        $(".table-responsive").attr("hidden", false);
                        $(".table-responsive").css("width","100%");
                        $("#warehouse_movement").css("width","100%");


                    });
    $(document).on('change','#type',
                    function() {
                        warehouseTable.ajax.reload();
                        $(".table-responsive").attr("hidden",false);
                        $(".table-responsive").css("width","100%");
                        $("#warehouse_movement").css("width","100%");

                    });
    $(document).on('change','#movement',
                    function() {
                        warehouseTable.ajax.reload();
                        $(".table-responsive").attr("hidden",false);
                        $(".table-responsive").css("width","100%");
                        $("#warehouse_movement").css("width","100%");

                    });
    $(document).on('change','#product_id',
                    function() {
                        warehouseTable.ajax.reload();
                        $(".table-responsive").attr("hidden",false);
                        $(".table-responsive").css("width","100%");
                        $("#warehouse_movement").css("width","100%");

            });
     
    
</script>
@endsection