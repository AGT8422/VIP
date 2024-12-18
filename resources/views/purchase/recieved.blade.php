@extends('layouts.app')
{{-- *1* --}}
@section('title', __('purchase.add_Receipt_response'))
{{-- *2* --}}
@section('content')
	<!-- Content Header (Page header) -->
    {{-- Section title of page --}}
    {{-- ************************************************ --}}
		<section class="content-header">
			<h1>@lang('purchase.add_Receipt_response') <i class="fa fa-keyboard-o hover-q text-muted" aria-hidden="true" data-container="body" data-toggle="popover" data-placement="bottom" data-content="@include('purchase.partials.keyboard_shortcuts_details')" data-html="true" data-trigger="hover" data-original-title="" title=""></i></h1>
			<h5><i><b>{{ "   Purchase   ( " . $transaction->ref_no . " ) >  " }} </b>{{ __("purchase.add_Receipt_response")   }} <b> {{"   "}} </b></i></h5>
			<br>   
		</section>
	{{-- ************************************************ --}}
	<!-- Main content -->
	{{-- Section body of page --}}
    {{-- ************************************************ --}}
		<section class="content" style="margin:0px 10%" >
			{{-- *1* section currency --}}
			{{-- ********************************************** --}}
				<!-- Page level currency setting -->
				<input type= "hidden" id= "p_code"     value= "{{$currency_details->code}}">
				<input type= "hidden" id= "p_symbol"   value= "{{$currency_details->symbol}}">
				<input type= "hidden" id= "p_thousand" value= "{{$currency_details->thousand_separator}}">
				<input type= "hidden" id= "p_decimal"  value= "{{$currency_details->decimal_separator}}">
			{{-- ********************************************** --}}
			{{-- *2* section form url --}}
			{{-- ********************************************** --}}
				{{-- *2/1* section form url --}}
				{{-- ********************************************** --}}
					@include('layouts.partials.error')
					@if(request()->type == "return_purchase")
						{!! Form::open(['url' => 'recive/purchase-return/'.app('request')->input('id'), 'method' => 'post', 'id' => 'add_purchase_form', 'files' => true ]) !!}
					@else
						{!! Form::open(['url' => 'recive/purchase/'.app('request')->input('id'), 'method' => 'post', 'id' => 'add_purchase_form', 'files' => true ]) !!}
					@endif
				{{-- ********************************************** --}}

				{{-- *2/2* section form body --}}
				{{-- ********************************************** --}}
						@if(request()->type == "return_purchase")
							{{-- *2/2-1* section form body top information // Return --}}
							{{-- *************************************************** --}}
								<input type="text" class="hide" id="return_type" value="return_type">
								@component('components.widget', ['class' => 'box-primary' , 'style'=>"margin:0px 10%" ])
									<div class="row">
										{{--  contact info --}}
										{{--  ************ --}}
										@if(!empty($transaction->contact))
											<div class="col-md-4">
												<div class="well">
													<strong>
														@if(in_array($transaction->type, ['purchase_return']))
															@lang('purchase.supplier') 
														@elseif(in_array($transaction->type, ['sell']))
															@lang('contact.customer') 
														@endif
													</strong>:{{ $transaction->contact->name }}<br>
													@if($transaction->type == 'purchase')
														--  
													@endif
												</div>
											</div>
										@endif
										{{--  purchase id hide --}}
										{{--  **************** --}}
										<input type="text" hidden name="transaction_id" id="transaction_id" value="{{$transaction->id}}" >
										{{--  purchase location --}}
										{{--  ***************** --}}
										<div class="col-md-4">
											<div class="well">
												<strong>@lang('purchase.ref_no'): </strong>{{ $transaction->ref_no }}
												@if(!empty($transaction->location))
													<br>
													<strong>@lang('purchase.location'): </strong>{{ $transaction->location->name }}
												@endif
											</div>

										</div>
										{{--  store /* final --}}
										{{--  ************** --}}
										<div class="col-md-4">
											<div class="well">
												{{--  total purchase --}}
												{{--  ************** --}}
												@php  
														$tr       = \App\Transaction::where("id",$transaction->return_parent_id)->first();
														$quantity = $tr->purchase_lines->sum("quantity_returned"); 
												@endphp
												<strong>@lang('sale.total_amount'): </strong><span class="display_currency" data-currency_symbol="false">{{intval($quantity)}}</span><br>
												{{--  store name  --}}
												{{--  **********  --}}
												@if(!empty($transaction->additional_notes))
													{{ $transaction->additional_notes }}<br>
													@lang("warehouse.nameW") : {{$tr->warehouse->name}}
												@else
													--<br><b>@lang("warehouse.nameW")  :</b> {{$tr->warehouse->name}}
												@endif
											</div>
										</div>
									</div>
									{{--  purchase information --}}
									{{--  ******************** --}}
									<h4>@lang("recieved.previous_purchase")</h4>
									
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
												<tr  @if(($main-$total_) == 0) style="border:1px solid #f1f1f1;" @else @php $array_remain[]=$purchase_line->product->id; @endphp  style="border:1px solid #f1f1f1; background:#ff4d4d9a"@endif>
													<td class="hide">
														<input class="pro_id" value="{{$purchase_line->product->id}}"/>
													</td>
													<td>{{ $loop->iteration }}</td>
													<td>
														{{ $purchase_line->product->name }}
														@if( $purchase_line->product->type == 'variable')
														- {{ $purchase_line->variations->product_variation->name}}
														- {{ $purchase_line->variations->name}}
														@endif
													</td>
													@php $cost        = \App\Product::product_cost_purchase($purchase_line->product->id,$tr->id,"return");  @endphp
													<td>
														@if(auth()->user()->hasRole('Admin#' . session('business.id')) ||  auth()->user()->can('product.avarage_cost'))
															<span class="display_currency" data-currency_symbol="true">{{ ($cost)?$cost:$purchase_line->bill_return_price }}</span>
														@else
															{{ "--" }}
														@endif
													</td>
													<td>{{@format_quantity($purchase_line->quantity_returned)}} {{$unit_name}}</td>
													<td>
														@php
														$line_total = (($cost)?$cost:$purchase_line->bill_return_price) * $purchase_line->quantity_returned;
														$total_before_tax += $line_total ;
														@endphp
														@if(auth()->user()->hasRole('Admin#' . session('business.id')) ||  auth()->user()->can('product.avarage_cost'))
															<span class="display_currency" data-currency_symbol="true">{{$line_total}}</span>
														@else
															{{ "--" }}
														@endif
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
									{{--  insert all Items button --}}
									{{--  *********************** --}}
									@if(($purline_return->qty - $RPrevious) > 0)
										<div  class="modal-head" >
											<button type="button" id="items_all" onclick="insert_item();" class="btn btn-primary all_item">@lang( 'messages.all_item' )</button>
										</div>
										<div>&nbsp;&nbsp;</div>
									@endif
									{{--  received previous information --}}
									{{--  ***************************** --}}
									<div class="row" >
										<div class="col-md-12">
											<strong>@lang('lang_v1.old_recieved'):</strong> 
										</div>
									</div>
									<div class="row"  >
										<div class="col-md-12">
												<div class="table-responsive">
													<table class="table table-recieve" >
													<tr style="background:#f1f1f1;">
													<th>@lang('purchase.ref_no')</th>
													<th>@lang('messages.date')</th>
													<th>@lang('product.product_name')</th>
													<th>@lang('product.unit')</th>
													<th>@lang('purchase.qty_total')</th>
													<th>@lang('purchase.qty_current')</th>
													<th>@lang('purchase.qty_remain')</th>
													<th>@lang('warehouse.nameW')</th>
													<th>@lang('purchase.payment_note')</th>
													{{-- <th class="no-print">@lang('messages.actions')</th> --}}
													</tr>

													@php
													$total = 0;
													$total_wrong = 0;
													$pre = "";
													$type = "base";
													@endphp

													@forelse ($RecievedPrevious as $Recieved)
														{{-- row info --}}
														@php

															if(!in_array($Recieved->product_id,$product_list_all_return)){
															$type          = "other";
															$total_wrong   = $total_wrong + $Recieved->current_qty ;
															}else{
															$type          = "base";
															$total         = $total + $Recieved->current_qty;
															}
											
														@endphp
														
														{{-- empty row --}}
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
																	{{-- <td>{{""}}</td>	 --}}
																<tr>
															@endif
															@php $style = ""; $pre  = $Recieved->created_at; @endphp
														@endif

														{{-- Row --}}
														<tr  @if($type != "other") style="background:#f1f1f1; width:100% !important" @else style="background:#ff5b5b; width:100% !important" @endif>
															<td>{{$Recieved->TrRecieved->reciept_no}}</td>
															<td>{{$Recieved->created_at}}</td>
															<td>
																<input type="hidden" name="recieve_previous_id[]" value="{{ $Recieved->id }}">
																{{$Recieved->product->name}}
															</td>
															<td>{{$Recieved->product->unit->actual_name}}</td>
															<td>{{$Recieved->total_qty}}</td>
															<td>{{$Recieved->current_qty}}</td>
															<td>{{$Recieved->total_qty - $Recieved->current_qty}}</td>
															<td>{{$Recieved->store->name}}</td>
															<td></td>
															{{-- <td><a class="remove_row" class="btn btn-xs btn-danger"> X </a></td> --}}
														</tr>
										
													@empty
														{{-- wrong row --}}
														@if((count($RecievedWrong)>0))
															@foreach($RecievedWrong as $Wrong)
																<tr class="danger alert">
																	<td>{{$Wrong->TrRecieved->reciept_no }}</td>
																	<td>{{$Wrong->created_at }}
																		<input type="hidden"  name="recieved_wrong_id[]" value="{{ $Wrong->id }}" >
																	</td>
																	<td> {{ $Wrong->product_name }}</td>
																	<td> {{ ($Wrong->unit)?$Wrong->unit->actual_name:' ' }}</td>
																	<td> {{ $Wrong->total_qty }}</td>
																	<td> {{ $Wrong->current_qty }}</td>
																	<td> {{ "" }}</td>
																	<td> {{ ($Wrong->store)?$Wrong->store->name:' ' }}</td>
																	<td> {{ trans('home.Wrong Recieved') }} .. </td>
																	{{-- <td><a class="remove_row" class="btn btn-xs btn-danger"> X </a></td> --}}
																</tr>
															@endforeach
														@endif

													@endforelse
														{{-- wrong row --}}
													@if((count($RecievedWrong)>0))
														@foreach($RecievedWrong as $Wrong)
															@php
															$total_wrong = $total_wrong + $Wrong->current_qty;
															@endphp
															<tr class="danger alert">
																<td>{{$Wrong->TrRecieved->reciept_no }}</td>
																<td>
																	{{$Wrong->created_at }}
																	<input type="hidden"  name="recieved_wrong_id[]" value="{{ $Wrong->id }}" >
																</td>
																<td> {{ $Wrong->product_name }}</td>
																<td> {{ ($Wrong->unit)?$Wrong->unit->actual_name:' ' }}</td>
																<td> {{ $Wrong->total_qty }}</td>
																<td> {{ $Wrong->current_qty }}</td>
																<td> {{ "" }}</td>
																<td> {{ ($Wrong->store)?$Wrong->store->name:' ' }}</td>
																<td> {{ trans('home.Wrong Recieved') }} </td>
																{{-- <td><a class="remove_row" class="btn btn-xs btn-danger"> X </a></td> --}}
															</tr>
														@endforeach
													@endif


													<tfoot>
													<tr class="bg-gray  font-17 footer-total" style="border:1px solid #f1f1f1;  ">
														<td>@lang('sale.total'): {{ $purline_return->qty}}</td>
														<td class="text-center " colspan="2"><strong></strong></td>
														<td>@if($purline_return->qty - $total < 0) Total more : {{-($purline_return->qty - $total)}}@endif </td>
														<td></td>
														<td>Total received : {{$total}} </td>
														<td>@if($purline_return->qty - $total < 0) Total remain : {{ 0 }} @else Total remain : {{ $purline_return->qty - $total}}@endif </td>
														<td>Wrong Received : {{$total_wrong}}</td>
														<td></td>
												
													</tr>
												</tfoot>

												</table>
											</div>
										</div>
									</div>
								@endcomponent
							{{-- ************************************************** --}}
							{{-- *2/2-2* section form body main information / search-box --}}
							{{-- **************************************************************** --}}	
								@component('components.widget', ['class' => 'box-primary', 'style'=>"margin:0px 10%"    ])
									{{-- location / store --}}
									{{-- **************** --}}	
									<div @if ($total >= $purline_return->qty) {{"hidden"}} @endif  class="row">
										{{-- location / search-box --}}
										{{-- ********************* --}}
										<div class="col-md-6 hide">
											<div class="form-group">
											{!! Form::label('location_id', __('purchase.business_location').':*') !!}
											{!! Form::select('location_id', $business_locations, null, ['class' => 'form-control select2', 'required', 'id' => 'location_id']); !!}
											</div>
										</div>
										{{-- store / search-box --}}
										{{-- ****************** --}}
										<div  class="col-md-3" >
											<div class="form-group">
												{!! Form::label('store_id', __('warehouse.nameW').':*') !!}
												{!! Form::select('store_id', $childs, null, ['class' => 'form-control select2', 'name' => "store_id", 'required', 'id' => 'store_id' ]); !!}
												{!! Form::text('transaction_id', $transaction->id , ["hidden", 'name' => "transaction_id",   'id' => 'transaction_id']); !!}
											</div>
										</div>
										{{-- optional / date    --}}
										{{-- ****************** --}}
										<div  class="col-md-3" >
											<div class="form-group">
												{!! Form::label('date', __('lang_v1.date').':*') !!}
												{!! Form::date('date',null, ['class' => 'form-control', 'name' => "date",  'id' => 'date' ]); !!}
											</div>
										</div>
										{{-- optional / currency --}}
										{{-- ******************* --}}
										<div class="col-md-3">
											<div class="form-group">
												<div class="multi-input">
													{!! Form::label('currency_id', __('business.currency') . ':') !!}  
													<br/>
													{!! Form::select('currency_id', $currencies, null, ['class' => 'form-control width-60 currency_id  select2', 'placeholder' => __('messages.please_select') ]); !!}
													{!! Form::text('currency_id_amount', null, ['class' => 'form-control width-40 pull-right currency_id_amount'   ]); !!}
													<br/>
													<div class="check_dep_curr hide">
														{!! Form::checkbox('depending_curr',null, 0, ['class' => 'depending_curr' ,'id'=>'depending_curr'   ]); !!}
														{!! Form::label('depending_curr', __('Depending On Currency Column') . '') !!}  
													</div>
													<br/> 
													<div class="check_dep_curr  hide"><input  type="checkbox" name="dis_currency" value="1"> <b>Discount</b> @show_tooltip(__('tooltip.dis_currency'))<br ></div>
												</div>
											</div>
										</div>
									</div>
									{{-- item-search / search-box --}}
									{{-- ************************ --}}
									<div class="row" @if ($total >= $purline_return->qty) {{"hidden"}} @endif   >
										<div class="col-sm-8 col-sm-offset-2">
											<div class="form-group">
												<div class="input-group">
													<span class="input-group-addon">
														<i class="fa fa-search"></i>
													</span>
													{!! Form::text('search_product', null, ['class' => 'form-control mousetrap', 'id' => 'search_product', 'placeholder' => __('lang_v1.search_product_placeholder')   ]); !!}
												</div>
											</div>
										</div>
									</div>
									{{-- table for item / search-box --}}
									{{-- *************************** --}}
									<div class="row" @if ($total >= $purline_return->qty) {{"hidden"}} @endif  >
										<div class="col-sm-12">
											<div class="table-responsive">
												<table class="table table-condensed table-bordered table-th-green text-center table-striped" id="purchase_entry_table">
													<thead>
														<tr>
															<th>#</th>
															<th>@lang( 'product.product_name' )</th>
															<th>@lang( 'purchase.purchase_quantity' )</th>
															<th>@lang( 'lang_v1.unit_cost_before_discount' )</th>
															<th>@lang( 'lang_v1.discount_percent' )</th>
															<th>@lang( 'purchase.unit_cost_before_tax' )</th>
															<th class="hide">@lang( 'purchase.subtotal_before_tax' )</th>
															<th class="hide">@lang( 'purchase.product_tax' )</th>
															<th class="hide">@lang( 'purchase.net_cost' )</th>
															<th>@lang( 'purchase.line_total' )</th>
															<th class="@if(!session('business.enable_editing_product_from_purchase')) hide @endif">
																@lang( 'lang_v1.profit_margin' )
															</th>

															<th>
																@lang( 'purchase.unit_selling_price' )
																<small>(@lang('product.inc_of_tax'))</small>
															</th>

															@if(session('business.enable_lot_number'))
																<th>
																	@lang('lang_v1.lot_number')
																</th>
															@endif

															@if(session('business.enable_product_expiry'))
																<th>
																	@lang('product.mfg_date') / @lang('product.exp_date')
																</th>
															@endif

															<th><i class="fa fa-trash" aria-hidden="true"></i></th>
														</tr>
													</thead>
													<tbody></tbody>
												</table>
											</div>
											<hr/>
											{{-- total item --}}
											<div class="pull-right col-md-5">
												<table class="pull-right col-md-12">
													<tr>
														<th class="col-md-7 text-right">@lang( 'lang_v1.total_items' ):</th>
														<td class="col-md-5 text-left">
															<span id="total_quantity" class="display_currency" data-currency_symbol="false"></span>
														</td>
													</tr>
													<tr>
														<th class="col-md-7 text-right">@lang('purchase.sub_total_amount' ):</th>
														<td class="col-md-5 text-left">
															<span id="total_subtotal_id" class="display_currency"></span>
															<!-- This is total before purchase tax-->
															{!! Form::hidden('total_subtotal_input_id', 0 , ['id' => 'total_subtotal_input_id']); !!}

														</td>
													</tr>
												</table>
											</div>
											{{-- joy item --}}
											<input type="hidden" id="row_count" value="0">
											<div class="joy hide" >joy</div>
										</div>
									</div>
								@endcomponent
							{{-- **************************************************************** --}}
							{{-- *2/2-3* section form body submit button --}}
							{{-- **************************************************************** --}}	
								@if(($purline_return->qty - $RPrevious) > 0)
									<div  class="modal-footer"  >
										<button type="submit"  @if (session()->get('user.language', config('app.locale')) == "ar") style="left-right:100px"  @else style="margin-right:50px" @endif class="btn btn-primary">@lang( 'messages.save' )</button>
									</div>
								@endif
							{{-- **************************************************************** --}}	
						@else
							{{-- *2/2-4* section form body top information // Return --}}
							{{-- ************************************************** --}}
								@component('components.widget', ['class' => 'box-primary' , 'style'=>"margin:0px 10%" ])
									<div class="row">
										{{--  contact info --}}
										{{--  ************ --}}
										@if(!empty($transaction->contact))
											<div class="col-md-4">
												<div class="well">
													<strong>
														@if(in_array($transaction->type, ['purchase']))
														@lang('purchase.supplier') 
														@elseif(in_array($transaction->type, ['sell']))
														@lang('contact.customer') 
														@endif
													</strong>:{{ $transaction->contact->name }}<br>
													@if($transaction->type == 'purchase')
													--  
													@endif
												</div>
											</div>
										@endif
										{{--  purchase id hide --}}
										{{--  **************** --}}
										<input type="text" hidden name="transaction_id" id="transaction_id" value="{{$transaction->id}}" >
										{{--  purchase location --}}
										{{--  ***************** --}}
										<div class="col-md-4">
											<div class="well">
												<strong>@lang('purchase.ref_no'): </strong>{{ $transaction->ref_no }}
												@if(!empty($transaction->location))
													<br>
													<strong>@lang('purchase.location'): </strong>{{ $transaction->location->name }}
												@endif
											</div>

										</div>
										{{--  store /* final --}}
										{{--  ************** --}}
										<div class="col-md-4">
											<div class="well">
												@php  $quantity = $quantity_all; @endphp
												<strong>@lang('sale.total_amount'): </strong><span class="display_currency" data-currency_symbol="false">{{intval($quantity)}}</span><br>
												@if(!empty($transaction->additional_notes))
													{{ $transaction->additional_notes }}
												@else
												--
												@endif
											</div>
										</div>
									</div>
									{{--  purchase information --}}
									{{--  ******************** --}}
									<div class="row">
										<div class="col-md-12">
											<div class="table-responsive">
												<table class="table table-recieve" >
													<tr style="background:#f1f1f1;">
														<th>@lang('messages.date')</th>
														<th>@lang('product.product_name')</th>
														<th>@lang('purchase.price')</th>
														<th>@lang('purchase.qty')</th>
														<th>@lang('purchase.recieved_status')</th>
														<th>@lang('warehouse.nameW')</th>
														<th>@lang("home.total received")</th>
														<th>@lang("purchase.total_remain")</th>
														<th>@lang('purchase.payment_note')</th>
													</tr>

													@php
														$array_final = [];
														$array_test  = []; 
													@endphp
													@forelse ($purchcaseline as $pr)
														@php
															$total_ = \App\Models\RecievedPrevious::where("transaction_id",$pr->transaction->id)->where("product_id",$pr->product->id)->sum("current_qty");    
															$main   = \App\PurchaseLine::where("transaction_id",$pr->transaction->id)->where("product_id",$pr->product->id)->sum("quantity");    
														@endphp
														@php
																$array = [];
																$array["product_name"]          = $pr->product->name;
																$array["product_id_src"]        = $pr->product->id;
																$array["product_id_str"]        = $pr->store_id;
																$array["product_id_unit"]       = $pr->product->unit_id;
																$array["product_id_qty"]        = $pr->quantity;
																$array["product_id_unit_value"] = $pr->product->unit->actual_name;
																$array_final[] = $array ; 
														@endphp 

														<tr  @if(($main-$total_) == 0) style="border:1px solid #f1f1f1;" @else @php $array_remain[]=$pr->product->id; @endphp  style="border:1px solid #f1f1f1; background:#ff4d4d9a"@endif>
															<td class="hide">
																<input class="pro_id" value="{{$pr->product->id}}"/>
															</td>
															<td>{{$pr->created_at}}</td>
															<td>{{$pr->product->name}}</td>
															<td>
																@php
																	$cost   = \App\Product::product_cost_purchase($pr->product->id,$transaction->id);
																@endphp
																@if(auth()->user()->hasRole('Admin#' . session('business.id')) ||  auth()->user()->can('product.avarage_cost'))
																	{{$cost}}
																@else
																	{{ "--" }}
																@endif
															
															</td>
															<td>{{$pr->quantity}}</td>
															<td>{{$transaction->status}}</td>
															<td>{{$pr->warehouse->name}}</td>
															<td>
																{{ $total_}}    
															</td>    
															<td>{{$main-$total_}}</td> 
															<td> </td> 
														
														</tr>

														@forelse($array_final as $value)
															@if(!in_array($value,$array_test))
															@php array_push($array_test,$value); @endphp
															@endif
														@empty @endforelse
														
													@empty @endforelse

													<td>{!! Form::text("count"  , count($array_test) ,['hidden','id' => "count" ]); !!}</td>

													<tfoot>
														<tr class="bg-gray  font-17 footer-total" style="border:1px solid #f1f1f1;  ">
															<td class="text-center " colspan="2"><strong>@lang('sale.total'):</strong></td>
															<td>{{$quantity_all}}</td>
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
									{{--  insert all Items button --}}
									{{--  *********************** --}}
									@if(($quantity_all - $RPrevious) > 0)
										<div  class="modal-head"  >
											<button type="button" id="items_all" onclick="insert_item();" class="btn btn-primary all_item">@lang( 'messages.all_item' )</button>
										</div>
									@endif
									{{--  received previous information --}}
									{{--  ***************************** --}}
									<div class="row" @if($hide == "true") {{"hidden"}} @endif>
										<div class="col-md-12">
											<strong>@lang('lang_v1.old_recieved'):</strong> 
										</div>
									
										<div class="col-md-12">
												<div class="table-responsive">
													<table class="table table-recieve" >
													<tr style="background:#f1f1f1;">
													<th>@lang('purchase.ref_no')</th>
													<th>@lang('messages.date')</th>
													<th>@lang('product.product_name')</th>
													<th>@lang('product.unit')</th>
													<th>@lang('purchase.qty_total')</th>
													<th>@lang('purchase.qty_current')</th>
													<th>@lang('purchase.qty_remain')</th>
													<th>@lang('warehouse.nameW')</th>
													<th>@lang('purchase.payment_note')</th>
													{{-- <th class="no-print">@lang('messages.actions')</th> --}}
													</tr>

													@php
													$total = 0;
													$total_wrong = 0;
													$pre = "";
													$type = "base";
													@endphp

													@forelse ($RecievedPrevious as $Recieved)
														{{-- row info --}}
														@php

															if(!in_array($Recieved->product_id,$product_list_all)){
															$type          = "other";
															$total_wrong   = $total_wrong + $Recieved->current_qty ;
															}else{
															$type          = "base";
															$total         = $total + $Recieved->current_qty;
															}
											
														@endphp
														
														{{-- empty row --}}
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
																	{{-- <td>{{""}}</td>	 --}}
																<tr>
															@endif
															@php $style = ""; $pre  = $Recieved->created_at; @endphp
														@endif

														{{-- Row --}}
														<tr  @if($type != "other") style="background:#f1f1f1; width:100% !important" @else style="background:#ff5b5b; width:100% !important" @endif>
															<td>{{$Recieved->TrRecieved->reciept_no}}</td>
															<td>{{$Recieved->created_at}}</td>
															<td>
																<input type="hidden" name="recieve_previous_id[]" value="{{ $Recieved->id }}">
																{{$Recieved->product->name}}
															</td>
															<td>{{$Recieved->product->unit->actual_name}}</td>
															<td>{{$Recieved->total_qty}}</td>
															<td>{{$Recieved->current_qty}}</td>
															<td>{{$Recieved->total_qty - $Recieved->current_qty}}</td>
															<td>{{$Recieved->store->name}}</td>
															<td></td>
															{{-- <td><a class="remove_row" class="btn btn-xs btn-danger"> X </a></td> --}}
														</tr>
										
													@empty
														{{-- wrong row --}}
														@if((count($RecievedWrong)>0))
															@foreach($RecievedWrong as $Wrong)
																<tr class="danger alert">
																	<td>{{$Wrong->TrRecieved->reciept_no }}</td>
																	<td>{{$Wrong->created_at }}
																		<input type="hidden"  name="recieved_wrong_id[]" value="{{ $Wrong->id }}" >
																	</td>
																	<td> {{ $Wrong->product_name }}</td>
																	<td> {{ ($Wrong->unit)?$Wrong->unit->actual_name:' ' }}</td>
																	<td> {{ $Wrong->total_qty }}</td>
																	<td> {{ $Wrong->current_qty }}</td>
																	<td> {{ "" }}</td>
																	<td> {{ ($Wrong->store)?$Wrong->store->name:' ' }}</td>
																	<td> {{ trans('home.Wrong Recieved') }} .. </td>
																	{{-- <td><a class="remove_row" class="btn btn-xs btn-danger"> X </a></td> --}}
																</tr>
															@endforeach
														@endif

													@endforelse
														{{-- wrong row --}}
													@if((count($RecievedWrong)>0))
														@foreach($RecievedWrong as $Wrong)
															@php
															$total_wrong = $total_wrong + $Wrong->current_qty;
															@endphp
															<tr class="danger alert">
																<td>{{$Wrong->TrRecieved->reciept_no }}</td>
																<td>
																	{{$Wrong->created_at }}
																	<input type="hidden"  name="recieved_wrong_id[]" value="{{ $Wrong->id }}" >
																</td>
																<td> {{ $Wrong->product_name }}</td>
																<td> {{ ($Wrong->unit)?$Wrong->unit->actual_name:' ' }}</td>
																<td> {{ $Wrong->total_qty }}</td>
																<td> {{ $Wrong->current_qty }}</td>
																<td> {{ "" }}</td>
																<td> {{ ($Wrong->store)?$Wrong->store->name:' ' }}</td>
																<td> {{ trans('home.Wrong Recieved') }} </td>
																{{-- <td><a class="remove_row" class="btn btn-xs btn-danger"> X </a></td> --}}
															</tr>
														@endforeach
													@endif


													<tfoot>
													<tr class="bg-gray  font-17 footer-total" style="border:1px solid #f1f1f1;  ">
														<td>@lang('sale.total'): {{$quantity_all}}</td>
														<td class="text-center " colspan="2"><strong></strong></td>
														<td>@if($quantity_all - $total < 0) Total more : {{-($quantity_all - $total)}}@endif </td>
														<td></td>
														<td>Total received : {{$total}} </td>
														<td>@if($quantity_all - $total < 0) Total remain : {{ 0 }} @else Total remain : {{ $quantity_all - $total}}@endif </td>
														<td>Wrong Received : {{$total_wrong}}</td>
														<td></td>
												
													</tr>
												</tfoot>

												</table>
											</div>
										</div>
									</div>
								@endcomponent
							{{-- ************************************************** --}}
							{{-- *2/2-5* section form body main information / search-box --}}
							{{-- **************************************************************** --}}	
								@component('components.widget', ['class' => 'box-primary'  , 'style'=>"margin:0px 10%"  ])
									{{-- location / store --}}
									{{-- **************** --}}	
									<div @if ($total >= $quantity_all) {{"hidden"}} @endif class="row">
										{{-- location / search-box --}}
										{{-- ********************* --}}
										<div class="col-md-6 hide">
											<div class="form-group">
											{!! Form::label('location_id', __('purchase.business_location').':*') !!}
											{!! Form::select('location_id', $business_locations, null, ['class' => 'form-control select2', 'required', 'id' => 'location_id']); !!}
											</div>
										</div>
										{{-- store / search-box --}}
										{{-- ****************** --}}
										<div @if ($total >= $quantity_all) {{"hidden"}} @endif class="col-md-3">
											<div class="form-group">
												{!! Form::label('store_id', __('warehouse.nameW').':*') !!}
												{!! Form::select('store_id', $childs, null, ['class' => 'form-control select2', 'name' => "store_id", 'required', 'id' => 'store_id' ]); !!}
												{!! Form::text('transaction_id', $transaction->id , ["hidden", 'name' => "transaction_id",   'id' => 'transaction_id']); !!}
											</div>
										</div>
										{{-- optional / date    --}}
										{{-- ****************** --}}
										<div  class="col-md-3" >
											<div class="form-group">
												{!! Form::label('date', __('lang_v1.date').':') !!}
												{!! Form::date('date',null, ['class' => 'form-control', 'name' => "date",  'id' => 'date' ]); !!}
											</div>
										</div>
									    {{-- optional / currency --}}
										{{-- ******************* --}}
										<div class="col-md-3">
											<div class="form-group">
												<div class="multi-input">
													{!! Form::label('currency_id', __('business.currency') . ':') !!}  
													<br/>
													{!! Form::select('currency_id', $currencies, null, ['class' => 'form-control width-60 currency_id  select2', 'placeholder' => __('messages.please_select') ]); !!}
													{!! Form::text('currency_id_amount', null, ['class' => 'form-control width-40 pull-right currency_id_amount'   ]); !!}
													<br/>
													<div class="check_dep_curr hide">
														{!! Form::checkbox('depending_curr',null, 0, ['class' => 'depending_curr' ,'id'=>'depending_curr'   ]); !!}
														{!! Form::label('depending_curr', __('Depending On Currency Column') . '') !!}  
													</div>
													<br/> 
													<div class="check_dep_curr  hide"><input  type="checkbox" name="dis_currency" value="1"> <b>Discount</b> @show_tooltip(__('tooltip.dis_currency'))<br ></div>
												</div>
											</div>
										</div>
									</div>
									{{-- item-search / search-box --}}
									{{-- ************************ --}}
									<div class="row" @if ($total >= $quantity_all) {{"hidden"}} @endif >
										<div class="col-sm-8 col-sm-offset-2">
											<div class="form-group">
												<div class="input-group">
													<span class="input-group-addon">
														<i class="fa fa-search"></i>
													</span>
													{!! Form::text('search_product', null, ['class' => 'form-control mousetrap', 'id' => 'search_product', 'placeholder' => __('lang_v1.search_product_placeholder')   ]); !!}
												</div>
											</div>
										</div>
									</div>
									{{-- table for item / search-box --}}
									{{-- *************************** --}}
									<div class="row" @if ($total >= $quantity_all) {{"hidden"}} @endif >
										<div class="col-sm-12">
											<div class="table-responsive">
												<table class="table table-condensed table-bordered table-th-green text-center table-striped" id="purchase_entry_table">
													<thead>
														<tr>
															<th>#</th>
															<th>@lang( 'product.product_name' )</th>
															<th>@lang( 'purchase.purchase_quantity' )</th>
 															<th>@lang( 'lang_v1.unit_cost_before_discount' )</th>
															<th>@lang( 'lang_v1.discount_percent' )</th>
															<th>@lang( 'purchase.unit_cost_before_tax' )</th>
															<th class="hide">@lang( 'purchase.subtotal_before_tax' )</th>
															<th class="hide">@lang( 'purchase.product_tax' )</th>
															<th class="hide">@lang( 'purchase.net_cost' )</th>
															<th>@lang( 'purchase.line_total' )</th>
															<th class="@if(!session('business.enable_editing_product_from_purchase')) hide @endif">
																@lang( 'lang_v1.profit_margin' )
															</th>

															<th>
																@lang( 'purchase.unit_selling_price' )
																<small>(@lang('product.inc_of_tax'))</small>
															</th>

															@if(session('business.enable_lot_number'))
																<th>
																	@lang('lang_v1.lot_number')
																</th>
															@endif
															
															@if(session('business.enable_product_expiry'))
																{{-- <th>
																	@lang('product.mfg_date') / @lang('product.exp_date')
																</th> --}}
															@endif

															<th><i class="fa fa-trash" aria-hidden="true"></i></th>
														</tr>
													</thead>
													<tbody></tbody>
												</table>
											</div>
											<hr/>
											{{-- total item --}}
											<div class="pull-right col-md-5">
												<table class="pull-right col-md-12">
													<tr>
														<th class="col-md-7 text-right">@lang( 'lang_v1.total_items' ):</th>
														<td class="col-md-5 text-left">
															<span id="total_quantity" class="display_currency" data-currency_symbol="false"></span>
														</td>
													</tr>
													<tr>
														<th class="col-md-7 text-right">@lang('purchase.sub_total_amount' ):</th>
														<td class="col-md-5 text-left">
															<span id="total_subtotal_id" class="display_currency"></span>
															<!-- This is total before purchase tax-->
															{!! Form::hidden('total_subtotal_input_id', 0 , ['id' => 'total_subtotal_input_id']); !!}

														</td>
													</tr>
												</table>
											</div>
											{{-- joy item --}}
											<input type="hidden" id="row_count" value="0">
											<div class="joy hide" >joy</div>
										</div>
									</div>
								@endcomponent
							{{-- additional-expense / search-box --}}
							{{-- ******************************* --}}
								@component('components.widget', ['class' => 'box-primary' , "title" => ($total >= $quantity_all)?"": __("home.add_Expense") , 'style'=>"margin:0px 10%" ])
									<div class="row @if ($total >= $quantity_all) {{"hidden"}} @endif ">
										<div class="col-xs-12">
											<table>
												<tr>
													<td>&nbsp;</td>
													<td>&nbsp;</td>
													<td>&nbsp;</td>
												<td>
													<div class="form-group">
														{!! Form::label('shipping_charges','(+) ' . __( 'purchase.supplier_shipping_charges' ) . ':') !!}
														<div class="input-group">
															<span class="input-group-btn">
																<button type="button"   class="btn btn-default bg-white btn-flat" title="@lang('unit.add_unit')" data-toggle="modal" data-target="#exampleModal"><i class="fa fa-plus-circle text-primary fa-lg"></i></button>
															</span>
															{!! Form::hidden('ADD_SHIP', 0, ['class' => 'form-control input_number' ,"id" => "total_ship_"  ]); !!}
															{!! Form::text('ADD_SHIP', 0, ['class' => 'form-control input_number' ,"id" => "total_ship_" , "disabled" ,'required']); !!}
														</div>
													</div>
													<!-- Modal -->
													<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
														@include('additional_Expense.create')
														
													</div>
												</td>
												</tr>
												{{-- purchase total --}}
												<tr>
												<td>&nbsp;</td>
												<td>&nbsp;</td>
												<td>&nbsp;</td>
												<td>
													{!! Form::hidden('final_total', 0 , ['id' => 'grand_total_hidden']); !!}
													<b>@lang('purchase.purchase_total'): </b><span id="grand_total_items" class="display_currency" data-currency_symbol='true'>0</span>
													<br>
													<br>
													{!! Form::hidden('final_total_hidden_items', 0 , ['id' => 'final_total_hidden_items']); !!}
													{!! Form::hidden('contact_id',$transaction->contact->id , ['id' => 'contact_id']); !!}
													
												</td>
												</tr>
												<tr>
												<td>&nbsp;</td>
												<td>&nbsp;</td>
												<td>&nbsp;</td>
												<td>
													<div class="form-group">
														{!! Form::label('shipping_charges','(+) ' . __( 'purchase.cost_shipping_charges' ) . ':') !!}
														<div class="input-group">
															<span class="input-group-btn">
																<button type="button"   class="btn btn-default bg-white btn-flat" title="@lang('unit.add_unit')" data-toggle="modal" data-target="#exampleModal"><i class="fa fa-plus-circle text-primary fa-lg"></i></button>
															</span>
															{!! Form::hidden('ADD_SHIP_', 0, ['class' => 'form-control input_number' ,"id" => "total_ship_c"  ]); !!}
															{!! Form::text('ADD_SHIP_', 0, ['class' => 'form-control input_number' ,"id" => "total_ship_c" , "disabled" ,'required']); !!}
														</div>
													</div>
													<!-- Modal -->
													<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
														@include('additional_Expense.create')
														
													</div>
												</td>
												</tr>
												{{-- total bill --}}
												<tr>
												<td>&nbsp;</td>
												<td>&nbsp;</td>
												<td>&nbsp;</td>
												<td>
													<br>
													<b>@lang('purchase.purchase_pay'): </b><span id="total_final_items" class="display_currency" data-currency_symbol='true'>0</span>
													
													{!! Form::hidden('total_final_items_', 0 , ['id' => 'total_final_items_']); !!}
												</td>
												</tr>
											</table>
										</div>
									</div>
								@endcomponent
							{{-- *2/2-6* section form body submit button --}}
							{{-- **************************************************************** --}}	
								<div  class="modal-footer" @if ($total >= $quantity_all) {{"hidden"}} @endif>
									<button type="submit"  @if (session()->get('user.language', config('app.locale')) == "ar") style="left-right:100px"  @else style="margin-right:50px" @endif class="btn btn-primary">@lang( 'messages.save' )</button>
								</div>
							{{-- **************************************************************** --}}

						@endif
				{{-- ********************************************** --}}

				{{-- *2/3* section form footer --}}
				{{-- ********************************************** --}}
						@if(count($array_remain)>0)
							@foreach($array_remain as $it)
								<input type="text" class="product_id_remain  hide" value="{{$it}}">
							@endforeach
						@endif
					{!! Form::close() !!}
				{{-- ********************************************** --}}

			{{-- ********************************************** --}}
		</section>
	{{-- ************************************************ --}}
	<!-- quick product modal -->
	{{-- Section modal --}}
	{{-- ********************************************** --}}
		<div class="modal fade quick_add_product_modal" tabindex="-1" role="dialog" aria-labelledby="modalTitle"></div>
		<div class="modal fade contact_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel"></div>
	{{-- ********************************************** --}}
	<!-- /.content -->
@endsection
{{-- *3* --}}
@section('javascript')
 	{{-- *1* section rolations --}}
    {{-- ************************************************************* --}}
		<script src="{{ asset('js/purchase.js?v=' . $asset_v) }}"></script>
		<script src="{{ asset('js/producte.js?v=' . $asset_v) }}"></script>
	{{-- ************************************************************* --}}
    {{-- *2* section additional --}}
    {{-- ************************************************************* --}}
		<script type="text/javascript">
			$('.currency_id_amount').change(function(){
				if($('.currency_id').val() != ""){
					update_row();
					total_bill();
				}
			})
			$('.currency_id').change(function(){
				var id = $(this).val();
				if(id == ""){
					$(".currency_id_amount").val("");
					$(".curr_column").attr("disabled",true);
					$(".curr_column").val(0);
					// $(".cur_symbol").html("Sub Total : " );
					$(".cur_symbol").addClass("hide" );
					$("#total_subtotal_cur").addClass("hide" );
					$(".i_curr").addClass("hide" );
					$("#discount_calculated_amount_cur").addClass("hide" );
					$(".t_curr").addClass("hide" );
					$("#tax_calculated_amount_curr").addClass("hide" );
					$(".o_curr").addClass("hide" );
					$("#grand_total_cur").addClass("hide" );
					$(".z_curr").addClass("hide" );
					$("#total_final_i_curr").addClass("hide" );
					$(".c_curr").addClass("hide" );
					$(".cur_check").addClass("hide" );
					$("#total_final_curr").addClass("hide" );
					$(".os_curr").addClass("hide" );
					$(".oss_curr").addClass("hide" );
					$(".add_section").addClass("hide" );
					$(".ship_curr").addClass("hide" );
					$(".check_dep_curr").addClass("hide" );
					$('input[name="dis_currency"]').prop('checked', false);
					$('#depending_curr').prop('checked', false);
					discount_cal_amount2() ;
					os_total_sub();
					os_grand();
				}else{
					$(".cur_symbol").removeClass("hide" );
					$("#total_subtotal_cur").removeClass("hide" );
					$(".curr_column").attr("disabled",false);
					$(".i_curr").removeClass("hide" );
					$("#discount_calculated_amount_cur").removeClass("hide" );
					$(".t_curr").removeClass("hide" );
					$("#tax_calculated_amount_curr").removeClass("hide" );
					$(".o_curr").removeClass("hide" );
					$("#grand_total_cur").removeClass("hide" );
					$(".z_curr").removeClass("hide" );
					$("#total_final_i_curr").removeClass("hide" );
					$(".c_curr").removeClass("hide" );
					$("#total_final_curr").removeClass("hide" );
					$(".cur_check").addClass("hide" );
					$(".os_curr").removeClass("hide" );
					$(".oss_curr").removeClass("hide" );
					$(".ship_curr").removeClass("hide" );
					$(".add_section").removeClass("hide" );
					
					
					// $(".check_dep_curr").removeClass("hide" );
					$('input[name="dis_currency"]').prop('checked', true);
					$('#depending_curr').prop('checked', true);

					$.ajax({
						url:"/symbol/amount/"+id,
						dataType: 'html',
						success:function(data){
							var object  = JSON.parse(data);
						
							$(".currency_id_amount").val(object.amount);
							$(".cur_symbol").html( @json(__('purchase.sub_total_amount')) + " " + object.symbol + " : "  );
							$(".i_curr").html( @json(__('purchase.discount')) + " " + object.symbol +" : " + "(-)");
							$(".t_curr").html( @json(__('purchase.purchase_tax')) + " " + object.symbol +" : " + "(+)" );
							$(".o_curr").html( @json(__('purchase.purchase_total')) + " " + object.symbol +" : "   );
							$(".z_curr").html( @json(__('purchase.purchase_total_')) + " " + object.symbol +" : "   );
							$(".c_curr").html( @json(__('purchase.purchase_pay')) + " " + object.symbol +" : "   );
							$(".ar_dis").html( @json(__('home.Cost without Tax currency')) + " " + object.symbol +"   "   );
							$(".ar_dis_total").html( @json(__('home.Total Currency')) + " " + object.symbol +"   "   );
							$(".br_dis").html( @json(__('lang_v1.Cost before without Tax')) + " " + object.symbol +"   "   );
							$(".header_texts").html( @json(__('Amount')) + " - Curr"    );
							$(".header_totals").html( @json(__('Total')) + " - Curr"   );
							$(".header_vats").html( @json(__('Vat')) + " - Curr"     );
							// $(".add_currency_id").val(id);
							var cuurency_id   = id ;
							var cuurency_text = object.symbol ;
							// $('.add_currency_id').val(cuurency_id).trigger('change');
							// $(".add_currency_id option").each(function(){
							// 		e = $(this);
							// 		console.log(e.attr("value"));
									
							// 		if(e.attr("value") == id){
							// 			e.attr("selected",true);
							// 		}else{
							// 			e.removeAttr("selected");
							// 		}
							// });
							// $(".add_currency_id_amount").val(object.amount);
							console.log("check if okk : " + id + " __ " + $(".add_currency_id").val());
							// $(".curr_column").removeClass("hide");
							os_total_sub();
						},
					});	 
				}
			})
			$(document).on("change",".purchase_quantity",function(){
					total_qty_items();
			});
			$(document).ready( function(){
				__page_leave_confirmation('#add_purchase_form');
				$('.paid_on').datetimepicker({
					format: moment_date_format + ' ' + moment_time_format,
					ignoreReadonly: true,
				});
			});
			$(document).on('change', '.payment_types_dropdown, #location_id', function(e) {
				var default_accounts = $('select#location_id').length ? 
							$('select#location_id')
							.find(':selected')
							.data('default_payment_accounts') : [];
				var payment_types_dropdown = $('.payment_types_dropdown');
				var payment_type = payment_types_dropdown.val();
				var payment_row = payment_types_dropdown.closest('.payment_row');
				var row_index = payment_row.find('.payment_row_index').val();

				var account_dropdown = payment_row.find('select#account_' + row_index);
				if (payment_type && payment_type != 'advance') {
					var default_account = default_accounts && default_accounts[payment_type]['account'] ? 
						default_accounts[payment_type]['account'] : '';
					if (account_dropdown.length && default_accounts) {
						account_dropdown.val(default_account);
						account_dropdown.change();
					}
				}
				

				if (payment_type == 'advance') {
					if (account_dropdown) {
						account_dropdown.prop('disabled', true);
						account_dropdown.closest('.form-group').addClass('hide');
					}
				} else {
					if (account_dropdown) {
						account_dropdown.prop('disabled', false); 
						account_dropdown.closest('.form-group').removeClass('hide');
					}    
				}
			});
			// $("#purchase_entry_table thead th:nth-child(4)").hide();
			$("<th>Store<th>").insertBefore("#purchase_entry_table thead  tr th:nth-child(11)");
			$("#purchase_entry_table thead th:nth-child(5)").hide();
			$("#purchase_entry_table thead th:nth-child(6)").hide();
			$("#purchase_entry_table thead th:nth-child(12)").hide();
			$("#purchase_entry_table thead th:nth-child(10)").hide();
			$("#purchase_entry_table thead th:nth-child(14)").hide();
			$("#purchase_entry_table thead th:nth-child(13)").hide();
			$('.remove_row').click(function(){
				$(this).parent().parent().remove();
			});
			function hide_items(){
				$("#purchase_entry_table tbody  td:nth-child(4)").hide();
				$("#purchase_entry_table tbody  td:nth-child(5)").hide();
				$("#purchase_entry_table tbody  td:nth-child(6)").hide();
				$("#purchase_entry_table tbody  td:nth-child(7)").hide();
				$("#purchase_entry_table tbody  td:nth-child(8)").hide();
				$("#purchase_entry_table tbody  td:nth-child(9)").hide();
				$("#purchase_entry_table tbody  td:nth-child(12)").hide();
				$("#purchase_entry_table tbody  td:nth-child(10)").hide();
				$("#purchase_entry_table tbody  td:nth-child(12)").hide();
				$(".expire").hide();
				
			}
			function get_purchase_entry_row(product_id, variation_id,row=null,total_remain=null) {
				if (product_id) {
					var row_count =0;
					if(row != null){
						row_count = row;
					}else{
						row_count = $('#row_count').val();
					}
					var type_send      = "received";
					var transaction_id = $('#transaction_id').val();
					var location_id    = $('#location_id').val();
					var main_store_o   =  $('#store_id option:selected').val();
					var return_type    =  $('#return_type').val();
					var types          = type_send;
		
					$.ajax({
						method: 'POST',
						url: '/purchases/get_purchase_entry_row?type_send=' + type_send + '&type_o=store&cost=cost&transaction_id='+transaction_id+'&main_store='+main_store_o+'&return_type='+return_type+'&total_remain='+total_remain+'&types=received',
						dataType: 'html',
						data: { 
							product_id:   product_id, 
							row_count:    row_count, 
							variation_id: variation_id,
							location_id:  location_id,
							return_type:  return_type,
							types:  types
						},
						success: function(result) {
							
							var val = $(result).find('.hidden_variation_id').val() ;
							$(result)
								.find('.purchase_quantity')
								.each(function() {
									
									row = $(this).closest('tr');
									$('#purchase_entry_table tbody').append(
										update_purchase_entry_row_values(row)
										
									);

									var array = [];
									var index_array = "<option value='";
									$("#store_id option").each(function () {
									
											array.push($(this).html());
										
									});
	
									var start  = 0;
									var count  = array.length;
									array.forEach(element => {
										if(start == count-1){
											index_array = index_array +element+"'>" + element + "</option>"
											start = start + 1 ; 
										
										}else{
											index_array = index_array +element+"'>" + element + "</option><option value='"
											start = start + 1 ; 
										}
									});

									var index = "<td><select  name='stop_"+product_id+"' id='stop_"+product_id+"' data-item='"+product_id+"' class='  uor'>"+ index_array + "</select></td>";
									$(index).insertBefore("#purchase_entry_table tbody  tr td:nth-child(11)");
									$("#purchase_entry_table tbody  td:nth-child(11)").hide();
									$("#purchase_entry_table tbody  td:nth-child(13)").hide();
									update_row_price_for_exchange_rate(row);
									update_inline_profit_percentage(row);
									update_table_total();
									update_grand_total();
									update_table_sr_number();
									hide_items();
									total_qty_items();
									//Check if multipler is present then multiply it when a new row is added.
									if(__getUnitMultiplier(row) > 1){
										row.find('select.sub_unit').trigger('change');
									}
								});

								$('.uor').on("change",function( ){
									var htmlString = $( this ).val();
									var idproduct = ".val_"+val+"_" +htmlString;
									$(idproduct).remove();
									$('.joy').after('<input class="input-form hide select2  val_'+val+"_" +htmlString+'" name="val_'+val+"_" +htmlString+'"  value="'+htmlString+'">'); 
							
								});
								
								if ($(result).find('.purchase_quantity').length) {
								$('#row_count').val(
									$(result).find('.purchase_quantity').length + parseInt(row_count)
								);
							}
						},
					});
				}
			}
			function sendForm(e){
				e.preventDefault();
			}
			function total_qty_items(){
				var total    = 0;
				$('.purchase_quantity').each(function () {
					var el       =  $(this).parent().parent();
					var quantity =  el.children().find(".purchase_quantity").val() ;
					var price    =  el.children().find(".purchase_cost_price").val() ;
					total       +=  price * quantity;
				});
				$("#total_subtotal_input_id").val(total);
				$("#total_subtotal_id").html(total);
				total_bill_items();
			}
			function insert_item(){
				var row = 0;
				if($(".product_id_remain").val()){
					$(".product_id_remain").each(function(){
						get_purchase_entry_row($(this).val(),$(this).val(),row,true);
						row = row + 1;
					});
					$("#items_all").remove();
				}else{
					$(".pro_id").each(function(){
						get_purchase_entry_row($(this).val(),$(this).val(),row,true);
						row = row + 1;
					})
					$("#items_all").remove();
				}
			}
			function total_bill_items(){
				var total =  $("#total_subtotal_input_id").val();       
				var ship  =  $("#total_ship_").val();       
				var ship_ =  $("#total_ship_c").val();        
				$("#total_final_i").html(parseFloat(total).toFixed(2));       
				$("#total_final_hidden_").val(parseFloat(total) + parseFloat(ship));       
				$("#grand_total_items").html((parseFloat(total) + parseFloat(ship)).toFixed(2));       
				$("#final_total_hidden_items").val((parseFloat(total) + parseFloat(ship)).toFixed(2));       
				$("#total_final_items").html((parseFloat(total) + parseFloat(ship) + parseFloat(ship_)).toFixed(2));       
				$("#total_final_items_").val((parseFloat(total) + parseFloat(ship) + parseFloat(ship_)).toFixed(2));       
				$("#grand_total2").html((parseFloat(total) + parseFloat(ship) + parseFloat(ship_)).toFixed(2));       
				$("#payment_due_").html(parseFloat($("#grand_total2").html()) - parseFloat($(".payment-amount").val()));       
				$(".hide_div").removeClass("hide");
			}
		</script>
	{{-- ************************************************************* --}}
    {{-- *3* section include --}}
    {{-- ************************************************************* --}}
		@yield('child_script')
	{{-- ************************************************************* --}} 
@endsection
