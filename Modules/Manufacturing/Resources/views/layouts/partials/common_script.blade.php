<script type="text/javascript">
	$(document).ready( function () {

		//Purchase table
	    productions_table = $('#productions_table').DataTable({
	        processing: true,
	        serverSide: true,
	        aaSorting: [[0, 'desc']],
	        ajax: {
	            url: '{{action("\Modules\Manufacturing\Http\Controllers\ProductionController@index")}}',
	            data: function(d) {
	                
	            },
	        },
	        columnDefs: [
	            {
	                targets: [6],
	                orderable: false,
	                searchable: false,
	            },
	        ],
	        columns: [
	            { data: 'transaction_date', name: 'transaction_date' },
	            { data: 'ref_no', name: 'ref_no' },
	            { data: 'location_name', name: 'bl.name' },
	            { data: 'product_name', name: 'product_name' },
	            { data: 'quantity', searchable: false },
	            { data: 'final_total', name: 'final_total' },
	            { data: 'action', name: 'action' },
	        ],
	        fnDrawCallback: function(oSettings) {
	            __currency_convert_recursively($('#productions_table'));
	        }
	    });

	    if ($('textarea#instructions').length > 0) {
            tinymce.init({
                selector: 'textarea#instructions',
            });
	    }

		if ($('#search_product').length) {
			initialize_search($('#search_product'));
	    }
	    if ($('.search_product').length) {
	    	$('.search_product').each( function(){
	    		initialize_search($(this));
	    	});
	    }

    	recipe_table = $('#recipe_table').DataTable({
	        processing: true,
	        serverSide: true,
	        ajax: '{{action("\Modules\Manufacturing\Http\Controllers\RecipeController@index")}}',
	        columnDefs: [
	            {
	                targets: [0, 5, 6, 7],
	                orderable: false,
	                searchable: false,
	            },
	        ],
	        "order": [[ 1, "desc" ]],
	        columns: [
	        	{ data: 'row_select' },
	            { data: 'recipe_name', name: 'recipe_name' },
	            { data: 'category', name: 'c.name' },
	            { data: 'sub_category', name: 'sc.name' },
	            { data: 'total_quantity', name: 'total_quantity' },
	            { data: 'recipe_total',class:"hide" },
	            { data: 'unit_cost' ,class:"hide"},
	            { data: 'action', name: 'action' },
	        ],
	        fnDrawCallback: function(oSettings) {
	            __currency_convert_recursively($('#recipe_table'));
	        },
	    });
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
    function change_max(e){
        var total_qty = 0;
        
        $("#ingredients_for_unit_recipe_table tbody .total_quantities").each(function(){
            var  els    = $(this);
            var  el    = $(this).val();
            var   i    = $(this).parent().parent().parent();
            if(i.find(".name").data("name") == e){
                total_qty += parseFloat(el) ; 
                console.log(max  +  " _+_ "  + total_qty);
            }
            var max    = els.attr("data-rule-max-value");
        });
    }
	$(document).on('shown.bs.modal', '#recipe_modal', function(){
		initSelect2($(this).find('#variation_id'), $('#recipe_modal'));
        $(this).find('#copy_recipe_id').select2();
	});

	$(document).on('shown.bs.modal', '.view_modal', function(){
		__currency_convert_recursively($('.view_modal'));
	});

	$(document).on('change', '.quantity, .row_sub_unit_id, #total_quantity, #extra_cost, #sub_unit_id', function(){
		calculateRecipeTotal();
	});

    function addIngredientRow(variation_id, search_element) {
    	var row_index = parseInt($('#row_index').val());
    	var ingredient_group = search_element.closest('.box').find('.ingredient_group');
        var row_ig_index = ingredient_group.length ? ingredient_group.data('ig_index') : '';
        var sort_order = ++(search_element.closest('.box').find('.ingredient-row-sortable').children().length);

    	$.ajax({
            url: "/manufacturing/get-ingredient-row/" + variation_id + '?row_index=' + row_index + '&row_ig_index=' + row_ig_index + '&sort_order=' + sort_order+"&item=row_",
            dataType: 'html',
            success: function(result) {
                search_element.closest('.box').find('table.ingredients_table tbody').append(result);
                calculateRecipeTotal();
           
                row_index++;
                $('#row_index').val(row_index);
            },
        });
    }

    function calculateRecipeTotal() {
    	var total = 0;
         
    	$('.ingredients_table tbody tr').each( function() {
    		var line_unit_price = $(this).find('.ingredient_price').val();
    		var quantity = __read_number($(this).find('.quantity'));
    		var multiplier = 1;
    		if ($(this).find('.row_sub_unit_id').length) {
    			multiplier = parseFloat(
		            $(this).find('.row_sub_unit_id')
		                .find(':selected')
		                .data('multiplier')
		        	);
    		}

    		var line_total = line_unit_price * quantity * multiplier;
    		$(this).find('span.ingredient_price').text(__currency_trans_from_en(line_total, true));
    		total += line_total;
    	});
    	$('span#ingredients_cost_text').text(__currency_trans_from_en(total, true));
    	$('#ingredients_cost').val(total);
    	var production_cost_percent = __read_number($('#extra_cost'));
    	var production_cost = __calculate_amount('percentage', production_cost_percent, total);
		total += production_cost;
    	__write_number($('#total'), total);
    }

	function initSelect2(element, dropdownParent = $('body')) {
		element.select2({
	        ajax: {
	            url: '/products/list',
	            dataType: 'json',
	            delay: 250,
	            data: function(params) {
	                return {
	                    term: params.term, // search term
	                };
	            },
	            processResults: function(data) {
	            	return {
			            results: $.map(data, function (value, key) {
			            	var name = value.type == 'variable' ? value.name + ' - ' + value.variation : value.name;
			            	name += ' (' + value.sub_sku + ')';
			                return {
			                    id: value.variation_id,
			                    text: name
			                }
			            })
			        };
	            },
	        },
	        minimumInputLength: 1,
	        escapeMarkup: function(markup) {
	            return markup;
	        },
	        dropdownParent: dropdownParent
	    });
	}

	$(document).on('click', 'button.remove_ingredient', function() {
        
        element = $(this).closest('tbody.ingredient-row-sortable');

		$(this).closest('tr').remove();

        //set the order of ingredient
        $(element).children().each(function(index) {
            $(this).find('input.sort_order').val(++index)
        });

		calculateRecipeTotal();
	});
    
	$(document).on('submit', '#recipe_form', function (e) {
		var ingredients_length = $('.ingredients_table tbody .quantity').length;
		if (ingredients_length < 1) {
			toastr.error('@lang("manufacturing::lang.please_add_ingredients")');
			e.preventDefault();
			return false;
		}
	});

	$(document).on('click', 'button#add_ingredient_group', function() {
		var ig_index = parseInt($('#ig_index').val());
    	$.ajax({
            url: "/manufacturing/ingredient-group-form" + '?ig_index=' + ig_index,
            dataType: 'html',
            success: function(result) {
            	var el = $(result);
                $('#box_group').append(el);
                initialize_search(el.find('.search_product'));
                el.find('.ingredient_group').focus();
                ig_index++;
                $('#ig_index').val(ig_index);
            },
        });
	});

	function initialize_search(element) {
		element.autocomplete({
            source: function(request, response) {
                $.getJSON(
                    '/products/list',
                    {
                        term: request.term,
                        product_types: ['single', 'variable']
                    },
                    response
                );
            },
            minLength: 2,
            response: function(event, ui) {
                if (ui.content.length == 0) {
                    toastr.error(LANG.no_products_found);
                    $('input#search_product').select();
                }
            },
            select: function(event, ui) {
                addIngredientRow(ui.item.variation_id, $(this));
            },
        }).autocomplete('instance')._renderItem = function(ul, item) {
	        var string = '<li>' + item.name;
            if (item.type == 'variable') {
                string += '-' + item.variation;
            }
            string +=
                ' (' +
                item.sub_sku +
                ')' +
                '</li>';
            return $(string).appendTo(ul);
        }
	}
	$(document).on('click', 'button.remove_ingredient_group', function() {
	$(this).closest('.box').remove();
	calculateRecipeTotal();
});

$(document).on('click', '#mass_update_product_price', function(e){
    e.preventDefault();
    var selected_rows = [];
    var unit_prices = [];
    var i = 0;
    $('.row-select:checked').each(function () {
    	var recipe_id = $(this).val();
        selected_rows[i++] = recipe_id;
        unit_prices[recipe_id] = $(this).closest('tr').find('span.unit_cost').data('unit_cost');
    });
    
    if(selected_rows.length > 0){
        swal({
            title: LANG.sure,
            icon: "warning",
            buttons: true,
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                var data = {
                	recipe_ids: selected_rows,
                	unit_prices: unit_prices
                }
                $.ajax({
                    method: "post",
                    url: "/manufacturing/update-product-prices",
                    dataType: 'json',
                    data: data,
                    success: function(result) {
                        if (result.success == true) {
                            toastr.success(result.msg);
                            recipe_table.ajax.reload();
                        } else {
                            toastr.error(result.msg);
                        }
                    },
                });
            }
        });
    } else{
        swal('@lang("lang_v1.no_row_selected")');
    }    
});
$(document).on('click', 'button.delete_recipe', function() {
    swal({
        title: LANG.sure,
        icon: 'warning',
        buttons: true,
        dangerMode: true,
    }).then(willDelete => {
        if (willDelete) {
            var href = $(this).data('href');
            var data = $(this).serialize();
            $.ajax({
                method: 'DELETE',
                url: href,
                dataType: 'json',
                data: data,
                success: function(result) {
                    if (result.success == true) {
                        toastr.success(result.msg);
                        recipe_table.ajax.reload();
                    } else {
                        toastr.error(result.msg);
                    }
                },
            });
        }
    });
});

$(document).on('click', '.delete-production', function(e) {
	e.preventDefault();
    swal({
        title: LANG.sure,
        icon: 'warning',
        buttons: true,
        dangerMode: true,
    }).then(willDelete => {
        if (willDelete) {
            var href = $(this).data('href');
            var data = $(this).serialize();
            $.ajax({
                method: 'DELETE',
                url: href,
                dataType: 'json',
                data: data,
                success: function(result) {
                     if (result.success == true) {
                        toastr.success(result.msg);
                        productions_table.ajax.reload();
                    } else {
                        toastr.error(result.msg);
                    }
                },
            });
        }
    });
});

$(document).on('change', '#choose_product_form #variation_id', function() {
    var variation_id = $(this).val();
    
    if (variation_id) {
        $.ajax({
            method: 'get',
            url: "/manufacturing/is-recipe-exist/" + variation_id,
            dataType: 'json',
            success: function(result) {
                if (result == 1) {
                    open_row();
                    $('#choose_product_form #recipe_selection').addClass('hide');
                } else {
                    open_row();
                    $('#choose_product_form #recipe_selection').removeClass('hide');
                }
            },
        });
    } else {
        open_row();
        $('#choose_product_form #recipe_selection').removeClass('hide');
    }
})
</script>