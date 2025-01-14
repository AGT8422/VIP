@extends('layouts.app')
@section('title', __('home.Voucher').$data->ref_no)

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('home.Voucher') {{ $data->ref_no }}</h1>
</section>

<!-- Main content -->
<section class="content">
	{!! Form::open(['url' => 'payment-voucher/edit/'.$data->id, 'method' => 'post', 'id' => 'add_expense_form', 'files' => true ]) !!}
	<div class="box box-solid">
		<div class="box-body">
			<div class="row"> 
				{{-- <div class="col-sm-4">
					<div class="form-group">
						{!! Form::label('ref_no', __('purchase.ref_no').':') !!}
						@show_tooltip(__('lang_v1.leave_empty_to_autogenerate'))
						{!! Form::text('ref_no', $data->ref_no, ['class' => 'form-control']); !!}
					</div>
				</div> --}}
				<div class="col-sm-3">
					<div class="form-group">
						{!! Form::label('location_id', __('home.Amount').':*') !!}
						{!! Form::text('amount',$data->amount, ['class' => 'form-control amount ','id'=>'amount_current','step'=>'any' ,'required']); !!}
					</div>
				</div>
				<div class="col-md-3">
					<div class="form-group">
						<div class="multi-input">
							{!! Form::label('currency_id', __('business.currency') . ':') !!}  
							<br/>
							{!! Form::select('currency_id', $currencies, $data->currency_id, ['class' => 'form-control width-60 currency_id  select2', 'placeholder' => __('messages.please_select') ]); !!}
							{!! Form::text('currency_id_amount', $data->exchange_price, ['class' => 'form-control width-40 pull-right currency_id_amount'   ]); !!}
						</div>
					</div>
				</div>
				<div class="col-sm-3">
					<div class="form-group">
						{!! Form::label('location_id', __('home.Amount Currency').':*') !!}
						{!! Form::text('amount_currency',$data->currency_amount, ['class' => 'form-control amount_currency ','step'=>'any'  ,'min'=>1]); !!}
					</div>
				</div>
				<input hidden name="type" type="text" value="{{ (app('request')->input('type') == 1)?1:0 }} ">
				@if($data->is_invoice != null)
					<input hidden name="separate" id="separate" type="text" value="{{$data->is_invoice}}">
				@endif
				<div  @if($data->is_invoice != null)  class="col-sm-3 hide" @else  class="col-sm-3 " @endif>
					<div class="form-group">
						{!! Form::label('contact_id', __('home.Contact').':*') !!} 
						<button type="button" class="btn open_bills btn-info btn-xs" data-toggle="modal" data-target="#exampleModal">
							<i class="fa fa-paper-plane" aria-hidden="true"></i>
						</button>
						@php
							$id_ = null; 
							if($data->account_type == 0){
								$account = \App\Account::where("contact_id",$data->contact_id)->first();
								if($account){
									$id_ = $account->id; 
								} 
							}else{
								$account = \App\Account::where("id",$data->contact_id)->first();
								if($account){
									$id_ = $account->id; 
								} 
							}
						@endphp
						{!! Form::select('contact_id', $account_list, $id_, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select') , 'required'] ); !!}
					</div>
				</div>
				<div class="col-sm-3">
					<div class="form-group">
						{!! Form::label('account_id', __('home.Account').':*') !!} 
						{!! Form::select('account_id', $accounts, $data->account_id, ['class' => 'form-control select2', 'required'] ); !!}
					</div>
				</div>
				<div class="col-sm-3">
					<div class="form-group">
						{!! Form::label('location_id', __('home.Date').':*') !!}
						{!! Form::date('date',$data->date,['class' => 'form-control','id'=>'date','required']); !!}
					</div>
				</div>
				<div class="col-sm-12">
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
				<div class="col-sm-12">
					<div class="form-group">
						{!! Form::label('type', __('home.Note').':') !!} 
						{!! Form::textarea('text', $data->text , ['class' => 'form-control ','required']); !!}
					</div>
				</div>
				
				<div class="clearfix"></div>
				<div class="col-sm-12">
					<button type="submit" class="btn btn-primary pull-right">@lang('messages.update')</button>
				</div>
				<div class="clearfix"></div>
				<div class="col-sm-6">
         
			{{-- <ul class="imgs_ul">
			    @if(!empty($data) )
				
 			       @if( $data->document != null )
					 @foreach($data->document as $doc)
					   <li>
							<?php $ar =  explode('.',$doc)  ?>
							<a onclick="$(this).parent().remove()" class="close_item">X</a>
							@if ($ar[1]  == 'pdf')
							<a href="{{ URL::to($doc) }}" target="_blank">
							  <i class="fa fa-eye show_eye"></i>
							</a>
							  <iframe  src="{{ URL::to($doc) }}" frameborder="0" width="100" height="100"></iframe>
							@else
							  <a href="{{ URL::to($doc) }}" target="_blank">
								<img src="{{ URL::to($doc) }}" class="img-thumbnail"> 
							  </a>
							  @endif
						   <input type="hidden" name="old_document[]" value="{{ $doc }}">
					   </li>
					   
				    @endforeach
			     @endif
			   @endif
				   </ul> --}}
				   
				   
			 </div>
			
			</div>
		</div>
	</div> <!--box end-->
	<input type="text" hidden name="type_send" id="type_send" value="{{$ID_TYPE}}">
	<!-- Modal -->
	<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" 
		aria-labelledby="exampleModalLabel" aria-hidden="true">	
		@include('cheques.partials.transaction_edit')	
	</div> 
	
{!! Form::close() !!}
</section>
@endsection
@section('javascript') 
      <script>
		 
		$('.open_bills').click(function(){
			var amount =  parseFloat($('input[name="amount"]').val());
			var remain =  parseFloat($('input[name="amount"]').val());
			$('#total').text(amount);
		})
		var oTable = null;
		 
		var cheque_type =   $("#type_send").val()   ;
		$('#contact_id').change(function(){
			var contact_id =  $('#contact_id option:selected').val();
			if (oTable != null ) 
			{
				oTable.clear().draw();
				$.get('/cheque/bills?contact_id='+contact_id+'&type='+cheque_type+'&account_id='+contact_id,function(data){
					$.each(data.data,function(index,value){
						oTable.row.add(value).draw();
					})
				});
			}
		})
		$('#amount_current').change(function(){
			remain();
			$('.choose_ul').html("");
			$('input[type="checkbox"]').each(function(){
				$(this).prop("checked",false);
			});
		});
		$(document).on("change","#contact_id",function(){
            var id =  $("#contact_id").val();   
            var i  =  1;   
			remain(i,id);
		});
		$(document).ready(function(){
			var contact_id =  $('#contact_id option:selected').val();
			oTable = 	$('#example').DataTable( {
								"ajax": "/cheque/bills?contact_id={{ $data->contact_id }}&type="+cheque_type+'&account_id='+contact_id,
								"pageLength" : -1,
								"columns": [
									{ "data": "check" },
									{ "data": "date"},
									{ "data": "ref_no"},
									{ "data": "name" },
									{ "data": "status" },
									{ "data": 'payment_status'},
									{ "data": "store_name" },
									{ "data": "grand_total" },
									{ "data": "due" },
									{ "data": "view" }
								]
				        	} );        
		})
		$('#add_expense_form').submit(function(e){
			var margin =  remain();
			if (margin < 0) {
				separate = $("#separate").val();
				if(separate == null){	
					alert('{{ trans("The Number Off Paid Bills is more then cheque ammount please review choosen bills") }}')
					e.preventDefault();
				}
			}
			
		})
		$('.currency_id_amount').change(function(){
			update_currency();
		})
		$('.currency_id').change(function(){
				var id = $(this).val();
				if(id == ""){
					$(".currency_id_amount").val("");
					$(".curr_column").addClass("hide");
				}else{
					$.ajax({
							url:"/symbol/amount/"+id,
						dataType: 'html',
						success:function(data){
							var object  = JSON.parse(data);
							$(".currency_id_amount").val(object.amount);
							// $(".curr_column").removeClass("hide");
							update_currency();
						},
					});	 
				}
		})
		$('.amount_currency').change(function(){
				var id = $(this).val();
				var currency = $('.currency_id_amount').val(); 
				if(currency != ""){
					$('.amount').val((id*currency).toFixed(4));
				}else{
					$('.amount').val((id).toFixed(4));
				}
				 
		})
		$('.amount').change(function(){
				var id = $(this).val();
				var currency = $('.currency_id_amount').val(); 
				if(currency != ""){
					$('.amount_currency').val((id/currency).toFixed(4));
				}else{
					$('.amount_currency').val((id).toFixed(4));
				}
				 
		})
		function update_currency(){
			var currency        = $('.currency_id_amount').val(); 
			var amount          = $(".amount") ;
			var amount_currency = $(".amount_currency") ;
			if(currency != "" && currency != 0){
				amount_currency.val((amount.val()/currency).toFixed(4));				
			}else{
				amount_currency.val(0);				
			}
		}

	  </script>
      @yield('child_script')
@endsection