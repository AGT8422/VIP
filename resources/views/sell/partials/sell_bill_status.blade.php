



@if($state == "delivereds")
    @php $color = "#98D973"; @endphp
@elseif($state == "separates")
    @php $color = "#36cff5"; @endphp
@elseif($state == "final")
    @php $color = "#36cff5"; @endphp
@elseif($state == "not_delivereds")
    @php $color = "#36cff5"; @endphp
@else
@php $color = "#000000"; @endphp
@endif
    @php   
        $lan = $state;   
          
        if($state == "delivereds"){
                $lan =  "Delivered";
        }elseif($state == "final"){
                $lan =  "Final";
        }elseif($state == "not_delivereds"){
                $lan =  "Final";
        }elseif($state == "separates"){
                $lan =  "Final";           
        }else{
            $lan = $state;
        }  
    @endphp
            
            
@if(auth()->user()->can("sell.update") || auth()->user()->can("sell.update_status"))
    @if($RecievedPrevious == null)
        <a @if($type_return)  @if($type_return == "not_equal") class=" no-print" @else  class="update_status no-print"  @endif    @else  class="update_status no-print" @endif   data-purchase_id="{{$id}}" data-status="@lang($lan)" >
            <span class="label  status-label" style="color:black;background-color:{{$color}}" data-status-name="@lang("lang_v1.$state")" data-orig-value="{{$state}}">
                    @lang($lan) 
            </span>
        </a>
    @else
        <a class=" no-print"   data-purchase_id="{{$id}}" data-status="@lang($lan)" >
            <span class="label status-label" style="color:black;background-color:{{$color}}" data-status-name="@lang("lang_v1.$state")" data-orig-value="{{$state}}">
                    @lang($lan) 
            </span>
        </a>
    @endif

@endif
