//This file contains all functions used products tab
 
 //***1****  Section-Starting //
//***************************//
$(document).ready(function() {
        $("#sub_unit_ids").select2({
            maximumSelectionLength: 2
        });
        $(document).on('ifChecked', 'input#enable_stock', function() {
            $('div#alert_quantity_div').show();
            $('div#quick_product_opening_stock_div').show();

            //Enable expiry selection
            if ($('#expiry_period_type').length) {
                $('#expiry_period_type').removeAttr('disabled');
            }

            if ($('#opening_stock_button').length) {
                $('#opening_stock_button').removeAttr('disabled');
            }
        });
        $(document).on('ifUnchecked', 'input#enable_stock', function() {
            $('div#alert_quantity_div').hide();
            $('div#quick_product_opening_stock_div').hide();
            $('input#alert_quantity').val(0);

            //Disable expiry selection
            if ($('#expiry_period_type').length) { 
                $('#expiry_period_type')
                    .val('')
                    .change();
                $('#expiry_period_type').attr('disabled', true);
            }
            if ($('#opening_stock_button').length) {
                $('#opening_stock_button').attr('disabled', true);
            }
        });
        //Start For product type single
        //If tax rate is changed
        $(document).on('change', 'select#tax', function() {
            if ($('select#type').val() == 'single') {
                var purchase_exc_tax = __read_number($('input#single_dpp1'));
                purchase_exc_tax = purchase_exc_tax == undefined ? 0 : purchase_exc_tax;

                var tax_rate = $('select#tax')
                    .find(':selected')
                    .data('rate');
                tax_rate = tax_rate == undefined ? 0 : tax_rate;

                var purchase_inc_tax = __add_percent(purchase_exc_tax, tax_rate);
                __write_number($('input#single_dpp_inc_tax1'), purchase_inc_tax);

                var selling_price = __read_number($('input#single_dsp1'));
                var selling_price_inc_tax = __add_percent(selling_price, tax_rate);
                __write_number($('input#single_dsp_inc_tax1'), selling_price_inc_tax);
            }
        });


        ready();


        $(document).on('click', '.submit_product_form', function(e) {
            e.preventDefault();
            var submit_type = $(this).attr('value');
            $('#submit_type').val(submit_type);
            $('form#product_add_form').validate({
                rules: {
                    sku: {
                        remote: {
                            url: '/products/check_product_sku',
                            type: 'post',
                            data: {
                                sku: function() {
                                    return $('#sku').val();
                                },
                                product_id: function() {
                                    if ($('#product_id').length > 0) {
                                        return $('#product_id').val();
                                    } else {
                                        return '';
                                    }
                                },
                            },
                        },
                    },
                    expiry_period: {
                        required: {
                            depends: function(element) {
                                return (
                                    $('#expiry_period_type')
                                        .val()
                                        .trim() != ''
                                );
                            },
                        },
                    },
                },
                messages: {
                    sku: {
                        remote: LANG.sku_already_exists,
                    },
                },
            });
            if ($('form#product_add_form').valid()) {
                $('form#product_add_form').submit();
            }
        });
        //End for product type single

        //Start for product type Variable
        //If purchase price exc tax is changed
        $(document).on('change', 'input.variable_dpp', function(e) {
            var tr_obj = $(this).closest('tr');

            var purchase_exc_tax = __read_number($(this));
            purchase_exc_tax = purchase_exc_tax == undefined ? 0 : purchase_exc_tax;

            var tax_rate = $('select#tax')
                .find(':selected')
                .data('rate');
            tax_rate = tax_rate == undefined ? 0 : tax_rate;

            var purchase_inc_tax = __add_percent(purchase_exc_tax, tax_rate);
            __write_number(tr_obj.find('input.variable_dpp_inc_tax'), purchase_inc_tax);

            var profit_percent = __read_number(tr_obj.find('input.variable_profit_percent'));
            var selling_price = __add_percent(purchase_exc_tax, profit_percent);
            __write_number(tr_obj.find('input.variable_dsp'), selling_price);

            var selling_price_inc_tax = __add_percent(selling_price, tax_rate);
            __write_number(tr_obj.find('input.variable_dsp_inc_tax'), selling_price_inc_tax);
        });

        //If purchase price inc tax is changed
        $(document).on('change', 'input.variable_dpp_inc_tax', function(e) {
            var tr_obj = $(this).closest('tr');

            var purchase_inc_tax = __read_number($(this));
            purchase_inc_tax = purchase_inc_tax == undefined ? 0 : purchase_inc_tax;

            var tax_rate = $('select#tax')
                .find(':selected')
                .data('rate');
            tax_rate = tax_rate == undefined ? 0 : tax_rate;

            var purchase_exc_tax = __get_principle(purchase_inc_tax, tax_rate);
            __write_number(tr_obj.find('input.variable_dpp'), purchase_exc_tax);

            var profit_percent = __read_number(tr_obj.find('input.variable_profit_percent'));
            var selling_price = __add_percent(purchase_exc_tax, profit_percent);
            __write_number(tr_obj.find('input.variable_dsp'), selling_price);

            var selling_price_inc_tax = __add_percent(selling_price, tax_rate);
            __write_number(tr_obj.find('input.variable_dsp_inc_tax'), selling_price_inc_tax);
        });

        $(document).on('change', 'input.variable_profit_percent', function(e) {
            var tax_rate = $('select#tax')
                .find(':selected')
                .data('rate');
            tax_rate = tax_rate == undefined ? 0 : tax_rate;

            var tr_obj = $(this).closest('tr');
            var profit_percent = __read_number($(this));

            var purchase_exc_tax = __read_number(tr_obj.find('input.variable_dpp'));
            purchase_exc_tax = purchase_exc_tax == undefined ? 0 : purchase_exc_tax;

            var selling_price = __add_percent(purchase_exc_tax, profit_percent);
            __write_number(tr_obj.find('input.variable_dsp'), selling_price);

            var selling_price_inc_tax = __add_percent(selling_price, tax_rate);
            __write_number(tr_obj.find('input.variable_dsp_inc_tax'), selling_price_inc_tax);
        });

        $(document).on('change', 'input.variable_dsp', function(e) {
            var tax_rate = $('select#tax')
                .find(':selected')
                .data('rate');
            tax_rate = tax_rate == undefined ? 0 : tax_rate;

            var tr_obj = $(this).closest('tr');
            var selling_price = __read_number($(this));
            var purchase_exc_tax = __read_number(tr_obj.find('input.variable_dpp'));

            var profit_percent = __get_rate(purchase_exc_tax, selling_price);
            __write_number(tr_obj.find('input.variable_profit_percent'), profit_percent);

            var selling_price_inc_tax = __add_percent(selling_price, tax_rate);
            __write_number(tr_obj.find('input.variable_dsp_inc_tax'), selling_price_inc_tax);
        });
        $(document).on('change', 'input.variable_dsp_inc_tax', function(e) {
            var tr_obj = $(this).closest('tr');
            var selling_price_inc_tax = __read_number($(this));

            var tax_rate = $('select#tax')
                .find(':selected')
                .data('rate');
            tax_rate = tax_rate == undefined ? 0 : tax_rate;

            var selling_price = __get_principle(selling_price_inc_tax, tax_rate);
            __write_number(tr_obj.find('input.variable_dsp'), selling_price);

            var purchase_exc_tax = __read_number(tr_obj.find('input.variable_dpp'));
            var profit_percent = __get_rate(purchase_exc_tax, selling_price);
            __write_number(tr_obj.find('input.variable_profit_percent'), profit_percent);
        });


        //#ks2024
        $(document).on('click',".view_list_price",function(){
            var list_price_view_button =  $(this);
            var first_number           =  list_price_view_button.data('variation_index'); 
            var second_number          =  list_price_view_button.data('value_index'); 
            var ks                     =  list_price_view_button.data('ks'); 
                       
             $(".prices_list").each(function(){ 
               if( $(this).data('variation_index') == first_number && $(this).data('value_index') == second_number && $(this).data('ks') == ks){
                    $(this).toggleClass('hide');
               }
             });      
        
        });
        $(document).on('click', '.add_variation_value_row', function(e) {
            var ks          = $(this).data('ks');
            var name        = '.variation_row_'+ks;
            var last_name   = '.row_variable_for_index:last .variation_row_index_'+ks;
            var variation_row_index = $(this)
                .closest('.variation_row')
                .find('.row_index')
                .val();
              
            var variation_value_row_index = $(this)
                .closest('table')
                .find('.row_variable_for_index:last .variation_row_index')
                .val();
           
            if (
                $(this)
                    .closest('.variation_row')
                    .find('.row_edit').length >= 1
            ) {
                var row_type = 'edit';
            } else {
                var row_type = 'add';
            }
            var main_id   = $('#unit_id').val();
            var list      = JSON.stringify($('#sub_unit_ids').val());
            var table     = $(this).closest('table'); 
            $.ajax({
                method: 'GET',
                url: '/products/get_variation_value_row',
                data: {
                    variation_row_index: variation_row_index,
                    value_index: variation_value_row_index,
                    ks: ks,
                    main_id: main_id,
                    list: list,
                    row_type: row_type,
                },
                dataType: 'html',
                success: function(result) {
                    if (result) {
                        // alert( result);
                        // if(table.find('.prices_list').data('value_index') == result.data('value_index') ){

                            table.append(result);
                        // }
                        toggle_dsp_input();
                        setTimeout(function()
                        {
                            ready();
                        },3000);
                    }
                },
            });
             
        });
        $(document).on('change', '.variation_template', function() {
            tr_obj = $(this).closest('tr');
            var ks = $(this).data("ks");
            var main_id   = $('#unit_id').val();
            var list      = JSON.stringify($('#sub_unit_ids').val());
            if ($(this).val() !== '') {
                tr_obj.find('input.variation_name').val(
                    $(this)
                        .find('option:selected')
                        .text()
                );

                var template_id = $(this).val();
                var row_index = $(this)
                    .closest('tr')
                    .find('.row_index')
                    .val();
                $.ajax({
                    method: 'POST',
                    url: '/products/get_variation_template',
                    dataType: 'html',
                    data: { template_id: template_id, row_index: row_index , ks:ks , main_id:main_id , list:list },
                    success: function(result) {
                        if (result) {
                            tr_obj
                                .find('table.variation_value_table')
                                .find('tbody')
                                .html(result);
                            toggle_dsp_input();
                            setTimeout(function()
                            {
                                ready();
                            },3000);
                        }
                    },
                });
            }
        });
        $(document).on('click', '.remove_variation_value_row', function() {
            swal({
                title: LANG.sure,
                icon: 'warning',
                buttons: true,
                dangerMode: true,
            }).then(willDelete => {
                if (willDelete) {
                    var ks               = $(this).data('ks');
                    var first_number     =  $(this).data('variation_index'); 
                    var second_number    =  $(this).data('value_index');  
                    var count = $(this)
                        .closest('table')
                        .find('.remove_variation_value_row').length;
                    if (count === 1) {
                        // if(ks == 1){
                        //     $(this)
                        //         .closest('.variation_row_1')
                        //         .remove();
                        // }else if(ks == 2){
                        //     $(this)
                        //         .closest('.variation_row_2')
                        //         .remove();
                        // }else if(ks == 3){
                        //     $(this)
                        //         .closest('.variation_row_3')
                        //         .remove();
                        // }
                        $(this).closest('.variation_row').remove();
                    } else {
                         $(".prices_list").each(function(){ 
                           if( $(this).data('variation_index') == first_number && $(this).data('value_index') == second_number && $(this).data('ks') == ks){
                                $(this).remove();
                           }
                         });      
                        $(this)
                            .closest('tr')
                            .remove();
                    }
                }
            });
        });
        //##############

        //If tax rate is changed
        $(document).on('change', 'select#tax', function() {
            if ($('select#type').val() == 'variable') {
                var tax_rate = $('select#tax')
                    .find(':selected')
                    .data('rate');
                tax_rate = tax_rate == undefined ? 0 : tax_rate;

                $('table.variation_value_table > tbody').each(function() {
                    $(this)
                        .find('tr')
                        .each(function() {
                            var purchase_exc_tax = __read_number($(this).find('input.variable_dpp'));
                            purchase_exc_tax = purchase_exc_tax == undefined ? 0 : purchase_exc_tax;

                            var purchase_inc_tax = __add_percent(purchase_exc_tax, tax_rate);
                            __write_number(
                                $(this).find('input.variable_dpp_inc_tax'),
                                purchase_inc_tax
                            );

                            var selling_price = __read_number($(this).find('input.variable_dsp'));
                            var selling_price_inc_tax = __add_percent(selling_price, tax_rate);
                            __write_number(
                                $(this).find('input.variable_dsp_inc_tax'),
                                selling_price_inc_tax
                            );
                        });
                });
            }
        });
        //End for product type Variable
        $(document).on('change', '#tax_type', function(e) {
            toggle_dsp_input();
        });
        toggle_dsp_input();

        $(document).on('change', '#expiry_period_type', function(e) {
            if ($(this).val()) {
                $('input#expiry_period').prop('disabled', false);
            } else {
                $('input#expiry_period').val('');
                $('input#expiry_period').prop('disabled', true);
            }
        });

        $(document).on('click', 'a.view-product', function(e) {
            e.preventDefault();
            $.ajax({
                url: $(this).attr('href'),
                dataType: 'html',
                success: function(result) {
                    $('#view_product_modal')
                        .html(result)
                        .modal('show');
                    
                        // dd("slow");

                    __currency_convert_recursively($('#view_product_modal'));
                },
            });
        });
        var img_fileinput_setting = {
            showUpload: false,
            showPreview: true,
            browseLabel: LANG.file_browse_label,
            removeLabel: LANG.remove,
            previewSettings: {
                image: { width: 'auto', height: 'auto', 'max-width': '100%', 'max-height': '100%' },
            },
        };
        $('#upload_image').fileinput(img_fileinput_setting);
        $('#product_brochure').fileinput(img_fileinput_setting);
        $('#upload_video').fileinput(img_fileinput_setting);
        $('#variation_images').fileinput(img_fileinput_setting);
        $('#import_products').fileinput(img_fileinput_setting);

        if ($('textarea#product_description').length > 0) {
            tinymce.init({
                selector: 'textarea#product_description',
                height:250
            });
        }
        if ($('textarea#full_description').length > 0) {
            tinymce.init({
                selector: 'textarea#full_description',
                height:250
            });
        }
});

 //***2***  Section-Quick-Add  //
