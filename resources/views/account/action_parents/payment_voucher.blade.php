<div class="modal-dialog modal-xl no-print " role="document">
	<div class="modal-content dummy">
		<div class="modal-header">
			<button type="button" class="close no-print" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
			<h4 class="modal-title" id="modalTitle">@lang('home.Voucher') (<b>@lang('home.Ref No').: {{ $data->ref_no }}</b> )</h4>
		</div>
		<div class="modal-body">
			<div class="row">
				<div class="col-xs-12">
					<p class="pull-right"><b>@lang('home.Date'):</b> {{ $data->date }} </p>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-4">
					<b>@lang('home.Ref No').:</b> {{ $data->ref_no }}<br />
					<b>@lang('home.Amount'):</b> {{ $data->amount }} <br />
					<b>@lang('home.Type'):</b>{{ isset($types[$data->type])?$types[$data->type]:'' }}<br />
				</div>
				<div class="col-sm-4">
					<b>@lang('home.Contact'):</b>{{ $data->contact?$data->contact->name:'--' }}<br />
					<b>@lang('home.Bank'):</b> {{ $data->account?$data->account->name:'--' }} <br />
				</div>
				
			</div>
			<br />
			@if($payment)
			@component("components.widget",["title"=>"All Payments"])
			   <table class="table table-bordered">
				  <tbody>
					 <tr style="background:#f1f1f1;">
						<th>@lang('home.ref_no_second')</th>
						<th>@lang('purchase.ref_no')</th>
						<th>@lang('purchase.amount')</th>
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
						<th>{{ $pay->amount }}</th>
						<th>{{ $pay->method }}</th>
						<th>{{ $pay->note }}</th>
						<th>{{ $pay->paid_on }}</th>
					 </tr> 
					 @endforeach                 
				  </tbody>
				  <tfoot>
					 <tr class="bg-gray  font-17 footer-total" style="border:1px solid #f1f1f1;  ">
						<td class="text-center " colspan="6"><strong>
						
					 </strong></td>
						
					 </tr>
				  </tfoot>
			   </table>
			@endcomponent
		 @endif
			
		</div>
		<div class="modal-footer">
		    <div class="edit">
				<a class="btn bg-yellow" href="/payment-voucher/edit/{{$data->id}}">@lang("messages.edit")</a>
			 </div>
            <button type="button" class="btn btn-primary no-print" aria-label="Print" onclick="$(this).closest('div.dummy').printThis();">
                <i class="fa fa-print"></i> Print      </button>
			<button type="button" class="btn btn-default no-print" data-dismiss="modal">Close</button>
		</div>
	</div>
</div>
