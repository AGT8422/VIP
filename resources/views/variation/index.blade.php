@extends('layouts.app')
@section('title', __('product.variations'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1><b>@lang('product.variations')</b>
        <small>@lang('lang_v1.manage_product_variations')</small>
           
    <h5><i><b>{{ "   Products  >  " }} </b>{{ "Variations"   }} <b> {{"   "}} </b></i></h5>  
    <br>  
    </h1>
    <!-- <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
        <li class="active">Here</li>
    </ol> -->
</section>

<!-- Main content -->
<section class="content">
    <div class="row" style="margin:0px 10%">
        @component('components.widget', ['class' => 'box-primary', 'title' => __('lang_v1.all_variations')])
            @slot('tool')
                <div class="box-tools">
                    <button type="button" class="btn btn-block btn-primary btn-modal" 
                    data-href="{{action('VariationTemplateController@create')}}" 
                    data-container=".variation_modal">
                    <i class="fa fa-plus"></i> @lang('messages.add')</button>
                </div>
            @endslot
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="variation_table">
                    <thead>
                        <tr>
                            <th>@lang('product.variations')</th>
                            <th>@lang('lang_v1.values')</th>
                            <th>@lang('messages.action')</th>
                        </tr>
                    </thead>
                </table>
            </div>
        @endcomponent
    </div>
    <div class="modal fade variation_modal" tabindex="-1" role="dialog" 
    	aria-labelledby="gridSystemModalLabel">
    </div>

</section>
<!-- /.content -->

@endsection
