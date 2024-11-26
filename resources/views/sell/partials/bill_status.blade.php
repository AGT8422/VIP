



@if($state == "received")
    @php $color = "#98D973"; $color = "#D9FFDA";
    $textColor = "#00FF01";@endphp
@elseif($state == "pending")
    @php $color = "#FAD9FF";$textColor = "#E000FF"; @endphp
@elseif($state == "ordered")
    @php $color = "#FFE2D9";$textColor = "#FF3800"; @endphp
@elseif($state == "final")
    @php $color = "#D9EDFF";$textColor = "#0086FF"; @endphp
@endif
    @php $lan = $state;      @endphp
     
    @if(auth()->user()->can("purchase.update") || auth()->user()->can("purchase.update_status"))
    @if(isset($Purchaseline) && ($Purchaseline <= $RecievedPrevious))
        {{-- @php  $state = "received" ; $color = "#D9FFDA"; $lan = $state; @endphp --}}
        <a  class=" no-print"          data-purchase_id="{{$id}}" data-status="@lang($state)" >
            <span class="label  status-label" style="color:{{$textColor}};background-color:{{$color}}" data-status-name="@lang("lang_v1.$state")" data-orig-value="{{$state}}">
                @lang($state) 
            </span>
        </a>
    @elseif($RecievedPrevious == null)
        <a @if(isset($type_return))  @if($type_return == "not_equal") class=" no-print" @else  class="update_status no-print"  @endif    @else  class="update_status no-print" @endif   data-purchase_id="{{$id}}" data-status="@lang($lan)" >
            <span class="label  status-label" style="color:{{$textColor}};background-color:{{$color}}" data-status-name="@lang("lang_v1.$state")" data-orig-value="{{$state}}">
                    @lang($lan) 
            </span>
        </a>
    @else
        <a class=" no-print"   data-purchase_id="{{$id}}" data-status="@lang($lan)" >
            <span class="label status-label" style="color:{{$textColor}};background-color:{{$color}}" data-status-name="@lang("lang_v1.$state")" data-orig-value="{{$state}}">
                    @lang($lan) 
            </span>
        </a>
    @endif

@endif
