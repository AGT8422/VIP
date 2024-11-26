@extends('layouts.app')
{{-- *1* --}}
@section('title', __( 'unit.units' ))
{{-- *2* --}}
@section('content')
    <!-- Content Header (Page header) -->
    {{-- *1* section title of page --}}
    {{-- ****************************** --}}
        <section class="content-header">
            <h1><b>@lang( 'unit.units' )</b><small>@lang( 'unit.manage_your_units' )</small></h1>
            <h5><i><b>{{ "   Products  >  " }} </b>  {{ "   Units     " }} <b> {{"   "}} </b></i></h5>  
            <br> 
        </section>
    {{-- ****************************** --}}
    <!-- Main content -->
    {{-- *2* section body of page --}}
    {{-- ****************************** --}}
        <section class="content">
            {{-- */1/* section tables --}}
            <div class="row" style="margin:0px 10%">
                @component('components.widget', ['class' => 'box-primary', 'title' => __( 'unit.all_your_units' )])
                    {{-- *//1//* section permissions for create --}}
                    @can('unit.create') 
                        @slot('tool')
                            <div class="box-tools">
                                <button type="button" class="btn btn-block btn-primary btn-modal"data-href="{{action('UnitController@create')}}" data-container=".unit_modal">
                                    <i class="fa fa-plus"></i> @lang( 'messages.add' )
                                </button>
                            </div>
                        @endslot
                    @endcan
                    {{-- *//2//* section permissions for table --}}
                    @if(auth()->user()->hasRole("Admin#".session('business.id')) || auth()->user()->can('unit.view') || auth()->user()->can('warehouse.views'))
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped dataTable" id="unit_table">
                                <thead>
                                    <tr>
                                        <th>@lang( 'unit.name' )</th>
                                        <th>@lang( 'unit.short_name' )</th>
                                        <th>@lang( 'unit.allow_decimal' ) @show_tooltip(__('tooltip.unit_allow_decimal'))</th>
                                        <th>@lang( 'messages.action' )</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    @endif
                @endcomponent
            </div>
            {{-- */2/* section modals --}}
            <div class="modal fade unit_modal" tabindex="-1" role="dialog" 
                aria-labelledby="gridSystemModalLabel">
            </div>

        </section>
    {{-- ****************************** --}}
    <!-- /.content -->
@endsection
