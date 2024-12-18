



@if($payment_status == "delivered")
    @php
     $color = "transparent !important";
     $color = "#D9FFDA";
     $textColor = "#00FF01";
    @endphp  
@elseif($payment_status == "separate") 
    @php

        $color = "#D9EDFF";
        $textColor = "#0086FF";
    @endphp
@elseif($payment_status == "wrong") 
    @php
     $textColor = "#ff1111";
     $color = "#5D1E24";
    @endphp
@else 
    @php
    $color = "#FFD9D9";
    $textColor = "#ff0000";
    
    @endphp
@endif

@if($state == "final" || $state == "received"  || $state == "ordered" || $state == "pending" )
        
        <a href="{{ action('TransactionPaymentController@showw', [    $id ,"status" => $payment_status.'hello' , $id, "type" => $type ])}}" class="view_payment_modal payment-status-label" style ='color:{{$textColor}}!important;font-size:large;border-radius:.3rem;padding:2px;background-color:{{$color}} !important;' >
            <span class="label"    @if($payment_status == "wrong" ) style ='color:rgb(255, 255, 255) !important' @else style ='color:{{$textColor}} !important'   @endif  >{{__('purchase.' . $payment_status)}}
   </span>
            {{-- 98D973 --}}
            
        </a>
  
@else
    <a   style ='color:{{$textColor}} !important;font-size:large;border-radius:.3rem;padding:2px;background-color:{{$color}} !important;' class=" ">
        <span class="label"   style ='color:{{$textColor}}  !important'>{{__('purchase.' . $payment_status)}}</span>
        {{-- 98D973 --}}
        
    </a>
@endif
 
@if($wrong != null)  
<br><span class="label bg-gray" style="background:rgb(248, 215, 105) !important">@lang('lang_v1.wrong_recieve')</span>
@endif