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
      @if(!empty($single_payment_line->transaction))
        <div class="row">
              @if(in_array($single_payment_line->transaction->type, ['purchase', 'purchase_return']))
                  <div class="col-xs-6">
                    @lang('purchase.supplier'):
                    <address>
                      <strong>{{ $single_payment_line->transaction->contact->supplier_business_name }}</strong>
                              {{ $single_payment_line->transaction->contact->name }}
                              {!! $single_payment_line->transaction->contact->contact_address !!}
                              @if(!empty($single_payment_line->transaction->contact->tax_number))
                                <br>@lang('contact.tax_no'): {{$single_payment_line->transaction->contact->tax_number}}
                              @endif

                              @if(!empty($single_payment_line->transaction->contact->mobile))
                                <br>@lang('contact.mobile'): {{$single_payment_line->transaction->contact->mobile}}
                              @endif

                              @if(!empty($single_payment_line->transaction->contact->email))
                                <br>@lang('business.email'): {{$single_payment_line->transaction->contact->email}}
                              @endif

                    </address>
                  </div>
                  <div class="col-xs-6">
                    @lang('business.business'):

                    <address>
                      <strong>{{ $single_payment_line->transaction->business->name }}</strong>

                      @if(!empty($single_payment_line->transaction->location))
                        {{ $single_payment_line->transaction->location->name }}

                        @if(!empty($single_payment_line->transaction->location->landmark))
                          <br>{{$single_payment_line->transaction->location->landmark}}
                        @endif

                        @if(!empty($single_payment_line->transaction->location->city) || !empty($single_payment_line->transaction->location->state) || !empty($single_payment_line->transaction->location->country))
                          <br>{{implode(',', array_filter([$single_payment_line->transaction->location->city, $single_payment_line->transaction->location->state, $single_payment_line->transaction->location->country]))}}
                        @endif

                      @endif
                      
                      @if(!empty($single_payment_line->transaction->business->tax_number_1))
                        <br>{{$single_payment_line->transaction->business->tax_label_1}}: {{$single_payment_line->transaction->business->tax_number_1}}
                      @endif

                      @if(!empty($single_payment_line->transaction->business->tax_number_2))
                        <br>{{$single_payment_line->transaction->business->tax_label_2}}: {{$single_payment_line->transaction->business->tax_number_2}}
                      @endif

                      @if(!empty($single_payment_line->transaction->location))

                        @if(!empty($single_payment_line->transaction->location->mobile))
                          <br>@lang('contact.mobile'): {{$single_payment_line->transaction->location->mobile}}
                        @endif

                        @if(!empty($single_payment_line->transaction->location->email))
                          <br>@lang('business.email'): {{$single_payment_line->transaction->location->email}}
                        @endif

                      @endif

                    </address>

                  </div>
              @else

                <div class="col-xs-6">
                    @if($single_payment_line->transaction->type != 'payroll' && !empty($single_payment_line->transaction->contact))
                      @lang('contact.customer'):
                      <address>
                        <strong>{{ $single_payment_line->transaction->contact->name ?? '' }}</strong>
                      
                        {!! $single_payment_line->transaction->contact->contact_address !!}
                        @if(!empty($single_payment_line->transaction->contact->tax_number))
                          <br>@lang('contact.tax_no'): {{$single_payment_line->transaction->contact->tax_number}}
                        @endif
                        @if(!empty($single_payment_line->transaction->contact->mobile))
                          <br>@lang('contact.mobile'): {{$single_payment_line->transaction->contact->mobile}}
                        @endif
                        @if(!empty($single_payment_line->transaction->contact->email))
                          <br>@lang('business.email'): {{$single_payment_line->transaction->contact->email}}
                        @endif
                      </address>
                    @else
                    
                        @if(!empty($single_payment_line->transaction->transaction_for))
                          @lang('essentials::lang.payroll_for'):
                          <address>
                              <strong>{{ $single_payment_line->transaction->transaction_for->user_full_name }}</strong>
                              @if(!empty($single_payment_line->transaction->transaction_for->address))
                                  <br>{{$single_payment_line->transaction->transaction_for->address}}
                              @endif
                              @if(!empty($single_payment_line->transaction->transaction_for->contact_number))
                                  <br>@lang('contact.mobile'): {{$single_payment_line->transaction->transaction_for->contact_number}}
                              @endif
                              @if(!empty($single_payment_line->transaction->transaction_for->email))
                                  <br>@lang('business.email'): {{$single_payment_line->transaction->transaction_for->email}}
                              @endif
                          </address>
                        @endif
                    
                    @endif
                </div>

                <div class="col-xs-6">
                    @lang('business.business'):
                    <address>
                      <strong>{{ $single_payment_line->transaction->business->name }}</strong>
                      @if(!empty($single_payment_line->transaction->location))
                        {{ $single_payment_line->transaction->location->name }}
                        @if(!empty($single_payment_line->transaction->location->landmark))
                          <br>{{$single_payment_line->transaction->location->landmark}}
                        @endif
                        @if(!empty($single_payment_line->transaction->location->city) || !empty($single_payment_line->transaction->location->state) || !empty($single_payment_line->transaction->location->country))
                          <br>{{implode(',', array_filter([$single_payment_line->transaction->location->city, $single_payment_line->transaction->location->state, $single_payment_line->transaction->location->country]))}}
                        @endif
                      @endif
                      
                      @if(!empty($single_payment_line->transaction->business->tax_number_1))
                        <br>{{$single_payment_line->transaction->business->tax_label_1}}: {{$single_payment_line->transaction->business->tax_number_1}}
                      @endif

                      @if(!empty($single_payment_line->transaction->business->tax_number_2))
                        <br>{{$single_payment_line->transaction->business->tax_label_2}}: {{$single_payment_line->transaction->business->tax_number_2}}
                      @endif

                      @if(!empty($single_payment_line->transaction->location))
                        @if(!empty($single_payment_line->transaction->location->mobile))
                          <br>@lang('contact.mobile'): {{$single_payment_line->transaction->location->mobile}}
                        @endif
                        @if(!empty($single_payment_line->transaction->location->email))
                          <br>@lang('business.email'): {{$single_payment_line->transaction->location->email}}
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
            @endif
            <br>
            <strong>@lang('purchase.qty') :</strong> <span class="display_currency" data-currency_symbol="true">{{$quantity_all1}}</span>
            <br>

        </div>

        <div class="col-xs-6">
      
          <b>@lang('purchase.ref_no'):</b> 
            @if(!empty($single_payment_line->ref_no))
              {{ $single_payment_line->ref_no }}
            @else
              --
            @endif
          <br/>

          {{-- <b>@lang('lang_v1.received_on'):</b> {{ @format_datetime($single_payment_line->created_at) }}<br/> --}}
          <b>@lang('lang_v1.received_on'):</b> {{  $single_payment_line->date  }}<br/>
          <br>

          @if(!empty($single_payment_line->document_path))
            <a href="{{$single_payment_line->document_path}}" class="btn btn-success btn-xs no-print" download="{{$single_payment_line->document_name}}"><i class="fa fa-download" data-toggle="tooltip" title="{{__('purchase.download_document')}}"></i> {{__('purchase.download_document')}}</a>
          @endif

        </div>

        <div class="col-xs-12">
      
          @component('components.widget', ['class' => 'box-primary', 'title' => __('recieved.all_recieved')])
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
                        <th>@lang('warehouse.nameW')</th>
                    </tr>

                    @php
                      $trd = \App\Models\TransactionRecieved::where("transaction_id",$single_payment_line->transaction->id)->first();
                                
                        if(!empty($trd)){
                          $id = $trd->id;
                        }else{
                          $id = 0;
                        }
                        $empty = 0;
                        $total = 0;
                        $total_purchase =  $quantity_all1;
                        $total_wrong = 0;
                        $pre = "";
                        $type = "base";
                    @endphp

                    @forelse ($RecievedPrevious as $Recieved)
                      @php
                          $date = Carbon::now();
                          $date_now       = ($Recieved->TrRecieved->date !=null)? $Recieved->TrRecieved->date : $Recieved->TrRecieved->created_at;
                          $product_name   = $Recieved->product->name;
                          $product_n_id   = $Recieved->product->id;
                          $note           = $Recieved->product->product_description;
                          $Warehouse_name = $Recieved->store->name; 
                          $unt = $Recieved->product->unit->actual_name;
                      @endphp 
                      @if ($pre != $date_now)
                          @if ($pre == "")

                          @else
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
                          @php
                              $style = "";
                              $pre = $date_now;
                          @endphp
                      @endif
                      <tr  @if($type != "other") style="background:#f1f1f1; width:100% !important" @else style="background:#ff5b5b; width:100% !important" @endif>
                            <td>{{($Recieved->TrRecieved->date !=null)? $Recieved->TrRecieved->date : $Recieved->TrRecieved->created_at}}</td>
                            <td>{{$product_name}}</td>
                            <td>{!!strip_tags($note)!!}</td>
                            <td>{{$unt}}</td>
                            <td>{{$Recieved->total_qty}}</td>
                            <td>{{$Recieved->current_qty}}</td>
                            <td>{{$Warehouse_name}}</td>
                      </tr>
                        
                    @empty
                     @forelse ($RecievedWrong as $Wrong)
                        @php
                            $date = Carbon::now();
                            $date_now       = ($Wrong->TrRecieved->date !=null)? $Wrong->TrRecieved->date : $Wrong->TrRecieved->created_at;
                            $product_name   = $Wrong->product->name;
                            $product_n_id   = $Wrong->product->id;
                            $note           = $Wrong->product->product_description;
                            $Warehouse_name = $Wrong->store->name; 
                            $unt = $Wrong->product->unit->actual_name;
                        @endphp 
                        @if ($pre != $date_now)
                            @if ($pre == "")

                            @else
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
                            @php
                                $style = "";
                                $pre = $date_now;
                            @endphp
                        @endif
                        <tr  @if($type != "other") style="background:#f1f1f1; width:100% !important" @else style="background:#ff5b5b; width:100% !important" @endif>
                              <td>{{($Wrong->TrRecieved->date !=null)? $Wrong->TrRecieved->date : $Wrong->TrRecieved->created_at}}</td>
                              <td>{{$product_name}}</td>
                              <td>{!!strip_tags($note)!!}</td>
                              <td>{{$unt}}</td>
                              <td>{{$Wrong->total_qty}}</td>
                              <td>{{$Wrong->current_qty}}</td>
                              <td>{{$Warehouse_name}}</td>
                        </tr>
                        @php $empty = 1; @endphp
                     @empty @endforelse
                    @endforelse
                      @if($empty == 0)
                        @if(count($RecievedWrong)>0)
                        @forelse ($RecievedWrong as $Wrong)
                              @php
                                  $date = Carbon::now();
                                  $date_now       = ($Wrong->TrRecieved->date !=null)? $Wrong->TrRecieved->date : $Wrong->TrRecieved->created_at;
                                  $product_name   = $Wrong->product->name;
                                  $product_n_id   = $Wrong->product->id;
                                  $note           = $Wrong->product->product_description;
                                  $Warehouse_name = $Wrong->store->name; 
                                  $unt = $Wrong->product->unit->actual_name;
                              @endphp 
                              @if ($pre != $date_now)
                                  @if ($pre == "")

                                  @else
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
                                  @php
                                      $style = "";
                                      $pre = $date_now;
                                  @endphp
                              @endif
                              <tr  @if($type != "other") style="background:#f1f1f1; width:100% !important" @else style="background:#ff5b5b; width:100% !important" @endif>
                                    <td>{{($Wrong->TrRecieved->date !=null)? $Wrong->TrRecieved->date : $Wrong->TrRecieved->created_at}}</td>
                                    <td>{{$product_name}}</td>
                                    <td>{!!strip_tags($note)!!}</td>
                                    <td>{{$unt}}</td>
                                    <td>{{$Wrong->total_qty}}</td>
                                    <td>{{$Wrong->current_qty}}</td>
                                    <td>{{$Warehouse_name}}</td>
                              </tr>
                        
                          @empty @endforelse
                        @endif
                      @endif
                    <tfoot>
                      @if(count($RecievedPrevious)>0)
                        <tr class="bg-gray  font-17 footer-total" style="border:1px solid #f1f1f1;  ">
                           
                          <td colspan="3">@lang("sale.total") : {{intval($total_purchase)}} </td>
                          <td colspan="2">@lang("home.total received") :{{intval($quantity_all)}} </td>
                          <td colspan="2">@lang("home.wrong received") : {{intval($quantity_wrg)}} </td>
                         </tr>
                      @elseif(count($RecievedWrong)>0)
                        <tr class="bg-gray  font-17 footer-total" style="border:1px solid #f1f1f1;  ">
                          <td colspan="3">@lang("sale.total") : {{intval($total_purchase)}} </td>
                          <td colspan="2">@lang("home.total received") :{{intval($quantity_all)}} </td>
                          <td colspan="2">@lang("home.wrong received") : {{intval($quantity_wrg)}} </td>
                        </tr>
                      @endif
                    </tfoot>

                  </table>
                </div>
              </div>
            </div>
          @endcomponent 

        </div>
      </div>
    </div>

    <div class="modal-footer">
      <a href="{{\URL::to('reports/receive/'.$id)}}" class="btn btn-primary no-print" target="_blank">@lang( 'messages.print' )</a>
       <button type="button" class="btn btn-default no-print" data-dismiss="modal">@lang( 'messages.close' ) </button>
    </div>

  </div>
</div>