@extends('layouts.app')

{{-- *1* --}}
@php
	if (!empty($status) && $status == 'quotation') {
		$title = __('lang_v1.add_quotation');
	} else if (!empty($status) && $status == 'draft') {
		$title = __('lang_v1.add_draft');
	}else if (!empty($status) && $status == 'ApprovedQuotation') {
		$title = __('lang_v1.add_approve');
	} else {
		$title = __('sale.add_sale');
	}
@endphp
@section('title', $title)
{{-- *2* --}}
@section('content')
@php
	 $pull            = in_array(session()->get('lang', config('app.locale')), config('constants.langs_rtl')) ? 'pull-left' : 'pull-right';
@endphp

		{{-- *2/1* CURRENCY SECTION --}}
		<!-- Page level currency setting -->
		<input type="hidden" id="type_of_sell" value="{{$title}}">
		<input type="hidden" id="p_symbol" value="{{$currency_details->symbol}}">
		<input type="hidden" id="p_thousand" value="{{$currency_details->thousand_separator}}">
		<input type="hidden" id="p_decimal" value="{{$currency_details->decimal_separator}}">
		<input type="hidden" id="p_code" value="{{$currency_details->code}}">
		<!-- Content Header (Page header) -->
		{{-- *2/2* HEADER SECTION --}}
		{{-- ****************************************** --}}
			<section class="content-header">
				<h1>{{$title}}</h1>
				<h5><i><b>{{ "   Sales  >  " }} </b>{{ "Create " . $title }}  <b> {{"   "}} </b></i><b class="{{$pull}}">{!!__('izo.main_currency')!!} : {{$currency_details->symbol}}</b></h5>
            
			</section>
		{{-- ****************************************** --}}
		<!-- /. Header -->
		
		<!-- Main Content -->
		{{-- *2/3* MAIN PAGE SECTION --}}
		{{-- ****************************************** --}}
			<section class="content no-print" >
				<input type="hidden" id="amount_rounding_method" value="{{$pos_settings['amount_rounding_method'] ?? ''}}">
				@if(!empty($pos_settings['allow_overselling']))
					<input type="hidden" id="is_overselling_allowed">
				@endif
				@if(session('business.enable_rp') == 1)
					<input type="hidden" id="reward_point_enabled">
				@endif
				@if(count($business_locations) > 0)
				<div class="row">
					@php
						$default_location2 = null;
						if(count($business_locations) == 1){
						$default_location2 = array_key_first($business_locations->toArray());
						}
					@endphp
					<div class="col-sm-6">
						<div class="form-group">
							<div class="input-group hide" >
								<span class="input-group-addon">
									<i class="fa fa-map-marker"></i>
								</span>
								{!! Form::select('select_location_id', $business_locations, $default_location2 , ['class' => 'form-control  ',
																'id' => 'select_location_id', 
																'required', 'autofocus',
																'placeholder'=>__('lang_v1.select_location')], $bl_attributes); !!}
								<span class="input-group-addon">
									@show_tooltip(__('tooltip.sale_location'))
								</span> 
							</div>
						</div>
					</div>
					{{-- @if(in_array('subscription', $enabled_modules))
					<div class="col-md-6 pull-right col-sm-6" >
						<div class="checkbox">
							<label>
								{!! Form::checkbox('is_recurring', 1, false, ['class' => 'input-icheck', 'id' => 'is_recurring']); !!} @lang('lang_v1.subscribe')?
							</label><button type="button" data-toggle="modal" data-target="#recurringInvoiceModal" class="btn btn-link"><i class="fa fa-external-link"></i></button>@show_tooltip(__('lang_v1.recurring_invoice_help'))
						</div>
					</div>
					@endif --}}
				</div>
				@endif

				@php
					$custom_labels = json_decode(session('business.custom_labels'), true);
				@endphp
				<input type="hidden" class="con_id" id="con_id" name="con_id" value="1">
				<input type="hidden" id="item_addition_method" value="{{$business_details->item_addition_method}}">
				{!! Form::open(['url' => action('SellPosController@store'), 'method' => 'post', 'id' => 'add_sell_form', 'files' => true ]) !!}
					<div class="row">
						<div class="col-md-12 col-sm-12">
							@component('components.widget', ['class' => 'box-primary' ,'title'=>__('sale.main_section'),])
								<div class="row" style="padding:10px; margin:0px 5%" > 
									<div class="col-sm-4" @if(session()->get('user.language', config('app.locale')) != "ar") style="background-color: #f7f7f7;border-radius:10px;padding:10px;box-shadow:0px 0px 10px #00000023;margin-right:5%;" @else style="background-color: #f7f7f7;border-radius:10px;padding:10px;box-shadow:0px 0px 10px #00000023;margin-left:5%;"  @endif>
										{!! Form::hidden('location_id', !empty($default_location) ? $default_location->id : null , ['id' => 'location_id', 'data-receipt_printer_type' => !empty($default_location->receipt_printer_type) ? $default_location->receipt_printer_type : 'browser', 'data-default_payment_accounts' => !empty($default_location) ? $default_location->default_payment_accounts : '']); !!}
										{{-- customer --}}
										<div class="@if(!empty($commission_agent)) col-sm-12 @else col-sm-12 @endif">
											<div class="form-group">
												{!! Form::label('contact_id', __('contact.customer') . ':*') !!}
												<div class="input-group">
													<span class="input-group-addon">
														<i class="fa fa-user"></i>
													</span>
													@if($walk_in_customer)
														<input type="hidden" id="default_customer_id" 
														value="{{ $walk_in_customer['id']}}" >
														<input type="hidden" id="default_customer_name" 
														value="{{ $walk_in_customer['name']}}" >
														<input type="hidden" id="default_customer_balance" value="{{ $walk_in_customer['balance'] ?? ''}}" >
														<input type="hidden" id="default_customer_address" value="{{ $walk_in_customer['shipping_address'] ?? ''}}" >
														@if(!empty($walk_in_customer['price_calculation_type']) && $walk_in_customer['price_calculation_type'] == 'selling_price_group')
															<input type="hidden" id="default_selling_price_group" 
														value="{{ $walk_in_customer['selling_price_group_id'] ?? ''}}" > --}}
														@endif
													@endif
													{!! Form::select('contact_id', 
														[], null, ['class' => 'form-control mousetrap select2', 'id' => 'customer_id', 'placeholder' => 'Enter Customer name / phone', 'required']); !!}
													<span class="input-group-btn">
														<button type="button" class="btn btn-default bg-white btn-flat add_new_customer" data-name=""><i class="fa fa-plus-circle text-primary fa-lg"></i></button>
													</span>
												</div>
											</div>
											<small>
												<strong>
													@lang('lang_v1.billing_address')
												</strong>
												<div id="billing_address_div">
													{!! $walk_in_customer['contact_address'] ?? '' !!}
												</div>
												<br>
												<strong>
													@lang('lang_v1.shipping_address'):
												</strong>
												<div id="shipping_address_div">
													@if($walk_in_customer)
														{{$walk_in_customer['supplier_business_name'] ?? ''}},<br>
														{{$walk_in_customer['name'] ?? ''}},<br>
														{{$walk_in_customer['shipping_address'] ?? ''}}
													@endif
												</div>					
											</small>
										</div>
										{!! Form::hidden('default_price_group', null, ['id' => 'default_price_group']) !!}
									</div>
									<div class="col-sm-7" style="background-color: #f7f7f7;border-radius:10px;padding:20px;box-shadow:0px 0px 10px #00000023">
										{{-- *2/3/1* invoice date  --}}
										<div class="@if(!empty($commission_agent)) col-sm-6 @else col-md-6 @endif">
											<div class="form-group">
												{!! Form::label('transaction_date', __('sale.sale_date') . ':*') !!}
												<div class="input-group">
													<span class="input-group-addon">
														<i class="fa fa-calendar"></i>
													</span>
													{!! Form::text('transaction_date', $default_datetime, ['class' => 'form-control', 'readonly', 'required']); !!}
												</div>
											</div>
										</div>
										{{-- invoice no schema
										<div class="col-sm-4">
											<div class="form-group">
												{!! Form::label('invoice_scheme_id', __('invoice.invoice_scheme') . ':') !!}
												{!! Form::select('invoice_scheme_id', $invoice_schemes,  $default_invoice_schemes->id, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select')]); !!}
											</div>
										</div> --}}
										{{-- *2/3/2* patterns --}}
										 
										@if(count($patterns)>1)
											<div class="col-sm-6">
												<div class="form-group">
													{!! Form::label('pattern_id', __('business.patterns') . ':*') !!}
													{!! Form::select('pattern_id', $patterns, null , ['class' => 'pattern_id form-control select2', 'required','placeholder' => __('messages.please_select')]); !!}
												</div>
											</div>
										@else
											@php  $default  =  array_key_first($patterns); @endphp 
											<div class="col-sm-6">
												<div class="form-group">
													{!! Form::label('pattern_id', __('business.patterns') . ':*') !!}
													{!! Form::select('pattern_id', $patterns, $default , ['class' => 'pattern_id form-control select2', 'required']); !!}
												</div>
											</div>
										@endif
										{{-- *2/3/3* term pay --}}
										<div class="col-md-6">
											<div class="form-group">
											<div class="multi-input">
												{!! Form::label('pay_term_number', __('contact.pay_term') . ':') !!} @show_tooltip(__('tooltip.pay_term'))
												<br/>
												{!! Form::number('pay_term_number', isset($walk_in_customer['pay_term_number'])?$walk_in_customer['pay_term_number']:null, ['class' => 'form-control width-40 pull-left', 'placeholder' => __('contact.pay_term')]); !!}

												{!! Form::select('pay_term_type', 
													['months' => __('lang_v1.months'), 
														'days' => __('lang_v1.days')], 
														isset($walk_in_customer['pay_term_type'])?$walk_in_customer['pay_term_type']:null, 
													['class' => 'form-control width-60 pull-left','placeholder' => __('messages.please_select')]); !!}
											</div>
											</div>
										</div>
									
										@php
											$group = (count($price_groups)>0)?array_key_first($price_groups):null;
										@endphp
										{{-- *2/3/5* price group --}}
										@if(!empty($price_groups))
											@if(count($price_groups) > 0)
												<div class="col-sm-6">
													{!! Form::label('pay_term_number', __('lang_v1.price_group') . ':') !!}
													<div class="form-group">
														<div class="input-group">
															<span class="input-group-addon">
																<i class="fas fa-money-bill-alt"></i>
															</span>
															@php
																reset($price_groups);
															@endphp
															{!! Form::hidden('hidden_price_group', key($price_groups), ['id' => 'hidden_price_group']) !!}
															{!! Form::select('price_group', $price_groups, $group , ['class' => 'form-control select2', 'id' => 'price_group','placeholder'=>__('lang_v1.select_pricgroup')]); !!}
															<span class="input-group-addon">
																@show_tooltip(__('lang_v1.price_group_help_text'))
															</span>
														</div>
													</div>
												</div>
											@else
												@php
													reset($price_groups);
												@endphp
												{!! Form::hidden('price_group', key($price_groups), ['id' => 'price_group']) !!}
											@endif
										@endif
										{{-- *2/3/6* invoice no --}}
										@can('edit_invoice_number')
											<div class="col-sm-6 hide">
												<div class="form-group">
													{!! Form::label('invoice_no', __('sale.invoice_no') . ':') !!}
													{!! Form::text('invoice_no', null, ['class' => 'form-control', 'placeholder' => __('sale.invoice_no')]); !!}
													<p class="help-block">@lang('lang_v1.keep_blank_to_autogenerate')</p>
												</div>
											</div>
										@endcan
										{{-- *2/3/7* service --}}
										@if(in_array('types_of_service', $enabled_modules) && !empty($types_of_service))
											<div class="col-md-6 col-sm-6">
												<div class="form-group">
													{!! Form::label('types_of_service_price_group', __('contact.service') . ':') !!}
													<div class="input-group">
														<span class="input-group-addon">
															<i class="fa fa-external-link-square-alt text-primary service_modal_btn"></i>
														</span>

														{!! Form::select('types_of_service_id', $types_of_service, null, ['class' => 'form-control', 'id' => 'types_of_service_id', 'style' => 'width: 100%;', 'placeholder' => __('lang_v1.select_types_of_service')]); !!}

														{!! Form::hidden('types_of_service_price_group', null, ['id' => 'types_of_service_price_group']) !!}

														<span class="input-group-addon">
															@show_tooltip(__('lang_v1.types_of_service_help'))
														</span> 
													</div>
													<small><p class="help-block hide" id="price_group_text">@lang('lang_v1.price_group'): <span></span></p></small>
												</div>
											</div>
											<div class="modal fade types_of_service_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel"></div>
										@endif
										{{-- *2/3/8* commission agent --}}
										@if(!empty($commission_agent))
										<div class="col-sm-6">
											<div class="form-group">
											{!! Form::label('commission_agent', __('lang_v1.commission_agent') . ':') !!}
											{!! Form::select('commission_agent', 
														$commission_agent, null, ['class' => 'form-control select2']); !!}
											</div>
										</div>
										@endif
										{{-- *2/3/9* store --}}
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
										{{-- *2/3/10* agent --}}
										<div class="col-md-6">
											<div class="form-group">
												{!! Form::label('types_of_service_price_group', __('home.Agent') . ':') !!}
												{{  Form::select('agent_id',$users,null,['class'=>'form-control select2 ' , "id"=>"agent_id",'placeholder'=>trans('home.Choose Agent')]) }}
											</div>
										</div>
										{{-- *2/3/11* cost center --}}
										<div class="col-md-6">
											<div class="form-group">
												{!! Form::label('types_of_service_price_group', __('home.Cost Center') . ':') !!}
												{{  Form::select('cost_center_id',$cost_centers,null,['class'=>'form-control select2 ','placeholder'=>trans('home.Cost Center')]) }}
											</div>
										</div>
										{{-- *2/3/12* project no --}}
										<div class="col-sm-6">
											<div class="form-group">
												{!! Form::label('project_no', __('sale.project_no') . ':') !!}
												{!! Form::text('project_no', null, ['class' => 'form-control', 'placeholder' => __('sale.project_no')]); !!}
												<p class="help-block">@lang('lang_v1.keep_blank_to_autogenerate')</p>
											</div>
										</div>
									 	{{-- *2/3/4* Currency --}}
										 <div class="col-md-6">
											<div class="form-group">
												<div class="multi-input">
												{!! Form::label('currency_id', __('business.currency') . ':') !!}  
												<br/>
												{!! Form::select('currency_id', $currencies, null, ['class' => 'form-control width-60 currency_id  select2', 'placeholder' => __('messages.please_select') ]); !!}
												{!! Form::number('currency_id_amount', null, ['class' => 'form-control width-40 pull-right currency_id_amount'   ]); !!}
												</div>
											</div>
											<br/>
											<div class="check_dep_curr hide">
												{!! Form::checkbox('depending_curr',null, 0, ['class' => 'depending_curr' ,'id'=>'depending_curr'   ]); !!}
												{!! Form::label('depending_curr', __('Depending On Currency Column') . '') !!}  
											</div>
											<br/> 
											<div class="check_dep_curr  hide"><input  type="checkbox" name="dis_currency" value="1"> <b>Discount</b> @show_tooltip(__('tooltip.dis_currency'))<br ></div>
										</div>
										{{-- *2/3/5* List Price--}}
										{{-- #2024-8-6 --}}
										<div class="col-sm-12">
											<div class="form-group">
												{!! Form::label('list_price', __('List  Of Prices').':') !!}
												{!! Form::select('list_price',$list_of_prices,null, ['class' => 'form-control select2' , 'id' => 'list_price' ]); !!}
											</div>
										</div>
										@php
											$custom_field_1_label = !empty($custom_labels['sell']['custom_field_1']) ? $custom_labels['sell']['custom_field_1'] : '';

											$is_custom_field_1_required = !empty($custom_labels['sell']['is_custom_field_1_required']) && $custom_labels['sell']['is_custom_field_1_required'] == 1 ? true : false;

											$custom_field_2_label = !empty($custom_labels['sell']['custom_field_2']) ? $custom_labels['sell']['custom_field_2'] : '';

											$is_custom_field_2_required = !empty($custom_labels['sell']['is_custom_field_2_required']) && $custom_labels['sell']['is_custom_field_2_required'] == 1 ? true : false;

											$custom_field_3_label = !empty($custom_labels['sell']['custom_field_3']) ? $custom_labels['sell']['custom_field_3'] : '';

											$is_custom_field_3_required = !empty($custom_labels['sell']['is_custom_field_3_required']) && $custom_labels['sell']['is_custom_field_3_required'] == 1 ? true : false;

											$custom_field_4_label = !empty($custom_labels['sell']['custom_field_4']) ? $custom_labels['sell']['custom_field_4'] : '';

											$is_custom_field_4_required = !empty($custom_labels['sell']['is_custom_field_4_required']) && $custom_labels['sell']['is_custom_field_4_required'] == 1 ? true : false;
										@endphp
										@if(!empty($custom_field_1_label))
											@php
												$label_1 = $custom_field_1_label . ':';
												if($is_custom_field_1_required) {
													$label_1 .= '*';
												}
											@endphp

											<div class="col-md-4">
												<div class="form-group">
													{!! Form::label('custom_field_1', $label_1 ) !!}
													{!! Form::text('custom_field_1', null, ['class' => 'form-control','placeholder' => $custom_field_1_label, 'required' => $is_custom_field_1_required]); !!}
												</div>
											</div>
										
										@endif
										@if(!empty($custom_field_2_label))
											@php
										
												$label_2 = $custom_field_2_label . ':';
												if($is_custom_field_2_required) {
													$label_2 .= '*';
												}
											@endphp

											<div class="col-md-4">
												<div class="form-group">
													{!! Form::label('custom_field_2', $label_2 ) !!}
													{!! Form::text('custom_field_2', null, ['class' => 'form-control','placeholder' => $custom_field_2_label, 'required' => $is_custom_field_2_required]); !!}
												</div>
											</div>
										@endif
										@if(!empty($custom_field_3_label))
											@php
												$label_3 = $custom_field_3_label . ':';
												if($is_custom_field_3_required) {
													$label_3 .= '*';
												}
											@endphp

											<div class="col-md-4">
												<div class="form-group">
													{!! Form::label('custom_field_3', $label_3 ) !!}
													{!! Form::text('custom_field_3', null, ['class' => 'form-control','placeholder' => $custom_field_3_label, 'required' => $is_custom_field_3_required]); !!}
												</div>
											</div>
										@endif
										@if(!empty($custom_field_4_label))
											@php
												$label_4 = $custom_field_4_label . ':';
												if($is_custom_field_4_required) {
													$label_4 .= '*';
												}
											@endphp

											<div class="col-md-4">
												<div class="form-group">
													{!! Form::label('custom_field_4', $label_4 ) !!}
													{!! Form::text('custom_field_4', null, ['class' => 'form-control','placeholder' => $custom_field_4_label, 'required' => $is_custom_field_4_required]); !!}
												</div>
											</div>
										@endif
											{{-- document --}}
											{{-- <div class="col-sm-12">
												<div class="form-group">
													{!! Form::label('upload_document', __('purchase.attach_document') . ':') !!}
													{!! Form::file('sell_document', ['id' => 'upload_document', 'accept' => implode(',', array_keys(config('constants.document_upload_mimes_types')))]); !!}
													<p class="help-block">
														@lang('purchase.max_file_size', ['size' => (config('constants.document_size_limit') / 1000000)])
														@includeIf('components.document_help_text')
													</p>
												</div>
											</div> --}}
										{{-- *2/3/13* status --}}
										@if(!empty($status))
											<input type="hidden"   name="status" id="status" value="{{$status}}">
											@else
											<div class="@if(!empty($commission_agent)) col-sm-6 hide @else col-sm-6 hide @endif">
												<div class="form-group">
													{!! Form::label('status', __('sale.status') . ':*') !!}
													{{--{!! Form::select('status', ['final' => __('sale.final'), 'draft' => __('sale.draft'), 'quotation' => __('lang_v1.quotation'), 'proforma' => __('lang_v1.proforma')], null, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'required']); !!}--}}
													{!! Form::select('status', [  'delivered' => __('sale.delivery_s'),'final' => __('sale.final'),'proforma' => __('lang_v1.proforma'),'quotation' => __('lang_v1.quotation'), 'draft' => __('sale.draft')], "final", ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'),  'required']); !!}
												</div>
											</div>
										@endif
										{{-- *2/3/14* Document --}}
										<div class="col-sm-12">
											<div class="form-group">
											{!! Form::label('document_sell[]', __('purchase.attach_document') . ':') !!}
											{!! Form::file('document_sell[]', ['multiple','id' => 'upload_document', 'accept' =>
											implode(',', array_keys(config('constants.document_upload_mimes_types')))]); !!}
											<p class="help-block">
												@lang('purchase.max_file_size', ['size' => (config('constants.document_size_limit') / 1000000)])
												@includeIf('components.document_help_text')
											</p>
											</div>
										</div>
											<!-- Call restaurant module if defined -->
											{{-- @if(in_array('tables' ,$enabled_modules) || in_array('service_staff' ,$enabled_modules))
												<span id="restaurant_module_span">
												</span>
											@endif --}}
									</div>
								</div>
							@endcomponent
							@component('components.widget', ['class' => 'box-primary' ,'title'=>__('sale.search_section')])
								<div class="col-sm-10 col-sm-offset-1">
									<h4>@lang('lang_v1.currency') :  {{$currency_data['symbol']}} </h4>
									<div class="form-group">
										<div class="input-group">
											<div class="input-group-btn">
												<button type="button" class="btn btn-default bg-white btn-flat" data-toggle="modal" data-target="#configure_search_modal" title="{{__('lang_v1.configure_product_search')}}"><i class="fa fa-barcode"></i></button>
											</div>
											{!! Form::text('search_product', null, ['class' => 'form-control mousetrap', 'id' => 'search_product', 'placeholder' => __('lang_v1.search_product_placeholder'),
											'autofocus' => is_null($default_location)? false : true,
											]); !!}
											<span class="input-group-btn">
												<button type="button" class="btn btn-default bg-white btn-flat pos_add_quick_product" data-href="{{action('ProductController@quickAdd')}}" data-container=".quick_add_product_modal"><i class="fa fa-plus-circle text-primary fa-lg"></i></button>
											</span>
										</div>
									</div>
								</div>
								<div class="row col-sm-12 pos_product_div" style="min-height: 0">
									<input type="hidden" name="sell_price_tax" id="sell_price_tax" value="{{$business_details->sell_price_tax}}">
									<!-- Keeps count of product rows -->
									<input type="hidden" id="product_row_count" value="0">
									@php
										$hide_tax = '';
										if( session()->get('business.enable_inline_tax') == 0){
											$hide_tax = 'hide';
										}
									@endphp
									{{-- table for products  used for  direct sell --}}
									<div class="table-responsive">
									<table class="table table-condensed table-bordered table-striped table-responsive dataTable" id="pos_table">
										<thead>
											<tr>
												<th>
													@lang("home.No")
												</th>
												<th>
													@lang("home.SE")
												</th>
												<th class="text-center">	
													@lang('sale.product') 
												</th>
												<th class="text-center">
													@lang('sale.qty')
												</th>
												@if(!empty($pos_settings['inline_service_staff']))
													<th class="text-center">
														@lang('restaurant.service_staff')
													</th>
												@endif

												<th class="text-center" @can('edit_product_price_from_sale_screen')) hide @endcan>
													@lang('sale.unit_price_exc'). {{$currency_details->symbol}}
												</th>
												<th class="text-center" @can('edit_product_price_from_sale_screen')) hide @endcan>
													@lang('sale.unit_price_inc'). {{$currency_details->symbol}}
												</th>
												<th class="text-center  curr_column br_dis cur_check hide" @can('edit_product_price_from_sale_screen')) hide @endcan>
													@lang('sale.unit_price_exc_new_currency')
												</th>
												<th class="text-center  curr_column   hide" @can('edit_product_price_from_sale_screen')) hide @endcan>
													@lang('sale.unit_price_inc_new_currency')
												</th>

												<th class="text-center" @can('edit_product_discount_from_sale_screen') hide @endcan>
													@lang('receipt.discount')
												</th>

												<th  class="text-center {{$hide_tax}}">
													@lang('sale.tax')
												</th>
												{{--  discount %  --}}
												<th class="text-center">
													@lang('home.percentage_discount')
												</th>
												{{--  discount Fixed --}}
												{{-- <th class="text-center">
													@lang('home.amount_discount')
												</th> --}}

												<th class="text-center {{$hide_tax}}">
													@lang('sale.price_inc_tax')
												</th>

												<th class="text-center  ">
													@lang('sale.cost_exc'). {{$currency_details->symbol}}
												</th>
												<th class="text-center  ">
													@lang('sale.cost_inc'). {{$currency_details->symbol}}
												</th>
												<th class="text-center curr_column ar_dis cur_check hide">
													@lang('sale.cost_exc_new_currency')
												</th>
												<th  class="text-center curr_column   hide">
													@lang('sale.cost_inc_new_currency')
												</th>

												@if(!empty($common_settings['enable_product_warranty']) && !empty($is_direct_sell))
												<th>@lang('lang_v1.warranty')</th>
												@endif

												<th class="text-center">
													@lang('sale.subtotal'). {{$currency_details->symbol}}
												</th>

												<th class="text-center"><i class="fas fa-times" aria-hidden="true"></i></th>
											</tr>
										</thead>
										<tbody>
										</tbody>
									</table>
									</div>
									<div class="table-responsive">
										<table class="table table-condensed table-bordered table-striped">
										<tr>
											<td>
												<div style="font-size:large" class="pull-right">
												<b>@lang('sale.item'):</b> 
												<span  class="total_quantity">0</span>
												&nbsp;&nbsp;&nbsp;&nbsp;
												<b>@lang('sale.total') . {{$currency_details->symbol}}: </b>
													<span class="price_total">0</span>
												</div>
											</td>
										</tr>
									</table>
									</div>
									<div class="table-responsive" >
										<table class="table table-condensed table-bordered table-striped">
											<tr>
												<td>
													<div style="font-size:large" class="pull-right">
													&nbsp;&nbsp;&nbsp;&nbsp;
													<b class="text-right cur_symbol hide">@lang('sale.total'): </b>
													<span class="price_total_curr hide">0</span>
													</div>
												</td>
											</tr>
										</table>
									</div>
								</div>
							@endcomponent
							@component('components.widget', ['class' => 'box-primary' ,'title'=>__('sale.footer_section')])
								<div class="col-md-3">
									<div class="form-group">
										{!! Form::label('discount_type', __('sale.discount_type') . ':*' ) !!}
										<div class="input-group">
											<span class="input-group-addon">
												<i class="fa fa-info"></i>
											</span>
											{!! Form::select('discount_type', [ '' => __('lang_v1.none'), 'fixed_before_vat' => __( 'home.fixed before vat' ), 'fixed_after_vat' => __( 'home.fixed after vat' ), 'percentage' => __( 'lang_v1.percentage' )], '', ['class' => 'form-control select2']); !!}
										</div>
									</div>
								</div>
								@php
									$max_discount = !is_null(auth()->user()->max_sales_discount_percent) ? auth()->user()->max_sales_discount_percent : '';

									//if sale discount is more than user max discount change it to max discount
									$sales_discount = $business_details->default_sales_discount;
									if($max_discount != '' && $sales_discount > $max_discount) $sales_discount = $max_discount;
								@endphp
								<div class="col-md-3">
									<div class="form-group">
										{!! Form::label('discount_amount', __('sale.discount_amount') . ':*' ) !!}. {{$currency_details->symbol}}
										<div class="input-group">
											<span class="input-group-addon">
												<i class="fa fa-info"></i>
											</span>
											{!! Form::text('discount_amount', @num_format($sales_discount), ['class' => 'form-control input_number', 'data-default' => $sales_discount, 'data-max-discount' => $max_discount, 'data-max-discount-error_msg' => __('lang_v1.max_discount_error_msg', ['discount' => $max_discount != '' ? @num_format($max_discount) : '']) ]); !!}
										</div>
									</div>
								</div>
								<div class="col-md-3 pull-left" ><br>
									<b class="i_curr hide">@lang( 'sale.discount_amount' ): (-)</b> 
									<span class="display_currency hide" id="total_discount_curr">0</span>
								</div>
								<div class="col-md-3 pull-right"><br>
									<b>@lang( 'sale.discount_amount' ). {{$currency_details->symbol}} : (-)</b> 
									<span class="display_currency" id="total_discount">0</span>
								</div>
								<div class="clearfix"></div>
								<div class="col-md-12 well well-sm bg-light-gray @if(session('business.enable_rp') != 1) hide @endif">
									<input type="hidden" name="rp_redeemed" id="rp_redeemed" value="0">
									<input type="hidden" name="rp_redeemed_amount" id="rp_redeemed_amount" value="0">
									<div class="col-md-12"><h4>{{session('business.rp_name')}}</h4></div>
									<div class="col-md-4">
										<div class="form-group">
											{!! Form::label('rp_redeemed_modal', __('lang_v1.redeemed') . ':' ) !!}
											<div class="input-group">
												<span class="input-group-addon">
													<i class="fa fa-gift"></i>
												</span>
												{!! Form::number('rp_redeemed_modal', 0, ['class' => 'form-control direct_sell_rp_input', 'data-amount_per_unit_point' => session('business.redeem_amount_per_unit_rp'), 'min' => 0, 'data-max_points' => 0, 'data-min_order_total' => session('business.min_order_total_for_redeem') ]); !!}
												<input type="hidden" id="rp_name" value="{{session('business.rp_name')}}">
											</div>
										</div>
									</div>
									<div class="col-md-4">
										<p><strong>@lang('lang_v1.available'):</strong> <span id="available_rp">0</span></p>
									</div>
									<div class="col-md-4">
										<p><strong>@lang('lang_v1.redeemed_amount'):</strong> (-)<span id="rp_redeemed_amount_text">0</span></p>
									</div>
								</div>
								<div class="clearfix"></div>
								@php
									$id   =  $taxes["tax_rates"]->toArray() ;
									$keys =  array_keys($id) ;
								@endphp
								<div class="col-md-3">
									<div class="form-group">
										{!! Form::label('tax_rate_id', __('sale.order_tax') . ': ' ) !!}. {{$currency_details->symbol}}
										<div class="input-group">
											<span class="input-group-addon">
												<i class="fa fa-info"></i>
											</span>
											{!! Form::select('tax_rate_id', $taxes['tax_rates'], $keys[1], ['placeholder' => __('messages.please_select'), 'class' => 'form-control', 'data-default'=> $keys[1]], $taxes['attributes']); !!}

											<input type="hidden" name="tax_calculation_amount" id="tax_calculation_amount" 
											value="@if(empty($edit)) {{@num_format($keys[1])}} @else {{@num_format(optional($transaction->tax)->amount)}} @endif" data-default="{{$business_details->tax_calculation_amount}}">
										</div>
									</div>
								</div>
								<div class="col-md-3 col-md-offset-3 pull-left" ><br>
										<b class="t_curr hide">@lang( 'sale.order_tax' ): (+)</b> 
									<span class="display_currency hide" id="order_tax_curr">0</span>
								</div>				
								<div class="col-md-3 col-md-offset-3 pull-right">
									<b>@lang( 'sale.order_tax' ) . {{$currency_details->symbol}}: (+)</b> 
									<span class="display_currency" id="order_tax">0</span>
								</div>	
								<div class="col-md-12">
									<div class="form-group">
										{!! Form::label('sell_note',__('sale.sell_note')) !!}
										{!! Form::select('sale_note',  $terms ,null , ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), "id"=>"sale_note"]); !!}
									</div>
								</div>
								<input type="hidden" name="is_direct_sale" value="1">
							@endcomponent
							@component('components.widget', ['class' => 'box-primary hide' ,'title'=>__('sale.payment_section')])
								<div class="col-md-4">
									<div class="form-group">
										{!! Form::label('shipping_details', __('sale.shipping_details')) !!}
										{!! Form::textarea('shipping_details',null, ['class' => 'form-control','placeholder' => __('sale.shipping_details') ,'rows' => '3', 'cols'=>'30']); !!}
									</div>
								</div>
								<div class="col-md-4">
									<div class="form-group">
										{!! Form::label('shipping_address', __('lang_v1.shipping_address')) !!}
										{!! Form::textarea('shipping_address',null, ['class' => 'form-control','placeholder' => __('lang_v1.shipping_address') ,'rows' => '3', 'cols'=>'30']); !!}
									</div>
								</div>
								<div class="col-md-4">
									<div class="form-group">
										{!!Form::label('shipping_charges', __('sale.shipping_charges'))!!}
										<div class="input-group">
										<span class="input-group-addon">
										<i class="fa fa-info"></i>
										</span>
										{!!Form::text('shipping_charges',@num_format(0.00),['class'=>'form-control input_number','placeholder'=> __('sale.shipping_charges')]);!!}
										</div>
									</div>
								</div>
								<div class="clearfix"></div>
								<div class="col-md-4">
									<div class="form-group">
										{!! Form::label('shipping_status', __('lang_v1.shipping_status')) !!}
										{!! Form::select('shipping_status',$shipping_statuses, null, ['class' => 'form-control','placeholder' => __('messages.please_select')]); !!}
									</div>
								</div>
								<div class="col-md-4">
									<div class="form-group">
										{!! Form::label('delivered_to', __('lang_v1.delivered_to') . ':' ) !!}
										{!! Form::text('delivered_to', null, ['class' => 'form-control','placeholder' => __('lang_v1.delivered_to')]); !!}
									</div>
								</div>
								@php
									$shipping_custom_label_1 = !empty($custom_labels['shipping']['custom_field_1']) ? $custom_labels['shipping']['custom_field_1'] : '';

									$is_shipping_custom_field_1_required = !empty($custom_labels['shipping']['is_custom_field_1_required']) && $custom_labels['shipping']['is_custom_field_1_required'] == 1 ? true : false;

									$shipping_custom_label_2 = !empty($custom_labels['shipping']['custom_field_2']) ? $custom_labels['shipping']['custom_field_2'] : '';

									$is_shipping_custom_field_2_required = !empty($custom_labels['shipping']['is_custom_field_2_required']) && $custom_labels['shipping']['is_custom_field_2_required'] == 1 ? true : false;

									$shipping_custom_label_3 = !empty($custom_labels['shipping']['custom_field_3']) ? $custom_labels['shipping']['custom_field_3'] : '';
									
									$is_shipping_custom_field_3_required = !empty($custom_labels['shipping']['is_custom_field_3_required']) && $custom_labels['shipping']['is_custom_field_3_required'] == 1 ? true : false;

									$shipping_custom_label_4 = !empty($custom_labels['shipping']['custom_field_4']) ? $custom_labels['shipping']['custom_field_4'] : '';
									
									$is_shipping_custom_field_4_required = !empty($custom_labels['shipping']['is_custom_field_4_required']) && $custom_labels['shipping']['is_custom_field_4_required'] == 1 ? true : false;

									$shipping_custom_label_5 = !empty($custom_labels['shipping']['custom_field_5']) ? $custom_labels['shipping']['custom_field_5'] : '';
									
									$is_shipping_custom_field_5_required = !empty($custom_labels['shipping']['is_custom_field_5_required']) && $custom_labels['shipping']['is_custom_field_5_required'] == 1 ? true : false;
								@endphp

								@if(!empty($shipping_custom_label_1))
									@php
										$label_1 = $shipping_custom_label_1 . ':';
										if($is_shipping_custom_field_1_required) {
											$label_1 .= '*';
										}
									@endphp

									<div class="col-md-4">
										<div class="form-group">
											{!! Form::label('shipping_custom_field_1', $label_1 ) !!}
											{!! Form::text('shipping_custom_field_1', null, ['class' => 'form-control','placeholder' => $shipping_custom_label_1, 'required' => $is_shipping_custom_field_1_required]); !!}
										</div>
									</div>
								@endif
								@if(!empty($shipping_custom_label_2))
									@php
										$label_2 = $shipping_custom_label_2 . ':';
										if($is_shipping_custom_field_2_required) {
											$label_2 .= '*';
										}
									@endphp

									<div class="col-md-4">
										<div class="form-group">
											{!! Form::label('shipping_custom_field_2', $label_2 ) !!}
											{!! Form::text('shipping_custom_field_2', null, ['class' => 'form-control','placeholder' => $shipping_custom_label_2, 'required' => $is_shipping_custom_field_2_required]); !!}
										</div>
									</div>
								@endif
								@if(!empty($shipping_custom_label_3))
									@php
										$label_3 = $shipping_custom_label_3 . ':';
										if($is_shipping_custom_field_3_required) {
											$label_3 .= '*';
										}
									@endphp

									<div class="col-md-4">
										<div class="form-group">
											{!! Form::label('shipping_custom_field_3', $label_3 ) !!}
											{!! Form::text('shipping_custom_field_3', null, ['class' => 'form-control','placeholder' => $shipping_custom_label_3, 'required' => $is_shipping_custom_field_3_required]); !!}
										</div>
									</div>
								@endif
								@if(!empty($shipping_custom_label_4))
									@php
										$label_4 = $shipping_custom_label_4 . ':';
										if($is_shipping_custom_field_4_required) {
											$label_4 .= '*';
										}
									@endphp

									<div class="col-md-4">
										<div class="form-group">
											{!! Form::label('shipping_custom_field_4', $label_4 ) !!}
											{!! Form::text('shipping_custom_field_4', null, ['class' => 'form-control','placeholder' => $shipping_custom_label_4, 'required' => $is_shipping_custom_field_4_required]); !!}
										</div>
									</div>
								@endif
								@if(!empty($shipping_custom_label_5))
									@php
										$label_5 = $shipping_custom_label_5 . ':';
										if($is_shipping_custom_field_5_required) {
											$label_5 .= '*';
										}
									@endphp

									<div class="col-md-4">
										<div class="form-group">
											{!! Form::label('shipping_custom_field_5', $label_5 ) !!}
											{!! Form::text('shipping_custom_field_5', null, ['class' => 'form-control','placeholder' => $shipping_custom_label_5, 'required' => $is_shipping_custom_field_5_required]); !!}
										</div>
									</div>
								@endif
								<div class="col-md-4">
									<div class="form-group">
										{!! Form::label('shipping_documents', __('lang_v1.shipping_documents') . ':') !!}
										{!! Form::file('shipping_documents[]', ['id' => 'shipping_documents', 'multiple', 'accept' => implode(',', array_keys(config('constants.document_upload_mimes_types')))]); !!}
										<p class="help-block">
											@lang('purchase.max_file_size', ['size' => (config('constants.document_size_limit') / 1000000)])
											@includeIf('components.document_help_text')
										</p>
									</div>
								</div>
								<div class="clearfix"></div>
							
							@endcomponent
							<div class="row">
								<div class="col-md-4 col-md-offset-8">
									@if(!empty($pos_settings['amount_rounding_method']) && $pos_settings['amount_rounding_method'] > 0)
									<small id="round_off"><br>(@lang('lang_v1.round_off'): <span id="round_off_text">0</span>)</small>
									<br/>
									<input type="hidden" name="round_off_amount" 
										id="round_off_amount" value=0>
									@endif
									<div style="font-size:large"><b>@lang('sale.total_payable'). {{$currency_details->symbol}}: </b>
										<input type="hidden" name="final_total" id="final_total_input">
										<span id="total_payable">0</span>
									</div><br>
									<span class="o_curr hide" style="font-size:large"><b>@lang('sale.total_payable'): </b>
									</span>
									<input type="hidden" name="final_total_curr" id="final_total_input_curr">
									<span id="total_payable_curr" class="hide">0</span>
								</div>
							</div>
						</div>
					</div>
					@if(empty($status) || !in_array($status, ['quotation', 'draft']))
						@can('sell.payments')
							@component('components.widget', ['class' => 'box-solid hide', 'id' => "payment_rows_div", 'title' => __('purchase.add_payment')])
							<div class="payment_row hide">
								<div class="row">
									<div class="col-md-12 mb-12">
										<strong>@lang('lang_v1.advance_balance'):</strong> <span id="advance_balance_text"></span>
										{!! Form::hidden('advance_balance', null, ['id' => 'advance_balance', 'data-error-msg' => __('lang_v1.required_advance_balance_not_available')]); !!}
									</div>
								</div>
								@include('sale_pos.partials.payment_row_form', ['row_index' => 0, 'show_date' => true,'cheque_type'=>0])
								<hr>
								<div class="row">
									<div class="col-sm-12">
										<div class="pull-right"><strong>@lang('lang_v1.balance'):</strong> <span class="balance_due">0.00</span></div>
									</div>
								</div>
							</div>
							@endcomponent
						@endcan
					@endif
					
					<div class="row">
						{!! Form::hidden('is_save_and_print', 0, ['id' => 'is_save_and_print']); !!}
						<div class="col-sm-12 sub    @if(session()->get('user.language', config('app.locale'))=='ar') text-right @else text-left  @endif ">
							{{-- <button type="button" id="submit-sell" class="btn btn-primary btn-flat">@lang('messages.save')</button> --}}
							<a data-pattern="3434"  data-href='{{action('SellController@check_msg')}}'  id='submit-sell' data-container='.view_modal' class='update_transfer btn btn-modal btn-primary pull-right btn-flat'>@lang('messages.save')</a>
							{{-- <button type="button" id="save-and-print" class="btn btn-primary btn-flat">@lang('lang_v1.save_and_print')</button> --}}
						</div>
					</div>
					
					@if(empty($pos_settings['disable_recurring_invoice']))
						@include('sale_pos.partials.recurring_invoice_modal')
					@endif
					
				{!! Form::close() !!}
			</section>
			<div id="sell_proccess"></div>
			<div class="modal fade contact_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
				@include('contact.create', ['quick_add' => true])
			</div>
		{{-- ****************************************** --}}
		
		<!-- /.content -->
		{{-- *2/4* MODAL SECTION --}}
		{{-- ****************************************** --}}
			<div class="modal fade register_details_modal" tabindex="-1" role="dialog" 
				aria-labelledby="gridSystemModalLabel">
			</div>
			<div class="modal fade close_register_modal" tabindex="-1" role="dialog" 
				aria-labelledby="gridSystemModalLabel">
			</div>
		{{-- ****************************************** --}}
	
		<!-- quick product modal -->
		{{-- *2/4* QUICK SECTION --}}
		{{-- ****************************************** --}}
			<div class="modal fade quick_add_product_modal" tabindex="-1" role="dialog" aria-labelledby="modalTitle"></div>
		{{-- ****************************************** --}}
		@include('sale_pos.partials.configure_search_modal')
		<input type="hidden" name="view_type" id="view_type" value="sell_add">
		<input type="hidden" name="change_dis" id="change_dis" value="-1">
