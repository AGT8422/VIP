<tr class="product_row">
    <td>
        {{$product->product_name}}
        <br/>
        {{$product->sub_sku}}
        <br/>
        <?php   $cypher = Illuminate\Support\Facades\Crypt::encryptString($line->purchase_note);  ?>
        <div class="description_line" data-line="{{$row_index}}">
            <pre style="white-space: nowrap;max-width:300px;max-height:150px" data-line="{{$row_index}}" class="btn btn-modal products_details" data-href="{{action('ProductController@changeDescription', ['id'=>$product->product_id,'text'=> $cypher  ,'line'=>$row_index,'return'=>'return'])}}" data-container=".view_modal">{!! $line->purchase_note !!}</pre>
        </div>
        <textarea class="form-control control_products_details" data-line="{{$row_index}}" style="visibility:hidden" id="products[{{$row_index}}][purchase_note]" name="products[{{$row_index}}][purchase_note]" rows="{{$row_index}}"> {!! $line->purchase_note !!}</textarea>
       

    </td>
    @if(session('business.enable_lot_number'))
        <td>
            <input type="text" name="products[{{$row_index}}][lot_number]" class="form-control" value="{{$product->lot_number ?? ''}}">
        </td>
    @endif
    @php 
        
    @endphp
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
                $line = \App\PurchaseLine::find($product->purchase_line_id);
                $cost = \App\Product::product_cost($product->product_id);
                if($currency != "" && $currency != 0) {
                    $percent = (isset($line )&& ($line->pp_without_discount != 0))?($line->discount_percent*($line->pp_without_discount/$currency)/100):0;
                }else{
                    $percent = (isset($line )&& ($line->pp_without_discount != 0))?($line->discount_percent*$line->pp_without_discount/100):0;
                }
                $taxx = \App\TaxRate::where("id",$line->transaction->tax_id)->first();
                $tax  = ($taxx)?$taxx->amount:0;
                @endphp
        @else
            @php
                $qty = 1;
                $purchase_price = $product->last_purchased_price;
                $line = \App\PurchaseLine::find($product->purchase_line_id);
                $cost = \App\Product::product_cost($product->product_id);
                if($currency != "" && $currency != 0) {
                    $percent = (isset($line )&& ($line->pp_without_discount != 0))?($line->discount_percent*($line->pp_without_discount/$currency)/100):0;
                }else{
                    $percent = (isset($line )&& ($line->pp_without_discount != 0))?($line->discount_percent*$line->pp_without_discount/100):0;
                }
                $taxx = \App\TaxRate::where("id",$line->transaction->tax_id)->first();
                $tax  = ($taxx)?$taxx->amount:0;
            @endphp
        @endif

        @php 
            
            $pro        = \App\Product::find($product->product_id); 
            $var        = ($pro->variations)?$pro->variations->first()->default_purchase_price:0;
        
            $allUnits       = [];
            $business   = \App\Business::find($pro->business_id);
            $allUnits[$pro->unit_id] = [
                'name' => $pro->unit->actual_name,
                'multiplier' => $pro->unit->base_unit_multiplier,
                'allow_decimal' => $pro->unit->allow_decimal,
                'price' => $var,
                'check_price' => 0,
                ];
              
            $productUtil    = new \App\Utils\ProductUtil();
            // $sub_units      = $productUtil->getSubUnits($pro->business_id, $product->unit_id, false, $product->product_id);
            // foreach($sub_units as $k => $lines){
            //     $allUnits[$k] =  $lines; 
            // }
            if($pro->sub_unit_ids != null){
                foreach($pro->sub_unit_ids  as $i){
                        $row_price    =  0;
                        $un           = \App\Unit::find($i);
                        $row_price    = \App\Models\ProductPrice::where("unit_id",$i)->where("product_id",$product->product_id)->where("number_of_default",0)->first();
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
            $count = 0; 
            $list_of_prices_in_unit = \App\Product::getProductPrices($product->product_id);
          
        @endphp

        <input type="text" class="form-control product_quantity input_number input_quantity" value="{{@format_quantity($qty)}}" name="products[{{$row_index}}][quantity]" 
        @if($product->unit_allow_decimal == 1) data-decimal=1 @else data-rule-abs_digit="true" data-msg-abs_digit="@lang('lang_v1.decimal_value_not_allowed')" data-decimal=0 @endif
        data-rule-required="true" data-msg-required="@lang('validation.custom-messages.this_field_is_required')" @if($product->enable_stock) data-rule-max-value="{{$product->qty_available}}زز" data-msg-max-value="@lang('validation.custom-messages.quantity_not_available', ['qty'=> $product->formatted_qty_available, 'unit' => $product->unit  ])"
        data-qty_available="{{$product->qty_available}}" 
        data-msg_max_default="@lang('validation.custom-messages.quantity_not_available1', ['qty'=> $product->formatted_qty_available, 'unit' => $product->unit  ])"
         @endif >
         <br>
         
         <select name="products[{{$row_index}}][sub_unit_id]" class="form-control input-sm sub_unit" required>
             @foreach($sub_units  as $sub_units_key => $sub_units_value)
                {{-- @if(isset($list_of_prices_in_unit[$sub_units_key])) --}}
                    <option value="{{$sub_units_key}}"  data-price="{{$sub_units_value['price']}}" data-check_price="{{$sub_units_value['check_price']}}"
                    data-multiplier="{{$sub_units_value['multiplier']}}"
                            @if($sub_units_key == $product->sub_unit_id) @php $count = $product->sub_unit_id; @endphp selected @endif>
                            {{$sub_units_value['name']}}
                        </option>
                {{-- @endif --}}
            @endforeach
        </select>


         
    </td>
    
  
    {{-- Product Price before exc.vat --}}
    <td>
        <input type="text" data-min="1" name="products[{{$row_index}}][unit_price_before_dis_exc]" class="form-control unit_price_before_dis_exc input_number mousetrap" value="{{round($line->pp_without_discount,config('constants.currency_precision'))}}">
        <br>
        <b><small>@lang('List Of Price'):</small></b>
        <div class="form-group">
          
            <select data-prices="{{json_encode($list_of_prices_in_unit)}}"  name="products[{{ $row_index }}][list_price]" class="form-control select2 list_price" placeholder="'Please Select'">
                <option value="" data-price="" @if($product->list_price == null) selected @endif >@lang('lang_v1.none')</option>
                @foreach($list_of_prices_in_unit as $key => $row_line)
                    {{-- @if($key == $count) --}}
                        @foreach ($row_line as $item)
                            <option value="{{$item['line_id']}}" @if($product->list_price == $item['line_id']) selected @endif data-price="{{$item['price']}}" data-value="{{$item['line_id']}}" >{{$item["name"]}}</option>
                        @endforeach
                    {{-- @endif --}}
                @endforeach
            </select>
        </div>
    </td>
    {{-- Product Price before inc.vat --}}
    <td>
        <input type="text" data-min="1" name="products[{{$row_index}}][unit_price_before_dis_inc]" class="form-control unit_price_before_dis_inc" value="{{round((($line->pp_without_discount*$tax)/100)+$line->pp_without_discount,config('constants.currency_precision'))}}">
    </td>
    <td @if($currency != "" && $currency != 0)  class="cur_check"  @else  class="cur_check hide"  @endif>
        <input type="text" name="products[{{$row_index}}][unit_price_before_dis_exc_new_currency]" class="form-control unit_price_before_dis_exc_new_currency" value="{{round((($currency != "" && $currency != 0)?$line->pp_without_discount/DoubleVal($currency):0),config('constants.currency_precision'))}}">
    </td >
    {{-- Product Fixed Discount Price --}}
    <td>
        <input type="text" data-min="1" name="products[{{$row_index}}][discount_amount_return]" class="form-control discount_amount_return" value="{{round($percent,config('constants.currency_precision'))}}">
    </td>
    {{-- Product Percentage Discount Price --}}
    <td>
        <input type="text" data-min="1" name="products[{{$row_index}}][discount_percent_return]" class="form-control discount_percent_return" value="{{round($line->discount_percent,config('constants.currency_precision'))}}">
    </td>
    {{-- Product Price after inc.vat --}}
    <td>
        <input type="text" data-min="1" name="products[{{$row_index}}][unit_price_after_dis_exc]" readOnly class="form-control unit_price_after_dis_exc" value="{{round($line->purchase_price,config('constants.currency_precision'))}}">
    </td>
    {{-- Product Price after inc.vat --}}
    <td> 
        <input type="text" data-min="1" name="products[{{$row_index}}][unit_price_after_dis_inc]" readOnly class="form-control unit_price_after_dis_inc" value="{{round($line->purchase_price_inc_tax,config('constants.currency_precision'))}}">
    </td>
    <td @if($currency != "" && $currency != 0)  class="cur_check"  @else  class="cur_check hide"  @endif>
        <input type="text" name="products[{{$row_index}}][unit_price_after_dis_exc_new_currency]" readOnly class="form-control unit_price_after_dis_exc_new_currency" value="{{round((($currency != "" && $currency != 0)?$line->purchase_price/$currency:0),config('constants.currency_precision'))}}">
    </td>
    @if(session('business.enable_product_expiry'))
        <td>
            <input type="text" name="products[{{$row_index}}][exp_date]" class="form-control expiry_datepicker" value="@if(!empty($product->exp_date)){{@format_date($product->exp_date)}}@endif" readonly>
        </td>
    @endif
    <td>
        <input type="text" data-min="1" readonly name="products[{{$row_index}}][sub_total_price]" readOnly class="form-control pos_line_sub__total" value="{{round($qty*$line->purchase_price_inc_tax,config('constants.currency_precision'))}}">
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