@extends('layouts.app')
@section('title', __('lang_v1.edit_terms'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('lang_v1.edit_terms') <i class="fa fa-keyboard-o hover-q text-muted" aria-hidden="true" data-container="body" data-toggle="popover" data-placement="bottom" data-content="@include('purchase.partials.keyboard_shortcuts_details')" data-html="true" data-trigger="hover" data-original-title="" title=""></i></h1>
</section>

<!-- Main content -->
<section class="content">

 
    {!! Form::open(['url' => action('QuotationController@update',["id" => $term->id]), 'method' => 'post', 'id' => 'Quotation_term']) !!}

 

    <div class="modal-body">
      <div class="form-group">
        {!! Form::label('name', __( 'lang_v1.name' ) . ':*') !!}
          {!! Form::text('name', $term->name, ['class' => 'form-control', 'required', 'placeholder' => __( 'lang_v1.name' ) ]); !!}
      </div>

      <div class="form-group">
        {!! Form::label('description_qutation', __( 'lang_v1.description' ) . ':') !!}
          {!! Form::textarea('description_qutation', $term->description, ['class' => 'form-control','id'=>'description_qutation', 'placeholder' => __( 'lang_v1.description' ) ]); !!}
      </div>
     
    </div>

    <div class="modal-footer">
      <button type="submit" class="btn btn-primary">@lang( 'messages.update' )</button>
      {{-- <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button> --}}
    </div>

    {!! Form::close() !!}



</section>
<!-- quick product modal -->
<div class="modal fade quick_add_product_modal" tabindex="-1" role="dialog" aria-labelledby="modalTitle"></div>
 
<!-- /.content -->
@endsection

@section('javascript')
   
@endsection