@stop
{{-- *3* --}}
@section('javascript')
		{{-- RELATION SECTION --}}
		<script src="{{ asset('js/posss.js?v=' . $asset_v) }}"></script>
		<script src="{{ asset('js/producte.js?v=' . $asset_v) }}"></script>
		<script src="{{ asset('js/opening_stock.js?v=' . $asset_v) }}"></script>
		<script src="{{ asset('js/sale_row_discount.js?v=' . $asset_v) }}"></script>
		<!-- Call restaurant module if defined -->
		@if(in_array('tables' ,$enabled_modules) || in_array('modifiers' ,$enabled_modules) || in_array('service_staff' ,$enabled_modules))
			<script src="{{ asset('js/restaurant.js?v=' . $asset_v) }}"></script>
		@endif
		{{-- ADDITIONAL SECTION --}}
		<script type="text/javascript">
			updatePattern();
			$("#pattern_id").each( function(e){  
				$(this).on("change",function(){
					updatePattern();
				})
			});
			// $(document).ready(function(){
			// 	pattern = $("#pattern_id").val();
			// 	html    = "{{\URL::to('alert-check/show')}}"+"?pattern="+pattern;
			// 	$("#submit-sell").attr("data-href", html) ;
			// });
			$('#pos_table tbody').sortable({
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
				if ($('input.depending_curr').is(':checked')) {
				    depending_curr();
                }else{
				    update_os();
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
					$(".price_total_curr").addClass("hide" );
					$(".i_curr").addClass("hide");
					$("#total_discount_curr").addClass("hide");
					$(".t_curr").addClass("hide");
					$("#order_tax_curr").addClass("hide");
					$(".o_curr").addClass("hide");
					$("#total_payable_curr").addClass("hide");
					$(".z_curr").addClass("hide");
					$("#total_final_i_curr").addClass("hide");
					$(".c_curr").addClass("hide");
					$(".cur_check").addClass("hide" );
					$("#total_final_curr").addClass("hide");
					$(".check_dep_curr").addClass("hide");
					
					$('input[name="dis_currency"]').prop('checked', false);
			    	$('#depending_curr').prop('checked', false);
				}else{
					$(".cur_symbol").removeClass("hide" );
					$("#total_subtotal_cur").removeClass("hide" );
					$(".price_total_curr").removeClass("hide" );
					$(".curr_column").attr("disabled",false);
					$(".i_curr").removeClass("hide");
					$("#total_discount_curr").removeClass("hide");
					$(".t_curr").removeClass("hide");
					$("#order_tax_curr").removeClass("hide");
					$(".o_curr").removeClass("hide");
					$("#total_payable_curr").removeClass("hide");
					$(".z_curr").removeClass("hide");
					$("#total_final_i_curr").removeClass("hide");
					$(".c_curr").removeClass("hide");
					$("#total_final_curr").removeClass("hide");
					$(".cur_check").removeClass("hide");
				    $('input[name="dis_currency"]').prop('checked', true);
				    $('#depending_curr').prop('checked', true);
				// 	$(".check_dep_curr").removeClass("hide");

					$.ajax({
						url:"/symbol/amount/"+id,
						dataType: 'html',
						success:function(data){
							var object  = JSON.parse(data);
							$(".currency_id_amount").val(object.amount);
							$(".cur_symbol").html( @json(__('sale.total')) + " " + object.symbol + " : "  );
							$(".i_curr").html( @json(__('sale.discount_amount')) + " " + object.symbol +" : " + "(-)");
							$(".t_curr").html( @json(__('sale.order_tax')) + " " + object.symbol +" : " + "(+)" );
							$(".o_curr").html( @json(__('sale.total_payable')) + " " + object.symbol +" : "   );
							$(".ar_dis").html( @json(__('home.Cost without Tax currency')) + " " + object.symbol +"   "   );
							$(".ar_dis_total").html( @json(__('home.Total Currency')) + " " + object.symbol +"   "   );
							$(".br_dis").html( @json(__('lang_v1.Cost before without Tax')) + " " + object.symbol +"   "   );
							// $(".curr_column").removeClass("hide");
							update_os();
						},
					});	 
					}
			})
			$(document).ready( function() {
				
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
				
				if ($('#status').val() == 'final' || $('#status').val() == 'delivered') {
					$('#payment_rows_div').addClass('hide');
					// $('#payment_rows_div').removeClass('hide');
				} else {
					$('#payment_rows_div').addClass('hide');
				}
			
				$('.paid_on').datetimepicker({
					format: moment_date_format + ' ' + moment_time_format.format('hh a'),
					ignoreReadonly: true,
				});
				$('#shipping_documents').fileinput({
					showUpload: false,
					showPreview: false,
					browseLabel: LANG.file_browse_label,
					removeLabel: LANG.remove,
				});
				$('#method_0').change(function(){
    				var  method  = $('.payment_types_dropdown option:selected').val();
    				if (method == 'cheque') {
    					$('#pay_os_account').hide();
    				}else{
    					$('#pay_os_account').show();
    				}
    				
    			})
			});
			function updatePattern(){
				pattern = $("#pattern_id").val();
				row     = $(".pos_unit_price").val();
				type    = $("#type_of_sell").val();
				button  = $(".sub").html("");
				button  = $(".sub").html("<a  data-href='{{action('SellController@check_msg')}}'  id='submit-sell' data-container='.view_modal' class='update_transfer btn btn-modal btn-primary pull-right btn-flat'>@lang('messages.save')</a>");
				html    = "{{\URL::to('alert-check/show')}}"+"?pattern="+pattern+"&complete="+row+"&type="+type;
				$("#submit-sell").attr("data-href", html) ;
			}

			// #2024-8-6
		$('table#pos_table').on('change', 'select.list_price', function() {
			 
			 var tr                      = $(this).closest('tr');
			 var base_unit_cost          = tr.find('input.base_unit_cost').val();
			 var base_unit_selling_price = tr.find('input.base_unit_selling_price').val();
			 var global_price            = $("#list_price").val();
			 var price = parseFloat(
				 $(this)
					 .find(':selected')
					 .data('price')
				 );
			 var cp_element = tr.find('input.pos_unit_price');
			 
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
			  
		});
   
		$(document).on("change","#list_price",function(){
			 var golbal = $(this).val();
			 $('table#pos_table .pos_unit_price').each( function() {
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
		$(document).on('click', '.add_new_customer', function() { 
			// setTimeout(() => {
				// alert($('.button-submit').html())
				html  =  '<button type="submit" id="contact-submit" class="btn btn-primary">Save</button>';
				$('.button-submit').find('#contact-submit').remove();
				// alert($('.button-submit').html())
				$('.button-submit').append(html);
			// }, 3000);  
			
		});
		</script>
		@yield('inner_script')
@endsection
