@php
    $common_settings = session()->get('business.common_settings');
      $sell_line =  \App\TransactionSellLine::first(); 
      if (!isset($action)) {
        $action = 'add';
      }
 @endphp
 

<tr class="product_row line_sorting" data-row_index="{{$row_count}}" @if(!empty($so_line)) data-so_id="{{$so_line->transaction_id}}" @endif>
     
    <td>
        <input type="text" class="line_sort" hidden name="line_sort[{{$row_count}}]" value="{{$row_count}}">    
        <div class="line_ordered">
            @if($action == 'add')
            {{$row_count}}
            @else
            {{$row_count+1}}
            @endif
        </div>
        <br>
        <i class="fas fa-sort pull-left handle cursor-pointer ui-sortable-handle" title="@lang('lang_v1.sort_order')"></i>
    </td>
    <td>
     
         {{-- {{ $so_line->se_note }} --}}
        <textarea class="form-control" name="products[{{$row_count}}][se_note]" rows="2">{{ $product->se_note }}</textarea>
    </td>
   {{--Product name--}}
    <td>
        <a target="_blank" href="/item-move/{{$product->product_id}}">
            @if(!empty($so_line))
                <input type="hidden"
                    name="products[{{$row_count}}][so_line_id]"
                    value="{{$so_line->id}}">
            @endif
            @php
            
                if($product->product_name == null){
                    $product_name = $product->name . '<br/>' . $product->sub_sku ;
                }else{
                    $product_name = $product->product_name . '<br/>' . $product->sub_sku ;
                }
                if(!empty($product->brand)){ $product_name .= ' ' . $product->brand ;}
            @endphp

            @if( ($edit_price || $edit_discount) && empty($is_direct_sell) )
                <div title="@lang('lang_v1.pos_edit_product_price_help')">
                    <span class="text-link text-info cursor-pointer" data-toggle="modal" data-target="#row_edit_product_price_modal_{{$row_count}}">
                        {!! $product_name !!}  
                        &nbsp;<i class="fa fa-info-circle"></i>
                    </span>
                </div>
            @else
                {!! $product_name !!}
            @endif
        </a>

        <input type="hidden" class="enable_sr_no" value="{{$product->enable_sr_no}}">
        <input type="hidden"
               class="product_type"
               name="products[{{$row_count}}][product_type]"
               value="{{$product->product_type}}">

        @php
            $hide_tax = 'hide';
            if(session()->get('business.enable_inline_tax') == 1){
                $hide_tax = '';
            }
           
            $tax_id                  = $product->tax_id;
            $item_tax                = !empty($product->item_tax) ? $product->item_tax : 0;
            $unit_price_inc_tax_     = $product->default_sell_price;
            $unit_price_inc_tax      = $product->sell_price_inc_tax;
            $unit_price_inc_tax_last = $product->sell_price_inc_tax;

            if(!empty($so_line)) {
                $tax_id   = $so_line->tax_id;
                $item_tax = $so_line->item_tax;
            }

            // dd($taxes["attributes"][$tax_id]);

            if($hide_tax == 'hide'){
                $tax_id             = null;
                $unit_price_inc_tax = $product->default_sell_price;
            }


            $discount_type   = !empty($product->line_discount_type) ? $product->line_discount_type : 'fixed';
            $discount_amount = !empty($product->line_discount_amount) ? $product->line_discount_amount : 0;

            if(!empty($discount)) {
                $discount_type   = $discount->discount_type;
                $discount_amount = $discount->discount_amount;
            }

            if(!empty($so_line)) {
                $discount_type   = $so_line->line_discount_type;
                $discount_amount = $so_line->line_discount_amount;
            }

            //   $sell_line_note = strip_tags($product->product_description);
              $sell_line_note = $product->product_description;
              if(!empty($product->sell_line_note)){
                //   $sell_line_note = strip_tags($product->sell_line_note);
                  $sell_line_note = $product->sell_line_note;
              }
        @endphp

        @if(!empty($discount))
            {!! Form::hidden("products[$row_count][discount_id]", $discount->id); !!}
        @endif

        @php
            $warranty_id = !empty($action) && $action == 'edit' && !empty($product->warranties->first())  ? $product->warranties->first()->id : $product->warranty_id;
        @endphp

        @if(empty($is_direct_sell))
            <div class="modal fade row_edit_product_price_model" id="row_edit_product_price_modal_{{$row_count}}" tabindex="-1" role="dialog">
                @include('sale_pos.partials.row_edit_product_price_modal')
            </div>
        @endif

        {!! Form::hidden("products_variation[]", $product->product_variation_id); !!}

         <!-- Description modal end -->
        @if(in_array('modifiers' , $enabled_modules))
            <div class="modifiers_html">
                @if(!empty($product->product_ms))
                    @include('restaurant.product_modifier_set.modifier_for_product', array('edit_modifiers' => true, 'row_count' => $loop->index, 'product_ms' => $product->product_ms ) )
                @endif
            </div>
        @endif

        @php
       
            if($status == "quotation" ||   $status == "draft" || $status == "ApprovedQuotation" || ( $status == "proforma"  && !isset($transaction_stock)) ){

                $max_quantity = 10000000000000;

            }else if ( $status == "proforma"  && isset($transaction_stock)) {
                $qty_available_e = 0;
                foreach ($transaction_stock as $key => $value) {
                    if($value->product_id == $product->product_id){
                        $qty_available_e = $qty_available_e + $value->product_qty ;
                    }
                }
                $max_quantity = $qty_available_e;
                $max_quantity = 10000000000000;
                
            }else if(!empty($action) && $action == 'edit') {
                // $store_id    =  $sell_line->store_id;
              
                        ;
                $warehouses   =  \App\Models\Warehouseinfo::where('store_id',$warehouse)
                                                ->where('product_id',$sell_line->product_id)
                                                ->select(DB::raw("SUM(product_qty) as stock"))->first();
                // $max_quantity = $warehouses->stock;
                $max_quantity = 10000000000000;
                $formatted_max_quantity = $max_quantity;
                
            }else {
                $max_quantity = $warehouse->stock;
            }
            $formatted_max_quantity = $max_quantity;

            if(!empty($action) && $action == 'edit') {
                if(!empty($so_line)) {
                    $qty_available = $so_line->quantity - $so_line->so_quantity_invoiced + $product->quantity_ordered;
                    // $max_quantity = $qty_available;
                    $max_quantity = 10000000000000;
                    $formatted_max_quantity = number_format($qty_available, config('constants.currency_precision', 2), session('currency')['decimal_separator'], session('currency')['thousand_separator']);
                }
            } else {
                if(!empty($so_line) && $so_line->qty_available <= $max_quantity) {
                    $max_quantity = $so_line->qty_available;
                    $formatted_max_quantity = $so_line->formatted_qty_available;
                }
            }
            
             
            $max_qty_rule = $max_quantity;
            $max_qty_msg = __('validation.custom-messages.quantity_not_available', ['qty'=> $formatted_max_quantity, 'unit' => $product->unit  ]);
        @endphp
 
        @if( session()->get('business.enable_lot_number') == 1 || session()->get('business.enable_product_expiry') == 1)
            @php
                $lot_enabled = session()->get('business.enable_lot_number');
                $exp_enabled = session()->get('business.enable_product_expiry');
                $lot_no_line_id = '';
                if(!empty($product->lot_no_line_id)){
                    $lot_no_line_id = $product->lot_no_line_id;
                }
            @endphp
            @if(!empty($product->lot_numbers) && empty($is_sales_order))
                <select class="form-control lot_number input-sm" name="products[{{$row_count}}][lot_no_line_id]" @if(!empty($product->transaction_sell_lines_id)) disabled @endif>
                    <option value="">@lang('lang_v1.lot_n_expiry')</option>
                    @foreach($product->lot_numbers as $lot_number)
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

                            //preselected lot number if product searched by lot number
                            if(!empty($purchase_line_id) && $purchase_line_id == $lot_number->purchase_line_id) {
                                $selected = "selected";

                                $max_qty_rule = $lot_number->qty_available;
                                $max_qty_msg = __('lang_v1.quantity_error_msg_in_lot', ['qty'=> $lot_number->qty_formated, 'unit' => $product->unit  ]);
                            }
                        @endphp
                        <option value="{{$lot_number->purchase_line_id}}" data-qty_available="{{$lot_number->qty_available}}" data-msg-max="@lang('lang_v1.quantity_error_msg_in_lot', ['qty'=> $lot_number->qty_formated, 'unit' => $product->unit  ])" {{$selected}}>@if(!empty($lot_number->lot_number) && $lot_enabled == 1){{$lot_number->lot_number}} @endif @if($lot_enabled == 1 && $exp_enabled == 1) - @endif @if($exp_enabled == 1 && !empty($lot_number->exp_date)) @lang('product.exp_date'): {{@format_date($lot_number->exp_date)}} @endif {{$expiry_text}}</option>
                    @endforeach
                </select>
            @endif
        @endif
        @if(!empty($is_direct_sell))
            <br>
            @if($sell_line_note == null || $sell_line_note == "") 
                <?php $pr =  \App\Product::find($product->product_id);  $cypher = Illuminate\Support\Facades\Crypt::encryptString(".");  ?>
                <div class="description_line" data-line="{{$row_count}}">
                    <pre style="white-space: nowrap;max-width:300px;max-height:150px" class="btn btn-modal products_details" data-href="{{action('ProductController@changeDescription', ['id'=>$product->product_id,'text'=> $cypher  ,'line'=>$row_count])}}" data-container=".view_modal">{!! "Enter Description" !!}</pre>
                </div>
            @else 
                <?php $pr =  \App\Product::find($product->product_id);  $cypher = Illuminate\Support\Facades\Crypt::encryptString($sell_line_note);  ?>
                    <div class="description_line" data-line="{{$row_count}}">
                        <pre style="white-space: nowrap;max-width:300px;max-height:150px" class="btn btn-modal products_details" data-href="{{action('ProductController@changeDescription', ['id'=>$product->product_id,'text'=> $cypher  ,'line'=>$row_count])}}" data-container=".view_modal">{!! $sell_line_note !!}</pre>
                    </div>
            @endif
            <textarea class="form-control control_products_details"   data-line="{{$row_count}}"  name="products[{{$row_count}}][sell_line_note]" rows="2" style="max-width:200px;white-space: normal; word-break: break-word;word-wrap: 
                    break-word;  
                    width: min-intrinsic;
                    width: -webkit-min-content;
                    width: -moz-min-content;
                    width: min-content;
                    display: table-caption;
                    display: -ms-grid;
                    -ms-grid-columns: min-content;visibility:hidden
                        "  > {!! $sell_line_note !!}</textarea>
            <!--<p class="help-block"><small>@lang('lang_v1.sell_line_description_help')</small></p>-->
        @endif

    </td>
   

    {{-- quantity --}}
    <td>
        {{-- If edit then transaction sell lines will be present --}}
        @if(!empty($product->transaction_sell_lines_id))
            <input type="hidden" name="products[{{$row_count}}][transaction_sell_lines_id]" class="form-control" value="{{$product->transaction_sell_lines_id}}">
        @endif

        <input type="hidden" name="products[{{$row_count}}][product_id]" class="form-control product_id" value="{{$product->product_id}}">

        <input type="hidden" value="{{$product->variation_id}}"
               name="products[{{$row_count}}][variation_id]" class="row_variation_id">

        <input type="hidden" value="{{$product->enable_stock}}"
               name="products[{{$row_count}}][enable_stock]">

        @if(empty($product->quantity_ordered))
            @php
                $product->quantity_ordered = 1;
            
            @endphp
        @else
        <?php $max_quantity +=$product->quantity_ordered; 
              $max_qty_rule = $max_quantity;
        ?>
        @endif

        @php
            $multiplier = 1;
            $allow_decimal = true;
            if($product->unit_allow_decimal != 1) {
                $allow_decimal = false;
            }
        @endphp
        @foreach($sub_units as $key => $value)
            @if(!empty($product->sub_unit_id) && $product->sub_unit_id == $key)
                @php
                    $multiplier   = $value['multiplier'];
                    $max_qty_rule = $max_qty_rule / $multiplier;
                    $unit_name    = $value['name'];
                    $max_qty_msg  = __('validation.custom-messages.quantity_not_available', ['qty'=> $max_qty_rule, 'unit' => $unit_name  ]);

                    if(!empty($product->lot_no_line_id)){
                        $max_qty_msg = __('lang_v1.quantity_error_msg_in_lot', ['qty'=> $max_qty_rule, 'unit' => $unit_name  ]);
                    }

                    if($value['allow_decimal']) {
                        $allow_decimal = true;
                    }
                @endphp
            @endif
        @endforeach
        <div class="input-group input-number">
            <span class="input-group-btn"><button type="button" class="btn btn-default btn-flat quantity-down"><i class="fa fa-minus text-danger"></i></button></span>
            <input type="text" min="1" data-min="1"
                   class="form-control pos_quantity input_number mousetrap "
                   value="{{@format_quantity($product->quantity_ordered)}}" name="products[{{$row_count}}][quantity]" data-allow-overselling="@if(empty($pos_settings['allow_overselling'])){{'false'}}@else{{'true'}}@endif"
                   @if($allow_decimal)
                   data-decimal=1
                   @else
                   data-decimal=0
                   data-rule-abs_digit="true"
                   data-msg-abs_digit="@lang('lang_v1.decimal_value_not_allowed')"
                   @endif
                   data-rule-required="true"
                   data-msg-required="@lang('validation.custom-messages.this_field_is_required')"
                   @if($product->enable_stock && empty($pos_settings['allow_overselling']) && empty($is_sales_order) )
                   data-rule-max-value="{{$max_qty_rule}}" data-qty_available="{{$max_quantity}}" data-msg-max-value="{{$max_qty_msg}}"
                   data-msg_max_default="@lang('validation.custom-messages.quantity_not_available', ['qty'=> $max_quantity, 'unit' => $product->unit  ])"
                @endif
            >
            <span class="input-group-btn"><button type="button" class="btn btn-default btn-flat quantity-up"><i class="fa fa-plus text-success"></i></button></span>
        </div>
        @php $count = 0;  @endphp
        <input type="hidden" name="products[{{$row_count}}][product_unit_id]" value="{{$product->unit_id}}">
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

        <input type="hidden" class="base_unit_multiplier" name="products[{{$row_count}}][base_unit_multiplier]" value="{{$multiplier}}">

        <input type="hidden" class="hidden_base_unit_sell_price" value="{{$product->default_sell_price / $multiplier}}">

        {{-- Hidden fields for combo products --}}
        @if($product->product_type == 'combo'&& !empty($product->combo_products))

            @foreach($product->combo_products as $k => $combo_product)

                @if(isset($action) && $action == 'edit')
                    @php
                        $combo_product['qty_required'] = $combo_product['quantity'] / $product->quantity_ordered;

                        $qty_total = $combo_product['quantity'];
                    @endphp
                @else
                    @php
                        $qty_total = $combo_product['qty_required'];
                    @endphp
                @endif

                <input type="hidden"
                       name="products[{{$row_count}}][combo][{{$k}}][product_id]"
                       value="{{$combo_product['product_id']}}">

                <input type="hidden"
                       name="products[{{$row_count}}][combo][{{$k}}][variation_id]"
                       value="{{$combo_product['variation_id']}}">

                <input type="hidden"
                       class="combo_product_qty"
                       name="products[{{$row_count}}][combo][{{$k}}][quantity]"
                       data-unit_quantity="{{$combo_product['qty_required']}}"
                       value="{{$qty_total}}">

                @if(isset($action) && $action == 'edit')
                    <input type="hidden"
                           name="products[{{$row_count}}][combo][{{$k}}][transaction_sell_lines_id]"
                           value="{{$combo_product['id']}}">
                @endif

            @endforeach
        @endif
    </td>

    @if($status ==  "o" || $status ==  "quotation" || $status == "delivered" ||  $status ==  "ApprovedQuotation" ||  $status ==  "proforma"  || $status ==  "draft"  || $action == 'edit'  )
    @else
       
        <td> {{ Form::select('stores_id[]',$childs,$store_id,['class'=>'form-control']) }} </td>
    @endif

    @if(!empty($is_direct_sell))
        @if(!empty($pos_settings['inline_service_staff']))
            <td>
                <div class="form-group">
                    <div class="input-group">
                        {!! Form::select("products[" . $row_count . "][res_service_staff_id]", $waiters, !empty($product->res_service_staff_id) ? $product->res_service_staff_id : null, ['class' => 'form-control select2 order_line_service_staff', 'placeholder' => __('restaurant.select_service_staff'), 'required' => (!empty($pos_settings['is_service_staff_required']) && $pos_settings['is_service_staff_required'] == 1) ? true : false ]); !!}
                    </div>
                </div>
            </td>
        @endif
        @php
            $pos_unit_price = !empty($product->unit_price_before_discount) ? $product->unit_price_before_discount : $product->default_sell_price;
            if(!empty($so_line)) {
                $pos_unit_price = $so_line->unit_price_before_discount;
            }
        @endphp
        
        <td @if(!auth()->user()->can('edit_product_price_from_sale_screen')) hide @endif>
            <input type="text" name="products[{{$row_count}}][unit_price]" class="form-control pos_unit_price input_number mousetrap" id="start_pos_unit_bf" value="{{$pos_unit_price}}">
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
          {{-- Unit Price Inc.vat --}}
        <td class="">
            <input type="text"   name=" products[{{$row_count}}][unit_price_inc_tax]" class="form-control  inc_vat_p" value="{{$unit_price_inc_tax_last}}" @if($edit_price)   @endif @if(!empty($pos_settings['enable_msp'])) data-rule-min-value="{{$unit_price_inc_tax_last}}" data-msg-min-value="{{__('lang_v1.minimum_selling_price_error_msg', ['price' => $unit_price_inc_tax_last])}}" @endif>
            <input type="hidden" name="products[{{$row_count}}][default_purchase_price]" class="form-control default_purchase_price input_number" value="{{$product->last_purchased_price}}" >
        </td>
        @php
            $price_final     = $pos_unit_price;
            $price_tax_final = $unit_price_inc_tax_last  ;
        @endphp
        <td @if(!auth()->user()->can('edit_product_price_from_sale_screen')) hide @endif @if($currency != "" && $currency != 0) class="text-center curr_column  cur_check " @else class="text-center curr_column  cur_check hide" @endif>
            <input type="text" name="products[{{$row_count}}][unit_price_new_currency]" class="form-control pos_unit_price_new_currency input_number mousetrap" id="start_pos_unit_bf" value="{{($currency != "" && $currency != 0)?$price_final/$currency:0}}">
        </td>
          {{-- Unit Price Inc.vat --}}
        <td class=" hide">
            <input type="text"   name=" products[{{$row_count}}][unit_price_inc_tax_new_currency]" class="form-control  inc_vat_p_new_currency" value="{{($currency != "" && $currency != 0)?$price_tax_final/$currency:0}}" @if($edit_price)   @endif @if(!empty($pos_settings['enable_msp'])) data-rule-min-value="{{($currency != "" && $currency != 0)?$price_tax_final/$currency:0}}" data-msg-min-value="{{__('lang_v1.minimum_selling_price_error_msg', ['price' => $unit_price_inc_tax_last])}}" @endif>
            <input type="hidden" name="products[{{$row_count}}][default_purchase_price_new_currency]" class="form-control default_purchase_price_new_currency input_number" value="{{($currency != "" && $currency != 0)?$price_tax_final/$currency:0}}" >
        </td>

        <td @if(!$edit_discount) hide @endif>
            @php
                // dd($discount_type);
            @endphp
            {!! Form::text("products[$row_count][line_discount_amount___]", ($discount_amount), ['class' => 'form-control input_number row_discount_amount' ,"id"=>"fixed_dis"]); !!}<br>
            {!! Form::select("products[$row_count][line_discount_type]", ['fixed' => __('lang_v1.fixed'), 'percentage' => __('lang_v1.percentage')], $discount_type , ['class' => 'form-control row_discount_type hide']); !!}
            @if(!empty($discount))
                <p class="help-block">{!! __('lang_v1.applied_discount_text', ['discount_name' => $discount->name, 'starts_at' => $discount->formated_starts_at, 'ends_at' => $discount->formated_ends_at]) !!}</p>
            @endif
        </td>
        
        <td class="text-center {{$hide_tax}}">
               {!! Form::hidden("products[$row_count][item_tax]", ($unit_price_inc_tax_last-$pos_unit_price), ['class' => 'item_tax']); !!}
            {!! Form::select("products[$row_count][tax_id]", $tax_dropdown['tax_rates'], $tax_id, ['placeholder' => 'Select', 'class' => 'form-control tax_id'], $tax_dropdown['attributes']); !!}
        </td>

        <td @if(!$edit_discount) hide @endif>
    
            {!! Form::text("products[$row_count][line_discount_amount]", ($pos_unit_price!=0)?@num_format(($discount_amount*100)/$pos_unit_price):0, ['class' => 'form-control input_number row_discount_percentage' ,"id"=>"percent_dis[$row_count]"]); !!}<br>
            {!! Form::select("products[$row_count][line_discount_type]", ['fixed' => __('lang_v1.fixed'), 'percentage' => __('lang_v1.percentage')], "percentage" , ['class' => 'form-control row_discount_type hide']); !!}
            @if(!empty($discount))
                <p class="help-block">{!! __('lang_v1.applied_discount_text', ['discount_name' => $discount->name, 'starts_at' => $discount->formated_starts_at, 'ends_at' => $discount->formated_ends_at]) !!}</p>
            @endif
        </td>
    @else
        {!! Form::select("products[$row_count][warranty_id]", $warranties, $warranty_id, ['placeholder' => __('messages.please_select'), 'class' => 'form-control']); !!}

        @if(!empty($pos_settings['inline_service_staff']))
            <td>
                <div class="form-group">
                    <div class="input-group">
                        {!! Form::select("products[" . $row_count . "][res_service_staff_id]", $waiters, !empty($product->res_service_staff_id) ? $product->res_service_staff_id : null, ['class' => 'form-control select2 order_line_service_staff', 'placeholder' => __('restaurant.select_service_staff'), 'required' => (!empty($pos_settings['is_service_staff_required']) && $pos_settings['is_service_staff_required'] == 1) ? true : false ]); !!}
                    </div>
                </div>
            </td>
        @endif

    @endif

    
    {{-- unit cost exc --}}
    <td>
        {!! Form::text("cost_exc", $pos_unit_price, ['class' => 'form-control input_number  pos_unit_price_os ' ,"id"=> "cost_exc[$row_count]" ,"readonly"]); !!}<br>
    </td>
    {{-- unit cost inc --}}
    <td>
        {!! Form::text("cost_inc", $unit_price_inc_tax_last, ['class' => 'form-control input_number   unit_price_inc_tax_last_os ',"id"=> "cost_inc[$row_count]"  ,"readonly"]); !!}<br>
    </td>
    
    {{-- unit cost exc --}}
    <td @if($currency != "" && $currency != 0) class="text-center curr_column  cur_check " @else class="text-center curr_column  cur_check hide" @endif>
        {!! Form::text("cost_exc_new_currency",($currency != "" && $currency != 0 )?$pos_unit_price/$currency:0 , ['class' => 'form-control input_number  pos_unit_price_os_new_currency ' ,"id"=> "cost_exc_new_currency[$row_count]" ,"readonly"]); !!}<br>
    </td>
    {{-- unit cost inc --}}
    <td class="text-center curr_column   hide">
        {!! Form::text("cost_inc_new_currency", ($currency != "" && $currency != 0)?$unit_price_inc_tax_last/$currency:0, ['class' => 'form-control input_number   unit_price_inc_tax_last_os_new_currency ',"id"=> "cost_inc_new_currency[$row_count]"  ,"readonly"]); !!}<br>
    </td>
   


    {{-- warranty --}}
    @if(!empty($common_settings['enable_product_warranty']) && !empty($is_direct_sell))
        <td>
            {!! Form::select("products[$row_count][warranty_id]", $warranties, $warranty_id, ['placeholder' => __('messages.please_select'), 'class' => 'form-control']); !!}
        </td>
    @endif

 
    {{--Total --}}
    <td class="text-center">
        @php
            $subtotal_type = !empty($pos_settings['is_pos_subtotal_editable']) ? 'text' : 'hidden';
        @endphp
        {{-- <input type="{{$subtotal_type}}"  class="form-control pos_line_total @if(!empty($pos_settings['is_pos_subtotal_editable'])) input_number @endif" value="{{@num_format($product->quantity_ordered*$unit_price_inc_tax_ )}}"> --}}
        <input type="{{$subtotal_type}}" class="form-control pos_line_total @if(!empty($pos_settings['is_pos_subtotal_editable'])) input_number @endif" value="{{@num_format($product->quantity_ordered*$unit_price_inc_tax )}}">
        <span class="display_currency pos_line_total_text @if(!empty($pos_settings['is_pos_subtotal_editable'])) hide @endif" data-currency_symbol="true">{{$product->quantity_ordered*$unit_price_inc_tax}}</span>
    </td>
    
    {{-- button remove --}}
    <td class="text-center " style="padding-top: 10px;" >
        <i class="fa fa-times  pos_remove_row cursor-pointer btn btn-danger" aria-hidden="true"></i>
    </td>

