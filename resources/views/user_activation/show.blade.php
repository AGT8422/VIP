@extends("layouts.app")

@section("title",__("lang_v1.list_of_user"))

@section('content')
    <section class="content">
        <h1>@lang("lang_v1.list_of_user")</h1>
        @component("components.filters",["class"=>"box-primary","title"=>__("report.filters")])
            <div class="row">
                <div class="col-md-4">
                    {{  Form::label("name",__('lang_v1.user_name')) }}
                    {{  Form::select("name",$user_name,null,['class'=>'form-control select2','id'=>'name' ,'placeholder'=>__("messages.please_select")]) }}
                </div>
                <div class="col-md-4">
                    {{  Form::label("email",__('lang_v1.user_name')) }}
                    {{  Form::select("email",$user_email,null,['class'=>'form-control select2','id'=>'email','placeholder'=>__("messages.please_select") ]) }}
                </div>
                <div class="col-md-4">
                    {{  Form::label("address",__('lang_v1.user_address')) }}
                    {{  Form::select("address",$user_addresses,null,['class'=>'form-control select2','id'=>'address','placeholder'=>__("messages.please_select") ]) }}
                </div>
                <div class="clearfix"></div>
                <div class="col-md-4">
                    {{  Form::label("username",__('lang_v1.user_username')) }}
                    {{  Form::select("username",$user_usernames,null,['class'=>'form-control select2','id'=>'username','placeholder'=>__("messages.please_select") ]) }}
                </div>
                <div class="col-md-4">
                    {{  Form::label("services",__('lang_v1.user_services')) }}
                    {{  Form::select("services",$user_services,null,['class'=>'form-control select2','id'=>'services','placeholder'=>__("messages.please_select") ]) }}
                </div>
                <div class="col-md-4">
                    {{  Form::label("mobile",__('lang_v1.user_mobile')) }}
                    {{  Form::select("mobile",$user_mobile,null,['class'=>'form-control select2','id'=>'mobile' ,'placeholder'=>__("messages.please_select")]) }}
                </div>
                <div class="clearfix"></div>
                <div class="col-md-4">
                    {{  Form::label("date",__('lang_v1.activation_date')) }}
                    {{  Form::date("date", null,['class'=>'form-control  ','id'=>'date' ]) }}
                </div>
            </div>
        @endcomponent
        @component("components.widget",["class"=>"box-primary","title"=>__("lang_v1.user_list")])
            <div class="row">

                <div class="col-md-12">
                    <h3 class="pull-right">
                        {{-- <a class="btn btn-primary" href="{{route("user-activation.create")}}">
                            <i class="fa fas fa-plus"></i>
                            @lang("messages.add")
                        </a> --}}
                    </h3>
                </div>
                <div class="col-md-12" >
                    <table class="table  table-borderd table-striped " style="width:100%" id="user_activation_request_table">
                        <thead>
                            <tr>
                                <th>@lang("messages.action")</th>
                                <th>@lang("lang_v1.no")</th>
                                <th>@lang("lang_v1.user_name")</th>
                                <th>@lang("lang_v1.user_type")</th>
                                <th>@lang("lang_v1.company_name")</th>
                                <th>@lang("lang_v1.device_no")</th>
                                <th>@lang("lang_v1.user_email")</th>
                                <th>@lang("lang_v1.user_address")</th>
                                <th>@lang("lang_v1.user_mobile")</th>
                                <th>@lang("lang_v1.user_services")</th>
                                <th>@lang("lang_v1.date")</th>
                             </tr>
                        </thead>
                         
                        <tfoot>
                            <tr>
                                <td  colspan="3" id="footer_total_users"></td>
                                <td   ></td>
                                <td  id="footer_payment"></td>
                                <td  id="footer_due_payment"></td>
                                <td  id="footer_status"></td>
                                <td  colspan="2"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        @endcomponent
        </section>
@endsection

@section("javascript")
    <script src="{{ asset("js/user_activate.js?v=" . $asset_v) }}"></script>
    <script type="text/javascript">
         setTimeout(() => {
            check_code();
         }, 2000);
        
    </script>
@endsection

