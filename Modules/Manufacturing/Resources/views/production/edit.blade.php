	@extends('layouts.app')
	@section('title', __('manufacturing::lang.production'))

	@section('content')

	<!-- Content Header (Page header) -->
	<section class="content-header">
		<h1>@lang('manufacturing::lang.production') </h1>
	</section>

	<!-- Main content -->
	<section class="content">

		{!! Form::open(['url' => action('\Modules\Manufacturing\Http\Controllers\ProductionController@update', [$production_purchase->id]), 'method' => 'put', 'id' => 'production_form', 'files' => true ]) !!}
		@component('components.widget', ['class' => 'box-primary'])
			<div class="row">
				<div class="col-sm-3">
					<input type="hidden" name="type" id="type" value="edit">
					<div class="form-group">
						{!! Form::label('ref_no', __('purchase.ref_no').':') !!}
						{!! Form::text('ref_no', $production_purchase->ref_no, ['class' => 'form-control']); !!}
					</div>
				</div> 
				<div class="col-sm-3">
					<div class="form-group">
						{!! Form::label('transaction_date', __('manufacturing::lang.mfg_date') . ':*') !!}
						<div class="input-group">
							<span class="input-group-addon">
								<i class="fa fa-calendar"></i>
							</span>
							{!! Form::text('transaction_date', @format_datetime($production_purchase->transaction_date), ['class' => 'form-control', 'readonly', 'required']); !!}
						</div>
					</div> 
				</div>
				@if($store_out != null || $store_out != "")
				<div class="col-sm-3">
					<div class="form-group">
						{!! Form::label('location_id', __('home.Store Out').':*') !!}
						@show_tooltip(__('tooltip.purchase_location'))
						<input name="location_id" type="hidden" value="{{ $production_purchase->location_id }}" id="location_id">
						{!! Form::select('store_id', $stores,$store_out, ['class' => 'form-control hide','required','id'=>'store_id_e']); !!}
						{!! Form::text('tes', $stores[$store_out],  ['class' => 'form-control ','required','readOnly','id'=>'tes']); !!}
					</div>
				</div>
				@else
					<div class="col-sm-3">
						<div class="form-group">
							<div class="er" style="color:red;font-weight:bold;font-size:large;">
								@lang("lang_v1.Please_choose_mfg")
							</div>
						</div>
					</div>
				@endif
				<div class="col-sm-3">
					<div class="form-group">
						{!! Form::label('location_id', __('home.Store In').':*') !!}
						@show_tooltip(__('tooltip.purchase_location'))
						<input name="location_id" type="hidden" value="{{array_keys($business_locations->toArray())[0]}}" id="location_id">
						{!! Form::select('store_in', $stores,$production_purchase->store_in, ['class' => 'form-control ','required']); !!}
					</div>
				</div>
				@php
					$purchase_line = $production_purchase->purchase_lines[0];
				@endphp
				<div class="col-sm-3">
					<div class="form-group">
						{!! Form::label('variation_id_shown', __('sale.product').':*') !!}
						{!! Form::select('variation_id_shown', $recipe_dropdown, $purchase_line->variation_id, ['class' => 'form-control', 'placeholder' => __('messages.please_select'), 'required', 'disabled']); !!}
						{!! Form::hidden('variation_id', $purchase_line->variation_id, ['id' => 'variation_id']); !!}
					</div>
				</div>
				<div class="col-sm-3">
					<div class="form-group">
						{!! Form::label('recipe_quantity', __('lang_v1.quantity').':*') !!} 
						<div class="@if(!empty($sub_units)) input_inline @else input-group @endif" id="recipe_quantity_input">
							{!! Form::text('quantity', @num_format($quantity), ['class' => 'form-control input_number', 'id' => 'recipe_quantity', 'required', 'data-rule-notEmpty' => 'true', 'data-rule-notEqualToWastedQuantity' => 'true']); !!}
							<span class="@if(empty($sub_units)) input-group-addon @endif" id="unit_html">
								@if(!empty($sub_units))
									<select name="sub_unit_id" class="form-control" id="sub_unit_id">
									@foreach($sub_units as $key => $value)
										<option 
											value="{{$key}}" 
											data-multiplier="{{$value['multiplier']}}" 
											data-unit_name="{{$value['name']}}"
											@if($key == $sub_unit_id)
												@php
													$unit_name = $value['name'];
												@endphp
												selected
											@endif
											>{{$value['name']}}</option>
									@endforeach
									</select>
								@else
									{{ $unit_name }}
								@endif
							</span>
						</div>
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
			</div>
		@endcomponent

		@component('components.widget', ['class' => 'box-primary', 'title' => __('manufacturing::lang.ingredients')])

			<div class="row">
				<div class="col-md-12">
					<div id="enter_ingredients_table">
						
						@include('manufacturing::recipe.ingredients_for_production',['list_of_prices' => $list_of_prices] )
					</div>
				</div>
			</div>
			<div class="row">
				@if(request()->session()->get('business.enable_lot_number') == 1)
					<div class="col-sm-3">
						<div class="form-group">
							{!! Form::label('lot_number', __('lang_v1.lot_number').':') !!}
							{!! Form::text('lot_number', $purchase_line->lot_number, ['class' => 'form-control']); !!}
						</div>
					</div>
				@endif
				@if(session('business.enable_product_expiry'))
					<div class="col-sm-3">
						<div class="form-group">
							{!! Form::label('exp_date', __('product.exp_date').':*') !!}
							<div class="input-group">
								<span class="input-group-addon">
									<i class="fa fa-calendar"></i>
								</span>
								{!! Form::text('exp_date', !empty($purchase_line->exp_date) ? @format_date($purchase_line->exp_date) : null, ['class' => 'form-control', 'readonly']); !!}
							</div>
						</div>
					</div>
				@endif
				<div class="col-md-3">
					<div class="form-group">
						{!! Form::label('mfg_wasted_units', __('manufacturing::lang.waste_units').':') !!} @show_tooltip(__('manufacturing::lang.wastage_tooltip'))
						<div class="input-group">
							{!! Form::text('mfg_wasted_units', @num_format($production_purchase->mfg_wasted_units), ['class' => 'form-control input_number']); !!}
							<span class="input-group-addon" id="wasted_units_text">{{$unit_name}}</span>
						</div>
					</div>
				</div>
				<div class="col-md-3">
					<div class="form-group">
						{!! Form::label('production_cost', __('manufacturing::lang.production_cost').':') !!} @show_tooltip(__('manufacturing::lang.production_cost_tooltip'))
						<div class="input-group">
							{!! Form::text('production_cost', @num_format($production_purchase->mfg_production_cost), ['class' => 'form-control input_number']); !!}
							<span class="input-group-addon"><i class="fa fa-percent"></i></span>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-3 col-md-offset-9">
					{!! Form::hidden('final_total', @num_format($production_purchase->final_total), ['id' => 'final_total']); !!}
					<strong>
						{{__('manufacturing::lang.total_production_cost')}}:
					</strong>
					<span id="total_production_cost" class="display_currency" data-currency_symbol="true">{{$total_production_cost}}</span><br>
					<strong>
						{{__('manufacturing::lang.total_cost')}}:
					</strong>
					<span id="final_total_text" class="display_currency" data-currency_symbol="true">{{ $production_purchase->final_total }}</span>
				</div>
			</div>
			<div class="row">
				<div class="col-md-3 col-md-offset-9">
					<div class="form-group">
						<br>
						<div class="checkbox">
							<label>
								@if($production_purchase->mfg_is_final == 1)
								<div class="col hide">
									{!! Form::checkbox('finalize', 1, 1, ['class' => 'input-icheck  ', 'id' => 'finalize']); !!} @lang('manufacturing::lang.finalize')
								</div>
								@else
								{!! Form::checkbox('finalize', 1, false, ['class' => 'input-icheck', 'id' => 'finalize']); !!} @lang('manufacturing::lang.finalize')
								@show_tooltip(__('manufacturing::lang.finalize_tooltip'))
								@endif
							</label> 
						</div>
					</div>
				</div>
			</div>
			@if($store_out != null || $store_out != "")
			<div class="row">
				<div class="col-md-12">
					<button type="submit" class="btn btn-primary pull-right  " id="save_submit">@lang('messages.submit')</button>
				</div>
			</div>
			@endif
			<div class="row">
				<div class="col-md-4">
					<div   style="position:relative;background-color:rgb(255, 255, 255)" >
						<span style="position: absolute;z-index:2;background-color:rgb(255, 255, 255);width:100%;height:100%">&nbsp;</span>
						{!! Form::text('final_before', 0, [  "style" => ":hover{color:white;} " ,"readOnly" ,'id' => 'final_before']); !!}
					</div>
				</div>
			</div>

		@endcomponent

	{!! Form::close() !!}
	</section>
	@endsection

	@section('javascript')
		@include('manufacturing::production.production_script')

		<script type="text/javascript">
	
			
				$(document).ready(function(){
						calculateRecipeTotal();
  						open_row();
				});
	
				
		</script>
	@endsection
