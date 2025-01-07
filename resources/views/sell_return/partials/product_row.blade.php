<tr class="product_row">
	{{-- Product Name --}}
	<td>
		{{$product->product_name}}
		<br/>
		{{$product->sub_sku}}@if(!empty($product->brand)), {{$product->brand}} @endif
		&nbsp;
		<input type="hidden" class="enable_sr_no" value="{{$product->enable_sr_no}}">
		<i class="fa fa-commenting cursor-pointer text-primary add-pos-row-description" title="@lang('lang_v1.add_description')" data-toggle="modal" data-target="#row_description_modal_{{$row_count}}"></i>
		<?php $txd = ($product->product_description!=NULL || $product->product_description!="")?$product->product_description:"Enter Description";  $cypher = ($product->product_description!=NULL || $product->product_description!="")?Illuminate\Support\Facades\Crypt::encryptString($product->product_description):Illuminate\Support\Facades\Crypt::encryptString('.');  ?>
        <div class="description_line" data-line="{{$row_count}}">
            <pre style="white-space: nowrap;max-width:300px;max-height:150px" data-line="{{$row_count}}" class="btn btn-modal products_details" data-href="{{action('ProductController@changeDescription', ['id'=>$product->product_id,'text'=> $cypher  ,'line'=>$row_count,'return'=>'return'])}}" data-container=".view_modal">{!! $txd !!}</pre>
        </div>
        <textarea class="form-control control_products_details" data-line="{{$row_count}}" style="visibility:hidden" id="products[{{$row_count}}][sell_line_note]" name="products[{{$row_count}}][sell_line_note]" rows="{{$row_count}}"> {!! $product->product_description !!}</textarea>
       
	</td>

	{{-- Product Quantity --}}
	<td>
		<input type="hidden" name="products[{{$row_count}}][sell_line_id]" class="form-control sell_line_id" value="{{$product->id}}">
		<input type="hidden" name="products[{{$row_count}}][product_id]" class="form-control product_id" value="{{$product->product_id}}">

		<input type="hidden" value="{{$product->variation_id}}" 
			name="products[{{$row_count}}][variation_id]" class="row_variation_id">

		<input type="hidden" value="{{$product->enable_stock}}" 
			name="products[{{$row_count}}][enable_stock]">
		
		@if(empty($product->quantity_ordered))
			@php
				$product->quantity_ordered = 1;
			@endphp
		@endif
		<div class="input-group input-number">
		<span class="input-group-btn"><button type="button" class="btn btn-default btn-flat quantity-down"><i class="fa fa-minus text-danger"></i></button></span>
			<input type="text" class="form-control pos_quantity input_number mousetrap" data-min="1" value="{{$product->quantity_ordered}}" name="products[{{$row_count}}][quantity]" 
			@if($product->unit_allow_decimal == 1) data-decimal=1 @else data-decimal=0 data-rule-abs_digit="true" data-msg-abs_digit="@lang('lang_v1.decimal_value_not_allowed')" @endif
			data-rule-required="true" data-msg-required="@lang('validation.custom-messages.this_field_is_required')" >
		<span class="input-group-btn"><button type="button" class="btn btn-default btn-flat quantity-up"><i class="fa fa-plus text-success"></i></button></span>
		</div>
		@php $count = 0;  @endphp 
		@if(count($sub_units) > 0)
			<br>
			<select name="products[{{$row_count}}][sub_unit_id]" class="form-control input-sm sub_unit">
				@foreach($sub_units as $key => $value)
					<option value="{{$key}}" @if($count == 0) selected  @php $count = $key;  @endphp @endif data-price="{{$value['price']}}" data-check_price="{{$value['check_price']}}" data-multiplier="{{$value['multiplier']}}" data-unit_name="{{$value['name']}}" data-allow_decimal="{{$value['allow_decimal']}}" @if(!empty($product->sub_unit_id) && $product->sub_unit_id == $key) selected @endif>
						{{$value['name']}}
					</option>
				@endforeach 
			</select>
		@else
			{{$product->unit}}
		@endif

	</td>
	@php
		$hide_tax = 'hide';
        if(session()->get('business.enable_inline_tax') == 1){
            $hide_tax = '';
        }
        
		$tax_id = $product->tax_id;
		$unit_price_inc_tax = $product->sell_price_inc_tax;
		if($hide_tax == 'hide'){
			$tax_id = null;
			$unit_price_inc_tax = $product->default_sell_price;
		}
	@endphp
	{{-- Product Price before exc.vat --}}
	<td>
		<input type="text" name="products[{{$row_count}}][unit_price_before_dis_exc]" class="form-control unit_price_before_dis_exc input_number mousetrap" value="{{$product->default_sell_price}}">
		<br>
		<b><small>@lang('List Of Price'):</small></b>
		<div class="form-group">
			
			<select data-prices="{{json_encode($list_of_prices_in_unit)}}"  name="purchases[{{ $row_count }}][list_price]" class="form-control select2 list_price" placeholder="'Please Select'">
				<option value="" data-price="" selected>@lang('lang_v1.none')</option>
				@foreach($list_of_prices_in_unit as $key => $row_line)
				@if($key == $count)
						@foreach ($row_line as $item)
							<option value="{{$item['line_id']}}" data-price="{{$item['price']}}" data-value="{{$item['line_id']}}" >{{$item["name"]}}</option>
						@endforeach
					@endif
				@endforeach
			</select>
		</div>
	</td>
	{{-- Product Price before inc.vat --}}
	<td>
		<input type="text" name="products[{{$row_count}}][unit_price_before_dis_inc]" class="form-control unit_price_before_dis_inc" value="{{@num_format($unit_price_inc_tax)}}">
	</td>
	<td @if($currency != "" && $currency != 0)  class="cur_check"  @else  class="cur_check hide"  @endif>
        <input type="text" name="products[{{$row_count}}][unit_price_before_dis_exc_new_currency]" class="form-control unit_price_before_dis_exc_new_currency" value="{{($currency != "" && $currency != 0)?$product->unit_price_before_discount/DoubleVal($currency):0}}">
    </td >
    {{-- Product Fixed Discount Price --}}
	<td>
		<input type="text" name="products[{{$row_count}}][discount_amount_return]" class="form-control discount_amount_return" value="{{0}}">
	</td>
    {{-- Product Percentage Discount Price --}}
	<td>
		<input type="text" name="products[{{$row_count}}][discount_percent_return]" class="form-control discount_percent_return" value="{{0}}">
	</td>
	{{-- Product Price after inc.vat --}}
	<td>
		<input type="text" name="products[{{$row_count}}][unit_price_after_dis_exc]" readOnly class="form-control unit_price_after_dis_exc" value="{{@num_format($product->default_sell_price)}}">
	</td>
	{{-- Product Price after inc.vat --}}
	<td>
		<input type="text" name="products[{{$row_count}}][unit_price_after_dis_inc]" readOnly class="form-control unit_price_after_dis_inc" value="{{@num_format($unit_price_inc_tax)}}">
	</td>
	<td @if($currency != "" && $currency != 0)  class="cur_check"  @else  class="cur_check hide"  @endif>
        <input type="text" name="products[{{$row_count}}][unit_price_after_dis_exc_new_currency]" readOnly class="form-control unit_price_after_dis_exc_new_currency" value="{{($currency != "" && $currency != 0)?$product->unit_price/$currency:0}}">
    </td>
	<td class="{{$hide_tax}}">
		<input type="hidden" name="products[{{$row_count}}][item_tax]" class="form-control item_tax">
		{!! Form::select("products[$row_count][tax_id]", $tax_dropdown['tax_rates'], $tax_id, ['placeholder' => 'Select', 'class' => 'form-control tax_id'], $tax_dropdown['attributes']); !!}
	</td>

	<td class="{{$hide_tax}}">
		<input type="text" name="products[{{$row_count}}][unit_price_inc_tax]" class="form-control pos_unit_price_inc_tax input_number" value="{{@num_format($unit_price_inc_tax)}}">
	</td>
	
	<td>
		<input type="text" readonly name="products[{{$row_count}}][sub_total_price]" readOnly class="form-control pos_line_sub__total" value="{{$product->quantity_ordered*$unit_price_inc_tax }}">
	</td>


	@if(session('business.enable_lot_number'))
        <td>
            {!! Form::text('products[' . $row_count . '][lot_number]', null, ['class' => 'form-control input-sm']); !!}
        </td>
    @endif

    {{-- @if(session('business.enable_product_expiry'))
        <td style="text-align: left;">
            <div class="input-group">
                <span class="input-group-addon">
                    <i class="fa fa-calendar"></i>
                </span>
                {!! Form::text('products[' . $row_count . '][exp_date]', null, ['class' => 'form-control input-sm expiry_datepicker', 'readonly']); !!}
            </div>
        </td>
    @endif --}}

	<td class="text-center">
		<i class="fa fa-trash pos_remove_row cursor-pointer" aria-hidden="true"></i>
	</td>
</tr>

<script type="text/javascript">
	$(document).ready(function(){
		$('input.expiry_datepicker').datepicker({
        	autoclose: true,
        	format:datepicker_date_format
    	});
	});
</script>