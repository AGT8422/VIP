@extends('layouts.app')
@section('title', __('lang_v1.add_opening_stock'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('lang_v1.add_opening_stock')</h1>
</section>

<!-- Main content -->
<section class="content">
	{!! Form::open(['url' => action('OpeningStockController@save'), 'method' => 'post', 'id' => 'add_opening_stock_form' ]) !!}
	{!! Form::hidden('product_id', $product->id); !!}

	<div class="container">
		<div class="row">
			<div class="col-md-12 hide">
				<div class="form-group">
					{!! Form::label('store_id', __('warehouse.warehouse').':*') !!}
					{{-- @show_tooltip(__('tooltip.purchase_location')) --}}
					{!! Form::select('store_id', $mainstore_categories, null, ['class' => 'form-control select2', 'required'], $bl_attributes); !!}
				</div>
			</div>
		</div>
	</div>
	@include('opening_stock.form-part')
	
	<div class="row">
		<div class="col-sm-12">
			<!--<button type="submit" class="btn btn-primary pull-right">@lang('messages.save') </button>-->
		</div>
	</div>

	{!! Form::close() !!}
</section>
@stop
 
 
