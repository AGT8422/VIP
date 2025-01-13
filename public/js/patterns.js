$(document).ready(function() {
    
    

    var pattern_table = $("#patterns_table").DataTable({
        processing:     true,
        serverSide:     true,
        scrollY:      "75vh",
        scrollX:        true,
        scrollCollapse: true,
        "ajax": {
            "url": "/patterns-list",
            "data":  function ( d ) {
                d.location_id    = $("#location_id").val();
                d.pattern_name   = $("#pattern_name").val();
                d.invoice_scheme = $("#invoice_scheme").val();
                d.invoice_layout = $("#invoice_layout").val();
                d.pos            = $("#pos_id").val();
                d.pattern_type   = $("#pattern_type").val();
                // d.user_id        = $("#user_id").val();
                d = __datatable_ajax_callback(d);
            }
        },
        aaSorting: [[1, 'desc']],
        columns: [
            { data: 'actions', searchable: false, orderable: false },
            { data: 'name', name: 'name' },
            { data: 'location_id', name: 'location_id' },
            { data: 'invoice_scheme', name: 'invoice_scheme' },
            { data: 'invoice_layout', name: 'invoice_layout' },
            { data: 'pos', name: 'pos' },
            { data: 'type', name: 'type' },
            { data: 'created_at', name: 'created_at' },
            { data: 'user_id', name: 'user_id' },
        ],
        fnDrawCallback: function(oSettings) {
            __currency_convert_recursively($('#patterns_table'));
        },
        // "footerCallback": function ( row, data, start, end, display ) {}
    });
    var pos_index_table = $("#pos_index_table").DataTable({
        processing:     true,
        serverSide:     true,
        scrollY:      "75vh",
        scrollX:        true,
        scrollCollapse: true,
        "ajax": {
            "url": "/pos-branch",
            "data":  function ( d ) {
                d.location_id    = $("#location_id").val();
                d.pattern_name   = $("#pattern_name").val();
                d.invoice_scheme = $("#invoice_scheme").val();
                d.invoice_layout = $("#invoice_layout").val();
                d.pos            = $("#pos_id").val();
                // d.user_id        = $("#user_id").val();
                d = __datatable_ajax_callback(d);
            }
        },
        aaSorting: [[1, 'desc']],
        columns: [
            { data: 'actions', searchable: false, orderable: false },
            { data: 'name', name: 'name' },
            { data: 'pattern', name: 'pattern' },
            { data: 'store_id', name: 'store_id' },
            { data: 'invoice_scheme', name: 'invoice_scheme' },
            { data: 'main_cash_id', name: 'main_cash_id' },
            { data: 'cash_id', name: 'cash_id' },
            { data: 'main_visa_id', name: 'main_visa_id' },
            { data: 'visa_id', name: 'visa_id' },
            { data: 'created_at', name: 'created_at' },
         ],
        fnDrawCallback: function(oSettings) {
            __currency_convert_recursively($('#pos_index_table'));
        },
        // "footerCallback": function ( row, data, start, end, display ) {}
    });
    pattern_table.ajax.reload();
    pos_index_table.ajax.reload();

    $(document).on("change","#location_id,#pattern_type,#pattern_name,#invoice_scheme,#invoice_layout,#pos",function(){
        pattern_table.ajax.reload();
    });

    pattern_form = $('form#add_patterns');
    
    $('button#submit-pattern').click(function(e) {
        if (pattern_form.valid()) {
            window.onbeforeunload = null;
            $(this).attr('disabled', true);
            pattern_form.submit();
        }
    });

    $('table#patterns_table tbody').on('click', 'a.delete-patterns', function(e) {
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
                    method: 'get',
                    url: href,
                    dataType: 'json',
                    success: function(result) {
                        if (result.success == 1) {
                            toastr.success(result.msg);
                            pattern_table.ajax.reload();
                        } else {
                            toastr.error(result.msg);
                        }
                    },
                });
            }
        });
    });

    

});