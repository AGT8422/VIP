 
<div class="table-responsive">
    <table class="table table-condensed table-bordered table-th-green text-center table-striped" id="purchase_entry_table">
        <thead>
              <tr>
                <th>#</th>
                <th>@lang( 'product.product_name' )</th>
                <th>@lang( 'home.Quantity' )</th>            
                <th>@lang( 'purchase.unit_cost_ex' )</th>            
                <th>@lang( 'Expire Date' )</th> 
                <th>@lang( 'delivery.store_name' )</th> 
                <th>@lang( 'home.Total' )</th>             
                <th>
                    <i class="fa fa-trash" aria-hidden="true"></i>
                </th>
              </tr>
        </thead>
        <tbody>
            <?php $row_count = 0; ?>
            @if(!isset($open))
                @foreach( $purchase_lines as $purchase_line)
                    <tr   tr class="line_sorting">
                        <td>
                            <input type="text" class="line_sort" hidden name="line_sort[{{$loop->index}}]" value="{{$loop->index}}">
                            <span class="sr_number line_ordered"></span>
                            <br>    
                            <i class="fas fa-sort pull-left handle cursor-pointer" title="@lang('lang_v1.sort_order')"></i>

                        </td>

                        <td>
                            {{-- {{ dd($purchase );}} --}}
                            <a  target="_blank" href="/item-move/{{$purchase_line->product->id}}">
                            {{ $purchase_line->product->name }} ({{$purchase_line->variations->sub_sku}})
                            </a>
                            @if( $purchase_line->product->type == 'variable') 
                                <br/>(<b>{{ $purchase_line->variations->product_variation->name}}</b> : {{ $purchase_line->variations->name}})
                            @endif
                        </td>

                        <td>
                            <input type="hidden" name="old_item_id[]" value="{{ $purchase_line->id }}"> 
                            {!! Form::hidden('purchases[' . $loop->index . '][product_id]', $purchase_line->product_id ); !!}
                            {!! Form::hidden('purchases[' . $loop->index . '][variation_id]', $purchase_line->variation_id ); !!}
                            {!! Form::hidden('purchases[' . $loop->index . '][purchase_line_id]',
                            $purchase_line->id); !!}

                            @php
                                $check_decimal = 'false';
                                if($purchase_line->product->unit->allow_decimal == 0){
                                    $check_decimal = 'true';
                                }
                            @endphp
                        
                            {!! Form::text('purchases[' . $loop->index . '][quantity]', 
                            $purchase_line->quantity ,
                            ['class' => 'form-control input-sm  purchase_quantity input_number mousetrap', "data-number" => $loop->index ,  'required', 'data-rule-abs_digit' => $check_decimal, 'data-msg-abs_digit' => __('lang_v1.decimal_value_not_allowed')]); !!} 

                            <input type="hidden" class="base_unit_cost" value="{{$purchase_line->variations->default_purchase_price}}">
                            {{-- @if(!empty($purchase_line->sub_units_options))
                                <br>
                                <select name="purchases[{{$loop->index}}][sub_unit_id]" class="form-control input-sm sub_unit" required>
                                    @foreach($purchase_line->sub_units_options as $sub_units_key => $sub_units_value)
                                        <option value="{{$sub_units_key}}" 
                                            data-multiplier="{{$sub_units_value['multiplier']}}"
                                            @if($sub_units_key == $purchase_line->sub_unit_id) selected @endif>
                                            {{$sub_units_value['name']}}
                                        </option>
                                    @endforeach
                                </select>
                            @else
                                {{ $purchase_line->product->unit->short_name }}
                            @endif --}}
                            @php 
                                $var     = $purchase_line->product->variations->first();
                                $var     = ($var)?$var->default_purchase_price:0;
                                $allUnits       = [];
                                $business   = \App\Business::find($purchase_line->product->business_id);
                                $allUnits[$purchase_line->product->unit_id] = [
                                    'name' => $purchase_line->product->unit->actual_name,
                                    'multiplier' => $purchase_line->product->unit->base_unit_multiplier,
                                    'allow_decimal' => $purchase_line->product->unit->allow_decimal,
                                    'price' => $var,
                                    'check_price' => $business->default_price_unit,
                                    ];
                                // $productUtil    = new \App\Utils\ProductUtil();
                                // $sub_units      = $productUtil->getSubUnits($purchase_line->product->business_id, $purchase_line->product->unit->id, false, $purchase_line->product->id);
                                // foreach($sub_units as $k => $line){
                                //     $allUnits[$k] =  $line; 
                                // }
                                if($purchase_line->product->sub_unit_ids != null){
                                    foreach($purchase_line->product->sub_unit_ids  as $i){
                                            $row_price    =  0;
                                            $un           = \App\Unit::find($i);
                                            $row_price    = \App\Models\ProductPrice::where("unit_id",$i)->where("product_id",$purchase_line->product->id)->where("number_of_default",0)->first();
                                            $row_price    = ($row_price)?$row_price->default_purchase_price:0;
                                            $allUnits[$i] = [
                                                'name'          => $un->actual_name,
                                                'multiplier'    => $un->base_unit_multiplier,
                                                'allow_decimal' => $un->allow_decimal,
                                                'price'         => $row_price,
                                                'check_price'   => $business->default_price_unit,
                                            ] ;
                                        }
                                } 
                                $sub_units              = $allUnits  ;
                                $count                  = $purchase_line->product->unit_id;
                                $list_of_prices_in_unit = \App\Product::getProductPrices($purchase_line->product->id);
                            @endphp
                            <br>
                            <select name="purchases[{{$loop->index}}][sub_unit_id]" class="form-control input-sm sub_unit" required>
                                @foreach($sub_units  as $sub_units_key => $sub_units_value)
                                    <option value="{{$sub_units_key}}"  data-price="{{$sub_units_value['price']}}" data-check_price="{{$sub_units_value['check_price']}}"
                                        data-multiplier="{{$sub_units_value['multiplier']}}"
                                        @if($sub_units_key == $purchase_line->sub_unit_id) @php $count = $purchase_line->sub_unit_id; @endphp selected @endif>
                                        {{$sub_units_value['name']}}
                                    </option>
                                @endforeach
                            </select>

                            <input type="hidden" name="purchases[{{$loop->index}}][product_unit_id]" value="{{$purchase_line->product->unit->id}}">

                            <input type="hidden" class="base_unit_selling_price" value="{{$purchase_line->variations->sell_price_inc_tax}}">
                        </td>

                        <td>
                                    {!! Form::text('purchases[' . $loop->index . '][pp_without_discount_s]',  round($purchase_line->pp_without_discount,config('constants.currency_precision'))  ,
                                        ['class' => 'form-control  input-sm purchase_unit_cost_without_discount_s input_number' , "data-number" =>  $loop->index , 'required']); !!}
                                    
                                <br>
                                @if(app('request')->input('type_o') == 'store' || isset($open) )
                                <textarea class="form-control" id="purchases[{{$row_count}}][purchase_note]" name="purchases[{{$row_count}}][purchase_note]" rows="{{$row_count}}"> {{ strip_tags($purchase_line->purchase_note) }}</textarea>
                                <!--<p class="help-block"><small>@lang('lang_v1.sell_line_description_help')</small></p>-->
                                @else
                                @endif
                            
                                <b><small>@lang('List Of Price'):</small></b>
                                <div class="form-group">
                                    <select data-prices="{{json_encode($list_of_prices_in_unit)}}"  name="purchases[{{ $row_count }}][list_price]" class="form-control select2 list_price" placeholder="'Please Select'">
                                        <option value="" data-price="" @if($purchase_line->list_price == null) selected @endif >@lang('lang_v1.none')</option>
                                        @foreach($list_of_prices_in_unit as $key => $row_line)
                                            @if($key == $count)
                                                @foreach ($row_line as $item)
                                                    <option value="{{$item['line_id']}}" @if($purchase_line->list_price == $item['line_id']) selected @endif data-price="{{$item['price']}}" data-value="{{$item['line_id']}}" >{{$item["name"]}}</option>
                                                @endforeach
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                        </td>

                        <td class="hide"></td>
                        <td class="hide"></td>
                        <td class="hide"></td>
                        <td class="hide"></td>
                        <td class="hide"></td>
                        <td class="hide"></td>
                        
                        
                        @if(session('business.enable_product_expiry'))
                            <td style="text-align: left;">

                                {{-- Maybe this condition for checkin expiry date need to be removed --}}
                                @php
                                    $expiry_period_type = !empty($purchase_line->product->expiry_period_type) ? $purchase_line->product->expiry_period_type : 'month';
                                @endphp
                                @if(!empty($expiry_period_type))
                                <input type="hidden" class="row_product_expiry" value="{{ $purchase_line->product->expiry_period }}">
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
                                    {!! Form::text('purchases[' . $loop->index . '][mfg_date]', null, ['class' => 'form-control input-sm expiry_datepicker mfg_date', 'readonly']); !!}
                                </div>
                                <b><small>@lang('product.exp_date'):</small></b>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </span>
                                    {!! Form::text('purchases[' . $loop->index . '][exp_date]', null, ['class' => 'form-control input-sm expiry_datepicker exp_date', 'readonly']); !!}
                                </div>
                                @else
                                <div class="text-center">
                                    @lang('product.not_applicable')
                                </div>
                                @endif
                            </td>
                        @endif

                        <td class="hide">Expire</td>
                        
                      
                        <td> 
                            @php
                                $store_id = 1;
                                $open_id_ = 0;
                            
                                foreach ($pline_store as  $key => $value) {
                                    if($value == $purchase_line->id){
                                        foreach ($open_store as  $key_ => $value_) {
                                            if($key == $key_){
                                                $store_id = $value_;
                                                $open_id_ = $open_id[$key];
                                                
                                            }
                                            
                                        }
                                        
                                        
                                    }
                                }
                                $open = \App\Models\OpeningQuantity::where("purchase_line_id",$purchase_line->id)->first();
                            @endphp
                            {!! Form::hidden('purchases['.$loop->index.'][open_id]',  ($open)?$open->id:null, [  'class' => 'form-control ', 'placeholder' => __('messages.please_select')] ); !!}
                            {!! Form::select('purchases['.$loop->index.'][store_id]', $mainstore_categories, $purchase_line->store_id, [  'class' => 'form-control select2', 'placeholder' => __('messages.please_select') ] ); !!}
                        </td>

                        <td>
                            <input class="form-control input-sm row_total_cost_ input_number"  disabled required="" name="{{ 'purchases[' . $loop->index . '][total_cost_after_tax]' }}" type="text" 
                                value="{{ $purchase_line->pp_without_discount  * $purchase_line->quantity  }}">
                        </td>
                        <td>
                            <i class="fa fa-times remove_purchase_entry_row text-danger" title="Remove" style="cursor:pointer;"></i>
                        </td>
                    </tr>
                    <?php $row_count = $loop->index + 1 ; ?>
                @endforeach
            @else
                @foreach( $variations as $variation)
                    <tr>
                        <td><span class="sr_number"></span></td>
                        <td>
                            {{ $product->name }} ({{$variation->sub_sku}})
                            @if( $product->type == 'variable' )
                                <br/>
                                (<b>{{ $variation->product_variation->name }}</b> : {{ $variation->name }})
                            @endif
                            @if($product->enable_stock == 1)
                                <br>
                                <small class="text-muted" style="white-space: nowrap;">@lang('report.current_stock'): @if(!empty($variation->variation_location_details->first())) {{@num_format($variation->variation_location_details->first()->qty_available)}} @else 0 @endif {{ $product->unit->short_name }}</small>
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
                            {!! Form::text('purchases[' . $row_count . '][quantity]', number_format(1, $quantity_precision, $currency_details->decimal_separator, $currency_details->thousand_separator), ['class' => 'form-control input-sm purchase_quantity purchase_ input_number mousetrap', 'required', 'data-rule-abs_digit' => $check_decimal, 'data-msg-abs_digit' => __('lang_v1.decimal_value_not_allowed')]); !!}
                            <input type="hidden" class="base_unit_cost" value="{{$variation->default_purchase_price}}">
                            <input type="hidden" class="base_unit_selling_price" value="{{$variation->sell_price_inc_tax}}">
                
                            <input type="hidden" name="purchases[{{$row_count}}][product_unit_id]" value="{{$product->unit->id}}">
                            @if(!empty($sub_units))
                                <br>
                                <select name="purchases[{{$row_count}}][sub_unit_id]" class="form-control input-sm sub_unit">
                                    @foreach($sub_units as $key => $value)
                                        <option value="{{$key}}" data-multiplier="{{$value['multiplier']}}"  data-price="{{$value['price']}}" data-check_price="{{$value['check_price']}}" >
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
                                number_format($variation->default_purchase_price, $currency_precision, $currency_details->decimal_separator, $currency_details->thousand_separator), [ ' class' => 'form-control input-sm  eb_price input_number ', 'required']); !!}
                            
                            @else
                                {!! Form::text('purchases[' . $row_count . '][pp_without_discount_s]',
                                $variation->default_purchase_price, [ ' class' => 'form-control input-sm purchase_unit_cost_without_discount input_number ', 'required']); !!}
                                    {!! Form::hidden('purchases[' . $row_count . '][pp_without_discount]',
                                    number_format($variation->default_purchase_price, $currency_precision, $currency_details->decimal_separator, $currency_details->thousand_separator), ['class' => 'form-control input-sm purchase_unit_cost_without_discount_s input_number', 'required']); !!}
                            @endif
                            <br>
                            @if(app('request')->input('type_o') == 'store' || isset($open) )
                            @else
                            <?php $pr =  \App\Product::find($product->product_id);  ?>
                            <textarea class="form-control" id="purchases[{{$row_count}}][purchase_note]" name="purchases[{{$row_count}}][purchase_note]" rows="{{$row_count}}"> {{ strip_tags($product->product_description) }}</textarea>
                            <!--<p class="help-block"><small>@lang('lang_v1.sell_line_description_help')</small></p>-->
                            @endif
                        
                    </td>
                        <td class="hide">
                            {!! Form::text('purchases[' . $row_count . '][pp_with_tax]',
                            number_format($variation->default_purchase_price, $currency_precision, $currency_details->decimal_separator, $currency_details->thousand_separator), ['class' => 'form-control input-sm purchase_unit_cost_with_tax input_number', 'required']); !!}
                        </td>
                        <td class="hide">
                            {!! Form::text('purchases[' . $row_count . '][discount_percent]', 0, ['class' => 'form-control input-sm inline_discounts input_number', 'required']); !!}
                        </td>
                        <td class="hide">
                            {!! Form::text('purchases[' . $row_count . '][purchase_price]',
                        $variation->default_purchase_price, ['class' => 'form-control input-sm purchase_unit_cost input_number' ,'readonly', 'required']); !!}
                        </td>
                        <td class="hide">
                            {!! Form::text('purchases[' . $row_count . '][unit_cost_after_tax]',
                            number_format($variation->default_purchase_price, $currency_precision, $currency_details->decimal_separator, $currency_details->thousand_separator), ['class' => 'form-control input-sm total_unit_cost_with_tax  input_number' ,'readonly', 'required']); !!}
                        </td>
                        <td class="hide">
                            {!! Form::text('purchases[' . $row_count . '][total_cost_after_tax]',
                            number_format($variation->default_purchase_price, $currency_precision, $currency_details->decimal_separator, $currency_details->thousand_separator), ['class' => 'form-control input-sm row_total_cost input_number','disabled', 'required']); !!}
                        </td>
                        <td  class="hide">
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
                        <td class="hide">
                            
                            @php
                                $dpp_inc_tax = number_format($variation->dpp_inc_tax, $currency_precision, $currency_details->decimal_separator, $currency_details->thousand_separator);
                                if($hide_tax == 'hide'){
                                    $dpp_inc_tax = number_format($variation->default_purchase_price, $currency_precision, $currency_details->decimal_separator, $currency_details->thousand_separator);
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
                                {{ number_format($variation->sell_price_inc_tax, $currency_precision, $currency_details->decimal_separator, $currency_details->thousand_separator)}}
                            @endif
                        </td>
                        @endif
                        @if(session('business.enable_lot_number'))
                            <td>
                                {!! Form::text('purchases[' . $row_count . '][lot_number]', null, ['class' => 'form-control input-sm']); !!}
                            </td>
                        @endif
                        @if(session('business.enable_product_expiry'))
                            <td class="hide" style="text-align: left;">
                
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
                        <?php $row_count++ ;?>
                        
                        @if(app('request')->input('type_o') == 'store' || isset($open) )
                    
                            <td> {{ Form::select('stores_id[]',$childs,null,['class'=>'form-control']) }} </td>
                            
                            <td>
                                <input class="form-control input-sm row_total_cost_ input_number"  disabled required="" name="{{ 'purchases[' . $loop->index . '][total_cost_after_tax]' }}" type="text" 
                                        value="">
                            </td>
                            <td></td>
                        @endif
                
                        <td><i class="fa fa-times remove_purchase_entry_row text-danger" title="Remove" style="cursor:pointer;"></i></td>
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>
    
</div>
<div class="row">
    <div class="col-md-6 "  >
        <span>@lang("purchase.qty") : </span><span class="total_item_"></span><br>
        <span>@lang("purchase.amount_total") : </span><span class="total_price_"></span>
    </div>
</div>
<input type="hidden" id="row_count" value="{{ $row_count }}">
 
 
 