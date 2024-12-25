@extends('layouts.app')
@section('title',$title)

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header no-print">
    <h1>{{ $title }}
        <small></small>
    </h1>
    <!-- <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
        <li class="active">Here</li>
    </ol> -->
</section>

<!-- Main content -->
<section class="content no-print">
    @if(session('yes'))
    <div class="alert success alert-success" >
        {{ session('yes')  }}
    </div>
    @endif
    @component('components.filters', ['title' => $title])
        <form action="{{ URL::to('gournal-voucher') }}" method="GET">
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('purchase_list_filter_location_id',  __('home.Refo No') . ':') !!}
                    {!! Form::text('name', app('request')->input('name'), ['class' => 'form-control', 'style' => 'width:100%']); !!}
                </div>
            </div>
           
           
            <div class="col-sm-3">
                <div class="form-group">
                    {!! Form::label('type', __('home.Date From').':') !!} 
                    {!! Form::date('date_from',app('request')->input('date_from'), ['class' => 'form-control ']); !!}
                </div>
            </div>
            <div class="col-sm-3">
                <div class="form-group">
                    {!! Form::label('type', __('home.Write Date To').':') !!} 
                    {!! Form::date('date_to',app('request')->input('date_to'), ['class' => 'form-control ']); !!}
                </div>
            </div>
           
            <div class="col-md-1">
                <label for="purchase_list_filter_location_id" class="label-control" style="width: 100%">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>
               <button type="submit" class="btn btn-md btn-primary">  Filter</button>
            </div>
            
        </form>
        
        
        
    @endcomponent
    @component('components.widget')
    <div class="col-md-12 ">
        <div class="box-header">
            <div class="box-tools">
                <a class="btn btn-block btn-primary" href="{{ URL::to('gournal-voucher/add') }}">
                <i class="fa fa-plus"></i> {{ trans('home.Add') }}</a>
            </div>
        </div>
        <table class="table table-bordered ">
          <tbody>
               <tr style="background:#f1f1f1;">
                <th>@lang('home.Ref No')</th>
                  <th>@lang('home.Amount')</th>
                  <th>@lang('lang_v1.date')</th>
                  <th>@lang('home.Action')</th>
               </tr>
                        
               @foreach ($allData as $item)
                @php
                   $amount = 0;
                @endphp
               <tr style="border:1px solid #f1f1f1;">
                <td>{{ $item->ref_no}}</td>
                    @foreach($items as $it)
                     @if($it->gournal_voucher_id == $item->id)
                            @php
                                $amount = $amount + $it->amount;
                            @endphp
                     @endif    
                    @endforeach
               
                
                <td>{{ number_format($amount,config('constants.currency_precision')) }}</td>
                @php
                    $dt = $item->date;
                    $formats = [
                        'Y-m-d', // 2024-12-25
                        'd/m/Y', // 25/12/2024
                        'm/d/Y', // 12/25/2024
                        'd-m-Y', // 25-12-2024
                        'Y/m/d', // 2024/12/25
                        'Y-m-d H:i:s', // 2024-12-25 14:30:00
                        'd-m-Y H:i:s', // 25-12-2024 14:30:00    
                    ];
                    $D_format = "ops";
                    foreach ($formats as $format) {
                        try {
                            $date = \Carbon::createFromFormat($format, $dt);
                            // Check if the parsed date matches the input date string
                            if ($date && $date->format($format) === $dt) {
                                $D_format = $format;
                            }
                        } catch (\Exception $e) {
                            // Continue to the next format
                        }
                    } 
                @endphp
                <td>{{\Carbon::createFromFormat($D_format,$item->date)->format(session()->get('business.date_format'))}}</td>
                {{-- <td>{{ $item->date }}</td> --}}
                <td>
                    <div class="btn-group">
                        <button type="button" class="btn btn-info dropdown-toggle btn-xs" data-toggle="dropdown" aria-expanded="false">@lang('home.Action')<span class="caret"></span><span class="sr-only">Toggle Dropdown </span></button>
                        <ul class="dropdown-menu dropdown-menu-left" role="menu">
                           
                             <li> 
                                <a href="#"  data-href="{{ URL::to('gournal-voucher/view/'.$item->id) }}" class="views-modal" data-container=".view_model"><i class="fa fa-eye"></i>
                                            @lang("messages.view")</a> 
                             </li> 
                             <li> 
                                <a href="#"  data-href="{{ URL::to('gournal-voucher/entry/'.$item->id) }}" class="views-modal" data-container=".view_model"><i class="fa fa-align-justify"></i>
                                            @lang("home.Entry")</a> 
                             </li> 
                             @if($item->document && $item->document != [])
                                <li>
                                {{-- @php dd($item->image); @endphp --}}
                                    <a class="btn-modal" data-href="{{URL::to('gournal-voucher/attachment/'.$item->id)}}" data-container=".view_modal">
                                        <i class="fas fa-file"></i>
                                        @lang("home.attachment")
                                        {{-- <iframe src="{{ URL::to($data->image) }}" height="150" width="150" frameborder="0"></iframe> --}}  
                                    </a>
                                
                                </li>
                            @endif
                            <li>
                                <a href="{{ URL::to('reports/ex-vh/'.$item->id) }}" target="_blank"><i class="fas fa-print"></i>@lang('messages.print')</a>
                            </li>
                            <li>
                                <a href="{{ URL::to('gournal-voucher/edit/'.$item->id) }}"><i class="fas fa-edit"></i>@lang('home.Edit')</a>
                            </li>
                            <li>
                                <a href="#" data-href="{{  action('HomeController@formAttach', ["type" => "expense_voucher","id" => $item->id]) }}" target="_blank" class="btn-modal"  data-container=".view_modal"><i class="fas fa-paperclip"></i>@lang('Add Attachment')</a>
                            </li>
                            @if(request()->session()->get("user.id") == 1 || request()->session()->get("user.id") == 7 || request()->session()->get("user.id") == 8 )
                            @if($item->status == 0)
                            <li>
                                <a   data-toggle="modal" 
                                data-target="#exampleModalDelete{{ $item->id }}" class="delete-purchase"><i class="fas fa-trash"></i>@lang('home.Delete')</a>
                            </li>
                            @endif
                            @endif
                            
                        </ul>
                    </div>
                    
                    {{-- <a href="{{ URL::to('cheque/edit/'.$item->id) }}"
                        class="btn  btn-md btn-primary">
                        {{ trans('home.Edit') }}
                    </a>
                    <!-- Button trigger modal -->
                    <button type="button" class="btn btn-danger" data-toggle="modal" 
                         data-target="#exampleModal{{ $item->id }}">
                       {{ trans('home.Delete') }}
                    </button> --}}
                    
                    <!-- Modal -->
                    <div class="modal fade" id="exampleModalDelete{{ $item->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
                            <a  href="{{ URL::to('gournal-voucher/delete/'.$item->id) }}" class="btn btn-danger">@lang('home.Delete')</a>
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
    <div class="modal fade view_model" tabindex="-1" role="dialog" 
    	aria-labelledby="gridSystemModalLabel">
    </div>

   

  
   

</section>

@section('javascript')
<script type="text/javascript">


    $(document).on('click', 'button.btn-modal', function() {
            var url = $(this).data('href');
            var container = $(this).data('container');
            $.ajax({
                url: url ,
                dataType: 'html',
                success: function(result) {
                    $(container)
                        .html(result)
                        .modal('show');
                    $('.os_exp_date').datepicker({
                        autoclose: true,
                        format: 'dd-mm-yyyy',
                        clearBtn: true,
                    });
                },
            });
        });
    $(document).on('click', 'a.views-modal', function() {
         
            var url = $(this).data('href');
            var container = $(this).data('container');
            $.ajax({
                url: url ,
                dataType: 'html',
                success: function(result) {
                    $(container)
                        .html(result)
                        .modal('show');
                    $('.os_exp_date').datepicker({
                        autoclose: true,
                        format: 'dd-mm-yyyy',
                        clearBtn: true,
                    });
                },
            });
        });
</script>
@stop
<!-- /.content -->
@stop
