<div class="modal-dialog modal-xl" role="document">
	<div class="modal-content">
		@if(!isset($basic))
		<div class="modal-header">
		    <button type="button" class="close no-print" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		      <h4 class="modal-title" id="modalTitle">{{$product->name}}</h4>
	    </div>
		@endif
		{{-- Product Info  --}}
		@if(!isset($basic))
	    <div class="modal-body">
      		<div class="row">
      			<div class="col-sm-12">
					<div class="col-sm-4 invoice-col">
	      				<b>@lang('product.sku'):</b>
						{{$product->sku }}<br>
						<b>@lang('product.brand'): </b>
						{{$product->brand->name ?? '--' }}<br>
						<b>@lang('product.unit'): </b>
						{{$product->unit->short_name ?? '--' }}<br>
						<b>@lang('product.barcode_type'): </b>
						{{$product->barcode_type ?? '--' }}
						@php 
    						$custom_labels = json_decode(session('business.custom_labels'), true);
						@endphp
						@if(!empty($product->product_custom_field1))
							<br/>
							<b>{{ $custom_labels['product']['custom_field_1'] ?? __('lang_v1.product_custom_field1') }}: </b>
							{{$product->product_custom_field1 }}
						@endif

						@if(!empty($product->product_custom_field2))
							<br/>
							<b>{{ $custom_labels['product']['custom_field_2'] ?? __('lang_v1.product_custom_field2') }}: </b>
							{{$product->product_custom_field2 }}
						@endif

						@if(!empty($product->product_custom_field3))
							<br/>
							<b>{{ $custom_labels['product']['custom_field_3'] ?? __('lang_v1.product_custom_field3') }}: </b>
							{{$product->product_custom_field3 }}
						@endif

						@if(!empty($product->product_custom_field4))
							<br/>
							<b>{{ $custom_labels['product']['custom_field_4'] ?? __('lang_v1.product_custom_field4') }}: </b>
							{{$product->product_custom_field4 }}
						@endif
						<br>
						<strong>@lang('lang_v1.available_in_locations'):</strong>
						@if(count($product->product_locations) > 0)
							{{implode(', ', $product->product_locations->pluck('name')->toArray())}}
						@else
							@lang('lang_v1.none')
						@endif
						@if(!empty($product->media->first())) <br>
							<strong>@lang('lang_v1.product_brochure'):</strong>
							<a href="{{$product->media->first()->display_url}}" download="{{$product->media->first()->display_name}}">
								<span class="label label-info">
									<i class="fas fa-download"></i>
									{{$product->media->first()->display_name}}
								</span>
							</a>
						@endif
	      			</div>

	      			<div class="col-sm-4 invoice-col">
						<b>@lang('product.category'): </b>
						{{$product->category->name ?? '--' }}<br>
						<b>@lang('product.sub_category'): </b>
						{{$product->sub_category->name ?? '--' }}<br>	
						
						<b>@lang('product.manage_stock'): </b>
						@if($product->enable_stock)
							@lang('messages.yes')
						@else
							@lang('messages.no')
						@endif
						<br>
						@if($product->enable_stock)
							<b>@lang('product.alert_quantity'): </b>
							{{$product->alert_quantity ?? '--' }}
						@endif

						@if(!empty($product->warranty))
							<br>
							<b>@lang('lang_v1.warranty'): </b>
							{{$product->warranty->display_name }}
						@endif
	      			</div>
					
	      			<div class="col-sm-4 invoice-col">
	      				<b>@lang('product.expires_in'): </b>
	      				@php
	  						$expiry_array = ['months'=>__('product.months'), 'days'=>__('product.days'), '' =>__('product.not_applicable') ];
	  					@endphp
	      				@if(!empty($product->expiry_period) && !empty($product->expiry_period_type))
							{{$product->expiry_period}} {{$expiry_array[$product->expiry_period_type]}}
						@else
							{{$expiry_array['']}}
	      				@endif
	      				<br>
						@if($product->weight)
							<b>@lang('lang_v1.weight'): </b>
							{{$product->weight }}<br>
						@endif
						<b>@lang('product.applicable_tax'): </b>
						{{$product->product_tax->name ?? __('lang_v1.none') }}<br>
						@php
							$tax_type = ['inclusive' => __('product.inclusive'), 'exclusive' => __('product.exclusive')];
						@endphp
						<b>@lang('product.selling_price_tax_type'): </b>
						{{$tax_type[$product->tax_type]  }}<br>
						<b>@lang('product.product_type'): </b>
						@lang('lang_v1.' . $product->type)
						
				</div>  
			</div>
		</div>
		@endif
		{{-- Warehouse && QTY --}}
		<div class="modal-body">
			<div class="row">
					
				
				<div class="col-md-12">
					<h4>@lang('home.Store'):</h4>
				</div>
					<div class="col-md-12">
						<div class="table-responsive">
							<table class="table table-condensed bg-gray">
								<tr style="background:#f1f1f1;">
									<th>@lang('warehouse.nameW')</th>
									<th>@lang('purchase.amount_current')</th>
								</tr>
								
								@php $total = 0; @endphp
								@forelse ($warehouse_info as  $warhouse)
								{{-- @php dd($product->id); @endphp --}}
									@if($warhouse->product_id == $product->id && $warhouse->product_qty != 0)
										@php $total    = $total + $warhouse->product_qty ;@endphp 
									 
										<tr style="border:1px solid #ffffff;BACKGROUND:#fefefe;">
											<td>{{$warhouse->store->name}}</td>
											<td>{{$warhouse->product_qty}}</td>
										</tr>
									@endif
								@empty @endforelse
										
							</table>
							@php  $subT = $COST_FINAL * $total;  @endphp
							{{-- @if (!auth()->user()->can('product.avarage_cost')) --}}
								<div class=" text-center bg-gray  font-17 footer-total">
									<span class="text-right" style="float:left;">   
										<strong>@lang('sale.total')       :</strong>{{ $total }}
									</span>
								@if(auth()->user()->hasRole('Admin#' . session('business.id')) ||  auth()->user()->can('product.avarage_cost'))
									<span class="text-center" style="float:center;">
										<strong>@lang('purchase.cost')    : </strong>{{round($COST_FINAL,2)}}
									</span> 
									<span class="text-right" style="float:right;">
										<strong>@lang('home.total_cost')  : </strong>{{ round($subT,2) }}
									</span>
								 @endif
								</div>
							{{-- @endif --}}
							
						</div>
					</div>
					
					
					<div class="clearfix"></div>
					<br>
				</div>
			</div>
		</div>
		{{-- Sell Prices --}}
		{{-- @if (!auth()->user()->can('product.avarage_cost'))  --}}
		@can("all_sales_prices")
		<div class="modal-body">
			<div class="row">
				<div class="col-md-12">
						<div class="table-responsive">
							<table class="table table-condensed bg-gray">
								<thead>
									<tr style="background:#4e4e4e; color:white">
										<th>@lang("home.average_sell")</th>
										<th>@lang("home.maxPriceSell")</th>
										<th>@lang("home.minPriceSell")</th>
										<th>@lang("home.lastPriceSell")</th>
									</tr>
								</thead>
								<tbody>
									@include("product.partials.tableOfSell")
								</tbody>
							 
							</table>
						</div>
			
					</div>
				</div>
		</div>
		@endcan
		{{-- Purchase Prices --}}
		@if(auth()->user()->hasRole('Admin#' . session('business.id')) ||  auth()->user()->can('product.avarage_cost'))
 			<div class="modal-body">
			<div class="row">
				<div class="col-md-12">
					<div class="table-responsive">
						<table class="table table-condensed bg-gray" >
							<thead>
								<tr style="background:#4e4e4e; color:white">
									<th>@lang("home.average_purchase")</th>
									<th>@lang("home.maxPricePurchase")</th>
									<th>@lang("home.minPricePurchase")</th>
									<th>@lang("home.lastPricePurchase")</th>
								</tr>
							</thead>
							<tbody>
								@include("product.partials.tableOfPurchase")
							</tbody>
						</table>
					</div>
				</div>
			</div>
		@endif
		
		<div class="row">
			<div class="col-md-12">
				<div class="table-responsive">
					<table class="table table-condensed bg-gray">
							<thead>
								<th > @lang("Product Price :")</th>
								<th colspan="3"  class="display_currency" data-currency_symbol="true" >{{$price}}</th>
							</thead>
					</table>
				</div>
	
			</div>
		</div>
		
 		{{-- @endif --}}
		{{-- print && close bottons   --}}
		@if(!isset($basic))
		<div class="modal-footer">
			<button type="button" class="btn btn-primary no-print" 
				aria-label="Print" 
				onclick="$(this).closest('div.modal').printThis();">
				<i class="fa fa-print"></i> @lang( 'messages.print' )
			</button>
			<button type="button" class="btn btn-default no-print" data-dismiss="modal">@lang( 'messages.close' )</button>
		</div>
		@endif
	</div>
</div>
