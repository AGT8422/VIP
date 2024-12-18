@extends('layouts.app')
@section('title', __('home.Edit_system_account'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('home.Edit_system_account')</h1>
</section>

<!-- Main content -->
<section class="content">
	{!! Form::open(['url' => 'account/system/update/'.$data->id, 'method' => 'post', 'id' => 'add_expense_form', 'files' => true ]) !!}
	<div class="box box-solid">
		<div class="box-body">
			<div class="row">
				<div class="col-sm-4">
					<div class="form-group">
					    
						{!! Form::label('pattern_id', __('business.enter pattern name').':*') !!} 
						{!! Form::select('pattern_id', $patterns,($data)?$data->pattern_id:null, ["required", 'class' => 'form-control select2', "disabled",'placeholder' => __('messages.please_select')]); !!}
					</div>
				</div>
			</div>
			<div class="row">&nbsp;<hr></div>
			<div class="row">
				<div class="col-sm-4">
					<div class="form-group">
						{!! Form::label('contact_id', __('home.Purchase').':*') !!} 
						{!! Form::select('purchase', $accounts,($data)?$data->purchase:null, ["required", 'class' => 'form-control select2', 'placeholder' => __('messages.please_select')]); !!}
					</div>
				</div>
                <div class="col-sm-4">
					<div class="form-group">
						{!! Form::label('contact_id', __('home.Purchase Tax').':*') !!} 
						{!! Form::select('purchase_tax', $accounts,($data)?$data->purchase_tax:null, ["required", 'class' => 'form-control select2', 'placeholder' => __('messages.please_select')]); !!}
					</div>
				</div>
                <div class="col-sm-4">
					<div class="form-group">
						{!! Form::label('contact_id', __('home.Cheque Debit').':*') !!} 
						{!! Form::select('cheque_debit', $accounts, ($data)?$data->cheque_debit:null, ["required", 'class' => 'form-control select2', 'placeholder' => __('messages.please_select')]); !!}
					</div>
				</div>
                <div class="col-sm-4">
					<div class="form-group">
						{!! Form::label('contact_id', __('home.Sale').':*') !!} 
						{!! Form::select('sale', $accounts,($data)?$data->sale:null, ["required", 'class' => 'form-control select2', 'placeholder' => __('messages.please_select')]); !!}
					</div>
				</div>
                <div class="col-sm-4">
					<div class="form-group">
						{!! Form::label('contact_id', __('home.Sale Tax').':*') !!} 
						{!! Form::select('sale_tax', $accounts,($data)?$data->sale_tax:null, ["required", 'class' => 'form-control select2', 'placeholder' => __('messages.please_select')]); !!}
					</div>
				</div>
                <div class="col-sm-4">
					<div class="form-group">
						{!! Form::label('contact_id', __('home.Cheque Collection').':*') !!} 
						{!! Form::select('cheque_collection', $accounts,($data)?$data->cheque_collection:null, ["required", 'class' => 'form-control select2', 'placeholder' => __('messages.please_select')]); !!}
					</div>
				</div>
                <div class="col-sm-4">
					<div class="form-group">
						{!! Form::label('contact_id', __('home.Journal Expense Tax').':*') !!} 
						{!! Form::select('journal_expense_tax', $accounts,($data)?$data->journal_expense_tax:null, ["required", 'class' => 'form-control select2','id'=>'journal_expense_tax', 'placeholder' => __('messages.please_select')]); !!}
					</div>
				</div>
				<div class="col-sm-4">
					<div class="form-group">
						{!! Form::label('contact_id', __('home.Sale Return').':*') !!} 
						{!! Form::select('sale_return', $accounts,($data)?$data->sale_return:null, ["required", 'class' => 'form-control select2', 'placeholder' => __('messages.please_select')]); !!}
					</div>
				</div>
                <div class="col-sm-4">
					<div class="form-group">
						{!! Form::label('contact_id', __('home.Sale Discount').':*') !!} 
						{!! Form::select('sale_discount', $accounts,($data)?$data->sale_discount:null, ["required", 'class' => 'form-control select2', 'placeholder' => __('messages.please_select')]); !!}
					</div>
				</div>
				<div class="col-sm-6">
					<div class="form-group">
						{!! Form::label('contact_id', __('home.Purchase Return').':*') !!} 
						{!! Form::select('purchase_return', $accounts,($data)?$data->purchase_return:null, ["required", 'class' => 'form-control select2', 'placeholder' => __('messages.please_select')]); !!}
					</div>
				</div>
                <div class="col-sm-6">
					<div class="form-group">
						{!! Form::label('contact_id', __('home.Purchase Discount').':*') !!} 
						{!! Form::select('purchase_discount', $accounts,($data)?$data->purchase_discount:null, ["required", 'class' => 'form-control select2', 'placeholder' => __('messages.please_select')]); !!}
					</div>
				</div>
				
				<div class="clearfix"></div>
			    <div class="col-sm-12">
					<div class="row">&nbsp;<hr></div>
                    <button type="submit" class="btn btn-primary pull-right">@lang('messages.update')</button>
                </div>
			</div>
		</div>
	</div> <!--box end-->
	{!! Form::close() !!}
</section>

@endsection
@section("javascript")
	<script type="text/javascript">
	    old_journal_expense_tax = $("#journal_expense_tax").val();
		$("#journal_expense_tax").on("change",function(){
			new_journal_expense_tax = $(this).val();
		 
			swal({
				title:LANG.sure,
				text:"Change The Journal Expense Account",
				icon: 'warning',
				buttons: true,
				dangerMode: true,
			}).then(willDelete=>{
				if(willDelete){
					$.ajax({
						method: 'GET',
						url: "/expense/change-account",
						dataType: 'json',
						data: {
							old_account:old_journal_expense_tax,
							new_account:new_journal_expense_tax,
						},
						success: function(result) {
							if (result.success == true) {
								toastr.success(result.msg); 
								$("#add_expense_form").submit()
							} else {
								toastr.error(result.msg);
							}
						},
					});
				}
			});
		});
	</script>
@endsection