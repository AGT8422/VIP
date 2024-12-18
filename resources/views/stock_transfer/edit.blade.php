@extends('layouts.app')
@section('title', __('lang_v1.edit_stock_transfer'))

@section('content')
	
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('lang_v1.edit_stock_transfer')</h1>
</section>
 
<!-- Main content -->
<section class="content no-print">
	{!! Form::open(['url' => action('StockTransferController@update', [$sell_transfer->id]), 'method' => 'put', 'id' => 'stock_transfer_form' ]) !!}
	<div class="box box-solid">
		<div class="box-body">
			<div class="row">
				<div class="col-sm-4">
					<div class="form-group">
						{!! Form::label('transaction_date', __('messages.date') . ':*') !!}
						<div class="input-group">
							<span class="input-group-addon">
								<i class="fa fa-calendar"></i>
							</span>
							{!! Form::text('transaction_date', @format_datetime($sell_transfer->transaction_date), ['class' => 'form-control', 'readonly', 'required']); !!}
						</div>
					</div>
				</div>
				<div class="col-sm-4">
					<div class="form-group">
						{!! Form::label('ref_no', __('purchase.ref_no').':') !!}
						{!! Form::text('ref_no', $sell_transfer->ref_no, ['class' => 'form-control', 'readonly']); !!}
					</div>
				</div>
				@if($purchase_transfer->status == "completed"  )  @php $hi = ""; $hide = "hide";  $type= "disabled";  @endphp  @else @php $hide = " ";  $type= ""; $hi = "hide"; @endphp @endif  
				<div class="col-sm-4 {{$hide}}">
					<div class="form-group">
						{!! Form::label('status', __('sale.status').':*') !!} @show_tooltip(__('lang_v1.completed_status_help'))
						{!! Form::select('status', $statuses, $purchase_transfer->status, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'),  'id' => 'status']); !!}
 					</div>
				</div>
				<div class="col-sm-4 {{$hi}}">
					<div class="form-group">
						{!! Form::label('status_', __('sale.status').':*') !!} @show_tooltip(__('lang_v1.completed_status_help'))
 						{!! Form::text('status_', $statuses[$purchase_transfer->status],  ['class' => 'form-control  ', 'placeholder' => __('messages.please_select'), $type ,   'id' => 'status_']); !!}
					</div>
				</div>
				<div class="clearfix"></div>
				<div class="col-sm-6 hide">
					<div class="form-group">
						{!! Form::label('location_id', __('warehouse.nameWFrom').':*') !!}
						{!! Form::select('location_id', $mainstore_categories, $sell_transfer->store  , ['class' => 'form-control select2  ', 'placeholder' => __('messages.please_select'), 'id' => 'location_id'   ]); !!}
					</div>
				</div>
				<div class="col-sm-6  ">
					<div class="form-group">
						{!! Form::label('location_id', __('warehouse.nameWFrom').':*') !!}
						{!! Form::text('location_id_old',  $mainstore_categories[$sell_transfer->store]    , ['class' => 'form-control  ',"disabled", 'placeholder' => __('messages.please_select'), 'id' => 'location_id_old'   ]); !!}
					</div>
				</div>
				<div class="col-sm-6 ">
					<div class="form-group">
						{!! Form::label('transfer_location_id', __('warehouse.nameWTo').':*') !!} 
						{!! Form::select('transfer_location_id', $mainstore_categories, $purchase_transfer->transfer_parent_id , ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'id' => 'transfer_location_id']); !!}
					</div>
				</div>
				
			</div> 
		</div>
	</div> <!--box end-->
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
							{!! Form::text('search_product', null, ['class' => 'form-control', 'id' => 'search_product_for_srock_adjustment', 'placeholder' => __('stock_adjustment.search_product')]); !!}
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-10 col-sm-offset-1">
					<div class="table-responsive">
					<table class="table table-bordered table-striped table-condensed" 
					id="stock_adjustment_product_table">
						<thead>
							<tr>
								<th class="col-sm-4 text-center">	
									@lang('sale.product')
								</th>
								<th class="col-sm-3 text-center">
									@lang('sale.qty')
								</th>
								<th class="col-sm-3 text-center">
									@lang('sale.available')
								</th>
								<th class="col-sm-2 text-center"><i class="fa fa-trash" aria-hidden="true"></i></th>
							</tr>
						</thead>
						<tbody>
							@php
								$product_row_index = 0;
								$subtotal = 0;
 							@endphp
							@foreach($products as $product)
							@php
							//  dd($sell_transfer->store)
							@endphp
								@include('stock_transfer.partials.product_table_row', ['product' => $product,'store' => $sell_transfer->store, 'row_index' => $loop->index , "quantity"  => $quantity,"store_in" => $purchase_transfer->transfer_parent_id, "status"=>$purchase_transfer->status ])
								@php
									$product_row_index = $loop->index + 1;
									$subtotal += ($product->quantity_ordered*$product->last_purchased_price);
								@endphp
							@endforeach
						</tbody>
						<tfoot>
							<tr class="text-center"><td colspan="3"></td><td><div class="pull-right"><b>@lang('stock_adjustment.total_amount'):</b> <span id="total_adjustment_">{{@num_format($subtotal)}}</span></div></td></tr>
						</tfoot>
					</table>
					<input type="hidden" id="product_row_index" value="{{$product_row_index}}">
					<input type="hidden" id="total_amount" name="final_total" value="{{$subtotal}}">
					</div>
				</div>
			</div>
		</div>
	</div> <!--box end-->
	<div class="box box-solid">
		<div class="box-body">
			<div class="row">
				{{-- <div class="col-sm-4">
					<div class="form-group">
							{!! Form::label('shipping_charges', __('lang_v1.shipping_charges') . ':') !!}
							{!! Form::text('shipping_charges', @num_format($sell_transfer->shipping_charges), ['class' => 'form-control input_number', 'placeholder' => __('lang_v1.shipping_charges')]); !!}
					</div>
				</div> --}}
				<div class="col-sm-12">
					<div class="form-group">
						{!! Form::label('additional_notes',__('purchase.additional_notes')) !!}
						{!! Form::textarea('additional_notes', $sell_transfer->additional_notes, ['class' => 'form-control', 'rows' => 3]); !!}
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-12">
					<button type="submit" id="save_stock_transfer" class="btn btn-primary pull-right">@lang('messages.save')</button>
				</div>
			</div>

		</div>
	</div> <!--box end-->
	{!! Form::close() !!}
</section>
@stop
@section('javascript')
	<script src="{{ asset('js/stock_transfers.js?v=' . $asset_v) }}"></script>
	<script type="text/javascript">
		__page_leave_confirmation('#stock_transfer_form');
		$('#stock_adjustment_product_table tbody').sortable({
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
	</script>
@endsection
