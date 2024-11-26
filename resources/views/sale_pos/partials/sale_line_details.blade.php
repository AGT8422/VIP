{{-- **1********* IF HAVE ITEMS **********1**  --}}
@if(count($sell->sell_lines)>0) 
    <table class="table bg-gray">
        <tr class="bg-green" style="text-align:center !important">
        <th  style="text-align:center !important">#</th>
        <th  style="text-align:center !important">{{ __('sale.product') }}</th>
        @if( session()->get('business.enable_lot_number') == 1)
            <th  style="text-align:center !important">{{ __('lang_v1.lot_n_expiry') }}</th>
        @endif
        <th  style="text-align:center !important">{{ __('sale.qty') }}</th>
        @if(!empty($pos_settings['inline_service_staff']))
            <th  style="text-align:center !important">
                @lang('restaurant.service_staff')
            </th>
        @endif
        <th  style="text-align:center !important" >@lang( 'lang_v1.unit_cost_before_discount' )</th>
        <th  style="text-align:center !important">@lang('home.Price Including Tax')</th>
        <th  style="text-align:center !important">{{ __('sale.discount') }}</th>
        <th  style="text-align:center !important" class="no-print text-right">@lang('purchase.unit_cost_before_tax')</th>
        <th  style="text-align:center !important" class="no-print text-right">@lang('purchase.subtotal_before_tax')</th>
        <th  style="text-align:center !important">{{ __('sale.tax') }}</th>
        <th  style="text-align:center !important" class="text-right">@lang('purchase.unit_cost_after_tax')</th>
        <th  style="text-align:center !important">{{ __('sale.subtotal') }}</th>
    </tr>
    @php
        
        $currency = \App\Currency::find($sell->business->currency_id); 
        $currency_symbol =isset($currency)?$currency->symbol:"";

        $qty_check = 0; 
        $product_check = 0; 
        $unit_price_before_discount_check = 0; 
        $unit_price_inc_tax_check = 0; 
        $line_discount_amount_check = 0; 
        $tax_check = 0;
        $id_array = []; 
        @endphp
    @if(isset($type_archive) && isset($new))
        @php $sells = $new_sell;   @endphp
    @else
        @php $sells = $sell;   @endphp 
    @endif
    @if(isset($type_archive))  
        @foreach($sell->sell_lines as $sell_old)
            @foreach($new_sell->sell_lines as $it)  
                @if($sell_old->new_id == $it->id)
                    @php
                        $qty_old = $it->quantity; 
                        $qty_new = $sell_old->quantity; 
                        if($qty_old!=$qty_new){$qty_check = 1;if(!in_array($sell_old->id,$id_array)){$id_array[]=$sell_old->id;} }
                        $product_old =$it->product->id; 
                        $product_new =$sell_old->product->id; 
                        if($product_old!=$product_new){$product_check = 1;if(!in_array($sell_old->id,$id_array)){$id_array[]=$sell_old->id;}}
                        $unit_price_before_discount_old = $it->unit_price_before_discount; 
                        $unit_price_before_discount_new = $sell_old->unit_price_before_discount; 
                        if($unit_price_before_discount_old!=$unit_price_before_discount_new){$unit_price_before_discount_check = 1;if(!in_array($sell_old->id,$id_array)){$id_array[]=$sell_old->id;}}
                        $unit_price_inc_tax_old    = $it->unit_price_inc_tax; 
                        $unit_price_inc_tax_new    = $sell_old->unit_price_inc_tax; 
                        if($unit_price_inc_tax_old!= $unit_price_inc_tax_new){$unit_price_inc_tax_check = 1;if(!in_array($sell_old->id,$id_array)){$id_array[]=$sell_old->id;}}
                        $line_discount_amount_old  = $it->line_discount_amount; 
                        $line_discount_amount_new  = $sell_old->line_discount_amount; 
                        if($line_discount_amount_old!=$line_discount_amount_new){$line_discount_amount_check = 1;if(!in_array($sell_old->id,$id_array)){$id_array[]=$sell_old->id;}}
                        $tax_old = $it->transaction->tax->amount ;
                        $tax_new = $sell_old->transaction->tax->amount;
                        if($tax_old!=$tax_new){$tax_check = 1;if(!in_array($sell_old->id,$id_array)){$id_array[]=$sell_old->id;}} 
                    @endphp
                
                @endif
            @endforeach 
        @endforeach 
    @endif
                  
                    
        
    @foreach($sells->sell_lines as $sell_line)
       
        
        <tr style="text-align:center">
            <td>{{ $loop->iteration }}</td>
            <td style="width:30% !important;text-align:left" @if($product_check == 1 && in_array($sell_line->id,$id_array)) class="change-bill"  @endif >
                
                @if( $sell_line->product->type == 'variable')
                - {{ $sell_line->variations->product_variation->name ?? ''}}
                - {{ $sell_line->variations->name ?? ''}},
                @endif
                {{ $sell_line->variations->sub_sku ?? ''}}
                @php
                $brand = $sell_line->product->brand;
                @endphp
                @if(!empty($brand->name))
                , {{$brand->name}}
                @endif

                @if(!empty($sell_line->sell_line_note))
                <br> {{strip_tags($sell_line->sell_line_note)}}
                @endif
                <br>

                @if($is_warranty_enabled && !empty($sell_line->warranties->first()) )
                    <br><small  style="background-color:#f1f1f1 ; padding:5px;margin: 10px 0px">{{$sell_line->warranties->first()->display_name ?? ''}} - {{ @format_date($sell_line->warranties->first()->getEndDate($sell->transaction_date))}}</small>
                    @if(!empty($sell_line->warranties->first()->description))
                    <br><small>{{$sell_line->warranties->first()->description ?? ''}}</small>
                    @endif
                @endif

                {{-- @if(in_array('kitchen', $enabled_modules))
                    <br><span class="label @if($sell_line->res_line_order_status == 'cooked' ) bg-red @elseif($sell_line->res_line_order_status == 'served') bg-green @else bg-light-blue @endif">@lang('restaurant.order_statuses.' . $sell_line->res_line_order_status) </span>
                @endif --}}
             
                <br>
                <br>
             
        
            @if(!isset($type_archive))
                <div class="btn-group"><button type="button" class="btn btn-info dropdown-toggle btn-xs no-print" data-toggle="dropdown" aria-expanded="false">@lang("messages.actions")<span class="caret"></span><span class="sr-only">Toggle Dropdown</span></button><ul class="dropdown-menu dropdown-menu-left" role="menu">
                    <li><a data-href="{{action('ProductController@view', [ $sell_line->product->id])}}" class="btn-modal" data-container=".view_modal"><i class="fa fa-eye"></i>@lang("messages.view")</a></li>
                    <li><a href="{{action('ProductController@edit', [ $sell_line->product->id])}}"><i class="glyphicon glyphicon-edit"></i>@lang("messages.edit")</a></li>
                    <li><a href="{{action('ItemMoveController@index', [$sell_line->product->id])}}"><i class="fas fa-history"></i>@lang("lang_v1.product_stock_history")</a></li>
                  </ul>
                    <button type="button" style="margin-left:10px" class="btn btn-primary btn-xs btn-modal no-print" id="view_s" data-container=".view_modal" data-href="{{action('ProductController@viewStock', [$sell_line->product->id])}}">@lang('lang_v1.view_Stock')</button> 
                    <button type="button" style="margin-left:10px" class="btn bg-yellow btn-xs btn-modal no-print" data-container=".view_modal" data-href="{{action('ProductController@viewDelivered', [$sell_line->product->id])}}">@lang('recieved.should_delivery')</button> 
                </div>
            @endif
            </td>
            @if( session()->get('business.enable_lot_number') == 1)
                <td>{{ $sell_line->lot_details->lot_number ?? '--' }}
                    @if( session()->get('business.enable_product_expiry') == 1 && !empty($sell_line->lot_details->exp_date))
                    ({{@format_date($sell_line->lot_details->exp_date)}})
                    @endif
                </td>
            @endif
            {{-- QUANTITY --}}
            <td>
                <span  @if($qty_check == 1 && in_array($sell_line->id,$id_array) ) class="change-bill  "  @else class=" "  @endif    data-is_quantity="true">{{ $sell_line->quantity }}</span> @if(!empty($sell_line->sub_unit)) {{$sell_line->sub_unit->short_name}} @else {{$sell_line->product->unit->short_name}} @endif
            </td>
            @if(!empty($pos_settings['inline_service_staff']))
                <td>
                {{ $sell_line->service_staff->user_full_name ?? '' }}
                </td>
            @endif
            {{-- UNIT PRICE WITHOUT DIS --}}
            <td>
                @if(auth()->user()->hasRole('Admin#' . session('business.id')) ||  auth()->user()->can('product.avarage_cost'))
                    <span @if($unit_price_before_discount_check == 1 && in_array($sell_line->id,$id_array) ) class="change-bill  "  @else class=" "  @endif  data-currency_symbol="true">{{ number_format($sell_line->unit_price_before_discount,2) }} {{ " " .$currency_symbol}}</span>
                @else
                    {{ "--" }}
                @endif
            </td>
         
             {{-- UNIT PRICE WITHOUT DIS WITH VAT --}}
            <td>
                @if(auth()->user()->hasRole('Admin#' . session('business.id')) ||  auth()->user()->can('product.avarage_cost'))
                    <span @if($unit_price_inc_tax_check == 1 && in_array($sell_line->id,$id_array)) class="change-bill  "  @else class=" "  @endif  data-currency_symbol="true">{{ number_format($sell_line->unit_price_inc_tax  ,2) }}  {{ " " .$currency_symbol}}</span>
                @else
                    {{ "--" }}
                @endif
            </td>
             {{--   DIS  AMOUNT --}}
            <td>
                @if(auth()->user()->hasRole('Admin#' . session('business.id')) ||  auth()->user()->can('product.avarage_cost'))
                    <span @if($line_discount_amount_check == 1 && in_array($sell_line->id,$id_array)) class="change-bill  "  @else class=" "  @endif  data-currency_symbol="true">{{ $sell_line->get_discount_amount_s() }}</span> @if($sell_line->line_discount_type == 'percentage') ({{($sell_line->unit_price_before_discount !=0)?number_format(($sell_line->line_discount_amount*100)/$sell_line->unit_price_before_discount,2):number_format($sell_line->line_discount_amount,2)}}%)  @endif
                @else
                    {{ "--" }}
                @endif               
            </td>
            {{--   Unit Price (After Dis) Includ.vat --}}
            @php

                $PRICE = $sell_line->unit_price_before_discount - $sell_line->get_discount_amount_s() + $sell_line->item_tax;

            @endphp
            <td>
                @if(auth()->user()->hasRole('Admin#' . session('business.id')) ||  auth()->user()->can('product.avarage_cost'))
                    <span class=" " data-currency_symbol=" ">{{ number_format($PRICE,2) }}  {{ " " .$currency_symbol}}</span> 
                @else
                    {{ "--" }}
                @endif
            </td>
            {{-- Subtotal (Before Tax) --}}
            <td>
                @if(auth()->user()->hasRole('Admin#' . session('business.id')) ||  auth()->user()->can('product.avarage_cost'))
                    <span class=" " data-currency_symbol=" ">{{ number_format(($PRICE - $sell_line->item_tax) * $sell_line->quantity  ,2) }}  {{ " " .$currency_symbol}}</span> 
                @else
                    {{ "--" }}
                @endif       
            </td>
            <td >
                @if(auth()->user()->hasRole('Admin#' . session('business.id')) ||  auth()->user()->can('product.avarage_cost'))
                    <span @if($tax_check == 1 && in_array($sell_line->id,$id_array)) class="change-bill  "  @else class=" "  @endif  data-currency_symbol=" ">{{ number_format( $sell_line->item_tax,2)  }}  {{ " " .$currency_symbol}}</span> 
                    @if(!empty($taxes[$sell_line->tax_id]))
                    ( {{ $taxes[$sell_line->tax_id] }} )
                    @endif
                @else
                    {{ "--" }}
                @endif
            </td>
            <td>
                @if(auth()->user()->hasRole('Admin#' . session('business.id')) ||  auth()->user()->can('product.avarage_cost'))
                    <span class=" " data-currency_symbol=" ">{{ number_format($PRICE,2) }}  {{ " " .$currency_symbol}}</span>
                @else
                    {{ "--" }}
                @endif
            </td>
            <td>
                @if(auth()->user()->hasRole('Admin#' . session('business.id')) ||  auth()->user()->can('product.avarage_cost'))
                    <span class=" " data-currency_symbol=" ">{{ number_format($sell_line->quantity * $PRICE,2) }}  {{ " " .$currency_symbol}}</span>
                @else
                    {{ "--" }}
                @endif
            </td>
        </tr>
        @if(!empty($sell_line->modifiers))
        @foreach($sell_line->modifiers as $modifier)
            <tr>
                <td>&nbsp;</td>
                <td>
                    {{ $modifier->product->name }} - {{ $modifier->variations->name ?? ''}},
                    {{ $modifier->variations->sub_sku ?? ''}}
                </td>
                @if( session()->get('business.enable_lot_number') == 1)
                    <td>&nbsp;</td>
                @endif
                <td>{{ $modifier->quantity }}</td>
                <td>
                    <span class="display_currency" data-currency_symbol="true">{{ $modifier->unit_price }}</span>
                </td>
                <td>
                    &nbsp;
                </td>
                <td>
                    <span class="display_currency" data-currency_symbol="true">{{ $modifier->item_tax }}</span> 
                    @if(!empty($taxes[$modifier->tax_id]))
                    ( {{ $taxes[$modifier->tax_id]}} )
                    @endif
                </td>
                <td>
                    <span class="display_currency" data-currency_symbol="true">{{ $modifier->unit_price_inc_tax }}</span>
                </td>
                <td>
                    <span class="display_currency" data-currency_symbol="true">{{ $modifier->quantity * $modifier->unit_price_inc_tax }}</span>
                </td>
            </tr>
            @endforeach
        @endif
    @endforeach
