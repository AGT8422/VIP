@extends("layouts.app")

@section("title",__("lang_v1.cash_list"))

@section("content")

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('lang_v1.cash_list') </h1>
    {{-- <strong> :::  {{ $product->name }} :::   </strong> --}}
</section>


<section class="content">

 
	<!-- Page level currency setting -->
	<input type="hidden" id="p_code"     value="{{$currency_details->code}}">
	<input type="hidden" id="p_symbol"   value="{{$currency_details->symbol}}">
	<input type="hidden" id="p_thousand" value="{{$currency_details->thousand_separator}}">
	<input type="hidden" id="p_decimal"  value="{{$currency_details->decimal_separator}}">

    {{-- <input type="hidden" id="product_id" value="{{$product->id}}"> --}}
    
    @component("components.widget",["title"=>__("lang_v1.cash_list")])
        <div class="row">
            <div class="col-xs-12">
                <div class="content">
                    <div class="col-xs-4 text-center">
                        <h3>@lang("home.add_new_account")</h3>
                        <div class=" text-center" style="width:50%;margin:0% 25%"  >
                            <a class="btn btn-block btn-primary btn-modal" data-container=".account_model" data-href="{{ \URL::to("/account/account/create?type=$type")}}">
                                <i class="fa fa-plus"></i> @lang('messages.add')</a>
                        </div>
                    </div>
                    {{-- <div class="col-xs-4 text-center "  >
                        <h3>@lang("home.total_account")</h3>
                        <div class="total text-center form-control" style=" font-weight:bold;font-size:large; "></div>
                    </div> --}}
                    <div class="col-xs-8   "  >
                        <h3>@lang("account.account")</h3>
                        <div class="form-group">
                             {!! Form::select('accounts', $accounts_type, null, ['class' => 'form-control select2',"id"=>"accounts", 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
                        </div>
                    </div>
                </div>
                <div class="col-xs-12   col-md-12 col-lg-12">
                    <div class="table">
                        <table class="table table-stripted" id="cash_list" style="width:100% !important">
                            <thead>
                                <tr>
                                    <th>@lang("account.account_number")</th>
                                    <th>@lang("home.name")</th>
                                    <th>@lang("account.debit")</th>
                                    <th>@lang("account.credit")</th>
                                    <th>@lang("purchase.status")</th>
                                    <th>@lang("purchase.balance")</th>
                                    <th  >@lang("messages.action")</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="2" id="footer_total"></td>
                                        <td   id="footer_total_debit"> </td>
                                        <td   id="footer_total_credit"> </td>
                                        <td   id="footer_total_status"> </td>
                                        <td   id="footer_total_balance"> </td>
                                        <td >    </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
        </div>
    @endcomponent
</section>
<div class="modal fade account_model" tabindex="-1" role="dialog" 
        aria-labelledby="gridSystemModalLabel">
</div>
 
@stop

@section('javascript')
    <script src="{{ asset('js/bank_and_cash.js?v=' . $asset_v) }} "></script>
@endsection