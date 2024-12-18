$(document).ready(function() {
    
    

    var system_account_table = $("#system_account_table").DataTable({
       processing:     true,
       serverSide:     true,
       scrollY:      "75vh",
       scrollX:        true,
       scrollCollapse: true,
       "ajax": {
           "url": "/account/system-account-list",
           "data":  function ( d ) {
               d.location_id    = $("#location_id").val();
               d.pattern_id   = $("#pattern_id").val();
            //    d.invoice_scheme = $("#invoice_scheme").val();
            //    d.invoice_layout = $("#invoice_layout").val();
            //    d.pos            = $("#pos_id").val();
               // d.user_id        = $("#user_id").val();
               d = __datatable_ajax_callback(d);
           }
       },
       aaSorting: [[1, 'desc']],
       columns: [
           { data: 'actions', searchable: false, orderable: false },
           { data: 'name', name: 'name' },
           { data: 'location_id', name: 'location_id' },
           { data: 'created_at', name: 'created_at' },
           { data: 'user_id', name: 'user_id' },
       ],
       fnDrawCallback: function(oSettings) {
           __currency_convert_recursively($('#system_account_table'));
       },
       // "footerCallback": function ( row, data, start, end, display ) {}
   });
   system_account_table.ajax.reload();

   $(document).on("change","#location_id,#pattern_id",function(){
    system_account_table.ajax.reload();
   });

//    pattern_form = $('form#add_patterns');
   
//    $('button#submit-pattern').click(function(e) {
//        if (pattern_form.valid()) {
//            window.onbeforeunload = null;
//            $(this).attr('disabled', true);
//            pattern_form.submit();
//        }
//    });

//    $('table#system_account_table tbody').on('click', 'a.delete-patterns', function(e) {
//        e.preventDefault();
//        swal({
//            title: LANG.sure,
//            icon: 'warning',
//            buttons: true,
//            dangerMode: true,
//        }).then(willDelete => {
//            if (willDelete) {
//                var href = $(this).data('href');
//                $.ajax({
//                    method: 'get',
//                    url: href,
//                    dataType: 'json',
//                    success: function(result) {
//                        if (result.success == 1) {
//                            toastr.success(result.msg);
//                            pattern_table.ajax.reload();
//                        } else {
//                            toastr.error(result.msg);
//                        }
//                    },
//                });
//            }
//        });
//    });



});