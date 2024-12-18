@extends("layouts.app")

@section("title",__("business.patterns"))

@section('content')
 

    <section class="content-header">
            <h3 class="content-header  no-print">@lang("business.patterns") </h3>
    </section>
 <section class="content-header  no-print">
     
     @component("components.filters" , ["title"=>__('report.filters')])
     
     <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('location_id',  __('purchase.business_location') . ':') !!}
            {!! Form::select('location_id',$business_locations , null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('pattern_name',  __('business.name') . ':') !!}
            {!! Form::select('pattern_name', $pattern_name, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('invoice_scheme',  __('invoice.invoice_scheme') . ':') !!}
            {!! Form::select('invoice_scheme', $invoice_schemes, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('invoice_layout',  __('invoice.invoice_layout') . ':') !!}
            {!! Form::select('invoice_layout',$invoice_layout, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group hide">
            {!! Form::label('pos',  __('business.pos') . ':') !!}
            {!! Form::select('pos',[], null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
        </div>
    </div>
     
     @endcomponent
    </section>
    <section class="content  no-print"> 
        @component("components.widget" , ["title"=>__("business.patterns")])
        {{-- @can('purchase.create') --}}
            @slot('tool')
                <div class="box-tools">
                    <a class="btn btn-block btn-primary" href="{{action('PatternController@create')}}">
                    <i class="fa fa-plus"></i> @lang('messages.add')</a>
                </div>
            @endslot
        {{-- @endcan --}}
        <table class="table table-bordered  table-striped ajax_view" id="patterns_table" style="width: 100%;">
            <thead>
                <tr>
                    <th>@lang('messages.action')</th>
                    <th>@lang('business.name')</th>
                    <th>@lang('purchase.location')</th>
                    <th>@lang('invoice.invoice_scheme')</th>
                    <th>@lang('invoice.invoice_layout')</th>
                    <th>@lang('business.pos')</th>
                    <th>@lang('messages.date')</th>
                    <th>@lang('lang_v1.added_by')</th>
                </tr>
            </thead>
            <tfoot> 
                <tr class="bg-gray font-17 text-center footer-total">
                    <td colspan="8"></td>
                </tr>
            </tfoot>
        </table>
        @endcomponent
 </section>
@stop

@section("javascript")
    <script src="{{ asset('js/patterns.js?v=' . $asset_v) }}"></script>
    <script type="text/javascript">

    </script>
@endsection
