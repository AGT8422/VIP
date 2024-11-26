@extends('layouts.app')
@section('title', __('lang_v1.sell_return'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header no-print">
    <h1>@lang('lang_v1.sell_return')</h1>
</section>

<!-- Main content -->
<section class="content no-print">

	{!! Form::hidden('location_id', $sell->location->id, ['id' => 'location_id', 'data-receipt_printer_type' => $sell->location->receipt_printer_type ]); !!}
	{!! Form::open(['url' => action('SellReturnController@store'), 'method' => 'POST', 'id' => 'sell_return_form','files' => true ]) !!}
	{!! Form::hidden('transaction_id', $sell->id); !!}
	<div class="box box-solid">
		<div class="box-header">
			<h3 class="box-title">@lang('lang_v1.parent_sale')</h3>
		</div>
		<div class="box-body">
			<div class="row">
				<div class="col-sm-4">
					<strong>@lang('sale.invoice_no'):</strong> {{ $sell->invoice_no }} <br>
					<strong>@lang('messages.date'):</strong> {{@format_date($sell->transaction_date)}}
				</div>
				<div class="col-sm-4">
					<strong>@lang('contact.customer'):</strong> {{ $sell->contact->name }} <br>
					<strong>@lang('purchase.business_location'):</strong> {{ $sell->location->name }}
				</div>
				<div class="col-sm-4">
					{{ trans('home.Cost Center') }} : {!! ($sell->cost_center)?$sell->cost_center->name:' ' !!}
				</div>
				<div class="col-sm-12 hide">
					<div class="form-group">
					{!! Form::label('document_expense[]', __('purchase.attach_document') . ':') !!}
					{!! Form::file('document_expense[]', ['multiple','id' => 'upload_document', 'accept' =>
					implode(',', array_keys(config('constants.document_upload_mimes_types')))]); !!}
					<p class="help-block">
						@lang('purchase.max_file_size', ['size' => (config('constants.document_size_limit') / 1000000)])
						@includeIf('components.document_help_text')
					</p>
					</div>
				</div>
				 
			</div>
		</div>
	</div>
	<div class="box box-solid">
		<div class="box-body">
			<div class="row">
				<div class="col-sm-4">
					<div class="form-group">
						{!! Form::label('invoice_no', __('sale.invoice_no').':') !!}
						{!! Form::text('invoice_no', !empty($sell->return_parent->invoice_no) ? $sell->return_parent->invoice_no : null, ['class' => 'form-control']); !!}
					</div>
					
				</div>
				<div class="col-sm-4">
					<div class="form-group">
						<div class="multi-input">
						  {!! Form::label('currency_id', __('business.currency') . ':') !!}  
						  <br/>
 						  {!! Form::select('currency_id', $currencies, ($sell->return_parent)?$sell->return_parent->currency_id:null, ['class' => 'form-control width-60 currency_id  select2', 'placeholder' => __('messages.please_select') ]); !!}
						  {!! Form::text('currency_id_amount', ($sell->return_parent)?$sell->return_parent->exchange_price:null, ['class' => 'form-control width-40 pull-right currency_id_amount'   ]); !!}
						</div>
					</div>
				</div>
				<input type="hidden" name="cost_center_id" value="{{$sell->cost_center_id}}">
				<div class="col-sm-3">
					<div class="form-group">
						{!! Form::label('transaction_date', __('messages.date') . ':*') !!}
						<div class="input-group">
							<span class="input-group-addon">
								<i class="fa fa-calendar"></i>
							</span>
							@php
								$transaction_date = !empty($sell->return_parent->transaction_date) ? $sell->return_parent->transaction_date : 'now';
							@endphp
							{!! Form::text('transaction_date', @format_datetime($transaction_date), ['class' => 'form-control', 'readonly', 'required']); !!}
						</div>
					</div>
				</div>
				
				<div class="col-sm-12">
					<table class="table bg-gray" id="sell_return_table">
			          	<thead>
				            <tr class="bg-green">
				              	<th>#</th>
				              	<th>@lang('product.product_name')</th>
				              	<th>@lang('sale.unit_price')</th>
								  <th @if($sell->return_parent)@if($sell->return_parent->exchange_price > 0) class="curr_column br_dis  cur_check  " @else class="curr_column br_dis  cur_check hide" @endif  @else class="curr_column br_dis  cur_check hide" @endif>@lang( 'lang_v1.Cost before without Tax' ) @if($sell->return_parent)@if($sell->return_parent->exchange_price > 0) {{  $sell->return_parent->currency->symbol }} @endif @endif</th>
				              	<th>@lang('lang_v1.sell_quantity')</th>
				              	<th>@lang('lang_v1.return_quantity')</th>
				              	<th>@lang('lang_v1.return_subtotal')</th>
				            </tr>
				        </thead>
				        <tbody>
				          	@foreach($sell->sell_lines as $sell_line)
				          		@php
					                $check_decimal = 'false';
					                if($sell_line->product->unit->allow_decimal == 0){
					                    $check_decimal = 'true';
					                }
					                $unit_name = $sell_line->product->unit->short_name;
					                if(!empty($sell_line->sub_unit)) {
					                	$unit_name = $sell_line->sub_unit->short_name;

					                	if($sell_line->sub_unit->allow_decimal == 0){
					                    	$check_decimal = 'true';
					                	} else {
					                		$check_decimal = 'false';
					                	}
					                }
									$dis_amount =  $sell->discount_amount;
									if ($sell->discount_type == 'fixed_after_vat') {
										$dis_amount        =  ($dis_amount *100)/105;
									}elseif ($sell->discount_type == 'percentage') {
										$dis_amount        =  $sell->total_before_tax*($dis_amount/100);
									}
									$transaction = \App\Transaction::where("return_parent_id",$sell_line->transaction->id)->first();
									$id = (!empty($transaction))?$transaction->id:null;
 									$itemMove    = \App\Models\ItemMove::orderBy("id","desc")->whereIn("state",["sale","sell_return"])->where("transaction_id",$sell_line->transaction->id)->where("unit_cost",">",0)->first();
									 
									$prices      =  (!empty($itemMove)) ?  round($itemMove->out_price,4)  : $sell_line->unit_price;
									$price_item  =  (!empty($itemMove) && $prices == 0 ) ? round($itemMove->row_price_inc_exp,4) : $prices     ;
									$unit_price  = ($sell_line->unit_price -  ($sell_line->unit_price/$sell->total_before_tax)*$dis_amount);
					            @endphp
				            <tr>
				              	<td>{{ $loop->iteration }}</td>
				              	<td>
				                	{{ $sell_line->product->name }}
				                 	@if( $sell_line->product->type == 'variable')
				                  	- {{ $sell_line->variations->product_variation->name}}
				                  	- {{ $sell_line->variations->name}}
				                 	@endif
				              	</td>
				              	<td> <input min="1"  name="products[{{$loop->index}}][unit_price_]" class=" unit_price_  form-control" value="{{$price_item}}"></td>
				              	<td @if($sell->return_parent) @if(  $sell->return_parent->exchange_price > 0   )  class="   cur_check  " @else class=" cur_check hide" @endif @else class=" cur_check hide" @endif>
									<input   name="products[{{$loop->index}}][unit_price_before_dis_exc_new_currency]" class="form-control unit_price_before_dis_exc_new_currency"  value="{{( $sell->return_parent  )?(( $sell->return_parent->exchange_price > 0   )?round($price_item / $sell->return_parent->exchange_price):0):0}}">
								</td>
								<td>{{ $sell_line->formatted_qty }} {{$unit_name}}</td>
				              	
				              	<td>
						            <input type="text" name="products[{{$loop->index}}][quantity]" value="{{@format_quantity($sell_line->quantity_returned)}}"
						            class="form-control input-sm input_number return_qty input_quantity"
						            data-rule-abs_digit="{{$check_decimal}}" 
						            data-msg-abs_digit="@lang('lang_v1.decimal_value_not_allowed')"
			              			data-rule-max-value="{{$sell_line->quantity}}"
			              			data-msg-max-value="@lang('validation.custom-messages.quantity_not_available', ['qty' => $sell_line->formatted_qty, 'unit' => $unit_name ])" 
						            >
						            <input name="products[{{$loop->index}}][unit_price]" type="hidden" class="unit_price" value="{{@num_format($unit_price)}}">
						            <input name="products[{{$loop->index}}][sell_line_id]" type="hidden" value="{{$sell_line->id}}">
				              	</td>
				              	<td>
				              		<div class="return_subtotal"></div>
				              	</td>
				            </tr>
				          	@endforeach
			          	</tbody>
			        </table>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-12 text-right">
					<strong @if($sell->return_parent)@if($sell->return_parent->exchange_price > 0 ) class="  cur_symbol" @else class="hide cur_symbol" @endif @else class="hide cur_symbol" @endif>@lang('purchase.purchase_pay')@if($sell->return_parent) @if($sell->return_parent->exchange_price > 0 ) {{$sell->return_parent->currency->symbol}} @endif @endif : </strong>&nbsp;
					<span id="total_subtotal_cur" @if($sell->return_parent)@if($sell->return_parent->exchange_price > 0 ) class="display_currency    " @else class="display_currency hide" @endif @else class="display_currency hide" @endif></span>
					<!-- This is total before purchase tax-->				
					<b>&nbsp;&nbsp;&nbsp;&nbsp;</b>
					<input type="hidden" id="total_subtotal_input_cur" value=0  name="total_before_tax_cur">
					<strong>@lang('purchase.sub_total_amount'): </strong>&nbsp;
					<span id="sub_return">0</span> 
				</div>
			</div>
			<h5>&nbsp;</h5>
			<div class="row">
				<div class="col-sm-4  ">
				</div>
				<div class="col-sm-4 text-right">
					<div class="form-group">
						@if($sell->return_parent != null)
							@php 
 							 $returned = \App\Transaction::where("id",$sell->return_parent->id)->first(); 
							@endphp
							@if($returned != null)
								<strong> @lang( 'purchase.discount_type' ) : </strong>&nbsp;
								{!! Form::select('discount_type', [ '' => __('lang_v1.none'), 'fixed_before_vat' => __( 'home.fixed before vat' ), 'fixed_after_vat' => __( 'home.fixed after vat' ), 'percentage' => __( 'lang_v1.percentage' )], ($returned->discount_type!=null)?$returned->discount_type:"", ['class' => ' form-control select2 discount_t',"id"=>"discount_type",'style'=>'width:30%;float:right']); !!}
							@else 
							<strong> @lang( 'purchase.discount_type' ) : </strong>&nbsp;
							{!! Form::select('discount_type', [ '' => __('lang_v1.none'), 'fixed_before_vat' => __( 'home.fixed before vat' ), 'fixed_after_vat' => __( 'home.fixed after vat' ), 'percentage' => __( 'lang_v1.percentage' )],  "", ['class' => ' form-control select2 discount_t',"id"=>"discount_type",'style'=>'width:30%;float:right']); !!}
							@endif
						@else 	
								<strong> @lang( 'purchase.discount_type' ) : </strong>&nbsp;
								{!! Form::select('discount_type', [ '' => __('lang_v1.none'), 'fixed_before_vat' => __( 'home.fixed before vat' ), 'fixed_after_vat' => __( 'home.fixed after vat' ), 'percentage' => __( 'lang_v1.percentage' )],  "", ['class' => ' form-control select2 discount_t',"id"=>"discount_type",'style'=>'width:30%;float:right']); !!}
						
						@endif
					</div>
				</div>
				@php
					$discount_type = !empty($sell->return_parent->discount_type) ? $sell->return_parent->discount_type : $sell->discount_type;
					$discount_amount = !empty($sell->return_parent->discount_amount) ? $sell->return_parent->discount_amount : $sell->discount_amount;
					
					if(isset($returned) ){
						$dis_amount_  =  $returned->discount_amount;
						$dis_amount_r =  $returned->discount_amount;
						if ($returned->discount_type == 'fixed_after_vat') {
							$dis_amount_r        =  ($dis_amount_r *100)/105;
						}elseif ($returned->discount_type == 'percentage') {
							$dis_amount_r        =   $returned->total_before_tax*($dis_amount_r/100) ;
						}
					}else{
						$dis_amount_         =  0;
						$dis_amount_r        =  0;
					}
				
				@endphp
			 
				<div class="col-sm-4 text-right">
					<div class="form-group">
						<strong> @lang( 'purchase.discount_amount' ) : </strong>&nbsp;
 						{!! Form::text('discount_amount', $dis_amount_, ['class' => 'form-control input_number','id'=>'discount_amount','style'=>'width:30%;float:right' ]); !!}
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-4  ">
  				</div>
				<div class="col-sm-4  ">
  				</div>
				<div class="col-sm-4 text-right">
					<div class="form-group">
						<b  @if($sell->return_parent) @if($sell->return_parent->exchange_price > 0 )  class="i_curr  " @else class="i_curr hide" @endif @else class="i_curr hide" @endif>@lang( 'purchase.discount' ) @if($sell->return_parent  ) @if($sell->return_parent->exchange_price > 0 ) {{  ($sell->return_parent->currency->symbol) }} @endif @endif : (-) </b> 
						<span id="discount_calculated_amount_cur" @if($sell->return_parent) @if($sell->return_parent->exchange_price > 0 )    class="display_currency" @else  class=" display_currency hide" @endif @else  class=" display_currency hide" @endif>{{($sell->return_parent  )?(($sell->return_parent->exchange_price > 0 )?round($dis_amount_r / $sell->return_parent->exchange_price):0):0}}</span>
						<b>&nbsp;&nbsp;&nbsp;&nbsp;</b>
						<b>@lang( 'purchase.discount' ):</b> (-) 
						<span id="dis_final" class="display_currency">{{0}}</span>
					</div>
				</div>
			</div>
			<h5>&nbsp;</h5>
			<div class="h-section">
			    
			@php
				$tax_percent = 0;
				if(!empty($sell->tax)){
					$tax_percent = $sell->tax->amount;
				}
			@endphp
			{!! Form::hidden('old_dis',0, ['id' => 'old_dis']); !!}
			{!! Form::hidden('last_discount',0, ['id' => 'last_discount']); !!}
			{!! Form::hidden('total_rt_sale',0, ['id' => 'total_rt_sale']); !!}
			{!! Form::hidden('sub_total_rt_sale',0, ['id' => 'sub_total_rt_sale']); !!}
			{!! Form::hidden('last_dis',0, ['id' => 'last_dis']); !!}
			{!! Form::hidden('tax_id', $sell->tax_id,["data-tax_amount" => $tax_percent]); !!}
			{!! Form::hidden('tax_amount', 0, ['id' => 'tax_amount']); !!}
			{!! Form::hidden('tax_percent', $tax_percent, ['id' => 'tax_percent']); !!}
			</div>

			<div class="row">
				<div class="col-sm-12 text-right">
					<b @if($sell->return_parent) @if($sell->return_parent->exchange_price > 0 )  class="o_curr " @else class="o_curr hide" @endif @else class="o_curr hide" @endif >@lang('lang_v1.total_return_a_discount'): </b>
					<span id="grand_total_cur" @if($sell->return_parent) @if($sell->return_parent->exchange_price > 0 ) class="display_currency  " @else class="display_currency hide" @endif @else class="display_currency hide" @endif  >0</span>
					<b>&nbsp;&nbsp;&nbsp;&nbsp;</b>
					<strong>@lang('lang_v1.total_return_a_discount'):</strong> 
					&nbsp;(-) <span id="total_return_discount"></span>
				</div>
				<h5>&nbsp;</h5>
				<div class="col-sm-12 text-right">
					<b  @if($sell->return_parent)  @if($sell->return_parent->exchange_price > 0 )  class="t_curr  " @else class="t_curr hide" @endif @else class="t_curr hide" @endif  >@lang( 'lang_v1.total_return_tax' ) @if($sell->return_parent)  @if($sell->return_parent->exchange_price > 0 ) {{$sell->return_parent->currency->symbol}} @endif @endif: (+)</b> 
					<span id="tax_calculated_amount_curr" @if($sell->return_parent) @if($sell->return_parent->exchange_price > 0 )  class="display_currency  "  @else class="display_currency hide"@endif @else class="display_currency hide"@endif >0</span></td>
					<b>&nbsp;&nbsp;&nbsp;&nbsp;</b>
					<strong>@lang('lang_v1.total_return_tax') - @if(!empty($sell->tax))({{$sell->tax->name}} - {{$sell->tax->amount}}%)@endif : </strong> 
					&nbsp;(+) <span id="total_return_tax"></span>
				</div>
				<h5>&nbsp;</h5>
				<div class="col-sm-12 text-right">
					<strong @if($sell->return_parent) @if($sell->return_parent->exchange_price > 0 )  class="z_curr  " @else class="z_curr hide" @endif @else class="z_curr hide" @endif >@lang('lang_v1.return_total')   @if($sell->return_parent)  @if($sell->return_parent->exchange_price > 0 ) {{$sell->return_parent->currency->symbol}} @endif @endif: </strong>&nbsp;
					<span id="total_final_i_curr"  @if($sell->return_parent) @if($sell->return_parent->exchange_price > 0 )  class="display_currency  " @else class="display_currency hide"@endif @else class="display_currency hide"@endif >0</span> 
					<b>&nbsp;&nbsp;&nbsp;&nbsp;</b>
					<strong>@lang('lang_v1.return_total'): </strong>&nbsp;
					<span id="net_return">0</span> 
				</div>
			</div>
			<br>
			<div class="row">
				<div class="col-sm-12">
					<button type="submit" class="btn btn-primary pull-right">@lang('messages.save')</button>
 				</div>
			</div>
		</div>
	</div>
	{!! Form::close() !!}

</section>
@stop
@section('javascript')
<script src="{{ asset('js/printer.js?v=' . $asset_v) }}"></script>
<script src="{{ asset('js/sell_return.js?v=' . $asset_v) }}"></script>
<script type="text/javascript">
	$(document).ready( function(){
		$('form#sell_return_form').validate();
		update_sell_return_total();
		all_changes();
		//Date picker
	    // $('#transaction_date').datepicker({
	    //     autoclose: true,
	    //     format: datepicker_date_format
	    // });
	});
	$(document).on('change', 'input.return_qty, #discount_amount,#discount_type, #discount_type,input.unit_price', function(){
		update_sell_return_total()
	});
	$('.currency_id_amount').change(function(){
		if($('.currency_id').val() != ""){
			// os_total_sub();
			update_row();
			update_row_add();
			all_changes();
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
				$(".cur_check").removeClass("hide" );
				

				$.ajax({
					url:"/symbol/amount/"+id,
					dataType: 'html',
					success:function(data){
						var object  = JSON.parse(data);
					 
						$(".currency_id_amount").val(object.amount);
 						$(".cur_symbol").html( @json(__('purchase.sub_total_amount')) + " " + object.symbol + " : "  );
 						$(".i_curr").html( @json(__('purchase.discount')) + " " + object.symbol +" : " + "(-)");
 						$(".t_curr").html( @json(__('lang_v1.total_return_tax')) + " " + object.symbol +" : " + "(+)" );
 						$(".o_curr").html( @json(__('lang_v1.total_return_a_discount')) + " " + object.symbol +" : "   );
 						$(".z_curr").html( @json(__('lang_v1.return_total')) + " " + object.symbol +" : "   );
 						$(".c_curr").html( @json(__('purchase.purchase_pay')) + " " + object.symbol +" : "   );
 						$(".ar_dis").html( @json(__('home.Cost without Tax currency')) + " " + object.symbol +"   "   );
 						$(".ar_dis_total").html( @json(__('home.Total Currency')) + " " + object.symbol +"   "   );
 						$(".br_dis").html( @json(__('lang_v1.Cost before without Tax')) + " " + object.symbol +"   "   );
						 
						// $(".curr_column").removeClass("hide");
						update_row();
						update_row_add();
						all_changes();
						update_sell_return_total();
					},
				});	 
			}
	})
	function update_sell_return_total(){
		var net_return = 0;
		$('table#sell_return_table tbody tr').each( function(){
			var quantity = __read_number($(this).find('input.return_qty'));
			var unit_price = __read_number($(this).find('input.unit_price'));
			var subtotal = quantity * unit_price;
			$(this).find('.return_subtotal').text(__currency_trans_from_en(subtotal, true));
			net_return += subtotal;
		});

		var discount_type =  $('#discount_type').val();
 		var tax_percent   = $('input#tax_percent').val();
 		var currancy      = $('.currency_id_amount').val();

		 
		if(discount_type == "") {
			var discount_percent = 0;
			discounted_net_return = net_return - discount_percent;
			$('span#dis_final').text(__currency_trans_from_en(discount_percent, true));
			if(currancy != "" && currancy != null){
				$('#discount_calculated_amount_cur').text( (discount_percent/currancy).toFixed(4));
			}
			$('#last_discount').val(discount_percent);
			$('#last_dis').val(discount_percent);
 			 
 		}else if(discount_type == "fixed_before_vat") {
			var discount_percent  =  $('#discount_amount').val();
 			discounted_net_return = net_return - discount_percent;
			$('span#dis_final').text(__currency_trans_from_en(discount_percent, true));
			if(currancy != "" && currancy != null){
				$('#discount_calculated_amount_cur').text( (discount_percent/currancy).toFixed(4));
			}
			$('#last_discount').val(discount_percent);
			$('#last_dis').val(discount_percent);
			 
 		}else if(discount_type == "fixed_after_vat") {
			var dis      =  $('#discount_amount').val();
			var between  =  dis*(tax_percent/(100+parseFloat(tax_percent)));
			var discount_percent =  dis - between ;
 			discounted_net_return = net_return - discount_percent;
			$('span#dis_final').text(__currency_trans_from_en(discount_percent, true));
			if(currancy != "" && currancy != null){
				$('#discount_calculated_amount_cur').text( (discount_percent/currancy).toFixed(4));
			}
			$('#last_discount').val(discount_percent);
			$('#last_dis').val(dis);
 
 		}else if(discount_type == "percentage") {
			var dis      =  $('#discount_amount').val();
			var discount_percent =  net_return*(parseFloat(dis)/(100));
 			discounted_net_return = net_return - discount_percent;
			$('span#dis_final').text(__currency_trans_from_en(discount_percent, true));
			if(currancy != "" && currancy != null){
				$('#discount_calculated_amount_cur').text( (discount_percent/currancy).toFixed(4));
			}
			$('#last_discount').val(discount_percent);
			$('#last_dis').val(dis);
 
		}

		 

		var total_tax = __calculate_amount('percentage', tax_percent, discounted_net_return);
		var net_return_inc_tax = total_tax + discounted_net_return;

// 		$('input#tax_amount').val(total_tax);
		$('input#tax_amount').remove();
		html = '<input id="tax_amount" name="tax_amount" type="hidden" value="'+total_tax+'" wfd-invisible="true">'
		$(".h-section").append(html);
		$('span#total_return_discount').text(__currency_trans_from_en(discounted_net_return, true));
		$('span#total_return_tax').text(__currency_trans_from_en(total_tax, true));
		$('span#net_return').text(__currency_trans_from_en(net_return_inc_tax, true));
		$('span#sub_return').text(__currency_trans_from_en( net_return, true));
		currancy     = $(".currency_id_amount").val();
		if(currancy != "" && currancy != null){
			$('#total_subtotal_cur').text((net_return/currancy).toFixed(4));
			$('#grand_total_cur').text((discounted_net_return/currancy).toFixed(4));
			$('#tax_calculated_amount_curr').text((total_tax/currancy).toFixed(4));
			$('#total_final_i_curr').text((net_return_inc_tax/currancy).toFixed(4));
		}
		$('#total_rt_sale').val(net_return_inc_tax);
		$('#sub_total_rt_sale').val(net_return);
  
	}
	function all_changes(){
		$(".return_qty").each(function(){
			var i = $(this);
			var el = i.parent().parent();
			el.children().find(".pos_quantity").on("change",function(){
				update_row();  
			})
			 
			el.parent().children().find(".unit_price_").on("change",function(){
				update_row_add();  
			})
			
			el.parent().children().find(".unit_price_before_dis_inc").on("change",function(){
				type = 1;
				update_row(type);   
			})
			el.parent().parent().children().find(".unit_price_before_dis_exc_new_currency").on("change",function(){
					var  el    =  $(this).parent().parent();
					var price  =  parseFloat($(this).val());
					var vat    = 0;
					var discount_percentage = el.children().find('.discount_percent_return').val();
					if ($('#tax_id option:selected').data('tax_amount')) {
						var vat =  parseFloat($('#tax_id option:selected').data('tax_amount'));
					}
					var x   =  price + ((vat/100)*price);
					var dap = (price * (discount_percentage/100));
					var px  =  price - dap ;
					// console.log(x+'vatss'); 
					// console.log(px+'dissss');
					el.children().find('.discount_amount_return').val(dap.toFixed(2));
					var vl = $(this).val();
					var e  = $(this).parent().parent();
					// var el = e.children().find(".purchase_unit_cost_with_tax_new_currency");
					var inc_vat      = e.children().find(".unit_price_before_dis_inc");
					var exc_vat      = e.children().find(".unit_price_");
					var exc_dis_vat  = e.children().find(".unit_price_after_dis_exc");
					var inc_dis_vat  = e.children().find(".unit_price_after_dis_inc");
					var exc_dis_vat_curr  = e.children().find(".unit_price_after_dis_exc_new_currency");
					var tax_amount   = parseFloat($('#tax_id option:selected').data('tax_amount')) ;
					var tax_rate     = parseFloat($('option:selected', $('#tax_id')).data('tax_amount'));
					var dis_amount   = ( parseFloat(e.children().find('.discount_amount_return').val()) > 0 )?parseFloat(e.children().find('.discount_amount_return').val()) :0;
					var discount     = parseInt($('input[name="dis_type"]:checked').val());
					
					currancy = $(".currency_id_amount").val();
					if(currancy != "" && currancy != 0){
						var  unit_tax   = ((tax_amount/100)*vl) + parseFloat(vl);
						var  percent    = currancy*vl;
						var  percent_tax    = ((tax_amount/100)*(currancy*vl)) + parseFloat(currancy*vl);
						exc_vat.val(percent.toFixed(2)); 
						inc_vat.val(percent_tax.toFixed(2));
					} 
					var tax_price      = parseFloat(exc_vat.val()) + parseFloat((tax_rate/100)*exc_vat.val());
					
					var amount         = exc_vat.val()  -   dis_amount ;
					var tax_amount_    = tax_price  -   dis_amount ;
					
					
					exc_dis_vat.val(amount.toFixed(4));
					inc_dis_vat.val(((tax_amount/100)*(amount) + parseFloat(amount)).toFixed(2));
					exc_dis_vat_curr.val((amount.toFixed(4)/currancy).toFixed(4));
					update_row_add();
					update_sell_return_total();

			})
			$(".discount_amount_return").on("change",function(){
				var el  = $(this).parent().parent();
				var ele = $(this).val();
				var  i  = el.children().find(".unit_price_").val();
				
				el.children().find(".discount_percent_return").val((ele*100/i).toFixed(2));
				update_row(); 
				update_sell_return_total();
				update_row_add();
			})
			$(".discount_percent_return").on("change",function(){
				var el  = $(this).parent().parent();
				var ele = $(this).val();
				var  i  = el.children().find(".unit_price_").val();
				// console.log(ele*i/100);  
				el.children().find(".discount_amount_return").val((ele*i/100).toFixed(2));
				update_row();
				update_row_add();
				update_sell_return_total();

			})
		})
	}
	function update_row_add(){
		$(".return_qty").each(function(){
			var e  = $(this);
			var el = $(this).parent().parent();
			var unit_price = el.children().find(".unit_price_");
			unit_price_currency = el.children().find(".unit_price_before_dis_exc_new_currency");
			unit_price_currency = $("#total_subtotal_input_cur");
			unit_price_currency = el.children().find(".unit_price_before_dis_exc_new_currency");
			unit_price_currency = el.children().find(".unit_price_before_dis_exc_new_currency");
 			var currancy = $(".currency_id_amount").val();
 			if( currancy != "" && currancy != null ){
 				unit_price_currency.val((unit_price.val()/currancy).toFixed(4));
			}



		});
	}
</script>
@endsection
