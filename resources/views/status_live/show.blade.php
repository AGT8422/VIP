<div class="modal-dialog modal-xl no-print" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close no-print" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="modalTitle"> @lang("home.Recycle Live")  </h4>
    </div>
    <div class="modal-body">
        <div class="felxDiv">
            @php
                $count = 0;
            @endphp
            @foreach ($allData as $item)
            @if($count != 0)
                <div class="row-lex">
                    <i class="fa fa-arrow-right"></i>
                </div>
            @endif
            <div class="box_state">
                <h4>{{$item->state}}</h4>
                <p>
                    @if($item->check_id != null)
                        <button class="btn btn-link btn-modal" data-container=".view_modal" data-href="{{action("General\CheckController@show",["$item->check_id"])}}">
                            {{$item->reference_no}}
                        </button>
                    @else   
                        @if($item->transaction->type == "purchase")
                        <button class="btn btn-link btn-modal" data-container=".view_modal" data-href="{{action("PurchaseController@show",["$item->transaction_id"])}}">
                            {{$item->reference_no}}
                        </button>
                        @elseif($item->transaction->type == "sale")
                        <button class="btn btn-link btn-modal" data-container=".view_modal" data-href="{{action("SellController@show",["$item->transaction_id"])}}">
                            {{$item->reference_no}}
                        </button>
                        @endif
                    @endif
                </p>
                <p>{{$item->created_at}}</p>
                @if($item->state == "Receive items")
                    <p id="price">@lang("purchase.qty") : {{$item->price}}</p>
                @else
                    <p id="price">@lang("lang_v1.price") : {{$item->price}}</p>
                @endif

            </div>
            @php $count++; @endphp
            @endforeach
        </div>
      
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-default no-print" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>
</div>
</div>

 
  
  
 
  