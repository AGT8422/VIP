
@extends("layouts.app")

@section("title",__("purchase.purchase_details"))

@section("content")
 
  <h1 style="padding:10px">@lang("home.new bill")</h1>
 <hr class="hr_class">
<section class="content">
  <div class="modal-header">
      <h4 class="modal-title" id="modalTitle"> @lang('purchase.purchase_details') (<b>@lang('purchase.ref_no'):</b> #{{ $purchase->ref_no }})
      </h4>
  </div>
  <div class="modal-body">

    <div class="row">
      <div class="col-sm-12">
        <p  @if($purchase->transaction_date !=  $new_purchase->transaction_date) class="pull-right change-bill" @else class="pull-right" @endif ><b>@lang('messages.date'):</b> {{ @format_date($purchase->transaction_date) }}</p>
      </div>
    </div>
    
    <div class="row invoice-info">

      <div class="col-sm-4 invoice-col"  >
        @lang('purchase.supplier'):
        <address>
          <a type="button"  @if($purchase->contact->id !=  $new_purchase->contact->id) class="change-bill btn btn-link" @else class="btn btn-link" @endif  
            href="{{URL::to('contacts/'.$purchase->contact->id)}}" ><strong>{{ $purchase->contact->supplier_business_name .  " " .   $purchase->contact->name  }}</strong></a>
  
          @if(!empty($purchase->contact->address_line_1))
            <br>{{$purchase->contact->address_line_1}}
          @endif
          @if(!empty($purchase->contact->address_line_2))
            <br>{{$purchase->contact->address_line_2}}
          @endif
          @if(!empty($purchase->contact->city) || !empty($purchase->contact->state) || !empty($purchase->contact->country))
            <br>{{implode(',', array_filter([$purchase->contact->city, $purchase->contact->state, $purchase->contact->country, $purchase->contact->zip_code]))}}
          @endif
          @if(!empty($purchase->contact->tax_number))
            <br>@lang('contact.tax_no'): {{$purchase->contact->tax_number}}
          @endif
          @if(!empty($purchase->contact->mobile))
            <br>@lang('contact.mobile'): {{$purchase->contact->mobile}}
          @endif
          @if(!empty($purchase->contact->email))
            <br>@lang('business.email'): {{$purchase->contact->email}}
          @endif
        </address>
        @if($purchase->document_path)
          
          <a @if($purchase->document_path !=  $new_purchase->document_path) class="change-bill btn btn-sm btn-success pull-left no-print" @else class="btn btn-sm btn-success pull-left no-print" @endif href="{{$purchase->document_path}}" 
          download="{{$purchase->document_name}}"  >
            <i class="fa fa-download"></i> 
              &nbsp;{{ __('purchase.download_document') }}
          </a>
        @endif
        @php
          
          
        @endphp
        &nbsp;&nbsp;
        {{-- <button data-href="/entry/transaction/{{$purchase->id}}" data-container=".view_modal" class="btn btn-modal bg-blue">@lang("home.Entry")</button> --}}
        {{-- @if(!auth()->user()->can('warehouse.views') && !auth()->user()->can('admin_supervisor.views') && !auth()->user()->can('admin_without.views') && !auth()->user()->can('manufuctoring.views')) --}}
          {{-- <a href="/purchases/{{$purchase->id}}/edit"   class="btn bg-yellow" >@lang("messages.edit")</a> --}}
        {{-- @endif --}}
      </div>

      <div class="col-sm-4 invoice-col">
        @lang('business.business'):
        <address>
          <strong>{{ $purchase->business->name }}</strong>
          {{ $purchase->location->name }}
          @if(!empty($purchase->location->landmark))
            <br>{{$purchase->location->landmark}}
          @endif
          @if(!empty($purchase->location->city) || !empty($purchase->location->state) || !empty($purchase->location->country))
            <br>{{implode(',', array_filter([$purchase->location->city, $purchase->location->state, $purchase->location->country]))}}
          @endif
          
          @if(!empty($purchase->business->tax_number_1))
            <br>{{$purchase->business->tax_label_1}}: {{$purchase->business->tax_number_1}}
          @endif

          @if(!empty($purchase->business->tax_number_2))
            <br>{{$purchase->business->tax_label_2}}: {{$purchase->business->tax_number_2}}
          @endif

          @if(!empty($purchase->location->mobile))
            <br>@lang('contact.mobile'): {{$purchase->location->mobile}}
          @endif
          @if(!empty($purchase->location->email))
            <br>@lang('business.email'): {{$purchase->location->email}}
          @endif
        </address>
      </div>

      <div class="col-sm-4 invoice-col">
        <b @if( $purchase->ref_no != $new_purchase->ref_no ) class="change-bill" @endif >@lang('purchase.ref_no'):</b> #{{ $purchase->ref_no }}<br/>
        <b @if( $purchase->transaction_date != $new_purchase->transaction_date ) class="change-bill" @endif >@lang('messages.date'):</b> {{ @format_date($purchase->transaction_date) }}<br/>
        <b @if( $purchase->status != $new_purchase->status ) class="change-bill" @endif >@lang('purchase.purchase_status'):</b> {{ __('lang_v1.' . $purchase->status) }}<br>
        <b @if( $purchase->payment_status != $new_purchase->payment_status ) class="change-bill" @endif >@lang('purchase.payment_status'):</b> {{ __('lang_v1.' . $purchase->payment_status) }}<br>
        <b @if( $purchase->store != $new_purchase->store ) class="change-bill" @endif >@lang('warehouse.nameW'):</b> {{ $store }}<br>
        @if($purchase->cost_center != null)
        <b @if( $purchase->cost_center->id != $new_purchase->cost_center->id ) class="change-bill" @endif >@lang('home.Cost Center'):</b> {{ $purchase->cost_center->name }}<br>
        @endif
        @if($purchase->sup_refe)
          <div>
            <strong @if( $purchase->sup_refe != $new_purchase->sup_refe ) class="change-bill" @endif > @lang("purchase.sup_refe") : </strong> 
            {{$purchase->sup_refe}}
          </div>
        @endif
      </div>
    </div>

    <br>
    <div class="row">
      <div class="col-sm-12 col-xs-12">
        <div class="table-responsive">
          <table class="table bg-gray">
            <thead>
              <tr class="bg-green">
                <th># </th>
                <th>@lang('product.product_name')</th>
                <th class="text-right">@lang('purchase.purchase_quantity')</th>
                <th class="text-right">@lang( 'lang_v1.unit_cost_before_discount' )</th>
                <th>@lang('home.Price Including Tax')</th>
                <th class="text-right">@lang( 'home.Discount' )</th>
                <th class="text-right">@lang('purchase.unit_cost_before_tax_')</th>
                <th class="no-print text-right">@lang('purchase.unit_cost_before_tax')</th>
                <th class="no-print text-right">@lang('purchase.subtotal_before_tax')</th>
                <th class="text-right">@lang('sale.tax')</th>
                {{-- <th class="text-right">@lang('purchase.unit_selling_price')</th> --}}
                @if(session('business.enable_lot_number'))
                  <th>@lang('lang_v1.lot_number')</th>
                @endif
                @if(session('business.enable_product_expiry'))
                  <th>@lang('product.mfg_date')</th>
                  <th>@lang('product.exp_date')</th>
                @endif
                <th class="text-right">@lang('sale.subtotal')</th>
              </tr>
            </thead>
            
            @php 
          
              $total_before_tax = 0.00;
              $total_befor_taxx  = 0; 
            @endphp
            @foreach($purchase->purchase_lines as  $purchase_line)

              <?php
                  $tax_price = $purchase_line->purchase_price;
              
                  $tx = 0 ;
                
                  foreach ($array as $key => $value) {
                      if($key == $purchase_line->tax_id ){
                        $tx = $value;
                      }
                  }
      
                 if (isset($purchase_line->transaction->tax->amount)) {
                    $tax_price =  
                    ($tx/100)* $purchase_line->purchase_price  +  $purchase_line->purchase_price  ;
                }

                $total_befor_taxx += $purchase_line->quantity * ($purchase_line->pp_without_discount - ($purchase_line->discount_percent*100/$purchase_line->pp_without_discount));
              ?> 
              <tr>
                <td >{{ $loop->iteration }} 
                </td>
                <td>
                  @php
                    $qty_check = 0; 
                    $product_check = 0; 
                    $pp_without_discount_check = 0; 
                    $discount_percent_check = 0; 
                    $purchase_price_check = 0; 
                    $tax_check = 0; 
                  @endphp

                  @foreach($new_purchase->purchase_lines as $it)
                    @if($last != null)
                      @php
                       $line =  $purchase_line->id;
                       $id_compare =  $it->new_id;
                      @endphp
                    @else
                      @php
                       $line       =  $purchase_line->new_id;
                       $id_compare =  $it->id;
                      @endphp
                    @endif
                    @if($line == $id_compare)
                      @php
                          $qty_old = $it->quantity; 
                          $qty_new = $purchase_line->quantity; 
                          if($qty_old!=$qty_new){$qty_check = 1;}
                          $product_old =$it->product->id; 
                          $product_new =$purchase_line->product->id; 
                          if($product_old!=$product_new){$product_check = 1;}
                          $pp_without_discount_old =$it->pp_without_discount; 
                          $pp_without_discount_new =$purchase_line->pp_without_discount; 
                          if($pp_without_discount_old!=$pp_without_discount_new){$pp_without_discount_check = 1;}
                          $discount_percent_old =$it->discount_percent; 
                          $discount_percent_new =$purchase_line->discount_percent; 
                          if($discount_percent_old!=$discount_percent_new){$discount_percent_check = 1;}
                          $purchase_price_old =$it->purchase_price; 
                          $purchase_price_new =$purchase_line->purchase_price; 
                          if($purchase_price_old!=$purchase_price_new){$purchase_price_check = 1;}
                          $tax_old = $it->transaction->tax->amount ;
                          $tax_new = $purchase_line->transaction->tax->amount;
                          if($tax_old!=$tax_new){$tax_check = 1;} 
                          
                        @endphp
                    @endif
                  @endforeach
                  
                  <a @if( $product_check == 1 ) class="change-bill" @endif  href="/item-move/{{$purchase_line->product->id}}"  target="_blank">
                  {{ $purchase_line->product->name }}
                  </a>
                
                  
                  @if( $purchase_line->product->type == 'variable')
                  - {{ $purchase_line->variations->product_variation->name}}
                  - {{ $purchase_line->variations->name}}
                  @endif
                  {{-- <div class="btn-group"><button type="button" class="btn btn-info dropdown-toggle btn-xs no-print" data-toggle="dropdown" aria-expanded="false">@lang("messages.actions")<span class="caret"></span><span class="sr-only">Toggle Dropdown</span></button><ul class="dropdown-menu dropdown-menu-left" role="menu">
                        <li><a data-href="{{action('ProductController@view', [ $purchase_line->product->id])}}" class="btn-modal" data-container=".products_modal"><i class="fa fa-eye"></i>@lang("messages.view")</a></li>
                        <li><a href="{{action('ProductController@edit', [ $purchase_line->product->id])}}"><i class="glyphicon glyphicon-edit"></i>@lang("messages.edit")</a></li>
                        <li><a href="{{action('ItemMoveController@index', [$purchase_line->product->id])}}"><i class="fas fa-history"></i>@lang("lang_v1.product_stock_history")</a></li>
                    </ul>
                      <button type="button" style="margin-left:10px" class="btn btn-primary btn-xs btn-modal no-print" id="view_s" data-container=".products_modal" data-href="{{action('ProductController@viewStock', [$purchase_line->product->id])}}">@lang('lang_v1.view_Stock')</button> 
                      <button type="button" style="margin-left:10px" class="btn btn-second btn-xs btn-modal no-print" data-container=".stocks_modal" data-href="{{action('ProductController@viewUnrecieved', [$purchase_line->product->id])}}">@lang('recieved.should_recieved')</button> 
                </div> --}}
                </td>
              
                <td @if($qty_check == 1) class="change-bill"  @endif><span class="display_currency" data-is_quantity="true" data-currency_symbol="false">{{ $purchase_line->quantity }}</span> @if(!empty($purchase_line->sub_unit)) {{$purchase_line->sub_unit->short_name}} @else {{$purchase_line->product->unit->short_name}} @endif</td>
                
                <td @if($pp_without_discount_check == 1) class="text-right change-bill" @else class="text-right" @endif>
                  <span class="display_currency" data-currency_symbol="true">
                      @php $prices = rtrim($purchase_line->pp_without_discount, '0'); @endphp
                      {{rtrim($prices, '.')}}
                  </span>
                </td>
                
                <td @if($pp_without_discount_check == 1) class="text-right change-bill"  @else class="text-right" @endif   >
                  <span class="display_currency" data-currency_symbol="true">
                    {{($purchase_line->pp_without_discount*$tx/100) + $purchase_line->pp_without_discount}} 
                  </span>
                </td>
                
                <td @if($discount_percent_check == 1) class="text-right change-bill" @else class="text-right" @endif   >
                  <span class="display_currency">
                    {{$purchase_line->discount_percent}}
                  </span> 
                  {{  ($purchase_line->transaction->inline_discount_type == 1)?trans('home.AED'):' ' }}  
                </td>

                <td @if($purchase_price_check == 1) class="text-right change-bill" @else class="text-right" @endif>
                  <span class="display_currency" data-currency_symbol="true">
                    @php $price_after = rtrim($purchase_line->purchase_price, '0'); @endphp
                    {{rtrim($price_after, '.')}}
                  </span>
                </td>

                <td  @if($purchase_price_check == 1) class="change-bill no-print text-right" @else class="no-print text-right"  @endif ><span class="display_currency" data-currency_symbol="true">{{($purchase_line->purchase_price + $purchase_line->purchase_price*$tx/100)}}</span></td>
                
                <td class="no-print text-right">
                  <span class="display_currency" data-currency_symbol="true">
                    @php  $pr = number_format(($purchase_line->quantity * ($purchase_line->pp_without_discount - ($purchase_line->discount_percent*100/$purchase_line->pp_without_discount))),4); $subtotal = rtrim($pr , '0');  @endphp
                    {{rtrim($subtotal, '.')}}
                  </span>
                </td>
                
                <td @if($product_check == 1) class="text-right change-bill" @else class="text-right" @endif   >
                  <span class="display_currency" data-currency_symbol="true">
                      {{ isset($purchase_line->transaction->tax)?$purchase_line->transaction->tax->amount.'%':0 }} 
                  </span> 
                  <br/>
                  @if(!empty($taxes[$purchase_line->tax_id])) 
                    <small>
                      ( {{ $taxes[$purchase_line->tax_id]}} ) 
                    </small>
                  @endif
                </td>
            
                @php
                  $sp = $purchase_line->variations->default_sell_price;
                  if(!empty($purchase_line->sub_unit->base_unit_multiplier)) {
                    $sp = $sp * $purchase_line->sub_unit->base_unit_multiplier;
                  }
                @endphp
                {{-- <td class="text-right"><span class="display_currency" data-currency_symbol="true">{{$sp}} </span></td> --}}
                @if(session('business.enable_lot_number'))
                  <td>{{$purchase_line->lot_number}}</td>
                @endif

                @if(session('business.enable_product_expiry'))
                <td>
                  @if( !empty($purchase_line->product->expiry_period_type) )
                    @if(!empty($purchase_line->mfg_date))
                      {{ @format_date($purchase_line->mfg_date) }}
                    @endif
                  @else
                    @lang('product.not_applicable')
                  @endif
                </td>
                <td>
                  @if( !empty($purchase_line->product->expiry_period_type) )
                    @if(!empty($purchase_line->exp_date))
                      {{ @format_date($purchase_line->exp_date) }}
                    @endif
                  @else
                    @lang('product.not_applicable')
                  @endif
                
                </td>
                @endif
                <td class="text-right"><span class="display_currency" data-currency_symbol="true">
                {{ number_format(($tax_price * $purchase_line->quantity),2) }}   
                </span></td>
              </tr>
              @php 
                $total_before_tax += $purchase_line->quantity * $purchase_line->purchase_price  ;
              @endphp
            @endforeach
          </table>
        </div>
      </div>
    </div>
    <br>
    <div class="row">
      <div class="col-sm-12 col-xs-12">
        
        <h4>{{ __('lang_v1.recieved_balance') }}:</h4>
            <div class="table-responsive">
              <table class="table table-recieve bg-gray" >
                <thead>
                  <tr style=" ">
                    <th>@lang('purchase.ref_no')</th>
                    <th>@lang('messages.date')</th>
                    <th>@lang('product.product_name')</th>
                    <th>@lang('product.unit')</th>
                    <th>@lang('purchase.amount_current')</th>
                    <th>@lang('warehouse.nameW')</th>
                    <th>@lang('purchase.payment_note')</th>
                    <th>@lang('home.Product Cost')</th>
                  </tr>
                </thead>
              @php
                $empty = 0;
                $total = 0;
                $total_wrong = 0;
                $pre = "";
                $type = "base";
              @endphp
              @forelse ($RecievedPrevious as $Recieved)
                @php
                  $date = Carbon::now();
                  $date_now = $Recieved->created_at;
                  $product_name = "";
                  $counter = 1; 
                  $counter_all = 1; 
                  foreach($product_list as $key => $product_l){
                    if($Recieved->product_name == $product_l ){
                      $product_name = $product_l;
                      $product_n_id = $key;
                    }
                    $counter = $counter + 1 ;
                  }
                  if($product_name == ""){
                    foreach($product_list_all as $key1 => $product_l_all){
                      if($Recieved->product_name == $product_l_all ){
                        $product_name = $product_l_all;
                        $product_n_id = $key1;
                        $type = "other";
                      }
                      $counter_all = $counter_all + 1 ;
                    }
                    $total = $total ;
                    $total_wrong = $total_wrong + $Recieved->current_qty ;
                  }else{
                    $type = "base"; 
                    $total = $total + $Recieved->current_qty;
                  }
                  
                  $Warehouse_name = "";
                  $counter_1 = 1; 
                  foreach($Warehouse_list as $key => $Warehouse_l){
                    if($Recieved->store_id == $key ){
                      $Warehouse_name = $Warehouse_l;
                  }
                  $counter_1 = $counter_1 + 1 ;
                  }
                  foreach($unit as $un){
                    if($Recieved->unit_id == $un->id ){
                      $unt = $un->actual_name;
                    }
                  }
      
                @endphp 
                @if ($pre != $date_now)
                  @if ($pre == "")
                  @else
                    <tr style="background:#f1f1f1; width:100% !important" >
                      <td>{{""}}</td>	
                      <td>{{""}}</td>	
                      <td>{{""}}</td>	
                      <td>{{""}}</td>	
                      <td>{{""}}</td>	
                      <td>{{""}}</td>	
                    <tr>
                  @endif
                  @php
                    $pre = $date_now;
                  @endphp
                @endif
                <tr @if($type != "other") style="background:#f1f1f1; width:100% !important" @else style="background:#ff5b5b; width:100% !important" @endif>
                  <td>{{$Recieved->TrRecieved->reciept_no}}</td>
                  <td>{{$Recieved->created_at}}</td>
                  <td>
                    <a href="/item-move/{{$Recieved->product->id}}"  target="_blank">
                      {{$product_name}}
                    </a>
                    <br>
                    {{-- <div class="btn-group no-print"><button type="button" class="btn btn-info dropdown-toggle btn-xs no-print no-print" data-toggle="dropdown" aria-expanded="false">@lang("messages.actions")<span class="caret"></span><span class="sr-only">Toggle Dropdown</span></button><ul class="dropdown-menu dropdown-menu-left" role="menu">
                        <li><a data-href="{{action('ProductController@view', [ $Recieved->product_id])}}" class="btn-modal no-print" data-container=".products_modal"><i class="fa fa-eye"></i>@lang("messages.view")</a></li>
                        <li><a href="{{action('ProductController@edit', [ $Recieved->product_id])}}"><i class="glyphicon glyphicon-edit no-print"></i>@lang("messages.edit")</a></li>
                        <li><a href="{{action('ItemMoveController@index', [$Recieved->product_id])}}"><i class="fas fa-history "></i>@lang("lang_v1.product_stock_history")</a></li>
                      </ul>
                        <button type="button" style="margin-left:10px" class="btn btn-primary btn-xs btn-modal no-print" id="view_s" data-container=".products_modal" data-href="{{action('ProductController@viewStock', [$Recieved->product_id])}}">@lang('lang_v1.view_Stock')</button> 
                        <button type="button" style="margin-left:10px" class="btn bg-yellow btn-xs btn-modal no-print" data-container=".stocks_modal" data-href="{{action('ProductController@viewUnrecieved', [$Recieved->product_id])}}">@lang('recieved.should_recieved')</button> 
                    </div> --}}
                  
                  </td>
                  <td>{{$unt}}</td>
                  <td>{{$Recieved->current_qty}}</td>
                  <td>{{$Warehouse_name}}</td>
                  <td>{{$Recieved->note}}</td>
                  <td>@format_currency(\App\Product::product_cost_expense($Recieved->product_id,$Recieved->transaction_id,$Recieved->transaction_deliveries_id)) </td>
                </tr>
                
              @empty  
                    @forelse ($RecievedWrong as $RecWrong)
                                    
                        @php
                            $empty =1;
                            $date = Carbon::now(); 
                            if(!in_array($RecWrong->product->id,$product_list_all)){
                                $type = "other";
                            }else{
                                $type = "base";
                            }
                            $total_wrong += $RecWrong->current_qty;
                        @endphp

                        @if ($pre != $RecWrong->created_at)
                            @if ($pre == "")  @else
                            <tr  style="border:1px solid #f1f1f1;" >
                                <td>{{""}}</td>	
                                <td>{{""}}</td>	
                                <td>{{""}}</td>	
                                <td>{{""}}</td>	
                                <td>{{""}}</td>	
                            <tr>
                            @endif
                            @php $style = ""; $pre = $RecWrong->created_at; @endphp
                        @endif
                        <tr  @if($type != "other") style="background:#f1f1f1; width:100% !important" @else style="background:#ff5b5b; width:100% !important" @endif>
                            <td>
                              <td>{{$RecWrong->TrRecieved->reciept_no}}</td>
                              <td>{{$RecWrong->created_at}}</td>
                              <a href="/item-move/{{$RecWrong->product->id}}"  target="_blank">
                                {{$RecWrong->product->name}}
                              </a>
                                <br>
                                <div class="btn-group no-print"><button type="button" class="btn btn-info dropdown-toggle btn-xs no-print no-print" data-toggle="dropdown" aria-expanded="false">@lang("messages.actions")<span class="caret"></span><span class="sr-only">Toggle Dropdown</span></button><ul class="dropdown-menu dropdown-menu-left" role="menu">
                                    <li><a data-href="{{action('ProductController@view', [ $RecWrong->product_id])}}" class="btn-modal no-print" data-container=".products_modal"><i class="fa fa-eye"></i>@lang("messages.view")</a></li>
                                    <li><a href="{{action('ProductController@edit', [ $RecWrong->product_id])}}"><i class="glyphicon glyphicon-edit no-print"></i>@lang("messages.edit")</a></li>
                                    <li><a href="{{action('ItemMoveController@index', [$RecWrong->product_id])}}"><i class="fas fa-history "></i>@lang("lang_v1.product_stock_history")</a></li>
                                  </ul>
                                    <button type="button" style="margin-left:10px" class="btn btn-primary btn-xs btn-modal no-print" id="view_s" data-container=".products_modal" data-href="{{action('ProductController@viewStock', [$RecWrong->product_id])}}">@lang('lang_v1.view_Stock')</button> 
                                    <button type="button" style="margin-left:10px" class="btn bg-yellow btn-xs btn-modal no-print" data-container=".stocks_modal" data-href="{{action('ProductController@viewUnrecieved', [$RecWrong->product_id])}}">@lang('recieved.should_recieved')</button> 
                                </div>
                              </td>
                            <td>{{$RecWrong->product->unit->actual_name}}</td>
                            <td>{{$RecWrong->current_qty}}</td>
                            <td>{{$RecWrong->store->name}}</td>
                            <td></td>
                            <td></td>
                        </tr>
                    @empty @endforelse
              @endforelse 
              @if($empty == 0)
                  @forelse ($RecievedWrong as $RecWrong)
                      @php
                          $date = Carbon::now(); 
                          if(!in_array($RecWrong->product->id,$product_list_all)){
                              $type = "other";
                          }else{
                              $type = "base";
                          }
                          $total_wrong += $RecWrong->current_qty;
                      @endphp

                      @if ($pre != $RecWrong->created_at)
                          @if ($pre == "")  @else
                          <tr  style="border:1px solid #f1f1f1;" >
                              <td>{{""}}</td>	
                              <td>{{""}}</td>	
                              <td>{{""}}</td>	
                              <td>{{""}}</td>	
                              <td>{{""}}</td>	
                          <tr>
                          @endif
                          @php $style = ""; $pre = $RecWrong->created_at; @endphp
                      @endif
                      <tr  @if($type != "other") style="background:#f1f1f1; width:100% !important" @else style="background:#ff5b5b; width:100% !important" @endif>
                        <td>{{$RecWrong->TrRecieved->reciept_no}}</td>
                        <td>{{$RecWrong->created_at}}</td>  
                        <td>
                          <a href="/item-move/{{$RecWrong->product->id}}"  target="_blank">
                            {{$RecWrong->product->name}}
                          </a>
                              <br>
                              <div class="btn-group no-print"><button type="button" class="btn btn-info dropdown-toggle btn-xs no-print no-print" data-toggle="dropdown" aria-expanded="false">@lang("messages.actions")<span class="caret"></span><span class="sr-only">Toggle Dropdown</span></button><ul class="dropdown-menu dropdown-menu-left" role="menu">
                                  <li><a data-href="{{action('ProductController@view', [ $RecWrong->product_id])}}" class="btn-modal no-print" data-container=".products_modal"><i class="fa fa-eye"></i>@lang("messages.view")</a></li>
                                  <li><a href="{{action('ProductController@edit', [ $RecWrong->product_id])}}"><i class="glyphicon glyphicon-edit no-print"></i>@lang("messages.edit")</a></li>
                                  <li><a href="{{action('ItemMoveController@index', [$RecWrong->product_id])}}"><i class="fas fa-history "></i>@lang("lang_v1.product_stock_history")</a></li>
                                </ul>
                                  <button type="button" style="margin-left:10px" class="btn btn-primary btn-xs btn-modal no-print" id="view_s" data-container=".products_modal" data-href="{{action('ProductController@viewStock', [$RecWrong->product_id])}}">@lang('lang_v1.view_Stock')</button> 
                                  <button type="button" style="margin-left:10px" class="btn bg-yellow btn-xs btn-modal no-print" data-container=".stocks_modal" data-href="{{action('ProductController@viewUnrecieved', [$RecWrong->product_id])}}">@lang('recieved.should_recieved')</button> 
                              </div>
                          </td>
                          <td>{{$RecWrong->product->unit->actual_name}}</td>
                          <td>{{$RecWrong->current_qty}}</td>
                          <td>{{$RecWrong->store->name}}</td>
                          
                          <td></td>
                          <td></td>
                      </tr>
                  @empty @endforelse
                @endif
              <tfoot>
                <tr class="bg-gray  font-17 footer-total" style="border:1px solid #f1f1f1;  ">
                <td class="text-center " colspan="2"><strong>@lang('sale.total'):</strong></td>
                <td>{{$total}}</td>
                <td></td>
                <td>Wrong receipt : {{$total_wrong}}</td>
                <td></td>
        
              
                </tr>
              </tfoot>
              </table>
          </div>
        </div>
      
  
    </div>

    <br>
    <div class="row">
      
      <div class="col-sm-12 col-xs-12">
        <h4>{{ __('sale.payment_info') }}:</h4>
      </div>
      <div class="col-md-6 col-sm-12 col-xs-12">
        <div class="table-responsive">
          <table class="table">
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

            @php  
              $array_pay_keys      = [];
              $new_pay_paid_on     = [];
              $new_pay_amount      = [];
              $new_pay_method      = [];
              $new_pay_note        = [];
            @endphp

            @foreach ($new_payment as $key => $item)
              @php
              $array_pay_keys[]               = $item->id;
              $new_pay_paid_on[$item->id]     = $item->paid_on;
              $new_pay_amount[$item->id]      = $item->amount;
              $new_pay_method[$item->id]      = $item->method;
              $new_pay_note[$item->id]        = $item->note;
              @endphp 
            @endforeach
            
            @forelse($all_payment as $payment_line)
              @php
                $total_paid += $payment_line->amount;
               @endphp
              <tr>
                <td  >{{ $loop->iteration }}</td>
                <td @if(in_array($payment_line->line_id,$array_pay_keys))  @if($new_pay_paid_on[$payment_line->line_id] != $payment_line->paid_on) class="  col-xs-2" @else  class="col-xs-2" @endif @else  class="col-xs-2" @endif>{{ @format_date($payment_line->paid_on) }}</td>
                <td  >{{ $payment_line->payment_ref_no }}</td>
                <td @if(in_array($payment_line->line_id,$array_pay_keys))  @if($new_pay_amount[$payment_line->line_id] != $payment_line->amount) class="  col-xs-2" @else  class="col-xs-2" @endif @else  class="col-xs-2" @endif><span class="display_currency" data-currency_symbol="true">@format_currency($payment_line->amount)</span></td>
                <td @if(in_array($payment_line->line_id,$array_pay_keys))  @if($new_pay_method[$payment_line->line_id] != $payment_line->method) class=" col-xs-2" @else  class="col-xs-2" @endif @else  class="col-xs-2" @endif>{{ $payment_methods[$payment_line->method] ?? '' }}</td>
                <td @if(in_array($payment_line->line_id,$array_pay_keys))  @if($new_pay_note[$payment_line->line_id] != $payment_line->note) class="  col-xs-2" @else  class="col-xs-2" @endif @else  class="col-xs-2" @endif>@if($payment_line->note) 
                  {{ ucfirst($payment_line->note) }}
                  @else
                  --
                  @endif
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="5" class="text-center">
                  @lang('purchase.no_payments')
                </td>
              </tr>
            @endforelse
          </table>
        </div>
      </div>
    
      <div class="col-md-6 col-sm-12 col-xs-12">
        <div class="table-responsive">
          <table class="table">
            <!-- <tr class="hide">
              <th>@lang('purchase.total_before_tax'): </th>
              <td></td>
              <td><span class="display_currency pull-right">  span></td>
            </tr> -->
            <?php
                  
                  $total_shiping_amount = 0;
            
                ?>
            <tr>
              <th>@lang('purchase.sub_total_amount'): </th>
              <td></td>
              <td><span  class="display_currency pull-right" data-currency_symbol="true">{{number_format($total_befor_taxx,2)}}</span></td>
            </tr>
            <tr>
              <th>@lang('purchase.discount'):</th>
              <td>
                <b>(-)</b>
                @if($purchase->discount_type == 'percentage')
                  (@format_currency($purchase->discount_amount) %)
                @endif
              </td>
              <td>
                @php
                $discount_i = $purchase->discount_amount;
                @endphp
                <span @if(($purchase->discount_type != $new_purchase->discount_type) || ($purchase->discount_amount != $new_purchase->discount_amount)) class="change-bill display_currency pull-right" @else class="display_currency pull-right" @endif    data-currency_symbol="true">
                  @if($purchase->discount_type == 'percentage')
                  @format_currency(($purchase->discount_amount * $total_before_tax / 100))
                  @elseif($purchase->discount_type == 'fixed_after_vat')
                    @php
                      $tax_ = \App\TaxRate::find($purchase->tax_id);
                      if($tax_ != null){
                        $tax = $tax_->amount;
                      }else{
                        $tax = 0;  
                      }
                      $discount_i = $purchase->discount_amount - ($purchase->discount_amount*$tax/(100+$tax));
                    @endphp
                    @format_currency($discount_i)
                  @else 
                  @format_currency($purchase->discount_amount)
                  @endif                  
                </span>
              </td>
            </tr>
            <tr>
              <th>@lang('purchase.purchase_tax'):</th>
              <td><b>(+)</b></td>
              <td class="text-right">
                  @if(!empty($purchase_taxes))
                    @foreach($purchase_taxes as $k => $v)
                      <strong > <small>{{$k}}</small></strong>  &nbsp;&nbsp; <span @if($new_purchase_taxes[$k] != $purchase_taxes[$k]) class = "  "  @endif class="display_currency pull-right" data-currency_symbol="true">@format_currency( $v )</span><br>
                    @endforeach
                  @else
                  0.00
                  @endif
                </td>
            </tr>
            <tr>
              <th>@lang('purchase.purchase_total_'): </th>
              <td></td>
              @if(!empty($purchase_taxes))
                @foreach($purchase_taxes as $k => $v)
                  <td><span class="display_currency pull-right" data-currency_symbol="true">@format_currency($total_befor_taxx - $discount_i + $v)</span></td>
                @endforeach
              @else
                  <td><span class="display_currency pull-right" data-currency_symbol="true">@format_currency($total_befor_taxx - $discount_i)</span></td>
              @endif
            </tr>
            @if( !empty( $purchase->shipping_charges ) )
              {{-- <tr>
                <th>@lang('purchase.additional_shipping_charges'):</th>
                <td><b>(+)</b></td>
                <td><span class="display_currency pull-right" >{{ number_format($purchase->shipping_charges,2) }}</span></td>
              </tr> --}}
            @endif
            
            <!--<tr>-->
            <!--  <th>@lang('purchase.additional_shipping_charges'):</th>-->
            <!--  <td><b>(+)</b></td>-->
            <!--  <td><span class="display_currency pull-right" >{{ number_format($purchase->shipping_charges,2) }}</span></td>-->
            <!--</tr>-->
            
          </table>
        </div>
      </div>
      <div class="row">
        <div class="col-sm-6">
          <div class="col-md-12 col-sm-12 col-xs-12">
          <strong>@lang('purchase.shipping_details'):</strong><br>
          <p class="well well-sm no-shadow bg-gray">
            @if($purchase->shipping_details)
              {!! $purchase->shipping_details !!}
            @else
              --
            @endif
          </p>
          </div>
        </div>
        
        <div class="col-md-6">
          <div class="col-md-12 col-sm-12 col-xs-12">
          <h4>{{ __('home.Additional Expenses') }}:</h4>
            <div class="table-responsive">
              <table class="table table-bordered table-responsive " id="additional_expense">
                <thead>
                  <tr>
                    <th>@lang("home.Supplier")</th>
                    <th>@lang("home.Amount")</th>
                    <th>@lang("home.Vat")</th>
                    <th>@lang("home.Total")</th>
                    <th>@lang("home.Debit")</th>
                    <th>@lang("home.Cost Center")</th>         
                    <th>@lang("home.Note")</th>
                    <th>@lang("home.Date")</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                    $total_shiping_s = 0;
                    $total_shiping_vat = 0;
                    //  $total_shiping_amount = 0;
                    $contacts =  \App\Contact::suppliers();
                    $expenses  =  \App\Account::main('Expenses');
                  ?>
                  @php   $total_contact = 0; $total_expense = 0; @endphp
                  @php  
                    $keys            = [];
                    $new_contact     = [];
                    $new_amount      = [];
                    $new_vat         = [];
                    $new_total       = [];
                    $new_account     = [];
                    $new_cost_center = [];
                    $new_text        = [];
                    $new_date        = [];
                  @endphp
                
                @foreach ($additional->items as $key => $item)
                  @php
                    $array_keys[]               = $item->id;
                    $new_contact[$item->id]     = $item->contact->id;
                    $new_amount[$item->id]      = $item->amount;
                    $new_vat[$item->id]         = $item->vat;
                    $new_total[$item->id]       = $item->total;
                    $new_account[$item->id]     = $item->account->id;
                    $new_cost_center[$item->id] = $item->cost_center->id;
                    $new_text[$item->id]        = $item->text;
                    $new_date[$item->id]        = $item->date;
                  @endphp 
                @endforeach
                  
                  @foreach ($shipp->items as $item)
                      @php
                      if($purchase->contact->id == $item->contact->id){ $total_contact +=  $item->amount + $item->vat; }else{ $total_expense +=  $item->amount + $item->vat;}
                      @endphp
                        <tr>
                          <input type="hidden" name="additional_shipping_item_id[]" value="{{ $item->id }}" >
                          <td @if(in_array($item->line_id,$array_keys))  @if($new_contact[$item->line_id] != $item->contact->id) class=" col-xs-2" @else  class="col-xs-2" @endif @else  class="col-xs-2" @endif>{{ $item->contact->name }}</td>
                          <td @if(in_array($item->line_id,$array_keys))  @if($new_amount[$item->line_id]  != $item->amount) class=" col-xs-2" @else  class="col-xs-2" @endif @else  class="col-xs-2" @endif>{{ $item->amount }}</td>
                          <td @if(in_array($item->line_id,$array_keys))  @if($new_vat[$item->line_id]     != $item->vat) class=" col-xs-2" @else  class="col-xs-2" @endif @else  class="col-xs-2" @endif>{{ $item->vat }}</td>
                          <td @if(in_array($item->line_id,$array_keys))  @if($new_total[$item->line_id]   != $item->total) class=" col-xs-2" @else  class="col-xs-2" @endif @else  class="col-xs-2" @endif>{{ $item->total }}</td>
                          <td @if(in_array($item->line_id,$array_keys))  @if($new_account[$item->line_id] != $item->account->id) class=" col-xs-2" @else  class="col-xs-2" @endif @else  class="col-xs-2" @endif>{{ $item->account->name}}</td>
                          @if($item->cost_center != null)
                          <td @if(in_array($item->line_id,$array_keys))  @if($new_cost_center[$item->line_id] != $item->cost_center->id) class=" col-xs-2" @else  class="col-xs-2" @endif @else  class="col-xs-2" @endif>{{ $item->cost_center->name }}</td>
                          @else
                          <td class=" col-xs-2" >{{  "----" }}</td>
                          @endif
                          <td @if(in_array($item->line_id,$array_keys))  @if($new_text[$item->line_id] != $item->text) class="change-bill col-xs-2" @else  class="col-xs-2" @endif @else  class="col-xs-2" @endif>{{ $item->text }}</td>
                          <td @if(in_array($item->line_id,$array_keys))  @if($new_date[$item->line_id] != \Carbon::createFromFormat('Y-m-d h:s:i', $item->date)->toDateString()) class="change-bill col-xs-2" @else  class="col-xs-2" @endif @else  class="col-xs-2" @endif>{{ date_format(\Carbon::createFromFormat('Y-m-d h:s:i', $item->date),"Y-m-d") }}</td>
                        </tr>
                        <?php
                        $total_shiping_s += $item->total;
                        $total_shiping_vat += $item->vat;
                        $total_shiping_amount += $item->amount;
                    ?>
                  @endforeach
              
                  
                  <tr id="addRow">
                    <td class="col-xs-2"> @lang('home.Total Amount') : <span id="shipping_total_amount">{{ $total_shiping_amount  }} </span> </td>
                    <td class="col-xs-2"> @lang('home.Total Vat') : <span id="shipping_total_vat_s">{{ $total_shiping_vat }}</span>   </td>
                    <td class="col-xs-2"> @lang('home.Total') : <span id="shipping_total_s">{{ $total_shiping_s }}</span>  </td>
                    <td class="col-xs-2"> </td>
                    <td class="col-xs-2"> </td>
                    <td class="col-xs-2"> </td>
                    </tr>
                </tbody>                 
              </table>
            </div>
          </div>
        </div>
    
        <div class="col-sm-6">
          <div class="col-md-12 col-sm-12 col-xs-12">
          <strong>@lang('purchase.additional_notes'):</strong><br>
          <p class="well well-sm no-shadow bg-gray">
            @if($purchase->additional_notes)
              {!! $purchase->additional_notes !!}
            @else
              --
            @endif
          </p>
          </div>
        </div>
    
        
      <div class="col-sm-6">
        <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="table-responsive">
          <table class="table">
            
            <tr>
              <th>@lang('purchase.purchase_total'):</th>
              <td></td>
              @if(!empty($purchase_taxes))
              @foreach($purchase_taxes as $k => $v)
                  <td><span class="display_currency pull-right" data-currency_symbol="true" >@format_currency(($total_befor_taxx - $discount_i + $v + $total_contact))</span></td>
              @endforeach
              @else
                <td><span class="display_currency pull-right" data-currency_symbol="true" >@format_currency(($total_befor_taxx - $discount_i  + $total_contact))</span></td>
              @endif
            </tr>
            <tr>
              <th>@lang('purchase.purchase_pay'):</th>
              <td> , </td>
              @if(!empty($purchase_taxes))
                @foreach($purchase_taxes as $k => $v)
                <td><span class="display_currency pull-right" data-currency_symbol="true" >@format_currency($total_befor_taxx - $discount_i + $v + $total_contact + $total_expense)</span></td>
                @endforeach
              @else
              <td><span class="display_currency pull-right" data-currency_symbol="true" >@format_currency($total_befor_taxx - $discount_i +  $total_contact + $total_expense)</span></td>
              @endif
            </tr>
          </table>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-12">
      <div class="col-md-12 col-sm-12 col-xs-12">
          <strong>{{ __('lang_v1.activities') }}:</strong><br>
          @includeIf('activity_log.activities', ['activity_type' => 'purchase'])
      </div>
    </div>
  </div>
  {{-- Barcode --}}
  <div class="row print_section">
    <div class="col-xs-12">
      <img class="center-block" src="data:image/png;base64,{{DNS1D::getBarcodePNG($purchase->ref_no, 'C128', 2,30,array(39, 48, 54), true)}}">
    </div>
  </div>
</section>
{{-- ............................................................................................................................................................................. --}}
{{-- ............................................................................................................................................................................. --}}
{{-- ............................................................................................................................................................................. --}}
<h1 style="padding:10px">@lang("home.old bill")</h1>
<hr class="hr_class">
<section class="content">

  <div class="modal-header">
      <h4 class="modal-title" id="modalTitle"> @lang('purchase.purchase_details') (<b>@lang('purchase.ref_no'):</b> #{{ $purchase->ref_no }})
      </h4>
  </div>

  <div class="modal-body">

    <div class="row">
      <div class="col-sm-12">
        <p class="pull-right"><b>@lang('messages.date'):</b> {{ @format_date($new_purchase->transaction_date) }}</p>
      </div>
    </div>
    
    <div class="row invoice-info">

      <div class="col-sm-4 invoice-col">
        @lang('purchase.supplier'):
        <address>
          <a type="button" class="btn btn-link"
            href="{{URL::to('contacts/'.$new_purchase->contact->id)}}" ><strong>{{ $new_purchase->contact->supplier_business_name .  " " .   $new_purchase->contact->name  }}</strong></a>
  
          @if(!empty($new_purchase->contact->address_line_1))
            <br>{{$new_purchase->contact->address_line_1}}
          @endif
          @if(!empty($new_purchase->contact->address_line_2))
            <br>{{$new_purchase->contact->address_line_2}}
          @endif
          @if(!empty($new_purchase->contact->city) || !empty($new_purchase->contact->state) || !empty($new_purchase->contact->country))
            <br>{{implode(',', array_filter([$new_purchase->contact->city, $new_purchase->contact->state, $new_purchase->contact->country, $new_purchase->contact->zip_code]))}}
          @endif
          @if(!empty($new_purchase->contact->tax_number))
            <br>@lang('contact.tax_no'): {{$new_purchase->contact->tax_number}}
          @endif
          @if(!empty($new_purchase->contact->mobile))
            <br>@lang('contact.mobile'): {{$new_purchase->contact->mobile}}
          @endif
          @if(!empty($new_purchase->contact->email))
            <br>@lang('business.email'): {{$new_purchase->contact->email}}
          @endif
        </address>
        @if($new_purchase->document_path)
          
          <a href="{{$new_purchase->document_path}}" 
          download="{{$new_purchase->document_name}}" class="btn btn-sm btn-success pull-left no-print">
            <i class="fa fa-download"></i> 
              &nbsp;{{ __('purchase.download_document') }}
          </a>
        @endif
        @php
          
          
        @endphp
        &nbsp;&nbsp;
        {{-- <button data-href="/entry/transaction/{{$purchase->id}}" data-container=".view_modal" class="btn btn-modal bg-blue">@lang("home.Entry")</button> --}}
        {{-- @if(!auth()->user()->can('warehouse.views') && !auth()->user()->can('admin_supervisor.views') && !auth()->user()->can('admin_without.views') && !auth()->user()->can('manufuctoring.views')) --}}
          {{-- <a href="/purchases/{{$purchase->id}}/edit"   class="btn bg-yellow" >@lang("messages.edit")</a> --}}
        {{-- @endif --}}
      </div>

      <div class="col-sm-4 invoice-col">
        @lang('business.business'):
        <address>
          <strong>{{ $new_purchase->business->name }}</strong>
          {{ $new_purchase->location->name }}
          @if(!empty($new_purchase->location->landmark))
            <br>{{$new_purchase->location->landmark}}
          @endif
          @if(!empty($new_purchase->location->city) || !empty($new_purchase->location->state) || !empty($new_purchase->location->country))
            <br>{{implode(',', array_filter([$new_purchase->location->city, $new_purchase->location->state, $new_purchase->location->country]))}}
          @endif
          
          @if(!empty($new_purchase->business->tax_number_1))
            <br>{{$new_purchase->business->tax_label_1}}: {{$new_purchase->business->tax_number_1}}
          @endif

          @if(!empty($new_purchase->business->tax_number_2))
            <br>{{$new_purchase->business->tax_label_2}}: {{$new_purchase->business->tax_number_2}}
          @endif

          @if(!empty($new_purchase->location->mobile))
            <br>@lang('contact.mobile'): {{$new_purchase->location->mobile}}
          @endif
          @if(!empty($new_purchase->location->email))
            <br>@lang('business.email'): {{$new_purchase->location->email}}
          @endif
        </address>
      </div>

      <div class="col-sm-4 invoice-col">
        <b>@lang('purchase.ref_no'):</b> #{{ $new_purchase->ref_no }}<br/>
        <b>@lang('messages.date'):</b> {{ @format_date($new_purchase->transaction_date) }}<br/>
        <b>@lang('purchase.purchase_status'):</b> {{ __('lang_v1.' . $new_purchase->status) }}<br>
        <b>@lang('purchase.payment_status'):</b> {{ __('lang_v1.' . $new_purchase->payment_status) }}<br>
        <b>@lang('warehouse.nameW'):</b> {{ $new_purchase->warehouse->name }}<br>
        @if($new_purchase->cost_center != null)
        <b>@lang('home.Cost Center'):</b> {{ $new_purchase->cost_center->name }}<br>
        @endif
        @if($new_purchase->sup_refe)
          <div>
            <strong> @lang("purchase.sup_refe") : </strong> 
            {{$new_purchase->sup_refe}}
          </div>
        @endif
      </div>
    </div>

    <br>
    <div class="row">
      <div class="col-sm-12 col-xs-12">
        <div class="table-responsive">
          <table class="table bg-gray">
            <thead>
              <tr class="bg-green">
                <th># </th>
                <th>@lang('product.product_name')</th>
                <th class="text-right">@lang('purchase.purchase_quantity')</th>
                <th class="text-right">@lang( 'lang_v1.unit_cost_before_discount' )</th>
                <th>@lang('home.Price Including Tax')</th>
                <th class="text-right">@lang( 'home.Discount' )</th>
                <th class="text-right">@lang('purchase.unit_cost_before_tax_')</th>
                <th class="no-print text-right">@lang('purchase.unit_cost_before_tax')</th>
                <th class="no-print text-right">@lang('purchase.subtotal_before_tax')</th>
                <th class="text-right">@lang('sale.tax')</th>
                {{-- <th class="text-right">@lang('purchase.unit_selling_price')</th> --}}
                @if(session('business.enable_lot_number'))
                  <th>@lang('lang_v1.lot_number')</th>
                @endif
                @if(session('business.enable_product_expiry'))
                  <th>@lang('product.mfg_date')</th>
                  <th>@lang('product.exp_date')</th>
                @endif
                <th class="text-right">@lang('sale.subtotal')</th>
              </tr>
            </thead>
            
            @php 
          
              $total_before_tax = 0.00;
              $total_befor_taxx  = 0; 
             
            @endphp
            @foreach($new_purchase->purchase_lines as $purchase_line)
              <?php
              $tax_price = $purchase_line->purchase_price;
              
                  $tx = 0 ;
                  foreach ($array as $key => $value) {
                      if($key == $purchase_line->tax_id ){
                        $tx = $value;
                      }
                  }
      
              if (isset($purchase_line->transaction->tax->amount)) {
                  $tax_price =  
                  ($tx/100)* $purchase_line->purchase_price  
                  +  $purchase_line->purchase_price  ;
              
              }
              $total_befor_taxx += $purchase_line->quantity * ($purchase_line->pp_without_discount - ($purchase_line->discount_percent*100/$purchase_line->pp_without_discount));
              ?> 
              <tr>
                <td>{{ $loop->iteration }} 
                </td>
                <td>
                  <a href="/item-move/{{$purchase_line->product->id}}"  target="_blank">
                  {{ $purchase_line->product->name }}
                  </a>
                
                  
                  @if( $purchase_line->product->type == 'variable')
                  - {{ $purchase_line->variations->product_variation->name}}
                  - {{ $purchase_line->variations->name}}
                  @endif
                  {{-- <div class="btn-group"><button type="button" class="btn btn-info dropdown-toggle btn-xs no-print" data-toggle="dropdown" aria-expanded="false">@lang("messages.actions")<span class="caret"></span><span class="sr-only">Toggle Dropdown</span></button><ul class="dropdown-menu dropdown-menu-left" role="menu">
                        <li><a data-href="{{action('ProductController@view', [ $purchase_line->product->id])}}" class="btn-modal" data-container=".products_modal"><i class="fa fa-eye"></i>@lang("messages.view")</a></li>
                        <li><a href="{{action('ProductController@edit', [ $purchase_line->product->id])}}"><i class="glyphicon glyphicon-edit"></i>@lang("messages.edit")</a></li>
                        <li><a href="{{action('ItemMoveController@index', [$purchase_line->product->id])}}"><i class="fas fa-history"></i>@lang("lang_v1.product_stock_history")</a></li>
                    </ul>
                      <button type="button" style="margin-left:10px" class="btn btn-primary btn-xs btn-modal no-print" id="view_s" data-container=".products_modal" data-href="{{action('ProductController@viewStock', [$purchase_line->product->id])}}">@lang('lang_v1.view_Stock')</button> 
                      <button type="button" style="margin-left:10px" class="btn btn-second btn-xs btn-modal no-print" data-container=".stocks_modal" data-href="{{action('ProductController@viewUnrecieved', [$purchase_line->product->id])}}">@lang('recieved.should_recieved')</button> 
                </div> --}}
                </td>
              
                <td><span class="display_currency" data-is_quantity="true" data-currency_symbol="false">{{ $purchase_line->quantity }}</span> @if(!empty($purchase_line->sub_unit)) {{$purchase_line->sub_unit->short_name}} @else {{$purchase_line->product->unit->short_name}} @endif</td>
                <td class="text-right"><span class="display_currency" data-currency_symbol="true">
                  @php $prices = rtrim($purchase_line->pp_without_discount, '0'); @endphp
                  {{rtrim($prices, '.')}}
                  </span></td>
                <td class="text-right"><span class="display_currency" data-currency_symbol="true">
                  {{($purchase_line->pp_without_discount*$tx/100) + $purchase_line->pp_without_discount}} </span></td>
                <td class="text-right"><span class="display_currency">
                {{$purchase_line->discount_percent}}</span> 
                  {{  ($purchase_line->transaction->inline_discount_type == 1)?trans('home.AED'):' ' }}  </td>
                <td class="text-right"><span class="display_currency" data-currency_symbol="true">
                
                  @php $price_after = rtrim($purchase_line->purchase_price, '0'); @endphp
                  {{rtrim($price_after, '.')}}
                </span></td>
                <td class="no-print text-right"><span class="display_currency" data-currency_symbol="true">{{($purchase_line->purchase_price + $purchase_line->purchase_price*$tx/100)}}</span></td>
                <td class="no-print text-right"><span class="display_currency" data-currency_symbol="true">
                  
                  @php  $pr = number_format(($purchase_line->quantity * ($purchase_line->pp_without_discount - ($purchase_line->discount_percent*100/$purchase_line->pp_without_discount))),4); $subtotal = rtrim($pr , '0');  @endphp
                  {{rtrim($subtotal, '.')}}
                </span></td>
                <td class="text-right"><span class="display_currency" data-currency_symbol="true">
                  {{ isset($purchase_line->transaction->tax)?$purchase_line->transaction->tax->amount.'%':0 }} </span> <br/><small>@if(!empty($taxes[$purchase_line->tax_id])) ( {{ $taxes[$purchase_line->tax_id]}} ) </small>@endif</td>
            
                @php
                  $sp = $purchase_line->variations->default_sell_price;
                  if(!empty($purchase_line->sub_unit->base_unit_multiplier)) {
                    $sp = $sp * $purchase_line->sub_unit->base_unit_multiplier;
                  }
                @endphp
                {{-- <td class="text-right"><span class="display_currency" data-currency_symbol="true">{{$sp}} </span></td> --}}

                @if(session('business.enable_lot_number'))
                  <td>{{$purchase_line->lot_number}}</td>
                @endif

                @if(session('business.enable_product_expiry'))
                <td>
                  @if( !empty($purchase_line->product->expiry_period_type) )
                    @if(!empty($purchase_line->mfg_date))
                      {{ @format_date($purchase_line->mfg_date) }}
                    @endif
                  @else
                    @lang('product.not_applicable')
                  @endif
                </td>
                <td>
                  @if( !empty($purchase_line->product->expiry_period_type) )
                    @if(!empty($purchase_line->exp_date))
                      {{ @format_date($purchase_line->exp_date) }}
                    @endif
                  @else
                    @lang('product.not_applicable')
                  @endif
                
                </td>
                @endif
                <td class="text-right"><span class="display_currency" data-currency_symbol="true">
                {{ number_format(($tax_price * $purchase_line->quantity),2) }}   
                </span></td>
              </tr>
              @php 
                $total_before_tax += $purchase_line->quantity * $purchase_line->purchase_price  ;
              @endphp
            @endforeach
          </table>
        </div>
      </div>
    </div>
    <br>
    <div class="row">
      <div class="col-sm-12 col-xs-12">
        
        <h4>{{ __('lang_v1.recieved_balance') }}:</h4>
            <div class="table-responsive">
              <table class="table table-recieve bg-gray" >
                <thead>
                  <tr style=" ">
                    <th>@lang('purchase.ref_no')</th>
                    <th>@lang('messages.date')</th>
                    <th>@lang('product.product_name')</th>
                    <th>@lang('product.unit')</th>
                    <th>@lang('purchase.amount_current')</th>
                    <th>@lang('warehouse.nameW')</th>
                    <th>@lang('purchase.payment_note')</th>
                    <th>@lang('home.Product Cost')</th>
                  </tr>
                </thead>
              @php
                $empty = 0;
                $total = 0;
                $total_wrong = 0;
                $pre = "";
                $type = "base";
              @endphp
              @forelse ($RecievedPrevious as $Recieved)
                @php
                  $date = Carbon::now();
                  $date_now = $Recieved->created_at;
                  $product_name = "";
                  $counter = 1; 
                  $counter_all = 1; 
                  foreach($product_list as $key => $product_l){
                    if($Recieved->product_name == $product_l ){
                      $product_name = $product_l;
                      $product_n_id = $key;
                    }
                    $counter = $counter + 1 ;
                  }
                  if($product_name == ""){
                    foreach($product_list_all as $key1 => $product_l_all){
                      if($Recieved->product_name == $product_l_all ){
                        $product_name = $product_l_all;
                        $product_n_id = $key1;
                        $type = "other";
                      }
                      $counter_all = $counter_all + 1 ;
                    }
                    $total = $total ;
                    $total_wrong = $total_wrong + $Recieved->current_qty ;
                  }else{
                    $type = "base"; 
                    $total = $total + $Recieved->current_qty;
                  }
                  
                  $Warehouse_name = "";
                  $counter_1 = 1; 
                  foreach($Warehouse_list as $key => $Warehouse_l){
                    if($Recieved->store_id == $key ){
                      $Warehouse_name = $Warehouse_l;
                  }
                  $counter_1 = $counter_1 + 1 ;
                  }
                  foreach($unit as $un){
                    if($Recieved->unit_id == $un->id ){
                      $unt = $un->actual_name;
                    }
                  }
      
                @endphp 
                @if ($pre != $date_now)
                  @if ($pre == "")
                  @else
                    <tr style="background:#f1f1f1; width:100% !important" >
                      <td>{{""}}</td>	
                      <td>{{""}}</td>	
                      <td>{{""}}</td>	
                      <td>{{""}}</td>	
                      <td>{{""}}</td>	
                      <td>{{""}}</td>	
                    <tr>
                  @endif
                  @php
                    $pre = $date_now;
                  @endphp
                @endif
                <tr @if($type != "other") style="background:#f1f1f1; width:100% !important" @else style="background:#ff5b5b; width:100% !important" @endif>
                  <td>{{$Recieved->TrRecieved->reciept_no}}</td>
                  <td>{{$Recieved->created_at}}</td>
                  <td>
                    <a href="/item-move/{{$Recieved->product->id}}"  target="_blank">
                      {{$product_name}}
                    </a>
                    <br>
                    {{-- <div class="btn-group no-print"><button type="button" class="btn btn-info dropdown-toggle btn-xs no-print no-print" data-toggle="dropdown" aria-expanded="false">@lang("messages.actions")<span class="caret"></span><span class="sr-only">Toggle Dropdown</span></button><ul class="dropdown-menu dropdown-menu-left" role="menu">
                        <li><a data-href="{{action('ProductController@view', [ $Recieved->product_id])}}" class="btn-modal no-print" data-container=".products_modal"><i class="fa fa-eye"></i>@lang("messages.view")</a></li>
                        <li><a href="{{action('ProductController@edit', [ $Recieved->product_id])}}"><i class="glyphicon glyphicon-edit no-print"></i>@lang("messages.edit")</a></li>
                        <li><a href="{{action('ItemMoveController@index', [$Recieved->product_id])}}"><i class="fas fa-history "></i>@lang("lang_v1.product_stock_history")</a></li>
                      </ul>
                        <button type="button" style="margin-left:10px" class="btn btn-primary btn-xs btn-modal no-print" id="view_s" data-container=".products_modal" data-href="{{action('ProductController@viewStock', [$Recieved->product_id])}}">@lang('lang_v1.view_Stock')</button> 
                        <button type="button" style="margin-left:10px" class="btn bg-yellow btn-xs btn-modal no-print" data-container=".stocks_modal" data-href="{{action('ProductController@viewUnrecieved', [$Recieved->product_id])}}">@lang('recieved.should_recieved')</button> 
                    </div> --}}
                  
                  </td>
                  <td>{{$unt}}</td>
                  <td>{{$Recieved->current_qty}}</td>
                  <td>{{$Warehouse_name}}</td>
                  <td>{{$Recieved->note}}</td>
                  <td>@format_currency(\App\Product::product_cost_expense($Recieved->product_id,$Recieved->transaction_id,$Recieved->transaction_deliveries_id)) </td>
                </tr>
                
              @empty  
                    @forelse ($RecievedWrong as $RecWrong)
                                    
                        @php
                            $empty =1;
                            $date = Carbon::now(); 
                            if(!in_array($RecWrong->product->id,$product_list_all)){
                                $type = "other";
                            }else{
                                $type = "base";
                            }
                            $total_wrong += $RecWrong->current_qty;
                        @endphp

                        @if ($pre != $RecWrong->created_at)
                            @if ($pre == "")  @else
                            <tr  style="border:1px solid #f1f1f1;" >
                                <td>{{""}}</td>	
                                <td>{{""}}</td>	
                                <td>{{""}}</td>	
                                <td>{{""}}</td>	
                                <td>{{""}}</td>	
                            <tr>
                            @endif
                            @php $style = ""; $pre = $RecWrong->created_at; @endphp
                        @endif
                        <tr  @if($type != "other") style="background:#f1f1f1; width:100% !important" @else style="background:#ff5b5b; width:100% !important" @endif>
                            <td>
                              <td>{{$RecWrong->TrRecieved->reciept_no}}</td>
                              <td>{{$RecWrong->created_at}}</td>
                              <a href="/item-move/{{$RecWrong->product->id}}"  target="_blank">
                                {{$RecWrong->product->name}}
                              </a>
                                <br>
                                <div class="btn-group no-print"><button type="button" class="btn btn-info dropdown-toggle btn-xs no-print no-print" data-toggle="dropdown" aria-expanded="false">@lang("messages.actions")<span class="caret"></span><span class="sr-only">Toggle Dropdown</span></button><ul class="dropdown-menu dropdown-menu-left" role="menu">
                                    <li><a data-href="{{action('ProductController@view', [ $RecWrong->product_id])}}" class="btn-modal no-print" data-container=".products_modal"><i class="fa fa-eye"></i>@lang("messages.view")</a></li>
                                    <li><a href="{{action('ProductController@edit', [ $RecWrong->product_id])}}"><i class="glyphicon glyphicon-edit no-print"></i>@lang("messages.edit")</a></li>
                                    <li><a href="{{action('ItemMoveController@index', [$RecWrong->product_id])}}"><i class="fas fa-history "></i>@lang("lang_v1.product_stock_history")</a></li>
                                  </ul>
                                    <button type="button" style="margin-left:10px" class="btn btn-primary btn-xs btn-modal no-print" id="view_s" data-container=".products_modal" data-href="{{action('ProductController@viewStock', [$RecWrong->product_id])}}">@lang('lang_v1.view_Stock')</button> 
                                    <button type="button" style="margin-left:10px" class="btn bg-yellow btn-xs btn-modal no-print" data-container=".stocks_modal" data-href="{{action('ProductController@viewUnrecieved', [$RecWrong->product_id])}}">@lang('recieved.should_recieved')</button> 
                                </div>
                              </td>
                            <td>{{$RecWrong->product->unit->actual_name}}</td>
                            <td>{{$RecWrong->current_qty}}</td>
                            <td>{{$RecWrong->store->name}}</td>
                            <td></td>
                            <td></td>
                        </tr>
                    @empty @endforelse
              @endforelse 
              @if($empty == 0)
                  @forelse ($RecievedWrong as $RecWrong)
                      @php
                          $date = Carbon::now(); 
                          if(!in_array($RecWrong->product->id,$product_list_all)){
                              $type = "other";
                          }else{
                              $type = "base";
                          }
                          $total_wrong += $RecWrong->current_qty;
                      @endphp

                      @if ($pre != $RecWrong->created_at)
                          @if ($pre == "")  @else
                          <tr  style="border:1px solid #f1f1f1;" >
                              <td>{{""}}</td>	
                              <td>{{""}}</td>	
                              <td>{{""}}</td>	
                              <td>{{""}}</td>	
                              <td>{{""}}</td>	
                          <tr>
                          @endif
                          @php $style = ""; $pre = $RecWrong->created_at; @endphp
                      @endif
                      <tr  @if($type != "other") style="background:#f1f1f1; width:100% !important" @else style="background:#ff5b5b; width:100% !important" @endif>
                        <td>{{$RecWrong->TrRecieved->reciept_no}}</td>
                        <td>{{$RecWrong->created_at}}</td>  
                        <td>
                          <a href="/item-move/{{$RecWrong->product->id}}"  target="_blank">
                            {{$RecWrong->product->name}}
                          </a>
                              <br>
                              <div class="btn-group no-print"><button type="button" class="btn btn-info dropdown-toggle btn-xs no-print no-print" data-toggle="dropdown" aria-expanded="false">@lang("messages.actions")<span class="caret"></span><span class="sr-only">Toggle Dropdown</span></button><ul class="dropdown-menu dropdown-menu-left" role="menu">
                                  <li><a data-href="{{action('ProductController@view', [ $RecWrong->product_id])}}" class="btn-modal no-print" data-container=".products_modal"><i class="fa fa-eye"></i>@lang("messages.view")</a></li>
                                  <li><a href="{{action('ProductController@edit', [ $RecWrong->product_id])}}"><i class="glyphicon glyphicon-edit no-print"></i>@lang("messages.edit")</a></li>
                                  <li><a href="{{action('ItemMoveController@index', [$RecWrong->product_id])}}"><i class="fas fa-history "></i>@lang("lang_v1.product_stock_history")</a></li>
                                </ul>
                                  <button type="button" style="margin-left:10px" class="btn btn-primary btn-xs btn-modal no-print" id="view_s" data-container=".products_modal" data-href="{{action('ProductController@viewStock', [$RecWrong->product_id])}}">@lang('lang_v1.view_Stock')</button> 
                                  <button type="button" style="margin-left:10px" class="btn bg-yellow btn-xs btn-modal no-print" data-container=".stocks_modal" data-href="{{action('ProductController@viewUnrecieved', [$RecWrong->product_id])}}">@lang('recieved.should_recieved')</button> 
                              </div>
                          </td>
                          <td>{{$RecWrong->product->unit->actual_name}}</td>
                          <td>{{$RecWrong->current_qty}}</td>
                          <td>{{$RecWrong->store->name}}</td>
                          
                          <td></td>
                          <td></td>
                      </tr>
                  @empty @endforelse
                @endif
              <tfoot>
                <tr class="bg-gray  font-17 footer-total" style="border:1px solid #f1f1f1;  ">
                <td class="text-center " colspan="2"><strong>@lang('sale.total'):</strong></td>
                <td>{{$total}}</td>
                <td></td>
                <td>Wrong receipt : {{$total_wrong}}</td>
                <td></td>
        
              
                </tr>
              </tfoot>
              </table>
          </div>
        </div>
      
  
    </div>

    <br>
    <div class="row">
      
      <div class="col-sm-12 col-xs-12">
        <h4>{{ __('sale.payment_info') }}:</h4>
      </div>
      <div class="col-md-6 col-sm-12 col-xs-12">
        <div class="table-responsive">
          <table class="table">
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
            @forelse($new_payment as $payment_line)
              @php
                $total_paid += $payment_line->amount;
              @endphp
              <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ @format_date($payment_line->paid_on) }}</td>
                <td>{{ $payment_line->payment_ref_no }}</td>
                <td><span class="display_currency" data-currency_symbol="true">@format_currency($payment_line->amount)</span></td>
                <td>{{ $payment_methods[$payment_line->method] ?? '' }}</td>
                <td>@if($payment_line->note) 
                  {{ ucfirst($payment_line->note) }}
                  @else
                  --
                  @endif
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="5" class="text-center">
                  @lang('purchase.no_payments')
                </td>
              </tr>
            @endforelse
          </table>
        </div>
      </div>
    
      <div class="col-md-6 col-sm-12 col-xs-12">
        <div class="table-responsive">
          <table class="table">
            <!-- <tr class="hide">
              <th>@lang('purchase.total_before_tax'): </th>
              <td></td>
              <td><span class="display_currency pull-right">  span></td>
            </tr> -->
            <?php
                  
                  $total_shiping_amount = 0;
            
                ?>
            <tr>
              <th>@lang('purchase.sub_total_amount'): </th>
              <td></td>
              <td><span class="display_currency pull-right" data-currency_symbol="true">{{number_format($total_befor_taxx,2)}}</span></td>
            </tr>
            <tr>
              <th>@lang('purchase.discount'):</th>
              <td>
                <b>(-)</b>
                @if($new_purchase->discount_type == 'percentage')
                  (@format_currency($new_purchase->discount_amount) %)
                @endif
              </td>
              <td>
                @php
                $discount_i = $new_purchase->discount_amount;
                @endphp
                <span  class="display_currency pull-right"    data-currency_symbol="true">
                  @if($purchase->discount_type == 'percentage')
                    @format_currency(($new_purchase->discount_amount * $total_before_tax / 100))
                  @elseif($new_purchase->discount_type == 'fixed_after_vat')
                    @php
                      $tax_ = \App\TaxRate::find($new_purchase->tax_id);
                      if($tax_ != null){
                        $tax = $tax_->amount;
                      }else{
                        $tax = 0;  
                      }
                      $discount_i = $new_purchase->discount_amount - ($new_purchase->discount_amount*$tax/(100+$tax));
                    @endphp
                    @format_currency($discount_i)
                  @else 
                    @format_currency($new_purchase->discount_amount)
                  @endif                  
                </span>
              </td>
            </tr>
            <tr>
              <th>@lang('purchase.purchase_tax'):</th>
              <td><b>(+)</b></td>
              <td class="text-right">
                
                  @if(!empty($new_purchase_taxes))
                    @foreach($new_purchase_taxes as $k => $v)
                  
                      <strong ><small>{{$k}}</small></strong>  &nbsp;&nbsp; <span class="display_currency pull-right" data-currency_symbol="true">@format_currency( $v )</span><br>
                    @endforeach
                  @else
                  0.00
                  @endif
                </td>
            </tr>
            <tr>
              <th>@lang('purchase.purchase_total_'): </th>
              <td></td>
              @if(!empty($new_purchase_taxes))
                @foreach($new_purchase_taxes as $k => $v)
                  <td><span class="display_currency pull-right" data-currency_symbol="true">@format_currency($total_befor_taxx - $discount_i + $v)</span></td>
                @endforeach
              @else
                  <td><span class="display_currency pull-right" data-currency_symbol="true">@format_currency($total_befor_taxx - $discount_i)</span></td>
              @endif
            </tr>
            @if( !empty( $new_purchase->shipping_charges ) )
              {{-- <tr>
                <th>@lang('purchase.additional_shipping_charges'):</th>
                <td><b>(+)</b></td>
                <td><span class="display_currency pull-right" >{{ number_format($purchase->shipping_charges,2) }}</span></td>
              </tr> --}}
            @endif
            
            <!--<tr>-->
            <!--  <th>@lang('purchase.additional_shipping_charges'):</th>-->
            <!--  <td><b>(+)</b></td>-->
            <!--  <td><span class="display_currency pull-right" >{{ number_format($purchase->shipping_charges,2) }}</span></td>-->
            <!--</tr>-->
            
          </table>
        </div>
      </div>
      <div class="row">
        <div class="col-sm-6">
          <div class="col-md-12 col-sm-12 col-xs-12">
          <strong>@lang('purchase.shipping_details'):</strong><br>
          <p @if($purchase->shipping_details != $new_purchase->shipping_details) class="change-bill well well-sm no-shadow bg-gray" @else class="well well-sm no-shadow bg-gray" @endif>
            @if($new_purchase->shipping_details)
              {!! $new_purchase->shipping_details !!}
            @else
              --
            @endif
          </p>
          </div>
        </div>
        
        <div class="col-md-6">
          <div class="col-md-12 col-sm-12 col-xs-12">
          <h4>{{ __('home.Additional Expenses') }}:</h4>
            <div class="table-responsive">
              <table class="table table-bordered table-responsive " id="additional_expense">
                <thead>
                  <tr>
                    <th>@lang("home.Supplier")</th>
                    <th>@lang("home.Amount")</th>
                    <th>@lang("home.Vat")</th>
                    <th>@lang("home.Total")</th>
                    <th>@lang("home.Debit")</th>
                    <th>@lang("home.Cost Center")</th>         
                    <th>@lang("home.Note")</th>
                    <th>@lang("home.Date")</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                    $total_shiping_s = 0;
                    $total_shiping_vat = 0;
                    //  $total_shiping_amount = 0;
                    $contacts =  \App\Contact::suppliers();
                    $expenses  =  \App\Account::main('Expenses');
                  ?>
                  @php   $total_contact = 0; $total_expense = 0; @endphp
                  
                  
                  @foreach ($additional->items as $item)
                      @php
                      if($purchase->contact->id == $item->contact->id){ $total_contact +=  $item->amount + $item->vat; }else{ $total_expense +=  $item->amount + $item->vat;}
                      @endphp
                        <tr>
                          <input type="hidden" name="additional_shipping_item_id[]" value="{{ $item->id }}" >
                          <td class="col-xs-2">{{ $item->contact->name }}</td>
                          <td class="col-xs-2">{{ $item->amount }}</td>
                          <td class="col-xs-2">{{ $item->vat }}</td>
                          <td class="col-xs-2">{{ $item->total }}</td>
                          <td class="col-xs-2">{{ $item->account->name}}</td>
                          @if($item->cost_center != null)
                          <td class="col-xs-2">{{ $item->cost_center->name }}</td>
                          @else
                          <td class="col-xs-2">{{  "----" }}</td>
                          @endif
                          <td class="col-xs-2">{{ $item->text }}</td>
                          <td class="col-xs-2">{{ $item->date }}</td>
                        </tr>
                        <?php
                          $total_shiping_s += $item->total;
                          $total_shiping_vat += $item->vat;
                          $total_shiping_amount += $item->amount;
                        ?>
                  @endforeach
              
                  
                  <tr id="addRow">
                    <td class="col-xs-2"> @lang('home.Total Amount') : <span id="shipping_total_amount">{{ $total_shiping_amount  }} </span> </td>
                    <td class="col-xs-2"> @lang('home.Total Vat') : <span id="shipping_total_vat_s">{{ $total_shiping_vat }}</span>   </td>
                    <td class="col-xs-2"> @lang('home.Total') : <span id="shipping_total_s">{{ $total_shiping_s }}</span>  </td>
                    <td class="col-xs-2"> </td>
                    <td class="col-xs-2"> </td>
                    <td class="col-xs-2"> </td>
                    </tr>
                </tbody>                 
              </table>
            </div>
          </div>
        </div>
    
        <div class="col-sm-6">
          <div class="col-md-12 col-sm-12 col-xs-12">
          <strong>@lang('purchase.additional_notes'):</strong><br>
          <p class="well well-sm no-shadow bg-gray">
            @if($purchase->additional_notes)
              {!! $purchase->additional_notes !!}
            @else
              --
            @endif
          </p>
          </div>
        </div>
    
        
      <div class="col-sm-6">
        <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="table-responsive">
          <table class="table">
            
            <tr>
              <th>@lang('purchase.purchase_total'):</th>
              <td></td>
              @if(!empty($new_purchase_taxes))
                @foreach($new_purchase_taxes as $k => $v)
                    <td><span  class="display_currency pull-right"   data-currency_symbol="true" >@format_currency(($total_befor_taxx - $discount_i + $v + $total_contact))</span></td>
                @endforeach
              @else
                    <td><span   class="display_currency pull-right"   data-currency_symbol="true" >@format_currency(($total_befor_taxx - $discount_i  + $total_contact))</span></td>
              @endif
            </tr>
            <tr>
              <th>@lang('purchase.purchase_pay'):</th>
              <td> , </td>
              @if(!empty($new_purchase_taxes))
                @foreach($new_purchase_taxes as $k => $v)
                <td><span   class="display_currency pull-right"   data-currency_symbol="true" >@format_currency($total_befor_taxx - $discount_i + $v + $total_contact + $total_expense)</span></td>
                @endforeach
              @else
                <td><span   class="display_currency pull-right"   data-currency_symbol="true" >@format_currency($total_befor_taxx - $discount_i +  $total_contact + $total_expense)</span></td>
              @endif
            </tr>
          </table>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-12">
      <div class="col-md-12 col-sm-12 col-xs-12">
          <strong>{{ __('lang_v1.activities') }}:</strong><br>
          @includeIf('activity_log.activities', ['activity_type' => 'purchase'])
      </div>
    </div>
  </div>

  {{-- Barcode --}}
  <div class="row print_section">
    <div class="col-xs-12">
      <img class="center-block" src="data:image/png;base64,{{DNS1D::getBarcodePNG($purchase->ref_no, 'C128', 2,30,array(39, 48, 54), true)}}">
    </div>
  </div>
</section>


@stop