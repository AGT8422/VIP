$(document).ready(function(){ 
    user_activate_table = $("#user_activation_table").DataTable({
            processing : true,
            serverSide : true,
            scrollY : true,
            scrollX : true,
            scrollCollaps : true,
            ajax : {
                url : '/user-activation/',
                data : function (d) {
                    d.user_name = $("#user_name").val();
                    d.user_email = $("#user_email").val();
                    d.user_address = $("#user_address").val();
                    d.user_mobile = $("#user_mobile").val();
                    d.user_username = $("#user_username").val();
                    d.user_status = $("#user_status").val();
                    d.user_service = $("#user_services").val();
                    d.user_products = $("#user_products").val();
                    d.user_date = $("#user_date").val();
                    // alert(JSON.stringify(d));
                    check_code();
                    d = __datatable_ajax_callback(d);
                }, 
            },
            aaSorting: [[0,'desc']],
            columns: [
                {data: 'id',name: 'id'},
                {data: 'user_name',name: 'user_name'},
                {data: 'user_email',name: 'user_email'},
                {data: 'company_name',name: 'company_name'},
                {data: 'user_address',name: 'user_address'},
                {data: 'user_mobile',name: 'user_mobile'},
                {data: 'user_username',name: 'user_username'},
                {data: 'user_password',name: 'user_password',class:"hide"},
                {data: 'user_activateion_code',name: 'user_activateion_code'},
                {data: 'user_status',name: 'user_status'},
                {data: 'user_service',name: 'user_service'},
                {data: 'user_products',name: 'user_products'},
                {data: 'user_dateactivate',name: 'user_dateactivate'},
                {data: 'created_at',name: 'created_at'},
                {data: 'user_payment',name: 'user_payment'},
                {data: 'user_due_payment',name: 'user_due_payment'},
                {data: 'user_number_device',name: 'user_number_device'},
                {data: 'user_token',name: 'user_token',class:"max-width:10px hide"},
                {data: 'activation_period',name: 'activation_period' },
            ],
            fnDrawCallback:function(oSettings){
                __currency_convert_recursively($("#user_activation_table"));
                check_code();
            },  
            "footerCallback": function( row,data,start,end,display){
                var users_count = 0;
                for( var r in data){
                    users_count++;                        
                }
                check_code();
                $("#footer_total_users").html(LANG.user_count + " :  " + users_count);
                // $("#footer_status").val("");
                // $("#footer_payment").val("");
                // $("#footer_due_payment").val("");
            },
        })
    user_activate_request_table = $("#user_activation_request_table").DataTable({
        processing : true,
        serverSide : true,
        scrollY : true,
        scrollX : true,
        scrollCollaps : true,
        ajax : {
            url : '/user-activation/shows',
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
            {data: 'type'    ,name: 'type'},
            {data: 'company_name'   ,name: 'company_name'},
            {data: 'device_no' ,name: 'device_no'},
            {data: 'email'  ,name: 'email'},
            {data: 'address',name: 'address'},
            {data: 'mobile' ,name: 'mobile'},
            {data: 'services' ,name: 'services'},
            {data: 'created_at'   ,name: 'created_at'},
         ],
        fnDrawCallback:function(oSettings){
            __currency_convert_recursively($("#user_activation_request_table"));
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
            {data: 'type'    ,name: 'type'},
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
    user_activate_request_table.ajax.reload();
    user_activation_login_request_table.ajax.reload();
})
$(document).on("change","#user_name,#user_email,#user_address,#user_mobile,#user_username,#user_status,#user_services,#user_products,#user_date",function(){
    user_activate_table.ajax.reload();
    user_activation_login_request_table.ajax.reload();
 })
function check_code(){
    $("#user_activation_table tbody #check-code-activate").each(function(){
            var i  = $(this);
            var e  = $(this).parent().parent();
            var username = e.children().find("#user-name-check").html();
            var password = e.children().find("#user-password-check").html();
            var correct  = e.children().find("span.correct-activation");
            var wrang    = e.children().find("span.wrang-activation");
            // alert(correct.html());
            i.on("change",function(){
                ee = $(this).val();
                if(ee != ""){
                    $.ajax({
                        url:"/check-activation?val="+ee+"&username="+username,
                        dataType:"html",
                        success: function(result){
                            var object  = JSON.parse(result);
                            if(object.success == 1){
                                correct.removeClass("hide");
                                wrang.addClass("hide");
                            }else if(object.success == 0){
                                correct.addClass("hide");
                                wrang.removeClass("hide");
                            }else{
                                correct.addClass("hide");
                                wrang.addClass("hide");
                            }
                        }
                    });
                }else{
                    correct.addClass("hide");
                    wrang.addClass("hide");
                }
                
            });
        });
}

