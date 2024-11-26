@extends('layouts.app')
@section('title', __('warehouse.conveyor'))

@section('content')

<section class="content-header">
    <h1>{{ __('warehouse.Warehouse_Conveyor') }}</h1>
    <br>
</section> 
  
<!-- Main content -->

<section class="content">
   
    {!! Form::open(['url' => action('WarehouseController@conveyor'), 'method' => 'post', 'id' => 'make_converyor', 'files' => true ]) !!}
    <div class="row">
        <div class="col-lg-12 col-md-12 ">
            @component('components.widget', ['class' => 'box-solid'])
                <div class="form-group col-lg-6  " id="parent_cat_div">
                    {!! Form::label('parent_id', __( 'warehouse.warehouse_source' ) . ':') !!}
                    {!! Form::select('parent_id', $mainstore_categories, null, ['class' => 'form-control']); !!}
                </div>
                <div class="form-group col-lg-6 " id="parent_cat_div">
                    {!! Form::label('parent_id', __( 'warehouse.warehouse_dest' ) . ':') !!}
                    {!! Form::select('parent_id', $mainstore_categories, null, ['class' => 'form-control']); !!}
                </div>
            @endcomponent 
            @component('components.widget', ['class' => 'box-solid'])
				<div class="col-sm-10 col-sm-offset-1">

                    
					<div class="form-group">
						<div class="input-group">
							<div class="input-group-btn">
								<button type="button" class="btn btn-default bg-white btn-flat" data-toggle="modal" data-target="#configure_search_modal" title="{{__('lang_v1.configure_product_search')}}"><i class="fa fa-barcode"></i></button>
							</div>
							{!! Form::text('search_product', null, ['class' => 'form-control mousetrap', 'id' => 'search_product', 'placeholder' => __('lang_v1.search_product_placeholder')]); !!}
							 
						</div>
					</div>
				</div>

				<div class="row col-sm-12 pos_product_div" style="min-height: 0">

					<input type="hidden" name="sell_price_tax" id="sell_price_tax" value="{{$business_details->sell_price_tax}}">

					<!-- Keeps count of product rows -->
					<input type="hidden" id="product_row_count" 
						value="0">
					@php
						$hide_tax = '';
						if( session()->get('business.enable_inline_tax') == 0){
							$hide_tax = 'hide';
						}
					@endphp



					{{-- table for products  used for  direct sell --}}
					<div class="table-responsive">
					<table class="table table-condensed table-bordered table-striped table-responsive" id="pos_table">
						<thead>
							<tr>
								<th class="text-center">	
									@lang('sale.product')
								</th>
								<th class="text-center">
									@lang('sale.qty')
								</th>
								@if(!empty($pos_settings['inline_service_staff']))
									<th class="text-center">
										@lang('restaurant.service_staff')
									</th>
								@endif

								<th @can('edit_product_price_from_sale_screen')) hide @endcan>
									@lang('sale.unit_price')
								</th>

								<th @can('edit_product_discount_from_sale_screen') hide @endcan>
									@lang('receipt.discount')
								</th>

								<th class="text-center {{$hide_tax}}">
									@lang('sale.tax')
								</th>

								<th class="text-center {{$hide_tax}}">
									@lang('sale.price_inc_tax')
								</th>

								@if(!empty($warranties))
									<th>@lang('lang_v1.warranty')</th>
								@endif

								<th class="text-center">
									@lang('sale.subtotal')
								</th>

								<th class="text-center"><i class="fas fa-times" aria-hidden="true"></i></th>
							</tr>
						</thead>
						<tbody></tbody>
					</table>
					</div>

 
				</div>
		 
            @endcomponent




                <div class="form-group col-lg-12">
                    <input type="submit" class="btn btn-primary" value="@lang('warehouse.start_conveyor')">
                </div>
            </form>
        </div>
    </div>
        
</section>

<div class="modal fade contact_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
	{{-- @include('contact.create', ['quick_add' => true]) --}}
