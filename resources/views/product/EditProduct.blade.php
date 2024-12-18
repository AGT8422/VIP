
 
@extends('layouts.app')
@section('title', __('purchase.update_open'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('purchase.update_open') <i class="fa fa-keyboard-o hover-q text-muted" aria-hidden="true" data-container="body" data-toggle="popover" data-placement="bottom" data-content="@include('purchase.partials.keyboard_shortcuts_details')" data-html="true" data-trigger="hover" data-original-title="" title=""></i></h1>
</section>

<!-- Main content -->
<section class="content">

	<!-- Page level currency setting -->
	<input type="hidden" id="p_code" value="{{$currency_details->code}}">
	<input type="hidden" id="p_symbol" value="{{$currency_details->symbol}}">
	<input type="hidden" id="p_thousand" value="{{$currency_details->thousand_separator}}">
	<input type="hidden" id="p_decimal" value="{{$currency_details->decimal_separator}}">

	@include('layouts.partials.error')

		{!! Form::open(['url' => '/opening-quantity/update/', 'method' => 'post', 'id' => 'add_purchase_form', 'files' => true ]) !!}
		@if(session('yes'))
			<div class="alert success alert-success">
			{{ session('yes') }}
			</div>
		@endif
	
        <input type="hidden" name="transaction_id" value="{{ $data->transaction_id }}">
		<div class="row">
			
 
			
			  
			  
			@if(count($business_locations) >= 1)
				@php 
					$default_location = current(array_keys($business_locations->toArray()));
					$search_disable = false; 
				   
				@endphp
			@else
				@php $default_location = null;
				    $search_disable = true;
				@endphp
			@endif
 

			<div  class="col-md-3">
				<div class="form-group">
					{!! Form::label('main_store_id', __('warehouse.nameW').':*') !!}
					{!! Form::select('main_store_id', $stores, null, ['class' => 'form-control select2', 'name' => "main_store_id", 'required', 'id' => 'main_store_id' ]); !!}
 				</div>
			</div>
			<div class="col-sm-3">
				<div class="form-group">
					{!! Form::label('date', __('home.Date').':') !!}
					{!! Form::date('date',$date, ['class' => 'form-control ' ]); !!}
				</div>
			</div>
			{{-- #2024-8-6 --}}
			<div class="col-sm-4">
				<div class="form-group">
					{!! Form::label('list_price', __('List  Of Prices').':') !!}
					{!! Form::select('list_price',$list_of_prices,$transaction->list_price, ['class' => 'form-control select2' , 'id' => 'list_price' ]); !!}
				</div>
			</div>
			@if(count($business_locations) >= 1)
				@php 
					$default_location = current(array_keys($business_locations->toArray()));
					$search_disable = false; 
				@endphp
			@else
				@php $default_location = null;
						$search_disable   = true;
				@endphp
			@endif
			<div class="col-sm-6  hide">
				<div class="form-group">
					{!! Form::label('location_id', __('purchase.business_location').':*') !!}
					@show_tooltip(__('tooltip.purchase_location'))
					{!! Form::select('location_id', $business_locations, $default_location, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'required'] ); !!}
				</div>
			</div>
		



			<!-- Currency Exchange Rate -->
			<div class="col-sm-3 @if(!$currency_details->purchase_in_diff_currency) hide @endif">
				<div class="form-group">
					{!! Form::label('exchange_rate', __('purchase.p_exchange_rate') . ':*') !!}
					@show_tooltip(__('tooltip.currency_exchange_factor'))
					<div class="input-group">
						<span class="input-group-addon">
							<i class="fa fa-info"></i>
						</span>
						{!! Form::number('exchange_rate', $currency_details->p_exchange_rate, ['class' => 'form-control', 'required', 'step' => 0.001]); !!}
					</div>
					<span class="help-block text-danger">
						@lang('purchase.diff_purchase_currency_help', ['currency' => $currency_details->name])
					</span>
				</div>
			</div>
			
		 

		
 

				{{--  store --}}
		</div>
 

	@component('components.widget', ['class' => 'box-primary'])
		<div class="row">
			<div class="col-sm-9  ">
				<div class="form-group">
					<div class="input-group">
						<span class="input-group-addon">
							<i class="fa fa-search"></i>
						</span>
						{!! Form::text('search_product', null, ['class' => 'form-control mousetrap', 'id' => 'search_product', 'placeholder' => __('lang_v1.search_product_placeholder')]); !!}
					</div>
				</div>
			</div>
			<div class="col-sm-2">
				<div class="form-group">
					<button tabindex="-1" type="button" class="btn btn-link btn-modal"data-href="{{action('ProductController@quickAdd')}}" 
            	data-container=".quick_add_product_modal"><i class="fa fa-plus"></i> @lang( 'product.add_new_product' ) </button>
				</div>
                
			</div>
		</div>
		@php
			$hide_tax = '';
			if( session()->get('business.enable_inline_tax') == 0){
				$hide_tax = 'hide';
			}
		@endphp
	 
		 
		@include("product.partials.edit_open_row")

		

		<div class="row">
			<div class="col-sm-12">
				<button type="button" id="submit_purchase_form" class="btn btn-primary pull-right btn-flat">@lang('messages.update')</button>
			</div>
		</div>

	@endcomponent
    <br>
   

 

{!! Form::close() !!}



</section>
<!-- quick product modal -->
<div class="modal fade quick_add_product_modal" tabindex="-1" role="dialog" aria-labelledby="modalTitle"></div>
<div class="modal fade contact_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
	@include('contact.create', ['quick_add' => true])
</div>
<!-- /.content -->
@endsection

@section('javascript')
	<script src="{{ asset('js/purchase.js?v=' . $asset_v) }}"></script>
	<script src="{{ asset('js/producte.js?v=' . $asset_v) }}"></script>
	<script src="{{ asset('js/open_total.js?v=' . $asset_v) }}"></script>
	<script type="text/javascript">
		$('#purchase_entry_table tbody').sortable({
			cursor: "move",
			handle: ".handle",
            items: "> tr",
			update: function(event, ui) {
				var count = 1;
				$(".line_sorting").each(function(){
					e      = $(this); 
					var el = $(this).children().find(".line_sort"); 
					var inner = $(this).children().find(".line_ordered");  
					e.attr("data-row_index",count);
					inner.html(count);
					el.attr("value",count++);
					// el.val(count++);					
				});
			}
		});
 		function total_(){
			var total = 0 ; 
			var qty = 0 ; 
			$('#purchase_entry_table .row_total_cost_').each(function(index){
				total = total +  parseFloat($(this).val()) ;
			});
			$('#purchase_entry_table .purchase_unit_cost_after_tax_').each(function(index){
				total = total +  parseFloat($(this).val()) ;
			});
			$('#purchase_entry_table .purchase_quantity').each(function(index){
				qty = qty +  parseFloat($(this).val()) ;
			});
			$(".total_item_").html(qty);
			$(".total_price_").html(total);
 
		}
		total_();
		
		$('#purchase_entry_table .purchase_quantity').change(function(){
			onChange($(this));
		});
		
		 
		$('#purchase_entry_table .purchase_').change(function(){ 
			onChange($(this));
		});
		 
		  
	    function onChange(item){
			 
			var tax_amount  = parseFloat($('#tax_id option:selected').data('tax_amount')) ;
			 var el         = item.parent().parent(); 
 			 var price      = parseFloat(item.val())*(tax_amount/100) + parseFloat(item.val()) ; 
			 var qty        = el.children().find('.purchase_quantity').val();
			 var eb_qty     = el.children().find('.purchase_').val();
			 var price_     = el.children().find('.purchase_unit_cost_without_discount_s').val();
			 var eb_price   = el.children().find('.eb_price ').val();
			 el.children().find('.row_total_cost_').val((qty * price_).toFixed(2));
			 el.children().find('.purchase_unit_cost_after_tax_').val((eb_qty * eb_price).toFixed(2));
 			 total_();
		 }
		 
		

		$(document).ready( function(){
 			var open  = "open";
			var edit_open  = "edit";
			if ($('#search_product').length > 0) {
				$('#search_product')
				.autocomplete({
							source: function(request, response) {
								$.getJSON( 
								'/purchases/get_products/open/' + open + "/" + edit_open  +  "?v=type_o=purchase" ,
								{ location_id: $('#location_id').val(), term: request.term },
								response
								);
							},
							minLength: 2,
							response: function(event, ui) {
								// alert(JSON.stringify(ui));
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
								if(JSON.stringify(ui.item.edit) != null){
									get_purchase_entry_row_open(ui.item.product_id, ui.item.variation_id , ui.item.open ,ui.item.edit);
									 
								}else if( JSON.stringify(ui.item.open) != null ){
									get_purchase_entry_row_open(ui.item.product_id, ui.item.variation_id , ui.item.open);
 								}else{
									get_purchase_entry_row(ui.item.product_id, ui.item.variation_id);
 								}
								 
							},
						})
						.autocomplete('instance')._renderItem = function(ul, item) {
						return $('<li>')
							.append('<div>' + item.text + '</div>')
							.appendTo(ul);
					};
				}

      		__page_leave_confirmation('#add_purchase_form');
      		$('.paid_on').datetimepicker({
                format: moment_date_format + ' ' + moment_time_format,
                ignoreReadonly: true,
            });
    	});

		var total_quantity  = 0 ;
		var total_amount  = 0 ;
		var total  = 0 ;

		setInterval(() => {
			$("#purchase_entry_table .eb_price").each(function(){
				var el = $(this);
				var el_q = $(this).parent().parent().children().find(".purchase_quantity").val();
				var el_p = $(this).parent().parent().children().find(".purchase_unit_cost_after_tax_");
				el.change(function(){
 					 $("#total_amount").val(el.val()) ;
					 el_p.val(el.val()*el_q) ;
		
				});
			});
			$("#purchase_entry_table .purchase_unit_cost_without_discount_s").each(function(){
				var el = $(this);
				var el_q = $(this).parent().parent().children().find(".purchase_quantity").val();
				var el_p = $(this).parent().parent().children().find(".row_total_cost_");
				el.change(function(){
					 $("#total_amount").val(el.val()) ;
					 el_p.val(el.val()*el_q) ;
				});
			});
			 
		
			
			total_quantity  = 0;
			total_amount  = 0;
			total =0;
			 
			$('.purchase_').each(function(){
				var e  =  $(this);
				var el =  $(this).parent().parent();
				var  quantity = parseFloat($(this).val());
				$(this).on("change",function(){
					tot_items();
					onChange(e);
				});
			});

			$('.purchase_quantity').each(function(){
				var el =  $(this).parent().parent();
				var  quantity = parseFloat($(this).val());
				$(this).on("change",function(){
					tot_items();
					
				});
			});
			$('.eb_price').each(function(){
				var el =  $(this).parent().parent();
				var  quantity = parseFloat($(this).val());
				$(this).on("change",function(){
					 
 					tot_items();
				 
				});
			});
			$('.purchase_unit_cost_without_discount_s').each(function(){
				var el =  $(this).parent().parent();
				var  quantity = parseFloat($(this).val());
				$(this).on("change",function(){
 					tot_items();
				 
				});
			});
			tot_items();
			
		}, 500);
		 
		
		function updatess(){
			$("#purchase_entry_table tbody  td:nth-child(5)").hide();
			$("#purchase_entry_table tbody  td:nth-child(6)").hide();
			$("#purchase_entry_table tbody  td:nth-child(7)").hide();
			$("#purchase_entry_table tbody  td:nth-child(8)").hide();
			$("#purchase_entry_table tbody  td:nth-child(9)").hide();
			$("#purchase_entry_table tbody  td:nth-child(10)").hide();
			$("#purchase_entry_table tbody  td:nth-child(12)").hide();
			
		}

		function tot_items(){
			var total_qty_ = 0;
			var total_qty_final = 0;
			
			$('.purchase_quantity').each(function(){
				var el =  $(this).parent().parent();
				var  quantity = parseFloat($(this).val());
				total_qty_ = total_qty_ + quantity;
			});
			console.log(total_qty_+"__");
			total_qty_final = parseFloat(total_qty_);
			$(".total_item_").html(total_qty_final);
			console.log(total_qty_final +"__*__");
			tot_price();
		}
		function tot_price(){
			 
				var total_prc = 0;
				var total_prc_ = 0;
				var total_prc_final = 0;
				$('.purchase_unit_cost_after_tax_').each(function(){
					var el =  $(this).parent().parent();
					var  prc = parseFloat($(this).val());
					total_prc = total_prc + prc;
				});
				$('.row_total_cost_').each(function(){
					var el =  $(this).parent().parent();
					var  prc = parseFloat($(this).val());
					total_prc_ = total_prc_ + prc;
				});
				total_prc_final = parseFloat(total_prc_) + parseFloat(total_prc);
				$(".total_price_").html(total_prc_final);
				console.log(total_prc_final);
				 
		}
    	$(document).on('change', '.payment_types_dropdown, #location_id', function(e) {
		    var default_accounts = $('select#location_id').length ? 
		                $('select#location_id')
		                .find(':selected')
		                .data('default_payment_accounts') : [];
		    var payment_types_dropdown = $('.payment_types_dropdown');
		    var payment_type = payment_types_dropdown.val();
		    var payment_row = payment_types_dropdown.closest('.payment_row');
	        var row_index = payment_row.find('.payment_row_index').val();

	        var account_dropdown = payment_row.find('select#account_' + row_index);
		    if (payment_type && payment_type != 'advance') {
		        var default_account = default_accounts && default_accounts[payment_type]['account'] ? 
		            default_accounts[payment_type]['account'] : '';
		        if (account_dropdown.length && default_accounts) {
		            account_dropdown.val(default_account);
		            account_dropdown.change();
		        }
		    }

		    if (payment_type == 'advance') {
		        if (account_dropdown) {
		            account_dropdown.prop('disabled', true);
		            account_dropdown.closest('.form-group').addClass('hide');
		        }
		    } else {
		        if (account_dropdown) {
		            account_dropdown.prop('disabled', false); 
		            account_dropdown.closest('.form-group').removeClass('hide');
		        }    
		    }
		});
		// #2024-8-6
		$('table#purchase_entry_table').on('change', 'select.sub_unit', function() {
			 
			 var tr = $(this).closest('tr');
			 var base_unit_cost = tr.find('input.base_unit_cost').val();
			 var prices = tr.find('.list_price');
			 var base_unit_selling_price = tr.find('input.base_unit_selling_price').val();
			 var global_prices = $('#list_price');

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
			  
			 var cp_element = tr.find('input.eb_price');
			 var cp_element_2 = tr.find('input.purchase_unit_cost_without_discount_s');
			 
			 if(check_price == 0){
				 cp_element.val(unit_cost) ;
				 cp_element_2.val(unit_cost) ;
			 }else{
				 cp_element.val(unit_cost* multiplier)
				 cp_element_2.val(unit_cost* multiplier)
 
			 } 
			 html = "";global_html = "";
			 list_price = JSON.stringify(tr.find('.list_price').data("prices"));
			 list_of    = JSON.parse(list_price);
			 
			 for(i in list_of){
				 if(i == $(this).find(':selected').val()){
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
			 cp_element.change();
			 cp_element_2.change();
			 tot_items();
			 tot_price();
		 });
		$(document).on("change","#list_price",function(){
			var golbal = $(this).val();
			$('table#purchase_entry_table .eb_price').each( function() {
				var price_item   = $(this); 
				var price      = $(this).closest("tr"); 
				var prices     = price.find(".list_price"); 
 				var list_price = price.find(".list_price").find(":selected").val(); 
				if(list_price == "" || list_price == null){
					prices.children().each(function(){ 
					if($(this).data("value") == golbal){
						price_item.val($(this).data("price"));
						 
 					}
				}); 
				}
			});
			$('table#purchase_entry_table .purchase_unit_cost_without_discount_s').each( function() {
				var price_item   = $(this); 
				var price      = $(this).closest("tr"); 
				var prices     = price.find(".list_price"); 
 				var list_price = price.find(".list_price").find(":selected").val(); 
				if(list_price == "" || list_price == null){
					prices.children().each(function(){ 
					if($(this).data("value") == golbal){
						price_item.val($(this).data("price"));
					 
 					}
				}); 
				}
			});
			$('#purchase_entry_table .purchase_quantity').each(function(){
				onChange($(this));
			});
			
		});
		$('table#purchase_entry_table').on('change', 'select.list_price', function() {
		 
			var tr                      = $(this).closest('tr');
			var base_unit_cost          = tr.find('input.base_unit_cost').val();
			var base_unit_selling_price = tr.find('input.base_unit_selling_price').val();
			var global_price            = $("#list_price").val();
			var price = parseFloat(
				$(this)
					.find(':selected')
					.data('price')
				);
			var cp_element = tr.find('input.eb_price');
			var cp_element_2 = tr.find('input.purchase_unit_cost_without_discount_s');
			
			if(isNaN(price) ){	
				$(this).children().each(function(){ 
					if($(this).data("value") == global_price){
						final_price = parseFloat($(this).data("price"));
					}
				}); 
			}else{
				final_price = parseFloat(price);
			}
			cp_element.val(final_price);
			cp_element_2.val(final_price);
			cp_element.change();
			cp_element_2.change();
			tot_price();
		});
	</script>
	@include('purchase.partials.keyboard_shortcuts')
@endsection
