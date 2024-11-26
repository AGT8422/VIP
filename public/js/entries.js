$(document).ready(function(){
 
    entries_table = $("#entries_table").DataTable({
            processing:true,
            serverSide:true,
            scrollY: "75vh",
            scrollX:        true,
            scrollCollapse: true,
            ajax :{
                 url  : '/account/entries/list',
                 data :function(d){
                    d.refe_no_e    = $("#refe_no_e").val();
                    d.ref_no_e     = $("#ref_no_e").val();
                    d.state      = $("#state").val();
                    var start = '';
                    var end = '';
                    if ($('#purchase_list_filter_date_range').val()) {
                        start = $('input#purchase_list_filter_date_range')
                            .data('daterangepicker')
                            .startDate.format('YYYY-MM-DD');
                        end = $('input#purchase_list_filter_date_range')
                            .data('daterangepicker')
                            .endDate.format('YYYY-MM-DD');
                    }
                    d.start_date = start;
                    d.end_date = end;
                    d = __datatable_ajax_callback(d);
                 },
            },
            aaSorting: [[1, 'desc']],
            columns:[
                {data:"action",name:"action" , searchable:false,orderable:false},
                {data:"refe_no_e",name:"refe_no_e"},
                {data:"ref_no_e",name:"ref_no_e"},
                {data:"state",name:"state"},
                {data:"created_at",name:"created_at"},
            ],
            fnDrawCallback: function(oSettings) {
                __currency_convert_recursively($('#entries_table'));
            },
            "footerCallback": function ( row, data, start, end, display ) {
                var total_entry  = 0;
                for (var r in data){
                    total_entry++;
                }
                $('.footer_entry_total').html(total_entry + "    " +  LANG.Entries);
            },
        })

        $(document).on('change','#refe_no_e,#ref_no_e,#state,#created_at',function() {
            entries_table.ajax.reload();
            }
        );
        $('#purchase_list_filter_date_range').daterangepicker(
            dateRangeSettings,
            function (start, end) {
                $('#purchase_list_filter_date_range').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
                entries_table.ajax.reload();
            }
        );
        $('#purchase_list_filter_date_range').on('cancel.daterangepicker', function(ev, picker) {
            $('#purchase_list_filter_date_range').val('');
            entries_table.ajax.reload();
        });
})