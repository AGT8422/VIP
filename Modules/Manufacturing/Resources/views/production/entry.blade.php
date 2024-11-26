<div class="modal-dialog modal-xl" role="document">
	<div class="modal-content">
      <div class="modal-header">
         <button type="button" class="close no-print" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
         {{-- <h4 class="modal-title" id="modalTitle">  <b> @lang('home.ref_no_first'):</b>#{{ ($data->entries->first()!= null)?$data->entries->first()->refe_no_e:"" }} 
         </h4> --}}
         <h4 class="modal-title" id="modalTitle"  >  <b> @lang('home.ref_no_second'): 
         
         @if($data->type == "purchase")
            <button type="button" class="btn btn-link btn-modal"
                  data-href="{{action('PurchaseController@show', [$data->id])}}" data-container=".view_modal"> 
                  # {{ $data->ref_no }}
               </button>
         @elseif($data->type == "sale")
         
            <button type="button" class="btn btn-link btn-modal"
                     data-href="{{action('SellController@show', [$data->id]) }}" data-container=".view_modal"> 
                     # {{ $data->invoice_no }}
                  </button>

         @endif</b>
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
                                    $debit  = 0;
                                    $credit = 0;
                              ?>
                              @php
                                 $array_ship_id = [];
                                 $entry_ref = [];
                                 $check_ref = [];
                                 $check_ = [];
                                 $array = [];
                                 $refs = "";
                                 $number = 0;
                                 $check_compare = "";
      
                              @endphp
                             
                           
                               @foreach($allData as $key=>$item)
                                 @php $transaction_id     = \App\AccountTransaction::last_entry($item->entry_id); @endphp
                                  @if(!in_array($item->entry_id,$array) && $item->entry_id!= null) 
                                    @php array_push($array,$item->entry_id) @endphp
                                    @if($transaction_id == $item->id)
                                       @if( $total_debit != 0)
                                        <tr role="row" class="odd"  style="background-color:#676767 !important;  color:#f1f1f1">
                                             <td> 
                                             </td>
                                             <td>
                                             </td>
                                             <td>
                                             <span class="display_currency" data-currency_symbol="true">
                                                {{ number_format($total_debit,2) }}</span>
                                             </td>
                                             <td>
                                             <span class="display_currency" data-currency_symbol="true">{{ number_format($total_credit,2) }} </span>
                                             </td>
                                             <td></td>
                                          </tr>
                                          @php 
                                             $total_debit  = 0;
                                             $total_credit = 0;
                                          @endphp
                                        
                                       @endif
                                       <thead >
                                          <tr>
                                             <td colspan="5" style="background-color:#eeeeee;color:black;font-weight:bolder">{{ $item->entry->refe_no_e  }}</td>
                                          </tr>
                                             <tr role="row">
                                                <th class="sorting_disabled" rowspan="1" colspan="1" style="width: 187px;">Account</th>
                                                <th class="sorting_disabled" rowspan="1" colspan="1" style="width: 187px;">Date</th>
                                                <th class="sorting_disabled" rowspan="1" colspan="1" style="width: 79px;">Debit</th>
                                                <th class="sorting_disabled" rowspan="1" colspan="1" style="width: 188px;">Credit</th>
                                                <th class="sorting_disabled" rowspan="1" colspan="1" style="width: 188px;">{{ trans('home.Cost Center') }}</th>
                                               </tr>
                                         </thead>
                                          
                                       @else 
                                          
                                       @endif
                                    @endif
                                 
                                 <?php 
                                    if($item->account->cost_center ==   0){
                                       if (($item->type == 'debit')) {
                                          $total_debit  += $item->amount;
                                       }else {
                                          $total_credit += $item->amount;
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
                                       <span class="display_currency" data-currency_symbol="true">{{ ($item->type == 'debit')?number_format($item->amount,2):0 }}</span>
                                    </td>
                                    <td>
                                       <span class="display_currency" data-currency_symbol="true">{{ ($item->type == 'credit')?number_format($item->amount,2):0 }} </span>
                                    </td>
                                    <td>
               
                                       
                                    </td>
                                 </tr>
                                 
                              @endforeach
                              <tr role="row" class="odd"  style="background-color:#676767 !important;  color:#f1f1f1">
                                 <td> 
                                 </td>
                                 <td>
                                 </td>
                                 <td>
                                 <span class="display_currency" data-currency_symbol="true">
                                    {{ number_format($total_debit,2) }}</span>
                                 </td>
                                 <td>
                                 <span class="display_currency" data-currency_symbol="true">{{ number_format($total_credit,2) }} </span>
                                 </td>
                                 <td></td>
                              </tr>
                        </tbody>
                       
                      
                     </table>
                  </div>
               </div>
            
               <div class="col-sm-12">
               </div>
               <div class="row invoice-info">
                  
               </div>
             

      </div>
        
        
    </div>

    <div class="modal fade view_model" tabindex="-1" role="dialog" 
            aria-labelledby="gridSystemModalLabel">
      </div>
</div>

       		 
      	 							 
	
        
