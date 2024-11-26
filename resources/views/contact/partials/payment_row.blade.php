<tr>
    @if(empty($payment->parent_id))
    <td @if($count_child_payments > 0) rowspan="{{$count_child_payments + 1}}" style="vertical-align:middle;" @endif>
        {{@format_datetime($payment->paid_on)}}
    </td>
    @endif
    <td @if($count_child_payments > 0) class="bg-gray" @endif>
        @if($payment->check_id != null)
            <button type="button" class="btn btn-link btn-modal"
                    data-href="{{\URL::to('/cheque/show', [$payment->check->id])}}" data-container=".view_modal">
                {{$payment->check->ref_no}}
            </button>
        @elseif($payment->payment_voucher_id != null)
            <button type="button" class="btn btn-link btn-modal"
                                    data-href="{{\URL::to('/payment-voucher/show', [$payment->voucher->id])}}" data-container=".view_modal">
                        {{$payment->voucher->ref_no}}
            </button>    
        @else
        <button type="button" class="btn btn-link btn-modal"
                data-href="{{\URL::to('/payments/view-payment/', [$payment->id])}}" data-container=".view_modal">
            {{$payment->payment_ref_no}}
        </button>
           
        @endif
        @if(!empty($parent_payment_ref_no))
            <br>@lang('lang_v1.parent_payment'): {{$parent_payment_ref_no}}
        @endif
    </td>
    <td @if($count_child_payments > 0) class="bg-gray" @endif>
        <span class="display_currency paid-amount" data-orig-value=" {{$payment->amount}}" data-currency_symbol ="true">{{$payment->amount}}</span>
    </td>
    <td @if($count_child_payments > 0) class="bg-gray" @endif>
        @php
            $method = !empty($payment_types[$payment->method]) ? $payment_types[$payment->method] : '';
            if ($payment->method == 'cheque') {
                $method .= '<br>(' . __('lang_v1.cheque_no') . ': ' . $payment->cheque_number . ')';
            } elseif ($payment->method == 'card') {
                $method .= '<br>(' . __('lang_v1.card_transaction_no') . ': ' . $payment->card_transaction_number . ')';
            } elseif ($payment->method == 'bank_transfer') {
                $method .= '<br>(' . __('lang_v1.bank_account_no') . ': ' . $payment->bank_account_number . ')';
            } elseif ($payment->method == 'custom_pay_1') {
                $method .= '<br>(' . __('lang_v1.transaction_no') . ': ' . $payment->transaction_no . ')';
            } elseif ($payment->method == 'custom_pay_2') {
                $method .= '<br>(' . __('lang_v1.transaction_no') . ': ' . $payment->transaction_no . ')';
            } elseif ($payment->method == 'custom_pay_3') {
                $method .= '<br>(' . __('lang_v1.transaction_no') . ': ' . $payment->transaction_no . ')';
            }
            if ($payment->is_return == 1) {
                $method .= '<br><small>(' . __('lang_v1.change_return') . ')</small>';
            }
        @endphp
        {!! $method ?? '' !!}
    </td>
    <td @if($count_child_payments > 0) class="bg-gray" @endif>
        @php
            $transaction_type = $payment->transaction->type ?? $payment->transaction_type;
            $transaction_id = $payment->transaction->id ?? $payment->transaction_id;
            $invoice_no = $payment->transaction->invoice_no ?? $payment->invoice_no;
            $return_parent_id = $payment->transaction->return_parent_id ?? $payment->return_parent_id;
            $ref_no = $payment->transaction->ref_no ?? $payment->ref_no;
        @endphp
        @if($transaction_type == 'sell')
            <a data-href="{{action('SellController@show', [$transaction_id])}}" href="#" data-container=".view_modal" class="btn-modal">{{$invoice_no}}</a> <br> <small>({{__('sale.sale')}}) </small>

        @elseif($transaction_type == 'sell_return')
            <a data-href="{{action('SellReturnController@show', [$return_parent_id])}}" href="#" data-container=".view_modal" class="btn-modal">{{$invoice_no }}</a> <br> <small>({{__('lang_v1.sell_return')}}) </small>
        @elseif($transaction_type == 'purchase_return')
            <a data-href="{{action('PurchaseReturnController@show', [$return_parent_id])}}" href="#" data-container=".view_modal" class="btn-modal">{{$ref_no}}</a> <br> <small>({{__('lang_v1.purchase_return')}}) </small>
        @elseif ($transaction_type == 'purchase')
            <a data-href="{{action('PurchaseController@show', [$transaction_id])}}" href="#" data-container=".view_modal" class="btn-modal">{{$ref_no}}</a> <br> <small>({{__('lang_v1.purchase')}}) </small>
        @else 
            @if(!empty($transaction_id))
                {{$ref_no}} <br> <small>({{__('lang_v1.' . $transaction_type)}}) </small>
            @endif
        @endif
    </td>
    <td @if($count_child_payments > 0) class="bg-gray" @endif>
        <button type="button" class="btn btn-primary btn-xs btn-modal" data-href="{{action('TransactionPaymentController@viewPayment', [$payment->id])}}" data-container=".view_modal"><i class="fas fa-eye"></i>{{__('messages.view')}}</button>
        &nbsp;
        @if(!empty($transaction_id))
         @if($payment->method != 'advance')
            @if($payment->payment_voucher_id != null)
                @php  $types_s = ($payment->transaction->type == 'sale')?0:1  @endphp
                <a class=" btn-info border-black btn-link" style=" color:white;border-radius:3px;padding:2px;" href="{{ URL::to('payment-voucher/edit/'.$payment->payment_voucher_id.'&type='.$types_s) }}">
                    <i class="fa fa-cog" aria-hidden="true"></i> @lang("messages.edit")
                </a>
            @elseif($payment->check_id != null)
                
                <a class=" btn-info border-black btn-link" style=" color:white; border-radius:3px;padding:2px;" href="{{ URL::to('cheque/edit/'.$payment->check_id) }}">
                
                    <i class="fa fa-cog" aria-hidden="true"></i> @lang("messages.edit")
                </a>
            
            @else
                <button type="button" class="btn btn-info btn-xs edit_payment" 
                    data-href="{{action('TransactionPaymentController@edit', [$payment->id]) }}">
                    <i class="fa fa-cog" aria-hidden="true"></i>  @lang("messages.edit")
                </button>
            @endif
        @endif
             {{-- <button type="button" class="btn btn-info btn-xs btn-modal" data-href="{{action('TransactionPaymentController@edit', [$payment->id])}}" data-container=".view_modal"><i class="fas fa-edit"></i> {{__('messages.edit')}}</button> --}}
        @endif
        &nbsp;
        @can("warehouse.Delete")
        <button type="button" class="btn btn-danger btn-xs delete_payment" data-href="{{action('TransactionPaymentController@destroy', [$payment->id])}}" > <i class="fas fa-trash"></i>{{__('messages.delete')}}</button>
        @endcan
    </td>
</tr>