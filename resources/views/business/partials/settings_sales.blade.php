<div class="pos-tab-content">

    <div class="row">
        <div class="col-sm-4">
            <div class="form-group">
                {!! Form::label('default_sales_discount', __('business.default_sales_discount') . ':*') !!}
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-percent"></i>
                    </span>
                    {!! Form::text('default_sales_discount', @num_format($business->default_sales_discount), ['class' => 'form-control input_number']); !!}
                </div>
            </div>
        </div>

        <div class="col-sm-4">
            <div class="form-group">
                {!! Form::label('default_sales_tax', __('business.default_sales_tax') . ':') !!}
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-info"></i>
                    </span>
                    {!! Form::select('default_sales_tax', $tax_rates, $business->default_sales_tax, ['class' => 'form-control select2','placeholder' => __('business.default_sales_tax'), 'style' => 'width: 100%;']); !!}
                </div>
            </div>
        </div>
        <!-- <div class="clearfix"></div> -->

        {{--<div class="col-sm-12 hide">
            <div class="form-group">
                {!! Form::label('sell_price_tax', __('business.sell_price_tax') . ':') !!}
                <div class="input-group">
                    <div class="radio">
                        <label>
                            <input type="radio" name="sell_price_tax" value="includes" 
                            class="input-icheck" @if($business->sell_price_tax == 'includes') {{'checked'}} @endif> Includes the Sale Tax
                        </label>
                    </div>
                    <div class="radio">
                        <label>
                            <input type="radio" name="sell_price_tax" value="excludes" 
                            class="input-icheck" @if($business->sell_price_tax == 'excludes') {{'checked'}} @endif>Excludes the Sale Tax (Calculate sale tax on Selling Price provided in Add Purchase)
                        </label>
                    </div>
                </div>
            </div>
        </div>--}}
        <div class="col-sm-4">
            <div class="form-group">
                {!! Form::label('sales_cmsn_agnt', __('lang_v1.sales_commission_agent') . ':') !!}
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-info"></i>
                    </span>
                    {!! Form::select('sales_cmsn_agnt', $commission_agent_dropdown, $business->sales_cmsn_agnt, ['class' => 'form-control select2', 'style' => 'width: 100%;']); !!}
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                {!! Form::label('item_addition_method', __('lang_v1.sales_item_addition_method') . ':') !!}
                {!! Form::select('item_addition_method', [ 0 => __('lang_v1.add_item_in_new_row'), 1 =>  __('lang_v1.increase_item_qty')], $business->item_addition_method, ['class' => 'form-control select2', 'style' => 'width: 100%;']); !!}
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                {!! Form::label('source_sell_price', __('Source Of Sale Price') . ':') !!}
                {!! Form::select('source_sell_price', [ 0 => __('PRODUCT CARD PRICE') ,1 => __('CUSTOMER SALE PRICE BEFORE LINE DISCOUNT'), 2 =>  __('CUSTOMER SALE PRICE AFTER LINE DISCOUNT'), 3 =>  __('CUSTOMER SALE PRICE AFTER TOTAL DISCOUNT')], $source_sell_price, ['class' => 'form-control select2', 'style' => 'width: 100%;']); !!}
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                {!! Form::label('amount_rounding_method', __('lang_v1.amount_rounding_method') . ':') !!} @show_tooltip(__('lang_v1.amount_rounding_method_help'))
                {!! Form::select('pos_settings[amount_rounding_method]', 
                [ 
                    '1' =>  __('lang_v1.round_to_nearest_whole_number'), 
                    '0.05' =>  __('lang_v1.round_to_nearest_decimal', ['multiple' => 0.05]), 
                    '0.1' =>  __('lang_v1.round_to_nearest_decimal', ['multiple' => 0.1]),
                    '0.5' =>  __('lang_v1.round_to_nearest_decimal', ['multiple' => 0.5])
                ], 
                !empty($pos_settings['amount_rounding_method']) ? $pos_settings['amount_rounding_method'] : null, ['class' => 'form-control select2', 'style' => 'width: 100%;', 'placeholder' => __('lang_v1.none')]); !!}
            </div>
        </div>

        <div class="col-sm-4">
            <div class="form-group">
                <div class="checkbox">
                <br>
                  <label>
                    {!! Form::checkbox('pos_settings[enable_msp]', 1,  
                        !empty($pos_settings['enable_msp']) ? true : false , 
                    [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.sale_price_is_minimum_sale_price' ) }} 
                  </label>
                  @show_tooltip(__('lang_v1.minimum_sale_price_help'))
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                <div class="checkbox">
                <br>
                  <label>
                    {!! Form::checkbox('separate_sell', 1,  
                         $separate_sell , 
                    [ 'class' => 'input-icheck','id' => 'separate_sell']); !!} {{ __( 'lang_v1.create_delivery_invoice' ) }} 
                  </label>
                  @show_tooltip(__('lang_v1.create_invoice_delivery'))
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                <div class="checkbox">
                <br>
                  <label>
                    {!! Form::checkbox('separate_pay_sell', 1,  
                         $separate_pay_sell , 
                    [ 'class' => 'input-icheck' ,'id' => 'separate_pay_sell']); !!} {{ __( 'lang_v1.create_invoice_payment' ) }} 
                  </label>
                  @show_tooltip(__('lang_v1.create_payment_invoice'))
                </div>
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="col-sm-4">
            <div class="form-group">
                <div class="checkbox">
                <br>
                  <label>
                    {!! Form::checkbox('pos_settings[allow_overselling]', 1,  
                        !empty($pos_settings['allow_overselling']) ? true : false , 
                    [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.allow_overselling' ) }} 
                  </label>
                  @show_tooltip(__('lang_v1.allow_overselling_help'))
                </div>
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="col-sm-4">
            <div class="form-group">
                {!! Form::label('sale_print_module', __('Sales Print Module ') . ':') !!}
                {!! Form::select('sale_print_module[]', $listModules, json_decode($business->sale_print_module), ['class' => 'form-control select2','multiple'=>true, 'style' => 'width: 100%;']); !!}
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                {!! Form::label('quotation_print_module', __('Quotation Print Module ') . ':') !!}
                {!! Form::select('quotation_print_module[]', $listModules, json_decode($business->quotation_print_module), ['class' => 'form-control select2','multiple'=>true, 'style' => 'width: 100%;']); !!}
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                {!! Form::label('approve_quotation_print_module', __('Approve Quotation Print Module ') . ':') !!}
                {!! Form::select('approve_quotation_print_module[]', $listModules, json_decode($business->approve_quotation_print_module), ['class' => 'form-control select2','multiple'=>true, 'style' => 'width: 100%;']); !!}
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                {!! Form::label('draft_print_module', __('Draft Print Module ') . ':') !!}
                {!! Form::select('draft_print_module[]', $listModules, json_decode($business->draft_print_module), ['class' => 'form-control select2','multiple'=>true, 'style' => 'width: 100%;']); !!}
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                {!! Form::label('return_sale_print_module', __('Return Sale Print Module ') . ':') !!}
                {!! Form::select('return_sale_print_module[]', $listModules, json_decode($business->return_sale_print_module), ['class' => 'form-control select2','multiple'=>true, 'style' => 'width: 100%;']); !!}
            </div>
        </div>
        
    </div>
</div>
@section("javascript")
    <script type="text/javascript">
        $(document).ready(function(){
            $('#separate_pay_sell').on('ifChecked', function(event){
                // $('#separate_sell').iCheck('uncheck');
            });
            $('#separate_sell').on('ifChecked', function(event){
                // $('#separate_pay_sell').iCheck('uncheck');
               
            });
        });
    </script>
@endsection
