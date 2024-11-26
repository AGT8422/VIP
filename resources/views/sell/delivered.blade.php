@extends('layouts.app')
@section('title', __('purchase.add_delivery'))



@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('purchase.add_delivery') <i class="fa fa-keyboard-o hover-q text-muted" aria-hidden="true" data-container="body" data-toggle="popover" data-placement="bottom" data-content="@include('purchase.partials.keyboard_shortcuts_details')" data-html="true" data-trigger="hover" data-original-title="" title=""></i></h1>
</section>

<!-- Main content -->
<section class="content">

	<!-- Page level currency setting -->
	<input type="hidden" id="p_code" value="{{$currency_details->code}}">
	<input type="hidden" id="p_symbol" value="{{$currency_details->symbol}}">
	<input type="hidden" id="p_thousand" value="{{$currency_details->thousand_separator}}">
	<input type="hidden" id="p_decimal" value="{{$currency_details->decimal_separator}}">

	@include('layouts.partials.error')

	{!! Form::open(['url' => action('TransactionPaymentController@make'), 'method' => 'post', 'id' => 'add_purchase_form', 'files' => true ]) !!}
	
	
	@component('components.widget', ['class' => 'box-primary'])
		
	 <div class="row">
      @if(!empty($transaction->contact))
        <div class="col-md-4">
          <div class="well">
            <strong>
            @if(in_array($transaction->type, ['purchase', 'purchase_return']))
              @lang('purchase.supplier') 
            @elseif(in_array($transaction->type, ['sell', 'sell_return']))
              @lang('contact.customer') 
            @endif
            </strong>:{{ $transaction->contact->name }}<br>
            @if($transaction->type == 'purchase')
            <strong>@lang('business.business'): </strong>{{ $transaction->contact->supplier_business_name }}
            @endif
          </div>
        </div>
        @endif
        <div class="col-md-4">
          <div class="well">
          @if(in_array($transaction->type, ['sell', 'sell_return']))
            <strong>@lang('sale.invoice_no'): </strong>{{ $transaction->invoice_no }}
          @else
            <strong>@lang('purchase.ref_no'): </strong>{{ $transaction->ref_no }}
          @endif
          @if(!empty($transaction->location))
            <br>
            <strong>@lang('purchase.location'): </strong>{{ $transaction->location->name }}
          @endif
          </div>
        </div>
        <div class="col-md-4">
          <div class="well">
			@php 
				$quantity = 0; 
				foreach ($purchcaseline as $value) {
					$quantity = $quantity + $value["quantity"];
					
				}


			@endphp
            <strong>@lang('sale.total_amount'): </strong><span class="display_currency" data-currency_symbol="false">{{$quantity}}</span><br>
            <strong>@lang('purchase.payment_note'): </strong>
            @if(!empty($transaction->additional_notes))
            {{ $transaction->additional_notes }}
            @else
              --
            @endif
          </div>
        </div>
      </div>
	  
		<div class="row">
			@if(count($business_locations) == 1)
				@php 
				
					$default_location = current(array_keys($business_locations->toArray()));
					$search_disable = false; 
				@endphp
			@else
				@php $default_location = null;
				$search_disable = true;
				@endphp
			@endif

		</div>
		<div class="row">
			<div class="col-md-12">
			  @if(!empty($transaction->contact))
				<strong>@lang('lang_v1.need_balance_items'):</strong> 
			  @endif
			</div>
		  </div>


		  <div class="row">
			<div class="col-md-12">
					<div class="table-responsive">
						<table class="table table-recieve" >
						<tr style="background:#f1f1f1;">
						  <th>@lang('messages.date')</th>
						  <th>@lang('product.product_name')</th>
						  <th>@lang('purchase.qty')</th>
						  <th>@lang('purchase.delivery_status')</th>
						  <th>@lang('warehouse.nameW')</th>
						  <th>@lang('purchase.payment_note')</th>
						  {{-- <th class="no-print">@lang('messages.actions')</th> --}}
						</tr>
						@php
						       

						 // dd($transaction);
						  $total2 = 0;
						@endphp
						@forelse ( $TransactionSellLine  as $payment)
						@php
								// dd($purchcaseline);
								// $trans_id =  
						  		
								$total2 = $total2 + $payment->quantity;
								$product_name = "";
								$product_id_src = "";
								$product_id_str = "";
								$product_id_unit = "";
								$product_id_qty = $payment->quantity;
								$product_id_unit_value = "";
								$counter = 1; 
	
								foreach($product_list as $key => $product_l){
									if($payment->product_id == $key ){
										// dd("stop");
									$product_name = $product_l;
									$product_id_src = $payment->product_id;
									$product_id_str = $payment->store_id;
									foreach($product as $rd){
									  if($rd->name == $product_name){
										$product_id_unit = $rd->unit_id;
										foreach($unit as $un){
											if($un->id == $product_id_unit){
											  $product_id_unit_value = $un->actual_name;
											};
										  }
									  };
									}
								  
	
								  }
								  $counter = $counter + 1 ;
								  
								}
								// dd($product_list);
								$Warehouse_name = "";
								$counter_1 = 1; 
								foreach($Warehouse_list as $key => $Warehouse_l){
								  if($payment->store_id == $key ){
									$Warehouse_name = $Warehouse_l;
								  }
								  $counter_1 = $counter_1 + 1 ;
								  
								  // dd($Warehouse_list);
								}
	
								// dd($transaction);
						  @endphp 

							<tr style="border:1px solid #f1f1f1;">
							  <td>{{$payment->created_at}}</td>
							  <td>{{$product_name}}</td>
							  <td>{{$payment->quantity}}</td>
							  <td>{{$transaction->status}}</td>
							  <td>{{$Warehouse_name}}</td>
							  <td>{!! Form::text('product_id_src', $product_id_src ,["hidden",'id' => 'product_id_src']); !!}</td>
							  <td hidden>{!! Form::text('product_id_str', $product_id_str ,["hidden",'id' => 'product_id_str']); !!}</td>
							  <td hidden>{!! Form::text('product_id_unit_value', $product_id_unit ,["hidden",'id' => 'product_id_unit_value']); !!}</td>
							  <td hidden>{!! Form::text('product_id_qty', $product_id_qty ,["hidden",'id' => 'product_id_qty']); !!}</td>
							</tr>
							
							
						@empty
						
						@endforelse
						<tfoot>
						  <tr class="bg-gray  font-17 footer-total" style="border:1px solid #f1f1f1;  ">
							<td class="text-center " colspan="2"><strong>@lang('sale.total'):</strong></td>
							<td>{{$total2}}</td>
							<td></td>
							<td></td>
							<td></td>
					   
						  </tr>
					   </tfoot>
						</table>
					</div>
			</div>
		  </div>

		  <div class="row">
			<div class="col-md-12">
			  @if(!empty($transaction->contact))
				<strong>@lang('lang_v1.old_delivered'):</strong> 
			  @endif
			</div>
		</div>

		<div class="row">
			<div class="col-md-12">
					<div class="table-responsive">
						<table class="table table-recieve" >
						<tr style="background:#f1f1f1;">
						  <th>@lang('messages.date')</th>
						  <th>@lang('product.product_name')</th>
						  <th>@lang('product.unit')</th>
						  <th>@lang('purchase.qty_total')</th>
						  <th>@lang('purchase.qty_current')</th>
						  {{-- <th>@lang('purchase.amount_remain')</th> --}}
						  <th>@lang('warehouse.nameW')</th>
						  <th>@lang('purchase.payment_note')</th>
						  {{-- <th class="no-print">@lang('messages.actions')</th> --}}
						</tr>
						@php
						  $total = 0;
						  $total_wrong = 0;
						  $pre = "";
						  $type = "base";
						@endphp
						@forelse ($RecievedPrevious as $Recieved)
	
						@php
							// dd($Recieved);
							$date = Carbon::now();
							
							$date_now = $Recieved->created_at;
							
							$product_name = "";
							$counter = 1; 
							$counter_all = 1; 
							foreach($product_list as $key => $product_l){
								if($Recieved->product_name == $product_l ){
									$product_name = $product_l;
									$product_n_id = $key;
								}
								$counter = $counter + 1 ;
							}
							if($product_name == ""){
								// dd( $product_list_all);
								foreach($product_list_all as $key1 => $product_l_all){
									if($Recieved->product_name == $product_l_all ){
										$product_name = $product_l_all;
										$product_n_id = $key1;
										$type = "other";
									}
									$counter_all = $counter_all + 1 ;
								}
								$total = $total ;
								$total_wrong = $total_wrong + $Recieved->current_qty ;
							}else{
								 $type = "base";
								$total = $total + $Recieved->current_qty;
							}
							
							
							$Warehouse_name = "";
							$counter_1 = 1; 
							foreach($Warehouse_list as $key => $Warehouse_l){
							  if($Recieved->store_id == $key ){
							    $Warehouse_name = $Warehouse_l;
							}
							$counter_1 = $counter_1 + 1 ;
							}
							foreach($unit as $un){
								if($Recieved->unit_id == $un->id ){
									$unt = $un->actual_name;
								}
							}
						
							// dd( $Recieved->total_qty - $total);
							// foreach ($purchcaseline as $purchase) {
							// 	$purchase->product_id = 
							// 	if($product_n_id ){
							
							// 		}
							// }
							
							  
							  // dd($Warehouse_list);
							
	
							// dd($transaction);
						@endphp 
						@if ($pre != $date_now)
							@if ($pre == "")

							@else
							<tr  style="border:1px solid #f1f1f1;" >
								<td>{{""}}</td>	
								<td>{{""}}</td>	
								<td>{{""}}</td>	
								<td>{{""}}</td>	
								<td>{{""}}</td>	
								<td>{{""}}</td>	
								{{-- <td>{{""}}</td>	 --}}
								<td>{{""}}</td>	
							<tr>
							@endif
							@php
								$style = "";
								$pre = $date_now;
							@endphp
						@endif
						<tr  @if($type != "other") style="background:#f1f1f1; width:100% !important" @else style="background:#ff5b5b; width:100% !important" @endif>
							  <td>{{$Recieved->created_at}}</td>
							  <td>{{$product_name}}</td>
							  <td>{{$unt}}</td>
							  <td>{{$Recieved->total_qty}}</td>
							  <td>{{$Recieved->current_qty}}</td>
							  {{-- <td>{{$Recieved->remain_qty}}</td> --}}
							  <td>{{$Warehouse_name}}</td>
							  <td></td>
							</tr>
							
						@empty
						
						@endforelse
						<tfoot>
						  <tr class="bg-gray  font-17 footer-total" style="border:1px solid #f1f1f1;  ">
							<td>@lang('sale.total'): {{$quantity_all}}</td>
							<td class="text-center " colspan="2"><strong></strong></td>
							<td>@if($quantity_all - $total < 0) Total more : {{-($quantity_all - $total)}}@endif </td>
							<td>Total Delivery : {{$total}} </td>
							<td>@if($quantity_all - $total < 0) Total remain : {{ 0 }} @else Total remain : {{ $quantity_all - $total}}@endif </td>
							{{-- <td></td> --}}
							<td>Wrong Delivery : {{$total_wrong}}</td>
					   
						  </tr>
					   </tfoot>
						</table>
				</div>
			</div>
		</div>
		
	@endcomponent
		
	 
		 
 
	@if ($total >= $total2)
		@php
			$style = "hidden";
		@endphp
	@endif
	
	
	@component('components.widget', ['class' => 'box-solid'])
	<div @if ($total >= $total2) {{"hidden"}} @endif class="row">
		<div class="col-md-6">
			<div class="form-group">
			  {!! Form::label('location_id', __('purchase.business_location').':*') !!}
			  {!! Form::select('location_id', $business_locations, null, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'required', 'id' => 'location_id']); !!}
			</div>
		</div>
		<div @if ($total >= $total2) {{"hidden"}} @endif class="col-md-6">
			<div class="form-group">
			  {!! Form::label('store_id', __('warehouse.nameW').':*') !!}
			  {!! Form::select('store_id', $Warehouse_list, null, ['class' => 'form-control select2', 'name' => "store_id", 'placeholder' => __('messages.please_select'), 'required', 'id' => 'store_id']); !!}
			  {!! Form::text('transaction_id', $transaction->id , ["hidden", 'name' => "transaction_id",   'id' => 'transaction_id']); !!}
			</div>
		  </div>
	  </div>
		 
	<div class="col-md-12" @if ($total >= $total2) {{"hidden"}} @endif>
	@if(!empty($transaction->contact))
		<strong>@lang('lang_v1.recieved_item'):</strong> 
		{!! Form::hidden('advance_balance', $transaction->contact->balance, ['id' => 'advance_balance', 'data-error-msg' => __('lang_v1.required_advance_balance_not_available')]); !!}
	@endif
	</div>
	 
	<div class="col-md-12">
		
		 
		<div class="form-group">
			<div class="input-group">
				<div class="input-group-btn">
					<button type="button" class="btn btn-default bg-white btn-flat" data-toggle="modal" data-target="#configure_search_modal" title="{{__('lang_v1.configure_product_search')}}"><i class="fa fa-barcode"></i></button>
				</div>
				{!! Form::text('search_product', null, ['class' => 'form-control mousetrap', 'id' => 'search_product', 'placeholder' => __('lang_v1.search_product_placeholder')]); !!}
			 
			</div>
		</div>
	</div>

	<div class="row col-sm-12 pos_product_div" style="min-height: 0">

		<input type="hidden" name="sell_price_tax" id="sell_price_tax" value="{{$business_details->sell_price_tax}}">

		<!-- Keeps count of product rows -->
		<input type="hidden" id="product_row_count" 
			value="0">
		@php
			$hide_tax = '';
			if( session()->get('business.enable_inline_tax') == 0){
				$hide_tax = 'hide';
			}
		@endphp



		{{-- table for products  used for  direct sell --}}
		<div class="table-responsive">
		<table class="table table-condensed table-bordered table-striped table-responsive" id="pos_table">
			<thead>
				<tr>
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
						@lang('sale.unit_price')
					</th>

					<th @can('edit_product_discount_from_sale_screen') hide @endcan>
						@lang('receipt.discount')
					</th>

					<th class="text-center {{$hide_tax}}">
						@lang('sale.tax')
					</th>

					<th class="text-center {{$hide_tax}}">
						@lang('sale.price_inc_tax')
					</th>

					@if(!empty($warranties))
						<th>@lang('lang_v1.warranty')</th>
					@endif

					<th class="text-center">
						@lang('sale.subtotal')
					</th>

					<th class="text-center"><i class="fas fa-times" aria-hidden="true"></i></th>
				</tr>
			</thead>
			<tbody></tbody>
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
					<b>@lang('sale.total'): </b>
						<span class="price_total">0</span>
					</div>
				</td>
			</tr>
		</table>
		</div>
	</div>
