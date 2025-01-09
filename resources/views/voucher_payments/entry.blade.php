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
               @foreach($last as $key=>$items)
                  @php  $cur = (session()->get('business.currency.symbol'))?session()->get('business.currency.symbol'):""; @endphp 
                  <?php
                     $total_debit  = 0;
                     $total_credit = 0;
                     $entry_ref    = [];
                  ?>
                  <table class="table table-bordered table-striped dataTable no-footer" id="account_book" role="grid" aria-describedby="account_book_info" style="width: 1536px;">
                     <thead>
                        <tr>
                           
                           <td @if($data->currency_id != null) colspan="7"  @else colspan="5" @endif  style="background-color:#f1f1f1 !important;color:black">{{$key}}</td>
                        </tr>
                        <tr role="row">
                           <th class="sorting_disabled" rowspan="1" colspan="1" style="width: 187px;">Account</th>
                           <th class="sorting_disabled" rowspan="1" colspan="1" style="width: 187px;">Date</th>
                           <th class="sorting_disabled" rowspan="1" colspan="1" style="width: 79px;">Debit - ( {{$cur}} ) </th>
                           <th class="sorting_disabled" rowspan="1" colspan="1" style="width: 188px;">Credit - ( {{$cur}} ) </th>
                           @if($data->currency_id != null)
                              <th class="sorting_disabled" rowspan="1" colspan="1" style="width: 79px;">Debit - ( {{($data->currency)?$data->currency->symbol:""}} ) </th>
                              <th class="sorting_disabled" rowspan="1" colspan="1" style="width: 188px;">Credit - ( {{($data->currency)?$data->currency->symbol:""}} ) </th>
                           @endif
                        </tr>
                     </thead>
                     @foreach($items as $k => $item)
                        <tbody>
                           @php 
                              if($item->payment_voucher->account_type == 0){
                                 $accounts = \App\Account::where("contact_id",$item->payment_voucher->contact_id)->first();
                                    if($accounts){
                                       $name = $accounts->name; 
                                       $idd  = $accounts->id; 
                                    }else{
                                       $name = "--"; 
                                       $idd  = null; 
                                    }
                                 }else{
                                    $accounts = \App\Account::find($item->payment_voucher->contact_id);
                                    if($accounts){
                                       $name = $accounts->name; 
                                       $idd  = $accounts->id; 
                                    }else{
                                       $name = "--"; 
                                       $idd  = null; 
                                    }
                                 }
                                 
                           @endphp
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
                                    'Y-m-d',       // 2024-12-25
                                    'd/m/Y',       // 25/12/2024
                                    'm/d/Y',       // 12/25/2024
                                    'd-m-Y',       // 25-12-2024
                                    'Y/m/d',       // 2024/12/25
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
                              <td>{{\Carbon::createFromFormat($D_format,date('Y-m-d',strtotime($item->operation_date)))->format(session()->get('business.date_format'))}}</td>
                              
                              <td>
                                 <span class="display_currency" data-currency_symbol="true">{{ ($item->type == 'debit')?number_format($item->amount,config("constants.currency_precision")):0 }}</span>
                              </td>
                              <td>
                                 <span class="display_currency" data-currency_symbol="true">{{ ($item->type == 'credit')?number_format($item->amount,config("constants.currency_precision")):0 }} </span>
                              </td>
                              @if($data->currency_id != null)
                                 <td>
                                    <span class="display_currency" data-currency_symbol="true">{{ ($item->type == 'debit')?number_format(($data->exchange_price!=0)?$item->amount/$data->exchange_price:0,config("constants.currency_precision")):0 }}</span>
                                 </td>
                                 <td>
                                    <span class="display_currency" data-currency_symbol="true">{{ ($item->type == 'credit')?number_format(($data->exchange_price!=0)?$item->amount/$data->exchange_price:0,config("constants.currency_precision")):0 }} </span>
                                 </td>
                              @endif
                              
                           </tr>
                        </tbody>
                     @endforeach

                     <tfoot>
                        <tr role="row" class="odd">
                              <td> 
                              </td>
                              <td>
                              </td>
                              <td>
                                 <span class="display_currency font_number" data-currency_symbol="true">
                                    {{ number_format($total_debit,config("constants.currency_precision")) }}</span>
                                 </td>
                              <td>
                                 <span class="display_currency font_number" data-currency_symbol="true">{{ number_format($total_credit,config("constants.currency_precision")) }} </span>
                              </td>
                              @if($data->currency_id != null)
                                 <td>
                                    <span class="display_currency font_number" data-currency_symbol="true">
                                       {{ number_format(($data->exchange_price!=0)?$total_debit/$data->exchange_price:0,config("constants.currency_precision")) }}</span>
                                    </td>
                                 <td>
                                    <span class="display_currency font_number" data-currency_symbol="true">{{ number_format(($data->exchange_price!=0)?$total_credit/$data->exchange_price:0,config("constants.currency_precision")) }} </span>
                                 </td>
                              @endif

                           </tr>
                     </tfoot>
                        
                  </table>
               @endforeach
            </div>
         </div>
         <div class="col-sm-12">
          </div>
          <div class="row invoice-info">
            
         </div>
        </div>
    </div>
</div>

       		 
      	 							 
	
        
