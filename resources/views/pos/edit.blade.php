@extends("layouts.app")

@section("title",__("home.Edit_pos"))

@section('content')
    <div class="row">
        <div class="col-xs-12">
            <h4> @lang("home.Edit_pos") </h4>            
            
        </div>
    </div>
     @component("components.widget" )
           <div class="row">
               <div class="col-sm-12  text-right"  >
                   <button type="button" id="submit-pattern" class="btn btn-primary btn-flat">@lang('messages.save')</button>
               </div>
           </div>
       @endcomponent
      
</section>
@stop
@section("javascript")
        {{-- <script src="{{ asset('js/patterns.js?v=' . $asset_v) }}"></script> --}}
@endsection