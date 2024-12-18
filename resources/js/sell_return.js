$(document).ready(function() {

    // alert($("#submit_purchase_return_form").html());
 
    //For edit pos form
    if ($('form#sell_return_form').length > 0) {
        pos_form_obj = $('form#sell_return_form');
    } else {
        pos_form_obj = $('form#add_pos_sell_form');
    }
    if ($('form#sell_return_form').length > 0 || $('form#add_pos_sell_form').length > 0) {
        initialize_printer();
    }

    //Date picker
    $('#transaction_date').datetimepicker({
        format: moment_date_format + ' ' + moment_time_format,
        ignoreReadonly: true,
    });

    pos_form_validator = pos_form_obj.validate({
        submitHandler: function(form) {
            var cnf = true;

            if (cnf) {
                var data = $(form).serialize();
                var url = $(form).attr('action');
                $.ajax({
                    method: 'POST',
                    url: url,
                    data: data,
                    dataType: 'json',
                    success: function(result) {
                        if (result.success == 1) {
                            toastr.success(result.msg);
                            //Check if enabled or not
                            if (result.receipt.is_enabled) {
                                pos_print(result.receipt);
                            }
                        } else {
                            toastr.error(result.msg);
                        }
                    },
                });
            }
            return false;
        },
    });
});
//Add products
if ($('#search_product_for_purchase_return').length > 0) {
    //Add Product
    $('#search_product_for_purchase_return')
        .autocomplete({
            source: function(request, response) {
                $.getJSON(
                    '/products/list',
                    { location_id: $('#location_id').val(), term: request.term },
                    response
                );
            },
            minLength: 2,
            response: function(event, ui) {
                if (ui.content.length == 1) {
                    
                    ui.item = ui.content[0];
                    if (ui.item.qty_available > 0 && ui.item.enable_stock == 1) {
                        $(this)
                            .data('ui-autocomplete')
                            ._trigger('select', 'autocompleteselect', ui);
                        $(this).autocomplete('close');
                    }
                } else if (ui.content.length == 0) {
                    swal(LANG.no_products_found);
                }
            },
            focus: function(event, ui) {
                if (ui.item.qty_available <= 0) {
                    return false;
                }
            },
            select: function(event, ui) {
                if (ui.item.enable_stock ==  0){
                    $(this).val(null);
                    curr = $(".currency_id_amount").val();
                    purchase_return_product_row(ui.item.variation_id,null,curr);
                }else if (ui.item.qty_available > 0) {
                    $(this).val(null);
                    curr = $(".currency_id_amount").val();
                    purchase_return_product_row(ui.item.variation_id,null,curr);
                } else {
                    alert(LANG.out_of_stock);
                }
            },
        })
        .autocomplete('instance')._renderItem = function(ul, item) {
        // if (item.qty_available <= 0) {
        //     var string = '<li class="ui-state-disabled">' + item.name;
        //     if (item.type == 'variable') {
        //         string += '-' + item.variation;
        //     }
        //     string += ' (' + item.sub_sku + ') (Out of stock) </li>';
        //     return $(string).appendTo(ul);
        // } else 
         
            var string = '<div>' + item.name;
            if (item.type == 'variable') {
                string += '-' + item.variation;
            }
            string += ' (' + item.sub_sku + ') </div>';
            return $('<li>')
                .append(string)
                .appendTo(ul);
         
    };
}
$(document).on('change', 'input.product_quantity', function() {
    update_table_row($(this).closest('tr'));
});
$(document).on('change', 'input.product_unit_price', function() {
    update_table_row($(this).closest('tr'));
});
$(document).on('click', '.pos_remove_row', function() {
    swal({
        title: LANG.sure,
        icon: 'warning',
        buttons: true,
        dangerMode: true,
    }).then(willDelete => {
        if (willDelete) {
            $(this)
                .closest('tr')
                .remove();
                update_table_total();
                update_row();
                discount_final();
                vats();
                final_price();
        }
    });
});
 $(document).on('click', 'button#submit_purchase_return_form', function(e) {
        e.preventDefault();
        $('button#submit_purchase_return_form').attr("disabled",true);
        //Check if product is present or not.
        if ($('table#purchase_return_product_table tbody tr').length <= 0) {
            toastr.warning(LANG.no_products_added);
            $('input#search_product_for_purchase_return').select();
            return false;
        }

        if ($('form#purchase_return_form').valid()) {
            $('form#purchase_return_form').submit();
        }
});
$(document).on("change","#tax_id",function(){
    update_row();  
    vats();
}); 
$(document).on("change","#discount_type",function(){
    update_row();  
    discount_final();
    vats();
}); 
$(document).on("change","#discount_amount",function(){
    update_row();  
    discount_final();
    vats();
}); 

