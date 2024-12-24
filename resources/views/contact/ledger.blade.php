	<!-- app css -->
	@if(!empty($for_pdf))
		<link rel="stylesheet" href="{{ asset('css/app.css?v='.$asset_v) }}">
	@endif
	<div class="col-md-12 col-sm-12 @if(!empty($for_pdf)) width-100 align-right @endif">
			<p class="text-right align-right"><strong>{{$contact->business->name}}</strong><br>{!! $contact->business->business_address !!}</p>
	</div>
	<div class="col-md-6 col-sm-6 col-xs-6 @if(!empty($for_pdf)) width-50 f-left @endif">
		<p class="blue-heading p-4 width-50">@lang('lang_v1.to'):</p>
		<p><strong>{{$contact->name}}</strong><br> {!! $contact->contact_address !!} @if(!empty($contact->email)) <br>@lang('business.email'): {{$contact->email}} @endif
		<br>@lang('contact.mobile'): {{$contact->mobile}}
		@if(!empty($contact->tax_number)) <br>@lang('contact.tax_no'): {{$contact->tax_number}} @endif
	</p>
	</div>
	<div class="col-md-6 col-sm-6 col-xs-6 text-right align-right @if(!empty($for_pdf)) width-50 f-left @endif">
		<h3 class="mb-0 blue-heading p-4">@lang('lang_v1.account_summary')</h3>
		<small>{{$ledger_details['start_date']}} @lang('lang_v1.to') {{$ledger_details['end_date']}}</small>
		<hr>
		<table class="table table-condensed text-left align-left no-border @if(!empty($for_pdf)) table-pdf @endif">
			<tr>
				<td>@lang('lang_v1.opening_balance')</td>
				<td class="align-right font_number">@format_currency($ledger_details['beginning_balance'])</td>
			</tr>
		<?php $diff =  0; ?>
		@if( $contact->type == 'supplier' || $contact->type == 'both')
			<tr>
				<td>@lang('report.total_purchase')</td> 
				<?php
				$pr_amount  =  \App\AccountTransaction::whereHas('transaction',function($query) use( $contact){
												$query->where('contact_id',$contact->id);
												$query->whereIn('type',['purchase','purchase_return']);
										})->whereHas('account',function($query) use( $contact){
												$query->where('contact_id',$contact->id);
										})->where('type','credit')->where("note","!=","refund Collect")
										->sum("amount");
				$pr_payed   =  \App\AccountTransaction::whereHas('account',function($query) use( $contact){
													$query->where('contact_id',$contact->id);
												})
												->where('type','debit')
												->whereNull("for_repeat")
												->whereNull("id_delete")
												->sum('amount');
				
				
				$diff       =   $pr_payed - $pr_amount;
								?>
				<td class="align-right font_number">@format_currency($pr_amount)</td>
			</tr>
			<tr>
			
				<td>@lang('sale.total_paid')</td>
				<td class="align-right font_number">@format_currency($pr_payed)</td>
			</tr>
			<tr>
				<td>@lang('lang_v1.advance_balance')</td>
				{{-- $ledger_details['total_purchase']-$ledger_details['total_credit'] --}}
				<td class="align-right font_number"> @format_currency(($diff > 0)?$diff:0 ) </td>
			</tr>
			<tr>
				<td><strong>@lang('lang_v1.balance_due')</strong></td>
				<td class="align-right font_number">@format_currency(($diff*-1 > 0)?abs($diff):0 ) </td>
			</tr>
		@endif
		@if( $contact->type == 'customer' || $contact->type == 'both')
						<?php
						$pr_amount  =  \App\AccountTransaction::whereHas('transaction',function($query) use( $contact){
											$query->where('contact_id',$contact->id);
											$query->whereIn('type',['sale','sell_return']);
											$query->where('note',"!=",'Add Payment');
									})->whereHas('account',function($query) use( $contact){
											$query->where('contact_id',$contact->id);
								})->where('type','debit')->where("note","!=","refund Collect")->whereNotNull("transaction_id")->sum('amount'); 
						$pr_payed  =  \App\AccountTransaction::whereHas('account',function($query) use( $contact){
												$query->where('contact_id',$contact->id);
											})->where('type','credit')->whereNull("for_repeat")->whereNull("id_delete")->sum('amount'); 
						$diff       =  $pr_payed -  $pr_amount  ;
					?>
			<tr>
				<td>@lang('lang_v1.total_invoice')</td>
				<td class="align-right font_number">@format_currency($pr_amount)</td>
			</tr>
			<tr>
				<td>@lang('sale.total_received')</td>
				<td class="align-right font_number">@format_currency($pr_payed )   </td>
			</tr>
			<tr>
				<td>@lang('lang_v1.advance_balance')</td>
				{{-- $ledger_details['total_purchase']-$ledger_details['total_credit'] --}}
				<td class="align-right font_number"> @format_currency(($diff > 0)?$diff:0 ) </td>
			</tr>
			<tr>
				<td><strong>@lang('lang_v1.balance_due')</strong></td>
				<td class="align-right font_number">@format_currency(($diff*-1 > 0)?abs($diff):0 )</td>
			</tr>
		@endif
	
		
		
		</table>
	</div>

	<div class="col-md-12 col-sm-12 @if(!empty($for_pdf)) width-100 @endif">
		<p class="text-center" style="text-align: center;"><strong>@lang('lang_v1.ledger_table_heading', ['start_date' => $ledger_details['start_date'], 'end_date' => $ledger_details['end_date']])</strong></p>
		<div class="table-responsive">
		<table class="table table-striped @if(!empty($for_pdf)) table-pdf td-border @endif" id="ledger_table">
			<thead>
				<tr class="row-border blue-heading">
					<th width="18%" class="text-center">@lang('lang_v1.date')</th>
					<th width="9%" class="text-center">@lang('purchase.Bill No.')</th>
					<th width="9%" class="text-center">@lang('purchase.ref_no')</th>
					<th width="8%" class="text-center">@lang('lang_v1.type')</th>
					{{-- <th width="10%" class="text-center hide">@lang('sale.location')</th> --}}
					{{-- <th width="5%" class="text-center hide">@lang('sale.payment_status')</th> --}}
					{{-- <th width="10%" class="text-center hide">@lang('sale.total')</th> --}}
					<th width="10%" class="text-center">@lang('account.debit')</th>
					<th width="10%" class="text-center">@lang('account.credit')</th>
					{{-- <th width="5%" class="text-center hide">@lang('lang_v1.payment_method')</th> --}}
					<th width="15%" class="text-center">@lang('lang_v1.note')</th>
					<th width="15%" class="text-center">@lang( 'lang_v1.balance' )</th>
				</tr>
			</thead>
			<tbody style="text-align:center !important">
				@php  
					$debit   = 0 ;
					$credit  = 0 ;
					$balance = 0;
					 
					 
				@endphp  
			 
				@foreach($ledger_details['ledger'] as $data)
 					@if($data['transaction_id'] != null)
						<?php  $tr      =  \App\Transaction::find($data['transaction_id']); ?>
						@if($tr)
							<?php  
								if($tr->type == "purchase" || $tr->type == "purchase_return" || $tr->type == "sale" || $tr->type == "sell_return"){
									$ship         =  \App\Models\AdditionalShipping::where("transaction_id",$data['transaction_id'])->where("type",0)->first();
									$shipItems    =  \App\Models\AdditionalShippingItem::join("additional_shippings as ad" , "additional_shipping_items.additional_shipping_id","=","ad.id")
																						->where("contact_id",$contact->id)
																						->where("ad.type",0)
																						->where("ad.transaction_id",$tr->id)
																						->sum('total');
								}
							?>
							@if($tr->status == 'received' || $tr->status == 'final' || $tr->status == 'delivered' )
								@if($data['type'] != "Payment")
								
										<tr  @if(!empty($for_pdf) && $loop->iteration % 2 == 0) class="odd" @endif>
											<td class="row-border text-center" style="text-align:center !important;">{{$data['date']}}  </td>
												@if($data['transaction_id']!=null)
													@if($contact->type == 'customer' || $contact->type == 'both')
													
														@if($data['type']=="Payment")
															<td></td>
															<td><button type="button" class="btn btn-link btn-modal"
																			data-href="{{action('TransactionPaymentController@viewPayment', [$data['transaction_id']])}}" data-container=".view_modal">
																{{$data['ref_no']}}</button></td>
														@else
															<td></td>
															<td><button type="button" class="btn btn-link btn-modal"
																			data-href="{{action('SellController@show', [$data['transaction_id']])}}" data-container=".view_modal">
																{{$data['ref_no']}}</button></td>
														@endif
													
													@else
													
														@if($data['type']=="Payment")
															<td></td>
															<td><button type="button" class="btn btn-link btn-modal"
																			data-href="{{action('TransactionPaymentController@viewPayment', [$data['transaction_id']])}}" data-container=".view_modal">
																{{$data['ref_no']}}</button></td>
														@else
														<td>{{$tr->sup_refe}}</td>
														<td><button type="button" class="btn btn-link btn-modal"
																			data-href="{{action('PurchaseController@show', [$data['transaction_id']])}}" data-container=".view_modal">
																{{$data['ref_no']}}</button></td>
														@endif
														
													@endif
												@else
													<td>{{$data['ref_no']}}</td>
												@endif
												
											<td>{{$data['type']}}</td>
											{{-- <td class="hide">{{$data['location']}}</td> --}}
											{{-- <td  class="hide">{{$data['payment_status']}}</td> --}}
											@if($data['type'] == "Purchase" || $data['type'] == "Sales" || $data['type'] == "Purchase Return" )
												{{-- <td class="ws-nowrap align-right hide">@if($data['total'] !== '') @format_currency($data['total'] - $shipItems) @endif</td> --}}
												<td class="ws-nowrap align-right font_number">
													@if($data['debit'] != '' )
													@format_currency($data['debit'] - $shipItems)
														@php
															$debit  += doubleVal($data['debit']) - $shipItems;
															$balance -=   (doubleVal($data['debit']) - $shipItems)
														@endphp
													@endif
												</td>
												<td class="ws-nowrap align-right font_number">
													@if($data['credit'] != '')
														@format_currency($data['credit']- $shipItems) 
														@php   $credit += doubleVal($data['credit']) - $shipItems; 
																$balance +=   (doubleVal($data['credit']) - $shipItems)
														@endphp
													@endif</td>
											@else
												{{-- <td class="ws-nowrap align-right hide">@if($data['total']  !== '') @format_currency($data['total']) @endif</td> --}}
												<td class="ws-nowrap align-right font_number">
													@if($data['debit']  != '' )
													@format_currency($data['debit']) 
													@php 
														$debit  += doubleVal($data['debit']);
														$balance -=   (doubleVal($data['debit']))
													@endphp 
													@endif
												</td>
												<td class="ws-nowrap align-right font_number">
													@if($data['credit'] != '')
														@format_currency($data['credit']) 
														@php
															$credit += doubleVal($data['credit']);
															$balance +=   (doubleVal($data['credit']) )
														@endphp
													@endif
												</td>
												
													
											@endif
											{{-- <td class="hide">{{$data['payment_method']}}</td> --}}
											<td>{!! $data['others'] !!}</td>
											<td class="font_number">{{  ($balance>0) ? number_format($balance,2)  . " / Credit" :  number_format($balance*-1,2) . " / Debit"  }}</td>
										</tr>
								
								@endif
							@endif
							
						@endif
					@elseif($data["type"] == "Cheque")
						@php   $cheque = \App\Models\Check::where("id",$data["check_id"])->first();  @endphp
						@if($cheque->status != 2)
							<tr @if(!empty($for_pdf) && $loop->iteration % 2 == 0) class="odd" @endif>
								<td class="row-border" style="text-align:center !important;">{{$data['date']}}  </td>
									@if($data['transaction_id']!=null)
										@if($contact->type == 'customer' || $contact->type == 'both')
										
											@if($data['type']=="Payment")
												<td></td>
												<td><button type="button" class="btn btn-link btn-modal"
																data-href="{{action('TransactionPaymentController@viewPayment', [$data['transaction_id']])}}" data-container=".view_modal">
													{{$data['ref_no']}}</button></td>
											@else
											<td></td>
												<td><button type="button" class="btn btn-link btn-modal"
																data-href="{{action('SellController@show', [$data['transaction_id']])}}" data-container=".view_modal">
													{{$data['ref_no']}}</button></td>
											@endif
										
										@else
											@if($data['type']=="Payment")
											    <td></td>
												<td><button type="button" class="btn btn-link btn-modal"
																data-href="{{action('TransactionPaymentController@viewPayment', [$data['transaction_id']])}}" data-container=".view_modal">
													{{$data['ref_no']}}</button></td>
											@else
											<td></td>
												<td><button type="button" class="btn btn-link btn-modal"
																data-href="{{action('PurchaseController@show', [$data['transaction_id']])}}" data-container=".view_modal">
													{{$data['ref_no']}}</button></td>
											@endif
										@endif
									
									@else
										<?php  $cheque  =  \App\models\Check::where("ref_no",$data['ref_no'])->first(); ?>
										<td>{{$cheque->cheque_no}}</td>
										<td>
											<button type="button" class="btn btn-link btn-modal"
																	data-href="{{\URL::to('/cheque/show', [$cheque->id])}}" data-container=".view_modal">
														{{$data['ref_no']}}</button>
										</td>
									@endif
									@php 
										$debit  += doubleVal($data['debit']);
										$credit += doubleVal($data['credit']);
										($data["debit"] == 0 || $data["debit"] == "")? $balance +=   (doubleVal($data['credit'])) : $balance -=   (doubleVal($data['debit']));
 									@endphp
									<td>{{$data['type']}}</td>
									{{-- <td  class="hide">{{$data['location']}}</td> --}}
									{{-- <td  class="hide">{{$data['payment_status']}}</td> --}}
									{{-- <td class="ws-nowrap align-right hide">@if($data['total'] !== '') @format_currency($data['total']) @endif</td> --}}
									
										<td class="ws-nowrap align-right font_number">@if($data['debit'] != '' ) @format_currency($data['debit']) @endif</td>
										<td class="ws-nowrap align-right font_number">@if($data['credit'] != '') @format_currency($data['credit']) @endif</td>
										{{-- <td class="hide">{{$data['payment_method']}}</td> --}}
										<td >{!! $data['others'] !!}</td>
										<td class="font_number">{{  ($balance>0) ? $balance . " / Credit" :  $balance*-1  . " / Debit"  }}</td>
							</tr>
						@else
							<tr @if(!empty($for_pdf) && $loop->iteration % 2 == 0) class="odd" @endif>
								<td class="row-border" style="text-align:center !important;">{{$data['date']}}  </td>
									@if($data['transaction_id']!=null)
										@if($contact->type == 'customer' || $contact->type == 'both')
										
											@if($data['type']=="Payment")
												<td></td>
												<td><button type="button" class="btn btn-link btn-modal"
																data-href="{{action('TransactionPaymentController@viewPayment', [$data['transaction_id']])}}" data-container=".view_modal">
													{{$data['ref_no']}}</button></td>
											@else
											<td></td>
												<td><button type="button" class="btn btn-link btn-modal"
																data-href="{{action('SellController@show', [$data['transaction_id']])}}" data-container=".view_modal">
													{{$data['ref_no']}}</button></td>
											@endif
										
										@else
											@if($data['type']=="Payment")
												<td></td>
												<td><button type="button" class="btn btn-link btn-modal"
																data-href="{{action('TransactionPaymentController@viewPayment', [$data['transaction_id']])}}" data-container=".view_modal">
													{{$data['ref_no']}}</button></td>
											@else
											<td></td>
												<td><button type="button" class="btn btn-link btn-modal"
																data-href="{{action('PurchaseController@show', [$data['transaction_id']])}}" data-container=".view_modal">
													{{$data['ref_no']}}</button></td>
											@endif
										@endif
									
									@else
										<?php  $cheque  =  \App\models\Check::where("ref_no",$data['ref_no'])->first(); ?>
										<td>{{$cheque->cheque_no}}</td>
										<td>
											<button type="button" class="btn btn-link btn-modal"
																	data-href="{{\URL::to('/cheque/show', [$cheque->id])}}" data-container=".view_modal">
														{{$data['ref_no']}}</button>
										</td>
									@endif
									@php 
										$debit  += doubleVal($data['debit']);
										$credit += doubleVal($data['credit']);
										($data["debit"] == 0)? $balance +=   (doubleVal($data['credit'])) : $balance -=   (doubleVal($data['debit']));
									@endphp
									<td>{{$data['type']}}</td>
									{{-- <td  class="hide">{{$data['location']}}</td> --}}
									{{-- <td  class="hide">{{$data['payment_status']}}</td> --}}
									{{-- <td class="ws-nowrap align-right hide">@if($data['total'] !== '') @format_currency($data['total']) @endif</td> --}}
									
										<td class="ws-nowrap align-right font_number"> </td>
										<td class="ws-nowrap align-right font_number"> @format_currency($cheque->amount)  </td>
 										{{-- <td class="hide">{{$data['payment_method']}}</td> --}}
										<td >{!! $data['others'] !!}</td>
										<td class="font_number">{{  ($balance>0) ? $balance . " / Credit" :  $balance*-1  . " / Debit"  }}</td>
							</tr>
							<tr @if(!empty($for_pdf) && $loop->iteration % 2 == 0) class="odd" @endif>
								<td class="row-border" style="text-align:center !important;">{{$data['date']}}  </td>
									@if($data['transaction_id']!=null)
										@if($contact->type == 'customer' || $contact->type == 'both')
										
											@if($data['type']=="Payment")
												<td></td>
												<td><button type="button" class="btn btn-link btn-modal"
																data-href="{{action('TransactionPaymentController@viewPayment', [$data['transaction_id']])}}" data-container=".view_modal">
													{{$data['ref_no']}}</button></td>
											@else
											<td></td>
												<td><button type="button" class="btn btn-link btn-modal"
																data-href="{{action('SellController@show', [$data['transaction_id']])}}" data-container=".view_modal">
													{{$data['ref_no']}}</button></td>
											@endif
										
										@else
											@if($data['type']=="Payment")
												<td></td>
												<td><button type="button" class="btn btn-link btn-modal"
																data-href="{{action('TransactionPaymentController@viewPayment', [$data['transaction_id']])}}" data-container=".view_modal">
													{{$data['ref_no']}}</button></td>
											@else
											<td></td>
												<td><button type="button" class="btn btn-link btn-modal"
																data-href="{{action('PurchaseController@show', [$data['transaction_id']])}}" data-container=".view_modal">
													{{$data['ref_no']}}</button></td>
											@endif
										@endif
									
									@else
										<?php  $cheque  =  \App\models\Check::where("ref_no",$data['ref_no'])->first(); ?>
										<td>{{$cheque->cheque_no}}</td>
										<td>
											<button type="button" class="btn btn-link btn-modal"
																	data-href="{{\URL::to('/cheque/show', [$cheque->id])}}" data-container=".view_modal">
														{{$data['ref_no']}}</button>
										</td>
									@endif
									@php 
										$debit  += doubleVal($data['debit']);
										$credit += doubleVal($data['credit']);
										($data["debit"] == 0)? $balance +=   (doubleVal($data['credit'])) : $balance -=   (doubleVal($data['debit']));
									@endphp
									<td>{{$data['type']}}</td>
									{{-- <td  class="hide">{{$data['location']}}</td> --}}
									{{-- <td  class="hide">{{$data['payment_status']}}</td> --}}
									{{-- <td class="ws-nowrap align-right hide">@if($data['total'] !== '') @format_currency($data['total']) @endif</td> --}}
									
										<td class="ws-nowrap align-right font_number"> @format_currency($cheque->amount)  </td>
										<td class="ws-nowrap align-right font_number">  </td>
										{{-- <td class="hide">{{$data['payment_method']}}</td> --}}
										<td >{!! $data['others'] !!}</td>
										<td class="font_number">{{  ($balance>0) ? $balance . " / Credit" :  $balance*-1  . " / Debit"  }}</td>
							</tr>
						@endif
					@elseif($data["type"] == "Voucher")
					
							<tr @if(!empty($for_pdf) && $loop->iteration % 2 == 0) class="odd" @endif>
									<td class="row-border" style="text-align:center !important;">{{$data['date']}}  </td>
									<?php    $voucher  =  \App\Models\PaymentVoucher::where("ref_no",$data['ref_no'])->first(); ?>
									<td></td>
									<td>
										<button type="button" class="btn btn-link btn-modal"
																data-href="{{\URL::to('/payment-voucher/show', [$voucher->id])}}" data-container=".view_modal">
													{{$data['ref_no']}}
										</button>
									</td>
									@php 
										$debit  += doubleVal($data['debit']);
										$credit += doubleVal($data['credit']);
										($data["debit"] == 0 || $data["debit"] == "")? $balance +=   (doubleVal($data['credit'])) : $balance -=   (doubleVal($data['debit']));
 									@endphp
									<td>{{$data['type']}}</td>
									{{-- <td  class="hide">{{$data['location']}}</td> --}}
									{{-- <td  class="hide">{{$data['payment_status']}}</td> --}}
									{{-- <td class="ws-nowrap align-right hide">@if($data['total']  !=='' ) @format_currency( $data['total'] ) @endif</td> --}}
									<td class="ws-nowrap align-right font_number">@if($data['debit']  != '' ) @format_currency( $data['debit'] ) @endif</td>
									<td class="ws-nowrap align-right font_number">@if($data['credit'] != '' ) @format_currency( $data['credit']) @endif</td>
									{{-- <td class="hide">{{$data['payment_method']}}</td> --}}
									<td>{!! $data['others'] !!}</td>
									<td class="font_number">{{  ($balance>0) ? $balance . " / Credit" : $balance*-1 . " / Debit"  }}</td>
							</tr>
						
					@elseif($data["type"] == "Journal-Voucher")
						<tr @if(!empty($for_pdf) && $loop->iteration % 2 == 0) class="odd" @endif>
							<td class="row-border" style="text-align:center !important;">{{$data['date']}}  </td>
							<?php    $voucher  =  \App\Models\DailyPayment::where("ref_no",$data['ref_no'])->first(); ?>
							<td></td>
							<td><button type="button" class="btn btn-link btn-modal"
												data-href="{{\URL::to('/daily-payment/show', [$voucher->id])}}" data-container=".view_modal">
									{{$data['ref_no']}}
								</button>
							</td>
							@php 
								$debit  += doubleVal($data['debit']);
								$credit += doubleVal($data['credit']);
								($data["debit"] == 0 || $data["debit"] == "")? $balance +=   (doubleVal($data['credit'])) : $balance -=   (doubleVal($data['debit']));
							@endphp
							<td>{{$data['type']}}</td>
							{{-- <td  class="hide">{{$data['location']}}</td> --}}
							{{-- <td  class="hide">{{$data['payment_status']}}</td> --}}
							{{-- <td class="ws-nowrap align-right hide">@if($data['total'] !== '') @format_currency($data['total']) @endif</td> --}}
							<td class="ws-nowrap align-right font_number">@if($data['debit'] != '' ) @format_currency($data['debit']) @endif</td>
							<td class="ws-nowrap align-right font_number">@if($data['credit'] != '') @format_currency($data['credit']) @endif</td>
							{{-- <td class="hide">{{$data['payment_method']}}</td> --}}
							<td>{!! $data['others'] !!}</td>
							<td class="font_number">{{  ($balance>0) ? $balance . " / Credit" : $balance*-1 . " / Debit"  }}</td>
						</tr>
					@elseif($data["type"] == "Expenses")
						<tr @if(!empty($for_pdf) && $loop->iteration % 2 == 0) class="odd" @endif>
							<td class="row-border" style="text-align:center !important;">{{$data['date']}}  </td>
							<td></td>
							<td><button type="button" class="btn btn-link btn-modal"
												data-href="{{action('PurchaseController@show', [$data['transaction_id']])}}" data-container=".view_modal">
									{{$data['ref_no']}}</button></td>
							@php 
								$debit  += doubleVal($data['debit']);
								$credit += doubleVal($data['credit']);
								($data["debit"] == 0 || $data["debit"] == "")? $balance +=   (doubleVal($data['credit'])) : $balance -=   (doubleVal($data['debit']));
							@endphp
							<td>{{$data['type']}}</td>
							{{-- <td  class="hide">{{$data['location']}}</td> --}}
							{{-- <td  class="hide">{{$data['payment_status']}}</td> --}}
							{{-- <td class="ws-nowrap align-right hide">@if($data['total'] !== '') @format_currency($data['total']) @endif</td> --}}
							<td class="ws-nowrap align-right font_number">@if($data['debit'] != '' ) @format_currency($data['debit']) @endif</td>
							<td class="ws-nowrap align-right font_number">@if($data['credit'] != '') @format_currency($data['credit']) @endif</td>
							{{-- <td class="hide">{{$data['payment_method']}}</td> --}}
							<td>{!! $data['others'] !!}</td>
							<td  class="font_number">{{  ($balance>0) ? $balance . " / Credit" : $balance*-1 . " / Debit"  }}</td>
						</tr>
					@endif
					
				@endforeach
			</tbody>
			<tfoot>
				@php
					if($balance > 0 ){
						$type = " / Credit" ;
					}elseif ($balance < 0){
						$type = " / Debit" ;
					}else{
						$type = "" ;
					}   
					($balance>0)?$balance:$balance = $balance*-1;
				@endphp
				<tr class="row-border blue-heading">
					<td colspan="4"></td>
					<td class="font_number">@format_currency($debit)</td>
					<td class="font_number">@format_currency($credit)</td>
					<td ></td>
					<td class="font_number">@format_currency($balance)  {{ $type }}</td>
				</tr>
			</tfoot>
		</table>
		</div>
	</div>