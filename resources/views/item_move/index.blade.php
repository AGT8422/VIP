@extends("layouts.app")

@section("title",__("home.ItemMove"))

@section("content")


@php
    $date_format = request()->session()->get('business.date_format');
    $date_formate = in_array(session()->get('lang', config('app.locale')), config('constants.langs_rtl')) ?   $date_format:$date_format;
    $date_period  = in_array(session()->get('lang', config('app.locale')), config('constants.langs_rtl')) ?   "هذه السنة":"This Year";
@endphp

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('home.ItemMove') </h1>
    @php
        if($variation_id != null){
            $variations   = \App\Variation::find($variation_id);
            $product_name = ($variations)? $product->name . ' | ' . $variations->name:$product->name; 
        }else{
            $product_name = $product->name; 
            
        }
    @endphp
    <h5><i><b>{{ __('home.ItemMove') }}  {{ __("izo.>") . " " }} </b>  <b>  {{ " ( " . $product_name . " ) " }}  </b></i></h5>
    <br>  
</section>


<section class="content">
  
    <div class="row">
        <div class="col-md-12">
            @component("components.filters",[ "class" => ""  , "title" => "Filter" ,"style" =>"margin:0px 4%"])
                    <div class="col-md-4"   >
                        <div class="form-group">
                            {!! Form::label('date', __('lang_v1.until_date') . ':') !!}
                            {!! Form::date('date', null, ['class' => 'form-control','id' => 'date']); !!}
                        </div>
                    </div>
                    <div class="col-md-4"   >
                        <div class="form-group">
    
                            {!! Form::label('transaction_date_range', __('report.date_range') . ':') !!}
    
                            <div class="input-group">
    
                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
    
                                {!! Form::text('transaction_date_range', null, ['class' => 'form-control', 'readonly', 'placeholder' => __('report.date_range')]) !!}
    
                            </div>
    
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('unit_id', __('product.unit') . ':') !!}
                            {!! Form::select('unit_id', $units, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'unit_product_id']); !!}
                        </div>
                    </div>
                        {{-- @foreach($check as $key=>$value)
                            @php $tr =  \App\Transaction::find($key); ($tr->type == "purchase")? $it = $tr->ref_no : $it = $tr->invoice_no ; @endphp
                                @if($tr->type=="purchase")
                                    <p> ::: <button class="btn btn-modal btn-link" data-container=".view_modal" data-href="{{action("PurchaseController@show",[$tr->id])}}">{{ $it }}</button>  ::: {{($value<0)?($value*-1) ." plus" : $value . " minus"}} ::: </p>
                                @elseif($tr->type=="sale")
                                    <p> ::: <button class="btn btn-modal btn-link" data-container=".view_modal" data-href="{{action("SellController@show",[$tr->id])}}">{{ $it }}</button>  ::: {{($value<0)?($value*-1) ." plus" : $value . " minus"}} ::: </p>
                                @endif
                        @endforeach
                    --}}
        
            @endcomponent
        </div>
    </div>
	<!-- Page level currency setting -->
	<input type="hidden" id="date_period" value="{{$date_period}}">
	<input type="hidden" id="date_format" value="{{'\''.$date_formate.'\''}}">
	<input type="hidden" id="p_code" value="{{$currency_details->code}}">
	<input type="hidden" id="p_symbol" value="{{$currency_details->symbol}}">
	<input type="hidden" id="p_thousand" value="{{$currency_details->thousand_separator}}">
	<input type="hidden" id="p_decimal" value="{{$currency_details->decimal_separator}}">

    <input type="hidden" id="product_id" value="{{$product->id}}">
    <input type="hidden" id="variation_id" value="{{$variation_id}}">
    <div class="row">
        <div class="col-md-12">
            @component("components.widget",["title"=>__("home.movement") , "class"=>"box-primary" ])
                <div class="row">
                    <div class="col-xs-12">
                        <div class="table">
                            <table class="table table-stripted table-bordered dataTable" id="item_move">
                                    <thead>
                                        <tr>
                                        <td>@lang("lang_v1.date")</td>
                                        <td class="max_width">@lang("home.name")</td>
                                        <td>@lang("home.account")</td>
                                        <td>@lang("home.state")</td>
                                        <td>@lang("purchase.ref_no")</td>
                                        <td id="plus">@lang("purchase.qty") (+)</td>
                                        <td id="minus">@lang("purchase.qty") (-)</td>
                                        @if(auth()->user()->hasRole('Admin#' . session('business.id')) ||  auth()->user()->can('product.avarage_cost'))
                                        <td>@lang("home.row_price")</td>
                                        <td>@lang("home.row_price_inc_exp")</td>
                                        <td id="UnitCost">@lang("home.unit_cost")</td>
                                        @endif
                                        <td>@lang("home.current_qty")</td>
                                        <td>@lang("warehouse.nameW") </td>
                                    </tr>
                                    </thead>
                                    <tbody></tbody>
                                    <tfoot>
                                        <tr>
                                            @if(auth()->user()->hasRole('Admin#' . session('business.id')) ||  auth()->user()->can('product.avarage_cost'))
                                                <td colspan="7" id="footer_total"></td>
                                                <td id="qty" colspan="1"></td>
                                                <td id="previous"></td>
                                            <td id="cost_final" colspan="3"> @lang("home.unit_cost") : &nbsp; @if(auth()->user()->hasRole('Admin#' . session('business.id')) ||  auth()->user()->can('product.avarage_cost'))@format_currency($movment_cost)@else {{ "--" }} @endif</td>
                                            @else
                                            <td colspan="6"></td>
                                            <td id="qty"></td>
                                            <td id="previous"></td>
                                                <td id="cost_final" colspan="1"> @lang("home.unit_cost") : &nbsp; @if(auth()->user()->hasRole('Admin#' . session('business.id')) ||  auth()->user()->can('product.avarage_cost'))@format_currency($movment_cost)@else {{ "--" }} @endif</td>
                                            @endif
                                        </tr>
                                    </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            @endcomponent
        </div>
    </section>

 
