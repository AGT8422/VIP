
@if($payment_status == "delivereds")
@php
   
$color = "#D9FFDA";
$textColor = "#00FF01";
@endphp  
@elseif($payment_status == "separates") 
@php
 $color = "#D9EDFF";
 $textColor = "#0086FF";
@endphp
@else 
@php
 
$color = "#FFD9D9";
$textColor = "#ff0000";
@endphp
@endif
@php
    // dd($approved);
@endphp
@if($approved == true)
<a href="{{ action('TransactionPaymentController@showww',[  "id" => $id ,"status" =>  $payment_status ,$id,"type" => $type,"approved"=>$approved  ])}}" data-orig-value="{{ $payment_status}}" data-status-name="{{__('lang_v1.' .$payment_status)}}" style ='font-size:medium;border:1px solid {{$color}};border-radius:.3rem;padding:2px;background-color:{{$color}} !important;color:white !important;' class="view_payment_modal payment-status-label" >
    <span class="label " style ='color:{{$textColor}}  !important'>
        {{__('lang_v1.' . $payment_status)}}            
    </span>

</a>
@else
<a href="{{ action('TransactionPaymentController@showww',[  "id" => $id ,"status" =>  $payment_status ,$id,"type" => $type  ])}}" data-orig-value="{{ $payment_status}}" data-status-name="{{__('lang_v1.' .$payment_status)}}" style ='font-size:medium;border:1px solid {{$color}};border-radius:.3rem;padding:2px;background-color:{{$color}} !important;color:white !important;' class="view_payment_modal payment-status-label" >
    <span class="label " style ='color:{{$textColor}}  !important'>
        {{__('lang_v1.' . $payment_status)}}            
    </span>

</a>
@endif
@if($wrong != null)  
<br><span class="label bg-gray" style="background:rgb(248, 215, 105) !important">@lang('lang_v1.wrong_recieve')</span>
@endif