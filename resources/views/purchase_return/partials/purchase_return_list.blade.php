<div class="table-responsive">
    <table class="table table-bordered table-striped ajax_view" id="purchase_return_datatable">
        <thead>
            <tr>
                <th>@lang('messages.date')</th>
                <th>@lang('purchase.ref_no')</th>
                <th>@lang('lang_v1.parent_purchase')</th>
                <th class="hide">@lang('purchase.location')</th>
                <th>@lang('purchase.purchase_status')</th>
                <th>@lang('purchase.supplier')</th>
                <th>@lang('purchase.payment_status')</th>
                <th>@lang('purchase.grand_total')</th>
                <th>@lang('purchase.payment_due') &nbsp;&nbsp;<i class="fa fa-info-circle text-info" data-toggle="tooltip" data-placement="bottom" data-html="true" data-original-title="{{ __('messages.purchase_due_tooltip')}}" aria-hidden="true"></i></th>
                <th>@lang('purchase.recieved_status')</th>
                <th>@lang('messages.action')</th>
            </tr>
        </thead>
        <tfoot>
            <tr class="bg-gray font-17 text-center footer-total">
                <td></td>
                <td></td>
                <td><strong>@lang('sale.total'):</strong></td>
                <td  class="hide"></td>
                <td></td>
                <td></td>
                <td id="footer_payment_status_count"></td>
                <td><span class="display_currency font_number" id="footer_purchase_return_total" data-currency_symbol ="true"></span></td>
                <td><span class="display_currency font_number" id="footer_total_due" data-currency_symbol ="true"></span></td>
                <td></td>
                <td></td>
            </tr>
        </tfoot>
    </table>
</div>