@extends('layouts.app')
@section('title', $title)

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header no-print">
    <h1> {{ $title }}
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
        <form action="{{ URL::to('account/cost-center') }}" method="GET">
            
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('purchase_list_filter_supplier_id',  __('home.Name') . ':') !!}
                    {!! Form::text('name', app('request')->input('name'), ['class' => 'form-control ', 'style' => 'width:100%']); !!}
                </div>
            </div>
            <div class="col-md-3">
                <label for="purchase_list_filter_location_id" class="label-control" style="width: 100%">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>
               <button type="submit" class="btn btn-md btn-primary">  Filter</button>
            </div>
            <div class="col-md-3">
                <label for="purchase_list_filter_location_id" class="label-control" style="width: 100%">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>
               <a href="{{ URL::to('/account/cost-center/add') }}"  class="btn btn-md btn-primary">@lang("messages.add")</a>
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
                    <td>{{  $item->account_number}}</td>
                    
                    <td>
                        <a href="{{ URL::to('account/account/'.$item->id) }}" class="btn btn-warning btn-xs"><i class="fa fa-book"></i>
                            
                            {{ trans('account.account_book_cost_center') }}
                        </a>
                        <a href="{{ URL::to('account/cost-center/edit/'.$item->id) }}"
                            class="btn  btn-xs btn-primary">
                            {{ trans('home.Edit') }}
                        </a>
                        
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
