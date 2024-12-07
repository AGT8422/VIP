@extends('layouts.app')
@section('title', __('lang_v1.edit_purchase_return'))

@php
	$lang = (session()->get('user.language', config('app.locale'))=='ar')?"right":"left";  
@endphp
	 

@section('special_css')
	<style>
		.box-solid{
			box-shadow: 0px 0px 0px transparent;
		}
		@media (max-width: 600px) {
			
			.container {
				width: 90%;
			}
			.content {
				width: 100%;
				margin: 0px;
			}
			#inside-content{
				margin-right: {{"0% !important"}};
				margin-left:  {{"0% !important"}};
			}
		}

		/* Styles for tablets (width between 600px and 900px) */
		@media (min-width: 600px) and (max-width: 900px) { 
			.container {
				width: 80%;
			}
			.content {
				width: 100%;
				margin: 0px;
			}
			#inside-content{
				/* margin-right: {{($lang=="left")?"0% !important":"10% !important"}};
				margin-left:  {{($lang=="left")?"10% !important":"0% !important"}}; */
			}
		}

		/* Styles for desktops (min width 900px) */
		@media (min-width: 900px) {
			.container {
				width: 70%;
			}
			.content {
				width: 80%;
				/* margin: 0px; */
			}
		}
	</style>
@endsection

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
<br>
    <h1>@lang('lang_v1.edit_purchase_return')</h1>
	<h5><i><b>{{ "   Purchase Return >  " }} </b>{{ "Edit Purchase Return : ("   }} <b> {{$purchase_return->ref_no}} </b> {{")"}}</i></h5>
	<br>
</section>

