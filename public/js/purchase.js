// *************** $$$ *************** \\
// |-----------  PURCHASE -----------| \\
// ----------------------- | AGT8422 | \\
$(document).ready(function() {

    if($("#edit_page").val()!=null){ 
        os_total_sub();
        os_grand();
    }

    if ($('input#iraqi_selling_price_adjustment').length > 0) {
        iraqi_selling_price_adjustment = true;
    } else {
        iraqi_selling_price_adjustment = false;
    }

    //Date picker
    $('#transaction_date').datetimepicker({
        format: moment_date_format + ' ' + moment_time_format,
        ignoreReadonly: true,
    });

    //get suppliers
    $('#supplier_id').select2({
        ajax: {
            url: '/purchases/get_suppliers',
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return {
                    q: params.term, // search term
                    page: params.page,
                };
            },
            processResults: function(data) {
                return {
                    results: data,
                };
            },
        },
        minimumInputLength: 1,
        escapeMarkup: function(m) {
            return m;
        },
        templateResult: function(data) {
            
             
            if (!data.id) {
                return data.text;
            }
            var html = data.text + ' - ' + data.business_name + ' (' + data.contact_id + ')';
          
            return html;
        },
        language: {
            noResults: function() {
                var name = $('#supplier_id')
                    .data('select2')
                    .dropdown.$search.val();
                return (
                    '<button type="button" data-name="' +
                    name +
                    '" class="btn btn-link add_new_supplier"><i class="fa fa-plus-circle fa-lg" aria-hidden="true"></i>&nbsp; ' +
                    __translate('add_name_as_new_supplier', { name: name }) +
                    '</button>'
                );
            },
        },
    }).on('select2:select', function (e) {
     
        var data = e.params.data;
        $('#pay_term_number').val(data.pay_term_number);
        $('#pay_term_type').val(data.pay_term_type);
        $('#advance_balance_text').text(__currency_trans_from_en(data.balance), true);
        $('#advance_balance').val(data.balance);
        set_supplier_address(data);
        setTimeout(() => {
            total_bill();
          }, 1000);
    });
    
    //Quick add supplier
    $(document).on('click', '.add_new_supplier', function() {
        $('#supplier_id').select2('close');
        var name = $(this).data('name');
         
        $('.contact_modal')
            .find('input#name')
            .val(name);
        $('.contact_modal')
            .find('select#contact_type')
            .val('supplier')
            .closest('div.contact_type_div')
            .addClass('hide');
        $('.contact_modal').modal('show');
    });

    $('form#quick_add_contact')
            .submit(function(e) {
                $('#contact-submit').remove();
                 e.preventDefault();
                })
            .validate({
            rules: {
                contact_id: {
                    remote: {
                        url: '/contacts/check-contact-id',
                        type: 'post',
                        data: {
                            contact_id: function() {
                                return $('#contact_id').val();
                            },
                            hidden_id: function() {
                                if ($('#hidden_id').length) {
                                    return $('#hidden_id').val();
                                } else {
                                    return '';
                                }
                            },
                        },
                    },
                },
            },
            messages: {
                contact_id: {
                    remote: LANG.contact_id_already_exists,
                },
            },
            submitHandler: function(form) {
                var data = $(form).serialize();
                $.ajax({
                    method: 'POST',
                    url: $(form).attr('action'),
                    dataType: 'json',
                    data: data,
                    beforeSend: function(xhr) {
                        __disable_submit_button($(form).find('button[type="submit"]'));
                    },
                    success: function(result) {
                        if (result.success == true) {
                            
                            $('select#supplier_id').append(
                                $('<option>', { value: result.data.id, text: result.data.first_name })
                            );
                            $('select#supplier_id')
                                .val(result.data.id)
                                .trigger('change');
                            $('div.contact_modal').modal('hide');
                            set_supplier_address(result.data);
                            toastr.success(result.msg);
                        } else {
                            toastr.error(result.msg);
                        }
                    },
                });
            },
    });

    $('.contact_modal').on('hidden.bs.modal', function() {
        $('form#quick_add_contact')
            .find('button[type="submit"]')
            .removeAttr('disabled');
        $('form#quick_add_contact')[0].reset();
    });

    //Add products
    if ($('#search_product').length > 0) {
        $('#search_product')
            .autocomplete({
                source: function(request, response) {
                    $.getJSON(
                        '/purchases/get_products',
                        { location_id: $('#location_id').val(), term: request.term },
                        response
                    );
                },
                minLength: 2,
                response: function(event, ui) {
                    total_bill();
                    if (ui.content.length == 1) {
                        ui.item = ui.content[0];
                        $(this)
                            .data('ui-autocomplete')
                            ._trigger('select', 'autocompleteselect', ui);
                        $(this).autocomplete('close');
                    } else if (ui.content.length == 0) {
                        var term = $(this).data('ui-autocomplete').term;
                        swal({
                            title: LANG.no_products_found,
                            text: __translate('add_name_as_new_product', { term: term }),
                            buttons: [LANG.cancel, LANG.ok],
                        }).then(value => {
                            if (value) {
                                var container = $('.quick_add_product_modal');
                                $.ajax({
                                    url: '/products/quick_add?product_name=' + term,
                                    dataType: 'html',
                                    success: function(result) {
                                        $(container)
                                            .html(result)
                                            .modal('show');
                                    },
                                });
                            }
                        });
                    }
                },
                select: function(event, ui) {
                    $(this).val(null);
                    curr = $(".currency_id_amount").val();
                    get_purchase_entry_row(ui.item.product_id, ui.item.variation_id,null,curr);
                    total_bill();
                },
            })
            .autocomplete('instance')._renderItem = function(ul, item) {
            return $('<li>')
                .append('<div>' + item.text + '</div>')
                .appendTo(ul);
        };
    }

    $(document).on('click', '.remove_purchase_entry_row', function() {
        swal({
            title: LANG.sure,
            icon: 'warning',
            buttons: true,
            dangerMode: true,
        }).then(value => {
            if (value) {
                $(this)
                    .closest('tr')
                    .remove();
                var total = $(this).parent().parent().children().find(".purchase_unit_cost_after_tax_").val();
                var qty   = $(this).parent().parent().children().find(".purchase_quantity").val();
                $("#total_amount").val($("#total_amount").val()-parseFloat(total));
                $("#total_qty_eb").val($("#total_qty_eb").val()-parseFloat(qty));
                update_table_total();
                update_grand_total();
                update_table_sr_number();
                update_down();
                os_total_sub();
                os_grand();
            }
        });
    });

    // $('.purchase_table .purchase_unit_cost_with_tax').on("change",function(){
    //     var tax_amount  = parseFloat($('#tax_id option:selected').data('tax_amount')) ;
    //     var el          =  $(this).parent().parent();
    //     var price       =  parseFloat($(this).val())/((tax_amount/100)+1) ;
    //     el.children().find('.purchase_unit_cost_without_discount_s').val(price.toFixed(2));
    //     el.children().find('.purchase_unit_cost_without_discount').val(price.toFixed(2));
    //     os_total_sub();
    //     os_grand();
    // });
    $('.purchase_table .purchase_unit_cost_without_discount').on("change",function(){
        var tax_amount  = parseFloat($('#tax_id option:selected').data('tax_amount')) ;
        var el          =  $(this).parent().parent();
        var price       =   parseFloat($(this).val())*(tax_amount/100) + parseFloat($(this).val()) ; 
        el.children().find('.purchase_unit_cost_without_discount_origin').val($(this).val());
        el.children().find('.purchase_unit_cost_with_tax').val(price.toFixed(2));
        var intial_avl =  parseFloat($(this).val());
            os_total_sub();
             os_grand();
              
        el.children().find('.purchase_unit_cost_without_discount_s').attr("type","text");
        el.children().find('.purchase_unit_cost_without_discount').attr("type","hidden");
        
    });
   
    //On Change of quantity
    $(document).on('change', '.purchase_quantity',  function() {
       
        os_total_sub();
        os_grand();
    
    });

    $(document).on('change', '.inline_discounts', function() {
        
        os_total_sub();
        os_grand();
        //update_grand_total();
    });
    $(document).on('change', '.purchase_unit_cost', function() {
        var row = $(this).closest('tr');
        var quantity = __read_number(row.find('input.purchase_quantity'), true);
        var purchase_before_tax = __read_number($(this), true);

        var sub_total_before_tax = quantity * purchase_before_tax;

        //Update unit cost price before discount
        var discount_percent = __read_number(row.find('input.inline_discounts'), true);
        var purchase_before_discount = __get_principle(purchase_before_tax, discount_percent, true);
        __write_number(
            row.find('input.purchase_unit_cost_without_discount'),
            purchase_before_discount,
            true
        );
        __write_number(
            row.find('input.purchase_unit_cost_without_discount_origin'),
            purchase_before_discount,
            true
        );

        //Tax
        var tax_rate = parseFloat(
            row
                .find('select.purchase_line_tax_id')
                .find(':selected')
                .data('tax_amount')
        );
        var tax = __calculate_amount('percentage', tax_rate, purchase_before_tax);

        var purchase_after_tax = purchase_before_tax + tax;
        var sub_total_after_tax = quantity * purchase_after_tax;

        row.find('.row_subtotal_before_tax').text(
            __currency_trans_from_en(sub_total_before_tax, false, true)
        );
        __write_number(
            row.find('input.row_subtotal_before_tax_hidden'),
            sub_total_before_tax,
            true
        );

        row.find('.purchase_product_unit_tax_text').text(
            __currency_trans_from_en(tax, false, true)
        );
        __write_number(row.find('input.purchase_product_unit_tax'), tax, true);

        //row.find('.purchase_product_unit_tax_text').text( tax );
        __write_number(row.find('input.purchase_unit_cost_after_tax'), purchase_after_tax, true);
        row.find('.row_subtotal_after_tax').text(
            __currency_trans_from_en(sub_total_after_tax, false, true)
        );
        __write_number(row.find('input.row_subtotal_after_tax_hidden'), sub_total_after_tax, true);

        update_inline_profit_percentage(row);
        update_table_total();
        update_grand_total();
    });
    $(document).on('change', 'select.purchase_line_tax_id', function() {
        var row = $(this).closest('tr');
        var purchase_before_tax = __read_number(row.find('.purchase_unit_cost'), true);
        var quantity = __read_number(row.find('input.purchase_quantity'), true);

        //Tax
        var tax_rate = parseFloat(
            $(this)
                .find(':selected')
                .data('tax_amount')
        );
        var tax = __calculate_amount('percentage', tax_rate, purchase_before_tax);

        //Purchase price
        var purchase_after_tax = purchase_before_tax + tax;
        var sub_total_after_tax = quantity * purchase_after_tax;

        row.find('.purchase_product_unit_tax_text').text(
            __currency_trans_from_en(tax, false, true)
        );
        __write_number(row.find('input.purchase_product_unit_tax'), tax, true);

        __write_number(row.find('input.purchase_unit_cost_after_tax'), purchase_after_tax, true);

        row.find('.row_subtotal_after_tax').text(
            __currency_trans_from_en(sub_total_after_tax, false, true)
        );
        __write_number(row.find('input.row_subtotal_after_tax_hidden'), sub_total_after_tax, true);

        update_table_total();
        update_grand_total();
    });
    $(document).on('change', '.purchase_unit_cost_after_tax', function() {
        var row = $(this).closest('tr');
        var purchase_after_tax = __read_number($(this), true);
        var quantity = __read_number(row.find('input.purchase_quantity'), true);

        var sub_total_after_tax = purchase_after_tax * quantity;

        //Tax
        var tax_rate = parseFloat(
            row
                .find('select.purchase_line_tax_id')
                .find(':selected')
                .data('tax_amount')
        );
        var purchase_before_tax = __get_principle(purchase_after_tax, tax_rate);
        var sub_total_before_tax = quantity * purchase_before_tax;
        var tax = __calculate_amount('percentage', tax_rate, purchase_before_tax);

        //Update unit cost price before discount
        var discount_percent = __read_number(row.find('input.inline_discounts'), true);
        var purchase_before_discount = __get_principle(purchase_before_tax, discount_percent, true);
        __write_number(
            row.find('input.purchase_unit_cost_without_discount'),
            purchase_before_discount,
            true
        );
        __write_number(
            row.find('input.purchase_unit_cost_without_discount_origin'),
            purchase_before_discount,
            true
        );

        row.find('.row_subtotal_after_tax').text(
            __currency_trans_from_en(sub_total_after_tax, false, true)
        );
        __write_number(row.find('input.row_subtotal_after_tax_hidden'), sub_total_after_tax, true);

        __write_number(row.find('.purchase_unit_cost'), purchase_before_tax, true);

        row.find('.row_subtotal_before_tax').text(
            __currency_trans_from_en(sub_total_before_tax, false, true)
        );
        __write_number(
            row.find('input.row_subtotal_before_tax_hidden'),
            sub_total_before_tax,
            true
        );

        row.find('.purchase_product_unit_tax_text').text(__currency_trans_from_en(tax, true, true));
        __write_number(row.find('input.purchase_product_unit_tax'), tax);

        update_table_total();
        update_grand_total();
    });
    $(' input#shipping_charges').change(function() {
        //update_grand_total();
        os_total_sub();
        os_grand();
    });
    //Purchase table
    purchase_table = $('#purchase_table').DataTable({
        processing: true,
        serverSide: true,
        scrollY: "75vh",
        scrollX:        true,
        scrollCollapse: true,
        ajax: {
            url: '/purchases',
            data: function(d) {
                if ($('#purchase_list_filter_location_id').length) {
                    d.location_id = $('#purchase_list_filter_location_id').val();
                }
                if ($('#purchase_list_filter_supplier_id').length) {
                    d.supplier_id = $('#purchase_list_filter_supplier_id').val();
                }
                if ($('#purchase_list_filter_receipt').length) {
                    d.sup_refe = $('#purchase_list_filter_receipt').val();
                }
                if ($('#purchase_list_filter_payment_status').length) {
                    d.payment_status = $('#purchase_list_filter_payment_status').val();
                }
                if ($('#purchase_list_filter_status').length) {
                    d.status = $('#purchase_list_filter_status').val();
                }

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
        pageLength : -1,
        aaSorting: [[1, 'desc']],
        columns: [
            { data: 'action', name: 'action', orderable: false, searchable: false },
            { data: 'transaction_date', name: 'transaction_date' },
            { data: 'sup_refe', name: 'sup_refe' },
            { data: 'ref_no', name: 'ref_no' },
            { data: 'location_name', name: 'BS.name' , class:"hide" },
            { data: 'name', name: 'contacts.name' },
            { data: 'status', name: 'status' },
            { data: 'payment_status', name: 'payment_status' },
            { data: 'recieved_status', name: 'recieved_status' },
            { data: 'warehouse', name: 'warehouse',orderable: false, searchable: false },
            { data: 'tax_amount', name: 'tax_amount',orderable: false, searchable: false },
            { data: 'final_total', name: 'final_total' , class:'final_tot'},
            { data: 'payment_due', name: 'payment_due', orderable: false, searchable: false },
            { data: 'added_by', name: 'u.first_name' },
        ],
        fnDrawCallback: function(oSettings) {
            __currency_convert_recursively($('#purchase_table'));
            grand = $(".grands");
            grand_html = $(".grands").html();
            $('.final_tot').each(function(){
                item = $(this);
                html = $(this).html();
                item.html(__currency_trans_from_en(html));
            });
            grand.html(grand_html);
        },
        "footerCallback": function ( row, data, start, end, display ) {
            var total_purchase = 0;
            var total_due  = 0;
            var footer_tax = 0;
            
            var total_purchase_return_due = 0;
            for (var r in data){
                footer_tax  +=  $(data[r].tax_amount) ? 
                parseFloat( data[r].tax_amount ) : 0;
                total_purchase += $(data[r].final_total) ? 
                parseFloat( data[r].final_total ) : 0;
                var payment_due_obj = $('<div>' + data[r].payment_due + '</div>');
                total_due += payment_due_obj.find('.payment_due').data('orig-value') ? 
                parseFloat(payment_due_obj.find('.payment_due').data('orig-value')) : 0;

                total_purchase_return_due += payment_due_obj.find('.purchase_return').data('orig-value') ? 
                parseFloat(payment_due_obj.find('.purchase_return').data('orig-value')) : 0;
            }
            $('.footer_purchase_total').html(__currency_trans_from_en(total_purchase));
            $('.footer_total_due').html(__currency_trans_from_en(total_due));
            $('.footer_tax').html(__currency_trans_from_en(footer_tax));
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
    $(document).on(
        'change',
        '#purchase_list_filter_location_id, \
                    #purchase_list_filter_supplier_id, #purchase_list_filter_payment_status,\
                     #purchase_list_filter_status',
        function() {
            purchase_table.ajax.reload();
        }
    );

    update_table_sr_number();

    $(document).on('change', '.mfg_date', function() {
        var this_date = $(this).val();
        var this_moment = moment(this_date, moment_date_format);
        var expiry_period = parseFloat(
            $(this)
                .closest('td')
                .find('.row_product_expiry')
                .val()
        );
        var expiry_period_type = $(this)
            .closest('td')
            .find('.row_product_expiry_type')
            .val();
        if (this_date) {
            if (expiry_period && expiry_period_type) {
                exp_date = this_moment
                    .add(expiry_period, expiry_period_type)
                    .format(moment_date_format);
                $(this)
                    .closest('td')
                    .find('.exp_date')
                    .datepicker('update', exp_date);
            } else {
                $(this)
                    .closest('td')
                    .find('.exp_date')
                    .datepicker('update', '');
            }
        } else {
            $(this)
                .closest('td')
                .find('.exp_date')
                .datepicker('update', '');
        }
    });

    $('#purchase_entry_table tbody').find('.expiry_datepicker').each(function() {
            $(this).datepicker({
                autoclose: true,
                format: datepicker_date_format,
            });
    });
    $(document).on('change', '.profit_percent', function() {
        var row = $(this).closest('tr');
        var profit_percent = __read_number($(this), true);

        var purchase_unit_cost = __read_number(row.find('input.purchase_unit_cost_after_tax'), true);
        var default_sell_price =
            parseFloat(purchase_unit_cost) +
            __calculate_amount('percentage', profit_percent, purchase_unit_cost);
        var exchange_rate = $('input#exchange_rate').val();
        __write_number(
            row.find('input.default_sell_price'),
            default_sell_price * exchange_rate,
            true
        );
    });
    $(document).on('change', '.default_sell_price', function() {
        var row = $(this).closest('tr');
        update_inline_profit_percentage(row);
    });
    $('table#purchase_table tbody').on('click', 'a.delete-purchase', function(e) {
        e.preventDefault();
        swal({
            title: LANG.sure,
            icon: 'warning',
            buttons: true,
            dangerMode: true,
        }).then(willDelete => {
            if (willDelete) {
                var href = $(this).attr('href');
                $.ajax({
                    method: 'DELETE',
                    url: href,
                    dataType: 'json',
                    success: function(result) {
                        if (result.success == true) {
                            toastr.success(result.msg);
                             
                        } else {
                            toastr.error(result.msg);
                        }
                    },
                });
            }
        });
    });
    // #2024-8-6
    $('table#purchase_entry_table').on('change', 'select.sub_unit', function() {
        var tr = $(this).closest('tr');
        var base_unit_cost = tr.find('input.base_unit_cost').val();
        var prices = tr.find('.list_price');
        var global_prices = $('#list_price');
        var base_unit_selling_price = tr.find('input.base_unit_selling_price').val();

        var multiplier = parseFloat(
            $(this)
                .find(':selected')
                .data('multiplier')
        );
        var check_price = parseFloat(
            $(this)
            .find(':selected')
            .data('check_price')
        );
        
        if(check_price == 0){	
            var base_unit_cost = parseFloat(
                    $(this)
                    .find(':selected')
                    .data('price')
            );
        }
        multiplier = isNaN(multiplier)?1:multiplier;
        var unit_sp   = base_unit_selling_price * multiplier;
        var unit_cost = base_unit_cost  ;

        var sp_element = tr.find('input.default_sell_price');
        __write_number(sp_element, unit_sp);
        var cp_element = tr.find('input.purchase_unit_cost_without_discount');
        var cp_element_edit = tr.find('input.purchase_unit_cost_without_discount_s');
   
        
        html = "";
        global_html = "";
        list_price = JSON.stringify(tr.find('.list_price').data("prices"));
        list_of    = JSON.parse(list_price);
        
        for(i in list_of){
            if(i == $(this).find(':selected').val()){
                html += '<option value="" data-price="null" selected>None</option>';
                counter = 0;
                for(e in list_of[i]){
                    if(list_of[i][e].line_id == global_prices.val()){
                        unit_cost = list_of[i][e].price;
                    }
                    var prc = (list_of[i][e].price)??0;
                    html += '<option value="'+list_of[i][e].line_id+'" data-price="'+prc+'"  data-value="'+list_of[i][e].line_id+'" >'+list_of[i][e].name+'</option>';
                    View_selected = (counter == 0)?"selected":"";
                    global_html += '<option value="'+list_of[i][e].line_id+'" '+View_selected+' data-price="'+prc+'"  data-value="'+list_of[i][e].line_id+'" >'+list_of[i][e].name+'</option>';
                    counter++;
                }
            }
        }
        if(check_price == 0){
            cp_element.val(unit_cost) ;
            cp_element_edit.val(unit_cost) ;
        }else{
            cp_element.val(unit_cost* multiplier)
            cp_element_edit.val(unit_cost* multiplier)
           
        }  
        if(html != ""){
            prices.html(html);
            // global_prices.html(global_html);
        }
        cp_element.change();
        cp_element_edit.change();
    });
    toggle_search();
});

// ******* FOR ON CLICK SECTION 
// ** 1
    $(document).on('click', 'button#submit_purchase_form', function(e) {
        e.preventDefault();
        //Check if product is present or not.
        if ($('table#purchase_entry_table tbody tr').length <= 0) {
            toastr.warning(LANG.no_products_added);
            $('input#search_product').select();
            return false;
        }
        $('form#add_purchase_form').validate({
            rules: {
                ref_no: {
                    remote: {
                        url: '/purchases/check_ref_number',
                        type: 'post',
                        data: {
                            ref_no: function() {
                                return $('#ref_no').val();
                            },
                            contact_id: function() {
                                return $('#supplier_id').val();
                            },
                            purchase_id: function() {
                                if ($('#purchase_id').length > 0) {
                                    return $('#purchase_id').val();
                                } else {
                                    return '';
                                }
                            },
                        },
                    },
                },
            },
            messages: {
                ref_no: {
                    remote: LANG.ref_no_already_exists,
                },
            },
        });
        var payment_types_dropdown = $('.payment_types_dropdown')
        var payment_type           = payment_types_dropdown.val();
        var payment_row            = payment_types_dropdown.closest('.payment_row');
        amount_element             = payment_row.find('.payment-amount');
        account_dropdown           = payment_row.find('.account-dropdown');
        if (payment_type == 'advance') {
            max_value = $('#advance_balance').val();
            msg = $('#advance_balance').data('error-msg');
            amount_element.rules('add', {
                'max-value': max_value,
                messages: {
                    'max-value': msg,
                },
            });
            if (account_dropdown) {
                account_dropdown.prop('disabled', true);
            }
        } else {
            amount_element.rules("remove", "max-value");
            if (account_dropdown) {
                account_dropdown.prop('disabled', false); 
            }    
        }
        if ($('form#add_purchase_form').valid()) {
            $(this).attr('disabled', true);
            $('form#add_purchase_form').submit();
        }
    });
// ** 2 *F
    $(document).on('shown.bs.modal', '.quick_add_product_modal', function(){
        var selected_location = $('#location_id').val();
        if (selected_location) {
            $('.quick_add_product_modal').find('#product_locations').val([selected_location]).trigger("change");
        }
    });
     
// ******* FOR ON CHANGE SECTION
// ** 1
    $('.purchase_table .purchase_unit_cost_with_tax').change(function(){
        var tax_amount  = parseFloat($('#tax_id option:selected').data('tax_amount')) ;
        var el          =  $(this).parent().parent();
        var price       =  parseFloat($(this).val())/((tax_amount/100)+1) ;
        el.children().find('.purchase_unit_cost_without_discount_s').val(price.toFixed(2));
        el.children().find('.purchase_unit_cost_without_discount').val(price.toFixed(2));
        el.children().find('.purchase_unit_cost_without_discount_origin').val(price);
        // el.children().find('.purchase_unit_cost_without_discount_s').val(price );
        // el.children().find('.purchase_unit_cost_without_discount').val(price );
        os_total_sub();
        os_grand();
    });
// ** 2
    $('.purchase_table .purchase_unit_cost_without_discount_s').change(function(){
        var tax_amount  = parseFloat($('#tax_id option:selected').data('tax_amount')) ;
        var el          =  $(this).parent().parent();
        el.children().find('.purchase_unit_cost_without_discount').val( parseFloat($(this).val()).toFixed(2));
        el.children().find('.purchase_unit_cost_without_discount_origin').val( $(this).val());
        var price       =   parseFloat($(this).val())*(tax_amount/100) + parseFloat($(this).val()) ; 
        el.children().find('.purchase_unit_cost_with_tax').val(price.toFixed(2));
        var intial_avl =  parseFloat($(this).val());
            os_total_sub();
            os_grand();
    });
// ** 3 
    $(document).on('change', 'input.payment-amount', function() {
        var payment = __read_number($(this), true);
        var grand_total = __read_number($('input#grand_total_hidden'), true);
        var bal = grand_total - payment;
        total_bill();
        // $('#payment_due_').text(__currency_trans_from_en(bal, true, true));
    });
// ** 4
    $('input[name="dis_type"]').change(function(){
        var discount  = parseInt($('input[name="dis_type"]:checked').val());
        var dis_currency  = parseInt($('input[name="dis_currency"]:checked').val());
        // console.log("###### discount type ##### "+ dis_currency);
        // console.log(!isNaN(dis_currency));
        var tax_rate = parseFloat($('option:selected', $('#tax_id')).data('tax_amount'));
        $('.purchase_unit_cost_without_discount').each(function () {
            var el        =  $(this).parent().parent();
            var dis_amount   = ( parseFloat(el.children().find('.inline_discounts').val()) > 0 )?parseFloat(el.children().find('.inline_discounts').val()) :0;
            var price_origin     =  parseFloat(el.children().find('.purchase_unit_cost_without_discount_origin').val());
            var price     =  parseFloat(el.children().find('.purchase_unit_cost_without_discount').val());
            
            var tax_price =  price + (tax_rate/100)*price;
            var quantity =  parseFloat(el.children().find('.purchase_quantity').val());
            if (discount == 0) {
                var amount       = price_origin  -  (dis_amount/100)*price_origin ;
                var  tax_amount  = tax_price  -  (dis_amount/100)*price_origin ;
            }else{
                var amount        = price_origin  -   dis_amount ;
                var tax_amount    = tax_price  -   dis_amount ;
                
            }
            el.children().find('.purchase_unit_cost').val(amount.toFixed(2));
            el.children().find('.total_unit_cost_with_tax').val(tax_amount.toFixed(2));
            el.children().find('.purchase_unit_cost_after_tax').val(tax_amount.toFixed(2));
            var total_unit_cost_with_tax   =  parseFloat(el.children().find('.total_unit_cost_with_tax').val());
            el.children().find('.row_total_cost').val(total_unit_cost_with_tax*quantity);

        });
        os_total_sub();
        os_grand();
    });
// ** 5 
    $('#discount_amount , #discount_type').change(function(){
        discount_cal_amount2() ;
        os_grand();
    //  console.log(amount+'final '+inital_amount+' initial '+discount_amount);
    });
// ** 6 
    $('#tax_id').change(function(){
        os_total_sub();
        os_grand();
    })  
// ** 7 
    $(document).on('change', '#location_id', function() {
        toggle_search();
        $('#purchase_entry_table tbody').html('');
        update_table_total();
        update_grand_total();
        update_table_sr_number();
    });
 

// ****** FOR  FUNCTIONS  SECTION AGT8422
// ** 1
    function total_bill(){
        
        var total =  $("#grand_total_hidden").val();       
        var total_curr =  $("#grand_total_cur_hidden").val();       
        var total_items =  $("#total_subtotal_input").val();       
        var ship =  $("#total_ship_").val();       
        var ship_ =  $("#total_ship_c").val();       
        $("#total_final_i").html(parseFloat(total).toFixed(2));  
        
        currancy = $(".currency_id_amount").val();
        if(currancy != "" && currancy != 0){
            $("#total_final_i_curr").html(parseFloat(total_curr).toFixed(2));       
            $("#grand_total_cur").html( (  parseFloat(total_curr) + parseFloat(ship)  ).toFixed(2));       
            $("#total_final_curr").html((  parseFloat(total_curr)  + parseFloat(ship)  + parseFloat(ship_)  ).toFixed(2));       
        }    
        $("#total_final_hidden_").val((parseFloat(total) + parseFloat(ship)).toFixed(2));       
        $("#grand_total").html( parseFloat(total).toFixed(2) + parseFloat(ship).toFixed(2) );       
        $("#total_final_").html(  parseFloat(total).toFixed(2) + parseFloat(ship).toFixed(2) + parseFloat(ship_).toFixed(2)  );       
        $("#grand_total2").html(  parseFloat(total).toFixed(2) + parseFloat(ship).toFixed(2) + parseFloat(ship_).toFixed(2)  );       
        $("#payment_due_").html(parseFloat($("#grand_total2").html().toFixed(2)) - parseFloat($(".payment-amount").val()).toFixed(2));       
        $(".hide_div").removeClass("hide");
        $("#grand_total_items").html(parseFloat(total_items).toFixed(2) + parseFloat(ship).toFixed(2));       
        $("#total_final_items").html(parseFloat(total_items).toFixed(2) + parseFloat(ship).toFixed(2) + parseFloat(ship_).toFixed(2));
        // console.log($(".hide_div").html());
    }
// ** 2
    function set_supplier_address(data) {
        console.log(data);
        var address = [];
        
        $("#supplier_address_div").attr("hidden" , false)
        if (data.text) {
            address.push(   "<strong>NAME COMPANY:  </strong>" + data.text);
        }else if(data.first_name){
            address.push(   "<strong>NAME COMPANY:  </strong>" + data.first_name);
        }
        
        if (data.tax_number) {
            address.push( '<br>' + "<strong>TRN : </strong>" + data.tax_number );
        }
        if (data.first_name) {
        if(data.supplier_business_name == null){
                data.supplier_business_name = "";
            }else{
                address.push('<br>'+ "<strong>FUL NAME :  </strong>" + data.supplier_business_name );
            
            }
            if(data.middle_name == null){
                data.middle_name = "";
            }else{
                
                address.push(+ " " + data.middle_name);
            }
            if(data.last_name == null){
                data.last_name = "";
            }else{
                address.push(+ " " + data.last_name);
                
            }
        }
        if (data.first_name) {
            address.push('<br>'+ "<strong>MOBILE :  </strong>" + data.mobile   );
        }
        if (data.address_line_1) {
            address.push('<br>' + data.address_line_1);
        }
        if (data.address_line_2) {
            address.push('<br>' + data.address_line_2);
        }
        if (data.city) {
            address.push('<br>' + data.city);
        }
        if (data.state) {
            address.push(data.state);
        }
        if (data.country) {
            address.push(data.country);
        }
        if (data.zip_code) {
            address.push('<br>' + data.zip_code);
        }
    
        
        var supplier_address = address.join(', ');
        $('#supplier_address_div').html(supplier_address);
    
    }
// ** 3
    function toggle_search() {
        if ($('#location_id').val()) {
            $('#search_product').removeAttr('disabled');
            $('#search_product').focus();
        } else {
            // $('#search_product').attr('disabled', true);
        }
    } 
// ** 4
    function update_table_sr_number() {
        var sr_number = 1;
        $('table#purchase_entry_table tbody')
            .find('.sr_number')
            .each(function() {
                $(this).text(sr_number);
                sr_number++;
            });
    }
// ** 5 
    function update_grand_total() {
        var st_before_tax = __read_number($('input#st_before_tax_input'), true);
        var total_subtotal = __read_number($('input#total_subtotal_input'), true);

        //Calculate Discount
        var discount_type = $('select#discount_type').val();
        var discount_amount = __read_number($('input#discount_amount'), true);
        var discount = __calculate_amount(discount_type, discount_amount, total_subtotal);
        $('#discount_calculated_amount').text(__currency_trans_from_en(discount, true, true));
        // ########
        var dis_currency  =  parseInt($('input[name="dis_currency"]:checked').val());
        if(isNaN(dis_currency)){
                //Calculate Tax
                var tax_rate = parseFloat($('option:selected', $('#tax_id')).data('tax_amount'));
                var tax = __calculate_amount('percentage', tax_rate, total_subtotal - discount);
                __write_number($('input#tax_amount'), tax);
                $('#tax_calculated_amount').text(__currency_trans_from_en(tax, true, true));
        }else{
                currancy = $(".currency_id_amount").val();
                if(currancy != "" && currancy != 0){
                    discount = discount * currancy;                       
                    //Calculate Tax
                    var tax_rate = parseFloat($('option:selected', $('#tax_id')).data('tax_amount'));
                    var tax = __calculate_amount('percentage', tax_rate, total_subtotal - discount);
                    __write_number($('input#tax_amount'), tax);
                    $('#tax_calculated_amount').text(__currency_trans_from_en(tax, true, true));
                }else{
                    //Calculate Tax
                    var tax_rate = parseFloat($('option:selected', $('#tax_id')).data('tax_amount'));
                    var tax = __calculate_amount('percentage', tax_rate, total_subtotal - discount);
                    __write_number($('input#tax_amount'), tax);
                    $('#tax_calculated_amount').text(__currency_trans_from_en(tax, true, true));
                }
            
        }
            
        //Calculate shipping
        var shipping_charges = __read_number($('input#shipping_charges'), true);

        //Calculate Final total
        grand_total = total_subtotal - discount + tax + shipping_charges;
       
        // console.log('21-9-2024 :::  ' + grand_total);
        $('input#grand_total_hidden').val(grand_total);

        var payment = __read_number($('input.payment-amount'), true);

        var due = grand_total - payment;
        // __write_number($('input.payment-amount'), grand_total, true);

        $('#grand_total').text(__currency_trans_from_en(grand_total, true, true));
        $('#grand_total2').text(__currency_trans_from_en(grand_total, true, true));

        // $('#payment_due_').text(__currency_trans_from_en(due, true, true));

        //__currency_convert_recursively($(document));
    }
// ** 6 
    function update_row_price_for_exchange_rate(row) {
        var exchange_rate = $('input#exchange_rate').val();

        if (exchange_rate == 1) {
            return true;
        }

        var purchase_unit_cost_without_discount =
            __read_number(row.find('.purchase_unit_cost_without_discount'), true) / exchange_rate;
        __write_number(
            row.find('.purchase_unit_cost_without_discount'),
            purchase_unit_cost_without_discount,
            true
        );
        __write_number(
            row.find('.purchase_unit_cost_without_discount_origin'),
            purchase_unit_cost_without_discount,
            true
        );

        var purchase_unit_cost = __read_number(row.find('.purchase_unit_cost'), true) / exchange_rate;
        __write_number(row.find('.purchase_unit_cost'), purchase_unit_cost, true);

        var row_subtotal_before_tax_hidden =
            __read_number(row.find('.row_subtotal_before_tax_hidden'), true) / exchange_rate;
        row.find('.row_subtotal_before_tax').text(
            __currency_trans_from_en(row_subtotal_before_tax_hidden, false, true)
        );
        __write_number(
            row.find('input.row_subtotal_before_tax_hidden'),
            row_subtotal_before_tax_hidden,
            true
        );

        var purchase_product_unit_tax =
            __read_number(row.find('.purchase_product_unit_tax'), true) / exchange_rate;
        __write_number(row.find('input.purchase_product_unit_tax'), purchase_product_unit_tax, true);
        row.find('.purchase_product_unit_tax_text').text(
            __currency_trans_from_en(purchase_product_unit_tax, false, true)
        );

        var purchase_unit_cost_after_tax =
            __read_number(row.find('.purchase_unit_cost_after_tax'), true) / exchange_rate;
        __write_number(
            row.find('input.purchase_unit_cost_after_tax'),
            purchase_unit_cost_after_tax,
            true
        );

        var row_subtotal_after_tax_hidden =
            __read_number(row.find('.row_subtotal_after_tax_hidden'), true) / exchange_rate;
        __write_number(
            row.find('input.row_subtotal_after_tax_hidden'),
            row_subtotal_after_tax_hidden,
            true
        );
        row.find('.row_subtotal_after_tax').text(
            __currency_trans_from_en(row_subtotal_after_tax_hidden, false, true)
        );
    }
// ** 7
    function iraqi_dinnar_selling_price_adjustment(row) {
        var default_sell_price = __read_number(row.find('input.default_sell_price'), true);

        //Adjsustment
        var remaining = default_sell_price % 250;
        if (remaining >= 125) {
            default_sell_price += 250 - remaining;
        } else {
            default_sell_price -= remaining;
        }

        __write_number(row.find('input.default_sell_price'), default_sell_price, true);

        update_inline_profit_percentage(row);
    }
// ** 8
    function update_inline_profit_percentage(row) {
        //Update Profit percentage
        var default_sell_price = __read_number(row.find('input.default_sell_price'), true);
        var exchange_rate = $('input#exchange_rate').val();
        default_sell_price_in_base_currency = default_sell_price / parseFloat(exchange_rate);

        var purchase_after_tax = __read_number(row.find('input.purchase_unit_cost_after_tax'), true);
        var profit_percent = __get_rate(purchase_after_tax, default_sell_price_in_base_currency);
        __write_number(row.find('input.profit_percent'), profit_percent, true);
    }
// ** 9
    function update_table_total() {
        var total_quantity  =  0;
        var  total_amount   =  0;
        $('.purchase_quantity').each(function(){
            var  el       =  $(this).parent().parent();
            var  quantity = parseFloat($(this).val());
            // var  amount   = parseFloat(el.children().find('.purchase_unit_cost').val()) ;
            var  amount   = parseFloat(el.children().find('.purchase_unit_cost').val()) ;
            console.log("##first : " + amount);
            total_quantity +=quantity;
            total_amount += (amount*quantity);
            var os_x      =  os_tax((amount*quantity));
            el.children().find('.row_total_cost').val(os_x);
            el.children().find('.row_total_cost').val(amount*quantity);
        });
        console.log("##second : " + total_amount);
        $('#total_quantity').text(total_quantity);
        $('#total_subtotal_input').val(total_amount.toFixed(2));
        $('#total_subtotal').text(total_amount.toFixed(2));
        

    }
// ** 10
    function discount_cal_amount2() {
        var dis      =  0;
        if ($('#discount_amount').val()) {
            dis      =  parseFloat($('#discount_amount').val()) ;
        }
        var dis_type =  $('#discount_type option:selected').val();
        var dd =  0;
        if(dis_type) {
            if (dis_type.length > 0) {
                
                var amount = 0;
                $('.row_total_cost').each(function(){
                    var item =  $(this).val();
                    item     = parseFloat(item.replace(',',''));
                    amount  += item;
                });
            
                var inital_amount  =  amount  + os_tax_amount(amount.toFixed(2));
                currancy = $(".currency_id_amount").val();
                // ########
                var dis_currency  =  parseInt($('input[name="dis_currency"]:checked').val());
                var vat_per    =  parseFloat($('#tax_id option:selected').data('tax_amount')) ;

                if (dis_type ==  'fixed_before_vat') {
                    
                    var discount_amount  =  dis;/***#$% */
                    if(!isNaN(dis_currency)){
                        $('#discount_calculated_amount2').text(dis);/***#$% */
                        if(currancy != "" && currancy != 0){/***#$% */
                            $('#discount_calculated_amount_cur').text((dis).toFixed(2));/***#$% */
                            $('#discount_calculated_amount2').text((dis*currancy).toFixed(2));/***#$% */
                        }
                    }else{ 
                        if(currancy != "" && currancy != 0){
                            dis     =  dis * currancy;
                            amount  =  amount -  dis; 
                            amount  =  amount +  os_tax_amount(amount);
                            var discount_amount  =  inital_amount - amount; 
                            $('#discount_calculated_amount2').text(dis.toFixed(2));  
                            $('#discount_calculated_amount_cur').text((dis/currancy).toFixed(2));   
                        }else{
                            $('#discount_calculated_amount2').text(dis.toFixed(2));  
                        }
                    }
                }else if(dis_type ==  'fixed_after_vat'){
                    if(!isNaN(dis_currency)){
                        amount  =  amount + os_tax_amount(amount);
                        amount  =  amount - dis;
                        var discount_amount  =  inital_amount - amount;
                       
                        var x =  (dis*100)/(100+parseFloat(vat_per.toFixed(2)));
                        $('#discount_calculated_amount2').text(x.toFixed(2));
                        if(currancy != "" && currancy != 0){
                            $('#discount_calculated_amount_cur').text((x).toFixed(2));
                            $('#discount_calculated_amount2').text((x*currancy).toFixed(2));/***#$% */
                        }
                    }else{
                        if(currancy != "" && currancy != 0){
                            dis     =  dis * currancy;
                            amount  =  amount + os_tax_amount(amount);
                            amount  =  amount - dis;
                            var discount_amount  =  inital_amount - amount;
                            var x                =  (dis*100)/(100+parseFloat(vat_per.toFixed(2)));
                            $('#discount_calculated_amount2').text((x*currancy).toFixed(2));
                            $('#discount_calculated_amount_cur').text(x.toFixed(2));  
                        }else{ 
                            var x                =  (dis*100)/(100+parseFloat(vat_per.toFixed(2)));
                            var discount_amount  =  x;
                            $('#discount_calculated_amount2').text((x).toFixed(2));
                        }
                    }
                }else{
                    if(!isNaN(dis_currency)){
                        var discount_amount = (dis/100)*amount;
                        var os_sub = parseFloat($('#total_subtotal_input').val());
                        var x      =  os_sub*(dis/100);
                        $('#discount_calculated_amount2').text(x.toFixed(2));
                        if(currancy != "" && currancy != 0){
                            $('#discount_calculated_amount_cur').text((x.toFixed(2)/currancy).toFixed(2));
                        }
                    }else{
                        var discount_amount = (dis/100)*amount;
                        var os_sub = parseFloat($('#total_subtotal_input').val());
                        var x      =  os_sub*(dis/100);
                        $('#discount_calculated_amount2').text(x.toFixed(2));
                        $('#discount_calculated_amount2').text(x.toFixed(2));
                        if(currancy != "" && currancy != 0){ 
                            $('#discount_calculated_amount_cur').text((x.toFixed(2)/currancy).toFixed(2)); 
                        }
                    }
                  
                }
                dd =  discount_amount;
            }
        }else{
            $('#discount_calculated_amount2').text(0);
            $('#discount_calculated_amount_cur').text(0);
        }
        return dd;
        
    }
// ** 11
    function os_grand() {
        if($('#total_subtotal_input_cur_edit').val() != null){
            var net_curr   =  parseFloat($('#total_subtotal_input_cur_edit').val());
        }else{
            var net_curr   =  parseFloat($('#total_subtotal_input_cur').val());
            
        }
        var dis_type      =  $('#discount_type option:selected').val(); /***#$% */
        var net           =  parseFloat($('#total_subtotal_input').val());
        var vat_per       =  parseFloat($('#tax_id option:selected').data('tax_amount')) ;
        var dis_count     =  discount_cal_amount2();
        var dis_xx        =  parseFloat($('#discount_calculated_amount2').text());
        var dis_xx_curr   =  parseFloat($('#discount_calculated_amount_cur').text());
        currancy          =  $(".currency_id_amount").val();
        // ########
        var dis_currency  =  parseInt($('input[name="dis_currency"]:checked').val());
        var checked = 0;
        if(isNaN(dis_currency)){
            checked = 1
            var x          = (net-dis_xx)*(vat_per/100);
        }else{
            if(currancy != "" && currancy != 0){
                checked = 2
                var x          = (net-(dis_xx))*(vat_per/100);
            }else{
                checked = 3
                var x          = (net-(dis_xx*0))*(vat_per/100);
            }
        }
        var x_curr  = (net_curr-dis_xx_curr)*(vat_per/100);

        // console.log("CHECK DISCOUNT ## " + checked + " _ " + x  );
        $('#tax_calculated_amount').text(x.toFixed(2));
        if(currancy != "" && currancy != 0){
            $('#tax_calculated_amount_curr').html(parseFloat(x_curr).toFixed(2));
            if(dis_type ==  'fixed_after_vat'){
                dis_count = (dis_count*100)/(100+parseFloat(vat_per)) * currancy; /***#$% */
            }
            else if(dis_type ==  'fixed_before_vat'){
                dis_count = dis_count * currancy; /***#$% */
            }
        }
        $('#tax_amount').val(x);
        var shipping   =  0;
        if ($('#shipping_charges').val()) {
            shipping   =  parseFloat($('#shipping_charges').val());
        }
        netTotal_final  = parseFloat(net - parseFloat(dis_count)).toFixed(2) ;
        vat_final       = parseFloat(((vat_per/100)*(net-parseFloat(dis_count)))).toFixed(2);
        shipping_final  = parseFloat(shipping).toFixed(2) ;
        // var grand     =  net + ((vat_per/100)*net) + shipping - dis_count;
        var grand          =  parseFloat(netTotal_final)  +  parseFloat(vat_final)   + parseFloat(shipping_final)  ;
        var grand_curr     =  parseFloat(net_curr) - dis_xx_curr + x_curr + parseFloat(shipping) ;

        $('#grand_total').text(grand);
        currancy = $(".currency_id_amount").val();
        if(currancy != "" && currancy != 0){
            $("#grand_total_cur_hidden").val(parseFloat(grand_curr).toFixed(2));       
            $("#total_final_i_curr").html(parseFloat(grand_curr).toFixed(2));          
            
        }     
        $('#grand_total_hidden').text(grand);
        $('#grand_total_hidden').val(grand);

        $('#grand_total2').text(grand);
        // $('#payment_due_').text(grand.toFixed(2));
        total_bill();
    }
// ** 12
    function update_discount() {
    } 
// ** 13
    function os_tax(amount) {
        var per =  $('#tax_id option:selected').data('tax_amount');
        return amount + amount*(per/100);
    }
// ** 14
    function os_tax_amount(amount) {
        var per =  $('#tax_id option:selected').data('tax_amount');
        return amount*(per/100);
    }

// ** 15 curr
    function eb_total_sub() {
        var amount                  =  0;
        var out_tax_amount          =  0;
        var out_tax_amount_cur      =  0;
        var out_tax_amount_cur_edit =  0;
        var total_qty_os            =  0;
        var check_edit              = 0;
        $('.purchase_unit_cost').each(function () {
            var el            =  $(this).parent().parent();
            // ........................
            var pp_price_origin    =  parseFloat(el.children().find('.purchase_unit_cost_without_discount_origin').val())
            var pp_price      =  parseFloat(el.children().find('.purchase_unit_cost_without_discount').val())
            var tax_price     =  parseFloat(el.children().find('.purchase_unit_cost_with_tax').val());
            var tax_rates     =  parseFloat($('option:selected', $('#tax_id')).data('tax_amount'));
            // ........................
            
            
            currancy = $(".currency_id_amount").val();
            
            // if(currancy != "" && currancy != 0){
            //     var pp_price_currency      =  parseFloat(el.children().find('.purchase_unit_cost_new_currency').val((pp_price/currancy).toFixed(2)));
            //     var tax_price_currency     =  parseFloat(el.children().find('.purchase_unit_cost_with_tax_new_currency').val((tax_price/currancy).toFixed(2)));
            // }else{
            //     var pp_price_currency      =  parseFloat(el.children().find('.purchase_unit_cost_new_currency').val(0))
            //     var tax_price_currency     =  parseFloat(el.children().find('.purchase_unit_cost_with_tax_new_currency').val(0))
                
            // }
            
            
            var dis_type      =  parseInt($('input[name="dis_type"]:checked').val());
            var dis_currency  =  parseInt($('input[name="dis_currency"]:checked').val());

            var dis_amount    = ( parseFloat(el.children().find('.inline_discounts').val()) > 0 )?parseFloat(el.children().find('.inline_discounts').val()) :0;
            
            
            if (dis_type == 1) {
                if(isNaN(dis_currency)){
                    var final_amount =  pp_price  - dis_amount;
                    var final_tax    =  tax_price - dis_amount;
                }else{
                    if(currancy != "" && currancy != 0){
                        dis_amount = dis_amount * currancy;                       
                        var final_amount=  pp_price - dis_amount;
                        var final_tax   =  tax_price - dis_amount;
                    }else{
                        var final_amount=  pp_price - dis_amount;
                        var final_tax   =  tax_price - dis_amount;
                    }
                }
            }else{
                if(isNaN(dis_currency)){
                    var final_amount_origin =  pp_price_origin  - ((dis_amount/100)*pp_price_origin);
                    var final_amount =  pp_price - ((dis_amount/100)*pp_price);
                    var final_tax    =  tax_price - ((dis_amount/100)*tax_price);
                }else{
                    var final_amount_origin = pp_price_origin  - ((dis_amount/100)*pp_price_origin);
                    var final_amount = pp_price  - ((dis_amount/100)*pp_price);
                    var final_tax   =  tax_price - ((dis_amount/100)*tax_price);
                }
            }
            
            
            var quantity        =  parseFloat(el.children().find('.purchase_quantity').val());
            var price           =  parseFloat(el.children().find('.eb_price ').val());
            
            total_qty_os    +=quantity;
            el.children().find('.purchase_unit_cost').val(final_amount_origin.toFixed(2));
            el.children().find('.row_total_cost').val(parseInt(final_amount_origin*quantity).toFixed(2));
            
            var before_tax_dis           = el.children().find('.total_cost_dis_new_currency'); 
            var before_tax_dis_edit      = (el.children().find('.unit_cost_after_new_currency') != null)?el.children().find('.unit_cost_after_new_currency'):el.children().find('.total_cost_dis_new_currency'); 
            var after_tax_dis            = el.children().find('.purchase_unit_cost_with_tax_new_currency'); 
            
            // ........................................................................................ price after dis with vat 
            var total_unit_cost_with_tax      =  os_tax(final_amount_origin);
            el.children().find('.total_unit_cost_with_tax').val(total_unit_cost_with_tax.toFixed(2));
            // ........................................................................................
            
            if(currancy != "" && currancy != 0){
                
                var pp_price_dis_currency      =  parseFloat(el.children().find('.total_cost_dis_new_currency').val((final_amount_origin/currancy).toFixed(2)));
                var tax_price_dis_currency     =  parseFloat(el.children().find('.total_unit_cost_with_tax_new_currency').val((total_unit_cost_with_tax.toFixed(2)/currancy).toFixed(2)));
                
                var tax_price_edit             =  parseFloat(el.children().find('.unit_cost_after_new_currency').val((final_amount_origin.toFixed(2)/currancy).toFixed(2)));
                var tax_price_edit_currency    =  parseFloat(el.children().find('.unit_cost_after_tax_new_currency').val((total_unit_cost_with_tax.toFixed(2)/currancy).toFixed(2)));
                // ************************************************************************************************************************************************************ previous error currency 1075.01
                if( el.children().find('.total_unit_cost_with_tax_new_currency').val() != null){
                    el.children().find('.row_total_cost_new_currency').val((parseFloat(el.children().find('.total_unit_cost_with_tax_new_currency').val()*quantity)).toFixed(2));
                }else{
                    check_edit = 1;
                    el.children().find('.row_total_cost_new_currency').val((parseFloat(el.children().find('.unit_cost_after_tax_new_currency').val()*quantity)).toFixed(2));
                }
            }else{
                var pp_price_dis_currency      =  parseFloat(el.children().find('.total_cost_dis_new_currency').val(0))
                var tax_price_dis_currency     =  parseFloat(el.children().find('.total_unit_cost_with_tax_new_currency').val(0))
                var tax_price_edit             =  parseFloat(el.children().find('.unit_cost_after_new_currency').val(0))
                var tax_price_edit_currency    =  parseFloat(el.children().find('.unit_cost_after_tax_new_currency').val(0))
                el.children().find('.row_total_cost_new_currency').val(0);
                
            }
            el.children().find('.purchase_unit_cost_with_tax').val(((pp_price_origin*tax_rates/100) + pp_price_origin).toFixed(2));
            out_tax_amount =  out_tax_amount + quantity*final_amount;
            out_tax_amount_cur = out_tax_amount_cur+ quantity*(before_tax_dis.val());
            out_tax_amount_cur_edit = out_tax_amount_cur_edit + quantity*(before_tax_dis_edit.val());
    
            amount =  amount + total_unit_cost_with_tax*quantity;
            var total_unit_cost_with_tax = parseFloat(el.children().find('.total_unit_cost_with_tax').val());
            var tax_row =  total_unit_cost_with_tax*quantity;
            
            el.children().find('.row_total_cost').val(parseFloat(total_unit_cost_with_tax*quantity).toFixed(2));
            el.children().find('.purchase_unit_cost_after_tax_').val(parseFloat(price*quantity).toFixed(2));
            el.children().find('.row_total_cost_').val(parseFloat(price*quantity).toFixed(2));
        })
        
        var tax_calculated_amount  =  os_tax_amount(out_tax_amount);
        var tax_calculated_amount_curr  =  os_tax_amount(out_tax_amount_cur);
        var tax_calculated_amount_edit_curr  =  os_tax_amount(out_tax_amount_cur_edit);
        // console.log("TAXSSSSS : " + out_tax_amount_cur_edit + " _ " +tax_calculated_amount_edit_curr);
    
        $('#total_subtotal').html(out_tax_amount.toFixed(2));
        $('#total_subtotal_input').val(out_tax_amount.toFixed(2));
        $('#total_subtotal_cur').html(out_tax_amount_cur.toFixed(2));
        $('#total_subtotal_cur_edit').html(out_tax_amount_cur_edit.toFixed(2));
        $('#total_subtotal_input_cur_edit').val(out_tax_amount_cur_edit.toFixed(2));
        $('#total_subtotal_input_cur').val(out_tax_amount_cur.toFixed(2));
        $('#tax_calculated_amount').html(tax_calculated_amount.toFixed(2));
        currancy = $(".currency_id_amount").val();
        if(currancy != "" && currancy != 0){
            if(   check_edit   != 1){ 
                $('#tax_calculated_amount_curr').html((tax_calculated_amount_curr).toFixed(2));
            }else{
                $('#tax_calculated_amount_curr').html((tax_calculated_amount_edit_curr).toFixed(2));
            }
        }
        $('#tax_amount').val(tax_calculated_amount);
        discount_cal_amount2();
        //alert(tax_calculated_amount);

        $('#total_quantity').text(total_qty_os);
        total_bill();
    }

// ** 15
    function os_total_sub() {
        
        var amount                  =  0;
        var out_tax_amount          =  0;
        var out_tax_amount_cur      =  0;
        var out_tax_amount_cur_edit =  0;
        var total_qty_os            =  0; 
        var check_edit              =  0;
        var check_counter           =  0;
        $('.purchase_unit_cost').each(function () {
            check_counter++;
            var el            =  $(this).parent().parent();
            var pp_price_origin      =  parseFloat(el.children().find('.purchase_unit_cost_without_discount_origin').val())
            var pp_price      =  parseFloat(el.children().find('.purchase_unit_cost_without_discount').val())
            var tax_price     =  parseFloat(el.children().find('.purchase_unit_cost_with_tax').val())
            currancy = $(".currency_id_amount").val();
            if(currancy != "" && currancy != 0){
                var pp_price_currency      =  parseFloat(el.children().find('.purchase_unit_cost_new_currency').val((final_amount_origin/currancy).toFixed(2)));
                var tax_price_currency     =  parseFloat(el.children().find('.purchase_unit_cost_with_tax_new_currency').val((tax_price/currancy).toFixed(2)));
            }else{
                var pp_price_currency      =  parseFloat(el.children().find('.purchase_unit_cost_new_currency').val(0))
                var tax_price_currency     =  parseFloat(el.children().find('.purchase_unit_cost_with_tax_new_currency').val(0))
                
            }
            var dis_type      =  parseInt($('input[name="dis_type"]:checked').val());
            // ########
            var dis_currency  =  parseInt($('input[name="dis_currency"]:checked').val());
            // ########
            var dis_amount    = ( parseFloat(el.children().find('.inline_discounts').val()) > 0 )?parseFloat(el.children().find('.inline_discounts').val()) :0;
            if (dis_type == 1) {
                 // ########
                if(isNaN(dis_currency)){
                    var final_amount_origin =  pp_price_origin - dis_amount;
                    var final_amount=  pp_price - dis_amount;
                    var final_tax   =  tax_price - dis_amount;
                }else{
                    if(currancy != "" && currancy != 0){
                        dis_amount = dis_amount * currancy;                       
                        var final_amount_origin=  pp_price_origin - dis_amount;
                        var final_amount=  pp_price - dis_amount;
                        var final_tax   =  tax_price - dis_amount;
                    }else{
                        var final_amount_origin=  pp_price_origin - dis_amount;
                        var final_amount=  pp_price - dis_amount;
                        var final_tax   =  tax_price - dis_amount;
                    }
                }
                 // ########
            }else{
                 // ########
                if(isNaN(dis_currency)){
                    var final_amount_origin = pp_price_origin  - ((dis_amount/100)*pp_price_origin);
                    var final_amount = pp_price  - ((dis_amount/100)*pp_price);
                    var final_tax    =  tax_price - ((dis_amount/100)*tax_price);
                }else{
                    var final_amount_origin = pp_price_origin  - ((dis_amount/100)*pp_price_origin);
                    var final_amount = pp_price  - ((dis_amount/100)*pp_price);
                    var final_tax    =  tax_price - ((dis_amount/100)*tax_price);
                }
                 // ########
            }
            var quantity        =  parseFloat(el.children().find('.purchase_quantity').val());
            var price           =  parseFloat(el.children().find('.eb_price').val());
            total_qty_os +=quantity;

            el.children().find('.purchase_unit_cost').val(final_amount_origin.toFixed(2));
            el.children().find('.row_total_cost').val(parseInt(final_amount_origin*quantity).toFixed(2));
            
            var before_tax_dis = el.children().find('.total_cost_dis_new_currency'); 
            var before_tax_dis_edit      = (el.children().find('.unit_cost_after_new_currency').val() != null)?el.children().find('.unit_cost_after_new_currency'):el.children().find('.total_cost_dis_new_currency'); 
            var after_tax_dis  = el.children().find('.purchase_unit_cost_with_tax_new_currency'); 
            
            var total_unit_cost_with_tax      =  os_tax(final_amount_origin);
            el.children().find('.total_unit_cost_with_tax').val(total_unit_cost_with_tax.toFixed(2));
            if(currancy != "" && currancy != 0){
                var pp_price_dis_currency      =  parseFloat(el.children().find('.total_cost_dis_new_currency').val((final_amount_origin/currancy).toFixed(2)));
                var tax_price_dis_currency     =  parseFloat(el.children().find('.total_unit_cost_with_tax_new_currency').val((total_unit_cost_with_tax.toFixed(2)/currancy).toFixed(2)));
                var tax_price_edit             =  parseFloat(el.children().find('.unit_cost_after_new_currency').val((final_amount_origin.toFixed(2)/currancy).toFixed(2)));
                var tax_price_edit_currency    =  parseFloat(el.children().find('.unit_cost_after_tax_new_currency').val((total_unit_cost_with_tax.toFixed(2)/currancy).toFixed(2)));
                if( el.children().find('.total_unit_cost_with_tax_new_currency').val() != null){
                    el.children().find('.row_total_cost_new_currency').val((parseFloat(el.children().find('.total_unit_cost_with_tax_new_currency').val()*quantity)).toFixed(2));
                }else{
                    check_edit = 1;
                    el.children().find('.row_total_cost_new_currency').val((parseFloat(el.children().find('.unit_cost_after_tax_new_currency').val()*quantity)).toFixed(2));
                }
            }else{
                var pp_price_dis_currency      =  parseFloat(el.children().find('.total_cost_dis_new_currency').val(0))
                var tax_price_dis_currency     =  parseFloat(el.children().find('.total_unit_cost_with_tax_new_currency').val(0))
                var tax_price_edit             =  parseFloat(el.children().find('.unit_cost_after_new_currency').val(0))
                var tax_price_edit_currency    =  parseFloat(el.children().find('.unit_cost_after_tax_new_currency').val(0))
                el.children().find('.row_total_cost_new_currency').val(0);
                
            }
            //  el.children().find('.purchase_unit_cost_with_tax').val(total_unit_cost_with_tax.toFixed(2));
            // el.children().find('.purchase_unit_cost_with_tax').val(((pp_price*tax_rates/100) + pp_price).toFixed(2));
            if($('#total_subtotal_cur_edit')){
                 
                out_tax_amount          =  out_tax_amount + quantity*(final_amount).toFixed(2);
            }else{ 

                out_tax_amount          =  out_tax_amount + quantity*final_amount;
            }
            out_tax_amount_cur      =  out_tax_amount_cur+ quantity*(before_tax_dis.val());
            out_tax_amount_cur_edit =  out_tax_amount_cur_edit + quantity*(before_tax_dis_edit.val());
           
            amount                  =  amount + total_unit_cost_with_tax*quantity;
            var total_unit_cost_with_tax = parseFloat(el.children().find('.total_unit_cost_with_tax').val());
            var tax_row =  total_unit_cost_with_tax*quantity;
            //alert(tax_row+'amount '+total_unit_cost_with_tax+'---------'+quantity);
            var  sub_unit  = el.children().find(".sub_unit");
            var multiplier = parseFloat(
                sub_unit.find(':selected')
                    .data('multiplier')
            );
            el.children().find('.row_total_cost').val(parseFloat(total_unit_cost_with_tax*quantity).toFixed(2));
            // el.children().find('.purchase_unit_cost_after_tax_').val(price*multiplier*quantity);#2024-8-6
            el.children().find('.row_total_cost_').val(parseFloat(price*quantity).toFixed(2));
        })
        var tax_calculated_amount  =  os_tax_amount(out_tax_amount);
        var tax_calculated_amount_curr  =  os_tax_amount(out_tax_amount_cur);
        var tax_calculated_amount_edit_curr  =  os_tax_amount(out_tax_amount_cur_edit);
        console.log("##fourth : " + out_tax_amount);
        $('#total_subtotal').html(out_tax_amount.toFixed(2));
        $('#total_subtotal_input').val(out_tax_amount.toFixed(2));
        $('#total_subtotal_cur').html(out_tax_amount_cur.toFixed(2));
        $('#total_subtotal_cur_edit').html(out_tax_amount_cur_edit.toFixed(2));
        $('#total_subtotal_input_cur_edit').val(out_tax_amount_cur_edit.toFixed(2));
        $('#total_subtotal_input_cur').val(out_tax_amount_cur.toFixed(2));
        $('#tax_calculated_amount').html(tax_calculated_amount.toFixed(2));
        currancy = $(".currency_id_amount").val();
        if(currancy != "" && currancy != 0){
            if(   check_edit   != 1){ 
                $('#tax_calculated_amount_curr').html((tax_calculated_amount_curr).toFixed(2));
            }else{
                $('#tax_calculated_amount_curr').html((tax_calculated_amount_edit_curr).toFixed(2));
            }
        }
        $('#tax_amount').val(tax_calculated_amount);
        discount_cal_amount2();
        //alert(tax_calculated_amount);
        $('#total_quantity').text(total_qty_os);
        total_bill() ;
    }
// ** 16
    $('.currency_id_amount').change(function(){
        if ($('input.depending_curr').is(':checked')) {
            discount_cal_amount2() ;
            eb_currency();
        }else{
            discount_cal_amount2() ;
            os_total_sub();
            os_grand();
        }
    
    });
    $('input[name="dis_currency"]').change(function(){
        
        os_total_sub();
        os_grand();
   
 
    });
       
    function eb_currency(){
        var idcount = 0;
        $('.purchase_unit_cost_new_currency').each(function(){
            var vl = $(this).val();
            var e  = $(this).parent().parent();
            var el = e.children().find(".purchase_unit_cost_with_tax_new_currency");
            var inc_vat = e.children().find(".purchase_unit_cost_with_tax");
            var exc_vat = e.children().find(".purchase_unit_cost_without_discount");
            var exc_vat_origin = e.children().find(".purchase_unit_cost_without_discount_origin");
            var exc_vat_s = e.children().find(".purchase_unit_cost_without_discount_s");
            var exc_dis_vat  = e.children().find(".purchase_unit_cost");
            var inc_dis_vat  = e.children().find(".total_unit_cost_with_tax");
            var tax_amount   = parseFloat($('#tax_id option:selected').data('tax_amount')) ;
            var tax_rate     = parseFloat($('option:selected', $('#tax_id')).data('tax_amount'));
            var dis_amount   = ( parseFloat(e.children().find('.inline_discounts').val()) > 0 )?parseFloat(e.children().find('.inline_discounts').val()) :0;
            var discount  = parseInt($('input[name="dis_type"]:checked').val());
    
            currancy = $(".currency_id_amount").val();
            if(currancy != "" && currancy != 0){
                var  unit_tax   = ((tax_amount/100)*vl) + parseFloat(vl);
                var  percent    = currancy*vl;
                var  percent_tax    = ((tax_amount/100)*(currancy*vl)) + parseFloat(currancy*vl);
                exc_vat.val(percent.toFixed(2));
                exc_vat_origin.val(percent.toFixed(2));
                exc_vat_s.val(percent.toFixed(2));
                inc_vat.val(percent_tax.toFixed(2));
                el.val(unit_tax.toFixed(2));
            }else{
                el.val(0);
            }
            var tax_price =  parseFloat(exc_vat_origin.val()) + parseFloat((tax_rate/100)*exc_vat_origin.val());
             if (discount == 0) {
                var  amount         = exc_vat_origin.val()  -  (dis_amount/100)*exc_vat_origin.val() ;
                var  tax_amount_    = tax_price  -  (dis_amount/100)*exc_vat_origin.val() ;
            }else{
                var amount         = exc_vat_origin.val()  -   dis_amount ;
                var tax_amount_    = tax_price  -   dis_amount ;
                
            }
            exc_dis_vat.val(amount.toFixed(2));
            inc_dis_vat.val(((tax_amount/100)*(amount) + parseFloat(amount)).toFixed(2));
           
    
            // os_total_sub();
            eb_total_sub();
            os_grand();
            idcount++;
        });
        
        
    }
    
    function update_after_tax_price() {
        $('.purchase_table .purchase_unit_cost_with_tax').change(function(){
            var tax_amount      = parseFloat($('#tax_id option:selected').data('tax_amount')) ;
            var el              =  $(this).parent().parent();
            var price           =  parseFloat($(this).val())/((tax_amount/100)+1) ;
            var before_cur      = el.children().find('.purchase_unit_cost_without_discount_s').val(price.toFixed(2));
            var before_cur_tax  = el.children().find('.purchase_unit_cost_without_discount').val(price.toFixed(2));
            var before_cur_tax_origin  = el.children().find('.purchase_unit_cost_without_discount_origin').val(price);
            // var before_cur      = el.children().find('.purchase_unit_cost_without_discount_s').val(price);
            // var before_cur_tax  = el.children().find('.purchase_unit_cost_without_discount').val(price);
            currancy = $(".currency_id_amount").val();
            if(currancy != "" && currancy != 0){
                el.children().find('.purchase_unit_cost_new_currency').val(price/currancy);
                el.children().find('.purchase_unit_cost_with_tax_new_currency').val($(this).val()/currancy);
            }else{
                el.children().find('.purchase_unit_cost_new_currency').val(0);
                el.children().find('.purchase_unit_cost_with_tax_new_currency').val(0);
            } 
            os_total_sub();
            os_grand();
        });
        $('.purchase_table .purchase_unit_cost_without_discount').change(function(){
            var tax_amount  = parseFloat($('#tax_id option:selected').data('tax_amount')) ;
            var el          =  $(this).parent().parent();
            var price       =   parseFloat($(this).val())*(tax_amount/100) + parseFloat($(this).val()) ; 
            el.children().find('.purchase_unit_cost_without_discount_origin').val($(this).val());
            el.children().find('.purchase_unit_cost_with_tax').val(price.toFixed(2));
            var intial_avl =  parseFloat($(this).val());
            currancy = $(".currency_id_amount").val();
            if(currancy != "" && currancy != 0){
                el.children().find('.purchase_unit_cost_new_currency').val($(this).val()/currancy);
                el.children().find('.purchase_unit_cost_with_tax_new_currency').val(price.toFixed(2)/currancy);
            }else{
                el.children().find('.purchase_unit_cost_new_currency').val(0);
                el.children().find('.purchase_unit_cost_with_tax_new_currency').val(0);
            } 
            os_total_sub();
            os_grand();
                
            // el.children().find('.purchase_unit_cost_without_discount_s').attr("type","text");
            // el.children().find('.purchase_unit_cost_without_discount').attr("type","hidden");
            
        });
        //**....................... eb
        $('.purchase_table .purchase_unit_cost_without_discount_s').on("click",function(){
                            var el          =  $(this).parent().parent();
                            
                            // el.children().find('.purchase_unit_cost_without_discount').attr("type","text");
                            // el.children().find('.purchase_unit_cost_without_discount_s').attr("type","hidden");
                            
        });
        $('.purchase_table .purchase_unit_cost_without_discount').on("change",function(){
                var el          =  $(this).parent().parent();
                el.children().find('.purchase_unit_cost_without_discount_origin').val($(this).val());
                el.children().find('.purchase_unit_cost_without_discount_s').val($(this).val());
                            
        });
    }  
// ** 17
    function update_purchase_entry_row_values(row) {
        if (typeof row != 'undefined') {
            var quantity = __read_number(row.find('.purchase_quantity'), true);
            var unit_cost_price = __read_number(row.find('.purchase_unit_cost'), true);
            var row_subtotal_before_tax = quantity * unit_cost_price; 
            var tax_rate = parseFloat(
                $('option:selected', row.find('.purchase_line_tax_id')).attr('data-tax_amount')
            );

            var unit_product_tax = __calculate_amount('percentage', tax_rate, unit_cost_price);

            var unit_cost_price_after_tax = unit_cost_price + unit_product_tax;
            var row_subtotal_after_tax = quantity * unit_cost_price_after_tax;
            var tax_amount  = parseFloat($('#tax_id option:selected').data('tax_amount')) ;
            var  unit_tax   = (tax_amount/100)*unit_cost_price + unit_cost_price;
            row.find('.purchase_unit_cost_with_tax').val(unit_tax.toFixed(2));
            row.find('.total_unit_cost_with_tax').val(unit_tax);
            row.find('.purchase_unit_cost_with_tax').val(unit_tax.toFixed(2));
            row.find('.row_total_cost').val(unit_tax);
            
            row.find('.row_subtotal_before_tax').text(
                __currency_trans_from_en(row_subtotal_before_tax, false, true)
            );
            __write_number(row.find('.row_subtotal_before_tax_hidden'), row_subtotal_before_tax, true);
            __write_number(row.find('.purchase_product_unit_tax'), unit_product_tax, true);
            row.find('.purchase_product_unit_tax_text').text(
                __currency_trans_from_en(unit_product_tax, false, true)
            );
            row.find('.purchase_unit_cost_after_tax').text(
                __currency_trans_from_en(unit_cost_price_after_tax, true)
            );
            row.find('.row_subtotal_after_tax').text(
                __currency_trans_from_en(row_subtotal_after_tax, false, true)
            );
            __write_number(row.find('.row_subtotal_after_tax_hidden'), row_subtotal_after_tax, true);

            row.find('.expiry_datepicker').each(function() {
                $(this).datepicker({
                    autoclose: true,
                    format: datepicker_date_format,
                });
            });
            
            return row;
        }
    }
// ** 18
    function update_total_qty(){
        total_quantity  = 0;
        total_amount  = 0;
        total =0;
        $('.purchase_quantity').each(function(){
            var el =  $(this).parent().parent();
            var  quantity = parseFloat($(this).val());
            if($(".total_amount").val() != null){

                if( quantity == null ){
                    $(".total_amount").val("");
                }else{
                    
                    // var  amount   = parseFloat(el.children().find('.purchase_unit_cost_without_discount').val()) ;
                    var  amount   = parseFloat(el.children().find('.purchase_unit_cost_without_discount_origin').val()) ;
                    // alert(quantity);
                    total_quantity += quantity;
                    total_amount += amount*quantity;
                    total  = total_amount;

                    $(".total_amount").val(total);
                    $(".total_qty").val(total_quantity);

                }
            }
        
        });
    }
// ** 19
    function get_purchase_entry_row_open(product_id, variation_id , open , edit = null) {
        if (product_id) {

        var global_price = $('#list_price').val();
        var row_count    = $('#row_count').val();
        var location_id  = $('#location_id').val();
        var contact_id   = $('#supplier_id option:selected').val();
        var open_i       = open;
        var edit_i       = edit;
        
        // if(edit_i != null){
        //     url_ = '/purchases/get_purchase_entry_row/' + open_i + "/" + edit_i ;
        // }else{
            url_ = '/purchases/get_purchase_entry_row/' + open_i ;
        // }
        $.ajax({
            method: 'POST',
            url: url_,
            dataType: 'html',
            data: { 
                product_id: product_id, 
                row_count: row_count, 
                variation_id: variation_id,
                location_id: location_id,
                global_price: global_price,
                contact_id: contact_id
            },
            success: function(result) {
                $(result)
                    .find('.purchase_quantity')
                    .each(function() {
                        row = $(this).closest('tr');

                        $('#purchase_entry_table tbody').append(
                            update_purchase_entry_row_values(row)
                        );
                        updatess();
                        // update_total_AND_QTY();
                    
                        //Check if multipler is present then multiply it when a new row is added.
                        if(__getUnitMultiplier(row) > 1){
                            row.find('select.sub_unit').trigger('change');
                        }
                    });
                if ($(result).find('.purchase_quantity').length) {
                    $('#row_count').val(
                        $(result).find('.purchase_quantity').length + parseInt(row_count)
                    );
                    
                    
                }
            },
        });
    }
    }
// ** 20
    function change_currency(){
        $('.purchase_table .purchase_unit_cost_new_currency').on("change" ,function(){
            var vl = $(this).val();
            var e  = $(this).parent().parent();
            var el = e.children().find(".purchase_unit_cost_with_tax_new_currency");
            var inc_vat = e.children().find(".purchase_unit_cost_with_tax");
            var exc_vat = e.children().find(".purchase_unit_cost_without_discount");
            var exc_vat_origin = e.children().find(".purchase_unit_cost_without_discount_origin");
            var exc_vat_s = e.children().find(".purchase_unit_cost_without_discount_s");
            var exc_dis_vat  = e.children().find(".purchase_unit_cost");
            var inc_dis_vat  = e.children().find(".total_unit_cost_with_tax");
            var tax_amount   = parseFloat($('#tax_id option:selected').data('tax_amount')) ;
            var tax_rate     = parseFloat($('option:selected', $('#tax_id')).data('tax_amount'));
            var dis_amount   = ( parseFloat(e.children().find('.inline_discounts').val()) > 0 )?parseFloat(e.children().find('.inline_discounts').val()) :0;
            var discount  = parseInt($('input[name="dis_type"]:checked').val());
            var dis_currency  = parseInt($('input[name="dis_currency"]:checked').val());
          
            

            currancy = $(".currency_id_amount").val();
            if(currancy != "" && currancy != 0){
                var  unit_tax   = ((tax_amount/100)*vl) + parseFloat(vl);
                var  percent    = currancy*vl;
                var  percent_tax    = ((tax_amount/100)*(currancy*vl)) + parseFloat(currancy*vl);
                exc_vat.val(percent.toFixed(2));
                exc_vat_origin.val(percent.toFixed(2));
                exc_vat_s.val(percent.toFixed(2));
                inc_vat.val(percent_tax.toFixed(2));
                el.val(unit_tax.toFixed(2));
            }else{
                el.val(0);
            }
            var tax_price =  parseFloat(exc_vat_origin.val()) + parseFloat((tax_rate/100)*exc_vat_origin.val());
            if (discount == 0) {
                var  amount         = exc_vat_origin.val()  -  (dis_amount/100)*exc_vat_origin.val() ;
                var  tax_amount_    = tax_price  -  (dis_amount/100)*exc_vat_origin.val() ;
            }else{
                var amount         = exc_vat_origin.val()  -   dis_amount ;
                var tax_amount_    = tax_price  -   dis_amount ;
                
            }
            exc_dis_vat.val(amount.toFixed(2));
            inc_dis_vat.val(((tax_amount/100)*(amount) + parseFloat(amount)).toFixed(2));
        

            os_total_sub();
            os_grand();
        
        });
        
    }
// ** 21
    function get_purchase_entry_row(product_id, variation_id,row=null,curr=null) {
        
        if (product_id) {
            var row_count =0;
            // alert(curr);
            if(row != null){
                row_count = row;
               
                
            }else{
                row_count = $('#row_count').val();
            }
            var global_price = $('#list_price').val();
            var location_id = $('#location_id').val();
            var contact_id  = $('#supplier_id option:selected').val();
            $.ajax({
                method: 'POST',
                url: '/purchases/get_purchase_entry_row',
                dataType: 'html',
                data: { 
                    product_id: product_id, 
                    currency: curr, 
                    row_count: row_count, 
                    variation_id: variation_id,
                    location_id: location_id,
                    contact_id: contact_id,
                    global_price: global_price
                },
                success: function(result) {
                    $(result)
                        .find('.purchase_quantity')
                        .each(function() {
                            row = $(this).closest('tr');

                            $('#purchase_entry_table tbody').append(
                                update_purchase_entry_row_values(row)
                            );
                            update_row_price_for_exchange_rate(row);
                            update_after_tax_price();
                            // update_discount();
                            update_inline_profit_percentage(row);
                            update_total_qty();
                            update_table_total();
                            update_grand_total();
                            update_table_sr_number();
                            total_bill();
                            discount_cal_amount2() ;
                            os_total_sub();
                            change_currency();
                            //Check if multipler is present then multiply it when a new row is added.
                            if(__getUnitMultiplier(row) > 1){
                                row.find('select.sub_unit').trigger('change');
                            }
                        });
                    if ($(result).find('.purchase_quantity').length) {
                        $('#row_count').val(
                            $(result).find('.purchase_quantity').length + parseInt(row_count)
                        );
                    
                    
                    }
                },
            });
        }
        total_bill();
    }
// ** 22
    function update_down(){
            
    }
