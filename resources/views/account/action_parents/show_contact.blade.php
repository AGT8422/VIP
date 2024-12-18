<div class="modal-dialog modal-xl" role="document" style="width:20% !important">
	<div class="modal-content">
		<div class="modal-header">
		    <button type="button" class="close no-print" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		      <h4 class="modal-title" id="modalTitle">{{$account->name}}</h4> 
	    </div>
	    <div class="modal-body">
      		<div class="row">
      			<div class="col-sm-12 text-center   " style="padding:10px;margin">
	      			<div class="col-sm-6 invoice-col">
                        @php
                            $debit  = \App\AccountTransaction::where("account_id",$account->id)->where("for_repeat",null)->where("type","debit")->sum("amount");
                            $credit = \App\AccountTransaction::where("account_id",$account->id)->where("for_repeat",null)->where("type","credit")->sum("amount");
                            $total  = $credit - $debit;
                        
                            if($total==0){
                                $total = 0;
                            }elseif($total<0){
                                $total = ($total)*-1 . " / Debit" ;
                            }else{
                                $total =  ($total) . " / Credit";
                            }

                        @endphp
                            @lang("sale.total")

                    </div>
	      			<div class="col-sm-6 invoice-col">
                            {{ $total }}
                                                        
                    </div>
	      		 
                </div>
                @php
                $account = \App\Account::where("id",$account->id)->first();
                if(!empty($account)){
                        $contact = $account->contact_id;
                }else{
                        $contact = null;
                }
                @endphp
                <br>
      			<div class="col-sm-12 text-center">
                    @if($contact != null)
	      			<div class="col-sm-6  ">
                          <a class="btn bg-blue" href="/contacts/{{$contact}}">@lang("home.account_statment")</a>
                    </div>
                    @endif
	      			<div class="col-sm-6  ">
                           <a class="btn bg-yellow" href="/account/account/{{$account->id}}">@lang("home.ledger_")</a>
                                                        
                    </div>
	      		 
                </div>
            </div>
        </div>

    </div>
</div>