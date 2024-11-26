@extends("layouts.app")

@section("title",__("Mobile Section"))

@section('content')
 

    <section class="content-header">
            <h3 class="content-header  no-print">@lang("Mobile Section") </h3>
    </section>
 <section class="content-header  no-print">
     
     @component("components.filters" , ["title"=>__('report.filters')])
     
     <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('name',  __('Customer Name') . ':') !!}
            {!! Form::select('name',$name , null, ['class' => 'form-control select2', "id"=> "name", 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
        </div>
    </div>
     <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('surename',  __('SureName') . ':') !!}
            {!! Form::select('surename',$surename , null, ['class' => 'form-control select2', "id"=> "surename", 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('username',  __('Username') . ':') !!}
            {!! Form::select('username', $username, null, ['class' => 'form-control select2', "id"=> "username", 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('device_ip',  __('Device ip') . ':') !!}
            {!! Form::select('device_ip', $device_ip, null, ['class' => 'form-control select2', "id"=> "device_ip", 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('device_id',  __('Device id') . ':') !!}
            {!! Form::select('device_id',$device_id, null, ['class' => 'form-control select2', "id"=> "device_id", 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
        </div>
    </div>
    {{-- <div class="col-md-3">
        <div class="form-group hide">
            {!! Form::label('pos',  __('business.pos') . ':') !!}
            {!! Form::select('pos',$name, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
        </div>
    </div> --}}
     
     @endcomponent
    </section>
    <section class="content  no-print"> 
        @component("components.widget" , ["title"=>__("List Of Customers")])
        {{-- @can('purchase.create') --}}
            @slot('tool')
                <div class="box-tools">
                    <a class="btn btn-block btn-primary" href="{{action('ApimobileController@createApi')}}">
                    <i class="fa fa-plus"></i> @lang('messages.add')</a>
                </div>
            @endslot
        {{-- @endcan --}}
        <table class="table table-bordered  table-striped ajax_view" id="mobile_table_app" style="width: 100%;">
            <thead>
                <tr>
                    <th>@lang('messages.action')</th>
                    <th>@lang('Customer Name')</th>
                    <th>@lang('Surename')</th>
                    <th>@lang('Username')</th>
                    <th>@lang('Device Id')</th>
                    <th>@lang('Device Ip')</th>
                    <th>@lang('Last Login')</th>
                    <th>@lang('messages.date')</th>
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
    <script src="{{ asset('js/mobile_app.js?v=' . $asset_v) }}"></script>
    <script type="text/javascript">

    </script>
@endsection
