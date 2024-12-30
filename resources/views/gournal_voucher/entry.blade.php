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
                              $start_point  = 1;
                          ?>
                        @foreach($allData as $key=>$item)
                           @foreach($entry as $key=>$ref)
                             {{-- @php dd($allData); @endphp  --}}
                              @if(!in_array($ref->refe_no_e,$entry_ref))
                                 @if($item->gournal_voucher_item)
                                    @if($item->gournal_voucher_item->gournal_voucher->ref_no == $ref->ref_no_e  )
                                       @php array_push($entry_ref,$ref->refe_no_e); @endphp 
                                       <thead>
                                          <tr>
                                             <td colspan="5" style="background-color:#f1f1f1 !important;color:black" class="font_number">{{$ref->refe_no_e}}</td>
                                          </tr>
                                             <tr role="row">
                                                <th class="sorting_disabled" rowspan="1" colspan="1" style="width: 187px;">Account</th>
                                                <th class="sorting_disabled" rowspan="1" colspan="1" style="width: 187px;">Date</th>
                                                <th class="sorting_disabled" rowspan="1" colspan="1" style="width: 79px;">Debit</th>
                                                <th class="sorting_disabled" rowspan="1" colspan="1" style="width: 188px;">Credit</th>
                                                <th class="sorting_disabled" rowspan="1" colspan="1" style="width: 188px;">Cost Center</th>
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
                            
                            @php
                            $dt =  date('Y-m-d',strtotime($item->operation_date));
                            $formats = [
                               'Y-m-d', // 2024-12-25
                               'd/m/Y', // 25/12/2024
                               'm/d/Y', // 12/25/2024
                               'd-m-Y', // 25-12-2024
                               'Y/m/d', // 2024/12/25
                               'Y-m-d H:i:s', // 2024-12-25 14:30:00
                               'd-m-Y H:i:s', // 25-12-2024 14:30:00    
                            ];
                            $D_format = "ops";
                            foreach ($formats as $format) {
                               try {
                                     $date = \Carbon::createFromFormat($format, $dt);
                                     // Check if the parsed date matches the input date string
                                     if ($date && $date->format($format) === $dt) {
                                        $D_format = $format;
                                     }
                               } catch (\Exception $e) {
                                     // Continue to the next format
                               }
                            } 
                         @endphp
                       <tr role="row" class="odd {{ ($item->account->cost_center > 0)?'alert-tr':' ' }}">
                          <td> 
                            <a target="_blank" href="{{ URL::to('account/account/'.$item->account_id) }}">{{ $item->account->name }}</a> 
                          </td>
                          <td class="font_number">{{\Carbon::createFromFormat($D_format,date('Y-m-d',strtotime($item->operation_date)))->format(session()->get('business.date_format'))}}</td>
                          <td>
                            <span class="display_currency font_number" data-currency_symbol="true">{{ ($item->type == 'debit')?$item->amount:0 }}</span>
                          </td>
                          <td>
                            <span class="display_currency font_number" data-currency_symbol="true">{{ ($item->type == 'credit')?$item->amount:0 }} </span>
                          </td>
                        <td>
                           @if($start_point%2 == 0)
                              @php $start_point++;   @endphp
                              @if($item->gournal_voucher_item != null)
                                 @if($item->gournal_voucher_item->cost_center_id != null)
                                    <a target="_blank" href="{{ URL::to('account/account/'.$item->gournal_voucher_item->cost_center_id) }}">
                                       @php $account = \App\Account::where("id",$item->gournal_voucher_item->cost_center_id)->first(); @endphp
                                       {{  (!empty($account))?$account->name:"" }}
                                    </a> 
                                 @endif
                              @endif

                           @else
                           @php $start_point++; @endphp
                           @endif
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
                          <span class="display_currency font_number" data-currency_symbol="true">
                             {{ number_format($total_debit,config('constants.currency_precision')) }}</span>
                           </td>
                        <td>
                       <span class="display_currency font_number" data-currency_symbol="true">{{ number_format($total_credit,config('constants.currency_precision')) }} </span>
                     </td>
                        <td></td>
                        </tfoot>
                        
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
</div>

       		 
      	 							 
	
        
