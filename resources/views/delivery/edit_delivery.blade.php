@extends('layouts.app')

@php
 
		$title = __('delivery.update_delivered');
@endphp

@section('title', $title)

@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>{{$title}}</h1>
</section>

<!-- Main content -->
<section class="content no-print">
	<input type="hidden" id="amount_rounding_method" value="{{$pos_settings['amount_rounding_method'] ?? ''}}">
	<input type="hidden" id="item_addition_method" value="{{$business_details->item_addition_method}}">
	@if(request()->return)
	{!! Form::open(['url' => 'delivery/update-return/'.app('request')->id, 'method' => 'post', 'id' => 'edit_sell_form', 'files' => true ]) !!}
	@else
		@if(request()->approved)
			{!! Form::open(['url' => 'delivery/update/'.app('request')->id.'?approved=1', 'method' => 'post', 'id' => 'edit_sell_form', 'files' => true ]) !!}
		@else
			{!! Form::open(['url' => 'delivery/update/'.app('request')->id, 'method' => 'post', 'id' => 'edit_sell_form', 'files' => true ]) !!}
		@endif
	@endif
	{{-- info --}}
	<div class="row">
		<div class="col-md-4">
		   <div class="well">
			  <strong> @lang("contact.customer")  </strong> : {{  $transaction->contact?$transaction->contact->name:' ' }}  <br> --
		   </div>
		</div>
		<div class="col-md-4">
		   <div class="well">
			  <strong>@lang("purchase.ref_no") :  </strong> {{ $transaction->invoice_no }} <br>
			  <strong>@lang("purchase.location") :</strong> {{  $transaction->location?$transaction->location->name:' '  }}
		   </div>
		</div>
		<div class="col-md-4">
		   <div class="well">
			  <strong>@lang('sale.total_amount') : </strong><span   data-currency_symbol="false"> {{  intval($transaction->sell_lines->sum('quantity')) }} </span><br>
			  <strong>@lang('lang_v1.note') : </strong> {{ strip_tags($transaction->additional_notes) }}
		   </div>
		</div>
	</div>
	{{-- delivery --}}
	<div class="row">
		<div class="col-md-12 col-sm-12">
			
			@if ($errors->any())
				<div class="alert alert-danger">
					<ul>
						@foreach ($errors->all() as $error)
							<li>{{ $error }}</li>
						@endforeach
					</ul>
				</div>
			@endif
			@if(session('yes'))
			   <div class="alert success alert-success">
				{{ session('yes') }}
			   </div>
			@endif



			@if(request()->return)  
				<input type="text" class="hide" id="return_type" value="return_sale">
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
					
									<tr  @if(($main-$total_) == 0) style="border:1px solid #f1f1f1;" @else @php $array_remain[] = $sell_line->product->id; @endphp style="border:1px solid #f1f1f1; background:#ff4d4d9a"@endif>
										<td class="hide">
										<input class="pro_id" value="{{$sell_line->product->id}}"/>
									</td>
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
				@if(($TrSeLine_return - $DelPrevious)>0)
					<div  class="modal-head"  >
						<button type="button" id="items_all" onclick="insert_item();" class="btn btn-primary all_item">@lang( 'messages.all_item' )</button>
					</div>
				@endif
				<input type="text" class="hide" id="trans_id" value="{{($tr->id)??0}}">
				@php
				$hide = "true";
				foreach ($DeliveredPrevious as  $value) {
					if($value->id){
						$hide = "false";
					}
				}
				$row = 0;
				$total = 0;
				$total_wrong = 0;
				$pre = "";
				$type = "base";
							
				@endphp
				@if(count($DeliveredPrevious)>0 || count($DeliveredWrong)>0)
					<div class="col-sm-12"  >
						<div class="form-group">
							<div class="row">
								<div class="col-md-12">
									<strong>@lang('lang_v1.old_delivered'):</strong> 
								</div>
							</div>
							<div class="row" >
								<div class="col-md-12">
									<div class="table-responsive">
										<table class="table table-recieve" id="prv_table" >
											<thead>
												<tr style="background:#f1f1f1;">
													<th>@lang('purchase.ref_no')</th>
													<th>@lang('messages.date')</th>
													<th>@lang('product.product_name')</th>
													<th>@lang('product.unit')</th>
													<th>@lang('purchase.qty_total')</th>
													<th>@lang('purchase.qty_current')</th>
													<th>@lang('warehouse.nameW')</th>
													<th>@lang('purchase.payment_note')</th>
													<th class="no-print">@lang('messages.actions')</th>
												</tr>
											</thead>
											<tbody class="top">
												<tr>
													<td>
														@php
															$del_rec     = \App\Models\TransactionDelivery::find(app('request')->id);
															if(!empty($del_rec)){
																$dates = $del_rec->date;
															}else{
																$dates = null;
															}
														@endphp
														{{-- delivery date  --}}
														<div class="col-sm-12">
															<div class="form-group">
																{!! Form::label('date_old', __('home.Date').':') !!}
																{!! Form::date('date_old',$dates, ['class' => 'form-control ' ]); !!}
															</div>
														</div>
													</td>
												</tr>
												@forelse ($DeliveredPrevious as $Recieved)
													@php
														$empty  = 0;
														$date   = Carbon::now();
														if(!in_array($Recieved->product_id,$product_list_all_return)) {$type = "other";}else{$type = "base";}
														$td     = \App\Models\TransactionDelivery::find($Recieved->transaction_recieveds_id);
														$data   = $td->document;
														$parsed = \App\Models\TransactionDelivery::get_string_between($data, '["', '"]');
													@endphp 

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

													<tr class="row_edit"  @if($type != "other") style="background:#f1f1f1; width:100% !important" @else style="background:#ff5b5b; width:100% !important" @endif>
														<td>{{ $Recieved->T_delivered->reciept_no; }}</td>
														<td>{!! Form::date('dates['.$Recieved->id.']',$Recieved->created_at, ['class' => 'form-control ' ]); !!} <input name="delivered_previouse_id[]" type="hidden" value="{{ $Recieved->id }}"> </td>
														<td>{{ $Recieved->product->name }}</td>
														<td>{{ $Recieved->product->unit->actual_name }}</td>
														<td>{{ $Recieved->total_qty }}</td>
														@php $max_qty = \App\Models\WarehouseInfo::where("product_id",$Recieved->product->id)->where("store_id",$Recieved->store_id)->sum("product_qty") + $Recieved->current_qty; $data_ = "data-max-$Recieved->store_id"   @endphp
														@php $all_qty = $TransactionSellLine->where("product_id",$Recieved->product->id)->sum("quantity");  @endphp
														<td>{{ Form::number('delivered_previouse_qty[]',$Recieved->current_qty,['class'=>'form-control item_qty','max'=>$max_qty,'min'=>0]) }}</td>
														<td>{{ Form::select('old_store_id[]',$childs,$Recieved->store_id,['class'=>'form-control o_store_id   st['.$Recieved->store_id.']' ,"data-max"=>$max_qty]) }}</td>
														<td></td>
														<td class="hide">
															<input id="max_item" disabled type="text" value="{{$max_qty}}">
														</td>
														<td class="hide">
															<input id="check_maxx" disabled type="text" value="{{$max_qty}}" data-all="{{$all_qty}}">
															<input id="product_id" disabled type="text" value="{{$Recieved->product->id}}">
															<input id="old_store"  disabled type="text" value="{{$Recieved->store_id}}">
															<input id="old_qty"    disabled type="text" value="{{$Recieved->current_qty}}">
														</td>
														<td> <a class="remove_row"> X  </a></td>
													</tr>
												@empty
													@forelse ($DeliveredWrong as $Recieved)
														@php $empty = 1;  @endphp
														<tr class="danger alert">
															<td>{{$Recieved->T_delivered->reciept_no; }}</td>
															<td>{{$Recieved->created_at}}
																<input name="wrong_ids[]" type="hidden" value="{{ $Recieved->id }}">
															</td>
														<td>{{  $Recieved->product_name   }}</td>
														<td>{{  $Recieved->unit?$Recieved->unit->actual_name:' ' }}</td>
														<td>{{  $TrSeLine_return   }}</td>
														<td>{{  $Recieved->current_qty }}</td>
														<td>{{  $Recieved->store?$Recieved->store->name:' ' }}</td>
														<td></td>
														<td> <a class="remove_row"> X </a></td>
														</tr>
													@empty @endforelse
												@endforelse
												@if($empty == 0)
													@forelse ($DeliveredWrong as $Recieved)
														<tr class="danger alert">
															<td>{{$Recieved->T_delivered->reciept_no; }}</td>
															<td>{{$Recieved->created_at}}
																<input name="wrong_ids[]" type="hidden" value="{{ $Recieved->id }}">
															</td>
														<td>{{  $Recieved->product_name   }}</td>
														<td>{{  $Recieved->unit?$Recieved->unit->actual_name:' ' }}</td>
														<td>{{  $TrSeLine_return   }}</td>
														<td>{{  $Recieved->current_qty }}</td>
														<td>{{  $Recieved->store?$Recieved->store->name:' ' }}</td>
														<td></td>
														<td> <a class="remove_row"> X </a></td>
														</tr>
													@empty @endforelse
												@endif
											</tbody>
											<tfoot>
												<tr class="bg-gray  font-17 footer-total" style="border:1px solid #f1f1f1;  ">
													<td>@lang('sale.total'): {{$TrSeLine_return}}</td>
													<td class="text-center " colspan="2"><strong></strong></td>
													<td>   </td>
													<td>Total Delivery : {{$DelPrevious}} </td>
													<td>@if($TrSeLine_return  - $DelPrevious < 0) Total remain : {{ 0 }} @else Total remain : {{ $TrSeLine_return - $DelPrevious}}@endif </td>
													{{-- <td></td> --}}
													<td>Wrong Delivery : {{ $DelWrong }}</td>
													<td></td>
													<td></td>
												</tr>
											</tfoot>

										</table>
									</div>
								</div>
							</div>
						</div>
					</div>
				@endif
				{!! Form::hidden('default_price_group', null, ['id' => 'default_price_group']) !!}
				<div class="clearfix"></div>
			@else
				<input type="text" class="hide" id="trans_id" value="{{($transaction->id)??0}}">

				@component('components.widget', ['class' => 'box-solid' , ])
					{!! Form::hidden('status_del','delivered', ['id' => 'status_del']); !!}
					<div class="clearfix"></div>
					<div class="col-sm-12">
						<div class="form-group">
							<div class="row">
								<div class="col-md-12">
									<strong>@lang('lang_v1.sale_bill'):</strong> 
								</div>
							</div>
							<div class="row">
								<div class="col-md-12">
									<div class="table-responsive">
										<table class="table table-recieve" >
											<tr style="background:#f1f1f1;">
												<th>@lang('messages.date')</th>
												<th>@lang('product.product_name')</th>
												<th>@lang('purchase.qty')</th>
												<th>@lang('purchase.delivery_status')</th>
												<th>@lang('warehouse.nameW')</th>
												<th>@lang("home.total delivered")</th>
                                				<th>@lang("purchase.total_remain")</th>
												<th>@lang('purchase.payment_note')</th>
											</tr>

											@php
												$status = "";
												if( ($transaction->status == "draft" && $transaction->sub_status == "proforma") || $transaction->status == "ApprovedQuotation" ){
													$status = "ApprovedQuotation";
												}else if($transaction->status == "draft" && $transaction->status == "quatation"){
													$status = "Quotation";
												}else if($transaction->status == "final"){
													$status = "final";
												}else{
													$status = $transaction->status;
												}
												$type = "";
												$total2 = $TrSeLine;
											@endphp
											@forelse ( $TransactionSellLine  as $payment)
												@php
													$total_ = \App\Models\DeliveredPrevious::where("transaction_id",$payment->transaction->id)->where("product_id",$payment->product->id)->sum("current_qty");    
													$main   = \App\TransactionSellLine::where("transaction_id",$payment->transaction->id)->where("product_id",$payment->product->id)->sum("quantity");    
												@endphp
												<tr  @if(($main-$total_) == 0) style="border:1px solid #f1f1f1;" @else @php $array_remain[] = $payment->product->id; @endphp style="border:1px solid #f1f1f1; background:#ff4d4d9a"@endif>
												<td class="hide">
													<input class="pro_id" value="{{$payment->product->id}}"/>
												</td>
												<td>{{$payment->created_at}}</td>
												<td>{{$payment->product->name}}</td>
												<td>{{$payment->quantity}}</td>
												<td>{{$status}}</td>
												<td>{{$payment->store->name}}</td>
												<td>
													{{ $total_ }}    
												</td>  
												<td>{{$main-$total_}}</td>
												<td>{!! Form::text('product_id_src', $payment->product->id ,["hidden",'id' => 'product_id_src']); !!}</td>
												<td hidden>{!! Form::text('product_id_str', $payment->store->id ,["hidden",'id' => 'product_id_str']); !!}</td>
												<td hidden>{!! Form::text('product_id_unit_value', $payment->product->unit->actual_name ,["hidden",'id' => 'product_id_unit_value']); !!}</td>
												<td hidden>{!! Form::text('product_id_qty', $payment->quantity ,["hidden",'id' => 'product_id_qty']); !!}</td>
												<td hidden>{!! Form::text('transaction_id', $transaction->id ,["hidden",'id' => 'transaction_id']); !!}</td> 
												</tr>
												
												
											@empty @endforelse
											
											<tfoot>
												<tr class="bg-gray  font-17 footer-total" style="border:1px solid #f1f1f1;  ">
													<td class="text-center " colspan="2"><strong>@lang('sale.total'):</strong></td>
													<td>{{$TrSeLine}}</td>
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
						</div>
					</div>
					@if(($TrSeLine - $DelPrevious) > 0)
						<div  class="modal-head"  >
							<button type="button" id="items_all" onclick="insert_item();" class="btn btn-primary all_item">@lang( 'messages.all_item' )</button>
						</div>
					@endif
					<h3>&nbsp;</h3>
					@php
						$hide = "true";
						foreach ($DeliveredPrevious as  $value) {
							if($value->id){
								$hide = "false";
							}
						}
						$row = 0;
						$total = 0;
						$total_wrong = 0;
						$pre = "";
						$type = "base";
									
					@endphp
					@if(count($DeliveredPrevious)>0 || count($DeliveredWrong)>0)
						<div class="col-sm-12"  >
							<div class="form-group">
								<div class="row">
									<div class="col-md-12">
										<strong>@lang('lang_v1.old_delivered'):</strong> 
									</div>
								</div>
								<div class="row" >
									<div class="col-md-12">
										<div class="table-responsive">
											<table class="table table-recieve" id="prv_table" >
												<thead>
													<tr style="background:#f1f1f1;">
														<th>@lang('purchase.ref_no')</th>
														<th>@lang('messages.date')</th>
														<th>@lang('product.product_name')</th>
														<th>@lang('product.unit')</th>
														<th>@lang('purchase.qty_total')</th>
														<th>@lang('purchase.qty_current')</th>
														<th>@lang('warehouse.nameW')</th>
														<th>@lang('purchase.payment_note')</th>
														<th class="no-print">@lang('messages.actions')</th>
													</tr>
												</thead>
												<tbody class="top">
													<tr>
														<td>
															@php
																$del_rec     = \App\Models\TransactionDelivery::find(app('request')->id);
																if(!empty($del_rec)){
																	$dates = $del_rec->date;
																}else{
																	$dates = null;
																}
															@endphp
															{{-- delivery date  --}}
															<div class="col-sm-12">
																<div class="form-group">
																	{!! Form::label('date_old', __('home.Date').':') !!}
																	{!! Form::date('date_old',$dates, ['class' => 'form-control ' ]); !!}
																</div>
															</div>
														</td>
													</tr>
													@forelse ($DeliveredPrevious as $Recieved)
														@php
															$empty  = 0;
															$date   = Carbon::now();
															if(!in_array($Recieved->product_id,$product_list_all)) {$type = "other";}else{$type = "base";}
															$td     = \App\Models\TransactionDelivery::find($Recieved->transaction_recieveds_id);
															$data   = $td->document;
															$parsed = \App\Models\TransactionDelivery::get_string_between($data, '["', '"]');
														@endphp 

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

														<tr class="row_edit"  @if($type != "other") style="background:#f1f1f1; width:100% !important" @else style="background:#ff5b5b; width:100% !important" @endif>
															<td>{{ $Recieved->T_delivered->reciept_no; }}</td>
															<td>{!! Form::date('dates['.$Recieved->id.']',$Recieved->created_at, ['class' => 'form-control ' ]); !!} <input name="delivered_previouse_id[]" type="hidden" value="{{ $Recieved->id }}"> </td>
															<td>{{ $Recieved->product->name }}</td>
															<td>{{ $Recieved->product->unit->actual_name }}</td>
															<td>{{ $Recieved->total_qty }}</td>
															@php $max_qty = \App\Models\WarehouseInfo::where("product_id",$Recieved->product->id)->where("store_id",$Recieved->store_id)->sum("product_qty") + $Recieved->current_qty; $data_ = "data-max-$Recieved->store_id"   @endphp
															@php $all_qty = $TransactionSellLine->where("product_id",$Recieved->product->id)->sum("quantity"); if($all_qty < $max_qty){  $max_qty = $all_qty ; }   @endphp
															<td>{{ Form::number('delivered_previouse_qty[]',$Recieved->current_qty,['class'=>'form-control item_qty','max'=>$max_qty,'min'=>0]) }}</td>
															<td>{{ Form::select('old_store_id[]',$childs,$Recieved->store_id,['class'=>'form-control o_store_id   st['.$Recieved->store_id.']' ,"data-max"=>$max_qty]) }}</td>
															<td></td>
															<td class="hide">
																<input id="max_item" disabled type="text" value="{{$max_qty}}">
															</td>
															<td class="hide">
																<input id="check_maxx" disabled type="text" value="{{$max_qty}}" data-all="{{$all_qty}}">
																<input id="product_id" disabled type="text" value="{{$Recieved->product->id}}">
																<input id="old_store"  disabled type="text" value="{{$Recieved->store_id}}">
																<input id="old_qty"    disabled type="text" value="{{$Recieved->current_qty}}">
															</td>
															<td> <a class="remove_row"> X  </a></td>
														</tr>
													@empty
														@forelse ($DeliveredWrong as $Recieved)
															@php $empty = 1;  @endphp
															<tr class="danger alert">
																<td>{{$Recieved->T_delivered->reciept_no; }}</td>
																<td>{{$Recieved->created_at}}
																	<input name="wrong_ids[]" type="hidden" value="{{ $Recieved->id }}">
																</td>
															<td>{{  $Recieved->product_name   }}</td>
															<td>{{  $Recieved->unit?$Recieved->unit->actual_name:' ' }}</td>
															<td>{{  $TrSeLine   }}</td>
															<td>{{  $Recieved->current_qty }}</td>
															<td>{{  $Recieved->store?$Recieved->store->name:' ' }}</td>
															<td></td>
															<td> <a class="remove_row"> X </a></td>
															</tr>
														@empty @endforelse
													@endforelse
													@if($empty == 0)
														@forelse ($DeliveredWrong as $Recieved)
															<tr class="danger alert">
																<td>{{$Recieved->T_delivered->reciept_no; }}</td>
																<td>{{$Recieved->created_at}}
																	<input name="wrong_ids[]" type="hidden" value="{{ $Recieved->id }}">
																</td>
															<td>{{  $Recieved->product_name   }}</td>
															<td>{{  $Recieved->unit?$Recieved->unit->actual_name:' ' }}</td>
															<td>{{  $TrSeLine   }}</td>
															<td>{{  $Recieved->current_qty }}</td>
															<td>{{  $Recieved->store?$Recieved->store->name:' ' }}</td>
															<td></td>
															<td> <a class="remove_row"> X </a></td>
															</tr>
														@empty @endforelse
													@endif
												</tbody>
												<tfoot>
													<tr class="bg-gray  font-17 footer-total" style="border:1px solid #f1f1f1;  ">
														<td>@lang('sale.total'): {{$TrSeLine }}</td>
														<td class="text-center " colspan="2"><strong></strong></td>
														<td>   </td>
														<td>Total Delivery : {{$DelPrevious}} </td>
														<td>@if($TrSeLine  - $DelPrevious < 0) Total remain : {{ 0 }} @else Total remain : {{ $TrSeLine - $DelPrevious}}@endif </td>
														{{-- <td></td> --}}
														<td>Wrong Delivery : {{ $DelWrong }}</td>
														<td></td>
														<td></td>
													</tr>
												</tfoot>

											</table>
										</div>
									</div>
								</div>
							</div>
						</div>
					@endif
					{!! Form::hidden('default_price_group', null, ['id' => 'default_price_group']) !!}
					<div class="clearfix"></div>
						
				@endcomponent
			@endif




			 
			@if(count($DeliveredPrevious)>0  )  
				@component('components.widget', ['class' => 'box-solid   ' ,"title" => "attachments"])
					@php  $array = 0 ;  @endphp
					@foreach($DeliveredPrevious as $dl) 
						@if($array == 0)
						@php
							$array  = 1;
							$td     = \App\Models\TransactionDelivery::find($dl->transaction_recieveds_id);
							$data   = $td->document;
							$parsed = \App\Models\TransactionDelivery::get_string_between($data, '["', '"]');
						@endphp
						  @if($parsed != "")
							{{-- attachment --}}
							<div class="col-md-2">
								<a href="{{ URL::to($parsed) }}" target="_blank">
									<i class="fas fa-eye"></i>
									@if($parsed != "")	<iframe src="{{ URL::to($parsed) }}" height="150" width="150" frameborder="0"></iframe>
									@endif
								</a>
							</div>
							@endif
							@if($parsed != "")
							{{-- document --}}
							<div class="col-sm-2" @if($TrSeLine - $DelPrevious < 0)  {{ "hidden" }}  @endif>
								<div class="form-group">
									{!! Form::label('upload_document', __('purchase.attach_document') . ':') !!}
									{!! Form::file('sell_document_['.$td->id.']', ['id' => 'upload_document', 'accept' => 'jpeg,png,jpg,pdf','multiple']); !!}
									<p class="help-block">
										@lang('purchase.max_file_size', ['size' => (config('constants.document_size_limit') / 1000000)])
										@includeIf('components.document_help_text')
									</p>
								</div>
							</div>
							@endif
						@endif
					@endforeach
				@endcomponent
				{{-- @if(($TrSeLine - $DelPrevious) <= 0) 
					<div  class="row" >
						{!! Form::hidden('is_save_and_print', 0, ['id' => 'is_save_and_print']); !!}
						{!! Form::hidden('lang',session()->get('user.language', config('app.locale')) , ['id' => 'lang']); !!}
						<div class="col-sm-12 text-right btn_box">
							<button type="button" id="submit-sell" @if(session()->get('user.language', config('app.locale')) == "ar") style="margin-left:60px" @else style="margin-right:60px;" @endif class="btn btn-primary btn-flat">@lang('messages.save')</button>
						</div>
					</div>
				@endif --}}
			@endif
			 
			
			@if(request()->return)
				 
				{{--  ..........  search  .............  --}}
				@if(($TrSeLine_return - $DelPrevious) > 0)   
					@component('components.widget', ['class' => 'box-solid'])
						@if(count($business_locations) > 0)
							<div class="row"   >
								<div class="col-sm-12 hide">
									<div class="form-group">
										<div class="input-group">
											<span class="input-group-addon">
												<i class="fa fa-map-marker"></i>
											</span>
											@php
												$location = "";
												foreach ($business_locations as  $key => $value) {
													$location = $key;
													break;
												}
											@endphp
										{!! Form::select('select_location_id1', $business_locations,  $location, ['class' => 'form-control ',
										'id' => 'select_location_id1', 
										'required', 'autofocus','placeholder'=>__('lang_v1.select_location')], $bl_attributes); !!}
										<span class="input-group-addon">
												@show_tooltip(__('tooltip.sale_location'))
											</span> 
										</div>
									</div>
								</div>
							</div>
						@endif
						{{-- delivery date  --}}
						<div class="col-sm-4">
							<div class="form-group">
								{!! Form::label('date', __('home.Date').':') !!}
								{!! Form::date('date',null, ['class' => 'form-control ' ]); !!}
							</div>
						</div>
						
						<div class="col-sm-4" @if($TrSeLine_return - $DelPrevious < 0)  {{ "hidden" }}  @endif >
							@php $store = ""; foreach ($childs as  $key => $value) { $store = $key; break; } @endphp
							<div class="form-group">
								{!! Form::label('store_id', __('warehouse.warehouse').':*') !!}
								{!! Form::select('store_id', $childs, $store, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'required'], $bl_attributes); !!}
							</div>
						</div>

						<div class="col-sm-4" @if($TrSeLine_return - $DelPrevious < 0)  {{ "hidden" }}  @endif>
							<div class="form-group">
								{!! Form::label('upload_document', __('purchase.attach_document') . ':') !!}
								{!! Form::file('sell_document[]', ['id' => 'upload_document', 'accept' => 'jpeg,png,jpg,pdf','multiple']); !!}
								<p class="help-block">
									@lang('purchase.max_file_size', ['size' => (config('constants.document_size_limit') / 1000000)])
									@includeIf('components.document_help_text')
								</p>
							</div>
						</div>
						<div class="col-sm-10 col-sm-offset-1" @if($TrSeLine_return - $DelPrevious < 0)  {{ "hidden" }}  @endif>
							<div class="form-group">
								<div class="input-group">
									<div class="input-group-btn">
										<button type="button" class="btn btn-default bg-white btn-flat" data-toggle="modal" data-target="#configure_search_modal" title="{{__('lang_v1.configure_product_search')}}"><i class="fa fa-barcode"></i></button>
									</div>
									{!! Form::text('search_product', null, ['class' => 'form-control mousetrap', 'id' => 'search_product', 'placeholder' => __('lang_v1.search_product_placeholder'),
									
									]); !!}
									{{-- <span class="input-group-btn">
										<button type="button" class="btn btn-default bg-white btn-flat pos_add_quick_product" data-href="{{action('ProductController@quickAdd')}}" data-container=".quick_add_product_modal"><i class="fa fa-plus-circle text-primary fa-lg"></i></button>
									</span> --}}
								</div>
							</div>
						</div>

						<div class="row col-sm-12 pos_product_div" style="min-height: 0"  >

							<input type="hidden" name="sell_price_tax" id="sell_price_tax" value="{{$business_details->sell_price_tax}}">

							<!-- Keeps count of product rows -->
							<input type="hidden" id="product_row_count" 
								value="0">
							@php
								$hide_tax = '';
								if( session()->get('business.enable_inline_tax') == 0){
									$hide_tax = 'hide';
								}
							@endphp


							{{-- table for products  used for  direct sell --}}
							<div class="table-responsive" @if($TrSeLine_return - $DelPrevious < 0)  {{ "hidden" }}  @endif>
								<table class="table table-condensed table-bordered table-striped table-responsive" id="pos_table">
									<thead>
										<tr>
											<th class="text-center">	
												@lang('sale.product')
											</th>
											<th class="text-center">
												@lang('sale.qty')
											</th>
										

											<th @can('edit_product_price_from_sale_screen')) hide @endcan>
												@lang('sale.unit_price')
											</th>

											<th @can('edit_product_discount_from_sale_screen') hide @endcan>
												@lang('receipt.discount')
											</th>

											<th class="text-center {{$hide_tax}}">
												@lang('sale.tax')
											</th>

											<th class="text-center {{$hide_tax}}">
												@lang('sale.price_inc_tax')
											</th>

											@if(!empty($warranties))
												<th>@lang('lang_v1.warranty')</th>
											@endif

											<th class="text-center">
												@lang('sale.subtotal')
											</th>

											<th class="text-center"><i class="fas fa-times" aria-hidden="true"></i></th>
										</tr>
									</thead>
									<tbody></tbody>
								</table>
							</div>

							
							<div class="table-responsive">
								<table class="table table-condensed table-bordered table-striped" id="finish_table">
									<tr>
										<td>
											<div class="pull-right">
											<b>@lang('sale.item'):</b> 
											<span class="total_quantity_">0</span>
										
										
										</td>
									</tr>
								</table>
							</div>
						</div>

						<div  class="row" >
							{!! Form::hidden('is_save_and_print', 0, ['id' => 'is_save_and_print']); !!}
							<div class="col-sm-12 text-right btn_box">
								<button type="button" id="submit-sell" @if(session()->get('user.language', config('app.locale')) == "ar") style="margin-left:60px" @else style="margin-right:60px;" @endif class="btn btn-primary btn-flat">@lang('messages.save')</button>
							</div>
						</div>
					@endcomponent
				@else
					<div  class="row" >
						{!! Form::hidden('is_save_and_print', 0, ['id' => 'is_save_and_print']); !!}
						<div class="col-sm-12 text-right btn_box">
							<button type="button" id="submit-sell" @if(session()->get('user.language', config('app.locale')) == "ar") style="margin-left:60px" @else style="margin-right:60px;" @endif class="btn btn-primary btn-flat">@lang('messages.save')</button>
						</div>
					</div>
				@endif
				@component('components.widget', ['class' => 'hide', "hidden" => true])
					<div  class="col-md-4">
						<div class="form-group">
							{!! Form::label('discount_type', __('sale.discount_type') . ':*' ) !!}
							<div class="input-group">
								<span class="input-group-addon">
									<i class="fa fa-info"></i>
								</span>
								{!! Form::select('discount_type', ['fixed' => __('lang_v1.fixed'), 'percentage' => __('lang_v1.percentage')], 'percentage' , ['class' => 'form-control','placeholder' => __('messages.please_select'), 'required', 'data-default' => 'percentage']); !!}
							</div>
						</div>
					</div>
					@php
						$max_discount = !is_null(auth()->user()->max_sales_discount_percent) ? auth()->user()->max_sales_discount_percent : '';

						//if sale discount is more than user max discount change it to max discount
						$sales_discount = $business_details->default_sales_discount;
						if($max_discount != '' && $sales_discount > $max_discount) $sales_discount = $max_discount;
					@endphp
					<div hidden class="col-md-4">
						<div class="form-group">
							{!! Form::label('discount_amount', __('sale.discount_amount') . ':*' ) !!}
							<div class="input-group">
								<span class="input-group-addon">
									<i class="fa fa-info"></i>
								</span>
								{!! Form::text('discount_amount', @num_format($sales_discount), ['class' => 'form-control input_number', 'data-default' => $sales_discount, 'data-max-discount' => $max_discount, 'data-max-discount-error_msg' => __('lang_v1.max_discount_error_msg', ['discount' => $max_discount != '' ? @num_format($max_discount) : '']) ]); !!}
							</div>
						</div>
					</div>
					<div hidden class="col-md-4"><br>
						<b>@lang( 'sale.discount_amount' ):</b>(-) 
						<span class="display_currency" id="total_discount">0</span>
					</div>
					<div class="clearfix"></div>
					<div hidden class="col-md-12 well well-sm bg-light-gray @if(session('business.enable_rp') != 1) hide @endif">
						<input type="hidden" name="rp_redeemed" id="rp_redeemed" value="0">
						<input type="hidden" name="rp_redeemed_amount" id="rp_redeemed_amount" value="0">
						<div class="col-md-12"><h4>{{session('business.rp_name')}}</h4></div>
						<div class="col-md-4">
							<div class="form-group">
								{!! Form::label('rp_redeemed_modal', __('lang_v1.redeemed') . ':' ) !!}
								<div class="input-group">
									<span class="input-group-addon">
										<i class="fa fa-gift"></i>
									</span>
									{!! Form::number('rp_redeemed_modal', 0, ['class' => 'form-control direct_sell_rp_input', 'data-amount_per_unit_point' => session('business.redeem_amount_per_unit_rp'), 'min' => 0, 'data-max_points' => 0, 'data-min_order_total' => session('business.min_order_total_for_redeem') ]); !!}
									<input type="hidden" id="rp_name" value="{{session('business.rp_name')}}">
								</div>
							</div>
						</div>
						<div class="col-md-4">
							<p><strong>@lang('lang_v1.available'):</strong> <span id="available_rp">0</span></p>
						</div>
						<div class="col-md-4">
							<p><strong>@lang('lang_v1.redeemed_amount'):</strong> (-)<span id="rp_redeemed_amount_text">0</span></p>
						</div>
					</div>
					<div class="clearfix"></div>
					<div hidden class="col-md-4">
						<div class="form-group">
							{!! Form::label('tax_rate_id', __('sale.order_tax') . ':*' ) !!}
							<div class="input-group">
								<span class="input-group-addon">
									<i class="fa fa-info"></i>
								</span>
								{!! Form::select('tax_rate_id', $taxes['tax_rates'], $business_details->default_sales_tax, ['placeholder' => __('messages.please_select'), 'class' => 'form-control', 'data-default'=> $business_details->default_sales_tax], $taxes['attributes']); !!}

								<input type="hidden" name="tax_calculation_amount" id="tax_calculation_amount" 
								value="@if(empty($edit)) {{@num_format($business_details->tax_calculation_amount)}} @else {{@num_format(optional($transaction->tax)->amount)}} @endif" data-default="{{$business_details->tax_calculation_amount}}">
							</div>
						</div>
					</div>
					<div  hidden class="col-md-4 col-md-offset-4">
						<b>@lang( 'sale.order_tax' ):</b>(+) 
						<span class="display_currency" id="order_tax">0</span>
					</div>				
					
					<div hidden class="col-md-12">
						<div class="form-group">
							{!! Form::label('sell_note',__('sale.sell_note')) !!}
							{!! Form::textarea('sale_note', null, ['class' => 'form-control', 'rows' => 3]); !!}
						</div>
					</div>
					<input type="hidden" name="is_direct_sale" value="1">
				@endcomponent  
			@else
				{{--  ..........  search  .............  --}}
				@if(($TrSeLine - $DelPrevious) > 0)   
					@component('components.widget', ['class' => 'box-solid'])
						@if(count($business_locations) > 0)
							<div class="row"   >
								<div class="col-sm-12 hide">
									<div class="form-group">
										<div class="input-group">
											<span class="input-group-addon">
												<i class="fa fa-map-marker"></i>
											</span>
											@php
												$location = "";
												foreach ($business_locations as  $key => $value) {
													$location = $key;
													break;
												}
											@endphp
										{!! Form::select('select_location_id1', $business_locations,  $location, ['class' => 'form-control ',
										'id' => 'select_location_id1', 
										'required', 'autofocus','placeholder'=>__('lang_v1.select_location')], $bl_attributes); !!}
										<span class="input-group-addon">
												@show_tooltip(__('tooltip.sale_location'))
											</span> 
										</div>
									</div>
								</div>
							</div>
						@endif
						{{-- delivery date  --}}
						<div class="col-sm-4">
							<div class="form-group">
								{!! Form::label('date', __('home.Date').':') !!}
								{!! Form::date('date',null, ['class' => 'form-control ' ]); !!}
							</div>
						</div>
						
						<div class="col-sm-4" @if($TrSeLine - $DelPrevious < 0)  {{ "hidden" }}  @endif >
							@php $store = ""; foreach ($childs as  $key => $value) { $store = $key; break; } @endphp
							<div class="form-group">
								{!! Form::label('store_id', __('warehouse.warehouse').':*') !!}
								{!! Form::select('store_id', $childs, $store, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'required'], $bl_attributes); !!}
							</div>
						</div>

						<div class="col-sm-4" @if($TrSeLine - $DelPrevious < 0)  {{ "hidden" }}  @endif>
							<div class="form-group">
								{!! Form::label('upload_document', __('purchase.attach_document') . ':') !!}
								{!! Form::file('sell_document[]', ['id' => 'upload_document', 'accept' => 'jpeg,png,jpg,pdf','multiple']); !!}
								<p class="help-block">
									@lang('purchase.max_file_size', ['size' => (config('constants.document_size_limit') / 1000000)])
									@includeIf('components.document_help_text')
								</p>
							</div>
						</div>
						<div class="col-sm-10 col-sm-offset-1" @if($TrSeLine - $DelPrevious < 0)  {{ "hidden" }}  @endif>
							<div class="form-group">
								<div class="input-group">
									<div class="input-group-btn">
										<button type="button" class="btn btn-default bg-white btn-flat" data-toggle="modal" data-target="#configure_search_modal" title="{{__('lang_v1.configure_product_search')}}"><i class="fa fa-barcode"></i></button>
									</div>
									{!! Form::text('search_product', null, ['class' => 'form-control mousetrap', 'id' => 'search_product', 'placeholder' => __('lang_v1.search_product_placeholder'),
									
									]); !!}
									{{-- <span class="input-group-btn">
										<button type="button" class="btn btn-default bg-white btn-flat pos_add_quick_product" data-href="{{action('ProductController@quickAdd')}}" data-container=".quick_add_product_modal"><i class="fa fa-plus-circle text-primary fa-lg"></i></button>
									</span> --}}
								</div>
							</div>
						</div>

						<div class="row col-sm-12 pos_product_div" style="min-height: 0" @if( $TrSeLine - $DelPrevious < 0 )  {{ "hidden" }}  @endif>

							<input type="hidden" name="sell_price_tax" id="sell_price_tax" value="{{$business_details->sell_price_tax}}">

							<!-- Keeps count of product rows -->
							<input type="hidden" id="product_row_count" 
								value="0">
							@php
								$hide_tax = '';
								if( session()->get('business.enable_inline_tax') == 0){
									$hide_tax = 'hide';
								}
							@endphp



							{{-- table for products  used for  direct sell --}}
							<div class="table-responsive" @if($TrSeLine - $DelPrevious < 0)  {{ "hidden" }}  @endif>
							<table class="table table-condensed table-bordered table-striped table-responsive" id="pos_table">
								<thead>
									<tr>
										<th class="text-center">	
											@lang('sale.product')
										</th>
										<th class="text-center">
											@lang('sale.qty')
										</th>
									

										<th @can('edit_product_price_from_sale_screen')) hide @endcan>
											@lang('sale.unit_price')
										</th>

										<th @can('edit_product_discount_from_sale_screen') hide @endcan>
											@lang('receipt.discount')
										</th>

										<th class="text-center {{$hide_tax}}">
											@lang('sale.tax')
										</th>

										<th class="text-center {{$hide_tax}}">
											@lang('sale.price_inc_tax')
										</th>

										@if(!empty($warranties))
											<th>@lang('lang_v1.warranty')</th>
										@endif

										<th class="text-center">
											@lang('sale.subtotal')
										</th>

										<th class="text-center"><i class="fas fa-times" aria-hidden="true"></i></th>
									</tr>
								</thead>
								<tbody></tbody>
							</table>
							</div>


							
							<div class="table-responsive">
								<table class="table table-condensed table-bordered table-striped" id="finish_table">
								<tr>
									<td>
										<div class="pull-right">
										<b>@lang('sale.item'):</b> 
										<span class="total_quantity_">0</span>
									
									
									</td>
								</tr>
							</table>
							</div>
						</div>

						<div  class="row" >
							{!! Form::hidden('is_save_and_print', 0, ['id' => 'is_save_and_print']); !!}
							<div class="col-sm-12 text-right btn_box">
								<button type="button" id="submit-sell" @if(session()->get('user.language', config('app.locale')) == "ar") style="margin-left:60px" @else style="margin-right:60px;" @endif class="btn btn-primary btn-flat">@lang('messages.save')</button>
							</div>
						</div>
					@endcomponent
				@else
					<div  class="row" >
						{!! Form::hidden('is_save_and_print', 0, ['id' => 'is_save_and_print']); !!}
						<div class="col-sm-12 text-right btn_box">
							<button type="button" id="submit-sell" @if(session()->get('user.language', config('app.locale')) == "ar") style="margin-left:60px" @else style="margin-right:60px;" @endif class="btn btn-primary btn-flat">@lang('messages.save')</button>
						</div>
					</div>
				@endif
				@component('components.widget', ['class' => 'hide', "hidden" => true])
					<div  class="col-md-4">
						<div class="form-group">
							{!! Form::label('discount_type', __('sale.discount_type') . ':*' ) !!}
							<div class="input-group">
								<span class="input-group-addon">
									<i class="fa fa-info"></i>
								</span>
								{!! Form::select('discount_type', ['fixed' => __('lang_v1.fixed'), 'percentage' => __('lang_v1.percentage')], 'percentage' , ['class' => 'form-control','placeholder' => __('messages.please_select'), 'required', 'data-default' => 'percentage']); !!}
							</div>
						</div>
					</div>
					@php
						$max_discount = !is_null(auth()->user()->max_sales_discount_percent) ? auth()->user()->max_sales_discount_percent : '';

						//if sale discount is more than user max discount change it to max discount
						$sales_discount = $business_details->default_sales_discount;
						if($max_discount != '' && $sales_discount > $max_discount) $sales_discount = $max_discount;
					@endphp
					<div hidden class="col-md-4">
						<div class="form-group">
							{!! Form::label('discount_amount', __('sale.discount_amount') . ':*' ) !!}
							<div class="input-group">
								<span class="input-group-addon">
									<i class="fa fa-info"></i>
								</span>
								{!! Form::text('discount_amount', @num_format($sales_discount), ['class' => 'form-control input_number', 'data-default' => $sales_discount, 'data-max-discount' => $max_discount, 'data-max-discount-error_msg' => __('lang_v1.max_discount_error_msg', ['discount' => $max_discount != '' ? @num_format($max_discount) : '']) ]); !!}
							</div>
						</div>
					</div>
					<div hidden class="col-md-4"><br>
						<b>@lang( 'sale.discount_amount' ):</b>(-) 
						<span class="display_currency" id="total_discount">0</span>
					</div>
					<div class="clearfix"></div>
					<div hidden class="col-md-12 well well-sm bg-light-gray @if(session('business.enable_rp') != 1) hide @endif">
						<input type="hidden" name="rp_redeemed" id="rp_redeemed" value="0">
						<input type="hidden" name="rp_redeemed_amount" id="rp_redeemed_amount" value="0">
						<div class="col-md-12"><h4>{{session('business.rp_name')}}</h4></div>
						<div class="col-md-4">
							<div class="form-group">
								{!! Form::label('rp_redeemed_modal', __('lang_v1.redeemed') . ':' ) !!}
								<div class="input-group">
									<span class="input-group-addon">
										<i class="fa fa-gift"></i>
									</span>
									{!! Form::number('rp_redeemed_modal', 0, ['class' => 'form-control direct_sell_rp_input', 'data-amount_per_unit_point' => session('business.redeem_amount_per_unit_rp'), 'min' => 0, 'data-max_points' => 0, 'data-min_order_total' => session('business.min_order_total_for_redeem') ]); !!}
									<input type="hidden" id="rp_name" value="{{session('business.rp_name')}}">
								</div>
							</div>
						</div>
						<div class="col-md-4">
							<p><strong>@lang('lang_v1.available'):</strong> <span id="available_rp">0</span></p>
						</div>
						<div class="col-md-4">
							<p><strong>@lang('lang_v1.redeemed_amount'):</strong> (-)<span id="rp_redeemed_amount_text">0</span></p>
						</div>
					</div>
					<div class="clearfix"></div>
					<div hidden class="col-md-4">
						<div class="form-group">
							{!! Form::label('tax_rate_id', __('sale.order_tax') . ':*' ) !!}
							<div class="input-group">
								<span class="input-group-addon">
									<i class="fa fa-info"></i>
								</span>
								{!! Form::select('tax_rate_id', $taxes['tax_rates'], $business_details->default_sales_tax, ['placeholder' => __('messages.please_select'), 'class' => 'form-control', 'data-default'=> $business_details->default_sales_tax], $taxes['attributes']); !!}

								<input type="hidden" name="tax_calculation_amount" id="tax_calculation_amount" 
								value="@if(empty($edit)) {{@num_format($business_details->tax_calculation_amount)}} @else {{@num_format(optional($transaction->tax)->amount)}} @endif" data-default="{{$business_details->tax_calculation_amount}}">
							</div>
						</div>
					</div>
					<div  hidden class="col-md-4 col-md-offset-4">
						<b>@lang( 'sale.order_tax' ):</b>(+) 
						<span class="display_currency" id="order_tax">0</span>
					</div>				
					
					<div hidden class="col-md-12">
						<div class="form-group">
							{!! Form::label('sell_note',__('sale.sell_note')) !!}
							{!! Form::textarea('sale_note', null, ['class' => 'form-control', 'rows' => 3]); !!}
						</div>
					</div>
					<input type="hidden" name="is_direct_sale" value="1">
				@endcomponent
			@endif
		</div>
	</div>
	{!! Form::close() !!}
		@if(count($array_remain)>0)
			@foreach($array_remain as $it)
				<input type="text" class="product_id_remain hide" value="{{$it}}">
			@endforeach
		@endif
