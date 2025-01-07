@foreach( $variations as $variation)
    <tr class="line_sorting">
        @if(isset($check_cost) && $check_cost == 1)
            @if(isset($global_price))
                    @if(  $global_price == "0" )
                        @php 
                            $cost = $variation->default_purchase_price;
                            
                        @endphp
                    @else
                        @php 
                            $pr   = \App\Models\ProductPrice::where("number_of_default",$global_price)->where("product_id",$product->id)->first();
                            $cost = ($pr)?$pr->default_purchase_price:0;
                        @endphp
                    @endif
            @endif
        @endif
        <td class="text-center">
            <input type="text" class="line_sort" hidden name="line_sort[{{$row_count}}]" value="">    

            <span class="sr_number line_ordered">{{$row_count+1}}</span>
            <br>
             <i class="fas fa-sort pull-left handle cursor-pointer" title="@lang('lang_v1.sort_order')"></i>
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
                <small class="text-muted" style="white-space: nowrap;">@lang('report.current_stock'):  {{@num_format($qty_pro)}}   {{ $product->unit->short_name }}</small>
            @endif
            <?php $pr =  \App\Product::find($product->product_id); $txd = ($product->product_description != null || $product->product_description != "")?$product->product_description:"Enter Description";  $cypher = ($product->product_description != null || $product->product_description != "")?Illuminate\Support\Facades\Crypt::encryptString($product->product_description):Illuminate\Support\Facades\Crypt::encryptString('.');  ?>
            <div class="description_line" data-line="{{$row_count}}">
                <pre style="white-space: nowrap;max-width:300px;max-height:150px" class="btn btn-modal products_details" data-href="{{action('ProductController@changeDescription', ['id'=>$product->id,'text'=> $cypher  ,'line'=>$row_count])}}" data-container=".view_modal">{!! $txd !!}</pre>
            </div>
            <textarea class="form-control control_products_details" data-line="{{$row_count}}" style="visibility:hidden" id="purchases[{{$row_count}}][purchase_note]" name="purchases[{{$row_count}}][purchase_note]" rows="{{$row_count}}"> {!! $product->product_description !!}</textarea>
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
            @if(isset($reamain))
            {!! Form::text('purchases[' . $row_count . '][quantity]',  ($reamain!=null)?$reamain:1, ["min"=>1,'class' => 'form-control input-sm purchase_quantity purchase_ input_number mousetrap', 'required', 'data-rule-abs_digit' => $check_decimal, 'data-msg-abs_digit' => __('lang_v1.decimal_value_not_allowed')]); !!}
            @else
            {!! Form::text('purchases[' . $row_count . '][quantity]',  1, ["min"=>1,'class' => 'form-control input-sm purchase_quantity purchase_ input_number mousetrap', 'required', 'data-rule-abs_digit' => $check_decimal, 'data-msg-abs_digit' => __('lang_v1.decimal_value_not_allowed')]); !!}
            @endif
            <input type="hidden" class="base_unit_cost" value="{{$variation->default_purchase_price}}">
            <input type="hidden" class="base_unit_selling_price" value="{{$variation->sell_price_inc_tax}}">
            @php $count = 0;  @endphp
            <input type="hidden" name="purchases[{{$row_count}}][product_unit_id]" value="{{$product->unit->id}}">
            @if(!empty($sub_units))
                <br>
                @php $io = 0; @endphp
                <select name="purchases[{{$row_count}}][sub_unit_id]" class="form-control input-sm sub_unit">
                    @foreach($sub_units as $key => $value)
                        @php 
                            $io++;      
                            if($product->type == "variable"){
                                $price_from_table = \App\Variation::find($variation->id);
                                $row_price        = \App\Models\ProductPrice::where("unit_id",$key)   
                                                                            ->where("product_id",$product->id)
                                                                            ->where("number_of_default",0)
                                                                            ->where("variations_value_id",$price_from_table->variation_value_id)
                                                                            ->where("variations_template_id",$price_from_table->product_variation->variation_template_id)
                                                                            ->where("ks_line",$io)
                                                                            ->first(); 
                               
                                $pr               = ($row_price)?$row_price->default_purchase_price:0; 
                            }else{
                                $pr = $value['price'];
                            }
                        @endphp
                        <option value="{{$key}}" @if($count == 0) selected  @php $count = $key;  @endphp @endif data-multiplier="{{$value['multiplier']}}" data-price="{{$pr}}" data-check_price="{{$value['check_price']}}">
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
                 round($cost,config('constants.currency_precision')), [ ' class' => 'form-control input-sm  eb_price input_number ', 'required']); !!}
              {!! Form::text('purchases[' . $row_count . '][purchase_unit_cost_without_discount_origin]',
               $cost, ['class' => 'form-control input-sm purchase_unit_cost_without_discount_origin input_number', 'required']); !!}
            @else
                  {!! Form::text('purchases[' . $row_count . '][pp_without_discount_s]',
                  (!empty($item_price))?round($item_price->purchase_price, config('constants.currency_precision')):round($cost, config('constants.currency_precision')), [ 'class' => 'form-control input-sm purchase_unit_cost_without_discount input_number ', 'required']); !!}
                    {!! Form::text('purchases[' . $row_count . '][purchase_unit_cost_without_discount_origin]',
                  (!empty($item_price))?$item_price->purchase_price:$cost, ['class' => 'form-control input-sm purchase_unit_cost_without_discount_origin input_number', 'required']); !!}
                    {!! Form::hidden('purchases[' . $row_count . '][pp_without_discount]',
                  (!empty($item_price))?round($item_price->purchase_price, config('constants.currency_precision')):round($cost, config('constants.currency_precision')), ['class' => 'form-control input-sm purchase_unit_cost_without_discount_s input_number', 'required']); !!}
            @endif
             <br>
             
            
            <b><small>@lang('List Of Price'):</small></b>
            <div class="form-group">
                @if($variation->variation_value_id != null)
                    @php 
                        $var_value_id    =   $variation->variation_value_id;
                        $var_template_id =   $variation->product_variation->variation_template_id;
                        $list_variations =   [
                            'variation_value_id'    => $var_value_id,
                            'variation_template_id' => $var_template_id
                        ];
                        $list_of_prices_in_unit = \App\Product::getProductPrices($product->id,$list_variations);
                    @endphp 
                @endif
                <select data-prices="{{json_encode($list_of_prices_in_unit)}}"  name="purchases[{{ $row_count }}][list_price]" class="form-control select2 list_price" placeholder="'Please Select'">
                    <option value="" data-price="" selected>@lang('lang_v1.none')</option>
                    @foreach($list_of_prices_in_unit as $key => $row_line)
                        @if($key == $count)
                            @foreach ($row_line as $item)
                                @php $price_line = ($item['price'])??0; @endphp
                                <option value="{{$item['line_id']}}" data-price="{{$price_line}}" data-value="{{$item['line_id']}}" >{{$item["name"] ." ( " .$price_line. " ) "}}</option>
                            @endforeach
                        @endif
                    @endforeach
                </select>
            </div>
             @if(app('request')->input('type_o') == 'store' || isset($open) )
             @else
                <?php $pr =  \App\Product::find($product->product_id); $txd = ($product->product_description != null || $product->product_description != "")?$product->product_description:"Enter Description";  $cypher = ($product->product_description != null || $product->product_description != "")?Illuminate\Support\Facades\Crypt::encryptString($product->product_description):Illuminate\Support\Facades\Crypt::encryptString('.');  ?>
                {{-- <div class="description_line" data-line="{{$row_count}}">
                    <pre style="white-space: nowrap;max-width:300px;max-height:150px" class="btn btn-modal products_details" data-href="{{action('ProductController@changeDescription', ['id'=>$product->id,'text'=> $cypher  ,'line'=>$row_count])}}" data-container=".view_modal">{!! $txd !!}</pre>
                </div>
                <textarea class="form-control control_products_details" data-line="{{$row_count}}" style="visibility:hidden" id="purchases[{{$row_count}}][purchase_note]" name="purchases[{{$row_count}}][purchase_note]" rows="{{$row_count}}"> {!! $product->product_description !!}</textarea>
                --}}
            @endif 
      </td>
   
        <td>
            {!! Form::text('purchases[' . $row_count . '][pp_with_tax]',
            (!empty($item_price))?$item_price->purchase_price_inc_tax:$cost, ['class' => 'form-control input-sm purchase_unit_cost_with_tax input_number', 'required']); !!}
        </td>
        @php
            $price_final     = (!empty($item_price))?$item_price->purchase_price:$cost;
            $price_tax_final = (!empty($item_price))?$item_price->purchase_price_inc_tax:$cost;
        @endphp
        <td @if($currency != "" && $currency != 0)  class="cur_check"  @else  class="cur_check hide"  @endif>
            {!! Form::text('purchases[' . $row_count . '][pp_new_currency]',
             ($currency != "" && $currency != 0)?$price_final/$currency:0, ['class' => ' curr_column form-control input-sm purchase_unit_cost_new_currency input_number',($currency == "" || $currency == 0)?'disabled':'', 'required']); !!}
        </td>
        <td class=" hide">
            {!! Form::hidden('purchases[' . $row_count . '][pp_with_tax_new_currency]',
             ($currency != "" && $currency != 0)?$price_tax_final/$currency:0, ['class' => ' curr_column form-control input-sm purchase_unit_cost_with_tax_new_currency input_number',($currency == "" || $currency == 0)?'disabled':'', 'required']); !!}
        </td>
        <td>
            {!! Form::text('purchases[' . $row_count . '][discount_percent]', 0, ['class' => 'form-control input-sm inline_discounts input_number', 'required']); !!}
        </td>
        @php
            $price_final_dis = (!empty($item_price))?$item_price->purchase_price:$cost;
            $price_tax_final_dis = (!empty($item_price))?$item_price->purchase_price_inc_tax:$cost;
            
        @endphp
        <td>
            {!! Form::text('purchases[' . $row_count . '][purchase_price]',
            (!empty($item_price))?$item_price->purchase_price:$cost, ['class' => 'form-control input-sm purchase_unit_cost input_number' ,'readonly', 'required']); !!}
        </td>
        <td>
            {!! Form::text('purchases[' . $row_count . '][unit_cost_after_tax]',
            (!empty($item_price))?$item_price->purchase_price_inc_tax:$cost, ['class' => 'form-control input-sm total_unit_cost_with_tax  input_number' ,'readonly', 'required']); !!}
        </td>
        <td @if($currency != "" && $currency != 0)  class="cur_check"  @else  class="cur_check hide"  @endif>
            {!! Form::text('purchases[' . $row_count . '][total_cost_dis_new_currency]',
            ($currency != "" && $currency != 0)?$price_final_dis/$currency:0, ['class' => 'curr_column form-control input-sm total_cost_dis_new_currency  input_number' ,'readonly', 'required']); !!}
        </td>
        <td class="  hide">
            {!! Form::hidden('purchases[' . $row_count . '][total_unit_cost_with_tax_new_currency]',
             ($currency != "" && $currency != 0)?$price_tax_final_dis/$currency:0, ['class' => 'curr_column form-control input-sm total_unit_cost_with_tax_new_currency  input_number' ,'readonly', 'required']); !!}
        </td>
   
        <td @if(isset($open) ||  $type_received != null)  class="hide"   @endif>
            {!! Form::text('purchases[' . $row_count . '][total_cost_after_tax]',
            (!empty($item_price))?$item_price->purchase_price_inc_tax:$cost, ['class' => 'form-control input-sm row_total_cost input_number','disabled', 'required']); !!}
        </td>
        <td  @if($currency != "" && $currency != 0)  class="cur_check"  @else  class="cur_check hide"  @endif>
            {!! Form::text('purchases[' . $row_count . '][row_total_cost_new_currency]',
             ($currency != "" && $currency != 0)?$price_tax_final_dis/$currency:0, ['class' => 'form-control input-sm row_total_cost_new_currency input_number','disabled', 'required']); !!}
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

        {{-- @if(isset($open)) --}}
            @if(session('business.enable_product_expiry'))
                <td class="expire" style="text-align: left;">

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
        {{-- @endif --}}
       
        
        @if(app('request')->input('cost') == 'cost')
                <td>
                    {!! Form::text('purchases[' . $row_count . '][purchase_cost_price]', round($cost,2), ['class' => 'form-control input-sm purchase_cost_price input_number',"readonly" => "true" ]); !!}
                </td>
        @endif
        
        @if(app('request')->input('type_o') == 'store' || isset($open) )
                @if(request()->input("return_type") == "return_type") 
                    @php $childs = \App\Models\Warehouse::product_stores($product->id); @endphp
                    <select name="stores_id[{{$row_count}}]"  class="form-control"  required>
                        @foreach($childs as $key=>$value)
                             <option value="{{ $key }}" data-max="{{ $value['available_qty'] }}"  > {{ $value['name'] }}  </option>
                       @endforeach
                    </select>
                @else 
                    <td> {{ Form::select('stores_id['.$row_count.']',$childs,null,['class'=>'form-control' ,"placeholder" => __("messages.please_select")]) }} </td>
                @endif
            
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
               
            @endif 
        @endif
         
        <?php $row_count++ ;?>
        <td><i class="fa fa-times remove_purchase_entry_row text-danger" title="Remove" style="cursor:pointer;"></i></td>
    </tr>
@endforeach

<input type="hidden" id="row_count" value="{{ $row_count }}">
