$(document).ready(function() {
    total_bill();
     //get suppliers
    //  $('#supplier_id').select2({
    //      ajax: {
    //          url: '/purchases/get_suppliers',
    //          dataType: 'json',
    //          delay: 250,
    //          data: function(params) {
    //              return {
    //                  q: params.term, // search term
    //                  page: params.page,
    //              };
    //          },
    //          processResults: function(data) {
    //              return {
    //                  results: data,
    //              };
    //          },
    //      },
    //      minimumInputLength: 1,
    //      escapeMarkup: function(m) {
    //          return m;
    //      },
    //      templateResult: function(data) {
    //          if (!data.id) {
    //              return data.text;
    //          }
    //          var html = data.text + ' - ' + data.business_name + ' (' + data.contact_id + ')';
    //          return html;
    //      }
    //  });

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

    $("#first_name").on("input",function(){
        $("#first_name").css({"outline":"0px solid red","box-shadow":"1px 1px 10px transparent","color":"gray"});
        $("#contact-submit").attr("disabled",false);
        
    });

    $("#first_name").on("change" ,function(e){
        var name = $("#first_name").val();
            // $("#name_p").css({"outline":"0px solid red","box-shadow":"1px 1px 10px transparent"});
        $.ajax({
            method: 'GET',
        url: '/contacts/check/' + name,
        async: false,
        data: {
            name: name,
        },
        dataType: 'json',
        success: function(result) {
            $results = result.status;
            if($results == true){
                toastr.error(LANG.product_name);
                $("#first_name").css({"outline":"1px solid red","box-shadow":"1px 1px 10px red","color":"red"})
                $("#contact-submit").attr("disabled",true);
            }
        }
    });
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
     if ($('#search_product_for_purchase_return').length > 0) {
         //Add Product
         $('#search_product_for_purchase_return')
             .autocomplete({
                 source: function(request, response) {
                     $.getJSON(
                         '/products/list',
                         { location_id: $('#location_id').val(), term: request.term ,return_check:true},
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
                     if (ui.item.qty_available > 0) {
                         $(this).val(null);
                         curr = $(".currency_id_amount").val();
                         purchase_return_product_row(ui.item.variation_id,curr);
                     } else {
                         alert(LANG.out_of_stock);
                     }
                 },
             })
             .autocomplete('instance')._renderItem = function(ul, item) {
             if (item.qty_available <= 0) {
                 var string = '<li class="ui-state-disabled">' + item.name;
                 if (item.type == 'variable') {
                     string += '-' + item.variation;
                 }
                 string += ' (' + item.sub_sku + ') (Out of stock) </li>';
                 return $(string).appendTo(ul);
             } else if (item.enable_stock != 1) {
                 return ul;
             } else {
                 var string = '<div>' + item.name;
                 if (item.type == 'variable') {
                     string += '-' + item.variation;
                 }
                 string += ' (' + item.sub_sku + ') </div>';
                 return $('<li>')
                     .append(string)
                     .appendTo(ul);
             }
         };
     }
 
     $('select#location_id').change(function() {
         if ($(this).val()) {
             $('#search_product_for_purchase_return').removeAttr('disabled');
         } else {
             $('#search_product_for_purchase_return').attr('disabled', 'disabled');
         }
         $('table#stock_adjustment_product_table tbody').html('');
         $('#product_row_index').val(0);
     });
 
     $(document).on('change', 'input.product_quantity', function() {
         update_table_row($(this).closest('tr'));
     });
     $(document).on('change', 'input.product_unit_price', function() {
         update_table_row($(this).closest('tr'));
     });
     $(document).on('click', '.remove_product_row', function() {
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
     //Date picker
     $('#transaction_date').datetimepicker({
         format: moment_date_format + ' ' + moment_time_format,
         ignoreReadonly: true,
     });

     $('form#purchase_return_form').validate();

     $(document).on('click', 'button#submit_purchase_return_form', function(e) {
         e.preventDefault();
 
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
 
     $('#tax_id').change(function() {
         update_table_total();
     });

     $('#purchase_return_product_table tbody')
         .find('.expiry_datepicker')
         .each(function() {
             $(this).datepicker({
                 autoclose: true,
                 format: datepicker_date_format,
             });
     });
 
     $('.currency_id_amount').change(function(){
         update_table_total();
         update_row();  
         discount_final();
         vats();
     });
});

function currency_changed(){
    $('.add_currency_id_amount').change(function(){
      update_shipping_row();
    });
    $('.add_currency_id').change(function(){
      alert("::s");
      var id = $(this).val();
      if(id == ""){
        $(".add_currency_id_amount").val("");
        // $(".check_dep_curr").addClass("hide" );
        // $('input[name="dis_currency"]').prop('checked', false);
        // $('#depending_curr').prop('checked', false);
        // discount_cal_amount2() ;
        //         os_total_sub();
        //         os_grand();
        update_shipping_row();
      }else{
        // $(".ship_curr").removeClass("hide" );
        // $(".check_dep_curr").removeClass("hide" );
        // $('input[name="dis_currency"]').prop('checked', true);
        // $('#depending_curr').prop('checked', true);
        $.ajax({
          url:"/symbol/amount/"+id,
          dataType: 'html',
          success:function(data){
            var object  = JSON.parse(data);
            $(".add_currency_id_amount").val(object.amount);
            // $(".cur_symbol").html( @json(__('purchase.sub_total_amount')) + " " + object.symbol + " : "  );
            update_shipping_row();
          },
        });	 
      }
    })
  }

function purchase_return_product_row(variation_id,curr=null) {
 var row_index   = parseInt($('#product_row_index').val());
 var location_id = $('#location_id').val();
 var check       = "Purchase";

 $.ajax({
     method: 'get',
     url: '/purchase-return/get_product_row?',
     data: { row_index: row_index, variation_id: variation_id, currency: curr, location_id: location_id },
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
 var quantity   = parseFloat(__read_number(tr.find('input.product_quantity')));
 var unit_price = parseFloat(__read_number(tr.find('input.product_unit_price')));
 var row_total = 0;
 if (quantity && unit_price) {
     row_total = quantity * unit_price;
 }
 tr.find('input.product_line_total').val(__number_f(row_total));
 update_table_total();
}

function all_changes(){
 
 $(".product_quantity").each(function(){
     var i = $(this);
     var el = i.parent().parent();
     el.children().find(".product_quantity").on("change",function(){
         update_row();  
     })
     el.children().find(".unit_price_before_dis_exc").on("change",function(){
         update_row();  
     })
     el.children().find(".unit_price_before_dis_inc").on("change",function(){
         type = 1; 
         update_row(type);  
     })
     el.children().find(".discount_amount_return").on("change",function(){
         var ele      = $(this).val();
         var currancy = $(".currency_id_amount").val();
         if(currancy != "" && currancy >= 0 ){
             var  i   = el.children().find(".unit_price_before_dis_exc_new_currency").val();
         }else{
             var  i   = el.children().find(".unit_price_before_dis_exc").val();
         }
         el.children().find(".discount_percent_return").val((ele*100/i).toFixed(2));
         update_row();
     })
     el.children().find(".discount_percent_return").on("change",function(){
         var ele = $(this).val();
         var currancy = $(".currency_id_amount").val();
         if(currancy != "" && currancy >= 0 ){
             var  i  = el.children().find(".unit_price_before_dis_exc_new_currency").val();
         }else{
             var  i  = el.children().find(".unit_price_before_dis_exc").val();
         }
         el.children().find(".discount_amount_return").val((ele*i/100).toFixed(2));
         update_row();
     })
 })
 $(".pos_quantity").each(function(){
     var i = $(this);
     var el = i.parent().parent().parent();
     el.children().find(".product_quantity").on("change",function(){
         update_row();  
     })
     el.children().find(".unit_price_before_dis_exc").on("change",function(){
         update_row();  
     })
     el.children().find(".unit_price_before_dis_inc").on("change",function(){
         type = 1;
         update_row(type);  
     })
     el.children().find(".discount_amount_return").on("change",function(){
         var ele      = $(this).val();
         var currancy = $(".currency_id_amount").val();
         if(currancy != "" && currancy >= 0 ){
             var  i   = el.children().find(".unit_price_before_dis_exc_new_currency").val();
         }else{
             var  i   = el.children().find(".unit_price_before_dis_exc").val();
         }
         el.children().find(".discount_percent_return").val((ele*100/i).toFixed(2));
    update_row();
     })
     el.children().find(".discount_percent_return").on("change",function(){
         var ele = $(this).val();
         var currancy = $(".currency_id_amount").val();
         if(currancy != "" && currancy >= 0 ){
             var  i  = el.children().find(".unit_price_before_dis_exc_new_currency").val();
         }else{
             var  i  = el.children().find(".unit_price_before_dis_exc").val();
         }
         el.children().find(".discount_amount_return").val((ele*i/100).toFixed(2));
        update_row();
     })
 })
 
 
}  

function init_tax(){
 var tax = $("#tax_id option:selected").data("tax_amount");
 var tax = $("#tax_calculated_amount").html("tax_amount");

}

function total_bill(){
    
    var total_amount_shiping = 0;
    var total_vat_shiping = 0;
    var total_shiping = 0;
    var  supplier_pay  =  0;
    var  cost_pay  =  0;
    var supplier_pay_curr          = 0;
    var cost_pay_curr              = 0;
    var supplier_id =  $('#supplier_id option:selected').val();
    $('.shipping_amount_s').each(function(){
    
    var el      =  $(this).parent().parent();
    var sup_id =   el.children().find('.supplier option:selected').val();
    var amount_curr  =  parseFloat(el.children().find('.shipping_amount_curr').val());
    el.children().find('.shipping_tax').val();
    var amount  =  parseFloat($(this).val()) ;
    var tax     =  parseFloat(el.children().find('.shipping_tax').val()) ;
    var tax_curr     =  parseFloat(el.children().find('.shipping_tax_curr').val()) ;
    total_vat_shiping += tax;
    var total_s = amount+tax; 
    total_amount_shiping += amount;
    total_shiping +=total_s;
    var total_curr_s      = amount_curr+tax_curr; 
    if ( (sup_id == supplier_id || sup_id == "" ) && supplier_id != ""  ) {
        supplier_pay +=total_s;
        supplier_pay_curr       += total_curr_s;
    }else{
        cost_pay +=total_s;
        cost_pay_curr           += total_curr_s;
    }
    el.children().find('.shipping_total').val(total_s);
    })
    $('#shipping_total_amount').text(total_amount_shiping);
    $('input[name="ADD_SHIP"]').val(supplier_pay);
    $('input[name="ADD_SHIP_"]').val(cost_pay);
    $('#shipping_total_vat_s').text(total_vat_shiping);
    $('#shipping_total_s').text(total_shiping);
    $('#cost_amount_supplier_curr').text(supplier_pay_curr.toFixed(2));
       
    $('#cost_amount_curr').text(cost_pay_curr.toFixed(2));
    var total_items    =  $("#total_subtotal_input_id").val();
    var sub_total_curr    = ($("#total_subtotal_input_cur").val() != null)?$("#total_subtotal_input_cur").val():0;
    var total          =  parseFloat($("#total_finals").html()).toFixed(2);       
    var ship  =  $("#total_ship_").val();       
    var ship_ =  $("#total_ship_c").val(); 
    currancy  =  $(".currency_id_amount").val();
    if(currancy != "" && currancy != 0){
        $("#total_final_i_curr").html(parseFloat(sub_total_curr).toFixed(2));       
        $("#grand_total_cur").html((((parseFloat(total) + parseFloat(ship)).toFixed(2))/currancy).toFixed(2));       
        $("#total_final_curr").html(((parseFloat(total) + parseFloat(ship) + parseFloat(ship_)).toFixed(2)/currancy).toFixed(2));       
    }  
    
    $("#total_final_i").html((parseFloat(total)).toFixed(2));      
    $("#total_final_hidden_").val((parseFloat(total) + parseFloat(ship)).toFixed(2));       
    $("#grand_total_hidden").val((parseFloat(total) + parseFloat(ship)).toFixed(2));       
    $("#grand_total").html((parseFloat(total) + parseFloat(ship)).toFixed(2));       
    $("#total_final_").html((parseFloat(total) + parseFloat(ship)).toFixed(2));       
    $("#total_final_x").html((parseFloat(total) + parseFloat(ship) + parseFloat(ship_)).toFixed(2));       
    $("#grand_total2").html((parseFloat(total) + parseFloat(ship) + parseFloat(ship_)).toFixed(2)); 
    $("#grand_total_items").html((parseFloat(total_items) + parseFloat(ship)).toFixed(2));       
    $("#final_total_hidden_items").val((parseFloat(total_items) + parseFloat(ship)).toFixed(2));       
    $("#total_final_items").html((parseFloat(total_items) + parseFloat(ship) + parseFloat(ship_)).toFixed(2));
    $("#total_final_items_").val((parseFloat(total_items) + parseFloat(ship) + parseFloat(ship_)).toFixed(2));
    $("#payment_due_").html(parseFloat($("#grand_total2").html()) - parseFloat($(".payment-amount").val()));       
    $(".hide_div").removeClass("hide");
    // console.log($(".hide_div").html());
   }

function update_row(type = null){
 
 $(".product_quantity").each(function(){
     // .... parent
     var i   = $(this).val();
     var el  = $(this).parent().parent();
     // console.log(el.html());
     var tax = $("#tax_id option:selected").data("tax_amount");
     var price_exc =  el.children().find(".unit_price_before_dis_exc");
     var currancy =  $(".currency_id_amount").val();
     // ..... get info
      if(type != null){
         var price  =  el.children().find(".unit_price_before_dis_inc").val();
         price_exc  =  el.children().find(".unit_price_before_dis_exc").val(parseFloat((price*100)/(100+tax)).toFixed(2));
     } 
     
     var dis_perce  =  el.children().find(".discount_percent_return").val();
     // el.children().find(".discount_amount_return").val(parseFloat((dis_perce*price_exc.val())/100).toFixed(2));

     var price_inc                     =  el.children().find(".unit_price_before_dis_inc");
     var price_after_exc               =  el.children().find(".unit_price_after_dis_exc");
     var price_after_inc               =  el.children().find(".unit_price_after_dis_inc");
     var price_exc_before_new_currency =  el.children().find(".unit_price_before_dis_exc_new_currency");
     var price_exc_after_new_currency  =  el.children().find(".unit_price_after_dis_exc_new_currency");
     var discount_amount_return =  el.children().find(".discount_amount_return").val();
      if(currancy != "" && currancy != null){
         var discount_amount_curr      =  el.children().find(".discount_amount_return").val();
         var discount_amount_return    =  parseFloat(el.children().find(".discount_amount_return").val()) * currancy;
      }
     var sub_total_price =  el.children().find(".pos_line_sub__total");
     
     //... tax 
     var percent   =  (price_exc.val()*tax/100).toFixed(2);
     var exc_price_before_dis =  parseFloat(price_exc.val()) + parseFloat(percent);
     
     // console.log("percent : "+price_exc);
     //... tax after 
     var percent_after   =  ((price_exc.val() - discount_amount_return)*tax/100).toFixed(2);
     var inc_price_af_dis =  parseFloat((price_exc.val() - discount_amount_return)) + parseFloat(percent_after);
      
     //.. set item values
     price_inc.val(exc_price_before_dis.toFixed(2));
     price_after_exc.val((price_exc.val() - discount_amount_return).toFixed(2));
     console.log(currancy);
     if(currancy != "" && currancy != null){
         price_exc_before_new_currency.val((parseFloat(price_exc.val()).toFixed(2)/parseFloat(currancy)).toFixed(2));
     console.log("#!1 " + price_exc.val());
     console.log(price_exc_before_new_currency.val());
     console.log(discount_amount_return);
         price_exc_after_new_currency.val( (parseFloat(price_exc_before_new_currency.val()) -  parseFloat(discount_amount_curr) ).toFixed(2));
     }
     price_after_inc.val((inc_price_af_dis).toFixed(2));
     sub_total_price.val((i*inc_price_af_dis).toFixed(2));
 });
 $(".pos_quantity").each(function(){
     // .... parent
     var i   = $(this).val();
     var el  = $(this).parent().parent().parent();
     // console.log(el.html());
     var tax = $("#tax_id option:selected").data("tax_amount");
     var price_exc =  el.children().find(".unit_price_before_dis_exc");
     // ..... get info
      if(type != null){
         var price  =  el.children().find(".unit_price_before_dis_inc").val();
         price_exc  =  el.children().find(".unit_price_before_dis_exc").val(parseFloat((price*100)/(100+tax)).toFixed(2));
     } 
     
     var dis_perce  =  el.children().find(".discount_percent_return").val();
     el.children().find(".discount_amount_return").val(parseFloat((dis_perce*price_exc.val())/100).toFixed(2));

     var price_inc       =  el.children().find(".unit_price_before_dis_inc");
     var price_after_exc =  el.children().find(".unit_price_after_dis_exc");
     var price_after_inc =  el.children().find(".unit_price_after_dis_inc");
     var price_exc_before_new_currency =  el.children().find(".unit_price_before_dis_exc_new_currency");
     var price_exc_after_new_currency  =  el.children().find(".unit_price_after_dis_exc_new_currency");
     var discount_amount_return =  el.children().find(".discount_amount_return").val();
     var sub_total_price =  el.children().find(".pos_line_sub__total");
     var currancy =  $(".currency_id_amount").val();
     
     //... tax 
     var percent   =  (price_exc.val()*tax/100).toFixed(2);
     var exc_price_before_dis =  parseFloat(price_exc.val()) + parseFloat(percent);
     
     // console.log("percent : "+price_exc);
     //... tax after 
     var percent_after   =  ((price_exc.val() - discount_amount_return)*tax/100).toFixed(2);
     var inc_price_af_dis =  parseFloat((price_exc.val() - discount_amount_return)) + parseFloat(percent_after);
      
     //.. set item values
     price_inc.val(exc_price_before_dis);
     price_after_exc.val(price_exc.val() - discount_amount_return);
     console.log(currancy);
     if(currancy != "" && currancy != null){
         price_exc_before_new_currency.val((parseFloat(price_exc.val()).toFixed(4)/parseFloat(currancy)).toFixed(2));
         price_exc_after_new_currency.val( (parseFloat(price_exc_before_new_currency.val()) - ( parseFloat( price_exc_before_new_currency.val() ) *dis_perce )  /100).toFixed(2));
     }
     price_after_inc.val((inc_price_af_dis).toFixed(2));
     sub_total_price.val((i*inc_price_af_dis).toFixed(2));
 });
 
 total_final();
 discount_final();
 vats(); 

 total_bill();
 // alert("ssss");
} 
function update_row_curr(type = null){
 
 $(".product_quantity").each(function(){
     // .... parent
     var i   = $(this).val();
     var el  = $(this).parent().parent();
     var currancy =  $(".currency_id_amount").val();
     // console.log(el.html());
     var tax = $("#tax_id option:selected").data("tax_amount");
     var price_exc_currency  =  el.children().find(".unit_price_before_dis_exc_new_currency");
     var price_exc =  el.children().find(".unit_price_before_dis_exc");
     // ..... get info
     //  if(type != null){
     //     var price  =  el.children().find(".unit_price_before_dis_inc").val();   
     //     price_exc  =  el.children().find(".unit_price_before_dis_exc").val(parseFloat((price*100)/(100+tax)).toFixed(2));
     // } 
     
     if(currancy != "" && currancy != null){
         if(type != null){
             var opposit_amount = (parseFloat(price_exc_currency.val()) * parseFloat(currancy)).toFixed(2); 
             el.children().find(".unit_price_before_dis_exc").val( opposit_amount );
             var price      =  el.children().find(".unit_price_before_dis_exc").val();
             var new_amount =  parseFloat(price)  + (price*tax/100);
             el.children().find(".unit_price_before_dis_inc").val(parseFloat(new_amount).toFixed(2));
         } 
     }
     
     
     var dis_perce  =  el.children().find(".discount_percent_return").val();
     // el.children().find(".discount_amount_return").val(parseFloat((dis_perce*price_exc.val())/100).toFixed(2));

     var price_inc       =  el.children().find(".unit_price_before_dis_inc");
     var price_after_exc =  el.children().find(".unit_price_after_dis_exc");
     var price_after_inc =  el.children().find(".unit_price_after_dis_inc");
     var price_exc_before_new_currency =  el.children().find(".unit_price_before_dis_exc_new_currency");
     var price_exc_after_new_currency  =  el.children().find(".unit_price_after_dis_exc_new_currency");
     var discount_amount_return =  el.children().find(".discount_amount_return").val();
      if(currancy != "" && currancy != null){
         var discount_amount_curr      =  el.children().find(".discount_amount_return").val();
         var discount_amount_return    =  parseFloat(el.children().find(".discount_amount_return").val()) * currancy;
      }
     var sub_total_price =  el.children().find(".pos_line_sub__total");
     
     //... tax 
     var percent   =  (price_exc.val()*tax/100).toFixed(2);
     var exc_price_before_dis =  parseFloat(price_exc.val()) + parseFloat(percent);
     
     // console.log("percent : "+price_exc);
     //... tax after 
     var percent_after   =  ((price_exc.val() - discount_amount_return)*tax/100).toFixed(2);
     var inc_price_af_dis =  parseFloat((price_exc.val() - discount_amount_return)) + parseFloat(percent_after);
      
     //.. set item values
     price_inc.val(exc_price_before_dis);
     price_after_exc.val(price_exc.val() - discount_amount_return);

     if(currancy != "" && currancy != null){
         // price_exc_before_new_currency.val((parseFloat(price_exc.val()).toFixed(4)/parseFloat(currancy)).toFixed(2));
         // price_exc_after_new_currency.val( (parseFloat(price_exc_before_new_currency.val()) - ( parseFloat( price_exc_before_new_currency.val() ) *dis_perce )  /100).toFixed(2));
     }
     price_after_inc.val((inc_price_af_dis).toFixed(2));
     sub_total_price.val((i*inc_price_af_dis).toFixed(2));
 });
 $(".pos_quantity").each(function(){
     // .... parent
     var i   = $(this).val();
     var el  = $(this).parent().parent().parent();
     // console.log(el.html());
     var tax = $("#tax_id option:selected").data("tax_amount");
     var price_exc =  el.children().find(".unit_price_before_dis_exc");
     // ..... get info
      if(type != null){
         var price  =  el.children().find(".unit_price_before_dis_inc").val();
         price_exc  =  el.children().find(".unit_price_before_dis_exc").val(parseFloat((price*100)/(100+tax)).toFixed(2));
     } 
     
     var dis_perce  =  el.children().find(".discount_percent_return").val();
     el.children().find(".discount_amount_return").val(parseFloat((dis_perce*price_exc.val())/100).toFixed(2));

     var price_inc       =  el.children().find(".unit_price_before_dis_inc");
     var price_after_exc =  el.children().find(".unit_price_after_dis_exc");
     var price_after_inc =  el.children().find(".unit_price_after_dis_inc");
     var price_exc_before_new_currency =  el.children().find(".unit_price_before_dis_exc_new_currency");
     var price_exc_after_new_currency  =  el.children().find(".unit_price_after_dis_exc_new_currency");
     var discount_amount_return =  el.children().find(".discount_amount_return").val();
     var sub_total_price =  el.children().find(".pos_line_sub__total");
     var currancy =  $(".currency_id_amount").val();
     
     //... tax 
     var percent   =  (price_exc.val()*tax/100).toFixed(2);
     var exc_price_before_dis =  parseFloat(price_exc.val()) + parseFloat(percent);
     
     // console.log("percent : "+price_exc);
     //... tax after 
     var percent_after   =  ((price_exc.val() - discount_amount_return)*tax/100).toFixed(2);
     var inc_price_af_dis =  parseFloat((price_exc.val() - discount_amount_return)) + parseFloat(percent_after);
      
     //.. set item values
     price_inc.val(exc_price_before_dis);
     price_after_exc.val(price_exc.val() - discount_amount_return);
     console.log(currancy);
     if(currancy != "" && currancy != null){
         price_exc_before_new_currency.val((parseFloat(price_exc.val()).toFixed(4)/parseFloat(currancy)).toFixed(2));
         price_exc_after_new_currency.val( (parseFloat(price_exc_before_new_currency.val()) - ( parseFloat( price_exc_before_new_currency.val() ) *dis_perce )  /100).toFixed(2));
     }
     price_after_inc.val((inc_price_af_dis).toFixed(2));
     sub_total_price.val((i*inc_price_af_dis).toFixed(2));
 });
 
 total_final();
 discount_final();
 vats(); 

 total_bill();
 // alert("ssss");
} 

function change_currency(){
    
    $('.unit_price_before_dis_exc_new_currency').change(function(){
        
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
        console.log(x+'vatss'); 
        console.log(px+'dissss');
        el.children().find('.discount_amount_return').val(dap.toFixed(2));
    update_table_total();
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
        
        
        exc_dis_vat.val(amount.toFixed(2));
        inc_dis_vat.val(((tax_amount/100)*(amount) + parseFloat(amount)).toFixed(2));
        exc_dis_vat_curr.val((amount.toFixed(2)/currancy).toFixed(2));
        
        update_table_total();
        
        
    });
 
}

function total_final(){

 var total_qty       = 0;
 var total_amount    = 0;
 var total_amount_curr    = 0;
 var qty_total       = $("#total_qty_return");
 var amount_total    = $("#total_return_");
 var grand_total     = $("#total_subtotal_cur");
 var grand_total_hidden_curr    = $("#total_subtotal_input_cur");
 var final_sub_total = $("#total_return_input");
 // console.log(final_sub_total);
 $(".product_quantity").each(function(){ 
     //..... item
     var i   = $(this).val();
     var el  = $(this).parent().parent();
     //...... get info 
     var sub_total_price      =  el.children().find(".unit_price_after_dis_exc").val();
     var sub_total_price_curr =  el.children().find(".unit_price_after_dis_exc_new_currency").val();
     total_qty         += parseFloat(i);
     total_amount      += parseFloat(i*sub_total_price);
     total_amount_curr += parseFloat(i*sub_total_price_curr);
 });

 //.. set item values
 qty_total.html(total_qty);
 amount_total.html(total_amount.toFixed(2));
 currancy = $(".currency_id_amount").val();
 if(currancy != "" && currancy != null){
     grand_total.text(total_amount_curr.toFixed(2));
     grand_total_hidden_curr.val(total_amount_curr.toFixed(2));
 }
 amount_total.html(total_amount.toFixed(2));
 final_sub_total.val(total_amount.toFixed(2));
 update_table_total();
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
     
     if(currancy != "" && currancy != 0){
         fin_dis = dis_amount;
         tax     = $("#tax_id option:selected").data("tax_amount");
         dis_text.html(parseFloat(fin_dis*currancy).toFixed(2));
         discount.val((fin_dis*currancy).toFixed(2));
        
         $('#discount_calculated_amount_cur').text(parseFloat(fin_dis).toFixed(2));
     }else{
         fin_dis = dis_amount;
         tax     = $("#tax_id option:selected").data("tax_amount");
         dis_text.html(parseFloat(fin_dis).toFixed(2));
         discount.val((fin_dis));
         
     }
     vats();
 }else if(dis_type == "fixed_after_vat"){
     
     if(currancy != "" && currancy != 0){
         
         tax     = $("#tax_id option:selected").data("tax_amount");
         perc_curr    =  (dis_amount*tax)/(100+tax);
         fin_dis_curr =  dis_amount - perc_curr ;
         var dis_c    =  dis_amount * currancy ;
         perc    = (dis_c*tax)/(100+tax);
         fin_dis =  dis_c - perc ;

         dis_text.html(parseFloat(fin_dis).toFixed(2));
         discount.val((fin_dis));
         $('#discount_calculated_amount_cur').text(parseFloat(fin_dis_curr).toFixed(2));
     }else{
         tax     = $("#tax_id option:selected").data("tax_amount");
         perc    = (dis_amount*tax)/(100+tax);
         fin_dis =  dis_amount - perc ;
         dis_text.html(parseFloat(fin_dis).toFixed(2));
         discount.val((fin_dis));
         
     }
     
     vats();
 }else if(dis_type == "percentage"){
     fin_dis =  (dis_amount*sub_total)/100;
     dis_text.html((parseFloat(fin_dis).toFixed(2)));
     discount.val((fin_dis));
     if(currancy != "" && currancy != 0){
         $('#discount_calculated_amount_cur').text((fin_dis/currancy).toFixed(2));
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
function fl_price(){
    var sub_total = $("#total_return_input").val();
    var sub_total_curr = $("#total_subtotal_input_cur").val();
    var discount  = $("#discount_amount2").val();
    var vat_input = $("#tax_amount2").val();
    finals = (sub_total - discount) + parseFloat(vat_input);
    currancy = $(".currency_id_amount").val();
    // console.log("sub_total : "+sub_total);
    // console.log("discount : "+discount);
    // console.log("final : "+finals);
    if(currancy != "" && currancy != 0){
        discount_curr  = (discount/currancy).toFixed(2);
        var tax       = $("#tax_id option:selected").data("tax_amount");
        finals_curr    = ( parseFloat(sub_total_curr) - parseFloat(discount_curr)) + parseFloat(((sub_total_curr-discount_curr)*tax/100).toFixed(2));
        // $("#total_final_i_curr").html(parseFloat(finals_curr).toFixed(2)) ;
    }
    $("#total_finals").html(parseFloat(finals).toFixed(2)) ;
    $("#total_finals_").val(parseFloat(finals).toFixed(2)) ;
    $("#total_final_input").val(parseFloat(finals).toFixed(2)) ;
}
function final_price(){
    var sub_total = $("#total_return_input").val();
    var sub_total_curr = $("#total_subtotal_input_cur").val();
    var discount  = $("#discount_amount2").val();
    var vat_input = $("#tax_amount2").val();
    finals = (sub_total - discount) + parseFloat(vat_input);
    currancy = $(".currency_id_amount").val();
    // console.log("sub_total : "+sub_total);
    // console.log("discount : "+discount);
    // console.log("final : "+finals);
    if(currancy != "" && currancy != 0){
        discount_curr  = (discount/currancy).toFixed(2);
        var tax       = $("#tax_id option:selected").data("tax_amount");
        finals_curr    = ( parseFloat(sub_total_curr) - parseFloat(discount_curr)) + parseFloat(((sub_total_curr-discount_curr)*tax/100).toFixed(2));
        // $("#total_final_i_curr").html(parseFloat(finals_curr).toFixed(2)) ;
    }
    $("#total_finals").html(parseFloat(finals).toFixed(2)) ;
    $("#total_finals_").val(parseFloat(finals).toFixed(2)) ;
    $("#total_final_input").val(parseFloat(finals).toFixed(2)) ;
}
function vats(){
    var sub_total = $("#total_return_input").val();
    var sub_total_curr = $("#total_subtotal_input_cur").val();
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
        discount_curr  = (discount/currancy).toFixed(2);
        $('#tax_calculated_amount_curr').text(((sub_total_curr-discount_curr)*tax/100).toFixed(2));
    }
    fl_price();
}

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

function get_stock_adjustment_details(rowData) {
 var div = $('<div/>')
     .addClass('loading')
     .text('Loading...');
 $.ajax({
     url: '/stock-adjustments/' + rowData.DT_RowId,
     dataType: 'html',
     success: function(data) {
         div.html(data).removeClass('loading');
     },
 });

 return div;
}