</section>
		@php
			$location = null;
			foreach ($business_locations as $key => $value) {
				$location = $key;
				break;
			}
		@endphp
		<input id="location_id" type="hidden" name="location_id" value="{{$location}}">
		<input type="hidden" name="view_type" id="view_type" value="delivery_page">
<!-- quick product modal -->
 
@stop

@section('javascript')
	<script src="{{ asset('js/posss.js?v=' . $asset_v) }}"></script>
	<script src="{{ asset('js/producte.js?v=' . $asset_v) }}"></script>
	<script src="{{ asset('js/opening_stock.js?v=' . $asset_v) }}"></script>
 
    <script type="text/javascript">
	 
    //variation_id is null when weighing_scale_barcode is used.
	function pos_product_row(variation_id = null, purchase_line_id = null, weighing_scale_barcode = null, quantity = 1) {
		
		//Get item addition method
		var item_addtn_method = 0;
		var add_via_ajax = true;

		if (variation_id != null && $('#item_addition_method').length) {
			item_addtn_method = $('#item_addition_method').val();
		}

		if (item_addtn_method == 0) {
			add_via_ajax = true;
		} else {
			var is_added = false;

		//Search for variation id in each row of pos table
		$('#pos_table tbody').find('tr').each(function() {
				var row_v_id = $(this).find('.row_variation_id').val();
				var enable_sr_no = $(this).find('.enable_sr_no').val();
				var modifiers_exist = false;
				if ($(this).find('input.modifiers_exist').length > 0) {
					modifiers_exist = true;
				}

				if (row_v_id == variation_id && enable_sr_no !== '1' && !modifiers_exist &&  !is_added ) {
					add_via_ajax = false;
					is_added = true;

					//Increment product quantity
					qty_element = $(this).find('.pos_quantity');
					var qty = __read_number(qty_element);
				
					__write_number(qty_element, qty + 1);
					qty_element.change();

					round_row_to_iraqi_dinnar($(this));

					$('input#search_product')
						.focus()
						.select();
				}
		});
	}

	if (add_via_ajax) {
	var product_row = $('input#product_row_count').val();
	var location_id = $('input#location_id').val();
	var customer_id = $('select#customer_id').val();
	var store_id    = $('select#store_id').val();
	var trans_id    = $('#trans_id').val();
	var status      = "delivered";

	var is_direct_sell = false;
	if (
		$('input[name="is_direct_sale"]').length > 0 &&
		$('input[name="is_direct_sale"]').val() == 1
	) {
		is_direct_sell = true;
	}

	var price_group = '';
	if ($('#price_group').length > 0) {
		price_group = parseInt($('#price_group').val());
	}

	//If default price group present
	if ($('#default_price_group').length > 0 && 
		!price_group) {
		price_group = $('#default_price_group').val();
	}

	//If types of service selected give more priority
	if ($('#types_of_service_price_group').length > 0 && 
		$('#types_of_service_price_group').val()) {
		price_group = $('#types_of_service_price_group').val();
	}

	$.ajax({
		method: 'GET',
		url: '/sells/pos/get_product_row/' + variation_id + '/' + location_id + "/" + store_id + "/" + status+"?view_type=delivery_page&type={{ $transaction->status }}&sub_type={{ $transaction->sub_status }}"  ,
		async: false,
		data: {
			product_row: product_row,
			customer_id: customer_id,
			is_direct_sell: is_direct_sell,
			price_group: price_group,
			purchase_line_id: purchase_line_id,
			weighing_scale_barcode: weighing_scale_barcode,
			quantity: quantity,
			trans_id: trans_id,

		},
		dataType: 'json',
		success: function(result) {
			if(result.enable_stock == 1){
			if (result.success) {
				 
				    $('table#pos_table tbody').append(result.html_content).find('input.pos_quantity');
					var array = [];
					var index_array = "<option value='";
					$("#store_id option").each(function () {
						array.push($(this).html());
					});

					var start  = 0;
					var count  = array.length;
					array.forEach(element => {
						if(start == count-1){
							index_array = index_array + element  + "'>" + element + "</option>"
							start = start + 1 ; 
						}else{
							index_array = index_array + element   +"'>" + element  + "</option><option value='"
							start = start + 1 ; 
						}
					});

					var index = "<td><select name='store_sub' id='store_sub' class='form-control select2'>"+ index_array + "</select></td>";
					$("#pos_table tbody  td:nth-child(5)").hide();

				//increment row count
				$('input#product_row_count').val(parseInt(product_row) + 1);
				var this_row = $('table#pos_table tbody').find('tr').last();
				pos_each_row(this_row);
				
				//For initial discount if present
				var line_total = __read_number(this_row.find('input.pos_line_total'));
				this_row.find('span.pos_line_total_text').text(line_total);

				pos_total_row();


				//Check if multipler is present then multiply it when a new row is added.
				if(__getUnitMultiplier(this_row) > 1){
					this_row.find('select.sub_unit').trigger('change');
				}

				if (result.enable_sr_no == '1') {
					var new_row = $('table#pos_table tbody')
						.find('tr')
						.last();
					new_row.find('.add-pos-row-description').trigger('click');
				}

				round_row_to_iraqi_dinnar(this_row);
			 

				$('input#search_product').focus().select();

				//Used in restaurant module to show popup window to select extra
				if (result.html_modifier) {
					$('table#pos_table tbody').find('tr').last().find('td:first').append(result.html_modifier);
				}

				//scroll bottom of items list
				$(".pos_product_div").animate({ scrollTop: $('.pos_product_div').prop("scrollHeight")}, 1000);
				stores_max();
			} else {
				toastr.error(result.msg);
				$('input#search_product')
					.focus()
					.select();
					 
			}
		}
		},
	});
	}
	}
	function stores_max(){
         $(".drive_store_id").each(function(){
            var el =  $(this).parent().parent();
            var max = el.children().find('.drive_store_id option:selected').data('max');
            console.log(max + "max");
            var val = el.children().find('.pos_quantity').val();
            var pos_qty = el.children().find('.pos_quantity');
            pos_qty.attr('max',max);
            pos_qty.attr("data-ebrahem" ,""+max );
            pos_qty.attr("data-rule-max-value" , ""+max );
            pos_qty.data("rule-max-value" , ""+max);
            pos_qty.attr("data-msg-max-value" ,"Only " + max + " available"  );
            pos_qty.attr("data-msg-max-default" , "Only " + max + " available" );
            pos_qty.attr("data-msg_max_default" , "Only " + max + " available" );
            if (val > max) {    
                pos_qty.val(max)
            }
            $("label.error").remove();
            pos_qty.removeClass("error");
        });
    }
	

	$(document).ready( function() {
		on_change_item();
		store_change();
		stores_max();

		$('#status').change(function(){
			if ($(this).val() == 'final') {
				$('#payment_rows_div').removeClass('hide');
			} else {
				$('#payment_rows_div').addClass('hide');
			}
		});
		$("<th>Store<th>").insertBefore("#pos_table thead  tr th:nth-child(5)");
		$("#pos_table thead th:nth-child(3)").hide();
		$("#pos_table thead th:nth-child(4)").hide();
		$("#pos_table thead th:nth-child(6)").hide();
		$("#pos_table thead th:nth-child(7)").hide();
		$("#pos_table thead th:nth-child(9)").hide();

	
		$('.paid_on').datetimepicker({
			format: moment_date_format + ' ' + moment_time_format.format('hh a'),
			ignoreReadonly: true,
		});

		$('#shipping_documents').fileinput({
			showUpload:  false,
			showPreview: false,
			browseLabel: LANG.file_browse_label,
			removeLabel: LANG.remove,
		});
	});
 
	function insert_item(){
		 
		if($(".product_id_remain").val()){
			$(".product_id_remain").each(function(){
					pos_product_row($(this).val());
			});
			$("#items_all").remove(); 
			 
		}else{
			$(".pro_id").each(function(){
				pos_product_row($(this).val());
			})
			$("#items_all").remove();
			 
		}
	}
	function max_quantity(){
		$(document).ready(function(){
			var array_total         = [];
			var array_total_new     = [];
			var array_final         = [];
			var array_final_new     = [];
			var array_store_product = [];
			var check               = 0;
			$(".item_qty").each(function(){
				var el         = $(this).parent().parent();
  				var obj_item   = {};
				//.1.//.... product id
				var product_id = el.children().find("#product_id").val();
				// console.log(product_id);
				
				//.2.//.... max for one product
				var max = el.children().find(".o_store_id").attr("data-max");
				// console.log(max);
				
				//.3.//.... current value of input
				var current = parseFloat(el.children().find(".item_qty").val());
				// console.log(current);
				
				//.4.//.... store  value of input
				var s_val = el.children().find(".o_store_id").val();

				//.5.//.... store  value of input
				var store = el.children().find(".o_store_id option:selected").val();
				// console.log(current);
				
				//.6.//... collect every item
				if(!array_total.includes(product_id)){
					array_total.push(product_id);
					obj_item = {pro_id:product_id,total:max,current:current,store:store}
					array_final.push(obj_item);
				}else{
					//.*.// check if exist increase current value 
					array_final.forEach(function(element) {
						Object.keys(element).forEach(function(key) {
							if(element[key] == product_id && element["store"] == store){
								element["current"]  = element["current"] + parseFloat(current); 
								element["total"]    = parseFloat(element["total"]) + parseFloat(max); 
 							}else if(element[key] == product_id){
								obj_item = {pro_id:product_id,total:max,current:current,store:store}
								array_final.push(obj_item);
							}
						});
					})
				}
			});

			$(".pos_quantity").each(function(){
				var el = $(this).parent().parent();
  				var obj_item = {};
				//.1.//.... product id
				var product_id = el.find(".row_product_id").val();
				// console.log(product_id);
				
				//.2.//.... max for one product
				var max = el.parent().find(".drive_store_id option:selected").attr("data-max");
				// console.log(max);
				
				//.3.//.... current value of input
				var current = parseFloat(el.children().find(".pos_quantity").val());
				// console.log(current);
				
				//.4.//.... store  value of input
				var s_val = el.parent().find(".drive_store_id").val();
				// console.log(current);

				//.4.//.... store  value of input
 				var store = el.parent().find(".drive_store_id option:selected").val();
				// console.log( store);
				
				//.5.//... collect every item
				if(!array_total_new.includes(product_id)){
					array_total_new.push(product_id);
					obj_item = {pro_id:product_id,total:max,current:current,store:store}
					array_final_new.push(obj_item);
				}else{
					//.*.// check if exist increase current value 
					array_final_new.forEach(function(element) {
						Object.keys(element).forEach(function(key) {
							if(!array_store_product.includes(element["pro_id"]+"_"+element["store"])){
								array_store_product.push(element["pro_id"]+"_"+element["store"]);
								if(key == "pro_id"){
									if(element[key] == product_id && element["store"] == store){
										element["current"]  = element["current"] + parseFloat(current); 
										// element["total"]    = parseFloat(element["total"]) + parseFloat(max); 
									}else if(element[key] == product_id ){
										obj_item = {pro_id:product_id,total:max,current:current,store:store}
										array_final_new.push(obj_item);
									}
								}
							}
						});
						 
					})
				}
			});
			// console.log("old array _ ");
			// console.log(array_final);
			// console.log("new array _ ");
			// console.log(array_final);
			var new_array   = [];
			var final_array = [];
 			if(array_final_new.length == 0){
 				//... check one array  .. ! update page up table just
				 array_final.forEach(function(element) {
					Object.keys(element).forEach(function(key) {
						// console.log(element["current"] + "__" +  element["total"]);
						if(element["current"] > element["total"]){
							check = 1;
						}
					});
				});
				
		    }else{
				if(!new_array.includes(product_id)){
					array_total_new.push(product_id);
					//. FOR EACH TO TOTAL ARRAY FIRST
					array_final.forEach(function(element) {
						Object.keys(element).forEach(function(key) {
							//. FOR EACH TO TOTAL ARRAY SECOND
							array_final_new.forEach(function(el) {
								Object.keys(el).forEach(function(ky) {
									// console.log("store _ id left : ( " + element["store"] + " ) " + " store _ id right ( " + el["store"] + " ) "  );
									// console.log("key :  ( " + key  + " ) "  );
									// console.log("value key :  ( " + element[key]  + " ) " + " product _ id right ( " + el["pro_id"] + " ) " );
									if(key == "pro_id"){
										if(element[key] == el["pro_id"] && element["store"] == el["store"]){
											product_id   = element["pro_id"];
											store        = element["store"];
											max          = element["total"];
											current      = element["current"] + parseFloat(el["current"]);
											obj_item     = {pro_id:product_id,total:max,current:current,store:store}
											final_array.push(obj_item);
										} 
									}
								});
							});
						});
					});
				}
				//... check one array  .. ! update page up table just
				array_final.forEach(function(element) {
					Object.keys(element).forEach(function(key) {
						// console.log("up_" +element["current"] + "__" +  element["total"]);
						if(element["current"] > element["total"]){
							check = 1;
						}
					});
				});
				//... check second array  .. ! update page down table just
				array_final_new.forEach(function(element) {
					Object.keys(element).forEach(function(key) {
						// console.log("down_" + element["current"] + "__" +  element["total"]);
						if(element["current"] > element["total"]){
							check = 1;
						}
					});
				});
				//... check third array  .. ! update page up and down table just
				final_array.forEach(function(element) {
					Object.keys(element).forEach(function(key) {
						// console.log("mix_" +element["current"] + "__" +  element["total"]);
						if(element["current"] > element["total"]){
							check = 1;
						}
					});
				});

				// console.log(JSON.stringify(final_array));
				// console.log(JSON.stringify(array_final_new));
				
			}
			if(check == 1){
 				$("#submit-sell").remove();
				 toastr.warning("more than limited item ");
			}else{
			    // console.log($(".btn-box").html());
				if($(".btn-box#submit-sell").html() == null){
					var a =	localStorage.getItem("button_delivery");
 					if($("#lang").val() == "ar"){
						$(".btn_box").html(
							'<button type="submit" id="submit-sell"   style="margin-bottom:50px;margin-left:50px"   class="btn btn-primary btn-flat"> save </button>'
						);
					}else{
						$(".btn_box").html(
							'<button type="submit" id="submit-sell"   style="margin-bottom:50px;margin-right:50px"   class="btn btn-primary btn-flat"> save </button>'
						);
					}

				}
			}
			// console.log("Delivery-Edit://.. if this value 0 the item correct if 1 there is quantity more than limit .. ! -> value : ( "+check+" )");
		});
	}
	function on_change_item(){
		max_quantity();
		$(".item_qty").ready(function(){
			$(".row_edit").each(()=>{
				var el = $(this);
				el.find(".item_qty").on("change",function(){
					max_quantity();
				});
			});
		});
	}
	function store_change(){
		 
		$(".item_qty").each(function(){
			var el       = $(this).parent().parent();
			var item     = el.children().find(".o_store_id")  ;
			item.on("change",function(){
				var max_val    = el.children().find(".item_qty");
				var vel        = el.children().find(".o_store_id");
				var old_qty    = el.children().find("#old_qty").val()  ;
				var old        = el.children().find("#old_store").val()  ;
				var product_id = el.children().find("#product_id").val()  ;
				var check_maxx = el.children().find("#check_maxx").val()  ;
				var item_val   = el.children().find(".o_store_id option:selected").val();
				var cl         = ".st["+item_val+"]";    
				var max        = el.parent().children().find()  ;
				$.ajax({
					url: "/sells/max-qty",
					type: "GET",
					data: {product:product_id,store:item_val} ,
 					success: function(result) {
						if(result.success == 1){
 							if(item_val == old ){
								var final = result.val + parseFloat(old_qty);
								if(final >= check_maxx){
									final = check_maxx
								}
								console.log(final + ">=" +  check_maxx);
								vel.attr("data-max",final);
								max_val.attr("max",final);
								max_quantity();
							}else{
								console.log(final + "<=" +  result.val);
								var final = result.val ;
								if(final >= check_maxx){
									final = check_maxx
								}
								vel.attr("data-max",final);
								max_val.attr("max",final);
								max_quantity();
							}
						}else{
							max_quantity();
						}
						
					}
				});
 			});
		});

	}

	$('.remove_row').click(function(){
		$(this).parent().parent().remove();
	})

    </script>
@endsection
