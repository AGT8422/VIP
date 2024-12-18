@extends('layouts.app')
@section('title', __('purchase.add_recieved'))



@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('purchase.add_recieved') <i class="fa fa-keyboard-o hover-q text-muted" aria-hidden="true" data-container="body" data-toggle="popover" data-placement="bottom" data-content="@include('purchase.partials.keyboard_shortcuts_details')" data-html="true" data-trigger="hover" data-original-title="" title=""></i></h1>
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
        <div class="col-md-6">
            <div class="form-group">
              {!! Form::label('location_id', __('lang_v1.loaction_addres').':*') !!}
              {!! Form::select('location_id', $business_locations, null, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'required', 'id' => 'location_id']); !!}
            </div>
          </div>
        <div class="col-md-6">
            <div class="form-group">
              {!! Form::label('store_id', __('warehouse.nameW').':*') !!}
              {!! Form::select('store_id', $Warehouse_list, null, ['class' => 'form-control select2', 'name' => "store_id", 'placeholder' => __('messages.please_select'), 'required', 'id' => 'store_id']); !!}
              {{-- {!! Form::text('transaction_id', $transaction->id , ["hidden", 'name' => "transaction_id",   'id' => 'transaction_id']); !!} --}}
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
				<strong>@lang('lang_v1.need_balance_items'):</strong> 
			</div>
		  </div>


		  <div class="row">
			<div class="col-md-12">
					<div class="table-responsive">
						<table class="table table-recieve" >
						<tr style="background:#f1f1f1;">
						  <th>@lang('messages.date')</th>
						  <th>@lang('product.product_name')</th>
						  <th>@lang('purchase.amount')</th>
						  <th>@lang('purchase.delivery_status')</th>
						  <th>@lang('warehouse.nameW')</th>
						  <th>@lang('purchase.payment_note')</th>
						  {{-- <th class="no-print">@lang('messages.actions')</th> --}}
						</tr>
						@php
						  // dd($transaction);
						  $total2 = 0;
						@endphp
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
			  {{-- @if(!empty($transaction->contact)) --}}
				<strong>@lang('lang_v1.old_recieved'):</strong> 
			  {{-- @endif --}}
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
						  <th>@lang('purchase.amount_total')</th>
						  <th>@lang('purchase.amount_current')</th>
						  <th>@lang('purchase.amount_remain')</th>
						  <th>@lang('warehouse.nameW')</th>
						  <th>@lang('purchase.payment_note')</th>
						  {{-- <th class="no-print">@lang('messages.actions')</th> --}}
						</tr>
						@php
						  $total = 0;
						@endphp
						{{-- @forelse ($RecievedPrevious as $Recieved)
	
						@php
							$total = $total + $Recieved->current_qty;
							// $product_name = "";
							// $counter = 1; 
							// foreach($product_list as $product_l){
							// 	dd( $product_l);
							// 	if($Recieved->product_name == $product_l ){
							// 		$product_name = $product_l;
							// 	}
							// 	$counter = $counter + 1 ;
							// }
							// dd( $Recieved);
							  
							// }
							$Warehouse_name = "";
							$counter_1 = 1; 
							foreach($Warehouse_list as $Warehouse_l){
							  if($Recieved->store_id == $counter_1 ){
							    $Warehouse_name = $Warehouse_l;
							  }
							  $counter_1 = $counter_1 + 1 ;
							}
							  
							  // dd($Warehouse_list);
							
	
							// dd($transaction);
						@endphp 
							<tr style="border:1px solid #f1f1f1;">
							  <td>{{$payment->created_at}}</td>
							  <td>{{$product_name}}</td>
							  <td>{{$Recieved->unit_id}}</td>
							  <td>{{$Recieved->total_qty}}</td>
							  <td>{{$Recieved->current_qty}}</td>
							  <td>{{$Recieved->remain_qty}}</td>
							  <td>{{$Warehouse_name}}</td>
							  <td></td>
							</tr>
							
						@empty --}}
						
						{{-- @endforelse --}}
						<tfoot>
						  <tr class="bg-gray  font-17 footer-total" style="border:1px solid #f1f1f1;  ">
							<td class="text-center " colspan="2"><strong>@lang('sale.total'):</strong></td>
							<td>{{$total}}</td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
					   
						  </tr>
					   </tfoot>
						</table>
				</div>
			</div>
		</div>

	@endcomponent

    
	@component('components.widget', ['class' => 'box-primary'])
	<div class="row" >
		<div class="col-md-12">
		{{-- @if(!empty($transaction->contact)) --}}
			<strong>@lang('lang_v1.recieved_item'):</strong> 
			{{-- {!! Form::hidden('advance_balance', $transaction->contact->balance, ['id' => 'advance_balance', 'data-error-msg' => __('lang_v1.required_advance_balance_not_available')]); !!} --}}
		{{-- @endif --}}
		</div>
	</div>
		<div class="row" >
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
		<div class="row" >
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
							</td> --}}
						</tr>
					</table>
				</div>

				<input type="hidden" id="row_count" value="0">
			</div>
		</div>
	@endcomponent

	<div class="modal-footer">
		<button type="submit"  class="btn btn-primary">@lang( 'messages.save' )</button>
		<button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
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
	<script src="{{ asset('js/purchase.js?v=' . $asset_v) }}"></script>
	<script src="{{ asset('js/producte.js?v=' . $asset_v) }}"></script>
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
						$(result)
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
