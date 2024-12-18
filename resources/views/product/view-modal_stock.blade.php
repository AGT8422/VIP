<div class="modal-dialog modal-xl" role="document">
	<div class="modal-content">
		<div class="modal-header">
		    <button type="button" class="close no-print" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		      <h4 class="modal-title" id="modalTitle">{{$product->name}}</h4>
	    </div>
	    <div class="modal-body">
      		<div class="row">
      			<div class="col-sm-9">
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
	      			<div class="clearfix"></div>
	      			<br>
      				<div class="col-sm-12">
      					{!! $product->product_description !!}
      				</div>
	      		</div>
      			<div class="col-sm-2 col-md-2 invoice-col">
      				<div class="thumbnail">
      					<img src="{{$product->image_url}}" alt="Product image">
      				</div>
      			</div>
      		</div>
      		@if($rack_details->count())
      		@if(session('business.enable_racks') || session('business.enable_row') || session('business.enable_position'))
      			<div class="row">
      				<div class="col-md-8">
      					<h4>@lang('lang_v1.rack_details'):</h4>
      				</div>
      				<div class="col-md-8">
      					<div class="table-responsive">
      					<table class="table table-condensed bg-gray">
      						<tr class="bg-green">
      							<th>@lang('business.location')</th>
      							@if(session('business.enable_racks'))
      								<th>@lang('lang_v1.rack')</th>
      							@endif
      							@if(session('business.enable_row'))
      								<th>@lang('lang_v1.row')</th>
      							@endif
      							@if(session('business.enable_position'))
      								<th>@lang('lang_v1.position')</th>
      							@endif
      							</tr>
      						@foreach($rack_details as $rd)
      							<tr>
	      							<td>{{$rd->name}}</td>
	      							@if(session('business.enable_racks'))
	      								<td>{{$rd->rack}}</td>
	      							@endif
	      							@if(session('business.enable_row'))
	      								<td>{{$rd->row}}</td>
	      							@endif
	      							@if(session('business.enable_position'))
	      								<td>{{$rd->position}}</td>
	      							@endif
      							</tr>
      						@endforeach
      					</table>
      					</div>
      				</div>
      			</div>
      		@endif
      		@endif
      		@if($product->type == 'single')
      			@include('product.partials.single_product_details')
      		@elseif($product->type == 'variable')
      			@include('product.partials.variable_product_details')
      		@elseif($product->type == 'combo')
      			@include('product.partials.combo_product_details')
      		@endif
      		@if($product->enable_stock == 1)
	      		<div class="row">
	      			<div class="col-md-12">
	      				<strong>@lang('lang_v1.product_stock_details')</strong>
	      			</div>
	      			<div class="col-md-12" id="view_product_stock_details" data-product_id="{{$product->id}}">
						  
						 <br>
						<div class="row">

							<div class="col-md-12">

								<strong>@lang("product.WareHouses")</strong>
								<br>
								<div class="table-responsive">
									<table class="table table-condensed bg-black">
										<thead>
											<th>@lang("#")</th>
											<th>@lang("warehouse.nameW")</th>
											{{-- <th>@lang("product.product_name")</th> --}}
											<th>@lang("lang_v1.quantity")</th>
										</thead>
										<tbody>
											@php
													$count = 1 ;
											@endphp
											@forelse($warehouseInfo_all as $ware)
												@php
													 $store_name = $list[$ware->store_id];
													 $quantity =  $ware->product_qty ;
												@endphp
												<tr  style="background:#f1f1f1; color:black">
													<td>{{$count}}</td>	
													<td>{{$store_name}}</td>	
													<td>{{$quantity}}</td>	
												</tr>	
												@php
													$count++ ;
												@endphp
											@empty
											
											@endforelse
										</tbody>
									</table>
								</div>
							</div>

							<div class="col-md-6">
								<strong  >@lang("recieved.all_recieved")</strong>
								<div>&nbsp;</div>
								<div class="table-responsive">
									
									<table class="table table-condensed bg-black">
										<thead>
											<th>@lang("#")</th>
											<th>@lang("purchase.ref_no")</th>
											<th>@lang("purchase.ref_no_p")</th>
											<th>@lang("lang_v1.quantity")</th>
										</thead>
										<tbody>
											@php
													$count = 1 ;
											@endphp
											@forelse($RecievedPrevious as $RPrevious)
												@php
													// dd($list_purchase);
													 $reciept_id =  $RPrevious->id ;
													 $reciept = $list_trans[$RPrevious->transaction_deliveries_id];
													 $ref = $list_purchase[$RPrevious->transaction_deliveries_id];
													 $quantity =  $RPrevious->current_qty ;
												@endphp
												<tr  style="background:#f1f1f1; color:black">
													<td>{{$count}}</td>	
													<td><button type="button" class="btn btn-link btn-modal"
														data-href="{{route('viewRecieve_ref', [$reciept_id])}}" data-container=".view_modal">{{$reciept}}</button></td>	
													<td><button type="button" class="btn btn-link btn-modal"
														data-href="{{action('PurchaseController@show', [$RPrevious->transaction_id])}}" data-container=".view_modal">{{$ref}}</button></td>	
													<td>{{$quantity}}</td>	
												</tr>	
												@php
													$count++ ;
												@endphp
											@empty
											
											@endforelse
										</tbody>
									</table>
								</div>
								
							</div>

							<div class="col-md-6">
								<div class="table-responsive">
									<strong>@lang("recieved.all_delivered")</strong><div>&nbsp;</div>
									<table class="table table-condensed bg-black">
										<thead>
											<th>@lang("#")</th>
											<th>@lang("purchase.ref_no")</th>
											<th>@lang("sale.invoice_no")</th>
											<th>@lang("lang_v1.quantity")</th>
										</thead>
										<tbody>
											@php
													 
													$count = 1 ;
											@endphp
											@forelse($DeliveredPrevious as $DPrevious)
												@php
												 
													 $reciept_id = $DPrevious->id;
													 $reciept = $list_trans_d[$DPrevious->transaction_recieveds_id];
													 $ref = $list_sell[$DPrevious->transaction_recieveds_id];
													 $quantity =  $DPrevious->current_qty ;
												@endphp
													<tr  style="background:#f1f1f1; color:black">
														<td>{{$count}}</td>	  
														<td><button type="button" class="btn btn-link btn-modal"
															data-href="{{route('viewDelivered', [$reciept_id])}}" data-container=".view_modal">{{$reciept}}</button></td>	
														<td><button type="button" class="btn btn-link btn-modal"
															data-href="{{action('SellController@show', [$DPrevious->transaction_id])}}" data-container=".view_modal">{{$ref}}</button></td>	
														<td>{{$quantity}}</td>	
													</tr>	
												@php
													$count++ ;
												@endphp
											@empty
											
											@endforelse
										</tbody>
									</table>
								</div>
								
							</div>

						
							{{-- <div class="col-md-6">
								<div class="table-responsive">
									<strong>@lang("recieved.supplier")</strong><div>&nbsp;</div>
									<table class="table table-condensed bg-black">
										<thead>
											<th>@lang("#")</th>
											<th>@lang("contact.name")</th>
											<th>@lang("contact.mobile")</th>
											<th>@lang("lang_v1.quantity")</th>
										</thead>
										<tbody>
											@php
													$count = 1 ;
											@endphp
											@forelse($supplier as $supp)
												@php
											 
												@endphp
												<tr  style="background:#f1f1f1; color:black">
											 
												@php
													 
												@endphp
											@empty
											
											@endforelse
										</tbody>
									</table>
								</div>
							</div> --}}

							{{-- <div class="col-md-6">
								<div class="table-responsive">
									<strong>@lang("recieved.customer")</strong><div>&nbsp;</div>
									<table class="table table-condensed bg-black">
										<thead>
											<th>@lang("#")</th>
											<th>@lang("contact.name")</th>
											<th>@lang("contact.mobile")</th>
											<th>@lang("lang_v1.quantity")</th>
										</thead>
										<tbody>
											@php
													$count = 1 ;
											@endphp
											@forelse($supplier as $supp)
												@php
													 
												@endphp
												<tr  style="background:#f1f1f1; color:black">
												 
												</tr>	
												@php
													 
												@endphp
											@empty
											
											@endforelse
										</tbody>
									</table>
								</div>
								
							</div> --}}

							{{-- <div class="col-md-12">
								<div class="table-responsive">
									<strong>@lang("recieved.Booking")</strong><div>&nbsp;</div>
									<table class="table table-condensed bg-black">
										<thead>
											<th>@lang("#")</th>
											<th>@lang("contact.name")</th>
											<th>@lang("contact.mobile")</th>
											<th>@lang("lang_v1.quantity")</th>
										</thead>
										<tbody>
											@php
													$count = 1 ;
											@endphp
											@forelse($supplier as $supp)
												@php
										 
												@endphp
												<tr  style="background:#f1f1f1; color:black">
											 
												</tr>	
												@php
													 
												@endphp
											@empty
											
											@endforelse
										</tbody>
									</table>
								</div>
								
							</div> --}}

						</div>
							  
					</div>
	      		</div>
      		@endif
      	</div>
 
  
      	<div class="modal-footer">
      		<button type="button" class="btn btn-primary no-print" 
	        aria-label="Print" 
	          onclick="$(this).closest('div.modal').printThis();">
	        <i class="fa fa-print"></i> @lang( 'messages.print' )
	      </button>
	      	<button type="button" class="btn btn-default no-print" data-dismiss="modal">@lang( 'messages.close' )</button>
	    </div>
	</div>
</div>