@stop

@section('javascript')
    <script src="{{ asset('js/function.js?v=' . $asset_v) }} "></script>
    {{-- <script src="{{ asset('js/item_move.js?v=' . $asset_v) }} "></script> --}}
    <script  type="text/javascript">
        $(document).ready(function(){
            dateRangeSettings.startDate = moment().startOf('year');

            dateRangeSettings.endDate   = moment().endOf('year');
    
            $('#transaction_date_range').daterangepicker(
                dateRangeSettings,
                function (start, end) {
                    $('#transaction_date_range').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
                    item_move.ajax.reload();
                }
            );
            
        });
        $(document).on('click', '.btn-modal', function(e) {
            e.preventDefault();
            var container = $(this).data('container');
            $.ajax({
                url: $(this).data('href'),
                dataType: 'html',
                success: function(result) {
                    $(container)
                        .html(result)
                        .modal('show');
                },
            });
        });
        var product_id   = $("#product_id").val();
        var variation_id = $("#variation_id").val();
        
        
        item_move  = $('#item_move').DataTable({
            processing: true,
            serverSide: true,
            ajax:{
                url:"/item-move/"+product_id+"?variation_id="+variation_id ,
                data:function(d){
                    Format_Date      = $('#date_format').val();  
                    Period_Date      = $('#date_period').val();
                    var start = dateRangeSettings.ranges["This Year"][0].format('YYYY-MM-DD');
                    var end   = dateRangeSettings.ranges["This Year"][1].format('YYYY-MM-DD');
                    
                    console.log(start + " ~ " + end + " " + Period_Date+ " " + Format_Date);
                    if($('#transaction_date_range').val()){
                        
                        start = $('input#transaction_date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
                        
                        end   = $('input#transaction_date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');
                        
                    }
                    console.log(start + " ~ " + end + " 222 " + Period_Date+ " " + Format_Date);
                    
                    d.start_date   = start;
                    d.end_date     = end;
                    d.unit_id      = $("#unit_product_id").val();
                    d.date         = $("#date").val();
                    d.product_id   = $("#product_id").val();
                    d.variation_id = $("#variation_id").val();
                    d = __datatable_ajax_callback(d);
                }
            },
            pageLength : -1,
            aaSorting:[0,"desc"],
            columns:[
                {data:"created_at",name:"created_at"},
                {data:"product",name:"product"},
                {data:"account",name:"account"},
                {data:"state",name:"state"},
                {data:"references",name:"references"},
                {data:"qty_plus",name:"qty_plus"},
                {data:"qty_minus",name:"qty_minus"},
                @if(auth()->user()->hasRole('Admin#' . session('business.id')) ||  auth()->user()->can('product.avarage_cost'))
                    {data:"row_price",name:"row_price" , class:"hi"},
                    {data:"row_price_inc_exp",name:"row_price_inc_exp" , class:"hi"},
                    {data:"unit_cost",name:"unit_cost",class:"hi"},
                @endif
                {data:"current_qty",name:"current_qty"},
                {data:"store_id",name:"store_id"},
            ],
            fnDrawCallback: function(oSettings) {
                __currency_convert_recursively($('#item_move'));
            },
            "footerCallback": function ( row, data, start, end, display ) {
                var total_plus  = 0;
                var total_minus = 0;
                var previous    = 0;
                var  check = 0; 
                for (var r in data){
                     
                    check        = 1; 
                    total_plus  += parseFloat(data[r].qty_plus) ;
                    total_minus += parseFloat(data[r].qty_minus) ;
                    if(r == 0){
                        @if(auth()->user()->hasRole('Admin#' . session('business.id')) ||  auth()->user()->can('product.avarage_cost'))
                           var costed =   data[r].unit_cost ;
                            $('#cost_final').html(    "Unit Cost : " + costed);
                        @endif
                        previous = data[r].before;
                        $('#previous').html(    "Previous Unit Cost : " + previous.cost);
                        $('#qty').html(    "Previous Qty : " + previous.qty + " ( " + data[r].signal + " ) " );
                        var q = parseFloat(data[r].current_qty);
                        $('#footer_total').html(LANG.qty + " : " + ((q).toFixed(2)));
                    }
                }
                if(check == 0){
                    $('#cost_final').html(    "Unit Cost : " + 0);
                    $('#footer_total').html(LANG.qty + " : " + ((0).toFixed(2)));
                    Format_Date      = $('#date_format').val();  
                    Period_Date      = $('#date_period').val();
                    var start_date = dateRangeSettings.ranges["This Year"][0].format('YYYY-MM-DD');
                    var end   = dateRangeSettings.ranges["This Year"][1].format('YYYY-MM-DD');
                    
                    if($('#transaction_date_range').val()){

                        start_date = $('input#transaction_date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');

                        end   = $('input#transaction_date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');

                    }
                    $.ajax({
                        url: '/get-previous-move',
                        dataType: 'html',
                        data:{
                             
                          start_date,
                          product_id,
                            
                        },
                        success: function(result) {
                            
                            $('#previous').html(    "Previous Unit Cost :\n   " + JSON.parse(result)["cost"] + "   ");
                            $('#qty').html(    "Previous Qty  : \n   " +  JSON.parse(result)["qty"] + " ( " + JSON.parse(result)["signal"] + " ) "  );
                        },
                    });
                    
                }
                add_attribute();
                
            }

        });
        $(document).on("change","#date,#unit_product_id",function(){
            item_move.ajax.reload();
        });

        function add_attribute(){

            var table = $('#item_move').DataTable();
        
            // Loop through each row in the table
            table.rows().every(function() {
                // Get the DOM element of the row
                var rowNode = this.node();
                // console.log($(rowNode));
                $(rowNode).find(".hi").addClass('display_currency');
                $(rowNode).find(".hi").attr('data-currency_symbol', true );
            
                
            });
        }
    </script>
@endsection