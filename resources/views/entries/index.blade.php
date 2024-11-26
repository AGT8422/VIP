@extends('layouts.app')
@section('title', __('home.Entries'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header no-print">
    <h1>@lang('home.Entries')
        <small></small>
    </h1>
</section>

<!-- Main content -->
<section class="content no-print">
    @if(session('yes'))
    <div class="alert success alert-success" >
        {{ session('yes')  }}
    </div>
    @endif
    @component('components.filters', ['title' => __('report.filters')])
      
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('refe_no_e',  __('home.ref_no_first') . ':') !!}
                    {!! Form::select('refe_no_e', $refe , null, ['class' => 'form-control select2',  'placeholder' => __('lang_v1.all'),'style' => 'width:100%']); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('ref_no_e',  __('home.ref_no_second') . ':') !!}
                    {!! Form::select('ref_no_e', $ref ,null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('state',  __('home.state') . ':') !!}
                    {!! Form::select('state', $state ,null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('purchase_list_filter_date_range', __('report.date_range') . ':') !!}
                    {!! Form::text('purchase_list_filter_date_range', null, ['placeholder' => __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'readonly']); !!}
                </div>
            </div>
       
    @endcomponent
    
    @component('components.widget', ['title' => __('home.List Entries')])
        <div class="row">
            <div class="col-md-12">
                <table class="table table-responsive table-stripted " id="entries_table" style="width:100%">
                    <thead>
                        <tr>
                            <th>@lang("messages.action")</th>
                            <th>@lang("home.ref_no_first")</th>
                            <th>@lang("home.ref_no_second")</th>
                            <th>@lang("home.state")</th>
                            <th>@lang("lang_v1.date")</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <td colspan="3">@lang("home.Total Entries") : {{$total}}</td>
                            <td class="footer_entry_total"></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    @endcomponent
        

    <div class="modal fade view_model" tabindex="-1" role="dialog" 
    	aria-labelledby="gridSystemModalLabel">
    </div>

</section>

@stop
@section("javascript")
    <script src="{{ asset('js/entries.js?v=' . $asset_v) }}"></script>
@endsection