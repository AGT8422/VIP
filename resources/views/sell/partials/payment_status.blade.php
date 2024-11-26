

@if($payment_status == "partial")
    @php  $color = "#D9EDFF"; $textColor = "#0086FF" ; @endphp  
@elseif($payment_status == "paid") 
    @php  $color = "#D9FFDA"; $textColor = "#00FF01" ; @endphp
@else 
    @php  $color = "#FFF5D9"; $textColor = "#EA9F00" ; @endphp
@endif
@php  $transaction_details = \App\Transaction::find($id);  @endphp
@if($transaction_details->sub_type == null || $transaction_details->sub_type != "return_payment")
    {{-- @payment_status($payment_status) --}}
    <a    href="{{ action('TransactionPaymentController@show', [$id])}}"  class="view_payment_modal payment-status-label"    data-orig-value="{{$payment_status}}" data-status-name="{{__('lang_v1.' . $payment_status)}}" style ='font-size:medium;border:1px solid {{$color}};border-radius:.3rem;padding:2px;background-color:{{$color}} !important;color:white !important;' >
        <span class="label   " style ='color:{{$textColor}}  !important'>
            {{__('lang_v1.' . $payment_status)}}     
        </span>
        {{-- @if($lock == 1)   {!! "<br><b style='color:black;background-color:transparent'>" . "Continue from Invoices" . "</b>" !!} @endif             --}}
        
        @php
            $parent  = "";
            $total   = 0 ;
            $refund  = "";
        @endphp
        @if(isset($cheques))
            @if(count($cheques)>0) 
                @foreach($cheques as $k => $check)
                    @php
                        $parent  = "";
                        
                        $parent .=  '<div class="btn-group">
                            <button  type="button" class="btn btn-note dropdown-toggle btn-xs" 
                            style="background:FFD9D9 ; color:red;border:1px solid rgb(255, 87, 87);margin-top:10px" data-toggle="dropdown" aria-expanded="false">' .
                            __("messages.related_check") .
                            '<span class="caret"></span><span class="sr-only">Toggle Dropdown
                                </span>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-left" role="menu">';
                                    
                    @endphp
                    @php
                        ($check->status == 2)? $refund = " ( <b> Refund </b> )":$refund = ""; 
                        $parent  .= "\n<a href='#' class='btn btn-info btn-modal'    data-href='/cheque/show/".$check->id."'data-container='.view_modal'><b>".$check->amount."</b> // ".$check->ref_no." ".$refund."  </a></div>";
                        $total   +=  $check->amount;
                    @endphp
                    {{-- <div class="modal fade" id="exampleModal{{$check->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel{{$check->id}}" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">{{ trans('home.Collect') }}</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            </div>
                            <div class="modal-body">
                            {{ Form::open(['url'=>'cheque/collect/'.$check->id]) }}
                                        @php
                                            $accounts = \App\Account::items(3);
                                            $business_id = auth()->user()->business_id;
                                            $setting = \App\Models\SystemAccount::where("business_id",$business_id)->first();
                                        @endphp
                                    <div class="form-group">
                                        {!! Form::label('account_id', __('home.Account').':*') !!}
                                        {!! Form::select('account_id', $accounts,null, ['class' => 'form-control ','style'=>'width:100%', 'placeholder' => __('messages.please_select'), 'required']); !!}
                                    </div>
                                    <div class="form-group">
                                        @php
                                            $time =  strtotime($check->due_date); 
                                        @endphp
                                        {!! Form::label('date', __('home.Date').':*') !!}
                                        {!! Form::date('date',null, ['class' => 'form-control ', 'required','style'=>'width:100%' ,'min' => date('Y-m-d',$time)  , 'max'=>date('Y-m-d')]); !!}
                                    </div>
                                    <div class="form-group">
                                        @php 
                                            if(!empty($setting)){
                                                if($check->type == 1){
                                                    $type_cheq = 0;
                                                } else if($check->type == 0){
                                                    $type_cheq = 1;
                                                }
                                            }
                                        @endphp 
                                        {!! Form::text('cheque_type_', $type_cheq, ['class' => 'form-control   hide', 'placeholder' => __('messages.please_select')]); !!}
                                    </div>
                                <button type="submit" class="btn btn-md btn-primary">{{ trans('home.Collect') }}</button>
                            {{  Form::token()  }}
                            {{  Form::close() }}
                            
                            </div>
                            
                            </div>
                        </div>
                    </div> --}}
                    {{-- <a  data-toggle='modal' data-target='#exampleModal".$check->id."' class='view_payment_modal' ><div style='padding:10px;'><i class='fas fa-money-bill-alt' aria-hidden='true'></i>".__('home.Collect')."</a> --}}
                @endforeach
                @php
                    $parent .='</ul>
                    </div><br><br><b>'.
                    $total.' 
                    </b>'; 
                @endphp
            @endif
        @endif
        <div class="modal fade view_model" tabindex="-1" role="dialog" 
            aria-labelledby="gridSystemModalLabel">
        </div>
    </a>
    <br>
    {!!  $parent !!}
