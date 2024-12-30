<div class="modal-dialog modal-xl" role="document">
	<div class="modal-content">
      <div class="modal-header">
         <button type="button" class="close no-print" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
         <h4 class="modal-title" id="modalTitle">  <b> @lang('home.Ref No'):</b> #{{ $data->ref_no.' '.$data->invoice_no }}
         </h4>
     </div>
		<div class="modal-body">
         <div class="row">
            <div class="col-md-12">
                <table class="table table-bordered table-striped dataTable no-footer" id="account_book" role="grid" aria-describedby="account_book_info" style="width: 1536px;">
                    
                    <tbody>
                        <?php
                              $total_debit  = 0;
                              $total_credit = 0;
                              $entry_ref    = [];
                          ?>
                        @foreach($allData as $key=>$item)
                        @php $transaction_id     = \App\AccountTransaction::last_entry($item->entry_id); @endphp
                           @foreach($entry as $key=>$ref)
                             {{-- @php dd($item->check->ref_no); @endphp  --}}
                              @if(!in_array($ref->refe_no_e,$entry_ref))
                                 @if($item->entry_id == $ref->id  )
                                    @php array_push($entry_ref,$ref->refe_no_e); @endphp 
                                    @if($transaction_id == $item->id)
                                       @if( $total_debit != 0)
                                          <tr role="row" class="odd"  style="background-color:#676767 !important;  color:#f1f1f1">
                                             <td> 
                                             </td>
                                             <td>
                                             </td>
                                             <td>
                                             <span class="display_currency" data-currency_symbol="true">
                                                {{ number_format($total_debit,config('constants.currency_precision')) }}</span>
                                             </td>
                                             <td>
                                             <span class="display_currency" data-currency_symbol="true">{{ number_format($total_credit,config('constants.currency_precision')) }} </span>
                                             </td>
                                           </tr>
                                          @php 
                                             $total_debit  = 0;
                                             $total_credit = 0;
                                          @endphp
                                       
                                       @endif
                                       <thead>
                                       <tr>
                                          <td colspan="5" style="background-color:#f1f1f1 !important;color:black">{{$ref->refe_no_e}}</td>
                                       </tr>
                                          <tr role="row">
                                           <th class="sorting_disabled" rowspan="1" colspan="1" style="width: 187px;">Account</th>
                                           <th class="sorting_disabled" rowspan="1" colspan="1" style="width: 187px;">Date</th>
                                           <th class="sorting_disabled" rowspan="1" colspan="1" style="width: 79px;">Debit</th>
                                           <th class="sorting_disabled" rowspan="1" colspan="1" style="width: 188px;">Credit</th>
                                          </tr>
                                       </thead>
                                    @endif   
                                 @endif
                              @endif
                           @endforeach

                           <?php 
                           if($item->account->cost_center ==   0){
                              if (($item->type == 'debit')) {
                                 $total_debit +=$item->amount;
                              }else {
                                 $total_credit +=$item->amount;
                              }
                           }
                             
                            ?>
                            
                       <tr role="row" class="odd {{ ($item->account->cost_center > 0)?'alert-tr':' ' }}">
                          <td> 
                            <a target="_blank" href="{{ URL::to('account/account/'.$item->account_id) }}">{{ $item->account->name }}</a> 
                          </td>
                          <td>
                           {{ date('Y-m-d',strtotime($item->operation_date)) }}
                        </td>
                        <td>
                          <span class="display_currency" data-currency_symbol="true">{{ ($item->type == 'debit')?number_format($item->amount,config('constants.currency_precision')):0 }}</span>
                        </td>
                        <td>
                           <span class="display_currency" data-currency_symbol="true">{{ ($item->type == 'credit')?number_format($item->amount,config('constants.currency_precision')):0 }} </span>
                        </td>
                          
                       </tr>
                       @endforeach
                       <tfoot>

                          <tr role="row" class="odd">
                             <td> 
                           </td>
                        <td>
                        </td>
                        <td>
                          <span class="display_currency" data-currency_symbol="true">
                             {{ number_format($total_debit,config('constants.currency_precision')) }}</span>
                           </td>
                        <td>
                       <span class="display_currency" data-currency_symbol="true">{{ number_format($total_credit,config('constants.currency_precision')) }} </span>
                     </td>
                        </tfoot>
                        
                     </tr>
                    </tbody>
                 </table>
            </div>
         </div>
        
          <div class="row invoice-info">
            
         </div>
        </div>
    </div>
</div>

       		 
      	 							 
	
        
