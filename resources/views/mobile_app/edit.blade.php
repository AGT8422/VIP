@extends("layouts.app")

@section("title",__("Edit Customer App"))

@section('content')
   <section class="content  no-print" style="margin:10%  30%"> 
    {!! Form::open(['url' => action('ApimobileController@updateApi' ,[$MobileApp->id]), 'method' => 'post', 'id' => 'add_customers', 'files' => true ]) !!}
       @component("components.widget" , ["title"=>__("Create Customer App")])

       {{-- name  --}}
       <div class="col-md-12">
            <div class="form-group">
                {!! Form::label('name', __('Customer Name') . ':*') !!}
                {!! Form::text('name', $MobileApp->name, ['class' => 'form-control' ,"placeholder" => __("Enter Customer Name"),   'required']); !!}
            </div>
        </div>
        {{-- surename    --}}
        <div class="col-md-12">
             <div class="form-group">
                 {!! Form::label('surname', __('SureName') . ':') !!}
                 {!! Form::text('surname', $MobileApp->surname, ['class' => 'form-control' ,"placeholder" => __("Enter Customer SureName")]); !!}
                </div>
            </div>
        {{-- email    --}}
        <div class="col-md-12">
                <div class="form-group">
                    {!! Form::label('email', __('Email') . ':') !!}
                    {!! Form::text('email', $MobileApp->email, ['class' => 'form-control' ,"placeholder" => __("Enter Email")   ]); !!}
                </div>
        </div>
        {{-- mobile    --}}
        <div class="col-md-12">
                <div class="form-group">
                    {!! Form::label('mobile', __('Mobile') . ':*') !!}
                    {!! Form::text('mobile', $MobileApp->mobile, ['class' => 'form-control' ,"placeholder" => __("Enter Mobile Number"),   'required']); !!}
                </div>
        </div>
        {{-- username    --}}
        <div class="col-md-12">
             <div class="form-group">
                 {!! Form::label('username', __('Username') . ':*') !!}
                 {!! Form::text('username', $MobileApp->username, ['class' => 'form-control' ,"placeholder" => __("Enter User Name"),   'required']); !!}
                </div>
        </div>
        {{-- Password  --}}
        <div class="col-md-12">
            <div class="form-group">
                {!! Form::label('password', __('Password') . ':*') !!}
                <input id="password" type="password" class="form-control" name="password" style="padding:10px;border-radius:0px" value=""  placeholder="Password">
            </div>
        </div>
        {{-- Api Url  --}}
        <div class="col-md-12">
            <div class="form-group">
                {!! Form::label('api_url', __('Api Url') . ':*') !!}
                {!! Form::text('api_url', $MobileApp->api_url, ['class' => 'form-control' ,"placeholder" => __("Enter Api Url"),   'required']); !!}
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12  text-right"  >
                <button type="button" id="submit-mobile" class="btn btn-primary btn-flat">@lang('messages.update')</button>
            </div>
        </div>
      
        
        @endcomponent
     
      
       
</section>
@stop
@section("javascript")
        <script src="{{ asset('js/mobile_app.js?v=' . $asset_v) }}"></script>
@endsection