//****************************//
//Quick add unit
$(document).on('submit', 'form#quick_add_unit_form', function(e) {
    e.preventDefault();
    var form = $(this);
    var data = form.serialize();

    $.ajax({
        method: 'POST',
        url: $(this).attr('action'),
        dataType: 'json',
        data: data,
        beforeSend: function(xhr) {
            __disable_submit_button(form.find('button[type="submit"]'));
        },
        success: function(result) {
            if (result.success == true) {
                var newOption = new Option(result.data.short_name, result.data.id, true, true);
                // Append it to the select
                $('#unit_id')
                    .append(newOption)
                    .trigger('change');
                $('div.view_modal').modal('hide');
                toastr.success(result.msg);
            } else {
                toastr.error(result.msg);
            }
        },
    });
});
//Quick add brand
$(document).on('submit', 'form#quick_add_brand_form', function(e) {
    e.preventDefault();
    var form = $(this);
    var data = form.serialize();

    $.ajax({
        method: 'POST',
        url: $(this).attr('action'),
        dataType: 'json',
        data: data,
        beforeSend: function(xhr) {
            __disable_submit_button(form.find('button[type="submit"]'));
        },
        success: function(result) {
            if (result.success == true) {
                var newOption = new Option(result.data.name, result.data.id, true, true);
                // Append it to the select
                $('#brand_id')
                    .append(newOption)
                    .trigger('change');
                $('div.view_modal').modal('hide');
                toastr.success(result.msg);
            } else {
                toastr.error(result.msg);
            }
        },
    });
});
//Quick add category
$(document).on('submit', 'form#category_add_form', function(e) {
    e.preventDefault();
    var form = $(this);
    var data = form.serialize();

    $.ajax({
        method: 'POST',
        url: $(this).attr('action'),
        dataType: 'json',
        data: data,
        beforeSend: function(xhr) {
            __disable_submit_button(form.find('button[type="submit"]'));
        },
        success: function(result) {
            if (result.success == true) {
                var newOption = new Option(result.data.name, result.data.id, true, true);
                // Append it to the select
             
                $('#category_id')
                    .append(newOption)
                    .trigger('change');
                $('div.view_modal').modal('hide');
                toastr.success(result.msg);
            } else {
                toastr.error(result.msg);
            }
        },
    });
});
//Quick add Sub category
$(document).on('submit', 'form#category_add_Sub_form', function(e) {
    e.preventDefault();
    var form = $(this);
    var data = form.serialize();
   

    $.ajax({
        method: 'POST',
        url: $(this).attr('action'),
        dataType: 'json',
        data: data,
        beforeSend: function(xhr) {
            __disable_submit_button(form.find('button[type="submit"]'));
        },
        success: function(result) {
            if($("#parent_id").val() == 0){
                toastr.error(LANG.PSS);
                $('button[type="submit"]').attr("disable",false);
            }else if (result.success == true) {
                var newOption = new Option(result.data.name, result.data.id, true, true);
                // Append it to the select
                
                $('#sub_category_id')
                    .append(newOption)
                    .trigger('change');
                $('div.view_modal').modal('hide');
                toastr.success(result.msg);
            } else {
                toastr.error(result.msg);
            }
        },
    });
});
$(document).on('click', 'button.apply-all', function(){
    var val = $(this).closest('.input-group').find('input').val();
    var target_class = $(this).data('target-class');
    $(this).closest('tbody').find('tr').each( function(){
        element =  $(this).find(target_class);
        element.val(val);
        element.change();
    });
});

 //***3****  Section-Functions //
