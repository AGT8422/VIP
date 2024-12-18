@extends('layouts.app')
{{-- *1* --}}
@section('title', __('purchase.edit_purchase'))
{{-- *2* --}}
@section('content')
  <!-- Content Header (Page header) -->
  {{-- *1* SECTION HEADER PAGE  --}}
  {{-- ********************************** --}}
    <section class="content-header">
        <h1>@lang('purchase.edit_purchase') <i class="fa fa-keyboard-o hover-q text-muted" aria-hidden="true" data-container="body" data-toggle="popover" data-placement="bottom" data-content="@include('purchase.partials.keyboard_shortcuts_details')" data-html="true" data-trigger="hover" data-original-title="" title=""></i></h1>
        <h5><i><b>{{ "   Purchases  >  " }} </b>{{ "Edit Purchase : ("   }} <b> {{$purchase->ref_no}} </b> {{")"}}</i></h5>
        <br>    
      </h1>
      </section>
  {{-- ********************************** --}}
  <!-- Main content -->
  {{-- *2* SECTION MAIN CONTENT  --}}
  {{-- ********************************** --}}
    <section class="content" style="margin:0px 10%">

        {{-- *2/1* Currency Section --}}
        <!-- Page level currency setting -->
        <input type="hidden" id="p_code" value="{{$currency_details->code}}">
        <input type="hidden" id="p_symbol" value="{{$currency_details->symbol}}">
        <input type="hidden" id="p_thousand" value="{{$currency_details->thousand_separator}}">
        <input type="hidden" id="p_decimal" value="{{$currency_details->decimal_separator}}">
        {{-- *2/2* Error Section --}}
        @include('layouts.partials.error')
        {{-- *2/3* Form Section --}}
        {!! Form::open(['url' =>  action('PurchaseController@update' , [$purchase->id] ), 'method' => 'PUT', 'id' => 'add_purchase_form', 'files' => true ]) !!}
          @php
            $currency_precision = config('constants.currency_precision', 2);
          @endphp
          <input type="hidden" id="edit_page" value="edit">
          <input type="hidden" id="purchase_id" value="{{ $purchase->id }}">
          {{-- *2/3/1* purchase main info --}}
          @component('components.widget', ['class' => 'box-primary' , 'title'=>__('purchase.main_section')])
              <div class="row" style="padding:10px;">
                  <div class="col-sm-4" @if(session()->get('user.language', config('app.locale')) != "ar") style="background-color: #f7f7f7;border-radius:10px;padding:10px;box-shadow:0px 0px 10px #00000023;margin-right:5%;" @else style="background-color: #f7f7f7;border-radius:10px;padding:10px;box-shadow:0px 0px 10px #00000023;margin-left:5%;"  @endif>
                      <div class="@if(!empty($default_purchase_status)) col-sm-12 @else col-sm-12 @endif">
                        <div class="form-group">
                          {!! Form::label('supplier_id', __('purchase.supplier') . ':*') !!}
                          <div class="input-group">
                            <span class="input-group-addon">
                              <i class="fa fa-user"></i>
                            </span>
                            {!! Form::select('supplier_id', [ $purchase->contact_id => $purchase->contact->name], $purchase->contact_id, ['class' => 'form-control', 'placeholder' => __('messages.please_select') , 'required', 'id' => 'supplier_id']); !!}
                            {!! Form::hidden('sup_id',  $purchase->contact_id, ['class' => 'form-control', 'id' => 'sup_id']); !!}
                            <span class="input-group-btn">
                              <button type="button" class="btn btn-default bg-white btn-flat add_new_supplier" data-name=""><i class="fa fa-plus-circle text-primary fa-lg"></i></button>
                            </span>
                          </div>
                        </div>
                        <strong>
                          @lang('business.address'):
                        </strong>
                        <div id="supplier_address_div">
                          {!! $purchase->contact->contact_address !!}
                        </div>
                      </div>
                  </div>
                  <div class="col-sm-7" style="background-color: #f7f7f7;border-radius:10px;padding:20px;box-shadow:0px 0px 10px #00000023">
                    {{-- *2/3/1-1* Purchase Source  --}}
                    <div class="@if(!empty($default_purchase_status)) col-sm-6 @else col-sm-6 @endif">
                      <div class="form-group">
                        {!! Form::label('sup_ref_no', __('purchase.sup_refe') . '') !!}
                
                        {!! Form::text('sup_ref_no', $purchase->sup_refe, ['class' => 'form-control' ]); !!}
                      </div>
                    </div>
                    {{-- *2/3/1-2*  Purchase Refernce Number    --}}
                    <div class="@if(!empty($default_purchase_status)) col-sm-6 @else col-sm-6 @endif">
                      <div class="form-group">
                        {!! Form::label('ref_no', __('purchase.ref_no') . '*') !!}
                        @show_tooltip(__('lang_v1.leave_empty_to_autogenerate'))
                        {!! Form::text('ref_no', $purchase->ref_no, ['class' => 'form-control', 'readonly' => true ,'required']); !!}
                      </div>
                    </div>
                    {{-- *2/3/1-3* Pay Term  --}}
                    <div class="col-md-6">
                      <div class="form-group">
                        <div class="multi-input">
                          {!! Form::label('pay_term_number', __('contact.pay_term') . ':') !!} @show_tooltip(__('tooltip.pay_term'))
                          <br/>
                          {!! Form::number('pay_term_number', $purchase->pay_term_number, ['class' => 'form-control width-40 pull-left', 'placeholder' => __('contact.pay_term')]); !!}

                          {!! Form::select('pay_term_type', 
                            ['months' => __('lang_v1.months'), 
                              'days' => __('lang_v1.days')], 
                              $purchase->pay_term_type, 
                            ['class' => 'form-control width-60 pull-left','placeholder' => __('messages.please_select'), 'id' => 'pay_term_type']); !!}
                        </div>
                      </div>
                    </div>
                    {{-- *2/3/1-4* Status  --}}
                    @if($purchase->status == "received" )  @php  $type= "disabled";  @endphp  @else @php  $type= ""; @endphp @endif   
                    <div class="col-sm-6 @if(!empty($default_purchase_status)) hide @endif">
                      <div class="form-group">
                        {!! Form::label('status', __('purchase.purchase_status') . ':*') !!}
                        @show_tooltip(__('tooltip.order_status'))
                        @if( !empty($TranRecieved)) 
                          @if($purchase->status != "received")
                            @if($state == "received")
                              {!! Form::select('status', [  'final' => __('lang_v1.purchase_final'), 'pending' => __('lang_v1.pending'), 'ordered' => __('lang_v1.ordered')], $purchase->status, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select') , $type ,'required' ]); !!}
                            @else
                              {!! Form::select('status', [  'received' => __('lang_v1.recieved'),'final' => __('lang_v1.purchase_final'), 'pending' => __('lang_v1.pending'), 'ordered' => __('lang_v1.ordered')], $state, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select') , $type ,'required' ]); !!}
                            @endif  
                          @else
                          {!! Form::select('status', $orderStatuses, $state, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select') , $type ,'required' ]); !!}
                          @endif   
                        @else
                          
                          {!! Form::select('status', $orderStatuses, $state, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select') , $type ,'required' ]); !!}
                            
                        @endif   
                        {!! Form::hidden('old_sts',$purchase->status, ['class' => 'form-control ',"id" => "old_sts"]); !!}
                      </div>
                    </div>
                    {{-- *2/3/1-5* Cost Center  --}}
                    <div class="col-sm-6">
                      <div class="form-group">
                        {!! Form::label('cost_center_id', __('home.Cost Center').':') !!}
                        {{-- @show_tooltip(__('tooltip.purchase_location')) --}}
                        {!! Form::select('cost_center_id', $cost_centers, $purchase->cost_center_id, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select')], $bl_attributes); !!}
                      </div>
                    </div>
                    {{-- *2/3/1-6* Purchase Date  --}}                      
                    <div class="@if(!empty($default_purchase_status)) col-sm-6 @else col-sm-6 @endif">
                      <div class="form-group">
                        {!! Form::label('transaction_date', __('purchase.purchase_date') . ':*') !!}
                        <div class="input-group">
                          <span class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                          </span>
                          {!! Form::text('transaction_date', @format_datetime($purchase->transaction_date), ['class' => 'form-control', 'readonly' => true , 'required']); !!}
                        </div>
                      </div>
                    </div>
                    {{-- *2/3/1-7* Location  --}}
                    <div class="col-sm-6 hide">
                      <div class="form-group">
                        {!! Form::label('location_id', __('purchase.business_location').':*') !!}
                        @show_tooltip(__('tooltip.purchase_location'))
                        {!! Form::select('location_id', $business_locations, $purchase->location_id, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'disabled']); !!}
                      </div>
                    </div>
                    {{-- *2/3/1-8* Exchange Rate  --}}
                    <!-- Currency Exchange Rate -->
                    <div class="col-sm-6 @if(!$currency_details->purchase_in_diff_currency) hide @endif">
                      <div class="form-group">
                        {!! Form::label('exchange_rate', __('purchase.p_exchange_rate') . ':*') !!}
                        @show_tooltip(__('tooltip.currency_exchange_factor'))
                        <div class="input-group">
                          <span class="input-group-addon">
                            <i class="fa fa-info"></i>
                          </span>
                          {!! Form::number('exchange_rate', $purchase->exchange_rate, ['class' => 'form-control', 'required', 'step' => 0.001]); !!}
                        </div>
                        <span class="help-block text-danger">
                          @lang('purchase.diff_purchase_currency_help', ['currency' => $currency_details->name])
                        </span>
                      </div>
                    </div>
                  
                    
                   
                    {{-- *2/3/1-9* Currency  --}}
                    <div class="col-md-6">
                      <div class="form-group">
                              <div class="multi-input">
                                {!! Form::label('currency_id', __('business.currency') . ':') !!}  
                                <br/>
                                {!! Form::select('currency_id', $currencies, $purchase->currency_id, ['class' => 'form-control width-60 currency_id  select2', 'placeholder' => __('messages.please_select')]); !!}
                                {!! Form::text('currency_id_amount', $purchase->exchange_price, ['class' => 'form-control width-40 pull-right currency_id_amount'   ]); !!}
                              </div>
                              <br/>
    						  <div @if($purchase->exchange_price > 1)   class="check_dep_curr hide"  @else   class="check_dep_curr hide"    @endif >
    						 @if($purchase->exchange_price > 1) 
    							 {!! Form::checkbox('depending_curr',null, 0, ['class' => 'depending_curr' , "checked" => "checked",'id'=>'depending_curr'   ]); !!}
    							 {!! Form::label('depending_curr', __('Depending On Currency Column') . '') !!}  
    						  @else
    							 {!! Form::checkbox('depending_curr',null, 0, ['class' => 'depending_curr' ,'id'=>'depending_curr'   ]); !!}
    							 {!! Form::label('depending_curr', __('Depending On Currency Column') . '') !!}  
    						  @endif
    						  </div>
    						  <br/> 
							 <div @if($purchase->exchange_price > 1)  class="curr_column   cur_check   hide"  @else  class="curr_column   cur_check hide"    @endif  > <input  type="checkbox" @if($purchase->exchange_price > 1) checked  @endif name="dis_currency" value="1"> <b>Discount</b> @show_tooltip(__('tooltip.dis_currency'))<br ></div>
										
                      </div>
                    </div>
                     {{-- *2/3/1-10* Store  --}}
                     <div class="col-sm-6">
                      <div class="form-group">
                        {!! Form::label('store', __('warehouse.warehouse').':*') !!}
                        {{-- @show_tooltip(__('tooltip.purchase_location')) --}}
                        {!! Form::select('store', $mainstore_categories,$store_id, ['class' => 'form-control select2',"selected" => "selected" ,'placeholder' => __('messages.please_select'), 'required'], $bl_attributes); !!}
                      </div>
                    </div>
                    
                    {{-- *2/3/1-11* Documents  --}}
                    <div class="col-sm-12">
                      <div class="form-group">
                      {!! Form::label('document_purchase[]', __('purchase.attach_document') . ':') !!}
                      {!! Form::file('document_purchase[]', ['multiple','id' => 'upload_document', 'accept' =>
                      implode(',', array_keys(config('constants.document_upload_mimes_types')))]); !!}
                      <p class="help-block">
                        @lang('purchase.max_file_size', ['size' => (config('constants.document_size_limit') / 1000000)])
                        @includeIf('components.document_help_text')
                      </p>
                      </div>
                    </div>

                    {{-- #2024-8-6 --}}
                    <div class="col-sm-12">
                      <div class="form-group">
                        {!! Form::label('list_price', __('List  Of Prices').':') !!}
                        {!! Form::select('list_price',$list_of_prices,$purchase->list_price, ['class' => 'form-control select2' , 'id' => 'list_price' ]); !!}
                      </div>
                    </div>
                    
                  </div>
              </div>
          @endcomponent
          {{-- *2/3/2* product search box --}}
          @component('components.widget', ['class' => 'box-primary' , 'title'=>__('purchase.search_section')])
              <div class="row">
                  <div class="col-sm-8 col-sm-offset-2">
                    <div class="form-group">
                      <div class="input-group">
                        <span class="input-group-addon">
                          <i class="fa fa-search"></i>
                        </span>
                        {!! Form::text('search_product', null, ['class' => 'form-control mousetrap', 'id' => 'search_product', 'placeholder' => __('lang_v1.search_product_placeholder'), 'autofocus']); !!}
                      </div>
                    </div>
                  </div>
                  <div class="col-sm-2">
                    <div class="form-group">
                      <button tabindex="-1" type="button" class="btn btn-link btn-modal"data-href="{{action('ProductController@quickAdd')}}" 
                            data-container=".quick_add_product_modal"><i class="fa fa-plus"></i> @lang( 'product.add_new_product' ) </button>
                    </div>
                </div>
              </div>

              <div class="row">
                  <div class="col-sm-12 purchase_table ">
                    @include('purchase.partials.edit_purchase_entry_row' ,["symbol"=>$currency_details->symbol,"list_of_prices"=>$list_of_prices])

                    <hr/>
                    <div class="row" style="background-color: #f7f7f7;padding:10px;margin:0px 1%;border-radius:10px;box-shadow:0px 0px 10px #00000022">
                      <div class="pull-right col-md-5">
                        <table class="pull-right col-md-12">
                          <tr>
                            <th class="col-md-7 text-right">@lang( 'lang_v1.total_items' ):</th>
                            <td class="col-md-5 text-left">
                              <span id="total_quantity" class="display_currency" data-currency_symbol="false">
                                  {{ $purchase->purchase_lines()->sum('quantity') }}
                              </span>
                            </td>
                          </tr>
                          <tr class="hide">
                            <th class="col-md-7 text-right">@lang( 'purchase.total_before_tax' ):</th>
                            <td class="col-md-5 text-left">
                              <span id="total_st_before_tax" class="display_currency"></span>
                              <input type="hidden" id="st_before_tax_input" value=0>
                            </td>
                          </tr>
                          @php
                          // dd($purchase);
                          @endphp
                          <tr>
                            <th class="col-md-7 text-right">@lang( 'purchase.sub_total_amount' ):</th>
                            <td class="col-md-5 text-left">
                              <span id="total_subtotal" class="display_currency">{{$purchase->total_before_tax/$purchase->exchange_rate}}</span>
                              <!-- This is total before purchase tax-->
                              <input type="hidden" id="total_subtotal_input" value="{{$purchase->total_before_tax/$purchase->exchange_rate}}" name="total_before_tax">
                            </td>
                          </tr>
                        </table>
                      </div>
                      <div class="pull-right col-md-5">
                        <table class="pull-right col-md-12">
                          <tr>
                            <th class="col-md-7 text-right"> &nbsp;</th>
                            <td class="col-md-5 text-left">
                              <span    >&nbsp;</span>
                            </td>
                          </tr>
                          <tr>
                            <th @if($purchase->exchange_price > 1) class="col-md-7 text-right  cur_symbol" @else class="col-md-7 text-right hide cur_symbol" @endif >@lang('purchase.sub_total_amount' ) @if($purchase->exchange_price > 1) {{  $purchase->currency->symbol }} @endif :</th>
                            <td class="col-md-5 text-left">
                              <span id="total_subtotal_cur_edit" @if($purchase->exchange_price > 1)  class="display_currency " @else class="display_currency hide"  @endif ></span>
                              <!-- This is total before purchase tax-->
                              <input type="hidden" id="total_subtotal_input_cur_edit" value=0  name="total_before_tax_cur">
                            </td>
                          </tr>
                        </table>
                      </div>
                    </div>
                    
                  </div>
              </div>
          @endcomponent
          {{-- *2/3/3* purchase details --}}
          @component('components.widget', ['class' => 'box-primary' , 'title'=>__('purchase.footer_section')])
              <div class="row">
                  <div class="col-sm-12">
                      <table class="table">
                        <tr>
                          <td class="col-md-3">
                            <div class="form-group">
                              {!! Form::label('discount_type', __( 'purchase.discount_type' ) . ':') !!}
                              {!! Form::select('discount_type', [ '' => __('lang_v1.none'), 'fixed_before_vat' => __( 'home.fixed before vat' ), 'fixed_after_vat' => __( 'home.fixed after vat' ),
                              'percentage' => __( 'lang_v1.percentage' )], $purchase->discount_type, ['class' => 'form-control select2']); !!}
                            </div>
                          </td>
                          <td class="col-md-3">

                            @php
                              if($purchase->discount_type == "fixed_after_vat"){
                                $tax_ = \App\TaxRate::find($purchase->tax_id);
                                if($tax_ != null){
                                  $tax = $tax_->amount;
                                }else{
                                  $tax = 0;  
                                }
                                // $discount_i = $purchase->discount_amount - ($purchase->discount_amount*$tax/(100+$tax));
                                $discount_i = $purchase->discount_amount;
                              }else{
                                $discount_i = $purchase->discount_amount;
                              }
                            @endphp

                            <div class="form-group">
                            {!! Form::label('discount_amount', __( 'purchase.discount_amount' ) . ':') !!} . {{$currency_details->symbol}}
                            {!! Form::text('discount_amount', 
                                $discount_i
                            // ($purchase->discount_type == 'fixed' ? 
                            //   number_format($purchase->discount_amount/$purchase->exchange_rate, $currency_precision, $currency_details->decimal_separator, $currency_details->thousand_separator)
                            // :
                            //   number_format($purchase->discount_amount, $currency_precision, $currency_details->decimal_separator, $currency_details->thousand_separator)
                            // )
                            , ['class' => 'form-control input_number']); !!}
                            </div>
                          </td>
                          <td>&nbsp;</td>
                          <td class="  pull-left "  >
                            <b  @if($purchase->exchange_price > 1)   class="i_curr  " @else  class="i_curr hide "  @endif > @lang( 'purchase.discount' ) @if($purchase->exchange_price > 1) {{  $purchase->currency->symbol }} @endif  : (-)</b>   
                            <span id="discount_calculated_amount_cur" @if($purchase->exchange_price > 1)  class="display_currency " @else class="display_currency hide"     @endif  >0</span>
                          </td>
                          <td class="  pull-right" >
                            <b>@lang( 'purchase.discount' ) . {{$currency_details->symbol}}:</b>(-) 
                            <span id="discount_calculated_amount2" class="display_currency">0</span>
                          </td>
                        </tr>
                        <tr>
                          <td>
                            <div class="form-group">
                            {!! Form::label('tax_id', __( 'purchase.purchase_tax' ) . ':') !!}. {{$currency_details->symbol}}
                            <select name="tax_id" id="tax_id" class="form-control select2" placeholder="'Please Select'">
                              <option value="" data-tax_amount="0" selected>@lang('lang_v1.none')</option>
                              @foreach($taxes as $tax)
                            
                                <option value="{{ $tax->id }}" @if($purchase->tax_id == $tax->id) {{'selected'}} @endif data-tax_amount="{{ $tax->amount }}"
                                >
                                  {{ $tax->name }}
                                </option>
                              @endforeach
                            </select>
                            {!! Form::hidden('tax_amount', $purchase->tax_amount, ['id' => 'tax_amount']); !!}
                            </div>
                          </td>
                          <td  >&nbsp;</td>
                          <td>&nbsp;</td>
                          <td class="  pull-left" >
                            <b  @if($purchase->exchange_price > 1)   class="t_curr  "  @else   class="t_curr hide"    @endif>@lang( 'purchase.purchase_tax' ) @if($purchase->exchange_price > 1) {{  $purchase->currency->symbol }} @endif : (+)</b> 
                            <span id="tax_calculated_amount_curr" @if($purchase->exchange_price > 1)  class="display_currency " @else class="display_currency hide"     @endif>0</span>
                          </td>
                          <td class="   pull-right" >
                            <b>@lang( 'purchase.purchase_tax' ) . {{$currency_details->symbol}}:</b>(+) 
                            <span id="tax_calculated_amount" class="display_currency">0</span>
                          </td>
                        </tr>
                        {{-- total  --}}
                        <tr>
                          <td>&nbsp;</td>
                          <td>&nbsp;</td>
                          <td>&nbsp;</td>
                          <td class="pull-left" >
                            <br>
                            <b  @if($purchase->exchange_price > 1)   class="z_curr "  @else   class="z_curr hide"     @endif>@lang('purchase.purchase_total_') @if($purchase->exchange_price > 1) {{  $purchase->currency->symbol }} @endif : </b>
                            <span id="total_final_i_curr"  @if($purchase->exchange_price > 1)  class="display_currency " @else class="display_currency hide"     @endif>0</span>
                          </td>
                          <td class="pull-right" >
                            <br>
                            <b>@lang('purchase.purchase_total_'). {{$currency_details->symbol}}: </b><span id="total_final_i" class="display_currency" data-currency_symbol='true'>0</span>
                          </td>
                        </tr>
                        <tr>
                          <td>
                            <div class="form-group">
                            {!! Form::label('shipping_details', __( 'purchase.shipping_details' ) . ':') !!}
                            {!! Form::text('shipping_details', $purchase->shipping_details, ['class' => 'form-control']); !!}
                            </div>
                          </td>
                          <td>&nbsp;</td>
                          <td>&nbsp;</td>
                          <td class="col-md-4">
                            <div class="form-group">
                              <div class="form-group">
                                <b  @if($purchase->exchange_price > 1) class="oss_curr " @else class="oss_curr hide" @endif>@lang('Additional Supplier Cost In Currency'): </b>
		                        <span id="cost_amount_supplier_curr"  @if($purchase->exchange_price > 1) class="display_currency" @else class="display_currency hide" @endif  >0</span> 
                                {!! Form::label('shipping_charges','(+) ' . __( 'purchase.supplier_shipping_charges' ) . ':') !!}
                                <div class="input-group">
                                  <span class="input-group-btn">
                                    <button type="button"   class="btn btn-default bg-white btn-flat" title="@lang('unit.add_unit')" data-toggle="modal" data-target="#exampleModal"><i class="fa fa-plus-circle text-primary fa-lg"></i></button>
                                  </span>
                                  {!! Form::hidden('ADD_SHIP',$purchase->ship_amount, ['class' => 'form-control input_number' ,"id" => "total_ship_"  ]); !!}
                                  {!! Form::text('ADD_SHIP', $purchase->ship_amount, ['class' => 'form-control input_number' ,"id" => "total_ship_" , "disabled" ,'required']); !!}
                                  {{-- {!! Form::text('shipping_charges',$purchase->shipping_charges/$purchase->exchange_rate, ['class' => 'form-control input_number' , "disabled"]); !!} --}}
                                </div>
                                </div>
                                <!-- Modal -->
                                <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                    <input id="ship_from" type="hidden" value="{{$ship_from}}">
                                    @include('additional_Expense.edit')
                                    
                                </div>
                            </div>
                          </td>
                        </tr>

                        <tr>
                          <td>&nbsp;</td>
                          <td>&nbsp;</td>
                          <td>&nbsp;</td>
                          <td class="pull-left" >

                            {{-- <b  @if($purchase->exchange_price > 1)   class="o_curr "  @else   class="o_curr hide"     @endif>@lang('purchase.purchase_total') @if($purchase->exchange_price > 1) {{  $purchase->currency->symbol }} @endif : </b>
                            {!! Form::hidden('grand_total_cur_hidden', 0 , ['id' => 'grand_total_cur_hidden']); !!} --}}
                            <b  @if($purchase->exchange_price > 1)   class="o_curr "  @else   class="o_curr hide"     @endif>@lang('purchase.purchase_total') @if($purchase->exchange_price > 1) {{  $purchase->currency->symbol }} @endif : </b>
                            {!! Form::hidden('grand_total_hidden_curr', 0 , ['id' => 'grand_total_hidden_curr']); !!}
                            <span id="grand_total_cur" @if($purchase->exchange_price > 1)  class="display_currency " @else class="display_currency hide"     @endif >0</span>
                          </td>
                          <td class="pull-right" >
                            {!! Form::hidden('final_total', 0 , ['id' => 'grand_total_hidden']); !!}
                            <b>@lang('purchase.purchase_total') . {{$currency_details->symbol}}: </b><span id="grand_total" class="display_currency" data-currency_symbol='true'>{{$purchase->final_total}}</span>
                            <br>
                            <br>
                            
                            {!! Form::hidden('final_total_hidden_', 0 , ['id' => 'total_final_hidden_']); !!}
                          
                        </tr>
                        <tr>
                          <td>&nbsp;</td>
                          <td>&nbsp;</td>
                          <td>&nbsp;</td>
                          <td>
                            <div class="form-group">
                              <b  @if($purchase->exchange_price > 1)  class="os_curr " @else  class="os_curr hide"    @endif >@lang('Additional Cost In Currency'): </b>
		                        <span id="cost_amount_curr" @if($purchase->exchange_price > 1)  class="display_currency os_curr  " @else class="display_currency os_curr  hide"  @endif >0</span> 
                            {!! Form::label('shipping_charges','(+) ' . __( 'purchase.cost_shipping_charges' ) . ':') !!}
                            <div class="input-group">
                              <span class="input-group-btn">
                                <button type="button"   class="btn btn-default bg-white btn-flat" title="@lang('unit.add_unit')" data-toggle="modal" data-target="#exampleModal"><i class="fa fa-plus-circle text-primary fa-lg"></i></button>
                              </span>
                                  {!! Form::hidden('ADD_SHIP_', 0, ['class' => 'form-control input_number' ,"id" => "total_ship_c"  ]); !!}
                                  {!! Form::text('ADD_SHIP_', 0, ['class' => 'form-control input_number' ,"id" => "total_ship_c" , "disabled" ,'required']); !!}
                            </div>
                            </div>
                            <!-- Modal -->
                            <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                              @include('additional_Expense.create')
                                
                            </div>
                          </td>
                        </tr>
                        <tr>
                          <td>&nbsp;</td>
                          <td>&nbsp;</td>
                          <td>&nbsp;</td>
                          <td class="pull-left" >
                            <br>
                            <b  @if($purchase->exchange_price > 1)   class="c_curr " @else  class="c_curr hide"     @endif>@lang('purchase.purchase_pay') @if($purchase->exchange_price > 1) {{  $purchase->currency->symbol }} @endif : </b>
                            <span id="total_final_curr" @if($purchase->exchange_price > 1)  class="display_currency " @else class="display_currency hide"     @endif  >0</span>
                          </td>
                          <td class="pull-right" >

                            <br>
                            
                            <b>@lang('purchase.purchase_pay') .{{$currency_details->symbol}}: </b><span id="total_final_"  class="display_currency"  data-currency_symbol='true'>0</span>
                            
                          </td>
                      
                          </td>
                        </tr>
                        <tr>
                          <td colspan="4">
                            <div class="form-group">
                              {!! Form::label('additional_notes',__('purchase.additional_notes')) !!}
                              {!! Form::textarea('additional_notes', $purchase->additional_notes, ['class' => 'form-control', 'rows' => 3]); !!}
                            </div>
                          </td>
                        </tr>
                        <tr>
                        </tr>

                      </table>
                  </div>
              </div>
              <div class="row">
                {{-- <div class="transfer_paym col-sm-2 pull-right text-right  hide "  style="">
                  <p class="check-attention">
                    {!! Form::checkbox('transfer_pay',    1  , 1  , [ 'class' => 'input-icheck pull-left' ,  'id'=>'transfer_pay']); !!}
                    {{ __( 'home.transfer_pay' ) }}   
                  </p>  
                </div>
                <h2>&nbsp;</h2> --}}
                <div class="col-sm-12 type_submit">
                            <button  type="button"  id="submit_purchase_form"   class="  btn   btn-primary pull-right btn-flat">@lang('messages.update')</button>
                </div>
            </div>
          @endcomponent
          
        {!! Form::close() !!}
    </section>
  {{-- ********************************** --}}
  <!-- /.content -->
  <!-- quick product modal -->
  {{-- *3* SECTION MODAL  --}}
  {{-- ********************************** --}}
    <div class="modal fade quick_add_product_modal" tabindex="-1" role="dialog" aria-labelledby="modalTitle"></div>
    <div class="modal fade contact_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
      @include('contact.create', ['quick_add' => true])
    </div>
  {{-- ********************************** --}}
@endsection
{{-- *3* --}}
@section('javascript')
  <script src="{{ asset('js/purchase.js?v=' . $asset_v) }}"></script>
  <script src="{{ asset('js/producte.js?v=' . $asset_v) }}"></script>

  <script type="text/javascript">
    refresh();
    $('#purchase_entry_table tbody').sortable({
			cursor: "move",
			handle: ".handle",
            items: "> tr",
			update: function(event, ui) {
				var count = 1;
				$(".line_sorting").each(function(){
					e      = $(this); 
					var el = $(this).children().find(".line_sort"); 
					var inner = $(this).children().find(".line_ordered");  
					e.attr("data-row_index",count);
					inner.html(count);
					el.attr("value",count++);
					// el.val(count++);					
				});
			}
		});
    change_currency();
    console.log("console.log('check ebrahemm #$%#$%#$% : sdsd')");
    function refresh(){
         
      $('.ship_curr').each(function(){
           if($('.currency_id').val() == ""){
            $(this).addClass("hide");
        }else{
            $(this).removeClass("hide");
        }
          
      });
       
    }
    $('.currency_id_amount').change(function(){
    //     if ($('input.depending_curr').is(':checked')) {
		  //  depending_curr();
    //     }else{
		  //  update_os();
            os_total_sub();
        // }
    })
    $('.currency_id').change(function(){
        var id = $(this).val();
        if(id == ""){
          // $(".purchase_unit_cost_without_discount_s").each(function(){
            //     var e =$(this).parent().parent();
            //     exc_before = e.children().find(".purchase_unit_cost_new_currency").val(0);
            //     inc_before = e.children().find(".purchase_unit_cost_with_tax_new_currency").val(0);
            //     exc_after  = e.children().find(".unit_cost_after_new_currency").val(0);
            //     inc_after  = e.children().find(".unit_cost_after_tax_new_currency").val(0);
            
            // });
            $(".currency_id_amount").val("");
            $(".curr_column").attr("disabled",true);
            $(".curr_column").val(0);
            // $(".cur_symbol").html("Sub Total : " );
            $(".cur_symbol").addClass("hide" );
            $("#total_subtotal_cur").addClass("hide" );
            $("#total_subtotal_cur_edit").addClass("hide" );
            $(".i_curr").addClass("hide" );
            $("#discount_calculated_amount_cur").addClass("hide" );
            $(".t_curr").addClass("hide" );
            $("#tax_calculated_amount_curr").addClass("hide" );
            $(".o_curr").addClass("hide" );
            $("#grand_total_cur").addClass("hide" );
            $(".z_curr").addClass("hide" );
            $("#total_final_i_curr").addClass("hide" );
            $(".c_curr").addClass("hide" );
            $(".cur_check").addClass("hide" );
            $("#total_final_curr").addClass("hide" );
            $(".os_curr").addClass("hide" );
			$(".oss_curr").addClass("hide" );
			$(".ship_curr").addClass("hide" );
			$(".check_dep_curr").addClass("hide" );
			$('input[name="dis_currency"]').prop('checked', false);
    		$('#depending_curr').prop('checked', false);;
    		discount_cal_amount2() ;
            os_total_sub();
            os_grand();
          }else{
            $(".cur_symbol").removeClass("hide" );
            $("#total_subtotal_cur").removeClass("hide" );
            $("#total_subtotal_cur_edit").removeClass("hide" );
            $(".curr_column").attr("disabled",false);
            $(".i_curr").removeClass("hide" );
            $("#discount_calculated_amount_cur").removeClass("hide" );
            $(".t_curr").removeClass("hide" );
            $("#tax_calculated_amount_curr").removeClass("hide" );
            $(".o_curr").removeClass("hide" );
            $("#grand_total_cur").removeClass("hide" );
            $(".z_curr").removeClass("hide" );
            $("#total_final_i_curr").removeClass("hide" );
            $(".c_curr").removeClass("hide" );
            $("#total_final_curr").removeClass("hide" );
            $(".cur_check").removeClass("hide" );
            $(".currency_id_amount").val("");
		    $(".os_curr").removeClass("hide" );
			$(".oss_curr").removeClass("hide" );
			$(".ship_curr").removeClass("hide" );
// 			$(".check_dep_curr").removeClass("hide" );
			$('input[name="dis_currency"]').prop('checked', true);
    		$('#depending_curr').prop('checked', true);
          $.ajax({
              url:"/symbol/amount/"+id,
            dataType: 'html',
            success:function(data){
              var object  = JSON.parse(data);
            
              $(".currency_id_amount").val(object.amount);
              $(".cur_symbol").html( @json(__('purchase.sub_total_amount')) + " " + object.symbol + " : "  );
              $(".i_curr").html( @json(__('purchase.discount')) + " " + object.symbol +" : " + "(-)");
              $(".t_curr").html( @json(__('purchase.purchase_tax')) + " " + object.symbol +" : " + "(+)" );
              $(".o_curr").html( @json(__('purchase.purchase_total')) + " " + object.symbol +" : "   );
              $(".z_curr").html( @json(__('purchase.purchase_total_')) + " " + object.symbol +" : "   );
              $(".c_curr").html( @json(__('purchase.purchase_pay')) + " " + object.symbol +" : "   );
              $(".ar_dis").html( @json(__('home.Cost without Tax currency')) + " " + object.symbol +"   "   );
              $(".ar_dis_total").html( @json(__('home.Total Currency')) + " " + object.symbol +"   "   );
              $(".br_dis").html( @json(__('lang_v1.Cost before without Tax')) + " " + object.symbol +"   "   );
              $(".header_texts").html( @json(__('Amount')) + " " + object.symbol +"   "    );
     		  $(".header_totals").html( @json(__('Total')) + " " + object.symbol +"   "    );
     		  $(".header_vats").html( @json(__('Vat')) + " " + object.symbol +"   "    );

              os_total_sub();
            },
          });	 
        }
      })
      $(document).ready(function(){
          change_supplier();
          $("#first_name").on("input",function(){
          $("#first_name").css({"outline":"0px solid red","box-shadow":"1px 1px 10px transparent","color":"gray"});
          $("#contact-submit").attr("disabled",false);

        });

        

        

        $("#first_name").on("change" ,function(e){
        var name = $("#first_name").val();
        // $("#name_p").css({"outline":"0px solid red","box-shadow":"1px 1px 10px transparent"});
        $.ajax({
          method: 'GET',
          url: '/contacts/check/' + name,
          async: false,
          data: {
            name: name,
          },
          dataType: 'json',
          success: function(result) {
            $results = result.status;
            if($results == true){
              toastr.error(LANG.product_name);
              $("#first_name").css({"outline":"1px solid red","box-shadow":"1px 1px 10px red","color":"red"})
              $("#contact-submit").attr("disabled",true);
            }
          }
          });
        }); 
      });


      function change_supplier(){
        $("#supplier_id").on("change",function(){
              var sup_id = $("#sup_id").val();
              // console.log("supplier " + sup_id  + " ___"+$(this).val()+"___ supplier");
              if(sup_id != $(this).val()){
                $(".type_submit").html("");
                $(".type_submit").html("<a data-href='{{action('PurchaseController@payment_msg')}}'  id='submit_purchase_form' data-container='.view_modal' class='update_transfer btn btn-modal btn-primary pull-right btn-flat'>@lang('messages.update')</a>");
              }else{
                $(".type_submit").html("");
                $(".type_submit").html("<button  type='button'  id='submit_purchase_form'   class='  btn   btn-primary pull-right btn-flat'>@lang('messages.update')</button>");
              }
          });  
      }
// #2024-8-6
$('table#purchase_entry_table').on('change', 'select.list_price', function() {
			 
       var tr                      = $(this).closest('tr');
       var base_unit_cost          = tr.find('input.base_unit_cost').val();
       var base_unit_selling_price = tr.find('input.base_unit_selling_price').val();
       var global_price            = $("#list_price").val();
       var price = parseFloat(
         $(this)
           .find(':selected')
           .data('price')
         );
       var cp_element = tr.find('input.purchase_unit_cost_without_discount_s');
       var cp_element_2 = tr.find('input.purchase_unit_cost_without_discount');
       
       if(isNaN(price) ){	
         $(this).children().each(function(){ 
           if($(this).data("value") == global_price){
             final_price = parseFloat($(this).data("price"));
           }
         }); 
       }else{
         final_price = parseFloat(price);
       }
       cp_element.val(final_price);
       cp_element_2.val(final_price);
       cp_element.change();
       cp_element_2.change();
      
     });
   
     $(document).on("change","#list_price",function(){
       var golbal = $(this).val();
       $('table#purchase_entry_table .purchase_unit_cost_without_discount_s').each( function() {
         var price_item      = $(this); 
         var price      = $(this).closest("tr"); 
         var prices     = price.find(".list_price"); 
         var list_price = price.find(".list_price").find(":selected").val(); 
         if(list_price == "" || list_price == null){
           prices.children().each(function(){ 
           if($(this).data("value") == golbal){
             price_item.val($(this).data("price"));
             price_item.change();
           }
         }); 
         }
       });
       $('table#purchase_entry_table .purchase_unit_cost_without_discount').each( function() {
         var price_item      = $(this); 
         var price      = $(this).closest("tr"); 
         var prices     = price.find(".list_price"); 
         var list_price = price.find(".list_price").find(":selected").val(); 
         if(list_price == "" || list_price == null){
           prices.children().each(function(){ 
           if($(this).data("value") == golbal){
             price_item.val($(this).data("price"));
             price_item.change();
           }
         }); 
         }
       });
        
        
     });
  </script>

  @include('purchase.partials.keyboard_shortcuts')
  @yield('edit_row_os')
  @yield('child_script')
@endsection