function purchase_return_product_row(variation_id,edit=null,currency=null) {
   
    var row_index = parseInt($('#product_row_index').val());
     
    var location_id = $('#location_id').val();
    var check = "Sale";
    $.ajax({
        method: 'POST',
        url: '/purchase-return/get_product_row?check='+check,
        data: { row_index: row_index, variation_id: variation_id, location_id: location_id,currency: currency },
        dataType: 'html',
        success: function(result) {
            $('table#purchase_return_product_table tbody').append(result);
            
            $('table#purchase_return_product_table tbody tr:last').find('.expiry_datepicker').datepicker({
                autoclose: true,
                format: datepicker_date_format,
            });
            update_row();
            all_changes();
            change_currency();
            update_table_total();
            $('#product_row_index').val(row_index + 1);
        },
    });
}

function update_table_total() {
    var table_total = 0;
    $('table#purchase_return_product_table tbody tr').each(function() {
        var this_total = parseFloat(__read_number($(this).find('input.product_line_total')));
        if (this_total) {
            table_total += this_total;
        }
    });
    var tax_rate = parseFloat($('option:selected', $('#tax_id')).data('tax_amount'));
    var tax = __calculate_amount('percentage', tax_rate, table_total);
    __write_number($('input#tax_amount'), tax);
    var final_total = table_total + tax;
    $('input#total_amount').val(final_total);
    $('span#total_return').text(__number_f(final_total));
}

function update_table_row(tr) {
    var quantity = parseFloat(__read_number(tr.find('input.product_quantity')));
    var unit_price = parseFloat(__read_number(tr.find('input.product_unit_price')));
    var row_total = 0;
    if (quantity && unit_price) {
        row_total = quantity * unit_price;
    }
    tr.find('input.product_line_total').val(__number_f(row_total));
    update_table_total();
}

function initialize_printer() {
    if ($('input#location_id').data('receipt_printer_type') == 'printer') {
        initializeSocket();
    }
}

function pos_print(receipt) {
    //If printer type then connect with websocket
    if (receipt.print_type == 'printer') {
        var content = receipt;
        content.type = 'print-receipt';

        //Check if ready or not, then print.
        if (socket.readyState != 1) {
            initializeSocket();
            setTimeout(function() {
                socket.send(JSON.stringify(content));
            }, 700);
        } else {
            socket.send(JSON.stringify(content));
        }
    } else if (receipt.html_content != '') {
        //If printer type browser then print content
        $('#receipt_section').html(receipt.html_content);
        __currency_convert_recursively($('#receipt_section'));
        setTimeout(function() {
        
            window.location.href = "/sell-return";
        }, 100);
    }
}

