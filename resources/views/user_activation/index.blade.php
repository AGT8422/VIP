@extends("layouts.app")

@section("title",__("lang_v1.list_of_user"))

@section('content')
    <section class="content">
        <h1>@lang("lang_v1.list_of_user")</h1>
        @component("components.filters",["class"=>"box-primary","title"=>__("report.filters")])
            <div class="row">
                <div class="col-md-4">
                    {{  Form::label("user_name",__('lang_v1.user_name')) }}
                    {{  Form::select("user_name",$user_name,null,['class'=>'form-control select2','id'=>'user_name' ,'placeholder'=>__("messages.please_select")]) }}
                </div>
                <div class="col-md-4">
                    {{  Form::label("user_email",__('lang_v1.user_name')) }}
                    {{  Form::select("user_email",$user_email,null,['class'=>'form-control select2','id'=>'user_email','placeholder'=>__("messages.please_select") ]) }}
                </div>
                <div class="col-md-4">
                    {{  Form::label("user_address",__('lang_v1.user_address')) }}
                    {{  Form::select("user_address",$user_addresses,null,['class'=>'form-control select2','id'=>'user_address','placeholder'=>__("messages.please_select") ]) }}
                </div>
                <div class="clearfix"></div>
                <div class="col-md-4">
                    {{  Form::label("user_username",__('lang_v1.user_username')) }}
                    {{  Form::select("user_username",$user_usernames,null,['class'=>'form-control select2','id'=>'user_username','placeholder'=>__("messages.please_select") ]) }}
                </div>
                <div class="col-md-4">
                    {{  Form::label("user_status",__('lang_v1.user_status')) }}
                    {{  Form::select("user_status",$user_status,null,['class'=>'form-control select2','id'=>'user_status','placeholder'=>__("messages.please_select") ]) }}
                </div>
                <div class="col-md-4">
                    {{  Form::label("user_services",__('lang_v1.user_services')) }}
                    {{  Form::select("user_services",$user_services,null,['class'=>'form-control select2','id'=>'user_services','placeholder'=>__("messages.please_select") ]) }}
                </div>
                <div class="clearfix"></div>
                <div class="col-md-4">
                    {{  Form::label("user_mobile",__('lang_v1.user_mobile')) }}
                    {{  Form::select("user_mobile",$user_mobile,null,['class'=>'form-control select2','id'=>'user_mobile' ,'placeholder'=>__("messages.please_select")]) }}
                </div>
                <div class="col-md-4">
                    {{  Form::label("user_products",__('lang_v1.user_products')) }}
                    {{  Form::select("user_products",$user_products,null,['class'=>'form-control select2','id'=>'user_products','placeholder'=>__("messages.please_select") ]) }}
                </div>
                <div class="col-md-4">
                    {{  Form::label("user_date",__('lang_v1.activation_date')) }}
                    {{  Form::date("user_date", null,['class'=>'form-control  ','id'=>'user_date' ]) }}
                </div>
            </div>
        @endcomponent
        @component("components.widget",["class"=>"box-primary","title"=>__("lang_v1.user_list")])
            <div class="row">

                <div class="col-md-12">
                    {{-- <h3 class="pull-right">
                        <a class="btn btn-primary" href="{{route("user-activation.create")}}">
                            <i class="fa fas fa-plus"></i>
                            @lang("messages.add")
                        </a>
                    </h3> --}}
                </div>
                <div class="col-md-12">
                    <table class="table  table-borderd table-striped " id="user_activation_table">
                        <thead>
                            <tr>
                                <th>@lang("lang_v1.no")</th>
                                <th>@lang("lang_v1.user_name")</th> 
                                <th>@lang("lang_v1.user_email")</th>
                                <th>@lang("lang_v1.company_name")</th>
                                <th>@lang("lang_v1.user_address")</th>
                                <th>@lang("lang_v1.user_mobile")</th>
                                <th>@lang("lang_v1.user_username")</th>
                                <th class="hide">@lang("lang_v1.user_password")</th>
                                <th>@lang("lang_v1.code")</th>
                                <th>@lang("lang_v1.user_status")</th>
                                <th>@lang("lang_v1.user_services")</th>
                                <th>@lang("lang_v1.user_products")</th>
                                <th>@lang("lang_v1.activation_date")</th>
                                <th>@lang("lang_v1.date")</th>
                                <th>@lang("lang_v1.user_payments")</th>
                                <th>@lang("lang_v1.user_due_payment")</th>
                                <th>@lang("lang_v1.user_number_device")</th>
                                <th>@lang("lang_v1.user_token")</th>
                                <th>@lang("lang_v1.until_date")</th>
                            </tr>
                        </thead>
                         
                        <tfoot>
                            <tr>
                                <td  colspan="8" id="footer_total_users"></td>
                                <td   ></td>
                                <td  id="footer_payment"></td>
                                <td  id="footer_due_payment"></td>
                                <td  id="footer_status"></td>
                                <td  colspan="4"></td>
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

