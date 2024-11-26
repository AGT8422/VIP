<div class="modal-dialog" role="document" style="font-size:medium !important;width:90%">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close no-print" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title no-print">
 
                @lang( 'purchase.view_payments' ) 
                (
                @if(in_array($transaction->type, ['purchase', 'expense', 'purchase_return', 'payroll']))    
                    @lang('purchase.ref_no'): {{ $transaction->ref_no }} 
                @elseif(in_array($transaction->type, ['sale', 'sell_return']))
                    @lang('sale.invoice_no'): {{ $transaction->invoice_no }}
                @endif
                )   
            </h4>
            <h4 class="modal-title visible-print-block">
                @if(in_array($transaction->type, ['purchase', 'expense', 'purchase_return', 'payroll'])) 
                    @lang('purchase.ref_no'): {{ $transaction->ref_no }}
                @elseif($transaction->type == 'sale')
                    @lang('sale.invoice_no'): {{ $transaction->invoice_no }}
                @endif
            </h4>
        </div>

        <div class="modal-body">
            @if(in_array($transaction->type, ['purchase', 'purchase_return']))
                <div class="row invoice-info">
                    <div class="col-sm-4 invoice-col">
                        @include('transaction_payment.transaction_supplier_details')
                    </div>
                    <div class="col-md-4 invoice-col">
                        @include('transaction_payment.payment_business_details')
                    </div>

                    <div class="col-sm-4 invoice-col">
                        <b>@lang('purchase.ref_no'):</b> #{{ $transaction->ref_no }}<br/>
                        <b>@lang('messages.date'):</b> {{ @format_date($transaction->transaction_date) }}<br/>
                        <b>@lang('purchase.purchase_status'):</b> {{ __('lang_v1.' . $transaction->status) }}<br>
                        <b>@lang('purchase.payment_status'):</b> {{ __('lang_v1.' . $transaction->payment_status) }}<br>
                    </div>
                </div>
            @elseif(in_array($transaction->type, ['expense', 'expense_refund']))
                <div class="row invoice-info">
                    @if(!empty($transaction->contact))
                        <div class="col-sm-4 invoice-col">
                            @lang('expense.expense_for'):
                            <address>
                                @php
                                $id = $transaction->contact->id;
                                $account = \App\Account::where("contact_id",$id)->first();
                                @endphp
                                <strong>{{ $transaction->contact->supplier_business_name }}</strong>
                             
                                <button class="btn btn-link">
                                    <a href="account/account/{{$account->id}}" target="_blank">
                                        <strong>{{ $transaction->contact->name }}</strong>
                                    </a>
                                </button> 
                                 {!! $transaction->contact->contact_address !!}
                                @if(!empty($transaction->contact->tax_number))
                                    <br>@lang('contact.tax_no'): {{$transaction->contact->tax_number}}
                                @endif
                                @if(!empty($transaction->contact->mobile))
                                    <br>@lang('contact.mobile'): {{$transaction->contact->mobile}}
                                @endif
                                @if(!empty($transaction->contact->email))
                                    <br>@lang('business.email'): {{$transaction->contact->email}}
                                @endif
                            </address>
                        </div>
                    @endif
                    <div class="col-md-4 invoice-col">
                        @include('transaction_payment.payment_business_details')
                    </div>

                    <div class="col-sm-4 invoice-col">
                        <b>@lang('purchase.ref_no'):</b> #{{ $transaction->ref_no }}<br/>
                        <b>@lang('messages.date'):</b> {{ @format_date($transaction->transaction_date) }}<br/>
                        <b>@lang('purchase.payment_status'):</b> {{ __('lang_v1.' . $transaction->payment_status) }}<br>
                    </div>
                </div>
            @elseif($transaction->type == 'payroll')
                <div class="row invoice-info">
                    <div class="col-sm-4 invoice-col">
                        @lang('essentials::lang.payroll_for'):
                        <address>
                            <strong>{{ $transaction->transaction_for->user_full_name }}</strong>
                            @if(!empty($transaction->transaction_for->address))
                                <br>{{$transaction->transaction_for->address}}
                            @endif
                            @if(!empty($transaction->transaction_for->contact_number))
                                <br>@lang('contact.mobile'): {{$transaction->transaction_for->contact_number}}
                            @endif
                            @if(!empty($transaction->transaction_for->email))
                                <br>@lang('business.email'): {{$transaction->transaction_for->email}}
                            @endif
                        </address>
                    </div>
                    <div class="col-md-4 invoice-col">
                        @include('transaction_payment.payment_business_details')
                    </div>
                    <div class="col-sm-4 invoice-col">
                        <b>@lang('purchase.ref_no'):</b> #{{ $transaction->ref_no }}<br/>
                        @php
                            $transaction_date = \Carbon::parse($transaction->transaction_date);
                        @endphp
                        <b>@lang( 'essentials::lang.month_year' ):</b> {{ $transaction_date->format('F') }} {{ $transaction_date->format('Y') }}<br/>
                        <b>@lang('purchase.payment_status'):</b> {{ __('lang_v1.' . $transaction->payment_status) }}<br>
                    </div>
                </div>
            @else
                <div class="row invoice-info">
                    <div class="col-sm-4 invoice-col">
                        @lang('contact.customer'):
                        <address>
                            
                            @php
                                $id = $transaction->contact->id;
                                $account = \App\Account::where("contact_id",$id)->first();
                                @endphp
                            <button class="btn btn-link">
                                <a href="account/account/{{$account->id}}" target="_blank">
                                    <strong>{{ $transaction->contact->name }}</strong>
                                </a>
                            </button> 

                            {!! $transaction->contact->contact_address !!}
                            @if(!empty($transaction->contact->tax_number))
                                <br>@lang('contact.tax_no'): {{$transaction->contact->tax_number}}
                            @endif
                            @if(!empty($transaction->contact->mobile))
                                <br>@lang('contact.mobile'): {{$transaction->contact->mobile}}
                            @endif
                            @if(!empty($transaction->contact->email))
                                <br>@lang('business.email'): {{$transaction->contact->email}}
                            @endif
                        </address>
                    </div>
                    <div class="col-md-4 invoice-col">
                        @include('transaction_payment.payment_business_details')
                    </div>
                    <div class="col-sm-4 invoice-col">
                        <b>@lang('sale.invoice_no'):</b> #{{ $transaction->invoice_no }}<br/>
                        <b>@lang('messages.date'):</b> {{ @format_date($transaction->transaction_date) }}<br/>
                        <b>@lang('purchase.payment_status'):</b> {{ __('lang_v1.' . $transaction->payment_status) }}<br>
                    </div>
                </div>
            @endif
            {{-- @can('send_notification')
                @if($transaction->type == 'purchase')
                    <div class="row no-print">
                        <div class="col-md-12 text-right">
                            <button type="button" class="btn btn-info btn-modal btn-xs" 
                            data-href="{{action('NotificationController@getTemplate', ['transaction_id' => $transaction->id,'template_for' => 'payment_paid'])}}" data-container=".view_modal"><i class="fa fa-envelope"></i> @lang('lang_v1.payment_paid_notification')</button>
                        </div>
                    </div>
                    <br>
                @endif
                @if($transaction->type == 'sale')
                    <div class="row no-print">
                        <div class="col-md-12 text-right">
                            <button type="button" class="btn btn-info btn-modal btn-xs" 
                            data-href="{{action('NotificationController@getTemplate', ['transaction_id' => $transaction->id,'template_for' => 'payment_received'])}}" data-container=".view_modal"><i class="fa fa-envelope"></i> @lang('lang_v1.payment_received_notification')</button>
                          
                            @if($transaction->payment_status != 'paid')
                                &nbsp;
                                <button type="button" class="btn btn-warning btn-modal btn-xs" data-href="{{action('NotificationController@getTemplate', ['transaction_id' => $transaction->id,'template_for' => 'payment_reminder'])}}" data-container=".view_modal"><i class="fa fa-envelope"></i> @lang('lang_v1.send_payment_reminder')</button>
                            @endif
                        </div>
                    </div>
                    <br>
                @endif
            @endcan --}}
            @if($transaction->payment_status != 'paid')
                <div class="row">
                    <div class="col-md-12">
                        @if( ((auth()->user()->can('purchase.payments') && (in_array($transaction->type, ['purchase', 'purchase_return']) )) || (auth()->user()->can('sell.payments') && (in_array($transaction->type, ['sale', 'sell_return']))) || (auth()->user()->can('expense.access') )  ) )
                            <a href="{{ action('TransactionPaymentController@addPayment', [$transaction->id]) }}" class="btn btn-primary btn-xs pull-right add_payment_modal no-print"><i class="fa fa-plus" aria-hidden="true"></i> @lang("purchase.add_payment")</a>
                        @endif
                    </div>
                </div>
            @endif
            @php $check_ = 0; @endphp
            <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>@lang('messages.date')</th>
                                <th>@lang('purchase.ref_no')</th>
                                <th>@lang('purchase.amount')</th>
                                <th>@lang('purchase.bill_amount')</th>
                                <th>@lang('purchase.balance')</th>
                                <th>@lang('purchase.payment_method')</th>
                                @foreach($payments as $payment)
                                <?php $cheque =  \App\Models\Check::where('transaction_payment_id',$payment->id)->first(); ?>
                                @if($payment->check_id != null)
                                    @php $check_ = 1 ; @endphp
                                @endif
                                @endforeach
                                @if($check_ == 1)
                                <th>@lang("home.cheque_status")</th>
                                @endif
                                <th>@lang('purchase.payment_note')</th>
                                @if($accounts_enabled)
                                <th>@lang('lang_v1.payment_account')</th>
                                @endif
                                <th class="no-print">@lang('messages.actions')</th>
                            </tr>
                        </thead>
                        
                         @php
                            $total_final        = 0 ;
                            $total_final_bill   = 0 ;
                            $total_final_last   = 0 ;/** NEW */
                        @endphp
                            @forelse ($payments as $payment)
                                <tr>
                                {{-- DATE --}}
                                <td>{{ @format_datetime($payment->paid_on) }}</td>
                                {{-- REFERENCE --}}
                                <td>
                                    <?php $cheque =  \App\Models\Check::where('transaction_payment_id',$payment->id)->first(); ?>
                                    @if($payment->check_id != null)
                                        <a href="#" data-href="{{ action('General\CheckController@show', [$payment->check->id]) }}" class="btn-modal" data-container=".view_modal">
                                            {{ $payment->check->ref_no }}
                                        </a>
                                    @elseif($payment->payment_voucher_id != null)
                                        <a href="#" data-href="{{ action('General\PaymentVoucherController@show', [$payment->voucher->id]) }}" class="btn-modal" data-container=".view_modal">
                                            {{ $payment->voucher->ref_no }}
                                        </a>
                                    @endif
                                </td>
                                {{-- AMOUNT --}}
                                <td>
                                    @if($payment->payment_voucher_id != null)
                                        @format_currency($payment->voucher->amount) 
                                        @php    
                                            // $total_final_last += $payment->check->amount;
                                        @endphp
                                    @endif
                                    @if($payment->check_id != null)
                                        @format_currency($payment->check->amount)
                                        @php    
                                            // $total_final_last += $payment->check->amount;
                                        @endphp
                                    @endif
                                </td>
                                {{-- SOURCE BILL --}}
                                @php
                                        $id_              = $payment->transaction->id;
                                        $tr_              = \App\Transaction::find($id_) ;
                                        $final_total_OLD_ = $tr_->final_total;
                                        $cost_shipp_      =  \App\Models\AdditionalShippingItem::whereHas("additional_shipping",function($query) use($id_,$tr_) {
                                                                                                $query->where("type",1);
                                                                                                $query->where("transaction_id",$id_);
                                                                                                $query->where("contact_id",$tr_->contact_id);
                                                                                            })->sum("total");
                                @endphp
                                <td><span class="display_currency" data-currency_symbol="true">@format_currency($payment->transaction->final_total+$cost_shipp_)</span></td>
                                {{-- BALANCE --}}
                                <td>
                                
                                    @if($payment->payment_voucher_id != null)
                                        @php
                                            $id              = $payment->transaction->id;
                                            $tr              = \App\Transaction::find($id) ;
                                            $final_total_OLD = $tr->final_total;
                                            $cost_shipp      =  \App\Models\AdditionalShippingItem::whereHas("additional_shipping",function($query) use($id,$tr) {
                                                                                                    $query->where("type",1);
                                                                                                    $query->where("transaction_id",$id);
                                                                                                    $query->where("contact_id",$tr->contact_id);
                                                                                                })->sum("total");
                                            $payment     = \App\TransactionPayment::where("payment_voucher_id",$payment->payment_voucher_id)->first();
                                            if(!empty($payment)){
                                                $transaction = \App\Transaction::where("id",$payment->transaction_id)->first();
                                                $total_bill  = $transaction->final_total+$cost_shipp;
                                                $sum         = \App\TransactionPayment::where("transaction_id",$transaction->id)->where("return_payment","=",0)->get();
                                                $price       = 0; 
                                                foreach ($sum as  $value) {
                                                    if($value->payment_voucher_id != $payment->payment_voucher_id && $value->created_at <= $payment->created_at ){
                                                                if($value->check_id != null){
                                                                    $check = \App\Models\Check::find($value->check_id);
                                                                    if($check->status == 2){
                                                                        continue;
                                                                    }else{
                                                                        $price += $value->amount;
                                                                    }
                                                                }else{
                                                                    $price += $value->amount;
                                                                }
                                                            }
                                                }
                                            }else{
                                                $price       = 0;
                                            }
                                            $total_final_bill += $payment->voucher->amount;
                                            $final = $payment->voucher->amount  -    ($payment->transaction->final_total + $cost_shipp - $price);
                                            $total_final = $final ;
                                        @endphp
                                        @if($final < 0)
                                        @format_currency($final*-1) . {{ "  / Credit " }}
                                        @elseif($final > 0 )
                                        @format_currency($final) . {{ "  / Debit " }}
                                        @else
                                        @format_currency($final) 
                                        @endif
                                        @php 
                                        
                                                $total_final_last += $price;
                                            
                                        @endphp 
                                    @endif
                                    
                                    @if($payment->check_id != null)
                                        @php
                                            $id              = $payment->transaction->id;
                                            $tr              = \App\Transaction::find($id) ;
                                            $final_total_OLD = $tr->final_total;
                                            $cost_shipp      =  \App\Models\AdditionalShippingItem::whereHas("additional_shipping",function($query) use($id,$tr) {
                                                                                                    $query->where("type",1);
                                                                                                    $query->where("transaction_id",$id);
                                                                                                    $query->where("contact_id",$tr->contact_id);
                                                                                                })->sum("total");
                                            $payment     = \App\TransactionPayment::where("check_id",$payment->check_id)->first();
                                            if(!empty($payment)){
                                                $transaction = \App\Transaction::where("id",$payment->transaction_id)->first();
                                                $total_bill  = $transaction->final_total+$cost_shipp;
                                                $sum         = \App\TransactionPayment::where("transaction_id",$transaction->id)->where("return_payment","=",0)->get();
                                                // if($payment->check_id == 476){ dd($sum); }
                                                $price       = 0; 
                                                foreach ($sum as  $value) {
                                                    if($value->check_id != $payment->check_id && $value->created_at <= $payment->created_at){
                                                                $price += $value->amount;
                                                    }
                                                }
                                            }else{
                                                $price       = 0;
                                            }
                                            
                                            $cheque =  \App\Models\Check::where('id',$payment->check_id)->first();
                                            if(!empty($cheque)){
                                                if($cheque->status == 2){ 
                                                    $final =  ($payment->transaction->final_total+$cost_shipp-$price);
                                                    $total_final = $final ;
                                                }else{
                                                    $total_final_bill += $payment->check->amount;
                                                    $final = $payment->check->amount - ($payment->transaction->final_total+$cost_shipp-$price);
                                                    $total_final = $final ;
                                                }
                                            }else{
                                                $total_final_bill += $payment->check->amount;
                                                $final = $payment->check->amount - ($payment->transaction->final_total+$cost_shipp-$price);
                                                $total_final = $final ;
                                            }
                                        @endphp
                                        @if($final < 0)
                                            @format_currency($final*-1) . {{ "  / Credit " }}
                                        @elseif($final > 0 )
                                            @format_currency($final) . {{ "  / Debit " }}
                                        @else
                                            @format_currency($final) 
                                        @endif
                                        @php 
                                        
                                                $total_final_last += $price;
                                            
                                        @endphp 
                                    @endif
                                
                                </td>
                                <td>{{ $payment_types[$payment->method] ?? '' }} {{( $payment->method  == "payment voucher")? "payment voucher" : ""}} {{( $payment->method  == "cash_visa")? "Cash & Visa" : ""}}</td>
                                    @if($payment->check_id != null)
                                        <?php $cheque =  \App\Models\Check::where('id',$payment->check_id)->first(); ?>
                                        <td>
                                            
                                            @if($cheque->status == 0){
                                            @lang('home.write'),
                                            @elseif($cheque->status == 1) 
                                            @lang('home.collected'),
                                            @elseif($cheque->status == 2) 
                                            @lang('home.Refund'),
                                            @elseif($cheque->status == 4) 
                                            @lang('home.Un Collect'),    
                                            @endif
                                            
                                        </td>
                                    @elseif($check_ == 1)
                                        <td></td>
                                    @endif
                                <td>{{ $payment->note }}</td>
                                
                                @if($accounts_enabled)
                                    <td>{{$payment->payment_account->name ?? ''}}{{( $payment->method  == "payment voucher")? $payment->voucher->account->name : ""}}</td>
                                @endif

                                {{-- buttons --}}
                                <td class="no-print" style="display: flex;">
                                    @if((auth()->user()->can('purchase.payments') && (in_array($transaction->type, ['purchase', 'purchase_return']) )) || (auth()->user()->can('sell.payments') && (in_array($transaction->type, ['sell', 'sell_return']))) || auth()->user()->can('expense.access') )
                                        @if($payment->method != 'advance')
                                            @if($payment->return_payment == 0)    
                                                @if($payment->payment_voucher_id != null)
                                                @php  $types_s = ($transaction->type == 'sale')?0:1  @endphp
                                                    <a class=" bg-blue border-black btn-link" style="border-radius:3px;padding:2px;" href="{{ URL::to('payment-voucher/edit/'.$payment->payment_voucher_id.'&type='.$types_s) }}">
                                                        <i class="fa fa-cog" aria-hidden="true"></i>
                                                    </a>
                                                @elseif($payment->check_id != null)
                                                    
                                                    <a class=" bg-blue border-black btn-link" style="border-radius:3px;padding:2px;" href="{{ URL::to('cheque/edit/'.$payment->check_id) }}">
                                                    
                                                        <i class="fa fa-cog" aria-hidden="true"></i>
                                                    </a>
                                                    
                                                @else
                                                    <button type="button" class="btn btn-info btn-xs edit_payment" 
                                                        data-href="{{action('TransactionPaymentController@edit', [$payment->id]) }}">
                                                        <i class="fa fa-cog" aria-hidden="true"></i>
                                                    </button>
                                                @endif
                                            @endif
                                        @endif
                                        &nbsp; 
                                        @if(request()->session()->get("user.id") == 1)
                                        <button type="button" class="btn btn-danger btn-xs delete_payment" 
                                            data-href="{{ action('TransactionPaymentController@destroy', [$payment->id]) }}">
                                            <i class="fa fa-trash" aria-hidden="true"></i>
                                        </button>
                                        @endif
                                        &nbsp;
                                        <button type="button" class="btn btn-primary btn-xs view_payment" data-href="{{ action('TransactionPaymentController@viewPayment', [$payment->id]) }}">
                                        <i class="fa fa-eye" aria-hidden="true"></i>
                                        </button>
                                        &nbsp;
                                        @if($payment->payment_voucher_id != null)
                                        <button type="button" class="btn btn-second bg-yellow btn-xs view_payment" data-href="{{ action('General\PaymentVoucherController@show', [$payment->payment_voucher_id]) }}">
                                            <i class="glyphicon glyphicon-edit"></i>
                                            </button>
                                        @endif
                                        @if($payment->check_id != null)
                                            <button type="button" class="btn btn-second bg-yellow btn-xs view_payment" data-href="{{ action('General\CheckController@show', [$payment->check_id]) }}">
                                                <i class="glyphicon glyphicon-edit"></i>
                                            </button>
                                        @endif
                                        &nbsp;
                                        @if($payment->payment_voucher_id != null)   
                                            @if($payment->return_payment == 0)
                                            <button type="button" class="btn btn-danger btn-xs return_payment" 
                                                        data-href="{{ action('TransactionPaymentController@return_payment', [$payment->id]) }}">
                                                <i class="fa fa-undo" aria-hidden="true"></i>
                                            </button>
                                            @endif
                                        @endif
                                    @endif
                                    @if(!empty($payment->document_path))
                                        &nbsp;
                                        <a href="{{$payment->document_path}}" class="btn btn-success btn-xs" download="{{$payment->document_name}}"><i class="fa fa-download" data-toggle="tooltip" title="{{__('purchase.download_document')}}"></i></a>
                                        @if(isFileImage($payment->document_name))
                                        &nbsp;
                                        <button data-href="{{$payment->document_path}}" class="btn btn-info btn-xs view_uploaded_document" data-toggle="tooltip" title="{{__('lang_v1.view_document')}}"><i class="fa fa-picture-o"></i></button>
                                        @endif

                                    @endif
                                </td>
                                </tr>
                                {{-- #$$$ --}}
                                @if($payment->return_payment == 1)
                                    @if($payment->payment_voucher_id != null)
                                        <tr style="color: beige;background-color:brown">
                                            {{-- DATE --}}
                                            <td>{{ @format_datetime($payment->paid_on) }}</td>
                                            {{-- REFERENCE --}}
                                            <td>
                                                <?php $cheque =  \App\Models\Check::where('transaction_payment_id',$payment->id)->first(); ?>
                                                @if($payment->check_id != null)
                                                    <a href="#" data-href="{{ action('General\CheckController@show', [$payment->check->id]) }}" class="btn-modal" data-container=".view_modal">
                                                        {{ $payment->check->ref_no }}
                                                    </a>
                                                @elseif($payment->payment_voucher_id != null)
                                                    <a href="#" data-href="{{ action('General\PaymentVoucherController@show', [$payment->voucher->id]) }}" class="btn-modal" data-container=".view_modal">
                                                        {{ $payment->voucher->ref_no }}
                                                    </a>
                                                @endif
                                            </td>
                                            {{-- AMOUNT --}}
                                            <td>
                                                @if($payment->payment_voucher_id != null)
                                                - @format_currency($payment->voucher->amount) 
                                                    @php    
                                                        // $total_final_last += $payment->check->amount;
                                                    @endphp
                                                @endif
                                                @if($payment->check_id != null)
                                                - @format_currency($payment->check->amount)
                                                    @php    
                                                        // $total_final_last += $payment->check->amount;
                                                    @endphp
                                                @endif
                                            </td>
                                            {{-- SOURCE BILL --}}
                                            @php
                                                    $id_              = $payment->transaction->id;
                                                    $tr_              = \App\Transaction::find($id_) ;
                                                    $final_total_OLD_ = $tr_->final_total;
                                                    $cost_shipp_      =  \App\Models\AdditionalShippingItem::whereHas("additional_shipping",function($query) use($id_,$tr_) {
                                                                                                            $query->where("type",1);
                                                                                                            $query->where("transaction_id",$id_);
                                                                                                            $query->where("contact_id",$tr_->contact_id);
                                                                                                        })->sum("total");
                                            @endphp
                                            <td><span class="display_currency" data-currency_symbol="true">@format_currency($payment->transaction->final_total+$cost_shipp_)</span></td>
                                            {{-- BALANCE --}}
                                            <td>
                                            
                                                @if($payment->payment_voucher_id != null)
                                                    @php
                                                        $id              = $payment->transaction->id;
                                                        $tr              = \App\Transaction::find($id) ;
                                                        $final_total_OLD = $tr->final_total;
                                                        $cost_shipp      =  \App\Models\AdditionalShippingItem::whereHas("additional_shipping",function($query) use($id,$tr) {
                                                                                                                $query->where("type",1);
                                                                                                                $query->where("transaction_id",$id);
                                                                                                                $query->where("contact_id",$tr->contact_id);
                                                                                                            })->sum("total");
                                                        $payment     = \App\TransactionPayment::where("payment_voucher_id",$payment->payment_voucher_id)->first();
                                                        if(!empty($payment)){
                                                            $transaction = \App\Transaction::where("id",$payment->transaction_id)->first();
                                                            $total_bill  = $transaction->final_total+$cost_shipp;
                                                            $sum         = \App\TransactionPayment::where("transaction_id",$transaction->id)->where("payment_voucher_id","!=",$payment->payment_voucher_id)->where("return_payment","=",0)->get();
                                                            $price       = 0; 
                                                            foreach ($sum as  $value) {
                                                                if($value->payment_voucher_id != $payment->payment_voucher_id && $value->created_at <= $payment->created_at ){
                                                                            if($value->check_id != null){
                                                                                $check = \App\Models\Check::find($value->check_id);
                                                                                if($check->status == 2){
                                                                                    continue;
                                                                                }else{
                                                                                    $price += $value->amount;
                                                                                }
                                                                            }else{
                                                                                $price += $value->amount;
                                                                            }
                                                                        }
                                                            }
                                                            
                                                        }else{
                                                            $price       = 0;
                                                        }
                                                        $total_final_bill -= $payment->voucher->amount;
                                                        $final             = ($payment->transaction->final_total + $cost_shipp - $price);
                                                    
                                                        $total_final       = $final ;
                                                    @endphp
                                                    @if($final < 0)
                                                    @format_currency($final*-1) . {{ "  / Credit " }}
                                                    @elseif($final > 0 )
                                                    @format_currency($final) . {{ "  / Debit " }}
                                                    @else
                                                    @format_currency($final) 
                                                    @endif
                                                    @php  $total_final_last -= $price; @endphp 
                                                @endif
                                                
                                                @if($payment->check_id != null)
                                                    @php
                                                        $id              = $payment->transaction->id;
                                                        $tr              = \App\Transaction::find($id) ;
                                                        $final_total_OLD = $tr->final_total;
                                                        $cost_shipp      =  \App\Models\AdditionalShippingItem::whereHas("additional_shipping",function($query) use($id,$tr) {
                                                                                                                $query->where("type",1);
                                                                                                                $query->where("transaction_id",$id);
                                                                                                                $query->where("contact_id",$tr->contact_id);
                                                                                                            })->sum("total");
                                                        $payment     = \App\TransactionPayment::where("check_id",$payment->check_id)->first();
                                                        if(!empty($payment)){
                                                            $transaction = \App\Transaction::where("id",$payment->transaction_id)->first();
                                                            $total_bill  = $transaction->final_total+$cost_shipp;
                                                            $sum         = \App\TransactionPayment::where("transaction_id",$transaction->id)->where("check_id","!=",$payment->check_id)->where("return_payment","=",0)->get();
                                                            // if($payment->check_id == 476){ dd($sum); }
                                                            $price       = 0; 
                                                            foreach ($sum as  $value) {
                                                                if($value->check_id != $payment->check_id && $value->created_at <= $payment->created_at){
                                                                            $price += $value->amount;
                                                                }
                                                            }
                                                        }else{
                                                            $price       = 0;
                                                        }
                                                        
                                                        $cheque =  \App\Models\Check::where('id',$payment->check_id)->first();
                                                        if(!empty($cheque)){
                                                            if($cheque->status == 2){ 
                                                                $final =  ($payment->transaction->final_total+$cost_shipp-$price);
                                                                $total_final = $final ;
                                                            }else{
                                                                $total_final_bill -= $payment->check->amount;
                                                                $final = $payment->check->amount + ($payment->transaction->final_total+$cost_shipp-$price);
                                                                $total_final = $final ;
                                                            }
                                                        }else{
                                                            $total_final_bill -= $payment->check->amount;
                                                            $final = $payment->check->amount + ($payment->transaction->final_total+$cost_shipp-$price);
                                                            $total_final = $final ;
                                                        }
                                                    @endphp
                                                    @if($final < 0)
                                                        @format_currency($final*-1) . {{ "  / Credit " }}
                                                    @elseif($final > 0 )
                                                        @format_currency($final) . {{ "  / Debit " }}
                                                    @else
                                                        @format_currency($final) 
                                                    @endif
                                                    @php 
                                                    
                                                            $total_final_last -= $price;
                                                        
                                                    @endphp 
                                                @endif
                                                        
                                            </td>
                                            <td>{{ $payment_types[$payment->method] ?? '' }} {{( $payment->method  == "payment voucher")? "payment voucher" : ""}} {{( $payment->method  == "cash_visa")? "Cash & Visa" : ""}}</td>
                                                @if($payment->check_id != null)
                                                    <?php $cheque =  \App\Models\Check::where('id',$payment->check_id)->first(); ?>
                                                    <td>
                                                        
                                                        @if($cheque->status == 0){
                                                        @lang('home.write'),
                                                        @elseif($cheque->status == 1) 
                                                        @lang('home.collected'),
                                                        @elseif($cheque->status == 2) 
                                                        @lang('home.Refund'),
                                                        @elseif($cheque->status == 4) 
                                                        @lang('home.Un Collect'),    
                                                        @endif
                                                        
                                                    </td>
                                                @elseif($check_ == 1)
                                                    <td></td>
                                                @endif
                                            <td>{{ $payment->note }}</td>
                                            
                                            @if($accounts_enabled)
                                            
                                                <td>{{$payment->payment_account->name ?? ''}}{{( $payment->method  == "payment voucher")? $payment->voucher->account->name : ""}}</td>
                                            @endif
            
                                            {{-- buttons --}}
                                            <td class="no-print" style="display: flex;">
                                            
                                            </td>
                                        </tr>
                                    @endif
                                @endif
                            @empty
                                @forelse ($payments_bill as $payment)
                                    <tr>
                                    {{-- DATE --}}
                                    <td>{{ @format_datetime($payment->paid_on) }}</td>
                                    {{-- REFERENCE --}}
                                    <td>
                                        <?php $cheque =  \App\Models\Check::where('transaction_payment_id',$payment->id)->first(); ?>
                                        @if($payment->check_id != null)
                                            <a href="#" data-href="{{ action('General\CheckController@show', [$payment->check->id]) }}" class="btn-modal" data-container=".view_modal">
                                                {{ $payment->check->ref_no }}
                                            </a>
                                        @elseif($payment->payment_voucher_id != null)
                                            <a href="#" data-href="{{ action('General\PaymentVoucherController@show', [$payment->voucher->id]) }}" class="btn-modal" data-container=".view_modal">
                                                {{ $payment->voucher->ref_no }}
                                            </a>
                                        @endif
                                    </td>
                                    {{-- AMOUNT --}}
                                    <td>
                                        @if($payment->payment_voucher_id != null)
                                            @format_currency($payment->voucher->amount) 
                                            @php    
                                                // $total_final_last += $payment->check->amount;
                                            @endphp
                                        @endif
                                        @if($payment->check_id != null)
                                            @format_currency($payment->check->amount)
                                            @php    
                                                // $total_final_last += $payment->check->amount;
                                            @endphp
                                        @endif
                                    </td>
                                    {{-- SOURCE BILL --}}
                                    @php
                                            $id_              = $payment->transaction->id;
                                            $tr_              = \App\Transaction::find($id_) ;
                                            $final_total_OLD_ = $tr_->final_total;
                                            $cost_shipp_      =  \App\Models\AdditionalShippingItem::whereHas("additional_shipping",function($query) use($id_,$tr_) {
                                                                                                    $query->where("type",1);
                                                                                                    $query->where("transaction_id",$id_);
                                                                                                    $query->where("contact_id",$tr_->contact_id);
                                                                                                })->sum("total");
                                    @endphp
                                    <td><span class="display_currency" data-currency_symbol="true">@format_currency($payment->transaction->final_total+$cost_shipp_)</span></td>
                                    {{-- BALANCE --}}
                                    <td>
                                    
                                        @if($payment->payment_voucher_id != null)
                                            @php
                                                $id              = $payment->transaction->id;
                                                $tr              = \App\Transaction::find($id) ;
                                                $final_total_OLD = $tr->final_total;
                                                $cost_shipp      =  \App\Models\AdditionalShippingItem::whereHas("additional_shipping",function($query) use($id,$tr) {
                                                                                                        $query->where("type",1);
                                                                                                        $query->where("transaction_id",$id);
                                                                                                        $query->where("contact_id",$tr->contact_id);
                                                                                                    })->sum("total");
                                                $payment     = \App\TransactionPayment::where("payment_voucher_id",$payment->payment_voucher_id)->first();
                                                if(!empty($payment)){
                                                    $transaction = \App\Transaction::where("id",$payment->transaction_id)->first();
                                                    $total_bill  = $transaction->final_total+$cost_shipp;
                                                    $sum         = \App\TransactionPayment::where("transaction_id",$transaction->id)->get();
                                                    $price       = 0; 
                                                    foreach ($sum as  $value) {
                                                        if($value->payment_voucher_id != $payment->payment_voucher_id && $value->created_at <= $payment->created_at ){
                                                                    if($value->check_id != null){
                                                                        $check = \App\Models\Check::find($value->check_id);
                                                                        if($check->status == 2){
                                                                            continue;
                                                                        }else{
                                                                            $price += $value->amount;
                                                                        }
                                                                    }else{
                                                                        $price += $value->amount;
                                                                    }
                                                                }
                                                    }
                                                }else{
                                                    $price       = 0;
                                                }
                                                $total_final_bill += $payment->voucher->amount;
                                                $final = $payment->voucher->amount  -    ($payment->transaction->final_total + $cost_shipp - $price);
                                                $total_final = $final ;
                                            @endphp
                                            @if($final < 0)
                                            @format_currency($final*-1) . {{ "  / Credit " }}
                                            @elseif($final > 0 )
                                            @format_currency($final) . {{ "  / Debit " }}
                                            @else
                                            @format_currency($final) 
                                            @endif
                                            @php 
                                            
                                                    $total_final_last += $price;
                                                
                                            @endphp 
                                        @endif
                                        
                                        @if($payment->check_id != null)
                                            @php
                                                $id              = $payment->transaction->id;
                                                $tr              = \App\Transaction::find($id) ;
                                                $final_total_OLD = $tr->final_total;
                                                $cost_shipp      =  \App\Models\AdditionalShippingItem::whereHas("additional_shipping",function($query) use($id,$tr) {
                                                                                                        $query->where("type",1);
                                                                                                        $query->where("transaction_id",$id);
                                                                                                        $query->where("contact_id",$tr->contact_id);
                                                                                                    })->sum("total");
                                                $payment     = \App\TransactionPayment::where("check_id",$payment->check_id)->first();
                                                if(!empty($payment)){
                                                    $transaction = \App\Transaction::where("id",$payment->transaction_id)->first();
                                                    $total_bill  = $transaction->final_total+$cost_shipp;
                                                    $sum         = \App\TransactionPayment::where("transaction_id",$transaction->id)->get();
                                                    // if($payment->check_id == 476){ dd($sum); }
                                                    $price       = 0; 
                                                    foreach ($sum as  $value) {
                                                        if($value->check_id != $payment->check_id && $value->created_at <= $payment->created_at){
                                                                    $price += $value->amount;
                                                        }
                                                    }
                                                }else{
                                                    $price       = 0;
                                                }
                                                
                                                $cheque =  \App\Models\Check::where('id',$payment->check_id)->first();
                                                if(!empty($cheque)){
                                                    if($cheque->status == 2){ 
                                                        $final =  ($payment->transaction->final_total+$cost_shipp-$price);
                                                        $total_final = $final ;
                                                    }else{
                                                        $total_final_bill += $payment->check->amount;
                                                        $final = $payment->check->amount - ($payment->transaction->final_total+$cost_shipp-$price);
                                                        $total_final = $final ;
                                                    }
                                                }else{
                                                    $total_final_bill += $payment->check->amount;
                                                    $final = $payment->check->amount - ($payment->transaction->final_total+$cost_shipp-$price);
                                                    $total_final = $final ;
                                                }
                                            @endphp
                                            @if($final < 0)
                                                @format_currency($final*-1) . {{ "  / Credit " }}
                                            @elseif($final > 0 )
                                                @format_currency($final) . {{ "  / Debit " }}
                                            @else
                                                @format_currency($final) 
                                            @endif
                                            @php 
                                            
                                                    $total_final_last += $price;
                                                
                                            @endphp 
                                        @endif
                                                
                                    </td>
                                    <td>{{ $payment_types[$payment->method] ?? '' }} {{( $payment->method  == "payment voucher")? "payment voucher" : ""}} {{( $payment->method  == "cash_visa")? "Cash & Visa" : ""}}</td>
                                        @if($payment->check_id != null)
                                            <?php $cheque =  \App\Models\Check::where('id',$payment->check_id)->first(); ?>
                                            <td>
                                                
                                                @if($cheque->status == 0){
                                                @lang('home.write'),
                                                @elseif($cheque->status == 1) 
                                                @lang('home.collected'),
                                                @elseif($cheque->status == 2) 
                                                @lang('home.Refund'),
                                                @elseif($cheque->status == 4) 
                                                @lang('home.Un Collect'),    
                                                @endif
                                                
                                            </td>
                                        @elseif($check_ == 1)
                                            <td></td>
                                        @endif
                                    <td>{{ $payment->note }}</td>
                                    
                                    @if($accounts_enabled)
                                    
                                        <td>{{$payment->payment_account->name ?? ''}}{{( $payment->method  == "payment voucher")? $payment->voucher->account->name : ""}}</td>
                                    @endif

                                    {{-- buttons --}}
                                    <td class="no-print" style="display: flex;">
                                        @if((auth()->user()->can('purchase.payments') && (in_array($transaction->type, ['purchase', 'purchase_return']) )) || (auth()->user()->can('sell.payments') && (in_array($transaction->type, ['sell', 'sell_return']))) || auth()->user()->can('expense.access') )
                                            @if($payment->method != 'advance')
                                                @if($payment->return_payment == 0)
                                                    @if($payment->payment_voucher_id != null)
                                                        @php  $types_s = ($transaction->type == 'sale')?0:1  @endphp
                                                        <a class=" bg-blue border-black btn-link" style="border-radius:3px;padding:2px;" href="{{ URL::to('payment-voucher/edit/'.$payment->payment_voucher_id.'&type='.$types_s) }}">
                                                            <i class="fa fa-cog" aria-hidden="true"></i>
                                                        </a>
                                                    @elseif($payment->check_id != null)
                                                        <a class=" bg-blue border-black btn-link" style="border-radius:3px;padding:2px;" href="{{ URL::to('cheque/edit/'.$payment->check_id) }}">
                                                            <i class="fa fa-cog" aria-hidden="true"></i>
                                                        </a>
                                                    @else
                                                        <button type="button" class="btn btn-info btn-xs edit_payment" 
                                                            data-href="{{action('TransactionPaymentController@edit', [$payment->id]) }}">
                                                            <i class="fa fa-cog" aria-hidden="true"></i>
                                                        </button>
                                                    @endif
                                                @endif
                                            @endif
                                            &nbsp; 
                                            @if(request()->session()->get("user.id") == 1)
                                            <button type="button" class="btn btn-danger btn-xs delete_payment" 
                                                data-href="{{ action('TransactionPaymentController@destroy', [$payment->id]) }}">
                                                <i class="fa fa-trash" aria-hidden="true"></i>
                                            </button>
                                            @endif
                                            &nbsp;
                                            <button type="button" class="btn btn-primary btn-xs view_payment" data-href="{{ action('TransactionPaymentController@viewPayment', [$payment->id]) }}">
                                            <i class="fa fa-eye" aria-hidden="true"></i>
                                            </button>
                                            &nbsp;
                                            @if($payment->payment_voucher_id != null)
                                                <button type="button" class="btn btn-second bg-yellow btn-xs view_payment" data-href="{{ action('General\PaymentVoucherController@show', [$payment->payment_voucher_id]) }}">
                                                    <i class="glyphicon glyphicon-edit"></i>
                                                </button>
                                            @endif
                                            &nbsp;
                                            @if($payment->check_id != null)
                                                <button type="button" class="btn btn-second bg-yellow btn-xs view_payment" data-href="{{ action('General\CheckController@show', [$payment->check_id]) }}">
                                                    <i class="glyphicon glyphicon-edit"></i>
                                                </button>
                                            @endif
                                            @if($payment->return_payment == 0)
                                                <button type="button" class="btn btn-danger btn-xs return_payment" 
                                                    data-href="{{ action('TransactionPaymentController@return_payment', [$payment->id]) }}">
                                                    <i class="fa fa-undo" aria-hidden="true"></i>
                                                </button>
                                            @endif
                                        @endif
                                        @if(!empty($payment->document_path))
                                            &nbsp;
                                            <a href="{{$payment->document_path}}" class="btn btn-success btn-xs" download="{{$payment->document_name}}"><i class="fa fa-download" data-toggle="tooltip" title="{{__('purchase.download_document')}}"></i></a>
                                            @if(isFileImage($payment->document_name))
                                            &nbsp;
                                            <button data-href="{{$payment->document_path}}" class="btn btn-info btn-xs view_uploaded_document" data-toggle="tooltip" title="{{__('lang_v1.view_document')}}"><i class="fa fa-picture-o"></i></button>
                                            @endif

                                        @endif
                                    </td>
                                    </tr>
                                    {{-- #$$$ --}}
                                    @if($payment->return_payment == 1)
                                        @if($payment->payment_voucher_id != null)    
                                            <tr style="color: beige;background-color:brown">
                                                {{-- DATE --}}
                                                <td>{{ @format_datetime($payment->paid_on) }}</td>
                                                {{-- REFERENCE --}}
                                                <td>
                                                    <?php $cheque =  \App\Models\Check::where('transaction_payment_id',$payment->id)->first(); ?>
                                                    @if($payment->check_id != null)
                                                        <a href="#" data-href="{{ action('General\CheckController@show', [$payment->check->id]) }}" class="btn-modal" data-container=".view_modal">
                                                            {{ $payment->check->ref_no }}
                                                        </a>
                                                    @elseif($payment->payment_voucher_id != null)
                                                        <a href="#" data-href="{{ action('General\PaymentVoucherController@show', [$payment->voucher->id]) }}" class="btn-modal" data-container=".view_modal">
                                                            {{ $payment->voucher->ref_no }}
                                                        </a>
                                                    @endif
                                                </td>
                                                {{-- AMOUNT --}}
                                                <td>
                                                    @if($payment->payment_voucher_id != null)
                                                        @format_currency($payment->voucher->amount) 
                                                        @php    
                                                            // $total_final_last += $payment->check->amount;
                                                        @endphp
                                                    @endif
                                                    @if($payment->check_id != null)
                                                        @format_currency($payment->check->amount)
                                                        @php    
                                                            // $total_final_last += $payment->check->amount;
                                                        @endphp
                                                    @endif
                                                </td>
                                                {{-- SOURCE BILL --}}
                                                @php
                                                        $id_              = $payment->transaction->id;
                                                        $tr_              = \App\Transaction::find($id_) ;
                                                        $final_total_OLD_ = $tr_->final_total;
                                                        $cost_shipp_      =  \App\Models\AdditionalShippingItem::whereHas("additional_shipping",function($query) use($id_,$tr_) {
                                                                                                                $query->where("type",1);
                                                                                                                $query->where("transaction_id",$id_);
                                                                                                                $query->where("contact_id",$tr_->contact_id);
                                                                                                            })->sum("total");
                                                @endphp
                                                <td><span class="display_currency" data-currency_symbol="true">@format_currency($payment->transaction->final_total+$cost_shipp_)</span></td>
                                                {{-- BALANCE --}}
                                                <td>
                                                
                                                    @if($payment->payment_voucher_id != null)
                                                        @php
                                                            $id              = $payment->transaction->id;
                                                            $tr              = \App\Transaction::find($id) ;
                                                            $final_total_OLD = $tr->final_total;
                                                            $cost_shipp      =  \App\Models\AdditionalShippingItem::whereHas("additional_shipping",function($query) use($id,$tr) {
                                                                                                                    $query->where("type",1);
                                                                                                                    $query->where("transaction_id",$id);
                                                                                                                    $query->where("contact_id",$tr->contact_id);
                                                                                                                })->sum("total");
                                                            $payment     = \App\TransactionPayment::where("payment_voucher_id",$payment->payment_voucher_id)->first();
                                                            if(!empty($payment)){
                                                                $transaction = \App\Transaction::where("id",$payment->transaction_id)->first();
                                                                $total_bill  = $transaction->final_total+$cost_shipp;
                                                                $sum         = \App\TransactionPayment::where("transaction_id",$transaction->id)->where("return_payment","=",0)->get();
                                                                $price       = 0; 
                                                                foreach ($sum as  $value) {
                                                                    if($value->payment_voucher_id != $payment->payment_voucher_id && $value->created_at <= $payment->created_at ){
                                                                                if($value->check_id != null){
                                                                                    $check = \App\Models\Check::find($value->check_id);
                                                                                    if($check->status == 2){
                                                                                        continue;
                                                                                    }else{
                                                                                        $price += $value->amount;
                                                                                    }
                                                                                }else{
                                                                                    $price += $value->amount;
                                                                                }
                                                                            }
                                                                }
                                                            }else{
                                                                $price       = 0;
                                                            }
                                                            $total_final_bill -= $payment->voucher->amount;
                                                            $final = $payment->voucher->amount  +    ($payment->transaction->final_total + $cost_shipp - $price);
                                                            $total_final = $final ;
                                                        @endphp
                                                        @if($final < 0)
                                                        @format_currency($final*-1) . {{ "  / Credit " }}
                                                        @elseif($final > 0 )
                                                        @format_currency($final) . {{ "  / Debit " }}
                                                        @else
                                                        @format_currency($final) 
                                                        @endif
                                                        @php 
                                                        
                                                                $total_final_last += $price;
                                                            
                                                        @endphp 
                                                    @endif
                                                    
                                                    @if($payment->check_id != null)
                                                        @php
                                                            $id              = $payment->transaction->id;
                                                            $tr              = \App\Transaction::find($id) ;
                                                            $final_total_OLD = $tr->final_total;
                                                            $cost_shipp      =  \App\Models\AdditionalShippingItem::whereHas("additional_shipping",function($query) use($id,$tr) {
                                                                                                                    $query->where("type",1);
                                                                                                                    $query->where("transaction_id",$id);
                                                                                                                    $query->where("contact_id",$tr->contact_id);
                                                                                                                })->sum("total");
                                                            $payment     = \App\TransactionPayment::where("check_id",$payment->check_id)->first();
                                                            if(!empty($payment)){
                                                                $transaction = \App\Transaction::where("id",$payment->transaction_id)->first();
                                                                $total_bill  = $transaction->final_total+$cost_shipp;
                                                                $sum         = \App\TransactionPayment::where("transaction_id",$transaction->id)->where("return_payment","=",0)->get();
                                                                // if($payment->check_id == 476){ dd($sum); }
                                                                $price       = 0; 
                                                                foreach ($sum as  $value) {
                                                                    if($value->check_id != $payment->check_id && $value->created_at <= $payment->created_at){
                                                                                $price += $value->amount;
                                                                    }
                                                                }
                                                            }else{
                                                                $price       = 0;
                                                            }
                                                            
                                                            $cheque =  \App\Models\Check::where('id',$payment->check_id)->first();
                                                            if(!empty($cheque)){
                                                                if($cheque->status == 2){ 
                                                                    $final =  ($payment->transaction->final_total+$cost_shipp-$price);
                                                                    $total_final = $final ;
                                                                }else{
                                                                    $total_final_bill -= $payment->check->amount;
                                                                    $final = $payment->check->amount + ($payment->transaction->final_total+$cost_shipp-$price);
                                                                    $total_final = $final ;
                                                                }
                                                            }else{
                                                                $total_final_bill -= $payment->check->amount;
                                                                $final = $payment->check->amount + ($payment->transaction->final_total+$cost_shipp-$price);
                                                                $total_final = $final ;
                                                            }
                                                        @endphp
                                                        @if($final < 0)
                                                            @format_currency($final*-1) . {{ "  / Credit " }}
                                                        @elseif($final > 0 )
                                                            @format_currency($final) . {{ "  / Debit " }}
                                                        @else
                                                            @format_currency($final) 
                                                        @endif
                                                        @php 
                                                        
                                                                $total_final_last += $price;
                                                            
                                                        @endphp 
                                                    @endif
                                                            
                                                </td>
                                                <td>{{ $payment_types[$payment->method] ?? '' }} {{( $payment->method  == "payment voucher")? "payment voucher" : ""}} {{( $payment->method  == "cash_visa")? "Cash & Visa" : ""}}</td>
                                                    @if($payment->check_id != null)
                                                        <?php $cheque =  \App\Models\Check::where('id',$payment->check_id)->first(); ?>
                                                        <td>
                                                            
                                                            @if($cheque->status == 0){
                                                            @lang('home.write'),
                                                            @elseif($cheque->status == 1) 
                                                            @lang('home.collected'),
                                                            @elseif($cheque->status == 2) 
                                                            @lang('home.Refund'),
                                                            @elseif($cheque->status == 4) 
                                                            @lang('home.Un Collect'),    
                                                            @endif
                                                            
                                                        </td>
                                                    @elseif($check_ == 1)
                                                        <td></td>
                                                    @endif
                                                <td>{{ $payment->note }}</td>
                                                
                                                @if($accounts_enabled)
                                                
                                                    <td>{{$payment->payment_account->name ?? ''}}{{( $payment->method  == "payment voucher")? $payment->voucher->account->name : ""}}</td>
                                                @endif
                
                                                {{-- buttons --}}
                                                <td class="no-print" style="display: flex;">
                                                    
                                                </td>
                                            </tr>
                                        @endif
                                    @endif
                                @empty
                                    <tr class="text-center">
                                    <td colspan="6">@lang('purchase.no_records_found')</td>
                                    @if($check_ == 1)
                                            <td></td>
                                    @endif
                                    </tr>
                                @endforelse
                            @endforelse

                        {{-- RESULT OF CALCULATE --}}
                        <tfoot>
                            <tr>
                                <td colspan="2"></td>
                                <td>
                                    @format_currency($total_final_bill)
                                </td>
                                <td></td>
                                <td>
                                    @if($total_final < 0)
                                    @format_currency($total_final*-1) . {{ "  / Credit " }}
                                    @elseif($total_final > 0 )
                                    @format_currency($total_final) . {{ "  / Debit " }}
                                    @else
                                    @format_currency($total_final) 
                                    @endif
                                </td>
                                <td colspan="4"></td>
                                @if($check_ == 1)
                                    <td></td>
                              @endif
                            </tr>
                        </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-primary no-print" 
              aria-label="Print" 
                onclick="$(this).closest('div.modal').printThis();">
                <i class="fa fa-print"></i> @lang( 'messages.print' )
            </button>
            <button type="button" class="btn btn-default no-print" data-dismiss="modal">@lang( 'messages.close' )</button>
        </div>
    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

