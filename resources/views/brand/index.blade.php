@extends('layouts.app')
@section('title', 'Brands')

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1><b>@lang( 'brand.brands' )</b>
        <small>@lang( 'brand.manage_your_brands' )</small>
    </h1>
    <h5><i><b>{{ "   Products  >  " }} </b>  {{ "   Brands     " }} <b> {{"   "}} </b></i></h5>  
	<br> 
</section>

<!-- Main content -->
<section class="content"> 
        @component('components.widget', ['class' => 'box-primary', 'title' => __( 'brand.all_your_brands' )])
            @can('brand.create')
                @slot('tool')
                    <div class="box-tools">
                        <button type="button" class="btn btn-block btn-primary btn-modal" 
                            data-href="{{action('BrandController@create')}}" 
                            data-container=".brands_modal">
                            <i class="fa fa-plus"></i> @lang( 'messages.add' )</button>
                    </div>
                @endslot
            @endcan
        
                <div class="table-responsive">
                    <table class="table table-bordered table-striped dataTable" id="brands_table">
                        <thead>
                            <tr>
                                <th>@lang( 'brand.brands' )</th>
                                <th>@lang( 'brand.note' )</th>
                                <th>@lang( 'messages.action' )</th>

                            </tr>
                        </thead>
                    </table>
                </div>
             
        @endcomponent 

    <div class="modal fade brands_modal" tabindex="-1" role="dialog" 
    	aria-labelledby="gridSystemModalLabel">
    </div>

</section>
<!-- /.content -->

@endsection
