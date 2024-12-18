<tr class="product_row">
    <td>
        {{$product->product_name}}
        <br/>
        {{$product->sub_sku}}
    </td>
    @if(session('business.enable_lot_number'))
        <td>
            <input type="text" name="products[{{$row_index}}][lot_number]" class="form-control" value="{{$product->lot_number ?? ''}}">
        </td>
    @endif
    
    <td>
        <input type="hidden" name="products[{{$row_index}}][product_id]" class="form-control product_id" value="{{$product->product_id}}">

        <input type="hidden" value="{{$product->variation_id}}" 
            name="products[{{$row_index}}][variation_id]">

        <input type="hidden" value="{{$product->enable_stock}}" 
            name="products[{{$row_index}}][enable_stock]">

        @if(!empty($edit))
            <input type="hidden" value="{{$product->purchase_line_id}}" 
            name="products[{{$row_index}}][purchase_line_id]">
            @php
                $qty = $product->quantity_returned;
                $purchase_price = $product->purchase_price;
            @endphp
        @else
            @php
                $qty = 1;
                $purchase_price = $product->last_purchased_price;
                $cost = \App\Product::product_cost($product->product_id);
            @endphp
        @endif
        @php 
        
            $var     = ($product->default_sell_price)?$product->default_sell_price:0;
           
            $allUnits       = [];
            $business   = \App\Business::find($product->business_id);
            $allUnits[$product->unit_id] = [
                'name' => $product->actual_name,
                'multiplier' => $product->base_unit_multiplier,
                'allow_decimal' => $product->allow_decimal,
                'price' => $var,
                'check_price' => 0,
                ];
            $productUtil    = new \App\Utils\ProductUtil();
            $sub_units      = $productUtil->getSubUnits($product->business_id, $product->unit_id, false, $product->product_id);
            foreach($sub_units as $k => $line){
                $allUnits[$k] =  $line; 
            }
            $sub_units = $allUnits  ;
            $count = 0;
            $list_of_prices_in_unit = \App\Product::getProductPrices($product->product_id);
        @endphp
            <input type="text" class="form-control product_quantity input_number input_quantity" value="{{@format_quantity($qty)}}" name="products[{{$row_index}}][quantity]" 
            @if($product->unit_allow_decimal == 1) data-decimal=1 @else data-rule-abs_digit="true" data-msg-abs_digit="@lang('lang_v1.decimal_value_not_allowed')" data-decimal=0 @endif
            data-rule-required="true" data-msg-required="@lang('validation.custom-messages.this_field_is_required')" @if($product->enable_stock) data-rule-max-value="{{$product->qty_available}}" data-msg-max-value="@lang('validation.custom-messages.quantity_not_available', ['qty'=> $product->formatted_qty_available, 'unit' => $product->unit  ])"
            data-qty_available="{{$product->qty_available}}" 
            data-msg_max_default="@lang('validation.custom-messages.quantity_not_available1', ['qty'=> $product->formatted_qty_available, 'unit' => $product->unit  ])"
            @endif >
            <br>
            <select name="products[{{$row_index}}][sub_unit_id]" class="form-control input-sm sub_unit">
                @foreach($sub_units as $key => $value)
                    <option value="{{$key}}" @if($count == 0) selected  @php $count = $key;  @endphp @endif data-multiplier="{{$value['multiplier']}}" data-price="{{$value['price']}}" data-check_price="{{$value['check_price']}}">
                        {{$value['name']}}
                    </option>
                    
                @endforeach
            </select>
        {{-- {{$product->unit}} --}}
    </td>

  
    {{-- Product Price before exc.vat --}}
    <td>
        <input type="text" name="products[{{$row_index}}][unit_price_before_dis_exc]" class="form-control unit_price_before_dis_exc input_number mousetrap" value="{{$cost}}">
        <br>
        <b><small>@lang('List Of Price'):</small></b>
        <div class="form-group">
            <select data-prices="{{json_encode($list_of_prices_in_unit)}}"  name="products[{{ $row_index }}][list_price]" class="form-control select2 list_price" placeholder="'Please Select'">
                <option value="" data-price="" selected>@lang('lang_v1.none')</option>
                @foreach($list_of_prices_in_unit as $key => $row_line)
                    @if($key == $count)
                        @foreach ($row_line as $item)
                            @php $pr = ($item['price']!=null)?$item['price']:0; @endphp
                            <option    value="{{$item['line_id']}}" data-price="{{$pr}}" data-value="{{$item['line_id']}}" >{{$item["name"]}}</option>
                        @endforeach
                    @endif
                @endforeach
            </select>
        </div>
    </td>
    {{-- Product Price before inc.vat --}}
    <td>
        <input type="text" name="products[{{$row_index}}][unit_price_before_dis_inc]" class="form-control unit_price_before_dis_inc" value="{{$cost}}">
    </td>
    {{-- Product Price before exc.vat new currency --}}
    {{-- @php dd($cost/DoubleVal($currency)); @endphp --}}
    <td @if($currency != "" && $currency != 0)  class="cur_check"  @else  class="cur_check hide"  @endif>
        <input type="text" name="products[{{$row_index}}][unit_price_before_dis_exc_new_currency]" class="form-control unit_price_before_dis_exc_new_currency" value="{{($currency != "" && $currency != 0)?$cost/DoubleVal($currency):0}}">
    </td >
    {{-- Product Fixed Discount Price --}}
    <td>
        <input type="text" name="products[{{$row_index}}][discount_amount_return]" class="form-control discount_amount_return" value="{{0}}">
    </td>
    {{-- Product Percentage Discount Price --}}
    <td>
        <input type="text" name="products[{{$row_index}}][discount_percent_return]" class="form-control discount_percent_return" value="{{0}}">
    </td>
    {{-- Product Price after inc.vat --}}
    <td>
        <input type="text" name="products[{{$row_index}}][unit_price_after_dis_exc]" readOnly class="form-control unit_price_after_dis_exc" value="{{$cost}}">
    </td>
    {{-- Product Price after inc.vat --}}
    <td>
        <input type="text" name="products[{{$row_index}}][unit_price_after_dis_inc]" readOnly class="form-control unit_price_after_dis_inc" value="{{$cost}}">
    </td>
    {{-- Product Price after exc.vat new currency --}}
    <td @if($currency != "" && $currency != 0)  class="cur_check"  @else  class="cur_check hide"  @endif>
        <input type="text" name="products[{{$row_index}}][unit_price_after_dis_exc_new_currency]" readOnly class="form-control unit_price_after_dis_exc_new_currency" value="{{($currency != "" && $currency != 0)?$cost/$currency:0}}">
    </td>
    @if(session('business.enable_product_expiry'))
        <td>
            <input type="text" name="products[{{$row_index}}][exp_date]" class="form-control expiry_datepicker" value="@if(!empty($product->exp_date)){{@format_date($product->exp_date)}}@endif" readonly>
        </td>
    @endif
    <td>
        <input type="text" readonly name="products[{{$row_index}}][sub_total_price]" readOnly class="form-control pos_line_sub__total" value="{{$qty*$cost}}">
    </td>
    {{-- @if (isset($stores))
        <td>
            {{ Form::select('store_id[]',$stores,null,['class'=>'form-control']) }}
        </td>
    @endif --}}
    <td class="text-center">
        <i class="fa fa-trash remove_product_row cursor-pointer" aria-hidden="true"></i>
    </td>
</tr>