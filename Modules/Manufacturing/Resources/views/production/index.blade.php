@extends('layouts.app')
@section('title', __('manufacturing::lang.production'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('manufacturing::lang.production')</h1>
</section>

<!-- Main content -->
<section class="content">
    <!-- Page level currency setting -->
    @if(isset($currency_details))
	<input type="hidden" id="p_code" value="{{$currency_details->code}}">
	<input type="hidden" id="p_symbol" value="{{$currency_details->symbol}}">
	<input type="hidden" id="p_thousand" value="{{$currency_details->thousand_separator}}">
	<input type="hidden" id="p_decimal" value="{{$currency_details->decimal_separator}}">
	@endif
    @component('components.widget', ['class' => 'box-primary'])
        @slot('tool')
            <div class="box-tools">
                <a class="btn btn-block btn-primary" href="{{action('\Modules\Manufacturing\Http\Controllers\ProductionController@create')}}">
                    <i class="fa fa-plus"></i> @lang( 'messages.add' )</a>
            </div>
        @endslot
        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="productions_table">
                 <thead>
                    <tr>
                        <th>@lang('messages.date')</th>
                        <th>@lang('purchase.ref_no')</th>
                        <th>@lang('purchase.location')</th>
                        <th>@lang('sale.product')</th>
                        <th>@lang('lang_v1.quantity')</th>
                        <th>@lang('manufacturing::lang.total_cost')</th>
                        <th>@lang('messages.action')</th>
                    </tr>
                </thead>
            </table>
        </div>
    @endcomponent
</section>
<!-- /.content -->
<div class="modal fade" id="recipe_modal" tabindex="-1" role="dialog" 
    aria-labelledby="gridSystemModalLabel">
</div>
@stop
@section('javascript')
    @include('manufacturing::layouts.partials.common_script')
@endsection
