@extends('layouts.app')

@section('title', __("home.Status Live")) 

@section("content")
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                @component("components.filters",["title"=>__("home.Status Live")])
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('reference_no',  __('home.ref_no_second') . ':') !!}
                        {!! Form::select('reference_no', $array_refe, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
                    </div>
                </div>
                @endcomponent
                @component("components.widget",["title"=>__("home.Status Live")])
                <div class="content">
                    <table class="table table-striped " style="width: 100% !important" id="status_table">
                        <thead>
                            <tr>
                                <th>@lang("messages.action")</th>
                                <th>@lang("home.ref_no_second")</th>
                                <th>@lang("lang_v1.date")</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <td  colspan="3">@lang("sale.total"):<span class="status_footer" ></span> </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
 
                @endcomponent
                
            </div>
        </div>
    </section>
@stop

<!-- /.content -->
<div class="modal fade view_modals" tabindex="-1" role="dialog" 
    	aria-labelledby="gridSystemModalLabel">
</div>


@section('javascript')
	<script src="{{ asset('js/status_live.js?v=' . $asset_v) }}"></script>
@endsection
