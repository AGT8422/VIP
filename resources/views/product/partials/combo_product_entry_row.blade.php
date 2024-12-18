@php
	$quantity    = isset($quantity) ? $quantity: 1;
	$multiplier  = isset($multiplier) ? $multiplier: 1;
	$unit_id     = isset($unit_id) ? $unit_id: null;
	$list_price  = isset($list_price) ? $list_price: null;
@endphp

@foreach($variations as $variation)
	<tr>
		<td class="text-center">
			@if($product->type == 'variable')
				{{ $product->name }} ( {{ $variation->name }} ) - {{ $variation->sub_sku }}
			@else
				{{ $product->name }} - {{ $variation->sub_sku }}
			@endif
			<input type="hidden" name="composition_variation_id[]" value="{{ $variation->id }}">
		</td>
		@php $count = 0; @endphp
		<td class="text-center">
			{!! Form::text('quantity[]', @num_format($quantity), ['class' => 'form-control col-sm-12 input-sm quantity input_number mousetrap', 'required', 'style '=> "width: 77px"]); !!}
			@if(!empty($sub_units))
                <br>
                <select name="unit[]" 
                	class="form-control input-sm sub_unit">
                    @foreach($sub_units as $key => $value)
						@php $count = $key; @endphp
                        <option value="{{$key}}" 
								data-price="{{$value['price']}}"
								data-check_price="{{$value['check_price']}}"
								data-multiplier="{{$value['multiplier']}}"
								@if($unit_id == $key) selected @endif
									>
                            {{$value['name']}}
                        </option>
                    @endforeach
                </select>
            @else 
            	<input type="hidden" name="unit[]" value="{{$product->unit->id}}">
                {{ $product->unit->short_name }}
            @endif

		</td>
		<td class="text-center">
			 	@php $row_price_line = $variation->default_purchase_price;  @endphp 
				@foreach($list_of_prices_in_unit as $key => $row_line)
					@if($key == $unit_id)
						@foreach ($row_line as $ind => $item)
							 @if($list_price == $ind) 
							  @php
									$row_price_line = $item['price'];
							  @endphp
							 @endif  
						@endforeach
					@endif
				@endforeach
			 
			<span class="purchase_price display_currency purchase_price_text" data-currency_symbol="true">
				{{ $row_price_line }}
			</span>
			<br>
			<b><small>@lang('List Of Price'):</small></b>
			<div class="form-group">
				<select data-prices="{{json_encode($list_of_prices_in_unit)}}"  name="list_price[]" class="form-control select2 list_price" placeholder="'Please Select'">
					<option value="" data-price="" >@lang('lang_v1.none')</option>
					@foreach($list_of_prices_in_unit as $key => $row_line)
						@if($key == $unit_id || $key == $count)
							@foreach ($row_line as $ind => $item)
								<option value="{{$item['line_id']}}"  @if($list_price == $ind) selected @endif  data-price="{{$item['price']}}" data-value="{{$item['line_id']}}" >{{$item["name"]}}</option>
							@endforeach
						@endif
					@endforeach
				</select>
			</div>
			<input type="hidden" class="purchase_price" value="{{ $row_price_line }}">
		</td>
		<td class="text-center">
			<span class="item_level_purchase_price display_currency" data-currency_symbol="true">
				{{$row_price_line * $quantity }}
			</span>
			<input type="hidden" class="item_level_purchase_price" value="{{$row_price_line * $quantity }}">
		</td>
		<td class="text-center">
			<span>
				<i class="fa fa-times remove_combo_product_entry_row text-danger" title="Remove" style="cursor:pointer;"></i>
			</span>
		</td>
	</tr>
@endforeach