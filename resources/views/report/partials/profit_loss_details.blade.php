    <div class="col-xs-12">
        @php @endphp 
        @component('components.widget')
            <table class="table table-striped">
                {{-- sales --}}
                <tr >
                    <th style="border-top:1px solid #00000034" >{{ __('home.total_sell') }}: <br>
                        <!-- sub type for total sales -->
                        @if(count($data['total_sell_by_subtype']) > 1)
                            <ul>
                                @foreach($data['total_sell_by_subtype'] as $sell)
                                    <li>
                                        <span class="display_currency" data-currency_symbol="true">
                                            {{$sell->total_before_tax}}    
                                        </span>
                                        @if(!empty($sell->sub_type))
                                            &nbsp;<small class="text-muted">({{ucfirst($sell->sub_type)}})</small>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                        <small class="text-muted"> 
                            (@lang('product.exc_of_tax'), @lang('sale.discount'))
                        </small>
                    </th>
                    @php
                        if($data['total_sell']<0){ $types = "   CR";  }else{ $types = "  DR"; }
                    @endphp
                    <td style="border-top:1px solid #00000034">
                        <span class="display_currency" data-currency_symbol="true">{{($data['total_sell']<0)?abs($data['total_sell']) : $data['total_sell'] }} </span>  {{" "}} .  {{$types}} <br>
                    </td>
                </tr>
                {{-- Sales Account --}}
                @foreach($data['total_sale_account'] as $key => $it)
                    @php if($it<0){ $tp = "   CR"; }elseif($it==0){ $tp = " ";}else{     $tp = "   CD"; } @endphp
                    @php $account = \App\Account::find($key);  @endphp
                    <tr>
                        <th>
                            <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</b>
                            <b>{{"##"}}</b>
                            {{$account->name . " : "}}
                        </th>
                        <td>
                            <span class="display_currency" data-currency_symbol="true">{{($it<0)?abs($it):$it  }} </span>  {{" "}} .  {{$tp}} <br>
                        </td>
                    </tr>
                @endforeach
                {{-- additional shipping cost --}}
                <tr>
                    @php
                        if(( $data['total_sell_shipping_charge'])<0 ){ $sell_shipping_type = "   CR"; }elseif($data['total_sell_shipping_charge'] == 0){$sell_shipping_type = " ";}else{ $sell_shipping_type = "   DB";}
                    @endphp
                    <th style="border-top:1px solid #00000034">{{ __('lang_v1.total_sell_shipping_charge') }}:</th>
                    <td style="border-top:1px solid #00000034">
                        <span class="display_currency" data-currency_symbol="true">{{($data['total_sell_shipping_charge']<0)?abs($data['total_sell_shipping_charge']):$data['total_sell_shipping_charge']}}</span> {{ " " . $sell_shipping_type}}
                    </td>
                </tr>
                {{-- sale return  --}}
                <tr>
                    @php
                        if(( $data['total_sell_return'])<0){$sell_return_type = "   CR"; }elseif($data['total_sell_return'] == 0){$sell_return_type = " ";}else{$sell_return_type = "   DR";}
                    @endphp
                    <th>{{ __('lang_v1.total_sell_return') }}:</th>
                    <td>
                        <span class="display_currency" data-currency_symbol="true">{{($data['total_sell_return'])?abs($data['total_sell_return']):$data['total_sell_return']}}</span> {{ " " . $sell_return_type }}
                    </td>
                </tr>
                {{-- sale discount --}}
                <tr>
                    @php
                        if(( $data['total_sell_discount'])<0){$sell_discount_type = "   CR"; }elseif($data['total_sell_discount'] == 0){$sell_discount_type = " ";}else{$sell_discount_type = "   DR";}
                    @endphp
                    <th>{{ __('lang_v1.total_sell_discount') }}:</th>
                    <td>
                        <span class="display_currency" data-currency_symbol="true">{{$data['total_sell_discount']}}</span> {{ " " . $sell_discount_type}}
                    </td>
                </tr>
                {{-- net sales --}}
                <tr>
                    <th>
                        <p class="text-muted mb-0" style="background-color:#c3c3c3;color:rgb(70, 70, 70)  ;padding:10px">
                            {{ __('lang_v1.net_sales') }}: 
                        </p>
                    </th>
                    <th>
                        @php
                            $net_sales = abs($data['total_sell']) +  abs($data['total_sell_shipping_charge']) - abs($data['total_sell_return']) - abs($data['total_sell_discount']) ;
                            $net_sales *=  -1;
                            if($net_sales<0){$net_sale_type = "   CR"; }elseif($net_sales == 0){$net_sale_type = " ";}else{$net_sale_type = "   DR";}
                        @endphp
                        <p class="text-muted mb-0" style="background-color:#c3c3c3;color:rgb(70, 70, 70)  ;padding:10px">
                            <span class="display_currency" data-currency_symbol="true">
                                {{
                                ($net_sales<0)?abs($net_sales):$net_sales
                                }}
                            </span>
                            {{ " " . $net_sale_type}}
                        </p>
                    </th>
                </tr>
                {{-- Purchase --}}
                <tr>
                    <th style="border-top:1px solid #00000034">{{ __('home.total_purchase') }}:<br><small class="text-muted">(@lang('product.exc_of_tax'), @lang('sale.discount'))</small></th>
                    @php
                        if($data['total_purchase']<0){ $types = "   CR";  }else{ $types = "   DR"; }
                    @endphp
                    <td style="border-top:1px solid #00000034">
                        <span class="display_currency" data-currency_symbol="true">{{$data['total_purchase']}}</span>  {{" "}} . {{$types}}
                    </td>
                </tr>
                {{-- Purchase Account --}}
                @foreach($data['total_purchase_account'] as $key => $it)
                    @php if($it<0){ $tp = "   CR"; }elseif($it==0){ $tp = " ";}else{     $tp = "   DR"; } @endphp
                    @php $account = \App\Account::find($key) ;  @endphp
                    <tr>
                        <th>
                            <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</b>
                            <b>{{"##"}}</b>
                            {{$account->name . " : "}}
                        </th>
                        <td>
                            <span class="display_currency" data-currency_symbol="true">{{($it<0)?abs($it):$it  }} </span>  {{" "}} .  {{$tp}} <br>
                        </td>
                    </tr>
                @endforeach
                {{-- opening stock --}}
                <tr>
                    @php 
                        if($data['opening_stock']<0){$open_type = "   CR";}elseif($data['opening_stock']==0){$open_type = " ";}else{$open_type = "   DR";}
                    @endphp
                    <th style="border-top:1px solid #00000034">{{ __('report.opening_stock') }} <br><small class="text-muted">(@lang('lang_v1.by_purchase_price'))</small>:</th>
                    <td style="border-top:1px solid #00000034">
                        <span class="display_currency" data-currency_symbol="true">{{($data['opening_stock']<0)?abs($data['opening_stock']):$data['opening_stock']}}</span>   {{ " " }} . {{$open_type}}
                    </td>
                </tr>
                {{-- additional shipping cost --}}
                <tr>
                    @php
                        if(( $data['total_purchase_shipping_charge'])<0 ){ $purchase_shipping_type = "   CR"; }elseif($data['total_purchase_shipping_charge'] == 0){$purchase_shipping_type = " ";}else{ $purchase_shipping_type = "   DR";}
                    @endphp
                    <th style="border-top:1px solid #00000034">{{ __('lang_v1.total_purchase_shipping_charge') }}:</th>
                    <td style="border-top:1px solid #00000034">
                        <span class="display_currency" data-currency_symbol="true">{{$data['total_purchase_shipping_charge']}}</span> {{ " " . $purchase_shipping_type}}
                    </td>
                </tr>
                {{-- purchase return  --}}
                <tr>
                    @php
                        if(( $data['total_purchase_return'])<0 ){ $purchase_return_type = "   CR"; }elseif($data['total_purchase_return'] == 0){$purchase_return_type = " ";}else{ $purchase_return_type = "   DR";}
                    @endphp
                    <th>{{ __('lang_v1.total_purchase_return') }}:</th>
                    <td>
                        <span class="display_currency" data-currency_symbol="true">{{$data['total_purchase_return']}}</span> {{ " " . $purchase_return_type }}
                    </td>
                </tr>
                {{-- purchase discount --}}
                <tr>
                    @php
                        if(( $data['total_purchase_discount'])<0 ){ $purchase_discount_type = "   CR"; }elseif($data['total_purchase_discount'] == 0){$purchase_discount_type = " ";}else{ $purchase_discount_type = "   DR";}
                    @endphp
                    <th>{{ __('lang_v1.total_purchase_discount') }}:</th>
                    <td>
                        <span class="display_currency" data-currency_symbol="true">{{($data['total_purchase_discount']<0)?abs($data['total_purchase_discount']):$data['total_purchase_discount']}}</span> {{ " " . $purchase_discount_type}}
                    </td>
                </tr>
                {{-- closing stock --}}
                <tr>
                    @php
                        if(( $closing )<0 ){ $closing_type = "   CR"; }elseif($closing == 0){$closing_type = " ";}else{ $closing_type = "   DR";}
                    @endphp
                    <th style="border-top:1px solid #00000034;border-bottom:1px solid #00000034">{{ __('report.closing_stock') }} <br><small class="text-muted">(@lang('lang_v1.by_purchase_price'))</small>:</th>
                    <td style="border-top:1px solid #00000034;border-bottom:1px solid #00000034">
                        <span class="display_currency" data-currency_symbol="true">{{($closing<0)?(abs($closing)):$closing }}</span> {{ " " . $closing_type}}
                    </td>
                </tr>
                {{-- net purchase --}}
                <tr>
                <th>
                    <p class="text-muted mb-0" style="background-color:#c3c3c3;color:rgb(70, 70, 70)  ;padding:10px">
                        {{ __('Cost Of Sales') }}: 
                        </p>
                </th>
                <th>
                    @php
                        $net_purchase = abs($data['total_purchase']) +   abs($data['total_purchase_shipping_charge']) -  abs($data['total_purchase_return']) -  abs($data['total_purchase_discount']) +  abs($data['opening_stock']) -  abs($closing) ;
                        $net_purchase *= -1;
                        if(($net_purchase)<0){$net_purchase_type = "   CR"; }elseif(($net_purchase) == 0){$net_purchase_type = " ";}else{$net_purchase_type = "   DR";}
                    @endphp
                    <p class="text-muted mb-0" style="background-color:#c3c3c3;color:rgb(70, 70, 70)  ;padding:10px">
                        <span class="display_currency" data-currency_symbol="true">
                            {{
                                ($net_purchase<0)?abs($net_purchase):$net_purchase
                            }}
                        </span>
                        {{ " " . $net_purchase_type}}
                    </p>
                </th>
                </tr>
                {{-- cross profit --}}
                <tr>
                    <th>
                        <p class="text-muted mb-0" style="background-color:#c3c3c3;color:rgb(70, 70, 70)  ;padding:10px">
                            {{ __('lang_v1.gross_profit') }}: 
                        </p>
                    </th>
                    @php
                        
                        $cross_profit  = abs($net_sales) - abs($net_purchase);
                        $cross_profit *= -1;
                        if((  $cross_profit  ) < 0 ){ $gross_profit_type = "   CR"; }elseif(($cross_profit) == 0){$gross_profit_type = " ";}else{ $gross_profit_type = "   DR"; }
                    @endphp
                    <th>
                        <p class="text-muted mb-0" style="background-color:#c3c3c3;color:rgb(70, 70, 70)  ;padding:10px">
                            <span class="display_currency" data-currency_symbol="true">{{($cross_profit)?abs($cross_profit):$cross_profit}}</span> {{ " " . $gross_profit_type }}
                        </p>
                    </th>
                    
                    
                </tr>
                {{-- Expenses --}}
                <tr>
                    @php
                        if(( $data['total_expense'] ) < 0 ){ $total_expense_type = "   CR"; }elseif($data['total_expense'] == 0){$total_expense_type = " ";}else{ $total_expense_type = "   DR"; }
                    @endphp
                    <th style="border-top:1px solid #00000034 ; border-bottom:1px solid #00000034">{{ __('report.total_expense') }}:</th>
                    <td style="border-top:1px solid #00000034 ; border-bottom:1px solid #00000034">
                        <span class="display_currency" data-currency_symbol="true">{{($data['total_expense']<0)?abs($data['total_expense']):$data['total_expense']}}</span> {{ " " . $total_expense_type}}
                    </td>
                </tr>
                {{-- Expenses Account --}}
                @foreach($data['total_expenses_account'] as $key => $it)
                    @php if($it<0){ $tp = "   CR"; }elseif($it==0){ $tp = " ";}else{     $tp = "   DR"; } @endphp
                    @php $account = \App\Account::find($key) ;  @endphp
                    <tr>
                        <th>
                            <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</b>
                            <b>{{"##"}}</b>
                            {{$account->name . " : "}}
                        </th>
                        <td>
                            <span class="display_currency" data-currency_symbol="true">{{($it<0)?abs($it):$it  }} </span>  {{" "}} .  {{$tp}} <br>
                        </td>
                    </tr>
                @endforeach
                {{-- Reveneus --}}
                <tr>
                    @php
                        if(( $data['total_reveneus'] ) < 0 ){ $total_reveneus_type = "   CR"; }elseif($data['total_reveneus'] == 0){$total_reveneus_type = " ";}else{ $total_reveneus_type = "   DR"; }
                    @endphp
                    <th style="border-top:1px solid #00000034 ; border-bottom:1px solid #00000034">{{ __('report.total_reveneus') }}:</th>
                    <td style="border-top:1px solid #00000034 ; border-bottom:1px solid #00000034">
                        <span class="display_currency" data-currency_symbol="true">{{($data['total_reveneus']<0)?abs($data['total_reveneus']):$data['total_reveneus']}}</span> {{ " " . $total_reveneus_type}}
                    </td>
                </tr>
                {{-- Reveneus Account --}}
                @foreach($data['total_reveneus_account'] as $key => $it)
                    @php if($it<0){ $tp = "   CR"; }elseif($it==0){ $tp = " ";}else{     $tp = "   DR"; } @endphp
                    @php $account = \App\Account::find($key) ;  @endphp
                    <tr>
                        <th>
                            <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</b>
                            <b>{{"##"}}</b>
                            {{$account->name . " : "}}
                        </th>
                        <td>
                            <span class="display_currency" data-currency_symbol="true">{{($it<0)?abs($it):$it  }} </span>  {{" "}} .  {{$tp}} <br>
                        </td>
                    </tr>
                @endforeach
                {{-- cross profit --}}
                <tr>
                    @php
                        $net_profit = abs($cross_profit) - abs($data['total_expense']) + abs($data['total_reveneus']);
                        $net_profit *= -1;
                        if( $net_profit  < 0 ){ $net_profit_type = "   CR"; }elseif( $net_profit == 0){$net_profit_type = " ";}else{ $net_profit_type = "   DR"; }
                    @endphp
                    <th>
                        <p class="text-muted mb-0" style="background-color:#c3c3c3;color:rgb(70, 70, 70)  ;padding:10px">
                            @if($net_profit < 0)
                                {{ __('report.net_profit') }}: 
                            @else
                                {{ __('report.net_loss') }}: 
                            @endif
                        </p>
                    </th>
                    <th>
                        <p class="text-muted mb-0" style="background-color:#c3c3c3;color:rgb(70, 70, 70)  ;padding:10px">
                            <span class="display_currency" data-currency_symbol="true">
                                {{
                                    ($net_profit<0)?abs($net_profit):$net_profit
                                }}
                            </span> 
                            {{ " " . $net_profit_type}}
                        </p>
                    </th>
                    
                    
                </tr>
            </table>
        @endcomponent
    </div>
    <div class="col-xs-6">
        @component('components.widget')
            <table class="table table-striped">
            
                {{-- <tr>
                <th>{{ __('report.closing_stock') }} <br><small class="text-muted">(@lang('lang_v1.by_sale_price'))</small>:</th>  
                    <td>
                    <span class="display_currency" data-currency_symbol="true">{{$data['closing_stock_by_sp']}}</span> 
                    </td>
                </tr> --}}
                {{-- <tr>
                    <th>{{ __('report.total_stock_adjustment') }}:</th>
                    <td>
                        <span class="display_currency" data-currency_symbol="true">{{$data['total_adjustment']}}</span>
                    </td>
                </tr>  --}}
                {{-- <tr>
                    <th>{{ __('lang_v1.total_transfer_shipping_charge') }}:</th>
                    <td>
                        <span class="display_currency" data-currency_symbol="true">{{$data['total_transfer_shipping_charges']}}</span>
                    </td>
                </tr> --}}
                {{-- <tr>
                    <th>{{ __('lang_v1.total_reward_amount') }}:</th>
                    <td>
                        <span class="display_currency" data-currency_symbol="true">{{$data['total_reward_amount']}}</span>
                    </td>
                </tr> --}}
                {{-- @foreach($data['left_side_module_data'] as $module_data)
                <tr>
                    <th>{{ $module_data['label'] }}:</th>
                    <td>
                        <span class="display_currency" data-currency_symbol="true">{{ $module_data['value'] }}</span>
                    </td>
                </tr>
                @endforeach --}}
                
                {{-- <tr>
                    <th>{{ __('report.total_stock_recovered') }}:</th>
                    <td>
                        <span class="display_currency" data-currency_symbol="true">{{$data['total_recovered']}}</span>
                    </td>
                </tr> --}}
                {{-- <tr>
                    <th>{{ __('lang_v1.total_sell_round_off') }}:</th>
                    <td>
                        <span class="display_currency" data-currency_symbol="true">{{$data['total_sell_round_off']}}</span>
                    </td>
                </tr> --}}
                {{-- <tr>
                    <td colspan="2">
                    &nbsp;
                    </td>
                </tr> --}}


                {{-- <tr>
                    <th>فوائد الأقساط : </th>
                    <td>
                        <span class="display_currency" data-currency_symbol="true">{{$data['installmentprofit']}}</span>
                    </td>
                </tr> --}}

                {{-- @foreach($data['right_side_module_data'] as $module_data)
                    <tr>
                        <th>{{ $module_data['label'] }}:</th>
                        <td>
                            <span class="display_currency" data-currency_symbol="true">{{ $module_data['value'] }}</span>
                        </td>
                    </tr>
                @endforeach --}}


            </table>
        @endcomponent
    </div>
    <br>
    <div class="col-xs-12">
        @component('components.widget')
            {{-- <h3 class="text-muted mb-0">
                {{ __('lang_v1.gross_profit') }}: 
                <span class="display_currency" data-currency_symbol="true">{{$data['gross_profit']}}</span>
            </h3> --}}
            {{-- <small class="help-block">
                (@lang('lang_v1.total_sell_price') - @lang('lang_v1.total_purchase_price'))
                @if(!empty($data['gross_profit_label']))
                    + {{$data['gross_profit_label']}}
                @endif
            </small> --}}

            {{-- <h3 class="text-muted mb-0">
                {{ __('report.net_profit') }}: 
                <span class="display_currency" data-currency_symbol="true">{{$data['net_profit']}}</span>
            </h3> --}}
            {{-- <small class="help-block">@lang('lang_v1.gross_profit') + (@lang('lang_v1.total_sell_shipping_charge') + @lang('report.total_stock_recovered') + @lang('lang_v1.total_purchase_discount') + @lang('lang_v1.total_sell_round_off') 
            @foreach($data['right_side_module_data'] as $module_data)
                @if(!empty($module_data['add_to_net_profit']))
                    + {{$module_data['label']}} 
                @endif
            @endforeach
        <br> - ( @lang('report.total_stock_adjustment') + @lang('report.total_expense') + @lang('lang_v1.total_purchase_shipping_charge') + @lang('lang_v1.total_transfer_shipping_charge') + @lang('lang_v1.total_sell_discount') + @lang('lang_v1.total_reward_amount')
            @foreach($data['left_side_module_data'] as $module_data)
                @if(!empty($module_data['add_to_net_profit']))
                    + {{$module_data['label']}}
                @endif 
            @endforeach </small> --}}


        @endcomponent
    </div>
