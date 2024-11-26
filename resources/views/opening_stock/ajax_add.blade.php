<div class="modal-dialog modal-xl" role="document">
	<div class="modal-content">
	{{-- {!! Form::open(['url' => action('OpeningStockController@save'), 'method' => 'post', 'id' => 'add_opening_stock_form' ]) !!}
	{!! Form::hidden('product_id', $product->id); !!}
		<div class="modal-header">
		    <button type="button" class="close no-print" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		      <h4 class="modal-title" id="modalTitle">@if(empty($purchases)) @lang('lang_v1.add_opening_stock') @else @lang('lang_v1.update_opening_stock') @endif</h4>
	    </div>
			<div class="container">
				<div class="row">
					<div class="col-md-12 hide">
						<div class="form-group">
							{!! Form::label('store_id', __('warehouse.warehouse').':*') !!}
							
							{!! Form::select('store_id', $mainstore_categories, null, ['class' => 'form-control select2', 'required'], $bl_attributes); !!}
						</div>
					</div>
				</div>
			</div>
	
	    <div class="modal-body">
			@include('opening_stock.form-part')
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-primary" id="add_opening_stock_btn">@lang('messages.save')</button>
		    <button type="button" class="btn btn-default no-print" data-dismiss="modal">@lang( 'messages.close' )</button>
		 </div>
	{!! Form::close() !!} --}}
	<div class="modal-header">
		<button type="button" class="close no-print" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		  <h4 class="modal-title" id="modalTitle"> @lang('lang_v1.opening_stock')</h4>
	</div>
		<div class="container">
			<div class="row">
				<table class="table table-condensed table-bordered table-th-green text-center table-striped" id="purchase_entry_table">
					<thead>
						<tr>
							<th>#</th>
							<th>@lang( 'home.quantity' )</th>
							<th>@lang( 'lang_v1.price' )</th>
							<th>@lang( 'home.Store' )</th>
							<th>@lang( 'home.Total' )</th>
						</tr>
					</thead>
					<tbody>
						@php
						    $total_item   = 0  ;
						    $total_amount = 0  ;
						@endphp
						@foreach ($product->opening_quantites as $key=>$item)
						@php
						    $total_item = $total_item + $item->quantity  ;
						    $total_amount = $total_amount + $item->price * $item->quantity;
						@endphp
							<tr>
								<td><button type="button" class="btn btn-link btn-modal"
									data-href="{{action('ProductController@ViewOpeningProduct', [$item->id])}}" data-container=".view_modal">{{ $item->transaction?$item->transaction->ref_no:'--' }}</button></td>
								<td>{{ $item->quantity }}</td>
								<td>{{ $item->price }}</td>
								<td>{{ $item->store?$item->store->name:'--' }}</td>
								<td>{{ $item->price*$item->quantity }}</td>

							</tr>
						@endforeach
					</tbody>
					<tfoot>
					    <tr>
					        <td colspan="1"></td>
					        <td >{{$total_item}}</td>
					        <td colspan="2"></td>
					        <td >{{$total_amount}}</td>
					    </tr>
					</tfoot>
				</table>
				<div class="button-close-open">
						@lang("messages.close")
				</div>
			</div>
		</div>
	</div>
</div>
 
 
<script src="{{ asset('js/opening_stock.js?v=' . $asset_v) }}"></script>


 
 
 