</table>
@else
{{-- **2********* IF NO HAVE ITEMS *******2**  --}}
    <table class="table bg-gray">
        <tr class="bg-green" style="text-align:center !important">
            <th  style="text-align:center !important">#</th>
            <th  style="text-align:center !important">{{ __('sale.product') }}</th>
            @if( session()->get('business.enable_lot_number') == 1)
                <th  style="text-align:center !important">{{ __('lang_v1.lot_n_expiry') }}</th>
            @endif
            <th  style="text-align:center !important">{{ __('sale.qty') }}</th>
            @if(!empty($pos_settings['inline_service_staff']))
                <th  style="text-align:center !important">
                    @lang('restaurant.service_staff')
                </th>
            @endif
            <th  style="text-align:center !important" >@lang( 'lang_v1.unit_cost_before_discount' )</th>
            <th  style="text-align:center !important">@lang('home.Price Including Tax')</th>
            <th  style="text-align:center !important">{{ __('sale.discount') }}</th>
            <th  style="text-align:center !important" class="no-print text-right">@lang('purchase.unit_cost_before_tax')</th>
            <th  style="text-align:center !important" class="no-print text-right">@lang('purchase.subtotal_before_tax')</th>
            <th  style="text-align:center !important">{{ __('sale.tax') }}</th>
            <th  style="text-align:center !important" class="text-right">@lang('purchase.unit_cost_after_tax')</th>
            <th  style="text-align:center !important">{{ __('sale.subtotal') }}</th>
        </tr>
        @php
            $qty_check = 0; 
            $product_check = 0; 
            $unit_price_before_discount_check = 0; 
            $unit_price_inc_tax_check = 0; 
            $line_discount_amount_check = 0; 
            $tax_check = 0;
            $id_array = []; 
            @endphp
        @if(isset($type_archive) && isset($new))
            @php $sells = $new_sell;   @endphp
        @else
            @php $sells = $sell;   @endphp 
        @endif
        @if(isset($type_archive))  
            @foreach($sell->sell_lines as $sell_old)
                @foreach($new_sell->sell_lines as $it)  
                    @if($sell_old->new_id == $it->id)
                        @php
                            $qty_old = $it->quantity; 
                            $qty_new = $sell_old->quantity; 
                            if($qty_old!=$qty_new){$qty_check = 1;if(!in_array($sell_old->id,$id_array)){$id_array[]=$sell_old->id;} }
                            $product_old =$it->product->id; 
                            $product_new =$sell_old->product->id; 
                            if($product_old!=$product_new){$product_check = 1;if(!in_array($sell_old->id,$id_array)){$id_array[]=$sell_old->id;}}
                            $unit_price_before_discount_old = $it->unit_price_before_discount; 
                            $unit_price_before_discount_new = $sell_old->unit_price_before_discount; 
                            if($unit_price_before_discount_old!=$unit_price_before_discount_new){$unit_price_before_discount_check = 1;if(!in_array($sell_old->id,$id_array)){$id_array[]=$sell_old->id;}}
                            $unit_price_inc_tax_old    = $it->unit_price_inc_tax; 
                            $unit_price_inc_tax_new    = $sell_old->unit_price_inc_tax; 
                            if($unit_price_inc_tax_old!= $unit_price_inc_tax_new){$unit_price_inc_tax_check = 1;if(!in_array($sell_old->id,$id_array)){$id_array[]=$sell_old->id;}}
                            $line_discount_amount_old  = $it->line_discount_amount; 
                            $line_discount_amount_new  = $sell_old->line_discount_amount; 
                            if($line_discount_amount_old!=$line_discount_amount_new){$line_discount_amount_check = 1;if(!in_array($sell_old->id,$id_array)){$id_array[]=$sell_old->id;}}
                            $tax_old = $it->transaction->tax->amount ;
                            $tax_new = $sell_old->transaction->tax->amount;
                            if($tax_old!=$tax_new){$tax_check = 1;if(!in_array($sell_old->id,$id_array)){$id_array[]=$sell_old->id;}} 
                        @endphp
                    
                    @endif
                @endforeach 
            @endforeach 
        @endif
        @foreach($sell->payment_lines  as $line_sep) 
            <tr style="text-align:center">
                {{-- # ordered --}}
                <td>{{ "1" }}</td>
                {{-- description --}}
                <td style="width:30% !important;text-align:left"   >
                    {{ $line_sep->note }}
                </td>
                {{-- lot_number --}}
                @if( session()->get('business.enable_lot_number') == 1)
                    <td>{{ $sell_line->lot_details->lot_number ?? '--' }}
                        @if( session()->get('business.enable_product_expiry') == 1 && !empty($sell_line->lot_details->exp_date))
                        ({{@format_date($sell_line->lot_details->exp_date)}})
                        @endif
                    </td>
                @endif
                {{-- QUANTITY --}}
                <td>
                    <span     class="display_currency"   data-currency_symbol="false"  data-is_quantity="true">{{ "1" }}</span>  
                </td>
                @php 
                    $tax_amount                          = \App\TaxRate::find($sell->tax_id);
                    $value                               = ($tax_amount)?$tax_amount->amount:0;
                    $subtotal_line                       = $line_sep->amount * 100 / ( 100 + $value) ; 
                    $value_tax                           = $line_sep->amount * $value / ( 100 + $value) ;
                @endphp
                @if(!empty($pos_settings['inline_service_staff']))
                    <td>
                    {{ ""  }}
                    </td>
                @endif
                {{-- UNIT PRICE WITHOUT DIS --}}
                <td>
                 
                    <span class="display_currency" data-currency_symbol="true">{{ $subtotal_line }}</span>
                </td>
            
                {{-- UNIT PRICE WITHOUT DIS WITH VAT --}}
                <td>
                    <span class="display_currency" data-currency_symbol="true">{{ $line_sep->amount }}</span>
                </td>
                {{--   DIS  AMOUNT --}}
                <td>
                    <span class="display_currency" data-currency_symbol="true">{{ 0 }}</span>   
                </td>
                {{--   Unit Price (After Dis) Includ.vat --}}
                <td>
                    <span class="display_currency" data-currency_symbol="true">{{ $line_sep->amount   }}</span>
                </td>
                    
                </td>
                {{-- Subtotal (Before Tax) --}}
                <td>
                    <span class="display_currency" data-currency_symbol="true">{{ 1 * $subtotal_line }}</span>
                </td>
                {{--   Tax   --}}
                <td >
                    <span class="display_currency" data-currency_symbol="true">{{ $value_tax }}</span>
                    
                </td>
                {{-- Unit Cost Price (After Tax) --}}
                <td>
                    <span class="display_currency" data-currency_symbol="true">{{ 1 * $line_sep->amount }}</span>
                    
                </td>
                {{-- Subtotal --}}
                <td>
                    <span class="display_currency" data-currency_symbol="true">{{ 1 * $subtotal_line }}</span>
                     
                </td>
            </tr>
            @if(!empty($sell_line->modifiers))
                @foreach($sell_line->modifiers as $modifier)
                    <tr>
                        <td>&nbsp;</td>
                        <td>
                            {{ $modifier->product->name }} - {{ $modifier->variations->name ?? ''}},
                            {{ $modifier->variations->sub_sku ?? ''}}
                        </td>
                        @if( session()->get('business.enable_lot_number') == 1)
                            <td>&nbsp;</td>
                        @endif
                        <td>{{ $modifier->quantity }}</td>
                        <td>
                            <span class="display_currency" data-currency_symbol="true">{{ $modifier->unit_price }}</span>
                        </td>
                        <td>
                            &nbsp;
                        </td>
                        <td>
                            <span class="display_currency" data-currency_symbol="true">{{ $modifier->item_tax }}</span> 
                            @if(!empty($taxes[$modifier->tax_id]))
                            ( {{ $taxes[$modifier->tax_id]}} )
                            @endif
                        </td>
                        <td>
                            <span class="display_currency" data-currency_symbol="true">{{ $modifier->unit_price_inc_tax }}</span>
                        </td>
                        <td>
                            <span class="display_currency" data-currency_symbol="true">{{ $modifier->quantity * $modifier->unit_price_inc_tax }}</span>
                        </td>
                    </tr>
                @endforeach
            @endif
         @endforeach
    </table>
@endif