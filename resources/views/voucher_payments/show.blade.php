<div class="modal-dialog modal-xl font_text" @if(count($payment)>0) style="width:60%" @else style="width:40%" @endif role="document">
	<div class="modal-content">
      <div class="modal-header">
         <button type="button" class="close no-print" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
         <h4 class="modal-title font_text" id="modalTitle"> @lang('home.Payment Voucher') (<b> @lang('home.Ref No'):</b> #{{ $data->ref_no }})
         </h4>
     </div>
      @php
         $line_height = "12px";
         $dt =  date('d/m/Y',strtotime($data->created_at));
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
		<div class="modal-body">
         <div class="col-sm-12">

            <p class="pull-right"><b>@lang('home.Date'):</b> {{\Carbon::createFromFormat($D_format,date('d/m/Y',strtotime($data->created_at)))->format(session()->get('business.date_format'))}}</p>
         </div>
        
         <div class="row invoice-info font_number">
            <div class="col-sm-4 invoice-col">
               @if($data->transaction)
                  <b>@lang('home.Transaction'):</b> #<button type="button" class="btn btn-link btn-modal"
                  data-href="{{action('PurchaseController@show', [$data->transaction_id])}}" data-container=".view_modal">{{ $data->transaction->ref_no }}</button><br/>
               @endif
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
               <p style="line-height: {{$line_height}}">
                  <b>{{ trans('home.Contact') }}:</b> 
                  <a href="/account/account/{{$account->id}}">
                     {{ $name }}
                  </a>
               </p>
               <p style="line-height: {{$line_height}}"> 
                   <b>@lang('home.Phone') : </b>{{ $account->contact? $account->contact->mobile:' ' }}
               </p>
            </div>
            @php
                  $dt =$data->date;
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
            
            <div class="col-sm-4 invoice-col font_number">
               <b>@lang('home.Ref No'):</b> #{{ $data->ref_no }}<br />
               <b>@lang('home.Due Date')</b> {{\Carbon::createFromFormat($D_format,$data->date)->format(session()->get('business.date_format'))}}<br />
            </div>
            <div class="col-sm-4 invoice-col font_number">
               <b>@lang('home.Account'):</b> {{ $data->account?$data->account->name:'---' }}<br />
               <b>@lang('home.Amount'):</b> {{ $data->amount }}<br />
            </div>
         </div>

         @if(count($payment)>0)
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
                        <th class="font_number">@format_currency($data->amount)</th>
                        <th class="font_number">@format_currency($pay->source)</th>
                        <th class="font_number">@format_currency($data->amount - $pay->source)</th>
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

       		 
      	 							 
	
        
