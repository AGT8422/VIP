<div class="modal-dialog modal-xl" role="document" style="font-size:medium !important;">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close no-print" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"  id="modalTitle"> @lang('lang_v1.opening_stock') ( {{$Transaction->ref_no }} )</h4>
        </div>
        <div class="modal-body">
           
                <div class="row">
                    <div class="col-md-12 text-left">
                        <b>@lang('messages.date') :</b>
                        <span>{{$Transaction->transaction_date?$Transaction->transaction_date:$Transaction->created_at}}</span>
                        <!--<br>-->
                        <!--<b>@lang('product.product_name') :</b>-->
                        <!--<span>{{$OpenQuantity->product?$OpenQuantity->product->name:'-------'}}</span>-->
                        <!--<br>-->
                        <!--<b>@lang('warehouse.nameW') :</b>-->
                        <!--<span>{{$OpenQuantity->store?$OpenQuantity->store->name:'-------'}}</span>-->
                        <!--<br>-->
                        
                    </div>
            
            </div>
           
        </div>
        <div class="modal-body">
           
                <div class="row">
                     
                    <div class="col-md-12 text-left">
                    <table class="table">
                            <thead>
                                <tr>
                                    
                                    <th>@lang('product.product_name')</th> 
                                    <th>@lang('purchase.qty')</th>
                                    <th>@lang('purchase.price')</th>
                                    <th>@lang('warehouse.nameW')</th> 
                                    <th>@lang('messages.date')</th> 
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                 
                                    $qty = 0;
                                    $prc_ = 0;
                                @endphp
                                @foreach($OpenQuantity_ as $op_)
                                @php
                                   $qty  =  $qty  +   $op_->quantity ; 
                                   $prc_ =  $prc_ + ( $op_->quantity * $op_->price ) ; 
                                @endphp
                                <tr>
                                    <td>
                                        {{$op_->product->name}}
                                        <br>
                                        <div class="btn-group"><button type="button" class="btn btn-info dropdown-toggle btn-xs no-print" data-toggle="dropdown" aria-expanded="false">@lang("messages.actions")<span class="caret"></span><span class="sr-only">Toggle Dropdown</span></button><ul class="dropdown-menu dropdown-menu-left" role="menu">
                                            <li><a data-href="{{action('ProductController@view', [ $op_->product->id])}}" class="btn-modal" data-container=".view_modal"><i class="fa fa-eye"></i>@lang("messages.view")</a></li>
                                            <li><a href="{{action('ProductController@edit', [ $op_->product->id])}}"><i class="glyphicon glyphicon-edit"></i>@lang("messages.edit")</a></li>
                                            @php 
                                                    $path  = '/item-move' . "/". $op_->product->id ;
                                                    if($op_->variation_id != null){
                                                        $path .= "?variation_id=" .$op_->variation_id ; 
                                                    } 
                                            @endphp
                                            <li><a href="{{\URL::to($path)}}"><i class="fas fa-history"></i>@lang("lang_v1.product_stock_history")</a></li>
                                            </ul>
                                            <button type="button" style="margin-left:10px" class="btn btn-primary btn-xs btn-modal no-print"   data-container=".view_modal" data-href="{{action('ProductController@viewStock', [$op_->product->id])}}">@lang('lang_v1.view_Stock')</button> 
                                            <button type="button" style="margin-left:10px" class="btn bg-yellow btn-xs btn-modal no-print" data-container=".view_modal" data-href="{{action('ProductController@viewUnrecieved', [$op_->product->id])}}">@lang('recieved.should_recieved')</button> 
                                        </div>
                                    </td> 
                                    <td>{{$op_->quantity}}</td> 
                                    <td>
                                        @if(auth()->user()->hasRole('Admin#' . session('business.id')) ||  auth()->user()->can('product.avarage_cost'))
                                                @format_currency($op_->price)
                                        @else
                                                {{ "--" }}
                                        @endif
                                    </td> 
                                    <td>{{$op_->store->name}}</td>
                                    <td>{{($op_->date)?$op_->date:$op_->created_at}}</td> 
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td></td>
                                    
                                    <td colspan="5">
                                        <b>@lang('purchase.qty') :</b>
                                        <span>{{ $qty}}</span>
                                        <b style="margin-left:100px">@lang('purchase.amount') :</b>
                                        <span style="margin-left:10px">@format_currency($prc_)</span>
                                      
                                    </td>
                                </tr>
                            </tfoot>
                    </table>
            </div>
           
        </div>
        <div class="modal-footer">
            @can("warehouse.Edit")
            <a class="btn bg-yellow  " href="/products/Opening_product/edit/{{$id}}">@lang("messages.edit")</a>
            @endcan
            <button type="button" class="btn btn-primary no-print" 
                aria-label="Print" 
                    onclick="$(this).closest('div.modal').printThis();">
                <i class="fa fa-print"></i> @lang( 'messages.print' )
                </button>
            <button type="button" class="btn btn-default no-print" data-dismiss="modal">@lang( 'messages.close' )</button>
        </div>
    </div>
</div>
 
 
            
 