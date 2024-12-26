<table class="table table-bordered tb_eb_purchase  table-striped " id="purchase_table" style="width:100%" >
    <thead>
        <tr style="  width:100% !important">
            <th>@lang('messages.action')</th>
            <th>@lang('messages.date')</th>
            <th>@lang('purchase.sup_refe')</th>
            <th>@lang('purchase.ref_no')</th>
            <th class="hide">@lang('purchase.location')</th>
            <th>@lang('purchase.supplier')</th>
            <th>@lang('purchase.purchase_status')</th>
            <th>@lang('purchase.payment_status')</th>
            <th>@lang('purchase.recieved_status')</th>
            <th>@lang('warehouse.nameW')</th>
            {{-- <th class="grands">@lang('purchase.grand_total')</th>  --}}
            {{-- <th>@lang('Purchase Tax')</th> --}}
            <th>@lang('Tax Amount')</th>
            <th class="grand">@lang('purchase.grand_total')</th>
            <th>@lang('purchase.payment_due') &nbsp;&nbsp;<i class="fa fa-info-circle text-info no-print" data-toggle="tooltip" data-placement="bottom" data-html="true" data-original-title="{{ __('messages.purchase_due_tooltip')}}" aria-hidden="true"></i></th>
            <th>@lang('lang_v1.added_by')</th>
        </tr>
    </thead>
    <tfoot> 
        <tr class="bg-gray font-17 text-center footer-total font_number"  >
            {{-- <td colspan="4"><strong>@lang('sale.total'):</strong></td> --}}
            <td></td>
            <td></td>
            <td><strong>@lang('sale.total'):</strong></td>
            <td class="footer_status_count font_number" style="font-family:arial !important"></td>
            <td></td>
            {{-- <td class="footer_payment_status_count"></td>
            <td class="footer_purchase_total"></td>
            <td class="footer_purchase_tax"></td> --}}
            <td class="footer_tax font_number" style="font-family:arial !important"></td>
            <td class="footer_payment_status_count font_number" style="font-family:arial !important"></td>
            <td class="footer_purchase_total font_number" style="font-family:arial !important"></td>
            <td class="text-left"><small>@lang('report.purchase_due') - <span class="footer_total_due font_number"></span><br>
            @lang('lang_v1.purchase_return') - <span class="footer_total_purchase_return_due font_number"></span>
            </small></td>
            <td></td>
        </tr>
    </tfoot>
</table>