@endcomponent
		
 
	{{-- @component('components.widget', ['class' => 'box-primary'   ])
	<div @if ($total >= $total2) {{"hidden"}} @endif class="row">
		<div class="col-md-6">
			<div class="form-group">
			  {!! Form::label('location_id', __('purchase.business_location').':*') !!}
			  {!! Form::select('location_id', $business_locations, null, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'required', 'id' => 'location_id']); !!}
			</div>
		  </div>
		<div @if ($total >= $total2) {{"hidden"}} @endif class="col-md-6">
			<div class="form-group">
			  {!! Form::label('store_id', __('warehouse.nameW').':*') !!}
			  {!! Form::select('store_id', $Warehouse_list, null, ['class' => 'form-control select2', 'name' => "store_id", 'placeholder' => __('messages.please_select'), 'required', 'id' => 'store_id']); !!}
			  {!! Form::text('transaction_id', $transaction->id , ["hidden", 'name' => "transaction_id",   'id' => 'transaction_id']); !!}
			</div>
		  </div>
	  </div>
	<div class="row" @if ($total >= $total2) {{"hidden"}} @endif>
		<div class="col-md-12">
		@if(!empty($transaction->contact))
			<strong>@lang('lang_v1.recieved_item'):</strong> 
			{!! Form::hidden('advance_balance', $transaction->contact->balance, ['id' => 'advance_balance', 'data-error-msg' => __('lang_v1.required_advance_balance_not_available')]); !!}
		@endif
		</div>
	</div>
		<div class="row" @if ($total >= $total2) {{"hidden"}} @endif >
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
		<div class="row" @if ($total >= $total2) {{"hidden"}} @endif >
			<div class="col-sm-12">
				<div class="table-responsive">
					<table class="table table-condensed table-bordered table-th-green text-center table-striped" id="purchase_entry_table">
						<thead>
							<tr>
								<th>#</th>
								<th>@lang( 'product.product_name' )</th>
								<th>@lang( 'purchase.purchase_quantity' )</th>
								<th>@lang( 'lang_v1.unit_cost_before_discount' )</th>
								<th>@lang( 'lang_v1.discount_percent' )</th>
								<th>@lang( 'purchase.unit_cost_before_tax' )</th>
								<th class="{{$hide_tax}}">@lang( 'purchase.subtotal_before_tax' )</th>
								<th class="{{$hide_tax}}">@lang( 'purchase.product_tax' )</th>
								<th class="{{$hide_tax}}">@lang( 'purchase.net_cost' )</th>
								<th>@lang( 'purchase.line_total' )</th>
								<th class="@if(!session('business.enable_editing_product_from_purchase')) hide @endif">
									@lang( 'lang_v1.profit_margin' )
								</th>
								<th>
									@lang( 'purchase.unit_selling_price' )
									<small>(@lang('product.inc_of_tax'))</small>
								</th>
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
				<div class="pull-right col-md-5">
					<table class="pull-right col-md-12">
						<tr>
							<th class="col-md-7 text-right">@lang( 'lang_v1.total_items' ):</th>
							<td class="col-md-5 text-left">
								<span id="total_quantity" class="display_currency" data-currency_symbol="false"></span>
							</td>
						</tr>
						<tr class="hide">
							<th class="col-md-7 text-right">@lang( 'purchase.total_before_tax' ):</th>
							<td class="col-md-5 text-left">
								<span id="total_st_before_tax" class="display_currency"></span>
								<input type="hidden" id="st_before_tax_input" value=0>
							</td>
						</tr>
						<tr>
							{{-- <th class="col-md-7 text-right">@lang( 'purchase.net_total_amount' ):</th>
							<td class="col-md-5 text-left">
								<span id="total_subtotal" class="display_currency"></span>
								<!-- This is total before purchase tax-->
								<input type="hidden" id="total_subtotal_input" value=0  name="total_before_tax">
							</td>  
					</table>
				</div>

				<input type="hidden" id="row_count" value="0">
			</div>
		</div>
	@endcomponent --}}

	<div @if ($total >= $total2) {{"hidden"}} @endif class="modal-footer">
		<button type="submit"  class="btn btn-primary">@lang( 'messages.save' )</button>
		{{-- <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button> --}}
	  </div>
  
	  {!! Form::close() !!}



</section>
<!-- quick product modal -->
<div class="modal fade quick_add_product_modal" tabindex="-1" role="dialog" aria-labelledby="modalTitle"></div>
<div class="modal fade contact_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
	@include('contact.create', ['quick_add' => true])
</div>
<!-- /.content -->
@endsection

@section('javascript')
	<script src="{{ asset('js/posss.js?v=' . $asset_v) }}"></script>
	<script src="{{ asset('js/producte.js?v=' . $asset_v) }}"></script>
	<script src="{{ asset('js/opening_stock.js?v=' . $asset_v) }}"></script>
 
	<script type="text/javascript">
		function get_purchase_entry_row(product_id, variation_id) {
			if (product_id) {
				var row_count = $('#row_count').val();
				var location_id = $('#location_id').val();
				$.ajax({
					method: 'POST',
					url: '/purchases/get_purchase_entry_row',
					dataType: 'html',
					data: { 
						product_id: product_id, 
						row_count: row_count, 
						variation_id: variation_id,
						location_id: location_id
					},
					success: function(result) {
						.find('.purchase_quantity')
						.each(function() {
							row = $(this).closest('tr');
							 
							
								$('#purchase_entry_table tbody').append(
									update_purchase_entry_row_values(row)
								);
								// $("<th>store</th>").insertBefore("#purchase_entry_table thead  tr th:nth-child(11)");
								var array = [];
								var index_array = "<option value='";
								$("#store_id option").each(function () {
									array.push($(this).html());
								});
								// alert(array);
								var start  = 0;
								var count  = array.length;
								array.forEach(element => {
									if(start == count-1){
										index_array = index_array + element + "'>" + element + "</option>"
										start = start + 1 ; 
									
									}else{
										index_array = index_array + element + "'>" + element + "</option><option value='"
										start = start + 1 ; 
									}
								});

								var index = "<td><select name='store_sub' id='store_sub' class='form-control select2'>"+ index_array + "</select></td>";
								$(index).insertBefore("#purchase_entry_table tbody  tr td:nth-child(11)");
								// $("#purchase_entry_table tbody  td:nth-child(12)").hide();
								$("#purchase_entry_table tbody  td:nth-child(13)").hide();
								update_row_price_for_exchange_rate(row);

								update_inline_profit_percentage(row);

								update_table_total();
								update_grand_total();
								update_table_sr_number();

								//Check if multipler is present then multiply it when a new row is added.
								if(__getUnitMultiplier(row) > 1){
									row.find('select.sub_unit').trigger('change');
								}
							});
						if ($(result).find('.purchase_quantity').length) {
							$('#row_count').val(
								$(result).find('.purchase_quantity').length + parseInt(row_count)
							);
						}
					},
				});
			}
		}

		$(document).ready( function(){
      		__page_leave_confirmation('#add_purchase_form');
      		$('.paid_on').datetimepicker({
                format: moment_date_format + ' ' + moment_time_format,
                ignoreReadonly: true,
            });
    	});

		// setInterval(() => {
		// 	alert($("#purchase_quantity").html());
		// }, 10000);

		$("<th>Store<th>").insertBefore("#purchase_entry_table thead  tr th:nth-child(11)");
		$("#purchase_entry_table thead th:nth-child(4)").hide();
		$("#purchase_entry_table thead th:nth-child(5)").hide();
		$("#purchase_entry_table thead th:nth-child(6)").hide();
		$("#purchase_entry_table thead th:nth-child(12)").hide();
		$("#purchase_entry_table thead th:nth-child(10)").hide();
		// $("#purchase_entry_table thead th:nth-child(9)").hide();
		$("#purchase_entry_table thead th:nth-child(14)").hide();
		$("#purchase_entry_table thead th:nth-child(13)").hide();
		setInterval(() => {
			// $("#purchase_entry_table tbody tr td:nth-child(2)").hide();
			$("#purchase_entry_table tbody  td:nth-child(4)").hide();
			$("#purchase_entry_table tbody  td:nth-child(5)").hide();
			$("#purchase_entry_table tbody  td:nth-child(6)").hide();
			$("#purchase_entry_table tbody  td:nth-child(12)").hide();
			$("#purchase_entry_table tbody  td:nth-child(10)").hide();
			$("#purchase_entry_table tbody  td:nth-child(12)").hide();
			
		}, 100);
		// $("#purchase_entry_table thead  tr").append("<td>store</td>");
		
		

		// $("#purchase_entry_table tbody td:nth-child(5)").hide();
		// $("#purchase_entry_table tbody td:nth-child(6)").hide();
		// $("#purchase_entry_table tbody td:nth-child(12)").hide();
		// $("#purchase_entry_table tbody td:nth-child(11)").hide();
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
	</script>
	@include('purchase.partials.keyboard_shortcuts')
@endsection