//****************************//
function toggle_dsp_input() {
    var tax_type = $('#tax_type').val();
    if (tax_type == 'inclusive') {
        $('.dsp_label').each(function() {
            $(this).text(LANG.inc_tax);
        });
        /*$('#single_dsp').addClass('hide');
        $('#single_dsp_inc_tax').removeClass('hide');*/

        $('.add-product-price-table')
            .find('.variable_dsp_inc_tax')
            .each(function() {
                $(this).removeClass('hide');
            });
        $('.add-product-price-table')
            .find('.variable_dsp')
            .each(function() {
                $(this).addClass('hide');
            });
    } else if (tax_type == 'exclusive') {
        $('.dsp_label').each(function() {
            $(this).text(LANG.exc_tax);
        });
       /* $('#single_dsp').removeClass('hide');
        $('#single_dsp_inc_tax').addClass('hide');*/

        $('.add-product-price-table')
            .find('.variable_dsp_inc_tax')
            .each(function() {
                $(this).addClass('hide');
            });
        $('.add-product-price-table')
            .find('.variable_dsp')
            .each(function() {
                $(this).removeClass('hide');
            });
    }
}

function get_product_details(rowData) {
    var div = $('<div/>')
        .addClass('loading')
        .text('Loading...');

    $.ajax({
        url: '/products/' + rowData.id,
        dataType: 'html',
        success: function(data) {
            div.html(data).removeClass('loading');
        },
    });

    return div;
}