function all_changes(){
    $(".pos_quantity").each(function(){
        var i = $(this);
        var el = i.parent().parent();
        el.children().find(".pos_quantity").on("change",function(){
            update_row();  
        })
        el.parent().children().find(".unit_price_before_dis_exc").on("change",function(){
            update_row();  
        })
        
        el.parent().children().find(".unit_price_before_dis_inc").on("change",function(){
            type = 1;
            update_row(type);   
        })
        el.parent().parent().children().find(".unit_price_before_dis_exc_new_currency").on("change",function(){
                var  el    =  $(this).parent().parent();
                var price  =  parseFloat($(this).val());
                var vat    = 0;
                var discount_percentage = el.children().find('.discount_percent_return').val();
                if ($('#tax_id option:selected').data('tax_amount')) {
                    var vat =  parseFloat($('#tax_id option:selected').data('tax_amount'));
                }
                var x   =  price + ((vat/100)*price);
                var dap = (price * (discount_percentage/100));
                var px  =  price - dap ;
                // console.log(x+'vatss'); 
                // console.log(px+'dissss');
                el.children().find('.discount_amount_return').val(dap.toFixed(2));
                var vl = $(this).val();
                var e  = $(this).parent().parent();
                // var el = e.children().find(".purchase_unit_cost_with_tax_new_currency");
                var inc_vat      = e.children().find(".unit_price_before_dis_inc");
                var exc_vat      = e.children().find(".unit_price_before_dis_exc");
                var exc_dis_vat  = e.children().find(".unit_price_after_dis_exc");
                var inc_dis_vat  = e.children().find(".unit_price_after_dis_inc");
                var exc_dis_vat_curr  = e.children().find(".unit_price_after_dis_exc_new_currency");
                var tax_amount   = parseFloat($('#tax_id option:selected').data('tax_amount')) ;
                var tax_rate     = parseFloat($('option:selected', $('#tax_id')).data('tax_amount'));
                var dis_amount   = ( parseFloat(e.children().find('.discount_amount_return').val()) > 0 )?parseFloat(e.children().find('.discount_amount_return').val()) :0;
                var discount     = parseInt($('input[name="dis_type"]:checked').val());
                
                currancy = $(".currency_id_amount").val();
                if(currancy != "" && currancy != 0){
                    var  unit_tax   = ((tax_amount/100)*vl) + parseFloat(vl);
                    var  percent    = currancy*vl;
                    var  percent_tax    = ((tax_amount/100)*(currancy*vl)) + parseFloat(currancy*vl);
                    exc_vat.val(percent.toFixed(2)); 
                    inc_vat.val(percent_tax.toFixed(2));
                } 
                var tax_price      = parseFloat(exc_vat.val()) + parseFloat((tax_rate/100)*exc_vat.val());
                
                var amount         = exc_vat.val()  -   dis_amount ;
                var tax_amount_    = tax_price  -   dis_amount ;
                
                
                exc_dis_vat.val(amount.toFixed(4));
                inc_dis_vat.val(((tax_amount/100)*(amount) + parseFloat(amount)).toFixed(2));
                exc_dis_vat_curr.val((amount.toFixed(4)/currancy).toFixed(4));
            
        })
        $(".discount_amount_return").on("change",function(){
            var el  = $(this).parent().parent();
            var ele = $(this).val();
            var  i  = el.children().find(".unit_price_before_dis_exc").val();
            
            el.children().find(".discount_percent_return").val((ele*100/i).toFixed(2));
            update_row(); 
        })
        $(".discount_percent_return").on("change",function(){
            var el  = $(this).parent().parent();
            var ele = $(this).val();
            var  i  = el.children().find(".unit_price_before_dis_exc").val();
            // console.log(ele*i/100);  
            el.children().find(".discount_amount_return").val((ele*i/100).toFixed(2));
            update_row();
        })
    })
    
    
}  

function init_tax(){
    var tax = $("#tax_id option:selected").data("tax_amount");
    var tax = $("#tax_calculated_amount").html("tax_amount");

}

