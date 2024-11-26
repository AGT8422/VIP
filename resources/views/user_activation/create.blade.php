@extends("layouts.app")

@section("title",__("lang_v1.create_user"))



@section('content')
    <section class="content">
        <h1>@lang("lang_v1.create_user")</h1>
        {!! Form::open(['url' => action('UserActivationController@store'), 'method' => 'post', 'id' => 'add_user_table', 'files' => true ]) !!}
        @component("components.widget",["class"=>"box-primary","title"=>__("lang_v1.user_info")])
            <div class="row">
                <div class="col-md-4 p-10">
                    {!!  Form::label("user_name" , __("lang_v1.user_name"))!!}
                    {!!  Form::text("user_name",null,["class"=>"form-control","id"=>"user_name","placeholder"=>__("messages.enter_user_name")])!!}
                </div>
                <div class="col-md-4 p-10">
                    {!!  Form::label("user_email" , __("lang_v1.user_email"))!!}
                    {!!  Form::text("user_email",null,["class"=>"form-control","id"=>"user_email","placeholder"=>__("messages.enter_user_email")])!!}
                </div>
                <div class="col-md-4 p-10">
                    {!!  Form::label("user_address" , __("lang_v1.user_address"))!!}
                    {!!  Form::text("user_address",null,["class"=>"form-control","id"=>"user_address","placeholder"=>__("messages.enter_user_address")])!!}
                </div>
                <div class="clearfix"></div>
                <div class="col-md-4 p-10">
                    {!!  Form::label("user_mobile" , __("lang_v1.user_mobile"))!!}
                    {!!  Form::text("user_mobile",null,["class"=>"form-control","id"=>"user_mobile","placeholder"=>__("messages.enter_user_mobile")])!!}
                </div>
                <div class="col-md-4 p-10">
                    {!!  Form::label("user_status" , __("lang_v1.user_status"))!!}
                    {!!  Form::text("user_status",null,["class"=>"form-control","id"=>"user_status","placeholder"=>__("messages.enter_user_status")])!!}
                </div>
                <div class="col-md-4 p-10">
                    {!!  Form::label("user_service" , __("lang_v1.user_services"))!!}
                    {!!  Form::text("user_service",null,["class"=>"form-control","id"=>"user_service","placeholder"=>__("messages.enter_user_services")])!!}
                </div>
                <div class="clearfix"></div>
                <div class="col-md-4 p-10">
                    {!!  Form::label("user_username" , __("lang_v1.user_username"))!!}
                    {!!  Form::text("user_username",null,["class"=>"form-control","id"=>"user_username","placeholder"=>__("messages.enter_user_username")])!!}
                </div>
                <div class="col-md-4 p-10">
                    {!!  Form::label("user_products" , __("lang_v1.user_products"))!!}
                    {!!  Form::text("user_products",null,["class"=>"form-control","id"=>"user_products","placeholder"=>__("messages.enter_user_products")])!!}
                </div>
                <div class="col-md-4 p-10">
                    {!!  Form::label("user_date" , __("messages.date"))!!}
                    {!!  Form::date("user_date",null,["class"=>"form-control","id"=>"user_user_date"])!!}
                </div>
                <div class="clearfix"></div>
                <h1>&nbsp;</h1>
                <div class="col-md-4 p-10">
                    {!!  Form::label("user_period" , __("lang_v1.user_period"))!!}@show_tooltip(__('lang_v1.user_period_help_text'))
                    {!!  Form::number("user_period",1,["class"=>"form-control","min"=>1,"id"=>"user_period","placeholder"=>__("messages.enter_user_period")])!!}
                </div>
                <div class="col-md-4 p-10">
                    {!!  Form::label("user_number_device" , __("User Devices Number"))!!} 
                    {!!  Form::number("user_number_device",1,["class"=>"form-control","min"=>1,"id"=>"user_number_device","placeholder"=>__("enter user device number")])!!}
                </div>
                <h1>&nbsp;</h1>
                <h1>&nbsp;</h1>
                <div class="row">
                    <div class="col-md-12 ">
                        <button type="submit" class="btn btn-primary pull-right">
                            @lang("messages.save")
                        </button>
                    </div>
                </div>

            </div>
        @endcomponent
        {!! Form::close() !!}
    </section>
@endsection