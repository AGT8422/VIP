@extends('layouts.app')
@section('title', __('home.Create_system_account'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('home.Create_system_account')</h1>
</section>

<!-- Main content -->
<section class="content">
	{!! Form::open(['url' => 'account/system/add', 'method' => 'post', 'id' => 'add_expense_form', 'files' => true ]) !!}
	<div class="box box-solid">
		<div class="box-body">
			<div class="row">
				<div class="col-sm-4">
					<div class="form-group">
						{!! Form::label('pattern_id', __('business.enter pattern name').':*') !!} 
						{!! Form::select('pattern_id', $patterns,null, ["required", 'class' => 'form-control select2', 'placeholder' => __('messages.please_select')]); !!}
					</div>
				</div>
			</div>
			<div class="row">&nbsp;<hr></div>
			<div class="row">
				<div class="col-sm-4">
					<div class="form-group">
						{!! Form::label('contact_id', __('home.Purchase').':*') !!} 
						{!! Form::select('purchase', $accounts,null, ["required", 'class' => 'form-control select2', 'placeholder' => __('messages.please_select')]); !!}
					</div>
				</div>
                <div class="col-sm-4">
					<div class="form-group">
						{!! Form::label('contact_id', __('home.Purchase Tax').':*') !!} 
						{!! Form::select('purchase_tax', $accounts,null, ["required", 'class' => 'form-control select2', 'placeholder' => __('messages.please_select')]); !!}
					</div>
				</div>
                <div class="col-sm-4">
					<div class="form-group">
						{!! Form::label('contact_id', __('home.Cheque Debit').':*') !!} 
						{!! Form::select('cheque_debit', $accounts,null, ["required", 'class' => 'form-control select2', 'placeholder' => __('messages.please_select')]); !!}
					</div>
				</div>
                <div class="col-sm-4">
					<div class="form-group">
						{!! Form::label('contact_id', __('home.Sale').':*') !!} 
						{!! Form::select('sale', $accounts,null, ["required", 'class' => 'form-control select2', 'placeholder' => __('messages.please_select')]); !!}
					</div>
				</div>
                <div class="col-sm-4">
					<div class="form-group">
						{!! Form::label('contact_id', __('home.Sale Tax').':*') !!} 
						{!! Form::select('sale_tax', $accounts,null, ["required", 'class' => 'form-control select2', 'placeholder' => __('messages.please_select')]); !!}
					</div>
				</div>
                <div class="col-sm-4">
					<div class="form-group">
						{!! Form::label('contact_id', __('home.Cheque Collection').':*') !!} 
						{!! Form::select('cheque_collection', $accounts,null, ["required", 'class' => 'form-control select2', 'placeholder' => __('messages.please_select')]); !!}
					</div>
				</div>
                <div class="col-sm-4">
					<div class="form-group">
						{!! Form::label('contact_id', __('home.Journal Expense Tax').':*') !!} 
						{!! Form::select('journal_expense_tax', $accounts,null, ["required", 'class' => 'form-control select2', 'placeholder' => __('messages.please_select')]); !!}
					</div>
				</div>
				<div class="col-sm-4">
					<div class="form-group">
						{!! Form::label('contact_id', __('home.Sale Return').':*') !!} 
						{!! Form::select('sale_return', $accounts,null, ["required", 'class' => 'form-control select2', 'placeholder' => __('messages.please_select')]); !!}
					</div>
				</div>
                <div class="col-sm-4">
					<div class="form-group">
						{!! Form::label('contact_id', __('home.Sale Discount').':*') !!} 
						{!! Form::select('sale_discount', $accounts,null, ["required", 'class' => 'form-control select2', 'placeholder' => __('messages.please_select')]); !!}
					</div>
				</div>
				<div class="col-sm-6">
					<div class="form-group">
						{!! Form::label('contact_id', __('home.Purchase Return').':*') !!} 
						{!! Form::select('purchase_return', $accounts,null, ["required", 'class' => 'form-control select2', 'placeholder' => __('messages.please_select')]); !!}
					</div>
				</div>
                <div class="col-sm-6">
					<div class="form-group">
						{!! Form::label('contact_id', __('home.Purchase Discount').':*') !!} 
						{!! Form::select('purchase_discount', $accounts,null, ["required", 'class' => 'form-control select2', 'placeholder' => __('messages.please_select')]); !!}
					</div>
				</div>
				
				<div class="clearfix"></div>
			    <div class="col-sm-12">
					<div class="row">&nbsp;<hr></div>
                    <button type="submit" class="btn btn-primary pull-right">@lang('messages.save')</button>
                </div>
			</div>
		</div>
	</div> <!--box end-->
	
	
{!! Form::close() !!}
</section>

@endsection