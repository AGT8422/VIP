@extends('layouts.app')
@section('title',$title)

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>{{ $title }}</h1>
</section>

<!-- Main content -->
<section class="content">
	{!! Form::open(['url' => 'account/cost-center/edit/'.$data->id, 'method' => 'post', 'id' => 'add_expense_form', 'files' => true ]) !!}
	<div class="box box-solid">
		<div class="box-body">
			<div class="row">
				<div class="col-sm-6">
					<div class="form-group">
						{!! Form::label('location_id', __('home.Name').':*') !!}
						{!! Form::text('name',$data->name, ['class' => 'form-control ',  'required']); !!}
					</div>
				</div>
				<div class="col-sm-6">
					<div class="form-group">
						{!! Form::label('location_id', __('home.Cost Center Number').':*') !!}
						{!! Form::text('account_number',$data->account_number, ['class' => 'form-control ',  'required']); !!}
					</div>
				</div>
                <div class="col-sm-12">
					<div class="form-group">
						{!! Form::label('location_id', __('home.Note').':*') !!}
						{!! Form::textarea('note',$data->note, ['class' => 'form-control ',  'required']); !!}
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