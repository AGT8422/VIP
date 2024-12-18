$(document).ready(function(){
    status_table = $("#status_table").DataTable({
        processing:true,
        serverSide:true,
        aaSorting:[[1,"desc"]],
        "ajax":{
            "url":"/status-live",
            "data":function(d){
                d.reference_no = $("#reference_no").val();
                d = __datatable_ajax_callback(d);
            },
        },
        columns:[
            {data:"action",name:"action" ,orderable: false, "searchable": false},
            {data:"reference_no",name:"reference_no"},
            {data:"created_at",name:"created_at"},
            
        ],
        scrollY:        "75vh",
        scrollX:        true,
        scrollCollapse: true,
        "fnDrawCallback": function (oSettings) {
            __currency_convert_recursively($('#status_table'));
        },
        "footerCallback": function ( row, data, start, end, display ) {
            var footer_status_total = 0;
            for (var r in data){
                footer_status_total += 1;
            } 
            $('.status_footer').html(  footer_status_total );
           
        },

    })
    $(document).on("change","#reference_no",function(){
        status_table.ajax.reload();
    })
});