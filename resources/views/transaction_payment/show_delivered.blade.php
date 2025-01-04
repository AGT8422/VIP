<div class="modal-dialog" role="document"  style="font-size:medium !important;width:90%"> 
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close no-print" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title no-print"> @lang( 'purchase.recieved_status' )  </h4>
            <h4 class="modal-title visible-print-block"> @lang('sale.invoice_no'): {{ $transaction->ref_no }} </h4>
        </div>

        <div class="modal-body">

            @if(in_array($transaction->type, ['purchase', 'purchase_return']))
                <div class="row invoice-info">
                    <div class="col-sm-4 invoice-col">
                        @include('transaction_payment.transaction_supplier_details')
                    </div>
                    <div class="col-md-4 invoice-col">
                        @include('transaction_payment.payment_business_details')
                    </div>
                    @php
                        $Global_state="";
                        if($state ==  "not_delivered"){
                            $Global_state =  "not_delivered" ;
                            
                        }else if ($state == "delivered") {
                            $Global_state="recieved";
                            
                        }else if ($state ==  "separate") {
                            $Global_state="separate";
                    
                        }   
                    @endphp
                    <div class="col-sm-4 invoice-col">
                        <b>@lang('purchase.ref_no'):</b> #{{ $transaction->ref_no }}<br/>
                        <b>@lang('messages.date'):</b> {{ @format_date($transaction->transaction_date) }}<br/>
                        <b>@lang('purchase.purchase_status'):</b> {{ __('lang_v1.' . $transaction->status) }}<br>
                        <b>@lang('sale.recieve_status'):</b> {{ __('lang_v1.' . $Global_state ) }}<br>
                    </div>
                </div>
            @elseif(in_array($transaction->type, ['expense', 'expense_refund']))
                <div class="row invoice-info">
                    @if(!empty($transaction->contact))
                        <div class="col-sm-4 invoice-col">
                            @lang('expense.expense_for'):
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
                    @endif
                    <div class="col-md-4 invoice-col">
                        @include('transaction_payment.payment_business_details')
                    </div>

                    <div class="col-sm-4 invoice-col">
                        <b>@lang('purchase.ref_no'):</b> #{{ $transaction->ref_no }}<br/>
                        <b>@lang('messages.date'):</b> {{ @format_date($transaction->transaction_date) }}<br/>
                        <b>@lang('purchase.payment_status'):</b> {{ __('lang_v1.' . $transaction->payment_status) }}<br>
                    </div>
                </div>
            @elseif($transaction->type == 'payroll')
                <div class="row invoice-info">
                    <div class="col-sm-4 invoice-col">
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
                    </div>
                    <div class="col-md-4 invoice-col">
                        @include('transaction_payment.payment_business_details')
                    </div>
                    <div class="col-sm-4 invoice-col">
                        <b>@lang('purchase.ref_no'):</b> #{{ $transaction->ref_no }}<br/>
                        @php $transaction_date = \Carbon::parse($transaction->transaction_date);  @endphp
                        <b>@lang( 'essentials::lang.month_year' ):</b> {{ $transaction_date->format('F') }} {{ $transaction_date->format('Y') }}<br/>
                        <b>@lang('purchase.payment_status'):</b> {{ __('lang_v1.' . $transaction->payment_status) }}<br>
                    </div>
                </div>
            @else
                <div class="row invoice-info">
                    <div class="col-sm-4 invoice-col">
                        @lang('contact.customer'):
                        <address>
                            <strong>{{ $transaction->contact->name }}</strong>

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
                    <div class="col-md-4 invoice-col">
                        @include('transaction_payment.payment_business_details')
                    </div>
                    <div class="col-sm-4 invoice-col">
                        <b>@lang('sale.invoice_no'):</b> #{{ $transaction->invoice_no }}<br/>
                        <b>@lang('messages.date'):</b> {{ @format_date($transaction->transaction_date) }}<br/>
                        <b>@lang('purchase.payment_status'):</b> {{ __('lang_v1.' . $transaction->payment_status) }}<br>
                    </div>
                </div>
            @endif
            
            @if(request()->type == "p_return")
                <div class="row">
                    <div class="col-md-12">
                        @if((auth()->user()->can('purchase.payments') && (in_array($transaction->type, ['purchase', 'purchase_return']) )) || (auth()->user()->can('sell.payments') && (in_array($transaction->type, ['sell', 'sell_return']))) || (auth()->user()->can('expense.access') ) )
                        <a  href="{{ action('PurchaseController@recieved_page', ["id" => $transaction->id ,"type" => "return_purchase"]) }}" class="btn btn-primary btn-xs pull-right  no-print"><i class="fa fa-plus" aria-hidden="true"></i>  @lang("purchase.add_Receipt_response")  </a>
                        @endif
                    </div>
                </div>
            @else
                <div class="row">
                    <div class="col-md-12"> 
                        @if((auth()->user()->can('purchase.payments') && (in_array($transaction->type, ['purchase', 'purchase_return']) )) || auth()->user()->can('warehouse.views') || (auth()->user()->can('sell.payments') && (in_array($transaction->type, ['sell', 'sell_return']))) || (auth()->user()->can('expense.access') ) )
                                <a  href="{{ action('PurchaseController@recieved_page', ["id" => $transaction->id]) }}" class="btn btn-primary btn-xs pull-right  no-print"><i class="fa fa-plus" aria-hidden="true"></i>  @lang("purchase.add_recieved")  </a>
                        @endif 
                    </div>
                </div>
            @endif

            @if(request()->type == "p_return")
                <div class="row">
                    <div class="col-sm-12">
                        <br>
                        <table class="table bg-gray">
                            <thead>
                            <tr class="bg-green">
                                <th>#</th>
                                <th>@lang('product.product_name')</th>
                                <th>@lang('sale.unit_price')</th>
                                <th>@lang('lang_v1.return_quantity')</th>
                                <th>@lang('lang_v1.return_subtotal')</th>
                                <th>@lang("home.total received")</th>
                                <th>@lang("purchase.total_remain")</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                 $tr = \App\Transaction::where("id",$transaction->return_parent_id)->first();
                                 $total_before_tax = 0;
                            @endphp
                            @foreach($tr->purchase_lines as $purchase_line)
                            @php
										 
                                $total_ = \App\Models\RecievedPrevious::where("transaction_id",$transaction->return_parent_id)->where("product_id",$purchase_line->product->id)->sum("current_qty");    
                                $main   = \App\PurchaseLine::where("transaction_id",$transaction->return_parent_id)->where("product_id",$purchase_line->product->id)->sum("quantity_returned");    
                                    
                            @endphp
                            @if($purchase_line->quantity_returned == 0)
                                @continue
                            @endif
                
                            @php
                                $unit_name = $purchase_line->product->unit->short_name;
                                if(!empty($purchase_line->sub_unit)) {
                                $unit_name = $purchase_line->sub_unit->short_name;
                                }
                            @endphp
                            <tr  @if(($main-$total_) == 0) style="border:1px solid #f1f1f1;" @else @php $array[] = $purchase_line->product->id; @endphp style="border:1px solid #f1f1f1; background:#ff4d4d9a"@endif>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    {{ $purchase_line->product->name }}
                                    @if( $purchase_line->product->type == 'variable')
                                    - {{ $purchase_line->variations->product_variation->name}}
                                    - {{ $purchase_line->variations->name}}
                                    @endif
                                </td>
                                @php $cost        = \App\Product::product_cost_purchase($purchase_line->product->id,$tr->id,"return");  @endphp
                                <td><span class="display_currency" data-currency_symbol="true">{{ ($cost)?$cost:$purchase_line->bill_return_price }}</span></td>
                                <td>{{@format_quantity($purchase_line->quantity_returned)}} {{$unit_name}}</td>
                                <td>
                                    @php
                                    $line_total = (($cost)?$cost:$purchase_line->bill_return_price) * $purchase_line->quantity_returned;
                                    $total_before_tax += $line_total ;
                                    @endphp
                                    <span class="display_currency" data-currency_symbol="true">{{$line_total}}</span>
                                </td> 
                                <td>
                                    {{ $total_ }}    
                                </td>  
                                <td>{{$main-$total_}}</td>
                            </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @else
                @component('components.widget', ['class' => 'box-primary', 'title' => __('recieved.previous_purchase')." ( ".__('purchase.ref_no') . " ) :$transaction->ref_no " ])
                    <div class="row">
                        <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table table-bordered " >
                                    <tr style="background:#f1f1f1;"  >
                                    <th>@lang('product.product_name')</th>
                                    <th>@lang('purchase.amount')</th>
                                    <th>@lang('Purchase Note')</th>
                                    <th>@lang('purchase.delivery_status')</th>
                                    <th>@lang('warehouse.nameW')</th>
                                    <th>@lang("home.total received")</th>
                                    <th>@lang("purchase.total_remain")</th>
                                    <th>@lang('purchase.payment_note')</th>
                                    </tr>
                                    @php
                                            // dd($transaction);
                                            $total2 = 0;
                                    @endphp
                                    @forelse ($purchcaseline as $payment)
                                    @php
                                        $total_ = \App\Models\RecievedPrevious::where("transaction_id",$payment->transaction->id)->where("product_id",$payment->product->id)->sum("current_qty");    
                                        $main   = \App\PurchaseLine::where("transaction_id",$payment->transaction->id)->where("product_id",$payment->product->id)->sum("quantity");    
                                    @endphp
                                        @php $total2 = $purline ; @endphp 
            
                                        <tr  @if(($main-$total_) == 0) style="border:1px solid #f1f1f1;" @else  style="border:1px solid #f1f1f1; background:#ff4d4d9a"@endif>
                                            {{-- <td>{{$payment->created_at}}</td> --}}
                                        <td>
                                            <a  target="_blank" href="/item-move/{{$payment->product->id}}">
                                            {{$payment->product->name}}
                                            </a>
                                        </td>
                                        <td>{{$payment->quantity}}</td>
                                        <td>{!! $payment->purchase_note !!}</td>
                                        <td>{{$transaction->status}}</td>
                                        <td>{{$payment->warehouse->name}}</td>
                                        <td>
                                            {{ $total_}}    
                                        </td>    
                                        <td>{{$main-$total_}}</td>    
                                        <td>{!! Form::text('product_id_src', $payment->product->id ,["hidden",'id' => 'product_id_src']); !!}</td>
                                        <td hidden>{!! Form::text('product_id_str', $payment->warehouse->id ,["hidden",'id' => 'product_id_str']); !!}</td>
                                        <td hidden>{!! Form::text('product_id_unit_value', $payment->product->unit->actual_name ,["hidden",'id' => 'product_id_unit_value']); !!}</td>
                                        <td hidden>{!! Form::text('product_id_qty', $payment->quantity ,["hidden",'id' => 'product_id_qty']); !!}</td>
                                       
                                    </tr>
                                        
                                        
                                    @empty
                                    
                                    @endforelse
                                    <tfoot>
                                    <tr class="bg-gray  font-17 footer-total" style="border:1px solid #f1f1f1;  ">
                                        <td class="text-center " colspan="1"><strong>@lang('sale.total'):</strong></td>
                                        <td>{{intval($total2)}}</td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                
                                    </tr>
                                </tfoot>
                                    </table>
                            </div>
                        </div>
                    </div>
                @endcomponent
            @endif
            @component('components.widget', ['class' => 'box-primary', 'title' => __('recieved.All_reciept')])
                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table class="table table-striped">
                            <tr style="background:#303030; color:white !important;">
                                <th>#</th>
                                <th>@lang('sale.reciept_no')</th>
                                <th>@lang('purchase.ref_no')</th>
                                <th>@lang('purchase.status')</th>
                                <th>@lang('warehouse.nameW')</th>
                                <th>@lang('messages.date')</th>
                                <th class="no-print">@lang('messages.actions')</th>
                            </tr>
                            @php $id_number = 1; @endphp
                            @forelse ($transaction_deliveries as $payment)
                                @php $owner = $payment["id"]; @endphp
                                <tr>
                                    <td>{{ $id_number }}</td>
                                    <td>{{ $payment->reciept_no}}</td>
                                    <td>{{ $payment->ref_no }}</td>
                                    <td>{{ $payment->status }}</td>
                                    <td>{{ $payment->store->name }}</td>
                                    <td>{{ ($payment->date)?$payment->date:$payment->created_at }}</td>
                                    <td class="no-print" style="display: flex;">
                                
                                    &nbsp;
                                    @if(auth()->user()->can("warehouse.Edit") || auth()->user()->can("manufuctoring.Edit") || auth()->user()->can("admin_without.Edit") || auth()->user()->can("admin_supervisor.Edit"))
                                    @if(request()->type == "p_return")
                                        <a type="button"  href="{{ action('PurchaseController@update_recieved', ["id" => $transaction->id,"trn"=>$payment->id,"return"=>"return"]) }}">
                                            <button class="btn bg-yellow"> <i class="fa fa-edit" aria-hidden="true"></i>
                                            </button>
                                        </a>
                                    @else
                                        <a type="button"  href="{{ action('PurchaseController@update_recieved', ["id" => $transaction->id,"trn"=>$payment->id]) }}">
                                            <button class="btn bg-yellow"> <i class="fa fa-edit" aria-hidden="true"></i>
                                            </button>
                                        </a>                                    
                                    @endif
                                    @endif
                                    &nbsp;  
                                    <button type="button" class="btn btn-primary btn-xs view_payment" data-href="{{ action('TransactionPaymentController@viewRecieve', [$owner]) }}">
                                        <i class="fa fa-eye" aria-hidden="true"></i>
                                    </button>
                                    &nbsp; 
                                    @can('receive_delete')
                                        @if(request()->type == "p_return")
                                            <button type="button" class="btn btn-danger btn-xs delete_recieve" 
                                                    data-href="{{ action('TransactionPaymentController@destroy_recieve', [$owner,"return_type"=>"return_type"]) }}"
                                                    ><i class="fa fa-trash" aria-hidden="true"></i>
                                            </button>
                                        @else
                                            <button type="button" class="btn btn-danger btn-xs delete_recieve" 
                                                    data-href="{{ action('TransactionPaymentController@destroy_recieve', [$owner]) }}"
                                                    ><i class="fa fa-trash" aria-hidden="true"></i>
                                            </button>
                                        @endif
                                    @endcan
                                    
                            
                                    @if(!empty($payment->document_path))
                                    &nbsp;
                                    <a href="{{$payment->document_path}}" class="btn btn-success btn-xs" download="{{$payment->document_name}}"><i class="fa fa-download" data-toggle="tooltip" title="{{__('purchase.download_document')}}"></i></a>
                                    @if(isFileImage($payment->document_name))
                                    &nbsp;
                                        <button data-href="{{$payment->document_path}}" class="btn btn-info btn-xs view_uploaded_document" data-toggle="tooltip" title="{{__('lang_v1.view_document')}}"><i class="fa fa-picture-o"></i></button>
                                    @endif

                                    @endif
                                    </td>
                                </tr>
                                @php $id_number++ @endphp
                            @empty

                                <tr class="text-center">
                                    <td colspan="6">@lang('purchase.no_records_found')</td>
                                </tr>
                            
                            @endforelse
                            </table>
                        </div>
                    </div>
                </div>
            @endcomponent
            @if(request()->type == "p_return")
                @component('components.widget', ['class' => 'box-primary', 'title' => __('recieved.all_return_recieved')])
                    <div class="row">
                        <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table table-bordered" >
                                        <tr style="background:#303030; color:white !important;">
                                            <th>@lang('product.product_name')</th>
                                            <th>@lang('product.unit')</th>
                                            <th>@lang('purchase.amount_current')</th>
                                            <th>@lang('warehouse.nameW')</th>
                                            <th>@lang('purchase.payment_note')</th>
                                        </tr>
                                        @php $empty = 0 ; $pre = ""; @endphp
                                        
                                        @forelse ($RecievedPrevious as $Recieved)
                                                @php
                                                    $date = Carbon::now(); 
                                                    if(!in_array($Recieved->product->id,$product_list_all_return)){
                                                        $type = "other";
                                                    }else{
                                                        $type = "base";
                                                    }
                                                @endphp

                                                @if ($pre != $Recieved->created_at)
                                                    @if ($pre == "")  @else
                                                    <tr  style="border:1px solid #f1f1f1;" >
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
                                                    <td>{{$Recieved->product->name}}</td>
                                                    <td>{{$Recieved->product->unit->actual_name}}</td>
                                                    <td>{{$Recieved->current_qty}}</td>
                                                    <td>{{$Recieved->store->name}}</td>
                                                    <td></td>
                                                </tr>
                                            
                                        @empty 
                                                @forelse ($RecievedWrong as $RecWrong)
                                    
                                                    @php
                                                        $empty =1;
                                                        $date = Carbon::now(); 
                                                        if(!in_array($RecWrong->product->id,$product_list_all_return)){
                                                            $type = "other";
                                                        }else{
                                                            $type = "base";
                                                        }
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
                                                        <td>{{$RecWrong->product->name}}</td>
                                                        <td>{{$RecWrong->product->unit->actual_name}}</td>
                                                        <td>{{$RecWrong->current_qty}}</td>
                                                        <td>{{$RecWrong->store->name}}</td>
                                                        <td></td>
                                                    </tr>
                                                @empty @endforelse
                                    
                                        @endforelse 
                                        @if($empty == 0)
                                            @forelse ($RecievedWrong as $RecWrong)
                                                @php
                                                    $date = Carbon::now(); 
                                                    if(!in_array($RecWrong->product->id,$product_list_all_return)){
                                                        $type = "other";
                                                    }else{
                                                        $type = "base";
                                                    }
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
                                                    <td>{{$RecWrong->product->name}}</td>
                                                    <td>{{$RecWrong->product->unit->actual_name}}</td>
                                                    <td>{{$RecWrong->current_qty}}</td>
                                                    <td>{{$RecWrong->store->name}}</td>
                                                    <td></td>
                                                </tr>
                                            @empty @endforelse
                                        @endif

                                        <tfoot>
                                            <tr class="bg-gray  font-17 footer-total" style="border:1px solid #f1f1f1;  ">
                                                <td>@lang("sale.total") : {{intval($purline_return)}} </td>
                                                <td class="text-center " colspan="1"><strong></strong></td>
                                                <td>{{$totals }} </td>
                                                <td></td>
                                                <td> </td>
                                            </tr>
                                        </tfoot>

                                    </table>
                            </div>
                        </div>
                    </div>
                @endcomponent
            @else
                @component('components.widget', ['class' => 'box-primary', 'title' => __('recieved.all_recieved')])
                    <div class="row">
                        <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table table-bordered" >
                                        <tr style="background:#303030; color:white !important;">
                                            <th>@lang('product.product_name')</th>
                                            <th>@lang('product.unit')</th>
                                            <th>@lang('purchase.amount_current')</th>
                                            <th>@lang('warehouse.nameW')</th>
                                            <th>@lang('purchase.payment_note')</th>
                                        </tr>
                                        @php $empty = 0 ; $pre = ""; @endphp
                                        
                                        @forelse ($RecievedPrevious as $Recieved)
                                                @php
                                                    $date = Carbon::now(); 
                                                    if(!in_array($Recieved->product->id,$product_list_all)){
                                                        $type = "other";
                                                    }else{
                                                        $type = "base";
                                                    }
                                                @endphp

                                                @if ($pre != $Recieved->created_at)
                                                    @if ($pre == "")  @else
                                                    <tr  style="border:1px solid #f1f1f1;" >
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
                                                    <td>{{$Recieved->product->name}}</td>
                                                    <td>{{$Recieved->product->unit->actual_name}}</td>
                                                    <td>{{$Recieved->current_qty}}</td>
                                                    <td>{{$Recieved->store->name}}</td>
                                                    <td></td>
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
                                                        <td>{{$RecWrong->product->name}}</td>
                                                        <td>{{$RecWrong->product->unit->actual_name}}</td>
                                                        <td>{{$RecWrong->current_qty}}</td>
                                                        <td>{{$RecWrong->store->name}}</td>
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
                                                    <td>{{$RecWrong->product->name}}</td>
                                                    <td>{{$RecWrong->product->unit->actual_name}}</td>
                                                    <td>{{$RecWrong->current_qty}}</td>
                                                    <td>{{$RecWrong->store->name}}</td>
                                                    <td></td>
                                                </tr>
                                            @empty @endforelse
                                        @endif

                                        <tfoot>
                                            <tr class="bg-gray  font-17 footer-total" style="border:1px solid #f1f1f1;  ">
                                                <td>@lang("sale.total") : {{intval($purline)}} </td>
                                                <td class="text-center " colspan="1"><strong></strong></td>
                                                <td>{{$totals }} </td>
                                                <td></td>
                                                <td> </td>
                                            </tr>
                                        </tfoot>

                                    </table>
                            </div>
                        </div>
                    </div>
                @endcomponent
            @endif
        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-primary no-print" 
              aria-label="Print" 
                onclick="$(this).closest('div.modal').printThis();">
                <i class="fa fa-print"></i> @lang( 'messages.print' )
            </button>
            <button type="button" class="btn btn-default no-print" data-dismiss="modal">@lang( 'messages.close' )</button>
        </div>
    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
 