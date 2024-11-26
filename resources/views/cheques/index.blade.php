@extends('layouts.app')
@section('title', __('home.Cheques'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header no-print">
    <h1>@lang('home.cheques')
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
    @component('components.filters', ['title' => __('report.filters')])
        <form action="{{ URL::to('cheque') }}" method="GET">
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('purchase_list_filter_location_id',  __('home.Refo No or Cheque No') . ':') !!}
                    {!! Form::text('name', app('request')->input('name'), ['class' => 'form-control', 'style' => 'width:100%']); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('purchase_list_filter_location_id',  __('purchase.business_location') . ':') !!}
                    {!! Form::select('location_id', $business_locations, app('request')->input('location_id'), ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('purchase_list_filter_supplier_id',  __('home.Contact') . ':') !!}
                    {!! Form::select('contact_id', $contacts, app('request')->input('contact_id'), ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('purchase_list_filter_typ_cheque_id',  __('home.Status') . ':') !!}
                    {!! Form::select('type_cheque_co',["1"=>"Write","2"=>"Collected","3"=>"UnCollected","4"=>"Refund"] , null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
                </div>
            </div>
            <div class="col-sm-3">
                <div class="form-group">
                    {!! Form::label('type', __('home.Cheque Type').':') !!} 
                    {!! Form::select('cheque_type', $types,app('request')->input('cheque_type'), ['class' => 'form-control select2', 'placeholder' => __('messages.please_select')]); !!}
                </div>
            </div>
            <div class="col-sm-3">
                <div class="form-group">
                    {!! Form::label('type', __('home.Write Date From').':') !!} 
                    {!! Form::date('write_date_from',app('request')->input('write_date_from'), ['class' => 'form-control ' ]); !!}
                </div>
            </div>
            <div class="col-sm-3">
                <div class="form-group">
                    {!! Form::label('type', __('home.Write Date To').':') !!} 
                    {!! Form::date('write_date_to',app('request')->input('write_date_to'), ['class' => 'form-control ' ]); !!}
                </div>
            </div>
            <div class="col-sm-2">
                <div class="form-group">
                    {!! Form::label('type', __('home.Due Date From').':') !!} 
                    {!! Form::date('due_date_from',app('request')->input('due_date_from'), ['class' => 'form-control ' ]); !!}
                </div>
            </div>
            <div class="col-sm-2">
                <div class="form-group">
                    {!! Form::label('type', __('home.Due Date To').':') !!} 
                    {!! Form::date('due_date_to',app('request')->input('due_date_to'), ['class' => 'form-control ', 'id'=>'due_date_to'    ]); !!}
                </div>
            </div>
            <div class="col-md-1">
                <label for="purchase_list_filter_location_id" class="label-control" style="width: 100%">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>
               <button type="submit" class="btn btn-md btn-primary">  Filter</button>
            </div>
            
        </form>
        
        @endcomponent
    @component("components.widget",["title"=>"All Cheque"])
        <div class="col-md-12">
            <table class="table table-bordered ">
                <tbody>
                   <tr style="background:#f1f1f1;">
                    <th>@lang('home.Ref No')</th>
                    <th>@lang('home.Cheque No')</th>
                    <th>@lang('home.Contact')</th>
                    <th>@lang('home.Amount')</th>
                    <th>@lang('purchase.payment_for')</th>
                    <th>@lang('home.Account')</th>
                    {{-- <th>@lang('home.Cheque Type')</th> --}}
                    <th>@lang('home.Collecting Account')</th>
                    <th>@lang('home.Status')</th>
                    <th>@lang('home.Write Date')</th>
                    <th>@lang('home.Due Date')</th>
                    <th>@lang('home.Collecting Date')</th>
                    <th>@lang('lang_v1.note')</th>
                    <th>@lang('home.Action')</th>
                   </tr>
                   @foreach ($allData as $item)
                   <tr style="border:1px solid #f1f1f1;">
                    <td>{{ $item->ref_no}}</td>
                    <td>{{ $item->cheque_no}}</td>
                    @php 
                        if($item->account_type == 0){
                            $account = \App\Account::where("contact_id",$item->contact_id)->first();
                            if($account){
                              $name = $account->name; 
                            }else{
                                $name = "--"; 
                            }
                        }else{
                            $account = \App\Account::where("id",$item->contact_id)->first();
                            if($account){
                              $name = $account->name; 
                            }else{
                            $name = "--"; 
                            }
                        }
                    @endphp
                    <td>{{  $name }} </td>
                    <td>{{ $item->amount }}</td>
                    @php $payment_id = \App\Transaction::where("id",$item->transaction_id)->first(); (!empty($payment_id))?$amount_p = $payment_id->final_total : $amount_p =  0;  ; @endphp
                    <td>{{ $amount_p }}</td>
                    <td>{{ $item->account?$item->account->name:'--' }}</td>
                    {{-- <td>{{ $item->type_name }}</td> --}}
                    <td>{{ $item->collecting_account?$item->collecting_account->name:' ' }}</td>
                    <td>{{ $item->status_name }}</td>
                    <td>{{ $item->write_date }}</td>
                    <td>{{ $item->due_date }}</td>
                        @php $accountTrans = \App\AccountTransaction::orderBy("id","desc")->where("check_id",$item->id)->first();  @endphp 
                        @if($item->collecting_date)
                            @if($accountTrans)
                                <td>{{ $accountTrans->operation_date->format('Y-m-d') }}</td>
                            @else
                                <td>{{ $item->collecting_date }}</td>
                            @endif
                        @else
                        <td></td>
                        @endif
                    <td>{{ $item->note }}</td>
                    <td>
                        <div class="btn-group">
                            <button type="button" class="btn btn-info dropdown-toggle btn-xs" data-toggle="dropdown" aria-expanded="false">@lang('home.Action')<span class="caret"></span><span class="sr-only">Toggle Dropdown </span></button>
                            <ul class="dropdown-menu dropdown-menu-left" role="menu">
                                {{-- if check created --}}
                                @if($item->status == 0 || $item->status == 4 || $item->status == 3)
                                    {{-- view --}}
                                    <li>
                                        <a href="#" data-href="{{ action('General\CheckController@show', [$item->id]) }}" class="btn-modal" data-container=".view_modal"><i class="fas fa-eye" aria-hidden="true"></i>@lang('home.View')</a>
                                    </li>
                                    {{-- entry --}}
                                    <li>
                                        <a href="#" data-href="{{ URL::to('cheque/entry/'.$item->id)}}" class="btn-modal" data-container=".view_modal"><i class="fa fa-align-justify" aria-hidden="true"></i>@lang('home.Entry')</a>
                                    </li>
                                    {{-- edit --}}
                                    <li>
                                        <a href="{{ URL::to('cheque/edit/'.$item->id) }}"><i class="fas fa-edit"></i>@lang('home.Edit')</a>
                                    </li>
                                    {{-- collect --}}
                                    {{-- @if($item->status == 4) --}}
                                    <li>
                                        <a  data-toggle="modal" data-target="#exampleModal{{ $item->id }}" class="view_payment_modal"><i class="fas fa-money-bill-alt" aria-hidden="true"></i>@lang('home.Collect')</a>
                                    </li>
                                    {{-- @endif --}}
                                    {{-- refund --}}
                                    {{-- @if($item->status != 2 && $item->status > 2) --}}
                                    <li>
                                        @if($item->account_type == 0)
                                            <a href="{{ URL::to('cheque/refund/'.$item->id.'?old=1') }}" class="view_payment_modal refund-modal"><i class="fas fa-money-bill-alt" aria-hidden="true"></i>@lang('home.Refund')</a>
                                        @else
                                            <a href="{{ URL::to('cheque/refund/'.$item->id) }}" class="view_payment_modal refund-modal"><i class="fas fa-money-bill-alt" aria-hidden="true"></i>@lang('home.Refund')</a>
                                        @endif

                                    </li>
                                    {{-- @endif --}}
                                    {{-- delete --}}
                                    {{-- @if($item->status == 0) --}}
                                    @if(request()->session()->get("user.id") == 1 || request()->session()->get("user.id") == 7 || request()->session()->get("user.id") == 8)
                                    <li>
                                        <a   data-toggle="modal" 
                                        data-target="#exampleModalDelete{{ $item->id }}" class="delete-purchase"><i class="fas fa-trash"></i>@lang('home.Delete')</a>
                                    </li>
                                    @endif
                                    {{-- @endif --}}
                                @endif
                                
                               {{-- if check collected  --}}
                                @if($item->status == 1)
                                    {{-- view --}}
                                    <li>
                                        <a href="#" data-href="{{ action('General\CheckController@show', [$item->id]) }}" class="btn-modal" data-container=".view_modal"><i class="fas fa-eye" aria-hidden="true"></i>@lang('home.View')</a>
                                    </li>
                                    {{-- entry --}}
                                    <li>
                                        <a href="#" data-href="{{ URL::to('cheque/entry/'.$item->id)}}" class="btn-modal" data-container=".view_modal"><i class="fa fa-align-justify" aria-hidden="true"></i>@lang('home.Entry')</a>
                                    </li>
                                    {{-- delete collect --}}
                                    <li>
                                        <a data-href="{{ URL::to('cheque/delete-collect/'.$item->id) }}" class="delete_collect"><i class="fas fa-trash"></i>@lang('home.Delete Collect')</a>
                                    </li>
                                    {{-- un collect --}}
                                    <li>
                                        <a data-href="{{ URL::to('cheque/un-collect/'.$item->id) }}" class="un_collect"><i class="fas fa-undo"></i>@lang('home.Un Collect')</a>
                                    </li>
                                @endif

                                {{-- if check refunded --}}
                                @if($item->status == 2)
                                    {{-- view --}}
                                    <li>
                                        <a href="#" data-href="{{ action('General\CheckController@show', [$item->id]) }}" class="btn-modal" data-container=".view_modal"><i class="fas fa-eye" aria-hidden="true"></i>@lang('home.View')</a>
                                    </li>
                                    {{-- entry --}}
                                    <li>
                                        <a href="#" data-href="{{ URL::to('cheque/entry/'.$item->id)}}" class="btn-modal" data-container=".view_modal"><i class="fa fa-align-justify" aria-hidden="true"></i>@lang('home.Entry')</a>
                                    </li>
                                @endif
                                @if($item->type == 1)
                                    <li>
                                        <a href="{{ URL::to('reports/o-ch/'.$item->id) }}" target="_blank"><i class="fas fa-print"></i>@lang('messages.print')</a>
                                    </li>
                                @else
                                    <li>
                                        <a href="{{ URL::to('reports/i-ch/'.$item->id) }}" target="_blank"><i class="fas fa-print"></i>@lang('messages.print')</a>
                                    </li>
                                @endif
                                @if($item->document && $item->document != [])
                                    <li>
                                    {{-- @php dd($item->image); @endphp --}}
                                        <a class="btn-modal" data-href="{{URL::to('cheque/attachment/'.$item->id)}}" data-container=".view_modal">
                                            <i class="fas fa-file"></i>
                                            @lang("home.attachment")
                                            {{-- <iframe src="{{ URL::to($data->image) }}" height="150" width="150" frameborder="0"></iframe> --}}  
                                        </a>
                                    
                                    </li>
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
                                <a  href="{{ URL::to('cheque/delete/'.$item->id) }}" class="btn btn-danger">@lang('home.Delete')</a>
                                </div>
                            </div>
                            </div>
                        </div>
                    </td>
                    </tr>
                    <div class="modal fade" id="exampleModal{{$item->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel{{$item->id}}" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                          <div class="modal-content">
                            <div class="modal-header">
                              <h5 class="modal-title" id="exampleModalLabel">{{ trans('home.Collect') }}</h5>
                              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                              </button>
                            </div>
                            <div class="modal-body">
                              {{ Form::open(['url'=>'cheque/collect/'.$item->id , 'id' => 'collect_form']) }}
                              
                                    <div class="form-group">
                                        {!! Form::label('account_id', __('home.Account').':*') !!}
                                        {!! Form::select('account_id', $accounts,null, ['class' => 'form-control ', 'id' => 'account_id', 'placeholder' => __('messages.please_select'), 'required']); !!}
                                    </div>
                                    <div class="form-group">
                                         @php
                                            $time =  strtotime($item->write_date); 
                                        @endphp
                                        {!! Form::label('date', __('home.Date').':*') !!}
                                        {!! Form::date('date',null, ['class' => 'form-control ', 'required' ,'id' => 'date', 'min' => date('Y-m-d',$time)  , 'max'=>date('Y-m-d')]); !!}
                                    </div>
                                    <div class="form-group">
                                        @php 
                                            if(!empty($setting)){
                                                if($item->type == 1){
                                                    $type_cheq = 0;
                                                } else if($item->type == 0){
                                                    $type_cheq = 1;
                                                }
                                            }
                                        @endphp 
                                         {!! Form::text('cheque_type_', $type_cheq, ['class' => 'form-control   hide', 'placeholder' => __('messages.please_select')]); !!}
                                    </div>
                                <button type="submit"  class="btn btn-md btn-primary collect_submit">{{ trans('home.Collect') }}</button>
                              {{  Form::token()  }}
                              {{  Form::close() }}
                              
                            </div>
                            
                          </div>
                        </div>
                      </div>
                   @endforeach
                   
                </tbody>
                <tfoot>
                   <tr class="bg-gray  font-17 footer-total" style="border:1px solid #f1f1f1;  ">
                      <td class="text-center " colspan="13"><strong>
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
        
        $(document).on('click', '.collect_submit', function() {
            $('.modal').hide();
        });
        $(document).on('click', 'button.btn-modal', function() {
            var url       = $(this).data('href');
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
        $(document).on('click', 'a.refund-modal', function(e){
            e.preventDefault();
            swal({
                title: LANG.sure,
                icon: "warning",
                buttons: true,
                dangerMode: true,
            }).then((willDelete) => {
                if (willDelete) {
                    var href = $(this).attr('href');
                    $.ajax({
                        method: "GET",
                        url: href,
                        dataType: "json",
                        success: function(result){
                            if(result.success == true){
                                toastr.success(result.msg);
                                window.history.back();
                            } else {
                                toastr.error(result.msg);
                            }
                        }
                    });
                }
            });
        });
        $(document).on('click', 'a.un_collect', function(e){
            e.preventDefault();
            swal({
                title: LANG.sure,
                icon: "warning",
                buttons: true,
                dangerMode: true,
            }).then((willDelete) => {
                if (willDelete) {
                    var href = $(this).attr('data-href');
                    $.ajax({
                        method: "GET",
                        url: href,
                        dataType: "json",
                        success: function(result){
                            if(result.success == true){
                                toastr.success(result.msg);
                                window.history.back();
                            } else {
                                toastr.error(result.msg);
                            }
                        }
                    });
                }
            });
        });
        $(document).on('click', 'a.delete_collect', function(e){
            e.preventDefault();
            swal({
                title: LANG.sure,
                icon: "warning",
                buttons: true,
                dangerMode: true,
            }).then((willDelete) => {
                if (willDelete) {
                    var href = $(this).attr('data-href');
                    $.ajax({
                        method: "GET",
                        url: href,
                        dataType: "json",
                        success: function(result){
                            if(result.success == true){
                                toastr.success(result.msg);
                                window.history.back();
                            } else {
                                toastr.error(result.msg);
                            }
                        }
                    });
                }
            });
        });
     
      



</script>
@stop
<!-- /.content -->
@stop
