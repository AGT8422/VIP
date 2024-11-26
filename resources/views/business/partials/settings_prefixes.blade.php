<div class="pos-tab-content">
     <div class="row">
        {{-- Purchase --}}
        <div class="col-sm-4">
            <div class="form-group">
                @php
                    $purchase_prefix = '';
                    if(!empty($business->ref_no_prefixes['purchase'])){
                        $purchase_prefix = $business->ref_no_prefixes['purchase'];
                    }
                @endphp
                {!! Form::label('ref_no_prefixes[purchase]', __('lang_v1.purchase_order') . ':') !!}
                {!! Form::text('ref_no_prefixes[purchase]', $purchase_prefix, ['class' => 'form-control']); !!}
            </div>
        </div>
        {{-- Purchase Return --}}
        <div class="col-sm-4">
            <div class="form-group">
                @php 
                    $purchase_return = '';
                    if(!empty($business->ref_no_prefixes['purchase_return'])){
                        $purchase_return = $business->ref_no_prefixes['purchase_return'];
                    }
                @endphp
                {!! Form::label('ref_no_prefixes[purchase_return]', __('lang_v1.purchase_return') . ':') !!}
                {!! Form::text('ref_no_prefixes[purchase_return]', $purchase_return, ['class' => 'form-control']); !!}
            </div>
        </div>
        {{-- Stock Transfer --}}
        <div class="col-sm-4">
            <div class="form-group">
                @php
                    $stock_transfer_prefix = '';
                    if(!empty($business->ref_no_prefixes['stock_transfer'])){
                        $stock_transfer_prefix = $business->ref_no_prefixes['stock_transfer'];
                    }
                @endphp
                {!! Form::label('ref_no_prefixes[stock_transfer]', __('lang_v1.stock_transfer') . ':') !!}
                {!! Form::text('ref_no_prefixes[stock_transfer]', $stock_transfer_prefix, ['class' => 'form-control']); !!}
            </div>
        </div>
        {{-- Stock Adjustment --}}
        <div class="col-sm-4">
            <div class="form-group">
                @php
                    $stock_adjustment_prefix = '';
                    if(!empty($business->ref_no_prefixes['stock_adjustment'])){
                        $stock_adjustment_prefix = $business->ref_no_prefixes['stock_adjustment'];
                    }
                @endphp
                {!! Form::label('ref_no_prefixes[stock_adjustment]', __('stock_adjustment.stock_adjustment') . ':') !!}
                {!! Form::text('ref_no_prefixes[stock_adjustment]', $stock_adjustment_prefix, ['class' => 'form-control']); !!}
            </div>
        </div>
        {{-- Sell Return --}}
        <div class="col-sm-4">
            <div class="form-group">
                @php
                    $sell_return_prefix = '';
                    if(!empty($business->ref_no_prefixes['sell_return'])){
                        $sell_return_prefix = $business->ref_no_prefixes['sell_return'];
                    }
                @endphp
                {!! Form::label('ref_no_prefixes[sell_return]', __('lang_v1.sell_return') . ':') !!}
                {!! Form::text('ref_no_prefixes[sell_return]', $sell_return_prefix, ['class' => 'form-control']); !!}
            </div>
        </div>
        {{-- Expense --}}
        <div class="col-sm-4">
            <div class="form-group">
                @php
                    $expenses_prefix = '';
                    if(!empty($business->ref_no_prefixes['expense'])){
                        $expenses_prefix = $business->ref_no_prefixes['expense'];
                    }
                @endphp
                {!! Form::label('ref_no_prefixes[expense]', __('expense.expenses') . ':') !!}
                {!! Form::text('ref_no_prefixes[expense]', $expenses_prefix, ['class' => 'form-control']); !!}
            </div>
        </div>
        {{-- Contacts --}}
        <div class="col-sm-4">
            <div class="form-group">
                @php
                    $contacts_prefix = '';
                    if(!empty($business->ref_no_prefixes['contacts'])){
                        $contacts_prefix = $business->ref_no_prefixes['contacts'];
                    }
                @endphp
                {!! Form::label('ref_no_prefixes[contacts]', __('contact.contacts') . ':') !!}
                {!! Form::text('ref_no_prefixes[contacts]', $contacts_prefix, ['class' => 'form-control']); !!}
            </div>
        </div>
        {{-- Purchase Payment --}}
        <div class="col-sm-4">
            <div class="form-group">
                @php
                    $purchase_payment = '';
                    if(!empty($business->ref_no_prefixes['purchase_payment'])){
                        $purchase_payment = $business->ref_no_prefixes['purchase_payment'];
                    }
                @endphp
                {!! Form::label('ref_no_prefixes[purchase_payment]', __('lang_v1.purchase_payment') . ':') !!}
                {!! Form::text('ref_no_prefixes[purchase_payment]', $purchase_payment, ['class' => 'form-control']); !!}
            </div>
        </div>
        {{-- Sell Payment --}}
        <div class="col-sm-4">
            <div class="form-group">
                @php
                    $sell_payment = '';
                    if(!empty($business->ref_no_prefixes['sell_payment'])){
                        $sell_payment = $business->ref_no_prefixes['sell_payment'];
                    }
                @endphp
                {!! Form::label('ref_no_prefixes[sell_payment]', __('lang_v1.sell_payment') . ':') !!}
                {!! Form::text('ref_no_prefixes[sell_payment]', $sell_payment, ['class' => 'form-control']); !!}
            </div>
        </div>
        {{-- Expense Payment --}}
        <div class="col-sm-4">
            <div class="form-group">
                @php
                    $expense_payment = '';
                    if(!empty($business->ref_no_prefixes['expense_payment'])){
                        $expense_payment = $business->ref_no_prefixes['expense_payment'];
                    }
                @endphp
                {!! Form::label('ref_no_prefixes[expense_payment]', __('lang_v1.expense_payment') . ':') !!}
                {!! Form::text('ref_no_prefixes[expense_payment]', $expense_payment, ['class' => 'form-control']); !!}
            </div>
        </div>
        {{-- Business Location --}}
        <div class="col-sm-4">
            <div class="form-group">
                @php
                    $business_location_prefix = '';
                    if(!empty($business->ref_no_prefixes['business_location'])){
                        $business_location_prefix = $business->ref_no_prefixes['business_location'];
                    }
                @endphp
                {!! Form::label('ref_no_prefixes[business_location]', __('business.business_location') . ':') !!}
                {!! Form::text('ref_no_prefixes[business_location]', $business_location_prefix, ['class' => 'form-control']); !!}
            </div>
        </div>
        {{-- Username --}}
        <div class="col-sm-4">
            <div class="form-group">
                @php
                    $username_prefix = !empty($business->ref_no_prefixes['username']) ? $business->ref_no_prefixes['username'] : '';
                @endphp
                {!! Form::label('ref_no_prefixes[username]', __('business.username') . ':') !!}
                {!! Form::text('ref_no_prefixes[username]', $username_prefix, ['class' => 'form-control']); !!}
            </div>
        </div>
        {{-- Subscription no --}}
        <div class="col-sm-4">
            <div class="form-group">
                @php
                    $subscription_prefix = !empty($business->ref_no_prefixes['subscription']) ? $business->ref_no_prefixes['subscription'] : '';
                @endphp
                {!! Form::label('ref_no_prefixes[subscription]', __('lang_v1.subscription_no') . ':') !!}
                {!! Form::text('ref_no_prefixes[subscription]', $subscription_prefix, ['class' => 'form-control']); !!}
            </div>
        </div>
        {{-- Draft --}}
        <div class="col-sm-4">
            <div class="form-group">
                @php
                    $draft_prefix = !empty($business->ref_no_prefixes['draft']) ? $business->ref_no_prefixes['draft'] : '';
                @endphp
                {!! Form::label('ref_no_prefixes[draft]', __('sale.draft') . ':') !!}
                {!! Form::text('ref_no_prefixes[draft]', $draft_prefix, ['class' => 'form-control']); !!}
            </div>
        </div>
        {{-- Purchase Receive --}}
        <div class="col-sm-4">
            <div class="form-group">
                @php
                    $draft_prefix = !empty($business->ref_no_prefixes['purchase_receive']) ? $business->ref_no_prefixes['purchase_receive'] : '';
                @endphp
                {!! Form::label('ref_no_prefixes[purchase_receive]', __('sale.purchase receive') . ':') !!}
                {!! Form::text('ref_no_prefixes[purchase_receive]', $draft_prefix, ['class' => 'form-control']); !!}
            </div>
        </div>
        {{-- Wrong Receive --}}
        <div class="col-sm-4 hide">
            <div class="form-group">
                @php
                    $draft_prefix = !empty($business->ref_no_prefixes['Wrong_receive']) ? $business->ref_no_prefixes['Wrong_receive'] : '';
                @endphp
                {!! Form::label('ref_no_prefixes[Wrong_receive]', __('sale.Wrong receive') . ':') !!}
                {!! Form::text('ref_no_prefixes[Wrong_receive]', $draft_prefix, ['class' => 'form-control']); !!}
            </div>
        </div>
        {{-- Project No --}}
        <div class="col-sm-4">
            <div class="form-group">
                @php
                    $draft_prefix = !empty($business->ref_no_prefixes['project_no']) ? $business->ref_no_prefixes['project_no'] : '';
                @endphp
                {!! Form::label('ref_no_prefixes[project_no]', __('sale.project_no') . ':') !!}
                {!! Form::text('ref_no_prefixes[project_no]', $draft_prefix, ['class' => 'form-control']); !!}
            </div>
        </div>
        {{-- Sell Delivery --}}
        <div class="col-sm-4">
            <div class="form-group">
                @php
                    $draft_prefix = !empty($business->ref_no_prefixes['sell_delivery']) ? $business->ref_no_prefixes['sell_delivery'] : '';
                @endphp
                {!! Form::label('ref_no_prefixes[sell_delivery]', __('sale.sell delivery') . ':') !!}
                {!! Form::text('ref_no_prefixes[sell_delivery]', $draft_prefix, ['class' => 'form-control']); !!}
            </div>
        </div>
        {{-- Wrong Delivery --}}
        <div class="col-sm-4 hide">
            <div class="form-group">
                @php
                    $draft_prefix = !empty($business->ref_no_prefixes['Wrong_delivery']) ? $business->ref_no_prefixes['Wrong_delivery'] : '';
                @endphp
                {!! Form::label('ref_no_prefixes[Wrong_delivery]', __('sale.Wrong delivery') . ':') !!}
                {!! Form::text('ref_no_prefixes[Wrong_delivery]', $draft_prefix, ['class' => 'form-control']); !!}
            </div>
        </div>
        {{-- Trans Delivery --}}
        <div class="col-sm-4">
            <div class="form-group">
                @php
                    $draft_prefix = !empty($business->ref_no_prefixes['trans_delivery']) ? $business->ref_no_prefixes['trans_delivery'] : '';
                @endphp
                {!! Form::label('ref_no_prefixes[trans_delivery]', __('sale.trans delivery') . ':') !!}
                {!! Form::text('ref_no_prefixes[trans_delivery]', $draft_prefix, ['class' => 'form-control']); !!}
            </div>
        </div>
        {{-- Approve Quotation --}}
        <div class="col-sm-4">
            <div class="form-group">
                @php 
                    $draft_prefix = !empty($business->ref_no_prefixes['Approve']) ? $business->ref_no_prefixes['Approve'] : '';
                @endphp
                {!! Form::label('ref_no_prefixes[Approve]', __('sale.Approve') . ':') !!}
                {!! Form::text('ref_no_prefixes[Approve]', $draft_prefix, ['class' => 'form-control']); !!}
            </div>
        </div>
        {{-- Open Quantity --}}
        <div class="col-sm-4">
            <div class="form-group">
                @php
                    $draft_prefix = !empty($business->ref_no_prefixes['Open_Quantity']) ? $business->ref_no_prefixes['Open_Quantity'] : '';
                @endphp
                {!! Form::label('ref_no_prefixes[Open_Quantity]', __('sale.Open_Quantity') . ':') !!}
                {!! Form::text('ref_no_prefixes[Open_Quantity]', $draft_prefix, ['class' => 'form-control']); !!}
            </div>
        </div>
        {{-- voucher --}}
        <div class="col-sm-4">
            <div class="form-group">
                @php
                    $voucher_prefix = !empty($business->ref_no_prefixes['voucher']) ? $business->ref_no_prefixes['voucher'] : '';
                @endphp
                {!! Form::label('ref_no_prefixes[voucher]', __('home.Voucher') . ':') !!}
                {!! Form::text('ref_no_prefixes[voucher]', $voucher_prefix, ['class' => 'form-control']); !!}
            </div>
        </div>
        {{-- Expense --}}
        <div class="col-sm-4">
            <div class="form-group">
                @php
                    $gouranl_voucher_prefix = !empty($business->ref_no_prefixes['gouranl_voucher']) ? $business->ref_no_prefixes['gouranl_voucher'] : '';
                @endphp
                {!! Form::label('ref_no_prefixes[gouranl_voucher]', __('home.Gournal Voucher') . ':') !!}
                {!! Form::text('ref_no_prefixes[gouranl_voucher]', $gouranl_voucher_prefix, ['class' => 'form-control']); !!}
            </div>
        </div>
        {{-- Daily payment --}}
        <div class="col-sm-4">
            <div class="form-group">
                @php
                    $daily_payment_prefix = !empty($business->ref_no_prefixes['daily_payment']) ? $business->ref_no_prefixes['daily_payment'] : '';
                @endphp
                {!! Form::label('ref_no_prefixes[daily_payment]', __('home.Daily Payment') . ':') !!}
                {!! Form::text('ref_no_prefixes[daily_payment]', $daily_payment_prefix, ['class' => 'form-control']); !!}
            </div>
        </div>
        {{-- Cheque --}}
        <div class="col-sm-4">
            <div class="form-group">
                @php
                    $Cheque_prefix = !empty($business->ref_no_prefixes['Cheque']) ? $business->ref_no_prefixes['Cheque'] : '';
                @endphp
                {!! Form::label('ref_no_prefixes[Cheque]', __('home.Cheque') . ':') !!}
                {!! Form::text('ref_no_prefixes[Cheque]', $Cheque_prefix, ['class' => 'form-control']); !!}
            </div>
        </div>
        {{-- Quotation --}}
        <div class="col-sm-4">
            <div class="form-group">
                @php
                    $quotation_prefix = !empty($business->ref_no_prefixes['quotation']) ? $business->ref_no_prefixes['quotation'] : '';
                @endphp
                {!! Form::label('ref_no_prefixes[quotation]', __('lang_v1.quotation') . ':') !!}
                {!! Form::text('ref_no_prefixes[quotation]', $quotation_prefix, ['class' => 'form-control']); !!}
            </div>
        </div>
        {{-- Supplier --}}
        <div class="col-sm-4">
            <div class="form-group">
                @php
                    $supplier_prefix = !empty($business->ref_no_prefixes['supplier']) ? $business->ref_no_prefixes['supplier'] : '';
                @endphp
                {!! Form::label('ref_no_prefixes[supplier]', __('purchase.supplier') . ':') !!}
                {!! Form::text('ref_no_prefixes[supplier]', $supplier_prefix, ['class' => 'form-control']); !!}
            </div>
        </div>
        {{-- Customer --}}
        <div class="col-sm-4">
            <div class="form-group">
                @php
                    $customer_prefix = !empty($business->ref_no_prefixes['customer']) ? $business->ref_no_prefixes['customer'] : '';
                @endphp
                {!! Form::label('ref_no_prefixes[customer]', __('contact.customer') . ':') !!}
                {!! Form::text('ref_no_prefixes[customer]', $customer_prefix, ['class' => 'form-control']); !!}
            </div>
        </div>
    </div>
</div>