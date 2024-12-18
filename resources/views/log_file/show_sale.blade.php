@extends("layouts.app")

@section("title",__("sale.sell_details"))

@section("content")

<h1>@lang("home.old bill")</h1>
<section class="content">
 
      <div class="row">
        <div class="col-xs-12">
            <p class="pull-right"><b>@lang('messages.date'):</b> {{ @format_date($sell->transaction_date) }}</p>
        </div>
      </div>
      <div class="row">
        @php
          $custom_labels = json_decode(session('business.custom_labels'), true);
        @endphp
        <div class="col-sm-4">
          
          @if($sell->previous != null)
            <b >{{ __('sale.first_no') }}:</b> #{{ $sell->previous }}<br>
            <b>{{ __('sale.secound') }}:</b>   #{{ ($sell->first_ref_no)??"--" }}<br>
            <b>{{ __('sale.prvious_no') }}:</b> #{{ ($sell->refe_no)??"--" }}<br>
            <b>{{ __('sale.final') }}:</b> #{{ $sell->invoice_no }}<br>
          @else
            <b @if($sell->first_ref_no != $new_sell->first_ref_no) class="change-bill"   @endif>{{ __('sale.first_no') }}:</b>   #{{ ($sell->first_ref_no)??"--" }}<br>
            <b @if($sell->refe_no != $new_sell->refe_no) class="change-bill"   @endif>{{ __('sale.prvious_no') }}:</b> #{{ ($sell->refe_no)??"--" }}<br>
            <b @if($sell->invoice_no != $new_sell->invoice_no) class="change-bill"   @endif>{{ __('sale.invoice') }}:</b> #{{ $sell->invoice_no }}<br>
          @endif
            <b @if($sell->project_no != $new_sell->project_no) class="change-bill"   @endif>{{ __('sale.project_no') }}:</b> #{{ $sell->project_no }}<br>
            <b @if($sell->status != $new_sell->status) class="change-bill"   @endif>{{ __('sale.status') }}:</b> 
          
          @if( $sell->status == 'draft' && $sell->sub_status == "proforma")
          {{ __('sale.ApprovedQuotation') }}
          @elseif($sell->status == 'draft' && $sell->is_quotation == 1)
          {{ __('lang_v1.quotation') }}
          @else
          {{ __('sale.' . $sell->status) }}
          @endif
          <br>
          <b @if($sell->payment_status != $new_sell->payment_status) class="change-bill"   @endif>{{ __('sale.payment_status') }}:</b> @if(!empty($sell->payment_status)){{ __('lang_v1.' . $sell->payment_status) }}
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
        
        </div>
        <div @if($sell->contact->supplier_business_name != $new_sell->contact->supplier_business_name) class="change-bill col-sm-4"   @else  class="col-sm-4" @endif   >
          @if(!empty($sell->contact->supplier_business_name))
            {{ $sell->contact->supplier_business_name }}<br>
          @endif
          <b >{{ __('sale.customer_name') }}:</b> <a type="button"  @if($sell->contact->id != $new_sell->contact->id) class=" btn btn-link change-bill "   @else class="btn btn-link" @endif
                  href="{{URL::to('contacts/'.$sell->contact->id)}}" ><strong>{{ $sell->contact->name }}</strong></a><br>
          <b >{{ __('business.address') }}:</b><br>
          
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
        {{-- <span class="label @if(!empty($shipping_status_colors[$sell->shipping_status])) {{$shipping_status_colors[$sell->shipping_status]}} @else {{'bg-gray'}} @endif">{{$shipping_statuses[$sell->shipping_status] ?? '' }}</span><br>
        @if(!empty($sell->shipping_address()))
          {{$sell->shipping_address()}}
        @else
          {{$sell->shipping_address ?? '--'}}
        @endif --}}
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
        {{-- <button data-href="/entry/transaction/{{$sell->id}}" data-container=".view_modal" class="btn btn-modal bg-blue">@lang("home.Entry")</button>
        <a href="/sells/{{$sell->id}}/edit"   class="btn bg-yellow" >@lang("messages.edit")</a>
       --}}
          <div class="row">
          <div class="col-sm-12 col-xs-12">
            <h4>{{ __('sale.products') }}:</h4>
          </div>
  
        <div class="col-sm-12 col-xs-12">
          <div class="table-responsive">
            @include('sale_pos.partials.sale_line_details',["type_archive"=>"archive"])
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
              @foreach($sell->payment_lines as $payment_line)
                @php
                  if($payment_line->is_return == 1){
                    $total_paid -= $payment_line->amount;
                  } else {
                    $total_paid += $payment_line->amount;
                  }
                @endphp
                <tr>
                  <td>{{ $loop->iteration }}</td>
                  <td>{{ @format_date($payment_line->paid_on) }}</td>
                  <td>{{ $payment_line->payment_ref_no }}</td>
                  <td><span class="display_currency" data-currency_symbol="true">{{ $payment_line->amount }}</span></td>
                  <td>
                    {{ $payment_types[$payment_line->method] ?? $payment_line->method }}
                    @if($payment_line->is_return == 1)
                      <br/>
                      ( {{ __('lang_v1.change_return') }} )
                    @endif
                  </td>
                  <td>@if($payment_line->note) 
                    {{ ucfirst($payment_line->note) }}
                    @else
                    --
                    @endif
                  </td>
                </tr>
              @endforeach
            </table>
          </div>
        </div>
        <div class="col-md-6 col-sm-12 col-xs-12">
          <div class="table-responsive">
            <table class="table bg-gray">
              
              <tr>
                <th>{{ __('sale.total') }}:</th>
                <td></td>
                   
                <td><span class="display_currency pull-right" data-currency_symbol="true">{{ number_format($sell->total_before_tax,2)  }}</span></td>
              </tr>
              <tr>
                <th>{{ __('sale.discount') }}:</th>
                <td><b>(-)</b></td>
                <td>
                  <div class="pull-right">
                    <span  @if( ($sell->discount_type != $new_sell->discount_type) || ($sell->discount_amount != $new_sell->discount_amount)  ) class="change-bill display_currency" @else class="display_currency"  @endif @if( $sell->discount_type == 'fixed') data-currency_symbol="true" @endif>
                      @if($sell->discount_type == "fixed_after_vat")
                      @php 
                            $tax = \App\TaxRate::find($sell->tax_id);
                            $discount = ( $sell->discount_amount * 100 ) / ( 100 + $tax->amount ) ;
                      @endphp
                          {{ number_format($discount,2) }}
                      @else
                        {{ number_format($sell->discount_amount,2) }}
                      @endif    
                    </span> @if( $sell->discount_type == 'percentage') {{ '%'}} @endif
                  </div>
                </td>
              </tr>
              @if(in_array('types_of_service' ,$enabled_modules) && !empty($sell->packing_charge))
                <tr>
                  <th>{{ __('lang_v1.packing_charge') }}:</th>
                  <td><b>(+)</b></td>
                  <td><div class="pull-right"><span class="display_currency" @if( $sell->packing_charge_type == 'fixed') data-currency_symbol="true" @endif>{{ number_format($sell->packing_charge,2) }}</span> @if( $sell->packing_charge_type == 'percent') {{ '%'}} @endif </div></td>
                </tr>
              @endif
              @if(session('business.enable_rp') == 1 && !empty($sell->rp_redeemed) )
                <tr>
                  <th>{{session('business.rp_name')}}:</th>
                  <td><b>(-)</b></td>
                  <td> <span class="display_currency pull-right" data-currency_symbol="true">{{number_format( $sell->rp_redeemed_amount,2) }}</span></td>
                </tr>
              @endif
              <tr>
                <th>{{ __('sale.order_tax') }}:</th>
                <td><b>(+)</b></td>
                <td class="text-right">
                   
                  @if(!empty($order_taxes))
                    @foreach($order_taxes as $k => $v)
                      <strong ><small>{{$k}}</small></strong>  &nbsp;&nbsp;  <span   @if($new_order_taxes[$k] != $order_taxes[$k]) class="change-bill display_currency pull-right" @else class= " display_currency pull-right"  @endif  data-currency_symbol="true">{{ number_format($v,2) }}</span><br>
                      
                    @endforeach
                  @else
                  0.00
                  @endif
                </td>
              </tr>
              <tr>
                <th>{{ __('sale.shipping') }}: @if($sell->shipping_details)({{$sell->shipping_details}}) @endif</th>
                <td><b>(+)</b></td>
                <td><span class="display_currency pull-right" data-currency_symbol="true">{{ number_format($sell->shipping_charges,2) }}</span></td>
              </tr>
              <tr>
                <th>{{ __('lang_v1.round_off') }}: </th>
                <td></td>
                <td><span class="display_currency pull-right" data-currency_symbol="true">{{ number_format($sell->round_off_amount,2) }}</span></td>
              </tr>
              <tr>
                <th>{{ __('sale.total_payable') }}: </th>
                <td></td>
                <td><span class="display_currency pull-right" data-currency_symbol="true">{{ number_format($sell->final_total,2) }}</span></td>
              </tr>
              <tr>
                <th>{{ __('sale.total_paid') }}:</th>
                <td></td>
                <td><span class="display_currency pull-right" data-currency_symbol="true" >{{ number_format($total_paid,2) }}</span></td>
              </tr>
              <tr>
                <th>{{ __('sale.total_remaining') }}:</th>
                <td></td>
                <td>
                  <!-- Converting total paid to string for floating point substraction issue -->
                  @php
                    $total_paid = (string) $total_paid;
                  @endphp
                  <span class="display_currency pull-right" data-currency_symbol="true" >{{ number_format($sell->final_total - $total_paid,2) }}</span></td>
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
     
  
 </section>
 
 <h1>@lang("home.new bill")</h1>
 <section class="content">
  <div class="row">
    <div class="col-xs-12">
        <p class="pull-right"><b>@lang('messages.date'):</b> {{ @format_date($new_sell->transaction_date) }}</p>
    </div>
  </div>
  <div class="row">
    @php
      $custom_labels = json_decode(session('business.custom_labels'), true);
    @endphp
    <div class="col-sm-4">
      
      @if($new_sell->previous != null)
       <b>{{ __('sale.first_no') }}:</b> #{{ $new_sell->previous }}<br>
       <b>{{ __('sale.secound') }}:</b>   #{{ ($new_sell->first_ref_no)??"--" }}<br>
       <b>{{ __('sale.prvious_no') }}:</b> #{{ ($new_sell->refe_no)??"--" }}<br>
       <b>{{ __('sale.final') }}:</b> #{{ $new_sell->invoice_no }}<br>
      @else
      <b>{{ __('sale.first_no') }}:</b>   #{{ ($new_sell->first_ref_no)??"--" }}<br>
      <b>{{ __('sale.prvious_no') }}:</b> #{{ ($new_sell->refe_no)??"--" }}<br>
       <b>{{ __('sale.invoice') }}:</b> #{{ $new_sell->invoice_no }}<br>
      @endif
      <b>{{ __('sale.project_no') }}:</b> #{{ $new_sell->project_no }}<br>
      <b>{{ __('sale.status') }}:</b> 
      
      @if( $new_sell->status == 'draft' && $new_sell->sub_status == "proforma")
      {{ __('sale.ApprovedQuotation') }}
      @elseif($new_sell->status == 'draft' && $new_sell->is_quotation == 1)
      {{ __('lang_v1.quotation') }}
      @else
      {{ __('sale.' . $new_sell->status) }}
      @endif
      <br>
      <b>{{ __('sale.payment_status') }}:</b> @if(!empty($new_sell->payment_status)){{ __('lang_v1.' . $new_sell->payment_status) }}
      @endif
      <br><b>{{ __('warehouse.nameW') }}:</b> {{
      
        $store
      
      
      }}
      @if(!empty($custom_labels['sell']['custom_field_1']))
        <br><strong>{{$custom_labels['sell']['custom_field_1'] ?? ''}}: </strong> {{$new_sell->custom_field_1}}
      @endif
      @if(!empty($custom_labels['sell']['custom_field_2']))
        <br><strong>{{$custom_labels['sell']['custom_field_2'] ?? ''}}: </strong> {{$new_sell->custom_field_2}}
      @endif
      @if(!empty($custom_labels['sell']['custom_field_3']))
        <br><strong>{{$custom_labels['sell']['custom_field_3'] ?? ''}}: </strong> {{$new_sell->custom_field_3}}
      @endif
      @if(!empty($custom_labels['sell']['custom_field_4']))
        <br><strong>{{$custom_labels['sell']['custom_field_4'] ?? ''}}: </strong> {{$new_sell->custom_field_4}}
      @endif
      
    </div>
    <div class="col-sm-4">
      @if(!empty($new_sell->contact->supplier_business_name))
        {{ $new_sell->contact->supplier_business_name }}<br>
      @endif
      <b>{{ __('sale.customer_name') }}:</b> <a type="button" class="btn btn-link"
      href="{{URL::to('contacts/'.$new_sell->contact->id)}}" ><strong>{{ $new_sell->contact->name }}</strong></a><br>
      <b>{{ __('business.address') }}:</b><br>
      
        {!! $new_sell->contact->contact_address !!}
        @if($new_sell->contact->mobile)
        <br>
            {{__('contact.mobile')}}: {{ $new_sell->contact->mobile }}
        @endif
        @if($new_sell->contact->alternate_number)
        <br>
            {{__('contact.alternate_contact_number')}}: {{ $new_sell->contact->alternate_number }}
        @endif
        @if($new_sell->contact->landline)
          <br>
            {{__('contact.landline')}}: {{ $new_sell->contact->landline }}
        @endif
   
      
    </div>
    <div class="col-sm-4">
    {{-- @if(in_array('tables' ,$enabled_modules))
       <strong>@lang('restaurant.table'):</strong>
        {{$new_sell->table->name ?? ''}}<br>
    @endif --}}
    @if(in_array('service_staff' ,$enabled_modules))
        <strong>@lang('restaurant.service_staff'):</strong>
        {{$new_sell->service_staff->user_full_name ?? ''}}<br>
    @endif

    <strong>@lang('sale.shipping'):</strong>
    {{-- <span class="label @if(!empty($shipping_status_colors[$new_sell->shipping_status])) {{$shipping_status_colors[$new_sell->shipping_status]}} @else {{'bg-gray'}} @endif">{{$shipping_statuses[$new_sell->shipping_status] ?? '' }}</span><br>
    @if(!empty($new_sell->shipping_address()))
      {{$new_sell->shipping_address()}}
    @else
      {{$new_sell->shipping_address ?? '--'}}
    @endif --}}
    @if(!empty($new_sell->delivered_to))
      <br><strong>@lang('lang_v1.delivered_to'): </strong> {{$new_sell->delivered_to}}
    @endif
    @if(!empty($new_sell->shipping_custom_field_1))
      <br><strong>{{$custom_labels['shipping']['custom_field_1'] ?? ''}}: </strong> {{$new_sell->shipping_custom_field_1}}
    @endif
    @if(!empty($new_sell->shipping_custom_field_2))
      <br><strong>{{$custom_labels['shipping']['custom_field_2'] ?? ''}}: </strong> {{$new_sell->shipping_custom_field_2}}
    @endif
    @if(!empty($new_sell->shipping_custom_field_3))
      <br><strong>{{$custom_labels['shipping']['custom_field_3'] ?? ''}}: </strong> {{$new_sell->shipping_custom_field_3}}
    @endif
    @if(!empty($new_sell->shipping_custom_field_4))
      <br><strong>{{$custom_labels['shipping']['custom_field_4'] ?? ''}}: </strong> {{$new_sell->shipping_custom_field_4}}
    @endif
    @if(!empty($new_sell->shipping_custom_field_5))
      <br><strong>{{$custom_labels['shipping']['custom_field_5'] ?? ''}}: </strong> {{$new_sell->shipping_custom_field_5}}
    @endif
    @php
      $medias = $new_sell->media->where('model_media_type', 'shipping_document')->all();
    @endphp
    @if(count($medias))
      @include('sell.partials.media_table', ['medias' => $medias])
    @endif

    @if(in_array('types_of_service' ,$enabled_modules))
      @if(!empty($new_sell->types_of_service))
        <strong>@lang('lang_v1.types_of_service'):</strong>
        {{$new_sell->types_of_service->name}}<br>
      @endif
      @if(!empty($new_sell->types_of_service->enable_custom_fields))
        <strong>{{ $custom_labels['types_of_service']['custom_field_1'] ?? __('lang_v1.service_custom_field_1' )}}:</strong>
        {{$new_sell->service_custom_field_1}}<br>
        <strong>{{ $custom_labels['types_of_service']['custom_field_2'] ?? __('lang_v1.service_custom_field_2' )}}:</strong>
        {{$new_sell->service_custom_field_2}}<br>
        <strong>{{ $custom_labels['types_of_service']['custom_field_3'] ?? __('lang_v1.service_custom_field_3' )}}:</strong>
        {{$new_sell->service_custom_field_3}}<br>
        <strong>{{ $custom_labels['types_of_service']['custom_field_4'] ?? __('lang_v1.service_custom_field_4' )}}:</strong>
        {{$new_sell->service_custom_field_4}}
      @endif
    @endif
    </div>
  </div>

  <br>

  <br>
    {{-- <button data-href="/entry/transaction/{{$new_sell->id}}" data-container=".view_modal" class="btn btn-modal bg-blue">@lang("home.Entry")</button>
    <a href="/sells/{{$new_sell->id}}/edit"   class="btn bg-yellow" >@lang("messages.edit")</a>
   --}}
      <div class="row">
      <div class="col-sm-12 col-xs-12">
        <h4>{{ __('sale.products') }}:</h4>
      </div>

    <div class="col-sm-12 col-xs-12">
      <div class="table-responsive">
        @include('sale_pos.partials.sale_line_details',["type_archive"=>"archive","new"=>"new"])
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
          @foreach($new_sell->payment_lines as $payment_line)
            @php
              if($payment_line->is_return == 1){
                $total_paid -= $payment_line->amount;
              } else {
                $total_paid += $payment_line->amount;
              }
            @endphp
            <tr>
              <td>{{ $loop->iteration }}</td>
              <td>{{ @format_date($payment_line->paid_on) }}</td>
              <td>{{ $payment_line->payment_ref_no }}</td>
              <td><span class="display_currency" data-currency_symbol="true">{{ $payment_line->amount }}</span></td>
              <td>
                {{ $payment_types[$payment_line->method] ?? $payment_line->method }}
                @if($payment_line->is_return == 1)
                  <br/>
                  ( {{ __('lang_v1.change_return') }} )
                @endif
              </td>
              <td>@if($payment_line->note) 
                {{ ucfirst($payment_line->note) }}
                @else
                --
                @endif
              </td>
            </tr>
          @endforeach
        </table>
      </div>
    </div>
    <div class="col-md-6 col-sm-12 col-xs-12">
      <div class="table-responsive">
        <table class="table bg-gray">
          
          <tr>
            <th>{{ __('sale.total') }}:</th>
            <td></td>
               
            <td><span class="display_currency pull-right" data-currency_symbol="true">{{ number_format($new_sell->total_before_tax,2)  }}</span></td>
          </tr>
          <tr>
            <th>{{ __('sale.discount') }}:</th>
            <td><b>(-)</b></td>
            <td>
              <div class="pull-right">
                <span class="display_currency" @if( $new_sell->discount_type == 'fixed') data-currency_symbol="true" @endif>
                  @if($new_sell->discount_type == "fixed_after_vat")
                  @php 
                        $tax = \App\TaxRate::find($new_sell->tax_id);
                        $discount = ( $new_sell->discount_amount * 100 ) / ( 100 + $tax->amount ) ;
                  @endphp
                      {{ number_format($discount,2) }}
                  @else
                    {{ number_format($new_sell->discount_amount,2) }}
                  @endif    
                </span> @if( $new_sell->discount_type == 'percentage') {{ '%'}} @endif
              </div>
            </td>
          </tr>
          @if(in_array('types_of_service' ,$enabled_modules) && !empty($new_sell->packing_charge))
            <tr>
              <th>{{ __('lang_v1.packing_charge') }}:</th>
              <td><b>(+)</b></td>
              <td><div class="pull-right"><span class="display_currency" @if( $new_sell->packing_charge_type == 'fixed') data-currency_symbol="true" @endif>{{ number_format($new_sell->packing_charge,2) }}</span> @if( $new_sell->packing_charge_type == 'percent') {{ '%'}} @endif </div></td>
            </tr>
          @endif
          @if(session('business.enable_rp') == 1 && !empty($new_sell->rp_redeemed) )
            <tr>
              <th>{{session('business.rp_name')}}:</th>
              <td><b>(-)</b></td>
              <td> <span class="display_currency pull-right" data-currency_symbol="true">{{number_format( $new_sell->rp_redeemed_amount,2) }}</span></td>
            </tr>
          @endif
          <tr>
            <th>{{ __('sale.order_tax') }}:</th>
            <td><b>(+)</b></td>
            <td class="text-right">
               
              @if(!empty($order_taxes))
                @foreach($order_taxes as $k => $v)
                  <strong><small>{{$k}}</small></strong>  &nbsp;&nbsp;  <span class="display_currency pull-right" data-currency_symbol="true">{{ number_format($v,2) }}</span><br>
                  
                @endforeach
              @else
              0.00
              @endif
            </td>
          </tr>
          <tr>
            <th>{{ __('sale.shipping') }}: @if($new_sell->shipping_details)({{$new_sell->shipping_details}}) @endif</th>
            <td><b>(+)</b></td>
            <td><span class="display_currency pull-right" data-currency_symbol="true">{{ number_format($new_sell->shipping_charges,2) }}</span></td>
          </tr>
          <tr>
            <th>{{ __('lang_v1.round_off') }}: </th>
            <td></td>
            <td><span class="display_currency pull-right" data-currency_symbol="true">{{ number_format($new_sell->round_off_amount,2) }}</span></td>
          </tr>
          <tr>
            <th>{{ __('sale.total_payable') }}: </th>
            <td></td>
            <td><span class="display_currency pull-right" data-currency_symbol="true">{{ number_format($new_sell->final_total,2) }}</span></td>
          </tr>
          <tr>
            <th>{{ __('sale.total_paid') }}:</th>
            <td></td>
            <td><span class="display_currency pull-right" data-currency_symbol="true" >{{ number_format($total_paid,2) }}</span></td>
          </tr>
          <tr>
            <th>{{ __('sale.total_remaining') }}:</th>
            <td></td>
            <td>
              <!-- Converting total paid to string for floating point substraction issue -->
              @php
                $total_paid = (string) $total_paid;
              @endphp
              <span class="display_currency pull-right" data-currency_symbol="true" >{{ number_format($new_sell->final_total - $total_paid,2) }}</span></td>
          </tr>
        </table>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-sm-6">
      <strong>{{ __( 'sale.sell_note')}}:</strong><br>
      <p class="well well-sm no-shadow bg-gray">
        @if($new_sell->additional_notes)
          {!! nl2br($new_sell->additional_notes)  !!}
        @else
          --
        @endif
      </p>
    </div>
    <div class="col-sm-6">
      <strong>{{ __( 'sale.staff_note')}}:</strong><br>
      <p class="well well-sm no-shadow bg-gray">
        @if($new_sell->staff_note)
          {!!   nl2br($new_sell->staff_note)  !!}
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
 

 </section>
@endsection

@section("javascript")
<script type="text/javascript">
  $(document).ready(function(){
    var element = $('div.modal-xl');
    __currency_convert_recursively(element);
  });
</script>
@endsection