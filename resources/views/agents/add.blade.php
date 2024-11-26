@extends('layouts.app')
@section('title', __('home.Agents'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('home.Agents')</h1>
</section>

<!-- Main content -->
<section class="content">
	{!! Form::open(['url' => 'agents/add', 'method' => 'post', 'id' => 'add_expense_form', 'files' => true ]) !!}
	<div class="box box-solid">
		<div class="box-body">
			<div class="row">
				<div class="col-sm-6">
					<div class="form-group">
						{!! Form::label('location_id', __('home.Name').':*') !!}
						{!! Form::text('name',null, ['class' => 'form-control ',  'required']); !!}
					</div>
				</div>
				<div class="col-sm-6">
					<div class="form-group">
						{!! Form::label('location_id', __('home.Phone').':*') !!}
						{!! Form::text('phone',null, ['class' => 'form-control ',  'required']); !!}
					</div>
				</div>
				<div class="clearfix"></div>
			
			</div>
		</div>
	</div> <!--box end-->
	
	<div class="col-sm-12">
		<button type="submit" class="btn btn-primary pull-right">@lang('messages.save')</button>
	</div>
{!! Form::close() !!}
</section>

@endsection