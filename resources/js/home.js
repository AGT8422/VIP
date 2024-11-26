$(document).ready(function() {
    //day
    var start = $('input[name="date-filter"]:checked').data('start');
    var end = $('input[name="date-filter"]:checked').data('end');
     //month
     var start_month = $('input[name="date-filter-month"]:checked').data('start');
    var end_month = $('input[name="date-filter-month"]:checked').data('end');
     //year
     var start_year = $('input[name="date-filter-year"]:checked').data('start');
    var end_year = $('input[name="date-filter-year"]:checked').data('end');
   
    update_statistics(start, end);
    update_expense(start,end);
    update_expense_month(start_month,end_month);
    update_expense_year(start_year,end_year);
    update_statistics_month(start_month,end_month);
    update_statistics_year(start_year,end_year);

    $(document).on('change', 'input[name="date-filter"], #dashboard_location', function() {
        var start = $('input[name="date-filter"]:checked').data('start');
        var end = $('input[name="date-filter"]:checked').data('end');
        update_statistics(start, end);
        update_expense(start,end)
         //month
        var start_month = $('input[name="date-filter-month"]:checked').data('start');
        var end_month = $('input[name="date-filter-month"]:checked').data('end');
        update_statistics_month(start_month,end_month);
        update_expense_month(start_month,end_month);

        //year
        var start_year = $('input[name="date-filter-year"]:checked').data('start');
        var end_year = $('input[name="date-filter-year"]:checked').data('end');
        update_statistics_year(start_year,end_year);
        update_expense_year(start_year,end_year);

        if ($('#quotation_table').length && $('#dashboard_location').length) {
            quotation_datatable.ajax.reload();
        }
        
    });
     

    //atock alert datatables
    var stock_alert_table = $('#stock_alert_table').DataTable({
        processing: true,
        serverSide: true,
        ordering: false,
        searching: false,
        scrollY:        "75vh",
        scrollX:        true,
        scrollCollapse: true,
        fixedHeader: false,
        dom: 'Btirp',
        ajax: '/home/product-stock-alert',
        columns:[
            {data:"product" ,name:"product"},
            {data:"location",name:"location"},
            {data:"stock"   ,name:"stock"},
        ],
        fnDrawCallback: function(oSettings) {
            __currency_convert_recursively($('#stock_alert_table'));
        },
    });

    //payment dues datatables
    var purchase_payment_dues_table = $('#purchase_payment_dues_table').DataTable({
        processing: true,
        serverSide: true,
        ordering: false,
        searching: false,
        scrollY:        "75vh",
        scrollX:        true,
        scrollCollapse: true,
        fixedHeader: false,
        dom: 'Btirp',
        ajax: '/home/purchase-payment-dues',
        fnDrawCallback: function(oSettings) {
            __currency_convert_recursively($('#purchase_payment_dues_table'));
        },
    });

    //Sales dues datatables
    var sales_payment_dues_table = $('#sales_payment_dues_table').DataTable({
        processing: true,
        serverSide: true,
        ordering: false,
        searching: false,
        scrollY:        "75vh",
        scrollX:        true,
        scrollCollapse: true,
        fixedHeader: false,
        dom: 'Btirp',
        ajax: '/home/sales-payment-dues',
        fnDrawCallback: function(oSettings) {
            __currency_convert_recursively($('#sales_payment_dues_table'));
        },
    });

    //Stock expiry report table
    stock_expiry_alert_table = $('#stock_expiry_alert_table').DataTable({
        processing: true,
        serverSide: true,
        searching: false,
        scrollY:        "75vh",
        scrollX:        true,
        scrollCollapse: true,
        fixedHeader: false,
        dom: 'Btirp',
        ajax: {
            url: '/reports/stock-expiry',
            data: function(d) {
                d.exp_date_filter = $('#stock_expiry_alert_days').val();
            },
        },
        order: [[3, 'asc']],
        columns: [
            { data: 'product', name: 'p.name' },
            { data: 'location', name: 'l.name' },
            { data: 'stock_left', name: 'stock_left' },
            { data: 'exp_date', name: 'exp_date' },
        ],
        fnDrawCallback: function(oSettings) {
            __show_date_diff_for_human($('#stock_expiry_alert_table'));
            __currency_convert_recursively($('#stock_expiry_alert_table'));
        },
    });

    if ($('#quotation_table').length) {
        quotation_datatable = $('#quotation_table').DataTable({
            processing: true,
            serverSide: true,
            aaSorting: [[0, 'desc']],
            "ajax": {
                "url": '/sells/draft-dt?is_quotation=1',
                "data": function ( d ) {
                    if ($('#dashboard_location').length > 0) {
                        d.location_id = $('#dashboard_location').val();
                    }
                }
            },
            columnDefs: [ {
                "targets": 4,
                "orderable": false,
                "searchable": false
            } ],
            columns: [
                { data: 'transaction_date', name: 'transaction_date'  },
                { data: 'invoice_no', name: 'invoice_no'},
                { data: 'name', name: 'contacts.name'},
                { data: 'business_location', name: 'bl.name'},
                { data: 'action', name: 'action'}
            ]            
        });
    }
});

