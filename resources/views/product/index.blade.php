@extends('layouts.app')
{{-- *1* --}}
@section('title', __('sale.products'))
{{-- *2* --}}
@section('content')
    <!-- Content Header (Page header) -->
    {{-- section title of page --}}
    {{-- ************************************************ --}}
        <section class="content-header">
            <h1>
            <b>@lang('sale.products')</b>
            <b><small>@lang('lang_v1.manage_products')</small><b>
                <h5><i><b>{{ "   Products  >  " }} </b>{{ "List Product"   }} <b> {{"   "}} </b></i></h5>
                <br>    
            </h1>
        </section>
    {{-- ************************************************ --}}

    <!-- Main content -->
    {{-- section body of page --}}
    {{-- ************************************************ --}}
        <section class="content">
            
            {{-- *1* section currency --}}
            {{-- ********************************************** --}}
            <!-- Page level currency setting -->
            <input type="hidden" id="p_code" value="{{$currency_details->code}}">
            <input type="hidden" id="p_symbol" value="{{$currency_details->symbol}}">
            <input type="hidden" id="p_thousand" value="{{$currency_details->thousand_separator}}">
            <input type="hidden" id="p_decimal" value="{{$currency_details->decimal_separator}}">
            {{-- ********************************************** --}}
            
            
            {{-- *2* section filter --}}
            {{-- ****************************************************** --}}
            <div class="row">
                <div class="col-md-12"> 
                         
                        @component('components.filters', ['class' => "box-primary",'title' => __('report.filters')])
                            <div class="col-md-4 hide" >
                                <div class="form-group">
                                    <form action="{{URL::to('/product/import-image')}}" method="POST" enctype="multipart/form-data" >
                                        @csrf
                                        <input type="file" name="file"/>
                                        <button type="submit">Import</button>
                                    </form>
                                </div>
                            </div>
                            <div class="col-md-4" >
                                <div class="form-group">
                                    {!! Form::label('product_name', __('product.name') . ':') !!}
                                    {!! Form::select('product_name', $list_product , null, ['class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'product_name', 'placeholder' => __('lang_v1.all')]); !!}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    {!! Form::label('category_id', __('product.category') . ':') !!}
                                    {!! Form::select('category_id', $categories, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'product_list_filter_category_id', 'placeholder' => __('lang_v1.all')]); !!}
                                </div>
                            </div>
                            <div class="col-md-4 hide" id="location_filter">
                                <div class="form-group">
                                    {!! Form::label('location_id',  __('purchase.business_location') . ':') !!}
                                    {!! Form::select('location_id', $business_locations, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
                                </div>
                            </div>
                            
                            <div class="col-md-4" >
                                <div class="form-group">
                                    {!! Form::label('type', __('product.product_type') . ':') !!}
                                    {!! Form::select('type', ['single' => __('lang_v1.single'), 'variable' => __('lang_v1.variable'), 'combo' => __('lang_v1.combo')], null, ['class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'product_list_filter_type', 'placeholder' => __('lang_v1.all')]); !!}
                                </div>
                            </div>
                            <div class="col-md-4" >
                                <div class="form-group">
                                    {!! Form::label('product_sku',__('product.sku'). ':') !!}
                                    {!! Form::select('product_sku', $products_code, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'product_list_filter_product_sku', 'placeholder' => __('lang_v1.all')]); !!}
                                </div>
                            </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        {!! Form::label('sub_category_id', __('product.sub_category') . ':') !!}
                                    {!! Form::select('sub_category_id', $sub_cat, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'product_list_filter_sub_category_id', 'placeholder' => __('lang_v1.all')]); !!}
                                </div>
                            </div>
                            <div class="col-md-4"  >
                                <div class="form-group">
                                    {!! Form::label('unit_id', __('product.unit') . ':') !!}
                                    {!! Form::select('unit_id', $units, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'product_list_filter_unit_id', 'placeholder' => __('lang_v1.all')]); !!}
                                </div>
                            </div>
                            <div class="col-md-4" >
                                <div class="form-group">
                                    {!! Form::label('tax_id', __('product.tax') . ':') !!}
                                    {!! Form::select('tax_id', $taxes, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'product_list_filter_tax_id', 'placeholder' => __('lang_v1.all')]); !!}
                                </div>
                            </div>
                            <div class="col-md-4" >
                                <div class="form-group">
                                    {!! Form::label('brand_id', __('product.brand') . ':') !!}
                                    {!! Form::select('brand_id', $brands, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'product_list_filter_brand_id', 'placeholder' => __('lang_v1.all')]); !!}
                                </div>
                            </div>
                            <div class="col-md-4" >
                                <div class="form-group">
                                    {!! Form::label('active_state', __('product.active_state') . ':') !!}
                                    {!! Form::select('active_state', ['active' => __('business.is_active'), 'inactive' => __('lang_v1.inactive')], null, ['class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'active_state', 'placeholder' => __('lang_v1.all')]); !!}
                                </div>
                            </div>
                            <div class="col-md-4" >
                                <div class="form-group">
                                    {!! Form::label('image', __('lang_v1.image') . ':') !!}
                                    {!! Form::select('type', ['default' => 'Without Image', 'image' => __('lang_v1.image')], null, ['class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'product_list_filter_image', 'placeholder' => __('lang_v1.all')]); !!}
                                </div>
                            </div>
                            <div class="col-md-4" >
                                <div class="form-group">
                                    {!! Form::label('image',__('report.current_stock'). ':') !!}
                                    {!! Form::select('current_stock', ['zero' =>__('izo.zero'), 'gtzero' => __('izo.more_than_zero') ,'lszero' => __('izo.less_than_zero')], null, ['class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'product_list_filter_current_stock', 'placeholder' => __('lang_v1.all')]); !!}
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('price',__('lang_v1.default_selling_price'). ':') !!}
                                    <div class="input-group">
                                        <input type="text" class="form-control " id="default_selling_price" name="default_selling_price" value="">
                                        <span class="input-group-btn">
                                            <button type="button" class="btn btn-default bg-white btn-flat btn-modal"  title="@lang('unit.add_unit')" ><i class="fa fa-search text-primary fa-lg"></i></button>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3" >
                                <div class="form-group">
                                    <br>
                                    <label>
                                        {!! Form::checkbox('not_for_selling', 1, false, ['class' => 'input-icheck', 'id' => 'not_for_selling']); !!} <strong>@lang('lang_v1.not_for_selling')</strong>
                                    </label>
                                </div>
                            </div>

                            {{-- ssection additional filter --}}
                            {{-- *************************************************** --}}
                                <!-- include module filter -->
                                {{--@if(!empty($pos_module_data))
                                    @foreach($pos_module_data as $key => $value)
                                        @if(!empty($value['view_path']))
                                            @includeIf($value['view_path'], ['view_data' => $value['view_data']])
                                        @endif
                                    @endforeach
                                @endif--}}
                                {{-- @if($is_woocommerce)
                                    <div class="col-md-3" >
                                        <div class="form-group">
                                            <br>
                                            <label>
                                            {!! Form::checkbox('woocommerce_enabled', 1, false,
                                            [ 'class' => 'input-icheck', 'id' => 'woocommerce_enabled']); !!} {{ __('lang_v1.woocommerce_enabled') }}
                                            </label>
                                        </div>
                                    </div>
                                @endif --}}
                            {{-- *************************************************** --}}
                        @endcomponent 
                </div>
            </div>
            {{-- ****************************************************** --}}

            {{-- *3* section product --}}
            {{-- **************************************************** --}} 
                
                {{-- section create --}}
                {{-- **************************************************** --}}
                <div class="col-md-12 text-right">
                    @can('product.create') <a class="btn btn-primary  " href="{{action('ProductController@create')}}"> <i class="fa fa-plus"></i> @lang('product.add_new_product')</a> @endcan
                    <h5>&nbsp;</h5>
                </div>
                {{-- **************************************************** --}}
                

                {{-- section product table --}}
                {{-- **************************************************** --}}
                @can('product.view')
                        @include("product.partials.table_product")
                @elsecan("warehouse.views")
                        @include("product.partials.table_product")
                @elsecan("manufuctoring.views")
                        @include("product.partials.table_product")
                @elsecan("admin_supervisor.views")
                        @include("product.partials.table_product")
                @elsecan("admin_without.views")
                        @include("product.partials.table_product")
                @endcan
                {{-- **************************************************** --}}
                 
            {{-- **************************************************** --}}


            {{-- *4* permission section --}}
            {{-- ************************************************************* --}}
            @can("product.avarage_cost") <div class="can" hidden id="can">{{" "}}</div> @endcan
            @can("stock_report.view") <div class="can" hidden id="can">{{" "}}</div> @endcan
            <input type="hidden" id="is_rack_enabled" value="{{$rack_enabled}}">
            {{-- ************************************************************* --}}


            {{-- *5* section modal --}}
            {{-- ************************************************************* --}}
            <div class="modal fade product_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel"> </div>
            <div class="modal fade" id="view_product_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel"> </div>
            <div class="modal fade" id="opening_stock_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel"> </div>
            {{-- ************************************************************* --}}
            
            @include('product.partials.edit_product_location_modal')
            
        </section>
    {{-- ************************************************ --}}
    <!-- /.content -->
@endsection
{{-- *3* --}}
@section('javascript')
    {{-- *1* section rolations --}}
    {{-- ************************************************************* --}}
    <script src="{{ asset('js/producte.js?v=' . $asset_v) }}"></script>
    <script src="{{ asset('js/opening_stock.js?v=' . $asset_v) }}"></script>
    <script src="{{ asset('js/reportt.js?v=' . $asset_v) }}"></script>
    {{-- ************************************************************* --}}
    {{-- *2* section additional --}}
    {{-- ************************************************************* --}}
    <script type="text/javascript">
   
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
        var sub_category_id =  getUrlParameter('sub_category_id');
        $(document).ready( function(){
                product_table = $('#product_table').DataTable({
                        processing: true,
                        serverSide: true,
                        aaSorting: [[3, 'asc']],
                        scrollY:        "75vh",
                        scrollX:        true,
                        scrollCollapse: true,
                        
                        "lengthMenu": [10, 25, 50, 75, 100 ,200,1000],
                        
                        "ajax": {
                            "url": "/products?sub_category_id="+sub_category_id,
                            "data": function ( d ) {
                                d.type = $('#product_list_filter_type').val();
                                d.category_id = $('#product_list_filter_category_id').val();
                                d.brand_id = $('#product_list_filter_brand_id').val();
                                d.unit_id = $('#product_list_filter_unit_id').val();
                                d.tax_id = $('#product_list_filter_tax_id').val();
                                d.active_state = $('#active_state').val();
                                d.not_for_selling = $('#not_for_selling').is(':checked');
                                d.location_id = $('#location_id').val();
                                d.current_stock=$("#product_list_filter_current_stock").val();
                                d.image_type=$("#product_list_filter_image").val();
                                d.default_selling_price=$("#default_selling_price").val();
                                d.product_name=$("#product_name").val();
                                d.product_sku=$("#product_list_filter_product_sku").val();
                                d.sub_category_id=$("#product_list_filter_sub_category_id").val();


                                if ($('#repair_model_id').length == 1) {
                                    d.repair_model_id = $('#repair_model_id').val();
                                }

                                if ($('#woocommerce_enabled').length == 1 && $('#woocommerce_enabled').is(':checked')) {
                                    d.woocommerce_enabled = 1;
                                }

                                d = __datatable_ajax_callback(d);
                            }
                        },
                        columnDefs: [ {
                            "targets": [0, 1, 2],
                            "orderable": false,
                            "searchable": false
                        } ],
                        columns: [
                                { data: 'mass_delete'  },
                                { data: 'mass_deletes' ,class: 'hide'   },
                                { data: 'image', name: 'products.image'  },
                                { data: 'action', name: 'action'},
                                { data: 'product', name: 'products.name'  },
                                { data: 'product_locations', name: 'product_locations' ,class:'hide' },
                                @can('view_purchase_price')
                                    { data: 'purchase_price', name: 'max_purchase_price', searchable: false},
                                @endcan
                                @can('access_default_selling_price')
                                    { data: 'selling_price_Exc', name: 'max_price_Exc', searchable: false},
                                @endcan
                                @can('access_default_selling_price')
                                    { data: 'selling_price', name: 'max_price', searchable: false},
                                @endcan
                                { data: 'current_stock', searchable: false},
                                { data: 'type', name: 'products.type'},
                                { data: 'category', name: 'c1.name'},
                                { data: 'brand', name: 'brands.name'},
                                { data: 'tax', name: 'tax_rates.name', searchable: false},
                                { data: 'sku', name: 'products.sku'},
                                { data: 'product_description', name: 'products.product_description'},
                                { data: 'product_custom_field1', name: 'products.product_custom_field1'  },
                                { data: 'product_custom_field2', name: 'products.product_custom_field2'  },
                                { data: 'product_custom_field3', name: 'products.product_custom_field3'  },
                                { data: 'product_custom_field4', name: 'products.product_custom_field4'  }

                            ],
                            createdRow: function( row, data, dataIndex ) {
                                if($('input#is_rack_enabled').val() == 1){
                                    var target_col = 0;
                                    @can('product.delete')
                                        target_col = 1;
                                    @endcan
                                    $( row ).find('td:eq('+target_col+') div').prepend('<i style="margin:auto;" class="fa fa-plus-circle text-success cursor-pointer no-print rack-details" title="' + LANG.details + '"></i>&nbsp;&nbsp;');
                                }
                                $( row ).find('td:eq(0)').attr('class', 'selectable_td');
                            },
                            fnDrawCallback: function(oSettings) {
                                __currency_convert_recursively($('#product_table'));
                                
                                $(".e-commerce").each(function() {
                                    var  e = $(this); 
                                    e.on('change', function() {
                                        if(!e.attr("checked")){
                                            e.attr("checked",true)
                                        }else{
                                            e.attr("checked",false);
                                            $.ajax({
                                                url: "/products/unchangeFeature?id="+e.val(),
                                                method: 'get',
                                                dataType: 'html',
                                                success: function(result) {
                                                    
                                                },
                                            });
                                        }
                                        if(e.attr("checked")){
                                            
                                            $.ajax({
                                                url: "/products/changeFeature?id="+e.val(),
                                                method: 'get',
                                                dataType: 'html',
                                                success: function(result) {
                                                    
                                                },
                                            });
                                        }
                                    });
                                });
                                
                            },
                });
                 
            // Array to track the ids of the details displayed rows
            var detailRows = [];

            $('#product_table tbody').on( 'click', 'tr i.rack-details', function () {
                var i = $(this);
                var tr = $(this).closest('tr');
                var row = product_table.row( tr );
                var idx = $.inArray( tr.attr('id'), detailRows );

                if ( row.child.isShown() ) {
                    i.addClass( 'fa-plus-circle text-success' );
                    i.removeClass( 'fa-minus-circle text-danger' );

                    row.child.hide();

                    // Remove from the 'open' array
                    detailRows.splice( idx, 1 );
                } else {
                    i.removeClass( 'fa-plus-circle text-success' );
                    i.addClass( 'fa-minus-circle text-danger' );

                    row.child( get_product_details( row.data() ) ).show();

                    // Add to the 'open' array
                    if ( idx === -1 ) {
                        detailRows.push( tr.attr('id') );
                    }
                }
            });

            $(document).on('click', 'a.delete-product', function(e){
                e.preventDefault();
                swal({
                  title: LANG.sure,
                  icon: "warning",
                  buttons: true,
                  dangerMode: true,
                }).then((willDelete) => {
                    if (willDelete) {
                        var href = $(this).attr('href');
                        $.ajax({
                            method: "DELETE",
                            url: href,
                            dataType: "json",
                            success: function(result){
                                if(result.success == true){
                                    toastr.success(result.msg);
                                    product_table.ajax.reload();
                                } else {
                                    toastr.error(result.msg);
                                }
                            }
                        });
                    }
                });
            });
            $(document).on('click', 'a.delete_product_image', function(e){
                e.preventDefault();
                var id = $(this).data('id');
                swal({
                    title: LANG.sure,
                    icon: 'warning',
                    buttons: true,
                    dangerMode: true,
                }).then(willDelete => {
                    if (willDelete) {
                    $.ajax({
                        url:"/product/remove-image",
                        dataType:"html",
                        method:"GET",
                        data:{
                        id:id
                        },
                        success: function(result) {
                            toastr.success("Deleted Successfully");
                        },
                });
                    }
                });
            });
            
            $(document).on('click', '#delete-selected', function(e){
                e.preventDefault();
                var selected_rows = getSelectedRows();

                if(selected_rows.length > 0){
                    $('input#selected_rows').val(selected_rows);
                    swal({
                        title: LANG.sure,
                        icon: "warning",
                        buttons: true,
                        dangerMode: true,
                    }).then((willDelete) => {
                        if (willDelete) {
                            $('form#mass_delete_form').submit();
                        }
                    });
                } else{
                    $('input#selected_rows').val('');
                    swal('@lang("lang_v1.no_row_selected")');
                }
            });

            $(document).on('click', '#deactivate-selected', function(e){
                e.preventDefault();
                var selected_rows = getSelectedRows();

                if(selected_rows.length > 0){
                    $('input#selected_products').val(selected_rows);
                    swal({
                        title: LANG.sure,
                        icon: "warning",
                        buttons: true,
                        dangerMode: true,
                    }).then((willDelete) => {
                        if (willDelete) {
                            var form = $('form#mass_deactivate_form')

                            var data = form.serialize();
                                $.ajax({
                                    method: form.attr('method'),
                                    url: form.attr('action'),
                                    dataType: 'json',
                                    data: data,
                                    success: function(result) {
                                        if (result.success == true) {
                                            toastr.success(result.msg);
                                            product_table.ajax.reload();
                                            form
                                            .find('#selected_products')
                                            .val('');
                                        } else {
                                            toastr.error(result.msg);
                                        }
                                    },
                                });
                        }
                    });
                } else{
                    $('input#selected_products').val('');
                    swal('@lang("lang_v1.no_row_selected")');
                }
            })
            $(document).on('click', '#edit-selected', function(e){
                e.preventDefault();
                var selected_rows = getSelectedRows();

                if(selected_rows.length > 0){
                    $('input#selected_products_for_edit').val(selected_rows);
                    $('form#bulk_edit_form').submit();
                } else{
                    $('input#selected_products').val('');
                    swal('@lang("lang_v1.no_row_selected")');
                }
            })

            $('table#product_table tbody').on('click', 'a.activate-product', function(e){
                e.preventDefault();
                var href = $(this).attr('href');
                $.ajax({
                    method: "get",
                    url: href,
                    dataType: "json",
                    success: function(result){
                        if(result.success == true){
                            toastr.success(result.msg);
                            product_table.ajax.reload();
                        } else {
                            toastr.error(result.msg);
                        }
                    }
                });
            });
            $(document).on("change","#product_list_filter_category_id",function(e){
                var id = $("#product_list_filter_category_id").val();
                // $("#name_p").css({"outline":"0px solid red","box-shadow":"1px 1px 10px transparent"});
                $.ajax({
                    method: 'GET',
                    url: '/product/sub-main/' + id,
                    async: false,
                    data: {
                        main: id,
                    },
                    dataType: 'json',
                    success: function(result) {
                        if (result.success == true) {
                            $('#product_list_filter_sub_category_id').html("");
                            
                            html = '<option selected="selected" value >All</option>';
                            for( var r in result.array){
                                html += "<option value='"+r+"' >"+result.array[r]+"</option>";
                            }
                            $('#product_list_filter_sub_category_id').append(html);
                            // toastr.success(result.msg);
                        } else {
                            // toastr.error(result.msg);
                        }
                    }
                });
            });
            $(document).on('change', '#product_name,#product_list_filter_product_sku,#product_list_filter_sub_category_id,#default_selling_price,#product_list_filter_image,#product_list_filter_current_stock,#product_list_filter_current_stock,#product_list_filter_type, #product_list_filter_category_id, #product_list_filter_brand_id, #product_list_filter_unit_id, #product_list_filter_tax_id, #location_id, #active_state, #repair_model_id',
                function() {
                    if ($("#product_list_tab").hasClass('active')) {
                        product_table.ajax.reload();
                    }

                    if ($("#product_stock_report").hasClass('active')) {
                        stock_report_table.ajax.reload();
                    }
            });

            $(document).on('ifChanged', '#not_for_selling, #woocommerce_enabled', function(){
                if ($("#product_list_tab").hasClass('active')) {
                    product_table.ajax.reload();
                }
              

                if ($("#product_stock_report").hasClass('active')) {
                    stock_report_table.ajax.reload();
                }
            });

            $('#product_location').select2({dropdownParent: $('#product_location').closest('.modal')});
        });

        

        // $(document).on('shown.bs.modal', 'div.view_product_modal, div.view_modal',
        //     function(){
        //         var div = $(this).find('#view_product_stock_details');
        //     if (div.length) {
        //         $.ajax({
        //             url: "{{action('ReportController@getStockReport')}}"  + '?for=view_product&product_id=' + div.data('product_id'),
        //             dataType: 'html',
        //             success: function(result) {
        //                 div.html(result);
        //                 __currency_convert_recursively(div);
        //             },
        //         });
        //     }
        //     __currency_convert_recursively($(this));
        // });


        // var data_table_initailized = false;
        // $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        //     if ($(e.target).attr('href') == '#product_stock_report') {
        //         if (!data_table_initailized) {
        //             //Stock report table
        //             var stock_report_cols = [
        //                 { data: 'sku', name: 'variations.sub_sku' },
        //                 { data: 'product', name: 'p.name' },
        //                 { data: 'location_name', name: 'l.name' },
        //                 { data: 'unit_price', name: 'variations.sell_price_inc_tax' },
        //                 { data: 'stock', name: 'stock', searchable: false },
        //             ];
        //             if ($('th.stock_price').length) {
        //                 stock_report_cols.push({ data: 'stock_price', name: 'stock_price', searchable: false });
        //                 stock_report_cols.push({ data: 'stock_price_curr', name: 'stock_price_curr', searchable: false });
        //                 stock_report_cols.push({ data: 'stock_value_by_sale_price', name: 'stock_value_by_sale_price', searchable: false, orderable: false });
        //                 stock_report_cols.push({ data: 'stock_value_by_sale_price_curr', name: 'stock_value_by_sale_price_curr', searchable: false, orderable: false });
        //                 stock_report_cols.push({ data: 'potential_profit', name: 'potential_profit', searchable: false, orderable: false });
        //             }

        //             stock_report_cols.push({ data: 'total_sold', name: 'total_sold', searchable: false });
        //             stock_report_cols.push({ data: 'total_transfered', name: 'total_transfered', searchable: false });
        //             stock_report_cols.push({ data: 'total_adjusted', name: 'total_adjusted', searchable: false });

        //             if ($('th.current_stock_mfg').length) {
        //                 stock_report_cols.push({ data: 'total_mfg_stock', name: 'total_mfg_stock', searchable: false });
        //             }

        //             stock_report_table = $('#stock_report_table').DataTable({
        //                 processing: true,
        //                 serverSide: true,
        //                 ajax: {
        //                     url: '/reports/stock-report',
        //                     data: function(d) {
        //                         d.location_id = $('#location_id').val();
        //                         d.category_id = $('#category_id').val();
        //                         d.sub_category_id = $('#sub_category_id').val();
        //                         d.brand_id = $('#brand').val();
        //                         d.unit_id = $('#unit').val();
        //                         d.only_mfg_products = $('#only_mfg_products').length && $('#only_mfg_products').is(':checked') ? 1 : 0;
        //                     },
        //                 },
        //                 columns: stock_report_cols,
        //                 fnDrawCallback: function(oSettings) {
        //                     debugger;
        //                     $('#footer_total_stock').html(__sum_stock($('#stock_report_table'), 'current_stock'));
        //                     $('#footer_total_sold').html(__sum_stock($('#stock_report_table'), 'total_sold'));
        //                     $('#footer_total_transfered').html(
        //                         __sum_stock($('#stock_report_table'), 'total_transfered')
        //                     );
        //                     $('#footer_total_adjusted').html(
        //                         __sum_stock($('#stock_report_table'), 'total_adjusted')
        //                     );
        //                     var total_stock_price = sum_table_col($('#stock_report_table'), 'total_stock_price');
        //                     $('#footer_total_stock_price').text(total_stock_price);
        //                     var total_stock_price_curr = sum_table_col($('#stock_report_table'), 'total_stock_price_curr');
        //                     $('#footer_total_stock_price_curr').text(total_stock_price_curr);
        //                     var total_stock_value_by_sale_price = sum_table_col($('#stock_report_table'), 'stock_value_by_sale_price');
        //                     $('#footer_stock_value_by_sale_price').text(total_stock_value_by_sale_price);
        //                     var footer_stock_value_by_sale_price_curr= sum_table_col($('#stock_report_table'), 'footer_stock_value_by_sale_price_curr');
        //                     var total_potential_profit = sum_table_col($('#stock_report_table'), 'potential_profit');
        //                     $('#footer_potential_profit').text(total_potential_profit);

        //                     __currency_convert_recursively($('#stock_report_table'));
        //                     if ($('th.current_stock_mfg').length) {
        //                         $('#footer_total_mfg_stock').html(
        //                             __sum_stock($('#stock_report_table'), 'total_mfg_stock')
        //                         );
        //                     }
        //                 },
        //             });


        //             data_table_initailized = true;
        //         } else {
        //             stock_report_table.ajax.reload();
        //         }
        //     } else {
        //         product_table.ajax.reload();
        //     }
        // });

        $(document).on('click', '.update_product_location', function(e){
            e.preventDefault();
            var selected_rows = getSelectedRows();

            if(selected_rows.length > 0){
                $('input#selected_products').val(selected_rows);
                var type = $(this).data('type');
                var modal = $('#edit_product_location_modal');
                if(type == 'add') {
                    modal.find('.remove_from_location_title').addClass('hide');
                    modal.find('.add_to_location_title').removeClass('hide');
                } else if(type == 'remove') {
                    modal.find('.add_to_location_title').addClass('hide');
                    modal.find('.remove_from_location_title').removeClass('hide');
                }

                modal.modal('show');
                modal.find('#product_location').select2({ dropdownParent: modal });
                modal.find('#product_location').val('').change();
                modal.find('#update_type').val(type);
                modal.find('#products_to_update_location').val(selected_rows);
            } else{
                $('input#selected_products').val('');
                swal('@lang("lang_v1.no_row_selected")');
            }
        });

        $(document).on('submit', 'form#edit_product_location_form', function(e) {
            e.preventDefault();
            var form = $(this);
            var data = form.serialize();

            $.ajax({
                method: $(this).attr('method'),
                url: $(this).attr('action'),
                dataType: 'json',
                data: data,
                beforeSend: function(xhr) {
                    __disable_submit_button(form.find('button[type="submit"]'));
                },
                success: function(result) {
                    if (result.success == true) {
                        $('div#edit_product_location_modal').modal('hide');
                        toastr.success(result.msg);
                        product_table.ajax.reload();
                        $('form#edit_product_location_form')
                        .find('button[type="submit"]')
                        .attr('disabled', false);
                    } else {
                        toastr.error(result.msg);
                    }
                },
            });
        });
    </script>
    <script type="text/javascript">
         
        $(document).on('click', '#select-all-row', function(e) {
            if (this.checked) {

                    $('.row-select')
                    .each(function() {
                        if (!this.checked) {
                            $(this)
                                .prop('checked', true)
                                .change();
                        }
                    });
            } else {
                $('.row-select')
                    .each(function() {
                        if (this.checked) {
                            $(this)
                                .prop('checked', false)
                                .change();
                        }
                    });
            }
        });
    </script>
    {{-- ************************************************************* --}}
@endsection
