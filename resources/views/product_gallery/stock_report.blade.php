@extends('layouts.app')
@section('title', __('sale.products'))

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1><b>@lang('sale.products')</b>
            <small>@lang('lang_v1.manage_products')</small>
        </h1>
        <h5><i><b>{{ "   Inventory    >  " }} </b>{{ "Inventory Report"   }} <b> {{"   "}} </b></i></h5>  
        <br> 
    </section>

    <!-- Main content -->
    <section class="content"> 
            <!-- Page level currency setting -->
	<input type="hidden" id="p_code" value="{{$currency_details->code}}">
	<input type="hidden" id="p_symbol" value="{{$currency_details->symbol}}">
	<input type="hidden" id="p_thousand" value="{{$currency_details->thousand_separator}}">
	<input type="hidden" id="p_decimal" value="{{$currency_details->decimal_separator}}">
        <div class="row" style="margin:0px 10%">
            <div class="col-md-12">
                @component('components.filters', ['title' => __('report.filters') , 'class' =>  "box-primary"])
                    <div class="col-md-3 hide" id="location_filter">
                        <div class="form-group">
                            {!! Form::label('location_id',  __('purchase.business_location') . ':') !!}
                            {!! Form::select('location_id', $business_locations, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('type', __('product.product_type') . ':') !!}
                            {!! Form::select('type', ['single' => __('lang_v1.single'), 'variable' => __('lang_v1.variable'), 'combo' => __('lang_v1.combo')], null, ['class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'product_list_filter_type', 'placeholder' => __('lang_v1.all')]); !!}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('category_id', __('product.category') . ':') !!}
                            {!! Form::select('category_id', $categories, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'product_list_filter_category_id', 'placeholder' => __('lang_v1.all')]); !!}
                        </div>
                    </div>
                    <div class="col-md-3 " style="display: none " >
                        <div class="form-group">
                            {!! Form::label('unit_id', __('product.unit') . ':') !!}
                            {!! Form::select('unit_id', $units, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'product_list_filter_unit_id', 'placeholder' => __('lang_v1.all')]); !!}
                        </div>
                    </div>
                    @php $is_admin = auth()->user()->hasRole('Admin#' . session('business.id')) ? true : false; @endphp
                    @if(!$is_admin)
                        @can("SalesMan.views")
                            <div class="col-md-3 "  >
                                <div class="form-group">
                                    {!! Form::label('price', __('lang_v1.price') . ':') !!}
                                    {!! Form::select('price', ["0"=>__("home.sale_price")], null, ['class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'price', 'placeholder' => __('lang_v1.all')]); !!}
                                </div>
                            </div>
                        @else
                            <div class="col-md-3 "  >
                                <div class="form-group">
                                    {!! Form::label('price', __('lang_v1.price') . ':') !!}
                                    {!! Form::select('price', ["0"=>__("home.sale_price"),"1"=>__("home.cost_price")], null, ['class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'price', 'placeholder' => __('lang_v1.all')]); !!}
                                </div>
                            </div>
                        @endcan
                    @elseif( $is_admin ||  auth()->user()->can('product.avarage_cost'))
                        <div class="col-md-3 "  >
                            <div class="form-group">
                                {!! Form::label('price', __('lang_v1.price') . ':') !!}
                                {!! Form::select('price', ["0"=>__("home.sale_price"),"1"=>__("home.cost_price")], null, ['class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'price', 'placeholder' => __('lang_v1.all')]); !!}
                            </div>
                        </div>
                    @endif
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('product_available', __('lang_v1.Available') . ':') !!}
                            {!! Form::select('product_available', [ -1 =>__("all"),0=>__("lang_v1.Not Available"),1=>__("lang_v1.Available"),2=>__("recieved.should_delivery"),3=>__("recieved.should_recieved")], null, ['class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'product_available' ]); !!}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('store_id', __('home.store') . ':') !!}
                            {!! Form::select('store_id',$store, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'store_id', 'placeholder' => __('lang_v1.all')]); !!}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('main_store', __('home.main_store') . ':') !!}
                            {!! Form::select('main_store',$main_store, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'main_store', 'placeholder' => __('lang_v1.all')]); !!}
                        </div>
                    </div>
                    <div class="col-md-3" style="display: none " >
                        <div class="form-group">
                            {!! Form::label('tax_id', __('product.tax') . ':') !!}
                            {!! Form::select('tax_id', $taxes, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'product_list_filter_tax_id', 'placeholder' => __('lang_v1.all')]); !!}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('brand_id', __('product.brand') . ':') !!}
                            {!! Form::select('brand_id', $brands, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'product_list_filter_brand_id', 'placeholder' => __('lang_v1.all')]); !!}
                        </div>
                    </div>

                    <div class="col-md-3"  >
                        <div class="form-group">
                            {!! Form::label('pricegroup', __('lang_v1.selling_price_group') . ':') !!}
                            {!! Form::select('pricegroup', $price_groups, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'pricegroup']); !!}
                        </div>
                    </div>
                    <div class="col-md-3"  >
                        <div class="form-group">
                            {!! Form::label('until_date', __('lang_v1.until_date') . ':') !!}
                            {!! Form::date('until_date', null, ['class' => 'form-control ', 'style' => 'width:100%', 'id' => 'until_date']); !!}
                        </div>
                    </div>

                    <div class="col-md-3" style="display: none " >
                        <br>
                        <div class="form-group">
                            {!! Form::select('active_state', ['active' => __('business.is_active'), 'inactive' => __('lang_v1.inactive')], null, ['class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'active_state', 'placeholder' => __('lang_v1.all')]); !!}
                        </div>
                    </div>
                    
                    <div class="col-md-3" style="display: none " >
                        <div class="form-group">
                            {!! Form::label('image', __('lang_v1.image') . ':') !!}
                            {!! Form::select('type', ['default' => __('home.without_image'), 'image' => __('lang_v1.image')], null, ['class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'product_list_filter_image', 'placeholder' => __('lang_v1.all')]); !!}
                        </div>
                    </div>

                    <div class="col-md-3" style="display: none " >
                        <div class="form-group">
                            {!! Form::label('image',__('report.current_stock'). ':') !!}
                            {!! Form::select('current_stock', ['zero' =>'Zero', 'gtzero' => __("home.more_than_zero"),'lszero' => __("home.less_than_zero")], null, ['class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'product_list_filter_current_stock', 'placeholder' => __('lang_v1.all')]); !!}
                        </div>
                    </div>
                    <div class="col-md-3"  style="display: none " >
                        <div class="form-group">
                            {!! Form::label('date',__('lang_v1.date'). ':') !!}
                            {!! Form::date('date', null, ['class' => 'form-control ' , 'id'=>'due_date'  ]); !!}
                        </div>
                    </div>

                    <!-- include module filter -->
                   {{-- @if(!empty($pos_module_data))
                        @foreach($pos_module_data as $key => $value)
                            @if(!empty($value['view_path']))
                                @includeIf($value['view_path'], ['view_data' => $value['view_data']])
                            @endif
                        @endforeach
                    @endif--}}

                    <div class="col-md-3" style="display: none " >
                        <div class="form-group">
                            <br>
                            <label>
                                {!! Form::checkbox('not_for_selling', 1, false, ['class' => 'input-icheck', 'id' => 'not_for_selling']); !!} <strong>@lang('lang_v1.not_for_selling')</strong>
                            </label>
                        </div>
                    </div>
                    @if($is_woocommerce)
                        <div class="col-md-3" style="display: none " >
                            <div class="form-group">
                                <br>
                                <label>
                                    {!! Form::checkbox('woocommerce_enabled', 1, false,
                                    [ 'class' => 'input-icheck', 'id' => 'woocommerce_enabled']); !!} {{ __('lang_v1.woocommerce_enabled') }}
                                </label>
                            </div>
                        </div>
                    @endif

                    <div class="col-md-3"  >
                        <div class="form-group">
                            <label>@lang('lang_v1.search')</label>
                            <div>
                                <input type="text" name="productname" id="productname" class="form-control" style="width: 80%;float: left; ">
                                <button type="button" class="btn-search"><i class="fa fa-search" ></i></button>
                            </div>
                        </div>
                    </div>




                   {{-- <div class="col-md-12">
                        <div class="mt-15">
                            @can('product.create')
                                <a class="btn btn-primary  " href="{{action('ProductController@create')}}">
                                    <i class="fa fa-plus"></i> @lang('product.add_new_product')</a>
                            @endcan
                        </div>
                    </div>--}}
                @endcomponent
            </div>
        </div>

        


        <input type="hidden" id="rem" name="rem" value="true" >
        <input type="hidden" id="offset" name="offset" value="0" >
        {{-- <div class="main-prduct container" id="products">


        </div> --}}

        <div class="loader " id="loader"></div>





    </section>
    <div class="row" style="margin:0px 10% ;background-color:#ffffff">
        <div class="col-sm-12">
         <section class="content" style="width:100% !important">
                <table class="table table-stripted table-bordered dataTable" id="stock_product" style="width:100% !important">
                    <thead>
                        <tr>
                            <th>@lang('Image')</th>
                            <th>@lang('Code')</th>
                            <th>@lang('product.product_name')</th>
                            <th>@lang('purchase.qty_current')</th>
                            <th>@lang('Actual Qty')</th>
                            <th>@lang('Over Qty')</th>
                            <th>@lang('lang_v1.price')</th>
                            <th>@lang('sale.total')</th>
                            <th>@lang('recieved.should_recieved')</th>
                            <th>@lang('recieved.should_delivery')</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <td colspan="3"></td>
                            <td class="footer_total_qty" id="footer_total_qty"></td>
                            <td class="footer_total_qty_actual" id="footer_total_qty_actual"></td>
                            <td class="footer_total_qty_over" id="footer_total_qty_over"></td>
                            <td></td>
                            <td class="footer_total_amount" id="footer_total_amount"></td>
                            <td></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </section>
        </div>
     <!-- /.content -->

@endsection

@section('javascript')
    <script src="{{ asset('js/producte.js?v=' . $asset_v) }}"></script>
    <script src="{{ asset('js/opening_stock.js?v=' . $asset_v) }}"></script>

     <script src="{{ asset('js/common.js?v=' . $asset_v) }}"></script>

    <script>
        $(document).ready( function() {
            product_stock = $("#stock_product").DataTable({
            processing: true,
            serverSide: true,
            buttons: [
            { extend: 'print', footer: true, title: 'Impression', exportOptions: { columns: ':visible' ,stripHtml: false } ,
                customize: function ( win ) {
                        $(win.document.body).css( 'font-size', '10pt' );
                /*            .prepend(
                                '<img src="http://datatables.net/media/images/logo-fade.png" style="position:absolute; top:0; left:0;" />'    
                            ); */
                            $(win.document.body).find('img').each(function(){
                                e = $(this);
                                image = e.parent().find('img');
                                img = "<img src='"+image.attr("src")+"' style='width: 100px;height: 100px'>";
                                e.parent().parent().html(img);
                                e.parent().parent().css( 'width', '100px' );
                                
                            });
                             
                        //   parent.parent()
                        //     .html(image)
                        //     .css( 'font-size', 'inherit' );
                    } },
            { extend: 'pdfHtml5', title: 'PDF', download: 'open', exportOptions: { columns: ':visible' } },
            { extend: 'copyHtml5', exportOptions: { columns: ':visible' } },
            { extend: 'csvHtml5', title: 'CSV', exportOptions: { columns: ':visible' } },
            { extend: 'excelHtml5', title: 'Excel', exportOptions: { columns: ':visible' } },
            // { extend: 'columnVisibility', text: 'Toutes', visibility: true },
            { extend: 'colvis', text: 'Visibilité' }
            ],
            "ajax": {
                "url": "/gallery/stock_report/table", 
                "data": function ( d ) {
                        d.type= $('#product_list_filter_type').val(),
                        d.category_id = $('#product_list_filter_category_id').val(),
                        d.brand_id = $('#product_list_filter_brand_id').val(),
                        d.unit_id = $('#product_list_filter_unit_id').val(),
                        d.until_date = $('#until_date').val(),
                        d.tax_id = $('#product_list_filter_tax_id').val(),
                        d.active_state = $('#active_state').val(),
                        d.price = $('#price').val(),
                        d.date = $('#date').val(),
                        d.main_store = $('#main_store').val(),
                        d.store_id = $('#store_id').val(),
                        d.product_available = $('#product_available').val(),
                        d.not_for_selling = $('#not_for_selling').is(':checked'),
                        d.location_id = $('#location_id').val(),
                        d.current_stock=$("#product_list_filter_current_stock").val(),
                        d.image_type=$("#product_list_filter_image").val(),
                        d.productname=$('#productname').val(),
                        d.pricegroup=$('#pricegroup').val()
                        d = __datatable_ajax_callback(d);
                }
            },
            aaSorting:[[1,"desc"]],
            columns: [
                { data: 'image' , name: 'image',orderable: false, "searchable": false  },
                { data: 'code', name: 'code' ,orderable: true, "searchable": true},
                { data: 'name', name: 'name' ,orderable: true, "searchable": true},
                { data: 'qty', name: 'qty' ,orderable: true, "searchable": true},
                { data: 'actual_qty', name: 'actual_qty' ,orderable: true, "searchable": true},
                { data: 'over_qty', name: 'over_qty' ,orderable: true, "searchable": true},
                { data: 'unit_price', name: 'unit_price',orderable: true, "searchable": true },
                { data: 'total_price', name: 'total_price' ,orderable: true, "searchable": true},
                { data: 'should_received', name: 'should_received' ,orderable: true, "searchable": true,orderable: true, "searchable": true},
                { data: 'should_delivered', name: 'should_delivered' ,orderable: true, "searchable": true},
             ],
            "fnDrawCallback": function (oSettings) {
                __currency_convert_recursively($('#stock_product'));
            },
            "footerCallback": function ( row, data, start, end, display ) {
                var  footer_total = 0;
                var  footer_total_price  = 0;
                var  footer_total_qty_actual  = 0;
                var  footer_total_qty_over  = 0;
                for (var r in data){
                    footer_total += data[r].qty;
                    footer_total_qty_actual += data[r].actual_qty;
                    footer_total_qty_over += data[r].over_qty;
                    footer_total_price +=  data[r].total_price;
                  } 
                $('.footer_total_qty').html(footer_total.toFixed(2));
                $('.footer_total_qty_actual').html(footer_total_qty_actual.toFixed(2));
                $('.footer_total_qty_over').html(footer_total_qty_over.toFixed(2));
                $('.footer_total_amount').html(__currency_trans_from_en(footer_total_price));
            },
        });
    });
    
    $('#pricegroup,#location_id ,#product_list_filter_type,#main_store,#store_id,#date,#product_available,#price,#until_date,#product_list_filter_category_id,#product_list_filter_brand_id').change(function () {
        // getproducts();
        product_stock.ajax.reload();
    });

    $('#productname').keyup(function () {
        // getproducts();

        product_stock.ajax.reload();
    });

    function getproducts() {
            $.ajax({
            url: "/gallery/stock_report",
            type: 'GET',
            data: {
                type: $('#product_list_filter_type').val(),
                category_id : $('#product_list_filter_category_id').val(),
                brand_id : $('#product_list_filter_brand_id').val(),
                unit_id : $('#product_list_filter_unit_id').val(),
                until_date : $('#until_date').val(),
                tax_id : $('#product_list_filter_tax_id').val(),
                active_state : $('#active_state').val(),
                price : $('#price').val(),
                date : $('#date').val(),
                main_store : $('#main_store').val(),
                store_id : $('#store_id').val(),
                product_available : $('#product_available').val(),
                not_for_selling : $('#not_for_selling').is(':checked'),
                location_id : $('#location_id').val(),
                current_stock:$("#product_list_filter_current_stock").val(),
                image_type:$("#product_list_filter_image").val(),
                productname:$('#productname').val(),
                pricegroup:$('#pricegroup').val()
            },
            success: function (data) {
                var products=document.getElementById("products");
                products.innerHTML =data['product'];
                final();

            }
        });

        
        
    }
    $('#loader').addClass('hidden');

    function final(){
        var total = 0;
            $("#total_price").each(()=>{
            var e = $(this).val();
            total += e;
        });
        alert(total);
    }
    
    
    </script>

@endsection