function update_row(type = null){
    
    $(".pos_quantity").each(function(){
        // .... parent
        var i   = $(this).val();
        var el  = $(this).parent().parent();
        var tax = $("#tax_id option:selected").data("tax_amount");
        var price_exc =  el.parent().children().find(".unit_price_before_dis_exc");

        if(type != null){
            var price  =  el.parent().children().find(".unit_price_before_dis_inc").val();
            price_exc  =  el.parent().children().find(".unit_price_before_dis_exc").val(parseFloat((price*100)/(100+tax)).toFixed(2));
        } 
        var dis_perce  =  el.parent().children().find(".discount_percent_return").val();
        // el.parent().children().find(".discount_amount_return").val(parseFloat((dis_perce*price_exc.val())/100).toFixed(2));

        // ..... get info
        var price_inc =  el.parent().children().find(".unit_price_before_dis_inc");
        var price_after_exc =  el.parent().children().find(".unit_price_after_dis_exc");
        var price_after_inc =  el.parent().children().find(".unit_price_after_dis_inc");
        var price_exc_before_new_currency =  el.parent().children().find(".unit_price_before_dis_exc_new_currency");
        var price_exc_after_new_currency  =  el.parent().children().find(".unit_price_after_dis_exc_new_currency");
        var discount_amount_return =  el.parent().children().find(".discount_amount_return").val();
        var sub_total_price =  el.parent().children().find(".pos_line_sub__total");
        var currancy =  $(".currency_id_amount").val();

        //... tax 
        var percent   =  (price_exc.val()*tax/100).toFixed(2);
        var exc_price_before_dis =  parseFloat(price_exc.val()) + parseFloat(percent);
        //... tax after 
        var percent_after   =  ((price_exc.val() - discount_amount_return)*tax/100).toFixed(2);
        var inc_price_af_dis =  parseFloat((price_exc.val() - discount_amount_return)) + parseFloat(percent_after);
        //.. set item values
        price_inc.val(exc_price_before_dis);
        price_after_exc.val(price_exc.val() - discount_amount_return);
        if(currancy != "" && currancy != null){
             price_exc_after_new_currency.val((parseFloat(price_after_exc.val()).toFixed(4)/parseFloat(currancy)).toFixed(4));
            price_exc_before_new_currency.val((parseFloat(price_exc.val()).toFixed(4)/parseFloat(currancy)).toFixed(4));
            change_currency();
        }
        price_after_inc.val(inc_price_af_dis);
        sub_total_price.val((i*inc_price_af_dis).toFixed(2));
    });
    total_final();
    discount_final();
    vats(); 
} 
function change_currency(){
    
    $('.product_row .unit_price_before_dis_exc_new_currency').change(function(){
        
        var  el    =  $(this).parent().parent();
        var price  =  parseFloat($(this).val());
        var vat    = 0;
        var discount_percentage = el.children().find('.discount_percent_return').val();
        if ($('#tax_id option:selected').data('tax_amount')) {
            var vat =  parseFloat($('#tax_id option:selected').data('tax_amount'));
        }
        var x   =  price + ((vat/100)*price);
        var dap = (price * (discount_percentage/100));
        var px  =  price - dap ;
        // console.log(x+'vatss'); 
        // console.log(px+'dissss');
        el.children().find('.discount_amount_return').val(dap.toFixed(2));
     
    })
    $('#purchase_return_product_table .unit_price_before_dis_exc_new_currency').on("change" ,function(){
        var vl = $(this).val();
        var e  = $(this).parent().parent();
        // var el = e.children().find(".purchase_unit_cost_with_tax_new_currency");
        var inc_vat      = e.children().find(".unit_price_before_dis_inc");
        var exc_vat      = e.children().find(".unit_price_before_dis_exc");
        var exc_dis_vat  = e.children().find(".unit_price_after_dis_exc");
        var inc_dis_vat  = e.children().find(".unit_price_after_dis_inc");
        var exc_dis_vat_curr  = e.children().find(".unit_price_after_dis_exc_new_currency");
        var tax_amount   = parseFloat($('#tax_id option:selected').data('tax_amount')) ;
        var tax_rate     = parseFloat($('option:selected', $('#tax_id')).data('tax_amount'));
        var dis_amount   = ( parseFloat(e.children().find('.discount_amount_return').val()) > 0 )?parseFloat(e.children().find('.discount_amount_return').val()) :0;
        var discount     = parseInt($('input[name="dis_type"]:checked').val());
        
        currancy = $(".currency_id_amount").val();
        if(currancy != "" && currancy != 0){
            var  unit_tax   = ((tax_amount/100)*vl) + parseFloat(vl);
            var  percent    = currancy*vl;
            var  percent_tax    = ((tax_amount/100)*(currancy*vl)) + parseFloat(currancy*vl);
            exc_vat.val(percent.toFixed(2)); 
            inc_vat.val(percent_tax.toFixed(2));
        } 
        var tax_price      = parseFloat(exc_vat.val()) + parseFloat((tax_rate/100)*exc_vat.val());
        
        var amount         = exc_vat.val()  -   dis_amount ;
        var tax_amount_    = tax_price  -   dis_amount ;
        
        
        exc_dis_vat.val(amount.toFixed(4));
        inc_dis_vat.val(((tax_amount/100)*(amount) + parseFloat(amount)).toFixed(2));
        exc_dis_vat_curr.val((amount.toFixed(4)/currancy).toFixed(4));
        
         
        
        
    });
    
}
function total_final(){

    var total_qty       = 0;
    var total_amount    = 0;
    var qty_total       = $("#total_qty_return");
    var amount_total    = $("#total_return_");
    var grand_total    = $("#total_subtotal_cur");

    var final_sub_total = $("#total_return_input");
    // console.log(final_sub_total);
    $(".pos_quantity").each(function(){ 
        //..... item
        var i   = $(this).val();
        var el  = $(this).parent().parent();
        //...... get info 
        var sub_total_price =  el.parent().children().find(".unit_price_after_dis_exc").val();
        total_qty += parseFloat(i);
        total_amount += parseFloat(i*sub_total_price);
    });

    //.. set item values
    qty_total.html(total_qty);
    amount_total.html(total_amount.toFixed(2));
    currancy = $(".currency_id_amount").val();
    if(currancy != "" && currancy != null){
        grand_total.text((total_amount/currancy).toFixed(4));
    }
    final_sub_total.val(total_amount);
    discount_final();
    vats();
}