function ready(){
         
        //..... //**1**// ..... first prices
        // If purchase price exc tax is changed
        $("input#single_dpp1").each(function(){
            // ..../*1*/.. select items
            var el      = $(this);
            var parent  = el.parent().parent().parent();
            //......................
            
            //...../*2*/.. functions section
            el.on('change', function(e) {
                // *1*  select all row element
                // ...............................................
                var dp_inc   = parent.children().find('.dpp_inc_tax');
                var dp_M_exc = parent.children().find('#single_dsp1');
                var dp_M_inc = parent.children().find('#single_dsp_inc_tax1');
                var m        = parent.children().find('#profit_percent1');
                // ...............................................
                //  *2* init values
                // ...............................................
                var purchase_exc_tax = __read_number(el);
               
                purchase_exc_tax     = purchase_exc_tax == undefined ? 0 : purchase_exc_tax;
                var tax_rate         = $('select#tax').find(':selected').data('rate');
                tax_rate = tax_rate == undefined ? 0 : tax_rate;
                var purchase_inc_tax = __add_percent(purchase_exc_tax, tax_rate);
                __write_number(dp_inc, purchase_inc_tax);
                var profit_percent   = __read_number(m);
                var selling_price    = __add_percent(purchase_exc_tax, profit_percent);
                __write_number(dp_M_exc, selling_price);
                var selling_price_inc_tax = __add_percent(selling_price, tax_rate);
                __write_number(dp_M_inc, selling_price_inc_tax);
                // .........................................................
            });
        });

        //If purchase price inc tax is changed
        $("input#single_dpp_inc_tax1").each(function(){
            
            // ..../*1*/.. select items
            var el      = $(this);
            var parent  = el.parent().parent().parent();
            //......................
            
            //...../*2*/.. functions section
            el.on('change', function(e) {
                // *1*  select all row element
                // ...............................................
                var dp_inc   = parent.children().find('#single_dpp1');
                var dp_M_exc = parent.children().find('#single_dsp1');
                var dp_M_inc = parent.children().find('#single_dsp_inc_tax1');
                var m        = parent.children().find('#profit_percent1');
                // ...............................................
                //  *2* init values
                // ...............................................
                var purchase_inc_tax = __read_number(el);
                purchase_inc_tax     = purchase_inc_tax == undefined ? 0 : purchase_inc_tax;
                var tax_rate = $('select#tax').find(':selected').data('rate');
                tax_rate = tax_rate == undefined ? 0 : tax_rate;
                var purchase_exc_tax = __get_principle(purchase_inc_tax, tax_rate);
                __write_number(dp_inc, purchase_exc_tax);
                var profit_percent = __read_number(m);
                profit_percent = profit_percent == undefined ? 0 : profit_percent;
                var selling_price = __add_percent(purchase_exc_tax, profit_percent);
                __write_number(dp_M_exc, selling_price);
                var selling_price_inc_tax = __add_percent(selling_price, tax_rate);
                __write_number(dp_M_inc, selling_price_inc_tax);
            });
        });

        $("input#profit_percent1").each(function(){
            
            // ..../*1*/.. select items
            var el      = $(this);
            var parent  = el.parent().parent();
            //......................
            
            //...../*2*/.. functions section
            el.on('change', function(e) {
                // *1*  select all row element
                // ...............................................
                var dp_inc   = parent.children().find('#single_dpp1');
                var dp_M_exc = parent.children().find('#single_dsp1');
                var dp_M_inc = parent.children().find('#single_dsp_inc_tax1');
                var single_m = parent.children().find('#single_dpp_inc_tax1');
                // ...............................................
                //  *2* init values
                // ...............................................
                var tax_rate = $('select#tax').find(':selected').data('rate');
                tax_rate = tax_rate == undefined ? 0 : tax_rate;
                var purchase_inc_tax = __read_number(single_m);
                purchase_inc_tax     = purchase_inc_tax == undefined ? 0 : purchase_inc_tax;
                var purchase_exc_tax = __read_number(dp_inc);
                purchase_exc_tax     = purchase_exc_tax == undefined ? 0 : purchase_exc_tax;
                var profit_percent   = __read_number(el);
                var selling_price    = __add_percent(purchase_exc_tax, profit_percent);
                __write_number(dp_M_exc, selling_price);
                var selling_price_inc_tax = __add_percent(selling_price, tax_rate);
                __write_number(dp_M_inc, selling_price_inc_tax);
            });
        });

        $("input#single_dsp1").each(function(){
            
            // ..../*1*/.. select items
            var el      = $(this);
            var parent  = el.parent().parent().parent();
            //......................
            
            //...../*2*/.. functions section
            el.on('change', function(e) {
                // *1*  select all row element
                // ...............................................
                var dp_inc   = parent.children().find('#single_dpp1');
                var dp_M_exc = parent.children().find('#single_dsp1');
                var dp_M_inc = parent.children().find('#single_dsp_inc_tax1');
                var m        = parent.children().find('#profit_percent1');
                // ...............................................
                //  *2* init values
                // ...............................................
                var tax_rate         = $('select#tax').find(':selected').data('rate');
                tax_rate = tax_rate == undefined ? 0 : tax_rate;
                var selling_price    = __read_number(dp_M_exc);
                var purchase_exc_tax = __read_number(dp_M_exc);
                var profit_percent   = __get_rate(purchase_exc_tax, selling_price);
                __write_number(m, profit_percent);
                var selling_price_inc_tax = __add_percent(selling_price, tax_rate);
                __write_number(dp_M_inc, selling_price_inc_tax);
            });
        });

        $("input#single_dsp_inc_tax1").each(function(){
            
            // ..../*1*/.. select items
            var el      = $(this);
            var parent  = el.parent().parent().parent();
            //......................
            
            //...../*2*/.. functions section
            el.on('change', function(e) {
                 // *1*  select all row element
                // ...............................................
                var dp_inc   = parent.children().find('#single_dpp1');
                var dp_M_exc = parent.children().find('#single_dsp1');
                var dp_M_inc = parent.children().find('#single_dsp_inc_tax1');
                var m        = parent.children().find('#profit_percent1');
                // ...............................................
                //  *2* init values
                // ...............................................
                var tax_rate = $('select#tax').find(':selected').data('rate');
                tax_rate = tax_rate == undefined ? 0 : tax_rate;
                var selling_price_inc_tax = __read_number(dp_M_inc);
                var selling_price = __get_principle(selling_price_inc_tax, tax_rate);
                __write_number(dp_M_exc, selling_price);
                var purchase_exc_tax = __read_number(dp_inc);
                var profit_percent = __get_rate(purchase_exc_tax, selling_price);
                __write_number(m, profit_percent);
            });
        });
        // ........................................................................

        //..... //**2**// ..... second prices
        // If purchase price exc tax is changed
        $("input#single_dpp2").each(function(){
            
            // ..../*1*/.. select items
            var el      = $(this);
            var parent  = el.parent().parent().parent();
            //......................
            
            //...../*2*/.. functions section
            el.on('change', function(e) {
                // *1*  select all row element
                // ...............................................
                var dp_inc   = parent.children().find('.dpp_inc_tax');
                var dp_M_exc = parent.children().find('#single_dsp2');
                var dp_M_inc = parent.children().find('#single_dsp_inc_tax2');
                var m        = parent.children().find('#profit_percent2');
                // ...............................................
                //  *2* init values
                // ...............................................
                var purchase_exc_tax = __read_number(el);
                purchase_exc_tax     = purchase_exc_tax == undefined ? 0 : purchase_exc_tax;
                var tax_rate         = $('select#tax').find(':selected').data('rate');
                tax_rate = tax_rate == undefined ? 0 : tax_rate;
                var purchase_inc_tax = __add_percent(purchase_exc_tax, tax_rate);
                __write_number(dp_inc, purchase_inc_tax);
                var profit_percent   = __read_number(m);
                var selling_price    = __add_percent(purchase_exc_tax, profit_percent);
                __write_number(dp_M_exc, selling_price);
                var selling_price_inc_tax = __add_percent(selling_price, tax_rate);
                __write_number(dp_M_inc, selling_price_inc_tax);
                // .........................................................
            });
        });

        //If purchase price inc tax is changed
        $("input#single_dpp_inc_tax2").each(function(){
            
            // ..../*1*/.. select items
            var el      = $(this);
            var parent  = el.parent().parent().parent();
            //......................
            
            //...../*2*/.. functions section
            el.on('change', function(e) {
                // *1*  select all row element
                // ...............................................
                var dp_inc   = parent.children().find('#single_dpp2');
                var dp_M_exc = parent.children().find('#single_dsp2');
                var dp_M_inc = parent.children().find('#single_dsp_inc_tax2');
                var m        = parent.children().find('#profit_percent2');
                // ...............................................
                //  *2* init values
                // ...............................................
                var purchase_inc_tax = __read_number(el);
                purchase_inc_tax     = purchase_inc_tax == undefined ? 0 : purchase_inc_tax;
                var tax_rate = $('select#tax').find(':selected').data('rate');
                tax_rate = tax_rate == undefined ? 0 : tax_rate;
                var purchase_exc_tax = __get_principle(purchase_inc_tax, tax_rate);
                __write_number(dp_inc, purchase_exc_tax);
                var profit_percent = __read_number(m);
                profit_percent = profit_percent == undefined ? 0 : profit_percent;
                var selling_price = __add_percent(purchase_exc_tax, profit_percent);
                __write_number(dp_M_exc, selling_price);
                var selling_price_inc_tax = __add_percent(selling_price, tax_rate);
                __write_number(dp_M_inc, selling_price_inc_tax);
            });
        });

        $("input#profit_percent2").each(function(){
            
            // ..../*1*/.. select items
            var el      = $(this);
            var parent  = el.parent().parent();
            //......................
            
            //...../*2*/.. functions section
            el.on('change', function(e) {
                // *1*  select all row element
                // ...............................................
                var dp_inc   = parent.children().find('#single_dpp2');
                var dp_M_exc = parent.children().find('#single_dsp2');
                var dp_M_inc = parent.children().find('#single_dsp_inc_tax2');
                var single_m = parent.children().find('#single_dpp_inc_tax2');
                // ...............................................
                //  *2* init values
                // ...............................................
                var tax_rate = $('select#tax').find(':selected').data('rate');
                tax_rate = tax_rate == undefined ? 0 : tax_rate;
                var purchase_inc_tax = __read_number(single_m);
                purchase_inc_tax     = purchase_inc_tax == undefined ? 0 : purchase_inc_tax;
                var purchase_exc_tax = __read_number(dp_inc);
                purchase_exc_tax     = purchase_exc_tax == undefined ? 0 : purchase_exc_tax;
                var profit_percent   = __read_number(el);
                var selling_price    = __add_percent(purchase_exc_tax, profit_percent);
                __write_number(dp_M_exc, selling_price);
                var selling_price_inc_tax = __add_percent(selling_price, tax_rate);
                __write_number(dp_M_inc, selling_price_inc_tax);
            });
        });

        $("input#single_dsp2").each(function(){
            
            // ..../*1*/.. select items
            var el      = $(this);
            var parent  = el.parent().parent().parent();
            //......................
            
            //...../*2*/.. functions section
            el.on('change', function(e) {
                // *1*  select all row element
                // ...............................................
                var dp_inc   = parent.children().find('#single_dpp2');
                var dp_M_exc = parent.children().find('#single_dsp2');
                var dp_M_inc = parent.children().find('#single_dsp_inc_tax2');
                var m        = parent.children().find('#profit_percent2');
                // ...............................................
                //  *2* init values
                // ...............................................
                var tax_rate         = $('select#tax').find(':selected').data('rate');
                tax_rate = tax_rate == undefined ? 0 : tax_rate;
                var selling_price    = __read_number(dp_M_exc);
                var purchase_exc_tax = __read_number(dp_M_exc);
                var profit_percent   = __get_rate(purchase_exc_tax, selling_price);
                __write_number(m, profit_percent);
                var selling_price_inc_tax = __add_percent(selling_price, tax_rate);
                __write_number(dp_M_inc, selling_price_inc_tax);
            });
        });

        $("input#single_dsp_inc_tax2").each(function(){
            
            // ..../*1*/.. select items
            var el      = $(this);
            var parent  = el.parent().parent().parent();
            //......................
            
            //...../*2*/.. functions section
            el.on('change', function(e) {
                 // *1*  select all row element
                // ...............................................
                var dp_inc   = parent.children().find('#single_dpp2');
                var dp_M_exc = parent.children().find('#single_dsp2');
                var dp_M_inc = parent.children().find('#single_dsp_inc_tax2');
                var m        = parent.children().find('#profit_percent2');
                // ...............................................
                //  *2* init values
                // ...............................................
                var tax_rate = $('select#tax').find(':selected').data('rate');
                tax_rate = tax_rate == undefined ? 0 : tax_rate;
                var selling_price_inc_tax = __read_number(dp_M_inc);
                var selling_price = __get_principle(selling_price_inc_tax, tax_rate);
                __write_number(dp_M_exc, selling_price);
                var purchase_exc_tax = __read_number(dp_inc);
                var profit_percent = __get_rate(purchase_exc_tax, selling_price);
                __write_number(m, profit_percent);
            });
        });
        // ...............................................................
   
   
   
        //..... //**3**// ..... third prices
        // If purchase price exc tax is changed
        $("input#single_dpp3").each(function(){
            
            // ..../*1*/.. select items
            var el      = $(this);
            var parent  = el.parent().parent().parent();
            //......................
            
            //...../*2*/.. functions section
            el.on('change', function(e) {
                // *1*  select all row element
                // ...............................................
                var dp_inc   = parent.children().find('.dpp_inc_tax');
                var dp_M_exc = parent.children().find('#single_dsp3');
                var dp_M_inc = parent.children().find('#single_dsp_inc_tax3');
                var m        = parent.children().find('#profit_percent3');
                // ...............................................
                //  *2* init values
                // ...............................................
                var purchase_exc_tax = __read_number(el);
                purchase_exc_tax     = purchase_exc_tax == undefined ? 0 : purchase_exc_tax;
                var tax_rate         = $('select#tax').find(':selected').data('rate');
                tax_rate = tax_rate == undefined ? 0 : tax_rate;
                var purchase_inc_tax = __add_percent(purchase_exc_tax, tax_rate);
                __write_number(dp_inc, purchase_inc_tax);
                var profit_percent   = __read_number(m);
                var selling_price    = __add_percent(purchase_exc_tax, profit_percent);
                __write_number(dp_M_exc, selling_price);
                var selling_price_inc_tax = __add_percent(selling_price, tax_rate);
                __write_number(dp_M_inc, selling_price_inc_tax);
                // .........................................................
            });
        });

        //If purchase price inc tax is changed
        $("input#single_dpp_inc_tax3").each(function(){
            
            // ..../*1*/.. select items
            var el      = $(this);
            var parent  = el.parent().parent().parent();
            //......................
            
            //...../*2*/.. functions section
            el.on('change', function(e) {
                // *1*  select all row element
                // ...............................................
                var dp_inc   = parent.children().find('#single_dpp3');
                var dp_M_exc = parent.children().find('#single_dsp3');
                var dp_M_inc = parent.children().find('#single_dsp_inc_tax3');
                var m        = parent.children().find('#profit_percent3');
                // ...............................................
                //  *2* init values
                // ...............................................
                var purchase_inc_tax = __read_number(el);
                purchase_inc_tax     = purchase_inc_tax == undefined ? 0 : purchase_inc_tax;
                var tax_rate = $('select#tax').find(':selected').data('rate');
                tax_rate = tax_rate == undefined ? 0 : tax_rate;
                var purchase_exc_tax = __get_principle(purchase_inc_tax, tax_rate);
                __write_number(dp_inc, purchase_exc_tax);
                var profit_percent = __read_number(m);
                profit_percent = profit_percent == undefined ? 0 : profit_percent;
                var selling_price = __add_percent(purchase_exc_tax, profit_percent);
                __write_number(dp_M_exc, selling_price);
                var selling_price_inc_tax = __add_percent(selling_price, tax_rate);
                __write_number(dp_M_inc, selling_price_inc_tax);
            });
        });

        $("input#profit_percent3").each(function(){
            
            // ..../*1*/.. select items
            var el      = $(this);
            var parent  = el.parent().parent();
            //......................
            
            //...../*2*/.. functions section
            el.on('change', function(e) {
                // *1*  select all row element
                // ...............................................
                var dp_inc   = parent.children().find('#single_dpp3');
                var dp_M_exc = parent.children().find('#single_dsp3');
                var dp_M_inc = parent.children().find('#single_dsp_inc_tax3');
                var single_m = parent.children().find('#single_dpp_inc_tax3');
                // ...............................................
                //  *2* init values
                // ...............................................
                var tax_rate = $('select#tax').find(':selected').data('rate');
                tax_rate = tax_rate == undefined ? 0 : tax_rate;
                var purchase_inc_tax = __read_number(single_m);
                purchase_inc_tax     = purchase_inc_tax == undefined ? 0 : purchase_inc_tax;
                var purchase_exc_tax = __read_number(dp_inc);
                purchase_exc_tax     = purchase_exc_tax == undefined ? 0 : purchase_exc_tax;
                var profit_percent   = __read_number(el);
                var selling_price    = __add_percent(purchase_exc_tax, profit_percent);
                __write_number(dp_M_exc, selling_price);
                var selling_price_inc_tax = __add_percent(selling_price, tax_rate);
                __write_number(dp_M_inc, selling_price_inc_tax);
            });
        });

        $("input#single_dsp3").each(function(){
            
            // ..../*1*/.. select items
            var el      = $(this);
            var parent  = el.parent().parent().parent();
            //......................
            
            //...../*2*/.. functions section
            el.on('change', function(e) {
                // *1*  select all row element
                // ...............................................
                var dp_inc   = parent.children().find('#single_dpp3');
                var dp_M_exc = parent.children().find('#single_dsp3');
                var dp_M_inc = parent.children().find('#single_dsp_inc_tax3');
                var m        = parent.children().find('#profit_percent3');
                // ...............................................
                //  *2* init values
                // ...............................................
                var tax_rate         = $('select#tax').find(':selected').data('rate');
                tax_rate = tax_rate == undefined ? 0 : tax_rate;
                var selling_price    = __read_number(dp_M_exc);
                var purchase_exc_tax = __read_number(dp_M_exc);
                var profit_percent   = __get_rate(purchase_exc_tax, selling_price);
                __write_number(m, profit_percent);
                var selling_price_inc_tax = __add_percent(selling_price, tax_rate);
                __write_number(dp_M_inc, selling_price_inc_tax);
            });
        });

        $("input#single_dsp_inc_tax3").each(function(){
            
            // ..../*1*/.. select items
            var el      = $(this);
            var parent  = el.parent().parent().parent();
            //......................
            
            //...../*2*/.. functions section
            el.on('change', function(e) {
                 // *1*  select all row element
                // ...............................................
                var dp_inc   = parent.children().find('#single_dpp3');
                var dp_M_exc = parent.children().find('#single_dsp3');
                var dp_M_inc = parent.children().find('#single_dsp_inc_tax3');
                var m        = parent.children().find('#profit_percent3');
                // ...............................................
                //  *2* init values
                // ...............................................
                var tax_rate = $('select#tax').find(':selected').data('rate');
                tax_rate = tax_rate == undefined ? 0 : tax_rate;
                var selling_price_inc_tax = __read_number(dp_M_inc);
                var selling_price = __get_principle(selling_price_inc_tax, tax_rate);
                __write_number(dp_M_exc, selling_price);
                var purchase_exc_tax = __read_number(dp_inc);
                var profit_percent = __get_rate(purchase_exc_tax, selling_price);
                __write_number(m, profit_percent);
            });
        });
        // ..................................................................
}