function update_statistics(start, end) {
     
    var location_id = '';
    if ($('#dashboard_location').length > 0) {
        location_id = $('#dashboard_location').val();
    }
    
    var data = { start: Date.now(), end: Date.now(), location_id: location_id };
    //get purchase details
    var loader = '<i class="fas fa-sync fa-spin fa-fw margin-bottom"></i>';
    $('.total_purchase').html(loader);
    $('.purchase_due').html(loader);
    $('.total_sell').html(loader);
    $('.invoice_due').html(loader);
    $('.total_expense').html(loader);
    $.ajax({
        method: 'get',
        url: '/home/get-totals',
        dataType: 'json',
        data: data,
        success: function(data) {
            //purchase details
            $('.total_purchase').html(__currency_trans_from_en(data.total_purchase, true));
            $('.purchase_due').html(__currency_trans_from_en(data.purchase_due, true));

            //sell details
            $('.total_sell').html(__currency_trans_from_en(data.total_sell, true));
            $('.invoice_due').html(__currency_trans_from_en(data.invoice_due, true));
            //expense details
            // $('.total_expense').html(__currency_trans_from_en(data.total_expense, true));
        },
    });
}
function format_date(date, format) {
    var year = date.getFullYear();
    var month = pad_zero(date.getMonth() + 1);
    var day = pad_zero(date.getDate());
    var hour = pad_zero(date.getHours());
    var minute = pad_zero(date.getMinutes());
    var second = pad_zero(date.getSeconds());
  
    format = format.replace(/yyyy/g, year);
    format = format.replace(/MM/g, month);
    format = format.replace(/dd/g, day);
    format = format.replace(/HH/g, hour);
    format = format.replace(/mm/g, minute);
    format = format.replace(/ss/g, second);
  
    return format;
  }
  
  function pad_zero(num) {
    return num < 10 ? '0' + num : num;
  }

