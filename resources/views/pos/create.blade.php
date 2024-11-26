@extends("layouts.app")

@section("title",__("home.Create_pos"))

@section('content')
    <section class="content  no-print"> 

        <div class="row">
            <div class="col-xs-12">
                <h4> @lang("home.Create_pos") </h4>            
                
            </div>
        </div>
        {!! Form::open(['url' => action('PosBranchController@store'), 'method' => 'post', 'id' => "ADD_POS", 'files' => true ]) !!}
        @component("components.widget" , ["title"=>__('home.add_pos')])
        
        {{-- pattern name  --}}
        <div class="col-md-6">
            <div class="form-group">
                {!! Form::label('pos', __('home.pos') . ':*') !!}
                {!! Form::text('pos', null, ["id" => "pos" ,'class' => 'form-control' ,"placeholder" => __("home.enter pos name"),   'required']); !!}
            </div>
        </div>
        {{-- pos    --}}
        <div class="col-md-6">
            <div class="form-group">
                {!! Form::label('pattern', __('home.pattern') . ':*') !!}
                {!! Form::select('pattern', $patterns,null, ["id" => "pattern" ,'class' => 'form-control select2' ,"placeholder" => __("home.select pattern"),   'required']); !!}
            </div>
        </div>
        {{-- code    --}}
        <div class="col-md-6">
            <div class="form-group">
                {!! Form::label('store', __('warehouse.nameW') . ':*') !!}
                {!! Form::select('store', $stores,null, ["id" => "store" ,'class' => 'form-control select2' ,"placeholder" => __("home.select store"),   'required']); !!}
            </div>
        </div>
        {{-- invoice scheme  --}}
        <div class="col-md-6">
            <div class="form-group">
                {!! Form::label('invoice_scheme', __('invoice.invoice_scheme') . ':*') !!}
                {!! Form::select('invoice_scheme',$invoice_schemes, $default_invoice_schemes, ["id" => "invoice_scheme" ,'class' => 'form-control' ,"placeholder" => __("messages.please_select"),   'required']); !!}
            </div>
        </div>
        {{-- account cash main  --}}
        <div class="col-md-6">
            <div class="form-group">
                {!! Form::label('cash_main', __('home.cash_main') . ':*') !!}
                {!! Form::select('cash_main',$accounts_cash, null, ["id" => "cash_main" ,'class' => 'form-control select2' ,"placeholder" => __("messages.please_select"),   'required']); !!}
            </div>
        </div>
        {{-- account cash  --}}
        <div class="col-md-6">
            <div class="form-group">
                {!! Form::label('cash', __('home.cash') . ':*') !!}
                {!! Form::select('cash',$accounts_cash, null , ["id" => "cash" ,'class' => 'form-control select2' ,"placeholder" => __("messages.please_select"),   'required']); !!}
            </div>
        </div>
        {{-- account  visa main  --}}
        <div class="col-md-6">
            <div class="form-group">
                {!! Form::label('visa_main', __('home.visa_main') . ':*') !!}
                {!! Form::select('visa_main',$accounts_visa, null, ["id" => "visa_main" ,'class' => 'form-control select2' ,"placeholder" => __("messages.please_select"),   'required']); !!}
            </div>
        </div>
        {{-- account visa  --}}
        <div class="col-md-6">
            <div class="form-group">
                {!! Form::label('visa', __('home.visa') . ':*') !!}
                {!! Form::select('visa',$accounts_visa, null, [ "id" => "visa" ,'class' => 'form-control select2' ,"placeholder" => __("messages.please_select"),   'required']); !!}
            </div>
        </div>
        
        
        
        @endcomponent
        @component("components.widget" )
        <div class="row">
            <div class="col-sm-12  text-right"  >
                <button type="submit"   class="btn btn-primary btn-flat">@lang('messages.save')</button>
            </div>
        </div>
        @endcomponent
        
    </section>
    @stop
    @section("javascript")
             <script src="{{ asset('js/patterns.js?v=' . $asset_v) }}"></script>
    @endsection