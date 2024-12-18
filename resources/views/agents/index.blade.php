@extends('layouts.app')
@section('title', __('home.Agents'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header no-print">
    <h1>@lang('home.Agents')
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
        <form action="{{ URL::to('agents') }}" method="GET">
            <div class="col-md-6">
                <div class="form-group">
                    {!! Form::label('purchase_list_filter_location_id',  __('home.Name') . ':') !!}
                    {!! Form::text('name', app('request')->input('name'), ['class' => 'form-control ', 'style' => 'width:100%', 'placeholder' => __('home.Name')]); !!}
                </div>
            </div>
           
            <div class="col-md-3">
                <label for="purchase_list_filter_location_id" class="label-control" style="width: 100%">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>
               <button type="submit" class="btn btn-md btn-primary">  Filter</button>
            </div>
            <div class="col-md-3">
                <label for="purchase_list_filter_location_id" class="label-control" style="width: 100%">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>
               <a href="{{ URL::to('/agents/add') }}"  class="btn btn-md btn-primary">@lang("messages.add")</a>
            </div>
        </form>
        
        
        
    @endcomponent

   

</section>

<section class="content">
    @component('components.widget', ['class' => 'box-primary', 'title' => __( 'home.Agents' )])
    <div class="col-md-12">
        <table class="table table-bordered ">
            <tbody>
               <tr style="background:#f1f1f1;">
                  <th>@lang('home.Name')</th>
                  <th>@lang('home.Action')</th>
                 
               </tr>
               @foreach ($allData as $item)
               <tr style="border:1px solid #f1f1f1;">
                <td>{{ $item->name}}</td>                    
                <td>
                    <a href="{{ URL::to('agents/edit/'.$item->id) }}"
                        class="btn  btn-md btn-primary">
                        {{ trans('home.Edit') }}
                    </a>
                    <!-- Button trigger modal -->
                    <button type="button" class="btn btn-danger" data-toggle="modal" 
                         data-target="#exampleModal{{ $item->id }}">
                       {{ trans('home.Delete') }}
                    </button>
                    
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
                            <a  href="{{ URL::to('agents/delete/'.$item->id) }}" class="btn btn-danger">@lang('home.Delete')</a>
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