</div>
<!-- /.content -->
<div class="modal fade register_details_modal" tabindex="-1" role="dialog" 
	aria-labelledby="gridSystemModalLabel">
</div>
<div class="modal fade close_register_modal" tabindex="-1" role="dialog" 
	aria-labelledby="gridSystemModalLabel">
</div>

<!-- quick product modal -->
<div class="modal fade quick_add_product_modal" tabindex="-1" role="dialog" aria-labelledby="modalTitle"></div>  
{{-- @include('sale_pos.partials.configure_search_modal') --}}

@stop

@section('javascript')
	<script src="{{ asset('js/posss.js?v=' . $asset_v) }}"></script>
	<script src="{{ asset('js/producte.js?v=' . $asset_v) }}"></script>
	<script src="{{ asset('js/opening_stock.js?v=' . $asset_v) }}"></script>
	<!-- Call restaurant module if defined -->
    @if(in_array('tables' ,$enabled_modules) || in_array('modifiers' ,$enabled_modules) || in_array('service_staff' ,$enabled_modules))
    	<script src="{{ asset('js/restaurant.js?v=' . $asset_v) }}"></script>
    @endif
    <script type="text/javascript">
    //variation_id is null when weighing_scale_barcode is used.
function pos_product_row(variation_id = null, purchase_line_id = null, weighing_scale_barcode = null, quantity = 1) {

//Get item addition method
var item_addtn_method = 0;
var add_via_ajax = true;

if (variation_id != null && $('#item_addition_method').length) {
    item_addtn_method = $('#item_addition_method').val();
}

if (item_addtn_method == 0) {
    add_via_ajax = true;
} else {
    var is_added = false;

    //Search for variation id in each row of pos table
    $('#pos_table tbody').find('tr').each(function() {
            var row_v_id = $(this).find('.row_variation_id').val();
            var enable_sr_no = $(this).find('.enable_sr_no').val();
            var modifiers_exist = false;
            if ($(this).find('input.modifiers_exist').length > 0) {
                modifiers_exist = true;
            }

            if (row_v_id == variation_id && enable_sr_no !== '1' && !modifiers_exist &&  !is_added ) {
                add_via_ajax = false;
                is_added = true;

                //Increment product quantity
                qty_element = $(this).find('.pos_quantity');
                var qty = __read_number(qty_element);
                __write_number(qty_element, qty + 1);
                qty_element.change();

                round_row_to_iraqi_dinnar($(this));

                $('input#search_product')
                    .focus()
                    .select();
            }
    });
}

if (add_via_ajax) {
    var product_row = $('input#product_row_count').val();
    var location_id = $('input#location_id').val();
    var customer_id = $('select#customer_id').val();
     
    var is_direct_sell = false;
    if (
        $('input[name="is_direct_sale"]').length > 0 &&
        $('input[name="is_direct_sale"]').val() == 1
    ) {
        is_direct_sell = true;
    }

    var price_group = '';
    if ($('#price_group').length > 0) {
        price_group = parseInt($('#price_group').val());
    }

    //If default price group present
    if ($('#default_price_group').length > 0 && 
        !price_group) {
        price_group = $('#default_price_group').val();
    }

    //If types of service selected give more priority
    if ($('#types_of_service_price_group').length > 0 && 
        $('#types_of_service_price_group').val()) {
        price_group = $('#types_of_service_price_group').val();
    }

    $.ajax({
        method: 'GET',
        url: '/sells/pos/get_product_row/' + variation_id + '/' + location_id,
        async: false,
        data: {
            product_row: product_row,
            customer_id: customer_id,
            is_direct_sell: is_direct_sell,
            price_group: price_group,
            purchase_line_id: purchase_line_id,
            weighing_scale_barcode: weighing_scale_barcode,
            quantity: quantity
 
        },
        dataType: 'json',
        success: function(result) {
            
            if (result.success) {
                $('table#pos_table tbody')
                    .append(result.html_content)
                    .find('input.pos_quantity');
					// alert("stop");
                    var array = [];
                    var index_array = "<option value='";
                    $("#store_id option").each(function () {
                        array.push($(this).html());
                    });
                    // alert(result.html_content);
                    var start  = 0;
                    var count  = array.length;
                    array.forEach(element => {
                        if(start == count-1){
                            index_array = index_array + element  + "'>" + element + "</option>"
                            start = start + 1 ; 
                        
                        }else{
                            index_array = index_array + element   +"'>" + element  + "</option><option value='"
                            start = start + 1 ; 
                        }
                    });

                    var index = "<td><select name='store_sub' id='store_sub' class='form-control select2'>"+ quantity + "</select></td>";
                    $(index).insertBefore("#pos_table tbody  tr td:nth-child(5)");
                    // $("#purchase_entry_table tbody  td:nth-child(12)").hide();
                    $("#pos_table tbody  td:nth-child(6)").hide();

                //increment row count
                $('input#product_row_count').val(parseInt(product_row) + 1);
                var this_row = $('table#pos_table tbody')
                    .find('tr')
                    .last();
                pos_each_row(this_row);

                //For initial discount if present
                var line_total = __read_number(this_row.find('input.pos_line_total'));
                this_row.find('span.pos_line_total_text').text(line_total);

                pos_total_row();


                //Check if multipler is present then multiply it when a new row is added.
                if(__getUnitMultiplier(this_row) > 1){
                    this_row.find('select.sub_unit').trigger('change');
                }

                if (result.enable_sr_no == '1') {
                    var new_row = $('table#pos_table tbody')
                        .find('tr')
                        .last();
                    new_row.find('.add-pos-row-description').trigger('click');
                }

                round_row_to_iraqi_dinnar(this_row);
                __currency_convert_recursively(this_row);

                $('input#search_product')
                    .focus()
                    .select();

                //Used in restaurant module to show popup window to select extra
                if (result.html_modifier) {
                    $('table#pos_table tbody')
                        .find('tr')
                        .last()
                        .find('td:first')
                        .append(result.html_modifier);
                }

                //scroll bottom of items list
                $(".pos_product_div").animate({ scrollTop: $('.pos_product_div').prop("scrollHeight")}, 1000);
            } else {
                toastr.error(result.msg);
                $('input#search_product')
                    .focus()
                    .select();
            }
        },
    });
}
}

    	$(document).ready( function() {
    		$('#status').change(function(){
    			if ($(this).val() == 'final') {
    				$('#payment_rows_div').removeClass('hide');
    			} else {
    				$('#payment_rows_div').addClass('hide');
    			}
    		});
            $("<th>Store<th>").insertBefore("#pos_table thead  tr th:nth-child(5)");
            $("#pos_table thead th:nth-child(3)").hide();
            $("#pos_table thead th:nth-child(4)").hide();
            $("#pos_table thead th:nth-child(6)").hide();
            $("#pos_table thead th:nth-child(7)").hide();
            $("#pos_table thead th:nth-child(9)").hide();

            setInterval(() => {
                // $("#purchase_entry_table tbody tr td:nth-child(2)").hide();
                $("#pos_table tbody  td:nth-child(3)").hide();
                $("#pos_table tbody  td:nth-child(4)").hide();
                $("#pos_table tbody  td:nth-child(6)").hide();
                $("#pos_table tbody  td:nth-child(8)").hide();
                // $("#pos_table tbody  td:nth-child(7)").hide();
                // $("#pos_table tbody  td:nth-child(9)").hide();
                
            }, 100);
    		$('.paid_on').datetimepicker({
                format: moment_date_format + ' ' + moment_time_format.format('hh a'),
                ignoreReadonly: true,
            });

            $('#shipping_documents').fileinput({
		        showUpload: false,
		        showPreview: false,
		        browseLabel: LANG.file_browse_label,
		        removeLabel: LANG.remove,
		    });
    	});
    </script>
@endsection