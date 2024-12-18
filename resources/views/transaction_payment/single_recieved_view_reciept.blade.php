<div class="modal-dialog" role="document" style="width:80%">
  <div class="modal-content">
    <div class="modal-header">
      
      <button type="button" class="close no-print" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title no-print">
        @lang( 'lang_v1.view_Receipt' )
        @if(!empty($single_payment_line->payment_ref_no))
          ( @lang('purchase.ref_no'): {{ $single_payment_line->payment_ref_no }} )
        @endif
      </h4>
      <h4 class="modal-title visible-print-block">
        @if(!empty($single_payment_line->payment_ref_no))
          ( @lang('purchase.ref_no'): {{ $single_payment_line->payment_ref_no }} )
        @endif
      </h4>
    </div>
    <div class="modal-body">
      @if(!empty($transaction))
      <div class="row">
        @if(in_array($transaction->type, ['purchase', 'purchase_return']))
            <div class="col-xs-6">
              @lang('purchase.supplier'):
              <address>
                <strong>{{ $transaction->contact->supplier_business_name }}</strong>
                {{ $transaction->contact->name }}
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
            <div class="col-xs-6">
              @lang('business.business'):
              <address>
                <strong>{{ $transaction->business->name }}</strong>

                @if(!empty($transaction->location))
                  {{ $transaction->location->name }}
                  @if(!empty($transaction->location->landmark))
                    <br>{{$transaction->location->landmark}}
                  @endif
                  @if(!empty($transaction->location->city) || !empty($transaction->location->state) || !empty($transaction->location->country))
                    <br>{{implode(',', array_filter([$transaction->location->city, $transaction->location->state, $transaction->location->country]))}}
                  @endif
                @endif
                
                @if(!empty($transaction->business->tax_number_1))
                  <br>{{$transaction->business->tax_label_1}}: {{$transaction->business->tax_number_1}}
                @endif

                @if(!empty($transaction->business->tax_number_2))
                  <br>{{$transaction->business->tax_label_2}}: {{$transaction->business->tax_number_2}}
                @endif

                @if(!empty($transaction->location))
                  @if(!empty($transaction->location->mobile))
                    <br>@lang('contact.mobile'): {{$transaction->location->mobile}}
                  @endif
                  @if(!empty($transaction->location->email))
                    <br>@lang('business.email'): {{$transaction->location->email}}
                  @endif
                @endif
              </address>
            </div>
        @else
          <div class="col-xs-6">
            @if($transaction->type != 'payroll' && !empty($transaction->contact))
              @lang('contact.customer'):
              <address>
                <strong>{{ $transaction->contact->name ?? '' }}</strong>
                
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
            @else
            @if(!empty($transaction->transaction_for))
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
            @endif
            @endif
          </div>
          <div class="col-xs-6">
            @lang('business.business'):
            <address>
              <strong>{{ $transaction->business->name }}</strong>
              @if(!empty($transaction->location))
                {{ $transaction->location->name }}
                @if(!empty($transaction->location->landmark))
                  <br>{{$transaction->location->landmark}}
                @endif
                @if(!empty($transaction->location->city) || !empty($transaction->location->state) || !empty($transaction->location->country))
                  <br>{{implode(',', array_filter([$transaction->location->city, $transaction->location->state, $transaction->location->country]))}}
                @endif
              @endif
              
              @if(!empty($transaction->business->tax_number_1))
                <br>{{$transaction->business->tax_label_1}}: {{$transaction->business->tax_number_1}}
              @endif

              @if(!empty($transaction->business->tax_number_2))
                <br>{{$transaction->business->tax_label_2}}: {{$transaction->business->tax_number_2}}
              @endif

              @if(!empty($transaction->location))
                @if(!empty($transaction->location->mobile))
                  <br>@lang('contact.mobile'): {{$transaction->location->mobile}}
                @endif
                @if(!empty($transaction->location->email))
                  <br>@lang('business.email'): {{$transaction->location->email}}
                @endif
              @endif
            </address>
          </div>
        @endif
      </div>
      @endif
      <div class="row">
          <br>
          <div class="col-xs-6">
                 <b>@lang('purchase.Receipt_no'):</b> 
              @if(!empty($single_payment_line->reciept_no))
                {{ $single_payment_line->reciept_no }}
              @else
                --
              @endif<br>
            <strong>@lang('purchase.qty') :</strong>
            <span class="display_currency" data-currency_symbol="true">
             @php

            //  dd($DeliveredPrevious);
             @endphp
              {{$TraSeLine}}
            </span>
            
            <!--<strong>@lang('lang_v1.payment_method') :</strong>-->
           <!-- {{ $payment_types[$single_payment_line->method] ?? '' }}<br>-->
           <!--@if($single_payment_line->method == "card")-->
           <!--   <strong>@lang('lang_v1.card_holder_name') :</strong>-->
           <!--   {{ $single_payment_line->card_holder_name }} <br>-->
           <!--   <strong>@lang('lang_v1.card_number') :</strong>~-->
           <!--   {{ $single_payment_line->card_number }} <br>-->
           <!--   <strong>@lang('lang_v1.card_transaction_number') :</strong>-->
           <!--   {{ $single_payment_line->card_transaction_number }}-->
              
           <!-- @elseif($single_payment_line->method == "cheque")-->
           <!--   <strong>@lang('lang_v1.cheque_number') :</strong>-->
           <!--   {{ $single_payment_line->cheque_number }}-->
           <!-- @elseif($single_payment_line->method == "bank_transfer")-->

           <!-- @elseif($single_payment_line->method == "custom_pay_1")-->

           <!--   <strong>@lang('lang_v1.transaction_number') :</strong>-->
           <!--   {{ $single_payment_line->transaction_no }}-->
           <!-- @elseif($single_payment_line->method == "custom_pay_2")-->

           <!--   <strong>@lang('lang_v1.transaction_number') :</strong>-->
           <!--   {{ $single_payment_line->transaction_no }}-->
           <!-- @elseif($single_payment_line->method == "custom_pay_3")-->

           <!--   <strong> @lang('lang_v1.transaction_number'):</strong>-->
           <!--   {{ $single_payment_line->transaction_no }}-->
           <!-- @endif --}}-->
            <!--<strong>@lang('purchase.payment_note') :</strong>-->
            <!--  {{ $single_payment_line->note }}-->
          </div>
          <div class="col-xs-6">
            <b>@lang('purchase.ref_no'):</b> 
              @if(!empty($single_payment_line->ref_no))
                {{ $single_payment_line->ref_no }}
              @else
                {{ $single_payment_line->invoice_no }}
              @endif
            
              <br/>
            <b>@lang('lang_v1.received_on'):</b> {{ @format_datetime($single_payment_line->date) }}<br/>
            <br>
            @if(!empty($single_payment_line->document_path))
              <a href="{{$single_payment_line->document_path}}" class="btn btn-success btn-xs no-print" download="{{$single_payment_line->document_name}}"><i class="fa fa-download" data-toggle="tooltip" title="{{__('purchase.download_document')}}"></i> {{__('purchase.download_document')}}</a>
            @endif
          </div>
          <div class="col-xs-12">
       
            @component('components.widget', ['class' => 'box-primary', 'title' => __('recieved.all_delivered')])
            <div class="row">
                <div class="col-md-12">
                        <div class="table-responsive">
                            <table class="table table-bordered" >
                            <tr style="background:#303030; color:white !important;">
                                <th>@lang('messages.date')</th>
                                <th>@lang('product.product_name')</th>
                                <th>@lang('purchase.payment_note')</th>
                                <th>@lang('product.unit')</th>
                                <th>@lang('purchase.qty_total')</th>
                                <th>@lang('purchase.qty_current')</th>
                                <!--<th>@lang('purchase.note')</th>-->
                                 <th>@lang('warehouse.nameW')</th>
                              </tr>
                            @php
                                $trd = \App\Models\TransactionDelivery::where("transaction_id",$transaction->id)->first();
                                
                                if(!empty($trd)){
                                  $id = $trd->id;
                                }else{
                                  $id = 0;
                                }
                                $empty = 0;
                                $total = 0;
                                $pre = "";
                                $type = "base";
                            @endphp
                            @forelse ($DeliveredPrevious as $Recieved)
                                @php $date = Carbon::now();   @endphp 
                                @if ($pre != $Recieved->created_at)
                                    @if ($pre == "") @else
                                    <tr  style="border:1px solid #f1f1f1;" >
                                        <td>{{""}}</td>	
                                        <td>{{""}}</td>	
                                        <td>{{""}}</td>	
                                        <td>{{""}}</td>	
                                        <td>{{""}}</td>	
                                        <td>{{""}}</td>	
                                        <td>{{""}}</td>	
                                    <tr>
                                    @endif
                                    @php $style = ""; $pre = $Recieved->created_at; @endphp
                                @endif
                                <tr  @if($type != "other") style="background:#f1f1f1; width:100% !important" @else style="background:#ff5b5b; width:100% !important" @endif>
                                      <td>{{$trd->date}}</td>
                                      <td>{{$Recieved->product->name}}</td>
                                      <td>{!!(isset($notes_array[$Recieved->product->id]))?strip_tags($notes_array[$Recieved->product->id]):"---"!!}</td>
                                      <td>{{$Recieved->product->unit->actual_name}}</td>
                                      <td>{{intval($TraSeLine)}}</td>
                                      <td>{{$Recieved->current_qty}}</td>
                                      <td>{{$Recieved->store->name}}</td>
                                  </tr>
                            @empty
                                @forelse ($DeliveredWrong as $Recieved)
                                  @php $empty = 1; $date = Carbon::now(); @endphp 
                                  @if ($pre != $Recieved->created_at)
                                      @if ($pre == "") @else
                                        <tr  style="border:1px solid #f1f1f1;" >
                                            <td>{{""}}</td>	
                                            <td>{{""}}</td>	
                                            <td>{{""}}</td>	
                                            <td>{{""}}</td>	
                                            <td>{{""}}</td>	
                                            <td>{{""}}</td>	
                                            <td>{{""}}</td>	
                                        <tr>
                                      @endif
                                      @php $style = ""; $pre = $Recieved->created_at; @endphp
                                  @endif
                                  <tr  @if($type != "other") style="background:#f1f1f1; width:100% !important" @else style="background:#ff5b5b; width:100% !important" @endif>
                                        <td>{{$trd->date}}</td>
                                        <td>{{$Recieved->product->name}}</td>
                                        <td>{!!(isset($notes_array[$Recieved->product->id]))?strip_tags($notes_array[$Recieved->product->id]):"---"!!}</td>
                                        <td>{{$Recieved->product->unit->actual_name}}</td>
                                        <td>{{$TraSeLine}}</td>
                                        <td>{{$Recieved->current_qty}}</td>
                                        <td>{{$Recieved->store->name}}</td>
                                    </tr>
                                @empty @endforelse
                            @endforelse
                            @if($empty == 0)
                                @forelse ($DeliveredWrong as $Recieved)
                                 @php  $date = Carbon::now(); @endphp 
                                  @if ($pre != $Recieved->created_at)
                                      @if ($pre == "") @else
                                        <tr  style="border:1px solid #f1f1f1;" >
                                            <td>{{""}}</td>	
                                            <td>{{""}}</td>	
                                            <td>{{""}}</td>	
                                            <td>{{""}}</td>	
                                            <td>{{""}}</td>	
                                            <td>{{""}}</td>	
                                            <td>{{""}}</td>	
                                        <tr>
                                      @endif
                                      @php $style = ""; $pre = $Recieved->created_at; @endphp
                                  @endif
                                  <tr  @if($type != "other") style="background:#f1f1f1; width:100% !important" @else style="background:#ff5b5b; width:100% !important" @endif>
                                        <td>{{$trd->date}}</td>
                                        <td>{{$Recieved->product->name}}</td>
                                        <td>{!!(isset($notes_array[$Recieved->product->id]))?strip_tags($notes_array[$Recieved->product->id]):"---"!!}</td>
                                        <td>{{$Recieved->product->unit->actual_name}}</td>
                                        <td>{{$TraSeLine}}</td>
                                        <td>{{$Recieved->current_qty}}</td>
                                        <td>{{$Recieved->store->name}}</td>
                                    </tr>
                                @empty @endforelse
                            @endif
                            <tfoot>
                              @if(count($DeliveredPrevious)>0)
                                <tr class="bg-gray  font-17 footer-total" style="border:1px solid #f1f1f1;  ">
                                  
                                  <td colspan="3">@lang("sale.total") : {{intval($TraSeLine)}} </td>
                                  <td colspan="2">@lang("home.total delivered") :{{intval($DelPrevious)}} </td>
                                  <td colspan="2">@lang("home.wrong delivered") : {{intval($DelWrong)}} </td>
                                </tr>
                              @elseif(count($DeliveredWrong)>0)
                                  <tr class="bg-gray  font-17 footer-total" style="border:1px solid #f1f1f1;  ">
                                    <td colspan="3">@lang("sale.total") : {{intval($TraSeLine)}} </td>
                                    <td colspan="2">@lang("home.total delivered") :{{intval($DelPrevious)}} </td>
                                    <td colspan="2">@lang("home.wrong delivered") : {{intval($DelWrong)}} </td>
                                  </tr>
                              @endif
                            </tfoot>
                          </table>
                          @lang("purchase.total_remain") : {{ $TraSeLine - $DelPrevious_total }}
                    </div>
                </div>
            </div>
            @endcomponent 
          </div>






      </div>
    </div>
    <div class="modal-footer">
      <a href="{{\URL::to('reports/delivery/'.$single_payment_line->id)}}" class="btn btn-primary no-print" target="_blank">@lang( 'messages.print' )</a>
      <button type="button" class="btn btn-default no-print" data-dismiss="modal">@lang( 'messages.close' )
      </button>
    </div>
  </div>
</div>