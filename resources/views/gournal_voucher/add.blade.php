@extends('layouts.app')
@section('title',$title)
@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>{{ $title }} </h1>
</section>
<!-- Main content -->
<section class="content">
	{!! Form::open(['url' => 'gournal-voucher/add', 'method' => 'post', 'id' => 'daily_payment_form', 'files' => true ]) !!}
	<div class="box box-solid">
		<div class="box-body">
			<div class="row">
				<div class="col-md-3">
					<div class="form-group">
						{!! Form::label('location_id', __('home.Main Credit').':*') !!}
						{{ Form::select('main_account_id',$accounts,null,['class'=>'form-control select2','id'=>'main_account_id'
						     ,'placeholder'=>trans('home.please account'),'required']) }}
					</div>
				</div>
				<input type="hidden" id="list_account_checked" value="0"  >
				<input type="hidden" id="list_account" data-list="{{json_encode($accounts)}}">
				<div class="col-sm-2">
					<div class="form-group">
						<div class="checkbox">
							 
							{!! Form::checkbox('main_credit',    1  , null  , [ 'class' => 'input-icheck' , 'id' => 'main_credit']); !!} {{ __( 'home.Main Credit' ) }} 
							 <br>
							 {!! Form::text("total_credit",0,['class'=>'form-control hide','readOnly','id'=>'total_credit']) !!}
						</div>
					</div>
				</div>
				
				<div class="col-md-2">
					<div class="form-group">
						{!! Form::label('location_id', __('home.Date').':*') !!}
						{!! Form::date('gournal_date',date('Y-m-d'), ['class' => 'form-control ' ,'required']); !!}
					</div>
				</div>
				{{-- <div class="col-md-2">
					<div class="form-group">
						{!! Form::label('location_id', __('home.Document').':*') !!}
						{!! Form::file('image', ['class' => 'form-control ' ,'accept'=>"image/x-png,image/gif,image/jpeg,application/pdf"]); !!}
					</div>
				</div> --}}
				<div class="col-md-2">
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
				{{-- <td>
					<div class="form-group">
						{!! Form::label('location_id', __('home.Document').':*') !!}
						{!! Form::file('variation_images[]', ['class' => 'variation_images', 'accept' => 'image/*', 'multiple']); !!}
					  <small><p class="help-block">@lang('purchase.max_file_size', ['size' => (config('constants.document_size_limit') / 1000000)]) <br> @lang('lang_v1.aspect_ratio_should_be_1_1')</p></small>
					</div>
				</td> --}}
				<input type="hidden" id="list_account_checked" value="0">
				<input type="hidden" id="list_account" data-list="{{json_encode($accounts)}}">
				<div class="col-md-3">
					<div class="form-group">
						<div class="multi-input">
							{!! Form::label('currency_id', __('business.currency') . ':') !!}  
							<br/>
							{!! Form::select('currency_id',  $currencies, null, ['class' => 'form-control width-60 currency_id  select2', 'placeholder' => __('messages.please_select')]); !!}
							{!! Form::text('currency_id_amount', null, ['class' => 'form-control width-40 pull-right currency_id_amount'   ]); !!}
						</div>
					</div>
				</div>
				<div class="col-md-3">
					<div class="form-group">
						{!! Form::label('location_id', __('home.Cost Center').':*') !!}
						{{ Form::select('cost_center_id',$cost_centers,null,['class'=>'form-control select2'
						     ,'placeholder'=>trans('home.please account')]) }}
					</div>
				</div>
				<div class="main_note col-sm-12 hide">
					<div class="form-group">
							 {!! Form::textarea("note_main",null,['class'=>'form-control ','id'=>'note_main',"placeholder"=>"Main Account Note"]) !!}
 					</div>
				</div>
				<div class="col-md-12">
					
					<table class="table">
					  <thead>
						<tr>
						  <th>{{ trans('home.Credit') }}</th>
						  <th>{{ trans('home.Debit') }}</th>
						  <th>{{ trans('home.Amount') }}</th>
						  <th>{{ trans('home.Cost Center') }}</th>
						  <th>{{ trans('home.Tax Percentage') }}</th>
						  <th>{{ trans('home.Tax Amount') }}</th>
						  <th>{{ trans('home.Net Amount') }}</th>
						  
						  <th class="hide">{{ trans('home.Date') }}</th>
						  <th>{{ trans('home.Note') }}</th>
						  <th></th>
						</tr>
					  </thead>
					  <tbody>
						<tr >
						  <td class="col-xs-2 crd_account">
							{{ Form::select('credit_account_id[]',$accounts,null,['class'=>'form-control select2 credit_account_id',
							    'placeholder'=>trans('home.please account')]) }}
						  </td>
						  <td class="col-xs-2">
							{{ Form::select('debit_account_id[]',$expenses,null,['class'=>'form-control select2 debit_account_id','placeholder'=>trans('home.please account'),'required']) }}
						  </td>
						  <td class="col-xs-1">
							{{ Form::number('amount[]',0,['class'=>'form-control amount','required','step'=>'any','min'=>1,'onKeyUp'=>"UpdateItem(this)"]) }}
						  </td>
						  <td class="col-xs-2">
							{{ Form::select('center_id[]',$cost_centers,null,['class'=>'form-control select2 center_id','placeholder'=>trans('home.please account')]) }}
						  </td>
						  
						  <td class="col-xs-1">
							{{ Form::number('tax_percentage[]',0,['class'=>'form-control tax_percentage','required',
										'step'=>'any','min'=>0,'onKeyUp'=>"UpdateItem(this)"]) }}
						  </td>
						  <td class="col-xs-1">
							{{ Form::number('tax_amount[]',0,['class'=>'form-control tax_amount','required','step'=>'any','min'=>0,'readOnly']) }}
						  </td>
						  <td class="col-xs-1">
							{{ Form::number('net_amount[]',0,['class'=>'form-control net_amount','required','step'=>'any','min'=>0,'readOnly']) }}
						  </td>
						  <td class="col-xs-1 hide">
							{{ Form::date('date[]',date('Y-m-d'),['class'=>'form-control  ','required']) }}
						  </td>
						  <td class="col-xs-2">
							{{ Form::text('text[]',null,['class'=>'form-control ']) }}
						  </td>
						  <td class="col-xs-1 text-center">
							<span class="addBtn">
								<i class="fa fa-plus"></i>
							  </span>
						  </td>
						</tr>
						
						<tr id="addRow">
							<td class="col-xs-1"></td>
						</tr>
					  </tbody>
					</table>
					  <div class="col-md-1 pull-right">
						<button id="save_entry" class="btn btn-primary pull-right">{{ trans('home.Save') }}</button>
					  </div>
					</div>
			</div>
		</div>
	</div> <!--box end-->
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
	function formatRows(main, prefer, common) {
		return '<tr><td class="col-xs-2 crd_account">{{ Form::select('credit_account_id[]',$accounts,null,['class'=>'form-control select2 credit_account_id ','placeholder'=>trans('home.please account')]) }}</td>' +
			    '<td class="col-xs-2">{{ Form::select('debit_account_id[]',$expenses,null,['class'=>'form-control select2 debit_account_id','placeholder'=>trans('home.please account'),'required']) }}</td>' +
		        '<td class="col-xs-1">{{ Form::number('amount[]',0,['class'=>'form-control amount','required','step'=>'any','min'=>0,'onKeyUp'=>"UpdateItem(this)"]) }}</td>' +
				'<td class="col-xs-2">{{ Form::select('center_id[]',$cost_centers,null,['class'=>'form-control select2 center_id','placeholder'=>trans('home.please account')]) }}</td>' +
				'<td class="col-xs-1">{{ Form::number('tax_percentage[]',0,['class'=>'form-control tax_percentage','required','step'=>'any','min'=>0,'onKeyUp'=>"UpdateItem(this)"]) }}</td>' +
				'<td class="col-xs-1">{{ Form::number('tax_amount[]',0,['class'=>'form-control tax_amount','required','step'=>'any','min'=>0,'readOnly']) }}</td>' +
				'<td class="col-xs-1">{{ Form::number('net_amount[]',0,['class'=>'form-control net_amount','required','step'=>'any','min'=>0,'readOnly']) }}</td>' +
				'<td class="col-xs-1 hide">{{ Form::date('date[]',date('Y-m-d'),['class'=>'form-control  ','required']) }}</td>' +
				'<td class="col-xs-2">{{ Form::text('text[]',null,['class'=>'form-control ']) }}</td>' +
				'<td class="col-xs-1 text-center"><a href="#" onClick="deleteRow(this)">' +
				'<i class="fas fa-trash" aria-hidden="true"></a></td></tr>';
	};
	function deleteRow(trash) {
	    $(trash).closest('tr').remove();
		if($(".checkbox div").hasClass("checked")){
			check_accounts();
			changes();
			ifHasClass();
		}
	};
	function addRow() {
		var main = $('.addMain').val();
		var preferred = $('.addPrefer').val();
		var common = $('.addCommon').val();
		$(formatRows(main,preferred,common)).insertBefore('#addRow');
		$('.select2').select2();
		if($(".checkbox div").hasClass("checked")){
			check_accounts();
			changes();
			ifHasClass();
		}
	}
	$('.addBtn').click(function()  {
	    addRow();
		change_dropdown();
	});
	
	function UpdateItem(el) {
		var item = $(el).parent().parent();
	    var amount       =  parseFloat(item.children().find('.amount').val());
	    var percentage   =  parseFloat(item.children().find('.tax_percentage').val());
	    var net          =  amount*(percentage/(100+percentage));
	     
		$(el).closest('tr').children().find('.tax_amount').val((net).toFixed(2))  
	    $(el).closest('tr').children().find('.net_amount').val((amount - net).toFixed(2));
	}

	function total_credit(zero = null){
		var total = 0; 
		$(".amount").each(function(){
			var i = $(this).val();
			total += parseFloat(i);
		});
		if(zero!=null){
			$(".main_note").addClass("hide");
			$("#total_credit").addClass("hide");
			$("#total_credit").val(0);
		}else{
			$(".main_note").removeClass("hide");
			$("#total_credit").removeClass("hide");
			$("#total_credit").val(total);
		}
	}
	$(document).ready(function(){
			check_accounts();
			changes();
	});
	function check_accounts(){
		var main  = $("#main_account_id option:selected").val()
		var check = 0;
		var total_credit = 0;
		$(".rows").each(function(){
			var i = $(this);
				if($(this).find(".credit_account_id option:selected").val() != main ){
					check = 1;
				}
				var total = $(this).find(".amount").val();
				total_credit += parseFloat(total);
		});
		if(check==0){
			if($("#main_account_id option:selected").val() != ""){
				$(".checkbox div").addClass("checked");
				$(".checkbox div #main_credit").val(1);
				$("#total_credit").val(total_credit);
			}
		}
	}

	function ifHasClass(){
		if($(".checkbox div").hasClass("checked")){
			$(".credit_account_id").each(function(){
				$(this).find("option").each(function(){
					if($(this).val() == $("#main_account_id option:selected").val() ){
						$(this).attr("selected","selected");
						$(".crd_account .select2-selection__rendered").html($(this).html())
						$(".crd_account .select2-selection__rendered").attr("title",$(this).html())
					}else{
						 
						$(this).removeAttr("selected");
						 
					}
				});
				total_credit();
			});
		}
	}

	function changes(){
		$(".amount").each(function(){
			$(this).on("change",function(){
					ifHasClass();
			})
		}); 
		$("#main_account_id").on('change',function(){
			if($(this).val() == ""){
				if($('#list_account_checked').val() == 1){
					unCheck(1);
					change_dropdown(1);
					$('#list_account_checked').val(0);
				}else{
					change_dropdown();
				}
			}else{
				ifHasClass();
				change_dropdown();
			}
				
			
		});
	}

	$(document).on('ifChecked', 'input#main_credit', function() {
		$('#list_account_checked').val(1);
		$(this).val(1);
		Check();
		setTimeout(() => {
			change_dropdown();
		}, 100);
	});

	$(document).on('ifUnchecked', 'input#main_credit', function() {
		$('#list_account_checked').val(0);
		$(this).val(0);
		unCheck();
		setTimeout(() => {
			change_dropdown(1);
		}, 100);
	});

	function Check(){
		changes();
		$(".credit_account_id").each(function(){
 			$(this).find("option").each(function(){
				if($(this).val() == $("#main_account_id option:selected").val()){
  					$(this).attr("selected","selected");
					$(".crd_account .select2-selection__rendered").html($(this).html())
					$(".crd_account .select2-selection__rendered").attr("title",$(this).html())
				}else{
					$(this).removeAttr("selected");
				}
			});
			total_credit();
		});
	}

	// ****
	function unCheck(check=null){
 
		changes();
		if(check == null){
			$(".credit_account_id").each(function(){
				list_account = JSON.stringify($('#list_account').data('list'));
				list         = JSON.parse(list_account);
				html         = '<option value=""  selected>please select account</option>';
				for(e in list){
					html += '<option value="'+e+'">'+list[e]+'</option>';
				}
				$(this).html(html) ;
			});
		}
		// $(".credit_account_id").each(function(){
		// 	$(this).find("option").each(function(){
		// 		if($(this).val() == ""){
 		// 			$(this).attr("selected","selected");
		// 			$(".crd_account .select2-selection__rendered").html($(this).html())
		// 			$(".crd_account .select2-selection__rendered").attr("title",$(this).html())
		// 		}else{
		// 			$(this).removeAttr("selected");
		// 		}
		// 	});
		// });
		total_credit(1);
		if(check!=null){
			$(".checkbox div").removeClass("checked");
			$(".checkbox div #main_credit").prop("checked",false);
			$(".checkbox div  #main_credit").change();
			$(".checkbox div #main_credit").val(1);
		}
	}
	function change_dropdown(check=null){ 
		if($(".checkbox div").hasClass("checked")){
			if(check==null){
				$(".credit_account_id").each(function(){		
					list_account = JSON.stringify($('#list_account').data('list'));
					list         = JSON.parse(list_account);
					for(e in list){
						if(e == $("#main_account_id option:selected").val()){
							html = '<option value="'+e+'" selected>'+list[e]+'</option>';
						}
					}
					$(this).html(html) ;
				});
			}
		}
		if(check!=null){
		
			$(".credit_account_id").each(function(){
				list_account = JSON.stringify($('#list_account').data('list'));
				list         = JSON.parse(list_account);
				html         = '<option value=""  selected>please select account</option>';
				for(e in list){
					html += '<option value="'+e+'">'+list[e]+'</option>';
				}
				$(this).html(html) ;
			});
				 
		 
		}
	}
	// ****  
		  
		$('#daily_payment_form').submit(function(){
			$('#save_entry').attr('disabled','disabled');
		});

  </script>
@endsection