@else
    @if($transaction_details->separate_type == null || $transaction_details->separate_type !=  "payment" )
    
        <i class="fa fa-undo" style="color:red;font-size:19px;font-weight:700" ></i>
        <br>
        <a    href="{{ action('TransactionPaymentController@show', [$id])}}"  class="view_payment_modal payment-status-label"    data-orig-value="{{$payment_status}}" data-status-name="{{__('lang_v1.' . $payment_status)}}" style ='font-size:medium;border:1px solid {{$color}};border-radius:.3rem;padding:2px;background-color:{{$color}} !important;color:white !important;' >
            <span class="label   " style ='color:{{$textColor}}  !important'>
                {{__('lang_v1.' . $payment_status)}}     
            </span>
            {{-- @if($lock == 1)   {!! "<br><b style='color:black;background-color:transparent'>" . "Continue from Invoices" . "</b>" !!} @endif             --}}
            
            @php
                $parent  = "";
                $total   = 0 ;
                $refund  = "";
            @endphp
            @if(isset($cheques))
                @if(count($cheques)>0) 
                    @foreach($cheques as $k => $check)
                        @php
                            $parent  = "";
                            
                            $parent .=  '<div class="btn-group">
                                <button  type="button" class="btn btn-note dropdown-toggle btn-xs" 
                                style="background:FFD9D9 ; color:red;border:1px solid rgb(255, 87, 87);margin-top:10px" data-toggle="dropdown" aria-expanded="false">' .
                                __("messages.related_check") .
                                '<span class="caret"></span><span class="sr-only">Toggle Dropdown
                                    </span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-left" role="menu">';
                                        
                        @endphp
                        @php
                            ($check->status == 2)? $refund = " ( <b> Refund </b> )":$refund = ""; 
                            $parent  .= "\n<a href='#' class='btn btn-info btn-modal'    data-href='/cheque/show/".$check->id."'data-container='.view_modal'><b>".$check->amount."</b> // ".$check->ref_no." ".$refund."  </a></div>";
                            $total   +=  $check->amount;
                        @endphp
                        {{-- <div class="modal fade" id="exampleModal{{$check->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel{{$check->id}}" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">{{ trans('home.Collect') }}</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                </div>
                                <div class="modal-body">
                                {{ Form::open(['url'=>'cheque/collect/'.$check->id]) }}
                                            @php
                                                $accounts = \App\Account::items(3);
                                                $business_id = auth()->user()->business_id;
                                                $setting = \App\Models\SystemAccount::where("business_id",$business_id)->first();
                                            @endphp
                                        <div class="form-group">
                                            {!! Form::label('account_id', __('home.Account').':*') !!}
                                            {!! Form::select('account_id', $accounts,null, ['class' => 'form-control ','style'=>'width:100%', 'placeholder' => __('messages.please_select'), 'required']); !!}
                                        </div>
                                        <div class="form-group">
                                            @php
                                                $time =  strtotime($check->due_date); 
                                            @endphp
                                            {!! Form::label('date', __('home.Date').':*') !!}
                                            {!! Form::date('date',null, ['class' => 'form-control ', 'required','style'=>'width:100%' ,'min' => date('Y-m-d',$time)  , 'max'=>date('Y-m-d')]); !!}
                                        </div>
                                        <div class="form-group">
                                            @php 
                                                if(!empty($setting)){
                                                    if($check->type == 1){
                                                        $type_cheq = 0;
                                                    } else if($check->type == 0){
                                                        $type_cheq = 1;
                                                    }
                                                }
                                            @endphp 
                                            {!! Form::text('cheque_type_', $type_cheq, ['class' => 'form-control   hide', 'placeholder' => __('messages.please_select')]); !!}
                                        </div>
                                    <button type="submit" class="btn btn-md btn-primary">{{ trans('home.Collect') }}</button>
                                {{  Form::token()  }}
                                {{  Form::close() }}
                                
                                </div>
                                
                                </div>
                            </div>
                        </div> --}}
                        {{-- <a  data-toggle='modal' data-target='#exampleModal".$check->id."' class='view_payment_modal' ><div style='padding:10px;'><i class='fas fa-money-bill-alt' aria-hidden='true'></i>".__('home.Collect')."</a> --}}
                    @endforeach
                    @php
                        $parent .='</ul>
                        </div><br><br><b>'.
                        $total.' 
                        </b>'; 
                    @endphp
                @endif
            @endif
            <div class="modal fade view_model" tabindex="-1" role="dialog" 
                aria-labelledby="gridSystemModalLabel">
            </div>
        </a>
        <br>
        {!!  $parent !!}
    @else
        <i class="fa fa-undo" style="color:red;font-size:19px;font-weight:700" ></i>
        <br>
        <span>
        {{ "Returned Payment"  }}
        </span>
    @endif
@endif
@section("javascript")
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
</script>
@endsection
