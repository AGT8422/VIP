@extends('layouts.app')
@section('title', __('lang_v1.edit_sell_return'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
<br>
    <h1>@lang('lang_v1.edit_sell_return')</h1>
</section>

<!-- Main content -->
<section class="content no-print">
	{!! Form::open(['url' => action('SellReturnController@update_return'), 'method' => 'post', 'id' => 'purchase_return_form', 'files' => true ]) !!}
	@component("components.widget",["class"=>"box-primary"])
	{!! Form::hidden('transaction_id', $transaction_return->id, ['id' => 'transaction_id']) !!}

	<div class="box box-solid">

		<div class="box-body">
			<div class="row">
				<div class="col-sm-4"  style="background-color:#f7f7f7;margin:auto 2%;border-radius:10px;padding:15px;box-shadow:1px 1px 10px grey">
					<div class="col-sm-12">
						<div class="form-group">
							{!! Form::label('contact_id', __('contact.customer') . ':*') !!}
							<div class="input-group">
								<span class="input-group-addon">
									<i class="fa fa-user"></i>
								</span>
								<input type="hidden" id="default_customer_id" 
								value="{{ $transaction_return->contact->id }}" >
								<input type="hidden" id="default_customer_name" 
								value="{{ $transaction_return->contact->name }}" >
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
								{!! $transaction_return->contact->contact_address ?? '' !!}
							</div>
							<br>
							<strong>
								@lang('lang_v1.shipping_address'):
							</strong>
							<div id="shipping_address_div">
								{!! $transaction_return->contact->supplier_business_name ?? '' !!}, <br>
								{!! $transaction_return->contact->name ?? '' !!}, <br>
								{!!$transaction_return->contact->shipping_address ?? '' !!}
							</div>						
						</small>
					</div>
				</div>
				<div class="col-md-7"  style="background-color:#f7f7f7;margin:auto 2%;border-radius:10px;padding:15px;box-shadow:1px 1px 10px grey">
					@php 
						$default_location =  array_key_first($business_locations->toArray());
						$taxx             =  array_key_first($business_locations->toArray());
					@endphp
					<div class="col-sm-6 hide">
						<div class="form-group">
							{!! Form::label('location_id', __('purchase.business_location').':*') !!}
							{!! Form::select('location_id', $business_locations, $default_location, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'required']); !!}
						</div>
					</div>
					<div class="col-sm-6 hide">
						<div class="form-group">
							{!! Form::label('ref_no', __('purchase.ref_no').':') !!}
							{!! Form::text('ref_no', $transaction_return->invoice_no, ['class' => 'form-control']); !!}
						</div>
					</div>
					<div class="clearfix"></div>
					<div class="col-sm-6">
						<div class="form-group">
							{!! Form::label('sup_ref_no', __('purchase.sup_refe').':') !!}
							{!! Form::text('sup_ref_no', $transaction_return->sup_refe, ['class' => 'form-control']); !!}
						</div>
					</div>
					<div class="col-sm-6">
						<div class="form-group">
							{!! Form::label('store_id', __('warehouse.warehouse').':*') !!}
							{!! Form::select('store_id', $mainstore_categories, $transaction_return->store, ['class' => 'form-control select2', 'required', 'placeholder' => __('messages.please_select') ] ); !!}
						</div>
					</div>
					<div class="clearfix"></div>
					<div class="col-sm-6">
						<div class="form-group">
							{!! Form::label('transaction_date', __('messages.date') . ':*') !!}
							<div class="input-group">
								<span class="input-group-addon">
									<i class="fa fa-calendar"></i>
								</span>
								{!! Form::text('transaction_date', @format_datetime($transaction_return->transaction_date), ['class' => 'form-control', 'readonly', 'required']); !!}
							</div>
						</div>
					</div>
					<div class="col-sm-6">
						@if($transaction_return->status == "delivered" || $RecievedPrevious > 0)
								<div class="form-group hide">
									{!! Form::label('status', __('sale.status') . ':*') !!}
									{{--{!! Form::select('status', ['final' => __('sale.final'), 'draft' => __('sale.draft'), 'quotation' => __('lang_v1.quotation'), 'proforma' => __('lang_v1.proforma')], null, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'required']); !!}--}}
									{{-- {!! Form::select('status', [  'delivered' => __('sale.delivery_s'),'final' => __('sale.final'),'proforma' => __('lang_v1.proforma'),'quotation' => __('lang_v1.quotation'), 'draft' => __('sale.draft')], "final", ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'),  'required']); !!} --}}
									{!! Form::select('status', [  'delivered' => __('sale.delivery_s'),'final' => __('sale.final')], $transaction_return->status, ['class' => 'form-control select2 ', 'placeholder' => __('messages.please_select'),  'required']); !!}
									{!! Form::text('status',   $transaction_return->status, ['class' => 'form-control select2',"readOnly", 'placeholder' => __('messages.please_select'),  'required']); !!}
								</div>
								<div class="form-group">
									{!! Form::label('status', __('sale.status') . ':*') !!}
									{{--{!! Form::select('status', ['final' => __('sale.final'), 'draft' => __('sale.draft'), 'quotation' => __('lang_v1.quotation'), 'proforma' => __('lang_v1.proforma')], null, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'required']); !!}--}}
									{{-- {!! Form::select('status', [  'delivered' => __('sale.delivery_s'),'final' => __('sale.final'),'proforma' => __('lang_v1.proforma'),'quotation' => __('lang_v1.quotation'), 'draft' => __('sale.draft')], "final", ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'),  'required']); !!} --}}
									{!! Form::text('status',   $statuss, ['class' => 'form-control ',"readOnly", 'placeholder' => __('messages.please_select'),  'required']); !!}
								</div>
						@else
							<div class="form-group ">
								{!! Form::label('status', __('sale.status') . ':*') !!}
								{{--{!! Form::select('status', ['final' => __('sale.final'), 'draft' => __('sale.draft'), 'quotation' => __('lang_v1.quotation'), 'proforma' => __('lang_v1.proforma')], null, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'required']); !!}--}}
								{{-- {!! Form::select('status', [  'delivered' => __('sale.delivery_s'),'final' => __('sale.final'),'proforma' => __('lang_v1.proforma'),'quotation' => __('lang_v1.quotation'), 'draft' => __('sale.draft')], "final", ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'),  'required']); !!} --}}
								{!! Form::select('status', [  'delivered' => __('sale.delivery_s'),'final' => __('sale.final')], $transaction_return->status, ['class' => 'form-control select2 ', 'placeholder' => __('messages.please_select'),  'required']); !!}
							</div>
						@endif
					</div>
					<div class="clearfix"></div>
					<div class="col-sm-6">
						{{-- patterns --}}
						<div class="form-group">
							{!! Form::label('pattern_id', __('business.patterns') . ':') !!}
							{!! Form::select('pattern_id', $patterns, $transaction_return->pattern_id , ['class' => 'form-control select2', 'placeholder' => __('messages.please_select')]); !!}
						</div>
					</div>
					{{-- *2/3/5* List Price--}}
					{{-- #2024-8-6 --}}
					<div class="col-sm-6">
						<div class="form-group">
							{!! Form::label('list_price', __('List  Of Prices').':') !!}
							{!! Form::select('list_price',$list_of_prices,null, ['class' => 'form-control select2' , 'id' => 'list_price' ]); !!}
						</div>
					</div>
					<div class="clearfix"></div>
					<div class="col-sm-6">
						<div class="form-group">
							{!! Form::label('cost_center_id', __('home.Cost Center').':*') !!}
							{{-- @show_tooltip(__('tooltip.purchase_location')) --}}
							{!! Form::select('cost_center_id', $cost_centers, $transaction_return->cost_center_id, ['class' => 'form-control select2','id'=>'cost_center_id', 'placeholder' => __('messages.please_select')] ); !!}
						</div>
					</div>
					{{-- agent --}}
					<div class="col-sm-6">
						{!! Form::label('types_of_service_price_group', __('home.Agent') . ':') !!}
						{{  Form::select('agent_id',$users,$transaction_return->agent_id,['class'=>'form-control select2 ' , "id"=>"agent_id",'placeholder'=>trans('home.Choose Agent')]) }}
					</div>
					<div class="clearfix"></div>
					{{-- project no --}}
					<div class="col-sm-6">
						<div class="form-group">
							{!! Form::label('project_no', __('sale.project_no') . ':') !!}
							{!! Form::text('project_no', $transaction_return->project_no, ['class' => 'form-control', 'placeholder' => __('sale.project_no')]); !!}
							<p class="help-block">@lang('lang_v1.keep_blank_to_autogenerate')</p>
						</div>
					</div>
					
					
					<div class="col-md-6">
						<div class="form-group">
							<div class="multi-input">
							{!! Form::label('currency_id', __('business.currency') . ':') !!}  
							<br/>
							{!! Form::select('currency_id', $currencies, $transaction_return->currency_id, ['class' => 'form-control width-60 currency_id  select2', 'placeholder' => __('messages.please_select') ]); !!}
							{!! Form::text('currency_id_amount', $transaction_return->exchange_price, ['class' => 'form-control width-40 pull-right currency_id_amount'   ]); !!}
							</div>
							<br/>
							<div class="check_dep_curr hide">
								{!! Form::checkbox('depending_curr',null, 0, ['class' => 'depending_curr' ,'id'=>'depending_curr'   ]); !!}
								{!! Form::label('depending_curr', __('Depending On Currency Column') . '') !!}  
							</div>
							<br/> 
							<div class="check_dep_curr  hide"><input  type="checkbox" name="dis_currency" value="1"> <b>Discount</b> @show_tooltip(__('tooltip.dis_currency'))<br ></div>
						</div>
					</div>
					<div class="clearfix"></div>
					
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
				</div>
			</div>
		</div>
	</div>
	
	<!--box end-->
	<div class="box box-solid">
		<div class="box-header">
        	<h3 class="box-title">{{ __('stock_adjustment.search_products') }}</h3>
       	</div>
		<div class="box-body">
			<div class="row">
				<div class="col-sm-8 col-sm-offset-2">
					<div class="form-group">
						<div class="input-group">
							<span class="input-group-addon">
								<i class="fa fa-search"></i>
							</span>
							{!! Form::text('search_product', null, ['class' => 'form-control', 'id' => 'search_product_for_purchase_return', 'placeholder' => __('stock_adjustment.search_products')]); !!}
						</div>
					</div>
				</div>
			</div>
		 
		 

			<div class="row">
				<div class="col-sm-12">
					<input type="hidden" id="total_amount" name="final_total" value="0">
					<div class="table-responsive">
						<table class="table table-bordered table-striped table-condensed" 
								id="purchase_return_product_table">
								@php
									$hide_tax = '';
									if( session()->get('business.enable_inline_tax') == 0){
										$hide_tax = 'hide';
									}
								@endphp
							<thead>
								<tr>
									<th class="text-center">	
										@lang('sale.product')
									</th>
									{{-- @if(session('business.enable_lot_number'))
										<th>
											@lang('lang_v1.lot_number')
										</th>
									@endif
									@if(session('business.enable_product_expiry'))
										<th>
											@lang('product.exp_date')
										</th>
									@endif --}}

									<th class="text-center">
										@lang('sale.qty')
									</th>

									<th @can('edit_product_price_from_sale_screen')) hide @endcan>
										@lang('sale.unit_price_exc')
									</th>
									<th @can('edit_product_price_from_sale_screen')) hide @endcan>
										@lang('sale.unit_price_inc')
									</th>
									<th @if($transaction_return->exchange_price > 0)  class="curr_column br_dis  cur_check  " @else class="curr_column br_dis  cur_check hide"  @endif >@lang( 'lang_v1.Cost before without Tax' ) @if($transaction_return->exchange_price > 0)  {{ $transaction_return->currency->symbol }} @endif</th>

									<th @can('edit_product_discount_from_sale_screen') hide @endcan>
										@lang('receipt.discount')
									</th>
	
									<th class="text-center {{$hide_tax}}">
										@lang('sale.tax')
									</th>
									{{--  discount %  --}}
									<th class="text-center">
										@lang('home.percentage_discount')
									</th>
									<th class="text-center {{$hide_tax}}">
										@lang('sale.price_inc_tax')
									</th>
									<th class="text-center  ">
										@lang('sale.cost_exc')
									</th>
									<th class="text-center  ">
										@lang('sale.cost_inc')
									</th>
									<th @if($transaction_return->exchange_price > 0)   class="curr_column ar_dis  cur_check  " @else  class="curr_column ar_dis  cur_check hide" @endif >@lang( 'home.Cost without Tax currency' ) @if($transaction_return->exchange_price > 0)  {{ $transaction_return->currency->symbol }} @endif</th>

									<th class="text-center">
										{{-- @lang('sale.subtotal') --}}
										@lang('sale.total')
									</th>
									<th class="text-center"><i class="fa fa-trash" aria-hidden="true"></i></th>
								</tr>
							</thead>
							<tbody>
								@php $count = -1; $currency = $transaction_return->exchange_price; @endphp 
								@foreach($transactionSell as $it)
									@include('sell_return.partials.edit_product_row', [
																						'product'                 => $it, 
																						'row_count'               => $loop->index, 
																						'tax_dropdown'            => $taxes,
																						'currency'                => $currency, 
																						  
																					])
									@php $count++; @endphp 
								@endforeach
							</tbody>
						</table>
					</div>
				</div>
				<input type="hidden" id="product_row_index" value="{{$count+1}}">
				<div class="clearfix"></div>
				<div class="row" style="padding:0px;border-radius:10px;background-color:#f7f7f7;margin:auto 1%;box-shadow:1px 1px 10px grey;">
					<div>&nbsp;</div>
					<div>&nbsp;</div>
					<div class="col-md-3">&nbsp;</div>
					<div class="col-md-3">&nbsp;</div>
					<div class="col-md-3">
						<br>
						<div class="pull-right col-md-12">
							<table class="pull-right col-md-12">
								<tr>
									<th class="col-md-7 text-right"> &nbsp;</th>
									<td class="col-md-5 text-left">
										<span    >&nbsp;</span>
									</td>
								</tr>
								<tr>
									<th  @if($transaction_return->exchange_price > 0)  class="col-md-7 text-right   cur_symbol" @else  class="col-md-7 text-right hide cur_symbol" @endif >@lang('purchase.sub_total_amount' ) @if($transaction_return->exchange_price > 0)  {{ $transaction_return->currency->symbol }} @endif:</th>
									<td class="col-md-5 text-left">
										<span id="total_subtotal_cur" @if($transaction_return->exchange_price > 0) class="display_currency  "  @else  class="display_currency hide"  @endif></span>
										<!-- This is total before purchase tax-->
										<input type="hidden" id="total_subtotal_input_cur" value=0  name="total_before_tax_cur">
									</td>
								</tr>
							</table>
						</div>
					</div>
					<div class="col-md-3">
						<div class="text-center"><b>@lang('purchase.qty_total'):</b> <span id="total_qty_return">0.00</span></div><br>
						<div class="text-center"><b>@lang('purchase.sub_total_amount'):</b> <span id="total_return_">0.00</span></div>
						<input  id="total_return_input" type="text"  name="total_return_input" class="hide" value="0"> 
					</div>
					
					<div class="clearfix"></div>
					<div>&nbsp;</div>
					<div>&nbsp;</div>
				</div>
			@endcomponent
			 @component("components.widget",["class"=>"box-primary"])
				<div>&nbsp;</div>
				<div>&nbsp;</div>
				{{-- Discount & Vat --}}
				<div class="col-sm-12">
					<table class="table">
						<tr>
							<td class="col-md-3 "  >
								<div class="form-group">
									{!! Form::label('discount_type', __( 'purchase.discount_type' ) . ':') !!}
									{!! Form::select('discount_type', [ '' => __('lang_v1.none'), 'fixed_before_vat' => __( 'home.fixed before vat' ), 'fixed_after_vat' => __( 'home.fixed after vat' ), 'percentage' => __( 'lang_v1.percentage' )], $transaction_return->discount_type, ['class' => 'form-control select2']); !!}
								</div>
							</td>
							@php
								if ($transaction_return->discount_type == "fixed_before_vat"){
									$dis = $transaction_return->discount_amount;
								}else if ($transaction_return->discount_type == "fixed_after_vat"){
									$tax = \App\TaxRate::find($transaction_return->tax_id);
									$dis = ($transaction_return->discount_amount*(100+$tax->amount))/100 ;
								}else if ($transaction_return->discount_type == "percentage"){
									$dis = ( 100 *  $transaction_return->discount_amount ) / $transaction_return->total_before_tax;
								}else{
									$dis = 0;
								}
							@endphp 	
							<td class="col-md-3">
								<div class="form-group">
								{!! Form::label('discount_amount', __( 'purchase.discount_amount' ) . ':') !!}
								{!! Form::text('discount_amount', number_format($dis,2), ['class' => 'form-control input_number', 'required']); !!}
								</div>
							</td>
							<td class="col-md-3 pull-right">
								<b  @if($transaction_return->exchange_price > 0) class="i_curr  " @else class="i_curr hide" @endif > @lang( 'purchase.discount' ) @if($transaction_return->exchange_price > 0)  {{ $transaction_return->currency->symbol }} @endif: (-)</b>   
								 <span id="discount_calculated_amount_cur" @if($transaction_return->exchange_price > 0) class="display_currency  "@else  class="display_currency hide"@endif >0</span>
							</td>
							<td class="col-md-3 text-right">
								<b>@lang( 'purchase.discount' ):</b>(-) 
								<span id="discount_calculated_amount2" class="display_currency">{{$transaction_return->discount_amount}}</span>
 								{!! Form::hidden('discount_amount2', $transaction_return->discount_amount , ['id' => 'discount_amount2']); !!}

							</td>
						</tr>
					
						<tr>
							<td>
								<div class="col-md-12">
									<div class="form-group">
										{!! Form::label('tax_id', __('purchase.purchase_tax') . ':') !!}
										<select name="tax_id" id="tax_id" class="form-control select2" placeholder="'Please Select'">
											<option value="" data-tax_amount="0" data-tax_type="fixed" >@lang('lang_v1.none')</option>
												@php $co = $transaction_return->tax_id; @endphp
												@foreach($taxes as $tax)
													<option value="{{ $tax->id }}" data-tax_amount="{{ $tax->amount }}" data-tax_type="{{ $tax->calculation_type }}" @if($co == $tax->id) selected @endif>{{ $tax->name }}</option>
 												@endforeach
										</select>
										{!! Form::hidden('tax_amount', $transaction_return->tax_amount, ['id' => 'tax_amount']); !!}
									</div>
								</div>
							</td>
							<td class="col-sm-3">&nbsp;</td>
							<td class="col-sm-3 pull-right" >
								<b @if($transaction_return->exchange_price > 0) class="t_curr  " @else class="t_curr hide" @endif  >@lang( 'purchase.purchase_tax' ) @if($transaction_return->exchange_price > 0)  {{ $transaction_return->currency->symbol }} @endif: (+)</b> 
								<span id="tax_calculated_amount_curr" @if($transaction_return->exchange_price > 0) class="display_currency  " @else class="display_currency hide" @endif  >0</span></td>
							</td>
							<td class="col-sm-3   text-right">
								<b>@lang( 'purchase.purchase_tax' ):</b>(+) 
								<span id="tax_calculated_amount" class="display_currency">{{$transaction_return->tax_amount}}</span>
								{!! Form::hidden('tax_amount2', $transaction_return->tax_amount , ['id' => 'tax_amount2']); !!}
							</td>
						</tr>
						 
						 
					</table>
				</div>
				{{-- total --}}
				<div class="col-sm-12 text-right">
					 
					<div  class="col-sm-3 pull-left"  >
					</div>
					<div  class="col-sm-3 pull-left"  >
					</div>
					<div  class="col-sm-3 pull-left"  >
						<br>
						<b @if($transaction_return->exchange_price > 0) class="z_curr  " @else class="z_curr hide" @endif >@lang('purchase.purchase_total_') @if($transaction_return->exchange_price > 0)  {{ $transaction_return->currency->symbol }} @endif: </b>
						<span id="total_final_i_curr" @if($transaction_return->exchange_price > 0) class="display_currency  " @else class="display_currency hide" @endif >0</span>
					</div>
					<div class="pull-right">
						 <b>@lang('purchase.purchase_pay'): </b><span id="total_final_" class="display_currency" data-currency_symbol='true'>{{$transaction_return->final_total}}</span>
						 {!! Form::hidden('total_final_input', $transaction_return->final_total  , ['id' => 'total_final_input']); !!}
						 <br>
						 <br>
					</div>
				</div>
				{{-- Submit --}}
				<div class="col-md-12">
					<button type="button" id="submit_purchase_return_form" class="btn btn-primary pull-right btn-flat">@lang('messages.submit')</button>
				</div>
			@endcomponent	
			</div>
		</div>
	</div> <!--box end-->
	 
	{!! Form::close() !!}
</section>
@stop
@section('javascript')
	<script src="{{ asset('js/sell_return.js?v=' . $asset_v) }}"></script>
	<script src="{{ asset('js/posss.js?v=' . $asset_v) }}"></script>
	<script type="text/javascript">
		__page_leave_confirmation('#purchase_return_form');
		update_row();
		all_changes();
		// update_table_total();
		change_currency();
		// update_table_total();
 		$('.currency_id_amount').change(function(){
			if($('.currency_id').val() != ""){
				// os_total_sub();
				update_row_curr(1);
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
					$(".check_dep_curr").addClass("hide");
					
					$('input[name="dis_currency"]').prop('checked', false);
			    	$('#depending_curr').prop('checked', false);
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
							
							// $(".curr_column").removeClass("hide");
							update_row();
						},
					});	 
				}
		})
		// #2024-8-6
		$('table#purchase_return_product_table').on('change', 'select.list_price', function() {
			 
			 var tr                      = $(this).closest('tr');
			 var base_unit_cost          = tr.find('input.base_unit_cost').val();
			 var base_unit_selling_price = tr.find('input.base_unit_selling_price').val();
			 var global_price            = $("#list_price").val();
			 var price = parseFloat(
				 $(this)
					 .find(':selected')
					 .data('price')
				 );
			 var cp_element = tr.find('input.unit_price_before_dis_exc');
			 
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
			 $('table#purchase_return_product_table .unit_price_before_dis_exc').each( function() {
				 
				 var price_item      = $(this); 
				 var price           = $(this).closest("tr"); 
				 var prices          = price.find(".list_price"); 
				 var list_price      = price.find(".list_price").find(":selected").val(); 
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
@endsection
