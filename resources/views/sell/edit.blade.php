@extends('layouts.app')
{{-- *1* --}}
@php
 	if (request()->input("status")== 'quotation') {
		$title = __('Edit Quotation');
		$number = __('lang_v1.quotation_no');
	} else if (request()->input("status") == 'draft') {
		$title = __('Edit Draft');
		$number = __('lang_v1.draft_no');
	}else if (request()->input("status") == 'ApprovedQuotation') {
		$title = __('Edit Approve');
		$number = __('lang_v1.approve_no');
	} else {
		$title = __('lang_v1.edit_sale');
		$number = __('sale.invoice_no');
	}
	 
@endphp
@section('title', __('sale.edit_sale'))
{{-- *2* --}}
@section('content')

	{{-- *2/1* MAIN PAGE SECTION --}}
	{{-- ******************************************** --}}
		<!-- Content Header (Page header) -->
			<section class="content-header">
				<h1>{{$title}} <small>({{$number}}: <span class="text-success">#{{$transaction->invoice_no}})</span></small></h1>
				<h5><i><b>{{ "   Sales  >  " }} </b>{{ "Edit "}} {{ $title }} {{ ": ( "   }} <b> {{$transaction->invoice_no}} </b> {{" )  "}}</i></h5>
                <br> 
			</section>
		<!-- /.header -->
		<!-- Main content -->
			<section class="content" style="margin:0px 5%">
		        <input type="hidden" id="type_of_sell" value="{{$title}}">
				<input type="hidden" id="amount_rounding_method" value="{{$pos_settings['amount_rounding_method'] ?? ''}}">
				<input type="hidden" id="amount_rounding_method" value="{{$pos_settings['amount_rounding_method'] ?? 'none'}}">
				@if(!empty($pos_settings['allow_overselling']))
					<input type="hidden" id="is_overselling_allowed">
				@endif
				@if(session('business.enable_rp') == 1)
					<input type="hidden" id="reward_point_enabled">
				@endif
				@php
					$custom_labels = json_decode(session('business.custom_labels'), true);
				@endphp
				<input type="hidden" class="con_id" id="con_id" name="con_id" value="1">
				<input type="hidden" id="item_addition_method" value="{{$business_details->item_addition_method}}">
				{!! Form::open(['url' => action('SellPosController@update',  $transaction->id ), 'method' => 'put', 'id' => 'edit_sell_form', 'files' => true ]) !!}
					{!! Form::hidden('location_id', $transaction->location_id, ['id' => 'location_id', 'data-receipt_printer_type' => !empty($location_printer_type) ? $location_printer_type : 'browser']); !!}
					<div class="row">
							<div class="col-md-12 col-sm-12">
								@component('components.widget', ['class' => 'box-primary','title'=>__('sale.main_section')])
										<div class="row" style="padding:10px; margin:0px 5%" > 
											<div class="col-sm-4" @if(session()->get('user.language', config('app.locale')) != "ar") style="background-color: #f7f7f7;border-radius:10px;padding:10px;box-shadow:0px 0px 10px #00000023;margin-right:5%;" @else style="background-color: #f7f7f7;border-radius:10px;padding:10px;box-shadow:0px 0px 10px #00000023;margin-left:5%;"  @endif>
												@if(!empty($transaction->selling_price_group_id))
													<div class="col-md-4 col-sm-6">
														<div class="form-group">
															<div class="input-group">
																<span class="input-group-addon">
																	<i class="fas fa-money-bill-alt"></i>
																</span>	
																{!! Form::hidden('price_group', $transaction->selling_price_group_id, ['id' => 'price_group']) !!}
																{!! Form::text('price_group_text', $transaction->price_group->name, ['class' => 'form-control', 'readonly']); !!}
																<span class="input-group-addon">
																	@show_tooltip(__('lang_v1.price_group_help_text'))
																</span> 
															</div>
														</div>
													</div>
												@endif

												@if(in_array('types_of_service', $enabled_modules) && !empty($transaction->types_of_service))
													<div class="col-md-4 col-sm-6">
														<div class="form-group">
															<div class="input-group">
																<span class="input-group-addon">
																	<i class="fas fa-external-link-square-alt text-primary service_modal_btn"></i>
																</span>
																{!! Form::text('types_of_service_text', $transaction->types_of_service->name, ['class' => 'form-control', 'readonly']); !!}

																{!! Form::hidden('types_of_service_id', $transaction->types_of_service_id, ['id' => 'types_of_service_id']) !!}

																<span class="input-group-addon">
																	@show_tooltip(__('lang_v1.types_of_service_help'))
																</span> 
															</div>
															<small><p class="help-block @if(empty($transaction->selling_price_group_id)) hide @endif" id="price_group_text">@lang('lang_v1.price_group'): <span>@if(!empty($transaction->selling_price_group_id)){{$transaction->price_group->name}}@endif</span></p></small>
														</div>
													</div>
													<div class="modal fade types_of_service_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
														@if(!empty($transaction->types_of_service))
														@include('types_of_service.pos_form_modal', ['types_of_service' => $transaction->types_of_service])
														@endif
													</div>
												@endif

												{{-- @if(in_array('subscription', $enabled_modules))
													<div class="col-md-4 pull-right col-sm-6">
														<div class="checkbox">
															<label>
															{!! Form::checkbox('is_recurring', 1, $transaction->is_recurring, ['class' => 'input-icheck', 'id' => 'is_recurring']); !!} @lang('lang_v1.subscribe')?
															</label><button type="button" data-toggle="modal" data-target="#recurringInvoiceModal" class="btn btn-link"><i class="fa fa-external-link"></i></button>@show_tooltip(__('lang_v1.recurring_invoice_help'))
														</div>
													</div>
												@endif --}}
												<div class="clearfix"></div>
												<div class="@if(!empty($commission_agent)) col-sm-12 @else col-sm-12 @endif">
													<div class="form-group">
														{!! Form::label('contact_id', __('contact.customer') . ':*') !!}
														<div class="input-group">
															<span class="input-group-addon">
																<i class="fa fa-user"></i>
															</span>
															<input type="hidden" id="default_customer_id" 
															value="{{ $transaction->contact->id }}" >
															<input type="hidden" id="default_customer_name" 
															value="{{ $transaction->contact->name }}" >
															{!! Form::select('contact_id', 
																[], null, ['class' => 'form-control mousetrap', 'id' => 'customer_id', 'placeholder' => 'Enter Customer name / phone', 'required']); !!}
															<span class="input-group-btn">
																<button type="button" class="btn btn-default bg-white btn-flat add_new_customer" data-name=""><i class="fa fa-plus-circle text-primary fa-lg"></i></button>
															</span>
														</div>
													</div>
													<small>
														<strong>
															@lang('lang_v1.billing_address'):
														</strong>
														<div id="billing_address_div">
															{!! $transaction->contact->contact_address ?? '' !!}
														</div>
														<br>
														<strong>
															@lang('lang_v1.shipping_address'):
														</strong>
														<div id="shipping_address_div">
															{!! $transaction->contact->supplier_business_name ?? '' !!}, <br>
															{!! $transaction->contact->name ?? '' !!}, <br>
															{!!$transaction->contact->shipping_address ?? '' !!}
														</div>						
													</small>
												</div>
											</div>
											<div class="col-sm-7" style="background-color: #f7f7f7;border-radius:10px;padding:20px;box-shadow:0px 0px 10px #00000023">
												<div class="col-md-6">
													<div class="form-group">
														<div class="multi-input">
														{!! Form::label('pay_term_number', __('contact.pay_term') . ':') !!} @show_tooltip(__('tooltip.pay_term'))
														<br/>
														{!! Form::number('pay_term_number', $transaction->pay_term_number, ['class' => 'form-control width-40 pull-left', 'placeholder' => __('contact.pay_term')]); !!}
				
														{!! Form::select('pay_term_type', 
															['months' => __('lang_v1.months'), 
																'days' => __('lang_v1.days')], 
																$transaction->pay_term_type, 
															['class' => 'form-control width-60 pull-left','placeholder' => __('messages.please_select')]); !!}
														</div>
													</div>
												</div>
												
												
													@if(!empty($commission_agent))
														<div class="col-sm-6">
															<div class="form-group">
															{!! Form::label('commission_agent', __('lang_v1.commission_agent') . ':') !!}
															{!! Form::select('commission_agent', 
																		$commission_agent, $transaction->commission_agent, ['class' => 'form-control select2']); !!}
															</div>
														</div>
													@endif
												<div class="@if(!empty($commission_agent)) col-sm-6 @else col-sm-6 @endif">
													<div class="form-group">
														{!! Form::label('transaction_date', __('sale.sale_date') . ':*') !!}
														<div class="input-group">
															<span class="input-group-addon">
																<i class="fa fa-calendar"></i>
															</span>
															{!! Form::text('transaction_date', $transaction->transaction_date, ['class' => 'form-control', 'readonly', 'required']); !!}
														</div>
													</div>
												</div>
												{{-- cost center --}}
												<div class="col-md-6">
													{!! Form::label('types_of_service_price_group', __('home.Cost Center') . ':') !!}
													{{  Form::select('cost_center_id',$cost_centers,$transaction->cost_center_id,['class'=>'form-control select2 ','placeholder'=>trans('home.Cost Center')]) }}
												</div>
				
												@php
													if($transaction->status == 'draft' && $transaction->is_quotation == 1){
														$status = 'quotation';
													} else if ($transaction->status == 'draft' && $transaction->sub_status == 'proforma' || $transaction->status == "ApprovedQuotation") {
														$status = 'proforma';
													} else {
														$status = $transaction->status;
													}
												@endphp
				
												<div class="@if(!empty($commission_agent)) col-sm-6 hide @else col-sm-6 hide @endif">
													<div class="form-group">
														{!! Form::hidden('sub_status', $transaction->sub_status, ['id' => 'sub_status']) !!}
														{!! Form::label('status', __('sale.status') . ':*') !!}
														{!! Form::select('status', ['delivered' => __('sale.delivery_s'),'final' => __('sale.final'),
														'proforma' => __('lang_v1.proforma'),'quotation' => __('lang_v1.quotation'), 'draft' => __('sale.draft')], $status, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'required']); !!}
													</div>
												</div>
												
												{{-- <div class="col-sm-4">
													<div class="form-group">
														{!! Form::label('invoice_scheme_id', __('invoice.invoice_scheme') . ':') !!}
														{!! Form::select('invoice_scheme_id', $invoice_schemes, ($default_invoice_schemes)?$default_invoice_schemes->id:NULL, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select')]); !!}
													</div>
												</div> --}}
												@php  $default  =  array_key_first($patterns); @endphp 
												<div class="col-sm-6">
													<div class="form-group">
														{!! Form::label('pattern_id', __('business.patterns') . ':*') !!}
														{!! Form::select('pattern_id', $patterns,  $transaction->pattern_id , ['class' => 'form-control select2','required', 'placeholder' => __('messages.please_select')]); !!}
													</div>
												</div>
										
												@if($transaction->status == 'draft' && $transaction->is_quotation == 1)
												<div class="col-sm-6">
													<div class="form-group">
														{!! Form::label('refe_nomber', __('sale.qutation_no') . ':') !!}
														{!! Form::text('refe_nomber', $transaction->invoice_no, ['class' => 'form-control', 'ReadOnly', 'placeholder' => __('sale.qutation_no')]); !!}
													</div>
												</div>
												@endif
												<div class="col-sm-6">
													<div class="form-group">
														{!! Form::label('project_no', __('sale.project_no') . ':') !!}
														{!! Form::text('project_no', $transaction->project_no, ['class' => 'form-control',   'placeholder' => __('sale.project_no')]); !!}
													</div>
												</div>
												@if($transaction->status == 'draft' && $transaction->is_quotation == 0)
												<div class="col-sm-6">
													<div class="form-group">
														{!! Form::label('refe_nomber', __('sale.Draft_no') . ':') !!}
														{!! Form::text('refe_nomber', $transaction->invoice_no, ['class' => 'form-control', 'ReadOnly', 'placeholder' => __('sale.Draft_no')]); !!}
													</div>
												</div>
												@endif
												
														
												@if($transaction->status == 'ApprovedQuotation')
												<div class="col-sm-6">
													<div class="form-group">
														{!! Form::label('refe_nomber', __('sale.ApprovedQutation_no') . ':') !!}
														{!! Form::text('refe_nomber', $transaction->invoice_no, ['class' => 'form-control', 'ReadOnly', 'placeholder' => __('sale.ApprovedQutation_no')]); !!}
													</div>
												</div>
												@endif
												
				
				
												
												
											
												@php
													$store = "";
													foreach ($stores as  $key => $value) {
														$store = $key;
														break;
													} 
												@endphp
												<div class="col-sm-6">
													<div class="form-group">
														{!! Form::label('store_id', __('warehouse.warehouse').':*') !!}
														{!! Form::select('store_id', $stores,$transaction->store, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'required'], $bl_attributes); !!}
													</div>
												</div>
												<div class="col-md-6"> 
													<div class="form-group">
													{!! Form::label('types_of_service_price_group', __('home.Agent') . ':') !!}
													{{  Form::select('agent_id',$users,$transaction->agent_id,['class'=>'form-control select2 ',"id"=>"agent_id", 'placeholder'=>trans('home.Choose Agent')]) }}
													</div>
												</div>
												<div class="col-md-6">
													<div class="form-group">
														<div class="multi-input">
														{!! Form::label('currency_id', __('business.currency') . ':') !!}  
														<br/>
														{!! Form::select('currency_id', $currencies,  $transaction->currency_id, ['class' => 'form-control width-60 currency_id  select2', 'placeholder' => __('messages.please_select') ]); !!}
														{!! Form::number('currency_id_amount',$transaction->exchange_price, ['class' => 'form-control width-40 pull-right currency_id_amount'   ]); !!}
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
				
													<div class="col-md-6">
														<div class="form-group">
															{!! Form::label('custom_field_1', $label_1 ) !!}
															{!! Form::text('custom_field_1', $transaction->custom_field_1, ['class' => 'form-control','placeholder' => $custom_field_1_label, 'required' => $is_custom_field_1_required]); !!}
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
				
													<div class="col-md-6">
														<div class="form-group">
															{!! Form::label('custom_field_2', $label_2 ) !!}
															{!! Form::text('custom_field_2', $transaction->custom_field_2, ['class' => 'form-control','placeholder' => $custom_field_2_label, 'required' => $is_custom_field_2_required]); !!}
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
				
													<div class="col-md-6">
														<div class="form-group">
															{!! Form::label('custom_field_3', $label_3 ) !!}
															{!! Form::text('custom_field_3', $transaction->custom_field_3, ['class' => 'form-control','placeholder' => $custom_field_3_label, 'required' => $is_custom_field_3_required]); !!}
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
				
													<div class="col-md-6">
														<div class="form-group">
															{!! Form::label('custom_field_4', $label_4 ) !!}
															{!! Form::text('custom_field_4', $transaction->custom_field_4, ['class' => 'form-control','placeholder' => $custom_field_4_label, 'required' => $is_custom_field_4_required]); !!}
														</div>
													</div>
												@endif
												@can('edit_invoice_number')
												@if($transaction->status == 'ApprovedQuotation')
												@else 
												<div class="col-sm-6">
													<div class="form-group">
														{{-- {!! Form::label('invoice_no', __('sale.invoice_no') . ':') !!} --}}
														{!! Form::hidden('invoice_no', "", ['class' => 'form-control', 'placeholder' => __('sale.invoice_no')]); !!}
													</div>
												</div>
												@endif
												@endcan
												<div class="clearfix"></div>
											</div>
										</div>
									</div>
									</div>

								
								@endcomponent
								
								@component('components.widget', ['class' => 'box-primary','title'=>__('sale.search_section')])
									<div class="col-sm-10 col-sm-offset-1">
										<div class="form-group">
											<div class="input-group">
												<div class="input-group-btn">
													<button type="button" class="btn btn-default bg-white btn-flat" data-toggle="modal" data-target="#configure_search_modal" title="{{__('lang_v1.configure_product_search')}}"><i class="fa fa-barcode"></i></button>
												</div>
												{!! Form::text('search_product', null, ['class' => 'form-control mousetrap', 'id' => 'search_product', 'placeholder' => __('lang_v1.search_product_placeholder'),
												'autofocus' => true,
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
										<input type="hidden" id="product_row_count" 
											value="{{count($sell_details)}}">
										@php
											$hide_tax = '';
											if( session()->get('business.enable_inline_tax') == 0){
												$hide_tax = 'hide';
											}
										@endphp
										<div class="table-responsive">
										<table class="table table-condensed table-bordered table-striped table-responsive dataTable " id="pos_table">
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
													<th @can('edit_product_price_from_sale_screen')) hide @endcan>
														@lang('sale.unit_price_exc'). {{$currency_details->symbol}}
													</th>
													<th @can('edit_product_price_from_sale_screen')) hide @endcan>
														@lang('sale.unit_price_inc'). {{$currency_details->symbol}}
													</th>
													<th @if($transaction->amount_in_currency > 0 ) class="text-center curr_column  cur_check " @else class="text-center curr_column  cur_check hide" @endif @if($transaction->amount_in_currency >0) hide @endif>
													@lang('sale.unit_price_exc_new_currency')
												</th>
													<th @can('edit_product_discount_from_sale_screen') hide @endcan>
														@lang('receipt.discount')
													</th>

													<th class="text-center {{$hide_tax}}">
														@lang('sale.tax')
													</th>
													{{--
													<th class="text-center" @can('edit_product_price_from_sale_screen')) hide @endcan>
														@lang('sale.unit_price_inc_new_currency')
													</th> --}}

													<th class="text-center">
														@lang('home.percentage_discount')
													</th>
													
													<th class="text-center {{$hide_tax}}">
														@lang('sale.price_inc_tax'). {{$currency_details->symbol}}
													</th>
													<th class="text-center">
														@lang('sale.cost_exc'). {{$currency_details->symbol}}
													</th>
													<th class="text-center  ">
														@lang('sale.cost_inc'). {{$currency_details->symbol}}
													</th>
													<th @if($transaction->amount_in_currency > 0 ) class="text-center curr_column  cur_check " @else class="text-center curr_column  cur_check hide" @endif>
														@lang('sale.cost_exc_new_currency')
													</th>
													{{-- <th class="text-center  ">
														@lang('sale.cost_inc_new_currency')
													</th> --}}
													@if(!empty($common_settings['enable_product_warranty']) && !empty($is_direct_sell))
														<th>@lang('lang_v1.warranty')</th>
													@endif
													<th class="text-center">
														@lang('sale.subtotal'). {{$currency_details->symbol}}
													</th>
													<th class="text-center"><i class="fa fa-close" aria-hidden="true"></i></th>
												</tr>
											</thead>
											<tbody>
											
												@foreach($sell_details as $sell_line)
													@php
													    #2024-8-6
														$buss                   =  \App\Business::find($sell_line->transaction->business_id);
														$pro                    =  \App\Product::find($sell_line->product->id);
														$allUnits               =  [];
														$var                    =  $pro->variations->first();
														$var                    =  ($var)?$var->default_sell_price:0;
														$UU                     = \App\Unit::find($sell_line->product->unit_id);
														$allUnits[$UU->id] = [
																					'name'          => $UU->actual_name,
																					'multiplier'    => $UU->base_unit_multiplier,
																					'allow_decimal' => $UU->allow_decimal,
																					'price'         => $var,
																					'check_price'   => $buss->default_price_unit,
																			];
														if($pro->sub_unit_ids != null){
															foreach($pro->sub_unit_ids  as $i){
																	$row_price    =  0;
																	$un           = \App\Unit::find($i);
																	$row_price    = \App\Models\ProductPrice::where("unit_id",$i)->where("product_id",$sell_line->product->id)->where("number_of_default",0)->first();
																	$row_price    = ($row_price)?$row_price->default_sell_price:0;
																	$allUnits[$i] = [
																		'name'          => $un->actual_name,
																		'multiplier'    => $un->base_unit_multiplier,
																		'allow_decimal' => $un->allow_decimal,
																		'price'         => $row_price,
																		'check_price'   => $buss->default_price_unit,
																	] ;
																}
														}
														$sub_units              = $allUnits  ;
 														$list_of_prices_in_unit = \App\Product::getProductPrices($sell_line->product_id);
													@endphp
													@include('sale_pos.view_types.sell_add', [	
																								'product'                => $sell_line, 
																								'row_count'              => $loop->index, 
																								'tax_dropdown'           => $taxes, 
																								'sub_units'              => $sub_units, 
																								'action'                 => 'edit', 
																								'is_direct_sell'         => true ,
																								"currency"               => ($transaction->exchange_price>1)?$transaction->exchange_price:null,
																								'list_of_prices'         => $list_of_prices,
																								'list_of_prices_in_unit' => $list_of_prices_in_unit 
																							])
												@endforeach
											</tbody>
										</table>
										</div>
										<div class="table-responsive">
										<table class="table table-condensed table-bordered table-striped table-responsive">
											<tr>
												<td>
													<div class="pull-right">
														<b>@lang('sale.item'):</b> 
														<span class="total_quantity">0</span>
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
													<b @if($transaction->amount_in_currency >0) class="text-right cur_symbol "  @else class="text-right cur_symbol hide"@endif  >@lang('sale.total') @if($transaction->amount_in_currency >0)  {{ ($transaction->currency)?$transaction->currency->symbol:"" }}  @endif : </b>
													<span @if($transaction->amount_in_currency >0)  class="price_total_curr "  @else class="price_total_curr hide" @endif >0</span>
													</div>
												</td>
											</tr>
										</table>
										</div>
									</div>
								@endcomponent

								@component('components.widget', ['class' => 'box-primary','title'=>__('sale.footer_section')])
									<div class="col-md-3">
										<div class="form-group">
											{!! Form::label('discount_type', __('sale.discount_type') . ':*' ) !!}
											<div class="input-group">
												<span class="input-group-addon">
													<i class="fa fa-info"></i>
												</span>
												{!! Form::select('discount_type', [ '' => __('lang_v1.none'), 'fixed_before_vat' => __( 'home.fixed before vat' ), 'fixed_after_vat' => __( 'home.fixed after vat' ), 'percentage' => __( 'lang_v1.percentage' )], $transaction->discount_type,
												['class' => 'form-control select2']); !!}
											</div>
										</div>
									</div>
									@php
										$max_discount = !is_null(auth()->user()->max_sales_discount_percent) ? auth()->user()->max_sales_discount_percent : '';
									@endphp
									<div class="col-md-3">
										<div class="form-group">
											{!! Form::label('discount_amount', __('sale.discount_amount') . ':*' ) !!} . {{$currency_details->symbol}}
											<div class="input-group">
												<span class="input-group-addon">
													<i class="fa fa-info"></i>
												</span>
												{!! Form::text('discount_amount', $transaction->discount_amount, ['class' => 'form-control input_number', 'data-default' => $business_details->default_sales_discount, 'data-max-discount' => $max_discount, 'data-max-discount-error_msg' => __('lang_v1.max_discount_error_msg', ['discount' => $max_discount != '' ? @num_format($max_discount) : '']) ]); !!}
											</div>
										</div>
									</div>
									<div class="col-md-3 pull-left" ><br>
										<b @if($transaction->amount_in_currency >0)  class="i_curr"  @else class="i_curr hide" @endif >@lang( 'sale.discount_amount' ) @if($transaction->amount_in_currency >0)  {{ ($transaction->currency)?$transaction->currency->symbol:"" }}  @endif : (-)</b> 
										<span @if($transaction->amount_in_currency >0)  class="display_currency" @else class="display_currency hide" @endif  id="total_discount_curr">0</span>
									</div>
									<div class="col-md-3 pull-right"><br>
										<b> @lang( 'sale.discount_amount' ). {{$currency_details->symbol}}  : (-)</b> 
										<span class="display_currency" id="total_discount">0</span>
									</div>
									
									<div class="clearfix"></div>
									<div class="col-md-12 well well-sm bg-light-gray @if(session('business.enable_rp') != 1) hide @endif">
										<input type="hidden" name="rp_redeemed" id="rp_redeemed" value="{{$transaction->rp_redeemed}}">
										<input type="hidden" name="rp_redeemed_amount" id="rp_redeemed_amount" value="{{$transaction->rp_redeemed_amount}}">
										<div class="col-md-12"><h4>{{session('business.rp_name')}}</h4></div>
										<div class="col-md-4">
											<div class="form-group">
												{!! Form::label('rp_redeemed_modal', __('lang_v1.redeemed') . ':' ) !!}
												<div class="input-group">
													<span class="input-group-addon">
														<i class="fa fa-gift"></i>
													</span>
													{!! Form::number('rp_redeemed_modal', $transaction->rp_redeemed, ['class' => 'form-control direct_sell_rp_input', 'data-amount_per_unit_point' => session('business.redeem_amount_per_unit_rp'), 'min' => 0, 'data-max_points' => !empty($redeem_details['points']) ? $redeem_details['points'] : 0, 'data-min_order_total' => session('business.min_order_total_for_redeem') ]); !!}
													<input type="hidden" id="rp_name" value="{{session('business.rp_name')}}">
												</div>
											</div>
										</div>
										<div class="col-md-4">
											<p><strong>@lang('lang_v1.available'):</strong> <span id="available_rp">{{$redeem_details['points'] ?? 0}}</span></p>
										</div>
										<div class="col-md-4">
											<p><strong>@lang('lang_v1.redeemed_amount'):</strong> (-)<span id="rp_redeemed_amount_text">{{@num_format($transaction->rp_redeemed_amount)}}</span></p>
										</div>
									</div>
									<div class="clearfix"></div>
									@php
										$id =  $taxes["tax_rates"]->toArray() ;
										$keys =  array_keys($id) ;
									@endphp
									<div class="col-md-3">
										<div class="form-group">
											{!! Form::label('tax_rate_id', __('sale.order_tax') . ': ' ) !!}. {{$currency_details->symbol}}
											<div class="input-group">
												<span class="input-group-addon">
													<i class="fa fa-info"></i>
												</span>
												{!! Form::select('tax_rate_id', $taxes['tax_rates'], $transaction->tax_id, ['placeholder' => __('messages.please_select'), 'class' => 'form-control', 'data-default'=> $business_details->default_sales_tax], $taxes['attributes']); !!}

												<input type="hidden" name="tax_calculation_amount" id="tax_calculation_amount" 
												value="{{@num_format(optional($transaction->tax)->amount)}}" data-default="{{$business_details->tax_calculation_amount}}">
											</div>
										</div>
									</div>
									<div class="col-md-3 col-md-offset-3 pull-left" ><br>
										<b @if($transaction->amount_in_currency >0)  class="t_curr "  @else  class="t_curr hide"  @endif  >@lang( 'sale.order_tax' ) @if($transaction->amount_in_currency >0) {{ ($transaction->currency)?$transaction->currency->symbol:"" }} @endif : (+)</b> 
									<span  @if($transaction->amount_in_currency >0)  class="display_currency " @else  class="display_currency hide" @endif   id="order_tax_curr">0</span>
									</div>				
									<div class="col-md-3 col-md-offset-3 pull-right">
										<b>@lang( 'sale.order_tax' ). {{$currency_details->symbol}}: (+)</b> 
										<span class="display_currency" id="order_tax">{{ $transaction->tax_amount }}</span>
									</div>	
									
										{{-- <div class="col-md-12">
											<div class="form-group">
												{!! Form::label('sell_note',__('sale.sell_note') . ':') !!}
												{!! Form::textarea('sale_note', $transaction->additional_notes, ['class' => 'form-control', 'rows' => 3]); !!}
											</div>
										</div> --}}
										<div class="col-md-12">
											<div class="form-group">
												{!! Form::label('sell_note',__('sale.sell_note')) !!}
												{!! Form::select('sale_note',  $terms ,$transaction->additional_notes , ['class' => 'form-control select2','placeholder' => __('messages.please_select'),  "id"=>"sale_note"]); !!}
											</div>
										</div>
									<input type="hidden" name="is_direct_sale" value="1">
								@endcomponent

								@component('components.widget', ['class' => 'box-primary hide','title'=>__('sale.payment_section')])
									<div class="col-md-4">
										<div class="form-group">
											{!! Form::label('shipping_details', __('sale.shipping_details')) !!}
											{!! Form::textarea('shipping_details',$transaction->shipping_details, ['class' => 'form-control','placeholder' => __('sale.shipping_details') ,'rows' => '3', 'cols'=>'30']); !!}
										</div>
									</div>
									<div class="col-md-4">
										<div class="form-group">
											{!! Form::label('shipping_address', __('lang_v1.shipping_address')) !!}
											{!! Form::textarea('shipping_address', $transaction->shipping_address, ['class' => 'form-control','placeholder' => __('lang_v1.shipping_address') ,'rows' => '3', 'cols'=>'30']); !!}
										</div>
									</div>
									<div class="col-md-4">
										<div class="form-group">
											{!!Form::label('shipping_charges', __('sale.shipping_charges'))!!}
											<div class="input-group">
											<span class="input-group-addon">
											<i class="fa fa-info"></i>
											</span>
											{!!Form::text('shipping_charges',@num_format($transaction->shipping_charges),['class'=>'form-control input_number','placeholder'=> __('sale.shipping_charges')]);!!}
											</div>
										</div>
									</div>
									<div class="clearfix"></div>
									<div class="col-md-4">
										<div class="form-group">
											{!! Form::label('shipping_status', __('lang_v1.shipping_status')) !!}
											{!! Form::select('shipping_status',$shipping_statuses, $transaction->shipping_status, ['class' => 'form-control','placeholder' => __('messages.please_select')]); !!}
										</div>
									</div>
									<div class="col-md-4">
										<div class="form-group">
											{!! Form::label('delivered_to', __('lang_v1.delivered_to') . ':' ) !!}
											{!! Form::text('delivered_to', $transaction->delivered_to, ['class' => 'form-control','placeholder' => __('lang_v1.delivered_to')]); !!}
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
												{!! Form::text('shipping_custom_field_1', !empty($transaction->shipping_custom_field_1) ? $transaction->shipping_custom_field_1 : null, ['class' => 'form-control','placeholder' => $shipping_custom_label_1, 'required' => $is_shipping_custom_field_1_required]); !!}
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
												{!! Form::text('shipping_custom_field_2', !empty($transaction->shipping_custom_field_2) ? $transaction->shipping_custom_field_2 : null, ['class' => 'form-control','placeholder' => $shipping_custom_label_2, 'required' => $is_shipping_custom_field_2_required]); !!}
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
												{!! Form::text('shipping_custom_field_3', !empty($transaction->shipping_custom_field_3) ? $transaction->shipping_custom_field_3 : null, ['class' => 'form-control','placeholder' => $shipping_custom_label_3, 'required' => $is_shipping_custom_field_3_required]); !!}
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
												{!! Form::text('shipping_custom_field_4', !empty($transaction->shipping_custom_field_4) ? $transaction->shipping_custom_field_4 : null, ['class' => 'form-control','placeholder' => $shipping_custom_label_4, 'required' => $is_shipping_custom_field_4_required]); !!}
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
												{!! Form::text('shipping_custom_field_5', !empty($transaction->shipping_custom_field_5) ? $transaction->shipping_custom_field_5 : null, ['class' => 'form-control','placeholder' => $shipping_custom_label_5, 'required' => $is_shipping_custom_field_5_required]); !!}
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
											@php
												$medias = $transaction->media->where('model_media_type', 'shipping_document')->all();
											@endphp
											@include('sell.partials.media_table', ['medias' => $medias, 'delete' => true])
										</div>
									</div>
									<div class="clearfix"></div>
								@endcomponent
								<div class="col-md-4 col-md-offset-8">
									@if(!empty($pos_settings['amount_rounding_method']) && $pos_settings['amount_rounding_method'] > 0)
									<small id="round_off"><br>(@lang('lang_v1.round_off'): <span id="round_off_text">0</span>)</small>
									<br/>
									<input type="hidden" name="round_off_amount" 
										id="round_off_amount" value=0>
									@endif
								</div>
								<div class="row">
									<div class="col-md-12 sub">
										{!! Form::hidden('is_save_and_print', 0, ['id' => 'is_save_and_print']); !!}
										{{-- <button type="button" class="btn btn-primary" id="submit-sell">@lang('messages.update')</button> --}}
										<a data-pattern="3434"  data-href='{{action('SellController@check_msg')}}'  id='submit-sell' data-container='.view_modal' class='update_transfer btn btn-modal btn-primary pull-right btn-flat'>@lang('messages.update')</a>
										{{-- <button type="button" id="save-and-print" class="btn btn-primary btn-flat">@lang('lang_v1.update_and_print')</button> --}}
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-12 col-md-offset-8 text-center text-right" >
									<div><b>@lang('sale.total_payable') . {{$currency_details->symbol}}: </b>
										<input type="hidden" name="final_total" id="final_total_input">
										<span id="total_payable">0</span>
									</div><br>
									{{-- @php dd($transaction); @endphp --}}
									<span @if( $transaction->amount_in_currency > 0  )  class="o_curr "  @else  class="o_curr hide"     @endif  style="font-size:large"><b>@lang('sale.total_payable') @if( $transaction->amount_in_currency >0   ) {{ ($transaction->currency)?$transaction->currency->symbol:"" }}      @endif : </b>
									</span>
									<input type="hidden" name="final_total_curr" id="final_total_input_curr">
									<span id="total_payable_curr" @if( $transaction->amount_in_currency >0 )  class="  "  @else  class="hide"    @endif  >0</span>
								</div>
							</div>
						</div>
						
					</div>
					@if(in_array('subscription', $enabled_modules))
						@include('sale_pos.partials.recurring_invoice_modal')
					@endif
				{!! Form::close() !!}
			</section>
		<!-- /.content -->
	{{-- ******************************************** --}}

	{{-- *2/3* MODAL SECTION   --}}
	{{-- ******************************************** --}}
		<div class="modal fade contact_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
			@include('contact.create', ['quick_add' => true])
		</div>
		<div class="modal fade register_details_modal" tabindex="-1" role="dialog" 
			aria-labelledby="gridSystemModalLabel">
		</div>
		<div class="modal fade close_register_modal" tabindex="-1" role="dialog" 
			aria-labelledby="gridSystemModalLabel">
		</div>
	{{-- ******************************************** --}}
	
	{{-- *2/4* QUICK SECTION   --}}
	{{-- ******************************************** --}}
		<!-- quick product modal -->
		<div class="modal fade quick_add_product_modal" tabindex="-1" role="dialog" aria-labelledby="modalTitle"></div>
	{{-- ******************************************** --}}

	@include('sale_pos.partials.configure_search_modal')
	<input type="hidden" name="view_type" id="view_type" value="sell_edit">
@stop
{{-- *3* --}}
@section('javascript')
	{{-- relation section   --}}
	<script src="{{ asset('js/posss.js?v=' . $asset_v) }}"></script>
	<script src="{{ asset('js/producte.js?v=' . $asset_v) }}"></script>
	<script src="{{ asset('js/opening_stock.js?v=' . $asset_v) }}"></script>
	<script src="{{ asset('js/sale_row_discount.js?v=' . $asset_v) }}"></script>
	{{-- additional section  --}}
    <!-- Call restaurant module if defined -->
    @if(in_array('tables' ,$enabled_modules) || in_array('modifiers' ,$enabled_modules) || in_array('service_staff' ,$enabled_modules))
    	<script src="{{ asset('js/restaurant.js?v=' . $asset_v) }}"></script>
    @endif

	<script type="text/javascript">
        updatePattern();
		$("#pattern_id").each( function(e){  
			$(this).on("change",function(){
				updatePattern();
			})
		});
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
					e.attr("data-row_index",count);
					inner.html(count);
					el.attr("value",count++);
					// el.val(count++);					
				});
			}
		});
		change_currency();
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
					$(".check_dep_curr").addClass("hide" );
        			$('input[name="dis_currency"]').prop('checked', false);
            		$('#depending_curr').prop('checked', false);;
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
				    // $(".check_dep_curr").removeClass("hide" );
            		$('input[name="dis_currency"]').prop('checked', true);
            		$('#depending_curr').prop('checked', true);
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
							// $(".curr_column").removeClass("hide");
							update_os();
							
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
			$('#shipping_documents').fileinput({
				showUpload: false,
				showPreview: false,
				browseLabel: LANG.file_browse_label,
				removeLabel: LANG.remove,
			});
			update_os();
			os_discount();
			change_discount();
		});
		function updatePattern(){
			pattern = $("#pattern_id").val();
			row     = $(".pos_unit_price").val();
			type    = $("#type_of_sell").val();
			button  = $(".sub").html("");
			button  = $(".sub").html("<a  data-href='{{action('SellController@check_msg')}}'  id='submit-sell' data-container='.view_modal' class='update_transfer btn btn-modal btn-primary pull-right btn-flat'>@lang('messages.update')</a>");
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
	</script>
	@yield('inner_script')
@endsection
