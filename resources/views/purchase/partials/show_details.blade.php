<div class="modal-header">
    <button type="button" class="close no-print" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <h4 class="modal-title" id="modalTitle"> @lang('purchase.purchase_details') (<b>@lang('purchase.ref_no'):</b> #{{ $purchase->ref_no }})
    </h4>
</div>
<div class="modal-body">

  <div class="row">
    <div class="col-sm-12">
      <p class="pull-right"><b>@lang('messages.date'):</b> {{ @format_date($purchase->transaction_date) }}</p>
    </div>
  </div>
  
  <div class="row invoice-info">

    <div class="col-sm-4 invoice-col">
      @lang('purchase.supplier'):
      <address>
        <a type="button" class="btn btn-link"
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
      {{-- @if($purchase->document != "[]" )
          @if($purchase->document_path )
            <a href="{{$purchase->document_path}}" 
               download="{{$purchase->document_name}}" class="btn btn-sm btn-success pull-left no-print">
              <i class="fa fa-download"></i> 
                &nbsp;{{ __('purchase.download_document') }}
            </a>
          @endif
      @endif --}}
      @php
         $currency = \App\Currency::find($purchase->business->currency_id); 
        $currency_symbol =isset($currency)?$currency->symbol:"";
        
      @endphp
      &nbsp;&nbsp;
      @if($purchase->status == "final" || $purchase->status == "received")
      <button data-href="/entry/transaction/{{$purchase->id}}" data-container=".view_modal" class="btn btn-modal bg-blue">@lang("home.Entry")</button>
      @endif
      @if(auth()->user()->hasRole('Admin#' . session('business.id')) ||  auth()->user()->can('product.avarage_cost')) 
        <a href="/purchases/{{$purchase->id}}/edit"   class="btn bg-yellow" >@lang("messages.edit")</a>
      @endif
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
      <b>@lang('purchase.ref_no'):</b> #{{ $purchase->ref_no }}<br/>
      <b>@lang('messages.date'):</b> {{ @format_date($purchase->transaction_date) }}<br/>
      <b>@lang('purchase.purchase_status'):</b> {{ __('lang_v1.' . $purchase->status) }}<br>
      <b>@lang('purchase.payment_status'):</b> {{ __('lang_v1.' . $purchase->payment_status) }}<br>
      <b>@lang('warehouse.nameW'):</b> {{ $store }}<br>
      @if($purchase->cost_center != null)
      <b>@lang('home.Cost Center'):</b> {{ $purchase->cost_center->name }}<br>
      @endif
      @if($purchase->sup_refe)
      <div>
        <strong> @lang("purchase.sup_refe") : </strong> 
        {{$purchase->sup_refe}}
      </div>
    @endif
    @if($purchase->currency_id !== null)
      <div>
        <strong> @lang("purchase.Additional Currency") : </strong> 
        @php $currency = \App\Currency::find($purchase->currency_id); @endphp
        {{$currency->currency . " ( " . $currency->symbol  . " ) " . $currency->code }}
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
              @if($purchase->currency_id !== null)
                <th class="text-right">@lang( 'lang_v1.Cost before without Tax' ) {{ " " . $currency->symbol}}</th>
                <th class="text-right"  >@lang( 'home.Cost before with Tax' ) {{ " " . $currency->symbol}}</th>
              @endif  
              <th class="text-right">@lang( 'home.Discount' )</th>
              <th class="text-right">@lang('purchase.unit_cost_before_tax_')</th>
              <th class="no-print text-right">@lang('purchase.unit_cost_before_tax')</th>
              @if($purchase->currency_id !== null)
                <th class="text-right">@lang( 'home.Cost without Tax currency' )   {{ " " . $currency->symbol}}</th>
                <th class="text-right" >@lang( 'home.Cost After Tax currency' )  {{ " " . $currency->symbol}}</th>
              @endif  
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
            $total_befor_taxx_currency  = 0; 
            $total_befor_taxx  = 0; 
          @endphp
          @foreach($purchase->purchase_lines as $purchase_line)
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
             if($purchase_line->pp_without_discount != 0){
                 
                $total_befor_taxx += $purchase_line->quantity * ($purchase_line->pp_without_discount - ($purchase_line->discount_percent*100/$purchase_line->pp_without_discount));
             }else{
                $total_befor_taxx += $purchase_line->quantity * ($purchase_line->pp_without_discount - 0);
                 
             }
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
                
                <div class="btn-group"><button type="button" class="btn btn-info dropdown-toggle btn-xs no-print" data-toggle="dropdown" aria-expanded="false">@lang("messages.actions")<span class="caret"></span><span class="sr-only">Toggle Dropdown</span></button><ul class="dropdown-menu dropdown-menu-left" role="menu">
                       <li><a data-href="{{action('ProductController@view', [ $purchase_line->product->id])}}" class="btn-modal" data-container=".products_modal"><i class="fa fa-eye"></i>@lang("messages.view")</a></li>
                       <li><a href="{{action('ProductController@edit', [ $purchase_line->product->id])}}"><i class="glyphicon glyphicon-edit"></i>@lang("messages.edit")</a></li>
                       <li><a href="{{action('ItemMoveController@index', [$purchase_line->product->id])}}"><i class="fas fa-history"></i>@lang("lang_v1.product_stock_history")</a></li>
                   </ul>
                     <button type="button" style="margin-left:10px" class="btn btn-primary btn-xs btn-modal no-print" id="view_s" data-container=".products_modal" data-href="{{action('ProductController@viewStock', [$purchase_line->product->id])}}">@lang('lang_v1.view_Stock')</button> 
                     <button type="button" style="margin-left:10px" class="btn btn-second btn-xs btn-modal no-print" data-container=".stocks_modal" data-href="{{action('ProductController@viewUnrecieved', [$purchase_line->product->id])}}">@lang('recieved.should_recieved')</button> 
               </div>
              </td>
             
              <td><span class="display_currency" data-is_quantity="true" data-currency_symbol="false">{{ $purchase_line->quantity }}</span> @if(!empty($purchase_line->sub_unit)) {{$purchase_line->sub_unit->short_name}} @else {{$purchase_line->product->unit->short_name}} @endif</td>
              <td class="text-right">
                @if(auth()->user()->hasRole('Admin#' . session('business.id')) ||  auth()->user()->can('product.avarage_cost'))
                  <span class="display_currency" data-currency_symbol="true">
                    @php $prices = rtrim($purchase_line->pp_without_discount, '0'); @endphp
                    {{rtrim($prices, '.')}}
                  </span>
                @else
                  {{ "--" }}
                @endif
              </td>
              <td class="text-right">
                  @if(auth()->user()->hasRole('Admin#' . session('business.id')) ||  auth()->user()->can('product.avarage_cost'))
                    <span class="display_currency" data-currency_symbol="true">
                    {{($purchase_line->pp_without_discount*$tx/100) + $purchase_line->pp_without_discount}}
                    </span>
                  @else
                    {{ "--" }}
                  @endif
              </td>

              @if($purchase->currency_id !== null)
                  <td class="text-right">
                    @if(auth()->user()->hasRole('Admin#' . session('business.id')) ||  auth()->user()->can('product.avarage_cost'))
                      <span class="display_currency" data-currency_symbol="true">
                      @php $prices = rtrim($purchase_line->pp_without_discount, '0'); @endphp
                      {{rtrim(round($prices/$purchase->exchange_price,3), '.')}}
                      </span>
                    @else
                      {{ "--" }}
                    @endif
                  </td>

                  <td class="text-right">
                    @if(auth()->user()->hasRole('Admin#' . session('business.id')) ||  auth()->user()->can('product.avarage_cost'))
                      <span class="display_currency" data-currency_symbol="true">
                        {{round((($purchase_line->pp_without_discount/($purchase->exchange_price))*$tx/100) + ($purchase_line->pp_without_discount/$purchase->exchange_price),3)}} 
                      </span>
                    @else
                      {{ "--" }}
                    @endif
                  </td>
              @endif

              <td class="text-right">
                @if(auth()->user()->hasRole('Admin#' . session('business.id')) ||  auth()->user()->can('product.avarage_cost'))
                  <span class="display_currency">
                  {{$purchase_line->discount_percent}}</span> 
                  {{  ($purchase_line->transaction->inline_discount_type == 1)?trans('home.AED'):' ' }}  
                @else
                  {{ "--" }}
                @endif
              </td>
              <td class="text-right">
                @if(auth()->user()->hasRole('Admin#' . session('business.id')) ||  auth()->user()->can('product.avarage_cost'))
                  <span class="display_currency" data-currency_symbol="true">   
                    @php $price_after = rtrim($purchase_line->purchase_price, '0'); @endphp
                    {{rtrim($price_after, '.')}}
                  </span>
                @else
                  {{ "--" }}
                @endif
              </td>
              <td class="no-print text-right">
                @if(auth()->user()->hasRole('Admin#' . session('business.id')) ||  auth()->user()->can('product.avarage_cost'))
                  <span class="display_currency" data-currency_symbol="true">{{($purchase_line->purchase_price + $purchase_line->purchase_price*$tx/100)}}
                  </span>
                @else
                  {{ "--" }}
                @endif
              </td>
              @if($purchase->currency_id !== null)
                <td class="text-right">
                  @if(auth()->user()->hasRole('Admin#' . session('business.id')) ||  auth()->user()->can('product.avarage_cost'))
                    <span class="display_currency" data-currency_symbol="true">
                      @if($purchase->exchange_price != 0)
                        @php $price_after = rtrim( $purchase_line->purchase_price, '0'); @endphp
                        {{rtrim(round($price_after/$purchase->exchange_price,3), '.')}}
                      @else
                        @php $price_after = rtrim( $purchase_line->purchase_price, '0'); @endphp
                        {{rtrim(round($price_after,3), '.')}}
                      @endif
                    </span>
                  @else
                    {{ "--" }}
                  @endif
                </td>
                @if($purchase->exchange_price != 0)
                  <td class="no-print text-right">
                    @if(auth()->user()->hasRole('Admin#' . session('business.id')) ||  auth()->user()->can('product.avarage_cost'))
                      <span class="display_currency" data-currency_symbol="true">
                        {{round((($purchase_line->purchase_price/$purchase->exchange_price) + ($purchase_line->purchase_price/$purchase->exchange_price)*$tx/100),3)}}
                      </span>
                    @else
                      {{ "--" }}
                    @endif
                  </td>
                @endif
              @endif
              <td class="no-print text-right">
                @if(auth()->user()->hasRole('Admin#' . session('business.id')) ||  auth()->user()->can('product.avarage_cost'))
                  <span class="display_currency" data-currency_symbol="true">
                    @php
                    if($purchase_line->pp_without_discount != 0){
                        $pr = number_format(($purchase_line->quantity * ($purchase_line->pp_without_discount - ($purchase_line->discount_percent*100/$purchase_line->pp_without_discount))),4); $subtotal = rtrim($pr , '0'); 
                    }else{
                        $pr = number_format(($purchase_line->quantity * ($purchase_line->pp_without_discount - 0)),4); $subtotal = rtrim($pr , '0'); 
                    }
                    @endphp
                    {{rtrim($subtotal, '.')}}
                  </span>
                @else
                  {{ "--" }}
                @endif
              </td>
              <td class="text-right">
                @if(auth()->user()->hasRole('Admin#' . session('business.id')) ||  auth()->user()->can('product.avarage_cost'))
                      <span class="display_currency" data-currency_symbol="true">
                      {{ isset($purchase_line->transaction->tax)?$purchase_line->transaction->tax->amount.'%':0 }} </span> <br/><small>@if(!empty($taxes[$purchase_line->tax_id])) ( {{ $taxes[$purchase_line->tax_id]}} ) </small>@endif
                @else
                  {{ "--" }}
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
              <td class="text-right">
                @if(auth()->user()->hasRole('Admin#' . session('business.id')) ||  auth()->user()->can('product.avarage_cost'))
                  <span class="display_currency" data-currency_symbol="true">
                  {{ number_format(($tax_price * $purchase_line->quantity),2) }}   
                  <br>
                  <hr>
                  @if($purchase->currency_id !== null)
                  {{ number_format((round($tax_price/$purchase->exchange_price,3) * $purchase_line->quantity),3) }} {{ " " . $currency->symbol}} 
                  @endif  
                </span>
                @else
                  {{ "--" }}
                @endif
            </td>
            </tr>
            @php 
              $total_before_tax += $purchase_line->quantity * $purchase_line->purchase_price  ;
              if($purchase->currency_id !== null){
              if($purchase->exchange_price != 0){                
                $total_before_tax_currency = round($total_before_tax /   $purchase->exchange_price ,3); 
                $total_befor_taxx_currency  += $purchase_line->quantity * (round((($purchase_line->purchase_price/$purchase->exchange_price) + ($purchase_line->purchase_price/$purchase->exchange_price)*$tx/100),3)) ;
              }else{
                $total_before_tax_currency = round($total_before_tax ,3); 
                $total_befor_taxx_currency  += $purchase_line->quantity * (round((($purchase_line->purchase_price) + ($purchase_line->purchase_price)*$tx/100),3)) ;
              }}
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
						  $pre   = "";
              $type  = "base";
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
                  <div class="btn-group no-print"><button type="button" class="btn btn-info dropdown-toggle btn-xs no-print no-print" data-toggle="dropdown" aria-expanded="false">@lang("messages.actions")<span class="caret"></span><span class="sr-only">Toggle Dropdown</span></button><ul class="dropdown-menu dropdown-menu-left" role="menu">
                      <li><a data-href="{{action('ProductController@view', [ $Recieved->product_id])}}" class="btn-modal no-print" data-container=".products_modal"><i class="fa fa-eye"></i>@lang("messages.view")</a></li>
                      <li><a href="{{action('ProductController@edit', [ $Recieved->product_id])}}"><i class="glyphicon glyphicon-edit no-print"></i>@lang("messages.edit")</a></li>
                      <li><a href="{{action('ItemMoveController@index', [$Recieved->product_id])}}"><i class="fas fa-history "></i>@lang("lang_v1.product_stock_history")</a></li>
                    </ul>
                      <button type="button" style="margin-left:10px" class="btn btn-primary btn-xs btn-modal no-print" id="view_s" data-container=".products_modal" data-href="{{action('ProductController@viewStock', [$Recieved->product_id])}}">@lang('lang_v1.view_Stock')</button> 
                      <button type="button" style="margin-left:10px" class="btn bg-yellow btn-xs btn-modal no-print" data-container=".stocks_modal" data-href="{{action('ProductController@viewUnrecieved', [$Recieved->product_id])}}">@lang('recieved.should_recieved')</button> 
                  </div>
                
                </td>
							  <td>{{$unt}}</td>
							  <td>{{$Recieved->current_qty}}</td>
							  <td>{{$Warehouse_name}}</td>
							  <td>{{$Recieved->note}}</td>
							  <td>
                  @if(auth()->user()->hasRole('Admin#' . session('business.id')) ||  auth()->user()->can('product.avarage_cost'))
                    {{number_format(\App\Product::product_cost_expense($Recieved->product_id,$Recieved->transaction_id,$Recieved->transaction_deliveries_id),3)}}  {{ " " .$currency_symbol}}  
                  @else
                    {{ "--" }}
                  @endif
                </td>
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
          @forelse($purchase->payment_lines as $payment_line)
            @php
              $total_paid += $payment_line->amount;
            @endphp
            <tr>
              <td>
                @if(auth()->user()->hasRole('Admin#' . session('business.id')) ||  auth()->user()->can('product.avarage_cost'))
                    {{ $loop->iteration }}
                @else
                    {{   "--"   }}
                @endif
              </td>
              <td>
                @if(auth()->user()->hasRole('Admin#' . session('business.id')) ||  auth()->user()->can('product.avarage_cost'))
                {{ @format_date($payment_line->paid_on) }}
                @else
                    {{   "--"   }}
                @endif
              </td>
              <td>
                @if(auth()->user()->hasRole('Admin#' . session('business.id')) ||  auth()->user()->can('product.avarage_cost'))
                {{ $payment_line->payment_ref_no }}
                @else
                    {{   "--"   }}
                @endif
              </td>
              <td>
                @if(auth()->user()->hasRole('Admin#' . session('business.id')) ||  auth()->user()->can('product.avarage_cost'))
                <span class="display_currency" data-currency_symbol="true">@format_currency($payment_line->amount)</span>
                @else
                    {{   "--"   }}
                @endif
              </td>
              <td>
                @if(auth()->user()->hasRole('Admin#' . session('business.id')) ||  auth()->user()->can('product.avarage_cost'))
                {{ $payment_methods[$payment_line->method] ?? '' }}
                @else
                    {{   "--"   }}
                @endif
              </td>
              <td>
                @if($payment_line->note) 
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
            <td>@if(auth()->user()->hasRole('Admin#' . session('business.id')) ||  auth()->user()->can('product.avarage_cost')) @if($purchase->currency_id !== null) {{ number_format($total_befor_taxx_currency,3)}}  {{ " " . $currency->symbol }} @endif  @else {{ "--" }} @endif</td>
            <td>
              @if(auth()->user()->hasRole('Admin#' . session('business.id')) ||  auth()->user()->can('product.avarage_cost'))
                  <span class="  pull-right" data-currency_symbol="false">{{number_format($total_befor_taxx,2)}}  {{ " " . $currency_symbol}}</span>
              @else
                  {{ "--" }}
              @endif
            </td>
          </tr>
          <tr>
            <th>@lang('purchase.discount'):</th>
            <td>
            @if(auth()->user()->hasRole('Admin#' . session('business.id')) ||  auth()->user()->can('product.avarage_cost'))              
              <b>(-)</b>
              @if($purchase->currency_id !== null) 
                  @php
                  $discount_i = $purchase->discount_amount;
                  @endphp
                  @if($purchase->discount_type == 'percentage')
                    @format_currency((($purchase->discount_amount * $total_before_tax / 100)/$purchase->exchange_price))
                  @elseif($purchase->discount_type == 'fixed_after_vat')
                      @php
                        $tax_ = \App\TaxRate::find($purchase->tax_id);
                        if($tax_ != null){
                          $tax = $tax_->amount;
                        }else{
                          $tax = 0;  
                        }
                      $discount_i = ($purchase->discount_amount - ($purchase->discount_amount*$tax/(100+$tax))/$purchase->exchange_price);
                      @endphp
                      {{round($discount_i,3)}}
                  @else 
                      {{round($purchase->discount_amount/$purchase->exchange_price,3)}}
                  @endif                  
                
                      
                {{ " " . $currency->symbol }} 
              @endif
              @if($purchase->discount_type == 'percentage')
                (@format_currency($purchase->discount_amount) %)
              @endif
            @else
                {{ "--" }}
            @endif  
            </td>
            <td>
                @php
                $discount_i = $purchase->discount_amount;
                @endphp
                @if(auth()->user()->hasRole('Admin#' . session('business.id')) ||  auth()->user()->can('product.avarage_cost'))
                  <span class="display_currency pull-right" data-currency_symbol="true">
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
                      {{number_format(($discount_i),3)}} {{ " " . $currency_symbol}}
                    @else 
                      {{number_format(($purchase->discount_amount),3)}} {{ " " . $currency_symbol}}
                    @endif    
                @else
                    {{ "--" }}
                @endif              
              </span>
            </td>
          </tr>
          <tr>
            <th>@lang('purchase.purchase_tax'):</th>
            <td><b>(+)</b>
              @if(auth()->user()->hasRole('Admin#' . session('business.id')) ||  auth()->user()->can('product.avarage_cost'))
                  @if($purchase->currency_id !== null) 
                      @php
                        $tax_ = \App\TaxRate::find($purchase->tax_id);
                        if($tax_ != null){
                          $tax = $tax_->amount;
                        }else{
                          $tax = 0;  
                        }
                      @endphp
                    &nbsp;&nbsp;  {{number_format($total_befor_taxx_currency*$tax/100,2) }}  
                    
                  
                    {{ " " . $currency->symbol }} 
              @else
                {{ "--" }}     
              @endif

            </td>
            <td class="text-right">
              @if(auth()->user()->hasRole('Admin#' . session('business.id')) ||  auth()->user()->can('product.avarage_cost'))
                @if(!empty($purchase_taxes))
                  @foreach($purchase_taxes as $k => $v)
                    <strong><small>{{$k}}</small></strong>  &nbsp;&nbsp; <span class="  pull-right"  > {{number_format(($v),3)}} {{ " " . $currency_symbol}} </span><br>
                  @endforeach
                @else
                  0.00 {{ " " . $currency_symbol}}
                @endif
              @else
                  {{ "--" }}
              @endif
              </td>
          </tr>
          <tr>
            <th>@lang('purchase.purchase_total_'): </th>
            <td>
              @if($purchase->currency_id !== null) 
                @if(auth()->user()->hasRole('Admin#' . session('business.id')) ||  auth()->user()->can('product.avarage_cost')) 
                  @php
                    $tax_ = \App\TaxRate::find($purchase->tax_id);
                    if($tax_ != null){
                      $tax = $tax_->amount;
                    }else{
                      $tax = 0;  
                    }
                  @endphp
                  @php
                  $discount_i_c = $purchase->discount_amount;
                  @endphp
            
                  @if($purchase->discount_type == 'percentage')
                    @php $discount_i_c = ($purchase->discount_amount * $total_before_tax / 100)/$purchase->exchange_price  @endphp 
                  @elseif($purchase->discount_type == 'fixed_after_vat')
                      @php
                        $tax_ = \App\TaxRate::find($purchase->tax_id);
                        if($tax_ != null){
                          $tax = $tax_->amount;
                        }else{
                          $tax = 0;  
                        }
                        $discount_i_c = ($purchase->discount_amount - ($purchase->discount_amount*$tax/(100+$tax))/$purchase->exchange_price);
                      @endphp
                  @else 
                    @php $discount_i_c =  round($purchase->discount_amount/$purchase->exchange_price,3)  @endphp
                  @endif 
                  {{round(($total_befor_taxx_currency - $discount_i_c + $tax),3)}}  
                  {{ " " . $currency->symbol }} 
                @else
                {{ "--" }}
                @endif
            @endif

            </td>
            @if(!empty($purchase_taxes))
              @foreach($purchase_taxes as $k => $v)
                <td>
                  @if(auth()->user()->hasRole('Admin#' . session('business.id')) ||  auth()->user()->can('product.avarage_cost'))
                    <span class="  pull-right" data-currency_symbol="true">{{number_format(($total_befor_taxx - $discount_i + $v),3)}} {{ " " . $currency_symbol}} </span>
                  @else
                    {{ "--" }}
                  @endif
                </td>
              @endforeach
            @else
                <td>
                  @if(auth()->user()->hasRole('Admin#' . session('business.id')) ||  auth()->user()->can('product.avarage_cost'))
                    <span class="  pull-right" data-currency_symbol="true">{{number_format(($total_befor_taxx - $discount_i),3)}} {{ " " . $currency_symbol}}  </span>
                  @else
                    {{ "--" }}
                  @endif 
                </td>
            @endif
          </tr>
          @if( !empty( $purchase->shipping_charges ) )
            {{-- <tr>
              <th>@lang('purchase.additional_shipping_charges'):</th>
              <td><b>(+)</b></td>
              <td><span class="display_currency pull-right" >  {{number_format(($purchase->shipping_charges),3)}} {{ " " . $currency_symbol}}</span></td>
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
                @php $total_contact = 0; $total_expense = 0; @endphp
                @foreach ($purchase->additional_shipings as $ships)
                    @foreach ($ships->items as $item)
                    @php
                    if($purchase->contact->id == $item->contact->id){ $total_contact +=  $item->amount + $item->vat; }else{ $total_expense +=  $item->amount + $item->vat;}
                    @endphp
                      <tr>
                        <input type="hidden" name="additional_shipping_item_id[]" value="{{ $item->id }}" >
                        <td class="col-xs-2">{{ $item->contact->name }}</td>
                        <td class="col-xs-2">@if(auth()->user()->hasRole('Admin#' . session('business.id')) ||  auth()->user()->can('product.avarage_cost')){{ $item->amount }} @else {{ "--" }} @endif</td>
                        <td class="col-xs-2">@if(auth()->user()->hasRole('Admin#' . session('business.id')) ||  auth()->user()->can('product.avarage_cost')){{ $item->vat }} @else {{ "--" }} @endif</td>
                        <td class="col-xs-2">@if(auth()->user()->hasRole('Admin#' . session('business.id')) ||  auth()->user()->can('product.avarage_cost')){{ $item->total }} @else {{ "--" }} @endif</td>
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
                @endforeach
                <tr id="addRow">
                  <td class="col-xs-2"> @lang('home.Total Amount') : <span id="shipping_total_amount">@if(auth()->user()->hasRole('Admin#' . session('business.id')) ||  auth()->user()->can('product.avarage_cost')){{ $total_shiping_amount  }}@else {{ "--" }} @endif </span> </td>
                  <td class="col-xs-2"> @lang('home.Total Vat') : <span id="shipping_total_vat_s">@if(auth()->user()->hasRole('Admin#' . session('business.id')) ||  auth()->user()->can('product.avarage_cost')){{ $total_shiping_vat }}@else {{ "--" }} @endif</span>   </td>
                  <td class="col-xs-2"> @lang('home.Total') : <span id="shipping_total_s">@if(auth()->user()->hasRole('Admin#' . session('business.id')) ||  auth()->user()->can('product.avarage_cost')){{ $total_shiping_s }}@else {{ "--" }} @endif</span>  </td>
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
            <td>
              @if(auth()->user()->hasRole('Admin#' . session('business.id')) ||  auth()->user()->can('product.avarage_cost'))
                @if($purchase->currency_id !== null) 
                    @if(!empty($purchase_taxes))
                      @foreach($purchase_taxes as $k => $v)
                        {{round(($total_befor_taxx_currency - $discount_i_c + $v + $total_contact),3) }}
                      @endforeach
                    @else
                          {{round(($total_befor_taxx_currency - $discount_i_c  + $total_contact),3) }}
                  @endif
                  {{ " " . $currency->symbol }} 
                  @endif
              @else 
                  {{ "--" }}
              @endif
            </td>
            @if(!empty($purchase_taxes))
            @foreach($purchase_taxes as $k => $v)
                <td>
                  @if(auth()->user()->hasRole('Admin#' . session('business.id')) ||  auth()->user()->can('product.avarage_cost'))
                    <span class="display_currency pull-right" data-currency_symbol="true" >{{number_format(($total_befor_taxx - $discount_i + $v + $total_contact),3)}} {{ " " . $currency_symbol}}</span>
                  @else 
                      {{ "--" }}
                  @endif
                </td>
            @endforeach
            @else
              <td>
                @if(auth()->user()->hasRole('Admin#' . session('business.id')) ||  auth()->user()->can('product.avarage_cost'))
                  <span class="display_currency pull-right" data-currency_symbol="true" >{{number_format(($total_befor_taxx - $discount_i  + $total_contact),3)}} {{ " " . $currency_symbol}}</span>
                @else 
                    {{ "--" }}
                @endif
              </td>
            @endif
          </tr>
          <tr>
            <th>@lang('purchase.purchase_pay'):</th>
            <td>  
              @if($purchase->currency_id !== null) 
                @if(auth()->user()->hasRole('Admin#' . session('business.id')) ||  auth()->user()->can('product.avarage_cost'))
                  @if(!empty($purchase_taxes))
                    @foreach($purchase_taxes as $k => $v)
                      {{round(($total_befor_taxx_currency - $discount_i_c + $v + $total_contact + $total_expense) ,3) }}
                    @endforeach
                  @else
                      {{round(($total_befor_taxx_currency - $discount_i_c +  $total_contact + $total_expense) ,3) }}
                  @endif
                  {{ " " . $currency->symbol }} 
                @else
                  {{ "--" }}
                @endif
              @endif

            </td>
            @if(!empty($purchase_taxes))
              @foreach($purchase_taxes as $k => $v)
              <td>
                @if(auth()->user()->hasRole('Admin#' . session('business.id')) ||  auth()->user()->can('product.avarage_cost'))
                <span class="  pull-right" data-currency_symbol="true" >{{number_format($total_befor_taxx - $discount_i + $v + $total_contact + $total_expense,3)}} {{ " " . $currency_symbol}}</span>
                @endif
                  {{ " " }} 
                @endif
              </td>
              @endforeach
            @else
            <td>
              @if(auth()->user()->hasRole('Admin#' . session('business.id')) ||  auth()->user()->can('product.avarage_cost'))
                <span class="  pull-right" data-currency_symbol="true" >{{number_format($total_befor_taxx - $discount_i +  $total_contact + $total_expense,3)}} {{ " " . $currency_symbol}}</span>
              @endif
                {{ " " . $currency->symbol }} 
              @endif
            </td>
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
</div>
  {{-- Barcode --}}
  <div class="row print_section">
    <div class="col-xs-12">
      <img class="center-block" src="data:image/png;base64,{{DNS1D::getBarcodePNG($purchase->ref_no, 'C128', 2,30,array(39, 48, 54), true)}}">
    </div>
  </div>
</div>

<!-- /.content -->
<div class="modal fade product_modal" tabindex="-1" role="dialog" 
    	aria-labelledby="gridSystemModalLabel">
</div>
<div class="modal fade products_modal" tabindex="-1" role="dialog" 
    	aria-labelledby="gridSystemModalLabel">
</div>
<div class="modal fade stocks_modal" tabindex="-1" role="dialog" 
    	aria-labelledby="gridSystemModalLabel">
</div>