<!-- Main content -->
<section class="content no-print">
	{!! Form::open(['url' => action('CombinedPurchaseReturnController@update'), 'method' => 'post', 'id' => 'purchase_return_form', 'files' => true ]) !!}
	@component("components.widget",["class"=>"box-primary"])
	<div class="box box-solid">
		<div class="box-body">
			<div class="row">
				<div class="col-md-4 col-sm-12 col-md-offset-1 col-12" style="background-color:#f7f7f7;padding:10px;border-radius:10px;box-shadow:1px 1px 10px grey;margin:10px 2%;">
					<div class="col-sm-12">
						<div class="form-group">
							<input type="hidden" name="purchase_return_id" value="{{$purchase_return->id}}">
							<input type="hidden" id="location_id" value="{{$purchase_return->location_id}}">
							{!! Form::label('supplier_id', __('purchase.supplier') . ':*') !!}
							<div class="input-group">
								<span class="input-group-addon">
									<i class="fa fa-user"></i>
								</span>
								{!! Form::select('contact_id', [ $purchase_return->contact_id => $purchase_return->contact->name], $purchase_return->contact_id, ['class' => 'form-control', 'placeholder' => __('messages.please_select'), 'required', 'id' => 'supplier_id']); !!}
								{!! Form::hidden('sup_id',  $purchase_return->contact_id, ['class' => 'form-control', 'id' => 'sup_id']); !!}
								<span class="input-group-btn">
									<button type="button" class="btn btn-default create_contact bg-white btn-flat add_new_supplier" data-name=""><i class="fa fa-plus-circle text-primary fa-lg"></i></button>
								</span>
							</div>
						</div> 
						<strong>
							@lang('business.address'):
						</strong>
						<div id="supplier_address_div">
							{!! $purchase_return->contact->contact_address !!}
						</div>
					</div>
				</div> 
				<div class="col-md-7 col-sm-6 " style="background-color:#f7f7f7;padding:10px;border-radius:10px;box-shadow:1px 1px 10px grey; margin:10px 2%;">
					@php 
						$default_location =  array_key_first($business_locations->toArray());
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
							{!! Form::text('ref_no', $purchase_return->ref_no, ['class' => 'form-control']); !!}
						</div>
					</div>
					
					@if($purchase_return->status == "received" || !empty($TranRecieved))       
						<div class="col-sm-6 hide">
							<div class="form-group">
								{!! Form::label('status', __('purchase.purchase_status') . ':*') !!}
								@show_tooltip(__('tooltip.order_status'))
								{!! Form::select('status', $orderStatuses, $purchase_return->status, ['class' => 'form-control select2   ', 'placeholder' => __('messages.please_select') ,  'required' ]); !!}
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-group">
								{!! Form::label('status', __('purchase.purchase_status') . ':*') !!}
								@show_tooltip(__('tooltip.order_status'))
								{!! Form::text('s',   $purchase_return->status, ['class' => 'form-control ', 'readOnly', 'placeholder' => __('messages.please_select')    ]); !!}
							</div>
						</div>
					@else
						<div class="col-sm-6  ">
								<div class="form-group">
								{!! Form::label('status', __('purchase.purchase_status') . ':*') !!}
								@show_tooltip(__('tooltip.order_status'))
								{!! Form::select('status', $orderStatuses, $purchase_return->status, ['class' => 'form-control select2   ', 'placeholder' => __('messages.please_select') ,  'required' ]); !!}
							</div>
						</div>
					@endif
					{!! Form::hidden('old_sts',$purchase_return->status, ['class' => 'form-control ',"id" => "old_sts"]); !!}
					<div class="col-sm-6">
						<div class="form-group">
							{!! Form::label('transaction_date', __('messages.date') . ':*') !!}
							<div class="input-group">
								<span class="input-group-addon">
									<i class="fa fa-calendar"></i>
								</span>
								{!! Form::text('transaction_date', @format_datetime($purchase_return->transaction_date), ['class' => 'form-control', 'readonly', 'required']); !!}
							</div>
						</div>
					</div>
					<div class="col-sm-6">
						<div class="form-group">
							{!! Form::label('store_id', __('warehouse.warehouse').':*') !!}
							{!! Form::select('store_id', $mainstore_categories, $purchase_return->store, ['class' => 'form-control select2', 'required' , 'placeholder' => __('messages.please_select') ] ); !!}
						</div>
					</div>
					<div class="col-sm-6">
						<div class="form-group">
							{!! Form::label('cost_center_id', __('home.Cost Center').':*') !!}
							{{-- @show_tooltip(__('tooltip.purchase_location')) --}}
							{!! Form::select('cost_center_id', $cost_centers, $purchase_return->cost_center_id, ['class' => 'form-control select2','id'=>'cost_center_id', 'placeholder' => __('messages.please_select')] ); !!}
						</div>
					</div>
					<div class="clearfix"></div>
					{{-- #2024-8-6 --}}
					<div class="col-sm-6 col-6">
						<div class="form-group">
							{!! Form::label('list_price', __('List  Of Prices').':') !!}
							{!! Form::select('list_price',$list_of_prices,null, ['class' => 'form-control select2' , 'id' => 'list_price' ]); !!}
						</div>
					</div>
					<div class="col-sm-6">
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
					<div class="col-sm-6">
						<div class="form-group">
							{!! Form::label('sup_ref_no', __('purchase.sup_refe').':') !!}
							{!! Form::text('sup_ref_no', $purchase_return->sup_refe, ['class' => 'form-control']); !!}
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<div class="multi-input">
							{!! Form::label('currency_id', __('business.currency') . ':') !!}  
							<br/>
							{!! Form::select('currency_id', $currencies, $purchase_return->currency_id, ['class' => 'form-control width-60 currency_id  select2', 'placeholder' => __('messages.please_select') ]); !!}
							{!! Form::text('currency_id_amount', $purchase_return->exchange_price, ['class' => 'form-control width-40 pull-right currency_id_amount'   ]); !!}
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
									<th @if($purchase_return->currency_id != null ) class="curr_column br_dis  cur_check  " @else class="curr_column br_dis  cur_check hide" @endif  >@lang( 'lang_v1.Cost before without Tax' )</th>
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
									<th @if($purchase_return->currency_id != null ) class="curr_column ar_dis  cur_check  " @else class="curr_column ar_dis  cur_check hide" @endif >@lang( 'home.Cost without Tax currency' ) @if($purchase_return->exchange_price > 1) {{  ($purchase_return->currency)?$purchase_return->currency->symbol:"" }} @endif</th>
									@if(session('business.enable_product_expiry'))
										<th>
											@lang('product.exp_date')
										</th>
									@endif
									<th class="text-center">
										@lang('sale.subtotal')
									</th>
									<th class="text-center"><i class="fa fa-trash" aria-hidden="true"></i></th>
								</tr>
							</thead>
							<tbody>
								@php $count = -1; $currency = ($purchase_return->exchange_price>1)?$purchase_return->exchange_price:null @endphp 
								@foreach($purchase_lines as $purchase_line)
									@include('purchase_return.partials.edit_product_table_row', ['product' => $purchase_line, 'row_index' => $loop->index, 'edit' => true ,"currency"=>$currency])
										@php
											$row_index = $loop->iteration;
										@endphp
									@php $count++; @endphp 

								@endforeach
							</tbody>
						</table>
					</div>
				</div>
				<input type="hidden" id="product_row_index" value="{{$count+1}}">
				<div class="clearfix"></div>
			<div class="row" style="background-color:#f7f7f7;padding:10px;border-radius:10px;box-shadow:1px 1px 10px grey; margin:auto 20px;">
				<div>&nbsp;</div>
				<div>&nbsp;</div>
				<div class="col-md-3">&nbsp;</div>
				<div class="col-md-3">&nbsp;</div>
				{{-- <div class="col-md-3">&nbsp;</div> --}}
				<div class="col-md-3">
					<br>
					<div class="pull-left col-md-12">
						<table class="pull-left col-md-12">
							<tr>
								<th class="col-md-7 text-right"> &nbsp;</th>
								<td class="col-md-5 text-left">
									<span    >&nbsp;</span>
								</td>
							</tr>
							<tr>
								<th  @if($purchase_return->exchange_price > 0) class="col-md-7 text-right  cur_symbol" @else class="col-md-7 text-right hide cur_symbol" @endif  >@lang('purchase.sub_total_amount' ) @if($purchase_return->exchange_price > 0) {{  ($purchase_return->currency)?$purchase_return->currency->symbol:"" }} @endif:</th>
								<td class="col-md-5 text-left">
									<span id="total_subtotal_cur" @if($purchase_return->exchange_price > 0) class="display_currency " @else class="display_currency hide" @endif ></span>
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
 					{!! Form::hidden('total_return_input', 0 , ['id' => 'total_return_input']); !!}
				</div>
			</div>
				<div class="clearfix"></div>
				<div>&nbsp;</div>
				<div>&nbsp;</div>
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
									{!! Form::select('discount_type', [ '' => __('lang_v1.none'), 'fixed_before_vat' => __( 'home.fixed before vat' ), 'fixed_after_vat' => __( 'home.fixed after vat' ), 'percentage' => __( 'lang_v1.percentage' )], $purchase_return->discount_type, ['class' => 'form-control select2']); !!}
								</div>
							</td>
							@php
								if ($purchase_return->discount_type == "fixed_before_vat"){
									$dis = $purchase_return->discount_amount;
								}else if ($purchase_return->discount_type == "fixed_after_vat"){
									$tax = \App\TaxRate::find($purchase_return->tax_id);
									$dis = ($purchase_return->discount_amount*(100+$tax->amount))/100 ;
								}else if ($purchase_return->discount_type == "percentage"){
									$dis = ( 100 *  $purchase_return->discount_amount ) / $purchase_return->total_before_tax;
								}else{
									$dis = 0;
								}
							@endphp
							<td class="col-md-3">
								<div class="form-group">
								{!! Form::label('discount_amount', __( 'purchase.discount_amount' ) . ':') !!}
								{!! Form::text('discount_amount', round($dis,2), ['class' => 'form-control input_number', 'required']); !!}
								</div>
							</td>
							<td class="col-md-3 pull-right">
								<b  @if($purchase_return->exchange_price > 0) class="i_curr  " @else class="i_curr hide" @endif > @lang( 'purchase.discount' ) @if($purchase_return->exchange_price > 0)  {{ ($purchase_return->currency)?$purchase_return->currency->symbol:"" }} @endif: (-)</b>   
								 <span id="discount_calculated_amount_cur" @if($purchase_return->exchange_price > 0) class="display_currency  "@else  class="display_currency hide"@endif >0</span>
							</td>
							<td class="col-md-3 text-right">
								<b>@lang( 'purchase.discount' ):</b>(-) 
 								<span id="discount_calculated_amount2" class="display_currency">{{$purchase_return->discount_amount}}</span>
 								{!! Form::hidden('discount_amount2', $purchase_return->discount_amount , ['id' => 'discount_amount2']); !!}
							</td>
						</tr>
					
						<tr>
							<td>
								<div class="col-md-12">
									<div class="form-group">
										{!! Form::label('tax_id', __('purchase.purchase_tax') . ':') !!}
										<select name="tax_id" id="tax_id" class="form-control select2" placeholder="'Please Select'">
											<option value="" data-tax_amount="0" data-tax_type="fixed" >@lang('lang_v1.none')</option>
												@php $co = $purchase_return->tax_id; @endphp
												@foreach($taxes as $tax)
													<option value="{{ $tax->id }}" data-tax_amount="{{ $tax->amount }}" data-tax_type="{{ $tax->calculation_type }}" @if($co == $tax->id) selected @endif>{{ $tax->name }}</option>
												@endforeach
										</select>
										{!! Form::hidden('tax_amount', $purchase_return->tax_amount, ['id' => 'tax_amount']); !!}
									</div>
								</div>
							</td>
							<td></td>
							<td class="col-sm-3 pull-right" >
								<b @if($purchase_return->exchange_price > 1) class="t_curr  " @else class="t_curr hide" @endif  >@lang( 'purchase.purchase_tax' ) @if($purchase_return->exchange_price > 1)  {{ ($purchase_return->currency)?$purchase_return->currency->symbol:"" }} @endif: (+)</b> 
								<span id="tax_calculated_amount_curr" @if($purchase_return->exchange_price > 1) class="display_currency  " @else class="display_currency hide" @endif  >0</span></td>
							</td>
					 
							<td class="col-sm-3   text-right">
								<b>@lang( 'purchase.purchase_tax' ):</b>(+) 
								<span id="tax_calculated_amount" class="display_currency">{{$purchase_return->tax_amount}}</span>
 								{!! Form::hidden('tax_amount2', $purchase_return->tax_amount, ['id' => 'tax_amount2']); !!}
							</td>
						</tr>
					 
				{{-- total bill --}}
				<tr>
					<td class=" col-sm-3">&nbsp;</td>
					<td class=" col-sm-3">&nbsp;&nbsp;&nbsp;&nbsp;</td>
					<td></td>
					<td  class="col-sm-3 pull-left"  >
						<br>
						<b @if($purchase_return->exchange_price > 0) class="z_curr  " @else class="z_curr hide" @endif >@lang('purchase.purchase_total_') @if($purchase_return->exchange_price > 1)  {{ ($purchase_return->currency)?$purchase_return->currency->symbol:"" }} @endif: </b>
						<span id="total_final_i_curr" @if($purchase_return->exchange_price > 1	) class="display_currency  " @else class="display_currency hide" @endif >0</span>
					</td>
					<td class="pull-right">
						<br>
						<b>@lang('purchase.purchase_total_'): </b><span id="total_finals" class="display_currency" data-currency_symbol='true'>{{$purchase_return->final_total}}</span>
						{!! Form::hidden('total_finals_', $purchase_return->final_total , ['id' => 'total_finals_']); !!}

					</td>
				</tr>
				<tr>
					<td>
						<div class="form-group">
						{!! Form::label('shipping_details', __( 'purchase.shipping_details' ) . ':') !!}
						{!! Form::text('shipping_details', $purchase_return->shipping_details, ['class' => 'form-control']); !!}
						</div>
					</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					@php $transaction_recieved  = null ;@endphp
					<td class="pull-right">
						<div class="form-group">
						{!! Form::label('shipping_charges','(+) ' . __( 'purchase.supplier_shipping_charges' ) . ':') !!}
						<div class="input-group">
							<span class="input-group-btn">
								<button type="button"   class="btn btn-default bg-white btn-flat" title="@lang('unit.add_unit')" data-toggle="modal" data-target="#exampleModal"><i class="fa fa-plus-circle text-primary fa-lg"></i></button>
							</span>
						      {!! Form::hidden('ADD_SHIP', $purchase_return->ship_amount, ['class' => 'form-control input_number' ,"id" => "total_ship_"  ]); !!}
						      {!! Form::text('ADD_SHIP', $purchase_return->ship_amount, ['class' => 'form-control input_number' ,"id" => "total_ship_" , "disabled" ,'required']); !!}
						</div>
						</div>
						<!-- Modal -->
						<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
							<input id="ship_from" type="hidden" value="{{$ship_from}}">
							@include('additional_Expense.edit',["purchase"=>$purchase_return,'transaction_recieved' => $transaction_recieved,"check"=>1])
						</div>
					</td>
				</tr>
				{{-- purchase total --}}
				<tr>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					 
					 
					<td class=" col-sm-4  pull-left">
						<b @if($purchase_return->exchange_price > 1) class="o_curr  " @else class="o_curr hide" @endif  >@lang('purchase.purchase_total') @if($purchase_return->exchange_price > 0)  {{ ($purchase_return->currency)?$purchase_return->currency->symbol:"" }} @endif: </b>
						<span id="grand_total_cur" @if($purchase_return->exchange_price > 1) class="display_currency  " @else class="display_currency hide" @endif    >0</span>
					 
						 
					</td>
					<td class=" col-sm-4 pull-right">
						{!! Form::hidden('final_total', 0 , ['id' => 'grand_total_hidden']); !!}
						<b>@lang('purchase.purchase_total'): </b><span id="total_final_" class="display_currency" data-currency_symbol='true'>0</span>
						<br>
						<br>
						{!! Form::hidden('final_total_hidden_', 0 , ['id' => 'total_final_hidden_']); !!}
						
					</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
 					<td>&nbsp;</td>
 					<td>&nbsp;</td>
					<td class="pull-right">
						<div class="form-group">
						{!! Form::label('shipping_charges','(+) ' . __( 'purchase.cost_shipping_charges' ) . ':') !!}
						<div class="input-group">
							<span class="input-group-btn">
								<button type="button"   class="btn btn-default bg-white btn-flat" title="@lang('unit.add_unit')" data-toggle="modal" data-target="#exampleModal"><i class="fa fa-plus-circle text-primary fa-lg"></i></button>
							</span>
						      {!! Form::hidden('ADD_SHIP_', 0, ['class' => 'form-control input_number' ,"id" => "total_ship_c"  ]); !!}
						      {!! Form::text('ADD_SHIP_', 0, ['class' => 'form-control input_number' ,"id" => "total_ship_c" , "disabled" ,'required']); !!}
						</div>
						</div>
						 
					</td>
				</tr>
				{{-- total bill --}}
				<tr>
					<td>&nbsp;</td>
					 
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td class="pull-left">
						<br>
						<b  @if($purchase_return->exchange_price > 1)   class="c_curr  " @else class="c_curr hide" @endif>@lang('purchase.purchase_pay') @if($purchase_return->exchange_price > 1)  {{ ($purchase_return->currency)?$purchase_return->currency->symbol:"" }} @endif: </b>
						<span id="total_final_curr" @if($purchase_return->exchange_price > 1)    class="display_currency " @else  class="display_currency hide" @endif   >0</span>
					</td>
					<td class="pull-right">
						<br>
						<b>@lang('purchase.purchase_pay'): </b><span id="total_final_x"  class="display_currency" data-currency_symbol='true'>0</span>
					</td>
				</tr>
				<tr>
					<td colspan="4">
						<div class="form-group">
							{!! Form::label('additional_notes',__('purchase.additional_notes')) !!}
							{!! Form::textarea('additional_notes', $purchase_return->additional_notes, ['class' => 'form-control', 'rows' => 3]); !!}
						</div>
					</td>
				</tr>
						 
					</table>
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
<!-- /.content -->
<!-- quick product modal -->
{{-- *3* SECTION MODAL  --}}
{{-- ********************************** --}}
<div class="modal fade quick_add_product_modal" tabindex="-1" role="dialog" aria-labelledby="modalTitle"></div>
<div class="modal fade contact_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
	@include('contact.create', ['quick_add' => true])
</div>
{{-- ********************************** --}}
@stop
@section('javascript')
<script src="{{ asset('js/purchase_return.js?v=' . $asset_v) }}"></script>
<script type="text/javascript">
		$(document).ready(function(){
			
			// #2024-8-6
		$('table#purchase_return_product_table').on('change', 'select.sub_unit', function() {
				
				var tr = $(this).closest('tr');
				var base_unit_cost = tr.find('input.base_unit_cost').val();
				var prices = tr.find('.list_price');
				var base_unit_selling_price = tr.find('input.base_unit_selling_price').val();
				var global_prices = $('#list_price');

				var multiplier = parseFloat(
					$(this)
						.find(':selected')
						.data('multiplier')
					);
				var check_price = parseFloat(
					$(this)
					.find(':selected')
					.data('check_price')
				);
				
				if(check_price == 0){	
					var base_unit_cost = parseFloat(
							$(this)
							.find(':selected')
							.data('price')
					);
				}
				multiplier = isNaN(multiplier)?1:multiplier;
				var unit_sp   = base_unit_selling_price * multiplier;
				var unit_cost = base_unit_cost  ;
			
				var sp_element = tr.find('input.default_sell_price');
				__write_number(sp_element, unit_sp);
				
				var cp_element = tr.find('input.eb_price');
				var cp_element_2 = tr.find('input.unit_price_before_dis_exc');
				
				if(check_price == 0){
					cp_element.val(unit_cost) ;
					cp_element_2.val(unit_cost) ;
				}else{
					cp_element.val(unit_cost* multiplier)
					cp_element_2.val(unit_cost* multiplier)
	
				} 
				html = "";global_html = "";
				list_price = JSON.stringify(tr.find('.list_price').data("prices"));
				list_of    = JSON.parse(list_price);
				
				for(i in list_of){
					if(i == $(this).find(':selected').val()){
						html += '<option value="" data-price="null" selected>None</option>';
						counter = 0;
						for(e in list_of[i]){
							html += '<option value="'+list_of[i][e].line_id+'" data-price="'+list_of[i][e].price+'"  data-value="'+list_of[i][e].line_id+'" >'+list_of[i][e].name+'</option>';
							View_selected = (counter == 0)?"selected":"";
							global_html += '<option value="'+list_of[i][e].line_id+'" '+View_selected+' data-price="'+list_of[i][e].price+'"  data-value="'+list_of[i][e].line_id+'" >'+list_of[i][e].name+'</option>';
							counter++;
						}
					}
				}
				
				if(html != ""){
					prices.html(html);
					global_prices.html(global_html);
				}
				cp_element.change();
				cp_element_2.change();
				update_row();
			});
			$(document).on("change","#list_price",function(){
				var golbal = $(this).val();
				$('table#purchase_return_product_table .eb_price').each( function() {
					var price_item   = $(this); 
					var price      = $(this).closest("tr"); 
					var prices     = price.find(".list_price"); 
					var list_price = price.find(".list_price").find(":selected").val(); 
					if(list_price == "" || list_price == null){
						prices.children().each(function(){ 
						if($(this).data("value") == golbal){
							price_item.val($(this).data("price"));
						}
					}); 
					}
				});
				$('table#purchase_return_product_table .unit_price_before_dis_exc').each( function() {
					var price_item   = $(this); 
					var price      = $(this).closest("tr"); 
					var prices     = price.find(".list_price"); 
					var list_price = price.find(".list_price").find(":selected").val(); 
					if(list_price == "" || list_price == null){
						prices.children().each(function(){ 
						if($(this).data("value") == golbal){
							price_item.val($(this).data("price"));
						}
					}); 
					}
				});
				update_row();
			});
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
				var cp_element = tr.find('input.eb_price');
				var cp_element_2 = tr.find('input.unit_price_before_dis_exc');
				
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
				cp_element_2.val(final_price);
				cp_element.change();
				cp_element_2.change();
				update_row();
			});

		change_currency();
		__page_leave_confirmation('#purchase_return_form');
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
				$(".os_curr").addClass("hide" );
				$(".oss_curr").addClass("hide" );
				$(".ship_curr").addClass("hide" );
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
				$(".os_curr").removeClass("hide" );
				$(".oss_curr").removeClass("hide" );
				$(".ship_curr").removeClass("hide" );				
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
					    $(".header_texts").html( @json(__('Amount')) + " " + object.symbol +"   "    );
 						$(".header_totals").html( @json(__('Total')) + " " + object.symbol +"   "    );
 						$(".header_vats").html( @json(__('Vat')) + " " + object.symbol +"   "    );
						// $(".curr_column").removeClass("hide");
						update_row();
					},
				});	 
			}
		})
		function total_bill(){
			var total_amount_shiping = 0;
			var total_vat_shiping = 0;
			var total_shiping = 0;
			var  supplier_pay  =  0;
			var  cost_pay  =  0;
			var supplier_id =  $('#supplier_id option:selected').val();
			$('.shipping_amount_s').each(function(){
			
			var el      =  $(this).parent().parent();
			var sup_id =   el.children().find('.supplier option:selected').val();
			
			el.children().find('.shipping_tax').val();
			var amount  =  parseFloat($(this).val()) ;
			var tax     = parseFloat(el.children().find('.shipping_tax').val()) ;
			total_vat_shiping += tax;
			var total_s = amount+tax; 
			total_amount_shiping += amount;
			total_shiping +=total_s;
			
			if ( (sup_id == supplier_id || sup_id == "" ) && supplier_id != ""  ) {
				supplier_pay +=total_s;
			}else{
				cost_pay +=total_s;
			}
		
			el.children().find('.shipping_total').val(total_s);
			})
			$('#shipping_total_amount').text(total_amount_shiping+'AED');
			$('input[name="ADD_SHIP"]').val(supplier_pay);
			$('input[name="ADD_SHIP_"]').val(cost_pay);
			$('#shipping_total_vat_s').text(total_vat_shiping+'AED');
			$('#shipping_total_s').text(total_shiping+'AED');
			
			var total_items =  $("#total_subtotal_input_id").val();       
			var total =  parseFloat($("#total_finals_").val()).toFixed(2);       
			var ship =  $("#total_ship_").val();       
			var ship_ =  $("#total_ship_c").val(); 
			currancy = $(".currency_id_amount").val();
			if(currancy != "" && currancy != 0){
				$("#total_final_i_curr").html(((parseFloat(total).toFixed(2))/currancy).toFixed(4));       
				$("#grand_total_cur").html((((parseFloat(total) + parseFloat(ship)).toFixed(2))/currancy).toFixed(4));       
				$("#total_final_curr").html(((parseFloat(total) + parseFloat(ship) + parseFloat(ship_)).toFixed(2)/currancy).toFixed(4));       
			}  
   			$("#total_final_i").html((parseFloat(total)).toFixed(2));      
			$("#total_final_hidden_").val((parseFloat(total)  + parseFloat(ship)).toFixed(2)); 
			$("#grand_total_hidden").val((parseFloat(total)  + parseFloat(ship)).toFixed(2));       
			$("#grand_total").html((parseFloat(total)  + parseFloat(ship) ).toFixed(2));       
			$("#total_final_").html((parseFloat(total) + parseFloat(ship)  ).toFixed(2));       
			$("#total_final_x").html((parseFloat(total)   + parseFloat(ship_)).toFixed(2));       
			$("#grand_total2").html((parseFloat(total)   + parseFloat(ship_)).toFixed(2)); 
			$("#grand_total_items").html((parseFloat(total_items) + parseFloat(ship)).toFixed(2));       
			$("#final_total_hidden_items").val((parseFloat(total_items) + parseFloat(ship)).toFixed(2));       
			$("#total_final_items").html((parseFloat(total_items) + parseFloat(ship) + parseFloat(ship_)).toFixed(2));
			$("#total_final_items_").val((parseFloat(total_items) + parseFloat(ship) + parseFloat(ship_)).toFixed(2));
			$("#payment_due_").html(parseFloat($("#grand_total2").html()) - parseFloat($(".payment-amount").val()));       
			$(".hide_div").removeClass("hide");
			// console.log($(".hide_div").html());
		}
		 
		total_bill();
		update_shipping();
		update_row(); 
		discount_final();
		all_changes();
					 
		
		__page_leave_confirmation('#purchase_return_form');
		});
		$(document).on('click', '.create_contact', function() { 
        setTimeout(() => {
            // alert($('.button-submit').html())
            html  =  '<button type="submit" id="contact-submit" class="btn btn-primary">Save</button>';
            $('.button-submit').find('#contact-submit').remove();
            // alert($('.button-submit').html())
            $('.button-submit').append(html);
        }, 100);  
    });
		</script>
@endsection
@yield('child_script')

