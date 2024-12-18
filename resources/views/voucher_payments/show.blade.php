<div class="modal-dialog modal-xl" role="document">
	<div class="modal-content">
      <div class="modal-header">
         <button type="button" class="close no-print" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
         <h4 class="modal-title" id="modalTitle"> @lang('home.Payment Voucher') (<b> @lang('home.Ref No'):</b> #{{ $data->ref_no }})
         </h4>
     </div>
		<div class="modal-body">
         <div class="col-sm-12">
            <p class="pull-right"><b>@lang('home.Date'):</b> {{  date('d/m/Y',strtotime($data->created_at)) }}</p>
            
            
         </div>
        
         <div class="row invoice-info">
            <div class="col-sm-4 invoice-col">
               @if($data->transaction)
               <b>@lang('home.Transaction'):</b> #<button type="button" class="btn btn-link btn-modal"
               data-href="{{action('PurchaseController@show', [$data->transaction_id])}}" data-container=".view_modal">{{ $data->transaction->ref_no }}</button><br />
               @endif
               {{ trans('home.Contact') }}: 
               
               @php 
                     if($data->account_type == 0){
                           $account = \App\Account::where("contact_id",$data->contact_id)->first();
                           if($account){
                           $name = $account->name; 
                           }else{
                              $name = "--"; 
                           }
                     }else{
                           $account = \App\Account::where("id",$data->contact_id)->first();
                           if($account){
                           $name = $account->name; 
                           }else{
                           $name = "--"; 
                           }
                     }
                @endphp
               <a href="/account/account/{{$account->id}}">
                  {{ $name }}
               </a>
               
               <address>
                  
                  <br />
                   @lang('home.Phone') : {{ $account->contact? $account->contact->mobile:' ' }}
               </address>
            </div>
         
           
            <div class="col-sm-4 invoice-col">
               <b>@lang('home.Ref No'):</b> #{{ $data->ref_no }}<br />
               <b>@lang('home.Due Date')</b> {{ $data->date }}<br />
            </div>
            <div class="col-sm-4 invoice-col">
               <b>@lang('home.Account'):</b> {{ $data->account?$data->account->name:'---' }}<br />
               <b>@lang('home.Amount'):</b> {{ $data->amount }}<br />
            </div>
         </div>

         @if($payment)
            @component("components.widget",["title"=>"All Payments"])
               <table class="table table-bordered">
                  <tbody>
                     <tr style="background:#f1f1f1;">
                        <th>@lang('home.ref_no_second')</th>
                        <th>@lang('purchase.ref_no')</th>
                        <th>@lang('purchase.amount')</th>
                        <th>@lang('purchase.payment_for')</th>
                        <th>@lang('purchase.balance')</th>
                        <th>@lang('purchase.payment_method')</th>
                        <th>@lang('purchase.payment_note')</th>
                        <th>@lang('messages.date')</th>
                     </tr>
                                       
                     @foreach ($payment as $pay)
                     <tr style="background:#f1f1f1;">
                        <th>
                           @if($pay->transaction->type == "purchase")
                           <button type="button" class="btn btn-link btn-modal" data-href="{{ action("PurchaseController@show",[$pay->transaction->id]) }}" data-container=".view_modal">
                              {{ $pay->transaction->ref_no }}
                           </button>   
                           @else
                           <button type="button" class="btn btn-link btn-modal" data-href="{{action("SellController@show",[$pay->transaction->id])}}" data-container=".view_modal">
                              {{ $pay->transaction->invoice_no }}
                           </button>
                           @endif
                        </th>
                        <th>
                           <button type="button" class="btn btn-link btn-modal" data-href="{{ action("TransactionPaymentController@show",[$pay->transaction->id]) }}" data-container=".view_modal">
                              {{ $pay->payment_ref_no }}
                           </button>   
                        </th>
                        <th>@format_currency($data->amount)</th>
                        <th>@format_currency($pay->source)</th>
                        <th>@format_currency($data->amount - $pay->source)</th>
                        <th>{{ $pay->method }}</th>
                        <th>{{ $pay->note }}</th>
                        <th>{{ $pay->paid_on }}</th>
                     </tr> 
                     @endforeach                 
                  </tbody>
                  <tfoot>
                     <tr class="bg-gray  font-17 footer-total" style="border:1px solid #f1f1f1;  ">
                        <td class="text-center " colspan="8"><strong>
                        
                     </strong></td>
                        
                     </tr>
                  </tfoot>
               </table>
            @endcomponent
         @endif
         <div class="edit">
            @if($data->return_voucher == 0 || $data->return_voucher == null)
               <a class="btn bg-yellow" href="/payment-voucher/edit/{{$data->id}}">@lang("messages.edit")</a>
            @endif
         </div>

        </div>
    </div>
</div>

       		 
      	 							 
	
        