</tr>
@section('inner_script')
<script>
        $(document).ready(function() {
                ClassicEditor
                .create(document.querySelector('#product_description'))
                .catch(error => {
                    console.error(error);
                });
            });
        $('.row_discount_percentage').change(function(){
            var  el     =  $(this).parent().parent();
            var dis     =  parseFloat($(this).val());
            var price   =  el.children().find('.pos_unit_price').val();
            var amount  =  parseFloat(price)*(dis/100);
            el.children().find('.row_discount_amount').val(amount.toFixed(4));
            update_os();
            os_discount();
        })
        $('.row_discount_amount').change(function(){
           
            var  el =  $(this).parent().parent();
            var dis =  parseFloat($(this).val());
            var price =  el.children().find('.pos_unit_price').val();
            var amount  =  (dis/price)*100;
            el.children().find('.row_discount_percentage').val(amount.toFixed(4));
            update_os();
            os_discount();
        })
        $('.pos_unit_price').change(function(){
  
            var  el    =  $(this).parent().parent();
            var price  =  parseFloat($(this).val());
            var vat    = 0;
            if ($('#tax_rate_id option:selected').data('rate')) {
                var vat =  parseFloat($('#tax_rate_id option:selected').data('rate'));
            }
            var x =  price + ((vat/100)*price);
            // console.log(x+'vat');
            el.children().find('.inc_vat_p').val(x.toFixed(4));
            el.children().find('.pos_unit_price_inc_tax').val(x.toFixed(4));
            update_os();
            os_discount();
        })
        $('.inc_vat_p').change(function(){
    
            var  el    =  $(this).parent().parent();
            var price  =  parseFloat($(this).val());
            el.children().find('.pos_unit_price_inc_tax').val(price);
            var vat    = 0;
            if ($('#tax_rate_id option:selected').data('rate')) {
                var vat =  parseFloat($('#tax_rate_id option:selected').data('rate'));
            }
            var x =  price/( (vat/100) +1);;
            el.children().find('.pos_unit_price').val(x.toFixed(4));
            update_os();
            os_discount();
        })
        // function update_os() {
        //     var amount = 0;
        //     var vat    = 0;
        //     if ($('#tax_rate_id option:selected').data('rate')) {
        //         var vat =  parseFloat($('#tax_rate_id option:selected').data('rate'));
        //     }
        //     $('.row_discount_percentage').each(function(){
        //         var  el    =  $(this).parent().parent();
        //         var price  =  parseFloat(el.children().find('.pos_unit_price').val(),10);
        //         var dis    = parseFloat(el.children().find('.row_discount_amount').val(),10);
        //         el.children().find('.pos_unit_price_os').val((price - dis).toFixed(2));
        //         var tax_price = parseFloat(el.children().find('.inc_vat_p').val()) ;
        //         el.children().find('.unit_price_inc_tax_last_os').val((tax_price - dis).toFixed(2));
        //         var qty  = parseFloat(el.children().find('.pos_quantity').val(),10) ;
        //         var row_amount  =  ((price - dis)*qty).toFixed(2);
        //         amount = parseFloat(amount) + parseFloat(row_amount) ;

        //         el.children().find('.pos_line_total_text').text(row_amount+'AED');
        //         el.children().find('.pos_line_total').val(row_amount);
                
        //     });
        //     amount =  parseFloat(amount,10);
        //     $('.price_total').text(amount.toFixed(2));
        //     var tax  =  parseFloat((vat/100)*amount,10);
        //     console.log(tax+'fixed_tax'+amount)
        //     $('#order_tax').text(tax.toFixed(2));

        //     var x=   parseFloat(amount,10) + parseFloat(tax,10);
        //     $('#total_payable').text(x.toFixed(2));
        // }
        function update_os() {
            
            var amount  = 0;
            var vat     = 0;
            var total_quantity = 0;
            if ($('#tax_rate_id option:selected').data('rate')) {
                var vat =  parseFloat($('#tax_rate_id option:selected').data('rate'));
            }
            $('.row_discount_percentage').each(function(){
                var  el    =  $(this).parent().parent();
                var price  =  parseFloat(el.children().find('.pos_unit_price').val(),10);
                var dis    =  0;
                if (el.children().find('.row_discount_amount').val()) {
                dis =  parseFloat(el.children().find('.row_discount_amount').val(),10); 
                }
                currancy = $(".currency_id_amount").val();
      
                if(currancy != "" && currancy != 0){
                    el.children().find('.pos_unit_price_new_currency').val((price/currancy).toFixed(4));
                    el.children().find('.inc_vat_p_new_currency').val((tax_price/currancy).toFixed(4));
                }else{
                    el.children().find('.pos_unit_price_new_currency').val(0);
                    el.children().find('.inc_vat_p_new_currency').val(0);
                }
                el.children().find('.pos_unit_price_os').val((price - dis).toFixed(2));
                var tax_price   = parseFloat(el.children().find('.inc_vat_p').val()) ;
                var qty         = parseFloat(el.children().find('.pos_quantity').val(),10) ;
                var row_amount  =  ((price - dis)*qty).toFixed(4);
                var after_discount_tax =  parseFloat(el.children().find('.pos_unit_price_os').val());
                el.children().find('.item_tax').val(after_discount_tax*(vat/100));
                var row_amount_tax  =  ( after_discount_tax + after_discount_tax*(vat/100));
                amount = parseFloat(amount) + parseFloat(row_amount) ;
                el.children().find('.unit_price_inc_tax_last_os').val((row_amount_tax).toFixed(4));
                el.children().find('.pos_line_total_text').text((row_amount_tax*qty));
                el.children().find('.pos_line_total').val((row_amount));
                total_quantity += qty;
                if(currancy != "" && currancy != 0){
                    el.children().find('.pos_unit_price_os_new_currency').val(after_discount_tax/currancy);
                    el.children().find('.unit_price_inc_tax_last_os_new_currency').val(row_amount_tax/currancy);
                }else{
                    el.children().find('.pos_unit_price_os_new_currency').val(0);
                    el.children().find('.unit_price_inc_tax_last_os_new_currency').val(0);
                }
                
            });
            change_discount();
            amount   =  parseFloat(amount,10);
            $('.price_total').text(amount.toFixed(4));
            $('.price_total_curr').text((amount.toFixed(2)/currancy).toFixed(4));
            var tax  =  parseFloat((vat/100)*amount,10);
            currancy = $(".currency_id_amount").val();
            if(currancy != "" && currancy != 0){
                $('#order_tax_curr').text((tax.toFixed(4)/currancy).toFixed(4));
            }
            $('#order_tax').text(tax.toFixed(4));
            $('#tax_calculation_amount').val(tax.toFixed(4));
            
            var x=   parseFloat(amount,10) + parseFloat(tax,10) ;
            $('#total_payable').text(x.toFixed(2));
            if(currancy != "" && currancy != 0){
                // $('#total_payable_curr').text(((x/currancy).toFixed(4)).toFixed(2)/currancy).toFixed(4);
            }
            $('#final_total_input').val(x.toFixed(4));
            $('#amount_0').val(x);
            $('.total_quantity').text(total_quantity);
            os_discount();

            
        }
        function pos_discount(total_amount) {
            var calculation_type = $('#discount_type').val();
            var calculation_amount = __read_number($('#discount_amount'));

            var discount = __calculate_amount(calculation_type, calculation_amount, total_amount);
            currany = $(".currency_id_amount").val();
            $('span#total_discount').text(__currency_trans_from_en(discount*currany, false));
            $('span#total_discount_curr').text(__currency_trans_from_en((discount).toFixed(4), false));
            

            return discount;
        }
        function os_discount() {
          
          var type = $('#discount_type').val();

          var discount_amount  =  0;
          var sub_total        =  0;
          var vat              =  0;
          var order_tax        =  0;
          if ($('#tax_rate_id option:selected').data('rate')) {
              vat  =  $('#tax_rate_id option:selected').data('rate');
          }
          $('.pos_line_total').each(function(){
              sub_total +=  parseFloat($(this).val());
          })
          order_tax = sub_total*(vat/100);
          
          if ($('#discount_amount').val() > 0 ) {
              discount_amount =  $('#discount_amount').val();
          }
          if (type == 'fixed_before_vat') {
              $('#total_discount').text(discount_amount);
              var order_tax   =  (sub_total - discount_amount)*(vat/100);
              currancy = $(".currency_id_amount").val();
              if(currancy != "" && currancy != 0){
                  var order_tax   =  (sub_total - (discount_amount*currancy))*(vat/100);
                  $('#total_discount_curr').text((parseFloat(discount_amount)).toFixed(4));
                  $('#total_discount').text((discount_amount*currancy).toFixed(4));
                  $('#order_tax_curr').text((order_tax.toFixed(4)/currancy).toFixed(4));
              }
              $('#order_tax').text(order_tax.toFixed(4));
              $('#tax_calculation_amount').val(order_tax.toFixed(4));
              

          }else if(type == 'fixed_after_vat'){
              var x =  (discount_amount*100)/(100+parseFloat(vat));
              $('#total_discount').text(x.toFixed(4));
              var order_tax   =  (sub_total - x)*(vat/100);
              $('#order_tax').text(order_tax.toFixed(4));
              $('#tax_calculation_amount').val(order_tax.toFixed(4));
              currancy = $(".currency_id_amount").val();
              if(currancy != "" && currancy != 0){
                  var order_tax   =  (sub_total - (x*currancy))*(vat/100);
                  $('#total_discount_curr').text(parseFloat(x).toFixed(4));
                  $('#total_discount').text((x*currancy).toFixed(4));
                  $('#order_tax_curr').text((order_tax.toFixed(4)/currancy).toFixed(4));
              }
              $('#order_tax').text(order_tax.toFixed(4));
              $('#endregionorder_tax').text(order_tax.toFixed(4));
              discount_amount =  x; 
          }else if (type ==  'percentage') {
              var x =  sub_total*(discount_amount/100);
              var order_tax   =  (sub_total - x)*(vat/100);
              $('#order_tax').text(parseFloat(order_tax).toFixed(4));
              $('#tax_calculation_amount').val(order_tax.toFixed(4));
              
              currancy = $(".currency_id_amount").val();
              if(currancy != "" && currancy != 0){
                  $('#total_discount_curr').text((x/currancy).toFixed(4));
                  $('#order_tax_curr').text((order_tax.toFixed(4)/currancy).toFixed(4));
                  $('#total_discount').text(x.toFixed(4));
                  discount_amount = (x/currancy).toFixed(4); 
              }else{
                  discount_amount =  x; 

              }
          }
          currancy = $(".currency_id_amount").val();
          if (type && $('#discount_amount').val() > 0 ) {
              var ship = $('#shipping_charges').val();
              if(ship == ""){
                  ship =  0;
              }
              var final =  sub_total -  discount_amount + order_tax + parseFloat(ship);
              
              if(currancy != "" && currancy != 0){
                    final =  sub_total -  (discount_amount*currancy) + order_tax + parseFloat(ship);
                    $('#total_payable').text(final.toFixed(4));
                    $('#total_payable_curr').text(parseFloat(final/currancy).toFixed(4));
              }else{
                  $('#total_payable').text(final.toFixed(4));
              } 

              $('#final_total_input').val(final.toFixed(4));
              $('#amount_0').val(final);  
          }else{
              var ship = $('#shipping_charges').val();
              if(ship == ""){
                  ship =  0;
              }
              var final =  sub_total + order_tax + parseFloat(ship);
              $('#total_payable').text(final.toFixed(4));
              if(currancy != "" && currancy != 0){
                  $('#order_tax_curr').text(parseFloat(order_tax/currancy).toFixed(4));
                  $('#total_payable_curr').text(parseFloat(final/currancy).toFixed(4));
              }
              $('#amount_0').val(final);
          }
          $('#order_tax').text(parseFloat(order_tax).toFixed(4));
          $('#tax_calculation_amount').val(order_tax.toFixed(4));
              
          
      }
</script>
@endsection
