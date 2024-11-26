@extends('layouts.app')
@section('title', __('recieved.recieved'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>{{ __('recieved.recieved') }}</h1>
    <br>
    {{-- <h2> {{ __('warehouse.add_info') }} </h2> --}}

    <!-- <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
        <li class="active">Here</li>
    </ol> -->
</section> 


<div class="row">
    <div class="col-md-12">
        
        <!-- Main content -->
        <section class="content no-print">
            @component('components.filters', ['title' => __('report.filters')])
            {{  Form::open(['url'=>'general/recieved','method'=>'GET']) }}
            <div class="col-md-3" >
                <div class="form-group">
                    {!! Form::text('ref_no', app('request')->input('ref_no'), ['class' => 'form-control  ','placeholder'=>trans('home.Ref No') ]); !!}
                </div>
            </div>
            <div class="col-md-3" >
                <div class="form-group">
                    {!! Form::select('supplier_id', $suppliers, app('request')->input('supplier_id'), ['class' => 'form-control  ','placeholder'=>trans('home.Please Choose Supplier') ]); !!}
                </div>
            </div>
            <div class="col-md-3" >
                <div class="form-group">
                    {!! Form::select('store_id', $warehouses, app('request')->input('store_id'), ['class' => 'form-control  ','placeholder' => trans('home.Choose Warehouse') ]); !!}
                </div>
            </div>
            <div class="col-md-3" >
                <div class="form-group">
                    <button type="submit" class="btn btn-primary btn-md">{{ __('home.Filter') }}</button>
                </div>
            </div>
            {{ Form::token() }}
            {{ Form::close() }}
              @endcomponent
            @component('components.widget', ['class' => 'box-primary', 'title' => __('recieved.all_recieved')])
            {{-- @slot('tool')
            <div class="box-tools">
                <a class="btn btn-block btn-primary" href="{{action('WarehouseController@create')}}">
                    <i class="fa fa-plus"></i> @lang('messages.add')</a>
                </div>
                @endslot --}}
                <div class="table-responsive">
                    <table class="table table-bordered table-striped  warehouse" style=""  ">
                        <thead>
                            <tr>
                                <th>@lang('messages.date')</th>
                                <th>@lang('purchase.ref_no')</th>
                                <th>@lang('purchase.location')</th>
                                <th>@lang('purchase.supplier')</th>
                                <th>@lang('warehouse.nameW')</th>
                                <th>@lang('messages.action')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($allData  as $data)
                                <tr>
                                    <td>
                                        {{ $data->created_at }}
                                    </td>
                                    <td>
                                        {{ $data->ref_no }}
                                    </td>
                                    <td>
                                        {{ $data->location?$data->location->name:'' }}
                                    </td>
                                    <td>
                                        {{ $data->contact?$data->contact->name:'' }}
                                    </td>
                                    <td>
                                        {{ $data->warehouse?$data->warehouse->name:'' }}
                                    </td>
                                    <td> 
                                        <a class="btn btn-sm btn-primary" href="{{ URL::to('purchases/recieved_page?ARRAY='.$data->id) }}">
                                            @lang('home.recieve')
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        
                    </table>
                </div>
                @endcomponent
            </section>
            
        
        </div>
    </div>

    
    
    
    
    
    {{-- <section id="receipt_section" class="print_section"></section> --}}
    
    @stop
    @section('javascript')

@endsection

{{--  --}}