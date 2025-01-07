@extends('layouts.app')
@section('title', __('business.business_settings'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('business.business_settings')</h1>
    <br>
    @include('layouts.partials.search_settings')
</section>

<!-- Main content -->
<section class="content">
{!! Form::open(['url' => action('BusinessController@postBusinessSettings'), 'method' => 'post', 'id' => 'bussiness_edit_form',
           'files' => true ]) !!}
    <div class="row">
        <div class="col-xs-12">
       <!--  <pos-tab-container> -->
        <div class="col-xs-12 pos-tab-container">
            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 pos-tab-menu">
                <div class="list-group">
                    <a href="#" class="list-group-item text-center active">@lang('business.business')</a>
                    <a href="#" class="list-group-item text-center">@lang('business.tax') @show_tooltip(__('tooltip.business_tax'))</a>
                    <a href="#" class="list-group-item text-center">@lang('business.product')</a>
                    <a href="#" class="list-group-item text-center">@lang('contact.contact')</a>
                    <a href="#" class="list-group-item text-center">@lang('business.sale')</a>
                    <a href="#" class="list-group-item text-center">@lang('sale.pos_sale')</a>
                    <a href="#" class="list-group-item text-center">@lang('purchase.purchases')</a>
                    <a href="#" class="list-group-item text-center">@lang('business.dashboard')</a>
                    <a href="#" class="list-group-item text-center">@lang('business.system')</a>
                    <a href="#" class="list-group-item text-center">@lang('lang_v1.prefixes')</a>
                    <a href="#" class="list-group-item text-center">@lang('lang_v1.email_settings')</a>
                    <a href="#" class="list-group-item text-center">@lang('lang_v1.sms_settings')</a>
                    <a href="#" class="list-group-item text-center">@lang('lang_v1.reward_point_settings')</a>
                    {{-- @if(auth()->user()->can('superadmin')) --}}
                    <a href="#" @can('superadmin')  class="list-group-item text-center"  @else class="list-group-item text-center"  @endcan>@lang('lang_v1.modules')</a>
                   
                    {{-- @endif --}}
                    <a href="#" class="list-group-item text-center">@lang('lang_v1.custom_labels')</a>
                    @if (auth()->user()->can('superadmin'))  
                        @php $is_mfg_enabled = true; @endphp
                    @else 
                        @php  
                        
                            $business_id    = session()->get('user.business_id');
                            $module_util    = new App\Utils\ModuleUtil();
                            $is_mfg_enabled = (boolean)$module_util->hasThePermissionInSubscription($business_id, 'manufacturing_module', 'superadmin_package');
                        @endphp
                    @endif
                    
                    @if ($is_mfg_enabled && (auth()->user()->can('manufacturing.access_recipe') || auth()->user()->can('manufacturing.access_production')))  
                        <a href="#" class="list-group-item text-center">@lang('lang_v1.MFG')</a>
                    @endif
                        
                    @if(auth()->user()->can('superadmin'))
                        <a href="#" class="list-group-item text-center">@lang('lang_v1.mobile_app')</a>
                    @endif
                    <a href="#" class="list-group-item text-center">@lang('business.currency')</a>
                </div>
            </div>
            
            <div class="col-lg-10 col-md-10 col-sm-10 col-xs-10 pos-tab">
                <!-- tab 1 start -->
                @include('business.partials.settings_business')
                <!-- tab 1 end -->
                <!-- tab 2 start -->
                @include('business.partials.settings_tax')
                <!-- tab 2 end -->
                <!-- tab 3 start -->
                @include('business.partials.settings_product')

                @include('business.partials.settings_contact')
                <!-- tab 3 end -->
                <!-- tab 4 start -->
                @include('business.partials.settings_sales')
                @include('business.partials.settings_pos')
                <!-- tab 4 end -->
                <!-- tab 5 start -->
                @include('business.partials.settings_purchase')
                <!-- tab 5 end -->
                <!-- tab 6 start -->
                @include('business.partials.settings_dashboard')
                <!-- tab 6 end -->
                <!-- tab 7 start -->
                @include('business.partials.settings_system')
                <!-- tab 7 end -->
                <!-- tab 8 start -->
                @include('business.partials.settings_prefixes')
                <!-- tab 8 end -->
                <!-- tab 9 start -->
                @include('business.partials.settings_email')
                <!-- tab 9 end -->
                <!-- tab 10 start -->
                @include('business.partials.settings_sms')
                <!-- tab 10 end -->
                <!-- tab 11 start -->
                @include('business.partials.settings_reward_point')
                <!-- tab 11 end -->
                <!-- tab 12 start -->
                {{-- @if(auth()->user()->can('superadmin')) --}}
                    @include('business.partials.settings_modules')
                {{-- @endif --}}
                <!-- tab 12 end -->
                <!-- tab 13 start -->
                @include('business.partials.settings_custom_labels')
                <!-- tab 13 end -->
                <!-- tab 14 start -->             
                
                @if ($is_mfg_enabled && (auth()->user()->can('manufacturing.access_recipe') || auth()->user()->can('manufacturing.access_production'))) 
                    @include('business.partials.settings_mfg_account')
                @endif
                    <!-- tab 14 end -->
                    <!-- tab 15 start -->
                @if(auth()->user()->can('superadmin'))
                    @include('business.partials.settings_mobile_app')
                @endif
                <!-- tab 15 end -->
                <!-- tab 16 start -->
                @include('business.partials.settings_currencies')
                <!-- tab 16 end -->
            </div>
        </div>
        <!--  </pos-tab-container> -->
        </div>
    </div>
    

    <div class="row">
        <div class="col-sm-12">
            <button class="btn btn-danger pull-right" type="submit">@lang('business.update_settings')</button>
        </div>
    </div>
{!! Form::close() !!}
</section>
<!-- /.content -->
@stop
@section('javascript')
<script type="text/javascript">
setTimeout(() => {

    $('.cur_select2').select2();
    __page_leave_confirmation('#bussiness_edit_form');
    $(document).on('ifToggled', '#use_superadmin_settings', function() {
        if ($('#use_superadmin_settings').is(':checked')) {
            $('#toggle_visibility').addClass('hide');
            $('.test_email_btn').addClass('hide');
        } else {
            $('#toggle_visibility').removeClass('hide');
            $('.test_email_btn').removeClass('hide');
        }
    });
    $('#test_email_btn').click( function() {
        var data = {
            mail_driver: $('#mail_driver').val(),
            mail_host: $('#mail_host').val(),
            mail_port: $('#mail_port').val(),
            mail_username: $('#mail_username').val(),
            mail_password: $('#mail_password').val(),
            mail_encryption: $('#mail_encryption').val(),
            mail_from_address: $('#mail_from_address').val(),
            mail_from_name: $('#mail_from_name').val(),
        };
        $.ajax({
            method: 'post',
            data: data,
            url: "{{ action('BusinessController@testEmailConfiguration') }}",
            dataType: 'json',
            success: function(result) {
                if (result.success == true) {
                    swal({
                        text: result.msg,
                        icon: 'success'
                    });
                } else {
                    swal({
                        text: result.msg,
                        icon: 'error'
                    });
                }
            },
        });
    });
    $('#test_sms_btn').click( function() {
        var test_number = $('#test_number').val();
        if (test_number.trim() == '') {
            toastr.error('{{__("lang_v1.test_number_is_required")}}');
            $('#test_number').focus();

            return false;
        }

        var data = {
            url: $('#sms_settings_url').val(),
            send_to_param_name: $('#send_to_param_name').val(),
            msg_param_name: $('#msg_param_name').val(),
            request_method: $('#request_method').val(),
            param_1: $('#sms_settings_param_key1').val(),
            param_2: $('#sms_settings_param_key2').val(),
            param_3: $('#sms_settings_param_key3').val(),
            param_4: $('#sms_settings_param_key4').val(),
            param_5: $('#sms_settings_param_key5').val(),
            param_6: $('#sms_settings_param_key6').val(),
            param_7: $('#sms_settings_param_key7').val(),
            param_8: $('#sms_settings_param_key8').val(),
            param_9: $('#sms_settings_param_key9').val(),
            param_10: $('#sms_settings_param_key10').val(),

            param_val_1: $('#sms_settings_param_val1').val(),
            param_val_2: $('#sms_settings_param_val2').val(),
            param_val_3: $('#sms_settings_param_val3').val(),
            param_val_4: $('#sms_settings_param_val4').val(),
            param_val_5: $('#sms_settings_param_val5').val(),
            param_val_6: $('#sms_settings_param_val6').val(),
            param_val_7: $('#sms_settings_param_val7').val(),
            param_val_8: $('#sms_settings_param_val8').val(),
            param_val_9: $('#sms_settings_param_val9').val(),
            param_val_10: $('#sms_settings_param_val10').val(),
            test_number: test_number
        };

        $.ajax({
            method: 'post',
            data: data,
            url: "{{ action('BusinessController@testSmsConfiguration') }}",
            dataType: 'json',
            success: function(result) {
                if (result.success == true) {
                    swal({
                        text: result.msg,
                        icon: 'success'
                    });
                } else {
                    swal({
                        text: result.msg,
                        icon: 'error'
                    });
                }
            },
        });
    });
    update_symbol();
    function start(){
       var def     = $(".cur_defult_check").val();
       var id      = $(".cur_defult_check_name").val();
        if(def  != null){
           check_box(id,def ); 
        }
    }
    function formatRows() {
      return '<tr class="ros">' +
            // '<td class="col-xs-1"><input type="checkbox" class="curr_right" name="curr_right[]"  /></td>' +
            '<td class="col-xs-1">{{ Form::date('currency_date[]',date('Y-m-d'), ['class'=>'form-control ','readOnly','required','max'=>date('Y-m-d')]) }}</td>' +
            '<td class="col-xs-1">{{ Form::select('currency_name[]',$currencies,null,['class'=>'form-control  cur_name cur_select2  ' ,'placeholder'=> __("messages.please_select")  ]) }}</td>' +
            '<td class="col-xs-2">{{ Form::text('currency_symbol[]',null,['class'=>'form-control cur_symbol','readOnly' ]) }}</td>' +
            '<td class="col-xs-2">{{ Form::number('currency_amount[]',0,['class'=>'form-control currency_amount','required','step'=>'any','min'=>0]) }}</td>' +
            '<td class="col-xs-2">{{ Form::number('currency_opposit_amount[]',0,['class'=>'form-control currency_opposit_amount','readOnly','required','step'=>'any','min'=>0]) }}</td>' +
            '<td class="col-xs-2">{{ Form::select('cur_default[ ]',["1"=>"Default"],null,['class'=>'form-control  cur_default cur_select2  ','required' ,'placeholder'=> __("messages.please_select")  ])}}</td>' +
            '<td class="col-xs-1 text-center"><a href="#" onClick="deleteRow(this)"><i class="fas fa-trash" aria-hidden="true"></a></td></tr>';
           
    };
    function formatRows2() {
      return '<tr class="ros">' +
            // '<td class="col-xs-1"><input type="checkbox" class="curr_right" name="curr_right[]"  /></td>' +
            '<td class="col-xs-1">{{ Form::date('currency_date[]',date('Y-m-d'), ['class'=>'form-control ','readOnly','required','max'=>date('Y-m-d')]) }}</td>' +
            '<td class="col-xs-1">{{ Form::select('currency_name[]',$currencies,null,['class'=>'form-control  cur_name cur_select2  ' ,'placeholder'=> __("messages.please_select")  ]) }}</td>' +
            '<td class="col-xs-2">{{ Form::text('currency_symbol[]',null,['class'=>'form-control cur_symbol','readOnly' ]) }}</td>' +
            '<td class="col-xs-2">{{ Form::number('currency_amount[]',0,['class'=>'form-control currency_amount','required','step'=>'any','min'=>0]) }}</td>' +
            '<td class="col-xs-2">{{ Form::number('currency_opposit_amount[]',0,['class'=>'form-control currency_opposit_amount','readOnly','required','step'=>'any','min'=>0]) }}</td>' +
            '<td class="col-xs-2">{{ Form::select('cur_default[]',["1"=>"Default"],null,['class'=>'form-control  cur_default cur_select2  ','disabled' ,'placeholder'=> __("messages.please_select")  ])}}</td>' +
            '<td class="col-xs-1 text-center"><a href="#" onClick="deleteRow(this)"><i class="fas fa-trash" aria-hidden="true"></a></td></tr>';
           
    };
    function addRow() {
       var check =  $(".cur_defult_check").val();
        if(check==null){
            $(formatRows()).insertBefore('#addRow');
        }else{
            $(formatRows2()).insertBefore('#addRow');

        }
       $('.cur_select2').select2();
       update_symbol();
    }
    function update_symbol(){
        $(".cur_name").each(function(){
            var e = $(this).parent().parent() ;
            var ee = e.children().find(".cur_symbol");  
            var el = e.children().find(".cur_name");  
            var check  = e.children().find(".add_default");  
            var amount = e.children().find(".currency_amount");  
            var opposit_amount = e.children().find(".currency_opposit_amount");  
            var cur_default = e.children().find(".cur_default");  
             
            el.on("change",function(){
                id = $(this).val();
                name = "cur_default["+id+"]";
                cur_default.attr("name",name); 
                if(id!=""){
                    $.ajax({
                        url: '/symbol/' + id ,
                        dataType: 'html',
                        success: function ( data ) {
                            ee.val(data);  
                        }
                    });
                }else{
                    ee.val("");
                    amount.val(0);
                    opposit_amount.val(0);
                }
               
            });
            amount.on("change",function(){
                id = $(this).val();
                opposit_amount.val((1/id).toFixed(4));
                 
            });
            cur_default.on("change",function(){
                x_default = $(this).val();
                 check_box(el.val(),x_default);
            });
            // opposit_amount.on("change",function(){
            //     id = $(this).val();
            //     amount.val();
            // });
        });      
        $(".cur_name").each(function(){
            var e = $(this).parent().parent() ;
            var check_box = e.children().find(".curr_right");  
            var el = e.children().find(".cur_name"); 
            
            check_box.on("change",function(){
                    main  = $(this) ;
                    id    = el.val();
                    if(!main.attr("checked")){
                        main.attr("checked",true)
                    }else{
                        main.attr("checked",false);
                        $.ajax({
                            url: "/symbol-left-amount/"+id,
                            method: 'get',
                            dataType: 'html',
                            success: function(result) {
                                if (JSON.parse(result).success == true) {
                                    toastr.success(JSON.parse(result).msg);
                                } else {
                                    toastr.error(JSON.parse(result).msg);
                                }
                            },
                        });
                        
                    }
                    if(main.attr("checked")){
                        $.ajax({
                            url: "/symbol-right-amount/"+id,
                            method: 'get',
                            dataType: 'html',
                            success: function(result) {
                                console.log(JSON.parse(result))
                                if (JSON.parse(result).success == true) {
                                    toastr.success(JSON.parse(result).msg);
                                } else {
                                    toastr.error(JSON.parse(result).msg);
                                }
                            },
                        });
                    }
               
               
            });
        });      
    }
    function check_box(id,x_default){
        $(".cur_name").each(function(){
            var e = $(this).parent().parent() ;
            var ee = e.children().find(".cur_symbol");  
            var el = e.children().find(".cur_name");  
            var check  = e.children().find(".add_default");  
            var amount = e.children().find(".currency_amount");  
            var opposit_amount = e.children().find(".currency_opposit_amount");
            var ex_default = e.children().find(".cur_default");
            console.log( id + "__ " + el.val());
            if(x_default == ""){
                ex_default.removeAttr("disabled");
            }else{
                if(el.val() != id  ){
                    ex_default.attr("disabled",true);
                } 
            }
        });

    }
    $(document).on('ifChecked', 'input#enable_product_prices', function() {
             $(".prices").removeClass("hide");
    });
    $(document).on('ifUnchecked', 'input#enable_product_prices', function() {
             $(".prices").addClass("hide");
    });
    function deleteRow(trash) {
        $(trash).closest('tr').remove();
    };
    
}, 2000);
</script>
@endsection