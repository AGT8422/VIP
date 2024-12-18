@extends("layouts.app")

@section("title",__("lang_v1.list_of_Register"))

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
                    <table class="table  table-borderd table-striped " style="width:100%" id="user_activation_login_request_table">
                        <thead>
                            <tr>
                                <th >@lang("messages.action")</th>
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
                                <td  ></td>
                                <td  colspan="4" id="footer_total_users"></td>
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
     $(document).ready(function(){

        user_activation_login_request_table = $("#user_activation_login_request_table").DataTable({
            processing : true,
            serverSide : true,
            scrollY : true,
            scrollX : true,
            scrollCollaps : true,
            ajax : {
                url : '/user-activation/login-users',
                data : function (d) {
                    d.user_name = $("#name").val();
                    d.user_email = $("#email").val();
                    d.user_address = $("#address").val();
                    d.user_mobile = $("#mobile").val();
                    d.user_username = $("#username").val();
                    d.user_status = $("#status").val();
                    d.user_service = $("#services").val();
                    d.user_products = $("#products").val();
                    d.user_date = $("#date").val();
                    // alert(JSON.stringify(d));
                    check_code();
                    d = __datatable_ajax_callback(d);
                }, 
            },
            aaSorting: [[1,'desc']],
            columns: [
                {data: 'activate',name: 'activate'},
                {data: 'id',name: 'id'},
                {data: 'name'    ,name: 'name'},
                {data: 'type'    ,name: 'type' },
                {data: 'company_name'   ,name: 'company_name'},
                {data: 'device_no' ,name: 'device_no'},
                {data: 'email'  ,name: 'email'},
                {data: 'address',name: 'address'},
                {data: 'mobile' ,name: 'mobile'},
                {data: 'services' ,name: 'services'},
                {data: 'created_at'   ,name: 'created_at'},
             ],
            fnDrawCallback:function(oSettings){
                __currency_convert_recursively($("#user_activation_login_request_table"));
                check_code();
            },  
            "footerCallback": function( row,data,start,end,display){
                var users_count = 0;
                for( var r in data){
                    users_count++;                        
                }
                check_code();
                $("#footer_total_users").html(LANG.user_count_request + " :  " + users_count);
                // $("#footer_status").val("");
                // $("#footer_payment").val("");
                // $("#footer_due_payment").val("");
            },
        })
            
        });
     $(document).on("change","#name,#email,#address,#mobile,#username,#services,#date",function(){
        user_activation_login_request_table.ajax.reload();
        })
     $(document).on("change","#user_name,#user_email,#user_address,#user_mobile,#user_username,#user_status,#user_services,#user_products,#user_date",function(){
        user_activation_login_request_table.ajax.reload();
     })
 
    </script>
@endsection