function discount_final(){
    var sub_total = $("#total_return_input").val();
    var dis_type  = $("#discount_type").val();
    var dis_amount= $("#discount_amount").val();
    var dis_text  = $("#discount_calculated_amount2");
    var discount  = $("#discount_amount2");
    var currancy  = $(".currency_id_amount").val();

    if(dis_type == "fixed_before_vat"){
       
        fin_dis = dis_amount;
        tax = $("#tax_id option:selected").data("tax_amount");
        dis_text.html(parseFloat(fin_dis).toFixed(2));
        discount.val((fin_dis));
        if(currancy != "" && currancy != 0){
            $('#discount_calculated_amount_cur').text((fin_dis/currancy).toFixed(4));
        }
        vats();
    }else if(dis_type == "fixed_after_vat"){
        tax  = $("#tax_id option:selected").data("tax_amount");
        perc = (dis_amount*tax)/(100+tax);
        fin_dis =  dis_amount - perc ;
        dis_text.html(parseFloat(fin_dis).toFixed(2));
        discount.val((fin_dis));
        if(currancy != "" && currancy != 0){
            $('#discount_calculated_amount_cur').text((fin_dis/currancy).toFixed(4));
        }
        vats();
    }else if(dis_type == "percentage"){
        fin_dis =  (dis_amount*sub_total)/100;
        dis_text.html(parseFloat(fin_dis).toFixed(2));
        discount.val((fin_dis));
        if(currancy != "" && currancy != 0){
            $('#discount_calculated_amount_cur').text((fin_dis/currancy).toFixed(4));
        }
        vats();
    }else{
        dis_text.html(0 );
        discount.val( 0 );
        if(currancy != "" && currancy != 0){
            $('#discount_calculated_amount_cur').text((0).toFixed(4));
        }
        vats();
    }
    // console.log("type : " + dis_type + " dis : "+discount.val());
}

function vats(){
    var sub_total = $("#total_return_input").val();
    var discount  = $("#discount_amount2").val();
    var fin = sub_total - discount;
    var vat_text  = $("#tax_calculated_amount");
    var vat_input = $("#tax_amount2");
    var tax       = $("#tax_id option:selected").data("tax_amount");
    var vat       = (fin*tax)/100;
    vat_text.html(vat.toFixed(2));
    vat_input.val(vat.toFixed(2));
    currancy = $(".currency_id_amount").val();
    if(currancy != "" && currancy != 0){
        $('#tax_calculated_amount_curr').text((vat/currancy).toFixed(4));
    }
    final_price();
}

function final_price(){
    var sub_total = $("#total_return_input").val();
    var discount  = $("#discount_amount2").val();
    var vat_input = $("#tax_amount2").val();
    finals = (sub_total - discount) + parseFloat(vat_input);
    currancy = $(".currency_id_amount").val();

    // console.log("sub_total : "+sub_total);
    // console.log("discount : "+discount);
    // console.log("final : "+finals);
    $("#total_final_").html(parseFloat(finals).toFixed(2)) ;
    $("#total_final_i_curr").html(parseFloat(finals/currancy).toFixed(4)) ;

    $("#total_final_input").val(parseFloat(finals).toFixed(2)) ;
}
 

// //Set the location and initialize printer
// function set_location(){
// 	if($('input#location_id').length == 1){
// 	       $('input#location_id').val($('select#select_location_id').val());
// 	       //$('input#location_id').data('receipt_printer_type', $('select#select_location_id').find(':selected').data('receipt_printer_ty
// 	}

// 	if($('input#location_id').val()){
// 	       $('input#search_product').prop( "disabled", false ).focus();
// 	} else {
// 	       $('input#search_product').prop( "disabled", true );
// 	}

// 	initialize_printer();
// }
