@extends('layouts.app')
@section('title',$title)

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
	@php $c_type = (app('request')->input('type') == 1)?1:0;  $title_rec = __('home.Receipt voucher') ; $title_pay = __('home.Payment Voucher') @endphp
    <h1 class="font_text">{{ ($c_type == 1)?$title_rec:$title_pay }} </h1>
	@php $mainUrl = '/payment-voucher'; $subUrl = "/payment-voucher/add?type=".$c_type;  @endphp  
    <h5 class="font_text"><i><b class="font_text"><a  class="font_text"href="{{\URL::to($mainUrl)}}">{{ __("home.Vouchers List") }} {{ __("izo.>") . " " }}</b>    </a> {{ ($c_type == 1)?$title_rec:$title_pay}}  </i></h5>
	
</section>

<!-- Main content -->
<section class="content font_text">
	{!! Form::open(['url' => 'payment-voucher/add', 'method' => 'post', 'id' => 'add_expense_form', 'files' => true ]) !!}
	<div class="box box-solid">
		<div class="box-body">
			<div class="row">
				{{-- <div class="col-sm-4">
					<div class="form-group">
						{!! Form::label('ref_no', __('purchase.ref_no').':') !!}
						@show_tooltip(__('lang_v1.leave_empty_to_autogenerate'))
						{!! Form::text('ref_no', null, ['class' => 'form-control']); !!}
					</div>
				</div> --}}
				
				<div class="col-sm-3">
					<div class="form-group">
						{!! Form::label('location_id', __('home.Amount').':*') !!}
						{!! Form::text('amount',1, ['class' => 'form-control amount font_number','id'=>'amount_current','step'=>'any' ,'required','min'=>1]); !!}
					</div>
				</div>
				<div class="col-md-3">
					<div class="form-group">
						<div class="multi-input">
							{!! Form::label('currency_id', __('business.currency') . ':') !!}  
							<br/>
							{!! Form::select('currency_id', $currencies, null, ['class' => 'form-control width-60 currency_id  select2', 'placeholder' => __('messages.please_select') ]); !!}
							{!! Form::text('currency_id_amount', null, ['class' => 'form-control width-40 pull-right currency_id_amount'   ]); !!}
						</div>
					</div>
				</div>
				<div class="col-sm-3">
					<div class="form-group">
						{!! Form::label('location_id', __('home.Amount Currency').':*') !!}
						{!! Form::text('amount_currency',1, ['class' => 'form-control amount_currency font_number','step'=>'any'  ,'min'=>1]); !!}
					</div>
				</div>
				<input hidden name="type" type="text" value="{{ (app('request')->input('type') == 1)?1:0 }} ">
				<div class="col-sm-3">
					<div class="form-group">
						{!! Form::label('contact_id', __('home.Contact').':*') !!} 
						<button type="button" class="btn open_bills btn-info btn-xs" data-toggle="modal" data-target="#exampleModal">
							<i class="fa fa-paper-plane" aria-hidden="true"></i>
						</button>
						{!! Form::select('contact_id', $account_list, null, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select') , 'required'] ); !!}
					</div>
					
				</div>
				<div class="col-sm-3">
					<div class="form-group">
						{!! Form::label('account_id', __('home.Account').':*') !!} 
						{!! Form::select('account_id', $accounts, null, ['class' => 'form-control select2', 'required','id'=>'contact_id'] ); !!}
					</div>
				</div>
				<div class="col-sm-3">
					<div class="form-group">
						{!! Form::label('location_id', __('home.Date').':*') !!}
						{!! Form::date('date',null, ['class' => 'form-control ' ,'id'=>'date','required']); !!}
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
						{!! Form::textarea('text', $title , ['class' => 'form-control ','required']); !!}
					</div>
				</div>
				<div class="clearfix"></div>
				<div class="col-sm-12">
					<button type="submit" class="btn btn-primary pull-right">@lang('messages.save')</button>
				</div>
			</div>
		</div>
	</div> <!--box end-->
	<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" 
							aria-labelledby="exampleModalLabel" aria-hidden="true">	
							@include('cheques.partials.transaction')	
					</div> 
	
{!! Form::close() !!}
</section>

@endsection
@section('javascript') 
      <script>
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
		var cheque_type =  {{ (app('request')->input('type') == 1)?0:1  }};
		$('#amount_current').change(function(){
			remain();
			$('.choose_ul').html("");
		});
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
			else {    
				oTable = 	$('#example').DataTable( {
								"ajax": "/cheque/bills?contact_id="+contact_id+'&type='+cheque_type+'&account_id='+contact_id,
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
			}
		})
		$('#add_expense_form').submit(function(e){
			var margin =  remain();
			if (margin < 0) {
				e.preventDefault();
			}
			
		})
		
	  </script>
      @yield('child_script')
@endsection