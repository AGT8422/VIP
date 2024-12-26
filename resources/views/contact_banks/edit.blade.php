@extends('layouts.app')
@section('title', __('home.Edit Contact Bank'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1 class="font_text">@lang('home.Edit Contact Bank')</h1>
	@php $mainUrl = '/contact-banks'; $subUrl = "/contact-banks/add";  @endphp  
    <h5 class="font_text"><i><b class="font_text"><a  class="font_text"href="{{\URL::to($mainUrl)}}">{{ __("home.Contact Banks") }} {{ __("izo.>") . " " }}</b> <b>{{ __("izo.list_of")  ." ".__("home.Contact Banks")   }} {{ __("izo.>") . " " }} </b></a> {{ __("home.Edit Contact Bank") }}  </i></h5>
 
</section>

<!-- Main content -->
<section class="content font_text">
	{!! Form::open(['url' => 'contact-banks/edit/'.$data->id, 'method' => 'post', 'id' => 'add_expense_form', 'files' => true ]) !!}
	<div class="box box-solid">
		<div class="box-body">
			<div class="row">
				<div class="col-sm-6">
					<div class="form-group">
						{!! Form::label('name', __('home.Name').':*') !!}
						{!! Form::text('name',$data->name, ['class' => 'form-control ',  'required']); !!}
					</div>
				</div>
				<div class="col-sm-6 hide">
					<div class="form-group">
						{!! Form::label('location_id', __('purchase.business_location').':*') !!}
						{!! Form::select('location_id', $business_locations,$data->location_id, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'required']); !!}
					</div>
				</div>
				{{-- <div class="col-sm-4">
					<div class="form-group">
						{!! Form::label('contact_id', __('lang_v1.expense_for_contact').':') !!} 
						{!! Form::select('contact_id', $contacts, $data->contact_id, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select')]); !!}
					</div>
				</div> --}}
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