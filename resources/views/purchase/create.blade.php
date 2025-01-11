@extends('layouts.app')
{{-- *1* --}}
@section('title', __('purchase.add_purchase'))
{{-- *2* --}}
@section('content')
@php
	 $pull            = in_array(session()->get('lang', config('app.locale')), config('constants.langs_rtl')) ? 'pull-left' : 'pull-right';
@endphp

	<!-- Content Header (Page header) -->
	{{-- *1* SECTION HEADER PAGE --}}
	{{-- **************************************** --}}
		<section class="content-header">
			<h1>@lang('purchase.add_purchase') <i class="fa fa-keyboard-o hover-q text-muted" aria-hidden="true" data-container="body" data-toggle="popover" data-placement="bottom" data-content="@include('purchase.partials.keyboard_shortcuts_details')" data-html="true" data-trigger="hover" data-original-title="" title=""></i></h1>
			<h5><i><b>{{ "   Purchases  >  " }} </b>{{ "Create Purchase   "   }} <b> {{ " " }} </b> {{""}}</i> <b class="{{$pull}}">{!!__('izo.main_currency')!!} : {{$currency_details->symbol}}</b></h5>
			 
		</section>
	{{-- **************************************** --}}

	<!-- Main content -->
	{{-- *2* SECTION PURCHASE BILL --}}
	{{-- **************************************** --}}
		<section class="content" style="padding:10px;">

			<!-- Page level currency setting -->
			{{-- *2/1* CURRENCY SEC --}}
			<input type="hidden" id="p_code"       value="{{$currency_details->code}}">
			<input type="hidden" id="p_symbol"     value="{{$currency_details->symbol}}">
			<input type="hidden" id="p_thousand"   value="{{$currency_details->thousand_separator}}">
			<input type="hidden" id="p_decimal"    value="{{$currency_details->decimal_separator}}">
			{{-- *2/2 ERROR SEC --}}
			@include('layouts.partials.error')
			{{-- *2/3 FORM SEC --}}
				{!! Form::open(['url' => action('PurchaseController@store'), 'method' => 'post', 'id' => 'add_purchase_form', 'files' => true ]) !!}
					{{-- *2/3/1* purchase main info --}}
					@component('components.widget', ['class' => 'box-primary', 'title' => __('purchase.main_section')])
						<div class="row"  style="padding:10px;">
							<div class="col-sm-4" @if(session()->get('user.language', config('app.locale')) != "ar") style="background-color: #f7f7f7;border-radius:10px;padding:10px;box-shadow:0px 0px 10px #00000023;margin-right:5%;" @else style="background-color: #f7f7f7;border-radius:10px;padding:10px;box-shadow:0px 0px 10px #00000023;margin-left:5%;"  @endif>
								<div class="@if(!empty($default_purchase_status)) col-sm-12 @else col-sm-12 @endif">
									<div class="form-group">
										{!! Form::label('supplier_id', __('purchase.supplier') . ':*') !!}
										<div class="input-group">
											<span class="input-group-addon">
												<i class="fa fa-user"></i>
											</span>
											{!! Form::select('contact_id', [], null, ['class' => 'form-control', 'placeholder' => __('messages.please_select'), 'required', 'id' => 'supplier_id']); !!}
											<span class="input-group-btn">
												<button type="button" class="btn btn-default bg-white btn-flat add_new_supplier" data-name=""><i class="fa fa-plus-circle text-primary fa-lg"></i></button>
											</span>
										</div>
									</div>
									<strong>
										@lang('business.CompanyInfo'):
									</strong>
									<div class="supplier_style"  hidden  id="supplier_address_div"></div>
								</div>
							</div>
							<div class="col-sm-7" style="background-color: #f7f7f7;border-radius:10px;padding:20px;box-shadow:0px 0px 10px #00000023">
								{{-- *2/3/1-1* reference bill auto generate --}}
								<div class="@if(!empty($default_purchase_status)) col-sm-4 @else col-sm-6 @endif hide">
									<div class="form-group">
										{!! Form::label('ref_no', __('purchase.ref_no').':') !!}
										@show_tooltip(__('lang_v1.leave_empty_to_autogenerate'))
										{!! Form::text('ref_no', null, ['class' => 'form-control']); !!}
									</div>
								</div>

								{{-- *2/3/1-2* reference source --}}
								<div class="@if(!empty($default_purchase_status)) col-sm-4 @else col-sm-6 @endif">
									<div class="form-group">
										{!! Form::label('sup_ref_no', __('purchase.sup_refe').':') !!}
										{!! Form::text('sup_ref_no', null, ['class' => 'form-control']); !!}
									</div>
								</div>
								
								{{-- *2/3/1-3* purchase date --}}
								<div class="@if(!empty($default_purchase_status)) col-sm-4 @else col-sm-6 @endif">
									<div class="form-group">
										{!! Form::label('transaction_date', __('purchase.purchase_date') . ':*') !!}
										<div class="input-group">
											<span class="input-group-addon">
												<i class="fa fa-calendar"></i>
											</span>
											{!! Form::text('transaction_date', @format_datetime('now'), ['class' => 'form-control', 'readonly', 'required']); !!}
										</div>
									</div>
								</div>
								
								{{-- *2/3/1-4* location  --}}
								@if(count($business_locations) >= 1)
									@php 
										$default_location =  array_key_first($business_locations->toArray());
										$search_disable = false; 
									@endphp
								@else
									@php $default_location = null;
									$search_disable = true;
									@endphp
								@endif
								@php
									$default_location2 = null;
									if(count($business_locations) >= 1){
									$default_location2 = array_key_first($business_locations->toArray())  ;
									}
								@endphp
								<div class="col-sm-6 hide">
									<div class="form-group">
										{!! Form::label('location_id', __('purchase.business_location').':*') !!}
										@show_tooltip(__('tooltip.purchase_location'))
										{!! Form::select('location_id', $business_locations, $default_location, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'required'], $bl_attributes); !!}
									</div>
								</div>

								{{-- *2/3/1-5* Exchange  --}}
								<!-- Currency Exchange Rate -->
								<div class="col-sm-6 @if(!$currency_details->purchase_in_diff_currency) hide @endif">
									<div class="form-group">
										{!! Form::label('exchange_rate', __('purchase.p_exchange_rate') . ':*') !!}
										@show_tooltip(__('tooltip.currency_exchange_factor'))
										<div class="input-group">
											<span class="input-group-addon">
												<i class="fa fa-info"></i>
											</span>
											{!! Form::number('exchange_rate', $currency_details->p_exchange_rate, ['class' => 'form-control', 'required', 'step' => 0.001]); !!}
										</div>
										<span class="help-block text-danger">
											@lang('purchase.diff_purchase_currency_help', ['currency' => $currency_details->name])
										</span>
									</div>
								</div>
								
								{{-- *2/3/1-6* pay term --}}
								<div class="col-md-6">
									<div class="form-group">
										<div class="multi-input">
										{!! Form::label('pay_term_number', __('contact.pay_term') . ':') !!} @show_tooltip(__('tooltip.pay_term'))
										<br/>
										{!! Form::number('pay_term_number', null, ['class' => 'form-control width-40 pull-left', 'placeholder' => __('contact.pay_term')]); !!}
										
										{!! Form::select('pay_term_type', 
										['months' => __('lang_v1.months'), 
										'days' => __('lang_v1.days')], 
										null, 
										['class' => 'form-control width-60 pull-left','placeholder' => __('messages.please_select'), 'id' => 'pay_term_type']); !!}
										</div>
									</div>
								</div>
								
								{{-- *2/3/1-7* Status --}}
								<div class="col-sm-6 @if(!empty($default_purchase_status)) hide @endif">
									<div class="form-group">
										{!! Form::label('status', __('purchase.purchase_status') . ':*') !!} @show_tooltip(__('tooltip.order_status'))
										{!! Form::select('status', $orderStatuses, ($default_purchase_status != null)?$default_purchase_status:'ordered', ['class' => 'form-control select2',   'required']); !!}
									</div>
								</div>	
								
								{{-- *2/3/1-8* Cost Center --}}
								<div class="col-sm-6">
									<div class="form-group">
										{!! Form::label('cost_center_id', __('home.Cost Center').':') !!}
										{{-- @show_tooltip(__('tooltip.purchase_location')) --}}
										{!! Form::select('cost_center_id', $cost_centers, null, ['class' => 'form-control select2','id'=>'cost_center_id', 'placeholder' => __('messages.please_select')], $bl_attributes); !!}
									</div>
								</div>

								{{-- *2/3/1-9* Store--}}
								<div class="col-sm-6">
									@php
										$store = "";
										foreach ($mainstore_categories as  $key => $value) {
											$store = $key;
											break;
										}
									@endphp
									<div class="form-group">
										{!! Form::label('store_id', __('warehouse.warehouse').':*') !!}
										{{-- @show_tooltip(__('tooltip.purchase_location')) --}}
										{!! Form::select('store_id', $mainstore_categories, $store, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'required'], $bl_attributes); !!}
									</div>
								</div>
								

								{{-- *2/3/1-10* Currency--}}
								<div class="col-md-6">
									<div class="form-group">
										<div class="multi-input">
											{!! Form::label('currency_id', __('business.currency') . ':') !!}  
											@show_tooltip(__('tooltip.currency_blank'))
											<br/>
											{!! Form::select('currency_id', $currencies, null, ['class' => 'form-control width-60 currency_id  select2', 'placeholder' => __('messages.please_select') ]); !!}
											{!! Form::text('currency_id_amount', null, ['class' => 'form-control width-40 pull-right currency_id_amount'   ]); !!}
											<br/>
											<div class="check_dep_curr hide">
												{!! Form::checkbox('depending_curr',null, 0, ['class' => 'depending_curr' ,'id'=>'depending_curr'   ]); !!}
												{!! Form::label('depending_curr', __('Depending On Currency Column') . '') !!}  
											</div>
											<br/> 
											<div class="check_dep_curr  hide"><input  type="checkbox" name="dis_currency" value="1"> <b>Discount</b> @show_tooltip(__('tooltip.dis_currency'))<br ></div>
										</div>
									</div>
								</div>
	
								{{-- *2/3/1-11* Document--}}
								<div class="col-md-6">
									<div class="form-group">
										{!! Form::label('document_purchase[]', __('purchase.attach_document') . ':') !!}
										{!! Form::file('document_purchase[]', ['multiple','id' => 'upload_document', 'accept' =>
										implode(',', array_keys(config('constants.document_upload_mimes_types')))]); !!}
										<p class="help-block">
										@lang('purchase.max_file_size', ['size' => (config('constants.document_size_limit') / 1000000)])
										@includeIf('components.document_help_text')
										</p>
									</div>
								</div>
								{{-- *2/3/1-12* List Price--}}
								{{-- #2024-8-6 --}}
								<div class="col-sm-6">
									<div class="form-group">
										{!! Form::label('list_price', __('List  Of Prices').':') !!}
										{!! Form::select('list_price',$list_of_prices,null, ['class' => 'form-control select2' , 'id' => 'list_price' ]); !!}
									</div>
								</div>
								@php $databaseName     =  "izo26102024_esai" ; $dab = Illuminate\Support\Facades\Config::get('database.connections.mysql.database'); @endphp
								{{-- @if($databaseName == $dab)  --}}
									@if(count($patterns)>1)
										<div class="col-sm-6 ">
											<div class="form-group">
												{!! Form::label('pattern_id', __('business.patterns') . ':*') !!}
												{!! Form::select('pattern_id', $patterns, null , ['class' => 'pattern_id form-control select2', 'required','placeholder' => __('messages.please_select')]); !!}
											</div>
										</div>
									@else
										@php  $default  =  array_key_first($patterns); @endphp 
										<div class="col-sm-6 ">
											<div class="form-group">
												{!! Form::label('pattern_id', __('business.patterns') . ':*') !!}
												{!! Form::select('pattern_id', $patterns, $default , ['class' => 'pattern_id form-control select2', 'required']); !!}
											</div>
										</div>
									@endif
								{{-- @endif --}}
							</div>
						</div>
					@endcomponent
					{{-- *2/3/2* purchase main info --}}
					@component('components.widget', ['class' => 'box-primary', 'title' => __('purchase.search_section')])
						<div class="row" style="padding:10px;">
							<div class="col-sm-8 col-sm-offset-2">
								<div class="form-group">
									<div class="input-group">
										<span class="input-group-addon">
											<i class="fa fa-search"></i>
										</span>
										{!! Form::text('search_product', null, ['class' => 'form-control mousetrap', 'id' => 'search_product', 'placeholder' => __('lang_v1.search_product_placeholder'), 'disabled' => $search_disable]); !!}
									</div>
								</div>
							</div>
							<div class="col-sm-2">
								<div class="form-group">
									<button tabindex="-1" type="button" class="btn btn-link btn-modal"data-href="{{action('ProductController@quickAdd')}}" 
								data-container=".quick_add_product_modal"><i class="fa fa-plus"></i> @lang( 'product.add_new_product' ) </button>
								</div>
							</div>
						</div>
						@php
							$hide_tax = '';
							if( session()->get('business.enable_inline_tax') == 0){
								$hide_tax = 'hide';
							}
						@endphp
						<div class="row">
							<div class="col-sm-12">
								<div class="table-responsive purchase_table">
									<table class="table table-condensed table-bordered table-th-green text-center table-striped dataTable" id="purchase_entry_table">
										<thead>
											<tr>
												<th>#</th>
												<th>@lang( 'product.product_name' )</th>
												<th>@lang( 'home.Quantity' )</th>
												<th>@lang( 'lang_v1.unit_cost_before_discount' ) . {{$currency_details->symbol}}</th>
												<th>@lang('home.Price Including Tax') . {{$currency_details->symbol}}</th>
												
												<th class="curr_column br_dis  cur_check hide">@lang( 'lang_v1.Cost before without Tax' )</th>
												<th class="curr_column  hide "  >@lang( 'home.Cost before with Tax' )</th>
											
												<th> 
													<input name="dis_type"  type="radio" value="0" checked >
													percentage discount <br>
													<input name="dis_type"  type="radio" value="1"  >
													amount discount <br>
												</th>
												<th>@lang( 'home.Cost without Tax' ) . {{$currency_details->symbol}}</th>
												<th>@lang( 'home.Cost After Tax' ) . {{$currency_details->symbol}}</th>
												<th class="curr_column ar_dis  cur_check hide">@lang( 'home.Cost without Tax currency' )</th>
												<th class="curr_column hide  " >@lang( 'home.Cost After Tax currency' )</th>
												<th>@lang( 'home.Total' ). {{$currency_details->symbol}}</th>
												<th class="cur_check ar_dis_total hide">@lang( 'home.Total Currency' ). {{$currency_details->symbol}}</th>
												<th class="{{$hide_tax}}">@lang( 'purchase.subtotal_before_tax' )</th>
												<th class="{{$hide_tax}}">@lang( 'purchase.product_tax' )</th>
												<th class="{{$hide_tax}}">@lang( 'purchase.net_cost' )</th>
												
												{{-- <th>@lang( 'purchase.line_total' )</th> --}}
												{{-- <th class="@if(!session('business.enable_editing_product_from_purchase')) hide @endif">
													@lang( 'lang_v1.profit_margin' )
												</th> --}}
												{{-- <th>
													@lang( 'purchase.unit_selling_price' )
													<small>(@lang('product.inc_of_tax'))</small>
												</th> --}}
												@if(session('business.enable_lot_number'))
													<th>
														@lang('lang_v1.lot_number')
													</th>
												@endif
												@if(session('business.enable_product_expiry'))
													<th>
														@lang('product.mfg_date') / @lang('product.exp_date')
													</th>
												@endif
												<th><i class="fa fa-trash" aria-hidden="true"></i></th>
											</tr>
										</thead>
										<tbody></tbody>
									</table>
								</div>
								<hr/>
								<div class="row" style="background-color: #f7f7f7;padding:10px;margin:0px 1%;border-radius:10px;box-shadow:0px 0px 10px #00000022">
									<div class="pull-right col-md-6" >
										<table class="pull-right col-md-12">
											<tr>
												<th class="col-md-7 text-right">@lang( 'lang_v1.total_items' ):</th>
												<td class="col-md-5 text-left">
													<span id="total_quantity" class="display_currency" data-currency_symbol="false"></span>
												</td>
											</tr>
											<tr class="hide">
												<th class="col-md-7 text-right">@lang('purchase.total_before_tax'). {{$currency_details->symbol}} :</th>
												<td class="col-md-5 text-left">
													<span id="total_st_before_tax" class="display_currency"></span>
													<input type="hidden" id="st_before_tax_input" value=0>
												</td>
											</tr>
											<tr>
												<th class="col-md-7 text-right">@lang('purchase.sub_total_amount' ). {{$currency_details->symbol}}:</th>
												<td class="col-md-5 text-left">
													<span id="total_subtotal" class="display_currency"></span>
													<!-- This is total before purchase tax-->
													<input type="hidden" id="total_subtotal_input" value=0  name="total_before_tax">
												</td>
											</tr>
										</table>
									</div>
									<div class="pull-right col-md-6" >
										<table class="pull-right col-md-12">
											<tr>
												<th class="col-md-7 text-right"> &nbsp;</th>
												<td class="col-md-5 text-left">
													<span    >&nbsp;</span>
												</td>
											</tr>
											<tr>
												<th class="col-md-7 text-right hide cur_symbol">@lang('purchase.sub_total_amount' ):</th>
												<td class="col-md-5 text-left">
													<span id="total_subtotal_cur" class="display_currency hide"></span>
													<!-- This is total before purchase tax-->
													<input type="hidden" id="total_subtotal_input_cur" value=0  name="total_before_tax_cur">
												</td>
											</tr>
										</table>
									</div>
								</div>
								<input type="hidden" id="row_count" value="0">
							</div>
						</div>
					@endcomponent
					{{-- *2/3/3* purchase main info --}}
					@component('components.widget', ['class' => 'box-primary', 'title' => __('purchase.footer_section')])
						<div class="row">
							<div class="col-sm-12">
							<table class="table">
								<tr>
									<td class="col-md-3">
										<div class="form-group">
											{!! Form::label('discount_type', __( 'purchase.discount_type' ) . ':') !!}
											{!! Form::select('discount_type', [ '' => __('lang_v1.none'), 'fixed_before_vat' => __( 'home.fixed before vat' ), 'fixed_after_vat' => __( 'home.fixed after vat' ), 'percentage' => __( 'lang_v1.percentage' )], '', ['class' => 'form-control select2']); !!}
										</div>
									</td>
									<td class="col-md-3">
										<div class="form-group">
										{!! Form::label('discount_amount', __( 'purchase.discount_amount' ) . ':') !!} 
										{!! Form::text('discount_amount', 0, ['class' => 'form-control input_number', 'required']); !!}
										</div>
									</td>
									<td class="col-md-3 pull-right">
										<b class="i_curr hide"> @lang( 'purchase.discount' ): (-)</b>    
										<span id="discount_calculated_amount_cur" class="display_currency hide">0</span>
									</td>
									<td class="col-md-3">
										<b>@lang( 'purchase.discount' ). {{$currency_details->symbol}}:</b>(-)   
										<span id="discount_calculated_amount2" class="display_currency">0</span>
									</td>
								</tr>
								<tr>
									<td>
										<div class="form-group">
										{!! Form::label('tax_id', __('purchase.purchase_tax') . ':') !!}
										<select name="tax_id" id="tax_id" class="form-control select2" placeholder="'Please Select'">
											<option value="" data-tax_amount="0" data-tax_type="fixed" selected>@lang('lang_v1.none')</option>
											@php
											$count = 1;
											@endphp
											@foreach($taxes as $tax)
												@if($count == 1)
													<option value="{{ $tax->id }}" data-tax_amount="{{ $tax->amount }}" selected data-tax_type="{{ $tax->calculation_type }}">{{ $tax->name }}</option>
													{{$count++}}
												@else
													<option value="{{ $tax->id }}" data-tax_amount="{{ $tax->amount }}" data-tax_type="{{ $tax->calculation_type }}">{{ $tax->name }}</option>
													{{$count++}}
												@endif
											@endforeach
										</select>
										{!! Form::hidden('tax_amount', 0, ['id' => 'tax_amount']); !!}
										</div>
									</td>
									<td>&nbsp;</td>
									<td class="pull-right">
										<b class="t_curr hide">@lang( 'purchase.purchase_tax' ): (+)</b> 
										<span id="tax_calculated_amount_curr" class="display_currency hide">0</span></td>
									<td>
										<b>@lang( 'purchase.purchase_tax' ). {{$currency_details->symbol}}:</b>(+)  
										<span id="tax_calculated_amount" class="display_currency">0</span>
									</td>
								</tr>
								{{-- total  --}}
								<tr>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td class="pull-right">
										<br>
										<b class="z_curr hide">@lang('purchase.purchase_total_'): </b>
										<span id="total_final_i_curr" class="display_currency hide">0</span>
									</td>
									<td>
										<br>
										<b>@lang('purchase.purchase_total_'). {{$currency_details->symbol}}: </b><span id="total_final_i" class="display_currency" data-currency_symbol='true'>0</span>
									</td>
								</tr>
								<tr>
									<td>
										<div class="form-group">
										{!! Form::label('shipping_details', __( 'purchase.shipping_details' ) . ':') !!}
										{!! Form::text('shipping_details', null, ['class' => 'form-control']); !!}
										</div>
									</td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td>
										<div class="form-group">
											<b class="oss_curr hide">@lang('Additional Supplier Cost In Currency'): </b>
						                    <span id="cost_amount_supplier_curr" class="display_currency">0</span> 
											{!! Form::label('shipping_charges','(+) ' . __( 'purchase.supplier_shipping_charges' ) . ':') !!}
											<div class="input-group">
												<span class="input-group-btn">
													<button type="button"   class="btn btn-default bg-white btn-flat" title="@lang('unit.add_unit')" data-toggle="modal" data-target="#exampleModal"><i class="fa fa-plus-circle text-primary fa-lg"></i></button>
												</span>
												{!! Form::hidden('ADD_SHIP', 0, ['class' => 'form-control input_number' ,"id" => "total_ship_"  ]); !!}
												{!! Form::text('ADD_SHIP', 0, ['class' => 'form-control input_number' ,"id" => "total_ship_" , "disabled" ,'required']); !!}
											</div>
										</div>
										<!-- Modal -->
										<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
											@include('additional_Expense.create')
												
										</div>
									</td>
								</tr>
								{{-- purchase total --}}
								<tr>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td class="pull-right">
										<b class="o_curr hide">@lang('purchase.purchase_total'): </b>
										<span id="grand_total_cur" class="display_currency hide"  >0</span>
									</td>
									<td>
										{!! Form::hidden('final_total', 0 , ['id' => 'grand_total_hidden']); !!}
										<b>@lang('purchase.purchase_total'). {{$currency_details->symbol}}: </b><span id="grand_total" class="display_currency" data-currency_symbol='true'>0</span>
										<br>
										<br>
										{!! Form::hidden('final_total_hidden_', 0 , ['id' => 'total_final_hidden_']); !!}
										
									</td>
								</tr>
								<tr>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td>
										<div class="form-group">
											<b class=" os_curr hide">@lang('Additional Cost In Currency'): </b>
											<span id="cost_amount_curr" class="display_currency os_curr  hide">0</span> 
										{!! Form::label('shipping_charges','(+) ' . __( 'purchase.cost_shipping_charges' ) . ':') !!}
										<div class="input-group">
											<span class="input-group-btn">
												<button type="button"   class="btn btn-default bg-white btn-flat" title="@lang('unit.add_unit')" data-toggle="modal" data-target="#exampleModal"><i class="fa fa-plus-circle text-primary fa-lg"></i></button>
											</span>
											{!! Form::hidden('ADD_SHIP_', 0, ['class' => 'form-control input_number' ,"id" => "total_ship_c"  ]); !!}
											{!! Form::text('ADD_SHIP_', 0, ['class' => 'form-control input_number' ,"id" => "total_ship_c" , "disabled" ,'required']); !!}
										</div>
										</div>
										<!-- Modal -->
										<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
											<input id="ship_from" type="hidden" value="{{$ship_from}}">
											@include('additional_Expense.create')
												
										</div>
									</td>
								</tr>
								{{-- total bill --}}
								<tr>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td class="pull-right">
										<br>
										<b class="c_curr hide">@lang('purchase.purchase_pay'): </b>
										<span id="total_final_curr" class="display_currency hide"  >0</span>
									</td>
									<td>
										<br>
										<b>@lang('purchase.purchase_pay'). {{$currency_details->symbol}}: </b><span id="total_final_" class="display_currency" data-currency_symbol='true'>0</span>
									</td>
								</tr>
								<tr>
									<td colspan="4">
										<div class="form-group">
											{!! Form::label('additional_notes',__('purchase.additional_notes')) !!}
											{!! Form::textarea('additional_notes', null, ['class' => 'form-control', 'rows' => 3]); !!}
										</div>
									</td>
								</tr>
								<tr>
									<td colspan="4">
										<br>
										{{-- <div class="row">
											<div class="col-sm-12">
												<button type="button" id="submit_purchase_form" class="btn btn-primary pull-right btn-flat">@lang('messages.save')</button>
											</div>
										</div> --}}
										{{-- @if($databaseName == $dab)  --}}
											<div class="row">
												<div class="col-sm-12 sub    @if(session()->get('user.language', config('app.locale'))=='ar') text-right @else text-left  @endif ">
													{{-- <button type="button" id="submit-sell" class="btn btn-primary btn-flat">@lang('messages.save')</button> --}}
													<a data-pattern="3434"  data-href='{{action('SellController@check_msg')}}'  id='submit-purchase' data-container='.view_modal' class='update_transfer btn btn-modal btn-primary pull-right btn-flat'>@lang('messages.save')</a>
													{{-- <button type="button" id="save-and-print" class="btn btn-primary btn-flat">@lang('lang_v1.save_and_print')</button> --}}
												</div>
											</div>
										{{-- @else  --}}
											{{-- <div class="row">
												<div class="col-sm-12">
													<button type="button" id="submit_purchase_form" class="btn btn-primary pull-right btn-flat">@lang('messages.save')</button>
												</div>
											</div> --}}
										{{-- @endif --}}
									</td>
								</tr>
							</table>
							</div>
						</div>
					@endcomponent
					{{-- *2/3/4* payments  info --}}
					@component('components.widget', ['class' => 'box-primary  disabled  hide', 'title' => __('purchase.add_payment')])
						<div class="box-body payment_row">
							<div class="row">
								<div class="col-md-6">
									<strong>@lang('purchase.purchase_total'):</strong> <span style="color:red;" id="grand_total2">0</span>
								</div>
								<div class="col-md-6">
									<strong>@lang('lang_v1.advance_balance'):</strong> <span id="advance_balance_text">0</span>
									{!! Form::hidden('advance_balance', null, ['id' => 'advance_balance', 'data-error-msg' => __('lang_v1.required_advance_balance_not_available')]); !!}
								</div>
							</div>

							<hr>
							@include('sale_pos.partials.payment_row_form', ['row_index' => 0, 'show_date' => true,'cheque_type'=>1])
							<hr>
							<div class="row">
								<div class="col-sm-12">
									<div class="pull-right"><strong>@lang('purchase.payment_due'):</strong> <span id="payment_due_">0.00</span></div>
								</div>
							</div>
							<br>
							<div class="row">
								<div class="col-sm-12">
									<button type="button" id="submit_purchase_form" class="btn btn-primary pull-right btn-flat">@lang('messages.save')</button>
								</div>
							</div>
							
						</div>
					@endcomponent

				{!! Form::close() !!}
			{{-- * END -- FORM --}}
		</section>
	{{-- **************************************** --}}
	<!-- /. content-->

	<!-- quick product modal -->	
	{{-- *3*  SECTION MODAL --}}
	{{-- **************************************** --}}
		<div class="modal fade quick_add_product_modal" tabindex="-1" role="dialog" aria-labelledby="modalTitle"></div>
		<div class="modal fade contact_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
			@include('contact.create', ['quick_add' => true])
		</div>
	{{-- **************************************** --}}
	<!-- /.modal -->
@endsection
{{-- *3* --}}
@section('javascript')
	<script src="{{ asset('js/purchase.js?v=' . $asset_v) }}"></script>
	<script src="{{ asset('js/producte.js?v=' . $asset_v) }}"></script>
	<script type="text/javascript">
		updatePattern();
			$("#pattern_id").each( function(e){  
				$(this).on("change",function(){
					updatePattern();
				})
			});
		$('#purchase_entry_table tbody').sortable({
			cursor: "move",
			handle: ".handle",
			items: "> tr",
			update: function(event, ui) {
				var count = 1;
				$(".line_sorting").each(function(){
					e      = $(this); 
					var el = $(this).children().find(".line_sort"); 
					var inner = $(this).children().find(".line_ordered"); 
					var line_id = "line_sort[" +count+ "]" ;
					e.attr("data-row_index",count);
					inner.html(count);
					// el.attr("name",line_id)
					el.attr("value",count++);
					// el.val(count++);					
				});
			}
		});
		$('.currency_id_amount').change(function(){
			if($('.currency_id').val() != ""){
				update_row();
				total_bill();
			}
		})
		$('.currency_id').change(function(){
			var id = $(this).val();
			if(id == ""){
				$(".currency_id_amount").val("");
				$(".curr_column").attr("disabled",true);
				$(".curr_column").val(0);
				// $(".cur_symbol").html("Sub Total : " );
				$(".cur_symbol").addClass("hide" );
				$("#total_subtotal_cur").addClass("hide" );
				$(".i_curr").addClass("hide" );
				$("#discount_calculated_amount_cur").addClass("hide" );
				$(".t_curr").addClass("hide" );
				$("#tax_calculated_amount_curr").addClass("hide" );
				$(".o_curr").addClass("hide" );
				$("#grand_total_cur").addClass("hide" );
				$(".z_curr").addClass("hide" );
				$("#total_final_i_curr").addClass("hide" );
				$(".c_curr").addClass("hide" );
				$(".cur_check").addClass("hide" );
				$("#total_final_curr").addClass("hide" );
				$(".os_curr").addClass("hide" );
				$(".oss_curr").addClass("hide" );
				$(".add_section").addClass("hide" );
				$(".ship_curr").addClass("hide" );
				$(".check_dep_curr").addClass("hide" );
				$('input[name="dis_currency"]').prop('checked', false);
				$('#depending_curr').prop('checked', false);
				discount_cal_amount2() ;
                os_total_sub();
                os_grand();
			}else{
				$(".cur_symbol").removeClass("hide" );
				$("#total_subtotal_cur").removeClass("hide" );
				$(".curr_column").attr("disabled",false);
				$(".i_curr").removeClass("hide" );
				$("#discount_calculated_amount_cur").removeClass("hide" );
				$(".t_curr").removeClass("hide" );
				$("#tax_calculated_amount_curr").removeClass("hide" );
				$(".o_curr").removeClass("hide" );
				$("#grand_total_cur").removeClass("hide" );
				$(".z_curr").removeClass("hide" );
				$("#total_final_i_curr").removeClass("hide" );
				$(".c_curr").removeClass("hide" );
				$("#total_final_curr").removeClass("hide" );
				$(".cur_check").removeClass("hide" );
				$(".os_curr").removeClass("hide" );
				$(".oss_curr").removeClass("hide" );
				$(".ship_curr").removeClass("hide" );
				$(".add_section").removeClass("hide" );
				 
				
				// $(".check_dep_curr").removeClass("hide" );
				$('input[name="dis_currency"]').prop('checked', true);
				$('#depending_curr').prop('checked', true);

				$.ajax({
					url:"/symbol/amount/"+id,
					dataType: 'html',
					success:function(data){
						var object  = JSON.parse(data);
					 
						$(".currency_id_amount").val(object.amount);
 						$(".cur_symbol").html( @json(__('purchase.sub_total_amount')) + " " + object.symbol + " : "  );
 						$(".i_curr").html( @json(__('purchase.discount')) + " " + object.symbol +" : " + "(-)");
 						$(".t_curr").html( @json(__('purchase.purchase_tax')) + " " + object.symbol +" : " + "(+)" );
 						$(".o_curr").html( @json(__('purchase.purchase_total')) + " " + object.symbol +" : "   );
 						$(".z_curr").html( @json(__('purchase.purchase_total_')) + " " + object.symbol +" : "   );
 						$(".c_curr").html( @json(__('purchase.purchase_pay')) + " " + object.symbol +" : "   );
 						$(".ar_dis").html( @json(__('home.Cost without Tax currency')) + " " + object.symbol +"   "   );
 						$(".ar_dis_total").html( @json(__('home.Total Currency')) + " " + object.symbol +"   "   );
 						$(".br_dis").html( @json(__('lang_v1.Cost before without Tax')) + " " + object.symbol +"   "   );
						 $(".header_texts").html( @json(__('Amount')) + " - Curr"    );
 						$(".header_totals").html( @json(__('Total')) + " - Curr"   );
 						$(".header_vats").html( @json(__('Vat')) + " - Curr"     );
						// $(".add_currency_id").val(id);
						var cuurency_id   = id ;
						var cuurency_text = object.symbol ;
						// $('.add_currency_id').val(cuurency_id).trigger('change');
						// $(".add_currency_id option").each(function(){
						// 		e = $(this);
						// 		console.log(e.attr("value"));
								 
						// 		if(e.attr("value") == id){
						// 			e.attr("selected",true);
						// 		}else{
						// 			e.removeAttr("selected");
						// 		}
						// });
						// $(".add_currency_id_amount").val(object.amount);
						console.log("check if okk : " + id + " __ " + $(".add_currency_id").val());
						// $(".curr_column").removeClass("hide");
						os_total_sub();
					},
				});	 
			}
		})
		
		$(document).ready( function(){
		    $("#first_name").on("input",function(){
				$("#first_name").css({"outline":"0px solid red","box-shadow":"1px 1px 10px transparent","color":"gray"});
				$("#contact-submit").attr("disabled",false);

			});

			$("#first_name").on("change" ,function(e){
			var name = $("#first_name").val();
			// $("#name_p").css({"outline":"0px solid red","box-shadow":"1px 1px 10px transparent"});
			$.ajax({
				method: 'GET',
				url: '/contacts/check/' + name,
				async: false,
				data: {
					name: name,
				},
				dataType: 'json',
				success: function(result) {
					$results = result.status;
					if($results == true){
						toastr.error(LANG.product_name);
						$("#first_name").css({"outline":"1px solid red","box-shadow":"1px 1px 10px red","color":"red"})
						$("#contact-submit").attr("disabled",true);
					}
				}
				});
			});
			if ($('#search_product').length > 0) {
				
				$('#search_product')
				.autocomplete({
							source: function(request, response) {
								var contact_id = $("#supplier_id option:selected").val();
								$.getJSON(
								'/purchases/get_products?v=type_o=purchase&contact_id='+contact_id,
								{ location_id: $('#location_id').val(), term: request.term },
								response
								);
							},
							minLength: 2,
							response: function(event, ui) {
								// alert(JSON.stringify(ui));
								if (ui.content.length == 1) {
									ui.item = ui.content[0];
									$(this)
										.data('ui-autocomplete')
										._trigger('select', 'autocompleteselect', ui);
									$(this).autocomplete('close');
								} else if (ui.content.length == 0) {
									var term = $(this).data('ui-autocomplete').term;
									swal({
										title: LANG.no_products_found,
										text: __translate('add_name_as_new_product', { term: term }),
										buttons: [LANG.cancel, LANG.ok],
									}).then(value => {
										if (value) {
											var container = $('.quick_add_product_modal');
											$.ajax({
												url: '/products/quick_add?product_name=' + term,
												dataType: 'html',
												success: function(result) {
													$(container)
														.html(result)
														.modal('show');
													
												},
											});
										}
									});
								}
							},
							select: function(event, ui) {
								$(this).val(null);
								var tax_percentage =  $('#tax_id option:selected').data('tax_amount');
								curr = $(".currency_id_amount").val();
								get_purchase_entry_row(ui.item.product_id, ui.item.variation_id,null,curr);
							},
						})
						.autocomplete('instance')._renderItem = function(ul, item) {
						return $('<li>')
							.append('<div>' + item.text + '</div>')
							.appendTo(ul);
					};
				}

      		__page_leave_confirmation('#add_purchase_form');
      		$('.paid_on').datetimepicker({
                format: moment_date_format + ' ' + moment_time_format,
                ignoreReadonly: true,
            });
    	});
    	$(document).on('change', '.payment_types_dropdown, #location_id', function(e) {
		    var default_accounts = $('select#location_id').length ? 
		                $('select#location_id')
		                .find(':selected')
		                .data('default_payment_accounts') : [];
		    var payment_types_dropdown = $('.payment_types_dropdown');
		    var payment_type = payment_types_dropdown.val();
		    var payment_row = payment_types_dropdown.closest('.payment_row');
	        var row_index = payment_row.find('.payment_row_index').val();

	        var account_dropdown = payment_row.find('select#account_' + row_index);
		    if (payment_type && payment_type != 'advance') {
		        var default_account = default_accounts && default_accounts[payment_type]['account'] ? 
		            default_accounts[payment_type]['account'] : '';
		        if (account_dropdown.length && default_accounts) {
		            account_dropdown.val(default_account);
		            account_dropdown.change();
		        }
		    }

		    if (payment_type == 'advance') {
		        if (account_dropdown) {
		            account_dropdown.prop('disabled', true);
		            account_dropdown.closest('.form-group').addClass('hide');
		        }
		    } else {
		        if (account_dropdown) {
		            account_dropdown.prop('disabled', false); 
		            account_dropdown.closest('.form-group').removeClass('hide');
		        }    
		    }
		});
		$('#method_0').change(function(){
			var  method  = $('.payment_types_dropdown option:selected').val();
			if (method == 'cheque') {
				$('#pay_os_account').hide();
			}else{
				$('#pay_os_account').show();
			}
			
		})
		function updatePattern(){
			pattern = $("#pattern_id").val();
			row     = $(".purchase_unit_cost_without_discount").val();
			type    = "Add Purchase";
			button  = $(".sub").html("");
			button  = $(".sub").html("<a  data-href='{{action('SellController@check_msg')}}'  id='submit-purchase' data-container='.view_modal' class='update_transfer btn btn-modal btn-primary pull-right btn-flat'>@lang('messages.save')</a>");
			html    = "{{\URL::to('alert-check/show')}}"+"?pattern="+pattern+"&complete="+row+"&type="+type;
			$("#submit-purchase").attr("data-href", html) ;
		}
		// #2024-8-6
		$('table#purchase_entry_table').on('change', 'select.list_price', function() {
			 
			var tr                      = $(this).closest('tr');
			var base_unit_cost          = tr.find('input.base_unit_cost').val();
			var base_unit_selling_price = tr.find('input.base_unit_selling_price').val();
			var global_price            = $("#list_price").val();
			var price = parseFloat(
				$(this)
					.find(':selected')
					.data('price')
				);
			var cp_element = tr.find('input.purchase_unit_cost_without_discount');
			
			if(isNaN(price) ){	
				$(this).children().each(function(){ 
					if($(this).data("value") == global_price){
						final_price = parseFloat($(this).data("price"));
					}
				}); 
			}else{
				final_price = parseFloat(price);
			}
			cp_element.val(final_price);
			cp_element.change();
			updatess();
		});
  
		$(document).on("change","#list_price",function(){
			var golbal = $(this).val();
			$('table#purchase_entry_table .eb_price').each( function() {
				var price_item      = $(this); 
				var price      = $(this).closest("tr"); 
				var prices     = price.find(".list_price"); 
				var list_price = price.find(".list_price").find(":selected").val(); 
				if(list_price == "" || list_price == null){
					prices.children().each(function(){ 
					if($(this).data("value") == golbal){
						price_item.val($(this).data("price"));
						price_item.change();
					}
				}); 
				}
			});
			$('table#purchase_entry_table .purchase_unit_cost_without_discount').each( function() {
				var price_item      = $(this); 
				var price      = $(this).closest("tr"); 
				var prices     = price.find(".list_price"); 
				var list_price = price.find(".list_price").find(":selected").val(); 
				if(list_price == "" || list_price == null){
					prices.children().each(function(){ 
					if($(this).data("value") == golbal){
						price_item.val($(this).data("price"));
						price_item.change();
					}
				}); 
				}
			});
		});
		$(document).on('click', '.add_new_supplier', function() { 
			// setTimeout(() => {
				// alert($('.button-submit').html())
				html  =  '<button type="submit" id="contact-submit" class="btn btn-primary">Save</button>';
				$('.button-submit').find('#contact-submit').remove();
				// alert($('.button-submit').html())
				$('.button-submit').append(html);
			// }, 3000);  
		});
	</script>
	@include('purchase.partials.keyboard_shortcuts')
	@yield('child_script')
@endsection
