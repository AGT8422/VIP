

<tr class="product_row line_sorting">
    <td>
        <input type="text" class="line_sort" hidden name="line_sort[{{$row_index}}]" value="">    
    
        <span class="line_ordered">{{$row_index+1}}</span>
        <br>
        <i class="fas fa-sort pull-left handle cursor-pointer" title="@lang('lang_v1.sort_order')"></i>
        @php
            $qty_avialable = \App\Models\WarehouseInfo::where("product_id",$product->product_id)->where("store_id",$store)->sum("product_qty");
            if($store_in != $store && $status == "completed"){
                $qty_avialable_format = number_format($qty_avialable+$product->quantity_ordered,2);
            }else{
                $qty_avialable_format = number_format($qty_avialable,2);
            }

        @endphp
        {{$product->name}}
        <br/>
        {{$product->sub_sku}}

            @if( session()->get('business.enable_lot_number') == 1 || session()->get('business.enable_product_expiry') == 1)
            @php
                $lot_enabled = session()->get('business.enable_lot_number');
                $exp_enabled = session()->get('business.enable_product_expiry');
                $lot_no_line_id = '';
                if(!empty($product->lot_no_line_id)){
                    $lot_no_line_id = $product->lot_no_line_id;
                }
            @endphp
            @if(!empty($product->lot_numbers))
                <select class="form-control lot_number" name="products[{{$row_index}}][lot_no_line_id]">
                    <option value="">@lang('lang_v1.lot_n_expiry')</option>
                    @foreach($product->lot_numbers as $key => $lot_number)
                        @php
                            $selected = "";
                            if($lot_number->purchase_line_id == $lot_no_line_id){
                                $selected = "selected";

                                $max_qty_rule = $lot_number->qty_available;
                                $max_qty_msg = __('lang_v1.quantity_error_msg_in_lot', ['qty'=> $lot_number->qty_formated, 'unit' => $product->unit  ]);
                            }

                            $expiry_text = '';
                            if($exp_enabled == 1 && !empty($lot_number->exp_date)){
                                if( \Carbon::now()->gt(\Carbon::createFromFormat('Y-m-d', $lot_number->exp_date)) ){
                                    $expiry_text = '(' . __('report.expired') . ')';
                                }
                            }
                        @endphp
                        <option value="{{$lot_number->purchase_line_id}}" data-qty_available="{{$lot_number->qty_available}}" data-msg-max="@lang('lang_v1.quantity_error_msg_in_lot', ['qty'=> $lot_number->qty_formated, 'unit' => $product->unit  ])" {{$selected}}>@if(!empty($lot_number->lot_number) && $lot_enabled == 1){{$lot_number->lot_number}} @endif @if($lot_enabled == 1 && $exp_enabled == 1) - @endif @if($exp_enabled == 1 && !empty($lot_number->exp_date)) @lang('product.exp_date'): {{@format_date($lot_number->exp_date)}} @endif {{$expiry_text}}</option>
                        @endforeach
                    </select>
                    @endif
                    @endif
                </td>
                <td>
                    {{-- If edit then transaction sell lines will be present --}}
         
        @if(!empty($product->transaction_sell_lines_id))
        <input type="hidden" name="products[{{$row_index}}][transaction_sell_lines_id]" class="form-control" value="{{$product->transaction_sell_lines_id}}">
        <input type="hidden" name="products[{{$row_index}}][number]" class="form-control" value="{{$counts[$row_index]}}">
        @endif

        <input type="hidden" name="products[{{$row_index}}][product_id]" class="form-control product_id" value="{{$product->product_id}}">

        <input type="hidden" value="{{$product->variation_id}}" 
            name="products[{{$row_index}}][variation_id]">

        
        
            <input type="hidden" value="{{$product->enable_stock}}" 
            name="products[{{$row_index}}][enable_stock]">
        
        @if(empty($product->quantity_ordered))
            @php
                $product->quantity_ordered = 1;
            @endphp
        @endif

        <input type="text" class="form-control product_quantity input_number input_quantity" value="{{@format_quantity($product->quantity_ordered)}}" name="products[{{$row_index}}][quantity]" 
        @if($product->unit_allow_decimal == 1) data-decimal=1 @else data-decimal=0 data-rule-abs_digit="true" data-msg-abs_digit="@lang('lang_v1.decimal_value_not_allowed')" @endif
        data-rule-required="true" data-msg-required="@lang('validation.custom-messages.this_field_is_required')" @if($product->enable_stock) data-rule-max-value="{{$qty_avialable+$product->quantity_ordered}}" data-msg-max-value="@lang('validation.custom-messages.quantity_not_available', ['qty'=> $qty_avialable_format, 'unit' => $product->unit  ])" @endif >
        {{$product->unit}}
        <input type="hidden" name="products[{{$row_index}}][unit_price]" class="form-control product_unit_price input_number" value="{{@num_format($product->last_purchased_price)}}">
    </td>
    <td>
        {{-- <input type="text" name="products[{{$row_index}}][unit_price]" class="form-control product_unit_price input_number" value="{{@num_format($product->last_purchased_price)}}"> --}}
        @lang("lang_v1.available_qty") :  {{$qty_avialable_format}} 
        <input type="text" readonly name="products[{{$row_index}}][price]" class=" hide form-control product_line_total" value="{{@num_format($qty_avialable_format+$product->quantity_ordered)}}">
    </td>
        {{-- <input type="text" readonly name="products[{{$row_index}}][price]" class="form-control product_line_total" value="{{@num_format($product->quantity_ordered)}}"> --}}
    
    <td class="text-center">
        <i class="fa fa-trash remove_product_row cursor-pointer" aria-hidden="true"></i>
    </td>
</tr>
 