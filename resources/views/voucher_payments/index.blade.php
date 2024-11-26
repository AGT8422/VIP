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
        <form action="{{ URL::to('payment-voucher') }}" method="GET">
            <div class="col-md-2">
                <div class="form-group">
                    {!! Form::label('purchase_list_filter_location_id',  __('home.Refo No') . ':') !!}
                    {!! Form::text('name', app('request')->input('name'), ['class' => 'form-control', 'style' => 'width:100%']); !!}
                </div>
            </div>
           
            <div class="col-md-2">
                <div class="form-group">
                    {!! Form::label('purchase_list_filter_supplier_id',  __('home.Contact') . ':') !!}
                    {!! Form::select('contact_id', $contacts, app('request')->input('contact_id'), ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
                </div>
            </div>
            <div class="col-sm-2">
                <div class="form-group">
                    {!! Form::label('type', __('home.Type').':') !!} 
                    {!! Form::select('voucher_type', $types,app('request')->input('voucher_type'), ['class' => 'form-control select2', 'placeholder' => __('messages.please_select')]); !!}
                </div>
            </div>
            <div class="col-sm-2">
                <div class="form-group">
                    {!! Form::label('type', __('home.Date From').':') !!} 
                    {!! Form::date('date_from',app('request')->input('date_from'), ['class' => 'form-control ']); !!}
                </div>
            </div>
            <div class="col-sm-2">
                <div class="form-group">
                    {!! Form::label('type', __('home.Date To').':') !!} 
                    {!! Form::date('date_to',app('request')->input('date_to'), ['class' => 'form-control ']); !!}
                </div>
            </div>
            
            <div class="col-md-1">
                <label for="purchase_list_filter_location_id" class="label-control" style="width: 100%">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>
               <button type="submit" class="btn btn-md btn-primary">  Filter</button>
            </div>
            
        </form>
        
        
        
    @endcomponent
    @component('components.widget', ['title' => __('home.Voucher')])
    <div class="col-md-12">
        <table class="table table-bordered ">
            <tbody>
               <tr style="background:#f1f1f1;">
                <th>@lang('home.Ref No')</th>
                  <th>@lang('home.Contact')</th>
                  <th>@lang('home.Amount')</th>
                  <th>@lang('purchase.bill_amount')</th>
                  <th>@lang('home.Account')</th>
                  <th>@lang('home.Type')</th>
                  <th>@lang('home.Date')</th>
                  <th>@lang('home.Action')</th>
               </tr>
               @foreach ($allData as $item)
                <tr  class="{{ (($item->amount - $item->payments->sum('amount')) > 0 )?'alert':'' }}" style="border:1px solid #f1f1f1;">
                    <td>
                        <a style="color:black" href="#" data-href="{{ action('General\PaymentVoucherController@show', [$item->id]) }}" class="btn-modal"
                            data-container=".view_modal">
                        {{ $item->ref_no}}
                        </a>
                    </td>
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
                    {{-- <td>{{ $item->contact?$item->contact->name:'' }}</td> --}}
                    <td>{{$name}}</td>
                    <td>@format_currency($item->amount)</td>
                    @php
                       $payment     = \App\TransactionPayment::where("payment_voucher_id",$item->id)->first();
                       if(!empty($payment)){
                            $transaction = \App\Transaction::where("id",$payment->transaction_id)->first();
                            $total_bill  = $transaction->final_total;
                            $sum         = \App\TransactionPayment::where("transaction_id",$transaction->id)->get();
                            $price       = 0; 
                            foreach ($sum as  $value) {
                                if($value->payment_voucher_id != $item->id){
                                    $price += $value->amount;
                                }
                            }
                        }else{
                            $price       = 0;
                            $total_bill  = 0;
                        }
                    
                    @endphp 
                    <td>@format_currency($total_bill) 
                        <br>
                        @if(!empty($payment))
                        @if($transaction->type == "sale")
                            <button class="btn btn-primery btn-link btn-modal" data-container=".view_modal" data-href="{{action("SellController@show",[$transaction->id])}}" >
                                {{$transaction->invoice_no}}
                            </button>
                        @elseif($transaction->type == "purchase")
                            <button class="btn btn-primery btn-link btn-modal" data-container=".view_modal" data-href="{{action("PurchaseController@show",[$transaction->id])}}" >
                                {{$transaction->ref_no}}
                             </button>
                        @endif
                        @endif
                    </td>
                    <td>{{ $item->account?$item->account->name:'--' }}</td>
                    <td>{{ isset($types[$item->type])?$types[$item->type]:'' }}</td>
                    <td>{{ $item->date }}</td>
                    <td>
                        <div class="btn-group">
                            <button type="button" class="btn btn-info dropdown-toggle btn-xs" data-toggle="dropdown" aria-expanded="false">@lang('home.Action')<span class="caret"></span><span class="sr-only">Toggle Dropdown </span></button>
                            <ul class="dropdown-menu dropdown-menu-left" role="menu"> 
                                <li>
                                    <a href="#" data-href="{{ action('General\PaymentVoucherController@show', [$item->id]) }}" class="btn-modal"
                                         data-container=".view_modal"><i class="fas fa-eye" aria-hidden="true"></i>@lang('home.View')</a>
                                </li>
                                <li>
                                </li>
                                 @if($item->return_voucher == 0 || $item->return_voucher == null)
                                    <li>
                                        
                                         @php  $types_s = ($item->contact->type == "customer")?0:1  @endphp
                                        <a href="{{ URL::to('payment-voucher/edit/'.$item->id.'&type='.$types_s) }}"><i class="fas fa-edit"></i>@lang('home.Edit')</a>
                                         
                                    </li>
                                @endif
                                <li>
                                    @php  $types_s = ($item->contact->type == "customer")?0:1  @endphp
                                    @if($types_s == 0)
                                        <a href="{{ URL::to('reports/r-vh/'.$item->id) }}" target="_blank"><i class="fas fa-print"></i>@lang('messages.print')</a>
                                    @else
                                        <a href="{{ URL::to('reports/p-vh/'.$item->id) }}" target="_blank"><i class="fas fa-print"></i>@lang('messages.print')</a>
                                    @endif
                                </li>
                                @if($item->document && $item->document != [])
                                    <li>
                                    {{-- @php dd($item->image); @endphp --}}
                                        <a class="btn-modal" data-href="{{URL::to('payment-voucher/attachment/'.$item->id)}}" data-container=".view_modal">
                                            <i class="fas fa-file"></i>
                                            @lang("home.attachment")
                                            {{-- <iframe src="{{ URL::to($data->image) }}" height="150" width="150" frameborder="0"></iframe> --}}  
                                        </a>
                                    
                                    </li>
                                @endif
                                <li>
                                     <a class="btn-modal " data-href="{{ URL::to('payment-voucher/whatsapp/'.$item->id) }}" data-container=".view_modal"><i class="fab fa-whatsapp-square" ></i> @lang('Send Whatsapp')</a>
                                </li>
                                <li>
                                     <a class="btn-modal " data-href="{{ URL::to('payment-voucher/entry/'.$item->id) }}" data-container=".view_modal"><i class="fa fa-align-justify"></i>@lang('home.Entry')</a>
                                </li>
                                @if($item->return_voucher == 0 || $item->return_voucher == null)
                                    <li>
                                        <a class=" return_sure" data-href="{{ URL::to('payment-voucher/return/'.$item->id) }}"  ><i class="fa fa-undo" aria-hidden="true"></i>@lang('Return Voucher')</a>
                                    </li>
                                @endif
                                @if(request()->session()->get("user.id") == 1 || request()->session()->get("user.id") == 7 || request()->session()->get("user.id") == 8)
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
                                <a  href="{{ URL::to('payment-voucher/delete/'.$item->id) }}" class="btn btn-danger">@lang('home.Delete')</a>
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
                  <td class="text-center " colspan="8"><strong>
                {{ $allData->appends($_GET)->links() }}    
                </strong></td>
                  
               </tr>
            </tfoot>
         </table>
    </div>

    @endcomponent

  
   

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
    $(document).on("click",".return_sure",function(){
        swal({
            title: LANG.sure,
            icon: 'warning',
            buttons: true,
            dangerMode: true,
        }).then(conditions =>{
            if(conditions){
                href = $(this).attr("data-href");
                $.ajax({
                    url:href,
                    dataType:"json",
                    method:"GET",
                    success:function(result){
                        if(result.success === 1){
                            toastr.success("success");
                            
                            location.reload();
                        }else{
                            toastr.error("error"); 
                            location.reload();

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
