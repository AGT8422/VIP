$(document).ready(function() {
    
    

    var mobile_app_table = $("#mobile_table_app").DataTable({
       processing:     true,
       serverSide:     true,
       scrollY:      "75vh",
       scrollX:        true,
       scrollCollapse: true,
       "ajax": {
           "url": "/get-api",
           "data":  function ( d ) {
               d.name    = $("#name").val();
               d.surename = $("#surename").val();
               d.username   = $("#username").val();
               d.device_ip = $("#device_ip").val();
               d.device_id            = $("#device_id").val();
               d.device_id            = $("#device_id").val();
               d = __datatable_ajax_callback(d);
           }
       },
       aaSorting: [[1, 'desc']],
       columns: [
           { data: 'actions', searchable: false, orderable: false },
           { data: 'name', name: 'name' },
           { data: 'surename', name: 'surename' },
           { data: 'username', name: 'username' },
           { data: 'device_id', name: 'device_id' },
           { data: 'device_ip', name: 'device_ip' },
           { data: 'lastlogin', name: 'lastlogin' },
           { data: 'created_at', name: 'created_at' },
       ],
       fnDrawCallback: function(oSettings) {
           __currency_convert_recursively($('#mobile_table_app'));
       },
       // "footerCallback": function ( row, data, start, end, display ) {}
   });
//     var pos_index_table = $("#pos_index_table").DataTable({
//        processing:     true,
//        serverSide:     true,
//        scrollY:      "75vh",
//        scrollX:        true,
//        scrollCollapse: true,
//        "ajax": {
//            "url": "/pos-branch",
//            "data":  function ( d ) {
//                d.location_id    = $("#location_id").val();
//                d.pattern_name   = $("#pattern_name").val();
//                d.invoice_scheme = $("#invoice_scheme").val();
//                d.invoice_layout = $("#invoice_layout").val();
//                d.pos            = $("#pos_id").val();
//                // d.user_id        = $("#user_id").val();
//                d = __datatable_ajax_callback(d);
//            }
//        },
//        aaSorting: [[1, 'desc']],
//        columns: [
//            { data: 'actions', searchable: false, orderable: false },
//            { data: 'name', name: 'name' },
//            { data: 'pattern', name: 'pattern' },
//            { data: 'store_id', name: 'store_id' },
//            { data: 'invoice_scheme', name: 'invoice_scheme' },
//            { data: 'main_cash_id', name: 'main_cash_id' },
//            { data: 'cash_id', name: 'cash_id' },
//            { data: 'main_visa_id', name: 'main_visa_id' },
//            { data: 'visa_id', name: 'visa_id' },
//            { data: 'created_at', name: 'created_at' },
//         ],
//        fnDrawCallback: function(oSettings) {
//            __currency_convert_recursively($('#pos_index_table'));
//        },
//        // "footerCallback": function ( row, data, start, end, display ) {}
//    });
   mobile_app_table.ajax.reload();
//    pos_index_table.ajax.reload();

   $(document).on("change","#name,#username,#surename,#device_ip,#device_id",function(){
    mobile_app_table.ajax.reload();
   });

   pattern_form = $('form#add_customers');
   
   $('button#submit-mobile').click(function(e) {
       if (pattern_form.valid()) {
           window.onbeforeunload = null;
           $(this).attr('disabled', true);
           pattern_form.submit();
       }
   });
    

   $('table#mobile_table_app tbody').on('click', 'a.delete-mobile', function(e) {
       e.preventDefault();
       swal({
           title: LANG.sure,
           icon: 'warning',
           buttons: true,
           dangerMode: true,
       }).then(willDelete => {
           if (willDelete) {
               var href = $(this).data('href');
               $.ajax({
                   method: 'delete',
                   url: href,
                   dataType: 'json',
                   success: function(result) {
                       if (result.success == 1) {
                           toastr.success(result.msg);
                           mobile_app_table.ajax.reload();
                       } else {
                           toastr.error(result.msg);
                       }
                   },
               });
           }
       });
   });



});