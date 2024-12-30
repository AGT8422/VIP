@extends('layouts.app')
@section('title', __('home.Cheque'))

@section('content')

<style>
	.imgs_ul{padding: 0;}
	.imgs_ul li{display: inline;position: relative;}
	.imgs_ul .close_item{position: absolute;z-index: 100;font-size: 22px;color: red;cursor: pointer;}
	.show_eye{
	  position: absolute;
	  bottom: 0;
	  z-index: 10;
	  font-size: 24px;
	}
  </style>

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('home.Cheque') : ( #{{$data->ref_no}} )</h1>
</section>

<!-- Main content -->
<section class="content">
	{!! Form::open(['url' => 'cheque/edit/'.$data->id, 'method' => 'post', 'id' => 'add_expense_form', 'files' => true ]) !!}
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
				<div class="col-sm-2">
					<div class="form-group">
						{!! Form::label('location_id', __('home.Cheque No').':*') !!}
						{!! Form::text('cheque_no',$data->cheque_no, ['class' => 'form-control ',  'required']); !!}
					</div>
				</div>
				<div class="col-sm-2">
					<div class="form-group">
						{!! Form::label('location_id', __('home.Amount').':*') !!}
						{!! Form::number('amount',$data->amount, ['class' => 'form-control amount','step'=>'any','required']); !!}
					</div>
				</div>
				<div class="col-md-2">
					<div class="form-group">
						<div class="multi-input">
							{!! Form::label('currency_id', __('business.currency') . ':') !!}  
							<br/>
							{!! Form::select('currency_id', $currencies, ($data->currency_id)?$data->currency_id:null, ['class' => 'form-control width-60 currency_id  select2', 'placeholder' => __('messages.please_select') ]); !!}
							{!! Form::text('currency_id_amount', ($data->currency_id)?$data->exchange_price:null, ['class' => 'form-control width-40 pull-right currency_id_amount'   ]); !!}
						</div>
					</div>
				</div>
				<div class="col-sm-2">
					<div class="form-group">
						{!! Form::label('location_id', __('home.Amount Currency').':*') !!}
						{!! Form::text('amount_currency',1, ['class' => 'form-control amount_currency ','step'=>'any' ,'required','min'=>1]); !!}
					</div>
				</div>
				<input name="account_id" type="hidden" value="{{ (app('request')->input('type') == 0)?12:13 }} ">
				{{-- <div class="col-sm-4">
					<div class="form-group">
						{!! Form::label('account_id', __('home.Account').':*') !!}
						{!! Form::select('account_id', $accounts,$data->account_id, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'required']); !!}
					</div>
				</div> --}}
					@if(count($business_locations) > 0)
					<?php  $x =  array_keys($business_locations->toArray()); ?> 
						<input name="location_id" type="hidden" value="{{ $x[1] }} ">
					@endif
					<input name="type" type="hidden" value="{{ $data->type }} ">
				<div class="col-sm-4">
					<div class="form-group">
						{!! Form::label('contact_id', __('home.Contact').':') !!} 
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
						{!! Form::select('contact_id', $account_list, $id_, ['class' => 'form-control select2','id'=>'contact_id', 'placeholder' => __('messages.please_select')]); !!}
					</div>
					
					<!-- Modal -->
					<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" 
							aria-labelledby="exampleModalLabel" aria-hidden="true">	
							@include('cheques.partials.transaction_edit')	
					</div> 
				</div>
				<div class="col-sm-4">
					<div class="form-group">
						{!! Form::label('bank_id', __('home.Bank').':') !!} 
						{!! Form::select('bank_id', $banks, $data->contact_bank_id, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select')]); !!}
					</div>
				</div>
				{{-- <div class="col-sm-4">
					<div class="form-group">
						{!! Form::label('type', __('home.Cheque Type').':') !!} 
						{!! Form::select('cheque_type', $types, $data->cheque_type, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select')]); !!}
					</div>
				</div> --}}
				<div class="col-sm-4">
					<div class="form-group">
						{!! Form::label('type', __('home.Write Date').':') !!} 
						{!! Form::date('write_date', $data->write_date, ['class' => 'form-control ','required','id'=>'write_date','max'=>date('Y-m-d')]); !!}
					</div>
				</div>
				<div class="col-sm-4">
					<div class="form-group">
						{!! Form::label('type', __('home.Due Date').':') !!} 
						{!! Form::date('due_date', $data->due_date, ['class' => 'form-control ' ,'required','id'=>'due_date' ,"max" ]); !!}
					</div>
				</div>
				<div class="col-sm-12">
					<div class="form-group">
						{!! Form::label('note', __('lang_v1.note').':') !!} 
						{!! Form::textarea('note',$data->note, ['class' => 'form-control ']); !!}
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

			    <div class="col-sm-12">
            		<button type="submit" id="sub_cheque" class="btn btn-primary pull-right">@lang('messages.update')</button>
            	</div>
			</div>
		</div>
	</div> <!--box end-->
	
	
{!! Form::close() !!}
</section>
@endsection
@section('javascript') 
      <script type="text/javascript">
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
		$('.open_bills').click(function(){
			var amount =  parseFloat($('input[name="amount"]').val());
			var remain =  parseFloat($('input[name="amount"]').val());
			$('#total').text(amount);
		})
		var oTable = null;
		var cheque_type =  '{{ $data->type }}';
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
		$(document).ready(function(){
			var contact_id =  $('#contact_id option:selected').val();
			oTable 		   = 	$('#example').DataTable( {
									"ajax": "/cheque/bills?contact_id={{ $data->contact_id }}&type="+cheque_type+'&account_id='+contact_id,
									"pageLength" : -1,
									"columns": [
										{ "data": "check" },
										{"data":"date"},
										{"data":"ref_no"},
										{ "data": "name" },
										{ "data": "status" },
										{"data":'payment_status'},
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
				alert('{{ trans("home.The Number Off Paid Bills is more then cheque ammount please review choosen bills") }}')
				e.preventDefault();
			}
			
		})

		function correct(){
			date   =  new Date($("#write_date").val());
            date2  =  new Date($("#due_date").val());
			if(date.getFullYear() <= "2000" || date.getFullYear() > "2099"){
				return false ;
			}
			if(date2.getFullYear() > "2099" || date2.getFullYear() <= "2000"){
				return false ;
			}
			 
			return true;
		}
		$(document).on("change","#due_date,#write_date",function(){
            date   =  new Date($(this).val());
            date2  =  new Date();
			result =  correct();
			console.log(result);
            if(result == false){
				submit = $("#sub_cheque").attr("disabled","disabled");	
                $(this).css({
                    "-webkit-box-shadow":"0px 0px 10px  red",
                    "-moz-box-shadow":"0px 0px 10px  red",
                    "-o-box-shadow":"0px 0px 10px  red",
                    "box-shadow":"0px 0px 10px  red",
                    "border":"1px solid red",
                    "transaction":".3s ease-in", 
                })
            }else  {
				submit = $("#sub_cheque").removeAttr("disabled");	
                $("input[type='date']").css({
                    "-webkit-box-shadow":"0px 0px 10px  transparent",
                    "-moz-box-shadow":"0px 0px 10px  transparent",
                    "-o-box-shadow":"0px 0px 10px  transparent",
                    "box-shadow":"0px 0px 10px  transparent",
                    "border":"1px solid #f1f1f1",
                    "transaction":".3s ease-in", 
                })
			}
            console.log(date.getFullYear() + " -----  " + date2.getFullYear());
        });
		
	  </script>
      @yield('child_script')
@endsection