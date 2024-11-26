@extends('layouts.app')

{{-- *1* --}}
@section('title', __('product.add_new_product'))

{{-- *2* --}}
@section('content')

  <!-- Content Header (Page header) -->
  {{-- */1/*section title of page --}}
  {{-- *********************************************** --}}
  <section class="content-header" >
    <h1 style="padding:10px; "><b>@lang('product.add_new_product')</b></h1>
    <h5><i><b>{{ "   Products  >  " }} </b>{{ "Add Product"   }} <b> {{"   "}} </b></i></h5>   
    <br>  
  </section>
  {{-- *********************************************** --}}

  <!-- Main content -->
  {{-- */2/*section body of page --}}
  {{-- *********************************************** --}}
    <section class="content">
      
      {{-- *1* section currency --}}
      {{-- *********************************************** --}}
      <!-- Page level currency setting -->
      <input type="hidden" id="p_code" value="{{$currency_details->code}}">
      <input type="hidden" id="p_symbol" value="{{$currency_details->symbol}}">
      <input type="hidden" id="p_thousand" value="{{$currency_details->thousand_separator}}">
      <input type="hidden" id="p_decimal" value="{{$currency_details->decimal_separator}}">
      {{-- *********************************************** --}}
      
      {{-- *2* section form --}}
      {{-- *********************************************** --}}
      @php
        $form_class = empty($duplicate_product) ? 'create' : '';
      @endphp
      {!! Form::open(['url' => action('ProductController@store'), 'method' => 'post', 
          'id' => 'product_add_form','class' => 'product_form ' . $form_class, 'files' => true ]) !!}
                {{-- *1* section product information --}}
                {{-- ******************************************************* --}}
                  <div class="row" style="margin:0px 10%;">   
                    @component('components.widget', ['class' => 'box-primary'  , "title" =>__('Product Information')  ])
                      <div class="row">
                          
                          <div class="col-sm-6">
                            <div class="form-group">
                              {!! Form::label('name', __('product.pro_name') . ':*') !!}
                                {!! Form::text('name', !empty($duplicate_product->name) ? $duplicate_product->name : null, ['id' =>"name_p",'class' => 'form-control', 'required',
                                'placeholder' => __('product.pro_name')]); !!}
                            </div>
                          </div>
                          <div class="col-sm-6">
                            <div class="form-group">
                              {!! Form::label('sku', __('product.sku') . ':') !!} @show_tooltip(__('tooltip.sku'))
                              {!! Form::text('sku', null, ['class' => 'form-control','placeholder' => __('product.sku')]); !!}
                            </div>
                          </div>
                              <div class="col-sm-6">
                                  <div class="form-group">
                                      {!! Form::label('sku2', __('product.sku2') . ':') !!}
                                      {!! Form::text('sku2', null, ['class' => 'form-control',
                                        'placeholder' => __('product.sku')]); !!}
                                  </div>
                              </div>
                          <div class="col-sm-6">
                            <div class="form-group">
                              {!! Form::label('barcode_type', __('product.barcode_type') . ':*') !!}
                                {!! Form::select('barcode_type', $barcode_types, !empty($duplicate_product->barcode_type) ? $duplicate_product->barcode_type : $barcode_default, ['class' => 'form-control select2', 'required']); !!}
                            </div>
                          </div>

                          {{-- ********************** --}}
                          <div class="clearfix"></div>
                          {{-- ********************** --}}

                          <div class="col-sm-6">
                            @php $unitm = \App\Unit::where("default",1)->first();  @endphp
                            <div class="form-group">
                              {!! Form::label('unit_id', __('product.unit') . ':*') !!}
                              <div class="input-group">
                                {!! Form::select('unit_id', $units, !empty($unitm) ? $unitm->id : session('business.default_unit'), ['class' => 'form-control select2', 'required']); !!}
                                <span class="input-group-btn">
                                  <button type="button" @if(!auth()->user()->can('unit.create'))   @endif class="btn btn-default bg-white btn-flat btn-modal" data-href="{{action('UnitController@create', ['quick_add' => true])}}" title="@lang('unit.add_unit')" data-container=".view_modal"><i class="fa fa-plus-circle text-primary fa-lg"></i></button>
                                </span>
                                
                              </div>
                            </div>
                          </div>
                          <div class="col-sm-6 @if(!session('business.enable_sub_units')) hide @endif">
                            <div class="form-group sub_unit_box">
                              @php 
                                // #6-8-2024 
                                $allList     = [];
                                $productUtil = new \App\Utils\ProductUtil() ;
                                $sub_units   = $productUtil->getSubUnits(session('business.id'), !empty($unitm) ? $unitm->id : session('business.default_unit'), true);
                                $un_id       = !empty($unitm) ? $unitm->id : session('business.default_unit');
                              @endphp
                              @foreach ($sub_units as $keies => $item)
                                 @if($keies != $un_id )
                                  @php  $allList[$keies] = $item["name"]; @endphp 
                                 @endif
                              @endforeach
                              {!! Form::label('sub_unit_ids', __('lang_v1.related_sub_units') . ':') !!} @show_tooltip(__('lang_v1.sub_units_tooltip'))
                              {!! Form::select('sub_unit_ids[]', $allList , !empty($duplicate_product->sub_unit_ids) ? $duplicate_product->sub_unit_ids : null, ['class' => 'form-control select2', 'multiple', 'id' => 'sub_unit_ids']); !!}
                            </div>
                          </div>
                          <div class="col-sm-6 @if(!session('business.enable_brand')) hide @endif">
                            <div class="form-group">
                                {!! Form::label('brand_id', __('product.brand') . ':') !!}
                              <div class="input-group">
                                {!! Form::select('brand_id', $brands, !empty($duplicate_product->brand_id) ? $duplicate_product->brand_id : null, ['placeholder' => __('messages.please_select'), 'class' => 'form-control select2']); !!}
                                <span class="input-group-btn">
                                  <button type="button" @if(!auth()->user()->can('brand.create')|| !auth()->user()->can('warehouse.views'))   @endif class="btn btn-default bg-white btn-flat btn-modal" data-href="{{action('BrandController@create', ['quick_add' => true])}}" title="@lang('brand.add_brand')" data-container=".view_modal"><i class="fa fa-plus-circle text-primary fa-lg"></i></button>
                                </span>
                              </div>
                            </div>
                            @php
                              $default_location = null;
                              if(count($business_locations) == 1){
                                $default_location = array_key_first($business_locations->toArray());
                              }
                            @endphp
                          </div>
                          <div class="col-sm-6 ">
                            <div class="form-group">
                              {!! Form::label('product_locations', __('business.business_locations') . ':') !!} @show_tooltip(__('lang_v1.product_location_help'))
                                {!! Form::select('product_locations[]', $business_locations ,$default_location, ['class' => 'form-control select2'  , 'id' => 'product_locations']); !!}
                            </div>
                          </div>
                          <div class="col-sm-6 @if(!session('business.enable_category')) hide @endif">
                            <div class="form-group">
                              {!! Form::label('category_id', __('product.category') . ':*') !!}
                              <div class="input-group">
                                  {!! Form::select('category_id', $categories, !empty($duplicate_product->category_id) ? $duplicate_product->category_id : null, ['placeholder' => __('messages.please_select'), 'class' => 'form-control select2' ,"required"]); !!}
                                  <span class="input-group-btn">
                                    <button type="button" @if(!auth()->user()->can('product.add_category') || !auth()->user()->can('warehouse.views'))   @endif class="btn btn-default bg-white btn-flat btn-modal" data-href="{{action('BrandController@create1', ['quick_add' => true])}}" title="@lang('product.category')" data-container=".view_modal"><i class="fa fa-plus-circle text-primary fa-lg"></i></button>
                                  </span>
                              </div>
                            </div>
                          </div>
                          <div class="col-sm-6 @if(!(session('business.enable_category') && session('business.enable_sub_category'))) hide @endif">
                            <div class="form-group">
                              {!! Form::label('sub_category_id', __('product.sub_category') . ':*') !!}
                              <div class="input-group">
                                {!! Form::select('sub_category_id', $sub_categories, !empty($duplicate_product->sub_category_id) ? $duplicate_product->sub_category_id : null, ['placeholder' => __('messages.please_select'), 'class' => 'form-control select2' ,"required"]); !!}
                                <span class="input-group-btn">
                                  <button type="button" @if(!auth()->user()->can('product.add_category') || !auth()->user()->can('warehouse.views'))   @endif class="btn btn-default bg-white btn-flat btn-modal" data-href="{{action('BrandController@createSub', ['quick_add' => true])}}" title="@lang('product.category')" data-container=".view_modal"><i class="fa fa-plus-circle text-primary fa-lg"></i></button>
                                </span>
                              </div>
                            </div>
                          </div>
                        
                          {{-- ********************** --}}
                          <div class="clearfix"></div>
                          {{-- ********************** --}}

                          <div class="col-sm-6">
                            <div class="form-group">
                            <br>
                              <label>
                                {!! Form::checkbox('enable_stock', 1, !empty($duplicate_product) ? $duplicate_product->enable_stock : true, ['class' => 'input-icheck', 'id' => 'enable_stock']); !!} <strong>@lang('product.manage_stock')</strong>
                              </label>@show_tooltip(__('tooltip.enable_stock')) <p class="help-block"><i>@lang('product.enable_stock_help')</i></p>
                            </div>
                          </div>
                          <div class="col-sm-6 @if(!empty($duplicate_product) && $duplicate_product->enable_stock == 0) hide @endif" id="alert_quantity_div">
                          <div class="form-group">
                            {!! Form::label('alert_quantity',  __('product.alert_quantity') . ':') !!} @show_tooltip(__('tooltip.alert_quantity'))
                            {!! Form::number('alert_quantity', !empty($duplicate_product->alert_quantity) ? $duplicate_product->alert_quantity : null , ['class' => 'form-control',
                            'placeholder' => __('product.alert_quantity'), 'min' => '0']); !!}
                          </div>
                          </div>
                        
                          @if(!empty($common_settings['enable_product_warranty']))
                            <div class="col-sm-6">
                              <div class="form-group">
                                {!! Form::label('warranty_id', __('lang_v1.warranty') . ':') !!}
                                {!! Form::select('warranty_id', $warranties, null, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select')]); !!}
                              </div>
                            </div>
                          @endif
                          
                          {{-- ********************** --}}
                          <div class="clearfix"></div>
                          {{-- ********************** --}}

                          <div class="col-sm-12">
                            <div class="form-group">
                              {!! Form::label('product_description', __('lang_v1.product_description') . ':') !!}
                                {!! Form::textarea('product_description', !empty($duplicate_product->product_description) ? $duplicate_product->product_description : null, ['class' => 'form-control']); !!}
                            </div>
                          </div>
                          <div class="col-sm-12">
                            <div class="form-group">
                              {!! Form::label('full_description', __('Full_description') . ':') !!}
                                {!! Form::textarea('full_description', !empty($duplicate_product->full_description) ? $duplicate_product->full_description : null, ['class' => 'form-control']); !!}
                            </div>
                          </div>

                          {{-- section additional --}}
                          {{-- ******************************** --}}
                            <!-- include module fields -->
                            {{-- @if(!empty($pos_module_data))
                            @foreach($pos_module_data as $key => $value)
                            @if(!empty($value['view_path']))
                            @includeIf($value['view_path'], ['view_data' => $value['view_data']])
                            @endif
                            @endforeach
                            @endif --}}
                          {{-- ******************************** --}}
                      </div>
                    @endcomponent
                  </div>
                {{-- ****************************************************** --}}
                
                {{-- *2* section media --}}
                {{-- ****************************************************** --}}
                  <div class="row" style="margin:0px 10%;">   
                    @component('components.widget', ['class' => 'box-primary' , "title" =>__('Product Media')  ])
                          <div class="row">
                            <div class="col-sm-6">
                              <div class="form-group">
                                {!! Form::label('image', __('lang_v1.product_image') . ':') !!}
                                {!! Form::file('image', ['id' => 'upload_image', 'accept' => implode(',', array_keys(config('constants.im_upload')))]); !!}
                                <small><p class="help-block">@lang('purchase.max_file_size', ['size' => (config('constants.document_size_limit') / 1000000)]) <br> @lang('lang_v1.aspect_ratio_should_be_1_1') <br> {{  "Allow Image : .jpeg , .jpg , .png"  }}</p> </small>
                              </div>
                            </div>
                            <div class="col-sm-6">
                              <div class="form-group">
                                {!! Form::label('product_brochure', __('lang_v1.product_brochure') . ':') !!}
                                {!! Form::file('product_brochure', ['id' => 'product_brochure', 'accept' => implode(',', array_keys(config('constants.document_upload_mimes_types')))]); !!}
                                <small>
                                    <p class="help-block">
                                        @lang('purchase.max_file_size', ['size' => (config('constants.document_size_limit') / 1000000)])
                                        @includeIf('components.document_help_text')
                                    </p>
                                </small>
                              </div>
                            </div>
                            
                            <div class="col-sm-6">
                                <div class="form-group">
                                  {!! Form::label('variation_images', __('lang_v1.product_image') . ':') !!} ++
                                  {!! Form::file('variation_images[]', ['class' => 'variation_images',"id"=>"variation_images", 'accept' => 'image/*', 'multiple']); !!}
                                  <small><p class="help-block">@lang('purchase.max_file_size', ['size' => (config('constants.document_size_limit') / 1000000)]) <br> @lang('lang_v1.aspect_ratio_should_be_1_1')</p></small>
                                </div>
                            </div>
                              <div class="col-sm-6">
                                <div class="form-group">
                                  {!! Form::label('vedio', __('vedio') . ':') !!}
                                  {!! Form::file('vedio', ['id' => 'upload_video', 'accept' => 'video/*']); !!}
                                  <small><p class="help-block">@lang('purchase.max_file_size', ['size' => (config('constants.vedio_size_limit') / 1000000)]) <br> @lang('lang_v1.aspect_ratio_should_be_1_1')</p></small>
                                </div>
                              </div>
                          
                            <div class="clearfix"></div>
                          </div>
                    @endcomponent
                  </div>
                {{-- ****************************************************** --}}
                
                {{-- *3* section product additional info --}}
                {{-- ****************************************************** --}}
                  <div class="row" style="margin:0px 10%;"> 
                    @component('components.widget', ['class' => 'box-primary',"title"=>__('Product Additional Info') ])
                        <div class="row">
                        @if(session('business.enable_product_expiry'))
                            @if(session('business.expiry_type') == 'add_expiry')
                              @php
                                $expiry_period = 12;
                                $hide = true;
                              @endphp
                            @else
                              @php
                                $expiry_period = null;
                                $hide = false;
                              @endphp
                            @endif
                          <div class="col-sm-6 @if($hide) hide @endif">
                            <div class="form-group">
                              <div class="multi-input">
                                {!! Form::label('expiry_period', __('product.expires_in') . ':') !!}<br>
                                {!! Form::text('expiry_period', !empty($duplicate_product->expiry_period) ? @num_format($duplicate_product->expiry_period) : $expiry_period, ['class' => 'form-control pull-left input_number',
                                  'placeholder' => __('product.expiry_period'), 'style' => 'width:60%;']); !!}
                                {!! Form::select('expiry_period_type', ['months'=>__('product.months'), 'days'=>__('product.days'), '' =>__('product.not_applicable') ], !empty($duplicate_product->expiry_period_type) ? $duplicate_product->expiry_period_type : 'months', ['class' => 'form-control select2 pull-left', 'style' => 'width:40%;', 'id' => 'expiry_period_type']); !!}
                              </div>
                            </div>
                          </div>
                        @endif

                        <div class="col-sm-6">
                          <div class="form-group">
                            <br>
                            {{-- <label>
                              {!! Form::checkbox('enable_sr_no', 1, !(empty($duplicate_product)) ? $duplicate_product->enable_sr_no : false, ['class' => 'input-icheck']); !!} <strong>@lang('lang_v1.enable_imei_or_sr_no')</strong>
                            </label> @show_tooltip(__('lang_v1.tooltip_sr_no')) --}}
                            <label>
                              {!! Form::checkbox('not_for_selling', 1, !(empty($duplicate_product)) ? $duplicate_product->not_for_selling : false, ['class' => 'input-icheck']); !!} <strong>@lang('lang_v1.not_for_selling')</strong>
                            </label> @show_tooltip(__('lang_v1.tooltip_not_for_selling'))
                          </div>
                        </div>

                        <div class="col-sm-6">
                          <div class="form-group">
                            {!! Form::label('weight',  __('lang_v1.weight') . ':') !!}
                            {!! Form::text('weight', !empty($duplicate_product->weight) ? $duplicate_product->weight : null, ['class' => 'form-control', 'placeholder' => __('lang_v1.weight')]); !!}
                          </div>
                        </div>

                        <div class="clearfix"></div>

                        <!-- Rack, Row & position number -->
                        @if(session('business.enable_racks') || session('business.enable_row') || session('business.enable_position'))
                          <div class="col-md-12">
                            <h4>@lang('lang_v1.rack_details'):
                              @show_tooltip(__('lang_v1.tooltip_rack_details'))
                            </h4>
                          </div>
                          @foreach($business_locations as $id => $location)
                            <div class="col-sm-3">
                              <div class="form-group">
                                {!! Form::label('rack_' . $id,  $location . ':') !!}
                                
                                @if(session('business.enable_racks'))
                                  {!! Form::text('product_racks[' . $id . '][rack]', !empty($rack_details[$id]['rack']) ? $rack_details[$id]['rack'] : null, ['class' => 'form-control', 'id' => 'rack_' . $id, 
                                    'placeholder' => __('lang_v1.rack')]); !!}
                                @endif

                                @if(session('business.enable_row'))
                                  {!! Form::text('product_racks[' . $id . '][row]', !empty($rack_details[$id]['row']) ? $rack_details[$id]['row'] : null, ['class' => 'form-control', 'placeholder' => __('lang_v1.row')]); !!}
                                @endif
                                
                                @if(session('business.enable_position'))
                                  {!! Form::text('product_racks[' . $id . '][position]', !empty($rack_details[$id]['position']) ? $rack_details[$id]['position'] : null, ['class' => 'form-control', 'placeholder' => __('lang_v1.position')]); !!}
                                @endif
                              </div>
                            </div>
                          @endforeach
                        @endif
                        
                        

                        @php
                          $custom_labels = json_decode(session('business.custom_labels'), true);
                          $product_custom_field1 = !empty($custom_labels['product']['custom_field_1']) ? $custom_labels['product']['custom_field_1'] : __('lang_v1.product_custom_field1');
                          $product_custom_field2 = !empty($custom_labels['product']['custom_field_2']) ? $custom_labels['product']['custom_field_2'] : __('lang_v1.product_custom_field2');
                          $product_custom_field3 = !empty($custom_labels['product']['custom_field_3']) ? $custom_labels['product']['custom_field_3'] : __('lang_v1.product_custom_field3');
                          $product_custom_field4 = !empty($custom_labels['product']['custom_field_4']) ? $custom_labels['product']['custom_field_4'] : __('lang_v1.product_custom_field4');
                        @endphp
                        <!--custom fields-->
                        <div class="clearfix"></div>
                        <div class="col-sm-3">
                          <div class="form-group">
                            {!! Form::label('product_custom_field1',  $product_custom_field1 . ':') !!}
                            {!! Form::text('product_custom_field1', !empty($duplicate_product->product_custom_field1) ? $duplicate_product->product_custom_field1 : null, ['class' => 'form-control', 'placeholder' => $product_custom_field1]); !!}
                          </div>
                        </div>

                        <div class="col-sm-3">
                          <div class="form-group">
                            {!! Form::label('product_custom_field2',  $product_custom_field2 . ':') !!}
                            {!! Form::text('product_custom_field2', !empty($duplicate_product->product_custom_field2) ? $duplicate_product->product_custom_field2 : null, ['class' => 'form-control', 'placeholder' => $product_custom_field2]); !!}
                          </div>
                        </div>

                        <div class="col-sm-3">
                          <div class="form-group">
                            {!! Form::label('product_custom_field3',  $product_custom_field3 . ':') !!}
                            {!! Form::text('product_custom_field3', !empty($duplicate_product->product_custom_field3) ? $duplicate_product->product_custom_field3 : null, ['class' => 'form-control', 'placeholder' => $product_custom_field3]); !!}
                          </div>
                        </div>

                        <div class="col-sm-3">
                          <div class="form-group">
                            {!! Form::label('product_custom_field4',  $product_custom_field4 . ':') !!}
                            {!! Form::text('product_custom_field4', !empty($duplicate_product->product_custom_field4) ? $duplicate_product->product_custom_field4 : null, ['class' => 'form-control', 'placeholder' => $product_custom_field4]); !!}
                          </div>
                        </div>
                        <!--custom fields-->
                        <div class="clearfix"></div>
                        <!--@include('layouts.partials.module_form_part')-->
                      </div>
                    @endcomponent
                  </div>
                {{-- ****************************************************** --}}
                
                {{-- *4* section old page for price --}}
                {{-- ****************************************************** --}}
                  {{-- @component("components.widget",["class"=>"box-primary","title"=>__("product.list_of_price")])
                            @include("product.partials.list_price")
                        @endcomponent --}}
                {{-- ****************************************************** --}}
                  
                {{-- *5* section product price  --}}
                {{-- ****************************************************** --}}
                  <div class="row" style="margin:0px 10%;"> 
                    @component('components.widget', ['class' => 'box-primary' ,"title"=>__('Product Prices')])
                        <div class="row">
                        @php
                            $id =  $taxes->toArray() ;
                            $keys =  array_keys($id) ;
                        @endphp
                        <div class="col-sm-6 @if(!session('business.enable_price_tax')) hide @endif">
                          <div class="form-group">
                            {!! Form::label('tax', __('product.applicable_tax') . ':') !!}
                              {!! Form::select('tax', $taxes, isset($keys[1])?$keys[1]:null, ['placeholder' => __('messages.please_select'), 'class' => 'form-control select2'], $tax_attributes); !!}
                          </div>
                        </div>

                        <div class="col-sm-6  hide ">
                          <div class="form-group">
                            {!! Form::label('tax_type', __('product.selling_price_tax_type') . ': ') !!}
                              {!! Form::select('tax_type', ['inclusive' => __('product.inclusive'), 'exclusive' => __('product.exclusive')], !empty($duplicate_product->tax_type) ? $duplicate_product->tax_type : 'exclusive',
                              ['class' => 'form-control select2']); !!}
                          </div>
                        </div>



                        <div class="col-sm-6">
                          <div class="form-group">
                            {!! Form::label('type', __('product.product_type') . ':*') !!} @show_tooltip(__('tooltip.product_type'))
                            {!! Form::select('type', $product_types, !empty($duplicate_product->type) ? $duplicate_product->type : null, ['class' => 'form-control select2',
                            'required', 'data-action' => !empty($duplicate_product) ? 'duplicate' : 'add', 'data-product_id' => !empty($duplicate_product) ? $duplicate_product->id : '0']); !!}
                          </div>
                        </div>

                        <div class="form-group col-sm-12" id="product_form_part">
                          @include('product.partials.single_product_form_part', [
                                                                                  'profit_percent' => $default_profit_percent,
                                                                                  'product_price'  => $product_price , 
                                                                                  'array_unit'     => $array_unit,
                                                                                  'units'          => $units,
                                                                                  'unit_id'        => !empty($unitm) ? $unitm->id : session('business.default_unit'),
                                                                                  'units_main'     => $units_main
                                                                                ])
                        </div>
                        <input type="hidden" id="variation_counter" value="1">
                        <input type="hidden" id="default_profit_percent" 
                          value="{{ $default_profit_percent }}">

                      </div>
                    @endcomponent
                  </div>
                {{-- ****************************************************** --}}
                
                {{-- *6* section modal --}}
                {{-- ****************************************************** --}}
                <div class="row">
                  <div class="col-sm-12">
                    <input type="hidden" name="submit_type" id="submit_type">
                    <div class="text-center">
                      {{-- *1* section save --}}
                      {{-- *************************************** --}}
                        <div class="btn-group">
                            @if($selling_price_group_count)
                              <button type="submit" value="submit_n_add_selling_prices" class="btn btn-warning submit_product_form">@lang('lang_v1.save_n_add_selling_price_group_prices')</button>
                            @endif

                            @can('product.opening_stock')
                              <button id="opening_stock_button" @if(!empty($duplicate_product) && $duplicate_product->enable_stock == 0) disabled @endif type="submit" value="submit_n_add_opening_stock" class="btn bg-purple submit_product_form">@lang('lang_v1.save_n_add_opening_stock')</button>
                            @endcan

                            <button type="submit" value="save_n_add_another" class="btn bg-maroon submit_product_form">@lang('lang_v1.save_n_add_another')</button>

                            <button type="submit" value="submit" class="btn btn-primary submit_product_form">@lang('messages.save')</button>
                        </div>
                      {{-- **************************************** --}}
                      
                      {{-- *2* section modal --}}
                      {{-- **************************************** --}}
                        <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                          @include('unit.create_add')
                        </div>
                      {{-- **************************************** --}}
                  </div>
                </div>
        </div>
      {!! Form::close() !!}
    </section>
  {{-- ********************************************** --}}
  <!-- /.content -->

@endsection

{{-- *3* --}}
@section('javascript')
  {{-- section rolation --}}
  <script src="{{ asset('js/producte.js?v=' . $asset_v) }}"></script>
  {{-- section additional --}}
  <script type="text/javascript">
    
    
    //... //**1**// previous code  
      $(document).ready(function(){
          __page_leave_confirmation('#product_add_form');
          onScan.attachTo(document, {
              suffixKeyCodes: [13], // enter-key expected at the end of a scan
              reactToPaste: true, // Compatibility to built-in scanners in paste-mode (as opposed to keyboard-mode)
              onScan: function(sCode, iQty) {
                  $('input#sku').val(sCode);
              },
              onScanError: function(oDebug) {
                  console.log(oDebug); 
              },
              minLength: 2,
              ignoreIfFocusOn: ['input', '.form-control']
              // onKeyDetect: function(iKeyCode){ // output all potentially relevant key events - great for debugging!
              //     console.log('Pressed: ' + iKeyCode);
              // }
          });
      });
      $(document).on("input","#name_p",function(){
        $("#name_p").css({"outline":"0px solid red","box-shadow":"1px 1px 10px transparent","color":"gray"})
      });
      $(document).on("change","#name_p",function(e){
        var name = $("#name_p").val();
        // $("#name_p").css({"outline":"0px solid red","box-shadow":"1px 1px 10px transparent"});
        $.ajax({
          method: 'GET',
          url: '/product/check/' + name,
          async: false,
          data: {
              name: name,
          },
          dataType: 'json',
          success: function(result) {
              $results = result.status;
              if($results == true){
                  toastr.error(LANG.product_name);
                  $("#name_p").css({"outline":"1px solid red","box-shadow":"1px 1px 10px red","color":"red"})
              }
            }
          });
      });
    // ................
    
    //... //**2**// previous code  
        //.. Product - Price O Functions ..\\
        //.. *************************** .. \\ 
        //..1 Add Row
        function add_row(){
              i = 1;
              var row_count = parseFloat($(".number-of-row").val()) + parseFloat(1) ;
              $html  =  '<tr>'+
                        '<td ><input  type="text" name="name_add[]"  class="form-control name_price" value="" placeholder="{{__('messages.please_enter_name')}}"></td>'+
                        '<td ><input  type="text" name="price[]" class="form-control amount_price" value="" placeholder="{{__('messages.please_enter_price')}}"></td>';
              $html +=  '<td class="multi-input column_1" currency[1]" >'+
                        '<input  type="text" name="currency_price[1][]" class="form-control amount_price width-70 pull-left" min="0" style="width:60% ;display:inline-block"  value="0" placeholder="{{__('messages.please_enter_price')}}">'+
                        '{{ Form::select('currency_amount_price[1][]',$currencies,null,['class'=>'form-control amount_price_s width-30 pull-left select2 ' ,'style'=>'width:40%' ,'placeholder'=>__('messages.please_select') ]) }}</td>'+
                        '</td>';
              $head  =  '<th >'+ '{{__('lang_v1.price_in_currency')}} &nbsp;&nbsp;<i class="fa fas fa-trash" onClick="delete_column(this);"></i>'+ '</th>';
              while (i < $(".number-of-column").val()) {
                  count = i + 1;
                  $html +=  '<td class="multi-input ceil_'+count+' column_'+count+'" currency['+count+']" >'+
                            '<input  class="ceil" hidden value="'+count+'">'+
                            '<input  type="text" name="currency_price['+count+'][]" class="form-control amount_price width-70 pull-left" min="0" style="width:60% ;display:inline-block"  value="0" placeholder="{{__('messages.please_enter_price')}}">'+
                            '{{ Form::select('currency_amount_price[1]',$currencies,null,['class'=>'form-control amount_price_s width-30 pull-left select2' ,'style'=>'width:40%' ,'placeholder'=>__('messages.please_select') ]) }}</td>'+
                            '</td>';
                  $head =   '<th >'+ '{{__('lang_v1.price_in_currency')}} &nbsp;&nbsp;<i class="fa fas fa-trash" onClick="delete_column(this);"></i>'+ '</th>';
                i++;
              }   
              $html +='<td class="last_column delete_row" onClick="delete_row(this);"><i class="fa fas fa-trash"></i></td>'+
                      '</tr>';
                $("table#product_price tbody").append($html);
                $(".number-of-row").attr("name",row_count) ;
                $('.amount_price_s').select2();
                update_currency();

                setTimeout(() => {
                  update_ceil();
                }, 1000);
        }
        //..2 Add Column
        function add_column(){
              if($(".number-of-column").val() < 5 ){
                var column_count = parseFloat($(".number-of-column").val())   + parseFloat(1)  ;
                
                $html = '<td class="multi-input ceil_'+column_count+' column_'+column_count+' currency['+column_count+']" >'+
                          '<input  class="ceil" hidden value="'+column_count+'">'+
                          '<input  type="text" name="currency_price['+column_count+'][]" class="form-control amount_price width-70 pull-left" min="0" style="width:100% ;display:inline-block"  value="0" placeholder="{{__('messages.please_enter_price')}}">'+
                          // '{{ Form::select('',$currencies,null,['class'=>'form-control amount_price_s width-30 pull-left select2' ,'style'=>"width:40%" ,'placeholder'=>__('messages.please_select') ]) }}</td>'+
                          '</td>';
                          $head = '<th class="column_'+column_count+'">'+
                            '{{__('product.unit')}} &nbsp;&nbsp;'+
                                '<i class="fa fas fa-trash" onClick="delete_column(this);"></i>'+
                        '</th>';
                        $foot = '<td  class="column_'+column_count+'"> </td>';
                        $("table#product_price thead .last_column").before($head);
                        $("table#product_price tbody .last_column").before($html);
                        $("table#product_price tfoot .last_column").before($foot);
                          
                        $(".number-of-column").attr("value",column_count);
                  }
                  parent_class_select = ".ceil_"+column_count;
                  name = "currency_amount_price["+column_count+"][]";
                  $("table#product_price tbody").find(parent_class_select).find(".amount_price_s").attr("name",name);
                  $('.amount_price_s').select2();
                  update_currency();

        }
        //..3 Update Select Name 
        function update_ceil(){
              $(".ceil").each(function(){
                  id = $(this).val();
                  name = "currency_amount_price["+id+"][]"
                  $(this).parent().find("select").attr("name",name);
              })
        }
        //..4 Delete Row
        function delete_row(e){
          $(e).closest('tr').remove();
        }
        
        //..5 Delete Column
        function delete_column(e){
          var column_count = parseFloat($(".number-of-column").val())   - parseFloat(1)  ;
          var row_count = parseFloat($(".number-of-row").val()) - parseFloat(1) ;
          $(".number-of-row").attr("name",row_count);
          column = $(e).parent().attr("class");
          count = 0;
          counts = 0;
          $("table#product_price th").each(function(){
              
            if((  $(this).attr("class") != "last_column"  ) &&  (  $(this).attr("class") != column ) && ($(this).attr("class")!=null) ){
              indx = column.slice(column.indexOf('_') + 1);
              after = $(this).attr("class").slice($(this).attr("class").indexOf('_') + 1);
              if(after > indx){
                if(count==0){
                  count=indx;
                  for_count = indx;
                }
                col = "column_"+for_count;
                $(this).attr("class" ,col );
                $(".number-of-column").attr("value",for_count);
                for_count++;
              }
            
            }else if((  $(this).attr("class") == column ) && ($(this).attr("class")!=null)){
              if(column == $(this).attr("class")){
                  class_column = "."+column;
                  $(class_column).each(function(){
                  $(this).remove();
                  value = indx = column.slice(column.indexOf('_') + 1)  - 1 ;
                  $(".number-of-column").attr("value",value)
                })
              }
            }
          });
          
          $("table#product_price tbody tr").each(function(){
            counts = 0;
            $(this).find("td").each(function(){ 
              if(( $(this).attr("class") != "last_column delete_row" && $(this).attr("class") != "last_column"  ) &&  (  $(this).attr("class") != column ) && ($(this).attr("class")!=null) ){
                console.log($(this).attr("class"));
                console.log(column  + " in td");
                
                indx = column.slice(column.indexOf('_') + 1);
                after = $(this).attr("class").slice($(this).attr("class").indexOf('_') + 1);
                if(counts==0){
                    counts     = indx;
                    for_countx = indx;
                }
                if(after > indx){
                  col =  "multi-input " + " ceil_" + for_countx + " column_"+ for_countx + " currency["+ for_countx +"]"  ;
                  name =  "currency_price["+ for_countx +"][]"  ;
                  nameS =  "currency_amount_price["+ for_countx +"][]"  ;
                  $(this).attr("class" ,col );
                  $(this).find(".ceil").attr("value",for_countx);
                  $(this).find(".amount_price").attr("name",name);
                  $(this).find(".amount_price_s").attr("name",nameS);
                  console.log($(this).find(".ceil").val()+"ssdsd"+for_countx);
                    
                  for_countx++;
                } 
              
              }else if((  $(this).attr("class") == column ) && ($(this).attr("class")!=null)){
                
                if(column == $(this).attr("class")){
                  class_column = "."+column;
                  $(class_column).each(function(){
                    $(this).remove();
                  })
                  
                }
              }
            })
          });
          // $(e).closest('th').remove();
          // $(e).closest('td').remove();
          
        }
        //..6 On Currency Change
        update_currency();
        function update_currency(){
          $(".amount_price_s").each(function(){
              $(this).on("change",function(){
                var id = $(this).val();
                // console.log(id);
                if(!$(this).parent().children().eq(0).hasClass("ceil")){
                  var old   = $(this).parent().children().eq(0);
                }else{
                  var old   = $(this).parent().children().eq(1);
                }
                var price = $(this).parent().parent().children().children().eq(2).val();
                console.log(old);
                console.log(id);
                if(id == ""){
                    old.val(0);
                }else{
                  $.ajax({
                      url:"/symbol/amount/"+id,
                      dataType: 'html',
                    success:function(data){
                      var object  = JSON.parse(data);
                      console.log(object);
                      old.val((price/object.amount).toFixed(4));
                    },
                  });	 
                }
              });
          })
        }

        function addUnit(){
            $html = '<tr>';
            $html +=  '<td style="vertical-align: middle;">'+
                      '{!! Form::label('actual_name', __( 'unit.name' ) . ':*') !!}'+
                      '{!! Form::text('actual_name[]', null, ['class' => 'form-control', 'required', 'placeholder' => __( 'unit.name' )]); !!}'+
                      '<br>'+
                      '{!! Form::text('base_unit_multiplier[]', null, ['class' => 'form-control input_number', 'placeholder' => __( 'lang_v1.times_base_unit' )]); !!}</td>'+
                      '<td style="vertical-align: middle;">'+
                      '{!! Form::label('short_name', __( 'unit.short_name' ) . ':*') !!}'+
                      '{!! Form::text('short_name[]', null, ['class' => 'form-control', 'placeholder' => __( 'unit.short_name' ), 'required']); !!}'+
                      '<br>'+
                      '{!! Form::select('base_unit_id[]', $units, null, ['placeholder' => __( 'lang_v1.select_base_unit' ), 'class' => 'form-control']); !!}</td>'+
                      '<td style="vertical-align: middle;">'+
                      '{!! Form::label('allow_decimal', __( 'unit.allow_decimal' ) . ':*') !!}'+
                      '{!! Form::select('allow_decimal[]', ['1' => __('messages.yes'), '0' => __('messages.no')], null, ['placeholder' => __( 'messages.please_select' ), 'required', 'class' => 'form-control']); !!}'+
                      '<br>'+
                      '{!! Form::text('price_unit[]', null, ['class' => 'form-control input_number', 'placeholder' => __( 'lang_v1.price' )]); !!}</td>'
            $html += "<td><br><br><br><i class='fa fas fa-trash' onClick='deleteR(this);'></i></td>";
            $html += "</tr>";
            $("#unit_tables").append($html);
        }
        //..# Delete Row
        function deleteR(e){
          $(e).closest('tr').remove();
        }
    //..............................

    //... //**3**// new code
      //.. Product - Prices N Functions ..\\
      //.. **************************** .. \\ 
      //...... view prices
        function addMore(){
          $(".more-price").toggleClass("hide");
          $(".mis").toggleClass("hide");
          $(".pls").toggleClass("hide");
        }
      //...... hide prices
      // ..... change unit 
       
        // $(".un_select").each(function(){
        //   var e = $(this);
        //   e.on("change",function(){
        //     e.attr("data-id",e.val());
        //     $(".un_select").each(function(){
        //       e.attr("data-change",0);
        //     });
        //     e.attr("data-change",1);
        //     un_select(e.val());
        //   })
        // })
         
        // function un_select(i){
        //   $.ajax({
        //         method: 'GET',
        //         url: "/units/change-units?id="+i,
        //         dataType: 'json',
        //         success: function(result) {
        //             if (result.success == true) {
        //                 toastr.success(result.msg);
        //                 $(".un_select").each(function(){
        //                     var el = $(this);  
        //                     if(1 != el.attr("data-change") ){
        //                         el.html("");
        //                         html = "<select>";
        //                         for(var j in result.list) {
        //                           html += "<option value='"+j+"'>" + result.list[j]+"</option>";
        //                         }
        //                         html += "</select>";
        //                       el.html(html);
        //                     }
        //                 });
        //             } else {
        //                 toastr.error(result.msg);
        //             }
        //         },
        //   });
          
        // }
    //.................
    
  </script>
@endsection