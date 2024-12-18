@extends("layouts.app")

@section("title",__("home.delete_page"))

@section('content')
 

    <section class="content-header">
            <h3 class="content-header  no-print">@lang("home.delete_page") </h3>
    </section>

    <section class="content-header  no-print">
        
        @component("components.widget" , ["title"=>__('home.All_Option')])
        {{-- delete every thing --}}
        <div class="col-md-3">
            <div class="form-group ">
                {!! Form::label('delete_all',  __('home.del-All') . ':') !!}
                <br>
                <button class="btn" style="background-color:rgb(0, 158, 182)">
                    <a class="btn  btn-modal" style="color:white" data-href="{{action("General\DeleteController@delete_all")}}" id="delete_all"><i class="fas fa-trash"></i> &nbsp;&nbsp;@lang("home.del-All")</a>
                </button>
            </div>
        </div>
        {{-- delete  items --}}
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('delete_items',  __('home.del-items') . ':') !!}
                <br>
                <button class="btn" style="background-color:rgb(0, 158, 182)">
                    <a class="btn   btn-modal" style="color:white"  data-href="{{action("General\DeleteController@delete_items")}}" id="delete_items"><i class="fas fa-trash"></i>&nbsp;&nbsp;@lang("home.del-items")</a>
                </button>
            </div>
        </div>
        {{-- delete purchases--}}
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('delete_purchases',  __('home.del-purchases') . ':') !!}
                <br>
                <button class="btn" style="background-color:rgb(0, 158, 182)">
                    <a class="btn  btn-modal"  style="color:white"  data-href="{{action("General\DeleteController@delete_purchases")}}" id="delete_purchases"><i class="fas fa-trash"></i>&nbsp;&nbsp;@lang("home.del-purchases")</a>
                </button>
            </div>
        </div>
        {{-- delete sells --}}
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('delete_sells',  __('home.del-sells') . ':') !!}
                <br>
                <button class="btn" style="background-color:rgb(0, 158, 182)">
                    <a class="btn  btn-modal"  style="color:white" data-href="{{action("General\DeleteController@delete_sells")}}" id="delete_sells"><i class="fas fa-trash"></i>&nbsp;&nbsp;@lang("home.del-sells")</a>
                </button>
            </div>
        </div>
        {{-- delete accounts --}}
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('delete_accounts',  __('home.del-accounts') . ':') !!}
                <br>
                <button class="btn" style="background-color:rgb(0, 158, 182)">
                    <a class="btn   btn-modal"  style="color:white" data-href="{{action("General\DeleteController@delete_accounts")}}" id="delete_accounts"><i class="fas fa-trash"></i>&nbsp;&nbsp;@lang("home.del-accounts")</a>
                </button>
            </div>
        </div>
        {{-- delete users --}}
        <div class="col-md-3">
            <div class="form-group ">
                {!! Form::label('delete_users',  __('home.del-users') . ':') !!}
                <br>
                <button class="btn" style="background-color:rgb(0, 158, 182)">
                    <a class="btn btn-modal"  style="color:white" data-href="{{action("General\DeleteController@delete_users")}}" id="delete_users"><i class="fas fa-trash"></i>&nbsp;&nbsp;@lang("home.del-users")</a>
                </button>
            </div>
        </div>
        {{-- delete suppliers --}}
        <div class="col-md-3">
            <div class="form-group ">
                {!! Form::label('delete_suppliers',  __('home.del-suppliers') . ':') !!}
                <br>
                <button class="btn" style="background-color:rgb(0, 158, 182)">
                    <a class="btn     btn-modal"  style="color:white" data-href="{{action("General\DeleteController@delete_suppliers")}}" id="delete_suppliers"><i class="fas fa-trash"></i>&nbsp;&nbsp;@lang("home.del-suppliers")</a>
                </button>
            </div>
        </div>
        {{-- delete customers --}}
        <div class="col-md-3">
            <div class="form-group ">
                {!! Form::label('delete_customers',  __('home.del-customers') . ':') !!}
                <br>
                <button class="btn" style="background-color:rgb(0, 158, 182)">
                    <a class="btn     btn-modal"  style="color:white" data-href="{{action("General\DeleteController@delete_customers")}}" id="delete_customers"><i class="fas fa-trash"></i>&nbsp;&nbsp;@lang("home.del-customers")</a>
                </button>
            </div>
        </div>
        {{-- delete payments --}}
        <div class="col-md-3">
            <div class="form-group ">
                {!! Form::label('delete_payments',  __('home.del-payments') . ':') !!}
                <br>
                <button class="btn" style="background-color:rgb(0, 158, 182)">
                    <a class="btn    btn-modal"  style="color:white" data-href="{{action("General\DeleteController@delete_payments")}}" id="delete_payments"><i class="fas fa-trash"></i>&nbsp;&nbsp;@lang("home.del-payments")</a>
                </button>
            </div>
        </div>
        {{-- reset numbers --}}
        <div class="col-md-3">
            <div class="form-group ">
                {!! Form::label('reset_numbers',  __('home.del-number') . ':') !!}
                <br>
                <button class="btn" style="background-color:rgb(0, 158, 182)">
                    <a class="btn btn-modal"  style="color:white" data-href="{{action("General\DeleteController@reset_numbers")}}" id="reset_numbers"><i class="fas fa-trash"></i>&nbsp;&nbsp;@lang("home.del-number")</a>
                </button>
            </div>
        </div>
        
        @endcomponent
    </section>
    
 @stop

@section("javascript")
    <script src="{{ asset('js/delete_file.js?v=' . $asset_v) }}"></script>
    <script type="text/javascript">

    </script>
@endsection
