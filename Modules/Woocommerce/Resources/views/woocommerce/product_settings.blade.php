@extends('layouts.app')
@section('title', __('woocommerce::lang.product_settings'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('woocommerce::lang.product_settings')</h1>
    <h5><i><b>{{ "   Ecommerce  >  " }} </b>{{ " Product Setting"   }} <b> {{"   "}} </b></i></h5>
    <br>
</section>

<!-- Main content -->
<section class="content">
    {!! Form::open(['action' => '\Modules\Woocommerce\Http\Controllers\WoocommerceController@updateProductSettings', 'method' => 'post']) !!}
    <div class="row">
        <div class="col-xs-12">
           <!--  <pos-tab-container> -->
            <div class="col-xs-12  ">
                {{--    name    --}}
                {{-- ********** --}}
                {{-- <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12  ">
                      @component("components.widget",['class'=>'box-primary','title'=>__('Feature  Section') ])
                        <div class="choose_type ">
                            <div class="row"  >
                                <div class="col-md-4"></div>
                                <div class="col-md-4" >
                                    <div class="form-group">
                                        <div class="ft-by-name">
                                            {!! Form::checkbox("ft-filter-name",    1  , 0  , [ 'class' => 'ft-filter-by-name input-icheck']); !!} {{ __( 'Filter name of product' ) }}
                                        </div>
                                        <br>
                                        <div class="ft-by-category">
                                            {!! Form::checkbox("ft-filter-cat",    1  , 0  , [ 'class' => 'ft-filter-by-category input-icheck']); !!} {{ __( 'Filter by categories' ) }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4"></div>
                            </div>
                        </div> 
                        <div class="filter-item">
                            <div class="ft-name hide text-center">
                                    <div class="row" style="padding:20px;margin:20px 10px;border:1px solid #00000034;border-radius:10px;">
                                        <div class="col-md-4"></div>
                                        <div class="col-md-4" >
                                            <div class="form-group">
                                                {!! Form::label('ft-product_name', __('product.name') . ':') !!}
                                                {!! Form::select('ft-product_name', $products , null, ['class' => 'ft-product_name form-control select2', 'style' => 'width:100%', 'id' => 'product_name', 'placeholder' => __('lang_v1.all')]); !!}
                                            </div>
                                        </div>
                                        <div class="col-md-4"></div>
                                    </div>
                            </div>
                            <div class="ft-cat hide ">
                                    <div class="row" style="padding:20px;margin:20px 10px;border:1px solid #00000034;border-radius:10px;">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                {!! Form::label('ft-category_id', __('product.category') . ':') !!}
                                                {!! Form::select('ft-category_id', $category, null, ['class' => ' ft-category_id form-control select2', 'style' => 'width:100%', 'id' => 'product_list_filter_category_id', 'placeholder' => __('lang_v1.all')]); !!}
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                {!! Form::label('ft-sub_category_id', __('product.sub_category') . ':') !!}
                                                {!! Form::select('ft-sub_category_id', $sub_category, null, ['class' => ' ft-sub_category_id form-control select2', 'style' => 'width:100%', 'id' => 'product_list_filter_sub_category_id', 'placeholder' => __('lang_v1.all')]); !!}
                                            </div>
                                        </div>
                                        <div class="col-md-4" >
                                            <div class="form-group">
                                                {!! Form::label('ft-brand_id', __('product.brand') . ':') !!}
                                                {!! Form::select('ft-brand_id', $brands, null, ['class' => 'ft-brand_id form-control select2', 'style' => 'width:100%', 'id' => 'product_list_filter_brand_id', 'placeholder' => __('lang_v1.all')]); !!}
                                            </div>
                                        </div>
                                    </div>
                            </div>
                        </div> 
                        <div class="products" style="width:100%">
                            <table class="table table-striped table-bordered dataTable" style="width:100%" id="table_feature_product">
                                <thead>
                                    <tr>
                                        <th style="background-color:#00000034">Action</th>
                                        <th style="background-color:#00000034">Name</th>
                                        <th style="background-color:#00000034">Code</th>
                                        <th style="background-color:#00000034">Description</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                             
                        </div>
                      @endcomponent
                </div> --}}
                {{--  discount  --}}
                {{-- ********** --}}
                {{-- <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12  ">
                      @component("components.widget",['class'=>'box-primary','title'=>__('Discount  Section') ])
                        <div class="choose_type ">
                            <div class="row"  >
                                <div class="col-md-4"></div>
                                <div class="col-md-4" >
                                    <div class="form-group">
                                        <div class="dis-by-name">
                                            {!! Form::checkbox("dis-filter-name",    1  , 0  , [ 'class' => 'dis-filter-by-name input-icheck']); !!} {{ __( 'Filter name of product' ) }}
                                        </div>
                                        <br>
                                        <div class="dis-by-category">
                                            {!! Form::checkbox("dis-filter-cat",    1  , 0  , [ 'class' => 'dis-filter-by-category input-icheck']); !!} {{ __( 'Filter by categories' ) }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4"></div>
                            </div>
                        </div> 
                        <div class="filter-item">
                            <div class="dis-name hide text-center">
                                    <div class="row" style="padding:20px;margin:20px 10px;border:1px solid #00000034;border-radius:10px;">
                                        <div class="col-md-4"></div>
                                        <div class="col-md-4" >
                                            <div class="form-group">
                                                {!! Form::label('dis-product_name', __('product.name') . ':') !!}
                                                {!! Form::select('dis-product_name', $products , null, ['class' => 'dis-product_name form-control select2', 'style' => 'width:100%', 'id' => 'product_name', 'placeholder' => __('lang_v1.all')]); !!}
                                            </div>
                                        </div>
                                        <div class="col-md-4"></div>
                                    </div>
                            </div>
                            <div class="dis-cat hide ">
                                    <div class="row" style="padding:20px;margin:20px 10px;border:1px solid #00000034;border-radius:10px;">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                {!! Form::label('dis-category_id', __('product.category') . ':') !!}
                                                {!! Form::select('dis-category_id', $category, null, ['class' => 'dis-category_id form-control select2', 'style' => 'width:100%', 'id' => 'product_list_filter_category_id', 'placeholder' => __('lang_v1.all')]); !!}
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                {!! Form::label('dis-sub_category_id', __('product.sub_category') . ':') !!}
                                                {!! Form::select('dis-sub_category_id', $sub_category, null, ['class' => 'dis-sub_category_id form-control select2', 'style' => 'width:100%', 'id' => 'product_list_filter_sub_category_id', 'placeholder' => __('lang_v1.all')]); !!}
                                            </div>
                                        </div>
                                        <div class="col-md-4" >
                                            <div class="form-group">
                                                {!! Form::label('dis-brand_id', __('product.brand') . ':') !!}
                                                {!! Form::select('dis-brand_id', $brands, null, ['class' => 'dis-brand_id form-control select2', 'style' => 'width:100%', 'id' => 'product_list_filter_brand_id', 'placeholder' => __('lang_v1.all')]); !!}
                                            </div>
                                        </div>
                                    </div>
                            </div>
                        </div> 
                        <div class="products" style="width:100%">
                            <table class="table table-striped table-bordered dataTable" style="width:100%" id="table_discount_product">
                                <thead>
                                    <tr>
                                        <th style="background-color:#00000034">Action</th>
                                        <th style="background-color:#00000034">Name</th>
                                        <th style="background-color:#00000034">Code</th>
                                        <th style="background-color:#00000034">Description</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                             
                        </div>
                      @endcomponent
                </div> --}}
                {{-- collection --}}
                {{-- ********** --}}
                {{-- <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12  ">
                      @component("components.widget",['class'=>'box-primary','title'=>__('Collection  Section') ])
                      <div class="choose_type">
                        <div class="row"  >
                            <div class="col-md-4"></div>
                            <div class="col-md-4" >
                                <div class="form-group">
                                    <div class="cl-by-name">
                                    {!! Form::checkbox("cl-filter-name",    1  , 0  , [ 'class' => 'cl-filter-by-name input-icheck']); !!} {{ __( 'Filter name of product' ) }}
                                </div>
                                <br>
                                    <div class="cl-by-category">
                                        {!! Form::checkbox("cl-filter-cat",    1  , 0  , [ 'class' => 'cl-filter-by-category input-icheck']); !!} {{ __( 'Filter by categories' ) }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4"></div>
                        </div>
                      </div> 
                      <div class="filter-item">
                          <div class="cl-name hide text-center">
                                <div class="row" style="padding:20px;margin:20px 10px;border:1px solid #00000034;border-radius:10px;">
                                    <div class="col-md-4"></div>
                                    <div class="col-md-4" >
                                        <div class="form-group">
                                            {!! Form::label('cl-product_name', __('product.name') . ':') !!}
                                            {!! Form::select('cl-product_name',$products , null, ['class' => 'cl-product_name form-control select2', 'style' => 'width:100%', 'id' => 'product_name', 'placeholder' => __('lang_v1.all')]); !!}
                                        </div>
                                    </div>
                                    <div class="col-md-4"></div>
                                </div>
                          </div>
                          <div class="cl-cat hide ">
                                <div class="row" style="padding:20px;margin:20px 10px;border:1px solid #00000034;border-radius:10px;">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            {!! Form::label('cl-category_id', __('product.category') . ':') !!}
                                            {!! Form::select('cl-category_id', $category, null, ['class' => 'cl-category_id form-control select2', 'style' => 'width:100%', 'id' => 'product_list_filter_category_id', 'placeholder' => __('lang_v1.all')]); !!}
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            {!! Form::label('cl-sub_category_id', __('product.sub_category') . ':') !!}
                                            {!! Form::select('cl-sub_category_id', $sub_category, null, ['class' => 'cl-sub_category_id form-control select2', 'style' => 'width:100%', 'id' => 'product_list_filter_sub_category_id', 'placeholder' => __('lang_v1.all')]); !!}
                                        </div>
                                    </div>
                                    <div class="col-md-4" >
                                        <div class="form-group">
                                            {!! Form::label('cl-brand_id', __('product.brand') . ':') !!}
                                            {!! Form::select('cl-brand_id', $brands, null, ['class' => 'cl-brand_id form-control select2', 'style' => 'width:100%', 'id' => 'product_list_filter_brand_id', 'placeholder' => __('lang_v1.all')]); !!}
                                        </div>
                                    </div>
                                </div>
                          </div>
                      </div> 
                      <div class="products" style="width:100%">
                            <table class="table table-striped table-bordered dataTable" style="width:100%" id="table_collection_product">
                                <thead>
                                    <tr>
                                        <th style="background-color:#00000034">Action</th>
                                        <th style="background-color:#00000034">Name</th>
                                        <th style="background-color:#00000034">Code</th>
                                        <th style="background-color:#00000034">Description</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                             
                        </div>
                      @endcomponent
                </div> --}}
                {{--    ALL name    --}}
                {{-- ********** --}}
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12  ">
                    @component("components.widget",['class'=>'box-primary','title'=>__('Product Setting') ])
                      <div class="choose_type ">
                          <div class="row"  >
                              <div class="col-md-4"></div>
                              <div class="col-md-4 hide" >
                                  <div class="form-group">
                                      <div class="fa-by-name">
                                          {!! Form::checkbox("fa-filter-name",    1  , 0  , [ 'class' => 'fa-filter-by-name input-icheck']); !!} {{ __( 'Filter name of product' ) }}
                                      </div>
                                      <br>
                                      <div class="fa-by-category">
                                          {!! Form::checkbox("fa-filter-cat",    1  , 0  , [ 'class' => 'fa-filter-by-category input-icheck']); !!} {{ __( 'Filter by categories' ) }}
                                      </div>
                                  </div>
                              </div>
                              <div class="col-md-4"></div>
                          </div>
                      </div> 
                      <div class="filter-item">
                          <div class="fa-name   text-center">
                                  <div class="row" style="padding:20px;margin:20px 10px;border:1px solid #00000034;border-radius:10px;">
                                      <div class="col-md-4"></div>
                                      <div class="col-md-4" >
                                          <div class="form-group">
                                              {!! Form::label('fa-product_name', __('product.name') . ':') !!}
                                              {!! Form::select('fa-product_name', $products , null, ['class' => 'fa-product_name form-control select2', 'style' => 'width:100%', 'id' => 'product_name', 'placeholder' => __('lang_v1.all')]); !!}
                                          </div>
                                      </div>
                                      <div class="col-md-4"></div>
                                  </div>
                          </div>
                          <div class="fa-cat   ">
                                  <div class="row" style="padding:20px;margin:20px 10px;border:1px solid #00000034;border-radius:10px;">
                                      <div class="col-md-4">
                                          <div class="form-group">
                                              {!! Form::label('fa-category_id', __('product.category') . ':') !!}
                                              {!! Form::select('fa-category_id', $category, null, ['class' => ' fa-category_id form-control select2', 'style' => 'width:100%', 'id' => 'product_list_filter_category_id', 'placeholder' => __('lang_v1.all')]); !!}
                                          </div>
                                      </div>
                                      <div class="col-md-4">
                                          <div class="form-group">
                                              {!! Form::label('fa-sub_category_id', __('product.sub_category') . ':') !!}
                                              {!! Form::select('fa-sub_category_id', $sub_category, null, ['class' => ' fa-sub_category_id form-control select2', 'style' => 'width:100%', 'id' => 'product_list_filter_sub_category_id', 'placeholder' => __('lang_v1.all')]); !!}
                                          </div>
                                      </div>
                                      <div class="col-md-4" >
                                          <div class="form-group">
                                              {!! Form::label('fa-brand_id', __('product.brand') . ':') !!}
                                              {!! Form::select('fa-brand_id', $brands, null, ['class' => 'fa-brand_id form-control select2', 'style' => 'width:100%', 'id' => 'product_list_filter_brand_id', 'placeholder' => __('lang_v1.all')]); !!}
                                          </div>
                                      </div>
                                  </div>
                          </div>
                      </div> 
                      <div class="products" style="width:100%">
                          <table class="table table-striped table-bordered dataTable" style="width:100%" id="table_product">
                              <thead>
                                  <tr>
                                      <th   style="background-color:#00000034">Action</th>
                                      <th   style="background-color:#00000034">Name</th>
                                      <th   style="background-color:#00000034">Code</th>
                                      <th   style="background-color:#00000034">Description</th>
                                      <th   style="background-color:#00000034">Type</th>
                                  </tr>
                              </thead>
                              <tbody>

                              </tbody>
                              <footer>
                                 <tr>
                                    <td class="footer_left" ></td>
                                    <td class="footer_left" ></td>
                                    <td class="footer_left" ></td>
                                    <td class="footer_left" ></td>
                                    <td class="footer_left" ></td>
                                 </tr>
                              </footer>
                          </table>
                           
                      </div>
                    @endcomponent
              </div>
            </div>
            <!--  </pos-tab-container> -->
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <div class="form-group pull-right">
            {{Form::submit('update', ['class'=>"btn btn-danger"])}}
            </div>
        </div>
    </div>
    


    {!! Form::close() !!}
</section>
@stop
@section('javascript')
    <script type="text/javascript">
        // .. Feature
            $(document).on('ifChecked', 'input.ft-filter-by-name', function() {
                    $('.ft-name').removeClass("hide");
                    $('.ft-by-category').addClass("hide");
            });
            $(document).on('ifUnchecked', 'input.ft-filter-by-name', function() {
                    $('.ft-name').addClass("hide");
                    $('.ft-by-category').removeClass("hide");
            });
            $(document).on('ifChecked', 'input.ft-filter-by-category', function() {
                    $('.ft-cat').removeClass("hide");
                    $('.ft-by-name').addClass("hide");
            });
            $(document).on('ifUnchecked', 'input.ft-filter-by-category', function() {
                    $('.ft-cat').addClass("hide");
                    $('.ft-by-name').removeClass("hide");
            });
            $(document).on('ifChecked', 'input.fa-filter-by-name', function() {
                    $('.fa-name').removeClass("hide");
                    $('.fa-by-category').addClass("hide");
            });
            $(document).on('ifUnchecked', 'input.fa-filter-by-category', function() {
                    $('.fa-cat').addClass("hide");
                    $('.fa-by-name').removeClass("hide");
                    $('.fa-by-category').removeClass("hide");
            });
        // ** end F

        // .. Discount
            $(document).on('ifChecked', '.dis-filter-by-name', function() {
                    $('.dis-name').removeClass("hide");
                    $('.dis-by-category').addClass("hide");
            });
            $(document).on('ifUnchecked', '.dis-filter-by-name', function() {
                    $('.dis-name').addClass("hide");
                    $('.dis-by-category').removeClass("hide");
            });
            $(document).on('ifChecked', '.dis-filter-by-category', function() {
                    $('.dis-cat').removeClass("hide");
                    $('.dis-by-name').addClass("hide");
            });
            $(document).on('ifUnchecked', '.dis-filter-by-category', function() {
                    $('.dis-cat').addClass("hide");
                    $('.dis-by-name').removeClass("hide");
            });
        // ** end D

        // .. Collection
            $(document).on('ifChecked', '.cl-filter-by-name', function() {
                    $('.cl-name').removeClass("hide");
                    $('.cl-by-category').addClass("hide");     
                });
            $(document).on('ifUnchecked', '.cl-filter-by-name', function() {
                    $('.cl-name').addClass("hide");
                    $('.cl-by-category').removeClass("hide");
            });
            $(document).on('ifChecked', '.cl-filter-by-category', function() {
                    $('.cl-cat').removeClass("hide");
                    $('.cl-by-name').addClass("hide");
            });
            $(document).on('ifUnchecked', '.cl-filter-by-category', function() {
                    $('.cl-cat').addClass("hide");
                    $('.cl-by-name').removeClass("hide");
            });
        // ** end C

        feature_table_product    = $('#table_feature_product').DataTable({
                        processing: true,
                        serverSide: true,
                        scrollY:        "75vh",
                        scrollX:        true,
                        scrollCollapse: true, 
                        "lengthMenu": [10, 25, 50, 75, 100 ,200,1000,10000],
                        "ajax": {
                            "url": "/woocommerce/products/get-pro",
                            "data": function ( d ) {
                                d.check        = "Feature";
                                d.name         = $('.ft-product_name').val();
                                d.category     = $('.ft-category_id').val();
                                d.sub_category = $('.ft-sub_category_id').val();
                                d.brand        = $('.ft-brand_id').val();
                                d              = __datatable_ajax_callback(d);
                            }
                        },
                        aaSorting: [[1, 'asc']],
                        columns: [
                                { data: 'action', name: 'action'},
                                { data: 'name', name: 'name'  },
                                { data: 'code', name: 'code'  },
                                { data: 'description', name: 'description' , class:"disc"    },
                                { data: 'type', name: 'type' , class:"type"    },
                            ],
                            fnDrawCallback: function(oSettings) {
                                __currency_convert_recursively($('#table_feature_product'));
                            },
        });
        table_product    = $('#table_product').DataTable({
                processing: true,
                serverSide: true,
                scrollY:        "75vh",
                scrollX:        true,
                scrollCollapse: true, 
                "lengthMenu": [10, 25, 50, 75, 100 ,200,1000,10000],
                "ajax": {
                    "url": "/woocommerce/products/get-pro",
                    "data": function ( d ) {
                        d.check        = "All";
                        d.name         = $('.fa-product_name').val();
                        d.category     = $('.fa-category_id').val();
                        d.sub_category = $('.fa-sub_category_id').val();
                        d.brand        = $('.fa-brand_id').val();
                        d              = __datatable_ajax_callback(d);
                    }
                },
                aaSorting: [[1, 'asc']],
                columns: [
                        { data: 'action', name: 'action'},
                        { data: 'name', name: 'name'  },
                        { data: 'code', name: 'code'  },
                        { data: 'description', name: 'description' , class:"disc"    },
                        { data: 'type', name: 'type' , class:"type"    },
                    ],
                    fnDrawCallback: function(oSettings) {
                        __currency_convert_recursively($('#table_product'));
                    },
                    footerCallback: function ( row, data, start, end, display ) {
                        
                        $('.footer_left').html("****");
                    },
                });
        discount_table_product   = $('#table_discount_product').DataTable({
                        processing: true,
                        serverSide: true,
                        scrollY:        "75vh",
                        scrollX:        true,
                        scrollCollapse: true, 
                        "lengthMenu": [10, 25, 50, 75, 100 ,200,1000,10000],
                        "ajax": {
                            "url": "/woocommerce/products/get-pro",
                            "data": function ( d ) {
                                d.check        = "Discount";
                                d.name         = $('.dis-product_name').val();
                                d.category     = $('.dis-category_id').val();
                                d.sub_category = $('.dis-sub_category_id').val();
                                d.brand        = $('.dis-brand_id').val();
                                d              = __datatable_ajax_callback(d);
                            }
                        },
                        aaSorting: [[1, 'asc']],
                        columns: [
                                 { data: 'action', name: 'action'},
                                { data: 'name', name: 'name'  },
                                { data: 'code', name: 'code'  },
                                { data: 'description', name: 'description' , class:"disc"    },
                                { data: 'type', name: 'type' , class:"type"    },
                            ],
                            fnDrawCallback: function(oSettings) {
                                __currency_convert_recursively($('#table_discount_product'));
                            },
                });;
        collection_table_product = $('#table_collection_product').DataTable({
                        processing: true,
                        serverSide: true,
                        scrollY:        "75vh",
                        scrollX:        true,
                        scrollCollapse: true, 
                        "lengthMenu": [10, 25, 50, 75, 100 ,200,1000,10000],
                        "ajax": {
                            "url": "/woocommerce/products/get-pro",
                            "data": function ( d ) {
                                d.check        = "Collection";
                                d.name         = $('.cl-product_name').val();
                                d.category     = $('.cl-category_id').val();
                                d.sub_category = $('.cl-sub_category_id').val();
                                d.brand        = $('.cl-brand_id').val();
                                d              = __datatable_ajax_callback(d);
                            }
                        },
                        aaSorting: [[1, 'asc']],
                        columns: [
                                 { data: 'action', name: 'action'},
                                { data: 'name', name: 'name'  },
                                { data: 'code', name: 'code'  },
                                { data: 'description', name: 'description' , class:"disc"    },
                                { data: 'type', name: 'type' , class:"type"    },
                            ],
                            fnDrawCallback: function(oSettings) {
                                __currency_convert_recursively($('#table_collection_product'));
                            },
                });

        
                $(document).on("change",".ft-sub_category_id,.ft-category_id,.ft-product_name,.ft-brand_id",function(){
            feature_table_product.ajax.reload();
            // alert("1");  
        });
        $(document).on("change",".dis-sub_category_id,.dis-category_id,.dis-product_name,.dis-brand_id",function(){
            discount_table_product.ajax.reload();
            // alert("2");
        });
        $(document).on("change",".cl-sub_category_id,.cl-category_id,.cl-product_name,.cl-brand_id",function(){
            collection_table_product.ajax.reload();
            // alert("3");
        });
        $(document).on("change",".fa-sub_category_id,.fa-category_id,.fa-product_name,.fa-brand_id",function(){
            table_product.ajax.reload();
            // alert("3");
        });


    </script>
@endsection