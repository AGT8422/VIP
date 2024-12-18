$(document).ready(function() {
    $(document).on('change', '.purchase_quantity', function() {
       
        update_table_total($(this).closest('table'));
    });
    $(document).on('change', '.unit_price', function() {
        update_table_total($(this).closest('table'));
    });

    $('.os_exp_date').datepicker({
        autoclose: true,
        format: datepicker_date_format,
    });

    $(document).on('click', '.add_stock_row', function() {
        var tr = $(this).data('row-html');
        var key = parseInt($(this).data('sub-key'));
        tr = tr.replace(/\__subkey__/g, key);
        $(this).data('sub-key', key + 1);

        $(tr)
            .insertAfter($(this).closest('tr'))
            .find('.os_exp_date')
            .datepicker({
                autoclose: true,
                format: datepicker_date_format,
            });
    });

    $(document).on('click', '.add-opening-stock', function(e) {
        e.preventDefault();
        $.ajax({
            url: $(this).data('href'),
            dataType: 'html',
            success: function(result) {
                $('#opening_stock_modal')
                    .html(result)
                    .modal('show');
            },
        });
    });
});
    
    $(document).on("click",".button-close-open",function(e){
            var models = $("#opening_stock_modal");
            
            models.modal("hide");
    })
    
    $(document).on('click', '.close_btn', function(e) {
     
        $('#opening_stock_modal').modal('hide');
   
    });

//Re-initialize data picker on modal opening
$('#opening_stock_modal')
    .off()
    .on('shown.bs.modal', function(e) {
        $('.os_exp_date').datepicker({
            autoclose: true,
            format: datepicker_date_format,
        });
    });
var st_os = 0;
$(document).on('click', 'button#add_opening_stock_btn', function(e) {
    e.preventDefault();
    $('#add_opening_stock_btn').prop('disabled', true);
    var btn = $(this);
    var data = $('form#add_opening_stock_form').serialize();
    if(st_os == 0){
        st_os = 1;
        $.ajax({ 
        method: 'POST',
        url: $('form#add_opening_stock_form').attr('action'),
        dataType: 'json',
        data: data,
        beforeSend: function(xhr) {
            __disable_submit_button(btn);
        },
        success: function(result) {
            if (result.success == true) {
                $('#opening_stock_modal').modal('hide');
                toastr.success(result.msg);
            } else {
                toastr.error(result.msg);
            }
        },
    });
    }
    setTimeout(function() { 
        st_os = 0;
        $('#add_opening_stock_btn').prop('disabled', false);
    }, 2000);
    return false;
});
$(document).on('click', '#add_opening_stock_form tbody .rw .delete_stock_row', function(e) {
        var id = $(this).data("id");
 
        var el =  $(this).parent().parent().data("id",id);
        el.attr("disable",true);
        el.attr("hidden",true);
    }); 

function update_table_total(table) {
    var total_subtotal = 0;
    table.find('tbody tr').each(function() {
        var qty = __read_number($(this).find('.purchase_quantity'));
        var unit_price = __read_number($(this).find('.unit_price'));
        var row_subtotal = qty * unit_price;
        $(this)
            .find('.row_subtotal_before_tax')
            .text(__number_f(row_subtotal));
        total_subtotal += row_subtotal;
    });
    table.find('tfoot tr #total_subtotal').text(__currency_trans_from_en(total_subtotal, true));
    table.find('tfoot tr #total_subtotal_hidden').val(total_subtotal);
    
}
