<div class="modal-dialog modal-xl no-print" role="document">
  <div class="modal-content">
    <div class="modal-header">
    <button type="button" class="close no-print" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <h4 class="modal-title" id="modalTitle"> @lang('sale.sell_details') (<b>@lang('sale.invoice_no'):</b> {{ $sell->invoice_no }})
    </h4>
  </div>
  <div class="modal-body">
    <div class="row">
      <div class="col-xs-12">
          <p class="pull-right"><b>@lang('messages.date'):</b> {{ @format_date($sell->transaction_date) }}</p>
      </div>
    </div>
    <div class="row">
      @php
       $currency = \App\Currency::find($sell->business->currency_id); 
        $currency_symbol =isset($currency)?$currency->symbol:"";
        $custom_labels = json_decode(session('business.custom_labels'), true);
      @endphp
      <div class="col-sm-4">
        
        @if($sell->refe_no != null)
          <b>{{ __('sale.draft_no') }}:</b>        #{{ ($sell->refe_no)??"--" }}<br>
        @endif
        @if($sell->first_ref_no != null)
          <b>{{ __('sale.quotation_no') }}:</b>    #{{ ($sell->first_ref_no)??"--" }}<br>
        @endif
        @if($sell->previous != null)
            <b>{{ __('sale.approve_no') }}:</b>     #{{ ($sell->previous)??"--" }}<br>
        @endif
        <b>{{ __('sale.invoice') }}:</b>        #{{ $sell->invoice_no }}<br>
        <b>{{ __('sale.project_no') }}:</b>     #{{ $sell->project_no }}<br>
        <b>{{ __('sale.status') }}:</b> 
        
        @if( $sell->status == 'draft' && $sell->sub_status == "proforma")
        {{ __('sale.ApprovedQuotation') }}
        @elseif($sell->status == 'draft' && $sell->is_quotation == 1)
        {{ __('lang_v1.quotation') }}
        @else
        {{ __('sale.' . $sell->status) }}
        @endif
        <br>
        <b>{{ __('sale.payment_status') }}:</b> @if(!empty($sell->payment_status)){{ __('lang_v1.' . $sell->payment_status) }}
        @endif
        <br><b>{{ __('warehouse.nameW') }}:</b> {{
        
          $store
        
        
        }}
        @if(!empty($custom_labels['sell']['custom_field_1']))
          <br><strong>{{$custom_labels['sell']['custom_field_1'] ?? ''}}: </strong> {{$sell->custom_field_1}}
        @endif
        @if(!empty($custom_labels['sell']['custom_field_2']))
          <br><strong>{{$custom_labels['sell']['custom_field_2'] ?? ''}}: </strong> {{$sell->custom_field_2}}
        @endif
        @if(!empty($custom_labels['sell']['custom_field_3']))
          <br><strong>{{$custom_labels['sell']['custom_field_3'] ?? ''}}: </strong> {{$sell->custom_field_3}}
        @endif
        @if(!empty($custom_labels['sell']['custom_field_4']))
          <br><strong>{{$custom_labels['sell']['custom_field_4'] ?? ''}}: </strong> {{$sell->custom_field_4}}
        @endif
         {{-- @if($sell->document)
          <br>
          <br>
          <a href="{{$sell->document_path}}" 
          download="{{$sell->document_name}}" class="btn btn-xs btn-success pull-left no-print">
            <i class="fa fa-download"></i> 
              &nbsp;{{ __('purchase.download_document') }}
          </a>
        @endif --}}
      </div>
      <div class="col-sm-4">
        @if(!empty($sell->contact->supplier_business_name))
          {{ $sell->contact->supplier_business_name }}<br>
        @endif
        <b>{{ __('sale.customer_name') }}:</b> <a type="button" class="btn btn-link"
        href="{{URL::to('contacts/'.$sell->contact->id)}}" ><strong>{{ $sell->contact->name }}</strong></a><br>
        <b>{{ __('business.address') }}:</b><br>
        @if(!empty($sell->billing_address()))
          {{$sell->billing_address()}}
        @else
          {!! $sell->contact->contact_address !!}
          @if($sell->contact->mobile)
          <br>
              {{__('contact.mobile')}}: {{ $sell->contact->mobile }}
          @endif
          @if($sell->contact->alternate_number)
          <br>
              {{__('contact.alternate_contact_number')}}: {{ $sell->contact->alternate_number }}
          @endif
          @if($sell->contact->landline)
            <br>
              {{__('contact.landline')}}: {{ $sell->contact->landline }}
          @endif
        @endif
        
      </div>
      <div class="col-sm-4">
      {{-- @if(in_array('tables' ,$enabled_modules))
         <strong>@lang('restaurant.table'):</strong>
          {{$sell->table->name ?? ''}}<br>
      @endif --}}
      @if(in_array('service_staff' ,$enabled_modules))
          <strong>@lang('restaurant.service_staff'):</strong>
          {{$sell->service_staff->user_full_name ?? ''}}<br>
      @endif

      <strong>@lang('sale.shipping'):</strong>
      <span class="label @if(!empty($shipping_status_colors[$sell->shipping_status])) {{$shipping_status_colors[$sell->shipping_status]}} @else {{'bg-gray'}} @endif">{{$shipping_statuses[$sell->shipping_status] ?? '' }}</span><br>
      @if(!empty($sell->shipping_address()))
        {{$sell->shipping_address()}}
      @else
        {{$sell->shipping_address ?? '--'}}
      @endif
      @if(!empty($sell->delivered_to))
        <br><strong>@lang('lang_v1.delivered_to'): </strong> {{$sell->delivered_to}}
      @endif
      @if(!empty($sell->shipping_custom_field_1))
        <br><strong>{{$custom_labels['shipping']['custom_field_1'] ?? ''}}: </strong> {{$sell->shipping_custom_field_1}}
      @endif
      @if(!empty($sell->shipping_custom_field_2))
        <br><strong>{{$custom_labels['shipping']['custom_field_2'] ?? ''}}: </strong> {{$sell->shipping_custom_field_2}}
      @endif
      @if(!empty($sell->shipping_custom_field_3))
        <br><strong>{{$custom_labels['shipping']['custom_field_3'] ?? ''}}: </strong> {{$sell->shipping_custom_field_3}}
      @endif
      @if(!empty($sell->shipping_custom_field_4))
        <br><strong>{{$custom_labels['shipping']['custom_field_4'] ?? ''}}: </strong> {{$sell->shipping_custom_field_4}}
      @endif
      @if(!empty($sell->shipping_custom_field_5))
        <br><strong>{{$custom_labels['shipping']['custom_field_5'] ?? ''}}: </strong> {{$sell->shipping_custom_field_5}}
      @endif
      @php
        $medias = $sell->media->where('model_media_type', 'shipping_document')->all();
      @endphp
      @if(count($medias))
        @include('sell.partials.media_table', ['medias' => $medias])
      @endif

      @if(in_array('types_of_service' ,$enabled_modules))
        @if(!empty($sell->types_of_service))
          <strong>@lang('lang_v1.types_of_service'):</strong>
          {{$sell->types_of_service->name}}<br>
        @endif
        @if(!empty($sell->types_of_service->enable_custom_fields))
          <strong>{{ $custom_labels['types_of_service']['custom_field_1'] ?? __('lang_v1.service_custom_field_1' )}}:</strong>
          {{$sell->service_custom_field_1}}<br>
          <strong>{{ $custom_labels['types_of_service']['custom_field_2'] ?? __('lang_v1.service_custom_field_2' )}}:</strong>
          {{$sell->service_custom_field_2}}<br>
          <strong>{{ $custom_labels['types_of_service']['custom_field_3'] ?? __('lang_v1.service_custom_field_3' )}}:</strong>
          {{$sell->service_custom_field_3}}<br>
          <strong>{{ $custom_labels['types_of_service']['custom_field_4'] ?? __('lang_v1.service_custom_field_4' )}}:</strong>
          {{$sell->service_custom_field_4}}
        @endif
      @endif
      </div>
    </div>
    <br>
    <br>
      <button data-href="/entry/transaction/{{$sell->id}}" data-container=".view_modal" class="btn btn-modal bg-blue">@lang("home.Entry")</button>
      @if(auth()->user()->hasRole('Admin#' . session('business.id')) ||  auth()->user()->can('product.avarage_cost')) 
      <a href="/sells/{{$sell->id}}/edit"   class="btn bg-yellow" >@lang("messages.edit")</a>
      @endif
      <div class="row">
      <div class="col-sm-12 col-xs-12">
        <h4>{{ __('sale.products') }}:</h4>
      </div>

      <div class="col-sm-12 col-xs-12">
        <div class="table-responsive">
          @include('sale_pos.partials.sale_line_details',$separate)
        </div>
      </div>
    </div>
    <div class="row">
      <!--<div class="col-sm-12 col-xs-12">-->
      <!--  <h4>{{ __('sale.all_delivered') }}:</h4>-->
      <!--</div>-->

      <!--<div class="col-sm-12 col-xs-12">-->
      <!--  <div class="table-responsive">-->
      <!--    @include('sale_pos.partials.sale_line_delivered_details')-->
      <!--  </div>-->
      <!--</div>-->
    </div>



    <div class="row">
      <div class="col-sm-12 col-xs-12">
        <h4>{{ __('sale.payment_info') }}:</h4>
      </div>
      <div class="col-md-6 col-sm-12 col-xs-12">
        <div class="table-responsive">
          <table class="table bg-gray">
            <tr class="bg-green">
              <th>#</th>
              <th>{{ __('messages.date') }}</th>
              <th>{{ __('purchase.ref_no') }}</th>
              <th>{{ __('sale.amount') }}</th>
              <th>{{ __('sale.payment_mode') }}</th>
              <th>{{ __('sale.payment_note') }}</th>
            </tr>
            @php
              $total_paid = 0;
            @endphp
            @forelse($sell->payment_lines as $payment_line)
              @php
               
                if($payment_line->is_return == 1){
                  $total_paid -= $payment_line->amount;
                } else {
                  $total_paid += $payment_line->amount;
                }
              @endphp
              <tr>
                <td>
                  @if(auth()->user()->hasRole('Admin#' . session('business.id')) ||  auth()->user()->can('product.avarage_cost'))
                    {{ $loop->iteration }}
                  @else
                    {{ "--" }}
                  @endif
                </td>
                <td>
                  @if(auth()->user()->hasRole('Admin#' . session('business.id')) ||  auth()->user()->can('product.avarage_cost'))
                    {{ @format_date($payment_line->paid_on) }}
                  @else
                    {{ "--" }}
                  @endif
                </td>
                <td>
                  @if(auth()->user()->hasRole('Admin#' . session('business.id')) ||  auth()->user()->can('product.avarage_cost'))
                    {{ $payment_line->payment_ref_no }}
                  @else
                    {{ "--" }}
                  @endif
                </td>
                <td>
                  @if(auth()->user()->hasRole('Admin#' . session('business.id')) ||  auth()->user()->can('product.avarage_cost'))
                    <span class="display_currency" data-currency_symbol="true">{{ $payment_line->amount }} @if($payment_line->return_payment == 1) <i class="fa fa-undu" style="color:red;font-size:19px;font-weight:700"></i> @endif</span>
                  @else
                    {{ "--" }}
                  @endif
                </td>
                <td>
                  @if(auth()->user()->hasRole('Admin#' . session('business.id')) ||  auth()->user()->can('product.avarage_cost'))
                    {{ $payment_types[$payment_line->method] ?? $payment_line->method }}
                    @if($payment_line->is_return == 1)
                      <br/>
                      ( {{ __('lang_v1.change_return') }} )
                    @endif
                  @else
                    {{ "--" }}
                  @endif
                </td>
                <td>
                  @if(auth()->user()->hasRole('Admin#' . session('business.id')) ||  auth()->user()->can('product.avarage_cost'))
                    @if($payment_line->note) 
                    {{ ucfirst($payment_line->note) }}
                    @else
                    --
                    @endif
                  @else
                    {{ "--" }}
                  @endif
                </td>

              </tr>
            @empty
                @forelse($separate as $payment_line)
                  @php
                    if($payment_line->is_return == 1){
                      $total_paid -= $payment_line->amount;
                    } else {
                      $total_paid += $payment_line->amount;
                    }
                  @endphp
                  <tr>
                    <td>
                      @if(auth()->user()->hasRole('Admin#' . session('business.id')) ||  auth()->user()->can('product.avarage_cost'))
                        {{ $loop->iteration }}
                      @else
                        {{ "--" }}
                      @endif
                    </td>
                    <td>
                      @if(auth()->user()->hasRole('Admin#' . session('business.id')) ||  auth()->user()->can('product.avarage_cost'))
                        {{ @format_date($payment_line->paid_on) }}
                      @else
                        {{ "--" }}
                      @endif
                    </td>
                    <td>
                      @if(auth()->user()->hasRole('Admin#' . session('business.id')) ||  auth()->user()->can('product.avarage_cost'))
                        {{ $payment_line->payment_ref_no }}
                      @else
                        {{ "--" }}
                      @endif
                    </td>
                    <td>
                      @if(auth()->user()->hasRole('Admin#' . session('business.id')) ||  auth()->user()->can('product.avarage_cost'))
                        <span class="display_currency" data-currency_symbol="true">{{ $payment_line->amount }} @if($payment_line->return_payment == 1) <i class="fa fa-undu" style="color:red;font-size:19px;font-weight:700"></i> @endif</span>
                      @else
                        {{ "--" }}
                      @endif
                    </td>
                    <td>
                      @if(auth()->user()->hasRole('Admin#' . session('business.id')) ||  auth()->user()->can('product.avarage_cost'))
                        {{ $payment_types[$payment_line->method] ?? $payment_line->method }}
                        @if($payment_line->is_return == 1)
                          <br/>
                          ( {{ __('lang_v1.change_return') }} )
                        @endif
                      @else
                        {{ "--" }}
                      @endif
                    </td>
                    <td>
                      @if(auth()->user()->hasRole('Admin#' . session('business.id')) ||  auth()->user()->can('product.avarage_cost'))
                        @if($payment_line->note) 
                        {{ ucfirst($payment_line->note) }}
                        @else
                        --
                        @endif
                      @else
                        {{ "--" }}
                      @endif
                    </td>

                  </tr>
                @empty
                
                @endforelse              
            @endforelse
            
            @forelse($separate_delivery as $payment_line)
                @if($payment_line != null)
                  @php
                    if($payment_line->is_return == 1){
                      $total_paid -= $payment_line->amount;
                    } else {
                      $total_paid += $payment_line->amount;
                    }
                  @endphp
                  <tr>
                    <td>
                      @if(auth()->user()->hasRole('Admin#' . session('business.id')) ||  auth()->user()->can('product.avarage_cost'))
                        {{ $loop->iteration }}
                      @else
                        {{ "--" }}
                      @endif
                    </td>
                    <td>
                      @if(auth()->user()->hasRole('Admin#' . session('business.id')) ||  auth()->user()->can('product.avarage_cost'))
                        {{ @format_date($payment_line->paid_on) }}
                      @else
                        {{ "--" }}
                      @endif
                    </td>
                    <td>
                      @if(auth()->user()->hasRole('Admin#' . session('business.id')) ||  auth()->user()->can('product.avarage_cost'))
                        {{ $payment_line->payment_ref_no }}
                      @else
                        {{ "--" }}
                      @endif
                    </td>
                    <td>
                      @if(auth()->user()->hasRole('Admin#' . session('business.id')) ||  auth()->user()->can('product.avarage_cost'))
                        <span class="display_currency" data-currency_symbol="true">{{ $payment_line->amount }} @if($payment_line->return_payment == 1) <i class="fa fa-undu" style="color:red;font-size:19px;font-weight:700"></i> @endif</span>
                      @else
                        {{ "--" }}
                      @endif
                    </td>
                    <td>
                      @if(auth()->user()->hasRole('Admin#' . session('business.id')) ||  auth()->user()->can('product.avarage_cost'))
                        {{ $payment_types[$payment_line->method] ?? $payment_line->method }}
                        @if($payment_line->is_return == 1)
                          <br/>
                          ( {{ __('lang_v1.change_return') }} )
                        @endif
                      @else
                        {{ "--" }}
                      @endif
                    </td>
                    <td>
                      @if(auth()->user()->hasRole('Admin#' . session('business.id')) ||  auth()->user()->can('product.avarage_cost'))
                        @if($payment_line->note) 
                        {{ ucfirst($payment_line->note) }}
                        @else
                        --
                        @endif
                      @else
                        {{ "--" }}
                      @endif
                    </td>

                  </tr>
                @endif
            @empty
            
            @endforelse   @forelse($sell->payment_lines as $payment_line)
              @php
               
                if($payment_line->is_return == 1){
                  $total_paid -= $payment_line->amount;
                } else {
                  $total_paid += $payment_line->amount;
                }
              @endphp
              <tr>
                <td>
                  @if(auth()->user()->hasRole('Admin#' . session('business.id')) ||  auth()->user()->can('product.avarage_cost'))
                    {{ $loop->iteration }}
                  @else
                    {{ "--" }}
                  @endif
                </td>
                <td>
                  @if(auth()->user()->hasRole('Admin#' . session('business.id')) ||  auth()->user()->can('product.avarage_cost'))
                    {{ @format_date($payment_line->paid_on) }}
                  @else
                    {{ "--" }}
                  @endif
                </td>
                <td>
                  @if(auth()->user()->hasRole('Admin#' . session('business.id')) ||  auth()->user()->can('product.avarage_cost'))
                    {{ $payment_line->payment_ref_no }}
                  @else
                    {{ "--" }}
                  @endif
                </td>
                <td>
                  @if(auth()->user()->hasRole('Admin#' . session('business.id')) ||  auth()->user()->can('product.avarage_cost'))
                    <span class="display_currency" data-currency_symbol="true">{{ $payment_line->amount }} @if($payment_line->return_payment == 1) <i class="fa fa-undu" style="color:red;font-size:19px;font-weight:700"></i> @endif</span>
                  @else
                    {{ "--" }}
                  @endif
                </td>
                <td>
                  @if(auth()->user()->hasRole('Admin#' . session('business.id')) ||  auth()->user()->can('product.avarage_cost'))
                    {{ $payment_types[$payment_line->method] ?? $payment_line->method }}
                    @if($payment_line->is_return == 1)
                      <br/>
                      ( {{ __('lang_v1.change_return') }} )
                    @endif
                  @else
                    {{ "--" }}
                  @endif
                </td>
                <td>
                  @if(auth()->user()->hasRole('Admin#' . session('business.id')) ||  auth()->user()->can('product.avarage_cost'))
                    @if($payment_line->note) 
                    {{ ucfirst($payment_line->note) }}
                    @else
                    --
                    @endif
                  @else
                    {{ "--" }}
                  @endif
                </td>

              </tr>
            @empty
                @forelse($separate as $payment_line)
                  @php
                    if($payment_line->is_return == 1){
                      $total_paid -= $payment_line->amount;
                    } else {
                      $total_paid += $payment_line->amount;
                    }
                  @endphp
                  <tr>
                    <td>
                      @if(auth()->user()->hasRole('Admin#' . session('business.id')) ||  auth()->user()->can('product.avarage_cost'))
                        {{ $loop->iteration }}
                      @else
                        {{ "--" }}
                      @endif
                    </td>
                    <td>
                      @if(auth()->user()->hasRole('Admin#' . session('business.id')) ||  auth()->user()->can('product.avarage_cost'))
                        {{ @format_date($payment_line->paid_on) }}
                      @else
                        {{ "--" }}
                      @endif
                    </td>
                    <td>
                      @if(auth()->user()->hasRole('Admin#' . session('business.id')) ||  auth()->user()->can('product.avarage_cost'))
                        {{ $payment_line->payment_ref_no }}
                      @else
                        {{ "--" }}
                      @endif
                    </td>
                    <td>
                      @if(auth()->user()->hasRole('Admin#' . session('business.id')) ||  auth()->user()->can('product.avarage_cost'))
                        <span class="display_currency" data-currency_symbol="true">{{ $payment_line->amount }} @if($payment_line->return_payment == 1) <i class="fa fa-undu" style="color:red;font-size:19px;font-weight:700"></i> @endif</span>
                      @else
                        {{ "--" }}
                      @endif
                    </td>
                    <td>
                      @if(auth()->user()->hasRole('Admin#' . session('business.id')) ||  auth()->user()->can('product.avarage_cost'))
                        {{ $payment_types[$payment_line->method] ?? $payment_line->method }}
                        @if($payment_line->is_return == 1)
                          <br/>
                          ( {{ __('lang_v1.change_return') }} )
                        @endif
                      @else
                        {{ "--" }}
                      @endif
                    </td>
                    <td>
                      @if(auth()->user()->hasRole('Admin#' . session('business.id')) ||  auth()->user()->can('product.avarage_cost'))
                        @if($payment_line->note) 
                        {{ ucfirst($payment_line->note) }}
                        @else
                        --
                        @endif
                      @else
                        {{ "--" }}
                      @endif
                    </td>

                  </tr>
                @empty
                
                @endforelse              
            @endforelse
            
            @forelse($separate_delivery as $payment_line)
                @if($payment_line != null)
                  @php
                    if($payment_line->is_return == 1){
                      $total_paid -= $payment_line->amount;
                    } else {
                      $total_paid += $payment_line->amount;
                    }
                  @endphp
                  <tr>
                    <td>
                      @if(auth()->user()->hasRole('Admin#' . session('business.id')) ||  auth()->user()->can('product.avarage_cost'))
                        {{ $loop->iteration }}
                      @else
                        {{ "--" }}
                      @endif
                    </td>
                    <td>
                      @if(auth()->user()->hasRole('Admin#' . session('business.id')) ||  auth()->user()->can('product.avarage_cost'))
                        {{ @format_date($payment_line->paid_on) }}
                      @else
                        {{ "--" }}
                      @endif
                    </td>
                    <td>
                      @if(auth()->user()->hasRole('Admin#' . session('business.id')) ||  auth()->user()->can('product.avarage_cost'))
                        {{ $payment_line->payment_ref_no }}
                      @else
                        {{ "--" }}
                      @endif
                    </td>
                    <td>
                      @if(auth()->user()->hasRole('Admin#' . session('business.id')) ||  auth()->user()->can('product.avarage_cost'))
                        <span class="display_currency" data-currency_symbol="true">{{ $payment_line->amount }} @if($payment_line->return_payment == 1) <i class="fa fa-undu" style="color:red;font-size:19px;font-weight:700"></i> @endif</span>
                      @else
                        {{ "--" }}
                      @endif
                    </td>
                    <td>
                      @if(auth()->user()->hasRole('Admin#' . session('business.id')) ||  auth()->user()->can('product.avarage_cost'))
                        {{ $payment_types[$payment_line->method] ?? $payment_line->method }}
                        @if($payment_line->is_return == 1)
                          <br/>
                          ( {{ __('lang_v1.change_return') }} )
                        @endif
                      @else
                        {{ "--" }}
                      @endif
                    </td>
                    <td>
                      @if(auth()->user()->hasRole('Admin#' . session('business.id')) ||  auth()->user()->can('product.avarage_cost'))
                        @if($payment_line->note) 
                        {{ ucfirst($payment_line->note) }}
                        @else
                        --
                        @endif
                      @else
                        {{ "--" }}
                      @endif
                    </td>

                  </tr>
                @endif
            @empty
            
            @endforelse   
            
          </table>
        </div>
      </div>
      <div class="col-md-6 col-sm-12 col-xs-12">
        <div class="table-responsive">
          <table class="table bg-gray">
            
            <tr>
              <th>{{ __('sale.total') }}:</th>
              <td></td>
                  
              <td>
                @if(auth()->user()->hasRole('Admin#' . session('business.id')) ||  auth()->user()->can('product.avarage_cost'))
                  <span class="display_currency pull-right" data-currency_symbol="true">{{ number_format($sell->total_before_tax,config("constants.currency_precision"))  }}</span>
                @else
                  {{ "--" }}
                @endif
            </td>
            </tr>
            <tr>
              <th>{{ __('sale.discount') }}:</th>
              <td><b>(-)</b></td>
              <td>
                @if(auth()->user()->hasRole('Admin#' . session('business.id')) ||  auth()->user()->can('product.avarage_cost'))
                  <div class="pull-right">
                    <span class=" " @if( $sell->discount_type == 'fixed') data-currency_symbol=" " @endif>
                      @if($sell->discount_type == "fixed_after_vat")
                      @php 
                            $tax = \App\TaxRate::find($sell->tax_id);
                            $discount = ( $sell->discount_amount * 100 ) / ( 100 + $tax->amount ) ;
                      @endphp
                          {{ number_format($discount,2) }}
                      @else
                        {{ number_format($sell->discount_amount,2) }} {{ " " .$currency_symbol}}
                      @endif    
                    </span> @if( $sell->discount_type == 'percentage') {{ '%'}} @endif
                  </div>
                  @else
                    {{ "--" }}
                  @endif
              </td>
            </tr>
            @if(in_array('types_of_service' ,$enabled_modules) && !empty($sell->packing_charge))
              <tr>
                <th>{{ __('lang_v1.packing_charge') }}:</th>
                <td><b>(+)</b></td>
                <td>
                  @if(auth()->user()->hasRole('Admin#' . session('business.id')) ||  auth()->user()->can('product.avarage_cost'))
                  <div class="pull-right"><span class=" " @if( $sell->packing_charge_type == 'fixed') data-currency_symbol=" " @endif>{{ number_format($sell->packing_charge,2) }}  {{ " " .$currency_symbol}}</span> 
                    @if( $sell->packing_charge_type == 'percent') {{ '%'}} @endif </div>
                  @else
                  {{ "--" }}
                  @endif
                </td>
              </tr>
            @endif
            @if(session('business.enable_rp') == 1 && !empty($sell->rp_redeemed) )
              <tr>
                <th>{{session('business.rp_name')}}:</th>
                <td><b>(-)</b></td>
                <td> 
                  @if(auth()->user()->hasRole('Admin#' . session('business.id')) ||  auth()->user()->can('product.avarage_cost'))
                    <span class="  pull-right" data-currency_symbol=" ">{{number_format( $sell->rp_redeemed_amount,2) }}{{ " " .$currency_symbol}}</span>
                  @else
                    {{ "--" }}
                  @endif  
                </td>
              </tr>
            @endif
            <tr>
              <th>{{ __('sale.order_tax') }}:</th>
              <td><b>(+)</b></td>
              <td class="text-right">
                @if(auth()->user()->hasRole('Admin#' . session('business.id')) ||  auth()->user()->can('product.avarage_cost'))
                    @if(!empty($order_taxes))
                      @foreach($order_taxes as $k => $v)
                        <strong><small>{{$k}}</small></strong>  &nbsp;&nbsp;  <span class="  pull-right" data-currency_symbol=" ">{{ number_format($v,2) }} {{ " " .$currency_symbol}}</span><br>
                        
                      @endforeach
                    @else
                    0.00
                      @endif
                @else
                  {{ "--" }}
                @endif
              </td>
            </tr>
            <tr>
              <th>{{ __('sale.shipping') }}: @if($sell->shipping_details)({{$sell->shipping_details}}) @endif</th>
              <td><b>(+)</b></td>
              <td>
              @if(auth()->user()->hasRole('Admin#' . session('business.id')) ||  auth()->user()->can('product.avarage_cost'))
                <span class="  pull-right" data-currency_symbol=" ">{{ number_format($sell->shipping_charges,2) }}  {{ " " .$currency_symbol}}</span>
              @else
                {{ "--" }}
              @endif 
              </td>
            </tr>
            <tr>
              <th>{{ __('lang_v1.round_off') }}: </th>
              <td></td>
              <td>
                @if(auth()->user()->hasRole('Admin#' . session('business.id')) ||  auth()->user()->can('product.avarage_cost'))
                  <span class="  pull-right" data-currency_symbol=" ">{{ number_format($sell->round_off_amount,2) }}  {{ " " .$currency_symbol}}</span>
                @else
                  {{ "--" }}
                @endif 
              </td>
            </tr>
            <tr>
              <th>{{ __('sale.total_payable') }}: </th>
              <td></td>
              <td>
                @if(auth()->user()->hasRole('Admin#' . session('business.id')) ||  auth()->user()->can('product.avarage_cost'))
                  <span class="  pull-right" data-currency_symbol=" ">{{ number_format($sell->final_total,2) }}  {{ " " .$currency_symbol}}</span>
                @else
                  {{ "--" }}
                @endif              
              </td>
            </tr>
            <tr>
              <th>{{ __('sale.total_paid') }}:</th>
              <td></td>
              <td>
                @if(auth()->user()->hasRole('Admin#' . session('business.id')) ||  auth()->user()->can('product.avarage_cost'))
                  <span class="  pull-right" data-currency_symbol=" " >{{ number_format($total_paid,2) }} {{ " " .$currency_symbol}}</span>
                @else
                  {{ "--" }}
                @endif   
              </td>
            </tr>
            <tr>
              <th>{{ __('sale.total_remaining') }}:</th>
              <td></td>
              <td>
                <!-- Converting total paid to string for floating point substraction issue -->
                @php
                  $total_paid = (string) $total_paid;
                @endphp
                @if(auth()->user()->hasRole('Admin#' . session('business.id')) ||  auth()->user()->can('product.avarage_cost'))
                  <span class="  pull-right" data-currency_symbol=" " >{{ number_format($sell->final_total - $total_paid,2) }} {{ " " .$currency_symbol}}</span>
                @else
                  {{ "--" }}
                @endif
              </td>
            </tr>
          </table>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-sm-6">
        <strong>{{ __( 'sale.sell_note')}}:</strong><br>
        <p class="well well-sm no-shadow bg-gray">
          @if($sell->additional_notes)
            {!! nl2br($sell->additional_notes)  !!}
          @else
            --
          @endif
        </p>
      </div>
      <div class="col-sm-6">
        <strong>{{ __( 'sale.staff_note')}}:</strong><br>
        <p class="well well-sm no-shadow bg-gray">
          @if($sell->staff_note)
            {!!   nl2br($sell->staff_note)  !!}
          @else
            --
          @endif
        </p>
      </div>
    </div>
    <div class="row">
      <div class="col-md-12">
            <strong>{{ __('lang_v1.activities') }}:</strong><br>
            @includeIf('activity_log.activities', ['activity_type' => 'sell'])
        </div>
    </div>
  </div>
  <div class="modal-footer">
    <a href="#" class="print-invoice btn btn-success" data-href="{{route('sell.printInvoice', [$sell->id])}}?package_slip=true"><i class="fas fa-file-alt" aria-hidden="true"></i> @lang("lang_v1.packing_slip")</a>

    @can('print_invoice')
      <a href="#" class="print-invoice btn btn-primary" data-href="{{route('sell.printInvoice', [$sell->id])}}"><i class="fa fa-print" aria-hidden="true"></i> @lang("lang_v1.print_invoice")</a>
    @endcan
      <button type="button" class="btn btn-default no-print" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>
  </div>
</div>

<script type="text/javascript">
  $(document).ready(function(){
    var element = $('div.modal-xl');
    __currency_convert_recursively(element);
  });
</script>
