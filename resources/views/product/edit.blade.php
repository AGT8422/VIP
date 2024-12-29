@extends('layouts.app')
{{-- *1* --}}
@section('title', __('product.edit_product'))

{{-- *2* --}}
@section('content')
    {{-- *1* section script started --}}
    {{-- *************************************** --}}
      <script src="http://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"> </script>
    {{-- *************************************** --}}
    
    <!-- Content Header (Page header) -->
    {{-- *2* section title of page --}}
    {{-- *************************************** --}}
      <section class="content-header font_text">
          <h1 style="padding:10px "><b>@lang('product.edit_product')</b> {{ " ... " }}<sub  ><b>{{ "   NAME:  ( " }} </b> {{ $product->name  }} <b> {{" ) "}} </b> <b>{{ "  // Code:  (" }} </b>  {{ $product->sku  }} <b> {{" )"}} </b>  </sub></h1>
          @php $mainUrl = '/products';  @endphp  
          <h5 class="font_text"><i><b class="font_text"><a  class="font_text"href="{{\URL::to($mainUrl)}}">{{ __("sale.products") }} {{ __("izo.>") . " " }}</a></b>{{  __("product.edit_product")   }} <b> {{"   "}} </b></i></h5>
        
      </section>
    {{-- *************************************** --}}
    
    <!-- Main content -->
    {{-- *3* section body of page --}}
    {{-- *************************************** --}}
      <section class="content font_text">
              {!! Form::open(['url' => action('ProductController@update' , [$product->id] ), 'method' => 'PUT', 'id' => 'product_add_form',
                  'class' => 'product_form', 'files' => true ]) !!}
              {{-- *1* section product Information --}}
              {{-- *************************************** --}}
                <input type="hidden" id="product_id" value="{{ $product->id }}"> 
                    @component('components.widget', ['class' => 'box-primary' , "title" =>__('izo.Product Information')])
                        <div class="row">
                            <div class="col-sm-6">
                              <div class="form-group">
                                {!! Form::label('name', __('product.pro_name') . ':*') !!}
                                  {!! Form::text('name', $product->name, ['class' => 'form-control', 'required',
                                  'placeholder' => __('product.pro_name')]); !!}
                              </div>
                            </div>

                            <div class="col-sm-6 @if(!(session('business.enable_category') && session('business.enable_sub_category'))) hide @endif">
                              <div class="form-group">
                                {!! Form::label('sku', __('product.sku')  . ':*') !!} @show_tooltip(__('tooltip.sku'))
                                {!! Form::text('sku', $product->sku, ['class' => 'form-control',
                                'placeholder' => __('product.sku'), 'required']); !!}
                              </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="form-group">
                                    {!! Form::label('sku2', __('product.sku2') . ':') !!}
                                    {!! Form::text('sku2',$product->sku2, ['class' => 'form-control',
                                      'placeholder' => __('product.sku2')]); !!}
                                </div>
                            </div>

                            
                            <div class="col-sm-6">
                              <div class="form-group">
                                {!! Form::label('barcode_type', __('product.barcode_type') . ':*') !!}
                                  {!! Form::select('barcode_type', $barcode_types, $product->barcode_type, ['placeholder' => __('messages.please_select'), 'class' => 'form-control select2', 'required']); !!}
                              </div>
                            </div>
                            <div class="clearfix"></div>
                            <div class="col-sm-6">
                              @php  $chk = \App\Product::checkIfIsMove($product->id);    @endphp
                              <div class="form-group">
                                {!! Form::label('unit_id', __('product.unit') . ':*') !!}
                                @if($chk)
                                    {!! Form::text('uun', $product->unit->actual_name, [ 'class' => 'form-control',"readOnly","style"=>"width:100% !important" ]); !!}
                                  
                                  <div class="input-group  hide">
                                    {!! Form::select('unit_id', $units, $product->unit_id, [    'class' => 'form-control select2',  'required']); !!}
                                    <span class="input-group-btn">
                                      <button type="button" @if(!auth()->user()->can('unit.create')) disabled @endif class="btn btn-default bg-white btn-flat quick_add_unit btn-modal" data-href="{{action('UnitController@create', ['quick_add' => true])}}" title="@lang('unit.add_unit')" data-container=".view_modal"><i class="fa fa-plus-circle text-primary fa-lg"></i></button>
                                    </span>
                                  </div>
                                  @else
                                    <div class="input-group">
                                      {!! Form::select('unit_id', $units, $product->unit_id, [    'class' => 'form-control select2 ',  'required']); !!}
                                      <span class="input-group-btn">
                                        <button type="button" @if(!auth()->user()->can('unit.create')) disabled @endif class="btn btn-default bg-white btn-flat quick_add_unit btn-modal" data-href="{{action('UnitController@create', ['quick_add' => true])}}" title="@lang('unit.add_unit')" data-container=".view_modal"><i class="fa fa-plus-circle text-primary fa-lg"></i></button>
                                      </span>
                                    </div>
                                  @endif
                              </div>
                            </div>

                            <div class="col-sm-6 @if(!session('business.enable_sub_units')) hide @endif">
                              <div @if($product->type == "combo") class="form-group hide  sub_unit_box" @else  class="form-group  sub_unit_box" @endif>
                                {!! Form::label('sub_unit_ids', __('lang_v1.related_sub_units') . ':') !!} @show_tooltip(__('lang_v1.sub_units_tooltip'))

                                <select name="sub_unit_ids[]" class="form-control select2" multiple  id="sub_unit_ids">
                                  @foreach($sub_units as $sub_unit_id => $sub_unit_value)
                                    @if($product->unit_id != $sub_unit_id )
                                      <option value="{{$sub_unit_id}}" 
                                        @if(is_array($product->sub_unit_ids) &&in_array($sub_unit_id, $product->sub_unit_ids))   selected 
                                        @endif
                                      >{{$sub_unit_value['name']}}</option>
                                    @endif
                                  @endforeach
                                </select>
                              </div>
                            </div>

                            <div class="col-sm-6 @if(!session('business.enable_brand')) hide @endif">
                              <div class="form-group">
                                {!! Form::label('brand_id', __('product.brand') . ':') !!}
                                <div class="input-group">
                                  {!! Form::select('brand_id', $brands, $product->brand_id, ['placeholder' => __('messages.please_select'), 'class' => 'form-control select2']); !!}
                                  <span class="input-group-btn">
                                    <button type="button" @if(!auth()->user()->can('brand.create')) disabled @endif class="btn btn-default bg-white btn-flat btn-modal" data-href="{{action('BrandController@create', ['quick_add' => true])}}" title="@lang('brand.add_brand')" data-container=".view_modal"><i class="fa fa-plus-circle text-primary fa-lg"></i></button>
                                  </span>
                                </div>
                              </div>
                            </div>

                            

                            
                            <div class="col-sm-6 @if(!session('business.enable_category')) hide @endif">
                              <div class="form-group">
                                {!! Form::label('category_id', __('product.category') . ':') !!}
                                  <div class="input-group"> 
                                    {!! Form::select('category_id', $categories, $product->category_id, ['placeholder' => __('messages.please_select'), 'class' => 'form-control select2']); !!}
                                    <span class="input-group-btn">
                                      <button type="button" @if(!auth()->user()->can('product.add_category')) disabled @endif class="btn btn-default bg-white btn-flat btn-modal" data-href="{{action('BrandController@create1', ['quick_add' => true])}}" title="@lang('product.category')" data-container=".view_modal"><i class="fa fa-plus-circle text-primary fa-lg"></i></button>
                                    </span>
                                  </div>
                                </div>
                            </div>
                            
                            <div class="col-sm-6 @if(!(session('business.enable_category') && session('business.enable_sub_category'))) hide @endif">
                              <div class="form-group">
                                {!! Form::label('sub_category_id', __('product.sub_category')  . ':') !!}
                                <div class="input-group"> 
                                    {!! Form::select('sub_category_id', $sub_categories, $product->sub_category_id, ['placeholder' => __('messages.please_select'), 'class' => 'form-control select2']); !!}
                                  <span class="input-group-btn">
                                    <button type="button" @if(!auth()->user()->can('product.add_category')) disabled @endif class="btn btn-default bg-white btn-flat btn-modal" data-href="{{action('BrandController@createSub', ['quick_add' => true])}}" title="@lang('product.category')" data-container=".view_modal"><i class="fa fa-plus-circle text-primary fa-lg"></i></button>
                                  </span>
                                </div>
                              </div>
                            </div>
                            <div class="clearfix"></div>
                            @php
                            $default_location = null;
                              if(count($business_locations) == 1){
                                $default_location = array_key_first($business_locations->toArray());
                              } 
                             
                            @endphp 
                            <div class="col-sm-6 hide">
                              <div class="form-group">
                                {!! Form::label('product_locations', __('business.business_locations') . ':') !!} @show_tooltip(__('lang_v1.product_location_help'))
                                  {!! Form::select('product_locations[]', $business_locations, $product->product_locations->first()->id, ['class' => 'form-control select2', 'id' => 'product_locations']); !!}
                              </div>
                            </div>
                            <div class="clearfix"></div>
                            <div class="col-sm-6">
                              <div  @if($product->type == "combo") class="form-group hide" @else class="form-group" @endif>
                              <br>
                                <label>
                                  {!! Form::checkbox('enable_stock', 1, $product->enable_stock, ['class' => 'input-icheck', 'id' => 'enable_stock']); !!} <strong>@lang('product.manage_stock')</strong>
                                </label>@show_tooltip(__('tooltip.enable_stock')) <p class="help-block"><i>@lang('product.enable_stock_help')</i></p>
                              </div>
                            </div>
                            <div @if($product->type == "combo")  class="col-sm-6 hide" @else  class="col-sm-6"  @endif id="alert_quantity_div" @if(!$product->enable_stock) style="display:none" @endif>
                              <div class="form-group">
                                {!! Form::label('alert_quantity', __('product.alert_quantity') . ':') !!} @show_tooltip(__('tooltip.alert_quantity'))
                                {!! Form::number('alert_quantity', $product->alert_quantity, ['class' => 'form-control',
                                'placeholder' => __('product.alert_quantity') , 'min' => '0']); !!}
                              </div>
                            </div>
                            @if(!empty($common_settings['enable_product_warranty']))
                            <div class="col-sm-6">
                              <div class="form-group">
                                {!! Form::label('warranty_id', __('lang_v1.warranty') . ':') !!}
                                {!! Form::select('warranty_id', $warranties, $product->warranty_id, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select')]); !!}
                              </div>
                            </div>
                            @endif
            
                            <div class="clearfix"></div>
                            <div class="col-sm-12">
                              <div class="form-group">
                                {!! Form::label('product_description', __('lang_v1.product_description') . ':') !!}
                                {!! Form::textarea('product_description', $product->product_description, ['class' => 'form-control']); !!}
                              </div>
                            </div>
                            <div class="col-sm-12">
                              <div class="form-group">
                                {!! Form::label('full_description', __('izo.Full_description') . ':') !!}
                                  {!! Form::textarea('full_description', !empty($product->full_description) ? $product->full_description : null, ['class' => 'form-control']); !!}
                              </div>
                            </div>
                            
                          
                    @endcomponent 
                 
              {{-- *************************************** --}}

              {{-- *2* section product Media --}}
              {{-- *************************************** --}}
                 
                  @component('components.widget', ['class' => 'box-primary' , "title" =>__('izo.Product Media')])
                      <div class="row">
                          <div class="col-sm-6">
                            <div class="form-group">
                              {!! Form::label('image', __('lang_v1.product_image') . ':') !!}
                                {!! Form::file('image', ['id' => 'upload_image','accept' => implode(',', array_keys(config('constants.im_upload')))]); !!}
                              <small><p class="help-block">@lang('purchase.max_file_size', ['size' => (config('constants.document_size_limit') / 1000000)]). @lang('lang_v1.aspect_ratio_should_be_1_1') @if(!empty($product->image)) <br> @lang('lang_v1.previous_image_will_be_replaced') @endif<br> {{  "Allow Image : .jpeg , .jpg , .png"  }}</p></small>
                              <div class="img_sec">
                                <img width="100" src="{{$product->image_url}}">
                                @if(auth()->user()->hasRole('Admin#' . session('business.id')) || auth()->user()->can('delete_product_image'))
                                  @if( $product->image != null )
                                  <span onclick="delete_image({{$product->id}});"><i class="fa fas fa-trash" style="color: red;cursor:pointer"></i></span>
                                  @endif
                                @endif
                              </div>
                            </div>
                          </div>
                          @php
                            $default_location = null;
                            if(count($business_locations) == 1){
                              $default_location = array_key_first($business_locations->toArray());
                            }
                          @endphp
                          <div class="col-sm-6 hide">
                            <div class="form-group">
                              {!! Form::label('product_locations', __('business.business_locations') . ':') !!} @show_tooltip(__('lang_v1.product_location_help'))
                                {!! Form::select('product_locations[]', $business_locations, $default_location, ['class' => 'form-control select2', 'id' => 'product_locations']); !!}
                            </div>
                          </div>
                          <div class="col-sm-6">
                            <div class="form-group">
                              {!! Form::label('product_brochure', __('lang_v1.product_brochure') . ':') !!}
                              {!! Form::file('product_brochure', ['id' => 'product_brochure', 'accept' => implode(',', array_keys(config('constants.document_upload_mimes_types')))]); !!}
                              <small>
                                  <p class="help-block">
                                      @lang('lang_v1.previous_file_will_be_replaced')<br>
                                      @lang('purchase.max_file_size', ['size' => (config('constants.document_size_limit') / 1000000)])
                                      @includeIf('components.document_help_text')
                                  </p>
                              </small>
                            </div>
                          </div>
                          <div class="clearfix"></div>
                          <div class="col-md-6">
                                @foreach($product_deatails_parent->variations as $variation )
                                    @php 
                                        $action = !empty($action) ? $action : '';
                                    @endphp
                                    @if($action !== 'duplicate')
                                        @foreach($variation->media as $media)
                                            <div class="img-thumbnail">
                                                <span class="badge bg-red delete-media" data-href="{{ action('ProductController@deleteMedia', ['media_id' => $media->id])}}"><i class="fa fa-close"></i></span>
                                                {!! $media->thumbnail() !!}
                                            </div>
                                        @endforeach
                                    @endif
                                @endforeach
                                <div class="form-group">
                                    {!! Form::label('variation_images', __('lang_v1.product_image') . ':') !!} ++
                                    {!! Form::file('variation_images[]', ['class' => 'variation_images', "id"=>"variation_images",'accept' => 'image/*', 'multiple']); !!}
                                    <small><p class="help-block">@lang('purchase.max_file_size', ['size' => (config('constants.document_size_limit') / 1000000)]) <br> @lang('lang_v1.aspect_ratio_should_be_1_1')</p></small>
                                </div>
                          </div>
                          <div class="col-sm-6">
                            <div class="col-sm-12 text-center" style="padding-top:10px;border:1px solid #9f9f9f;border-radius:10px;background-color:#d6d6d6">
                                @php $vedi = json_decode($product->product_vedio)  ; $vedio = ""; @endphp
                                @if(isset($vedi) && $vedi != null )
                                @foreach ($vedi as $item)
                                  @php $vedio = $item ;  @endphp
                                @endforeach
                                <video controls width="450" height="250">
                                  <source src="{{ asset('storage/app/public/'.$vedio) }}" type="video/mp4">
                                  <source src="{{ asset('storage/app/public/'.$vedio) }}" type="video/webm">
                                  <source src="{{ asset('storage/app/public/'.$vedio) }}" type="video/ogg">
                                    <!-- Add more source tags for different video formats if needed -->
                                </video>
                                <br>
                                @endif
                              <div class="form-group text-left" style="background-color:#d6d6d6">
                                {!! Form::label('vedio', __('izo.video') . ':') !!}
                                {!! Form::file('vedio', ['id' => 'upload_video', 'accept' => 'video/*']); !!}
                                <small><p class="help-block">@lang('purchase.max_file_size', ['size' => (config('constants.vedio_size_limit') / 1000000)]) <br> @lang('lang_v1.aspect_ratio_should_be_1_1')</p></small>
                              </div>
                            </div>
                          </div>
                      </div>
                  @endcomponent 
                
              {{-- *************************************** --}}
              
              {{-- *3* section product  Additional Info --}}
              {{-- *************************************** --}}
                
                  @component('components.widget', ['class' => 'box-primary',"title" =>__('izo.Product Additional Info')])
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
                        <div class="col-sm-4 @if($hide) hide @endif">
                          <div class="form-group">
                            <div class="multi-input">
                              @php
                                $disabled = false;
                                $disabled_period = false;
                                if( empty($product->expiry_period_type) || empty($product->enable_stock) ){
                                  $disabled = true;
                                }
                                if( empty($product->enable_stock) ){
                                  $disabled_period = true;
                                }
                              @endphp
                                {!! Form::label('expiry_period', __('product.expires_in') . ':') !!}<br>
                                {!! Form::text('expiry_period', @num_format($product->expiry_period), ['class' => 'form-control pull-left input_number',
                                  'placeholder' => __('product.expiry_period'), 'style' => 'width:60%;', 'disabled' => $disabled]); !!}
                                {!! Form::select('expiry_period_type', ['months'=>__('product.months'), 'days'=>__('product.days'), '' =>__('product.not_applicable') ], $product->expiry_period_type, ['class' => 'form-control select2 pull-left', 'style' => 'width:40%;', 'id' => 'expiry_period_type', 'disabled' => $disabled_period]); !!}
                            </div>
                          </div>
                        </div>
                        @endif
                        <div class="col-sm-6">
                          <div class="checkbox">
                            {{-- <label>
                              {!! Form::checkbox('enable_sr_no', 1, $product->enable_sr_no, ['class' => 'input-icheck']); !!} <strong>@lang('lang_v1.enable_imei_or_sr_no')</strong>
                            </label>
                            @show_tooltip(__('lang_v1.tooltip_sr_no')) --}}
                            
                          <label>
                            {!! Form::checkbox('not_for_selling', 1, $product->not_for_selling, ['class' => 'input-icheck']); !!} <strong>@lang('lang_v1.not_for_selling')</strong>
                          </label> @show_tooltip(__('lang_v1.tooltip_not_for_selling'))
                          </div>
                        </div>

                        



                      <!-- Rack, Row & position number -->
                      @if(session('business.enable_racks') || session('business.enable_row') || session('business.enable_position'))
                        <div class="col-md-12">
                          <h4>@lang('lang_v1.rack_details'):
                            @show_tooltip(__('lang_v1.tooltip_rack_details'))
                          </h4>
                        </div>
                        @foreach($business_locations as $id => $location)
                          <div class="col-sm-6">
                            <div class="form-group">
                              {!! Form::label('rack_' . $id,  $location . ':') !!}

                              
                                @if(!empty($rack_details[$id]))
                                  @if(session('business.enable_racks'))
                                    {!! Form::text('product_racks_update[' . $id . '][rack]', $rack_details[$id]['rack'], ['class' => 'form-control', 'id' => 'rack_' . $id]); !!}
                                  @endif

                                  @if(session('business.enable_row'))
                                    {!! Form::text('product_racks_update[' . $id . '][row]', $rack_details[$id]['row'], ['class' => 'form-control']); !!}
                                  @endif

                                  @if(session('business.enable_position'))
                                    {!! Form::text('product_racks_update[' . $id . '][position]', $rack_details[$id]['position'], ['class' => 'form-control']); !!}
                                  @endif
                                @else
                                  {!! Form::text('product_racks[' . $id . '][rack]', null, ['class' => 'form-control', 'id' => 'rack_' . $id, 'placeholder' => __('lang_v1.rack')]); !!}

                                  {!! Form::text('product_racks[' . $id . '][row]', null, ['class' => 'form-control', 'placeholder' => __('lang_v1.row')]); !!}

                                  {!! Form::text('product_racks[' . $id . '][position]', null, ['class' => 'form-control', 'placeholder' => __('lang_v1.position')]); !!}
                                @endif

                            </div>
                          </div>
                        @endforeach
                      @endif
                      <div class="col-sm-12">
                        <div class="form-group">
                          {!! Form::label('weight',  __('lang_v1.weight') . ':') !!}
                          {!! Form::text('weight', $product->weight, ['class' => 'form-control', 'placeholder' => __('lang_v1.weight')]); !!}
                        </div>
                      </div>
                      <div class="clearfix"></div>
                    
                      <div class="clearfix"></div>
                      @php
                        $custom_labels = json_decode(session('business.custom_labels'), true);
                        $product_custom_field1 = !empty($custom_labels['product']['custom_field_1']) ? $custom_labels['product']['custom_field_1'] : __('lang_v1.product_custom_field1');
                        $product_custom_field2 = !empty($custom_labels['product']['custom_field_2']) ? $custom_labels['product']['custom_field_2'] : __('lang_v1.product_custom_field2');
                        $product_custom_field3 = !empty($custom_labels['product']['custom_field_3']) ? $custom_labels['product']['custom_field_3'] : __('lang_v1.product_custom_field3');
                        $product_custom_field4 = !empty($custom_labels['product']['custom_field_4']) ? $custom_labels['product']['custom_field_4'] : __('lang_v1.product_custom_field4');
                      @endphp
                      <!--custom fields-->
                      <div class="col-sm-3">
                        <div class="form-group">
                          {!! Form::label('product_custom_field1',  $product_custom_field1 . ':') !!}
                          {!! Form::text('product_custom_field1', $product->product_custom_field1, ['class' => 'form-control', 'placeholder' => $product_custom_field1]); !!}
                        </div>
                      </div>

                      <div class="col-sm-3">
                        <div class="form-group">
                          {!! Form::label('product_custom_field2',  $product_custom_field2 . ':') !!}
                          {!! Form::text('product_custom_field2', $product->product_custom_field2, ['class' => 'form-control', 'placeholder' => $product_custom_field2]); !!}
                        </div>
                      </div>

                      <div class="col-sm-3">
                        <div class="form-group">
                          {!! Form::label('product_custom_field3',  $product_custom_field3 . ':') !!}
                          {!! Form::text('product_custom_field3', $product->product_custom_field3, ['class' => 'form-control', 'placeholder' => $product_custom_field3]); !!}
                        </div>
                      </div>

                      <div class="col-sm-3">
                        <div class="form-group">
                          {!! Form::label('product_custom_field4',  $product_custom_field4 . ':') !!}
                          {!! Form::text('product_custom_field4', $product->product_custom_field4, ['class' => 'form-control', 'placeholder' => $product_custom_field4]); !!}
                        </div>
                      </div>
                      <!--custom fields-->
                      {{-- @include('layouts.partials.module_form_part') --}}
                      </div>
                  @endcomponent
                 
              {{-- *************************************** --}}
              
              {{-- *4* section product Prices --}}
              {{-- *************************************** --}}
                 
                  @component('components.widget', ['class' => 'box-primary',"title"=>__('izo.Product Prices')])
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

                          <div class="col-sm-6 hide ">
                            <div class="form-group">
                              {!! Form::label('tax_type', __('product.selling_price_tax_type') . ':*') !!}
                                {!! Form::select('tax_type',['inclusive' => __('product.inclusive'), 'exclusive' => __('product.exclusive')], $product->tax_type,
                                ['class' => 'form-control select2', 'required']); !!}
                            </div>
                          </div>


                          <div class="col-sm-6">
                            <div class="form-group">
                              {!! Form::label('type', __('product.product_type') . ':*') !!} @show_tooltip(__('tooltip.product_type'))
                              {!! Form::select('type', $product_types, $product->type, ['class' => 'form-control select2',
                                'required','disabled', 'data-action' => 'edit', 'data-product_id' => $product->id ]); !!}
                            </div>
                          </div>

                          <div   @if(auth()->user()->hasRole('Admin#' . session('business.id')) ||  auth()->user()->can('product.avarage_cost'))  class="form-group col-sm-12"  @else class="form-group col-sm-12 hide" @endif id="product_form_part"></div>
                          <input type="hidden" id="variation_counter" value="0">
                          <input type="hidden" id="default_profit_percent" value="{{ $default_profit_percent }}">
                      </div>
                  @endcomponent
               
              {{-- *************************************** --}}
  
              {{-- *5* section modal --}}
              {{-- *************************************** --}}
               
                  <input type="hidden" name="submit_type" id="submit_type">
                      {{-- *1* section save  --}}
                      {{-- *************************************** --}}
                        <div class="col-sm-12">
                          <div class="text-center">
                            <div class="btn-group">
                              @if($selling_price_group_count)
                                <button type="submit" value="submit_n_add_selling_prices" class="btn btn-warning submit_product_form">@lang('lang_v1.save_n_add_selling_price_group_prices')</button>
                              @endif

                              @can('product.opening_stock')
                              <button type="submit" @if(empty($product->enable_stock)) disabled="true" @endif id="opening_stock_button"  value="update_n_edit_opening_stock" class="btn bg-purple submit_product_form">@lang('lang_v1.update_n_edit_opening_stock')</button>
                              @endif

                              <button type="submit" value="save_n_add_another" class="btn bg-maroon submit_product_form">@lang('lang_v1.update_n_add_another')</button>

                              <button type="submit" value="submit" class="btn btn-primary submit_product_form">@lang('messages.update')</button>
                            </div>
                          </div>
                        </div>
                      {{-- *************************************** --}}
                      {{-- *2* section modal --}}
                      {{-- *************************************** --}}
                        <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                          @include('unit.edit_add' , ["product"=> $product,'unitall'=>$unitall])
                        </div>
                      {{-- *************************************** --}}
                
              {{-- *************************************** --}}

              {!! Form::close() !!}
      </section>
    {{-- *************************************** --}}
    <!-- /.content -->
@endsection

{{-- *4* --}}
@section('javascript')
  
  {{-- section relation --}}
  <script src="{{ asset('js/producte.js?v=' . $asset_v) }}"></script>
  {{-- section additional --}}
  <script type="text/javascript">
      //.. Previous Code
      $(document).ready( function(){
        __page_leave_confirmation('#product_add_form');
       
      });
    
      //.. Product - Price - Functions ..\\
      //.. *************************** .. \\ 
      
      //..1 Add Row
      function add_row(){
            i = 1;
           
            var row_count = parseFloat($(".number-of-row").val())   ;
            $html  =  '<tr>'+
                      '<td ><input  type="hidden"    name="line_id[]" class="form-control line_id" value="null"  ><input  type="text" name="name_add[]"  class="form-control name_price" value="" placeholder="{{__('messages.please_enter_name')}}"></td>'+
                      '<td ><input  type="text" name="price[]" class="form-control amount_price" value="" placeholder="{{__('messages.please_enter_price')}}"></td>';
            $html +=  '<td class="multi-input column_1  currency[1]" >'+
                      '<input  type="text" name="currency_price[1][]" class="form-control amount_price width-70 pull-left" min="0" style="width:60% ;display:inline-block"  value="0" placeholder="{{__('messages.please_enter_price')}}">'+
                      '{{ Form::select('currency_amount_price[1][]',$currencies,null,['class'=>'form-control amount_price_s width-30 pull-left select2 ' ,'style'=>'width:40%' ,'placeholder'=>__('messages.please_select') ]) }}</td>'+
                      '</td>';
            $head  =  '<th >'+ '{{__('lang_v1.price_in_currency')}} &nbsp;&nbsp;<i class="fa fas fa-trash" onClick="delete_column(this);"></i>'+ '</th>';
            while (i < $(".number-of-column").val()) {
                count = i + 1;
                $html +=  '<td class="multi-input ceil_'+count+' column_'+count+' currency['+count+']" >'+
                          '<input  class="ceil" hidden value="'+count+'">'+
                          '<input  type="text" name="currency_price['+count+'][]" class="form-control amount_price width-70 pull-left" min="0" style="width:60% ;display:inline-block"  value="0" placeholder="{{__('messages.please_enter_price')}}">'+
                          '{{ Form::select('',$currencies,null,['class'=>'form-control amount_price_s width-30 pull-left select2' ,'style'=>'width:40%' ,'placeholder'=>__('messages.please_select') ]) }}</td>'+
                          '</td>';
                $head =   '<th >'+ '{{__('lang_v1.price_in_currency')}} &nbsp;&nbsp;<i class="fa fas fa-trash" onClick="delete_column(this);"></i>'+ '</th>';
              i++;
            }
            $html +='<td class="last_column delete_row" onClick="delete_row(this);"><i class="fa fas fa-trash"></i></td>'+
                    '</tr>';
              $("table#product_price tbody").append($html);
              $(".number-of-row").val(row_count) ;
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
              
                      $html = '<td class="multi-input ceil_'+column_count+' column_'+column_count+'  currency['+column_count+']" >'+
                        '<input  class="ceil" hidden value="'+column_count+'">'+
                        '<input  type="text" name="currency_price['+column_count+'][]" class="form-control amount_price width-70 pull-left" min="0" style="width:60% ;display:inline-block"  value="0" placeholder="{{__('messages.please_enter_price')}}">'+
                        '{{ Form::select('',$currencies,null,['class'=>'form-control amount_price_s width-30 pull-left select2' ,'style'=>"width:40%" ,'placeholder'=>__('messages.please_select') ]) }}</td>'+
                        '</td>';
                     $head = '<th class="column_'+column_count+'">'+
                      '{{__('lang_v1.price_in_currency')}} &nbsp;&nbsp;<i class="fa fas fa-trash" onClick="delete_column(this);"></i>'+
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
        $(".number-of-row").attr("value",row_count);
        column = $(e).parent().attr("class");
        count = 0;
        counts = 0;
        $("table#product_price th").each(function(){
           
          if(( $(this).attr("class") != "last_column") &&  (  $(this).attr("class") != column ) && ($(this).attr("class")!=null) ){
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
            if(( $(this).attr("class") != "last_column delete_row" && $(this).attr("class") != "last_column"   ) &&  (  $(this).attr("class") != column ) && ($(this).attr("class")!=null)   ){
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
          $html += '<td style="vertical-align: middle;">'+
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
      ready();
      
      function delete_image(id){
            
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
                          html = '<input type="text" hidden value="delete_header_image" id="delete_header_image" name="delete_header_image">';
                          $(".img_sec").html(html);
                    },
                  });
                }
            });
        
        
      }
  </script>
@endsection