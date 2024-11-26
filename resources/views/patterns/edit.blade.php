@extends("layouts.app")

@section("title",__("business.Edit_patterns"))

@section('content')
   <section class="content  no-print"> 
    {!! Form::open(['url' => action('PatternController@update', [$pattern->id]), 'method' => 'post', 'id' => 'add_patterns', 'files' => true ]) !!}
       @component("components.widget" , ["title"=>__("business.Edit_patterns")])

       {{-- pattern name  --}}
       <div class="col-md-6">
            <div class="form-group">
                {!! Form::label('name', __('business.name') . ':*') !!}
                {!! Form::text('name', $pattern->name, ['class' => 'form-control' ,"placeholder" => __("business.enter pattern name"),   'required']); !!}
            </div>
        </div>
        {{-- pos    --}}
        <div class="col-md-6">
             <div class="form-group">
                 {!! Form::label('pos', __('business.pos') . ':*') !!}
                 {!! Form::text('pos', $pattern->pos, ['class' => 'form-control' ,"placeholder" => __("business.enter pos name"),   'required']); !!}
                </div>
            </div>
        {{-- code    --}}
        <div class="col-md-6">
             <div class="form-group">
                 {!! Form::label('code', __('business.code') . ':*') !!}
                 {!! Form::text('code', $pattern->code, ['class' => 'form-control' ,"placeholder" => __("business.enter pattern code"),   'required']); !!}
                </div>
        </div>
        {{-- invoice scheme  --}}
        <div class="col-md-6">
            <div class="form-group">
                {!! Form::label('invoice_scheme', __('invoice.invoice_scheme') . ':*') !!}
                {!! Form::select('invoice_scheme',$invoice_schemes, $pattern->invoice_scheme, ['class' => 'form-control' ,"placeholder" => __("messages.please_select"),   'required']); !!}
            </div>
        </div>
        {{-- location  --}}
        <div class="col-md-6">
            <div class="form-group">
                {!! Form::label('location_id', __('purchase.business_location') . ':*') !!}
                {!! Form::select('location_id',$business_locations, $pattern->location_id, ['class' => 'form-control' ,"placeholder" => __("messages.please_select"),   'required']); !!}
            </div>
        </div>
       {{-- invoice layout  --}}
        <div class="col-md-6">
            <div class="form-group">
                {!! Form::label('invoice_layout', __('invoice.invoice_layout') . ':*') !!}
                {!! Form::select('invoice_layout',$invoice_layout,  $pattern->invoice_layout, ['class' => 'form-control' ,"placeholder" => __("messages.please_select"),  'required']); !!}
            </div>
        </div>
        
        @endcomponent
        @component("components.widget" )
           <div class="row">
               <div class="col-sm-12  text-right"  >
                   <button type="button" id="submit-pattern" class="btn btn-primary btn-flat">@lang('messages.update')</button>
               </div>
           </div>
       @endcomponent
      
       
</section>
@stop
@section("javascript")
        <script src="{{ asset('js/patterns.js?v=' . $asset_v) }}"></script>
@endsection