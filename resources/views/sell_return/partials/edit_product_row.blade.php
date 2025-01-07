<tr class="product_row">
	{{-- Product Name --}}
	<td>
		{{$product->product->name}}
		<br/>
		{{$product->sub_sku}}@if(!empty($product->brand)), {{$product->brand}} @endif
		&nbsp;
		<input type="hidden" class="enable_sr_no" value="{{$product->enable_sr_no}}">
		<i class="fa fa-commenting cursor-pointer text-primary add-pos-row-description" title="@lang('lang_v1.add_description')" data-toggle="modal" data-target="#row_description_modal_{{$row_count}}"></i>
		<?php $txt = ($product->sell_line_note)?$product->sell_line_note:(($product->product->product_description!=null || $product->product->product_description != '')?$product->product->product_description:'.');   $cypher = Illuminate\Support\Facades\Crypt::encryptString($txt);  ?>
        <div class="description_line" data-line="{{$row_count}}">
            <pre style="white-space: nowrap;max-width:300px;max-height:150px" data-line="{{$row_count}}" class="btn btn-modal products_details" data-href="{{action('ProductController@changeDescription', ['id'=>$product->product->id,'text'=> $cypher  ,'line'=>$row_count,'return'=>'return'])}}" data-container=".view_modal">{!! ($product->sell_line_note)?$product->sell_line_note:(($product->product->product_description!=null||$product->product->product_description!="")?$product->product->product_description:"Enter Description") !!}</pre>
        </div>
        <textarea class="form-control control_products_details" data-line="{{$row_count}}" style="visibility:hidden" id="products[{{$row_count}}][sell_line_note]" name="products[{{$row_count}}][sell_line_note]" rows="{{$row_count}}"> {!! ($product->sell_line_note)?$product->sell_line_note:$product->product->product_description !!}</textarea>
       
	</td>

	{{-- Product Quantity --}}
	<td>
		@php
	 
			$pro        = \App\Product::find($product->product_id); 
            $var        = ($pro->variations)?$pro->variations->first()->default_sell_price:0;
        
            $allUnits   = [];
            $business   = \App\Business::find($pro->business_id);
            $allUnits[$pro->unit_id] = [
                'name' => $pro->unit->actual_name,
                'multiplier' => $pro->unit->base_unit_multiplier,
                'allow_decimal' => $pro->unit->allow_decimal,
                'price' => $var,
                'check_price' => 0,
                ];
              
            // $productUtil    = new \App\Utils\ProductUtil();
            // $sub_units      = $productUtil->getSubUnits($pro->business_id, $pro->unit_id, false, $pro->id);
            // foreach($sub_units as $k => $lines){
            //     $allUnits[$k] =  $lines; 
            // }
            if($pro->sub_unit_ids != null){
                foreach($pro->sub_unit_ids  as $i){
                        $row_price    =  0;
                        $un           = \App\Unit::find($i);
                        $row_price    = \App\Models\ProductPrice::where("unit_id",$i)->where("product_id",$pro->id)->where("number_of_default",0)->first();
                        $row_price    = ($row_price)?$row_price->default_sell_price:0;
                        $allUnits[$i] = [
                            'name'          => $un->actual_name,
                            'multiplier'    => $un->base_unit_multiplier,
                            'allow_decimal' => $un->allow_decimal,
                            'price'         => $row_price,
                            'check_price'   => $business->default_price_unit,
                        ] ;
                    }
            } 
            $sub_units = $allUnits  ;
			$list_of_prices_in_unit = \App\Product::getProductPrices($pro->id);
		@endphp
		<input type="hidden" name="row_count" class="form-control row_count" value="{{$row_count}}">
		<input type="hidden" name="products[{{$row_count}}][sell_line_id]" class="form-control sell_line_id" value="{{$product->id}}">
		<input type="hidden" name="products[{{$row_count}}][product_id]" class="form-control product_id" value="{{$product->product_id}}">

		<input type="hidden" value="{{$product->variation_id}}"  name="products[{{$row_count}}][variation_id]" class="row_variation_id">

		<input type="hidden" value="{{$product->enable_stock}}" 
			name="products[{{$row_count}}][enable_stock]">
		
		@if(empty($product->quantity_ordered))
			@php
				$product->quantity_ordered = $product->quantity;
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
			{{$product->product->unit->actual_name}}
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
		$tax_i = \App\TaxRate::where("id",$product->transaction->tax_id)->first();
		if(!empty($tax_i)){
			$vat = $tax_i->amount; 
		}else{
			$vat = 0;
		}
	@endphp
	{{-- Product Price before exc.vat --}}
	<td>
		<input type="text" name="products[{{$row_count}}][unit_price_before_dis_exc]" class="form-control unit_price_before_dis_exc input_number mousetrap" value="{{floatVal($product->unit_price_before_discount)}}">
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
		<input type="text" name="products[{{$row_count}}][unit_price_before_dis_inc]" class="form-control unit_price_before_dis_inc" value="{{floatVal($product->unit_price_before_discount+$vat  )}}">
	</td>
	<td @if($currency != "" && $currency != 0)  class="cur_check"  @else  class="cur_check hide"  @endif>
        <input type="text" name="products[{{$row_count}}][unit_price_before_dis_exc_new_currency]" class="form-control unit_price_before_dis_exc_new_currency" value="{{($currency != "" && $currency != 0)?$product->unit_price_before_discount/DoubleVal($currency):0}}">
    </td >
    {{-- Product Fixed Discount Price --}}
	<td>
		<input type="text" name="products[{{$row_count}}][discount_amount_return]" class="form-control discount_amount_return" value="{{($product->unit_price_before_discount!=0)?round(floatVal(($product->line_discount_amount*100)/$product->unit_price_before_discount),2):0}}">
	</td>
    {{-- Product Percentage Discount Price --}}
	<td>
		<input type="text" name="products[{{$row_count}}][discount_percent_return]" class="form-control discount_percent_return" value="{{floatVal($product->line_discount_amount)}}">
	</td>
	{{-- Product Price after exc.vat --}}
	<td>
		<input type="text" name="products[{{$row_count}}][unit_price_after_dis_exc]" class="form-control unit_price_after_dis_exc" readOnly value="{{@num_format($product->unit_price)}}">
	</td>
	{{-- Product Price after inc.vat --}}
	<td>
		<input type="text" name="products[{{$row_count}}][unit_price_after_dis_inc]" class="form-control unit_price_after_dis_inc" readOnly value="{{@num_format($product->unit_price_inc_tax)}}">
	</td>
	<td @if($currency != "" && $currency != 0)  class="cur_check"  @else  class="cur_check hide"  @endif>
        <input type="text" name="products[{{$row_count}}][unit_price_after_dis_exc_new_currency]" readOnly class="form-control unit_price_after_dis_exc_new_currency" value="{{($currency != "" && $currency != 0)?round($product->unit_price/$currency,2):0}}">
    </td>

	<td class="{{$hide_tax}}">
	 
		<input type="hidden" name="products[{{$row_count}}][item_tax]" class="form-control item_tax">
		{!! Form::select("products[$row_count][tax_id]", [], null, ['placeholder' => 'Select', 'class' => 'form-control tax_id'] ); !!}
	</td>

	<td class="{{$hide_tax}}">
		<input type="text" name="products[{{$row_count}}][unit_price_inc_tax]" class="form-control pos_unit_price_inc_tax input_number" value="{{@num_format($unit_price_inc_tax)}}" readOnly>
	</td>
	
	<td>
		<input type="text" readonly name="products[{{$row_count}}][sub_total_price]" class="form-control pos_line_sub__total" value="{{$product->quantity_ordered*$product->unit_price_inc_tax }}" readOnly>
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

 