@if(!session('business.enable_price_tax')) 
  @php
    $default = 0;
    $class = 'hide';
  @endphp
@else
  @php
    $default = null;
    $class = '';
  @endphp
@endif
@php $count =0 ;    @endphp
<div class="col-sm-12"><br>
  @if(($empty)==0)
    @foreach($other_unit as $k => $i)
        @php $count++ ; $list_new = [];  @endphp
        <div class="packages" data-is_exist="{{isset($list_unit[$i])}}" data-value="{{$i}}">
              @foreach($units_main as $n => $row_line)
                  @if($i == $n)
                    @php 
                      $list_new[$n] = $row_line; 
                    @endphp
                  @endif
              @endforeach
            {!! Form::select('unit_D[]',$list_new ,  (isset($list_unit[$i]))?$i:null  , ['class' => 'form-control input-sm width-10 dpp un_select input_number select2','id'=>'unit_D',"data-id"=>$k,"style"=>"width:10%" , 'placeholder' => __('messages.please_select')     ]); !!}
            <div class="table-responsive">
              <table class="table table-bordered add-product-price-table table-condensed {{$class}}">
                  <head>
                      <tr>
                        <th style="vertical-align: bottom">
                          {{__('home.Type Price')}}
                        </th>
                        <th>@lang('product.default_purchase_price')</th>
                        <th>@lang('product.profit_percent') @show_tooltip(__('tooltip.profit_percent'))</th>
                        <th>@lang('product.default_selling_price')</th>
                      </tr>
                  </head>
                  <body>
                    {{-- 1 default_price --}}
                      @php $product_prices = \App\Models\ProductPrice::where("product_id",$product_details->product_id)->where("number_of_default",0)->where("unit_id",$i)->first(); @endphp
                      <tr>
                          <td style="vertical-align: bottom"> {{ (!empty($product_prices))?$product_prices->name:"Default Price" }} </td>
                          <td>
                              @php $varid = null; foreach ($product_details->variations as $key => $var) { $varid  =   $var->id; } @endphp
                              <input type="hidden" name="single_variation_id1" value="{{$varid}}">
                              <div class="col-sm-6">
                                {!! Form::label('single_dpp', trans('product.exc_of_tax') . ':*') !!}
                                {!! Form::text('single_dpp'.$count.'[]', @num_format((!empty($product_prices))?$product_prices->default_purchase_price:0), ['class' => 'form-control input-sm dpp input_number', 'placeholder' => __('product.exc_of_tax'),'id' => 'single_dpp'.$count, 'required']); !!}
                              </div>
                              <div class="col-sm-6">
                                {!! Form::label('single_dpp_inc_tax', trans('product.inc_of_tax') . ':*') !!}
                                {!! Form::text('single_dpp_inc_tax'.$count.'[]', @num_format((!empty($product_prices))?$product_prices->dpp_inc_tax:0), ['class' => 'form-control input-sm dpp_inc_tax input_number', 'placeholder' => __('product.inc_of_tax'),'id' => 'single_dpp_inc_tax'.$count, 'required']); !!}
                              </div>
                          </td>
                          <td> <br/> {!! Form::text('profit_percent'.$count.'[]', @num_format((!empty($product_prices))?$product_prices->profit_percent:0), ['class' => 'form-control input-sm input_number', 'id' => 'profit_percent'.$count, 'required']); !!}</td>
                          <td>
                              <div class="col-sm-6">
                                  {!! Form::label('single_dpp', trans('product.exc_of_tax') . ':*') !!}
                                  {!! Form::text('single_dsp'.$count.'[]', @num_format((!empty($product_prices))?$product_prices->default_sell_price:0), ['class' => 'form-control input-sm  input_number', 'placeholder' => __('product.exc_of_tax'), 'id' => 'single_dsp'.$count, 'required']); !!}
                              </div>

                              <div class="col-sm-6">
                                {!! Form::label('single_dpp_inc_tax', trans('product.inc_of_tax') . ':*') !!}
                                {!! Form::text('single_dsp_inc_tax'.$count.'[]', @num_format((!empty($product_prices))?$product_prices->sell_price_inc_tax:0), ['class' => 'form-control input-sm  input_number', 'placeholder' => __('product.inc_of_tax'), 'id' => 'single_dsp_inc_tax'.$count, 'required']); !!}
                              </div>
                          </td>
                      </tr>        
                    {{-- 2 whole_price --}}
                      @php $product_prices = \App\Models\ProductPrice::where("product_id",$product_details->product_id)->where("number_of_default",1)->where("unit_id",$i)->first(); @endphp
                      <tr>
                          <td style="vertical-align: bottom"> {{ (!empty($product_prices))?$product_prices->name:"Whole Price" }} </td>
                          <td>
                              @php $varid = null; foreach ($product_details->variations as $key => $var) { $varid  =   $var->id; } @endphp
                              <input type="hidden" name="single_variation_id1" value="{{$varid}}">
                              <div class="col-sm-6">
                                {!! Form::label('single_dpp', trans('product.exc_of_tax') . ':*') !!}
                                {!! Form::text('single_dpp'.$count.'[]', @num_format((!empty($product_prices))?$product_prices->default_purchase_price:0), ['class' => 'form-control input-sm dpp input_number', 'placeholder' => __('product.exc_of_tax'),'id' => 'single_dpp'.$count, 'required']); !!}
                              </div>
                              <div class="col-sm-6">
                                {!! Form::label('single_dpp_inc_tax', trans('product.inc_of_tax') . ':*') !!}
                                {!! Form::text('single_dpp_inc_tax'.$count.'[]', @num_format((!empty($product_prices))?$product_prices->dpp_inc_tax:0), ['class' => 'form-control input-sm dpp_inc_tax input_number', 'placeholder' => __('product.inc_of_tax'),'id' => 'single_dpp_inc_tax'.$count, 'required']); !!}
                              </div>
                          </td>
                          <td> <br/> {!! Form::text('profit_percent'.$count.'[]', @num_format((!empty($product_prices))?$product_prices->profit_percent:0), ['class' => 'form-control input-sm input_number', 'id' => 'profit_percent'.$count, 'required']); !!}</td>
                          <td>
                              <div class="col-sm-6">
                                  {!! Form::label('single_dpp', trans('product.exc_of_tax') . ':*') !!}
                                  {!! Form::text('single_dsp'.$count.'[]', @num_format((!empty($product_prices))?$product_prices->default_sell_price:0), ['class' => 'form-control input-sm  input_number', 'placeholder' => __('product.exc_of_tax'), 'id' => 'single_dsp'.$count, 'required']); !!}
                              </div>

                              <div class="col-sm-6">
                                {!! Form::label('single_dpp_inc_tax', trans('product.inc_of_tax') . ':*') !!}
                                {!! Form::text('single_dsp_inc_tax'.$count.'[]', @num_format((!empty($product_prices))?$product_prices->sell_price_inc_tax:0), ['class' => 'form-control input-sm  input_number', 'placeholder' => __('product.inc_of_tax'), 'id' => 'single_dsp_inc_tax'.$count, 'required']); !!}
                              </div>
                          </td>
                      </tr>          
                    {{-- 3 retail_price --}}
                      @php $product_prices = \App\Models\ProductPrice::where("product_id",$product_details->product_id)->where("number_of_default",2)->where("unit_id",$i)->first(); @endphp
                      <tr>
                        <td style="vertical-align: bottom"> {{ (!empty($product_prices))?$product_prices->name:"Retail Price" }} </td>
                        <td>
                            @php $varid = null; foreach ($product_details->variations as $key => $var) { $varid  =   $var->id; } @endphp
                            <input type="hidden" name="single_variation_id1" value="{{$varid}}">
                            <div class="col-sm-6">
                              {!! Form::label('single_dpp', trans('product.exc_of_tax') . ':*') !!}
                              {!! Form::text('single_dpp'.$count.'[]', @num_format((!empty($product_prices))?$product_prices->default_purchase_price:0), ['class' => 'form-control input-sm dpp input_number', 'placeholder' => __('product.exc_of_tax'),'id' => 'single_dpp'.$count, 'required']); !!}
                            </div>
                            <div class="col-sm-6">
                              {!! Form::label('single_dpp_inc_tax', trans('product.inc_of_tax') . ':*') !!}
                              {!! Form::text('single_dpp_inc_tax'.$count.'[]', @num_format((!empty($product_prices))?$product_prices->dpp_inc_tax:0), ['class' => 'form-control input-sm dpp_inc_tax input_number', 'placeholder' => __('product.inc_of_tax'),'id' => 'single_dpp_inc_tax'.$count, 'required']); !!}
                            </div>
                        </td>
                        <td> <br/> {!! Form::text('profit_percent'.$count.'[]', @num_format((!empty($product_prices))?$product_prices->profit_percent:0), ['class' => 'form-control input-sm input_number', 'id' => 'profit_percent'.$count, 'required']); !!}</td>
                        <td>
                            <div class="col-sm-6">
                                {!! Form::label('single_dpp', trans('product.exc_of_tax') . ':*') !!}
                                {!! Form::text('single_dsp'.$count.'[]', @num_format((!empty($product_prices))?$product_prices->default_sell_price:0), ['class' => 'form-control input-sm  input_number', 'placeholder' => __('product.exc_of_tax'), 'id' => 'single_dsp'.$count, 'required']); !!}
                            </div>

                            <div class="col-sm-6">
                              {!! Form::label('single_dpp_inc_tax', trans('product.inc_of_tax') . ':*') !!}
                              {!! Form::text('single_dsp_inc_tax'.$count.'[]', @num_format((!empty($product_prices))?$product_prices->sell_price_inc_tax:0), ['class' => 'form-control input-sm  input_number', 'placeholder' => __('product.inc_of_tax'), 'id' => 'single_dsp_inc_tax'.$count, 'required']); !!}
                            </div>
                        </td>
                      </tr> 
                    {{-- 4 minimum_price --}}
                      @php $product_prices = \App\Models\ProductPrice::where("product_id",$product_details->product_id)->where("number_of_default",3)->where("unit_id",$i)->first(); @endphp
                      <tr>
                        <td style="vertical-align: bottom"> {{ (!empty($product_prices))?$product_prices->name:"Minimum Price" }} </td>
                        <td>
                            @php $varid = null; foreach ($product_details->variations as $key => $var) { $varid  =   $var->id; } @endphp
                            <input type="hidden" name="single_variation_id1" value="{{$varid}}">
                            <div class="col-sm-6">
                              {!! Form::label('single_dpp', trans('product.exc_of_tax') . ':*') !!}
                              {!! Form::text('single_dpp'.$count.'[]', @num_format((!empty($product_prices))?$product_prices->default_purchase_price:0), ['class' => 'form-control input-sm dpp input_number', 'placeholder' => __('product.exc_of_tax'),'id' => 'single_dpp'.$count, 'required']); !!}
                            </div>
                            <div class="col-sm-6">
                              {!! Form::label('single_dpp_inc_tax', trans('product.inc_of_tax') . ':*') !!}
                              {!! Form::text('single_dpp_inc_tax'.$count.'[]', @num_format((!empty($product_prices))?$product_prices->dpp_inc_tax:0), ['class' => 'form-control input-sm dpp_inc_tax input_number', 'placeholder' => __('product.inc_of_tax'),'id' => 'single_dpp_inc_tax'.$count, 'required']); !!}
                            </div>
                        </td>
                        <td> <br/> {!! Form::text('profit_percent'.$count.'[]', @num_format((!empty($product_prices))?$product_prices->profit_percent:0), ['class' => 'form-control input-sm input_number', 'id' => 'profit_percent'.$count, 'required']); !!}</td>
                        <td>
                            <div class="col-sm-6">
                                {!! Form::label('single_dpp', trans('product.exc_of_tax') . ':*') !!}
                                {!! Form::text('single_dsp'.$count.'[]', @num_format((!empty($product_prices))?$product_prices->default_sell_price:0), ['class' => 'form-control input-sm  input_number', 'placeholder' => __('product.exc_of_tax'), 'id' => 'single_dsp'.$count, 'required']); !!}
                            </div>

                            <div class="col-sm-6">
                              {!! Form::label('single_dpp_inc_tax', trans('product.inc_of_tax') . ':*') !!}
                              {!! Form::text('single_dsp_inc_tax'.$count.'[]', @num_format((!empty($product_prices))?$product_prices->sell_price_inc_tax:0), ['class' => 'form-control input-sm  input_number', 'placeholder' => __('product.inc_of_tax'), 'id' => 'single_dsp_inc_tax'.$count, 'required']); !!}
                            </div>
                        </td>
                      </tr>
                    {{-- 5 last_price --}}
                      @php $product_prices = \App\Models\ProductPrice::where("product_id",$product_details->product_id)->where("number_of_default",4)->where("unit_id",$i)->first(); @endphp
                      <tr>
                        <td style="vertical-align: bottom"> {{ (!empty($product_prices))?$product_prices->name:"Last Price" }} </td>
                        <td>
                            @php $varid = null; foreach ($product_details->variations as $key => $var) { $varid  =   $var->id; } @endphp
                            <input type="hidden" name="single_variation_id1" value="{{$varid}}">
                            <div class="col-sm-6">
                              {!! Form::label('single_dpp', trans('product.exc_of_tax') . ':*') !!}
                              {!! Form::text('single_dpp'.$count.'[]', @num_format((!empty($product_prices))?$product_prices->default_purchase_price:0), ['class' => 'form-control input-sm dpp input_number', 'placeholder' => __('product.exc_of_tax'),'id' => 'single_dpp'.$count, 'required']); !!}
                            </div>
                            <div class="col-sm-6">
                              {!! Form::label('single_dpp_inc_tax', trans('product.inc_of_tax') . ':*') !!}
                              {!! Form::text('single_dpp_inc_tax'.$count.'[]', @num_format((!empty($product_prices))?$product_prices->dpp_inc_tax:0), ['class' => 'form-control input-sm dpp_inc_tax input_number', 'placeholder' => __('product.inc_of_tax'),'id' => 'single_dpp_inc_tax'.$count, 'required']); !!}
                            </div>
                        </td>
                        <td> <br/> {!! Form::text('profit_percent'.$count.'[]', @num_format((!empty($product_prices))?$product_prices->profit_percent:0), ['class' => 'form-control input-sm input_number', 'id' => 'profit_percent'.$count, 'required']); !!}</td>
                        <td>
                            <div class="col-sm-6">
                                {!! Form::label('single_dpp', trans('product.exc_of_tax') . ':*') !!}
                                {!! Form::text('single_dsp'.$count.'[]', @num_format((!empty($product_prices))?$product_prices->default_sell_price:0), ['class' => 'form-control input-sm  input_number', 'placeholder' => __('product.exc_of_tax'), 'id' => 'single_dsp'.$count, 'required']); !!}
                            </div>

                            <div class="col-sm-6">
                              {!! Form::label('single_dpp_inc_tax', trans('product.inc_of_tax') . ':*') !!}
                              {!! Form::text('single_dsp_inc_tax'.$count.'[]', @num_format((!empty($product_prices))?$product_prices->sell_price_inc_tax:0), ['class' => 'form-control input-sm  input_number', 'placeholder' => __('product.inc_of_tax'), 'id' => 'single_dsp_inc_tax'.$count, 'required']); !!}
                            </div>
                        </td>
                      </tr>
                    {{-- 6 ECM Before Price --}}
                      @php $product_prices = \App\Models\ProductPrice::where("product_id",$product_details->product_id)->where("number_of_default",5)->where("unit_id",$i)->first(); @endphp
                      <tr>
                        <td style="vertical-align: bottom"> {{ (!empty($product_prices))?$product_prices->name:"ECM Before Price" }} </td>
                        <td>
                            @php $varid = null; foreach ($product_details->variations as $key => $var) { $varid  =   $var->id; } @endphp
                            <input type="hidden" name="single_variation_id1" value="{{$varid}}">
                            <div class="col-sm-6">
                              {!! Form::label('single_dpp', trans('product.exc_of_tax') . ':*') !!}
                              {!! Form::text('single_dpp'.$count.'[]', @num_format((!empty($product_prices))?$product_prices->default_purchase_price:0), ['class' => 'form-control input-sm dpp input_number', 'placeholder' => __('product.exc_of_tax'),'id' => 'single_dpp'.$count, 'required']); !!}
                            </div>
                            <div class="col-sm-6">
                              {!! Form::label('single_dpp_inc_tax', trans('product.inc_of_tax') . ':*') !!}
                              {!! Form::text('single_dpp_inc_tax'.$count.'[]', @num_format((!empty($product_prices))?$product_prices->dpp_inc_tax:0), ['class' => 'form-control input-sm dpp_inc_tax input_number', 'placeholder' => __('product.inc_of_tax'),'id' => 'single_dpp_inc_tax'.$count, 'required']); !!}
                            </div>
                        </td>
                        <td> <br/> {!! Form::text('profit_percent'.$count.'[]', @num_format((!empty($product_prices))?$product_prices->profit_percent:0), ['class' => 'form-control input-sm input_number', 'id' => 'profit_percent'.$count, 'required']); !!}</td>
                        <td>
                            <div class="col-sm-6">
                                {!! Form::label('single_dpp', trans('product.exc_of_tax') . ':*') !!}
                                {!! Form::text('single_dsp'.$count.'[]', @num_format((!empty($product_prices))?$product_prices->default_sell_price:0), ['class' => 'form-control input-sm  input_number', 'placeholder' => __('product.exc_of_tax'), 'id' => 'single_dsp'.$count, 'required']); !!}
                            </div>

                            <div class="col-sm-6">
                              {!! Form::label('single_dpp_inc_tax', trans('product.inc_of_tax') . ':*') !!}
                              {!! Form::text('single_dsp_inc_tax'.$count.'[]', @num_format((!empty($product_prices))?$product_prices->sell_price_inc_tax:0), ['class' => 'form-control input-sm  input_number', 'placeholder' => __('product.inc_of_tax'), 'id' => 'single_dsp_inc_tax'.$count, 'required']); !!}
                            </div>
                        </td>
                      </tr>
                    {{-- 7 ECM After Price --}}
                      @php $product_prices = \App\Models\ProductPrice::where("product_id",$product_details->product_id)->where("number_of_default",6)->where("unit_id",$i)->first(); @endphp
                      <tr>
                        <td style="vertical-align: bottom"> {{ (!empty($product_prices))?$product_prices->name:"ECM After Price" }} </td>
                        <td>
                            @php $varid = null; foreach ($product_details->variations as $key => $var) { $varid  =   $var->id; } @endphp
                            <input type="hidden" name="single_variation_id1" value="{{$varid}}">
                            <div class="col-sm-6">
                              {!! Form::label('single_dpp', trans('product.exc_of_tax') . ':*') !!}
                              {!! Form::text('single_dpp'.$count.'[]', @num_format((!empty($product_prices))?$product_prices->default_purchase_price:0), ['class' => 'form-control input-sm dpp input_number', 'placeholder' => __('product.exc_of_tax'),'id' => 'single_dpp'.$count, 'required']); !!}
                            </div>
                            <div class="col-sm-6">
                              {!! Form::label('single_dpp_inc_tax', trans('product.inc_of_tax') . ':*') !!}
                              {!! Form::text('single_dpp_inc_tax'.$count.'[]', @num_format((!empty($product_prices))?$product_prices->dpp_inc_tax:0), ['class' => 'form-control input-sm dpp_inc_tax input_number', 'placeholder' => __('product.inc_of_tax'),'id' => 'single_dpp_inc_tax'.$count, 'required']); !!}
                            </div>
                        </td>
                        <td> <br/> {!! Form::text('profit_percent'.$count.'[]', @num_format((!empty($product_prices))?$product_prices->profit_percent:0), ['class' => 'form-control input-sm input_number', 'id' => 'profit_percent'.$count, 'required']); !!}</td>
                        <td>
                            <div class="col-sm-6">
                                {!! Form::label('single_dpp', trans('product.exc_of_tax') . ':*') !!}
                                {!! Form::text('single_dsp'.$count.'[]', @num_format((!empty($product_prices))?$product_prices->default_sell_price:0), ['class' => 'form-control input-sm  input_number', 'placeholder' => __('product.exc_of_tax'), 'id' => 'single_dsp'.$count, 'required']); !!}
                            </div>

                            <div class="col-sm-6">
                              {!! Form::label('single_dpp_inc_tax', trans('product.inc_of_tax') . ':*') !!}
                              {!! Form::text('single_dsp_inc_tax'.$count.'[]', @num_format((!empty($product_prices))?$product_prices->sell_price_inc_tax:0), ['class' => 'form-control input-sm  input_number', 'placeholder' => __('product.inc_of_tax'), 'id' => 'single_dsp_inc_tax'.$count, 'required']); !!}
                            </div>
                        </td>
                      </tr>
                    {{-- 8 custom_price 1 --}}
                      @php $product_prices = \App\Models\ProductPrice::where("product_id",$product_details->product_id)->where("number_of_default",7)->where("unit_id",$i)->first(); @endphp
                      <tr>
                        <td style="vertical-align: bottom"> {{ (!empty($product_prices))?$product_prices->name:"Custom Price 1" }} </td>
                        <td>
                            @php $varid = null; foreach ($product_details->variations as $key => $var) { $varid  =   $var->id; } @endphp
                            <input type="hidden" name="single_variation_id1" value="{{$varid}}">
                            <div class="col-sm-6">
                              {!! Form::label('single_dpp', trans('product.exc_of_tax') . ':*') !!}
                              {!! Form::text('single_dpp'.$count.'[]', @num_format((!empty($product_prices))?$product_prices->default_purchase_price:0), ['class' => 'form-control input-sm dpp input_number', 'placeholder' => __('product.exc_of_tax'),'id' => 'single_dpp'.$count, 'required']); !!}
                            </div>
                            <div class="col-sm-6">
                              {!! Form::label('single_dpp_inc_tax', trans('product.inc_of_tax') . ':*') !!}
                              {!! Form::text('single_dpp_inc_tax'.$count.'[]', @num_format((!empty($product_prices))?$product_prices->dpp_inc_tax:0), ['class' => 'form-control input-sm dpp_inc_tax input_number', 'placeholder' => __('product.inc_of_tax'),'id' => 'single_dpp_inc_tax'.$count, 'required']); !!}
                            </div>
                        </td>
                        <td> <br/> {!! Form::text('profit_percent'.$count.'[]', @num_format((!empty($product_prices))?$product_prices->profit_percent:0), ['class' => 'form-control input-sm input_number', 'id' => 'profit_percent'.$count, 'required']); !!}</td>
                        <td>
                            <div class="col-sm-6">
                                {!! Form::label('single_dpp', trans('product.exc_of_tax') . ':*') !!}
                                {!! Form::text('single_dsp'.$count.'[]', @num_format((!empty($product_prices))?$product_prices->default_sell_price:0), ['class' => 'form-control input-sm  input_number', 'placeholder' => __('product.exc_of_tax'), 'id' => 'single_dsp'.$count, 'required']); !!}
                            </div>

                            <div class="col-sm-6">
                              {!! Form::label('single_dpp_inc_tax', trans('product.inc_of_tax') . ':*') !!}
                              {!! Form::text('single_dsp_inc_tax'.$count.'[]', @num_format((!empty($product_prices))?$product_prices->sell_price_inc_tax:0), ['class' => 'form-control input-sm  input_number', 'placeholder' => __('product.inc_of_tax'), 'id' => 'single_dsp_inc_tax'.$count, 'required']); !!}
                            </div>
                        </td>
                      </tr>
                    {{-- 9 custom_price 2 --}}
                      @php $product_prices = \App\Models\ProductPrice::where("product_id",$product_details->product_id)->where("number_of_default",8)->where("unit_id",$i)->first(); @endphp
                      <tr>
                        <td style="vertical-align: bottom"> {{ (!empty($product_prices))?$product_prices->name:"Custom Price 2" }} </td>
                        <td>
                            @php $varid = null; foreach ($product_details->variations as $key => $var) { $varid  =   $var->id; } @endphp
                            <input type="hidden" name="single_variation_id1" value="{{$varid}}">
                            <div class="col-sm-6">
                              {!! Form::label('single_dpp', trans('product.exc_of_tax') . ':*') !!}
                              {!! Form::text('single_dpp'.$count.'[]', @num_format((!empty($product_prices))?$product_prices->default_purchase_price:0), ['class' => 'form-control input-sm dpp input_number', 'placeholder' => __('product.exc_of_tax'),'id' => 'single_dpp'.$count, 'required']); !!}
                            </div>
                            <div class="col-sm-6">
                              {!! Form::label('single_dpp_inc_tax', trans('product.inc_of_tax') . ':*') !!}
                              {!! Form::text('single_dpp_inc_tax'.$count.'[]', @num_format((!empty($product_prices))?$product_prices->dpp_inc_tax:0), ['class' => 'form-control input-sm dpp_inc_tax input_number', 'placeholder' => __('product.inc_of_tax'),'id' => 'single_dpp_inc_tax'.$count, 'required']); !!}
                            </div>
                        </td>
                        <td> <br/> {!! Form::text('profit_percent'.$count.'[]', @num_format((!empty($product_prices))?$product_prices->profit_percent:0), ['class' => 'form-control input-sm input_number', 'id' => 'profit_percent'.$count, 'required']); !!}</td>
                        <td>
                            <div class="col-sm-6">
                                {!! Form::label('single_dpp', trans('product.exc_of_tax') . ':*') !!}
                                {!! Form::text('single_dsp'.$count.'[]', @num_format((!empty($product_prices))?$product_prices->default_sell_price:0), ['class' => 'form-control input-sm  input_number', 'placeholder' => __('product.exc_of_tax'), 'id' => 'single_dsp'.$count, 'required']); !!}
                            </div>

                            <div class="col-sm-6">
                              {!! Form::label('single_dpp_inc_tax', trans('product.inc_of_tax') . ':*') !!}
                              {!! Form::text('single_dsp_inc_tax'.$count.'[]', @num_format((!empty($product_prices))?$product_prices->sell_price_inc_tax:0), ['class' => 'form-control input-sm  input_number', 'placeholder' => __('product.inc_of_tax'), 'id' => 'single_dsp_inc_tax'.$count, 'required']); !!}
                            </div>
                        </td>
                      </tr>
                    {{-- 10 custom_price 3 --}}
                      @php $product_prices = \App\Models\ProductPrice::where("product_id",$product_details->product_id)->where("number_of_default",9)->where("unit_id",$i)->first(); @endphp
                      <tr>
                        <td style="vertical-align: bottom"> {{ (!empty($product_prices))?$product_prices->name:"Custom Price 3" }} </td>
                        <td>
                            @php $varid = null; foreach ($product_details->variations as $key => $var) { $varid  =   $var->id; } @endphp
                            <input type="hidden" name="single_variation_id1" value="{{$varid}}">
                            <div class="col-sm-6">
                              {!! Form::label('single_dpp', trans('product.exc_of_tax') . ':*') !!}
                              {!! Form::text('single_dpp'.$count.'[]', @num_format((!empty($product_prices))?$product_prices->default_purchase_price:0), ['class' => 'form-control input-sm dpp input_number', 'placeholder' => __('product.exc_of_tax'),'id' => 'single_dpp'.$count, 'required']); !!}
                            </div>
                            <div class="col-sm-6">
                              {!! Form::label('single_dpp_inc_tax', trans('product.inc_of_tax') . ':*') !!}
                              {!! Form::text('single_dpp_inc_tax'.$count.'[]', @num_format((!empty($product_prices))?$product_prices->dpp_inc_tax:0), ['class' => 'form-control input-sm dpp_inc_tax input_number', 'placeholder' => __('product.inc_of_tax'),'id' => 'single_dpp_inc_tax'.$count, 'required']); !!}
                            </div>
                        </td>
                        <td> <br/> {!! Form::text('profit_percent'.$count.'[]', @num_format((!empty($product_prices))?$product_prices->profit_percent:0), ['class' => 'form-control input-sm input_number', 'id' => 'profit_percent'.$count, 'required']); !!}</td>
                        <td>
                            <div class="col-sm-6">
                                {!! Form::label('single_dpp', trans('product.exc_of_tax') . ':*') !!}
                                {!! Form::text('single_dsp'.$count.'[]', @num_format((!empty($product_prices))?$product_prices->default_sell_price:0), ['class' => 'form-control input-sm  input_number', 'placeholder' => __('product.exc_of_tax'), 'id' => 'single_dsp'.$count, 'required']); !!}
                            </div>

                            <div class="col-sm-6">
                              {!! Form::label('single_dpp_inc_tax', trans('product.inc_of_tax') . ':*') !!}
                              {!! Form::text('single_dsp_inc_tax'.$count.'[]', @num_format((!empty($product_prices))?$product_prices->sell_price_inc_tax:0), ['class' => 'form-control input-sm  input_number', 'placeholder' => __('product.inc_of_tax'), 'id' => 'single_dsp_inc_tax'.$count, 'required']); !!}
                            </div>
                        </td>
                      </tr>            
                  </body>
              </table>
            </div>
        </div>
      
    @endforeach
  @else
        @foreach($other_unit as $k => $i)
        @php $count++ ; $list_new = [];   @endphp
          @foreach($units_main as $n => $row_line)
              @if($i == $n)
                @php 
                  $list_new[$n] = $row_line; 
                @endphp
              @endif
          @endforeach
        {!! Form::select('unit_D[]', $list_new,  $i  , ['class' => 'form-control input-sm width-10 dpp un_select input_number select2',"data-id"=>$k,"style"=>"width:10%" ,'id'=>'unit_D', 'placeholder' => __('messages.please_select')     ]); !!}
        <div class="table-responsive">
          <table class="table table-bordered add-product-price-table table-condensed {{$class}}">
              <head>
                  <tr>
                    <th style="vertical-align: bottom">
                      {{__('home.Type Price')}}
                    </th>
                    <th>@lang('product.default_purchase_price')</th>
                    <th>@lang('product.profit_percent') @show_tooltip(__('tooltip.profit_percent'))</th>
                    <th>@lang('product.default_selling_price')</th>
                  </tr>
              </head>
              <body>
                {{-- 1 default_price --}}
                  @php $product_prices = \App\Models\ProductPrice::where("product_id",$product_details->product_id)->where("number_of_default",0)->where("unit_id",$i)->first(); @endphp
                  <tr>
                      <td style="vertical-align: bottom"> {{ (!empty($product_prices))?$product_prices->name:"Default Price" }} </td>
                      <td>
                          @php $varid = null; foreach ($product_details->variations as $key => $var) { $varid  =   $var->id; } @endphp
                          <input type="hidden" name="single_variation_id1" value="{{$varid}}">
                          <div class="col-sm-6">
                            {!! Form::label('single_dpp', trans('product.exc_of_tax') . ':*') !!}
                            {!! Form::text('single_dpp'.$count.'[]', @num_format((!empty($product_prices))?$product_prices->default_purchase_price:0), ['class' => 'form-control input-sm dpp input_number', 'placeholder' => __('product.exc_of_tax'),'id' => 'single_dpp'.$count, 'required']); !!}
                          </div>
                          <div class="col-sm-6">
                            {!! Form::label('single_dpp_inc_tax', trans('product.inc_of_tax') . ':*') !!}
                            {!! Form::text('single_dpp_inc_tax'.$count.'[]', @num_format((!empty($product_prices))?$product_prices->dpp_inc_tax:0), ['class' => 'form-control input-sm dpp_inc_tax input_number', 'placeholder' => __('product.inc_of_tax'),'id' => 'single_dpp_inc_tax'.$count, 'required']); !!}
                          </div>
                      </td>
                      <td> <br/> {!! Form::text('profit_percent'.$count.'[]', @num_format((!empty($product_prices))?$product_prices->profit_percent:0), ['class' => 'form-control input-sm input_number', 'id' => 'profit_percent'.$count, 'required']); !!}</td>
                      <td>
                          <div class="col-sm-6">
                              {!! Form::label('single_dpp', trans('product.exc_of_tax') . ':*') !!}
                              {!! Form::text('single_dsp'.$count.'[]', @num_format((!empty($product_prices))?$product_prices->default_sell_price:0), ['class' => 'form-control input-sm  input_number', 'placeholder' => __('product.exc_of_tax'), 'id' => 'single_dsp'.$count, 'required']); !!}
                          </div>

                          <div class="col-sm-6">
                            {!! Form::label('single_dpp_inc_tax', trans('product.inc_of_tax') . ':*') !!}
                            {!! Form::text('single_dsp_inc_tax'.$count.'[]', @num_format((!empty($product_prices))?$product_prices->sell_price_inc_tax:0), ['class' => 'form-control input-sm  input_number', 'placeholder' => __('product.inc_of_tax'), 'id' => 'single_dsp_inc_tax'.$count, 'required']); !!}
                          </div>
                      </td>
                  </tr>        
                {{-- 2 whole_price --}}
                  @php $product_prices = \App\Models\ProductPrice::where("product_id",$product_details->product_id)->where("number_of_default",1)->where("unit_id",$i)->first(); @endphp
                  <tr>
                      <td style="vertical-align: bottom"> {{ (!empty($product_prices))?$product_prices->name:"Whole Price" }} </td>
                      <td>
                          @php $varid = null; foreach ($product_details->variations as $key => $var) { $varid  =   $var->id; } @endphp
                          <input type="hidden" name="single_variation_id1" value="{{$varid}}">
                          <div class="col-sm-6">
                            {!! Form::label('single_dpp', trans('product.exc_of_tax') . ':*') !!}
                            {!! Form::text('single_dpp'.$count.'[]', @num_format((!empty($product_prices))?$product_prices->default_purchase_price:0), ['class' => 'form-control input-sm dpp input_number', 'placeholder' => __('product.exc_of_tax'),'id' => 'single_dpp'.$count, 'required']); !!}
                          </div>
                          <div class="col-sm-6">
                            {!! Form::label('single_dpp_inc_tax', trans('product.inc_of_tax') . ':*') !!}
                            {!! Form::text('single_dpp_inc_tax'.$count.'[]', @num_format((!empty($product_prices))?$product_prices->dpp_inc_tax:0), ['class' => 'form-control input-sm dpp_inc_tax input_number', 'placeholder' => __('product.inc_of_tax'),'id' => 'single_dpp_inc_tax'.$count, 'required']); !!}
                          </div>
                      </td>
                      <td> <br/> {!! Form::text('profit_percent'.$count.'[]', @num_format((!empty($product_prices))?$product_prices->profit_percent:0), ['class' => 'form-control input-sm input_number', 'id' => 'profit_percent'.$count, 'required']); !!}</td>
                      <td>
                          <div class="col-sm-6">
                              {!! Form::label('single_dpp', trans('product.exc_of_tax') . ':*') !!}
                              {!! Form::text('single_dsp'.$count.'[]', @num_format((!empty($product_prices))?$product_prices->default_sell_price:0), ['class' => 'form-control input-sm  input_number', 'placeholder' => __('product.exc_of_tax'), 'id' => 'single_dsp'.$count, 'required']); !!}
                          </div>

                          <div class="col-sm-6">
                            {!! Form::label('single_dpp_inc_tax', trans('product.inc_of_tax') . ':*') !!}
                            {!! Form::text('single_dsp_inc_tax'.$count.'[]', @num_format((!empty($product_prices))?$product_prices->sell_price_inc_tax:0), ['class' => 'form-control input-sm  input_number', 'placeholder' => __('product.inc_of_tax'), 'id' => 'single_dsp_inc_tax'.$count, 'required']); !!}
                          </div>
                      </td>
                  </tr>          
                {{-- 3 retail_price --}}
                  @php $product_prices = \App\Models\ProductPrice::where("product_id",$product_details->product_id)->where("number_of_default",2)->where("unit_id",$i)->first(); @endphp
                  <tr>
                    <td style="vertical-align: bottom"> {{ (!empty($product_prices))?$product_prices->name:"Retail Price" }} </td>
                    <td>
                        @php $varid = null; foreach ($product_details->variations as $key => $var) { $varid  =   $var->id; } @endphp
                        <input type="hidden" name="single_variation_id1" value="{{$varid}}">
                        <div class="col-sm-6">
                          {!! Form::label('single_dpp', trans('product.exc_of_tax') . ':*') !!}
                          {!! Form::text('single_dpp'.$count.'[]', @num_format((!empty($product_prices))?$product_prices->default_purchase_price:0), ['class' => 'form-control input-sm dpp input_number', 'placeholder' => __('product.exc_of_tax'),'id' => 'single_dpp'.$count, 'required']); !!}
                        </div>
                        <div class="col-sm-6">
                          {!! Form::label('single_dpp_inc_tax', trans('product.inc_of_tax') . ':*') !!}
                          {!! Form::text('single_dpp_inc_tax'.$count.'[]', @num_format((!empty($product_prices))?$product_prices->dpp_inc_tax:0), ['class' => 'form-control input-sm dpp_inc_tax input_number', 'placeholder' => __('product.inc_of_tax'),'id' => 'single_dpp_inc_tax'.$count, 'required']); !!}
                        </div>
                    </td>
                    <td> <br/> {!! Form::text('profit_percent'.$count.'[]', @num_format((!empty($product_prices))?$product_prices->profit_percent:0), ['class' => 'form-control input-sm input_number', 'id' => 'profit_percent'.$count, 'required']); !!}</td>
                    <td>
                        <div class="col-sm-6">
                            {!! Form::label('single_dpp', trans('product.exc_of_tax') . ':*') !!}
                            {!! Form::text('single_dsp'.$count.'[]', @num_format((!empty($product_prices))?$product_prices->default_sell_price:0), ['class' => 'form-control input-sm  input_number', 'placeholder' => __('product.exc_of_tax'), 'id' => 'single_dsp'.$count, 'required']); !!}
                        </div>

                        <div class="col-sm-6">
                          {!! Form::label('single_dpp_inc_tax', trans('product.inc_of_tax') . ':*') !!}
                          {!! Form::text('single_dsp_inc_tax'.$count.'[]', @num_format((!empty($product_prices))?$product_prices->sell_price_inc_tax:0), ['class' => 'form-control input-sm  input_number', 'placeholder' => __('product.inc_of_tax'), 'id' => 'single_dsp_inc_tax'.$count, 'required']); !!}
                        </div>
                    </td>
                  </tr> 
                {{-- 4 minimum_price --}}
                  @php $product_prices = \App\Models\ProductPrice::where("product_id",$product_details->product_id)->where("number_of_default",3)->where("unit_id",$i)->first(); @endphp
                  <tr>
                    <td style="vertical-align: bottom"> {{ (!empty($product_prices))?$product_prices->name:"Minimum Price" }} </td>
                    <td>
                        @php $varid = null; foreach ($product_details->variations as $key => $var) { $varid  =   $var->id; } @endphp
                        <input type="hidden" name="single_variation_id1" value="{{$varid}}">
                        <div class="col-sm-6">
                          {!! Form::label('single_dpp', trans('product.exc_of_tax') . ':*') !!}
                          {!! Form::text('single_dpp'.$count.'[]', @num_format((!empty($product_prices))?$product_prices->default_purchase_price:0), ['class' => 'form-control input-sm dpp input_number', 'placeholder' => __('product.exc_of_tax'),'id' => 'single_dpp'.$count, 'required']); !!}
                        </div>
                        <div class="col-sm-6">
                          {!! Form::label('single_dpp_inc_tax', trans('product.inc_of_tax') . ':*') !!}
                          {!! Form::text('single_dpp_inc_tax'.$count.'[]', @num_format((!empty($product_prices))?$product_prices->dpp_inc_tax:0), ['class' => 'form-control input-sm dpp_inc_tax input_number', 'placeholder' => __('product.inc_of_tax'),'id' => 'single_dpp_inc_tax'.$count, 'required']); !!}
                        </div>
                    </td>
                    <td> <br/> {!! Form::text('profit_percent'.$count.'[]', @num_format((!empty($product_prices))?$product_prices->profit_percent:0), ['class' => 'form-control input-sm input_number', 'id' => 'profit_percent'.$count, 'required']); !!}</td>
                    <td>
                        <div class="col-sm-6">
                            {!! Form::label('single_dpp', trans('product.exc_of_tax') . ':*') !!}
                            {!! Form::text('single_dsp'.$count.'[]', @num_format((!empty($product_prices))?$product_prices->default_sell_price:0), ['class' => 'form-control input-sm  input_number', 'placeholder' => __('product.exc_of_tax'), 'id' => 'single_dsp'.$count, 'required']); !!}
                        </div>

                        <div class="col-sm-6">
                          {!! Form::label('single_dpp_inc_tax', trans('product.inc_of_tax') . ':*') !!}
                          {!! Form::text('single_dsp_inc_tax'.$count.'[]', @num_format((!empty($product_prices))?$product_prices->sell_price_inc_tax:0), ['class' => 'form-control input-sm  input_number', 'placeholder' => __('product.inc_of_tax'), 'id' => 'single_dsp_inc_tax'.$count, 'required']); !!}
                        </div>
                    </td>
                  </tr>
                {{-- 5 last_price --}}
                  @php $product_prices = \App\Models\ProductPrice::where("product_id",$product_details->product_id)->where("number_of_default",4)->where("unit_id",$i)->first(); @endphp
                  <tr>
                    <td style="vertical-align: bottom"> {{ (!empty($product_prices))?$product_prices->name:"Last Price" }} </td>
                    <td>
                        @php $varid = null; foreach ($product_details->variations as $key => $var) { $varid  =   $var->id; } @endphp
                        <input type="hidden" name="single_variation_id1" value="{{$varid}}">
                        <div class="col-sm-6">
                          {!! Form::label('single_dpp', trans('product.exc_of_tax') . ':*') !!}
                          {!! Form::text('single_dpp'.$count.'[]', @num_format((!empty($product_prices))?$product_prices->default_purchase_price:0), ['class' => 'form-control input-sm dpp input_number', 'placeholder' => __('product.exc_of_tax'),'id' => 'single_dpp'.$count, 'required']); !!}
                        </div>
                        <div class="col-sm-6">
                          {!! Form::label('single_dpp_inc_tax', trans('product.inc_of_tax') . ':*') !!}
                          {!! Form::text('single_dpp_inc_tax'.$count.'[]', @num_format((!empty($product_prices))?$product_prices->dpp_inc_tax:0), ['class' => 'form-control input-sm dpp_inc_tax input_number', 'placeholder' => __('product.inc_of_tax'),'id' => 'single_dpp_inc_tax'.$count, 'required']); !!}
                        </div>
                    </td>
                    <td> <br/> {!! Form::text('profit_percent'.$count.'[]', @num_format((!empty($product_prices))?$product_prices->profit_percent:0), ['class' => 'form-control input-sm input_number', 'id' => 'profit_percent'.$count, 'required']); !!}</td>
                    <td>
                        <div class="col-sm-6">
                            {!! Form::label('single_dpp', trans('product.exc_of_tax') . ':*') !!}
                            {!! Form::text('single_dsp'.$count.'[]', @num_format((!empty($product_prices))?$product_prices->default_sell_price:0), ['class' => 'form-control input-sm  input_number', 'placeholder' => __('product.exc_of_tax'), 'id' => 'single_dsp'.$count, 'required']); !!}
                        </div>

                        <div class="col-sm-6">
                          {!! Form::label('single_dpp_inc_tax', trans('product.inc_of_tax') . ':*') !!}
                          {!! Form::text('single_dsp_inc_tax'.$count.'[]', @num_format((!empty($product_prices))?$product_prices->sell_price_inc_tax:0), ['class' => 'form-control input-sm  input_number', 'placeholder' => __('product.inc_of_tax'), 'id' => 'single_dsp_inc_tax'.$count, 'required']); !!}
                        </div>
                    </td>
                  </tr>
                {{-- 6 ECM Before Price --}}
                  @php $product_prices = \App\Models\ProductPrice::where("product_id",$product_details->product_id)->where("number_of_default",5)->where("unit_id",$i)->first(); @endphp
                  <tr>
                    <td style="vertical-align: bottom"> {{ (!empty($product_prices))?$product_prices->name:"ECM Before Price" }} </td>
                    <td>
                        @php $varid = null; foreach ($product_details->variations as $key => $var) { $varid  =   $var->id; } @endphp
                        <input type="hidden" name="single_variation_id1" value="{{$varid}}">
                        <div class="col-sm-6">
                          {!! Form::label('single_dpp', trans('product.exc_of_tax') . ':*') !!}
                          {!! Form::text('single_dpp'.$count.'[]', @num_format((!empty($product_prices))?$product_prices->default_purchase_price:0), ['class' => 'form-control input-sm dpp input_number', 'placeholder' => __('product.exc_of_tax'),'id' => 'single_dpp'.$count, 'required']); !!}
                        </div>
                        <div class="col-sm-6">
                          {!! Form::label('single_dpp_inc_tax', trans('product.inc_of_tax') . ':*') !!}
                          {!! Form::text('single_dpp_inc_tax'.$count.'[]', @num_format((!empty($product_prices))?$product_prices->dpp_inc_tax:0), ['class' => 'form-control input-sm dpp_inc_tax input_number', 'placeholder' => __('product.inc_of_tax'),'id' => 'single_dpp_inc_tax'.$count, 'required']); !!}
                        </div>
                    </td>
                    <td> <br/> {!! Form::text('profit_percent'.$count.'[]', @num_format((!empty($product_prices))?$product_prices->profit_percent:0), ['class' => 'form-control input-sm input_number', 'id' => 'profit_percent'.$count, 'required']); !!}</td>
                    <td>
                        <div class="col-sm-6">
                            {!! Form::label('single_dpp', trans('product.exc_of_tax') . ':*') !!}
                            {!! Form::text('single_dsp'.$count.'[]', @num_format((!empty($product_prices))?$product_prices->default_sell_price:0), ['class' => 'form-control input-sm  input_number', 'placeholder' => __('product.exc_of_tax'), 'id' => 'single_dsp'.$count, 'required']); !!}
                        </div>

                        <div class="col-sm-6">
                          {!! Form::label('single_dpp_inc_tax', trans('product.inc_of_tax') . ':*') !!}
                          {!! Form::text('single_dsp_inc_tax'.$count.'[]', @num_format((!empty($product_prices))?$product_prices->sell_price_inc_tax:0), ['class' => 'form-control input-sm  input_number', 'placeholder' => __('product.inc_of_tax'), 'id' => 'single_dsp_inc_tax'.$count, 'required']); !!}
                        </div>
                    </td>
                  </tr>
                {{-- 7 ECM After Price --}}
                  @php $product_prices = \App\Models\ProductPrice::where("product_id",$product_details->product_id)->where("number_of_default",6)->where("unit_id",$i)->first(); @endphp
                  <tr>
                    <td style="vertical-align: bottom"> {{ (!empty($product_prices))?$product_prices->name:"ECM After Price" }} </td>
                    <td>
                        @php $varid = null; foreach ($product_details->variations as $key => $var) { $varid  =   $var->id; } @endphp
                        <input type="hidden" name="single_variation_id1" value="{{$varid}}">
                        <div class="col-sm-6">
                          {!! Form::label('single_dpp', trans('product.exc_of_tax') . ':*') !!}
                          {!! Form::text('single_dpp'.$count.'[]', @num_format((!empty($product_prices))?$product_prices->default_purchase_price:0), ['class' => 'form-control input-sm dpp input_number', 'placeholder' => __('product.exc_of_tax'),'id' => 'single_dpp'.$count, 'required']); !!}
                        </div>
                        <div class="col-sm-6">
                          {!! Form::label('single_dpp_inc_tax', trans('product.inc_of_tax') . ':*') !!}
                          {!! Form::text('single_dpp_inc_tax'.$count.'[]', @num_format((!empty($product_prices))?$product_prices->dpp_inc_tax:0), ['class' => 'form-control input-sm dpp_inc_tax input_number', 'placeholder' => __('product.inc_of_tax'),'id' => 'single_dpp_inc_tax'.$count, 'required']); !!}
                        </div>
                    </td>
                    <td> <br/> {!! Form::text('profit_percent'.$count.'[]', @num_format((!empty($product_prices))?$product_prices->profit_percent:0), ['class' => 'form-control input-sm input_number', 'id' => 'profit_percent'.$count, 'required']); !!}</td>
                    <td>
                        <div class="col-sm-6">
                            {!! Form::label('single_dpp', trans('product.exc_of_tax') . ':*') !!}
                            {!! Form::text('single_dsp'.$count.'[]', @num_format((!empty($product_prices))?$product_prices->default_sell_price:0), ['class' => 'form-control input-sm  input_number', 'placeholder' => __('product.exc_of_tax'), 'id' => 'single_dsp'.$count, 'required']); !!}
                        </div>

                        <div class="col-sm-6">
                          {!! Form::label('single_dpp_inc_tax', trans('product.inc_of_tax') . ':*') !!}
                          {!! Form::text('single_dsp_inc_tax'.$count.'[]', @num_format((!empty($product_prices))?$product_prices->sell_price_inc_tax:0), ['class' => 'form-control input-sm  input_number', 'placeholder' => __('product.inc_of_tax'), 'id' => 'single_dsp_inc_tax'.$count, 'required']); !!}
                        </div>
                    </td>
                  </tr>
                {{-- 8 custom_price 1 --}}
                  @php $product_prices = \App\Models\ProductPrice::where("product_id",$product_details->product_id)->where("number_of_default",7)->where("unit_id",$i)->first(); @endphp
                  <tr>
                    <td style="vertical-align: bottom"> {{ (!empty($product_prices))?$product_prices->name:"Custom Price 1" }} </td>
                    <td>
                        @php $varid = null; foreach ($product_details->variations as $key => $var) { $varid  =   $var->id; } @endphp
                        <input type="hidden" name="single_variation_id1" value="{{$varid}}">
                        <div class="col-sm-6">
                          {!! Form::label('single_dpp', trans('product.exc_of_tax') . ':*') !!}
                          {!! Form::text('single_dpp'.$count.'[]', @num_format((!empty($product_prices))?$product_prices->default_purchase_price:0), ['class' => 'form-control input-sm dpp input_number', 'placeholder' => __('product.exc_of_tax'),'id' => 'single_dpp'.$count, 'required']); !!}
                        </div>
                        <div class="col-sm-6">
                          {!! Form::label('single_dpp_inc_tax', trans('product.inc_of_tax') . ':*') !!}
                          {!! Form::text('single_dpp_inc_tax'.$count.'[]', @num_format((!empty($product_prices))?$product_prices->dpp_inc_tax:0), ['class' => 'form-control input-sm dpp_inc_tax input_number', 'placeholder' => __('product.inc_of_tax'),'id' => 'single_dpp_inc_tax'.$count, 'required']); !!}
                        </div>
                    </td>
                    <td> <br/> {!! Form::text('profit_percent'.$count.'[]', @num_format((!empty($product_prices))?$product_prices->profit_percent:0), ['class' => 'form-control input-sm input_number', 'id' => 'profit_percent'.$count, 'required']); !!}</td>
                    <td>
                        <div class="col-sm-6">
                            {!! Form::label('single_dpp', trans('product.exc_of_tax') . ':*') !!}
                            {!! Form::text('single_dsp'.$count.'[]', @num_format((!empty($product_prices))?$product_prices->default_sell_price:0), ['class' => 'form-control input-sm  input_number', 'placeholder' => __('product.exc_of_tax'), 'id' => 'single_dsp'.$count, 'required']); !!}
                        </div>

                        <div class="col-sm-6">
                          {!! Form::label('single_dpp_inc_tax', trans('product.inc_of_tax') . ':*') !!}
                          {!! Form::text('single_dsp_inc_tax'.$count.'[]', @num_format((!empty($product_prices))?$product_prices->sell_price_inc_tax:0), ['class' => 'form-control input-sm  input_number', 'placeholder' => __('product.inc_of_tax'), 'id' => 'single_dsp_inc_tax'.$count, 'required']); !!}
                        </div>
                    </td>
                  </tr>
                {{-- 9 custom_price 2 --}}
                  @php $product_prices = \App\Models\ProductPrice::where("product_id",$product_details->product_id)->where("number_of_default",8)->where("unit_id",$i)->first(); @endphp
                  <tr>
                    <td style="vertical-align: bottom"> {{ (!empty($product_prices))?$product_prices->name:"Custom Price 2" }} </td>
                    <td>
                        @php $varid = null; foreach ($product_details->variations as $key => $var) { $varid  =   $var->id; } @endphp
                        <input type="hidden" name="single_variation_id1" value="{{$varid}}">
                        <div class="col-sm-6">
                          {!! Form::label('single_dpp', trans('product.exc_of_tax') . ':*') !!}
                          {!! Form::text('single_dpp'.$count.'[]', @num_format((!empty($product_prices))?$product_prices->default_purchase_price:0), ['class' => 'form-control input-sm dpp input_number', 'placeholder' => __('product.exc_of_tax'),'id' => 'single_dpp'.$count, 'required']); !!}
                        </div>
                        <div class="col-sm-6">
                          {!! Form::label('single_dpp_inc_tax', trans('product.inc_of_tax') . ':*') !!}
                          {!! Form::text('single_dpp_inc_tax'.$count.'[]', @num_format((!empty($product_prices))?$product_prices->dpp_inc_tax:0), ['class' => 'form-control input-sm dpp_inc_tax input_number', 'placeholder' => __('product.inc_of_tax'),'id' => 'single_dpp_inc_tax'.$count, 'required']); !!}
                        </div>
                    </td>
                    <td> <br/> {!! Form::text('profit_percent'.$count.'[]', @num_format((!empty($product_prices))?$product_prices->profit_percent:0), ['class' => 'form-control input-sm input_number', 'id' => 'profit_percent'.$count, 'required']); !!}</td>
                    <td>
                        <div class="col-sm-6">
                            {!! Form::label('single_dpp', trans('product.exc_of_tax') . ':*') !!}
                            {!! Form::text('single_dsp'.$count.'[]', @num_format((!empty($product_prices))?$product_prices->default_sell_price:0), ['class' => 'form-control input-sm  input_number', 'placeholder' => __('product.exc_of_tax'), 'id' => 'single_dsp'.$count, 'required']); !!}
                        </div>

                        <div class="col-sm-6">
                          {!! Form::label('single_dpp_inc_tax', trans('product.inc_of_tax') . ':*') !!}
                          {!! Form::text('single_dsp_inc_tax'.$count.'[]', @num_format((!empty($product_prices))?$product_prices->sell_price_inc_tax:0), ['class' => 'form-control input-sm  input_number', 'placeholder' => __('product.inc_of_tax'), 'id' => 'single_dsp_inc_tax'.$count, 'required']); !!}
                        </div>
                    </td>
                  </tr>
                {{-- 10 custom_price 3 --}}
                  @php $product_prices = \App\Models\ProductPrice::where("product_id",$product_details->product_id)->where("number_of_default",9)->where("unit_id",$i)->first(); @endphp
                  <tr>
                    <td style="vertical-align: bottom"> {{ (!empty($product_prices))?$product_prices->name:"Custom Price 3" }} </td>
                    <td>
                        @php $varid = null; foreach ($product_details->variations as $key => $var) { $varid  =   $var->id; } @endphp
                        <input type="hidden" name="single_variation_id1" value="{{$varid}}">
                        <div class="col-sm-6">
                          {!! Form::label('single_dpp', trans('product.exc_of_tax') . ':*') !!}
                          {!! Form::text('single_dpp'.$count.'[]', @num_format((!empty($product_prices))?$product_prices->default_purchase_price:0), ['class' => 'form-control input-sm dpp input_number', 'placeholder' => __('product.exc_of_tax'),'id' => 'single_dpp'.$count, 'required']); !!}
                        </div>
                        <div class="col-sm-6">
                          {!! Form::label('single_dpp_inc_tax', trans('product.inc_of_tax') . ':*') !!}
                          {!! Form::text('single_dpp_inc_tax'.$count.'[]', @num_format((!empty($product_prices))?$product_prices->dpp_inc_tax:0), ['class' => 'form-control input-sm dpp_inc_tax input_number', 'placeholder' => __('product.inc_of_tax'),'id' => 'single_dpp_inc_tax'.$count, 'required']); !!}
                        </div>
                    </td>
                    <td> <br/> {!! Form::text('profit_percent'.$count.'[]', @num_format((!empty($product_prices))?$product_prices->profit_percent:0), ['class' => 'form-control input-sm input_number', 'id' => 'profit_percent'.$count, 'required']); !!}</td>
                    <td>
                        <div class="col-sm-6">
                            {!! Form::label('single_dpp', trans('product.exc_of_tax') . ':*') !!}
                            {!! Form::text('single_dsp'.$count.'[]', @num_format((!empty($product_prices))?$product_prices->default_sell_price:0), ['class' => 'form-control input-sm  input_number', 'placeholder' => __('product.exc_of_tax'), 'id' => 'single_dsp'.$count, 'required']); !!}
                        </div>

                        <div class="col-sm-6">
                          {!! Form::label('single_dpp_inc_tax', trans('product.inc_of_tax') . ':*') !!}
                          {!! Form::text('single_dsp_inc_tax'.$count.'[]', @num_format((!empty($product_prices))?$product_prices->sell_price_inc_tax:0), ['class' => 'form-control input-sm  input_number', 'placeholder' => __('product.inc_of_tax'), 'id' => 'single_dsp_inc_tax'.$count, 'required']); !!}
                        </div>
                    </td>
                  </tr>            
              </body>
          </table>
        </div>
        @endforeach
  @endif
</div>
<script type="text/javascript">
ready();
</script>
