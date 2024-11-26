@foreach( $variations as $variation)
    <tr>
        <td>
            {{-- <i class="fas fa-sort pull-left handle cursor-pointer" title="@lang('lang_v1.sort_order')">
            </i>&nbsp; --}}
            
            <span class="sr_number"></span>
        </td>
        <td>
            <a  target="_blank" href="/item-move/{{$product->id}}">
                {{ $product->name }} ({{$variation->sub_sku}})
            </a>
            @if( $product->type == 'variable' )
                <br/>
                (<b>{{ $variation->product_variation->name }}</b> : {{ $variation->name }})
            @endif
            @php
            $qty_pro = \App\Models\WarehouseInfo::where("product_id",$variation->id)->sum("product_qty");
        @endphp 
            @if($product->enable_stock == 1)
                <br>
                <small class="text-muted" style="white-space: nowrap;">@lang('report.current_stock'): {{@num_format($qty_pro)}}  {{ $product->unit->short_name }}</small>
            @endif
            
        </td>
        <td>
            {!! Form::hidden('purchases[' . $row_count . '][product_id]', $product->id ); !!}
            {!! Form::hidden('purchases[' . $row_count . '][variation_id]', $variation->id , ['class' => 'hidden_variation_id']); !!}

            @php
                $check_decimal = 'false';
                if($product->unit->allow_decimal == 0){
                    $check_decimal = 'true';
                }
                $currency_precision = config('constants.currency_precision', 2);
                $quantity_precision = config('constants.quantity_precision', 2);
            @endphp
            {!! Form::text('purchases[' . $row_count . '][quantity]',  1, ["min"=>1,'class' => 'form-control input-sm purchase_quantity purchase_ input_number mousetrap', 'required', 'data-rule-abs_digit' => $check_decimal, 'data-msg-abs_digit' => __('lang_v1.decimal_value_not_allowed')]); !!}
            <input type="hidden" class="base_unit_cost" value="{{$variation->default_purchase_price}}">
            <input type="hidden" class="base_unit_selling_price" value="{{$variation->sell_price_inc_tax}}">

            <input type="hidden" name="purchases[{{$row_count}}][product_unit_id]" value="{{$product->unit->id}}">
            @if(!empty($sub_units))
                <br>
                <select name="purchases[{{$row_count}}][sub_unit_id]" class="form-control input-sm sub_unit">
                    @foreach($sub_units as $key => $value)
                        <option value="{{$key}}" data-multiplier="{{$value['multiplier']}}">
                            {{$value['name']}}
                        </option>
                    @endforeach
                </select>
            @else 
                {{ $product->unit->short_name }}
            @endif
        </td>
        <td>
            @if(isset($open))
              {!! Form::text('purchases[' . $row_count . '][pp_without_discount]',
                 $cost, [ ' class' => 'form-control input-sm  eb_price input_number ', 'required']); !!}
               
            @else
                  {!! Form::text('purchases[' . $row_count . '][pp_without_discount_s]',
                  (!empty($item_price))?$item_price->purchase_price:0, [ ' class' => 'form-control input-sm purchase_unit_cost_without_discount input_number ', 'required']); !!}
                    {!! Form::hidden('purchases[' . $row_count . '][pp_without_discount]',
                  (!empty($item_price))?$item_price->purchase_price:0, ['class' => 'form-control input-sm purchase_unit_cost_without_discount_s input_number', 'required']); !!}
            @endif
             <br>
             @if(app('request')->input('type_o') == 'store' || isset($open) )
             @else
                <?php $pr =  \App\Product::find($product->product_id);  ?>
                <textarea class="form-control" id="purchases[{{$row_count}}][purchase_note]" name="purchases[{{$row_count}}][purchase_note]" rows="{{$row_count}}"> {{ strip_tags($product->product_description) }}</textarea>
                <!--<p class="help-block"><small>@lang('lang_v1.sell_line_description_help')</small></p>-->
            @endif
         
      </td>
        <td>
            {!! Form::text('purchases[' . $row_count . '][pp_with_tax]',
            (!empty($item_price))?$item_price->purchase_price_inc_tax:0, ['class' => 'form-control input-sm purchase_unit_cost_with_tax input_number', 'required']); !!}
        </td>
        <td>
            {!! Form::text('purchases[' . $row_count . '][discount_percent]', 0, ['class' => 'form-control input-sm inline_discounts input_number', 'required']); !!}
        </td>
        <td>
            {!! Form::text('purchases[' . $row_count . '][purchase_price]',
            (!empty($item_price))?$item_price->purchase_price:0, ['class' => 'form-control input-sm purchase_unit_cost input_number' ,'readonly', 'required']); !!}
        </td>
        <td>
            {!! Form::text('purchases[' . $row_count . '][unit_cost_after_tax]',
            (!empty($item_price))?$item_price->purchase_price_inc_tax:0, ['class' => 'form-control input-sm total_unit_cost_with_tax  input_number' ,'readonly', 'required']); !!}
        </td>
        <td>
            {!! Form::text('purchases[' . $row_count . '][total_cost_after_tax]',
            (!empty($item_price))?$item_price->purchase_price_inc_tax:0, ['class' => 'form-control input-sm row_total_cost input_number','disabled', 'required']); !!}
        </td>
        <td class="{{$hide_tax}}">
            <span class="row_subtotal_before_tax display_currency">0</span>
            <input type="hidden" class="row_subtotal_before_tax_hidden" value=0>
        </td>
        <td class="{{$hide_tax}}">
            <div class="input-group">
                <select name="purchases[{{ $row_count }}][purchase_line_tax_id]" class="form-control select2 input-sm purchase_line_tax_id" placeholder="'Please Select'">
                    <option value="" data-tax_amount="0" @if( $hide_tax == 'hide' )
                    selected @endif >@lang('lang_v1.none')</option>
                    @foreach($taxes as $tax)
                        <option value="{{ $tax->id }}" data-tax_amount="{{ $tax->amount }}" @if( $product->tax == $tax->id && $hide_tax != 'hide') selected @endif >{{ $tax->name }}</option>
                    @endforeach
                </select>
                {!! Form::hidden('purchases[' . $row_count . '][item_tax]', 0, ['class' => 'purchase_product_unit_tax']); !!}
                <span class="input-group-addon purchase_product_unit_tax_text">
                    0.00</span>
            </div>
        </td>
        <td class="{{$hide_tax}}">
            
            @php
                $dpp_inc_tax = $variation->dpp_inc_tax;
                if($hide_tax == 'hide'){
                    $dpp_inc_tax = $variation->default_purchase_price;
                }

            @endphp
            {!! Form::text('purchases[' . $row_count . '][purchase_price_inc_tax]', $dpp_inc_tax, ['class' => 'form-control input-sm purchase_unit_cost_after_tax input_number', 'required']); !!}
        </td>
        @if (!isset($page_type))
            <td>
                <span class="row_subtotal_after_tax display_currency">0</span>
                <input type="hidden" class="row_subtotal_after_tax_hidden" value=0>
            </td>
        @endif
        
        {{-- <td class="@if(!session('business.enable_editing_product_from_purchase')) hide @endif">
            {!! Form::text('purchases[' . $row_count . '][profit_percent]', number_format($variation->profit_percent, $currency_precision, $currency_details->decimal_separator, $currency_details->thousand_separator), ['class' => 'form-control input-sm input_number profit_percent', 'required']); !!}
        </td> --}}
        @if (!isset($page_type))
        <td>
            @if(session('business.enable_editing_product_from_purchase'))
                {!! Form::text('purchases[' . $row_count . '][default_sell_price]', number_format($variation->sell_price_inc_tax, $currency_precision, $currency_details->decimal_separator, $currency_details->thousand_separator), ['class' => 'form-control input-sm input_number default_sell_price', 'required']); !!}
            @else
                {{ $variation->sell_price_inc_tax}}
            @endif
        </td>
        @endif
        @if(session('business.enable_lot_number'))
            <td>
                {!! Form::text('purchases[' . $row_count . '][lot_number]', null, ['class' => 'form-control input-sm']); !!}
            </td>
        @endif

        
        @if(session('business.enable_product_expiry'))
            <td style="text-align: left;">

                {{-- Maybe this condition for checkin expiry date need to be removed --}}
                @php
                    $expiry_period_type = !empty($product->expiry_period_type) ? $product->expiry_period_type : 'month';
                @endphp
                @if(!empty($expiry_period_type))
                <input type="hidden" class="row_product_expiry" value="{{ $product->expiry_period }}">
                <input type="hidden" class="row_product_expiry_type" value="{{ $expiry_period_type }}">

                @if(session('business.expiry_type') == 'add_manufacturing')
                    @php
                        $hide_mfg = false;
                    @endphp
                @else
                    @php
                        $hide_mfg = true;
                    @endphp
                @endif

                <b class="@if($hide_mfg) hide @endif"><small>@lang('product.mfg_date'):</small></b>
                <div class="input-group @if($hide_mfg) hide @endif">
                    <span class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                    </span>
                    {!! Form::text('purchases[' . $row_count . '][mfg_date]', null, ['class' => 'form-control input-sm expiry_datepicker mfg_date', 'readonly']); !!}
                </div>
                <b><small>@lang('product.exp_date'):</small></b>
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                    </span>
                    {!! Form::text('purchases[' . $row_count . '][exp_date]', null, ['class' => 'form-control input-sm expiry_datepicker exp_date', 'readonly']); !!}
                </div>
                @else
                <div class="text-center">
                    @lang('product.not_applicable')
                </div>
                @endif
            </td>
        @endif
       
        
        @if(app('request')->input('cost') == 'cost')
                <td>
                    {!! Form::text('purchases[' . $row_count . '][purchase_cost_price]', round($cost,2), ['class' => 'form-control input-sm purchase_cost_price input_number',"readonly" => "true" ]); !!}
                </td>
        @endif
        
        @if(app('request')->input('type_o') == 'store' || isset($open) )
       
            <td> {{ Form::select('stores_id['.$row_count.']',$childs,null,['class'=>'form-control',"placeholder" => __("messages.please_select")]) }} </td>
            
            @if($send != "received")
            <td>
                {{-- <input class="form-control input-sm row_total_cost_ input_number"  disabled required="" name="{{ 'purchases[' . $loop->index . '][total_cost_after_tax]' }}" type="text" 
                        value="{{$variation->default_purchase_price}}"> --}}
                        @php
                        $dpp_inc_tax = $variation->dpp_inc_tax;
                        if($hide_tax == 'hide'){
                            $dpp_inc_tax = $variation->default_purchase_price;
                        }
                        @endphp
                    @if(isset($open))
                        {!! Form::text('purchases[' . $row_count . '][purchase_price_inc_tax_]', $cost, ['class' => 'form-control input-sm purchase_unit_cost_after_tax_ input_number',"readonly" => "true"  , 'required']); !!}
                    @else
                        {!! Form::text('purchases[' . $row_count . '][purchase_price_inc_tax_]', $dpp_inc_tax, ['class' => 'form-control input-sm purchase_unit_cost_after_tax_ input_number',"readonly" => "true"  , 'required']); !!}
                    @endif
                </td>
                {{-- <td></td> --}}
                
            @endif 
        @endif
        <?php $row_count++ ;?>
        <td><i class="fa fa-times remove_purchase_entry_row text-danger" title="Remove" style="cursor:pointer;"></i></td>
    </tr>
@endforeach

<input type="hidden" id="row_count" value="{{ $row_count }}">