function update_statistics_month(start, end) {
     
    var location_id = '';
    if ($('#dashboard_location').length > 0) {
        location_id = $('#dashboard_location').val();
    }
    var data = { start: start, end: end, location_id: location_id };
    //get purchase details
    var loader = '<i class="fas fa-sync fa-spin fa-fw margin-bottom"></i>';
    $('.total_purchase').html(loader);
    $('.purchase_due').html(loader);
    $('.total_sell').html(loader);
    $('.invoice_due').html(loader);
    $('.total_expense').html(loader);
    $.ajax({
        method: 'get',
        url: '/home/get-totals?month=1',
        dataType: 'json',
        data: data,
        success: function(data) {
            //purchase details
            $('.total_purchase_month').html(__currency_trans_from_en(data.total_purchase, true));
            $('.purchase_due_month').html(__currency_trans_from_en(data.purchase_due, true));

            //sell details
            $('.total_sell_month').html(__currency_trans_from_en(data.total_sell, true));
            $('.invoice_due_month').html(__currency_trans_from_en(data.invoice_due, true));
            //expense details
            // $('.total_expense_month').html(__currency_trans_from_en(data.total_expense, true));
            
        },
    });
}
function update_statistics_year(start, end) {
    var location_id = '';
    if ($('#dashboard_location').length > 0) {
        location_id = $('#dashboard_location').val();
    }
    var data = { start: start, end: end, location_id: location_id };
    //get purchase details
    var loader = '<i class="fas fa-sync fa-spin fa-fw margin-bottom"></i>';
    $('.total_purchase').html(loader);
    $('.purchase_due').html(loader);
    $('.total_sell').html(loader);
    $('.invoice_due').html(loader);
    $('.total_expense').html(loader);
    $.ajax({
        method: 'get',
        url: '/home/get-totals?year=1',
        dataType: 'json',
        data: data,
        success: function(data) {
            //purchase details
            $('.total_purchase_year').html(__currency_trans_from_en(data.total_purchase, true));
            $('.purchase_due_year').html(__currency_trans_from_en(data.purchase_due, true));

            //sell details
            $('.total_sell_year').html(__currency_trans_from_en(data.total_sell, true));
            $('.invoice_due_year').html(__currency_trans_from_en(data.invoice_due, true));
            //expense details
            // $('.total_expense_year').html(__currency_trans_from_en(data.total_expense, true));
        },
    });

}
function update_expense(start,end){
 
    var location_id = '';
    if ($('#dashboard_location').length > 0) {
        location_id = $('#dashboard_location').val();
    }
    var data = { start: start, end: end, location_id: location_id };
    //get purchase details
    var loader = '<i class="fas fa-sync fa-spin fa-fw margin-bottom"></i>';
    $('.total_purchase').html(loader);
    $('.purchase_due').html(loader);
    $('.total_sell').html(loader);
    $('.invoice_due').html(loader);
    $('.total_expense').html(loader);
    $.ajax({
        method: 'get',
        url: '/home/get-expense',
        dataType: 'json',
        data: data,
        success: function(data) {
            //expense details
             $('.total_expense').html(__currency_trans_from_en(data, true));
        },
    });

}
function update_expense_month(start,end){
 
    var location_id = '';
    if ($('#dashboard_location').length > 0) {
        location_id = $('#dashboard_location').val();
    }
    var data = { start: start, end: end, location_id: location_id };
    //get purchase details
    var loader = '<i class="fas fa-sync fa-spin fa-fw margin-bottom"></i>';
    $('.total_purchase').html(loader);
    $('.purchase_due').html(loader);
    $('.total_sell').html(loader);
    $('.invoice_due').html(loader);
    $('.total_expense').html(loader);
    $.ajax({
        method: 'get',
        url: '/home/get-expense',
        dataType: 'json',
        data: data,
        success: function(data) {
            //expense details
             $('.total_expense_month').html(__currency_trans_from_en(data, true));
        },
    });

}
function update_expense_year(start,end){
   
    var location_id = '';
    if ($('#dashboard_location').length > 0) {
        location_id = $('#dashboard_location').val();
    }
    var data = { start: start, end: end, location_id: location_id };
    //get purchase details
    var loader = '<i class="fas fa-sync fa-spin fa-fw margin-bottom"></i>';
    $('.total_purchase').html(loader);
    $('.purchase_due').html(loader);
    $('.total_sell').html(loader);
    $('.invoice_due').html(loader);
    $('.total_expense').html(loader);
    $.ajax({
        method: 'get',
        url: '/home/get-expense',
        dataType: 'json',
        data: data,
        success: function(data) {
            //expense details
             $('.total_expense_year').html(__currency_trans_from_en(data, true));
        },
    });

}
