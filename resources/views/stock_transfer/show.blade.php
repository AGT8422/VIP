
<div class="modal-dialog modal-xl" role="document">
	<div class="modal-content">
		<div class="modal-header">
		    <button type="button" class="close no-print" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		    <h4 class="modal-title" id="modalTitle"> @lang('lang_v1.stock_transfer_details') (<b>@lang('purchase.ref_no'):</b> #{{ $sell_transfer->ref_no }})
		    </h4>
		</div>
		<div class="modal-body">
				<div class="row invoice-info">
				  <div class="col-sm-4 invoice-col">
				    @lang('warehouse.nameWFrom'):
				    <address>
				      <strong>{{ $location_details['sell']->warehouse->name }}</strong>
				      
				      {{-- @if(!empty($location_details['sell']->landmark))
				        <br>{{$location_details['sell']->landmark}}
				      @endif --}}

				      {{-- @if(!empty($location_details['sell']->city) || !empty($location_details['sell']->state) || !empty($location_details['sell']->country))
				        <br>{{implode(',', array_filter([$location_details['sell']->city, $location_details['sell']->state, $location_details['sell']->country]))}}
				      @endif

				      @if(!empty($sell_transfer->contact->tax_number))
				        <br>@lang('contact.tax_no'): {{$sell_transfer->contact->tax_number}}
				      @endif

				      @if(!empty($location_details['sell']->mobile))
				        <br>@lang('contact.mobile'): {{$location_details['sell']->mobile}}
				      @endif
				      @if(!empty($location_details['sell']->email))
				        <br>Email: {{$location_details['sell']->email}}
				      @endif --}}
				    </address>
				  </div>

				  <div class="col-md-4 invoice-col">
				    @lang('warehouse.nameWTo'):
				    <address>
				      <strong>{{ $location_details['purchase']->warehouse_to->name  }}</strong>
{{-- 				      
				      @if(!empty($location_details['purchase']->landmark))
				        <br>{{$location_details['purchase']->landmark}}
				      @endif

				      @if(!empty($location_details['purchase']->city) || !empty($location_details['purchase']->state) || !empty($location_details['purchase']->country))
				        <br>{{implode(',', array_filter([$location_details['purchase']->city, $location_details['purchase']->state, $location_details['purchase']->country]))}}
				      @endif

				      @if(!empty($sell_transfer->contact->tax_number))
				        <br>@lang('contact.tax_no'): {{$sell_transfer->contact->tax_number}}
				      @endif

				      @if(!empty($location_details['purchase']->mobile))
				        <br>@lang('contact.mobile'): {{$location_details['purchase']->mobile}}
				      @endif
				      @if(!empty($location_details['purchase']->email))
				        <br>Email: {{$location_details['purchase']->email}}
				      @endif --}}
				    </address>
				  </div>
				
				  <div class="col-sm-4 invoice-col">
				    <b>@lang('purchase.ref_no'):</b> #{{ $sell_transfer->ref_no }}<br/>
				    <b>@lang('messages.date'):</b> {{ @format_date($sell_transfer->transaction_date) }}<br/>
				    <b>@lang('sale.status'):</b> {{$statuses[$sell_transfer->status] ?? ''}}
				  </div>
				</div>

				<br>
				<div class="row">
				  <div class="col-xs-12">
				    <div class="table-responsive">
				      <table class="table bg-gray">
				        <tr class="bg-green">
				          <th>#</th>
				          <th>@lang('sale.product')</th>
				          <th>@lang('sale.qty')</th>
				          {{-- <th>@lang('sale.subtotal')</th> --}}
				        </tr>
				        @php 
				          $total = 0.00;
				        @endphp
				        @foreach($sell_transfer->sell_lines as $sell_lines)
				          <tr>
				            <td>{{ $loop->iteration }}</td>
				            <td>
								<a  target="_blank" href="/item-move/{{$sell_lines->product->id}}">
											{{ $sell_lines->product->name }}
								</a>
				               @if( $sell_lines->product->type == 'variable')
				                - {{ $sell_lines->variations->product_variation->name}}
				                - {{ $sell_lines->variations->name}}
				               @endif
				               @if($lot_n_exp_enabled && !empty($sell_lines->lot_details))
				                <br>
				                <strong>@lang('lang_v1.lot_n_expiry'):</strong> 
				                @if(!empty($sell_lines->lot_details->lot_number))
				                  {{$sell_lines->lot_details->lot_number}}
				                @endif
				                @if(!empty($sell_lines->lot_details->exp_date))
				                  - {{@format_date($sell_lines->lot_details->exp_date)}}
				                @endif
				               @endif
								<br>
							   <div class="btn-group"><button type="button" class="btn btn-info dropdown-toggle btn-xs no-print" data-toggle="dropdown" aria-expanded="false">@lang("messages.actions")<span class="caret"></span><span class="sr-only">Toggle Dropdown</span></button><ul class="dropdown-menu dropdown-menu-left" role="menu">
								<li><a data-href="{{action('ProductController@view', [ $sell_lines->product->id])}}" class="btn-modal" data-container=".products_modal"><i class="fa fa-eye"></i>@lang("messages.view")</a></li>
								<li><a href="{{action('ProductController@edit', [ $sell_lines->product->id])}}"><i class="glyphicon glyphicon-edit"></i>@lang("messages.edit")</a></li>
								<li><a href="{{action('ItemMoveController@index', [$sell_lines->product->id])}}"><i class="fas fa-history"></i>@lang("lang_v1.product_stock_history")</a></li>
								</ul>
								<button type="button" style="margin-left:10px" class="btn btn-primary btn-xs btn-modal no-print" id="view_s" data-container=".products_modal" data-href="{{action('ProductController@viewStock', [$sell_lines->product->id])}}">@lang('lang_v1.view_Stock')</button> 
								<button type="button" style="margin-left:10px" class="btn btn-second btn-xs btn-modal no-print" data-container=".products_modal" data-href="{{action('ProductController@viewUnrecieved', [$sell_lines->product->id])}}">@lang('recieved.should_recieved')</button> 
							</div>
				            </td>
				            <td>{{ @format_quantity($sell_lines->quantity) }} {{$sell_lines->product->unit->short_name ?? ""}}</td>
				            {{-- <td>
				              <span class="display_currency" data-currency_symbol="true">{{ $sell_lines->unit_price_inc_tax * $sell_lines->quantity }}</span>
				            </td> --}}
				          </tr>
				          @php 
				            $total += ($sell_lines->unit_price_inc_tax * $sell_lines->quantity);
				          @endphp
				        @endforeach
				      </table>
				    </div>
				  </div>
				</div>
				<br>
				{{-- <div class="row">
				  
				  <div class="col-xs-12 col-md-6 col-md-offset-6">
				    <div class="table-responsive">
				      <table class="table">
				        <tr>
				          <th>@lang('purchase.net_total_amount'): </th>
				          <td></td>
				          <td><span class="display_currency pull-right" data-currency_symbol="true">{{ $total }}</span></td>
				        </tr>
				        @if( !empty( $sell_transfer->shipping_charges ) )
				          <tr>
				            <th>@lang('purchase.additional_shipping_charges'):</th>
				            <td><b>(+)</b></td>
				            <td><span class="display_currency pull-right" data-currency_symbol="true">{{ $sell_transfer->shipping_charges }}</span></td>
				          </tr>
				        @endif
				        <tr>
				          <th>@lang('purchase.purchase_total'):</th>
				          <td></td>
				          <td><span class="display_currency pull-right" data-currency_symbol="true" >{{ $sell_transfer->final_total }}</span></td>
				        </tr>
				      </table>
				    </div>
				  </div>
				</div> --}}
				<div class="row">
				  <div class="col-sm-6">
				    <strong>@lang('purchase.additional_notes'):</strong><br>
				    <p class="well well-sm no-shadow bg-gray">
				      @if($sell_transfer->additional_notes)
				        {{ $sell_transfer->additional_notes }}
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
				<div class="row print_section">
				  <div class="col-xs-12">
				    <img class="center-block" src="data:image/png;base64,{{DNS1D::getBarcodePNG($sell_transfer->ref_no, 'C128', 2,30,array(39, 48, 54), true)}}">
				  </div>
				</div>
		</div>
		<div class="modal-footer">
			@php
				$tr = \App\Transaction::where("type","Stock_In")->where("ref_no",$sell_transfer->ref_no)->first(); 
			if(!empty($tr)){
				$id = $tr->id;
			}else{
				$id = $sell_transfer->id;
			}
			@endphp
			<a href="{{action("StockTransferController@edit", [$id])}}" class="btn bg-yellow btn-xs"><i class="fa fa-edit" aria-hidden="true"></i>@lang("messages.edit")</a>;

			<button type="button" class="btn btn-primary no-print" aria-label="Print" 
			onclick="$(this).closest('div.modal-content').printThis();"><i class="fa fa-print"></i> @lang( 'messages.print' )
			</button>
			<button type="button" class="btn btn-default no-print" data-dismiss="modal">@lang( 'messages.close' )</button>
		</div>
	</div>
</div>

