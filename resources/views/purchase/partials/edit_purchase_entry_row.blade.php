{{-- *1* --}}
@php
    $hide_tax = '';
    if( session()->get('business.enable_inline_tax') == 0){
        $hide_tax = 'hide';
    }
    $currency_precision = config('constants.currency_precision', 2);
    $quantity_precision = config('constants.quantity_precision', 2);
    $tax_amount_os =  $purchase->tax?$purchase->tax->amount:0;
@endphp
<div class="table-responsive">
    <table class="table table-condensed table-bordered table-th-green text-center table-striped dataTable" id="purchase_entry_table">
        <thead>
              <tr>
                <th>#</th>
                <th>@lang( 'product.product_name' )</th>
                <th>@lang( 'home.Quantity' )</th>
                <th>@lang( 'lang_v1.unit_cost_before_discount' ). {{isset($symbol)?$symbol:""}}</th>
                <th>@lang( 'home.Price Including Tax' ) . {{isset($symbol)?$symbol:""}}</th>
                <th @if($purchase->exchange_price > 1) class="curr_column br_dis  cur_check  " @else class="curr_column br_dis cur_check hide " @endif>@lang( 'lang_v1.Cost before without Tax' ) @if($purchase->exchange_price > 1 && $purchase->exchange_price != 1)  {{ ($purchase->currency)?$purchase->currency->symbol:"" }} @endif</th>
				<th @if($purchase->exchange_price > 1) class="  hide" @else class=" hide " @endif>@lang( 'home.Cost before with Tax' )</th>
							 
                <th >
                    <input name="dis_type" id="Discount"  type="radio" value="0" {{ ($purchase->dis_type == 0)?'checked':'' }}  >
                    @lang("home.percentage_discount") <br>
                    <input name="dis_type" id="Discount1" type="radio" value="1" {{ ($purchase->dis_type == 1)?'checked':'' }} >
                    @lang("home.amount_discount") <br>
                </th>
                <th>@lang( 'home.Cost without Tax' ). {{isset($symbol)?$symbol:""}}</th>
                <th>@lang( 'home.Cost After Tax' ). {{isset($symbol)?$symbol:""}}</th>
                <th @if($purchase->exchange_price > 1) class="curr_column ar_dis  cur_check  " @else class="curr_column ar_dis cur_check hide " @endif>@lang( 'home.Cost without Tax currency' ) @if($purchase->exchange_price > 1 && $purchase->exchange_price != 1)  {{ ($purchase->currency)?$purchase->currency->symbol:"" }} @endif</th>
				<th @if($purchase->exchange_price > 1) class="  hide" @else class=" hide " @endif>@lang( 'home.Cost After Tax currency' )</th>
                <th>@lang( 'home.Total' ). {{isset($symbol)?$symbol:""}}</th>
                <th @if($purchase->exchange_price > 1) class="curr_column ar_dis_total cur_check  " @else class="curr_column ar_dis_total cur_check hide " @endif>@lang( 'home.Total Currency' ) @if($purchase->exchange_price > 1 && $purchase->exchange_price != 1)  {{ ($purchase->currency)?$purchase->currency->symbol:"" }} @endif</th>
                {{-- <th>@lang( 'lang_v1.discount_percent' )</th>
                <th>@lang( 'purchase.unit_cost_before_tax' )</th> --}}
                <th class="{{$hide_tax}}">@lang( 'purchase.subtotal_before_tax' )</th>
                <th class="{{$hide_tax}}">@lang( 'purchase.product_tax' )</th>
                <th class="{{$hide_tax}}">@lang( 'purchase.net_cost' )</th>
                {{-- <th>@lang( 'purchase.line_total' )</th> --}}
                 
                {{-- <th>@lang( 'purchase.unit_selling_price') <small>(@lang('product.inc_of_tax'))</small></th> --}}
                @if(session('business.enable_lot_number'))
                    <th>
                        @lang('lang_v1.lot_number')
                    </th>
                @endif
                @if(session('business.enable_product_expiry'))
                    <th>@lang('product.mfg_date') / @lang('product.exp_date')</th>
                @endif
                <th>
                    <i class="fa fa-trash" aria-hidden="true"></i>
                </th>
              </tr>
        </thead>
        <tbody>
            <?php $row_count = 0; ?>
            @foreach($purchase->purchase_lines as $purchase_line)
                @php 
                    $list_of_prices_in_unit = \App\Product::getProductPrices($purchase_line->product->id);
                    $cypher = Illuminate\Support\Facades\Crypt::encryptString($purchase_line->purchase_note);
                @endphp 

                <tr class="line_sorting">
                    <td>
                        <input type="text" class="line_sort" hidden name="line_sort[{{$loop->index}}]" value="{{$loop->index}}">
                        <span class="sr_number line_ordered"></span>
                        <br>    
                        <i class="fas fa-sort pull-left handle cursor-pointer" title="@lang('lang_v1.sort_order')"></i>

                    </td>
                    <td>
                        <a  target="_blank" href="/item-move/{{$purchase_line->product->id}}">
                        {{ $purchase_line->product->name }} ({{$purchase_line->variations->sub_sku}})
                        <br>
                        <br>
                        </a>
                        @if( $purchase_line->product->type == 'variable') 
                            <br/>(<b>{{ $purchase_line->variations->product_variation->name}}</b> : {{ $purchase_line->variations->name}})
                        @endif
                        <div class="description_line" data-line="{{$row_count}}">
                            <pre style="white-space: nowrap;max-width:300px;max-height:150px" class="btn btn-modal products_details" data-href="{{action('ProductController@changeDescription', ['id'=>$purchase_line->product->id,'text'=> $cypher  ,'line'=>$row_count])}}" data-container=".view_modal">{!! $purchase_line->purchase_note !!}</pre>
                        </div>
                        <textarea class="form-control control_products_details" style="visibility:hidden" data-line="{{$row_count}}"  id="purchases[{{$row_count}}][purchase_note]" name="purchases[{{$row_count}}][purchase_note]" rows="{{$row_count}}"> {!! $purchase_line->purchase_note !!}</textarea>
                        
                    </td>
                    
                    <td>
                        {!! Form::hidden('purchases[' . $loop->index . '][product_id]', $purchase_line->product_id ); !!}
                        {!! Form::hidden('purchases[' . $loop->index . '][variation_id]', $purchase_line->variation_id ); !!}
                        {!! Form::hidden('purchases[' . $loop->index . '][purchase_line_id]',$purchase_line->id); !!}
                        @php
                            $check_decimal = 'false';
                            if($purchase_line->product->unit->allow_decimal == 0){
                                $check_decimal = 'true';
                            }
                        @endphp
                    
                        {!! Form::text('purchases[' . $loop->index . '][quantity]', 
                            $purchase_line->quantity ,
                            ["min"=>1,'class' => 'form-control input-sm  purchase_quantity input_number mousetrap', "data-number" => $loop->index ,  'required', 'data-rule-abs_digit' => $check_decimal, 'data-msg-abs_digit' => __('lang_v1.decimal_value_not_allowed')]); !!} 

                            <input type="hidden" class="base_unit_cost" value="{{$purchase_line->variations->default_purchase_price}}">
                            {{-- @if(!empty($purchase_line->sub_units_options))
                                <br>
                                <select name="purchases[{{$loop->index}}][sub_unit_id]" class="form-control input-sm sub_unit">
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
                                $productUtil    = new \App\Utils\ProductUtil();
                                $sub_units      = $productUtil->getSubUnits($purchase_line->product->business_id, $purchase_line->product->unit->id, false, $purchase_line->product->id);
                                foreach($sub_units as $k => $line){
                                    $allUnits[$k] =  $line; 
                                }
                                $sub_units = $allUnits  ;
                                $count = $purchase_line->product->unit->id;
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
    
                    <input type="hidden" class="tax_row" value="{{ $purchase->tax?$purchase->tax->amount:0 }}">

                    {{-- */* unit price before dis     --}}
                    <td>
                        {!! Form::text('purchases[' . $loop->index . '][pp_without_discount_s]', round($purchase_line->pp_without_discount/$purchase->exchange_rate,config('constants.currency_percision'))  ,
                            ['class' => 'form-control  input-sm purchase_unit_cost_without_discount_s input_number' , "data-number" =>  $loop->index , 'required']); !!}
                        {!! Form::hidden('purchases[' . $loop->index . '][pp_without_discount]',  round($purchase_line->pp_without_discount/$purchase->exchange_rate,config('constants.currency_percision')) ,
                            ['class' => 'form-control  input-sm purchase_unit_cost_without_discount input_number' , "data-number" =>  $loop->index , 'required']); !!}
                        <br>
                        <b><small>@lang('List Of Price'):</small></b>
                                <div class="form-group">
                                    <select data-prices="{{json_encode($list_of_prices_in_unit)}}"  name="purchases[{{ $row_count }}][list_price]" class="form-control select2 list_price" placeholder="'Please Select'">
                                        <option value="" data-price="" @if($purchase_line->list_price == null) selected @endif >@lang('lang_v1.none')</option>
                                        @foreach($list_of_prices_in_unit as $key => $row_line)
                                                @if($key == $purchase_line->sub_unit_id)
                                                    @foreach ($row_line as $item)
                                                        @php $price_line = ($item['price']!=null)?$item['price']:0; @endphp 
                                                        <option value="{{$item['line_id']}}" @if($purchase_line->list_price == $item['line_id']) selected @endif data-price="{{$price_line}}" data-value="{{$item['line_id']}}" >{{$item["name"]}}</option>
                                                    @endforeach
                                                @else
                                                    @if($key == $count)
                                                        @foreach ($row_line as $item)
                                                        @php $price_line = ($item['price']!=null)?$item['price']:0; @endphp 
                                                            <option value="{{$item['line_id']}}" @if($purchase_line->list_price == $item['line_id']) selected @endif data-price="{{$price_line}}" data-value="{{$item['line_id']}}" >{{$item["name"]}}</option>
                                                        @endforeach
                                                    @endif
                                                @endif
                                        @endforeach
                                    </select>
                                </div>
                        <!--<p class="help-block"><small>@lang('lang_v1.sell_line_description_help')</small></p>-->
                    
                    </td>
                    {{-- */* unit price before dis includ.vat --}}
                    @php
                        if( $purchase->tax_id != null ){
                            $before_dis_inc_vat =  floatval($purchase_line->pp_without_discount)  +   (floatval($purchase_line->pp_without_discount) * floatval($tax_amount_os)   / 100 );
                        }else{
                            $before_dis_inc_vat =  floatval($purchase_line->pp_without_discount);
                        }
                    @endphp

                    <td>
                        {!! Form::text('purchases[' . $loop->index . '][pp_without_discount]',  round($before_dis_inc_vat/$purchase->exchange_rate,config('constants.currency_percision')) ,
                        ['class' => 'form-control input-sm purchase_pos_inc  purchase_unit_cost_with_tax  input_number', "data-number" =>  $loop->index , 'required']); !!}
                    </td>
                    @php
                        $currency = $purchase->exchange_price; 
                    @endphp
                    <td @if($purchase->exchange_price > 1) class="curr_column  cur_check  " @else class="curr_column  cur_check hide " @endif>
                        {!! Form::text('purchases[' . $loop->index . '][purchase_unit_cost_new_currency]', number_format(($currency!= null && $currency != 0)?( $purchase_line->pp_without_discount/$purchase->exchange_rate)/$currency:0, $currency_precision, $currency_details->decimal_separator, $currency_details->thousand_separator),
                        ['class' => 'form-control input-sm purchase_pos_inc  purchase_unit_cost_new_currency  input_number', "data-number" =>  $loop->index , 'required']); !!}
                    </td>
                    <td @if($purchase->exchange_price > 1) class=" hide" @else class=" hide " @endif>
                        {!! Form::text('purchases[' . $loop->index . '][purchase_unit_cost_with_tax_new_currency]', number_format(($currency!= null && $currency != 0)?($before_dis_inc_vat/$purchase->exchange_rate)/$currency:0, $currency_precision, $currency_details->decimal_separator, $currency_details->thousand_separator),
                        ['class' => 'form-control input-sm purchase_pos_inc  purchase_unit_cost_with_tax_new_currency  input_number', "data-number" =>  $loop->index , 'required']); !!}
                    </td>
                    {{-- */* discount % && fixed --}}
                    <td>
                        @php
                        ($purchase->dis_type == 1)? $discount_i = ($purchase_line->discount_percent * floatval($purchase_line->pp_without_discount))/100: $discount_i = $purchase_line->discount_percent;
                        @endphp
                        {!! Form::text('purchases[' . $loop->index . '][discount_percent]', number_format( $purchase_line->discount_percent, $currency_precision, $currency_details->decimal_separator, $currency_details->thousand_separator), 
                        ['class' => 'form-control input-sm inline_discounts  input_number','data-number'=>$loop->index ]); !!} <b> </b>
                    </td>
                        {{-- {{dd(floatval($purchase_line->item_tax));}} --}}
                    
                    {{-- */* unit price after dis  --}}
                    <td>
                        {!! Form::text('purchases[' . $loop->index . '][purchase_price]', 
                        number_format($purchase_line->purchase_price/$purchase->exchange_rate, $currency_precision, $currency_details->decimal_separator, $currency_details->thousand_separator),
                        ['class' => 'form-control input-sm cost_pos purchase_unit_cost  input_number', 'readonly', "data-number" =>  $loop->index , 'required'  ]); !!}
                    </td>
                    {{-- */* unit price after dis includ.vat --}}
                    @php
                        if( $purchase->tax_id != null ){
                            
                            $After_dis_inc_vat =  floatval($purchase_line->purchase_price)  +   (floatval($purchase_line->purchase_price) * floatval($tax_amount_os)   / 100 );
                        }else{
                            
                            $After_dis_inc_vat =  floatval($purchase_line->purchase_price);
                        }
                    @endphp
                    <td>
                        {!! Form::text('purchases[' . $loop->index . '][unit_cost_after_tax]', 
                        number_format($After_dis_inc_vat/$purchase->exchange_rate, $currency_precision, $currency_details->decimal_separator, $currency_details->thousand_separator), 
                        ['class' => 'form-control input-sm cost_pos_inc total_unit_cost_with_tax   input_number','readonly', "data-number" =>  $loop->index , 'required' ]); !!}
                    </td>
                    <td @if($purchase->exchange_price > 1) class="curr_column  cur_check  " @else class="curr_column  cur_check hide " @endif>
                        {!! Form::text('purchases[' . $loop->index . '][unit_cost_after_new_currency]', 
                        number_format(($currency!= null && $currency != 0)?($purchase_line->purchase_price/$purchase->exchange_rate)/$currency:0, $currency_precision, $currency_details->decimal_separator, $currency_details->thousand_separator), 
                        ['class' => 'form-control input-sm cost_pos_inc unit_cost_after_new_currency   input_number','readonly', "data-number" =>  $loop->index , 'required' ]); !!}
                    </td>
                    <td @if($purchase->exchange_price > 1) class="  hide" @else class="  hide " @endif>
                        {!! Form::text('purchases[' . $loop->index . '][unit_cost_after_tax_new_currency]', 
                        number_format(($currency!= null && $currency != 0)?($After_dis_inc_vat/$purchase->exchange_rate)/$currency:0, $currency_precision, $currency_details->decimal_separator, $currency_details->thousand_separator), 
                        ['class' => 'form-control input-sm cost_pos_inc unit_cost_after_tax_new_currency   input_number','readonly', "data-number" =>  $loop->index , 'required' ]); !!}
                    </td>
                    @php
                        if($purchase->exchange_rate != 0){
                            $total_price_eb = $purchase_line->quantity * $purchase_line->purchase_price/$purchase->exchange_rate;
                        }else{
                            $total_price_eb = $purchase_line->quantity * $purchase_line->purchase_price; 
                        }
                    @endphp
                    <td class="{{$hide_tax}}">
                        <span class="row_subtotal_before_tax">
                            {{number_format($total_price_eb, $currency_precision, $currency_details->decimal_separator, $currency_details->thousand_separator)}}
                        </span>
                        <input type="hidden" class="row_subtotal_before_tax_hidden" value="{{number_format($total_price_eb, $currency_precision, $currency_details->decimal_separator, $currency_details->thousand_separator)}}">
                    </td>
                    <td class="{{$hide_tax}}">
                        <div class="input-group">
                            <select name="purchases[{{ $loop->index }}][purchase_line_tax_id]" class="form-control input-sm purchase_line_tax_id" placeholder="'Please Select'">
                                <option value="" data-tax_amount="0" @if( empty( $purchase_line->tax_id ) )
                                selected @endif >@lang('lang_v1.none')</option>
                                @foreach($taxes as $tax)
                                    <option value="{{ $tax->id }}" data-tax_amount="{{ $tax->amount }}" @if( $purchase_line->tax_id == $tax->id) selected @endif >{{ $tax->name }}</option>
                                @endforeach
                            </select>
                            <span class="input-group-addon purchase_product_unit_tax_text">
                                {{number_format($purchase_line->item_tax/$purchase->exchange_rate, $currency_precision, $currency_details->decimal_separator, $currency_details->thousand_separator)}}
                            </span>
                            {!! Form::hidden('purchases[' . $loop->index . '][item_tax]', number_format($purchase_line->item_tax/$purchase->exchange_rate, $currency_precision, $currency_details->decimal_separator, $currency_details->thousand_separator), ['class' => 'purchase_product_unit_tax']); !!}
                        </div>
                    </td>
                    {{-- <td class="{{$hide_tax}}">
                        {!! Form::text('purchases[' . $loop->index . '][purchase_price_inc_tax]', number_format($purchase_line->purchase_price_inc_tax/$purchase->exchange_rate, $currency_precision, $currency_details->decimal_separator, $currency_details->thousand_separator), ['class' => 'form-control input-sm purchase_unit_cost_after_tax input_number', 'required']); !!}
                    </td> --}}
                    @php
                        if($purchase->exchange_rate != 0){
                            $total_eb = $After_dis_inc_vat * $purchase_line->quantity/$purchase->exchange_rate;
                        }else{
                            $total_eb = $After_dis_inc_vat * $purchase_line->quantity; 
                        }
                    @endphp
                    <td  @if($purchase->exchange_price > 1) class="curr_column  cur_check  " @else class="curr_column  cur_check hide " @endif>
                        <input class="form-control input-sm row_total_cost_new_currency input_number"  readonly required="" name="{{ 'purchases[' . $loop->index . '][total_cost_after_tax]' }}" type="text" 
                        value="{{$total_eb}}">
                    </td>
                    <td >
                    
                        <input class="form-control input-sm row_total_cost input_number"  readonly required="" name="{{ 'purchases[' . $loop->index . '][total_cost_after_tax]' }}" type="text" 
                        value="{{ $total_eb }}">
                    </td>
                     

                    

                    {{-- <td>
                        @if(session('business.enable_editing_product_from_purchase'))
                            {!! Form::hidden('purchases[' . $loop->index . '][default_sell_price]', number_format($sp, $currency_precision, $currency_details->decimal_separator, $currency_details->thousand_separator), ['class' => 'form-control input-sm input_number default_sell_price', 'required']); !!}
                        @else
                            {{number_format($sp, $currency_precision, $currency_details->decimal_separator, $currency_details->thousand_separator)}}
                        @endif

                    </td> --}}

                    @if(session('business.enable_lot_number'))
                        <td>
                            {!! Form::text('purchases[' . $loop->index . '][lot_number]', $purchase_line->lot_number, ['class' => 'form-control input-sm']); !!}
                        </td>
                    @endif

                    @if(session('business.enable_product_expiry'))
                        <td style="text-align: left;">
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
                            @php
                                $mfg_date = null;
                                $exp_date = null;
                                if(!empty($purchase_line->mfg_date)){
                                    $mfg_date = $purchase_line->mfg_date;
                                }
                                if(!empty($purchase_line->exp_date)){
                                    $exp_date = $purchase_line->exp_date;
                                }
                            @endphp
                            <div class="input-group @if($hide_mfg) hide @endif">
                                <span class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </span>
                                {!! Form::text('purchases[' . $loop->index . '][mfg_date]', !empty($mfg_date) ? @format_date($mfg_date) : null, ['class' => 'form-control input-sm expiry_datepicker mfg_date', 'readonly']); !!}
                            </div>
                            <b><small>@lang('product.exp_date'):</small></b>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </span>
                                {!! Form::text('purchases[' . $loop->index . '][exp_date]', !empty($exp_date) ? @format_date($exp_date) : null, ['class' => 'form-control input-sm expiry_datepicker exp_date', 'readonly']); !!}
                            </div>
                            @else
                            <div class="text-center">
                                @lang('product.not_applicable')
                            </div>
                            @endif
                        </td>
                    @endif
                
                    <td><i class="fa fa-times remove_purchase_entry_row text-danger" title="Remove" style="cursor:pointer;"></i></td>
                </tr>
                <?php $row_count = $loop->index + 1 ; ?>
            @endforeach
        </tbody>
    </table>
</div>
{{-- *2* --}}
<input type="hidden" id="row_count" value="{{ $row_count }}">
{{-- *3* --}}
@section('edit_row_os')
    {{-- */1* relations  section --}}
    <script src="{{ asset('js/purchase.js?v=' . $asset_v) }}"></script>
    {{-- */2* additional section --}}
    <script>
        $('.purchase_table .purchase_unit_cost_with_tax').change(function(){
            var tax_amount  = parseFloat($('#tax_id option:selected').data('tax_amount')) ;
            var el          =  $(this).parent().parent();
            var price       =  parseFloat($(this).val())/((tax_amount/100)+1) ;
            el.children().find('.purchase_unit_cost_without_discount_s').val(price.toFixed(2));
            el.children().find('.purchase_unit_cost_without_discount').val(price);
            os_total_sub();
            os_grand();
        });
        $('.purchase_table .purchase_unit_cost_without_discount').change(function(){
            var tax_amount  = parseFloat($('#tax_id option:selected').data('tax_amount')) ;
            var el          =  $(this).parent().parent();
            var price       =   parseFloat($(this).val())*(tax_amount/100) + parseFloat($(this).val()) ; 
            el.children().find('.purchase_unit_cost_with_tax').val(price.toFixed(2));
            var intial_avl =  parseFloat($(this).val());
                os_total_sub();
                os_grand();
                
            el.children().find('.purchase_unit_cost_without_discount_s').attr("type","text");
            el.children().find('.purchase_unit_cost_without_discount').attr("type","hidden");
            
        });
    </script>
@endsection 
