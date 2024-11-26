<div class="modal-dialog modal-xl" role="document">
	<div class="modal-content">
		<div class="modal-header">
		    <button type="button" class="close no-print" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		      <h4 class="modal-title" id="modalTitle"> </h4>
	    </div>
	    <div class="modal-body">
            <table class="table ">
                <thead>
                <tr>
                    <th>{{ trans('purchase.ref_no') }}</th>
                    <th>{{ trans('home.Credit') }}</th>
                    <th>{{ trans('home.Debit') }}</th>
                    <th>{{ trans('home.Amount') }}</th>
                    <th>{{ trans('home.Tax Account') }}</th>
                    <th>{{ trans('home.Tax Amount') }}</th>
                    <th>{{ trans('home.Net Amount') }}</th>
                    <th>{{ trans('home.Date') }}</th>
                    <th>{{ trans('home.Note') }}</th>
                    <th>{{ trans('home.Cost Center') }}</th>
                </tr>
                <thead>
                    <tbody>
                @forelse($items as $it)
                        @php
                            $amount = 0;
                            $credit = "";
                            $depit  = "";
                            $tax    = "";
                            $taxAcc = "";
                            $date   = "";
                            $note   = "";
                            
                            $amount    = $amount + $it->amount ;
                            
                            foreach($accts as $key => $value){
                                if($it->credit_account_id == $key){
                                    $credit    = $value;
                                  
                                }
                                if($it->debit_account_id == $key){
                                    $depit     = $value;
                                }
                                if($it->tax_account_id == $key){
                                    $taxAcc    = $value;
                                }
                            }
                    
                            $tax       = $it->tax_amount;
                            
                            $date      = $it->date ;
                            $note      = $it->note ;
                            
                         @endphp
                
                            <tr>
                                <td> {{$vcher->ref_no}}</td>
                                <td><a href="account/account/{{$it->credit_account_id}}">{{$credit}}</a> </td>
                                <td><a href="account/account/{{$it->debit_account_id}}">{{$depit}}</a></td>
                                <td>{{$amount}}</td>
                                <td><a href="account/account/{{$it->tax_account_id}}">{{$taxAcc}}</a> </td>
                                <td>{{$tax}} </td>
                                <td>{{$amount - $tax}} </td>
                                <td>{{$date}} </td>
                                <td>{{$note}} </td>
                                <td><a href="account/account/{{$it->cost_center_id}}">{{($it->cost_center)?$it->cost_center->name:"--"}} </a></td>
                            </tr>
                    @empty

                    @endforelse
                    </tbody>
            </table>
        </div>
        
  
        <div class="modal-footer">
            <button type="button" class="btn btn-primary no-print" 
          aria-label="Print" 
            onclick="$(this).closest('div.modal').printThis();">
          <i class="fa fa-print"></i> @lang( 'messages.print' )
        </button>
            <button type="button" class="btn btn-default no-print" data-dismiss="modal">@lang( 'messages.close' )</button>
      </div>
  </div>
</div>
