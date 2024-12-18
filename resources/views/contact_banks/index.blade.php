@extends('layouts.app')
@section('title', __('home.Contact Bank'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header no-print">
    <h1>@lang('home.Contact Bank')
        <small></small>
    </h1>
    <!-- <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
        <li class="active">Here</li>
    </ol> -->
</section>

<!-- Main content -->
<section class="content no-print">
    @component('components.filters', ['title' => __('report.filters')])
        <form action="{{ URL::to('contact-banks') }}" method="GET">
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('purchase_list_filter_location_id',  __('purchase.business_location') . ':') !!}
                    {!! Form::select('location_id', $business_locations, app('request')->input('location_id'), ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
                </div>
            </div>
            {{-- <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('purchase_list_filter_supplier_id',  __('home.Contact') . ':') !!}
                    {!! Form::select('contact_id', $contacts, app('request')->input('contact_id'), ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
                </div>
            </div> --}}
            <div class="col-md-3">
                <label for="purchase_list_filter_location_id" class="label-control" style="width: 100%">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>
               <button type="submit" class="btn btn-md btn-primary">  Filter</button>
            </div>
            <div class="col-md-3">
                <label for="purchase_list_filter_location_id" class="label-control" style="width: 100%">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>
               <a href="{{ URL::to('/contact-banks/add') }}"  class="btn btn-md btn-primary">@lang("messages.add")</a>
            </div>
        </form>
        
        <div class="col-md-12">
            <table class="table table-bordered ">
                <tbody>
                   <tr style="background:#f1f1f1;">
                      <th>@lang('home.Bank Name')</th>
                      <th>@lang('purchase.business_location')</th>
                      <th>@lang('home.Action')</th>
                     
                   </tr>
                   @foreach ($allData as $item)
                   <tr style="border:1px solid #f1f1f1;">
                    <td>{{ $item->name}}</td>
                    <td>{{  $item->location?$item->location->name:'-------' }}</td>
                    
                    <td>
                        <a href="{{ URL::to('contact-banks/edit/'.$item->id) }}"
                            class="btn  btn-md btn-primary">
                            {{ trans('home.Edit') }}
                        </a>
                        @if( request()->session()->get("user.id") == 1)
                            <!-- Button trigger modal -->
                            <button type="button" class="btn btn-danger" data-toggle="modal" 
                                data-target="#exampleModal{{ $item->id }}">
                            {{ trans('home.Delete') }}
                            </button>
                        @endif
                        
                        <!-- Modal -->
                        <div class="modal fade" id="exampleModal{{ $item->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">@lang('home.Alert')</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                </div>
                                <div class="modal-body">
                                  @lang('home.are you sure deleting this')
                                </div>
                                <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                   @lang('home.close')
                                </button>
                           
                                <a  href="{{ URL::to('contact-banks/delete/'.$item->id) }}" class="btn btn-danger">@lang('home.Delete')</a>
                          
                                </div>
                            </div>
                            </div>
                        </div>
                    </td>
                    </tr>
                   @endforeach
                   
                </tbody>
                <tfoot>
                   <tr class="bg-gray  font-17 footer-total" style="border:1px solid #f1f1f1;  ">
                      <td class="text-center " colspan="6"><strong>
                    {{ $allData->appends($_GET)->links() }}    
                    </strong></td>
                      
                   </tr>
                </tfoot>
             </table>
        </div>
        
    @endcomponent

   

</section>



<!-- /.content -->
@stop
