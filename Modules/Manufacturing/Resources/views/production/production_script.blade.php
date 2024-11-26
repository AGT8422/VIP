<script type="text/javascript">
	$(document).ready( function() {
        calculateRecipeTotal();
        jQuery.validator.addMethod("notEmpty", function(value, element, param) {
            return __number_uf(value) > 0
        }, "{{__('manufacturing::lang.quantity_greater_than_zero')}}");

        jQuery.validator.addMethod("notEqualToWastedQuantity", function(value, element, param) {
            var waste_qty = __read_number($('#mfg_wasted_units'));
            var qty = __number_uf(value);
            return qty > waste_qty;
        }, "{{__('manufacturing::lang.waste_qty_less_than_qty')}}");

		$('#transaction_date').datetimepicker({
	        format: moment_date_format + ' ' + moment_time_format,
	        ignoreReadonly: true,
	    });

	    $('#exp_date').datepicker({
            autoclose: true,
            format: datepicker_date_format,
        });

	    production_form_validator = $('#production_form').validate();
	});

	$(document).on('change', '#production_form #variation_id, #production_form #location_id,  #production_form #store_id', function () {
		var variation_id = $("#variation_id").val();
		var location_id  = $("#location_id").val();
		var old_store    = $("#store_hidden").val();
		var type         = $("#type").val();
		var store_id     = $('#store_id option:selected').val();
        
		if(variation_id && location_id) {
			$.ajax({
	            url: "/manufacturing/get-recipe-details?variation_id=" + variation_id + "&location_id=" + location_id+'&store_id='+store_id+'&old_store='+old_store+'&type='+type,
	            dataType: 'json',
	            success: function(result) {
	                $('#enter_ingredients_table').html(result.ingredient_table);
	                if (result.is_sub_unit) {
	                	$('#recipe_quantity_input').removeClass('input-group');
	                	$('#recipe_quantity_input').addClass('input_inline');
	                	$('#unit_html').removeClass('input-group-addon');
	                } else {
	                	$('#recipe_quantity_input').addClass('input-group');
	                	$('#recipe_quantity_input').removeClass('input_inline');
	                	$('#unit_html').addClass('input-group-addon');
	                }
	                __write_number($('#recipe_quantity'), result.recipe.total_quantity);
	                $('#unit_html').html(result.unit_html);
	                $('#wasted_units_text').text(result.unit_name);
                    calculateRecipeTotal()
                    onChange() 
                    var mfg_wasted_units = __calculate_amount('percentage', $('#waste_percent').val(), result.recipe.total_quantity);
                    __write_number($('#mfg_wasted_units'), mfg_wasted_units); 
                    __write_number($('#production_cost'), result.recipe.extra_cost);
                    calculateRecipeTotal();
                    open_row();
	                // __currency_convert_recursively($('#enter_ingredients_table'));
                    
	            },
	        });
 		} else {
			$('#enter_ingredients_table').html('');
	        calculateRecipeTotal();
            open_row();
		}
	});
	$(document).on('change', '#list_price', function(){
        
        calculateRecipeTotal();
    });
	
	$(document).on('change', '#production_cost, input.mfg_waste_percent', function(){
		
        calculateRecipeTotal();
        
	});

    $(document).on('change', '#recipe_quantity, #sub_unit_id', function(){
        var recipe_quantity = __read_number($('#recipe_quantity'));

        var mfg_wasted_units = __calculate_amount('percentage', $('#waste_percent').val(), recipe_quantity);
        __write_number($('#mfg_wasted_units'), mfg_wasted_units);

        var multiplier = 1;
        if ($('#sub_unit_id').length) {
            multiplier = parseFloat(
            $('#sub_unit_id')
                .find(':selected')
                .data('multiplier')
            );
            recipe_quantity = recipe_quantity * multiplier; 
        }
        $('#ingredients_for_unit_recipe_table tbody tr').each( function() {
            var line_unit_quantity = parseFloat($(this).find('.unit_quantity').val());
            var line_multiplier = __getUnitMultiplier($(this));
            var line_total_quantity = (recipe_quantity * line_unit_quantity) / line_multiplier;
            __write_number($(this).find('.total_quantities'), line_total_quantity);
            $(this).find('.total_quantities').change();
        });
    });

	$(document).on('change', '#sub_unit_id', function(){
		var unit_name = $(this)
                .find(':selected')
                .data('unit_name');
            $('#wasted_units_text').text(unit_name);
	});

    function open_row(){
        $("#ingredients_for_unit_recipe_table tbody .total_quantities").each(function() {
            var el = $(this); 
            var i  = $(this).parent().parent().parent(); 
            el.on("change",function(){
            
                var e = i.find(".name").data("name") ;
                change_max(e);
                el.attr("data-rule-max-value");
            });
        });
    }
        

	$(document).on('change', '.total_quantities', function(){
		if (production_form_validator) {
    		production_form_validator.element($(this));
		}
		calculateRecipeTotal();
	});

	function calculateRecipeTotal(id=null,line_price=null) {
		var recipe_quantity = __read_number($('#recipe_quantity'));

        var multiplier = 1;

        if ($('#sub_unit_id').length) {
            multiplier = parseFloat(
            $('#sub_unit_id')
                .find(':selected')
                .data('multiplier')
            );
            recipe_quantity = recipe_quantity * multiplier; 
        }

        var total_ingredients_cost = 0;

        $('#ingredients_for_unit_recipe_table tbody tr').each( function() {
            if ($(this).find('.ingredient_price').length > 0) {
                var id_int               = parseFloat($(this).find('.id_int').val());
                var line_unit_price      = parseFloat($(this).find('.ingredient_price').val());
                var line_unit_quantity   = parseFloat($(this).find('.unit_quantity').val());
                var line_unit_total      = line_unit_price * line_unit_quantity;
                var line_total_quantity  = __read_number($(this).find('.total_quantities'));
                var line_multiplier      = __getUnitMultiplier($(this));
                var line_waste_percent   = __read_number($(this).find('.mfg_waste_percent'));
                var global_prices        = $('#list_price');
                var line_final_quantity  = __substract_percent(line_total_quantity, line_waste_percent);

                var line_total           = line_unit_price * line_total_quantity * line_multiplier;
                
                if(id!=null){
                    if(id == id_int){
                        if($(this).find('.list_price').find(":selected").val() == ""){
                            $(this).find('.list_price').children().each(function(){
                                if($(this).data('value') == global_prices.find(':selected').val()){
                                    line_total = parseFloat($(this).data('price')) * line_total_quantity;
                                }
                            });
                            $(this).find('span.ingredient_total_price').text(__currency_trans_from_en(line_total, true));
                        }else{    
                            line_total = parseFloat(line_price) * line_total_quantity;
                            $(this).find('span.ingredient_total_price').text(__currency_trans_from_en(line_total, true));
                        }
                       
                    }else{
                        if($(this).find('.list_price').find(":selected").val() == ""){
                            $(this).find('.list_price').children().each(function(){
                                if($(this).data('value') == global_prices.find(':selected').val()){
                                    line_total = parseFloat($(this).data('price')) * line_total_quantity;
                                }
                            });
                            $(this).find('span.ingredient_total_price').text(__currency_trans_from_en(line_total, true));
                        }else{
                           
                            line_total = parseFloat($('#list_price').find(":selected").data("price")) * line_total_quantity;
                        } 
                    }
                }else{
                    
                    if($(this).find('.list_price').find(":selected").val() == ""){
                        $(this).find('.list_price').children().each(function(){
                            if($(this).data('value') == global_prices.find(':selected').val()){
                                line_total = parseFloat($(this).data('price')) * line_total_quantity;
                            }
                        });
                        $(this).find('span.ingredient_total_price').text(__currency_trans_from_en(line_total, true));
                    }else{

                        line_total = parseFloat($(this).find('.list_price').find(":selected").data("price")) * line_total_quantity;
                    } 
                }
                
                $(this).find('span.row_final_quantity').text(__currency_trans_from_en(line_final_quantity, false));

                var line_unit_name = '';

                if ($(this).find('.sub_unit').length) {
                    line_unit_name = $(this).find('.sub_unit')
                    .find('option:selected')
                    .text();
                } else {
                    line_unit_name = $(this).find('.line_unit_span').text();
                }
                $(this).find('.row_unit_text').text(line_unit_name);

                total_ingredients_cost += line_total;
            }
        });

        $('#total_ingredient_price').text(__currency_trans_from_en(total_ingredients_cost, true));
        $('#total_ingredient_price_').val(total_ingredients_cost);


        var production_cost_percent = __read_number($('#production_cost'));

        var production_cost = __calculate_amount('percentage', production_cost_percent, total_ingredients_cost);


        $('span#total_production_cost').text(__currency_trans_from_en(production_cost, true));
        $('span#total_production_cost_').val(production_cost);


        var final_price = total_ingredients_cost + production_cost;


        __write_number($('#final_before'), total_ingredients_cost);
        __write_number($('#final_total'), final_price);
        $('span#final_total_text').text(__currency_trans_from_en(final_price, true));
        __write_number($('#total'), total_ingredients_cost);

       
        update();
    }

    function update(){
       console.log($("#production_cost").val());
    
    }
    function change_max(e)
    {
            var total_qty = 0;
            $("#ingredients_for_unit_recipe_table tbody .total_quantities").each(function(){
                var  els    = $(this);
                var  el    = $(this).val();
                var   i    = $(this).parent().parent().parent();
                if(i.find(".name").data("name") == e){
                    total_qty += parseFloat(el) ; 
                    var max    = els.attr("data-rule-max-value");
                    console.log(max  +  " _+_ "  + total_qty);
                    if(total_qty > max){
                        $("#save_submit").addClass("hide");
                    }else{
                        $("#save_submit").removeClass("hide");
                    }
                    
                }
            });
    }
    // #2024-8-6
    $(document).on('change', 'select.sub_unit', function() {
        var tr = $(this).closest('tr');
        var selected_option = $(this).find(':selected');
        var multiplier      = parseFloat(selected_option.data('multiplier'));
        var allow_decimal   = parseInt(selected_option.data('allow_decimal'));

        var qty_element    = tr.find('input.total_quantities');
        var base_max_avlbl = qty_element.data('qty_available');
        var error_msg_line = 'pos_max_qty_error';

        qty_element.attr('data-decimal', allow_decimal);
        var abs_digit = true;
        if (allow_decimal) {
            abs_digit = false;
        }
        qty_element.rules('add', {
            abs_digit: abs_digit,
        });

        if (base_max_avlbl) {
            var max_avlbl = parseFloat(base_max_avlbl) / multiplier;
            var formated_max_avlbl = __number_f(max_avlbl);
            var unit_name = selected_option.data('unit_name');
            var max_err_msg = __translate(error_msg_line, {
                max_val: formated_max_avlbl,
                unit_name: unit_name,
            });
            qty_element.attr('data-rule-max-value', max_avlbl);
            qty_element.attr('data-msg-max-value', max_err_msg);
            qty_element.rules('add', {
                'max-value': max_avlbl,
                messages: {
                    'max-value': max_err_msg,
                },
            });
            qty_element.trigger('change');
        }
    });
    function onChange(){
        $('.sub_unit').each(function(){
            var item = $(this);
            onChangeFunction(item); 
        });
        $('.list_price').each(function(){
            var item = $(this);
            onListChangeFunction(item); 
        });
    }
    function onChangeFunction(item){
         item.on('change',function(){ 
            var value_selected = $(this).find(':selected').val();
            var tr             = $(this).closest('tr');
            var prices         = tr.find('.list_price');
            var global_prices  = $('#list_price');
            html               = "";
            global_html        = "";
            list_price         = JSON.stringify(tr.find('.list_price').data("prices"));
            list_of            = JSON.parse(list_price);
            for(i in list_of){ 
                if(i == value_selected){
                    html += '<option value="" data-price="null" selected>None</option>';
                    counter = 0;
                    for(e in list_of[i]){
                        html += '<option value="'+list_of[i][e].line_id+'" data-price="'+list_of[i][e].price+'"  data-value="'+list_of[i][e].line_id+'" >'+list_of[i][e].name+'</option>';
                        View_selected = (counter == 0)?"selected":"";
                        global_html += '<option value="'+list_of[i][e].line_id+'" '+View_selected+' data-price="'+list_of[i][e].price+'"  data-value="'+list_of[i][e].line_id+'" >'+list_of[i][e].name+'</option>';
                        counter++;
                    }
                }
            }
            if(html != ""){
                prices.html(html);
                global_prices.html(global_html);
            } 
            calculateRecipeTotal();
         } );
    }
    function onListChangeFunction(item){
         item.on('change',function(){ 
            var global_prices  = $('#list_price');
            var tr             = $(this).closest('tr');
            var id             = tr.find('.id_int').val();
            var prices         = tr.find('.list_price');
            var value_selected = prices.find(':selected').val();
            var value_price    = prices.find(':selected').data('price');
            calculateRecipeTotal(id,value_price)
         } );
    }
    onChange();
</script>