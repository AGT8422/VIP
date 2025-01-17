@extends('layouts.app')
@section('title', __('sale.products'))

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1><b>@lang('sale.products')</b>
            <small>@lang('lang_v1.manage_products')</small>
        </h1>
        <h5><i><b>{{ "   Inventory  >  " }} </b>{{ "Products Gallary"   }} <b> {{"   "}} </b></i></h5>  
        <br> 
    </section>
  

    <!-- Main content -->
    <section class="content"> 
        <div class="col-md-12">
            @component('components.filters', ['title' => __('report.filters'),'class' => "box-primary"])
                
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('type', __('product.product_type') . ':') !!}
                        {!! Form::select('type', ['single' => __('lang_v1.single'), 'variable' => __('lang_v1.variable'), 'combo' => __('lang_v1.combo')], null, ['class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'product_list_filter_type', 'placeholder' => __('lang_v1.all')]); !!}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('category_id', __('product.category') . ':') !!}
                        {!! Form::select('category_id', $categories, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'product_list_filter_category_id', 'placeholder' => __('lang_v1.all')]); !!}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('sub_category_id',   __('product.sub_category').':') !!}
                        {!! Form::select('sub_category_id', $sub_cat, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'sub_category_id', 'placeholder' => __('lang_v1.all')]); !!}
                    </div>
                </div>
                    <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('product_available', __('lang_v1.Available') . ':') !!}
                        {!! Form::select('product_available', [1=>__("lang_v1.Available"),0=>__("lang_v1.Not Available")], null, ['class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'product_available', 'placeholder' => __('lang_v1.all')]); !!}
                    </div>
                </div>
                <div class="col-md-4 " style="display: none " >
                    <div class="form-group">
                        {!! Form::label('unit_id', __('product.unit') . ':') !!}
                        {!! Form::select('unit_id', $units, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'product_list_filter_unit_id', 'placeholder' => __('lang_v1.all')]); !!}
                    </div>
                </div>
                <div class="col-md-4" style="display: none " >
                    <div class="form-group">
                        {!! Form::label('tax_id', __('product.tax') . ':') !!}
                        {!! Form::select('tax_id', $taxes, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'product_list_filter_tax_id', 'placeholder' => __('lang_v1.all')]); !!}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('brand_id', __('product.brand') . ':') !!}
                        {!! Form::select('brand_id', $brands, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'product_list_filter_brand_id', 'placeholder' => __('lang_v1.all')]); !!}
                    </div>
                </div>

                <div class="col-md-4" style="display: none " >
                    <br>
                    <div class="form-group">
                        {!! Form::select('active_state', ['active' => __('business.is_active'), 'inactive' => __('lang_v1.inactive')], null, ['class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'active_state', 'placeholder' => __('lang_v1.all')]); !!}
                    </div>
                </div>
                <div class="col-md-3" style="display: none " >
                    <div class="form-group">
                        {!! Form::label('image', __('lang_v1.image') . ':') !!}
                        {!! Form::select('type', ['default' => __("home.without_image"), 'image' => __('lang_v1.image')], null, ['class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'product_list_filter_image', 'placeholder' => __('lang_v1.all')]); !!}
                    </div>
                </div>
                <div class="col-md-3" style="display: none " >
                    <div class="form-group">
                        {!! Form::label('image',__('report.current_stock'). ':') !!}
                        {!! Form::select('current_stock', ['zero' =>'Zero', 'gtzero' => __("home.more_than_zero"),'lszero' => __("home.less_than_zero")], null, ['class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'product_list_filter_current_stock', 'placeholder' => __('lang_v1.all')]); !!}
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

                <div class="col-md-2"  >
                    <div class="form-group">
                        <label>@lang('lang_v1.search')</label>
                        <div>
                            <input type="text" name="productname" id="productname" class="form-control" style="width: 80%;float: left; ">
                            <button type="button" class="btn-search"><i class="fa fa-search" ></i></button>
                        </div>
                    </div>
                </div>
                    <div class="col-md-1">
                        <div class="mt-15">
                            @can('product.create')
                                <a class="btn btn-primary  " href="{{action('ProductController@create')}}">
                                    <i class="fa fa-plus"></i> @lang('product.add_new_product')</a>
                            @endcan
                        </div>
                    </div>
            @endcomponent
        </div>
         


        <input type="hidden" id="rem" name="rem" value="true" >
        <input type="hidden" id="offset" name="offset" value="0" >
        <div class="main-prduct container" id="products">


          </div>
        <div class="loader hidden" id="loader"></div>
        <div style="justify-content: center;display: flex;">
            <button class="btn btn-success" style="background-color:#26252b;" onclick="getproducts()" id="morebtn">{{"See More ++"}}</button>
        </div>

        <input type="hidden" id="is_rack_enabled" value="{{$rack_enabled}}">

        <div class="modal fade product_modal" tabindex="-1" role="dialog"
             aria-labelledby="gridSystemModalLabel">
        </div>

        <div class="modal fade" id="view_product_modal" tabindex="-1" role="dialog"
             aria-labelledby="gridSystemModalLabel">
        </div>

        <div class="modal fade" id="opening_stock_modal" tabindex="-1" role="dialog"
             aria-labelledby="gridSystemModalLabel">
        </div>

        @include('product.partials.edit_product_location_modal')

    </section>
    <!-- /.content -->

@endsection

@section('javascript')
    <script src="{{ asset('js/producte.js?v=' . $asset_v) }}"></script>
    <script src="{{ asset('js/opening_stock.js?v=' . $asset_v) }}"></script>
    <script src="{{ asset('js/gallery.js?v=' . $asset_v) }}"></script>
    {{--  <script src="{{ asset('js/reportt.js?v=' . $asset_v) }}"></script>--}}


@endsection