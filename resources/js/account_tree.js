$(document).ready(function() {

    purchase_table = $('#account_tree_table').DataTable({
        processing: true,
        serverSide: true,
        scrollY: "75vh",
        scrollX:        true,
        scrollCollapse: true,
        ajax: {
            url: '/account_tree/get_account',
            data: function(d) {

            },
        },
        aaSorting: [[1, 'desc']],
        columns: [
             
            { data: 'transaction_date', name: 'transaction_date' },
            { data: 'ref_no', name: 'ref_no' },
            { data: 'location_name', name: 'BS.name' },
            { data: 'name', name: 'contacts.name' },
            { data: 'status', name: 'status' },
            { data: 'payment_status', name: 'payment_status' },
            { data: 'recieved_status', name: 'recieved_status' },
            { data: 'warehouse', name: 'warehouse',orderable: false, searchable: false },
            { data: 'final_total', name: 'final_total' },
            { data: 'payment_due', name: 'payment_due', orderable: false, searchable: false },
            { data: 'added_by', name: 'u.first_name' },
        ],
        fnDrawCallback: function(oSettings) {
            __currency_convert_recursively($('#purchase_table'));
        },
        "footerCallback": function ( row, data, start, end, display ) {
            var total_purchase = 0;
            var total_due = 0;
            var total_purchase_return_due = 0;
            for (var r in data){
                total_purchase += $(data[r].final_total).data('orig-value') ? 
                parseFloat($(data[r].final_total).data('orig-value')) : 0;
                var payment_due_obj = $('<div>' + data[r].payment_due + '</div>');
                total_due += payment_due_obj.find('.payment_due').data('orig-value') ? 
                parseFloat(payment_due_obj.find('.payment_due').data('orig-value')) : 0;

                total_purchase_return_due += payment_due_obj.find('.purchase_return').data('orig-value') ? 
                parseFloat(payment_due_obj.find('.purchase_return').data('orig-value')) : 0;
            }

            $('.footer_purchase_total').html(__currency_trans_from_en(total_purchase));
            $('.footer_total_due').html(__currency_trans_from_en(total_due));
            $('.footer_total_purchase_return_due').html(__currency_trans_from_en(total_purchase_return_due));
            $('.footer_status_count').html(__count_status(data, 'status'));
            $('.footer_payment_status_count').html(__count_status(data, 'payment_status'));
        },
        createdRow: function(row, data, dataIndex) {
            $(row)
                .find('td:eq(5)')
                .attr('class', 'clickable_td');
        },
    });
});