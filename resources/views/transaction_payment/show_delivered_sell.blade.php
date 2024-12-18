<div class="modal-dialog" role="document" style="width:90%">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close no-print" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title no-print">
                @lang( 'purchase.delivery_status' ) 
                (
                @if(in_array($transaction->type, ['purchase', 'expense', 'purchase_return', 'payroll']))    
                    @lang('purchase.ref_no'): {{ $transaction->ref_no }} 
                @elseif(in_array($transaction->type, ['sale', 'sell_return']))
                    @lang('sale.invoice_no'): {{ $transaction->invoice_no }}
                @endif
                )   
            </h4>
            <h4 class="modal-title visible-print-block">
                @if(in_array($transaction->type, ['purchase', 'expense', 'purchase_return', 'payroll'])) 
                    @lang('purchase.ref_no'): {{ $transaction->ref_no }}
                @elseif($transaction->type == 'sale')
                    @lang('sale.invoice_no'): {{ $transaction->invoice_no }}
                @endif
            </h4>
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

                    <div class="col-sm-4 invoice-col">
                        <b>@lang('purchase.ref_no'):</b> #{{ $transaction->ref_no }}<br/>
                        <b>@lang('messages.date'):</b> {{ @format_date($transaction->transaction_date) }}<br/>
                        <b>@lang('purchase.purchase_status'):</b> {{ __('sale.' . $transaction->status) }}<br>
                        <b>@lang('purchase.payment_status'):</b> {{ __('lang_v1.' . $transaction->payment_status) }}<br>
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
                        @php
                            $transaction_date = \Carbon::parse($transaction->transaction_date);
                        @endphp
                        <b>@lang( 'essentials::lang.month_year' ):</b> {{ $transaction_date->format('F') }} {{ $transaction_date->format('Y') }}<br/>
                        <b>@lang('purchase.payment_status'):</b> {{ __('lang_v1.' . $transaction->payment_status) }}<br>
                    </div>
                </div>
            @else
                <div class="row invoice-info">
                    <div class="col-sm-4 invoice-col">
                        @lang('contact.customer'):
                        <address>
                            <strong>{{ $transaction->contact->name }}</strong> {!! $transaction->contact->contact_address !!}
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
           
            @if(request()->type == "s_return")
                <div class="row">
                    <div class="col-md-12">
                        @if((auth()->user()->can('purchase.payments') && (in_array($transaction->type, ['purchase', 'purchase_return']) )) || (auth()->user()->can('sell.payments') && (in_array($transaction->type, ['sell', 'sell_return']))) || (auth()->user()->can('expense.access') ) || (auth()->user()->can('warehouse.views') ) )
                                @if(request()->approved)    
                                    <a href="{{ action('DeliveryPageController@create', ["id" => $transaction->id,"reciept"=>"e","type"=>"return_sale","approved"=>"approved"]) }}" class="btn btn-primary btn-xs pull-right   no-print"><i class="fa fa-plus" aria-hidden="true"></i> @lang("purchase.add_Delivery_response")</a>
                                @else
                                    <a href="{{ action('DeliveryPageController@create', ["id" => $transaction->id,"reciept"=>"e","type"=>"return_sale"]) }}" class="btn btn-primary btn-xs pull-right   no-print"><i class="fa fa-plus" aria-hidden="true"></i> @lang("purchase.add_Delivery_response")</a>
                                @endif
                            @endif
                    </div>
                </div>
            @else 
                @if(($TraSeLine - $DelPrevious) > 0)  
                    <div class="row">
                        <div class="col-md-12">
                            @if((auth()->user()->can('purchase.payments') && (in_array($transaction->type, ['purchase', 'purchase_return']) )) || (auth()->user()->can('sell.payments') && (in_array($transaction->type, ['sell', 'sell_return']))) || (auth()->user()->can('expense.access') )|| (auth()->user()->can('warehouse.views') ) )
                                @if(request()->approved)    
                                    <a href="{{ action('DeliveryPageController@create', ["id" => $transaction->id,"reciept"=>"e","approved"=>"approved"]) }}" class="btn btn-primary btn-xs pull-right   no-print"><i class="fa fa-plus" aria-hidden="true"></i> @lang("purchase.add_delivery")</a>
                                @else
                                    <a href="{{ action('DeliveryPageController@create', ["id" => $transaction->id,"reciept"=>"e"]) }}" class="btn btn-primary btn-xs pull-right   no-print"><i class="fa fa-plus" aria-hidden="true"></i> @lang("purchase.add_delivery")</a>
                                @endif
                            @endif
                        </div>
                    </div>
                @endif
            @endif
           
            @if(request()->type == "s_return")
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
                                <th>@lang("home.total delivered")</th>
                                <th>@lang("purchase.total_remain")</th>
                            </tr>
                            </thead>
                            <tbody>
                                @php
                                    $tr = \App\Transaction::where("id",$transaction->return_parent_id)->first();
                                    $total_before_tax = 0;
                                @endphp
                                @foreach($tr->sell_lines as $sell_line)
                                @php
										 
                                    $total_ = \App\Models\DeliveredPrevious::where("transaction_id",$transaction->return_parent_id)->where("product_id",$sell_line->product->id)->sum("current_qty");    
                                    $main   = \App\TransactionSellLine::where("transaction_id",$transaction->return_parent_id)->where("product_id",$sell_line->product->id)->sum("quantity_returned");    
                                        
                                @endphp
                                @if($sell_line->quantity_returned == 0)
                                    @continue
                                @endif
                    
                                @php
                                    $unit_name = $sell_line->product->unit->short_name;
                                    if(!empty($sell_line->sub_unit)) {
                                        $unit_name = $sell_line->sub_unit->short_name;
                                    }
                                @endphp
                    
                                <tr  @if(($main-$total_) == 0) style="border:1px solid #f1f1f1;" @else @php $array[] = $sell_line->product->id; @endphp style="border:1px solid #f1f1f1; background:#ff4d4d9a"@endif>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        {{ $sell_line->product->name }}
                                        @if( $sell_line->product->type == 'variable')
                                        - {{ $sell_line->variations->product_variation->name}}
                                        - {{ $sell_line->variations->name}}
                                        @endif
                                    </td>
                                    <td><span class="display_currency" data-currency_symbol="true">{{ $sell_line->bill_return_price }}</span></td>
                                    <td>{{@format_quantity($sell_line->quantity_returned)}} {{$unit_name}}</td>
                                    <td>
                                        @php
                                        $line_total = $sell_line->bill_return_price * $sell_line->quantity_returned;
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
                @component("components.widget",["title"=>__("recieved.previous_purchase")])
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table table-bordered " >
                                <tr style="background:#f1f1f1;"  >
                                <th>@lang('product.product_name')</th>
                                <th>@lang('purchase.amount')</th>
                                <th>@lang('purchase.delivery_status')</th>
                                <th>@lang('warehouse.nameW')</th>
                                <th>@lang("home.total delivered")</th>
                                <th>@lang("purchase.total_remain")</th>
                                <th>@lang('purchase.payment_note')</th>
                                </tr>
                                @php
                                // dd($transaction);
                                $total2 = 0;
                                @endphp
                                @forelse ($TransactionSellLine as $payment)
                                    @if(isset($transaction_child))
                                        @php
                                            $total_ =0;
                                            // $total_ = \App\Models\DeliveredPrevious::where("transaction_id",$payment->transaction->id)->where("product_id",$payment->product->id)->sum("current_qty");    
                                            $main   = \App\TransactionSellLine::where("transaction_id",$payment->transaction->id)->where("product_id",$payment->product->id)->sum("quantity");    
                                        @endphp
                                        @foreach($transaction_child as $one)
                                            @php
                                                $total_ += \App\Models\DeliveredPrevious::where("transaction_id",$one->id)->where("product_id",$payment->product->id)->sum("current_qty");    
                                            @endphp
                                        @endforeach
                                    @else
                                        @php
                                            $total_ = \App\Models\DeliveredPrevious::where("transaction_id",$payment->transaction->id)->where("product_id",$payment->product->id)->sum("current_qty");    
                                            $main   = \App\TransactionSellLine::where("transaction_id",$payment->transaction->id)->where("product_id",$payment->product->id)->sum("quantity");    
                                        @endphp
                                    @endif
                                    @php $total2 = $TraSeLine ; @endphp 
                                    <tr  @if(($main-$total_) == 0) style="border:1px solid #f1f1f1;" @else  style="border:1px solid #f1f1f1; background:#ff4d4d9a"@endif>
                                    <td>
                                        <a  target="_blank" href="/item-move/{{$payment->product->id}}">
                                        {{$payment->product->name}}
                                        </a>
                                    </td>
                                    <td>{{$payment->quantity}}</td>
                                    <td>{{app("request")->status}}</td>
                                    <td>{{$payment->store->name}}</td>
                                    <td>
                                        {{ $total_}}    
                                    </td>    
                                    <td>{{$main-$total_}}</td>    
                                    <td>{!! Form::text('product_id_src', $payment->product->id ,["hidden",'id' => 'product_id_src']); !!}</td>
                                    <td hidden>{!! Form::text('product_id_str', $payment->store->id ,["hidden",'id' => 'product_id_str']); !!}</td>
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
                            
                                </tr>
                            </tfoot>
                                </table>
                        </div>
                        </div>
                    </div>
                @endcomponent
            @endif
          
            @component("components.widget",["title"=>__("recieved.All_reciept")])
                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <tr style="background:#303030; color:white !important;">
                                    <th>#</th>
                                    <th>@lang('sale.reciept_no')</th>
                                    <th>@lang('sale.invoice_no')</th>
                                    @if(isset($transaction_child))
                                        <th>Total Payments</th>
                                        <th>Total Remaining</th>
                                    @endif
                                    <th>@lang('purchase.status')</th>
                                    <th>@lang('warehouse.nameW')</th>
                                    <th>@lang('messages.date')</th>
                                    <th class="no-print">@lang('messages.actions')</th>
                                    <th class="no-print">@lang('purchase.attach_document')</th>
                                </tr>
                                @php $count = 0;  @endphp
                                @if(isset($transaction_child))
                                    @forelse ($transaction_delivery as $payment)
                                        @php  $count++;  $owner = $payment[0]["id"]; @endphp
                                        <tr style="background:#f3f3f3; color:rgb(0, 0, 0) !important;">
                                            <td>{{ $count }}</td>
                                            <td>{{ $payment[0]->reciept_no}}</td>
                                            <td>{{ $payment[0]->invoice_no }}</td>
                                            @if(isset($transaction_child))
                                                    @php
                                                        $final_total =  0;
                                                        $transaction_payment = 0;
                                                    @endphp
                                                @foreach($transaction_child  as $one)
                                                    @if($one->id == $payment[0]->transaction_id)
                                                        @php
                                                            $transaction_payment = \App\TransactionPayment::where("transaction_id",$one->id)->where("return_payment",0)->sum("amount");
                                                            $final_total = $one->final_total;
                                                        @endphp
                                                    @endif
                                                @endforeach
                                                <td @if($transaction_payment == 0) style="color:black;background-color:#ff9494" @endif >{{$transaction_payment}}</td>
                                                <td @if($transaction_payment == 0) style="color:black;background-color:#ff9494" @endif  >{{$final_total - $transaction_payment}}</td>
                                            @endif
                                            <td>{{ $payment[0]->status }}</td>
                                            <td>{{ $payment[0]->store->name }}</td>
                                            <td>{{ $payment[0]->date }}</td>
                                            <td class="no-print" style="display: flex;">
                                            @if(auth()->user()->can("warehouse.Edit") || auth()->user()->can("manufuctoring.Edit") || auth()->user()->can("admin_without.Edit") || auth()->user()->can("admin_supervisor.Edit"))
                                        
                                            &nbsp; 
                                            @if( request()->session()->get("user.id") == 1)
                                            @if(request()->type == "s_return")  
                                                    <button type="button" class="btn btn-danger btn-xs delete_recieve" 
                                                        data-href="{{ action('TransactionPaymentController@destroy_delivery', [$owner,"return_type"=>"return_type"]) }}"
                                                        ><i class="fa fa-trash" aria-hidden="true"></i>
                                                    </button>
                                            @else
                                                    <button type="button" class="btn btn-danger btn-xs delete_recieve" 
                                                        data-href="{{ action('TransactionPaymentController@destroy_delivery', [$owner]) }}"
                                                        ><i class="fa fa-trash" aria-hidden="true"></i>
                                                    </button>
                                            @endif
                                            @endif
                                            @endif
                                            &nbsp; 
                                            @php $idDelivery = $payment[0]->id; @endphp
                                            @if(auth()->user()->can("warehouse.Edit") || auth()->user()->can("manufuctoring.Edit") || auth()->user()->can("admin_without.Edit") || auth()->user()->can("admin_supervisor.Edit"))
                                                @if(request()->type == "s_return")    
                                                    @if($payment[0]->status != "Service Item" && $payment[0]->is_invoice == null)
                                                        <a href="{{ action('DeliveryPageController@edit_delivery', ["id"  => $idDelivery,"return"=>"return"]) }}" class="btn  btn-xs pull-right bg-yellow  no-print"> <i class="fa fa-edit"></i>   </a>
                                                    @endif
                                                @else
                                                @if(request()->approved)
                                                    @if($payment[0]->status != "Service Item" && $payment[0]->is_invoice == null) 
                                                        <a href="{{ action('DeliveryPageController@edit_delivery', ["id"  => $idDelivery,"approved"=>"approved"]) }}" class="btn  btn-xs pull-right bg-yellow  no-print"> <i class="fa fa-edit"></i>   </a>
                                                    @endif
                                                @else
                                                    @if($payment[0]->status != "Service Item" && $payment[0]->is_invoice == null) 
                                                        <a href="{{ action('DeliveryPageController@edit_delivery', ["id"  => $idDelivery]) }}" class="btn  btn-xs pull-right bg-yellow  no-print"> <i class="fa fa-edit"></i>   </a>
                                                    @endif
                                                @endif
                                                @endif
                                            @endif
                                            &nbsp;
                                            <button type="button" class="btn btn-primary btn-xs view_payment" data-href="{{ action('TransactionPaymentController@viewDelivered', [$idDelivery]) }}">
                                                <i class="fa fa-eye" aria-hidden="true"></i>
                                            </button>
                                            @php
                                                $data = $payment[0]->document;
                                                $parsed = \App\Models\TransactionDelivery::get_string_between($data, '["', '"]');
                                            @endphp
                                            @if($parsed != "")
                                                <td>
                                                    <a href="{{ URL::to($parsed) }}" target="_blank">
                                                        <i class="fas fa-eye"></i>
                                                        </a>
                                                </td>
                                            @endif
                                            @if(!empty($payment[0]->document_path))
                                            &nbsp;
                                            @php $path = $payment[0]->document_path; $pathName = $payment[0]->document_name; @endphp 
                                            <a href="{{$path}}" class="btn btn-success btn-xs" download="{{$pathName}}"><i class="fa fa-download" data-toggle="tooltip" title="{{__('purchase.download_document')}}"></i></a>
                                            @if(isFileImage($payment[0]->document_name))
                                            &nbsp;
                                                <button data-href="{{$path}}" class="btn btn-info btn-xs view_uploaded_document" data-toggle="tooltip" title="{{__('lang_v1.view_document')}}"><i class="fa fa-picture-o"></i></button>
                                            @endif
                                            @endif
                                            </td>
                                        
                                        </tr>
                                    @empty
                                        <tr class="text-center">
                                            <td colspan="6">@lang('purchase.no_records_found')</td>
                                        </tr>
                                    @endforelse
                                @else
                                    @forelse ($transaction_delivery as $payment)
                                        @php  $count++;  $owner = $payment["id"]; @endphp
                                        <tr style="background:#f3f3f3; color:rgb(0, 0, 0) !important;"">
                                            <td>{{ $count }}</td>
                                            <td>{{ $payment->reciept_no}}</td>
                                            <td>{{ $payment->invoice_no }}</td>
                                            <td>{{ $payment->status }}</td>
                                            <td>{{ $payment->store->name }}</td>
                                            <td>{{ $payment->date }}</td>
                                            <td class="no-print" style="display: flex;">
                                            @if(auth()->user()->can("warehouse.Edit") || auth()->user()->can("manufuctoring.Edit") || auth()->user()->can("admin_without.Edit") || auth()->user()->can("admin_supervisor.Edit"))
                                        
                                            &nbsp; 
                                            @if( request()->session()->get("user.id") == 1)
                                            @if(request()->type == "s_return")  
                                                    <button type="button" class="btn btn-danger btn-xs delete_recieve" 
                                                        data-href="{{ action('TransactionPaymentController@destroy_delivery', [$owner,"return_type"=>"return_type"]) }}"
                                                        ><i class="fa fa-trash" aria-hidden="true"></i>
                                                    </button>
                                            @else
                                                    <button type="button" class="btn btn-danger btn-xs delete_recieve" 
                                                        data-href="{{ action('TransactionPaymentController@destroy_delivery', [$owner]) }}"
                                                        ><i class="fa fa-trash" aria-hidden="true"></i>
                                                    </button>
                                            @endif
                                            @endif
                                            @endif
                                            &nbsp; 
                                           
                                            @if(auth()->user()->can("warehouse.Edit") || auth()->user()->can("manufuctoring.Edit") || auth()->user()->can("admin_without.Edit") || auth()->user()->can("admin_supervisor.Edit"))
                                                @if(request()->type == "s_return")    
                                                    @if($payment->status != "Service Item" && $payment->is_invoice == null)
                                                        <a href="{{ action('DeliveryPageController@edit_delivery', ["id"  => $payment->id,"return"=>"return"]) }}" class="btn  btn-xs pull-right bg-yellow  no-print"> <i class="fa fa-edit"></i>   </a>
                                                    @endif
                                                @else
                                                @if(request()->approved)
                                                    @if($payment->status != "Service Item" && $payment->is_invoice == null) 
                                                        <a href="{{ action('DeliveryPageController@edit_delivery', ["id"  => $payment->id,"approved"=>"approved"]) }}" class="btn  btn-xs pull-right bg-yellow  no-print"> <i class="fa fa-edit"></i>   </a>
                                                    @endif
                                                @else
                                                    @if($payment->status != "Service Item" && $payment->is_invoice == null) 
                                                        <a href="{{ action('DeliveryPageController@edit_delivery', ["id"  => $payment->id]) }}" class="btn  btn-xs pull-right bg-yellow  no-print"> <i class="fa fa-edit"></i>   </a>
                                                    @endif
                                                @endif
                                                @endif
                                            @endif
                                            &nbsp;
                                            <button type="button" class="btn btn-primary btn-xs view_payment" data-href="{{ action('TransactionPaymentController@viewDelivered', [$payment->id]) }}">
                                                <i class="fa fa-eye" aria-hidden="true"></i>
                                            </button>
                                            @php
                                                $data = $payment->document;
                                                $parsed = \App\Models\TransactionDelivery::get_string_between($data, '["', '"]');
                                            @endphp
                                            @if($parsed != "")
                                                <td>
                                                    <a href="{{ URL::to($parsed) }}" target="_blank">
                                                        <i class="fas fa-eye"></i>
                                                        </a>
                                                </td>
                                            @endif
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
                                    @empty
                                        <tr class="text-center">
                                            <td colspan="6">@lang('purchase.no_records_found')</td>
                                        </tr>
                                    @endforelse
                                @endif
                            </table>
                        </div>
                    </div>
                </div>
            @endcomponent
           
            @if(isset($transaction_child))
                @if(request()->type == "s_return")
                    @component("components.widget",["title"=>__("recieved.all_return_delivered")])
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
                                                @forelse ($DeliveredWrong as $RecWrong)
                                    
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
                                            @forelse ($DeliveredWrong as $RecWrong)
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
                                                <td>@lang("sale.total") : {{intval($TraSeLine_return)}} </td>
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
                    @component("components.widget",["title"=>__("recieved.all_delivered")])
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
                                        @php $empty = 0 ; $pre = "";   @endphp
                                        @foreach($RecievedPrevious as $RecievedPreviousItem)
                                            @forelse ($RecievedPreviousItem as $Recieved)
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
                                                @foreach($DeliveredWrong as $k => $DeliveredWrongItem)
                                                    @forelse ($DeliveredWrongItem as $RecWrong)
                                        
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
                                                @endforeach
                                            @endforelse 
                                        @endforeach
                                        @if($empty == 0)
                                        @foreach($DeliveredWrong as $k => $DeliveredWrongItem)
                                            @forelse ($DeliveredWrongItem as $RecWrong)
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
                                            @endforeach
                                        @endif

                                        <tfoot>
                                            <tr class="bg-gray  font-17 footer-total" style="border:1px solid #f1f1f1;  ">
                                                <td>@lang("sale.total") : {{intval($TraSeLine)}} </td>
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
            @else
                @if(request()->type == "s_return")
                    @component("components.widget",["title"=>__("recieved.all_return_delivered")])
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
                                                @forelse ($DeliveredWrong as $RecWrong)
                                    
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
                                            @forelse ($DeliveredWrong as $RecWrong)
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
                                                <td>@lang("sale.total") : {{intval($TraSeLine_return)}} </td>
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
                    @component("components.widget",["title"=>__("recieved.all_delivered")])
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
                                                @forelse ($DeliveredWrong as $RecWrong)
                                    
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
                                            @forelse ($DeliveredWrong as $RecWrong)
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
                                                <td>@lang("sale.total") : {{intval($TraSeLine)}} </td>
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