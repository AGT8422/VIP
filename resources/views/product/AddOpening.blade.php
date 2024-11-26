@extends('layouts.app')
@section('title', __('purchase.add_Open'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1><b>@lang('purchase.add_Open') <i class="fa fa-keyboard-o hover-q text-muted" aria-hidden="true" data-container="body" data-toggle="popover" data-placement="bottom" data-content="@include('purchase.partials.keyboard_shortcuts_details')" data-html="true" data-trigger="hover" data-original-title="" title=""></i></b></h1>
	<h5><i><b>{{ "   Products  >  " }} </b> <b>{{ "   List Opening Stock  >  " }} </b> {{ "Create Opening Stock "   }} <b> {{"   "}} </b></i></h5>  
	<br> 
</section>

<!-- Main content -->
<section class="content">
		<!-- Page level currency setting -->
		<input type="hidden" id="p_code" value="{{$currency_details->code}}">
		<input type="hidden" id="p_symbol" value="{{$currency_details->symbol}}">
		<input type="hidden" id="p_thousand" value="{{$currency_details->thousand_separator}}">
		<input type="hidden" id="p_decimal" value="{{$currency_details->decimal_separator}}">
		@include('layouts.partials.error')
		{!! Form::open(['url' => 'opening-quantity/add', 'method' => 'post', 'id' => 'add_purchase_form', 'files' => true ]) !!}
			@if(session('yes'))
				<div class="alert success alert-success">
				{{ session('yes') }}
				</div>
			@endif
			<div class="row" style="margin:0px 10%">
				<div class="content">
					@component("components.widget",["class"=>"box-primary","title"=>__("Insert The Data")])
						<div  class="col-md-4">
							<div class="form-group">
								{!! Form::label('store_id_', __('warehouse.nameW').':*') !!}
								{!! Form::select('store_id_', $childs, null, ['class' => 'form-control ', 'name' => "store_id_", 'required', 'id' => 'store_id_' ]); !!}
							</div>
						</div>

						<div class="col-sm-4">
							<div class="form-group">
								{!! Form::label('date', __('home.Date').':') !!}
								{!! Form::date('date',null, ['class' => 'form-control ' ]); !!}
							</div>
						</div>
						{{-- #2024-8-6 --}}
						<div class="col-sm-4">
							<div class="form-group">
								{!! Form::label('list_price', __('List  Of Prices').':') !!}
								{!! Form::select('list_price',$list_of_prices,null, ['class' => 'form-control select2' , 'id' => 'list_price' ]); !!}
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
								{!! Form::select('location_id', $business_locations, $default_location, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'required'], $bl_attributes); !!}
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
						<div class="col-sm-10">
							<br>
							<div class="row">
								<div class="col-sm-11">
									<div class="form-group">
										<div class="input-group" style="padding:3px">
											<span class="input-group-addon">
												<i class="fa fa-search"></i>
											</span>
											{!! Form::text('search_product', null, ['class' => 'form-control mousetrap', 'id' => 'search_product', 'placeholder' => __('lang_v1.search_product_placeholder'), 'disabled' => $search_disable]); !!}
										</div>
									</div>
								</div>
								<div class="col-sm-1">
									<div class="form-group">
										<button tabindex="-1" type="button" class="btn btn-info btn-modal"data-href="{{action('ProductController@quickAdd')}}" 
									data-container=".quick_add_product_modal"><i class="fa fa-plus"></i> @lang( 'product.add_new_product' ) </button>
									</div>
								</div>
							</div>
						</div>
								@php
									$hide_tax = '';
									if( session()->get('business.enable_inline_tax') == 0){
										$hide_tax = 'hide';
									}
								@endphp
							<div class="row">
								<div class="col-sm-12">
									<div class="  table-responsive">
										<table class="table table-condensed table-bordered table-th-green text-center table-striped dataTable" id="purchase_entry_table">
											<thead>
												<tr>
													<th>#</th>
													<th>@lang( 'product.product_name' )</th>
													<th>@lang( 'home.quantity' )</th>
													<th>@lang( 'lang_v1.price' )</th>
													<th class="expire hide">@lang( 'Expire Date' )</th>
													<th>@lang( 'home.Store' )</th>
													<th>@lang( 'home.Total' )</th>
													<th><i class="fa fa-trash" aria-hidden="true"></i></th>
												</tr>
											</thead>
											<tbody></tbody>
											<tfoot></tfoot>
										</table>
									</div>
									<hr/>
									<div class="pull-right col-md-5">
										<table class="pull-right col-md-12">
											<tr>
												<th class="col-md-7 text-right">@lang( 'lang_v1.total_items' ):</th>
												<td class="col-md-5 text-left">
													<input class="total_qty" id="total_qty_eb" name="total_qty_eb" style="border:0px solid transparent"> 
												</td>
											</tr>
											<tr>
												<th class="col-md-7 text-right">@lang( 'purchase.net_total_amount'):</th>
												<td class="col-md-5 text-left">
													<input class="total_amount_eb hide " id="total_amount_eb" name="total_amount_eb" style="border:0px solid transparent" value="">  
													<input class="total_amount  " id="total_amount" name="total_amount" style="border:0px solid transparent" value="">  
													<!-- This is total before purchase tax-->
													<input type="hidden" id="total_subtotal_input" value=0  name="total_before_tax">
												</td>
											</tr>
									
										</table>
									</div>

									<input type="hidden" id="row_count" value="0">
									<div class="col-sm-3 hide">
										<div class="form-group">
											{!! Form::label('store_id', __('warehouse.warehouse').':*') !!}
											{{-- @show_tooltip(__('tooltip.purchase_location')) --}}
											{!! Form::select('store_id', $mainstore_categories, null, ['class' => 'form-control select2',  'required'], $bl_attributes); !!}
										</div>
									</div>
									<div class="row">
										<div class="col-sm-12">
											<button type="button" id="submit_purchase_form" class="btn btn-primary pull-right btn-flat">@lang('messages.save')</button>
										</div>
									</div>
								</div>
							</div> 
						
						
					@endcomponent
				</div>
			</div>
		{!! Form::close() !!}
</section>
<!-- quick product modal -->
<div class="modal fade product_v_modal" tabindex="-1" role="dialog" aria-labelledby="modalTitle"></div>
<div class="modal fade stock_v_modal" tabindex="-1" role="dialog" aria-labelledby="modalTitle"></div>
<div class="modal fade quick_add_product_modal" tabindex="-1" role="dialog" aria-labelledby="modalTitle"></div>
<div class="modal fade contact_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
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
					var line_id = "line_sort[" +count+ "]" ;
					e.attr("data-row_index",count);
					inner.html(count);
					// el.attr("name",line_id)
					el.attr("value",count++);
					// el.val(count++);					
				});
			}
		});

		$(document).ready( function(){
			var open  = "open";
			if ($('#search_product').length > 0) {
				$('#search_product')
				.autocomplete({
							source: function(request, response) {
								$.getJSON(
								'/purchases/get_products/open/' + open + "?v=type_o=purchase'" ,
								{ location_id: $('#location_id').val(), term: request.term },
								response
								);
							},
							minLength: 2,
							response: function(event, ui) {
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
								if( JSON.stringify(ui.item.open) != null ){
									get_purchase_entry_row_open(ui.item.product_id, ui.item.variation_id , ui.item.open);
									//  #2024-8-6
									if(JSON.stringify(ui.item.session) === "1"){
										$(".expire").removeClass("hide");
									}
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

		$(document).on("change",".eb_price,.purchase_",function(){
			updatess();
		});

		function updatess(check_price = 0){
			var total_quantity  = 0 ;
			var total_amount  = 0 ;
			var total  = 0 ;
		 
			$("#purchase_entry_table tbody  td:nth-child(5)").hide();
			$("#purchase_entry_table tbody  td:nth-child(6)").hide();
  			$("#purchase_entry_table tbody  td:nth-child(12)").hide();
			$("#purchase_entry_table tbody  td:nth-child(10)").hide();
			$("#purchase_entry_table tbody  td:nth-child(8)").hide();
			$("#purchase_entry_table tbody  td:nth-child(7)").hide();
			$("#purchase_entry_table tbody  td:nth-child(9)").hide();
			$("#purchase_entry_table tbody  td:nth-child(15)").hide();

			$('.purchase_quantity').each(function(){
				var  el        = $(this).parent().parent();
				var  quantity  = parseFloat($(this).val());
				var  price     = el.children().find(".eb_price").val();
				var  sub_unit  = el.children().find(".sub_unit");
				
				var multiplier = parseFloat(
					sub_unit.find(':selected')
						.data('multiplier')
				);
				 
			 
				multiplier = isNaN(multiplier)?1:multiplier;
				 
				if(check_price == 0){
						el.children().find(".purchase_unit_cost_after_tax_").val((quantity*price).toFixed(3)); 
				}else{
 					el.children().find(".purchase_unit_cost_after_tax_").val((quantity*price).toFixed(3)); 
				}
				 

				if($(".total_amount").val() != null){

					if( quantity == null ){
						$(".total_amount").val("");
					}else{
						
						var  amount   = parseFloat(el.children().find('.eb_price').val()) ;
						// alert(quantity);
						total_quantity += quantity;
						total_amount   += amount*quantity ;
						total           = total_amount.toFixed(3) ;

						$(".total_amount").val(total);
						$(".total_qty").val(total_quantity);

					}
				}
			
			});
			 
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
		//  #2024-8-6
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
			
			
			html = "";global_html = "";
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
						html += '<option value="'+list_of[i][e].line_id+'" data-price="'+list_of[i][e].price+'"  data-value="'+list_of[i][e].line_id+'" >'+list_of[i][e].name+'</option>';
						View_selected = (counter == 0)?"selected":"";
						global_html += '<option value="'+list_of[i][e].line_id+'" '+View_selected+' data-price="'+list_of[i][e].price+'"  data-value="'+list_of[i][e].line_id+'" >'+list_of[i][e].name+'</option>';
						counter++;
					}
				}
			}
			if(check_price == 0){
				cp_element.val(unit_cost) ;
			}else{
				cp_element.val(unit_cost* multiplier)

			} 
			if(html != ""){
				prices.html(html);
			 
				// global_prices.html(global_html);
			}
			cp_element.change();
			updatess(check_price);
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
			cp_element.change();
			updatess();
		});
	 
		$(document).on("change","#list_price",function(){
			var golbal = $(this).val();
			$('table#purchase_entry_table .eb_price').each( function() {
				var price_item      = $(this); 
				var price      = $(this).closest("tr"); 
				var prices     = price.find(".list_price"); 
				var list_price = price.find(".list_price").find(":selected").val(); 
				if(list_price == "" || list_price == null){
					prices.children().each(function(){ 
					if($(this).data("value") == golbal){
						price_item.val($(this).data("price"));
						updatess();
					}
				}); 
				}
			});
			 
		});

	</script>
	@include('purchase.partials.keyboard_shortcuts')
@endsection
