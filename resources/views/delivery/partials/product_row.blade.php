@php
    $common_settings = session()->get('business.common_settings');
      $sell_line =  \App\TransactionSellLine::first(); 
      if (!isset($action)) {
        $action = 'add';
      }
@endphp
<tr class="product_row" data-row_index="{{$row_count}}" @if(!empty($so_line)) data-so_id="{{$so_line->transaction_id}}" @endif>
   {{--Product name--}}
    <td> 
        <a target="_blank" href="/item-move/{{$product->product_variation_id}}">
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
            <span class=" cursor-pointer" data-toggle="modals" data-target="#row_edit_product_price_modal_{{$row_count}}">
                {!! $product_name !!}
                &nbsp;
            </span>
                </div>
            @else
                {!! $product_name !!}
            @endif
        </a>
        <input type="hidden" class="enable_stock" value="{{$product->enable_stock}}">
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

            $tax_id = $product->tax_id;
            $item_tax = !empty($product->item_tax) ? $product->item_tax : 0;
            $unit_price_inc_tax = $product->sell_price_inc_tax;

            if(!empty($so_line)) {
                $tax_id = $so_line->tax_id;
                $item_tax = $so_line->item_tax;
            }

            if($hide_tax == 'hide'){
                $tax_id = null;
                $unit_price_inc_tax = $product->default_sell_price;
            }

            $discount_type = !empty($product->line_discount_type) ? $product->line_discount_type : 'fixed';
            $discount_amount = !empty($product->line_discount_amount) ? $product->line_discount_amount : 0;

            if(!empty($discount)) {
                $discount_type = $discount->discount_type;
                $discount_amount = $discount->discount_amount;
            }

            if(!empty($so_line)) {
                $discount_type = $so_line->line_discount_type;
                $discount_amount = $so_line->line_discount_amount;
            }

              $sell_line_note = '';
              if(!empty($product->sell_line_note)){
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
            $max_quantity = $available_qty;
       
            $formatted_max_quantity = $max_quantity;

            if(!empty($action) && $action == 'edit') {
                if(!empty($so_line)) {
                    $qty_available = $so_line->quantity - $so_line->so_quantity_invoiced + $product->quantity_ordered;
                    $max_quantity = $qty_available;
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
                                $max_qty_msg  = __('lang_v1.quantity_error_msg_in_lot', ['qty'=> $lot_number->qty_formated, 'unit' => $product->unit  ]);
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
            <?php $pr =  \App\Product::find($product->product_id);  ?>
            <textarea class="form-control" name="products[{{$row_count}}][sell_line_note]" rows="2"> {{ strip_tags($pr->product_description) }}</textarea>
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
        <input type="hidden" value="{{$product->product_id}}"
               name="products[{{$row_count}}][product_ids]" class="row_product_id">
        <input type="hidden" value="{{$product->enable_stock}}"
               name="products[{{$row_count}}][enable_stock]">

        @if(empty($product->quantity_ordered))
            @php
                $product->quantity_ordered = 1;
            @endphp
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
                    $multiplier = $value['multiplier'];
                    $max_qty_rule = $max_qty_rule / $multiplier;
                    $unit_name = $value['name'];
                    $max_qty_msg = __('validation.custom-messages.quantity_not_available', ['qty'=> $max_qty_rule, 'unit' => $unit_name  ]);

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
            <span  class="input-group-btn"><button type="button"    class="btn btn-default btn-flat quantity-down"><i class="fa fa-minus text-danger"></i></button></span>
            <input type="text" data-min="1"
                   max="{{$max_quantity}}"
                   class="form-control  pos_quantity   mousetrap  "
                   value="{{  @format_quantity($product->quantity_ordered) }}" name="products[{{$row_count}}][quantity]" data-allow-overselling="@if(empty($pos_settings['allow_overselling'])){{'false'}}@else{{'true'}}@endif"
                   @if($allow_decimal)
                   data-decimal=1
                   @else
                   data-decimal=0
                   data-rule-abs_digit="true"
                   data-msg-abs_digit="@lang('lang_v1.decimal_value_not_allowed')"
                   @endif
                   data-ebrahem="hi"
                   data-rule-required="true"
                   data-msg-required="@lang('validation.custom-messages.this_field_is_required')"
                   @if($product->enable_stock && empty($pos_settings['allow_overselling']) && empty($is_sales_order) )
                   data-rule-max-value="{{$max_qty_rule}}" data-qty_available="{{$max_quantity}}" data-msg-max-value="{{$max_qty_msg}}"
                   data-msg_max_default="@lang('validation.custom-messages.quantity_not_available', ['qty'=> $max_quantity, 'unit' => $product->unit  ])"
                @endif
            >
            <span  class="input-group-btn"><button type="button"      class="btn btn-default btn-flat quantity-up"><i class="fa fa-plus text-success"></i></button></span>
        </div>

        <input type="hidden" name="products[{{$row_count}}][product_unit_id]" value="{{$product->unit_id}}">
      @if(count($sub_units) > 0)
            <br>
            <select name="products[{{$row_count}}][sub_unit_id]" class="form-control input-sm sub_unit">
                @foreach($sub_units as $key => $value)
                    <option value="{{$key}}" data-multiplier="{{$value['multiplier']}}" data-unit_name="{{$value['name']}}" data-allow_decimal="{{$value['allow_decimal']}}" @if(!empty($product->sub_unit_id) && $product->sub_unit_id == $key) selected @endif>
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
        
        @if(isset($list_of_prices))
            <br>
            <select name="products[{{$row_count}}][price_from_bill]"  style="padding:5px;width:100%" id="price_from_bill">
                 @foreach ($list_of_prices as $k => $item)
                    <option value="{{$k}}" data-line_id="{{$k}}">{{$item}}</option>
                 @endforeach
            </select>
        @endif
    </td>
     @if($status ==  "o" || $status ==  "quotation"   ||  $status ==  "ApprovedQuotation" ||  $status ==  "proforma"  || $status ==  "draft"  || $action == 'edit'  )
     @else
         <td>
             <select name="stores_id[]" id="stor" class="form-control  drive_store_id"  required>
                 @foreach($childs as $key=>$value)
                      <option value="{{ $key }}" data-max="{{ $value['available_qty'] }}" {{ ($key == $store_id)?'selected':'' }}> {{ $value['name'] }}  </option>
                @endforeach
             </select>
        </td>

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
            <input type="text" name="products[{{$row_count}}][unit_price]" class="form-control pos_unit_price input_number mousetrap" value="{{@num_format($pos_unit_price)}}">
         </td>
    <td @if(!$edit_discount) hide @endif>
        @php
            // dd($discount_type);
        @endphp
        
        {!! Form::text("products[$row_count][line_discount_amount]", @num_format($discount_amount), ['class' => 'form-control input_number row_discount_amount']); !!}<br>
        {!! Form::select("products[$row_count][line_discount_type]", ['fixed' => __('lang_v1.fixed'), 'percentage' => __('lang_v1.percentage')], $discount_type , ['class' => 'form-control row_discount_type hide']); !!}
        @if(!empty($discount))
            <p class="help-block">{!! __('lang_v1.applied_discount_text', ['discount_name' => $discount->name, 'starts_at' => $discount->formated_starts_at, 'ends_at' => $discount->formated_ends_at]) !!}</p>
        @endif
    </td>

    <td class="text-center {{$hide_tax}}">
        {!! Form::hidden("products[$row_count][item_tax]", @num_format($item_tax), ['class' => 'item_tax']); !!}
        {!! Form::select("products[$row_count][tax_id]", $tax_dropdown['tax_rates'], $tax_id, ['placeholder' => 'Select', 'class' => 'form-control tax_id'], $tax_dropdown['attributes']); !!}
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

    {{--Unit price --}}
    <td class="{{$hide_tax}}">
         <input type="text"   name="products[{{$row_count}}][unit_price_inc_tax]" class="form-control pos_unit_price_inc_tax input_number" value="{{@num_format($unit_price_inc_tax)}}" @if($edit_price) readonly @endif @if(!empty($pos_settings['enable_msp'])) data-rule-min-value="{{$unit_price_inc_tax}}" data-msg-min-value="{{__('lang_v1.minimum_selling_price_error_msg', ['price' => @num_format($unit_price_inc_tax)])}}" @endif>
         <input type="hidden" name="products[{{$row_count}}][default_purchase_price]" class="form-control default_purchase_price input_number" value="{{@num_format($product->last_purchased_price)}}" >

     </td>
     <td @if(!$edit_discount) hide @endif>
    
        {!! Form::text("products[$row_count][line_discount_amount]", @num_format($discount_amount), ['class' => 'form-control input_number row_discount_amount']); !!}<br>
        {!! Form::select("products[$row_count][line_discount_type]", ['fixed' => __('lang_v1.fixed'), 'percentage' => __('lang_v1.percentage')], "percentage" , ['class' => 'form-control row_discount_type hide']); !!}
        @if(!empty($discount))
            <p class="help-block">{!! __('lang_v1.applied_discount_text', ['discount_name' => $discount->name, 'starts_at' => $discount->formated_starts_at, 'ends_at' => $discount->formated_ends_at]) !!}</p>
        @endif
    </td>
    {{--   discount % --}}
    {{-- <td class=" ">
         <input type="text"   name="products[{{$row_count}}][unit_price_inc_tax]" class="form-control pos_unit_price_inc_tax input_number" value="{{@num_format($unit_price_inc_tax)}}" @if($edit_price) readonly @endif @if(!empty($pos_settings['enable_msp'])) data-rule-min-value="{{$unit_price_inc_tax}}" data-msg-min-value="{{__('lang_v1.minimum_selling_price_error_msg', ['price' => @num_format($unit_price_inc_tax)])}}" @endif>
         <input type="hidden" name="products[{{$row_count}}][default_purchase_price]" class="form-control default_purchase_price input_number" value="{{@num_format($product->last_purchased_price)}}" >

     </td> --}}
    {{--   discount  fixed --}}
    {{-- <td class="{{$hide_tax}} ">
         <input type="text"   name="products[{{$row_count}}][unit_price_inc_tax]" class="form-control pos_unit_price_inc_tax input_number" value="{{@num_format($unit_price_inc_tax)}}" @if($edit_price) readonly @endif @if(!empty($pos_settings['enable_msp'])) data-rule-min-value="{{$unit_price_inc_tax}}" data-msg-min-value="{{__('lang_v1.minimum_selling_price_error_msg', ['price' => @num_format($unit_price_inc_tax)])}}" @endif>
         <input type="hidden" name="products[{{$row_count}}][default_purchase_price]" class="form-control default_purchase_price input_number" value="{{@num_format($product->last_purchased_price)}}" >

     </td> --}}


    @if(!empty($common_settings['enable_product_warranty']) && !empty($is_direct_sell))
        <td>
            {!! Form::select("products[$row_count][warranty_id]", $warranties, $warranty_id, ['placeholder' => __('messages.please_select'), 'class' => 'form-control']); !!}
        </td>
    @endif


    {{--Added by eng ali to show unit price in pos--}}
   {{-- @if(empty($is_direct_sell))
    <td>
        {{$product->sell_price_inc_tax}}
        <span class="display_currency pos_line_total_text " data-currency_symbol="true">{{$product->quantity_ordered*$unit_price_inc_tax}}</span>
    </td>
    @endif--}}




    {{--Total --}}
    <td class="text-center hide"  >
        @php
            $subtotal_type = !empty($pos_settings['is_pos_subtotal_editable']) ? 'text' : 'hidden';

        @endphp
        <input type="{{$subtotal_type}}" class="form-control pos_line_total @if(!empty($pos_settings['is_pos_subtotal_editable'])) input_number @endif" value="{{@num_format($product->quantity_ordered*$unit_price_inc_tax )}}">
        <span class="display_currency pos_line_total_text @if(!empty($pos_settings['is_pos_subtotal_editable'])) hide @endif" data-currency_symbol="true">{{$product->quantity_ordered*$unit_price_inc_tax}}</span>
    </td>
    <td class="text-center " style="padding-top: 10px;" >
        <i class="fa fa-times  pos_remove_row cursor-pointer btn btn-danger" aria-hidden="true"></i>
    </td>
</tr>
<script>
    $(".drive_store_id").each(function(){
        
        $(this).on("change",function(){
            stores_max();
        });
    });
    hide_items();

    function stores_max(){
         $(".drive_store_id").each(function(){
            var el =  $(this).parent().parent();
            var max = el.children().find('.drive_store_id option:selected').data('max');
            console.log(max + "max");
            var val = el.children().find('.pos_quantity').val();
            var pos_qty = el.children().find('.pos_quantity');
            pos_qty.attr('max',max);
            pos_qty.attr("data-ebrahem" ,""+max );
            pos_qty.attr("data-rule-max-value" , ""+max );
            pos_qty.data("rule-max-value" , ""+max);
            pos_qty.attr("data-msg-max-value" ,"Only " + max + " available"  );
            pos_qty.attr("data-msg-max-default" , "Only " + max + " available" );
            pos_qty.attr("data-msg_max_default" , "Only " + max + " available" );
            if (val > max) {    
                pos_qty.val(max)
            }
            $("label.error").remove();
            pos_qty.removeClass("error");
        });
    }

    $('.pos_quantity').each(function(){
        var el = $(this).on("input",function(){
            var max = el.attr("max");
            if(el.val()<=max){
                $("label.error").remove();  
                el.children().find('.pos_quantity').removeClass("error");
            }   
            max_quantity();   
         })
        var el = $(this).on("change",function(){
            var max = el.attr("max");
            if(el.val()<=max){
                // check_error();
            }   
            max_quantity();   
         })
    })
    
    // function check_max_os() {
    //     var total_qty = 0;
    //     var val = $('table#pos_table tbody tr').each(function(){
    //             var el = $(this).find(".pos_quantity");

    //             total_qty += parseFloat(el.val()) ;
    //     });
    //     update_items_total(total_qty)
    // }
     
    function hide_items(){
		$("#pos_table tbody  td:nth-child(4)").hide();
		$("#pos_table tbody  td:nth-child(6)").hide();
		$("#pos_table tbody  td:nth-child(8)").hide();
		$("#pos_table tbody  td:nth-child(7)").hide();
		$("#pos_table tbody  td:nth-child(9)").hide();
 	}
    // function check_max_os() {
    //     $('.pos_quantity').each(function(){
    //         var max = $(this).attr('max');
    //         var val = $(this).val()
    //         if(val > max){ $(this).addClass('error')}else{ $(this).removeClass('error')}
    //         console.log(max+'____'+val)
    //     })
    // }
   

</script>