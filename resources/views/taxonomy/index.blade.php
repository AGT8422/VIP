@extends('layouts.app')
@php
    $heading = !empty($module_category_data['heading']) ? $module_category_data['heading'] : __('category.categories');
    $navbar = !empty($module_category_data['navbar']) ? $module_category_data['navbar'] : null;
@endphp
@section('title', $heading)

@section('content')
@if(!empty($navbar))
    @include($navbar)
@endif
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1><b>{{$heading }}</b>
        <small>
            {{ $module_category_data['sub_heading'] ?? __( 'category.manage_your_categories' ) }}
        </small>
        @if(isset($module_category_data['heading_tooltip']))
            @show_tooltip($module_category_data['heading_tooltip'])
        @endif
    </h1>
    <h5><i><b>{{ "   Products  >  " }} </b>  {{ "   Categories     " }} <b> {{"   "}} </b></i></h5>  
	<br> 
</section>

<!-- Main content -->
<section class="content">
    @php
        $cat_code_enabled = isset($module_category_data['enable_taxonomy_code']) && !$module_category_data['enable_taxonomy_code'] ? false : true;
    @endphp
    <input type="hidden" id="category_type" value="{{request()->get('type')}}">
    <div class="row">    
        <div class="col-sm-3">
            <div class="row" style="margin:0px 5%">
                @component('components.widget', ['class' => 'box-solid'])
                    <h4>@lang("warehouse.component")</h4>
                    @slot('tool')
                        <div class="box-tools">
                            <button type="button" class="btn btn-block btn-primary btn-modal" 
                            data-href="{{action('TaxonomyController@create')}}?type={{request()->get('type')}}" 
                            data-container=".category_modal">
                            <i class="fa fa-plus"></i> @lang( 'messages.add' )</button>
                        </div>
                    @endslot
                    <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                        @foreach($parents as $single)
                        <div class="panel panel-default">
                        <div class="panel-heading" role="tab" id="headingTwo{{ $single->id }}">
                            <h4 class="panel-title">
                            <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo{{ $single->id }}" aria-expanded="false" aria-controls="collapseTwo">
                                {{ $single->short_code . "  " }}   {{ $single->name }} 
                            </a>
                        </h4>
                        </div>
                        <div id="collapseTwo{{ $single->id }}" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo{{ $single->id }}">
                            <div class="panel-body">
                            @foreach($single->childs as $child)
                            <ul> 
                                <li>
                                    <a href="{{ URL::to('products?sub_category_id='.$child->id) }}">
                                        {{ $child->short_code . "  " }}   {{ $child->name }}
                                    </a>
                                </li>
                            </ul>
                            @endforeach
                            </div>
                        </div>
                        </div>
                        @endforeach
                        
                    </div>
                @endcomponent
            </div>
        </div>
        <div class="col-sm-9">
            <div class="row" style="margin:0px 0%">
                @component('components.widget', ['class' => 'box-solid'])

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped dataTable" id="category_table">
                            <thead>
                                <tr>
                                    <th>@if(!empty($module_category_data['taxonomy_label'])) {{$module_category_data['taxonomy_label']}} @else @lang( 'category.category' ) @endif</th>
                                    @if($cat_code_enabled)
                                        <th>{{ $module_category_data['taxonomy_code_label'] ?? __( 'category.code' )}}</th>
                                    @endif
                                    <th>@lang( 'lang_v1.description' )</th>
                                    <th>@lang( 'messages.action' )</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <td></td>
                                    @if($cat_code_enabled) <td></td> @endif
                                    <td></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @endcomponent
            </div>
        </div>
    </div>
    
    

    <div class="modal fade category_modal" tabindex="-1" role="dialog" 
    	aria-labelledby="gridSystemModalLabel">
    </div>

</section>
<!-- /.content -->
@stop
@section('javascript')
@includeIf('taxonomy.taxonomies_js')
@endsection
